# -*- coding: utf-8 -*-
"""Face Recognition System - Flask API Backend (JSON only, no templates)."""

import os
os.environ.setdefault('KMP_DUPLICATE_LIB_OK', 'TRUE')
os.environ.setdefault('OMP_NUM_THREADS', '1')
os.environ.setdefault('MKL_NUM_THREADS', '1')

from flask import Flask, request, jsonify, Response, session, g, send_from_directory
from flask_cors import CORS
import secrets
import sys
import cv2
import numpy as np
from datetime import datetime, date, timedelta
import threading
import time
import base64
import io
import json
import mysql.connector
from functools import wraps
from PIL import Image as PILImage

from backend.config import (
    ERP_MAIN_CONFIG, ERP_DOCS_CONFIG,
    EMPLOYEE_TABLE, EMPLOYEE_COLUMNS,
    IMAGE_TABLE, IMAGE_COLUMNS,
    CAMERA_CONFIG
)
from backend.models.database import db, User, Attendance
from backend.models.face_recognition_module import FaceRecognition
from backend.models.erp_integration import erp_attendance
from backend.services.attendance_service import AttendanceService
from backend.services.import_employees import ERPImporter


def create_app():
    app = Flask(__name__)
    CORS(app, supports_credentials=True)
    app.config['SECRET_KEY'] = os.environ.get('SECRET_KEY', 'face-recognition-secret-key')
    app.config['SQLALCHEMY_DATABASE_URI'] = 'sqlite:///attendance.db'
    app.config['SQLALCHEMY_TRACK_MODIFICATIONS'] = False
    app.config['UPLOAD_FOLDER'] = 'static/faces'

    db.init_app(app)

    face_recognizer = FaceRecognition(det_thresh=0.5)
    face_recognition_lock = threading.Lock()
    attendance_service_ref = [None]  # mutable container

    os.makedirs(app.config['UPLOAD_FOLDER'], exist_ok=True)

    ADMIN_PASSWORD = os.environ.get('ADMIN_PASSWORD', 'admin123')
    admin_tokens = set()  # In-memory admin tokens

    # --- Helper functions ---

    def load_known_faces():
        users = User.query.all()
        face_recognizer.load_known_faces(users)
        if attendance_service_ref[0]:
            attendance_service_ref[0].update_known_faces(face_recognizer)

    def perform_auto_attendance(user_id, current_encoding=None, min_dist=1.0):
        try:
            today = date.today()
            current_time = datetime.now()
            user = User.query.get(user_id)
            if not user:
                return

            # Adaptive learning
            if current_encoding is not None and min_dist < 0.35:
                try:
                    with face_recognition_lock:
                        should_learn = True
                        current_encodings = []
                        if isinstance(user.face_encoding, list):
                            current_encodings = user.face_encoding
                        else:
                            current_encodings = [user.face_encoding]

                        for known_enc in current_encodings:
                            dist = face_recognizer.compute_distance([known_enc], current_encoding)[0]
                            if dist < 0.1:
                                should_learn = False
                                break

                        if should_learn:
                            current_encodings.append(current_encoding)
                            if len(current_encodings) > 5:
                                current_encodings.pop(0)
                            user.face_encoding = current_encodings
                            db.session.commit()
                            load_known_faces()
                except Exception as e:
                    print(f"Adaptive learning error: {e}")

            last_attendance = Attendance.query.filter_by(
                user_id=user_id, date=today
            ).order_by(Attendance.check_in_time.desc()).first()
            if last_attendance:
                last_time = last_attendance.check_in_time
                if (current_time - last_time).total_seconds() < 600:
                    return

            if erp_attendance.check_recent_attendance(user.employee_id, minutes=10):
                return

            status = 'present'
            if current_time.hour > 8 or (current_time.hour == 8 and current_time.minute > 30):
                status = 'late'

            new_attendance = Attendance(
                user_id=user_id,
                check_in_time=current_time,
                date=today,
                status=status
            )
            db.session.add(new_attendance)
            db.session.commit()

            erp_attendance.create_attendance_record(
                employee_id=user.employee_id,
                attendance_time=current_time
            )
        except Exception as e:
            print(f"Auto attendance error: {e}")

    def admin_required(f):
        @wraps(f)
        def wrapper(*args, **kwargs):
            token = request.headers.get('X-Admin-Token')
            if not (token and token in admin_tokens) and not session.get('is_admin'):
                return jsonify({'success': False, 'message': 'Unauthorized'}), 401
            return f(*args, **kwargs)
        return wrapper

    # --- Initialize ---

    with app.app_context():
        db.create_all()
        load_known_faces()
        attendance_service_ref[0] = AttendanceService(
            app, face_recognizer, perform_auto_attendance
        )

    # --- Video streaming ---

    def generate_frames():
        while True:
            svc = attendance_service_ref[0]
            if not svc:
                time.sleep(1)
                continue
            frame = svc.get_processed_frame()
            if frame is None:
                time.sleep(0.1)
                continue
            ret, buffer = cv2.imencode('.jpg', frame, [int(cv2.IMWRITE_JPEG_QUALITY), 80])
            frame_bytes = buffer.tobytes()
            yield (b'--frame\r\n'
                   b'Content-Type: image/jpeg\r\n\r\n' + frame_bytes + b'\r\n')

    # ======================== ROUTES ========================

    @app.route('/api/health')
    def health():
        return jsonify({'status': 'ok', 'timestamp': datetime.now().isoformat()})

    @app.route('/video_feed')
    def video_feed():
        return Response(generate_frames(),
                        mimetype='multipart/x-mixed-replace; boundary=frame')

    # --- Auth ---

    @app.route('/api/admin_login', methods=['POST'])
    def admin_login():
        data = request.get_json() or {}
        if data.get('password') == ADMIN_PASSWORD:
            token = secrets.token_hex(32)
            admin_tokens.add(token)
            session['is_admin'] = True
            return jsonify({'success': True, 'token': token})
        return jsonify({'success': False, 'message': 'Sai mật khẩu'}), 401

    @app.route('/api/admin_logout', methods=['POST'])
    def admin_logout():
        token = request.headers.get('X-Admin-Token')
        if token:
            admin_tokens.discard(token)
        session.pop('is_admin', None)
        return jsonify({'success': True})

    @app.route('/api/auth_status')
    def auth_status():
        token = request.headers.get('X-Admin-Token')
        is_admin = (token and token in admin_tokens) or bool(session.get('is_admin'))
        return jsonify({'is_admin': is_admin})

    # --- Registration ---

    @app.route('/api/register', methods=['POST'])
    def api_register():
        try:
            name = request.form.get('name')
            employee_id = request.form.get('employee_id')
            department = request.form.get('department')
            position = request.form.get('position')

            existing_user = User.query.filter_by(employee_id=employee_id).first()
            if existing_user:
                return jsonify({'success': False, 'message': 'Mã nhân viên đã tồn tại'})

            if 'image' not in request.files:
                return jsonify({'success': False, 'message': 'Không tìm thấy ảnh'})

            image = request.files['image']
            with face_recognition_lock:
                face_encoding, error = face_recognizer.encode_face_from_image(image)

            if error:
                return jsonify({'success': False, 'message': error})

            new_user = User(
                name=name, employee_id=employee_id,
                department=department, position=position,
                face_encoding=face_encoding
            )
            db.session.add(new_user)
            db.session.commit()
            load_known_faces()
            return jsonify({'success': True, 'message': 'Đăng ký thành công'})
        except Exception as e:
            return jsonify({'success': False, 'message': str(e)})

    @app.route('/api/register_base64', methods=['POST'])
    def api_register_base64():
        try:
            data = request.get_json()
            if not data:
                return jsonify({'success': False, 'message': 'Không có dữ liệu JSON'})

            name = data.get('name')
            employee_id = data.get('employee_id')
            department = data.get('department')
            position = data.get('position')
            image_base64 = data.get('image_base64')

            if not all([name, employee_id, image_base64]):
                return jsonify({'success': False, 'message': 'Vui lòng nhập đủ thông tin bắt buộc'})

            existing_user = User.query.filter_by(employee_id=employee_id).first()
            if existing_user:
                return jsonify({'success': False, 'message': 'Mã nhân viên đã tồn tại'})

            with face_recognition_lock:
                face_encoding, error = face_recognizer.encode_face_from_base64(image_base64)

            if error:
                return jsonify({'success': False, 'message': error})

            new_user = User(
                name=name, employee_id=employee_id,
                department=department, position=position,
                face_encoding=face_encoding
            )
            db.session.add(new_user)
            db.session.commit()
            load_known_faces()
            return jsonify({'success': True, 'message': 'Đăng ký thành công'})
        except Exception as e:
            return jsonify({'success': False, 'message': str(e)})

    @app.route('/api/register_from_erp', methods=['POST'])
    def register_from_erp():
        employee_id = (request.args.get('employee_id')
                      or (request.json.get('employee_id') if request.is_json else None)
                      or request.form.get('employee_id'))
        if not employee_id:
            return jsonify({'success': False, 'message': 'Thiếu mã nhân viên'}), 400

        existing_user = User.query.filter_by(employee_id=employee_id).first()
        if existing_user:
            return jsonify({'success': False, 'message': 'Nhân viên đã tồn tại trong hệ thống'}), 409

        try:
            conn = mysql.connector.connect(**ERP_MAIN_CONFIG)
            cursor = conn.cursor(dictionary=True)
            query = f"""
                SELECT {EMPLOYEE_COLUMNS['employee_id']} as employee_id,
                       {EMPLOYEE_COLUMNS['name']} as name,
                       {EMPLOYEE_COLUMNS['department']} as department,
                       {EMPLOYEE_COLUMNS['position']} as position
                FROM {EMPLOYEE_TABLE}
                WHERE {EMPLOYEE_COLUMNS['employee_id']} = %s
                LIMIT 1
            """
            cursor.execute(query, (employee_id,))
            emp = cursor.fetchone()
            cursor.close()
            conn.close()
            if not emp or not isinstance(emp, dict):
                return jsonify({'success': False, 'message': 'Không tìm thấy nhân viên trong ERP'}), 404
        except Exception as e:
            return jsonify({'success': False, 'message': f'Lỗi truy vấn ERP: {str(e)}'}), 500

        try:
            conn = mysql.connector.connect(**ERP_DOCS_CONFIG)
            cursor = conn.cursor()
            query = f"""
                SELECT {IMAGE_COLUMNS['image_blob']}
                FROM {IMAGE_TABLE}
                WHERE {IMAGE_COLUMNS['employee_id']} = %s AND {IMAGE_COLUMNS['image_blob']} IS NOT NULL
                LIMIT 1
            """
            cursor.execute(query, (employee_id,))
            result = cursor.fetchone()
            cursor.close()
            conn.close()
            if not result or not isinstance(result[0], (bytes, bytearray)):
                return jsonify({'success': False, 'message': 'Không tìm thấy ảnh nhân viên trong ERP'}), 404
            image_blob = result[0]
        except Exception as e:
            return jsonify({'success': False, 'message': f'Lỗi truy vấn ảnh ERP: {str(e)}'}), 500

        image_file = io.BytesIO(image_blob)
        with face_recognition_lock:
            face_encoding, error = face_recognizer.encode_face_from_image(image_file)

        if error:
            return jsonify({'success': False, 'message': f'Lỗi nhận diện khuôn mặt: {error}'}), 400

        try:
            new_user = User(
                name=emp.get('name'), employee_id=emp.get('employee_id'),
                department=emp.get('department'), position=emp.get('position'),
                face_encoding=face_encoding
            )
            db.session.add(new_user)
            db.session.commit()
            load_known_faces()
            return jsonify({'success': True, 'message': 'Đăng ký thành công', 'employee': emp})
        except Exception as e:
            db.session.rollback()
            return jsonify({'success': False, 'message': f'Lỗi lưu: {str(e)}'}), 500

    # --- Camera ---

    @app.route('/api/start_camera', methods=['POST'])
    def start_camera():
        svc = attendance_service_ref[0]
        if svc:
            data = request.get_json(silent=True) or {}
            camera_type = data.get('camera_type', 'rtsp')
            device_index = data.get('device_index', 0)
            rtsp_url = data.get('rtsp_url')

            if camera_type not in ['rtsp', 'device']:
                return jsonify({'success': False, 'message': 'camera_type không hợp lệ'})

            started = svc.start(
                camera_type=camera_type,
                device_index=device_index,
                rtsp_url=rtsp_url
            )
            if started:
                return jsonify({'success': True, 'message': 'Đã bật camera'})
            return jsonify({'success': False, 'message': 'Không mở được camera'})
        return jsonify({'success': False, 'message': 'Service not initialized'})

    @app.route('/api/stop_camera', methods=['POST'])
    def stop_camera():
        svc = attendance_service_ref[0]
        if svc:
            svc.stop()
            return jsonify({'success': True, 'message': 'Đã tắt camera'})
        return jsonify({'success': False, 'message': 'Service not initialized'})

    @app.route('/api/camera_status')
    def camera_status():
        svc = attendance_service_ref[0]
        running = svc.is_running if svc else False
        return jsonify({
            'success': True,
            'running': running,
            'default_camera_type': CAMERA_CONFIG.get('default_camera_type', 'rtsp'),
            'default_device_index': CAMERA_CONFIG.get('device_index', 0)
        })

    # --- Attendance ---

    @app.route('/api/check_attendance', methods=['POST'])
    def check_attendance():
        try:
            user_id = request.json.get('user_id')
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

            erp_success = erp_attendance.create_attendance_record(
                employee_id=user.employee_id,
                attendance_time=current_time
            )

            msg = f'Điểm danh thành công cho {user.name}'
            if not erp_success:
                msg += ' (lỗi ghi ERP)'
            return jsonify({'success': True, 'message': msg})
        except Exception as e:
            return jsonify({'success': False, 'message': str(e)})

    @app.route('/api/attendance_image', methods=['POST'])
    def attendance_image():
        try:
            attendance_code = None
            if 'image' in request.files:
                image_file = request.files['image']
                attendance_code = request.form.get('attendance_code')
                with face_recognition_lock:
                    face_encoding, error = face_recognizer.encode_face_from_image(image_file)
            else:
                data = request.get_json()
                if not data or 'image_base64' not in data:
                    return jsonify({'success': False, 'message': 'Thiếu ảnh'}), 400
                image_base64 = data['image_base64']
                attendance_code = data.get('attendance_code')
                with face_recognition_lock:
                    face_encoding, error = face_recognizer.encode_face_from_base64(image_base64)

            if error:
                return jsonify({'success': False, 'message': error}), 400

            with face_recognition_lock:
                matches = face_recognizer.compare_faces(
                    face_recognizer.known_face_encodings, face_encoding, tolerance=0.5
                )
                face_distances = face_recognizer.compute_distance(
                    face_recognizer.known_face_encodings, face_encoding
                )

            if len(face_distances) == 0 or not any(matches):
                return jsonify({'success': False, 'message': 'Không nhận diện được nhân viên'}), 404

            best_match_index = int(np.argmin(face_distances))
            if not matches[best_match_index]:
                return jsonify({'success': False, 'message': 'Không nhận diện được nhân viên'}), 404

            user_id = face_recognizer.known_face_ids[best_match_index]
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

            results = face_recognizer.engine.detect_and_encode(image)
            if len(results) == 0:
                return jsonify({'success': False, 'message': 'Không tìm thấy khuôn mặt'}), 400

            res = results[0]
            bbox = res['bbox']
            bbox_xywh = [bbox[0], bbox[1], bbox[2] - bbox[0], bbox[3] - bbox[1]]
            is_real, spoof_score = face_recognizer.engine.check_anti_spoofing(image, bbox_xywh)
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
                'status': status
            })
        except Exception as e:
            return jsonify({'success': False, 'message': str(e)}), 500

    # --- Stats ---

    @app.route('/api/get_attendance_stats')
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

    @app.route('/api/get_recent_activity')
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

    @app.route('/api/get_today_attendance')
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
                data.append({
                    'name': user.name,
                    'employee_id': user.employee_id,
                    'department': user.department,
                    'time': att.check_in_time.strftime('%H:%M:%S') if att.check_in_time else '',
                    'status': 'Đúng giờ' if att.status == 'present' else ('Trễ' if att.status == 'late' else att.status)
                })
            return jsonify({'success': True, 'data': data})
        except Exception as e:
            return jsonify({'success': False, 'message': str(e), 'data': []})

    @app.route('/api/employees')
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

    @app.route('/api/erp_employee_info')
    def get_erp_employee_info():
        employee_id = request.args.get('employee_id')
        if not employee_id:
            return jsonify({'success': False, 'message': 'Thiếu mã nhân viên'}), 400
        try:
            conn = mysql.connector.connect(**ERP_MAIN_CONFIG)
            cursor = conn.cursor(dictionary=True)
            query = f"""
                SELECT {EMPLOYEE_COLUMNS['employee_id']} as employee_id,
                       {EMPLOYEE_COLUMNS['name']} as name,
                       {EMPLOYEE_COLUMNS['department']} as department,
                       {EMPLOYEE_COLUMNS['position']} as position
                FROM {EMPLOYEE_TABLE}
                WHERE {EMPLOYEE_COLUMNS['employee_id']} = %s
                LIMIT 1
            """
            cursor.execute(query, (employee_id,))
            emp = cursor.fetchone()
            cursor.close()
            conn.close()
            if not emp:
                return jsonify({'success': False, 'message': 'Không tìm thấy nhân viên'}), 404
            emp['image_base64'] = None
        except Exception as e:
            return jsonify({'success': False, 'message': f'Lỗi truy vấn: {str(e)}'}), 500

        try:
            conn = mysql.connector.connect(**ERP_DOCS_CONFIG)
            cursor = conn.cursor()
            query = f"""
                SELECT {IMAGE_COLUMNS['image_blob']}
                FROM {IMAGE_TABLE}
                WHERE {IMAGE_COLUMNS['employee_id']} = %s AND {IMAGE_COLUMNS['image_blob']} IS NOT NULL
                LIMIT 1
            """
            cursor.execute(query, (employee_id,))
            result = cursor.fetchone()
            cursor.close()
            conn.close()
            if result and isinstance(result[0], (bytes, bytearray)):
                image_base64 = base64.b64encode(result[0]).decode('utf-8')
                emp['image_base64'] = f"data:image/jpeg;base64,{image_base64}"
        except Exception:
            pass
        return jsonify({'success': True, 'employee': emp})

    # --- Report ---

    @app.route('/api/report')
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

    # --- Admin Employee Management ---

    @app.route('/api/admin/employees')
    @admin_required
    def get_admin_employees():
        try:
            users = User.query.all()
            employees = []
            for u in users:
                has_face = False
                face_count = 0
                if u.face_encoding is not None:
                    if isinstance(u.face_encoding, list):
                        face_count = len(u.face_encoding)
                        has_face = face_count > 0
                    else:
                        face_count = 1
                        has_face = True

                employees.append({
                    'id': u.id,
                    'name': u.name,
                    'employee_id': u.employee_id,
                    'department': u.department or '',
                    'position': u.position or '',
                    'has_face': has_face,
                    'face_count': face_count,
                    'created_at': u.created_at.strftime('%Y-%m-%d %H:%M') if u.created_at else ''
                })
            return jsonify({'success': True, 'employees': employees})
        except Exception as e:
            return jsonify({'success': False, 'message': str(e), 'employees': []}), 500

    @app.route('/api/admin/update_face', methods=['POST'])
    @admin_required
    def admin_update_face():
        try:
            if request.is_json:
                data = request.get_json()
                user_id = data.get('user_id')
                image_base64 = data.get('image_base64')
            else:
                user_id = request.form.get('user_id')
                image_base64 = None

            if not user_id:
                return jsonify({'success': False, 'message': 'Thiếu user_id'}), 400

            user = User.query.get(user_id)
            if not user:
                return jsonify({'success': False, 'message': 'Không tìm thấy nhân viên'}), 404

            if 'image' in request.files:
                with face_recognition_lock:
                    face_encoding, error = face_recognizer.encode_face_from_image(request.files['image'])
            elif image_base64:
                with face_recognition_lock:
                    face_encoding, error = face_recognizer.encode_face_from_base64(image_base64)
            else:
                return jsonify({'success': False, 'message': 'Thiếu ảnh'}), 400

            if error:
                return jsonify({'success': False, 'message': error}), 400

            replace_all = (request.form.get('replace_all', 'false') == 'true'
                          if not request.is_json else data.get('replace_all', False))

            if replace_all:
                user.face_encoding = [face_encoding.tolist() if hasattr(face_encoding, 'tolist') else face_encoding]
            else:
                current_encodings = []
                if user.face_encoding:
                    if isinstance(user.face_encoding, list):
                        current_encodings = user.face_encoding
                    else:
                        current_encodings = [user.face_encoding]

                current_encodings.append(
                    face_encoding.tolist() if hasattr(face_encoding, 'tolist') else face_encoding
                )
                if len(current_encodings) > 5:
                    current_encodings = current_encodings[-5:]
                user.face_encoding = current_encodings

            db.session.commit()
            load_known_faces()

            return jsonify({
                'success': True,
                'message': f'Đã cập nhật khuôn mặt cho {user.name}',
                'face_count': len(user.face_encoding) if isinstance(user.face_encoding, list) else 1
            })
        except Exception as e:
            return jsonify({'success': False, 'message': str(e)}), 500

    @app.route('/api/admin/delete_employee/<int:user_id>', methods=['DELETE'])
    @admin_required
    def admin_delete_employee(user_id):
        try:
            user = User.query.get(user_id)
            if not user:
                return jsonify({'success': False, 'message': 'Không tìm thấy nhân viên'}), 404

            Attendance.query.filter_by(user_id=user_id).delete()
            db.session.delete(user)
            db.session.commit()
            load_known_faces()
            return jsonify({'success': True, 'message': f'Đã xóa nhân viên {user.name}'})
        except Exception as e:
            db.session.rollback()
            return jsonify({'success': False, 'message': str(e)}), 500

    @app.route('/api/admin/clear_face/<int:user_id>', methods=['POST'])
    @admin_required
    def admin_clear_face(user_id):
        try:
            user = User.query.get(user_id)
            if not user:
                return jsonify({'success': False, 'message': 'Không tìm thấy nhân viên'}), 404

            user.face_encoding = []
            db.session.commit()
            load_known_faces()
            return jsonify({'success': True, 'message': f'Đã xóa dữ liệu khuôn mặt của {user.name}'})
        except Exception as e:
            return jsonify({'success': False, 'message': str(e)}), 500

    @app.route('/api/admin/reload_from_erp', methods=['POST'])
    @admin_required
    def reload_from_erp():
        try:
            data = request.get_json()
            employee_id = data.get('employee_id') if data else None
            if not employee_id:
                return jsonify({'success': False, 'message': 'Thiếu mã nhân viên'}), 400

            user = User.query.filter_by(employee_id=employee_id).first()
            if not user:
                return jsonify({'success': False, 'message': 'Không tìm thấy nhân viên'}), 404

            conn = mysql.connector.connect(**ERP_DOCS_CONFIG)
            cursor = conn.cursor()
            query = f"""
                SELECT {IMAGE_COLUMNS['image_blob']}
                FROM {IMAGE_TABLE}
                WHERE {IMAGE_COLUMNS['employee_id']} = %s AND {IMAGE_COLUMNS['image_blob']} IS NOT NULL
                LIMIT 1
            """
            cursor.execute(query, (employee_id,))
            result = cursor.fetchone()
            cursor.close()
            conn.close()

            if not result or not isinstance(result[0], (bytes, bytearray)):
                return jsonify({'success': False, 'message': 'Không tìm thấy ảnh trong ERP'}), 404

            image_file = io.BytesIO(result[0])
            with face_recognition_lock:
                face_encoding, error = face_recognizer.encode_face_from_image(image_file)
                if error:
                    return jsonify({'success': False, 'message': f'Lỗi nhận diện: {error}'}), 400

                user.face_encoding = face_encoding
                db.session.commit()
                load_known_faces()

            return jsonify({'success': True, 'message': f'Đã tải lại dữ liệu từ ERP cho {user.name}'})
        except Exception as e:
            return jsonify({'success': False, 'message': str(e)}), 500

    # --- Serve React frontend (production) ---
    # Determine dist folder location
    if getattr(sys, 'frozen', False):
        _base = sys._MEIPASS
    else:
        _base = os.path.dirname(os.path.dirname(__file__))
    _dist_dir = os.path.join(_base, 'frontend_dist')
    print(f'[FaceCheck] frozen={getattr(sys, "frozen", False)}, _base={_base}, _dist_dir={_dist_dir}, exists={os.path.isdir(_dist_dir)}')

    @app.route('/api/debug_paths')
    def debug_paths():
        return jsonify({
            'frozen': getattr(sys, 'frozen', False),
            '_base': _base,
            '_dist_dir': _dist_dir,
            'dist_exists': os.path.isdir(_dist_dir),
            'cwd': os.getcwd(),
            'exe': sys.executable,
        })

    if os.path.isdir(_dist_dir):
        @app.route('/')
        def serve_index():
            return send_from_directory(_dist_dir, 'index.html')

        @app.route('/assets/<path:filename>')
        def serve_assets(filename):
            return send_from_directory(os.path.join(_dist_dir, 'assets'), filename)

        @app.errorhandler(404)
        def fallback(e):
            return send_from_directory(_dist_dir, 'index.html')

    return app


if __name__ == '__main__':
    app = create_app()
    app.run(host='0.0.0.0', port=5000, debug=False)
