<?php
/////////////coding cr_lv0330///////////////
class cr_lv0330 extends lv_controler
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
	public $lv012 = null;
	public $lv013 = null;
	public $lv014 = null;

	///////////
	public $DefaultFieldList = "lv199,lv001,lv114,lv214,lv283,lv002,lv069,lv115,lv316,lv318,lv360,lv361,lv362,lv363,lv370,lv371,lv364,lv365,lv366,lv367,lv368,lv369,lv353,lv013,lv011,lv010,lv012,lv014,lv015,lv016,lv006,lv017,lv004,lv005";
	////////////////////GetDate
	public $DateDefault = "1900-01-01";
	public $DateCurrent = "1900-01-01";
	public $Count = null;
	public $paging = null;
	public $lang = null;
	protected $objhelp = 'cr_lv0330';
	////////////
	var $ArrOther = array();
	var $ArrPush = array();
	var $ArrFunc = array();
	var $ArrGet = array("lv001" => "2", "lv002" => "3", "lv003" => "4", "lv004" => "5", "lv005" => "6", "lv006" => "7", "lv007" => "8", "lv008" => "9", "lv009" => "10", "lv010" => "11", "lv011" => "12", "lv012" => "13", "lv013" => "14", "lv014" => "15", "lv015" => "16", "lv016" => "17", "lv353" => "354", "lv360" => "361", "lv361" => "362", "lv362" => "363", "lv363" => "364", "lv364" => "365", "lv365" => "366", "lv366" => "367", "lv367" => "368", "lv368" => "369", "lv369" => "370", "lv114" => "115", "lv115" => "116", "lv214" => "215", "lv199" => "200", "lv283" => "284", "lv370" => "371", "lv371" => "372", "lv017" => "18", "lv027" => "28", "lv316" => "317", "lv318" => "319", "lv069" => "70");
	var $ArrView = array("lv001" => "0", "lv002" => "0", "lv003" => "0", "lv004" => "22", "lv005" => "22", "lv006" => "0", "lv007" => "0", "lv008" => "0", "lv009" => "0", "lv010" => "0", "lv011" => "2", "lv012" => "0", "lv013" => "2", "lv014" => "0", "lv015" => "0", "lv016" => "0", "lv353" => "2", "lv360" => "10", "lv361" => "0", "lv362" => "10", "lv363" => "0", "lv364" => "10", "lv365" => "0", "lv366" => "10", "lv367" => "0", "lv368" => "10", "lv369" => "0", "lv114" => "0", "lv115" => "0", "lv214" => "0", "lv370" => "10", "lv371" => "0", "lv017" => "0", "lv027" => "0");

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
		$vlv912 = $this->Get_User($_SESSION['ERPSOFV2RUserID'], 'lv912');
		if ($vlv912 == 0) {
			$this->ArrView['lv026'] = '3';
			$this->ArrView['lv012'] = '3';
			$this->ArrView['lv013'] = '3';
			$this->ArrView['lv099'] = '3';
		}
	}
	function LV_LoadMau($vID)
	{
		if ($this->isView == 0)
			return false;
		$lvsql = "select lv199 from cr_lv0330_rpt Where lv001='$vID'";
		$vresult = db_query($lvsql);
		$vrow = db_fetch_array($vresult);
		if ($vrow) {
			return $vrow['lv199'];
		}
		return '';
	}
	function LV_LoadMauNguon($vID)
	{
		if ($this->isView == 0)
			return false;
		$lvsql = "select lv003 from  cr_lv0328 Where lv001='$vID'";
		$vresult = db_query($lvsql);
		$vrow = db_fetch_array($vresult);
		if ($vrow) {
			return $vrow['lv003'];
		}
		return '';
	}
	function LV_UpdateMau($vID, $vStrMau)
	{
		if ($this->isAdd == 0)
			return false;
		$lvsql = "select lv001 from  cr_lv0330_rpt Where lv001='$vID'";
		$vresult = db_query($lvsql);
		$vrow = db_fetch_array($vresult);
		if ($vrow) {
			if ($vrow['lv001'] != '' && $vrow['lv001'] != null) {
				$vSql = "update cr_lv0330_rpt set lv199='" . sof_escape_string($vStrMau) . "' where lv001='$vID'";
			} else {
				$vSql = "insert into cr_lv0330_rpt(lv001,lv199) values('$vID','" . sof_escape_string($vStrMau) . "')";
			}
		} else {
			$vSql = "insert into cr_lv0330_rpt(lv001,lv199) values('$vID','" . sof_escape_string($vStrMau) . "')";
		}
		$vresult = db_query($vSql);
		return $vresult;
	}
	function LV_Load()
	{
		$vsql = "select * from  cr_lv0330";
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
			$this->lv014 = $vrow['lv014'];
			$this->lv015 = $vrow['lv015'];
			$this->lv016 = $vrow['lv016'];
			$this->lv017 = $vrow['lv017'];
			$this->lv027 = $vrow['lv027'];

			$this->lv353 = $vrow['lv353'];
			$this->lv360 = $vrow['lv360'];
			$this->lv361 = $vrow['lv361'];
			$this->lv362 = $vrow['lv362'];
			$this->lv363 = $vrow['lv363'];
			$this->lv364 = $vrow['lv364'];
			$this->lv365 = $vrow['lv365'];
			$this->lv366 = $vrow['lv366'];
			$this->lv367 = $vrow['lv367'];
			$this->lv368 = $vrow['lv368'];
			$this->lv369 = $vrow['lv369'];
			$this->lv370 = $vrow['lv370'];
			$this->lv371 = $vrow['lv371'];

			$this->lv114 = $vrow['lv114'];
			$this->lv115 = $vrow['lv115'];
		}
	}
	function LV_LoadSupID($vlv001)
	{
		$lvsql = "select * from  cr_lv0330 Where lv001='$vlv001'";
		$vresult = db_query($lvsql);
		$vrow = db_fetch_array($vresult);
		if ($vrow) {
			$this->lv001 = $vrow['lv001'];
			$this->lv002 = $vrow['lv002'];

		}
	}
	function LV_LoadID($vlv001)
	{
		$lvsql = "select * from  cr_lv0330 Where lv001='$vlv001'";
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
			$this->lv016 = $vrow['lv016'];
			$this->lv017 = $vrow['lv017'];
			$this->lv027 = $vrow['lv027'];

			$this->lv353 = $vrow['lv353'];
			$this->lv360 = $vrow['lv360'];
			$this->lv361 = $vrow['lv361'];
			$this->lv362 = $vrow['lv362'];
			$this->lv363 = $vrow['lv363'];
			$this->lv364 = $vrow['lv364'];
			$this->lv365 = $vrow['lv365'];
			$this->lv366 = $vrow['lv366'];
			$this->lv367 = $vrow['lv367'];
			$this->lv368 = $vrow['lv368'];
			$this->lv369 = $vrow['lv369'];
			$this->lv370 = $vrow['lv370'];
			$this->lv371 = $vrow['lv371'];
			$this->lv114 = $vrow['lv114'];
			$this->lv115 = $vrow['lv115'];
		}
	}
	function LV_LoadPBHID($vlv001)
	{
		$lvsql = "select * from  cr_lv0330 Where lv089='$vlv001'";
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
			$this->lv016 = $vrow['lv016'];
			$this->lv017 = $vrow['lv017'];
			$this->lv027 = $vrow['lv027'];

			$this->lv353 = $vrow['lv353'];
			$this->lv360 = $vrow['lv360'];
			$this->lv361 = $vrow['lv361'];
			$this->lv362 = $vrow['lv362'];
			$this->lv363 = $vrow['lv363'];
			$this->lv364 = $vrow['lv364'];
			$this->lv365 = $vrow['lv365'];
			$this->lv366 = $vrow['lv366'];
			$this->lv367 = $vrow['lv367'];
			$this->lv368 = $vrow['lv368'];
			$this->lv369 = $vrow['lv369'];
			$this->lv370 = $vrow['lv370'];
			$this->lv371 = $vrow['lv371'];
			$this->lv114 = $vrow['lv114'];
			$this->lv115 = $vrow['lv115'];
		}

	}
	function LV_XuLyQuiDoiDemToi($vDate, $vSoDem, $vDonVi)
	{
		switch ($vDonVi) {
			case 'nam':
				$vDay = getday($vDate);
				$vMonth = getmonth($vDate);
				$vYear = getyear($vDate);
				return ($vYear + $vSoDem) . '-' . $vMonth . '-' . $vDay;
				break;
			case 'thang':
				$vDay = getday($vDate);
				$vMonth = getmonth($vDate);
				$vYear = getyear($vDate);
				if (($vSoDem + $vMonth) > 12) {
					$vSoNam = (int) ($vSoDem + $vMonth - 1) / 12;
					$vSoThang = ($vSoDem + $vMonth) % 12;
					if ($vSoThang == 0)
						$vSoThang = 12;
					return ($vYear) . '-' . Fillnum($vSoThang, 2) . '-' . $vDay;
				} else
					return ($vYear) . '-' . Fillnum($vSoDem + $vMonth, 2) . '-' . $vDay;
				break;
			default:
				return ADDDATE($vDate, $vSoDem);
				break;
		}
	}
	function LV_XuLyQuiDoi($vDonVi)
	{
		if ($this->ArrDV[$vDonVi][0])
			return $this->ArrDV[$vDonVi][1];
		$this->ArrDV[$vDonVi][0] = true;
		$this->ArrDV[$vDonVi][1] = 1;
		$lvsql = "select lv003 from  cr_lv0330 Where lv001='$vDonVi'";
		$vresult = db_query($lvsql);
		while ($vrow = db_fetch_array($vresult)) {
			$this->ArrDV[$vDonVi][1] = $vrow['lv003'];
			if ($this->ArrDV[$vDonVi][1] == 0)
				$this->ArrDV[$vDonVi][1] = 1;
		}
		return $this->ArrDV[$vDonVi][1];
	}
	function LV_LoadIDAmount($vlv001)
	{
		$lvsql = "select * from  cr_lv0330 Where lv001='$vlv001'";
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
			$this->lv016 = $vrow['lv016'];
			$this->lv017 = $vrow['lv017'];
			$this->lv027 = $vrow['lv027'];

			$this->lv353 = $vrow['lv353'];
			$this->lv360 = $vrow['lv360'];
			$this->lv361 = $vrow['lv361'];
			$this->lv362 = $vrow['lv362'];
			$this->lv363 = $vrow['lv363'];
			$this->lv364 = $vrow['lv364'];
			$this->lv365 = $vrow['lv365'];
			$this->lv366 = $vrow['lv366'];
			$this->lv367 = $vrow['lv367'];
			$this->lv368 = $vrow['lv368'];
			$this->lv369 = $vrow['lv369'];
			$this->lv370 = $vrow['lv370'];
			$this->lv371 = $vrow['lv371'];
			$this->lv114 = $vrow['lv114'];
			$this->lv115 = $vrow['lv115'];
		}
	}
	function LV_Insert()
	{
		if ($this->isAdd == 0)
			return false;
		$this->lv004 = ($this->lv004 != "") ? recoverdate(($this->lv004), $this->lang) : $this->DateDefault;
		$this->lv005 = ($this->lv005 != "") ? recoverdate(($this->lv005), $this->lang) . " " . gettime($this->lv005) : $this->DateDefault;
		$this->lv013 = ($this->lv013 != "") ? recoverdate(($this->lv013), $this->lang) : $this->DateDefault;
		$this->lv353 = ($this->lv353 != "") ? recoverdate(($this->lv353), $this->lang) : $this->DateDefault;
		$lvsql = "insert into cr_lv0330 (lv001,lv002,lv003,lv004,lv005,lv006,lv007,lv008,lv009,lv010,lv011,lv353,lv360,lv361,lv362,lv363,lv364,lv365,lv366,lv367,lv368,lv369,lv370,lv371,lv114) values('$this->lv001','$this->lv002','$this->lv003',concat('$this->lv004',' ',CURRENT_TIME()),concat('$this->lv005',' ',CURRENT_TIME()),'$this->lv006','$this->lv007','$this->lv008','$this->lv009','$this->lv010','$this->lv011','$this->lv353','$this->lv360','$this->lv361','$this->lv362','$this->lv363','$this->lv364','$this->lv365','$this->lv366','$this->lv367','$this->lv368','$this->lv369','$this->lv370','$this->lv371','$this->lv114')";
		$vReturn = db_query($lvsql);
		if ($vReturn) {
			$this->InsertLogOperation($this->DateCurrent, 'cr_lv0330.insert', sof_escape_string($lvsql));
			//$this->LV_XuLyCopyChungTu($this->lv001);
		}
		return $vReturn;
	}
	function LV_InsertPMH()
	{
		if ($this->isAdd == 0)
			return false;
		//$this->lv004 = ($this->lv004!="")?recoverdate(($this->lv004), $this->lang):$this->DateDefault;
		//$this->lv005 = ($this->lv005!="")?recoverdate(($this->lv005), $this->lang)." ".gettime($this->lv005):$this->DateDefault;
		$lvsql = "insert into cr_lv0330 (lv001,lv002,lv003,lv004,lv005,lv006,lv007,lv008,lv009,lv010,lv011,lv353,lv360,lv361,lv362,lv363,lv364,lv365,lv366,lv367,lv368,lv369,lv370,lv371,lv114) values('$this->lv001','$this->lv002','$this->lv003',concat('$this->lv004',' ',CURRENT_TIME()),concat('$this->lv005',' ',CURRENT_TIME()),'$this->lv006','$this->lv007','$this->lv008','$this->lv009','$this->lv010','$this->lv011','$this->lv353','$this->lv360','$this->lv361','$this->lv362','$this->lv363','$this->lv364','$this->lv365','$this->lv366','$this->lv367','$this->lv368','$this->lv369','$this->lv370','$this->lv371','$this->lv114')";
		$vReturn = db_query($lvsql);
		if ($vReturn) {
			$this->InsertLogOperation($this->DateCurrent, 'cr_lv0330.insert', sof_escape_string($lvsql));
			//$this->LV_XuLyCopyChungTu($this->lv001);
		}
		return $vReturn;
	}
	function LV_XuLyCopyChungTu($vGMH)
	{
		$lvsql = "insert into cr_lv0171 (lv002,lv003,lv004,lv005,lv006,lv007,lv008,lv009,lv010,lv011,lv012,lv013,lv014,lv015,lv016,lv017,lv018,lv019) 
		select '$vGMH' lv002,lv003,lv004,lv005,lv006,lv007,lv008,'$this->LV_UserID' lv009,now() lv010,lv011,lv012,lv013,lv014,lv015,lv016,lv017,lv018,lv019 from  cr_lv0308";
		$vReturn = db_query($lvsql);
		if ($vReturn) {
			$this->InsertLogOperation($this->DateCurrent, 'cr_lv0171.insert', sof_escape_string($lvsql));
		}

	}
	function LV_Update()
	{
		if ($this->isEdit == 0)
			return false;
		$this->lv004 = ($this->lv004 != "") ? recoverdate(($this->lv004), $this->lang) : $this->DateDefault;
		$this->lv005 = ($this->lv005 != "") ? recoverdate(($this->lv005), $this->lang) . " " . gettime($this->lv005) : $this->DateDefault;
		$this->lv013 = ($this->lv013 != "") ? recoverdate(($this->lv013), $this->lang) : $this->DateDefault;
		$this->lv353 = ($this->lv353 != "") ? recoverdate(($this->lv353), $this->lang) : $this->DateDefault;

		$lvsql = "Update cr_lv0330 set lv002='$this->lv002',lv003='" . sof_escape_string($this->lv003) . "',lv004=concat('$this->lv004',' ',CURRENT_TIME()),lv005=concat('$this->lv005',' ',CURRENT_TIME()),lv006='$this->lv006',lv007='$this->lv007',lv008='$this->lv008',lv009='$this->lv009',lv010='$this->lv010',lv011='$this->lv011',lv012='" . sof_escape_string($this->lv012) . "',lv013='$this->lv013',lv014='$this->lv014',lv015='$this->lv015',lv016='$this->lv016',lv353='$this->lv353',lv360='$this->lv360',lv361='$this->lv361',lv362='$this->lv362',lv363='$this->lv363',lv364='$this->lv364',lv365='$this->lv365',lv366='$this->lv366',lv367='$this->lv367',lv368='$this->lv368',lv369='$this->lv369',lv370='$this->lv370',lv371='$this->lv371' where  lv001='$this->lv001' AND lv006<=0;";
		$vReturn = db_query($lvsql);
		if ($vReturn) {
			$this->InsertLogOperation($this->DateCurrent, 'cr_lv0330.update', sof_escape_string($lvsql));
		}
		return $vReturn;
	}
	function LV_Delete($lvarr)
	{
		if ($this->isDel == 0)
			return false;
		$lvsql = "DELETE FROM cr_lv0330  WHERE cr_lv0330.lv006<=0 AND cr_lv0330.lv001 IN ($lvarr)  ";
		$vReturn = db_query($lvsql);
		if ($vReturn) {
			$this->InsertLogOperation($this->DateCurrent, 'cr_lv0330.delete', sof_escape_string($lvsql));

		}
		return $vReturn;
	}
	function LV_DeletePBH($vID)
	{
		if ($this->isDel == 0)
			return false;
		$lvsql = "DELETE FROM cr_lv0330  WHERE cr_lv0330.lv017<=0 AND cr_lv0330.lv001 ='$vID' ";
		$vReturn = db_query($lvsql);
		if ($vReturn) {
			$this->InsertLogOperation($this->DateCurrent, 'cr_lv0330.delete', sof_escape_string($lvsql));

		}
		return $vReturn;
	}
	function LV_NhanBaoHanh($lvarr)
	{
		if ($this->isApr == 0)
			return false;
		$lvsql1 = "select lv001 from cr_lv0330 where cr_lv0330.lv001 IN ($lvarr)  and lv006=1";
		$bResult = db_query($lvsql1);
		while ($vrow = db_fetch_array($bResult)) {
			$lvsql = "Update cr_lv0330 set lv017=1,lv003='$this->LV_UserID',lv009=now()  where lv001 ='" . $vrow['lv001'] . "' and lv006=1 and lv017=0";
			$vReturn = db_query($lvsql);
			if ($vReturn) {
				$this->InsertLogOperation($this->DateCurrent, 'cr_lv0330.approval', sof_escape_string($lvsql));
				$this->LV_SetHistoryArr('NhanBH', $vrow['lv001']);
			}
		}
	}
	function LV_Aproval($lvarr)
	{
		if ($this->isApr == 0)
			return false;
		$lvsql = "Update cr_lv0330 set lv006=1 WHERE cr_lv0330.lv001 IN ($lvarr)";
		$vReturn = db_query($lvsql);
		if ($vReturn) {
			$this->InsertLogOperation($this->DateCurrent, 'cr_lv0330.approval', sof_escape_string($lvsql));
			$this->LV_SetHistoryArr('Apr', $lvarr);
		}
		return $vReturn;
	}
	function LV_UnAproval($lvarr)
	{
		if ($this->isApr == 0)
			return false;
		$lvsql = "Update cr_lv0330 set lv006=0,lv017=0,lv027=0  WHERE cr_lv0330.lv001 IN ($lvarr) ";
		$vReturn = db_query($lvsql);
		if ($vReturn) {
			$this->InsertLogOperation($this->DateCurrent, 'cr_lv0330.unapproval', sof_escape_string($lvsql));
			$this->LV_SetHistoryArr('UnApr', $lvarr);
		}
		return $vReturn;
	}
	//History 
	function LV_SetHistoryArr($vFun, $lvarr)
	{
		$vArr = explode(",", $lvarr);
		foreach ($vArr as $vLongTermID) {
			$vLongTermID = str_replace("'", "", $vLongTermID);
			if ($vLongTermID != '')
				$this->LV_SetHistory($vFun, $vLongTermID);
		}
	}
	//Log
	function LV_SetHistory($vFun, $vLongTermID)
	{
		$vTitle = '';
		switch ($vFun) {
			case 'Apr':
				$vTitle = "ĐNBH nhập bảo hành!";
				break;
			case 'UnApr':
				$vTitle = "ĐNBH thì bị huỷ!";
				break;
			case 'NhanBH':
				$vTitle = "ĐNBH nhận bảo hành!";
				break;
			default:
				break;
		}
		//sl_lv0329
		if ($vTitle != '') {
			$lvsql = "insert into cr_lv0390 (lv002,lv003,lv004,lv005,lv006,lv007) values('" . $vLongTermID . "','" . sof_escape_string($vTitle) . "','$this->LV_UserID',now(),'$vFun',0)";
			$vReturn = db_query($lvsql);
			if ($vReturn) {
				$this->InsertLogOperation($this->DateCurrent, 'cr_lv0390.insert', sof_escape_string($lvsql));
				if ($vFun == 'UnApr') {
					$lvsql = "update cr_lv0390 set lv008=lv008+1 where lv002='$vLongTermID'";
					$vReturn = db_query($lvsql);
					if ($vReturn) {
						$this->InsertLogOperation($this->DateCurrent, 'cr_lv0390.update', sof_escape_string($lvsql));
					}
				}
			}
		}
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
		if ($vStrID == '')
			return "''";
		return $vStrID;
	}
	//////////Get Filter///////////////
	function LV_GetPlanWorkThis($vPlanID)
	{
		$vCodeID = '';
		$sqlS = "select lv001 from  cr_lv0005 where lv003='BHPBH' and lv002='$vPlanID'";
		$bResult = db_query($sqlS);
		while ($vrow = db_fetch_array($bResult)) {
			if ($vCodeID != '') {
				$vCodeID = $vCodeID . ",'" . $vrow['lv001'] . "'";
			} else {
				$vCodeID = "'" . $vrow['lv001'] . "'";
			}
		}
		return $vCodeID;
	}
	protected function GetCondition()
	{
		$strCondi = "";
		if ($this->lv001 != "")
			$strCondi = $strCondi . " and lv001  like '%$this->lv001%'";
		if ($this->PLanID != '') {
			$vPLanID = $this->LV_GetPlanWorkThis($this->PLanID);
			if ($vPLanID != '') {
				$strCondi = $strCondi . " and ( lv114  in ( $vPLanID))";
			} else {
				$strCondi = $strCondi . " and 1=0";
				return $strCondi;
			}
		}
		if ($this->lv002 != "") {
			if (!strpos($this->lv002, ',') === false) {
				$vArrName = explode(",", $this->lv002);
				foreach ($vArrName as $vName) {
					if ($vName != "") {
						if ($strCondi == "")
							$strCondi = " AND ( lv002 = '$vName'";
						else
							$strCondi = $strCondi . " OR lv002 = '$vName'";
					}
				}
				$strCondi = $strCondi . ")";

			} else {
				$strCondi = $strCondi . " and lv002  like '%$this->lv002%'";
			}
		}
		if ($this->lv003 != "")
			$strCondi = $strCondi . " and lv003  like '%$this->lv003%'";
		if ($this->lv004 != "")
			$strCondi = $strCondi . " and lv004  like '%$this->lv004%'";
		if ($this->lv005 != "")
			$strCondi = $strCondi . " and lv005  like '%$this->lv005%'";
		if ($this->lv006 . '' != "")
			$strCondi = $strCondi . " and lv006  = '$this->lv006'";
		if ($this->lv007 != "")
			$strCondi = $strCondi . " and lv007 like '%$this->lv007%'";
		if ($this->lv008 != "")
			$strCondi = $strCondi . " and lv008 like '%$this->lv008%'";
		if ($this->lv010 . '' != "")
			$strCondi = $strCondi . " and lv010  = '$this->lv010'";
		if ($this->lv017 . '' != "") {
			if ($this->lv017 == '-1') {
				$strCondi = $strCondi . " and lv006=0 and lv017=0";
			} elseif ($this->lv017 == '0') {
				$strCondi = $strCondi . " and lv006= 1 and lv017=0";
			} else
				$strCondi = $strCondi . " and lv017  = '$this->lv017'";
		}
		if ($this->lv115 != "") {
			if (!strpos($this->lv115, ',') === false) {
				$vArrName = explode(",", $this->lv115);
				foreach ($vArrName as $vName) {
					if ($vName != "") {
						if ($strCondi == "")
							$strCondi = " AND ( lv115 = '$vName'";
						else
							$strCondi = $strCondi . " OR lv115 = '$vName'";
					}
				}
				$strCondi = $strCondi . ")";

			} else {
				$strCondi = $strCondi . " and lv115  like '%$this->lv115%'";
			}
		}
		return $strCondi;
	}
	function LV_GetJob()
	{
		$lvsql = "select A.lv001 from wh_lv0022 A inner join cr_lv0330 B on A.lv002=B.lv001 where B.lv001='$vquotationid'";
		$vresult1 = db_query($lvsql);
		while ($vrow = db_fetch_array($vresult1)) {
		}
	}
	////////Quotation///////////
	function LV_PushQuotation($vquotationid)
	{
		$lvarr = '';
		//Chi tiết báo giá
		$lvsql = "select A.lv001 from cr_lv0376 A inner join sl_lv0010 B on A.lv002=B.lv001 where B.lv014='$vquotationid'";
		$vresult1 = db_query($lvsql);
		while ($vrow = db_fetch_array($vresult1)) {
			//$vAmount=$vrow['lv053'];
			//$vAmount=($vAmount-$vAmount*$vrow['CK_Lan1']/100);
			//$vAmount=$this->LV_GetPercent($vquotationid,$vAmount);
			//Attached files
			$lvsql = "insert into wh_lv0032(lv002,lv014,lv015,lv016,lv017,lv018,lv019,lv020,lv021,lv022,lv023,lv024,lv025,lv026,lv027,lv028,lv029,lv030,lv031,lv032,lv033,lv034,lv035,lv036,lv037,lv038,lv039,lv040,lv041,lv042,lv043,lv044,lv045,lv046,lv047,lv048,lv049,lv050,lv057,lv058,lv059,lv060,lv080,lv081,lv082,lv083,lv084,lv085,lv086,lv087,lv088,lv110,lv111,lv112,lv113,lv114,lv115,lv116,lv117,lv118,lv119,lv120,lv121,lv122,lv123,lv124,lv125,lv126,lv127,lv128,lv129,lv130,lv131,lv132,lv133,lv134,lv135,lv003,lv004,lv005,lv008,lv009) 
				select '$this->LV_UserID' lv002,lv014,lv015,lv016,lv017,lv018,lv019,lv020,lv021,lv022,lv023,lv024,lv025,lv026,lv027,lv028,lv029,lv030,lv031,lv032,lv033,lv034,lv035,lv036,lv037,lv038,lv039,lv040,lv041,lv042,lv043,lv044,lv045,lv046,lv047,lv048,lv049,lv050,lv057,lv058,lv059,lv060,lv080,lv081,lv082,lv083,lv084,lv085,lv086,lv087,lv088,lv110,lv111,lv112,lv113,lv114,lv115,lv116,lv117,lv118,lv119,lv120,lv121,lv122,lv123,lv124,lv125,lv126,lv127,lv128,lv129,lv130,lv131,lv132,lv133,lv134,lv135,lv003,lv051,lv052,lv009 lv008,lv008 lv009 from cr_lv0376 where lv001='" . $vrow['lv001'] . "'";
			$vReturn = db_query($lvsql);
			if ($vReturn) {
				$vAttachID = sof_insert_id();
				if ($lvarr == '')
					$lvarr = "'$vAttachID'";
				else
					$lvarr = $lvarr . ",'$vAttachID'";
				$this->InsertLogOperation($this->DateCurrent, 'cr_lv0166.insert', sof_escape_string($lvsql));
				//Attached files
				$lvsql = "insert into erp_minhphuong_documents_v3_0.cri_lv0166(lv002,lv003,lv004,lv005,lv006,lv007,lv008) select '$this->LV_UserID',lv003,lv004,lv005,lv006,'$vAttachID',lv009 from erp_minhphuong_documents_v3_0.cri_lv0376 where lv002='" . $vrow['lv001'] . "'";
				$vReturn = db_query($lvsql);
				if ($vReturn) {
					$this->InsertLogOperation($this->DateCurrent, 'erp_minhphuong_documents_v3_0.cri_lv0166.insert', sof_escape_string($lvsql));
				}

			}
		}
		$this->LV_UpdateMoTa($lvarr);
	}
	function LV_PushPMH($vquotationid)
	{
		$lvarr = '';
		//Chi tiết báo giá
		$lvsql = "select A.lv001 from wh_lv0022 A inner join cr_lv0330 B on A.lv002=B.lv001 where B.lv001='$vquotationid'";
		$vresult1 = db_query($lvsql);
		while ($vrow = db_fetch_array($vresult1)) {
			//$vAmount=$vrow['lv053'];
			//$vAmount=($vAmount-$vAmount*$vrow['CK_Lan1']/100);
			//$vAmount=$this->LV_GetPercent($vquotationid,$vAmount);
			//Attached files
			$lvsql = "insert into wh_lv0032(lv002,lv014,lv015,lv016,lv017,lv018,lv019,lv020,lv021,lv022,lv023,lv024,lv025,lv026,lv027,lv028,lv029,lv030,lv031,lv032,lv033,lv034,lv035,lv036,lv037,lv038,lv039,lv040,lv041,lv042,lv043,lv044,lv045,lv046,lv047,lv048,lv049,lv050,lv057,lv058,lv059,lv060,lv080,lv081,lv082,lv083,lv084,lv085,lv086,lv087,lv088,lv110,lv111,lv112,lv113,lv114,lv115,lv116,lv117,lv118,lv119,lv120,lv121,lv122,lv123,lv124,lv125,lv126,lv127,lv128,lv129,lv130,lv131,lv132,lv133,lv134,lv135,lv003,lv004,lv005,lv008,lv009,lv012) 
				select '$this->LV_UserID' lv002,lv014,lv015,lv016,lv017,lv018,lv019,lv020,lv021,lv022,lv023,lv024,lv025,lv026,lv027,lv028,lv029,lv030,lv031,lv032,lv033,lv034,lv035,lv036,lv037,lv038,lv039,lv040,lv041,lv042,lv043,lv044,lv045,lv046,lv047,lv048,lv049,lv050,lv057,lv058,lv059,lv060,lv080,lv081,lv082,lv083,lv084,lv085,lv086,lv087,lv088,lv110,lv111,lv112,lv113,lv114,lv115,lv116,lv117,lv118,lv119,lv120,lv121,lv122,lv123,lv124,lv125,lv126,lv127,lv128,lv129,lv130,lv131,lv132,lv133,lv134,lv135,lv003,lv004,lv005,lv008,lv009,lv012 from wh_lv0022 where lv001='" . $vrow['lv001'] . "'";
			$vReturn = db_query($lvsql);
			if ($vReturn) {
				$vAttachID = sof_insert_id();
				if ($lvarr == '')
					$lvarr = "'$vAttachID'";
				else
					$lvarr = $lvarr . ",'$vAttachID'";
				$this->InsertLogOperation($this->DateCurrent, 'cr_lv0166.insert', sof_escape_string($lvsql));
				//Attached files
				$lvsql = "insert into erp_minhphuong_documents_v3_0.cri_lv0166(lv002,lv003,lv004,lv005,lv006,lv007,lv008) select '$this->LV_UserID',lv003,lv004,lv005,lv006,'$vAttachID',lv009 from erp_minhphuong_documents_v3_0.cri_lv0176 where lv002='" . $vrow['lv001'] . "'";
				$vReturn = db_query($lvsql);
				if ($vReturn) {
					$this->InsertLogOperation($this->DateCurrent, 'erp_minhphuong_documents_v3_0.cri_lv0166.insert', sof_escape_string($lvsql));
				}

			}
		}
		$this->LV_UpdateMoTa($lvarr);
	}
	//////Funtion Xu ly mua hang tu dong tu PBHID
	function LV_AutoCreatePBH($vJobID, $vPBHID, $vListJobID)
	{

		////Dò danh sách đầu ra PBH đã duyệt/////
		$lvsql = "select * from cr_lv0113 where lv115='$vPBHID' and lv114 in ($vListJobID)";
		$vresult = db_query($lvsql);
		while ($vrow = db_fetch_array($vresult)) {
			/////Tạo ra phiếu mua hàngg tự độnng
			$this->mocr_lv0330->lv001 = InsertWithCheckFist('cr_lv0330', 'lv001', '/PMH/MP' . substr(getyear($this->DateCurrent), -2, 2), 4);
			$this->mocr_lv0330->lv004 = $this->DateCurrent;
			$this->mocr_lv0330->lv002 = '';
			$this->mocr_lv0330->lv003 = '';
			$this->mocr_lv0330->lv114 = $vJobID;
			$this->mocr_lv0330->lv115 = $vPBHID;
			$bResultI = $this->mocr_lv0330->LV_Insert();
			if ($bResultI) {

				////////////Tạo chi tiết phiếu mua hàng
				if ($isAll == 1)
					$lvsql = "SELECT A.*,(AA.lv004) SoLuong,AB.lv006 MaPBH FROM cr_lv0276 A inner join cr_lv0114 AA on A.lv001=AA.lv198  inner join cr_lv0113 AB on AA.lv002=AB.lv001  WHERE AB.lv001='$vquotationid' ";
				else
					$lvsql = "SELECT A.*,(AA.lv004-IF(ISNULL(CC.lv130),0,CC.lv130)) SoLuong,AB.lv006 MaPBH FROM cr_lv0276 A inner join cr_lv0114 AA on A.lv001=AA.lv198  inner join cr_lv0113 AB on AA.lv002=AB.lv001 left join sl_lv0014 CC on A.lv001=CC.lv001  WHERE AB.lv001='$vquotationid' ";
				$vresult1 = db_query($lvsql);
				while ($vrow1 = db_fetch_array($vresult1)) {
					//$vAmount=$vrow1['lv053'];
					//$vAmount=($vAmount-$vAmount*$vrow1['CK_Lan1']/100);
					//$vAmount=$this->LV_GetPercent($vquotationid,$vAmount);
					//Attached files
					if ($vrow1['SoLuong'] > 0) {
						$vMaPBH = $vrow1['MaPBH'];
						$lvsql = "insert into wh_lv0032(lv002,lv014,lv015,lv016,lv017,lv018,lv019,lv020,lv021,lv022,lv023,lv024,lv025,lv026,lv027,lv028,lv029,lv030,lv031,lv032,lv033,lv034,lv035,lv036,lv037,lv038,lv039,lv040,lv041,lv042,lv043,lv044,lv045,lv046,lv047,lv048,lv049,lv050,lv057,lv058,lv059,lv060,lv080,lv081,lv082,lv083,lv084,lv085,lv086,lv087,lv088,lv110,lv111,lv112,lv113,lv114,lv115,lv116,lv117,lv118,lv119,lv120,lv121,lv122,lv123,lv124,lv125,lv126,lv127,lv128,lv129,lv130,lv131,lv132,lv133,lv134,lv135,lv003,lv004,lv005,lv008,lv009,lv012) 
							select '$this->LV_UserID' lv002,lv014,lv015,lv016,lv017,lv018,lv019,lv020,lv021,lv022,lv023,lv024,lv025,lv026,lv027,lv028,lv029,lv030,lv031,lv032,lv033,lv034,lv035,lv036,lv037,lv038,lv039,lv040,lv041,lv042,lv043,lv044,lv045,lv046,lv047,lv048,lv049,lv050,lv057,lv058,lv059,lv060,lv080,lv081,lv082,lv083,lv084,lv085,lv086,lv087,lv088,lv110,lv111,lv112,lv113,lv114,lv115,lv116,lv117,lv118,lv119,lv120,lv121,lv122,lv123,lv124,lv125,lv126,lv127,lv128,lv129,lv130,lv131,lv132,lv133,lv134,lv135,lv003,'" . $vrow1['SoLuong'] . "',lv052,lv009 lv008,lv008 lv009,'$vMaPBH' lv012 from cr_lv0276 where lv001='" . $vrow1['lv001'] . "'";
						$vReturn = db_query($lvsql);
						if ($vReturn) {
							$vAttachID = sof_insert_id();
							if ($lvarr == '')
								$lvarr = "'$vAttachID'";
							else
								$lvarr = $lvarr . ",'$vAttachID'";
							$this->InsertLogOperation($this->DateCurrent, 'cr_lv0166.insert', sof_escape_string($lvsql));
							//Attached files
							$lvsql = "insert into erp_minhphuong_documents_v3_0.cri_lv0166(lv002,lv003,lv004,lv005,lv006,lv007,lv008) select '$this->LV_UserID',lv003,lv004,lv005,lv006,'$vAttachID',lv009 from erp_minhphuong_documents_v3_0.cri_lv0276 where lv002='" . $vrow1['lv001'] . "'";
							$vReturn = db_query($lvsql);
							if ($vReturn) {
								$this->InsertLogOperation($this->DateCurrent, 'erp_minhphuong_documents_v3_0.cri_lv0166.insert', sof_escape_string($lvsql));
							}

						}
					}
				}
			}
		}
	}
	function LV_PushContract($vquotationid)
	{
		$lvarr = '';
		//Chi tiết báo giá
		$lvsql = "select A.lv001 from cr_lv0276 A inner join sl_lv0013 B on A.lv002=B.lv001 where B.lv115='$vquotationid'";
		$vresult1 = db_query($lvsql);
		while ($vrow = db_fetch_array($vresult1)) {
			//$vAmount=$vrow['lv053'];
			//$vAmount=($vAmount-$vAmount*$vrow['CK_Lan1']/100);
			//$vAmount=$this->LV_GetPercent($vquotationid,$vAmount);
			//Attached files
			$lvsql = "insert into wh_lv0032(lv002,lv014,lv015,lv016,lv017,lv018,lv019,lv020,lv021,lv022,lv023,lv024,lv025,lv026,lv027,lv028,lv029,lv030,lv031,lv032,lv033,lv034,lv035,lv036,lv037,lv038,lv039,lv040,lv041,lv042,lv043,lv044,lv045,lv046,lv047,lv048,lv049,lv050,lv057,lv058,lv059,lv060,lv080,lv081,lv082,lv083,lv084,lv085,lv086,lv087,lv088,lv110,lv111,lv112,lv113,lv114,lv115,lv116,lv117,lv118,lv119,lv120,lv121,lv122,lv123,lv124,lv125,lv126,lv127,lv128,lv129,lv130,lv131,lv132,lv133,lv134,lv135,lv003,lv004,lv005,lv008,lv009,lv012) 
				select '$this->LV_UserID' lv002,lv014,lv015,lv016,lv017,lv018,lv019,lv020,lv021,lv022,lv023,lv024,lv025,lv026,lv027,lv028,lv029,lv030,lv031,lv032,lv033,lv034,lv035,lv036,lv037,lv038,lv039,lv040,lv041,lv042,lv043,lv044,lv045,lv046,lv047,lv048,lv049,lv050,lv057,lv058,lv059,lv060,lv080,lv081,lv082,lv083,lv084,lv085,lv086,lv087,lv088,lv110,lv111,lv112,lv113,lv114,lv115,lv116,lv117,lv118,lv119,lv120,lv121,lv122,lv123,lv124,lv125,lv126,lv127,lv128,lv129,lv130,lv131,lv132,lv133,lv134,lv135,lv003,lv051,lv052,lv009 lv008,lv008 lv009,'$vquotationid' lv012 from cr_lv0276 where lv001='" . $vrow['lv001'] . "'";
			$vReturn = db_query($lvsql);
			if ($vReturn) {
				$vAttachID = sof_insert_id();
				if ($lvarr == '')
					$lvarr = "'$vAttachID'";
				else
					$lvarr = $lvarr . ",'$vAttachID'";
				$this->InsertLogOperation($this->DateCurrent, 'cr_lv0166.insert', sof_escape_string($lvsql));
				//Attached files
				$lvsql = "insert into erp_minhphuong_documents_v3_0.cri_lv0166(lv002,lv003,lv004,lv005,lv006,lv007,lv008) select '$this->LV_UserID',lv003,lv004,lv005,lv006,'$vAttachID',lv009 from erp_minhphuong_documents_v3_0.cri_lv0276 where lv002='" . $vrow['lv001'] . "'";
				$vReturn = db_query($lvsql);
				if ($vReturn) {
					$this->InsertLogOperation($this->DateCurrent, 'erp_minhphuong_documents_v3_0.cri_lv0166.insert', sof_escape_string($lvsql));
				}

			}
		}
		$this->LV_UpdateMoTa($lvarr);
	}
	function LV_PushContractDNDR_Del($vquotationid)
	{
		$lvsql = "delete from wh_lv0032  WHERE lv002='$this->LV_UserID'  ";
		$vReturn = db_query($lvsql);
		if ($vReturn) {
			$this->InsertLogOperation($this->DateCurrent, 'wh_lv0032.insert', sof_escape_string($lvsql));
		}
	}
	function LV_PushContractDNDR($vquotationid, $isAll, $isBom)
	{
		$lvarr = '';
		//Chi tiết báo giá
		if ($isBom == 1) {
			if ($isAll == 1)
				$lvsql = "SELECT AB.lv006 MaPBH,AA.lv003 ItemID,AA.lv005 DVT,AA.lv004 SoLuong FROM cr_lv0214 AA  inner join cr_lv0113 AB on AA.lv002=AB.lv001  WHERE AB.lv001='$vquotationid' ";
			else
				$lvsql = "SELECT AB.lv006 MaPBH,AA.lv003 ItemID,AA.lv005 DVT,-1*(AA.lv091) SoLuong FROM cr_lv0214 AA inner join cr_lv0113 AB on AA.lv002=AB.lv001  WHERE AB.lv001='$vquotationid' and AA.lv091<0 ";
			$vresult2 = db_query($lvsql);
			while ($vrow2 = db_fetch_array($vresult2)) {
				$vSTT = 0;
				$lvsql = "SELECT AA.*,'" . $vrow2['MaPBH'] . "' MaPBH,'" . $vrow2['ItemID'] . "' ItemID,'" . $vrow2['DVT'] . "' DVT,'" . $vrow2['SoLuong'] . "' SoLuong FROM wh_lv0022 AA   WHERE AA.lv003='" . $vrow2['ItemID'] . "'  order by lv001 DESC limit 0,1";
				while ($vrow = db_fetch_array($vresult1)) {
					$vSTT++;
					$vMaPBH = $vrow['MaPBH'];
					//$vAmount=$vrow['lv053'];
					//$vAmount=($vAmount-$vAmount*$vrow['CK_Lan1']/100);
					//$vAmount=$this->LV_GetPercent($vquotationid,$vAmount);
					//Attached files
					$lvsql = "insert into wh_lv0032(lv002,lv014,lv015,lv016,lv017,lv018,lv019,lv020,lv021,lv022,lv023,lv024,lv025,lv026,lv027,lv028,lv029,lv030,lv031,lv032,lv033,lv034,lv035,lv036,lv037,lv038,lv039,lv040,lv041,lv042,lv043,lv044,lv045,lv046,lv047,lv048,lv049,lv050,lv057,lv058,lv059,lv060,lv080,lv081,lv082,lv083,lv084,lv085,lv086,lv087,lv088,lv110,lv111,lv112,lv113,lv114,lv115,lv116,lv117,lv118,lv119,lv120,lv121,lv122,lv123,lv124,lv125,lv126,lv127,lv128,lv129,lv130,lv131,lv132,lv133,lv134,lv135,lv003,lv004,lv005,lv008,lv009,lv012) 
							select '$this->LV_UserID' lv002,lv014,lv015,lv016,lv017,lv018,lv019,lv020,lv021,lv022,lv023,lv024,lv025,lv026,lv027,lv028,lv029,lv030,lv031,lv032,lv033,lv034,lv035,lv036,lv037,lv038,lv039,lv040,lv041,lv042,lv043,lv044,lv045,lv046,lv047,lv048,lv049,lv050,lv057,lv058,lv059,lv060,lv080,lv081,lv082,lv083,lv084,lv085,lv086,lv087,lv088,lv110,lv111,lv112,lv113,lv114,lv115,lv116,lv117,lv118,lv119,lv120,lv121,lv122,lv123,lv124,lv125,lv126,lv127,lv128,lv129,lv130,lv131,lv132,lv133,lv134,lv135,lv003,'" . $vrow['SoLuong'] . "',lv052,lv009 lv008,lv008 lv009,'$vMaPBH' lv012 from cr_lv0276 where lv001='" . $vrow['lv001'] . "'";
					$vReturn = db_query($lvsql);
					if ($vReturn) {
						$vAttachID = sof_insert_id();
						if ($lvarr == '')
							$lvarr = "'$vAttachID'";
						else
							$lvarr = $lvarr . ",'$vAttachID'";
						$this->InsertLogOperation($this->DateCurrent, 'wh_lv0032.insert', sof_escape_string($lvsql));
						//Attached files
						$lvsql = "insert into erp_minhphuong_documents_v3_0.cri_lv0176(lv002,lv003,lv004,lv005,lv006,lv007,lv008) select '$this->LV_UserID',lv003,lv004,lv005,lv006,'$vAttachID',lv009 from erp_minhphuong_documents_v3_0.cri_lv0276 where lv002='" . $vrow['lv001'] . "'";
						$vReturn = db_query($lvsql);
						if ($vReturn) {
							$this->InsertLogOperation($this->DateCurrent, 'erp_minhphuong_documents_v3_0.cri_lv0176.insert', sof_escape_string($lvsql));
						}

					}
				}
				if ($vSTT == 0) {
					//Xử lý chèn tay vào hê thống
					$lvsql = "insert into wh_lv0032(lv002,lv003,lv004,lv005,lv012,lv010) values('$this->LV_UserID','" . $vrow2['ItemID'] . "','" . $vrow2['SoLuong'] . "','" . $vrow2['DVT'] . "','$vMaPBH','PMH')";
					$vReturn = db_query($lvsql);
					if ($vReturn) {
						$this->InsertLogOperation($this->DateCurrent, 'wh_lv0032.insert', sof_escape_string($lvsql));
					}
				}
			}
		} else {
			if ($isAll == 1)
				$lvsql = "SELECT A.*,(AA.lv004) SoLuong,AB.lv006 MaPBH FROM cr_lv0276 A inner join cr_lv0114 AA on A.lv001=AA.lv198  inner join cr_lv0113 AB on AA.lv002=AB.lv001  WHERE AB.lv001='$vquotationid' ";
			else
				$lvsql = "SELECT A.*,(AA.lv004-IF(ISNULL(CC.lv130),0,CC.lv130)) SoLuong,AB.lv006 MaPBH FROM cr_lv0276 A inner join cr_lv0114 AA on A.lv001=AA.lv198  inner join cr_lv0113 AB on AA.lv002=AB.lv001 left join sl_lv0014 CC on A.lv001=CC.lv001  WHERE AB.lv001='$vquotationid' ";
			$vresult1 = db_query($lvsql);
			while ($vrow = db_fetch_array($vresult1)) {
				//$vAmount=$vrow['lv053'];
				//$vAmount=($vAmount-$vAmount*$vrow['CK_Lan1']/100);
				//$vAmount=$this->LV_GetPercent($vquotationid,$vAmount);
				//Attached files
				if ($vrow['SoLuong'] > 0) {
					$vMaPBH = $vrow['MaPBH'];
					$lvsql = "insert into wh_lv0032(lv002,lv014,lv015,lv016,lv017,lv018,lv019,lv020,lv021,lv022,lv023,lv024,lv025,lv026,lv027,lv028,lv029,lv030,lv031,lv032,lv033,lv034,lv035,lv036,lv037,lv038,lv039,lv040,lv041,lv042,lv043,lv044,lv045,lv046,lv047,lv048,lv049,lv050,lv057,lv058,lv059,lv060,lv080,lv081,lv082,lv083,lv084,lv085,lv086,lv087,lv088,lv110,lv111,lv112,lv113,lv114,lv115,lv116,lv117,lv118,lv119,lv120,lv121,lv122,lv123,lv124,lv125,lv126,lv127,lv128,lv129,lv130,lv131,lv132,lv133,lv134,lv135,lv003,lv004,lv005,lv008,lv009,lv012) 
							select '$this->LV_UserID' lv002,lv014,lv015,lv016,lv017,lv018,lv019,lv020,lv021,lv022,lv023,lv024,lv025,lv026,lv027,lv028,lv029,lv030,lv031,lv032,lv033,lv034,lv035,lv036,lv037,lv038,lv039,lv040,lv041,lv042,lv043,lv044,lv045,lv046,lv047,lv048,lv049,lv050,lv057,lv058,lv059,lv060,lv080,lv081,lv082,lv083,lv084,lv085,lv086,lv087,lv088,lv110,lv111,lv112,lv113,lv114,lv115,lv116,lv117,lv118,lv119,lv120,lv121,lv122,lv123,lv124,lv125,lv126,lv127,lv128,lv129,lv130,lv131,lv132,lv133,lv134,lv135,lv003,'" . $vrow['SoLuong'] . "',lv052,lv009 lv008,lv008 lv009,'$vMaPBH' lv012 from cr_lv0276 where lv001='" . $vrow['lv001'] . "'";
					$vReturn = db_query($lvsql);
					if ($vReturn) {
						$vAttachID = sof_insert_id();
						if ($lvarr == '')
							$lvarr = "'$vAttachID'";
						else
							$lvarr = $lvarr . ",'$vAttachID'";
						$this->InsertLogOperation($this->DateCurrent, 'cr_lv0166.insert', sof_escape_string($lvsql));
						//Attached files
						$lvsql = "insert into erp_minhphuong_documents_v3_0.cri_lv0166(lv002,lv003,lv004,lv005,lv006,lv007,lv008) select '$this->LV_UserID',lv003,lv004,lv005,lv006,'$vAttachID',lv009 from erp_minhphuong_documents_v3_0.cri_lv0276 where lv002='" . $vrow['lv001'] . "'";
						$vReturn = db_query($lvsql);
						if ($vReturn) {
							$this->InsertLogOperation($this->DateCurrent, 'erp_minhphuong_documents_v3_0.cri_lv0166.insert', sof_escape_string($lvsql));
						}

					}
				}
			}

		}
		//$this->LV_UpdateMoTa($lvarr);
		return $vMaPBH;
	}
	function LV_PushContractCCDC($vquotationid)
	{
		$lvarr = '';
		//Chi tiết báo giá
		$lvsql = "select A.lv001 from cr_lv0151 A inner join cr_lv0150 B on A.lv002=B.lv001 where B.lv001='$vquotationid'";
		$vresult1 = db_query($lvsql);
		while ($vrow = db_fetch_array($vresult1)) {
			//$vAmount=$vrow['lv053'];
			//$vAmount=($vAmount-$vAmount*$vrow['CK_Lan1']/100);
			//$vAmount=$this->LV_GetPercent($vquotationid,$vAmount);
			//Attached files
			$lvsql = "insert into wh_lv0032(lv002,lv003,lv004,lv005,lv006,lv007,lv013,lv054,lv011) 
				select '$this->LV_UserID' lv002,lv003,lv004,lv005,lv006,lv007,now() lv013,'' lv054,lv011 from cr_lv0151 where lv001='" . $vrow['lv001'] . "'";
			$vReturn = db_query($lvsql);
			if ($vReturn) {
				$vAttachID = sof_insert_id();
				if ($lvarr == '')
					$lvarr = "'$vAttachID'";
				else
					$lvarr = $lvarr . ",'$vAttachID'";
				$this->InsertLogOperation($this->DateCurrent, 'cr_lv0166.insert', sof_escape_string($lvsql));
				//Attached files
				/*$lvsql="insert into erp_minhphuong_documents_v3_0.cri_lv0166(lv002,lv003,lv004,lv005,lv006,lv007,lv008) select '$this->LV_UserID',lv003,lv004,lv005,lv006,'$vAttachID',lv009 from erp_minhphuong_documents_v3_0.cri_lv0276 where lv002='".$vrow['lv001']."'";
																																																				$vReturn= db_query($lvsql);
																																																				if($vReturn) 
																																																				{
																																																					$this->InsertLogOperation($this->DateCurrent,'erp_minhphuong_documents_v3_0.cri_lv0166.insert',sof_escape_string($lvsql));
																																																				}*/

			}
		}
		$this->LV_UpdateMoTa($lvarr);
	}
	function LV_UpdateMoTa($lvarr)
	{
		$lvsql = "select * from wh_lv0032 where lv001 IN ($lvarr)";
		$vresult = db_query($lvsql);
		while ($vrow = db_fetch_array($vresult)) {

			$vStrDes = $this->LV_ReplaceFull($vrow);
			//$vItemWH=str_replace(' ','',$vrow['lv009'].$vrow['lv014'].$vrow['lv016'].$vrow['lv021'].$vrow['lv022'].$vrow['lv024'].$vrow['lv031'].$vrow['lv026'].$vrow['lv027'].$vrow['lv030'].$vrow['lv029'].$vrow['lv046'].$vrow['lv036']);
			$lvsql1 = "Update wh_lv0032 set lv011='" . sof_escape_string($vStrDes) . "' where lv001='" . $vrow['lv001'] . "'";
			$vReturn1 = db_query($lvsql1);
			if ($vReturn1)
				$this->InsertLogOperation($this->DateCurrent, 'wh_lv0032.update', sof_escape_string($lvsql1));
		}

		return $vReturn;
	}
	function LV_ReplaceFull($vrow)
	{
		$vList = "lv093,lv014,lv022,lv023,lv020,lv081,lv030,lv024,lv036,lv082,lv068,lv029,lv028,lv046,lv026,lv032,lv033,lv083,lv027,lv084,lv085,lv086,lv087,lv088,lv089,lv090,lv091,lv092,lv093,lv094,lv095";
		$vArrList = explode(',', $vList);
		foreach ($vArrList as $vField) {
			if (trim($vrow[$vField]) != '') {
				if ($vStrObj == '')
					$vStrObj = $this->ArrPush[(int) $this->ArrGet[$vField]] . ': ' . $vrow[$vField];
				else
					$vStrObj = $vStrObj . "\r" . $this->ArrPush[(int) $this->ArrGet[$vField]] . ': ' . $vrow[$vField];
			}
		}
		return $vStrObj;
	}
	protected function GetConditionMini()
	{
		$strCondi = "";
		//$strwh=$this->Get_WHControler();
		//	$strCondi=$strCondi." and lv002 in ($strwh)";
		return $strCondi;
	}
	/////////////////Xu ly lay so luong don mua hang////////////
	function LV_GetSLPMH($vLoaiNguon, $vMaNguon, $vItemID, $vTrangThai = '')
	{
		$vArrReturn = array(-1 => 0, 0 => 0, 1 => 0, 2 => 0, 3 => 0);
		$vStrCondition = " and A.lv003='$vItemID'";
		//0. Phiếu đầu ra, 1. PBH , 2. Bc VT 
		switch ($vLoaiNguon) {
			case 0:
				$vStrCondition = $vStrCondition . " and B.lv090='$vMaNguon' ";
				break;
			case 1:
				$vStrCondition = $vStrCondition . " and B.lv089='$vMaNguon' ";
				break;
			case 2:
				$vStrCondition = $vStrCondition . " and B.lv088='$vMaNguon' ";
				break;
		}
		if ($vTrangThai != '') {
			$vStrCondition = $vStrCondition . " and B.lv027='$vTrangThai' ";
		}
		$sqlC = "SELECT sum(A.lv004) SLMua,B.lv027 States FROM wh_lv0022 A inner join cr_lv0330 B on A.lv002=B.lv001 WHERE 1=1 $vStrCondition";
		$bResultC = db_query($sqlC);
		while ($arrRowC = db_fetch_array($bResultC)) {
			$vArrReturn[$arrRowC['States']] = $arrRowC['SLMua'];
		}
		return $vArrReturn;
	}
	////////////////Count///////////////////////////
	function GetCount()
	{
		$sqlC = "SELECT COUNT(*) AS nums FROM cr_lv0330 WHERE 1=1 " . $this->GetCondition();
		$bResultC = db_query($sqlC);
		$arrRowC = db_fetch_array($bResultC);
		return $arrRowC['nums'];
	}
	function LV_GetBLMoney($vContractID, &$vVAT = 0)
	{
		$lvsql = "select sum(PM.lv003) money,sum(PM.lv004) VAT,sum(PM.lv005) thuekhac from ((select sum(A.lv004*A.lv006) lv003,sum(A.lv004*A.lv006*B.lv006/100) lv004,0 lv005 from wh_lv0022 A inner join wh_lv0021 B on A.lv002=B.lv001  where 1=1 and B.lv087='$vContractID' )) PM ";
		$vresult = db_query($lvsql);
		$vrow = db_fetch_array($vresult);
		if ($vrow['VAT'] == 0) {
			if ($vrow['money'] == 0)
				return "0";
		}
		$vVAT = $vrow['VAT'];
		return $vrow['VAT'] + $vrow['money'] + $vrow['thuekhac'];
	}
	function LV_CreateTC($strar)
	{
		$vArrdonhang = explode("@", $strar);
		$vNow = GetServerDate();
		foreach ($vArrdonhang as $donhang) {
			$this->LV_LoadSupID($donhang);
			if ($donhang != "") {
				$vcusid = $this->lv002;
				$this->lvwh_lv0003->LV_LoadID($vcusid);
				//$this->lvwh_lv0052=new wh_lv0052($_SESSION['ERPSOFV2RRight'],$_SESSION['ERPSOFV2RUserID'],'Wh0052');
				$this->lvwh_lv0052->lv001 = InsertWithCheck('ac_lv0004', 'lv001', 'PC', 4);
				$this->lvwh_lv0052->lv002 = '1';
				$this->lvwh_lv0052->lv003 = 'SUP';
				$this->lvwh_lv0052->lv004 = $this->lvwh_lv0003->lv001;
				$this->lvwh_lv0052->lv005 = $this->lvwh_lv0003->lv002;
				$this->lvwh_lv0052->lv006 = $this->lvwh_lv0003->lv006;
				$this->lvwh_lv0052->lv007 = $this->lv807;
				$this->lvwh_lv0052->lv008 = $this->LV_UserID;
				$this->lvwh_lv0052->lv009 = $this->FormatView($vNow, 2);
				if ($this->lvwh_lv0052->lv010 == '')
					$this->lvwh_lv0052->lv010 = '1111';
				if ($this->lvwh_lv0052->lv910 == '')
					$this->lvwh_lv0052->lv910 = '331';
				$this->lvwh_lv0052->lv011 = 'VND';
				$this->lvwh_lv0052->lv012 = '1';
				$this->lvwh_lv0052->lv013 = $this->lv001;
				$this->lvwh_lv0052->lv014 = $this->FormatView($vInvoiceDate, 2);
				$this->lvwh_lv0052->lv015 = $donhang;
				$this->lvwh_lv0052->lv016 = '';
				$this->lvwh_lv0052->lv018 = $this->lv818;
				$this->lvwh_lv0052->lv022 = 'MINHPHUONG';
				$this->lvwh_lv0052->lv019 = $this->LV_GetBLMoney($donhang);
				if ($this->lvwh_lv0052->lv019 > 0)
					$this->lvwh_lv0052->LV_Insert();
			}
		}
		$this->lv002 = '';
		$this->lv001 = '';
	}
	function LV_CreateNganHang($strar)
	{
		$vArrdonhang = explode("@", $strar);
		$vNow = GetServerDate();
		foreach ($vArrdonhang as $donhang) {
			$this->LV_LoadSupID($donhang);
			if ($donhang != "") {
				$vcusid = $this->lv002;
				$this->lvwh_lv0003->LV_LoadID($vcusid);
				//$this->lvwh_lv0052=new wh_lv0052($_SESSION['ERPSOFV2RRight'],$_SESSION['ERPSOFV2RUserID'],'Wh0052');
				$this->lvwh_lv0052->lv001 = InsertWithCheck('ac_lv0004', 'lv001', 'PC', 4);
				$this->lvwh_lv0052->lv002 = '1';
				$this->lvwh_lv0052->lv003 = 'SUP';
				$this->lvwh_lv0052->lv004 = $this->lvwh_lv0003->lv001;
				$this->lvwh_lv0052->lv005 = $this->lvwh_lv0003->lv002;
				$this->lvwh_lv0052->lv006 = $this->lvwh_lv0003->lv006;
				$this->lvwh_lv0052->lv007 = $this->lv807;
				$this->lvwh_lv0052->lv008 = $this->LV_UserID;
				$this->lvwh_lv0052->lv009 = $this->FormatView($vNow, 2);
				if ($this->lvwh_lv0052->lv010 == '')
					$this->lvwh_lv0052->lv010 = '1121';
				if ($this->lvwh_lv0052->lv910 == '')
					$this->lvwh_lv0052->lv910 = '331';
				$this->lvwh_lv0052->lv011 = 'VND';
				$this->lvwh_lv0052->lv012 = '1';
				$this->lvwh_lv0052->lv013 = $this->lv001;
				$this->lvwh_lv0052->lv014 = $this->FormatView($vInvoiceDate, 2);
				$this->lvwh_lv0052->lv015 = $donhang;
				$this->lvwh_lv0052->lv016 = '';
				$this->lvwh_lv0052->lv018 = $this->lv818;
				$this->lvwh_lv0052->lv022 = 'MINHPHUONG';
				if ($vMoney == 0)
					$this->lvwh_lv0052->lv019 = $this->LV_GetBLMoney($donhang);
				else
					$this->lvwh_lv0052->lv019 = $vMoney;
				if ($this->lvwh_lv0052->lv019 > 0)
					$this->lvwh_lv0052->LV_Insert();
			}
		}
		$this->lv002 = '';
		$this->lv001 = '';
	}
	function LV_CreateTreoCongNo($strar)
	{
		$vArrdonhang = explode("@", $strar);
		$vNow = GetServerDate();
		foreach ($vArrdonhang as $donhang) {
			$this->LV_LoadSupID($donhang);
			if ($donhang != "") {
				$vcusid = $this->lv002;
				$this->lvwh_lv0003->LV_LoadID($vcusid);
				//$this->lvwh_lv0052=new wh_lv0052($_SESSION['ERPSOFV2RRight'],$_SESSION['ERPSOFV2RUserID'],'Wh0052');
				$this->lvwh_lv0052->lv001 = InsertWithCheck('ac_lv0004', 'lv001', 'TN', 4);
				$this->lvwh_lv0052->lv002 = '1';
				$this->lvwh_lv0052->lv003 = 'SUP';
				$this->lvwh_lv0052->lv004 = $this->lvwh_lv0003->lv001;
				$this->lvwh_lv0052->lv005 = $this->lvwh_lv0003->lv002;
				$this->lvwh_lv0052->lv006 = $this->lvwh_lv0003->lv006;
				$this->lvwh_lv0052->lv007 = $this->lv807;
				$this->lvwh_lv0052->lv008 = $this->LV_UserID;
				$this->lvwh_lv0052->lv009 = $this->FormatView($vNow, 2);
				if ($this->lvwh_lv0052->lv010 == '')
					$this->lvwh_lv0052->lv010 = '331';
				if ($this->lvwh_lv0052->lv910 == '')
					$this->lvwh_lv0052->lv910 = '156';
				$this->lvwh_lv0052->lv011 = 'VND';
				$this->lvwh_lv0052->lv012 = '1';
				$this->lvwh_lv0052->lv013 = $this->lv001;
				$this->lvwh_lv0052->lv014 = $this->FormatView($vInvoiceDate, 2);
				$this->lvwh_lv0052->lv015 = $donhang;
				$this->lvwh_lv0052->lv016 = '';
				$this->lvwh_lv0052->lv017 = 12;
				$this->lvwh_lv0052->lv022 = 'MINHPHUONG';
				if ($vMoney == 0)
					$this->lvwh_lv0052->lv019 = $this->LV_GetBLMoney($donhang);
				else
					$this->lvwh_lv0052->lv019 = $vMoney;
				if ($this->lvwh_lv0052->lv019 > 0)
					$this->lvwh_lv0052->LV_InsertTreo();
			}
		}
		$this->lv002 = '';
		$this->lv001 = '';
	}
	function LV_GetPCMoney($vContractID, $vTKNo, $vCurency)
	{
		//GetMoney
		$vListParent = $this->LV_GetInvoiceParent($vContractID);
		$lvsql = "select if(ISNULL(sum(A.lv004)),0,sum(A.lv004)) SumMoney from ac_lv0005 A inner join ac_lv0004 B on A.lv002=B.lv001 WHERE B.lv002=1 and B.lv017=0 and A.lv002 in (" . $vListParent . ") and A.lv005 like '$vTKNo%' and B.lv011='$vCurency'";
		$vReturnArr = array();
		$lvResult = db_query($lvsql);
		$row = db_fetch_array($lvResult);
		return $row['SumMoney'];

	}
	function LV_GetInvoiceParent_DotAll($vContractID)
	{
		$vResult = '';
		$lvsql = "select B.lv001 from wh_lv0021 B where B.lv087='$vContractID' ";
		$lvResult = db_query($lvsql);
		while ($row = db_fetch_array($lvResult)) {
			if ($vResult == "")
				$vResult = "'" . $row['lv001'] . "'";
			else
				$vResult = $vResult . ",'" . $row['lv001'] . "'";
			;
		}
		return $vResult;
	}
	function LV_GetPCMoneyDotAll($vContractID, $vCurency)
	{
		//GetMoney
		$vListPMH = $this->LV_GetInvoiceParent_DotAll($vContractID);
		if ($vListPMH != '')
			$lvsql = "select if(ISNULL(sum(A.lv003)),0,sum(A.lv003)) SumMoney from ac_lv0005 A inner join ac_lv0004 B on A.lv002=B.lv001 WHERE B.lv002=1 and B.lv017=0 and (B.lv118='$vContractID' or B.lv116 in ($vListPMH)) and lv011='$vCurency'";
		else
			$lvsql = "select if(ISNULL(sum(A.lv003)),0,sum(A.lv003)) SumMoney from ac_lv0005 A inner join ac_lv0004 B on A.lv002=B.lv001 WHERE B.lv002=1 and B.lv017=0 and B.lv118='$vContractID' and lv011='$vCurency'";
		$vReturnArr = array();
		$lvResult = db_query($lvsql);
		$row = db_fetch_array($lvResult);
		return $row['SumMoney'];

	}
	function LV_GetPCMoneyDot($vContractID, $vDot, $vCurency)
	{
		//GetMoney
		$vListPMH = $this->LV_GetInvoiceParent_DotAll($vContractID);
		if ($vListPMH != '')
			$lvsql = "select if(ISNULL(sum(A.lv003)),0,sum(A.lv003)) SumMoney from ac_lv0005 A inner join ac_lv0004 B on A.lv002=B.lv001 WHERE B.lv002=1 and B.lv017=0 and (B.lv118='$vContractID' or B.lv116 in ($vListPMH)) and B.lv018 in ($vDot) and lv011='$vCurency'";
		else
			$lvsql = "select if(ISNULL(sum(A.lv003)),0,sum(A.lv003)) SumMoney from ac_lv0005 A inner join ac_lv0004 B on A.lv002=B.lv001 WHERE B.lv002=1 and B.lv017=0 and B.lv118='$vContractID' and B.lv018 in ($vDot) and lv011='$vCurency'";
		$vReturnArr = array();
		$lvResult = db_query($lvsql);
		$row = db_fetch_array($lvResult);
		return $row['SumMoney'];

	}
	function LV_GetPCMoneyTreo($vContractID, $vTKCo)
	{
		//GetMoney
		$vListParent = $this->LV_GetInvoiceParent($vContractID);
		$lvsql = "select if(ISNULL(sum(A.lv004)),0,sum(A.lv004)) SumMoney from ac_lv0005 A  inner join ac_lv0004 B on A.lv002=B.lv001 WHERE B.lv002=1 and B.lv017=12 and A.lv002 in (" . $vListParent . ")  and A.lv006 like '$vTKCo%'";
		$vReturnArr = array();
		$lvResult = db_query($lvsql);
		$row = db_fetch_array($lvResult);
		return $row['SumMoney'];

	}
	function LV_GetInvoiceParent($vContractID)
	{
		$vResult = '';
		$lvsql = "select B.lv001 from ac_lv0004 B where B.lv013='$vContractID' ";
		$lvResult = db_query($lvsql);
		while ($row = db_fetch_array($lvResult)) {
			if ($vResult == "")
				$vResult = "'" . $row['lv001'] . "'";
			else
				$vResult = $vResult . ",'" . $row['lv001'] . "'";
			;
		}
		if ($vResult == '')
			return "''";
		else
			return $vResult;
	}
	function LV_Exist($vlv001)
	{
		$lvsql = "select count(*) num from  cr_lv0330 Where lv001='$vlv001'";
		$vresult = db_query($lvsql);
		$vrow = db_fetch_array($vresult);
		if ($vrow) {
			if ($vrow['num'] > 0)
				return true;
			else
				return false;
		}
		return false;
	}
	function LV_InsertTempMore()
	{
		$this->lv004 = ($this->lv004 != "") ? recoverdate(($this->lv004), $this->lang) : $this->DateDefault;
		$this->lv005 = ($this->lv005 != "") ? recoverdate(($this->lv005), $this->lang) : $this->DateDefault;
		if ($this->lv002 != '') {
			$vReturn = $this->LV_InsertTempNCC('');
		} else {
			//Dò ra danh sách báo giá
			$lvsql = "select distinct A.lv299 from wh_lv0032 A left join wh_lv0003 B on A.lv299=B.lv001 where A.lv002='" . $this->LV_UserID . "'";
			$vresult1 = db_query($lvsql);
			while ($vrow = db_fetch_array($vresult1)) {
				$vNCCID = $vrow['lv299'];
				if ($vNCCID == '')
					$vNCCID = ' ';
				$vReturn = $this->LV_InsertTempNCC($vNCCID);
			}
		}
		return $vReturn;
	}
	function LV_InsertTempNCC($vNCCID)
	{
		if ($this->isAdd == 0)
			return false;
		$this->lv001 = InsertWithCheckFist('cr_lv0330', 'lv001', '/PMH/MP' . substr(getyear($this->DateCurrent), -2, 2), 4);
		if (trim($this->lv002) != '')
			$lvsql = "insert into cr_lv0330 (lv001,lv002,lv003,lv004,lv005,lv006,lv007,lv008,lv009,lv010,lv011,lv012,lv013,lv014,lv015,lv114,lv104,lv105,lv106,lv107,lv108,lv109,lv110,lv111,lv112,lv113,lv088,lv089,lv090) values('$this->lv001','$this->lv002','$this->lv003',concat('$this->lv004',' ',CURRENT_TIME()),concat('$this->lv005',' ',CURRENT_TIME()),'$this->lv006','$this->lv007','$this->lv008','$this->lv009','$this->lv010','$this->lv011','$this->lv012','$this->lv013','$this->lv014','$this->lv015','$this->lv114','" . sof_escape_string($this->lv104) . "','" . sof_escape_string($this->lv105) . "','" . sof_escape_string($this->lv106) . "','" . sof_escape_string($this->lv107) . "','" . sof_escape_string($this->lv108) . "','" . sof_escape_string($this->lv109) . "','" . sof_escape_string($this->lv110) . "','" . sof_escape_string($this->lv111) . "','" . sof_escape_string($this->lv112) . "','" . sof_escape_string($this->lv113) . "','" . sof_escape_string($this->lv088) . "','" . sof_escape_string($this->lv089) . "','" . sof_escape_string($this->lv090) . "')";
		else
			$lvsql = "insert into cr_lv0330 (lv001,lv002,lv003,lv004,lv005,lv006,lv007,lv008,lv009,lv010,lv011,lv012,lv013,lv014,lv015,lv114,lv104,lv105,lv106,lv107,lv108,lv109,lv110,lv111,lv112,lv113,lv088,lv089,lv090) values('$this->lv001','" . trim($vNCCID) . "','$this->lv003',concat('$this->lv004',' ',CURRENT_TIME()),concat('$this->lv005',' ',CURRENT_TIME()),'$this->lv006','$this->lv007','$this->lv008','$this->lv009','$this->lv010','$this->lv011','$this->lv012','$this->lv013','$this->lv014','$this->lv015','$this->lv114','" . sof_escape_string($this->lv104) . "','" . sof_escape_string($this->lv105) . "','" . sof_escape_string($this->lv106) . "','" . sof_escape_string($this->lv107) . "','" . sof_escape_string($this->lv108) . "','" . sof_escape_string($this->lv109) . "','" . sof_escape_string($this->lv110) . "','" . sof_escape_string($this->lv111) . "','" . sof_escape_string($this->lv112) . "','" . sof_escape_string($this->lv113) . "','" . sof_escape_string($this->lv088) . "','" . sof_escape_string($this->lv089) . "','" . sof_escape_string($this->lv090) . "')";
		$vReturn = db_query($lvsql);
		if ($vReturn) {
			$vInsertID = $this->lv001;
			$this->InsertLogOperation($this->DateCurrent, 'cr_lv0330.insert', sof_escape_string($lvsql));
			$this->LV_InsertXMLChildNCC($vInsertID, $vNCCID);
			$this->LV_ChangeChild($vInsertID);
		}
		return $vReturn;
	}
	function LV_InsertXMLChildNCC($vInsertID, $vNCCID)
	{

		//Chi tiết báo giá
		if ($vNCCID == '')
			$lvsql = "select lv001 from wh_lv0032 where lv002='" . $this->LV_UserID . "'";
		else
			$lvsql = "select lv001 from wh_lv0032 where lv002='" . $this->LV_UserID . "' and lv299='" . trim($vNCCID) . "'";
		$vresult1 = db_query($lvsql);
		while ($vrow = db_fetch_array($vresult1)) {
			//Attached files
			$lvsql = "insert into wh_lv0022(lv002,lv003,lv004,lv005,lv006,lv007,lv008,lv009,lv010
			,lv011,lv012,lv013,lv014,lv015,lv016,lv017,lv018,lv019,lv020
			,lv021,lv022,lv023,lv024,lv025,lv026,lv027,lv028,lv029,lv030
			,lv031,lv032,lv033,lv034,lv035,lv036,lv037,lv038,lv039,lv040
			,lv041,lv042,lv043,lv044,lv045,lv046,lv047,lv048,lv049,lv050,
			lv051,lv052,lv053,lv054,lv055,lv056,lv057,lv058,lv059,lv060,lv061,lv062,lv063,lv064,lv065,lv068,lv070,lv080,lv081,lv082,lv083,lv084,lv085,lv086,lv087,lv088,lv089,lv090,lv091,lv092,lv093,lv094,lv095,lv101,lv102,lv103,lv104,lv110,lv111,lv112,lv113,lv114,lv115,lv116,lv117,lv118,lv119,lv120,lv121,lv122,lv123,lv124,lv125,lv126,lv127,lv128,lv129,lv130,lv131,lv132,lv133,lv134,lv135,lv136,lv137,lv299) select '$vInsertID' lv002,lv003,lv004,lv005,lv006,lv007,lv008,lv009,lv010
			,lv011,lv012,lv013,lv014,lv015,lv016,lv017,lv018,lv019,lv020
			,lv021,lv022,lv023,lv024,lv025,lv026,lv027,lv028,lv029,lv030
			,lv031,lv032,lv033,lv034,lv035,lv036,lv037,lv038,lv039,lv040
			,lv041,lv042,lv043,lv044,lv045,lv046,lv047,lv048,lv049,lv050,
			lv051,lv052,lv053,lv054,lv055,lv056,lv057,lv058,lv059,lv060,lv061,lv062,lv063,lv064,lv065,lv068,lv070,lv080,lv081,lv082,lv083,lv084,lv085,lv086,lv087,lv088,lv089,lv090,lv091,lv092,lv093,lv094,lv095,lv101,lv102,lv103,lv104,lv110,lv111,lv112,lv113,lv114,lv115,lv116,lv117,lv118,lv119,lv120,lv121,lv122,lv123,lv124,lv125,lv126,lv127,lv128,lv129,lv130,lv131,lv132,lv133,lv134,lv135,lv136,lv137,lv299 from wh_lv0032 where lv001='" . $vrow['lv001'] . "'";
			$vReturn = db_query($lvsql);
			if ($vReturn) {
				$vAttachID = sof_insert_id();
				$this->InsertLogOperation($this->DateCurrent, 'wh_lv0022.insert', sof_escape_string($lvsql));
				//Attached files
				$lvsql = "insert into erp_minhphuong_documents_v3_0.cri_lv0176(lv002,lv003,lv004,lv005,lv006,lv007,lv008,lv009) select '$vAttachID',lv003,lv004,lv005,lv006,'$vInsertID',lv008,lv009 from erp_minhphuong_documents_v3_0.cri_lv0166 where lv007='" . $vrow['lv001'] . "'";
				$vReturn = db_query($lvsql);
				if ($vReturn) {
					$this->InsertLogOperation($this->DateCurrent, 'erp_minhphuong_documents_v3_0.cri_lv0176.insert', sof_escape_string($lvsql));
					$lvsql1 = "delete from erp_minhphuong_documents_v3_0.cri_lv0166 where lv007='" . $vrow['lv001'] . "'";
					$vReturn = db_query($lvsql1);
					$lvsql1 = "delete from wh_lv0032 where lv001='" . $vrow['lv001'] . "'";
					$vReturn = db_query($lvsql1);
				}

			}
		}
		$lvsql = "select lv001 from cr_lv0170 where lv002='" . $this->LV_UserID . "'";
		$vresult1 = db_query($lvsql);
		while ($vrow = db_fetch_array($vresult1)) {
			//Attached files
			$lvsql = "insert into cr_lv0171(lv002,lv003,lv004,lv005,lv006,lv007,lv008,lv009,lv010) select '$vInsertID',lv003,lv004,lv005,lv006,lv007,lv008,lv009,lv010 from cr_lv0170 where lv001='" . $vrow['lv001'] . "'";
			$vReturn = db_query($lvsql);
			if ($vReturn) {
				$vAttachID = sof_insert_id();
				$this->InsertLogOperation($this->DateCurrent, 'cr_lv0171.insert', sof_escape_string($lvsql));
				//Attached files
				$lvsql = "insert into erp_minhphuong_documents_v3_0.cr_lv0171(lv002,lv003,lv004,lv005,lv006,lv007,lv008) select '$vAttachID',lv003,lv004,lv005,lv006,'$vInsertID',lv008 from erp_minhphuong_documents_v3_0.cr_lv0170 where lv002='" . $vrow['lv001'] . "'";
				$vReturn = db_query($lvsql);
				if ($vReturn) {
					$this->InsertLogOperation($this->DateCurrent, 'erp_minhphuong_documents_v3_0.cr_lv0171.insert', sof_escape_string($lvsql));
					$lvsql1 = "delete from erp_minhphuong_documents_v3_0.cr_lv0170 where lv002='" . $vrow['lv001'] . "'";
					$vReturn = db_query($lvsql1);
					$lvsql1 = "delete from cr_lv0170 where lv001='" . $vrow['lv001'] . "'";
					$vReturn = db_query($lvsql1);
				}

			}
		}
	}
	function LV_InsertTemp()
	{

		if ($this->isAdd == 0)
			return false;
		$this->lv004 = ($this->lv004 != "") ? recoverdate(($this->lv004), $this->lang) : $this->DateDefault;
		$this->lv005 = ($this->lv005 != "") ? recoverdate(($this->lv005), $this->lang) : $this->DateDefault;
		$lvsql = "insert into cr_lv0330 (lv001,lv002,lv003,lv004,lv005,lv006,lv007,lv008,lv009,lv010,lv011,lv012,lv013,lv014,lv015,lv114,lv104,lv105,lv106,lv107,lv108,lv109,lv110,lv111,lv112,lv113,lv088,lv089,lv090) values('$this->lv001','$this->lv002','$this->lv003',concat('$this->lv004',' ',CURRENT_TIME()),concat('$this->lv005',' ',CURRENT_TIME()),'$this->lv006','$this->lv007','$this->lv008','$this->lv009','$this->lv010','$this->lv011','$this->lv012','$this->lv013','$this->lv014','$this->lv015','$this->lv114','" . sof_escape_string($this->lv104) . "','" . sof_escape_string($this->lv105) . "','" . sof_escape_string($this->lv106) . "','" . sof_escape_string($this->lv107) . "','" . sof_escape_string($this->lv108) . "','" . sof_escape_string($this->lv109) . "','" . sof_escape_string($this->lv110) . "','" . sof_escape_string($this->lv111) . "','" . sof_escape_string($this->lv112) . "','" . sof_escape_string($this->lv113) . "','" . sof_escape_string($this->lv088) . "','" . sof_escape_string($this->lv089) . "','" . sof_escape_string($this->lv090) . "')";
		$vReturn = db_query($lvsql);
		if ($vReturn) {
			$vInsertID = $this->lv001;
			$this->InsertLogOperation($this->DateCurrent, 'cr_lv0330.insert', sof_escape_string($lvsql));
			$this->LV_InsertXMLChild($vInsertID);
			$this->LV_ChangeChild($vInsertID);
		}
		return $vReturn;
	}
	function LV_InsertXMLChild($vInsertID)
	{

		//Chi tiết báo giá
		$lvsql = "select lv001 from wh_lv0032 where lv002='" . $this->LV_UserID . "'";
		$vresult1 = db_query($lvsql);
		while ($vrow = db_fetch_array($vresult1)) {
			//Attached files
			$lvsql = "insert into wh_lv0022(lv002,lv003,lv004,lv005,lv006,lv007,lv008,lv009,lv010
			,lv011,lv012,lv013,lv014,lv015,lv016,lv017,lv018,lv019,lv020
			,lv021,lv022,lv023,lv024,lv025,lv026,lv027,lv028,lv029,lv030
			,lv031,lv032,lv033,lv034,lv035,lv036,lv037,lv038,lv039,lv040
			,lv041,lv042,lv043,lv044,lv045,lv046,lv047,lv048,lv049,lv050,
			lv051,lv052,lv053,lv054,lv055,lv056,lv057,lv058,lv059,lv060,lv061,lv062,lv063,lv064,lv065,lv068,lv070,lv080,lv081,lv082,lv083,lv084,lv085,lv086,lv087,lv088,lv089,lv090,lv091,lv092,lv093,lv094,lv095,lv101,lv102,lv103,lv104,lv110,lv111,lv112,lv113,lv114,lv115,lv116,lv117,lv118,lv119,lv120,lv121,lv122,lv123,lv124,lv125,lv126,lv127,lv128,lv129,lv130,lv131,lv132,lv133,lv134,lv135,lv136,lv137) select '$vInsertID' lv002,lv003,lv004,lv005,lv006,lv007,lv008,lv009,lv010
			,lv011,lv012,lv013,lv014,lv015,lv016,lv017,lv018,lv019,lv020
			,lv021,lv022,lv023,lv024,lv025,lv026,lv027,lv028,lv029,lv030
			,lv031,lv032,lv033,lv034,lv035,lv036,lv037,lv038,lv039,lv040
			,lv041,lv042,lv043,lv044,lv045,lv046,lv047,lv048,lv049,lv050,
			lv051,lv052,lv053,lv054,lv055,lv056,lv057,lv058,lv059,lv060,lv061,lv062,lv063,lv064,lv065,lv068,lv070,lv080,lv081,lv082,lv083,lv084,lv085,lv086,lv087,lv088,lv089,lv090,lv091,lv092,lv093,lv094,lv095,lv101,lv102,lv103,lv104,lv110,lv111,lv112,lv113,lv114,lv115,lv116,lv117,lv118,lv119,lv120,lv121,lv122,lv123,lv124,lv125,lv126,lv127,lv128,lv129,lv130,lv131,lv132,lv133,lv134,lv135,lv136,lv137 from wh_lv0032 where lv001='" . $vrow['lv001'] . "'";
			$vReturn = db_query($lvsql);
			if ($vReturn) {
				$vAttachID = sof_insert_id();
				$this->InsertLogOperation($this->DateCurrent, 'wh_lv0022.insert', sof_escape_string($lvsql));
				//Attached files
				$lvsql = "insert into erp_minhphuong_documents_v3_0.cri_lv0176(lv002,lv003,lv004,lv005,lv006,lv007,lv008,lv009) select '$vAttachID',lv003,lv004,lv005,lv006,'$vInsertID',lv008,lv009 from erp_minhphuong_documents_v3_0.cri_lv0166 where lv007='" . $vrow['lv001'] . "'";
				$vReturn = db_query($lvsql);
				if ($vReturn) {
					$this->InsertLogOperation($this->DateCurrent, 'erp_minhphuong_documents_v3_0.cri_lv0176.insert', sof_escape_string($lvsql));
					$lvsql1 = "delete from erp_minhphuong_documents_v3_0.cri_lv0166 where lv007='" . $vrow['lv001'] . "'";
					$vReturn = db_query($lvsql1);
					$lvsql1 = "delete from wh_lv0032 where lv001='" . $vrow['lv001'] . "'";
					$vReturn = db_query($lvsql1);
				}

			}
		}
		$lvsql = "select lv001 from cr_lv0170 where lv002='" . $this->LV_UserID . "'";
		$vresult1 = db_query($lvsql);
		while ($vrow = db_fetch_array($vresult1)) {
			//Attached files
			$lvsql = "insert into cr_lv0171(lv002,lv003,lv004,lv005,lv006,lv007,lv008,lv009,lv010) select '$vInsertID',lv003,lv004,lv005,lv006,lv007,lv008,lv009,lv010 from cr_lv0170 where lv001='" . $vrow['lv001'] . "'";
			$vReturn = db_query($lvsql);
			if ($vReturn) {
				$vAttachID = sof_insert_id();
				$this->InsertLogOperation($this->DateCurrent, 'cr_lv0171.insert', sof_escape_string($lvsql));
				//Attached files
				$lvsql = "insert into erp_minhphuong_documents_v3_0.cr_lv0171(lv002,lv003,lv004,lv005,lv006,lv007,lv008) select '$vAttachID',lv003,lv004,lv005,lv006,'$vInsertID',lv008 from erp_minhphuong_documents_v3_0.cr_lv0170 where lv002='" . $vrow['lv001'] . "'";
				$vReturn = db_query($lvsql);
				if ($vReturn) {
					$this->InsertLogOperation($this->DateCurrent, 'erp_minhphuong_documents_v3_0.cr_lv0171.insert', sof_escape_string($lvsql));
					$lvsql1 = "delete from erp_minhphuong_documents_v3_0.cr_lv0170 where lv002='" . $vrow['lv001'] . "'";
					$vReturn = db_query($lvsql1);
					$lvsql1 = "delete from cr_lv0170 where lv001='" . $vrow['lv001'] . "'";
					$vReturn = db_query($lvsql1);
				}

			}
		}

	}
	//////////////////////Buil list////////////////////
