# -*- coding: utf-8 -*-
"""
Shared helper functions used by multiple route blueprints.

Mirrors the helper closures that were previously inside create_app().
"""

import os
import io
import base64
import secrets
import time
from datetime import datetime
from functools import wraps
from urllib.parse import quote

from PIL import Image, ImageOps

from flask import request, jsonify, g

from backend.models.database import db, AttendanceLocation, EmployeeImage
from backend.services.erp_http_client import ERPServiceError, erp_http_client
from backend.routes._state import state


# ─── Employee image helpers ─────────────────────────────────────────────

def sanitize_employee_id(employee_id):
    raw = str(employee_id or '').strip()
    return ''.join(ch if ch.isalnum() or ch in ('-', '_') else '_' for ch in raw)


def employee_image_paths(employee_id):
    # Legacy disk paths kept for one-time migration/cleanup.
    safe_id = sanitize_employee_id(employee_id)
    if not safe_id:
        return []
    upload_folder = state.app.config['UPLOAD_FOLDER']
    return [
        os.path.join(upload_folder, f'{safe_id}.jpg'),
        os.path.join(upload_folder, f'{safe_id}.jpeg'),
        os.path.join(upload_folder, f'{safe_id}.png'),
    ]


def _normalize_image_blob(image_bytes, preferred_ext='.jpg'):
    if not image_bytes:
        return None, None

    ext = (preferred_ext or '.jpg').strip().lower()
    if ext not in ('.jpg', '.jpeg', '.png'):
        ext = '.jpg'

    try:
        img = Image.open(io.BytesIO(image_bytes))
        img = ImageOps.exif_transpose(img)

        if ext in ('.jpg', '.jpeg') and img.mode not in ('RGB', 'L'):
            img = img.convert('RGB')

        if max(img.size) > 1280:
            resampling = getattr(Image, 'Resampling', Image)
            img.thumbnail((1280, 1280), resampling.LANCZOS)

        out = io.BytesIO()
        if ext == '.png':
            if img.mode not in ('RGB', 'RGBA', 'L'):
                img = img.convert('RGBA')
            img.save(out, format='PNG', optimize=True)
            return out.getvalue(), 'image/png'

        if img.mode != 'RGB':
            img = img.convert('RGB')
        img.save(out, format='JPEG', quality=80, optimize=True)
        return out.getvalue(), 'image/jpeg'
    except Exception:
        header = image_bytes[:16]
        if header.startswith(b'\x89PNG'):
            return image_bytes, 'image/png'
        return image_bytes, 'image/jpeg'


def _generate_unique_image_token():
    for _ in range(8):
        token = secrets.token_urlsafe(24).replace('-', '').replace('_', '')
        if not EmployeeImage.query.filter_by(image_token=token).first():
            return token
    return secrets.token_hex(24)


def build_local_image_url(image_token):
    token = (image_token or '').strip()
    if not token:
        return ''
    return f"/api/image/token/{quote(token, safe='')}"


def _get_local_image_row(employee_id):
    safe_id = sanitize_employee_id(employee_id)
    if not safe_id:
        return None
    return EmployeeImage.query.filter_by(employee_id=safe_id).first()


def _migrate_legacy_file_if_needed(employee_id):
    safe_id = sanitize_employee_id(employee_id)
    if not safe_id:
        return None

    row = EmployeeImage.query.filter_by(employee_id=safe_id).first()
    if row and row.image_blob:
        return row

    for legacy_path in employee_image_paths(safe_id):
        if not os.path.exists(legacy_path):
            continue
        try:
            with open(legacy_path, 'rb') as f:
                legacy_bytes = f.read()
            _, ext = os.path.splitext(legacy_path)
            save_local_employee_image(safe_id, legacy_bytes, preferred_ext=ext or '.jpg', source='legacy_file')
            break
        except OSError:
            continue

    for legacy_path in employee_image_paths(safe_id):
        if os.path.exists(legacy_path):
            try:
                os.remove(legacy_path)
            except OSError:
                pass

    return EmployeeImage.query.filter_by(employee_id=safe_id).first()


