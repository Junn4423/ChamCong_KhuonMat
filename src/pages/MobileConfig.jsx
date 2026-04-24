import { useEffect, useMemo, useState } from 'react'
import { LoaderCircle, QrCode, RefreshCw, Save, Smartphone, Wifi } from 'lucide-react'
import QRCode from 'qrcode'
import { api } from '../services/api'

const DEFAULT_FORM = {
  enabled: true,
  server_name: 'FaceCheck',
  allow_udp_discovery: true,
  allow_qr_pairing: true,
  discovery_port: 45876,
  api_port: 5000,
  web_port: 5173,
  qr_ttl_seconds: 300,
  pass_hint: '',
}

function toSafeInt(value, fallback, min, max) {
  const parsed = Number.parseInt(value, 10)
  if (!Number.isFinite(parsed)) return fallback
  return Math.max(min, Math.min(max, parsed))
}

export default function MobileConfig() {
  const [loading, setLoading] = useState(true)
  const [saving, setSaving] = useState(false)
  const [generatingQr, setGeneratingQr] = useState(false)
  const [statusText, setStatusText] = useState('')
  const [statusType, setStatusType] = useState('info')
  const [form, setForm] = useState({ ...DEFAULT_FORM })
  const [pairVersion, setPairVersion] = useState(1)
  const [serverId, setServerId] = useState('')
  const [passConfigured, setPassConfigured] = useState(false)
  const [passInput, setPassInput] = useState('')
  const [confirmPassInput, setConfirmPassInput] = useState('')
  const [lanCandidates, setLanCandidates] = useState([])
  const [selectedQrHost, setSelectedQrHost] = useState('')
  const [qrSession, setQrSession] = useState(null)
  const [qrDataUrl, setQrDataUrl] = useState('')
  const [activeQrSessions, setActiveQrSessions] = useState([])

  const pairMethodsLabel = useMemo(() => {
    const methods = []
    methods.push('Pass')
    if (form.allow_udp_discovery) methods.push('UDP discovery')
    if (form.allow_qr_pairing) methods.push('QR pairing')
    return methods.join(' + ')
  }, [form.allow_qr_pairing, form.allow_udp_discovery])

  async function loadData() {
    setLoading(true)
    try {
      const response = await api.getMobileConfigSettings()
      if (!response?.success) {
        setStatusType('error')
        setStatusText(response?.message || 'Không thể tải cấu hình mobile.')
        return
      }

      const settings = response.settings || {}
      setForm({
        enabled: settings.enabled !== false,
        server_name: settings.server_name || DEFAULT_FORM.server_name,
        allow_udp_discovery: settings.allow_udp_discovery !== false,
        allow_qr_pairing: settings.allow_qr_pairing !== false,
        discovery_port: toSafeInt(settings.discovery_port, DEFAULT_FORM.discovery_port, 1024, 65535),
        api_port: toSafeInt(settings.api_port, DEFAULT_FORM.api_port, 1, 65535),
        web_port: toSafeInt(settings.web_port, DEFAULT_FORM.web_port, 1, 65535),
        qr_ttl_seconds: toSafeInt(settings.qr_ttl_seconds, DEFAULT_FORM.qr_ttl_seconds, 30, 3600),
        pass_hint: settings.pass_hint || '',
      })
      setPassConfigured(Boolean(settings.pass_configured))
      setPairVersion(toSafeInt(settings.pair_version, 1, 1, 2_000_000_000))
      setServerId(settings.server_id || '')

      const nextCandidates = Array.isArray(response.lan_ipv4_candidates)
        ? response.lan_ipv4_candidates
        : []
      setLanCandidates(nextCandidates)
      setSelectedQrHost(current => current || nextCandidates[0] || '')
      setActiveQrSessions(Array.isArray(response.active_qr_sessions) ? response.active_qr_sessions : [])
      setStatusText('')
    } catch (error) {
      setStatusType('error')
      setStatusText(error?.message || 'Không thể tải cấu hình mobile.')
    } finally {
      setLoading(false)
    }
  }

  useEffect(() => {
    loadData()
  }, [])

  function updateForm(field, value) {
    setForm(prev => ({
      ...prev,
      [field]: value,
    }))
  }

  async function handleSave() {
    if (passInput || confirmPassInput) {
      if (passInput.length < 4) {
        setStatusType('error')
        setStatusText('Pass mobile cần tối thiểu 4 ký tự.')
        return
      }
      if (passInput !== confirmPassInput) {
        setStatusType('error')
        setStatusText('Nhập lại pass chưa trùng nhau.')
        return
      }
    }

    setSaving(true)
    setStatusText('')

    try {
      const payload = {
        enabled: form.enabled,
        server_name: form.server_name,
        allow_udp_discovery: form.allow_udp_discovery,
        allow_qr_pairing: form.allow_qr_pairing,
        discovery_port: toSafeInt(form.discovery_port, DEFAULT_FORM.discovery_port, 1024, 65535),
        api_port: toSafeInt(form.api_port, DEFAULT_FORM.api_port, 1, 65535),
        web_port: toSafeInt(form.web_port, DEFAULT_FORM.web_port, 1, 65535),
        qr_ttl_seconds: toSafeInt(form.qr_ttl_seconds, DEFAULT_FORM.qr_ttl_seconds, 30, 3600),
        pass_hint: form.pass_hint,
      }

      if (passInput) {
        payload.pair_pass = passInput
      }

      const response = await api.saveMobileConfigSettings(payload)
      if (!response?.success) {
        setStatusType('error')
        setStatusText(response?.message || 'Không thể lưu cấu hình mobile.')
        return
      }

      setPassInput('')
      setConfirmPassInput('')
      setStatusType('success')
      setStatusText(response.message || 'Đã lưu cấu hình mobile.')
      await loadData()
    } catch (error) {
      setStatusType('error')
      setStatusText(error?.message || 'Không thể lưu cấu hình mobile.')
    } finally {
      setSaving(false)
    }
  }

  async function handleGenerateQr() {
    setGeneratingQr(true)
    setStatusText('')
    try {
      const response = await api.createMobileQrSession({
        target_host: selectedQrHost,
      })

      if (!response?.success) {
        setStatusType('error')
        setStatusText(response?.message || 'Không thể tạo QR pairing.')
        return
      }

      const session = response.session || null
      setQrSession(session)
      setActiveQrSessions(Array.isArray(response.active_qr_sessions) ? response.active_qr_sessions : [])

      const qrPayload = session?.qr_payload
      const qrText = JSON.stringify(qrPayload || {})
      if (qrText && qrText !== '{}') {
        const generated = await QRCode.toDataURL(qrText, {
          errorCorrectionLevel: 'M',
          margin: 1,
          width: 256,
        })
        setQrDataUrl(generated)
      } else {
        setQrDataUrl('')
      }

      setStatusType('success')
      setStatusText('Đã tạo QR pairing. Nhập pass trên mobile để hoàn tất kết nối.')
    } catch (error) {
      setStatusType('error')
      setStatusText(error?.message || 'Không thể tạo QR pairing.')
    } finally {
      setGeneratingQr(false)
    }
  }

  if (loading) {
    return (
      <div className="min-h-[240px] flex items-center justify-center">
        <div className="flex items-center gap-2 text-slate-500 text-sm">
          <LoaderCircle size={16} className="animate-spin" />
          Đang tải cấu hình mobile...
        </div>
      </div>
    )
  }

  return (
    <div className="space-y-4 md:space-y-6">
      <div className="space-y-1">
        <h1 className="text-2xl font-bold text-slate-800 tracking-tight">Cấu hình mobile</h1>
        <p className="text-sm text-slate-500">
          Thiết lập auto-config cho app mobile: UDP discovery + pass pairing và QR pairing + pass.
        </p>
      </div>

      <div className="bg-white rounded-2xl shadow-sm border border-slate-200/60 px-4 sm:px-5 py-4 flex flex-wrap items-center justify-between gap-3">
        <div className="space-y-1">
          <p className="text-sm font-semibold text-slate-800">Thông tin pairing hiện tại</p>
          <p className="text-xs text-slate-500">
            Server ID: <span className="font-mono">{serverId || '-'}</span> | Pair version: {pairVersion} | Method: {pairMethodsLabel}
          </p>
          <p className="text-xs text-slate-500">
            Pass mobile: {passConfigured ? 'Đã cấu hình' : 'Chưa cấu hình'}
          </p>
        </div>

        <div className="flex items-center gap-2">
          <button
            type="button"
            onClick={loadData}
            disabled={saving || generatingQr}
            className="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200 hover:bg-slate-200 disabled:opacity-60 disabled:cursor-not-allowed transition-colors"
          >
            <RefreshCw size={14} />
            Tải lại
          </button>
          <button
            type="button"
            onClick={handleSave}
            disabled={saving || generatingQr}
            className="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-xs font-semibold bg-primary-600 text-white border border-primary-600 hover:bg-primary-700 disabled:opacity-60 disabled:cursor-not-allowed transition-colors"
          >
            {saving ? <LoaderCircle size={14} className="animate-spin" /> : <Save size={14} />}
            {saving ? 'Đang lưu...' : 'Lưu cấu hình'}
          </button>
        </div>
      </div>

      {statusText && (
        <div className={`rounded-xl px-4 py-3 text-sm border ${
          statusType === 'success'
            ? 'border-emerald-200 bg-emerald-50 text-emerald-700'
            : 'border-red-200 bg-red-50 text-red-600'
        }`}>
          {statusText}
        </div>
      )}

      <div className="grid xl:grid-cols-[1.05fr_0.95fr] gap-4 lg:gap-6">
        <div className="bg-white rounded-2xl shadow-sm border border-slate-200/60 overflow-hidden">
          <div className="px-4 sm:px-5 py-4 border-b border-slate-100">
            <h2 className="text-base font-semibold text-slate-800">Tự động kết nối mobile</h2>
            <p className="text-xs text-slate-500 mt-1">
              Mobile sẽ tự UDP discover server trong LAN, sau đó user chỉ cần nhập pass 1 lần.
            </p>
          </div>

          <div className="p-4 sm:p-5 space-y-4">
            <label className="space-y-1 block">
              <span className="text-xs text-slate-500">Tên hệ thống trên mobile</span>
              <input
                type="text"
                value={form.server_name}
                onChange={event => updateForm('server_name', event.target.value)}
                className="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm bg-white focus:ring-2 focus:ring-primary-500/20 focus:border-primary-400"
              />
            </label>

            <div className="grid grid-cols-3 gap-2">
              <label className="space-y-1">
                <span className="text-xs text-slate-500">UDP port</span>
                <input
                  type="number"
                  min={1024}
                  max={65535}
                  value={form.discovery_port}
                  onChange={event => updateForm('discovery_port', event.target.value)}
                  className="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm bg-white"
                />
              </label>
              <label className="space-y-1">
                <span className="text-xs text-slate-500">API port</span>
                <input
                  type="number"
                  min={1}
                  max={65535}
                  value={form.api_port}
                  onChange={event => updateForm('api_port', event.target.value)}
                  className="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm bg-white"
                />
              </label>
              <label className="space-y-1">
                <span className="text-xs text-slate-500">Web port</span>
                <input
                  type="number"
                  min={1}
                  max={65535}
                  value={form.web_port}
                  onChange={event => updateForm('web_port', event.target.value)}
                  className="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm bg-white"
                />
              </label>
            </div>

            <div className="space-y-2">
              <label className="flex items-center justify-between border border-slate-200 rounded-lg px-3 py-2">
                <span className="text-sm text-slate-700">Bật module mobile pairing</span>
                <input
                  type="checkbox"
                  checked={form.enabled}
                  onChange={event => updateForm('enabled', event.target.checked)}
                />
              </label>
              <label className="flex items-center justify-between border border-slate-200 rounded-lg px-3 py-2">
                <span className="text-sm text-slate-700">Cho phép UDP discovery</span>
                <input
                  type="checkbox"
                  checked={form.allow_udp_discovery}
                  onChange={event => updateForm('allow_udp_discovery', event.target.checked)}
                />
              </label>
              <label className="flex items-center justify-between border border-slate-200 rounded-lg px-3 py-2">
                <span className="text-sm text-slate-700">Cho phép QR pairing</span>
                <input
                  type="checkbox"
                  checked={form.allow_qr_pairing}
                  onChange={event => updateForm('allow_qr_pairing', event.target.checked)}
                />
              </label>
            </div>

            <label className="space-y-1 block">
              <span className="text-xs text-slate-500">Gợi ý pass (hiện trên mobile)</span>
              <input
                type="text"
                value={form.pass_hint}
                onChange={event => updateForm('pass_hint', event.target.value)}
                className="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm bg-white"
                placeholder="Ví dụ: Liên hệ IT để nhận pass"
              />
            </label>

            <div className="grid grid-cols-1 sm:grid-cols-2 gap-2">
              <label className="space-y-1 block">
                <span className="text-xs text-slate-500">Pass mobile mới</span>
                <input
                  type="password"
                  value={passInput}
                  onChange={event => setPassInput(event.target.value)}
                  className="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm bg-white"
                  placeholder="Nhập pass mới"
                />
              </label>
              <label className="space-y-1 block">
                <span className="text-xs text-slate-500">Nhập lại pass</span>
                <input
                  type="password"
                  value={confirmPassInput}
                  onChange={event => setConfirmPassInput(event.target.value)}
                  className="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm bg-white"
                  placeholder="Nhập lại pass"
                />
              </label>
            </div>
          </div>
        </div>

        <div className="space-y-4">
          <div className="bg-white rounded-2xl shadow-sm border border-slate-200/60 overflow-hidden">
            <div className="px-4 sm:px-5 py-4 border-b border-slate-100 flex items-center gap-2">
              <QrCode size={16} className="text-slate-600" />
              <h2 className="text-base font-semibold text-slate-800">QR pairing + pass</h2>
            </div>
            <div className="p-4 sm:p-5 space-y-3">
              <label className="space-y-1 block">
                <span className="text-xs text-slate-500">IP để đóng gói vào QR</span>
                <select
                  value={selectedQrHost}
                  onChange={event => setSelectedQrHost(event.target.value)}
                  className="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm bg-white"
                >
                  {lanCandidates.length === 0 ? <option value="">(Không có IP LAN)</option> : null}
                  {lanCandidates.map(ip => (
                    <option key={ip} value={ip}>{ip}</option>
                  ))}
                </select>
              </label>
              <label className="space-y-1 block">
                <span className="text-xs text-slate-500">QR TTL (giây)</span>
                <input
                  type="number"
                  min={30}
                  max={3600}
                  value={form.qr_ttl_seconds}
                  onChange={event => updateForm('qr_ttl_seconds', event.target.value)}
                  className="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm bg-white"
                />
              </label>

              <button
                type="button"
                onClick={handleGenerateQr}
                disabled={generatingQr || !form.allow_qr_pairing}
                className="w-full inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg text-xs font-semibold bg-sky-600 text-white border border-sky-600 hover:bg-sky-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
              >
                {generatingQr ? <LoaderCircle size={14} className="animate-spin" /> : <QrCode size={14} />}
                {generatingQr ? 'Đang tạo...' : 'Tạo QR pairing'}
              </button>

              {qrSession && qrDataUrl ? (
                <div className="border border-slate-200 rounded-xl p-3 bg-slate-50">
                  <p className="text-xs text-slate-500">Pairing code</p>
                  <p className="font-mono text-base font-semibold text-slate-800">{qrSession.pairing_code}</p>
                  <p className="text-[11px] text-slate-500 mb-2">Hết hạn: {qrSession.expires_at}</p>
                  <img src={qrDataUrl} alt="Mobile pairing QR" className="w-48 h-48 rounded-lg border border-slate-200 bg-white mx-auto" />
                </div>
              ) : null}
            </div>
          </div>

          <div className="bg-white rounded-2xl shadow-sm border border-slate-200/60 overflow-hidden">
            <div className="px-4 sm:px-5 py-4 border-b border-slate-100 flex items-center gap-2">
              <Wifi size={16} className="text-slate-600" />
              <h2 className="text-base font-semibold text-slate-800">LAN và QR sessions</h2>
            </div>
            <div className="p-4 sm:p-5 space-y-3">
              <div>
                <p className="text-xs text-slate-500 mb-1">LAN IP candidates</p>
                {lanCandidates.length === 0 ? (
                  <p className="text-xs text-amber-700">Chưa tìm thấy IP LAN hợp lệ trên máy chủ.</p>
                ) : (
                  <div className="flex flex-wrap gap-1.5">
                    {lanCandidates.map(ip => (
                      <span key={ip} className="inline-flex items-center px-2 py-1 rounded-full text-xs bg-emerald-50 text-emerald-700 border border-emerald-200 font-mono">
                        {ip}
                      </span>
                    ))}
                  </div>
                )}
              </div>

              <div>
                <p className="text-xs text-slate-500 mb-1">Active QR sessions</p>
                {activeQrSessions.length === 0 ? (
                  <p className="text-xs text-slate-500">Chưa có session nào.</p>
                ) : (
                  <div className="space-y-1">
                    {activeQrSessions.map(session => (
                      <div key={session.pairing_code} className="text-xs text-slate-600 border border-slate-200 rounded-lg px-2 py-1.5">
                        <span className="font-mono font-semibold">{session.pairing_code}</span>
                        {' | '}
                        còn {session.seconds_left}s
                        {session.used ? ' | đã dùng' : ''}
                        {session.target_host ? ` | host ${session.target_host}` : ''}
                      </div>
                    ))}
                  </div>
                )}
              </div>
            </div>
          </div>

          <div className="bg-white rounded-2xl shadow-sm border border-slate-200/60 px-4 sm:px-5 py-4 text-xs text-slate-600">
            <p className="font-semibold text-slate-700 mb-1 flex items-center gap-1.5">
              <Smartphone size={14} />
              Flow user mobile
            </p>
            <p>1) Mở app mobile trong cùng LAN.</p>
            <p>2) App tự UDP detect server và hiện tên hệ thống.</p>
            <p>3) User nhập pass mobile 1 lần (hoặc pairing code từ QR + pass).</p>
            <p>4) App lưu server_id + pair_version; khi pass đổi, pair_version tăng và app sẽ yêu cầu nhập lại pass.</p>
          </div>
        </div>
      </div>
    </div>
  )
}
