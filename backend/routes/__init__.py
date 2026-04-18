# -*- coding: utf-8 -*-
"""Route blueprints package – register all blueprints onto a Flask app."""

from backend.routes.auth import auth_bp
from backend.routes.registration import registration_bp
from backend.routes.camera import camera_bp
from backend.routes.attendance import attendance_bp
from backend.routes.employee import employee_bp
from backend.routes.location import location_bp
from backend.routes.system_settings import system_settings_bp

ALL_BLUEPRINTS = [
    auth_bp,
    registration_bp,
    camera_bp,
    attendance_bp,
    employee_bp,
    location_bp,
    system_settings_bp,
]


def register_all_blueprints(app):
    """Register every route blueprint onto *app*."""
    for bp in ALL_BLUEPRINTS:
        app.register_blueprint(bp)
