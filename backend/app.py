# -*- coding: utf-8 -*-
"""Face Recognition System - Flask API Backend (JSON only, no templates).

This file is now a thin orchestrator that:
  1. Creates the Flask app & configures extensions.
  2. Initialises the face-recognition engine.
  3. Populates the shared runtime state for all blueprint modules.
  4. Registers every route blueprint from backend.routes.

All route handlers live in backend/routes/<module>.py,
mirroring the frontend src/services/modules/<module>.js structure.
"""

import os
os.environ.setdefault('KMP_DUPLICATE_LIB_OK', 'TRUE')
os.environ.setdefault('OMP_NUM_THREADS', '1')
os.environ.setdefault('MKL_NUM_THREADS', '1')

from flask import Flask, jsonify, send_from_directory
from flask_cors import CORS
import sys
import numpy as np
from datetime import datetime, date
import threading
import time

from backend.config import CAMERA_CONFIG
from backend.face_encoding_utils import face_encoding_count, normalize_face_encodings
from backend.models.database import db, User, Attendance, ensure_database_schema
from backend.models.erp_integration import erp_attendance
from backend.runtime import ensure_data_dir, ensure_db_path, sqlite_database_uri
from backend.services.attendance_service import AttendanceService

_FACE_IMPORT_ERROR = None
try:
    from backend.models.face_recognition_module import FaceRecognition
except Exception as exc:  # pragma: no cover - runtime fallback path
    FaceRecognition = None
    _FACE_IMPORT_ERROR = str(exc)


