# -*- coding: utf-8 -*-
"""System settings routes — load/save global settings in SQLite."""

from flask import Blueprint, request, jsonify

from backend.routes._helpers import admin_required
from backend.services.system_settings_service import get_system_settings, save_system_settings

system_settings_bp = Blueprint('system_settings', __name__)


@system_settings_bp.route('/api/system_settings', methods=['GET'])
@admin_required
def api_get_system_settings():
    settings = get_system_settings()
    return jsonify({
        'success': True,
        'settings': settings,
    })


@system_settings_bp.route('/api/system_settings', methods=['POST', 'PUT'])
@admin_required
def api_save_system_settings():
    payload = request.get_json(silent=True) or {}

    try:
        settings = save_system_settings(payload)
    except Exception as exc:
        return jsonify({
            'success': False,
            'message': f'Không thể lưu cài đặt hệ thống: {exc}',
        }), 500

    return jsonify({
        'success': True,
        'message': 'Đã lưu cài đặt hệ thống',
        'settings': settings,
    })
