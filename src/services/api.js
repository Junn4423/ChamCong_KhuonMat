// API is always same-origin: in dev via Vite proxy, in production served from Flask
const API_BASE = '';

// Simple token-based auth (stored in memory, survives page navigation via hash router)
let adminToken = localStorage.getItem('adminToken') || null;

export function setAdminToken(token) {
  adminToken = token;
  if (token) localStorage.setItem('adminToken', token);
  else localStorage.removeItem('adminToken');
}

export function getAdminToken() {
  return adminToken;
}

async function request(path, options = {}) {
  const url = `${API_BASE}${path}`;
  const headers = { ...(options.headers || {}) };
  if (adminToken) {
    headers['X-Admin-Token'] = adminToken;
  }
  const res = await fetch(url, {
    ...options,
    headers,
  });
  if (!res.ok) {
    // Try to parse JSON error body for 4xx/5xx
    try {
      const body = await res.json();
      return body; // Return the error JSON (e.g. {success: false, message: '...'})
    } catch {
      throw new Error(`HTTP ${res.status}: ${res.statusText}`);
    }
  }
  return res.json();
}

export const api = {
  // Health
  health: () => request('/api/health'),

  // Auth
  login: (password) => request('/api/admin_login', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ password }),
  }),
  logout: () => request('/api/admin_logout', { method: 'POST' }),
  authStatus: () => request('/api/auth_status'),

  // Registration
  register: (formData) => request('/api/register', {
    method: 'POST',
    body: formData,
  }),
  registerBase64: (data) => request('/api/register_base64', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(data),
  }),
  registerFromErp: (employeeId) => request('/api/register_from_erp', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ employee_id: employeeId }),
  }),

  // Camera
  startCamera: (data) => request('/api/start_camera', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(data),
  }),
  stopCamera: () => request('/api/stop_camera', { method: 'POST' }),
  cameraStatus: () => request('/api/camera_status'),

  // Attendance
  checkAttendance: (userId) => request('/api/check_attendance', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ user_id: userId }),
  }),
  attendanceImage: (formData) => request('/api/attendance_image', {
    method: 'POST',
    body: formData,
  }),
  attendanceImageBase64: (data) => request('/api/attendance_image', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(data),
  }),

  // Stats & Data
  getStats: () => request('/api/get_attendance_stats'),
  getRecentActivity: () => request('/api/get_recent_activity'),
  getTodayAttendance: () => request('/api/get_today_attendance'),
  getEmployees: () => request('/api/employees'),
  getReport: (date) => request(`/api/report?date=${date}`),

  // ERP
  getErpEmployeeInfo: (employeeId) => request(`/api/erp_employee_info?employee_id=${employeeId}`),

  // Admin
  getAdminEmployees: () => request('/api/admin/employees'),
  updateFace: (formData) => request('/api/admin/update_face', {
    method: 'POST',
    body: formData,
  }),
  updateFaceBase64: (data) => request('/api/admin/update_face', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(data),
  }),
  deleteEmployee: (userId) => request(`/api/admin/delete_employee/${userId}`, {
    method: 'DELETE',
  }),
  clearFace: (userId) => request(`/api/admin/clear_face/${userId}`, {
    method: 'POST',
  }),
  reloadFromErp: (employeeId) => request('/api/admin/reload_from_erp', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ employee_id: employeeId }),
  }),

  // Video feed URL
  videoFeedUrl: () => `${API_BASE}/video_feed`,
};
