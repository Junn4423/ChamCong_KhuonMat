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

from backend.models.database import db, User, Attendance, EmployeeImage
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
    resolve_request_auth_mode,
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
        include_preview = False
        if 'image' in request.files:
            image_file = request.files['image']
            attendance_code = request.form.get('attendance_code')
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
            include_preview_raw = data.get('include_preview', False)
            if isinstance(include_preview_raw, str):
                include_preview = include_preview_raw.strip().lower() in {'1', 'true', 'yes', 'on'}
            else:
                include_preview = bool(include_preview_raw)
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
                'detection_bbox': detection_bbox,
                'face_count': len(results),
                'preview_image_base64': preview_image_base64,
            }), 400

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
            'detection_bbox': detection_bbox,
            'face_count': len(results),
            'preview_image_base64': preview_image_base64,
        })
    except Exception as e:
        return jsonify({'success': False, 'message': str(e)}), 500


@attendance_bp.route('/api/attendance_detect', methods=['POST'])
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


@attendance_bp.route('/api/report/push_to_erp', methods=['POST'])
@admin_required
def api_report_push_to_erp():
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
    for att, user in records:
        attendance_time = att.check_in_time or datetime.combine(report_date, datetime.min.time())
        ok = erp_attendance.create_attendance_record(
            employee_id=user.employee_id,
            attendance_time=attendance_time,
        )
        if ok:
            pushed += 1
        else:
            failed_ids.append(user.employee_id)

    failed = len(failed_ids)
    total = len(records)

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
