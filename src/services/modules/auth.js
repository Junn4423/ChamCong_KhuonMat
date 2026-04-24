/**
 * Auth module — login, logout, session status.
 */
import { CLIENT_DEVICE_TYPE, request } from '../request'

export const authApi = {
  adminLogin: (username, password, mode = 'system') => request('/api/admin_login', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ username, password, mode, deviceType: CLIENT_DEVICE_TYPE }),
  }),

  login: (username, password, mode = 'system') => request('/api/admin_login', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ username, password, mode, deviceType: CLIENT_DEVICE_TYPE }),
  }),

  adminLogout: () => request('/api/admin_logout', { method: 'POST' }),

  logout: () => request('/api/admin_logout', { method: 'POST' }),

  sessionStatus: () => request('/api/auth_status'),

  employeeLogin: (username, password) => request('/api/employee/login', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ username, password, deviceType: CLIENT_DEVICE_TYPE }),
  }),

  employeeLogout: () => request('/api/employee/logout', { method: 'POST' }),

  employeeStatus: () => request('/api/employee/status'),
}
