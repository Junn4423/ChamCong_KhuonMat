<?php
/////////////coding sl_lv0001///////////////
class sl_lv0001 extends lv_controler
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
	public $lv024 = null;
	public $lv025 = null;
	public $SaveTotal = null;

	///////////
	public $DefaultFieldList = "lv001,lv002,lv003,lv017,lv004,lv005,lv006,lv010,lv011,lv012,lv013,lv014,lv015,lv016,lv026,lv027,lv028,lv099,lv100,lv101,lv102,lv022";
	////////////////////GetDate
	public $DateDefault = "1900-01-01";
	public $DateCurrent = "1900-01-01";
	public $Count = null;
	public $paging = null;
	public $lang = null;
	protected $objhelp = 'sl_lv0001';
	////////////
	var $ArrOther = array();
	var $ArrPush = array();
	var $ArrFunc = array();
	var $ArrGet = array("lv001" => "2", "lv002" => "3", "lv003" => "4", "lv004" => "5", "lv005" => "6", "lv006" => "7", "lv007" => "8", "lv008" => "9", "lv009" => "10", "lv010" => "11", "lv011" => "12", "lv012" => "13", "lv013" => "14", "lv014" => "15", "lv015" => "16", "lv016" => "17", "lv017" => "18", "lv018" => "19", "lv019" => "20", "lv020" => "21", "lv021" => "22", "lv022" => "23", "lv023" => "24", "lv024" => "25", "lv025" => "26", "lv026" => "27", "lv027" => "28", "lv028" => "29", "lv099" => "30", "lv100" => "31", "lv101" => "102", "lv102" => "103", "lv022" => "23");
	var $ArrView = array("lv001" => "0", "lv002" => "0", "lv003" => "0", "lv004" => "0", "lv005" => "2", "lv006" => "0", "lv007" => "0", "lv008" => "0", "lv009" => "0", "lv010" => "0", "lv011" => "0", "lv012" => "0", "lv013" => "0", "lv014" => "0", "lv015" => "0", "lv016" => "0", "lv017" => "2", "lv018" => "0", "lv019" => "2", "lv020" => "0", "lv021" => "0", "lv022" => "0", "lv023" => "0", "lv024" => "22", "lv025" => "0", "lv026" => "10", "lv027" => "10", "lv028" => "10", "lv029" => "10", "lv099" => "10", "lv100" => "10", "lv101" => "10", "lv102" => "10", "lv022" => "0");
	public $LE_CODE = "NjlIUS02VFdULTZIS1QtNlFIQQ==";
	function __construct($vCheckAdmin, $vUserID, $vright)
	{
		$this->DateCurrent = GetServerDate() . " " . GetServerTime();
		$this->Set_User($vCheckAdmin, $vUserID, $vright);
		$this->isRel = 1;
		$this->isHelp = 1;
		$this->isConfig = 0;
		$this->isFil = 1;
		$this->isUnApr = 0;
		$this->lang = $_GET['lang'];
		$this->SaveTotal = 0;
	}

	function LV_Load()
	{
		$vsql = "select * from  sl_lv0001";
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
			$this->lv099 = $vrow['lv099'];
			$this->lv100 = $vrow['lv100'];
			$this->lv101 = $vrow['lv101'];
			$this->lv102 = $vrow['lv102'];
		}
	}
	function LV_LoadID($vlv001)
	{
		$lvsql = "select * from  sl_lv0001 Where lv001='$vlv001'";
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
			$this->lv024 = $vrow['lv024'];
			$this->lv025 = $vrow['lv025'];
			$this->lv099 = $vrow['lv099'];
			$this->lv100 = $vrow['lv100'];
			$this->lv101 = $vrow['lv101'];
			$this->lv102 = $vrow['lv102'];
		} else
			$this->lv001 = "";
	}
	function LV_Insert()
	{

		if ($this->isAdd == 0)
			return false;
		$this->lv005 = ($this->lv005 != "") ? recoverdate(($this->lv005), $this->lang) . " " . gettime($this->lv005) : $this->DateDefault;
		$this->lv017 = ($this->lv017 != "") ? recoverdate(($this->lv017), $this->lang) . " " . gettime($this->lv004) : $this->DateDefault;
		$lvsql = "insert into sl_lv0001 (lv001,lv002,lv003,lv004,lv005,lv006,lv007,lv008,lv009,lv010,lv011,lv012,lv013,lv014,lv015,lv016,lv017,lv018,lv019,lv020,lv021,lv022,lv023,lv024,lv025,lv099) values('$this->lv001','$this->lv002','$this->lv003','$this->lv004','$this->lv005','$this->lv006','$this->lv007','$this->lv008','$this->lv009','$this->lv010','$this->lv011','$this->lv012','$this->lv013','$this->lv014','$this->lv015','$this->lv016','$this->lv017','$this->lv018','$this->lv019','$this->lv020','$this->lv021','$this->lv022','$this->lv023','$this->lv024','$this->lv025','$this->lv099')";
		$vReturn = db_query($lvsql);
		if ($vReturn)
			$this->InsertLogOperation($this->DateCurrent, 'sl_lv0001.insert', sof_escape_string($lvsql));
		return $vReturn;
	}
	function LV_Update()
	{
		if ($this->isEdit == 0)
			return false;
		$this->lv005 = ($this->lv005 != "") ? recoverdate(($this->lv005), $this->lang) . " " . gettime($this->lv005) : $this->DateDefault;
		$this->lv017 = ($this->lv017 != "") ? recoverdate(($this->lv017), $this->lang) . " " . gettime($this->lv004) : $this->DateDefault;
		if ($this->lv001_ == "")
			$this->lv001_ = $this->lv001;
		$lvsql = "Update sl_lv0001 set lv001='$this->lv001_',lv002='$this->lv002',lv003='$this->lv003',lv004='$this->lv004',lv005='$this->lv005',lv006='$this->lv006',lv007='$this->lv007',lv008='$this->lv008',lv009='$this->lv009',lv010='$this->lv010',lv011='$this->lv011',lv012='$this->lv012',lv013='$this->lv013',lv014='$this->lv014',lv015='$this->lv015',lv016='$this->lv016',lv017='$this->lv017',lv018='$this->lv018',lv019='$this->lv019',lv020='$this->lv020',lv021='$this->lv021',lv022='$this->lv022',lv023='$this->lv023',lv099='$this->lv099' where  lv001='$this->lv001';";
		$vReturn = db_query($lvsql);
		if ($vReturn) {
			$vsql = "update sl_lv0013 set lv002='$this->lv001_' where lv002='$this->lv001'";
			$vReturn = db_query($vsql);
			$vsql = "update sl_lv0014 set lv015='$this->lv001_' where lv015='$this->lv001'";
			$vReturn = db_query($vsql);
			$this->InsertLogOperation($this->DateCurrent, 'sl_lv0001.update', sof_escape_string($lvsql));
		}
		return $vReturn;
	}
	function LV_UpdateState($vCustomerID, $vState)
	{
		$lvsql = "Update sl_lv0001 set lv023='$vState' where  lv001='$vCustomerID';";
		$vReturn = db_query($lvsql);
		if ($vReturn)
			$this->InsertLogOperation($this->DateCurrent, 'sl_lv0001.update', sof_escape_string($lvsql));
		return $vReturn;
	}
	function LV_Delete($lvarr)
	{
		if ($this->isDel == 0)
			return false;
		$lvsql = "DELETE FROM sl_lv0001  WHERE sl_lv0001.lv001 IN ($lvarr) and (select count(*) from sl_lv0010 B where  B.lv002= sl_lv0001.lv001)<=0  and (select count(*) from sl_lv0013 B where  B.lv002= sl_lv0001.lv001)<=0 ";
		$vReturn = db_query($lvsql);
		if ($vReturn)
			$this->InsertLogOperation($this->DateCurrent, 'sl_lv0001.delete', sof_escape_string($lvsql));
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
		if ($this->lv001 != "")
			$strCondi = $strCondi . " and lv001  like '%$this->lv001%'";
		if ($this->lv002 != "")
			$strCondi = $strCondi . " and lv002  like '%$this->lv002%'";
		if ($this->lv003 != "")
			$strCondi = $strCondi . " and lv003  like '%$this->lv003%'";
		if ($this->lv004 != "")
			$strCondi = $strCondi . " and lv004  like '%$this->lv004%'";
		if ($this->lv005 != "")
			$strCondi = $strCondi . " and lv005  like '%$this->lv005%'";
		if ($this->lv006 != "")
			$strCondi = $strCondi . " and lv006  like '%$this->lv006%'";
		if ($this->lv007 != "")
			$strCondi = $strCondi . " and lv007 like '%$this->lv007%'";
		if ($this->lv008 != "")
			$strCondi = $strCondi . " and lv008 like '%$this->lv008%'";
		if ($this->lv009 != "")
			$strCondi = $strCondi . " and lv009 like '%$this->lv009%'";
		if ($this->lv010 != "")
			$strCondi = $strCondi . " and lv010 like '%$this->lv010%'";
		if ($this->lv011 != "")
			$strCondi = $strCondi . " and lv011 like '%$this->lv011%'";
		if ($this->lv012 != "")
			$strCondi = $strCondi . " and lv012 like '%$this->lv012%'";
		if ($this->lv013 != "")
			$strCondi = $strCondi . " and lv013 like '%$this->lv013%'";
		if ($this->lv014 != "")
			$strCondi = $strCondi . " and lv014 like '%$this->lv014%'";
		if ($this->lv015 != "")
			$strCondi = $strCondi . " and lv015 like '%$this->lv015%'";
		if ($this->lv016 != "")
			$strCondi = $strCondi . " and lv016 like '%$this->lv016%'";
		if ($this->lv017 != "")
			$strCondi = $strCondi . " and lv017 like '%$this->lv017%'";
		if ($this->lv018 != "")
			$strCondi = $strCondi . " and lv018 like '%$this->lv018%'";
		if ($this->lv019 != "")
			$strCondi = $strCondi . " and lv019 like '%$this->lv019%'";
		if ($this->lv020 != "")
			$strCondi = $strCondi . " and lv020 like '%$this->lv020%'";
		if ($this->lv021 != "")
			$strCondi = $strCondi . " and lv021 like '%$this->lv021%'";
		if ($this->lv022 != "")
			$strCondi = $strCondi . " and lv022 like '%$this->lv022%'";
		if ($this->lv023 != "")
			$strCondi = $strCondi . " and lv023 like '%$this->lv023%'";
		if ($this->lv024 != "")
			$strCondi = $strCondi . " and lv024 like '%$this->lv024%'";
		if ($this->lv099 != "")
			$strCondi = $strCondi . " and lv099 like '$this->lv099'";
		if ($this->lv025 != "" || $this->lv129 != "") {
			$vsql = "select lv001 from hr_lv0020 where lv029 in ('" . str_replace(",", "','", $this->lv129) . "')";
			$vstr = $this->GetMultiValue($vsql);
			$strCondi = $strCondi . " and lv025 in ('$this->lv025',$vstr)";
		}
		return $strCondi;
	}
	function LV_ListPopupYear($vCustomerID, $vYearTo)
	{
		$i = 2014;
		$vStrReturn = '
		<div  style="position:relative"><img src="../images/icon/income.png" onclick="document.getElementById(\'logto\').style.display=\'block\'">
		<div id="logto" style="display:none;position:absolute;z-index:9999;background:#FFF;" >
		<img src="../images/icon/close.png" style="position:absolute;right:0px;top:0px" onclick="document.getElementById(\'logto\').style.display=\'none\'">
		
		<table border=1 cellspacing=0 cellpadding=0 class="lvtable" style="width:150px!important">
		<tr class="lvhtable"><td  class="lvhtable">Năm</td><td  class="lvhtable">Tổng tiền nạp</td><td  class="lvhtable">Tổng tiền mua</td><td  class="lvhtable">Tổng tiền còn lại</td>
		';
		$vSum = 0;
		for (; $i <= $vYearTo; $i++) {
			$vTotalContract = $this->LV_GetContractMoney($vCustomerID, $i);
			$vTotalMoneyPT = $this->LV_GetPTMoney($vCustomerID, $i) - $this->LV_GetPCMoney($vCustomerID, $i);
			$vSum1 = $vSum1 + $vTotalMoneyPT;
			$vSum2 = $vSum2 + $vTotalContract;
			$vStrReturn = $vStrReturn . "<tr class=\"lvlinehtable" . ($i % 2) . "\"><td>" . $i . "</td><td align=right>" . $this->FormatView($vTotalMoneyPT, 10) . "</td><td align=right>" . $this->FormatView($vTotalContract, 10) . "</td><td align=right>" . $this->FormatView($vTotalMoneyPT - $vTotalContract, 10) . "</td></tr>";
		}
		$vStrReturn = $vStrReturn . "
		<tr class=\"lvlinehtable" . ($i % 2) . "\"><td >Tổng:</td><td align=right> " . $this->FormatView($vSum1, 10) . "</td><td align=right> " . $this->FormatView($vSum2, 10) . "</td><td align=right> " . $this->FormatView($vSum1 - $vSum2, 10) . "</td></tr>";
		$i++;
		$vStrReturn = $vStrReturn . "
		<tr class=\"lvlinehtable" . ($i % 2) . "\"><td colspan='4' >Ma KH: " . $vCustomerID . "</td></tr>
		</table>
		</div>
		</div>
		";
		$this->SaveTotal = $vSum1 - $vSum2;
		return $vStrReturn;
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
	////////////////Count///////////////////////////
	function GetCount()
	{
		$sqlC = "SELECT COUNT(*) AS nums FROM sl_lv0001 WHERE 1=1 " . $this->GetCondition();
		$bResultC = db_query($sqlC);
		$arrRowC = db_fetch_array($bResultC);
		return $arrRowC['nums'];
	}
	function LV_GetContractMoney($vCusID, $vYear = '')
	{
		if ($vYear != "")
			$vCondition = " and Year(B.lv004)='$vYear'";
		$lvsql = "
			select * from (
			select sum(PM1.money-PM1.convertmoney+PM1.convertmoney*PM1.VAT/100-PM1.money*PM1.CKTM/100) TongTien from (select PM.ContractID,PM.VAT,PM.CKTM,sum(PM.lv003) money,sum(PM.lv004) convertmoney,sum(PM.lv005) discountmoney from ((select sum(A.lv004*A.lv006) lv003,sum(A.lv004*A.lv006*A.lv008/100) lv004,sum(A.lv004*A.lv006*A.lv011/100) lv005,B.lv022 CKTM,B.lv006 VAT,A.lv002 ContractID from sl_lv0014 A inner join sl_lv0013 B on A.lv002=B.lv001  where 1=1 AND B.lv015=0 AND B.lv011>=2 and B.lv002='$vCusID'  $vCondition  )) PM ) PM1
			union
			select -sum(PM1.money-PM1.convertmoney+PM1.convertmoney*PM1.VAT/100-PM1.money*PM1.CKTM/100) TongTien from (select PM.ContractID,PM.VAT,PM.CKTM,sum(PM.lv003) money,sum(PM.lv004) convertmoney,sum(PM.lv005) discountmoney from ((select sum(A.lv004*A.lv006) lv003,sum(A.lv004*A.lv006*A.lv008/100) lv004,sum(A.lv004*A.lv006*A.lv011/100) lv005,B.lv022 CKTM,B.lv006 VAT,A.lv002 ContractID from sl_lv0014 A inner join sl_lv0013 B on A.lv002=B.lv001  where 1=1 AND B.lv015=1 AND B.lv011>=2 and B.lv002='$vCusID'  $vCondition  )) PM ) PM1) MP2
		";
		$vresult = db_query($lvsql);
		$vrow = db_fetch_array($vresult);
		if ($vrow['TongTien'] == 0) {
			return "0";
		}
		return $vrow['TongTien'];
	}
	function LV_GetContractMoneyArr()
	{
		$vArrMoney = array();
		if ($vYear != "")
			$vCondition = " and Year(B.lv004)='$vYear'";
		$lvsql = "
			select * from (
			select lv002,sum(PM1.money-PM1.convertmoney+PM1.convertmoney*PM1.VAT/100-PM1.money*PM1.CKTM/100) TongTien from (select lv002,PM.ContractID,PM.VAT,PM.CKTM,sum(PM.lv003) money,sum(PM.lv004) convertmoney,sum(PM.lv005) discountmoney from ((select B.lv002,sum(A.lv004*A.lv006) lv003,sum(A.lv004*A.lv006*A.lv008/100) lv004,sum(A.lv004*A.lv006*A.lv011/100) lv005,B.lv022 CKTM,B.lv006 VAT,A.lv002 ContractID from sl_lv0014 A inner join sl_lv0013 B on A.lv002=B.lv001  where 1=1 AND B.lv015=0 AND B.lv011>=2   $vCondition  group by B.lv002)) PM group by lv002) PM1 group by lv002
			union
			select lv002,-sum(PM1.money-PM1.convertmoney+PM1.convertmoney*PM1.VAT/100-PM1.money*PM1.CKTM/100) TongTien from (select lv002,PM.ContractID,PM.VAT,PM.CKTM,sum(PM.lv003) money,sum(PM.lv004) convertmoney,sum(PM.lv005) discountmoney from ((select B.lv002,sum(A.lv004*A.lv006) lv003,sum(A.lv004*A.lv006*A.lv008/100) lv004,sum(A.lv004*A.lv006*A.lv011/100) lv005,B.lv022 CKTM,B.lv006 VAT,A.lv002 ContractID from sl_lv0014 A inner join sl_lv0013 B on A.lv002=B.lv001  where 1=1 AND B.lv015=1 AND B.lv011>=2   $vCondition  group by B.lv002)) PM group by lv002) PM1 group by lv002) MP2 group by lv002
		";
		$vresult = db_query($lvsql);
		while ($vrow = db_fetch_array($vresult)) {
			$vArrMoney[$vrow['lv002']] = $vrow['TongTien'];
		}
		return $vArrMoney;
	}
	function LV_GetInvoiceParent($vCusID, $vtype, $vYear = '')
	{
		$vResult = '';
		if ($vYear != "")
			$vCondition = " and Year(B.lv009)='$vYear'";
		$lvsql = "select B.lv001 from ac_lv0004 B where B.lv003='CUS' and B.lv004='$vCusID' and B.lv002='$vtype' and B.lv017=0 $vCondition";
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
	function LV_GetPointVoucher($vCusID, $vDateStart, $vDateEnd)
	{
		//GetMoney
		if (trim($vDateStart) != "") {
			$vCondition = $vCondition . " AND A.lv005>='$vDateStart 00:00:00' ";
		}
		if (trim($vDateEnd) != "") {
			$vCondition = $vCondition . " AND A.lv005<='$vDateEnd 23:59:59' ";
		}
		if ($vCusID == '' || $vCusID == NULL)
			return 0;
		$lvsql = "select if(ISNULL(sum(lv004)),0,sum(lv004)) SumPoint from sl_lv0115 A  WHERE A.lv002='$vCusID' and lv015>=1 $vCondition";
		$vReturnArr = array();
		$lvResult = db_query($lvsql);
		$row = db_fetch_array($lvResult);
		return $row['SumPoint'];
	}
	function LV_GetPointVoucherArr($vDateStart, $vDateEnd)
	{
		//GetMoney
		$vArrPoint = array();
		if (trim($vDateStart) != "") {
			$vCondition = $vCondition . " AND A.lv005>='$vDateStart 00:00:00' ";
		}
		if (trim($vDateEnd) != "") {
			$vCondition = $vCondition . " AND A.lv005<='$vDateEnd 23:59:59' ";
		}
		$lvsql = "select A.lv002,if(ISNULL(sum(lv004)),0,sum(lv004)) SumPoint from sl_lv0115 A  WHERE lv015>=1 $vCondition group by A.lv002";
		$vReturnArr = array();
		$lvResult = db_query($lvsql);
		while ($row = db_fetch_array($lvResult)) {
			$vArrPoint[$row['lv002']] = $row['SumPoint'];
		}
		return $vArrPoint;
	}
	function LV_GetPCMoney($vCusID, $vYear = '')
	{
		//GetMoney
		if ($vCusID == '' || $vCusID == NULL)
			return 0;
		$vListParent = $this->LV_GetInvoiceParent($vCusID, 1, $vYear);
		$lvsql = "select if(ISNULL(sum(lv004)),0,sum(lv004)) SumMoney from ac_lv0005 A  WHERE A.lv002 in (" . $vListParent . ")";
		$vReturnArr = array();
		$lvResult = db_query($lvsql);
		$row = db_fetch_array($lvResult);
		return $row['SumMoney'];
	}
	function LV_GetPTMoney($vCusID, $vYear = '')
	{
		//GetMoney
		if ($vCusID == '' || $vCusID == NULL)
			return 0;
		$vListParent = $this->LV_GetInvoiceParent($vCusID, 0, $vYear);
		$lvsql = "select if(ISNULL(sum(lv004)),0,sum(lv004)) SumMoney from ac_lv0005 A  WHERE  A.lv002 in (" . $vListParent . ") ";
		$vReturnArr = array();
		$lvResult = db_query($lvsql);
		$row = db_fetch_array($lvResult);
		return $row['SumMoney'];
	}
	//////////////////////Buil list////////////////////
	function LV_BuilList($lvList, $lvFrom, $lvChkAll, $lvChk, $curRow, $maxRows, $paging, $lvOrderList, $lvSortNum)
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
		<div id=\"func_id\" style='position:relative;background:#f2f2f2'><div style=\"float:left\">" . $this->TabFunction($lvFrom, $lvList, $maxRows) . "</div><div style=\"float:right\">" . $this->ListFieldSave($lvFrom, $lvList, $maxRows, $lvOrderList, $lvSortNum) . "</div><div style='float:right'>&nbsp;&nbsp;&nbsp;</div><div style='float:right'>" . $this->ListFieldExport($lvFrom, $lvList, $maxRows) . "</div></div><div style='height:35px'></div><table  align=\"center\" class=\"lvtable\"><!--<tr ><td colspan=\"" . (2 + count($lstArr)) . "\" class=\"lvTTable\">" . $this->ArrPush[0] . "</td></tr>-->
		@#01
		@#02
		<tr ><td colspan=\"" . (count($lstArr) + 2) . "\">$paging</td></tr>
		<tr class=\"cssbold_tab\"><td colspan=\"" . (count($lstArr) + 2) . "\">" . $this->TabFunction($lvFrom, $lvList, $maxRows) . "</td></tr>
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
		$lvHref = "<a href=\"javascript:FunctRunning1('@01')\" class=@#04 style=\"text-decoration:none\">@02</a>";
		$lvTdH = "<td width=\"@01\" class=\"lvhtable\">@02</td>";
		$lvTdF = "<td align=\"right\"><strong>@01</strong></td>";
		$strF = "<tr><td colspan=\"2\">&nbsp;</td>";
		$lvTd = "<td align=@#05>@02</td>";
		$sqlS = "SELECT * FROM sl_lv0001 WHERE 1=1  " . $this->GetCondition() . " $strSort LIMIT $curRow, $maxRows";
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
		$vTotalDauKy = 0;
		$vTotalBH = 0;
		$vTotalPT = 0;
		$vArrCus = $this->mosl_lv0013->LV_GetContractMoneyCustomerDateArr($this->lvsl_lv0070->lv015, $this->lvsl_lv0070->lv016);
		$vArrCusDetail = $this->mosl_lv0013->LV_GetContractMoneyCustomerDetailDateArr($this->lvsl_lv0070->lv015, $this->lvsl_lv0070->lv016);
		$vArrPoint = $this->LV_GetPointVoucherArr($this->lvsl_lv0070->lv015, $this->lvsl_lv0070->lv016);
		$vArrMoney = $this->LV_GetContractMoneyArr();
		while ($vrow = db_fetch_array($bResult)) {
			$strL = "";
			$vorder++;
			$vFlagMoney = true;
			$vFlagContract = true;
			$vTotalMoney = 0;
			$vTotalDauKy = $vTotalDauKy + $vrow['lv099'];
			$vTotalContract = $vArrMoney[$vrow['lv001']];
			$vTotalBH = $vTotalBH + $vTotalContract;
			$vTotalMoney = $this->LV_GetPTMoney($vrow['lv001']) - $this->LV_GetPCMoney($vrow['lv001']);
			$vTotalPT = $vTotalPT + $vTotalMoney;
			$vSum = $vArrCus[$vrow['lv001']] + $vArrCusDetail[$vrow['lv001']];
			$vTotalPoint = round($vSum / $this->lvsl_lv0070->lv009, 0);
			for ($i = 0; $i < count($lstArr); $i++) {
				switch ($lstArr[$i]) {

					case "lv100":
						db_query("update sl_lv0001 set lv100='$vTotalPoint' where lv001='" . $vrow['lv001'] . "'");
						$vTotalPoint = $this->mosl_lv0013->FormatView($vTotalPoint, 10);
						$vTemp = str_replace("@02", str_replace("@02", $this->getvaluelink($lstArr[$i], $this->FormatView($vTotalPoint, (int) $this->ArrView[$lstArr[$i]])), str_replace("@01", $vrow['lv001'], $lvHref)), $this->Align($lvTd, (int) $this->ArrView[$lstArr[$i]]));
						break;
					case 'lv101':
						$vPointVoucher = $vArrPoint[$vrow['lv001']];
						db_query("update sl_lv0001 set lv101='$vPointVoucher',lv102=(lv100-$vPointVoucher) where lv001='" . $vrow['lv001'] . "'");
						$vTemp = str_replace("@02", str_replace("@02", $this->getvaluelink($lstArr[$i], $this->FormatView($vPointVoucher, (int) $this->ArrView[$lstArr[$i]])), str_replace("@01", $vrow['lv001'], $lvHref)), $this->Align($lvTd, (int) $this->ArrView[$lstArr[$i]]));
						break;
					case "lv026":
						if ($vFlagContract) {
							$vFlagContract = false;
						}
						$vTemp = str_replace("@02", str_replace("@02", $this->getvaluelink($lstArr[$i], $this->FormatView($vTotalContract, (int) $this->ArrView[$lstArr[$i]])), str_replace("@01", $vrow['lv001'], $lvHref)), $this->Align($lvTd, (int) $this->ArrView[$lstArr[$i]]));
						break;
					case "lv027":
						if ($vFlagMoney) {

							$vFlagMoney = false;
						}
						$vTemp = str_replace("@02", str_replace("@02", $this->getvaluelink($lstArr[$i], $this->FormatView($vTotalMoney, (int) $this->ArrView[$lstArr[$i]])), str_replace("@01", $vrow['lv001'], $lvHref)), $this->Align($lvTd, (int) $this->ArrView[$lstArr[$i]]));
						break;
					case "lv028":
						if ($vFlagMoney) {
							//$vTotalMoney=$this->LV_GetPTMoney($vrow['lv001'])-$this->LV_GetPCMoney($vrow['lv001']);
							$vFlagMoney = false;
						}
						if ($vFlagContract) {
							$vFlagContract = false;
						}
						$vTemp = str_replace("@02", str_replace("@02", $this->getvaluelink($lstArr[$i], $this->FormatView(-$vTotalMoney + $vrow['lv099'] + $vTotalContract, (int) $this->ArrView[$lstArr[$i]])), str_replace("@01", $vrow['lv001'], $lvHref)), $this->Align($lvTd, (int) $this->ArrView[$lstArr[$i]]));
						break;
					default:
						$vTemp = str_replace("@02", str_replace("@02", $this->getvaluelink($lstArr[$i], $this->FormatView($vrow[$lstArr[$i]], (int) $this->ArrView[$lstArr[$i]])), str_replace("@01", $vrow['lv001'], $lvHref)), $this->Align($lvTd, (int) $this->ArrView[$lstArr[$i]]));
						break;
				}
				$strL = $strL . $vTemp;
			}
			$strTr = $strTr . str_replace("@#01", $strL, str_replace("@02", $vrow['lv001'], str_replace("@03", $vorder, str_replace("@01", $vorder % 2, $lvTr))));
		}
		$strF = $strF . "</tr>";
		$strF = str_replace("<!--lv099-->", $this->FormatView($vTotalDauKy, 10), $strF);
		$strF = str_replace("<!--lv026-->", $this->FormatView($vTotalBH, 10), $strF);
		$strF = str_replace("<!--lv027-->", $this->FormatView($vTotalPT, 10), $strF);
		$strF = str_replace("<!--lv028-->", $this->FormatView($vTotalBH - $vTotalPT + $vTotalDauKy, 10), $strF);
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
			window.open('sl_lv0001/?lang=" . $this->lang . "&func='+value,'','width=800,height=600,left=200,top=100,screenX=0,screenY=100,resizable=yes,status=no,scrollbars=yes,menubar=yes');
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
						<li class=\"menusubT\"><img src=\"$this->Dir../images/lvicon/config.png\" border=\"0\" class=\"lv_funcshowimg\"/><span class=\"lv_funcshowtext\">" . $this->ArrFunc[11] . "</span>
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
		$strTemp = str_replace("@01", $lvNumPage, $lvScript);
		$strGetScript = $strGetScript . $strTemp;
		$strTemp = str_replace("@01", $lvSortPage, $lvScript);
		$strGetScript = $strGetScript . $strTemp;
		$strTemp = str_replace("@01", $lvButton, $lvScript);
		$strGetScript = $strGetScript . $strTemp;
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
		$sqlS = "SELECT * FROM sl_lv0001 WHERE 1=1  " . $this->RptCondition . " $strSort LIMIT $curRow, $maxRows";
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
				if ($lstArr[$i] == "lv026") {

					$vTemp = str_replace("@02", $this->getvaluelink($lstArr[$i], $this->FormatView($this->LV_GetContractMoney($vrow['lv001']), (int) $this->ArrView[$lstArr[$i]])), $this->Align($lvTd, (int) $this->ArrView[$lstArr[$i]]));
				} else
					$vTemp = str_replace("@02", $this->getvaluelink($lstArr[$i], $this->FormatView($vrow[$lstArr[$i]], (int) $this->ArrView[$lstArr[$i]])), $this->Align($lvTd, (int) $this->ArrView[$lstArr[$i]]));
				$strL = $strL . $vTemp;
			}


			$strTr = $strTr . str_replace("@#01", $strL, str_replace("@02", $vrow['lv001'], str_replace("@03", $vorder, str_replace("@01", $vorder % 2, $lvTr))));
		}
		$strTrH = str_replace("@#01", $strH, $lvTrH);
		return str_replace("@#01", $strTrH . $strTr, $lvTable);
	}

	public function LV_LinkField($vFile, $vSelectID)
	{
		return ($this->CreateSelect($this->sqlcondition($vFile, $vSelectID), 2));
	}
	private function sqlcondition($vFile, $vSelectID)
	{
		$vsql = "";
		switch ($vFile) {
			case 'lv007':
				$vsql = "select lv001,lv002,IF(lv001='$vSelectID',1,0) lv003 from  hr_lv0023";
				break;
			case 'lv008':
				$vsql = "select lv001,lv002,IF(lv001='$vSelectID',1,0) lv003 from  hr_lv0014";
				break;
			case 'lv018':
				$vsql = "select lv001,lv002,IF(lv001='$vSelectID',1,0) lv003 from  hr_lv0014";
				break;
			case 'lv022':
				$vsql = "select lv001,lv002,IF(lv001='$vSelectID',1,0) lv003 from  sl_lv0034";
				break;
			case 'lv023':
				$vsql = "select lv001,lv002,IF(lv001='$vSelectID',1,0) lv003 from  sl_lv0035";
				break;
		}
		return $vsql;
	}
	private function getvaluelink($vFile, $vSelectID)
	{
		switch ($vFile) {
			case 'lv007':
				$vsql = "select lv001,lv002,IF(lv001='$vSelectID',1,0) lv003 from  hr_lv0023 where lv001='$vSelectID'";
				break;
			case 'lv008':
				$vsql = "select lv001,lv002,IF(lv001='$vSelectID',1,0) lv003 from  hr_lv0014 where lv001='$vSelectID'";
				break;
			case 'lv018':
				$vsql = "select lv001,lv002,IF(lv001='$vSelectID',1,0) lv003 from  hr_lv0014 where lv001='$vSelectID'";
				break;
			case 'lv022':
				$vsql = "select lv001,lv002,IF(lv001='$vSelectID',1,0) lv003 from  sl_lv0034 where lv001='$vSelectID'";
				break;
			case 'lv023':
				$vsql = "select lv001,lv002,IF(lv001='$vSelectID',1,0) lv003 from  sl_lv0035 where lv001='$vSelectID'";
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

	function getKhachHang()
	{
		$vArrRe = [];
		$vsql = "SELECT * from  sl_lv0001";
		$vresult = db_query($vsql);
		while ($vrow = mysqli_fetch_assoc($vresult)) {
			$vArrRe[] = $vrow;
		}
		return $vArrRe;
	}
}