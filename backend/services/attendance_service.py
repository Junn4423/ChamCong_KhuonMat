import cv2
import numpy as np
import threading
import time
import queue
import os
from datetime import datetime, date
from backend.config import CAMERA_CONFIG, PERFORMANCE_CONFIG, MOTION_CONFIG
from backend.services.tracking_service import SimpleTracker


class MotionDetector:
    def __init__(self, threshold=25, min_area=500):
        self.threshold = threshold
        self.min_area = min_area
        self.bg_subtractor = cv2.createBackgroundSubtractorMOG2(
            history=500, varThreshold=25, detectShadows=True
        )

    def detect(self, frame):
        fg_mask = self.bg_subtractor.apply(frame)
        _, fg_mask = cv2.threshold(fg_mask, 250, 255, cv2.THRESH_BINARY)
        kernel = cv2.getStructuringElement(cv2.MORPH_ELLIPSE, (3, 3))
        fg_mask = cv2.morphologyEx(fg_mask, cv2.MORPH_OPEN, kernel)
        contours, _ = cv2.findContours(fg_mask, cv2.RETR_EXTERNAL, cv2.CHAIN_APPROX_SIMPLE)
        for contour in contours:
            if cv2.contourArea(contour) > self.min_area:
                return True
        return False


def ai_worker(input_queue, result_queue, initial_known_faces, engine, config_overrides=None):
    """Worker thread for AI detection, anti-spoofing, and recognition."""
    tracker = SimpleTracker(max_lost=2, iou_threshold=0.25, smoothing=0.6)

    known_face_encodings = initial_known_faces.get('encodings', np.empty((0, 512)))
    known_face_names = initial_known_faces.get('names', [])
    known_face_ids = initial_known_faces.get('ids', [])

    while True:
        try:
            try:
                task_item = input_queue.get(timeout=1)
            except (KeyboardInterrupt, queue.Empty):
                continue

            if task_item is None:
                break

            if isinstance(task_item, dict) and task_item.get('cmd') == 'update_faces':
                known_face_encodings = task_item.get('encodings', np.empty((0, 512)))
                known_face_names = task_item.get('names', [])
                known_face_ids = task_item.get('ids', [])
                continue

            frame_idx, frame = task_item

            h, w = frame.shape[:2]
            target_w = PERFORMANCE_CONFIG.get('detection_width', 1280)
            scale = 1.0
            if w > target_w:
                scale = target_w / w
                new_h = int(h * scale)
                small_frame = cv2.resize(frame, (target_w, new_h))
            else:
                small_frame = frame

            results = engine.detect_and_encode(small_frame)

            detections = []
            det_embeddings = []
            for res in results:
                bbox = res['bbox']
                if scale != 1.0:
                    bbox = bbox / scale
                    res['bbox'] = bbox
                x = int(bbox[0])
                y = int(bbox[1])
                w_box = int(bbox[2] - bbox[0])
                h_box = int(bbox[3] - bbox[1])
                detections.append([x, y, w_box, h_box])
                det_embeddings.append(res['embedding'])

            tracks = tracker.update(detections, det_embeddings)
            final_results = []

            for track in tracks:
                track_id = track['id']
                bbox = track['bbox']

                if not track['checked']:
                    embedding = tracker.get_track_embedding(track_id)
                    if embedding is not None and bbox[2] > 50 and bbox[3] > 50:
                        try:
                            is_real, score = engine.check_anti_spoofing(frame, bbox)
                        except Exception as e:
                            print(f"Anti-spoofing check failed: {e}")
                            is_real, score = True, 1.0
                        if not is_real:
                            tracker.update_track_info(track_id, "Fake", True)
                        else:
                            name = "Unknown"
                            user_id = None
                            min_dist = 0.0

                            if len(known_face_encodings) > 0:
                                curr_emb = embedding / np.linalg.norm(embedding)
                                similarities = np.dot(known_face_encodings, curr_emb)
                                best_match_index = np.argmax(similarities)
                                best_sim = similarities[best_match_index]

                                if best_sim > 0.4:
                                    name = known_face_names[best_match_index]
                                    user_id = known_face_ids[best_match_index]
                                    min_dist = 1.0 - best_sim

                            tracker.update_track_info(track_id, name, False)

                            if user_id:
                                final_results.append({
                                    'type': 'attendance',
                                    'user_id': user_id,
                                    'name': name,
                                    'dist': min_dist,
                                    'encoding': embedding
                                })

                final_results.append({
                    'type': 'display',
                    'bbox': bbox,
                    'name': track['name'],
                    'track_id': track_id,
                    'lost': track.get('lost', 0)
                })

            result_queue.put(final_results)

        except Exception as e:
            print(f"AI Worker Error: {e}")
            import traceback
            traceback.print_exc()


