import React, { useEffect, useMemo, useRef, useState } from 'react'
import { useNavigate } from 'react-router-dom'
import { Image as ImageIcon, X, Loader2, RotateCw } from 'lucide-react'
import { api } from '../services/api'
import { useToast } from '../components/Toast'

function getManageStatusBadge(employee) {
  const statusCode = (employee?.status_code || '').toLowerCase()

  if (statusCode === 'ready' || employee?.has_face) {
    return {
      label: employee?.status_text || 'Đã đăng ký khuôn mặt',
      tone: 'bg-emerald-50 text-emerald-700 border-emerald-200',
    }
  }

  if (statusCode === 'image_only' || employee?.has_local_image) {
    return {
      label: employee?.status_text || 'Đã cập nhật ảnh, chưa trích xuất khuôn mặt',
      tone: 'bg-sky-50 text-sky-700 border-sky-200',
    }
  }

  return {
    label: employee?.status_text || 'Đã có trong hệ thống (chưa có khuôn mặt)',
    tone: 'bg-amber-50 text-amber-700 border-amber-200',
  }
}

function ImagePreviewModal({ viewer, onClose, onPush, isInternalMode }) {
  if (!viewer.open) return null

  return (
    <div className="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4">
      <div className="w-full max-w-4xl bg-white rounded-2xl shadow-2xl overflow-hidden">
        <div className="px-4 sm:px-6 py-4 border-b border-slate-100 flex items-center justify-between gap-3">
          <div>
            <h2 className="text-lg font-semibold text-slate-800">Ảnh nhân viên</h2>
            <p className="text-sm text-slate-500">
              {viewer.employee?.name || '-'} · {viewer.employee?.employee_id || '-'}
            </p>
          </div>
          <button
            onClick={onClose}
            className="w-8 h-8 rounded-lg bg-slate-100 text-slate-500 hover:bg-slate-200 flex items-center justify-center transition-colors"
          >
            <X size={18} />
          </button>
        </div>

        <div className="grid lg:grid-cols-[360px_1fr] gap-0">
          <div className="bg-slate-50 border-r border-slate-100 min-h-[220px] sm:min-h-[360px] flex items-center justify-center p-4 sm:p-6">
            {viewer.loading ? (
              <div className="flex flex-col items-center gap-2">
                <Loader2 className="w-6 h-6 animate-spin text-primary-500" />
                <span className="text-sm text-slate-400">Đang tải ảnh...</span>
              </div>
            ) : viewer.error ? (
              <div className="text-center">
                <div className="flex justify-center mb-2 opacity-30 text-slate-500"><ImageIcon size={48} /></div>
                <p className="text-sm text-red-500">{viewer.error}</p>
              </div>
            ) : (
              <img
                src={viewer.imageUrl || viewer.imageBase64}
                alt={viewer.employee?.name || 'Employee'}
                className="max-h-[420px] w-full object-contain rounded-xl border border-slate-200 bg-white"
                onError={e => { e.target.style.display = 'none' }}
              />
            )}
          </div>

          <div className="p-4 sm:p-6 space-y-5">
            <div className="grid sm:grid-cols-2 gap-4 text-sm">
              <div>
                <p className="text-slate-400 mb-1 text-xs uppercase tracking-wider">Họ tên</p>
                <p className="font-semibold text-slate-800">{viewer.employee?.name || '-'}</p>
              </div>
              <div>
                <p className="text-slate-400 mb-1 text-xs uppercase tracking-wider">Mã nhân viên</p>
                <p className="font-semibold text-slate-800 font-mono">{viewer.employee?.employee_id || '-'}</p>
              </div>
              <div>
                <p className="text-slate-400 mb-1 text-xs uppercase tracking-wider">Phòng ban</p>
                <p className="font-semibold text-slate-800">{viewer.employee?.department || '-'}</p>
              </div>
              <div>
                <p className="text-slate-400 mb-1 text-xs uppercase tracking-wider">Số mẫu khuôn mặt</p>
                <p className="font-semibold text-slate-800">{viewer.employee?.face_count || 0}</p>
              </div>
              <div>
                <p className="text-slate-400 mb-1 text-xs uppercase tracking-wider">Trạng thái</p>
                <p className="font-semibold text-slate-800">{viewer.employee?.status_text || '-'}</p>
              </div>
              <div>
                <p className="text-slate-400 mb-1 text-xs uppercase tracking-wider">Nguồn ảnh</p>
                <p className="font-semibold text-slate-800">
                  {viewer.imageSource === 'erp' ? 'ERP' : 'Hệ thống local'}
                </p>
              </div>
            </div>

            <div className="grid sm:flex gap-2">
              <button
                onClick={() => onPush(viewer.employee)}
                disabled={viewer.loading || isInternalMode}
                className="w-full sm:w-auto px-4 py-2 bg-emerald-600 text-white rounded-xl text-sm font-medium hover:bg-emerald-700 disabled:opacity-50 transition-colors"
              >
                Đẩy ERP
              </button>
              <button
                onClick={onClose}
                className="w-full sm:w-auto px-4 py-2 bg-slate-100 text-slate-600 rounded-xl text-sm font-medium hover:bg-slate-200 transition-colors"
              >
                Đóng
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  )
}