def save_local_employee_image(employee_id, image_bytes, preferred_ext='.jpg', source='local', erp_image_token=None):
    if not image_bytes:
        return None

    safe_id = sanitize_employee_id(employee_id)
    if not safe_id:
        return None

    normalized_bytes, mime_type = _normalize_image_blob(image_bytes, preferred_ext=preferred_ext)
    if not normalized_bytes:
        return None

    row = EmployeeImage.query.filter_by(employee_id=safe_id).first()
    if row is None:
        row = EmployeeImage(employee_id=safe_id, image_token=_generate_unique_image_token())

    row.image_blob = normalized_bytes
    row.mime_type = mime_type or 'image/jpeg'
    row.source = (source or 'local')[:32]
    if erp_image_token is not None:
        row.erp_image_token = (str(erp_image_token or '').strip() or None)
    row.updated_at = datetime.utcnow()

    db.session.add(row)
    db.session.commit()
    return row.image_token


def get_employee_image_by_token(image_token):
    token = (image_token or '').strip()
    if not token:
        return None
    return EmployeeImage.query.filter_by(image_token=token).first()


def get_local_employee_image(employee_id):
    row = _get_local_image_row(employee_id)
    if row and row.image_blob:
        return row.image_blob, (row.mime_type or 'image/jpeg')

    row = _migrate_legacy_file_if_needed(employee_id)
    if row and row.image_blob:
        return row.image_blob, (row.mime_type or 'image/jpeg')

    return None, None


def get_local_employee_image_token(employee_id):
    row = _get_local_image_row(employee_id)
    if row and row.image_token:
        return row.image_token

    row = _migrate_legacy_file_if_needed(employee_id)
    if row and row.image_token:
        return row.image_token

    return ''


def get_local_employee_erp_image_token(employee_id):
    row = _get_local_image_row(employee_id)
    if row and row.erp_image_token:
        return row.erp_image_token

    row = _migrate_legacy_file_if_needed(employee_id)
    if row and row.erp_image_token:
        return row.erp_image_token

    return ''


def get_local_employee_image_url(employee_id):
    token = get_local_employee_image_token(employee_id)
    if not token:
        return ''
    return build_local_image_url(token)


def remove_local_employee_images(employee_id):
    safe_id = sanitize_employee_id(employee_id)
    if safe_id:
        EmployeeImage.query.filter_by(employee_id=safe_id).delete()
        db.session.commit()

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
    path_hint = (image_path or '').lower() if isinstance(image_path, str) else ''
    if path_hint.endswith('.png') or path_hint == 'image/png':
        mime = 'image/png'
    return f"data:{mime};base64,{base64.b64encode(image_bytes).decode('utf-8')}"


def get_erp_employee_image_data(employee_id, employee=None):
    if not getattr(g, 'erp_auth', None):
        return None
    try:
        image_data = erp_http_client.get_employee_profile_image(
            g.erp_auth,
            employee_id,
            employee=employee,
        )
        if isinstance(image_data, dict) and image_data.get('bytes'):
            existing_row = _get_local_image_row(employee_id)
            local_token = ''
            latest_erp_token = str(image_data.get('image_token') or '').strip() or None

            if existing_row and existing_row.image_blob and (existing_row.source or '') not in {'erp_cache'}:
                if latest_erp_token and latest_erp_token != (existing_row.erp_image_token or '').strip():
                    existing_row.erp_image_token = latest_erp_token
                    db.session.add(existing_row)
                    db.session.commit()
                local_token = existing_row.image_token or ''
            else:
                local_token = save_local_employee_image(
                    employee_id,
                    image_data.get('bytes'),
                    preferred_ext='.jpg',
                    source='erp_cache',
                    erp_image_token=latest_erp_token,
                )

            if local_token:
                image_data = {
                    **image_data,
                    'local_image_token': local_token,
                    'local_image_url': build_local_image_url(local_token),
                }
        return image_data
    except ERPServiceError:
        return None
    except Exception:
        return None


def get_erp_employee_image_blob(employee_id, employee=None):
    image_data = get_erp_employee_image_data(employee_id, employee=employee)
    if not isinstance(image_data, dict):
        return None
    return image_data.get('bytes')


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
        'domain': auth_info.get('domain', ''),
        'method': auth_info.get('method', ''),
        'database': auth_info.get('database', ''),
        'IPv4': auth_info.get('IPv4', ''),
        'lv006': auth_info.get('lv006', ''),
        'lv900': auth_info.get('lv900', ''),
        'lv705': auth_info.get('lv705', ''),
        'lv667': auth_info.get('lv667', ''),
        'lv040': auth_info.get('lv040', ''),
        'device_type': auth_info.get('device_type', ''),
        'type_code': auth_info.get('type_code', ''),
        'quota_exceeded': bool(auth_info.get('quota_exceeded', False)),
        'quota_message': auth_info.get('quota_message', ''),
        'storage_info': auth_info.get('storage_info', {}),
        'auth_mode': auth_info.get('auth_mode', 'system'),
    }


