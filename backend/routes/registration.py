# -*- coding: utf-8 -*-
"""Registration routes for employee face enrollment."""

import base64
import io

from flask import Blueprint, g, jsonify, request

from backend.face_encoding_utils import normalize_face_encodings
from backend.models.database import User, db
from backend.routes._helpers import get_erp_employee_image_blob, save_local_employee_image, session_required
from backend.routes._state import state
from backend.services.erp_http_client import ERPServiceError, erp_http_client

registration_bp = Blueprint('registration', __name__)


def _resolve_erp_image_token(image_bytes, employee=None, username=None, auth=None):
    if isinstance(employee, dict):
        existing_token = str(employee.get('image_token') or '').strip()
        if existing_token:
            return existing_token

    token_payload = erp_http_client.create_sof_image_token(
        image_bytes,
        username=username,
        auth=auth,
    )
    return str(token_payload.get('token') or '').strip()


@registration_bp.route('/api/register', methods=['POST'])
def api_register():
    try:
        name = (request.form.get('name') or '').strip()
        employee_id = (request.form.get('employee_id') or '').strip()
        department = (request.form.get('department') or '').strip()
        position = (request.form.get('position') or '').strip()

        if not employee_id:
            return jsonify({'success': False, 'message': 'Thi?u m? nh?n vi?n'}), 400

        existing_user = User.query.filter_by(employee_id=employee_id).first()
        if existing_user:
            return jsonify({'success': False, 'message': 'M? nh?n vi?n ?? t?n t?i'})

        if 'image' not in request.files:
            return jsonify({'success': False, 'message': 'Kh?ng t?m th?y ?nh'})

        image_file = request.files['image']
        image_bytes = image_file.read()
        if not image_bytes:
            return jsonify({'success': False, 'message': '?nh r?ng'}), 400

        with state.face_recognition_lock:
            face_encoding, error = state.face_recognizer.encode_face_from_image(io.BytesIO(image_bytes))

        if error:
            return jsonify({'success': False, 'message': error}), 400

        erp_image_token = _resolve_erp_image_token(image_bytes)

        new_user = User(
            name=name or employee_id,
            employee_id=employee_id,
            department=department,
            position=position,
            face_encoding=normalize_face_encodings(face_encoding),
        )
        db.session.add(new_user)
        db.session.commit()
        save_local_employee_image(
            employee_id,
            image_bytes,
            '.jpg',
            source='register_upload',
            erp_image_token=erp_image_token,
        )
        state.load_known_faces()
        return jsonify({
            'success': True,
            'message': '??ng k? th?nh c?ng',
            'erp_image_token': erp_image_token,
        })
    except ERPServiceError as exc:
        db.session.rollback()
        return jsonify({'success': False, 'message': exc.message}), (exc.status_code or 500)
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
            return jsonify({'success': False, 'message': 'Thi?u th?ng tin b?t bu?c'}), 400

        existing_user = User.query.filter_by(employee_id=employee_id).first()
        if existing_user:
            return jsonify({'success': False, 'message': 'M? nh?n vi?n ?? t?n t?i'})

        raw_base64 = image_base64.split(',', 1)[1] if ',' in image_base64 else image_base64
        try:
            image_bytes = base64.b64decode(raw_base64)
        except Exception:
            return jsonify({'success': False, 'message': 'D? li?u ?nh base64 kh?ng h?p l?'}), 400

        with state.face_recognition_lock:
            face_encoding, error = state.face_recognizer.encode_face_from_image(io.BytesIO(image_bytes))

        if error:
            return jsonify({'success': False, 'message': error}), 400

        erp_image_token = _resolve_erp_image_token(image_bytes)

        new_user = User(
            name=name or employee_id,
            employee_id=employee_id,
            department=department,
            position=position,
            face_encoding=normalize_face_encodings(face_encoding),
        )
        db.session.add(new_user)
        db.session.commit()
        save_local_employee_image(
            employee_id,
            image_bytes,
            '.jpg',
            source='register_base64',
            erp_image_token=erp_image_token,
        )
        state.load_known_faces()
        return jsonify({
            'success': True,
            'message': '??ng k? th?nh c?ng',
            'erp_image_token': erp_image_token,
        })
    except ERPServiceError as exc:
        db.session.rollback()
        return jsonify({'success': False, 'message': exc.message}), (exc.status_code or 500)
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
        return jsonify({'success': False, 'message': 'Thi?u m? nh?n vi?n'}), 400

    existing_user = User.query.filter_by(employee_id=employee_id).first()
    if existing_user:
        return jsonify({'success': False, 'message': 'Nh?n vi?n ?? t?n t?i trong h? th?ng'}), 409

    try:
        emp = erp_http_client.get_employee(g.erp_auth, employee_id)
    except ERPServiceError as exc:
        return jsonify({'success': False, 'message': exc.message}), (exc.status_code or 500)
    except Exception as exc:
        return jsonify({'success': False, 'message': f'L?i truy v?n ERP: {exc}'}), 500

    image_blob = get_erp_employee_image_blob(employee_id, employee=emp)
    if not image_blob:
        return jsonify({
            'success': False,
            'message': 'Nh?n vi?n ch?a c? ?nh khu?n m?t tr?n ERP. Vui l?ng t?i ?nh l?n tr??c khi ??ng k?',
        }), 400

    with state.face_recognition_lock:
        face_encoding, error = state.face_recognizer.encode_face_from_image(io.BytesIO(image_blob))
    if error:
        return jsonify({'success': False, 'message': f'L?i nh?n di?n khu?n m?t: {error}'}), 400

    face_encoding_value = normalize_face_encodings(face_encoding)
    try:
        token_username = ''
        session_user = getattr(g, 'session_user', None)
        if isinstance(session_user, dict):
            token_username = (session_user.get('code') or '').strip()
        if not token_username and getattr(g, 'erp_auth', None):
            token_username = (g.erp_auth.get('code') or '').strip()

        erp_image_token = _resolve_erp_image_token(
            image_blob,
            employee=emp,
            username=token_username or None,
            auth=getattr(g, 'erp_auth', None),
        )
    except ERPServiceError as exc:
        return jsonify({'success': False, 'message': exc.message}), (exc.status_code or 500)

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
        save_local_employee_image(
            employee_id,
            image_blob,
            '.jpg',
            source='register_from_erp',
            erp_image_token=erp_image_token,
        )
        state.load_known_faces()

        employee_payload = {
            **emp,
            'registered': True,
            'has_face': bool(face_encoding_value),
            'status_text': '?? ??ng k? khu?n m?t' if face_encoding_value else '?? ??ng k? (ch?a c? khu?n m?t)',
            'image_token': erp_image_token,
        }
        return jsonify({
            'success': True,
            'message': '??ng k? th?nh c?ng',
            'employee': employee_payload,
            'erp_image_token': erp_image_token,
        })
    except Exception as exc:
        db.session.rollback()
        return jsonify({'success': False, 'message': f'L?i l?u: {exc}'}), 500
