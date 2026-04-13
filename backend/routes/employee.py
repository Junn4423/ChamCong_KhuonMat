# -*- coding: utf-8 -*-
"""Employee routes — ERP employees, admin CRUD.  (maps to frontend modules/employee.js)"""

import io
import base64
from datetime import datetime, date

from flask import Blueprint, request, jsonify, g

from backend.models.database import db, User, Attendance
from backend.face_encoding_utils import face_encoding_count, normalize_face_encodings
from backend.services.erp_http_client import ERPServiceError, erp_http_client
from backend.services.import_employees import ERPImporter
from backend.routes._state import state
from backend.routes._helpers import (
    admin_required, session_required,
    sanitize_employee_id,
    get_local_employee_image, save_local_employee_image,
    remove_local_employee_images,
    to_data_uri, get_erp_employee_image_blob,
)

employee_bp = Blueprint('employee', __name__)


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
            local_image_bytes, _ = get_local_employee_image(employee_id)
            has_local_image = local_image_bytes is not None

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
                'status_text': status_text,
            })

        return jsonify({'success': True, 'employees': employees})
    except ERPServiceError as exc:
        return jsonify({'success': False, 'message': exc.message, 'employees': []}), (exc.status_code or 500)
    except Exception as exc:
        return jsonify({'success': False, 'message': str(exc), 'employees': []}), 500


@employee_bp.route('/api/erp/import_all', methods=['POST'])
@session_required
def api_erp_import_all():
    try:
        def save_img_cb(employee_id, image_blob):
            save_local_employee_image(employee_id, image_blob, '.jpg')

        importer = ERPImporter(
            face_recognizer=state.face_recognizer,
            erp_client=erp_http_client,
            save_image_callback=save_img_cb,
        )
        result = importer.import_all_employees(g.erp_auth)
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
    local_image_bytes, local_image_path = get_local_employee_image(employee_id)
    if local_user is None and local_image_bytes is not None:
        remove_local_employee_images(employee_id)
        local_image_bytes, local_image_path = (None, None)

    image_bytes = get_erp_employee_image_blob(employee_id, employee=emp)
    image_base64 = to_data_uri(image_bytes, '.jpg') if image_bytes else None

    registered = local_user is not None
    status_text = (
        'Đã có thông tin trong hệ thống chấm công'
        if registered
        else 'Chưa có thông tin trong hệ thống chấm công'
    )

    employee_payload = {
        **emp,
        'employee_id': employee_id,
        'user_id': local_user.id if local_user else None,
        'registered': registered,
        'has_face': has_face,
        'face_count': fc,
        'has_local_image': bool(local_image_bytes) if local_user is not None else False,
        'status_text': status_text,
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
            local_image_bytes, _ = get_local_employee_image(u.employee_id)
            has_local_image = local_image_bytes is not None

            employees.append({
                'id': u.id,
                'name': u.name,
                'employee_id': u.employee_id,
                'department': u.department or '',
                'position': u.position or '',
                'has_face': has_face,
                'face_count': fc,
                'has_local_image': has_local_image,
                'status_text': 'Đã đăng ký khuôn mặt' if has_face else 'Đã có trong hệ thống (chưa có khuôn mặt)',
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
    image_bytes, image_path = get_local_employee_image(employee_id)
    image_source = 'local'
    if user is None and image_bytes is not None:
        remove_local_employee_images(employee_id)
        image_bytes, image_path = (None, None)

    if image_bytes is None and getattr(g, 'erp_auth', None):
        erp_emp = None
        try:
            erp_emp = erp_http_client.get_employee(g.erp_auth, employee_id)
        except Exception:
            erp_emp = None

        image_bytes = get_erp_employee_image_blob(employee_id, employee=erp_emp)
        if image_bytes:
            image_source = 'erp'
            image_path = '.jpg'

    if image_bytes is None:
        return jsonify({'success': False, 'message': 'Không tìm thấy ảnh nhân viên'}), 404

    employee_payload = {
        'employee_id': employee_id,
        'name': user.name if user else employee_id,
        'department': user.department if user else '',
        'position': user.position if user else '',
        'has_face': face_encoding_count(user.face_encoding) > 0 if user else False,
        'face_count': face_encoding_count(user.face_encoding) if user else 0,
        'status_text': 'Đã đăng ký khuôn mặt' if (user and face_encoding_count(user.face_encoding) > 0) else 'Đã có trong hệ thống',
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
        'image_base64': to_data_uri(image_bytes, image_path),
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

        replace_all = False
        if isinstance(data, dict):
            replace_all = bool(data.get('replace_all', False))
        else:
            replace_all = (request.form.get('replace_all', 'false').lower() == 'true')

        current_encodings = normalize_face_encodings(user.face_encoding)
        new_encoding = normalize_face_encodings(face_encoding)[0] if normalize_face_encodings(face_encoding) else []
        if replace_all:
            user.face_encoding = [new_encoding] if new_encoding else []
        else:
            merged = current_encodings + ([new_encoding] if new_encoding else [])
            user.face_encoding = merged[-5:] if len(merged) > 5 else merged

        db.session.commit()
        save_local_employee_image(user.employee_id, image_bytes, '.jpg')
        state.load_known_faces()

        return jsonify({
            'success': True,
            'message': f'Đã cập nhật khuôn mặt cho {user.name}',
            'face_count': face_encoding_count(user.face_encoding),
        })
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

    if not getattr(g, 'erp_auth', None):
        return jsonify({'success': False, 'message': 'Phiên ERP hết hạn. Vui lòng đăng nhập lại'}), 401

    image_bytes, _ = get_local_employee_image(employee_id)
    if image_bytes is None:
        return jsonify({'success': False, 'message': 'Không có ảnh local để đẩy ERP'}), 404

    try:
        erp_http_client.upload_employee_image(
            g.erp_auth,
            employee_id,
            image_bytes,
            filename=f'{sanitize_employee_id(employee_id)}.jpg',
        )
        return jsonify({'success': True, 'message': f'Đã đẩy ảnh lên ERP cho {employee_id}'})
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
        save_local_employee_image(employee_id, image_blob, '.jpg')
        state.load_known_faces()
        return jsonify({'success': True, 'message': f'Đã tải lại dữ liệu từ ERP cho {user.name}'})
    except ERPServiceError as exc:
        return jsonify({'success': False, 'message': exc.message}), (exc.status_code or 500)
    except Exception as exc:
        db.session.rollback()
        return jsonify({'success': False, 'message': str(exc)}), 500
