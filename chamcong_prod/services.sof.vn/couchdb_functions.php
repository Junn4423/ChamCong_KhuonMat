<?php
// ============ CouchDB Functions ============
// Cấu hình CouchDB

// $couchHost = '127.0.0.1';
// $couchPort = '5984';
// $couchUser = 'root';
// $couchPass = 'rootsof';
// $couchDB = 'couchdb182';


$couchHost = '192.168.1.20';
$couchPort = '5984';
$couchUser = 'admin';
$couchPass = 'rootsof';
$couchDB = 'couchdb182';

$useTable = 'lv_lv0066';
$serverTable = 'lv_lv0067';
// Bảng user dùng cho các hàm lookup token
$vUserTable = $useTable;

function makeCouchRequest($url, $method = 'GET', $data = null)
{
  global $couchHost, $couchPort;

  $curl = curl_init();
  $options = array(
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => array(
      'Content-Type: application/json',
      'Referer: http://' . $couchHost . ':' . $couchPort
    )
  );

  if ($method === 'GET') {
    $options[CURLOPT_HTTPGET] = true;
  } else if ($method === 'PUT') {
    $options[CURLOPT_CUSTOMREQUEST] = 'PUT';
    $options[CURLOPT_POSTFIELDS] = json_encode($data);
  } else if ($method === 'POST') {
    $options[CURLOPT_CUSTOMREQUEST] = 'POST';
    $options[CURLOPT_POSTFIELDS] = json_encode($data);
  }
  curl_setopt_array($curl, $options);
  $response = curl_exec($curl);
  /*if ($response === false) {
      echo "CURL ERROR: " . curl_error($curl);
  }*/
  $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
  return array('code' => $httpCode, 'body' => $response);
}

/**
 * Tìm user dựa vào token (chỉ cần token, không cần username)
 * @param string $vToken Token cần tìm
 * 
 * @return array Kết quả: ['success' => bool, 'username' => string, 'deviceType' => string, 'userData' => array]
 */
function findUserByToken($vToken)
{
  global $couchHost, $couchPort, $couchUser, $couchPass, $couchDB, $vUserTable;
  $couchURL = "http://{$couchUser}:{$couchPass}@{$couchHost}:{$couchPort}";

  // Sử dụng Mango Query (_find) để tìm token
  $query = array(
    'selector' => array(
      '$or' => array(
        array('lv097' => $vToken),
        array('lv297' => $vToken),
        array('lv397' => $vToken),
        array('lv497' => $vToken),
        array('lv597' => $vToken)
      )
    ),
    'limit' => 1
  );

  $result = makeCouchRequest("{$couchURL}/{$couchDB}/_find", 'POST', $query);

  if ($result['code'] !== 200) {
    // Nếu Mango Query không hoạt động, fallback: lấy tất cả documents
    return findUserByTokenFallback($vToken);
  }

  $response = json_decode($result['body'], true);

  if (isset($response['docs']) && count($response['docs']) > 0) {
    $userData = $response['docs'][0];
    $username = str_replace($vUserTable . ':', '', $userData['_id']);

    // Xác định device type
    $deviceType = '';
    if (isset($userData['lv097']) && $userData['lv097'] === $vToken && $userData['lv097'] !== '') {
      $deviceType = 'web';
    } elseif (isset($userData['lv297']) && $userData['lv297'] === $vToken && $userData['lv297'] !== '') {
      $deviceType = 'mobile';
    } elseif (isset($userData['lv397']) && $userData['lv397'] === $vToken && $userData['lv397'] !== '') {
      $deviceType = 'desktop';
    } elseif (isset($userData['lv497']) && $userData['lv497'] === $vToken && $userData['lv497'] !== '') {
      $deviceType = 'chamcongdes';
    } elseif (isset($userData['lv597']) && $userData['lv597'] === $vToken && $userData['lv597'] !== '') {
      $deviceType = 'chamcongapp';
    }

    return array(
      'success' => true,
      'username' => $username,
      'deviceType' => $deviceType,
      'userData' => $userData
    );
  }

  return array('success' => false, 'message' => 'Token not found');
}

