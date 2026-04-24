import json

from backend.models.database import db, SystemSetting


SYSTEM_SETTING_KEY_MODULE_VISIBILITY = 'module_visibility'
SYSTEM_SETTING_KEY_ATTENDANCE = 'attendance_settings'

MODULE_TOGGLE_KEYS = (
    'attendance',
    'camera_management',
    'online_sync',
    'sync_verify',
    'offline_manage',
    'report',
    'online_attendance_check',
    'account_management',
    'mobile_config',
)

DEFAULT_MODULE_VISIBILITY = {
    key: True for key in MODULE_TOGGLE_KEYS
}

DEFAULT_ATTENDANCE_SETTINGS = {
    'mode': 'checkin_checkout',
    'cooldown_hours': 0,
    'cooldown_minutes': 10,
    'cooldown_seconds': 0,
}


def _coerce_int(value, min_value, max_value, fallback):
    try:
        parsed = int(float(value))
    except (TypeError, ValueError):
        return fallback
    return max(min_value, min(max_value, parsed))


def normalize_module_visibility(payload):
    source = payload if isinstance(payload, dict) else {}
    normalized = dict(DEFAULT_MODULE_VISIBILITY)

    for key in MODULE_TOGGLE_KEYS:
        raw = source.get(key)
        if isinstance(raw, bool):
            normalized[key] = raw

    return normalized


def normalize_attendance_settings(payload):
    source = payload if isinstance(payload, dict) else {}

    mode = str(source.get('mode') or '').strip().lower()
    if mode != 'auto_record':
        mode = 'checkin_checkout'

    return {
        'mode': mode,
        'cooldown_hours': _coerce_int(
            source.get('cooldown_hours'),
            0,
            23,
            DEFAULT_ATTENDANCE_SETTINGS['cooldown_hours'],
        ),
        'cooldown_minutes': _coerce_int(
            source.get('cooldown_minutes'),
            0,
            59,
            DEFAULT_ATTENDANCE_SETTINGS['cooldown_minutes'],
        ),
        'cooldown_seconds': _coerce_int(
            source.get('cooldown_seconds'),
            0,
            59,
            DEFAULT_ATTENDANCE_SETTINGS['cooldown_seconds'],
        ),
    }


def _read_setting_json(setting_key):
    row = SystemSetting.query.filter_by(key=setting_key).first()
    if not row or not row.value:
        return {}

    try:
        parsed = json.loads(row.value)
        return parsed if isinstance(parsed, dict) else {}
    except json.JSONDecodeError:
        return {}


def _write_setting_json(setting_key, payload):
    row = SystemSetting.query.filter_by(key=setting_key).first()
    if row is None:
        row = SystemSetting(key=setting_key, value='{}')
    row.value = json.dumps(payload, ensure_ascii=True)
    db.session.add(row)


def get_system_settings():
    module_visibility = normalize_module_visibility(
        _read_setting_json(SYSTEM_SETTING_KEY_MODULE_VISIBILITY)
    )
    attendance_settings = normalize_attendance_settings(
        _read_setting_json(SYSTEM_SETTING_KEY_ATTENDANCE)
    )

    return {
        'module_visibility': module_visibility,
        'attendance_settings': attendance_settings,
    }


def save_system_settings(payload):
    source = payload if isinstance(payload, dict) else {}
    current = get_system_settings()

    module_visibility = normalize_module_visibility(
        source.get('module_visibility', current['module_visibility'])
    )
    attendance_settings = normalize_attendance_settings(
        source.get('attendance_settings', current['attendance_settings'])
    )

    _write_setting_json(SYSTEM_SETTING_KEY_MODULE_VISIBILITY, module_visibility)
    _write_setting_json(SYSTEM_SETTING_KEY_ATTENDANCE, attendance_settings)
    db.session.commit()

    return {
        'module_visibility': module_visibility,
        'attendance_settings': attendance_settings,
    }


def get_attendance_mode():
    attendance_settings = get_system_settings()['attendance_settings']
    return attendance_settings.get('mode', DEFAULT_ATTENDANCE_SETTINGS['mode'])


def get_attendance_cooldown_seconds():
    attendance_settings = get_system_settings()['attendance_settings']
    hours = _coerce_int(attendance_settings.get('cooldown_hours'), 0, 23, 0)
    minutes = _coerce_int(attendance_settings.get('cooldown_minutes'), 0, 59, 10)
    seconds = _coerce_int(attendance_settings.get('cooldown_seconds'), 0, 59, 0)
    return (hours * 3600) + (minutes * 60) + seconds
