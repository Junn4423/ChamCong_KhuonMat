import cv2
import numpy as np
import insightface
from insightface.app import FaceAnalysis
import os
import sys

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
    candidate_dirs = [model_dir]
    resource_dir = os.path.join(PROJECT_ROOT, 'Silent-Face-Anti-Spoofing', 'resources', 'anti_spoof_models')
    fallback_dir = os.path.join(PROJECT_ROOT, 'models')
    for path in (resource_dir, fallback_dir):
        if path not in candidate_dirs:
            candidate_dirs.append(path)
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
        import anti_spoof_predict

        self.cropper = CropImage()
        self.anti_spoof = anti_spoof_predict.AntiSpoofPredict(device_id)
        self.model_paths = []
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

    def is_real_face(self, frame, bbox):
        prediction = np.zeros((1, 3), dtype=np.float32)
        for model_path in self.model_paths:
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
            prediction += self.anti_spoof.predict(img, model_path)
        return build_prediction_score(prediction, len(self.model_paths))


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

    def detect_and_encode(self, frame):
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
        return self.anti_spoofing.is_real_face(frame, bbox_xywh)

    def compute_sim(self, feat1, feat2):
        return np.dot(feat1, feat2) / (np.linalg.norm(feat1) * np.linalg.norm(feat2))