def create_app():
    app = Flask(__name__)
    CORS(app, supports_credentials=True)
    data_dir = ensure_data_dir()
    upload_dir = data_dir / 'faces'
    upload_dir.mkdir(parents=True, exist_ok=True)

    app.config['SECRET_KEY'] = os.environ.get('SECRET_KEY', 'face-recognition-secret-key')
    app.config['SQLALCHEMY_DATABASE_URI'] = sqlite_database_uri()
    app.config['SQLALCHEMY_TRACK_MODIFICATIONS'] = False
    app.config['UPLOAD_FOLDER'] = str(upload_dir)

    db.init_app(app)

    # ── Face recognition engine (with fallback) ──────────────────────────

    class _FallbackEngine:
        @staticmethod
        def detect_and_encode(_frame):
            return []

        @staticmethod
        def check_anti_spoofing(_frame, _bbox_xywh):
            return True, 1.0

    class _FallbackFaceRecognizer:
        def __init__(self):
            self.known_face_encodings = np.empty((0, 512))
            self.known_face_names = []
            self.known_face_ids = []
            self.engine = _FallbackEngine()

        def load_known_faces(self, _users):
            self.known_face_encodings = np.empty((0, 512))
            self.known_face_names = []
            self.known_face_ids = []

        @staticmethod
        def encode_face_from_image(_image_file):
            return None, 'Module nhận diện khuôn mặt chưa sẵn sàng'

        @staticmethod
        def encode_face_from_base64(_image_base64):
            return None, 'Module nhận diện khuôn mặt chưa sẵn sàng'

        @staticmethod
        def compare_faces(_known_face_encodings, _face_encoding_to_check, tolerance=0.6):
            _ = tolerance
            return []

        @staticmethod
        def compute_distance(_face_encodings, _face_to_compare):
            return np.empty((0,))

        @staticmethod
        def draw_faces_on_frame(frame, _face_locations, _face_names):
            return frame

    try:
        if FaceRecognition is None:
            raise RuntimeError(f"FaceRecognition import failed: {_FACE_IMPORT_ERROR or 'unknown'}")
        face_recognizer = FaceRecognition(det_thresh=0.35)
    except Exception as exc:
        print(f'FaceRecognition init failed, fallback to API-safe mode: {exc}')
        face_recognizer = _FallbackFaceRecognizer()

    face_recognition_lock = threading.Lock()
    attendance_service_ref = [None]  # mutable container

    # ── Populate shared state for blueprints ─────────────────────────────

    from backend.routes._state import state

    state.app = app
    state.face_recognizer = face_recognizer
    state.face_recognition_lock = face_recognition_lock
    state.attendance_service_ref = attendance_service_ref
    state.admin_tokens = set()
    state.erp_sessions = {}
    state.camera_runtime_state = {
        'camera_id': None,
        'camera_name': None,
        'latest_location': None,
        'location_enabled': False,
        'updated_at': None,
    }
    state.INTERNAL_ADMIN_USERNAME = os.environ.get('INTERNAL_ADMIN_USERNAME', 'admin')
    state.INTERNAL_ADMIN_PASSWORD = os.environ.get(
        'INTERNAL_ADMIN_PASSWORD',
        os.environ.get('ADMIN_PASSWORD', '1'),
    )
    # Keep legacy field for compatibility with older code paths.
    state.ADMIN_PASSWORD = state.INTERNAL_ADMIN_PASSWORD
    state.SESSION_TTL_SECONDS = int(os.environ.get('SESSION_TTL_SECONDS', 8 * 3600))

    # ── Core functions shared with both routes and background tasks ──────

    def load_known_faces():
        users = User.query.all()
        face_recognizer.load_known_faces(users)
        if attendance_service_ref[0]:
            attendance_service_ref[0].update_known_faces(face_recognizer)

    state.load_known_faces = load_known_faces

    def perform_auto_attendance(user_id, current_encoding=None, min_dist=1.0):
        try:
            today = date.today()
            current_time = datetime.now()
            user = User.query.get(user_id)
            if not user:
                return

            # Adaptive learning
            if current_encoding is not None and min_dist < 0.35:
                try:
                    with face_recognition_lock:
                        should_learn = True
                        current_encodings = []
                        for enc in normalize_face_encodings(user.face_encoding):
                            enc_arr = np.array(enc)
                            if enc_arr.shape == (512,):
                                current_encodings.append(enc_arr.tolist())

                        for known_enc in current_encodings:
                            dist = face_recognizer.compute_distance([known_enc], current_encoding)[0]
                            if dist < 0.1:
                                should_learn = False
                                break

                        if should_learn:
                            current_encodings.append(current_encoding)
                            if len(current_encodings) > 5:
                                current_encodings.pop(0)
                            user.face_encoding = current_encodings
                            db.session.commit()
                            load_known_faces()
                except Exception as e:
                    print(f"Adaptive learning error: {e}")

            last_attendance = Attendance.query.filter_by(
                user_id=user_id, date=today
            ).order_by(Attendance.check_in_time.desc()).first()
            if last_attendance and not last_attendance.check_out_time:
                return

            if last_attendance:
                last_time = last_attendance.check_out_time or last_attendance.check_in_time
                if (current_time - last_time).total_seconds() < 600:
                    return

            if erp_attendance.check_recent_attendance(user.employee_id, minutes=10):
                return

            status = 'present'
            if current_time.hour > 8 or (current_time.hour == 8 and current_time.minute > 30):
                status = 'late'

            new_attendance = Attendance(
                user_id=user_id,
                check_in_time=current_time,
                date=today,
                status=status
            )
            db.session.add(new_attendance)
            db.session.commit()

            from backend.routes._helpers import get_runtime_location, save_attendance_location
            runtime_loc = get_runtime_location()
            if runtime_loc:
                try:
                    save_attendance_location(new_attendance.id, runtime_loc, source='checkin_runtime')
                except Exception as loc_exc:
                    db.session.rollback()
                    print(f"Auto attendance location save error: {loc_exc}")

            erp_attendance.create_attendance_record(
                employee_id=user.employee_id,
                attendance_time=current_time
            )
        except Exception as e:
            print(f"Auto attendance error: {e}")

    # ── Initialize database & services ───────────────────────────────────

    with app.app_context():
        db.create_all()
        ensure_database_schema()
        load_known_faces()
        attendance_service_ref[0] = AttendanceService(
            app, face_recognizer, perform_auto_attendance
        )

    # ── Register route blueprints ────────────────────────────────────────

    from backend.routes import register_all_blueprints
    register_all_blueprints(app)

    # ── Health check (stays here — tiny) ─────────────────────────────────

    @app.route('/api/health')
    def health():
        return jsonify({'status': 'ok', 'timestamp': datetime.now().isoformat()})

    # ── Serve React frontend (production) ────────────────────────────────

    if getattr(sys, 'frozen', False):
        _base = sys._MEIPASS
    else:
        _base = os.path.dirname(os.path.dirname(__file__))
    _dist_dir = os.path.join(_base, 'frontend_dist')
    print(f'[FaceCheck] frozen={getattr(sys, "frozen", False)}, _base={_base}, _dist_dir={_dist_dir}, exists={os.path.isdir(_dist_dir)}')

    @app.route('/api/debug_paths')
    def debug_paths():
        return jsonify({
            'frozen': getattr(sys, 'frozen', False),
            '_base': _base,
            '_dist_dir': _dist_dir,
            'dist_exists': os.path.isdir(_dist_dir),
            'cwd': os.getcwd(),
            'exe': sys.executable,
        })

    if os.path.isdir(_dist_dir):
        @app.route('/')
        def serve_index():
            return send_from_directory(_dist_dir, 'index.html')

        @app.route('/assets/<path:filename>')
        def serve_assets(filename):
            return send_from_directory(os.path.join(_dist_dir, 'assets'), filename)

        @app.errorhandler(404)
        def fallback(e):
            return send_from_directory(_dist_dir, 'index.html')
    else:
        @app.route('/')
        def backend_root_info():
            return jsonify({
                'service': 'FaceCheck API',
                'status': 'running',
                'frontend_dist_exists': False,
                'health': '/api/health',
                'debug_paths': '/api/debug_paths',
                'frontend_dev_hint': 'Run start_web.bat and open http://localhost:5173 for UI',
            })

        @app.route('/favicon.ico')
        def backend_favicon_noop():
            return ('', 204)

    return app


if __name__ == '__main__':
    app = create_app()
    app.run(host='0.0.0.0', port=5000, debug=False)
