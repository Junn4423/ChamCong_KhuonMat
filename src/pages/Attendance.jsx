import { useEffect, useMemo, useRef, useState } from 'react'
import { Link } from 'react-router-dom'
import { api } from '../services/api'

function isBrowserCameraType(cameraType) {
  return cameraType === 'browser' || cameraType === 'mobile'
}

function buildStartPayload(camera) {
  return {
    camera_id: camera.id || undefined,
    camera_type: camera.camera_type || 'device',
    device_index: Number(camera.device_index) || 0,
    rtsp_url: camera.rtsp_url || '',
    camera_options: { ...(camera.camera_options || {}) },
    processing_options: { ...(camera.processing_options || {}) },
  }
}

function buildBrowserConstraints(camera) {
  const cameraOptions = camera?.camera_options || {}
  const facingMode = cameraOptions.facing_mode || 'user'
  const constraints = {
    audio: false,
    video: {
      width: { ideal: Number(cameraOptions.frame_width) || 1280 },
      height: { ideal: Number(cameraOptions.frame_height) || 720 },
    },
  }

  if (facingMode && facingMode !== 'any') {
    constraints.video.facingMode = { ideal: facingMode }
  }

  return constraints
}

function describeCamera(camera) {
  if (!camera) return 'Chưa có cấu hình camera'

  switch (camera.camera_type) {
    case 'rtsp':
      return 'Nguồn RTSP'
    case 'device':
      return `Camera thiết bị #${camera.device_index || 0}`
    case 'browser':
      return 'Webcam trình duyệt'
    case 'mobile':
      return 'Camera điện thoại / webview'
    default:
      return camera.camera_type || 'Không rõ loại'
  }
}

function formatLocation(location) {
  if (!location || location.latitude == null || location.longitude == null) {
    return 'Chưa có dữ liệu location'
  }

  const lat = Number(location.latitude)
  const lng = Number(location.longitude)
  const accuracy = Number(location.accuracy)
  const coords = `${lat.toFixed(6)}, ${lng.toFixed(6)}`
  const accText = Number.isFinite(accuracy) ? ` ±${Math.round(accuracy)}m` : ''

  if (location.label) {
    return `${location.label} (${coords}${accText})`
  }

  return `${coords}${accText}`
}

