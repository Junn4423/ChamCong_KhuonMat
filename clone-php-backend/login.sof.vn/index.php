<?php
error_reporting(E_ERROR);
header('Content-Type: application/json; charset=UTF-8');

include('config.php');

function lv_trim_value($value)
{
	if ($value === null) {
		return '';
	}
	return trim((string)$value);
}

function lv_now_text()
{
	return date('Y-m-d H:i:s');
}

function lv_couch_cfg()
{
	return array(
		'host' => defined('COUCHDB_HOST') ? COUCHDB_HOST : '192.168.1.20',
		'port' => defined('COUCHDB_PORT') ? COUCHDB_PORT : '5984',
		'user' => defined('COUCHDB_USER') ? COUCHDB_USER : 'admin',
		'pass' => defined('COUCHDB_PASS') ? COUCHDB_PASS : 'rootsof',
		'dispatcher_db' => defined('COUCHDB_DISPATCHER_DB') ? COUCHDB_DISPATCHER_DB : 'couchdb',
		'route_doc_prefix' => defined('COUCHDB_ROUTE_DOC_PREFIX') ? COUCHDB_ROUTE_DOC_PREFIX : 'dispatcher:prefix:',
		'user_table' => defined('COUCHDB_USER_TABLE') ? COUCHDB_USER_TABLE : 'lv_lv0066',
	);
}

function lv_make_couch_request($path, $method = 'GET', $data = null)
{
	$cfg = lv_couch_cfg();
	$baseUrl = 'http://' . $cfg['host'] . ':' . $cfg['port'];
	$url = preg_match('/^https?:\/\//i', $path) ? $path : $baseUrl . '/' . ltrim($path, '/');

	$curl = curl_init();
	$options = array(
		CURLOPT_URL => $url,
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_TIMEOUT => 10,
		CURLOPT_HTTPHEADER => array('Content-Type: application/json'),
		CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
		CURLOPT_USERPWD => $cfg['user'] . ':' . $cfg['pass'],
	);

	$method = strtoupper((string)$method);
	if ($method === 'POST') {
		$options[CURLOPT_POST] = true;
		if ($data !== null) {
			$options[CURLOPT_POSTFIELDS] = json_encode($data);
		}
	} elseif ($method === 'PUT') {
		$options[CURLOPT_CUSTOMREQUEST] = 'PUT';
		if ($data !== null) {
			$options[CURLOPT_POSTFIELDS] = json_encode($data);
		}
	} elseif ($method !== 'GET') {
		$options[CURLOPT_CUSTOMREQUEST] = $method;
		if ($data !== null) {
			$options[CURLOPT_POSTFIELDS] = json_encode($data);
		}
	}

	curl_setopt_array($curl, $options);
	$response = curl_exec($curl);
	$httpCode = (int)curl_getinfo($curl, CURLINFO_HTTP_CODE);
	$error = null;
	if ($response === false) {
		$error = curl_error($curl);
	}
	curl_close($curl);

	return array(
		'code' => $httpCode,
		'body' => $response,
		'error' => $error,
	);
}

function lv_extract_prefix($username)
{
	$username = lv_trim_value($username);
	$dotPos = strpos($username, '.');
	if ($dotPos === false || $dotPos <= 0 || $dotPos >= (strlen($username) - 1)) {
		return '';
	}
	return strtolower(substr($username, 0, $dotPos));
}

function lv_route_doc_by_username($username)
{
	$cfg = lv_couch_cfg();
	$prefix = lv_extract_prefix($username);
	if ($prefix === '') {
		return array(
			'success' => false,
			'message' => 'Tai khoan khong hop le de dieu phoi (yeu cau dinh dang prefix.username co dau cham)',
		);
	}

	$routeDocId = $cfg['route_doc_prefix'] . $prefix;
	$encodedId = str_replace('%3A', ':', rawurlencode($routeDocId));
	$result = lv_make_couch_request($cfg['dispatcher_db'] . '/' . $encodedId, 'GET');

	if ((int)$result['code'] !== 200) {
		return array('success' => false, 'message' => 'Khong tim thay cau hinh dieu phoi');
	}

	$doc = json_decode($result['body'], true);
	if (!is_array($doc)) {
		return array('success' => false, 'message' => 'Du lieu dieu phoi khong hop le');
	}
	if (isset($doc['active']) && (string)$doc['active'] === '0') {
		return array('success' => false, 'message' => 'He thong dang tam khoa');
	}

	$database = lv_trim_value(isset($doc['database']) ? $doc['database'] : '');
	if ($database === '') {
		return array('success' => false, 'message' => 'Chua cau hinh database dich cho he thong');
	}

	$doc['prefix'] = $prefix;
	$doc['database'] = $database;
	return array('success' => true, 'route' => $doc);
}

