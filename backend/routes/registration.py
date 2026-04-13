# -*- coding: utf-8 -*-
"""Registration routes — register faces.  (maps to frontend modules/registration.js)"""

import io
import base64

from flask import Blueprint, request, jsonify, g

from backend.models.database import db, User
from backend.face_encoding_utils import normalize_face_encodings
from backend.services.erp_http_client import ERPServiceError, erp_http_client
from backend.routes._state import state
from backend.routes._helpers import (
    save_local_employee_image, get_erp_employee_image_blob,
    session_required,
)

registration_bp = Blueprint('registration', __name__)


@registration_bp.route('/api/register', methods=['POST'])
def api_register():
    try:
        name = (request.form.get('name') or '').strip()
        employee_id = (request.form.get('employee_id') or '').strip()
        department = (request.form.get('department') or '').strip()
        position = (request.form.get('position') or '').strip()

        if not employee_id:
            return jsonify({'success': False, 'message': 'Thiếu mã nhân viên'}), 400

        existing_user = User.query.filter_by(employee_id=employee_id).first()
        if existing_user:
            return jsonify({'success': False, 'message': 'Mã nhân viên đã tồn tại'})

        if 'image' not in request.files:
            return jsonify({'success': False, 'message': 'Không tìm thấy ảnh'})

        image_file = request.files['image']
        image_bytes = image_file.read()
        if not image_bytes:
            return jsonify({'success': False, 'message': 'Ảnh rỗng'}), 400

        with state.face_recognition_lock:
            face_encoding, error = state.face_recognizer.encode_face_from_image(io.BytesIO(image_bytes))

        if error:
            return jsonify({'success': False, 'message': error})

        new_user = User(
            name=name or employee_id,
            employee_id=employee_id,
            department=department,
            position=position,
            face_encoding=normalize_face_encodings(face_encoding),
        )
        db.session.add(new_user)
        db.session.commit()
        save_local_employee_image(employee_id, image_bytes, '.jpg')
        state.load_known_faces()
        return jsonify({'success': True, 'message': 'Đăng ký thành công'})
    except Exception as exc:
        db.session.rollback()
        return jsonify({'success': False, 'message': str(exc)}), 500


@registration_bp.route('/api/register_base64', methods=['POST'])
def api_register_base64():
    try:
        data = request.get_json(silent=True) or {}
        name = (data.get('name') or '').strip()
        employee_id = (data.get('employee_id') or '').strip()
        department = (data.get('department') or '').strip()
        position = (data.get('position') or '').strip()
        image_base64 = data.get('image_base64')

        if not all([employee_id, image_base64]):
            return jsonify({'success': False, 'message': 'Thiếu thông tin bắt buộc'}), 400

        existing_user = User.query.filter_by(employee_id=employee_id).first()
        if existing_user:
            return jsonify({'success': False, 'message': 'Mã nhân viên đã tồn tại'})

        raw_base64 = image_base64.split(',', 1)[1] if ',' in image_base64 else image_base64
        try:
            image_bytes = base64.b64decode(raw_base64)
        except Exception:
            return jsonify({'success': False, 'message': 'Dữ liệu ảnh base64 không hợp lệ'}), 400

        with state.face_recognition_lock:
            face_encoding, error = state.face_recognizer.encode_face_from_image(io.BytesIO(image_bytes))

        if error:
            return jsonify({'success': False, 'message': error})

        new_user = User(
            name=name or employee_id,
            employee_id=employee_id,
            department=department,
            position=position,
            face_encoding=normalize_face_encodings(face_encoding),
        )
        db.session.add(new_user)
        db.session.commit()
        save_local_employee_image(employee_id, image_bytes, '.jpg')
        state.load_known_faces()
        return jsonify({'success': True, 'message': 'Đăng ký thành công'})
    except Exception as exc:
        db.session.rollback()
        return jsonify({'success': False, 'message': str(exc)}), 500


@registration_bp.route('/api/register_from_erp', methods=['POST'])
@session_required
def register_from_erp():
    employee_id = (
        request.args.get('employee_id')
        or ((request.get_json(silent=True) or {}).get('employee_id'))
        or request.form.get('employee_id')
    )
    employee_id = (employee_id or '').strip()
    if not employee_id:
        return jsonify({'success': False, 'message': 'Thiếu mã nhân viên'}), 400

    existing_user = User.query.filter_by(employee_id=employee_id).first()
    if existing_user:
        return jsonify({'success': False, 'message': 'Nhân viên đã tồn tại trong hệ thống'}), 409

    try:
        emp = erp_http_client.get_employee(g.erp_auth, employee_id)
    except ERPServiceError as exc:
        return jsonify({'success': False, 'message': exc.message}), (exc.status_code or 500)
    except Exception as exc:
        return jsonify({'success': False, 'message': f'Lỗi truy vấn ERP: {exc}'}), 500

    image_blob = get_erp_employee_image_blob(employee_id, employee=emp)
    if not image_blob:
        return jsonify({
            'success': False,
            'message': 'Nhân viên chưa có ảnh khuôn mặt trên ERP. Vui lòng tải ảnh lên trước khi đăng ký',
        }), 400

    face_encoding_value = []
    if image_blob:
        with state.face_recognition_lock:
            face_encoding, error = state.face_recognizer.encode_face_from_image(io.BytesIO(image_blob))
        if error:
            return jsonify({'success': False, 'message': f'Lỗi nhận diện khuôn mặt: {error}'}), 400
        face_encoding_value = normalize_face_encodings(face_encoding)

    try:
        new_user = User(
            name=(emp.get('name') or employee_id),
            employee_id=employee_id,
            department=(emp.get('department') or ''),
            position=(emp.get('position') or ''),
            face_encoding=face_encoding_value,
        )
        db.session.add(new_user)
        db.session.commit()
        if image_blob:
            save_local_employee_image(employee_id, image_blob, '.jpg')
        state.load_known_faces()

        from backend.face_encoding_utils import face_encoding_count
        employee_payload = {
            **emp,
            'registered': True,
            'has_face': bool(face_encoding_value),
            'status_text': 'Đã đăng ký khuôn mặt' if face_encoding_value else 'Đã đăng ký (chưa có khuôn mặt)',
        }
        return jsonify({'success': True, 'message': 'Đăng ký thành công', 'employee': employee_payload})
    except Exception as exc:
        db.session.rollback()
        return jsonify({'success': False, 'message': f'Lỗi lưu: {exc}'}), 500
