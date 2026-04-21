<?php
header('Content-Type: application/json');
ob_start();

$ipClient = $_SERVER['REMOTE_ADDR'];
system('arp ' . $ipClient . ' -a');
$macOutput = ob_get_contents();
ob_clean();

$macPos = strpos($macOutput, " " . $ipClient . " ");
$mac = substr($macOutput, ($macPos + strlen($ipClient) + 2), 30);

include("../sof/config.php");
include("../sof/function.php");

$username = isset($_SERVER['HTTP_X_USER_USERNAME']) ? $_SERVER['HTTP_X_USER_USERNAME'] : '';
$token = isset($_SERVER['HTTP_X_USER_TOKEN']) ? $_SERVER['HTTP_X_USER_TOKEN'] : '';

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
  case 'logoutTable': {
      $response = [
        'status' => 1009,
      ];

      if (empty($username) || empty($token)) {
        $response['status'] = 1009;
      } else {
        $sql = "SELECT * FROM sl_lv0009 WHERE lv198 = '$username' AND lv200 = '$token'";
        $result = db_query($sql);
        if ($result && db_num_rows($result) > 0) {
          $updateSql = "UPDATE sl_lv0009 SET lv200 = '', lv201 = NULL WHERE lv198 = '$username'";
          if (db_query($updateSql)) {
            $response = [
              'status' => 2003,
            ];
          }
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