function lv_extract_username_from_doc($userDoc, $userTable)
{
	if (!is_array($userDoc)) {
		return '';
	}

	$username = lv_trim_value(isset($userDoc['lv001']) ? $userDoc['lv001'] : '');
	if ($username !== '') {
		return $username;
	}

	$docId = lv_trim_value(isset($userDoc['_id']) ? $userDoc['_id'] : '');
	$prefix = $userTable . ':';
	if ($docId !== '' && strpos($docId, $prefix) === 0) {
		return substr($docId, strlen($prefix));
	}
	return '';
}

function lv_find_user_doc($database, $userTable, $username)
{
	$docId = $userTable . ':' . $username;
	$encodedDocId = str_replace('%3A', ':', rawurlencode($docId));
	$direct = lv_make_couch_request($database . '/' . $encodedDocId, 'GET');
	if ((int)$direct['code'] === 200) {
		$doc = json_decode($direct['body'], true);
		if (is_array($doc)) {
			return array('success' => true, 'doc' => $doc, 'doc_id' => $docId);
		}
	}

	$selectorPayload = array(
		'selector' => array(
			'$or' => array(
				array('lv001' => $username),
				array('_id' => $docId),
			)
		),
		'limit' => 5,
	);
	$findResult = lv_make_couch_request($database . '/_find', 'POST', $selectorPayload);
	if ((int)$findResult['code'] === 200) {
		$findBody = json_decode($findResult['body'], true);
		if (is_array($findBody) && isset($findBody['docs']) && is_array($findBody['docs'])) {
			foreach ($findBody['docs'] as $candidate) {
				if (!is_array($candidate)) {
					continue;
				}
				if (strcasecmp(lv_extract_username_from_doc($candidate, $userTable), $username) === 0) {
					$resolvedId = lv_trim_value(isset($candidate['_id']) ? $candidate['_id'] : $docId);
					return array('success' => true, 'doc' => $candidate, 'doc_id' => $resolvedId);
				}
			}
		}
	}

	$allDocs = lv_make_couch_request($database . '/_all_docs?include_docs=true&limit=2000', 'GET');
	if ((int)$allDocs['code'] === 200) {
		$allBody = json_decode($allDocs['body'], true);
		if (is_array($allBody) && isset($allBody['rows']) && is_array($allBody['rows'])) {
			foreach ($allBody['rows'] as $row) {
				$candidate = (is_array($row) && isset($row['doc']) && is_array($row['doc'])) ? $row['doc'] : null;
				if (!is_array($candidate)) {
					continue;
				}
				if (strcasecmp(lv_extract_username_from_doc($candidate, $userTable), $username) === 0) {
					$resolvedId = lv_trim_value(isset($candidate['_id']) ? $candidate['_id'] : $docId);
					return array('success' => true, 'doc' => $candidate, 'doc_id' => $resolvedId);
				}
			}
		}
	}

	return array('success' => false, 'message' => 'Tai khoan khong ton tai tren CouchDB');
}

function lv_device_fields($deviceType)
{
	$device = strtolower(lv_trim_value($deviceType));
	if ($device === 'mobile') {
		return array('mobile', 'lv297', 'lv298', 'lv299');
	}
	if ($device === 'desktop') {
		return array('desktop', 'lv397', 'lv398', 'lv399');
	}
	return array('web', 'lv097', 'lv098', 'lv296');
}

function lv_resolve_domain($userDoc, $device)
{
	if ($device === 'mobile') {
		return lv_trim_value(isset($userDoc['lv665']) ? $userDoc['lv665'] : '');
	}
	$desktopDomain = lv_trim_value(isset($userDoc['lv688']) ? $userDoc['lv688'] : '');
	if ($desktopDomain !== '') {
		return $desktopDomain;
	}
	return lv_trim_value(isset($userDoc['lv668']) ? $userDoc['lv668'] : '');
}

function lv_resolve_system_name($routeDoc)
{
	$systemName = lv_trim_value(isset($routeDoc['system_name']) ? $routeDoc['system_name'] : '');
	if ($systemName === '') {
		$systemName = lv_trim_value(isset($routeDoc['service_name']) ? $routeDoc['service_name'] : '');
	}
	if ($systemName === '') {
		$systemName = lv_trim_value(isset($routeDoc['note']) ? $routeDoc['note'] : '');
	}
	return $systemName;
}

