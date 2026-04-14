/**
 * Unified API — aggregates all module APIs into one `api` object.
 *
 * Module files live in ./modules/ and each export a domain-specific API slice.
 * This file merges them so existing consumers (`import { api } from '../services/api'`)
 * continue to work without any changes.
 *
 * Session token helpers are re-exported from ./request.js.
 */

// Re-export session helpers for consumers that import them from 'api'
export { setSessionToken, getSessionToken, clearSessionToken, SESSION_EXPIRED_EVENT } from './request'
// Re-export request for advanced use-cases
export { request } from './request'

// Module imports
import { authApi } from './modules/auth'
import { registrationApi } from './modules/registration'
import { cameraApi } from './modules/camera'
import { attendanceApi } from './modules/attendance'
import { employeeApi } from './modules/employee'
import { locationApi } from './modules/location'
import { request } from './request'

/**
 * Single merged API object — backwards-compatible with every page/component
 * that already does `api.someMethod()`.
 */
export const api = {
  health: () => request('/api/health'),

  // Auth
  ...authApi,

  // Registration
  ...registrationApi,

  // Camera
  ...cameraApi,

  // Attendance
  ...attendanceApi,

  // Employee (ERP + Admin)
  ...employeeApi,

  // Location
  ...locationApi,
}
