<?php
/////////////coding jo_lv0016///////////////
class   jo_lv0016 extends lv_controler
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
	public $lv017 = null;
	public $doanhthu = null;
	public $percent1 = null;
	public $percent2 = null;
	public $percent4 = null;
	public $percent3 = null;
	///////////
	public $DefaultFieldList = "lv001,lv002,lv003,lv004,lv005,lv006,lv007,lv008,lv009,lv010,lv011,lv012,lv013,lv014,lv015,lv016";
	////////////////////GetDate
	public $DateDefault = "1900-01-01";
	public $DateCurrent = "1900-01-01";
	public $Count = null;
	public $paging = null;
	public $lang = null;
	protected $objhelp = 'jo_lv0016';
	////////////
	var $ArrOther = array();
	var $ArrPush = array();
	var $ArrFunc = array();
	var $ArrGet = array("lv001" => "2", "lv002" => "3", "lv003" => "4", "lv004" => "5", "lv005" => "6", "lv006" => "7", "lv007" => "8", "lv008" => "9", "lv009" => "10", "lv010" => "11", "lv011" => "12", "lv012" => "13", "lv013" => "14", "lv014" => "15", "lv015" => "16", "lv016" => "17", "lv026" => "27", "lv027" => "28", "lv028" => "29", "lv029" => "30");
	var $ArrView = array("lv001" => "0", "lv002" => "0", "lv003" => "0", "lv004" => "0", "lv005" => "0", "lv006" => "0", "lv007" => "0", "lv008" => "0", "lv009" => "10", "lv010" => "0", "lv011" => "10", "lv012" => "0", "lv013" => "10", "lv014" => "0", "lv015" => "0", "lv015" => "0", "lv026" => "0", "lv027" => "0", "lv028" => "0", "lv029" => "0");

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
	}
	function LV_Load()
	{
		$vsql = "select * from  jo_lv0016";
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
			$this->lv026 = $vrow['lv026'];
			$this->lv027 = $vrow['lv027'];
			$this->lv028 = $vrow['lv028'];
			$this->lv029 = $vrow['lv029'];
			$this->lv337 = $vrow['lv337'];
			$this->lv338 = $vrow['lv338'];
			$this->lv339 = $vrow['lv339'];
			$this->lv340 = $vrow['lv340'];
			$this->lv341 = $vrow['lv341'];
			$this->lv342 = $vrow['lv342'];
			$this->lv343 = $vrow['lv343'];
			$this->lv344 = $vrow['lv344'];
			$this->lv345 = $vrow['lv345'];
			$this->lv346 = $vrow['lv346'];
			$this->lv347 = $vrow['lv347'];
			$this->lv348 = $vrow['lv348'];
			$this->lv349 = $vrow['lv349'];
			$this->lv350 = $vrow['lv350'];
			$this->lv351 = $vrow['lv351'];

			$this->lv361 = $vrow['lv361'];
			$this->lv362 = $vrow['lv362'];
			$this->lv363 = $vrow['lv363'];
			$this->lv400 = $vrow['lv400'];
		}
	}
	function LV_AutoUpdate($month, $year)
	{
		//$vsql="insert into hr_lv0106 select *,$month,$year from  jo_lv0016";
		//$vresult=db_query($vsql);
		return $vresult;
	}
	function getCapDuyet()
	{
		$mojo_lv0016 = new jo_lv0016($_SESSION['ERPSOFV2RRight'], $_SESSION['ERPSOFV2RUserID'], 'Jo0016');
		$data = $mojo_lv0016->LV_LoadID("admin");
		$capDuyet = $mojo_lv0016->lv400;
		return $capDuyet;
	}
	function LV_LoadMonthYear($month, $year)
	{
		$vsql = "select A.*,B.lv002 doanhthu,B.lv003 percent1,B.lv004 percent2,B.lv005 percent3,B.lv006 percent4 from  hr_lv0106 A left join sl_lv0042 B on A.lv006=B.lv001 where A.lv015='$month' and A.lv016='$year'";
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
			$this->lv026 = $vrow['lv026'];
			$this->lv027 = $vrow['lv027'];
			$this->lv028 = $vrow['lv028'];
			$this->lv029 = $vrow['lv029'];
			$this->lv337 = $vrow['lv337'];
			$this->lv338 = $vrow['lv338'];
			$this->lv339 = $vrow['lv339'];
			$this->lv340 = $vrow['lv340'];
			$this->lv341 = $vrow['lv341'];
			$this->lv342 = $vrow['lv342'];
			$this->lv343 = $vrow['lv343'];
			$this->lv344 = $vrow['lv344'];
			$this->lv345 = $vrow['lv345'];
			$this->lv346 = $vrow['lv346'];
			$this->lv347 = $vrow['lv347'];
			$this->lv348 = $vrow['lv348'];
			$this->lv349 = $vrow['lv349'];
			$this->lv350 = $vrow['lv350'];
			$this->lv351 = $vrow['lv351'];

			$this->lv361 = $vrow['lv361'];
			$this->lv362 = $vrow['lv362'];
			$this->lv363 = $vrow['lv363'];
			$this->lv400 = $vrow['lv400'];

			$this->percent1 = $vrow['percent1'];
			$this->percent2 = $vrow['percent2'];
			$this->percent3 = $vrow['percent3'];
			$this->percent4 = $vrow['percent4'];
			$this->doanhthu = $vrow['doanhthu'];
		}
	}
	function LV_LoadID($vlv001)
	{
		$lvsql = "select * from  jo_lv0016 Where lv001='$vlv001'";
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
			$this->lv026 = $vrow['lv026'];
			$this->lv027 = $vrow['lv027'];
			$this->lv028 = $vrow['lv028'];
			$this->lv029 = $vrow['lv029'];
			$this->lv337 = $vrow['lv337'];
			$this->lv338 = $vrow['lv338'];
			$this->lv339 = $vrow['lv339'];
			$this->lv340 = $vrow['lv340'];
			$this->lv341 = $vrow['lv341'];
			$this->lv342 = $vrow['lv342'];
			$this->lv343 = $vrow['lv343'];
			$this->lv344 = $vrow['lv344'];
			$this->lv345 = $vrow['lv345'];
			$this->lv346 = $vrow['lv346'];
			$this->lv347 = $vrow['lv347'];
			$this->lv348 = $vrow['lv348'];
			$this->lv349 = $vrow['lv349'];
			$this->lv350 = $vrow['lv350'];
			$this->lv351 = $vrow['lv351'];
			$this->lv361 = $vrow['lv361'];
			$this->lv362 = $vrow['lv362'];
			$this->lv363 = $vrow['lv363'];
			$this->lv400 = $vrow['lv400'];
		}
	}
	function LV_LoadMonthID($vlv001)
	{
		$lvsql = "select * from  hr_lv0106 Where concat(lv015,lv016)='$vlv001'";
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
			$this->lv015 = $vrow['lv015'];
			$this->lv026 = $vrow['lv026'];
			$this->lv027 = $vrow['lv027'];
			$this->lv028 = $vrow['lv028'];
			$this->lv029 = $vrow['lv029'];
			$this->lv337 = $vrow['lv337'];
			$this->lv338 = $vrow['lv338'];
			$this->lv339 = $vrow['lv339'];
			$this->lv340 = $vrow['lv340'];
			$this->lv341 = $vrow['lv341'];
			$this->lv342 = $vrow['lv342'];
			$this->lv343 = $vrow['lv343'];
			$this->lv344 = $vrow['lv344'];
			$this->lv345 = $vrow['lv345'];
			$this->lv346 = $vrow['lv346'];
			$this->lv347 = $vrow['lv347'];
			$this->lv348 = $vrow['lv348'];
			$this->lv349 = $vrow['lv349'];
			$this->lv350 = $vrow['lv350'];
			$this->lv351 = $vrow['lv351'];
			$this->lv361 = $vrow['lv361'];
			$this->lv362 = $vrow['lv362'];
			$this->lv363 = $vrow['lv363'];
			$this->lv400 = $vrow['lv400'];
		}
	}
	function LV_Insert()
	{
		if ($this->isAdd == 0) return false;
		$this->lv002 = GetServerDate() . " " . GetServerTime();
		$lvsql = "insert into jo_lv0016 (lv001,lv002,lv003,lv004,lv005,lv006,lv007,lv008,lv009,lv010,lv011,lv012,lv013,lv014,lv015,lv016,lv017,lv026,lv027,lv028,lv029,lv337,lv338,lv339, lv400) values('$this->lv001','$this->lv002','$this->lv003','$this->lv004','$this->lv005','$this->lv006','$this->lv007','$this->lv008','$this->lv009','$this->lv010','$this->lv011','$this->lv012','$this->lv013','$this->lv014','$this->lv015','$this->lv016','$this->lv017','$this->lv026','$this->lv027','$this->lv028','$this->lv029','$this->lv337','$this->lv338','$this->lv339','$this->lv400')";
		$vReturn = db_query($lvsql);
		if ($vReturn) {
			$this->InsertLogOperation($this->DateCurrent, 'jo_lv0016.insert', sof_escape_string($lvsql));
		}
		return $vReturn;
	}
	function LV_InsertMonth()
	{

		if ($this->isAdd == 0) return false;
		$this->lv002 = GetServerDate() . " " . GetServerTime();
		$lvsql = "insert into hr_lv0106 (lv001,lv002,lv003,lv004,lv005,lv006,lv007,lv008,lv009,lv010,lv011,lv012,lv013,lv014,lv015,lv016) values('$this->lv001','$this->lv002','$this->lv003','$this->lv004','$this->lv005','$this->lv006','$this->lv007','$this->lv008','$this->lv009','$this->lv010','$this->lv011','$this->lv012','$this->lv013','$this->lv014','$this->lv015','$this->lv016')";
		$vReturn = db_query($lvsql);
		if ($vReturn) {
			$this->InsertLogOperation($this->DateCurrent, 'jo_lv0016.insert', sof_escape_string($lvsql));
		}
		return $vReturn;
	}
	function LV_Update()
	{
		if ($this->isEdit == 0) return false;
		$this->lv002 = GetServerDate() . " " . GetServerTime();
		$lvsql = "Update jo_lv0016 set lv002='$this->lv002',lv003='$this->lv003',lv004='$this->lv004',lv005='$this->lv005',lv006='$this->lv006',lv007='$this->lv007',lv008='$this->lv008',lv009='$this->lv009',lv010='$this->lv010',lv011='$this->lv011',lv012='$this->lv012',lv013='$this->lv013',lv014='$this->lv014',lv015='$this->lv015',lv016='$this->lv016',lv017='$this->lv017',lv026='$this->lv026',lv027='$this->lv027',lv028='$this->lv028',lv029='$this->lv029',lv337='$this->lv337',lv338='$this->lv338',lv339='$this->lv339',lv340='$this->lv340',lv341='$this->lv341',lv342='$this->lv342',lv343='$this->lv343',lv344='$this->lv344',lv345='$this->lv345',lv346='$this->lv346',lv347='$this->lv347',lv348='$this->lv348',lv349='$this->lv349',lv350='$this->lv350',lv351='$this->lv351',lv361='$this->lv361',lv362='$this->lv362',lv363='$this->lv363',lv400='$this->lv400'";
		$vReturn = db_query($lvsql);
		if ($vReturn) {
			$this->InsertLogOperation($this->DateCurrent, 'jo_lv0016.update', sof_escape_string($lvsql));
		}
		return $vReturn;
	}
	function LV_UpdateMonth($vlv001)
	{
		if ($this->isEdit == 0) return false;
		$this->lv002 = GetServerDate() . " " . GetServerTime();
		$lvsql = "Update hr_lv0106 set lv002='$this->lv002',lv003='$this->lv003',lv004='$this->lv004',lv005='$this->lv005',lv006='$this->lv006',lv007='$this->lv007',lv008='$this->lv008',lv009='$this->lv009',lv010='$this->lv010',lv011='$this->lv011',lv012='$this->lv012',lv013='$this->lv013',lv014='$this->lv014',lv015='$this->lv015',lv016='$this->lv016',lv026='$this->lv026',lv027='$this->lv027',lv028='$this->lv028',lv029='$this->lv029' where  concat(lv015,lv016)='$vlv001' AND lv007<=0;";
		$vReturn = db_query($lvsql);
		if ($vReturn) {
			$this->InsertLogOperation($this->DateCurrent, 'jo_lv0016.update', sof_escape_string($lvsql));
		}
		return $vReturn;
	}
	function LV_Delete($lvarr)
	{
		if ($this->isDel == 0) return false;
		$lvsql = "DELETE FROM jo_lv0016  WHERE jo_lv0016.lv007<=0 AND jo_lv0016.lv001 IN ($lvarr) and (select count(*) from ts_lv0009 B where  B.lv002= jo_lv0016.lv001)<=0  ";
		$vReturn = db_query($lvsql);
		if ($vReturn) $this->InsertLogOperation($this->DateCurrent, 'jo_lv0016.delete', sof_escape_string($lvsql));
		return $vReturn;
	}
	function LV_Aproval($lvarr)
	{
		if ($this->isApr == 0) return false;
		$lvsql = "Update jo_lv0016 set lv007=1  WHERE jo_lv0016.lv001 IN ($lvarr)  ";
		$vReturn = db_query($lvsql);
		if ($vReturn) $this->InsertLogOperation($this->DateCurrent, 'jo_lv0016.approval', sof_escape_string($lvsql));
		return $vReturn;
	}
	function LV_Exist()
	{
		$lvsql = "select count(*) num from  jo_lv0016 ";
		$vresult = db_query($lvsql);
		$vrow = db_fetch_array($vresult);
		if ($vrow) {
			if ($vrow['num'] > 0) return true;
			else return false;
		}
		return false;
	}
	function LV_UnAproval($lvarr)
	{
		if ($this->isApr == 0) return false;
		$lvsql = "Update jo_lv0016 set lv007=0  WHERE jo_lv0016.lv001 IN ($lvarr)  ";
		$vReturn = db_query($lvsql);
		if ($vReturn) $this->InsertLogOperation($this->DateCurrent, 'jo_lv0016.unapproval', sof_escape_string($lvsql));
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
		return $strCondi;
	}
	protected function GetConditionRpt()
	{
		$strCondi = "";
		if ($this->lv001 != "") $strCondi = $strCondi . " and lv001 = '$this->lv001'";
		if ($this->lv002 != "") $strCondi = $strCondi . " and lv002  like '%$this->lv002%'";
		if ($this->lv003 != "") $strCondi = $strCondi . " and lv003  like '%$this->lv003%'";
		if ($this->lv004 != "") $strCondi = $strCondi . " and lv004  like '%$this->lv004%'";
		if ($this->lv005 != "") $strCondi = $strCondi . " and lv005  like '%$this->lv005%'";
		if ($this->lv006 != "") $strCondi = $strCondi . " and lv006  like '%$this->lv006%'";
		if ($this->lv007 != "")  $strCondi = $strCondi . " and lv007 like '%$this->lv007%'";
		if ($this->lv008 != "")  $strCondi = $strCondi . " and lv008 like '%$this->lv008%'";
		if ($this->lv009 != "")  $strCondi = $strCondi . " and lv009 like '%$this->lv009%'";
		$strwh = $this->Get_WHControler();
		$strCondi = $strCondi . " and lv002 in ($strwh)";
		return $strCondi;
	}
	protected function GetConditionMini()
	{
		$strCondi = "";
		$strwh = $this->Get_WHControler();
		$strCondi = $strCondi . " and lv002 in ($strwh)";
		return $strCondi;
	}
	////////////////Count///////////////////////////
	function GetCount()
	{
		$sqlC = "SELECT COUNT(*) AS nums FROM jo_lv0016 WHERE 1=1 " . $this->GetCondition();
		$bResultC = db_query($sqlC);
		$arrRowC = db_fetch_array($bResultC);
		return $arrRowC['nums'];
	}
	function LV_GetMoneyTimes($vtime)
	{
		$time = $vtime % 10;
		if (strpos(',' . $this->lv008 . ',', ',' . $time . ',') === false) {
			return $this->lv011;
		} else {
			return $this->lv009;
		}
	}
	function LV_GetBLMoney($vContractID)
	{
		$lvsql = "select sum(PM.lv003) money,sum(PM.lv004) convertmoney from ((select sum(A.lv004*A.lv008) lv003,sum(A.lv004*A.lv008*A.lv010/100) lv004 from ts_lv0009 A inner join jo_lv0016 B on A.lv002=B.lv001  where 1=1 and B.lv001='$vContractID' )) PM ";
		$vresult = db_query($lvsql);
		$vrow = db_fetch_array($vresult);
		if ($vrow['convertmoney'] == 0) {
			if ($vrow['money'] == 0) return "0";
		}
		return $vrow['convertmoney'] + $vrow['money'];
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
		$lvHref = "<a href=\"javascript:FunctRunning1('@01')\" style=\"text-decoration:none\" align='@#05'>@02</a>";
		$lvTdH = "<td width=\"@01\" class=\"lvhtable\">@02</td>";
		$lvTd = "<td  class='@#04' align='@#05'>@02</td>";
		$sqlS = "SELECT * FROM hr_lv0106 WHERE 1=1  " . $this->GetCondition() . " $strSort LIMIT $curRow, $maxRows";
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
				$vTemp = str_replace("@02", str_replace("@02", $this->getvaluelink($lstArr[$i], $this->FormatView($vrow[$lstArr[$i]], (int)$this->ArrView[$lstArr[$i]])), str_replace("@01", $vrow['lv001'], $lvHref)), $this->Align($lvTd, (int)$this->ArrView[$lstArr[$i]]));
				$strL = $strL . $vTemp;
			}
			$strTr = $strTr . str_replace("@#01", $strL, str_replace("@02", $vrow['lv015'] . $vrow['lv016'], str_replace("@03", $vorder, str_replace("@01", $vorder % 2, $lvTr))));
			if ($vrow['lv007'] == 1)		$strTr = str_replace("@#04", "lvlineapproval", $strTr);
			else $strTr = str_replace("@#04", "", $strTr);
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
			window.open('" . $this->Dir . "jo_lv0016/?lang=" . $this->lang . "&childfunc='+value+'&ID=" . base64_encode($this->lv002) . "','','width=800,height=600,left=200,top=100,screenX=0,screenY=100,resizable=yes,status=no,scrollbars=yes,menubar=yes');
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
			<td width=1% align='@#05'>@03</td>
			@#01
		</tr>
		";
		$lvTdH = "<td width=\"@01\" class=\"lvhtable\">@02</td>";
		$lvTd = "<td  class='@#04' align='@#05'>@02</td>";
		$sqlS = "SELECT * FROM jo_lv0016 WHERE 1=1  " . $this->RptCondition . " $strSort LIMIT $curRow, $maxRows";
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


			$strTr = $strTr . str_replace("@#01", $strL, str_replace("@02", $vrow['lv015'] . $vrow['lv016'], str_replace("@03", $vorder, str_replace("@01", $vorder % 2, $lvTr))));
			if ($vrow['lv007'] == 1)		$strTr = str_replace("@#04", "lvlineapproval", $strTr);
			else	$strTr = str_replace("@#04", "", $strTr);
		}
		$strTrH = str_replace("@#01", $strH, $lvTrH);
		return str_replace("@#01", $strTrH . ($strTr ?? ""), $lvTable);
	}
	//////////////////////Buil list////////////////////
	function LV_BuilListReportMini($lvList, $lvFrom, $lvChkAll, $lvChk, $curRow, $maxRows, $paging, $lvOrderList, $lvDateSort)
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
		$lvTd = "<td  class='@#04' align='@#05'>@02</td>";
		$sqlS = "SELECT * FROM jo_lv0016 WHERE 1=1 and lv009='$lvDateSort' " . $this->GetConditionMini() . " $strSort LIMIT $curRow, $maxRows";
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


			$strTr = $strTr . str_replace("@#01", $strL, str_replace("@02", $vrow['lv015'] . $vrow['lv016'], str_replace("@03", $vorder, str_replace("@01", $vorder % 2, $lvTr))));
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
		$lvTd = "<td  class='@#04' align='@#05'>@02</td>";
		$sqlS = "SELECT * FROM jo_lv0016 WHERE 1=1  " . $this->GetConditionRpt() . " $strSort LIMIT $curRow, $maxRows";
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
				if ($lstArr[$i] == "lv010") {
					$vTemp = str_replace("@02", str_replace("@02", $this->getvaluelink($lstArr[$i], $this->FormatView($this->LV_GetBLMoney($vrow['lv001']), (int)$this->ArrView[$lstArr[$i]])), str_replace("@01", $vrow['lv001'], $lvHref)), $this->Align($lvTd, (int)$this->ArrView[$lstArr[$i]]));
				} else
					$vTemp = str_replace("@02", $this->getvaluelink($lstArr[$i], $this->FormatView($vrow[$lstArr[$i]], (int)$this->ArrView[$lstArr[$i]])), $this->Align($lvTd, (int)$this->ArrView[$lstArr[$i]]));
				$strL = $strL . $vTemp;
			}
			$strTr = $strTr . str_replace("@#01", $strL, str_replace("@02", $vrow['lv001'], str_replace("@03", $vorder, str_replace("@01", $vorder % 2, $lvTr))));
			if ($vrow['lv007'] == 1)		$strTr = str_replace("@#04", "", $strTr);
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
				$strwh = $this->Get_WHControler();
				$vsql = "select lv001,lv003 lv002,IF(lv001='$vSelectID',1,0) lv003 from  ts_lv0001 where lv001 in ($strwh)";
				break;
			case 'lv003':
				$vsql = "select lv001,lv002,IF(lv001='$vSelectID',1,0) lv003 from  hr_lv0020";
				break;
				break;
			case 'lv008':
				$vsql = "select lv001,lv002 lv002,IF(lv001='$vSelectID',1,0) lv003 from  sl_lv0007";
				break;
			case 'lv010':
				$vsql = "select lv001,lv002 lv002,IF(lv001='$vSelectID',1,0) lv003 from  sl_lv0007";
				break;
			case 'lv012':
				$vsql = "select lv001,lv002 lv002,IF(lv001='$vSelectID',1,0) lv003 from  sl_lv0007";
				break;
			case 'lv014':
				$vsql = "select lv001,lv002,IF(lv001='$vSelectID',1,0) lv003 from  sl_lv0007";
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
			case 'lv002':
				$vsql = "select lv001,lv003 lv002,IF(lv001='$vSelectID',1,0) lv003 from  ts_lv0001 where lv001='$vSelectID'";
				break;
			case 'lv003':
				$vsql = "select lv001,lv002,IF(lv001='$vSelectID',1,0) lv003 from  hr_lv0020 where lv001='$vSelectID'";
				break;
			case 'lv006':
				$vsql = "select lv001,lv001 lv002,IF(lv001='$vSelectID',1,0) lv003 from  sl_lv0042 where lv001='$vSelectID'";
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
