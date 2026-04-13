# -*- coding: utf-8 -*-
"""Auth routes — login, logout, session status.  (maps to frontend modules/auth.js)"""

import secrets
from flask import Blueprint, request, jsonify, session, g

from backend.services.erp_http_client import ERPServiceError, erp_http_client
from backend.routes._state import state
from backend.routes._helpers import resolve_request_auth, issue_session

auth_bp = Blueprint('auth', __name__)


def _login_erp_account(username, password):
    auth_info = erp_http_client.login(username, password)
    token, user_payload = issue_session(auth_info)
    return {
        'success': True,
        'token': token,
        'user': user_payload,
    }


@auth_bp.route('/api/session/login', methods=['POST'])
def session_login():
    data = request.get_json(silent=True) or {}
    username = (data.get('username') or '').strip()
    password = data.get('password') or ''
    if not username or not password:
        return jsonify({'success': False, 'message': 'Vui lòng nhập tài khoản và mật khẩu ERP'}), 400
    try:
        return jsonify(_login_erp_account(username, password))
    except ERPServiceError as exc:
        return jsonify({'success': False, 'message': exc.message}), (exc.status_code or 401)
    except Exception as exc:
        return jsonify({'success': False, 'message': f'Lỗi đăng nhập ERP: {exc}'}), 500


@auth_bp.route('/api/session/logout', methods=['POST'])
def session_logout():
    token = request.headers.get('X-Session-Token') or request.headers.get('X-Admin-Token')
    if token:
        state.erp_sessions.pop(token, None)
        state.admin_tokens.discard(token)
    session.pop('is_admin', None)
    return jsonify({'success': True})


@auth_bp.route('/api/session/status')
def session_status():
    token, auth_obj, user_obj = resolve_request_auth()
    authenticated = bool(auth_obj)
    if authenticated:
        g.session_token = token
        g.erp_auth = auth_obj
        g.session_user = user_obj
    return jsonify({'authenticated': authenticated, 'user': user_obj if authenticated else None})


@auth_bp.route('/api/admin_login', methods=['POST'])
def admin_login():
    data = request.get_json(silent=True) or {}
    username = (data.get('username') or '').strip()
    password = data.get('password') or ''

    try:
        response = _login_erp_account(username, password)
        state.admin_tokens.add(response['token'])
        session['is_admin'] = True
        return jsonify(response)
    except ERPServiceError:
        pass
    except Exception:
        pass

    if password == state.ADMIN_PASSWORD and (not username or username == 'admin'):
        token = secrets.token_hex(32)
        state.admin_tokens.add(token)
        session['is_admin'] = True
        return jsonify({'success': True, 'token': token, 'user': {'name': 'Admin', 'code': 'admin'}})

    return jsonify({'success': False, 'message': 'Sai thông tin đăng nhập'}), 401


@auth_bp.route('/api/admin_logout', methods=['POST'])
def admin_logout():
    token = request.headers.get('X-Admin-Token') or request.headers.get('X-Session-Token')
    if token:
        state.admin_tokens.discard(token)
        state.erp_sessions.pop(token, None)
    session.pop('is_admin', None)
    return jsonify({'success': True})


@auth_bp.route('/api/auth_status')
def auth_status():
    token, auth_obj, user_obj = resolve_request_auth()
    is_admin = bool((token and token in state.admin_tokens) or session.get('is_admin') or auth_obj)
    return jsonify({'is_admin': is_admin, 'authenticated': bool(auth_obj), 'user': user_obj})
