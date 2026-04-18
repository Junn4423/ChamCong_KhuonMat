<?php

if (!function_exists('cc_ngocchung_trace_id')) {
    function cc_ngocchung_trace_id()
    {
        return 'cc_' . date('YmdHis') . '_' . substr(md5(uniqid('', true)), 0, 10);
    }
}

if (!function_exists('cc_ngocchung_error')) {
    function cc_ngocchung_error($message, $errorCode = 'ERP_ATTENDANCE_ERROR', $httpCode = 400, $details = array())
    {
        if (is_int($httpCode) && $httpCode >= 400) {
            http_response_code($httpCode);
        }
        return array(
            'success' => false,
            'message' => (string)$message,
            'error_code' => (string)$errorCode,
            'trace_id' => cc_ngocchung_trace_id(),
            'details' => is_array($details) ? $details : array(),
        );
    }
}

if (!function_exists('cc_ngocchung_success')) {
    function cc_ngocchung_success($message, $payload = array())
    {
        $response = array(
            'success' => true,
            'message' => (string)$message,
        );
        if (is_array($payload)) {
            foreach ($payload as $key => $value) {
                $response[$key] = $value;
            }
        }
        return $response;
    }
}

if (!function_exists('cc_ngocchung_parse_date')) {
    function cc_ngocchung_parse_date($rawValue, $defaultValue = '')
    {
        $value = trim((string)$rawValue);
        if ($value === '') {
            return $defaultValue;
        }
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
            return '';
        }
        $parts = explode('-', $value);
        if (count($parts) !== 3) {
            return '';
        }
        $year = (int)$parts[0];
        $month = (int)$parts[1];
        $day = (int)$parts[2];
        if (!checkdate($month, $day, $year)) {
            return '';
        }
        return sprintf('%04d-%02d-%02d', $year, $month, $day);
    }
}

if (!function_exists('cc_ngocchung_parse_time')) {
    function cc_ngocchung_parse_time($rawValue, $defaultValue = '')
    {
        $value = trim((string)$rawValue);
        if ($value === '') {
            return $defaultValue;
        }
        if (preg_match('/^\d{2}:\d{2}$/', $value)) {
            $value .= ':00';
        }
        if (!preg_match('/^\d{2}:\d{2}:\d{2}$/', $value)) {
            return '';
        }
        $parts = explode(':', $value);
        if (count($parts) !== 3) {
            return '';
        }
        $hour = (int)$parts[0];
        $minute = (int)$parts[1];
        $second = (int)$parts[2];
        if ($hour < 0 || $hour > 23 || $minute < 0 || $minute > 59 || $second < 0 || $second > 59) {
            return '';
        }
        return sprintf('%02d:%02d:%02d', $hour, $minute, $second);
    }
}

if (!function_exists('cc_ngocchung_parse_attendance_type')) {
    function cc_ngocchung_parse_attendance_type($rawValue)
    {
        $value = strtoupper(trim((string)$rawValue));
        if ($value === 'OUT' || $value === 'CHECKOUT' || $value === 'CHECK_OUT') {
            return 'OUT';
        }
        return 'IN';
    }
}

if (!function_exists('cc_ngocchung_parse_int')) {
    function cc_ngocchung_parse_int($rawValue, $defaultValue, $minValue, $maxValue)
    {
        $value = (int)$rawValue;
        if ($value < $minValue) {
            return $defaultValue;
        }
        if ($value > $maxValue) {
            return $maxValue;
        }
        return $value;
    }
}

if (!function_exists('cc_ngocchung_parse_sort_column')) {
    function cc_ngocchung_parse_sort_column($rawValue)
    {
        $sortMap = array(
            'date' => 'lv002',
            'time' => 'lv003',
            'employee_id' => 'lv001',
            'attendance_type' => 'lv004',
            'source' => 'lv005',
            'camera_ip' => 'lv099',
        );
        $key = strtolower(trim((string)$rawValue));
        if (isset($sortMap[$key])) {
            return $sortMap[$key];
        }
        return 'lv002';
    }
}

if (!function_exists('cc_ngocchung_parse_sort_dir')) {
    function cc_ngocchung_parse_sort_dir($rawValue)
    {
        $key = strtolower(trim((string)$rawValue));
        return $key === 'asc' ? 'ASC' : 'DESC';
    }
}

