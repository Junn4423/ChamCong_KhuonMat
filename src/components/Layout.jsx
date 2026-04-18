import { NavLink, Navigate, Outlet, useLocation } from 'react-router-dom'
import { useEffect, useState } from 'react'
import { api, clearSessionToken, SESSION_EXPIRED_EVENT } from '../services/api'
import {
  Home,
  Camera,
  UserPlus,
  Video,
  Settings,
  BarChart,
  Shuffle,
  Users,
} from 'lucide-react'
import { ROUTES } from '../config/routes'
import {
  MODULE_SETTINGS_EVENT,
  MODULE_TOGGLE_KEYS,
  getModuleVisibility,
} from '../services/moduleSettings'

const navItems = [
  { to: ROUTES.dashboard, label: 'Trang chủ', icon: Home },
  { to: ROUTES.attendance, label: 'Điểm danh', icon: Camera, moduleKey: MODULE_TOGGLE_KEYS.attendance },
  { to: ROUTES.cameraManagement, label: 'Quản lý camera', icon: Video, moduleKey: MODULE_TOGGLE_KEYS.cameraManagement },
  { to: ROUTES.onlineSync, label: 'Đồng bộ nhân viên online', icon: UserPlus, moduleKey: MODULE_TOGGLE_KEYS.onlineSync },
  { to: ROUTES.syncVerify, label: 'Xử lý đồng bộ', icon: Shuffle, moduleKey: MODULE_TOGGLE_KEYS.syncVerify },
  { to: ROUTES.offlineManage, label: 'Quản lý nhân viên offline', icon: Users, moduleKey: MODULE_TOGGLE_KEYS.offlineManage },
  { to: ROUTES.report, label: 'Báo cáo', icon: BarChart, moduleKey: MODULE_TOGGLE_KEYS.report },
  { to: ROUTES.systemSettings, label: 'Cài đặt hệ thống', icon: Settings },
]

const routedSystemNames = {
  hr: 'Hệ thống Nhân Sự',
  er: 'Hệ thống ERP',
  lg: 'Hệ thống Vận Tải',
  cf: 'Chuỗi hệ thống Cafe, Nhà Hàng Quán Ăn',
  ht: 'Hệ thống Khách Sạn',
  pk: 'Hệ thống Bãi Xe',
  sl: 'Hệ thống Bán Hàng',
}

