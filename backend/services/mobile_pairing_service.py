# -*- coding: utf-8 -*-
"""Mobile pairing settings, pass verification, and QR session helpers."""

from __future__ import annotations

import hashlib
import hmac
import json
import os
import secrets
import socket
import string
import threading
import time
from datetime import datetime, timezone
from typing import Dict, List, Optional, Tuple

from backend.models.database import SystemSetting, db


SYSTEM_SETTING_KEY_MOBILE_PAIRING = 'mobile_pairing_settings'
PAIRING_SCHEMA = 'facecheck-mobile-pairing-v1'
DISCOVERY_SCHEMA = 'facecheck-mobile-discovery-v1'

PBKDF2_ITERATIONS = 180000

DEFAULT_MOBILE_PAIRING_SETTINGS = {
    'enabled': True,
    'server_id': '',
    'server_name': 'FaceCheck',
    'allow_udp_discovery': True,
    'allow_qr_pairing': True,
    'discovery_port': int(os.getenv('MOBILE_DISCOVERY_PORT', 45876)),
    'api_port': int(os.getenv('MOBILE_API_PORT', os.getenv('FLASK_PORT', 5000))),
    'web_port': int(os.getenv('MOBILE_WEB_PORT', 5173)),
    'qr_ttl_seconds': 300,
    'pair_version': 1,
    'pass_hash': '',
    'pass_hint': '',
    'pass_updated_at': '',
    'updated_at': '',
}

_QR_SESSION_LOCK = threading.Lock()
_QR_SESSION_STORE: Dict[str, Dict[str, object]] = {}


def _now_utc_iso() -> str:
    return datetime.now(timezone.utc).isoformat()


def _coerce_int(value, fallback, minimum, maximum):
    try:
        parsed = int(float(value))
    except (TypeError, ValueError):
        return fallback
    return max(minimum, min(maximum, parsed))


def _coerce_bool(value, fallback):
    if isinstance(value, bool):
        return value
    if isinstance(value, (int, float)):
        return bool(value)
    if isinstance(value, str):
        text = value.strip().lower()
        if text in {'1', 'true', 'yes', 'on'}:
            return True
        if text in {'0', 'false', 'no', 'off'}:
            return False
    return fallback


def _normalize_host(value: str) -> str:
    text = str(value or '').strip()
    if not text:
        return ''
    if '://' in text:
        text = text.split('://', 1)[1]
    text = text.split('/', 1)[0].strip()
    text = text.split('@')[-1].strip()
    if ':' in text:
        host_part, port_part = text.rsplit(':', 1)
        if port_part.isdigit():
            text = host_part
    text = text.strip('[]').strip()
    return text


def _is_valid_ipv4(ip: str) -> bool:
    parts = ip.split('.')
    if len(parts) != 4:
        return False
    for part in parts:
        if not part.isdigit():
            return False
        value = int(part)
        if value < 0 or value > 255:
            return False
    return True


def _is_private_ipv4(ip: str) -> bool:
    if not _is_valid_ipv4(ip):
        return False
    part_a, part_b, *_ = [int(item) for item in ip.split('.')]
    if part_a == 10:
        return True
    if part_a == 172 and 16 <= part_b <= 31:
        return True
    if part_a == 192 and part_b == 168:
        return True
    return False


def _score_lan_candidate(ip: str) -> tuple:
    if not _is_valid_ipv4(ip):
        return (99, ip)
    part_a, part_b, *_ = [int(item) for item in ip.split('.')]
    if part_a == 192 and part_b == 168:
        return (0, ip)
    if part_a == 10:
        return (1, ip)
    if part_a == 172 and 16 <= part_b <= 31:
        return (2, ip)
    if _is_private_ipv4(ip):
        return (3, ip)
    return (4, ip)


def _read_setting_json(setting_key: str) -> dict:
    row = SystemSetting.query.filter_by(key=setting_key).first()
    if not row or not row.value:
        return {}
    try:
        parsed = json.loads(row.value)
    except json.JSONDecodeError:
        return {}
    return parsed if isinstance(parsed, dict) else {}


