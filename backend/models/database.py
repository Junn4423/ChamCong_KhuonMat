from flask_sqlalchemy import SQLAlchemy
from datetime import datetime
import pickle
import numpy as np

db = SQLAlchemy()


class NumpyCompatUnpickler(pickle.Unpickler):
    """Handle numpy version mismatches when unpickling face encodings."""
    def find_class(self, module, name):
        # numpy 2.x -> 1.x compatibility
        if module == 'numpy._core.numeric':
            module = 'numpy.core.numeric'
        elif module == 'numpy._core.multiarray':
            module = 'numpy.core.multiarray'
        elif module.startswith('numpy._core'):
            module = module.replace('numpy._core', 'numpy.core', 1)
        return super().find_class(module, name)


class NumpyPickleType(db.TypeDecorator):
    """PickleType that handles numpy version compatibility."""
    impl = db.LargeBinary
    cache_ok = True

    def process_bind_param(self, value, dialect):
        if value is not None:
            return pickle.dumps(value, protocol=pickle.HIGHEST_PROTOCOL)
        return None

    def process_result_value(self, value, dialect):
        if value is not None:
            try:
                return pickle.loads(value)
            except ModuleNotFoundError:
                return NumpyCompatUnpickler(
                    __import__('io').BytesIO(value)
                ).load()
        return None


class User(db.Model):
    __tablename__ = 'users'

    id = db.Column(db.Integer, primary_key=True)
    name = db.Column(db.String(100), nullable=False)
    employee_id = db.Column(db.String(50), unique=True, nullable=False)
    department = db.Column(db.String(100))
    position = db.Column(db.String(100))
    face_encoding = db.Column(NumpyPickleType, nullable=False)
    created_at = db.Column(db.DateTime, default=datetime.utcnow)

    attendances = db.relationship('Attendance', backref='user', lazy=True)

    def __repr__(self):
        return f'<User {self.name}>'


class Attendance(db.Model):
    __tablename__ = 'attendance'

    id = db.Column(db.Integer, primary_key=True)
    user_id = db.Column(db.Integer, db.ForeignKey('users.id'), nullable=False)
    check_in_time = db.Column(db.DateTime, nullable=False)
    check_out_time = db.Column(db.DateTime)
    date = db.Column(db.Date, nullable=False)
    status = db.Column(db.String(20), default='present')

    def __repr__(self):
        return f'<Attendance {self.user_id} - {self.date}>'


class AttendanceLocation(db.Model):
    __tablename__ = 'attendance_locations'

    id = db.Column(db.Integer, primary_key=True)
    attendance_id = db.Column(db.Integer, db.ForeignKey('attendance.id'), nullable=False, index=True)
    latitude = db.Column(db.Float)
    longitude = db.Column(db.Float)
    accuracy = db.Column(db.Float)
    label = db.Column(db.String(255))
    source = db.Column(db.String(32), default='client')
    raw = db.Column(db.Text)
    captured_at = db.Column(db.DateTime, default=datetime.utcnow, nullable=False)

    attendance = db.relationship(
        'Attendance',
        backref=db.backref('locations', lazy=True, cascade='all, delete-orphan'),
    )

    def __repr__(self):
        return f'<AttendanceLocation attendance={self.attendance_id} lat={self.latitude} lng={self.longitude}>'
