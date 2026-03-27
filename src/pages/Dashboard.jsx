import { useState, useEffect } from 'react'
import { api } from '../services/api'

export default function Dashboard() {
  const [stats, setStats] = useState(null)
  const [activities, setActivities] = useState([])

  useEffect(() => {
    loadData()
    const interval = setInterval(loadData, 30000)
    return () => clearInterval(interval)
  }, [])

  async function loadData() {
    try {
      const [statsRes, actRes] = await Promise.all([
        api.getStats(),
        api.getRecentActivity(),
      ])
      if (statsRes.success) setStats(statsRes.data)
      if (actRes.success) setActivities(actRes.activities)
    } catch (e) {
      console.error('Dashboard load error:', e)
    }
  }

  return (
    <div className="space-y-6">
      <h1 className="text-2xl font-bold text-gray-800">Tổng quan</h1>

      {/* Stats cards */}
      <div className="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <StatCard
          label="Tổng nhân viên"
          value={stats?.total_employees ?? '-'}
          color="blue"
        />
        <StatCard
          label="Có mặt hôm nay"
          value={stats?.present_today ?? '-'}
          color="green"
        />
        <StatCard
          label="Đi trễ"
          value={stats?.late_today ?? '-'}
          color="yellow"
        />
        <StatCard
          label="Vắng mặt"
          value={stats?.absent_today ?? '-'}
          color="red"
        />
      </div>

      {/* Recent activity */}
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
              <div key={i} className="px-5 py-3 flex items-center justify-between">
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

function StatCard({ label, value, color }) {
  const colors = {
    blue: 'bg-blue-50 text-blue-700 border-blue-200',
    green: 'bg-green-50 text-green-700 border-green-200',
    yellow: 'bg-yellow-50 text-yellow-700 border-yellow-200',
    red: 'bg-red-50 text-red-700 border-red-200',
  }

  return (
    <div className={`rounded-xl border p-4 ${colors[color]}`}>
      <p className="text-sm font-medium opacity-75">{label}</p>
      <p className="text-3xl font-bold mt-1">{value}</p>
    </div>
  )
}
