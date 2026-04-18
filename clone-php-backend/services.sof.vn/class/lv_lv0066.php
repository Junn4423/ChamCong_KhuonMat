<?php
// error_reporting(E_ERROR);
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
class lv_lv0066 extends lv_controler
{

	var $lv001 = null;
	var $lv002 = null;
	var $lv003 = null;
	var $lv004 = null;
	var $lv005 = null;
	var $lv006 = null;
	var $lv095 = null;
	var $lv099 = null;
	public $DefaultFieldList = "lv199,lv001,lv002,lv004,lv005,lv665,lv668,lv669,lv670,lv671,lv672,lv673,lv674,lv675,lv097,lv098,lv296,lv297,lv298,lv299,lv397,lv398,lv399";
	////////////////////GetDate
	public $DateCurrent = "1900-01-01";
	public $Count = null;
	public $paging = null;
	public $lang = null;
	public $lv007 = null;
	public $lv906 = null;
	public $DeptList = null;
	public $ArrGetValueLink = null;




	protected $objhelp = 'lv_lv0066';
	////////////
	var $ArrOther = array();
	var $ArrPush = array();
	var $ArrFunc = array();
	var $ArrGet = array("lv001" => "2", "lv002" => "3", "lv003" => "4", "lv004" => "5", "lv005" => "6", "lv006" => "7", "lv007" => "9", "lv099" => "8", "lv094" => "10", "lv095" => "11", "lv909" => "910", "lv910" => "911", "lv911" => "912", "lv912" => "913", "lv906" => "907", "lv905" => "906", "lv904" => "905", "lv913" => "914", "lv914" => "915","lv665"=>"666","lv666"=>"667","lv667"=>"668","lv668"=>"669","lv669"=>"670","lv670"=>"671","lv671"=>"672","lv672"=>"673","lv673"=>"674","lv674"=>"675","lv675"=>"676","lv097"=>"98","lv098"=>"99","lv296"=>"297","lv297"=>"298","lv298"=>"299","lv299"=>"300","lv397"=>"398","lv398"=>"399","lv399"=>"400","lv199"=>"200");
	var $ArrView = array("lv001" => "0", "lv002" => "0", "lv003" => "0", "lv004" => "0", "lv005" => "3", "lv06" => "0", "lv099" => "0", "lv007" => "0", "lv095" => "0", "lv094" => "0", "lv909" => "0", "lv910" => "0", "lv911" => "0", "lv912" => "0", "lv913" => "0", "lv906" => "0", "lv905" => "0", "lv904" => "0");
	var $ArrViewEnter=array("lv199"=>"-1","lv098"=>"-1","lv097"=>"-1","lv296"=>"-1","lv297"=>"-1","lv298"=>"-1","lv299"=>"-1","lv397"=>"-1","lv398"=>"-1","lv399"=>"-1");

	public $LE_CODE = "NjlIUS02VFdULTZIS1QtNlFIQQ==";
	function __construct($vCheckAdmin, $vUserID, $vright, $vSkipAuth = false)
	{
		$this->DateCurrent = GetServerDate() . " " . GetServerTime();
		if (!$vSkipAuth) {
			$this->Set_User($vCheckAdmin, $vUserID, $vright);
		}
		$this->isRel = 1;
		$this->isHelp = 1;
		$this->isConfig = 0;
		$this->isRpt = 0;
		$this->isFil = 1;
		$this->isReset = 1;
		$this->isAddPer = 1;
		$this->isAddMoreRight = 0;
		$this->isDelMoreRight = 0;

		$this->lang = $_GET['lang'];

	}

	function LV_Load()
	{
		$vsql = "select * from  lv_lv0066";
		$vresult = db_query($vsql);
		$vrow = db_fetch_array($vresult);
		if ($vrow) {
			$this->lv001 = $vrow['lv001'];
			$this->lv002 = $vrow['lv002'];
			$this->lv003 = $vrow['lv003'];
			$this->lv004 = $vrow['lv004'];
			$this->lv005 = $vrow['lv005'];
			$this->lv006 = $vrow['lv006'];
			$this->lv007 = $vrow['lv007'];
			$this->lv094 = $vrow['lv094'];
			$this->lv095 = $vrow['lv095'];
			$this->lv099 = $vrow['lv099'];
			$this->lv904 = $vrow['lv904'];
			$this->lv905 = $vrow['lv905'];
			$this->lv906 = $vrow['lv906'];
			$this->lv909 = $vrow['lv909'];
			$this->lv910 = $vrow['lv910'];
			$this->lv911 = $vrow['lv911'];
			$this->lv912 = $vrow['lv912'];
			$this->lv913 = $vrow['lv913'];
			$this->lv666 = $vrow['lv666'];
			$this->lv667 = $vrow['lv667'];
			$this->lv668 = $vrow['lv668'];
			$this->lv669 = $vrow['lv669'];
			$this->lv670 = $vrow['lv670'];
			$this->lv671 = $vrow['lv671'];
			$this->lv672 = $vrow['lv672'];
			$this->lv673 = $vrow['lv673'];
			$this->lv674 = $vrow['lv674'];
			$this->lv675 = $vrow['lv675'];
			$this->lv097 = $vrow['lv097'];
			$this->lv098 = $vrow['lv098'];
			$this->lv296 = $vrow['lv296'];
			$this->lv297 = $vrow['lv297'];
			$this->lv298 = $vrow['lv298'];
			$this->lv299 = $vrow['lv299'];
			$this->lv397 = $vrow['lv397'];
			$this->lv398 = $vrow['lv398'];
			$this->lv399 = $vrow['lv399'];
		}
	}
	function LV_LoadID($vlv001)
	{
		$lvsql = "select * from  lv_lv0066 Where lv001='$vlv001'";
		$vresult = db_query($lvsql);
		$vrow = db_fetch_array($vresult);
		if ($vrow) {
			$this->lv001 = $vrow['lv001'];
			$this->lv002 = $vrow['lv002'];
			$this->lv003 = $vrow['lv003'];
			$this->lv004 = $vrow['lv004'];
			$this->lv005 = $vrow['lv005'];
			$this->lv006 = $vrow['lv006'];
			$this->lv007 = $vrow['lv007'];
			$this->lv094 = $vrow['lv094'];
			$this->lv095 = $vrow['lv095'];
			$this->lv099 = $vrow['lv099'];
			$this->lv904 = $vrow['lv904'];
			$this->lv905 = $vrow['lv905'];
			$this->lv906 = $vrow['lv906'];
			$this->lv909 = $vrow['lv909'];
			$this->lv910 = $vrow['lv910'];
			$this->lv911 = $vrow['lv911'];
			$this->lv912 = $vrow['lv912'];
			$this->lv913 = $vrow['lv913'];
			$this->lv666 = $vrow['lv666'];
			$this->lv667 = $vrow['lv667'];
			$this->lv668 = $vrow['lv668'];
			$this->lv669 = $vrow['lv669'];
			$this->lv670 = $vrow['lv670'];
			$this->lv671 = $vrow['lv671'];
			$this->lv672 = $vrow['lv672'];
			$this->lv673 = $vrow['lv673'];
			$this->lv674 = $vrow['lv674'];
			$this->lv675 = $vrow['lv675'];
			$this->lv097 = $vrow['lv097'];
			$this->lv098 = $vrow['lv098'];
			$this->lv296 = $vrow['lv296'];
			$this->lv297 = $vrow['lv297'];
			$this->lv298 = $vrow['lv298'];
			$this->lv299 = $vrow['lv299'];
			$this->lv397 = $vrow['lv397'];
			$this->lv398 = $vrow['lv398'];
			$this->lv399 = $vrow['lv399'];

		}
	}
	function Load($vlv001)
	{
		$vsql = "SELECT * FROM lv_lv0066 WHERE lv001='$vlv001' ;";
		$vresult = db_query($vsql);
		$vrow = db_fetch_array($vresult);
		if ($vrow) {
			$this->lv001 = $vrow['lv001'];
			$this->lv002 = $vrow['lv002'];
			$this->lv003 = $vrow['lv003'];
			$this->lv004 = $vrow['lv004'];
			$this->lv005 = $vrow['lv005'];
			$this->lv006 = $vrow['lv006'];
			$this->lv007 = $vrow['lv007'];
			$this->lv094 = $vrow['lv094'];
			$this->lv095 = $vrow['lv095'];
			$this->lv099 = $vrow['lv099'];
			$this->lv904 = $vrow['lv904'];
			$this->lv905 = $vrow['lv905'];
			$this->lv906 = $vrow['lv906'];
			$this->lv909 = $vrow['lv909'];
			$this->lv910 = $vrow['lv910'];
			$this->lv911 = $vrow['lv911'];
			$this->lv912 = $vrow['lv912'];
			$this->lv913 = $vrow['lv913'];
			$this->lv666 = $vrow['lv666'];
			$this->lv667 = $vrow['lv667'];
			$this->lv668 = $vrow['lv668'];
			$this->lv669 = $vrow['lv669'];
			$this->lv670 = $vrow['lv670'];
			$this->lv671 = $vrow['lv671'];
			$this->lv672 = $vrow['lv672'];
			$this->lv673 = $vrow['lv673'];
			$this->lv674 = $vrow['lv674'];
			$this->lv675 = $vrow['lv675'];
			$this->lv097 = $vrow['lv097'];
			$this->lv098 = $vrow['lv098'];
			$this->lv296 = $vrow['lv296'];
			$this->lv297 = $vrow['lv297'];
			$this->lv298 = $vrow['lv298'];
			$this->lv299 = $vrow['lv299'];
			$this->lv397 = $vrow['lv397'];
			$this->lv398 = $vrow['lv398'];
			$this->lv399 = $vrow['lv399'];
		} else {
			$this->lv001 = null;
			$this->lv002 = null;
			$this->lv003 = null;
			$this->lv004 = null;
			$this->lv005 = null;
			$this->lv006 = null;
		}
	}
	function LV_SendCreateDemo($vUserID)
	{
		echo "Starting LV_SendCreateDemo for $vUserID <br>";
		$vsql = "SELECT * FROM lv_lv0066 WHERE lv001 in ('$vUserID')";
		echo "Executing SQL: $vsql <br>";
		$vresult = db_query($vsql);
		while($vrow = db_fetch_array($vresult))
		{
			echo "Found user data, preparing to send... <br>";
			$this->lv001 = $vrow['lv001'];
			$this->lv002 = $vrow['lv002'];
			$this->lv003 = $vrow['lv003'];
			$this->lv004 = $vrow['lv004'];
			$this->lv005 = $vrow['lv005'];
			$this->lv006 = $vrow['lv006'];
			$this->lv007 = $vrow['lv007'];
			$this->lv094 = $vrow['lv094'];
			$this->lv095 = $vrow['lv095'];
			$this->lv099 = $vrow['lv099'];
			$this->lv100 = $vrow['lv100'];
			$this->lv904 = $vrow['lv904'];
			$this->lv905 = $vrow['lv905'];
			$this->lv906 = $vrow['lv906'];
			$this->lv909 = $vrow['lv909'];
			$this->lv910 = $vrow['lv910'];
			$this->lv911 = $vrow['lv911'];
			$this->lv912 = $vrow['lv912'];
			$this->lv913 = $vrow['lv913'];
			$this->lv665 = $vrow['lv665'];
			$this->lv666 = $vrow['lv666'];
			$this->lv667 = $vrow['lv667'];
			$this->lv668 = $vrow['lv668'];
			$this->lv669 = $vrow['lv669'];
			$this->lv670 = $vrow['lv670'];
			$this->lv671 = $vrow['lv671'];
			$this->lv672 = $vrow['lv672'];
			$this->lv673 = $vrow['lv673'];
			$this->lv674 = $vrow['lv674'];
			$this->lv675 = $vrow['lv675'];

			$this->lv676 = $vrow['lv676'];

			$this->lv097 = $vrow['lv097'];
			$this->lv098 = $vrow['lv098'];
			$this->lv296 = $vrow['lv296'];
			$this->lv297 = $vrow['lv297'];
			$this->lv298 = $vrow['lv298'];
			$this->lv299 = $vrow['lv299'];
			$this->lv397 = $vrow['lv397'];
			$this->lv398 = $vrow['lv398'];
			$this->lv399 = $vrow['lv399'];
		
		// Lấy thông tin email và số điện thoại từ hr_lv0020
		$sqlHR = "SELECT lv038, lv039, lv040, lv041 FROM lv_lv0066 WHERE lv001='$this->lv001'";
		$resultHR = db_query($sqlHR);
		$rowHR = db_fetch_array($resultHR);
		
		$soDienThoaiChinh = $rowHR ? $rowHR['lv038'] : '';
		$soDienThoaiPhu = $rowHR ? $rowHR['lv039'] : '';
		$emailChinh = $rowHR ? $rowHR['lv040'] : '';
		$emailPhu = $rowHR ? $rowHR['lv041'] : '';
		
		// Lấy thông tin từ lv_lv0008 dựa vào lv003 (role)
		$arrRoles = array();
		if (!empty($this->lv003)) {
			$sqlLv0008 = "SELECT lv001, lv003, lv004 FROM lv_lv0008 WHERE lv002='$this->lv003'";
			$resultLv0008 = db_query($sqlLv0008);
			while ($rowLv0008 = db_fetch_array($resultLv0008)) {
				$roleData = array(
					'lv001' => $rowLv0008['lv001'],
					'lv003' => $rowLv0008['lv003'],
					'lv004' => $rowLv0008['lv004'],
					'permissions' => array()
				);
				
				// Lấy thông tin từ lv_lv0009 dựa vào lv001 của lv_lv0008
				$sqlLv0009 = "SELECT lv002, lv004 FROM lv_lv0009 WHERE lv003='".$rowLv0008['lv001']."'";
				$resultLv0009 = db_query($sqlLv0009);
				while ($rowLv0009 = db_fetch_array($resultLv0009)) {
					$roleData['permissions'][] = array(
						'lv002' => $rowLv0009['lv002'],
						'lv004' => $rowLv0009['lv004']
					);
				}
				
				$arrRoles[] = $roleData;
			}
		}
		$postData = array(
                "UserID" => $this->lv001,
                "NhomQuanLy" => $this->lv002,
                "Quyen" => $this->lv003,
                "TenNguoiDung" => $this->lv004,
                "Password" => $this->lv005,
                "Domain" => $this->lv668,
                "DomainApp" => $this->lv665,
                "Method" => $this->lv669,
                "DBMySql" => $this->lv670,
                "DocDBMySql" => $this->lv671,
                "DeActive" => (int)$this->lv007, // Ép kiểu số nguyên
                "SoDienThoaiChinh" => $soDienThoaiChinh,
                "SoDienThoaiPhu" => $soDienThoaiPhu,
                "EmailChinh" => $emailChinh,
                "EmailPhu" => $emailPhu,
                "IPv4Server" => $this->lv094,
                "MySQLUser" => $this->lv095,
                "MySQLPassword" => $this->lv099,
                "MySQLPort" => $this->lv100,
                "Roles" => $arrRoles, // Thêm thông tin Roles
                "MaBanHang" => $this->lv676
            );
		$jsonData = json_encode($postData, JSON_UNESCAPED_UNICODE);
		// echo json_encode($postData, JSON_UNESCAPED_UNICODE);
		// die();
		$curl = curl_init();
		curl_setopt_array($curl, array(
		CURLOPT_URL => 'http://192.168.1.20/erpdung-hao/services/createdemo/index.php',
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => '',
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 0,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => 'POST',
		CURLOPT_POSTFIELDS => $jsonData,
		CURLOPT_HTTPHEADER => array(
			'SOF-User-Token: SOF2025DEVELOPER',
			'SOF-Token: SOF2025ADMIN',
			'SOF-User: admin',
			'Content-Type: application/json'
		),
		));
			$response = curl_exec($curl);

			curl_close($curl);
			echo $response;
		}
		
	}