def _write_setting_json(setting_key: str, payload: dict) -> None:
    row = SystemSetting.query.filter_by(key=setting_key).first()
    if row is None:
        row = SystemSetting(key=setting_key, value='{}')
    row.value = json.dumps(payload, ensure_ascii=True)
    db.session.add(row)


def _normalize_settings(raw: dict, include_sensitive: bool = False) -> dict:
    source = raw if isinstance(raw, dict) else {}
    normalized = dict(DEFAULT_MOBILE_PAIRING_SETTINGS)

    normalized['enabled'] = _coerce_bool(source.get('enabled'), True)
    normalized['server_id'] = str(source.get('server_id') or '').strip() or secrets.token_hex(12)
    normalized['server_name'] = str(source.get('server_name') or '').strip()[:64] or 'FaceCheck'
    normalized['allow_udp_discovery'] = _coerce_bool(source.get('allow_udp_discovery'), True)
    normalized['allow_qr_pairing'] = _coerce_bool(source.get('allow_qr_pairing'), True)
    normalized['discovery_port'] = _coerce_int(
        source.get('discovery_port'),
        DEFAULT_MOBILE_PAIRING_SETTINGS['discovery_port'],
        1024,
        65535,
    )
    normalized['api_port'] = _coerce_int(
        source.get('api_port'),
        DEFAULT_MOBILE_PAIRING_SETTINGS['api_port'],
        1,
        65535,
    )
    normalized['web_port'] = _coerce_int(
        source.get('web_port'),
        DEFAULT_MOBILE_PAIRING_SETTINGS['web_port'],
        1,
        65535,
    )
    normalized['qr_ttl_seconds'] = _coerce_int(
        source.get('qr_ttl_seconds'),
        DEFAULT_MOBILE_PAIRING_SETTINGS['qr_ttl_seconds'],
        30,
        3600,
    )
    normalized['pair_version'] = _coerce_int(source.get('pair_version'), 1, 1, 2_000_000_000)
    normalized['pass_hint'] = str(source.get('pass_hint') or '').strip()[:120]
    normalized['pass_updated_at'] = str(source.get('pass_updated_at') or '').strip()[:64]
    normalized['updated_at'] = str(source.get('updated_at') or '').strip()[:64]

    pass_hash = str(source.get('pass_hash') or '').strip()
    if include_sensitive:
        normalized['pass_hash'] = pass_hash

    normalized['pass_configured'] = bool(pass_hash)
    return normalized


def get_mobile_pairing_settings(include_sensitive: bool = False) -> dict:
    raw = _read_setting_json(SYSTEM_SETTING_KEY_MOBILE_PAIRING)
    normalized_sensitive = _normalize_settings(raw, include_sensitive=True)

    should_persist = False
    if normalized_sensitive['server_id'] != str(raw.get('server_id') or '').strip():
        should_persist = True
    if not raw:
        should_persist = True

    if should_persist:
        persisted = {
            **normalized_sensitive,
            'updated_at': _now_utc_iso(),
        }
        _write_setting_json(SYSTEM_SETTING_KEY_MOBILE_PAIRING, persisted)
        db.session.commit()
        normalized_sensitive = _normalize_settings(persisted, include_sensitive=True)

    if include_sensitive:
        return normalized_sensitive
    return _normalize_settings(normalized_sensitive, include_sensitive=False)


def hash_pair_pass(raw_pass: str) -> str:
    text = str(raw_pass or '').strip()
    salt = secrets.token_bytes(16)
    digest = hashlib.pbkdf2_hmac('sha256', text.encode('utf-8'), salt, PBKDF2_ITERATIONS)
    return 'pbkdf2_sha256${}${}${}'.format(
        PBKDF2_ITERATIONS,
        salt.hex(),
        digest.hex(),
    )


