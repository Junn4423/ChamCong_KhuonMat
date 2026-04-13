<?php
// error_reporting(E_ERROR);
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

// Cho phép từ mọi origin (hoặc cụ thể origin nếu muốn)
header("Access-Control-Allow-Origin: *");

// Cho phép các phương thức
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");

// Cho phép các header custom (như Content-Type)
header("Access-Control-Allow-Headers: Content-Type, Authorization, SOF-User-Token");

// Nếu là request OPTIONS (preflight), trả về sớm
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
	http_response_code(200);
	exit();
}
session_start();


header("Content-Type: application/json; charset=UTF-8");
include("config.php");
include("function.php");
include("lv_controler.php");
include("kebao.php");
include("haolamvatttu.php");

$inputJSON = file_get_contents('php://input');
$input = json_decode($inputJSON, true);
$vtable = $input['table'] ?? $_POST['table'] ?? "";
$vfun   = $input['func']  ?? $_POST['func']  ?? "";


$vlimit = isset($input['limit']) ? $input['limit'] : (isset($_POST['limit']) ? $_POST['limit'] : "");
$vmonth = isset($input['month']) ? $input['month'] : (isset($_POST['month']) ? $_POST['month'] : "");
$vyear = isset($input['year']) ? $input['year'] : (isset($_POST['year']) ? $_POST['year'] : "");


$vOutput = array();