/**
 * Fallback: Tìm user bằng cách lấy tất cả documents (nếu Mango Query không hỗ trợ)
 * @param string $vToken Token cần tìm
 * 
 * @return array Kết quả
 */
function findUserByTokenFallback($vToken)
{
  global $couchHost, $couchPort, $couchUser, $couchPass, $couchDB, $vUserTable;
  $couchURL = "http://{$couchUser}:{$couchPass}@{$couchHost}:{$couchPort}";

  // Lấy tất cả documents từ table
  $result = makeCouchRequest("{$couchURL}/{$couchDB}/_all_docs?include_docs=true");

  if ($result['code'] !== 200) {
    return array('success' => false, 'message' => 'Database error');
  }

  $response = json_decode($result['body'], true);

  if (isset($response['rows'])) {
    foreach ($response['rows'] as $row) {
      if (!isset($row['doc'])) continue;

      $userData = $row['doc'];

      // Kiểm tra token ở cả 3 cột
      if ((isset($userData['lv097']) && $userData['lv097'] === $vToken && $userData['lv097'] !== '') ||
        (isset($userData['lv297']) && $userData['lv297'] === $vToken && $userData['lv297'] !== '') ||
        (isset($userData['lv397']) && $userData['lv397'] === $vToken && $userData['lv397'] !== '') ||
        (isset($userData['lv497']) && $userData['lv497'] === $vToken && $userData['lv497'] !== '') ||
        (isset($userData['lv597']) && $userData['lv597'] === $vToken && $userData['lv597'] !== '')
      ) {

        $username = str_replace($vUserTable . ':', '', $userData['_id']);

        // Xác định device type
        $deviceType = '';
        if (isset($userData['lv097']) && $userData['lv097'] === $vToken && $userData['lv097'] !== '') {
          $deviceType = 'web';
        } elseif (isset($userData['lv297']) && $userData['lv297'] === $vToken && $userData['lv297'] !== '') {
          $deviceType = 'mobile';
        } elseif (isset($userData['lv397']) && $userData['lv397'] === $vToken && $userData['lv397'] !== '') {
          $deviceType = 'desktop';
        } elseif (isset($userData['lv497']) && $userData['lv497'] === $vToken && $userData['lv497'] !== '') {
          $deviceType = 'chamcongdes';
        } elseif (isset($userData['lv597']) && $userData['lv597'] === $vToken && $userData['lv597'] !== '') {
          $deviceType = 'chamcongapp';
        }

        return array(
          'success' => true,
          'username' => $username,
          'deviceType' => $deviceType,
          'userData' => $userData
        );
      }
    }
  }

  return array('success' => false, 'message' => 'Token not found');
}
function couchdbLogin($username, $password, $TypeCode,$deviceType)
{
   global $couchHost, $couchPort, $couchUser, $couchPass, $couchDB, $useTable, $serverTable;
  // Lấy thông tin user từ bảng lv0066
  $vGetConfigArr=explode(".",$username);
  $vPrefix=$vGetConfigArr[0];
  $useTable1="dispatcher:prefix";
  $couchURL = "http://{$couchUser}:{$couchPass}@{$couchHost}:{$couchPort}";
  $requestURL = "{$couchURL}/{$couchDB}/{$useTable1}:{$vPrefix}";
  $result = makeCouchRequest($requestURL);
  if ($result['code'] !== 200) {
    return array("success" => false, "message" => "Đăng nhập thất bại", "httpCode" => $result['code']);
  }

  $userData = json_decode($result['body'], true);
  $couchDBOk= $userData['database'] ?? null;
  return couchdbLoginLevel1($username, $password, $TypeCode,$couchDBOk,$deviceType);
}
function couchdbLoginLevel1($username, $password, $TypeCode,$couchDBOk,$deviceType)
{
  global $couchHost, $couchPort, $couchUser, $couchPass, $couchDB, $useTable, $serverTable;
  // Lấy thông tin user từ bảng lv0066
  $couchURL = "http://{$couchUser}:{$couchPass}@{$couchHost}:{$couchPort}";
  $requestURL = "{$couchURL}/{$couchDBOk}/{$useTable}:{$username}";

  $result = makeCouchRequest($requestURL);
  if ($result['code'] !== 200) {
    return array("success" => false, "message" => "Đăng nhập thất bại", "httpCode" => $result['code']);
  }

  $userData = json_decode($result['body'], true);
  $inputHash = md5($password);

  // Kiểm tra password trước
  if (!isset($userData['lv005']) || $userData['lv005'] !== $inputHash) {
    return array("success" => false, "message" => "Tên đăng nhập hoặc mật khẩu không đúng", "httpCode" => 401);
  }
  if ($TypeCode !== $userData['lv676']) {
    return array("success" => false, "message" => "Đăng nhập sai app", "httpCode" => 402);
  }
  switch (strtolower($deviceType)) {
          case 'mobile':
            $domain=$userData['lv665'] ?? null;
          break;
          case 'desktop':
            $domain=$userData['lv668'] ?? null;
          break;
          case 'chamcongdes':
            $domain=$userData['lv496'] ?? null;
          break;
           case 'chamcongapp':
            $domain=$userData['lv596'] ?? null;
          break;
          default:
            $domain=$userData['lv668'] ?? null;
          break;
      }
  $response = array(
    "method"          => $userData['lv669'] ?? null,
    "IPv4"            => $userData['lv094'] ?? null,
    "user"            => $userData['lv096'] ?? null,
    "pass"            => $userData['lv099'] ?? null,
    "port"            => $userData['lv100'] ?? null,
    'domain'          => $domain ?? null,
    'dbName'          => $userData['lv670'] ?? null,
    'lv705'           => $userData['lv705'] ?? null,
    'lv667'           => $userData['lv667'] ?? null,
    'lv040'           => $userData['lv040'] ?? null,
    // Quyền từ CouchDB: dạng BANHANG_FULL@admin
    'lv003'           => $userData['lv003'] ?? null,
    'couchDBOk'           => $couchDBOk ?? null
  );
  return array("success" => true, "message" => "Đăng nhập thành công", "response" => $response);
}