def _to_auth_object(payload):
    if not isinstance(payload, dict):
        return None
    if 'auth' in payload and isinstance(payload['auth'], dict):
        return payload['auth']
    code = (payload.get('code') or '').strip()
    token = (payload.get('token') or '').strip()
    if code and token:
        auth_obj = {'code': code, 'token': token}
        for key in (
            'database',
            'IPv4',
            'server_ip',
            'role',
            'domain',
            'method',
            'lv006',
            'lv900',
            'device_type',
            'type_code',
        ):
            value = payload.get(key)
            if isinstance(value, str):
                value = value.strip()
            if value not in (None, ''):
                auth_obj[key] = value
        return auth_obj
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
        'database': (auth_info.get('database') or '').strip(),
        'IPv4': (auth_info.get('IPv4') or '').strip(),
        'server_ip': (auth_info.get('IPv4') or '').strip(),
        'role': (auth_info.get('role') or '').strip(),
        'domain': (auth_info.get('domain') or '').strip(),
        'method': (auth_info.get('method') or '').strip(),
        'lv006': (auth_info.get('lv006') or '').strip(),
        'lv900': (auth_info.get('lv900') or '').strip(),
        'device_type': (auth_info.get('device_type') or '').strip(),
        'type_code': (auth_info.get('type_code') or '').strip(),
    }
    user_payload = _build_session_user(auth_info)
    state.erp_sessions[token] = {
        'auth': auth_payload,
        'user': user_payload,
        'mode': user_payload.get('auth_mode', 'system'),
        'expires_at': time.time() + state.SESSION_TTL_SECONDS,
    }
    return token, user_payload


def issue_internal_session(username=''):
    user_code = (str(username or '').strip() or state.INTERNAL_ADMIN_USERNAME or 'admin')
    user_payload = {
        'code': user_code,
        'name': 'Admin nội bộ',
        'department': 'Nội bộ',
        'role': 'internal_admin',
        'userid': user_code,
        'domain': '',
        'method': '',
        'database': '',
        'IPv4': '',
        'lv006': '',
        'lv900': '',
        'lv705': '',
        'lv667': '',
        'lv040': '',
        'device_type': '',
        'type_code': '',
        'quota_exceeded': False,
        'quota_message': '',
        'storage_info': {},
        'auth_mode': 'internal',
    }

    token = secrets.token_hex(32)
    state.erp_sessions[token] = {
        'user': user_payload,
        'mode': 'internal',
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


def resolve_request_auth_mode(default='internal'):
    user_obj = getattr(g, 'session_user', None)
    if isinstance(user_obj, dict):
        mode = (user_obj.get('auth_mode') or '').strip().lower()
        if mode:
            return mode

    session_token = (
        request.headers.get('X-Session-Token')
        or request.headers.get('X-Admin-Token')
    )
    if session_token:
        payload = _resolve_session_payload(session_token)
        if isinstance(payload, dict):
            mode = (payload.get('mode') or '').strip().lower()
            if mode:
                return mode

    return default


# ─── Decorators ──────────────────────────────────────────────────────────

def admin_required(f):
    @wraps(f)
    def wrapper(*args, **kwargs):
        token, auth_obj, user_obj = resolve_request_auth()
        if token:
            g.session_token = token
        if user_obj:
            g.session_user = user_obj
        if auth_obj:
            g.erp_auth = auth_obj
            return f(*args, **kwargs)

        admin_token = token or request.headers.get('X-Admin-Token')
        if not (admin_token and admin_token in state.admin_tokens and user_obj):
            return jsonify({
                'success': False,
                'message': 'Phiên đăng nhập đã hết hạn, vui lòng đăng nhập lại',
            }), 401
        return f(*args, **kwargs)
    return wrapper


def session_required(f):
    @wraps(f)
    def wrapper(*args, **kwargs):
        token, auth_obj, user_obj = resolve_request_auth()
        if not auth_obj:
            return jsonify({
                'success': False,
                'message': 'Phiên đăng nhập đã hết hạn, vui lòng đăng nhập lại',
            }), 401
        g.session_token = token
        g.erp_auth = auth_obj
        g.session_user = user_obj
        return f(*args, **kwargs)
    return wrapper