export default function Layout() {
  const [apiStatus, setApiStatus] = useState('checking')
  const [moduleVisibility, setModuleVisibility] = useState(() => getModuleVisibility())
  const [sidebarOpen, setSidebarOpen] = useState(() => {
    const saved = localStorage.getItem('sidebarOpen')
    if (saved !== null) return saved === 'true'
    if (typeof window !== 'undefined') return window.innerWidth >= 1024
    return true
  })
  const [authState, setAuthState] = useState({
    loading: true,
    authenticated: false,
    user: null,
  })
  const location = useLocation()

  useEffect(() => {
    checkSession()
    checkApi()
    const apiInterval = setInterval(checkApi, 10000)
    const sessionInterval = setInterval(checkSession, 15000)
    return () => {
      clearInterval(apiInterval)
      clearInterval(sessionInterval)
    }
  }, [])

  useEffect(() => {
    function handleSessionExpired() {
      setAuthState({ loading: false, authenticated: false, user: null })
    }

    window.addEventListener(SESSION_EXPIRED_EVENT, handleSessionExpired)
    return () => window.removeEventListener(SESSION_EXPIRED_EVENT, handleSessionExpired)
  }, [])

  useEffect(() => {
    function refreshModuleVisibility() {
      setModuleVisibility(getModuleVisibility())
    }

    refreshModuleVisibility()
    window.addEventListener(MODULE_SETTINGS_EVENT, refreshModuleVisibility)
    window.addEventListener('storage', refreshModuleVisibility)
    return () => {
      window.removeEventListener(MODULE_SETTINGS_EVENT, refreshModuleVisibility)
      window.removeEventListener('storage', refreshModuleVisibility)
    }
  }, [])

  useEffect(() => {
    function hideTranslateToolbar() {
      if (typeof document === 'undefined') {
        return
      }

      document.body.style.setProperty('top', '0px', 'important')
      document.documentElement.style.setProperty('top', '0px', 'important')

      const toolbarNodes = document.querySelectorAll(
        'iframe.goog-te-banner-frame, .goog-te-banner-frame, body > .skiptranslate',
      )

      toolbarNodes.forEach(node => {
        if (node.id === 'google_translate_element_hidden') {
          return
        }
        node.style.setProperty('display', 'none', 'important')
        node.style.setProperty('visibility', 'hidden', 'important')
        node.style.setProperty('height', '0px', 'important')
        node.style.setProperty('min-height', '0px', 'important')
        node.style.setProperty('width', '0px', 'important')
        node.style.setProperty('opacity', '0', 'important')
        node.style.setProperty('pointer-events', 'none', 'important')
      })
    }

    hideTranslateToolbar()
    const interval = setInterval(hideTranslateToolbar, 450)
    const observer = new MutationObserver(() => hideTranslateToolbar())
    observer.observe(document.documentElement, { childList: true, subtree: true })

    return () => {
      clearInterval(interval)
      observer.disconnect()
    }
  }, [])

  useEffect(() => {
    if (window.innerWidth < 1024) {
      setSidebarOpen(false)
    }
  }, [location.pathname])

  function toggleSidebar() {
    const next = !sidebarOpen
    setSidebarOpen(next)
    localStorage.setItem('sidebarOpen', String(next))
  }

  async function checkSession() {
    try {
      const res = await api.sessionStatus()
      if (res.is_admin) {
        setAuthState({
          loading: false,
          authenticated: true,
          user: {
            name: res.user?.name || 'Admin',
            code: res.user?.code || 'admin',
            department: res.user?.department || 'Quản trị viên',
            route_prefix: res.user?.route_prefix || '',
            system_name: res.user?.system_name || '',
            system_note: res.user?.system_note || '',
            welcome_message: res.user?.welcome_message || '',
          },
        })
      } else {
        clearSessionToken()
        setAuthState({ loading: false, authenticated: false, user: null })
      }
    } catch {
      clearSessionToken()
      setAuthState({ loading: false, authenticated: false, user: null })
    }
  }

  async function checkApi() {
    try {
      const res = await api.health()
      setApiStatus(res.status === 'ok' ? 'ok' : 'error')
    } catch {
      setApiStatus('error')
    }
  }

  async function handleLogout() {
    try {
      await api.logout()
    } finally {
      clearSessionToken()
      localStorage.removeItem('savedUsername')
      localStorage.removeItem('savedPassword')
      localStorage.removeItem('rememberLogin')
      setAuthState({ loading: false, authenticated: false, user: null })
    }
  }

  if (authState.loading) {
    return (
      <div className="min-h-screen flex items-center justify-center bg-slate-50">
        <div className="flex flex-col items-center gap-3">
          <div className="w-8 h-8 border-3 border-primary-500 border-t-transparent rounded-full animate-spin" />
          <p className="text-sm text-slate-400">Đang kiểm tra phiên...</p>
        </div>
      </div>
    )
  }

  if (!authState.authenticated) {
    return <Navigate to={ROUTES.login} replace />
  }

  const statusDot = {
    ok: 'bg-emerald-400',
    error: 'bg-red-400',
    checking: 'bg-amber-400 animate-pulse',
  }
  const routedPrefix = (authState.user?.route_prefix || '').trim().toLowerCase()
  const resolvedSystemName = authState.user?.system_name
    || authState.user?.system_note
    || routedSystemNames[routedPrefix]
    || ''
  const welcomeMessage = resolvedSystemName
    ? `Xin chào, đây là hệ thống chấm công ${resolvedSystemName}`
    : (authState.user?.welcome_message || '')
  const visibleNavItems = navItems.filter(item => !item.moduleKey || moduleVisibility[item.moduleKey] !== false)

  return (
    <div className="relative min-h-dvh bg-slate-50 overflow-x-hidden">
      {sidebarOpen && (
        <div
          className="lg:hidden fixed inset-0 z-40 bg-slate-900/50 sidebar-overlay backdrop-blur-[1px]"
          onClick={() => setSidebarOpen(false)}
        />
      )}

      <aside className={`
        fixed top-0 left-0 z-50 h-screen
        w-[84vw] max-w-[300px] sm:w-[260px] bg-white border-r border-slate-200
        flex flex-col transition-transform duration-300 ease-in-out
        ${sidebarOpen ? 'translate-x-0' : '-translate-x-full'}
      `}>
        <div className="px-5 py-5 flex items-center gap-3 border-b border-slate-100">
          <div className="w-9 h-9 rounded-xl flex items-center justify-center overflow-hidden bg-white shrink-0">
            <img src="https://sof.com.vn/logo.png" alt="SOF Logo" className="w-full h-full object-contain" />
          </div>
          <div>
            <h1 className="text-lg font-bold text-slate-800 tracking-tight">FaceCheck</h1>
            <div className="flex items-center gap-1.5">
              <span className={`w-1.5 h-1.5 rounded-full ${statusDot[apiStatus]}`} />
              <span className="text-[10px] text-slate-400 uppercase tracking-wider font-medium">
                {apiStatus === 'ok' ? 'Online' : apiStatus === 'error' ? 'Offline' : '...'}
              </span>
            </div>
          </div>
        </div>

        <nav className="flex-1 px-3 py-4 space-y-0.5 overflow-y-auto">
          {visibleNavItems.map(item => (
            <NavLink
              key={item.to}
              to={item.to}
              end={item.to === '/'}
              className={({ isActive }) =>
                `flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 ${
                  isActive
                    ? 'bg-primary-50 text-primary-700 shadow-sm shadow-primary-100'
                    : 'text-slate-600 hover:bg-slate-50 hover:text-slate-800'
                }`
              }
            >
              <span className="flex items-center justify-center w-5 text-center">
                <item.icon size={18} strokeWidth={2.5} />
              </span>
              {item.label}
            </NavLink>
          ))}
        </nav>

        <div className="px-4 py-4 border-t border-slate-100">
          <div className="flex items-center gap-3 mb-3">
            <div className="w-9 h-9 rounded-xl bg-gradient-to-br from-slate-200 to-slate-300 flex items-center justify-center text-slate-600 font-semibold text-xs">
              {(authState.user?.name || authState.user?.code || '?')[0]?.toUpperCase()}
            </div>
            <div className="flex-1 min-w-0">
              <p className="text-sm font-semibold text-slate-700 truncate">{authState.user?.name || authState.user?.code}</p>
              <p className="text-xs text-slate-400 truncate">{authState.user?.department || 'ERP Session'}</p>
            </div>
          </div>
          <button
            onClick={handleLogout}
            className="w-full px-3 py-2 rounded-lg text-sm font-medium text-slate-500 hover:bg-red-50 hover:text-red-600 transition-colors text-center"
          >
            Đăng xuất
          </button>
        </div>
      </aside>

      <div className={`min-h-dvh min-w-0 flex flex-col transition-[margin] duration-300 ${
        sidebarOpen ? 'lg:ml-[260px]' : 'lg:ml-0'
      }`}>
        <header className="sticky top-0 z-30 bg-white/80 backdrop-blur-md border-b border-slate-200/60 px-3 sm:px-4 lg:px-6">
          <div className="flex items-center h-12 sm:h-14 gap-3 sm:gap-4">
            <button
              onClick={toggleSidebar}
              className="w-9 h-9 rounded-lg flex items-center justify-center hover:bg-slate-100 text-slate-600 transition-colors"
              title={sidebarOpen ? 'Đóng menu' : 'Mở menu'}
            >
              <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path
                  strokeLinecap="round"
                  strokeLinejoin="round"
                  strokeWidth={2}
                  d={sidebarOpen ? 'M6 18L18 6M6 6l12 12' : 'M4 6h16M4 12h16M4 18h16'}
                />
              </svg>
            </button>
            <div className="flex-1 min-w-0">
              {welcomeMessage && (
                <p className="text-xs sm:text-sm font-medium text-primary-700 truncate">{welcomeMessage}</p>
              )}
            </div>
            <div className="flex items-center gap-2 text-sm">
              <span className="text-slate-500 hidden sm:inline">{authState.user?.name || authState.user?.code}</span>
            </div>
          </div>
        </header>

        <main className="flex-1 p-3 sm:p-4 lg:p-6 page-content">
          <div className="w-full mx-auto">
            <Outlet />
          </div>
        </main>
      </div>
    </div>
  )
}
