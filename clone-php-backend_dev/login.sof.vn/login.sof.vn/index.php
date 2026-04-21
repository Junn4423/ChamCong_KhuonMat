<?php
// error_reporting(E_ALL);
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
header("Content-Type: application/json; charset=UTF-8");
//Neu la login noi bo thi de ipv4 o day, nguoc lai login 1 noi, dich vu noi khac thi ipv4 phai lay tu couchdb
// Include CouchDB functions first
include("../sof/couchdb_functions.php");

$sofUserToken = isset($_SERVER['HTTP_X_SOF_USER_TOKEN']) ? $_SERVER['HTTP_X_SOF_USER_TOKEN'] : '';

if ($sofUserToken === '' || $sofUserToken !== '8c4f2b9a71d6e3c9f0ab42d5e8c1f7a39b6d0e4f1a2c8b7d5e9f3a1c6b4d2e8') {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => 'Unauthorized'
    ]);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);

if (isset($input['method'])) {
    $method = $input['method'];
} elseif (isset($_POST['method'])) {
    $method = $_POST['method'];
} else {
    $method = '';
}


$response = array();

function CodeAutoFill($vLen = 10)
{
    $vStrReturn = "";
    for ($i = 1; $i <= $vLen; $i++) {
        $vStrReturn = $vStrReturn . ASCCodeAuto();
    }
    return $vStrReturn;
}

function ASCCodeAuto()
{
    $vcode = rand(1, 3);
    switch ($vcode) {
        case 1:
            return chr(rand(48, 57));
            break;
        case 2:
            return chr(rand(65, 90));
            break;
        default:
            return chr(rand(97, 122));
            break;
    }
}

function randomChar()
{
    $set = rand(1, 3);
    return chr(
        $set === 1 ? rand(48, 57) : ($set === 2 ? rand(65, 90) : rand(97, 122))
    );
}

function generateToken($length = 16)
{
    $str = '';
    for ($i = 0; $i < $length; $i++) {
        $str .= randomChar();
    }
    return $str;
}

/**
 * Lưu token vào MySQL
 * @param string $vIP IP của MySQL server (lv094)
 * @param string $vDatabase Tên database (lv670)
 * @param string $vUserMySql User MySQL (lv096)
 * @param string $vPassMySql Password MySQL (lv099)
 * @param string $vPort Port MySQL (lv100)
 * @param string $vUserName Tên đăng nhập user
 * @param string $vToken Token cần lưu
 * @param string $vDeviceType Loại thiết bị (web, mobile, desktop)
 * @return bool Kết quả lưu token
 */
function save_token_mysql($vIP, $vDatabase, $vUserMySql, $vPassMySql, $vPort, $vUserName, $vToken, $vDeviceType)
{
    // Tạo câu SQL tùy theo loại thiết bị
    switch (strtolower($vDeviceType)) {
        case "web":
            $vsql = "UPDATE lv_lv0007 SET lv097='$vToken', lv098=NOW() WHERE lv001='$vUserName'";
            break;

        case "mobile":
            $vsql = "UPDATE lv_lv0007 SET lv297='$vToken', lv298=NOW() WHERE lv001='$vUserName'";
            break;

        case "desktop":
            $vsql = "UPDATE lv_lv0007 SET lv397='$vToken', lv398=NOW() WHERE lv001='$vUserName'";
            break;

        default:
            return false;
    }

    // Kết nối MySQL trực tiếp với thông tin từ CouchDB
    //echo "$vIP, $vUserMySql, $vPassMySql, $vDatabase, (int)$vPort";
    $db_link = mysqli_connect($vIP, $vUserMySql, $vPassMySql, $vDatabase, (int)$vPort);

    if (!$db_link) {
        error_log("MySQL Connection Error in save_token_mysql: " . mysqli_connect_error());
        return false;
    }

    // Set charset UTF8
    mysqli_set_charset($db_link, 'utf8');

    // Chạy SQL
    $result = mysqli_query($db_link, $vsql);

    // Đóng kết nối
    mysqli_close($db_link);

    return $result ? true : false;
}
function is_account_expired($expireDate)
{
    if (empty($expireDate)) {
        return false;
    }
    try {
        $tz = new DateTimeZone('Asia/Ho_Chi_Minh');
        $today = new DateTime('today', $tz);
        $expire = new DateTime($expireDate, $tz);
        return $expire < $today;
    } catch (Exception $e) {
        return false;
    }
}

