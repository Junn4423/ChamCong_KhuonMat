<?php
 error_reporting(E_ERROR);
 ini_set('display_errors', 0);
 ini_set('display_startup_errors', 0);
//error_reporting(E_ERROR);
//ini_set('display_errors', '0');

// Cho phép từ mọi origin (hoặc cụ thể origin nếu muốn)
header("Access-Control-Allow-Origin: *");

// Cho phép các phương thức
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");

// Cho phép các header custom (như Content-Type)
header("Access-Control-Allow-Headers: Content-Type, Authorization, SOF-User-Token, X-SOF-USER-TOKEN, X-USER-TOKEN, X-USER-CODE, X-USER-USERNAME, X-DATABASE, X-SERVER-IP, X-SERVER-PORT, X-SERVER-USER, X-SERVER-PASSWORD");


// Nếu là request OPTIONS (preflight), trả về sớm
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
	http_response_code(200);
	exit();
}
session_start();

$role = isset($_SERVER['HTTP_ROLE']) ? $_SERVER['HTTP_ROLE'] : '';
$username = isset($_SERVER['HTTP_USERNAME']) ? $_SERVER['HTTP_USERNAME'] : '';
// // ===== BẢO MẬT API =====
// // Kiểm tra SOF User Token
// $userToken = $_SERVER['HTTP_SOF_USER_TOKEN'] ?? $input['token'] ?? $_POST['token'] ?? '';
// $validToken = 'SOF2025DEVELOPER';

// // Kiểm tra token
// if ($userToken !== $validToken) {
//     http_response_code(401);
//     echo json_encode([
//         'error' => 'Unauthorized',
//         'message' => 'Invalid or missing SOF-User-Token'
//     ]);
//     exit;
// }
// // ===== END BẢO MẬT =====


header("Content-Type: application/json; charset=UTF-8");
include("config.php");
include("function.php");
include("lv_controler.php");


$inputJSON = file_get_contents('php://input');
$input = json_decode($inputJSON, true);
// Đảm bảo $input luôn là mảng để tránh cảnh báo undefined index
if (!is_array($input)) {
	$input = [];
}

// ===== QUAN TRỌNG: XỬ LÝ AUTH TRƯỚC =====
// Check nếu là request authentication (login/register)
if (isset($input['method']) || isset($_POST['method'])) {
	if (file_exists("register_user.php")) {
		include("register_user.php");
		exit();
	}

	http_response_code(501);
	echo json_encode([
		'success' => false,
		'message' => 'Auth gateway chua duoc copy vao chamcong/services.sof.vn',
		'errorType' => 'not_implemented'
	]);
	exit();
}

$vtable = $input['table'] ?? ($_POST['table'] ?? null);
$vfun = $input['func'] ?? ($_POST['func'] ?? null);

// Nếu thiếu table/func thì trả về lỗi rõ ràng và kết thúc sớm
if (empty($vtable) || empty($vfun)) {
	http_response_code(400);
	echo json_encode([
		'success' => false,
		'message' => 'Thiếu tham số table hoặc func',
		'errorType' => 'bad_request'
	]);
	exit();
}
$vlimit = isset($input['limit']) ? $input['limit'] : (isset($_POST['limit']) ? $_POST['limit'] : "");
$vmonth = isset($input['month']) ? $input['month'] : (isset($_POST['month']) ? $_POST['month'] : "");
$vyear = isset($input['year']) ? $input['year'] : (isset($_POST['year']) ? $_POST['year'] : "");


$vOutput = array();

