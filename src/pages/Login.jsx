import { useEffect, useState } from 'react'
import { Link, useNavigate } from 'react-router-dom'
import { api, setSessionToken } from '../services/api'
import { ROUTES } from '../config/routes'

export default function Login() {
  const [form, setForm] = useState({ username: '', password: '' })
  const [loginMode, setLoginMode] = useState('system')
  const [rememberMe, setRememberMe] = useState(false)
  const [error, setError] = useState('')
  const [loading, setLoading] = useState(false)
  const [checking, setChecking] = useState(true)
  const navigate = useNavigate()

  useEffect(() => {
    const savedRemember = localStorage.getItem('rememberLogin') === 'true'
    if (savedRemember) {
      const savedUser = localStorage.getItem('savedUsername') || ''
      const savedPass = localStorage.getItem('savedPassword') || ''
      const savedMode = localStorage.getItem('savedLoginMode') || 'system'
      setForm({ username: savedUser, password: savedPass })
      setLoginMode(savedMode === 'internal' ? 'internal' : 'system')
      setRememberMe(true)
    }
    checkCurrentSession()
  }, [])

  async function checkCurrentSession() {
    try {
      const res = await api.sessionStatus()
      if (res.is_admin) {
        navigate(ROUTES.dashboard, { replace: true })
        return
      }
    } catch {
      // Ignore and show login form.
    }
    setChecking(false)
  }

  async function handleSubmit(event) {
    event.preventDefault()
    setLoading(true)
    setError('')

    try {
      const res = await api.login(form.username, form.password, loginMode)
      if (res.success) {
        setSessionToken(res.token)

        if (rememberMe) {
          localStorage.setItem('rememberLogin', 'true')
          localStorage.setItem('savedUsername', form.username)
          localStorage.setItem('savedPassword', form.password)
          localStorage.setItem('savedLoginMode', loginMode)
        } else {
          localStorage.removeItem('rememberLogin')
          localStorage.removeItem('savedUsername')
          localStorage.removeItem('savedPassword')
          localStorage.removeItem('savedLoginMode')
        }

        navigate(ROUTES.dashboard, { replace: true })
      } else {
        setError(res.message || (loginMode === 'internal' ? 'Đăng nhập nội bộ thất bại' : 'Đăng nhập hệ thống thất bại'))
      }
    } catch {
      setError('Không thể kết nối backend')
    }

    setLoading(false)
  }

  if (checking) {
    return (
      <div className="min-h-screen flex items-center justify-center bg-slate-50">
        <div className="flex flex-col items-center gap-3">
          <div className="w-8 h-8 border-3 border-primary-500 border-t-transparent rounded-full animate-spin" />
          <p className="text-sm text-slate-400">Đang kiểm tra phiên...</p>
        </div>
      </div>
    )
  }

  return (
    <div className="min-h-screen flex items-center justify-center bg-gradient-to-br from-slate-50 via-white to-primary-50 px-4">
      <div className="w-full max-w-md">
        <div className="text-center mb-8">
          <div className="inline-flex w-24 h-24 rounded-2xl flex items-center justify-center overflow-hidden bg-white shrink-0 mb-4 shadow-lg shadow-slate-200">
            <img src="https://sof.com.vn/logo.png" alt="SOF Logo" className="w-[80%] h-[80%] object-contain" />
          </div>
          <h1 className="text-3xl font-bold text-slate-800 tracking-tight">FaceCheck</h1>
          <p className="text-slate-400 mt-2">Hệ thống chấm công nhận diện khuôn mặt</p>
        </div>

        <form onSubmit={handleSubmit} className="bg-white p-7 rounded-2xl shadow-xl shadow-slate-200/50 border border-slate-200/60 space-y-5">
          {error && (
            <div className="p-3.5 bg-red-50 text-red-600 text-sm rounded-xl border border-red-100 flex items-center gap-2">
              <span className="text-red-400">×</span>
              {error}
            </div>
          )}

          <div>
            <p className="block text-sm font-medium text-slate-700 mb-2">Chế độ đăng nhập</p>
            <div className="grid grid-cols-2 gap-2">
              <label className={`flex items-center justify-center gap-2 px-3 py-2 rounded-xl border text-sm cursor-pointer transition-colors ${
                loginMode === 'system'
                  ? 'border-primary-300 bg-primary-50 text-primary-700'
                  : 'border-slate-200 bg-white text-slate-600 hover:bg-slate-50'
              }`}>
                <input
                  type="radio"
                  name="loginMode"
                  value="system"
                  checked={loginMode === 'system'}
                  onChange={() => setLoginMode('system')}
                  className="sr-only"
                />
                Login hệ thống
              </label>
              <label className={`flex items-center justify-center gap-2 px-3 py-2 rounded-xl border text-sm cursor-pointer transition-colors ${
                loginMode === 'internal'
                  ? 'border-emerald-300 bg-emerald-50 text-emerald-700'
                  : 'border-slate-200 bg-white text-slate-600 hover:bg-slate-50'
              }`}>
                <input
                  type="radio"
                  name="loginMode"
                  value="internal"
                  checked={loginMode === 'internal'}
                  onChange={() => setLoginMode('internal')}
                  className="sr-only"
                />
                Login admin nội bộ
              </label>
            </div>
          </div>

          <div>
            <label className="block text-sm font-medium text-slate-700 mb-1.5">
              {loginMode === 'system' ? 'Tài khoản hệ thống' : 'Tài khoản admin nội bộ'}
            </label>
            <input
              type="text"
              value={form.username}
              onChange={event => setForm({ ...form, username: event.target.value })}
              className="w-full px-4 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-primary-500/30 focus:border-primary-500 transition-all text-sm"
              placeholder={loginMode === 'system' ? 'Nhập mã đăng nhập hệ thống' : 'Nhập tài khoản nội bộ'}
              required
              autoFocus
            />
          </div>

          <div>
            <label className="block text-sm font-medium text-slate-700 mb-1.5">
              {loginMode === 'system' ? 'Mật khẩu hệ thống' : 'Mật khẩu nội bộ'}
            </label>
            <input
              type="password"
              value={form.password}
              onChange={event => setForm({ ...form, password: event.target.value })}
              className="w-full px-4 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-primary-500/30 focus:border-primary-500 transition-all text-sm"
              placeholder={loginMode === 'system' ? 'Nhập mật khẩu hệ thống' : 'Nhập mật khẩu nội bộ'}
              required
            />
          </div>

          <div className="flex items-center gap-2">
            <input
              type="checkbox"
              id="rememberMe"
              checked={rememberMe}
              onChange={event => setRememberMe(event.target.checked)}
              className="w-4 h-4 rounded border-slate-300 text-primary-600 focus:ring-primary-500 cursor-pointer"
            />
            <label htmlFor="rememberMe" className="text-sm text-slate-600 cursor-pointer select-none">
              Ghi nhớ đăng nhập
            </label>
          </div>

          <button
            type="submit"
            disabled={loading}
            className="w-full px-4 py-3 bg-gradient-to-r from-primary-600 to-primary-700 text-white rounded-xl font-semibold hover:from-primary-700 hover:to-primary-800 disabled:opacity-50 transition-all shadow-sm shadow-primary-200 text-sm"
          >
            {loading ? (
              <span className="flex items-center justify-center gap-2">
                <span className="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin" />
                Đang đăng nhập...
              </span>
            ) : (loginMode === 'system' ? 'Đăng nhập hệ thống' : 'Đăng nhập nội bộ')}
          </button>
        </form>

        <p className="text-center text-xs text-slate-400 mt-6">FaceCheck v1.0 · Hệ thống nội bộ</p>
        <div className="mt-3 text-center">
          <Link
            to={ROUTES.employeeLogin}
            className="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-medium bg-emerald-50 text-emerald-700 hover:bg-emerald-100 transition-colors"
          >
            Sang cổng chấm công nhân viên
          </Link>
        </div>
      </div>
    </div>
  )
}
