/**
 * Location module — get/set current location state.
 */
import { request } from '../request'

export const locationApi = {
  getLocationState: () => request('/api/location/current'),

  updateLocationState: (payload) => request('/api/location/current', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(payload),
  }),
}
