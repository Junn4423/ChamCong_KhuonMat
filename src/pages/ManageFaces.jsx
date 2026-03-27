import { useState, useEffect } from 'react'
import { useNavigate } from 'react-router-dom'
import { api, setAdminToken } from '../services/api'

export default function ManageFaces() {
  const [employees, setEmployees] = useState([])
  const [loading, setLoading] = useState(true)
  const [isAdmin, setIsAdmin] = useState(false)
  const [search, setSearch] = useState('')
  const navigate = useNavigate()

  useEffect(() => {
    checkAuth()
  }, [])

  async function checkAuth() {
    try {
      const res = await api.authStatus()
      if (res.is_admin) {
        setIsAdmin(true)
        loadEmployees()
      } else {
        navigate('/login')
      }
    } catch (e) {
      navigate('/login')
    }
  }

  async function loadEmployees() {
    setLoading(true)
    try {
      const res = await api.getAdminEmployees()
      if (res.success) setEmployees(res.employees)
    } catch (e) {
      console.error(e)
    }
    setLoading(false)
  }

  async function handleDelete(userId, name) {
    if (!confirm(`Xác nhận xóa nhân viên ${name}?`)) return
    try {
      const res = await api.deleteEmployee(userId)
      if (res.success) loadEmployees()
      else alert(res.message)
    } catch (e) {
      alert('Lỗi kết nối')
    }
  }

  async function handleClearFace(userId, name) {
    if (!confirm(`Xóa dữ liệu khuôn mặt của ${name}?`)) return
    try {
      const res = await api.clearFace(userId)
      if (res.success) loadEmployees()
      else alert(res.message)
    } catch (e) {
      alert('Lỗi kết nối')
    }
  }

  async function handleReloadErp(employeeId) {
    try {
      const res = await api.reloadFromErp(employeeId)
      if (res.success) {
        alert(res.message)
        loadEmployees()
      } else {
        alert(res.message)
      }
    } catch (e) {
      alert('Lỗi kết nối')
    }
  }

  async function handleUpdateFace(userId) {
    const input = document.createElement('input')
    input.type = 'file'
    input.accept = 'image/*'
    input.onchange = async (e) => {
      const file = e.target.files[0]
      if (!file) return
      const fd = new FormData()
      fd.append('user_id', userId)
      fd.append('image', file)
      try {
        const res = await api.updateFace(fd)
        if (res.success) {
          alert(res.message)
          loadEmployees()
        } else {
          alert(res.message)
        }
      } catch (err) {
        alert('Lỗi kết nối')
      }
    }
    input.click()
  }

  async function handleLogout() {
    await api.logout()
    setAdminToken(null)
    navigate('/login')
  }

  const filtered = employees.filter(emp =>
    emp.name.toLowerCase().includes(search.toLowerCase()) ||
    emp.employee_id.toLowerCase().includes(search.toLowerCase()) ||
    (emp.department || '').toLowerCase().includes(search.toLowerCase())
  )

  if (!isAdmin) return null

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <h1 className="text-2xl font-bold text-gray-800">Quản lý nhân viên</h1>
        <button
          onClick={handleLogout}
          className="px-4 py-2 text-sm text-gray-600 hover:text-red-600 font-medium"
        >
          Đăng xuất Admin
        </button>
      </div>

      <div className="flex gap-3">
        <input
          type="text"
          value={search}
          onChange={e => setSearch(e.target.value)}
          placeholder="Tìm kiếm nhân viên..."
          className="flex-1 px-4 py-2 border border-gray-300 rounded-lg"
        />
        <button
          onClick={loadEmployees}
          className="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg text-sm font-medium hover:bg-gray-200"
        >
          Làm mới
        </button>
      </div>

      {loading ? (
        <div className="text-center py-12 text-gray-400">Đang tải...</div>
      ) : (
        <div className="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
          <div className="overflow-x-auto">
            <table className="w-full text-sm">
              <thead className="bg-gray-50">
                <tr>
                  <th className="px-4 py-3 text-left font-medium text-gray-600">Nhân viên</th>
                  <th className="px-4 py-3 text-left font-medium text-gray-600">Mã NV</th>
                  <th className="px-4 py-3 text-left font-medium text-gray-600">Phòng ban</th>
                  <th className="px-4 py-3 text-center font-medium text-gray-600">Ảnh</th>
                  <th className="px-4 py-3 text-right font-medium text-gray-600">Thao tác</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-gray-50">
                {filtered.map(emp => (
                  <tr key={emp.id} className="hover:bg-gray-50">
                    <td className="px-4 py-3">
                      <p className="font-medium text-gray-800">{emp.name}</p>
                      <p className="text-xs text-gray-400">{emp.position}</p>
                    </td>
                    <td className="px-4 py-3 text-gray-600">{emp.employee_id}</td>
                    <td className="px-4 py-3 text-gray-600">{emp.department}</td>
                    <td className="px-4 py-3 text-center">
                      {emp.has_face ? (
                        <span className="inline-block px-2 py-1 bg-green-50 text-green-700 text-xs rounded-full">
                          {emp.face_count} ảnh
                        </span>
                      ) : (
                        <span className="inline-block px-2 py-1 bg-red-50 text-red-600 text-xs rounded-full">
                          Chưa có
                        </span>
                      )}
                    </td>
                    <td className="px-4 py-3 text-right">
                      <div className="flex gap-1 justify-end flex-wrap">
                        <button
                          onClick={() => handleUpdateFace(emp.id)}
                          className="px-2 py-1 bg-blue-50 text-blue-700 rounded text-xs hover:bg-blue-100"
                        >
                          Cập nhật ảnh
                        </button>
                        <button
                          onClick={() => handleReloadErp(emp.employee_id)}
                          className="px-2 py-1 bg-purple-50 text-purple-700 rounded text-xs hover:bg-purple-100"
                        >
                          ERP
                        </button>
                        <button
                          onClick={() => handleClearFace(emp.id, emp.name)}
                          className="px-2 py-1 bg-yellow-50 text-yellow-700 rounded text-xs hover:bg-yellow-100"
                        >
                          Xóa ảnh
                        </button>
                        <button
                          onClick={() => handleDelete(emp.id, emp.name)}
                          className="px-2 py-1 bg-red-50 text-red-600 rounded text-xs hover:bg-red-100"
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
          {filtered.length === 0 && (
            <div className="px-5 py-8 text-center text-gray-400">
              Không tìm thấy nhân viên
            </div>
          )}
        </div>
      )}
    </div>
  )
}
