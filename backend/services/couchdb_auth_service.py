# -*- coding: utf-8 -*-
"""CouchDB auth service for dynamic database routing."""

from __future__ import annotations

import hashlib
import time
from datetime import datetime

from backend.services.couchdb_adapter import CouchDBAdapter
from backend.services.errors import ERPServiceError


class CouchDBDynamicAuthService:
    DEVICE_FIELDS = {
        'web': ('lv097', 'lv098', 'lv296'),
        'mobile': ('lv297', 'lv298', 'lv299'),
        'desktop': ('lv397', 'lv398', 'lv399'),
    }

    def __init__(
        self,
        host,
        port,
        database,
        user_table='lv_lv0066',
        username='',
        password='',
        timeout=10,
        log_doc_id='logs',
    ):
        self.user_table = (user_table or 'lv_lv0066').strip()
        self.username = (username or '').strip()
        self.password = password or ''
        self.log_doc_id = (log_doc_id or 'logs').strip()
        self.adapter = CouchDBAdapter(
            host=host,
            port=port,
            database=database,
            username=username,
            password=password,
            timeout=timeout,
        )

    @staticmethod
    def _now_text():
        return datetime.now().strftime('%Y-%m-%d %H:%M:%S')

    @staticmethod
    def _normalize_device(device_type):
        device = str(device_type or 'web').strip().lower()
        return device if device in {'web', 'mobile', 'desktop'} else 'web'

    @staticmethod
    def _normalize_code(value):
        return str(value or '').strip().upper()

    def _credential_attempts(self, login_username='', login_password=''):
        attempts = []

        if self.username:
            attempts.append((self.username, self.password))

        attempts.append((None, None))

        login_username = (login_username or '').strip()
        if login_username:
            attempts.append((login_username, login_password or ''))

        deduped = []
        seen = set()
        for auth_user, auth_password in attempts:
            key = (auth_user or '', auth_password or '')
            if key in seen:
                continue
            seen.add(key)
            deduped.append((auth_user, auth_password))
        return deduped

    def _extract_username(self, user_doc):
        if not isinstance(user_doc, dict):
            return ''
        username = str(user_doc.get('lv001') or '').strip()
        if username:
            return username
        doc_id = str(user_doc.get('_id') or '')
        prefix = f'{self.user_table}:'
        if doc_id.startswith(prefix):
            return doc_id[len(prefix):]
        return ''

    def _find_user_doc_by_selector(self, username, auth_user, auth_password):
        selector = {
            '$or': [
                {'lv001': username},
                {'_id': f'{self.user_table}:{username}'},
            ]
        }
        response = self.adapter.find_documents(
            selector=selector,
            limit=5,
            auth_user=auth_user,
            auth_password=auth_password,
        )
        docs = response.get('docs') if isinstance(response, dict) else []
        for doc in docs or []:
            if not isinstance(doc, dict):
                continue
            if self._extract_username(doc) == username:
                return doc
        return None

    def _find_user_doc_by_all_docs(self, username, auth_user, auth_password):
        response = self.adapter.get_all_docs(
            include_docs=True,
            limit=2000,
            auth_user=auth_user,
            auth_password=auth_password,
        )
        rows = response.get('rows') if isinstance(response, dict) else []
        for row in rows or []:
            doc = row.get('doc') if isinstance(row, dict) else None
            if not isinstance(doc, dict):
                continue
            if self._extract_username(doc) == username:
                return doc
        return None

    def _get_user_doc_with_auth(self, username, login_password=''):
        username = (username or '').strip()
        if not username:
            raise ERPServiceError('Thieu ten tai khoan', status_code=400)

        doc_id = f'{self.user_table}:{username}'
        attempts = self._credential_attempts(username, login_password)

        unauthorized = False
        for auth_user, auth_password in attempts:
            try:
                user_doc = self.adapter.get_document(
                    doc_id,
                    auth_user=auth_user,
                    auth_password=auth_password,
                    allow_not_found=True,
                )
                if isinstance(user_doc, dict):
                    return user_doc, (auth_user, auth_password)

                user_doc = self._find_user_doc_by_selector(username, auth_user, auth_password)
                if isinstance(user_doc, dict):
                    return user_doc, (auth_user, auth_password)

                user_doc = self._find_user_doc_by_all_docs(username, auth_user, auth_password)
                if isinstance(user_doc, dict):
                    return user_doc, (auth_user, auth_password)
            except ERPServiceError as exc:
                if exc.status_code == 401:
                    unauthorized = True
                    continue
                raise

        if unauthorized:
            raise ERPServiceError(
                'Khong the doc CouchDB: kiem tra ERP_COUCHDB_USER/ERP_COUCHDB_PASSWORD hoac quyen tai khoan',
                status_code=503,
            )
        raise ERPServiceError('Tai khoan khong ton tai tren CouchDB', status_code=404)

    def _resolve_domain(self, user_doc, device_type):
        if device_type == 'mobile':
            return str(user_doc.get('lv665') or '').strip()
        desktop_domain = str(user_doc.get('lv688') or '').strip()
        if desktop_domain:
            return desktop_domain
        return str(user_doc.get('lv668') or '').strip()

    def _resolve_device_token(self, user_doc, device_type):
        token_field, _, _ = self.DEVICE_FIELDS[device_type]
        fallback = [token_field, 'lv097', 'lv297', 'lv397']
        for field in fallback:
            value = str(user_doc.get(field) or '').strip()
            if value:
                return value
        return ''

    def authenticate_user(self, username, password, device_type='web', type_code=''):
        username = (username or '').strip()
        password = password or ''
        device = self._normalize_device(device_type)
        user_doc, _ = self._get_user_doc_with_auth(username, login_password=password)

        input_hash = hashlib.md5(password.encode('utf-8')).hexdigest()
        doc_hash = str(user_doc.get('lv005') or '').strip().lower()
        if not doc_hash or doc_hash != input_hash:
            raise ERPServiceError('Mat khau khong chinh xac', status_code=401)

        _, _, block_field = self.DEVICE_FIELDS[device]
        if str(user_doc.get(block_field, '0')).strip() == '1':
            raise ERPServiceError(f'Tai khoan bi cam dang nhap tren thiet bi {device}', status_code=403)

        expected_type = self._normalize_code(type_code)
        account_type = self._normalize_code(user_doc.get('lv676'))
        if expected_type and account_type and account_type != expected_type:
            raise ERPServiceError('Tai khoan khong co quyen truy cap ung dung nay', status_code=403)

        account_code = self._extract_username(user_doc) or username
        return {
            'code': account_code,
            'token': self._resolve_device_token(user_doc, device),
            'userid': str(user_doc.get('lv006') or '').strip(),
            'department': str(user_doc.get('lv003') or '').strip(),
            'role': str(user_doc.get('lv004') or '').strip(),
            'name': str(user_doc.get('lv002') or account_code).strip(),
            'domain': self._resolve_domain(user_doc, device),
            'method': str(user_doc.get('lv669') or 'http').strip(),
            'database': str(user_doc.get('lv670') or '').strip(),
            'IPv4': str(user_doc.get('lv094') or '').strip(),
            'lv006': str(user_doc.get('lv006') or '').strip(),
            'lv900': str(user_doc.get('lv900') or '').strip(),
            'lv705': str(user_doc.get('lv705') or '').strip(),
            'lv667': str(user_doc.get('lv667') or '').strip(),
            'lv040': str(user_doc.get('lv040') or '').strip(),
            'quota_exceeded': False,
            'quota_message': '',
            'storage_info': {},
        }

    def save_token(self, username, token, device_type='web', login_password=''):
        username = (username or '').strip()
        token = (token or '').strip()
        if not username or not token:
            return False

        device = self._normalize_device(device_type)
        token_field, date_field, block_field = self.DEVICE_FIELDS[device]

        for attempt in range(3):
            user_doc, (auth_user, auth_password) = self._get_user_doc_with_auth(
                username,
                login_password=login_password,
            )
            user_doc[token_field] = token
            user_doc[date_field] = self._now_text()
            user_doc[block_field] = 0
            user_doc['updated_at'] = self._now_text()

            try:
                self.adapter.put_document(
                    user_doc.get('_id') or f'{self.user_table}:{username}',
                    user_doc,
                    auth_user=auth_user,
                    auth_password=auth_password,
                )
                return True
            except ERPServiceError as exc:
                if exc.status_code == 409 and attempt < 2:
                    time.sleep(0.1 * (attempt + 1))
                    continue
                raise

        return False

    def remove_token(self, username, device_type='web', login_password=''):
        username = (username or '').strip()
        if not username:
            return False

        device = self._normalize_device(device_type)
        token_field, date_field, _ = self.DEVICE_FIELDS[device]

        for attempt in range(3):
            user_doc, (auth_user, auth_password) = self._get_user_doc_with_auth(
                username,
                login_password=login_password,
            )
            user_doc[token_field] = ''
            user_doc[date_field] = self._now_text()
            user_doc['updated_at'] = self._now_text()

            try:
                self.adapter.put_document(
                    user_doc.get('_id') or f'{self.user_table}:{username}',
                    user_doc,
                    auth_user=auth_user,
                    auth_password=auth_password,
                )
                return True
            except ERPServiceError as exc:
                if exc.status_code == 409 and attempt < 2:
                    time.sleep(0.1 * (attempt + 1))
                    continue
                raise

        return False

    def write_auth_log(self, username, status, device_type='web', token='', ip='', mac=''):
        username = (username or '').strip()
        if not username:
            return False

        now = datetime.now()
        entry = {
            'username': username,
            'date': now.strftime('%Y-%m-%d'),
            'time': now.strftime('%H:%M:%S'),
            'status': int(status),
            'ip': str(ip or '').strip(),
            'mac': str(mac or '').strip() or 'unknown',
            'deviceType': self._normalize_device(device_type),
            'token': str(token or '').strip(),
            'timestamp': int(now.timestamp()),
        }

        attempts = self._credential_attempts()
        last_error = None

        for retry in range(3):
            conflict_error = None
            for auth_user, auth_password in attempts:
                try:
                    log_doc = self.adapter.get_document(
                        self.log_doc_id,
                        auth_user=auth_user,
                        auth_password=auth_password,
                        allow_not_found=True,
                    )
                    if not isinstance(log_doc, dict):
                        log_doc = {
                            '_id': self.log_doc_id,
                            'type': 'login_logs',
                            'logs': [],
                        }
                    if not isinstance(log_doc.get('logs'), list):
                        log_doc['logs'] = []
                    log_doc['logs'].append(entry)

                    self.adapter.put_document(
                        self.log_doc_id,
                        log_doc,
                        auth_user=auth_user,
                        auth_password=auth_password,
                    )
                    return True
                except ERPServiceError as exc:
                    last_error = exc
                    if exc.status_code == 409:
                        conflict_error = exc
                        break
                    if exc.status_code == 401:
                        continue
                    raise

            if conflict_error and retry < 2:
                time.sleep(0.1 * (retry + 1))
                continue
            break

        if last_error:
            raise last_error
        return False

    def find_user_by_token(self, token):
        token = (token or '').strip()
        if not token:
            raise ERPServiceError('Thieu token de xac minh', status_code=400)

        selector = {
            '$or': [
                {'lv097': token},
                {'lv297': token},
                {'lv397': token},
            ]
        }

        unauthorized = False
        for auth_user, auth_password in self._credential_attempts():
            try:
                response = self.adapter.find_documents(
                    selector=selector,
                    limit=1,
                    auth_user=auth_user,
                    auth_password=auth_password,
                )
                docs = response.get('docs') if isinstance(response, dict) else []
                if docs:
                    doc = docs[0]
                else:
                    doc = None

                if doc is None:
                    rows = self.adapter.get_all_docs(
                        include_docs=True,
                        limit=2000,
                        auth_user=auth_user,
                        auth_password=auth_password,
                    ).get('rows', [])
                    for row in rows:
                        candidate = row.get('doc') if isinstance(row, dict) else None
                        if not isinstance(candidate, dict):
                            continue
                        if (
                            str(candidate.get('lv097') or '').strip() == token
                            or str(candidate.get('lv297') or '').strip() == token
                            or str(candidate.get('lv397') or '').strip() == token
                        ):
                            doc = candidate
                            break

                if not isinstance(doc, dict):
                    continue

                device_type = ''
                if str(doc.get('lv097') or '').strip() == token:
                    device_type = 'web'
                    if str(doc.get('lv296', '0')).strip() == '1':
                        raise ERPServiceError('Tai khoan bi cam dang nhap tren thiet bi web', status_code=403)
                elif str(doc.get('lv297') or '').strip() == token:
                    device_type = 'mobile'
                    if str(doc.get('lv299', '0')).strip() == '1':
                        raise ERPServiceError('Tai khoan bi cam dang nhap tren thiet bi mobile', status_code=403)
                elif str(doc.get('lv397') or '').strip() == token:
                    device_type = 'desktop'
                    if str(doc.get('lv399', '0')).strip() == '1':
                        raise ERPServiceError('Tai khoan bi cam dang nhap tren thiet bi desktop', status_code=403)

                return {
                    'success': True,
                    'username': self._extract_username(doc),
                    'deviceType': device_type,
                    'userData': doc,
                }
            except ERPServiceError as exc:
                if exc.status_code == 401:
                    unauthorized = True
                    continue
                raise

        if unauthorized:
            raise ERPServiceError(
                'Khong the doc CouchDB: kiem tra ERP_COUCHDB_USER/ERP_COUCHDB_PASSWORD hoac quyen tai khoan',
                status_code=503,
            )
        raise ERPServiceError('Token khong hop le hoac da het han', status_code=404)

    def resolve_dynamic_database(self, token):
        token_info = self.find_user_by_token(token)
        user_data = token_info.get('userData') or {}
        return {
            'database': str(user_data.get('lv670') or '').strip(),
            'IPv4': str(user_data.get('lv094') or '').strip(),
            'user': str(user_data.get('lv096') or '').strip(),
            'password': str(user_data.get('lv099') or '').strip(),
            'port': str(user_data.get('lv100') or '').strip(),
            'username': token_info.get('username') or '',
            'deviceType': token_info.get('deviceType') or '',
        }
