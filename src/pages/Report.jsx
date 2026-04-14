import React, { useEffect, useMemo, useState } from 'react'
import { api, setSessionToken } from '../services/api'
import { Download, Search, UploadCloud, X } from 'lucide-react'
import { useToast } from '../components/Toast'

function SystemLoginModal({
  open,
  form,
  setForm,
  loading,
  error,
  onClose,
  onSubmit,
}) {
  if (!open) return null

  return (
    <div className="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4">
      <div className="w-full max-w-md bg-white rounded-2xl shadow-2xl overflow-hidden border border-slate-200">
        <div className="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
          <h2 className="text-base font-semibold text-slate-800">Đăng nhập hệ thống</h2>
          <button
            onClick={onClose}
            className="w-8 h-8 rounded-lg bg-slate-100 text-slate-500 hover:bg-slate-200 flex items-center justify-center transition-colors"
          >
            <X size={16} />
          </button>
        </div>

        <form onSubmit={onSubmit} className="p-5 space-y-4">
          <p className="text-sm text-slate-600">
            Chuẩn bị đẩy data chấm công lên hệ thống, vui lòng đăng nhập hệ thống.
          </p>

          {error && (
            <div className="px-3 py-2 rounded-lg border border-red-200 bg-red-50 text-sm text-red-600">
              {error}
            </div>
          )}

          <div>
            <label className="block text-sm font-medium text-slate-700 mb-1.5">Tài khoản hệ thống</label>
            <input
              type="text"
              value={form.username}
              onChange={event => setForm(prev => ({ ...prev, username: event.target.value }))}
              className="w-full px-3 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-primary-500/30 focus:border-primary-500 text-sm"
              placeholder="Nhập tài khoản hệ thống"
              required
            />
          </div>

          <div>
            <label className="block text-sm font-medium text-slate-700 mb-1.5">Mật khẩu hệ thống</label>
            <input
              type="password"
              value={form.password}
              onChange={event => setForm(prev => ({ ...prev, password: event.target.value }))}
              className="w-full px-3 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-primary-500/30 focus:border-primary-500 text-sm"
              placeholder="Nhập mật khẩu hệ thống"
              required
            />
          </div>

          <div className="grid sm:flex sm:justify-end gap-2 pt-1">
            <button
              type="button"
              onClick={onClose}
              className="w-full sm:w-auto px-4 py-2 rounded-xl bg-slate-100 text-slate-600 text-sm font-medium hover:bg-slate-200 transition-colors"
              disabled={loading}
            >
              Hủy
            </button>
            <button
              type="submit"
              disabled={loading}
              className="w-full sm:w-auto px-4 py-2 rounded-xl bg-primary-600 text-white text-sm font-medium hover:bg-primary-700 transition-colors disabled:opacity-50"
            >
              {loading ? 'Đang đăng nhập...' : 'Đăng nhập và tiếp tục'}
            </button>
          </div>
        </form>
      </div>
    </div>
  )
}

function getStatusTone(status) {
  return status === 'Đúng giờ'
    ? 'bg-green-50 text-green-700'
    : 'bg-yellow-50 text-yellow-700'
}