function saveImageToDB($fileData, $cot, $lv001)
{
	try {
		$cot = trim((string)$cot);
		if (!preg_match('/^lv[0-9]{3}$/', $cot)) {
			throw new Exception("Tên cột không hợp lệ");
		}

		$docsTable = function_exists('lv_docs_table_name') ? lv_docs_table_name('cr_lv0382') : '`cr_lv0382`';
		if ($docsTable === '') {
			throw new Exception("Không xác định được bảng tài liệu ảnh");
		}

		// Kết nối đến CSDL
		$db = db_connect();
		if (!$db) {
			throw new Exception("Lỗi kết nối CSDL");
		}
		// Kiểm tra xem lv002 = $lv001 đã tồn tại chưa
		$checkSql = "SELECT COUNT(*) as count FROM {$docsTable} WHERE lv002 = ?";

		$checkStmt = mysqli_prepare($db, $checkSql);
		mysqli_stmt_bind_param($checkStmt, "s", $lv001);
		mysqli_stmt_execute($checkStmt);
		$result = mysqli_stmt_get_result($checkStmt);
		$row = mysqli_fetch_assoc($result);
		$exists = $row['count'] > 0;
		mysqli_stmt_close($checkStmt);


		if ($exists) {
			$sql = "UPDATE {$docsTable} SET {$cot} = ? WHERE lv002 = ?";
		} else {
			$sql = "INSERT INTO {$docsTable} (lv002, {$cot}) VALUES (?, ?)";
		}

		$stmt = mysqli_prepare($db, $sql);

		if (!$stmt) {
			throw new Exception("Lỗi chuẩn bị truy vấn: " . mysqli_error($db));
		}


		$null = NULL;

		if ($exists) {
			// Gắn tham số: "sb" = string ($lv001), blob ($fileData)
			mysqli_stmt_bind_param($stmt, "bs", $null, $lv001);
			mysqli_stmt_send_long_data($stmt, 0, $fileData);
		} else {
			mysqli_stmt_bind_param($stmt, "sb", $lv001, $null);
			mysqli_stmt_send_long_data($stmt, 1, $fileData);
		}

		// Thực thi truy vấn
		if (!mysqli_stmt_execute($stmt)) {
			throw new Exception("Lỗi thực thi truy vấn: " . mysqli_stmt_error($stmt));
		}

		// Đóng kết nối
		mysqli_stmt_close($stmt);
		mysqli_close($db);

		return [
			'success' => true,
			'message' => 'Lưu ảnh vào CSDL thành công.',
		];
	} catch (Exception $e) {
		return [
			'success' => false,
			'message' => $e->getMessage(),
		];
	}
}

function cc_extract_image_token($value)
{
	$token = trim((string)$value);
	if ($token === '') {
		return '';
	}
	if (strlen($token) < 12) {
		return '';
	}
	if (preg_match('/[\s\/\\\\\?#&]/', $token)) {
		return '';
	}
	return $token;
}

function cc_build_token_image_url($token)
{
	$token = cc_extract_image_token($token);
	if ($token === '') {
		return '';
	}

	$baseUrlCandidates = array(
		getenv('SOF_TOKEN_IMAGE_BASE_URL'),
		getenv('TOKEN_IMAGE_BASE_URL'),
		'http://192.168.1.87/token',
	);
	foreach ($baseUrlCandidates as $baseUrl) {
		$baseUrl = rtrim(trim((string)$baseUrl), '/');
		if ($baseUrl !== '') {
			return $baseUrl . '/' . rawurlencode($token);
		}
	}

	return '';
}

function cc_fetch_remote_image_bytes($url)
{
	$url = trim((string)$url);
	if ($url === '') {
		return array(false, '', '');
	}

	if (function_exists('curl_init')) {
		$curl = curl_init($url);
		curl_setopt_array($curl, array(
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_CONNECTTIMEOUT => 5,
			CURLOPT_TIMEOUT => 15,
			CURLOPT_HTTPHEADER => array('Accept: image/*,*/*'),
			CURLOPT_HEADER => false,
		));
		$data = curl_exec($curl);
		$error = curl_error($curl);
		$statusCode = (int)curl_getinfo($curl, CURLINFO_HTTP_CODE);
		$contentType = trim((string)curl_getinfo($curl, CURLINFO_CONTENT_TYPE));
		curl_close($curl);

		if ($data !== false && $statusCode >= 200 && $statusCode < 300 && $data !== '') {
			return array($data, $contentType, '');
		}
		return array(false, $contentType, $error !== '' ? $error : ('HTTP ' . $statusCode));
	}

	$context = stream_context_create(array(
		'http' => array(
			'method' => 'GET',
			'timeout' => 15,
			'ignore_errors' => true,
			'header' => "Accept: image/*,*/*\r\n",
		),
	));
	$data = @file_get_contents($url, false, $context);
	$contentType = '';
	if (isset($http_response_header) && is_array($http_response_header)) {
		foreach ($http_response_header as $headerLine) {
			if (stripos($headerLine, 'Content-Type:') === 0) {
				$contentType = trim(substr($headerLine, 13));
				break;
			}
		}
	}
	if ($data !== false && $data !== '') {
		return array($data, $contentType, '');
	}

	return array(false, $contentType, 'Cannot fetch remote image');
}

