/**
 * Auth module — login, logout, session status.
 */
import { request } from '../request'

export const authApi = {
  login: (username, password) => request('/api/admin_login', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ username, password }),
  }),

  logout: () => request('/api/admin_logout', { method: 'POST' }),

  sessionStatus: () => request('/api/auth_status'),
}
