import React, { useEffect, useState, useMemo } from 'react'
import { api } from '../services/api'
import { Download, Search } from 'lucide-react'

export default function Report() {
  const [date, setDate] = useState(new Date().toISOString().split('T')[0])
  const [records, setRecords] = useState([])
  const [loading, setLoading] = useState(false)

  // Pagination
  const [page, setPage] = useState(1)
  const [pageSize, setPageSize] = useState(10)

  useEffect(() => {
    setPage(1)
    loadReport()
  }, [date])

  const paginatedRecords = useMemo(() => {
    const startIndex = (page - 1) * pageSize
    return records.slice(startIndex, startIndex + pageSize)
  }, [records, page, pageSize])

  const totalPages = Math.ceil(records.length / pageSize)

  async function loadReport() {
    setLoading(true)
    try {
      const res = await api.getReport(date)
      if (res.success) setRecords(res.records)
    } catch (error) {
      console.error(error)
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

  return (
    <div className="space-y-6">
      <h1 className="text-2xl font-bold text-gray-800">Báo cáo điểm danh</h1>

      <div className="flex gap-3 items-center flex-wrap">
        <input
          type="date"
          value={date}
          onChange={event => setDate(event.target.value)}
          className="px-3 py-2 border border-gray-300 rounded-lg text-sm"
        />
        <button
          onClick={loadReport}
          disabled={loading}
          className="flex items-center gap-2 px-4 py-2 bg-primary-600 text-white rounded-lg text-sm font-medium hover:bg-primary-700 disabled:opacity-50"
        >
          <Search size={16} />
          Xem báo cáo
        </button>
        <button
          onClick={exportCSV}
          disabled={records.length === 0}
          className="flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-600 rounded-lg text-sm font-medium hover:bg-gray-200 disabled:opacity-50"
        >
          <Download size={16} />
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
                {paginatedRecords.map((record, index) => (
                  <tr key={`${record.employee_id}-${index}`} className="hover:bg-gray-50">
                    <td className="px-4 py-3 text-gray-400">{(page - 1) * pageSize + index + 1}</td>
                    <td className="px-4 py-3 font-medium text-gray-800">{record.name}</td>
                    <td className="px-4 py-3 text-gray-600">{record.employee_id}</td>
                    <td className="px-4 py-3 text-gray-600">{record.department}</td>
                    <td className="px-4 py-3 text-gray-600">{record.check_in_time}</td>
                    <td className="px-4 py-3 text-gray-600">{record.check_out_time || '-'}</td>
                    <td className="px-4 py-3">
                      <span className={`inline-block px-2 py-1 text-xs rounded-full font-medium ${
                        record.status === 'Đúng giờ'
                          ? 'bg-green-50 text-green-700'
                          : 'bg-yellow-50 text-yellow-700'
                      }`}>
                        {record.status}
                      </span>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>

            {records.length > 0 && (
              <div className="px-5 py-4 border-t border-gray-200 flex items-center justify-between flex-wrap gap-4">
                <div className="flex items-center gap-2">
                  <span className="text-sm text-gray-500">Hiển thị</span>
                  <select
                    value={pageSize}
                    onChange={e => setPageSize(Number(e.target.value))}
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
