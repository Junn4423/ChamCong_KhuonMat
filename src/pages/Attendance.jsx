import { useState, useEffect, useRef } from 'react'
import { api } from '../services/api'

export default function Attendance() {
  const [cameraRunning, setCameraRunning] = useState(false)
  const [cameraType, setCameraType] = useState('device')
  const [deviceIndex, setDeviceIndex] = useState(0)
  const [rtspUrl, setRtspUrl] = useState('')
  const [todayRecords, setTodayRecords] = useState([])
  const [loading, setLoading] = useState(false)
  const [captureMode, setCaptureMode] = useState(false)
  const videoRef = useRef(null)
  const canvasRef = useRef(null)
  const streamRef = useRef(null)

  useEffect(() => {
    checkCameraStatus()
    loadTodayRecords()
    const interval = setInterval(loadTodayRecords, 15000)
    return () => {
      clearInterval(interval)
      stopLocalCamera()
    }
  }, [])

  async function checkCameraStatus() {
    try {
      const res = await api.cameraStatus()
      if (res.success) {
        setCameraRunning(res.running)
        setCameraType(res.default_camera_type || 'device')
        setDeviceIndex(res.default_device_index || 0)
      }
    } catch (e) {
      console.error(e)
    }
  }

  async function loadTodayRecords() {
    try {
      const res = await api.getTodayAttendance()
      if (res.success) setTodayRecords(res.data)
    } catch (e) {
      console.error(e)
    }
  }

  async function handleStartCamera() {
    setLoading(true)
    try {
      const res = await api.startCamera({
        camera_type: cameraType,
        device_index: deviceIndex,
        rtsp_url: rtspUrl || undefined,
      })
      if (res.success) setCameraRunning(true)
      else alert(res.message)
    } catch (e) {
      alert('Không thể kết nối server')
    }
    setLoading(false)
  }

  async function handleStopCamera() {
    try {
      await api.stopCamera()
      setCameraRunning(false)
    } catch (e) {
      console.error(e)
    }
  }

  // Local camera capture for image-based attendance
  async function startLocalCamera() {
    try {
      const stream = await navigator.mediaDevices.getUserMedia({
        video: { width: 640, height: 480, facingMode: 'user' }
      })
      streamRef.current = stream
      if (videoRef.current) {
        videoRef.current.srcObject = stream
      }
      setCaptureMode(true)
    } catch (e) {
      alert('Không thể truy cập camera')
    }
  }

  function stopLocalCamera() {
    if (streamRef.current) {
      streamRef.current.getTracks().forEach(t => t.stop())
      streamRef.current = null
    }
    setCaptureMode(false)
  }

  async function captureAndAttend() {
    if (!videoRef.current || !canvasRef.current) return
    const canvas = canvasRef.current
    const video = videoRef.current
    canvas.width = video.videoWidth
    canvas.height = video.videoHeight
    const ctx = canvas.getContext('2d')
    ctx.drawImage(video, 0, 0)
    const dataUrl = canvas.toDataURL('image/jpeg', 0.9)

    setLoading(true)
    try {
      const res = await api.attendanceImageBase64({ image_base64: dataUrl })
      if (res.success) {
        alert(`✅ ${res.message}`)
        loadTodayRecords()
      } else {
        alert(`❌ ${res.message}`)
      }
    } catch (e) {
      alert('Lỗi kết nối server')
    }
    setLoading(false)
  }

  return (
    <div className="space-y-6">
      <h1 className="text-2xl font-bold text-gray-800">Điểm danh</h1>

      <div className="grid lg:grid-cols-2 gap-6">
        {/* Camera / Video panel */}
        <div className="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
          <div className="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <h2 className="text-lg font-semibold text-gray-800">Camera</h2>
            <span className={`w-3 h-3 rounded-full ${cameraRunning ? 'bg-green-500' : 'bg-gray-300'}`} />
          </div>

          <div className="p-4">
            {/* Server camera stream */}
            {cameraRunning && !captureMode && (
              <div className="relative">
                <img
                  src={api.videoFeedUrl()}
                  alt="Camera feed"
                  className="w-full rounded-lg bg-gray-900"
                />
              </div>
            )}

            {/* Local camera capture */}
            {captureMode && (
              <div className="relative">
                <video
                  ref={videoRef}
                  autoPlay
                  playsInline
                  muted
                  className="w-full rounded-lg bg-gray-900"
                />
                <canvas ref={canvasRef} className="hidden" />
              </div>
            )}

            {!cameraRunning && !captureMode && (
              <div className="h-64 bg-gray-100 rounded-lg flex items-center justify-center text-gray-400">
                Camera chưa được bật
              </div>
            )}

            {/* Camera controls */}
            <div className="mt-4 space-y-3">
              {/* Server camera controls */}
              <div className="flex gap-2 flex-wrap">
                <select
                  value={cameraType}
                  onChange={e => setCameraType(e.target.value)}
                  className="px-3 py-2 border border-gray-300 rounded-lg text-sm"
                >
                  <option value="device">Webcam</option>
                  <option value="rtsp">RTSP</option>
                </select>

                {cameraType === 'device' && (
                  <input
                    type="number"
                    value={deviceIndex}
                    onChange={e => setDeviceIndex(parseInt(e.target.value) || 0)}
                    className="w-20 px-3 py-2 border border-gray-300 rounded-lg text-sm"
                    placeholder="Index"
                    min={0}
                  />
                )}

                {cameraType === 'rtsp' && (
                  <input
                    type="text"
                    value={rtspUrl}
                    onChange={e => setRtspUrl(e.target.value)}
                    className="flex-1 min-w-48 px-3 py-2 border border-gray-300 rounded-lg text-sm"
                    placeholder="rtsp://..."
                  />
                )}
              </div>

              <div className="flex gap-2">
                {!cameraRunning ? (
                  <button
                    onClick={handleStartCamera}
                    disabled={loading}
                    className="px-4 py-2 bg-primary-600 text-white rounded-lg text-sm font-medium hover:bg-primary-700 disabled:opacity-50"
                  >
                    Bật Camera Server
                  </button>
                ) : (
                  <button
                    onClick={handleStopCamera}
                    className="px-4 py-2 bg-red-600 text-white rounded-lg text-sm font-medium hover:bg-red-700"
                  >
                    Tắt Camera Server
                  </button>
                )}

                {!captureMode ? (
                  <button
                    onClick={startLocalCamera}
                    className="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700"
                  >
                    Chụp ảnh điểm danh
                  </button>
                ) : (
                  <>
                    <button
                      onClick={captureAndAttend}
                      disabled={loading}
                      className="px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-medium hover:bg-green-700 disabled:opacity-50"
                    >
                      {loading ? 'Đang xử lý...' : 'Điểm danh'}
                    </button>
                    <button
                      onClick={stopLocalCamera}
                      className="px-4 py-2 bg-gray-500 text-white rounded-lg text-sm font-medium hover:bg-gray-600"
                    >
                      Đóng
                    </button>
                  </>
                )}
              </div>
            </div>
          </div>
        </div>

        {/* Today's records */}
        <div className="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
          <div className="px-5 py-4 border-b border-gray-100">
            <h2 className="text-lg font-semibold text-gray-800">Điểm danh hôm nay</h2>
          </div>
          <div className="divide-y divide-gray-50 max-h-[500px] overflow-y-auto">
            {todayRecords.length === 0 ? (
              <div className="px-5 py-8 text-center text-gray-400">
                Chưa có ai điểm danh hôm nay
              </div>
            ) : (
              todayRecords.map((rec, i) => (
                <div key={i} className="px-5 py-3 flex items-center justify-between">
                  <div>
                    <p className="font-medium text-gray-800">{rec.name}</p>
                    <p className="text-sm text-gray-500">{rec.employee_id} · {rec.department}</p>
                  </div>
                  <div className="text-right">
                    <span className={`inline-block px-2 py-1 text-xs rounded-full font-medium ${
                      rec.status === 'Đúng giờ'
                        ? 'bg-green-50 text-green-700'
                        : 'bg-yellow-50 text-yellow-700'
                    }`}>
                      {rec.status}
                    </span>
                    <p className="text-xs text-gray-400 mt-1">{rec.time}</p>
                  </div>
                </div>
              ))
            )}
          </div>
        </div>
      </div>
    </div>
  )
}
