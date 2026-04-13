<?php
/////////////coding cr_lv0154///////////////
class   cr_lv0154 extends lv_controler
{
	public $lv001 = null;
	public $lv002 = null;
	public $lv003 = null;
	public $lv004 = null;
	public $lv005 = null;
	public $lv006 = null;
	public $lv007 = null;
	public $lv008 = null;
	public $lv009 = null;
	public $lv010 = null;
	public $lv011 = null;

	///////////
	public $DefaultFieldList = "lv199,lv001,lv029,lv214,lv114,lv115,lv116,lv004,lv002,lv003,lv829,lv862,lv009,lv118";
	////////////////////GetDate
	public $DateDefault = "1900-01-01";
	public $DateCurrent = "1900-01-01";
	public $Count = null;
	public $paging = null;
	public $lang = null;
	protected $objhelp = 'cr_lv0154';
	////////////
	var $ArrOther = array();
	var $ArrPush = array();
	var $ArrFunc = array();
	var $ArrGet = array("lv001" => "2", "lv002" => "3", "lv003" => "4", "lv004" => "5", "lv005" => "6", "lv006" => "7", "lv007" => "8", "lv008" => "9", "lv009" => "10", "lv010" => "11", "lv011" => "12", "lv012" => "13", "lv013" => "14", "lv014" => "15", "lv099" => "100", "lv106" => "107", "lv114" => "115", "lv199" => "200", "lv112" => "113", "lv113" => "114", "lv114" => "115", "lv115" => "116", "lv116" => "117", "lv110" => "111", "lv111" => "112", "lv214" => "215", "lv117" => "118", "lv118" => "119", "lv199" => "200", "lv214" => "215", "lv829" => "830", "lv862" => "863", "lv029" => "29");
	var $ArrView = array("lv001" => "0", "lv002" => "0", "lv003" => "0", "lv004" => "0", "lv005" => "0", "lv006" => "0", "lv007" => "0", "lv008" => "0", "lv009" => "22", "lv010" => "22", "lv011" => "0", "lv012" => "0", "lv013" => "0", "lv014" => "0", "lv099" => "0", "lv016" => "0", "lv114" => "0", "lv199" => "0", "lv112" => "10", "lv113" => "10", "lv110" => "10", "lv111" => "10", "lv118" => "22");

