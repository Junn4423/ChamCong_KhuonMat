# -*- coding: utf-8 -*-
"""Admin and public APIs for mobile auto-configuration pairing."""

from __future__ import annotations

from flask import Blueprint, jsonify, request

from backend.routes._helpers import admin_required
from backend.services.mobile_pairing_service import (
    build_discovery_offer,
    create_qr_pairing_session,
    get_local_ipv4_candidates,
    get_mobile_pairing_settings,
    list_qr_pairing_sessions,
    resolve_local_ip_for_peer,
    save_mobile_pairing_settings,
    validate_pair_request,
)


mobile_config_bp = Blueprint('mobile_config', __name__)


@mobile_config_bp.route('/api/mobile_config/settings', methods=['GET'])
@admin_required
def get_mobile_config_settings():
    settings = get_mobile_pairing_settings(include_sensitive=False)
    candidates = get_local_ipv4_candidates()
    discovery_preview = None
    if candidates:
        discovery_preview = build_discovery_offer(settings, candidates[0])

    return jsonify({
        'success': True,
        'settings': settings,
        'lan_ipv4_candidates': candidates,
        'active_qr_sessions': list_qr_pairing_sessions(),
        'discovery_preview': discovery_preview,
    })


@mobile_config_bp.route('/api/mobile_config/settings', methods=['POST', 'PUT'])
@admin_required
def save_mobile_config_settings():
    payload = request.get_json(silent=True) or {}

    try:
        settings, changed_pass = save_mobile_pairing_settings(payload)
    except Exception as exc:
        return jsonify({
            'success': False,
            'message': f'Khong the luu cau hinh mobile: {exc}',
        }), 500

    message = 'Da luu cau hinh mobile'
    if changed_pass:
        message += ' va cap nhat pass pairing'

    return jsonify({
        'success': True,
        'message': message,
        'settings': settings,
        'lan_ipv4_candidates': get_local_ipv4_candidates(),
    })


@mobile_config_bp.route('/api/mobile_config/qr_session', methods=['POST'])
@admin_required
def create_mobile_qr_session():
    payload = request.get_json(silent=True) or {}
    target_host = payload.get('target_host') or payload.get('host') or ''

    try:
        qr_session = create_qr_pairing_session(target_host=target_host)
    except Exception as exc:
        return jsonify({
            'success': False,
            'message': f'Khong the tao QR pairing: {exc}',
        }), 500

    return jsonify({
        'success': True,
        'message': 'Da tao ma QR pairing',
        'session': qr_session,
        'active_qr_sessions': list_qr_pairing_sessions(),
    })


@mobile_config_bp.route('/api/mobile_config/discovery_offer', methods=['GET'])
def get_mobile_discovery_offer():
    settings = get_mobile_pairing_settings(include_sensitive=False)
    peer_ip = request.remote_addr or ''
    local_ip = resolve_local_ip_for_peer(peer_ip)
    offer = build_discovery_offer(settings, local_ip)
    return jsonify({
        'success': True,
        'offer': offer,
    })


@mobile_config_bp.route('/api/mobile_config/pair', methods=['POST'])
def pair_mobile_client():
    payload = request.get_json(silent=True) or {}
    result = validate_pair_request(
        payload=payload,
        request_host=request.host,
        remote_addr=request.remote_addr,
    )
    status_code = int(result.get('status_code') or 200)
    body = {
        'success': bool(result.get('success')),
        'message': result.get('message') or '',
    }
    if result.get('success'):
        body['connection'] = result.get('connection') or {}
    return jsonify(body), status_code