function get_days_remaining($expireDate)
{
    if (empty($expireDate)) {
        return null;
    }
    try {
        $tz = new DateTimeZone('Asia/Ho_Chi_Minh');
        $today = new DateTime('today', $tz);
        $expire = new DateTime($expireDate, $tz);
        $diff = $today->diff($expire);
        $days = (int)$diff->format('%r%a');
        return $days;
    } catch (Exception $e) {
        return null;
    }
}


switch ($method) {
    case 'loginUser': 
{
            $response = [
                'username' => '',
                'right' => '',
                'token' => '',
                'table' => null,
                'user_type' => '',
                'domain' => '',
                'IPv4' => '',
                'method' => '',
                'deviceType' => '',
                'status' => 1004,
            ];

            if (isset($input['username'])) {
                $username = $input['username'];
            } elseif (isset($_POST['username'])) {
                $username = $_POST['username'];
            } else {
                $username = '';
            }
            if (isset($input['password'])) {
                $password = $input['password'];
            } elseif (isset($_POST['password'])) {
                $password = $_POST['password'];
            } else {
                $password = '';
            }
            if (isset($input['deviceType'])) {
                $deviceType = trim($input['deviceType']);
            } elseif (isset($_POST['deviceType'])) {
                $deviceType = trim($_POST['deviceType']);
            } else {
                $deviceType = 'mobile'; // Mặc định là mobile
            }

            if (isset($input['TYPE-SOF-CODE'])) {
                $TypeCode = trim($input['TYPE-SOF-CODE']);
            } elseif (isset($_POST['TYPE-SOF-CODE'])) {
                $TypeCode = trim($_POST['TYPE-SOF-CODE']);
            } else {
                $TypeCode = 'mobile'; // Mặc định là mobile
            }

            if (empty($username)) {
                $response['status'] = 1001;
            } elseif (empty($password)) {
                $response['status'] = 1002;
            } else {
                // Cấu hình thời gian
                date_default_timezone_set('Asia/Ho_Chi_Minh');
                $vDate = date('Y-m-d');
                $vTime = date('H:i:s');
                // Xác thực qua CouchDB trước
                $vKQCouchDB = couchdbLogin($username, $password, $TypeCode,$deviceType);
                if ($vKQCouchDB['success']) {
                    $expireDate = $vKQCouchDB['response']['lv705'] ?? null;
                    $daysRemaining = get_days_remaining($expireDate);
                    $warningThreshold = 7;
                    $expireWarning = $daysRemaining !== null && $daysRemaining <= $warningThreshold;
                    if (is_account_expired($expireDate)) {
                        $response = [
                            'status' => 3001,
                            'message' => 'Tài khoản đã hết hạn sử dụng',
                            'expireDate' => $expireDate,
                            'daysRemaining' => $daysRemaining,
                            'expireWarning' => true,
                            'orderId' => $vKQCouchDB['response']['lv667'] ?? null,
                            'userCode' => $vKQCouchDB['response']['lv040'] ?? null,
			    'dbName' => $db_mysql,
                        ];
                        break;
                    }
                    // Lấy thông tin máy chủ từ CouchDB
                    $methodConnect = $vKQCouchDB['response']['method']; // phương thức kết nối (http/https)
                    $db_mysql = $vKQCouchDB['response']['dbName'] ?? ''; // database mysql (dbName)
                    $ipv4 = $vKQCouchDB['response']['IPv4'];
                    $dbUser = $vKQCouchDB['response']['user'];
                    $dbPass = $vKQCouchDB['response']['pass'];
                    $dbPort = $vKQCouchDB['response']['port'];
                    $couchDBOk= $vKQCouchDB['response']['couchDBOk'];
                    // Thiết lập thông số DB động từ CouchDB trước khi load config
            
                    // Xác thực loại thiết bị
                    if (!in_array(strtolower($deviceType), ['mobile', 'web', 'desktop'])) {
                        echo json_encode(array("success" => false, "message" => "Device Type not recognized. [Mobile, Web, Desktop]"));
                        exit();
                    }
                    if (empty($username)) {
                        $response['status'] = 1001;
                    } elseif (empty($password)) {
                        $response['status'] = 1002;
                    } else {
                        $token = generateToken();	
			//echo" $ipv4, $db_mysql, $dbUser, $dbPass, $dbPort, $username, $token, $deviceType";
                        if (save_token_mysql($ipv4, $db_mysql, $dbUser, $dbPass, $dbPort, $username, $token, $deviceType)) {
// Luu token vao CouchDB de services co the verify token tu header FE
                            $isSavedTokenCouch = saveToken($username, $token, $deviceType,$couchDBOk);
                            if (!$isSavedTokenCouch) {
                                $response = [
                                    'status' => 5002,
                                    'message' => 'Save token to CouchDB failed',
                                ];
                                break;
                            }
                            // Ưu tiên quyền từ CouchDB, không phụ thuộc MySQL để lấy lv003
                            $rightFromCouch = $vKQCouchDB['response']['lv003'] ?? '';

                            // Giữ logic cũ: lấy bàn từ MySQL
                            //$userInfo = get_user_info($username);
                            $tableFromMysql = $db_mysql;
                            $response = [
                                'username' => $username,
                                'right' => $rightFromCouch,
                                'token' => $token,
                                'table' => $tableFromMysql,
                                'expireDate' => $vKQCouchDB['response']['lv705'] ?? null,
                                'lv705' => $vKQCouchDB['response']['lv705'] ?? null,
                                'daysRemaining' => $daysRemaining,
                                'expireWarning' => $expireWarning,
                                'orderId' => $vKQCouchDB['response']['lv667'] ?? null,
                                'userCode' => $vKQCouchDB['response']['lv040'] ?? null,
                                'domain' => $vKQCouchDB['response']['domain'] ?? null,
                                'status' => 2000,
				'dbName' => $db_mysql,
                            ];
                            // Lay database name tu CouchDB (uu tien lv670)
                            $db_mysql_log = $vKQCouchDB["response"]["dbName"] ?? "";

                            // Set session (quan trong)
                            $_SESSION["USER_NAME"] = $username;
                            $_SESSION["USER_RIGHT"] = $rightFromCouch;
                            $_SESSION["DATABASE_NAME"] = $db_mysql_log;
                            $_SESSION["SOFIP"] = $lvIpClient;
                            $_SESSION["SOFMAC"] = $lvmac;
                            // Ghi log dang nhap vao MongoDB
                            $mongoFunctionPath = $currentDir . "/../mongodb/mongodb_functions.php";             
               if (file_exists($mongoFunctionPath)) {
                                require_once($mongoFunctionPath);
                                if (function_exists("insertLoginLogToMongo")) {
                                    insertLoginLogToMongo(
                                        $username,
                                        $vDate . " " . $vTime,
                                        0,
                                        $lvIpClient,
                                        $lvmac,
                                        $deviceType,
                                        $token,
                                        $db_mysql_log
                                    );
                                }
                            }

                            //Logtime($username, GetServerDate(), GetServerTime(), 0, $ipClient, $mac);
                        }
                        $vKQCouchDBLoginLog = logTimeCouchDB($username, $vDate, $vTime, 0, $lvIpClient, $lvmac, $deviceType, $token);
                    }
                }
            }
            break;
        }
    case 'checkLogin': {
            $response = [
                'status' => false,
            ];

            if (isset($input['username'])) {
                $username = $input['username'];
            } elseif (isset($_POST['username'])) {
                $username = $_POST['username'];
            } else {
                $username = '';
            }

            if (isset($input['token'])) {
                $token = $input['token'];
            } elseif (isset($_POST['token'])) {
                $token = $_POST['token'];
            } else {
                $token = '';
            }


            $sql = "SELECT lv001 FROM lv_lv0007 WHERE lv001 = '$username' AND lv297 = '$token' AND lv299 = 0";
            $result = db_query($sql);
            if ($result && db_num_rows($result) > 0) {
                $response = [
                    'status' => true,
                ];
            }
            break;
        }
    default: {
        }
}

echo json_encode($response);
ob_end_flush();
