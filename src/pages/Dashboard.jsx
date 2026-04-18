import { useEffect, useState } from 'react'
import { api } from '../services/api'

function formatStorageSize(storageMb) {
  if (typeof storageMb !== 'number' || Number.isNaN(storageMb)) return '0 MB'
  if (storageMb >= 1024) {
    return `${(storageMb / 1024).toFixed(2)} GB`
  }
  return `${storageMb.toFixed(2)} MB`
}

export default function Dashboard() {
  const [stats, setStats] = useState(null)
  const [systemStats, setSystemStats] = useState(null)
  const [activities, setActivities] = useState([])

  useEffect(() => {
    loadData()
    const interval = setInterval(loadData, 30000)
    return () => clearInterval(interval)
  }, [])

  async function loadData() {
    try {
      const [statsRes, systemStatsRes, actRes] = await Promise.all([
        api.getStats(),
        api.getSystemStorageStats(),
        api.getRecentActivity(),
      ])

      if (statsRes.success) setStats(statsRes.data)
      if (systemStatsRes.success) setSystemStats(systemStatsRes.data)
      if (actRes.success) setActivities(actRes.activities)
    } catch (e) {
      console.error('Dashboard load error:', e)
    }
  }

  return (
    <div className="space-y-6">
      <h1 className="text-2xl font-bold text-gray-800">Tổng quan</h1>

      <div className="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <StatCard
          label="Tổng nhân viên"
          value={stats?.total_employees ?? 0}
          color="blue"
        />
        <StatCard
          label="Có mặt hôm nay"
          value={stats?.present_today ?? 0}
          color="green"
        />
        <StatCard
          label="Đi trễ"
          value={stats?.late_today ?? 0}
          color="yellow"
        />
        <StatCard
          label="Vắng mặt"
          value={stats?.absent_today ?? 0}
          color="red"
        />
      </div>

      <div className="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div className="px-5 py-4 border-b border-gray-100">
          <h2 className="text-lg font-semibold text-gray-800">Lưu trữ hệ thống</h2>
          <p className="text-sm text-gray-500 mt-1">
            Theo dõi nhanh số lượng nhân viên, ảnh khuôn mặt, dung lượng dữ liệu và camera chấm công đang lưu.
          </p>
        </div>

        <div className="p-5 grid grid-cols-2 xl:grid-cols-4 gap-4">
          <StatCard
            label="Nhân viên lưu trữ"
            value={systemStats?.employee_count ?? 0}
            hint={systemStats ? `${systemStats.face_sample_count || 0} mẫu khuôn mặt AI` : ''}
            color="slate"
          />
          <StatCard
            label="Ảnh khuôn mặt"
            value={systemStats?.face_image_count ?? 0}
            hint="Ảnh gốc đang lưu local"
            color="indigo"
          />
          <StatCard
            label="Dung lượng dữ liệu"
            value={formatStorageSize(systemStats?.storage_mb)}
            // hint={systemStats?.data_dir ? `Thư mục: ${systemStats.data_dir}` : ''}
            color="emerald"
          />
          <StatCard
            label="Camera chấm công"
            value={systemStats?.camera_count ?? 0}
            hint={systemStats ? `${systemStats.enabled_camera_count || 0} camera đang bật` : ''}
            color="amber"
          />
        </div>
      </div>

      <div className="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div className="px-5 py-4 border-b border-gray-100">
          <h2 className="text-lg font-semibold text-gray-800">Hoạt động gần đây</h2>
        </div>
        <div className="divide-y divide-gray-50">
          {activities.length === 0 ? (
            <div className="px-5 py-8 text-center text-gray-400">
              Chưa có hoạt động nào hôm nay
            </div>
          ) : (
            activities.map((act, i) => (
              <div key={i} className="px-5 py-3 flex items-center justify-between gap-4">
                <div>
                  <p className="font-medium text-gray-800">{act.name}</p>
                  <p className="text-sm text-gray-500">{act.department}</p>
                </div>
                <div className="text-right">
                  <span className="inline-block px-2 py-1 bg-green-50 text-green-700 text-xs rounded-full font-medium">
                    {act.status}
                  </span>
                  <p className="text-xs text-gray-400 mt-1">{act.time}</p>
                </div>
              </div>
            ))
          )}
        </div>
      </div>
    </div>
  )
}

function StatCard({ label, value, color, hint = '' }) {
  const colors = {
    blue: 'bg-blue-50 text-blue-700 border-blue-200',
    green: 'bg-green-50 text-green-700 border-green-200',
    yellow: 'bg-yellow-50 text-yellow-700 border-yellow-200',
    red: 'bg-red-50 text-red-700 border-red-200',
    slate: 'bg-slate-50 text-slate-700 border-slate-200',
    indigo: 'bg-indigo-50 text-indigo-700 border-indigo-200',
    emerald: 'bg-emerald-50 text-emerald-700 border-emerald-200',
    amber: 'bg-amber-50 text-amber-700 border-amber-200',
  }

  return (
    <div className={`rounded-xl border p-4 ${colors[color]}`}>
      <p className="text-sm font-medium opacity-75">{label}</p>
      <p className="text-3xl font-bold mt-1 notranslate" translate="no">{value}</p>
      {hint && <p className="text-xs opacity-75 mt-2 break-all">{hint}</p>}
    </div>
  )
}
