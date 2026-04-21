# -*- coding: utf-8 -*-
"""Smoke test ERP service APIs on DEV (192.168.1.20)."""

from __future__ import annotations

import json
import os
from datetime import datetime
from pathlib import Path
from typing import Any, Dict, List, Optional, Tuple

from backend.services.erp_http_client import ERPHttpClient, ERPServiceError


USERNAME = "erp.chamcongpro1"
PASSWORD = "123456"
DEV_HOST = "192.168.1.20"


def _now_text() -> str:
    return datetime.now().strftime("%Y-%m-%d %H:%M:%S")


def _unique(items: List[str]) -> List[str]:
    out: List[str] = []
    for item in items:
        text = str(item or "").strip()
        if text and text not in out:
            out.append(text)
    return out


def _response_success(response: Any) -> bool:
    if isinstance(response, dict):
        if response.get("success") is False:
            return False
        status_text = str(response.get("status") or response.get("Status") or "").strip().lower()
        if status_text in {"error", "failed", "fail"}:
            return False
        if status_text.isdigit() and int(status_text) >= 3000:
            return False
        return True

    if isinstance(response, list) and response and isinstance(response[0], bool):
        return bool(response[0])

    return bool(response)


def _sniff_image_valid(image_bytes: bytes) -> Tuple[bool, str]:
    if not image_bytes:
        return False, "empty image payload"

    head = image_bytes[:256].lower()
    if (
        b"image not found" in head
        or b"khong tim thay anh" in head
        or b"missing lv001" in head
        or b"<html" in head
        or b'"message":"invalid"' in head
        or b'"error"' in head
    ):
        return False, "payload looks like error text/html"

    return True, "ok"


def _build_service_headers(client: ERPHttpClient, auth: Dict[str, Any], sof_token: str) -> Dict[str, str]:
    headers = client._build_service_headers(auth)

    headers["X-DEVICE-TYPE"] = "desktop"

    if sof_token:
        headers["X-SOF-USER-TOKEN"] = sof_token
        headers["SOF-User-Token"] = sof_token

    user_code = str(auth.get("code") or "").strip()
    if user_code:
        headers["X-USER-CODE"] = user_code

    database = str(auth.get("database") or auth.get("dbName") or auth.get("table") or "").strip()
    if database:
        headers["X-DATABASE"] = database

    return headers


def _make_client(service_url: str, login_url: str, sof_token: str, type_code: str) -> ERPHttpClient:
    client = ERPHttpClient(
        service_url=service_url,
        login_url=login_url,
        service_path_suffix="/services.sof.vn/index.php",
        sof_dev_token=sof_token,
        type_code=type_code,
        device_type="desktop",
    )
    # Force deterministic TYPE-SOF-CODE for smoke test.
    client.type_code_dynamic = False
    return client


def _select_login(
    login_urls: List[str],
    service_url: str,
    sof_token: str,
) -> Tuple[Optional[ERPHttpClient], Optional[Dict[str, Any]], Dict[str, Any]]:
    attempts: List[Dict[str, str]] = []

    for login_url in login_urls:
        for type_code in ("CHAMCONG", "ERP"):
            client = _make_client(service_url, login_url, sof_token, type_code)
            try:
                auth = client.login(USERNAME, PASSWORD)
                if isinstance(auth, dict) and str(auth.get("token") or "").strip():
                    return client, auth, {
                        "login_url": login_url,
                        "type_code": type_code,
                        "token_source": str(auth.get("token_source") or "").strip(),
                        "attempts": attempts,
                    }
                attempts.append(
                    {
                        "login_url": login_url,
                        "type_code": type_code,
                        "error": "empty token in auth payload",
                    }
                )
            except ERPServiceError as exc:
                attempts.append(
                    {
                        "login_url": login_url,
                        "type_code": type_code,
                        "error": f"{exc.message}",
                    }
                )

    return None, None, {"attempts": attempts}


