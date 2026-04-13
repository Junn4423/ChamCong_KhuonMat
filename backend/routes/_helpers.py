# -*- coding: utf-8 -*-
"""
Shared helper functions used by multiple route blueprints.

Mirrors the helper closures that were previously inside create_app().
"""

import os
import base64
import secrets
import time
from datetime import datetime
from functools import wraps

from flask import request, jsonify, session, g

from backend.models.database import db, AttendanceLocation
from backend.services.erp_http_client import ERPServiceError, erp_http_client
from backend.routes._state import state


# ─── Employee image helpers ─────────────────────────────────────────────

def sanitize_employee_id(employee_id):
    raw = str(employee_id or '').strip()
    return ''.join(ch if ch.isalnum() or ch in ('-', '_') else '_' for ch in raw)


def employee_image_paths(employee_id):
    safe_id = sanitize_employee_id(employee_id)
    if not safe_id:
        return []
    upload_folder = state.app.config['UPLOAD_FOLDER']
    return [
        os.path.join(upload_folder, f'{safe_id}.jpg'),
        os.path.join(upload_folder, f'{safe_id}.jpeg'),
        os.path.join(upload_folder, f'{safe_id}.png'),
    ]


def save_local_employee_image(employee_id, image_bytes, preferred_ext='.jpg'):
    if not image_bytes:
        return None
    safe_id = sanitize_employee_id(employee_id)
    if not safe_id:
        return None
    ext = preferred_ext if preferred_ext in ('.jpg', '.jpeg', '.png') else '.jpg'
    output_path = os.path.join(state.app.config['UPLOAD_FOLDER'], f'{safe_id}{ext}')
    with open(output_path, 'wb') as f:
        f.write(image_bytes)

    for old_path in employee_image_paths(employee_id):
        if old_path != output_path and os.path.exists(old_path):
            try:
                os.remove(old_path)
            except OSError:
                pass
    return output_path


def get_local_employee_image(employee_id):
    for image_path in employee_image_paths(employee_id):
        if os.path.exists(image_path):
            try:
                with open(image_path, 'rb') as f:
                    return f.read(), image_path
            except OSError:
                continue
    return None, None


def remove_local_employee_images(employee_id):
    for image_path in employee_image_paths(employee_id):
        if os.path.exists(image_path):
            try:
                os.remove(image_path)
            except OSError:
                pass


def to_data_uri(image_bytes, image_path=None):
    if not image_bytes:
        return None
    mime = 'image/jpeg'
    if image_path and image_path.lower().endswith('.png'):
        mime = 'image/png'
    return f"data:{mime};base64,{base64.b64encode(image_bytes).decode('utf-8')}"


def get_erp_employee_image_blob(employee_id, employee=None):
    if not getattr(g, 'erp_auth', None):
        return None
    try:
        image_data = erp_http_client.get_employee_profile_image(
            g.erp_auth,
            employee_id,
            employee=employee,
        )
        return image_data.get('bytes')
    except ERPServiceError:
        return None
    except Exception:
        return None


# ─── Location helpers ────────────────────────────────────────────────────

def coerce_float(value):
    try:
        return float(value)
    except (TypeError, ValueError):
        return None


def parse_location_payload(payload):
    if not isinstance(payload, dict):
        return None

    loc = payload.get('location') if isinstance(payload.get('location'), dict) else payload
    lat = coerce_float(loc.get('latitude', loc.get('lat')))
    lng = coerce_float(loc.get('longitude', loc.get('lng')))
    if lat is None or lng is None:
        return None

    accuracy = coerce_float(loc.get('accuracy'))
    label = (loc.get('label') or loc.get('address') or '').strip()
    provider = (loc.get('provider') or '').strip()
    return {
        'latitude': lat,
        'longitude': lng,
        'accuracy': accuracy,
        'label': label,
        'provider': provider,
        'timestamp': datetime.now().isoformat(),
    }


def location_to_text(location_payload):
    if not isinstance(location_payload, dict):
        return ''
    lat = location_payload.get('latitude')
    lng = location_payload.get('longitude')
    if lat is None or lng is None:
        return ''
    accuracy = location_payload.get('accuracy')
    label = (location_payload.get('label') or '').strip()
    base = f"{lat:.6f}, {lng:.6f}"
    if accuracy is not None:
        base += f" ±{accuracy:.0f}m"
    if label:
        return f"{label} ({base})"
    return base


def set_runtime_location(location_payload, enabled=None):
    crs = state.camera_runtime_state
    if enabled is not None:
        crs['location_enabled'] = bool(enabled)
    if location_payload is None:
        return
    crs['latest_location'] = dict(location_payload)
    crs['updated_at'] = datetime.now().isoformat()