class Camera:
    def __init__(self, camera_type='rtsp', device_index=0, rtsp_url=None, options=None):
        self.camera_type = camera_type
        self.device_index = int(device_index) if str(device_index).isdigit() else 0
        self.rtsp_url = rtsp_url or CAMERA_CONFIG.get('rtsp_url')
        self.flip_mode = CAMERA_CONFIG.get('flip_mode', 'auto')
        self.options = options or {}
        self.source = self.rtsp_url if self.camera_type == 'rtsp' else self.device_index
        self.video = None
        self.is_running = True
        self.is_connected = False
        self.lock = threading.Lock()
        self.frame = None
        self.reconnect_delay = float(self.options.get('reconnect_delay', 5) or 5)
        if self.reconnect_delay < 1:
            self.reconnect_delay = 1
        if self.reconnect_delay > 30:
            self.reconnect_delay = 30

        self._open_capture()
        self.thread = threading.Thread(target=self._reader, daemon=True)
        self.thread.start()

    def _set_capture_property(self, prop_name, value):
        if self.video is None:
            return
        prop = getattr(cv2, prop_name, None)
        if prop is None:
            return
        try:
            self.video.set(prop, value)
        except Exception:
            pass

    def _build_ffmpeg_options(self):
        transport = (self.options.get('rtsp_transport') or 'tcp').strip().lower() or 'tcp'
        low_latency = bool(self.options.get('low_latency', True))
        parts = [f'rtsp_transport;{transport}']
        if low_latency:
            parts.extend([
                'fflags;nobuffer',
                'flags;low_delay',
                'max_delay;500000',
                'probesize;32768',
                'analyzeduration;0',
            ])
        return '|'.join(parts)

    def _open_capture(self):
        if self.camera_type == 'rtsp':
            try:
                os.environ['OPENCV_FFMPEG_CAPTURE_OPTIONS'] = self._build_ffmpeg_options()
            except Exception:
                pass

        self.video = cv2.VideoCapture(self.source)
        self.is_connected = bool(self.video and self.video.isOpened())
        if not self.is_connected:
            return

        self._set_capture_property('CAP_PROP_BUFFERSIZE', int(self.options.get('buffer_size', 1)))
        self._set_capture_property('CAP_PROP_FRAME_WIDTH', int(self.options.get('frame_width', 1280)))
        self._set_capture_property('CAP_PROP_FRAME_HEIGHT', int(self.options.get('frame_height', 720)))
        self._set_capture_property('CAP_PROP_FPS', int(self.options.get('target_fps', 25)))
        self._set_capture_property('CAP_PROP_OPEN_TIMEOUT_MSEC', int(self.options.get('open_timeout_ms', 5000)))
        self._set_capture_property('CAP_PROP_READ_TIMEOUT_MSEC', int(self.options.get('read_timeout_ms', 5000)))

    def _apply_flip(self, frame):
        mode = self.flip_mode
        if mode == 'auto':
            mode = 'horizontal' if self.camera_type == 'device' else 'none'
        if mode == 'horizontal':
            return cv2.flip(frame, 1)
        if mode == 'vertical':
            return cv2.flip(frame, 0)
        if mode == 'both':
            return cv2.flip(frame, -1)
        return frame

    def _reader(self):
        while self.is_running:
            if not self.is_connected:
                if self.video:
                    self.video.release()
                self._open_capture()
                if not self.is_connected:
                    time.sleep(self.reconnect_delay)
                    continue

            frame_drop_count = int(self.options.get('frame_drop_count', 0) or 0)
            if frame_drop_count > 0:
                for _ in range(frame_drop_count):
                    if not self.video.grab():
                        break

            ret, frame = self.video.read()
            if not ret:
                self.is_connected = False
                with self.lock:
                    self.frame = None
                time.sleep(1)
                continue

            frame = self._apply_flip(frame)
            with self.lock:
                self.frame = frame

        if self.video:
            self.video.release()

    def get_frame(self):
        with self.lock:
            if self.frame is None:
                return None
            return self.frame.copy()

    def is_opened(self):
        return self.is_connected

    def stop(self):
        self.is_running = False
        if hasattr(self, 'thread'):
            self.thread.join(timeout=3)


