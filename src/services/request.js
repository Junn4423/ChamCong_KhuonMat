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

function buildAuthHeaders(headers = {}) {
  const nextHeaders = { ...headers }
  if (sessionToken) {
    nextHeaders['X-Admin-Token'] = sessionToken
    nextHeaders['X-Session-Token'] = sessionToken
  }
  return nextHeaders
}

/**
 * Generic fetch wrapper that injects auth header and handles errors.
 */
export async function request(path, options = {}) {
  const url = `${API_BASE}${path}`
  const headers = buildAuthHeaders(options.headers || {})

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

export async function requestBlob(path, options = {}) {
  const url = `${API_BASE}${path}`
  const headers = buildAuthHeaders(options.headers || {})

  const res = await fetch(url, {
    ...options,
    headers,
  })

  if (res.status === 401) {
    clearSessionToken()
    notifySessionExpired()
    throw new Error(EXPIRED_SESSION_MESSAGE)
  }

  if (!res.ok) {
    try {
      const payload = await res.json()
      throw new Error(payload?.message || `HTTP ${res.status}: ${res.statusText}`)
    } catch (error) {
      throw new Error(error?.message || `HTTP ${res.status}: ${res.statusText}`)
    }
  }

  const contentDisposition = res.headers.get('Content-Disposition') || ''
  const utf8Match = contentDisposition.match(/filename\*=UTF-8''([^;]+)/i)
  const basicMatch = contentDisposition.match(/filename="?([^\";]+)"?/i)
  const filename = utf8Match?.[1]
    ? decodeURIComponent(utf8Match[1])
    : (basicMatch?.[1] || '')

  return {
    blob: await res.blob(),
    filename,
  }
}
