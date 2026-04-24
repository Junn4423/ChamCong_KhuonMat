# -*- coding: utf-8 -*-
"""Auth routes — login, logout, session status.  (maps to frontend modules/auth.js)"""

from flask import Blueprint, request, jsonify, g

from backend.services.erp_http_client import ERPServiceError, erp_http_client
from backend.services.employee_account_service import employee_account_service
from backend.routes._state import state
from backend.routes._helpers import (
    resolve_request_auth,
    issue_session,
    issue_internal_session,
    issue_employee_session,
)

auth_bp = Blueprint('auth', __name__)


def _resolve_requested_device_type(payload):
    if not isinstance(payload, dict):
        payload = {}

    for raw_value in (
        payload.get('deviceType'),
        payload.get('device_type'),
        request.headers.get('X-Device-Type'),
        request.headers.get('X-DEVICE-TYPE'),
    ):
        value = str(raw_value or '').strip()
        if value:
            return value
    return ''


def _login_erp_account(username, password, client_ip='', device_type=''):
    auth_info = erp_http_client.login(
        username,
        password,
        client_ip=client_ip,
        device_type=device_type,
    )
    if isinstance(auth_info, dict):
        auth_info = {**auth_info, 'auth_mode': 'system'}
    token, user_payload = issue_session(auth_info)
    return {
        'success': True,
        'token': token,
        'user': user_payload,
    }


def _build_auth_status_payload(token, auth_obj, user_obj):
    auth_mode = None
    if isinstance(user_obj, dict):
        auth_mode = str(user_obj.get('auth_mode') or '').strip().lower() or None
    elif auth_obj:
        auth_mode = 'system'

    authenticated = bool(user_obj) or bool(auth_obj)
    is_employee = auth_mode == 'employee'
    is_admin = bool(auth_obj)

    if not is_admin and token and token in state.admin_tokens:
        is_admin = auth_mode in {'internal', 'system'}

    return {
        'authenticated': authenticated,
        'is_admin': bool(is_admin),
        'is_employee': bool(is_employee),
        'auth_mode': auth_mode,
        'user': user_obj if authenticated else None,
    }


@auth_bp.route('/api/session/login', methods=['POST'])
def session_login():
    data = request.get_json(silent=True) or {}
    username = (data.get('username') or '').strip()
    password = data.get('password') or ''
    device_type = _resolve_requested_device_type(data)
    if not username or not password:
        return jsonify({'success': False, 'message': 'Vui lòng nhập tài khoản và mật khẩu ERP'}), 400
    try:
        return jsonify(
            _login_erp_account(
                username,
                password,
                client_ip=request.remote_addr or '',
                device_type=device_type,
            )
        )
    except ERPServiceError as exc:
        return jsonify({'success': False, 'message': exc.message}), (exc.status_code or 401)
    except Exception as exc:
        return jsonify({'success': False, 'message': f'Lỗi đăng nhập ERP: {exc}'}), 500


@auth_bp.route('/api/session/logout', methods=['POST'])
def session_logout():
    token = request.headers.get('X-Session-Token') or request.headers.get('X-Admin-Token')
    payload = state.erp_sessions.get(token) if token else None
    auth_payload = payload.get('auth') if isinstance(payload, dict) else None
    if auth_payload:
        erp_http_client.logout(auth_payload, client_ip=request.remote_addr or '')
    if token:
        state.erp_sessions.pop(token, None)
        state.admin_tokens.discard(token)
    return jsonify({'success': True})


@auth_bp.route('/api/session/status')
def session_status():
    token, auth_obj, user_obj = resolve_request_auth()
    if token:
        g.session_token = token
    if auth_obj:
        g.erp_auth = auth_obj
    if user_obj:
        g.session_user = user_obj
    return jsonify(_build_auth_status_payload(token, auth_obj, user_obj))


@auth_bp.route('/api/admin_login', methods=['POST'])
def admin_login():
    data = request.get_json(silent=True) or {}
    username = (data.get('username') or '').strip()
    password = data.get('password') or ''
    mode = str(data.get('mode') or 'system').strip().lower()
    device_type = _resolve_requested_device_type(data)

    if mode not in {'system', 'internal'}:
        return jsonify({'success': False, 'message': 'Chế độ đăng nhập không hợp lệ'}), 400

    if mode == 'internal':
        internal_username = (state.INTERNAL_ADMIN_USERNAME or 'admin').strip()
        expected_username = internal_username or 'admin'
        provided_username = username or expected_username

        if provided_username != expected_username or password != state.INTERNAL_ADMIN_PASSWORD:
            return jsonify({'success': False, 'message': 'Sai thông tin đăng nhập nội bộ'}), 401

        token, user_payload = issue_internal_session(expected_username)
        state.admin_tokens.add(token)
        return jsonify({'success': True, 'token': token, 'user': user_payload})

    if not username or not password:
        return jsonify({'success': False, 'message': 'Vui lòng nhập tài khoản và mật khẩu hệ thống'}), 400

    try:
        response = _login_erp_account(
            username,
            password,
            client_ip=request.remote_addr or '',
            device_type=device_type,
        )
        state.admin_tokens.add(response['token'])
        return jsonify(response)
    except ERPServiceError as exc:
        return jsonify({'success': False, 'message': exc.message}), (exc.status_code or 401)
    except Exception as exc:
        return jsonify({'success': False, 'message': f'Lỗi đăng nhập hệ thống: {exc}'}), 500


@auth_bp.route('/api/admin_logout', methods=['POST'])
def admin_logout():
    token = request.headers.get('X-Admin-Token') or request.headers.get('X-Session-Token')
    payload = state.erp_sessions.get(token) if token else None
    auth_payload = payload.get('auth') if isinstance(payload, dict) else None
    if auth_payload:
        erp_http_client.logout(auth_payload, client_ip=request.remote_addr or '')
    if token:
        state.admin_tokens.discard(token)
        state.erp_sessions.pop(token, None)
    return jsonify({'success': True})


@auth_bp.route('/api/employee/login', methods=['POST'])
def employee_login():
    data = request.get_json(silent=True) or {}
    username = (data.get('username') or '').strip()
    password = data.get('password') or ''

    if not username or password is None or str(password) == '':
        return jsonify({'success': False, 'message': 'Vui lòng nhập tài khoản và mật khẩu nhân viên'}), 400

    try:
        account, user = employee_account_service.authenticate_employee(username, password)
        token, user_payload = issue_employee_session(user, account)
        return jsonify({'success': True, 'token': token, 'user': user_payload})
    except ERPServiceError as exc:
        return jsonify({'success': False, 'message': exc.message}), (exc.status_code or 401)
    except Exception as exc:
        return jsonify({'success': False, 'message': f'Lỗi đăng nhập nhân viên: {exc}'}), 500


@auth_bp.route('/api/employee/logout', methods=['POST'])
def employee_logout():
    token = request.headers.get('X-Session-Token') or request.headers.get('X-Admin-Token')
    if token:
        state.erp_sessions.pop(token, None)
        state.admin_tokens.discard(token)
    return jsonify({'success': True})


@auth_bp.route('/api/employee/status')
def employee_status():
    token, auth_obj, user_obj = resolve_request_auth()
    payload = _build_auth_status_payload(token, auth_obj, user_obj)
    payload['authenticated'] = bool(payload.get('authenticated') and payload.get('is_employee'))
    if not payload['authenticated']:
        payload['user'] = None
    return jsonify(payload)


@auth_bp.route('/api/auth_status')
def auth_status():
    token, auth_obj, user_obj = resolve_request_auth()
    return jsonify(_build_auth_status_payload(token, auth_obj, user_obj))