	// Hàm tự động tạo tài khoản từ phiếu mua hàng
	function LV_AutoCreateAccountFromPurchase($vPurchaseOrderID,$vEmail,$vPhone,$vlink,$vTitle)
	{
		// Query lấy thông tin sản phẩm từ chi tiết phiếu mua hàng
		$sqlPurchase = "SELECT DISTINCT A.lv001, A.lv003, B.lv002, 
						COALESCE(C.lv002, D.lv011) as ProductName, E.lv036 as MaBanHang, C.lv100 as LinkSP,
						D.lv003 as maSP
						FROM sl_lv0013 A 
						LEFT JOIN cr_lv0276 B ON A.lv001 = B.lv002 
						LEFT JOIN sl_lv0007 C ON B.lv003 = C.lv001 
						LEFT JOIN sl_lv0006 E ON E.lv001 = C.lv003 
						LEFT JOIN sl_lv0014 D ON A.lv001 = D.lv002 
						WHERE A.lv001 = '$vPurchaseOrderID'";
		$resultPurchase = db_query($sqlPurchase);
		
		// Mảng lưu các prefix đã được tạo để tránh tạo trùng
		$createdPrefixes = array();
		
		while ($rowPurchase = db_fetch_array($resultPurchase)) {
			$productName = strtolower($rowPurchase['ProductName']);
			$maSP = strtolower($rowPurchase['maSP']);
			$prefix = '';
			$packageType = '';
			$groupmanager =''; // basic, full, pro
			
			// Xác định prefix dựa vào tên sản phẩm
			if (strpos($productName, 'cà phê') !== false || strpos($productName, 'cafe') !== false || strpos($productName, 'coffee') !== false|| strpos($maSP, 'cafe') !== false) {
				$prefix = 'cafe';
				$groupmanager = 'CAFE';
				$domaindes = 'v2.des.cafe.banhangonline.top';
				$domainapp='app.cafe.banhangonline.top';
			} elseif (strpos($productName, 'khách sạn') !== false || strpos($productName, 'hotel') !== false || strpos($maSP, 'khachs') !== false) {
				$prefix = 'khachsan';
				$groupmanager = 'KHACHSAN';
				$domaindes = 'des.khachsan.banhangonline.top';
				$domainapp='app.khachsan.banhangonline.top';
			} elseif (strpos($productName, 'nhà hàng') !== false || strpos($productName, 'restaurant') !== false || strpos($maSP, 'nhah') !== false) {
				$prefix = 'nhahang';
				$groupmanager = 'NHAHANG';
				$domaindes = 'des.khachsan.banhangonline.top';
				$domainapp='app.khachsan.banhangonline.top';
			} elseif (strpos($productName, 'quán ăn') !== false || strpos($productName, 'food shop') !== false || strpos($maSP, 'quan') !== false) {
				$prefix = 'quanan';
				$groupmanager = 'QUANAN';
				$domaindes = 'v2.des.cafe.banhangonline.top';
				$domainapp='app.cafe.banhangonline.top';
			} elseif (strpos($productName, 'bán hàng') !== false || strpos($productName, 'sale shop') !== false || strpos($maSP, 'banh') !== false) {
				$prefix = 'banhang';
				$groupmanager = 'BANHANG';
				$domaindes = 'des.banhang.banhangonline.top';
				$domainapp='app.banhang.banhangonline.top';
			} else {
				continue;
			}
			
			// Xác định gói (basic, full, pro)
			if (strpos($productName, 'basic') !== false|| strpos($maSP, 'bs') !== false) {
				$packageType = 'basic';
			} elseif (strpos($productName, 'full') !== false || strpos($maSP, 'fu') !== false) {
				$packageType = 'full';
			} elseif (strpos($productName, 'pro') !== false || strpos($maSP, 'pr') !== false) {
				$packageType = 'pro';
			}
			
			// Tạo prefix đầy đủ (ví dụ: cafebasic, khachsanpro)
			$fullPrefix = $prefix . $packageType;
			
			// Kiểm tra prefix đã được tạo chưa, nếu rồi thì bỏ qua
			if (in_array($fullPrefix, $createdPrefixes)) {
				continue;
			}
			
			// Lấy số thứ tự tiếp theo
			$sqlMax = "SELECT MAX(CAST(SUBSTRING(lv001, LENGTH('$fullPrefix') + 1) AS UNSIGNED)) as maxNum 
					   FROM lv_lv0066 
					   WHERE lv001 LIKE '$fullPrefix%'";
			$resultMax = db_query($sqlMax);
			$rowMax = db_fetch_array($resultMax);
			$nextNum = ($rowMax && $rowMax['maxNum']) ? $rowMax['maxNum'] + 1 : 1;
			
			// Tạo mã tài khoản mới
			$newUserID = $fullPrefix . $nextNum;
			
			// Tạo tên database tự động
			// Ví dụ: cafe_basic_v1_0 và cafe_basic_document_v1_0
			$dbName = $prefix . '_' . ($packageType ? $packageType . '_' : '') . 'v' . $nextNum . '_0';
			$dbDocumentName = $prefix . '_' . ($packageType ? $packageType . '_' : '') . 'document_v' . $nextNum . '_0';
			
			// Kiểm tra xem tài khoản đã tồn tại chưa
			if (!$this->Exist($newUserID)) {
				// Tạo tài khoản mới
				$this->lv001 = $newUserID;
				$this->lv002 = $groupmanager; // Nhóm Quản lý
				$this->lv003 = 'admin'; // Quyền admin
				$this->lv004 = 'Auto Account - ' . ucfirst($prefix) . ($packageType ? ' ' . ucfirst($packageType) : ''); // Tên người dùng
				$this->lv005 = '123456'; // Mật khẩu mặc định
				$this->lv006 = ''; // Mã nhân viên
				$this->lv038 = $vPhone; // sdt chính
				$this->lv039 = ''; // sđt phụ
				$this->lv040 = $vEmail; // email chính
				$this->lv041 = ''; // email phụ
				$this->lv094 = '192.168.1.20'; // ipv4 server
				$this->lv095 = 'root';
				$this->lv099 = '';
				$this->lv100 = '3306';
				$this->lv906 = '';
				$this->lv905 = '';
				$this->lv904 = '';
				$this->lv667 = $vPurchaseOrderID; // Mã hợp đồng
				$this->lv668 = $domaindes; // domain desk
				$this->lv665 = $domainapp; // domain app
				$this->lv669 = 'http'; // Method
				$this->lv670 = $dbName; // DBMySql - tự động tạo
				$this->lv671 = $dbDocumentName; // DocDBMySql - tự động tạo
				$this->lv672 = '';
				$this->lv673 = '';
				$this->lv674 = '';
				$this->lv675 = '';
				$this->lv676 = $rowPurchase['MaBanHang'];
				$this->lv400 = $rowPurchase['LinkSP']; // Link cài đặt
				// Insert vào database
				$result = $this->LV_Insert();
				
				if ($result) {
					echo "Đã tạo tài khoản: <strong>$newUserID</strong> (Loại: " . ucfirst($prefix) . ($packageType ? ' - Gói ' . ucfirst($packageType) : '') . ")<br>";
					// Đánh dấu prefix này đã được tạo
					$this->LV_SendCreateDemo($newUserID);
					// Gửi mail ngay sau khi tạo
					$this->LV_SendAccountMail($newUserID,$vlink,$vTitle);

					$createdPrefixes[] = $fullPrefix;
				} else {
					echo "Lỗi khi tạo tài khoản: $newUserID<br>";
				}
			} else {
				echo "Tài khoản $newUserID đã tồn tại<br>";
				$createdPrefixes[] = $fullPrefix;
			}
		}
		
		if (count($createdPrefixes) == 0) {
			echo "Không tìm thấy sản phẩm phù hợp (Cà phê, Khách sạn, Nhà hàng, Quán ăn) trong phiếu này!<br>";
		}
	}

