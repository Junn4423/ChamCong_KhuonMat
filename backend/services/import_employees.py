# -*- coding: utf-8 -*-
"""ERP bulk import module backed by HTTP services."""

import io

from backend.face_encoding_utils import normalize_face_encodings
from backend.models.database import db, User
from backend.services.erp_http_client import ERPServiceError, erp_http_client

try:  # Optional when AI dependencies are not installed.
    from backend.models.face_recognition_module import FaceRecognition
except Exception:  # pragma: no cover
    FaceRecognition = None


class ERPImporter:
    def __init__(self, face_recognizer=None, erp_client=None, save_image_callback=None):
        self.erp_client = erp_client or erp_http_client
        if face_recognizer is not None:
            self.face_recognizer = face_recognizer
        elif FaceRecognition is not None:
            self.face_recognizer = FaceRecognition(det_thresh=0.1, det_size=(640, 640))
        else:
            self.face_recognizer = None
        self.save_image_callback = save_image_callback
        self.imported_count = 0
        self.updated_count = 0
        self.skipped_count = 0
        self.error_count = 0
        self.without_face_count = 0

    def get_employees_from_erp(self, auth):
        return self.erp_client.list_employees(auth)

    def get_employee_image(self, auth, employee_data):
        image_data = self.erp_client.get_employee_profile_image(
            auth,
            employee_data['employee_id'],
            employee=employee_data,
        )
        return image_data['bytes']

    def blob_to_face_encoding(self, blob_data):
        try:
            if self.face_recognizer is None:
                return None, 'Face recognizer not available'
            image_file = io.BytesIO(blob_data)
            face_encoding, error = self.face_recognizer.encode_face_from_image(image_file)
            return face_encoding, error
        except Exception as e:
            return None, f"Image processing error: {str(e)}"

    @staticmethod
    def _normalized(value):
        return str(value or '').strip()

    def import_employee(self, auth, employee_data, overwrite_existing=False):
        employee_id = self._normalized(employee_data.get('employee_id'))
        name = self._normalized(employee_data.get('name'))
        department = self._normalized(employee_data.get('department'))
        position = self._normalized(employee_data.get('position'))

        if not employee_id:
            self.error_count += 1
            return False

        existing = User.query.filter_by(employee_id=employee_id).first()
        if existing and not overwrite_existing:
            self.skipped_count += 1
            return False

        image_blob = None
        face_encoding_value = []

        try:
            image_blob = self.get_employee_image(auth, employee_data)
        except ERPServiceError:
            image_blob = None

        if image_blob:
            face_encoding, error = self.blob_to_face_encoding(image_blob)
            if not error and face_encoding is not None:
                face_encoding_value = normalize_face_encodings(face_encoding)
            else:
                self.without_face_count += 1
        else:
            self.without_face_count += 1

        if existing:
            existing.name = name or employee_id
            existing.department = department
            existing.position = position
            if overwrite_existing:
                # Overwrite mode replaces face data completely to mirror ERP source.
                existing.face_encoding = face_encoding_value if face_encoding_value else []
            elif face_encoding_value:
                existing.face_encoding = face_encoding_value
        else:
            new_user = User(
                name=name or employee_id,
                employee_id=employee_id,
                department=department,
                position=position,
                face_encoding=face_encoding_value,
            )
            db.session.add(new_user)

        db.session.commit()
        if self.save_image_callback and image_blob:
            self.save_image_callback(employee_id, image_blob, employee_data)

        if existing:
            self.updated_count += 1
        else:
            self.imported_count += 1
        return True

    def import_all_employees(self, auth, employee_ids=None, overwrite_existing=False):
        employees = self.get_employees_from_erp(auth)

        requested_ids = []
        if isinstance(employee_ids, (list, tuple, set)):
            requested_ids = [self._normalized(item) for item in employee_ids if self._normalized(item)]

        if requested_ids:
            selected_upper = {emp_id.upper() for emp_id in requested_ids}
            employees = [
                emp for emp in employees
                if self._normalized(emp.get('employee_id')).upper() in selected_upper
            ]

        if not employees:
            return {
                'imported': 0,
                'updated': 0,
                'skipped': 0,
                'errors': 0,
                'without_face': 0,
                'total': 0,
                'requested': len(requested_ids),
                'overwrite_existing': bool(overwrite_existing),
            }

        self.imported_count = 0
        self.updated_count = 0
        self.skipped_count = 0
        self.error_count = 0
        self.without_face_count = 0

        for emp in employees:
            try:
                self.import_employee(auth, emp, overwrite_existing=overwrite_existing)
            except Exception:
                db.session.rollback()
                self.error_count += 1

        return {
            'imported': self.imported_count,
            'updated': self.updated_count,
            'skipped': self.skipped_count,
            'errors': self.error_count,
            'without_face': self.without_face_count,
            'total': len(employees),
            'requested': len(requested_ids) if requested_ids else len(employees),
            'overwrite_existing': bool(overwrite_existing),
        }
