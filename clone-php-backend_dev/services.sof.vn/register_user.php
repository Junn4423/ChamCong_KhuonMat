<?php
// FILE: register_user.php

// 1. Không session_start() vì index.php đã làm rồi.
// session_start();

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, X-SOF-USER-TOKEN");

$SOF_USER_TOKEN = 'SOF2025DEVELOPER';

// Ensure shared ERP helpers are loaded (db_connect, sof_escape_string, etc.)
$sofRoot = realpath(__DIR__ . '/../../..');
if ($sofRoot && is_dir($sofRoot)) {
    if (!function_exists('db_connect')) {
        $cfg = $sofRoot . '/soft/config.php';
        if (file_exists($cfg)) {
            require_once $cfg;
        }
    }
    if (!function_exists('sof_escape_string')) {
        $fn = $sofRoot . '/soft/function.php';
        if (file_exists($fn)) {
            require_once $fn;
        }
    }
}

// Minimal fallback to avoid fatal if helper still missing
if (!function_exists('sof_escape_string')) {
    function sof_escape_string($str)
    {
        return addslashes($str ?? '');
    }
}

// Hard guard: fail fast with JSON if core DB helpers are unavailable
if (!function_exists('db_connect')) {
    echo json_encode([
        'success' => false,
        'status' => 500,
        'message' => 'Thiếu hàm db_connect trong môi trường ERP',
        'errorType' => 'missing_db_connect'
    ]);
    exit();
}

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

// Check Token Bảo Mật
// $sofUserToken = isset($_SERVER['HTTP_X_SOF_USER_TOKEN']) ? $_SERVER['HTTP_X_SOF_USER_TOKEN'] : '';

// if ($sofUserToken === '' || $sofUserToken != $SOF_USER_TOKEN) {
//     echo json_encode([
//         'status' => 403,
//         'message' => 'Forbidden: Invalid Token',
//     ]);
//     exit();
// }

error_reporting(E_ALL);
ini_set('log_errors', 1);
// ini_set('display_errors', 1); 

