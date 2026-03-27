import { useState, useRef } from 'react'
import { api } from '../services/api'

export default function Register() {
  const [mode, setMode] = useState('manual') // manual | erp | camera
  const [form, setForm] = useState({ name: '', employee_id: '', department: '', position: '' })
  const [imageFile, setImageFile] = useState(null)
  const [imagePreview, setImagePreview] = useState(null)
  const [erpId, setErpId] = useState('')
  const [erpInfo, setErpInfo] = useState(null)
  const [loading, setLoading] = useState(false)
  const [message, setMessage] = useState(null)
  const videoRef = useRef(null)
  const canvasRef = useRef(null)
  const streamRef = useRef(null)
  const [cameraActive, setCameraActive] = useState(false)
  const [capturedImage, setCapturedImage] = useState(null)
  const [cameraDevices, setCameraDevices] = useState([])
  const [selectedDeviceId, setSelectedDeviceId] = useState('')

  function handleFileChange(e) {
    const file = e.target.files[0]
    if (file) {
      setImageFile(file)
      const reader = new FileReader()
      reader.onload = ev => setImagePreview(ev.target.result)
      reader.readAsDataURL(file)
    }
  }

  async function handleManualRegister(e) {
    e.preventDefault()
    if (!form.name || !form.employee_id) {
      setMessage({ type: 'error', text: 'Vui lòng nhập tên và mã nhân viên' })
      return
    }

    setLoading(true)
    setMessage(null)
    try {
      let res
      if (capturedImage) {
        res = await api.registerBase64({
          ...form,
          image_base64: capturedImage,
        })
      } else if (imageFile) {
        const fd = new FormData()
        fd.append('name', form.name)
        fd.append('employee_id', form.employee_id)
        fd.append('department', form.department)
        fd.append('position', form.position)
        fd.append('image', imageFile)
        res = await api.register(fd)
      } else {
        setMessage({ type: 'error', text: 'Vui lòng chọn ảnh hoặc chụp từ camera' })
        setLoading(false)
        return
      }

      if (res.success) {
        setMessage({ type: 'success', text: res.message })
        setForm({ name: '', employee_id: '', department: '', position: '' })
        setImageFile(null)
        setImagePreview(null)
        setCapturedImage(null)
      } else {
        setMessage({ type: 'error', text: res.message })
      }
    } catch (e) {
      setMessage({ type: 'error', text: 'Lỗi kết nối server' })
    }
    setLoading(false)
  }

  async function handleErpLookup() {
    if (!erpId) return
    setLoading(true)
    setErpInfo(null)
    try {
      const res = await api.getErpEmployeeInfo(erpId)
      if (res.success) {
        setErpInfo(res.employee)
      } else {
        setMessage({ type: 'error', text: res.message })
      }
    } catch (e) {
      setMessage({ type: 'error', text: 'Lỗi kết nối server' })
    }
    setLoading(false)
  }

  async function handleErpRegister() {
    if (!erpId) return
    setLoading(true)
    setMessage(null)
    try {
      const res = await api.registerFromErp(erpId)
      if (res.success) {
        setMessage({ type: 'success', text: res.message })
        setErpId('')
        setErpInfo(null)
      } else {
        setMessage({ type: 'error', text: res.message })
      }
    } catch (e) {
      setMessage({ type: 'error', text: 'Lỗi kết nối server' })
    }
    setLoading(false)
  }

  async function loadCameraDevices() {
    try {
      // Need a temporary stream to trigger permission prompt first
      const tempStream = await navigator.mediaDevices.getUserMedia({ video: true })
      tempStream.getTracks().forEach(t => t.stop())
      const devices = await navigator.mediaDevices.enumerateDevices()
      const videoDevices = devices.filter(d => d.kind === 'videoinput')
      setCameraDevices(videoDevices)
      if (videoDevices.length > 0 && !selectedDeviceId) {
        setSelectedDeviceId(videoDevices[0].deviceId)
      }
      return videoDevices
    } catch (e) {
      console.error('Camera enumeration failed:', e)
      alert('Không thể truy cập camera. Vui lòng kiểm tra quyền truy cập camera.')
      return []
    }
  }

  async function startCamera() {
    try {
      let devices = cameraDevices
      if (devices.length === 0) {
        devices = await loadCameraDevices()
        if (devices.length === 0) return
      }
      const constraints = {
        video: selectedDeviceId
          ? { deviceId: { exact: selectedDeviceId }, width: 640, height: 480 }
          : { width: 640, height: 480, facingMode: 'user' }
      }
      const stream = await navigator.mediaDevices.getUserMedia(constraints)
      streamRef.current = stream
      if (videoRef.current) videoRef.current.srcObject = stream
      setCameraActive(true)
    } catch (e) {
      console.error('Camera start failed:', e)
      alert('Không thể truy cập camera: ' + e.message)
    }
  }

  function stopCamera() {
    if (streamRef.current) {
      streamRef.current.getTracks().forEach(t => t.stop())
      streamRef.current = null
    }
    setCameraActive(false)
  }

  function capturePhoto() {
    if (!videoRef.current || !canvasRef.current) return
    const canvas = canvasRef.current
    const video = videoRef.current
    canvas.width = video.videoWidth
    canvas.height = video.videoHeight
    canvas.getContext('2d').drawImage(video, 0, 0)
    const dataUrl = canvas.toDataURL('image/jpeg', 0.9)
    setCapturedImage(dataUrl)
    setImagePreview(dataUrl)
    stopCamera()
  }

  return (
    <div className="space-y-6">
      <h1 className="text-2xl font-bold text-gray-800">Đăng ký nhân viên</h1>

      {/* Mode selector */}
      <div className="flex gap-2">
        <button
          onClick={() => setMode('manual')}
          className={`px-4 py-2 rounded-lg text-sm font-medium ${
            mode === 'manual' ? 'bg-primary-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'
          }`}
        >
          Nhập thủ công
        </button>
        <button
          onClick={() => setMode('erp')}
          className={`px-4 py-2 rounded-lg text-sm font-medium ${
            mode === 'erp' ? 'bg-primary-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'
          }`}
        >
          Từ ERP
        </button>
      </div>

      {message && (
        <div className={`p-4 rounded-lg text-sm ${
          message.type === 'success' ? 'bg-green-50 text-green-700 border border-green-200'
            : 'bg-red-50 text-red-700 border border-red-200'
        }`}>
          {message.text}
        </div>
      )}

      {mode === 'manual' && (
        <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
          <form onSubmit={handleManualRegister} className="space-y-4">
            <div className="grid sm:grid-cols-2 gap-4">
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">Họ tên *</label>
                <input
                  type="text"
                  value={form.name}
                  onChange={e => setForm({ ...form, name: e.target.value })}
                  className="w-full px-3 py-2 border border-gray-300 rounded-lg"
                  required
                />
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">Mã nhân viên *</label>
                <input
                  type="text"
                  value={form.employee_id}
                  onChange={e => setForm({ ...form, employee_id: e.target.value })}
                  className="w-full px-3 py-2 border border-gray-300 rounded-lg"
                  required
                />
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">Phòng ban</label>
                <input
                  type="text"
                  value={form.department}
                  onChange={e => setForm({ ...form, department: e.target.value })}
                  className="w-full px-3 py-2 border border-gray-300 rounded-lg"
                />
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">Chức vụ</label>
                <input
                  type="text"
                  value={form.position}
                  onChange={e => setForm({ ...form, position: e.target.value })}
                  className="w-full px-3 py-2 border border-gray-300 rounded-lg"
                />
              </div>
            </div>

            {/* Image input */}
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Ảnh khuôn mặt *</label>
              <div className="flex gap-3 items-start">
                <div>
                  <input
                    type="file"
                    accept="image/*"
                    onChange={handleFileChange}
                    className="text-sm"
                  />
                  <div className="mt-2 flex flex-wrap items-center gap-2">
                    {cameraDevices.length > 0 && (
                      <select
                        value={selectedDeviceId}
                        onChange={e => setSelectedDeviceId(e.target.value)}
                        className="px-2 py-1.5 border border-gray-300 rounded-lg text-sm max-w-[200px]"
                      >
                        {cameraDevices.map((d, i) => (
                          <option key={d.deviceId} value={d.deviceId}>
                            {d.label || `Camera ${i + 1}`}
                          </option>
                        ))}
                      </select>
                    )}
                    <button
                      type="button"
                      onClick={cameraActive ? capturePhoto : startCamera}
                      className="px-3 py-1.5 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700"
                    >
                      {cameraActive ? 'Chụp ảnh' : 'Mở Camera'}
                    </button>
                    {cameraActive && (
                      <button
                        type="button"
                        onClick={stopCamera}
                        className="px-3 py-1.5 bg-gray-500 text-white rounded-lg text-sm hover:bg-gray-600"
                      >
                        Đóng Camera
                      </button>
                    )}
                  </div>
                </div>
                {imagePreview && (
                  <img src={imagePreview} alt="Preview" className="w-32 h-32 object-cover rounded-lg border" />
                )}
              </div>
              {cameraActive && (
                <div className="mt-3">
                  <video ref={videoRef} autoPlay playsInline muted className="w-64 rounded-lg" />
                  <canvas ref={canvasRef} className="hidden" />
                </div>
              )}
            </div>

            <button
              type="submit"
              disabled={loading}
              className="px-6 py-2.5 bg-primary-600 text-white rounded-lg font-medium hover:bg-primary-700 disabled:opacity-50"
            >
              {loading ? 'Đang xử lý...' : 'Đăng ký'}
            </button>
          </form>
        </div>
      )}

      {mode === 'erp' && (
        <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-4">
          <div className="flex gap-3">
            <input
              type="text"
              value={erpId}
              onChange={e => setErpId(e.target.value)}
              placeholder="Nhập mã nhân viên ERP"
              className="flex-1 px-3 py-2 border border-gray-300 rounded-lg"
            />
            <button
              onClick={handleErpLookup}
              disabled={loading}
              className="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 disabled:opacity-50"
            >
              Tra cứu
            </button>
          </div>

          {erpInfo && (
            <div className="p-4 bg-gray-50 rounded-lg space-y-2">
              <div className="flex gap-4">
                {erpInfo.image_base64 && (
                  <img src={erpInfo.image_base64} alt="" className="w-24 h-24 object-cover rounded-lg" />
                )}
                <div>
                  <p className="font-medium text-gray-800">{erpInfo.name}</p>
                  <p className="text-sm text-gray-500">Mã: {erpInfo.employee_id}</p>
                  <p className="text-sm text-gray-500">Phòng ban: {erpInfo.department}</p>
                  <p className="text-sm text-gray-500">Chức vụ: {erpInfo.position}</p>
                </div>
              </div>
              <button
                onClick={handleErpRegister}
                disabled={loading}
                className="px-4 py-2 bg-primary-600 text-white rounded-lg text-sm font-medium hover:bg-primary-700 disabled:opacity-50"
              >
                {loading ? 'Đang xử lý...' : 'Đăng ký nhân viên này'}
              </button>
            </div>
          )}
        </div>
      )}
    </div>
  )
}
