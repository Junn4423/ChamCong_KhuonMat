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
  onlineAttendanceCheck: 'online_attendance_check',
  accountManagement: 'account_management',
  mobileConfig: 'mobile_config',
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
  {
    key: MODULE_TOGGLE_KEYS.onlineAttendanceCheck,
    label: 'Kiểm tra chấm công online',
    path: ROUTES.onlineAttendanceCheck,
    description: 'Bật hoặc tắt module kiểm tra dữ liệu chấm công đã đẩy lên ERP.',
  },
  {
    key: MODULE_TOGGLE_KEYS.accountManagement,
    label: 'Quản lý tài khoản nhân viên',
    path: ROUTES.accountManagement,
    description: 'Bật hoặc tắt module kéo tài khoản từ ERP và quản lý lock/reset.',
  },
  {
    key: MODULE_TOGGLE_KEYS.mobileConfig,
    label: 'Cấu hình mobile',
    path: ROUTES.mobileConfig,
    description: 'Bật hoặc tắt module auto-config cho app mobile (UDP discovery, QR pairing, pass).',
  },
])

const DEFAULT_VISIBILITY = MODULE_TOGGLE_DEFINITIONS.reduce((accumulator, moduleDef) => {
  accumulator[moduleDef.key] = true
  return accumulator
}, {})

export const DEFAULT_MODULE_VISIBILITY = Object.freeze(DEFAULT_VISIBILITY)

let moduleVisibilityCache = { ...DEFAULT_MODULE_VISIBILITY }

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
  return { ...moduleVisibilityCache }
}

function emitModuleVisibilityChanged(normalized) {
  if (typeof window !== 'undefined') {
    window.dispatchEvent(new CustomEvent(MODULE_SETTINGS_EVENT, { detail: normalized }))
  }
}

export function applyModuleVisibility(nextVisibility, options = {}) {
  const normalized = normalizeVisibility(nextVisibility)
  moduleVisibilityCache = normalized
  if (options.emitEvent !== false) {
    emitModuleVisibilityChanged(normalized)
  }
  return { ...normalized }
}

export function saveModuleVisibility(nextVisibility) {
  return applyModuleVisibility(nextVisibility)
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
  return applyModuleVisibility(next)
}

export function resetModuleVisibility() {
  return applyModuleVisibility(DEFAULT_MODULE_VISIBILITY)
}

export function isModuleEnabled(moduleKey) {
  if (!moduleKey || !(moduleKey in DEFAULT_MODULE_VISIBILITY)) {
    return true
  }
  const visibility = getModuleVisibility()
  return visibility[moduleKey] !== false
}
