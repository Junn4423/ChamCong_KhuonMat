import React, { useEffect, useMemo, useState } from 'react'
import { Download, RefreshCw, Search, UploadCloud, X } from 'lucide-react'
import { api, setSessionToken } from '../services/api'
import { useToast } from '../components/Toast'

function todayString() {
  return new Date().toISOString().split('T')[0]
}

function createDefaultFilters() {
  const today = todayString()
  return {
    startDate: today,
    endDate: today,
    startTime: '',
    endTime: '',
    status: 'all',
    keyword: '',
    sortBy: 'check_in_time',
    sortDir: 'desc',
  }
}

function buildReportParams(filters) {
  return {
    start_date: filters.startDate,
    end_date: filters.endDate,
    start_time: filters.startTime,
    end_time: filters.endTime,
    status: filters.status,
    keyword: filters.keyword,
    sort_by: filters.sortBy,
    sort_dir: filters.sortDir,
  }
}

function downloadBlob(blob, filename) {
  const url = URL.createObjectURL(blob)
  const anchor = document.createElement('a')
  anchor.href = url
  anchor.download = filename || `attendance_${todayString()}.xlsx`
  anchor.click()
  URL.revokeObjectURL(url)
}

function getStatusTone(statusKey) {
  if (statusKey === 'present') return 'bg-emerald-50 text-emerald-700 border-emerald-100'
  if (statusKey === 'checked_out') return 'bg-sky-50 text-sky-700 border-sky-100'
  return 'bg-amber-50 text-amber-700 border-amber-100'
}

function describeLocationText(locationText) {
  const raw = String(locationText || '').trim()
  if (!raw) return '-'

  const pattern = /^(.*?)\s*\(([-\d.]+),\s*([-\d.]+)(?:\s*[Â±+-](\d+)m)?\)$/i
  const match = raw.match(pattern)
  if (!match) return raw

  const label = (match[1] || '').trim()
  const lat = match[2]
  const lng = match[3]
  const accuracy = match[4]

  const parts = []
  if (label) parts.push(label)
  parts.push(`${lat}, ${lng}`)
  if (accuracy) parts.push(`±${accuracy}m`)
  return parts.join(' | ')
}

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
          <h2 className="text-base font-semibold text-slate-800">Dang nhap he thong</h2>
          <button
            onClick={onClose}
            className="w-8 h-8 rounded-lg bg-slate-100 text-slate-500 hover:bg-slate-200 flex items-center justify-center transition-colors"
            disabled={loading}
          >
            <X size={16} />
          </button>
        </div>

        <form onSubmit={onSubmit} className="p-5 space-y-4">
          <p className="text-sm text-slate-600">
            De day du lieu cham cong len he thong, vui long dang nhap che do system.
          </p>

          {error && (
            <div className="px-3 py-2 rounded-lg border border-red-200 bg-red-50 text-sm text-red-600">
              {error}
            </div>
          )}

          <div>
            <label className="block text-sm font-medium text-slate-700 mb-1.5">Tai khoan</label>
            <input
              type="text"
              value={form.username}
              onChange={event => setForm(prev => ({ ...prev, username: event.target.value }))}
              className="w-full px-3 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-primary-500/30 focus:border-primary-500 text-sm"
              placeholder="Nhap tai khoan"
              required
            />
          </div>

          <div>
            <label className="block text-sm font-medium text-slate-700 mb-1.5">Mat khau</label>
            <input
              type="password"
              value={form.password}
              onChange={event => setForm(prev => ({ ...prev, password: event.target.value }))}
              className="w-full px-3 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-primary-500/30 focus:border-primary-500 text-sm"
              placeholder="Nhap mat khau"
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
              Huy
            </button>
            <button
              type="submit"
              disabled={loading}
              className="w-full sm:w-auto px-4 py-2 rounded-xl bg-primary-600 text-white text-sm font-medium hover:bg-primary-700 transition-colors disabled:opacity-50"
            >
              {loading ? 'Dang dang nhap...' : 'Dang nhap va tiep tuc'}
            </button>
          </div>
        </form>
      </div>
    </div>
  )
}

