<?php
/*
Báº£ng quyá»�n ERPSOFV2R.com
KhÃ´ng Ä‘Æ°á»£c sá»­a
NgÃ y táº¡o:06/04/2007
*/
if (file_exists(__DIR__ . '/couchdb_functions.php')) {
	include_once(__DIR__ . '/couchdb_functions.php');
}

class lv_controler
{
	protected $isView = 0;
	protected $isAdd = 0;
	protected $isEdit = 0;
	protected $isDel = 0;
	protected $isFil = 0;
	protected $isRpt = 0;
	protected $isRel = 0;
	protected $isHelp = 0;
	protected $isConfig = 0;
	protected $isFKho = 0;
	protected $isApr = 0;
	protected $isUnApr = 0;
	///////////////Control////////////////////
	protected $UserID = "";
	protected $isLog = 1;
	public $LV_UserID = "";
	protected $ValidatedUserCode = "";
	///////////Load max////////////
	public $MaxRows = 0;
	public $CurPage = 0;
	public $SortNum = 0;
	public $ListView = "";
	public $ListOrder = "";
	public $RptCondition = "";
	public $GB_Sort = null;
	protected $isRight = NULL;
	public $LE_CODE = "NjlIUS02VFdULTZIS1QtNlFIQQ==";
	public $domainrel = "http://192.168.100.2/staff";
	public $domain = "http://192.168.100.2/staff";

	public function Set_User($vCheckAdmin, $vUserID, $vright)
	{
		$this->UserID = $vUserID;
		$this->isRight = $vCheckAdmin;
		$auth = $this->Extract_Request_Auth();
		$code = $auth['code'];
		$token = $auth['token'];

		if (!$this->CheckStaff_Valid($code, $token)) {
			echo json_encode(['message' => 'invalid']);
			exit();
		}

		$effectiveUserID = trim((string)$vUserID);
		if ($effectiveUserID == "") {
			$effectiveUserID = $this->ValidatedUserCode != "" ? $this->ValidatedUserCode : $code;
		}

		$this->UserID = $effectiveUserID;
		$this->LV_UserID = $effectiveUserID != "" ? $this->Get_User($effectiveUserID, 'lv006') : "";
		$this->isAdd = $this->SetOnce($vCheckAdmin, $effectiveUserID, $vright, "Add");
		$this->isEdit = $this->SetOnce($vCheckAdmin, $effectiveUserID, $vright, "Edit");
		$this->isDel = $this->SetOnce($vCheckAdmin, $effectiveUserID, $vright, "Del");
		$this->isFil = $this->SetOnce($vCheckAdmin, $effectiveUserID, $vright, "Fil");
		$this->isRpt = $this->SetOnce($vCheckAdmin, $effectiveUserID, $vright, "Rpt");
		$this->isRel = $this->SetOnce($vCheckAdmin, $effectiveUserID, $vright, "Rel");
		$this->isHelp = $this->SetOnce($vCheckAdmin, $effectiveUserID, $vright, "Help");
		$this->isApr = $this->SetOnce($vCheckAdmin, $effectiveUserID, $vright, "Apr");
		$this->isUnApr = $this->SetOnce($vCheckAdmin, $effectiveUserID, $vright, "UnApr");
		$this->isView = $this->SetOnce($vCheckAdmin, $effectiveUserID, $vright, "View");
		$this->isConfig = $this->SetOnce($vCheckAdmin, $effectiveUserID, $vright, "Config");
		$this->isFKho = $this->SetOnce($vCheckAdmin, $effectiveUserID, $vright, "FKho");
	}
	public function CheckStaff_Valid($vCode, $vToken)
	{
		$this->ValidatedUserCode = "";
		$vCode = trim((string)$vCode);
		$vToken = trim((string)$vToken);

		if ($vToken == "") {
			return false;
		}

		if (function_exists('findUserByToken')) {
			$tokenResult = findUserByToken($vToken);
			if (is_array($tokenResult) && !empty($tokenResult['success'])) {
				$resolvedCode = isset($tokenResult['username']) ? trim((string)$tokenResult['username']) : "";
				if ($resolvedCode == "" && isset($tokenResult['userData']['lv001'])) {
					$resolvedCode = trim((string)$tokenResult['userData']['lv001']);
				}
				if ($vCode != "" && $resolvedCode != "" && strcasecmp($resolvedCode, $vCode) !== 0) {
					return false;
				}
				$this->ValidatedUserCode = $resolvedCode != "" ? $resolvedCode : $vCode;
				return true;
			}
			if (is_array($tokenResult) && isset($tokenResult['message']) && strtolower((string)$tokenResult['message']) == 'token not found') {
				return false;
			}
		}

		if ($vCode == "") {
			return false;
		}

		$lvsql = "select count(*) Nums from  lv_lv0007 Where lv001='" . sof_escape_string($vCode) . "' and lv097='" . sof_escape_string($vToken) . "'";
		$vresult = db_query($lvsql);
		$vrow = db_fetch_array($vresult);
		if ($vrow && isset($vrow['Nums']) && (int)$vrow['Nums'] > 0) {
			$this->ValidatedUserCode = $vCode;
			return true;
		}

		return false;
	}

