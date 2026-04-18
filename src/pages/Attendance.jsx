import { useEffect, useMemo, useRef, useState } from 'react'
import { Link } from 'react-router-dom'
import { api } from '../services/api'

function isBrowserCameraType(cameraType) {
  return cameraType === 'browser' || cameraType === 'mobile'
}

const FIXED_BROWSER_CAMERA_ID = 'fixed-browser-camera'
const BROWSER_DEVICE_STORAGE_PREFIX = 'attendance-browser-device:'
const LIVE_DETECT_INTERVAL_MS = 900
const LIVE_DETECT_MAX_WIDTH = 480
const AUTO_ATTENDANCE_COOLDOWN_MS = 120000
const AUTO_ATTENDANCE_STREAK_REQUIRED = 2
const GPS_REFRESH_INTERVAL_MS = 30000
const GEOLOCATION_SAMPLE_TIMEOUT_MS = 12000
const GEOLOCATION_TARGET_ACCURACY_METERS = 35
const GEOLOCATION_MIN_SAMPLE_COUNT = 2

const FIXED_BROWSER_CAMERA = {
  id: FIXED_BROWSER_CAMERA_ID,
  name: 'Camera trình duyệt cố định',
  camera_type: 'browser',
  device_index: 0,
  rtsp_url: '',
  is_default: false,
  camera_options: {
    frame_width: 1280,
    frame_height: 720,
    target_fps: 25,
    buffer_size: 2,
    frame_drop_count: 0,
    low_latency: true,
    facing_mode: 'user',
    preview_mirror: true,
    browser_device_id: '',
  },
  processing_options: {
    fps_limit: 25,
    skip_ai_frames: 1,
    stream_jpeg_quality: 85,
    no_motion_delay: 2.0,
  },
}

function buildBrowserDeviceStorageKey(cameraId) {
  return `${BROWSER_DEVICE_STORAGE_PREFIX}${cameraId || FIXED_BROWSER_CAMERA_ID}`
}

function readSavedBrowserDeviceId(cameraId) {
  const key = buildBrowserDeviceStorageKey(cameraId)
  return (localStorage.getItem(key) || '').trim()
}

function saveBrowserDeviceId(cameraId, deviceId) {
  const key = buildBrowserDeviceStorageKey(cameraId)
  const normalized = (deviceId || '').trim()
  if (normalized) {
    localStorage.setItem(key, normalized)
    return
  }
  localStorage.removeItem(key)
}

