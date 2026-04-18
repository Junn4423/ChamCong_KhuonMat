# -*- coding: utf-8 -*-
"""Employee routes — ERP employees, admin CRUD.  (maps to frontend modules/employee.js)"""

import io
import base64
from datetime import datetime, date

from flask import Blueprint, request, jsonify, g, Response

from backend.models.database import db, User, Attendance, EmployeeImage
from backend.face_encoding_utils import face_encoding_count, normalize_face_encodings
from backend.services.erp_http_client import ERPServiceError, erp_http_client
from backend.services.import_employees import ERPImporter
from backend.routes._state import state
from backend.routes._helpers import (
    admin_required, session_required,
    get_local_employee_image, save_local_employee_image,
    remove_local_employee_images, get_local_employee_image_url,
    get_local_employee_image_token, get_local_employee_erp_image_token, get_employee_image_by_token,
    build_local_image_url,
    to_data_uri, get_erp_employee_image_blob, get_erp_employee_image_data,
    resolve_request_auth_mode,
)

employee_bp = Blueprint('employee', __name__)

SYNC_COMPARE_FIELDS = (
    ('name', 'Họ tên'),
    ('department', 'Phòng ban'),
    ('position', 'Vị trí'),
)


def _safe_compare_text(value):
    return str(value or '').strip()


def _normalize_compare_text(value):
    return _safe_compare_text(value).casefold()


def _build_profile_differences(local_user, erp_employee):
    differences = []
    for field_name, field_label in SYNC_COMPARE_FIELDS:
        local_value = _safe_compare_text(getattr(local_user, field_name, ''))
        erp_value = _safe_compare_text((erp_employee or {}).get(field_name, ''))
        if _normalize_compare_text(local_value) != _normalize_compare_text(erp_value):
            differences.append({
                'field': field_name,
                'label': field_label,
                'system_value': local_value,
                'erp_value': erp_value,
            })
    return differences


def _build_face_mismatch(employee_id, local_user, erp_employee, image_row=None):
    erp_image_token = _safe_compare_text((erp_employee or {}).get('image_token', ''))
    system_image_token = _safe_compare_text(getattr(image_row, 'erp_image_token', ''))
    local_image_token = _safe_compare_text(getattr(image_row, 'image_token', ''))
    has_local_image = bool(local_image_token)

    token_same = bool(erp_image_token and system_image_token and erp_image_token == system_image_token)
    if token_same:
        return None

    if not erp_image_token and not system_image_token and not has_local_image:
        return None

    reasons = []
    if erp_image_token and system_image_token and erp_image_token != system_image_token:
        reasons.append('Token ảnh giữa ERP và hệ thống khác nhau.')
    if erp_image_token and not system_image_token:
        reasons.append('ERP có token ảnh nhưng hệ thống chưa lưu token tham chiếu tương ứng.')
    if system_image_token and not erp_image_token:
        reasons.append('Hệ thống có token tham chiếu ảnh nhưng ERP đang trống token.')
    if has_local_image and not erp_image_token:
        reasons.append('Hệ thống có ảnh local nhưng ERP chưa có token ảnh.')
    if erp_image_token and not has_local_image:
        reasons.append('ERP có token ảnh nhưng hệ thống chưa có ảnh local.')

    can_push_local_to_erp = has_local_image
    can_pull_erp_to_system = bool(local_user is not None and erp_image_token)

    direction_hint = 'review'
    if can_push_local_to_erp and not can_pull_erp_to_system:
        direction_hint = 'push_local_to_erp'
    elif can_pull_erp_to_system and not can_push_local_to_erp:
        direction_hint = 'pull_erp_to_system'

    return {
        'employee_id': employee_id,
        'name': _safe_compare_text((erp_employee or {}).get('name')) or _safe_compare_text(getattr(local_user, 'name', '')),
        'department': _safe_compare_text((erp_employee or {}).get('department')) or _safe_compare_text(getattr(local_user, 'department', '')),
        'erp_image_token': erp_image_token,
        'system_image_token': system_image_token,
        'local_image_token': local_image_token,
        'has_local_image': has_local_image,
        'can_push_local_to_erp': can_push_local_to_erp,
        'can_pull_erp_to_system': can_pull_erp_to_system,
        'direction_hint': direction_hint,
        'reasons': reasons,
    }


