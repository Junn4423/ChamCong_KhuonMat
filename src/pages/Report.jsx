import React, { useEffect, useMemo, useState } from 'react'
import { Download, RefreshCw, Search, UploadCloud, X } from 'lucide-react'
import { api, setSessionToken } from '../services/api'
import { useToast } from '../components/Toast'
import { ROUTES } from '../config/routes'
import ReportActionButton from '../components/ReportActionButton'

const DEFAULT_PUSH_STATUS = Object.freeze({
  key: 'unchecked',
  label: 'Chưa kiểm tra',
  missingTypes: [],
})

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

function getPushStatusTone(statusKey) {
  if (statusKey === 'synced') return 'bg-emerald-50 text-emerald-700 border-emerald-100'
  if (statusKey === 'partial') return 'bg-amber-50 text-amber-700 border-amber-100'
  if (statusKey === 'missing') return 'bg-rose-50 text-rose-700 border-rose-100'
  if (statusKey === 'no_data') return 'bg-slate-100 text-slate-600 border-slate-200'
  return 'bg-slate-100 text-slate-600 border-slate-200'
}

function describeLocationText(locationText) {
  const raw = String(locationText || '').trim()
  if (!raw) return '-'

  // Legacy location payloads may include "address | lat,lng | ±accuracy".
  // Report UI should only keep the first readable address segment.
  const cleaned = raw.split('|')[0]?.trim() || ''
  return cleaned || raw
}

function normalizeEmployeeId(value) {
  return String(value || '').trim().toUpperCase()
}

function normalizeDateText(value) {
  const raw = String(value || '').trim()
  if (!raw) return ''

  const normalized = raw.split(/[T\s]/)[0]
  if (/^\d{4}-\d{2}-\d{2}$/.test(normalized)) {
    return normalized
  }

  const vnDateMatch = normalized.match(/^(\d{2})\/(\d{2})\/(\d{4})$/)
  if (!vnDateMatch) {
    return ''
  }

  const [, day, month, year] = vnDateMatch
  return `${year}-${month}-${day}`
}

function normalizeTimeText(value) {
  const raw = String(value || '').trim()
  if (!raw) return ''

  const extracted = raw.split(/[T\s]/).pop() || raw
  const match = extracted.match(/^(\d{1,2}):(\d{1,2})(?::(\d{1,2}))?$/)
  if (!match) {
    return ''
  }

  const hour = Number(match[1])
  const minute = Number(match[2])
  const second = Number(match[3] || 0)
  if (
    Number.isNaN(hour)
    || Number.isNaN(minute)
    || Number.isNaN(second)
    || hour < 0
    || hour > 23
    || minute < 0
    || minute > 59
    || second < 0
    || second > 59
  ) {
    return ''
  }

  const pad = item => String(item).padStart(2, '0')
  return `${pad(hour)}:${pad(minute)}:${pad(second)}`
}

function normalizeAttendanceType(value) {
  return String(value || '').trim().toUpperCase() === 'OUT' ? 'OUT' : 'IN'
}

function buildAttendanceEventKey(employeeId, dateText, timeText, attendanceType) {
  const employee = normalizeEmployeeId(employeeId)
  const dateValue = normalizeDateText(dateText)
  const timeValue = normalizeTimeText(timeText)
  const typeValue = normalizeAttendanceType(attendanceType)

  if (!employee || !dateValue || !timeValue) {
    return ''
  }

  return `${employee}|${dateValue}|${timeValue}|${typeValue}`
}

function resolveRowKey(record, index) {
  if (record?.attendance_id != null) {
    return String(record.attendance_id)
  }
  return `${record?.employee_id || 'row'}-${record?.date || ''}-${record?.check_in_time || ''}-${index}`
}