function cc_map_employee_row($row)
{
	if (!is_array($row)) {
		return array();
	}

	$employeeId = trim((string)($row['lv001'] ?? ''));
	if ($employeeId === '') {
		return array();
	}

	$imageRef = trim((string)($row['lv007'] ?? ''));
	$imageToken = cc_extract_image_token($imageRef);

	return array(
		'maNhanVien' => $employeeId,
		'tenNhanVien' => trim((string)($row['lv002'] ?? '')),
		'soDienThoai' => trim((string)($row['lv039'] ?? '')),
		'email' => trim((string)($row['lv041'] ?? '')),
		'maPhongBan' => trim((string)($row['lv029'] ?? '')),
		'lv001' => $employeeId,
		'lv002' => trim((string)($row['lv002'] ?? '')),
		'lv039' => trim((string)($row['lv039'] ?? '')),
		'lv041' => trim((string)($row['lv041'] ?? '')),
		'lv029' => trim((string)($row['lv029'] ?? '')),
		'lv007' => $imageRef,
		'image_ref' => $imageRef,
		'image_token' => $imageToken,
	);
}

function cc_detect_image_mime($imageBytes)
{
	if (!is_string($imageBytes) || $imageBytes === '') {
		return '';
	}

	if (function_exists('getimagesizefromstring')) {
		$imageInfo = @getimagesizefromstring($imageBytes);
		if (is_array($imageInfo) && !empty($imageInfo['mime'])) {
			return trim((string)$imageInfo['mime']);
		}
	}

	if (function_exists('finfo_open')) {
		$finfo = @finfo_open(FILEINFO_MIME_TYPE);
		if ($finfo) {
			$mime = @finfo_buffer($finfo, $imageBytes);
			@finfo_close($finfo);
			if (is_string($mime) && trim($mime) !== '') {
				return trim($mime);
			}
		}
	}

	return '';
}

