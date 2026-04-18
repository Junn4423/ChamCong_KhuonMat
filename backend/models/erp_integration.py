# -*- coding: utf-8 -*-
"""ERP attendance integration module."""

from datetime import date as date_cls
from datetime import datetime, timedelta
from backend.config import (
    ERP_FETCH_MODE,
    ERP_MAIN_CONFIG,
    ATTENDANCE_TABLE,
    ATTENDANCE_COLUMNS,
    CAMERA_CONFIG,
)
from backend.services.erp_http_client import ERPServiceError, erp_http_client
import logging

logger = logging.getLogger(__name__)


class ERPAttendanceIntegration:
    def __init__(self):
        self.config = ERP_MAIN_CONFIG
        self.table = ATTENDANCE_TABLE
        self.columns = ATTENDANCE_COLUMNS
        self.camera_ip = CAMERA_CONFIG['ip']
        self.default_source = CAMERA_CONFIG['default_source']
        self.http_mode = ERP_FETCH_MODE == 'http_service'
        self.last_error_message = ''

    @staticmethod
    def _normalize_date_text(value, fallback):
        text = str(value or '').strip()
        if not text:
            return fallback
        try:
            return datetime.strptime(text, '%Y-%m-%d').date()
        except ValueError:
            return fallback

    @staticmethod
    def _format_date_value(value):
        if hasattr(value, 'strftime'):
            try:
                return value.strftime('%Y-%m-%d')
            except Exception:
                return str(value)
        return str(value or '')

    @staticmethod
    def _format_time_value(value):
        if hasattr(value, 'strftime'):
            try:
                return value.strftime('%H:%M:%S')
            except Exception:
                return str(value)
        return str(value or '')

    def _set_last_error(self, message=''):
        self.last_error_message = str(message or '').strip()

    def get_connection(self):
        if self.http_mode:
            return None
        try:
            import mysql.connector
            return mysql.connector.connect(**self.config)
        except Exception as e:
            self._set_last_error(e)
            logger.error(f"ERP DB connection error: {e}")
            return None

    def test_connection(self):
        if self.http_mode:
            try:
                erp_http_client.get_online_attendance({'page': 1, 'page_size': 1})
                self._set_last_error('')
                return True
            except ERPServiceError as exc:
                self._set_last_error(exc.message)
                logger.error(f"ERP HTTP attendance test error: {exc.message}")
                return False
            except Exception as exc:
                self._set_last_error(exc)
                logger.error(f"ERP HTTP attendance test error: {exc}")
                return False
        try:
            conn = self.get_connection()
            if conn:
                cursor = conn.cursor()
                cursor.execute("SELECT VERSION()")
                version = cursor.fetchone()
                cursor.close()
                conn.close()
                self._set_last_error('')
                return True
            return False
        except Exception as e:
            self._set_last_error(e)
            logger.error(f"ERP connection test error: {e}")
            return False

    def check_table_exists(self):
        if self.http_mode:
            try:
                erp_http_client.get_online_attendance({'page': 1, 'page_size': 1})
                self._set_last_error('')
                return True
            except ERPServiceError as exc:
                self._set_last_error(exc.message)
                logger.error(f"ERP attendance HTTP table check error: {exc.message}")
                return False
            except Exception as exc:
                self._set_last_error(exc)
                logger.error(f"ERP attendance HTTP table check error: {exc}")
                return False
        try:
            conn = self.get_connection()
            if not conn:
                return False
            cursor = conn.cursor()
            cursor.execute(f"SHOW TABLES LIKE '{self.table}'")
            result = cursor.fetchone()
            cursor.close()
            conn.close()
            self._set_last_error('')
            return result is not None
        except Exception as e:
            self._set_last_error(e)
            logger.error(f"Table check error: {e}")
            return False

    def create_attendance_record(self, employee_id, attendance_time=None, attendance_code=None, auth=None):
        if self.http_mode:
            try:
                erp_http_client.push_attendance_record(
                    employee_id=employee_id,
                    attendance_time=attendance_time,
                    attendance_code=attendance_code,
                    source=self.default_source,
                    camera_ip=self.camera_ip,
                    auth=auth,
                )
                self._set_last_error('')
                return True
            except ERPServiceError as exc:
                self._set_last_error(exc.message)
                logger.error(f"ERP attendance HTTP record error: {exc.message}")
                return False
            except Exception as exc:
                self._set_last_error(exc)
                logger.error(f"ERP attendance HTTP record error: {exc}")
                return False
        try:
            if not attendance_time:
                attendance_time = datetime.now()
            attendance_date = attendance_time.strftime('%Y-%m-%d')
            attendance_time_str = attendance_time.strftime('%H:%M:%S')

            conn = self.get_connection()
            if not conn:
                return False

            cursor = conn.cursor()
            attendance_type = str(attendance_code or '').strip().upper() or 'IN'
            if attendance_type not in {'IN', 'OUT'}:
                attendance_type = 'IN'
            query = f"""
                INSERT INTO {self.table} (
                    {self.columns['employee_id']},
                    {self.columns['date']},
                    {self.columns['time']},
                    {self.columns['type']},
                    {self.columns['source']},
                    {self.columns['camera_ip']}
                ) VALUES (%s, %s, %s, %s, %s, %s)
            """
            values = (
                employee_id,
                attendance_date,
                attendance_time_str,
                attendance_type,
                self.default_source,
                self.camera_ip
            )
            cursor.execute(query, values)
            conn.commit()
            cursor.close()
            conn.close()
            self._set_last_error('')
            return True
        except Exception as e:
            self._set_last_error(e)
            logger.error(f"ERP attendance record error: {e}")
            return False

    def get_attendance_history(self, employee_id, days=7, auth=None):
        if self.http_mode:
            try:
                employee_id = str(employee_id or '').strip()
                normalized_days = max(1, int(days or 1))
                today = date_cls.today()
                start_date = today - timedelta(days=max(0, normalized_days - 1))

                payload = erp_http_client.get_online_attendance(
                    {
                        'start_date': start_date.strftime('%Y-%m-%d'),
                        'end_date': today.strftime('%Y-%m-%d'),
                        'employee_id': employee_id,
                        'sort_by': 'date',
                        'sort_dir': 'desc',
                        'page': 1,
                        'page_size': max(20, min(500, normalized_days * 50)),
                    },
                    auth=auth,
                )

                records = []
                for item in payload.get('records') or []:
                    if not isinstance(item, dict):
                        continue
                    row_employee_id = str(item.get('employee_id') or '').strip()
                    if employee_id and row_employee_id and row_employee_id != employee_id:
                        continue
                    records.append({
                        'employee_id': row_employee_id,
                        'date': str(item.get('attendance_date') or '').strip(),
                        'time': str(item.get('attendance_time') or '').strip(),
                        'type': str(item.get('attendance_type') or '').strip(),
                        'source': str(item.get('source') or '').strip(),
                        'camera_ip': str(item.get('camera_ip') or '').strip(),
                    })
                self._set_last_error('')
                return records
            except ERPServiceError as exc:
                self._set_last_error(exc.message)
                logger.error(f"ERP attendance history HTTP error: {exc.message}")
                return []
            except Exception as exc:
                self._set_last_error(exc)
                logger.error(f"ERP attendance history HTTP error: {exc}")
                return []
        try:
            conn = self.get_connection()
            if not conn:
                return []
            cursor = conn.cursor(dictionary=True)
            query = f"""
                SELECT
                    {self.columns['employee_id']} as employee_id,
                    {self.columns['date']} as date,
                    {self.columns['time']} as time,
                    {self.columns['type']} as type,
                    {self.columns['source']} as source,
                    {self.columns['camera_ip']} as camera_ip
                FROM {self.table}
                WHERE {self.columns['employee_id']} = %s
                  AND {self.columns['date']} >= DATE_SUB(CURDATE(), INTERVAL %s DAY)
                ORDER BY {self.columns['date']} DESC, {self.columns['time']} DESC
            """
            cursor.execute(query, (employee_id, days))
            results = cursor.fetchall()
            cursor.close()
            conn.close()
            self._set_last_error('')
            return results
        except Exception as e:
            self._set_last_error(e)
            logger.error(f"ERP attendance history error: {e}")
            return []

    def check_recent_attendance(self, employee_id, minutes=10, auth=None):
        if self.http_mode:
            try:
                exists = erp_http_client.check_recent_attendance(
                    employee_id=employee_id,
                    minutes=minutes,
                    auth=auth,
                )
                self._set_last_error('')
                return bool(exists)
            except ERPServiceError as exc:
                self._set_last_error(exc.message)
                logger.error(f"ERP recent attendance HTTP check error: {exc.message}")
                return False
            except Exception as exc:
                self._set_last_error(exc)
                logger.error(f"ERP recent attendance HTTP check error: {exc}")
                return False
        try:
            conn = self.get_connection()
            if not conn:
                return False
            cursor = conn.cursor()
            cutoff_time = datetime.now() - timedelta(minutes=minutes)
            cutoff_str = cutoff_time.strftime('%Y-%m-%d %H:%M:%S')
            query = f"""
                SELECT COUNT(*) as count
                FROM {self.table}
                WHERE {self.columns['employee_id']} = %s
                  AND CONCAT({self.columns['date']}, ' ', {self.columns['time']}) >= %s
            """
            cursor.execute(query, (employee_id, cutoff_str))
            result = cursor.fetchone()
            cursor.close()
            conn.close()
            if result and result[0] > 0:
                self._set_last_error('')
                return True
            self._set_last_error('')
            return False
        except Exception as e:
            self._set_last_error(e)
            logger.error(f"ERP recent attendance check error: {e}")
            return False

    def list_online_attendance(self, filters=None, auth=None):
        filters = filters if isinstance(filters, dict) else {}

        if self.http_mode:
            payload = erp_http_client.get_online_attendance(filters, auth=auth)
            self._set_last_error('')
            return {
                'records': payload.get('records') or [],
                'meta': payload.get('meta') or {},
                'filters': payload.get('filters') or {},
            }

        today = date_cls.today()
        start_date = self._normalize_date_text(filters.get('start_date'), today)
        end_date = self._normalize_date_text(filters.get('end_date'), today)
        if end_date < start_date:
            start_date, end_date = end_date, start_date

        employee_id = str(filters.get('employee_id') or '').strip()
        keyword = str(filters.get('keyword') or '').strip()
        attendance_type = str(filters.get('attendance_type') or '').strip().upper()
        if attendance_type not in {'IN', 'OUT'}:
            attendance_type = ''

        sort_map = {
            'date': self.columns['date'],
            'attendance_date': self.columns['date'],
            'time': self.columns['time'],
            'attendance_time': self.columns['time'],
            'employee_id': self.columns['employee_id'],
            'attendance_type': self.columns['type'],
            'status': self.columns['type'],
            'source': self.columns['source'],
            'camera_ip': self.columns['camera_ip'],
        }
        sort_by = sort_map.get(str(filters.get('sort_by') or '').strip().lower(), self.columns['date'])
        sort_dir = 'ASC' if str(filters.get('sort_dir') or '').strip().lower() == 'asc' else 'DESC'

        try:
            page = int(filters.get('page') or 1)
        except (TypeError, ValueError):
            page = 1
        try:
            page_size = int(filters.get('page_size') or 50)
        except (TypeError, ValueError):
            page_size = 50

        page = max(1, page)
        page_size = max(1, min(500, page_size))
        offset = (page - 1) * page_size

        where_clauses = [
            f"{self.columns['date']} >= %s",
            f"{self.columns['date']} <= %s",
        ]
        params = [start_date.strftime('%Y-%m-%d'), end_date.strftime('%Y-%m-%d')]

        if employee_id:
            where_clauses.append(f"{self.columns['employee_id']} = %s")
            params.append(employee_id)

        if attendance_type:
            where_clauses.append(f"UPPER({self.columns['type']}) = %s")
            params.append(attendance_type)

        if keyword:
            like_keyword = f"%{keyword}%"
            where_clauses.append(
                "("
                f"{self.columns['employee_id']} LIKE %s OR "
                f"{self.columns['type']} LIKE %s OR "
                f"{self.columns['source']} LIKE %s OR "
                f"{self.columns['camera_ip']} LIKE %s"
                ")"
            )
            params.extend([like_keyword, like_keyword, like_keyword, like_keyword])

        where_sql = ' AND '.join(where_clauses)

        conn = None
        cursor = None
        try:
            import mysql.connector

            conn = mysql.connector.connect(**self.config)
            cursor = conn.cursor(dictionary=True)

            count_sql = f"SELECT COUNT(*) AS total FROM {self.table} WHERE {where_sql}"
            cursor.execute(count_sql, tuple(params))
            count_row = cursor.fetchone() or {}
            total = int(count_row.get('total') or 0)

            list_sql = (
                f"SELECT {self.columns['employee_id']} AS employee_id, "
                f"{self.columns['date']} AS attendance_date, "
                f"{self.columns['time']} AS attendance_time, "
                f"{self.columns['type']} AS attendance_type, "
                f"{self.columns['source']} AS source, "
                f"{self.columns['camera_ip']} AS camera_ip "
                f"FROM {self.table} "
                f"WHERE {where_sql} "
                f"ORDER BY {sort_by} {sort_dir}, {self.columns['time']} {sort_dir} "
                f"LIMIT %s OFFSET %s"
            )
            list_params = list(params) + [page_size, offset]
            cursor.execute(list_sql, tuple(list_params))
            rows = cursor.fetchall() or []

            records = []
            for row in rows:
                if not isinstance(row, dict):
                    continue
                records.append({
                    'employee_id': str(row.get('employee_id') or '').strip(),
                    'attendance_date': self._format_date_value(row.get('attendance_date')),
                    'attendance_time': self._format_time_value(row.get('attendance_time')),
                    'attendance_type': str(row.get('attendance_type') or '').strip().upper(),
                    'source': str(row.get('source') or '').strip(),
                    'camera_ip': str(row.get('camera_ip') or '').strip(),
                })

            self._set_last_error('')
            return {
                'records': records,
                'meta': {
                    'page': page,
                    'page_size': page_size,
                    'total': total,
                    'total_pages': max(1, int((total + page_size - 1) / page_size)),
                },
                'filters': {
                    'start_date': start_date.strftime('%Y-%m-%d'),
                    'end_date': end_date.strftime('%Y-%m-%d'),
                    'employee_id': employee_id,
                    'attendance_type': attendance_type or 'all',
                    'keyword': keyword,
                    'sort_by': str(filters.get('sort_by') or 'date').strip().lower() or 'date',
                    'sort_dir': 'asc' if sort_dir == 'ASC' else 'desc',
                },
            }
        except Exception as exc:
            self._set_last_error(exc)
            logger.error(f"ERP online attendance list error: {exc}")
            return {
                'records': [],
                'meta': {
                    'page': page,
                    'page_size': page_size,
                    'total': 0,
                    'total_pages': 1,
                },
                'filters': {
                    'start_date': start_date.strftime('%Y-%m-%d'),
                    'end_date': end_date.strftime('%Y-%m-%d'),
                    'employee_id': employee_id,
                    'attendance_type': attendance_type or 'all',
                    'keyword': keyword,
                    'sort_by': str(filters.get('sort_by') or 'date').strip().lower() or 'date',
                    'sort_dir': 'asc' if sort_dir == 'ASC' else 'desc',
                },
            }
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


erp_attendance = ERPAttendanceIntegration()
