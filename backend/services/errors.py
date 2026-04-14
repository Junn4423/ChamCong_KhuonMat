# -*- coding: utf-8 -*-
"""Shared backend service exceptions."""


class ERPServiceError(Exception):
    """Raised when an ERP/CouchDB service call fails."""

    def __init__(self, message, status_code=None, payload=None):
        super().__init__(message)
        self.message = message
        self.status_code = status_code
        self.payload = payload
