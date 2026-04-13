import cv2
import numpy as np
import os
import io
import base64
from PIL import Image, ImageDraw, ImageFont
from .insightface_module import InsightFaceWrapper
from backend.config import PERFORMANCE_CONFIG
from backend.face_encoding_utils import normalize_face_encodings


class FaceRecognition:
    def __init__(self, det_thresh=0.5, det_size=None):
        self.known_face_encodings = []
        self.known_face_names = []
        self.known_face_ids = []
        self.engine = InsightFaceWrapper(det_thresh=det_thresh, det_size=det_size)

    def load_known_faces(self, users):
        self.known_face_encodings = []
        self.known_face_names = []
        self.known_face_ids = []

        temp_encodings = []

        for user in users:
            for encoding in normalize_face_encodings(user.face_encoding):
                enc_arr = np.array(encoding)
                if enc_arr.shape == (512,):
                    temp_encodings.append(enc_arr)
                    self.known_face_names.append(user.name)
                    self.known_face_ids.append(user.id)

        if len(temp_encodings) > 0:
            self.known_face_encodings = np.array(temp_encodings)
            norms = np.linalg.norm(self.known_face_encodings, axis=1)
            self.known_face_encodings = self.known_face_encodings / norms[:, np.newaxis]
        else:
            self.known_face_encodings = np.empty((0, 512))

    def encode_face_from_image(self, image_file):
        if isinstance(image_file, (str, os.PathLike)):
            image = cv2.imread(str(image_file))
        else:
            file_bytes = np.asarray(bytearray(image_file.read()), dtype=np.uint8)
            image = cv2.imdecode(file_bytes, cv2.IMREAD_COLOR)

        if image is None:
            return None, "Khong the doc file anh"

        results = self.engine.detect_and_encode(image)

        if len(results) == 0:
            h, w = image.shape[:2]
            return None, f"Khong tim thay khuon mat trong anh (kich thuoc: {w}x{h})"

        if len(results) > 1:
            results.sort(
                key=lambda x: (x['bbox'][2] - x['bbox'][0]) * (x['bbox'][3] - x['bbox'][1]),
                reverse=True,
            )

        return results[0]['embedding'], None

    def encode_face_from_base64(self, base64_string):
        try:
            if ',' in base64_string:
                base64_string = base64_string.split(',')[1]

            image_bytes = base64.b64decode(base64_string)
            image_array = np.frombuffer(image_bytes, np.uint8)
            image = cv2.imdecode(image_array, cv2.IMREAD_COLOR)

            if image is None:
                return None, "Khong the giai ma anh base64"

            results = self.engine.detect_and_encode(image)

            if len(results) == 0:
                h, w = image.shape[:2]
                return None, f"Khong tim thay khuon mat trong anh (kich thuoc: {w}x{h})"

            if len(results) > 1:
                results.sort(
                    key=lambda x: (x['bbox'][2] - x['bbox'][0]) * (x['bbox'][3] - x['bbox'][1]),
                    reverse=True,
                )

            return results[0]['embedding'], None

        except Exception as e:
            return None, f"Lỗi xử lý ảnh base64: {str(e)}"

    def compute_distance(self, face_encodings, face_to_compare):
        if len(face_encodings) == 0:
            return np.empty((0))

        face_to_compare = np.array(face_to_compare)
        norm_to_compare = np.linalg.norm(face_to_compare)

        if norm_to_compare == 0:
            return np.ones(len(face_encodings))

        normalized_to_compare = face_to_compare / norm_to_compare
        similarities = np.dot(face_encodings, normalized_to_compare)
        distances = 1.0 - similarities
        return distances

    def compare_faces(self, known_face_encodings, face_encoding_to_check, tolerance=0.6):
        distances = self.compute_distance(known_face_encodings, face_encoding_to_check)
        return list(distances <= tolerance)

    def recognize_face_from_frame(self, frame):
        results = self.engine.detect_and_encode(frame)

        face_locations = []
        face_names = []
        face_ids = []
        face_encodings = []
        min_distances = []

        for res in results:
            bbox = res['bbox']
            embedding = res['embedding']

            face_loc = (bbox[1], bbox[2], bbox[3], bbox[0])
            face_locations.append(face_loc)
            face_encodings.append(embedding)

            name = "Unknown"
            user_id = None
            min_dist = 0.0

            if len(self.known_face_encodings) > 0:
                known_encodings = self.known_face_encodings
                curr_emb = embedding / np.linalg.norm(embedding)
                similarities = np.dot(known_encodings, curr_emb)
                best_match_index = np.argmax(similarities)
                best_sim = similarities[best_match_index]

                if best_sim > 0.4:
                    name = self.known_face_names[best_match_index]
                    user_id = self.known_face_ids[best_match_index]
                    min_dist = 1.0 - best_sim
                else:
                    min_dist = 1.0 - best_sim

            face_names.append(name)
            face_ids.append(user_id)
            min_distances.append(min_dist)

        return face_locations, face_names, face_ids, face_encodings, min_distances

    def draw_faces_on_frame(self, frame, face_locations, face_names):
        if not face_locations:
            return frame

        try:
            rgb_frame = cv2.cvtColor(frame, cv2.COLOR_BGR2RGB)
            pil_image = Image.fromarray(rgb_frame)
            draw = ImageDraw.Draw(pil_image)

            try:
                font_path = os.path.join(os.path.dirname(__file__), '..', '..', 'static', 'fonts', 'DejaVuSans.ttf')
                font = ImageFont.truetype(font_path, 40)
            except (IOError, OSError):
                font = ImageFont.load_default()

            for (top, right, bottom, left), name in zip(face_locations, face_names):
                name = name or 'Unknown'
                color = (0, 255, 0) if name != "Unknown" else (0, 0, 255)
                draw.rectangle(((left, top), (right, bottom)), outline=color, width=2)

                try:
                    text_bbox = draw.textbbox((left, bottom), name, font=font)
                    text_width = text_bbox[2] - text_bbox[0]
                    text_height = text_bbox[3] - text_bbox[1]
                except Exception:
                    text_width = len(name) * 20
                    text_height = 30
                draw.rectangle(((left, bottom), (left + text_width + 4, bottom + text_height + 4)), fill=color)
                draw.text((left + 6, bottom + 2), name, font=font, fill=(255, 255, 255))

            return cv2.cvtColor(np.array(pil_image), cv2.COLOR_RGB2BGR)
        except Exception as e:
            print(f"draw_faces_on_frame error: {e}")
            return frame

    @staticmethod
    def frame_to_base64(frame):
        _, buffer = cv2.imencode('.jpg', frame)
        frame_base64 = base64.b64encode(buffer).decode('utf-8')
        return f"data:image/jpeg;base64,{frame_base64}"
