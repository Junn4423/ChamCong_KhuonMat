import json
import os
import unicodedata
from functools import lru_cache
from urllib.parse import urlencode
from urllib.request import Request, urlopen


GEOCODER_URL = os.environ.get(
    'FACECHECK_GEOCODER_URL',
    'https://nominatim.openstreetmap.org/reverse',
)
GEOCODER_LANGUAGE = os.environ.get('FACECHECK_GEOCODER_LANGUAGE', 'vi')
GEOCODER_TIMEOUT_SECONDS = float(os.environ.get('FACECHECK_GEOCODER_TIMEOUT_SECONDS', '4'))
GEOCODER_USER_AGENT = os.environ.get('FACECHECK_GEOCODER_USER_AGENT', 'FaceCheck/1.0')


def _normalize_text(value):
    text = str(value or '').strip().lower()
    if not text:
        return ''
    normalized = unicodedata.normalize('NFKD', text)
    return ''.join(ch for ch in normalized if not unicodedata.combining(ch))


def is_generic_location_label(label):
    normalized = _normalize_text(label)
    if not normalized:
        return True
    generic_labels = {
        'browser',
        'current device',
        'device location',
        'gps',
        'thiet bi hien tai',
        'toa do gps',
        'vi tri hien tai',
        'vi tri thiet bi',
    }
    return normalized in generic_labels


def _pick_first(*values):
    for value in values:
        text = str(value or '').strip()
        if text:
            return text
    return ''


def _dedupe_parts(parts):
    unique = []
    seen = set()
    for part in parts:
        text = str(part or '').strip()
        if not text:
            continue
        key = _normalize_text(text)
        if not key or key in seen:
            continue
        unique.append(text)
        seen.add(key)
    return unique


def _format_address(payload):
    if not isinstance(payload, dict):
        return ''

    address = payload.get('address') or {}
    if not isinstance(address, dict):
        address = {}

    house_number = _pick_first(address.get('house_number'))
    road_name = _pick_first(
        address.get('road'),
        address.get('pedestrian'),
        address.get('footway'),
        address.get('residential'),
        address.get('street'),
    )
    road = ' '.join(part for part in (house_number, road_name) if part).strip()

    ward = _pick_first(
        address.get('suburb'),
        address.get('quarter'),
        address.get('neighbourhood'),
        address.get('city_block'),
        address.get('hamlet'),
    )
    district = _pick_first(
        address.get('city_district'),
        address.get('district'),
        address.get('county'),
    )
    city = _pick_first(
        address.get('city'),
        address.get('town'),
        address.get('municipality'),
        address.get('village'),
    )
    province = _pick_first(
        address.get('state'),
        address.get('region'),
    )

    parts = _dedupe_parts([road, ward, district, city, province])
    if parts:
        return ', '.join(parts)

    display_name = str(payload.get('display_name') or '').strip()
    if not display_name:
        return ''
    compact_parts = _dedupe_parts(display_name.split(','))
    return ', '.join(compact_parts[:5])


@lru_cache(maxsize=512)
def _reverse_geocode_cached(lat_key, lng_key):
    params = urlencode({
        'format': 'jsonv2',
        'lat': lat_key,
        'lon': lng_key,
        'zoom': 18,
        'addressdetails': 1,
        'namedetails': 0,
        'accept-language': GEOCODER_LANGUAGE,
    })
    request = Request(
        f'{GEOCODER_URL}?{params}',
        headers={
            'User-Agent': GEOCODER_USER_AGENT,
            'Accept': 'application/json',
            'Accept-Language': GEOCODER_LANGUAGE,
        },
    )

    with urlopen(request, timeout=GEOCODER_TIMEOUT_SECONDS) as response:
        payload = json.loads(response.read().decode('utf-8'))

    return {
        'label': _format_address(payload),
        'provider': 'nominatim',
        'display_name': str(payload.get('display_name') or '').strip(),
    }


def reverse_geocode(latitude, longitude):
    if latitude is None or longitude is None:
        return {}

    lat_key = f'{float(latitude):.6f}'
    lng_key = f'{float(longitude):.6f}'
    try:
        return dict(_reverse_geocode_cached(lat_key, lng_key))
    except Exception:
        return {}


def enrich_location_payload(location_payload):
    if not isinstance(location_payload, dict):
        return None

    enriched = dict(location_payload)
    latitude = enriched.get('latitude')
    longitude = enriched.get('longitude')
    if latitude is None or longitude is None:
        return enriched

    current_label = str(enriched.get('label') or enriched.get('address') or '').strip()
    should_resolve_label = is_generic_location_label(current_label)

    if should_resolve_label:
        geocode_payload = reverse_geocode(latitude, longitude)
        resolved_label = str(geocode_payload.get('label') or '').strip()
        if resolved_label:
            enriched['label'] = resolved_label
            enriched['address'] = resolved_label

        resolved_provider = str(geocode_payload.get('provider') or '').strip()
        current_provider = str(enriched.get('provider') or '').strip()
        if resolved_provider:
            enriched['provider'] = f'{current_provider}+{resolved_provider}' if current_provider else resolved_provider
            enriched['geocode_provider'] = resolved_provider

        display_name = str(geocode_payload.get('display_name') or '').strip()
        if display_name:
            enriched['display_name'] = display_name

    return enriched