export default function Attendance() {
  const [cameraRunning, setCameraRunning] = useState(false)
  const [cameraLoading, setCameraLoading] = useState(false)
  const [cameraRuntimeMode, setCameraRuntimeMode] = useState(null)
  const [activeCameraId, setActiveCameraId] = useState('')
  const [cameras, setCameras] = useState([])
  const [selectedCameraId, setSelectedCameraId] = useState('')
  const [todayRecords, setTodayRecords] = useState([])
  const [locationEnabled, setLocationEnabled] = useState(false)
  const [locationBusy, setLocationBusy] = useState(false)
  const [locationInfo, setLocationInfo] = useState(null)
  const [locationError, setLocationError] = useState('')
  const [streamKey, setStreamKey] = useState(Date.now())
  const [attendanceBusy, setAttendanceBusy] = useState(false)
  const [attendanceFeedback, setAttendanceFeedback] = useState(null)

  const imgRef = useRef(null)
  const videoRef = useRef(null)
  const canvasRef = useRef(null)
  const locationTimerRef = useRef(null)
  const browserStreamRef = useRef(null)

  const selectedCamera = useMemo(
    () => cameras.find(item => item.id === selectedCameraId) || null,
    [cameras, selectedCameraId]
  )

  const activeCamera = useMemo(
    () => cameras.find(item => item.id === activeCameraId) || null,
    [activeCameraId, cameras]
  )

  const browserCameraSelected = Boolean(selectedCamera && isBrowserCameraType(selectedCamera.camera_type))
  const browserCameraSupported = typeof navigator !== 'undefined'
    && !!navigator.mediaDevices
    && typeof navigator.mediaDevices.getUserMedia === 'function'
  const requiresSecureContext = typeof window !== 'undefined' && !window.isSecureContext

  useEffect(() => {
    initializePage()
    const attendanceTimer = setInterval(loadTodayRecords, 15000)
    return () => {
      clearInterval(attendanceTimer)
      if (locationTimerRef.current) clearInterval(locationTimerRef.current)
      stopBrowserCameraStream()
    }
  }, [])

  useEffect(() => {
    if (locationTimerRef.current) {
      clearInterval(locationTimerRef.current)
      locationTimerRef.current = null
    }

    if (!locationEnabled) return

    locationTimerRef.current = setInterval(() => {
      updateBrowserLocation()
    }, 30000)

    return () => {
      if (locationTimerRef.current) clearInterval(locationTimerRef.current)
      locationTimerRef.current = null
    }
  }, [locationEnabled])

  async function initializePage() {
    await Promise.all([
      checkCameraStatus(),
      loadCameras(),
      loadTodayRecords(),
      loadLocationState(),
    ])
  }

  function stopBrowserCameraStream() {
    if (browserStreamRef.current) {
      browserStreamRef.current.getTracks().forEach(track => track.stop())
      browserStreamRef.current = null
    }

    if (videoRef.current) {
      videoRef.current.srcObject = null
    }
  }

  async function checkCameraStatus() {
    try {
      const res = await api.cameraStatus()
      if (!res.success) return
      setCameraRunning(!!res.running)
      setCameraRuntimeMode(res.running ? 'backend' : null)
      setActiveCameraId(res.camera_id || '')
      if (res.camera_id) setSelectedCameraId(res.camera_id)
      if (typeof res.location_enabled === 'boolean') setLocationEnabled(res.location_enabled)
      if (res.latest_location) setLocationInfo(res.latest_location)
    } catch (error) {
      console.error(error)
    }
  }

  async function loadCameras() {
    try {
      const res = await api.getCameras()
      if (!res.success) return
      const list = res.cameras || []
      setCameras(list)

      if (list.length === 0) {
        setSelectedCameraId('')
        return
      }

      setSelectedCameraId(currentId => {
        if (currentId && list.some(item => item.id === currentId)) return currentId
        return list.find(item => item.is_default)?.id || list[0].id
      })
    } catch (error) {
      console.error(error)
    }
  }

  async function loadTodayRecords() {
    try {
      const res = await api.getTodayAttendance()
      if (res.success) setTodayRecords(res.data || [])
    } catch (error) {
      console.error(error)
    }
  }

  async function loadLocationState() {
    try {
      const res = await api.getLocationState()
      if (!res.success) return
      setLocationEnabled(!!res.enabled)
      setLocationInfo(res.location || null)
    } catch (error) {
      console.error(error)
    }
  }

  async function handleStartBrowserCamera() {
    if (!selectedCamera) return
    if (!browserCameraSupported) {
      window.alert('Thiết bị hoặc trình duyệt hiện tại chưa hỗ trợ camera trực tiếp.')
      return
    }

    setCameraLoading(true)
    setAttendanceFeedback(null)

    try {
      if (cameraRuntimeMode === 'backend') {
        await api.stopCamera()
        setStreamKey(Date.now())
      }

      stopBrowserCameraStream()

      const stream = await navigator.mediaDevices.getUserMedia(buildBrowserConstraints(selectedCamera))
      browserStreamRef.current = stream

      if (videoRef.current) {
        videoRef.current.srcObject = stream
        try {
          await videoRef.current.play()
        } catch (error) {
          console.error(error)
        }
      }

      setCameraRunning(true)
      setCameraRuntimeMode('browser')
      setActiveCameraId(selectedCamera.id || '')
    } catch (error) {
      const message = error?.message || 'Không thể mở camera trình duyệt'
      window.alert(message)
    }

    setCameraLoading(false)
  }

  async function handleStartCamera() {
    if (!selectedCamera) {
      window.alert('Vui lòng chọn camera đã lưu trước khi bật.')
      return
    }

    if (browserCameraSelected) {
      await handleStartBrowserCamera()
      return
    }

    setCameraLoading(true)
    setAttendanceFeedback(null)
    try {
      stopBrowserCameraStream()
      const res = await api.startCamera(buildStartPayload(selectedCamera))
      if (res.success) {
        setCameraRunning(true)
        setCameraRuntimeMode('backend')
        setActiveCameraId(selectedCamera.id || res.camera_id || '')
        setStreamKey(Date.now())
      } else {
        window.alert(res.message || 'Không thể bật camera')
      }
    } catch {
      window.alert('Không thể kết nối backend')
    }
    setCameraLoading(false)
  }

  async function handleStopCamera() {
    setCameraLoading(true)
    try {
      if (cameraRuntimeMode === 'browser') {
        stopBrowserCameraStream()
      } else {
        await api.stopCamera()
        setStreamKey(Date.now())
      }
      setCameraRunning(false)
      setCameraRuntimeMode(null)
      setActiveCameraId('')
    } catch (error) {
      console.error(error)
    }
    setCameraLoading(false)
  }

  async function captureBrowserAttendance() {
    if (!cameraRunning || cameraRuntimeMode !== 'browser') {
      window.alert('Hãy bật camera trình duyệt trước khi điểm danh.')
      return
    }

    if (!videoRef.current || !canvasRef.current) {
      window.alert('Camera chưa sẵn sàng để chụp.')
      return
    }

    const video = videoRef.current
    if (!video.videoWidth || !video.videoHeight) {
      window.alert('Luồng camera chưa sẵn sàng, vui lòng thử lại.')
      return
    }

    setAttendanceBusy(true)
    setAttendanceFeedback(null)

    try {
      const canvas = canvasRef.current
      canvas.width = video.videoWidth
      canvas.height = video.videoHeight

      const context = canvas.getContext('2d')
      context.drawImage(video, 0, 0, canvas.width, canvas.height)

      const payload = {
        image_base64: canvas.toDataURL('image/jpeg', 0.9),
      }

      if (locationEnabled && locationInfo) {
        payload.location = locationInfo
      }

      const res = await api.attendanceImageBase64(payload)
      if (res.success) {
        setAttendanceFeedback({
          type: 'success',
          message: res.message,
          detail: res.location_text || '',
        })
        await loadTodayRecords()
      } else {
        setAttendanceFeedback({
          type: 'error',
          message: res.message || 'Không thể điểm danh bằng camera trình duyệt',
          detail: '',
        })
      }
    } catch (error) {
      setAttendanceFeedback({
        type: 'error',
        message: error?.message || 'Không thể gửi ảnh điểm danh',
        detail: '',
      })
    }

    setAttendanceBusy(false)
  }

  function handleVideoError() {
    if (!cameraRunning || !imgRef.current || cameraRuntimeMode !== 'backend') return
    setTimeout(() => {
      setStreamKey(Date.now())
    }, 1200)
  }

  function getCurrentPositionPromise() {
    return new Promise((resolve, reject) => {
      if (!navigator.geolocation) {
        reject(new Error('Trình duyệt không hỗ trợ geolocation'))
        return
      }

      navigator.geolocation.getCurrentPosition(
        position => resolve(position),
        error => reject(error),
        {
          enableHighAccuracy: true,
          timeout: 10000,
          maximumAge: 0,
        }
      )
    })
  }

  async function updateBrowserLocation() {
    if (!locationEnabled) return
    setLocationBusy(true)
    setLocationError('')
    try {
      const position = await getCurrentPositionPromise()
      const payload = {
        enabled: true,
        location: {
          latitude: position.coords.latitude,
          longitude: position.coords.longitude,
          accuracy: position.coords.accuracy,
          label: 'Thiết bị hiện tại',
          provider: 'browser',
        },
      }
      const res = await api.updateLocationState(payload)
      if (res.success) {
        setLocationInfo(res.location || payload.location)
      } else {
        setLocationError(res.message || 'Không cập nhật được location')
      }
    } catch (error) {
      setLocationError(error?.message || 'Không lấy được location')
    }
    setLocationBusy(false)
  }

  async function toggleLocation(nextEnabled) {
    setLocationEnabled(nextEnabled)
    setLocationError('')

    if (!nextEnabled) {
      setLocationBusy(true)
      try {
        const res = await api.updateLocationState({ enabled: false })
        if (res.success) setLocationInfo(res.location || null)
      } catch (error) {
        console.error(error)
      }
      setLocationBusy(false)
      return
    }

    await updateBrowserLocation()
  }

  const showActiveCameraWarning = cameraRunning && activeCameraId && selectedCameraId && activeCameraId !== selectedCameraId
  const previewMirror = !!selectedCamera?.camera_options?.preview_mirror

  return (
    <div className="grid xl:grid-cols-[1.1fr_0.9fr] gap-6">
      <div className="bg-white rounded-2xl shadow-sm border border-slate-200/60 overflow-hidden">
        <div className="px-5 py-4 border-b border-slate-100 flex items-center justify-between gap-3 flex-wrap">
          <div>
            <h1 className="text-2xl font-bold text-slate-800 tracking-tight">Điểm danh</h1>
            <p className="text-sm text-slate-500 mt-1">
              Chọn camera đã cấu hình sẵn rồi bật camera. Với webcam hoặc điện thoại, hệ thống sẽ mở trực tiếp camera trên thiết bị đang dùng.
            </p>
          </div>
          <div className="flex items-center gap-2 text-sm font-medium">
            <span className={`w-2.5 h-2.5 rounded-full ${cameraRunning ? 'bg-emerald-500' : 'bg-slate-300'}`} />
            <span className={cameraRunning ? 'text-emerald-700' : 'text-slate-500'}>
              {cameraRunning
                ? (cameraRuntimeMode === 'browser' ? 'Camera trình duyệt đang chạy' : 'Camera backend đang chạy')
                : 'Camera đang tắt'}
            </span>
          </div>
        </div>

        <div className="p-5 space-y-4">
          <div className="overflow-hidden rounded-2xl border border-slate-200 bg-slate-950 aspect-video">
            {cameraRunning && cameraRuntimeMode === 'backend' ? (
              <img
                ref={imgRef}
                src={`${api.videoFeedUrl()}?t=${streamKey}`}
                alt="Video feed"
                className="w-full h-full object-cover"
                onError={handleVideoError}
              />
            ) : cameraRunning && cameraRuntimeMode === 'browser' ? (
              <video
                ref={videoRef}
                autoPlay
                muted
                playsInline
                className="w-full h-full object-cover"
                style={{ transform: previewMirror ? 'scaleX(-1)' : 'none' }}
              />
            ) : (
              <div className="w-full h-full flex items-center justify-center text-slate-300 text-sm bg-slate-900 text-center px-6">
                {browserCameraSelected
                  ? 'Bật camera để xem luồng từ webcam hoặc điện thoại ngay trên thiết bị hiện tại'
                  : 'Bật camera để xem luồng hình trực tiếp'}
              </div>
            )}
          </div>

          <canvas ref={canvasRef} className="hidden" />

          <div className="grid md:grid-cols-[1fr_auto_auto] gap-3 items-center">
            <select
              value={selectedCameraId}
              onChange={event => setSelectedCameraId(event.target.value)}
              className="px-3 py-2.5 border border-slate-200 rounded-xl text-sm bg-white focus:ring-2 focus:ring-primary-500/20 focus:border-primary-400 transition-all"
            >
              <option value="">-- Chọn camera --</option>
              {cameras.map(item => (
                <option key={item.id} value={item.id}>
                  {item.name} {item.is_default ? '(Mặc định)' : ''}
                </option>
              ))}
            </select>

            {!cameraRunning ? (
              <button
                onClick={handleStartCamera}
                disabled={cameraLoading || !selectedCamera}
                className="px-4 py-2.5 bg-primary-600 text-white rounded-xl text-sm font-semibold hover:bg-primary-700 disabled:opacity-50 transition-colors shadow-sm"
              >
                {cameraLoading ? 'Đang bật...' : 'Bật camera'}
              </button>
            ) : (
              <button
                onClick={handleStopCamera}
                disabled={cameraLoading}
                className="px-4 py-2.5 bg-red-500 text-white rounded-xl text-sm font-semibold hover:bg-red-600 disabled:opacity-50 transition-colors shadow-sm"
              >
                {cameraLoading ? 'Đang tắt...' : 'Tắt camera'}
              </button>
            )}

            <Link
              to="/cameras"
              className="px-4 py-2.5 bg-slate-100 text-slate-700 rounded-xl text-sm font-semibold hover:bg-slate-200 transition-colors text-center"
            >
              Quản lý camera
            </Link>
          </div>

          {browserCameraSelected && (
            <div className="rounded-xl border border-blue-100 bg-blue-50/70 px-4 py-3 text-sm text-blue-700 space-y-2">
              <p>
                Camera này hoạt động trực tiếp trên thiết bị đang mở trang điểm danh. Phù hợp cho webcam laptop hoặc camera điện thoại khi dùng webview.
              </p>
              {!browserCameraSupported && (
                <p className="text-red-600">
                  Trình duyệt hoặc webview hiện tại chưa hỗ trợ `getUserMedia`, nên không thể mở camera trực tiếp.
                </p>
              )}
              {requiresSecureContext && (
                <p className="text-amber-700">
                  Một số trình duyệt di động chỉ cho mở camera khi chạy trong HTTPS hoặc webview đã cấp quyền camera.
                </p>
              )}
            </div>
          )}

          {selectedCamera ? (
            <div className="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-600 space-y-1">
              <p className="font-medium text-slate-700">{selectedCamera.name}</p>
              <p>{describeCamera(selectedCamera)}</p>
              {browserCameraSelected && (
                <p>
                  Hướng camera: {selectedCamera.camera_options?.facing_mode === 'environment'
                    ? 'Camera sau'
                    : selectedCamera.camera_options?.facing_mode === 'any'
                      ? 'Tự chọn theo thiết bị'
                      : 'Camera trước'}
                </p>
              )}
            </div>
          ) : (
            <div className="rounded-xl border border-dashed border-slate-200 px-4 py-4 text-sm text-slate-400">
              Chưa có camera nào. Hãy thêm camera ở mục quản lý camera trước.
            </div>
          )}

          {showActiveCameraWarning && (
            <div className="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-700">
              Camera đang chạy là <strong>{activeCamera?.name || activeCameraId}</strong>. Nếu muốn đổi sang camera khác, hãy tắt camera hiện tại rồi bật lại.
            </div>
          )}

          {browserCameraSelected && cameraRunning && cameraRuntimeMode === 'browser' && (
            <button
              onClick={captureBrowserAttendance}
              disabled={attendanceBusy}
              className="w-full md:w-auto px-4 py-2.5 bg-emerald-600 text-white rounded-xl text-sm font-semibold hover:bg-emerald-700 disabled:opacity-50 transition-colors shadow-sm"
            >
              {attendanceBusy ? 'Đang nhận diện...' : 'Chụp và điểm danh'}
            </button>
          )}

          {attendanceFeedback && (
            <div className={`rounded-xl px-4 py-3 text-sm border ${
              attendanceFeedback.type === 'success'
                ? 'border-emerald-200 bg-emerald-50 text-emerald-700'
                : 'border-red-200 bg-red-50 text-red-600'
            }`}>
              <p className="font-medium">{attendanceFeedback.message}</p>
              {attendanceFeedback.detail && (
                <p className="mt-1 opacity-80">{attendanceFeedback.detail}</p>
              )}
            </div>
          )}
        </div>
      </div>

      <div className="space-y-6">
        <div className="bg-white rounded-2xl shadow-sm border border-slate-200/60 p-5 space-y-4">
          <div className="flex items-center justify-between gap-3">
            <div>
              <h2 className="text-lg font-semibold text-slate-800">Location</h2>
              <p className="text-sm text-slate-500 mt-1">
                Bật để gửi vị trí hiện tại cùng dữ liệu chấm công.
              </p>
            </div>
            <label className="inline-flex items-center gap-2 cursor-pointer">
              <input
                type="checkbox"
                checked={locationEnabled}
                onChange={event => toggleLocation(event.target.checked)}
                className="w-4 h-4 rounded border-slate-300 text-primary-600 focus:ring-primary-500"
              />
              <span className="text-sm font-medium text-slate-700">Bật location</span>
            </label>
          </div>

          <div className="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-600">
            {locationBusy ? 'Đang cập nhật vị trí...' : formatLocation(locationInfo)}
          </div>

          {locationError && (
            <div className="rounded-xl border border-red-100 bg-red-50 px-4 py-3 text-sm text-red-600">
              {locationError}
            </div>
          )}
        </div>

        <div className="bg-white rounded-2xl shadow-sm border border-slate-200/60 overflow-hidden">
          <div className="px-5 py-4 border-b border-slate-100">
            <h2 className="text-lg font-semibold text-slate-800">Điểm danh hôm nay</h2>
          </div>

          <div className="divide-y divide-slate-100">
            {todayRecords.length === 0 ? (
              <div className="px-5 py-10 text-center text-slate-400 text-sm">
                Chưa có bản ghi điểm danh hôm nay
              </div>
            ) : (
              todayRecords.map((record, index) => (
                <div key={`${record.employee_id}-${record.time}-${index}`} className="px-5 py-4 flex items-start justify-between gap-4">
                  <div>
                    <p className="font-semibold text-slate-800">{record.name}</p>
                    <p className="text-sm text-slate-500 mt-1">
                      {record.employee_id} · {record.department || 'Chưa có phòng ban'}
                    </p>
                    {record.location_text && (
                      <p className="text-xs text-slate-400 mt-1">{record.location_text}</p>
                    )}
                  </div>
                  <div className="text-right shrink-0">
                    <span className="inline-flex px-2.5 py-1 rounded-full text-xs font-medium bg-amber-50 text-amber-700">
                      {record.status || 'Điểm danh'}
                    </span>
                    <p className="text-sm text-slate-500 mt-2">{record.time || '--:--:--'}</p>
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
