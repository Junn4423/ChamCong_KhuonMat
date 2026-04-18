# -*- coding: utf-8 -*-
"""HTTP client for ERP service endpoints."""

from __future__ import annotations

import json
import mimetypes
import base64
import secrets
import string
import uuid
from datetime import datetime
from urllib import parse, request
from urllib.error import HTTPError, URLError
from urllib.parse import urljoin, urlparse

from backend.config import (
    ERP_COUCHDB_DB,
    ERP_COUCHDB_DISPATCHER_DB,
    ERP_COUCHDB_FALLBACK_DISPATCHER_DB,
    ERP_COUCHDB_FALLBACK_HOST,
    ERP_COUCHDB_FALLBACK_PASSWORD,
    ERP_COUCHDB_FALLBACK_PORT,
    ERP_COUCHDB_FALLBACK_USER,
    ERP_COUCHDB_HOST,
    ERP_COUCHDB_LOG_DOC_ID,
    ERP_COUCHDB_PASSWORD,
    ERP_COUCHDB_PORT,
    ERP_COUCHDB_ROUTE_DOC_PREFIX,
    ERP_COUCHDB_ROUTE_PREFIX_LENGTH,
    ERP_COUCHDB_USER,
    ERP_COUCHDB_USER_TABLE,
    ERP_HTTP_DEVICE_TYPE,
    ERP_HTTP_IMAGE_COLUMN,
    ERP_HTTP_LOGIN_URL,
    ERP_HTTP_TOKEN_IMAGE_MODE,
    ERP_HTTP_TOKEN_IMAGE_PROD_BASE_URL,
    ERP_HTTP_TOKEN_IMAGE_TEST_BASE_URL,
    ERP_HTTP_TOKEN_REGISTER_URL,
    ERP_HTTP_TOKEN_REGISTER_USERNAME,
    ERP_HTTP_TOKEN_REGISTER_API_TOKEN,
    ERP_HTTP_TOKEN_COLUMN,
    ERP_HTTP_SOF_DEV_TOKEN,
    ERP_HTTP_SERVICE_URL,
    ERP_HTTP_TIMEOUT,
    ERP_HTTP_TYPE_CODE,
    ERP_MAIN_CONFIG,
)
from backend.services.couchdb_auth_service import CouchDBDynamicAuthService
from backend.services.errors import ERPServiceError


