import numpy as np


class SimpleTracker:
    """IOU-based tracker with bbox smoothing and embedding identity verification."""

    def __init__(self, max_lost=2, iou_threshold=0.25, smoothing=0.6):
        self.next_id = 0
        self.tracks = {}
        self.max_lost = max_lost
        self.iou_threshold = iou_threshold
        self.smoothing = smoothing

    def update(self, detections, embeddings=None):
        if embeddings is None:
            embeddings = [None] * len(detections)

        if not self.tracks:
            for i, bbox in enumerate(detections):
                self._register(bbox, embeddings[i])
            return self._get_results()

        track_ids = list(self.tracks.keys())
        track_bboxes = [self.tracks[tid]['bbox'] for tid in track_ids]

        if not detections:
            for tid in track_ids:
                self.tracks[tid]['lost'] += 1
            self._clean_tracks()
            return self._get_results()

        iou_matrix = np.zeros((len(track_ids), len(detections)))
        for i, t_bbox in enumerate(track_bboxes):
            for j, d_bbox in enumerate(detections):
                iou_matrix[i, j] = self._iou(t_bbox, d_bbox)

        matched_track_set = set()
        used_detections = set()

        if iou_matrix.size > 0:
            flat_indices = np.argsort(iou_matrix.ravel())[::-1]
            for idx in flat_indices:
                t_idx, d_idx = np.unravel_index(idx, iou_matrix.shape)
                if t_idx in matched_track_set or d_idx in used_detections:
                    continue
                if iou_matrix[t_idx, d_idx] < self.iou_threshold:
                    continue

                matched_track_set.add(t_idx)
                used_detections.add(d_idx)
                tid = track_ids[t_idx]
                new_bbox = detections[d_idx]

                old_bbox = self.tracks[tid]['bbox']
                alpha = self.smoothing
                self.tracks[tid]['bbox'] = [
                    int(old * (1 - alpha) + new * alpha)
                    for old, new in zip(old_bbox, new_bbox)
                ]
                self.tracks[tid]['lost'] = 0

                new_emb = embeddings[d_idx]
                if (new_emb is not None
                        and self.tracks[tid]['checked']
                        and self.tracks[tid]['embedding'] is not None):
                    sim = self._cosine_sim(self.tracks[tid]['embedding'], new_emb)
                    if sim < 0.3:
                        self.tracks[tid]['name'] = "Unknown"
                        self.tracks[tid]['checked'] = False
                        self.tracks[tid]['is_fake'] = False

                if new_emb is not None:
                    self.tracks[tid]['embedding'] = new_emb

        for i, tid in enumerate(track_ids):
            if i not in matched_track_set:
                self.tracks[tid]['lost'] += 1

        for i, bbox in enumerate(detections):
            if i not in used_detections:
                self._register(bbox, embeddings[i])

        self._clean_tracks()
        return self._get_results()

    def _register(self, bbox, embedding=None):
        self.tracks[self.next_id] = {
            'bbox': list(bbox),
            'lost': 0,
            'name': "Unknown",
            'is_fake': False,
            'checked': False,
            'embedding': embedding
        }
        self.next_id += 1

    def _clean_tracks(self):
        self.tracks = {tid: t for tid, t in self.tracks.items() if t['lost'] <= self.max_lost}

    def _get_results(self):
        return [{
            'id': tid,
            'bbox': t['bbox'],
            'name': t['name'],
            'is_fake': t['is_fake'],
            'checked': t['checked'],
            'lost': t['lost']
        } for tid, t in self.tracks.items()]

    def update_track_info(self, track_id, name, is_fake):
        if track_id in self.tracks:
            self.tracks[track_id]['name'] = name
            self.tracks[track_id]['is_fake'] = is_fake
            self.tracks[track_id]['checked'] = True

    def get_track_embedding(self, track_id):
        if track_id in self.tracks:
            return self.tracks[track_id].get('embedding')
        return None

    def _cosine_sim(self, emb1, emb2):
        n1 = np.linalg.norm(emb1)
        n2 = np.linalg.norm(emb2)
        if n1 == 0 or n2 == 0:
            return 0.0
        return float(np.dot(emb1 / n1, emb2 / n2))

    def _iou(self, bbox1, bbox2):
        x1, y1, w1, h1 = bbox1
        x2, y2, w2, h2 = bbox2
        xA = max(x1, x2)
        yA = max(y1, y2)
        xB = min(x1 + w1, x2 + w2)
        yB = min(y1 + h1, y2 + h2)
        inter = max(0, xB - xA) * max(0, yB - yA)
        union = w1 * h1 + w2 * h2 - inter
        return inter / float(union) if union > 0 else 0