export default function Report() {
  const { toast } = useToast()
  const [date, setDate] = useState(new Date().toISOString().split('T')[0])
  const [records, setRecords] = useState([])
  const [loading, setLoading] = useState(false)
  const [pushing, setPushing] = useState(false)
  const [authMode, setAuthMode] = useState('system')

  const [showSystemLoginModal, setShowSystemLoginModal] = useState(false)
  const [systemLoginLoading, setSystemLoginLoading] = useState(false)
  const [systemLoginError, setSystemLoginError] = useState('')
  const [systemLoginForm, setSystemLoginForm] = useState({ username: '', password: '' })

  const [page, setPage] = useState(1)
  const [pageSize, setPageSize] = useState(10)

  useEffect(() => {
    setPage(1)
    loadReport()
  }, [date])

  useEffect(() => {
    loadAuthMode()
  }, [])

  const paginatedRecords = useMemo(() => {
    const startIndex = (page - 1) * pageSize
    return records.slice(startIndex, startIndex + pageSize)
  }, [records, page, pageSize])

  const totalPages = Math.max(1, Math.ceil(records.length / pageSize))
  const isInternalMode = authMode === 'internal'

  async function loadAuthMode() {
    try {
      const res = await api.sessionStatus()
      const mode = (res?.auth_mode || res?.user?.auth_mode || 'system').toLowerCase()
      setAuthMode(mode === 'internal' ? 'internal' : 'system')
    } catch {
      setAuthMode('system')
    }
  }

  async function loadReport() {
    setLoading(true)
    try {
      const res = await api.getReport(date)
      if (res.success) {
        setRecords(res.records || [])
      } else {
        toast.error(res.message || 'Không tải được báo cáo')
      }
    } catch (error) {
      console.error(error)
      toast.error('Không thể kết nối backend')
    }
    setLoading(false)
  }

  function exportCSV() {
    if (records.length === 0) return
    const headers = ['Tên', 'Mã NV', 'Phòng ban', 'Giờ vào', 'Giờ ra', 'Trạng thái']
    const rows = records.map(record => [
      record.name,
      record.employee_id,
      record.department,
      record.check_in_time,
      record.check_out_time,
      record.status,
    ])
    const csv = [headers, ...rows].map(row => row.join(',')).join('\n')
    const blob = new Blob(['\ufeff' + csv], { type: 'text/csv;charset=utf-8;' })
    const url = URL.createObjectURL(blob)
    const anchor = document.createElement('a')
    anchor.href = url
    anchor.download = `attendance_${date}.csv`
    anchor.click()
    URL.revokeObjectURL(url)
  }

  async function pushOfflineToOnline() {
    setPushing(true)
    try {
      const res = await api.pushReportToErp(date)
      if (res.success) {
        toast.success(res.message || 'Đẩy dữ liệu ERP thành công')
      } else {
        toast.error(res.message || 'Đẩy dữ liệu ERP thất bại')
      }
    } catch {
      toast.error('Không thể đẩy dữ liệu điểm danh lên ERP')
    }
    setPushing(false)
  }

  async function handlePushButtonClick() {
    if (isInternalMode) {
      setSystemLoginError('')
      setShowSystemLoginModal(true)
      return
    }

    await pushOfflineToOnline()
  }

  async function handleSystemLoginSubmit(event) {
    event.preventDefault()
    setSystemLoginLoading(true)
    setSystemLoginError('')

    try {
      const res = await api.login(systemLoginForm.username, systemLoginForm.password, 'system')
      if (!res.success) {
        setSystemLoginError(res.message || 'Đăng nhập hệ thống thất bại')
        setSystemLoginLoading(false)
        return
      }

      setSessionToken(res.token)
      setAuthMode('system')
      setShowSystemLoginModal(false)
      toast.success('Đăng nhập hệ thống thành công')
      await pushOfflineToOnline()
    } catch {
      setSystemLoginError('Không thể đăng nhập hệ thống')
    }

    setSystemLoginLoading(false)
  }

  return (
    <div className="space-y-4 md:space-y-6">
      <SystemLoginModal
        open={showSystemLoginModal}
        form={systemLoginForm}
        setForm={setSystemLoginForm}
        loading={systemLoginLoading}
        error={systemLoginError}
        onClose={() => {
          if (!systemLoginLoading) {
            setShowSystemLoginModal(false)
          }
        }}
        onSubmit={handleSystemLoginSubmit}
      />

      <div>
        <h1 className="text-2xl font-bold text-gray-800">Báo cáo điểm danh</h1>
        {isInternalMode && (
          <p className="text-sm text-amber-700 mt-1">
            Đang ở chế độ nội bộ. Khi bấm đẩy dữ liệu, hệ thống sẽ yêu cầu đăng nhập hệ thống.
          </p>
        )}
      </div>

      <div className="grid sm:flex gap-3 items-stretch sm:items-center">
        <input
          type="date"
          value={date}
          onChange={event => setDate(event.target.value)}
          className="w-full sm:w-auto px-3 py-2 border border-gray-300 rounded-lg text-sm"
        />
        <button
          onClick={loadReport}
          disabled={loading}
          className="w-full sm:w-auto flex items-center justify-center gap-2 px-4 py-2 bg-primary-600 text-white rounded-lg text-sm font-medium hover:bg-primary-700 disabled:opacity-50"
        >
          <Search size={16} />
          Xem báo cáo
        </button>
        <button
          onClick={exportCSV}
          disabled={records.length === 0}
          className="w-full sm:w-auto flex items-center justify-center gap-2 px-4 py-2 bg-gray-100 text-gray-600 rounded-lg text-sm font-medium hover:bg-gray-200 disabled:opacity-50"
        >
          <Download size={16} />
          Xuất CSV
        </button>
        <button
          onClick={handlePushButtonClick}
          disabled={pushing || loading || records.length === 0}
          className="w-full sm:w-auto flex items-center justify-center gap-2 px-4 py-2 bg-emerald-600 text-white rounded-lg text-sm font-medium hover:bg-emerald-700 disabled:opacity-50"
        >
          <UploadCloud size={16} />
          {pushing ? 'Đang đẩy...' : 'Đẩy dữ liệu offline -> online'}
        </button>
      </div>

      <div className="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        {loading ? (
          <div className="px-5 py-12 text-center text-gray-400">Đang tải...</div>
        ) : (
          <div>
            <div className="hidden lg:block overflow-x-auto">
              <table className="w-full text-sm min-w-[920px]">
                <thead className="bg-gray-50">
                  <tr>
                    <th className="px-4 py-3 text-left font-medium text-gray-600">#</th>
                    <th className="px-4 py-3 text-left font-medium text-gray-600">Họ tên</th>
                    <th className="px-4 py-3 text-left font-medium text-gray-600">Mã NV</th>
                    <th className="px-4 py-3 text-left font-medium text-gray-600">Phòng ban</th>
                    <th className="px-4 py-3 text-left font-medium text-gray-600">Giờ vào</th>
                    <th className="px-4 py-3 text-left font-medium text-gray-600">Giờ ra</th>
                    <th className="px-4 py-3 text-left font-medium text-gray-600">Trạng thái</th>
                  </tr>
                </thead>
                <tbody className="divide-y divide-gray-50">
                  {paginatedRecords.map((record, index) => (
                    <tr key={`${record.employee_id}-${index}`} className="hover:bg-gray-50">
                      <td className="px-4 py-3 text-gray-400">{(page - 1) * pageSize + index + 1}</td>
                      <td className="px-4 py-3 font-medium text-gray-800">{record.name}</td>
                      <td className="px-4 py-3 text-gray-600">{record.employee_id}</td>
                      <td className="px-4 py-3 text-gray-600">{record.department}</td>
                      <td className="px-4 py-3 text-gray-600">{record.check_in_time}</td>
                      <td className="px-4 py-3 text-gray-600">{record.check_out_time || '-'}</td>
                      <td className="px-4 py-3">
                        <span className={`inline-block px-2 py-1 text-xs rounded-full font-medium ${getStatusTone(record.status)}`}>
                          {record.status}
                        </span>
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>

            <div className="lg:hidden divide-y divide-gray-100">
              {paginatedRecords.map((record, index) => (
                <div key={`${record.employee_id}-${index}`} className="px-4 py-3 sm:px-5">
                  <div className="flex items-start justify-between gap-3">
                    <div className="min-w-0">
                      <p className="font-semibold text-gray-800 truncate">{record.name}</p>
                      <p className="text-xs text-gray-500 mt-0.5">{record.employee_id} · {record.department || '-'}</p>
                      <p className="text-xs text-gray-400 mt-1">STT {(page - 1) * pageSize + index + 1}</p>
                    </div>
                    <span className={`shrink-0 inline-block px-2 py-1 text-[11px] rounded-full font-medium ${getStatusTone(record.status)}`}>
                      {record.status}
                    </span>
                  </div>

                  <div className="mt-2 grid grid-cols-2 gap-2 text-xs text-gray-600">
                    <p>Giờ vào: {record.check_in_time || '-'}</p>
                    <p>Giờ ra: {record.check_out_time || '-'}</p>
                  </div>
                </div>
              ))}
            </div>

            {records.length > 0 && (
              <div className="px-4 sm:px-5 py-4 border-t border-gray-200 flex items-center justify-between flex-wrap gap-4">
                <div className="flex items-center gap-2">
                  <span className="text-sm text-gray-500">Hiển thị</span>
                  <select
                    value={pageSize}
                    onChange={event => {
                      setPageSize(Number(event.target.value))
                      setPage(1)
                    }}
                    className="border border-gray-300 rounded-lg px-2 py-1 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary-500/20"
                  >
                    <option value={5}>5</option>
                    <option value={10}>10</option>
                    <option value={20}>20</option>
                    <option value={50}>50</option>
                    <option value={100}>100</option>
                  </select>
                  <span className="text-sm text-gray-500">trên tổng {records.length}</span>
                </div>

                {totalPages > 1 && (
                  <div className="flex gap-1">
                    <button
                      onClick={() => setPage(p => Math.max(1, p - 1))}
                      disabled={page === 1}
                      className="px-3 py-1 rounded-lg border border-gray-200 text-sm font-medium hover:bg-gray-50 disabled:opacity-50 text-gray-600"
                    >
                      Trước
                    </button>
                    {Array.from({ length: totalPages }, (_, i) => i + 1)
                      .filter(p => p === 1 || p === totalPages || Math.abs(p - page) <= 1)
                      .map((p, i, arr) => (
                        <React.Fragment key={p}>
                          {i > 0 && arr[i - 1] !== p - 1 && <span className="px-2 py-1 text-gray-400">...</span>}
                          <button
                            onClick={() => setPage(p)}
                            className={`px-3 py-1 rounded-lg text-sm font-medium transition-colors ${
                              page === p
                                ? 'bg-primary-600 text-white'
                                : 'border border-gray-200 text-gray-600 hover:bg-gray-50'
                            }`}
                          >
                            {p}
                          </button>
                        </React.Fragment>
                      ))}
                    <button
                      onClick={() => setPage(p => Math.min(totalPages, p + 1))}
                      disabled={page === totalPages}
                      className="px-3 py-1 rounded-lg border border-gray-200 text-sm font-medium hover:bg-gray-50 disabled:opacity-50 text-gray-600"
                    >
                      Sau
                    </button>
                  </div>
                )}
              </div>
            )}

            {records.length === 0 && (
              <div className="px-5 py-12 text-center text-gray-400">
                Không có dữ liệu điểm danh cho ngày này
              </div>
            )}
          </div>
        )}
      </div>
    </div>
  )
}