def verify_pair_pass(raw_pass: str, stored_hash: str) -> bool:
    text = str(raw_pass or '').strip()
    encoded = str(stored_hash or '').strip()
    if not encoded:
        return False

    try:
        algorithm, iterations_text, salt_hex, digest_hex = encoded.split('$', 3)
        if algorithm != 'pbkdf2_sha256':
            return False
        iterations = int(iterations_text)
        salt = bytes.fromhex(salt_hex)
        expected_digest = bytes.fromhex(digest_hex)
    except (ValueError, TypeError):
        return False

    candidate_digest = hashlib.pbkdf2_hmac('sha256', text.encode('utf-8'), salt, iterations)
    return hmac.compare_digest(candidate_digest, expected_digest)


def save_mobile_pairing_settings(payload: dict) -> Tuple[dict, bool]:
    source = payload if isinstance(payload, dict) else {}
    current = get_mobile_pairing_settings(include_sensitive=True)
    next_settings = dict(current)
    changed_pass = False

    next_settings['enabled'] = _coerce_bool(source.get('enabled'), next_settings['enabled'])
    next_settings['server_name'] = str(
        source.get('server_name') if source.get('server_name') is not None else next_settings['server_name']
    ).strip()[:64] or 'FaceCheck'
    next_settings['allow_udp_discovery'] = _coerce_bool(
        source.get('allow_udp_discovery'),
        next_settings['allow_udp_discovery'],
    )
    next_settings['allow_qr_pairing'] = _coerce_bool(
        source.get('allow_qr_pairing'),
        next_settings['allow_qr_pairing'],
    )
    next_settings['discovery_port'] = _coerce_int(
        source.get('discovery_port'),
        next_settings['discovery_port'],
        1024,
        65535,
    )
    next_settings['api_port'] = _coerce_int(source.get('api_port'), next_settings['api_port'], 1, 65535)
    next_settings['web_port'] = _coerce_int(source.get('web_port'), next_settings['web_port'], 1, 65535)
    next_settings['qr_ttl_seconds'] = _coerce_int(
        source.get('qr_ttl_seconds'),
        next_settings['qr_ttl_seconds'],
        30,
        3600,
    )
    next_settings['pass_hint'] = str(source.get('pass_hint') or '').strip()[:120]

    next_pass = str(source.get('pair_pass') or '').strip()
    if next_pass:
        next_settings['pass_hash'] = hash_pair_pass(next_pass)
        next_settings['pair_version'] = _coerce_int(next_settings.get('pair_version'), 1, 1, 2_000_000_000) + 1
        next_settings['pass_updated_at'] = _now_utc_iso()
        changed_pass = True

    if _coerce_bool(source.get('rotate_pair_version'), False):
        next_settings['pair_version'] = _coerce_int(next_settings.get('pair_version'), 1, 1, 2_000_000_000) + 1
        changed_pass = True

    next_settings['updated_at'] = _now_utc_iso()

    normalized = _normalize_settings(next_settings, include_sensitive=True)
    _write_setting_json(SYSTEM_SETTING_KEY_MOBILE_PAIRING, normalized)
    db.session.commit()

    return _normalize_settings(normalized, include_sensitive=False), changed_pass


def get_local_ipv4_candidates() -> List[str]:
    candidates = set()

    try:
        host_info = socket.gethostbyname_ex(socket.gethostname())
        for ip in host_info[2]:
            if _is_valid_ipv4(ip) and not ip.startswith('127.'):
                candidates.add(ip)
    except OSError:
        pass

    try:
        probe = socket.socket(socket.AF_INET, socket.SOCK_DGRAM)
        probe.connect(('8.8.8.8', 80))
        local_ip = probe.getsockname()[0]
        if _is_valid_ipv4(local_ip) and not local_ip.startswith('127.'):
            candidates.add(local_ip)
    except OSError:
        pass
    finally:
        try:
            probe.close()
        except Exception:
            pass

    return sorted(candidates, key=_score_lan_candidate)


def resolve_local_ip_for_peer(peer_ip: str) -> str:
    ip = str(peer_ip or '').strip()
    if _is_valid_ipv4(ip):
        probe = None
        try:
            probe = socket.socket(socket.AF_INET, socket.SOCK_DGRAM)
            probe.connect((ip, 9))
            local_ip = probe.getsockname()[0]
            if _is_valid_ipv4(local_ip) and not local_ip.startswith('127.'):
                return local_ip
        except OSError:
            pass
        finally:
            if probe is not None:
                try:
                    probe.close()
                except Exception:
                    pass

    candidates = get_local_ipv4_candidates()
    if candidates:
        return candidates[0]
    return '127.0.0.1'