	private function GetRequestHeaderValue($headerName)
	{
		$headerName = strtolower((string)$headerName);
		if (function_exists('getallheaders')) {
			$headers = getallheaders();
			if (is_array($headers)) {
				foreach ($headers as $key => $value) {
					if (strtolower((string)$key) === $headerName) {
						return trim((string)$value);
					}
				}
			}
		}

		$serverKey = 'HTTP_' . strtoupper(str_replace('-', '_', $headerName));
		if (isset($_SERVER[$serverKey])) {
			return trim((string)$_SERVER[$serverKey]);
		}

		return '';
	}

	private function GetRequestAuthValue($input, $key)
	{
		if (is_array($input) && isset($input[$key]) && trim((string)$input[$key]) != "") {
			return trim((string)$input[$key]);
		}
		if (isset($_POST[$key]) && trim((string)$_POST[$key]) != "") {
			return trim((string)$_POST[$key]);
		}
		if (isset($_GET[$key]) && trim((string)$_GET[$key]) != "") {
			return trim((string)$_GET[$key]);
		}
		return '';
	}

	private function Extract_Request_Auth()
	{
		global $input;
		$requestInput = is_array($input) ? $input : null;
		if (!is_array($requestInput)) {
			$rawBody = file_get_contents('php://input');
			if ($rawBody) {
				$decoded = json_decode($rawBody, true);
				if (is_array($decoded)) {
					$requestInput = $decoded;
					$input = $decoded;
				}
			}
		}

		$code = $this->GetRequestAuthValue($requestInput, 'code');
		$token = $this->GetRequestAuthValue($requestInput, 'token');

		if ($code == '') {
			$code = $this->GetRequestHeaderValue('X-USER-CODE');
		}
		if ($code == '') {
			$code = $this->GetRequestHeaderValue('X-USER-USERNAME');
		}
		if ($token == '') {
			$token = $this->GetRequestHeaderValue('X-USER-TOKEN');
		}
		if ($token == '') {
			$token = $this->GetRequestHeaderValue('X-SOF-USER-TOKEN');
		}
		if ($token == '') {
			$token = $this->GetRequestHeaderValue('SOF-User-Token');
		}
		if ($token == '') {
			$authorization = $this->GetRequestHeaderValue('Authorization');
			if ($authorization != '' && stripos($authorization, 'Bearer ') === 0) {
				$token = trim(substr($authorization, 7));
			}
		}

		return array(
			'code' => trim((string)$code),
			'token' => trim((string)$token),
		);
	}

