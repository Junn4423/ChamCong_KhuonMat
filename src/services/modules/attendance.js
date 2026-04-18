/**
 * Attendance module — check attendance, stats, today records, reports.
 */
import { request, requestBlob } from '../request'

function buildReportQuery(input) {
  const params = new URLSearchParams()

  if (typeof input === 'string') {
    if (input) params.set('date', input)
    return params.toString()
  }

  const source = input && typeof input === 'object' ? input : {}
  for (const [key, value] of Object.entries(source)) {
    if (value == null) continue
    const normalized = String(value).trim()
    if (!normalized) continue
    params.set(key, normalized)
  }
  return params.toString()
}

export const attendanceApi = {
  checkAttendance: (userId, location = null, attendanceType = 'checkin', options = null) => request('/api/check_attendance', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
      user_id: userId,
      location,
      attendance_type: attendanceType,
      ...(options && typeof options === 'object' ? options : {}),
    }),
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

  attendanceDetectFrame: (data) => request('/api/attendance_detect', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(data),
  }),

  getStats: () => request('/api/get_attendance_stats'),

  getSystemStorageStats: () => request('/api/system_storage_stats'),

  getRecentActivity: () => request('/api/get_recent_activity'),

  getTodayAttendance: () => request('/api/get_today_attendance'),

  getReport: (filters) => {
    const query = buildReportQuery(filters)
    return request(`/api/report${query ? `?${query}` : ''}`)
  },

  getOnlineAttendance: (filters) => {
    const query = buildReportQuery(filters)
    return request(`/api/report/online_attendance${query ? `?${query}` : ''}`)
  },

  exportReportExcel: (filters) => {
    const query = buildReportQuery(filters)
    return requestBlob(`/api/report/export_xlsx${query ? `?${query}` : ''}`)
  },

  pushReportToErp: (filters) => request('/api/report/push_to_erp', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(
      typeof filters === 'string'
        ? { date: filters }
        : (filters || {})
    ),
  }),
}
