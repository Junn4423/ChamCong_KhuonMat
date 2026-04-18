import { ROUTES } from '../config/routes'

export const MODULE_SETTINGS_STORAGE_KEY = 'facecheck.module_visibility.v1'
export const MODULE_SETTINGS_EVENT = 'facecheck:module-settings-changed'

export const MODULE_TOGGLE_KEYS = Object.freeze({
  attendance: 'attendance',
  cameraManagement: 'camera_management',
  onlineSync: 'online_sync',
  syncVerify: 'sync_verify',
  offlineManage: 'offline_manage',
  report: 'report',
})

export const MODULE_TOGGLE_DEFINITIONS = Object.freeze([
  {
    key: MODULE_TOGGLE_KEYS.attendance,
    label: 'Điểm danh',
    path: ROUTES.attendance,
    description: 'Bật hoặc tắt module chấm công trực tiếp.',
  },
  {
    key: MODULE_TOGGLE_KEYS.cameraManagement,
    label: 'Quản lý camera',
    path: ROUTES.cameraManagement,
    description: 'Bật hoặc tắt module cấu hình camera.',
  },
  {
    key: MODULE_TOGGLE_KEYS.onlineSync,
    label: 'Đồng bộ nhân viên online',
    path: ROUTES.onlineSync,
    description: 'Bật hoặc tắt module đồng bộ nhân viên từ ERP.',
  },
  {
    key: MODULE_TOGGLE_KEYS.syncVerify,
    label: 'Xử lý đồng bộ',
    path: ROUTES.syncVerify,
    description: 'Bật hoặc tắt module so sánh dữ liệu ERP và hệ thống.',
  },
  {
    key: MODULE_TOGGLE_KEYS.offlineManage,
    label: 'Quản lý nhân viên offline',
    path: ROUTES.offlineManage,
    description: 'Bật hoặc tắt module quản lý dữ liệu khuôn mặt local.',
  },
  {
    key: MODULE_TOGGLE_KEYS.report,
    label: 'Báo cáo',
    path: ROUTES.report,
    description: 'Bật hoặc tắt module báo cáo và xuất dữ liệu.',
  },
])

const DEFAULT_VISIBILITY = MODULE_TOGGLE_DEFINITIONS.reduce((accumulator, moduleDef) => {
  accumulator[moduleDef.key] = true
  return accumulator
}, {})

export const DEFAULT_MODULE_VISIBILITY = Object.freeze(DEFAULT_VISIBILITY)

function normalizeVisibility(input) {
  const merged = { ...DEFAULT_MODULE_VISIBILITY }
  if (!input || typeof input !== 'object') {
    return merged
  }

  for (const moduleDef of MODULE_TOGGLE_DEFINITIONS) {
    const rawValue = input[moduleDef.key]
    if (typeof rawValue === 'boolean') {
      merged[moduleDef.key] = rawValue
    }
  }
  return merged
}

export function getModuleVisibility() {
  if (typeof window === 'undefined') {
    return { ...DEFAULT_MODULE_VISIBILITY }
  }

  try {
    const raw = localStorage.getItem(MODULE_SETTINGS_STORAGE_KEY)
    if (!raw) {
      return { ...DEFAULT_MODULE_VISIBILITY }
    }
    return normalizeVisibility(JSON.parse(raw))
  } catch {
    return { ...DEFAULT_MODULE_VISIBILITY }
  }
}

export function saveModuleVisibility(nextVisibility) {
  const normalized = normalizeVisibility(nextVisibility)
  if (typeof window !== 'undefined') {
    localStorage.setItem(MODULE_SETTINGS_STORAGE_KEY, JSON.stringify(normalized))
    window.dispatchEvent(new CustomEvent(MODULE_SETTINGS_EVENT, { detail: normalized }))
  }
  return normalized
}

export function setModuleEnabled(moduleKey, enabled) {
  if (!moduleKey || !(moduleKey in DEFAULT_MODULE_VISIBILITY)) {
    return getModuleVisibility()
  }

  const current = getModuleVisibility()
  const next = {
    ...current,
    [moduleKey]: Boolean(enabled),
  }
  return saveModuleVisibility(next)
}

export function resetModuleVisibility() {
  return saveModuleVisibility(DEFAULT_MODULE_VISIBILITY)
}

export function isModuleEnabled(moduleKey) {
  if (!moduleKey || !(moduleKey in DEFAULT_MODULE_VISIBILITY)) {
    return true
  }
  const visibility = getModuleVisibility()
  return visibility[moduleKey] !== false
}
