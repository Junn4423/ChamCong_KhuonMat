# -*- coding: utf-8 -*-
"""Camera profile registry stored as JSON in source tree."""

from __future__ import annotations

import json
import re
from copy import deepcopy
from datetime import datetime
from pathlib import Path


class CameraRegistryError(Exception):
    pass


def _utc_now_iso():
    return datetime.utcnow().isoformat(timespec='seconds') + 'Z'


def _to_bool(value, default=False):
    if value is None:
        return bool(default)
    if isinstance(value, bool):
        return value
    if isinstance(value, (int, float)):
        return value != 0
    if isinstance(value, str):
        return value.strip().lower() in {'1', 'true', 'yes', 'on'}
    return bool(default)


def _to_int(value, default, min_value=None, max_value=None):
    try:
        val = int(value)
    except (TypeError, ValueError):
        val = int(default)
    if min_value is not None:
        val = max(min_value, val)
    if max_value is not None:
        val = min(max_value, val)
    return val


class CameraRegistry:
    def __init__(self, storage_path=None):
        base_dir = Path(__file__).resolve().parents[1]
        self.storage_path = Path(storage_path) if storage_path else (base_dir / 'data' / 'cameras.json')
        self._ensure_storage()

    def _ensure_storage(self):
        self.storage_path.parent.mkdir(parents=True, exist_ok=True)
        if self.storage_path.exists():
            return
        payload = {
            'version': 1,
            'updated_at': _utc_now_iso(),
            'cameras': [
                {
                    'id': 'device-default',
                    'name': 'Webcam Mac Dinh',
                    'camera_type': 'device',
                    'device_index': 0,
                    'rtsp_url': '',
                    'enabled': True,
                    'is_default': True,
                    'camera_options': {
                        'frame_width': 1280,
                        'frame_height': 720,
                        'target_fps': 25,
                        'buffer_size': 2,
                        'frame_drop_count': 0,
                        'low_latency': True,
                        'rtsp_transport': 'tcp',
                        'open_timeout_ms': 5000,
                        'read_timeout_ms': 5000,
                    },
                    'processing_options': {
                        'fps_limit': 25,
                        'skip_ai_frames': 1,
                        'stream_jpeg_quality': 85,
                        'no_motion_delay': 2.0,
                    },
                },
                {
                    'id': 'ezviz-rtsp',
                    'name': 'EZVIZ RTSP',
                    'camera_type': 'rtsp',
                    'device_index': 0,
                    'rtsp_url': '',
                    'enabled': True,
                    'is_default': False,
                    'camera_options': {
                        'frame_width': 1280,
                        'frame_height': 720,
                        'target_fps': 25,
                        'buffer_size': 1,
                        'frame_drop_count': 1,
                        'low_latency': True,
                        'rtsp_transport': 'tcp',
                        'open_timeout_ms': 5000,
                        'read_timeout_ms': 5000,
                    },
                    'processing_options': {
                        'fps_limit': 25,
                        'skip_ai_frames': 1,
                        'stream_jpeg_quality': 88,
                        'no_motion_delay': 1.5,
                    },
                },
            ],
        }
        self._write(payload)

    def _read(self):
        self._ensure_storage()
        try:
            raw = self.storage_path.read_text(encoding='utf-8')
            payload = json.loads(raw)
        except Exception as exc:
            raise CameraRegistryError(f'Khong doc duoc file camera: {exc}') from exc

        if not isinstance(payload, dict):
            raise CameraRegistryError('File camera khong hop le')
        cameras = payload.get('cameras')
        if not isinstance(cameras, list):
            payload['cameras'] = []
        return payload

    def _write(self, payload):
        payload = dict(payload)
        payload['updated_at'] = _utc_now_iso()
        self.storage_path.write_text(
            json.dumps(payload, ensure_ascii=False, indent=2),
            encoding='utf-8',
        )

    @staticmethod
    def _slug(text):
        source = (text or '').strip().lower()
        source = re.sub(r'[^a-z0-9]+', '-', source).strip('-')
        return source or 'camera'

    def _normalize_camera(self, item):
        if not isinstance(item, dict):
            raise CameraRegistryError('Du lieu camera khong hop le')

        camera_type = (item.get('camera_type') or 'device').strip().lower()
        if camera_type not in {'device', 'rtsp', 'browser', 'mobile'}:
            raise CameraRegistryError('camera_type khong hop le')

        name = (item.get('name') or '').strip()
        if not name:
            raise CameraRegistryError('Thieu ten camera')

        camera_id = (item.get('id') or '').strip()
        if not camera_id:
            camera_id = self._slug(name)

        camera_options_in = item.get('camera_options') or {}
        processing_options_in = item.get('processing_options') or {}

        camera_options = {
            'frame_width': _to_int(camera_options_in.get('frame_width', 1280), 1280, 320, 3840),
            'frame_height': _to_int(camera_options_in.get('frame_height', 720), 720, 240, 2160),
            'target_fps': _to_int(camera_options_in.get('target_fps', 25), 25, 5, 60),
            'buffer_size': _to_int(camera_options_in.get('buffer_size', 1 if camera_type == 'rtsp' else 2), 1, 1, 16),
            'frame_drop_count': _to_int(camera_options_in.get('frame_drop_count', 1 if camera_type == 'rtsp' else 0), 0, 0, 8),
            'low_latency': _to_bool(camera_options_in.get('low_latency', True)),
            'rtsp_transport': ((camera_options_in.get('rtsp_transport') or 'tcp').strip().lower() or 'tcp'),
            'open_timeout_ms': _to_int(camera_options_in.get('open_timeout_ms', 5000), 5000, 1000, 30000),
            'read_timeout_ms': _to_int(camera_options_in.get('read_timeout_ms', 5000), 5000, 1000, 30000),
            'facing_mode': ((camera_options_in.get('facing_mode') or 'user').strip().lower() or 'user'),
            'preview_mirror': _to_bool(
                camera_options_in.get('preview_mirror', camera_type in {'browser', 'mobile'}),
                camera_type in {'browser', 'mobile'},
            ),
        }

        if camera_options['facing_mode'] not in {'user', 'environment', 'any'}:
            camera_options['facing_mode'] = 'user'

        processing_options = {
            'fps_limit': _to_int(processing_options_in.get('fps_limit', 25), 25, 5, 60),
            'skip_ai_frames': _to_int(processing_options_in.get('skip_ai_frames', 1), 1, 1, 8),
            'stream_jpeg_quality': _to_int(processing_options_in.get('stream_jpeg_quality', 85), 85, 40, 95),
            'no_motion_delay': float(processing_options_in.get('no_motion_delay', 2.0) or 2.0),
        }
        if processing_options['no_motion_delay'] < 0:
            processing_options['no_motion_delay'] = 0.0
        if processing_options['no_motion_delay'] > 10:
            processing_options['no_motion_delay'] = 10.0

        return {
            'id': camera_id,
            'name': name,
            'camera_type': camera_type,
            'device_index': _to_int(item.get('device_index', 0), 0, 0, 32),
            'rtsp_url': (item.get('rtsp_url') or '').strip(),
            'enabled': _to_bool(item.get('enabled', True), True),
            'is_default': _to_bool(item.get('is_default', False), False),
            'camera_options': camera_options,
            'processing_options': processing_options,
        }

    def list_cameras(self):
        payload = self._read()
        cameras = payload.get('cameras', [])
        return deepcopy(cameras)

    def get_camera(self, camera_id):
        camera_id = (camera_id or '').strip()
        if not camera_id:
            return None
        for item in self.list_cameras():
            if item.get('id') == camera_id:
                return item
        return None

    def get_default_camera(self):
        cameras = self.list_cameras()
        for item in cameras:
            if item.get('enabled') and item.get('is_default'):
                return item
        for item in cameras:
            if item.get('enabled'):
                return item
        return None

    def upsert_camera(self, camera_payload):
        normalized = self._normalize_camera(camera_payload)
        payload = self._read()
        cameras = payload.get('cameras', [])

        updated = False
        for idx, item in enumerate(cameras):
            if item.get('id') == normalized['id']:
                cameras[idx] = normalized
                updated = True
                break
        if not updated:
            cameras.append(normalized)

        if normalized.get('is_default'):
            for item in cameras:
                if item.get('id') != normalized['id']:
                    item['is_default'] = False

        payload['cameras'] = cameras
        self._write(payload)
        return normalized, (not updated)

    def delete_camera(self, camera_id):
        camera_id = (camera_id or '').strip()
        if not camera_id:
            raise CameraRegistryError('Thieu camera_id')
        payload = self._read()
        cameras = payload.get('cameras', [])
        remaining = [item for item in cameras if item.get('id') != camera_id]
        if len(remaining) == len(cameras):
            return False

        has_default = any(item.get('is_default') for item in remaining if item.get('enabled'))
        if remaining and not has_default:
            remaining[0]['is_default'] = True

        payload['cameras'] = remaining
        self._write(payload)
        return True


camera_registry = CameraRegistry()
