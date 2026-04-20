import { useEffect, useState } from 'react'
import { Link, useNavigate } from 'react-router-dom'
import { api, clearSessionToken, setSessionToken } from '../services/api'
import { ROUTES } from '../config/routes'

export default function EmployeeLogin() {
  const [form, setForm] = useState({ username: '', password: '' })
  const [loading, setLoading] = useState(false)
  const [checking, setChecking] = useState(true)
  const [error, setError] = useState('')
  const navigate = useNavigate()

  useEffect(() => {
    verifyEmployeeSession()
  }, [])

  async function verifyEmployeeSession() {
    try {
      const res = await api.employeeStatus()
      if (res?.authenticated && res?.is_employee) {
        navigate(ROUTES.employeeAttendance, { replace: true })
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
      clearSessionToken()
      const res = await api.employeeLogin(form.username, form.password)
      if (!res?.success) {
        setError(res?.message || 'Đăng nhập nhân viên thất bại')
        setLoading(false)
        return
      }

      setSessionToken(res.token)
      navigate(ROUTES.employeeAttendance, { replace: true })
    } catch {
      setError('Không thể kết nối backend')
    }

    setLoading(false)
  }

  if (checking) {
    return (
      <div className="min-h-screen flex items-center justify-center bg-slate-50">
        <div className="flex flex-col items-center gap-3">
          <div className="w-8 h-8 border-3 border-emerald-500 border-t-transparent rounded-full animate-spin" />
          <p className="text-sm text-slate-400">Đang kiểm tra phiên...</p>
        </div>
      </div>
    )
  }

  return (
    <div className="min-h-screen flex items-center justify-center bg-gradient-to-br from-emerald-50 via-white to-slate-100 px-4 py-8">
      <div className="w-full max-w-md">
        <div className="text-center mb-8">
          <h1 className="text-3xl font-bold text-slate-800 tracking-tight">Chấm Công Nhân Viên</h1>
          <p className="text-slate-500 mt-2">Đăng nhập bằng tài khoản nhân viên đã đồng bộ từ ERP</p>
        </div>

        <form onSubmit={handleSubmit} className="bg-white rounded-2xl p-7 border border-slate-200 shadow-xl shadow-slate-100 space-y-5">
          {error && (
            <div className="px-3 py-2.5 rounded-xl border border-red-100 bg-red-50 text-red-600 text-sm">
              {error}
            </div>
          )}

          <div>
            <label className="block text-sm font-medium text-slate-700 mb-1.5">Tài khoản nhân viên</label>
            <input
              type="text"
              value={form.username}
              onChange={event => setForm({ ...form, username: event.target.value })}
              placeholder="Tên đăng nhập"
              className="w-full px-4 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 transition-all text-sm"
              required
              autoFocus
            />
          </div>

          <div>
            <label className="block text-sm font-medium text-slate-700 mb-1.5">Mật khẩu</label>
            <input
              type="password"
              value={form.password}
              onChange={event => setForm({ ...form, password: event.target.value })}
              placeholder="Mật khẩu"
              className="w-full px-4 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 transition-all text-sm"
              required
            />
          </div>

          <button
            type="submit"
            disabled={loading}
            className="w-full px-4 py-3 rounded-xl bg-gradient-to-r from-emerald-600 to-emerald-700 text-white font-semibold text-sm hover:from-emerald-700 hover:to-emerald-800 disabled:opacity-50"
          >
            {loading ? 'Đang đăng nhập...' : 'Vào màn hình chấm công'}
          </button>
        </form>

        <div className="mt-5 flex items-center justify-center gap-2 text-xs text-slate-500">
          <Link to={ROUTES.portal} className="hover:text-slate-700">Về trang chọn cổng</Link>
          <span>•</span>
          <Link to={ROUTES.login} className="hover:text-slate-700">Đăng nhập quản trị</Link>
        </div>
      </div>
    </div>
  )
}
