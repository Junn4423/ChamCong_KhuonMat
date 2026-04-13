<?php
/////////////coding cr_lv0149///////////////
class   cr_lv0149 extends lv_controler
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
	public $lv039 = null;
	public $lastId = null;

	///////////
	public $DefaultFieldList = "lv003,lv088,lv089,lv049,lv039,lv012,lv006,lv008,lv010,lv017,lv015,lv005";
	////////////////////GetDate
	public $DateDefault = "1900-01-01";
	public $DateCurrent = "1900-01-01";
	public $Count = null;
	public $paging = null;
	public $lang = null;
	public $objlot = null;
	protected $objhelp = 'cr_lv0149';
	////////////
	var $ArrOther = array();
	var $ArrPush = array();
	var $ArrFunc = array();
	var $ArrGet = array("lv001" => "2", "lv002" => "3", "lv003" => "4", "lv004" => "5", "lv005" => "6", "lv006" => "7", "lv007" => "8", "lv008" => "9", "lv009" => "10", "lv010" => "11", "lv011" => "12", "lv012" => "13", "lv013" => "14", "lv014" => "15", "lv015" => "16", "lv016" => "17", "lv017" => "18", "lv039" => "40", "lv049" => "50", "lv198" => "199", "lv088" => "89", "lv089" => "90");
	var $ArrView = array("lv001" => "0", "lv002" => "0", "lv003" => "0", "lv004" => "10", "lv005" => "0", "lv006" => "10", "lv007" => "0", "lv008" => "10", "lv009" => "0", "lv010" => "10", "lv011" => "10", "lv012" => "10", "lv013" => "10", "lv014" => "0", "lv015" => "0", "lv016" => "22", "lv017" => "10", "lv039" => "0", "lv198" => "0");
	var $ArrViewEnter = array("lv017" => "-1", "lv003" => "789", "lv005" => "-1", "lv088" => "-1", "lv089" => "-1", "lv049" => "-1", "lv039" => "0", "lv012" => "88");
	var $Tables = array('lv003' => 'sl_lv0007', 'lv005' => 'sl_lv0005', 'lv009' => 'hr_lv0018');
	var $TableLink = array("lv003" => "concat(lv001,@! @!,lv002)");
	var $TableReturn = array("lv003" => "lv001");
	public $LE_CODE = "NjlIUS02VFdULTZIS1QtNlFIQQ==";
	function __construct($vCheckAdmin, $vUserID, $vright)
	{
		$this->DateCurrent = GetServerDate() . " " . GetServerTime();
		$this->Set_User($vCheckAdmin, $vUserID, $vright);
		$this->isRel = 1;
		$this->isHelp = 1;
		$this->isConfig = 0;
		$this->isRpt = 0;
		$this->isFil = 0;
		$this->isAdd = 0;


		$this->isApr = 0;
		$this->isUnApr = 0;
		$this->lang = $_GET['lang'];
	}
	function LV_OptionType()
	{
		if ($this->TypePC == 1) {
			$this->DefaultFieldList = "lv003,lv088,lv089,lv049,lv039,lv012,lv006,lv005,lv008,lv010,lv017,lv015";
		} else {
			$this->DefaultFieldList = "lv003,lv088,lv089,lv049,lv039,lv006,lv005,lv008,lv010,lv017,lv015";
		}
	}
	protected function LV_CheckLock()
	{
		$lvsql = "select lv007 from wh_lv0010 B where  B.lv001='$this->lv002'";
		$vReturn = db_query($lvsql);
		if ($vReturn) {
			$vrow = db_fetch_array($vReturn);
			if ($vrow['lv007'] >= 1) {
				$this->isAdd = 0;
				$this->isEdit = 0;
				$this->isDel = 0;
			}
		}
	}
	function LV_Load()
	{
		$vsql = "select * from  cr_lv0149";
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
		}
	}
	function LV_LoadID($vlv001)
	{
		$lvsql = "select * from  cr_lv0149 Where lv001='$vlv001'";
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
		}
	}
	function LV_Insert()
	{
		$lvsql = "insert into cr_lv0149 (lv002,lv003,lv004,lv005,lv006,lv007,lv008,lv009,lv010,lv011,lv012,lv013,lv014,lv015,lv016,lv198,lv039) values('$this->lv002','$this->lv003','$this->lv004','$this->lv005','$this->lv006','$this->LV_UserID','$this->lv008','$this->lv009','$this->lv010','$this->lv011','$this->lv012','$this->lv013','$this->lv014','$this->lv015','$this->lv016','$this->lv198','$this->lv039')";
		$vReturn = db_query($lvsql);
		if ($vReturn) {
			$vInsertID = sof_insert_id();
			$this->lastId = $vInsertID;
			$this->InsertLogOperation($this->DateCurrent, 'cr_lv0149.insert', sof_escape_string($lvsql));
		}
		return $vReturn;
	}
	function LV_InsertCONTRACT($vlvUser, $vlv005, $vlv006, $vlWHID, $vDate)
	{
		if (1 == 1) {
			$this->lv011 = ($this->lv011 != "") ? recoverdate(($this->lv011), $this->lang) : $this->DateDefault;
			$lvsql = "select '$vlvUser' lv002,A.lv003,A.lv004,A.lv005,A.lv004 lv006,A.lv005 lv007,A.lv006 lv008,A.lv007 lv009,A.lv008 lv010,A.lv011,A.lv012,B.lv012 State,concat(A.lv009,'-',A.lv010,':',A.lv013) lv015,Now() lv016,A.lv001 lv198  from sl_lv0014 A inner join sl_lv0007 B on A.lv003=B.lv001 inner join sl_lv0013 C on C.lv001=A.lv002 where C.lv115='$vlv006'";
			//			return str_replace("'","!",$lvsql);
			$vresult = db_query($lvsql);
			$subsqlall = "";
			$i = 1;
			while ($vrow = db_fetch_array($vresult)) {
				$this->lv002 = $this->LV_UserID;
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
				$this->lv198 = $vrow['lv198'];
				//$vReturn=$this->LV_Insert_FI_LI_FO($vrow['State'],$vlWHID);
				$vReturn = $this->LV_Insert();
			}
		} else {
			$lvsql = "select '$vlvUser',A.lv003,A.lv004,A.lv005,A.lv004,A.lv005,A.lv006,A.lv007 ,A.lv008 ,A.lv010,A.lv009,A.lv012,A.lv011 from sl_lv0014 A where A.lv002='$vlv006'";
			$vresult = db_query($lvsql);
			$subsqlall = "";
			$i = 1;
			while ($vrow = db_fetch_array($vresult)) {
				$subsql = "(select '$i' lv001,'$vlvUser' lv002,A.lv003,A.lv004*" . $vrow['lv004'] . " lv004,A.lv005,'' lv006,'' lv007,0 lv008,'' lv009
													,0 lv010,0 lv011,'' lv012,'' lv013
													,IF(A.lv007=1 AND A.lv008=1,'" . $vrow['lv012'] . "-" . $vrow['lv009'] . "',(IF(A.lv008=1,'" . $vrow['lv009'] . "',IF(A.lv007=1,'" . $vrow['lv012'] . "','')))) lv014 
													from  mn_lv0004  A where A.lv002='" . $vrow['lv003'] . "' and A.lv003<>'" . $vrow['lv003'] . "')";
				if ($subsqlall == "")
					$subsqlall = $subsql;
				else
					$subsqlall = $subsqlall . " union " . $subsql;
				$i++;
			}
			$vsqlAll = "insert into cr_lv0149 (lv002,lv003,lv004,lv005,lv006,lv007,lv008,lv009
													,lv010,lv011,lv012,lv013
													,lv014
													) select MP.lv002,MP.lv003,SUM(MP.lv004) lv004,MP.lv005,MP.lv006,MP.lv007,MP.lv008,MP.lv009,MP.lv010,MP.lv011,MP.lv012,MP.lv013,MP.lv014 from (";
			$vsqlAll = $vsqlAll . $subsqlall . ") MP group by lv003,lv014";
			db_query($vsqlAll);
		}
		return $vReturn;
	}
	function LV_GetQuantityRecieption($vPOID, $vItemID, $vLot)
	{
		$lvsql = "select sum(A.lv004) TotalQty from wh_lv0009 A inner join wh_lv0008 B on A.lv002=B.lv001 where  B.lv006='$vPOID' and B.lv005='MUAHANG' and A.lv003='$vItemID'";
		$vReturn = db_query($lvsql);
		if ($vReturn) {
			$vrow = db_fetch_array($vReturn);
			return $this->FormatView($vrow['TotalQty'], 10);
		}
	}
	function LV_InsertPO($vlvUser, $vlv005, $vlv006, $vlWHID, $vDate, $vlv913 = 0)
	{
		$this->lv011 = ($this->lv011 != "") ? recoverdate(($this->lv011), $this->lang) : $this->DateDefault;
		$lvsql = "select '$vlvUser' lv002,A.lv003 lv003,A.lv004 lv004,B.lv004 lv005,A.lv004 lv006,A.lv005 lv007,A.lv006 lv008,A.lv007 lv009,A.lv008 lv010,0 lv011,A.lv009 lv012,'$vlv913' lv013,A.lv010 lv014,A.lv012 lv015,'$vDate' lv016,A.lv010 lv017,A.lv011 lv018,A.lv002 POID,B.lv006 convertqty,A.lv098,A.lv099 from sl_lv0007 B inner join  wh_lv0022 A on A.lv003=B.lv001 where A.lv002='$vlv006' "; // AND (B.lv016 like '%$vlWHID%')";
		$vresult = db_query($lvsql);
		while ($vrow = db_fetch_array($vresult)) {
			$vconvertqty = (float)$vrow['convertqty'];
			if ($vconvertqty == 0) $vconvertqty = 1;
			//$vSLN=$this->LV_GetQuantityRecieption($vrow['POID'],$vrow['lv003'],$vrow['lv014']);
			//$vSLC=$vSLN;
			$vSLC = $vrow['lv004'];
			if ($vSLC > 0) {
				$lvsql1 = "insert into cr_lv0149 (lv002,lv003,lv004,
			lv005,lv006,lv007,lv008,lv009,lv010
			,lv011,lv012,lv013,lv014,lv015,lv016) value('" . $vrow['lv002'] . "','" . $vrow['lv003'] . "','" . ($vSLC * $vconvertqty) . "'
			,'" . $vrow['lv005'] . "','$vSLC','" . $vrow['lv007'] . "','" . ($vrow['lv008'] / $vconvertqty) . "','" . $vrow['lv009'] . "','" . $vrow['lv010'] . "'
			,'" . $vrow['lv011'] . "','" . $vrow['lv012'] . "','" . $vrow['lv013'] . "','" . $vrow['lv014'] . "','" . $vrow['lv015'] . "','" . $vrow['lv016'] . "')";
				$vresult1 = db_query($lvsql1);
			}
		}
		return $vReturn;
	}
	function LV_Update()
	{
		$lvsql = "Update cr_lv0149 set lv002='$this->lv002',lv003='$this->lv003',lv004='$this->lv004',lv005='$this->lv005',lv006='$this->lv006',lv007='$this->lv007',lv008='$this->lv008',lv009='$this->lv009',lv010='$this->lv010',lv011='$this->lv011',lv012='$this->lv012',lv013='$this->lv013',lv014='$this->lv014',lv015='$this->lv015',lv016='$this->lv016' where  lv001='$this->lv001';";
		$vReturn = db_query($lvsql);
		if ($vReturn) $this->InsertLogOperation($this->DateCurrent, 'cr_lv0149.update', sof_escape_string($lvsql));
		return $vReturn;
	}
	function LV_UpdateEdit($vOldlv004, $vOldlv006)
	{
		$lvsql = "Update cr_lv0149 set lv002='$this->lv002',lv003='$this->lv003',lv004='$this->lv004',lv005='$this->lv005',lv006='$this->lv006',lv007='$this->lv007',lv008='$this->lv008',lv009='$this->lv009',lv010='$this->lv010',lv011='$this->lv011',lv012='$this->lv012',lv013='$this->lv013',lv014='$this->lv014',lv015='$this->lv015',lv016='$this->lv016' where  lv001='$this->lv001';";
		$vReturn = db_query($lvsql);
		if ($vReturn) {
			$this->InsertLogOperation($this->DateCurrent, 'cr_lv0149.update', sof_escape_string($lvsql));
		}
		return $vReturn;
	}
	function LV_CheckLocked($vlv002)
	{
		$lvsql = "select lv007 from  wh_lv0010 Where lv001='$vlv002'";
		$vresult = db_query($lvsql);
		if ($vresult) {
			$vrow = db_fetch_array($vresult);
			if ($vrow) {
				if ($vrow['lv007'] <= 0)
					return true;
				else
					return false;
			} else
				return false;
		} else
			return false;
	}
	function LV_Delete($lvarr)
	{
		// if ($this->isDel == 0) return false;
		$lvsql = "DELETE FROM cr_lv0149  WHERE cr_lv0149.lv001 IN ($lvarr)  ";
		$vReturn = db_query($lvsql);
		if ($vReturn) {

			$this->InsertLogOperation($this->DateCurrent, 'cr_lv0149.delete', sof_escape_string($lvsql));
		}
		return $vReturn;
	}
	function GetParentWH($vlv001)
	{
		$sqlD = "SELECT lv002 FROM wh_lv0010 WHERE lv001='$vlv001' ";
		$vresult = db_query($sqlD);
		if ($vresult) {
			$vrow = db_fetch_array($vresult);
			return $vrow['lv002'];
		}
	}
	function LV_Insert_FI_LI_FO($state, $vwhid)
	{
		if ($state == "LIFO")
			return $this->LV_Insert_LotConfirm($this->lv003, $vwhid, 'desc');
		else
			return $this->LV_Insert_LotConfirm($this->lv003, $vwhid, 'asc');
	}
	function LV_Get_Inputing_sl($userid, $lotid, $lvitem, $lvwhid)
	{
		$vsql = "select IF(ISNULL(sum(lv004)),0,sum(lv004)) slreal from cr_lv0149 where lv002='$userid' and lv014='$lotid' and lv003='$lvitem'";
		$bResultC = db_query($vsql);
		$arrRowC = db_fetch_array($bResultC);
		return $arrRowC['slreal'];
	}
	function LV_Insert_LotConfirm($lvitem, $lvwhid, $lvsort)
	{
		if ($lvsort == "") $lvsort = "asc";
		$vsql = "select A.lv001,A.lv004 from wh_lv0020 A where A.lv002='$lvitem' and A.lv003='$lvwhid' order by A.lv004 $lvsort";
		//return str_replace("'","!",$vsql);
		$vReturn = db_query($vsql);
		while ($vrow = db_fetch_array($vReturn)) {
			$slt = $this->objlot->LV_Get_slt_lot($vrow['lv001'], $lvitem, $lvwhid);
			$sqldaxuat = $this->LV_Get_Inputing_sl($this->lv002, $vrow['lv001'], $lvitem, $lvwhid);
			$slt = $slt - $sqldaxuat;

			if ($slt > 0) {
				if ($this->lv004 > $slt) {
					$slxuat = $slt;
					$this->lv004 = $this->lv004 - $slt;
				} else {
					$slxuat = $this->lv004;
					$this->lv004 = 0;
				}
				$this->lv014 = $vrow['lv001'];
				$lvsql = "insert into cr_lv0149 (lv002,lv003,lv004,lv005,lv006,lv007,lv008,lv009,lv010,lv011,lv012,lv013,lv014,lv015,lv016) values('$this->lv002','$this->lv003','$slxuat','$this->lv005','$this->lv006','$this->lv007','$this->lv008','$this->lv009','$this->lv010','$this->lv011','$this->lv012','$this->lv013','$this->lv014','$this->lv015','$this->lv016')";
				$vReturn1 = db_query($lvsql);
				if ($vReturn1) {
					$this->InsertLogOperation($this->DateCurrent, 'cr_lv0149.insert', sof_escape_string($lvsql));
				}
				if ($this->lv004 <= 0) return $vReturn1;
			}
		}
		return $vReturn;
	}
	function LV_InsertWEB($vlvUser, $vlv005, $vDate, $objitem = null)
	{
		if ($objitem == null) return;
		$lvsql = "select * from wb_lv0017 where lv009='$vlv005'";
		$vReturn = db_query_second($lvsql);
		while ($vrow = db_fetch_array_second($vReturn)) {
			$objitem->LV_LoadID($vrow['lv008']);
			if ($objitem->lv001 != NULL) {
				$this->lv002 = $vlvUser;
				$this->lv003 = $vrow['lv008'];

				$this->lv004 = $vrow['lv004'];
				$this->lv005 = $objitem->lv004;
				$this->lv006 = $vrow['lv004'];
				$this->lv007 = $objitem->lv004;
				if ($objitem->lv007 != 0)
					$this->lv008 = $objitem->lv007;
				else
					$this->lv008 = $vrow['lv003'];
				$this->lv009 = $objitem->lv008;
				$this->lv010 = $objitem->lv011;
				$this->lv011 = 0;
				$this->lv012 = '';
				$this->lv013 = '';
				$this->lv014 = '';
				$this->lv015 = 'TransactionID:' . $vlv005;
				$this->lv016 = $vDate;
				$this->lv017 = '';
				$this->lv018 = '';
				$this->lv019 = '';
				$this->lv020 = '';
				$this->LV_Insert();
			}
		}
		return $vReturn;
	}
	function LV_UpdateQty($vID, $vValue)
	{
		if ($this->isEdit == 0) return false;
		if ($vValue == 0) {
			$this->LV_Delete("'$vID'");
		} else {
			$lvsql = "Update cr_lv0149 set lv004='$vValue',lv006='$vValue' where  lv001='$vID';";
			$vReturn = db_query($lvsql);
		}
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
	//////////Get Filter///////////////
	protected function GetCondition()
	{
		$strCondi = "";
		if ($this->lv001 != "") $strCondi = $strCondi . " and lv001  like '%$this->lv001%'";
		if ($this->lv002 != "") $strCondi = $strCondi . " and lv002  like '%$this->lv002%'";
		if ($this->lv003 != "") $strCondi = $strCondi . " and lv003  like '%$this->lv003%'";
		if ($this->lv004 != "") $strCondi = $strCondi . " and lv004  like '%$this->lv004%'";
		if ($this->lv005 != "") $strCondi = $strCondi . " and lv005  like '%$this->lv005%'";
		if ($this->lv006 != "") $strCondi = $strCondi . " and lv006  like '%$this->lv006%'";
		if ($this->lv007 != "")  $strCondi = $strCondi . " and lv007 like '%$this->lv007%'";
		if ($this->lv008 != "")  $strCondi = $strCondi . " and lv008 like '%$this->lv008%'";
		if ($this->lv009 != "")  $strCondi = $strCondi . " and lv009 like '%$this->lv009%'";
		if ($this->lv010 != "")  $strCondi = $strCondi . " and lv010 like '%$this->lv010%'";
		if ($this->lv011 != "")  $strCondi = $strCondi . " and lv011 like '%$this->lv011%'";
		if ($this->lv012 != "")  $strCondi = $strCondi . " and lv012 like '%$this->lv012%'";
		if ($this->lv013 != "")  $strCondi = $strCondi . " and lv013 like '%$this->lv013%'";
		if ($this->lv014 != "")  $strCondi = $strCondi . " and lv014 like '%$this->lv014%'";
		if ($this->lv015 != "")  $strCondi = $strCondi . " and lv015 like '%$this->lv015%'";

		return $strCondi;
	}
	////////////////Count///////////////////////////
	function GetCount()
	{
		$sqlC = "SELECT COUNT(*) AS nums FROM cr_lv0149 WHERE 1=1 " . $this->GetCondition();
		$bResultC = db_query($sqlC);
		$arrRowC = db_fetch_array($bResultC);
		return $arrRowC['nums'];
	}
	//////////////////////Buil list////////////////////
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
		<div id=\"func_id\" style='position:relative;background:#f2f2f2'><div style=\"float:left\">" . $this->TabFunction($lvFrom, $lvList, $maxRows) . "</div><div style=\"float:right\">" . $this->ListFieldSave($lvFrom, $lvList, $maxRows, $lvOrderList, $lvSortNum) . "</div><div style='float:right'>&nbsp;&nbsp;&nbsp;</div><div style='float:right'>" . $this->ListFieldExport($lvFrom, $lvList, $maxRows) . "</div></div><div style='height:35px'></div><table  align=\"center\" class=\"lvtable\"><!--<tr ><td colspan=\"" . (2 + count($lstArr)) . "\" class=\"lvTTable\">" . $this->ArrPush[0] . "</td></tr>-->
		@#01
		@#02
		<tr ><td colspan=\"" . (count($lstArr) + 2) . "\">$paging</td></tr>
		<tr class=\"cssbold_tab\"><td colspan=\"" . (count($lstArr)) . "\">" . $this->TabFunction($lvFrom, $lvList, $maxRows) . "</td><td colspan=\"2\" align=right></td></tr>
		</table>
		";
		$lvTrH = "<tr class=\"lvhtable\">
			<td width=1% class=\"lvhtable\">" . $this->ArrPush[1] . "</td>
			<td width=1%><input name=\"$lvChkAll\" type=\"checkbox\" id=\"$lvChkAll\" onclick=\"DoChkAll($lvFrom, '$lvChk', this)\" value=\"$curRow\" tabindex=\"4\"/></td>
			@#01
		</tr>
		";
		$lvTr = "<tr class=\"lvlinehtable@01\">
			<td width=1% onclick=\"Select_Check('$lvChk@03',$lvFrom, '$lvChk', '$lvChkAll')\">@03</td>
			<td width=1%><input name=\"$lvChk\" type=\"checkbox\" id=\"$lvChk@03\" onclick=\"CheckOne($lvFrom, '$lvChk', '$lvChkAll', this)\" value=\"@02\" tabindex=\"4\"  onKeyUp=\"return CheckKeyCheck(event,2,'$lvChk',$lvFrom, '$lvChk', '$lvChkAll',@03)\"/></td>
			@#01
		</tr>
		";
		$lvTdF = "<td align=\"right\"><strong>@01</strong></td>";
		$strF = "<tr><td colspan=\"2\">&nbsp;</td>";
		$lvHref = "@02";
		$lvTdH = "<td width=\"@01\" class=\"lvhtable\">@02</td>";
		$lvTd = "<td align=@#05>@02</td>";
		$lvSumTd = "<tr><td colspan=\"" . count($lstArr) . "\"><strong>" . $this->ArrPush[98] . ": @#01</strong></td></tr>";
		$lvTotalNumTd = "<tr><td colspan=\"" . count($lstArr) . "\"><strong>" . $this->ArrPush[99] . ": @#01</strong></td></tr>";
		$lvTdTextBox = "<td align=@#05><input type=\"textbox\" value=\"@02\" @03 onblur=\"updateqty(this,@01)\" style=\"width:60px\" tabindex=\"2\"/></td>";
		$sqlS = "SELECT *,lv003 lv088,lv003 lv089 FROM cr_lv0149 WHERE 1=1  " . $this->GetCondition() . " $strSort LIMIT $curRow, $maxRows";
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
			$vField = $lstArr[$i];
			$vStringNumber = "";
			if ($this->ArrViewEnter[$vField] == null) $this->ArrViewEnter[$vField] = 0;
			$vStringNumber = "";
			switch ($vField) {
				case 'lv006':
					$vStringNumber = ' onkeyup="CalculateM()" onchange="CalculateM();" ';
					break;
				case 'lv003':
					$vStringNumber = ' onblur="LoadAccName(this);" ';
					break;
			}
			switch ($this->ArrViewEnter[$vField]) {
				case '789':
					if ($this->isPopupPlus == 0) $this->isPopupPlus = 1;
					$vstr = '
						<table style="width:100%">
							<tr>
							<td>
								<ul id="pop-nav' . (($this->isPopupPlus == 1) ? '' : $this->isPopupPlus) . '" lang="pop-nav' . (($this->isPopupPlus == 1) ? '' : $this->isPopupPlus) . '" onMouseOver="ChangeName(this,' . (($this->isPopupPlus == 1) ? '1' : $this->isPopupPlus) . ')" onkeyup="ChangeName(this,' . (($this->isPopupPlus == 1) ? '1' : $this->isPopupPlus) . ')"> <li class="menupopT">
								<input type="text" autocomplete="off" class="search_img_btn" name="qxtlv005_search" id="qxtlv005_search" style="width:100%;height:22px;min-width:60px;" onKeyUp="LoadPopupParent(this,\'qxt' . $vField . '\',\'' . $this->Tables[$vField] . '\',\'concat(lv002,@! @!,lv001)\',2)" onFocus="LoadPopupParent(this,\'qxt' . $vField . '\',\'' . $this->Tables[$vField] . '\',\'concat(lv002,@! @!,lv001)\',2)" tabindex="2" >
								<div id="lv_popup' . (($this->isPopupPlus == 1) ? '' : $this->isPopupPlus) . '" lang="lv_popup' . (($this->isPopupPlus == 1) ? '' : $this->isPopupPlus) . '"> </div>	
							</td>
							<td>
							<input ' . $vStringNumber . ' autocomplete="off" class="txtenterquick" name="qxt' . $vField . '" type="text" id="qxt' . $vField . '" value="' . $this->Values[$vField] . '" tabindex="2" style="width:100%;min-width:30px;text-align:center;" onKeyPress="return CheckKey(event,7)">
							</td>
							</tr>
						</table>
						';
					$this->isPopupPlus++;
					$vTempEnter = str_replace("@02", $vstr, $this->Align($lvTd, (int)($this->ArrView[$lstArr[$i]] ?? 0)));
					break;
				case '678':
					if ($this->isPopupPlus == 0) $this->isPopupPlus = 1;
					$vstr = '
							<table style="width:100%">
								<tr>
								<td>
									<ul id="pop-nav' . (($this->isPopupPlus == 1) ? '' : $this->isPopupPlus) . '" lang="pop-nav' . (($this->isPopupPlus == 1) ? '' : $this->isPopupPlus) . '" onMouseOver="ChangeName(this,' . (($this->isPopupPlus == 1) ? '1' : $this->isPopupPlus) . ')" onkeyup="ChangeName(this,' . (($this->isPopupPlus == 1) ? '1' : $this->isPopupPlus) . ')"> <li class="menupopT">
									<input type="text" autocomplete="off" class="search_img_btn" name="qxtlv005_search" id="qxtlv005_search" style="width:100%;height:22px;min-width:60px;" onKeyUp="LoadSelfNextParentNewSreen(this,\'qxt' . $vField . '\',\'' . $this->Tables[$vField] . '\',\'' . $this->TableReturn[$vField] . '\',\'lv001,lv002,lv010\',\'\',\'\',1)" onFocus="LoadSelfNextParentNewSreen(this,\'qxt' . $vField . '\',\'' . $this->Tables[$vField] . '\',\'' . $this->TableReturn[$vField] . '\',\'lv001,lv002,lv010\',\'\',\'\',1)"   tabindex="2" >
									<div id="lv_popup' . (($this->isPopupPlus == 1) ? '' : $this->isPopupPlus) . '" lang="lv_popup' . (($this->isPopupPlus == 1) ? '' : $this->isPopupPlus) . '"> </div>	
								</td>
								<td>
								<input ' . $vStringNumber . ' autocomplete="off" class="txtenterquick" name="qxt' . $vField . '" type="text" id="qxt' . $vField . '" value="' . $this->Values[$vField] . '" tabindex="2" style="width:100%;min-width:30px;text-align:center;" onKeyPress="return CheckKey(event,7)">
								</td>
								</tr>
							</table>
							';
					$this->isPopupPlus++;
					$vTempEnter = str_replace("@02", $vstr, $this->Align($lvTd, (int)($this->ArrView[$lstArr[$i]] ?? 0)));
					break;
				case 99:
					if ($this->isPopupPlus == 0) $this->isPopupPlus = 1;
					$vstr = '<ul style="width:100%" id="pop-nav' . (($this->isPopupPlus == 1) ? '' : $this->isPopupPlus) . '" lang="pop-nav' . $this->isPopupPlus . '" onMouseOver="ChangeName(this,' . $this->isPopupPlus . ')" onKeyUp="ChangeName(this,' . $this->isPopupPlus . ')"> <li class="menupopT">
								<input ' . $vStringNumber . ' autocomplete="off" class="txtenterquick" type="text" autocomplete="off" style="width:100%;min-width:30px" name="qxt' . $vField . '" id="qxt' . $vField . '" onKeyUp="LoadPopupParentTabIndex(event,this,\'qxt' . $vField . '\',\'' . $this->Tables[$vField] . '\',\'concat(lv002,@! @!,lv001)\')"  onKeyPress="return CheckKey(event,7)" tabindex="2"  value="' . $this->Values[$vField] . '">
								<div id="lv_popup' . (($this->isPopupPlus == 1) ? '' : $this->isPopupPlus) . '" lang="lv_popup' . $this->isPopupPlus . '"> </div>						  
								</li>
							</ul>';
					$this->isPopupPlus++;
					$vTempEnter = str_replace("@02", $vstr, $this->Align($lvTd, (int)($this->ArrView[$lstArr[$i]] ?? 0)));
					break;
				case 999:
					if ($this->isPopupPlus == 0) $this->isPopupPlus = 1;
					$vstr = '<ul style="width:100%" id="pop-nav' . (($this->isPopupPlus == 1) ? '' : $this->isPopupPlus) . '" lang="pop-nav' . $this->isPopupPlus . '" onMouseOver="ChangeName(this,' . $this->isPopupPlus . ')" onKeyUp="ChangeName(this,' . $this->isPopupPlus . ')"> <li class="menupopT">
								<input ' . $vStringNumber . ' autocomplete="off" class="txtenterquick" type="text" autocomplete="off" style="width:100%;min-width:30px" name="qxt' . $vField . '" id="qxt' . $vField . '" onKeyUp="LoadSelfNextParent(this,\'qxt' . $vField . '\',\'' . $this->Tables[$vField] . '\',\'' . $this->TableLinkReturn[$vField] . '\',\'' . $this->TableLink[$vField] . '\')"  onKeyPress="return CheckKey(event,7)" tabindex="2" value="' . $this->Values[$vField] . '" onblur="if(this.value.substr(this.value.length-1,this.value.length)==\',\') {this.value=this.value.substr(0,this.value.length-1);};">
								<div id="lv_popup' . (($this->isPopupPlus == 1) ? '' : $this->isPopupPlus) . '" lang="lv_popup' . $this->isPopupPlus . '"> </div>						  
								</li>
							</ul>';
					$this->isPopupPlus++;
					$vTempEnter = str_replace("@02", $vstr, $this->Align($lvTd, (int)($this->ArrView[$lstArr[$i]] ?? 0)));
					break;
				case 88:
					$vstr = '<select ' . $vStringNumber . ' class="selenterquick" name="qxt' . $vField . '" id="qxt' . $vField . '" tabindex="2" style="width:100%;min-width:30px" onKeyPress="return CheckKey(event,7)">' . $this->LV_LinkField($vField, $this->Values[$vField]) . '</select>';
					$vTempEnter = str_replace("@02", $vstr, $this->Align($lvTd, (int)($this->ArrView[$lstArr[$i]] ?? 0)));
					break;
				case 89:
					$vstr = '<select ' . $vStringNumber . ' class="selenterquick" name="qxt' . $vField . '" id="qxt' . $vField . '" tabindex="2" style="width:100%;min-width:30px" onKeyPress="return CheckKey(event,7)">
							<option value="">...</option>
						' . $this->LV_LinkField($vField, $this->Values[$vField]) . '</select>';
					$vTempEnter = str_replace("@02", $vstr, $this->Align($lvTd, (int)($this->ArrView[$lstArr[$i]] ?? 0)));
					break;
				case 4:
					$vstr = '<table><tr><td><input autocomplete="off" class="txtenterquick"  autocomplete="off" name="qxt' . $vField . '_1" type="text" id="qxt' . $vField . '_1" value="' . $this->Values[$vField] . '" tabindex="2" maxlength="32" style="width:100%;min-width:80px" onKeyPress="return CheckKey(event,7)" ondblclick="if(self.gfPop)gfPop.fPopCalendar(this);return false;"></td><td><input class="txtenterquick"  autocomplete="off" name="qxt' . $vField . '_2" type="text" id="qxt' . $vField . '_2" value="' . $this->Values[$vField] . '" tabindex="2" maxlength="32" style="width:50%;min-width:60px" onKeyPress="return CheckKey(event,7)" ></td></tr></table>';
					$vTempEnter = str_replace("@02", $vstr, $this->Align($lvTd, (int)($this->ArrView[$lstArr[$i]] ?? 0)));
					break;
				case 22:
				case 2:
					$vstr = '<input autocomplete="off" class="txtenterquick"  autocomplete="off" name="qxt' . $vField . '" type="text" id="qxt' . $vField . '" value="' . $this->Values[$vField] . '" tabindex="2" maxlength="32" style="width:100%;min-width:60px" onKeyPress="return CheckKey(event,7)" ondblclick="if(self.gfPop)gfPop.fPopCalendar(this);return false;">';
					$vTempEnter = str_replace("@02", $vstr, $this->Align($lvTd, (int)($this->ArrView[$lstArr[$i]] ?? 0)));
					break;
				case 33:
					$vstr = '<input autocomplete="off" class="txtenterquick" name="qxt' . $vField . '" type="checkbox" id="qxt' . $vField . '" value="1" ' . (($this->Values[$vField] == 1) ? 'checked="true"' : '') . ' tabindex="2" style="width:100%;min-width:30px;text-align:center;" onKeyPress="return CheckKey(event,7)">';
					$vTempEnter = str_replace("@02", $vstr, $this->Align($lvTd, (int)($this->ArrView[$lstArr[$i]] ?? 0)));
					break;
				case 0:
					$vstr = '<input ' . $vStringNumber . ' autocomplete="off" class="txtenterquick" name="qxt' . $vField . '" type="text" id="qxt' . $vField . '" value="' . htmlspecialchars($this->Values[$vField] ?? '', ENT_QUOTES) . '" tabindex="2" style="width:100%;min-width:30px;text-align:center;" onKeyPress="return CheckKey(event,7)">';
					$vTempEnter = str_replace("@02", $vstr, $this->Align($lvTd, (int)($this->ArrView[$lstArr[$i]] ?? 0)));
					break;
				default:
					if ($vField == 'lv088')
						$vTempEnter = '<td><div id="qxt' . $vField . '" style="width:160px"></div></td>';
					else
						$vTempEnter = '<td><div id="qxt' . $vField . '"></div></td>';
					break;
			}
			$strTrEnter = $strTrEnter . $vTempEnter;
			$strTrEnterEmpty = $strTrEnterEmpty . "<td>&nbsp;</td>";
		}
		if ($this->isEdit == 1)
			$strTrEnter = "<tr class='entermobil'><td colspan='2'>" . '<img tabindex="2" border="0" title="Add" class="imgButton" onclick="Save()" onmouseout="this.src=\'' . $this->Dir . '../images/iconcontrol/btn_add.jpg\';" onmouseover="this.src=\'' . $this->Dir . '../images/iconcontrol/btn_add_02.jpg\';" src="' . $this->Dir . '../images/iconcontrol/btn_add.jpg" onkeypress="return CheckKey(event,11)">' . "</td>" . $strTrEnter . "</tr>";
		else
			$strTrEnter = "";
		$vsumamount = 0;
		$vtotalnum = 0;
		while ($vrow = db_fetch_array($bResult)) {
			$strL = "";
			$vorder++;
			$vlineamount = $vrow['lv004'] * $vrow['lv008'] - $vrow['lv004'] * $vrow['lv008'] * $vrow['lv011'] / 100 + $vrow['lv004'] * $vrow['lv008'] * $vrow['lv010'] / 100;
			$vsumamount = $vsumamount + $vlineamount;
			$vtotalnum = $vtotalnum + $vrow['lv004'];

			$vslline = -1;
			for ($i = 0; $i < count($lstArr); $i++) {
				switch ($lstArr[$i]) {
					case 'lv017':
						$vTemp = str_replace("@02", str_replace("@02", $this->getvaluelink($lstArr[$i], $this->FormatView($vlineamount, (int)$this->ArrView[$lstArr[$i]])), str_replace("@01", $vrow['lv001'], $lvHref)), $this->Align($lvTd, (int)$this->ArrView[$lstArr[$i]]));
						break;
					case 'lv006':
						if ($this->GetEdit() > 0) {
							$vlv093 = round($vrow['lv006'], 1);
							$lvTdTextBox = "<input class='txtenterquick' type=\"textbox\" value=\"" . $vlv093 . "\" @03 onfocus=\"if(this.value=='') this.value='" . $vlv093 . "'\" onblur=\"UpdateText(this,'" . $vrow['lv001'] . "',6)\" style=\"min-width:60px;width:100%;text-align:center\" tabindex=\"2\"  maxlength=\"100\" />";
							$vTemp = str_replace("@02", str_replace("@02", $lvTdTextBox, str_replace("@01", $vrow['lv001'], $lvHref)), $this->Align($lvTd, (int)$this->ArrView[$lstArr[$i]]));
						} else {
							$vTemp = str_replace("@02", str_replace("@02", $this->getvaluelink($lstArr[$i], $this->FormatView($vrow[$lstArr[$i]], (int)$this->ArrView[$lstArr[$i]])), str_replace("@01", $vrow['lv001'], $lvHref)), $this->Align($lvTd, (int)$this->ArrView[$lstArr[$i]]));
						}
						break;
					case 'lv008':
						if ($this->GetEdit() > 0) {
							$vlv093 = round($vrow['lv008'], 1);
							$lvTdTextBox = "<input class='txtenterquick' type=\"textbox\" value=\"" . $vlv093 . "\" @03 onfocus=\"if(this.value=='') this.value='" . $vlv093 . "'\" onblur=\"UpdateText(this,'" . $vrow['lv001'] . "',8)\" style=\"min-width:60px;width:100%;text-align:center\" tabindex=\"2\"  maxlength=\"100\" />";
							$vTemp = str_replace("@02", str_replace("@02", $lvTdTextBox, str_replace("@01", $vrow['lv001'], $lvHref)), $this->Align($lvTd, (int)$this->ArrView[$lstArr[$i]]));
						} else {
							$vTemp = str_replace("@02", str_replace("@02", $this->getvaluelink($lstArr[$i], $this->FormatView($vrow[$lstArr[$i]], (int)$this->ArrView[$lstArr[$i]])), str_replace("@01", $vrow['lv001'], $lvHref)), $this->Align($lvTd, (int)$this->ArrView[$lstArr[$i]]));
						}
						break;
					case 'lv010':
						if ($this->GetEdit() > 0) {
							$vlv093 = round($vrow['lv010'], 1);
							$lvTdTextBox = "<input class='txtenterquick' type=\"textbox\" value=\"" . $vlv093 . "\" @03 onfocus=\"if(this.value=='') this.value='" . $vlv093 . "'\" onblur=\"UpdateText(this,'" . $vrow['lv001'] . "',10)\" style=\"min-width:60px;width:100%;text-align:center\" tabindex=\"2\"  maxlength=\"100\" />";
							$vTemp = str_replace("@02", str_replace("@02", $lvTdTextBox, str_replace("@01", $vrow['lv001'], $lvHref)), $this->Align($lvTd, (int)$this->ArrView[$lstArr[$i]]));
						} else {
							$vTemp = str_replace("@02", str_replace("@02", $this->getvaluelink($lstArr[$i], $this->FormatView($vrow[$lstArr[$i]], (int)$this->ArrView[$lstArr[$i]])), str_replace("@01", $vrow['lv001'], $lvHref)), $this->Align($lvTd, (int)$this->ArrView[$lstArr[$i]]));
						}
						break;
					case 'lv039':
						if ($this->GetEdit() > 0) {
							$vlv093 = $vrow['lv039'];
							$lvTdTextBox = "<input class='txtenterquick' type=\"textbox\" value=\"" . $vlv093 . "\" @03 onfocus=\"if(this.value=='') this.value='" . $vlv093 . "'\" onblur=\"UpdateText(this,'" . $vrow['lv001'] . "',39)\" style=\"min-width:60px;width:100%;text-align:center\" tabindex=\"2\"  maxlength=\"255\" />";
							$vTemp = str_replace("@02", str_replace("@02", $lvTdTextBox, str_replace("@01", $vrow['lv001'], $lvHref)), $this->Align($lvTd, (int)$this->ArrView[$lstArr[$i]]));
						} else {
							$vTemp = str_replace("@02", str_replace("@02", $this->getvaluelink($lstArr[$i], $this->FormatView($vrow[$lstArr[$i]], (int)$this->ArrView[$lstArr[$i]])), str_replace("@01", $vrow['lv001'], $lvHref)), $this->Align($lvTd, (int)$this->ArrView[$lstArr[$i]]));
						}
						break;
					case 'lv015':
						if ($this->GetEdit() > 0) {
							$vlv093 = $vrow['lv015'];
							$lvTdTextBox = "<input class='txtenterquick' type=\"textbox\" value=\"" . $vlv093 . "\" @03 onfocus=\"if(this.value=='') this.value='" . $vlv093 . "'\" onblur=\"UpdateText(this,'" . $vrow['lv001'] . "',15)\" style=\"min-width:60px;width:100%;text-align:center\" tabindex=\"2\"  maxlength=\"255\" />";
							$vTemp = str_replace("@02", str_replace("@02", $lvTdTextBox, str_replace("@01", $vrow['lv001'], $lvHref)), $this->Align($lvTd, (int)$this->ArrView[$lstArr[$i]]));
						} else {
							$vTemp = str_replace("@02", str_replace("@02", $this->getvaluelink($lstArr[$i], $this->FormatView($vrow[$lstArr[$i]], (int)$this->ArrView[$lstArr[$i]])), str_replace("@01", $vrow['lv001'], $lvHref)), $this->Align($lvTd, (int)$this->ArrView[$lstArr[$i]]));
						}
						break;
					case 'lv027':
						if (1 == 1) {
							$lvTdTextBox = "<td align=center><input class='txtenterquick' type=\"checkbox\" value=\"1\" " . (($vrow['lv027'] == 1) ? 'checked="true"' : '') . " @03 onclick=\"UpdateTextCheck(this,'" . $vrow['lv001'] . "',27)\" style=\"width:35px;text-align:center;\" tabindex=\"2\" maxlength=\"32\"   onKeyPress=\"return CheckKey(event,7)\"/></td>";
							$vTemp = str_replace("@02", $this->FormatView($vrow[$lstArr[$i]], 0), $this->Align(str_replace("@01", $vrow['lv001'], $lvTdTextBox), (int)$this->ArrView[$lstArr[$i]]));
						} else
							$vTemp = str_replace("@02", $this->getvaluelink($lstArr[$i], $this->FormatView($vrow[$lstArr[$i]], (int)$this->ArrView[$lstArr[$i]])), $this->Align($lvTd, (int)$this->ArrView[$lstArr[$i]]));

						break;
					case 'lv013':
						//$vslline = $this->objlot->LV_Get_slt_lot_all($vrow['lv014'],$vrow['lv003']);
						$vTemp = str_replace("@02", str_replace("@02", $this->getvaluelink($lstArr[$i], $this->FormatView($vslline, (int)$this->ArrView[$lstArr[$i]])), str_replace("@01", $vrow['lv001'], $lvHref)), $this->Align($lvTd, (int)$this->ArrView[$lstArr[$i]]));
						break;
					case 'lv049':
						if ($this->isEdit > 0 || $this->isAdd > 0) {
							$lvImg = "<center><a target='_blank' href='cr_lv0149/readfile.php?UserID=" . $vrow['lv001'] . "&type=8&size=0'><img name=\"imgView\" border=\"0\" style=\"border-color:#CCCCCC\" title=\"\" alt=\"Image\" width1=\"96px\" height=\"48px\" src=\"cr_lv0149/readfile.php?UserID=" . $vrow['lv001'] . "&type=8&size=1\" /></a></center>";
							$vTempEnter = '<td>
						<table valign="top" style="width:100%;border: 1px solid #89B4D6">
							<tr>
							<td align="center">
								<div style="text-align:center" id="attachfile_8_' . $vrow['lv001'] . '">' . $lvImg . '</div>
							</td>
							</tr>
							<tr>
							<td align=" title="Phiếu khai thác thông tin khách hàng">
							<div id="framupload_8_' . $vrow['lv001'] . '" style="width:116px">
							<iframe  height=28 width="100%" marginheight=0 marginwidth=0 frameborder=0 src="cr_lv0149?childdetailfunc=upload&ViTriUp=8&UserID=' . $vrow['lv001'] . '&lang=' . $plang . '"></iframe>
							</div>
							</td>
							
						</tr>
						</table>	</td>
						';
							$vTemp = str_replace("@02", $vrow[$lstArr[$i]], str_replace("@01", $vrow['lv001'], $vTempEnter));
						} else {
							$lvImg = "<td  ><a target='_blank' href='cr_lv0149/readfile.php?UserID=" . $vrow['lv001'] . "&type=8&size=0'><img name=\"imgView\" border=\"0\" style=\"border-color:#CCCCCC\" title=\"\" alt=\"Image\" width1=\"96px\" height=\"48px\" src=\"cr_lv0366/readfile.php?UserID=" . $vrow['lv001'] . "&type=8&size=1\" /></a></td>";
							$vTemp = str_replace("@02", $vrow[$lstArr[$i]], str_replace("@01", $vrow['lv001'], $lvImg));
						}
						break;
					default:
						$vTemp = str_replace("@02", str_replace("@02", $this->getvaluelink($lstArr[$i], $this->FormatView($vrow[$lstArr[$i]], (int)$this->ArrView[$lstArr[$i]])), str_replace("@01", $vrow['lv001'], $lvHref)), $this->Align($lvTd, (int)$this->ArrView[$lstArr[$i]]));
						break;
				}
				$strL = $strL . $vTemp;
			}


			$strTr = $strTr . str_replace("@#01", $strL, str_replace("@02", $vrow['lv001'], str_replace("@03", $vorder, str_replace("@01", $vorder % 2, $lvTr))));
		}
		$strF = $strF . "</tr>";
		$strF = str_replace("<!--lv004-->", $this->FormatView($vtotalnum, 10), $strF);
		$strF = str_replace("<!--lv017-->", $this->FormatView($vsumamount, 10), $strF);
		$strF = str_replace("<!--lv013-->", $this->FormatView($vSLTon13, 10), $strF);
		$strF = str_replace("<!--lv012-->", $this->FormatView($vSLTon12, 10), $strF);
		$strF = str_replace("<!--lv01-->", '<p style="text-align:center;padding:5px">Tổng:</p>', $strF);
		$lvTable = str_replace("@#02", $strF, $lvTable);
		$strTrH = str_replace("@#01", $strH, $lvTrH);
		$lvSumTd = str_replace("@#01", $this->FormatView($vsumamount, 10), $lvSumTd);
		$lvTotalNumTd = str_replace("@#01", $this->FormatView($vtotalnum, 10), $lvTotalNumTd);
		return str_replace("@#01", $strTrH . $strTrEnter . $strTr, str_replace("@#04", $lvSumTd, str_replace("@#03", $lvTotalNumTd, $lvTable)));
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
			window.open('" . $this->Dir . "cr_lv0149/?lang=" . $this->lang . "&childdetailfunc='+value+'&ID=" . base64_encode($this->lv002) . "','','width=800,height=600,left=200,top=100,screenX=0,screenY=100,resizable=yes,status=no,scrollbars=yes,menubar=yes');
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
			<td width=1% onclick=\"Select_Check('$lvChk@03',$lvFrom, '$lvChk', '$lvChkAll')\">@03</td>
			@#01
		</tr>
		";
		$lvTdF = "<td align=\"@#05\"><strong>@01</strong></td>";
		$strF = "<tr><td colspan=\"1\">&nbsp;</td>";
		$lvTdH = "<td width=\"@01\" class=\"lvhtable\">@02</td>";
		$lvTd = "<td align=@#05>@02</td>";
		$sqlS = "SELECT *,lv003 lv088,lv003 lv089 FROM cr_lv0149 WHERE 1=1  " . $this->RptCondition . " $strSort LIMIT $curRow, $maxRows";
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
			$vslline = -1;
			$vlineamount = $vrow['lv004'] * $vrow['lv008'] - $vrow['lv004'] * $vrow['lv008'] * $vrow['lv012'] / 100 + $vrow['lv004'] * $vrow['lv008'] * $vrow['lv010'] / 100;
			$vsumamount = $vsumamount + $vlineamount;
			$vtotalnum = $vtotalnum + $vrow['lv004'];
			for ($i = 0; $i < count($lstArr); $i++) {
				switch ($lstArr[$i]) {
					case 'lv017':
						$vTemp = str_replace("@02", str_replace("@02", $this->getvaluelink($lstArr[$i], $this->FormatView($vlineamount, (int)$this->ArrView[$lstArr[$i]])), str_replace("@01", $vrow['lv001'], $lvHref)), $this->Align($lvTd, (int)$this->ArrView[$lstArr[$i]]));
						break;
					case 'lv006':
						if ($this->GetEdit() > 0) {
							$vlv093 = $vrow['lv006'];
							$lvTdTextBox = "<input class='txtenterquick' type=\"textbox\" value=\"" . $vlv093 . "\" @03 onfocus=\"if(this.value=='') this.value='" . $vlv093 . "'\" onblur=\"UpdateText(this,'" . $vrow['lv001'] . "',6)\" style=\"min-width:60px;width:100%;text-align:center\" tabindex=\"2\"  maxlength=\"100\" />";
							$vTemp = str_replace("@02", str_replace("@02", $lvTdTextBox, str_replace("@01", $vrow['lv001'], $lvHrefNone)), $this->Align($lvTd, (int)$this->ArrView[$lstArr[$i]]));
						} else {
							$vTemp = str_replace("@02", str_replace("@02", $this->getvaluelink($lstArr[$i], $this->FormatView($vrow[$lstArr[$i]], (int)$this->ArrView[$lstArr[$i]])), str_replace("@01", $vrow['lv001'], $lvHref)), $this->Align($lvTd, (int)$this->ArrView[$lstArr[$i]]));
						}
						break;
					case 'lv012':
						if ($vslline == -1) {
							if (trim($this->WHID) == '')
								$vslline = $this->objlot->LV_Get_slt_lot_all($vrow['lv014'], $vrow['lv003']);
							else
								$vslline = $this->objlot->LV_Get_slt_lot_once($vrow['lv014'], $vrow['lv003'], $this->WHID);
						}
						$vTemp = str_replace("@02", $this->getvaluelink($lstArr[$i], $this->FormatView($vslline, (int)$this->ArrView[$lstArr[$i]])), $this->Align($lvTd, (int)$this->ArrView[$lstArr[$i]]));
						break;
					case 'lv013':
						if ($vslline == -1) {
							if (trim($this->WHID) == '')
								$vslline = $this->objlot->LV_Get_slt_lot_all($vrow['lv014'], $vrow['lv003']);
							else
								$vslline = $this->objlot->LV_Get_slt_lot_once($vrow['lv014'], $vrow['lv003'], $this->WHID);
						}
						$vTemp = str_replace("@02", $this->getvaluelink($lstArr[$i], $this->FormatView($vslline - $vrow['lv004'], (int)$this->ArrView[$lstArr[$i]])), $this->Align($lvTd, (int)$this->ArrView[$lstArr[$i]]));
						break;
					default:
						$vTemp = str_replace("@02", $this->getvaluelink($lstArr[$i], $this->FormatView($vrow[$lstArr[$i]], (int)$this->ArrView[$lstArr[$i]])), $this->Align($lvTd, (int)$this->ArrView[$lstArr[$i]]));
						break;
				}
				$strL = $strL . $vTemp;
			}


			$strTr = $strTr . str_replace("@#01", $strL, str_replace("@02", $vrow['lv001'], str_replace("@03", $vorder, str_replace("@01", $vorder % 2, $lvTr))));
		}
		$strF = $strF . "</tr>";
		$strF = str_replace("<!--lv004-->", $this->FormatView($vtotalnum, 10), $strF);
		$strF = str_replace("<!--lv017-->", $this->FormatView($vsumamount, 10), $strF);
		$strF = str_replace("<!--lv003-->", '<p style="text-align:center;padding:5px">Tổng:</p>', $strF);
		$lvTable = str_replace("@#02", $strF, $lvTable);
		$strTrH = str_replace("@#01", $strH, $lvTrH);
		return str_replace("@#01", $strTrH . ($strTr ?? ""), $lvTable);
	}

	//////////////////////Buil list////////////////////
	//////////////////////Buil list////////////////////
	function LV_BuilListReportOther($lvList, $lvFrom, $lvChkAll, $lvChk, $curRow, $maxRows, $paging, $lvOrderList, $vTax)
	{

		if ($lvList == "") $lvList = $this->DefaultFieldList;
		if ($this->isView == 0) return false;
		$lvList = $lvList . "";
		$lvOrderList = $lvOrderList . "";
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
		$lvTable = "<table  align=\"center\" class=\"lvtable\" border=1 cellspacing=\"0\" cellpadding=\"0\">
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
			<td width=1% onclick=\"Select_Check('$lvChk@03',$lvFrom, '$lvChk', '$lvChkAll')\">@03</td>
			@#01
		</tr>
		";
		$lvTrTotal = "<tr >
			<td class=\"lvlineboldtable\"  colspan=@04>@03</td>
			<td class=\"lvlineboldtable\">@01</td>
		</tr>
		";
		$lvTdF = "<td align=\"@#05\"><strong>@01</strong></td>";
		$strF = "<tr><td colspan=\"1\">&nbsp;</td>";
		$lvTdH = "<td width=\"@01\" class=\"lvhtable\">@02</td>";
		$lvTd = "<td align=@#05>@02</td>";
		$sqlS = "SELECT *,lv003 lv088,lv003 lv089 FROM cr_lv0149 WHERE 1=1  " . $this->GetCondition() . " $strSort LIMIT $curRow, $maxRows";
		$vorder = $curRow;
		$bResult = db_query($sqlS);
		$this->Count = db_num_rows($bResult);
		$strTrH = "";
		$strH = "";
		$strSubTotal = 0;
		$strSubTax = 0;
		$strTotalAmount = 0;
		$vUnitPrice = "VNĐ";
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
			for ($i = 0; $i < count($lstArr); $i++) {
				if ($lstArr[$i] == 'lv017') {
					$vTemp = str_replace("@02", str_replace("@02", $this->getvaluelink($lstArr[$i], $this->FormatView($vlineamount, (int)$this->ArrView[$lstArr[$i]])), str_replace("@01", $vrow['lv001'], $lvHref)), $this->Align($lvTd, (int)$this->ArrView[$lstArr[$i]]));
				} elseif ($lstArr[$i] == 'lv012') {
					if ($vslline == -1) {
						$vslline = $this->objlot->LV_Get_slt_lot_all($vrow['lv014'], $vrow['lv003']);
					}
					$vTemp = str_replace("@02", $this->getvaluelink($lstArr[$i], $this->FormatView($vslline, (int)$this->ArrView[$lstArr[$i]])), $this->Align($lvTd, (int)$this->ArrView[$lstArr[$i]]));
				} elseif ($lstArr[$i] == 'lv013') {
					if ($vslline == -1) {
						$vslline = $this->objlot->LV_Get_slt_lot_all($vrow['lv014'], $vrow['lv003']);
					}
					$vTemp = str_replace("@02", $this->getvaluelink($lstArr[$i], $this->FormatView($vslline - $vrow['lv004'], (int)$this->ArrView[$lstArr[$i]])), $this->Align($lvTd, (int)$this->ArrView[$lstArr[$i]]));
				} else
					$vTemp = str_replace("@02", $this->getvaluelink($lstArr[$i], $this->FormatView($vrow[$lstArr[$i]], (int)$this->ArrView[$lstArr[$i]])), $this->Align($lvTd, (int)$this->ArrView[$lstArr[$i]]));
				$strL = $strL . $vTemp;
			}


			$strTr = $strTr . str_replace("@#01", $strL, str_replace("@02", $vrow['lv001'], str_replace("@03", $vorder, str_replace("@01", $vorder % 2, $lvTr))));
		}
		$strF = $strF . "</tr>";
		$strF = str_replace("<!--lv004-->", $this->FormatView($vtotalnum, 10), $strF);
		$strF = str_replace("<!--lv017-->", $this->FormatView($vsumamount, 10), $strF);
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
			case 'lv003':
				$vsql = "select lv001,lv002,IF(lv001='$vSelectID',1,0) lv003 from  sl_lv0007";
				break;
			case 'lv005':
				$vsql = "select lv001,lv002,IF(lv001='$vSelectID',1,0) lv003 from  sl_lv0005";
				break;
			case 'lv007':
				$vsql = "select lv001,lv002,IF(lv001='$vSelectID',1,0) lv003 from  sl_lv0005";
				break;
			case 'lv009':
				$vsql = "select lv001,lv002,IF(lv001='$vSelectID',1,0) lv003 from  hr_lv0018 order by lv004";
				break;
			case 'lv012':
				$vsql = "select A.lv001,concat(B.lv001,':',A.lv005,' = ',A.lv003) lv002,IF(A.lv001='$vSelectID',1,0) lv003 from  cr_lv0203 A inner join cr_lv0202 B on A.lv002=B.lv001 where B.lv001='$this->DNPC' order by A.lv001";
				break;
		}
		return $vsql;
	}
	public function getvaluelink($vFile, $vSelectID)
	{
		if (!empty($this->ArrGetValueLink[$vFile][$vSelectID][0] ?? null)) {
			return $this->ArrGetValueLink[$vFile][$vSelectID][1] ?? null;
		}
		if ($vSelectID == "") {
			return $vSelectID;
		}
		switch ($vFile) {
			case 'lv089':
				$vsql = "select lv001,lv010 lv002,IF(lv001='$vSelectID',1,0) lv003 from  sl_lv0007 where lv001='$vSelectID'";
				$lvopt = 0;
				break;
			case 'lv088':
				$vsql = "select lv001,lv002,IF(lv001='$vSelectID',1,0) lv003 from  sl_lv0007 where lv001='$vSelectID'";
				$lvopt = 0;
				break;
			case 'lv005':
				$vsql = "select lv001,lv002,IF(lv001='$vSelectID',1,0) lv003 from  sl_lv0005 where lv001='$vSelectID'";
				break;
			case 'lv007':
				$vsql = "select lv001,lv002,IF(lv001='$vSelectID',1,0) lv003 from  sl_lv0005 where lv001='$vSelectID'";
				break;
			case 'lv009':
				$lvopt = 1;
				$vsql = "select lv001,lv002,IF(lv001='$vSelectID',1,0) lv003 from  hr_lv0018 where lv001='$vSelectID'";
				break;
			case 'lv012':
				$vsql = "select A.lv001,concat(B.lv001,':',A.lv005,' = ',A.lv003) lv002,IF(A.lv001='$vSelectID',1,0) lv003 from  cr_lv0203 A inner join cr_lv0202 B on A.lv002=B.lv001 where A.lv001='$vSelectID'";
				break;
			default:
				$vsql = "";
				break;
		}
		if ($vsql == "") {
			return $vSelectID;
		} else
			$lvResult = db_query($vsql);
		while ($row = db_fetch_array($lvResult)) {
			return ($lvopt == 0) ? $row['lv002'] : (($lvopt == 1) ? $row['lv001'] . "(" . $row['lv002'] . ")" : (($lvopt == 2) ? $row['lv002'] . "(" . $row['lv001'] . ")" : $row['lv001']));
		}
	}
	function MB_LayMaBOM()
	{
		$vsql = "select lv001,lv002 from sl_lv0007";
		return db_query($vsql);
	}
	function MB_LoadVatTu()
	{
		$vsql = "select * from cr_lv0149 where lv008 <> 0";
		return db_query($vsql);
	}
}
