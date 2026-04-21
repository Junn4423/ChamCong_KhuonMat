<?php
// Global Security Guard & CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS, PATCH");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, Token, sof-user-token, x-user-token, x-user-code, x-user-counter, " . (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']) ? $_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'] : '*'));
header("Access-Control-Allow-Credentials: true");
if (!function_exists('getallheaders')) {
	function getallheaders()
	{
		$headers = [];
		foreach ($_SERVER as $name => $value) {
			if (substr($name, 0, 5) == 'HTTP_') {
				$headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
			}
		}
		return $headers;
	}
}
$headers = getallheaders();
$normalized_headers = array_change_key_case($headers, CASE_LOWER);
$vXSOFUserToken = isset($normalized_headers['x-sof-user-token']) ? $normalized_headers['x-sof-user-token'] : '';
$vUserToken = isset($normalized_headers['x-user-token']) ? $normalized_headers['x-user-token'] : '';
$vUserCode = isset($normalized_headers['x-user-code']) ? $normalized_headers['x-user-code'] : '';
if ($vUserCode === '') {
	$vUserCode = isset($normalized_headers['x-user-username']) ? $normalized_headers['x-user-username'] : '';
}
//Dat security
$is_service_request = (strpos($vXSOFUserToken, '8c4f2b9a71d6e3c9f0ab42d5e8c1f7a39b6d0e4f1a2c8b7d5e9f3a1c6b4d2e8') !== false && !$is_image_proxy);
if ($is_service_request) {
	include_once("couchdb_functions.php");
	//$vUserCode = $normalized_headers['x-user-code'] ?? '';
	//$vUserToken = $normalized_headers['x-user-token'] ?? $normalized_headers['sof-user-token'] ?? '';
	$verifyResult = ['success' => false];
	if (!empty($vUserCode) && !empty($vUserToken)) {
		$verifyResult = verifyToken($vUserCode, $vUserToken);
	} elseif (!empty($vUserToken)) {
		// Tìm user dựa trên token duy nhất (Stateless linh hoạt)
		$verifyResult = findUserByToken($vUserToken);
		if ($verifyResult['success']) {
			$vUserCode = $verifyResult['username'];
		} else {
			$vUserCode = '';
		}
	}
	if ($verifyResult['success'] && isset($verifyResult['userData']['lv670'])) {
		// Ưu tiên thông tin từ CouchDB (Dành cho chế độ đa người dùng/đa cơ sở dữ liệu)
		$vDataBaseName = $verifyResult['userData']['lv670'];
		define("DB_DATABASE", $vDataBaseName);
		$couchRight = $verifyResult['userData']['lv003'] ?? '';
		/*
		// Xử lý Right Codes (Mã quyền)
		if (!empty($couchRight) && strpos($couchRight, '@') !== false) {
			$crParts = explode('@', $couchRight);
			$crRole = trim(end($crParts));
			$crGroup = trim($crParts[0]);
			$crGroupFolder = str_replace('_', '/', $crGroup);
			$crFile = 'sof/couch_rights/' . $crGroupFolder . '/' . $crRole . '/rights.json';
			// Thử cả 2 đường dẫn (từ gốc hoặc từ thư mục con)
			if (!file_exists($crFile) && file_exists('../' . $crFile)) {
				$crFile = '../' . $crFile;
			}

			if (file_exists($crFile)) {
				$crJson = json_decode(file_get_contents($crFile), true);
				if (isset($crJson['lv_lv0008']) && is_array($crJson['lv_lv0008'])) {
					$crCodes = array_map(function ($item) {
						return $item['lv003'] ?? '';
					}, array_filter($crJson['lv_lv0008'], function ($item) {
						return (isset($item['lv004']) && (string) $item['lv004'] === '1');
					}));
					$vArrQuyen = implode(',', array_unique(array_filter($crCodes)));
					//					$SOF_CONTEXT['ERPSOFV2RRight'] = implode(',', array_unique(array_filter($crCodes)));
					// Miễn trừ kiểm tra quyền cho module lấy danh sách quyền
					if ($vtable !== 'user_permissions') {
						$vBienFunc = checkSOFFunction($crJson, $vfun, $vtable);
					}
				} else {
					echo json_encode(array("success" => false, "message" => "Không tìm thấy cấu hình quyền (lv_lv0008)"));
					exit();
				}
			}
		}
			*/
	} else {
		// Token verification failed - set a flag to indicate session conflict
		define("TOKEN_VERIFICATION_FAILED", true);
	}
}
/**
 * Check if user has permission to access specific table and function
 * @param array $crJson Rights data from rights.json
 * @param string $vfun Function name (View, Add, Edit, Del, Apr, Rpt, etc.)
 * @param string $vtable Table/Module code (Sl0005, Sl0006, etc.)
 * 
 * @return array ['success' => bool, 'message' => string]
 */
/*
function checkSOFFunction($crJson, $vfun, $vtable = '')
{
	// Validate input
	if (empty($vtable) || empty($vfun)) {
		return [
			'success' => false,
			'message' => 'Thiếu thông tin bảng hoặc hàm'
		];
	}

	if (!isset($crJson['lv_lv0008']) || !isset($crJson['lv_lv0009'])) {
		return [
			'success' => false,
			'message' => 'Cấu hình quyền không hợp lệ'
		];
	}

	// Step 1: Find module in lv_lv0008 (User-Module mapping)
	// lv003 = module code (e.g., "Sl0005")
	// lv004 = enabled (1 = enabled, 0 = disabled)
	$moduleId = null;
	foreach ($crJson['lv_lv0008'] as $item) {
		if ($item['lv003'] === $vtable && (int)$item['lv004'] === 1) {
			$moduleId = $item['lv001'];
			break;
		}
	}

	// Module not found or disabled
	if ($moduleId === null) {
		return [
			'success' => false,
			'message' => "Không có quyền truy cập module {$vtable}"
		];
	}

	// Step 2: Find function in lv_lv0009 (Module-Function mapping)
	// lv003 = reference to lv001 in lv_lv0008 (module id)
	// lv002 = function name (View, Add, Edit, Del, Apr, Rpt, etc.)
	// lv004 = enabled (1 = enabled, 0 = disabled)
	foreach ($crJson['lv_lv0009'] as $item) {
		if ($item['lv003'] == $moduleId && $item['lv002'] === $vfun && (int)$item['lv004'] === 1) {
			return [
				'success' => true,
				'message' => "Có quyền truy cập {$vfun} trên {$vtable}"
			];
		}
	}

	// Function not found or disabled for this module
	return [
		'success' => false,
		'message' => "Không có quyền thực hiện {$vfun} trên {$vtable}"
	];
}
	*/