function withFixedBrowserCamera(list) {
  const source = Array.isArray(list) ? list : []
  const hasFixed = source.some(item => item?.id === FIXED_BROWSER_CAMERA_ID)
  return hasFixed ? source : [FIXED_BROWSER_CAMERA, ...source]
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

function buildBrowserConstraints(camera, browserDeviceId = '') {
  const cameraOptions = camera?.camera_options || {}
  const facingMode = cameraOptions.facing_mode || 'user'
  const fixedDeviceId = (browserDeviceId || '').trim()
  const baseConstraints = {
    audio: false,
    video: {
      width: { ideal: Number(cameraOptions.frame_width) || 1280 },
      height: { ideal: Number(cameraOptions.frame_height) || 720 },
    },
  }

  const constraints = []

  if (fixedDeviceId) {
    constraints.push(
      {
        ...baseConstraints,
        video: {
          ...baseConstraints.video,
          deviceId: { exact: fixedDeviceId },
        },
      },
      {
        ...baseConstraints,
        video: {
          ...baseConstraints.video,
          deviceId: { ideal: fixedDeviceId },
        },
      },
      {
        audio: false,
        video: {
          deviceId: { exact: fixedDeviceId },
        },
      },
    )
  }

  if (!facingMode || facingMode === 'any') {
    constraints.push(baseConstraints, { audio: false, video: true })
    return constraints
  }

  const fallbackMode = facingMode === 'environment' ? 'user' : 'environment'

  constraints.push(
    {
      ...baseConstraints,
      video: {
        ...baseConstraints.video,
        facingMode: { ideal: facingMode },
      },
    },
    {
      ...baseConstraints,
      video: {
        ...baseConstraints.video,
        facingMode,
      },
    },
    {
      ...baseConstraints,
      video: {
        ...baseConstraints.video,
        facingMode: fallbackMode,
      },
    },
    { audio: false, video: { facingMode } },
    { audio: false, video: true },
  )

  return constraints
}

async function tryOpenBrowserCamera(constraintsList) {
  let lastError = null

  for (const constraints of constraintsList) {
    try {
      return await navigator.mediaDevices.getUserMedia(constraints)
    } catch (error) {
      lastError = error
    }
  }

  throw lastError || new Error('Không thể mở camera trình duyệt')
}

function waitForVideoFrame(videoElement, timeoutMs = 7000) {
  if (!videoElement) {
    const error = new Error('Không tìm thấy vùng preview video')
    error.code = 'NO_VIDEO_FRAME'
    return Promise.reject(error)
  }

  if (videoElement.readyState >= 2 && videoElement.videoWidth > 0 && videoElement.videoHeight > 0) {
    return Promise.resolve()
  }

  return new Promise((resolve, reject) => {
    let settled = false

    const cleanup = () => {
      if (settled) return
      settled = true
      clearTimeout(timeoutHandle)
      videoElement.removeEventListener('loadedmetadata', checkReady)
      videoElement.removeEventListener('loadeddata', checkReady)
      videoElement.removeEventListener('canplay', checkReady)
      videoElement.removeEventListener('playing', checkReady)
      videoElement.removeEventListener('resize', checkReady)
      videoElement.removeEventListener('error', handleVideoError)
    }

    const handleVideoError = () => {
      cleanup()
      const error = new Error('Không tải được luồng video từ camera')
      error.code = 'NO_VIDEO_FRAME'
      reject(error)
    }

    const checkReady = () => {
      if (videoElement.readyState >= 2 && videoElement.videoWidth > 0 && videoElement.videoHeight > 0) {
        cleanup()
        resolve()
      }
    }

    const timeoutHandle = setTimeout(() => {
      cleanup()
      const error = new Error('Camera đã mở nhưng chưa nhận được khung hình')
      error.code = 'NO_VIDEO_FRAME'
      reject(error)
    }, timeoutMs)

    videoElement.addEventListener('loadedmetadata', checkReady)
    videoElement.addEventListener('loadeddata', checkReady)
    videoElement.addEventListener('canplay', checkReady)
    videoElement.addEventListener('playing', checkReady)
    videoElement.addEventListener('resize', checkReady)
    videoElement.addEventListener('error', handleVideoError)

    checkReady()
  })
}

async function attachStreamToVideo(videoElement, stream) {
  if (!videoElement || !stream) {
    const error = new Error('Không thể gắn camera vào khung preview')
    error.code = 'NO_VIDEO_FRAME'
    throw error
  }

  videoElement.autoplay = true
  videoElement.muted = true
  videoElement.playsInline = true
  videoElement.setAttribute('autoplay', 'true')
  videoElement.setAttribute('muted', 'true')
  videoElement.setAttribute('playsinline', 'true')
  videoElement.setAttribute('webkit-playsinline', 'true')
  if ('disablePictureInPicture' in videoElement) {
    videoElement.disablePictureInPicture = true
  }

  if (videoElement.srcObject !== stream) {
    videoElement.srcObject = stream
  }

  try {
    await videoElement.play()
  } catch {
    // Safari can require metadata first before play succeeds.
  }

  await waitForVideoFrame(videoElement)

  if (videoElement.paused) {
    try {
      await videoElement.play()
    } catch {
      // Keep fallback silent; readiness check below will raise explicit error.
    }
  }

  if (!videoElement.videoWidth || !videoElement.videoHeight) {
    const error = new Error('Camera đã mở nhưng không có khung hình hiển thị')
    error.code = 'NO_VIDEO_FRAME'
    throw error
  }
}

function getCameraErrorMessage(error, requiresSecureContext) {
  const name = error?.name || ''

  if (error?.code === 'NO_VIDEO_FRAME') {
    return 'Đã cấp quyền camera nhưng không nhận được khung hình. Hãy thử đổi camera trong danh sách hoặc bấm Bật camera lại.'
  }

  if (requiresSecureContext || name === 'SecurityError') {
    return 'Trình duyệt iPhone yêu cầu HTTPS (hoặc webview đã cấp quyền) để mở camera trực tiếp.'
  }

  if (name === 'NotAllowedError' || name === 'PermissionDeniedError') {
    return 'Camera đang bị từ chối quyền truy cập. Hãy bật quyền Camera cho trình duyệt/webview rồi thử lại.'
  }

  if (name === 'NotFoundError' || name === 'DevicesNotFoundError') {
    return 'Không tìm thấy camera trên thiết bị.'
  }

  if (name === 'NotReadableError' || name === 'TrackStartError') {
    return 'Camera đang được ứng dụng khác sử dụng. Hãy đóng ứng dụng đó rồi thử lại.'
  }

  if (name === 'OverconstrainedError' || name === 'ConstraintNotSatisfiedError') {
    return 'Thông số camera chưa phù hợp với thiết bị. Hãy đổi hướng camera hoặc thử lại.'
  }

  return error?.message || 'Không thể mở camera trình duyệt'
}

function getLocationErrorMessage(error, requiresSecureContext) {
  if (requiresSecureContext || error?.name === 'SecurityError') {
    return 'Trình duyệt trên iPhone/iPad cần HTTPS để lấy GPS chính xác. Hãy mở bằng HTTPS hoặc webview đã cấp quyền.'
  }

  if (error?.code === 1 || error?.name === 'PermissionDeniedError') {
    return 'Trình duyệt đang chặn quyền vị trí. Hãy bật GPS và cấp quyền Location cho trình duyệt/webview rồi thử lại.'
  }

  if (error?.code === 2 || error?.name === 'PositionUnavailableError') {
    return 'Chưa xác định được vị trí GPS. Hãy bật Location Services, mở ngoài trời nếu cần, và thử lại.'
  }

  if (error?.code === 3 || error?.name === 'TimeoutError') {
    return 'Lấy GPS quá lâu. Hệ thống sẽ dùng điểm có sai số tốt nhất nếu đã nhận được, hoặc bạn hãy thử lại.'
  }

  return error?.message || 'Không lấy được vị trí hiện tại'
}

function getLocationTimestampMs(location) {
  const raw = location?.timestamp || location?.captured_at || ''
  if (!raw) return 0
  const ts = Date.parse(raw)
  return Number.isFinite(ts) ? ts : 0
}

function getPositionAccuracy(position) {
  const accuracy = Number(position?.coords?.accuracy)
  return Number.isFinite(accuracy) ? accuracy : Number.POSITIVE_INFINITY
}

function collectBestPositionPromise(options = {}) {
  const timeoutMs = Number(options.timeoutMs) || GEOLOCATION_SAMPLE_TIMEOUT_MS
  const targetAccuracy = Number(options.targetAccuracy) || GEOLOCATION_TARGET_ACCURACY_METERS
  const minSamples = Math.max(1, Number(options.minSamples) || GEOLOCATION_MIN_SAMPLE_COUNT)

  return new Promise((resolve, reject) => {
    if (!navigator.geolocation) {
      reject(new Error('Trình duyệt không hỗ trợ geolocation'))
      return
    }

    let bestPosition = null
    let sampleCount = 0
    let settled = false
    let watchId = null

    const cleanup = () => {
      if (watchId != null && typeof navigator.geolocation.clearWatch === 'function') {
        navigator.geolocation.clearWatch(watchId)
      }
      clearTimeout(timeoutHandle)
    }

    const resolveWithPosition = (position) => {
      if (settled) return
      settled = true
      cleanup()
      resolve(position)
    }

    const rejectWithError = (error) => {
      if (settled) return
      settled = true
      cleanup()
      reject(error)
    }

    const handlePosition = (position) => {
      sampleCount += 1
      if (!bestPosition || getPositionAccuracy(position) < getPositionAccuracy(bestPosition)) {
        bestPosition = position
      }

      if (getPositionAccuracy(bestPosition) <= targetAccuracy && sampleCount >= minSamples) {
        resolveWithPosition(bestPosition)
      }
    }

    const handleError = (error) => {
      if (bestPosition) {
        resolveWithPosition(bestPosition)
        return
      }
      rejectWithError(error)
    }

    const timeoutHandle = setTimeout(() => {
      if (bestPosition) {
        resolveWithPosition(bestPosition)
        return
      }
      const timeoutError = new Error('Lấy GPS quá thời gian cho phép')
      timeoutError.code = 3
      rejectWithError(timeoutError)
    }, timeoutMs)

    if (typeof navigator.geolocation.watchPosition === 'function') {
      watchId = navigator.geolocation.watchPosition(
        handlePosition,
        handleError,
        {
          enableHighAccuracy: true,
          timeout: timeoutMs,
          maximumAge: 0,
        }
      )
      return
    }

    navigator.geolocation.getCurrentPosition(
      resolveWithPosition,
      rejectWithError,
      {
        enableHighAccuracy: true,
        timeout: timeoutMs,
        maximumAge: 0,
      }
    )
  })
}

function sanitizeDeviceLabel(label, index) {
  const normalized = (label || '').trim()
  if (normalized) return normalized
  return `Camera ${index + 1}`
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

function computeContainedVideoRect(containerWidth, containerHeight, frameWidth, frameHeight) {
  if (!containerWidth || !containerHeight || !frameWidth || !frameHeight) return null

  const containerAspect = containerWidth / containerHeight
  const frameAspect = frameWidth / frameHeight

  if (frameAspect > containerAspect) {
    const renderWidth = containerWidth
    const renderHeight = renderWidth / frameAspect
    return {
      renderWidth,
      renderHeight,
      offsetX: 0,
      offsetY: (containerHeight - renderHeight) / 2,
    }
  }

  const renderHeight = containerHeight
  const renderWidth = renderHeight * frameAspect
  return {
    renderWidth,
    renderHeight,
    offsetX: (containerWidth - renderWidth) / 2,
    offsetY: 0,
  }
}

function projectDetectionBoxToPreview(detection, frameSize, containerSize, mirrored = false) {
  const bbox = Array.isArray(detection?.bbox) ? detection.bbox : null
  if (!bbox || bbox.length < 4) return null

  const frameWidth = Number(frameSize?.width) || 0
  const frameHeight = Number(frameSize?.height) || 0
  const containerWidth = Number(containerSize?.width) || 0
  const containerHeight = Number(containerSize?.height) || 0
  if (!frameWidth || !frameHeight || !containerWidth || !containerHeight) return null

  const fit = computeContainedVideoRect(containerWidth, containerHeight, frameWidth, frameHeight)
  if (!fit) return null

  let [x1, y1, x2, y2] = bbox.map(value => Number(value) || 0)
  x1 = Math.max(0, Math.min(frameWidth - 1, x1))
  y1 = Math.max(0, Math.min(frameHeight - 1, y1))
  x2 = Math.max(x1 + 1, Math.min(frameWidth, x2))
  y2 = Math.max(y1 + 1, Math.min(frameHeight, y2))

  if (mirrored) {
    const mirroredX1 = frameWidth - x2
    const mirroredX2 = frameWidth - x1
    x1 = Math.max(0, mirroredX1)
    x2 = Math.min(frameWidth, mirroredX2)
  }

  const left = fit.offsetX + (x1 / frameWidth) * fit.renderWidth
  const top = fit.offsetY + (y1 / frameHeight) * fit.renderHeight
  const width = ((x2 - x1) / frameWidth) * fit.renderWidth
  const height = ((y2 - y1) / frameHeight) * fit.renderHeight

  if (width < 2 || height < 2) return null
  return { left, top, width, height }
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
  const [lastCapturePreview, setLastCapturePreview] = useState(null)
  const [liveDetections, setLiveDetections] = useState([])
  const [liveDetectionFrame, setLiveDetectionFrame] = useState({ width: 0, height: 0 })
  const [liveDetectionError, setLiveDetectionError] = useState('')
  const [liveRecognizeEnabled, setLiveRecognizeEnabled] = useState(true)
  const [autoAttendanceEnabled, setAutoAttendanceEnabled] = useState(false)
  const [previewViewport, setPreviewViewport] = useState({ width: 0, height: 0 })
  const [browserDevices, setBrowserDevices] = useState([])
  const [browserDevicesLoading, setBrowserDevicesLoading] = useState(false)
  const [selectedBrowserDeviceId, setSelectedBrowserDeviceId] = useState('')

  const imgRef = useRef(null)
  const previewContainerRef = useRef(null)
  const videoRef = useRef(null)
  const canvasRef = useRef(null)
  const detectCanvasRef = useRef(null)
  const locationTimerRef = useRef(null)
  const detectTimerRef = useRef(null)
  const detectInFlightRef = useRef(false)
  const autoAttendanceInFlightRef = useRef(false)
  const autoAttendanceStreakRef = useRef({ userId: null, count: 0 })
  const autoAttendanceCooldownRef = useRef({})
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
    && typeof navigator.mediaDevices.enumerateDevices === 'function'
  const requiresSecureContext = typeof window !== 'undefined' && !window.isSecureContext

  useEffect(() => {
    initializePage()
    const attendanceTimer = setInterval(loadTodayRecords, 15000)
    return () => {
      clearInterval(attendanceTimer)
      if (locationTimerRef.current) clearInterval(locationTimerRef.current)
      if (detectTimerRef.current) clearInterval(detectTimerRef.current)
      stopBrowserCameraStream()
    }
  }, [])

  useEffect(() => {
    if (!browserCameraSelected || !selectedCamera) {
      setBrowserDevices([])
      setSelectedBrowserDeviceId('')
      return
    }

    const preferredId = (
      readSavedBrowserDeviceId(selectedCamera.id)
      || selectedCamera.camera_options?.browser_device_id
      || ''
    ).trim()
    setSelectedBrowserDeviceId(preferredId)
    loadBrowserDevices({ withPermission: false, preferredDeviceId: preferredId })
  }, [browserCameraSelected, selectedCamera?.id])

  useEffect(() => {
    if (!browserCameraSupported || typeof navigator === 'undefined' || !navigator.mediaDevices) {
      return undefined
    }

    const mediaDevices = navigator.mediaDevices
    const handleDeviceChange = () => {
      loadBrowserDevices({ withPermission: false })
    }

    if (typeof mediaDevices.addEventListener === 'function') {
      mediaDevices.addEventListener('devicechange', handleDeviceChange)
      return () => {
        mediaDevices.removeEventListener('devicechange', handleDeviceChange)
      }
    }

    const previousHandler = mediaDevices.ondevicechange
    mediaDevices.ondevicechange = handleDeviceChange
    return () => {
      if (mediaDevices.ondevicechange === handleDeviceChange) {
        mediaDevices.ondevicechange = previousHandler || null
      }
    }
  }, [browserCameraSupported, selectedCamera?.id])

  useEffect(() => {
    if (locationTimerRef.current) {
      clearInterval(locationTimerRef.current)
      locationTimerRef.current = null
    }

    if (!locationEnabled) return

    locationTimerRef.current = setInterval(() => {
      updateBrowserLocation()
    }, GPS_REFRESH_INTERVAL_MS)

    return () => {
      if (locationTimerRef.current) clearInterval(locationTimerRef.current)
      locationTimerRef.current = null
    }
  }, [locationEnabled])

  useEffect(() => {
    const container = previewContainerRef.current
    if (!container) return undefined

    const updateViewport = () => {
      setPreviewViewport({
        width: container.clientWidth || 0,
        height: container.clientHeight || 0,
      })
    }

    updateViewport()

    if (typeof ResizeObserver === 'undefined') {
      window.addEventListener('resize', updateViewport)
      return () => {
        window.removeEventListener('resize', updateViewport)
      }
    }

    const observer = new ResizeObserver(updateViewport)
    observer.observe(container)
    return () => {
      observer.disconnect()
    }
  }, [browserCameraSelected])

  useEffect(() => {
    if (detectTimerRef.current) {
      clearInterval(detectTimerRef.current)
      detectTimerRef.current = null
    }

    detectInFlightRef.current = false

    if (!(cameraRunning && cameraRuntimeMode === 'browser' && browserCameraSelected)) {
      setLiveDetections([])
      setLiveDetectionFrame({ width: 0, height: 0 })
      setLiveDetectionError('')
      autoAttendanceStreakRef.current = { userId: null, count: 0 }
      autoAttendanceInFlightRef.current = false
      return undefined
    }

    let cancelled = false

    const detectRealtimeFaces = async () => {
      if (cancelled || detectInFlightRef.current || attendanceBusy) return

      const video = videoRef.current
      const detectCanvas = detectCanvasRef.current
      if (!video || !detectCanvas) return
      if (!video.videoWidth || !video.videoHeight || video.readyState < 2) return

      detectInFlightRef.current = true
      try {
        const targetWidth = Math.max(160, Math.min(video.videoWidth, LIVE_DETECT_MAX_WIDTH))
        const scale = targetWidth / Math.max(1, video.videoWidth)
        const targetHeight = Math.max(120, Math.round(video.videoHeight * scale))

        detectCanvas.width = targetWidth
        detectCanvas.height = targetHeight

        const context = detectCanvas.getContext('2d')
        if (!context) return

        context.drawImage(video, 0, 0, targetWidth, targetHeight)

        const recognizeRealtime = liveRecognizeEnabled || autoAttendanceEnabled

        const res = await api.attendanceDetectFrame({
          image_base64: detectCanvas.toDataURL('image/jpeg', 0.65),
          max_faces: 3,
          recognize: recognizeRealtime,
          tolerance: 0.5,
        })

        if (cancelled) return

        if (res?.success) {
          const detections = Array.isArray(res.detections) ? res.detections : []
          setLiveDetections(detections)
          setLiveDetectionFrame({
            width: Number(res.frame_width) || targetWidth,
            height: Number(res.frame_height) || targetHeight,
          })
          setLiveDetectionError('')

          if (autoAttendanceEnabled && recognizeRealtime && !autoAttendanceInFlightRef.current) {
            const matchedDetections = detections.filter(
              item => Boolean(item?.matched) && item?.user_id != null,
            )

            if (matchedDetections.length === 0) {
              autoAttendanceStreakRef.current = { userId: null, count: 0 }
            } else {
              const candidate = matchedDetections[0]
              const candidateUserId = String(candidate.user_id)
              const streak = autoAttendanceStreakRef.current

              if (streak.userId === candidateUserId) {
                streak.count += 1
              } else {
                streak.userId = candidateUserId
                streak.count = 1
              }

              const now = Date.now()
              const lastAttemptAt = Number(autoAttendanceCooldownRef.current[candidateUserId] || 0)
              const cooldownPassed = now - lastAttemptAt >= AUTO_ATTENDANCE_COOLDOWN_MS

              if (streak.count >= AUTO_ATTENDANCE_STREAK_REQUIRED && cooldownPassed) {
                autoAttendanceCooldownRef.current[candidateUserId] = now
                autoAttendanceInFlightRef.current = true
                setAttendanceBusy(true)

                try {
                  const payloadLocation = (locationEnabled && locationInfo) ? locationInfo : null
                  const submitRes = await api.checkAttendance(candidate.user_id, payloadLocation, 'checkin')

                  if (submitRes?.success) {
                    setAttendanceFeedback({
                      type: 'success',
                      message: `${submitRes.message} (Tự động)`,
                      detail: submitRes.location_text || '',
                    })
                    await loadTodayRecords()
                  } else {
                    const failureMessage = submitRes?.message || 'Không thể tự động điểm danh'
                    const isAlreadyChecked = failureMessage.toLowerCase().includes('10 phút')
                      || failureMessage.toLowerCase().includes('gần đây')

                    setAttendanceFeedback({
                      type: isAlreadyChecked ? 'success' : 'error',
                      message: isAlreadyChecked
                        ? `${failureMessage} (đã có bản ghi gần thời điểm hiện tại)`
                        : failureMessage,
                      detail: '',
                    })
                  }
                } catch (error) {
                  setAttendanceFeedback({
                    type: 'error',
                    message: error?.message || 'Không thể tự động điểm danh',
                    detail: '',
                  })
                } finally {
                  autoAttendanceInFlightRef.current = false
                  setAttendanceBusy(false)
                }
              }
            }
          }
        } else {
          setLiveDetectionError(res?.message || 'Không nhận được dữ liệu nhận diện realtime')
          autoAttendanceStreakRef.current = { userId: null, count: 0 }
        }
      } catch (error) {
        if (!cancelled) {
          console.error(error)
          setLiveDetectionError('Không thể nhận diện realtime từ camera trình duyệt')
        }
      } finally {
        detectInFlightRef.current = false
      }
    }

    detectRealtimeFaces()
    detectTimerRef.current = setInterval(detectRealtimeFaces, LIVE_DETECT_INTERVAL_MS)

    return () => {
      cancelled = true
      if (detectTimerRef.current) {
        clearInterval(detectTimerRef.current)
        detectTimerRef.current = null
      }
      detectInFlightRef.current = false
      autoAttendanceInFlightRef.current = false
      autoAttendanceStreakRef.current = { userId: null, count: 0 }
    }
  }, [
    attendanceBusy,
    autoAttendanceEnabled,
    browserCameraSelected,
    cameraRunning,
    cameraRuntimeMode,
    liveRecognizeEnabled,
    locationEnabled,
    locationInfo,
  ])

  useEffect(() => {
    if (!cameraRunning || cameraRuntimeMode !== 'browser') return

    const videoElement = videoRef.current
    const browserStream = browserStreamRef.current
    if (!videoElement || !browserStream) return

    let cancelled = false

    const bindPreview = async () => {
      try {
        await attachStreamToVideo(videoElement, browserStream)
      } catch (error) {
        if (cancelled) return
        console.error(error)
        window.alert(getCameraErrorMessage(error, requiresSecureContext))
        stopBrowserCameraStream()
        setCameraRunning(false)
        setCameraRuntimeMode(null)
        setActiveCameraId('')
      }
    }

    bindPreview()

    return () => {
      cancelled = true
    }
  }, [cameraRunning, cameraRuntimeMode, requiresSecureContext])

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
      videoRef.current.pause()
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
      const list = withFixedBrowserCamera(res.cameras || [])
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

  async function loadBrowserDevices({ withPermission = false, preferredDeviceId = '' } = {}) {
    if (!browserCameraSupported || !navigator.mediaDevices?.enumerateDevices) {
      setBrowserDevices([])
      return []
    }

    setBrowserDevicesLoading(true)
    try {
      if (withPermission) {
        try {
          const stream = await navigator.mediaDevices.getUserMedia({ video: true, audio: false })
          stream.getTracks().forEach(track => track.stop())
        } catch {
          // Ignore permission errors here; actual start flow handles it with user-facing message.
        }
      }

      let allDevices = await navigator.mediaDevices.enumerateDevices()
      let videoInputs = allDevices.filter(item => item.kind === 'videoinput')

      if (withPermission && videoInputs.some(item => !(item.label || '').trim())) {
        allDevices = await navigator.mediaDevices.enumerateDevices()
        videoInputs = allDevices.filter(item => item.kind === 'videoinput')
      }

      const normalized = videoInputs.map((device, index) => ({
        id: device.deviceId,
        label: sanitizeDeviceLabel(device.label, index),
      }))

      setBrowserDevices(normalized)

      const preferred = (preferredDeviceId || selectedBrowserDeviceId || '').trim()
      if (preferred && normalized.some(item => item.id === preferred)) {
        setSelectedBrowserDeviceId(preferred)
      } else if (selectedBrowserDeviceId && !normalized.some(item => item.id === selectedBrowserDeviceId)) {
        setSelectedBrowserDeviceId('')
      }

      return normalized
    } catch (error) {
      console.error(error)
      return []
    } finally {
      setBrowserDevicesLoading(false)
    }
  }

  function handleBrowserDeviceChange(deviceId) {
    const normalized = (deviceId || '').trim()
    setSelectedBrowserDeviceId(normalized)
    if (selectedCamera?.id) {
      saveBrowserDeviceId(selectedCamera.id, normalized)
    }
  }

  async function handleStartBrowserCamera() {
    if (!selectedCamera) return
    if (!browserCameraSupported) {
      window.alert('Thiết bị hoặc trình duyệt hiện tại chưa hỗ trợ mở camera trực tiếp bằng getUserMedia.')
      return
    }

    setCameraLoading(true)
    setAttendanceFeedback(null)
    setLastCapturePreview(null)
    setLiveDetections([])
    setLiveDetectionFrame({ width: 0, height: 0 })
    setLiveDetectionError('')

    try {
      if (cameraRuntimeMode === 'backend') {
        await api.stopCamera()
        setStreamKey(Date.now())
      }

      stopBrowserCameraStream()

      await loadBrowserDevices({ withPermission: true })
      const preferredDeviceId = (selectedBrowserDeviceId || '').trim()

      const stream = await tryOpenBrowserCamera(buildBrowserConstraints(selectedCamera, preferredDeviceId))

      const track = stream.getVideoTracks()?.[0]
      if (!track || track.readyState !== 'live') {
        stream.getTracks().forEach(item => item.stop())
        const error = new Error('Không thể lấy luồng video trực tiếp từ camera')
        error.code = 'NO_VIDEO_FRAME'
        throw error
      }

      browserStreamRef.current = stream

      const openedDeviceId = (track?.getSettings?.().deviceId || preferredDeviceId || '').trim()
      if (openedDeviceId && selectedCamera?.id) {
        setSelectedBrowserDeviceId(openedDeviceId)
        saveBrowserDeviceId(selectedCamera.id, openedDeviceId)
      }

      setCameraRunning(true)
      setCameraRuntimeMode('browser')
      setActiveCameraId(selectedCamera.id || '')
    } catch (error) {
      const message = getCameraErrorMessage(error, requiresSecureContext)
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
    setLastCapturePreview(null)
    setLiveDetections([])
    setLiveDetectionFrame({ width: 0, height: 0 })
    setLiveDetectionError('')
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
      setLastCapturePreview(null)
      setLiveDetections([])
      setLiveDetectionFrame({ width: 0, height: 0 })
      setLiveDetectionError('')
    } catch (error) {
      console.error(error)
    }
    setCameraLoading(false)
  }

  async function captureBrowserAttendance(attendanceType = 'checkin') {
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

    const canvas = canvasRef.current
    canvas.width = video.videoWidth
    canvas.height = video.videoHeight

    const context = canvas.getContext('2d')
    context.drawImage(video, 0, 0, canvas.width, canvas.height)
    setAttendanceBusy(true)
    setAttendanceFeedback(null)

    const normalizedAttendanceType = String(attendanceType || 'checkin').toLowerCase() === 'checkout'
      ? 'checkout'
      : 'checkin'
    const actionLabel = normalizedAttendanceType === 'checkout' ? 'Checkout' : 'Checkin'

    try {
      const payload = {
        image_base64: canvas.toDataURL('image/jpeg', 0.9),
        include_preview: true,
        attendance_type: normalizedAttendanceType,
      }

      if (locationEnabled) {
        const nextLocation = await ensureFreshLocationForAttendance()
        if (nextLocation) {
          payload.location = nextLocation
        }
      }

      const res = await api.attendanceImageBase64(payload)
      const capturePreview = res?.preview_image_base64
        ? {
          imageBase64: res.preview_image_base64,
          bbox: Array.isArray(res.detection_bbox) ? res.detection_bbox : null,
          faceCount: Number.isFinite(Number(res.face_count)) ? Number(res.face_count) : null,
        }
        : null
      setLastCapturePreview(capturePreview)

      if (res.success) {
        setAttendanceFeedback({
          type: 'success',
          message: res.message || `${actionLabel} thành công`,
          detail: res.location_text || '',
        })
        await loadTodayRecords()
      } else {
        setAttendanceFeedback({
          type: 'error',
          message: res.message || `Không thể ${actionLabel} bằng camera trình duyệt`,
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
    setLocationBusy(true)
    setLocationError('')
    try {
      const position = await collectBestPositionPromise({
        timeoutMs: GEOLOCATION_SAMPLE_TIMEOUT_MS,
        targetAccuracy: GEOLOCATION_TARGET_ACCURACY_METERS,
        minSamples: GEOLOCATION_MIN_SAMPLE_COUNT,
      })
      const payload = {
        enabled: true,
        location: {
          latitude: position.coords.latitude,
          longitude: position.coords.longitude,
          accuracy: position.coords.accuracy,
          label: 'Thiết bị hiện tại',
          provider: 'browser_gps',
          timestamp: new Date(position.timestamp || Date.now()).toISOString(),
        },
      }
      const res = await api.updateLocationState(payload)
      if (res.success) {
        const nextLocation = res.location || payload.location
        setLocationInfo(nextLocation)
        return nextLocation
      } else {
        setLocationError(res.message || 'Không cập nhật được location')
        return null
      }
    } catch (error) {
      setLocationError(error?.message || 'Không lấy được location')
      return null
    } finally {
      setLocationBusy(false)
    }
  }

  async function ensureFreshLocationForAttendance() {
    if (!locationEnabled) return null

    const accuracy = Number(locationInfo?.accuracy)
    const ageMs = Date.now() - getLocationTimestampMs(locationInfo)
    const hasLocation = locationInfo?.latitude != null && locationInfo?.longitude != null
    const shouldRefresh = (
      !hasLocation
      || !Number.isFinite(accuracy)
      || accuracy > (GEOLOCATION_TARGET_ACCURACY_METERS * 2)
      || ageMs > (GPS_REFRESH_INTERVAL_MS * 2)
    )

    if (!shouldRefresh) {
      return locationInfo
    }

    const nextLocation = await updateBrowserLocation()
    return nextLocation || locationInfo || null
  }

  async function toggleLocation(nextEnabled) {
    setLocationError('')

    if (!nextEnabled) {
      setLocationBusy(true)
      try {
        const res = await api.updateLocationState({ enabled: false })
        if (res.success) setLocationInfo(res.location || null)
      } catch (error) {
        console.error(error)
      } finally {
        setLocationEnabled(false)
        setLocationBusy(false)
      }
      return
    }

    const updated = await updateBrowserLocation()
    setLocationEnabled(!!updated)
  }

  const showActiveCameraWarning = cameraRunning && activeCameraId && selectedCameraId && activeCameraId !== selectedCameraId
  const previewMirror = !!selectedCamera?.camera_options?.preview_mirror
  const activeBrowserDeviceLabel = browserDevices.find(item => item.id === selectedBrowserDeviceId)?.label || ''
  const liveOverlayBoxes = useMemo(() => {
    if (!liveDetections.length) return []

    return liveDetections
      .map((detection, index) => {
        const rect = projectDetectionBoxToPreview(
          detection,
          liveDetectionFrame,
          previewViewport,
          previewMirror,
        )
        if (!rect) return null

        const label = detection?.matched && detection?.name && detection.name !== 'Unknown'
          ? detection.name
          : 'Face'

        return {
          id: `${index}-${(detection?.bbox || []).join('-')}`,
          rect,
          label,
        }
      })
      .filter(Boolean)
  }, [liveDetections, liveDetectionFrame, previewViewport, previewMirror])

  const liveRecognizedNames = useMemo(() => {
    const names = []
    for (const detection of liveDetections) {
      if (!detection?.matched) continue
      const name = String(detection?.name || '').trim()
      if (!name || name === 'Unknown') continue
      if (!names.includes(name)) names.push(name)
    }
    return names
  }, [liveDetections])

  return (
    <div className="grid xl:grid-cols-[1.1fr_0.9fr] gap-4 lg:gap-6">
      <div className="bg-white rounded-2xl shadow-sm border border-slate-200/60 overflow-hidden">
        <div className="px-4 sm:px-5 py-4 border-b border-slate-100 flex items-center justify-between gap-3 flex-wrap">
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

        <div className="p-4 sm:p-5 space-y-4">
          <div
            ref={previewContainerRef}
            className={`relative overflow-hidden rounded-2xl border border-slate-200 bg-slate-950 ${
              browserCameraSelected
                ? 'aspect-[3/4] min-h-[420px] max-h-[78vh] sm:aspect-[4/5] sm:min-h-[520px] lg:aspect-video lg:min-h-0 lg:max-h-none'
                : 'aspect-video'
            }`}
          >
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
                className={`w-full h-full ${browserCameraSelected ? 'object-contain' : 'object-cover'}`}
                style={{ transform: previewMirror ? 'scaleX(-1)' : 'none' }}
              />
            ) : (
              <div className="w-full h-full flex items-center justify-center text-slate-300 text-sm bg-slate-900 text-center px-6">
                {browserCameraSelected
                  ? 'Bật camera để xem luồng từ webcam hoặc điện thoại ngay trên thiết bị hiện tại'
                  : 'Bật camera để xem luồng hình trực tiếp'}
              </div>
            )}

            {browserCameraSelected && cameraRunning && cameraRuntimeMode === 'browser' && (
              <div className="pointer-events-none absolute inset-0 flex items-center justify-center">
                <div className="w-[70%] h-[72%] max-w-[360px] rounded-[36%] border-2 border-white/70 shadow-[0_0_0_9999px_rgba(2,6,23,0.22)]" />
              </div>
            )}

            {browserCameraSelected && cameraRunning && cameraRuntimeMode === 'browser' && liveOverlayBoxes.length > 0 && (
              <div className="pointer-events-none absolute inset-0">
                {liveOverlayBoxes.map(box => (
                  <div
                    key={box.id}
                    className="absolute border-2 border-emerald-400/95 rounded-xl bg-emerald-400/10"
                    style={{
                      left: `${box.rect.left}px`,
                      top: `${box.rect.top}px`,
                      width: `${box.rect.width}px`,
                      height: `${box.rect.height}px`,
                    }}
                  >
                    <span className="absolute -top-6 left-0 px-2 py-0.5 rounded-md text-[11px] font-semibold bg-emerald-500/90 text-white whitespace-nowrap">
                      {box.label}
                    </span>
                  </div>
                ))}
              </div>
            )}
          </div>

          {browserCameraSelected && cameraRunning && cameraRuntimeMode === 'browser' && (
            <div className="flex items-center gap-2 flex-wrap">
              <button
                onClick={() => captureBrowserAttendance('checkin')}
                disabled={attendanceBusy}
                className="w-full sm:w-auto px-4 py-2.5 bg-emerald-600 text-white rounded-xl text-sm font-semibold hover:bg-emerald-700 disabled:opacity-50 transition-colors shadow-sm"
              >
                {attendanceBusy ? 'Đang xử lý...' : 'Checkin'}
              </button>
              <button
                onClick={() => captureBrowserAttendance('checkout')}
                disabled={attendanceBusy}
                className="w-full sm:w-auto px-4 py-2.5 bg-amber-500 text-white rounded-xl text-sm font-semibold hover:bg-amber-600 disabled:opacity-50 transition-colors shadow-sm"
              >
                {attendanceBusy ? 'Đang xử lý...' : 'Checkout'}
              </button>
              <span className="text-xs text-slate-500">Đặt khuôn mặt gọn trong vùng bo tròn rồi bấm chụp.</span>
            </div>
          )}

          {browserCameraSelected && (
            <p className="text-xs sm:text-sm text-slate-500">
              Khung preview trên điện thoại đã chuyển sang tỉ lệ dọc. Hãy giữ toàn bộ khuôn mặt nằm trong vùng bo tròn để tăng độ chính xác khi chụp điểm danh.
            </p>
          )}

          {browserCameraSelected && cameraRunning && cameraRuntimeMode === 'browser' && (
            <div className="rounded-xl border border-emerald-100 bg-emerald-50/70 px-3 py-2 text-xs sm:text-sm text-emerald-700 flex flex-wrap items-center gap-x-4 gap-y-1">
              <span>Realtime detect: {liveDetections.length} khuôn mặt</span>
              <span>Refresh: ~{(LIVE_DETECT_INTERVAL_MS / 1000).toFixed(1)}s</span>
              {liveDetectionError && <span className="text-red-600">{liveDetectionError}</span>}
            </div>
          )}

          {browserCameraSelected && cameraRunning && cameraRuntimeMode === 'browser' && (
            <div className="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-xs sm:text-sm text-slate-700 space-y-2">
              <div className="flex flex-wrap items-center gap-4">
                <label className="inline-flex items-center gap-2 cursor-pointer">
                  <input
                    type="checkbox"
                    checked={liveRecognizeEnabled}
                    onChange={event => {
                      const enabled = event.target.checked
                      setLiveRecognizeEnabled(enabled)
                      if (!enabled) {
                        setAutoAttendanceEnabled(false)
                      }
                    }}
                    className="rounded border-slate-300"
                  />
                  <span>Nhận dạng realtime</span>
                </label>

                <label className="inline-flex items-center gap-2 cursor-pointer">
                  <input
                    type="checkbox"
                    checked={autoAttendanceEnabled}
                    onChange={event => {
                      const enabled = event.target.checked
                      setAutoAttendanceEnabled(enabled)
                      if (enabled) {
                        setLiveRecognizeEnabled(true)
                      }
                    }}
                    className="rounded border-slate-300"
                  />
                  <span>Tự điểm danh (không cần bấm chụp)</span>
                </label>
              </div>

              {liveRecognizeEnabled && (
                <p className="text-slate-600">
                  {liveRecognizedNames.length > 0
                    ? `Đang nhận dạng: ${liveRecognizedNames.join(', ')}`
                    : 'Đang nhận dạng: chưa có khuôn mặt khớp'}
                </p>
              )}

              {autoAttendanceEnabled && (
                <p className="text-slate-500">
                  Hệ thống sẽ tự điểm danh khi khuôn mặt được nhận dạng ổn định {AUTO_ATTENDANCE_STREAK_REQUIRED} khung liên tiếp.
                </p>
              )}
            </div>
          )}

          <canvas ref={canvasRef} className="hidden" />
          <canvas ref={detectCanvasRef} className="hidden" />

          <div className="grid sm:grid-cols-2 xl:grid-cols-[1fr_auto_auto] gap-3 items-center">
            <select
              value={selectedCameraId}
              onChange={event => setSelectedCameraId(event.target.value)}
              className="sm:col-span-2 xl:col-span-1 px-3 py-2.5 border border-slate-200 rounded-xl text-sm bg-white focus:ring-2 focus:ring-primary-500/20 focus:border-primary-400 transition-all"
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
                className="w-full sm:w-auto px-4 py-2.5 bg-primary-600 text-white rounded-xl text-sm font-semibold hover:bg-primary-700 disabled:opacity-50 transition-colors shadow-sm"
              >
                {cameraLoading ? 'Đang bật...' : 'Bật camera'}
              </button>
            ) : (
              <button
                onClick={handleStopCamera}
                disabled={cameraLoading}
                className="w-full sm:w-auto px-4 py-2.5 bg-red-500 text-white rounded-xl text-sm font-semibold hover:bg-red-600 disabled:opacity-50 transition-colors shadow-sm"
              >
                {cameraLoading ? 'Đang tắt...' : 'Tắt camera'}
              </button>
            )}

            <Link
              to="/quan-ly-camera"
              className="w-full sm:w-auto px-4 py-2.5 bg-slate-100 text-slate-700 rounded-xl text-sm font-semibold hover:bg-slate-200 transition-colors text-center"
            >
              Quản lý camera
            </Link>
          </div>

          {browserCameraSelected && (
            <div className="rounded-xl border border-blue-100 bg-blue-50/70 px-4 py-3 text-sm text-blue-700 space-y-3">
              <p>
                Camera này hoạt động trực tiếp trên thiết bị đang mở trang điểm danh. Phù hợp cho webcam laptop hoặc camera điện thoại khi dùng webview.
              </p>
              {browserCameraSupported && (
                <div className="grid sm:grid-cols-[1fr_auto] gap-2">
                  <select
                    value={selectedBrowserDeviceId}
                    onChange={event => handleBrowserDeviceChange(event.target.value)}
                    className="w-full px-3 py-2 border border-blue-200 rounded-xl text-sm bg-white text-slate-700"
                  >
                    <option value="">Tự chọn camera theo thiết bị</option>
                    {browserDevices.map(device => (
                      <option key={device.id} value={device.id}>{device.label}</option>
                    ))}
                  </select>
                  <button
                    type="button"
                    onClick={() => loadBrowserDevices({ withPermission: true })}
                    disabled={browserDevicesLoading}
                    className="px-3 py-2 rounded-xl bg-white border border-blue-200 text-blue-700 text-sm font-medium hover:bg-blue-100 disabled:opacity-50"
                  >
                    {browserDevicesLoading ? 'Đang quét...' : 'Quét camera'}
                  </button>
                </div>
              )}
              {selectedBrowserDeviceId && (
                <p className="text-xs text-blue-800">
                  Đang ưu tiên camera cố định: {activeBrowserDeviceLabel || selectedBrowserDeviceId}
                </p>
              )}
              {!browserCameraSupported && !requiresSecureContext && (
                <p className="text-red-600">
                  Trình duyệt hoặc webview hiện tại chưa hỗ trợ mở camera trực tiếp bằng getUserMedia.
                </p>
              )}
              {requiresSecureContext && (
                <p className="text-amber-700">
                  Thiết bị đang mở bằng HTTP theo IP LAN. iPhone/WebView có thể chặn camera live; hãy bật HTTPS để mở camera trình duyệt.
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
              {browserCameraSelected && selectedBrowserDeviceId && (
                <p>
                  Thiết bị cố định: {activeBrowserDeviceLabel || selectedBrowserDeviceId}
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

          {browserCameraSelected && lastCapturePreview?.imageBase64 && (
            <div className="rounded-xl border border-slate-200 bg-slate-50 p-3 space-y-2">
              <p className="text-sm font-medium text-slate-700">Ảnh nhận diện lần chụp gần nhất (có bounding box)</p>
              <img
                src={lastCapturePreview.imageBase64}
                alt="Detection preview"
                className="w-full max-h-56 object-contain rounded-lg border border-slate-200 bg-black"
              />
              <div className="text-xs text-slate-500 flex items-center justify-between gap-2">
                <span>Bounding box hiện hiển thị trên ảnh chụp gửi nhận diện.</span>
                {Number.isFinite(lastCapturePreview.faceCount) && (
                  <span>Faces: {lastCapturePreview.faceCount}</span>
                )}
              </div>
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
                Bật GPS để xin quyền truy cập vị trí, sau đó hệ thống sẽ lưu vị trí tại thời điểm chấm công.
              </p>
            </div>
            <div className="flex items-center gap-2">
              <button
                type="button"
                onClick={() => toggleLocation(!locationEnabled)}
                disabled={locationBusy}
                className={`px-3 py-2 rounded-xl text-sm font-medium transition-colors disabled:opacity-50 ${
                  locationEnabled
                    ? 'bg-emerald-100 text-emerald-700 hover:bg-emerald-200'
                    : 'bg-primary-600 text-white hover:bg-primary-700'
                }`}
              >
                {locationBusy
                  ? 'Đang xử lý...'
                  : (locationEnabled ? 'Tắt GPS' : 'Bật GPS')}
              </button>
              <button
                type="button"
                onClick={() => updateBrowserLocation()}
                disabled={locationBusy || !locationEnabled}
                className="px-3 py-2 rounded-xl text-sm font-medium bg-slate-100 text-slate-700 hover:bg-slate-200 disabled:opacity-50"
              >
                Lấy lại vị trí
              </button>
            </div>
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
                <div key={`${record.employee_id}-${record.check_in_time || record.time}-${record.check_out_time || ''}-${index}`} className="px-5 py-4 flex items-start justify-between gap-4">
                  <div>
                    <p className="font-semibold text-slate-800">{record.name}</p>
                    <p className="text-sm text-slate-500 mt-1">
                      {record.employee_id} · {record.department || 'Chưa có phòng ban'}
                    </p>
                    {record.check_in_location_text && (
                      <p className="text-xs text-slate-400 mt-1">Vị trí Checkin: {record.check_in_location_text}</p>
                    )}
                    {record.check_out_location_text && (
                      <p className="text-xs text-slate-400 mt-1">Vị trí Checkout: {record.check_out_location_text}</p>
                    )}
                  </div>
                  <div className="text-right shrink-0">
                    <span className="inline-flex px-2.5 py-1 rounded-full text-xs font-medium bg-amber-50 text-amber-700">
                      {record.status || 'Điểm danh'}
                    </span>
                    <p className="text-sm text-slate-500 mt-2">Vào: {record.check_in_time || record.time || '--:--:--'}</p>
                    <p className="text-sm text-slate-500">Ra: {record.check_out_time || '--:--:--'}</p>
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