/**
 * Ghi log đăng nhập vào CouchDB 
 * (với retry conflict handling)
 * @param string $vUserName Tên đăng nhập
 * @param string $vDate Ngày đăng nhập (YYYY-MM-DD)
 * @param string $vTime Thời gian đăng nhập (HH:MM:SS)
 * @param int $vStatus Trạng thái đăng nhập (0: thành công, 1: thất bại)
 * @param string $lvIpClient Địa chỉ IP client
 * @param string $lvmac Địa chỉ MAC client
 * @param string $vDeviceType Loại thiết bị (Mobile, Web, Desktop)
 * @return array Kết quả ghi log
 */
function logTimeCouchDB($vUserName, $vDate, $vTime, $vStatus, $lvIpClient, $lvmac, $vDeviceType, $vToken)
{
  global $couchHost, $couchPort, $couchUser, $couchPass, $couchDB;

  $couchURL = "http://{$couchUser}:{$couchPass}@{$couchHost}:{$couchPort}";
  $logDocId = "logs";

  $logEntry = array(
    'username' => $vUserName,
    'date' => $vDate,
    'time' => $vTime,
    'status' => $vStatus,
    'ip' => $lvIpClient,
    'mac' => $lvmac,
    'deviceType' => $vDeviceType,
    'token' => $vToken,
    'timestamp' => time()
  );

  // Retry logic
  $maxRetries = 3;
  for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
    // GET document
    $result = makeCouchRequest("{$couchURL}/{$couchDB}/{$logDocId}");

    if ($result['code'] === 200) {
      $logDoc = json_decode($result['body'], true);
      if (!isset($logDoc['logs'])) $logDoc['logs'] = array();
    } else if ($result['code'] === 404) {
      $logDoc = array(
        '_id' => $logDocId,
        'type' => 'login_logs',
        'logs' => array()
      );
    } else {
      return array("success" => false, "httpCode" => $result['code'], "message" => "GET failed");
    }

    $logDoc['logs'][] = $logEntry;

    // PUT document
    $result = makeCouchRequest("{$couchURL}/{$couchDB}/{$logDocId}", 'PUT', $logDoc);

    if (in_array($result['code'], [200, 201, 202])) {
      return array("success" => true, "httpCode" => $result['code'], "attempt" => $attempt);
    } else if ($result['code'] === 409 && $attempt < $maxRetries) {
      usleep(100 * $attempt * 1000); // Backoff: 100ms, 200ms, 300ms
    } else {
      return array("success" => false, "httpCode" => $result['code'], "message" => "PUT failed after $attempt attempts");
    }
  }

  return array("success" => false, "message" => "Unknown error");
}

