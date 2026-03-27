import { useState, useEffect } from 'react'
import { useNavigate } from 'react-router-dom'
import { api } from '../services/api'

export default function Report() {
  const [date, setDate] = useState(new Date().toISOString().split('T')[0])
  const [records, setRecords] = useState([])
  const [loading, setLoading] = useState(false)
  const [isAdmin, setIsAdmin] = useState(false)
  const navigate = useNavigate()

  useEffect(() => {
    checkAuth()
  }, [])

  useEffect(() => {
    if (isAdmin) loadReport()
  }, [date, isAdmin])

  async function checkAuth() {
    try {
      const res = await api.authStatus()
      if (res.is_admin) {
        setIsAdmin(true)
      } else {
        navigate('/login')
      }
    } catch (e) {
      navigate('/login')
    }
  }

  async function loadReport() {
    setLoading(true)
    try {
      const res = await api.getReport(date)
      if (res.success) setRecords(res.records)
    } catch (e) {
      console.error(e)
    }
    setLoading(false)
  }

  function exportCSV() {
    if (records.length === 0) return
    const headers = ['Tên', 'Mã NV', 'Phòng ban', 'Giờ vào', 'Giờ ra', 'Trạng thái']
    const rows = records.map(r => [
      r.name, r.employee_id, r.department,
      r.check_in_time, r.check_out_time, r.status,
    ])
    const csv = [headers, ...rows].map(r => r.join(',')).join('\n')
    const blob = new Blob(['\ufeff' + csv], { type: 'text/csv;charset=utf-8;' })
    const url = URL.createObjectURL(blob)
    const a = document.createElement('a')
    a.href = url
    a.download = `attendance_${date}.csv`
    a.click()
    URL.revokeObjectURL(url)
  }

  if (!isAdmin) return null

  return (
    <div className="space-y-6">
      <h1 className="text-2xl font-bold text-gray-800">Báo cáo điểm danh</h1>

      <div className="flex gap-3 items-center flex-wrap">
        <input
          type="date"
          value={date}
          onChange={e => setDate(e.target.value)}
          className="px-3 py-2 border border-gray-300 rounded-lg text-sm"
        />
        <button
          onClick={loadReport}
          disabled={loading}
          className="px-4 py-2 bg-primary-600 text-white rounded-lg text-sm font-medium hover:bg-primary-700 disabled:opacity-50"
        >
          Xem báo cáo
        </button>
        <button
          onClick={exportCSV}
          disabled={records.length === 0}
          className="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg text-sm font-medium hover:bg-gray-200 disabled:opacity-50"
        >
          Xuất CSV
        </button>
      </div>

      <div className="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        {loading ? (
          <div className="px-5 py-12 text-center text-gray-400">Đang tải...</div>
        ) : (
          <div className="overflow-x-auto">
            <table className="w-full text-sm">
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
                {records.map((rec, i) => (
                  <tr key={i} className="hover:bg-gray-50">
                    <td className="px-4 py-3 text-gray-400">{i + 1}</td>
                    <td className="px-4 py-3 font-medium text-gray-800">{rec.name}</td>
                    <td className="px-4 py-3 text-gray-600">{rec.employee_id}</td>
                    <td className="px-4 py-3 text-gray-600">{rec.department}</td>
                    <td className="px-4 py-3 text-gray-600">{rec.check_in_time}</td>
                    <td className="px-4 py-3 text-gray-600">{rec.check_out_time || '-'}</td>
                    <td className="px-4 py-3">
                      <span className={`inline-block px-2 py-1 text-xs rounded-full font-medium ${
                        rec.status === 'Đúng giờ'
                          ? 'bg-green-50 text-green-700'
                          : 'bg-yellow-50 text-yellow-700'
                      }`}>
                        {rec.status}
                      </span>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
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