def _select_service_url(
    service_urls: List[str],
    login_url: str,
    sof_token: str,
    type_code: str,
    auth: Dict[str, Any],
) -> Tuple[Optional[ERPHttpClient], Optional[str], Dict[str, Any]]:
    probes: List[Dict[str, Any]] = []

    for service_url in service_urls:
        client = _make_client(service_url, login_url, sof_token, type_code)
        headers = _build_service_headers(client, auth, sof_token)
        payload = {
            "table": "hr_lv0020",
            "func": "data",
            "code": str(auth.get("code") or ""),
            "token": str(auth.get("token") or ""),
        }
        try:
            response = client._post_json(service_url, payload, headers=headers)
            mapped = client._map_employees(response)
            probes.append(
                {
                    "service_url": service_url,
                    "ok": _response_success(response),
                    "employee_count": len(mapped),
                }
            )
            if _response_success(response) and len(mapped) > 0:
                return client, service_url, {"probes": probes}
        except ERPServiceError as exc:
            probes.append(
                {
                    "service_url": service_url,
                    "ok": False,
                    "error": exc.message,
                }
            )

    return None, None, {"probes": probes}


def run() -> int:
    env_login_url = os.getenv("ERP_HTTP_LOGIN_URL_DEV") or os.getenv("ERP_HTTP_LOGIN_URL") or ""
    env_service_url = os.getenv("ERP_HTTP_SERVICE_URL_DEV") or os.getenv("ERP_HTTP_SERVICE_URL") or ""
    sof_token = (
        os.getenv("ERP_HTTP_SOF_DEV_TOKEN_DEV")
        or os.getenv("ERP_HTTP_SOF_DEV_TOKEN")
        or "8c4f2b9a71d6e3c9f0ab42d5e8c1f7a39b6d0e4f1a2c8b7d5e9f3a1c6b4d2e8"
    )

    login_urls = _unique(
        [
            env_login_url,
            f"http://{DEV_HOST}/erpdung-hao/services/erpv1/login.sof.vn/login.sof.vn/index.php",
            f"http://{DEV_HOST}/login.sof.vn/index.php",
        ]
    )

    service_urls = _unique(
        [
            env_service_url,
            f"http://{DEV_HOST}/services.sof.vn/index.php",
            f"http://{DEV_HOST}/chamcong/services.sof.vn/index.php",
            f"http://{DEV_HOST}/erpdung-hao/services/erpv1/services.sof.vn/index.php",
        ]
    )

    results: List[Dict[str, Any]] = []

    def record(name: str, status: str, detail: str, extra: Optional[Dict[str, Any]] = None) -> None:
        entry: Dict[str, Any] = {
            "api": name,
            "status": status,
            "detail": detail,
        }
        if extra:
            entry.update(extra)
        results.append(entry)

    client, auth, login_meta = _select_login(
        login_urls=login_urls,
        service_url=service_urls[0],
        sof_token=sof_token,
    )

    if not client or not auth:
        record("B0 Login", "FAIL", "Login DEV failed for all candidate URLs/type codes", login_meta)
        report = {
            "executed_at": _now_text(),
            "target": "DEV",
            "host": DEV_HOST,
            "login": login_meta,
            "service_selection": {},
            "results": results,
            "summary": {
                "pass": 0,
                "warn": 0,
                "fail": 1,
                "skip": 0,
            },
        }
        report_path = Path("dev_smoke_test_report.json")
        report_path.write_text(json.dumps(report, ensure_ascii=False, indent=2), encoding="utf-8")
        print(json.dumps(report, ensure_ascii=False, indent=2))
        print(f"REPORT_FILE={report_path}")
        return 1

    record(
        "B0 Login",
        "PASS",
        "Login DEV success",
        {
            "login_url": login_meta.get("login_url"),
            "type_code": login_meta.get("type_code"),
            "token_source": login_meta.get("token_source"),
            "code": str(auth.get("code") or ""),
            "database": str(auth.get("database") or auth.get("dbName") or auth.get("table") or ""),
            "domain": str(auth.get("domain") or ""),
        },
    )

    selected_type_code = str(login_meta.get("type_code") or "CHAMCONG")
    selected_login_url = str(login_meta.get("login_url") or login_urls[0])

    preferred_service_url = _make_client(
        service_urls[0],
        selected_login_url,
        sof_token,
        selected_type_code,
    )._resolve_service_url(auth)

    probe_urls = _unique([preferred_service_url] + service_urls)
    svc_client, selected_service_url, svc_meta = _select_service_url(
        service_urls=probe_urls,
        login_url=selected_login_url,
        sof_token=sof_token,
        type_code=selected_type_code,
        auth=auth,
    )

    if not svc_client or not selected_service_url:
        record(
            "Service URL select",
            "FAIL",
            "Could not find a working DEV service URL",
            svc_meta,
        )
        report = {
            "executed_at": _now_text(),
            "target": "DEV",
            "host": DEV_HOST,
            "login": login_meta,
            "service_selection": svc_meta,
            "results": results,
        }
        report_path = Path("dev_smoke_test_report.json")
        report_path.write_text(json.dumps(report, ensure_ascii=False, indent=2), encoding="utf-8")
        print(json.dumps(report, ensure_ascii=False, indent=2))
        print(f"REPORT_FILE={report_path}")
        return 1

    record(
        "Service URL select",
        "PASS",
        "Selected working DEV service URL",
        {
            "service_url": selected_service_url,
            "probes": svc_meta.get("probes"),
        },
    )

    client = svc_client
    service_url = selected_service_url
    headers = _build_service_headers(client, auth, sof_token)
    code = str(auth.get("code") or "").strip()
    token = str(auth.get("token") or "").strip()

    employees_data: List[Dict[str, Any]] = []
    employees_fallback: List[Dict[str, Any]] = []
    employee_id = ""
    employee_row: Dict[str, Any] = {}

    try:
        payload = {
            "table": "hr_lv0020",
            "func": "data",
            "code": code,
            "token": token,
        }
        response = client._post_json(service_url, payload, headers=headers)
        employees_data = client._map_employees(response)
        if _response_success(response) and employees_data:
            record(
                "S1 List Employees (data)",
                "PASS",
                "Employee list via func=data returned data",
                {"employee_count": len(employees_data)},
            )
        else:
            record(
                "S1 List Employees (data)",
                "FAIL",
                "Employee list via func=data returned empty/unexpected payload",
                {"raw_type": type(response).__name__},
            )
    except ERPServiceError as exc:
        record("S1 List Employees (data)", "FAIL", exc.message)

    try:
        payload = {
            "table": "hr_lv0020",
            "func": "LayNhanVien",
            "code": code,
            "token": token,
        }
        response = client._post_json(service_url, payload, headers=headers)
        employees_fallback = client._map_employees(response)
        if _response_success(response) and employees_fallback:
            record(
                "S2 List Employees (LayNhanVien)",
                "PASS",
                "Employee list via fallback func returned data",
                {"employee_count": len(employees_fallback)},
            )
        else:
            record(
                "S2 List Employees (LayNhanVien)",
                "FAIL",
                "Fallback employee list returned empty/unexpected payload",
                {"raw_type": type(response).__name__},
            )
    except ERPServiceError as exc:
        record("S2 List Employees (LayNhanVien)", "FAIL", exc.message)

    if employees_data:
        employee_row = employees_data[0]
        employee_id = str(employee_row.get("employee_id") or "").strip()
    elif employees_fallback:
        employee_row = employees_fallback[0]
        employee_id = str(employee_row.get("employee_id") or "").strip()

    if not employee_id:
        record("Employee selector", "FAIL", "No employee_id available from employee list")

    detail_row: Dict[str, Any] = {}
    if employee_id:
        try:
            payload = {
                "table": "hr_lv0020",
                "func": "layNhanVienTheoMa",
                "maNhanVien": employee_id,
                "code": code,
                "token": token,
            }
            response = client._post_json(service_url, payload, headers=headers)
            rows = client._map_employees(response)
            if _response_success(response) and rows:
                detail_row = rows[0]
                record(
                    "S3 Get Employee By ID",
                    "PASS",
                    "layNhanVienTheoMa returned employee record",
                    {"employee_id": employee_id},
                )
            else:
                record(
                    "S3 Get Employee By ID",
                    "FAIL",
                    "layNhanVienTheoMa returned empty/unexpected payload",
                    {"employee_id": employee_id},
                )
        except ERPServiceError as exc:
            record("S3 Get Employee By ID", "FAIL", exc.message, {"employee_id": employee_id})
    else:
        record("S3 Get Employee By ID", "SKIP", "Skipped because employee_id is missing")

    if employee_id:
        try:
            payload = {
                "table": "getAnhTable",
                "func": "getAnh",
                "lv001": employee_id,
                "cot": "lv008",
                "code": code,
                "token": token,
            }
            image_bytes, content_type = client._post_form(
                service_url,
                payload,
                expect_json=False,
                headers=headers,
            )
            valid, reason = _sniff_image_valid(image_bytes)
            if valid:
                record(
                    "S4 Get Employee Image",
                    "PASS",
                    "getAnh returned image bytes",
                    {"employee_id": employee_id, "bytes": len(image_bytes), "content_type": content_type},
                )
            else:
                record(
                    "S4 Get Employee Image",
                    "FAIL",
                    f"getAnh payload invalid: {reason}",
                    {"employee_id": employee_id, "bytes": len(image_bytes)},
                )
        except ERPServiceError as exc:
            record("S4 Get Employee Image", "FAIL", exc.message, {"employee_id": employee_id})
    else:
        record("S4 Get Employee Image", "SKIP", "Skipped because employee_id is missing")

    image_path = Path("static") / "images" / "placeholder.jpg"
    image_bytes = image_path.read_bytes() if image_path.exists() else b""

    if employee_id and image_bytes:
        try:
            fields = {
                "table": "cr_lv0382",
                "func": "uploadAnh",
                "lv001": employee_id,
                "cot": "lv008",
                "code": code,
                "token": token,
            }
            response = client._post_multipart(
                service_url,
                fields,
                file_field="image",
                filename="placeholder.jpg",
                file_bytes=image_bytes,
                file_content_type="image/jpeg",
                headers=headers,
            )
            if _response_success(response):
                record(
                    "S5 Upload Employee Image",
                    "PASS",
                    "uploadAnh accepted multipart image payload",
                    {"employee_id": employee_id},
                )
            else:
                record(
                    "S5 Upload Employee Image",
                    "FAIL",
                    "uploadAnh returned error payload",
                    {"employee_id": employee_id, "response": response},
                )
        except ERPServiceError as exc:
            record("S5 Upload Employee Image", "FAIL", exc.message, {"employee_id": employee_id})
    elif not image_bytes:
        record("S5 Upload Employee Image", "SKIP", "placeholder image not found at static/images/placeholder.jpg")
    else:
        record("S5 Upload Employee Image", "SKIP", "Skipped because employee_id is missing")

    image_token = str(detail_row.get("image_token") or employee_row.get("image_token") or "").strip()
    token_to_update = image_token or f"SMOKE_{datetime.now().strftime('%Y%m%d%H%M%S')}"

    if employee_id:
        try:
            payload = {
                "table": "hr_lv0020",
                "func": "updateImageToken",
                "lv001": employee_id,
                "tokenAnh": token_to_update,
                "cot": "lv007",
                "code": code,
                "token": token,
            }
            response = client._post_json(service_url, payload, headers=headers)
            if _response_success(response):
                status = "PASS" if image_token else "WARN"
                detail = "updateImageToken success"
                if not image_token:
                    detail = "updateImageToken success with generated smoke token"
                record(
                    "S6 Update Employee Image Token",
                    status,
                    detail,
                    {"employee_id": employee_id, "used_token": token_to_update},
                )
            else:
                record(
                    "S6 Update Employee Image Token",
                    "FAIL",
                    "updateImageToken returned error payload",
                    {"employee_id": employee_id, "response": response},
                )
        except ERPServiceError as exc:
            record("S6 Update Employee Image Token", "FAIL", exc.message, {"employee_id": employee_id})
    else:
        record("S6 Update Employee Image Token", "SKIP", "Skipped because employee_id is missing")

    attendance_dt = datetime.now()
    attendance_date = attendance_dt.strftime("%Y-%m-%d")
    attendance_time = attendance_dt.strftime("%H:%M:%S")

    if employee_id:
        try:
            payload = {
                "table": "chamcong_ngocchung",
                "func": "pushAttendance",
                "employee_id": employee_id,
                "attendance_date": attendance_date,
                "attendance_time": attendance_time,
                "attendance_type": "IN",
                "source": "SmokeTest",
                "camera_ip": DEV_HOST,
            }
            response = client._post_json(service_url, payload, headers=headers)
            if _response_success(response):
                record(
                    "S7 Push Attendance",
                    "PASS",
                    "pushAttendance accepted attendance record",
                    {
                        "employee_id": employee_id,
                        "attendance_date": attendance_date,
                        "attendance_time": attendance_time,
                    },
                )
            else:
                record(
                    "S7 Push Attendance",
                    "FAIL",
                    "pushAttendance returned error payload",
                    {"employee_id": employee_id, "response": response},
                )
        except ERPServiceError as exc:
            record("S7 Push Attendance", "FAIL", exc.message, {"employee_id": employee_id})
    else:
        record("S7 Push Attendance", "SKIP", "Skipped because employee_id is missing")

    if employee_id:
        try:
            payload = {
                "table": "chamcong_ngocchung",
                "func": "checkRecentAttendance",
                "employee_id": employee_id,
                "minutes": 10,
            }
            response = client._post_json(service_url, payload, headers=headers)
            if _response_success(response):
                record(
                    "S8 Check Recent Attendance",
                    "PASS",
                    "checkRecentAttendance returned success",
                    {
                        "employee_id": employee_id,
                        "exists": response.get("exists") if isinstance(response, dict) else None,
                        "count": response.get("count") if isinstance(response, dict) else None,
                    },
                )
            else:
                record(
                    "S8 Check Recent Attendance",
                    "FAIL",
                    "checkRecentAttendance returned error payload",
                    {"employee_id": employee_id, "response": response},
                )
        except ERPServiceError as exc:
            record("S8 Check Recent Attendance", "FAIL", exc.message, {"employee_id": employee_id})
    else:
        record("S8 Check Recent Attendance", "SKIP", "Skipped because employee_id is missing")

    try:
        payload = {
            "table": "chamcong_ngocchung",
            "func": "getOnlineAttendance",
            "start_date": attendance_date,
            "end_date": attendance_date,
            "attendance_type": "all",
            "sort_by": "date",
            "sort_dir": "desc",
            "page": 1,
            "page_size": 50,
            "employee_id": employee_id,
            "keyword": "",
        }
        response = client._post_json(service_url, payload, headers=headers)
        records = []
        if isinstance(response, dict):
            if isinstance(response.get("records"), list):
                records = response.get("records") or []
            elif isinstance(response.get("data"), list):
                records = response.get("data") or []

        if _response_success(response):
            record(
                "S9 Get Online Attendance",
                "PASS",
                "getOnlineAttendance returned success",
                {"records": len(records), "employee_id": employee_id},
            )
        else:
            record(
                "S9 Get Online Attendance",
                "FAIL",
                "getOnlineAttendance returned error payload",
                {"response": response},
            )
    except ERPServiceError as exc:
        record("S9 Get Online Attendance", "FAIL", exc.message)

    if image_bytes:
        try:
            token_payload = client.create_sof_image_token(image_bytes, username=code, auth=auth)
            token_value = str(token_payload.get("token") or "").strip() if isinstance(token_payload, dict) else ""
            if token_value:
                record(
                    "S10 Create SOF Image Token",
                    "PASS",
                    "Token register service returned token",
                    {"token_preview": f"{token_value[:8]}..." if len(token_value) > 8 else token_value},
                )
            else:
                record(
                    "S10 Create SOF Image Token",
                    "FAIL",
                    "Token register service returned no token",
                    {"response": token_payload},
                )
        except ERPServiceError as exc:
            record("S10 Create SOF Image Token", "FAIL", exc.message)
    else:
        record("S10 Create SOF Image Token", "SKIP", "Skipped because placeholder image is missing")

    pass_count = sum(1 for item in results if item["status"] == "PASS")
    warn_count = sum(1 for item in results if item["status"] == "WARN")
    fail_count = sum(1 for item in results if item["status"] == "FAIL")
    skip_count = sum(1 for item in results if item["status"] == "SKIP")

    report = {
        "executed_at": _now_text(),
        "target": "DEV",
        "host": DEV_HOST,
        "selected_login_url": selected_login_url,
        "selected_type_code": selected_type_code,
        "selected_service_url": service_url,
        "login": login_meta,
        "service_selection": svc_meta,
        "results": results,
        "summary": {
            "pass": pass_count,
            "warn": warn_count,
            "fail": fail_count,
            "skip": skip_count,
        },
    }

    report_path = Path("dev_smoke_test_report.json")
    report_path.write_text(json.dumps(report, ensure_ascii=False, indent=2), encoding="utf-8")

    print(json.dumps(report, ensure_ascii=False, indent=2))
    print(f"REPORT_FILE={report_path}")

    return 0 if fail_count == 0 else 2


if __name__ == "__main__":
    raise SystemExit(run())
