<?php

/////////////coding hr_kb0002///////////////
class   hr_kb0006 extends lv_controler
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
	public $lv015 = null;
	public $lv016 = null;
	public $lv017 = null;
	public $lv018 = null;
	public $lv019 = null;
	public $lv020 = null;
	public $lv021 = null;
	public $lv022 = null;
	public $lv023 = null;
	///////////
	public $DefaultFieldList = "lv199,lv015,lv829,lv022,lv003,lv025,lv016,lv017,lv098,lv008,lv009,lv013,lv021";
	////////////////////GetDate
	public $DateDefault = "1900-01-01";
	public $DateCurrent = "1900-01-01";
	public $Count = null;
	public $paging = null;
	public $lang = null;
	protected $objhelp = 'hr_kb0002';
	////////////
	var $ArrOther = array();
	var $ArrPush = array();
	var $ArrFunc = array();
	var $ArrGet = array("lv001" => "2", "lv002" => "3", "lv003" => "4", "lv004" => "5", "lv005" => "6", "lv006" => "7", "lv007" => "8", "lv008" => "9", "lv009" => "10", "lv010" => "11", "lv011" => "12", "lv012" => "13", "lv013" => "14", "lv014" => "15", "lv015" => "16", "lv016" => "17", "lv017" => "18", "lv018" => "19", "lv019" => "20", "lv020" => "21", "lv021" => "22", "lv022" => "23", "lv023" => "24", "lv024" => "25", "lv025" => "26", "lv098" => "99", "lv199" => "200", "lv829" => "830");
	var $ArrView = array("lv001" => "0", "lv002" => "0", "lv003" => "0", "lv004" => "0", "lv005" => "2", "lv006" => "0", "lv007" => "2", "lv008" => "0", "lv009" => "0", "lv010" => "0", "lv011" => "0", "lv012" => "0", "lv013" => "0", "lv014" => "0", "lv015" => "0", "lv016" => "4", "lv017" => "4", "lv018" => "4", "lv019" => "4", "lv020" => "0", "lv021" => "0", "lv022" => "0", "lv023" => "0", "lv024" => "2", "lv025" => "22", "lv098" => "10", "lv199" => "0");
	public $LE_CODE = "NjlIUS02VFdULTZIS1QtNlFIQQ==";

	function __construct($vCheckAdmin, $vUserID, $vright)
	{
		$this->DateCurrent = GetServerDate() . " " . GetServerTime();
		$this->Set_User($vCheckAdmin, $vUserID, $vright);
		$this->isRel = 1;
		$this->isHelp = 1;
		$this->isConfig = 0;
		$this->isFil = 1;
		$this->isAdd = 0;
		$this->isEdit = 0;
		$this->isDel = 0;
		$this->lang = $_GET['lang'];
	}

	function LV_Load()
	{
		$vsql = "select * from  hr_kb0002";
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
			$this->lv018 = $vrow['lv018'];
			$this->lv019 = $vrow['lv019'];
			$this->lv020 = $vrow['lv020'];
			$this->lv021 = $vrow['lv021'];
			$this->lv022 = $vrow['lv022'];
			$this->lv023 = $vrow['lv023'];
			$this->lv024 = $vrow['lv024'];
			$this->lv025 = $vrow['lv025'];
		}
	}
	function LV_LoadID($vlv001)
	{
		$lvsql = "select * from  hr_kb0002 Where lv001='$vlv001'";
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
			$this->lv018 = $vrow['lv018'];
			$this->lv019 = $vrow['lv019'];
			$this->lv020 = $vrow['lv020'];
			$this->lv021 = $vrow['lv021'];
			$this->lv022 = $vrow['lv022'];
			$this->lv023 = $vrow['lv023'];
		}
	}
	function LV_CheckPhepLienKe($vMaNV, $vIdPhep, $vDateFrom, $vDateTo)
	{
		//Do phép liền kề
		$sqlS1 = "SELECT sum(DATEDIFF(lv017,lv016)+1) NumsRequire,sum(DATEDIFF(lv017,lv016)+IF(LEFT(lv003,3)='1/2',0.5,1)) NumsRequireMore  FROM hr_kb0002 WHERE (hr_kb0002.lv003<>'CT' and hr_kb0002.lv003<>'QCC') and hr_kb0002.lv001 <>'$vIdPhep' and lv015='$vMaNV' and ( (ADDDATE('$vDateFrom',INTERVAL -1 DAY )<=substr(lv017,1,10) and ADDDATE('$vDateFrom',INTERVAL 1 DAY)>=substr(lv016,1,10)) or (ADDDATE('$vDateTo',INTERVAL -1 DAY )<=substr(lv017,1,10) and ADDDATE('$vDateTo',INTERVAL 1 DAY)>=substr(lv016,1,10)) )	and lv004<>2 and lv021<>2 and (lv022 not in (13,2,3,1,4,8,5,6,9,7))	";
		$bResult = db_query($sqlS1);
		while ($vrow = db_fetch_array($bResult)) {
			return $vrow['NumsRequireMore'];
		}
	}

	function LV_AddtTimeAuto($vEmployeeID, $vDate, $vTime)
	{
		if ($vDate >= '2023-08-01') {
			$lvsql = "insert into tc_lv0012 (lv001,lv002,lv003,lv004,lv006,lv005,lv098) values('$vEmployeeID','$vDate','$vTime','I','0','$this->LV_UserID',now())";
			$vReturn = db_query($lvsql);
			if ($vReturn) {
				$this->InsertLogOperation($this->DateCurrent, 'tc_lv0012.insert', sof_escape_string($lvsql));
			}
		}
	}
	function LV_Aproval($lvarr)
	{
		// if($this->isApr==0) return false;
		$sqlS1 = "SELECT *,DATEDIFF(lv017,lv016) NumsRequire FROM hr_kb0002 WHERE  hr_kb0002.lv001 IN ($lvarr)";
		$bResult = db_query($sqlS1);
		while ($vrow = db_fetch_array($bResult)) {

			$vSoPhepKLDaDung = 0;
			if ($vrow['lv003'] == 'KL' || $vrow['lv003'] == '1/2KL') {
				$vCodeList = "'KL','1/2KL'";
				$vSoPhepKLDaDung = $this->motc_lv0008->get_count_codeidyear($vrow['lv015'], getyear($vrow['lv016']), $vCodeList);
			}
			$vDateFrom = substr($vrow['lv016'], 0, 10);
			$vDateTo = substr($vrow['lv017'], 0, 10);
			$vIsTrue = true;
			if ($vrow['lv022'] > 0) {
				$vIsTrue = false;
				$vNghiLienKe = 0;
			} else {
				$vIsTrue = true;
				$vNghiLienKe = $this->LV_CheckPhepLienKe($vrow['lv015'], $vrow['lv001'], $vDateFrom, $vDateTo);
			}
			$vNgayNghi = $vrow['NumsRequire'] + 1;
			if ($vIsTrue == true && (($vrow['lv003'] <> 'CT' && $vrow['lv003'] <> 'QCC' && ($vNghiLienKe + $vNgayNghi) > 3) || (($vrow['lv003'] == 'KL' || $vrow['lv003'] == '1/2KL') && ($vNgayNghi + $vSoPhepKLDaDung) > 7))) {
				if (($vrow['lv003'] == 'KL' || $vrow['lv003'] == '1/2KL') && ($vNgayNghi + $vSoPhepKLDaDung) > 7)
					$lvsql = "Update hr_kb0002 set lv004='1',lv010='Phép KL đã dùng:" . ($vNgayNghi + $vSoPhepKLDaDung) . " ngày>=7 ngày.',lv013='$this->LV_UserID',lv047=now()  WHERE hr_kb0002.lv001 ='" . $vrow['lv001'] . "' ";
				else
					$lvsql = "update kr_kb0002 set lv006=1,lv047=NOW(),lv024='' where lv001='" . $vrow['lv001'] . "'";
				$vReturn = db_query($lvsql);
				if ($vReturn) {
					$this->InsertLogOperation($this->DateCurrent, 'hr_kb0002.approval', sof_escape_string($lvsql));
				}
			} else {
				$lvsql = "Update hr_kb0002 set lv006='1',lv013='$this->LV_UserID',lv047=now()  WHERE hr_kb0002.lv001 ='" . $vrow['lv001'] . "'";
				$vReturn = db_query($lvsql);
				if ($vReturn) {
					$this->InsertLogOperation($this->DateCurrent, 'hr_kb0002.approval', sof_escape_string($lvsql));
					include_once('jo_lv0016.php');
					$mojo_lv0016 = new jo_lv0016($_SESSION['ERPSOFV2RRight'], $_SESSION['ERPSOFV2RUserID'], 'Jo0016');
					$capDuyet = $mojo_lv0016->getCapDuyet();
					if ($capDuyet == 1) {
						include_once('hr_kb0002.php');
						include_once('tc_lv0012.php');
						$mohr_kb0002 = new hr_kb0002($_SESSION['ERPSOFV2RRight'], $_SESSION['ERPSOFV2RUserID'], 'Kb0002');
						$id = $vrow['lv001']; // Đảm bảo là số/nguyên, không phải chuỗi lạ
						$mohr_kb0002->KB_LoadID_1($id);
						$datetime = $mohr_kb0002->lv003;
						// Tách ngày
						$ngay = date('Y-m-d', strtotime($datetime));
						// Tách giờ
						$gio = date('H:i:s', strtotime($datetime));
						if ($mohr_kb0002->lv004 == 1)
							$loaiChamCong = 'IN';
						else
							$loaiChamCong = 'OUT';
						$motc_lv0012 = new tc_lv0012($_SESSION['ERPSOFV2RRight'], $_SESSION['ERPSOFV2RUserID'], 'Tc0012');
						$motc_lv0012->LV_InsertAuto_1($mohr_kb0002->lv002, $ngay, $gio, $loaiChamCong, $this->LV_UserID);
					}
				}
				if ($vrow['lv003'] == 'QCC') {
					$vEmployeeID = $vrow['lv015'];
					$vDate = substr($vrow['lv016'], 0, 10);
					$vTime = substr($vrow['lv016'], 11, 8);
					$this->LV_AddtTimeAuto($vEmployeeID, $vDate, $vTime);
				}
			}
		}
		return $vReturn;
	}
	function LV_UnAproval($lvarr)
	{
		// if ($this->isUnApr == 0) return false;
		$lvsql = "Update hr_kb0002 set lv006='2',lv021='2',lv046='2',lv013='$this->LV_UserID',lv045=now()  WHERE hr_kb0002.lv001 IN ($lvarr)  ";
		$vReturn = db_query($lvsql);
		if ($vReturn) {
			$this->InsertLogOperation($this->DateCurrent, 'hr_kb0002.unapproval', sof_escape_string($lvsql));
		}
		return $vReturn;
	}
	function LV_Insert()
	{

		if ($this->isAdd == 0) return false;
		$this->lv005 = ($this->lv005 != "") ? recoverdate(($this->lv005), $this->lang) : $this->DateDefault;
		$this->lv007 = ($this->lv007 != "") ? recoverdate(($this->lv007), $this->lang) : $this->DateDefault;
		$this->lv016 = ($this->lv016 != "") ? recoverdate(($this->lv016), $this->lang) : $this->DateDefault;
		$this->lv017 = ($this->lv017 != "") ? recoverdate(($this->lv017), $this->lang) : $this->DateDefault;
		$this->lv018 = ($this->lv018 != "") ? recoverdate(($this->lv018), $this->lang) : $this->DateDefault;
		$this->lv019 = ($this->lv019 != "") ? recoverdate(($this->lv019), $this->lang) : $this->DateDefault;
		$lvsql = "insert into hr_kb0002 (lv001,lv002,lv003,lv004,lv006,lv008,lv011,lv012,lv013,lv014,lv015,lv016,lv017,lv018,lv019,lv020,lv021,lv022,lv023) values('$this->lv001','$this->lv002','$this->lv003','$this->lv004','$this->lv006','$this->lv008','$this->lv011','$this->lv012','$this->lv013','$this->lv014','$this->lv015','$this->lv016','$this->lv017','$this->lv018','$this->lv019','$this->lv020','$this->lv021','$this->lv022','$this->lv023')";
		$vReturn = db_query($lvsql);
		if ($vReturn) $this->InsertLogOperation($this->DateCurrent, 'hr_kb0002.insert', sof_escape_string($lvsql));
		return $vReturn;
	}
	function LV_Update()
	{
		if ($this->isEdit == 0) return false;
		$this->lv005 = ($this->lv005 != "") ? recoverdate(($this->lv005), $this->lang) : $this->DateDefault;
		$this->lv007 = ($this->lv007 != "") ? recoverdate(($this->lv007), $this->lang) : $this->DateDefault;
		$this->lv016 = ($this->lv016 != "") ? recoverdate(($this->lv016), $this->lang) : $this->DateDefault;
		$this->lv017 = ($this->lv017 != "") ? recoverdate(($this->lv017), $this->lang) : $this->DateDefault;
		$this->lv018 = ($this->lv018 != "") ? recoverdate(($this->lv018), $this->lang) : $this->DateDefault;
		$this->lv019 = ($this->lv019 != "") ? recoverdate(($this->lv019), $this->lang) : $this->DateDefault;
		$lvsql = "Update hr_kb0002 set lv002='$this->lv002',lv003='$this->lv003',lv004='$this->lv004',lv006='$this->lv006',lv008='$this->lv008',lv011='$this->lv011',lv012='$this->lv012',lv013='$this->lv013',lv014='$this->lv014',lv015='$this->lv015',lv016='$this->lv016',lv017='$this->lv017',lv018='$this->lv018',lv019='$this->lv019',lv020='$this->lv020',lv021='$this->lv021',lv022='$this->lv022' where  lv001='$this->lv001';";
		$vReturn = db_query($lvsql);
		if ($vReturn) $this->InsertLogOperation($this->DateCurrent, 'hr_kb0002.update', sof_escape_string($lvsql));
		return $vReturn;
	}
	function LV_Delete($lvarr)
	{
		if ($this->isDel == 0) return false;
		$lvsql = "DELETE FROM hr_kb0002  WHERE hr_kb0002.lv001 IN ($lvarr) and hr_kb0002.lv021<=0 and (select count(*) from jo_lv005 B where  B.lv002= hr_kb0002.lv001)<=0 ";
		$vReturn = db_query($lvsql);
		if ($vReturn) $this->InsertLogOperation($this->DateCurrent, 'hr_kb0002.delete', sof_escape_string($lvsql));
		return $vReturn;
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
	function LV_GetDep($vDepID)
	{
		if ($vDepID == "") return '';
		$vReturn = "'" . str_replace(",", "','", $vDepID) . "'";
		if ($this->isChildCheck . "" == "") $this->isChildCheck = 1;
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
		if (trim($vDepID) == "") return '';
		$vReturn = "'" . str_replace(",", "','", $vDepID) . "'";
		$vsql = "select lv001 from  hr_lv0002 where lv002 in ($vReturn) order by lv103 asc";
		$bResult = db_query($vsql);
		while ($vrow = db_fetch_array($bResult)) {
			$vReturn = $vReturn . "," . $this->LV_GetChildDep($vrow['lv001']);
		}
		return $vReturn;
	}
	function LV_GetDepParentPrivate($vDepID)
	{
		$vListDept = $this->LV_GetDep($vDepID);
		$lvsql = "select A.lv001 from hr_lv0020 A inner join hr_lv0002 B on A.lv029=B.lv001 where (A.lv029 in ($vListDept))";
		$lvResult = db_query($lvsql);
		while ($row = db_fetch_array($lvResult)) {
			if ($vResult == "")
				$vResult = "'" . $row['lv001'] . "'";
			else
				$vResult = $vResult . ",'" . $row['lv001'] . "'";;
		}
		if ($vResult == '')
			return "''";
		else
			return $vResult;
	}
	function LV_GetDepParent($vDepID)
	{
		$vListDept = $this->LV_GetDep($vDepID);
		$lvsql = "select A.lv001 from hr_lv0020 A inner join hr_lv0002 B on A.lv029=B.lv001 where (A.lv029 in ($vListDept)) or (B.lv100='$this->LV_UserID')";
		$lvResult = db_query($lvsql);
		while ($row = db_fetch_array($lvResult)) {
			if ($vResult == "")
				$vResult = "'" . $row['lv001'] . "'";
			else
				$vResult = $vResult . ",'" . $row['lv001'] . "'";;
		}
		if ($vResult == '')
			return "''";
		else
			return $vResult;
	}
	//////////Get Filter///////////////
	protected function GetCondition()
	{
		$strCondi = "";
		if ($this->LV_UserID == 'MP008') {
			$vListStaff = $this->LV_GetDepParent('KD,PTTT');
			$strCondi = $strCondi . " and ((lv015 in ($vListStaff) and lv003='CT' and lv013='MP001') or lv013='$this->LV_UserID')";
		} elseif ($this->LV_UserID == 'MP001' && $_SESSION['ERPSOFV2RUserID'] != 'admin') {
			$vListStaff = $this->LV_GetDepParentPrivate('KD,PTTT');
			$strCondi = $strCondi . " and (lv013='$this->LV_UserID' and ((lv015 not in ($vListStaff) and lv003='CT') or lv003<>'CT' ))";
		} else
			if ($this->lv013 != "")  $strCondi = $strCondi . " and lv013='$this->lv013'";
		if ($this->lv001 != "") $strCondi = $strCondi . " and lv001  like '%$this->lv001%'";
		if ($this->lv002 != "") $strCondi = $strCondi . " and lv002  like '%$this->lv002%'";
		if ($this->lv003 != "") $strCondi = $strCondi . " and lv003  like '%$this->lv003%'";
		if ($this->lv005 != "") $strCondi = $strCondi . " and lv005  like '%$this->lv005%'";
		if ($this->lv006 != "") $strCondi = $strCondi . " and lv006 = '$this->lv006'";
		if ($this->lv007 != "")  $strCondi = $strCondi . " and lv007 like '%$this->lv007%'";
		if ($this->lv008 != "")  $strCondi = $strCondi . " and lv008 like '%$this->lv008%'";
		if ($this->lv009 != "")  $strCondi = $strCondi . " and lv009 like '%$this->lv009%'";
		if ($this->lv010 != "")  $strCondi = $strCondi . " and lv010 like '%$this->lv010%'";
		if ($this->lv011 != "")  $strCondi = $strCondi . " and lv011 like '%$this->lv011%'";
		if ($this->lv012 != "")  $strCondi = $strCondi . " and lv012 like '%$this->lv012%'";

		if ($this->lv014 != "")  $strCondi = $strCondi . " and lv014 like '%$this->lv014%'";
		if ($this->lv015 != "")  $strCondi = $strCondi . " and lv015 like '%$this->lv015%'";
		if ($this->lv016 != "")  $strCondi = $strCondi . " and lv016 like '%$this->lv016%'";
		if ($this->lv017 != "")  $strCondi = $strCondi . " and lv017 like '%$this->lv017%'";
		if ($this->lv018 != "")  $strCondi = $strCondi . " and lv018 like '%$this->lv018%'";
		if ($this->lv019 != "")  $strCondi = $strCondi . " and lv019 like '%$this->lv019%'";
		if ($this->lv020 != "")  $strCondi = $strCondi . " and lv020 like '%$this->lv020%'";
		if ($this->lv022 != "")  $strCondi = $strCondi . " and lv022 like '%$this->lv022%'";
		if ($this->lv023 != "")  $strCondi = $strCondi . " and lv023 like '%$this->lv023%'";
		$strCondi = $strCondi . " and lv006=0 and lv021=0";
		return $strCondi;
	}
	////////////////Count///////////////////////////
	function GetCount()
	{
		$sqlC = "SELECT COUNT(*) AS nums FROM hr_kb0002 WHERE 1=1 " . $this->GetCondition();
		$bResultC = db_query($sqlC);
		$arrRowC = db_fetch_array($bResultC);
		return $arrRowC['nums'];
	}
	//////////////////////Buil list////////////////////
	function LV_BuilList($lvList, $lvFrom, $lvChkAll, $lvChk, $curRow, $maxRows, $paging, $lvOrderList, $lvSortNum)
	{
		if ($curRow < 0) $curRow = 0;
		if ($lvList == "") $lvList = $this->DefaultFieldList;
		if ($this->isView == 0) return false;
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
		<table  align=\"center\" class=\"lvtable\">
		<!--<tr ><td colspan=\"" . (2 + count($lstArr)) . "\" class=\"lvTTable\">" . $this->ArrPush[0] . "</td></tr>-->
		<tr ><td colspan=\"" . (count($lstArr)) . "\">" . $this->TabFunction($lvFrom, $lvList, $maxRows) . "</td><td colspan=\"2\" align=right>" . $this->ListFieldSave($lvFrom, $lvList, $maxRows, $lvOrderList, $lvSortNum) . "</td></tr>
		@#01
		<tr ><td colspan=\"" . (count($lstArr) + 2) . "\">$paging</td></tr>
		<tr ><td colspan=\"" . (count($lstArr)) . "\">" . $this->TabFunction($lvFrom, $lvList, $maxRows) . "</td><td colspan=\"2\" align=right>" . $this->ListFieldExport($lvFrom, $lvList, $maxRows) . "</td></tr>
		</table>
		";
		$lvTrH = "<tr class=\"lvhtable\">
			<td width=1% class=\"lvhtable\">" . $this->ArrPush[1] . "</td>
			<td width=1%><input name=\"$lvChkAll\" type=\"checkbox\" id=\"$lvChkAll\" onclick=\"DoChkAll($lvFrom, '$lvChk', this)\" value=\"$curRow\" tabindex=\"2\"/></td>
			@#01
		</tr>
		";
		$lvTr = "<tr @#44 class=\"lvlinehtable@01\">
			<td width=1% onclick=\"Select_Check('$lvChk@03',$lvFrom, '$lvChk', '$lvChkAll')\">@03</td>
			<td width=1%><input name=\"$lvChk\" type=\"checkbox\" id=\"$lvChk@03\" onclick=\"CheckOne($lvFrom, '$lvChk', '$lvChkAll', this)\" value=\"@02\" tabindex=\"2\"  onKeyUp=\"return CheckKeyCheck(event,2,'$lvChk',$lvFrom, '$lvChk', '$lvChkAll',@03)\"/></td>
			@#01
		</tr>
		";
		$lvHref = "<span onclick=\"ProcessTextHiden(this)\"><a href=\"javascript:FunctRunning1('@01')\" style=\"text-decoration:none\" class=@#04>@02</a></span>";
		$lvTdH = "<td width=\"@01\" class=\"lvhtable\">@02</td>";
		$lvTd = "<td  class=\"@#04\" align=\"@#05\" nowrap>@02</td>";
		echo $sqlS = "SELECT *,DATEDIFF(lv016,lv025) SoNgayXinPhep,lv015 lv829,(DATEDIFF(lv017,lv016)+IF(LEFT(lv003,3)='1/2',0.5,1)) lv098,TIMEDIFF(lv017,lv016) lv098_1 FROM hr_kb0002 WHERE 1=1  " . $this->GetCondition() . " $strSort LIMIT $curRow, $maxRows";
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
			if ($vrow['SoNgayXinPhep'] < 2) {
				if ($vrow['lv008'] != 'CT') {
					//$vrow['lv008']=$vrow['lv008'].'('.'Xin phép không đúng trước 2 ngày'.')';
				}
				if (($vrow['lv003'] == 'CT' || $vrow['lv003'] == 'QCC') && ($vrow['SoNgayXinPhep'] >= -1))
					$vAlarms = '';
				else
					$vAlarms = 'style="-webkit-animation: glowing 1500ms infinite;-moz-animation: glowing 1500ms infinite;-o-animation: glowing 1500ms infinite;animation: glowing 1500ms infinite;"';
			} else
				$vAlarms = '';
			for ($i = 0; $i < count($lstArr); $i++) {
				switch ($lstArr[$i]) {
					case 'lv199':
						//<img style="height:30px" src="../clsall/barcode/barcode.php?barnumber='.$vrow['lv001'].'"/>
						$vChucNang = '<table><tr>';
						//if(($vrow['lv008']<1)) 
						{
							if ($this->isApr == 1) $vChucNang = $vChucNang . '<td><input type="button" value="Duyệt" style="padding:3px;border-radius:3px;font-weight:bold;cursor:pointer;" onclick="AprQLy(\'' . ($vrow['lv001']) . '\')"/></td>';
							if ($this->isUnApr == 1) $vChucNang = $vChucNang . '<td><input type="button" value="Không duyệt" style="padding:3px;border-radius:3px;font-weight:bold;cursor:pointer;" onclick="UnAprQLy(\'' . ($vrow['lv001']) . '\')"/></td>';
						}
						$vStr1 = '
								';
						$vChucNang = $vStr1 . $vChucNang . '</tr></table>';
						$vTemp = str_replace("@02", $vChucNang, $this->Align($lvTd, (int)$this->ArrView[$lstArr[$i]]));
						break;
					case 'lv098':
						switch ($vrow['lv022']) {
							case 13:
								$vTemp = str_replace("@02", $vrow['lv098_1'], $this->Align($lvTd, (int)$this->ArrView[$lstArr[$i]]));
								break;
							default:
								$vTemp = str_replace("@02", str_replace("@02", $this->getvaluelink($lstArr[$i], $this->FormatView($vrow[$lstArr[$i]], (int)$this->ArrView[$lstArr[$i]])), str_replace("@01", $vrow['lv001'], $lvHref)), $this->Align($lvTd, (int)$this->ArrView[$lstArr[$i]]));
								break;
						}
						break;
					case 'lv009':
						if ($this->GetApr() == 1) {
							$lvTdTextBox = "<td align=@#05 rowspan=\"@#99\"><input type=\"textbox\" value=\"@02\" @03 onblur=\"UpdateText(this,'" . $vrow['lv001'] . "',9)\" style=\"min-width:125px;width:100%;\" tabindex=\"2\"  maxlength=\"32\"  /></td>";
							$vTemp = str_replace("@02", $this->FormatView($vrow[$lstArr[$i]], (int)$this->ArrView[$lstArr[$i]]), $this->Align(str_replace("@01", $vrow['lv001'], $lvTdTextBox), (int)$this->ArrView[$lstArr[$i]]));
						} else
							$vTemp = str_replace("@02", str_replace("@02", $this->getvaluelink($lstArr[$i], $this->FormatView($vrow[$lstArr[$i]], (int)$this->ArrView[$lstArr[$i]])), str_replace("@01", $vrow['lv001'], $lvHref)), $this->Align($lvTd, (int)$this->ArrView[$lstArr[$i]]));
						break;
					case 'lv018':
						$vTemp = str_replace("@02", str_replace("@02", $this->getvaluelink($lstArr[$i], $this->FormatView(str_replace(" ", "&nbsp;", str_replace("\n", "<br/>", $vrow[$lstArr[$i]])), (int)$this->ArrView[$lstArr[$i]])), str_replace("@01", $vrow['lv001'], $lvHref)), $this->Align($lvTd, (int)$this->ArrView[$lstArr[$i]]));
						break;
					default:
						$vTemp = str_replace("@02", str_replace("@02", $this->getvaluelink($lstArr[$i], $this->FormatView($vrow[$lstArr[$i]], (int)$this->ArrView[$lstArr[$i]])), str_replace("@01", $vrow['lv001'], $lvHref)), $this->Align($lvTd, (int)$this->ArrView[$lstArr[$i]]));
				}
				$strL = $strL . $vTemp;
			}
			$strTr = $strTr . str_replace("@#01", $strL, str_replace("@02", $vrow['lv001'], str_replace("@03", $vorder, str_replace("@01", $vorder % 2, str_replace("@#44", $vAlarms, $lvTr)))));
			if ($vrow['lv021'] == 1) {
				switch ($vrow['lv006']) {
					case 0:
						$strTr = str_replace("@#04", "lvlineapproval", $strTr);
						break;
					case 1:
						$strTr = str_replace("@#04", "lvlineapproval_level1", $strTr);
						break;
					case 2:
						$strTr = str_replace("@#04", "lvlineapproval_level2", $strTr);
						break;
				}
			} else	$strTr = str_replace("@#04", "", $strTr);
		}
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
						<li class=\"menusubT1\"><img src=\"$this->Dir../images/lvicon/export.png\" border=\"0\" />" . $this->ArrFunc[12] . "
							<ul id=\"submenu1-nav\">
							@#01
							</ul>
						</li>
					</ul>";
		$strScript = "		
		<script language=\"javascript\">
		function Export(vFrom,value)
		{
			window.open('hr_kb0002/?lang=" . $this->lang . "&func='+value,'','width=800,height=600,left=200,top=100,screenX=0,screenY=100,resizable=yes,status=no,scrollbars=yes,menubar=yes');
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
		$lvTable = "<!--<div align=\"center\"><img  src=\"" . $this->GetLogo() . "\" style=\"max-width:1024px\" /></div>-->
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
		$sqlS = "SELECT *,lv015 lv829 FROM hr_kb0002 WHERE 1=1  " . $this->RptCondition . " $strSort LIMIT $curRow, $maxRows";
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
				$vTemp = str_replace("@02", $this->getvaluelink($lstArr[$i], $this->FormatView($vrow[$lstArr[$i]], (int)$this->ArrView[$lstArr[$i]])), $this->Align($lvTd, (int)$this->ArrView[$lstArr[$i]]));
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
			case 'lv003':
				$vsql = "select lv001,lv002,IF(lv001='$vSelectID',1,0) lv003 from  tc_lv0002";
				break;
			case 'lv004':
				$vsql = "select lv001,lv002,IF(lv001='$vSelectID',1,0) lv003 from  jo_lv0003";
				break;
			case 'lv006':
				$vsql = "select lv001,lv002,IF(lv001='$vSelectID',1,0) lv003 from  jo_lv0003";
				break;
			case 'lv022':
				$vsql = "select lv001,lv002,IF(lv001='$vSelectID',1,0) lv003 from  jo_lv0015";
				break;
		}
		return $vsql;
	}
	private  function getvaluelink($vFile, $vSelectID)
	{
		switch ($vFile) {
			case 'lv003':
				$lvopt = 0;
				$vsql = "select lv001,lv003 lv002,IF(lv001='$vSelectID',1,0) lv003 from  tc_lv0002 where lv001='$vSelectID'";
				break;
			case 'lv004':
				$vsql = "select lv001,lv002,IF(lv001='$vSelectID',1,0) lv003 from  jo_lv0003 where lv001='$vSelectID'";
				break;
			case 'lv006':
				$vsql = "select lv001,lv002,IF(lv001='$vSelectID',1,0) lv003 from  jo_lv0003 where lv001='$vSelectID'";
				break;
			case 'lv013':
				$lvopt = 0;
				$vsql = "select lv001,lv002,IF(lv001='$vSelectID',1,0) lv003 from  hr_lv0020 where lv001='$vSelectID'";
				break;
			case 'lv015':
				$lvopt = 0;
				$vsql = "select lv001,lv002,IF(lv001='$vSelectID',1,0) lv003 from  hr_lv0020 where lv001='$vSelectID'";
				break;
			case 'lv022':
				$lvopt = 0;
				$vsql = "select lv001,lv002,IF(lv001='$vSelectID',1,0) lv003 from  jo_lv0100 where lv001='$vSelectID'";
				break;
			case 'lv021':
				$vsql = "select lv001,lv002,IF(lv001='$vSelectID',1,0) lv003 from  jo_lv0003 where lv001='$vSelectID'";
				break;
			case 'lv829':
				$vsql = "select B.lv001,B.lv003 lv002,IF(A.lv001='$vSelectID',1,0) lv003 from  hr_lv0020 A inner join hr_lv0002 B on A.lv029=B.lv001 where A.lv001='$vSelectID'";
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