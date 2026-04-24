import { request } from '../request'

export const mobileConfigApi = {
  getMobileConfigSettings: () => request('/api/mobile_config/settings'),

  saveMobileConfigSettings: (payload = {}) => request('/api/mobile_config/settings', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(payload),
  }),

  createMobileQrSession: (payload = {}) => request('/api/mobile_config/qr_session', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(payload),
  }),

  getMobileDiscoveryOffer: () => request('/api/mobile_config/discovery_offer'),

  mobilePair: (payload = {}) => request('/api/mobile_config/pair', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(payload),
  }),
}
