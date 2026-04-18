# -*- coding: utf-8 -*-
"""CouchDB auth service with dispatcher-based database routing."""

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
        dispatcher_database=None,
        route_doc_prefix='dispatcher:prefix:',
        route_prefix_length=2,
        fallback_host=None,
        fallback_port=None,
        fallback_dispatcher_database=None,
        fallback_username='',
        fallback_password='',
    ):
        self.user_table = (user_table or 'lv_lv0066').strip()
        self.username = (username or '').strip()
        self.password = password or ''
        self.log_doc_id = (log_doc_id or 'logs').strip()
        self.dispatcher_database = (dispatcher_database or database or '').strip()
        self.route_doc_prefix = (route_doc_prefix or 'dispatcher:prefix:').strip()
        # Deprecated: routing no longer depends on fixed prefix length.
        # Keep for backward compatibility of constructor signature.
        try:
            self.route_prefix_length = int(route_prefix_length) if route_prefix_length is not None else 0
        except (TypeError, ValueError):
            self.route_prefix_length = 0
        self.host = (host or '').strip()
        self.port = int(port or 5984)
        self.timeout = timeout or 10
        self.adapter = CouchDBAdapter(
            host=host,
            port=port,
            database=self.dispatcher_database,
            username=username,
            password=password,
            timeout=timeout,
        )

        self.fallback_host = (fallback_host or '').strip()
        try:
            self.fallback_port = int(fallback_port or 5984)
        except (TypeError, ValueError):
            self.fallback_port = 5984
        self.fallback_dispatcher_database = (
            (fallback_dispatcher_database or '').strip()
            if fallback_dispatcher_database is not None
            else self.dispatcher_database
        )
        self.fallback_dispatcher_database = (
            self.fallback_dispatcher_database or self.dispatcher_database
        ).strip()
        self.fallback_username = (fallback_username or '').strip()
        self.fallback_password = fallback_password or ''
        self.fallback_adapter = None
        if self.fallback_host and self.fallback_dispatcher_database:
            self.fallback_adapter = CouchDBAdapter(
                host=self.fallback_host,
                port=self.fallback_port,
                database=self.fallback_dispatcher_database,
                username=self.fallback_username or self.username,
                password=self.fallback_password or self.password,
                timeout=self.timeout,
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

    @staticmethod
    def _first_non_empty(*values):
        for value in values:
            text = str(value or '').strip()
            if text:
                return text
        return ''

    def _build_adapter(self, database):
        return self._build_adapter_with_host(database)

    def _build_adapter_with_host(self, database, host=None, port=None):
        resolved_host = (host or self.host or '').strip()
        try:
            resolved_port = int(port or self.port or 5984)
        except (TypeError, ValueError):
            resolved_port = int(self.port or 5984)
        return CouchDBAdapter(
            host=resolved_host,
            port=resolved_port,
            database=database,
            username=self.username,
            password=self.password,
            timeout=self.timeout,
        )

    def _normalize_prefix(self, username):
        """Extract route prefix from username.

        New convention: username MUST be in the form "<prefix>.<username>".
        The prefix is everything before the first dot, with variable length.

        Examples:
        - "er.chamcongbasic1" -> "er"
        - "hr1" -> "" (invalid for routing)
        """

        username = (username or '').strip().lower()
        if not username:
            return ''

        prefix, sep, rest = username.partition('.')
        if sep != '.' or not prefix or not rest:
            return ''

        return prefix.strip()

    def _credential_attempts(self, login_username='', login_password=''):
        attempts = []

        if self.username:
            attempts.append((self.username, self.password))

        if self.fallback_host and self.fallback_username:
            attempts.append((self.fallback_username, self.fallback_password))

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

    def _extract_username(self, user_doc, user_table=None):
        if not isinstance(user_doc, dict):
            return ''
        username = str(user_doc.get('lv001') or '').strip()
        if username:
            return username
        doc_id = str(user_doc.get('_id') or '')
        prefix = f'{(user_table or self.user_table).strip() or self.user_table}:'
        if doc_id.startswith(prefix):
            return doc_id[len(prefix):]
        return ''

    def _find_user_doc_by_selector(self, username, auth_user, auth_password, adapter, user_table):
        selector = {
            '$or': [
                {'lv001': username},
                {'_id': f'{user_table}:{username}'},
            ]
        }
        response = adapter.find_documents(
            selector=selector,
            limit=5,
            auth_user=auth_user,
            auth_password=auth_password,
        )
        docs = response.get('docs') if isinstance(response, dict) else []
        for doc in docs or []:
            if not isinstance(doc, dict):
                continue
            if self._extract_username(doc, user_table=user_table) == username:
                return doc
        return None

    def _find_user_doc_by_all_docs(self, username, auth_user, auth_password, adapter, user_table):
        response = adapter.get_all_docs(
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
            if self._extract_username(doc, user_table=user_table) == username:
                return doc
        return None

    def _get_user_doc_with_auth(self, username, login_password='', adapter=None, user_table=None):
        username = (username or '').strip()
        if not username:
            raise ERPServiceError('Thieu ten tai khoan', status_code=400)

        adapter = adapter or self.adapter
        user_table = (user_table or self.user_table).strip() or self.user_table
        doc_id = f'{user_table}:{username}'
        attempts = self._credential_attempts(username, login_password)

        unauthorized = False
        for auth_user, auth_password in attempts:
            try:
                user_doc = adapter.get_document(
                    doc_id,
                    auth_user=auth_user,
                    auth_password=auth_password,
                    allow_not_found=True,
                )
                if isinstance(user_doc, dict):
                    return user_doc, (auth_user, auth_password)

                user_doc = self._find_user_doc_by_selector(
                    username,
                    auth_user,
                    auth_password,
                    adapter,
                    user_table,
                )
                if isinstance(user_doc, dict):
                    return user_doc, (auth_user, auth_password)

                user_doc = self._find_user_doc_by_all_docs(
                    username,
                    auth_user,
                    auth_password,
                    adapter,
                    user_table,
                )
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

    def _route_doc_matches(self, route_doc, prefix):
        if not isinstance(route_doc, dict):
            return False
        if str(route_doc.get('active', '1')).strip() == '0':
            return False

        database = str(route_doc.get('database') or '').strip()
        if not database:
            return False

        route_prefix = str(route_doc.get('prefix') or '').strip().lower()
        if not route_prefix:
            doc_id = str(route_doc.get('_id') or '').strip()
            if self.route_doc_prefix and doc_id.startswith(self.route_doc_prefix):
                route_prefix = doc_id[len(self.route_doc_prefix):].strip().lower()

        if route_prefix == prefix:
            return True

        username_prefix = str(route_doc.get('username_prefix') or '').strip().lower()
        if username_prefix and username_prefix.startswith(prefix):
            return True

        return False

    def _normalize_route(self, route_doc, prefix=''):
        if not isinstance(route_doc, dict):
            raise ERPServiceError('Du lieu dieu phoi CouchDB khong hop le', status_code=500)

        database = str(route_doc.get('database') or '').strip()
        if not database:
            raise ERPServiceError('Thieu database dich trong cau hinh dieu phoi', status_code=500)

        route_prefix = str(route_doc.get('prefix') or '').strip().lower()
        if not route_prefix:
            doc_id = str(route_doc.get('_id') or '').strip()
            if self.route_doc_prefix and doc_id.startswith(self.route_doc_prefix):
                route_prefix = doc_id[len(self.route_doc_prefix):].strip().lower()
        if not route_prefix:
            route_prefix = (prefix or '').strip().lower()

        user_table = str(route_doc.get('user_table') or '').strip() or self.user_table
        system_name = self._first_non_empty(
            route_doc.get('system_name'),
            route_doc.get('service_name'),
            route_doc.get('note'),
        )
        system_note = str(route_doc.get('note') or '').strip()
        welcome_message = str(route_doc.get('welcome_message') or '').strip()
        if not welcome_message and system_name:
            welcome_message = f'Xin chao, day la he thong cham cong {system_name}'

        return {
            'prefix': route_prefix,
            'database': database,
            'user_table': user_table,
            'system_name': system_name,
            'system_note': system_note,
            'welcome_message': welcome_message,
            'doc_id': str(route_doc.get('_id') or '').strip(),
            'raw': route_doc,
        }

    def _apply_route_connection(self, route_payload, route_doc=None, default_host=None, default_port=None):
        if not isinstance(route_payload, dict):
            return route_payload

        route_doc = route_doc if isinstance(route_doc, dict) else {}
        host = str(
            route_doc.get('couchdb_host')
            or route_doc.get('host')
            or default_host
            or self.host
            or ''
        ).strip()
        port_value = route_doc.get('couchdb_port') or route_doc.get('port') or default_port or self.port
        try:
            port = int(port_value or 5984)
        except (TypeError, ValueError):
            port = int(self.port or 5984)

        route_payload['couchdb_host'] = host
        route_payload['couchdb_port'] = port
        return route_payload

    def _find_route_doc_by_selector(self, prefix, auth_user, auth_password, adapter=None):
        adapter = adapter or self.adapter
        selector = {
            '$or': [
                {'prefix': prefix},
                {'username_prefix': f'{prefix}.'},
                {'_id': f'{self.route_doc_prefix}{prefix}'},
            ]
        }
        response = adapter.find_documents(
            selector=selector,
            limit=10,
            auth_user=auth_user,
            auth_password=auth_password,
        )
        docs = response.get('docs') if isinstance(response, dict) else []
        for doc in docs or []:
            if self._route_doc_matches(doc, prefix):
                return doc
        return None

    def _find_route_doc_by_all_docs(self, prefix, auth_user, auth_password, adapter=None):
        adapter = adapter or self.adapter
        response = adapter.get_all_docs(
            include_docs=True,
            limit=2000,
            auth_user=auth_user,
            auth_password=auth_password,
        )
        rows = response.get('rows') if isinstance(response, dict) else []
        for row in rows or []:
            doc = row.get('doc') if isinstance(row, dict) else None
            if self._route_doc_matches(doc, prefix):
                return doc
        return None

    def _resolve_route_by_prefix(
        self,
        prefix,
        login_username='',
        login_password='',
        adapter=None,
        default_host=None,
        default_port=None,
    ):
        prefix = (prefix or '').strip().lower()
        if not prefix:
            raise ERPServiceError('Thieu prefix dieu phoi', status_code=400)

        adapter = adapter or self.adapter
        doc_id = f'{self.route_doc_prefix}{prefix}'
        unauthorized = False
        attempts = self._credential_attempts(login_username=login_username, login_password=login_password)

        for auth_user, auth_password in attempts:
            try:
                route_doc = adapter.get_document(
                    doc_id,
                    auth_user=auth_user,
                    auth_password=auth_password,
                    allow_not_found=True,
                )
                if isinstance(route_doc, dict) and self._route_doc_matches(route_doc, prefix):
                    payload = self._normalize_route(route_doc, prefix)
                    return self._apply_route_connection(payload, route_doc, default_host, default_port)

                route_doc = self._find_route_doc_by_selector(prefix, auth_user, auth_password, adapter=adapter)
                if isinstance(route_doc, dict):
                    payload = self._normalize_route(route_doc, prefix)
                    return self._apply_route_connection(payload, route_doc, default_host, default_port)

                route_doc = self._find_route_doc_by_all_docs(prefix, auth_user, auth_password, adapter=adapter)
                if isinstance(route_doc, dict):
                    payload = self._normalize_route(route_doc, prefix)
                    return self._apply_route_connection(payload, route_doc, default_host, default_port)
            except ERPServiceError as exc:
                if exc.status_code == 401:
                    unauthorized = True
                    continue
                raise

        if unauthorized:
            raise ERPServiceError(
                'Khong the doc CouchDB dieu phoi: kiem tra ERP_COUCHDB_USER/ERP_COUCHDB_PASSWORD',
                status_code=503,
            )
        raise ERPServiceError('Khong tim thay cau hinh dieu phoi cho tai khoan', status_code=404)

    def _resolve_route_by_username(self, username, login_password=''):
        username = (username or '').strip()
        if not username:
            raise ERPServiceError('Thieu ten tai khoan', status_code=400)

        prefix = self._normalize_prefix(username)
        if not prefix:
            raise ERPServiceError(
                'Tai khoan khong hop le de dieu phoi (yeu cau dinh dang prefix.username co dau cham)',
                status_code=400,
            )

        primary_error = None
        try:
            return self._resolve_route_by_prefix(
                prefix,
                login_username=username,
                login_password=login_password,
                adapter=self.adapter,
                default_host=self.host,
                default_port=self.port,
            )
        except ERPServiceError as exc:
            primary_error = exc

        fallback_adapter = self.fallback_adapter
        fallback_host = (self.fallback_host or '').strip()
        if fallback_adapter and fallback_host:
            status_code = int(primary_error.status_code or 0)
            should_try_fallback = status_code == 404 or status_code >= 500
            same_host = (
                fallback_host == (self.host or '').strip()
                and int(self.fallback_port or 0) == int(self.port or 0)
                and (self.fallback_dispatcher_database or '').strip() == (self.dispatcher_database or '').strip()
            )
            if should_try_fallback and not same_host:
                try:
                    return self._resolve_route_by_prefix(
                        prefix,
                        login_username=username,
                        login_password=login_password,
                        adapter=fallback_adapter,
                        default_host=fallback_host,
                        default_port=self.fallback_port,
                    )
                except ERPServiceError:
                    raise primary_error

        raise primary_error

    def _list_routes_with_auth(self, auth_user, auth_password):
        response = self.adapter.get_all_docs(
            include_docs=True,
            limit=2000,
            auth_user=auth_user,
            auth_password=auth_password,
        )
        rows = response.get('rows') if isinstance(response, dict) else []
        routes = []
        for row in rows or []:
            doc = row.get('doc') if isinstance(row, dict) else None
            if not isinstance(doc, dict):
                continue

            doc_id = str(doc.get('_id') or '').strip()
            if self.route_doc_prefix and doc_id and not doc_id.startswith(self.route_doc_prefix):
                continue

            try:
                prefix = ''
                if self.route_doc_prefix and doc_id.startswith(self.route_doc_prefix):
                    prefix = doc_id[len(self.route_doc_prefix):].strip().lower()
                route_payload = self._normalize_route(doc, prefix=prefix)
                route_payload = self._apply_route_connection(route_payload, doc, self.host, self.port)
                routes.append(route_payload)
            except ERPServiceError:
                continue

        return routes

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

    def _find_user_by_token_in_database(self, adapter, token, user_table, auth_user, auth_password):
        selector = {
            '$or': [
                {'lv097': token},
                {'lv297': token},
                {'lv397': token},
            ]
        }

        doc = None
        response = adapter.find_documents(
            selector=selector,
            limit=1,
            auth_user=auth_user,
            auth_password=auth_password,
        )
        docs = response.get('docs') if isinstance(response, dict) else []
        if docs:
            doc = docs[0]

        if doc is None:
            rows = adapter.get_all_docs(
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
            return None, ''

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

        username = self._extract_username(doc, user_table=user_table)
        if not username:
            return None, ''

        return doc, device_type

    def authenticate_user(self, username, password, device_type='web', type_code=''):
        username = (username or '').strip()
        password = password or ''
        device = self._normalize_device(device_type)

        route = self._resolve_route_by_username(username, login_password=password)
        target_db = route.get('database') or ''
        target_user_table = route.get('user_table') or self.user_table
        target_adapter = self._build_adapter_with_host(
            target_db,
            host=route.get('couchdb_host'),
            port=route.get('couchdb_port'),
        )

        user_doc, _ = self._get_user_doc_with_auth(
            username,
            login_password=password,
            adapter=target_adapter,
            user_table=target_user_table,
        )

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

        account_code = self._extract_username(user_doc, user_table=target_user_table) or username
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
            'route_prefix': route.get('prefix') or '',
            'routed_database': target_db,
            'dispatch_database': self.dispatcher_database,
            'system_name': route.get('system_name') or '',
            'system_note': route.get('system_note') or '',
            'welcome_message': route.get('welcome_message') or '',
        }

    def save_token(self, username, token, device_type='web', login_password=''):
        username = (username or '').strip()
        token = (token or '').strip()
        if not username or not token:
            return False

        device = self._normalize_device(device_type)
        token_field, date_field, block_field = self.DEVICE_FIELDS[device]
        route = self._resolve_route_by_username(username, login_password=login_password)
        target_db = route.get('database') or ''
        target_user_table = route.get('user_table') or self.user_table
        target_adapter = self._build_adapter_with_host(
            target_db,
            host=route.get('couchdb_host'),
            port=route.get('couchdb_port'),
        )

        for attempt in range(3):
            user_doc, (auth_user, auth_password) = self._get_user_doc_with_auth(
                username,
                login_password=login_password,
                adapter=target_adapter,
                user_table=target_user_table,
            )
            user_doc[token_field] = token
            user_doc[date_field] = self._now_text()
            user_doc[block_field] = 0
            user_doc['updated_at'] = self._now_text()

            try:
                target_adapter.put_document(
                    user_doc.get('_id') or f'{target_user_table}:{username}',
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
        route = self._resolve_route_by_username(username, login_password=login_password)
        target_db = route.get('database') or ''
        target_user_table = route.get('user_table') or self.user_table
        target_adapter = self._build_adapter_with_host(
            target_db,
            host=route.get('couchdb_host'),
            port=route.get('couchdb_port'),
        )

        for attempt in range(3):
            user_doc, (auth_user, auth_password) = self._get_user_doc_with_auth(
                username,
                login_password=login_password,
                adapter=target_adapter,
                user_table=target_user_table,
            )
            user_doc[token_field] = ''
            user_doc[date_field] = self._now_text()
            user_doc['updated_at'] = self._now_text()

            try:
                target_adapter.put_document(
                    user_doc.get('_id') or f'{target_user_table}:{username}',
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

        route = self._resolve_route_by_username(username)
        target_db = route.get('database') or ''
        target_adapter = self._build_adapter_with_host(
            target_db,
            host=route.get('couchdb_host'),
            port=route.get('couchdb_port'),
        )

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
                    log_doc = target_adapter.get_document(
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
                    log_doc['updated_at'] = self._now_text()

                    target_adapter.put_document(
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

        unauthorized = False
        for auth_user, auth_password in self._credential_attempts():
            try:
                routes = self._list_routes_with_auth(auth_user, auth_password)
                candidates = []
                seen_databases = set()

                for route in routes:
                    database = route.get('database') or ''
                    if not database or database in seen_databases:
                        continue
                    seen_databases.add(database)
                    candidates.append((database, route))

                if self.dispatcher_database and self.dispatcher_database not in seen_databases:
                    candidates.append((self.dispatcher_database, None))

                for database, route in candidates:
                    adapter = self._build_adapter_with_host(
                        database,
                        host=(route or {}).get('couchdb_host'),
                        port=(route or {}).get('couchdb_port'),
                    )
                    user_table = (route or {}).get('user_table') or self.user_table
                    try:
                        doc, device_type = self._find_user_by_token_in_database(
                            adapter,
                            token,
                            user_table,
                            auth_user,
                            auth_password,
                        )
                    except ERPServiceError as exc:
                        if exc.status_code == 401:
                            unauthorized = True
                            continue
                        raise

                    if not isinstance(doc, dict):
                        continue

                    username = self._extract_username(doc, user_table=user_table)
                    route_payload = route
                    if not route_payload and username:
                        try:
                            route_payload = self._resolve_route_by_username(username)
                        except ERPServiceError:
                            route_payload = {
                                'prefix': self._normalize_prefix(username),
                                'database': database,
                                'user_table': user_table,
                                'system_name': '',
                                'system_note': '',
                                'welcome_message': '',
                            }

                    return {
                        'success': True,
                        'username': username,
                        'deviceType': device_type,
                        'userData': doc,
                        'routed_database': database,
                        'route': route_payload or {},
                        'system_name': (route_payload or {}).get('system_name', ''),
                        'welcome_message': (route_payload or {}).get('welcome_message', ''),
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
        route = token_info.get('route') or {}
        return {
            'database': str(user_data.get('lv670') or '').strip(),
            'IPv4': str(user_data.get('lv094') or '').strip(),
            'user': str(user_data.get('lv096') or '').strip(),
            'password': str(user_data.get('lv099') or '').strip(),
            'port': str(user_data.get('lv100') or '').strip(),
            'username': token_info.get('username') or '',
            'deviceType': token_info.get('deviceType') or '',
            'routed_database': token_info.get('routed_database') or '',
            'route_prefix': str(route.get('prefix') or '').strip(),
            'system_name': str(route.get('system_name') or '').strip(),
            'welcome_message': str(route.get('welcome_message') or '').strip(),
        }