function buildPushStatus(expectedTypes, matchedTypes) {
  const expected = Array.from(new Set(expectedTypes))
  const matched = new Set(matchedTypes)

  if (expected.length === 0) {
    return {
      key: 'no_data',
      label: 'Không có dữ liệu',
      missingTypes: [],
    }
  }

  const missingTypes = expected.filter(type => !matched.has(type))
  if (missingTypes.length === 0) {
    return {
      key: 'synced',
      label: expected.length > 1 ? 'Đã đẩy đủ' : 'Đã đẩy',
      missingTypes: [],
    }
  }

  if (missingTypes.length === expected.length) {
    return {
      key: 'missing',
      label: 'Chưa đẩy',
      missingTypes,
    }
  }

  return {
    key: 'partial',
    label: `Đẩy thiếu (${missingTypes.join(', ')})`,
    missingTypes,
  }
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
          <h2 className="text-base font-semibold text-slate-800">Đăng nhập hệ thống</h2>
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
            Để tiếp tục thao tác với ERP, vui lòng đăng nhập chế độ system.
          </p>

          {error && (
            <div className="px-3 py-2 rounded-lg border border-red-200 bg-red-50 text-sm text-red-600">
              {error}
            </div>
          )}

          <div>
            <label className="block text-sm font-medium text-slate-700 mb-1.5">Tài khoản</label>
            <input
              type="text"
              value={form.username}
              onChange={event => setForm(prev => ({ ...prev, username: event.target.value }))}
              className="w-full px-3 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-primary-500/30 focus:border-primary-500 text-sm"
              placeholder="Nhập tài khoản"
              required
            />
          </div>

          <div>
            <label className="block text-sm font-medium text-slate-700 mb-1.5">Mật khẩu</label>
            <input
              type="password"
              value={form.password}
              onChange={event => setForm(prev => ({ ...prev, password: event.target.value }))}
              className="w-full px-3 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-primary-500/30 focus:border-primary-500 text-sm"
              placeholder="Nhập mật khẩu"
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

export default function Report() {
  const { toast } = useToast()
  const [filters, setFilters] = useState(createDefaultFilters)
  const [records, setRecords] = useState([])
  const [summary, setSummary] = useState({ total_records: 0, unique_employees: 0 })
  const [filterDescription, setFilterDescription] = useState('')
  const [loading, setLoading] = useState(false)
  const [exporting, setExporting] = useState(false)
  const [pushing, setPushing] = useState(false)
  const [checkingData, setCheckingData] = useState(false)
  const [authMode, setAuthMode] = useState('system')
  const [pushStatusByRow, setPushStatusByRow] = useState({})
  const [checkSummary, setCheckSummary] = useState(null)
  const [pendingSystemAction, setPendingSystemAction] = useState(null)

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

  function resetPushStatusView() {
    setPushStatusByRow({})
    setCheckSummary(null)
  }

  function getRowPushStatus(record, index) {
    const rowKey = resolveRowKey(record, index)
    return pushStatusByRow[rowKey] || DEFAULT_PUSH_STATUS
  }

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
        resetPushStatusView()
      } else {
        toast.error(res.message || 'Không tải được báo cáo')
      }
    } catch (error) {
      console.error(error)
      toast.error(error?.message || 'Không thể kết nối backend')
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
      toast.success('Đã xuất file Excel')
    } catch (error) {
      toast.error(error?.message || 'Không xuất được file Excel')
    } finally {
      setExporting(false)
    }
  }

  async function pushOfflineToOnline() {
    setPushing(true)
    try {
      const res = await api.pushReportToErp(buildReportParams(filters))
      if (res.success) {
        toast.success(res.message || 'Đẩy dữ liệu ERP thành công')
      } else {
        toast.error(res.message || 'Đẩy dữ liệu ERP thất bại')
      }
    } catch (error) {
      toast.error(error?.message || 'Không thể đẩy dữ liệu điểm danh lên ERP')
    } finally {
      setPushing(false)
    }
  }

  async function collectOnlineEventKeys(reportRows) {
    const employeeIds = Array.from(
      new Set(reportRows.map(row => normalizeEmployeeId(row.employee_id)).filter(Boolean)),
    )

    const onlineEventKeys = new Set()
    for (const employeeId of employeeIds) {
      let pageCursor = 1
      let totalPagesRemote = 1
      let guard = 0

      do {
        guard += 1
        const res = await api.getOnlineAttendance({
          start_date: filters.startDate,
          end_date: filters.endDate,
          employee_id: employeeId,
          sort_by: 'date',
          sort_dir: 'desc',
          page: pageCursor,
          page_size: 500,
        })

        if (!res?.success) {
          throw new Error(res?.message || `Không tải được dữ liệu online của nhân viên ${employeeId}`)
        }

        const onlineRows = Array.isArray(res.records) ? res.records : []
        for (const row of onlineRows) {
          const eventKey = buildAttendanceEventKey(
            row?.employee_id,
            row?.attendance_date,
            row?.attendance_time,
            row?.attendance_type,
          )
          if (eventKey) {
            onlineEventKeys.add(eventKey)
          }
        }

        const parsedTotalPages = Number(res?.meta?.total_pages || 1)
        totalPagesRemote = Number.isFinite(parsedTotalPages) && parsedTotalPages > 0
          ? parsedTotalPages
          : 1

        pageCursor += 1
      } while (pageCursor <= totalPagesRemote && guard <= 100)
    }

    return onlineEventKeys
  }

  function buildPushStatusMap(reportRows, onlineEventKeys) {
    const statusByRow = {}
    const resultSummary = {
      totalRows: reportRows.length,
      syncedRows: 0,
      partialRows: 0,
      missingRows: 0,
      noDataRows: 0,
    }

    reportRows.forEach((record, index) => {
      const employeeId = normalizeEmployeeId(record.employee_id)
      const dateText = normalizeDateText(record.date || record.date_display)

      const expectedEvents = []
      const checkInEventKey = buildAttendanceEventKey(employeeId, dateText, record.check_in_time, 'IN')
      if (checkInEventKey) {
        expectedEvents.push({ type: 'IN', key: checkInEventKey })
      }

      const checkOutEventKey = buildAttendanceEventKey(employeeId, dateText, record.check_out_time, 'OUT')
      if (checkOutEventKey) {
        expectedEvents.push({ type: 'OUT', key: checkOutEventKey })
      }

      const expectedTypes = expectedEvents.map(item => item.type)
      const matchedTypes = expectedEvents
        .filter(item => onlineEventKeys.has(item.key))
        .map(item => item.type)

      const pushStatus = buildPushStatus(expectedTypes, matchedTypes)
      statusByRow[resolveRowKey(record, index)] = pushStatus

      if (pushStatus.key === 'synced') resultSummary.syncedRows += 1
      if (pushStatus.key === 'partial') resultSummary.partialRows += 1
      if (pushStatus.key === 'missing') resultSummary.missingRows += 1
      if (pushStatus.key === 'no_data') resultSummary.noDataRows += 1
    })

    return {
      statusByRow,
      resultSummary,
    }
  }

  async function checkPushStatusData() {
    if (records.length === 0) {
      toast.error('Không có dữ liệu báo cáo để kiểm tra')
      return
    }

    setCheckingData(true)
    try {
      const onlineEventKeys = await collectOnlineEventKeys(records)
      const { statusByRow, resultSummary } = buildPushStatusMap(records, onlineEventKeys)
      setPushStatusByRow(statusByRow)
      setCheckSummary({
        ...resultSummary,
        checkedAt: new Date().toLocaleString('vi-VN'),
      })

      const unresolvedRowsCount = resultSummary.partialRows + resultSummary.missingRows
      if (unresolvedRowsCount > 0) {
        toast.error(`Đã kiểm tra ${resultSummary.totalRows} dòng. Còn ${unresolvedRowsCount} dòng chưa đẩy đủ.`)
      } else {
        toast.success(`Đã kiểm tra ${resultSummary.totalRows} dòng. Dữ liệu đã đẩy đầy đủ.`)
      }
    } catch (error) {
      console.error(error)
      toast.error(error?.message || 'Không kiểm tra được trạng thái đẩy ERP')
    } finally {
      setCheckingData(false)
    }
  }

  async function handlePushButtonClick() {
    if (isInternalMode) {
      setPendingSystemAction('push')
      setSystemLoginError('')
      setShowSystemLoginModal(true)
      return
    }

    setPendingSystemAction(null)
    await pushOfflineToOnline()
  }

  async function handleCheckDataButtonClick() {
    if (isInternalMode) {
      setPendingSystemAction('check')
      setSystemLoginError('')
      setShowSystemLoginModal(true)
      return
    }

    setPendingSystemAction(null)
    await checkPushStatusData()
  }

  async function handleSystemLoginSubmit(event) {
    event.preventDefault()
    setSystemLoginLoading(true)
    setSystemLoginError('')

    try {
      const res = await api.login(systemLoginForm.username, systemLoginForm.password, 'system')
      if (!res.success) {
        setSystemLoginError(res.message || 'Đăng nhập hệ thống thất bại')
        return
      }

      const nextAction = pendingSystemAction

      setSessionToken(res.token)
      setAuthMode('system')
      setShowSystemLoginModal(false)
      setPendingSystemAction(null)
      toast.success('Đăng nhập hệ thống thành công')
      if (nextAction === 'check') {
        await checkPushStatusData()
      } else {
        await pushOfflineToOnline()
      }
    } catch {
      setSystemLoginError('Không thể đăng nhập hệ thống')
    } finally {
      setSystemLoginLoading(false)
    }
  }

  const unresolvedRows = checkSummary
    ? (checkSummary.partialRows + checkSummary.missingRows)
    : 0

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
            setPendingSystemAction(null)
          }
        }}
        onSubmit={handleSystemLoginSubmit}
      />

      <div>
        <h1 className="text-2xl font-bold text-slate-800">Báo cáo điểm danh</h1>
        <p className="text-sm text-slate-500 mt-1">
          Lọc theo khoảng ngày, giờ vào, trạng thái; sau đó sắp xếp và xuất Excel theo đúng bộ dữ liệu đang xem.
        </p>
        {isInternalMode && (
          <p className="text-sm text-amber-700 mt-1">
            Đang ở chế độ nội bộ. Khi kiểm tra data hoặc đẩy dữ liệu, hệ thống sẽ yêu cầu đăng nhập system.
          </p>
        )}
      </div>

      <div className="bg-white rounded-2xl shadow-sm border border-slate-200/60 p-4 sm:p-5 space-y-4">
        <div className="grid md:grid-cols-2 xl:grid-cols-4 gap-3">
          <div>
            <label className="block text-xs font-medium text-slate-500 mb-1.5 uppercase tracking-wide">Từ ngày</label>
            <input
              type="date"
              value={filters.startDate}
              onChange={event => setFilters(prev => ({ ...prev, startDate: event.target.value }))}
              className="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm bg-white focus:ring-2 focus:ring-primary-500/20 focus:border-primary-400"
            />
          </div>

          <div>
            <label className="block text-xs font-medium text-slate-500 mb-1.5 uppercase tracking-wide">Đến ngày</label>
            <input
              type="date"
              value={filters.endDate}
              onChange={event => setFilters(prev => ({ ...prev, endDate: event.target.value }))}
              className="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm bg-white focus:ring-2 focus:ring-primary-500/20 focus:border-primary-400"
            />
          </div>

          <div>
            <label className="block text-xs font-medium text-slate-500 mb-1.5 uppercase tracking-wide">Giờ vào từ</label>
            <input
              type="time"
              value={filters.startTime}
              onChange={event => setFilters(prev => ({ ...prev, startTime: event.target.value }))}
              className="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm bg-white focus:ring-2 focus:ring-primary-500/20 focus:border-primary-400"
            />
          </div>

          <div>
            <label className="block text-xs font-medium text-slate-500 mb-1.5 uppercase tracking-wide">Giờ vào đến</label>
            <input
              type="time"
              value={filters.endTime}
              onChange={event => setFilters(prev => ({ ...prev, endTime: event.target.value }))}
              className="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm bg-white focus:ring-2 focus:ring-primary-500/20 focus:border-primary-400"
            />
          </div>

          <div>
            <label className="block text-xs font-medium text-slate-500 mb-1.5 uppercase tracking-wide">Trạng thái</label>
            <select
              value={filters.status}
              onChange={event => setFilters(prev => ({ ...prev, status: event.target.value }))}
              className="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm bg-white focus:ring-2 focus:ring-primary-500/20 focus:border-primary-400"
            >
              <option value="all">Tất cả</option>
              <option value="present">Đúng giờ</option>
              <option value="late">Trễ</option>
              <option value="checked_out">Đã checkout</option>
            </select>
          </div>

          <div>
            <label className="block text-xs font-medium text-slate-500 mb-1.5 uppercase tracking-wide">Tìm nhân viên</label>
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
              placeholder="Mã NV, tên, phòng ban..."
              className="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm bg-white focus:ring-2 focus:ring-primary-500/20 focus:border-primary-400"
            />
          </div>

          <div>
            <label className="block text-xs font-medium text-slate-500 mb-1.5 uppercase tracking-wide">Sắp xếp theo</label>
            <select
              value={filters.sortBy}
              onChange={event => setFilters(prev => ({ ...prev, sortBy: event.target.value }))}
              className="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm bg-white focus:ring-2 focus:ring-primary-500/20 focus:border-primary-400"
            >
              <option value="check_in_time">Giờ vào</option>
              <option value="check_out_time">Giờ ra</option>
              <option value="date">Ngày</option>
              <option value="name">Họ tên</option>
              <option value="employee_id">Mã NV</option>
              <option value="department">Phòng ban</option>
              <option value="status">Trạng thái</option>
            </select>
          </div>

          <div>
            <label className="block text-xs font-medium text-slate-500 mb-1.5 uppercase tracking-wide">Thứ tự</label>
            <select
              value={filters.sortDir}
              onChange={event => setFilters(prev => ({ ...prev, sortDir: event.target.value }))}
              className="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm bg-white focus:ring-2 focus:ring-primary-500/20 focus:border-primary-400"
            >
              <option value="desc">Giảm dần</option>
              <option value="asc">Tăng dần</option>
            </select>
          </div>
        </div>

        <div className="flex flex-wrap gap-2">
          <ReportActionButton to={ROUTES.onlineAttendanceCheck}>
            Kiểm tra dữ liệu chấm công online
          </ReportActionButton>

          <ReportActionButton
            onClick={handleCheckDataButtonClick}
            disabled={checkingData || loading || records.length === 0}
            icon={Search}
          >
            {checkingData ? 'Đang kiểm tra...' : 'Kiểm tra data'}
          </ReportActionButton>

          <ReportActionButton onClick={handleApplyFilters} disabled={loading} icon={Search}>
            {loading ? 'Đang tải...' : 'Xem báo cáo'}
          </ReportActionButton>

          <ReportActionButton onClick={handleResetFilters} disabled={loading} icon={RefreshCw}>
            Đặt lại bộ lọc
          </ReportActionButton>

          <ReportActionButton
            onClick={handleExportExcel}
            disabled={exporting || loading || records.length === 0}
            icon={Download}
          >
            {exporting ? 'Đang xuất...' : 'Xuất Excel'}
          </ReportActionButton>

          <ReportActionButton
            onClick={handlePushButtonClick}
            disabled={pushing || loading || records.length === 0}
            icon={UploadCloud}
          >
            {pushing ? 'Đang đẩy...' : 'Đẩy dữ liệu offline -> online'}
          </ReportActionButton>
        </div>

        <div className="grid sm:grid-cols-2 xl:grid-cols-4 gap-3">
          <div className="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
            <p className="text-xs uppercase tracking-wide text-slate-500">Tổng bản ghi</p>
            <p className="mt-1 text-2xl font-bold text-slate-800">{summary.total_records || 0}</p>
          </div>
          <div className="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
            <p className="text-xs uppercase tracking-wide text-slate-500">Nhân viên</p>
            <p className="mt-1 text-2xl font-bold text-slate-800">{summary.unique_employees || 0}</p>
          </div>
          <div className="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
            <p className="text-xs uppercase tracking-wide text-slate-500">Bộ lọc hiện tại</p>
            <p className="mt-1 text-sm text-slate-700 leading-6">{filterDescription || 'Chưa có mô tả bộ lọc'}</p>
          </div>
          <div className="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
            <p className="text-xs uppercase tracking-wide text-slate-500">Trạng thái đẩy</p>
            {checkSummary ? (
              <div className="mt-1 space-y-1">
                <p className="text-sm font-semibold text-slate-800">
                  Đã đẩy đủ: {checkSummary.syncedRows}/{checkSummary.totalRows}
                </p>
                <p className="text-xs text-slate-600">
                  Chưa đẩy đủ: {unresolvedRows} (thiếu: {checkSummary.partialRows}, chưa đẩy: {checkSummary.missingRows})
                </p>
                <p className="text-xs text-slate-500">Lần kiểm tra: {checkSummary.checkedAt}</p>
              </div>
            ) : (
              <p className="mt-1 text-sm text-slate-700 leading-6">Chưa kiểm tra data ERP.</p>
            )}
          </div>
        </div>
      </div>

      <div className="bg-white rounded-2xl shadow-sm border border-slate-200/60 overflow-hidden">
        {loading ? (
          <div className="px-5 py-12 text-center text-slate-400">Đang tải...</div>
        ) : (
          <div>
            <div className="hidden xl:block overflow-x-auto">
              <table className="w-full text-sm min-w-[1480px]">
                <thead className="bg-slate-50">
                  <tr>
                    <th className="px-4 py-3 text-left font-medium text-slate-600">#</th>
                    <th className="px-4 py-3 text-left font-medium text-slate-600">Ngày</th>
                    <th className="px-4 py-3 text-left font-medium text-slate-600">Họ tên</th>
                    <th className="px-4 py-3 text-left font-medium text-slate-600">Mã NV</th>
                    <th className="px-4 py-3 text-left font-medium text-slate-600">Phòng ban</th>
                    <th className="px-4 py-3 text-left font-medium text-slate-600">Giờ vào</th>
                    <th className="px-4 py-3 text-left font-medium text-slate-600">Giờ ra</th>
                    <th className="px-4 py-3 text-left font-medium text-slate-600">Vị trí checkin</th>
                    <th className="px-4 py-3 text-left font-medium text-slate-600">Vị trí checkout</th>
                    <th className="px-4 py-3 text-left font-medium text-slate-600">Trạng thái</th>
                    <th className="px-4 py-3 text-left font-medium text-slate-600">Trạng thái đẩy</th>
                  </tr>
                </thead>
                <tbody className="divide-y divide-slate-50">
                  {paginatedRecords.map((record, index) => {
                    const pushStatus = getRowPushStatus(record, index)
                    return (
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
                        <td className="px-4 py-3">
                          <span className={`inline-flex px-2.5 py-1 rounded-full text-xs font-medium border ${getPushStatusTone(pushStatus.key)}`}>
                            {pushStatus.label}
                          </span>
                        </td>
                      </tr>
                    )
                  })}
                </tbody>
              </table>
            </div>

            <div className="xl:hidden divide-y divide-slate-100">
              {paginatedRecords.map((record, index) => {
                const pushStatus = getRowPushStatus(record, index)
                return (
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
                      <div className="shrink-0 flex flex-col items-end gap-1">
                        <span className={`inline-flex px-2 py-1 text-[11px] rounded-full font-medium border ${getStatusTone(record.status_key)}`}>
                          {record.status || '-'}
                        </span>
                        <span className={`inline-flex px-2 py-1 text-[11px] rounded-full font-medium border ${getPushStatusTone(pushStatus.key)}`}>
                          {pushStatus.label}
                        </span>
                      </div>
                    </div>

                    <div className="mt-3 grid grid-cols-2 gap-2 text-xs text-slate-600">
                      <p>Giờ vào: {record.check_in_time || '-'}</p>
                      <p>Giờ ra: {record.check_out_time || '-'}</p>
                      <p className="col-span-2">Checkin: {describeLocationText(record.check_in_location)}</p>
                      <p className="col-span-2">Checkout: {describeLocationText(record.check_out_location)}</p>
                    </div>
                  </div>
                )
              })}
            </div>

            {records.length > 0 && (
              <div className="px-4 sm:px-5 py-4 border-t border-slate-200 flex items-center justify-between flex-wrap gap-4">
                <div className="flex items-center gap-2">
                  <span className="text-sm text-slate-500">Hiển thị</span>
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
                  <span className="text-sm text-slate-500">trên tổng {records.length}</span>
                </div>

                {totalPages > 1 && (
                  <div className="flex gap-1">
                    <button
                      onClick={() => setPage(current => Math.max(1, current - 1))}
                      disabled={page === 1}
                      className="px-3 py-1 rounded-lg border border-slate-200 text-sm font-medium hover:bg-slate-50 disabled:opacity-50 text-slate-600"
                    >
                      Trước
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
                Không có dữ liệu điểm danh theo bộ lọc hiện tại
              </div>
            )}
          </div>
        )}
      </div>
    </div>
  )
}
