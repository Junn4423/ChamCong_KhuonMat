/**
 * Employee module — ERP employees, admin employee management, face CRUD.
 */
import { request } from '../request'

export const employeeApi = {
  // --- ERP ---
  getErpEmployees: () => request('/api/erp/employees'),

  importAllFromErp: () => request('/api/erp/import_all', { method: 'POST' }),

  getErpEmployeeInfo: (employeeId) =>
    request(`/api/erp_employee_info?employee_id=${encodeURIComponent(employeeId)}`),

  // --- Local employee list ---
  getEmployees: () => request('/api/employees'),

  // --- Admin employee management ---
  getAdminEmployees: () => request('/api/admin/employees'),

  getAdminEmployeeImage: (employeeId) =>
    request(`/api/admin/employee_image?employee_id=${encodeURIComponent(employeeId)}`),

  updateFace: (formData) => request('/api/admin/update_face', {
    method: 'POST',
    body: formData,
  }),

  updateFaceBase64: (data) => request('/api/admin/update_face', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(data),
  }),

  deleteEmployee: (userId) => request(`/api/admin/delete_employee/${userId}`, {
    method: 'DELETE',
  }),

  clearFace: (userId) => request(`/api/admin/clear_face/${userId}`, {
    method: 'POST',
  }),

  pushToErp: (employeeId) => request('/api/admin/push_to_erp', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ employee_id: employeeId }),
  }),
}