	public function GetBuilCheckListDept($vListID, $vID, $vTabIndex, $vTbl, $vFieldView = 'lv002', $vDepID = "")
	{
		$vListID = "," . $vListID . ",";
		$strTbl = "<table  align=\"center\" class=\"lvtable\">
		<input type=\"hidden\" id=$vID name=$vID value=\"@#02\">
		@#01
		</table>
		";
		$lvChk = "<input type=\"checkbox\" id=\"$vID@01\" value=\"@02\" @03 title=\"@04\" tabindex=\"$vTabIndex\">";
		$lvTrH = "<tr class=\"lvlinehtable1\">
			<td width=1%>@#01</td><td>@#02</td>
			
		</tr>
		";
		if ($vDepID == "") {
			$vsql = "select * from  " . $vTbl . " where lv002='SOF' order by lv103 asc";
		} else {
			$vReturn = "'" . str_replace(",", "','", $vDepID) . "'";
			$vsql = "select lv001,lv003 from  hr_lv0002 where (lv001 in ($vReturn))  order by lv003";
		}

		$strGetList = "";
		$strGetScript = "";
		$i = 0;
		$vresult = db_query($vsql);
		$numrows = db_num_rows($vresult);
		while ($vrow = db_fetch_array($vresult)) {

			$strTempChk = str_replace("@01", $i, $lvChk);
			$strTempChk = str_replace("@02", $vrow['lv001'], $strTempChk);
			if (strpos($vListID, "," . $vrow['lv001'] . ",") === FALSE)
				$strTempChk = str_replace("@03", "", $strTempChk);
			else
				$strTempChk = str_replace("@03", "checked=checked", $strTempChk);

			$strTempChk = str_replace("@04", $vrow['lv003'], $strTempChk);

			$strTemp = str_replace("@#01", $strTempChk, $lvTrH);
			if ($this->LV_IsNameDep == 1)
				$strTemp = str_replace("@#02", $vrow[$vFieldView] . "(" . $vrow['lv001'] . ")", $strTemp);
			else
				$strTemp = str_replace("@#02", $vrow[$vFieldView], $strTemp);
			$strGetScript = $strGetScript . $strTemp;
			$strGetScript = $strGetScript . $this->GetBuilCheckListChild($vListID, $vID, $vrow['lv001'], $vTbl, $vFieldView, $i, $numrows, '');
			$i++;

		}
		$strReturn = str_replace("@#01", $strGetScript, str_replace("@#02", $numrows, $strTbl));
		return $strReturn;
	}
	function GetBuilCheckListChild($vListID, $vID, $vParentID, $vTbl, $vFieldView, &$i, &$numrows, $vspace)
	{
		$strGetScript = "";
		$lvChk = "<input type=\"checkbox\" id=\"$vID@01\" value=\"@02\" @03 title=\"@04\" tabindex=\"$vTabIndex\">";
		$lvTrH = "<tr class=\"lvlinehtable1\">
			<td width=1%>@#01</td><td>@#02</td>			
		</tr>
		";
		$vsql1 = "select * from  " . $vTbl . " where lv002='" . $vParentID . "' order by lv003";
		$vresult1 = db_query($vsql1);
		$vnum = db_num_rows($vresult1);
		$numrows = $numrows + $vnum;
		$i++;
		while ($vrow1 = db_fetch_array($vresult1)) {
			$strTempChk = str_replace("@01", $i, $lvChk);
			$strTempChk = str_replace("@02", $vrow1['lv001'], $strTempChk);
			if (strpos($vListID, "," . $vrow1['lv001'] . ",") === FALSE)
				$strTempChk = str_replace("@03", "", $strTempChk);
			else
				$strTempChk = str_replace("@03", "checked=checked", $strTempChk);

			$strTempChk = str_replace("@04", '&nbsp;&nbsp;&nbsp;' . $vrow1['lv003'], $strTempChk);

			$strTemp = str_replace("@#01", $strTempChk, $lvTrH);
			if ($this->LV_IsNameDep == 1)
				$strTemp = str_replace("@#02", $vspace . '|-----' . $vrow1[$vFieldView] . "(" . $vrow1['lv001'] . ")", $strTemp);
			else
				$strTemp = str_replace("@#02", $vspace . '|-----' . $vrow1[$vFieldView], $strTemp);
			$strGetScript = $strGetScript . $strTemp;
			$strGetScript = $strGetScript . $this->GetBuilCheckListChild($vListID, $vID, $vrow1['lv001'], $vTbl, $vFieldView, $i, $numrows, $vspace . '&nbsp;&nbsp;&nbsp;');
			$i++;
		}
		$i--;
		return $strGetScript;
	}          
	function LV_Insert()
	{
			// if ($this->isAdd == 0)
			// 	return false;
		$vsql = "INSERT INTO lv_lv0066(lv001, lv002, lv003, lv004, lv005, lv006,lv038,lv039,lv040,lv041,lv094,lv095,lv099,lv100,lv906,lv905,lv904,lv665,lv667,lv668,lv669,lv670,lv671,lv672,lv673,lv674,lv675,lv676,lv400) VALUES ('$this->lv001', '$this->lv002', '$this->lv003', '$this->lv004', '$this->lv005', '$this->lv006','$this->lv038','$this->lv039','$this->lv040','$this->lv041','$this->lv094','$this->lv095','$this->lv099','$this->lv100','$this->lv906','$this->lv905','$this->lv904','$this->lv665','$this->lv667','$this->lv668','$this->lv669','$this->lv670','$this->lv671','$this->lv672','$this->lv673','$this->lv674','$this->lv675','$this->lv676','$this->lv400') ;";
		// echo $vsql; // Debug: In câu lệnh SQL để kiểm tra
		$result = db_query($vsql);
		if ($result) $this->InsertLogOperation($this->DateCurrent, 'lv_lv0066.insert', sof_escape_string($lvsql));
		return $result;
	}
	function LV_Update()
	{
		if ($this->isEdit == 0)
			return false;
		$vsql = "UPDATE lv_lv0066 SET lv002='$this->lv002', lv003='$this->lv003', lv004='$this->lv004',lv006='$this->lv006',lv094='$this->lv094',lv095='$this->lv095',lv099='$this->lv099',lv906='$this->lv906',lv905='$this->lv905',lv904='$this->lv904',lv670='$this->lv670',lv671='$this->lv671',lv672='$this->lv672',lv673='$this->lv673',lv674='$this->lv674',lv675='$this->lv675' WHERE lv001='$this->lv001' ;";
		$result = db_query($vsql);
		if ($result) $this->InsertLogOperation($this->DateCurrent, 'lv_lv0066.insert', sof_escape_string($lvsql));
		return $result;
	}
	function LV_ProcessAddWarehouse($lvarr, $vGroupID)
	{
		if ($this->isEdit == 0)
			return false;
		if ($vGroupID == '')
			return;
		$lvsql = "select * from lv_lv0066 where lv001 in($lvarr)";
		$vReturn = db_query($lvsql);
		while ($vrow = db_fetch_array($vReturn)) {
			$vsqlc = "INSERT INTO wh_lv0034(lv002, lv003, lv004,lv005) VALUES ('" . $vrow['lv001'] . "', '" . $vGroupID . "', 'VT','$this->LV_UserID')";
			$cResutl = db_query($vsqlc);
		}
	}

	function LV_ProcessAddGroup($lvarr, $vGroupID)
	{
		if ($this->isEdit == 0)
			return false;
		if ($vGroupID == '')
			return;
		$lvsql = "select * from lv_lv0066 where lv001 in($lvarr)";
		$vReturn = db_query($lvsql);
		while ($vrow = db_fetch_array($vReturn)) {
			$this->LV_GroupSecurityUdate($vrow['lv001'], $vGroupID);
		}
	}
	function LV_Delete($strar)
	{
		if ($this->isDel == 0)
			return false;
		$lvsql = "DELETE FROM lv_lv0066 WHERE lv_lv0066.lv001 IN (" . $strar . ") and (select count(*) from lv_lv0008 B where B.lv002=lv_lv0066.lv001)<=0;";
		$vReturn = db_query($lvsql);
		if ($vReturn)
			$this->InsertLogOperation($this->DateCurrent, 'lv_lv0066.delete', sof_escape_string($lvsql));
		return $vReturn;
	}
	function LV_Aproval($lvarr)
	{
		if ($this->isApr == 0)
			return false;
		$lvsql = "Update lv_lv0066 set lv007=1,lv008='',lv005='1'  WHERE lv_lv0066.lv001 IN ($lvarr)  ";
		$vReturn = db_query($lvsql);
		if ($vReturn)
			$this->InsertLogOperation($this->DateCurrent, 'lv_lv0066.approval', sof_escape_string($lvsql));
		return $vReturn;
	}
	function LV_UnAproval($lvarr)
	{
		if ($this->isApr == 0)
			return false;
		$lvsql = "Update lv_lv0066 set lv007=0  WHERE lv_lv0066.lv001 IN ($lvarr)  ";
		$vReturn = db_query($lvsql);
		if ($vReturn)
			$this->InsertLogOperation($this->DateCurrent, 'lv_lv0066.unapproval', sof_escape_string($lvsql));
		return $vReturn;
	}