function cc_create_sof_image_token($imageBytes, $username)
{
	$username = trim((string)$username);
	if ($username === '') {
		$username = 'admin';
	}

	$imageMime = cc_detect_image_mime($imageBytes);
	if ($imageMime === '' || stripos($imageMime, 'image/') !== 0) {
		return array(
			'success' => false,
			'message' => 'Dữ liệu upload không phải ảnh hợp lệ',
		);
	}

	$tokenRegisterUrl = trim((string)(getenv('SOF_TOKEN_REGISTER_URL') ?: getenv('ERP_HTTP_TOKEN_REGISTER_URL') ?: 'http://192.168.1.87/createtoken/index.php'));
	if ($tokenRegisterUrl === '') {
		return array(
			'success' => false,
			'message' => 'Chưa cấu hình URL đăng ký token ảnh',
		);
	}

	$payloadAttempts = array(
		array('username' => $username, 'ImgSOF' => base64_encode($imageBytes)),
		array('username' => $username, '_ImgSOF' => base64_encode($imageBytes)),
	);

	$userTokenCandidates = array_values(array_unique(array_filter(array(
		trim((string)getenv('SOF_TOKEN_REGISTER_USER_TOKEN')),
		trim((string)getenv('TOKEN_REGISTER_USER_TOKEN')),
		trim((string)getenv('ERP_HTTP_SOF_DEV_TOKEN')),
		'SOF2025DEVELOPER',
	))));
	$adminTokenCandidates = array_values(array_unique(array(
		trim((string)getenv('SOF_TOKEN_REGISTER_ADMIN_TOKEN')),
		trim((string)getenv('TOKEN_REGISTER_ADMIN_TOKEN')),
		trim((string)getenv('ERP_HTTP_TOKEN_REGISTER_API_TOKEN')),
		'SOF2025ADMIN',
		'',
	)));

	$lastMessage = 'Đăng ký TokenSOF thất bại';
	foreach ($payloadAttempts as $payload) {
		$jsonBody = json_encode($payload);
		if (!is_string($jsonBody) || $jsonBody === '') {
			continue;
		}

		foreach ($userTokenCandidates as $userToken) {
			foreach ($adminTokenCandidates as $adminToken) {
				$headers = array(
					'Accept: application/json',
					'Content-Type: application/json; charset=UTF-8',
					'X-DEVICE-TYPE: desktop',
					'X-SOF-USER-TOKEN: ' . $userToken,
					'SOF-User-Token: ' . $userToken,
					'SOF-User: ' . $username,
					'Admin-Contact: ' . $username,
				);
				if ($adminToken !== '') {
					$headers[] = 'SOF-Token: ' . $adminToken;
				}

				$responseBody = false;
				$curlError = '';
				$statusCode = 0;

				if (function_exists('curl_init')) {
					$curl = curl_init($tokenRegisterUrl);
					curl_setopt_array($curl, array(
						CURLOPT_RETURNTRANSFER => true,
						CURLOPT_FOLLOWLOCATION => true,
						CURLOPT_CONNECTTIMEOUT => 5,
						CURLOPT_TIMEOUT => 20,
						CURLOPT_POST => true,
						CURLOPT_POSTFIELDS => $jsonBody,
						CURLOPT_HTTPHEADER => $headers,
					));
					$responseBody = curl_exec($curl);
					$curlError = curl_error($curl);
					$statusCode = (int)curl_getinfo($curl, CURLINFO_HTTP_CODE);
					curl_close($curl);
				} else {
					$context = stream_context_create(array(
						'http' => array(
							'method' => 'POST',
							'header' => implode("\r\n", $headers) . "\r\n",
							'content' => $jsonBody,
							'timeout' => 20,
							'ignore_errors' => true,
						),
					));
					$responseBody = @file_get_contents($tokenRegisterUrl, false, $context);
				}

				if ($responseBody === false || $responseBody === '') {
					if ($curlError !== '') {
						$lastMessage = $curlError;
					} elseif ($statusCode > 0) {
						$lastMessage = 'HTTP ' . $statusCode;
					}
					continue;
				}

				$decoded = json_decode($responseBody, true);
				if (!is_array($decoded)) {
					$lastMessage = 'Dịch vụ TokenSOF trả về dữ liệu không hợp lệ';
					continue;
				}

				$tokenValue = trim((string)($decoded['TokenSOF'] ?? $decoded['token'] ?? $decoded['tokenSOF'] ?? ''));
				if ($tokenValue !== '') {
					return array(
						'success' => true,
						'token' => $tokenValue,
						'message' => trim((string)($decoded['Message'] ?? $decoded['message'] ?? 'OK')),
						'raw' => $decoded,
					);
				}

				$decodedMessage = trim((string)($decoded['Message'] ?? $decoded['message'] ?? $decoded['error'] ?? 'Đăng ký TokenSOF thất bại'));
				if ($decodedMessage !== '') {
					$lastMessage = $decodedMessage;
				}
			}
		}
	}

	return array(
		'success' => false,
		'message' => $lastMessage,
	);
}