@employee_bp.route('/api/image/token/<string:image_token>')
def serve_image_by_token(image_token):
    row = get_employee_image_by_token(image_token)
    if row is None or not row.image_blob:
        return jsonify({'success': False, 'message': 'Không tìm thấy ảnh theo token'}), 404

    return Response(
        row.image_blob,
        mimetype=row.mime_type or 'image/jpeg',
        headers={
            'Cache-Control': 'public, max-age=3600',
        },
    )


# ─── Public ──────────────────────────────────────────────────────────────

@employee_bp.route('/api/employees')
def get_employees():
    try:
        users = User.query.all()
        employees = [{
            'id': u.id,
            'name': u.name,
            'employee_id': u.employee_id,
            'department': u.department,
            'position': u.position,
            'created_at': u.created_at.strftime('%Y-%m-%d %H:%M:%S') if u.created_at else ''
        } for u in users]
        return jsonify({'success': True, 'employees': employees})
    except Exception as e:
        return jsonify({'success': False, 'message': str(e), 'employees': []})


# ─── ERP Employees ──────────────────────────────────────────────────────

@employee_bp.route('/api/erp/employees')
@session_required
def api_erp_employees():
    try:
        erp_employees = erp_http_client.list_employees(g.erp_auth)
        local_users = {u.employee_id: u for u in User.query.all()}

        employees = []
        for emp in erp_employees:
            employee_id = (emp.get('employee_id') or '').strip()
            if not employee_id:
                continue

            local_user = local_users.get(employee_id)
            registered = local_user is not None
            fc = face_encoding_count(local_user.face_encoding) if local_user else 0
            has_face = fc > 0
            local_image_url = get_local_employee_image_url(employee_id)
            local_image_token = get_local_employee_image_token(employee_id)
            local_erp_image_token = get_local_employee_erp_image_token(employee_id)
            has_local_image = bool(local_image_url)
            erp_image_token = (emp.get('image_token') or '').strip()
            erp_image_url = (emp.get('image_url') or '').strip()
            needs_erp_image_sync = bool(local_image_token and not erp_image_token)

            status_text = (
                'Đã có thông tin trong hệ thống chấm công'
                if registered
                else 'Chưa có thông tin trong hệ thống chấm công'
            )

            employees.append({
                **emp,
                'user_id': local_user.id if local_user else None,
                'registered': registered,
                'has_face': has_face,
                'face_count': fc,
                'has_local_image': has_local_image,
                'image_token': erp_image_token,
                'erp_image_token': erp_image_token,
                'local_image_token': local_image_token,
                'local_erp_image_token': local_erp_image_token,
                'image_url': erp_image_url,
                'erp_image_url': erp_image_url,
                'local_image_url': local_image_url,
                'needs_erp_image_sync': needs_erp_image_sync,
                'status_text': status_text,
            })

        return jsonify({'success': True, 'employees': employees})
    except ERPServiceError as exc:
        return jsonify({'success': False, 'message': exc.message, 'employees': []}), (exc.status_code or 500)
    except Exception as exc:
        return jsonify({'success': False, 'message': str(exc), 'employees': []}), 500


