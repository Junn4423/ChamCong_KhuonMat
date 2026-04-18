export const ROUTES = Object.freeze({
  login: '/login',
  dashboard: '/',
  attendance: '/diem-danh',
  cameraManagement: '/quan-ly-camera',
  onlineSync: '/dong-bo-nhan-vien-online',
  syncVerify: '/xu-ly-dong-bo',
  offlineManage: '/quan-ly-nhan-vien-offline',
  report: '/bao-cao',
  onlineAttendanceCheck: '/kiem-tra-cham-cong-online',
  systemSettings: '/cai-dat-he-thong',
})

export const LEGACY_ROUTE_ALIASES = Object.freeze([
  { from: '/attendance', to: ROUTES.attendance },
  { from: '/cameras', to: ROUTES.cameraManagement },
  { from: '/camera', to: ROUTES.cameraManagement },
  { from: '/report', to: ROUTES.report },
  { from: '/register', to: ROUTES.onlineSync },
  { from: '/manage', to: ROUTES.offlineManage },
])