function cc_update_employee_image_token($employeeId, $imageToken, $columnName = 'lv007')
{
	$employeeId = trim((string)$employeeId);
	$imageToken = trim((string)$imageToken);
	$columnName = trim((string)$columnName);

	if ($employeeId === '' || $imageToken === '' || !preg_match('/^lv[0-9]{3}$/', $columnName)) {
		return array(
			'success' => false,
			'message' => 'Thiếu dữ liệu cập nhật token ảnh',
		);
	}

	$db = db_connect();
	if (!$db) {
		return array(
			'success' => false,
			'message' => 'Không kết nối được cơ sở dữ liệu',
		);
	}

	$employeeIdEsc = mysqli_real_escape_string($db, $employeeId);
	$imageTokenEsc = mysqli_real_escape_string($db, $imageToken);
	$sql = "UPDATE hr_lv0020 SET {$columnName}='{$imageTokenEsc}' WHERE lv001='{$employeeIdEsc}'";
	$result = db_query($sql);
	if (!$result) {
		return array(
			'success' => false,
			'message' => 'Lỗi cập nhật token ảnh: ' . sof_error(),
		);
	}

	$affectedRows = (int)mysqli_affected_rows($db);
	if ($affectedRows <= 0) {
		$checkSql = "SELECT lv001 FROM hr_lv0020 WHERE lv001='{$employeeIdEsc}' LIMIT 1";
		$checkResult = db_query($checkSql);
		if (!$checkResult || mysqli_num_rows($checkResult) <= 0) {
			return array(
				'success' => false,
				'message' => 'Không tìm thấy nhân viên trong ERP',
			);
		}
	}

	return array(
		'success' => true,
		'employee_id' => $employeeId,
		'image_token' => $imageToken,
		'affected_rows' => $affectedRows,
		'unchanged' => $affectedRows <= 0,
		'table' => 'hr_lv0020',
		'column' => $columnName,
	);
}
switch ($vtable) {
	case "hr_lv0020":
		include_once("./class/hr_lv0020.php");
		$hr_lv0020 = new hr_lv0020($_SESSION['ERPSOFV2RRight'], $_SESSION['ERPSOFV2RUserID'], 'Tc0002');

		switch ($vfun) {
			case "data":
				$vOutput = $hr_lv0020->getNhanVien();
				break;

			case "LayNhanVien":
				$vOutput = array();
				$objEmp = $hr_lv0020->MB_LayNhanVien();
				if ($objEmp) {
					while ($vrow = db_fetch_array($objEmp)) {
						$mappedRow = cc_map_employee_row($vrow);
						if (!empty($mappedRow)) {
							$vOutput[] = $mappedRow;
						}
					}
				}
				break;

			case "layNhanVienTheoMa":
				$maNhanVien = $input['maNhanVien'] ?? $_POST['maNhanVien'] ?? "";
				$maNhanVien = trim((string)$maNhanVien);
				$vOutput = array();
				if ($maNhanVien === "") {
					break;
				}
				$objEmp = $hr_lv0020->layNhanVienTheoMa($maNhanVien);
				if ($objEmp) {
					while ($vrow = db_fetch_array($objEmp)) {
						$mappedRow = cc_map_employee_row($vrow);
						if (!empty($mappedRow)) {
							$vOutput[] = $mappedRow;
						}
					}
				}
				break;

			case "updateImageToken":
				$lv001 = $input['lv001'] ?? $_POST['lv001'] ?? "";
				$tokenAnh = $input['tokenAnh'] ?? $_POST['tokenAnh'] ?? "";
				$cot = $input['cot'] ?? $_POST['cot'] ?? "lv007";

				$lv001 = trim((string)$lv001);
				$tokenAnh = trim((string)$tokenAnh);
				$cot = trim((string)$cot);

				if ($lv001 === "") {
					$vOutput = array('success' => false, 'message' => 'Thiếu mã nhân viên');
					break;
				}
				if ($tokenAnh === "") {
					$vOutput = array('success' => false, 'message' => 'Thiếu token ảnh');
					break;
				}
				if (!preg_match('/^lv[0-9]{3}$/', $cot)) {
					$vOutput = array('success' => false, 'message' => 'Tên cột không hợp lệ');
					break;
				}

				$db = db_connect();
				if (!$db) {
					$vOutput = array('success' => false, 'message' => 'Không kết nối được cơ sở dữ liệu');
					break;
				}

				$lv001Esc = mysqli_real_escape_string($db, $lv001);
				$tokenEsc = mysqli_real_escape_string($db, $tokenAnh);
				$sql = "UPDATE hr_lv0020 SET {$cot}='{$tokenEsc}' WHERE lv001='{$lv001Esc}'";
				$result = db_query($sql);
				if (!$result) {
					$vOutput = array('success' => false, 'message' => 'Lỗi cập nhật token ảnh: ' . sof_error());
					break;
				}

				$affectedRows = mysqli_affected_rows($db);
				if ($affectedRows <= 0) {
					$checkSql = "SELECT lv001 FROM hr_lv0020 WHERE lv001='{$lv001Esc}' LIMIT 1";
					$checkResult = db_query($checkSql);
					if (!$checkResult || mysqli_num_rows($checkResult) <= 0) {
						$vOutput = array('success' => false, 'message' => 'Không tìm thấy nhân viên trong ERP');
						break;
					}

					$vOutput = array(
						'success' => true,
						'employee_id' => $lv001,
						'image_token' => $tokenAnh,
						'affected_rows' => 0,
						'unchanged' => true,
						'table' => 'hr_lv0020',
						'column' => $cot,
					);
					break;
				}

				$vOutput = array(
					'success' => true,
					'employee_id' => $lv001,
					'image_token' => $tokenAnh,
					'affected_rows' => (int)$affectedRows,
					'unchanged' => false,
					'table' => 'hr_lv0020',
					'column' => $cot,
				);
				break;
		}
		break;

	case "cr_lv0382":
		switch ($vfun) {
			case "uploadAnh":
				$lv001 = trim((string)($input['lv001'] ?? $_POST['lv001'] ?? ""));
				$cot = trim((string)($input['cot'] ?? $_POST['cot'] ?? "lv008"));

				if ($lv001 === "") {
					$vOutput = array('success' => false, 'message' => 'Thiếu mã nhân viên');
					break;
				}
				if (!preg_match('/^lv[0-9]{3}$/', $cot)) {
					$vOutput = array('success' => false, 'message' => 'Tên cột không hợp lệ');
					break;
				}

				$fileField = null;
				if (isset($_FILES['image'])) {
					$fileField = $_FILES['image'];
				} elseif (isset($_FILES['file'])) {
					$fileField = $_FILES['file'];
				}

				if (!is_array($fileField) || empty($fileField['tmp_name']) || !is_uploaded_file($fileField['tmp_name'])) {
					$vOutput = array('success' => false, 'message' => 'Không nhận được ảnh upload');
					break;
				}

				$fileData = @file_get_contents($fileField['tmp_name']);
				if ($fileData === false || $fileData === '') {
					$vOutput = array('success' => false, 'message' => 'Không đọc được dữ liệu ảnh');
					break;
				}

				$legacyOutput = array('success' => false, 'message' => '');
				if (preg_match('/^[0-9]+$/', $lv001)) {
					$legacyOutput = saveImageToDB($fileData, $cot, $lv001);
					if (!empty($legacyOutput['success'])) {
						$vOutput = $legacyOutput;
						if (!isset($vOutput['employee_id'])) {
							$vOutput['employee_id'] = $lv001;
						}
						if (!isset($vOutput['column'])) {
							$vOutput['column'] = $cot;
						}
						break;
					}
				}

				$uploadUsername = trim((string)($input['code'] ?? $_POST['code'] ?? lv_get_request_header_value('X-USER-CODE') ?? ''));
				$tokenResult = cc_create_sof_image_token($fileData, $uploadUsername);
				if (empty($tokenResult['success'])) {
					$vOutput = array(
						'success' => false,
						'message' => $tokenResult['message'] ?? 'Không tạo được token ảnh',
						'employee_id' => $lv001,
						'column' => $cot,
					);
					if (!empty($legacyOutput['message'])) {
						$vOutput['legacy_error'] = $legacyOutput['message'];
					}
					break;
				}

				$updateTokenOutput = cc_update_employee_image_token($lv001, $tokenResult['token'], 'lv007');
				if (empty($updateTokenOutput['success'])) {
					$vOutput = $updateTokenOutput;
					$vOutput['employee_id'] = $lv001;
					$vOutput['column'] = $cot;
					$vOutput['image_token'] = $tokenResult['token'];
					if (!empty($legacyOutput['message'])) {
						$vOutput['legacy_error'] = $legacyOutput['message'];
					}
					break;
				}

				$vOutput = array(
					'success' => true,
					'message' => 'Upload ảnh và cập nhật token ảnh thành công.',
					'employee_id' => $lv001,
					'column' => $cot,
					'image_token' => $tokenResult['token'],
					'token_column' => 'lv007',
					'storage_method' => 'token_service',
				);
				if (!empty($legacyOutput['message'])) {
					$vOutput['legacy_warning'] = $legacyOutput['message'];
				}
				break;
		}
		break;

	case "getAnhTable":
		switch ($vfun) {
			case "getAnh":
				$lv001 = $input['lv001'] ?? $_POST['lv001'] ?? ($_GET['lv001'] ?? '');
				$cot = $input['cot'] ?? $_POST['cot'] ?? ($_GET['cot'] ?? '');
				$cot = trim((string)$cot);

				if (!preg_match('/^lv[0-9]{3}$/', $cot)) {
					http_response_code(400);
					echo "Invalid cot parameter.";
					exit();
				}

				if (!$lv001) {
					http_response_code(400);
					echo "Missing lv001 parameter.";
					exit();
				}

				$db = db_connect();
				if ($db === false) {
					http_response_code(500);
					echo "Loi ket noi den co so du lieu.";
					exit();
				}

				$lv001 = mysqli_real_escape_string($db, $lv001);
				$docsTable = function_exists('lv_docs_table_name') ? lv_docs_table_name('cr_lv0382') : '`cr_lv0382`';
				if ($docsTable === '') {
					http_response_code(500);
					echo "Cannot resolve documents table.";
					mysqli_close($db);
					exit();
				}

				$selectColumns = array($cot);
				foreach (array('lv008', 'lv007') as $fallbackColumn) {
					if (!in_array($fallbackColumn, $selectColumns, true)) {
						$selectColumns[] = $fallbackColumn;
					}
				}
				$sql = "SELECT " . implode(', ', $selectColumns) . " FROM {$docsTable} WHERE lv002 = '$lv001'";
				$vresult = db_query($sql);

				if ($vresult && mysqli_num_rows($vresult) > 0) {
					$imageData = mysqli_fetch_assoc($vresult);
					$imageBinary = '';
					foreach ($selectColumns as $columnName) {
						$candidate = isset($imageData[$columnName]) ? $imageData[$columnName] : '';
						if (is_string($candidate) && $candidate !== '') {
							$imageBinary = $candidate;
							break;
						}
					}

					if ($imageBinary !== '') {
						header("Content-Type: image/jpeg");
						echo $imageBinary;
						mysqli_free_result($vresult);
						mysqli_close($db);
						exit();
					}

					mysqli_free_result($vresult);
				} elseif ($vresult) {
					mysqli_free_result($vresult);
				}

				$sqlEmployee = "SELECT lv007 FROM hr_lv0020 WHERE lv001 = '$lv001' LIMIT 1";
				$employeeResult = db_query($sqlEmployee);
				$imageToken = '';
				if ($employeeResult && mysqli_num_rows($employeeResult) > 0) {
					$employeeRow = mysqli_fetch_assoc($employeeResult);
					$imageToken = cc_extract_image_token($employeeRow['lv007'] ?? '');
				}
				if ($employeeResult) {
					mysqli_free_result($employeeResult);
				}

				if ($imageToken !== '') {
					$imageUrl = cc_build_token_image_url($imageToken);
					list($remoteImageData, $remoteContentType, $remoteError) = cc_fetch_remote_image_bytes($imageUrl);
					if ($remoteImageData !== false && $remoteImageData !== '') {
						header("Content-Type: " . ($remoteContentType !== '' ? $remoteContentType : 'image/jpeg'));
						echo $remoteImageData;
						mysqli_close($db);
						exit();
					}
					if ($remoteError !== '') {
						error_log('getAnh fallback token fetch failed for ' . $lv001 . ': ' . $remoteError);
					}
				}

				http_response_code(404);
				echo "Image not found.";
				mysqli_close($db);
				exit();
		}
		break;
}

include("chamcong_ngocchung.php");
if ($vfun == 'data') {
	$i = 1;
	echo "[";
	foreach ($vOutput as $vData) {
		if ($i > 1)
			echo ",";
		echo json_encode($vData);

		$i++;
	}
	echo "]";
} else
	echo json_encode($vOutput);