@employee_bp.route('/api/erp/sync_compare')
@session_required
def api_erp_sync_compare():
    try:
        erp_employees = erp_http_client.list_employees(g.erp_auth)
        local_users = {u.employee_id: u for u in User.query.all()}
        local_images = {row.employee_id: row for row in EmployeeImage.query.all()}

        new_employees = []
        profile_mismatches = []
        face_mismatches = []

        for emp in erp_employees:
            employee_id = _safe_compare_text(emp.get('employee_id'))
            if not employee_id:
                continue

            local_user = local_users.get(employee_id)
            if local_user is None:
                new_employees.append({
                    'employee_id': employee_id,
                    'name': _safe_compare_text(emp.get('name')),
                    'department': _safe_compare_text(emp.get('department')),
                    'position': _safe_compare_text(emp.get('position')),
                    'erp_image_token': _safe_compare_text(emp.get('image_token')),
                    'erp_image_url': _safe_compare_text(emp.get('image_url')),
                })
                continue

            differences = _build_profile_differences(local_user, emp)
            if differences:
                profile_mismatches.append({
                    'employee_id': employee_id,
                    'erp': {
                        'name': _safe_compare_text(emp.get('name')),
                        'department': _safe_compare_text(emp.get('department')),
                        'position': _safe_compare_text(emp.get('position')),
                    },
                    'system': {
                        'name': _safe_compare_text(local_user.name),
                        'department': _safe_compare_text(local_user.department),
                        'position': _safe_compare_text(local_user.position),
                    },
                    'differences': differences,
                })

            face_diff = _build_face_mismatch(
                employee_id,
                local_user,
                emp,
                image_row=local_images.get(employee_id),
            )
            if face_diff:
                face_mismatches.append(face_diff)

        new_employees.sort(key=lambda item: item.get('employee_id') or '')
        profile_mismatches.sort(key=lambda item: item.get('employee_id') or '')
        face_mismatches.sort(key=lambda item: item.get('employee_id') or '')

        return jsonify({
            'success': True,
            'generated_at': datetime.utcnow().isoformat(),
            'summary': {
                'erp_total': len(erp_employees),
                'system_total': len(local_users),
                'new_count': len(new_employees),
                'profile_mismatch_count': len(profile_mismatches),
                'face_mismatch_count': len(face_mismatches),
            },
            'new_employees': new_employees,
            'profile_mismatches': profile_mismatches,
            'face_mismatches': face_mismatches,
        })
    except ERPServiceError as exc:
        return jsonify({'success': False, 'message': exc.message}), (exc.status_code or 500)
    except Exception as exc:
        return jsonify({'success': False, 'message': str(exc)}), 500


@employee_bp.route('/api/erp/import_all', methods=['POST'])
@session_required
def api_erp_import_all():
    try:
        payload = request.get_json(silent=True) or {}
        raw_ids = payload.get('employee_ids')
        overwrite_raw = payload.get('overwrite_existing', False)

        employee_ids = []
        if isinstance(raw_ids, list):
            for item in raw_ids:
                employee_id = (str(item or '').strip())
                if employee_id:
                    employee_ids.append(employee_id)
            employee_ids = list(dict.fromkeys(employee_ids))

        if isinstance(overwrite_raw, str):
            overwrite_existing = overwrite_raw.strip().lower() in {'1', 'true', 'yes', 'y', 'on'}
        else:
            overwrite_existing = bool(overwrite_raw)

        def save_img_cb(employee_id, image_blob, employee_data=None):
            erp_image_token = ''
            if isinstance(employee_data, dict):
                erp_image_token = (employee_data.get('image_token') or '').strip()

            save_local_employee_image(
                employee_id,
                image_blob,
                '.jpg',
                source='erp_import',
                erp_image_token=erp_image_token,
            )

        importer = ERPImporter(
            face_recognizer=state.face_recognizer,
            erp_client=erp_http_client,
            save_image_callback=save_img_cb,
        )
        result = importer.import_all_employees(
            g.erp_auth,
            employee_ids=employee_ids,
            overwrite_existing=overwrite_existing,
        )
        state.load_known_faces()
        return jsonify({'success': True, 'result': result})
    except ERPServiceError as exc:
        return jsonify({'success': False, 'message': exc.message}), (exc.status_code or 500)
    except Exception as exc:
        return jsonify({'success': False, 'message': str(exc)}), 500


