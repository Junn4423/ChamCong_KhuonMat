/**
 * Registration module — register faces via file, base64, or ERP import.
 */
import { request } from '../request'

export const registrationApi = {
  register: (formData) => request('/api/register', {
    method: 'POST',
    body: formData,
  }),

  registerBase64: (data) => request('/api/register_base64', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(data),
  }),

  registerFromErp: (employeeId) => request('/api/register_from_erp', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ employee_id: employeeId }),
  }),
}
