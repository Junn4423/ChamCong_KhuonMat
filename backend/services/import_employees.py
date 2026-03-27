# -*- coding: utf-8 -*-
"""ERP bulk import module."""

import mysql.connector
import os
import io
import tempfile

from backend.models.database import db, User
from backend.models.face_recognition_module import FaceRecognition
from backend.config import (
    ERP_MAIN_CONFIG, ERP_DOCS_CONFIG,
    EMPLOYEE_TABLE, EMPLOYEE_COLUMNS,
    IMAGE_TABLE, IMAGE_COLUMNS,
    IMPORT_CONFIG
)


class ERPImporter:
    def __init__(self, face_recognizer=None):
        self.erp_main_config = ERP_MAIN_CONFIG
        self.erp_docs_config = ERP_DOCS_CONFIG
        if face_recognizer:
            self.face_recognizer = face_recognizer
        else:
            self.face_recognizer = FaceRecognition(det_thresh=0.1, det_size=(640, 640))
        self.imported_count = 0
        self.skipped_count = 0
        self.error_count = 0

    def connect_to_erp_main(self):
        try:
            return mysql.connector.connect(**self.erp_main_config)
        except mysql.connector.Error as e:
            print(f"ERP main DB error: {e}")
            return None

    def connect_to_erp_docs(self):
        try:
            return mysql.connector.connect(**self.erp_docs_config)
        except mysql.connector.Error as e:
            print(f"ERP docs DB error: {e}")
            return None

    def get_employees_from_erp(self):
        conn = self.connect_to_erp_main()
        if not conn:
            return []
        try:
            cursor = conn.cursor(dictionary=True)
            query = f"""
                SELECT
                    {EMPLOYEE_COLUMNS['employee_id']} as employee_id,
                    {EMPLOYEE_COLUMNS['name']} as name,
                    {EMPLOYEE_COLUMNS['department']} as department,
                    {EMPLOYEE_COLUMNS['position']} as position
                FROM {EMPLOYEE_TABLE}
                WHERE {EMPLOYEE_COLUMNS['employee_id']} IS NOT NULL
                  AND {EMPLOYEE_COLUMNS['name']} IS NOT NULL
                  AND {EMPLOYEE_COLUMNS['employee_id']} != ''
                  AND {EMPLOYEE_COLUMNS['name']} != ''
            """
            cursor.execute(query)
            employees = cursor.fetchall()
            return employees
        except mysql.connector.Error as e:
            print(f"Employee query error: {e}")
            return []
        finally:
            conn.close()

    def get_employee_image(self, employee_id):
        conn = self.connect_to_erp_docs()
        if not conn:
            return None
        try:
            cursor = conn.cursor()
            query = f"""
                SELECT {IMAGE_COLUMNS['image_blob']}
                FROM {IMAGE_TABLE}
                WHERE {IMAGE_COLUMNS['employee_id']} = %s
                  AND {IMAGE_COLUMNS['image_blob']} IS NOT NULL
                LIMIT 1
            """
            cursor.execute(query, (employee_id,))
            result = cursor.fetchone()
            if result and len(result) > 0 and result[0] is not None:
                return result[0]
            return None
        except mysql.connector.Error as e:
            print(f"Image query error for {employee_id}: {e}")
            return None
        finally:
            conn.close()

    def blob_to_face_encoding(self, blob_data):
        try:
            with tempfile.NamedTemporaryFile(suffix='.jpg', delete=False) as temp_file:
                temp_file.write(blob_data)
                temp_file_path = temp_file.name
            try:
                with open(temp_file_path, 'rb') as image_file:
                    face_encoding, error = self.face_recognizer.encode_face_from_image(image_file)
                    return face_encoding, error
            finally:
                os.unlink(temp_file_path)
        except Exception as e:
            return None, f"Image processing error: {str(e)}"

    def import_employee(self, employee_data):
        employee_id = employee_data['employee_id']
        name = employee_data['name']
        department = employee_data.get('department', '')
        position = employee_data.get('position', '')

        if IMPORT_CONFIG['skip_existing']:
            existing = User.query.filter_by(employee_id=employee_id).first()
            if existing:
                self.skipped_count += 1
                return False

        image_blob = self.get_employee_image(employee_id)
        if not image_blob:
            if IMPORT_CONFIG['require_image']:
                self.error_count += 1
                return False

        face_encoding = None
        if image_blob:
            face_encoding, error = self.blob_to_face_encoding(image_blob)
            if error:
                self.error_count += 1
                return False

        if face_encoding is None:
            self.error_count += 1
            return False

        new_user = User(
            name=name,
            employee_id=employee_id,
            department=department,
            position=position,
            face_encoding=face_encoding
        )
        db.session.add(new_user)
        db.session.commit()
        self.imported_count += 1
        return True

    def import_all_employees(self):
        employees = self.get_employees_from_erp()
        if not employees:
            return {'imported': 0, 'skipped': 0, 'errors': 0, 'total': 0}

        self.imported_count = 0
        self.skipped_count = 0
        self.error_count = 0

        for emp in employees:
            try:
                self.import_employee(emp)
            except Exception as e:
                print(f"Import error for {emp.get('employee_id')}: {e}")
                self.error_count += 1

        return {
            'imported': self.imported_count,
            'skipped': self.skipped_count,
            'errors': self.error_count,
            'total': len(employees)
        }
