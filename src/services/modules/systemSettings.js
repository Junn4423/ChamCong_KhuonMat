/**
 * System settings module — global settings stored in backend SQLite.
 */
import { request } from '../request'

export const systemSettingsApi = {
  getSystemSettings: () => request('/api/system_settings'),

  saveSystemSettings: (settingsPayload) => request('/api/system_settings', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(settingsPayload || {}),
  }),
}