@employee_bp.route('/api/erp_employee_info')
@session_required
def get_erp_employee_info():
    employee_id = (request.args.get('employee_id') or '').strip()
    if not employee_id:
        return jsonify({'success': False, 'message': 'Thiếu mã nhân viên'}), 400

    try:
        emp = erp_http_client.get_employee(g.erp_auth, employee_id)
    except ERPServiceError as exc:
        return jsonify({'success': False, 'message': exc.message}), (exc.status_code or 500)
    except Exception as exc:
        return jsonify({'success': False, 'message': f'Lỗi truy vấn ERP: {exc}'}), 500

    local_user = User.query.filter_by(employee_id=employee_id).first()
    fc = face_encoding_count(local_user.face_encoding) if local_user else 0
    has_face = fc > 0
    local_image_url = get_local_employee_image_url(employee_id)

    erp_image_data = get_erp_employee_image_data(employee_id, employee=emp) or {}
    image_bytes = erp_image_data.get('bytes')
    image_mime = erp_image_data.get('content_type') or 'image/jpeg'
    image_url = (
        (emp.get('image_url') or '').strip()
        or (erp_image_data.get('source_url') or '').strip()
    )

    image_base64 = None if image_url else (to_data_uri(image_bytes, image_mime) if image_bytes else None)

    registered = local_user is not None
    status_text = (
        'Đã có thông tin trong hệ thống chấm công'
        if registered
        else 'Chưa có thông tin trong hệ thống chấm công'
    )
    erp_image_token = (emp.get('image_token') or '').strip()
    erp_image_url = (emp.get('image_url') or '').strip()
    local_image_token = get_local_employee_image_token(employee_id)
    local_erp_image_token = get_local_employee_erp_image_token(employee_id)
    needs_erp_image_sync = bool(local_image_token and not erp_image_token)

    employee_payload = {
        **emp,
        'employee_id': employee_id,
        'user_id': local_user.id if local_user else None,
        'registered': registered,
        'has_face': has_face,
        'face_count': fc,
        'has_local_image': bool(local_image_url),
        'status_text': status_text,
        'image_token': erp_image_token,
        'erp_image_token': erp_image_token,
        'local_image_token': local_image_token,
        'local_erp_image_token': local_erp_image_token,
        'image_url': image_url,
        'erp_image_url': erp_image_url,
        'local_image_url': local_image_url,
        'needs_erp_image_sync': needs_erp_image_sync,
        'image_base64': image_base64,
        'has_erp_image': bool(image_bytes),
    }
    return jsonify({'success': True, 'employee': employee_payload})


# ─── Admin Employee Management ──────────────────────────────────────────

@employee_bp.route('/api/admin/employees')
@admin_required
def get_admin_employees():
    try:
        users = User.query.all()
        employees = []
        for u in users:
            fc = face_encoding_count(u.face_encoding)
            has_face = fc > 0
            local_image_url = get_local_employee_image_url(u.employee_id)
            local_image_token = get_local_employee_image_token(u.employee_id)
            has_local_image = bool(local_image_url)

            if has_face:
                status_code = 'ready'
                status_text = 'Đã đăng ký khuôn mặt'
            elif has_local_image:
                status_code = 'image_only'
                status_text = 'Đã cập nhật ảnh, chưa trích xuất khuôn mặt'
            else:
                status_code = 'empty'
                status_text = 'Đã có trong hệ thống (chưa có khuôn mặt)'

            employees.append({
                'id': u.id,
                'name': u.name,
                'employee_id': u.employee_id,
                'department': u.department or '',
                'position': u.position or '',
                'has_face': has_face,
                'face_count': fc,
                'has_local_image': has_local_image,
                'image_token': local_image_token,
                'image_url': local_image_url,
                'status_code': status_code,
                'status_text': status_text,
                'created_at': u.created_at.strftime('%Y-%m-%d %H:%M') if u.created_at else '',
            })
        return jsonify({'success': True, 'employees': employees})
    except Exception as exc:
        return jsonify({'success': False, 'message': str(exc), 'employees': []}), 500


