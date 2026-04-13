import React, { useCallback, useEffect, useMemo, useRef, useState } from 'react'
import { useSearchParams } from 'react-router-dom'
import { api } from '../services/api'
import { useToast } from '../components/Toast'
import { Download, Eye, RotateCw, UserSquare } from 'lucide-react'

function getStatusText(employee) {
  return employee?.registered
    ? 'Đã có thông tin trong hệ thống chấm công'
    : 'Chưa có thông tin trong hệ thống chấm công'
}

function getPrimaryActionLabel(employee) {
  return employee?.registered ? 'Cập nhật thông tin mới' : 'Đăng ký khuôn mặt'
}

function StatusBadge({ employee }) {
  const registered = Boolean(employee?.registered)
  const tone = registered
    ? 'bg-emerald-50 text-emerald-700 border border-emerald-200'
    : 'bg-amber-50 text-amber-700 border border-amber-200'

  return (
    <span className={`inline-block px-2.5 py-1 rounded-lg text-xs font-medium ${tone}`}>
      {getStatusText(employee)}
    </span>
  )
}

export default function Register() {
  const { toast } = useToast()
  const [erpEmployees, setErpEmployees] = useState([])
  const [employeeSearch, setEmployeeSearch] = useState('')
  const [selectedEmployeeId, setSelectedEmployeeId] = useState('')
  const [selectedEmployee, setSelectedEmployee] = useState(null)
  const [imageFile, setImageFile] = useState(null)
  const [imagePreview, setImagePreview] = useState(null)
  const [detailLoading, setDetailLoading] = useState(false)
  const [listLoading, setListLoading] = useState(true)
  const [submitLoading, setSubmitLoading] = useState(false)
  const [pushLoading, setPushLoading] = useState(false)
  const [searchParams, setSearchParams] = useSearchParams()

  const [page, setPage] = useState(1)
  const [pageSize, setPageSize] = useState(10)

  const lastFetchedId = useRef('')
  const initialLoadDone = useRef(false)
  const selectionRequestSeq = useRef(0)

  const preselectedEmployeeId = (searchParams.get('employee_id') || '').trim()

  const filteredErpEmployees = useMemo(() => {
    const keyword = employeeSearch.trim().toLowerCase()
    if (!keyword) return erpEmployees

    return erpEmployees.filter(employee =>
      employee.employee_id.toLowerCase().includes(keyword)
      || (employee.name || '').toLowerCase().includes(keyword)
      || (employee.department || '').toLowerCase().includes(keyword)
    )
  }, [employeeSearch, erpEmployees])

  useEffect(() => {
    setPage(1)
  }, [employeeSearch, pageSize])

  const paginatedErpEmployees = useMemo(() => {
    const startIndex = (page - 1) * pageSize
    return filteredErpEmployees.slice(startIndex, startIndex + pageSize)
  }, [filteredErpEmployees, page, pageSize])

  const totalPages = Math.max(1, Math.ceil(filteredErpEmployees.length / pageSize))
  const previewImage = imagePreview || selectedEmployee?.image_base64 || null
  const hasReadyFaceSource = Boolean(imageFile || selectedEmployee?.image_base64)
  const canSubmitSelectedEmployee = Boolean(
    selectedEmployee
    && hasReadyFaceSource
    && (!selectedEmployee.registered || selectedEmployee.user_id)
  )

  const fetchEmployeeDetail = useCallback(async employeeId => {
    const res = await api.getErpEmployeeInfo(employeeId)
    if (!res.success) {
      throw new Error(res.message || 'Không tải được thông tin nhân viên')
    }
    return res.employee
  }, [])

  const handleSelectEmployee = useCallback(async (employeeId, options = {}) => {
    const { force = false, keepUpload = false } = options
    if (!employeeId) return
    if (!force && lastFetchedId.current === employeeId && selectedEmployeeId === employeeId) return

    const requestSeq = ++selectionRequestSeq.current
    lastFetchedId.current = employeeId
    setSelectedEmployeeId(employeeId)
    setDetailLoading(true)

    setSearchParams(prev => {
      const params = new URLSearchParams(prev)
      if (params.get('employee_id') !== employeeId) {
        params.set('employee_id', employeeId)
        return params
      }
      return prev
    }, { replace: true })

    if (!keepUpload) {
      setImageFile(null)
      setImagePreview(null)
    }

    try {
      const employee = await fetchEmployeeDetail(employeeId)
      if (requestSeq !== selectionRequestSeq.current) return

      setSelectedEmployee(employee)
    } catch (error) {
      if (requestSeq !== selectionRequestSeq.current) return
      setSelectedEmployee(null)
      toast.error(error.message || 'Không tải được thông tin nhân viên')
    }

    if (requestSeq === selectionRequestSeq.current) {
      setDetailLoading(false)
    }
  }, [fetchEmployeeDetail, searchParams, selectedEmployeeId, setSearchParams, toast])

  const loadErpEmployees = useCallback(async (focusEmployeeId = '', options = {}) => {
    const { keepCurrentSelection = false } = options
    setListLoading(true)

    try {
      const res = await api.getErpEmployees()
      if (res.success) {
        const employees = res.employees || []
        setErpEmployees(employees)

        const nextEmployeeId = focusEmployeeId || selectedEmployeeId
        if (nextEmployeeId) {
          const exists = employees.some(employee => employee.employee_id === nextEmployeeId)
          if (exists) {
            await handleSelectEmployee(nextEmployeeId, {
              force: true,
              keepUpload: keepCurrentSelection && nextEmployeeId === selectedEmployeeId,
            })
          } else if (selectedEmployeeId === nextEmployeeId) {
            setSelectedEmployee(null)
            setSelectedEmployeeId('')
          }
        }
      } else {
        toast.error(res.message || 'Không tải được danh sách ERP')
      }
    } catch {
      toast.error('Không thể tải danh sách ERP')
    }

    setListLoading(false)
  }, [handleSelectEmployee, selectedEmployeeId, toast])

  useEffect(() => {
    if (!initialLoadDone.current) {
      initialLoadDone.current = true
      loadErpEmployees(preselectedEmployeeId)
    }
  }, [loadErpEmployees, preselectedEmployeeId])

  const handleActionClick = useCallback((employeeId, e) => {
    if (e) e.stopPropagation()
    if (selectedEmployeeId === employeeId) {
      handleSelectEmployee(employeeId, { force: true })
    } else {
      setSearchParams(prev => {
        const params = new URLSearchParams(prev)
        params.set('employee_id', employeeId)
        return params
      }, { replace: true })
    }
  }, [selectedEmployeeId, handleSelectEmployee, setSearchParams])

  useEffect(() => {
    if (!preselectedEmployeeId || erpEmployees.length === 0) return
    if (selectedEmployeeId === preselectedEmployeeId) return
    if (lastFetchedId.current === preselectedEmployeeId) return

    const employee = erpEmployees.find(item => item.employee_id === preselectedEmployeeId)
    if (employee) {
      handleSelectEmployee(employee.employee_id)
    }
  }, [erpEmployees, handleSelectEmployee, preselectedEmployeeId, selectedEmployeeId])

  function handleFileChange(event) {
    const file = event.target.files?.[0]
    setImageFile(file || null)

    if (!file) {
      setImagePreview(null)
      return
    }

    const reader = new FileReader()
    reader.onload = loadEvent => setImagePreview(loadEvent.target?.result || null)
    reader.readAsDataURL(file)
  }

  async function handleSubmitEmployee(event) {
    event.preventDefault()

    if (!selectedEmployee) {
      toast.error('Vui lòng chọn nhân viên ERP trước khi thao tác')
      return
    }

    if (!imageFile && !selectedEmployee.image_base64) {
      toast.error('Nhân viên chưa có ảnh từ ERP. Vui lòng tải ảnh khuôn mặt lên trước khi tiếp tục')
      return
    }

    setSubmitLoading(true)

    try {
      let res

      if (selectedEmployee.registered) {
        if (!selectedEmployee.user_id) {
          toast.error('Không tìm thấy bản ghi của nhân viên trong hệ thống chấm công')
          setSubmitLoading(false)
          return
        }

        if (imageFile) {
          const payload = new FormData()
          payload.append('user_id', selectedEmployee.user_id)
          payload.append('image', imageFile)
          payload.append('replace_all', 'true')
          res = await api.updateFace(payload)
        } else {
          res = await api.updateFaceBase64({
            user_id: selectedEmployee.user_id,
            image_base64: selectedEmployee.image_base64,
            replace_all: true,
          })
        }
      } else if (imageFile) {
        const payload = new FormData()
        payload.append('employee_id', selectedEmployee.employee_id)
        payload.append('name', selectedEmployee.name || '')
        payload.append('department', selectedEmployee.department || '')
        payload.append('image', imageFile)
        res = await api.register(payload)
      } else {
        res = await api.registerFromErp(selectedEmployee.employee_id)
      }

      if (res?.success) {
        toast.success(res.message || `${getPrimaryActionLabel(selectedEmployee)} thành công`)
        setImageFile(null)
        setImagePreview(null)
        lastFetchedId.current = ''
        await loadErpEmployees(selectedEmployee.employee_id)
      } else {
        toast.error(res?.message || `${getPrimaryActionLabel(selectedEmployee)} thất bại`)
      }
    } catch {
      toast.error(
        selectedEmployee.registered
          ? 'Không thể cập nhật thông tin nhân viên'
          : 'Không thể đăng ký khuôn mặt cho nhân viên'
      )
    }

    setSubmitLoading(false)
  }

  async function pushAllToAttendanceSystem() {
    setPushLoading(true)
    try {
      const res = await api.importAllFromErp()
      if (res.success) {
        const { imported, skipped, errors, total, without_face } = res.result
        toast.success(
          `Đã xử lý ${total} nhân viên ERP. Thêm mới: ${imported}, bỏ qua: ${skipped}, chưa có khuôn mặt: ${without_face || 0}, lỗi: ${errors}.`
        )
        await loadErpEmployees(selectedEmployeeId)
      } else {
        toast.error(res.message || 'Không thể đẩy dữ liệu vào hệ thống chấm công')
      }
    } catch {
      toast.error('Không thể đẩy dữ liệu vào hệ thống chấm công')
    }
    setPushLoading(false)
  }

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between gap-3 flex-wrap">
        <div className="space-y-1">
          <h1 className="text-2xl font-bold text-slate-800 tracking-tight">Đăng ký khuôn mặt</h1>
          <p className="text-sm text-slate-500">
            Đồng bộ danh sách ERP về đây để quản lý trước, sau đó mới đẩy dữ liệu sang hệ thống chấm công.
          </p>
        </div>

        <div className="flex gap-2 flex-wrap">
          <button
            onClick={() => loadErpEmployees(selectedEmployeeId, { keepCurrentSelection: true })}
            disabled={listLoading}
            className="flex items-center gap-2 px-4 py-2 bg-white text-slate-600 rounded-xl text-sm font-semibold hover:bg-slate-100 border border-slate-200 disabled:opacity-50 transition-colors"
          >
            <RotateCw size={16} className={listLoading ? 'animate-spin' : ''} />
            {listLoading ? 'Đang đồng bộ...' : 'Đồng bộ data từ ERP'}
          </button>

          <button
            onClick={pushAllToAttendanceSystem}
            disabled={pushLoading}
            className="flex items-center gap-2 px-4 py-2 bg-primary-600 text-white rounded-xl text-sm font-semibold hover:bg-primary-700 disabled:opacity-50 transition-colors shadow-sm"
          >
            <Download size={16} />
            {pushLoading ? 'Đang đẩy dữ liệu...' : 'Đẩy dữ liệu vào hệ thống chấm công'}
          </button>
        </div>
      </div>

      <div className="grid xl:grid-cols-[1.15fr_0.85fr] gap-6">
        <div className="bg-white rounded-2xl shadow-sm border border-slate-200/60 overflow-hidden">
          <div className="px-5 py-4 border-b border-slate-100 space-y-3">
            <div className="flex items-center justify-between gap-3 flex-wrap">
              <h2 className="text-base font-semibold text-slate-800">Danh sách nhân viên ERP</h2>
              <button
                onClick={() => loadErpEmployees(selectedEmployeeId, { keepCurrentSelection: true })}
                disabled={listLoading}
                className="flex items-center gap-1.5 px-3 py-1.5 bg-slate-100 text-slate-600 rounded-lg text-xs font-medium hover:bg-slate-200 disabled:opacity-50 transition-colors"
              >
                <RotateCw size={14} className={listLoading ? 'animate-spin' : ''} />
                Làm mới
              </button>
            </div>

            <input
              type="text"
              value={employeeSearch}
              onChange={event => setEmployeeSearch(event.target.value)}
              placeholder="Tìm theo mã, tên hoặc phòng ban"
              className="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-400 transition-all"
            />
            <p className="text-xs text-slate-400">
              Dùng nút Xem để nạp thông tin nhân viên vào khung bên phải và kiểm tra ảnh ERP trước khi đăng ký/cập nhật.
            </p>
          </div>

          <div className="overflow-x-auto">
            <table className="w-full text-sm">
              <thead className="bg-slate-50">
                <tr>
                  <th className="px-4 py-3 text-left font-medium text-slate-500 text-xs uppercase tracking-wider">Mã NV</th>
                  <th className="px-4 py-3 text-left font-medium text-slate-500 text-xs uppercase tracking-wider">Tên</th>
                  <th className="px-4 py-3 text-left font-medium text-slate-500 text-xs uppercase tracking-wider">Phòng ban</th>
                  <th className="px-4 py-3 text-center font-medium text-slate-500 text-xs uppercase tracking-wider">Trạng thái</th>
                  <th className="px-4 py-3 text-right font-medium text-slate-500 text-xs uppercase tracking-wider">Thao tác</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-slate-50">
                {paginatedErpEmployees.map(employee => (
                  <tr
                    key={employee.employee_id}
                    className={`transition-colors ${
                      selectedEmployeeId === employee.employee_id ? 'bg-primary-50/60' : 'hover:bg-slate-50'
                    }`}
                    onClick={(e) => handleActionClick(employee.employee_id, e)}
                  >
                    <td className="px-4 py-3 text-slate-600 font-mono text-xs">{employee.employee_id}</td>
                    <td className="px-4 py-3 font-medium text-slate-800">{employee.name || '-'}</td>
                    <td className="px-4 py-3 text-slate-500">{employee.department || '-'}</td>
                    <td className="px-4 py-3 text-center">
                      <StatusBadge employee={employee} />
                    </td>
                    <td className="px-4 py-3 text-right">
                      <div className="flex gap-2 justify-end flex-wrap">
                        <button
                          type="button"
                          onClick={event => handleActionClick(employee.employee_id, event)}
                          className="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium bg-sky-50 text-sky-700 border border-sky-200 hover:bg-sky-100 transition-colors"
                        >
                          <Eye size={14} />
                          Xem
                        </button>

                        <button
                          type="button"
                          onClick={event => handleActionClick(employee.employee_id, event)}
                          disabled={!employee.registered || detailLoading || submitLoading}
                          className={`px-3 py-1.5 rounded-lg text-xs font-medium transition-colors ${
                            employee.registered
                              ? 'bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100'
                              : 'bg-slate-100 text-slate-400 border border-slate-200 cursor-not-allowed'
                          }`}
                        >
                          Cập nhật thông tin mới
                        </button>

                        <button
                          type="button"
                          onClick={event => handleActionClick(employee.employee_id, event)}
                          disabled={employee.registered || detailLoading || submitLoading}
                          className={`px-3 py-1.5 rounded-lg text-xs font-medium transition-colors ${
                            employee.registered
                              ? 'bg-slate-100 text-slate-400 border border-slate-200 cursor-not-allowed'
                              : 'bg-primary-600 text-white hover:bg-primary-700'
                          }`}
                        >
                          Đăng ký khuôn mặt
                        </button>
                      </div>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>

            {listLoading && (
              <div className="px-5 py-10 text-center text-slate-400 text-sm">Đang tải danh sách ERP...</div>
            )}

            {!listLoading && filteredErpEmployees.length > 0 && (
              <div className="px-5 py-4 border-t border-slate-100 flex items-center justify-between flex-wrap gap-4">
                <div className="flex items-center gap-2">
                  <span className="text-sm text-slate-500">Hiển thị</span>
                  <select
                    value={pageSize}
                    onChange={event => setPageSize(Number(event.target.value))}
                    className="border border-slate-200 rounded-lg px-2 py-1 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-primary-500/20"
                  >
                    <option value={5}>5</option>
                    <option value={10}>10</option>
                    <option value={20}>20</option>
                    <option value={50}>50</option>
                    <option value={100}>100</option>
                  </select>
                  <span className="text-sm text-slate-500">trên tổng {filteredErpEmployees.length}</span>
                </div>

                <div className="flex items-center gap-2">
                  <button
                    onClick={() => setPage(current => Math.max(1, current - 1))}
                    disabled={page === 1}
                    className="px-3 py-1.5 rounded-lg border border-slate-200 text-sm text-slate-600 hover:bg-slate-50 disabled:opacity-50"
                  >
                    Trước
                  </button>
                  <span className="text-sm text-slate-500">
                    Trang {page}/{totalPages}
                  </span>
                  <button
                    onClick={() => setPage(current => Math.min(totalPages, current + 1))}
                    disabled={page === totalPages}
                    className="px-3 py-1.5 rounded-lg border border-slate-200 text-sm text-slate-600 hover:bg-slate-50 disabled:opacity-50"
                  >
                    Sau
                  </button>
                </div>
              </div>
            )}

            {!listLoading && filteredErpEmployees.length === 0 && (
              <div className="px-5 py-10 text-center text-slate-400 text-sm">
                Không có nhân viên ERP phù hợp
              </div>
            )}
          </div>
        </div>

        <div className="bg-white rounded-2xl shadow-sm border border-slate-200/60 p-6">
          {!selectedEmployee ? (
            <div className="h-full min-h-[360px] flex items-center justify-center text-center text-slate-400">
              <div>
                <div className="flex justify-center mb-3 opacity-30"><UserSquare size={48} /></div>
                <p className="text-sm">Chọn một nhân viên ERP để đăng ký hoặc cập nhật thông tin khuôn mặt</p>
              </div>
            </div>
          ) : (
            <form key={selectedEmployeeId} onSubmit={handleSubmitEmployee} className="space-y-5">
              <div className="flex gap-4 items-start">
                {previewImage ? (
                  <img
                    key={`img-${selectedEmployeeId}`}
                    src={previewImage}
                    alt={selectedEmployee.name}
                    className="w-24 h-24 object-cover rounded-2xl border border-slate-200 shadow-sm"
                    onError={event => { event.target.style.display = 'none' }}
                  />
                ) : (
                  <div className="w-24 h-24 rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50 flex items-center justify-center text-xs text-slate-400 text-center px-2">
                    Chưa có ảnh
                  </div>
                )}

                <div className="space-y-1.5 flex-1 min-w-0">
                  <p className="text-xl font-bold text-slate-800 truncate">{selectedEmployee.name}</p>
                  <p className="text-sm text-slate-500">Mã NV: <span className="font-mono">{selectedEmployee.employee_id}</span></p>
                  <p className="text-sm text-slate-500">Phòng ban: {selectedEmployee.department || '-'}</p>
                  <p className="text-sm text-slate-500">Email: {selectedEmployee.email || '-'}</p>
                  <p className="text-sm text-slate-500">SĐT: {selectedEmployee.phone || '-'}</p>
                  <StatusBadge employee={selectedEmployee} />
                </div>
              </div>

              <div className="rounded-xl border border-blue-100 bg-blue-50/60 p-4 text-sm text-blue-700 space-y-2">
                <p>
                  {selectedEmployee.registered
                    ? 'Nhân viên này đã có bản ghi trong hệ thống chấm công. Bạn có thể tải ảnh mới lên, hoặc dùng ngay ảnh hiện có từ ERP để cập nhật lại dữ liệu khuôn mặt.'
                    : 'Nhân viên này chưa có bản ghi trong hệ thống chấm công. Bạn có thể đăng ký trực tiếp bằng ảnh từ ERP, hoặc tải ảnh khuôn mặt mới lên trước khi đăng ký.'}
                </p>
                <p className="text-blue-600">
                  {selectedEmployee.image_base64
                    ? 'ERP hiện đã có ảnh khuôn mặt cho nhân viên này.'
                    : 'ERP chưa có ảnh khuôn mặt. Muốn thao tác thành công, bạn cần tải ảnh mới lên.'}
                </p>
              </div>

              <div>
                <label className="block text-sm font-medium text-slate-700 mb-2">Tải ảnh khuôn mặt mới</label>
                <input
                  type="file"
                  accept="image/*"
                  onChange={handleFileChange}
                  className="w-full text-sm file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:bg-primary-50 file:text-primary-700 file:font-medium file:cursor-pointer hover:file:bg-primary-100"
                />
              </div>

              {!hasReadyFaceSource && (
                <div className="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-700">
                  Chưa có nguồn ảnh khuôn mặt khả dụng. Hãy tải ảnh mới lên trước khi tiếp tục.
                </div>
              )}

              <button
                type="submit"
                disabled={submitLoading || detailLoading || !canSubmitSelectedEmployee}
                className="px-6 py-2.5 bg-primary-600 text-white rounded-xl font-semibold hover:bg-primary-700 disabled:opacity-50 transition-colors shadow-sm text-sm"
              >
                {submitLoading ? 'Đang xử lý...' : `${getPrimaryActionLabel(selectedEmployee)} cho ${selectedEmployee.employee_id}`}
              </button>
            </form>
          )}
        </div>
      </div>
    </div>
  )
}