	public $LE_CODE = "NjlIUS02VFdULTZIS1QtNlFIQQ==";
	function __construct($vCheckAdmin, $vUserID, $vright)
	{
		$this->DateCurrent = GetServerDate() . " " . GetServerTime();
		$this->Set_User($vCheckAdmin, $vUserID, $vright);
		$this->isRel = 1;
		$this->isHelp = 1;
		$this->isConfig = 0;
		$this->isFil = 1;
		$this->lang = $_GET['lang'];
		$this->isDel = 0;
		$this->isAdd = 0;
		$this->isEdit = 0;
	}
	function LV_Get_MainPage($vtoday, &$numinvoice)
	{
		$vsql = "select * from  cr_lv0150 where year(lv009)=year('$vtoday') and month(lv009)=month('$vtoday') and day(lv009)=day('$vtoday')";
		$vresult = db_query($vsql);
		$i = 1;
		while ($vrow = db_fetch_array($vresult)) {
			if ($vrow['lv007'] == 0)
				$str = $str . "<a href=\"?lang=" . $_GET['lang'] . "&opt=19&item=&InvoiceID=" . $vrow['lv001'] . "&link=d2hfbHYwMDEwL3doX2x2MDAxMC5waHA=\">" . '<font color="black" title="' . $vrow['lv008'] . '" >Phiếu xuất::' . $vrow['lv001'] . "(" . $vrow['lv004'] . "-" . $vrow['lv002'] . "-" . $vrow['lv003'] . "-[ Total:" . $this->FormatView($this->LV_GetBLMoney($vrow['lv001']), 10) . " ])" . "</font></a> | ";
			else
				$str = $str . "<a href=\"?lang=" . $_GET['lang'] . "&opt=19&item=&InvoiceID=" . $vrow['lv001'] . "&link=d2hfbHYwMDEwL3doX2x2MDAxMC5waHA=\">" . '<font color="red" title="' . $vrow['lv008'] . '" >Invoice:' . $vrow['lv001'] . "(" . $vrow['lv004'] . "-" . $vrow['lv002'] . "-" . $vrow['lv003'] . "-[ Total:" . $this->FormatView($this->LV_GetBLMoney($vrow['lv001']), 10) . " ])" . "</font></a> | ";
			$i++;
		}
		$numinvoice = $i - 1;
		if ($i == 1) $str = "Không có phiếu";
		return $str;
	}
	function LV_Load()
	{
		$vsql = "select * from  cr_lv0150";
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
			$this->lv008 = $vrow['lv008'];
			$this->lv009 = $vrow['lv009'];
			$this->lv010 = $vrow['lv010'];
			$this->lv011 = $vrow['lv011'];
			$this->lv012 = $vrow['lv012'];
			$this->lv013 = $vrow['lv013'];
			$this->lv114 = $vrow['lv114'];
			$this->lv099 = $vrow['lv099'];
		}
	}
	function LV_LoadID($vlv001)
	{
		$lvsql = "select * from  cr_lv0150 Where lv001='$vlv001'";
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
			$this->lv008 = $vrow['lv008'];
			$this->lv009 = $vrow['lv009'];
			$this->lv010 = $vrow['lv010'];
			$this->lv011 = $vrow['lv011'];
			$this->lv012 = $vrow['lv012'];
			$this->lv013 = $vrow['lv013'];
			$this->lv014 = $vrow['lv014'];
			$this->lv015 = $vrow['lv015'];
			$this->lv114 = $vrow['lv114'];
			$this->lv099 = $vrow['lv099'];
		}
	}
	function LV_LoadPBH($vPBH, $vLanGH)
	{
		$lvsql = "select * from  cr_lv0150 Where lv006='$vPBH' and lv005='$vLanGH'";
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
			$this->lv008 = $vrow['lv008'];
			$this->lv009 = $vrow['lv009'];
			$this->lv010 = $vrow['lv010'];
			$this->lv011 = $vrow['lv011'];
			$this->lv012 = $vrow['lv012'];
			$this->lv013 = $vrow['lv013'];
			$this->lv014 = $vrow['lv014'];
			$this->lv114 = $vrow['lv114'];
			$this->lv099 = $vrow['lv099'];
		}
	}
	function LV_Insert()
	{

		if ($this->isAdd == 0) return false;
		$this->lv009 = ($this->lv009 != "") ? recoverdate(($this->lv009), $this->lang) : $this->DateDefault;
		$this->lv010 = ($this->lv010 != "") ? recoverdate(($this->lv010), $this->lang) : $this->DateDefault;
		$lvsql = "insert into cr_lv0150 (lv001,lv002,lv003,lv004,lv005,lv006,lv007,lv008,lv009,lv010,lv011,lv012,lv013,lv014,lv099,lv114) values('$this->lv001','$this->lv002','$this->lv003','$this->lv004','$this->lv005','$this->lv006','$this->lv007','$this->lv008',concat(CURRENT_DATE(),' ',CURRENT_TIME()),'$this->lv010','$this->lv011','$this->lv012','$this->lv013','$this->lv014','$this->lv099','$this->lv114')";
		$vReturn = db_query($lvsql);
		if ($vReturn) {
			$this->InsertLogOperation($this->DateCurrent, 'cr_lv0150.insert', sof_escape_string($lvsql));
		}
		return $vReturn;
	}
	function LV_Update()
	{
		if ($this->isEdit == 0) return false;
		$this->lv009 = ($this->lv009 != "") ? recoverdate(($this->lv009), $this->lang) : $this->DateDefault;
		$this->lv010 = ($this->lv010 != "") ? recoverdate(($this->lv010), $this->lang) : $this->DateDefault;
		$lvsql = "Update cr_lv0150 set lv002='$this->lv002',lv003='$this->lv003',lv004='$this->lv004',lv005='$this->lv005',lv006='$this->lv006',lv008='$this->lv008',lv009=concat('$this->lv009',' ',CURRENT_TIME()),lv010='$this->lv010',lv011='$this->lv011',lv012='$this->lv012',lv013='$this->lv013',lv014='$this->lv014',lv099='$this->lv099',lv114='$this->lv114' where  lv001='$this->lv001' AND lv007<=0;";
		$vReturn = db_query($lvsql);
		if ($vReturn) {
			//$this->LV_InsertOther($this->lv001);
			$this->InsertLogOperation($this->DateCurrent, 'cr_lv0150.update', sof_escape_string($lvsql));
		}
		return $vReturn;
	}
	public function GetBuilCheckListDept($vListID, $vID, $vTabIndex, $vTbl, $vFieldView = 'lv002', $vDepID = "")
	{
		$vListID = "," . $vListID . ",";
		$strTbl = "<table  align=\"center\" class=\"lvtable\">
		<input type=\"hidden\" id=$vID name=$vID value=\"@#02\">
		<tr class=\"lvlinehtable1\">
			@#01
		</tr>
		</table>
		";
		$lvChk = "<input type=\"checkbox\" id=\"$vID@01\" value=\"@02\" @03 title=\"@04\" tabindex=\"$vTabIndex\">";
		$lvTrH = "<td width=1%>@#01</td><td>@#02</td>";
		if ($vDepID == "") {
			$vsql = "select * from  " . $vTbl . " where lv001 in (1,2)";
		} else {
			$vReturn = "'" . str_replace(",", "','", $vDepID) . "'";
			$vsql = "select lv001,lv003 from  ts_lv0001 where (lv001 in ($vReturn)) and lv001 in ('KHOVATTU','CAPPHAT') ";
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
		$lvTrH = "<td width=1%>@#01</td><td>@#02</td>";
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
	function LV_GetFullXuatKhoVanTai($lvarr)
	{
		$vStrReturn = "";
		$lvsql = "select lv002 from  ts_lv0011 Where lv013='1' and lv002 in ($lvarr)";
		$vresult = db_query($lvsql);
		while ($vrow = db_fetch_array($vresult)) {
			if ($vStrReturn == '') {
				$vStrReturn = "'" . $vrow['lv002'] . "'";
			} else {
				if (strpos($vStrReturn, "'" . $vrow['lv002'] . "'") === false) $vStrReturn = $vStrReturn . ",'" . $vrow['lv002'] . "'";
			}
		}
		return $vStrReturn;
	}
	function LV_Delete($lvarr)
	{
		if ($this->isDel == 0) return false;
		//$lvarr1=$this->LV_GetFullXuatKhoVanTai($lvarr);
		if ($lvarr1 == '') $lvarr1 = "''";
		$lvsql = "DELETE FROM cr_lv0150  WHERE cr_lv0150.lv007<=0 AND cr_lv0150.lv001 IN ($lvarr)";
		$vReturn = db_query($lvsql);
		if ($vReturn) $this->InsertLogOperation($this->DateCurrent, 'cr_lv0150.delete', sof_escape_string($lvsql));
		return $vReturn;
	}
	function LV_Aproval($lvarr)
	{
		// if($this->isApr==0) return false;
		$lvsql = "Update cr_lv0150 set lv027=3  WHERE cr_lv0150.lv001 IN ($lvarr)  and lv007=1 and lv027=2";
		$vReturn = db_query($lvsql);
		if ($vReturn) {
			$this->InsertLogOperation($this->DateCurrent, 'cr_lv0150.approval', sof_escape_string($lvsql));
			$this->LV_SetHistoryArr('Apr', $lvarr);
			//$this->LV_InsertLocal($lvarr);
		}
		return $vReturn;
	}
	//History 
	function LV_SetHistoryArr($vFun, $lvarr)
	{
		$vArr = explode(",", $lvarr);
		foreach ($vArr as $vLongTermID) {
			$vLongTermID = str_replace("'", "", $vLongTermID);
			if ($vLongTermID != '') $this->LV_SetHistory($vFun, $vLongTermID);
		}
	}
	//Log
	function LV_SetHistory($vFun, $vLongTermID)
	{

		$vTitle = '';
		switch ($vFun) {
			case 'Apr':
				$vTitle = "ĐN cấp VT BGĐ duyệt!";
				break;
			case 'UnApr':
				$vTitle = "ĐN cấp VT BGĐ trả lại!";
				break;
			default:
				break;
		}
		//cr_lv0313
		if ($vTitle != '') {
			$lvsql = "insert into cr_lv0313 (lv002,lv003,lv004,lv005,lv006,lv007,lv009) values('" . $vLongTermID . "','" . sof_escape_string($vTitle) . "','$this->LV_UserID',now(),'$vFun',3,'" . sof_escape_string($this->Remark) . "')";
			$vReturn = db_query($lvsql);
			if ($vReturn) {
				$this->InsertLogOperation($this->DateCurrent, 'cr_lv0313.insert', sof_escape_string($lvsql));
				if ($vFun == 'UnApr') {
					$lvsql = "update cr_lv0313 set lv008=lv008+1 where lv002='$vLongTermID'";
					$vReturn = db_query($lvsql);
					if ($vReturn) {
						$this->InsertLogOperation($this->DateCurrent, 'cr_lv0313.update', sof_escape_string($lvsql));
					}
				}
			}
		}
	}
	function AddLotReciept($lvLotId, $lvItemId, $lvWhId, $lvColor, $lvSize, $lvTypeSize, $lvNote, $lvExpireDate)
	{
		if ($this->CheckLot($lvLotId, $lvItemId, $lvWhId) <= 0) {
			$lvsql = "insert into ts_lv0020 (lv001,lv002,lv003,lv004,lv005,lv006,lv007,lv008) select lv001,lv002,'$lvWhId',lv004,lv005,lv006,lv007,lv008 from ts_lv0020 where lv001='$lvLotId' and lv002='$lvItemId' limit 0,1";
			$vReturn = db_query($lvsql);
			if ($vReturn) $this->InsertLogOperation($this->DateCurrent, 'ts_lv0020.insert', sof_escape_string($lvsql));
		}
	}
	function LV_CheckDetail($vEmpID, $vWhID, $vNow, &$vGetKQ = '')
	{
		$vArrItem = array();
		$vDateStart = $vNow . ' 00:00:00';
		$vDateEnd = $vNow . ' 23:59:59';
		$vReturn = true;
		$lvsql = "select A.lv001,A.lv003 ItemID,A.lv004 SLItem,(select sum(A1.lv004) from ts_lv0009 A1 where A1.lv003=A.lv003 and A1.lv002 IN (select A11.lv001 from ts_lv0008 A11 Where A11.lv009<'$vDateStart' and A11.lv002='$vWhID')) ReReceiptQty,(select sum(A1.lv004) from ts_lv0009 A1 where A1.lv003=A.lv003 and A1.lv002 IN (select A11.lv001 from ts_lv0008 A11 Where A11.lv009>='$vDateStart' and A11.lv009<='$vDateEnd' and A11.lv002='$vWhID')) InReceiptQty,(select sum(A2.lv004) from ts_lv0011 A2 where A2.lv003=A.lv003 and A2.lv002 IN (select A21.lv001 from cr_lv0150 A21 Where A21.lv009<'$vDateStart' and A21.lv002='$vWhID')) ReOutlv004 ,(select sum(A2.lv004) from ts_lv0011 A2 where A2.lv003=A.lv003 and A2.lv002 IN (select A21.lv001 from cr_lv0150 A21 Where A21.lv009>='$vDateStart' and A21.lv009<='$vDateEnd' and A21.lv002='$vWhID')) InOutlv004 from ts_lv0031 A where lv007=1 and lv027=2 and A.lv002='$vEmpID'";
		$vresult = db_query($lvsql);
		while ($vrow = db_fetch_array($vresult)) {
			$vReReceiptQty = $vrow['ReReceiptQty'];
			$vInReceiptQty = $vrow['InReceiptQty'];
			$vReOutlv004 = $vrow['ReOutlv004'];
			$vInOutlv004 = $vrow['InOutlv004'];
			$vNumTon = $vReReceiptQty - $vReOutlv004 + $vInReceiptQty - $vInOutlv004;
			$vItem = $vrow['ItemID'];
			$vCodeID = $vrow['lv001'];
			if ($vNumTon < ($vArrItem[$vItem] + $vrow['SLItem']) || $vrow['SLItem'] < 0) {
				$vGetKQ = $vGetKQ . $vItem . "(SL/Tồn):" . ($vArrItem[$vItem] + $vrow['SLItem']) . "/" . $vNumTon . (($vrow['SLItem'] < 0) ? '->SL âm' : '') . "<br/>";
				$vReturn = false;
			}
			$vArrItem[$vItem] = $vArrItem[$vItem] + $vrow['SLItem'];

			//Lấy số tồn theo kho
			//Cập nhật số tồn cho dòng check và trả về true = Cho phép bán và false=ko cho phep ban
			$i++;
		}
		return $vReturn;
	}
	function CheckLot($lvLotId, $lvItemId, $lvWhId)
	{
		$sqlC = "SELECT COUNT(*) AS nums FROM ts_lv0020 WHERE lv001='$lvLotId' and lv002='$lvItemId' and lv003='$lvWhId'";
		$bResultC = db_query($sqlC);
		$arrRowC = db_fetch_array($bResultC);
		return $arrRowC['nums'];
	}
	function CheckChild($vlv002, $vlv003)
	{
		$sqlD = "SELECT count(*) nums FROM ts_lv0012 WHERE lv002='$vlv002' and lv003='$vlv003'";
		$vresult = db_query($sqlD);
		if ($vresult) {
			$vrow = db_fetch_array($vresult);
			if ($vrow['nums'] <= 0) {
				echo $lvsql = "insert into ts_lv0012 (lv002,lv003,lv004,lv005,lv006,lv007,lv008,lv009,lv010,lv011,lv012,lv013) values('$vlv002','$vlv003','0','$this->lv005','0','$this->lv007','$this->lv008','$this->lv009','$this->lv010','$this->lv011','$this->lv012','$this->lv013')";
				db_query($lvsql);
			}
		}
	}
	function LV_CheckInsertLocal($vMaPhieu)
	{
		$lvsql1 = "select count(*) nums from cr_lv0151 where lv002='" . $vMaPhieu . "' and lv087>0";
		$vReturn1 = db_query($lvsql1);
		$vrow1 = db_fetch_array($vReturn1);
		return $vrow1['nums'];
	}
	function LV_InsertLocal($lvarr)
	{
		//Xác định xuất khoe theo nguồn
		$vNow = GetServerDate();
		$lvsql = "select A.*,B.lv113 LoaiPBH from cr_lv0150 A left join sl_lv0013 B on A.lv006=B.lv115 where  A.lv001 IN ($lvarr)  and (select count(*) from ts_lv0010 BB where BB.lv006=A.lv001 )<=0";
		$vresult = db_query($lvsql);
		while ($vrow = db_fetch_array($vresult)) {
			if ($this->LV_CheckInsertLocal($vrow['lv001']) > 0) {
				$vWhID = 1;
				if ($vrow['LoaiPBH'] == 4) {
					$vlv010 = 'NOIBO';
					$vlv099 = '3';
				} else {
					$vlv010 = '';
					$vlv099 = '';
				}
				$vPNID = InsertWithCheckFist('ts_lv0010', 'lv001', '/PXK/MP' . substr(getyear($vNow), -2, 2), 4);
				$vslq = "insert into ts_lv0010(lv001,lv002,lv003,lv004,lv005,lv006,lv007,lv008,lv009,lv010,lv099) values('" . $vPNID . "','" . $vWhID . "','" . $vrow['lv003'] . "','" . $vrow['lv004'] . "','" . $vrow['lv005'] . "','" . $vrow['lv001'] . "',0,'" . $vrow['lv008'] . "','" . $vrow['lv009'] . "','" . $vlv010 . "','" . $vlv099 . "')";
				if (db_query($vslq)) {
					$lvsql1 = "select '" . $vPNID . "',lv003,lv087 lv004,lv005,lv006,lv007,lv008,lv009,lv010,lv011,lv012,lv013,lv014,lv015,lv016,lv198 from cr_lv0151 where lv002='" . $vrow['lv001'] . "' and lv087>0";
					$vReturn1 = db_query($lvsql1);
					while ($vrow1 = db_fetch_array($vReturn1)) {
						$lvsql = "insert into ts_lv0011 (lv002,lv003,lv004,lv005,lv006,lv007,lv008,lv009,lv010,lv011,lv012,lv013,lv014,lv015,lv016,lv198) values('" . $vPNID . "','" . $vrow1['lv003'] . "','" . $vrow1['lv004'] . "','" . $vrow1['lv005'] . "','" . $vrow1['lv006'] . "','" . $vrow1['lv007'] . "','" . $vrow1['lv008'] . "','" . $vrow1['lv009'] . "','" . $vrow1['lv010'] . "','" . $vrow1['lv011'] . "','" . $vrow1['lv012'] . "','" . $vrow1['lv013'] . "','" . $vrow1['lv014'] . "','" . $vrow1['lv015'] . "','" . $vrow1['lv016'] . "','" . $vrow1['lv198'] . "')";
						db_query($lvsql);
						$this->CheckChild($vrow1['lv003'], $vrow['lv099']);
						$vlv011 = $vrow1['lv011'];
						//$vlv011 = ($vlv011!="")?recoverdate(($vlv011), $this->lang):$this->DateDefault;
						$this->AddLotReciept($vrow1['lv014'], $vrow1['lv003'], $vrow['lv099'], $vrow1['lv006'], $vrow1['lv008'], $vrow1['lv019'], $vrow1['lv015'], $vlv011);
					}
				} else {
					echo 'loi';
				}
			}
		}
	}
	/*
	function LV_InsertLocal($lvarr)
	{
		$lvsql="select A.* from cr_lv0150 A inner join ts_lv0048 B on A.lv010=B.lv001 where B.lv003=1 and A.lv001 IN ($lvarr) and A.lv001 not in (select lv099 from ts_lv0053) and A.lv001 not in (select IF(ISNULL(lv099),'',lv099) from ts_lv0008) and lv007=1";
		$vresult=db_query($lvsql);
		while($vrow=db_fetch_array($vresult))
		{
			$vslq="insert into ts_lv0053(lv001,lv002,lv003,lv004,lv005,lv006,lv007,lv008,lv009,lv010,lv099) values('".$vrow['lv001']."','".$vrow['lv099']."','".$vrow['lv011']."','".$vrow['lv004']."','".$vrow['lv005']."','".$vrow['lv006']."','".$vrow['lv007']."','".$vrow['lv008']."','".$vrow['lv009']."','".$vrow['lv010']."','".$vrow['lv001']."')";
			if(db_query($vslq))
			{
				$lvsql="insert into ts_lv0054 (lv002,lv003,lv004,lv005,lv006,lv007,lv008,lv009,lv010,lv011,lv012,lv013,lv014,lv015,lv016) select lv002,lv003,lv004,lv005,lv006,lv007,lv008,lv009,lv010,lv011,lv012,lv013,lv014,lv015,lv016 from ts_lv0011 where lv002='".$vrow['lv001']."'";
				db_query($lvsql);
			}
		}
	}*/
	function LV_UnAproval($lvarr)
	{
		if ($this->isUnApr == 0) return false;
		$lvsql = "Update cr_lv0150 set lv007=0,lv027=0,lv028=0,lv030=0  WHERE cr_lv0150.lv001 IN ($lvarr) and lv007=1 and lv027=2 ";
		$vReturn = db_query($lvsql);
		if ($vReturn) {
			$this->InsertLogOperation($this->DateCurrent, 'cr_lv0150.unapproval', sof_escape_string($lvsql));
			$this->LV_SetHistoryArr('UnApr', $lvarr);
		}
		return $vReturn;
	}
	function LV_InsertTemp()
	{

		if ($this->isAdd == 0) return false;
		$this->lv009 = ($this->lv009 != "") ? recoverdate(($this->lv009), $this->lang) : $this->DateDefault;
		$lvsql = "insert into cr_lv0150 (lv001,lv002,lv003,lv004,lv005,lv006,lv007,lv008,lv009,lv010,lv011,lv099,lv114) values('$this->lv001','$this->lv002','$this->lv003','$this->lv004','$this->lv005','$this->lv006','$this->lv007','$this->lv008',concat('$this->lv009',' ',CURRENT_TIME()),'$this->lv010','$this->lv011','$this->lv099','$this->lv114')";
		$vReturn = db_query($lvsql);
		if ($vReturn) {
			//$this->LV_InsertOther($this->lv001);
			$this->InsertLogOperation($this->DateCurrent, 'cr_lv0150.insert', sof_escape_string($lvsql));
		}
		return $vReturn;
	}
	function LV_Exist($vlv001)
	{
		$lvsql = "select count(*) num from  cr_lv0150 Where lv001='$vlv001'";
		$vresult = db_query($lvsql);
		$vrow = db_fetch_array($vresult);
		if ($vrow) {
			if ($vrow['num'] > 0) return true;
			else return false;
		}
		return false;
	}
	//////////get view///////////////
	function GetView()
	{
		return $this->isView;
	} //////////get view///////////////
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
	function LV_GetProjectName($vPrjName)
	{
		$vStrID = '';
		$lvsql = "select distinct A.lv001 from  cr_lv0005 A inner join cr_lv0004 B on A.lv002=B.lv001  where B.lv009 like '%$vPrjName%'";
		$vresult = db_query($lvsql);
		while ($vrow = db_fetch_array($vresult)) {
			if ($vStrID == '') {
				$vStrID = "'" . $vrow['lv001'] . "'";
			} else {
				$vStrID = $vStrID . ",'" . $vrow['lv001'] . "'";
			}
		}
		if ($vStrID == '') return "''";
		return $vStrID;
	}
	function LV_GetTenKeHoach($vPrjName)
	{
		$vStrID = '';
		$lvsql = "select distinct A.lv001 from  cr_lv0005 A inner join cr_lv0004 B on A.lv002=B.lv001  where B.lv002 like '%$vPrjName%'";
		$vresult = db_query($lvsql);
		while ($vrow = db_fetch_array($vresult)) {
			if ($vStrID == '') {
				$vStrID = "'" . $vrow['lv001'] . "'";
			} else {
				$vStrID = $vStrID . ",'" . $vrow['lv001'] . "'";
			}
		}
		if ($vStrID == '') return "''";
		return $vStrID;
	}
	//////////Get Filter///////////////
	protected function GetCondition()
	{
		$strCondi = "";
		if (trim($this->TenDuAn) != "") {
			$vPrjName = $this->LV_GetProjectName(trim($this->TenDuAn));
			$strCondi = $strCondi . " and A.lv114 in ($vPrjName)";
		}
		if (trim($this->TenKH) != "") {
			$vPrjName = $this->LV_GetTenKeHoach(trim($this->TenKH));
			$strCondi = $strCondi . " and A.lv114 in ($vPrjName)";
		}
		if ($this->isAll == 0) $strCondi = $strCondi . " and B.lv042='$this->LV_UserID'";
		if ($this->lv001 != "") $strCondi = $strCondi . " and A.lv001  like '%$this->lv001%'";
		if ($this->lv002 != "") $strCondi = $strCondi . " and A.lv002  like '%$this->lv002%'";
		if ($this->lv003 != "") $strCondi = $strCondi . " and A.lv003  like '%$this->lv003%'";
		if ($this->lv004 != "") {
			if (!strpos($this->lv004, ',') === false) {
				$vArrNameCus = explode(",", $this->lv004);
				foreach ($vArrNameCus as $vNameCus) {
					if ($vNameCus != "") {
						if ($strCondi == "")
							$strCondi = " AND ( A.lv004 = '$vNameCus'";
						else
							$strCondi = $strCondi . " OR A.lv004 = '$vNameCus'";
					}
				}
				$strCondi = $strCondi . ")";
			} else {
				$strCondi = $strCondi . " and A.lv004  = '$this->lv004'";
			}
		}

		if ($this->lv005 != "") $strCondi = $strCondi . " and A.lv005  like '%$this->lv005%'";
		if ($this->lv006 != "") $strCondi = $strCondi . " and A.lv006  = '$this->lv006'";
		if ($this->lv007 != "")  $strCondi = $strCondi . " and A.lv007 like '%$this->lv007%'";
		if ($this->lv008 != "")  $strCondi = $strCondi . " and A.lv008 like '%$this->lv008%'";
		if ($this->lv009 != "")  $strCondi = $strCondi . " and A.lv009 like '%$this->lv009%'";
		if ($this->lv010 != "")  $strCondi = $strCondi . " and A.lv010 like '%$this->lv010%'";
		if ($this->lv011 != "") {
			if (!strpos($this->lv011, ',') === false) {
				$vArrNameCus = explode(",", $this->lv011);
				foreach ($vArrNameCus as $vNameCus) {
					if ($vNameCus != "") {
						if ($strCondi == "")
							$strCondi = " AND ( A.lv011 = '$vNameCus'";
						else
							$strCondi = $strCondi . " OR A.lv011 = '$vNameCus'";
					}
				}
				$strCondi = $strCondi . ")";
			} else {
				$strCondi = $strCondi . " and A.lv011  = '$this->lv002'";
			}
		}

		/*$strwh=$this->Get_WHControler();
		$strCondi=$strCondi." and lv002 in ($strwh)";
		if($this->lv806!="")  
		{
			$vListSupplier="'".str_replace(",","','",$this->lv806)."'";
			$vListPhieuMua=$this->LV_GetDonMuaHang($vListSupplier);
			$strCondi=$strCondi." and (lv006 in ($vListPhieuMua))";
		}*/
		return $strCondi;
	}
	function LV_GetDonMuaHang($vListSupplier)
	{
		$vStrReturn = '';
		$lvsql = "select lv001 from ts_lv0021 where lv002 in ($vListSupplier)";
		$vReturn = db_query($lvsql);
		while ($vrow = db_fetch_array($vReturn)) {
			if ($vStrReturn == '')
				$vStrReturn = "'" . $vrow['lv001'] . "'";
			else
				$vStrReturn = $vStrReturn . ",'" . $vrow['lv001'] . "'";
		}
		return $vStrReturn;
	}
	protected function GetConditionRpt()
	{
		$strCondi = "";
		if ($this->lv001 != "") $strCondi = $strCondi . " and A.lv001  = '$this->lv001'";
		if ($this->lv002 != "") $strCondi = $strCondi . " and A.lv002  like '%$this->lv002%'";
		if ($this->lv003 != "") $strCondi = $strCondi . " and A.lv003  like '%$this->lv003%'";
		if ($this->lv004 != "") $strCondi = $strCondi . " and A.lv004  like '%$this->lv004%'";
		if ($this->lv005 != "") $strCondi = $strCondi . " and A.lv005  like '%$this->lv005%'";
		if ($this->lv006 != "") $strCondi = $strCondi . " and A.lv006  like '%$this->lv006%'";
		if ($this->lv007 != "")  $strCondi = $strCondi . " and A.lv007 like '%$this->lv007%'";
		if ($this->lv008 != "")  $strCondi = $strCondi . " and A.lv008 like '%$this->lv008%'";
		if ($this->lv009 != "")  $strCondi = $strCondi . " and A.lv009 like '%$this->lv009%'";
		if ($this->lv010 != "")  $strCondi = $strCondi . " and A.lv010 like '%$this->lv010%'";
		if ($this->lv011 != "")  $strCondi = $strCondi . " and A.lv011 like '%$this->lv011%'";
		/*$strwh=$this->Get_WHControler();
		$strCondi=$strCondi." and lv002 in ($strwh)";*/
		return $strCondi;
	}
	protected function GetConditionMini()
	{
		$strCondi = "";
		return '';
		$strwh = $this->Get_WHControler();
		$strCondi = $strCondi . " and lv002 in ($strwh)";
		return $strCondi;
	}
	function LV_CheckData($vlv002, $vWarehourID)
	{
		$lvsql = "select A.lv004 lv004 from sl_lv0014 A inner join sl_lv0007 B on A.lv003=B.lv001  where A.lv002='$vlv002'";
		$vReturn = db_query($lvsql);
		while ($vrow = db_fetch_array($vReturn)) {
			$this->lv004 = $vrow['lv004'];
			if ($this->lv004 > 0)	return true;
		}
		return false;
	}
	////////////////Count///////////////////////////
	function GetCount()
	{
		$sqlC = "SELECT COUNT(*) AS nums FROM cr_lv0150 A inner join hr_lv0020 B on A.lv003=B.lv001 WHERE A.lv007=1 and A.lv027=2 and A.lv030=1 " . $this->GetCondition();
		$bResultC = db_query($sqlC);
		$arrRowC = db_fetch_array($bResultC);
		return $arrRowC['nums'];
	}
	function LV_GetBLMoney($vContractID, &$vlineamount2 = 0)
	{
		$vAr91 = array();
		$vAr93 = array();
		$vAr93T = array();
		$vAr91T = array();
		$vlineamount2 = 0;
		$lvsql = "select lv004,lv090,lv091,lv092,lv093  from cr_lv0151 A   where A.lv002='$vContractID'";
		$vresult = db_query($lvsql);
		$vi = 0;
		$vA91 = 0;
		while ($vrow = db_fetch_array($vresult)) {
			if ($vrow['lv091'] >= 0) {
				$vAr91[$vi] = 100;
				$vA91 = $vA91 + 100;
			} else {
				$vrow['lv091'] = 0;
				$vAr91[$vi] = $vrow['lv090'] * 100 / $vrow['lv004'];
				$vA91 = $vA91 + $vrow['lv090'] * 100 / $vrow['lv004'];
				$vAr91T = $vrow['lv091'];
			}
			if ($vrow['lv093'] >= 0) {
				$vAr93[$vi] = 100;
				$vA93 = $vA93 + 100;
			} else {
				$vrow['lv093'] = 0;
				$vAr93[$vi] = $vrow['lv092'] * 100 / $vrow['lv004'];
				$vA93 = $vA93 + $vrow['lv092'] * 100 / $vrow['lv004'];
				$vAr93T = $vrow['lv093'];
			}
			$vi++;
		}
		if ($vi == 0) {
			$vlineamount2 = 0;
			return 0;
		}
		$vlineamount2 = Round($vA93 / $vi, 2);
		return round($vA91 / $vi, 2);
	}
	function LV_GetBLBOM($vContractID, &$vlineamount2 = 0)
	{
		$vAr91 = array();
		$vAr93 = array();
		$vAr93T = array();
		$vAr91T = array();
		$vlineamount2 = 0;
		$lvsql = "select lv004,lv090,lv091,lv092,lv093  from cr_lv0214 A   where A.lv002='$vContractID'";
		$vresult = db_query($lvsql);
		$vi = 0;
		$vA91 = 0;
		while ($vrow = db_fetch_array($vresult)) {
			if ($vrow['lv091'] >= 0) {
				$vAr91[$vi] = 100;
				$vA91 = $vA91 + 100;
			} else {
				$vrow['lv091'] = 0;
				$vAr91[$vi] = $vrow['lv090'] / $vrow['lv004'];
				$vA91 = $vA91 + $vrow['lv090'] / $vrow['lv004'];
				$vAr91T = $vrow['lv091'];
			}
			if ($vrow['lv093'] >= 0) {
				$vAr93[$vi] = 100;
				$vA93 = $vA93 + 100;
			} else {
				$vrow['lv093'] = 0;
				$vAr93[$vi] = $vrow['lv092'] / $vrow['lv004'];
				$vA93 = $vA93 + $vrow['lv092'] / $vrow['lv004'];
				$vAr93T = $vrow['lv093'];
			}
			$vi++;
		}
		if ($vi == 0) {
			$vlineamount2 = 0;
			return 0;
		}
		$vlineamount2 = Round($vA93 / $vi, 2);
		return round($vA91 / $vi, 2);
	}
	//////////////////////Buil list////////////////////
	//////////////////////Buil list////////////////////
	function LV_GetCustomer()
	{
		$lvsql = "select lv001,lv002 from sl_lv0001";
		$vReturn = db_query($lvsql);
		while ($vrow = db_fetch_array($vReturn)) {
			$this->ArrCus[$vrow['lv001']] = $vrow['lv002'];
		}
		return;
	}
	function LV_GetSupplier()
	{
		$lvsql = "select lv001,lv002 from ts_lv0003";
		$vReturn = db_query($lvsql);
		while ($vrow = db_fetch_array($vReturn)) {
			$this->ArrSup[$vrow['lv001']] = $vrow['lv002'];
		}
		return;
	}
	function LV_BuilList($lvList, $lvFrom, $lvChkAll, $lvChk, $curRow, $maxRows, $paging, $lvOrderList, $lvSortNum)
	{
		if ($curRow < 0) $curRow = 0;
		if ($lvList == "") $lvList = $this->DefaultFieldList;
		if ($this->isView == 0) return false;
		$this->LV_GetCustomer();
		$this->LV_GetSupplier();
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
		$lvTable = "
		<div id=\"func_id\" style='position:relative;background:#f2f2f2'><div style=\"float:left\">" . $this->TabFunction($lvFrom, $lvList, $maxRows) . "</div><div style=\"float:right\">" . $this->ListFieldSave($lvFrom, $lvList, $maxRows, $lvOrderList, $lvSortNum) . "</div><div style='float:right'>&nbsp;&nbsp;&nbsp;</div><div style='float:right'>" . $this->ListFieldExport($lvFrom, $lvList, $maxRows) . "</div></div><div style='height:35px'></div><table  align=\"center\" class=\"lvtable\"><!--<tr ><td colspan=\"" . (2 + count($lstArr)) . "\" class=\"lvTTable\">" . $this->ArrPush[0] . "</td></tr>-->
		@#01
		@#02
		<tr ><td colspan=\"" . (count($lstArr) + 2) . "\">$paging</td></tr>
		<tr class=\"cssbold_tab\"><td colspan=\"" . (count($lstArr)) . "\">" . $this->TabFunction($lvFrom, $lvList, $maxRows) . "</td><td colspan=\"2\" align=right></td></tr>
		</table>
		";
		$lvTrH = "<tr class=\"lvhtable\">
			<td width=1% class=\"lvhtable\">" . $this->ArrPush[1] . "</td>
			<td width=1%><input name=\"$lvChkAll\" type=\"checkbox\" id=\"$lvChkAll\" onclick=\"DoChkAll($lvFrom, '$lvChk', this)\" value=\"$curRow\" tabindex=\"2\"/></td>
			@#01
		</tr>
		";
		$lvTr = "<tr class=\"lvlinehtable@01\">
			<td width=1% onclick=\"Select_Check('$lvChk@03',$lvFrom, '$lvChk', '$lvChkAll')\">@03</td>
			<td width=1%><input name=\"$lvChk\" type=\"checkbox\" id=\"$lvChk@03\" onclick=\"CheckOne($lvFrom, '$lvChk', '$lvChkAll', this)\" value=\"@02\" tabindex=\"2\"  onKeyUp=\"return CheckKeyCheck(event,2,'$lvChk',$lvFrom, '$lvChk', '$lvChkAll',@03)\"/></td>
			@#01
		</tr>
		";
		//$lvHref="<span onclick=\"ProcessTextHiden(this)\"><a href=\"javascript:FunctRunning1('@01')\" class=@#04 style=\"text-decoration:none\">@02</a></span>";
		$lvHref = "@02";
		$lvTdH = "<td width=\"@01\" class=\"lvhtable\">@02</td>";
		$lvTd = "<td  class=\"@#04\" align=\"@#05\">@02</td>";
		$lvTdF = "<td align=\"right\"><strong>@01</strong></td>";
		$strF = "<tr><td colspan=\"2\">&nbsp;</td>";
		$sqlS = "SELECT A.*,A.lv114 lv214,A.lv003 lv829,A.lv003 lv862 FROM cr_lv0150 A inner join hr_lv0020 B on A.lv003=B.lv001 WHERE A.lv007=1 and A.lv027=2 and A.lv030=1  " . $this->GetCondition() . " $strSort LIMIT $curRow, $maxRows";
		$vorder = $curRow;
		$bResult = db_query($sqlS);
		$this->Count = db_num_rows($bResult);
		$strTrH = "";
		$strH = "";
		for ($i = 0; $i < count($lstArr); $i++) {
			$vTemp = str_replace("@01", "", $lvTdH);
			$vTemp = str_replace("@02", $this->ArrPush[(int)$this->ArrGet[$lstArr[$i]]], $vTemp);
			$strH = $strH . $vTemp;
			$vTempF = str_replace("@01", "<!--" . $lstArr[$i] . "-->", $lvTdF);
			$strF = $strF . $vTempF;
		}
		while ($vrow = db_fetch_array($bResult)) {
			$strL = "";
			$vorder++;
			$vlineamount2 = 0;
			$vlineamount = $this->LV_GetBLMoney($vrow['lv001'], $vlineamount2);
			$vsumamount = $vsumamount + $vlineamount;
			$vlineamount3 = 0;
			$vlineamount4 = 0;
			$vlineamount3 = $this->LV_GetBLBOM($vrow['lv001'], $vlineamount4);
			for ($i = 0; $i < count($lstArr); $i++) {
				switch ($lstArr[$i]) {
					case 'lv199':
						$vStr1 = '';
						$vChucNang = "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\">
						<tr>
						";
						$vChucNang = $vChucNang . '<td><span onclick="ProcessTextHidenMore(this)"><a href="javascript:FunctRunning1(\'' . $vrow['lv001'] . '\')"><img style="cursor:pointer;width:25px;;padding:5px;"  alt="NoImg" src="../images/icon/work_experience.png" align="middle" border="0" name="new" class="lviconimg"></a></span></td>';
						if ($this->GetEdit() == 1 && $vrow['lv007'] == 0) {
							$vChucNang = $vChucNang . '
							<td><img Title="' . (($vrow['lv027'] == 0) ? 'Edit' : 'View') . '" style="cursor:pointer;width:25px;padding:5px;" onclick="Edit(\'' . ($vrow['lv001']) . '\')" alt="NoImg" src="../images/icon/' . (($vrow['lv027'] == 0) ? 'Edt.png' : 'detail.png') . '" align="middle" border="0" name="new" class="lviconimg"></td>
							';
						}
						if ($this->GetApr() == 1) {
							//$vChucNang=$vChucNang.'<td><input type="button" value="Duyệt" style="padding:3px;border-radius:3px;font-weight:bold;cursor:pointer;width:80px;" onclick="Approvals(\''.$vrow['lv001'].'@\')"/></td>';
							$vid = $vrow['lv001'];
							$vChucNang = $vChucNang . '
							<td><div id="' . $vid . '" style="padding:3px;white-space: nowrap;/*position:relative;*/">
							<span onclick="showghichu(\'' . $vid . '\')" style="cursor:pointer"><input type="button" value="Duyệt" style="padding:3px;border-radius:3px;font-weight:bold;cursor:pointer;"/></span>
							<div style="display:none;position:absolute;background:#f3b12b;overflow:hidden;" id="ghichu_' . $vid . '" >
								<table>
									<tr>
										<td>
											<input id="txtghichu_' . $vid . '" type="textbox" onfocus="if(this.value==\'Ghi chú\'){this.value=\'\'}" value="Ghi chú" onchange="addtime(\'' . $vlv001 . '\',\'' . $vlv002 . '\',\'' . $vid . '\',this)" style="width:190px;right:0px;top:0px;"/>
										</td>
										<td>
											<img Title="Duyệt" style="cursor:pointer;width:25px;padding:5px;" onclick="ApprovalsOk(\'' . ($vrow['lv001']) . '\')" alt="NoImg" src="../images/controlright/save_f2.png" align="middle" border="0" name="new" class="lviconimg">
										</td>
										<td>
											<img src="../images/icon/close.png"  onclick="closeghichu(\'' . $vid . '\')"/>
										</td>
									</tr>
								</table>
							</div> ' . $strReturn . '
							
							</div>
							
							</td>
							';
						}
						if ($this->GetUnApr() == 1) {
							//$vChucNang=$vChucNang.'<td><input type="button" value="Trả lại" style="padding:3px;border-radius:3px;font-weight:bold;cursor:pointer;text-align:center;width:80px;" onclick="UnApprovals(\''.$vrow['lv001'].'@\')"/></td>';							
							$vid = $vrow['lv001'];
							$vChucNang = $vChucNang . '
							<td><div id="' . $vid . '" style="padding:3px;white-space: nowrap;/*position:relative;*/">
							<span onclick="showtimeadd(\'' . $vid . '\')" style="cursor:pointer"><input type="button" value="Trả lại" style="padding:3px;border-radius:3px;font-weight:bold;cursor:pointer;"/></span>
							<div style="display:none;position:absolute;background:#f3b12b;overflow:hidden;" id="timeadd_' . $vid . '" >
								<table>
									<tr>
										<td>
											<input id="txttimeadd_' . $vid . '" type="textbox" onfocus="if(this.value==\'Ghi chú\'){this.value=\'\'}" value="Ghi chú" onchange="addtime(\'' . $vlv001 . '\',\'' . $vlv002 . '\',\'' . $vid . '\',this)" style="width:190px;right:0px;top:0px;"/>
										</td>
										<td>
											<img Title="Không duyệt" style="cursor:pointer;width:25px;padding:5px;" onclick="UnAprNoOk(\'' . ($vrow['lv001']) . '\')" alt="NoImg" src="../images/controlright/save_f2.png" align="middle" border="0" name="new" class="lviconimg">
										</td>
										<td>
											<img src="../images/icon/close.png"  onclick="closetimeadd(\'' . $vid . '\')"/>
										</td>
									</tr>
								</table>
							</div> ' . $strReturn . '
							
							</div>
							
							</td>
							';
						}
						if ($vrow['lv003'] == '3' || (strpos($vrow['lv002'], '_VTP') > 0)) {
							$vChucNang = $vChucNang . '<td><img style="cursor:pointer;height:25px;padding:5px;" onclick="Report(\'' . $vrow['lv001'] . '\')" alt="NoImg" src="../images/icon/Rpt.png" align="middle" border="0" name="new" class="lviconimg"></td>';
							//$vChucNang=$vChucNang.'<td><img style="cursor:pointer;height:32px;padding:5px;" onclick="Report10(\''.$vrow['lv001'].'\')" alt="NoImg" src="../images/controlright/contract.gif" align="middle" border="0" name="new" class="lviconimg"></td>';
						} else {
							$vChucNang = $vChucNang . '<td><img style="cursor:pointer;height:25px;padding:5px;" onclick="Report11(\'' . $vrow['lv001'] . '\')" alt="NoImg" src="../images/icon/Rpt.png" align="middle" border="0" name="new" class="lviconimg"></td>';
							//$vChucNang=$vChucNang.'<td><img style="cursor:pointer;height:32px;padding:5px;" onclick="Report12(\''.$vrow['lv001'].'\')" alt="NoImg" src="../images/controlright/contract.gif" align="middle" border="0" name="new" class="lviconimg"></td>';
						}
						/*
						<span onclick="ProcessTextHiden(this)"><a href="javascript:FunctRunning1(\''.$vrow['lv001'].'\')"><img style="cursor:pointer;width:25px;;padding:5px;"  alt="NoImg" src="../images/icon/work_experience.png" align="middle" border="0" name="new" class="lviconimg"></a></span>
						';*/
						/*$vStr='	<td>
						<div style="cursor:pointer;color:blue;" onclick="showDetailBBG(\'chitietbbgid_'.$vrow['lv001'].'\',\''.$vrow['lv001'].'\')">'.'<img style="cursor:pointer;width:25px;;padding:5px;"  alt="NoImg" src="../images/icon/job.png" title="Xem chi tiết BBG"/>'.'</div>
						<div style="display:none;position:absolute;z-index:999999999999;background:#efefef;" id="chitietbbgid_'.$vrow['lv001'].'" class="noidung_member">					
							<div class="hd_cafe" style="width:100%">
								<ul class="qlycafe" style="width:100%">
									<li style="padding:10px;"><img onclick="document.getElementById(\'chitietbbgid_'.$vrow['lv001'].'\').style.display=\'none\';" width="20" src="images/icon/close.png"/></li>
									<li style="padding:10px;"><div style="width:100%;padding-top:2px;">
									<strong>CHI TIẾT PHIẾU BÁN HÀNG:'.$vrow['lv115'].'</strong></div>
									</li>
								</ul>
							</div>
							<div id="chitietbbg_'.$vrow['lv001'].'" style="min-width:360px;overflow:hidden;">

							</div>
							<div width="100%;height:40px;">
								<center>
									<div style="width:160px;border-radius:5px;cursor:pointer;height:30px;padding-top:10px;" onclick="document.getElementById(\'chitietbbgid_'.$vrow['lv001'].'\').style.display=\'none\';">ĐÓNG LẠI</div>
								</center>
							</div>
						</div>	
					</td>
						';
						$vStr1='<td>
										<div style="cursor:pointer;color:blue;" onclick="showDetailHistory(\'chitietid_'.$vrow['lv001'].'\',\''.$vrow['lv001'].'\')">'.'<img style="cursor:pointer;width:25px;;padding:5px;"  alt="NoImg" src="../images/icon/license.png" title="Xem lịch sử duyệt"/>'.'</div>
										<div style="display:none;position:absolute;z-index:999999999999;background:#efefef;" id="chitietid_'.$vrow['lv001'].'" class="noidung_member">					
											<div class="hd_cafe" style="width:100%">
												<ul class="qlycafe" style="width:100%">
													<li style="padding:10px;"><img onclick="document.getElementById(\'chitietid_'.$vrow['lv001'].'\').style.display=\'none\';" width="20" src="images/icon/close.png"/></li>
													<li style="padding:10px;"><div style="width:100%;padding-top:2px;">
													<strong>LỊCH SỬ DUYỆT PHIẾU BÁN HÀNG:'.$vrow['lv115'].'</strong></div>
													</li>
												</ul>
											</div>
											<div id="chitietlichsu_'.$vrow['lv001'].'" style="min-width:360px;overflow:hidden;"></div>
											<div width="100%;height:40px;">
												<center>
													<div style="width:160px;border-radius:5px;cursor:pointer;height:30px;padding-top:10px;" onclick="document.getElementById(\'chitietid_'.$vrow['lv001'].'\').style.display=\'none\';">ĐÓNG LẠI</div>
												</center>
											</div>
										</div>	
									</td>
									';*/
						$vChucNang = $vChucNang . $vStr1 . $vStr;
						$vStr1 = '<td>
									<div style="cursor:pointer;color:blue;" onclick="showDetailHistory(\'chitietid_' . $vrow['lv001'] . '\',\'' . $vrow['lv001'] . '\')">' . '<img style="cursor:pointer;width:25px;;padding:5px;"  alt="NoImg" src="../images/icon/license.png" title="Xem lịch sử duyệt"/>' . '</div>
									<div style="display:none;position:absolute;z-index:999999999999;background:#efefef;" id="chitietid_' . $vrow['lv001'] . '" class="noidung_member">					
										<div class="hd_cafe" style="width:100%">
											<ul class="qlycafe" style="width:100%">
												<li style="padding:10px;"><img onclick="document.getElementById(\'chitietid_' . $vrow['lv001'] . '\').style.display=\'none\';" width="20" src="images/icon/close.png"/></li>
												<li style="padding:10px;"><div style="width:100%;padding-top:2px;">
												<strong>LỊCH SỬ DUYỆT ĐN CẤP VT:' . $vrow['lv0001'] . '</strong></div>
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
						$vChucNang = $vChucNang . "</tr></table>";
						$vTemp = str_replace("@02", $vChucNang, $this->Align($lvTd, (int)$this->ArrView[$lstArr[$i]]));
						break;
					case 'lv115':
						if ($vrow['lv115'] == '') {
							$vTemp = str_replace("@02", str_replace("@02", $this->getvaluelink($lstArr[$i], $this->FormatView($vrow[$lstArr[$i]], (int)$this->ArrView[$lstArr[$i]])), str_replace("@01", $vrow['lv001'], $lvHref)), $this->Align($lvTd, (int)$this->ArrView[$lstArr[$i]]));
						} else {
							$vStr1 = '';
							$vChucNang = "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\">
								<tr>
								";
							$vChucNang = $vChucNang . '<td>' . $vrow['lv115'] . '</td>';
							$vChucNang = $vChucNang . '
								<td><div style="cursor:pointer;color:blue;" onclick="showDetailBBG(\'' . $vrow['lv001'] . '\',\'' . $vrow['lv115'] . '\')">' . '<img style="cursor:pointer;width:25px;;padding:5px;"  alt="NoImg" src="../images/icon/job.png" title="Xem chi tiết BBG"/>' . '</div>
									<div style="display:none;position:absolute;z-index:999999999999;background:#efefef;" id="chitietbbgid_' . $vrow['lv001'] . '" class="noidung_member">					
										<div class="hd_cafe" style="width:100%">
											<ul class="qlycafe" style="width:100%">
												<li style="padding:10px;"><img onclick="document.getElementById(\'chitietbbgid_' . $vrow['lv001'] . '\').style.display=\'none\';" width="20" src="images/icon/close.png"/></li>
												<li style="padding:10px;"><div style="width:100%;padding-top:2px;">
												<strong>CHI TIẾT PHIẾU BÁN HÀNG:' . $vrow['lv115'] . '</strong></div>
												</li>
											</ul>
										</div>
										<div id="chitietbbg_' . $vrow['lv001'] . '" style="min-width:360px;overflow:hidden;">

										</div>
										<div width="100%;height:40px;">
											<center>
												<div style="width:160px;border-radius:5px;cursor:pointer;height:30px;padding-top:10px;" onclick="document.getElementById(\'chitietbbgid_' . $vrow['lv001'] . '\').style.display=\'none\';">ĐÓNG LẠI</div>
											</center>
										</div>
									</div>	</td>
								';
							$vChucNang = $vChucNang . "</tr></table>";
							$vTemp = str_replace("@02", $vChucNang, $this->Align($lvTd, (int)$this->ArrView[$lstArr[$i]]));
						}
						break;
					case 'lv116':
						if ($vrow['lv116'] == '') {
							$vTemp = str_replace("@02", str_replace("@02", $this->getvaluelink($lstArr[$i], $this->FormatView($vrow[$lstArr[$i]], (int)$this->ArrView[$lstArr[$i]])), str_replace("@01", $vrow['lv001'], $lvHref)), $this->Align($lvTd, (int)$this->ArrView[$lstArr[$i]]));
						} else {
							$vStr1 = '';
							$vChucNang = "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\">
								<tr>
								";
							$vChucNang = $vChucNang . '<td>' . $vrow['lv116'] . '</td>';
							$vChucNang = $vChucNang . '
								<td><img Title="Báo cáo BBNBH" style="cursor:pointer;width:25px;padding:5px;" onclick="Rpt_BaoHanh(\'' . ($vrow['lv116']) . '\')" alt="NoImg" src="../images/icon/detail.png" align="middle" border="0" name="new" class="lviconimg"></td>
								';
							$vChucNang = $vChucNang . "</tr></table>";
							$vTemp = str_replace("@02", $vChucNang, $this->Align($lvTd, (int)$this->ArrView[$lstArr[$i]]));
						}
						break;
					case 'lv110':
						$vTemp = str_replace("@02", str_replace("@02", $this->getvaluelink($lstArr[$i], $this->FormatView($vlineamount3, (int)$this->ArrView[$lstArr[$i]])) . '%', str_replace("@01", $vrow['lv001'], $lvHref)), $this->Align($lvTd, (int)$this->ArrView[$lstArr[$i]]));
						break;
					case 'lv111':
						$vTemp = str_replace("@02", str_replace("@02", $this->getvaluelink($lstArr[$i], $this->FormatView($vlineamount4, (int)$this->ArrView[$lstArr[$i]])) . '%', str_replace("@01", $vrow['lv001'], $lvHref)), $this->Align($lvTd, (int)$this->ArrView[$lstArr[$i]]));
						break;
					case 'lv112':
						$vTemp = str_replace("@02", str_replace("@02", $this->getvaluelink($lstArr[$i], $this->FormatView($vlineamount, (int)$this->ArrView[$lstArr[$i]])) . '%', str_replace("@01", $vrow['lv001'], $lvHref)), $this->Align($lvTd, (int)$this->ArrView[$lstArr[$i]]));
						break;
					case 'lv113':
						$vTemp = str_replace("@02", str_replace("@02", $this->getvaluelink($lstArr[$i], $this->FormatView($vlineamount2, (int)$this->ArrView[$lstArr[$i]])) . '%', str_replace("@01", $vrow['lv001'], $lvHref)), $this->Align($lvTd, (int)$this->ArrView[$lstArr[$i]]));
						break;
					case 'lv106':

						if ($vrow['lv005'] == 'KHACHHANG')
							$vTitle = $this->ArrCus[$vrow['lv006']];
						elseif ($vrow['lv005'] == 'NHACUNGCAP')
							$vTitle = $this->ArrSup[$vrow['lv006']];
						else
							$vTitle = '';
						$vTemp = str_replace("@02", str_replace("@02", $this->getvaluelink($lstArr[$i], $this->FormatView($vTitle, (int)$this->ArrView[$lstArr[$i]])), str_replace("@01", $vrow['lv001'], $lvHref)), $this->Align($lvTd, (int)$this->ArrView[$lstArr[$i]]));
						break;
					default:
						$vTemp = str_replace("@02", str_replace("@02", $this->getvaluelink($lstArr[$i], $this->FormatView($vrow[$lstArr[$i]], (int)$this->ArrView[$lstArr[$i]])), str_replace("@01", $vrow['lv001'], $lvHref)), $this->Align($lvTd, (int)$this->ArrView[$lstArr[$i]]));
						break;
				}
				$strL = $strL . $vTemp;
			}
			$strTr = $strTr . str_replace("@#01", $strL, str_replace("@02", $vrow['lv001'], str_replace("@03", $vorder, str_replace("@01", $vorder % 2, $lvTr))));
			if ($vrow['lv007'] == 1)		$strTr = str_replace("@#04", "lvlineapproval", $strTr);
			else $strTr = str_replace("@#04", "", $strTr);
		}
		$strF = $strF . "</tr>";
		//$strF=str_replace("<!--lv012-->",$this->FormatView($vsumamount,10),$strF);
		//$strF=str_replace("<!--lv003-->",'<p style="text-align:center;padding:5px">Tổng:</p>',$strF);
		$lvTable = str_replace("@#02", $strF, $lvTable);
		$strTrH = str_replace("@#01", $strH, $lvTrH);
		return str_replace("@#01", $strTrH . ($strTr ?? ""), $lvTable);
	}
	/////////////////////ListFieldExport//////////////////////////
	function ListFieldExport($lvFrom, $lvList, $maxRows)
	{
		if ($lvList == "") $lvList = $this->DefaultFieldList;
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
			window.open('" . $this->Dir . "cr_lv0154/?lang=" . $this->lang . "&childfunc='+value+'&ID=" . base64_encode($this->lv002) . "','','width=800,height=600,left=200,top=100,screenX=0,screenY=100,resizable=yes,status=no,scrollbars=yes,menubar=yes');
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
		if ($lvList == "") $lvList = $this->DefaultFieldList;
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

			$strTempChk = str_replace("@01", $i, $lvChk . $this->ArrPush[(int)$this->ArrGet[$lstArr[$i]]]);
			$strTempChk = str_replace("@02", $lstArr[$i], $strTempChk);

			$strTempChk = str_replace("@07", 100 + $i, $strTempChk);
			if (strpos($lvList, "," . $lstArr[$i] . ",") === FALSE) {
				$strTempChk = str_replace("@03", "", $strTempChk);
			} else {
				$strTempChk = str_replace("@03", "checked=checked", $strTempChk);
			}
			if (!isset($lvArrOrder[$i]) || $lvArrOrder[$i] === NULL || $lvArrOrder[$i] === "") {
				$strTempChk = str_replace("@06", $i, $strTempChk);
			} else
				$strTempChk = str_replace("@06", $lvArrOrder[$i], $strTempChk);


			$strTemp = str_replace("@01", $strTempChk, $lvScript);
			$strGetScript = $strGetScript . $strTemp;
		}
		$strReturn = str_replace("@#01", $strGetScript, $lvSelect) . $strScript;
		return $strReturn;
	}
	public function GetBuilCheckList($vListID, $vID, $vTabIndex)
	{
		$vListID = "," . $vListID . ",";
		$strTbl = "<table  align=\"center\" class=\"lvtable\">
		<input type=\"hidden\" id=$vID name=$vID value=\"@#02\">
		@#01
		</table>
		<script language=\"javascript\">
		function getChecked(len,nameobj,namevalue)
		{
			var str='';
			for(i=0;i<len;i++)
			{
			div = document.getElementById(nameobj+i);
			if(div.checked)
				{
				div1 = document.getElementById(namevalue+i);
				if(str=='') 
					str=(namevalue=='')?div.value:div1.value;
				else
					 str=str+','+(namevalue=='')?div.value:div1.value;
				}
			
			}
			return str;
		}
		</script>
		";
		$lvChk = "<input type=\"checkbox\" id=\"$vID@01\" value=\"@02\" @03 title=\"@04\" tabindex=\"$vTabIndex\">";
		$lvTrH = "<tr class=\"lvlinehtable1\">
			<td width=1%>@#01</td><td>@#02</td>
			
		</tr>
		";
		$vsql = "select * from  hr_lv0004";
		$strGetList = "";
		$strGetScript = "";
		$i = 0;
		$vresult = db_query($vsql);
		$numrows = db_num_rows($vresult);
		while ($vrow = db_fetch_array($vresult)) {

			$strTempChk = str_replace("@01", $i, $lvChk);
			$strTempChk = str_replace("@02", $vrow['lv001'], $strTempChk);
			if (strpos($vListID, "," . $vrow['lv001'] . ",") === FALSE) {
				$strTempChk = str_replace("@03", "", $strTempChk);
			} else {
				$strTempChk = str_replace("@03", "checked=checked", $strTempChk);
			}

			$strTempChk = str_replace("@04", $vrow['lv003'], $strTempChk);

			$strTemp = str_replace("@#01", $strTempChk, $lvTrH);
			$strTemp = str_replace("@#02", $vrow['lv002'] . "(" . $vrow['lv001'] . ")", $strTemp);
			$strGetScript = $strGetScript . $strTemp;
			$i++;
		}
		$strReturn = str_replace("@#01", $strGetScript, str_replace("@#02", $numrows, $strTbl));
		return $strReturn;
	}
	//////////////////////Buil list////////////////////
	function LV_BuilListReport($lvList, $lvFrom, $lvChkAll, $lvChk, $curRow, $maxRows, $paging, $lvOrderList)
	{

		if ($lvList == "") $lvList = $this->DefaultFieldList;
		if ($this->isView == 0) return false;
		$this->LV_GetCustomer();
		$this->LV_GetSupplier();
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
		$lvTable = "
		<div align=\"center\"><h1>" . ($this->ArrPush[0]) . "</h2></div>
		<table  align=\"center\" class=\"lvtable\" border=1>
		@#01
		@#02
		</table>
		";
		$lvTrH = "<tr class=\"lvhtable\">
			<td width=1% class=\"lvhtable\">" . $this->ArrPush[1] . "</td>
			
			@#01
		</tr>
		";
		$lvTr = "<tr class=\"lvlinehtable@01\">
			<td width=1% class=@#04>@03</td>
			@#01
		</tr>
		";
		$lvTdF = "<td align=\"@#05\"><strong>@01</strong></td>";
		$strF = "<tr><td colspan=\"1\">&nbsp;</td>";
		$lvTdH = "<td width=\"@01\" class=\"lvhtable\">@02</td>";
		$lvTd = "<td  class=\"#04\" align=\"@#05\">@02</td>";
		$sqlS = "SELECT A.*,A.lv114 lv214,A.lv003 lv829,A.lv003 lv862 FROM cr_lv0150 A inner join hr_lv0020 B on A.lv003=B.lv001 WHERE A.lv007=1 and A.lv027=2 and A.lv030=1  " . $this->RptCondition . " $strSort LIMIT $curRow, $maxRows";
		$vorder = $curRow;
		$bResult = db_query($sqlS);
		$this->Count = db_num_rows($bResult);
		$strTrH = "";
		$strH = "";
		for ($i = 0; $i < count($lstArr); $i++) {
			$vTemp = str_replace("@01", "", $lvTdH);
			$vTemp = str_replace("@02", $this->ArrPush[(int)$this->ArrGet[$lstArr[$i]]], $vTemp);
			$strH = $strH . $vTemp;
			$vTempF = str_replace("@01", "<!--" . $lstArr[$i] . "-->", $this->Align($lvTdF, (int)$this->ArrView[$lstArr[$i]]));
			$strF = $strF . $vTempF;
		}

		while ($vrow = db_fetch_array($bResult)) {
			$strL = "";
			$vorder++;
			//$vlineamount=$this->LV_GetBLMoney($vrow['lv001']);
			$vsumamount = $vsumamount + $vlineamount;
			for ($i = 0; $i < count($lstArr); $i++) {
				if ($lstArr[$i] == "lv012") {
					$vTemp = str_replace("@02", $this->getvaluelink($lstArr[$i], $this->FormatView($vlineamount, (int)$this->ArrView[$lstArr[$i]])), $this->Align($lvTd, (int)$this->ArrView[$lstArr[$i]]));
				} elseif ($lstArr[$i] == "lv106") {
					if ($vrow['lv005'] == 'KHACHHANG')
						$vTitle = $this->ArrCus[$vrow['lv006']];
					elseif ($vrow['lv005'] == 'NHACUNGCAP')
						$vTitle = $this->ArrSup[$vrow['lv006']];
					else
						$vTitle = '';
					$vTemp = str_replace("@02", $this->getvaluelink($lstArr[$i], $this->FormatView($vTitle, (int)$this->ArrView[$lstArr[$i]])), $this->Align($lvTd, (int)$this->ArrView[$lstArr[$i]]));
				} else
					$vTemp = str_replace("@02", $this->getvaluelink($lstArr[$i], $this->FormatView($vrow[$lstArr[$i]], (int)$this->ArrView[$lstArr[$i]])), $this->Align($lvTd, (int)$this->ArrView[$lstArr[$i]]));
				$strL = $strL . $vTemp;
			}


			$strTr = $strTr . str_replace("@#01", $strL, str_replace("@02", $vrow['lv001'], str_replace("@03", $vorder, str_replace("@01", $vorder % 2, $lvTr))));
			if ($vrow['lv011'] == 1)		$strTr = str_replace("@#04", "lvlineapproval", $strTr);
			else	$strTr = str_replace("@#04", "", $strTr);
		}
		$strF = $strF . "</tr>";
		$strF = str_replace("<!--lv012-->", $this->FormatView($vsumamount, 10), $strF);
		$strF = str_replace("<!--lv003-->", '<p style="text-align:center;padding:5px">Tổng:</p>', $strF);
		$lvTable = str_replace("@#02", $strF, $lvTable);
		$strTrH = str_replace("@#01", $strH, $lvTrH);
		return str_replace("@#01", $strTrH . ($strTr ?? ""), $lvTable);
	}
	//////////////////////Buil list////////////////////
	function LV_BuilListReportMini($lvList, $lvFrom, $lvChkAll, $lvChk, $curRow, $maxRows, $paging, $lvOrderList, $lvDateSort)
	{

		if ($lvList == "") $lvList = $this->DefaultFieldList;
		if ($this->isView == 0) return false;
		$this->LV_GetCustomer();
		$this->LV_GetSupplier();
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
		$lvTable = "
		<ul id=\"menu3-nav\">
			<li class=\"menusubT3\">
				<a target=\"_self\" href=\"\">
				<img style=\"position:absolute;right:0px;top:-20px\" src=\"../images/lvicon/recent_l.png\" height=\"50\" border=\"0\">
				</a>
			<ul id=\"submenu3-nav\">
				<li><table  align=\"center\" class=\"lvtable\">
		<!--<tr ><td colspan=\"" . (2 + count($lstArr)) . "\" class=\"lvTTable\">" . $this->ArrPush[0] . "</td></tr>-->
		@#01
		</table></li>
			</ul>
			</li>
		</ul>
		";
		$lvTrH = "<tr class=\"lvhtable\">
			<td width=1% class=\"lvhtable\">" . $this->ArrPush[1] . "</td>
			<td width=1%><input name=\"$lvChkAll\" type=\"checkbox\" id=\"$lvChkAll\" onclick=\"DoChkAll($lvFrom, '$lvChk', this)\" value=\"$curRow\" tabindex=\"2\"/></td>
			@#01
		</tr>
		";
		$lvTr = "<tr class=\"lvlinehtable@01\">
			<td width=1% onclick=\"Select_Check('$lvChk@03',$lvFrom, '$lvChk', '$lvChkAll')\">@03</td>
			<td width=1%><input type=\"image\" class=\"btn_img_rpt\" name=\"$lvChk\"  id=\"$lvChk@03\" onclick=\"Report('@02')\" value=\"&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\"  tabindex=\"2\"  border=\"0\"/></td>
			@#01
		</tr>
		";
		$lvTdH = "<td width=\"@01\" class=\"lvhtable\">@02</td>";
		$lvTd = "<td  class=\"#04\" align=\"@#05\">@02</td>";
		$sqlS = "SELECT A.*,A.lv114 lv214,A.lv003 lv829,A.lv003 lv862 FROM cr_lv0150 A inner join hr_lv0020 B on A.lv003=B.lv001 WHERE A.lv007=1 and A.lv027=2 and A.lv030=1 and lv009 like '$lvDateSort%' " . $this->GetConditionMini() . " $strSort LIMIT $curRow, $maxRows";
		$vorder = $curRow;
		$bResult = db_query($sqlS);
		$this->Count = db_num_rows($bResult);
		$strTrH = "";
		$strH = "";
		for ($i = 0; $i < count($lstArr); $i++) {
			$vTemp = str_replace("@01", "", $lvTdH);
			$vTemp = str_replace("@02", $this->ArrPush[(int)$this->ArrGet[$lstArr[$i]]], $vTemp);
			$strH = $strH . $vTemp;
		}
		while ($vrow = db_fetch_array($bResult)) {
			$strL = "";
			$vorder++;
			for ($i = 0; $i < count($lstArr); $i++) {
				if ($lstArr[$i] == "lv012") {
					$vTemp = str_replace("@02", $this->getvaluelink($lstArr[$i], $this->FormatView($this->LV_GetBLMoney($vrow['lv001']), (int)$this->ArrView[$lstArr[$i]])), $this->Align($lvTd, (int)$this->ArrView[$lstArr[$i]]));
				} elseif ($lstArr[$i] == "lv106") {
					if ($vrow['lv005'] == 'KHACHHANG')
						$vTitle = $this->ArrCus[$vrow['lv006']];
					elseif ($vrow['lv005'] == 'NHACUNGCAP')
						$vTitle = $this->ArrSup[$vrow['lv006']];
					else
						$vTitle = '';
					$vTemp = str_replace("@02", $this->getvaluelink($lstArr[$i], $this->FormatView($vTitle, (int)$this->ArrView[$lstArr[$i]])), $this->Align($lvTd, (int)$this->ArrView[$lstArr[$i]]));
				} else
					$vTemp = str_replace("@02", $this->getvaluelink($lstArr[$i], $this->FormatView($vrow[$lstArr[$i]], (int)$this->ArrView[$lstArr[$i]])), $this->Align($lvTd, (int)$this->ArrView[$lstArr[$i]]));
				$strL = $strL . $vTemp;
			}


			$strTr = $strTr . str_replace("@#01", $strL, str_replace("@02", $vrow['lv001'], str_replace("@03", $vorder, str_replace("@01", $vorder % 2, $lvTr))));
			if ($vrow['lv007'] == 1)		$strTr = str_replace("@#04", "lvlineapproval", $strTr);
			else	$strTr = str_replace("@#04", "", $strTr);
		}
		$strTrH = str_replace("@#01", $strH, $lvTrH);
		return str_replace("@#01", $strTrH . ($strTr ?? ""), $lvTable);
	}
	//////////////////////Buil list////////////////////
	function LV_BuilListReportOther($lvList, $lvFrom, $lvChkAll, $lvChk, $curRow, $maxRows, $paging, $lvOrderList)
	{
		if ($lvList == "") $lvList = $this->DefaultFieldList;
		$lvList = str_replace("lv199,", "", $lvList);
		if ($this->isView == 0) return false;
		$this->LV_GetCustomer();
		$this->LV_GetSupplier();
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
		$lvTable = "
		<table  align=\"center\" class=\"lvtable\" border=1 cellspacing=\"0\" cellpadding=\"0\">
		@#01
		</table>
		";
		$lvTrH = "<tr class=\"lvhtable\">			
			@#01
		</tr>
		";
		$lvTr = "<tr class=\"lvlinehtable@01\">
			
			@#01
		</tr>
		";
		$lvTdF = "<td align=\"@#05\"><strong>@01</strong></td>";
		$strF = "<tr><td colspan=\"1\">&nbsp;</td>";
		$lvTdH = "<td width=\"@01\" class=\"lvhtable\">@02</td>";
		$lvTd = "<td  class=\"#04\" align=\"@#05\">@02</td>";
		$sqlS = "SELECT A.*,A.lv114 lv214,A.lv003 lv829,A.lv003 lv862 FROM cr_lv0150 A inner join hr_lv0020 B on A.lv003=B.lv001 WHERE A.lv007=1 and A.lv027=2 and A.lv030=1  " . $this->GetConditionRpt() . " $strSort LIMIT $curRow, $maxRows";
		$vorder = $curRow;
		$bResult = db_query($sqlS);
		$this->Count = db_num_rows($bResult);
		$strTrH = "";
		$strH = "";
		for ($i = 0; $i < count($lstArr); $i++) {
			$vTemp = str_replace("@01", "", $lvTdH);
			$vTemp = str_replace("@02", $this->ArrPush[(int)$this->ArrGet[$lstArr[$i]]], $vTemp);
			$strH = $strH . $vTemp;
			$vTempF = str_replace("@01", "<!--" . $lstArr[$i] . "-->", $this->Align($lvTdF, (int)$this->ArrView[$lstArr[$i]]));
			$strF = $strF . $vTempF;
		}

		while ($vrow = db_fetch_array($bResult)) {
			$strL = "";
			$vorder++;
			//$vlineamount=$this->LV_GetBLMoney($vrow['lv001']);
			$vsumamount = $vsumamount + $vlineamount;
			for ($i = 0; $i < count($lstArr); $i++) {
				if ($lstArr[$i] == "lv012") {
					$vTemp = str_replace("@02", $this->getvaluelink($lstArr[$i], $this->FormatView($vlineamount, (int)$this->ArrView[$lstArr[$i]])), $this->Align($lvTd, (int)$this->ArrView[$lstArr[$i]]));
				} elseif ($lstArr[$i] == "lv106") {
					if ($vrow['lv005'] == 'KHACHHANG')
						$vTitle = $this->ArrCus[$vrow['lv006']];
					elseif ($vrow['lv005'] == 'NHACUNGCAP')
						$vTitle = $this->ArrSup[$vrow['lv006']];
					else
						$vTitle = '';
					$vTemp = str_replace("@02", $this->getvaluelink($lstArr[$i], $this->FormatView($vTitle, (int)$this->ArrView[$lstArr[$i]])), $this->Align($lvTd, (int)$this->ArrView[$lstArr[$i]]));
				} else
					$vTemp = str_replace("@02", $this->getvaluelink($lstArr[$i], $this->FormatView($vrow[$lstArr[$i]], (int)$this->ArrView[$lstArr[$i]])), $this->Align($lvTd, (int)$this->ArrView[$lstArr[$i]]));
				$strL = $strL . $vTemp;
			}


			$strTr = $strTr . str_replace("@#01", $strL, str_replace("@02", $vrow['lv001'], str_replace("@03", $vorder, str_replace("@01", $vorder % 2, $lvTr))));
			if ($vrow['lv011'] == 1)		$strTr = str_replace("@#04", "", $strTr);
		}
		$strF = $strF . "</tr>";
		$strF = str_replace("<!--lv012-->", $this->FormatView($vsumamount, 10), $strF);
		$strF = str_replace("<!--lv003-->", '<p style="text-align:center;padding:5px">Tổng:</p>', $strF);
		$lvTable = str_replace("@#02", $strF, $lvTable);
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
				//$strwh=$this->Get_WHControler();
				$vsql = "select lv001,lv003 lv002,IF(lv001='$vSelectID',1,0) lv003 from  ts_lv0001 where  lv001 in ('0','1','2','3','4')";
				break;
			case 'lv003':
				$vsql = "select lv001,lv002,IF(lv001='$vSelectID',1,0) lv003 from  hr_lv0020";
				break;
			case 'lv005':
				$vsql = "select lv001,lv002,IF(lv001='$vSelectID',1,0) lv003 from  cr_lv0039";
				break;
			case 'lv008':
				$vsql = "select lv001,lv002,IF(lv001='$vSelectID',1,0) lv003 from  hr_lv0020";
				break;
			case 'lv010':
				$vsql = "select lv001,lv002,IF(lv001='$vSelectID',1,0) lv003 from  ts_lv0048";
				break;
			case 'lv011':
				$vsql = "select lv001,lv002,IF(lv001='$vSelectID',1,0) lv003 from  hr_lv0020";
				break;
			case 'lv911':
				$vsql = "select B.lv001,concat(B.lv004,B.lv003,B.lv002) lv002,IF(B.lv001='$vSelectID',1,0) lv003 from  ts_lv0034 A inner join lv_lv0007 C on C.lv001=A.lv002 inner join hr_lv0020 B on C.lv006=B.lv001   where A.lv003='$vSelectID'";
				break;
			case 'lv099':
				$vsql = "select lv001,lv003 lv002,IF(lv001='$vSelectID',1,0) lv003 from  ts_lv0001";
				break;
			case 'lv114':
				if ($this->PlanID == '')
					$vsql = "select lv001,concat(lv004,' ',DATE_FORMAT(lv005,'%d/%m/%Y %H:%i')) lv002,IF(lv001='$vSelectID',1,0) lv003 from  cr_lv0005 where lv003='ĐNVT' and lv011='1'";
				else
					$vsql = "select lv001,concat(lv004,' ',DATE_FORMAT(lv005,'%d/%m/%Y %H:%i')) lv002,1 lv003 from  cr_lv0005 where lv003='ĐNVT' and lv011='1' and lv002='$this->PlanID'";
				break;
		}
		return $vsql;
	}
	public  function getvaluelink($vFile, $vSelectID)
	{
		if (!empty($this->ArrGetValueLink[$vFile][$vSelectID][0] ?? null)) {
			return $this->ArrGetValueLink[$vFile][$vSelectID][1] ?? null;
		}
		if ($vSelectID == "") {
			return $vSelectID;
		}
		switch ($vFile) {
			case 'lv002':
				$vsql = "select lv001,lv003 lv002,IF(lv001='$vSelectID',1,0) lv003 from  ts_lv0001 where lv001='$vSelectID'";
				break;
			case 'lv029':
				$vsql = "select lv001,lv002,IF(lv001='$vSelectID',1,0) lv003 from  cr_lv0153 where lv001='$vSelectID'";
				break;
			case 'lv003':
				$vsql = "select lv001,lv002,IF(lv001='$vSelectID',1,0) lv003 from  hr_lv0020 where lv001='$vSelectID'";
				break;
			case 'lv005':
				$vsql = "select lv001,lv002,IF(lv001='$vSelectID',1,0) lv003 from  cr_lv0039 where lv001='$vSelectID'";
				break;
			case 'lv008':
				$vsql = "select lv001,lv002,IF(lv001='$vSelectID',1,0) lv003 from  hr_lv0020 where lv001='$vSelectID'";
				break;
			case 'lv011':
				$vsql = "select lv001,lv002,IF(lv001='$vSelectID',1,0) lv003 from  hr_lv0020 where lv001='$vSelectID'";
				break;
			case 'lv007':
				$vsql = "select lv001,lv002,IF(lv001='$vSelectID',1,0) lv003 from (select 0 lv001,'Mở khóa' lv002 union select 1 lv001,'Khóa' lv002) MP 	 where MP.lv001='$vSelectID'";
				break;
			case 'lv099':
				$vsql = "
						select lv001,lv003 lv002,IF(lv001='$vSelectID',1,0) lv003 from  ts_lv0001 where lv001='$vSelectID'
					UNION
						select lv001,lv003 lv002,IF(lv001='$vSelectID',1,0) lv003 from  hr_lv0002 where lv001='$vSelectID'
				";
				break;
			case 'lv114':
				$vsql = "select A.lv001,B.lv009 lv002,'' lv003 from  cr_lv0005 A inner join cr_lv0004 B on A.lv002=B.lv001 where  A.lv001='$vSelectID'";
				break;
			case 'lv214':
				$vsql = "select A.lv001,B.lv002 lv002,'' lv003 from  cr_lv0005 A inner join cr_lv0004 B on A.lv002=B.lv001 where  A.lv001='$vSelectID'";
				break;
			case 'lv117':
				$vsql = "select lv001,lv002,IF(lv001='$vSelectID',1,0) lv003 from  hr_lv0020 where lv001='$vSelectID'";
				break;
			case 'lv829':
				$vsql = "select A.lv001,B.lv003 lv002,IF(A.lv001='$vSelectID',1,0) lv003 from  hr_lv0020 A inner join hr_lv0002 B on A.lv029=B.lv001 where A.lv001='$vSelectID'";
				break;
			case 'lv862':
				$vsql = "select lv001,lv062 lv002,IF(lv001='$vSelectID',1,0) lv003 from  hr_lv0020 where lv001='$vSelectID'";
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
		while ($row = db_fetch_array($lvResult)) {
			$this->ArrGetValueLink[$vFile][$vSelectID][1] = ($lvopt == 0) ? $row['lv002'] : (($lvopt == 1) ? $row['lv001'] . "(" . $row['lv002'] . ")" : (($lvopt == 2) ? $row['lv002'] . "(" . $row['lv001'] . ")" : $row['lv001']));
			return $this->ArrGetValueLink[$vFile][$vSelectID][1];
		}
	}
}