def _cleanup_qr_sessions() -> None:
    now_ts = time.time()
    with _QR_SESSION_LOCK:
        expired_codes = []
        for code, record in _QR_SESSION_STORE.items():
            expires_at = float(record.get('expires_at') or 0)
            if expires_at <= now_ts:
                expired_codes.append(code)
        for code in expired_codes:
            _QR_SESSION_STORE.pop(code, None)


def _generate_pairing_code() -> str:
    alphabet = string.ascii_uppercase + string.digits
    return ''.join(secrets.choice(alphabet) for _ in range(8))


def _build_base_urls(host: str, settings: dict) -> Tuple[str, str]:
    selected_host = _normalize_host(host) or resolve_local_ip_for_peer('')
    api_base_url = f'http://{selected_host}:{settings["api_port"]}'
    web_base_url = f'http://{selected_host}:{settings["web_port"]}'
    return api_base_url, web_base_url


def build_discovery_offer(settings: dict, host: str) -> dict:
    api_base_url, web_base_url = _build_base_urls(host, settings)
    return {
        'type': 'facecheck.mobile.offer',
        'schema': DISCOVERY_SCHEMA,
        'server_id': settings.get('server_id', ''),
        'server_name': settings.get('server_name', 'FaceCheck'),
        'pair_version': int(settings.get('pair_version') or 1),
        'pair_pass_required': bool(settings.get('pass_configured')),
        'allow_qr_pairing': bool(settings.get('allow_qr_pairing')),
        'allow_udp_discovery': bool(settings.get('allow_udp_discovery')),
        'pass_hint': str(settings.get('pass_hint') or '').strip(),
        'api_base_url': api_base_url,
        'web_base_url': web_base_url,
        'pair_endpoint': '/api/mobile_config/pair',
        'discovery_port': int(settings.get('discovery_port') or DEFAULT_MOBILE_PAIRING_SETTINGS['discovery_port']),
        'timestamp': _now_utc_iso(),
    }


def create_qr_pairing_session(target_host: str = '') -> dict:
    _cleanup_qr_sessions()
    settings = get_mobile_pairing_settings(include_sensitive=False)
    ttl_seconds = int(settings.get('qr_ttl_seconds') or DEFAULT_MOBILE_PAIRING_SETTINGS['qr_ttl_seconds'])
    expires_at = time.time() + ttl_seconds

    with _QR_SESSION_LOCK:
        code = _generate_pairing_code()
        while code in _QR_SESSION_STORE:
            code = _generate_pairing_code()

        _QR_SESSION_STORE[code] = {
            'code': code,
            'created_at': _now_utc_iso(),
            'expires_at': expires_at,
            'pair_version': int(settings.get('pair_version') or 1),
            'server_id': settings.get('server_id', ''),
            'target_host': _normalize_host(target_host),
            'used_at': '',
            'used': False,
        }

    api_base_url, web_base_url = _build_base_urls(target_host, settings)
    qr_payload = {
        'schema': PAIRING_SCHEMA,
        'server_id': settings.get('server_id', ''),
        'pair_version': int(settings.get('pair_version') or 1),
        'pairing_code': code,
        'api_base_url': api_base_url,
        'web_base_url': web_base_url,
    }

    return {
        'pairing_code': code,
        'expires_at': datetime.fromtimestamp(expires_at, tz=timezone.utc).isoformat(),
        'ttl_seconds': ttl_seconds,
        'qr_payload': qr_payload,
    }


