# -*- coding: utf-8 -*-
"""Attendance routes — check-in, stats, report.  (maps to frontend modules/attendance.js)"""

import io
import os
import json
import base64
import math
from datetime import datetime, date

import cv2
import numpy as np
from PIL import Image as PILImage
from flask import Blueprint, request, jsonify, send_file, g

from backend.models.database import db, User, Attendance, EmployeeImage
from backend.models.erp_integration import erp_attendance
from backend.face_encoding_utils import face_encoding_count
from backend.services.camera_registry import camera_registry
from backend.services.report_service import (
    build_report_filter_text,
    build_report_rows,
    build_report_workbook,
    serialize_report_rows,
)
from backend.services.system_settings_service import (
    get_attendance_cooldown_seconds,
    get_attendance_mode,
)
from backend.runtime import ensure_data_dir, ensure_db_path
from backend.routes._state import state
from backend.routes._helpers import (
    admin_required,
    employee_required,
    parse_location_payload, location_to_text,
    get_runtime_location, save_attendance_location,
    serialize_attendance_location,
    serialize_attendance_locations,
    resolve_request_auth_mode,
)

attendance_bp = Blueprint('attendance', __name__)
DEFAULT_ATTENDANCE_COOLDOWN_SECONDS = 600


def _normalize_attendance_type(value):
    attendance_type = str(value or 'checkin').strip().lower()
    if attendance_type in {'auto', 'record', 'auto_record', 'ghi_cham_cong'}:
        return 'auto'
    if attendance_type in {'checkout', 'check_out', 'out'}:
        return 'checkout'
    return 'checkin'


def _attendance_type_label(attendance_type):
    normalized = _normalize_attendance_type(attendance_type)
    if normalized == 'checkout':
        return 'Checkout'
    if normalized == 'auto':
        return 'Ghi chấm công'
    return 'Checkin'


def _erp_attendance_code(attendance_type, attendance_code=None):
    provided = str(attendance_code or '').strip().upper()
    if provided:
        return provided
    return 'OUT' if attendance_type == 'checkout' else 'IN'


def _location_source(attendance_type, has_request_location):
    suffix = 'request' if has_request_location else 'runtime'
    return f'{attendance_type}_{suffix}'


def _serialize_attendance_user(user):
    if not user:
        return None

    return {
        'id': user.id,
        'name': user.name,
        'employee_id': user.employee_id,
        'department': user.department,
        'position': user.position,
    }


def _distance_to_similarity_percent(distance):
    try:
        score = 1.0 - float(distance)
    except (TypeError, ValueError):
        return None
    score = max(0.0, min(1.0, score))
    return round(score * 100.0, 2)


def _resolve_effective_attendance_type(requested_attendance_type):
    attendance_mode = get_attendance_mode()
    attendance_type = _normalize_attendance_type(requested_attendance_type)

    if attendance_mode == 'auto_record':
        return attendance_mode, 'auto'

    if attendance_type == 'auto':
        attendance_type = 'checkin'

    return attendance_mode, attendance_type


def _parse_cooldown_seconds(value, default_seconds=DEFAULT_ATTENDANCE_COOLDOWN_SECONDS):
    try:
        parsed = int(float(value))
    except (TypeError, ValueError):
        parsed = int(default_seconds)
    return max(0, min(parsed, 24 * 60 * 60))


def _format_cooldown_text(total_seconds):
    safe_seconds = max(0, int(total_seconds or 0))
    hours = safe_seconds // 3600
    minutes = (safe_seconds % 3600) // 60
    seconds = safe_seconds % 60

    parts = []
    if hours:
        parts.append(f'{hours} giờ')
    if minutes:
        parts.append(f'{minutes} phút')
    if seconds or not parts:
        parts.append(f'{seconds} giây')
    return ' '.join(parts)


def _is_within_cooldown(current_time, previous_time, cooldown_seconds):
    if cooldown_seconds <= 0 or previous_time is None:
        return False
    return (current_time - previous_time).total_seconds() < cooldown_seconds


def _cooldown_remaining_seconds(current_time, previous_time, cooldown_seconds):
    if cooldown_seconds <= 0 or previous_time is None:
        return 0

    remaining = cooldown_seconds - (current_time - previous_time).total_seconds()
    if remaining <= 0:
        return 0
    return int(math.ceil(remaining))


