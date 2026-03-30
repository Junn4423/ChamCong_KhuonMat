import cv2
import numpy as np
import insightface
from insightface.app import FaceAnalysis
import os
import sys
import threading

from backend.config import INTEL_OPTIMIZATION, PERFORMANCE_CONFIG

os.environ.setdefault('KMP_DUPLICATE_LIB_OK', 'TRUE')
os.environ.setdefault('OMP_NUM_THREADS', str(INTEL_OPTIMIZATION['omp_num_threads']))
os.environ.setdefault('MKL_NUM_THREADS', str(INTEL_OPTIMIZATION['mkl_num_threads']))

if getattr(sys, 'frozen', False):
    PROJECT_ROOT = getattr(sys, '_MEIPASS', os.path.abspath(os.path.join(os.path.dirname(sys.executable), '_internal')))
else:
    # backend/models/ -> backend/ -> project root
    PROJECT_ROOT = os.path.abspath(os.path.join(os.path.dirname(__file__), '..', '..'))

ANTI_SPOOF_SRC_DIR = os.path.join(PROJECT_ROOT, 'Silent-Face-Anti-Spoofing', 'src')
if ANTI_SPOOF_SRC_DIR not in sys.path:
    sys.path.append(ANTI_SPOOF_SRC_DIR)

from generate_patches import CropImage


def parse_model_name(model_name):
    stem = os.path.splitext(os.path.basename(model_name))[0]
    info = stem.split('_')[0:-1]
    h_input, w_input = info[-1].split('x')
    model_type = stem.split('_')[-1]
    scale = None if info[0] == 'org' else float(info[0])
    return int(h_input), int(w_input), model_type, scale


def softmax(values):
    shifted = values - np.max(values, axis=1, keepdims=True)
    exp_values = np.exp(shifted)
    return exp_values / np.sum(exp_values, axis=1, keepdims=True)


def build_prediction_score(prediction, model_count):
    label = int(np.argmax(prediction))
    value = float(prediction[0][label] / max(1, model_count))
    return label == 1, value


def get_default_anti_spoof_dirs(model_dir):
    candidate_dirs = [os.path.abspath(model_dir)]
    resource_dir = os.path.join(PROJECT_ROOT, 'Silent-Face-Anti-Spoofing', 'resources', 'anti_spoof_models')
    fallback_dir = os.path.join(PROJECT_ROOT, 'models')
    for path in (resource_dir, fallback_dir):
        abs_path = os.path.abspath(path)
        if abs_path not in candidate_dirs:
            candidate_dirs.append(abs_path)
    return candidate_dirs


class OnnxAntiSpoofingWrapper:
    def __init__(self, model_dir='Silent-Face-Anti-Spoofing/resources/anti_spoof_models'):
        import onnxruntime as ort

        self.cropper = CropImage()
        available_providers = ort.get_available_providers()
        providers = [p for p in ('CPUExecutionProvider',) if p in available_providers]
        if not providers:
            providers = ['CPUExecutionProvider']

        self.sessions = []
        seen_paths = set()
        for candidate_dir in get_default_anti_spoof_dirs(model_dir):
            if not os.path.isdir(candidate_dir):
                continue
            for model_name in sorted(os.listdir(candidate_dir)):
                if not model_name.endswith('.onnx'):
                    continue
                model_path = os.path.join(candidate_dir, model_name)
                if model_path in seen_paths:
                    continue
                session = ort.InferenceSession(model_path, providers=providers)
                self.sessions.append({
                    'path': model_path,
                    'input_name': session.get_inputs()[0].name,
                    'session': session,
                })
                seen_paths.add(model_path)

        if not self.sessions:
            raise FileNotFoundError('No ONNX anti-spoof models found')

    def is_real_face(self, frame, bbox):
        prediction = np.zeros((1, 3), dtype=np.float32)
        for model_info in self.sessions:
            h_input, w_input, _model_type, scale = parse_model_name(model_info['path'])
            crop_params = {
                'org_img': frame,
                'bbox': bbox,
                'scale': scale,
                'out_w': w_input,
                'out_h': h_input,
                'crop': scale is not None,
            }
            img = self.cropper.crop(**crop_params)
            tensor = img.transpose((2, 0, 1)).astype(np.float32, copy=False)
            tensor = np.expand_dims(tensor, axis=0)
            logits = model_info['session'].run(None, {model_info['input_name']: tensor})[0]
            prediction += softmax(logits)
        return build_prediction_score(prediction, len(self.sessions))


