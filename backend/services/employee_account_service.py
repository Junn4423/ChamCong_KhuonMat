# -*- coding: utf-8 -*-
"""Employee account management service (local SQLite auth for attendance users)."""

import hashlib
import os
import re
from datetime import datetime

from sqlalchemy import func

from backend.models.database import db, User, EmployeeAccount
from backend.services.erp_http_client import ERPServiceError, erp_http_client

_MD5_HASH_RE = re.compile(r'^[a-f0-9]{32}$')


def _normalize_employee_id(value):
    return str(value or '').strip()


def _normalize_username(value):
    return str(value or '').strip()


def _normalize_md5_hash(value):
    token = str(value or '').strip().lower()
    if _MD5_HASH_RE.fullmatch(token):
        return token
    return ''


class EmployeeAccountService:
    def __init__(self):
        raw_max_attempts = str(os.environ.get('EMPLOYEE_MAX_FAILED_ATTEMPTS', '5')).strip()
        try:
            parsed = int(raw_max_attempts)
        except (TypeError, ValueError):
            parsed = 5
        self.max_failed_attempts = max(1, parsed)

    @staticmethod
    def hash_password_md5(password_plain):
        raw_password = '' if password_plain is None else str(password_plain)
        return hashlib.md5(raw_password.encode('utf-8')).hexdigest()

    def list_accounts(self):
        rows = (
            db.session.query(EmployeeAccount, User)
            .join(User, EmployeeAccount.user_id == User.id)
            .order_by(User.employee_id.asc())
            .all()
        )

        accounts = []
        for account, user in rows:
            accounts.append({
                'id': account.id,
                'user_id': user.id,
                'employee_id': user.employee_id,
                'name': user.name,
                'department': user.department or '',
                'position': user.position or '',
                'username': account.username,
                'is_active': bool(account.is_active),
                'is_locked': bool(account.is_locked),
                'failed_attempts': int(account.failed_attempts or 0),
                'last_login_at': account.last_login_at.isoformat() if account.last_login_at else '',
                'synced_at': account.synced_at.isoformat() if account.synced_at else '',
                'updated_at': account.updated_at.isoformat() if account.updated_at else '',
            })

        return accounts

    def _find_account_by_username(self, username):
        normalized_username = _normalize_username(username)
        if not normalized_username:
            return None

        return (
            EmployeeAccount.query
            .filter(func.lower(EmployeeAccount.username) == normalized_username.lower())
            .first()
        )

    @staticmethod
    def _find_user_by_employee_id(employee_id):
        normalized_id = _normalize_employee_id(employee_id)
        if not normalized_id:
            return None
        return User.query.filter_by(employee_id=normalized_id).first()

    def upsert_account_for_user(self, user, username, password_hash_md5, overwrite_password=False):
        if user is None:
            raise ERPServiceError('Không tìm thấy nhân viên nội bộ để gán tài khoản', status_code=404)

        normalized_username = _normalize_username(username) or _normalize_employee_id(user.employee_id)
        if not normalized_username:
            raise ERPServiceError('Thiếu tên đăng nhập tài khoản nhân viên', status_code=400)

        normalized_hash = _normalize_md5_hash(password_hash_md5)
        if not normalized_hash:
            raise ERPServiceError(
                f'Tài khoản {normalized_username} không có mật khẩu md5 hợp lệ từ ERP',
                status_code=400,
            )

        username_conflict = (
            EmployeeAccount.query
            .filter(func.lower(EmployeeAccount.username) == normalized_username.lower())
            .filter(EmployeeAccount.user_id != user.id)
            .first()
        )
        if username_conflict is not None:
            raise ERPServiceError(
                f'Tên đăng nhập {normalized_username} đã được gán cho nhân viên khác',
                status_code=409,
            )

        account = EmployeeAccount.query.filter_by(user_id=user.id).first()
        created = account is None
        password_updated = False

        if created:
            account = EmployeeAccount(
                user_id=user.id,
                username=normalized_username,
                password_hash_md5=normalized_hash,
                is_active=True,
                is_locked=False,
                failed_attempts=0,
                synced_at=datetime.utcnow(),
                updated_at=datetime.utcnow(),
            )
            password_updated = True
        else:
            account.username = normalized_username

            current_hash = _normalize_md5_hash(account.password_hash_md5)
            if overwrite_password or not current_hash:
                if current_hash != normalized_hash:
                    account.password_hash_md5 = normalized_hash
                    password_updated = True

            account.synced_at = datetime.utcnow()
            account.updated_at = datetime.utcnow()

        db.session.add(account)
        db.session.commit()

        return {
            'account': account,
            'created': created,
            'password_updated': password_updated,
        }

    def pull_accounts_from_erp(self, auth, overwrite_password=False, employee_ids=None):
        credentials = erp_http_client.list_employee_credentials(auth, employee_ids=employee_ids)

        requested_count = len(employee_ids or [])
        summary = {
            'requested': requested_count,
            'fetched': len(credentials),
            'created': 0,
            'updated': 0,
            'password_updated': 0,
            'skipped_missing_user': 0,
            'skipped_missing_hash': 0,
            'skipped_invalid': 0,
            'errors': [],
            'overwrite_password': bool(overwrite_password),
        }

        for item in credentials:
            employee_id = _normalize_employee_id(item.get('employee_id'))
            username = _normalize_username(item.get('username')) or employee_id
            password_hash_md5 = _normalize_md5_hash(item.get('password_hash_md5'))

            if not employee_id or not username:
                summary['skipped_invalid'] += 1
                continue

            if not password_hash_md5:
                summary['skipped_missing_hash'] += 1
                continue

            user = self._find_user_by_employee_id(employee_id)
            if user is None:
                summary['skipped_missing_user'] += 1
                continue

            try:
                result = self.upsert_account_for_user(
                    user,
                    username,
                    password_hash_md5,
                    overwrite_password=overwrite_password,
                )
            except ERPServiceError as exc:
                summary['errors'].append({
                    'employee_id': employee_id,
                    'message': exc.message,
                    'status_code': exc.status_code,
                })
                continue
            except Exception as exc:
                db.session.rollback()
                summary['errors'].append({
                    'employee_id': employee_id,
                    'message': str(exc),
                    'status_code': 500,
                })
                continue

            if result.get('created'):
                summary['created'] += 1
            else:
                summary['updated'] += 1

            if result.get('password_updated'):
                summary['password_updated'] += 1

        return summary

    def authenticate_employee(self, username, password_plain):
        normalized_username = _normalize_username(username)
        if not normalized_username:
            raise ERPServiceError('Vui lòng nhập tài khoản nhân viên', status_code=400)

        if password_plain is None or str(password_plain) == '':
            raise ERPServiceError('Vui lòng nhập mật khẩu', status_code=400)

        account = self._find_account_by_username(normalized_username)
        if account is None:
            raise ERPServiceError('Sai tài khoản hoặc mật khẩu', status_code=401)

        if not account.user:
            raise ERPServiceError('Tài khoản chưa liên kết nhân viên', status_code=404)

        if not bool(account.is_active):
            raise ERPServiceError('Tài khoản đã bị vô hiệu hóa', status_code=403)

        if bool(account.is_locked):
            raise ERPServiceError('Tài khoản đang bị khóa. Vui lòng liên hệ quản trị.', status_code=423)

        expected_hash = _normalize_md5_hash(account.password_hash_md5)
        if not expected_hash:
            raise ERPServiceError('Tài khoản chưa có mật khẩu hợp lệ', status_code=403)

        provided_hash = self.hash_password_md5(password_plain)
        if provided_hash != expected_hash:
            account.failed_attempts = int(account.failed_attempts or 0) + 1
            account.updated_at = datetime.utcnow()

            if account.failed_attempts >= self.max_failed_attempts:
                account.is_locked = True
                db.session.add(account)
                db.session.commit()
                raise ERPServiceError(
                    f'Tài khoản đã bị khóa sau {self.max_failed_attempts} lần nhập sai',
                    status_code=423,
                )

            db.session.add(account)
            db.session.commit()
            raise ERPServiceError('Sai tài khoản hoặc mật khẩu', status_code=401)

        account.failed_attempts = 0
        account.is_locked = False
        account.last_login_at = datetime.utcnow()
        account.updated_at = datetime.utcnow()
        db.session.add(account)
        db.session.commit()

        return account, account.user

    def reset_password(self, account_id, new_password_plain):
        account = EmployeeAccount.query.filter_by(id=account_id).first()
        if account is None:
            raise ERPServiceError('Không tìm thấy tài khoản nhân viên', status_code=404)

        raw_password = str(new_password_plain or '')
        if len(raw_password) < 4:
            raise ERPServiceError('Mật khẩu mới phải có ít nhất 4 ký tự', status_code=400)

        account.password_hash_md5 = self.hash_password_md5(raw_password)
        account.failed_attempts = 0
        account.is_locked = False
        account.is_active = True
        account.updated_at = datetime.utcnow()
        db.session.add(account)
        db.session.commit()

        return account

    def upsert_account_with_plain_password(self, user, username, password_plain):
        if user is None:
            raise ERPServiceError('Không tìm thấy nhân viên nội bộ để gán tài khoản', status_code=404)

        raw_password = str(password_plain or '')
        if len(raw_password) < 4:
            raise ERPServiceError('Mật khẩu phải có ít nhất 4 ký tự', status_code=400)

        username_value = _normalize_username(username) or _normalize_employee_id(user.employee_id)
        password_hash_md5 = self.hash_password_md5(raw_password)
        result = self.upsert_account_for_user(
            user,
            username_value,
            password_hash_md5,
            overwrite_password=True,
        )

        account = result.get('account')
        if account is not None:
            account.is_active = True
            account.is_locked = False
            account.failed_attempts = 0
            account.updated_at = datetime.utcnow()
            db.session.add(account)
            db.session.commit()

        return result

    def set_lock_state(self, account_id, is_locked):
        account = EmployeeAccount.query.filter_by(id=account_id).first()
        if account is None:
            raise ERPServiceError('Không tìm thấy tài khoản nhân viên', status_code=404)

        account.is_locked = bool(is_locked)
        if not account.is_locked:
            account.failed_attempts = 0
        account.updated_at = datetime.utcnow()
        db.session.add(account)
        db.session.commit()
        return account


employee_account_service = EmployeeAccountService()
