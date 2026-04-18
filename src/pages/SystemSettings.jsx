import React, { useEffect, useMemo, useState } from 'react'
import {
  Globe2,
  Languages,
  LoaderCircle,
  RefreshCw,
  ToggleLeft,
  ToggleRight,
} from 'lucide-react'
import {
  MODULE_SETTINGS_EVENT,
  MODULE_TOGGLE_DEFINITIONS,
  getModuleVisibility,
  resetModuleVisibility,
  setModuleEnabled,
} from '../services/moduleSettings'

const GOOGLE_TRANSLATE_CALLBACK_NAME = 'googleTranslateElementInitFaceCheck'
const GOOGLE_TRANSLATE_SCRIPT_URL = `https://translate.google.com/translate_a/element.js?cb=${GOOGLE_TRANSLATE_CALLBACK_NAME}`
const GOOGLE_TRANSLATE_WRAPPER_ID = 'google_translate_element_hidden'
const LANGUAGE_LOAD_TIMEOUT_MS = 25000
const LANGUAGE_PENDING_STORAGE_KEY = 'facecheck.translate.pending.v1'

const LANGUAGE_OPTIONS = [
  { code: 'vi', label: 'Tiếng Việt' },
  { code: 'en', label: 'English' },
  { code: 'ja', label: '日本語' },
  { code: 'ko', label: '한국어' },
  { code: 'zh-CN', label: '中文 (简体)' },
  { code: 'th', label: 'ภาษาไทย' },
]

function normalizeLanguageCode(languageCode) {
  return String(languageCode || '').trim().toLowerCase()
}

function getLanguageLabel(languageCode) {
  return LANGUAGE_OPTIONS.find(option => option.code === languageCode)?.label || languageCode
}

function readGoogTranslateTargetLanguage() {
  const cookiePart = document.cookie
    .split(';')
    .map(part => part.trim())
    .find(part => part.startsWith('googtrans='))

  if (!cookiePart) {
    return ''
  }

  const rawValue = decodeURIComponent(cookiePart.slice('googtrans='.length))
  const segments = rawValue.split('/').filter(Boolean)
  return normalizeLanguageCode(segments[segments.length - 1] || '')
}

function setGoogTranslateCookie(targetLanguageCode) {
  const normalizedLanguage = normalizeLanguageCode(targetLanguageCode)
  if (!normalizedLanguage) {
    return
  }

  const cookieValue = encodeURIComponent(`/auto/${normalizedLanguage}`)
  const maxAge = 60 * 60 * 24 * 30
  document.cookie = `googtrans=${cookieValue};path=/;max-age=${maxAge}`

  const host = window.location.hostname || ''
  const isIpv4Host = /^\d{1,3}(\.\d{1,3}){3}$/.test(host)
  if (host && host !== 'localhost' && !isIpv4Host) {
    document.cookie = `googtrans=${cookieValue};path=/;domain=.${host};max-age=${maxAge}`
  }
}

function savePendingLanguage(languageCode) {
  if (typeof window === 'undefined') {
    return
  }

  try {
    sessionStorage.setItem(
      LANGUAGE_PENDING_STORAGE_KEY,
      JSON.stringify({
        languageCode,
        timestamp: Date.now(),
      }),
    )
  } catch {
    // Ignore storage failures; fallback still works with cookie + reload.
  }
}

function readPendingLanguage() {
  if (typeof window === 'undefined') {
    return null
  }

  try {
    const raw = sessionStorage.getItem(LANGUAGE_PENDING_STORAGE_KEY)
    if (!raw) {
      return null
    }
    const parsed = JSON.parse(raw)
    if (!parsed || !parsed.languageCode) {
      return null
    }
    return parsed
  } catch {
    return null
  }
}

function clearPendingLanguage() {
  if (typeof window === 'undefined') {
    return
  }

  try {
    sessionStorage.removeItem(LANGUAGE_PENDING_STORAGE_KEY)
  } catch {
    // Ignore storage failures.
  }
}