//////////////////////Buil list////////////////////
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
		$lvHref = "@02";
		//$lvHref="<span onclick=\"ProcessTextHiden(this)\"><a href=\"javascript:FunctRunning1('@01')\" class=@#04 style=\"text-decoration:none\">@02</a></span>";
		$lvTdH = "<td width=\"@01\" class=\"lvhtable\">@02</td>";
		$lvTdF = "<td align=\"right\"><strong>@01</strong></td>";
		$strF = "<tr><td colspan=\"2\">&nbsp;</td>";
		$lvTd = "<td  class=\"#04\" align=\"@#05\">@02</td>";
		$sqlS = "SELECT *,lv114 lv214,lv114 lv283,lv115 lv069 FROM cr_lv0330 WHERE 1=1  " . $this->GetCondition() . " $strSort LIMIT $curRow, $maxRows";
		$vorder = $curRow;
		$bResult = db_query($sqlS);
		$this->Count = db_num_rows($bResult);
		$strTrH = "";
		$strTr = "";
		for ($i = 0; $i < count($lstArr); $i++) {
			$vTemp = str_replace("@01", "", $lvTdH);
			$vTemp = str_replace("@02", $this->ArrPush[(int) $this->ArrGet[$lstArr[$i]]], $vTemp);
			$strH = $strH . $vTemp;
			$vTempF = str_replace("@01", "<!--" . $lstArr[$i] . "-->", $lvTdF);
			$strF = $strF . $vTempF;
		}
		while ($vrow = db_fetch_array($bResult)) {
			$strL = "";
			$vorder++;


			for ($i = 0; $i < count($lstArr); $i++) {
				switch ($lstArr[$i]) {
					case 'lv199':
						$vChucNang = "
						<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\">
						<tr>
						";
						$vChucNang = $vChucNang . '<td><span onclick="ProcessTextHidenMore(this)"><a href="javascript:FunctRunning1(\'' . $vrow['lv001'] . '\')"><img style="cursor:pointer;width:25px;;padding:5px;"  alt="NoImg" src="../images/icon/work_experience.png" align="middle" border="0" name="new" class="lviconimg"></a></span></td>
						';
						if ($this->GetEdit() == 1) {
							$vChucNang = $vChucNang . '
							<td><img Title="' . (($vrow['lv017'] == 0) ? 'Edit' : 'View') . '" style="cursor:pointer;width:25px;padding:5px;" onclick="Edit(\'' . ($vrow['lv001']) . '\')" alt="NoImg" src="../images/icon/' . (($vrow['lv017'] == 0) ? 'Edt.png' : 'detail.png') . '" align="middle" border="0" name="new" class="lviconimg"></td>
							';
						}

						/*$vChucNang='
																																																																														<img style="cursor:pointer;height:25px;padding:5px;" onclick="Report(\''.base64_encode($vrow['lv001']).'\')" alt="NoImg" src="../images/icon/Rpt.png" align="middle" border="0" name="new" class="lviconimg">
																																																																														<span onclick="ProcessTextHiden(this)"><a href="javascript:FunctRunning1(\''.$vrow['lv001'].'\')"><img style="cursor:pointer;width:25px;;padding:5px;"  alt="NoImg" src="../images/icon/work_experience.png" align="middle" border="0" name="new" class="lviconimg"></a></span>
																																																																														';*/
						$vStr = '	
						';
						$vStr1 = '<td>
									<div style="cursor:pointer;color:blue;" onclick="showDetailHistory(\'chitietid_' . $vrow['lv001'] . '\',\'' . $vrow['lv001'] . '\')">' . '<img style="cursor:pointer;width:25px;;padding:5px;"  alt="NoImg" src="../images/icon/license.png" title="Xem lịch sử duyệt"/>' . '</div>
									<div style="display:none;position:absolute;z-index:999999999999;background:#efefef;" id="chitietid_' . $vrow['lv001'] . '" class="noidung_member">					
										<div class="hd_cafe" style="width:100%">
											<ul class="qlycafe" style="width:100%">
												<li style="padding:10px;"><img onclick="document.getElementById(\'chitietid_' . $vrow['lv001'] . '\').style.display=\'none\';" width="20" src="images/icon/close.png"/></li>
												<li style="padding:10px;"><div style="width:100%;padding-top:2px;">
												<strong>LỊCH SỬ DUYỆT PMH:' . $vrow['lv001'] . '</strong></div>
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
						if ($this->GetRpt() == 1) {
							$vChucNang = $vChucNang . '<td><img title="Báo cáo nhận bảo hành"  style="cursor:pointer;height:25px;padding:5px;" onclick="Report8(\'' . $vrow['lv001'] . '\')" alt="NoImg" src="../images/icon/Rpt.png" align="middle" border="0" name="new" class="lviconimg"></td>';
							$vChucNang = $vChucNang . '<td><img title="Báo cáo trả bảo hành"  style="cursor:pointer;height:25px;padding:5px;" onclick="Report9(\'' . $vrow['lv001'] . '\')" alt="NoImg" src="../images/icon/listemp.png" align="middle" border="0" name="new" class="lviconimg"></td>';
							$vChucNang = $vChucNang . '<td><img title="Báo cáo hình ảnh bảo hành" style="cursor:pointer;height:25px;padding:5px;" onclick="Report10(\'' . $vrow['lv001'] . '\')" alt="NoImg" src="../images/icon/note_r.png" align="middle" border="0" name="new" class="lviconimg"></td>';

						}
						if ($this->GetApr() == 1 && $vrow['lv006'] == 0) {
							$vChucNang = $vChucNang . '<td><input type="button" value="Khoá" style="padding:3px;border-radius:3px;font-weight:bold;cursor:pointer;" onclick="Approvals(\'' . $vrow['lv001'] . '@\')"/></td>';
						}
						if ($this->GetUnApr() == 1 && $vrow['lv006'] == 1 && $vrow['lv017'] == 0) {
							$vChucNang = $vChucNang . '<td><input type="button" value="Mở khoá" style="padding:3px;border-radius:3px;font-weight:bold;cursor:pointer;" onclick="UnApprovals(\'' . $vrow['lv001'] . '@\')"/></td>';
						}
						if ($vrow['lv006'] == 1 && $vrow['lv017'] == 0) {
							$vChucNang = $vChucNang . '<td><input type="button" value="Nhận bảo hành" style="padding:3px;border-radius:3px;font-weight:bold;cursor:pointer;" onclick="NhanBaoHanh(\'' . $vrow['lv001'] . '@\')"/></td>';
						}
						$vChucNang = $vChucNang . "</tr></table>";
						$vTemp = str_replace("@02", $vChucNang, $this->Align($lvTd, (int) $this->ArrView[$lstArr[$i]]));
						break;

					default:
						$vTemp = str_replace("@02", str_replace("@02", $this->getvaluelink($lstArr[$i], $this->FormatView($vrow[$lstArr[$i]], (int) $this->ArrView[$lstArr[$i]])), str_replace("@01", $vrow['lv001'], $lvHref)), $this->Align($lvTd, (int) $this->ArrView[$lstArr[$i]]));
						break;
				}
				$strL = $strL . $vTemp;
			}
			$strTr = $strTr . str_replace("@#01", $strL, str_replace("@02", $vrow['lv001'], str_replace("@03", $vorder, str_replace("@01", $vorder % 2, $lvTr))));
			if ($vrow['lv011'] == 1)
				$strTr = str_replace("@#04", "lvlineapproval", $strTr);
			else
				$strTr = str_replace("@#04", "", $strTr);

		}

		$strF = $strF . "</tr>";

		$lvTable = str_replace("@#02", $strF, $lvTable);
		$strTrH = str_replace("@#01", $strH, $lvTrH);
		return str_replace("@#01", $strTrH . $strTr, $lvTable);
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
			window.open('" . $this->Dir . "cr_lv0330/?lang=" . $this->lang . "&childfunc='+value+'&ID=" . base64_encode($this->lv002) . "','','width=800,height=600,left=200,top=100,screenX=0,screenY=100,resizable=yes,status=no,scrollbars=yes,menubar=yes');
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
			if ($lvArrOrder[$i] == NULL || $lvArrOrder[$i] == "") {
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
		$lvTdH = "<td width=\"@01\" class=\"lvhtable\">@02</td>";
		$lvTdF = "<td align=\"right\"><strong>@01</strong></td>";
		$strF = "<tr><td colspan=\"1\">&nbsp;</td>";
		$lvTd = "<td  class=\"#04\" align=\"@#05\">@02</td>";
		$sqlS = "SELECT *,lv114 lv214,lv114 lv283,lv115 lv069 FROM cr_lv0330 WHERE 1=1  " . $this->RptCondition . " $strSort";
		$vorder = $curRow;
		$bResult = db_query($sqlS);
		$this->Count = db_num_rows($bResult);
		$strTrH = "";
		$strTr = "";
		for ($i = 0; $i < count($lstArr); $i++) {
			$vTemp = str_replace("@01", "", $lvTdH);
			$vTemp = str_replace("@02", $this->ArrPush[(int) $this->ArrGet[$lstArr[$i]]], $vTemp);
			$strH = $strH . $vTemp;
			$vTempF = str_replace("@01", "<!--" . $lstArr[$i] . "-->", $lvTdF);
			$strF = $strF . $vTempF;
		}

		while ($vrow = db_fetch_array($bResult)) {
			$strL = "";
			$vorder++;

			for ($i = 0; $i < count($lstArr); $i++) {
				switch ($lstArr[$i]) {

					default:
						$vTemp = str_replace("@02", $this->getvaluelink($lstArr[$i], $this->FormatView($vrow[$lstArr[$i]], (int) $this->ArrView[$lstArr[$i]])), $this->Align($lvTd, (int) $this->ArrView[$lstArr[$i]]));
						break;
				}
				$strL = $strL . $vTemp;
			}
			$strTr = $strTr . str_replace("@#01", $strL, str_replace("@02", $vrow['lv001'], str_replace("@03", $vorder, str_replace("@01", $vorder % 2, $lvTr))));
			if ($vrow['lv017'] == 1)
				$strTr = str_replace("@#04", "lvlineapproval", $strTr);
			else
				$strTr = str_replace("@#04", "", $strTr);

		}
		$strF = $strF . "</tr>";

		$lvTable = str_replace("@#02", $strF, $lvTable);
		$strTrH = str_replace("@#01", $strH, $lvTrH);
		return str_replace("@#01", $strTrH . $strTr, $lvTable);
	}
	//////////////////////Buil list////////////////////
	function LV_BuilListReportOtherNew($lvList, $lvFrom, $lvChkAll, $lvChk, $curRow, $maxRows, $paging, $lvOrderList)
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
		$lvTdH = "<td width=\"@01\" class=\"lvhtable\">@02</td>";
		$lvTd = "<td  class=@#04>@02</td>";
		$sqlS = "SELECT *,lv114 lv189 FROM cr_lv0330 WHERE 1=1  " . $this->GetCondition() . " $strSort LIMIT $curRow, $maxRows";
		$vorder = $curRow;
		$bResult = db_query($sqlS);
		$this->Count = db_num_rows($bResult);
		$strTrH = "";
		$strTr = "";
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
			if ($vrow['lv011'] == 1)
				$strTr = str_replace("@#04", "", $strTr);

		}
		$strTrH = str_replace("@#01", $strH, $lvTrH);
		return str_replace("@#01", $strTrH . $strTr, $lvTable);
	}
	//////////////////////Buil list////////////////////
	function LV_BuilListReportOther($lvList, $lvFrom, $lvChkAll, $lvChk, $curRow, $maxRows, $paging, $lvOrderList)
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
		$lvTable = "
		<div align=\"center\" class=lv0>" . ($this->ArrPush[0]) . "</div>
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
		$lvTdH = "<td width=\"@01\" class=\"lvhtable\">@02</td>";
		$lvTd = "<td  class=\"#04\" align=\"@#05\">@02</td>";
		$sqlS = "SELECT * FROM cr_lv0330 WHERE 1=1  " . $this->GetCondition() . " $strSort LIMIT $curRow, $maxRows";
		$vorder = $curRow;
		$bResult = db_query($sqlS);
		$this->Count = db_num_rows($bResult);
		$strTrH = "";
		$strTr = "";
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
			if ($vrow['lv011'] == 1)
				$strTr = str_replace("@#04", "", $strTr);

		}
		$strF = $strF . "</tr>";
		$strF = str_replace("<!--lv012-->", $this->FormatView($vSumTienHD, $this->ArrView['lv012']), $strF);
		$strF = str_replace("<!--lv013-->", $this->FormatView($vSumTienPC, $this->ArrView['lv013']), $strF);
		$strF = str_replace("<!--lv099-->", $this->FormatView($vSumTienHD - $vSumTienPC, 10), $strF);
		$lvTable = str_replace("@#02", $strF, $lvTable);
		$strTrH = str_replace("@#01", $strH, $lvTrH);
		return str_replace("@#01", $strTrH . $strTr, $lvTable);
	}
	//////////////////////Buil list////////////////////
	function LV_BuilListReportMini($lvList, $lvFrom, $lvChkAll, $lvChk, $curRow, $maxRows, $paging, $lvOrderList, $lvDateSort)
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
		$sqlS = "SELECT *,lv114 lv189 FROM cr_lv0330 WHERE 1=1 and lv004 like '$lvDateSort%' " . $this->GetConditionMini() . " $strSort LIMIT $curRow, $maxRows";
		$vorder = $curRow;
		$bResult = db_query($sqlS);
		$this->Count = db_num_rows($bResult);
		$strTrH = "";
		$strTr = "";
		for ($i = 0; $i < count($lstArr); $i++) {
			$vTemp = str_replace("@01", "", $lvTdH);
			$vTemp = str_replace("@02", $this->ArrPush[(int) $this->ArrGet[$lstArr[$i]]], $vTemp);
			$strH = $strH . $vTemp;

		}

		while ($vrow = db_fetch_array($bResult)) {
			$strL = "";
			$vorder++;
			for ($i = 0; $i < count($lstArr); $i++) {
				if ($lstArr[$i] == "lv012") {
					$vTemp = str_replace("@02", $this->getvaluelink($lstArr[$i], $this->FormatView($this->LV_GetBLMoney($vrow['lv001']), (int) $this->ArrView[$lstArr[$i]])), $this->Align($lvTd, (int) $this->ArrView[$lstArr[$i]]));
				} elseif ($lstArr[$i] == "lv013") {
					$vTemp = str_replace("@02", $this->getvaluelink($lstArr[$i], $this->FormatView($this->LV_GetPCMoney($vrow['lv001']), (int) $this->ArrView[$lstArr[$i]])), $this->Align($lvTd, (int) $this->ArrView[$lstArr[$i]]));
				} else
					$vTemp = str_replace("@02", $this->getvaluelink($lstArr[$i], $this->FormatView($vrow[$lstArr[$i]], (int) $this->ArrView[$lstArr[$i]])), $this->Align($lvTd, (int) $this->ArrView[$lstArr[$i]]));
				$strL = $strL . $vTemp;
			}


			$strTr = $strTr . str_replace("@#01", $strL, str_replace("@02", $vrow['lv001'], str_replace("@03", $vorder, str_replace("@01", $vorder % 2, $lvTr))));
			if ($vrow['lv007'] == 1)
				$strTr = str_replace("@#04", "lvlineapproval", $strTr);
			else
				$strTr = str_replace("@#04", "", $strTr);

		}
		$strTrH = str_replace("@#01", $strH, $lvTrH);
		return str_replace("@#01", $strTrH . $strTr, $lvTable);
	}
	public function LV_LinkField($vFile, $vSelectID)
	{
		return ($this->CreateSelect($this->sqlcondition($vFile, $vSelectID), 0));
	}
	private function sqlcondition($vFile, $vSelectID)
	{
		$vsql = "";
		switch ($vFile) {
			case 'lv000':
				$vsql = "select lv001,lv002,IF(concat(lv001,'')='$vSelectID',1,0) lv003 from  cr_lv0039";
				break;
			case 'lv002':
				$vsql = "select lv001,lv002,IF(lv001='$vSelectID',1,0) lv003 from  sl_lv0001";
				break;
			case 'lv007':
				$vsql = "select lv001,lv004 lv002,IF(lv001='$vSelectID',1,0) lv003 from  sl_lv0009 order by lv005 asc";
				break;
			case 'lv008':
				$vsql = "select lv001,lv002,IF(lv001='$vSelectID',1,0) lv003 from  sl_lv0008";
				break;
			case 'lv012':
				$vsql = "select lv001,lv002,IF(lv001='$vSelectID',1,0) lv003 from  hr_lv0018";
				break;
			case 'lv014':
				$vsql = "select lv001,lv002 lv002,IF(lv001='$vSelectID',1,0) lv003 from  hr_lv0020 ";
				break;
			case 'lv015':
				$vsql = "select lv001,lv002 lv002,IF(lv001='$vSelectID',1,0) lv003 from  hr_lv0020 ";
				break;
			case 'lv016':
				$vsql = "select lv001,lv002 lv002,IF(lv001='$vSelectID',1,0) lv003 from  hr_lv0020";
				break;
			case 'lv017':
				$vsql = "select lv001,lv002 lv002,IF(lv001='$vSelectID',1,0) lv003 from  cr_lv0331 ";
				break;
			case 'lv361':
				$vsql = "select lv001,lv002 lv002,IF(lv001='$vSelectID',1,0) lv003 from  cr_lv0334 order by lv004 asc";
				break;
			case 'lv363':
				$vsql = "select lv001,lv002 lv002,IF(lv001='$vSelectID',1,0) lv003 from  cr_lv0334 order by lv004 asc ";
				break;
			case 'lv365':
				$vsql = "select lv001,lv002 lv002,IF(lv001='$vSelectID',1,0) lv003 from  cr_lv0334 order by lv004 asc";
				break;
			case 'lv367':
				$vsql = "select lv001,lv002 lv002,IF(lv001='$vSelectID',1,0) lv003 from  cr_lv0334 order by lv004 asc";
				break;
			case 'lv369':
				$vsql = "select lv001,lv002 lv002,IF(lv001='$vSelectID',1,0) lv003 from  cr_lv0334 order by lv004 asc";
				break;
			case 'lv371':
				$vsql = "select lv001,lv002 lv002,IF(lv001='$vSelectID',1,0) lv003 from  cr_lv0334 order by lv004 asc";
				break;
			case 'lv027':
				$vsql = "select lv001,lv002,IF(concat('',lv001)='$vSelectID',1,0) lv003 from  cr_lv0155";
				break;
			case 'lv114':
				$vsql = "select lv001,concat(lv004,' ',DATE_FORMAT(lv005,'%d/%m/%Y %H:%i')) lv002,IF(lv001='$vSelectID',1,0) lv003 from  cr_lv0005 where (lv006='$this->LV_UserID' or lv007='$this->LV_UserID')  and lv011='1' and ((lv003='PMH') or lv001='$vSelectID')  order by lv010 DESC ";
				break;
			case 'lv888':
				$vsql = "select lv001,lv002 lv002,IF(lv001='$vSelectID',1,0) lv003 from  cr_lv0328 order by lv002";
				break;
		}
		return $vsql;
	}
	public function getvaluelink($vFile, $vSelectID)
	{
		if ($this->ArrGetValueLink[$vFile][$vSelectID][0])
			return $this->ArrGetValueLink[$vFile][$vSelectID][1];
		if ($vSelectID == "") {
			return $vSelectID;
		}
		switch ($vFile) {
			case 'lv069':
				$vsql = "select lv069 lv001,lv069 lv002 from  sl_lv0013 where lv115='$vSelectID'";
				break;
			case 'lv002':
				$vsql = "select lv001,lv002,IF(lv001='$vSelectID',1,0) lv003 from  sl_lv0001 where lv001='$vSelectID'";
				break;
			case 'lv007':
				if ($this->lang == 'VN')
					$vsql = "select lv001,lv004 lv002,IF(lv001='$vSelectID',1,0) lv003 from  sl_lv0009 where lv001='$vSelectID'";
				else
					$vsql = "select lv001,lv002,IF(lv001='$vSelectID',1,0) lv003 from  sl_lv0009 where lv001='$vSelectID'";
				break;
			case 'lv014':
				$vsql = "select lv001,lv002,IF(lv001='$vSelectID',1,0) lv003 from  hr_lv0020 where lv001='$vSelectID'";
				break;
			case 'lv015':
				$vsql = "select lv001,lv002,IF(lv001='$vSelectID',1,0) lv003 from  hr_lv0020 where lv001='$vSelectID'";
				break;
			case 'lv016':
				$vsql = "select lv001,lv002,IF(lv001='$vSelectID',1,0) lv003 from  hr_lv0020 where lv001='$vSelectID'";
				break;
			case 'lv316':
				$lvopt = 2;
				$vsql = "select lv001,lv002,IF(lv001='$vSelectID',1,0) lv003 from  hr_lv0020 where lv001='$vSelectID'";
				break;
			case 'lv318':
				$lvopt = 2;
				$vsql = "select lv001,lv002,IF(lv001='$vSelectID',1,0) lv003 from  hr_lv0020 where lv001='$vSelectID'";
				break;
			case 'lv361':
				$vsql = "select lv001,lv002 lv002,IF(lv001='$vSelectID',1,0) lv003 from  cr_lv0334 where lv001='$vSelectID'";
				break;
			case 'lv363':
				$vsql = "select lv001,lv002 lv002,IF(lv001='$vSelectID',1,0) lv003 from  cr_lv0334 where lv001='$vSelectID'";
				break;
			case 'lv365':
				$vsql = "select lv001,lv002 lv002,IF(lv001='$vSelectID',1,0) lv003 from  cr_lv0334 where lv001='$vSelectID'";
				break;
			case 'lv367':
				$vsql = "select lv001,lv002 lv002,IF(lv001='$vSelectID',1,0) lv003 from  cr_lv0334 where lv001='$vSelectID'";
				break;
			case 'lv369':
				$vsql = "select lv001,lv002 lv002,IF(lv001='$vSelectID',1,0) lv003 from  cr_lv0334 where lv001='$vSelectID'";
				break;
			case 'lv371':
				$vsql = "select lv001,lv002 lv002,IF(lv001='$vSelectID',1,0) lv003 from  cr_lv0334 where lv001='$vSelectID'";
				break;
			case 'lv010':
				$lvopt = 0;
				$vsql = "select lv001,lv002,IF(lv001='$vSelectID',1,0) lv003 from  hr_lv0020 where lv001='$vSelectID'";
				break;
			case 'lv027':
				$vsql = "select lv001,lv002,IF(concat('',lv001)='$vSelectID',1,0) lv003 from  cr_lv0155 where lv001='$vSelectID'";
				break;
			case 'lv189':
				$lvopt = 0;
				$vsql = "select A.lv001 lv001,B.lv009 lv002 from  cr_lv0005 A inner join cr_lv0004 B on A.lv002=B.lv001  where A.lv001='$vSelectID' ";
				break;
			case 'lv283':
				$lvopt = 0;
				$vsql = "select A.lv001 lv001,B.lv083 lv002 from  cr_lv0005 A inner join cr_lv0004 B on A.lv002=B.lv001  where A.lv001='$vSelectID' ";
				break;
			case 'lv114':// du an
				$vsql = "select A.lv001,B.lv009 lv002,'' lv003 from  cr_lv0005 A inner join cr_lv0004 B on A.lv002=B.lv001 where  A.lv001='$vSelectID'";
				break;
			case 'lv214':
				$vsql = "select A.lv001,B.lv002 lv002,'' lv003 from  cr_lv0005 A inner join cr_lv0004 B on A.lv002=B.lv001 where  A.lv001='$vSelectID'";
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

	function getAll()
	{
		$vArrRe = [];
		$vsql = "SELECT a.*, c.lv002 as tenDuAn, c.lv083 as diaChi, c.lv001 as maDuAn FROM `cr_lv0330` a JOIN cr_lv0005 b ON a.lv114 = b.lv001 JOIN cr_lv0004 c ON c.lv001 = b.lv002";
		$vresult = db_query($vsql);
		while ($vrow = mysqli_fetch_assoc($vresult)) {
			// Giữ nguyên mã cột từ cơ sở dữ liệu
			$vArrRe[] = $vrow;
		}
		return $vArrRe;
	}

	function capNhat(
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
		$lv809,
	) {
		$success = true; // Biến để theo dõi trạng thái thực hiện
		$errorMessage = '';
		$vsql = "UPDATE cr_lv0330 SET 
				lv004 = '" . sof_escape_string($lv004) . "',
				lv005 = '" . sof_escape_string($lv005) . "',
				lv012 = '" . sof_escape_string($lv012) . "',
				lv013 = '" . sof_escape_string($lv013) . "',
				lv014 = '" . sof_escape_string($lv014) . "',
				lv015 = '" . sof_escape_string($lv015) . "',
				lv016 = '" . sof_escape_string($lv016) . "',
				lv353 = '" . sof_escape_string($lv353) . "',
				lv360 = '" . sof_escape_string($lv360) . "',
				lv361 = '" . sof_escape_string($lv361) . "',
				lv362 = '" . sof_escape_string($lv362) . "',
				lv363 = '" . sof_escape_string($lv363) . "',
				lv364 = '" . sof_escape_string($lv364) . "',
				lv365 = '" . sof_escape_string($lv365) . "',
				lv366 = '" . sof_escape_string($lv366) . "',
				lv367 = '" . sof_escape_string($lv367) . "',
				lv368 = '" . sof_escape_string($lv368) . "',
				lv369 = '" . sof_escape_string($lv369) . "',
				lv370 = '" . sof_escape_string($lv370) . "',
				lv371 = '" . sof_escape_string($lv371) . "',
				lv802 = '" . sof_escape_string($lv802) . "',
				lv809 = '" . sof_escape_string($lv809) . "'
			WHERE lv001 = '" . sof_escape_string($lv001) . "'";


		$vReturn = db_query($vsql);
		if (!$vReturn) {
			$success = false;
			$errorMessage .= "Lỗi cập nhật";
		}

		return [
			'success' => $success,
			'message' => "Cập nhật thành công"
		];
	}

	function xoaPhieu($lv001)
	{
		$success = true;
		$errorMessage = '';
		$vsql = "DELETE FROM cr_lv0330 WHERE lv001 = '" . sof_escape_string($lv001) . "'";
		$vReturn = db_query($vsql);
		if (!$vReturn) {
			$success = false;
			$errorMessage .= "Lỗi xoá dữ liệu";
		}
		return [
			'success' => $success,
			'message' => "Xoá phiếu thành công"
		];
	}



}
?>