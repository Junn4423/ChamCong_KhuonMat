# -*- coding: utf-8 -*-
"""Configuration for Face Recognition System backend."""

import os
from dotenv import load_dotenv

load_dotenv()

ERP_FETCH_MODE = os.getenv('ERP_FETCH_MODE', 'http_service').lower()
ERP_HTTP_SERVICE_URL = os.getenv(
    'ERP_HTTP_SERVICE_URL',
    'http://192.168.1.20/erpdung-hao/services/erpv1/services.sof.vn/index.php',
)
ERP_HTTP_LOGIN_URL = os.getenv(
    'ERP_HTTP_LOGIN_URL',
    'http://192.168.1.20/erpdung-hao/services/erpv1/login.sof.vn/index.php',
)
ERP_HTTP_TIMEOUT = int(os.getenv('ERP_HTTP_TIMEOUT', 10))
ERP_HTTP_IMAGE_COLUMN = os.getenv('ERP_HTTP_IMAGE_COLUMN', 'lv008')

MYSQL_USE_PURE = os.getenv('MYSQL_USE_PURE', 'true').lower() != 'false'
MYSQL_COMMON_CONFIG = {
    'charset': 'utf8mb4',
    'connection_timeout': 3,
    'use_pure': MYSQL_USE_PURE,
}

# ERP Main Database (employee info)
ERP_MAIN_CONFIG = {
    'host': os.getenv('ERP_HOST', '192.168.1.82'),
    'port': int(os.getenv('ERP_PORT', 3306)),
    'user': os.getenv('ERP_USER', 'faceuser'),
    'password': os.getenv('ERP_PASSWORD', 'THU@1982'),
    'database': os.getenv('ERP_DATABASE', 'erp_sofv4_0'),
    **MYSQL_COMMON_CONFIG,
}

# ERP Documents Database (employee images)
ERP_DOCS_CONFIG = {
    'host': os.getenv('ERP_DOCS_HOST', '192.168.1.82'),
    'port': int(os.getenv('ERP_DOCS_PORT', 3306)),
    'user': os.getenv('ERP_DOCS_USER', 'faceuser'),
    'password': os.getenv('ERP_DOCS_PASSWORD', 'THU@1982'),
    'database': os.getenv('ERP_DOCS_DATABASE', 'erp_sof_documents_v4_0'),
    **MYSQL_COMMON_CONFIG,
}

# Table/Column mappings
EMPLOYEE_TABLE = 'hr_lv0020'
EMPLOYEE_COLUMNS = {
    'employee_id': 'lv001',
    'name': 'lv002',
    'department': 'lv003',
}

IMAGE_TABLE = 'hr_lv0041'
IMAGE_COLUMNS = {
    'employee_id': 'lv002',
    'image_blob': 'lv008'
}

ATTENDANCE_TABLE = 'tc_lv0012'
ATTENDANCE_COLUMNS = {
    'employee_id': 'lv001',
    'date': 'lv002',
    'time': 'lv003',
    'type': 'lv004',
    'source': 'lv005',
    'camera_ip': 'lv099',
    'lv199': 'lv199'
}

# Import settings
IMPORT_CONFIG = {
    'batch_size': 10,
    'skip_existing': True,
    'require_image': True,
    'face_encoding_tolerance': 0.4,
    'temp_image_format': '.jpg'
}

# Camera settings
CAMERA_CONFIG = {
    'ip': os.getenv('CAMERA_IP', '192.168.1.97'),
    'default_source': 'Camera',
    'default_camera_type': os.getenv('DEFAULT_CAMERA_TYPE', 'rtsp'),
    'device_index': int(os.getenv('DEVICE_CAMERA_INDEX', 0)),
    'rtsp_url': os.getenv('RTSP_URL', 'rtsp://admin:GYTKAX@192.168.1.97/h264/ch1/main/av_stream'),
    'flip_mode': os.getenv('CAMERA_FLIP_MODE', 'auto')
}

# Performance tuning
PERFORMANCE_CONFIG = {
    'skip_frames': int(os.getenv('SKIP_FRAMES', 8)),
    'resize_factor': float(os.getenv('RESIZE_FACTOR', 0.33)),
    'fps_limit': int(os.getenv('FPS_LIMIT', 30)),
    'detection_width': int(os.getenv('DETECTION_WIDTH', 1280)),
    'detection_height': int(os.getenv('DETECTION_HEIGHT', 1280))
}

# Intel CPU Optimization
INTEL_OPTIMIZATION = {
    'omp_num_threads': int(os.getenv('OMP_NUM_THREADS', 4)),
    'mkl_num_threads': int(os.getenv('MKL_NUM_THREADS', 4)),
    'openvino_cache_dir': os.getenv('OPENVINO_CACHE_DIR', './openvino_cache'),
    'use_onnx_antispoof': os.getenv('USE_ONNX_ANTISPOOF', 'false').lower() == 'true'
}

# Motion detection
MOTION_CONFIG = {
    'threshold': int(os.getenv('MOTION_THRESHOLD', 25)),
    'min_area': int(os.getenv('MOTION_MIN_AREA', 500))
}
