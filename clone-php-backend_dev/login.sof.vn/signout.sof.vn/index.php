<?php
header('Content-Type: application/json');
//ob_start();
//$ipClient = $_SERVER['REMOTE_ADDR'];
//system('arp ' . $ipClient . ' -a');
//$macOutput = ob_get_contents();
//ob_clean();
$macPos = strpos($macOutput, " " . $ipClient . " ");
$mac = substr($macOutput, ($macPos + strlen($ipClient) + 2), 30);
$databaseNameHeader = isset($_SERVER["HTTP_X_DATABASE_NAME"]) ? trim($_SERVER["HTTP_X_DATABASE_NAME"]) : "";
if ($databaseNameHeader !== "") {
    if (preg_match("/^[A-Za-z0-9_]+$/", $databaseNameHeader)) {
        $_SESSION["DB_DATABASE"] = $databaseNameHeader;
    }
}

// error_reporting(E_ALL);
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
header("Content-Type: application/json; charset=UTF-8");
define("DB_DATABASE", isset($_SESSION['DB_DATABASE']) ? $_SESSION['DB_DATABASE'] : "");
include("../sof/config.php");
include("../sof/function.php");
include("../sof/constants.php");

$username = isset($_SERVER['HTTP_X_USER_USERNAME']) ? $_SERVER['HTTP_X_USER_USERNAME'] : '';
$token = isset($_SERVER['HTTP_X_USER_TOKEN']) ? $_SERVER['HTTP_X_USER_TOKEN'] : '';
$sofUserToken = isset($_SERVER['HTTP_X_SOF_USER_TOKEN']) ? $_SERVER['HTTP_X_SOF_USER_TOKEN'] : '';

if ($sofUserToken === '' || $sofUserToken != $SOF_USER_TOKEN) {
	echo json_encode(
		[
			'status' => 403,
			'message' => 'Forbidden',
		]
	);
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

switch ($method) {
	case 'logoutUser': {
			$response = [
				'status' => 1009,
			];

			if (empty($username)) {
				$response['status'] = 1009;
			} else {
				$updateSql = "UPDATE lv_lv0007 SET lv297 = '', lv298 = NULL WHERE lv001 = '$username'";
				if (db_query($updateSql)) {
					$response = [
						'status' => 2003,
					];
				}
			}
			break;
		}
	default: {
			break;
		}
}

echo json_encode($response);
ob_end_flush();
