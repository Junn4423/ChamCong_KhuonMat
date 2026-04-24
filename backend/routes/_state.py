# -*- coding: utf-8 -*-
"""
Shared mutable runtime state for the route layer.

app.py writes into this module at startup so that every blueprint
can access the same objects without circular imports.
"""


class _AppState:
    """Mutable bag that holds references injected by create_app()."""
    app = None
    face_recognizer = None
    face_recognition_lock = None
    attendance_service_ref = None   # [AttendanceService | None]
    admin_tokens = None             # set()
    erp_sessions = None             # dict
    camera_runtime_state = None     # dict
    mobile_discovery_service = None # MobileUdpDiscoveryService | None
    load_known_faces = None         # callable
    ADMIN_PASSWORD = 'admin123'
    INTERNAL_ADMIN_USERNAME = 'admin'
    INTERNAL_ADMIN_PASSWORD = '1'
    SESSION_TTL_SECONDS = 8 * 3600


state = _AppState()
