import { NavLink, Navigate, Outlet, useLocation } from 'react-router-dom'
import { useEffect, useState } from 'react'
import { api, clearSessionToken } from '../services/api'
import { Home, Camera, UserPlus, Video, Settings, BarChart } from 'lucide-react'

const navItems = [
  { to: '/', label: 'Trang chủ', icon: Home },
  { to: '/attendance', label: 'Điểm danh', icon: Camera },
  { to: '/cameras', label: 'Camera', icon: Video },
  { to: '/register', label: 'Khuôn mặt', icon: UserPlus },
  { to: '/manage', label: 'Quản lý', icon: Settings },
  { to: '/report', label: 'Báo cáo', icon: BarChart },
]

export default function Layout() {
  const [apiStatus, setApiStatus] = useState('checking')
  const [sidebarOpen, setSidebarOpen] = useState(() => {
    const saved = localStorage.getItem('sidebarOpen')
    return saved !== null ? saved === 'true' : true
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
    const interval = setInterval(checkApi, 10000)
    return () => clearInterval(interval)
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
    return <Navigate to="/login" replace />
  }

  const statusDot = {
    ok: 'bg-emerald-400',
    error: 'bg-red-400',
    checking: 'bg-amber-400 animate-pulse',
  }

  return (
    <div className="relative min-h-screen bg-slate-50 overflow-x-hidden">
      {sidebarOpen && (
        <div
          className="lg:hidden fixed inset-0 z-40 bg-slate-900/50 sidebar-overlay"
          onClick={() => setSidebarOpen(false)}
        />
      )}

      <aside className={`
        fixed top-0 left-0 z-50 h-screen
        w-[260px] bg-white border-r border-slate-200
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
          {navItems.map(item => (
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

      <div className={`min-h-screen min-w-0 flex flex-col transition-[margin] duration-300 ${
        sidebarOpen ? 'lg:ml-[260px]' : 'lg:ml-0'
      }`}>
        <header className="sticky top-0 z-30 bg-white/80 backdrop-blur-md border-b border-slate-200/60 px-4 lg:px-6">
          <div className="flex items-center h-14 gap-4">
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
            <div className="flex-1" />
            <div className="flex items-center gap-2 text-sm">
              <span className="text-slate-500 hidden sm:inline">{authState.user?.name || authState.user?.code}</span>
            </div>
          </div>
        </header>

        <main className="flex-1 p-4 lg:p-6 page-content">
          <div className="w-full mx-auto">
            <Outlet />
          </div>
        </main>
      </div>
    </div>
  )
}
