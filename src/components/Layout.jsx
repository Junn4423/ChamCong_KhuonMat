import { NavLink, Outlet } from 'react-router-dom'
import { useState, useEffect } from 'react'
import { api } from '../services/api'

const navItems = [
  { to: '/', label: 'Trang chủ', icon: '🏠' },
  { to: '/attendance', label: 'Điểm danh', icon: '📷' },
  { to: '/register', label: 'Đăng ký', icon: '➕' },
  { to: '/manage', label: 'Quản lý', icon: '👤' },
  { to: '/report', label: 'Báo cáo', icon: '📊' },
  { to: '/api-test', label: 'API', icon: '🔌' },
]

export default function Layout() {
  const [apiStatus, setApiStatus] = useState('checking') // 'ok' | 'error' | 'checking'

  useEffect(() => {
    checkApi()
    const interval = setInterval(checkApi, 10000)
    return () => clearInterval(interval)
  }, [])

  async function checkApi() {
    try {
      const res = await api.health()
      setApiStatus(res.status === 'ok' ? 'ok' : 'error')
    } catch {
      setApiStatus('error')
    }
  }

  const statusColors = {
    ok: 'bg-green-500',
    error: 'bg-red-500',
    checking: 'bg-yellow-500 animate-pulse',
  }
  const statusText = {
    ok: 'API Connected',
    error: 'API Disconnected',
    checking: 'Checking...',
  }

  return (
    <div className="min-h-screen flex flex-col">
      {/* Top navbar */}
      <nav className="bg-white border-b border-gray-200 sticky top-0 z-50">
        <div className="max-w-7xl mx-auto px-4 sm:px-6">
          <div className="flex items-center justify-between h-14">
            <div className="flex items-center gap-3">
              <NavLink to="/" className="text-xl font-bold text-primary-600">
                FaceCheck
              </NavLink>
              <span className="flex items-center gap-1.5 text-xs text-gray-500" title={statusText[apiStatus]}>
                <span className={`w-2 h-2 rounded-full ${statusColors[apiStatus]}`}></span>
                <span className="hidden sm:inline">{statusText[apiStatus]}</span>
              </span>
            </div>
            <div className="hidden md:flex gap-1">
              {navItems.map(item => (
                <NavLink
                  key={item.to}
                  to={item.to}
                  end={item.to === '/'}
                  className={({ isActive }) =>
                    `px-3 py-2 rounded-lg text-sm font-medium transition-colors ${
                      isActive
                        ? 'bg-primary-50 text-primary-700'
                        : 'text-gray-600 hover:bg-gray-100'
                    }`
                  }
                >
                  {item.label}
                </NavLink>
              ))}
            </div>
          </div>
        </div>
      </nav>

      {/* Main content */}
      <main className="flex-1 max-w-7xl mx-auto w-full px-4 sm:px-6 py-6">
        <Outlet />
      </main>

      {/* Bottom nav for mobile */}
      <nav className="md:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 z-50">
        <div className="flex justify-around py-2">
          {navItems.map(item => (
            <NavLink
              key={item.to}
              to={item.to}
              end={item.to === '/'}
              className={({ isActive }) =>
                `flex flex-col items-center gap-0.5 text-xs ${
                  isActive ? 'text-primary-600' : 'text-gray-500'
                }`
              }
            >
              <span className="text-lg">{item.icon}</span>
              <span>{item.label}</span>
            </NavLink>
          ))}
        </div>
      </nav>

      {/* Footer (desktop) */}
      <footer className="hidden md:block text-center py-3 text-sm text-gray-400">
        &copy; 2025 FaceCheck
      </footer>
    </div>
  )
}