@employee_bp.route('/api/admin/employee_image')
@admin_required
def get_admin_employee_image():
    employee_id = (request.args.get('employee_id') or '').strip()
    if not employee_id:
        return jsonify({'success': False, 'message': 'Thiếu mã nhân viên'}), 400

    user = User.query.filter_by(employee_id=employee_id).first()
    image_bytes, image_mime = get_local_employee_image(employee_id)
    local_image_token = get_local_employee_image_token(employee_id)
    image_url = build_local_image_url(local_image_token) if local_image_token else ''
    local_image_available = bool(image_url)
    image_source = 'local_token'

    if image_bytes is None and getattr(g, 'erp_auth', None):
        erp_emp = None
        try:
            erp_emp = erp_http_client.get_employee(g.erp_auth, employee_id)
        except Exception:
            erp_emp = None

        image_data = get_erp_employee_image_data(employee_id, employee=erp_emp)
        image_bytes = image_data.get('bytes') if isinstance(image_data, dict) else None
        if image_bytes:
            image_source = 'erp'
            image_mime = image_data.get('content_type') if isinstance(image_data, dict) else 'image/jpeg'
            image_url = (
                (image_data.get('local_image_url') or '').strip()
                if isinstance(image_data, dict)
                else ''
            ) or (
                (image_data.get('source_url') or '').strip()
                if isinstance(image_data, dict)
                else ''
            ) or ((erp_emp or {}).get('image_url') or '').strip()
            if image_url and not local_image_token:
                local_image_token = get_local_employee_image_token(employee_id)
                local_image_available = bool(local_image_token)

    if image_bytes is None:
        return jsonify({'success': False, 'message': 'Không tìm thấy ảnh nhân viên'}), 404

    employee_payload = {
        'employee_id': employee_id,
        'name': user.name if user else employee_id,
        'department': user.department if user else '',
        'position': user.position if user else '',
        'has_face': face_encoding_count(user.face_encoding) > 0 if user else False,
        'face_count': face_encoding_count(user.face_encoding) if user else 0,
        'status_code': (
            'ready'
            if (user and face_encoding_count(user.face_encoding) > 0)
            else ('image_only' if (user and local_image_available) else 'empty')
        ),
        'status_text': (
            'Đã đăng ký khuôn mặt'
            if (user and face_encoding_count(user.face_encoding) > 0)
            else ('Đã cập nhật ảnh, chưa trích xuất khuôn mặt' if (user and local_image_available) else 'Đã có trong hệ thống (chưa có khuôn mặt)')
        ),
        'id': user.id if user else None,
    }

    if not user and getattr(g, 'erp_auth', None):
        try:
            erp_emp = erp_http_client.get_employee(g.erp_auth, employee_id)
            employee_payload['name'] = erp_emp.get('name') or employee_payload['name']
            employee_payload['department'] = erp_emp.get('department') or employee_payload['department']
            employee_payload['position'] = erp_emp.get('position') or employee_payload['position']
        except Exception:
            pass

    return jsonify({
        'success': True,
        'employee': employee_payload,
        'image_token': local_image_token,
        'erp_image_token': get_local_employee_erp_image_token(employee_id),
        'image_url': image_url,
        'image_base64': None if image_url else to_data_uri(image_bytes, image_mime),
        'image_source': image_source,
    })


