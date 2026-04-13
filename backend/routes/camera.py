# -*- coding: utf-8 -*-
"""Camera routes — start/stop camera, CRUD configs.  (maps to frontend modules/camera.js)"""

import time
import cv2
from flask import Blueprint, request, jsonify, Response

from backend.config import CAMERA_CONFIG
from backend.services.camera_registry import camera_registry, CameraRegistryError
from backend.routes._state import state
from backend.routes._helpers import admin_required, get_runtime_location, location_to_text

camera_bp = Blueprint('camera', __name__)


def _generate_frames():
    while True:
        try:
            svc = state.attendance_service_ref[0]
            if not svc:
                time.sleep(1)
                continue
            frame = svc.get_processed_frame()
            if frame is None:
                time.sleep(0.1)
                continue
            jpeg_quality = int(svc.get_stream_jpeg_quality()) if hasattr(svc, 'get_stream_jpeg_quality') else 80
            ret, buffer = cv2.imencode('.jpg', frame, [int(cv2.IMWRITE_JPEG_QUALITY), jpeg_quality])
            if not ret:
                time.sleep(0.1)
                continue
            frame_bytes = buffer.tobytes()
            yield (b'--frame\r\n'
                   b'Content-Type: image/jpeg\r\n\r\n' + frame_bytes + b'\r\n')
        except GeneratorExit:
            return
        except Exception as e:
            print(f"Frame generation error: {e}")
            time.sleep(0.1)


@camera_bp.route('/video_feed')
def video_feed():
    return Response(_generate_frames(),
                    mimetype='multipart/x-mixed-replace; boundary=frame')


@camera_bp.route('/api/start_camera', methods=['POST'])
def start_camera():
    svc = state.attendance_service_ref[0]
    if not svc:
        return jsonify({'success': False, 'message': 'Service not initialized'})

    data = request.get_json(silent=True) or {}
    camera_id = (data.get('camera_id') or '').strip()

    try:
        profile = camera_registry.get_camera(camera_id) if camera_id else camera_registry.get_default_camera()
    except CameraRegistryError as exc:
        return jsonify({'success': False, 'message': exc.args[0]}), 500

    profile = profile or {}
    camera_type = (data.get('camera_type') or profile.get('camera_type') or 'rtsp').strip().lower()
    device_index = data.get('device_index', profile.get('device_index', 0))
    rtsp_url = (data.get('rtsp_url') or profile.get('rtsp_url') or '').strip()

    if camera_type not in ['rtsp', 'device']:
        return jsonify({'success': False, 'message': 'camera_type không hợp lệ'})
    if camera_type == 'rtsp' and not rtsp_url:
        return jsonify({'success': False, 'message': 'Thiếu rtsp_url cho camera RTSP'}), 400

    camera_options = dict(profile.get('camera_options') or {})
    camera_options.update(data.get('camera_options') or {})
    processing_options = dict(profile.get('processing_options') or {})
    processing_options.update(data.get('processing_options') or {})

    started = svc.start(
        camera_type=camera_type,
        device_index=device_index,
        rtsp_url=rtsp_url,
        camera_options=camera_options,
        processing_options=processing_options,
    )
    if not started:
        return jsonify({'success': False, 'message': 'Không mở được camera'})

    crs = state.camera_runtime_state
    crs['camera_id'] = profile.get('id') if profile else None
    crs['camera_name'] = profile.get('name') if profile else None
    return jsonify({
        'success': True,
        'message': 'Đã bật camera',
        'camera_id': crs.get('camera_id'),
        'camera_name': crs.get('camera_name'),
        'camera_type': camera_type,
        'camera_options': camera_options,
        'processing_options': processing_options,
    })


@camera_bp.route('/api/stop_camera', methods=['POST'])
def stop_camera():
    svc = state.attendance_service_ref[0]
    if svc:
        svc.stop()
        return jsonify({'success': True, 'message': 'Đã tắt camera'})
    return jsonify({'success': False, 'message': 'Service not initialized'})


@camera_bp.route('/api/camera_status')
def camera_status():
    svc = state.attendance_service_ref[0]
    running = svc.is_running if svc else False
    runtime_status = svc.get_runtime_status() if (svc and hasattr(svc, 'get_runtime_status')) else {}
    latest_location = get_runtime_location(max_age_seconds=86400)
    crs = state.camera_runtime_state
    return jsonify({
        'success': True,
        'running': running,
        'default_camera_type': CAMERA_CONFIG.get('default_camera_type', 'rtsp'),
        'default_device_index': CAMERA_CONFIG.get('device_index', 0),
        'camera_id': crs.get('camera_id'),
        'camera_name': crs.get('camera_name'),
        'location_enabled': bool(crs.get('location_enabled')),
        'latest_location': latest_location,
        'latest_location_text': location_to_text(latest_location) if latest_location else '',
        'runtime': runtime_status,
    })


@camera_bp.route('/api/cameras')
@admin_required
def list_cameras():
    try:
        cameras = camera_registry.list_cameras()
        return jsonify({'success': True, 'cameras': cameras})
    except CameraRegistryError as exc:
        return jsonify({'success': False, 'message': exc.args[0], 'cameras': []}), 500


@camera_bp.route('/api/cameras', methods=['POST'])
@admin_required
def upsert_camera():
    data = request.get_json(silent=True) or {}
    try:
        camera, created = camera_registry.upsert_camera(data)
        return jsonify({'success': True, 'camera': camera, 'created': created})
    except CameraRegistryError as exc:
        return jsonify({'success': False, 'message': exc.args[0]}), 400
    except Exception as exc:
        return jsonify({'success': False, 'message': str(exc)}), 500


@camera_bp.route('/api/cameras/<camera_id>', methods=['DELETE'])
@admin_required
def delete_camera(camera_id):
    try:
        deleted = camera_registry.delete_camera(camera_id)
        if not deleted:
            return jsonify({'success': False, 'message': 'Không tìm thấy camera'}), 404
        return jsonify({'success': True, 'message': 'Đã xóa camera'})
    except CameraRegistryError as exc:
        return jsonify({'success': False, 'message': exc.args[0]}), 400
    except Exception as exc:
        return jsonify({'success': False, 'message': str(exc)}), 500