function saveImageToDB($fileData, $cot, $lv001)
{
	try {
		// Kết nối đến CSDL
		$db = db_connect();
		// Kiểm tra xem lv002 = $lv001 đã tồn tại chưa
		$checkSql = "SELECT COUNT(*) as count FROM erp_sof_documents_v4_0.cr_lv0382 WHERE lv002 = ?";

		$checkStmt = mysqli_prepare($db, $checkSql);
		mysqli_stmt_bind_param($checkStmt, "s", $lv001);
		mysqli_stmt_execute($checkStmt);
		$result = mysqli_stmt_get_result($checkStmt);
		$row = mysqli_fetch_assoc($result);
		$exists = $row['count'] > 0;
		mysqli_stmt_close($checkStmt);


		if ($exists) {
			$sql = "UPDATE erp_sof_documents_v4_0.cr_lv0382 SET $cot = ? WHERE lv002 = ?";
		} else {
			$sql = "INSERT INTO erp_sof_documents_v4_0.cr_lv0382 (lv002, $cot) VALUES (?, ?)";
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



switch ($vtable) {

	case "da_lh0012":
		// API cho bảng FAQ (da_lh0012 = câu hỏi, da_lh0013 = câu trả lời)
		switch ($vfun) {
			case "data":
				// Lấy tất cả FAQ với câu trả lời từ bảng da_lh0013
				$vArrRe = [];
				$vsql = "SELECT 
					q.lv001 as id, 
					q.lv002 as question, 
					q.lv003 as tags, 
					q.lv004 as date,
					a.lv001 as answer_id,
					a.lv003 as answer
				FROM da_lh0012 q
				LEFT JOIN da_lh0013 a ON a.lv002 = q.lv001
				ORDER BY q.lv001";
				$vresult = db_query($vsql);
				while ($vrow = mysqli_fetch_assoc($vresult)) {
					$vArrRe[] = $vrow;
				}
				$vOutput = $vArrRe;
				break;
			case "insertQuestion":
				// Thêm câu hỏi mới
				$cauHoi = $input['cauHoi'] ?? $_POST['cauHoi'] ?? "";
				$tags = $input['tags'] ?? $_POST['tags'] ?? "General";
				db_connect();
				$cauHoi = sof_escape_string($cauHoi);
				$tags = sof_escape_string($tags);
				$vsql = "INSERT INTO da_lh0012 (lv002, lv003, lv004) VALUES ('$cauHoi', '$tags', CURDATE())";
				$vresult = db_query($vsql);
				if ($vresult) {
					$vOutput = [
						'success' => true,
						'id' => sof_insert_id(),
						'message' => 'Thêm câu hỏi thành công'
					];
				} else {
					$vOutput = ['success' => false, 'message' => 'Lỗi: ' . sof_error()];
				}
				break;
			case "updateQuestion":
				// Cập nhật câu hỏi
				$ma = $input['ma'] ?? $_POST['ma'] ?? "";
				$cauHoi = $input['cauHoi'] ?? $_POST['cauHoi'] ?? "";
				$tags = $input['tags'] ?? $_POST['tags'] ?? "";
				db_connect();
				$ma = sof_escape_string($ma);
				$cauHoi = sof_escape_string($cauHoi);
				$tags = sof_escape_string($tags);
				$vsql = "UPDATE da_lh0012 SET lv002='$cauHoi', lv003='$tags' WHERE lv001='$ma'";
				$vresult = db_query($vsql);
				$vOutput = $vresult ? ['success' => true, 'message' => 'Cập nhật thành công'] : ['success' => false, 'message' => 'Lỗi: ' . sof_error()];
				break;
			case "deleteQuestion":
				// Xóa câu hỏi
				$ma = $input['ma'] ?? $_POST['ma'] ?? "";
				db_connect();
				$ma = sof_escape_string($ma);
				$vsql = "DELETE FROM da_lh0012 WHERE lv001='$ma'";
				$vresult = db_query($vsql);
				$vOutput = $vresult ? ['success' => true, 'message' => 'Xóa thành công'] : ['success' => false, 'message' => 'Lỗi: ' . sof_error()];
				break;
			default:
				$vOutput = ['success' => false, 'message' => 'Hành động da_lh0012 không hợp lệ'];
		}
		break;

	case "cr_lv0330":
		include("cr_lv0330.php");
		$cr_lv0330 = new cr_lv0330($_SESSION['ERPSOFV2RRight'], $_SESSION['ERPSOFV2RUserID'], 'Tc0002');
		switch ($vfun) {
			case "data":
				$vOutput = $cr_lv0330->getAll();
				break;
			case "capNhat":
				$lv001 = $input['lv001'] ?? $_POST['lv001'] ?? "";
				// echo $lv001;
				$lv004 = $input['lv004'] ?? $_POST['lv004'] ?? "";
				$lv005 = $input['lv005'] ?? $_POST['lv005'] ?? "";
				$lv012 = $input['lv012'] ?? $_POST['lv012'] ?? "";
				$lv013 = $input['lv013'] ?? $_POST['lv013'] ?? "";
				$lv014 = $input['lv014'] ?? $_POST['lv014'] ?? "";
				$lv015 = $input['lv015'] ?? $_POST['lv015'] ?? "";
				$lv016 = $input['lv016'] ?? $_POST['lv016'] ?? "";
				$lv353 = $input['lv353'] ?? $_POST['lv353'] ?? "";
				$lv360 = $input['lv360'] ?? $_POST['lv360'] ?? "";
				$lv361 = $input['lv361'] ?? $_POST['lv361'] ?? "";
				$lv361 = $input['lv361'] ?? $_POST['lv362'] ?? "";
				$lv363 = $input['lv363'] ?? $_POST['lv363'] ?? "";
				$lv364 = $input['lv364'] ?? $_POST['lv364'] ?? "";
				$lv365 = $input['lv365'] ?? $_POST['lv365'] ?? "";
				$lv366 = $input['lv366'] ?? $_POST['lv366'] ?? "";
				$lv367 = $input['lv367'] ?? $_POST['lv367'] ?? "";
				$lv368 = $input['lv368'] ?? $_POST['lv368'] ?? "";
				$lv369 = $input['lv369'] ?? $_POST['lv369'] ?? "";
				$lv370 = $input['lv370'] ?? $_POST['lv370'] ?? "";
				$lv371 = $input['lv371'] ?? $_POST['lv371'] ?? "";
				$lv802 = $input['lv802'] ?? $_POST['lv802'] ?? "";
				$lv809 = $input['lv809'] ?? $_POST['lv809'] ?? "";
				$vOutput = $cr_lv0330->capNhat(
					$lv001,
					$lv004,
					$lv005,
					$lv012,
					$lv013,
					$lv014,
					$lv015,
					$lv016,
					$lv353,
					$lv360,
					$lv361,
					$lv362,
					$lv363,
					$lv364,
					$lv365,
					$lv366,
					$lv367,
					$lv368,
					$lv369,
					$lv370,
					$lv371,
					$lv802,
					$lv809
				);
				break;
			case "xoaPhieu":
				$lv001 = $input['lv001'] ?? $_POST['lv001'] ?? "";
				$vOutput = $cr_lv0330->xoaPhieu($lv001);
				break;
		}
		break;

	case "sl_lv0001":
		include("sl_lv0001.php");
		$sl_lv0001 = new sl_lv0001($_SESSION['ERPSOFV2RRight'], $_SESSION['ERPSOFV2RUserID'], 'Tc0002');
		switch ($vfun) {
			case "data":
				$vOutput = $sl_lv0001->getKhachHang();
				break;
		}
		break;
	case "sl_lv0013":
		include("sl_lv0013.php");
		switch ($vfun) {
			case "insert":
				$rawLv002 = $input['maKH'] ?? $_POST['maKH'] ?? "";
				$rawLv003 = $input['tenKH'] ?? $_POST['tenKH'] ?? "";
				// $rawLv094 = $input['caLam'] ?? $_POST['caLam'] ?? "";
				$rawLv021 = $input['ngayLam'] ?? $_POST['ngayLam'] ?? "";
				$rawLv005 = $input['ngayKetThuc'] ?? $_POST['ngayKetThuc'] ?? "";
				$rawItemID = $input['vItemId'] ?? $input['itemId'] ?? $_POST['vItemId'] ?? $_POST['itemId'] ?? "";
				// $vPrice = (float)($input['price'] ?? $_POST['price'] ?? 0);
				// $vPercent = (float)($input['percent'] ?? $_POST['percent'] ?? 0);
				$rawLv104 = $input['email'] ?? $_POST['email'] ?? "";
				$rawLv009 = $input['sdt'] ?? $_POST['sdt'] ?? "";
				$rawLv030 = $input['nguoiDaiDien'] ?? $_POST['nguoiDaiDien'] ?? "";

				// Kết nối database
				db_connect();

				// $	 = (int)($input['update'] ?? $_POST['update'] ?? 0);
				$lv002 = sof_escape_string($rawLv002);
				$lv003 = sof_escape_string($rawLv003);
				// $lv094 = sof_escape_string($rawLv094);
				$lv021 = sof_escape_string($rawLv021);
				$lv005 = sof_escape_string($rawLv005);
				$lv009 = sof_escape_string($rawLv009);
				$lv030 = sof_escape_string($rawLv030);
				$lv104 = sof_escape_string($rawLv104);
				$lvItemID = sof_escape_string($rawItemID);

				$vsql = "INSERT INTO sl_lv0013 (lv002, lv003, lv011, lv027, lv021, lv005,lv104,lv105, lv009, lv030) VALUES ('{$lv002}', '{$lv003}', 0, 0, '{$lv021}', '{$lv005}', '{$lv104}', now(), '{$lv009}', '{$lv030}')";
				$vresult = db_query($vsql);
				if ($vresult) {
					$vContractID = sof_insert_id();
					$detailMessage = '';
					if ($lvItemID !== '') {
						$vDetailID = '';
						$detailCheckSql = "SELECT lv001 FROM cr_lv0276 WHERE lv002='{$vContractID}' AND lv003='{$lvItemID}' LIMIT 1";
						$detailCheckResult = db_query($detailCheckSql);
						if ($detailCheckResult) {
							$detailRow = db_fetch_array($detailCheckResult);
							if ($detailRow) {
								$vDetailID = $detailRow['lv001'];
							}
						}
						$detailSql = "INSERT INTO cr_lv0276(lv002,lv003,lv004,lv005,lv006,lv007,lv008,lv011,lv018,lv019) SELECT '{$vContractID}','{$lvItemID}', 1, A.lv004, 0, A.lv008, 0, 0, A.lv009, A.lv002 FROM sl_lv0007 A WHERE lv001='{$lvItemID}'";
						$detailResult = db_query($detailSql);
						$detailMessage = $detailResult ? 'Chi tiết đã được cập nhật.' : ('Chi tiết không thể cập nhật: ' . sof_error());
					}
					$vOutput = [
						'success' => true,
						'message' => 'Thêm đơn hàng thành công.',
						'lv001' => $vContractID,
						'detail' => $detailMessage
					];
				} else {
					$vOutput = [
						'success' => false,
						'message' => 'Không thể thêm đơn hàng: ' . sof_error()
					];
				}
				break;
			default:
				$vOutput = ['success' => false, 'message' => 'Hành động sl_lv0013 không hợp lệ.'];
		}
		break;
	case "cr_lv0334":
		switch ($vfun) {
			case "data":
				$vArrRe = [];
				$vsql = "SELECT * FROM `cr_lv0334`";
				$vresult = db_query($vsql);
				while ($vrow = mysqli_fetch_assoc($vresult)) {
					$vArrRe[] = $vrow;
				}
				$vOutput = $vresult;
		}
		break;


	case "cr_lv0052":
		include("cr_lv0052.php");
		$cr_lv0052 = new cr_lv0052($_SESSION['ERPSOFV2RRight'], $_SESSION['ERPSOFV2RUserID'], 'Tc0002');
		switch ($vfun) {
			case "data":
				$lv001 = $input['lv001'] ?? $_POST['lv001'] ?? "";
				$vOutput = $cr_lv0052->getChiTiet($lv001);
				break;
			case "xoa":
				$lv001 = $input['lv001'] ?? $_POST['lv001'] ?? "";
				$vOutput = $cr_lv0052->xoa($lv001);
		}
		break;

	case "hr_lv0020":
		include("./class/hr_lv0020.php");
		$hr_lv0020 = new hr_lv0020($_SESSION['ERPSOFV2RRight'], $_SESSION['ERPSOFV2RUserID'], 'Tc0002');
		switch ($vfun) {
			case "data":
				$vOutput = $hr_lv0020->getNhanVien();
		}
		break;

	case "cr_lv0382":
		include("cr_lv0382.php");
		$cr_lv0382 = new cr_lv0382($_SESSION['ERPSOFV2RRight'], $_SESSION['ERPSOFV2RUserID'], 'Tc0002');
		switch ($vfun) {
			case "data":
				$lv001 = $input['lv001'] ?? $_POST['lv001'] ?? "";
				$vOutput = $cr_lv0382->getChiTietPhieu($lv001);
				break;
			case "uploadAnh":
				$lv001 = $input['lv001'] ?? $_POST['lv001'] ?? "";
				$cot = $input['cot'] ?? $_POST['cot'] ?? "";
				if (isset($_FILES['image'])) {
					$file = $_FILES['image'];
					$fileData = file_get_contents($file['tmp_name']);
					$vOutput = saveImageToDB($fileData, $cot, $lv001);
				} else {
					echo json_encode([
						'status' => 'error',
						'message' => 'Không nhận được ảnh.'
					]);
				}
				break;

			case "themMoi":
				$lv001 = $input['lv001'] ?? $_POST['lv001'] ?? "";
				$lv003 = $input['lv003'] ?? $_POST['lv003'] ?? "";
				$lv008 = $input['lv008'] ?? $_POST['lv008'] ?? "";
				$vOutput = $cr_lv0382->themMoi($lv001, $lv003, $lv008);
				break;

			case "xoa":
				$lv001 = $input['lv001'] ?? $_POST['lv001'] ?? "";
				$vOutput = $cr_lv0382->xoa($lv001);
				break;

			case "sua":
				$lv001 = $input['lv001'] ?? $_POST['lv001'] ?? "";
				$lv003 = $input['lv003'] ?? $_POST['lv003'] ?? "";
				$lv008 = $input['lv008'] ?? $_POST['lv008'] ?? "";
				$vOutput = $cr_lv0382->sua($lv001, $lv003, $lv008);
				break;
		}
		break;

	case "getAnhTable":
		switch ($vfun) {
			case "getAnh":
				$lv001 = $input['lv001'] ?? $_POST['lv001'] ?? $_GET['lv001'];
				$cot = $input['cot'] ?? $_POST['cot'] ?? $_GET['cot'];
				if ($lv001) {
					// Kết nối đến CSDL
					$db = db_connect(); // Đảm bảo bạn đã gọi đúng hàm kết nối

					// Kiểm tra kết nối đến CSDL
					if ($db === false) {
						http_response_code(500);
						echo "Lỗi kết nối đến cơ sở dữ liệu.";
						break;
					}

					// Sử dụng mysqli_real_escape_string để bảo vệ khỏi SQL Injection
					$lv001 = mysqli_real_escape_string($db, $lv001);

					// Tạo câu truy vấn SQL
					$sql = "SELECT $cot FROM erp_sof_documents_v4_0.cr_lv0382 WHERE lv002 = '$lv001'";
					// Thực thi câu lệnh SQL
					$vresult = db_query($sql);

					if ($vresult && mysqli_num_rows($vresult) > 0) {
						// Lấy dữ liệu ảnh từ kết quả truy vấn
						$imageData = mysqli_fetch_assoc($vresult);
						$imageData = $imageData[$cot]; // Lấy dữ liệu của cột ảnh

						// Trả về ảnh dưới định dạng Content-Type đúng
						header("Content-Type: image/jpeg");
						echo $imageData;
					} else {
						http_response_code(404);
						echo "Image not found.";
					}

					// Đóng kết nối
					mysqli_free_result($vresult);
					mysqli_close($db); // Đảm bảo đóng kết nối sau khi sử dụng
				} else {
					http_response_code(400);
					echo "Missing lv001 parameter.";
				}
				break;
		}
	case "kanban_board":

		include("haolam.php"); // Include file controller mới
		$kanbanController = new KanbanBoardController();

		switch ($vfun) {
			case 'get_velocity_chart_data':
				$planId = $input['planId'] ?? $_GET['planId'] ?? '';
				$vOutput = $kanbanController->get_velocity_chart_data($planId);
				break;
			case 'get_cfd_chart_data':
				$planId = $input['planId'] ?? $_GET['planId'] ?? '';
				$vOutput = $kanbanController->get_cfd_chart_data($planId);
				break;
			// case 'get_cfd_chart_data_advanced':
			// 	$planId = isset($requestData['planId']) ? $requestData['planId'] : '';
			// 	$startDate = isset($requestData['startDate']) ? $requestData['startDate'] : null;
			// 	$endDate = isset($requestData['endDate']) ? $requestData['endDate'] : null;
			// 	$response = $kanbanController->get_cfd_chart_data_advanced($planId, $startDate, $endDate);
			// 	break;
			case 'get_burndown_chart_data':
				$planId = $input['planId'] ?? $_GET['planId'] ?? '';
				$vOutput = $kanbanController->get_burndown_chart_data($planId);
				break;
			case 'get_board':
				$projectId = $input['project_id'] ?? $_GET['project_id'] ?? 0;
				$vOutput = $kanbanController->get_board_data($projectId);
				break;
			case 'get_gantt_data':
				$projectId = $input['project_id'] ?? $_GET['project_id'] ?? 0;
				$vOutput = $kanbanController->get_gantt_data($projectId);
				break;
			case 'get_jo_lv0016_lv007':
				$filter = $input['filter'] ?? [];
				$vOutput = $kanbanController->get_jo_lv0016_lv007_data($filter);
				break;
			case 'get_user_task_count':
				$userId = $input['userId'] ?? '';
				$departmentId = $input['departmentId'] ?? '';
				$vOutput = $kanbanController->get_user_task_count($userId, $departmentId);
				break;
			case 'move_task':
				$vOutput = $kanbanController->move_task_data($input);
				break;
			// API CHO MOBILE
			// === PHASE MANAGEMENT ROUTES ===

			case 'get_phases':
				$vOutput = $kanbanController->get_phases_data($input);
				break;

			case 'create_column':
				$vOutput = $kanbanController->create_column_data($input);
				break;

			case 'update_column':
				$vOutput = $kanbanController->update_column_data($input);
				break;

			case 'delete_column':
				$vOutput = $kanbanController->delete_column_data($input);
				break;
			// === PROJECT MANAGEMENT ROUTES ===

			case 'get_projects':
				$vOutput = $kanbanController->get_projects_data($input);
				break;

			case 'create_project':
				$vOutput = $kanbanController->create_project_data($input);
				break;

			case 'update_project':
				$vOutput = $kanbanController->update_project_data($input);
				break;

			case 'delete_project':
				$vOutput = $kanbanController->delete_project_data($input);
				break;

			case 'check_project_plans':
				$vOutput = $kanbanController->check_project_plans_data($input);
				break;

			case 'add_all_departments_to_project':
				$projectId = $input['project_id'] ?? $_GET['project_id'] ?? 0;
				$vOutput = $kanbanController->addAllDepartmentsToProject($projectId);
				break;
			// index.php (router)
			case 'get_child_projects':
				$parentId = $input['parentId'] ?? $_GET['parentId'] ?? '';
				$vOutput = $kanbanController->get_child_projects($parentId);
				break;
			case 'get_project_status_list':
				$vOutput = $kanbanController->get_project_status_list();
				break;
			// === TASK MANAGEMENT ROUTES ===
			case 'get_tasks_by_project':
				$vOutput = $kanbanController->get_tasks_by_project_data($input);
				break;

			case 'create_task_da3':
				$vOutput = $kanbanController->create_taskda3_data($input);
				break;

			case 'update_task':
				$vOutput = $kanbanController->update_task_data($input);
				break;

			case 'delete_task':
				$vOutput = $kanbanController->delete_task_data($input);
				break;
			// === TASK ASSIGNMENT ROUTES ===
			case 'get_departments':
				$vOutput = $kanbanController->get_departments_data($input);
				break;

			case 'get_task_assignments':
				$vOutput = $kanbanController->get_task_assignments_data($input);
				break;

			case 'assign_task_to_departments': // Đổi tên từ assign_task_to_department
				$vOutput = $kanbanController->assign_task_to_departments_data($input);
				break;

			case 'update_task_assignment_status': // Đổi tên từ update_task_assignment
				$vOutput = $kanbanController->update_task_assignment_status_data($input);
				break;

			case 'remove_task_assignment':
				$vOutput = $kanbanController->remove_task_assignment_data($input);
				break;

			case 'take_task':
				$vOutput = $kanbanController->take_task_data($input);
				break;
			case 'take_task_web':
				$taskData = $input['data'] ?? [];
				$vOutput = $kanbanController->take_task_data($taskData);
				break;
			// === PLAN MANAGEMENT ===
			case 'get_employees':
				$vOutput = $kanbanController->get_employees_data($input);
				break;

			case 'get_plans':
				$vOutput = $kanbanController->get_plans_data($input);
				break;
			case 'get_assigned_plans': // <-- Thêm case này!
				$userId = $input['userId'] ?? $_GET['userId'] ?? ($_SESSION['userlogin_smcd'] ?? '');
				$vOutput = $kanbanController->get_assigned_plans_data($userId, $input);
				break;

			case 'create_plan':
				$vOutput = $kanbanController->create_plan_data($input);
				break;

			case 'update_plan':
				$vOutput = $kanbanController->update_plan_data($input);
				break;

			case 'delete_plan':
				$vOutput = $kanbanController->delete_plan_data($input);
				break;

			case 'get_plan_detail':
				$vOutput = $kanbanController->get_plan_detail_data($input);
				break;

			case 'get_plan_tasks':
				$vOutput = $kanbanController->get_plan_tasks_data($input);
				break;

			case 'create_plan_task':
				$vOutput = $kanbanController->create_plan_task_data($input);
				break;

			//=== ICON API ===
			case 'get_icon_list':
				$vOutput = $kanbanController->get_icon_list();
				break;

			case 'get_color_list':
				$vOutput = $kanbanController->get_color_list();
				break;

			case 'get_project_icons':
				$projectId = $input['projectId'] ?? $_GET['projectId'] ?? '0';
				$vOutput = $kanbanController->get_project_icons($projectId);
				break;
			//=================================================================================================
			case 'create_task':
				$userId = $input['userId'] ?? $_GET['userId'] ?? ($_SESSION['userlogin_smcd'] ?? '');
				$vOutput = $kanbanController->create_task_data($input, $userId);
				break;
			case 'update_column_order':
				$projectId = $input['project_id'] ?? $_GET['project_id'] ?? 0;
				$vOutput = $kanbanController->update_column_order_data($input, $projectId);
				break;
			case 'assign_user':
				$vOutput = $kanbanController->assign_user_data($input);
				break;
			case 'assign_user_to_stage':
				$vOutput = $kanbanController->assign_user_to_stage_data($input);
				break;
			case 'add_existing_column':
				$vOutput = $kanbanController->add_existing_column_data($input);
				break;
			case 'get_available_columns':
				$projectId = $input['project_id'] ?? $_GET['project_id'] ?? 0;
				$vOutput = $kanbanController->get_available_columns_data($projectId);
				break;
			case 'get_comments':
				$taskId = $input['taskId'] ?? $_GET['taskId'] ?? 0;
				$vOutput = $kanbanController->get_comments_data($taskId);
				break;
			case 'post_comment':
				$vOutput = $kanbanController->post_comment_data($input);
				break;
			case 'set_evaluation':
				$vOutput = $kanbanController->set_evaluation_data($input);
				break;
			case 'create_evaluation_icon':
				$vOutput = $kanbanController->create_evaluation_icon_data($input);
				break;
			case 'get_filtered_board':
				$projectId = $input['projectId'] ?? $_GET['projectId'] ?? '';
				$departmentId = $input['departmentId'] ?? $_GET['departmentId'] ?? '';
				$userId = $input['userId'] ?? $_GET['userId'] ?? ($_SESSION['userlogin_smcd'] ?? '');
				$user_role = $input['user_role'] ?? '';
				$vOutput = $kanbanController->get_filtered_board_data($projectId, $departmentId, $userId, $user_role);
				break;
			case 'get_department_overview':
				$departmentId = $input['departmentId'] ?? $_GET['departmentId'] ?? '';
				$userId = $input['userId'] ?? $_GET['userId'] ?? ($_SESSION['userlogin_smcd'] ?? '');
				$user_role = $input['user_role'] ?? '';
				$vOutput = $kanbanController->get_department_overview_data($departmentId, $userId, $user_role);
				break;
			case 'create_plan_tasks':
				$plan_id = $_POST['plan_id'] ?? '';
				$projectId = $input['projectId'] ?? $_GET['projectId'] ?? '';
				$userId = $input['userId'] ?? $_GET['userId'] ?? ($_SESSION['userlogin_smcd'] ?? '');
				$result = $kanbanController->CreatePlanTasks($plan_id, $projectId, $userId);
				break;
			case 'get_filters':
				$vOutput = $kanbanController->get_filters_data();
				break;
			case 'get_current_user':
				$userId = $_SESSION['userlogin_smcd'] ?? '';
				$vOutput = $kanbanController->get_current_user_data($userId);
				break;
			case 'move_task_for_user':
				$vOutput = $kanbanController->move_task_for_user_data($input);
				break;
			case 'move_to_in_progress':
				$vOutput = $kanbanController->move_to_in_progress_data($input);
				break;
			case 'toggle_completion_for_dept':
				$vOutput = $kanbanController->toggle_completion_for_dept_data($input);
				break;
			case 'get_projects_by_department':
				$departmentId = $input['departmentId'] ?? $_GET['departmentId'] ?? '';
				$vOutput = $kanbanController->get_projects_by_department_data($departmentId);
				break;
			case 'get_project_board_all_departments':
				$projectId = $input['projectId'] ?? $_GET['projectId'] ?? '';
				$vOutput = $kanbanController->get_project_board_all_departments_data($projectId);
				break;
			case 'get_recent_tasks_for_user':
				$userId = $input['userId'] ?? $_GET['userId'] ?? '';
				$vOutput = $kanbanController->get_recent_tasks_for_user_data($userId);
				break;
			case 'get_departments_by_project':
				$projectId = $input['projectId'] ?? $_GET['projectId'] ?? '';
				$vOutput = $kanbanController->get_departments_by_project_data($projectId);
				break;
			case 'create_work_log':
				$vOutput = $kanbanController->create_work_log_data($input);
				break;
			case 'get_work_log':
				$userId = $input['userId'] ?? $_GET['userId'] ?? ($_SESSION['userlogin_smcd'] ?? '');
				$taskId = $input['taskId'] ?? $_GET['taskId'] ?? '';
				$vOutput = $kanbanController->get_work_log_data($taskId, $userId);
				break;
			case 'get_work_log_for_timeline':
				$taskId = $input['taskId'] ?? $_GET['taskId'] ?? '';
				$vOutput = $kanbanController->get_work_log_data_for_timeline($taskId);
				break;
			case 'get_user_dashboard_stats':
				$userId = $input['userId'] ?? $_GET['userId'] ?? '';
				$vOutput = $kanbanController->get_user_dashboard_stats_data($userId);
				break;
			default:
				$vOutput = ['success' => false, 'message' => 'Hành động Kanban không hợp lệ.'];
				break;
		}
		break;
	case "ml_lv0100":
		include("./class/ml_lv0100.php");
		include("./class/ml_lv0009.php");
		include("./class/ml_lv0008.php");
		include("./class/lv_lv0066.php");
		include("./class/class.phpmailer.php");
		include("./class/class.smtp.php");
		switch ($vfun) {
			case "sendMail":
				$lvuser = $input['lvuser'] ?? $_POST['lvuser'] ?? "";
				$lvemail = $input['lvemail'] ?? $_POST['lvemail'] ?? "";
				$vTo = $input['vTo'] ?? $_POST['vTo'] ?? "";
				$lvtitle = $input['lvtitle'] ?? $_POST['lvtitle'] ?? "";
				$lvcontent = $input['lvcontent'] ?? $_POST['lvcontent'] ?? "";

				// Kiểm tra các tham số bắt buộc
				if (empty($lvuser) || empty($lvemail) || empty($vTo) || empty($lvtitle) || empty($lvcontent)) {
					$vOutput = [
						'success' => false,
						'message' => 'Thiếu tham số cần thiết (lvuser, lvemail, vTo, lvtitle, lvcontent)'
					];
					break;
				}

				// Gọi hàm gửi mail
				try {
					$mailService = new ml_lv0100($_SESSION['ERPSOFV2RRight'] ?? '', $_SESSION['ERPSOFV2RUserID'] ?? '', 'Ml0100');
					$lv_lv0066 = new lv_lv0066($_SESSION['ERPSOFV2RRight'] ?? '', $_SESSION['ERPSOFV2RUserID'] ?? '', 'Lv0066', true);
					$result = $lv_lv0066->LV_SendMail($lvcontent, $lvtitle, $lvuser, $lvemail, $vTo);
					$vOutput = $result;
				} catch (Exception $e) {
					$vOutput = [
						'success' => false,
						'message' => 'Lỗi gửi mail: ' . $e->getMessage()
					];
				}
				break;
			case "createAccountFromOrder":
				// Prepare the single input array
				$accountData = [
					'orderId'  => $input['orderId'] ?? $_POST['orderId'] ?? "",
					'email'    => $input['email'] ?? $_POST['email'] ?? "",
					'phone'    => $input['phone'] ?? $_POST['phone'] ?? "",
					'link'     => $input['link'] ?? $_POST['link'] ?? "",
					'title'    => $input['title'] ?? $_POST['title'] ?? "Thông tin tài khoản phần mềm ERP SOF.VN"
				];

				if (empty($accountData['orderId'])) {
					$vOutput = [
						'success' => false,
						'message' => 'Thiếu tham số orderId'
					];
					break;
				}

				try {
					ob_start();
					
					$lv_lv0066 = new lv_lv0066($_SESSION['ERPSOFV2RRight'] ?? '', $_SESSION['ERPSOFV2RUserID'] ?? 'System', 'Lv0066', true);
					
					// Pass the single array argument
					$lv_lv0066->LV_AutoCreateAccountFromPurchase($accountData);
					
					$outputLog = ob_get_clean();
					
					$vOutput = [
						'success' => true,
						'message' => 'Đã xử lý yêu cầu.',
						'log' => $outputLog 
					];
				} catch (Exception $e) {
					ob_end_clean();
					$vOutput = [
						'success' => false,
						'message' => 'Lỗi: ' . $e->getMessage()
					];
				}
				break;
			default:
				$vOutput = [
					'success' => false,
					'message' => 'Hành động mail không hợp lệ. Hỗ trợ: sendMail, sendMailWithAttachment'
				];
				break;
		}
		break;

	case "sl_lv0515":
		include("./class/lv_lv0066.php");
		switch ($vfun) {
			case "getDownloadLinks":
				// 1. Nhận tham số productId (Mã sản phẩm)
				$vProductId = $input['productId'] ?? $_POST['productId'] ?? "";
				if (empty($vProductId)) {
					$vOutput = [
						'success' => false,
						'message' => 'Thiếu tham số productId (Mã sản phẩm)'
					];
					break;
				}

				try {
					// 2. Khởi tạo class sl_lv0515
					// Đảm bảo file clsall/sl_lv0515.php đã được require trước đó
					$lv_lv0066 = new lv_lv0066($_SESSION['ERPSOFV2RRight'] ?? '', $_SESSION['ERPSOFV2RUserID'] ?? 'System', 'Lv0066', true);
					// 3. Gọi hàm lấy danh sách link
					$links = $lv_lv0066->LV_GetLinksByProduct($vProductId);
					
					if (!empty($links)) {
						$vOutput = [
							'success' => true,
							'message' => 'Lấy dữ liệu thành công',
							'data'    => $links
						];
					} else {
						$vOutput = [
							'success' => false,
							'message' => 'Không tìm thấy link download nào cho sản phẩm này.',
							'data'    => []
						];
					}

				} catch (Exception $e) {
					$vOutput = [
						'success' => false,
						'message' => 'Lỗi hệ thống: ' . $e->getMessage()
					];
				}
				break;

			default:
				$vOutput = [
					'success' => false,
					'message' => 'Hành động sl_lv0513 không hợp lệ. Hỗ trợ: insert'
				];
				break;
		}
		break;
	case "sl_lv0512":
		switch ($vfun) {
			case "insert":
				// Lấy tham số
				$lv002 = trim($input['lv002'] ?? $_POST['lv002'] ?? ''); // Mã phiếu
				$lv003 = trim($input['lv003'] ?? $_POST['lv003'] ?? ''); // Email người nhận
				$lv004 = trim($input['lv004'] ?? $_POST['lv004'] ?? ''); // Tiêu đề
				$lv005 = $input['lv005'] ?? $_POST['lv005'] ?? '';       // Nội dung HTML
				$lv006 = (int) ($input['lv006'] ?? $_POST['lv006'] ?? 0); // Trạng thái (0/1)

				// Validate bắt buộc
				if ($lv002 === '' || $lv003 === '' || $lv004 === '' || $lv005 === '') {
					$vOutput = [
						'success' => false,
						'message' => 'Thiếu tham số lv002, lv003, lv004 hoặc lv005'
					];
					break;
				}

				// Kết nối DB
				db_connect();
				$lv002Esc = sof_escape_string($lv002);
				$lv003Esc = sof_escape_string($lv003);
				$lv004Esc = sof_escape_string($lv004);
				$lv005Esc = sof_escape_string($lv005);
				$lv006Esc = (int) $lv006;

				$sql = "INSERT INTO sl_lv0512 (lv002, lv003, lv004, lv005, lv006) VALUES ('{$lv002Esc}', '{$lv003Esc}', '{$lv004Esc}', '{$lv005Esc}', {$lv006Esc})";
				$res = db_query($sql);
				if ($res) {
					$vOutput = [
						'success' => true,
						'message' => 'Lưu log mail thành công',
						'lv001' => sof_insert_id()
					];
				} else {
					$vOutput = [
						'success' => false,
						'message' => 'Không thể lưu log mail: ' . sof_error()
					];
				}
				break;

			default:
				$vOutput = [
					'success' => false,
					'message' => 'Hành động sl_lv0512 không hợp lệ. Hỗ trợ: insert'
				];
				break;
		}
		break;

	case "sl_lv0513":
		switch ($vfun) {
			case "insert":
				// Lấy tham số
				$lv002 = trim($input['tenKH'] ?? $_POST['tenKH'] ?? ''); // Tên khách hàng / Công ty
				$lv003 = trim($input['nguoiDaiDien'] ?? $_POST['nguoiDaiDien'] ?? ''); // Người đại diện
				$lv004 = trim($input['email'] ?? $_POST['email'] ?? ''); // Email
				$lv005 = trim($input['soDienThoai'] ?? $_POST['soDienThoai'] ?? ''); // Số điện thoại
				$lv006 = trim($input['dichVuQuanTam'] ?? $_POST['dichVuQuanTam'] ?? ''); // Dịch vụ quan tâm
				$lv007 = $input['noiDungTinNhan'] ?? $_POST['noiDungTinNhan'] ?? '';       // Nội dung tin nhắn
				$lv009 = $input['linkNguon'] ?? $_POST['linkNguon'] ?? ''; // Link nguồn gửi yêu cầu
				// Validate bắt buộc
				if ($lv002 === '' || $lv004 === '') {
					$vOutput = [
						'success' => false,
						'message' => 'Thiếu tham số bắt buộc: lv002 (Tên khách hàng) hoặc lv004 (Email)'
					];
					break;
				}

				// Validate email format
				if (!filter_var($lv004, FILTER_VALIDATE_EMAIL)) {
					$vOutput = [
						'success' => false,
						'message' => 'Email không hợp lệ'
					];
					break;
				}

				// Kết nối DB
				db_connect();
				$lv002Esc = sof_escape_string($lv002);
				$lv003Esc = sof_escape_string($lv003);
				$lv004Esc = sof_escape_string($lv004);
				$lv005Esc = sof_escape_string($lv005);
				$lv006Esc = sof_escape_string($lv006);
				$lv007Esc = sof_escape_string($lv007);

				$lv009Esc = sof_escape_string($lv009);

				$sql = "INSERT INTO sl_lv0513 (lv002, lv003, lv004, lv005, lv006, lv007, lv008, lv009) 
				        VALUES ('{$lv002Esc}', '{$lv003Esc}', '{$lv004Esc}', '{$lv005Esc}', '{$lv006Esc}', '{$lv007Esc}', NOW(), '{$lv009Esc}')";
				$res = db_query($sql);
				
				if ($res) {
					$vOutput = [
						'success' => true,
						'message' => 'Lưu yêu cầu tư vấn thành công',
						'lv001' => sof_insert_id()
					];
				} else {
					$vOutput = [
						'success' => false,
						'message' => 'Không thể lưu yêu cầu: ' . sof_error()
					];
				}
				break;

			default:
				$vOutput = [
					'success' => false,
					'message' => 'Hành động sl_lv0513 không hợp lệ. Hỗ trợ: insert'
				];
				break;
		}
		break;
}

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