@employee_bp.route('/api/admin/update_face', methods=['POST'])
@admin_required
def admin_update_face():
    try:
        data = request.get_json(silent=True) if request.is_json else None
        user_id = data.get('user_id') if isinstance(data, dict) else request.form.get('user_id')
        image_base64 = data.get('image_base64') if isinstance(data, dict) else None

        if not user_id:
            return jsonify({'success': False, 'message': 'Thiếu user_id'}), 400

        user = User.query.get(user_id)
        if not user:
            return jsonify({'success': False, 'message': 'Không tìm thấy nhân viên'}), 404

        image_bytes = None
        if 'image' in request.files:
            image_bytes = request.files['image'].read()
        elif image_base64:
            raw_base64 = image_base64.split(',', 1)[1] if ',' in image_base64 else image_base64
            image_bytes = base64.b64decode(raw_base64)
        else:
            return jsonify({'success': False, 'message': 'Thiếu ảnh'}), 400

        if not image_bytes:
            return jsonify({'success': False, 'message': 'Ảnh rỗng'}), 400

        with state.face_recognition_lock:
            face_encoding, error = state.face_recognizer.encode_face_from_image(io.BytesIO(image_bytes))

        if error:
            return jsonify({'success': False, 'message': error}), 400

        token_username = ''
        session_user = getattr(g, 'session_user', None)
        if isinstance(session_user, dict):
            token_username = (session_user.get('code') or '').strip()
        if not token_username and getattr(g, 'erp_auth', None):
            token_username = (g.erp_auth.get('code') or '').strip()

        sof_token_payload = erp_http_client.create_sof_image_token(
            image_bytes,
            username=token_username or None,
            auth=getattr(g, 'erp_auth', None),
        )
        erp_image_token = str(sof_token_payload.get('token') or '').strip()

        replace_all = False
        if isinstance(data, dict):
            replace_all = bool(data.get('replace_all', False))
        else:
            replace_all = (request.form.get('replace_all', 'false').lower() == 'true')

        current_encodings = normalize_face_encodings(user.face_encoding)
        normalized_new = normalize_face_encodings(face_encoding)
        new_encoding = normalized_new[0] if normalized_new else []
        if replace_all:
            user.face_encoding = [new_encoding] if new_encoding else []
        else:
            merged = current_encodings + ([new_encoding] if new_encoding else [])
            user.face_encoding = merged[-5:] if len(merged) > 5 else merged

        db.session.commit()
        image_token = save_local_employee_image(
            user.employee_id,
            image_bytes,
            '.jpg',
            source='manual_upload',
            erp_image_token=erp_image_token,
        )
        state.load_known_faces()

        return jsonify({
            'success': True,
            'message': f'Cập nhật khuôn mặt cho {user.name}',
            'face_count': face_encoding_count(user.face_encoding),
            'image_token': image_token,
            'erp_image_token': erp_image_token,
            'image_url': build_local_image_url(image_token) if image_token else '',
        })
    except ERPServiceError as exc:
        db.session.rollback()
        return jsonify({'success': False, 'message': exc.message}), (exc.status_code or 500)
    except Exception as exc:
        db.session.rollback()
        return jsonify({'success': False, 'message': str(exc)}), 500

@employee_bp.route('/api/admin/push_to_erp', methods=['POST'])
@admin_required
def admin_push_to_erp():
    employee_id = ''
    if request.is_json:
        employee_id = (request.get_json(silent=True) or {}).get('employee_id') or ''
    else:
        employee_id = request.form.get('employee_id') or ''
    employee_id = employee_id.strip()

    if not employee_id:
        return jsonify({'success': False, 'message': 'Thiếu mã nhân viên'}), 400

    if resolve_request_auth_mode(default='internal') != 'system':
        return jsonify({
            'success': False,
            'message': 'Đang ở chế độ nội bộ. Vui lòng đăng nhập hệ thống để đẩy dữ liệu lên ERP.',
        }), 403

    image_bytes, _ = get_local_employee_image(employee_id)
    if image_bytes is None:
        return jsonify({'success': False, 'message': 'Không có ảnh local để đăng ký token ERP'}), 404

    try:
        erp_image_token = get_local_employee_erp_image_token(employee_id)
        if not erp_image_token:
            token_username = ''
            session_user = getattr(g, 'session_user', None)
            if isinstance(session_user, dict):
                token_username = (session_user.get('code') or '').strip()
            if not token_username and getattr(g, 'erp_auth', None):
                token_username = (g.erp_auth.get('code') or '').strip()

            sof_token_payload = erp_http_client.create_sof_image_token(
                image_bytes,
                username=token_username or None,
                auth=getattr(g, 'erp_auth', None),
            )
            erp_image_token = str(sof_token_payload.get('token') or '').strip()
            save_local_employee_image(
                employee_id,
                image_bytes,
                '.jpg',
                source='erp_push_cache',
                erp_image_token=erp_image_token,
            )

        erp_update_result = erp_http_client.update_employee_image_token(
            employee_id,
            erp_image_token,
            auth=getattr(g, 'erp_auth', None),
        )
        return jsonify({
            'success': True,
            'message': f'Cập nhật token ảnh lên ERP cho {employee_id}',
            'erp_image_token': erp_image_token,
            'erp_column': erp_http_client.token_column,
            'erp_update': erp_update_result,
        })
    except ERPServiceError as exc:
        return jsonify({'success': False, 'message': exc.message}), (exc.status_code or 500)
    except Exception as exc:
        return jsonify({'success': False, 'message': str(exc)}), 500

