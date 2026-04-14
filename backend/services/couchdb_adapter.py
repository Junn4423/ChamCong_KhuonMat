# -*- coding: utf-8 -*-
"""Low-level CouchDB HTTP adapter."""

from __future__ import annotations

import base64
import json
from urllib import parse, request
from urllib.error import HTTPError, URLError

from backend.services.errors import ERPServiceError


class CouchDBAdapter:
    def __init__(self, host, port, database, username='', password='', timeout=10):
        self.host = (host or '').strip()
        self.port = int(port or 5984)
        self.database = (database or '').strip()
        self.username = (username or '').strip()
        self.password = password or ''
        self.timeout = timeout or 10

    def _validate_config(self):
        if not self.host or not self.database:
            raise ERPServiceError('Thieu cau hinh CouchDB (host/database) tren backend', status_code=500)

    def _build_url(self, path, query=None):
        self._validate_config()
        base = f'http://{self.host}:{self.port}'
        clean_path = str(path or '').lstrip('/')
        if not clean_path:
            raise ERPServiceError('CouchDB request path khong hop le', status_code=500)
        url = f'{base}/{clean_path}'
        if query:
            url = f"{url}?{parse.urlencode(query, doseq=True)}"
        return url

    @staticmethod
    def _build_basic_auth(username, password):
        if not username:
            return ''
        encoded = base64.b64encode(f'{username}:{password or ""}'.encode('utf-8')).decode('ascii')
        return f'Basic {encoded}'

    @staticmethod
    def _decode_json(raw):
        if raw is None:
            return None
        text = raw.decode('utf-8', errors='ignore').strip()
        if not text:
            return None
        try:
            return json.loads(text)
        except json.JSONDecodeError:
            return None

    def request_json(
        self,
        path,
        method='GET',
        payload=None,
        query=None,
        auth_user=None,
        auth_password=None,
        allow_not_found=False,
    ):
        body = None
        headers = {'Accept': 'application/json'}

        if payload is not None:
            body = json.dumps(payload).encode('utf-8')
            headers['Content-Type'] = 'application/json; charset=utf-8'

        auth_value = self._build_basic_auth(auth_user, auth_password)
        if auth_value:
            headers['Authorization'] = auth_value

        req = request.Request(
            self._build_url(path, query=query),
            data=body,
            headers=headers,
            method=method,
        )

        try:
            with request.urlopen(req, timeout=self.timeout) as response:
                parsed = self._decode_json(response.read())
                return parsed if parsed is not None else {}
        except HTTPError as exc:
            raw = exc.read()
            parsed = self._decode_json(raw)
            if allow_not_found and exc.code == 404:
                return None

            message = str(exc)
            if isinstance(parsed, dict):
                message = parsed.get('reason') or parsed.get('message') or parsed.get('error') or message
            raise ERPServiceError(message, status_code=exc.code, payload=parsed) from exc
        except URLError as exc:
            raise ERPServiceError(f'Khong the ket noi CouchDB: {exc.reason}', status_code=503) from exc

    def get_document(self, doc_id, auth_user=None, auth_password=None, allow_not_found=False):
        encoded_db = parse.quote(self.database, safe='')
        encoded_doc = parse.quote(str(doc_id or ''), safe=':')
        return self.request_json(
            f'{encoded_db}/{encoded_doc}',
            method='GET',
            auth_user=auth_user,
            auth_password=auth_password,
            allow_not_found=allow_not_found,
        )

    def put_document(self, doc_id, document, auth_user=None, auth_password=None):
        encoded_db = parse.quote(self.database, safe='')
        encoded_doc = parse.quote(str(doc_id or ''), safe=':')
        return self.request_json(
            f'{encoded_db}/{encoded_doc}',
            method='PUT',
            payload=document,
            auth_user=auth_user,
            auth_password=auth_password,
        )

    def find_documents(self, selector, limit=1, auth_user=None, auth_password=None):
        encoded_db = parse.quote(self.database, safe='')
        payload = {'selector': selector}
        if limit:
            payload['limit'] = int(limit)
        return self.request_json(
            f'{encoded_db}/_find',
            method='POST',
            payload=payload,
            auth_user=auth_user,
            auth_password=auth_password,
        )

    def get_all_docs(self, include_docs=True, limit=None, auth_user=None, auth_password=None):
        encoded_db = parse.quote(self.database, safe='')
        query = {'include_docs': 'true' if include_docs else 'false'}
        if limit:
            query['limit'] = int(limit)
        return self.request_json(
            f'{encoded_db}/_all_docs',
            method='GET',
            query=query,
            auth_user=auth_user,
            auth_password=auth_password,
        )