def _apply_attendance_action(
    user,
    attendance_type='checkin',
    request_location=None,
    attendance_code=None,
    erp_auth=None,
    cooldown_seconds=DEFAULT_ATTENDANCE_COOLDOWN_SECONDS,
):
    attendance_type = _normalize_attendance_type(attendance_type)
    cooldown_seconds = _parse_cooldown_seconds(cooldown_seconds)
    today_val = date.today()
    current_time = datetime.now()

    if attendance_type == 'auto':
        cooldown_text = _format_cooldown_text(cooldown_seconds)

        if cooldown_seconds > 0:
            latest_record = Attendance.query.filter_by(
                user_id=user.id,
                date=today_val,
            ).filter(Attendance.check_in_time.isnot(None)).order_by(Attendance.check_in_time.desc()).first()

            if latest_record and _is_within_cooldown(
                current_time,
                latest_record.check_in_time,
                cooldown_seconds,
            ):
                remaining_seconds = _cooldown_remaining_seconds(
                    current_time,
                    latest_record.check_in_time,
                    cooldown_seconds,
                )
                return {
                    'ok': False,
                    'status_code': 400,
                    'message': f'Chỉ được ghi chấm công 1 lần mỗi {cooldown_text}',
                    'cooldown_remaining_seconds': remaining_seconds,
                }

        if cooldown_seconds > 0:
            recent_minutes = max(1, (cooldown_seconds + 59) // 60)
            if erp_attendance.check_recent_attendance(user.employee_id, minutes=recent_minutes, auth=erp_auth):
                latest_record_for_erp = Attendance.query.filter_by(
                    user_id=user.id,
                    date=today_val,
                ).filter(Attendance.check_in_time.isnot(None)).order_by(Attendance.check_in_time.desc()).first()
                remaining_seconds = cooldown_seconds
                if latest_record_for_erp and latest_record_for_erp.check_in_time:
                    remaining_seconds = _cooldown_remaining_seconds(
                        current_time,
                        latest_record_for_erp.check_in_time,
                        cooldown_seconds,
                    ) or cooldown_seconds
                return {
                    'ok': False,
                    'status_code': 400,
                    'message': f'Đã có ghi chấm công trong ERP gần {cooldown_text}',
                    'cooldown_remaining_seconds': remaining_seconds,
                }

        status = 'present'
        if current_time.hour > 8 or (current_time.hour == 8 and current_time.minute > 30):
            status = 'late'

        new_attendance = Attendance(
            user_id=user.id,
            check_in_time=current_time,
            date=today_val,
            status=status,
        )
        db.session.add(new_attendance)
        db.session.commit()

        effective_location = request_location or get_runtime_location()
        if effective_location:
            try:
                save_attendance_location(
                    new_attendance.id,
                    effective_location,
                    source=_location_source('checkin', bool(request_location)),
                )
            except Exception as loc_exc:
                db.session.rollback()
                print(f"record location save error: {loc_exc}")

        erp_success = erp_attendance.create_attendance_record(
            employee_id=user.employee_id,
            attendance_time=current_time,
            attendance_code=_erp_attendance_code('record', attendance_code),
            auth=erp_auth,
        )

        message = f'Ghi chấm công thành công cho {user.name}'
        if not erp_success:
            message += ' (lỗi ghi ERP)'

        return {
            'ok': True,
            'status_code': 200,
            'message': message,
            'attendance': new_attendance,
            'location': effective_location,
            'location_text': location_to_text(effective_location) if effective_location else '',
            'attendance_type': 'record',
        }

    active_attendance = Attendance.query.filter_by(
        user_id=user.id,
        date=today_val,
    ).filter(Attendance.check_out_time.is_(None)).order_by(Attendance.check_in_time.desc()).first()

    if attendance_type == 'checkout':
        cooldown_text = _format_cooldown_text(cooldown_seconds)

        if cooldown_seconds > 0:
            latest_checkout = Attendance.query.filter_by(
                user_id=user.id,
                date=today_val,
            ).filter(Attendance.check_out_time.isnot(None)).order_by(Attendance.check_out_time.desc()).first()

            if latest_checkout and _is_within_cooldown(
                current_time,
                latest_checkout.check_out_time,
                cooldown_seconds,
            ):
                remaining_seconds = _cooldown_remaining_seconds(
                    current_time,
                    latest_checkout.check_out_time,
                    cooldown_seconds,
                )
                return {
                    'ok': False,
                    'status_code': 400,
                    'message': f'Chỉ được Checkout 1 lần mỗi {cooldown_text}',
                    'cooldown_remaining_seconds': remaining_seconds,
                }

        if not active_attendance:
            return {
                'ok': False,
                'status_code': 400,
                'message': f'Nhân viên {user.name} chưa Checkin nên không thể Checkout',
            }

        active_attendance.check_out_time = current_time
        db.session.commit()

        effective_location = request_location or get_runtime_location()
        if effective_location:
            try:
                save_attendance_location(
                    active_attendance.id,
                    effective_location,
                    source=_location_source('checkout', bool(request_location)),
                )
            except Exception as loc_exc:
                db.session.rollback()
                print(f"checkout location save error: {loc_exc}")

        erp_success = erp_attendance.create_attendance_record(
            employee_id=user.employee_id,
            attendance_time=current_time,
            attendance_code=_erp_attendance_code('checkout', attendance_code),
            auth=erp_auth,
        )

        message = f'Checkout thành công cho {user.name}'
        if not erp_success:
            message += ' (lỗi ghi ERP)'

        return {
            'ok': True,
            'status_code': 200,
            'message': message,
            'attendance': active_attendance,
            'location': effective_location,
            'location_text': location_to_text(effective_location) if effective_location else '',
            'attendance_type': 'checkout',
        }

    if active_attendance:
        return {
            'ok': False,
            'status_code': 400,
            'message': f'Nhân viên {user.name} đã Checkin và chưa Checkout',
        }

    cooldown_text = _format_cooldown_text(cooldown_seconds)
    latest_checkin = None
    if cooldown_seconds > 0:
        latest_checkin = Attendance.query.filter_by(
            user_id=user.id,
            date=today_val,
        ).filter(Attendance.check_in_time.isnot(None)).order_by(Attendance.check_in_time.desc()).first()

        if latest_checkin and _is_within_cooldown(
            current_time,
            latest_checkin.check_in_time,
            cooldown_seconds,
        ):
            remaining_seconds = _cooldown_remaining_seconds(
                current_time,
                latest_checkin.check_in_time,
                cooldown_seconds,
            )
            return {
                'ok': False,
                'status_code': 400,
                'message': f'Chỉ được Checkin 1 lần mỗi {cooldown_text}',
                'cooldown_remaining_seconds': remaining_seconds,
            }

    if cooldown_seconds > 0:
        recent_minutes = max(1, (cooldown_seconds + 59) // 60)
        if erp_attendance.check_recent_attendance(user.employee_id, minutes=recent_minutes, auth=erp_auth):
            remaining_seconds = cooldown_seconds
            if latest_checkin and latest_checkin.check_in_time:
                remaining_seconds = _cooldown_remaining_seconds(
                    current_time,
                    latest_checkin.check_in_time,
                    cooldown_seconds,
                ) or cooldown_seconds
            return {
                'ok': False,
                'status_code': 400,
                'message': f'Đã Checkin trong ERP gần {cooldown_text}',
                'cooldown_remaining_seconds': remaining_seconds,
            }

    status = 'present'
    if current_time.hour > 8 or (current_time.hour == 8 and current_time.minute > 30):
        status = 'late'

    new_attendance = Attendance(
        user_id=user.id,
        check_in_time=current_time,
        date=today_val,
        status=status,
    )
    db.session.add(new_attendance)
    db.session.commit()

    effective_location = request_location or get_runtime_location()
    if effective_location:
        try:
            save_attendance_location(
                new_attendance.id,
                effective_location,
                source=_location_source('checkin', bool(request_location)),
            )
        except Exception as loc_exc:
            db.session.rollback()
            print(f"checkin location save error: {loc_exc}")

    erp_success = erp_attendance.create_attendance_record(
        employee_id=user.employee_id,
        attendance_time=current_time,
        attendance_code=_erp_attendance_code('checkin', attendance_code),
        auth=erp_auth,
    )

    message = f'Checkin thành công cho {user.name}'
    if not erp_success:
        message += ' (lỗi ghi ERP)'

    return {
        'ok': True,
        'status_code': 200,
        'message': message,
        'attendance': new_attendance,
        'location': effective_location,
        'location_text': location_to_text(effective_location) if effective_location else '',
        'attendance_type': 'checkin',
    }


@attendance_bp.route('/api/check_attendance', methods=['POST'])
@admin_required
def check_attendance():
    try:
        data = request.get_json(silent=True) or {}
        user_id = data.get('user_id')
        request_location = parse_location_payload(data)
        requested_attendance_type = _normalize_attendance_type(data.get('attendance_type'))
        attendance_cooldown_seconds = get_attendance_cooldown_seconds()
        attendance_mode, attendance_type = _resolve_effective_attendance_type(requested_attendance_type)
        if not user_id:
            return jsonify({'success': False, 'message': 'Không tìm thấy user_id'}), 400

        user = User.query.get(user_id)
        if not user:
            return jsonify({'success': False, 'message': 'Không tìm thấy nhân viên'}), 404

        action_result = _apply_attendance_action(
            user=user,
            attendance_type=attendance_type,
            request_location=request_location,
            erp_auth=getattr(g, 'erp_auth', None),
            cooldown_seconds=attendance_cooldown_seconds,
        )
        if not action_result.get('ok'):
            return jsonify({
                'success': False,
                'message': action_result.get('message') or 'Không thể chấm công',
                'cooldown_remaining_seconds': int(action_result.get('cooldown_remaining_seconds') or 0),
                'user': _serialize_attendance_user(user),
            }), int(action_result.get('status_code') or 400)

        attendance_row = action_result.get('attendance')
        if attendance_row is not None:
            location_bundle = serialize_attendance_locations(attendance_row.id)
            checkin_location_text = ((location_bundle or {}).get('checkin') or {}).get('text', '')
            checkout_location_text = ((location_bundle or {}).get('checkout') or {}).get('text', '')
        else:
            checkin_location_text = ''
            checkout_location_text = ''

        return jsonify({
            'success': True,
            'message': action_result.get('message') or 'Chấm công thành công',
            'attendance_mode': attendance_mode,
            'requested_attendance_type': requested_attendance_type,
            'attendance_type': action_result.get('attendance_type') or attendance_type,
            'attendance_type_label': _attendance_type_label(action_result.get('attendance_type') or attendance_type),
            'user': _serialize_attendance_user(user),
            'location': action_result.get('location'),
            'location_text': action_result.get('location_text') or '',
            'check_in_time': (
                attendance_row.check_in_time.strftime('%H:%M:%S')
                if attendance_row and attendance_row.check_in_time
                else ''
            ),
            'check_out_time': (
                attendance_row.check_out_time.strftime('%H:%M:%S')
                if attendance_row and attendance_row.check_out_time
                else ''
            ),
            'check_in_location_text': checkin_location_text,
            'check_out_location_text': checkout_location_text,
        })
    except Exception as e:
        return jsonify({'success': False, 'message': str(e)}), 500


@attendance_bp.route('/api/attendance_image', methods=['POST'])
@admin_required
def attendance_image():
    try:
        attendance_code = None
        request_location = None
        include_preview = False
        attendance_type = 'checkin'
        requested_attendance_type = 'checkin'
        attendance_cooldown_seconds = get_attendance_cooldown_seconds()
        if 'image' in request.files:
            image_file = request.files['image']
            attendance_code = request.form.get('attendance_code')
            requested_attendance_type = _normalize_attendance_type(request.form.get('attendance_type'))
            attendance_type = requested_attendance_type
            include_preview_raw = request.form.get('include_preview', 'false')
            include_preview = str(include_preview_raw).strip().lower() in {'1', 'true', 'yes', 'on'}
            request_location = parse_location_payload({
                'latitude': request.form.get('latitude'),
                'longitude': request.form.get('longitude'),
                'accuracy': request.form.get('accuracy'),
                'label': request.form.get('location_label') or request.form.get('location'),
                'provider': request.form.get('location_provider'),
            })
            if not request_location:
                location_raw = request.form.get('location')
                if location_raw:
                    try:
                        request_location = parse_location_payload({'location': json.loads(location_raw)})
                    except Exception:
                        request_location = None
            with state.face_recognition_lock:
                face_encoding, error = state.face_recognizer.encode_face_from_image(image_file)
        else:
            data = request.get_json()
            if not data or 'image_base64' not in data:
                return jsonify({'success': False, 'message': 'Thiếu ảnh'}), 400
            image_base64 = data['image_base64']
            attendance_code = data.get('attendance_code')
            requested_attendance_type = _normalize_attendance_type(data.get('attendance_type'))
            attendance_type = requested_attendance_type
            include_preview_raw = data.get('include_preview', False)
            if isinstance(include_preview_raw, str):
                include_preview = include_preview_raw.strip().lower() in {'1', 'true', 'yes', 'on'}
            else:
                include_preview = bool(include_preview_raw)
            request_location = parse_location_payload(data)
            with state.face_recognition_lock:
                face_encoding, error = state.face_recognizer.encode_face_from_base64(image_base64)

        attendance_mode, attendance_type = _resolve_effective_attendance_type(requested_attendance_type)

        if error:
            return jsonify({'success': False, 'message': error}), 400

        with state.face_recognition_lock:
            matches = state.face_recognizer.compare_faces(
                state.face_recognizer.known_face_encodings, face_encoding, tolerance=0.5
            )
            face_distances = state.face_recognizer.compute_distance(
                state.face_recognizer.known_face_encodings, face_encoding
            )

        if len(face_distances) == 0 or not any(matches):
            return jsonify({'success': False, 'message': 'Không nhận diện được nhân viên'}), 404

        best_match_index = int(np.argmin(face_distances))
        if not matches[best_match_index]:
            return jsonify({'success': False, 'message': 'Không nhận diện được nhân viên'}), 404

        user_id = state.face_recognizer.known_face_ids[best_match_index]
        user = User.query.get(user_id)
        if not user:
            return jsonify({'success': False, 'message': 'Không tìm thấy nhân viên'}), 404

        # Anti-spoofing check
        if 'image' in request.files:
            image_file = request.files['image']
            image_file.stream.seek(0)
            file_bytes = np.asarray(bytearray(image_file.read()), dtype=np.uint8)
            image = cv2.imdecode(file_bytes, cv2.IMREAD_COLOR)
        else:
            image_bytes = base64.b64decode(
                image_base64.split(',')[1] if ',' in image_base64 else image_base64
            )
            pil_img = PILImage.open(io.BytesIO(image_bytes))
            image = np.array(pil_img)

        results = state.face_recognizer.engine.detect_and_encode(image)
        if len(results) == 0:
            return jsonify({'success': False, 'message': 'Không tìm thấy khuôn mặt'}), 400

        res = results[0]
        bbox = res['bbox']

        height, width = image.shape[:2]
        x1 = max(0, min(width - 1, int(bbox[0])))
        y1 = max(0, min(height - 1, int(bbox[1])))
        x2 = max(x1 + 1, min(width, int(bbox[2])))
        y2 = max(y1 + 1, min(height, int(bbox[3])))
        detection_bbox = [x1, y1, x2, y2]

        preview_image_base64 = None
        if include_preview:
            try:
                preview_frame = image.copy()
                if len(preview_frame.shape) == 2:
                    preview_frame = cv2.cvtColor(preview_frame, cv2.COLOR_GRAY2BGR)
                elif len(preview_frame.shape) == 3 and preview_frame.shape[2] == 4:
                    preview_frame = cv2.cvtColor(preview_frame, cv2.COLOR_RGBA2BGR)

                cv2.rectangle(preview_frame, (x1, y1), (x2, y2), (0, 220, 255), 2)
                cv2.putText(
                    preview_frame,
                    user.name,
                    (x1, max(26, y1 - 10)),
                    cv2.FONT_HERSHEY_SIMPLEX,
                    0.7,
                    (0, 220, 255),
                    2,
                    cv2.LINE_AA,
                )

                ok, encoded = cv2.imencode('.jpg', preview_frame, [int(cv2.IMWRITE_JPEG_QUALITY), 82])
                if ok:
                    preview_image_base64 = (
                        'data:image/jpeg;base64,'
                        + base64.b64encode(encoded.tobytes()).decode('utf-8')
                    )
            except Exception as preview_exc:
                print(f"attendance_image preview generation error: {preview_exc}")

        bbox_xywh = [bbox[0], bbox[1], bbox[2] - bbox[0], bbox[3] - bbox[1]]
        is_real, spoof_score = state.face_recognizer.engine.check_anti_spoofing(image, bbox_xywh)
        if not is_real:
            return jsonify({
                'success': False,
                'message': 'Phát hiện khuôn mặt giả mạo!',
                'user': _serialize_attendance_user(user),
                'detection_bbox': detection_bbox,
                'face_count': len(results),
                'preview_image_base64': preview_image_base64,
            }), 400

        action_result = _apply_attendance_action(
            user=user,
            attendance_type=attendance_type,
            request_location=request_location,
            attendance_code=attendance_code,
            erp_auth=getattr(g, 'erp_auth', None),
            cooldown_seconds=attendance_cooldown_seconds,
        )
        if not action_result.get('ok'):
            return jsonify({
                'success': False,
                'message': action_result.get('message') or 'Không thể chấm công',
                'cooldown_remaining_seconds': int(action_result.get('cooldown_remaining_seconds') or 0),
                'user': _serialize_attendance_user(user),
                'detection_bbox': detection_bbox,
                'face_count': len(results),
                'preview_image_base64': preview_image_base64,
            }), int(action_result.get('status_code') or 400)

        attendance_row = action_result.get('attendance')
        location_bundle = serialize_attendance_locations(attendance_row.id) if attendance_row else {}

        return jsonify({
            'success': True,
            'message': action_result.get('message') or f'Điểm danh thành công cho {user.name}',
            'user': _serialize_attendance_user(user),
            'status': attendance_row.status if attendance_row else '',
            'attendance_mode': attendance_mode,
            'requested_attendance_type': requested_attendance_type,
            'attendance_type': action_result.get('attendance_type') or attendance_type,
            'attendance_type_label': _attendance_type_label(action_result.get('attendance_type') or attendance_type),
            'check_in_time': attendance_row.check_in_time.strftime('%H:%M:%S') if attendance_row and attendance_row.check_in_time else '',
            'check_out_time': attendance_row.check_out_time.strftime('%H:%M:%S') if attendance_row and attendance_row.check_out_time else '',
            'location': action_result.get('location'),
            'location_text': action_result.get('location_text') or '',
            'check_in_location_text': ((location_bundle or {}).get('checkin') or {}).get('text', ''),
            'check_out_location_text': ((location_bundle or {}).get('checkout') or {}).get('text', ''),
            'detection_bbox': detection_bbox,
            'face_count': len(results),
            'preview_image_base64': preview_image_base64,
        })
    except Exception as e:
        return jsonify({'success': False, 'message': str(e)}), 500


@attendance_bp.route('/api/employee/attendance_image', methods=['POST'])
@employee_required
def employee_attendance_image():
    try:
        attendance_code = None
        request_location = None
        attendance_type = 'checkin'
        requested_attendance_type = 'checkin'
        attendance_cooldown_seconds = get_attendance_cooldown_seconds()

        image_bytes = b''
        if 'image' in request.files:
            image_file = request.files['image']
            attendance_code = request.form.get('attendance_code')
            requested_attendance_type = _normalize_attendance_type(request.form.get('attendance_type'))
            attendance_type = requested_attendance_type
            request_location = parse_location_payload({
                'latitude': request.form.get('latitude'),
                'longitude': request.form.get('longitude'),
                'accuracy': request.form.get('accuracy'),
                'label': request.form.get('location_label') or request.form.get('location'),
                'provider': request.form.get('location_provider'),
            })

            image_bytes = image_file.read() or b''
            if not image_bytes:
                return jsonify({'success': False, 'message': 'Thiếu ảnh'}), 400

            with state.face_recognition_lock:
                face_encoding, error = state.face_recognizer.encode_face_from_image(io.BytesIO(image_bytes))
        else:
            data = request.get_json(silent=True) or {}
            image_base64 = data.get('image_base64')
            if not image_base64:
                return jsonify({'success': False, 'message': 'Thiếu ảnh'}), 400

            attendance_code = data.get('attendance_code')
            requested_attendance_type = _normalize_attendance_type(data.get('attendance_type'))
            attendance_type = requested_attendance_type
            request_location = parse_location_payload(data)

            raw_base64 = image_base64.split(',', 1)[1] if ',' in image_base64 else image_base64
            try:
                image_bytes = base64.b64decode(raw_base64)
            except Exception:
                return jsonify({'success': False, 'message': 'Dữ liệu ảnh base64 không hợp lệ'}), 400

            with state.face_recognition_lock:
                face_encoding, error = state.face_recognizer.encode_face_from_base64(image_base64)

        attendance_mode, attendance_type = _resolve_effective_attendance_type(requested_attendance_type)

        if error:
            return jsonify({'success': False, 'message': error}), 400

        with state.face_recognition_lock:
            matches = state.face_recognizer.compare_faces(
                state.face_recognizer.known_face_encodings,
                face_encoding,
                tolerance=0.5,
            )
            face_distances = state.face_recognizer.compute_distance(
                state.face_recognizer.known_face_encodings,
                face_encoding,
            )

        if len(face_distances) == 0:
            return jsonify({
                'success': False,
                'message': 'Không nhận diện được nhân viên',
                'similarity_percent': 0,
                'mismatch': False,
            }), 404

        best_match_index = int(np.argmin(face_distances))
        best_distance = float(face_distances[best_match_index])
        similarity_percent = _distance_to_similarity_percent(best_distance) or 0

        if not any(matches) or not matches[best_match_index]:
            return jsonify({
                'success': False,
                'message': 'Không nhận diện được nhân viên',
                'similarity_percent': similarity_percent,
                'mismatch': False,
                'attendance_mode': attendance_mode,
                'requested_attendance_type': requested_attendance_type,
                'attendance_type': attendance_type,
            }), 404

        matched_user_id = state.face_recognizer.known_face_ids[best_match_index]
        matched_user = User.query.get(matched_user_id)
        if not matched_user:
            return jsonify({'success': False, 'message': 'Không tìm thấy nhân viên'}), 404

        employee_user = getattr(g, 'employee_user', None)
        if employee_user is None:
            return jsonify({'success': False, 'message': 'Phiên đăng nhập không hợp lệ'}), 401

        image = cv2.imdecode(np.frombuffer(image_bytes, dtype=np.uint8), cv2.IMREAD_COLOR)
        if image is None:
            pil_img = PILImage.open(io.BytesIO(image_bytes))
            if pil_img.mode != 'RGB':
                pil_img = pil_img.convert('RGB')
            image = cv2.cvtColor(np.array(pil_img), cv2.COLOR_RGB2BGR)

        with state.face_recognition_lock:
            detect_results = state.face_recognizer.engine.detect_and_encode(image)

        if len(detect_results) == 0:
            return jsonify({'success': False, 'message': 'Không tìm thấy khuôn mặt'}), 400

        bbox = detect_results[0].get('bbox')
        if bbox is None or len(bbox) < 4:
            return jsonify({'success': False, 'message': 'Không xác định được vùng khuôn mặt'}), 400

        bbox_xywh = [bbox[0], bbox[1], bbox[2] - bbox[0], bbox[3] - bbox[1]]
        is_real, spoof_score = state.face_recognizer.engine.check_anti_spoofing(image, bbox_xywh)
        if not is_real:
            return jsonify({
                'success': False,
                'message': 'Phát hiện khuôn mặt giả mạo!',
                'similarity_percent': similarity_percent,
                'mismatch': False,
                'spoof_score': float(spoof_score),
                'attendance_mode': attendance_mode,
                'requested_attendance_type': requested_attendance_type,
                'attendance_type': attendance_type,
            }), 400

        if matched_user.id != employee_user.id:
            return jsonify({
                'success': False,
                'message': (
                    f'Khuôn mặt được nhận diện là {matched_user.name} '
                    f'({matched_user.employee_id}), không khớp tài khoản đang đăng nhập'
                ),
                'mismatch': True,
                'similarity_percent': similarity_percent,
                'expected_user': _serialize_attendance_user(employee_user),
                'detected_user': _serialize_attendance_user(matched_user),
                'attendance_mode': attendance_mode,
                'requested_attendance_type': requested_attendance_type,
                'attendance_type': attendance_type,
            }), 409

        action_result = _apply_attendance_action(
            user=employee_user,
            attendance_type=attendance_type,
            request_location=request_location,
            attendance_code=attendance_code,
            erp_auth=None,
            cooldown_seconds=attendance_cooldown_seconds,
        )
        if not action_result.get('ok'):
            return jsonify({
                'success': False,
                'message': action_result.get('message') or 'Không thể chấm công',
                'cooldown_remaining_seconds': int(action_result.get('cooldown_remaining_seconds') or 0),
                'similarity_percent': similarity_percent,
                'mismatch': False,
                'user': _serialize_attendance_user(employee_user),
                'attendance_mode': attendance_mode,
                'requested_attendance_type': requested_attendance_type,
                'attendance_type': attendance_type,
            }), int(action_result.get('status_code') or 400)

        attendance_row = action_result.get('attendance')
        location_bundle = serialize_attendance_locations(attendance_row.id) if attendance_row else {}

        return jsonify({
            'success': True,
            'message': action_result.get('message') or f'Điểm danh thành công cho {employee_user.name}',
            'user': _serialize_attendance_user(employee_user),
            'status': attendance_row.status if attendance_row else '',
            'attendance_mode': attendance_mode,
            'requested_attendance_type': requested_attendance_type,
            'attendance_type': action_result.get('attendance_type') or attendance_type,
            'attendance_type_label': _attendance_type_label(action_result.get('attendance_type') or attendance_type),
            'check_in_time': attendance_row.check_in_time.strftime('%H:%M:%S') if attendance_row and attendance_row.check_in_time else '',
            'check_out_time': attendance_row.check_out_time.strftime('%H:%M:%S') if attendance_row and attendance_row.check_out_time else '',
            'location': action_result.get('location'),
            'location_text': action_result.get('location_text') or '',
            'check_in_location_text': ((location_bundle or {}).get('checkin') or {}).get('text', ''),
            'check_out_location_text': ((location_bundle or {}).get('checkout') or {}).get('text', ''),
            'similarity_percent': similarity_percent,
            'mismatch': False,
        })
    except Exception as exc:
        return jsonify({'success': False, 'message': str(exc)}), 500


@attendance_bp.route('/api/employee/attendance_detect', methods=['POST'])
@employee_required
def employee_attendance_detect():
    try:
        data = request.get_json(silent=True) or {}
        image_base64 = data.get('image_base64')
        if not image_base64:
            return jsonify({'success': False, 'message': 'Thiếu ảnh'}), 400

        raw_base64 = image_base64.split(',', 1)[1] if ',' in image_base64 else image_base64
        try:
            image_bytes = base64.b64decode(raw_base64)
        except Exception:
            return jsonify({'success': False, 'message': 'Dữ liệu ảnh base64 không hợp lệ'}), 400
        pil_img = PILImage.open(io.BytesIO(image_bytes))
        if pil_img.mode != 'RGB':
            pil_img = pil_img.convert('RGB')
        image = cv2.cvtColor(np.array(pil_img), cv2.COLOR_RGB2BGR)

        if image is None or image.size == 0:
            return jsonify({'success': False, 'message': 'Không đọc được dữ liệu ảnh'}), 400

        with state.face_recognition_lock:
            results = state.face_recognizer.engine.detect_and_encode(image)

            if not results:
                return jsonify({
                    'success': True,
                    'detected': False,
                    'matched': False,
                    'mismatch': False,
                    'similarity_percent': 0,
                    'expected_user': _serialize_attendance_user(getattr(g, 'employee_user', None)),
                    'detected_user': None,
                })

            embedding = results[0].get('embedding')
            if embedding is None:
                return jsonify({
                    'success': True,
                    'detected': True,
                    'matched': False,
                    'mismatch': False,
                    'similarity_percent': 0,
                    'expected_user': _serialize_attendance_user(getattr(g, 'employee_user', None)),
                    'detected_user': None,
                })

            matches = state.face_recognizer.compare_faces(
                state.face_recognizer.known_face_encodings,
                embedding,
                tolerance=0.5,
            )
            distances = state.face_recognizer.compute_distance(
                state.face_recognizer.known_face_encodings,
                embedding,
            )

        if len(distances) == 0:
            return jsonify({
                'success': True,
                'detected': True,
                'matched': False,
                'mismatch': False,
                'similarity_percent': 0,
                'expected_user': _serialize_attendance_user(getattr(g, 'employee_user', None)),
                'detected_user': None,
            })

        best_idx = int(np.argmin(distances))
        similarity_percent = _distance_to_similarity_percent(float(distances[best_idx])) or 0
        matched = bool(any(matches) and matches[best_idx])

        detected_user = None
        mismatch = False
        if matched:
            detected_user_id = state.face_recognizer.known_face_ids[best_idx]
            detected_user = User.query.get(detected_user_id)
            employee_user = getattr(g, 'employee_user', None)
            mismatch = bool(detected_user and employee_user and detected_user.id != employee_user.id)

        return jsonify({
            'success': True,
            'detected': True,
            'matched': matched,
            'mismatch': mismatch,
            'similarity_percent': similarity_percent,
            'expected_user': _serialize_attendance_user(getattr(g, 'employee_user', None)),
            'detected_user': _serialize_attendance_user(detected_user) if detected_user else None,
        })
    except Exception as exc:
        return jsonify({'success': False, 'message': str(exc)}), 500


@attendance_bp.route('/api/employee/attendance/history')
@employee_required
def employee_attendance_history():
    employee_user = getattr(g, 'employee_user', None)
    if employee_user is None:
        return jsonify({'success': False, 'message': 'Phiên đăng nhập không hợp lệ', 'records': []}), 401

    date_raw = str(request.args.get('date') or '').strip()
    start_date_raw = str(request.args.get('start_date') or '').strip()
    end_date_raw = str(request.args.get('end_date') or '').strip()

    date_filter = None
    start_date = None
    end_date = None
    try:
        if date_raw:
            date_filter = datetime.strptime(date_raw, '%Y-%m-%d').date()
        if start_date_raw:
            start_date = datetime.strptime(start_date_raw, '%Y-%m-%d').date()
        if end_date_raw:
            end_date = datetime.strptime(end_date_raw, '%Y-%m-%d').date()
    except ValueError:
        return jsonify({
            'success': False,
            'message': 'Định dạng ngày không hợp lệ (YYYY-MM-DD)',
            'records': [],
        }), 400

    try:
        limit_raw = int(request.args.get('limit') or 50)
    except (TypeError, ValueError):
        limit_raw = 50
    limit = max(1, min(limit_raw, 300))

    query = Attendance.query.filter_by(user_id=employee_user.id)
    if date_filter is not None:
        query = query.filter(Attendance.date == date_filter)
    else:
        if start_date is not None:
            query = query.filter(Attendance.date >= start_date)
        if end_date is not None:
            query = query.filter(Attendance.date <= end_date)

    rows = (
        query
        .order_by(Attendance.date.desc(), Attendance.check_in_time.desc())
        .limit(limit)
        .all()
    )

    records = []
    for row in rows:
        location_bundle = serialize_attendance_locations(row.id)
        checkin_location_payload = (location_bundle or {}).get('checkin')
        checkout_location_payload = (location_bundle or {}).get('checkout')

        if row.check_out_time:
            status_text = 'Đã Checkout'
        else:
            status_text = 'Đúng giờ' if row.status == 'present' else ('Trễ' if row.status == 'late' else row.status)

        records.append({
            'id': row.id,
            'date': row.date.strftime('%Y-%m-%d') if row.date else '',
            'check_in_time': row.check_in_time.strftime('%H:%M:%S') if row.check_in_time else '',
            'check_out_time': row.check_out_time.strftime('%H:%M:%S') if row.check_out_time else '',
            'status': status_text,
            'check_in_location_text': (checkin_location_payload or {}).get('text', ''),
            'check_out_location_text': (checkout_location_payload or {}).get('text', ''),
        })

    return jsonify({
        'success': True,
        'employee': _serialize_attendance_user(employee_user),
        'records': records,
        'filters': {
            'date': date_filter.strftime('%Y-%m-%d') if date_filter else '',
            'start_date': start_date.strftime('%Y-%m-%d') if start_date else '',
            'end_date': end_date.strftime('%Y-%m-%d') if end_date else '',
            'limit': limit,
        },
    })


@attendance_bp.route('/api/employee/attendance_settings')
@employee_required
def employee_attendance_settings():
    attendance_mode, effective_type = _resolve_effective_attendance_type('checkin')
    return jsonify({
        'success': True,
        'attendance_settings': {
            'mode': attendance_mode,
            'cooldown_seconds': get_attendance_cooldown_seconds(),
            'effective_attendance_type': effective_type,
        },
    })


@attendance_bp.route('/api/attendance_detect', methods=['POST'])
@admin_required
def attendance_detect():
    try:
        data = request.get_json(silent=True) or {}
        image_base64 = data.get('image_base64')
        if not image_base64:
            return jsonify({'success': False, 'message': 'Thiếu ảnh', 'detections': []}), 400

        raw_base64 = image_base64.split(',', 1)[1] if ',' in image_base64 else image_base64
        try:
            image_bytes = base64.b64decode(raw_base64)
        except Exception:
            return jsonify({'success': False, 'message': 'Dữ liệu ảnh base64 không hợp lệ', 'detections': []}), 400

        pil_img = PILImage.open(io.BytesIO(image_bytes))
        if pil_img.mode != 'RGB':
            pil_img = pil_img.convert('RGB')
        image = cv2.cvtColor(np.array(pil_img), cv2.COLOR_RGB2BGR)

        if image is None or image.size == 0:
            return jsonify({'success': False, 'message': 'Không đọc được dữ liệu ảnh', 'detections': []}), 400

        frame_height, frame_width = image.shape[:2]

        max_faces_raw = data.get('max_faces', 3)
        try:
            max_faces = int(max_faces_raw)
        except Exception:
            max_faces = 3
        max_faces = max(1, min(max_faces, 10))

        recognize_raw = data.get('recognize', False)
        if isinstance(recognize_raw, str):
            recognize = recognize_raw.strip().lower() in {'1', 'true', 'yes', 'on'}
        else:
            recognize = bool(recognize_raw)

        tolerance_raw = data.get('tolerance', 0.5)
        try:
            tolerance = float(tolerance_raw)
        except Exception:
            tolerance = 0.5
        tolerance = max(0.2, min(tolerance, 0.8))

        with state.face_recognition_lock:
            results = state.face_recognizer.engine.detect_and_encode(image)
            raw_known_encodings = state.face_recognizer.known_face_encodings
            known_encodings = list(raw_known_encodings) if raw_known_encodings is not None else []

            raw_known_names = state.face_recognizer.known_face_names
            known_names = list(raw_known_names) if raw_known_names is not None else []

            raw_known_ids = state.face_recognizer.known_face_ids
            known_ids = list(raw_known_ids) if raw_known_ids is not None else []

            sorted_results = sorted(
                results,
                key=lambda item: (item['bbox'][2] - item['bbox'][0]) * (item['bbox'][3] - item['bbox'][1]),
                reverse=True,
            )[:max_faces]

            detections = []
            for idx, res in enumerate(sorted_results):
                bbox = res.get('bbox')
                if bbox is None or len(bbox) < 4:
                    continue

                x1 = max(0, min(frame_width - 1, int(bbox[0])))
                y1 = max(0, min(frame_height - 1, int(bbox[1])))
                x2 = max(x1 + 1, min(frame_width, int(bbox[2])))
                y2 = max(y1 + 1, min(frame_height, int(bbox[3])))

                detection_item = {
                    'track_id': idx,
                    'bbox': [x1, y1, x2, y2],
                    'name': 'Unknown',
                    'user_id': None,
                    'matched': False,
                    'distance': None,
                }

                embedding = res.get('embedding')
                if recognize and embedding is not None and len(known_encodings) > 0:
                    matches = state.face_recognizer.compare_faces(
                        known_encodings,
                        embedding,
                        tolerance=tolerance,
                    )
                    distances = state.face_recognizer.compute_distance(known_encodings, embedding)

                    if len(distances) > 0:
                        best_idx = int(np.argmin(distances))
                        detection_item['distance'] = float(distances[best_idx])
                        matched_value = matches[best_idx] if best_idx < len(matches) else False
                        if isinstance(matched_value, np.ndarray):
                            matched_value = bool(np.all(matched_value))
                        else:
                            matched_value = bool(matched_value)

                        if matched_value:
                            detection_item['matched'] = True
                            detection_item['name'] = known_names[best_idx] if best_idx < len(known_names) else 'Known'
                            detection_item['user_id'] = known_ids[best_idx] if best_idx < len(known_ids) else None

                detections.append(detection_item)

        return jsonify({
            'success': True,
            'message': 'OK',
            'frame_width': frame_width,
            'frame_height': frame_height,
            'detected_count': len(detections),
            'recognition_enabled': recognize,
            'detections': detections,
        })
    except Exception as e:
        return jsonify({'success': False, 'message': str(e), 'detections': []}), 500


# --- Stats ---

@attendance_bp.route('/api/system_storage_stats')
@admin_required
def get_system_storage_stats():
    try:
        users = User.query.all()
        cameras = camera_registry.list_cameras()
        face_image_count = EmployeeImage.query.filter(EmployeeImage.image_blob.isnot(None)).count()

        paths_to_measure = []
        data_dir = ensure_data_dir()
        if os.path.isdir(data_dir):
            for root, _, files in os.walk(data_dir):
                for filename in files:
                    paths_to_measure.append(os.path.join(root, filename))

        camera_storage_path = str(camera_registry.storage_path)
        if os.path.isfile(camera_storage_path):
            paths_to_measure.append(camera_storage_path)

        db_path = str(ensure_db_path())
        if os.path.isfile(db_path):
            paths_to_measure.append(db_path)

        seen_paths = set()
        total_storage_bytes = 0
        for path in paths_to_measure:
            normalized_path = os.path.abspath(path)
            if normalized_path in seen_paths:
                continue
            seen_paths.add(normalized_path)
            if not os.path.isfile(normalized_path):
                continue
            try:
                total_storage_bytes += os.path.getsize(normalized_path)
            except OSError:
                continue

        enabled_camera_count = sum(1 for camera in cameras if camera.get('enabled'))

        return jsonify({'success': True, 'data': {
            'employee_count': len(users),
            'face_image_count': face_image_count,
            'face_sample_count': sum(face_encoding_count(user.face_encoding) for user in users),
            'storage_bytes': total_storage_bytes,
            'storage_mb': round(total_storage_bytes / (1024 * 1024), 2),
            'camera_count': len(cameras),
            'enabled_camera_count': enabled_camera_count,
            'data_dir': str(data_dir),
            'database_path': db_path,
        }})
    except Exception as exc:
        return jsonify({'success': False, 'message': str(exc), 'data': {}}), 500


@attendance_bp.route('/api/get_attendance_stats')
@admin_required
def get_attendance_stats():
    try:
        today = date.today()
        total_employees = User.query.count()
        today_records = Attendance.query.filter_by(date=today).order_by(
            Attendance.check_in_time.asc()
        ).all()

        unique_users = {}
        for rec in today_records:
            if rec.user_id not in unique_users:
                unique_users[rec.user_id] = rec.status

        present_today = len(unique_users)
        late_today = sum(1 for s in unique_users.values() if s == 'late')
        on_time_today = sum(1 for s in unique_users.values() if s == 'present')

        return jsonify({
            'success': True,
            'data': {
                'total_employees': total_employees,
                'present_today': present_today,
                'late_today': late_today,
                'on_time_today': on_time_today,
                'absent_today': max(0, total_employees - present_today)
            }
        })
    except Exception as e:
        return jsonify({'success': False, 'message': str(e)})


@attendance_bp.route('/api/get_recent_activity')
@admin_required
def get_recent_activity():
    try:
        records = (
            db.session.query(Attendance, User)
            .join(User, Attendance.user_id == User.id)
            .order_by(Attendance.check_in_time.desc())
            .limit(10)
            .all()
        )
        activities = []
        for att, user in records:
            activities.append({
                'name': user.name,
                'department': user.department,
                'time': att.check_in_time.strftime('%H:%M:%S %d/%m/%Y') if att.check_in_time else '',
                'status': 'Điểm danh'
            })
        return jsonify({'success': True, 'activities': activities})
    except Exception as e:
        return jsonify({'success': False, 'message': str(e), 'activities': []})


@attendance_bp.route('/api/get_today_attendance')
@admin_required
def get_today_attendance():
    try:
        today = date.today()
        records = (
            db.session.query(Attendance, User)
            .join(User, Attendance.user_id == User.id)
            .filter(Attendance.date == today)
            .order_by(Attendance.check_in_time.desc())
            .all()
        )
        data = []
        for att, user in records:
            location_bundle = serialize_attendance_locations(att.id)
            checkin_location_payload = (location_bundle or {}).get('checkin')
            checkout_location_payload = (location_bundle or {}).get('checkout')
            latest_location_payload = (location_bundle or {}).get('latest') or serialize_attendance_location(att.id)

            if att.check_out_time:
                status_text = 'Đã Checkout'
            else:
                status_text = 'Đúng giờ' if att.status == 'present' else ('Trễ' if att.status == 'late' else att.status)

            data.append({
                'name': user.name,
                'employee_id': user.employee_id,
                'department': user.department,
                'time': att.check_in_time.strftime('%H:%M:%S') if att.check_in_time else '',
                'check_in_time': att.check_in_time.strftime('%H:%M:%S') if att.check_in_time else '',
                'check_out_time': att.check_out_time.strftime('%H:%M:%S') if att.check_out_time else '',
                'status': status_text,
                'location': latest_location_payload,
                'location_text': (latest_location_payload or {}).get('text', ''),
                'check_in_location': checkin_location_payload,
                'check_out_location': checkout_location_payload,
                'check_in_location_text': (checkin_location_payload or {}).get('text', ''),
                'check_out_location_text': (checkout_location_payload or {}).get('text', ''),
            })
        return jsonify({'success': True, 'data': data})
    except Exception as e:
        return jsonify({'success': False, 'message': str(e), 'data': []})


@attendance_bp.route('/api/report')
@admin_required
def api_report():
    rows, normalized_filters, summary = build_report_rows(request.args.to_dict(flat=True))
    return jsonify({
        'success': True,
        'records': serialize_report_rows(rows),
        'summary': summary,
        'filters': {
            'start_date': normalized_filters['start_date'].strftime('%Y-%m-%d'),
            'end_date': normalized_filters['end_date'].strftime('%Y-%m-%d'),
            'start_time': normalized_filters['start_time'].strftime('%H:%M:%S') if normalized_filters.get('start_time') else '',
            'end_time': normalized_filters['end_time'].strftime('%H:%M:%S') if normalized_filters.get('end_time') else '',
            'status': normalized_filters['status'],
            'keyword': normalized_filters['keyword'],
            'sort_by': normalized_filters['sort_by'],
            'sort_dir': normalized_filters['sort_dir'],
            'description': build_report_filter_text(normalized_filters, summary),
        },
    })

    try:
        date_str = request.args.get('date', date.today().strftime('%Y-%m-%d'))
        report_date = datetime.strptime(date_str, '%Y-%m-%d').date()
    except ValueError:
        report_date = date.today()

    records = (
        db.session.query(Attendance, User)
        .join(User, Attendance.user_id == User.id)
        .filter(Attendance.date == report_date)
        .all()
    )
    res = []
    for att, user in records:
        location_bundle = serialize_attendance_locations(att.id)
        checkin_location_payload = (location_bundle or {}).get('checkin')
        checkout_location_payload = (location_bundle or {}).get('checkout')

        if att.check_out_time:
            status_text = 'Đã Checkout'
        else:
            status_text = 'Đúng giờ' if att.status == 'present' else ('Trễ' if att.status == 'late' else att.status)

        res.append({
            'name': user.name,
            'employee_id': user.employee_id,
            'department': user.department,
            'check_in_time': att.check_in_time.strftime('%H:%M:%S') if att.check_in_time else '',
            'check_out_time': att.check_out_time.strftime('%H:%M:%S') if att.check_out_time else '',
            'check_in_location': (checkin_location_payload or {}).get('text', ''),
            'check_out_location': (checkout_location_payload or {}).get('text', ''),
            'status': status_text,
        })
    return jsonify({'success': True, 'records': res})


@attendance_bp.route('/api/report/export_xlsx')
@admin_required
def api_report_export_xlsx():
    try:
        rows, normalized_filters, summary = build_report_rows(request.args.to_dict(flat=True))
        workbook_stream = build_report_workbook(rows, normalized_filters, summary)
        filename = (
            f"attendance_{normalized_filters['start_date'].strftime('%Y%m%d')}"
            f"_{normalized_filters['end_date'].strftime('%Y%m%d')}.xlsx"
        )
        return send_file(
            workbook_stream,
            as_attachment=True,
            download_name=filename,
            mimetype='application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        )
    except Exception as exc:
        return jsonify({'success': False, 'message': str(exc)}), 500


@attendance_bp.route('/api/report/push_to_erp', methods=['POST'])
@admin_required
def api_report_push_to_erp():
    payload = request.get_json(silent=True) or {}
    if resolve_request_auth_mode(default='internal') != 'system':
        return jsonify({
            'success': False,
            'message': 'Đang ở chế độ nội bộ. Vui lòng đăng nhập hệ thống để đẩy dữ liệu.',
        }), 403

    rows, normalized_filters, summary = build_report_rows(payload)
    if not rows:
        return jsonify({
            'success': True,
            'message': 'Không có dữ liệu điểm danh để đẩy lên ERP',
            'result': {
                'start_date': normalized_filters['start_date'].strftime('%Y-%m-%d'),
                'end_date': normalized_filters['end_date'].strftime('%Y-%m-%d'),
                'total': 0,
                'pushed': 0,
                'failed': 0,
                'failed_employee_ids': [],
                'records': 0,
            },
        })

    pushed = 0
    failed_ids = []
    failed_details = []
    total_events = 0
    for row in rows:
        att = row.get('_attendance')
        user = row.get('_user')
        if att is None or user is None:
            continue

        events = []
        checkin_time = att.check_in_time or datetime.combine(
            att.date or normalized_filters['start_date'],
            datetime.min.time(),
        )
        events.append(('IN', checkin_time))
        if att.check_out_time:
            events.append(('OUT', att.check_out_time))

        for event_code, event_time in events:
            total_events += 1
            ok = erp_attendance.create_attendance_record(
                employee_id=user.employee_id,
                attendance_time=event_time,
                attendance_code=event_code,
                auth=getattr(g, 'erp_auth', None),
            )
            if ok:
                pushed += 1
            else:
                row_key = f"{user.employee_id}:{event_code}"
                failed_ids.append(row_key)
                failed_details.append({
                    'employee_id': user.employee_id,
                    'attendance_type': event_code,
                    'attendance_time': event_time.strftime('%Y-%m-%d %H:%M:%S'),
                    'error': erp_attendance.last_error_message or 'ERP tu choi ghi cham cong',
                })

    failed = len(failed_ids)
    total = total_events
    message = f'Đã đẩy {pushed}/{total} bản ghi lên ERP.'
    if failed:
        message += f' Có {failed} bản ghi thất bại.'

    return jsonify({
        'success': failed == 0,
        'message': message,
        'result': {
            'start_date': normalized_filters['start_date'].strftime('%Y-%m-%d'),
            'end_date': normalized_filters['end_date'].strftime('%Y-%m-%d'),
            'total': total,
            'pushed': pushed,
            'failed': failed,
            'failed_employee_ids': failed_ids,
            'failed_details': failed_details,
            'records': summary.get('total_records', 0),
        },
    })

    if resolve_request_auth_mode(default='internal') != 'system':
        return jsonify({
            'success': False,
            'message': 'Đang ở chế độ nội bộ. Vui lòng đăng nhập hệ thống để đẩy dữ liệu.',
        }), 403

    payload = request.get_json(silent=True) or {}
    date_str = str(payload.get('date') or date.today().strftime('%Y-%m-%d')).strip()
    try:
        report_date = datetime.strptime(date_str, '%Y-%m-%d').date()
    except ValueError:
        return jsonify({'success': False, 'message': 'Định dạng ngày không hợp lệ (YYYY-MM-DD)'}), 400

    records = (
        db.session.query(Attendance, User)
        .join(User, Attendance.user_id == User.id)
        .filter(Attendance.date == report_date)
        .all()
    )

    if not records:
        return jsonify({
            'success': True,
            'message': 'Không có dữ liệu điểm danh để đẩy lên ERP',
            'result': {
                'date': report_date.strftime('%Y-%m-%d'),
                'total': 0,
                'pushed': 0,
                'failed': 0,
                'failed_employee_ids': [],
            },
        })

    pushed = 0
    failed_ids = []
    total_events = 0
    for att, user in records:
        events = []
        checkin_time = att.check_in_time or datetime.combine(report_date, datetime.min.time())
        events.append(('IN', checkin_time))
        if att.check_out_time:
            events.append(('OUT', att.check_out_time))

        for event_code, event_time in events:
            total_events += 1
            ok = erp_attendance.create_attendance_record(
                employee_id=user.employee_id,
                attendance_time=event_time,
                attendance_code=event_code,
                auth=getattr(g, 'erp_auth', None),
            )
            if ok:
                pushed += 1
            else:
                failed_ids.append(f"{user.employee_id}:{event_code}")

    failed = len(failed_ids)
    total = total_events

    message = f'Đã đẩy {pushed}/{total} bản ghi lên ERP.'
    if failed:
        message += f' Có {failed} bản ghi thất bại.'

    return jsonify({
        'success': failed == 0,
        'message': message,
        'result': {
            'date': report_date.strftime('%Y-%m-%d'),
            'total': total,
            'pushed': pushed,
            'failed': failed,
            'failed_employee_ids': failed_ids,
        },
    })


@attendance_bp.route('/api/report/online_attendance')
@admin_required
def api_report_online_attendance():
    if resolve_request_auth_mode(default='internal') != 'system':
        return jsonify({
            'success': False,
            'message': 'Đang ở chế độ nội bộ. Vui lòng đăng nhập hệ thống để kiểm tra dữ liệu online.',
        }), 403

    filters = request.args.to_dict(flat=True)
    try:
        result = erp_attendance.list_online_attendance(
            filters=filters,
            auth=getattr(g, 'erp_auth', None),
        )
        records = result.get('records') or []
        meta = result.get('meta') or {}
        resolved_filters = result.get('filters') or {}

        if erp_attendance.last_error_message and not records:
            return jsonify({
                'success': False,
                'message': erp_attendance.last_error_message,
                'records': [],
                'meta': meta,
                'filters': resolved_filters,
            }), 502

        return jsonify({
            'success': True,
            'message': 'Đã tải dữ liệu chấm công online',
            'records': records,
            'meta': meta,
            'filters': resolved_filters,
        })
    except Exception as exc:
        status_code = int(getattr(exc, 'status_code', 0) or 500)
        message = str(getattr(exc, 'message', '') or str(exc) or 'Khong the tai du lieu cham cong online')
        return jsonify({
            'success': False,
            'message': message,
            'records': [],
            'meta': {},
            'filters': filters,
        }), status_code
