/**
 * Core HTTP request helper and session token management.
 * All API modules import `request` from here.
 */

export const API_BASE = window.electronAPI?.apiPort
  ? `http://127.0.0.1:${window.electronAPI.apiPort}`
  : ''

let sessionToken = localStorage.getItem('sessionToken') || null

export function setSessionToken(token) {
  sessionToken = token
  if (token) localStorage.setItem('sessionToken', token)
  else localStorage.removeItem('sessionToken')
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
  }

  const res = await fetch(url, {
    ...options,
    headers,
  })

  if (!res.ok) {
    try {
      return await res.json()
    } catch {
      throw new Error(`HTTP ${res.status}: ${res.statusText}`)
    }
  }

  return res.json()
}