@employee_bp.route('/api/admin/delete_employee/<int:user_id>', methods=['DELETE'])
@admin_required
def admin_delete_employee(user_id):
    try:
        user = User.query.get(user_id)
        if not user:
            return jsonify({'success': False, 'message': 'Không tìm thấy nhân viên'}), 404

        Attendance.query.filter_by(user_id=user_id).delete()
        db.session.delete(user)
        db.session.commit()
        remove_local_employee_images(user.employee_id)
        state.load_known_faces()
        return jsonify({'success': True, 'message': f'Đã xóa nhân viên {user.name}'})
    except Exception as exc:
        db.session.rollback()
        return jsonify({'success': False, 'message': str(exc)}), 500


@employee_bp.route('/api/admin/clear_face/<int:user_id>', methods=['POST'])
@admin_required
def admin_clear_face(user_id):
    try:
        user = User.query.get(user_id)
        if not user:
            return jsonify({'success': False, 'message': 'Không tìm thấy nhân viên'}), 404

        user.face_encoding = []
        db.session.commit()
        remove_local_employee_images(user.employee_id)
        state.load_known_faces()
        return jsonify({'success': True, 'message': f'Đã xóa dữ liệu khuôn mặt của {user.name}'})
    except Exception as exc:
        db.session.rollback()
        return jsonify({'success': False, 'message': str(exc)}), 500


@employee_bp.route('/api/admin/reload_from_erp', methods=['POST'])
@admin_required
def reload_from_erp():
    data = request.get_json(silent=True) or {}
    employee_id = (data.get('employee_id') or '').strip()
    if not employee_id:
        return jsonify({'success': False, 'message': 'Thiếu mã nhân viên'}), 400

    user = User.query.filter_by(employee_id=employee_id).first()
    if not user:
        return jsonify({'success': False, 'message': 'Không tìm thấy nhân viên'}), 404

    if not getattr(g, 'erp_auth', None):
        return jsonify({'success': False, 'message': 'Phiên ERP hết hạn. Vui lòng đăng nhập lại'}), 401

    try:
        employee_meta = erp_http_client.get_employee(g.erp_auth, employee_id)
        image_blob = get_erp_employee_image_blob(employee_id, employee=employee_meta)
        if not image_blob:
            return jsonify({'success': False, 'message': 'Không tìm thấy ảnh trong ERP'}), 404

        with state.face_recognition_lock:
            face_encoding, error = state.face_recognizer.encode_face_from_image(io.BytesIO(image_blob))
        if error:
            return jsonify({'success': False, 'message': f'Lỗi nhận diện: {error}'}), 400

        user.face_encoding = normalize_face_encodings(face_encoding)
        db.session.commit()
        erp_image_token = (employee_meta.get('image_token') or '').strip()
        save_local_employee_image(employee_id, image_blob, '.jpg', source='erp_reload', erp_image_token=erp_image_token)
        state.load_known_faces()
        return jsonify({'success': True, 'message': f'Đã tải lại dữ liệu từ ERP cho {user.name}'})
    except ERPServiceError as exc:
        return jsonify({'success': False, 'message': exc.message}), (exc.status_code or 500)
    except Exception as exc:
        db.session.rollback()
        return jsonify({'success': False, 'message': str(exc)}), 500