const emptyViewer = {
  open: false,
  loading: false,
  employee: null,
  imageUrl: null,
  imageBase64: null,
  imageSource: 'local',
  error: '',
}

export default function ManageFaces() {
  const navigate = useNavigate()
  const { toast } = useToast()
  const [authMode, setAuthMode] = useState('system')
  const [employees, setEmployees] = useState([])
  const [loading, setLoading] = useState(true)
  const [bulkDeleting, setBulkDeleting] = useState(false)
  const [search, setSearch] = useState('')
  const [faceFilter, setFaceFilter] = useState('all')
  const [viewer, setViewer] = useState(emptyViewer)
  const [selectedEmployeeIds, setSelectedEmployeeIds] = useState([])
  const selectAllRef = useRef(null)
  
  // Pagination
  const [page, setPage] = useState(1)
  const [pageSize, setPageSize] = useState(10)

  useEffect(() => {
    loadEmployees()
    loadAuthMode()
  }, [])

  const filteredEmployees = useMemo(() => {
    const keyword = search.trim().toLowerCase()

    return employees.filter(employee => {
      const matchesKeyword = !keyword || (
        employee.name.toLowerCase().includes(keyword) ||
        employee.employee_id.toLowerCase().includes(keyword) ||
        (employee.department || '').toLowerCase().includes(keyword)
      )

      const matchesFaceFilter = (
        faceFilter === 'all' ||
        (faceFilter === 'with_face' && employee.has_face) ||
        (faceFilter === 'without_face' && !employee.has_face)
      )

      return matchesKeyword && matchesFaceFilter
    })
  }, [employees, search, faceFilter])

  // Reset page when filters change
  useEffect(() => {
    setPage(1)
  }, [search, faceFilter, pageSize])

  const paginatedEmployees = useMemo(() => {
    const startIndex = (page - 1) * pageSize
    return filteredEmployees.slice(startIndex, startIndex + pageSize)
  }, [filteredEmployees, page, pageSize])

  const paginatedEmployeeIds = useMemo(
    () => paginatedEmployees.map(employee => employee.id),
    [paginatedEmployees]
  )

  const selectedEmployees = useMemo(
    () => filteredEmployees.filter(employee => selectedEmployeeIds.includes(employee.id)),
    [filteredEmployees, selectedEmployeeIds]
  )

  const selectedOnPageCount = useMemo(
    () => paginatedEmployeeIds.filter(id => selectedEmployeeIds.includes(id)).length,
    [paginatedEmployeeIds, selectedEmployeeIds]
  )

  const allOnPageSelected = paginatedEmployees.length > 0 && selectedOnPageCount === paginatedEmployees.length
  const someOnPageSelected = selectedOnPageCount > 0 && !allOnPageSelected

  const totalPages = Math.ceil(filteredEmployees.length / pageSize)
  const isInternalMode = authMode === 'internal'

  useEffect(() => {
    const visibleEmployeeIds = new Set(filteredEmployees.map(employee => employee.id))
    setSelectedEmployeeIds(prev => prev.filter(id => visibleEmployeeIds.has(id)))
  }, [filteredEmployees])

  useEffect(() => {
    if (selectAllRef.current) {
      selectAllRef.current.indeterminate = someOnPageSelected
    }
  }, [someOnPageSelected])

  async function loadEmployees() {
    setLoading(true)
    try {
      const res = await api.getAdminEmployees()
      if (res.success) {
        setEmployees(res.employees)
      } else {
        toast.error(res.message || 'Không tải được danh sách nhân viên')
      }
    } catch {
      toast.error('Không thể kết nối backend')
    }
    setLoading(false)
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

  function closeViewer() {
    setViewer(emptyViewer)
  }

  function toggleEmployeeSelection(employeeId) {
    setSelectedEmployeeIds(prev => (
      prev.includes(employeeId)
        ? prev.filter(id => id !== employeeId)
        : [...prev, employeeId]
    ))
  }

  function toggleSelectAllOnPage(checked) {
    setSelectedEmployeeIds(prev => {
      const pageIds = new Set(paginatedEmployeeIds)
      if (checked) {
        const mergedIds = new Set(prev)
        paginatedEmployeeIds.forEach(id => mergedIds.add(id))
        return Array.from(mergedIds)
      }
      return prev.filter(id => !pageIds.has(id))
    })
  }

  async function handleViewImage(employee) {
    setViewer({
      open: true,
      loading: true,
      employee,
      imageUrl: null,
      imageBase64: null,
      imageSource: 'local',
      error: '',
    })

    try {
      const res = await api.getAdminEmployeeImage(employee.employee_id)
      if (res.success) {
        setViewer({
          open: true,
          loading: false,
          employee: {
            ...employee,
            ...(res.employee || {}),
          },
          imageUrl: res.image_url || null,
          imageBase64: res.image_base64,
          imageSource: res.image_source || 'local',
          error: '',
        })
      } else {
        setViewer({
          open: true,
          loading: false,
          employee,
          imageUrl: null,
          imageBase64: null,
          imageSource: 'local',
          error: res.message || 'Không tải được ảnh hiện có',
        })
      }
    } catch {
      setViewer({
        open: true,
        loading: false,
        employee,
        imageUrl: null,
        imageBase64: null,
        imageSource: 'local',
        error: 'Không thể tải ảnh hiện có từ backend',
      })
    }
  }

  async function handleDelete(userId, name) {
    if (!window.confirm(`Xác nhận xóa nhân viên ${name}?`)) return
    try {
      const res = await api.deleteEmployee(userId)
      if (res.success) {
        toast.success(res.message)
        if (viewer.employee?.id === userId) closeViewer()
        setSelectedEmployeeIds(prev => prev.filter(id => id !== userId))
        await loadEmployees()
      } else {
        toast.error(res.message)
      }
    } catch {
      toast.error('Không thể xóa nhân viên')
    }
  }

  async function handleBulkDelete() {
    if (selectedEmployees.length === 0) return

    const previewNames = selectedEmployees.slice(0, 3).map(employee => employee.name).join(', ')
    const remainingCount = selectedEmployees.length - 3
    const previewLabel = remainingCount > 0
      ? `${previewNames} và ${remainingCount} nhân viên khác`
      : previewNames

    if (!window.confirm(`Xác nhận xóa ${selectedEmployees.length} nhân viên (${previewLabel})?`)) return

    setBulkDeleting(true)
    const failedIds = []
    let successCount = 0

    for (const employee of selectedEmployees) {
      try {
        const res = await api.deleteEmployee(employee.id)
        if (res.success) {
          successCount += 1
          if (viewer.employee?.id === employee.id) closeViewer()
        } else {
          failedIds.push(employee.id)
        }
      } catch {
        failedIds.push(employee.id)
      }
    }

    if (successCount > 0) {
      toast.success(`Đã xóa ${successCount}/${selectedEmployees.length} nhân viên`)
    }
    if (failedIds.length > 0) {
      toast.error(`Có ${failedIds.length} nhân viên xóa thất bại`)
    }

    setSelectedEmployeeIds(failedIds)
    await loadEmployees()
    setBulkDeleting(false)
  }
  async function handleClearFace(userId, name) {
    if (!window.confirm(`Xóa dữ liệu khuôn mặt của ${name}?`)) return
    try {
      const res = await api.clearFace(userId)
      if (res.success) {
        toast.success(res.message)
        loadEmployees()
      } else {
        toast.error(res.message)
      }
    } catch {
      toast.error('Không thể xóa dữ liệu khuôn mặt')
    }
  }

  async function handlePushToErp(employee) {
    if (!employee) return

    if (isInternalMode) {
      toast.error('Đang ở chế độ nội bộ')
      return
    }

    if (!window.confirm(`Đẩy ảnh nhân viên ${employee.name} (${employee.employee_id}) lên ERP?`)) return

    try {
      const res = await api.pushToErp(employee.employee_id)
      if (res.success) {
        toast.success(res.message)
      } else {
        toast.error(res.message)
      }
    } catch {
      toast.error('Không thể đẩy ảnh lên ERP')
    }
  }

  async function handleUpdateFace(userId) {
    const input = document.createElement('input')
    input.type = 'file'
    input.accept = 'image/*'
    input.onchange = async event => {
      const file = event.target.files?.[0]
      if (!file) return

      const payload = new FormData()
      payload.append('user_id', userId)
      payload.append('image', file)

      try {
        const res = await api.updateFace(payload)
        if (res.success) {
          toast.success(res.message)
          await loadEmployees()
          if (viewer.employee?.id === userId) {
            handleViewImage({ ...viewer.employee, has_local_image: true, has_face: true })
          }
        } else {
          toast.error(res.message)
        }
      } catch {
        toast.error('Không thể cập nhật khuôn mặt')
      }
    }
    input.click()
  }

  function handleRegisterFace(employee) {
    navigate(`/register?employee_id=${encodeURIComponent(employee.employee_id)}`)
  }

  const filterButtons = [
    { key: 'all', label: 'Tất cả' },
    { key: 'with_face', label: 'Có khuôn mặt' },
    { key: 'without_face', label: 'Chưa có' },
  ]

  return (
    <div className="space-y-4 md:space-y-6">
      <ImagePreviewModal
        viewer={viewer}
        onClose={closeViewer}
        onPush={handlePushToErp}
        isInternalMode={isInternalMode}
      />

      <div className="flex items-center justify-between gap-3 flex-wrap">
        <div>
          <h1 className="text-2xl font-bold text-slate-800 tracking-tight">Quản lý nhân viên</h1>
          {isInternalMode && (
            <p className="text-sm text-amber-700 mt-1">Đang ở chế độ nội bộ. Nút đẩy ERP đã bị khóa.</p>
          )}
        </div>
        <button
          onClick={loadEmployees}
          className="w-full sm:w-auto flex items-center justify-center gap-2 px-4 py-2 bg-white text-slate-600 rounded-xl text-sm font-medium hover:bg-slate-100 border border-slate-200 transition-colors"
        >
          <RotateCw size={16} />
          Làm mới
        </button>
      </div>

      <div className="grid grid-cols-2 sm:flex gap-2">
        {filterButtons.map(btn => (
          <button
            key={btn.key}
            onClick={() => setFaceFilter(btn.key)}
            className={`px-4 py-2 rounded-xl text-sm font-medium transition-all text-center ${
              faceFilter === btn.key
                ? 'bg-primary-600 text-white shadow-sm'
                : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200'
            }`}
          >
            {btn.label}
          </button>
        ))}
      </div>

      <input
        type="text"
        value={search}
        onChange={e => setSearch(e.target.value)}
        placeholder="Tìm kiếm theo tên, mã nhân viên hoặc phòng ban..."
        className="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-400 transition-all"
      />

      {selectedEmployeeIds.length > 0 && (
        <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 bg-red-50 border border-red-200 rounded-xl px-4 py-3">
          <p className="text-sm text-red-700 font-medium">
            Đã chọn {selectedEmployeeIds.length} nhân viên
          </p>
          <div className="flex items-center gap-2">
            <button
              onClick={() => setSelectedEmployeeIds([])}
              disabled={bulkDeleting}
              className="px-3 py-1.5 bg-white text-slate-600 rounded-lg text-xs font-medium hover:bg-slate-100 border border-slate-200 transition-colors disabled:opacity-60"
            >
              Bỏ chọn
            </button>
            <button
              onClick={handleBulkDelete}
              disabled={bulkDeleting}
              className="px-3 py-1.5 bg-red-600 text-white rounded-lg text-xs font-medium hover:bg-red-700 transition-colors disabled:opacity-60"
            >
              {bulkDeleting ? 'Đang xóa...' : 'Xóa đã chọn'}
            </button>
          </div>
        </div>
      )}

      {loading ? (
        <div className="text-center py-12 text-slate-400">
          <div className="w-6 h-6 border-2 border-primary-500 border-t-transparent rounded-full animate-spin mx-auto mb-3" />
          <p className="text-sm">Đang tải dữ liệu...</p>
        </div>
      ) : (
        <div className="bg-white rounded-2xl shadow-sm border border-slate-200/60 overflow-hidden">
          <div className="hidden lg:block overflow-x-auto">
            <table className="w-full text-sm min-w-[980px]">
              <thead className="bg-slate-50">
                <tr>
                  <th className="px-4 py-3 text-left">
                    <input
                      ref={selectAllRef}
                      type="checkbox"
                      checked={allOnPageSelected}
                      onChange={e => toggleSelectAllOnPage(e.target.checked)}
                      disabled={paginatedEmployees.length === 0 || bulkDeleting}
                      className="w-4 h-4 rounded border-slate-300 text-primary-600 focus:ring-primary-500/30 disabled:opacity-60"
                    />
                  </th>
                  <th className="px-4 py-3 text-left font-medium text-slate-500 text-xs uppercase tracking-wider">Nhân viên</th>
                  <th className="px-4 py-3 text-left font-medium text-slate-500 text-xs uppercase tracking-wider">Mã NV</th>
                  <th className="px-4 py-3 text-left font-medium text-slate-500 text-xs uppercase tracking-wider">Phòng ban</th>
                  <th className="px-4 py-3 text-center font-medium text-slate-500 text-xs uppercase tracking-wider">Trạng thái</th>
                  <th className="px-4 py-3 text-center font-medium text-slate-500 text-xs uppercase tracking-wider">Ảnh</th>
                  <th className="px-4 py-3 text-right font-medium text-slate-500 text-xs uppercase tracking-wider">Thao tác</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-slate-50">
                {paginatedEmployees.map(employee => (
                  <tr key={employee.id} className="hover:bg-slate-50 transition-colors">
                    <td className="px-4 py-3 align-top">
                      <input
                        type="checkbox"
                        checked={selectedEmployeeIds.includes(employee.id)}
                        onChange={() => toggleEmployeeSelection(employee.id)}
                        disabled={bulkDeleting}
                        className="mt-0.5 w-4 h-4 rounded border-slate-300 text-primary-600 focus:ring-primary-500/30 disabled:opacity-60"
                      />
                    </td>
                    <td className="px-4 py-3">
                      <p className="font-medium text-slate-800">{employee.name}</p>
                      <p className="text-xs text-slate-400">{employee.created_at}</p>
                    </td>
                    <td className="px-4 py-3 text-slate-600 font-mono text-xs">{employee.employee_id}</td>
                    <td className="px-4 py-3 text-slate-500">{employee.department || '-'}</td>
                    <td className="px-4 py-3 text-center">
                      {(() => {
                        const badge = getManageStatusBadge(employee)
                        return (
                          <span className={`inline-block px-2.5 py-1 rounded-lg text-xs font-medium border ${badge.tone}`}>
                            {badge.label}
                          </span>
                        )
                      })()}
                    </td>
                    <td className="px-4 py-3 text-center">
                      {employee.has_local_image ? (
                        <button
                          onClick={() => handleViewImage(employee)}
                          className="px-3 py-1.5 bg-blue-50 text-blue-700 rounded-lg text-xs font-medium hover:bg-blue-100 transition-colors border border-blue-200"
                        >
                          Xem ảnh
                        </button>
                      ) : (
                        <span className="text-xs text-slate-400">Chưa có ảnh</span>
                      )}
                    </td>
                    <td className="px-4 py-3 text-right">
                      <div className="flex gap-1 justify-end flex-wrap">
                        {!employee.has_face && (
                          <button
                            onClick={() => handleRegisterFace(employee)}
                            className="px-2.5 py-1.5 bg-primary-50 text-primary-700 rounded-lg text-xs hover:bg-primary-100 border border-primary-200 transition-colors"
                          >
                            Đăng ký
                          </button>
                        )}
                        <button
                          onClick={() => handleUpdateFace(employee.id)}
                          className="px-2.5 py-1.5 bg-blue-50 text-blue-700 rounded-lg text-xs hover:bg-blue-100 border border-blue-200 transition-colors"
                        >
                          Cập nhật
                        </button>
                        <button
                          onClick={() => handlePushToErp(employee)}
                          disabled={isInternalMode}
                          className="px-2.5 py-1.5 bg-emerald-50 text-emerald-700 rounded-lg text-xs hover:bg-emerald-100 border border-emerald-200 transition-colors disabled:opacity-50"
                        >
                          Đẩy ERP
                        </button>
                        <button
                          onClick={() => handleClearFace(employee.id, employee.name)}
                          className="px-2.5 py-1.5 bg-amber-50 text-amber-700 rounded-lg text-xs hover:bg-amber-100 border border-amber-200 transition-colors"
                        >
                          Xóa ảnh
                        </button>
                        <button
                          onClick={() => handleDelete(employee.id, employee.name)}
                          className="px-2.5 py-1.5 bg-red-50 text-red-600 rounded-lg text-xs hover:bg-red-100 border border-red-200 transition-colors"
                        >
                          Xóa
                        </button>
                      </div>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>

          <div className="lg:hidden">
            {paginatedEmployees.length > 0 && (
              <div className="px-4 py-3 border-b border-slate-100 bg-slate-50 flex items-center justify-between gap-3">
                <label className="inline-flex items-center gap-2 text-xs text-slate-600 font-medium">
                  <input
                    type="checkbox"
                    checked={allOnPageSelected}
                    onChange={e => toggleSelectAllOnPage(e.target.checked)}
                    disabled={paginatedEmployees.length === 0 || bulkDeleting}
                    className="w-4 h-4 rounded border-slate-300 text-primary-600 focus:ring-primary-500/30 disabled:opacity-60"
                  />
                  Chọn tất cả trang
                </label>
                <span className="text-xs text-slate-500">{paginatedEmployees.length} nhân viên</span>
              </div>
            )}

            <div className="divide-y divide-slate-100">
              {paginatedEmployees.map(employee => {
                const badge = getManageStatusBadge(employee)
                return (
                  <div key={employee.id} className="p-3 sm:p-4 space-y-3">
                    <div className="flex items-start gap-3">
                      <input
                        type="checkbox"
                        checked={selectedEmployeeIds.includes(employee.id)}
                        onChange={() => toggleEmployeeSelection(employee.id)}
                        disabled={bulkDeleting}
                        className="mt-1 w-4 h-4 rounded border-slate-300 text-primary-600 focus:ring-primary-500/30 disabled:opacity-60"
                      />

                      <div className="min-w-0 flex-1">
                        <p className="font-semibold text-slate-800 truncate">{employee.name}</p>
                        <p className="text-xs text-slate-500 mt-0.5 font-mono">{employee.employee_id}</p>
                        <p className="text-xs text-slate-500 mt-0.5 truncate">{employee.department || '-'}</p>
                        <p className="text-[11px] text-slate-400 mt-1">{employee.created_at}</p>
                      </div>

                      <span className={`shrink-0 inline-block px-2.5 py-1 rounded-lg text-[11px] font-medium border ${badge.tone}`}>
                        {badge.label}
                      </span>
                    </div>

                    <div className="flex items-center justify-between gap-2">
                      <span className="text-xs text-slate-500">Ảnh nhân viên</span>
                      {employee.has_local_image ? (
                        <button
                          onClick={() => handleViewImage(employee)}
                          className="px-3 py-1.5 bg-blue-50 text-blue-700 rounded-lg text-xs font-medium hover:bg-blue-100 transition-colors border border-blue-200"
                        >
                          Xem ảnh
                        </button>
                      ) : (
                        <span className="text-xs text-slate-400">Chưa có ảnh</span>
                      )}
                    </div>

                    <div className="grid grid-cols-2 gap-2">
                      {!employee.has_face && (
                        <button
                          onClick={() => handleRegisterFace(employee)}
                          className="px-2.5 py-1.5 bg-primary-50 text-primary-700 rounded-lg text-xs hover:bg-primary-100 border border-primary-200 transition-colors"
                        >
                          Đăng ký
                        </button>
                      )}
                      <button
                        onClick={() => handleUpdateFace(employee.id)}
                        className="px-2.5 py-1.5 bg-blue-50 text-blue-700 rounded-lg text-xs hover:bg-blue-100 border border-blue-200 transition-colors"
                      >
                        Cập nhật
                      </button>
                      <button
                        onClick={() => handlePushToErp(employee)}
                        disabled={isInternalMode}
                        className="px-2.5 py-1.5 bg-emerald-50 text-emerald-700 rounded-lg text-xs hover:bg-emerald-100 border border-emerald-200 transition-colors disabled:opacity-50"
                      >
                        Đẩy ERP
                      </button>
                      <button
                        onClick={() => handleClearFace(employee.id, employee.name)}
                        className="px-2.5 py-1.5 bg-amber-50 text-amber-700 rounded-lg text-xs hover:bg-amber-100 border border-amber-200 transition-colors"
                      >
                        Xóa ảnh
                      </button>
                      <button
                        onClick={() => handleDelete(employee.id, employee.name)}
                        className="col-span-2 px-2.5 py-1.5 bg-red-50 text-red-600 rounded-lg text-xs hover:bg-red-100 border border-red-200 transition-colors"
                      >
                        Xóa nhân viên
                      </button>
                    </div>
                  </div>
                )
              })}
            </div>
          </div>
          
          {filteredEmployees.length > 0 && (
            <div className="px-4 sm:px-5 py-4 border-t border-slate-100 flex items-center justify-between flex-wrap gap-4">
              <div className="flex items-center gap-2">
                <span className="text-sm text-slate-500">Hiển thị</span>
                <select
                  value={pageSize}
                  onChange={e => setPageSize(Number(e.target.value))}
                  className="border border-slate-200 rounded-lg px-2 py-1 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-primary-500/20"
                >
                  <option value={5}>5</option>
                  <option value={10}>10</option>
                  <option value={20}>20</option>
                  <option value={50}>50</option>
                  <option value={100}>100</option>
                </select>
                <span className="text-sm text-slate-500">trên tổng {filteredEmployees.length}</span>
              </div>

              {totalPages > 1 && (
                <div className="flex gap-1">
                  <button
                    onClick={() => setPage(p => Math.max(1, p - 1))}
                    disabled={page === 1}
                    className="px-3 py-1 rounded-lg border border-slate-200 text-sm font-medium hover:bg-slate-50 disabled:opacity-50 text-slate-600"
                  >
                    Trước
                  </button>
                  {Array.from({ length: totalPages }, (_, i) => i + 1)
                    .filter(p => p === 1 || p === totalPages || Math.abs(p - page) <= 1)
                    .map((p, i, arr) => (
                      <React.Fragment key={p}>
                        {i > 0 && arr[i - 1] !== p - 1 && <span className="px-2 py-1 text-slate-400">...</span>}
                        <button
                          onClick={() => setPage(p)}
                          className={`px-3 py-1 rounded-lg text-sm font-medium transition-colors ${
                            page === p
                              ? 'bg-primary-600 text-white'
                              : 'border border-slate-200 text-slate-600 hover:bg-slate-50'
                          }`}
                        >
                          {p}
                        </button>
                      </React.Fragment>
                    ))}
                  <button
                    onClick={() => setPage(p => Math.min(totalPages, p + 1))}
                    disabled={page === totalPages}
                    className="px-3 py-1 rounded-lg border border-slate-200 text-sm font-medium hover:bg-slate-50 disabled:opacity-50 text-slate-600"
                  >
                    Sau
                  </button>
                </div>
              )}
            </div>
          )}

          {filteredEmployees.length === 0 && (
            <div className="px-5 py-8 text-center text-slate-400 text-sm">
              Không tìm thấy nhân viên
            </div>
          )}
        </div>
      )}
    </div>
  )
}