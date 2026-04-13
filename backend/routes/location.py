# -*- coding: utf-8 -*-
"""Location routes — get/set runtime location.  (maps to frontend modules/location.js)"""

from flask import Blueprint, request, jsonify

from backend.routes._state import state
from backend.routes._helpers import (
    admin_required,
    parse_location_payload, location_to_text,
    set_runtime_location, get_runtime_location,
)

location_bp = Blueprint('location', __name__)


@location_bp.route('/api/location/current', methods=['GET', 'POST'])
@admin_required
def location_current():
    if request.method == 'GET':
        latest = get_runtime_location(max_age_seconds=86400)
        return jsonify({
            'success': True,
            'enabled': bool(state.camera_runtime_state.get('location_enabled')),
            'location': latest,
            'location_text': location_to_text(latest) if latest else '',
        })

    data = request.get_json(silent=True) or {}
    enabled = data.get('enabled') if 'enabled' in data else None
    location_payload = parse_location_payload(data)

    if enabled is not None:
        state.camera_runtime_state['location_enabled'] = bool(enabled)
    if location_payload:
        set_runtime_location(location_payload)
    elif enabled is None:
        return jsonify({'success': False, 'message': 'Dữ liệu location không hợp lệ'}), 400

    latest = get_runtime_location(max_age_seconds=86400)
    return jsonify({
        'success': True,
        'enabled': bool(state.camera_runtime_state.get('location_enabled')),
        'location': latest,
        'location_text': location_to_text(latest) if latest else '',
    })
