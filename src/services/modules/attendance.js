/**
 * Attendance module — check attendance, stats, today records, reports.
 */
import { request } from '../request'

export const attendanceApi = {
  checkAttendance: (userId, location = null) => request('/api/check_attendance', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ user_id: userId, location }),
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

  getStats: () => request('/api/get_attendance_stats'),

  getSystemStorageStats: () => request('/api/system_storage_stats'),

  getRecentActivity: () => request('/api/get_recent_activity'),

  getTodayAttendance: () => request('/api/get_today_attendance'),

  getReport: (date) => request(`/api/report?date=${date}`),
}
