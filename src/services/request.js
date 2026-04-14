/**
 * Core HTTP request helper and session token management.
 * All API modules import `request` from here.
 */

export const API_BASE = window.electronAPI?.apiPort
  ? `http://127.0.0.1:${window.electronAPI.apiPort}`
  : ''

const SESSION_TOKEN_STORAGE_KEY = 'sessionToken'
export const SESSION_EXPIRED_EVENT = 'facecheck:session-expired'
const EXPIRED_SESSION_MESSAGE = 'Phiên đăng nhập đã hết hạn, vui lòng đăng nhập lại.'
let sessionExpiredNotified = false

// Keep auth token per browser tab/session so restart/reopen always requires login.
let sessionToken = sessionStorage.getItem(SESSION_TOKEN_STORAGE_KEY) || null

if (!sessionToken) {
  localStorage.removeItem(SESSION_TOKEN_STORAGE_KEY)
}

function notifySessionExpired() {
  if (sessionExpiredNotified) {
    return
  }
  sessionExpiredNotified = true
  if (typeof window !== 'undefined') {
    window.dispatchEvent(new CustomEvent(SESSION_EXPIRED_EVENT))
  }
}

export function setSessionToken(token) {
  sessionToken = token
  if (token) {
    sessionExpiredNotified = false
    sessionStorage.setItem(SESSION_TOKEN_STORAGE_KEY, token)
    localStorage.removeItem(SESSION_TOKEN_STORAGE_KEY)
  } else {
    sessionStorage.removeItem(SESSION_TOKEN_STORAGE_KEY)
    localStorage.removeItem(SESSION_TOKEN_STORAGE_KEY)
  }
}

export function getSessionToken() {
  return sessionToken
}

export function clearSessionToken() {
  setSessionToken(null)
}

/**
 * Generic fetch wrapper that injects auth header and handles errors.
 */
export async function request(path, options = {}) {
  const url = `${API_BASE}${path}`
  const headers = { ...(options.headers || {}) }

  if (sessionToken) {
    headers['X-Admin-Token'] = sessionToken
    headers['X-Session-Token'] = sessionToken
  }

  const res = await fetch(url, {
    ...options,
    headers,
  })

  if (res.status === 401) {
    clearSessionToken()
    notifySessionExpired()
    try {
      const payload = await res.json()
      return {
        ...(payload || {}),
        success: false,
        message: payload?.message || EXPIRED_SESSION_MESSAGE,
      }
    } catch {
      return {
        success: false,
        message: EXPIRED_SESSION_MESSAGE,
      }
    }
  }

  if (!res.ok) {
    try {
      return await res.json()
    } catch {
      throw new Error(`HTTP ${res.status}: ${res.statusText}`)
    }
  }

  return res.json()
}
