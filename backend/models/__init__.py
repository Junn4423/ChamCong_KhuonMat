from .database import db, User, Attendance, AttendanceLocation

try:  # Optional at runtime when AI dependencies are unavailable.
    from .face_recognition_module import FaceRecognition
except Exception:  # pragma: no cover
    FaceRecognition = None

try:
    from .erp_integration import erp_attendance
except Exception:  # pragma: no cover
    erp_attendance = None