class AttendanceService:
    def __init__(self, app, face_recognizer, perform_attendance_callback):
        self.app = app
        self.face_recognizer = face_recognizer
        self.perform_attendance_callback = perform_attendance_callback

        self.camera = None
        self.is_running = False
        self.thread = None
        self.lock = threading.Lock()
        self.processed_frame = None
        self.last_attendance_check = {}

        self.ai_process = None
        self.fps = 0
        self.latest_results = []
        self.last_ai_update_time = 0

        self.display_tracks = {}

        self.motion_detector = MotionDetector(
            threshold=MOTION_CONFIG['threshold'],
            min_area=MOTION_CONFIG['min_area']
        )
        self.last_motion_time = time.time()
        self.no_motion_delay = 2.0

        self.camera_options = {
            'frame_width': 1280,
            'frame_height': 720,
            'target_fps': 25,
            'buffer_size': 1,
            'frame_drop_count': 0,
            'low_latency': True,
            'rtsp_transport': 'tcp',
            'open_timeout_ms': 5000,
            'read_timeout_ms': 5000,
        }
        self.processing_options = {
            'fps_limit': PERFORMANCE_CONFIG['fps_limit'],
            'skip_ai_frames': 1,
            'stream_jpeg_quality': 80,
            'no_motion_delay': 2.0,
        }

        self.input_queue = queue.Queue(maxsize=2)
        self.result_queue = queue.Queue()

    @staticmethod
    def _coerce_int(value, default, min_value=None, max_value=None):
        try:
            val = int(value)
        except (TypeError, ValueError):
            val = int(default)
        if min_value is not None:
            val = max(min_value, val)
        if max_value is not None:
            val = min(max_value, val)
        return val

    @staticmethod
    def _coerce_float(value, default, min_value=None, max_value=None):
        try:
            val = float(value)
        except (TypeError, ValueError):
            val = float(default)
        if min_value is not None:
            val = max(min_value, val)
        if max_value is not None:
            val = min(max_value, val)
        return val

    def _normalize_camera_options(self, camera_options, camera_type):
        src = camera_options or {}
        normalized = {
            'frame_width': self._coerce_int(src.get('frame_width', 1280), 1280, 320, 3840),
            'frame_height': self._coerce_int(src.get('frame_height', 720), 720, 240, 2160),
            'target_fps': self._coerce_int(src.get('target_fps', 25), 25, 5, 60),
            'buffer_size': self._coerce_int(src.get('buffer_size', 1 if camera_type == 'rtsp' else 2), 1, 1, 16),
            'frame_drop_count': self._coerce_int(src.get('frame_drop_count', 1 if camera_type == 'rtsp' else 0), 0, 0, 8),
            'low_latency': bool(src.get('low_latency', True)),
            'rtsp_transport': (src.get('rtsp_transport') or 'tcp'),
            'open_timeout_ms': self._coerce_int(src.get('open_timeout_ms', 5000), 5000, 1000, 30000),
            'read_timeout_ms': self._coerce_int(src.get('read_timeout_ms', 5000), 5000, 1000, 30000),
        }
        return normalized

    def _normalize_processing_options(self, processing_options):
        src = processing_options or {}
        normalized = {
            'fps_limit': self._coerce_int(src.get('fps_limit', PERFORMANCE_CONFIG['fps_limit']), PERFORMANCE_CONFIG['fps_limit'], 5, 60),
            'skip_ai_frames': self._coerce_int(src.get('skip_ai_frames', 1), 1, 1, 8),
            'stream_jpeg_quality': self._coerce_int(src.get('stream_jpeg_quality', 80), 80, 40, 95),
            'no_motion_delay': self._coerce_float(src.get('no_motion_delay', 2.0), 2.0, 0.0, 10.0),
        }
        return normalized

    def get_stream_jpeg_quality(self):
        return int(self.processing_options.get('stream_jpeg_quality', 80))

    def get_runtime_status(self):
        return {
            'camera_options': dict(self.camera_options),
            'processing_options': dict(self.processing_options),
            'no_motion_delay': self.no_motion_delay,
            'camera_type': getattr(self.camera, 'camera_type', None),
            'camera_source': getattr(self.camera, 'source', None),
        }

    def start(self, camera_type=None, device_index=None, rtsp_url=None, camera_options=None, processing_options=None):
        if self.is_running:
            return True

        resolved_camera_type = camera_type or CAMERA_CONFIG.get('default_camera_type', 'rtsp')
        resolved_device_index = CAMERA_CONFIG.get('device_index', 0) if device_index is None else device_index
        resolved_rtsp_url = rtsp_url or CAMERA_CONFIG.get('rtsp_url')
        resolved_camera_options = self._normalize_camera_options(camera_options, resolved_camera_type)
        resolved_processing_options = self._normalize_processing_options(processing_options)
        self.camera_options = resolved_camera_options
        self.processing_options = resolved_processing_options
        self.no_motion_delay = resolved_processing_options.get('no_motion_delay', 2.0)

        self.camera = Camera(
            camera_type=resolved_camera_type,
            device_index=resolved_device_index,
            rtsp_url=resolved_rtsp_url,
            options=resolved_camera_options,
        )
        if not self.camera.is_opened():
            if resolved_camera_type == 'rtsp':
                self.camera.stop()
                self.camera = Camera(
                    camera_type='device',
                    device_index=resolved_device_index,
                    rtsp_url=resolved_rtsp_url,
                    options=self._normalize_camera_options(resolved_camera_options, 'device'),
                )
            if not self.camera.is_opened():
                return False

        initial_faces = {
            'encodings': self.face_recognizer.known_face_encodings,
            'names': self.face_recognizer.known_face_names,
            'ids': self.face_recognizer.known_face_ids
        }

        self.ai_process = threading.Thread(
            target=ai_worker,
            args=(self.input_queue, self.result_queue, initial_faces, self.face_recognizer.engine),
            daemon=True
        )
        self.ai_process.start()

        self.is_running = True
        self.thread = threading.Thread(target=self._process_loop, daemon=True)
        self.thread.start()
        return True

    def update_known_faces(self, face_recognizer):
        if self.is_running and self.ai_process:
            self.face_recognizer = face_recognizer
            update_cmd = {
                'cmd': 'update_faces',
                'encodings': face_recognizer.known_face_encodings,
                'names': face_recognizer.known_face_names,
                'ids': face_recognizer.known_face_ids
            }
            if not self.input_queue.full():
                self.input_queue.put(update_cmd)

    def stop(self):
        self.is_running = False
        if self.camera:
            self.camera.stop()
        if self.ai_process:
            try:
                if not self.input_queue.full():
                    self.input_queue.put_nowait(None)
                else:
                    try:
                        self.input_queue.get_nowait()
                    except Exception:
                        pass
                    self.input_queue.put_nowait(None)
            except Exception:
                pass
            self.ai_process.join(timeout=3)
        if self.thread:
            self.thread.join(timeout=3)

    def get_processed_frame(self):
        with self.lock:
            if self.processed_frame is None:
                if self.camera:
                    return self.camera.get_frame()
                return None
            return self.processed_frame.copy()

    def _process_loop(self):
        frame_count = 0

        while self.is_running:
            loop_start = time.time()

            try:
                if not self.camera or not self.camera.is_opened():
                    time.sleep(1)
                    continue

                frame = self.camera.get_frame()
                if frame is None:
                    time.sleep(0.01)
                    continue

                frame_count += 1

                small_frame = cv2.resize(frame, (0, 0), fx=0.5, fy=0.5)
                has_motion = self.motion_detector.detect(small_frame)

                if has_motion:
                    self.last_motion_time = time.time()

                is_device_camera = bool(self.camera and getattr(self.camera, 'camera_type', '') == 'device')
                should_process_ai = is_device_camera or (time.time() - self.last_motion_time) < self.no_motion_delay

                if should_process_ai:
                    skip_ai_frames = max(1, int(self.processing_options.get('skip_ai_frames', 1)))
                    if frame_count % skip_ai_frames == 0 and not self.input_queue.full():
                        self.input_queue.put((frame_count, frame))
                else:
                    self.latest_results = []
                    self.display_tracks.clear()

                try:
                    while not self.result_queue.empty():
                        results = self.result_queue.get_nowait()
                        self.latest_results = results
                        self.last_ai_update_time = time.time()

                        active_ids = set()
                        for res in results:
                            if res.get('type') == 'display':
                                tid = res['track_id']
                                lost = res.get('lost', 0)
                                if lost <= 1:
                                    active_ids.add(tid)
                                    if tid in self.display_tracks:
                                        self.display_tracks[tid]['target_bbox'] = list(res['bbox'])
                                        self.display_tracks[tid]['name'] = res['name']
                                        self.display_tracks[tid]['last_update'] = time.time()
                                    else:
                                        self.display_tracks[tid] = {
                                            'bbox': list(res['bbox']),
                                            'target_bbox': list(res['bbox']),
                                            'name': res['name'],
                                            'last_update': time.time()
                                        }

                        for tid in list(self.display_tracks.keys()):
                            if tid not in active_ids:
                                del self.display_tracks[tid]

                        current_time = time.time()
                        for res in results:
                            if res.get('type') == 'attendance':
                                user_id = res['user_id']
                                dist = res['dist']
                                encoding = res['encoding']

                                if user_id not in self.last_attendance_check or current_time - self.last_attendance_check[user_id] > 600:
                                    with self.app.app_context():
                                        try:
                                            self.perform_attendance_callback(user_id, encoding, dist)
                                        except Exception as e:
                                            print(f"Attendance callback error: {e}")
                                    self.last_attendance_check[user_id] = current_time
                except queue.Empty:
                    pass

                if time.time() - self.last_ai_update_time > 2.0:
                    self.display_tracks.clear()

                display_alpha = 0.35
                face_locations = []
                face_names = []

                for tid, dt in list(self.display_tracks.items()):
                    if time.time() - dt['last_update'] > 1.5:
                        del self.display_tracks[tid]
                        continue

                    target = dt['target_bbox']
                    current = dt['bbox']
                    dt['bbox'] = [
                        int(c + (t - c) * display_alpha)
                        for c, t in zip(current, target)
                    ]

                    bbox = dt['bbox']
                    top = bbox[1]
                    left = bbox[0]
                    bottom = bbox[1] + bbox[3]
                    right = bbox[0] + bbox[2]
                    face_locations.append((top, right, bottom, left))
                    face_names.append(dt['name'] or 'Unknown')

                try:
                    frame = self.face_recognizer.draw_faces_on_frame(frame, face_locations, face_names)
                except Exception as e:
                    print(f"Draw faces error: {e}")

                with self.lock:
                    self.processed_frame = frame

            except Exception as e:
                print(f"Process loop error: {e}")
                import traceback
                traceback.print_exc()
                time.sleep(0.1)

            elapsed = time.time() - loop_start
            fps_limit = max(1, int(self.processing_options.get('fps_limit', PERFORMANCE_CONFIG['fps_limit'])))
            sleep_time = max(0, (1.0 / fps_limit) - elapsed)
            if sleep_time > 0:
                time.sleep(sleep_time)
