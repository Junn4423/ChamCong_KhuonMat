# -*- coding: utf-8 -*-
"""Attendance routes — check-in, stats, report.  (maps to frontend modules/attendance.js)"""

import io
import os
import json
import base64
from datetime import datetime, date

import cv2
import numpy as np
from PIL import Image as PILImage
from flask import Blueprint, request, jsonify

from backend.models.database import db, User, Attendance
from backend.models.erp_integration import erp_attendance
from backend.face_encoding_utils import face_encoding_count
from backend.services.camera_registry import camera_registry
from backend.runtime import ensure_data_dir, ensure_db_path
from backend.routes._state import state
from backend.routes._helpers import (
    admin_required,
    parse_location_payload, location_to_text,
    get_runtime_location, save_attendance_location,
    serialize_attendance_location,
)

attendance_bp = Blueprint('attendance', __name__)


@attendance_bp.route('/api/check_attendance', methods=['POST'])
def check_attendance():
    try:
        data = request.get_json(silent=True) or {}
        user_id = data.get('user_id')
        request_location = parse_location_payload(data)
        if not user_id:
            return jsonify({'success': False, 'message': 'Không tìm thấy user_id'})

        user = User.query.get(user_id)
        if not user:
            return jsonify({'success': False, 'message': 'Không tìm thấy nhân viên'})

        today = date.today()
        current_time = datetime.now()

        last_attendance = Attendance.query.filter_by(
            user_id=user_id, date=today
        ).order_by(Attendance.check_in_time.desc()).first()
        if last_attendance:
            last_time = last_attendance.check_out_time or last_attendance.check_in_time
            if (current_time - last_time).total_seconds() < 600:
                return jsonify({'success': False, 'message': 'Chỉ được điểm danh 1 lần mỗi 10 phút'})

        if erp_attendance.check_recent_attendance(user.employee_id, minutes=10):
            return jsonify({'success': False, 'message': 'Đã chấm công trong ERP gần đây'})

        status = 'present'
        if current_time.hour > 8 or (current_time.hour == 8 and current_time.minute > 30):
            status = 'late'

        new_attendance = Attendance(
            user_id=user_id, check_in_time=current_time,
            date=today, status=status
        )
        db.session.add(new_attendance)
        db.session.commit()

        effective_location = request_location or get_runtime_location()
        if effective_location:
            try:
                save_attendance_location(
                    new_attendance.id,
                    effective_location,
                    source='request' if request_location else 'runtime',
                )
            except Exception as loc_exc:
                db.session.rollback()
                print(f"check_attendance location save error: {loc_exc}")

        erp_success = erp_attendance.create_attendance_record(
            employee_id=user.employee_id,
            attendance_time=current_time
        )

        msg = f'Điểm danh thành công cho {user.name}'
        if not erp_success:
            msg += ' (lỗi ghi ERP)'
        return jsonify({
            'success': True,
            'message': msg,
            'location': effective_location,
            'location_text': location_to_text(effective_location) if effective_location else '',
        })
    except Exception as e:
        return jsonify({'success': False, 'message': str(e)})


@attendance_bp.route('/api/attendance_image', methods=['POST'])
def attendance_image():
    try:
        attendance_code = None
        request_location = None
        if 'image' in request.files:
            image_file = request.files['image']
            attendance_code = request.form.get('attendance_code')
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
            request_location = parse_location_payload(data)
            with state.face_recognition_lock:
                face_encoding, error = state.face_recognizer.encode_face_from_base64(image_base64)

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
        bbox_xywh = [bbox[0], bbox[1], bbox[2] - bbox[0], bbox[3] - bbox[1]]
        is_real, spoof_score = state.face_recognizer.engine.check_anti_spoofing(image, bbox_xywh)
        if not is_real:
            return jsonify({'success': False, 'message': 'Phát hiện khuôn mặt giả mạo!'}), 400

        today_val = date.today()
        current_time = datetime.now()
        status = 'present'
        if current_time.hour > 8 or (current_time.hour == 8 and current_time.minute > 30):
            status = 'late'

        new_attendance = Attendance(
            user_id=user_id, check_in_time=current_time,
            date=today_val, status=status
        )
        db.session.add(new_attendance)
        db.session.commit()

        effective_location = request_location or get_runtime_location()
        if effective_location:
            try:
                save_attendance_location(
                    new_attendance.id,
                    effective_location,
                    source='request' if request_location else 'runtime',
                )
            except Exception as loc_exc:
                db.session.rollback()
                print(f"attendance_image location save error: {loc_exc}")

        erp_attendance.create_attendance_record(
            employee_id=user.employee_id,
            attendance_time=current_time,
            attendance_code=attendance_code
        )

        return jsonify({
            'success': True,
            'message': f'Điểm danh thành công cho {user.name}',
            'user': {
                'name': user.name, 'employee_id': user.employee_id,
                'department': user.department, 'position': user.position
            },
            'status': status,
            'location': effective_location,
            'location_text': location_to_text(effective_location) if effective_location else '',
        })
    except Exception as e:
        return jsonify({'success': False, 'message': str(e)}), 500


# --- Stats ---

@attendance_bp.route('/api/system_storage_stats')
@admin_required
def get_system_storage_stats():
    try:
        users = User.query.all()
        cameras = camera_registry.list_cameras()
        face_dir = state.app.config['UPLOAD_FOLDER']

        face_image_count = 0
        if os.path.isdir(face_dir):
            for filename in os.listdir(face_dir):
                if filename.lower().endswith(('.jpg', '.jpeg', '.png', '.webp')):
                    face_image_count += 1

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
            location_payload = serialize_attendance_location(att.id)
            data.append({
                'name': user.name,
                'employee_id': user.employee_id,
                'department': user.department,
                'time': att.check_in_time.strftime('%H:%M:%S') if att.check_in_time else '',
                'status': 'Đúng giờ' if att.status == 'present' else ('Trễ' if att.status == 'late' else att.status),
                'location': location_payload,
                'location_text': (location_payload or {}).get('text', ''),
            })
        return jsonify({'success': True, 'data': data})
    except Exception as e:
        return jsonify({'success': False, 'message': str(e), 'data': []})


@attendance_bp.route('/api/report')
@admin_required
def api_report():
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
        res.append({
            'name': user.name,
            'employee_id': user.employee_id,
            'department': user.department,
            'check_in_time': att.check_in_time.strftime('%H:%M:%S') if att.check_in_time else '',
            'check_out_time': att.check_out_time.strftime('%H:%M:%S') if att.check_out_time else '',
            'status': 'Đúng giờ' if att.status == 'present' else ('Trễ' if att.status == 'late' else att.status)
        })
    return jsonify({'success': True, 'records': res})