/**
 * Lưu token vào CouchDB
 * @param string $vUserName Tên đăng nhập
 * @param string $vToken Token cần lưu
 * @param string $vDeviceType Loại thiết bị (mobile, web, desktop)
 * 
 * @return bool Kết quả lưu token
 */
function saveToken($vUserName, $vToken, $vDeviceType,$couchDBOk='')
{
  global $couchHost, $couchPort, $couchUser, $couchPass, $couchDB, $vUserTable;
  if($couchDBOk=='') $couchDBOk=$couchDB;
  $couchURL = "http://{$couchUser}:{$couchPass}@{$couchHost}:{$couchPort}";

  // Lấy tài liệu người dùng
  $result = makeCouchRequest("{$couchURL}/{$couchDBOk}/{$vUserTable}:{$vUserName}");
  if ($result['code'] !== 200) {
    return false; // Không tìm thấy người dùng
  }
  $userData = json_decode($result['body'], true);
  // Cập nhật token tương ứng
  switch (strtolower($vDeviceType)) {
    case 'mobile':
      $userData['lv297'] = $vToken; // Token Mobile
      $userData['lv298'] = date('Y-m-d H:i:s'); // Ngày Token Mobile
      $userData['lv299'] = 0; // Cho phép đăng nhập Mobile
      break;
    case 'web':
      $userData['lv097'] = $vToken; // Token Web
      $userData['lv098'] = date('Y-m-d H:i:s'); // Ngày Token Web
      $userData['lv296'] = 0; // Cho phép đăng nhập Web
      break;
    case 'desktop':
      $userData['lv397'] = $vToken; // Token Desktop
      $userData['lv398'] = date('Y-m-d H:i:s'); // Ngày Token Desktop
      $userData['lv399'] = 0; // Cho phép đăng nhập Desktop
      break;
    case 'chamcongdes':
      $userData['lv497'] = $vToken; // Token Desktop Cham Cong
      $userData['lv498'] = date('Y-m-d H:i:s'); // Ngày Token Desktop Cham Cong
      $userData['lv499'] = 0; // Cho phép đăng nhập Desktop Cham Cong
      break;
    case 'chamcongapp':
      $userData['lv597'] = $vToken; // Token App Cham Cong
      $userData['lv598'] = date('Y-m-d H:i:s'); // Ngày Token App Cham Cong
      $userData['lv599'] = 0; // Cho phép đăng nhập App Cham Cong
      break;
    default:
      return false; // Loại thiết bị không hợp lệ
  }
  // Gửi cập nhật về CouchDB
  $updateResult = makeCouchRequest("{$couchURL}/{$couchDBOk}/{$vUserTable}:{$vUserName}", 'PUT', $userData);
  if (in_array($updateResult['code'], [200, 201, 202])) {
    return true; // Cập nhật thành công
  }
  return false; // Cập nhật thất bại
}

/**
 * Xác thực token và trả về device type
 * @param string $vUserName Tên đăng nhập
 * @param string $vToken Token cần kiểm tra
 * 
 * @return array Kết quả: ['success' => bool, 'deviceType' => string, 'userData' => array]
 */