	public function LV_Escape_String($lvStr)
	{
		return mysql_escape_string($lvStr);
	}
	public function LV_SortBuild($lvArr, $sort)
	{
		if (count($lvArr) == 0)
			return " lv001 " . $sort;
		$lvReturn = "";
		for ($i = 0; $i <= 9; $i++) {
			if ($lvArr[$i] != "" && $lvArr[$i] != NULL) {
				if ($lvReturn == "")
					$lvReturn = $lvArr[$i] . " " . $sort;
				else
					$lvReturn = $lvReturn . "," . $lvArr[$i] . " " . $sort;
			}
		}
		return $lvReturn;
	}
	public function getsort($vArr1, $vArr2)
	{
		$lstArr = $vArr1;
		$vArr1 = explode(",", $this->DefaultFieldList);
		$vReturn = array();
		$vGetArr = array();
		$vBothArr = array();
		for ($i = 0; $i < count($vArr1); $i++) {
			$vBothArr[$i][0] = $vArr2[$i];
			$vBothArr[$i][1] = $vArr1[$i];
			$vGetArr[$i] = $vArr2[(int) substr($vArr1[$i], 2, 3) - 1];
			if (!(strpos($vBothArr[$i][0], ".") === false)) {
				$tmpArr = explode(".", $vBothArr[$i][0]);
				$this->GB_Sort[(int) ($tmpArr[1])] = $vBothArr[$i][1];
			}
		}
		$strpos = "";
		for ($i = 0; $i < count($vArr1); $i++) {
			$pos = 0;
			$vTem = $vBothArr[$i][0];
			for ($j = 0; $j < count($vBothArr); $j++) {
				if ($vTem > $vBothArr[$j][0])
					$pos++;
			}
			while (true) {
				if (strpos($strpos, "@" . $pos . "@") === false) {
					$vReturn[$pos] = $vBothArr[$i][1];
					break;
				} else {
					$pos++;
				}
			}
			$strpos = "@" . $pos . "@" . $strpos;
		}
		$vArrTemp = array();
		$j = 0;
		for ($i = 0; $i < count($vReturn); $i++) {

			if (array_search($vReturn[$i], $lstArr) === FALSE) {
			} else {
				$vArrTemp[$j] = $vReturn[$i];
				$j++;
			}
		}
		return $vArrTemp;
	}
	private function SetOnce($vCheckAdmin, $vUserID, $vright, $vrightcontrol)
	{
		if ('admin' == $vCheckAdmin) {
			return 1;
		} else {
			$vsql = "select count(*) as count from lv_lv0009 A Where A.lv002='$vrightcontrol' and A.lv004=1 and A.lv003 in (select lv001 from lv_lv0008 B where B.lv002='$vUserID' and B.lv003='$vright' and B.lv004=1) ";
			$tresult = db_query($vsql);
			$trow = db_fetch_array($tresult);
			return (int) $trow['count'];
		}
	}
	public function Get_User($vUserID, $vField)
	{
		$vsql = "select $vField from lv_lv0007 where lv001='$vUserID'";
		$tresult = db_query($vsql);
		$trow = db_fetch_array($tresult);
		return $trow[$vField];
	}
	protected function TabFunction($lvFrom, $lvList, $maxRows)
	{
		$strTable = "<TABLE id=lvtoolbar cellSpacing=0 cellPadding=0 border=0>
        <TBODY>
        <TR vAlign=center align=middle>
		@#01
		</TR>
		</TBODY>
		</TABLE>
		";
		$strTDNo = "<td><div style='margin-top:7px;'>/</div></td>";
		$strTD = "<td nowrap=\"nowrap\"><a class=lvtoolbar 
            href=\"javascript:@02(@03)\" tabindex=\"@05\"><div style='float:left'><img alt=\"NoImg\" src=\"$this->Dir../images/icon/@04.png\" align=\"middle\" border=\"0\" name=\"new\" class=\"lviconimg\" /></div><div class='lv_functext' style='float:left'>@01</div></a></td>";
		$strNoneTD = "<td nowrap=\"nowrap\"><a class=lvtoolbar 
             tabindex=\"@05\"><div style='float:left'><img alt=\"NoImg\" src=\"$this->Dir../images/icon/@04.png\" align=\"middle\" border=\"0\" name=\"new\" class=\"lviconimg\" ></div><div style='float:left'  class='lv_functext'>@01</div></a></td>";
		$strFunc = "";
		if ($this->isAdd == 1) {
			$strTemp = str_replace("@02", "Add", $strTD);
			$strTemp = str_replace("@03", "", $strTemp);
			$strTemp = str_replace("@04", "Add", $strTemp);
			$strTemp = str_replace("@05", "1", $strTemp);
			$strTemp = str_replace("@01", $this->ArrFunc[1], $strTemp);
			$strFunc = $strFunc . $strTemp . $strTDNo;
			if ($this->isOnce == 1) {
				$strTemp = str_replace("@02", "AddOne", $strTD);
				$strTemp = str_replace("@03", "", $strTemp);
				$strTemp = str_replace("@04", "AddOne", $strTemp);
				$strTemp = str_replace("@05", "1", $strTemp);
				$strTemp = str_replace("@01", GetLangTopBar(11, $this->lang), $strTemp);
				$strFunc = $strFunc . $strTemp . $strTDNo;
			}
			if ($this->isAddPer == 1) {
				$strTemp = str_replace("@02", "AddPer", $strTD);
				$strTemp = str_replace("@03", "", $strTemp);
				$strTemp = str_replace("@04", "AddPer", $strTemp);
				$strTemp = str_replace("@05", "1", $strTemp);
				$strTemp = str_replace("@01", GetLangTopBar(13, $this->lang), $strTemp);
				$strFunc = $strFunc . $strTemp . $strTDNo;
			}
		}
		if ($this->isEdit == 1) {
			$strTemp = str_replace("@02", "Edt", $strTD);
			$strTemp = str_replace("@03", "", $strTemp);
			$strTemp = str_replace("@04", "Edt", $strTemp);
			$strTemp = str_replace("@05", "3", $strTemp);
			$strTemp = str_replace("@01", $this->ArrFunc[2], $strTemp);
			$strFunc = $strFunc . $strTemp . $strTDNo;
			if ($this->isReset == 1) {
				$strTemp = str_replace("@02", "Reset", $strTD);
				$strTemp = str_replace("@03", "", $strTemp);
				$strTemp = str_replace("@04", "Reset", $strTemp);
				$strTemp = str_replace("@05", "1", $strTemp);
				$strTemp = str_replace("@01", GetLangTopBar(12, $this->lang), $strTemp);
				$strFunc = $strFunc . $strTemp . $strTDNo;
			}
		}
		if ($this->isDel == 1) {
			$strTemp = str_replace("@02", "Del", $strTD);
			$strTemp = str_replace("@03", "", $strTemp);
			$strTemp = str_replace("@04", "Del", $strTemp);
			$strTemp = str_replace("@05", "3", $strTemp);
			$strTemp = str_replace("@01", $this->ArrFunc[3], $strTemp);
			$strFunc = $strFunc . $strTemp . $strTDNo;
		}
		if ($this->isFil == 1) {
			$strTemp = str_replace("@02", "Fil", $strTD);
			$strTemp = str_replace("@03", "", $strTemp);
			$strTemp = str_replace("@04", "Filter", $strTemp);
			$strTemp = str_replace("@05", "3", $strTemp);
			$strTemp = str_replace("@01", $this->ArrFunc[4], $strTemp);
			$strFunc = $strFunc . $strTemp . $strTDNo;
		}
		if ($this->isRpt == 1) {
			$strTemp = str_replace("@02", "Rpt", $strTD);
			$strTemp = str_replace("@03", "", $strTemp);
			$strTemp = str_replace("@04", "Rpt", $strTemp);
			$strTemp = str_replace("@05", "3", $strTemp);
			$strTemp = str_replace("@01", $this->ArrFunc[5], $strTemp);
			$strFunc = $strFunc . $strTemp . $strTDNo;
		}
		if ($this->isApr == 1) {
			$strTemp = str_replace("@02", "Apr", $strTD);
			$strTemp = str_replace("@03", "", $strTemp);
			$strTemp = str_replace("@04", "Apr", $strTemp);
			$strTemp = str_replace("@05", "3", $strTemp);
			$strTemp = str_replace("@01", $this->ArrFunc[6], $strTemp);
			$strFunc = $strFunc . $strTemp . $strTDNo;
		}
		if ($this->isUnApr == 1) {
			$strTemp = str_replace("@02", "UnApr", $strTD);
			$strTemp = str_replace("@03", "", $strTemp);
			$strTemp = str_replace("@04", "UnApr", $strTemp);
			$strTemp = str_replace("@05", "3", $strTemp);
			$strTemp = str_replace("@01", $this->ArrFunc[7], $strTemp);
			$strFunc = $strFunc . $strTemp . $strTDNo;
		}
		if ($this->isRel == 1) {
			$strTemp = str_replace("@02", $lvFrom . ".submit", $strTD);
			$strTemp = str_replace("@03", "", $strTemp);
			$strTemp = str_replace("@04", "Reload", $strTemp);
			$strTemp = str_replace("@05", "3", $strTemp);
			$strTemp = str_replace("@01", $this->ArrFunc[8], $strTemp);
			$strFunc = $strFunc . $strTemp . $strTDNo;
		}
		if ($this->isHelp == 1) {
			$strTemp = str_replace("@02", "Help", $strTD);
			$strTemp = str_replace("@03", "'" . ($this->objhelp) . "','$this->lang'", $strTemp);
			$strTemp = str_replace("@04", "Help", $strTemp);
			$strTemp = str_replace("@05", "3", $strTemp);
			$strTemp = str_replace("@01", $this->ArrFunc[9], $strTemp);
			$strFunc = $strFunc . $strTemp;
		}
		if ($this->isConfig == 1) {
			$strTemp = str_replace("@02", "Config", $strNoneTD);
			$strTemp = str_replace("@03", "'hr_lv0001'", $strTemp);
			$strTemp = str_replace("@04", "Config", $strTemp);
			$strTemp = str_replace("@05", "3", $strTemp);
			$strTemp = str_replace("@01", $this->ArrFunc[10], $strTemp);
			$strFunc = $strFunc . $strTemp;
		}

		return str_replace("@#01", $strFunc, $strTable);
	}
	/////////////InsertLog/////////////////////	
	protected function InsertLogOperation($vDateLog, $vTableID, $vLogText)
	{
		if ($this->isLog == 0)
			return false;
		$lvsql = "insert into lv_lv0001 (lv002,lv003,lv004,lv005,lv006,lv007) values('$this->UserID','$vDateLog','$vTableID','$vLogText','" . $_SESSION['SOFIP'] . "','" . $_SESSION['SOFMAC'] . "')";
		$return = db_query($lvsql);
		if ($return)
			return $return;
		else {
			$lvsql = "insert into lv_lv0001 (lv002,lv003,lv004,lv005) values('$this->UserID','$vDateLog','$vTableID','$vLogText')";
			return db_query($lvsql);
		}
	}
	public function SaveOperation($vlv002, $vlv003, $vlv004, $vlv005, $vlv006, $vlv007, $vlv008)
	{
		$lvsql = "select count(*) nums from lv_lv0002 where lv002='$vlv002' and lv003='$vlv003' ";
		$vresult = db_query($lvsql);
		$vrow = db_fetch_array($vresult);
		if ($vrow['nums'] > 0) {
			return $this->UpdateSaveOperation($vlv002, $vlv003, $vlv004, $vlv005, $vlv006, $vlv007, $vlv008);
		} else
			return $this->InsertSaveOperation($vlv002, $vlv003, $vlv004, $vlv005, $vlv006, $vlv007, $vlv008);
	}
	//////////Insert Save Oper//////////////////////////////////	
	protected function InsertSaveOperation($vlv002, $vlv003, $vlv004, $vlv005, $vlv006, $vlv007, $vlv008)
	{
		$lv_lv009 = mysql_escape_string($this->GetCondition());
		$lvsql = "insert into lv_lv0002 (lv002,lv003,lv004,lv005,lv006,lv007,lv008,lv009) values('$vlv002','$vlv003','$vlv004','$vlv005','$vlv006','$vlv007','$vlv008','$lv_lv009')";
		return db_query($lvsql);
	}
	protected function UpdateSaveOperation($vlv002, $vlv003, $vlv004, $vlv005, $vlv006, $vlv007, $vlv008)
	{
		$lv_lv009 = mysql_escape_string($this->GetCondition());
		$lvsql = "update lv_lv0002 set lv004='$vlv004',lv005='$vlv005',lv006='$vlv006',lv007='$vlv007',lv008='$vlv008',lv009='$lv_lv009' where lv002='$vlv002' and lv003='$vlv003'";
		return db_query($lvsql);
	}
	public function LoadSaveOperation($vlv002, $vlv003)
	{
		$lvsql = "select lv004,lv005,lv006,lv007,lv008,lv009 from  lv_lv0002 Where lv002='$vlv002' and lv003='$vlv003'";
		$vresult = db_query($lvsql);
		$vrow = db_fetch_array($vresult);
		if ($vrow) {
			$this->ListView = $vrow['lv004'];
			$this->MaxRows = $vrow['lv005'];
			$this->CurPage = $vrow['lv006'];
			$this->ListOrder = $vrow['lv007'];
			$this->SortNum = $vrow['lv008'];
			$this->RptCondition = $vrow['lv009'];
		} else {
			$lvsql = "select lv004,lv005,lv006,lv007,lv008,lv009 from  lv_lv0002 Where lv002='admin' and lv003='$vlv003'";
			$vresult = db_query($lvsql);
			$vrow = db_fetch_array($vresult);
			if ($vrow) {
				$this->ListView = $vrow['lv004'];
				$this->MaxRows = $vrow['lv005'];
				$this->CurPage = $vrow['lv006'];
				$this->ListOrder = $vrow['lv007'];
				$this->SortNum = $vrow['lv008'];
				$this->RptCondition = $vrow['lv009'];
			}
		}
	}
	public function CreateSelect($lvsql, $lvopt)
	{
		$strReturn = "";
		$strOption = '<option value="@01" @03>@02</option>';
		$lvResult = db_query($lvsql);
		while ($row = db_fetch_array($lvResult)) {
			$lvTemp = str_replace("@01", $row['lv001'], $strOption);
			$lvTemp = str_replace("@03", ((int) $row['lv003'] == 1) ? 'selected="selected"' : '', $lvTemp);
			$lvTemp = str_replace("@02", ($lvopt == 0) ? $row['lv002'] : (($lvopt == 1) ? $row['lv001'] . "(" . $row['lv002'] . ")" : (($lvopt == 2) ? $row['lv002'] . "(" . $row['lv001'] . ")" : $row['lv001'])), $lvTemp);
			$strReturn = $strReturn . $lvTemp;
		}
		return $strReturn;
	}
	public function FormatView($vValue, $vopt)
	{
		$this->langconver = $this->lang;

		switch ($vopt) {
			case 1:
				$this->langconver = 'EN';
				return LCurrency($vValue, $this->langconver);
			case 2:
				if ($vValue == '' || $vValue == '0000-00-00')
					return '';
				return formatdate($vValue, $this->lang);
			case 22:
				return formatdate(substr($vValue, 0, 10), $this->lang) . ' ' . substr($vValue, 10, 9);
			case 3:
				return '***';
			case 4:
				return formatdate($vValue, $this->langconver) . " " . substr($vValue, 11, 8);
			case 5:
				$this->langconver = 'EN';
				if ($vValue < 0)
					return '(' . LCurrency(-$vValue, $this->langconver) . ')';
				else
					return LCurrency($vValue, $this->langconver);
			case 6:
				return '<img src="' . $this->path_server . $this->path_web . $vValue . '" border="0" style="height:' . $this->img_height . 'px" />';
			case 10:
				$this->langconver = 'EN';
				return LCurrencys($vValue, $this->langconver);
			case 20:
				$this->langconver = 'EN';
				return ($vValue == 0) ? '&nbsp' : LCurrencys($vValue, $this->langconver);
			case 13:
				$this->langconver = 'EN';
				return LCurrencys($vValue, $this->langconver);
			default:
				//if($vValue==NULL) $vValue="&nbsp;";
				return $vValue;
		}
	}
	public function FormatSave($vValue, $vopt)
	{
		switch ($vopt) {
			case 1:
				return LCurrency($vValue, $this->lang);
			case 2:
				return recoverdate($vValue, $this->lang);
			default:
				return $vValue;
		}
	}
	public function GetLogo()
	{
		$vPathLogo = '../../../images/logo/';
		$lvsql = "select  lv001,lv009 from  hr_lv0001 where lv001='THAIDUCLAM'";
		$vresult = db_query($lvsql);
		$vrow = db_fetch_array($vresult);
		if ($vrow) {
			return $vPathLogo . $vrow['lv001'] . "/" . $vrow['lv009'];
		}

		return '';
	}
	public function Get_LECODE()
	{
		return base64_decode($this->LE_CODE);
	}
	public function GetCompany()
	{
		$lvsql = "select  lv002 from  hr_lv0001 where lv001='THAIDUCLAM'";
		$vresult = db_query($lvsql);
		if (!$vresult)
			return;
		$vrow = db_fetch_array($vresult);
		if ($vrow) {
			return $vrow['lv002'];
		}

		return '';
	}
	public function GetAddress()
	{
		$lvsql = "select  lv003 from  hr_lv0001 where lv001='THAIDUCLAM'";
		$vresult = db_query($lvsql);
		if (!$vresult)
			return;
		$vrow = db_fetch_array($vresult);
		if ($vrow) {
			return $vrow['lv003'];
		}

		return '';
	}
	public function GetPhone()
	{
		$lvsql = "select  lv005 from  hr_lv0001 where lv001='THAIDUCLAM'";
		$vresult = db_query($lvsql);
		if (!$vresult)
			return;
		$vrow = db_fetch_array($vresult);
		if ($vrow) {
			return $vrow['lv005'];
		}

		return '';
	}
	public function GetFax()
	{
		$lvsql = "select  lv006 from  hr_lv0001 where lv001='THAIDUCLAM'";
		$vresult = db_query($lvsql);
		if (!$vresult)
			return;
		$vrow = db_fetch_array($vresult);
		if ($vrow) {
			return $vrow['lv006'];
		}

		return '';
	}
	public function GetTaxCode()
	{
		$lvsql = "select  lv008 from  hr_lv0001 where lv001='THAIDUCLAM'";
		$vresult = db_query($lvsql);
		if (!$vresult)
			return;
		$vrow = db_fetch_array($vresult);
		if ($vrow) {
			return $vrow['lv006'];
		}

		return '';
	}
	public function insert_id()
	{
		$lvsql = "select  LAST_INSERT_ID() ID";
		$vresult = db_query($lvsql);
		if (!$vresult)
			return;
		$vrow = db_fetch_array($vresult);
		if ($vrow) {
			return $vrow['ID'];
		}

		return '';
	}
	public function GetWeb()
	{
		$lvsql = "select  lv007 from  hr_lv0001 where lv001='THAIDUCLAM'";
		$vresult = db_query($lvsql);
		if (!$vresult)
			return;
		$vrow = db_fetch_array($vresult);
		if ($vrow) {
			return $vrow['lv007'];
		}

		return '';
	}

	public function Align($vtd, $vopt)
	{
		switch ($vopt) {
			case 1:
			case 10:
				return str_replace("@#05", "right", $vtd);
				break;
			case 3:
				return str_replace("@#05", "center", $vtd);
				break;
			default:
				return str_replace("@#05", "left", $vtd);
		}
	}
	///WH controler
	public function Get_WHControler()
	{
		$vUserID = $this->UserID;
		if ('admin' == $this->isRight) {
			$lvsql = "select  lv001 from  wh_lv0001";
		} else
			$lvsql = "select  lv003 as lv001 from  wh_lv0034 where lv002='$vUserID'";
		$str_return = "";
		$vresult = db_query($lvsql);
		while ($vrow = db_fetch_array($vresult)) {
			if ($str_return == "")
				$str_return = "'" . $vrow['lv001'] . "'";
			else
				$str_return = $str_return . ",'" . $vrow['lv001'] . "'";
		}
		if ($str_return == "")
			$str_return = "''";
		return $str_return;
	}
}