class ERPHttpClient:
    @staticmethod
    def _build_url_candidates(url, default_scheme='http'):
        text = str(url or '').strip()
        if not text:
            return []

        parsed = urlparse(text)
        scheme = (parsed.scheme or '').strip().lower()

        if scheme in {'http', 'https'} and parsed.netloc:
            return [text]

        if text.startswith('//'):
            host_path = text.lstrip('/')
        elif scheme in {'http', 'https'} and not parsed.netloc:
            host_path = (parsed.path or '').lstrip('/')
        elif '://' in text:
            host_path = text.split('://', 1)[1].lstrip('/')
        else:
            host_path = text.lstrip('/')

        if not host_path:
            return []

        primary = default_scheme if default_scheme in {'http', 'https'} else 'http'
        secondary = 'https' if primary == 'http' else 'http'

        candidates = [f'{primary}://{host_path}', f'{secondary}://{host_path}']
        unique = []
        for candidate in candidates:
            if candidate not in unique:
                unique.append(candidate)
        return unique

    @staticmethod
    def _normalize_absolute_url(url, default_scheme='http'):
        candidates = ERPHttpClient._build_url_candidates(url, default_scheme=default_scheme)
        if not candidates:
            return ''
        return candidates[0]

    def __init__(
        self,
        service_url=None,
        login_url=None,
        timeout=None,
        image_column=None,
        sof_dev_token=None,
        type_code=None,
        device_type=None,
        couchdb_host=None,
        couchdb_port=None,
        couchdb_db=None,
        couchdb_dispatcher_db=None,
        couchdb_route_doc_prefix=None,
        couchdb_route_prefix_length=None,
        couchdb_user_table=None,
        couchdb_user=None,
        couchdb_password=None,
        token_image_mode=None,
        token_image_prod_base_url=None,
        token_image_test_base_url=None,
        token_register_url=None,
        token_register_username=None,
        token_register_api_token=None,
        token_column=None,
    ):
        self.service_url = self._normalize_absolute_url(
            service_url or ERP_HTTP_SERVICE_URL,
            default_scheme='http',
        )
        self.login_url = self._normalize_absolute_url(
            login_url or ERP_HTTP_LOGIN_URL,
            default_scheme='http',
        )
        self.timeout = timeout or ERP_HTTP_TIMEOUT
        self.image_column = image_column or ERP_HTTP_IMAGE_COLUMN
        self.sof_dev_token = (sof_dev_token or ERP_HTTP_SOF_DEV_TOKEN or '').strip()
        self.type_code = (type_code or ERP_HTTP_TYPE_CODE or '').strip()
        self.device_type = (device_type or ERP_HTTP_DEVICE_TYPE or 'web').strip()
        self.couchdb_host = (couchdb_host or ERP_COUCHDB_HOST or '').strip()
        self.couchdb_port = int(couchdb_port or ERP_COUCHDB_PORT or 5984)
        self.couchdb_db = (couchdb_db or ERP_COUCHDB_DB or '').strip()
        self.couchdb_dispatcher_db = (
            couchdb_dispatcher_db
            or ERP_COUCHDB_DISPATCHER_DB
            or self.couchdb_db
        ).strip()
        self.couchdb_route_doc_prefix = (
            couchdb_route_doc_prefix
            or ERP_COUCHDB_ROUTE_DOC_PREFIX
            or 'dispatcher:prefix:'
        ).strip()
        # Deprecated: routing no longer depends on fixed prefix length.
        raw_prefix_length = (
            couchdb_route_prefix_length
            if couchdb_route_prefix_length is not None
            else ERP_COUCHDB_ROUTE_PREFIX_LENGTH
        )
        try:
            self.couchdb_route_prefix_length = int(raw_prefix_length)
        except (TypeError, ValueError):
            self.couchdb_route_prefix_length = 0
        self.couchdb_user_table = (couchdb_user_table or ERP_COUCHDB_USER_TABLE or 'lv_lv0066').strip()
        self.couchdb_user = (couchdb_user or ERP_COUCHDB_USER or '').strip()
        self.couchdb_password = (couchdb_password or ERP_COUCHDB_PASSWORD or '').strip()
        self.couchdb_log_doc_id = (ERP_COUCHDB_LOG_DOC_ID or 'logs').strip()
        self.token_image_mode = (token_image_mode or ERP_HTTP_TOKEN_IMAGE_MODE or 'test').strip().lower()
        self.token_image_prod_base_url = (
            token_image_prod_base_url
            or ERP_HTTP_TOKEN_IMAGE_PROD_BASE_URL
            or 'https://sof.com.vn/loadimage'
        ).strip().rstrip('/')
        self.token_image_test_base_url = (
            token_image_test_base_url
            or ERP_HTTP_TOKEN_IMAGE_TEST_BASE_URL
            or 'http://192.168.1.87/token'
        ).strip().rstrip('/')
        self.token_register_url = (
            token_register_url
            or ERP_HTTP_TOKEN_REGISTER_URL
            or 'http://192.168.1.87/createtoken/index.php'
        ).strip()
        self.token_register_url = self._normalize_absolute_url(
            self.token_register_url,
            default_scheme='http',
        )
        self.token_register_username = (
            token_register_username
            or ERP_HTTP_TOKEN_REGISTER_USERNAME
            or 'admin'
        ).strip()
        self.token_register_api_token = (
            token_register_api_token
            or ERP_HTTP_TOKEN_REGISTER_API_TOKEN
            or ''
        ).strip()
        self.token_column = (token_column or ERP_HTTP_TOKEN_COLUMN or 'lv007').strip()
        self.service_dir_url = self.service_url.rsplit('/', 1)[0] + '/'
        self.load_image_url = urljoin(self.service_dir_url, 'loadAnh.php')
        self.couchdb_auth_service = CouchDBDynamicAuthService(
            host=self.couchdb_host,
            port=self.couchdb_port,
            database=self.couchdb_db,
            user_table=self.couchdb_user_table,
            username=self.couchdb_user,
            password=self.couchdb_password,
            timeout=self.timeout,
            log_doc_id=self.couchdb_log_doc_id,
            dispatcher_database=self.couchdb_dispatcher_db,
            route_doc_prefix=self.couchdb_route_doc_prefix,
            route_prefix_length=self.couchdb_route_prefix_length,
            fallback_host=ERP_COUCHDB_FALLBACK_HOST,
            fallback_port=ERP_COUCHDB_FALLBACK_PORT,
            fallback_dispatcher_database=ERP_COUCHDB_FALLBACK_DISPATCHER_DB,
            fallback_username=ERP_COUCHDB_FALLBACK_USER,
            fallback_password=ERP_COUCHDB_FALLBACK_PASSWORD,
        )

    def capabilities(self):
        return {
            'login': True,
            'list_employees': True,
            'get_employee': True,
            'get_employee_image': True,
            'upload_employee_image': True,
            'attendance_http_api': True,
        }

    @staticmethod
    def _normalize_attendance_datetime(value):
        if value is None:
            return datetime.now()
        if isinstance(value, datetime):
            return value
        if isinstance(value, str):
            text = value.strip()
            if not text:
                return datetime.now()
            for date_format in (
                '%Y-%m-%d %H:%M:%S',
                '%Y-%m-%d %H:%M',
                '%Y-%m-%dT%H:%M:%S',
                '%Y-%m-%dT%H:%M',
            ):
                try:
                    return datetime.strptime(text, date_format)
                except ValueError:
                    continue
        raise ERPServiceError('Thoi gian cham cong khong hop le', status_code=400)

    def login(self, username, password, client_ip='', client_mac=''):
        username = (username or '').strip()
        password = password or ''
        if not username or not password:
            raise ERPServiceError('Vui long nhap tai khoan va mat khau ERP', status_code=400)

        gateway_response = None
        gateway_error = None
        gateway_headers = self._build_gateway_headers()
        for request_type, attempt_payload in self._build_gateway_login_attempts(username, password):
            try:
                if request_type == 'form':
                    candidate = self._post_form(
                        self.login_url,
                        attempt_payload,
                        headers=gateway_headers,
                    )
                else:
                    candidate = self._post_json(
                        self.login_url,
                        attempt_payload,
                        headers=gateway_headers,
                    )
            except ERPServiceError as exc:
                gateway_error = exc
                continue

            gateway_response = candidate
            normalized = self._normalize_gateway_login_response(candidate, requested_username=username)
            if normalized:
                return self._merge_login_payload({}, normalized, token_source='gateway')

        # Fallback to direct CouchDB auth when gateway rejects or cannot parse token payload.
        couch_auth = None
        couch_auth_error = None
        try:
            couch_auth = self.couchdb_auth_service.authenticate_user(
                username,
                password,
                device_type=self.device_type,
                type_code=self.type_code,
            )
        except ERPServiceError as exc:
            couch_auth_error = exc

        if couch_auth:
            merged = self._merge_login_payload(couch_auth, gateway_response or {}, token_source='couchdb_direct')
            self._sync_couchdb_post_login(
                username=username,
                auth_payload=merged,
                client_ip=client_ip,
                client_mac=client_mac,
                login_password=password,
            )
            if gateway_error:
                merged['gateway_warning'] = gateway_error.message
            return merged

        if isinstance(gateway_response, dict):
            message = (
                gateway_response.get('message')
                or gateway_response.get('error')
                or gateway_response.get('reason')
                or ''
            )
            status_text = str(gateway_response.get('status') or '').strip()
            if not message and status_text:
                message = f'Dang nhap gateway that bai (status={status_text})'
            if message:
                raise ERPServiceError(str(message), status_code=401, payload=gateway_response)

        if gateway_error:
            raise gateway_error
        if couch_auth_error:
            raise couch_auth_error
        raise ERPServiceError('Dang nhap that bai', status_code=401)

    def _build_gateway_login_attempts(self, username, password):
        modern_payload = {
            'method': 'loginUser',
            'username': username,
            'password': password,
        }
        if self.device_type:
            modern_payload['deviceType'] = self.device_type
        if self.type_code:
            modern_payload['TYPE-SOF-CODE'] = self.type_code

        modern_base_payload = {
            'method': 'loginUser',
            'username': username,
            'password': password,
        }

        legacy_payload = {
            'txtUserName': username,
            'txtPassword': password,
        }
        if self.device_type:
            legacy_payload['txtDeviceType'] = self.device_type
        if self.type_code:
            legacy_payload['txtTypeCode'] = self.type_code

        legacy_base_payload = {
            'txtUserName': username,
            'txtPassword': password,
        }

        attempts = []
        seen = set()
        for request_type in ('json', 'form'):
            for payload in (
                modern_payload,
                modern_base_payload,
                legacy_payload,
                legacy_base_payload,
            ):
                signature = (request_type, json.dumps(payload, sort_keys=True, ensure_ascii=False))
                if signature in seen:
                    continue
                seen.add(signature)
                attempts.append((request_type, payload))
        return attempts

    def _normalize_gateway_login_response(self, response, requested_username=''):
        if not isinstance(response, dict):
            return {}

        status_raw = str(response.get('status') or response.get('Status') or '').strip()
        status_lower = status_raw.lower()
        try:
            status_number = int(status_raw) if status_raw else None
        except (TypeError, ValueError):
            status_number = None

        if response.get('success') is False:
            return {}
        if status_lower in {'error', 'failed', 'fail'}:
            return {}
        if status_number is not None and status_number >= 3000:
            return {}

        token = str(response.get('token') or '').strip()
        if not token:
            return {}

        code = (
            str(response.get('code') or '').strip()
            or str(response.get('username') or '').strip()
            or str(response.get('user') or '').strip()
            or (requested_username or '').strip()
        )
        if not code:
            return {}

        normalized = dict(response)
        normalized['code'] = code
        normalized['token'] = token

        if not str(normalized.get('database') or '').strip():
            normalized['database'] = str(
                response.get('database')
                or response.get('dbName')
                or response.get('table')
                or ''
            ).strip()

        if not str(normalized.get('role') or '').strip():
            normalized['role'] = str(response.get('role') or response.get('right') or '').strip()

        if not str(normalized.get('userid') or '').strip():
            normalized['userid'] = str(response.get('userid') or response.get('userCode') or '').strip()

        if not str(normalized.get('name') or '').strip():
            normalized['name'] = str(response.get('name') or response.get('username') or code).strip()

        if not str(normalized.get('method') or '').strip():
            normalized['method'] = 'http'

        return normalized

    @staticmethod
    def _generate_local_token(length=32):
        alphabet = string.ascii_letters + string.digits
        return ''.join(secrets.choice(alphabet) for _ in range(max(8, int(length))))

    def _sync_couchdb_post_login(self, username, auth_payload, client_ip='', client_mac='', login_password=''):
        if not isinstance(auth_payload, dict):
            return

        token = str(auth_payload.get('token') or '').strip()
        if not token:
            return

        sync_warnings = []
        try:
            self.couchdb_auth_service.save_token(
                username=username,
                token=token,
                device_type=self.device_type,
                login_password=login_password,
            )
        except ERPServiceError as exc:
            sync_warnings.append(f'save_token: {exc.message}')

        try:
            self.couchdb_auth_service.write_auth_log(
                username=username,
                status=0,
                device_type=self.device_type,
                token=token,
                ip=client_ip,
                mac=client_mac,
            )
        except ERPServiceError as exc:
            sync_warnings.append(f'write_auth_log: {exc.message}')

        if sync_warnings:
            auth_payload['couchdb_sync_warning'] = '; '.join(sync_warnings)

    def logout(self, auth, client_ip='', client_mac=''):
        auth = auth or {}
        username = str(auth.get('code') or '').strip()
        if not username:
            return

        device_type = str(auth.get('device_type') or self.device_type or 'web').strip().lower()
        token = str(auth.get('token') or '').strip()

        try:
            self.couchdb_auth_service.remove_token(
                username=username,
                device_type=device_type,
            )
        except ERPServiceError:
            pass

        try:
            self.couchdb_auth_service.write_auth_log(
                username=username,
                status=1,
                device_type=device_type,
                token=token,
                ip=client_ip,
                mac=client_mac,
            )
        except ERPServiceError:
            pass

    def _merge_login_payload(self, couch_auth, response, token_source):
        merged = dict(couch_auth or {})
        response = response if isinstance(response, dict) else {}

        for key in (
            'code',
            'token',
            'userid',
            'department',
            'role',
            'name',
            'domain',
            'method',
            'database',
            'IPv4',
            'lv006',
            'lv900',
            'lv705',
            'lv667',
            'lv040',
            'quota_exceeded',
            'quota_message',
            'storage_info',
            'route_prefix',
            'routed_database',
            'dispatch_database',
            'system_name',
            'system_note',
            'welcome_message',
        ):
            value = response.get(key)
            if value not in (None, ''):
                merged[key] = value

        merged['code'] = (merged.get('code') or '').strip()
        merged['token'] = (merged.get('token') or '').strip() or self._generate_local_token(32)
        merged['device_type'] = self.device_type
        merged['type_code'] = self.type_code
        merged['token_source'] = token_source
        merged['quota_exceeded'] = bool(merged.get('quota_exceeded', False))
        if not isinstance(merged.get('storage_info'), dict):
            merged['storage_info'] = {}
        return merged

    def list_employees(self, auth):
        employees = []
        errors = []
        had_explicit_success = False
        service_headers = self._build_service_headers(auth)

        # Prefer `data` because some deployments return reduced columns on `LayNhanVien`
        # (missing lv007/image token), while `data` keeps full employee fields.
        for func_name in ('data', 'LayNhanVien'):
            try:
                payload = self._build_auth_payload(auth, table='hr_lv0020', func=func_name)
                response = self._post_json(self._resolve_service_url(auth), payload, headers=service_headers)
                if self._is_invalid_session_response(response):
                    raise ERPServiceError(
                        'Phien lam viec ERP da het han, vui long dang nhap lai',
                        status_code=401,
                        payload=response,
                    )
                service_error = self._extract_service_error(response)
                if service_error:
                    raise ERPServiceError(service_error, status_code=502, payload=response)
                if self._is_explicit_success_response(response):
                    had_explicit_success = True
                employees = self._map_employees(response)
                if employees:
                    break
            except ERPServiceError as exc:
                errors.append(exc)

        if not employees and errors and not had_explicit_success:
            auth_errors = [exc for exc in errors if exc.status_code in (401, 403)]
            if auth_errors:
                raise auth_errors[-1]
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
            response = self._post_json(
                self._resolve_service_url(auth),
                payload,
                headers=self._build_service_headers(auth),
            )
            if self._is_invalid_session_response(response):
                raise ERPServiceError('Phien lam viec ERP da het han, vui long dang nhap lai', status_code=401)
            service_error = self._extract_service_error(response)
            if service_error:
                raise ERPServiceError(service_error, status_code=502, payload=response)
            matches = self._map_employees(response)
        except ERPServiceError as exc:
            if exc.status_code == 401:
                raise
            direct_error = exc

        if (
            not matches
            or not (
                (matches[0].get('image_ref') or '').strip()
                or (matches[0].get('image_token') or '').strip()
                or (matches[0].get('image_url') or '').strip()
            )
        ):
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
            'image_token': (employee.get('image_token') or '').strip(),
            'image_url': (employee.get('image_url') or '').strip(),
        }

    def get_employee_profile_image(self, auth, employee_id, employee=None):
        employee = employee or self.get_employee(auth, employee_id)
        image_ref = (employee.get('image_ref') or '').strip()
        image_token = self._extract_image_token(employee.get('image_token') or image_ref)
        image_url = (employee.get('image_url') or '').strip()
        if not image_url and image_token:
            image_url = self._build_token_image_url(image_token)
        profile_error = None

        if image_url:
            try:
                image_bytes, content_type = self._open(
                    image_url,
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
                    'image_token': image_token,
                    'source_url': image_url,
                    'source': 'token_url',
                }
            except ERPServiceError as exc:
                profile_error = profile_error or exc

        for image_column in self._candidate_image_columns():
            try:
                image_data = self.get_employee_image(auth, employee_id, image_column=image_column)
                return {
                    **image_data,
                    'image_ref': image_ref,
                    'image_token': image_token,
                    'source_url': None,
                    'source': 'getAnh',
                }
            except ERPServiceError as exc:
                if exc.status_code and exc.status_code != 404:
                    profile_error = exc

        if self._looks_like_image_ref(image_ref):
            try:
                url = self._build_employee_image_url(image_ref, auth=auth)
                image_bytes, content_type = self._open(
                    url,
                    headers={
                        **self._build_service_headers(auth),
                        'Accept': 'image/*,*/*',
                    },
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
                    'image_token': image_token,
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
        image_bytes, content_type = self._post_form(
            self._resolve_service_url(auth),
            payload,
            expect_json=False,
            headers=self._build_service_headers(auth),
        )
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
            self._resolve_service_url(auth),
            fields,
            file_field='image',
            filename=filename,
            file_bytes=image_bytes,
            file_content_type=content_type,
            headers=self._build_service_headers(auth),
        )

        if isinstance(response, dict):
            if response.get('success') is False:
                raise ERPServiceError(response.get('message') or 'ERP tu choi luu anh', payload=response)
            if str(response.get('status', '')).lower() == 'error':
                raise ERPServiceError(response.get('message') or 'ERP tu choi luu anh', payload=response)

        return response

    def push_attendance_record(
        self,
        employee_id,
        attendance_time=None,
        attendance_code='IN',
        source='',
        camera_ip='',
        auth=None,
    ):
        employee_id = (employee_id or '').strip()
        if not employee_id:
            raise ERPServiceError('Thieu ma nhan vien', status_code=400)

        attendance_dt = self._normalize_attendance_datetime(attendance_time)
        attendance_type = str(attendance_code or 'IN').strip().upper()
        if attendance_type not in {'IN', 'OUT'}:
            attendance_type = 'IN'

        payload = {
            'table': 'chamcong_ngocchung',
            'func': 'pushAttendance',
            'employee_id': employee_id,
            'attendance_date': attendance_dt.strftime('%Y-%m-%d'),
            'attendance_time': attendance_dt.strftime('%H:%M:%S'),
            'attendance_type': attendance_type,
            'source': (source or '').strip() or 'Camera',
            'camera_ip': (camera_ip or '').strip(),
        }

        response = self._post_json(
            self._resolve_service_url(auth or {}),
            payload,
            headers=self._build_service_headers(auth or {}),
        )

        if self._is_invalid_session_response(response):
            raise ERPServiceError('Phien lam viec ERP da het han, vui long dang nhap lai', status_code=401, payload=response)
        if not isinstance(response, dict):
            raise ERPServiceError('ERP service pushAttendance tra ve du lieu khong hop le', status_code=502)

        ok = bool(response.get('success'))
        status_text = str(response.get('status') or '').strip().lower()
        if (not ok) or status_text in {'error', 'failed', 'fail'}:
            raise ERPServiceError(
                str(response.get('message') or response.get('error') or 'ERP service tu choi ghi cham cong').strip(),
                status_code=502,
                payload=response,
            )

        verify_payload = response.get('verify') if isinstance(response.get('verify'), dict) else {}
        verify_inserted = verify_payload.get('inserted')
        verify_matched_rows = int(verify_payload.get('matched_rows') or 0)
        if verify_inserted is False:
            raise ERPServiceError('ERP service bao ghi cham cong khong thanh cong', status_code=502, payload=response)
        if verify_payload and verify_matched_rows <= 0:
            raise ERPServiceError('ERP service khong xac thuc du lieu sau ghi', status_code=502, payload=response)

        return response

    def check_recent_attendance(self, employee_id, minutes=10, auth=None):
        employee_id = (employee_id or '').strip()
        if not employee_id:
            raise ERPServiceError('Thieu ma nhan vien', status_code=400)

        try:
            normalized_minutes = int(minutes)
        except (TypeError, ValueError):
            normalized_minutes = 10
        if normalized_minutes < 1:
            normalized_minutes = 1
        if normalized_minutes > 1440:
            normalized_minutes = 1440

        payload = {
            'table': 'chamcong_ngocchung',
            'func': 'checkRecentAttendance',
            'employee_id': employee_id,
            'minutes': normalized_minutes,
        }

        response = self._post_json(
            self._resolve_service_url(auth or {}),
            payload,
            headers=self._build_service_headers(auth or {}),
        )

        if self._is_invalid_session_response(response):
            raise ERPServiceError('Phien lam viec ERP da het han, vui long dang nhap lai', status_code=401, payload=response)
        if not isinstance(response, dict):
            raise ERPServiceError('ERP service checkRecentAttendance tra ve du lieu khong hop le', status_code=502)

        ok = bool(response.get('success'))
        status_text = str(response.get('status') or '').strip().lower()
        if (not ok) or status_text in {'error', 'failed', 'fail'}:
            raise ERPServiceError(
                str(response.get('message') or response.get('error') or 'Khong the kiem tra cham cong gan day').strip(),
                status_code=502,
                payload=response,
            )

        count = int(response.get('count') or 0)
        exists = response.get('exists')
        if isinstance(exists, bool):
            return exists
        return count > 0

    def get_online_attendance(self, filters=None, auth=None):
        filters = filters if isinstance(filters, dict) else {}

        today = datetime.now().strftime('%Y-%m-%d')
        start_date = str(filters.get('start_date') or '').strip() or today
        end_date = str(filters.get('end_date') or '').strip() or today
        employee_id = str(filters.get('employee_id') or '').strip()
        keyword = str(filters.get('keyword') or '').strip()

        attendance_type = str(filters.get('attendance_type') or '').strip().upper()
        if attendance_type not in {'IN', 'OUT'}:
            attendance_type = 'all'

        sort_by_raw = str(filters.get('sort_by') or '').strip().lower()
        sort_by_map = {
            'date': 'date',
            'attendance_date': 'date',
            'time': 'time',
            'attendance_time': 'time',
            'employee_id': 'employee_id',
            'attendance_type': 'attendance_type',
            'status': 'attendance_type',
            'source': 'source',
            'camera_ip': 'camera_ip',
        }
        sort_by = sort_by_map.get(sort_by_raw, 'date')

        sort_dir = str(filters.get('sort_dir') or '').strip().lower()
        sort_dir = 'asc' if sort_dir == 'asc' else 'desc'

        try:
            page = int(filters.get('page') or 1)
        except (TypeError, ValueError):
            page = 1
        try:
            page_size = int(filters.get('page_size') or 50)
        except (TypeError, ValueError):
            page_size = 50
        page = max(1, page)
        page_size = min(500, max(1, page_size))

        payload = {
            'table': 'chamcong_ngocchung',
            'func': 'getOnlineAttendance',
            'start_date': start_date,
            'end_date': end_date,
            'attendance_type': attendance_type,
            'sort_by': sort_by,
            'sort_dir': sort_dir,
            'page': page,
            'page_size': page_size,
        }
        if employee_id:
            payload['employee_id'] = employee_id
        if keyword:
            payload['keyword'] = keyword

        response = self._post_json(
            self._resolve_service_url(auth or {}),
            payload,
            headers=self._build_service_headers(auth or {}),
        )

        if self._is_invalid_session_response(response):
            raise ERPServiceError('Phien lam viec ERP da het han, vui long dang nhap lai', status_code=401, payload=response)
        if not isinstance(response, dict):
            raise ERPServiceError('ERP service getOnlineAttendance tra ve du lieu khong hop le', status_code=502)

        ok = bool(response.get('success'))
        status_text = str(response.get('status') or '').strip().lower()
        if (not ok) or status_text in {'error', 'failed', 'fail'}:
            raise ERPServiceError(
                str(response.get('message') or response.get('error') or 'Khong the tai du lieu cham cong online').strip(),
                status_code=502,
                payload=response,
            )

        records = response.get('records')
        if not isinstance(records, list):
            records = response.get('data') if isinstance(response.get('data'), list) else []

        meta = response.get('meta') if isinstance(response.get('meta'), dict) else {}
        if not meta:
            total = len(records)
            total_pages = int((total + page_size - 1) / page_size) if page_size else 1
            meta = {
                'page': page,
                'page_size': page_size,
                'total': total,
                'total_pages': max(1, total_pages),
            }

        return {
            'records': records,
            'meta': meta,
            'filters': response.get('filters') if isinstance(response.get('filters'), dict) else {},
            'raw': response,
        }

    def create_sof_image_token(self, image_bytes, username=None, auth=None):
        if not image_bytes:
            raise ERPServiceError('Thiếu dữ liệu ảnh để đăng ký token', status_code=400)
        if not self.token_register_url:
            raise ERPServiceError('Chưa cấu hình URL đăng ký TokenSOF', status_code=500)

        token_username = (
            username
            or self._pick_auth_value(auth or {}, 'code', 'username', 'user')
            or self.token_register_username
            or 'admin'
        ).strip() or 'admin'
        encoded_image = base64.b64encode(image_bytes).decode('utf-8')
        payload_attempts = (
            ('json', {'username': token_username, 'ImgSOF': encoded_image}),
            ('json', {'username': token_username, '_ImgSOF': encoded_image}),
            ('form', {'username': token_username, '_ImgSOF': encoded_image}),
        )
        header_attempts = self._build_token_register_header_variants(token_username, auth=auth)

        last_error = None
        preferred_error = None
        for request_type, payload in payload_attempts:
            for headers in header_attempts:
                try:
                    if request_type == 'json':
                        response = self._post_json(
                            self.token_register_url,
                            payload,
                            headers=headers,
                        )
                    else:
                        response = self._post_form(
                            self.token_register_url,
                            payload,
                            headers=headers,
                        )
                except ERPServiceError as exc:
                    last_error = exc
                    preferred_error = self._pick_preferred_token_register_error(preferred_error, exc)
                    continue

                token_result = self._extract_token_register_result(response)
                if token_result:
                    return token_result

                last_error = self._token_register_error_from_response(response)
                preferred_error = self._pick_preferred_token_register_error(preferred_error, last_error)

        if preferred_error:
            raise preferred_error
        if last_error:
            raise last_error
        raise ERPServiceError('Dang ky TokenSOF that bai', status_code=502)

    def _extract_token_register_result(self, response):
        if not isinstance(response, dict):
            return None

        token = str(
            response.get('TokenSOF')
            or response.get('token')
            or response.get('tokenSOF')
            or ''
        ).strip()
        if not token:
            return None

        status_text = str(response.get('Status') or response.get('status') or '').strip().lower()
        is_success = bool(response.get('success'))
        if response.get('success') is False and status_text not in {'success', 'ok'}:
            return None
        if status_text and status_text not in {'success', 'ok'} and not is_success:
            return None

        return {
            'token': token,
            'message': str(response.get('Message') or response.get('message') or 'OK').strip() or 'OK',
            'raw': response,
        }

    @staticmethod
    def _token_register_error_from_response(response):
        if not isinstance(response, dict):
            return ERPServiceError('Dịch vụ TokenSOF trả về dữ liệu không hợp lệ', status_code=502)

        message = str(
            response.get('Message')
            or response.get('message')
            or response.get('error')
            or 'Đăng ký TokenSOF thất bại'
        ).strip() or 'Đăng ký TokenSOF thất bại'

        lowered = message.lower()
        status_code = 401 if ('unauthorized' in lowered or 'forbidden' in lowered) else 502
        return ERPServiceError(message, status_code=status_code, payload=response)

    @staticmethod
    def _unique_values(*values):
        unique = []
        for value in values:
            text = str(value or '').strip()
            if text and text not in unique:
                unique.append(text)
        return unique

    def _build_token_register_header_variants(self, username, auth=None):
        auth = auth or {}
        auth_user_token = self._pick_auth_value(auth, 'token', 'api_token', 'user_token')

        configured_user_token = (self.sof_dev_token or self.token_register_api_token or '').strip()
        configured_admin_token = (self.token_register_api_token or '').strip()

        variant_inputs = [
            (auth_user_token, configured_admin_token),
            (auth_user_token, ''),
            (configured_user_token, configured_admin_token),
            (configured_user_token, ''),
            ((self.token_register_api_token or '').strip(), ''),
            ('SOF2025DEVELOPER', 'SOF2025ADMIN'),
            ('SOF2025DEVELOPER', ''),
        ]

        variants = []
        seen = set()
        for user_token, admin_token in variant_inputs:
            user_token = (user_token or '').strip()
            if not user_token:
                continue
            headers = self._build_token_register_headers(
                username=username,
                user_token=user_token,
                admin_token=admin_token,
            )
            key = tuple(sorted(headers.items()))
            if key in seen:
                continue
            seen.add(key)
            variants.append(headers)

        return variants

    @staticmethod
    def _pick_preferred_token_register_error(current_error, candidate_error):
        if candidate_error is None:
            return current_error
        if current_error is None:
            return candidate_error

        def score(err):
            message = str(getattr(err, 'message', '') or '').strip().lower()
            status_code = int(getattr(err, 'status_code', 0) or 0)
            value = 0
            if status_code == 401 or 'unauthorized' in message or 'forbidden' in message:
                value += 100
            if 'missing header' in message:
                value -= 20
            return value

        return candidate_error if score(candidate_error) >= score(current_error) else current_error

    def update_employee_image_token(self, employee_id, image_token, auth=None):
        employee_id = (employee_id or '').strip()
        image_token = (image_token or '').strip()
        if not employee_id:
            raise ERPServiceError('Thiếu mã nhân viên', status_code=400)
        if not image_token:
            raise ERPServiceError('Thiếu token ảnh để đẩy lên ERP', status_code=400)

        conn = None
        cursor = None
        last_direct_error = None
        try:
            import mysql.connector

            conn = mysql.connector.connect(**ERP_MAIN_CONFIG)
            cursor = conn.cursor()
            cursor.execute(
                f'UPDATE hr_lv0020 SET {self.token_column} = %s WHERE lv001 = %s',
                (image_token, employee_id),
            )
            conn.commit()
            affected_rows = int(cursor.rowcount or 0)
            unchanged = False
            if affected_rows <= 0:
                cursor.execute('SELECT lv001 FROM hr_lv0020 WHERE lv001 = %s LIMIT 1', (employee_id,))
                row = cursor.fetchone()
                if not row:
                    raise ERPServiceError(
                        f'Không tìm thấy nhân viên trong ERP để cập nhật {self.token_column}',
                        status_code=404,
                    )
                unchanged = True

            return {
                'success': True,
                'employee_id': employee_id,
                'image_token': image_token,
                'affected_rows': affected_rows,
                'unchanged': unchanged,
                'table': 'hr_lv0020',
                'column': self.token_column,
                'database': str(ERP_MAIN_CONFIG.get('database') or ''),
                'host': str(ERP_MAIN_CONFIG.get('host') or ''),
                'method': 'direct_db',
            }
        except ERPServiceError:
            raise
        except Exception as exc:
            last_direct_error = exc
        finally:
            if cursor is not None:
                try:
                    cursor.close()
                except Exception:
                    pass
            if conn is not None:
                try:
                    conn.close()
                except Exception:
                    pass

        fallback_error = None
        if auth:
            try:
                return self._update_employee_image_token_via_service(auth, employee_id, image_token)
            except ERPServiceError as exc:
                fallback_error = exc

        if fallback_error is not None:
            direct_message = str(last_direct_error or '').strip()
            fallback_message = str(fallback_error.message or '').strip()
            message = 'Không thể cập nhật token ảnh lên ERP'
            if direct_message:
                message = f'{message} (direct_db: {direct_message}'
                if fallback_message:
                    message = f'{message}; service: {fallback_message})'
                else:
                    message = f'{message})'
            elif fallback_message:
                message = f'{message} (service: {fallback_message})'
            raise ERPServiceError(message, status_code=(fallback_error.status_code or 502), payload=fallback_error.payload)

        raise ERPServiceError(f'Không thể cập nhật token ảnh lên ERP: {last_direct_error}', status_code=502) from last_direct_error

    def _update_employee_image_token_via_service(self, auth, employee_id, image_token):
        payload = self._build_auth_payload(
            auth,
            table='hr_lv0020',
            func='updateImageToken',
            lv001=employee_id,
            tokenAnh=image_token,
            cot=self.token_column,
        )

        response = self._post_json(
            self._resolve_service_url(auth),
            payload,
            headers=self._build_service_headers(auth),
        )

        if self._is_invalid_session_response(response):
            raise ERPServiceError('Phiên làm việc ERP đã hết hạn, vui lòng đăng nhập lại', status_code=401, payload=response)
        if not isinstance(response, dict):
            raise ERPServiceError('ERP service updateImageToken trả về dữ liệu không hợp lệ', status_code=502)

        ok = bool(response.get('success'))
        status_text = str(response.get('status') or '').strip().lower()
        if (not ok) or status_text in {'error', 'failed', 'fail'}:
            raise ERPServiceError(
                str(response.get('message') or response.get('error') or 'ERP service tu choi cap nhat token anh').strip(),
                status_code=502,
                payload=response,
            )

        affected_rows = int(response.get('affected_rows') or 0)
        unchanged = bool(response.get('unchanged'))
        return {
            'success': True,
            'employee_id': employee_id,
            'image_token': image_token,
            'affected_rows': affected_rows,
            'unchanged': unchanged,
            'table': 'hr_lv0020',
            'column': self.token_column,
            'database': str(response.get('database') or ''),
            'host': str(response.get('host') or ''),
            'method': 'service',
            'raw': response,
        }

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

    def _build_gateway_headers(self):
        headers = {
            'Accept': 'application/json',
            'X-DEVICE-TYPE': self.device_type,
        }
        if self.sof_dev_token:
            headers['X-SOF-USER-TOKEN'] = self.sof_dev_token
            headers['SOF-User-Token'] = self.sof_dev_token
        return headers

    def _build_token_register_headers(self, username='', user_token='', admin_token=''):
        headers = {
            'Accept': 'application/json',
            'X-DEVICE-TYPE': self.device_type,
        }

        user_token = (user_token or '').strip()
        if user_token:
            headers['X-SOF-USER-TOKEN'] = user_token
            headers['SOF-User-Token'] = user_token
            headers['SOF-USER-TOKEN'] = user_token
            headers['Authorization'] = f'Bearer {user_token}'

        admin_token = (admin_token or '').strip()
        if admin_token:
            headers['SOF-Token'] = admin_token
            headers['SOF-TOKEN'] = admin_token

        username = (username or '').strip()
        if username:
            headers['SOF-User'] = username
            headers['Admin-Contact'] = username
            headers['X-Admin-Contact'] = username

        return headers

    @staticmethod
    def _pick_auth_value(auth, *keys):
        for key in keys:
            value = auth.get(key)
            if value is None:
                continue
            text = str(value).strip()
            if text:
                return text
        return ''

    def _resolve_service_url(self, auth):
        auth = auth or {}
        domain = self._pick_auth_value(auth, 'domain')
        if not domain:
            return self.service_url

        method = (self._pick_auth_value(auth, 'method') or 'http').lower()
        if method not in {'http', 'https'}:
            method = 'http'

        candidate = self._normalize_absolute_url(domain.strip(), default_scheme=method)
        if not candidate:
            return self.service_url

        candidate = candidate.rstrip('/')
        lower_candidate = candidate.lower()
        if lower_candidate.endswith('/services.sof.vn/index.php'):
            return candidate
        if lower_candidate.endswith('/services.sof.vn'):
            return f'{candidate}/index.php'
        if lower_candidate.endswith('/index.php'):
            return candidate

        return f'{candidate}/services.sof.vn/index.php'

    def _build_service_headers(self, auth):
        auth = auth or {}
        headers = self._build_gateway_headers()

        user_token = self._pick_auth_value(auth, 'token')
        if user_token:
            headers['X-USER-TOKEN'] = user_token

        database = self._pick_auth_value(auth, 'database')
        if database:
            headers['X-DATABASE'] = database

        server_ip = self._pick_auth_value(auth, 'IPv4', 'server_ip')
        if server_ip:
            headers['X-SERVER-IP'] = server_ip

        user_code = self._pick_auth_value(auth, 'code')
        if user_code:
            headers['X-USER-CODE'] = user_code
            headers['X-USER-USERNAME'] = user_code

        user_role = self._pick_auth_value(auth, 'role')
        if user_role:
            headers['X-USER-RIGHT'] = user_role

        return headers

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

    @staticmethod
    def _extract_service_error(value):
        if isinstance(value, list):
            # Legacy PHP formats:
            #   [success, message, data]
            #   [success, data]
            if value and isinstance(value[0], bool):
                success = value[0]
                if not success:
                    message = ''
                    for item in value[1:]:
                        if isinstance(item, str) and item.strip():
                            message = item.strip()
                            break
                        if isinstance(item, dict):
                            message = str(item.get('message') or item.get('error') or item.get('reason') or '').strip()
                            if message:
                                break
                    return message or 'ERP service tra ve loi'
            return ''

        if not isinstance(value, dict):
            return ''

        if value.get('success') is False:
            return str(value.get('message') or value.get('error') or value.get('reason') or 'ERP service tra ve loi').strip()

        status_text = str(value.get('status') or '').strip().lower()
        if status_text in {'error', 'failed', 'fail'}:
            return str(value.get('message') or value.get('error') or value.get('reason') or 'ERP service tra ve loi').strip()

        error_type = str(value.get('errorType') or '').strip().lower()
        if error_type in {'error', 'failed', 'fail'}:
            return str(value.get('message') or value.get('error') or value.get('reason') or 'ERP service tra ve loi').strip()

        return ''

    @staticmethod
    def _is_explicit_success_response(value):
        if isinstance(value, dict):
            if 'success' in value:
                return bool(value.get('success'))
            status_text = str(value.get('status') or '').strip().lower()
            if status_text in {'ok', 'success', 'succeed'}:
                return True
            return False

        if isinstance(value, list):
            if value and isinstance(value[0], bool):
                return bool(value[0])
            return False

        return False

    def _map_employees(self, response):
        employees = []
        for item in self._extract_list(response):
            if not isinstance(item, dict):
                continue
            employee_id = (item.get('employee_id') or item.get('maNhanVien') or item.get('lv001') or '').strip()
            if not employee_id:
                continue
            image_token = self._extract_image_token(item.get('image_token') or item.get('lv007'))
            image_url = (item.get('image_url') or '').strip()
            if not image_url and image_token:
                image_url = self._build_token_image_url(image_token)
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
                'image_token': image_token,
                'image_url': image_url,
            })
        return employees

    @staticmethod
    def _extract_image_token(value):
        token = (value or '').strip()
        if not token:
            return ''
        if len(token) < 12:
            return ''
        if any(ch.isspace() for ch in token):
            return ''
        if any(ch in token for ch in ('/', '\\', '?', '#', '&')):
            return ''
        return token

    def _build_token_image_url(self, image_token):
        token = self._extract_image_token(image_token)
        if not token:
            return ''

        mode = 'prod' if self.token_image_mode == 'prod' else 'test'
        base_url = self.token_image_prod_base_url if mode == 'prod' else self.token_image_test_base_url
        if not base_url:
            return ''

        return f"{base_url}/{parse.quote(token, safe='')}"

    def _build_employee_image_url(self, image_ref, auth=None):
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
        service_url = self._resolve_service_url(auth)
        service_dir_url = service_url.rsplit('/', 1)[0] + '/'
        load_image_url = urljoin(service_dir_url, 'loadAnh.php')
        return f"{load_image_url}?{parse.urlencode(query)}"

    @staticmethod
    def _extract_list(value):
        if value is None:
            return []
        if isinstance(value, list):
            # Legacy PHP formats:
            #   [success, message, data]
            #   [success, data]
            #   [success, data, count]
            if value and isinstance(value[0], bool):
                success = bool(value[0])
                if not success:
                    return []

                # Prefer the first container payload after the success flag.
                for candidate in value[1:]:
                    if isinstance(candidate, list):
                        return candidate
                    if isinstance(candidate, dict):
                        return [candidate]
                return []
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

    def _post_json(self, url, payload, headers=None):
        body = json.dumps(payload).encode('utf-8')
        request_headers = {
            'Content-Type': 'application/json; charset=utf-8',
            'Accept': 'application/json',
        }
        if headers:
            request_headers.update(headers)
        raw, _ = self._open(
            url,
            data=body,
            headers=request_headers,
        )
        return self._parse_json(raw)

    def _post_form(self, url, payload, expect_json=True, headers=None):
        body = parse.urlencode(payload).encode('utf-8')
        request_headers = {'Content-Type': 'application/x-www-form-urlencoded'}
        if headers:
            request_headers.update(headers)
        raw, headers = self._open(
            url,
            data=body,
            headers=request_headers,
        )
        if expect_json:
            return self._parse_json(raw)
        return raw, headers.get_content_type()

    def _post_multipart(
        self,
        url,
        fields,
        file_field,
        filename,
        file_bytes,
        file_content_type,
        headers=None,
    ):
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

        request_headers = {'Content-Type': f'multipart/form-data; boundary={boundary}'}
        if headers:
            request_headers.update(headers)

        raw, _ = self._open(
            url,
            data=bytes(body),
            headers=request_headers,
        )
        return self._parse_json(raw)

    def _open(self, url, data=None, headers=None, method=None):
        url_candidates = self._build_url_candidates(url, default_scheme='http')
        if not url_candidates:
            raise ERPServiceError('URL ERP khong hop le hoac dang de trong')

        last_url_error = None
        for index, normalized_url in enumerate(url_candidates):
            req = request.Request(
                normalized_url,
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
                last_url_error = exc
                if index < len(url_candidates) - 1:
                    continue

                reason_text = str(exc.reason or '').strip()
                if 'unknown url type' in reason_text.lower():
                    raise ERPServiceError(
                        f'URL ERP khong hop le: {url}. Vui long them http:// hoac https://',
                    ) from exc

                if len(url_candidates) > 1:
                    attempted = ' / '.join(url_candidates)
                    raise ERPServiceError(
                        f'Khong the ket noi ERP service (da thu {attempted}): {exc.reason}',
                    ) from exc

                raise ERPServiceError(f'Khong the ket noi ERP service: {exc.reason}') from exc
            except ValueError as exc:
                last_url_error = exc
                if index < len(url_candidates) - 1:
                    continue
                raise ERPServiceError(
                    f'URL ERP khong hop le: {url}. Vui long them http:// hoac https://',
                ) from exc

        if last_url_error:
            raise ERPServiceError(f'Khong the ket noi ERP service: {last_url_error}') from last_url_error
        raise ERPServiceError('Khong the ket noi ERP service')

    def _parse_json(self, raw):
        try:
            return json.loads(raw.decode('utf-8-sig'))
        except json.JSONDecodeError as exc:
            raise ERPServiceError('ERP tra ve JSON khong hop le') from exc


erp_http_client = ERPHttpClient()