switch ($vtable) {
    case 'chamcong_ngocchung':
        $db = db_connect();
        if (!$db) {
            $vOutput = cc_ngocchung_error(
                'Khong ket noi duoc database ERP',
                'DB_CONNECT_FAILED',
                500,
                array('db_error' => sof_error())
            );
            break;
        }

        switch ($vfun) {
            case 'pushAttendance':
            case 'push_attendance':
                $employeeIdRaw = $input['employee_id'] ?? $_POST['employee_id'] ?? $_POST['lv001'] ?? '';
                $attendanceDateRaw = $input['attendance_date'] ?? $_POST['attendance_date'] ?? $_POST['lv002'] ?? date('Y-m-d');
                $attendanceTimeRaw = $input['attendance_time'] ?? $_POST['attendance_time'] ?? $_POST['lv003'] ?? date('H:i:s');
                $attendanceTypeRaw = $input['attendance_type'] ?? $_POST['attendance_type'] ?? $_POST['lv004'] ?? 'IN';
                $sourceRaw = $input['source'] ?? $_POST['source'] ?? $_POST['lv005'] ?? 'Camera';
                $cameraIpRaw = $input['camera_ip'] ?? $_POST['camera_ip'] ?? $_POST['lv099'] ?? '';

                $employeeId = trim((string)$employeeIdRaw);
                $attendanceDate = cc_ngocchung_parse_date($attendanceDateRaw, date('Y-m-d'));
                $attendanceTime = cc_ngocchung_parse_time($attendanceTimeRaw, date('H:i:s'));
                $attendanceType = cc_ngocchung_parse_attendance_type($attendanceTypeRaw);
                $source = trim((string)$sourceRaw);
                $cameraIp = trim((string)$cameraIpRaw);

                if ($employeeId === '') {
                    $vOutput = cc_ngocchung_error('Thieu ma nhan vien', 'MISSING_EMPLOYEE_ID', 400);
                    break;
                }
                if ($attendanceDate === '') {
                    $vOutput = cc_ngocchung_error('Ngay cham cong khong hop le (YYYY-MM-DD)', 'INVALID_ATTENDANCE_DATE', 400);
                    break;
                }
                if ($attendanceTime === '') {
                    $vOutput = cc_ngocchung_error('Gio cham cong khong hop le (HH:MM:SS)', 'INVALID_ATTENDANCE_TIME', 400);
                    break;
                }
                if ($source === '') {
                    $source = 'Camera';
                }

                $sql = "INSERT INTO tc_lv0012 (lv001, lv002, lv003, lv004, lv005, lv099) VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = mysqli_prepare($db, $sql);
                if (!$stmt) {
                    $vOutput = cc_ngocchung_error(
                        'Khong the khoi tao truy van ghi cham cong',
                        'PREPARE_INSERT_FAILED',
                        500,
                        array('db_error' => mysqli_error($db))
                    );
                    break;
                }

                mysqli_stmt_bind_param($stmt, 'ssssss', $employeeId, $attendanceDate, $attendanceTime, $attendanceType, $source, $cameraIp);
                $executeOk = mysqli_stmt_execute($stmt);
                $affectedRows = (int)mysqli_stmt_affected_rows($stmt);
                $insertError = mysqli_stmt_error($stmt);
                mysqli_stmt_close($stmt);

                if (!$executeOk || $affectedRows <= 0) {
                    $vOutput = cc_ngocchung_error(
                        'Ghi cham cong that bai tren ERP',
                        'INSERT_ATTENDANCE_FAILED',
                        500,
                        array(
                            'db_error' => $insertError !== '' ? $insertError : mysqli_error($db),
                            'affected_rows' => $affectedRows,
                        )
                    );
                    break;
                }

                $verifySql = "SELECT COUNT(*) AS total FROM tc_lv0012 WHERE lv001 = ? AND lv002 = ? AND lv003 = ? AND lv004 = ?";
                $verifyStmt = mysqli_prepare($db, $verifySql);
                if (!$verifyStmt) {
                    $vOutput = cc_ngocchung_error(
                        'Da ghi cham cong nhung khong the xac thuc sau ghi',
                        'VERIFY_PREPARE_FAILED',
                        500,
                        array('db_error' => mysqli_error($db))
                    );
                    break;
                }

                mysqli_stmt_bind_param($verifyStmt, 'ssss', $employeeId, $attendanceDate, $attendanceTime, $attendanceType);
                $verifyOk = mysqli_stmt_execute($verifyStmt);
                $verifyResult = $verifyOk ? mysqli_stmt_get_result($verifyStmt) : false;
                $verifyRow = $verifyResult ? mysqli_fetch_assoc($verifyResult) : null;
                $matchedRows = (int)($verifyRow['total'] ?? 0);
                $verifyError = mysqli_stmt_error($verifyStmt);
                mysqli_stmt_close($verifyStmt);

                if (!$verifyOk || $matchedRows <= 0) {
                    $vOutput = cc_ngocchung_error(
                        'Khong tim thay ban ghi cham cong sau khi ghi ERP',
                        'VERIFY_ATTENDANCE_FAILED',
                        500,
                        array(
                            'db_error' => $verifyError !== '' ? $verifyError : mysqli_error($db),
                            'matched_rows' => $matchedRows,
                        )
                    );
                    break;
                }

                $vOutput = cc_ngocchung_success('Da day cham cong len ERP thanh cong', array(
                    'data' => array(
                        'employee_id' => $employeeId,
                        'attendance_date' => $attendanceDate,
                        'attendance_time' => $attendanceTime,
                        'attendance_type' => $attendanceType,
                        'source' => $source,
                        'camera_ip' => $cameraIp,
                    ),
                    'verify' => array(
                        'inserted' => true,
                        'matched_rows' => $matchedRows,
                    ),
                ));
                break;

            case 'checkRecentAttendance':
            case 'check_recent_attendance':
                $employeeIdRaw = $input['employee_id'] ?? $_POST['employee_id'] ?? $_POST['lv001'] ?? '';
                $minutesRaw = $input['minutes'] ?? $_POST['minutes'] ?? 10;

                $employeeId = trim((string)$employeeIdRaw);
                $minutes = cc_ngocchung_parse_int($minutesRaw, 10, 1, 1440);
                if ($employeeId === '') {
                    $vOutput = cc_ngocchung_error('Thieu ma nhan vien', 'MISSING_EMPLOYEE_ID', 400);
                    break;
                }

                $cutoffDateTime = date('Y-m-d H:i:s', time() - ($minutes * 60));
                $cutoffDate = substr($cutoffDateTime, 0, 10);
                $cutoffTime = substr($cutoffDateTime, 11, 8);

                $sql = "SELECT COUNT(*) AS total FROM tc_lv0012 WHERE lv001 = ? AND (lv002 > ? OR (lv002 = ? AND lv003 >= ?))";
                $stmt = mysqli_prepare($db, $sql);
                if (!$stmt) {
                    $vOutput = cc_ngocchung_error(
                        'Khong the khoi tao truy van kiem tra cham cong gan day',
                        'PREPARE_RECENT_FAILED',
                        500,
                        array('db_error' => mysqli_error($db))
                    );
                    break;
                }

                mysqli_stmt_bind_param($stmt, 'ssss', $employeeId, $cutoffDate, $cutoffDate, $cutoffTime);
                $executeOk = mysqli_stmt_execute($stmt);
                $result = $executeOk ? mysqli_stmt_get_result($stmt) : false;
                $row = $result ? mysqli_fetch_assoc($result) : null;
                $count = (int)($row['total'] ?? 0);
                $error = mysqli_stmt_error($stmt);
                mysqli_stmt_close($stmt);

                if (!$executeOk) {
                    $vOutput = cc_ngocchung_error(
                        'Khong the kiem tra du lieu cham cong gan day',
                        'CHECK_RECENT_FAILED',
                        500,
                        array('db_error' => $error !== '' ? $error : mysqli_error($db))
                    );
                    break;
                }

                $vOutput = cc_ngocchung_success('Kiem tra du lieu gan day thanh cong', array(
                    'employee_id' => $employeeId,
                    'minutes' => $minutes,
                    'count' => $count,
                    'exists' => $count > 0,
                    'cutoff' => $cutoffDateTime,
                ));
                break;

            case 'getOnlineAttendance':
            case 'get_online_attendance':
                $today = date('Y-m-d');
                $startDateRaw = $input['start_date'] ?? $_POST['start_date'] ?? $today;
                $endDateRaw = $input['end_date'] ?? $_POST['end_date'] ?? $today;
                $employeeIdRaw = $input['employee_id'] ?? $_POST['employee_id'] ?? '';
                $attendanceTypeRaw = $input['attendance_type'] ?? $_POST['attendance_type'] ?? 'all';
                $keywordRaw = $input['keyword'] ?? $_POST['keyword'] ?? '';
                $sortByRaw = $input['sort_by'] ?? $_POST['sort_by'] ?? 'date';
                $sortDirRaw = $input['sort_dir'] ?? $_POST['sort_dir'] ?? 'desc';
                $pageRaw = $input['page'] ?? $_POST['page'] ?? 1;
                $pageSizeRaw = $input['page_size'] ?? $_POST['page_size'] ?? 50;

                $startDate = cc_ngocchung_parse_date($startDateRaw, $today);
                $endDate = cc_ngocchung_parse_date($endDateRaw, $today);
                if ($startDate === '' || $endDate === '') {
                    $vOutput = cc_ngocchung_error('Khoang ngay loc khong hop le (YYYY-MM-DD)', 'INVALID_DATE_RANGE', 400);
                    break;
                }
                if ($endDate < $startDate) {
                    $tmp = $startDate;
                    $startDate = $endDate;
                    $endDate = $tmp;
                }

                $employeeId = trim((string)$employeeIdRaw);
                $keyword = trim((string)$keywordRaw);
                $attendanceType = strtoupper(trim((string)$attendanceTypeRaw));
                if ($attendanceType !== 'IN' && $attendanceType !== 'OUT') {
                    $attendanceType = 'ALL';
                }
                $sortColumn = cc_ngocchung_parse_sort_column($sortByRaw);
                $sortDirection = cc_ngocchung_parse_sort_dir($sortDirRaw);
                $page = cc_ngocchung_parse_int($pageRaw, 1, 1, 1000000);
                $pageSize = cc_ngocchung_parse_int($pageSizeRaw, 50, 1, 500);
                $offset = ($page - 1) * $pageSize;

                $whereClauses = array();
                $whereClauses[] = "lv002 >= '" . sof_escape_string($startDate) . "'";
                $whereClauses[] = "lv002 <= '" . sof_escape_string($endDate) . "'";

                if ($employeeId !== '') {
                    $whereClauses[] = "lv001 = '" . sof_escape_string($employeeId) . "'";
                }

                if ($attendanceType !== 'ALL') {
                    $whereClauses[] = "UPPER(lv004) = '" . sof_escape_string($attendanceType) . "'";
                }

                if ($keyword !== '') {
                    $keywordEsc = sof_escape_string($keyword);
                    $whereClauses[] = "(lv001 LIKE '%" . $keywordEsc . "%' OR lv004 LIKE '%" . $keywordEsc . "%' OR lv005 LIKE '%" . $keywordEsc . "%' OR lv099 LIKE '%" . $keywordEsc . "%')";
                }

                $whereSql = count($whereClauses) > 0 ? ('WHERE ' . implode(' AND ', $whereClauses)) : '';

                $countSql = "SELECT COUNT(*) AS total FROM tc_lv0012 " . $whereSql;
                $countResult = db_query($countSql);
                if (!$countResult) {
                    $vOutput = cc_ngocchung_error(
                        'Khong the dem du lieu cham cong online',
                        'COUNT_ATTENDANCE_FAILED',
                        500,
                        array('db_error' => sof_error())
                    );
                    break;
                }

                $countRow = db_fetch_array($countResult);
                $totalRows = (int)($countRow['total'] ?? 0);

                $dataSql = "SELECT lv001, lv002, lv003, lv004, lv005, lv099 FROM tc_lv0012 " .
                    $whereSql .
                    " ORDER BY " . $sortColumn . " " . $sortDirection . ", lv003 " . $sortDirection .
                    " LIMIT " . (int)$offset . ", " . (int)$pageSize;
                $dataResult = db_query($dataSql);
                if (!$dataResult) {
                    $vOutput = cc_ngocchung_error(
                        'Khong the tai danh sach cham cong online',
                        'LIST_ATTENDANCE_FAILED',
                        500,
                        array('db_error' => sof_error())
                    );
                    break;
                }

                $records = array();
                while ($row = db_fetch_array($dataResult)) {
                    $records[] = array(
                        'employee_id' => $row['lv001'],
                        'attendance_date' => $row['lv002'],
                        'attendance_time' => $row['lv003'],
                        'attendance_type' => strtoupper((string)$row['lv004']),
                        'source' => $row['lv005'],
                        'camera_ip' => $row['lv099'],
                    );
                }

                $totalPages = $pageSize > 0 ? (int)ceil($totalRows / $pageSize) : 1;
                if ($totalPages <= 0) {
                    $totalPages = 1;
                }

                $vOutput = cc_ngocchung_success('Lay du lieu cham cong online thanh cong', array(
                    'records' => $records,
                    'meta' => array(
                        'page' => $page,
                        'page_size' => $pageSize,
                        'total' => $totalRows,
                        'total_pages' => $totalPages,
                    ),
                    'filters' => array(
                        'start_date' => $startDate,
                        'end_date' => $endDate,
                        'employee_id' => $employeeId,
                        'attendance_type' => $attendanceType,
                        'keyword' => $keyword,
                        'sort_by' => strtolower(trim((string)$sortByRaw)),
                        'sort_dir' => strtolower(trim((string)$sortDirRaw)),
                    ),
                ));
                break;

            default:
                $vOutput = cc_ngocchung_error(
                    'Hanh dong chamcong_ngocchung khong hop le',
                    'INVALID_FUNCTION',
                    400,
                    array('allowed' => array('pushAttendance', 'checkRecentAttendance', 'getOnlineAttendance'))
                );
                break;
        }
        break;
}