class TorchAntiSpoofingWrapper:
    def __init__(self, model_dir='Silent-Face-Anti-Spoofing/resources/anti_spoof_models', device_id=0):
        import torch

        self.cropper = CropImage()
        self.device = torch.device("cpu")
        self.model_paths = []
        self._cached_models = {}  # Pre-loaded models to avoid reload on every predict
        seen_paths = set()
        for candidate_dir in get_default_anti_spoof_dirs(model_dir):
            if not os.path.isdir(candidate_dir):
                continue
            for model_name in sorted(os.listdir(candidate_dir)):
                if not model_name.endswith('.pth'):
                    continue
                model_path = os.path.join(candidate_dir, model_name)
                if model_path in seen_paths:
                    continue
                self.model_paths.append(model_path)
                seen_paths.add(model_path)

        if not self.model_paths:
            raise FileNotFoundError('No PyTorch anti-spoof models found')

        # Pre-load all models once at init instead of reloading on every predict
        from model_lib.MiniFASNet import MiniFASNetV1, MiniFASNetV2, MiniFASNetV1SE, MiniFASNetV2SE
        from utility import get_kernel
        _MODEL_MAPPING = {
            'MiniFASNetV1': MiniFASNetV1,
            'MiniFASNetV2': MiniFASNetV2,
            'MiniFASNetV1SE': MiniFASNetV1SE,
            'MiniFASNetV2SE': MiniFASNetV2SE
        }
        for model_path in self.model_paths:
            try:
                model_name = os.path.basename(model_path)
                h_input, w_input, model_type, _ = parse_model_name(model_path)
                kernel_size = get_kernel(h_input, w_input)
                model = _MODEL_MAPPING[model_type](conv6_kernel=kernel_size).to(self.device)
                state_dict = torch.load(model_path, map_location=self.device)
                keys = iter(state_dict)
                first_layer_name = next(keys)
                if first_layer_name.find('module.') >= 0:
                    from collections import OrderedDict
                    new_state_dict = OrderedDict()
                    for key, value in state_dict.items():
                        new_state_dict[key[7:]] = value
                    model.load_state_dict(new_state_dict)
                else:
                    model.load_state_dict(state_dict)
                model.eval()
                self._cached_models[model_path] = model
                print(f'  Loaded anti-spoof model: {model_name}')
            except Exception as e:
                print(f'  Warning: failed to load anti-spoof model {model_path}: {e}')

    def is_real_face(self, frame, bbox):
        import torch
        import torch.nn.functional as F
        from data_io import transform as trans

        prediction = np.zeros((1, 3), dtype=np.float32)
        test_transform = trans.Compose([trans.ToTensor()])
        model_count = 0
        for model_path in self.model_paths:
            model = self._cached_models.get(model_path)
            if model is None:
                continue
            h_input, w_input, _model_type, scale = parse_model_name(model_path)
            crop_params = {
                'org_img': frame,
                'bbox': bbox,
                'scale': scale,
                'out_w': w_input,
                'out_h': h_input,
                'crop': scale is not None,
            }
            img = self.cropper.crop(**crop_params)
            if img is None or img.size == 0:
                continue
            img_tensor = test_transform(img)
            img_tensor = img_tensor.unsqueeze(0).to(self.device)
            with torch.no_grad():
                result = model.forward(img_tensor)
                result = F.softmax(result, dim=1).cpu().numpy()
            prediction += result
            model_count += 1
        if model_count == 0:
            return True, 1.0
        return build_prediction_score(prediction, model_count)


class AntiSpoofingWrapper:
    def __init__(self, model_dir='Silent-Face-Anti-Spoofing/resources/anti_spoof_models', device_id=0):
        self.backend = None

        if INTEL_OPTIMIZATION['use_onnx_antispoof']:
            try:
                self.backend = OnnxAntiSpoofingWrapper(model_dir=model_dir)
                print('Anti-spoofing backend: ONNX Runtime')
            except Exception as exc:
                print(f'Warning: ONNX anti-spoofing unavailable, falling back to PyTorch: {exc}')

        if self.backend is None:
            try:
                self.backend = TorchAntiSpoofingWrapper(model_dir=model_dir, device_id=device_id)
                print('Anti-spoofing backend: PyTorch')
            except Exception as exc:
                print(f'Warning: PyTorch anti-spoofing also unavailable: {exc}')
                print('Anti-spoofing: DISABLED')

    def is_real_face(self, frame, bbox):
        if self.backend is None:
            return True, 1.0
        return self.backend.is_real_face(frame, bbox)


class InsightFaceWrapper:
    def __init__(self, device='cpu', det_thresh=0.5, det_size=None):
        if det_size is None:
            det_size = (
                PERFORMANCE_CONFIG['detection_width'],
                PERFORMANCE_CONFIG['detection_height']
            )

        providers = ['OpenVINOExecutionProvider', 'CPUExecutionProvider']
        self.app = FaceAnalysis(name='buffalo_s', providers=providers)
        self.app.prepare(ctx_id=0, det_size=det_size, det_thresh=det_thresh)

        print('InsightFace initialized with Intel optimizations')
        print(f'  Detection size: {det_size}, Threshold: {det_thresh}')

        self.anti_spoofing = AntiSpoofingWrapper()
        self._lock = threading.RLock()

    def detect_and_encode(self, frame):
        with self._lock:
            faces = self.app.get(frame)
        results = []
        for face in faces:
            results.append({
                'bbox': face.bbox.astype(int),
                'embedding': face.embedding,
                'kps': face.kps
            })
        return results

    def check_anti_spoofing(self, frame, bbox_xywh):
        # Validate bbox before passing to anti-spoofing
        x, y, w, h = bbox_xywh[0], bbox_xywh[1], bbox_xywh[2], bbox_xywh[3]
        if w <= 0 or h <= 0:
            return True, 1.0
        frame_h, frame_w = frame.shape[:2]
        # Clamp to frame bounds
        x = max(0, min(x, frame_w - 1))
        y = max(0, min(y, frame_h - 1))
        w = min(w, frame_w - x)
        h = min(h, frame_h - y)
        if w <= 10 or h <= 10:
            return True, 1.0
        safe_bbox = [int(x), int(y), int(w), int(h)]
        try:
            with self._lock:
                return self.anti_spoofing.is_real_face(frame, safe_bbox)
        except Exception as e:
            print(f'Anti-spoofing error: {e}')
            return True, 1.0

    def compute_sim(self, feat1, feat2):
        return np.dot(feat1, feat2) / (np.linalg.norm(feat1) * np.linalg.norm(feat2))