function getTranslateProbeFingerprint() {
  return Array.from(document.querySelectorAll('[data-translate-probe="1"]'))
    .map(node => String(node?.textContent || '').replace(/\s+/g, ' ').trim())
    .filter(Boolean)
    .join(' | ')
}

function isLanguageEffectivelyApplied(targetLanguageCode, baselineFingerprint) {
  const targetLanguage = normalizeLanguageCode(targetLanguageCode)
  if (!targetLanguage) {
    return false
  }

  const combo = document.querySelector('.goog-te-combo')
  const comboLanguage = normalizeLanguageCode(combo?.value)
  const cookieLanguage = readGoogTranslateTargetLanguage()
  const hasTranslatedClass = document.body.classList.contains('translated-ltr')
    || document.body.classList.contains('translated-rtl')
  const currentFingerprint = getTranslateProbeFingerprint()
  const fingerprintChanged = Boolean(baselineFingerprint)
    && Boolean(currentFingerprint)
    && currentFingerprint !== baselineFingerprint

  if (targetLanguage === 'vi') {
    const languageMatched = comboLanguage === 'vi' || cookieLanguage === 'vi' || cookieLanguage === ''
    if (!languageMatched) {
      return false
    }
    return !hasTranslatedClass || fingerprintChanged
  }

  const languageMatched = comboLanguage === targetLanguage || cookieLanguage === targetLanguage
  if (!languageMatched) {
    return false
  }

  if (hasTranslatedClass || fingerprintChanged) {
    return true
  }

  // Fallback for some browsers where visible markers update late.
  return comboLanguage === targetLanguage && cookieLanguage === targetLanguage
}

function waitForLanguageApply(targetLanguageCode, baselineFingerprint, timeoutMs = LANGUAGE_LOAD_TIMEOUT_MS) {
  return new Promise((resolve, reject) => {
    const startedAt = Date.now()
    const timer = setInterval(() => {
      if (isLanguageEffectivelyApplied(targetLanguageCode, baselineFingerprint)) {
        clearInterval(timer)
        resolve(true)
        return
      }

      if (Date.now() - startedAt >= timeoutMs) {
        clearInterval(timer)
        reject(new Error('language-apply-timeout'))
      }
    }, 320)
  })
}

function ensureGoogleTranslateWidget(onReady) {
  const initialize = () => {
    try {
      if (!window.google || !window.google.translate || !window.google.translate.TranslateElement) {
        return false
      }
      if (!document.getElementById(GOOGLE_TRANSLATE_WRAPPER_ID)) {
        return false
      }
      // Rebuild widget host to avoid duplicate gadgets after hot reload.
      document.getElementById(GOOGLE_TRANSLATE_WRAPPER_ID).innerHTML = ''
      // eslint-disable-next-line new-cap
      new window.google.translate.TranslateElement(
        {
          pageLanguage: 'vi',
          includedLanguages: LANGUAGE_OPTIONS.map(item => item.code).join(','),
          autoDisplay: false,
          layout: window.google.translate.TranslateElement.InlineLayout.SIMPLE,
        },
        GOOGLE_TRANSLATE_WRAPPER_ID,
      )
      if (typeof onReady === 'function') {
        onReady()
      }
      return true
    } catch {
      return false
    }
  }

  if (initialize()) {
    return
  }

  window[GOOGLE_TRANSLATE_CALLBACK_NAME] = () => {
    initialize()
  }

  if (!document.querySelector('script[data-facecheck-google-translate="1"]')) {
    const script = document.createElement('script')
    script.src = GOOGLE_TRANSLATE_SCRIPT_URL
    script.async = true
    script.defer = true
    script.setAttribute('data-facecheck-google-translate', '1')
    document.body.appendChild(script)
  }
}

function applyLanguageToGoogleWidget(languageCode) {
  const combo = document.querySelector('.goog-te-combo')
  if (!combo) {
    return false
  }
  combo.value = languageCode
  combo.dispatchEvent(new Event('change', { bubbles: true }))
  return true
}