def list_qr_pairing_sessions() -> List[dict]:
    _cleanup_qr_sessions()
    now_ts = time.time()
    rows = []
    with _QR_SESSION_LOCK:
        for code, record in _QR_SESSION_STORE.items():
            expires_at = float(record.get('expires_at') or 0)
            rows.append({
                'pairing_code': code,
                'expires_at': datetime.fromtimestamp(expires_at, tz=timezone.utc).isoformat(),
                'seconds_left': max(0, int(expires_at - now_ts)),
                'used': bool(record.get('used')),
                'used_at': str(record.get('used_at') or ''),
                'target_host': str(record.get('target_host') or ''),
            })
    rows.sort(key=lambda item: item['expires_at'])
    return rows


def _consume_qr_pairing_code(pairing_code: str) -> Tuple[bool, Optional[dict]]:
    _cleanup_qr_sessions()
    code = str(pairing_code or '').strip().upper()
    if not code:
        return False, None

    with _QR_SESSION_LOCK:
        record = _QR_SESSION_STORE.get(code)
        if not record:
            return False, None
        if bool(record.get('used')):
            return False, None
        expires_at = float(record.get('expires_at') or 0)
        if expires_at <= time.time():
            _QR_SESSION_STORE.pop(code, None)
            return False, None
        record['used'] = True
        record['used_at'] = _now_utc_iso()
        return True, dict(record)


def validate_pair_request(payload: dict, request_host: str = '', remote_addr: str = '') -> dict:
    source = payload if isinstance(payload, dict) else {}
    settings_sensitive = get_mobile_pairing_settings(include_sensitive=True)
    settings_public = _normalize_settings(settings_sensitive, include_sensitive=False)

    if not settings_public.get('enabled', True):
        return {
            'success': False,
            'status_code': 403,
            'message': 'Mobile pairing dang bi tat tren he thong.',
        }

    expected_server_id = str(settings_public.get('server_id') or '')
    request_server_id = str(source.get('server_id') or '').strip()
    if request_server_id and request_server_id != expected_server_id:
        return {
            'success': False,
            'status_code': 409,
            'message': 'May chu pairing khong trung voi server hien tai.',
        }

    pass_hash = str(settings_sensitive.get('pass_hash') or '').strip()
    pass_hint = str(settings_public.get('pass_hint') or '').strip()
    pair_pass = str(source.get('pair_pass') or '').strip()
    if pass_hash:
        if not pair_pass:
            message = 'Vui long nhap pass mobile de ket noi.'
            if pass_hint:
                message += f' Goi y: {pass_hint}'
            return {
                'success': False,
                'status_code': 401,
                'message': message,
            }
        if not verify_pair_pass(pair_pass, pass_hash):
            message = 'Pass mobile khong dung.'
            if pass_hint:
                message += f' Goi y: {pass_hint}'
            return {
                'success': False,
                'status_code': 401,
                'message': message,
            }

    pairing_code = str(source.get('pairing_code') or '').strip().upper()
    consumed_session = None
    pairing_method = 'pass'

    if pairing_code:
        if not settings_public.get('allow_qr_pairing', True):
            return {
                'success': False,
                'status_code': 403,
                'message': 'QR pairing dang bi tat tren he thong.',
            }
        consumed_ok, session = _consume_qr_pairing_code(pairing_code)
        if not consumed_ok or not session:
            return {
                'success': False,
                'status_code': 401,
                'message': 'Ma QR pairing khong hop le hoac da het han.',
            }
        consumed_session = session
        pairing_method = 'qr_pass' if pair_pass else 'qr'

    requested_host = _normalize_host(source.get('target_host') or '')
    if not requested_host:
        requested_host = _normalize_host(request_host or '') or _normalize_host(remote_addr or '')
    if not requested_host and consumed_session:
        requested_host = _normalize_host(consumed_session.get('target_host') or '')

    api_base_url, web_base_url = _build_base_urls(requested_host, settings_public)

    connection = {
        'label': settings_public.get('server_name') or 'FaceCheck',
        'web_base_url': web_base_url,
        'api_base_url': api_base_url,
        'server_id': expected_server_id,
        'pair_version': int(settings_public.get('pair_version') or 1),
        'pairing_method': pairing_method,
        'paired_at': _now_utc_iso(),
    }

    return {
        'success': True,
        'status_code': 200,
        'message': 'Pairing thanh cong.',
        'connection': connection,
    }