	function NT_TaoChuKy($maCongTy, $maNhanVien)
	{
		// Mapping sang mã ISO 2 ký tự
		$countryMap = [
			'CH' => 'CN', // Trung quốc - China
			'Germany' => 'DE', // Đức - Germany
			'IT' => 'IT', // Ý - Italy
			'MY' => 'MY', // Mã Lai - Malaysia
			'SN' => 'SG', // Singapore
			'UK' => 'GB', // United Kingdom
			'US' => 'US', // United States
			'VIETNAM' => 'VN', // Việt Nam - VN
		];

		// 1. Lấy thông tin nhân viên
		$lvsql = "SELECT *
				FROM lv_lv0066
				INNER JOIN hr_lv0020
					ON lv_lv0066.lv006 = hr_lv0020.lv001
				WHERE lv_lv0066.lv001 = '" . $maNhanVien . "'";

		$vReturn = db_query($lvsql);

		while ($vrow = db_fetch_array($vReturn)) {
			// Lấy tỉnh/thành phố
			$lvsql = "SELECT *
					FROM hr_lv0023
					INNER JOIN hr_lv0020
						ON hr_lv0020.lv032 = hr_lv0023.lv001
					WHERE hr_lv0020.lv032 = '" . $vrow['lv032'] . "'";

			$vReturn = db_query($lvsql);
			$tenTinh_ThanhPho = db_fetch_array($vReturn)['lv002'];

			// Mapping mã quốc gia
			$countryCode = trim($vrow['lv031']);
			$countryISO = isset($countryMap[$countryCode]) ? $countryMap[$countryCode] : 'VN'; // fallback VN

			// 2. Gửi dữ liệu cho API Python
			$payload = [
				'txtlv001' => $maNhanVien,
				'sign_password' => '1', // Mật khẩu ký số
				'certificate_info' => [
					'country' => $countryISO,
					'state' => $tenTinh_ThanhPho,
					'locality' => $vrow['lv034'],
					'organization' => $maCongTy,
					'name' => $vrow['lv002'],
					'email' => $vrow['lv040'],
					'title' => $vrow['lv034']
				]
			];

			// 3. Gửi POST qua cURL
			$ch = curl_init('http://localhost:5050/auth/register');
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, [
				'Content-Type: application/json'
			]);
			curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

			$response = curl_exec($ch);
			$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			curl_close($ch);

			// 4. Xử lý phản hồi
			if ($httpcode == 200) {
				// echo "<br>Đã nhận chứng chỉ từ API";

				// Giải mã JSON từ API
				$apiData = json_decode($response, true);

				if (isset($apiData['private_key'], $apiData['certificate'], $apiData['sign_password'])) {
					$private_key = addslashes($apiData['private_key']);
					$certificate = addslashes($apiData['certificate']);
					$sign_password = addslashes($apiData['sign_password']);

					// Cập nhật vào bảng lv_lv0066
					$vsqlc = "UPDATE lv_lv0066
                  SET lv601 = '" . $private_key . "',
                      lv603 = '" . $certificate . "',
                      lv604 = '" . $sign_password . "'
                  WHERE lv001 = '" . $maNhanVien . "'";

					$cResult = db_query($vsqlc);

					if ($cResult) {
						echo "<br><font color='green'> Đăng ký thông tin ký số cho '" . $maNhanVien . "' thành công.</font>";
					} else {
						echo "<br><font color='red'> Lỗi lưu vào Cơ sở dữ liệu. </font> ";
					}
				} else {
					echo "<br><font color='red'> Dữ liệu trả về từ API không đủ. </font>";
				}
			} else {
				echo "<br><font color='red'> Lỗi tạo chứng chỉ (" . $httpcode . "): " . $response . '</font>';
			}
		}
	}

	function LV_CreateKySo($lvarr)
	{
		// Lấy mã/tên công ty
		$lvsql = "select * from hr_lv0001";
		$vReturn = db_query($lvsql);
		$maCongTy = db_fetch_array($vReturn)[0];

		$lvsql = "select * from lv_lv0066 where lv001 in($lvarr)";
		$vReturn = db_query($lvsql);
		while ($vrow = db_fetch_array($vReturn)) {
			// Kiểm tra nếu đã có key và cert
			if (($vrow["lv601"] == '' or empty($vrow["lv601"])) and ($vrow["lv603"] == '' or empty($vrow["lv603"]))) {
				$this->NT_TaoChuKy($maCongTy, $vrow['lv001']);
			} else {
				echo '<br> Tài khoản ' . $vrow['lv001'] . ' đã có thông tin ký số';
			}
			echo '<br/>';
		}
	}
	function LV_ResetPwd($lvarr)
	{
		$lvsql = "select * from lv_lv0066 where lv001 in($lvarr)";
		$vReturn = db_query($lvsql);
		while ($vrow = db_fetch_array($vReturn)) {
			$this->LV_ResetOne($vrow['lv001']);
		}
	}
	function LV_ResetOne($lv001)
	{
		$str = "";
		$length = 0;
		for ($i = 0; $i < 6; $i++) {
			// this numbers refer to numbers of the ascii table (small-caps)
			$str .= chr(rand(97, 122));
		}
		// $sql = "select A.lv001,B.lv040 Email1,B.lv041 Email2,md5('$str') passcode from lv_lv0066 A inner join hr_lv0020 B on A.lv006=B.lv001 where A.lv001='$lv001'";
		$sql = "SELECT lv001, lv004, lv668, lv040 as Email1, lv041 as Email2, lv004 as EmployeeName 
				FROM lv_lv0066 
				WHERE lv001='$lv001'";
		$vReturn = db_query($sql);
		$vrow = db_fetch_array($vReturn);
		$lvsql = "update lv_lv0066 set lv005=md5('$str') where lv001='$lv001'";
		$vReturn = db_query($lvsql);
		$lvcontent = "Kính gửi Chủ Quản,<br/>
			Đầy là tài khoản để duyệt phép hoặc sắp ca .v.v.<br/>
			Link đăng nhập: <a href=\"" . $this->linklocal . "\">" . $this->linklocal . "</a><br/>
			User:$lv001 <br/>
			Pass:$str <br/>
			Trân trọng.
			--------------Noreply HR---------------<br/>
			Xin vui lòng không gửi lại email này<br/>
		";
		$lvtitle = "Gửi tài khoản cho chủ quản duyệt phép hoặc sắp ca .v.v.";
		$lvemail = "newsletter@sof.vn";
		$vTo = "";
		if (trim($vrow['Email1'] . '') != "" && trim($vrow['Email2'] . '') != "") {
			$vTo = $vrow['Email1'] . ";" . $vrow['Email2'];
			//break;
		} elseif (trim($vrow['Email1'] . '') != "") {
			$vTo = $vrow['Email1'];
		} elseif (trim($vrow['Email2'] . '') != "") {
			$vTo = $vrow['Email2'];
		}

		if ($vTo != '') {
			//echo $lvcontent;
			$lvuser = $_SESSION['ERPSOFV2RUserID'];
			$this->LV_SendMail($lvcontent, $lvtitle, $lvuser, $lvemail, $vTo);

			return $vReturn;
		} else {
			echo $lvcontent;
		}
	}

	// Hàm mới: Gửi mail thông tin tài khoản và mật khẩu
	function LV_SendAccountMail($lv001,$vlink,$vTitle)
	{
		
		// Lấy thông tin tài khoản và email từ lv_lv0066
		$sql = "SELECT lv001, lv004, lv005,lv400,lv667, lv668, lv040 as Email1, lv041 as Email2, lv004 as EmployeeName 
				FROM lv_lv0066 
				WHERE lv001='$lv001'";
		// Tạm thời comment - lấy email từ hr_lv0020
		// $sql = "SELECT A.lv001, A.lv004, A.lv668, B.lv040 Email1, B.lv041 Email2, B.lv002 as EmployeeName 
		// 		FROM lv_lv0066 A 
		// 		INNER JOIN hr_lv0020 B ON A.lv006=B.lv001 
		// 		WHERE A.lv001='$lv001'";
		$vReturn = db_query($sql);
		$vrow = db_fetch_array($vReturn);
		
		
		// Tạo nội dung email
		$lvcontent = "Xin chào " . $vrow['EmployeeName'] . ",<br/><br/>
			Đây là thông tin tài khoản hệ thống của bạn:<br/><br/>
			<strong>Link ứng dụng:</strong> <a href=\"" . $vlink . "\">" . $vlink . "</a><br/>
			<strong>Tên đăng nhập:</strong> " . $lv001 . "<br/>
			<strong>Mật khẩu mới:</strong> " . $vrow['lv005'] . "<br/>
			<strong>Tên người dùng:</strong> " . $vrow['lv004'] . "<br/>
			<strong>Link cài đặt:</strong> <a href=\"" . $vrow['lv400'] . "\">" . $vrow['lv400'] . "</a><br/>
			Vui lòng đăng nhập và đổi mật khẩu sau lần đăng nhập đầu tiên.<br/><br/>
			Trân trọng,<br/>
			--------------Hệ thống ERP---------------<br/>
			<em>Xin vui lòng không trả lời email này</em><br/>
		";
		
		$lvtitle = $vTitle ?? "Thông tin tài khoản hệ thống ERP";
		$lvemail = "noreply@sof.vn";
		$vTo = "";
		
		// Xác định email người nhận
		if (trim($vrow['Email1'] . '') != "" && trim($vrow['Email2'] . '') != "") {
			$vTo = $vrow['Email1'] . ";" . $vrow['Email2'];
		} elseif (trim($vrow['Email1'] . '') != "") {
			$vTo = $vrow['Email1'];
		} elseif (trim($vrow['Email2'] . '') != "") {
			$vTo = $vrow['Email2'];
		}

		// Gửi email
		if ($vTo != '') {
			$lvuser = $_SESSION['ERPSOFV2RUserID'] ?? 'admin';
			$this->LV_SendMail($lvcontent, $lvtitle, $lvuser, $lvemail, $vTo, null, $vrow['lv667']);
			return $vReturn;
		} else {
			echo $lvcontent;
			echo "<br/><strong style='color:red;'>Lỗi: Không tìm thấy email người nhận!</strong>";
		}
	}

	function LV_SendMail($lvcontent,$lvtitle,$lvuser,$lvemail,$vTo, $vLogID = null,$vOderId=null)
	{
		// // Include các class cần thiết cho gửi mail
		// if (!class_exists('ml_lv0008')) {
		// 	include_once(__DIR__ . '/ml_lv0008.php');
		// }
		// if (!class_exists('ml_lv0100')) {
		// 	include_once(__DIR__ . '/ml_lv0100.php');
		// }
		// if (!class_exists('ml_lv0009')) {
		// 	include_once(__DIR__ . '/ml_lv0009.php');
		// }
		
		$lvListId_del="";
		$sendSuccess = false;
		$lvml_lv0008=new ml_lv0008($_SESSION['ERPSOFV2RRight'],$_SESSION['ERPSOFV2RUserID'],'Ml0008', true);
		$lvml_lv0100=new ml_lv0100($_SESSION['ERPSOFV2RRight'],$_SESSION['ERPSOFV2RUserID'],'Ml0100');		
		$lvml_lv0009=new ml_lv0009($_SESSION['ERPSOFV2RRight'],$_SESSION['ERPSOFV2RUserID'],'Ml0009', true);
		$lvml_lv0009->LV_LoadSMTP();
		$lvml_lv0008->LV_LoadUser($lvuser,$lvemail);
			$this->Domain=$lvml_lv0009->lv010;
			$vstrTo=SplitTo(str_replace(";",",",str_replace(" ","",$vTo)),"<",">",",");
			$vstrToSend=$this->SplitToEsc($vstrTo,",",0);
			$lvml_lv0100=new ml_lv0100($_SESSION['ERPSOFV2RRight'],$_SESSION['ERPSOFV2RUserID'],'Ml0100');
			$lvml_lv0100->To(explode(",",$vstrToSend));		
			if($lvml_lv0008->lv005==1)
			{
				$lvml_lv0100->lvml_lv0009=$lvml_lv0009;
				$lvml_lv0100->lvml_lv0008=$lvml_lv0008;
				$lvml_lv0100->To(explode(",",$vstrToSend));
				$lvml_lv0100->From($lvemail);
				$lvml_lv0100->Mail->CharSet = "UTF-8";

				$lvml_lv0100->Subject("=?UTF-8?B?" . base64_encode($lvtitle) . "?=");
				$lvml_lv0100->Priority(3);	
				$lvml_lv0100->Content_type("multipart/related");
				$lvml_lv0100->charset="utf-8";
				$lvml_lv0100->ctencoding="quoted-printable";
				$lvml_lv0100->Cc(explode(",",$vstrCCSend));
				$lvml_lv0100->Bcc(explode(",",$vstrBCCSend));
				$lvml_lv0100->Body($lvcontent,'');
				$lvml_lv0100->Content_type('text/html');
				if($lvml_lv0100->Send())
				{
					$sendSuccess = true;
					echo 'Thành công gửi! Email:'.$vTo."<br/>";
				}
				else
					$sendSuccess = false;
					echo 'Không thành công gửi! Email:'.$vTo."<br/>";

			}
			else	
				echo 'Không thành công gửi! Email:'.$vTo."<br/>";
			try {
					// Lấy mã phiếu bán hàng nếu có trong đối tượng
					$lv002_log = $vOderId;
					$lv003_log = isset($vstrToSend) ? $vstrToSend : $vTo;
					$lv004_log = $lvtitle;
					$lv005_log = $lvcontent;
					$lv006_log = $sendSuccess ? 1 : 0;

					$lv002Esc = sof_escape_string($lv002_log);
					$lv003Esc = sof_escape_string($lv003_log);
					$lv004Esc = sof_escape_string($lv004_log);
					$lv005Esc = sof_escape_string($lv005_log);
					$lv006Esc = (int)$lv006_log;
					if($vLogID != null) {
						$updateSql = "UPDATE sl_lv0512 SET lv006 = {$lv006Esc}, lv005 = '{$lv005Esc}' WHERE lv001 = '{$vLogID}'";
						db_query($updateSql);
						if (method_exists($this, 'InsertLogOperation')) $this->InsertLogOperation($this->DateCurrent, 'sl_lv0512.update', sof_escape_string($updateSql));
					} else {
						$insertSql = "INSERT INTO sl_lv0512 (lv002, lv003, lv004, lv005, lv006) VALUES ('{$lv002Esc}','{$lv003Esc}','{$lv004Esc}','{$lv005Esc}', {$lv006Esc})";
						db_query($insertSql);
						if (method_exists($this, 'InsertLogOperation')) $this->InsertLogOperation($this->DateCurrent, 'sl_lv0512.insert', sof_escape_string($insertSql));
					}
				} catch (Exception $e) {
					// ignore logging errors
				}
		$vReturn = array('success' => $sendSuccess);
		return $vReturn;
	}
	function SplitToEsc($vAddress,$vPara1,$vopt)
	{
		$strTemp=$vAddress;
		$vArrTemp=explode($vPara1,$strTemp);
		$strReturn="";
		if(count($vArrTemp)==0) return $vAddress;
		for($i=0;$i<count($vArrTemp);$i++)
		{
			if($vopt==1)
			{
				if (!(strpos($vArrTemp[$i],"@11111111".$this->Domain)===false))
				{
					if($strReturn!="")
						$strReturn=$strReturn.$vPara1.trim($vArrTemp[$i]);
					else
						$strReturn=$strReturn.trim($vArrTemp[$i]);			
				}		
			}
			else
			{
				if ((strpos($vArrTemp[$i],"@11111".$this->Domain)===false))
				{
					if($strReturn!="")
						$strReturn=$strReturn.$vPara1.trim($vArrTemp[$i]);
					else
						$strReturn=$strReturn.trim($vArrTemp[$i]);			
				}		
			
			}
		}
		return $strReturn;
	}
	//Kiem tra ton tai
	function Exist($vlv001)
	{
		$vsql = "SELECT lv001 FROM lv_lv0066 WHERE lv001='" . $vlv001 . "'";
		$vresult = db_query($vsql);
		$this->isExist = db_num_rows($vresult);
		return $this->isExist;
	}
	function Getlv004($lv001)
	{
		$vsql = "select  lv004 from lv_lv0066 where lv001='$lv001'";
		$tresult = db_query($vsql);
		$trow = db_fetch_array($tresult);
		if ($trow) {
			return $trow['lv004'];
		}
		return "";
	}
	function LV_GroupDeleteRight($vUserID)
	{
		$vsql = "delete from lv_lv0008   where lv002='$vUserID'";
		$tresult = db_query($vsql);
	}
	function LV_CheckExistRight($vUserID, $vRight)
	{
		$vsql = "select count(*) nums from lv_lv0008   where lv002='$vUserID' and lv003='$vRight'";
		$tresult = db_query($vsql);
		$trow = db_fetch_array($tresult);
		return $trow['nums'];
	}
	function LV_GroupSecurityUdate($vUserID, $vGroupID)
	{
		$vsql = "select * from lv_lv0008 A  where A.lv002='$vGroupID'";
		$tresult = db_query($vsql);
		while ($trow = db_fetch_array($tresult)) {
			$vCount = $this->LV_CheckExistRight($vUserID, $trow['lv003']);
			if ($vCount == 0) {
				$vlv001 = InsertWithCheckExt('lv_lv0008', 'lv001', '', 1);
				$vsqlc = "INSERT INTO lv_lv0008(lv002, lv003, lv004) VALUES ('$vUserID', '" . $trow['lv003'] . "', '" . $trow['lv004'] . "')";
				$cResutl = db_query($vsqlc);
				if ($cResutl) {
					$vlv001 = sof_insert_id();
					$vsqlcc = "INSERT INTO lv_lv0009(lv002, lv003, lv004) SELECT lv002,'$vlv001', lv004 from lv_lv0009 where lv003='" . $trow['lv001'] . "'";
					$cResutl = db_query($vsqlcc);
				}
			}
		}

	}
	function GetEmployee($plang, $lv001, $vState)
	{
		$vsql = "select  A.lv004,A.lv006,B.lv002 FirstName,B.lv003 MiddleName,B.lv004 LastName from lv_lv0066 A left join hr_lv0020 B on A.lv006=B.lv001 where A.lv001='$lv001'";
		$tresult = db_query($vsql);
		$trow = db_fetch_array($tresult);
		if ($trow) {
			if ($trow['lv006'] == NULL || $trow['lv006'] == "") {
				return $trow['lv004'] . (($vState == 1) ? "" : (" (" . $lv001 . ")"));
			} else {
				if (strtoupper($plang) == "EN") {
					return $trow['MiddleName'] . " " . $trow['FirstName'] . " " . $trow['LastName'] . (($vState == 1) ? "" : (" (" . $trow['lv006'] . ")"));
				} else {
					return $trow['LastName'] . $trow['MiddleName'] . " " . $trow['FirstName'] . " " . (($vState == 1) ? "" : (" (" . $trow['lv006'] . ")"));
				}

			}

		}
		return "";
	}
	public function GetBuilCheckList($vListID, $vID, $vTabIndex, $vTbl, $vFieldView = 'lv002')
	{
		$vListID = "," . $vListID . ",";
		$strTbl = "<table  align=\"center\" class=\"lvtable\">
		<input type=\"hidden\" id=$vID name=$vID value=\"@#02\">
		@#01
		</table>
		";
		$lvChk = "<input type=\"checkbox\" id=\"$vID@01\" value=\"@02\" @03 title=\"@04\" tabindex=\"$vTabIndex\">";
		$lvTrH = "<tr class=\"lvlinehtable1\">
			<td width=1%>@#01</td><td>@#02</td>
			
		</tr>
		";
		$vsql = "select * from  " . $vTbl . " where lv002='SOF' order by lv103";
		$strGetList = "";
		$strGetScript = "";
		$i = 0;
		$vresult = db_query($vsql);
		$numrows = db_num_rows($vresult);
		while ($vrow = db_fetch_array($vresult)) {

			$strTempChk = str_replace("@01", $i, $lvChk);
			$strTempChk = str_replace("@02", $vrow['lv001'], $strTempChk);
			if (strpos($vListID, "," . $vrow['lv001'] . ",") === FALSE)
				$strTempChk = str_replace("@03", "", $strTempChk);
			else
				$strTempChk = str_replace("@03", "checked=checked", $strTempChk);

			$strTempChk = str_replace("@04", $vrow['lv003'], $strTempChk);

			$strTemp = str_replace("@#01", $strTempChk, $lvTrH);
			$strTemp = str_replace("@#02", $vrow[$vFieldView] . "(" . $vrow['lv001'] . ")", $strTemp);
			$strGetScript = $strGetScript . $strTemp;
			$strGetScript = $strGetScript . $this->GetBuilCheckListChild1($vListID, $vID, $vrow['lv001'], $vTbl, $vFieldView, $i, $numrows, '');
			$i++;

		}
		$strReturn = str_replace("@#01", $strGetScript, str_replace("@#02", $numrows, $strTbl));
		return $strReturn;
	}
	function GetBuilCheckListChild1($vListID, $vID, $vParentID, $vTbl, $vFieldView, &$i, &$numrows, $vspace)
	{
		$vTabIndex = $vTabIndex ?? 0;
		$strGetScript = "";
		$lvChk = "<input type=\"checkbox\" id=\"$vID@01\" value=\"@02\" @03 title=\"@04\" tabindex=\"$vTabIndex\">";
		$lvTrH = "<tr class=\"lvlinehtable1\">
			<td width=1%>@#01</td><td>@#02</td>			
		</tr>
		";
		$vsql1 = "select * from  " . $vTbl . " where lv002='" . $vParentID . "' order by lv003";
		$vresult1 = db_query($vsql1);
		$vnum = db_num_rows($vresult1);
		$numrows = $numrows + $vnum;
		$i++;
		while ($vrow1 = db_fetch_array($vresult1)) {
			$strTempChk = str_replace("@01", $i, $lvChk);
			$strTempChk = str_replace("@02", $vrow1['lv001'], $strTempChk);
			if (strpos($vListID, "," . $vrow1['lv001'] . ",") === FALSE)
				$strTempChk = str_replace("@03", "", $strTempChk);
			else
				$strTempChk = str_replace("@03", "checked=checked", $strTempChk);

			$strTempChk = str_replace("@04", '&nbsp;&nbsp;&nbsp;' . $vrow1['lv003'], $strTempChk);

			$strTemp = str_replace("@#01", $strTempChk, $lvTrH);
			$strTemp = str_replace("@#02", $vspace . '|-----' . $vrow1[$vFieldView] . "(" . $vrow1['lv001'] . ")", $strTemp);
			$strGetScript = $strGetScript . $strTemp;
			$strGetScript = $strGetScript . $this->GetBuilCheckListChild1($vListID, $vID, $vrow1['lv001'], $vTbl, $vFieldView, $i, $numrows, $vspace . '&nbsp;&nbsp;&nbsp;');
			$i++;
		}
		$i--;
		return $strGetScript;
	}
	//////////get view///////////////
	function GetView()
	{
		return $this->isView;
	}//////////get view///////////////
	function GetRpt()
	{
		return $this->isRpt;
	}
	//////////get view///////////////
	function GetAdd()
	{
		return $this->isAdd;
	}
	//////////get edit///////////////
	function GetEdit()
	{
		return $this->isEdit;
	}
	//////////get edit///////////////
	function GetApr()
	{
		return $this->isApr;
	}
	//////////get edit///////////////
	function GetUnApr()
	{
		return $this->isUnApr;
	}
	//////////Get Filter///////////////
	protected function GetCondition()
	{
		$strCondi = " and lv667='$this->lv667' ";
		if ($this->lv001 != "")
			$strCondi = $strCondi . " and lv001 like '%$this->lv001%'";
		if ($this->lv002 != "")
			$strCondi = $strCondi . " and lv002 like '%$this->lv002%'";
		if ($this->lv003 != "")
			$strCondi = $strCondi . " and lv003 like '%$this->lv003%'";
		if ($this->lv004 != "")
			$strCondi = $strCondi . " and lv004 like '%$this->lv004%'";
		if ($this->lv005 != "")
			$strCondi = $strCondi . " and lv005 like '%$this->lv005%'";
		if ($this->lv006 != "")
			$strCondi = $strCondi . " and lv006 like '%$this->lv006%'";
		if ($this->lv007 != "")
			$strCondi = $strCondi . " and lv007 = '$this->lv007'";
		if ($this->lv099 != "")
			$strCondi = $strCondi . " and lv099 like '%$this->lv099%'";
		if ($this->lv906 != "")
			$strCondi = $strCondi . " and lv906 like '%$this->lv906%'";
		if ($this->DeptList != "") {
			$strCondi = $strCondi . " and lv006 in (" . $this->GetMultiValue("select lv001 from hr_lv0020 where lv029 in (" . $this->LV_GetDep($this->DeptList) . ")") . ")";
		}
		return $strCondi;
	}
	function GetMultiValue($sqlS)
	{
		$lv_str = "";
		$bResult = db_query($sqlS);
		while ($vrow = db_fetch_array($bResult)) {
			if ($lv_str == "")
				$lv_str = $vrow['lv001'];
			else
				$lv_str = $lv_str . "','" . $vrow['lv001'];
		}
		$lv_str = "'" . $lv_str . "'";
		return $lv_str;
	}
	function LV_GetDep($vDepID)
	{
		if ($vDepID == "")
			return '';
		$vReturn = "'" . str_replace(",", "','", $vDepID) . "'";
		if ($this->isChildCheck == "")
			$this->isChildCheck = 1;
		if ($this->isChildCheck == 1) {
			$vsql = "select lv001 from  hr_lv0002 where lv001 in ($vReturn)  order by lv103 asc";
			$bResult = db_query($vsql);
			while ($vrow = db_fetch_array($bResult)) {
				//$vReturn=$vReturn.",'".$vrow['lv001']."'";
				$vReturn = $vReturn . "," . $this->LV_GetChildDep($vrow['lv001']);
			}
		}
		return $vReturn;
	}
	function LV_GetChildDep($vDepID)
	{
		$vReturn = "";
		if (trim($vDepID) == "")
			return '';
		$vReturn = "'" . str_replace(",", "','", $vDepID) . "'";
		$vsql = "select lv001 from  hr_lv0002 where lv002 in ($vReturn) order by lv103 asc";
		$bResult = db_query($vsql);
		while ($vrow = db_fetch_array($bResult)) {
			$vReturn = $vReturn . "," . $this->LV_GetChildDep($vrow['lv001']);
		}
		return $vReturn;
	}
	////////////////Count///////////////////////////
	function GetCount()
	{
		$sqlC = "SELECT COUNT(*) AS nums FROM lv_lv0066 WHERE 1=1 " . $this->GetCondition();
		$bResultC = db_query($sqlC);
		$arrRowC = db_fetch_array($bResultC);
		return $arrRowC['nums'];
	}
	function LV_UpdateChangeChild($lvsql)
	{
		$vReturn= db_query($lvsql);
		if($vReturn) $this->InsertLogOperation($this->DateCurrent,'lv_lv0066.update',sof_escape_string($lvsql));
		return $vReturn;
	}
	function LV_BuilList($lvList, $lvFrom, $lvChkAll, $lvChk, $curRow, $maxRows, $paging, $lvOrderList, $lvSortNum)
	{
		if ($curRow < 0)
			$curRow = 0;
		if ($lvList == "")
			$lvList = $this->DefaultFieldList;
		if ($this->isView == 0)
			return false;
		$lstArr = explode(",", $lvList);
		$lstOrdArr = explode(",", $lvOrderList);
		$lstArr = $this->getsort($lstArr, $lstOrdArr);
		$strSort = "";
		switch ($lvSortNum) {
			case 0:
				break;
			case 1:
				$strSort = " order by " . $this->LV_SortBuild($this->GB_Sort, "asc");
				break;
			case 2:
				$strSort = " order by " . $this->LV_SortBuild($this->GB_Sort, "desc");
				break;
		}
		$lvTable="
		<div id=\"func_id\" style='position:relative;background:#f2f2f2'><div style=\"float:left\">".$this->TabFunction($lvFrom,$lvList,$maxRows)."</div><div style=\"float:right\">".$this->ListFieldSave($lvFrom,$lvList,$maxRows,$lvOrderList,$lvSortNum)."</div><div style='float:right'>&nbsp;&nbsp;&nbsp;</div><div style='float:right'>".$this->ListFieldExport($lvFrom,$lvList,$maxRows)."</div></div><div style='height:35px'></div><table  align=\"center\" class=\"lvtable\"><!--<tr ><td colspan=\"".(2+count($lstArr))."\" class=\"lvTTable\">".$this->ArrPush[0]."</td></tr>-->
		@#01
		<tr ><td colspan=\"".(count($lstArr)+2)."\">$paging</td></tr>
		<tr class=\"cssbold_tab\"><td colspan=\"".(count($lstArr)+2)."\">".$this->TabFunction($lvFrom,$lvList,$maxRows)."</td></tr>
		</table>
		";
		$lvTrH="<tr class=\"lvhtable\">
			<td width=1% class=\"lvhtable\">".$this->ArrPush[1]."</td>
			<td width=1%><input name=\"$lvChkAll\" type=\"checkbox\" id=\"$lvChkAll\" onclick=\"DoChkAll($lvFrom, '$lvChk', this)\" value=\"$curRow\" tabindex=\"2\"/></td>
			@#01
		</tr>
		";
		$lvTr="<tr class=\"lvlinehtable@01\"><td width=1% onclick=\"Select_Check('$lvChk@03',$lvFrom, '$lvChk', '$lvChkAll')\">@03</td>	<td width=1%><input name=\"$lvChk\" type=\"checkbox\" id=\"$lvChk@03\" onclick=\"CheckOne($lvFrom, '$lvChk', '$lvChkAll', this)\" value=\"@02\" tabindex=\"2\"  onKeyUp=\"return CheckKeyCheck(event,2,'$lvChk',$lvFrom, '$lvChk', '$lvChkAll',@03)\"/></td>@#01</tr>";
		$lvTdH="<td width=\"@01\" class=\"lvhtable\">@02</td>";
		$lvTd="<td align=@#05>@02</td>";
		$sqlS = "SELECT A.*, B.lv027 as OrderStatus FROM lv_lv0066 A LEFT JOIN sl_lv0013 B ON A.lv667 = B.lv001 WHERE 1=1  " . $this->GetCondition() . " $strSort LIMIT $curRow, $maxRows";
		$vorder = $curRow;
		$bResult = db_query($sqlS);
		$this->Count = db_num_rows($bResult);
		$strH = '';
		$strTr = '';
		for($i=0;$i<count($lstArr);$i++)
			{
				$vTemp=str_replace("@01","",$lvTdH);
				$vTemp=str_replace("@02",$this->ArrPush[(int)$this->ArrGet[$lstArr[$i]]],$vTemp);
				$strH=$strH.$vTemp;
				$vField=$lstArr[$i];
				$vStringNumber="";
				//if($this->ArrViewEnter[$vField] = $this->ArrViewEnter[$vField] ?? 0) $this->ArrViewEnter[$vField]=0;
				$vStringNumber="";
				switch($this->ArrView[$vField] ?? 0)
				{
					case '10':
					case '20':
					case '1':
						$vStringNumber=' onfocus="LayLaiGiaTri(this)" onblur="SetGiaTri(this);" ';
						break;
				}
				switch($this->ArrViewEnter[$vField])
				{			
					case 99:
						if($this->isPopupPlus==0) $this->isPopupPlus=1;
						$vstr='<ul style="width:100%" id="pop-nav'.(($this->isPopupPlus==1)?'':$this->isPopupPlus).'" lang="pop-nav'.$this->isPopupPlus.'" onMouseOver="ChangeName(this,'.$this->isPopupPlus.')" onKeyUp="ChangeName(this,'.$this->isPopupPlus.')"> <li class="menupopT">
									<input autocomplete="off" class="txtenterquick" type="text" autocomplete="off" style="width:100%;min-width:30px" name="qxt'.$vField.'" id="qxt'.$vField.'" onKeyUp="LoadPopupParentTabIndex(event,this,\'qxt'.$vField.'\',\''.$this->Tables[$vField].'\',\'concat(lv002,@! @!,lv001)\')"  onKeyPress="return CheckKey(event,7)" tabindex="2" onblur="LoadSource(this.value)" value="'.$this->Values[$vField].'">
									<div id="lv_popup'.(($this->isPopupPlus==1)?'':$this->isPopupPlus).'" lang="lv_popup'.$this->isPopupPlus.'"> </div>						  
									</li>
								</ul>';
						$this->isPopupPlus++;
						$vTempEnter = str_replace("@02", $vstr, $this->Align($lvTd, (int)($this->ArrView[$lstArr[$i]] ?? 0)));
						break;
					case 999:
						if($this->isPopupPlus==0) $this->isPopupPlus=1;
						$vstr='<ul style="width:100%" id="pop-nav'.(($this->isPopupPlus==1)?'':$this->isPopupPlus).'" lang="pop-nav'.$this->isPopupPlus.'" onMouseOver="ChangeName(this,'.$this->isPopupPlus.')" onKeyUp="ChangeName(this,'.$this->isPopupPlus.')"> <li class="menupopT">
									<input autocomplete="off" class="txtenterquick" type="text" autocomplete="off" style="width:100%;min-width:30px" name="qxt'.$vField.'" id="qxt'.$vField.'" onKeyUp="LoadSelfNextParent(this,\'qxt'.$vField.'\',\''.$this->Tables[$vField].'\',\''.$this->TableLinkReturn[$vField].'\',\''.$this->TableLink[$vField].'\')"  onKeyPress="return CheckKey(event,7)" tabindex="2" value="'.$this->Values[$vField].'" onblur="if(this.value.substr(this.value.length-1,this.value.length)==\',\') {this.value=this.value.substr(0,this.value.length-1);};">
									<div id="lv_popup'.(($this->isPopupPlus==1)?'':$this->isPopupPlus).'" lang="lv_popup'.$this->isPopupPlus.'"> </div>						  
									</li>
								</ul>';
						$this->isPopupPlus++;
						$vTempEnter = str_replace("@02", $vstr, $this->Align($lvTd, (int)($this->ArrView[$lstArr[$i]] ?? 0)));
						break;
					case 88:
						$vstr='<select class="selenterquick" name="qxt'.$vField.'" id="qxt'.$vField.'" tabindex="2" style="width:100%;min-width:30px" onKeyPress="return CheckKey(event,7)">'.$this->LV_LinkField($vField,$this->Values[$vField]).'</select>';
						$vTempEnter = str_replace("@02", $vstr, $this->Align($lvTd, (int)($this->ArrView[$lstArr[$i]] ?? 0)));
						break;
					case 89:
							$vstr='<select class="selenterquick" name="qxt'.$vField.'" id="qxt'.$vField.'" tabindex="2" style="width:100%;min-width:30px" onKeyPress="return CheckKey(event,7)">
								<option value="">...</option>
							'.$this->LV_LinkField($vField,$this->Values[$vField]).'</select>';
							$vTempEnter = str_replace("@02", $vstr, $this->Align($lvTd, (int)($this->ArrView[$lstArr[$i]] ?? 0)));
							break;
					case 4:
						$vstr='<table><tr><td><input autocomplete="off" class="txtenterquick"  autocomplete="off" name="qxt'.$vField.'_1" type="text" id="qxt'.$vField.'_1" value="'.$this->Values[$vField].'" tabindex="2" maxlength="32" style="width:100%;min-width:80px" onKeyPress="return CheckKey(event,7)" ondblclick="if(self.gfPop)gfPop.fPopCalendar(this);return false;"></td><td><input class="txtenterquick"  autocomplete="off" name="qxt'.$vField.'_2" type="text" id="qxt'.$vField.'_2" value="'.$this->Values[$vField].'" tabindex="2" maxlength="32" style="width:50%;min-width:60px" onKeyPress="return CheckKey(event,7)" ></td></tr></table>';
						$vTempEnter = str_replace("@02", $vstr, $this->Align($lvTd, (int)($this->ArrView[$lstArr[$i]] ?? 0)));
						break;
					case 22:
					case 2:
						$vstr='<input autocomplete="off" class="txtenterquick"  autocomplete="off" name="qxt'.$vField.'" type="text" id="qxt'.$vField.'" value="'.$this->Values[$vField].'" tabindex="2" maxlength="32" style="width:100%;min-width:60px" onKeyPress="return CheckKey(event,7)" ondblclick="if(self.gfPop)gfPop.fPopCalendar(this);return false;">';
						$vTempEnter = str_replace("@02", $vstr, $this->Align($lvTd, (int)($this->ArrView[$lstArr[$i]] ?? 0)));
						break;
					case 33:
						$vstr='<input autocomplete="off" class="txtenterquick" name="qxt'.$vField.'" type="checkbox" id="qxt'.$vField.'" value="1" '.(($this->Values[$vField]==1)?'checked="true"':'').' tabindex="2" style="width:100%;min-width:30px;text-align:center;" onKeyPress="return CheckKey(event,7)">';
						$vTempEnter = str_replace("@02", $vstr, $this->Align($lvTd, (int)($this->ArrView[$lstArr[$i]] ?? 0)));
						break;
					case 0:
						$vstr = '<input '.$vStringNumber.' autocomplete="off" class="txtenterquick" name="qxt'.$vField.'" type="text" id="qxt'.$vField.'" value="'.htmlspecialchars($this->Values[$vField] ?? '', ENT_QUOTES).'" tabindex="2" style="width:100%;min-width:30px;text-align:center;" onKeyPress="return CheckKey(event,7)">';
						$vTempEnter = str_replace("@02", $vstr, $this->Align($lvTd, (int)($this->ArrView[$lstArr[$i]] ?? 0)));
						break;
					default:
						$vTempEnter="<td>&nbsp;</td>";
						break;
				}
				$strTrEnter=$strTrEnter.$vTempEnter;
				$strTrEnterEmpty=$strTrEnterEmpty."<td>&nbsp;</td>";
			}
		if($this->isAdd==1) 
			$strTrEnter="<tr class='entermobil'><td colspan='2'>".'<img tabindex="2" border="0" title="Add" class="imgButton" onclick="Save()" onmouseout="this.src=\'../images/iconcontrol/btn_add.jpg\';" onmouseover="this.src=\'../images/iconcontrol/btn_add_02.jpg\';" src="../images/iconcontrol/btn_add.jpg" onkeypress="return CheckKey(event,11)">'."</td>".$strTrEnter."</tr>";
		else
			$strTrEnter="";//"<tr class='entermobil'><td colspan='2'>".'&nbsp;'."</td>".$strTrEnterEmpty."</tr>";
			
		//print_r(db_fetch_array($bResult));
		while ($vrow = db_fetch_array($bResult)) {
			$strL = "";
			$vorder++;



			for ($i = 0; $i < count($lstArr); $i++) {


				switch ($lstArr[$i]) {
					case 'lv199':
						$vChucNang = "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\">
					<tr>
					";
						//$vChucNang=$vChucNang.'<td><a href="'.$this->LinkQR.'" target="_blank">QR</a><td>';
						$vChucNang = $vChucNang . '<td><span onclick="ProcessTextHidenMore(this)"><a href="javascript:FunctRunning1(\'' . $vrow['lv001'] . '\')"><img style="cursor:pointer;width:25px;;padding:5px;"  alt="NoImg" src="../images/icon/work_experience.png" align="middle" border="0" name="new" class="lviconimg"></a></span></td>';
						if ($this->GetEdit() == 1) {
							$vChucNang = $vChucNang . '
						<td><img Title="' . (($vrow['lv027'] == 0) ? 'Edit' : 'View') . '" style="cursor:pointer;width:25px;padding:5px;" onclick="Edit(\'' . ($vrow['lv001']) . '\')" alt="NoImg" src="../images/icon/' . (($vrow['lv027'] == 0) ? 'Edt.png' : 'detail.png') . '" align="middle" border="0" name="new" class="lviconimg"></td>
						';
						
						}
					
						$vStr = '	
					';
						$vStr1 = '<td>
							<div style="cursor:pointer;color:blue;" onclick="showDetailHistory(\'chitietid_' . $vrow['lv001'] . '\',\'' . $vrow['lv001'] . '\')">' . '<img style="cursor:pointer;width:25px;;padding:5px;"  alt="NoImg" src="../images/icon/license.png" title="Xem lịch sử duyệt"/>' . '</div>
							<div style="display:none;position:absolute;z-index:999999999999;background:#efefef;" id="chitietid_' . $vrow['lv001'] . '" class="noidung_member">					
								<div class="hd_cafe" style="width:100%">
									<ul class="qlycafe" style="width:100%">
										<li style="padding:10px;"><img onclick="document.getElementById(\'chitietid_' . $vrow['lv001'] . '\').style.display=\'none\';" width="20" src="images/icon/close.png"/></li>
										<li style="padding:10px;"><div style="width:100%;padding-top:2px;">
										<strong>LỊCH SỬ TÀI KHOẢN:' . $vrow['lv014'] . '</strong></div>
										</li>
									</ul>
								</div>
								<div id="chitietlichsu_' . $vrow['lv001'] . '" style="min-width:360px;overflow:hidden;"></div>
								<div width="100%;height:40px;">
									<center>
										<div style="width:160px;border-radius:5px;cursor:pointer;height:30px;padding-top:10px;" onclick="document.getElementById(\'chitietid_' . $vrow['lv001'] . '\').style.display=\'none\';">ĐÓNG LẠI</div>
									</center>
								</div>
							</div>	
						</td>
						';
						$vChucNang = $vChucNang . $vStr1;
						
						if ($this->GetApr() == 1 ) {
							$vChucNang = $vChucNang . '<td><img title="Báo cáo phân tích "  style="cursor:pointer;height:25px;padding:5px;" onclick="ReportBBGPTich(\'' . $vrow['lv001'] . '\')" alt="NoImg" src="../images/icon/AddPer.png" align="middle" border="0" name="new" class="lviconimg"></td>';
						}
						if ($this->GetApr() == 1 && $vrow['OrderStatus'] == 5 || $vrow['OrderStatus'] == 2) {
							$vChucNang = $vChucNang . '<td><input type="button" value="Tạo demo" style="border-radius:3px;font-weight:bold;cursor:pointer;" onclick="CreateDemo(\'' . $vrow['lv001'] . '@\')"/></td>';
						}
						// Thêm nút gửi mail - chỉ hiện khi phiếu bán hàng có lv027 = 3
						if (isset($vrow['OrderStatus']) && $vrow['OrderStatus'] == 2) {
							$vChucNang = $vChucNang . '<td><input type="button" value="Gửi mail" style="border-radius:3px;font-weight:bold;cursor:pointer;background:#2196F3;color:white;" onclick="SendEmailAccount(\'' . $vrow['lv001'] . '\')"/></td>';
						}
						// Thêm nút reset password - chỉ hiện khi phiếu bán hàng có lv027 = 5
						if (isset($vrow['OrderStatus']) && $vrow['OrderStatus'] == 5) {
							$vChucNang = $vChucNang . '<td><input type="button" value="Reset mật khẩu" style="border-radius:3px;font-weight:bold;cursor:pointer;background:#2196F3;color:white;" onclick="ResetPass(\'' . $vrow['lv001'] . '@\')"/></td>';
						}
						if(isset($vrow['OrderStatus']) && $vrow['OrderStatus'] == 2){
						$vChucNang = $vChucNang . '<td><input type="button" value="Nhân bản" style="border-radius:3px;font-weight:bold;cursor:pointer;background:#ccc;color:black;" onclick="Clone(\'' . $vrow['lv001'] . '\')"/></td>';
						}
						
						$vChucNang = $vChucNang . "</tr></table>";
						$vTemp = str_replace("@02", $vChucNang, $this->Align($lvTd, (int)$this->ArrView[$lstArr[$i]]));
						break;
					case 'lv909':
						if ($this->GetEdit() == 1) {
							$lvTdTextBox = "<td align=center><input class='txtenterquick' type=\"checkbox\" value=\"1\" " . (($vrow['lv909'] == 1) ? 'checked="true"' : '') . " @03 onclick=\"UpdateTextCheck(this,'" . $vrow['lv001'] . "',909)\" style=\"width:35px;text-align:center;\" tabindex=\"2\" maxlength=\"32\"   onKeyPress=\"return CheckKey(event,7)\"/></td>";
							$vTemp = str_replace("@02", $this->FormatView($vrow[$lstArr[$i]], 0), $this->Align(str_replace("@01", $vrow['lv001'], $lvTdTextBox), (int) $this->ArrView[$lstArr[$i]]));
						} else
							$vTemp = str_replace("@02", $this->getvaluelink($lstArr[$i], $this->FormatView($vrow[$lstArr[$i]], (int) $this->ArrView[$lstArr[$i]])), $this->Align($lvTd, (int) $this->ArrView[$lstArr[$i]]));
						break;
					case 'lv001':
						$vField=$lstArr[$i];
						$vSTTCot=1;
						$vID=$vrow['lv001'];
						$vStringNumber='ondblclick="this.readOnly=false" onblur="XuLyPostShow(this,\''.$vID.'\','.$vSTTCot.');" readonly="true"';
						$vStyle = 'width:100%;min-width:80px;text-align:center;';
						//$vStringNumber=' onblur="UpdateText(this,\''.$vID.'\','.$vSTTCot.')" ';
						//$vStringNumber=' onfocus="LayLaiGiaTri(this)" onblur="SetGiaTri(this);UpdateText(this,\''.$vID.'\','.$vSTTCot.')" ';
						$vstr='<textarea '.$vStringNumber.'  autocomplete="off" class="txtenterquick" name="qxt'.$vField.'_'.$vID.'" type="text" id="qxt'.$vField.'_'.$vID.'"  title="'.$vrow[$vField].'" tabindex="2" style="'.$vStyle.'">'.$vrow[$vField].'</textarea>';
						$vTemp=str_replace("@02",$vstr,$this->Align($lvTd,(int)$this->ArrView[$lstArr[$i]]));
						break;
					case 'lv910':
						if ($this->GetEdit() == 1) {
							$lvTdTextBox = "<td align=center><input class='txtenterquick' type=\"checkbox\" value=\"1\" " . (($vrow['lv910'] == 1) ? 'checked="true"' : '') . " @03 onclick=\"UpdateTextCheck(this,'" . $vrow['lv001'] . "',910)\" style=\"width:35px;text-align:center;\" tabindex=\"2\" maxlength=\"32\"   onKeyPress=\"return CheckKey(event,7)\"/></td>";
							$vTemp = str_replace("@02", $this->FormatView($vrow[$lstArr[$i]], 0), $this->Align(str_replace("@01", $vrow['lv001'], $lvTdTextBox), (int) $this->ArrView[$lstArr[$i]]));
						} else
							$vTemp = str_replace("@02", $this->getvaluelink($lstArr[$i], $this->FormatView($vrow[$lstArr[$i]], (int) $this->ArrView[$lstArr[$i]])), $this->Align($lvTd, (int) $this->ArrView[$lstArr[$i]]));
						break;
					case 'lv911':
						if ($this->GetEdit() == 1) {
							$lvTdTextBox = "<td align=center><input class='txtenterquick' type=\"checkbox\" value=\"1\" " . (($vrow['lv911'] == 1) ? 'checked="true"' : '') . " @03 onclick=\"UpdateTextCheck(this,'" . $vrow['lv001'] . "',911)\" style=\"width:35px;text-align:center;\" tabindex=\"2\" maxlength=\"32\"   onKeyPress=\"return CheckKey(event,7)\"/></td>";
							$vTemp = str_replace("@02", $this->FormatView($vrow[$lstArr[$i]], 0), $this->Align(str_replace("@01", $vrow['lv001'], $lvTdTextBox), (int) $this->ArrView[$lstArr[$i]]));
						} else
							$vTemp = str_replace("@02", $this->getvaluelink($lstArr[$i], $this->FormatView($vrow[$lstArr[$i]], (int) $this->ArrView[$lstArr[$i]])), $this->Align($lvTd, (int) $this->ArrView[$lstArr[$i]]));
						break;
					case 'lv912':
						if ($this->GetEdit() == 1) {
							$lvTdTextBox = "<td align=center><input class='txtenterquick' type=\"checkbox\" value=\"1\" " . (($vrow['lv912'] == 1) ? 'checked="true"' : '') . " @03 onclick=\"UpdateTextCheck(this,'" . $vrow['lv001'] . "',912)\" style=\"width:35px;text-align:center;\" tabindex=\"2\" maxlength=\"32\"   onKeyPress=\"return CheckKey(event,7)\"/></td>";
							$vTemp = str_replace("@02", $this->FormatView($vrow[$lstArr[$i]], 0), $this->Align(str_replace("@01", $vrow['lv001'], $lvTdTextBox), (int) $this->ArrView[$lstArr[$i]]));
						} else
							$vTemp = str_replace("@02", $this->getvaluelink($lstArr[$i], $this->FormatView($vrow[$lstArr[$i]], (int) $this->ArrView[$lstArr[$i]])), $this->Align($lvTd, (int) $this->ArrView[$lstArr[$i]]));
						break;
					case 'lv913':
						if ($this->GetEdit() == 1) {
							$lvTdTextBox = "<td align=center><input class='txtenterquick' type=\"checkbox\" value=\"1\" " . (($vrow['lv913'] == 1) ? 'checked="true"' : '') . " @03 onclick=\"UpdateTextCheck(this,'" . $vrow['lv001'] . "',913)\" style=\"width:35px;text-align:center;\" tabindex=\"2\" maxlength=\"32\"   onKeyPress=\"return CheckKey(event,7)\"/></td>";
							$vTemp = str_replace("@02", $this->FormatView($vrow[$lstArr[$i]], 0), $this->Align(str_replace("@01", $vrow['lv001'], $lvTdTextBox), (int) $this->ArrView[$lstArr[$i]]));
						} else
							$vTemp = str_replace("@02", $this->getvaluelink($lstArr[$i], $this->FormatView($vrow[$lstArr[$i]], (int) $this->ArrView[$lstArr[$i]])), $this->Align($lvTd, (int) $this->ArrView[$lstArr[$i]]));
						break;
					case 'lv914':
						if ($this->GetEdit() == 1) {
							$lvTdTextBox = "<td align=center><input class='txtenterquick' type=\"checkbox\" value=\"1\" " . (($vrow['lv914'] == 1) ? 'checked="true"' : '') . " @03 onclick=\"UpdateTextCheck(this,'" . $vrow['lv001'] . "',914)\" style=\"width:35px;text-align:center;\" tabindex=\"2\" maxlength=\"32\"   onKeyPress=\"return CheckKey(event,7)\"/></td>";
							$vTemp = str_replace(
								"@02",
								$this->FormatView($vrow[$lstArr[$i]] ?? '', 0),
								$this->Align(str_replace("@01", $vrow['lv001'] ?? '', $lvTdTextBox), (int) ($this->ArrView[$lstArr[$i]] ?? 0))
							);
						} else
							$vTemp = str_replace("@02", $this->getvaluelink($lstArr[$i], $this->FormatView($vrow[$lstArr[$i]], (int) $this->ArrView[$lstArr[$i]])), $this->Align($lvTd, (int) $this->ArrView[$lstArr[$i]]));
						break;
					default:
						$key = '';
						$vTemp = str_replace("@02", $this->getvaluelink($key, $this->FormatView($vrow[$lstArr[$i]] ?? '', (int) ($this->ArrView[$lstArr[$i]] ?? 0))), $this->Align($lvTd, (int) ($this->ArrView[$key] ?? 0)));
						break;
				}
				$strL = $strL . $vTemp;
				$key = $lstArr[$i];

				$val = isset($vrow[$key]) ? $vrow[$key] : ""; // Hoặc giá trị mặc định khác
				$viewType = isset($this->ArrView[$key]) ? (int) $this->ArrView[$key] : 0;

				$vTemp = str_replace(
					"@02",
					$this->getvaluelink($key, $this->FormatView($val, $viewType)),
					$this->Align($lvTd, $viewType)
				);
				//$vTemp=str_replace("@02",$this->getvaluelink($lstArr[$i],$this->FormatView($vrow[$lstArr[$i]],(int)$this->ArrView[$lstArr[$i]])),$this->Align($lvTd,(int)$this->ArrView[$lstArr[$i]]));
			}
			$strTr = $strTr . str_replace("@#01", $strL, str_replace("@02", $vrow['lv001'], str_replace("@03", $vorder, str_replace("@01", $vorder % 2, $lvTr))));

		}
		$strTrH=str_replace("@#01",$strH,$lvTrH);
		return str_replace("@#01",$strTrH.$strTrEnter.$strTr,$lvTable);
	}
	/////////////////////ListFieldExport//////////////////////////
	function ListFieldExport($lvFrom, $lvList, $maxRows)
	{
		if ($lvList == "")
			$lvList = $this->DefaultFieldList;
		$lvList = "," . $lvList . ",";
		$lstArr = explode(",", $this->DefaultFieldList);
		$lvSelect = "<ul id=\"menu1-nav\" onkeyup=\"return CheckKeyCheckTabExp(event)\">
						<li class=\"menusubT1\"><img src=\"$this->Dir../images/lvicon/config.png\" border=\"0\" />" . $this->ArrFunc[12] . "
							<ul id=\"submenu1-nav\">
							@#01
							</ul>
						</li>
					</ul>";
		$strScript = "		
		<script language=\"javascript\">
		function Export(vFrom,value)
		{
			window.open('" . $this->Dir . "lv_lv0066/?lang=" . $this->lang . "&func='+value+'&ID=" . base64_encode($this->lv002) . "','','width=800,height=600,left=200,top=100,screenX=0,screenY=100,resizable=yes,status=no,scrollbars=yes,menubar=yes');
		}
	
		
		</script>
";
		$lvScript = "<li class=\"menuT\"> @01 </li>";
		$lvexcel = "<input class=lvbtdisplay type=\"button\" id=\"lvbuttonexcel\" value=\"" . $this->ArrFunc[13] . "\" onclick=\"Export($lvFrom,'excel')\">";
		$lvpdf = "<input class=lvbtdisplay type=\"button\" id=\"lvbutton\" value=\"" . $this->ArrFunc[15] . "\" onclick=\"Export($lvFrom,'pdf')\">";
		$lvword = "<input class=lvbtdisplay type=\"button\" id=\"lvbutton\" value=\"" . $this->ArrFunc[14] . "\" onclick=\"Export($lvFrom,'word')\">";
		$strGetList = "";
		$strGetScript = "";

		$strTemp = str_replace("@01", $lvexcel, $lvScript);
		$strGetScript = $strGetScript . $strTemp;
		$strTemp = str_replace("@01", $lvword, $lvScript);
		$strGetScript = $strGetScript . $strTemp;
		$strTemp = str_replace("@01", $lvpdf, $lvScript);
		$strGetScript = $strGetScript . $strTemp;
		$strReturn = str_replace("@#01", $strGetScript, $lvSelect) . $strScript;
		return $strReturn;

	}
	/////////////////////ListFieldSave//////////////////////////
	function ListFieldSave($lvFrom, $lvList, $maxRows, $lvOrder, $lvSortNum)
	{
		if ($lvList == "")
			$lvList = $this->DefaultFieldList;
		$lvList = "," . $lvList . ",";
		$lstArr = explode(",", $this->DefaultFieldList);
		$lvArrOrder = explode(",", $lvOrder);
		$lvSelect = "<ul id=\"menu-nav\" onkeyup=\"return CheckKeyCheckTab(event,$lvFrom," . count($lstArr) . ")\">
						<li class=\"menusubT\"><img src=\"$this->Dir../images/lvicon/config.png\" border=\"0\" />" . $this->ArrFunc[11] . "
							<ul id=\"submenu-nav\">
							@#01
							</ul>
						</li>
					</ul>";
		$strScript = "		
		<script language=\"javascript\">
		function SelectChk(vFrom,len)
		{
			vFrom.txtFieldList.value=getChecked(len,'lvdisplaychk');
			vFrom.txtOrderList.value=getAlllen(len,'lvorder');
			vFrom.txtFlag.value=2;
			vFrom.submit();
		}
		function lv_on_open(opt)
		{
			div = document.getElementById('lvsllist');
			if(opt==0)
			{
				div.size=1;
			}
			else
				div.size=div.length;
			
		}
		function getChecked(len,nameobj)
		{
			var str='';
			for(i=0;i<len;i++)
			{
			div = document.getElementById(nameobj+i);
			if(div.checked)
				{

				if(str=='') 
					str=''+div.value;
				else
					 str=str+','+div.value;

				}
			}
			return str;
		}
		function getAlllen(len,nameobj)
		{
			var str='';
			for(i=0;i<len;i++)
			{
				div = document.getElementById(nameobj+i);
				if(str=='') 
					str=''+div.value;
				else
					 str=str+','+div.value;
			}
			return str;
		}
		</script>
";
		$lvScript = "<li class=\"menuT\"> @01 </li>";
		$lvNumPage = "" . $this->ArrOther[2] . "<input type=\"text\" class=\"lvmaxrow\" name=lvmaxrow id=lvmaxrow value=\"$maxRows\">";
		$lvSortPage = "" . GetLangSort(0, $this->lang) . "<select class=\"lvsortrow\" name=lvsort id=lvsort >
				<option value=0 " . (($lvSortNum == 0) ? 'selected' : '') . ">" . GetLangSort(1, $this->lang) . "</option>
				<option value=1 " . (($lvSortNum == 1) ? 'selected' : '') . ">" . GetLangSort(2, $this->lang) . "</option>
				<option value=2 " . (($lvSortNum == 2) ? 'selected' : '') . ">" . GetLangSort(3, $this->lang) . "</option>
		</select>";
		$lvChk = "<input type=\"checkbox\" id=\"lvdisplaychk@01\" name=\"lvdisplaychk@01\" value=\"@02\" @03><input id=\"lvorder@01\" name=\"lvorder@01\"  type=\"text\" value=\"@06\"\ style=\"width:20px\" >";
		$lvButton = "<input class=lvbtdisplay type=\"button\" id=\"lvbutton\" value=\"" . $this->ArrOther[1] . "\" onclick=\"SelectChk($lvFrom," . count($lstArr) . ")\">";
		$strGetList = "";
		$strGetScript = "";
		$strTemp = str_replace("@01", $lvButton, $lvScript);
		$strGetScript = $strGetScript . $strTemp;
		$strTemp = str_replace("@01", $lvNumPage, $lvScript);
		$strGetScript = $strGetScript . $strTemp;
		$strTemp = str_replace("@01", $lvSortPage, $lvScript);
		$strGetScript = $strGetScript . $strTemp;

		for ($i = 0; $i < count($lstArr); $i++) {

			$strTempChk = str_replace("@01", $i, $lvChk . $this->ArrPush[(int) $this->ArrGet[$lstArr[$i]]]);
			$strTempChk = str_replace("@02", $lstArr[$i], $strTempChk);

			$strTempChk = str_replace("@07", 100 + $i, $strTempChk);
			if (strpos($lvList, "," . $lstArr[$i] . ",") === FALSE) {
				$strTempChk = str_replace("@03", "", $strTempChk);

			} else {
				$strTempChk = str_replace("@03", "checked=checked", $strTempChk);
			}
			if (!isset($lvArrOrder[$i]) || $lvArrOrder[$i] == NULL || $lvArrOrder[$i] == "") {
				$strTempChk = str_replace("@06", $i, $strTempChk);
			} else
				$strTempChk = str_replace("@06", $lvArrOrder[$i], $strTempChk);


			$strTemp = str_replace("@01", $strTempChk, $lvScript);
			$strGetScript = $strGetScript . $strTemp;
		}
		$strReturn = str_replace("@#01", $strGetScript, $lvSelect) . $strScript;
		return $strReturn;

	}

	//////////////////////Buil list////////////////////
	function LV_BuilListReport($lvList, $lvFrom, $lvChkAll, $lvChk, $curRow, $maxRows, $paging, $lvOrderList)
	{

		if ($lvList == "")
			$lvList = $this->DefaultFieldList;
		if ($this->isView == 0)
			return false;
		$lstArr = explode(",", $lvList);
		$lstOrdArr = explode(",", $lvOrderList);
		$lstArr = $this->getsort($lstArr, $lstOrdArr);
		$strSort = "";
		switch ($lvSortNum) {
			case 0:
				break;
			case 1:
				$strSort = " order by " . $this->LV_SortBuild($this->GB_Sort, "asc");
				break;
			case 2:
				$strSort = " order by " . $this->LV_SortBuild($this->GB_Sort, "desc");
				break;
		}
		$lvTable = "<div align=\"center\"><img  src=\"" . $this->GetLogo() . "\" /></div>
		<div align=\"center\"><h1>" . ($this->ArrPush[0]) . "</h2></div>
		<table  align=\"center\" class=\"lvtable\" border=1>
		@#01
		</table>
		";
		$lvTrH = "<tr class=\"lvhtable\">
			<td width=1% class=\"lvhtable\">" . $this->ArrPush[1] . "</td>
			
			@#01
		</tr>
		";
		$lvTr = "<tr class=\"lvlinehtable@01\">
			<td width=1% onclick=\"Select_Check('$lvChk@03',$lvFrom, '$lvChk', '$lvChkAll')\">@03</td>
			@#01
		</tr>
		";
		$lvTdH = "<td width=\"@01\" class=\"lvhtable\">@02</td>";
		$lvTd = "<td align=@#05>@02</td>";
		$sqlS = "SELECT * FROM lv_lv0066 WHERE 1=1  " . $this->RptCondition . " $strSort LIMIT $curRow, $maxRows";
		$vorder = $curRow;
		$bResult = db_query($sqlS);
		$this->Count = db_num_rows($bResult);
		$strTrH = "";
		$strH = "";
		for ($i = 0; $i < count($lstArr); $i++) {
			$vTemp = str_replace("@01", "", $lvTdH);
			$vTemp = str_replace("@02", $this->ArrPush[(int) $this->ArrGet[$lstArr[$i]]], $vTemp);
			$strH = $strH . $vTemp;

		}

		while ($vrow = db_fetch_array($bResult)) {
			$strL = "";
			$vorder++;
			for ($i = 0; $i < count($lstArr); $i++) {
				$vTemp = str_replace("@02", $this->getvaluelink($lstArr[$i], $this->FormatView($vrow[$lstArr[$i]], (int) $this->ArrView[$lstArr[$i]])), $this->Align($lvTd, (int) $this->ArrView[$lstArr[$i]]));
				$strL = $strL . $vTemp;
			}
			$strTr = $strTr . str_replace("@#01", $strL, str_replace("@02", $vrow['lv001'], str_replace("@03", $vorder, str_replace("@01", $vorder % 2, $lvTr))));

		}
		$strTrH = str_replace("@#01", $strH, $lvTrH);
		return str_replace("@#01", $strTrH . ($strTr ?? ""), $lvTable);
	}

	public function LV_LinkField($vFile, $vSelectID)
	{
		return ($this->CreateSelect($this->sqlcondition($vFile, $vSelectID), 0));
	}
	private function sqlcondition($vFile, $vSelectID)
	{
		$vsql = "";
		switch ($vFile) {
			case 'lv002':
				$vsql = "select lv001,lv003 lv002,IF(lv001='$vSelectID',1,0) lv003 from  hr_lv0002";
				break;
			case 'lv003':
				$vsql = "
				select lv001,lv001 lv002,IF(lv001='$vSelectID',1,0) lv003 from  lv_lv0004
				union 
				select lv001,lv001 lv002,IF(lv001='$vSelectID',1,0) lv003 from  lv_lv0066
				";
				break;
			case 'lv903':
				$vsql = "select lv001,lv003 lv002,IF(lv001='$vSelectID',1,0) lv003 from  ts_lv0001";
				break;
			case 'lv099':
				$vsql = "select lv001,lv002,IF(lv001='$vSelectID',1,0) lv003 from  lv_lv0011";
				break;
			case 'lv094':
				$vsql = "select lv001,lv003 lv002,IF(lv001='$vSelectID',1,0) lv003 from  lv_lv0066";
				break;
			case 'lv906':
				$vsql = "select lv001,lv002,IF(lv001='$vSelectID',1,0) lv003 from  cr_lv0008";
				break;

		}
		return $vsql;
	}
	private function getvaluelink($vFile, $vSelectID)
	{
		$linkArr = $this->ArrGetValueLink[$vFile][$vSelectID] ?? null;
		if (is_array($linkArr) && isset($linkArr[0])) {
			return $linkArr[1] ?? '';
		}
		if ($vSelectID == "") {
			return $vSelectID;
		}
		switch ($vFile) {
			case 'lv003':
				$vsql = "select lv001,lv002,IF(lv001='$vSelectID',1,0) lv003 from  lv_lv0004 where lv001='$vSelectID'";
				break;
			case 'lv099':
				$vsql = "select lv001,lv002,IF(lv001='$vSelectID',1,0) lv003 from  lv_lv0011 where lv001='$vSelectID'";
				break;
			case 'lv094':
				$vsql = "select lv001,lv003 lv002,IF(lv001='$vSelectID',1,0) lv003 from  lv_lv0066 where lv001='$vSelectID'";
				break;
			default:
				$vsql = "";
				break;
		}
		if ($vsql == "") {
			return $vSelectID;
		} else {
			$lvResult = db_query($vsql);
			$this->ArrGetValueLink[$vFile][$vSelectID][0] = true;
		}
		$lvopt = $lvopt ?? 0;
		while ($row = db_fetch_array($lvResult)) {
			$this->ArrGetValueLink[$vFile][$vSelectID][1] = ($lvopt == 0) ? $row['lv002'] : (($lvopt == 1) ? $row['lv001'] . "(" . $row['lv002'] . ")" : (($lvopt == 2) ? $row['lv002'] . "(" . $row['lv001'] . ")" : $row['lv001']));
			return $this->ArrGetValueLink[$vFile][$vSelectID][1];
		}

	}

}
?>