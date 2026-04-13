/**
 * Camera module — start/stop camera, status, manage camera configs.
 */
import { request, API_BASE } from '../request'

export const cameraApi = {
  startCamera: (data) => request('/api/start_camera', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(data),
  }),

  stopCamera: () => request('/api/stop_camera', { method: 'POST' }),

  cameraStatus: () => request('/api/camera_status'),

  getCameras: () => request('/api/cameras'),

  saveCamera: (camera) => request('/api/cameras', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(camera),
  }),

  deleteCamera: (cameraId) => request(`/api/cameras/${encodeURIComponent(cameraId)}`, {
    method: 'DELETE',
  }),

  videoFeedUrl: () => `${API_BASE}/video_feed`,
}
