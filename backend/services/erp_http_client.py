# -*- coding: utf-8 -*-
"""HTTP client for ERP service endpoints."""

from __future__ import annotations

import json
import mimetypes
import uuid
from urllib import parse, request
from urllib.error import HTTPError, URLError
from urllib.parse import urljoin, urlparse

from backend.config import (
    ERP_HTTP_IMAGE_COLUMN,
    ERP_HTTP_LOGIN_URL,
    ERP_HTTP_SERVICE_URL,
    ERP_HTTP_TIMEOUT,
)


class ERPServiceError(Exception):
    """Raised when an ERP HTTP service call fails."""

    def __init__(self, message, status_code=None, payload=None):
        super().__init__(message)
        self.message = message
        self.status_code = status_code
        self.payload = payload


class ERPHttpClient:
    def __init__(self, service_url=None, login_url=None, timeout=None, image_column=None):
        self.service_url = service_url or ERP_HTTP_SERVICE_URL
        self.login_url = login_url or ERP_HTTP_LOGIN_URL
        self.timeout = timeout or ERP_HTTP_TIMEOUT
        self.image_column = image_column or ERP_HTTP_IMAGE_COLUMN
        self.service_dir_url = self.service_url.rsplit('/', 1)[0] + '/'
        self.load_image_url = urljoin(self.service_dir_url, 'loadAnh.php')

    def capabilities(self):
        return {
            'login': True,
            'list_employees': True,
            'get_employee': True,
            'get_employee_image': True,
            'upload_employee_image': True,
            'attendance_http_api': False,
        }

    def login(self, username, password):
        payload = {
            'txtUserName': (username or '').strip(),
            'txtPassword': password or '',
        }
        if not payload['txtUserName'] or not payload['txtPassword']:
            raise ERPServiceError('Vui long nhap tai khoan va mat khau ERP', status_code=400)

        response = self._post_json(self.login_url, payload)
        if not isinstance(response, dict):
            raise ERPServiceError('ERP tra ve du lieu dang nhap khong hop le')

        code = (response.get('code') or '').strip()
        token = (response.get('token') or '').strip()
        if not code or not token:
            raise ERPServiceError(
                response.get('message') or 'Dang nhap ERP that bai',
                status_code=401,
                payload=response,
            )

        return {
            'code': code,
            'token': token,
            'userid': response.get('userid', ''),
            'department': response.get('department', ''),
            'role': response.get('role', ''),
            'name': response.get('name', ''),
        }

    def list_employees(self, auth):
        employees = []
        errors = []

        for func_name in ('LayNhanVien', 'data'):
            try:
                payload = self._build_auth_payload(auth, table='hr_lv0020', func=func_name)
                response = self._post_json(self.service_url, payload)
                if self._is_invalid_session_response(response):
                    raise ERPServiceError(
                        'Phien lam viec ERP da het han, vui long dang nhap lai',
                        status_code=401,
                        payload=response,
                    )
                employees = self._map_employees(response)
                if employees:
                    break
            except ERPServiceError as exc:
                errors.append(exc)

        if not employees and errors and all((exc.status_code == 401 for exc in errors if exc.status_code)):
            raise errors[-1]

        return employees

    def get_employee(self, auth, employee_id):
        employee_id = (employee_id or '').strip()
        if not employee_id:
            raise ERPServiceError('Thieu ma nhan vien', status_code=400)

        # First try dedicated endpoint. Some clone deployments may return 500 here,
        # so we gracefully fallback to list + filter.
        matches = []
        direct_error = None
        try:
            payload = self._build_auth_payload(
                auth,
                table='hr_lv0020',
                func='layNhanVienTheoMa',
                maNhanVien=employee_id,
            )
            response = self._post_json(self.service_url, payload)
            if self._is_invalid_session_response(response):
                raise ERPServiceError('Phien lam viec ERP da het han, vui long dang nhap lai', status_code=401)
            matches = self._map_employees(response)
        except ERPServiceError as exc:
            if exc.status_code == 401:
                raise
            direct_error = exc

        if not matches or not (matches[0].get('image_ref') or '').strip():
            try:
                matches = [
                    emp for emp in self.list_employees(auth)
                    if (emp.get('employee_id') or '').strip().upper() == employee_id.upper()
                ]
            except ERPServiceError as exc:
                if exc.status_code == 401:
                    raise
                if direct_error is None:
                    direct_error = exc

        if not matches and direct_error and direct_error.status_code and direct_error.status_code >= 500:
            raise ERPServiceError(
                f'Loi ERP khi truy van nhan vien {employee_id}: {direct_error.message}',
                status_code=502,
                payload=direct_error.payload,
            )

        if not matches:
            raise ERPServiceError('Khong tim thay nhan vien trong ERP', status_code=404)

        employee = matches[0]
        return {
            'employee_id': (employee.get('employee_id') or employee_id).strip(),
            'name': (employee.get('name') or '').strip(),
            'department': (employee.get('department') or '').strip(),
            'email': (employee.get('email') or '').strip(),
            'phone': (employee.get('phone') or '').strip(),
            'position': (employee.get('position') or '').strip(),
            'image_ref': (employee.get('image_ref') or '').strip(),
        }

    def get_employee_profile_image(self, auth, employee_id, employee=None):
        employee = employee or self.get_employee(auth, employee_id)
        image_ref = (employee.get('image_ref') or '').strip()
        profile_error = None

        for image_column in self._candidate_image_columns():
            try:
                image_data = self.get_employee_image(auth, employee_id, image_column=image_column)
                return {
                    **image_data,
                    'image_ref': image_ref,
                    'source_url': None,
                    'source': 'getAnh',
                }
            except ERPServiceError as exc:
                if exc.status_code and exc.status_code != 404:
                    profile_error = exc

        if self._looks_like_image_ref(image_ref):
            try:
                url = self._build_employee_image_url(image_ref)
                image_bytes, content_type = self._open(
                    url,
                    headers={'Accept': 'image/*,*/*'},
                    method='GET',
                )
                if not image_bytes:
                    raise ERPServiceError('Khong co du lieu anh tu ERP', status_code=404)

                if len(image_bytes) == 7525088:
                    import hashlib
                    if hashlib.md5(image_bytes).hexdigest() == '309afa1bbe342e05345b7cca48a2c656':
                        raise ERPServiceError('ERP tra ve anh chung mac dinh', status_code=404)

                sniff = image_bytes[:256].lower()
                if (
                    b'khong tim thay anh' in sniff
                    or b'image not found' in sniff
                    or b'thieu tham so' in sniff
                    or b'<html' in sniff
                    or b'"message":"invalid"' in sniff
                    or b'"error"' in sniff
                ):
                    raise ERPServiceError('Khong tim thay anh nhan vien trong ERP', status_code=404)

                return {
                    'bytes': image_bytes,
                    'content_type': content_type or 'image/jpeg',
                    'image_ref': image_ref,
                    'source_url': url,
                    'source': 'loadAnh',
                }
            except ERPServiceError as exc:
                profile_error = profile_error or exc

        if profile_error:
            raise profile_error

        raise ERPServiceError('Khong tim thay anh nhan vien trong ERP', status_code=404)

    def get_employee_image(self, auth, employee_id, image_column=None):
        employee_id = (employee_id or '').strip()
        if not employee_id:
            raise ERPServiceError('Thieu ma nhan vien', status_code=400)

        payload = self._build_auth_payload(
            auth,
            table='getAnhTable',
            func='getAnh',
            lv001=employee_id,
            cot=image_column or self.image_column,
        )
        image_bytes, content_type = self._post_form(self.service_url, payload, expect_json=False)
        if not image_bytes:
            raise ERPServiceError('Khong co du lieu anh tu ERP', status_code=404)

        if len(image_bytes) == 7525088:
            import hashlib
            if hashlib.md5(image_bytes).hexdigest() == '309afa1bbe342e05345b7cca48a2c656':
                raise ERPServiceError('ERP tra ve anh chung mac dinh', status_code=404)

        # Some PHP deployments respond 200 with plain text when image is missing.
        sniff = image_bytes[:256].lower()
        if (
            b'image not found' in sniff
            or b'missing lv001' in sniff
            or b'khong tim thay anh' in sniff
            or b'<html' in sniff
            or b'"message":"invalid"' in sniff
            or b'"error"' in sniff
        ):
            raise ERPServiceError('Khong tim thay anh nhan vien trong ERP', status_code=404)

        return {
            'bytes': image_bytes,
            'content_type': content_type or 'image/jpeg',
            'image_column': payload['cot'],
        }

    def _candidate_image_columns(self):
        columns = []
        for candidate in (self.image_column, 'lv008', 'lv007'):
            column = (candidate or '').strip()
            if column and column not in columns:
                columns.append(column)
        return columns

    @staticmethod
    def _looks_like_image_ref(value):
        ref = (value or '').strip().lower()
        if not ref:
            return False
        if '/' in ref or '\\' in ref:
            return True
        image_exts = ('.jpg', '.jpeg', '.png', '.webp', '.bmp', '.gif')
        return ref.endswith(image_exts)

    def upload_employee_image(self, auth, employee_id, image_bytes, filename='face.jpg', image_column=None):
        employee_id = (employee_id or '').strip()
        if not employee_id:
            raise ERPServiceError('Thieu ma nhan vien', status_code=400)
        if not image_bytes:
            raise ERPServiceError('Thieu du lieu anh de day len ERP', status_code=400)

        content_type = mimetypes.guess_type(filename)[0] or 'image/jpeg'
        fields = self._build_auth_payload(
            auth,
            table='cr_lv0382',
            func='uploadAnh',
            lv001=employee_id,
            cot=image_column or self.image_column,
        )
        response = self._post_multipart(
            self.service_url,
            fields,
            file_field='image',
            filename=filename,
            file_bytes=image_bytes,
            file_content_type=content_type,
        )

        if isinstance(response, dict):
            if response.get('success') is False:
                raise ERPServiceError(response.get('message') or 'ERP tu choi luu anh', payload=response)
            if str(response.get('status', '')).lower() == 'error':
                raise ERPServiceError(response.get('message') or 'ERP tu choi luu anh', payload=response)

        return response

    def _build_auth_payload(self, auth, **extra):
        auth = auth or {}
        code = (auth.get('code') or '').strip()
        token = (auth.get('token') or '').strip()
        if not code or not token:
            raise ERPServiceError('Phien lam viec ERP da het han, vui long dang nhap lai', status_code=401)
        return {
            **extra,
            'code': code,
            'token': token,
        }

    @staticmethod
    def _is_invalid_session_response(value):
        if isinstance(value, dict):
            message = str(value.get('message', '')).strip().lower()
            if message in {'invalid', 'unauthorized'}:
                return True
            error_type = str(value.get('errorType', '')).strip().lower()
            if error_type == 'unauthorized':
                return True
        return False

    def _map_employees(self, response):
        employees = []
        for item in self._extract_list(response):
            if not isinstance(item, dict):
                continue
            employee_id = (item.get('employee_id') or item.get('maNhanVien') or item.get('lv001') or '').strip()
            if not employee_id:
                continue
            employees.append({
                'employee_id': employee_id,
                'name': (
                    item.get('name')
                    or item.get('tenNhanVien')
                    or item.get('lv002')
                    or ''
                ).strip(),
                'department': (
                    item.get('department')
                    or item.get('phongBan')
                    or item.get('maPhongBan')
                    or item.get('lv029')
                    or ''
                ).strip(),
                'email': (item.get('email') or item.get('lv041') or '').strip(),
                'phone': (item.get('phone') or item.get('soDienThoai') or item.get('lv039') or '').strip(),
                'position': (item.get('position') or item.get('chucVu') or item.get('lv003') or '').strip(),
                'image_ref': (
                    item.get('image_ref')
                    or item.get('image')
                    or item.get('avatar')
                    or item.get('img')
                    or item.get('Img')
                    or item.get('lv007')
                    or ''
                ).strip(),
            })
        return employees

    def _build_employee_image_url(self, image_ref):
        parsed = urlparse(image_ref)
        if parsed.scheme and parsed.netloc:
            return image_ref

        normalized = image_ref.replace('\\', '/').strip('/')
        filename = normalized.rsplit('/', 1)[-1]
        subdir = ''
        if '/' in normalized:
            subdir = normalized.rsplit('/', 1)[0]

        query = {'filename': filename}
        if subdir:
            query['subdir'] = subdir
        return f"{self.load_image_url}?{parse.urlencode(query)}"

    @staticmethod
    def _extract_list(value):
        if value is None:
            return []
        if isinstance(value, list):
            return value
        if isinstance(value, dict):
            if isinstance(value.get('data'), list):
                return value['data']
            if isinstance(value.get('employees'), list):
                return value['employees']
            if isinstance(value.get('records'), list):
                return value['records']
            return [value]
        return []

    def _post_json(self, url, payload):
        body = json.dumps(payload).encode('utf-8')
        raw, _ = self._open(
            url,
            data=body,
            headers={
                'Content-Type': 'application/json; charset=utf-8',
                'Accept': 'application/json',
            },
        )
        return self._parse_json(raw)

    def _post_form(self, url, payload, expect_json=True):
        body = parse.urlencode(payload).encode('utf-8')
        raw, headers = self._open(
            url,
            data=body,
            headers={'Content-Type': 'application/x-www-form-urlencoded'},
        )
        if expect_json:
            return self._parse_json(raw)
        return raw, headers.get_content_type()

    def _post_multipart(self, url, fields, file_field, filename, file_bytes, file_content_type):
        boundary = f'----FaceCheckBoundary{uuid.uuid4().hex}'
        body = bytearray()

        for name, value in fields.items():
            body.extend(f'--{boundary}\r\n'.encode('utf-8'))
            body.extend(
                f'Content-Disposition: form-data; name="{name}"\r\n\r\n{value}\r\n'.encode('utf-8')
            )

        body.extend(f'--{boundary}\r\n'.encode('utf-8'))
        body.extend(
            (
                f'Content-Disposition: form-data; name="{file_field}"; filename="{filename}"\r\n'
                f'Content-Type: {file_content_type}\r\n\r\n'
            ).encode('utf-8')
        )
        body.extend(file_bytes)
        body.extend(f'\r\n--{boundary}--\r\n'.encode('utf-8'))

        raw, _ = self._open(
            url,
            data=bytes(body),
            headers={'Content-Type': f'multipart/form-data; boundary={boundary}'},
        )
        return self._parse_json(raw)

    def _open(self, url, data=None, headers=None, method=None):
        req = request.Request(
            url,
            data=data,
            headers=headers or {},
            method=method or ('POST' if data is not None else 'GET'),
        )
        try:
            with request.urlopen(req, timeout=self.timeout) as response:
                return response.read(), response.headers
        except HTTPError as exc:
            body = exc.read()
            payload = None
            message = body.decode('utf-8', errors='ignore').strip() or str(exc)
            if 'application/json' in (exc.headers.get('Content-Type') or ''):
                try:
                    payload = self._parse_json(body)
                    if isinstance(payload, dict):
                        message = payload.get('message') or payload.get('error') or message
                except ERPServiceError:
                    payload = None
            raise ERPServiceError(message, status_code=exc.code, payload=payload) from exc
        except URLError as exc:
            raise ERPServiceError(f'Khong the ket noi ERP service: {exc.reason}') from exc

    def _parse_json(self, raw):
        try:
            return json.loads(raw.decode('utf-8-sig'))
        except json.JSONDecodeError as exc:
            raise ERPServiceError('ERP tra ve JSON khong hop le') from exc


erp_http_client = ERPHttpClient()