export default function SystemSettings() {
  const [moduleVisibility, setModuleVisibility] = useState(() => getModuleVisibility())
  const [widgetReady, setWidgetReady] = useState(false)
  const [languageCode, setLanguageCode] = useState('vi')
  const [languageLoading, setLanguageLoading] = useState(false)
  const [languageProgress, setLanguageProgress] = useState(0)
  const [languageStatusText, setLanguageStatusText] = useState('')

  const enabledCount = useMemo(
    () => MODULE_TOGGLE_DEFINITIONS.filter(moduleDef => moduleVisibility[moduleDef.key] !== false).length,
    [moduleVisibility],
  )

  useEffect(() => {
    function refreshVisibility() {
      setModuleVisibility(getModuleVisibility())
    }

    refreshVisibility()
    window.addEventListener(MODULE_SETTINGS_EVENT, refreshVisibility)
    window.addEventListener('storage', refreshVisibility)
    return () => {
      window.removeEventListener(MODULE_SETTINGS_EVENT, refreshVisibility)
      window.removeEventListener('storage', refreshVisibility)
    }
  }, [])

  useEffect(() => {
    ensureGoogleTranslateWidget(() => setWidgetReady(true))

    const readyTimer = setInterval(() => {
      if (document.querySelector('.goog-te-combo')) {
        setWidgetReady(true)
        clearInterval(readyTimer)
      }
    }, 300)

    return () => clearInterval(readyTimer)
  }, [])

  useEffect(() => {
    const pending = readPendingLanguage()
    if (!pending?.languageCode) {
      return
    }

    const pendingLanguageCode = pending.languageCode
    const pendingLanguageLabel = getLanguageLabel(pendingLanguageCode)
    setLanguageCode(pendingLanguageCode)
    setLanguageProgress(55)
    setLanguageStatusText(`Đang xác nhận chuyển sang ${pendingLanguageLabel}...`)

    const startedAt = Date.now()
    const timer = setInterval(() => {
      const cookieLanguage = readGoogTranslateTargetLanguage()
      const confirmed = isLanguageEffectivelyApplied(pendingLanguageCode, '')
        || cookieLanguage === normalizeLanguageCode(pendingLanguageCode)

      if (confirmed) {
        clearInterval(timer)
        clearPendingLanguage()
        setLanguageProgress(100)
        setLanguageStatusText(`Đã chuyển sang ${pendingLanguageLabel}.`)
        return
      }

      const elapsedMs = Date.now() - startedAt
      if (elapsedMs >= 12000) {
        clearInterval(timer)
        clearPendingLanguage()
        setLanguageProgress(100)
        setLanguageStatusText(`Đã gửi yêu cầu chuyển ngôn ngữ ${pendingLanguageLabel}.`) 
      }
    }, 450)

    return () => clearInterval(timer)
  }, [])

  function handleToggleModule(moduleKey, enabled) {
    const next = setModuleEnabled(moduleKey, enabled)
    setModuleVisibility(next)
  }

  function handleResetModules() {
    const next = resetModuleVisibility()
    setModuleVisibility(next)
  }

  function handleLanguageChange(nextLanguageCode) {
    setLanguageCode(nextLanguageCode)
    setLanguageProgress(0)
    setLanguageStatusText('')
  }

  async function handleLoadLanguage() {
    const baselineFingerprint = getTranslateProbeFingerprint()
    const languageLabel = getLanguageLabel(languageCode)

    if (!widgetReady) {
      setLanguageStatusText('Google Translate chưa sẵn sàng. Vui lòng thử lại sau vài giây.')
      setLanguageProgress(0)
      return
    }

    if (isLanguageEffectivelyApplied(languageCode, baselineFingerprint)) {
      setLanguageProgress(100)
      setLanguageStatusText(`Trang đang hiển thị ở ${languageLabel}.`)
      return
    }

    setLanguageLoading(true)
    setLanguageProgress(6)
    setLanguageStatusText(`Đang tải ngôn ngữ ${languageLabel}...`)

    const progressTimer = setInterval(() => {
      setLanguageProgress(prev => {
        if (prev >= 92) {
          return prev
        }
        return prev < 60 ? prev + 6 : prev + 2
      })
    }, 280)

    let succeeded = false

    try {
      const applied = applyLanguageToGoogleWidget(languageCode)
      if (!applied) {
        throw new Error('widget-not-ready')
      }

      await waitForLanguageApply(languageCode, baselineFingerprint)
      succeeded = true
      setLanguageProgress(100)
      setLanguageStatusText(`Đã chuyển sang ${languageLabel}.`)
    } catch {
      setLanguageStatusText('Đang tải lại trang để áp dụng ngôn ngữ...')
      setLanguageProgress(96)
      savePendingLanguage(languageCode)
      setGoogTranslateCookie(languageCode)
      setTimeout(() => {
        window.location.reload()
      }, 420)
    } finally {
      clearInterval(progressTimer)
      if (succeeded) {
        setLanguageLoading(false)
      }
    }
  }

  return (
    <div className="space-y-4 md:space-y-6">
      <div className="space-y-1">
        <h1 id="system-settings-title" data-translate-probe="1" className="text-2xl font-bold text-slate-800 tracking-tight">Cài đặt hệ thống</h1>
        <p data-translate-probe="1" className="text-sm text-slate-500">
          Quản lý bật/tắt module và cấu hình ngôn ngữ giao diện bằng Google Translate.
        </p>
      </div>

      <div className="grid xl:grid-cols-[1.1fr_0.9fr] gap-4 lg:gap-6">
        <div className="bg-white rounded-2xl shadow-sm border border-slate-200/60 overflow-hidden">
          <div className="px-4 sm:px-5 py-4 border-b border-slate-100 flex items-center justify-between gap-3 flex-wrap">
            <div className="space-y-1">
              <h2 data-translate-probe="1" className="text-base font-semibold text-slate-800">Bật/Tắt module</h2>
              <p className="text-xs text-slate-500">Đang bật {enabledCount}/{MODULE_TOGGLE_DEFINITIONS.length} module</p>
            </div>

            <button
              type="button"
              onClick={handleResetModules}
              className="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200 hover:bg-slate-200 transition-colors"
            >
              <RefreshCw size={14} />
              Bật lại tất cả
            </button>
          </div>

          <div className="divide-y divide-slate-100">
            {MODULE_TOGGLE_DEFINITIONS.map(moduleDef => {
              const enabled = moduleVisibility[moduleDef.key] !== false
              return (
                <div key={moduleDef.key} className="px-4 sm:px-5 py-4 flex items-start justify-between gap-3">
                  <div className="space-y-1">
                    <p className="text-sm font-semibold text-slate-800">{moduleDef.label}</p>
                    <p className="text-xs text-slate-500">{moduleDef.description}</p>
                    <p className="text-[11px] font-mono text-slate-400">Route: {moduleDef.path}</p>
                  </div>

                  <button
                    type="button"
                    onClick={() => handleToggleModule(moduleDef.key, !enabled)}
                    className={`inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium border transition-colors ${
                      enabled
                        ? 'bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-100'
                        : 'bg-slate-100 text-slate-600 border-slate-200 hover:bg-slate-200'
                    }`}
                  >
                    {enabled ? <ToggleRight size={14} /> : <ToggleLeft size={14} />}
                    {enabled ? 'Đang bật' : 'Đang tắt'}
                  </button>
                </div>
              )
            })}
          </div>
        </div>

        <div className="bg-white rounded-2xl shadow-sm border border-slate-200/60 overflow-hidden">
          <div className="px-4 sm:px-5 py-4 border-b border-slate-100 space-y-1">
            <h2 data-translate-probe="1" className="text-base font-semibold text-slate-800">Đa ngôn ngữ</h2>
            <p className="text-xs text-slate-500">Sử dụng Google Translate widget với giao diện tùy chỉnh, toolbar mặc định đã được ẩn.</p>
          </div>

          <div className="p-4 sm:p-5 space-y-4">
            <div data-translate-probe="1" className="rounded-xl border border-sky-200 bg-sky-50 px-4 py-3 text-sm text-sky-800 flex items-start gap-2">
              <Languages size={16} className="mt-0.5" />
              <p>
                Chọn ngôn ngữ để dịch toàn bộ trang hiện tại. Nếu chưa thấy tác dụng ngay, hãy chờ vài giây để plugin tải xong.
              </p>
            </div>

            <label className="block text-sm font-medium text-slate-700" htmlFor="language-select">
              Ngôn ngữ hiển thị
            </label>
            <select
              id="language-select"
              value={languageCode}
              onChange={event => handleLanguageChange(event.target.value)}
              disabled={languageLoading}
              className="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm bg-white focus:ring-2 focus:ring-primary-500/20 focus:border-primary-400"
            >
              {LANGUAGE_OPTIONS.map(option => (
                <option key={option.code} value={option.code}>{option.label}</option>
              ))}
            </select>

            <div className="grid grid-cols-2 sm:grid-cols-3 gap-2">
              {LANGUAGE_OPTIONS.map(option => (
                <button
                  key={option.code}
                  type="button"
                  onClick={() => handleLanguageChange(option.code)}
                  disabled={languageLoading}
                  className={`px-3 py-2 rounded-lg text-xs font-medium border transition-colors ${
                    languageCode === option.code
                      ? 'bg-primary-50 text-primary-700 border-primary-200'
                      : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50'
                  }`}
                >
                  {option.label}
                </button>
              ))}
            </div>

            <button
              type="button"
              onClick={handleLoadLanguage}
              disabled={!widgetReady || languageLoading}
              className="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-sky-600 text-white text-sm font-semibold hover:bg-sky-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
            >
              {languageLoading ? <LoaderCircle size={16} className="animate-spin" /> : <Globe2 size={16} />}
              {languageLoading ? 'Đang tải ngôn ngữ...' : 'Tải ngôn ngữ'}
            </button>

            {(languageLoading || languageProgress > 0 || languageStatusText) && (
              <div className="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 space-y-2">
                <div className="flex items-center justify-between text-xs text-slate-600">
                  <p>{languageStatusText || 'Đang xử lý...'}</p>
                  <span>{Math.round(languageProgress)}%</span>
                </div>
                <div className="h-2 rounded-full bg-slate-200 overflow-hidden">
                  <div
                    className="h-full bg-gradient-to-r from-sky-500 to-emerald-500 transition-[width] duration-200 ease-linear"
                    style={{ width: `${Math.max(0, Math.min(100, languageProgress))}%` }}
                  />
                </div>
              </div>
            )}

            {!widgetReady && (
              <p className="text-xs text-amber-700">
                Widget đang khởi tạo. Nút tải ngôn ngữ sẽ bật khi Google Translate sẵn sàng.
              </p>
            )}

            {widgetReady && !languageLoading && languageProgress === 0 && !languageStatusText && (
              <p className="text-xs text-slate-500">
                Chọn ngôn ngữ, sau đó bấm "Tải ngôn ngữ" để áp dụng và theo dõi tiến trình.
              </p>
            )}

            {widgetReady && languageProgress === 100 && !languageLoading && (
              <button
                type="button"
                onClick={() => {
                  setLanguageProgress(0)
                  setLanguageStatusText('')
                }}
                className="text-xs text-slate-500 hover:text-slate-700 underline"
              >
                Ẩn trạng thái tải ngôn ngữ
              </button>
            )}
          </div>
        </div>
      </div>

      <div id={GOOGLE_TRANSLATE_WRAPPER_ID} className="sr-only" aria-hidden="true" />
    </div>
  )
}
