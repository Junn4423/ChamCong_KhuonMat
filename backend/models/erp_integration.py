# -*- coding: utf-8 -*-
"""ERP attendance integration module."""

import mysql.connector
from datetime import datetime, timedelta
from backend.config import ERP_MAIN_CONFIG, ATTENDANCE_TABLE, ATTENDANCE_COLUMNS, CAMERA_CONFIG
import logging

logger = logging.getLogger(__name__)


class ERPAttendanceIntegration:
    def __init__(self):
        self.config = ERP_MAIN_CONFIG
        self.table = ATTENDANCE_TABLE
        self.columns = ATTENDANCE_COLUMNS
        self.camera_ip = CAMERA_CONFIG['ip']
        self.default_source = CAMERA_CONFIG['default_source']

    def get_connection(self):
        try:
            return mysql.connector.connect(**self.config)
        except mysql.connector.Error as e:
            logger.error(f"ERP DB connection error: {e}")
            return None

    def test_connection(self):
        try:
            conn = self.get_connection()
            if conn:
                cursor = conn.cursor()
                cursor.execute("SELECT VERSION()")
                version = cursor.fetchone()
                cursor.close()
                conn.close()
                return True
            return False
        except Exception as e:
            logger.error(f"ERP connection test error: {e}")
            return False

    def check_table_exists(self):
        try:
            conn = self.get_connection()
            if not conn:
                return False
            cursor = conn.cursor()
            cursor.execute(f"SHOW TABLES LIKE '{self.table}'")
            result = cursor.fetchone()
            cursor.close()
            conn.close()
            return result is not None
        except Exception as e:
            logger.error(f"Table check error: {e}")
            return False

    def create_attendance_record(self, employee_id, attendance_time=None, attendance_code=None):
        try:
            if not attendance_time:
                attendance_time = datetime.now()
            attendance_date = attendance_time.strftime('%Y-%m-%d')
            attendance_time_str = attendance_time.strftime('%H:%M:%S')

            conn = self.get_connection()
            if not conn:
                return False

            cursor = conn.cursor()
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
                'IN',
                self.default_source,
                self.camera_ip
            )
            cursor.execute(query, values)
            conn.commit()
            cursor.close()
            conn.close()
            return True
        except Exception as e:
            logger.error(f"ERP attendance record error: {e}")
            return False

    def get_attendance_history(self, employee_id, days=7):
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
            return results
        except Exception as e:
            logger.error(f"ERP attendance history error: {e}")
            return []

    def check_recent_attendance(self, employee_id, minutes=10):
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
                return True
            return False
        except Exception as e:
            logger.error(f"ERP recent attendance check error: {e}")
            return False


erp_attendance = ERPAttendanceIntegration()