def get_runtime_location(max_age_seconds=600):
    crs = state.camera_runtime_state
    latest = crs.get('latest_location')
    updated_at = crs.get('updated_at')
    if not latest or not updated_at:
        return None
    try:
        ts = datetime.fromisoformat(updated_at)
        age = (datetime.now() - ts).total_seconds()
        if age > max_age_seconds:
            return None
    except Exception:
        return None
    return dict(latest)


def save_attendance_location(attendance_id, location_payload, source='client'):
    if not attendance_id or not location_payload:
        return None
    lat = coerce_float(location_payload.get('latitude'))
    lng = coerce_float(location_payload.get('longitude'))
    if lat is None or lng is None:
        return None
    location_row = AttendanceLocation(
        attendance_id=attendance_id,
        latitude=lat,
        longitude=lng,
        accuracy=coerce_float(location_payload.get('accuracy')),
        label=(location_payload.get('label') or '')[:255],
        source=(source or 'client')[:32],
        raw=str(location_payload)[:2000],
    )
    db.session.add(location_row)
    db.session.commit()
    return location_row


def serialize_attendance_location(attendance_id):
    row = AttendanceLocation.query.filter_by(attendance_id=attendance_id).order_by(
        AttendanceLocation.captured_at.desc()
    ).first()
    if not row:
        return None
    payload = {
        'latitude': row.latitude,
        'longitude': row.longitude,
        'accuracy': row.accuracy,
        'label': row.label,
        'source': row.source,
        'captured_at': row.captured_at.isoformat() if row.captured_at else None,
    }
    payload['text'] = location_to_text(payload)
    return payload


# ─── Session / Auth helpers ──────────────────────────────────────────────

def _build_session_user(auth_info):
    return {
        'code': auth_info.get('code', ''),
        'name': auth_info.get('name', ''),
        'department': auth_info.get('department', ''),
        'role': auth_info.get('role', ''),
        'userid': auth_info.get('userid', ''),
    }


def _to_auth_object(payload):
    if not isinstance(payload, dict):
        return None
    if 'auth' in payload and isinstance(payload['auth'], dict):
        return payload['auth']
    code = (payload.get('code') or '').strip()
    token = (payload.get('token') or '').strip()
    if code and token:
        return {'code': code, 'token': token}
    return None


def _to_user_object(payload):
    if not isinstance(payload, dict):
        return None
    if 'user' in payload and isinstance(payload['user'], dict):
        return payload['user']
    if payload.get('name') or payload.get('code'):
        return _build_session_user(payload)
    return None


def issue_session(auth_info):
    token = secrets.token_hex(32)
    auth_payload = {
        'code': (auth_info.get('code') or '').strip(),
        'token': (auth_info.get('token') or '').strip(),
    }
    user_payload = _build_session_user(auth_info)
    state.erp_sessions[token] = {
        'auth': auth_payload,
        'user': user_payload,
        'expires_at': time.time() + state.SESSION_TTL_SECONDS,
    }
    return token, user_payload


def _resolve_session_payload(session_token):
    payload = state.erp_sessions.get(session_token)
    if not payload:
        return None
    expires_at = payload.get('expires_at')
    if isinstance(expires_at, (int, float)) and expires_at <= time.time():
        state.erp_sessions.pop(session_token, None)
        return None
    payload['expires_at'] = time.time() + state.SESSION_TTL_SECONDS
    return payload


def resolve_request_auth():
    session_token = (
        request.headers.get('X-Session-Token')
        or request.headers.get('X-Admin-Token')
    )
    if not session_token:
        return None, None, None
    payload = _resolve_session_payload(session_token)
    if not payload:
        return session_token, None, None
    return session_token, _to_auth_object(payload), _to_user_object(payload)


# ─── Decorators ──────────────────────────────────────────────────────────

def admin_required(f):
    @wraps(f)
    def wrapper(*args, **kwargs):
        token, auth_obj, user_obj = resolve_request_auth()
        if auth_obj:
            g.session_token = token
            g.erp_auth = auth_obj
            g.session_user = user_obj
            return f(*args, **kwargs)

        admin_token = request.headers.get('X-Admin-Token')
        if not (admin_token and admin_token in state.admin_tokens) and not session.get('is_admin'):
            return jsonify({'success': False, 'message': 'Unauthorized'}), 401
        return f(*args, **kwargs)
    return wrapper


def session_required(f):
    @wraps(f)
    def wrapper(*args, **kwargs):
        token, auth_obj, user_obj = resolve_request_auth()
        if not auth_obj:
            return jsonify({'success': False, 'message': 'Unauthorized'}), 401
        g.session_token = token
        g.erp_auth = auth_obj
        g.session_user = user_obj
        return f(*args, **kwargs)
    return wrapper
