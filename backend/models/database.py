from flask_sqlalchemy import SQLAlchemy
from datetime import datetime
import pickle
import numpy as np
from sqlalchemy import inspect, text

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
    employee_account = db.relationship('EmployeeAccount', backref='user', uselist=False, lazy=True)

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


class EmployeeImage(db.Model):
    __tablename__ = 'employee_images'

    id = db.Column(db.Integer, primary_key=True)
    employee_id = db.Column(db.String(50), unique=True, nullable=False, index=True)
    image_token = db.Column(db.String(96), unique=True, nullable=False, index=True)
    erp_image_token = db.Column(db.String(128), index=True)
    image_blob = db.Column(db.LargeBinary)
    mime_type = db.Column(db.String(64), default='image/jpeg')
    source = db.Column(db.String(32), default='local')
    updated_at = db.Column(db.DateTime, default=datetime.utcnow, onupdate=datetime.utcnow)

    def __repr__(self):
        return f'<EmployeeImage employee_id={self.employee_id} token={self.image_token}>'


class EmployeeAccount(db.Model):
    __tablename__ = 'employee_accounts'

    id = db.Column(db.Integer, primary_key=True)
    user_id = db.Column(db.Integer, db.ForeignKey('users.id'), unique=True, nullable=False, index=True)
    username = db.Column(db.String(50), unique=True, nullable=False, index=True)
    password_hash_md5 = db.Column(db.String(32), nullable=False)
    is_active = db.Column(db.Boolean, nullable=False, default=True)
    is_locked = db.Column(db.Boolean, nullable=False, default=False)
    failed_attempts = db.Column(db.Integer, nullable=False, default=0)
    last_login_at = db.Column(db.DateTime)
    synced_at = db.Column(db.DateTime)
    created_at = db.Column(db.DateTime, default=datetime.utcnow, nullable=False)
    updated_at = db.Column(db.DateTime, default=datetime.utcnow, onupdate=datetime.utcnow, nullable=False)

    def __repr__(self):
        return f'<EmployeeAccount username={self.username} employee_id={self.user.employee_id if self.user else ""}>'


class SystemSetting(db.Model):
    __tablename__ = 'system_settings'

    key = db.Column(db.String(100), primary_key=True)
    value = db.Column(db.Text, nullable=False)
    updated_at = db.Column(db.DateTime, default=datetime.utcnow, onupdate=datetime.utcnow)

    def __repr__(self):
        return f'<SystemSetting key={self.key}>'


def ensure_database_schema():
    inspector = inspect(db.engine)
    table_names = set(inspector.get_table_names())

    with db.engine.begin() as connection:
        if 'employee_images' in table_names:
            columns = {column['name'] for column in inspector.get_columns('employee_images')}
            if 'erp_image_token' not in columns:
                connection.execute(text('ALTER TABLE employee_images ADD COLUMN erp_image_token VARCHAR(128)'))

        if 'employee_accounts' in table_names:
            account_columns = {column['name'] for column in inspector.get_columns('employee_accounts')}

            if 'password_hash_md5' not in account_columns:
                connection.execute(text('ALTER TABLE employee_accounts ADD COLUMN password_hash_md5 VARCHAR(32)'))
                if 'password_hash' in account_columns:
                    connection.execute(text(
                        "UPDATE employee_accounts "
                        "SET password_hash_md5 = lower(trim(password_hash)) "
                        "WHERE (password_hash_md5 IS NULL OR trim(password_hash_md5) = '') "
                        "AND password_hash IS NOT NULL"
                    ))

            if 'is_active' not in account_columns:
                connection.execute(text('ALTER TABLE employee_accounts ADD COLUMN is_active BOOLEAN'))
                connection.execute(text('UPDATE employee_accounts SET is_active = 1 WHERE is_active IS NULL'))

            if 'is_locked' not in account_columns:
                connection.execute(text('ALTER TABLE employee_accounts ADD COLUMN is_locked BOOLEAN'))
                connection.execute(text('UPDATE employee_accounts SET is_locked = 0 WHERE is_locked IS NULL'))

            if 'failed_attempts' not in account_columns:
                connection.execute(text('ALTER TABLE employee_accounts ADD COLUMN failed_attempts INTEGER'))
                connection.execute(text('UPDATE employee_accounts SET failed_attempts = 0 WHERE failed_attempts IS NULL'))

            if 'last_login_at' not in account_columns:
                connection.execute(text('ALTER TABLE employee_accounts ADD COLUMN last_login_at DATETIME'))

            if 'synced_at' not in account_columns:
                connection.execute(text('ALTER TABLE employee_accounts ADD COLUMN synced_at DATETIME'))

            if 'created_at' not in account_columns:
                connection.execute(text('ALTER TABLE employee_accounts ADD COLUMN created_at DATETIME'))
                connection.execute(text('UPDATE employee_accounts SET created_at = CURRENT_TIMESTAMP WHERE created_at IS NULL'))

            if 'updated_at' not in account_columns:
                connection.execute(text('ALTER TABLE employee_accounts ADD COLUMN updated_at DATETIME'))
                connection.execute(text('UPDATE employee_accounts SET updated_at = CURRENT_TIMESTAMP WHERE updated_at IS NULL'))