function verifyToken($vUserName, $vToken)
{
  global $couchHost, $couchPort, $couchUser, $couchPass, $couchDB, $vUserTable;
 $couchURL = "http://{$couchUser}:{$couchPass}@{$couchHost}:{$couchPort}";
	//echo "{$couchURL}/{$couchDB}/{$vUserTable}:{$vUserName}";
  // Lấy tài liệu người dùng
  $result = makeCouchRequest("{$couchURL}/{$couchDB}/{$vUserTable}:{$vUserName}");
  if ($result['code'] !== 200) {
    return array('success' => false, 'message' => 'User not found');
  }

  $userData = json_decode($result['body'], true);

  // Kiểm tra token ở cả 3 cột
  if (isset($userData['lv097']) && $userData['lv097'] === $vToken && $userData['lv097'] !== '') {
    return array('success' => true, 'deviceType' => 'web', 'userData' => $userData);
  }

  if (isset($userData['lv297']) && $userData['lv297'] === $vToken && $userData['lv297'] !== '') {
    return array('success' => true, 'deviceType' => 'mobile', 'userData' => $userData);
  }

  if (isset($userData['lv397']) && $userData['lv397'] === $vToken && $userData['lv397'] !== '') {
    return array('success' => true, 'deviceType' => 'desktop', 'userData' => $userData);
  }

  if (isset($userData['lv497']) && $userData['lv497'] === $vToken && $userData['lv497'] !== '') {
    return array('success' => true, 'deviceType' => 'chamcongdes', 'userData' => $userData);
  }

  if (isset($userData['lv597']) && $userData['lv597'] === $vToken && $userData['lv597'] !== '') {
    return array('success' => true, 'deviceType' => 'chamcongapp', 'userData' => $userData);
  }

  return array('success' => false, 'message' => 'Invalid token');
}

/**
 * Xóa token khỏi CouchDB (đăng xuất)
 * @param string $vUserName Tên đăng nhập
 * @param string $vDeviceType Loại thiết bị (mobile, web, desktop)
 * 
 * @return bool Kết quả xóa token
 */
function removeToken($vUserName, $vDeviceType)
{
  global $couchHost, $couchPort, $couchUser, $couchPass, $couchDB, $vUserTable;
  $couchURL = "http://{$couchUser}:{$couchPass}@{$couchHost}:{$couchPort}";

  // Lấy tài liệu người dùng
  $result = makeCouchRequest("{$couchURL}/{$couchDB}/{$vUserTable}:{$vUserName}");
  if ($result['code'] !== 200) {
    return false; // Không tìm thấy người dùng
  }

  $userData = json_decode($result['body'], true);
  // Xóa token tương ứng
  switch (strtolower($vDeviceType)) {
    case 'mobile':
      $userData['lv297'] = ''; // Xóa Token Mobile
      $userData['lv298'] = date('Y-m-d H:i:s'); // Cập nhật thời gian
      break;
    case 'web':
      $userData['lv097'] = ''; // Xóa Token Web
      $userData['lv098'] = date('Y-m-d H:i:s'); // Cập nhật thời gian
      break;
    case 'desktop':
      $userData['lv397'] = ''; // Xóa Token Desktop
      $userData['lv398'] = date('Y-m-d H:i:s'); // Cập nhật thời gian
      break;
    case 'chamcongdes':
      $userData['lv497'] = ''; // Xóa Token Desktop Cham Cong
      $userData['lv498'] = date('Y-m-d H:i:s'); // Cập nhật thời gian
      break;
    case 'chamcongapp':
      $userData['lv597'] = ''; // Xóa Token App Cham Cong
      $userData['lv598'] = date('Y-m-d H:i:s'); // Cập nhật thời gian
      break;
    default:
      return false; // Loại thiết bị không hợp lệ
  }
  // Gửi cập nhật về CouchDB
  $updateResult = makeCouchRequest("{$couchURL}/{$couchDB}/{$vUserTable}:{$vUserName}", 'PUT', $userData);
  if (in_array($updateResult['code'], [200, 201, 202])) {
    return true; // Cập nhật thành công
  }
  return false; // Cập nhật thất bại
}