function lv_resolve_welcome_message($routeDoc, $systemName)
{
	$welcome = lv_trim_value(isset($routeDoc['welcome_message']) ? $routeDoc['welcome_message'] : '');
	if ($welcome === '' && $systemName !== '') {
		$welcome = 'Xin chao, day la he thong cham cong ' . $systemName;
	}
	return $welcome;
}

function lv_generate_token($length = 32)
{
	$alphabet = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$max = strlen($alphabet) - 1;
	$output = '';
	$length = max(8, (int)$length);
	for ($i = 0; $i < $length; $i++) {
		$output .= $alphabet[random_int(0, $max)];
	}
	return $output;
}

function lv_write_login_log($database, $username, $status, $deviceType, $token, $ip, $mac)
{
	$logDocId = 'logs';
	$encodedLogId = str_replace('%3A', ':', rawurlencode($logDocId));
	$docRes = lv_make_couch_request($database . '/' . $encodedLogId, 'GET');
	$doc = null;
	if ((int)$docRes['code'] === 200) {
		$doc = json_decode($docRes['body'], true);
	}
	if (!is_array($doc)) {
		$doc = array(
			'_id' => $logDocId,
			'type' => 'login_logs',
			'logs' => array(),
		);
	}
	if (!isset($doc['logs']) || !is_array($doc['logs'])) {
		$doc['logs'] = array();
	}

	$doc['logs'][] = array(
		'username' => $username,
		'date' => date('Y-m-d'),
		'time' => date('H:i:s'),
		'status' => (int)$status,
		'ip' => lv_trim_value($ip),
		'mac' => lv_trim_value($mac) !== '' ? lv_trim_value($mac) : 'unknown',
		'deviceType' => lv_trim_value($deviceType) !== '' ? lv_trim_value($deviceType) : 'web',
		'token' => lv_trim_value($token),
		'timestamp' => time(),
	);

	$doc['updated_at'] = lv_now_text();
	lv_make_couch_request($database . '/' . $encodedLogId, 'PUT', $doc);
}

$result = array(
	'code' => '',
	'token' => '',
	'userid' => '',
	'department' => '',
	'role' => '',
	'name' => '',
	'domain' => '',
	'method' => '',
	'database' => '',
	'IPv4' => '',
	'lv006' => '',
	'lv900' => '',
	'lv705' => '',
	'lv667' => '',
	'lv040' => '',
	'device_type' => '',
	'type_code' => '',
	'routed_database' => '',
	'dispatch_database' => '',
	'route_prefix' => '',
	'system_name' => '',
	'system_note' => '',
	'welcome_message' => '',
);

$inputJSON = file_get_contents('php://input');
$input = json_decode($inputJSON, true);

$username = isset($input['txtUserName']) ? $input['txtUserName'] : (isset($_POST['txtUserName']) ? $_POST['txtUserName'] : '');
$password = isset($input['txtPassword']) ? $input['txtPassword'] : (isset($_POST['txtPassword']) ? $_POST['txtPassword'] : '');
$deviceType = isset($input['txtDeviceType']) ? $input['txtDeviceType'] : (isset($_POST['txtDeviceType']) ? $_POST['txtDeviceType'] : 'web');
$typeCode = isset($input['txtTypeCode']) ? $input['txtTypeCode'] : (isset($_POST['txtTypeCode']) ? $_POST['txtTypeCode'] : '');

$username = lv_trim_value($username);
$password = (string)$password;
$deviceType = lv_trim_value($deviceType);
$typeCode = lv_trim_value($typeCode);

if ($username === '') {
	$result['message'] = 'Please enter your Login Name!';
	echo json_encode($result);
	exit;
}

if ($password === '') {
	$result['message'] = 'Please enter your Password!';
	echo json_encode($result);
	exit;
}

$cfg = lv_couch_cfg();
$routeResult = lv_route_doc_by_username($username);
if (empty($routeResult['success'])) {
	$result['message'] = isset($routeResult['message']) ? $routeResult['message'] : 'Khong tim thay cau hinh dieu phoi';
	echo json_encode($result);
	exit;
}

$routeDoc = $routeResult['route'];
$targetDb = lv_trim_value(isset($routeDoc['database']) ? $routeDoc['database'] : '');
$userTable = lv_trim_value(isset($routeDoc['user_table']) ? $routeDoc['user_table'] : '');
if ($userTable === '') {
	$userTable = $cfg['user_table'];
}