try {
    $lvIpClient = $_SERVER['REMOTE_ADDR'];

    // Validate DB connection early to surface errors clearly
    if (!isset($db) && function_exists('db_connect')) {
        $db = db_connect();
    }
    if (!isset($db) || $db === null) {
        echo json_encode([
            'success' => false,
            'status' => 500,
            'message' => 'Kết nối cơ sở dữ liệu thất bại',
            'errorType' => 'db_connect_failed'
        ]);
        exit();
    }
    
    // 2. Tắt đoạn lệnh hệ thống gây treo máy (ARP)
    // ob_start(); 
    // system('arp ' . $lvIpClient . ' -a'); 
    // $mycom = ob_get_contents(); 
    // ob_clean(); 
    
    // Giả lập MAC Address để code phía dưới không bị lỗi biến undefined
    $lvmac = "00-00-00-00-00-00"; 
    if (!isset($db) && function_exists('db_connect')) {
        $db = db_connect();
    }
    
    // Nếu vẫn chưa có $db (trường hợp hiếm), ta ép kết nối lại (fallback)
    if (!isset($db) || $db === null) {
        // Chỉ include nếu function db_connect chưa tồn tại
        if (!function_exists('db_connect')) {
        }
        if (function_exists('db_connect')) {
            $db = db_connect();
        }
    }

    // --- CÁC HÀM HỖ TRỢ ---
    // Fallback cho các hàm thiếu trong soft/function.php
    if (!function_exists('GetServerDate')) {
        function GetServerDate() {
            return date('Y-m-d');
        }
    }
    
    if (!function_exists('GetServerTime')) {
        function GetServerTime() {
            return date('H:i:s');
        }
    }
    
    if (!function_exists('Logtime')) {
        function Logtime($userId, $date, $time, $action, $ip, $mac) {
            // Fallback: không làm gì nếu hàm không tồn tại
            error_log("Logtime called for user: $userId at $date $time from $ip");
            return true;
        }
    }
    
    if (!function_exists('CodeAutoFill')) {
        function CodeAutoFill($vLen = 10)
        {
            $vStrReturn = "";
            for ($i = 1; $i <= $vLen; $i++) {
                $vStrReturn .= ASCCodeAuto();
            }
            return $vStrReturn;
        }
    }

    if (!function_exists('ASCCodeAuto')) {
        function ASCCodeAuto()
        {
            $vcode = rand(1, 3);
            switch ($vcode) {
                case 1:
                    return chr(rand(48, 57)); // 0-9
                case 2:
                    return chr(rand(65, 90)); // A-Z
                default:
                    return chr(rand(97, 122)); // a-z
            }
        }
    }

    // Safe DB helper to surface query errors instead of fatal warnings
    if (!function_exists('db_query_safe')) {
        function db_query_safe($sql)
        {
            // Remember last SQL for debugging when exceptions bubble up
            $GLOBALS['__LAST_SQL__'] = $sql;
            $result = db_query($sql);
            if ($result === false) {
                $err = function_exists('sof_error') ? sof_error() : 'unknown_db_error';
                error_log("register_user.php SQL error: " . $err . " | SQL: " . $sql);
                throw new Exception("Lỗi cơ sở dữ liệu: " . $err);
            }
            return $result;
        }
    }

    // --- XỬ LÝ INPUT ---
    $input = json_decode(file_get_contents('php://input'), true);

    // Check if this is a table-based API call (for nc_cart, sl_lv0006, sl_lv0007, etc.)
    $vtable = $input['table'] ?? $_POST['table'] ?? $_GET['vtable'] ?? $_GET['table'] ?? '';
    $vfun = $input['func'] ?? $_POST['func'] ?? $_GET['vfun'] ?? $_GET['func'] ?? 
            $input['fun'] ?? $_POST['fun'] ?? $_GET['fun'] ?? ''; // Also support 'fun' as alias
    
    if ($vtable !== '') {
        // Route to ngocchung.php for table-based operations
        $vOutput = [];
        include_once(__DIR__ . '/ngocchung.php');
        
        // Output result from ngocchung.php
        echo json_encode($vOutput, JSON_UNESCAPED_UNICODE);
        exit();
    }

    if (isset($input['method'])) {
        $method = $input['method'];
    } elseif (isset($_POST['method'])) {
        $method = $_POST['method'];
    } else {
        $method = '';
    }

    $response = array();

    switch ($method) {
        case 'registerCustomer': {
                $response = [
                    'username' => '',
                    'token' => '',
                    'name' => '',
                    'role' => '',
                    'status' => 0,
                    'message' => '',
                    'errorType' => 'validation'
                ];

                // Get input parameters
                $username = $input['username'] ?? $_POST['username'] ?? '';
                $password = $input['password'] ?? $_POST['password'] ?? '';
                $email = $input['email'] ?? $_POST['email'] ?? '';
                $phone = $input['phone'] ?? $_POST['phone'] ?? '';
                $companyName = $input['companyName'] ?? $_POST['companyName'] ?? '';

                // Comprehensive validation
                if (empty($username)) {
                    $response['message'] = "Tên đăng nhập không được để trống";
                    $response['errorType'] = "missing_username";
                    $response['status'] = 1001;
                } elseif (strlen($username) < 3) {
                    $response['message'] = "Tên đăng nhập phải có ít nhất 3 ký tự";
                    $response['errorType'] = "username_too_short";
                    $response['status'] = 1001;
                } elseif (strlen($username) > 50) {
                    $response['message'] = "Tên đăng nhập quá dài (tối đa 50 ký tự)";
                    $response['errorType'] = "username_too_long";
                    $response['status'] = 1001;
                } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
                    $response['message'] = "Tên đăng nhập chỉ chứa chữ, số và dấu gạch dưới";
                    $response['errorType'] = "username_invalid_chars";
                    $response['status'] = 1001;
                } elseif (empty($email)) {
                    $response['message'] = "Email không được để trống";
                    $response['errorType'] = "missing_email";
                    $response['status'] = 1003;
                } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $response['message'] = "Địa chỉ email không hợp lệ";
                    $response['errorType'] = "invalid_email";
                    $response['status'] = 1003;
                } elseif (empty($password)) {
                    $response['message'] = "Mật khẩu không được để trống";
                    $response['errorType'] = "missing_password";
                    $response['status'] = 1002;
                } elseif (strlen($password) < 6) {
                    $response['message'] = "Mật khẩu phải có ít nhất 6 ký tự";
                    $response['errorType'] = "password_too_short";
                    $response['status'] = 1002;
                } elseif (strlen($password) > 100) {
                    $response['message'] = "Mật khẩu quá dài (tối đa 100 ký tự)";
                    $response['errorType'] = "password_too_long";
                    $response['status'] = 1002;
                } else {
                    // Check if customer ID or email already exists
                    // Đảm bảo escape string để tránh SQL Injection
                    $safeUser = sof_escape_string($username);
                    $safeEmail = sof_escape_string($email);

                    $checkUsernameSql = "SELECT lv001 FROM sl_lv0001 WHERE lv001 = '" . $safeUser . "'";
                    $checkEmailSql = "SELECT lv015 FROM sl_lv0001 WHERE lv015 = '" . $safeEmail . "'";

                    $usernameResult = db_query_safe($checkUsernameSql);
                    $emailResult = db_query_safe($checkEmailSql);

                    if (db_num_rows($usernameResult) > 0 && db_num_rows($emailResult) > 0) {
                        $response['message'] = "Cả tên đăng nhập và email đều đã được sử dụng";
                        $response['errorType'] = "both_exist";
                        $response['status'] = 1004;
                    } elseif (db_num_rows($usernameResult) > 0) {
                        $response['message'] = "Tên đăng nhập '" . $username . "' đã được sử dụng";
                        $response['errorType'] = "username_exists";
                        $response['status'] = 1004;
                    } elseif (db_num_rows($emailResult) > 0) {
                        $response['message'] = "Email '" . $email . "' đã được đăng ký";
                        $response['errorType'] = "email_exists";
                        $response['status'] = 1004;
                    } else {
                        // Generate password hash and token
                        $passwordHash = md5($password);
                        $token = CodeAutoFill(32);
                        $currentDateTime = GetServerDate() . ' ' . GetServerTime();

                        // Insert new customer
                        $sql = "INSERT INTO sl_lv0001 (
                        lv001, lv002, lv010, lv011, lv015, lv022, lv023, lv024, lv025, lv099, lv199, lv297, lv298
                    ) VALUES (
                        '" . $safeUser . "',
                        '" . sof_escape_string($companyName) . "',
                        '" . sof_escape_string($phone) . "',
                        '" . sof_escape_string($phone) . "',
                        '" . $safeEmail . "',
                        '1',
                        '1',
                        '$currentDateTime',
                        '" . $safeUser . "',
                        '0',
                        '" . $passwordHash . "',
                        '" . $token . "',
                        now()
                    )";

                        $result = db_query_safe($sql);

                        if ($result) {
                            $response = [
                                'username' => $username,
                                'token' => $token,
                                'name' => $companyName ?: $username,
                                'role' => 'customer',
                                'email' => $email,
                                'phone' => $phone,
                                'status' => 2000,
                                'message' => "Đăng ký tài khoản thành công! Chào mừng bạn tham gia hệ thống.",
                                'errorType' => "success"
                            ];

                            // Log successful registration
                            Logtime($username, GetServerDate(), GetServerTime(), 0, $lvIpClient, $lvmac);
                            error_log("Successful registration: " . $username . " - " . $email);
                        } else {
                            $response['message'] = "Không thể tạo tài khoản do lỗi hệ thống. Vui lòng liên hệ quản trị viên.";
                            $response['errorType'] = "database_error";
                            $response['status'] = 1005;
                            error_log("Database insert failed for user: " . $username);
                        }
                    }
                }
                break;
            }
        case 'facebookLogin': {
                $response = [
                    'username' => '',
                    'token' => '',
                    'name' => '',
                    'role' => '',
                    'email' => '',
                    'phone' => '',
                    'status' => 0,
                    'message' => '',
                    'errorType' => 'validation'
                ];

                $facebookId = $input['facebookId'] ?? $_POST['facebookId'] ?? '';
                $emailRaw = $input['email'] ?? $_POST['email'] ?? '';
                $nameRaw = $input['name'] ?? $_POST['name'] ?? '';
                $phoneRaw = $input['phone'] ?? $_POST['phone'] ?? '';

                $email = strtolower(trim($emailRaw));
                $name = trim($nameRaw);
                $phone = trim($phoneRaw);

                if (empty($facebookId)) {
                    $response['message'] = "Thiếu mã định danh Facebook";
                    $response['errorType'] = "missing_facebook_id";
                    $response['status'] = 1010;
                    break;
                }

                if (empty($email)) {
                    $response['message'] = "Không lấy được email từ Facebook";
                    $response['errorType'] = "missing_email";
                    $response['status'] = 1003;
                    break;
                }

                $safeEmail = sof_escape_string($email);

                $existingSql = "SELECT lv001, lv002, lv010, lv015, lv297 FROM sl_lv0001 WHERE lv015 = '" . $safeEmail . "' AND lv099 = 0 LIMIT 1";
                $existingResult = db_query_safe($existingSql);

                if ($existingResult && db_num_rows($existingResult) > 0) {
                    $row = db_fetch_array($existingResult);
                    $username = $row['lv001'];
                    $token = $row['lv297'];

                    if ($token == '' || $token == null) {
                        $token = CodeAutoFill(32);
                    }

                    $safeName = sof_escape_string($name == '' ? $row['lv002'] : $name);
                    $safePhone = sof_escape_string($phone == '' ? $row['lv010'] : $phone);

                    $updateSql = "UPDATE sl_lv0001 SET lv002 = '" . $safeName . "', lv010 = '" . $safePhone . "', lv011 = '" . $safePhone . "', lv297 = '" . $token . "', lv298 = now() WHERE lv001 = '" . $username . "'";
                    db_query_safe($updateSql);

                    $response = [
                        'username' => $username,
                        'token' => $token,
                        'name' => $safeName != '' ? $safeName : $username,
                        'role' => 'customer',
                        'email' => $email,
                        'phone' => $safePhone,
                        'status' => 2000,
                        'message' => "Đăng nhập Facebook thành công",
                        'errorType' => "success"
                    ];

                    Logtime($username, GetServerDate(), GetServerTime(), 0, $lvIpClient, $lvmac);
                } else {
                    $baseUsername = preg_replace('/[^a-zA-Z0-9_]/', '_', (strpos($email, '@') !== false ? substr($email, 0, strpos($email, '@')) : $email));
                    if ($baseUsername == '' || $baseUsername == null) {
                        $baseUsername = 'facebook_user';
                    }

                    $username = $baseUsername;
                    $suffix = 1;
                    while (true) {
                        $checkSql = "SELECT lv001 FROM sl_lv0001 WHERE lv001 = '" . $username . "'";
                        $checkResult = db_query_safe($checkSql);
                        if (!$checkResult || db_num_rows($checkResult) == 0) {
                            break;
                        }
                        $username = $baseUsername . $suffix;
                        $suffix++;
                        if ($suffix > 50) {
                            $username = $baseUsername . CodeAutoFill(4);
                            break;
                        }
                    }

                    $safeUsername = sof_escape_string($username);
                    $safeName = sof_escape_string($name == '' ? $username : $name);
                    $safePhone = sof_escape_string($phone);
                    $passwordHash = md5(CodeAutoFill(12));
                    $token = CodeAutoFill(32);
                    $currentDateTime = GetServerDate() . ' ' . GetServerTime();

                    $insertSql = "INSERT INTO sl_lv0001 (
                        lv001, lv002, lv010, lv011, lv015, lv022, lv023, lv024, lv025, lv099, lv199, lv297, lv298
                    ) VALUES (
                        '" . $safeUsername . "',
                        '" . $safeName . "',
                        '" . $safePhone . "',
                        '" . $safePhone . "',
                        '" . $safeEmail . "',
                        '1',
                        '1',
                        '" . $currentDateTime . "',
                        '" . $safeUsername . "',
                        '0',
                        '" . $passwordHash . "',
                        '" . $token . "',
                        now()
                    )";

                    $result = db_query_safe($insertSql);

                    if ($result) {
                        $response = [
                            'username' => $safeUsername,
                            'token' => $token,
                            'name' => $safeName,
                            'role' => 'customer',
                            'email' => $email,
                            'phone' => $safePhone,
                            'status' => 2000,
                            'message' => "Đăng nhập Facebook thành công",
                            'errorType' => "success"
                        ];

                        Logtime($safeUsername, GetServerDate(), GetServerTime(), 0, $lvIpClient, $lvmac);
                    } else {
                        $response['message'] = "Không thể tạo tài khoản Facebook, vui lòng thử lại";
                        $response['errorType'] = "database_error";
                        $response['status'] = 1005;
                    }
                }
                break;
            }
        case 'googleLogin': {
                $response = [
                    'username' => '',
                    'token' => '',
                    'name' => '',
                    'role' => '',
                    'email' => '',
                    'phone' => '',
                    'status' => 0,
                    'message' => '',
                    'errorType' => 'validation'
                ];

                $googleId = $input['googleId'] ?? $_POST['googleId'] ?? '';
                $emailRaw = $input['email'] ?? $_POST['email'] ?? '';
                $nameRaw = $input['name'] ?? $_POST['name'] ?? '';
                $phoneRaw = $input['phone'] ?? $_POST['phone'] ?? '';

                $email = strtolower(trim($emailRaw));
                $name = trim($nameRaw);
                $phone = trim($phoneRaw);

                if (empty($googleId)) {
                    $response['message'] = "Thiếu mã định danh Google";
                    $response['errorType'] = "missing_google_id";
                    $response['status'] = 1010;
                    break;
                }

                if (empty($email)) {
                    $response['message'] = "Không lấy được email từ Google";
                    $response['errorType'] = "missing_email";
                    $response['status'] = 1003;
                    break;
                }

                $safeEmail = sof_escape_string($email);

                // Tìm theo email
                $existingSql = "SELECT lv001, lv002, lv010, lv015, lv297 FROM sl_lv0001 WHERE lv015 = '" . $safeEmail . "' AND lv099 = 0 LIMIT 1";
                $existingResult = db_query_safe($existingSql);

                if ($existingResult && db_num_rows($existingResult) > 0) {
                    $row = db_fetch_array($existingResult);
                    $username = $row['lv001'];
                    $token = $row['lv297'];

                    if ($token == '' || $token == null) {
                        $token = CodeAutoFill(32);
                    }

                    $safeName = sof_escape_string($name == '' ? $row['lv002'] : $name);
                    $safePhone = sof_escape_string($phone == '' ? $row['lv010'] : $phone);

                    $updateSql = "UPDATE sl_lv0001 SET lv002 = '" . $safeName . "', lv010 = '" . $safePhone . "', lv011 = '" . $safePhone . "', lv297 = '" . $token . "', lv298 = now() WHERE lv001 = '" . $username . "'";
                    db_query_safe($updateSql);

                    $response = [
                        'username' => $username,
                        'token' => $token,
                        'name' => $safeName != '' ? $safeName : $username,
                        'role' => 'customer',
                        'email' => $email,
                        'phone' => $safePhone,
                        'status' => 2000,
                        'message' => "Đăng nhập Google thành công",
                        'errorType' => "success"
                    ];

                    Logtime($username, GetServerDate(), GetServerTime(), 0, $lvIpClient, $lvmac);
                } else {
                    // Tạo username từ email, tránh trùng
                    $baseUsername = preg_replace('/[^a-zA-Z0-9_]/', '_', (strpos($email, '@') !== false ? substr($email, 0, strpos($email, '@')) : $email));
                    if ($baseUsername == '' || $baseUsername == null) {
                        $baseUsername = 'google_user';
                    }

                    $username = $baseUsername;
                    $suffix = 1;
                    while (true) {
                        $checkSql = "SELECT lv001 FROM sl_lv0001 WHERE lv001 = '" . $username . "'";
                        $checkResult = db_query_safe($checkSql);
                        if (!$checkResult || db_num_rows($checkResult) == 0) {
                            break;
                        }
                        $username = $baseUsername . $suffix;
                        $suffix++;
                        if ($suffix > 50) {
                            $username = $baseUsername . CodeAutoFill(4);
                            break;
                        }
                    }

                    $safeUsername = sof_escape_string($username);
                    $safeName = sof_escape_string($name == '' ? $username : $name);
                    $safePhone = sof_escape_string($phone);
                    $passwordHash = md5(CodeAutoFill(12));
                    $token = CodeAutoFill(32);
                    $currentDateTime = GetServerDate() . ' ' . GetServerTime();

                    $insertSql = "INSERT INTO sl_lv0001 (
                        lv001, lv002, lv010, lv011, lv015, lv022, lv023, lv024, lv025, lv099, lv199, lv297, lv298
                    ) VALUES (
                        '" . $safeUsername . "',
                        '" . $safeName . "',
                        '" . $safePhone . "',
                        '" . $safePhone . "',
                        '" . $safeEmail . "',
                        '1',
                        '1',
                        '" . $currentDateTime . "',
                        '" . $safeUsername . "',
                        '0',
                        '" . $passwordHash . "',
                        '" . $token . "',
                        now()
                    )";

                    $result = db_query_safe($insertSql);

                    if ($result) {
                        $response = [
                            'username' => $safeUsername,
                            'token' => $token,
                            'name' => $safeName,
                            'role' => 'customer',
                            'email' => $email,
                            'phone' => $safePhone,
                            'status' => 2000,
                            'message' => "Đăng nhập Google thành công",
                            'errorType' => "success"
                        ];

                        Logtime($safeUsername, GetServerDate(), GetServerTime(), 0, $lvIpClient, $lvmac);
                    } else {
                        $response['message'] = "Không thể tạo tài khoản Google, vui lòng thử lại";
                        $response['errorType'] = "database_error";
                        $response['status'] = 1005;
                    }
                }
                break;
            }
        case 'login': {
                $response = [
                    'username' => '',
                    'token' => '',
                    'name' => '',
                    'role' => '',
                    'email' => '',
                    'phone' => '',
                    'status' => 0,
                    'message' => '',
                    'errorType' => 'validation'
                ];

                $username = $input['username'] ?? $_POST['username'] ?? '';
                $password = $input['password'] ?? $_POST['password'] ?? '';

                // Validation
                if (empty($username)) {
                    $response['message'] = "Tên đăng nhập hoặc email không được để trống";
                    $response['errorType'] = "missing_username";
                    $response['status'] = 1001;
                } elseif (empty($password)) {
                    $response['message'] = "Mật khẩu không được để trống";
                    $response['errorType'] = "missing_password";
                    $response['status'] = 1002;
                } else {
                    // Determine if username is email or username
                    $isEmail = filter_var($username, FILTER_VALIDATE_EMAIL);
                    $field = $isEmail ? 'lv015' : 'lv001';
                    
                    $safeUser = sof_escape_string($username);
                    $passwordHash = md5($password);

                    // Check if user exists and password matches
                    $sql = "SELECT lv001, lv002, lv010, lv015, lv297 FROM sl_lv0001 WHERE " . $field . " = '" . $safeUser . "' AND lv199 = '" . $passwordHash . "' AND lv099 = 0";
                    $result = db_query_safe($sql);

                    if ($result && db_num_rows($result) > 0) {
                        $row = db_fetch_array($result);

                        // Generate new token if needed or use existing
                        $token = $row['lv297'];
                        if (empty($token)) {
                            $token = CodeAutoFill(32);
                            // Update token in database
                            $updateSql = "UPDATE sl_lv0001 SET lv297 = '" . $token . "', lv298 = now() WHERE lv001 = '" . $row['lv001'] . "'";
                            db_query_safe($updateSql);
                        }

                        $response = [
                            'username' => $row['lv001'],
                            'token' => $token,
                            'name' => $row['lv002'] ?: $row['lv001'],
                            'role' => 'customer',
                            'email' => $row['lv015'],
                            'phone' => $row['lv010'],
                            'status' => 2000,
                            'message' => "Đăng nhập thành công!",
                            'errorType' => "success"
                        ];

                        // Log successful login
                        Logtime($row['lv001'], GetServerDate(), GetServerTime(), 0, $lvIpClient, $lvmac);
                        error_log("Successful login: " . $row['lv001']);
                    } else {
                        $response['message'] = "Tên đăng nhập hoặc mật khẩu không đúng";
                        $response['errorType'] = "invalid_credentials";
                        $response['status'] = 1006;
                        error_log("Failed login attempt for user: " . $username);
                    }
                }
                break;
            }
        case 'checkLogin': {
                $response = [
                    'status' => false,
                ];

                $username = $input['username'] ?? $_POST['username'] ?? '';
                $token = $input['token'] ?? $_POST['token'] ?? '';
                
                $safeUser = sof_escape_string($username);
                $safeToken = sof_escape_string($token);

                $sql = "SELECT lv001 FROM sl_lv0001 WHERE lv001 = '$safeUser' AND lv297 = '$safeToken'";
                $result = db_query_safe($sql);
                if ($result && db_num_rows($result) > 0) {
                    $response = [
                        'status' => true,
                    ];
                }
                break;
            }
        default: {
                $response = [
                    'status' => 0,
                    'message' => 'Invalid method',
                    'errorType' => 'invalid_method'
                ];
            }
    }

    // Prepare comprehensive JSON response
    $finalResponse = array(
        "status" => isset($response['status']) ? $response['status'] : 0,
        "success" => isset($response['status']) && $response['status'] == 2000,
        "data" => $response,
        "message" => isset($response['message']) ? $response['message'] : '',
        "errorType" => isset($response['errorType']) ? $response['errorType'] : 'unknown',
        "timestamp" => date('Y-m-d H:i:s'),
        "requestId" => uniqid('reg_')
    );

    echo json_encode($finalResponse, JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    // Enhanced error handling with logging
    error_log("Registration Exception: " . $e->getMessage() . " at " . $e->getFile() . ":" . $e->getLine());
    http_response_code(500);

    $response = array(
        "status" => 0,
        "success" => false,
        "data" => array(),
        "message" => "Hệ thống đang bảo trì. Vui lòng thử lại sau ít phút.",
        "errorType" => "system_error",
        "errorMessage" => $e->getMessage(),
        "errorFile" => basename($e->getFile()),
        "errorLine" => $e->getLine(),
        "lastSql" => isset($GLOBALS['__LAST_SQL__']) ? $GLOBALS['__LAST_SQL__'] : null,
        "timestamp" => date('Y-m-d H:i:s'),
        "requestId" => uniqid('err_')
    );
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
}

// 4. Không gọi ob_end_flush() vì không có ob_start()
// ob_end_flush(); 
?>