export default function Report() {
  const { toast } = useToast()
  const [filters, setFilters] = useState(createDefaultFilters)
  const [records, setRecords] = useState([])
  const [summary, setSummary] = useState({ total_records: 0, unique_employees: 0 })
  const [filterDescription, setFilterDescription] = useState('')
  const [loading, setLoading] = useState(false)
  const [exporting, setExporting] = useState(false)
  const [pushing, setPushing] = useState(false)
  const [authMode, setAuthMode] = useState('system')

  const [showSystemLoginModal, setShowSystemLoginModal] = useState(false)
  const [systemLoginLoading, setSystemLoginLoading] = useState(false)
  const [systemLoginError, setSystemLoginError] = useState('')
  const [systemLoginForm, setSystemLoginForm] = useState({ username: '', password: '' })

  const [page, setPage] = useState(1)
  const [pageSize, setPageSize] = useState(10)

  useEffect(() => {
    loadAuthMode()
    loadReport(createDefaultFilters())
  }, [])

  const totalPages = Math.max(1, Math.ceil(records.length / pageSize))

  const paginatedRecords = useMemo(() => {
    const startIndex = (page - 1) * pageSize
    return records.slice(startIndex, startIndex + pageSize)
  }, [records, page, pageSize])

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

  async function loadReport(nextFilters = filters) {
    setLoading(true)
    try {
      const requestParams = buildReportParams(nextFilters)
      const res = await api.getReport(requestParams)
      if (res.success) {
        setRecords(res.records || [])
        setSummary(res.summary || { total_records: 0, unique_employees: 0 })
        setFilterDescription(res.filters?.description || '')
      } else {
        toast.error(res.message || 'Khong tai duoc bao cao')
      }
    } catch (error) {
      console.error(error)
      toast.error(error?.message || 'Khong the ket noi backend')
    } finally {
      setLoading(false)
    }
  }

  async function handleApplyFilters() {
    setPage(1)
    await loadReport(filters)
  }

  async function handleResetFilters() {
    const nextFilters = createDefaultFilters()
    setFilters(nextFilters)
    setPage(1)
    await loadReport(nextFilters)
  }

  async function handleExportExcel() {
    if (records.length === 0) return
    setExporting(true)
    try {
      const { blob, filename } = await api.exportReportExcel(buildReportParams(filters))
      downloadBlob(blob, filename)
      toast.success('Da xuat file Excel')
    } catch (error) {
      toast.error(error?.message || 'Khong xuat duoc file Excel')
    } finally {
      setExporting(false)
    }
  }

  async function pushOfflineToOnline() {
    setPushing(true)
    try {
      const res = await api.pushReportToErp(buildReportParams(filters))
      if (res.success) {
        toast.success(res.message || 'Day du lieu ERP thanh cong')
      } else {
        toast.error(res.message || 'Day du lieu ERP that bai')
      }
    } catch (error) {
      toast.error(error?.message || 'Khong the day du lieu diem danh len ERP')
    } finally {
      setPushing(false)
    }
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
        setSystemLoginError(res.message || 'Dang nhap he thong that bai')
        return
      }

      setSessionToken(res.token)
      setAuthMode('system')
      setShowSystemLoginModal(false)
      toast.success('Dang nhap he thong thanh cong')
      await pushOfflineToOnline()
    } catch {
      setSystemLoginError('Khong the dang nhap he thong')
    } finally {
      setSystemLoginLoading(false)
    }
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
        <h1 className="text-2xl font-bold text-slate-800">Bao cao diem danh</h1>
        <p className="text-sm text-slate-500 mt-1">
          Loc theo khoang ngay, gio vao, trang thai; sau do sort va xuat Excel theo dung bo du lieu dang xem.
        </p>
        {isInternalMode && (
          <p className="text-sm text-amber-700 mt-1">
            Dang o che do noi bo. Khi day du lieu, he thong se yeu cau dang nhap system.
          </p>
        )}
      </div>

      <div className="bg-white rounded-2xl shadow-sm border border-slate-200/60 p-4 sm:p-5 space-y-4">
        <div className="grid md:grid-cols-2 xl:grid-cols-4 gap-3">
          <div>
            <label className="block text-xs font-medium text-slate-500 mb-1.5 uppercase tracking-wide">Tu ngay</label>
            <input
              type="date"
              value={filters.startDate}
              onChange={event => setFilters(prev => ({ ...prev, startDate: event.target.value }))}
              className="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm bg-white focus:ring-2 focus:ring-primary-500/20 focus:border-primary-400"
            />
          </div>

          <div>
            <label className="block text-xs font-medium text-slate-500 mb-1.5 uppercase tracking-wide">Den ngay</label>
            <input
              type="date"
              value={filters.endDate}
              onChange={event => setFilters(prev => ({ ...prev, endDate: event.target.value }))}
              className="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm bg-white focus:ring-2 focus:ring-primary-500/20 focus:border-primary-400"
            />
          </div>

          <div>
            <label className="block text-xs font-medium text-slate-500 mb-1.5 uppercase tracking-wide">Gio vao tu</label>
            <input
              type="time"
              value={filters.startTime}
              onChange={event => setFilters(prev => ({ ...prev, startTime: event.target.value }))}
              className="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm bg-white focus:ring-2 focus:ring-primary-500/20 focus:border-primary-400"
            />
          </div>

          <div>
            <label className="block text-xs font-medium text-slate-500 mb-1.5 uppercase tracking-wide">Gio vao den</label>
            <input
              type="time"
              value={filters.endTime}
              onChange={event => setFilters(prev => ({ ...prev, endTime: event.target.value }))}
              className="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm bg-white focus:ring-2 focus:ring-primary-500/20 focus:border-primary-400"
            />
          </div>

          <div>
            <label className="block text-xs font-medium text-slate-500 mb-1.5 uppercase tracking-wide">Trang thai</label>
            <select
              value={filters.status}
              onChange={event => setFilters(prev => ({ ...prev, status: event.target.value }))}
              className="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm bg-white focus:ring-2 focus:ring-primary-500/20 focus:border-primary-400"
            >
              <option value="all">Tat ca</option>
              <option value="present">Dung gio</option>
              <option value="late">Tre</option>
              <option value="checked_out">Da checkout</option>
            </select>
          </div>

          <div>
            <label className="block text-xs font-medium text-slate-500 mb-1.5 uppercase tracking-wide">Tim nhan vien</label>
            <input
              type="text"
              value={filters.keyword}
              onChange={event => setFilters(prev => ({ ...prev, keyword: event.target.value }))}
              onKeyDown={event => {
                if (event.key === 'Enter') {
                  event.preventDefault()
                  handleApplyFilters()
                }
              }}
              placeholder="Ma NV, ten, phong ban..."
              className="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm bg-white focus:ring-2 focus:ring-primary-500/20 focus:border-primary-400"
            />
          </div>

          <div>
            <label className="block text-xs font-medium text-slate-500 mb-1.5 uppercase tracking-wide">Sort theo</label>
            <select
              value={filters.sortBy}
              onChange={event => setFilters(prev => ({ ...prev, sortBy: event.target.value }))}
              className="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm bg-white focus:ring-2 focus:ring-primary-500/20 focus:border-primary-400"
            >
              <option value="check_in_time">Gio vao</option>
              <option value="check_out_time">Gio ra</option>
              <option value="date">Ngay</option>
              <option value="name">Ho ten</option>
              <option value="employee_id">Ma NV</option>
              <option value="department">Phong ban</option>
              <option value="status">Trang thai</option>
            </select>
          </div>

          <div>
            <label className="block text-xs font-medium text-slate-500 mb-1.5 uppercase tracking-wide">Thu tu</label>
            <select
              value={filters.sortDir}
              onChange={event => setFilters(prev => ({ ...prev, sortDir: event.target.value }))}
              className="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm bg-white focus:ring-2 focus:ring-primary-500/20 focus:border-primary-400"
            >
              <option value="desc">Giam dan</option>
              <option value="asc">Tang dan</option>
            </select>
          </div>
        </div>

        <div className="flex flex-wrap gap-2">
          <button
            onClick={handleApplyFilters}
            disabled={loading}
            className="w-full sm:w-auto flex items-center justify-center gap-2 px-4 py-2.5 bg-primary-600 text-white rounded-xl text-sm font-semibold hover:bg-primary-700 disabled:opacity-50 transition-colors"
          >
            <Search size={16} />
            {loading ? 'Dang tai...' : 'Xem bao cao'}
          </button>

          <button
            onClick={handleResetFilters}
            disabled={loading}
            className="w-full sm:w-auto flex items-center justify-center gap-2 px-4 py-2.5 bg-slate-100 text-slate-700 rounded-xl text-sm font-semibold hover:bg-slate-200 disabled:opacity-50 transition-colors"
          >
            <RefreshCw size={16} />
            Dat lai bo loc
          </button>

          <button
            onClick={handleExportExcel}
            disabled={exporting || loading || records.length === 0}
            className="w-full sm:w-auto flex items-center justify-center gap-2 px-4 py-2.5 bg-emerald-600 text-white rounded-xl text-sm font-semibold hover:bg-emerald-700 disabled:opacity-50 transition-colors"
          >
            <Download size={16} />
            {exporting ? 'Dang xuat...' : 'Xuat Excel'}
          </button>

          <button
            onClick={handlePushButtonClick}
            disabled={pushing || loading || records.length === 0}
            className="w-full sm:w-auto flex items-center justify-center gap-2 px-4 py-2.5 bg-sky-600 text-white rounded-xl text-sm font-semibold hover:bg-sky-700 disabled:opacity-50 transition-colors"
          >
            <UploadCloud size={16} />
            {pushing ? 'Dang day...' : 'Day du lieu offline -> online'}
          </button>
        </div>

        <div className="grid sm:grid-cols-3 gap-3">
          <div className="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
            <p className="text-xs uppercase tracking-wide text-slate-500">Tong ban ghi</p>
            <p className="mt-1 text-2xl font-bold text-slate-800">{summary.total_records || 0}</p>
          </div>
          <div className="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
            <p className="text-xs uppercase tracking-wide text-slate-500">Nhan vien</p>
            <p className="mt-1 text-2xl font-bold text-slate-800">{summary.unique_employees || 0}</p>
          </div>
          <div className="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
            <p className="text-xs uppercase tracking-wide text-slate-500">Bo loc hien tai</p>
            <p className="mt-1 text-sm text-slate-700 leading-6">{filterDescription || 'Chua co mo ta bo loc'}</p>
          </div>
        </div>
      </div>

      <div className="bg-white rounded-2xl shadow-sm border border-slate-200/60 overflow-hidden">
        {loading ? (
          <div className="px-5 py-12 text-center text-slate-400">Dang tai...</div>
        ) : (
          <div>
            <div className="hidden xl:block overflow-x-auto">
              <table className="w-full text-sm min-w-[1360px]">
                <thead className="bg-slate-50">
                  <tr>
                    <th className="px-4 py-3 text-left font-medium text-slate-600">#</th>
                    <th className="px-4 py-3 text-left font-medium text-slate-600">Ngay</th>
                    <th className="px-4 py-3 text-left font-medium text-slate-600">Ho ten</th>
                    <th className="px-4 py-3 text-left font-medium text-slate-600">Ma NV</th>
                    <th className="px-4 py-3 text-left font-medium text-slate-600">Phong ban</th>
                    <th className="px-4 py-3 text-left font-medium text-slate-600">Gio vao</th>
                    <th className="px-4 py-3 text-left font-medium text-slate-600">Gio ra</th>
                    <th className="px-4 py-3 text-left font-medium text-slate-600">Vi tri checkin</th>
                    <th className="px-4 py-3 text-left font-medium text-slate-600">Vi tri checkout</th>
                    <th className="px-4 py-3 text-left font-medium text-slate-600">Trang thai</th>
                  </tr>
                </thead>
                <tbody className="divide-y divide-slate-50">
                  {paginatedRecords.map((record, index) => (
                    <tr key={`${record.attendance_id}-${index}`} className="hover:bg-slate-50">
                      <td className="px-4 py-3 text-slate-400">{(page - 1) * pageSize + index + 1}</td>
                      <td className="px-4 py-3 text-slate-600">{record.date_display || record.date || '-'}</td>
                      <td className="px-4 py-3 font-medium text-slate-800">{record.name || '-'}</td>
                      <td className="px-4 py-3 text-slate-600">{record.employee_id || '-'}</td>
                      <td className="px-4 py-3 text-slate-600">{record.department || '-'}</td>
                      <td className="px-4 py-3 text-slate-600">{record.check_in_time || '-'}</td>
                      <td className="px-4 py-3 text-slate-600">{record.check_out_time || '-'}</td>
                      <td className="px-4 py-3 text-slate-600 max-w-[260px] whitespace-normal break-words">
                        {describeLocationText(record.check_in_location)}
                      </td>
                      <td className="px-4 py-3 text-slate-600 max-w-[260px] whitespace-normal break-words">
                        {describeLocationText(record.check_out_location)}
                      </td>
                      <td className="px-4 py-3">
                        <span className={`inline-flex px-2.5 py-1 rounded-full text-xs font-medium border ${getStatusTone(record.status_key)}`}>
                          {record.status || '-'}
                        </span>
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>

            <div className="xl:hidden divide-y divide-slate-100">
              {paginatedRecords.map((record, index) => (
                <div key={`${record.attendance_id}-${index}`} className="px-4 py-4 sm:px-5">
                  <div className="flex items-start justify-between gap-3">
                    <div className="min-w-0">
                      <p className="font-semibold text-slate-800 truncate">{record.name || '-'}</p>
                      <p className="text-xs text-slate-500 mt-0.5">
                        {(record.date_display || record.date || '-')}{' '}
                        | {record.employee_id || '-'} | {record.department || '-'}
                      </p>
                      <p className="text-xs text-slate-400 mt-1">STT {(page - 1) * pageSize + index + 1}</p>
                    </div>
                    <span className={`shrink-0 inline-flex px-2 py-1 text-[11px] rounded-full font-medium border ${getStatusTone(record.status_key)}`}>
                      {record.status || '-'}
                    </span>
                  </div>

                  <div className="mt-3 grid grid-cols-2 gap-2 text-xs text-slate-600">
                    <p>Gio vao: {record.check_in_time || '-'}</p>
                    <p>Gio ra: {record.check_out_time || '-'}</p>
                    <p className="col-span-2">Checkin: {describeLocationText(record.check_in_location)}</p>
                    <p className="col-span-2">Checkout: {describeLocationText(record.check_out_location)}</p>
                  </div>
                </div>
              ))}
            </div>

            {records.length > 0 && (
              <div className="px-4 sm:px-5 py-4 border-t border-slate-200 flex items-center justify-between flex-wrap gap-4">
                <div className="flex items-center gap-2">
                  <span className="text-sm text-slate-500">Hien thi</span>
                  <select
                    value={pageSize}
                    onChange={event => {
                      setPageSize(Number(event.target.value))
                      setPage(1)
                    }}
                    className="border border-slate-200 rounded-lg px-2 py-1 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-primary-500/20"
                  >
                    <option value={5}>5</option>
                    <option value={10}>10</option>
                    <option value={20}>20</option>
                    <option value={50}>50</option>
                    <option value={100}>100</option>
                  </select>
                  <span className="text-sm text-slate-500">tren tong {records.length}</span>
                </div>

                {totalPages > 1 && (
                  <div className="flex gap-1">
                    <button
                      onClick={() => setPage(current => Math.max(1, current - 1))}
                      disabled={page === 1}
                      className="px-3 py-1 rounded-lg border border-slate-200 text-sm font-medium hover:bg-slate-50 disabled:opacity-50 text-slate-600"
                    >
                      Truoc
                    </button>

                    {Array.from({ length: totalPages }, (_, i) => i + 1)
                      .filter(item => item === 1 || item === totalPages || Math.abs(item - page) <= 1)
                      .map((item, index, arr) => (
                        <React.Fragment key={item}>
                          {index > 0 && arr[index - 1] !== item - 1 && (
                            <span className="px-2 py-1 text-slate-400">...</span>
                          )}
                          <button
                            onClick={() => setPage(item)}
                            className={`px-3 py-1 rounded-lg text-sm font-medium transition-colors ${
                              page === item
                                ? 'bg-primary-600 text-white'
                                : 'border border-slate-200 text-slate-600 hover:bg-slate-50'
                            }`}
                          >
                            {item}
                          </button>
                        </React.Fragment>
                      ))}

                    <button
                      onClick={() => setPage(current => Math.min(totalPages, current + 1))}
                      disabled={page === totalPages}
                      className="px-3 py-1 rounded-lg border border-slate-200 text-sm font-medium hover:bg-slate-50 disabled:opacity-50 text-slate-600"
                    >
                      Sau
                    </button>
                  </div>
                )}
              </div>
            )}

            {records.length === 0 && (
              <div className="px-5 py-12 text-center text-slate-400">
                Khong co du lieu diem danh theo bo loc hien tai
              </div>
            )}
          </div>
        )}
      </div>
    </div>
  )
}
