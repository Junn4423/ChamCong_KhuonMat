# -*- coding: utf-8 -*-
"""
Module tích hợp với hệ thống ERP
Ghi dữ liệu chấm công vào bảng tc_lv0012
"""

import mysql.connector
from datetime import datetime, date
from backend.config import ERP_MAIN_CONFIG, ATTENDANCE_TABLE, ATTENDANCE_COLUMNS, CAMERA_CONFIG
import logging

# Thiết lập logging
logging.basicConfig(level=logging.INFO)
logger = logging.getLogger(__name__)

class ERPAttendanceIntegration:
    """Lớp tích hợp chấm công với ERP"""
    
    def __init__(self):
        self.config = ERP_MAIN_CONFIG
        self.table = ATTENDANCE_TABLE
        self.columns = ATTENDANCE_COLUMNS
        self.camera_ip = CAMERA_CONFIG['ip']
        self.default_source = CAMERA_CONFIG['default_source']
        
    def get_connection(self):
        """Tạo kết nối đến database ERP"""
        try:
            connection = mysql.connector.connect(**self.config)
            return connection
        except mysql.connector.Error as e:
            logger.error(f"Lỗi kết nối database ERP: {e}")
            return None
    
    def test_connection(self):
        """Test kết nối database ERP"""
        try:
            conn = self.get_connection()
            if conn:
                cursor = conn.cursor()
                cursor.execute("SELECT VERSION()")
                version = cursor.fetchone()
                cursor.close()
                conn.close()
                if version and len(version) > 0:
                    logger.info(f"Kết nối ERP thành công. MySQL version: {version[0]}")
                return True
            return False
        except Exception as e:
            logger.error(f"Lỗi test kết nối ERP: {e}")
            return False
    
    def check_table_exists(self):
        """Kiểm tra bảng tc_lv0012 có tồn tại không"""
        try:
            conn = self.get_connection()
            if not conn:
                return False
                
            cursor = conn.cursor()
            cursor.execute(f"SHOW TABLES LIKE '{self.table}'")
            result = cursor.fetchone()
            cursor.close()
            conn.close()
            
            exists = result is not None
            if exists:
                logger.info(f"Bảng {self.table} tồn tại trong database ERP")
            else:
                logger.warning(f"Bảng {self.table} không tồn tại trong database ERP")
            return exists
            
        except Exception as e:
            logger.error(f"Lỗi kiểm tra bảng: {e}")
            return False
    
    def create_attendance_record(self, employee_id, attendance_time=None, attendance_code=None):
        """
        Tạo bản ghi chấm công trong bảng tc_lv0012
        Args:
            employee_id (str): Mã nhân viên
            attendance_time (datetime, optional): Thời gian chấm công. Mặc định là hiện tại
            attendance_code (str, optional): Mã attendance_code để ghi vào lv199
        Returns:
            bool: True nếu thành công, False nếu có lỗi
        """
        try:
            if not attendance_time:
                attendance_time = datetime.now()
            attendance_date = attendance_time.strftime('%Y-%m-%d')
            attendance_time_str = attendance_time.strftime('%H:%M:%S')
            conn = self.get_connection()
            if not conn:
                logger.error("Không thể kết nối database ERP")
                return False
            cursor = conn.cursor()
            # Tạo query INSERT có thêm lv199
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
            logger.info(f"Đã ghi chấm công ERP cho nhân viên {employee_id} vào {attendance_date} {attendance_time_str} với mã {attendance_code}")
            cursor.close()
            conn.close()
            return True
        except mysql.connector.Error as e:
            logger.error(f"Lỗi MySQL khi ghi chấm công ERP: {e}")
            return False
        except Exception as e:
            logger.error(f"Lỗi chung khi ghi chấm công ERP: {e}")
            return False
    
    def get_attendance_history(self, employee_id, days=7):
        """
        Lấy lịch sử chấm công của nhân viên từ ERP
        
        Args:
            employee_id (str): Mã nhân viên
            days (int): Số ngày gần đây (mặc định 7 ngày)
        
        Returns:
            list: Danh sách bản ghi chấm công
        """
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
            logger.error(f"Lỗi lấy lịch sử chấm công ERP: {e}")
            return []
    
    def check_recent_attendance(self, employee_id, minutes=10):
        """
        Kiểm tra xem nhân viên đã chấm công trong vòng X phút gần đây chưa
        
        Args:
            employee_id (str): Mã nhân viên
            minutes (int): Số phút kiểm tra (mặc định 10 phút)
        
        Returns:
            bool: True nếu đã chấm công gần đây
        """
        try:
            conn = self.get_connection()
            if not conn:
                return False
            
            cursor = conn.cursor()
            
            # Calculate cutoff time in Python to avoid MySQL timezone issues
            from datetime import timedelta
            cutoff_time = datetime.now() - timedelta(minutes=minutes)
            cutoff_str = cutoff_time.strftime('%Y-%m-%d %H:%M:%S')
            
            # Use STR_TO_DATE to ensure correct comparison
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
            
            return (result and len(result) > 0 and result[0] > 0) if result else False
            
        except Exception as e:
            logger.error(f"Lỗi kiểm tra chấm công gần đây: {e}")
            return False

# Instance toàn cục để sử dụng
erp_attendance = ERPAttendanceIntegration() 