$userDocResult = lv_find_user_doc($targetDb, $userTable, $username);
if (empty($userDocResult['success'])) {
	$result['message'] = isset($userDocResult['message']) ? $userDocResult['message'] : 'Login failed, please try again!';
	echo json_encode($result);
	exit;
}

$userDoc = $userDocResult['doc'];
$docPassword = strtolower(lv_trim_value(isset($userDoc['lv005']) ? $userDoc['lv005'] : ''));
$inputHash = md5($password);
if ($docPassword === '' || $docPassword !== $inputHash) {
	$result['message'] = 'Login failed, please try again!';
	echo json_encode($result);
	exit;
}

list($device, $tokenField, $dateField, $blockField) = lv_device_fields($deviceType);
if (lv_trim_value(isset($userDoc[$blockField]) ? $userDoc[$blockField] : '0') === '1') {
	$result['message'] = 'Tai khoan bi cam dang nhap tren thiet bi ' . $device;
	echo json_encode($result);
	exit;
}

$expectedType = strtoupper($typeCode);
$accountType = strtoupper(lv_trim_value(isset($userDoc['lv676']) ? $userDoc['lv676'] : ''));
if ($expectedType !== '' && $accountType !== '' && $accountType !== $expectedType) {
	$result['message'] = 'Tai khoan khong co quyen truy cap ung dung nay';
	echo json_encode($result);
	exit;
}

$token = lv_generate_token(32);
$userDoc[$tokenField] = $token;
$userDoc[$dateField] = lv_now_text();
$userDoc[$blockField] = 0;
$userDoc['updated_at'] = lv_now_text();

$docId = lv_trim_value(isset($userDoc['_id']) ? $userDoc['_id'] : $userDocResult['doc_id']);
if ($docId === '') {
	$docId = $userTable . ':' . $username;
}
$encodedDocId = str_replace('%3A', ':', rawurlencode($docId));
$putResult = lv_make_couch_request($targetDb . '/' . $encodedDocId, 'PUT', $userDoc);
if ((int)$putResult['code'] < 200 || (int)$putResult['code'] >= 300) {
	$result['message'] = 'Khong the cap nhat token dang nhap';
	echo json_encode($result);
	exit;
}

$systemName = lv_resolve_system_name($routeDoc);
$welcomeMessage = lv_resolve_welcome_message($routeDoc, $systemName);

$result['code'] = lv_extract_username_from_doc($userDoc, $userTable);
if ($result['code'] === '') {
	$result['code'] = $username;
}
$result['token'] = $token;
$result['userid'] = lv_trim_value(isset($userDoc['lv006']) ? $userDoc['lv006'] : '');
$result['department'] = lv_trim_value(isset($userDoc['lv003']) ? $userDoc['lv003'] : '');
$result['role'] = lv_trim_value(isset($userDoc['lv004']) ? $userDoc['lv004'] : '');
$result['name'] = lv_trim_value(isset($userDoc['lv002']) ? $userDoc['lv002'] : $result['code']);
$result['domain'] = lv_resolve_domain($userDoc, $device);
$result['method'] = lv_trim_value(isset($userDoc['lv669']) ? $userDoc['lv669'] : 'http');
$result['database'] = lv_trim_value(isset($userDoc['lv670']) ? $userDoc['lv670'] : '');
$result['IPv4'] = lv_trim_value(isset($userDoc['lv094']) ? $userDoc['lv094'] : '');
$result['lv006'] = lv_trim_value(isset($userDoc['lv006']) ? $userDoc['lv006'] : '');
$result['lv900'] = lv_trim_value(isset($userDoc['lv900']) ? $userDoc['lv900'] : '');
$result['lv705'] = lv_trim_value(isset($userDoc['lv705']) ? $userDoc['lv705'] : '');
$result['lv667'] = lv_trim_value(isset($userDoc['lv667']) ? $userDoc['lv667'] : '');
$result['lv040'] = lv_trim_value(isset($userDoc['lv040']) ? $userDoc['lv040'] : '');
$result['device_type'] = $device;
$result['type_code'] = $typeCode;
$result['routed_database'] = $targetDb;
$result['dispatch_database'] = $cfg['dispatcher_db'];
$result['route_prefix'] = lv_trim_value(isset($routeDoc['prefix']) ? $routeDoc['prefix'] : lv_extract_prefix($username));
$result['system_name'] = $systemName;
$result['system_note'] = lv_trim_value(isset($routeDoc['note']) ? $routeDoc['note'] : '');
$result['welcome_message'] = $welcomeMessage;

lv_write_login_log(
	$targetDb,
	$result['code'],
	0,
	$device,
	$token,
	isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '',
	''
);

echo json_encode($result);