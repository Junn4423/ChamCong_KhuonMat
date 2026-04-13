<?php
/////////////coding cr_lv0151/////////////// 
class   cr_lv0151 extends lv_controler
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

	///////////
	public $DefaultFieldList = "lv003,lv027,lv088,lv089,lv049,lv039,lv015,lv012,lv006,lv004,lv005,lv008,lv117,lv010,lv017";
	//public $DefaultFieldList="lv001,lv002,lv102,lv103,lv104,lv003,lv088,lv004,lv005,lv008,lv010,lv009,lv014,lv015,lv016,lv017,lv109";	
	////////////////////GetDate
	public $DateDefault = "1900-01-01";
	public $DateCurrent = "1900-01-01";
	public $Count = null;
	public $paging = null;
	public $lang = null;
	protected $objhelp = 'cr_lv0127';
	public $objlot = null;
	public $obj_wb_lv0006 = null;
	////////////
	var $ArrOther = array();
	var $ArrPush = array();
	var $ArrFunc = array();
	var $ArrGet = array("lv001" => "2", "lv002" => "3", "lv003" => "4", "lv004" => "5", "lv005" => "6", "lv006" => "7", "lv007" => "8", "lv008" => "9", "lv009" => "10", "lv010" => "11", "lv011" => "12", "lv012" => "13", "lv013" => "14", "lv014" => "15", "lv015" => "16", "lv016" => "17", "lv017" => "18", "lv027" => "28", "lv028" => "29", "lv039" => "40", "lv049" => "50", "lv088" => '89', "lv089" => '90', "lv099" => "100", "lv100" => "101", "lv102" => "103", "lv103" => "104", "lv104" => "105", "lv109" => "110", "lv198" => "199", "lv090" => "91", "lv091" => "92", "lv092" => "93", "lv093" => "94", "lv094" => "95", "lv095" => "96", "lv096" => "97", "lv097" => "98", "lv098" => "99", "lv099" => "100", "lv100" => "101", "lv101" => "102", "lv102" => "103", "lv103" => "104", "lv117" => "118", "lv039" => "40");
	var $ArrView = array("lv001" => "0", "lv002" => "0", "lv003" => "0", "lv004" => "20", "lv005" => "0", "lv006" => "10", "lv007" => "0", "lv008" => "10", "lv009" => "21", "lv010" => "10", "lv011" => "10", "lv012" => "0", "lv013" => "0", "lv014" => "0", "lv015" => "0", "lv016" => "22", "lv017" => "10", "lv027" => "0", "lv028" => "22", "lv088" => "0", "lv096" => "10", "lv097" => "10", "lv098" => "10", "lv099" => "10", "lv100" => "10", "lv101" => "10", "lv102" => "0", "lv103" => "22", "lv104" => "0", "lv109" => "4", "lv198" => "0", "lv117" => "10");
	var $ArrViewEnter = array("lv017" => "-1", "lv003" => "789", "lv005" => "-1", "lv088" => "-1", "lv089" => "-1", "lv049" => "-1", "lv012" => "88", "lv027" => "3");
	var $Tables = array('lv003' => 'sl_lv0007', 'lv005' => 'sl_lv0005', 'lv009' => 'hr_lv0018');
	var $TableLink = array("lv003" => "concat(lv001,@! @!,lv002)");
	public $LE_CODE = "NjlIUS02VFdULTZIS1QtNlFIQQ==";
	function __construct($vCheckAdmin, $vUserID, $vright)
	{
		$this->DateCurrent = GetServerDate() . " " . GetServerTime();
		$this->Set_User($vCheckAdmin, $vUserID, $vright);
		$this->isRel = 1;
		$this->isHelp = 1;
		$this->isConfig = 0;
		$this->isRpt = 0;
		$this->isFil = 1;

		$this->isApr = 0;
		$this->isUnApr = 0;
		$this->lang = $_GET['lang'];
	}
	function LV_OptionType()
	{
		if ($this->TypePC == 1) {
			if ($this->isDaCapPhat > 3)
				$this->DefaultFieldList = "lv003,lv027,lv028,lv088,lv089,lv049,lv039,lv015,lv012,lv006,lv004,lv005,lv008,lv010,lv017";
			else
				$this->DefaultFieldList = "lv003,lv088,lv089,lv049,lv039,lv015,lv012,lv006,lv004,lv005,lv008,lv010,lv017";
		} else {
			if ($this->isDaCapPhat > 3)
				$this->DefaultFieldList = "lv003,lv027,lv028,lv088,lv089,lv049,lv039,lv015,lv006,lv004,lv005,lv008,lv010,lv017";
			else
				$this->DefaultFieldList = "lv003,lv088,lv089,lv049,lv039,lv015,lv006,lv004,lv005,lv008,lv010,lv017";
		}
	}
	protected function LV_CheckLock()
	{
		$lvsql = "select lv007 from cr_lv0150 B where  B.lv001='$this->lv002'";
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
		$vsql = "select * from  cr_lv0151";
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
			$this->lv020 = $vrow['lv020'];
			$this->lv027 = $vrow['lv027'];
			$this->lv028 = $vrow['lv028'];
			$this->lv039 = $vrow['lv039'];

			$this->lv087 = $vrow['lv087'];
			$this->lv088 = $vrow['lv088'];
			$this->lv089 = $vrow['lv089'];
			$this->lv090 = $vrow['lv090'];
			$this->lv091 = $vrow['lv091'];
			$this->lv092 = $vrow['lv092'];
			$this->lv093 = $vrow['lv093'];
			$this->lv094 = $vrow['lv094'];
			$this->lv095 = $vrow['lv095'];
			$this->lv096 = $vrow['lv096'];
			$this->lv097 = $vrow['lv097'];
			$this->lv098 = $vrow['lv098'];
			$this->lv099 = $vrow['lv099'];
			$this->lv100 = $vrow['lv100'];
			$this->lv101 = $vrow['lv101'];
			$this->lv198 = $vrow['lv198'];
		}
	}
	function LV_LoadID($vlv001)
	{
		$lvsql = "select * from  cr_lv0151 Where lv001='$vlv001'";
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
			$this->lv020 = $vrow['lv020'];
			$this->lv027 = $vrow['lv027'];
			$this->lv028 = $vrow['lv028'];
			$this->lv039 = $vrow['lv039'];

			$this->lv087 = $vrow['lv087'];
			$this->lv088 = $vrow['lv088'];
			$this->lv089 = $vrow['lv089'];
			$this->lv090 = $vrow['lv090'];
			$this->lv091 = $vrow['lv091'];
			$this->lv092 = $vrow['lv092'];
			$this->lv093 = $vrow['lv093'];
			$this->lv094 = $vrow['lv094'];
			$this->lv095 = $vrow['lv095'];
			$this->lv096 = $vrow['lv096'];
			$this->lv097 = $vrow['lv097'];
			$this->lv098 = $vrow['lv098'];
			$this->lv099 = $vrow['lv099'];
			$this->lv100 = $vrow['lv100'];
			$this->lv101 = $vrow['lv101'];
			$this->lv198 = $vrow['lv198'];
		}
	}
	function LV_Insert()
	{
		// if ($this->isAdd == 0) return false;
		// if (!$this->LV_CheckLocked($this->lv002)) return false;
		$lvsql = "insert into cr_lv0151 (lv002,lv003,lv004,lv005,lv006,lv007,lv008,lv009,lv010,lv011,lv012,lv013,lv014,lv015,lv016,lv017,lv018,lv019,lv020,lv198,lv039) values('$this->lv002','$this->lv003','$this->lv004','$this->lv005','$this->lv006','$this->lv007','$this->lv008','$this->lv009','$this->lv010','$this->lv011','$this->lv012','$this->lv013','$this->lv014','$this->lv015','$this->lv016','$this->lv017','$this->lv018','$this->lv019','$this->lv020','$this->lv198','$this->lv039')";
		$vReturn = db_query($lvsql);
		if ($vReturn) {
			$this->InsertIDNow = sof_insert_id();
			$this->InsertLogOperation($this->DateCurrent, 'cr_lv0151.insert', sof_escape_string($lvsql));
		}
		return $vReturn;
	}
	function LV_InsertTemp($vTemID, $vlv002, $vWhId)
	{

		$lvsql = "select lv001,'$vTemID' lv002,lv003,lv004,lv005,lv006,lv007,lv008,lv009,lv010,lv011,lv012,lv013,lv014,lv015,lv016,lv017,lv018,lv019,lv020,lv198,lv039 from cr_lv0149 where lv002='$vlv002' and lv004<>0";
		$vReturn = db_query($lvsql);
		while ($vrow = db_fetch_array($vReturn)) {
			$this->lv002 = $vrow['lv002'];
			$this->lv003 = $vrow['lv003'];
			$this->lv004 = $vrow['lv004'];
			$this->lv005 = $vrow['lv005'];
			$this->lv006 = $vrow['lv004'];
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
			$this->lv027 = $vrow['lv027'];
			$this->lv028 = $vrow['lv028'];
			$this->lv039 = $vrow['lv039'];
			$this->lv198 = $vrow['lv198'];
			$vKetQua = $this->LV_Insert();
			if ($vKetQua) {
				$vAttachID = $this->InsertIDNow;
				$lvsql = "insert into erp_sof_documents_v4_0.cri_lv0151(lv002,lv003,lv004,lv005,lv006,lv007,lv008,lv009) select '$vAttachID',lv003,lv004,lv005,lv006,'$vInsertID',lv008,lv009 from erp_sof_documents_v4_0.cri_lv0149 where lv007='" . $vrow['lv001'] . "'";
				$vReturn1 = db_query($lvsql);
				if ($vReturn1) {
					$this->InsertLogOperation($this->DateCurrent, 'erp_sof_documents_v4_0.cri_lv0151.insert', sof_escape_string($lvsql));
					$lvsql1 = "delete from erp_sof_documents_v4_0.cri_lv0149 where lv007='" . $vrow['lv001'] . "'";
					$vReturn2 = db_query($lvsql1);
					//$lvsql1="delete from cr_lv0365 where lv001='".$vrow['lv001']."'";
					//$vReturn= db_query($lvsql1);
				}
			}
			//$this->CheckChild($this->lv003,$vWhId);
			//$this->AddLotReciept($this->lv014,$this->lv003,$vWhId,$this->lv006,$this->lv008,$this->lv019,$this->lv015,$this->lv011);
		}
		if ($vReturn) $this->LV_DeleteTemp($vlv002);
		return $vReturn;
	}
	function AddLotReciept($lvLotId, $lvItemId, $lvWhId, $lvColor, $lvSize, $lvTypeSize, $lvNote, $lvExpireDate)
	{
		return;
		if ($this->CheckLot($lvLotId, $lvItemId, $lvWhId) <= 0) {
			$lvsql = "insert into wh_lv0020 (lv001,lv002,lv003,lv004,lv005,lv006,lv007,lv008) values('$lvLotId','$lvItemId','$lvWhId','$lvExpireDate','$lvColor','$lvSize',concat(CURRENT_DATE(),' ',CURRENT_TIME()),'$lvNote')";
			$vReturn = db_query($lvsql);
			if ($vReturn) $this->InsertLogOperation($this->DateCurrent, 'wh_lv0020.insert', sof_escape_string($lvsql));
		}
	}
	function CheckLot($lvLotId, $lvItemId, $lvWhId)
	{
		$sqlC = "SELECT COUNT(*) AS nums FROM wh_lv0020 WHERE lv001='$lvLotId' and lv002='$lvItemId' and lv003='$lvWhId'";
		$bResultC = db_query($sqlC);
		$arrRowC = db_fetch_array($bResultC);
		return $arrRowC['nums'];
	}
	function LV_InsertTempSLChild($vInvoiceID, $vTemID, $vQty)
	{
		$lvsql = "select '$vInvoiceID' lv002,A.lv003,A.lv004*$vQty lv004,A.lv005 lv005,'' lv006,'' lv007,B.lv007 lv008,B.lv008 lv009,'' lv010,'' lv011,concat(CURDATE(),' ',CURTIME()) lv016,B.lv016 WHID from mn_lv0004 A inner join sl_lv0007 B on A.lv003=B.lv001  where A.lv002='$vTemID'";
		$vReturn = db_query($lvsql);
		while ($vrow = db_fetch_array($vReturn)) {
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
			$this->lv027 = $vrow['lv027'];
			$this->lv028 = $vrow['lv028'];
			$this->WHID = $vrow['WHID'];
			if ($this->lv004 > 0) {
				$this->LV_Insert();
			}
		}
	}
	function LV_Insert_FI_LI_FO($state, $vwhid, $vItem)
	{
		if ($state == "LIFO")
			return $this->LV_Insert_LotConfirm($vItem, $vwhid, 'desc');
		else
			return $this->LV_Insert_LotConfirm($vItem, $vwhid, 'asc');
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
				$lvsql = "insert into cr_lv0151 (lv002,lv003,lv004,lv005,lv006,lv007,lv008,lv009,lv010,lv011,lv012,lv013,lv014,lv015,lv016,lv017,lv018,lv019,lv020) values('$this->lv002','$this->lv003','$slxuat','$this->lv005','$this->lv006','$this->lv007','$this->lv008','$this->lv009','$this->lv010','$this->lv011','$this->lv012','$this->lv013','$this->lv014','$this->lv015','$this->lv016','$this->lv017','$this->lv018','$this->lv019','$this->lv020')";
				$vReturn1 = db_query($lvsql);
				if ($vReturn1) {
					$this->InsertLogOperation($this->DateCurrent, 'cr_lv0151.insert', sof_escape_string($lvsql));
				}
				if ($this->lv004 <= 0) return $vReturn1;
			}
		}
		return $vReturn;
	}
	function LV_InsertTempSL($vInvoiceID, $vlv002, $vWarehourID)
	{
		$lvsql = "select '$vInvoiceID' lv002,A.lv003,A.lv004,B.lv004 lv005,'' lv006,'' lv007,A.lv006 lv008,A.lv007 lv009,A.lv008 lv010,A.lv011 lv011,concat(CURDATE(),' ',CURTIME()) lv016,B.lv016 WHID,B.lv012 TypeWH from sl_lv0014 A inner join sl_lv0007 B on A.lv003=B.lv001  where A.lv002='$vlv002'";
		$vReturn = db_query($lvsql);
		while ($vrow = db_fetch_array($vReturn)) {
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
			$this->WHID = $vrow['WHID'];
			if ($this->lv004 > 0) {
				if ($vrow['TypeWH'] == 'FIFO' || $vrow['TypeWH'] == 'LIFO')
					$this->LV_Insert_FI_LI_FO($vrow['TypeWH'], $vWarehourID, $this->lv003);
				else
					$this->LV_Insert();
				$this->LV_InsertTempSLChild($vInvoiceID, $this->lv003, $this->lv004);
			}
		}
		return $vReturn;
	}
	function LV_DeleteTemp($vlv002)
	{
		$lvsql = "DELETE FROM cr_lv0149  WHERE cr_lv0149.lv002='$vlv002'";
		$vReturn = db_query($lvsql);
		return $vReturn;
	}
	function LV_Update()
	{
		// if ($this->isEdit == 0) return false;
		// if (!$this->LV_CheckLocked($this->lv002)) return false;
		//$this->lv015 = ($this->lv015!="")?recoverdate(($this->lv015), $this->lang):$this->DateDefault;
		$lvsql = "Update cr_lv0151 set lv002='$this->lv002',lv003='$this->lv003',lv004='$this->lv004',lv005='$this->lv005',lv006='$this->lv006',lv007='$this->lv007',lv008='$this->lv008',lv009='$this->lv009',lv010='$this->lv010',lv011='$this->lv011',lv012='$this->lv012',lv013='$this->lv013',lv014='$this->lv014',lv015='$this->lv015',lv016='$this->lv016' where  lv001='$this->lv001' and lv013<>'1';";
		$vReturn = db_query($lvsql);
		if ($vReturn) $this->InsertLogOperation($this->DateCurrent, 'cr_lv0151.update', sof_escape_string($lvsql));
		return $vReturn;
	}
	function LV_UpdateEdit($vOldlv004, $vOldlv006)
	{
		if ($this->isEdit == 0) return false;
		if (!$this->LV_CheckLocked($this->lv002)) return false;
		$vlv003 = $this->GetParentWH($this->lv002);
		$lvsql = "Update cr_lv0151 set lv002='$this->lv002',lv003='$this->lv003',lv004='$this->lv004',lv005='$this->lv005',lv006='$this->lv006',lv007='$this->lv007',lv008='$this->lv008',lv009='$this->lv009',lv010='$this->lv010',lv011='$this->lv011',lv012='$this->lv012',lv013='$this->lv013',lv014='$this->lv014',lv015='$this->lv015',lv016='$this->lv016' where  lv001='$this->lv001';";
		$vReturn = db_query($lvsql);
		if ($vReturn) {
			$this->InsertLogOperation($this->DateCurrent, 'cr_lv0151.update', sof_escape_string($lvsql));
		}
		return $vReturn;
	}
	function LV_CheckLocked($vlv002)
	{
		$lvsql = "select lv007 from  cr_lv0150 Where lv001='$vlv002'";
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
	function LV_UpdateChangeChild($lvsql)
	{
		$vReturn = db_query($lvsql);
		if ($vReturn) $this->InsertLogOperation($this->DateCurrent, 'cr_lv0151.update', sof_escape_string($lvsql));
		return $vReturn;
	}
	function LV_Delete($lvarr)
	{
		if ($this->isDel == 0) return false;
		$lvsql = "DELETE FROM cr_lv0151  WHERE cr_lv0151.lv001 IN ($lvarr) and lv013<>'1' and (select lv007 from cr_lv0150 B where  B.lv001= cr_lv0151.lv002)<=0  ";
		$vReturn = db_query($lvsql);
		if ($vReturn) {

			$this->InsertLogOperation($this->DateCurrent, 'cr_lv0151.delete', sof_escape_string($lvsql));
		}
		return $vReturn;
	}
	function LV_DeleteDetail($lvarr)
	{
		$sqlD = "SELECT A.lv001 lv001,A.lv002 lv002,A.lv003 lv003,A.lv004 lv004,A.lv006 lv006,B.lv002 lvb002 FROM cr_lv0151 A inner join cr_lv0150 B on A.lv002=B.lv001 WHERE A.lv001 IN ($lvarr) and lv013<>'1' and (select lv007 from cr_lv0150 B where  B.lv001= A.lv002)<=0;";
		$vresult = db_query($sqlD);
		$strReturn = false;
		while ($vrow = db_fetch_array($vresult)) {

			$vsql = "update wh_lv0012 set lv004=lv004+" . $vrow['lv004'] . ",lv006=lv006+" . $vrow['lv006'] . " where lv002='" . $vrow['lv003'] . "' and lv003='" . $vrow['lvb002'] . "'";
			if (db_query($vsql)) {
				$tsql = "delete from cr_lv0151 where lv001='" . $vrow['lv001'] . "'";
				if (db_query($tsql)) {
					if ($this->obj_wb_lv0006 != null) {
						$this->obj_wb_lv0006->LV_Update_Balance($vrow['lv003'], $vrow['lvb002']);
					}
					$strReturn = true;
				} else {
					$vsql = "update wh_lv0012 set lv004=lv004-" . $vrow['lv004'] . ",lv006=lv006-" . $vrow['lv006'] . " where lv002='" . $vrow['lv003'] . "' and lv003='" . $vrow['lvb002'] . "'";
					db_query($vsql);
					$strReturn = false;
				}
			}
		}
		return $strReturn;
	}
	function DeleteOrther($strar)
	{
		$sqlD = "SELECT A.*,B.lv002 lvb002 FROM cr_lv0151 A inner join cr_lv0150 B on A.lv002=B.lv001 WHERE B.lv007<=0;";
		$vresult = db_query($sqlD);
		$strReturn = false;
		while ($vrow = db_fetch_array($vresult)) {
			$vsql = "update wh_lv0012 set lv004=lv004+" . $vrow['lv004'] . ",lv006=lv006+" . $vrow['lv006'] . " where lv002='" . $vrow['lv003'] . "' and lv003='" . $vrow['lvb002'] . "'";
			if (db_query($vsql)) {
				$tsql = "delete from cr_lv0151 where lv001='" . $vrow['lv001'] . "'";
				if (db_query($tsql)) {
					if ($this->obj_wb_lv0006 != null) {
						$this->obj_wb_lv0006->LV_Update_Balance($vrow['lv003'], $vrow['lvb002']);
					}
					$strReturn = true;
					$strReturn = true;
				} else {
					$vsql = "update wh_lv0012 set lv004=lv004-" . $vrow['lv004'] . ",lv006=lv006-" . $vrow['lv006'] . " where lv002='" . $vrow['lv003'] . "' and lv003='" . $vrow['lvb002'] . "'";
					db_query($vsql);
					$strReturn = false;
				}
			}
		}
		return $strReturn;
	}
	function CheckChild($vlv002, $vlv003)
	{
		$sqlD = "SELECT count(*) nums FROM wh_lv0012 WHERE lv002='$vlv002' and lv003='$vlv003'";
		$vresult = db_query($sqlD);
		if ($vresult) {
			$vrow = db_fetch_array($vresult);
			if ($vrow['nums'] <= 0) {
				$lvsql = "insert into wh_lv0012 (lv002,lv003,lv004,lv005,lv006,lv007,lv008,lv009,lv010,lv011,lv012,lv013) values('$vlv002','$vlv003','0','$this->lv005','0','$this->lv007','$this->lv008','$this->lv009','$this->lv010','$this->lv011','$this->lv012','$this->lv013')";
				db_query($lvsql);
			}
		}
	}
	function GetParentWH($vlv001)
	{
		$sqlD = "SELECT lv002 FROM cr_lv0150 WHERE lv001='$vlv001' ";
		$vresult = db_query($sqlD);
		if ($vresult) {
			$vrow = db_fetch_array($vresult);
			return $vrow['lv002'];
		}
	}
	function LV_QuetKhoDNCVT($vContractID)
	{
		$vAr91 = array();
		$vAr93 = array();
		$vAr93T = array();
		$vAr91T = array();
		$lvsql = "select A.lv001,B.lv002 Kho,A.lv003  from cr_lv0151 A inner join cr_lv0150 B on A.lv002=B.lv001   where A.lv002='$vContractID'";
		$vresult = db_query($lvsql);
		$vi = 0;
		$vA91 = 0;
		while ($vrow = db_fetch_array($vresult)) {
			$vKhoCheck = "'" . str_replace(",", "','", $vrow['Kho']) . "'";
			if ($vrow['Kho'] == '3') {
				//Kho 1
				$vlv090 = (float) $this->objlot->LV_Get_slt_more_kholinhkien($vrow['lv003'], $vKhoCheck);
				//Kho 1
				$vlv090 = $vlv090 + (float) $this->objlot->LV_Get_slt_more($vrow['lv003'], $vKhoCheck);
			} else {
				//Kho 1
				$vlv090 = (float) $this->objlot->LV_Get_slt_more($vrow['lv003'], $vKhoCheck);
				//Kho 2,3
				$vlv092 = (float) $this->objlot->LV_Get_slt_more($vrow['lv003'], $vKhoCheck . ",'CAPPHAT'");
			}
			//Tất cả kho trừ kho 0
			$lvsql1 = "update  cr_lv0151 set lv090='$vlv090',lv091=($vlv090-lv004),lv092='$vlv092',lv093=($vlv092-lv004),lv087=IF($vlv090>=lv004,lv004,IF(($vlv090-lv004)>=0,$vlv090,0)),lv094='" . $this->LV_UserID . "',lv095=now()   where lv001='" . $vrow['lv001'] . "'";
			$vresult1 = db_query($lvsql1);
			if ($vresult1) {

				$this->InsertLogOperation($this->DateCurrent, 'cr_lv0151.update', sof_escape_string($lvsql1));
			}
		}
		//$this->LV_QuetKhoCopyBOM($vContractID);
		//$this->LV_QuetKhoDNGHBOM($vContractID);
		return;
	}
	///////////Quét mua hàng/////////
	function LV_QuetMuaHang($vContractID)
	{
		$vAr91 = array();
		$vAr93 = array();
		$vAr93T = array();
		$vAr91T = array();
		$lvsql = "select lv001,lv003  from cr_lv0151 A   where A.lv002='$vContractID'";
		$vresult = db_query($lvsql);
		$vi = 0;
		$vA91 = 0;
		while ($vrow = db_fetch_array($vresult)) {
			//Kho 1
			$vLoaiNguon = 2;
			$vMaNguon = $vContractID;
			$vItemID = $vrow['lv003'];
			$vArrKho = $this->objbuyer->LV_GetSLPMH($vLoaiNguon, $vMaNguon, $vItemID);
			$vSumOK = $vArrKho[1] + $vArrKho[2] + $vArrKho[3];
			$vSLDaDuyet = $vArrKho[2] + $vArrKho[3];
			$vSLDoiDuyet = $vArrKho[1];
			$vSLLapDH = $vArrKho[0];
			$vSLHuy = $vArrKho[-1];
			//Tất cả kho trừ kho 0
			echo $lvsql1 = "update  cr_lv0151 set 
			lv096='$vSumOK',
			lv097=(lv091+lv096),
			lv098='$vSLDaDuyet',
			lv099='$vSLDoiDuyet',
			lv100='$vSLLapDH',
			lv101='$vSLHuy',lv102='" . $this->LV_UserID . "',lv103=now()   where lv001='" . $vrow['lv001'] . "'";
			$vresult1 = db_query($lvsql1);
			if ($vresult1) {

				$this->InsertLogOperation($this->DateCurrent, 'cr_lv0114.update', sof_escape_string($lvsql1));
			}
		}
		//$this->LV_QuetKhoCopyBOM($vContractID);
		//$this->LV_QuetKhoDNGHBOM($vContractID);
		return;
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
		if ($this->lv001 != "") $strCondi = $strCondi . " and A.lv001  like '%$this->lv001%'";
		if ($this->lv002 != "") $strCondi = $strCondi . " and A.lv002  = '$this->lv002'";

		if ($this->lv003 != "") {
			$vListItem = "'" . str_replace(",", "','", $this->lv003) . "'";
			$strCondi = $strCondi . " and A.lv003  in ($vListItem)";
		}

		if ($this->lv004 != "") $strCondi = $strCondi . " and A.lv004  like '%$this->lv004%'";
		if ($this->lv005 != "") $strCondi = $strCondi . " and A.lv005  like '%$this->lv005%'";
		if ($this->lv006 != "") $strCondi = $strCondi . " and A.lv006  like '%$this->lv006%'";
		if ($this->lv007 != "")  $strCondi = $strCondi . " and A.lv007 like '%$this->lv007%'";
		if ($this->lv008 != "")  $strCondi = $strCondi . " and A.lv008 like '%$this->lv008%'";
		if ($this->lv009 != "")  $strCondi = $strCondi . " and A.lv009 like '%$this->lv009%'";
		if ($this->lv010 != "")  $strCondi = $strCondi . " and A.lv010 like '%$this->lv010%'";
		if ($this->lv011 != "")  $strCondi = $strCondi . " and A.lv011 like '%$this->lv011%'";
		if ($this->lv012 != "")  $strCondi = $strCondi . " and A.lv012 like '%$this->lv012%'";
		if ($this->lv013 != "")  $strCondi = $strCondi . " and A.lv013 like '%$this->lv013%'";
		if ($this->lv014 != "")  $strCondi = $strCondi . " and A.lv014 like '%$this->lv014%'";
		if ($this->lv015 != "")  $strCondi = $strCondi . " and A.lv015 like '%$this->lv015%'";
		if ($this->lv016 != "")  $strCondi = $strCondi . " and A.lv016 like '%$this->lv016%'";
		if ($this->lv809 != "")  $strCondi = $strCondi . " and BB.lv009 like '%$this->lv809%'";
		if ($this->lv901 != "")  $strCondi = $strCondi . " and BB.lv001 like '%$this->lv901%'";
		if ($this->lv909 != "")  $strCondi = $strCondi . " and BB.lv009 like '%$this->lv909%'";
		if ($this->lv906 != "")  $strCondi = $strCondi . " and BB.lv006 = '$this->lv906'";
		if ($this->lv911 != "")  $strCondi = $strCondi . " and BB.lv011 = '$this->lv911'";
		//$strwh=$this->Get_WHControler();
		//$strCondi=$strCondi." and A.lv002 in (select B.lv001 from cr_lv0150 B where B.lv002 in ($strwh))";
		return $strCondi;
	}
	////////////////Count///////////////////////////
	function GetCount()
	{
		$sqlC = "SELECT COUNT(*) AS nums FROM cr_lv0151 A  inner join cr_lv0150 BB on A.lv002=BB.lv001 WHERE 1=1 " . $this->GetCondition();
		$bResultC = db_query($sqlC);
		$arrRowC = db_fetch_array($bResultC);
		return $arrRowC['nums'];
	}
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
		$lvsql = "select lv001,lv002 from wh_lv0003";
		$vReturn = db_query($lvsql);
		while ($vrow = db_fetch_array($vReturn)) {
			$this->ArrSup[$vrow['lv001']] = $vrow['lv002'];
		}
		return;
	}
	//////////////////////Buil list////////////////////
	//////////////////////Buil list////////////////////
	function LV_BuilList($lvList, $lvFrom, $lvChkAll, $lvChk, $curRow, $maxRows, $paging, $lvOrderList, $lvSortNum)
	{
		if ($curRow < 0) $curRow = 0;
		$this->LV_CheckLock();
		if ($lvList == "") $lvList = $this->DefaultFieldList;
		if ($this->isView == 0) return false;
		//$this->LV_GetCustomer();
		//$this->LV_GetSupplier();
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
		<!--<tr><td colspan=\"6\">@#03</td></tr>
		<tr><td colspan=\"6\">@#04</td></tr>-->
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
		$lvTdH = "<td width=\"@01\" class=\"lvhtable\">@02</td>";
		$lvTd = "<td align=@#05>@02</td>";
		$strF = "<tr><td colspan=\"2\">&nbsp;</td>";
		$lvTdF = "<td align=\"right\"><strong>@01</strong></td>";
		$lvSumTd = "<tr><td colspan=\"" . count($lstArr) . "\"><strong>" . $this->ArrPush[98] . ": @#01</strong></td></tr>";
		$lvTotalNumTd = "<tr><td colspan=\"" . count($lstArr) . "\"><strong>" . $this->ArrPush[99] . ": @#01</strong></td></tr>";
		$sqlS = "SELECT A.*,BB.lv005 lv102,BB.lv006 lv103,A.lv003 lv088,A.lv003 lv089,BB.lv009 lv109 FROM cr_lv0151 A inner join cr_lv0150 BB on A.lv002=BB.lv001   WHERE 1=1  " . $this->GetCondition() . " $strSort LIMIT $curRow, $maxRows";
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
			if ($this->ArrViewEnter[$vField] = $this->ArrViewEnter[$vField] ?? 0) $this->ArrViewEnter[$vField] = 0;
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
		if ($this->isAdd == 1)
			$strTrEnter = "<tr class='entermobil'><td colspan='2'>" . '<img tabindex="2" border="0" title="Add" class="imgButton" onclick="Save()" onmouseout="this.src=\'' . $this->Dir . '../images/iconcontrol/btn_add.jpg\';" onmouseover="this.src=\'' . $this->Dir . '../images/iconcontrol/btn_add_02.jpg\';" src="' . $this->Dir . '../images/iconcontrol/btn_add.jpg" onkeypress="return CheckKey(event,11)">' . "</td>" . $strTrEnter . "</tr>";
		else
			$strTrEnter = "";
		$vsumamount = 0;
		$vtotalnum = 0;
		while ($vrow = db_fetch_array($bResult)) {
			$strL = "";
			$vorder++;
			$vrow['lv117'] = $vrow['lv004'] * $vrow['lv008'];
			$slv117 = $slv117 + $vrow['lv117'];
			$vlineamount = $vrow['lv004'] * $vrow['lv008'] + $vrow['lv004'] * $vrow['lv008'] * $vrow['lv010'] / 100;
			$vsumamount = $vsumamount + $vlineamount;
			$vtotalnum = $vtotalnum + $vrow['lv004'];
			$slv006 = $slv006 + $vrow['lv006'];

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
					case 'lv005':
						if ($this->GetEdit() > 0) {
							$vlv093 = $vrow['lv005'];
							$vField = 'lv005';
							$lvTdTextBox = '<select  class="selenterquick"  tabindex="2" style="width:100%;min-width:30px" onKeyPress="return CheckKey(event,7)" onblur="UpdateText(this,\'' . $vrow['lv001'] . '\',5)">
							<option value="">...</option>
						' . $this->LV_LinkField($vField, $vrow[$vField]) . '</select>';

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
							$lvImg = "<center><a target='_blank' href='" . $this->Dir . "cr_lv0151/readfile.php?UserID=" . $vrow['lv001'] . "&type=8&size=0'><img name=\"imgView\" border=\"0\" style=\"border-color:#CCCCCC\" title=\"\" alt=\"Image\" width1=\"96px\" height=\"48px\" src=\"" . $this->Dir . "cr_lv0151/readfile.php?UserID=" . $vrow['lv001'] . "&type=8&size=1\" /></a></center>";
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
							<iframe  height=28 width="100%" marginheight=0 marginwidth=0 frameborder=0 src="' . $this->Dir . 'cr_lv0151?childdetailfuncv1=upload&ViTriUp=8&UserID=' . $vrow['lv001'] . '&lang=' . $plang . '&Dir=' . $this->Dir . '"></iframe>
							</div>
							</td>
							
						</tr>
						</table>	</td>
								';
							$vTemp = str_replace("@02", $vrow[$lstArr[$i]], str_replace("@01", $vrow['lv001'], $vTempEnter));
						} else {
							$lvImg = "<td  ><a target='_blank' href='" . $this->Dir . "cr_lv0151/readfile.php?UserID=" . $vrow['lv001'] . "&type=8&size=0'><img name=\"imgView\" border=\"0\" style=\"border-color:#CCCCCC\" title=\"\" alt=\"Image\" width1=\"96px\" height=\"48px\" src=\"" . $this->Dir . "cr_lv0151/readfile.php?UserID=" . $vrow['lv001'] . "&type=8&size=1\" /></a></td>";
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
		$strF = str_replace("<!--lv006-->", $this->FormatView($slv006, 10), $strF);
		$strF = str_replace("<!--lv017-->", $this->FormatView($vsumamount, 10), $strF);
		$strF = str_replace("<!--lv117-->", $this->FormatView($slv117, 10), $strF);
		$strF = str_replace("<!--lv003-->", '<p style="text-align:center;padding:5px">Tổng:</p>', $strF);
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
			window.open('" . $this->Dir . "cr_lv0151/?lang=" . $this->lang . "&childdetailfunc='+value+'&ID=" . base64_encode($this->lv002) . "','','width=800,height=600,left=200,top=100,screenX=0,screenY=100,resizable=yes,status=no,scrollbars=yes,menubar=yes');
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
			<td width=1% onclick=\"Select_Check('$lvChk@03',$lvFrom, '$lvChk', '$lvChkAll')\">@03</td>
			@#01
		</tr>
		";
		$lvTdF = "<td align=\"@#05\"><strong>@01</strong></td>";
		$strF = "<tr><td colspan=\"1\">&nbsp;</td>";
		$lvTdH = "<td width=\"@01\" class=\"lvhtable\">@02</td>";
		$lvTd = "<td align=@#05>@02</td>";
		$sqlS = "SELECT A.*,BB.lv005 lv102,BB.lv006 lv103,BB.lv005 lv102,BB.lv006 lv103,A.lv003 lv088,A.lv003 lv089,BB.lv006  lv099,BB.lv009 lv109 FROM cr_lv0151 A inner join cr_lv0150 BB on A.lv002=BB.lv001   WHERE 1=1  " . $this->GetCondition() . " $strSort LIMIT $curRow, $maxRows";
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
			$vlineamount = $vrow['lv004'] * $vrow['lv008'] - $vrow['lv004'] * $vrow['lv008'] * $vrow['lv011'] / 100 + $vrow['lv004'] * $vrow['lv008'] * $vrow['lv010'] / 100;
			$vsumamount = $vsumamount + $vlineamount;
			$vtotalnum = $vtotalnum + $vrow['lv004'];
			for ($i = 0; $i < count($lstArr); $i++) {
				if ($lstArr[$i] == 'lv017') {
					$vTemp = str_replace("@02", $this->getvaluelink($lstArr[$i], $this->FormatView($vlineamount, (int)$this->ArrView[$lstArr[$i]])), $this->Align($lvTd, (int)$this->ArrView[$lstArr[$i]]));
				} elseif ($lstArr[$i] == 'lv013') {
					$vslline = $this->objlot->LV_Get_slt_lot_all($vrow['lv014'], $vrow['lv003']);
					$vTemp = str_replace("@02", $this->getvaluelink($lstArr[$i], $this->FormatView($vslline, (int)$this->ArrView[$lstArr[$i]])), $this->Align($lvTd, (int)$this->ArrView[$lstArr[$i]]));
				} elseif ($lstArr[$i] == "lv104") {
					if ($vrow['lv102'] == 'KHACHHANG')
						$vTitle = $this->ArrCus[$vrow['lv103']];
					elseif ($vrow['lv102'] == 'NHACUNGCAP')
						$vTitle = $this->ArrSup[$vrow['lv103']];
					else
						$vTitle = '';
					$vTemp = str_replace("@02", $this->getvaluelink($lstArr[$i], $this->FormatView($vTitle, (int)$this->ArrView[$lstArr[$i]])), $this->Align($lvTd, (int)$this->ArrView[$lstArr[$i]]));
				} else
					$vTemp = str_replace("@02", $this->getvaluelink($lstArr[$i], $this->FormatView($vrow[$lstArr[$i]], (int)$this->ArrView[$lstArr[$i]])), $this->Align($lvTd, (int)$this->ArrView[$lstArr[$i]]));
				$strL = $strL . $vTemp;
			}


			$strTr = $strTr . str_replace("@#01", $strL, str_replace("@02", $vrow['lv001'], str_replace("@03", $vorder, str_replace("@01", $vorder % 2, $lvTr))));
		}
		$strF = $strF . "</tr>";
		$strF = str_replace("<!--lv004-->", $this->FormatView(round($vtotalnum, 0), 10), $strF);
		$strF = str_replace("<!--lv017-->", $this->FormatView(round($vsumamount, 0), 10), $strF);
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
		$this->LV_GetCustomer();
		$this->LV_GetSupplier();
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
		$lvTrH = "<tr>
			<td width=\"1%\" class=\"lvtabletd\"><strong>" . $this->ArrPush[1] . "</strong></td>
			
			@#01
		</tr>
		";
		$lvTr = "<tr class=\"lvlinehtable@01\">
			<td width=1% onclick=\"Select_Check('$lvChk@03',$lvFrom, '$lvChk', '$lvChkAll')\">@03</td>
			@#01
		</tr>
		";
		$lvTrTotal = "<tr >
			<td class=\"lvtabletotal\"  colspan=@04  >@03</td>
			<td class=\"lvtabletotal\" >@01</td>
		</tr>
		";
		$lvTdF = "<td align=\"@#05\" class=\"lvtabletotal\" ><strong>@01</strong></td>";
		$strF = "<tr><td colspan=\"1\" class=\"lvtabletotal\">&nbsp;</td>";
		$lvTdH = "<td height=\"35\" width=\"@01\" class=\"lvtabletd\" ><strong>@02</strong></td>";
		$lvTd = "<td align=@#05>@02</td>";
		$sqlS = "SELECT A.*,BB.lv005 lv102,BB.lv006 lv103,BB.lv005 lv102,BB.lv006 lv103,A.lv003 lv088,A.lv003 lv089,BB.lv006  lv099,BB.lv009 lv109 FROM cr_lv0151 A inner join cr_lv0150 BB on A.lv002=BB.lv001   WHERE 1=1  " . $this->GetCondition() . " $strSort LIMIT $curRow, $maxRows";
		$vorder = $curRow;
		$bResult = db_query($sqlS);
		$this->Count = db_num_rows($bResult);
		$strTrH = "";
		$strH = "";
		$strSubTotal = 0;
		$strSubTax = 0;
		$strTotalAmount = 0;
		$vUnitPrice = "VNĐ";
		$sumtrue = false;
		for ($i = 0; $i < count($lstArr); $i++) {
			$vTemp = str_replace("@01", "", $lvTdH);
			$vTemp = str_replace("@02", $this->ArrPush[(int)$this->ArrGet[$lstArr[$i]]], $vTemp);
			$strH = $strH . $vTemp;
			/*if($lstArr[$i]=='lv014')
				{
					$vTemp=str_replace("@01","",$lvTdH);
					$vTemp=str_replace("@02",$this->ArrPush[99],$vTemp);
					$strH=$strH.$vTemp;
				}*/
			if ($lstArr[$i] == 'lv017') $sumtrue = true;
			$vTempF = str_replace("@01", "<!--" . $lstArr[$i] . "-->", $this->Align($lvTdF, (int)$this->ArrView[$lstArr[$i]]));
			$strF = $strF . $vTempF;
		}
		if ($sumtrue == false) $lvTrTotal = "";
		while ($vrow = db_fetch_array($bResult)) {
			$strL = "";
			$vorder++;
			$vtotalnum = $vtotalnum + $vrow['lv004'];
			$vlineamount = $vrow['lv004'] * $vrow['lv008'] - $vrow['lv004'] * $vrow['lv008'] * $vrow['lv011'] / 100 + $vrow['lv004'] * $vrow['lv008'] * $vrow['lv010'] / 100;
			$vsumamount = $vsumamount + $vlineamount;
			for ($i = 0; $i < count($lstArr); $i++) {
				switch ($lstArr[$i]) {
					case 'lv117':
						$vTemp = str_replace("@02", $this->getvaluelink($lstArr[$i], $this->FormatView($vlineamount, (int)$this->ArrView[$lstArr[$i]])), $this->Align($lvTd, (int)$this->ArrView[$lstArr[$i]]));
						break;
					case 'lv104':
						if ($vrow['lv102'] == 'KHACHHANG')
							$vTitle = $this->ArrCus[$vrow['lv103']];
						elseif ($vrow['lv102'] == 'NHACUNGCAP')
							$vTitle = $this->ArrSup[$vrow['lv103']];
						else
							$vTitle = '';
						$vTemp = str_replace("@02", $this->getvaluelink($lstArr[$i], $this->FormatView($vTitle, (int)$this->ArrView[$lstArr[$i]])), $this->Align($lvTd, (int)$this->ArrView[$lstArr[$i]]));
						break;
					default:
						$vTemp = str_replace("@02", $this->getvaluelink($lstArr[$i], $this->FormatView($vrow[$lstArr[$i]], (int)$this->ArrView[$lstArr[$i]])), $this->Align($lvTd, (int)$this->ArrView[$lstArr[$i]]));
						break;
				}
				$strL = $strL . $vTemp;
				/*if($lstArr[$i]=="lv014")
				{
					$vTemp=str_replace("@02",$this->FormatView($this->LV_GetLot($vrow['lv003'],$vrow['lv014']),2),$lvTd);
					$strL=$strL.$vTemp;
					
				}
				*/
			}
			$vUnitPrice = $this->getvaluelink('lv007', $this->FormatView($vrow['lv007'], (int)$this->ArrView[$vrow['lv007']]));
			$strTr = $strTr . str_replace("@#01", $strL, str_replace("@02", $vrow['lv001'], str_replace("@03", $vorder, str_replace("@01", $vorder % 2, $lvTr))));
			if ($vTax > 0 || $vTax == -1) {
				$strSubTotal = $strSubTotal + $vrow['lv004'] * $vrow['lv006'];
			} else {
				$strSubTotal = $strSubTotal + $vrow['lv004'] * $vrow['lv006'] + $vrow['lv004'] * $vrow['lv006'] * $vrow['lv008'] / 100;
			}
		}
		/*$strLine=str_replace("@01",$this->FormatView($strSubTotal,1),$lvTrTotal);
			$strLine=str_replace("@03",$this->ArrPush[15],$strLine);
			$strLine=str_replace("@04",count($lstArr),$strLine);
			$strTr=$strTr.$strLine;
			if($vTax>0)
			{
			$strSubTax=$strSubTotal*$vTax/100;
			$strLine=str_replace("@01",$this->FormatView($strSubTax,1),$lvTrTotal);
			$strLine=str_replace("@03",str_replace("@02",$this->FormatView($vTax,10),$this->ArrPush[16]),$strLine);
			$strLine=str_replace("@04",count($lstArr),$strLine);
			$strTr=$strTr.$strLine;
			}
		
			$strTotalAmount=$strSubTotal+$strSubTax;
			$strLine=str_replace("@01",$this->FormatView($strTotalAmount,1),$lvTrTotal);
			$strLine=str_replace("@03",$this->ArrPush[17],$strLine);
			$strLine=str_replace("@04",count($lstArr),$strLine);
			$strTr=$strTr.$strLine;*/
		$strF = $strF . "</tr>";
		$strF = str_replace("<!--lv004-->", $this->FormatView($vtotalnum, 10), $strF);
		$strF = str_replace("<!--lv003-->", '<p style="text-align:center;padding:5px">Tổng:</p>', $strF);
		$strF = str_replace("<!--lv017-->", $this->FormatView($vsumamount, 10), $strF);
		$lvTable = str_replace("@#02", $strF, $lvTable);
		$strTrH = str_replace("@#01", str_replace("@01", "(" . $vUnitPrice . ")", $strH), $lvTrH);
		return str_replace("@#01", $strTrH . ($strTr ?? ""), $lvTable);
	}
	function LV_GetLot($vItem, $vLotID)
	{
		$lvsql = "select lv004 from  wh_lv0020 Where lv001='$vLotID' and lv002='$vItem'";
		$vresult = db_query($lvsql);
		$vrow = db_fetch_array($vresult);
		if ($vrow) {
			return $vrow['lv004'];
		}
		return '';
	}
	function Get_String_BuilHeader()
	{
		$this->lvHeader = "";
		$strTD = "";
		$sqlS1 = "SELECT lv014  FROM cr_lv0151 A WHERE 1=1  " . $this->GetCondition() . " ";
		$bResult = db_query($sqlS1);
		$ArSize = array();
		$ArColor = array();
		while ($vrow = db_fetch_array($bResult)) {

			$vData = explode("-", $vrow['lv014'], 2);
			if (count($vData) == 1) {
				$ArCol[$vData[0]] = $vData[0];
				//$ArSize['-']='-';
			} else {

				$ArSize[$vData[0]] = $vData[0];
				$ArCol[$vData[1]] = $vData[1];
			}
		}
		$strTD = array();
		$vArSize = $ArSize;
		$this->lvHeader = '<td class="lvhtable" align="center">Mã sản phẩm</td><td class="lvhtable" align="center">Tên </td><td class="lvhtable" align="center">Màu</td>';
		$this->lvFooter = '<tr style="font-weight:bold"><td  align="center">&nbsp</td><td  align="center">&nbsp</td><td  align="center">&nbsp</td><td  align="center">&nbsp</td>';
		$this->ArSize = $ArSize;
		foreach ($ArSize as $size) {
			$this->lvHeader = $this->lvHeader . '<td class="lvhtable" align="center"><b><font color="' . $color . '">' . $size . '</font></b></td>';
			$this->lvFooter = $this->lvFooter . '<td  align="right"><!--' . $size . '--></td>';
		}
		$this->lvHeader = $this->lvHeader . '<td class="lvhtable" align="center">TC SL</td><td class="lvhtable" align="center">Đơn vị</td><td class="lvhtable" align="center">Ghi chú</td>';
		$this->lvFooter = $this->lvFooter . '<td  align="right"><!--allsize--></td><td>&nbsp</td><td>&nbsp;</td></tr>';
		$i = 0;
		$this->ArCol = $ArCol;
		foreach ($ArCol as $Col) {
			$strTD[$Col][1] = '<!--%' . $Col . '%--><td align="center"><!--#stt#--><td align=";left"><!--#item#--></td><td align=";left"><!--#itemname#--></td><td align="center">' . $Col . '</td>';
			foreach ($vArSize as $size) {
				$strTD[$Col][1] = $strTD[$Col][1] . '<td align="right"><!--' . $size . '-' . $Col . '-->' . '</td>';
			}
			$strTD[$Col][1] = $strTD[$Col][1] . '<td align="right"><!--sum:*item*-' . $Col . '-->' . '</td><td align="center"><!--unit:*item*-' . $Col . '-->' . '</td><td align="center"><!--note:*item*-' . $Col . '-->' . '</td>';
		}

		$i++;
		return $strTD;
	}
	function LV_BuilListReportOtherSoon($lvList, $lvFrom, $lvChkAll, $lvChk, $curRow, $maxRows, $paging, $lvOrderList, $vTax)
	{
		$vArrRow = $this->Get_String_BuilHeader();
		if ($lvList == "") $lvList = $this->DefaultFieldList;
		if ($this->isView == 0) return false;
		$this->LV_GetCustomer();
		$this->LV_GetSupplier();
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
		$lvTdH = "<td width=\"@01\" class=\"lvhtable\">@02</td>";
		$lvTd = "<td align=@#05>@02</td>";
		$strF = "<tr><td colspan=\"2\">&nbsp;</td>";
		$lvTdF = "<td align=\"right\"><strong>@01</strong></td>";
		$sqlS = "select * from (SELECT lv003,lv014,sum(lv004) lv004,lv005 FROM cr_lv0151 A WHERE 1=1  " . $this->GetCondition() . " group by lv003,lv014) MP order by MP.lv003";
		$vorder = $curRow;
		$bResult = db_query($sqlS);
		$this->Count = db_num_rows($bResult);
		$strTrH = "";
		$strH = "";
		$ArrItem = array();
		$ArrSumItem = array();
		$ArrSumSize = array();
		$vDataItem = "213412412312";
		while ($vrow = db_fetch_array($bResult)) {
			$vData = explode("-", $vrow['lv014'], 2);
			if (count($vData) == 1) {
				$CodeID = $vData[0];
				$SizeID = "-";
			} else {
				$SizeID = $vData[0];
				$CodeID = $vData[1];
			}
			/*if($vDataItem!=$vrow['lv003'])
			{
				
				
				$ArrItem[$vDataItem][1]=str_replace('<!--sum:'.$CodeID.'-->',$ArrSumItem[$vDataItem][0],$ArrItem[$vDataItem]);
				$ArrItem[$vDataItem]=str_replace('<!--note:'.$CodeID.'-->',$ArrSumItem[$vDataItem][1],$ArrItem[$vDataItem]);
				$vDataItem=$vrow['lv003'];
			}*/
			$ArrSumSize[$SizeID] = $ArrSumSize[$SizeID] + $vrow['lv004'];
			$ArrItem[$vrow['lv003']][0] = $vrow['lv003'];
			$ArrSumItem[$vrow['lv003']][$CodeID][2] = $vrow['lv005'];
			$ArrSumItem[$vrow['lv003']][$CodeID][0] = $ArrSumItem[$vrow['lv003']][$CodeID][0] + $vrow['lv004'];
			$ArrSumItem[$vrow['lv003']][$CodeID][1] = $ArrSumItem[$vrow['lv003']][$CodeID][1] . $vrow['lv015'];
			$ArrItem[$vrow['lv003']][1] = $this->LV_GetValue($vArrRow, $vrow['lv003'], $vrow['lv014'], $ArrItem[$vrow['lv003']][1], $vorder);
			$ArrItem[$vrow['lv003']][1] = str_replace('<!--' . (($vrow['lv014'] == "") ? "-" : $vrow['lv014']) . '-->', $this->FormatView($vrow['lv004'], 10), $ArrItem[$vrow['lv003']][1]);
		}
		foreach ($ArrItem as $stritem) {
			$vArCol = $this->ArCol;
			foreach ($vArCol as $Col) {
				$stritem[1] = str_replace("<!--sum:" . $stritem[0] . "-$Col-->", $ArrSumItem[$stritem[0]][$Col][0], $stritem[1]);
				$stritem[1] = str_replace("<!--note:" . $stritem[0] . "-$Col-->", $ArrSumItem[$stritem[0]][$Col][1], $stritem[1]);
				$stritem[1] = str_replace("<!--unit:" . $stritem[0] . "-$Col-->", $ArrSumItem[$stritem[0]][$Col][2], $stritem[1]);
			}
			$strTrH = $strTrH . $stritem[1];
		}
		$ArSize = $this->ArSize;
		if (count($ArSize) == 0) {
			$vSumAll = $ArrSumSize['-'];
		} else {
			foreach ($ArSize as $size) {
				$vSumAll = $vSumAll + $ArrSumSize[$size];
				$this->lvFooter = str_replace("<!--$size-->", $this->FormatView($ArrSumSize[$size], 10), $this->lvFooter);
			}
		}
		$this->lvFooter = str_replace("<!--allsize-->", $this->FormatView($vSumAll, 10), $this->lvFooter);

		$strTrH = str_replace("@#01", $this->lvHeader, $lvTrH) . $strTrH . $this->lvFooter;
		return str_replace("@#01", $strTrH, $lvTable);
	}
	function LV_GetValue($vArrRow, $vItem, $vLotID, $vStrItem, &$vorder)
	{
		$lvTr = "<tr class=\"lvlinehtable1\">
			@#01
		</tr>";
		$vData = explode("-", $vLotID, 2);
		if (count($vData) == 1) {
			$CodeID = $vData[0];
		} else {

			$CodeID = $vData[1];
		}
		if (!strpos($vStrItem, "<!--%$CodeID%-->") === false) return $vStrItem;
		if ($vStrItem == "") {
			$vStrItem = $vStrItem . str_replace("@#01", $vArrRow[$CodeID][1], $lvTr);
			$vorder++;
			$vStrItem = str_replace("<!--#stt#-->", $vorder, $vStrItem);
			$vStrItem = str_replace("<!--#item#-->", $this->getvaluelink('lv003', $vItem), $vStrItem);
			$vStrItem = str_replace("<!--#itemname#-->", $this->getvaluelink('lv088', $vItem), $vStrItem);
		} else {
			$vStrItem = $vStrItem . str_replace("@#01", $vArrRow[$CodeID][1], $lvTr);
			$vStrItem = str_replace("<!--#item#-->", '', $vStrItem);
		}
		$vStrItem = str_replace("*item*-", $vItem . "-", $vStrItem);
		return $vStrItem;
	}
	public function LV_LinkField($vFile, $vSelectID)
	{
		return ($this->CreateSelect($this->sqlcondition($vFile, $vSelectID), 0));
	}
	public function LV_LinkFieldExt($vFile, $vSelectID)
	{
		return ($this->CreateSelect($this->sqlconditionext($vFile, $vSelectID), 1));
	}
	private function sqlconditionext($vFile, $vSelectID)
	{
		$vsql = "";
		switch ($vFile) {
			case 'lv003':
				$vsql = "select lv001,lv002,IF(lv001='$vSelectID',1,0) lv003 from  sl_lv0007 where 1=1 and lv015=0 and (lv016 ='$vSelectID'  or lv016='') order by lv001 asc";
				break;
			case 'lv103':
				$vsql = "select lv001,lv003 lv002,IF(lv001='$vSelectID',1,0) lv003 from  hr_lv0002";
				break;
			case 'lv102':
				$vsql = "select lv001,lv002,IF(lv001='$vSelectID',1,0) lv003 from  wh_lv0001";
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
				$vsql = "select lv001,concat(lv001,' ',lv002) lv002,IF(lv001='$vSelectID',1,0) lv003 from  ac_lv0002";
				break;
			case 'lv013':
				$vsql = "select lv001,concat(lv001,' ',lv002) lv002,IF(lv001='$vSelectID',1,0) lv003 from  ac_lv0002";
				break;
			case 'lv017':
				$vsql = "select lv001,lv002,IF(lv001='$vSelectID',1,0) lv003 from  wh_lv0036";
				break;
			case 'lv099':
				$vsql = "select lv001,lv002,IF(lv001='$vSelectID',1,0) lv003 from  ac_lv0003";
				break;
		}
		return $vsql;
	}
	private function sqlcondition($vFile, $vSelectID)
	{
		$vsql = "";
		switch ($vFile) {
			case 'lv003':
				$strwh = $this->Get_WHControler();
				$vsql = "select lv001,lv002,IF(lv001='$vSelectID',1,0) lv003 from  sl_lv0007 where 1=1 and lv015=0 and (lv016 in ($strwh))";
				break;
			case 'lv103':
				$vsql = "select lv001,lv002,IF(lv001='$vSelectID',1,0) lv003 from  hr_lv0002";
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
			case 'lv014':
				if ($this->objlot->lv003 == "")
					$vsql = "select A.lv001,A.lv001 lv002,0 lv003,concat(A.lv006,' - ',A.lv005)  lv004,(select sum(B.lv004) from cr_lv0149 B where B.lv002='" . getInfor($_SESSION['LEVINHUserID'], 2) . "' and B.lv003=A.lv002 and B.lv014=A.lv001 ) QuantityUsed from  wh_lv0020 A where A.lv002='$vSelectID' order by lv004";
				else
					$vsql = "select A.lv001,A.lv001 lv002,0 lv003,concat(A.lv006,' - ',A.lv005)  lv004,(select sum(B.lv004) from cr_lv0149 B where B.lv002='" . getInfor($_SESSION['LEVINHUserID'], 2) . "' and B.lv003=A.lv002 and B.lv014=A.lv001 ) QuantityUsed from  wh_lv0020 A where A.lv002='$vSelectID' and A.lv003='" . $this->objlot->lv003 . "' order by lv004";
				$lvResult = db_query($vsql);
				$vsql = "";
				while ($row = db_fetch_array($lvResult)) {
					$slt = 0;
					$vsltused = (float)$row['QuantityUsed'];
					$slt = $this->objlot->LV_Get_slt_lot($row['lv001'], $vSelectID, $this->objlot->lv003);
					$slt = $slt - $vsltused;
					if ($slt > 0) {
						if ($vsql == "")
							$vsql = $vsql . "select '" . $row['lv001'] . "' lv001,'Slt: " . $this->FormatView($slt, 10) . " - S-C: " . ($row['lv004']) . "' lv002,0 lv003";
						else
							$vsql = $vsql . " UNION select '" . $row['lv001'] . "' lv001,'Slt: " . $this->FormatView($slt, 10) . " - S-C: " . ($row['lv004']) . "' lv002,0 lv003";
					}
				}
				break;
			case 'lv017':
				$vsql = "select lv001,lv002,IF(lv001='$vSelectID',1,0) lv003 from  wh_lv0036";
				break;
			case 'lv099':
				$vsql = "select lv001,lv002,IF(lv001='$vSelectID',1,0) lv003 from  ac_lv0003";
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
			case 'lv005':
				$vsql = "select lv001,lv002,IF(lv001='$vSelectID',1,0) lv003 from  sl_lv0005 where lv001='$vSelectID'";
				break;
			case 'lv089':
				if (trim($this->PBHID) != '' && $this->PBHID != null) {
					$vsql = "
					select A.lv001,REPLACE(A.lv011,'\n','<br/>') lv002 from  cr_lv0276 A inner join sl_lv0013 B on A.lv002=B.lv001 where A.lv003='$vSelectID' and B.lv115='$this->PBHID'
					union
					select lv001,lv010 lv002 from  sl_lv0007 where lv001='$vSelectID'
					";
				} else {
					$vsql = "select lv001,lv010 lv002,IF(lv001='$vSelectID',1,0) lv003 from  sl_lv0007 where lv001='$vSelectID'";
				}
				$lvopt = 0;
				break;
			case 'lv088':
				$vsql = "select lv001,lv002,IF(lv001='$vSelectID',1,0) lv003 from  sl_lv0007 where lv001='$vSelectID'";
				$lvopt = 0;
				break;
			case 'lv007':
				$vsql = "select lv001,lv002,IF(lv001='$vSelectID',1,0) lv003 from  hr_lv0020 where lv001='$vSelectID'";
				break;
			case 'lv009':
				$vsql = "select lv001,lv002,IF(lv001='$vSelectID',1,0) lv003 from  hr_lv0018 where lv001='$vSelectID'";
				break;
			case 'lv099':
				$vsql = "select lv001,lv002,IF(lv001='$vSelectID',1,0) lv003 from  ac_lv0003 where lv001='$vSelectID'";
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
		$lvopt = 0;
		while ($row = db_fetch_array($lvResult)) {
			return ($lvopt == 0) ? $row['lv002'] : (($lvopt == 1) ? $row['lv001'] . "(" . $row['lv002'] . ")" : (($lvopt == 2) ? $row['lv002'] . "(" . $row['lv001'] . ")" : $row['lv001']));
		}
	}
	function MB_ChiTietPhieu($maPhieu)
	{
		$sqlS = "SELECT A.*,BB.lv005 lv102,BB.lv006 lv103,A.lv003 lv088,A.lv003 lv089,BB.lv009 lv109 FROM cr_lv0151 A inner join cr_lv0150 BB on A.lv002=BB.lv001 where A.lv002 ='$maPhieu'";
		return db_query($sqlS);
	}
}
