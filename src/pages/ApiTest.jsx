import { useState } from 'react'
import { api, getAdminToken } from '../services/api'

export default function ApiTest() {
  const [results, setResults] = useState([])
  const [testing, setTesting] = useState(false)

  const baseUrl = window.location.origin

  async function testEndpoint(name, fn) {
    const start = Date.now()
    try {
      const res = await fn()
      const ms = Date.now() - start
      return { name, status: 'ok', ms, data: JSON.stringify(res).slice(0, 200) }
    } catch (e) {
      const ms = Date.now() - start
      return { name, status: 'error', ms, data: e.message }
    }
  }

  async function runAllTests() {
    setTesting(true)
    setResults([])
    const tests = [
      ['GET /api/health', () => api.health()],
      ['GET /api/get_attendance_stats', () => api.getStats()],
      ['GET /api/get_recent_activity', () => api.getRecentActivity()],
      ['GET /api/get_today_attendance', () => api.getTodayAttendance()],
      ['GET /api/employees', () => api.getEmployees()],
      ['GET /api/camera_status', () => api.cameraStatus()],
      ['GET /api/auth_status', () => api.authStatus()],
    ]

    const newResults = []
    for (const [name, fn] of tests) {
      const result = await testEndpoint(name, fn)
      newResults.push(result)
      setResults([...newResults])
    }
    setTesting(false)
  }

  async function testSingleFetch() {
    setResults([])
    const url = `${window.location.origin}/api/health`
    const start = Date.now()
    try {
      const res = await fetch(url)
      const json = await res.json()
      const ms = Date.now() - start
      setResults([{ name: `Raw fetch ${url}`, status: 'ok', ms, data: JSON.stringify(json) }])
    } catch (e) {
      const ms = Date.now() - start
      setResults([{ name: `Raw fetch ${url}`, status: 'error', ms, data: e.message }])
    }
  }

  return (
    <div className="space-y-6">
      <h1 className="text-2xl font-bold text-gray-800">API Connection Test</h1>

      {/* Connection info */}
      <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-5 space-y-3">
        <h2 className="font-semibold text-gray-700">Connection Info</h2>
        <div className="grid sm:grid-cols-2 gap-2 text-sm">
          <div className="flex justify-between bg-gray-50 px-3 py-2 rounded">
            <span className="text-gray-500">Origin:</span>
            <span className="font-mono font-medium text-xs">{window.location.origin}</span>
          </div>
          <div className="flex justify-between bg-gray-50 px-3 py-2 rounded">
            <span className="text-gray-500">API Base:</span>
            <span className="font-mono font-medium text-xs">{baseUrl} (same-origin)</span>
          </div>
          <div className="flex justify-between bg-gray-50 px-3 py-2 rounded">
            <span className="text-gray-500">Electron:</span>
            <span className="font-mono font-medium">{window.electronAPI ? 'Yes' : 'No'}</span>
          </div>
          <div className="flex justify-between bg-gray-50 px-3 py-2 rounded">
            <span className="text-gray-500">Admin Token:</span>
            <span className="font-mono font-medium">{getAdminToken() ? 'Set' : 'Not set'}</span>
          </div>
          <div className="flex justify-between bg-gray-50 px-3 py-2 rounded">
            <span className="text-gray-500">Protocol:</span>
            <span className="font-mono font-medium">{window.location.protocol}</span>
          </div>
          <div className="flex justify-between bg-gray-50 px-3 py-2 rounded">
            <span className="text-gray-500">Dev Mode:</span>
            <span className="font-mono font-medium">{import.meta.env.DEV ? 'Yes' : 'No'}</span>
          </div>
        </div>
      </div>

      {/* Actions */}
      <div className="flex gap-3">
        <button
          onClick={runAllTests}
          disabled={testing}
          className="px-5 py-2.5 bg-primary-600 text-white rounded-lg font-medium hover:bg-primary-700 disabled:opacity-50"
        >
          {testing ? 'Testing...' : 'Test All Endpoints'}
        </button>
        <button
          onClick={testSingleFetch}
          className="px-5 py-2.5 bg-gray-100 text-gray-700 rounded-lg font-medium hover:bg-gray-200"
        >
          Raw Fetch Test
        </button>
      </div>

      {/* Results */}
      {results.length > 0 && (
        <div className="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
          <div className="px-5 py-3 border-b border-gray-100">
            <h2 className="font-semibold text-gray-700">Results</h2>
          </div>
          <div className="divide-y divide-gray-50">
            {results.map((r, i) => (
              <div key={i} className="px-5 py-3">
                <div className="flex items-center justify-between">
                  <div className="flex items-center gap-2">
                    <span className={`w-2.5 h-2.5 rounded-full ${r.status === 'ok' ? 'bg-green-500' : 'bg-red-500'}`}></span>
                    <span className="font-medium text-sm text-gray-800">{r.name}</span>
                  </div>
                  <span className="text-xs text-gray-400">{r.ms}ms</span>
                </div>
                <pre className={`mt-1 text-xs font-mono px-2 py-1 rounded overflow-x-auto ${
                  r.status === 'ok' ? 'bg-green-50 text-green-800' : 'bg-red-50 text-red-700'
                }`}>
                  {r.data}
                </pre>
              </div>
            ))}
          </div>
        </div>
      )}
    </div>
  )
}
