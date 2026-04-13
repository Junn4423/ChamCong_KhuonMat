<?php
/////////////coding sl_lv0013///////////////
class   sl_lv0013 extends lv_controler
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
	///////////
	public $DefaultFieldList = "lv199,lv200,lv016,lv002,lv003,lv350,lv900,lv113,lv101,lv069,lv115,lv014,lv224,lv112,lv212,lv214,lv004,lv026,lv018,lv013,lv015,lv027,lv114,lv353"; //,lv354";
	//public $DefaultFieldList="lv199,lv113,lv114,lv115,lv014,lv112,lv212,lv214,lv224,lv004,lv005,lv024,lv011,lv003,lv012,lv101,lv017,lv008,lv102,lv013,lv015,lv002,lv030,lv028,lv009,lv104,lv006,lv022,lv025,lv106,lv029,lv016,lv020,lv018,lv108,lv109,lv110,lv900";
	//public $DefaultFieldList="lv199,lv115,lv113,lv101,lv004,lv005,lv024,lv003,lv014,lv112,lv009,lv013,lv017,lv016,lv020,lv018,lv019,lv029,lv023,lv105,lv007,lv030,lv108,lv109,lv110,lv900,lv011,lv114";
	////////////////////GetDate
	public $DateDefault = "1900-01-01";
	public $DateCurrent = "1900-01-01";
	public $Count = null;
	public $paging = null;
	public $sumTang = 0;
	public $lang = null;
	public $obj_conf = null;
	protected $objhelp = 'sl_lv0013';
	public $obj_child = null;
	public $itemlist = null;
	public $mosl_lv0014 = null;
	public $lvsl_lv0001 = null;
	public $lvsl_lv0058 = null;
	////////////
	var $ArrOther = array();
	var $ArrPush = array();
	var $ArrFunc = array();
	var $ArrGet = array("lv001" => "2", "lv002" => "3", "lv003" => "4", "lv004" => "5", "lv005" => "6", "lv006" => "7", "lv007" => "8", "lv008" => "9", "lv009" => "10", "lv010" => "11", "lv810" => "811", "lv011" => "12", "lv012" => "13", "lv013" => "14", "lv014" => "15", "lv015" => "16", "lv016" => "17", "lv017" => "18", "lv018" => "19", "lv019" => "20", "lv020" => "21", "lv021" => "22", "lv022" => "23", "lv023" => "24", "lv024" => "25", "lv025" => "26", "lv026" => "27", "lv027" => "28", "lv028" => "29", "lv029" => "30", "lv030" => "31", "lv069" => "70", "lv101" => "102", "lv102" => "103", "lv103" => "104", "lv104" => "105", "lv105" => "106", "lv106" => "107", "lv107" => "108", "lv108" => "109", "lv109" => "110", "lv110" => "111", "lv111" => "112", "lv112" => "113", "lv113" => "114", "lv114" => "115", "lv199" => "200", "lv200" => "201", "lv201" => "202", "lv202" => "203", "lv203" => "204", "lv204" => "205", "lv205" => "206", "lv206" => "207", "lv207" => "208", "lv210" => "211", "lv115" => "116", "lv116" => "117", "lv900" => "901", "lv212" => "213", "lv214" => "215", "lv224" => "225", "lv350" => "351", "lv353" => "354", "lv354" => "355");
	var $ArrView = array("lv001" => "0", "lv002" => "0", "lv003" => "0", "lv004" => "2", "lv005" => "2", "lv006" => "0", "lv007" => "0", "lv008" => "0", "lv009" => "0", "lv010" => "0", "lv810" => "0", "lv011" => "0", "lv012" => "0", "lv013" => "0", "lv014" => "0", "lv015" => "0", "lv016" => "0", "lv017" => "0", "lv018" => "0", "lv019" => "22", "lv020" => "0", "lv021" => "22", "lv022" => "10", "lv023" => "0", "lv024" => "10", "lv025" => "10", "lv026" => "0", "lv027" => "10", "lv028" => "0", "lv029" => "0", "lv030" => "0", "lv101" => "0", "lv102" => "0", "lv103" => "0", "lv104" => "0", "lv105" => "22", "lv106" => "2", "lv107" => "2", "lv108" => "10", "lv109" => "10", "lv110" => "10", "lv111" => "2", "lv112" => "0", "lv113" => "0", "lv114" => "10", "lv199" => "0", "lv200" => "0", "lv201" => "0", "lv202" => "0", "lv203" => "0", "lv204" => "0", "lv205" => "0", "lv206" => "0", "lv207" => "0", "lv210" => "10", "lv116" => "0", "lv900" => "1", "lv350" => "0", "lv353" => "2", "lv354" => "2");
	var $ArrViewImport = array("lv001" => "0", "lv002" => "0", "lv003" => "0", "lv004" => "2", "lv005" => "2", "lv006" => "0", "lv007" => "0", "lv008" => "0", "lv009" => "0", "lv010" => "0", "lv810" => "0", "lv011" => "0", "lv012" => "0", "lv013" => "0", "lv014" => "0", "lv015" => "0", "lv016" => "0", "lv017" => "0", "lv018" => "0", "lv019" => "22", "lv020" => "0", "lv021" => "22", "lv022" => "10", "lv023" => "0", "lv024" => "10", "lv025" => "10", "lv026" => "0", "lv027" => "10", "lv028" => "0", "lv029" => "0", "lv030" => "0", "lv101" => "0", "lv102" => "0", "lv103" => "0", "lv104" => "0", "lv105" => "22", "lv106" => "2", "lv107" => "2", "lv108" => "10", "lv109" => "10", "lv110" => "10", "lv111" => "2", "lv112" => "0", "lv113" => "0", "lv114" => "10", "lv199" => "0", "lv200" => "0", "lv201" => "0", "lv202" => "0", "lv203" => "0", "lv204" => "0", "lv205" => "0", "lv206" => "0", "lv207" => "0", "lv210" => "10", "lv116" => "0", "lv900" => "1", "lv350" => "0", "lv407" => "2", "lv507" => "2", "lv607" => "2", "lv707" => "2", "lv313" => "2", "lv314" => "2", "lv353" => "2", "lv354" => "2");
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
		$vlv910 = $this->Get_User($_SESSION['ERPSOFV2RUserID'], 'lv910');
		if ($vlv910 == 0) {
			$this->ArrView['lv900'] = '3';
			$this->ArrView['lv901'] = '3';
			$this->ArrView['lv902'] = '3';
		}
	}
	function LV_GetBangRun($lvsl_lv0014)
	{
		$vsql = "select A.*,(select lv001 from sl_lv0013 BB where BB.lv007=A.lv001 and (BB.lv011=0 || BB.lv011=1 ) limit 0,1) active  from sl_lv0009 A  order by lv005 asc";
		$lvResult = db_query($vsql);
		while ($row = db_fetch_array($lvResult)) {
			$vorder++;
			$active = false;
			$checks = false;

			$vHDGop = $this->LV_CheckGopBan($row['lv001']);
			if ($vHDGop != NULL) {
				$checks = true;
			}
			if ($row['active'] != "" && $row['active'] != NULL) {
				$active = true;
				$vCurHD = $this->LV_GetTimeInvoice($row['active']);
				$this->LV_GetDetailRun($lvsl_lv0014, $vCurHD, $row['active']);
			}
		}
		return $vLeft;
	}
	function LV_GetTimeInvoice($vContractID)
	{
		$lvsql = "select lv001,lv006 VAT,IF(lv011=0,lv004,lv005) lv004,TIME_TO_SEC(substr(IF(lv011=0,lv004,lv005),12,8)) timeview,TIME_TO_SEC(TIMEDIFF('24:00:00',substr(IF(lv011=0,lv004,lv005),12,8))) timeagain,TIME_TO_SEC('24:00:00') h24,DATEDIFF(CURRENT_DATE(),substr(IF(lv011=0,lv004,lv005),1,12)) days,TIME_TO_SEC(CURRENT_TIME()) curtime,lv011 state,lv002 CMND,lv003 CusName,lv009 Address,lv013 Note,lv022 CKTM from  sl_lv0013 Where lv001='$vContractID'";
		$vresult = db_query($lvsql);
		return db_fetch_array($vresult);
	}
	function LV_CheckGopBan($vBang)
	{
		$lvsql = "select BB.lv001,concat(CC.lv002,'(',BB.lv007,')') name from sl_lv0013 BB left join sl_lv0009 CC on BB.lv007=CC.lv001 where concat(BB.lv024,',') like '%," . $vBang . ",%' and BB.lv011=0 ";
		$vresult = db_query($lvsql);
		if ($vresult) {
			$vrow = db_fetch_array($vresult);
			return $vrow;
		}
		return null;
	}
	function LV_GetItem_Time($vCode)
	{
		$vcondition = "";
		$lvsql = "select * from  sl_lv0007 Where lv017='" . $vCode . "'";
		$vresult = db_query($lvsql);
		while ($vrow = db_fetch_array($vresult)) {
			$vreturn = $vreturn . "," . $vrow['lv001'];
		}
		return $vreturn;
	}
	function LV_GetDetailRun($lvsl_lv0014, $vCurHD, $vContractID)
	{
		if ($this->itemlistTime == null) $this->itemlistTime = $this->LV_GetItem_Time('TIME');
		if ($this->itemlistDay == null) $this->itemlistDay = $this->LV_GetItem_Time('DAY');
		if ($this->itemlistCalc == null) $this->itemlistCalc = $this->LV_GetItem_Time('CALC');
		$vobjtext = "";
		$lvsql = "select A.*,C.lv002 Name from sl_lv0014 A inner join sl_lv0013 B on A.lv002=B.lv001 inner join sl_lv0007 C on A.lv003=C.lv001 where 1=1 and B.lv001='$vContractID'";
		$vresult = db_query($lvsql);
		$i = 1;
		$vSum = 0;
		$vSumTT = 0;
		$isCal = true;
		$isDetail = true;
		$timeview = $vCurHD['timeview'];
		$curtime = $vCurHD['curtime'];
		$timeagain = $vCurHD['timeagain'];
		$days = $vCurHD['days'];
		$h24 = $vCurHD['h24'];
		$vsate = (int)$vCurHD['state'];
		if ($days > 0)
			$limittime = $timeagain + ($days - 1) * $h24 + $curtime;
		else
			$limittime = $curtime - $timeview;
		while ($vrow = db_fetch_array($vresult)) {
			if (strpos("," . $this->itemlistTime . ",", "," . $vrow['lv003'] . ",") === false) {
				if (strpos("," . $this->itemlistDay . ",", "," . $vrow['lv003'] . ",") === false) {
					$vobjtext = $vobjtext . '';
				} else {
					//$lvsl_lv0014->LV_UpdateQty($vrow['lv001'],$limittime);
				}
			} else {
				//$lvsl_lv0014->LV_UpdateQty($vrow['lv001'],$limittime);
			}
			if (strpos("," . $this->itemlistCalc . ",", "," . $vrow['lv003'] . ",") === false) {
				$vobjtext = $vobjtext . '';
			} else {
				$vQty = $lvsl_lv0014->LV_UpdateCalc($vrow['lv001']);
			}
		}
		return $vstr;
	}
	function LV_GetDetail($vContractID, $vBangID, $vLangArr, &$vSum)
	{
		$vobjtext = "";
		$vstr = '
			<table class="lvtable">
			<tr class="lvhtable"><td class="lvhtable">S</td>' . (($this->obj_conf->lv012 == 1) ? '<td class="lvhtable">Xóa</td>' : '') . '<td class="lvhtable">Tên</td><td class="lvhtable">SLượng</td><td class="lvhtable">ĐVị</td><td class="lvhtable">Giá<td class="lvhtable">Ggiá(%)</td><!--<td class="lvhtable">Ghi chú</td>--></tr>
		';
		$lvsql = "select A.*,D.lv002 UnitName,C.lv002 Name,C.lv009 ColorName from cr_lv0276 A inner join sl_lv0013 B on A.lv002=B.lv001 inner join sl_lv0007 C on A.lv003=C.lv001 left join sl_lv0005 D on C.lv004=D.lv001 where 1=1 and B.lv001='$vContractID'";
		$vresult = db_query($lvsql);
		$i = 1;
		$vSum = 0;
		$vSumTT = 0;
		$isCal = true;
		$isDetail = true;
		while ($vrow = db_fetch_array($vresult)) {
			$vSum = $vSum + $vrow['lv004'] * $vrow['lv006'] - $vrow['lv004'] * $vrow['lv006'] * $vrow['lv011'] / 100;
			$vSumTT = $vSumTT + $vrow['lv017'] * $vrow['lv006'];
			$vstr = $vstr . '<tr class="lvlinehtable' . ($i % 2) . (($vrow['lv004'] == 0) ? ' textunderline' : '') . '"  ><td style="color:' . $vrow['ColorName'] . '">' . $i . '</td>' . (($this->obj_conf->lv012 == 1) ? '<td style="color:' . $vrow['ColorName'] . ';text-align:center"><img style="cursor:pointer" src="../images/icon/delete.png"  onclick="delline(this,\'' . $vrow['lv001'] . '\')"/></td>' : '') . '<td style="color:' . $vrow['ColorName'] . '">' . $vrow['Name'] . '</td><td><input  id="detail_id_' . $vrow['lv001'] . '" style="width:30px;text-align:center;" type="textbox" value="' . $vrow['lv004'] . '" onblur="changeqty(this,\'' . $vrow['lv001'] . '\')" title="' . $this->obj_conf->lv004 . '"/></td><td>' . $vrow['UnitName'] . '</td><td align="right"><input  id="detail_id_' . $vrow['lv001'] . '" style="width:80px;text-align:right;" type="textbox" value="' . $vrow['lv006'] . '" onblur="changeprice(this,\'' . $vrow['lv001'] . '\')" title="' . $this->obj_conf->lv004 . '"/></td><td><input  id="detail_id_' . $vrow['lv001'] . '" style="width:25px;text-align:center;" type="textbox" value="' . $vrow['lv011'] . '" onblur="changediscount(this,\'' . $vrow['lv001'] . '\')" title="' . $this->obj_conf->lv004 . '"/></td><!--<td><input type="textbox" value="' . $vrow['lv010'] . '" style="width:150px;text-align:center;background:orange" onblur="changeqtytratruoc(this,\'' . $vrow['lv001'] . '\')" name="tratruoc_' . $vContractID . '_' . $i . '" title="' . $this->FormatView($vrow['lv013'], 4) . '"/></td>--></tr>';
			$i++;
		}
		$vstr = $vstr . '
		<tr class="lvhtable"><td class="lvhtable">&nbsp;' . $vobjtext . '</td><td class="lvhtable">&nbsp;</td><td class="lvhtable">&nbsp;</td><td class="lvhtable">&nbsp;</td><td class="lvhtable">&nbsp;</td><td class="lvhtable" align="right"><span id="tongtienct">' . $this->FormatView($vSum, 10) . '</span></td><td  colspan="2"></td></tr>
		</table>
		';
		return $vstr;
	}
	function LV_GetContractMoney($vContractID, $vTax = 0)
	{
		$lvsql = "select PM.CKTM,sum(PM.lv003) money,sum(PM.lv004) convertmoney,sum(PM.lv005) discountmoney from ((select sum(A.lv004*A.lv006) lv003,sum(A.lv004*A.lv006*A.lv008/100) lv004,sum(A.lv004*A.lv006*A.lv011/100) lv005,B.lv022 CKTM from sl_lv0014 A inner join sl_lv0013 B on A.lv002=B.lv001  where 1=1 and B.lv001='$vContractID'  )) PM ";
		$vresult = db_query($lvsql);
		$vrow = db_fetch_array($vresult);
		if ($vrow['convertmoney'] == 0) {
			if ($vrow['money'] == 0) return "0";
		}
		if ($vTax != 0) {
			$allsum = $vrow['money'];
			if ($vrow['CKTM'] > 0)
				return round($allsum + $vrow['money'] * $vTax / 100 - $allsum * $vrow['CKTM'] / 100 - $vrow['discountmoney'], 0);
			else
				return round($allsum + $vrow['money'] * $vTax / 100 - $vrow['discountmoney'], 0);
		} else {
			$allsum = $vrow['money'];
			if ($vrow['CKTM'] > 0)
				return round($allsum + $vrow['convertmoney'] - $allsum * $vrow['CKTM'] / 100 - $vrow['discountmoney'], 0);
			else
				return round($allsum + $vrow['convertmoney'] - $vrow['discountmoney'], 0);
		}
	}
	function SaveOperationBBG($mahopdong, $vFieldList, $vOrderList)
	{
		$lvsql = "Update sl_lv0013 set lv398='" . sof_escape_string($vFieldList) . "',lv399='" . sof_escape_string($vOrderList) . "' where  lv001='$mahopdong';";
		$vReturn = db_query($lvsql);
		if ($vReturn) $this->InsertLogOperation($this->DateCurrent, 'sl_lv0013.update', sof_escape_string($lvsql));
		return $vReturn;
	}
	function SaveOperationBBG_Mau($mahopdong, $vMauIn, $vlang)
	{
		return;
		$lvsql = "Update sl_lv0013 set lv396='" . sof_escape_string($vlang) . "',lv397='" . sof_escape_string($vMauIn) . "' where  lv001='$mahopdong';";
		$vReturn = db_query($lvsql);
		if ($vReturn) $this->InsertLogOperation($this->DateCurrent, 'sl_lv0013.update', sof_escape_string($lvsql));
		return $vReturn;
	}
	function LoadSaveOperationBBG($mahopdong)
	{
		$lvsql = "select lv396,lv397,lv398,lv399 from  sl_lv0013 Where lv001='$mahopdong'";
		$vresult = db_query($lvsql);
		$vrow = db_fetch_array($vresult);
		if ($vrow) {
			$this->MauLang = $vrow['lv396'];
			$this->MauIn = $vrow['lv397'];
			$this->ListView = $vrow['lv398'];
			$this->ListOrder = $vrow['lv399'];
		}
	}
	function LV_Load()
	{
		$vsql = "select * from  sl_lv0013";
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
			$this->lv029 = $vrow['lv029'];
			$this->lv030 = $vrow['lv030'];
			$this->lv099 = $vrow['lv099'];
			$this->lv101 = $vrow['lv101'];
			$this->lv102 = $vrow['lv102'];
			$this->lv103 = $vrow['lv103'];
			$this->lv104 = $vrow['lv104'];
			$this->lv105 = $vrow['lv105'];
			$this->lv106 = $vrow['lv106'];
			$this->lv107 = $vrow['lv107'];
			$this->lv108 = $vrow['lv108'];
			$this->lv109 = $vrow['lv109'];
			$this->lv110 = $vrow['lv110'];
			$this->lv111 = $vrow['lv111'];
			$this->lv112 = $vrow['lv112'];
			$this->lv113 = $vrow['lv113'];
			$this->lv114 = $vrow['lv114'];
			$this->lv115 = $vrow['lv115'];
			$this->lv116 = $vrow['lv116'];
			$this->lv117 = $vrow['lv117'];
			$this->lv118 = $vrow['lv118'];
			$this->lv214 = $vrow['lv214'];
			$this->lv212 = $vrow['lv212'];
			$this->lv224 = $vrow['lv224'];
			$this->lv225 = $vrow['lv225'];
			$this->lv226 = $vrow['lv226'];
			$this->lv227 = $vrow['lv227'];
			$this->lv228 = $vrow['lv228'];
			$this->lv228 = $vrow['lv228'];
			$this->lv230 = $vrow['lv230'];
			$this->lv231 = $vrow['lv231'];
			$this->lv232 = $vrow['lv232'];
			$this->lv233 = $vrow['lv233'];
			$this->lv350 = $vrow['lv350'];
			$this->lv353 = $vrow['lv353'];
			$this->lv354 = $vrow['lv354'];
			$this->lv394 = $vrow['lv394'];
			$this->lv395 = $vrow['lv395'];
			$this->lv396 = $vrow['lv396'];
			$this->lv397 = $vrow['lv397'];
			$this->lv398 = $vrow['lv398'];
			$this->lv399 = $vrow['lv399'];
		}
	}
	function LV_SetNULL()
	{
		$this->lv001 = null;
		$this->lv002 = null;
		$this->lv003 = null;
		$this->lv004 = null;
		$this->lv005 = null;
		$this->lv006 = null;
		$this->lv007 = null;
		$this->lv008 = null;
		$this->lv009 = null;
		$this->lv010 = null;
		$this->lv011 = null;
		$this->lv012 = null;
		$this->lv013 = null;
		$this->lv014 = null;
		$this->lv015 = null;
		$this->lv016 = null;
		$this->lv017 = null;
	}
	function LV_LoadEmailUser($vCodeID)
	{
		$lvsql = "select lv009 from  lv_lv0007 Where lv001='$vCodeID' ";
		$vresult = db_query($lvsql);
		$vrow = db_fetch_array($vresult);
		if ($vrow) {
			return $vrow['lv009'];
		}
		return '';
	}
	function LV_LoadEmailAuth($vCodeID)
	{
		$lvsql = "select lv040 from  hr_lv0020 Where lv001='$vCodeID' ";
		$vresult = db_query($lvsql);
		$vrow = db_fetch_array($vresult);
		if ($vrow) {
			return $vrow['lv040'];
		}
		return '';
	}
	function LV_LoadEmailID($vPRID)
	{
		$lvsql = "select * from  sl_lv0013 Where lv001='$vPRID'";
		$vresult = db_query($lvsql);
		$vrow = db_fetch_array($vresult);
		if ($vrow) {
			$this->EmailAlarmCC = '';
			//////Get Email 1: lv016
			$vEmail1 = $this->LV_LoadEmailUser($vrow['lv016']);
			if ($vEmail1 != '') {
				if ($this->EmailAlarmCC != '')
					$this->EmailAlarmCC = $this->EmailAlarmCC . ',' . $vEmail1;
				else
					$this->EmailAlarmCC = $vEmail1;
			}
			//////Get Email 2: lv020
			$vEmail2 = $this->LV_LoadEmailAuth($vrow['lv020']);
			if ($vEmail2 != '') {
				if ($this->EmailAlarmCC != '')
					$this->EmailAlarmCC = $this->EmailAlarmCC . ',' . $vEmail2;
				else
					$this->EmailAlarmCC = $vEmail2;
			}
			//////Get Email 3: lv018
			if ($vEmail3 != '') {
				$vEmail3 = $this->LV_LoadEmailAuth($vrow['lv018']);
				if ($this->EmailAlarmCC != '')
					$this->EmailAlarmCC = $this->EmailAlarmCC . ',' . $vEmail3;
				else
					$this->EmailAlarmCC = $vEmail3;
			}
			$this->EmailNoiDung = '';
			$this->EmailTitle = 'New Procurement Requisition No.:[' . $vrow['lv014'] . ']';
			$this->EmailNoiDung = 'Dear Sir,<br/><br/>Please be advised that PR No.:' . $vrow['lv014'] . ' for RE-Test has been issued. Highly appreciate CBD\'s arrangements of this PR in due course.';
			$this->EmailNoiDung = $this->EmailNoiDung . '<br/><br/>Yours Sincerely,';
			$this->EmailNoiDung = $this->EmailNoiDung . '<br/><br/>' . $this->molv_lv0007->lv004;
			$this->EmailNoiDung = $this->EmailNoiDung . '<br/>' . $this->molv_lv0007->lv010;
		} else {
			$this->EmailNoiDung = '';
		}
	}
	function LV_GetListCus($vCusId, $vField = 'lv001')
	{
		$vsql = "select $vField from sl_lv0013 where lv002='$vCusId'";
		$vresult = db_query($vsql);
		$strReturn = "";
		if ($vresult) {
			while ($vrow = db_fetch_array($vresult)) {
				if ($strReturn == "") $strReturn = "'" . $vrow["$vField"] . "'";
				else $strReturn = $strReturn . ",'" . $vrow["$vField"] . "'";
			}
			if ($strReturn == '') return "''";
			return $strReturn;
		}
		if ($strReturn == '') return "''";
		return $strReturn;
	}
	function LV_UpdateGocQuay($vlv007, $vValue, $vOpt)
	{
		$vField = 'lv' . Fillnum($vOpt, 3);
		$lvsql = "update hao_erp_sof_documents_v5_0.hr_lv0211 set $vField='$vValue' Where lv007='$vlv007' ";
		$vResult = db_query($lvsql);
		return $vResult;
	}
	function LV_LayGocQuay($vlv007)
	{
		$lvsql = "select lv108,lv109,lv110,lv111,lv112,lv113,lv114,lv115 from  hao_erp_sof_documents_v5_0.hr_lv0211 Where lv007='$vlv007' ";
		$vresult = db_query($lvsql);
		$vrow = db_fetch_array($vresult);
		if ($vrow) {
			$this->quay_lv108 = $vrow['lv108'];
			$this->quay_lv109 = $vrow['lv109'];
			$this->quay_lv110 = $vrow['lv110'];
			$this->quay_lv111 = $vrow['lv111'];
			$this->quay_lv112 = $vrow['lv112'];
			$this->quay_lv113 = $vrow['lv113'];
			$this->quay_lv114 = $vrow['lv114'];
			$this->quay_lv115 = $vrow['lv115'];
		} else {
			$this->quay_lv108 = 0;
			$this->quay_lv109 = 0;
			$this->quay_lv110 = 0;
			$this->quay_lv111 = 0;
			$this->quay_lv112 = 0;
			$this->quay_lv113 = 0;
			$this->quay_lv114 = 0;
			$this->quay_lv115 = 0;
		}
	}
	/*function LV_GetQRcode($vcode)
	{
		$vStrReturn='';
		//Local
		file_get_contents($this->domains."qrcode/index.php?data=".$vcode);
		//Mạng
		//echo file_get_contents("https://quanlykho.benthanhhouse.com.vn/qrcode/index.php?data=".$vcode);
		$vStrImg='../../qrcode/images/'.$vcode.'.png';
		$vStrReturn='<table style="width:226px;height:150px;font:8px Arial;"><tr>';
		
		$vStrReturn=$vStrReturn.'<td style="padding-left:0px;padding-right:15px;width:150px;" valign="top">
		<center><img style="width:150px;height:150px;" src="'.$vStrImg.'"/></center>
		<div style="padding-left:5px;width:200px;"><div note="transform: rotate(-90deg);" style="width:200px;heigth:65px;"><center><font style="font:10px Arial;"><strong>'.$vrow['lv002']."</strong></font>".'<br/><font style="font:12px Arial;"><strong>Mã: '.$vrow['lv001'].'</strong></font></center></div></td>';
		$vStrReturn=$vStrReturn.'</tr></table>';
		return $vStrReturn;
	}*/
	function LV_GetQRcodeLink($vcode, $vFileName)
	{
		$arrContextOptions = array(
			"ssl" => array(
				"verify_peer" => false,
				"verify_peer_name" => false,
			),
		);
		//Local
		//echo $this->domains."qrcode/index.php?data=".$vcode."&filename=".$vFileName;
		$this->LinkQR = $this->domains . "qrcode/index.php?data=" . $vcode . "&filename=" . $vFileName;
		file_get_contents($this->domains . "qrcode/index.php?data=" . $vcode . "&filename=" . $vFileName, false, stream_context_create($arrContextOptions));
		//Mạng
		//echo file_get_contents("https://quanlykho.benthanhhouse.com.vn/qrcode/index.php?data=".$vcode);
		return $vStrImg = '../../qrcode/images/' . $vFileName . '.png';
	}
	function LV_GetWell($vQuotationID)
	{
		$lvsql = "select lv003 lv001 from  sl_lv0304 Where lv002='$vQuotationID'";
		$vresult = db_query($lvsql);
		while ($vrow = db_fetch_array($vresult)) {
			if ($strLv001 == "")
				$strLv001 = $vrow['lv001'];
			else
				$strLv001 = $strLv001 . "," . $vrow['lv001'] . "";
		}
		return  $strLv001;
	}
	function LV_ChangeShiftAutoUpdate($vProgID)
	{
		if ($vProgID == "" || $vProgID == NULL) return;
		$lvsql = "select * from  sl_lv0013 Where lv011='0' and (lv012<>'$vProgID' and lv012<>'')";
		$vresult = db_query($lvsql);
		while ($vrow = db_fetch_array($vresult)) {
			if ($strLv001 == "")
				$strLv001 = "'" . $vrow['lv001'] . "'";
			else
				$strLv001 = $strLv001 . ",'" . $vrow['lv001'] . "'";
		}
		if ($strLv001 != "") {
			$vsql = "select * from sl_lv0014 where lv002 in ($strLv001)";
			$vresult = db_query($vsql);
			while ($vrow = db_fetch_array($vresult)) {
				$this->mosl_lv0014->LV_CheckPriceItem($vItem, $vProgId, $vPercent, $vPrice);
				$vsql = "update sl_lv0014 set lv006=IF($vPrice=0,lv006,$vPrice),lv011='$vPercent' where lv001='" . $vrow['lv001'] . "'";
				db_query($vsql);
			}
			$vsql = "update sl_lv0013 set lv012='$vProgID' where lv001 in ($strLv001) and lv011=0";
			db_query($vsql);
		}
	}

	function LV_LoadChild($vparent)
	{
		$vsql = "select * from  sl_lv0013 where lv017='$vparent'";
		$vresult = db_query($vsql);
		return $vresult;
	}

	function LV_LoadID($vlv001)
	{
		$lvsql = "select * from  sl_lv0013 Where lv001='$vlv001'";
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
			$this->lv026 = $vrow['lv026'];
			$this->lv027 = $vrow['lv027'];
			$this->lv028 = $vrow['lv028'];
			$this->lv029 = $vrow['lv029'];
			$this->lv030 = $vrow['lv030'];
			$this->lv099 = $vrow['lv099'];
			$this->lv101 = $vrow['lv101'];
			$this->lv102 = $vrow['lv102'];
			$this->lv103 = $vrow['lv103'];
			$this->lv104 = $vrow['lv104'];
			$this->lv105 = $vrow['lv105'];
			$this->lv106 = $vrow['lv106'];
			$this->lv107 = $vrow['lv107'];
			$this->lv108 = $vrow['lv108'];
			$this->lv109 = $vrow['lv109'];
			$this->lv110 = $vrow['lv110'];
			$this->lv111 = $vrow['lv111'];
			$this->lv112 = $vrow['lv112'];
			$this->lv113 = $vrow['lv113'];
			$this->lv114 = $vrow['lv114'];
			$this->lv115 = $vrow['lv115'];
			$this->lv116 = $vrow['lv116'];
			$this->lv117 = $vrow['lv117'];
			$this->lv118 = $vrow['lv118'];
			$this->lv214 = $vrow['lv214'];
			$this->lv212 = $vrow['lv212'];
			$this->lv224 = $vrow['lv224'];
			$this->lv225 = $vrow['lv225'];
			$this->lv226 = $vrow['lv226'];
			$this->lv227 = $vrow['lv227'];
			$this->lv228 = $vrow['lv228'];
			$this->lv228 = $vrow['lv228'];
			$this->lv230 = $vrow['lv230'];
			$this->lv231 = $vrow['lv231'];
			$this->lv232 = $vrow['lv232'];
			$this->lv233 = $vrow['lv233'];
			$this->lv350 = $vrow['lv350'];
			$this->lv353 = $vrow['lv353'];
			$this->lv354 = $vrow['lv354'];
			$this->lv394 = $vrow['lv394'];
			$this->lv395 = $vrow['lv395'];
			$this->lv396 = $vrow['lv396'];
			$this->lv397 = $vrow['lv397'];
			$this->lv398 = $vrow['lv398'];
			$this->lv399 = $vrow['lv399'];
		} else {
			$this->lv001 = null;
			$this->lv002 = null;
		}
	}
	function LV_GetPTMoney($vContractID)
	{
		//GetMoney
		$vListParent = $this->LV_GetInvoiceParent($vContractID, 0);
		$lvsql = "select if(ISNULL(sum(lv004)),0,sum(lv004)) SumMoney from ac_lv0005 A  WHERE A.lv002 in (" . $vListParent . ") ";
		$vReturnArr = array();
		$lvResult = db_query($lvsql);
		$row = db_fetch_array($lvResult);
		return $row['SumMoney'];
	}
	function LV_GetInvoiceParent($vContractID, $vtype)
	{
		$vResult = '';
		$lvsql = "select B.lv001 from ac_lv0004 B where B.lv013='$vContractID' and B.lv002='$vtype'";
		$lvResult = db_query($lvsql);
		while ($row = db_fetch_array($lvResult)) {
			if ($vResult == "")
				$vResult = "'" . $row['lv001'] . "'";
			else
				$vResult = $vResult . ",'" . $row['lv001'] . "'";;
		}
		if ($vResult == '') return "''";
		else
			return $vResult;
	}
	function LV_LoadQRID($vID)
	{
		$lvsql = "select * from  sl_lv0013 Where lv115='$vID'";
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
			$this->lv026 = $vrow['lv026'];
			$this->lv027 = $vrow['lv027'];
			$this->lv028 = $vrow['lv028'];
			$this->lv029 = $vrow['lv029'];
			$this->lv030 = $vrow['lv030'];
			$this->lv099 = $vrow['lv099'];
			$this->lv101 = $vrow['lv101'];
			$this->lv102 = $vrow['lv102'];
			$this->lv103 = $vrow['lv103'];
			$this->lv104 = $vrow['lv104'];
			$this->lv105 = $vrow['lv105'];
			$this->lv106 = $vrow['lv106'];
			$this->lv107 = $vrow['lv107'];
			$this->lv108 = $vrow['lv108'];
			$this->lv109 = $vrow['lv109'];
			$this->lv110 = $vrow['lv110'];
			$this->lv111 = $vrow['lv111'];
			$this->lv112 = $vrow['lv112'];
			$this->lv113 = $vrow['lv113'];
			$this->lv114 = $vrow['lv114'];
			$this->lv115 = $vrow['lv115'];
			$this->lv116 = $vrow['lv116'];
			$this->lv117 = $vrow['lv117'];
			$this->lv118 = $vrow['lv118'];
			$this->lv214 = $vrow['lv214'];
			$this->lv212 = $vrow['lv212'];
			$this->lv224 = $vrow['lv224'];
			$this->lv225 = $vrow['lv225'];
			$this->lv226 = $vrow['lv226'];
			$this->lv227 = $vrow['lv227'];
			$this->lv228 = $vrow['lv228'];
			$this->lv228 = $vrow['lv228'];
			$this->lv230 = $vrow['lv230'];
			$this->lv231 = $vrow['lv231'];
			$this->lv232 = $vrow['lv232'];
			$this->lv233 = $vrow['lv233'];
			$this->lv350 = $vrow['lv350'];
			$this->lv353 = $vrow['lv353'];
			$this->lv354 = $vrow['lv354'];
			$this->lv394 = $vrow['lv394'];
			$this->lv395 = $vrow['lv395'];
			$this->lv396 = $vrow['lv396'];
			$this->lv397 = $vrow['lv397'];
			$this->lv398 = $vrow['lv398'];
			$this->lv399 = $vrow['lv399'];
		} else {
			$this->lv001 = null;
			$this->lv002 = null;
		}
	}
	function LV_LoadIDAmount($vlv001)
	{
		$lvsql = "select * from  sl_lv0013 Where lv001='$vlv001'";
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
			$this->lv026 = $vrow['lv026'];
			$this->lv027 = $vrow['lv027'];
			$this->lv028 = $vrow['lv028'];
			$this->lv029 = $vrow['lv029'];
			$this->lv030 = $vrow['lv030'];
			$this->lv099 = $vrow['lv099'];
			$this->lv101 = $vrow['lv101'];
			$this->lv102 = $vrow['lv102'];
			$this->lv103 = $vrow['lv103'];
			$this->lv104 = $vrow['lv104'];
			$this->lv105 = $vrow['lv105'];
			$this->lv106 = $vrow['lv106'];
			$this->lv107 = $vrow['lv107'];
			$this->lv108 = $vrow['lv108'];
			$this->lv109 = $vrow['lv109'];
			$this->lv110 = $vrow['lv110'];
			$this->lv111 = $vrow['lv111'];
			$this->lv112 = $vrow['lv112'];
			$this->lv113 = $vrow['lv113'];
			$this->lv114 = $vrow['lv114'];
			$this->lv115 = $vrow['lv115'];
			$this->lv116 = $vrow['lv116'];
			$this->lv117 = $vrow['lv117'];
			$this->lv118 = $vrow['lv118'];
			$this->lv214 = $vrow['lv214'];
			$this->lv212 = $vrow['lv212'];
			$this->lv224 = $vrow['lv224'];
			$this->lv225 = $vrow['lv225'];
			$this->lv226 = $vrow['lv226'];
			$this->lv227 = $vrow['lv227'];
			$this->lv228 = $vrow['lv228'];
			$this->lv228 = $vrow['lv228'];
			$this->lv230 = $vrow['lv230'];
			$this->lv231 = $vrow['lv231'];
			$this->lv232 = $vrow['lv232'];
			$this->lv233 = $vrow['lv233'];
			$this->lv350 = $vrow['lv350'];
			$this->lv353 = $vrow['lv353'];
			$this->lv354 = $vrow['lv354'];
			$this->lv394 = $vrow['lv394'];
			$this->lv395 = $vrow['lv395'];
			$this->lv396 = $vrow['lv396'];
			$this->lv397 = $vrow['lv397'];
			$this->lv398 = $vrow['lv398'];
			$this->lv399 = $vrow['lv399'];
		}
	}
	function LV_CreatePublic($vPlanID, $vFields, $vValues, $vTable)
	{
		$sqlC = "SELECT COUNT(*) AS nums FROM $vTable WHERE lv001='$vPlanID'";
		$bResultC = db_query($sqlC);
		$arrRowC = db_fetch_array($bResultC);
		if ($arrRowC['nums'] == 0) {
			$lvsql = "insert into $vTable (" . $vFields . ") values(" . $vValues . ")";
			$vReturn = db_query($lvsql);
			if ($vReturn) $this->InsertLogOperation($this->DateCurrent, $vTable . '.insert', sof_escape_string($lvsql));
		}
	}
	function LV_CreateThanhToan($vInsertID, $vFields, $vValues, $vTable, $vLan)
	{
		$sqlC = "SELECT COUNT(*) AS nums FROM $vTable WHERE lv002='$vInsertID' and lv004='$vLan'";
		$bResultC = db_query($sqlC);
		$arrRowC = db_fetch_array($bResultC);
		if ($arrRowC['nums'] == 0) {
			$lvsql = "insert into $vTable (lv002,lv004," . $vFields . ") values('$vInsertID','$vLan'," . $vValues . ")";
			$vReturn = db_query($lvsql);
			if ($vReturn) $this->InsertLogOperation($this->DateCurrent, $vTable . '.insert', sof_escape_string($lvsql));
		}
	}

	function LV_InsertFieldFull($vArrFied, $row)
	{

		if ($this->isAdd == 0) return false;
		$vFields = '';
		$vValues = '';
		$vArrField = array();
		$vValueUpdate = '';
		$vCatalogueID = '';
		$visCon = false;
		$vTien = 0;
		$vPlanID = '';
		$vCusID = '';
		$vLoaiBH = 0;
		$vTenDuAn = '';
		$vStartDate = '';
		$vEndDate = '';
		$vNguoiTiepNhan = '';
		$vSale = '';
		$vPBHID = '';
		foreach ($vArrFied as $vField) {

			if (strlen(trim($vField)) > 4 &&  trim($vField) != 'lv0' && trim($vField) != 'lv00' && trim($vField) != 'lv000') {
				if ($this->ArrViewImport[$vField] == '2') {
					$row[$vField] = recoverdate($row[$vField], 'VN');
				}
				if (trim($vField) == 'lv115') {
					$vPBHID = $row[$vField];
				}
				if (trim($vField) == 'lv353' || trim($vField) == 'lv354') {
					if ($sStrUpdate == '') {

						$sStrUpdate = "$vField='" . sof_escape_string($row[$vField]) . "'";
					} else {
						$sStrUpdate = $sStrUpdate . ",$vField='" . sof_escape_string($row[$vField]) . "'";
					}
				}
				if ($vField > 'lv200' && $vField < 'lv400') {
					if ($vField == 'lv306') $vTien = $row[$vField];
					if ($vField == 'lv313') $vStartDate = $row[$vField];
					if ($vField == 'lv314') $vEndDate = $row[$vField];
				} else if ($vField > 'lv400' && $vField < 'lv500') {
					if ($row['lv412'] > 0) {
						//Lần 0

						//Thanh toán
						$vField1 = str_replace('lv4', 'lv0', $vField);
						if ($vFieldsTTLan0 == '') {
							$vFieldsTTLan0 = $vField1;
							$vValuesTTLan0 = "'" . $row[$vField] . "'";
						} else {
							$vFieldsTTLan0 = $vFieldsTTLan0 . ',' . $vField1;
							$vValuesTTLan0 = $vValuesTTLan0 . ",'" . sof_escape_string($row[$vField]) . "'";
						}
						if ($vField1 == 'lv007') {
							$vFieldsTTLan0 = $vFieldsTTLan0 . ',lv008';
							$vValuesTTLan0 = $vValuesTTLan0 . ",'" . sof_escape_string($row[$vField]) . "'";
						}
					}
				} else if ($vField > 'lv500' && $vField < 'lv600') {
					if ($row['lv512'] > 0) {
						//Lần 1
						//Thanh toán
						$vField1 = str_replace('lv5', 'lv0', $vField);
						if ($vFieldsTTLan1 == '') {
							$vFieldsTTLan1 = $vField1;
							$vValuesTTLan1 = "'" . $row[$vField] . "'";
						} else {
							$vFieldsTTLan1 = $vFieldsTTLan1 . ',' . $vField1;
							$vValuesTTLan1 = $vValuesTTLan1 . ",'" . sof_escape_string($row[$vField]) . "'";
						}
						if ($vField1 == 'lv007') {
							$vFieldsTTLan1 = $vFieldsTTLan1 . ',lv008';
							$vValuesTTLan1 = $vValuesTTLan1 . ",'" . sof_escape_string($row[$vField]) . "'";
						}
					}
				} else if ($vField > 'lv600' && $vField < 'lv700') {
					if ($row['lv612'] > 0) {
						//Lần 2
						//Thanh toán
						$vField1 = str_replace('lv6', 'lv0', $vField);
						if ($vFieldsTTLan2 == '') {
							$vFieldsTTLan2 = $vField1;
							$vValuesTTLan2 = "'" . $row[$vField] . "'";
						} else {
							$vFieldsTTLan2 = $vFieldsTTLan2 . ',' . $vField1;
							$vValuesTTLan2 = $vValuesTTLan2 . ",'" . sof_escape_string($row[$vField]) . "'";
						}
						if ($vField1 == 'lv007') {
							$vFieldsTTLan2 = $vFieldsTTLan2 . ',lv008';
							$vValuesTTLan2 = $vValuesTTLan2 . ",'" . sof_escape_string($row[$vField]) . "'";
						}
					}
				} else if ($vField > 'lv700' && $vField < 'lv800') {
					if ($row['lv712'] > 0) {
						//Lần 3 
						//Thanh toán
						$vField1 = str_replace('lv7', 'lv0', $vField);
						if ($vFieldsTTLan3 == '') {
							$vFieldsTTLan3 = $vField1;
							$vValuesTTLan3 = "'" . $row[$vField] . "'";
						} else {
							$vFieldsTTLan3 = $vFieldsTTLan3 . ',' . $vField1;
							$vValuesTTLan3 = $vValuesTTLan3 . ",'" . sof_escape_string($row[$vField]) . "'";
						}
						if ($vField1 == 'lv007') {
							$vFieldsTTLan3 = $vFieldsTTLan3 . ',lv008';
							$vValuesTTLan3 = $vValuesTTLan3 . ",'" . sof_escape_string($row[$vField]) . "'";
						}
					}
				} else if ($vField > 'lv800' && $vField < 'lv900') {
					//Plan
					$vField1 = str_replace('lv8', 'lv0', $vField);
					if ($vField1 == 'lv001') $vPlanID = $row[$vField];

					if ($vFieldsPlan == '') {
						$vFieldsPlan = $vField1;
						$vValuesPlan = "'" . $row[$vField] . "'";
					} else {
						$vFieldsPlan = $vFieldsPlan . ',' . $vField1;
						$vValuesPlan = $vValuesPlan . ",'" . sof_escape_string($row[$vField]) . "'";
					}
					if ($vField1 == 'lv002') {
						$vTenDuAn = $row[$vField];
						$vFieldsPlan = $vFieldsPlan . ',lv009';
						$vValuesPlan = $vValuesPlan . ",'" . sof_escape_string($row[$vField]) . "'";
					}
				} else if ($vField > 'lv900') {
					//Khách hàng
					$vField1 = str_replace('lv9', 'lv0', $vField);
					if ($vField1 == 'lv001') $vCusID = $row[$vField];

					if ($vFieldsCus == '') {
						$vFieldsCus = $vField1;
						$vValuesCus = "'" . $row[$vField] . "'";
					} else {
						$vFieldsCus = $vFieldsCus . ',' . $vField1;
						$vValuesCus = $vValuesCus . ",'" . sof_escape_string($row[$vField]) . "'";
					}
				} else {
					if ($vField1 == 'lv113') $vLoaiBH = $row[$vField];
					$row[$vField] = trim($row[$vField]);
					//$vFieldCheck=str_replace('lv8','lv0',$vField);
					if ($vField == 'lv016') {
						if ($vFieldsPlan == '') {
							$vFieldsPlan = 'lv079';
							$vValuesPlan = "'" . $row[$vField] . "'";
						} else {
							$vFieldsPlan = $vFieldsPlan . ',lv079';
							$vValuesPlan = $vValuesPlan . ",'" . sof_escape_string($row[$vField]) . "'";
						}
						if (trim($row[$vField]) != '') {
							if ($vNguoiTiepNhan == '')
								$vNguoiTiepNhan = $vNguoiTiepNhan . $row[$vField];
							else
								$vNguoiTiepNhan = $vNguoiTiepNhan . ',' . $row[$vField];
						}
						$vSale = $row[$vField];
					}
					if ($vField == 'lv013') {
						if ($vFieldsPlan == '') {
							$vFieldsPlan = 'lv080';
							$vValuesPlan = "'" . $row[$vField] . "'";
						} else {
							$vFieldsPlan = $vFieldsPlan . ',lv080';
							$vValuesPlan = $vValuesPlan . ",'" . sof_escape_string($row[$vField]) . "'";
						}
						if (trim($row[$vField]) != '') {
							if ($vNguoiTiepNhan == '')
								$vNguoiTiepNhan = $vNguoiTiepNhan . $row[$vField];
							else
								$vNguoiTiepNhan = $vNguoiTiepNhan . ',' . $row[$vField];
						}
					}
					if ($vField == 'lv018') {
						if ($vFieldsPlan == '') {
							$vFieldsPlan = 'lv081';
							$vValuesPlan = "'" . $row[$vField] . "'";
						} else {
							$vFieldsPlan = $vFieldsPlan . ',lv081';
							$vValuesPlan = $vValuesPlan . ",'" . sof_escape_string($row[$vField]) . "'";
						}
						if (trim($row[$vField]) != '') {
							if ($vNguoiTiepNhan == '')
								$vNguoiTiepNhan = $vNguoiTiepNhan . $row[$vField];
							else
								$vNguoiTiepNhan = $vNguoiTiepNhan . ',' . $row[$vField];
						}
					}
					if ($vArrField[$vFieldCheck] == true && $vFieldCheck != '') {
						//echo $vField.'=>'.$row[$vField].'<br/>';
						if ($vValueUpdate == '')
							$vValueUpdate = $vValueUpdate . "$vFieldCheck=concat($vFieldCheck,'\n','" . sof_escape_string($row[$vField]) . "')";
						else
							$vValueUpdate = $vValueUpdate . ",$vFieldCheck=concat($vFieldCheck,'\n','" . sof_escape_string($row[$vField]) . "')";
					} else {
						if ($vFields == '') {
							$vFields = $vField;
							$vValues = "'" . sof_escape_string($row[$vField]) . "'";
						} else {
							$vFields = $vFields . ',' . $vField;
							$vValues = $vValues . ",'" . sof_escape_string($row[$vField]) . "'";
						}
						$row[$vField] = $ArrList[$i];
						$i++;
					}
				}
			}
			$vArrField[$vField] = true;
		}
		if (trim($vPBHID) != '' && $vPBHID != null) {
			//KH Tạo 
			if ($vCusID != '') {
				if ($vSale != '') {
					$vFieldsCus = $vFieldsCus . ',lv025,lv024';
					$vValuesCus = $vValuesCus . ",'" . ($vSale) . "',now()";
				}
				$this->LV_CreatePublic($vCusID, $vFieldsCus, $vValuesCus, 'sl_lv0001');
			}
			//Xử lý tạo PLAN và tạo công việc
			$vJobID = '';
			if ($vPlanID != '') {
				if ($vNguoiTiepNhan != '') {
					$vFieldsPlan = $vFieldsPlan . ',lv097';
					$vValuesPlan = $vValuesPlan . ",'" . ($vNguoiTiepNhan) . "'";
				}
				$this->LV_CreatePublic($vPlanID, $vFieldsPlan, $vValuesPlan, 'cr_lv0004');
				$this->mocr_lv0092->lv011 = 1;
				switch ($vLoaiBH) {
					case 1:
						$this->mocr_lv0092->lv004 = 'Tạo PBH Bảo Hành';
						$vJobID = $this->mocr_lv0092->LV_CheckCreateJobPlan($vPlanID, 'HĐKT');
						break;
					case 2:
						$this->mocr_lv0092->lv004 = 'Tạo PBH Bảo Hành';
						$vJobID = $this->mocr_lv0092->LV_CheckCreateJobPlan($vPlanID, 'PLHĐ');
						break;
					default:
						$this->mocr_lv0092->lv004 = 'Tạo PBH Bảo Hành';
						$vJobID = $this->mocr_lv0092->LV_CheckCreateJobPlan($vPlanID, 'PO');
						break;
				}
			}
			if (trim($vPBHID) != '' && $vPBHID != null)
				$vIDNew = $this->LV_CheckExist($vPBHID);
			else
				$vIDNew = null;
			if ($vIDNew == null) {
				$lvsql = "insert into sl_lv0013 (lv002,lv003,lv114," . $vFields . ") values('$vCusID','" . sof_escape_string($vTenDuAn) . "','$vJobID'," . $vValues . ")";
				$vReturn = db_query($lvsql);
				if ($vReturn) {
					$vIDAuto = sof_insert_id();
					$this->InsertLogOperation($this->DateCurrent, 'sl_lv0013.insert', sof_escape_string($lvsql));
					if ($vIDAuto > 0) {
						//$lvsql="update sl_lv0013 set $vValueUpdate  where lv001='$vIDAuto'";
						//$vReturn= db_query($lvsql);
						$vMaSP = '0000000000000';
						$lvsql = "insert into cr_lv0276(lv002,lv003,lv012,lv051,lv053,lv054) values('$vIDAuto','$vMaSP','$this->lv115','1','$vTien','$vTien')";
						$vReturn = db_query($lvsql);
						if ($vReturn) {
							$vIDAuto1 = sof_insert_id();
							$this->InsertLogOperation($this->DateCurrent, 'sl_lv0013.insert', sof_escape_string($lvsql));
							$lvsql = "insert into sl_lv0014(lv001,lv002,lv003,lv004,lv006,lv013,lv014) values('$vIDAuto1','$vIDAuto','$vMaSP','1','$vTien','$vStartDate','$vEndDate')";
							$vReturn = db_query($lvsql);
							if ($vReturn) $this->InsertLogOperation($this->DateCurrent, 'sl_lv0013.insert', sof_escape_string($lvsql));
						}
						//TT Lan 0
						if ($vFieldsTTLan0 != '') {
							$this->LV_CreateThanhToan($vIDAuto, $vFieldsTTLan0, $vValuesTTLan0, 'sl_lv0324', 0);
						}
						//TT Lan 1
						if ($vFieldsTTLan1 != '') {
							$this->LV_CreateThanhToan($vIDAuto, $vFieldsTTLan1, $vValuesTTLan1, 'sl_lv0324', 1);
						}
						//TT Lan 2
						if ($vFieldsTTLan2 != '') {
							$this->LV_CreateThanhToan($vIDAuto, $vFieldsTTLan2, $vValuesTTLan2, 'sl_lv0324', 2);
						}
						//TT Lan 3
						if ($vFieldsTTLan3 != '') {
							$this->LV_CreateThanhToan($vIDAuto, $vFieldsTTLan3, $vValuesTTLan3, 'sl_lv0324', 3);
						}
					}
					$vListIDAuto = "'" . $vIDAuto . "'";
				}
			} else {
				$lvsql = "update sl_lv0013 set $sStrUpdate  where lv001='$vIDNew'";
				$vReturn = db_query($lvsql);
				if ($vReturn) $this->InsertLogOperation($this->DateCurrent, 'sl_lv0013.update', sof_escape_string($lvsql));
				//return $vReturn;
			}
		}
		return $vReturn;
	}
	//Xu ly kiem tra ton tai
	function LV_CheckExist($vPBHID)
	{
		$lvsql = "select lv001 from  sl_lv0013 Where lv115='$vPBHID'";
		$vresult = db_query($lvsql);
		$vrow = db_fetch_array($vresult);
		if ($vrow) {
			return $vrow['lv001'];
		}
		return null;
	}
	function LV_GetDataFill()
	{
		$vReturn = array();
		$lvsql = "select * from sl_lv0054";
		$vresult = db_query($lvsql);
		while ($vrow = db_fetch_array($vresult)) {
			$this->ArrStateName[$vrow['lv002']] = $vrow['lv001'];
		}
	}
	function LV_GetTienGiaoHang($vContractID, $vDot, &$vVAT = 0)
	{
		$lvsql = "SELECT sum(AA.lv004*A.lv053) ThanhTien,sum(AA.lv004*A.lv053*B.lv110/100) VAT FROM cr_lv0276 A inner join cr_lv0114 AA on A.lv001=AA.lv198  inner join cr_lv0113 AB on AA.lv002=AB.lv001 and AB.lv005='$vDot' inner join sl_lv0013 B on A.lv002=B.lv001 WHERE A.lv002='$vContractID' ";
		$vresult = db_query($lvsql);
		$vrow = db_fetch_array($vresult);
		$vVAT = $vrow['VAT'];
		return $vrow['ThanhTien'] + $vrow['VAT'];
	}
	function LV_GetTienHopDong($vContractID, $vDot = '')
	{
		if ($vDot == '') {
			$lvsql = "select B.lv108,B.lv109,B.lv110 from sl_lv0013 B where B.lv001='$vContractID'";
			$vresult = db_query($lvsql);
			$vrow = db_fetch_array($vresult);
			$vlv900 = $this->LV_LoadAmountDetail($vContractID);
			$vDiscount = $vrow['lv108'];
			$vTienDisCount = $vlv900 * $vDiscount / 100;
			$vCostShip = (float)$vrow['lv109'];
			$vVAT = (float)$vrow['lv110'];
			$vTienVAT = ($vlv900 - $vTienDisCount) * $vVAT / 100; //$vTienVAT=($vlv900-$vTienDisCount+$vCostShip)*$vVAT/100;
			$vlv900 = $vlv900 + $vTienVAT + $vCostShip - $vTienDisCount;
			return $vlv900;
		} else {
			$lvsql = "select B.lv108,B.lv109,B.lv110 from sl_lv0013 B where B.lv001='$vContractID'";
			$vresult = db_query($lvsql);
			$vrow = db_fetch_array($vresult);
			$vlv900 = $this->LV_LoadAmountDetail($vContractID);
			$vDiscount = $vrow['lv108'];
			$vTienDisCount = $vlv900 * $vDiscount / 100;
			$vCostShip = (float)$vrow['lv109'];
			$vVAT = (float)$vrow['lv110'];
			$vTienVAT = ($vlv900 - $vTienDisCount) * $vVAT / 100; //$vTienVAT=($vlv900-$vTienDisCount+$vCostShip)*$vVAT/100;
			$vlv900 = $vlv900 + $vTienVAT + $vCostShip - $vTienDisCount;
			$this->mosl_lv0324->LV_LoadLanGiaoHang($vContractID, $vDot);
			if ($this->mosl_lv0324->lv001 != null) {
				$vPercent = $this->mosl_lv0324->lv003;
				//Tiền VAT
				$vVAT = $vTienVAT;
				//Tổng tiền sau thuế
				$vTongTien = $vlv900;
				$vSoTienThanhToan = 0;
				if ($this->mosl_lv0324->lv012 > 0) {
					$vSoTienThanhToan = $vSoTienThanhToan + $this->mosl_lv0324->lv012;
				} else {
					if ($this->mosl_lv0324->lv003 > 0) {
						$vSoTienThanhToan = $vSoTienThanhToan + $this->mosl_lv0324->lv003 * ($vTongTien - $vVAT) / 100;
					}
				}
				if ($this->mosl_lv0324->lv013 > 0) {
					$vSoTienThanhToan = $vSoTienThanhToan + $this->mosl_lv0324->lv013;
				} else {

					if ($this->mosl_lv0324->lv011 > 0 && $vVAT > 0) {
						$vSoTienThanhToan = $vSoTienThanhToan + $this->mosl_lv0324->lv011 * $vVAT / 100;
					}
				}
				return $vSoTienThanhToan;
			}
			return $vlv900;
		}
	}
	function LV_GetTienHopDongPT($vContractID, $vDot = '', $vTKNo = '', $vQuiDoi)
	{
		if ($vQuiDoi == 0) $vQuiDoi = 1;
		if ($vDot == '0') {
			$vTKCo = '1311';
		} else {
			$vTKCo = '1312';
		}
		$lvsql = "DELETE FROM ac_lv0076  WHERE ac_lv0076.lv002='$this->LV_UserID'";
		$vReturn = db_query($lvsql);
		if ($vDot == '') {
			$lvsql = "select B.lv108,B.lv109,B.lv110 from sl_lv0013 B where B.lv001='$vContractID'";
			$vresult = db_query($lvsql);
			$vrow = db_fetch_array($vresult);
			$vlv900 = $this->LV_LoadAmountDetail($vContractID);
			$vDiscount = $vrow['lv108'];
			$vTienDisCount = $vlv900 * $vDiscount / 100;
			$vCostShip = (float)$vrow['lv109'];
			$vVAT = (float)$vrow['lv110'];
			$vTienVAT = ($vlv900 - $vTienDisCount) * $vVAT / 100; //$vTienVAT=($vlv900-$vTienDisCount+$vCostShip)*$vVAT/100;
			$vlv900 = $vlv900 + $vTienVAT + $vCostShip - $vTienDisCount;
			$lvsql = "insert into ac_lv0076(lv002,lv003,lv004,lv005,lv006,lv007) select '$this->LV_UserID' lv002,'$vlv900' lv003,($vlv900*$vQuiDoi) lv004,'$vTKNo' lv005,'$vTKCo' lv006,lv002 lv007 from ac_lv0002 where lv001='131'";
			$vReturn = db_query($lvsql);
			return $vlv900;
		} else {
			$lvsql = "select B.lv108,B.lv109,B.lv110 from sl_lv0013 B where B.lv001='$vContractID'";
			$vresult = db_query($lvsql);
			$vrow = db_fetch_array($vresult);
			$vlv900 = $this->LV_LoadAmountDetail($vContractID);
			$vDiscount = $vrow['lv108'];
			$vTienDisCount = $vlv900 * $vDiscount / 100;
			$vCostShip = (float)$vrow['lv109'];
			$vVAT = (float)$vrow['lv110'];
			$vTienVAT = ($vlv900 - $vTienDisCount) * $vVAT / 100; //$vTienVAT=($vlv900-$vTienDisCount+$vCostShip)*$vVAT/100;
			$vlv900 = $vlv900 + $vTienVAT + $vCostShip - $vTienDisCount;
			$this->mosl_lv0324->LV_LoadLanGiaoHang($vContractID, $vDot);

			if ($this->mosl_lv0324->lv001 != null) {
				$vPercent = $this->mosl_lv0324->lv003;
				//Tiền VAT
				$vVAT = $vTienVAT;
				//Tổng tiền sau thuế
				$vTongTien = $vlv900;
				$vSoTienThanhToan = 0;
				if ($this->mosl_lv0324->lv012 > 0) {
					$vSoTienTinh = $this->mosl_lv0324->lv012;
					$lvsql = "insert into ac_lv0076(lv002,lv003,lv004,lv005,lv006,lv007) select '$this->LV_UserID' lv002,'" . $vSoTienTinh . "' lv003," . ($vSoTienTinh * $vQuiDoi) . " lv004,'$vTKNo' lv005,'$vTKCo' lv006,lv002 lv007 from ac_lv0002 where lv001='$vTKCo'";
					$vReturn = db_query($lvsql);
					$vSoTienThanhToan = $vSoTienThanhToan + $this->mosl_lv0324->lv012;
				} else {
					if ($this->mosl_lv0324->lv003 > 0) {
						$vSoTienTinh = $this->mosl_lv0324->lv003 * ($vTongTien - $vVAT) / 100;
						$lvsql = "insert into ac_lv0076(lv002,lv003,lv004,lv005,lv006,lv007) select '$this->LV_UserID' lv002,'" . $vSoTienTinh . "' lv003," . ($vSoTienTinh * $vQuiDoi) . " lv004,'$vTKNo' lv005,'$vTKCo' lv006,lv002 lv007 from ac_lv0002 where lv001='$vTKCo'";
						$vReturn = db_query($lvsql);
						$vSoTienThanhToan = $vSoTienThanhToan + $this->mosl_lv0324->lv003 * ($vTongTien - $vVAT) / 100;
					}
				}
				if ($this->mosl_lv0324->lv013 > 0) {
					$vSoTienTinh = $this->mosl_lv0324->lv013;
					$vTKCo = '1331';
					$lvsql = "insert into ac_lv0076(lv002,lv003,lv004,lv005,lv006,lv007) select '$this->LV_UserID' lv002,'" . $vSoTienTinh . "' lv003," . ($vSoTienTinh * $vQuiDoi) . " lv004,'$vTKNo' lv005,'$vTKCo' lv006,lv002 lv007 from ac_lv0002 where lv001='$vTKCo'";
					$vReturn = db_query($lvsql);
					$vSoTienThanhToan = $vSoTienThanhToan + $this->mosl_lv0324->lv013;
				} else {

					if ($this->mosl_lv0324->lv011 > 0 && $vVAT > 0) {
						$vSoTienTinh = $this->mosl_lv0324->lv011 * $vVAT / 100;
						$vTKCo = '1331';
						$lvsql = "insert into ac_lv0076(lv002,lv003,lv004,lv005,lv006,lv007) select '$this->LV_UserID' lv002,'" . $vSoTienTinh . "' lv003," . ($vSoTienTinh * $vQuiDoi) . " lv004,'$vTKNo' lv005,'$vTKCo' lv006,lv002 lv007 from ac_lv0002 where lv001='$vTKCo'";
						$vReturn = db_query($lvsql);
						$vSoTienThanhToan = $vSoTienThanhToan + $this->mosl_lv0324->lv011 * $vVAT / 100;
					}
				}

				return $vSoTienThanhToan;
			}
			$lvsql = "insert into ac_lv0076(lv002,lv003,lv004,lv005,lv006,lv007) select '$this->LV_UserID' lv002,'$vlv900' lv003,($vlv900*$vQuiDoi) lv004,'$vTKNo' lv005,'131' lv006,lv002 lv007 from ac_lv0002 where lv001='131'";
			$vReturn = db_query($lvsql);
			return $vlv900;
		}
	}
	function LV_GetBH_Invoice($vDetailID)
	{
		$vReturn = array();
		$lvsql = "select B.lv001,B.lv006,B.lv007 from sl_lv0014 A inner join sl_lv0013 B on A.lv002=B.lv001 where B.lv001 in (select BB.lv002 from sl_lv0014 BB where BB.lv001='$vDetailID')";
		$vresult = db_query($lvsql);
		$vrow = db_fetch_array($vresult);
		$vReturn[0] = $vrow['lv001'];
		$vReturn[1] = $vrow['lv007'];
		$vReturn[2] = $this->LV_GetContractMoneyConLai($vReturn[0], $vrow['lv006']);
		$vReturn[3] = $this->LV_GetContractMoney($vReturn[0], $vrow['lv006']);
		return $vReturn;
	}
	function LV_ExistTemp($vUserID)
	{
		$lvsql = "select count(*) num from sl_lv0032 where lv002='$vUserID'";
		$vresult = db_query($lvsql);
		$vrow = db_fetch_array($vresult);
		if ($vrow) {
			return $vrow['num'];
		}
		return 0;
	}
	function LV_ExistDetail($vDonHang, $vTypeRoom)
	{
		$lvsql = "select A.lv003,(select BB.lv003 from sl_lv0072 BB where BB.lv004=A.lv003 and BB.lv002=B.lv007 limit 0,1) TypeRoom from sl_lv0014 A inner join sl_lv0013 B on A.lv002=B.lv001 where A.lv002='$vDonHang'";
		$vresult = db_query($lvsql);
		$vType = false;
		while ($vrow = db_fetch_array($vresult)) {

			if ($vrow['TypeRoom'] != "") {
				if ($vTypeRoom == $vrow['TypeRoom']) $vType = true;
			}
		}
		return 0;
	}
	function LV_ExistTempDefault($vRoomID)
	{
		$lvsql = "select count(*) num from sl_lv0072 where lv002='$vRoomID'";
		$vresult = db_query($lvsql);
		$vrow = db_fetch_array($vresult);
		if ($vrow) {
			return $vrow['num'];
		}
		return 0;
	}
	function LV_RoomExist($vlv007)
	{
		$lvsql = "select lv001 from sl_lv0013 BB where BB.lv007='$vlv007' and BB.lv011=0";
		$vresult = db_query($lvsql);
		$vrow = db_fetch_array($vresult);
		if ($vrow) {
			return $vrow['lv001'];
		}
		return '';
	}
	function LV_Exist($vlv007)
	{
		$lvsql = "select lv001 from sl_lv0013 BB where BB.lv007='$vlv007' and (BB.lv011=0 or BB.lv011=1)";
		$vresult = db_query($lvsql);
		$vrow = db_fetch_array($vresult);
		if ($vrow) {
			return $vrow['lv001'];
		}
		return '';
	}
	function LV_ExistEmp($vlv010, $vOpt = 0)
	{
		$lvsql = "select lv001 from sl_lv0013 BB where BB.lv016='$vlv010' and (BB.lv011=0) and BB.lv015='$vOpt'";
		$vresult = db_query($lvsql);
		$vrow = db_fetch_array($vresult);
		if ($vrow) {
			return $vrow['lv001'];
		}
		return '';
	}
	function LV_ExistEmp1($vlv010, $vOpt = 0)
	{
		$lvsql = "select lv001 from sl_lv0013 BB where BB.lv016='$vlv010' and (BB.lv011=1) and BB.lv015='$vOpt'";
		$vresult = db_query($lvsql);
		$vrow = db_fetch_array($vresult);
		if ($vrow) {
			return $vrow['lv001'];
		}
		return '';
	}

	function LV_ProcessUpdate($vBangID, $vStartDate)
	{
		$lvsql = "select lv001 from sl_lv0013 where lv007='$vBangID' and lv004='$vStartDate'";
		$vresult = db_query($lvsql);
		$vrow = db_fetch_array($vresult);
		$vCodeId = '';
		if ($vrow) {
			$vCodeId = $vrow['lv001'];
		}
		if ($vCodeId != "" && $vCodeId != NULL) {
			//check state 
		} else {
			//Insert 
			$this->lv001 = InsertWithCheck('sl_lv0013', 'lv001', 'BH-' . getmonth($this->DateCurrent) . "/" . getyear($this->DateCurrent) . "-", 1);
			$this->lv004 = $vStartDate;
			$this->lv007 = $vBangID;
			$this->LV_InsertTemp();
		}
	}
	function LV_InsertAuto()
	{

		if ($this->isAdd == 0) return false;
		$lvsql = "insert into sl_lv0013 
		(lv002,lv003,lv004,lv005,lv006,lv007,lv008,lv009,lv010,lv011,lv012,lv013,lv014,lv015,lv016,lv017
		,lv018,lv019,lv020,lv021
		,lv022
		,lv023,lv024,lv025,lv026,lv027
		,lv028
		,lv029,lv030
		,lv101,lv102,lv103,lv104,lv105,lv106,lv107,lv108,lv109,lv110,lv111,lv112
		,lv113,lv114,lv115,lv116)	
		values
		('" . sof_escape_string($this->lv002) . "','" . sof_escape_string($this->lv003) . "','" . sof_escape_string($this->lv004) . "','" . sof_escape_string($this->lv005) . "','" . sof_escape_string($this->lv006) . "','" . sof_escape_string($this->lv007) . "','" . sof_escape_string($this->lv008) . "','" . sof_escape_string($this->lv009) . "','" . sof_escape_string($this->lv010) . "','" . sof_escape_string($this->lv011) . "','" . sof_escape_string($this->lv012) . "','" . sof_escape_string($this->lv013) . "','" . sof_escape_string($this->lv014) . "','" . sof_escape_string($this->lv015) . "','" . sof_escape_string($this->lv016) . "','" . sof_escape_string($this->lv017) . "','" . sof_escape_string($this->lv018) . "','" . sof_escape_string($this->lv019) . "','" . sof_escape_string($this->lv020) . "','" . sof_escape_string($this->lv021) . "','" . sof_escape_string($this->lv022) . "','" . sof_escape_string($this->lv023) . "','" . sof_escape_string($this->lv024) . "','" . sof_escape_string($this->lv025) . "','" . sof_escape_string($this->lv026) . "','" . sof_escape_string($this->lv027) . "','" . sof_escape_string($this->lv028) . "','" . sof_escape_string($this->lv029) . "','" . sof_escape_string($this->lv030) . "','" . sof_escape_string($this->lv101) . "','" . sof_escape_string($this->lv102) . "','" . sof_escape_string($this->lv103) . "','" . sof_escape_string($this->lv104) . "','" . sof_escape_string($this->lv105) . "','" . sof_escape_string($this->lv106) . "','" . sof_escape_string($this->lv107) . "','" . sof_escape_string($this->lv108) . "','" . sof_escape_string($this->lv109) . "','" . sof_escape_string($this->lv110) . "','" . sof_escape_string($this->lv111) . "','" . sof_escape_string($this->lv112) . "','" . sof_escape_string($this->lv113) . "','" . sof_escape_string($this->lv114) . "','" . sof_escape_string($this->lv115) . "','" . sof_escape_string($this->lv116) . "')";
		$vReturn = db_query($lvsql);
		if ($vReturn) {
			$this->insert_id = sof_insert_id();
			$this->InsertLogOperation($this->DateCurrent, 'sl_lv0013.insert', sof_escape_string($lvsql));
		}
		return $vReturn;
	}
	function LV_LoadMaOld($lv116)
	{
		$lvsql = "select lv001 from  sl_lv0013 Where lv116='$lv116'";
		$vresult = db_query($lvsql);
		$vrow = db_fetch_array($vresult);
		if ($vrow) {
			$this->lv001 = $vrow['lv001'];
		} else {
			$this->lv001 = null;
		}
		return $this->lv001;
	}
	function LV_LoadMau($vID)
	{
		if ($this->isView == 0) return false;
		$lvsql = "select lv199 from  sl_lv0013_rpt Where lv001='$vID'";
		$vresult = db_query($lvsql);
		$vrow = db_fetch_array($vresult);
		if ($vrow) {
			return $vrow['lv199'];
		}
		return '';
	}
	function LV_LoadMauNguon($vID)
	{
		if ($this->isView == 0) return false;
		$lvsql = "select lv003,lv004,lv005,lv232,lv233 from  sl_lv0016 Where lv001='$vID'";
		$vresult = db_query($lvsql);
		$vrow = db_fetch_array($vresult);
		if ($vrow) {
			$this->PBH_TenVN = $vrow['lv005'];
			$this->PBH_TenEN = $vrow['lv004'];
			if ($vrow['lv232'] == 1 || $vrow['lv232'] == 1) {
				$vIsTrue = true;
				if ($vrow['lv232'] == 1) {
					if ($vrow['lv232'] != $this->lv232) {
						$vIsTrue = false;
						$vStr = $vStr . 'Chưa check ĐÃ ĐỦ HÀNG <BR/>';
					}
				}
				if ($vrow['lv233'] == 1) {
					if ($vrow['lv233'] != $this->lv233) {
						$vIsTrue = false;
						$vStr = $vStr . 'Chưa check ĐÃ ĐỦ HỒ SƠ<BR/>';
					}
				}
				if ($vIsTrue)
					return $vrow['lv003'];
				else
					return $vStr;
			}

			return $vrow['lv003'];
		}
		return '';
	}
	function LV_UpdateMau($vID, $vStrMau)
	{
		if ($this->isAdd == 0) return false;
		$lvsql = "select lv001 from  sl_lv0013_rpt Where lv001='$vID'";
		$vresult = db_query($lvsql);
		$vrow = db_fetch_array($vresult);
		if ($vrow) {
			if ($vrow['lv001'] != '' && $vrow['lv001'] != null) {
				$vSql = "update sl_lv0013_rpt set lv199='" . sof_escape_string($vStrMau) . "' where lv001='$vID'";
			} else {
				$vSql = "insert into sl_lv0013_rpt(lv001,lv199) values('$vID','" . sof_escape_string($vStrMau) . "')";
			}
		} else {
			$vSql = "insert into sl_lv0013_rpt(lv001,lv199) values('$vID','" . sof_escape_string($vStrMau) . "')";
		}
		$vresult = db_query($vSql);
		return $vresult;
	}


	function LV_GetDefaultList()
	{
		$link = sqlsrv_connect($this->Server, $this->connectionOptions);
		if (!$link) {
			print_r(sqlsrv_errors());
			return;
		}
		$lvsql = "SELECT  [GRP_ID] lv099
		,[GRP_CODE] lv001
		FROM [ContractManagement].[dbo].[DEPTGROUP]";
		$vresult = sqlsrv_query($link, $lvsql);
		$i = 0;
		while ($vrow = sqlsrv_fetch_array($vresult)) {
			$this->LV_GroupList[$vrow['lv099']] = $vrow['lv001'];
		}

		$lvsql = "SELECT [BUDGET_ID] lv099
      ,[BUDGET_CODE] lv001
  FROM [ContractManagement].[dbo].[BUDGET_CODE]";
		$vresult = sqlsrv_query($link, $lvsql);
		$i = 0;
		while ($vrow = sqlsrv_fetch_array($vresult)) {
			$this->LV_BudgetList[$vrow['lv099']] = $vrow['lv001'];
		}
		$lvsql = "SELECT [USERS_ID] lv099
		,[USERS_CODE] lv001
	FROM [ContractManagement].[dbo].[USERS]
   where 1=1 $vCondition";
		$vresult = sqlsrv_query($link, $lvsql);
		$i = 0;
		while ($vrow = sqlsrv_fetch_array($vresult)) {
			$this->LV_UserList[$vrow['lv099']] = $vrow['lv001'];
		}

		$lvsql = "SELECT  [AuthorizationID] lv099
		,[Authorizer] lv001
	FROM [ContractManagement].[dbo].[AuthorizationList]
   where 1=1 $vCondition";
		$vresult = sqlsrv_query($link, $lvsql);
		$i = 0;
		while ($vrow = sqlsrv_fetch_array($vresult)) {
			$this->LV_AuthorizationList[$vrow['lv099']] = $vrow['lv001'];
		}
	}
	function LV_GetDataAuto()
	{
		$this->LV_GetDefaultList();
		//Tạo lệnh sản xuất từ SAP.
		//Điều kiện sản xuất
		//Biểu diễn lệnh sản xuất, Mã Lệnh, Mã Lệnh SAP, 

		$link = sqlsrv_connect($this->Server, $this->connectionOptions);
		if (!$link) {
			print_r(sqlsrv_errors());
			return;
		}
		$vCondition = "";
		$lvsql = "SELECT  [PR_ID] lv116,[TitlePR] lv003,CONVERT(VARCHAR(20),[PR_DATE],120) lv004,CONVERT(VARCHAR(20),[PR_REQUIRED_DATE],120) lv005,[PR_Type] lv007,[DeliveryLocation] lv009,[USERS_ID] lv010,[PR_STATUS] lv011,[Amendment] lv012,[TestCert] lv013,[PR_NO] lv014,[PR_REVISION_NO] lv015,[OPERATOR_ID] lv016,[BUDGET_ID] lv017,[ReceivedID] lv018,CONVERT(VARCHAR(20),[PR_RECEIVED_DATE],120) lv019,[SignedPersonID] lv020,[Createdby] lv023,[PR_PRIORITY] lv024,[Long_TermID] lv026,[ISLOCK] lv027,[Title1] lv028,[PR_REMARK] lv029,[GRP_ID] lv101,[PR_ARFNO] lv102,[Title2] lv103,[PR_EMAIL_SENDER] lv104,CONVERT(VARCHAR(20),[PR_EMAIL_DATE],120) lv106,CONVERT(VARCHAR(20),[dPREdit_NO],120) lv107,[BudgetAC] lv109,[ApprovedRFA] lv110,CONVERT(VARCHAR(20),[PR_CONTRACT_SIGNED_DATE],120) lv111,[POREF_NO] lv112,[UPREdit_ID] lv114,[PR_INTENDED_CONTRACT_NO] lv115,[PR_DESC] lv113 FROM [ContractManagement].[dbo].[PR] where 1=1 $vCondition";
		$vresult = sqlsrv_query($link, $lvsql);
		$i = 0;
		while ($vrow = sqlsrv_fetch_array($vresult)) {
			$vLSXID = $this->LV_LoadMaOld($vrow['lv116']);
			if ($vLSXID == null) {
				$this->lv001 = $vrow['lv001'];
				$this->lv002 = $vrow['lv002'];
				$this->lv003 = $vrow['lv003'];
				$this->lv004 = $vrow['lv004'];
				$this->lv005 = $vrow['lv005'];
				$this->lv006 = $vrow['lv006'];
				$this->lv007 = $vrow['lv007'];
				$this->lv008 = $vrow['lv008'];
				$this->lv009 = $vrow['lv009'];
				$this->lv010 = $this->LV_UserList[$vrow['lv010']];
				$this->lv011 = $vrow['lv011'];
				$this->lv012 = $vrow['lv012'];
				$this->lv013 = $vrow['lv013'];
				$this->lv014 = $vrow['lv014'];
				$this->lv015 = $vrow['lv015'];
				$this->lv016 = $this->LV_UserList[$vrow['lv016']];
				$this->lv017 = $this->LV_BudgetList[$vrow['lv017']];
				$this->lv018 = $this->LV_AuthorizationList[$vrow['lv018']];
				$this->lv019 = $vrow['lv019'];
				$this->lv020 = $this->LV_AuthorizationList[$vrow['lv020']];
				$this->lv021 = $vrow['lv021'];
				$this->lv022 = $vrow['lv022'];
				$this->lv029 = $vrow['lv029'];
				$this->lv023 = $vrow['lv023'];
				$this->lv099 = $vrow['lv099'];

				$this->lv101 = $this->LV_GroupList[$vrow['lv101']];
				$this->lv102 = $vrow['lv102'];
				$this->lv103 = $vrow['lv103'];
				$this->lv104 = $vrow['lv104'];
				$this->lv105 = $vrow['lv105'];
				$this->lv106 = $vrow['lv106'];
				$this->lv107 = $vrow['lv107'];
				$this->lv108 = $vrow['lv108'];
				$this->lv109 = $vrow['lv109'];
				$this->lv110 = $vrow['lv110'];
				$this->lv111 = $vrow['lv111'];
				$this->lv112 = $vrow['lv112'];
				$this->lv113 = $vrow['lv113'];
				$this->lv114 = $vrow['lv114'];
				$this->lv115 = $vrow['lv115'];
				$this->lv116 = $vrow['lv116'];
				$this->lv214 = $vrow['lv214'];
				$this->lv212 = $vrow['lv212'];
				$this->lv224 = $vrow['lv224'];
				$this->lv225 = $vrow['lv225'];
				$this->lv226 = $vrow['lv226'];
				$this->lv227 = $vrow['lv227'];
				$this->lv228 = $vrow['lv228'];
				$this->lv228 = $vrow['lv228'];
				$this->lv230 = $vrow['lv230'];
				$this->lv231 = $vrow['lv231'];
				$this->lv232 = $vrow['lv232'];
				$this->lv233 = $vrow['lv233'];
				$this->lv394 = $vrow['lv394'];
				$this->lv395 = $vrow['lv395'];
				$this->lv396 = $vrow['lv396'];
				$this->lv397 = $vrow['lv397'];
				$this->lv398 = $vrow['lv398'];
				$this->lv399 = $vrow['lv399'];
				$vResult = $this->LV_InsertAuto();
				if ($vResult) {
					$this->LV_GetChildPR($vrow['lv116'], $this->insert_id);
				}
			}
		}
		//Biễu diễn chi tiết sản xuất. 
	}
	///Xử lý chuyển dữ liệu kích hoạt kho và bảo hành.
	function LV_CapNhatThayDoiHopDong($lvarr)
	{
		$lvsql = "select lv001,lv025,lv019 from  sl_lv0013 Where lv001 IN ($lvarr)";
		$vresults = db_query($lvsql);
		while ($vrow1 = db_fetch_array($vresults)) {
			$vContractID = $vrow1['lv001'];
			//Mục tiêu là kích hoạt bảo hành và báo cáo danh thu theo dữ liệu có sẵn 
			$lvsql = "select  A.*,C.lv004 SupplierID,B.lv001 CodeID,C.lv009 OrderNo from cr_lv0276 A left join sl_lv0014 B on A.lv001=B.lv001 left join cr_lv0013 C on A.lv004=C.lv001 where A.lv002='$vContractID'";
			$vresult = db_query($lvsql);
			$vType = false;
			while ($vrow = db_fetch_array($vresult)) {
				//Biểu diễn mã kho
				$vItemWH = str_replace(' ', '', $vrow['lv008'] . $vrow['lv026'] . $vrow['lv027'] . $vrow['lv031'] . $vrow['lv020']);
				$vCodeID = $vrow['CodeID'];
				//////B1. Dò lại tất cả chi tiết hợp đồng in.
				//////B2. Xử lý thêm hoặc cập nhật dữ liệu mới.
				//////B3. Liên kết chi tiết 2 bên phải đồng bộ, không được có sự khác nhau về chi tiết
				///////Xử lý đồng bộ sản phẩm.
				$this->mosl_lv0007->LV_LoadID($vItemWH);
				if ($this->mosl_lv0007->lv001 == NULL) {
					$this->mosl_lv0007->lv001 = $vItemWH;
					$this->mosl_lv0007->lv002 = $vrow['lv008'];
					$this->mosl_lv0007->lv003 = 'TP';
					$this->mosl_lv0007->lv010 = $vrow['lv011'];
					$this->mosl_lv0007->lv016 = 'BAOHANH';
					//Nha cung cấp
					$this->mosl_lv0007->lv009 = $vrow['lv004'];
					$this->mosl_lv0007->lv013 = $vrow['SupplierID'];
					$this->mosl_lv0007->lv017 = $vrow['OrderNo'];
					$this->mosl_lv0007->LV_Insert();
				}
				if ($vCodeID > 0) {
					///Xử lý update
					$vsql1 = "update sl_lv0014 set lv003='$vItemWH',lv013='" . $vrow1['lv019'] . "',lv014=ADDDATE('" . ($vrow1['lv019']) . "','" . ($vrow1['lv025']) . "'),lv004='" . $vrow['lv051'] . "',lv005='" . $vrow['lv052'] . "',lv006='" . $vrow['lv053'] . "',lv007='VND',lv009='" . sof_escape_string($vrow['OrderNo']) . "',lv010='" . sof_escape_string($vrow['lv011']) . "',lv018='$this->LV_UserID',lv019=now(),lv020='" . sof_escape_string($vrow['lv004']) . "',lv021='" . sof_escape_string($vrow['SupplierID']) . "' where lv001='" . $vrow['lv001'] . "'";
					$vresult2 = db_query($vsql1);
					if ($vresult2) {
						$this->InsertLogOperation($this->DateCurrent, 'sl_lv0014.insert', sof_escape_string($vsql1));
					}
				} else {
					//Xử lý thêm mới.
					$vsql1 = "insert into sl_lv0014(lv001,lv013,lv014,lv002,lv003,lv004,lv005,lv006,lv007,lv009,lv010,lv018,lv019,lv020,lv021) 
					values('" . $vrow['lv001'] . "','" . $vrow1['lv019'] . "',ADDDATE('" . ($vrow1['lv019']) . "','" . ($vrow1['lv025']) . "'),'" . sof_escape_string($vrow['lv002']) . "','" . sof_escape_string($vItemWH) . "','" . sof_escape_string($vrow['lv051']) . "','" . sof_escape_string($vrow['lv052']) . "','" . sof_escape_string($vrow['lv053']) . "','VND','" . sof_escape_string($vrow['lv008']) . "','" . sof_escape_string($vrow['OrderNo']) . "','$this->LV_UserID',now(),'" . sof_escape_string($vrow['lv004']) . "','" . sof_escape_string($vrow['SupplierID']) . "');";
					$vresult2 = db_query($vsql1);
					if ($vresult2) {

						$this->InsertLogOperation($this->DateCurrent, 'sl_lv0014.insert', sof_escape_string($vsql1));
						$vsql2 = "update cr_lv0276 set lv003='$vItemWH' where lv001='" . $vrow['lv001'] . "'";
						$vresult3 = db_query($vsql2);
					}
				}
			}
		}
	}
	function LV_Insert()
	{

		if ($this->isAdd == 0) return false;
		$lvsql = "
		insert into sl_lv0013 
		(lv101,lv005,lv004,lv024,lv003,lv014,lv015,lv009,lv013,lv102,lv017,lv109,lv110,lv112,lv016,lv020,lv018,lv029,lv023,lv105) 
		values
		('$this->lv101','$this->lv005','$this->lv004','$this->lv024','$this->lv003','$this->lv014','$this->lv015','$this->lv009','$this->lv013','$this->lv102','$this->lv017','$this->lv109','$this->lv110','$this->lv112','$this->lv016','$this->lv020','$this->lv018','$this->lv029','$this->LV_UserID',now())
		";
		$vReturn = db_query($lvsql);
		if ($vReturn) {
			$this->insert_id = sof_insert_id();
			echo $this->insert_id;
			$this->InsertLogOperation($this->DateCurrent, 'sl_lv0013.insert', sof_escape_string($lvsql));
		}
		return $vReturn;
	}

	function LV_InsertXML()
	{
		if ($this->isAdd == 0) return false;
		$lvsql = "
		insert into sl_lv0013 
		(lv101,lv005,lv004,lv024,lv003,lv014,lv015,lv009,lv013,lv102,lv017,lv109,lv110,lv112,lv016,lv020,lv018,lv029,lv023,lv105) 
		values
		('$this->lv101','$this->lv005','$this->lv004','$this->lv024','$this->lv003','$this->lv014','$this->lv015','$this->lv009','$this->lv013','$this->lv102','$this->lv017','$this->lv109','$this->lv110','$this->lv112','$this->lv016','$this->lv020','$this->lv018','$this->lv029','$this->LV_UserID',now())
		";
		$vReturn = db_query($lvsql);
		if ($vReturn) $this->InsertLogOperation($this->DateCurrent, 'sl_lv0013.insert', sof_escape_string($lvsql));
		return $vReturn;
	}
	function LV_InsertBoth($oldid, $newid)
	{
		if ($this->isAdd == 0) return false;
		$lvsql = "Update sl_lv0014 set lv002='$newid'  WHERE lv002 ='" . $oldid . "'";
		$vReturn = db_query($lvsql);
		$lvsql = "Update sl_lv0013 set lv025='$newid' WHERE lv001 ='" . $oldid . "'";
		$vReturn = db_query($lvsql);
	}
	function LV_InsertTemp()
	{

		if ($this->isAdd == 0) return false;
		$this->lv004 = ($this->lv004 != "") ? recoverdate(($this->lv004), $this->lang) : $this->DateDefault;
		$this->lv005 = ($this->lv005 != "") ? recoverdate(($this->lv005), $this->lang) : $this->DateDefault;
		$lvsql = "insert into sl_lv0013 (lv001,lv002,lv003,lv004,lv005,lv006,lv007,lv008,lv009,lv010,lv011,lv012,lv013,lv014,lv015,lv016,lv017,lv022) values('$this->lv001','$this->lv002','$this->lv003','$this->lv004','$this->lv005','$this->lv006','$this->lv007','$this->lv008','$this->lv009','$this->lv010','$this->lv011','$this->lv012','$this->lv013','$this->lv014','$this->lv015','$this->lv016','$this->lv017','$this->lv022')";
		$vReturn = db_query($lvsql);
		if ($vReturn) $this->InsertLogOperation($this->DateCurrent, 'sl_lv0013.insert', sof_escape_string($lvsql));
		return $vReturn;
	}
	function LV_Update()
	{
		if ($this->isEdit == 0) return false;
		$this->lv005 = ($this->lv005 != "") ? recoverdate(($this->lv005), $this->lang) : $this->DateDefault;
		$this->lv004 = ($this->lv004 != "") ? recoverdate(($this->lv004), $this->lang) : $this->DateDefault;
		$lvsql = "Update sl_lv0013 set lv101='" . sof_escape_string($this->lv101) . "',lv005='" . sof_escape_string($this->lv005) . "',lv004='" . sof_escape_string($this->lv004) . "',lv024='" . sof_escape_string($this->lv024) . "',lv003='" . sof_escape_string($this->lv003) . "',lv014='" . sof_escape_string($this->lv014) . "',lv015='" . sof_escape_string($this->lv015) . "',lv009='" . sof_escape_string($this->lv009) . "',lv013='" . sof_escape_string($this->lv013) . "',lv102='" . sof_escape_string($this->lv102) . "',lv017='" . sof_escape_string($this->lv017) . "',lv109='" . sof_escape_string($this->lv109) . "',lv110='" . sof_escape_string($this->lv110) . "',lv112='" . sof_escape_string($this->lv112) . "',lv016='" . sof_escape_string($this->lv016) . "',lv020='" . sof_escape_string($this->lv020) . "',lv018='" . sof_escape_string($this->lv018) . "',lv029='" . sof_escape_string($this->lv029) . "'   where  lv001='$this->lv001' and lv027=0;";
		$vReturn = db_query($lvsql);
		if ($vReturn) {
			$this->LV_SetHistory('Edit', $this->lv001);
			$this->InsertLogOperation($this->DateCurrent, 'sl_lv0013.update', sof_escape_string($lvsql));
		}
		return $vReturn;
	}
	function LV_UpdateNoDate()
	{
		if ($this->isEdit == 0) return false;

		$this->lv004 = ($this->lv004 != "") ? recoverdate(($this->lv004), $this->lang) . " " . gettime($this->lv004) : $this->DateDefault;
		$this->lv005 = ($this->lv005 != "") ? recoverdate(($this->lv005), $this->lang) . " " . gettime($this->lv005) : $this->DateDefault;
		$this->lv105 = ($this->lv105 != "") ? recoverdate(($this->lv105), $this->lang) . " " . gettime($this->lv105) : $this->DateDefault;
		$this->lv106 = ($this->lv106 != "") ? recoverdate(($this->lv106), $this->lang) . " " . gettime($this->lv106) : $this->DateDefault;
		$this->lv107 = ($this->lv107 != "") ? recoverdate(($this->lv107), $this->lang) . " " . gettime($this->lv107) : $this->DateDefault;
		$this->lv111 = ($this->lv111 != "") ? recoverdate(($this->lv111), $this->lang) . " " . gettime($this->lv111) : $this->DateDefault;
		$vListChuKyFix = $this->LV_GetChuKyDaFix();
		$lvsql = "Update sl_lv0013 set lv101='" . sof_escape_string($this->lv101) . "',lv005='" . sof_escape_string($this->lv005) . "',lv004='" . sof_escape_string($this->lv004) . "',lv024='" . sof_escape_string($this->lv024) . "',lv003='" . sof_escape_string($this->lv003) . "',lv014='" . sof_escape_string($this->lv014) . "',lv015='" . sof_escape_string($this->lv015) . "',lv009='" . sof_escape_string($this->lv009) . "',lv013='" . sof_escape_string($this->lv013) . "',lv102='" . sof_escape_string($this->lv102) . "',lv017='" . sof_escape_string($this->lv017) . "',lv109='" . sof_escape_string($this->lv109) . "',lv110='" . sof_escape_string($this->lv110) . "',lv112='" . sof_escape_string($this->lv112) . "',lv016='" . sof_escape_string($this->lv016) . "',lv020='" . sof_escape_string($this->lv020) . "',lv018='" . sof_escape_string($this->lv018) . "',lv029='" . sof_escape_string($this->lv029) . "'   where  lv001='$this->lv001' and lv027=0;";
		$vReturn = db_query($lvsql);
		if ($vReturn) $this->InsertLogOperation($this->DateCurrent, 'sl_lv0013.update', sof_escape_string($lvsql));
		return $vReturn;
	}
	function LV_Delete($lvarr)
	{
		if ($this->isDel == 0) return false;
		$lvsql = "delete from sl_lv0013 WHERE  lv001 IN ($lvarr) and lv027=0";
		$vReturn = db_query($lvsql);
		if ($vReturn) $this->InsertLogOperation($this->DateCurrent, 'sl_lv0013.delete', sof_escape_string($lvsql));
		return $vReturn;
	}
	function LV_DeleteKhac($lvarr)
	{
		if ($this->isDel == 0) return false;
		$vListChuKyFix = $this->LV_GetChuKyDaFix();
		$lvsql = "Update sl_lv0013 set lv011=-2,lv018='$this->LV_UserID',lv019=concat(CURDATE(),' ',CURTIME())  WHERE sl_lv0013.lv001 IN ($lvarr) and  sl_lv0013.lv011<=4 and (lv017 not in ($vListChuKyFix) or ISNULL(lv017));";
		$vReturn = db_query($lvsql);
		if ($vReturn) $this->InsertLogOperation($this->DateCurrent, 'sl_lv0013.delete', sof_escape_string($lvsql));
		return $vReturn;
	}

	//Lay những kỳ đã fix
	function LV_GetChuKyDaFix()
	{
		if ($this->ArrFixChuKy[1]) return $this->ArrFixChuKy[0];
		$vReturn = "";
		$vsql = "select lv001 from  hr_lv0111 where lv011='3' and lv001!='DEFAULT'";
		$vresult = db_query($vsql);
		while ($vrow = db_fetch_array($vresult)) {
			if ($vReturn == "")
				$vReturn = "'" . $vrow['lv001'] . "'";
			else
				$vReturn = $vReturn . ",'" . $vrow['lv001'] . "'";
		}
		$this->ArrFixChuKy[0] = $vReturn;
		$this->ArrFixChuKy[1] = true;
		return $this->ArrFixChuKy[0];
	}

	function LV_SendMessageContract($lvarr)
	{
		if ($this->isApr == 0) return false;
		$vNoiDung = $this->molv_lv0007->moml_lv0013->lv003;
		$sqlS = "SELECT A.lv010,B.lv002 TenTV,A.lv112,A.lv103 FROM sl_lv0013 A inner join hr_lv0020 B on A.lv010=B.lv001 WHERE A.lv001 in ($lvarr)";
		$bResult = db_query($sqlS);
		while ($vrow = db_fetch_array($bResult)) {

			$vNoiDungGui = str_replace("{1}", $vrow['lv010'], $vNoiDung);
			$vNoiDungGui = str_replace("{2}", $vrow['TenTV'], $vNoiDungGui);
			$vNoiDungGui = str_replace("{3}", $vrow['lv112'], $vNoiDungGui);
			$vNoiDungGui = str_replace("{4}", $vrow['lv103'], $vNoiDungGui);
			$this->molv_lv0007->moml_lv0013->lv003 = $vNoiDungGui;
			$vresult = $this->molv_lv0007->LV_SendMailAll("'" . $vrow['lv010'] . "'");
		}
		return;
	}
	//Tự động cập nhật đơn hàng hoàn thành
	function LV_AutoRunUpdateChuKy($vChuKyTinh, $vNgayTinh)
	{
		if ($this->isApr == 0) return false;
		$vListChuKyFix = $this->LV_GetChuKyDaFix();
		$lvsql = "Update sl_lv0013 set lv111=ADDDATE(lv107,21),lv017='$vChuKyTinh',lv011=4,lv018='$this->LV_UserID',lv019=concat(CURDATE(),' ',CURTIME())  WHERE ADDDATE(lv107,21)='$vNgayTinh' and ((lv104='Nhân thọ' and lv011=3)) and (lv017 not in ($vListChuKyFix) or ISNULL(lv017))";
		//$lvsql = "Update sl_lv0013 set lv111=ADDDATE(lv107,IF(lv104='Phi nhân thọ',0,22)),lv017='$vChuKyTinh',lv011=4,lv018='$this->LV_UserID',lv019=concat(CURDATE(),' ',CURTIME())  WHERE ADDDATE(lv107,IF(lv104='Phi nhân thọ',0,22))='$vNgayTinh' and ((lv104='Nhân thọ' and lv011=3) or (lv104='Phi nhân thọ' and lv011 in (1,2,3))) and (lv017 not in ($vListChuKyFix) or ISNULL(lv017))";
		$vReturn = db_query($lvsql);
		if ($vReturn) $this->InsertLogOperation($this->DateCurrent, 'sl_lv0013.approval', sof_escape_string($lvsql));
		//if($vNgayTinh<='2021-01-10')
		/*{
			$lvsql = "Update sl_lv0013 set lv111=ADDDATE(lv107,IF(lv104='Phi nhân thọ',0,22)),lv017='$vChuKyTinh',lv018='$this->LV_UserID',lv019=concat(CURDATE(),' ',CURTIME())  WHERE (lv017='' or ISNULL(lv017)) and ADDDATE(lv107,IF(lv104='Phi nhân thọ',0,22))='$vNgayTinh' and ((lv104='Nhân thọ' and lv011=4) or (lv104='Phi nhân thọ' and lv011 in (4)))  and (lv017 not in ($vListChuKyFix) or ISNULL(lv017))";
			//$lvsql = "Update sl_lv0013 set lv111=ADDDATE(lv107,IF(lv104='Phi nhân thọ',0,22)),lv017='$vChuKyTinh',lv018='$this->LV_UserID',lv019=concat(CURDATE(),' ',CURTIME())  WHERE  ADDDATE(lv107,IF(lv104='Phi nhân thọ',0,22))='$vNgayTinh' and ((lv104='Nhân thọ' and lv011=4) or (lv104='Phi nhân thọ' and lv011 in (4))) ";
			$vReturn= db_query($lvsql);
			if($vReturn) $this->InsertLogOperation($this->DateCurrent,'sl_lv0013.approval',sof_escape_string($lvsql));
		}*/
		return $vReturn;
	}
	//Tự động cập nhật đơn hàng hoàn thành
	function LV_AutoComplete($vListContractID = '')
	{
		if ($this->isApr == 0) return false;
		$vListChuKyFix = $this->LV_GetChuKyDaFix();
		if ($vListStaffID != "") {
			$lvsql = "Update sl_lv0013 set lv111=ADDDATE(lv107,IF(lv104='Phi nhân thọ',0,21)),lv011=4,lv018='$this->LV_UserID',lv019=concat(CURDATE(),' ',CURTIME())  WHERE sl_lv0013.lv001 IN ($vListContractID) and ADDDATE(lv107,IF(lv104='Phi nhân thọ',0,21))<=CurDate() and (lv011<=3 and lv011>=0) and (lv017 not in ($vListChuKyFix) or ISNULL(lv017))";
			$vReturn = db_query($lvsql);
		} else {
			$lvsql = "Update sl_lv0013 set lv111=ADDDATE(lv107,IF(lv104='Phi nhân thọ',0,21)),lv011=4,lv018='$this->LV_UserID',lv019=concat(CURDATE(),' ',CURTIME())  WHERE  ADDDATE(lv107,IF(lv104='Phi nhân thọ',0,21))<=CurDate() and (lv011<=3 and lv011>=0) and (lv017 not in ($vListChuKyFix) or ISNULL(lv017))";
			$vReturn = db_query($lvsql);
		}

		if ($vReturn) $this->InsertLogOperation($this->DateCurrent, 'sl_lv0013.approval', sof_escape_string($lvsql));
		return $vReturn;
	}

	function LV_ChuongTrinhKinhDoanh($vDateCal)
	{
		if ($this->ProgKinhDoanhDetail[$vDateCal][0][0]) return;
		$this->LV_GetSanPhamChiNhanh();
		$lvsql = "select A.* from  sl_lv0059 A  where A.lv008>=1 and (date(A.lv003)<='$vDateCal' and date(A.lv004)>='$vDateCal')";
		$vresult = db_query($lvsql);
		while ($vrow = db_fetch_array($vresult)) {
			$this->ProgKinhDoanh[$vDateCal][$vrow['lv099']][9] = $vrow['lv009'];
			$this->ProgKinhDoanh[$vDateCal][$vrow['lv099']][99] = $vrow['lv099'];
			$vProgID = $vrow['lv001'];
			$lvsql = "select A.* from  sl_lv0060 A  where A.lv002='$vProgID'";
			$vresult1 = db_query($lvsql);
			while ($vrow1 = db_fetch_array($vresult1)) {
				$vItemID1 = $vrow1['lv003'];
				$this->ProgKinhDoanhDetail[$vDateCal][$vItemID1][4] = $vrow1['lv004'];
				if ($vrow1['lv005'] > 0) $this->ProgKinhDoanhDetail[$vDateCal][$vItemID1][5] = $vrow1['lv005'];
				if ($vrow1['lv006'] > 0) $this->ProgKinhDoanhDetail[$vDateCal][$vItemID1][6] = $vrow1['lv006'];
				$this->ProgKinhDoanhDetail[$vDateCal][$vItemID1][99] = $vrow['lv099'];
				foreach ($this->ArrSanPhamSS[$vItemID1] as $vItemID) {
					$this->ProgKinhDoanhDetail[$vDateCal][$vItemID][4] = $vrow1['lv004'];
					if ($vrow1['lv005'] > 0) $this->ProgKinhDoanhDetail[$vDateCal][$vItemID][5] = $vrow1['lv005'];
					if ($vrow1['lv006'] > 0) $this->ProgKinhDoanhDetail[$vDateCal][$vItemID][6] = $vrow1['lv006'];
					$this->ProgKinhDoanhDetail[$vDateCal][$vItemID][99] = $vrow['lv099'];
					$vItemKhac = $this->ArrSanPham[$vItemID][1];
					if ($vItemKhac == null) {
						$vItemKhac = $this->ArrSanPham[$vItemID][111];
					}
					$this->ProgKinhDoanhDetail[$vDateCal][$vItemKhac][4] = $vrow1['lv004'];
					if ($vrow1['lv005'] > 0) $this->ProgKinhDoanhDetail[$vDateCal][$vItemKhac][5] = $vrow1['lv005'];
					if ($vrow1['lv006'] > 0) $this->ProgKinhDoanhDetail[$vDateCal][$vItemKhac][6] = $vrow1['lv006'];
					$this->ProgKinhDoanhDetail[$vDateCal][$vItemKhac][99] = $vrow['lv099'];
				}
			}
		}
		$this->ProgKinhDoanhDetail[$vDateCal][0][0] = true;
	}

	function LV_GetSanPhamChiNhanh()
	{
		if ($this->ArrSanPham[0][0]) return;
		$lvsql = "select A.lv001 CodeID,B.lv001,B.lv003,B.lv006,B.lv009 from mn_lv0005 A inner join sl_lv0007 B on B.lv001=A.lv002"; // where A.lv001='UNG_THU_VU_1'";	
		$vresult = db_query($lvsql);
		while ($vrow = db_fetch_array($vresult)) {
			//Bang San Pham
			$this->ArrSanPham[$vrow['lv001']][3] = $vrow['lv003'];
			$this->ArrSanPham[$vrow['lv001']][6] = $vrow['lv006'];
			$this->ArrSanPham[$vrow['lv001']][9] = $vrow['lv009'];
			$this->ArrSanPham[$vrow['lv001']][1] = $vrow['CodeID'];
			$this->ArrSanPham[$vrow['CodeID']][3] = $vrow['lv003'];
			$this->ArrSanPham[$vrow['CodeID']][6] = $vrow['lv006'];
			$this->ArrSanPham[$vrow['CodeID']][9] = $vrow['lv009'];

			$this->ArrSanPhamSS[$vrow['lv001']][$vrow['CodeID']] = $vrow['CodeID'];
		}
		//print_r($this->ArrSanPham);
		$this->ArrSanPham[0][0] = true;
	}
	//Lấy tên sản  phẩm
	function LV_Get_NameItem($vContractID)
	{
		$vStrReturn = '';
		$sqlS = "SELECT B.lv002 TenSP FROM sl_lv0014 A  left join sl_lv0007 B on A.lv003=B.lv001 WHERE A.lv002='" . $vContractID . "'";
		$bResult = db_query($sqlS);
		while ($vrow = db_fetch_array($bResult)) {
			if ($vStrReturn == '')
				$vStrReturn = $vrow['TenSP'];
			else
				$vStrReturn = $vStrReturn . "," . $vrow['TenSP'];
		}
		return $vStrReturn;
	}
	//Xử lý chi tiết
	function LV_CheckToUpdate($vContractID)
	{
		$this->LV_GetSanPhamChiNhanh();
		$vArrView = array('nhantho' => 0, 'tongphi' => 0, 'tongdiem' => 0, 'tongdiemmoi' => 0, 'tongtientuvan' => 0);
		$sqlS = "SELECT A.lv001,A.lv003,A.lv006,A.lv012,B.lv013 CongTy,B.lv003 LoaiBaoHiem,B.lv009 DinhMucPhi,B.lv001 CodeID,AA.lv104 BaoHiem,AA.lv103,AA.lv105,AA.lv011 StateHD FROM sl_lv0014 A inner join sl_lv0013 AA on  A.lv002=AA.lv001 left join sl_lv0007 B on A.lv003=B.lv001 WHERE A.lv002='" . $vContractID . "'";
		$bResult = db_query($sqlS);
		while ($vrow = db_fetch_array($bResult)) {
			$vLineOne = $vTr;
			$vOrder++;
			if ($vrow['CodeID'] == null) {
				$vrow['DinhMucPhi'] = $this->ArrSanPham[$vrow['lv003']][9];
			}
			//Ngay tính
			$vDateCal = $vrow['lv105'];
			//print_r($this->ProgKinhDoanhDetail[$vDateCal]);
			$vQuiDoiProg = $this->ProgKinhDoanhDetail[$vDateCal][$vrow['lv003']][6];
			$vDinhMucPhiProg = $this->ProgKinhDoanhDetail[$vDateCal][$vrow['lv003']][5];
			$vPhiBH = round($vrow['lv006'], 0);
			if (($vQuiDoiProg > 0 || $vDinhMucPhiProg > 0) && $vrow['StateHD'] <= 4) {
				$vPFYPProg = $vPhiBH / $vQuiDoiProg;
				$vsql = "update sl_lv0014 t1 inner join sl_lv0013 t2 on t1.lv002=t2.lv001 set t1.lv011='" . $vQuiDoiProg . "',t1.lv012='" . $vPFYPProg . "'  where t1.lv001='" . $vrow['lv001'] . "' and t2.lv011<=4";
				if (db_query($vsql)) {
					$vrow['lv012'] = $vPFYPProg;
				}
			}
			$vPFYP = round($vrow['lv012'], 0);
			$sPFYP = $sPFYP + $vPFYP;
			$vLoaiBaoHiem = $vrow['LoaiBaoHiem'];

			if ($vLoaiBaoHiem == 'Nhân thọ') {
				$vArrView['nhantho'] = $vArrView['nhantho'] + $vPFYP;
			} else {
				if ($vrow['BaoHiem'] == 'Nhân thọ') {
					if ($vDinhMucPhiProg > 0)
						$vPhiBHTuVan = $vPFYP * $vDinhMucPhiProg / 100;
					else
						$vPhiBHTuVan = $vPFYP * $vrow['DinhMucPhi'] / 100;
				} else {
					if ($vDinhMucPhiProg > 0) {
						//echo "$vPhiBH*$vDinhMucPhiProg/100=";
						$vPhiBHTuVan = $vPhiBH * $vDinhMucPhiProg / 100;
					} else
						$vPhiBHTuVan = $vPhiBH * $vrow['DinhMucPhi'] / 100;
				}
				if ($vPhiBHTuVan > 0) $vArrView['tongtientuvan'] = $vArrView['tongtientuvan'] + $vPhiBHTuVan;
			}
			//NCC

			$vNCC = $vrow['lv103'];
			$vTyLeDinhMucPhi = $this->ProgKinhDoanhDetail[$vDateCal][$vrow['lv003']][4];
			if ($vTyLeDinhMucPhi > 0) {
				$vPFYPMoi = $vrow['lv006'] / $vTyLeDinhMucPhi;
				$vArrView['tongdiemmoi'] = $vArrView['tongdiemmoi'] + $vPFYPMoi;
			} else
				$vArrView['tongdiemmoi'] = $vArrView['tongdiemmoi'] + $vPFYP;
			$vArrView['tongdiem'] = $vArrView['tongdiem'] + $vPFYP;
			$vArrView['tongphi'] = $vArrView['tongphi'] + $vPhiBH;
			if ($vrow['CongTy'] != '' && $vrow['CongTy'] != null) $vArrView['congty'] = $vrow['CongTy'];
		}
		return $vArrView;
	}
	function LV_Aproval($lvarr)
	{
		if ($this->isApr == 0) return false;
		$lvsql = "Update sl_lv0013 set lv011=1,lv027=1,lv034=0,lv394='$this->LV_UserID',lv395=now() WHERE sl_lv0013.lv001 IN ($lvarr) and lv011=0";
		$vReturn = db_query($lvsql);
		if ($vReturn) {
			$this->InsertLogOperation($this->DateCurrent, 'sl_lv0013.approval', sof_escape_string($lvsql));
			$this->LV_SetHistoryArr('Apr', $lvarr);
		}
		return $vReturn;
	}
	function LV_UnAproval($lvarr)
	{
		if ($this->isUnApr == 0) return false;
		$lvsql = "Update sl_lv0013 set lv011=0,lv027=-1 WHERE sl_lv0013.lv001 IN ($lvarr) and lv011=0";
		$vReturn = db_query($lvsql);
		if ($vReturn) {
			$this->InsertLogOperation($this->DateCurrent, 'sl_lv0013.unapproval', sof_escape_string($lvsql));
			$this->LV_SetHistoryArr('UnApr', $lvarr);
		}
		return $vReturn;
	}
	function LV_UpdateChuKyTinh($lvarr, $vChuKyTinh)
	{
		if ($this->isApr == 0) return false;
		$vListChuKyFix = $this->LV_GetChuKyDaFix();
		if (strpos($vListChuKyFix, $vChuKyTinh) === false) {
			$lvsql = "Update sl_lv0013 set lv017='$vChuKyTinh'  WHERE sl_lv0013.lv001 IN ($lvarr)  and (lv017 not in ($vListChuKyFix) or ISNULL(lv017))";
			//$lvsql = "Update sl_lv0013 set lv017='$vChuKyTinh',lv011=4  WHERE sl_lv0013.lv001 IN ($lvarr)  ";
			$vReturn = db_query($lvsql);
			if ($vReturn) $this->InsertLogOperation($this->DateCurrent, 'sl_lv0013.update', sof_escape_string($lvsql));
			return $vReturn;
		} else {
			echo '<font color=red>Chu kỳ ' . $vChuKyTinh . ' đã bị khoá!,Không cho cập nhật';
			return;
		}
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
				$vTitle = "PBH đề xuất lên quản lý duyệt!";
				break;
			case 'UnApr':
				$vTitle = "PBH thì bị huỷ!";
				break;
			case 'Edit':
				$vTitle = "PBH đang sửa!";
				break;
			case 'Add':
				$vTitle = "PBH đang tạo!";
				break;
			default:
				break;
		}
		//sl_lv0329
		if ($vTitle != '') {
			$lvsql = "insert into sl_lv0329 (lv002,lv003,lv004,lv005,lv006,lv007) values('" . $vLongTermID . "','" . sof_escape_string($vTitle) . "','$this->LV_UserID',now(),'$vFun',0)";
			$vReturn = db_query($lvsql);
			if ($vReturn) {
				$this->InsertLogOperation($this->DateCurrent, 'sl_lv0329.insert', sof_escape_string($lvsql));
				if ($vFun == 'UnApr') {
					$lvsql = "update sl_lv0329 set lv008=lv008+1 where lv002='$vLongTermID'";
					$vReturn = db_query($lvsql);
					if ($vReturn) {
						$this->InsertLogOperation($this->DateCurrent, 'sl_lv0329.update', sof_escape_string($lvsql));
					}
				}
			}
		}
	}
	function LV_CheckUserApr($vLongTermID, $vUserID, $vApr)
	{
		$vArrGiatri = array('KetQua' => '0', 'NgayDuyet' => '');
		$isOk = 0;
		$sqlS = "select lv005 NgayDuyet from sl_lv0329 where lv002='" . $vLongTermID . "' and lv004='$vUserID' and lv006='$vApr' and lv008=0 limit 0,1";
		$bResult = db_query($sqlS);
		while ($vrow = db_fetch_array($bResult)) {
			$isOk++;
			$vNgayDuyet = $vrow['NgayDuyet'];
		}
		$vArrGiatri['KetQua'] = $isOk;
		$vArrGiatri['NgayDuyet'] = $vNgayDuyet;
		return $vArrGiatri;
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
	function LV_GetMemberGroup($vParentNode)
	{
		$vReturn = "'" . $vParentNode . "'";
		$strArr = array();
		$sqlS = "SELECT A.lv001 Node,A.lv042 ParentNode FROM hr_lv0020 A  where lv045>=0 and lv001<>'SOF' order by lv045 DESC";
		$bResult = db_query($sqlS);
		$vexit = true;
		while ($vrow = db_fetch_array($bResult)) {
			//echo $vrow['Node']."<br>";
			$strArrReConfirm = array();
			$vexit = false;
			$strArr[$vrow['ParentNode']][$vrow['Node']]['ParentNode'] = $vrow['ParentNode'];
			$strArr[$vrow['Node']][$vrow['Node']]['ParentNode'] = $vrow['ParentNode'];
			$strArr[$vrow['ParentNode']][$vrow['Node']]['Node'] = $vrow['Node'];
		}
		foreach ($strArr[$vParentNode] as $vNode) {
			if ($vNode['Node'] != 'SOF') {
				if ($vReturn == '') $vReturn = "'" . $vNode['Node'] . "'";
				else $vReturn = $vReturn . ",'" . $vNode['Node'] . "'";
				$this->LV_GetMoreFull($strArr, $vNode['Node'], $vDateStart, $vReturn);
			}
		}

		return $vReturn;
	}
	function LV_GetMoreFull($strArr, $vStaffID, &$vReturn)
	{

		foreach ($strArr[$vStaffID] as $vNode) {
			if ($vNode['Node'] != 'SOF') {
				if ($vReturn == '') $vReturn = "'" . $vNode['Node'] . "'";
				else $vReturn = $vReturn . ",'" . $vNode['Node'] . "'";
				$this->LV_GetMoreFull($strArr, $vNode['Node'], $vReturn);
			}
		}
	}
	//////////Get Filter///////////////
	protected function GetCondition()
	{
		$strCondi1 = "";
		if ($this->SoHDList != "") {
			$vArrID = explode("\n", $this->SoHDList);
			foreach ($vArrID as $vID) {
				$vID = trim($vID);
				if ($vID != "") {
					if ($strCondi1 == "")
						$strCondi1 = " AND ( lv112='$vID'";
					else
						$strCondi1 = $strCondi1 . " OR lv112= '$vID'";
				}
			}
			$strCondi1 = $strCondi1 . ")";
		}
		$strCondi = $strCondi1;
		if ($this->thanhvien != '') {
			$strCondi = $strCondi . " and ( lv010  in ( $this->thanhvien))";
		}
		if ($this->lv001 != "") $strCondi = $strCondi . " and lv001  like '%$this->lv001%'";
		if ($this->lv002 != "") {
			if (!strpos($this->lv002, ',') === false) {
				$vArrNameCus = explode(",", $this->lv002);
				foreach ($vArrNameCus as $vNameCus) {
					if ($vNameCus != "") {
						if ($strCondi == "")
							$strCondi = " AND ( lv002 = '$vNameCus'";
						else
							$strCondi = $strCondi . " OR lv002 = '$vNameCus'";
					}
				}
				$strCondi = $strCondi . ")";
			} else {
				$strCondi = $strCondi . " and lv002  = '$this->lv002'";
			}
		}
		if ($this->lv003 != "") $strCondi = $strCondi . " and lv003  like '%$this->lv003%'";
		if ($this->lv004 != "") $strCondi = $strCondi . " and lv004  like '%$this->lv004%'";
		if ($this->lv005 != "") $strCondi = $strCondi . " and lv005  like '%$this->lv005%'";
		if ($this->lv006 != "") $strCondi = $strCondi . " and lv006  like '%$this->lv006%'";
		if ($this->lv007 != "")  $strCondi = $strCondi . " and lv007 like '%$this->lv007%'";
		if ($this->lv008 != "")  $strCondi = $strCondi . " and lv008 like '%$this->lv008%'";
		if ($this->lv009 != "")  $strCondi = $strCondi . " and lv009 like '%$this->lv009%'";
		if ($this->lv010 != "") {
			if (!strpos($this->lv010, ',') === false) {
				$vArrName = explode(",", $this->lv010);
				$strCondi1 = '';
				foreach ($vArrName as $vName) {
					if ($vName != "") {
						if ($strCondi1 == "")
							$strCondi1 = " AND ( lv010 = '$vName'";
						else
							$strCondi1 = $strCondi1 . " OR lv010 = '$vName'";
					}
				}
				if ($strCondi1 != '') $strCondi = $strCondi . $strCondi1 . ")";
			} else {
				$strCondi = $strCondi . " and lv010  = '$this->lv010'";
			}
		}
		switch ($this->datetype) {
			case 0:
				if ($this->datefrom != '') {
					$strCondi = $strCondi . " and lv004 >= '" . recoverdate($this->datefrom, $this->lang) . "'";
				}
				if ($this->dateto != '') {
					$strCondi = $strCondi . " and lv004 <= '" . recoverdate($this->dateto, $this->lang) . "'";
				}
				break;
			case 1:
				if ($this->datefrom != '') {
					$strCondi = $strCondi . " and lv005 >= '" . recoverdate($this->datefrom, $this->lang) . "'";
				}
				if ($this->dateto != '') {
					$strCondi = $strCondi . " and lv005 <= '" . recoverdate($this->dateto, $this->lang) . "'";
				}
				break;
		}
		//////////////
		if ($this->lv011 != "") {
			if (!strpos($this->lv011, ',') === false) {
				$vArrName = explode(",", $this->lv011);
				$strCondi1 = '';
				foreach ($vArrName as $vName) {
					if ($vName != "") {
						if ($strCondi1 == "")
							$strCondi1 = " AND ( lv011 = '$vName'";
						else
							$strCondi1 = $strCondi1 . " OR lv011 = '$vName'";
					}
				}
				if ($strCondi1 != '') $strCondi = $strCondi . $strCondi1 . ")";
			} else {
				$strCondi = $strCondi . " and lv011  = '$this->lv011'";
			}
		}
		if ($this->lv012 != "")  $strCondi = $strCondi . " and lv012 like '%$this->lv012%'";
		if ($this->lv013 != "")  $strCondi = $strCondi . " and lv013 like '%$this->lv013%'";
		if ($this->lv014 != "")  $strCondi = $strCondi . " and lv014 like '%$this->lv014%'";
		if ($this->lv015 != "")  $strCondi = $strCondi . " and lv015 like '%$this->lv015%'";
		if ($this->lv016 != "")  $strCondi = $strCondi . " and lv016 like '%$this->lv016%'";
		//if($this->lv017!="")  $strCondi=$strCondi." and lv017 like '%$this->lv017%'";
		if ($this->lv017 != "") {
			$strCondi = $strCondi . " AND lv017 in ('" . str_replace(",", "','", $this->lv017) . "') ";
		}
		if ($this->lv018 != "")  $strCondi = $strCondi . " and lv018 like '%$this->lv018%'";
		if ($this->lv019 != "")  $strCondi = $strCondi . " and lv019 like '%$this->lv019%'";
		if ($this->lv027 != "") {
			switch ($this->lv027) {
				case 10:
					$strCondi = $strCondi . " and lv027 = '1' and lv034='0'";
					break;
				case 31:
					$strCondi = $strCondi . " and lv027 = '3' and lv034='1'";
					break;
				case 12:
					$strCondi = $strCondi . " and lv027 = '1' and lv034='2'";
					break;
				case 13:
					$strCondi = $strCondi . " and lv027 = '1' and lv034='3'";
					break;
				default:
					$strCondi = $strCondi . " and lv027 = '$this->lv027'";
					break;
			}
		}
		if ($this->lv024 != "") {
			$strCondi = $strCondi . " AND lv024 in ($this->lv024)";
		}
		if ($this->lv030 != "")  $strCondi = $strCondi . " and lv030 = '$this->lv030'";
		//if($this->lv103!="")  $strCondi=$strCondi." and lv103 = '$this->lv103'";
		if ($this->lv103 != "") {
			$strCondi = $strCondi . " AND lv103 in ('" . str_replace(",", "','", $this->lv103) . "')";
		}
		if ($this->lv104 != "")  $strCondi = $strCondi . " and lv104 = '$this->lv104'";
		if ($this->lv108 != "")  $strCondi = $strCondi . " and lv108 = '$this->lv108'";
		if ($this->lv109 != "")  $strCondi = $strCondi . " and lv109 = '$this->lv109'";
		if ($this->lv110 != "")  $strCondi = $strCondi . " and lv110 = '$this->lv110'";
		if ($this->lv112 != "")  $strCondi = $strCondi . " and lv112 like '%$this->lv112%'";
		if ($this->lv113 != "")  $strCondi = $strCondi . " and lv113 = '$this->lv113'";
		if ($this->lv114 != "")  $strCondi = $strCondi . " and lv114 = '$this->lv114'";
		if ($this->lv115 != "") {
			if (!strpos($this->lv115, ',') === false) {
				$vArrName = explode(",", $this->lv115);
				$strCondi1 = '';
				foreach ($vArrName as $vName) {
					if ($vName != "") {
						if ($strCondi1 == "")
							$strCondi1 = " AND ( lv115 = '$vName'";
						else
							$strCondi1 = $strCondi1 . " OR lv115 = '$vName'";
					}
				}
				if ($strCondi1 != '') $strCondi = $strCondi . $strCondi1 . ")";
			} else {
				$strCondi = $strCondi . " and lv115  = '$this->lv115'";
			}
		}
		if ($this->lv069 != "") {
			if (!strpos($this->lv069, ',') === false) {
				$vArrName = explode(",", $this->lv069);
				$strCondi1 = '';
				foreach ($vArrName as $vName) {
					if ($vName != "") {
						if ($strCondi1 == "")
							$strCondi1 = " AND ( lv069 = '$vName'";
						else
							$strCondi1 = $strCondi1 . " OR lv069 = '$vName'";
					}
				}
				if ($strCondi1 != '') $strCondi = $strCondi . $strCondi1 . ")";
			} else {
				$strCondi = $strCondi . " and lv069  = '$this->lv069'";
			}
		}
		if ($this->lv116 != "")  $strCondi = $strCondi . " and lv116 like '%$this->lv116%'";
		//Originator
		if ($this->lv101 != "") {
			if (!strpos($this->lv101, ',') === false) {
				$vArrName = explode(",", $this->lv101);
				$strCondi1 = '';
				foreach ($vArrName as $vName) {
					if ($vName != "") {
						if ($strCondi1 == "")
							$strCondi1 = " AND ( lv101 = '$vName'";
						else
							$strCondi1 = $strCondi1 . " OR lv101 = '$vName'";
					}
				}
				if ($strCondi1 != '') $strCondi = $strCondi . $strCondi1 . ")";
			} else {
				$strCondi = $strCondi . " and lv101  = '$this->lv101'";
			}
		}
		//Originated by
		if ($this->lv023 != "") {
			if (!strpos($this->lv023, ',') === false) {
				$vArrName = explode(",", $this->lv023);
				$strCondi1 = '';
				foreach ($vArrName as $vName) {
					if ($vName != "") {
						if ($strCondi1 == "")
							$strCondi1 = " AND ( lv023 = '$vName'";
						else
							$strCondi1 = $strCondi1 . " OR lv023 = '$vName'";
					}
				}
				if ($strCondi1 != '') $strCondi = $strCondi . $strCondi1 . ")";
			} else {
				$strCondi = $strCondi . " and lv023  = '$this->lv023'";
			}
		}
		return $strCondi;
	}
	protected function GetConditionView()
	{
		$strCondi1 = "";
		if ($this->SoHDList != "") {
			$vArrID = explode("\n", $this->SoHDList);
			foreach ($vArrID as $vID) {
				$vID = trim($vID);
				if ($vID != "") {
					if ($strCondi1 == "")
						$strCondi1 = " AND ( lv112='$vID'";
					else
						$strCondi1 = $strCondi1 . " OR lv112= '$vID'";
				}
			}
			$strCondi1 = $strCondi1 . ")";
		}
		$strCondi = $strCondi1;
		if ($this->thanhvien != '') {
			$strCondi = $strCondi . " and ( lv010  in ( $this->thanhvien))";
		}
		if ($this->lv001 != "") $strCondi = $strCondi . " and lv001  like '%$this->lv001%'";
		if ($this->lv002 != "") {
			if (!strpos($this->lv002, ',') === false) {
				$vArrNameCus = explode(",", $this->lv002);
				foreach ($vArrNameCus as $vNameCus) {
					if ($vNameCus != "") {
						if ($strCondi == "")
							$strCondi = " AND ( lv002 = '$vNameCus'";
						else
							$strCondi = $strCondi . " OR lv002 = '$vNameCus'";
					}
				}
				$strCondi = $strCondi . ")";
			} else {
				$strCondi = $strCondi . " and lv002  = '$this->lv002'";
			}
		}
		if ($this->lv003 != "") $strCondi = $strCondi . " and lv003  like '%$this->lv003%'";
		if ($this->lv004 != "") $strCondi = $strCondi . " and lv004  like '%$this->lv004%'";
		if ($this->lv005 != "") $strCondi = $strCondi . " and lv005  like '%$this->lv005%'";
		if ($this->lv006 != "") $strCondi = $strCondi . " and lv006  like '%$this->lv006%'";
		if ($this->lv007 != "")  $strCondi = $strCondi . " and lv007 like '%$this->lv007%'";
		if ($this->lv008 != "")  $strCondi = $strCondi . " and lv008 like '%$this->lv008%'";
		if ($this->lv009 != "")  $strCondi = $strCondi . " and lv009 like '%$this->lv009%'";
		if ($this->lv010 != "") {
			if (!strpos($this->lv010, ',') === false) {
				$vArrName = explode(",", $this->lv010);
				$strCondi1 = '';
				foreach ($vArrName as $vName) {
					if ($vName != "") {
						if ($strCondi1 == "")
							$strCondi1 = " AND ( lv010 = '$vName'";
						else
							$strCondi1 = $strCondi1 . " OR lv010 = '$vName'";
					}
				}
				if ($strCondi1 != '') $strCondi = $strCondi . $strCondi1 . ")";
			} else {
				$strCondi = $strCondi . " and lv010  = '$this->lv010'";
			}
		}
		switch ($this->datetype) {
			case 0:
				if ($this->datefrom != '') {
					$strCondi = $strCondi . " and lv004 >= '" . recoverdate($this->datefrom, $this->lang) . "'";
				}
				if ($this->dateto != '') {
					$strCondi = $strCondi . " and lv004 <= '" . recoverdate($this->dateto, $this->lang) . "'";
				}
				break;
			case 1:
				if ($this->datefrom != '') {
					$strCondi = $strCondi . " and lv005 >= '" . recoverdate($this->datefrom, $this->lang) . "'";
				}
				if ($this->dateto != '') {
					$strCondi = $strCondi . " and lv005 <= '" . recoverdate($this->dateto, $this->lang) . "'";
				}
				break;
		}
		//////////////

		if ($this->lv011 != "")  $strCondi = $strCondi . " and lv011 like '$this->lv011'";
		if ($this->lv012 != "")  $strCondi = $strCondi . " and lv012 like '%$this->lv012%'";
		if ($this->lv013 != "")  $strCondi = $strCondi . " and lv013 like '%$this->lv013%'";
		if ($this->lv014 != "")  $strCondi = $strCondi . " and lv014 like '%$this->lv014%'";
		if ($this->lv015 != "")  $strCondi = $strCondi . " and lv015 like '%$this->lv015%'";
		if ($this->lv016 != "") {
			if (!strpos($this->lv016, ',') === false) {
				$vArrName = explode(",", $this->lv011);
				$strCondi1 = '';
				foreach ($vArrName as $vName) {
					if ($vName != "") {
						if ($strCondi1 == "")
							$strCondi1 = " AND ( lv016 = '$vName'";
						else
							$strCondi1 = $strCondi1 . " OR lv016 = '$vName'";
					}
				}
				if ($strCondi1 != '') $strCondi = $strCondi . $strCondi1 . ")";
			} else {
				$strCondi = $strCondi . " and lv016  = '$this->lv016'";
			}
		}
		//if($this->lv017!="")  $strCondi=$strCondi." and lv017 like '%$this->lv017%'";
		if ($this->lv017 != "") {
			$strCondi = $strCondi . " AND lv017 in ('" . str_replace(",", "','", $this->lv017) . "') ";
		}
		if ($this->lv018 != "") {
			if (!strpos($this->lv018, ',') === false) {
				$vArrName = explode(",", $this->lv011);
				$strCondi1 = '';
				foreach ($vArrName as $vName) {
					if ($vName != "") {
						if ($strCondi1 == "")
							$strCondi1 = " AND ( lv018 = '$vName'";
						else
							$strCondi1 = $strCondi1 . " OR lv018 = '$vName'";
					}
				}
				if ($strCondi1 != '') $strCondi = $strCondi . $strCondi1 . ")";
			} else {
				$strCondi = $strCondi . " and lv018  = '$this->lv018'";
			}
		}
		if ($this->lv019 != "")  $strCondi = $strCondi . " and lv019 like '%$this->lv019%'";
		if ($this->lv020 != "") {
			if (!strpos($this->lv020, ',') === false) {
				$vArrName = explode(",", $this->lv011);
				$strCondi1 = '';
				foreach ($vArrName as $vName) {
					if ($vName != "") {
						if ($strCondi1 == "")
							$strCondi1 = " AND ( lv020 = '$vName'";
						else
							$strCondi1 = $strCondi1 . " OR lv020 = '$vName'";
					}
				}
				if ($strCondi1 != '') $strCondi = $strCondi . $strCondi1 . ")";
			} else {
				$strCondi = $strCondi . " and lv020  = '$this->lv020'";
			}
		}
		//if($this->lv024!="")  $strCondi=$strCondi." and lv024 = '$this->lv024'";
		if ($this->lv024 != "") {
			$strCondi = $strCondi . " AND lv024 in ($this->lv024)";
		}
		if ($this->lv030 != "")  $strCondi = $strCondi . " and lv030 = '$this->lv030'";
		//if($this->lv103!="")  $strCondi=$strCondi." and lv103 = '$this->lv103'";
		if ($this->lv103 != "") {
			$strCondi = $strCondi . " AND lv103 in ('" . str_replace(",", "','", $this->lv103) . "')";
		}
		if ($this->lv104 != "")  $strCondi = $strCondi . " and lv104 = '$this->lv104'";
		if ($this->lv108 != "")  $strCondi = $strCondi . " and lv108 = '$this->lv108'";
		if ($this->lv109 != "")  $strCondi = $strCondi . " and lv109 = '$this->lv109'";
		if ($this->lv110 != "")  $strCondi = $strCondi . " and lv110 = '$this->lv110'";
		if ($this->lv112 != "")  $strCondi = $strCondi . " and lv112 like '%$this->lv112%'";
		if ($this->lv113 != "")  $strCondi = $strCondi . " and lv113 = '$this->lv113'";
		if ($this->lv114 != "")  $strCondi = $strCondi . " and lv114 like '%$this->lv114%'";
		if ($this->lv115 != "")  $strCondi = $strCondi . " and lv115 like '%$this->lv115%'";
		if ($this->lv116 != "")  $strCondi = $strCondi . " and lv116 like '%$this->lv116%'";
		//Originator
		if ($this->lv101 != "") {
			if (!strpos($this->lv101, ',') === false) {
				$vArrName = explode(",", $this->lv101);
				$strCondi1 = '';
				foreach ($vArrName as $vName) {
					if ($vName != "") {
						if ($strCondi1 == "")
							$strCondi1 = " AND ( lv101 = '$vName'";
						else
							$strCondi1 = $strCondi1 . " OR lv101 = '$vName'";
					}
				}
				if ($strCondi1 != '') $strCondi = $strCondi . $strCondi1 . ")";
			} else {
				$strCondi = $strCondi . " and lv101  = '$this->lv101'";
			}
		}
		//Originated by
		if ($this->lv023 != "") {
			if (!strpos($this->lv023, ',') === false) {
				$vArrName = explode(",", $this->lv023);
				$strCondi1 = '';
				foreach ($vArrName as $vName) {
					if ($vName != "") {
						if ($strCondi1 == "")
							$strCondi1 = " AND ( lv023 = '$vName'";
						else
							$strCondi1 = $strCondi1 . " OR lv023 = '$vName'";
					}
				}
				if ($strCondi1 != '') $strCondi = $strCondi . $strCondi1 . ")";
			} else {
				$strCondi = $strCondi . " and lv023  = '$this->lv023'";
			}
		}
		return $strCondi;
	}
	public function GetBuilCheckListSource($vListID, $vID, $vTabIndex)
	{
		$vListID = "," . $vListID . ",";
		$strTbl = "<table  align=\"center\" class=\"lvtable\">
		<input type=\"hidden\" id=$vID name=$vID value=\"@#02\">
		@#01
		</table>
		<script language=\"javascript\">
		function getChecked(len,nameobj)
		{
			var str='';
			for(i=0;i<len;i++)
			{
			div = document.getElementById(nameobj+i);
			if(div.checked)
				{
				if(str=='') 
					str=div.value;
				else
					 str=str+','+div.value;
				}
			
			}
			return str;
		}
		</script>
		";
		$lvChk = "<input onchange=\"ChangeInfor()\" type=\"checkbox\" id=\"$vID@01\" value=\"@02\" @03 title=\"@04\" tabindex=\"$vTabIndex\">";
		$lvTrH = "<tr class=\"lvlinehtable1\">
			<td width=1%>@#01</td><td>@#02</td>
			
		</tr>
		";
		$vsql = "
		select '' lv001,'Tất cả' lv002,'Tất cả' lv003
		union
		select '0' lv001,'Kéo phí' lv002,'Kéo phí' lv003
		union
		select '2' lv001,'Từ cổnng dịch vụ MINH PHUONG' lv002,'Từ cổnng dịch vụ MINH PHUONG' lv003
		union
		select '3' lv001,'Tải lên tính thưởng' lv002,'Tải lên tính thưởng' lv003
		union
		select '1' lv001,'Tải lên không tính thưởng' lv002,'Tải lên không tính thưởng' lv003
		";
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
			$strTemp = str_replace("@#02", $vrow['lv002'] . "(" . $vrow['lv001'] . ")", $strTemp);
			$strGetScript = $strGetScript . $strTemp;
			$i++;
		}
		$strReturn = str_replace("@#01", $strGetScript, str_replace("@#02", $numrows, $strTbl));
		return $strReturn;
	}
	public function GetBuilCheckListChuKy($vListID, $vID, $vTabIndex)
	{
		$vListID = "," . $vListID . ",";
		$strTbl = "<table  align=\"center\" class=\"lvtable\">
		<input type=\"hidden\" id=$vID name=$vID value=\"@#02\">
		@#01
		</table>
		<script language=\"javascript\">
		function getChecked(len,nameobj)
		{
			var str='';
			for(i=0;i<len;i++)
			{
			div = document.getElementById(nameobj+i);
			if(div.checked)
				{
				if(str=='') 
					str=div.value;
				else
					 str=str+','+div.value;
				}
			
			}
			return str;
		}
		</script>
		";
		$lvChk = "<input onchange=\"ChangeInfor()\" type=\"checkbox\" id=\"$vID@01\" value=\"@02\" @03 title=\"@04\" tabindex=\"$vTabIndex\">";
		$lvTrH = "<tr class=\"lvlinehtable1\">
			<td width=1%>@#01</td><td>@#02</td>
			
		</tr>
		";
		$vsql = "select * from  hr_lv0111 order by lv004 DESC";
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
			$strTemp = str_replace("@#02", $vrow['lv002'], $strTemp);
			$strGetScript = $strGetScript . $strTemp;
			$i++;
		}
		$strReturn = str_replace("@#01", $strGetScript, str_replace("@#02", $numrows, $strTbl));
		return $strReturn;
	}
	public function GetBuilCheckListCongTyBaoHiem($vListID, $vID, $vTabIndex)
	{
		$vListID = "," . $vListID . ",";
		$strTbl = "<table  align=\"center\" class=\"lvtable\">
		<input type=\"hidden\" id=$vID name=$vID value=\"@#02\">
		@#01
		</table>
		<script language=\"javascript\">
		function getChecked(len,nameobj)
		{
			var str='';
			for(i=0;i<len;i++)
			{
			div = document.getElementById(nameobj+i);
			if(div.checked)
				{
				if(str=='') 
					str=div.value;
				else
					 str=str+','+div.value;
				}
			
			}
			return str;
		}
		</script>
		";
		$lvChk = "<input onchange=\"ChangeInfor()\" type=\"checkbox\" id=\"$vID@01\" value=\"@02\" @03 title=\"@04\" tabindex=\"$vTabIndex\">";
		$lvTrH = "<tr class=\"lvlinehtable1\">
			<td width=1%>@#01</td><td>@#02</td>
			
		</tr>
		";
		$vsql = "select * from  wh_lv0003 where lv018=3 order by lv004 DESC";
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
			$strTemp = str_replace("@#02", $vrow['lv002'], $strTemp);
			$strGetScript = $strGetScript . $strTemp;
			$i++;
		}
		$strReturn = str_replace("@#01", $strGetScript, str_replace("@#02", $numrows, $strTbl));
		return $strReturn;
	}
	////////////////Count///////////////////////////
	function GetCountReturn()
	{
		$vIsUser = 0;
		$vIsMore = 0;
		$sqlC = "SELECT COUNT(DISTINCT B.lv001) AS nums  FROM sl_lv0329 A inner join sl_lv0013 B on A.lv002=B.lv001 WHERE B.lv011=0 and B.lv027=0 and A.lv007=1 ";
		$bResultC = db_query($sqlC);
		$arrRowC = db_fetch_array($bResultC);
		return $arrRowC['nums'];
	}
	function GetCount()
	{
		$sqlC = "SELECT COUNT(*) AS nums FROM sl_lv0013 WHERE 1=1 " . $this->GetCondition();
		$bResultC = db_query($sqlC);
		$arrRowC = db_fetch_array($bResultC);
		return $arrRowC['nums'];
	}

	function LV_GetAlarm($vNgayConLai)
	{
		if ($vNgayConLai >= 0 && $vNgayConLai <= 60) {
			return "Còn " . $vNgayConLai . " ngày đến hạn";
		} elseif ($vNgayConLai >= 0 && $vNgayConLai >= 60) {
			return "Còn " . $vNgayConLai . " ngày đến hạn";
		} elseif ($vNgayConLai > 7) {
			return "";
		} elseif ($vNgayConLai < 0) {
			return "Quá hạn " . (-1 * $vNgayConLai) . " ngày";
		}
		return '';
	}

	//Gửi kết quả
	function LV_SendResult($vResult)
	{
		$vListStaff = '';
		$vListCus = '';
		while ($vrow = db_fetch_array($vResult)) {
			$vArrHopDong[$vrow['lv001']]['lv001'] = $vrow['lv001'];
			$vArrHopDong[$vrow['lv001']]['lv101'] = $vrow['lv101'];
			$vArrHopDong[$vrow['lv001']]['lv004'] = $vrow['lv004'];
			$vArrHopDong[$vrow['lv001']]['lv005'] = $vrow['lv005'];
			$vArrHopDong[$vrow['lv001']]['lv024'] = $vrow['lv024'];
			$vArrHopDong[$vrow['lv001']]['lv003'] = $vrow['lv003'];
			$vArrHopDong[$vrow['lv001']]['lv014'] = $vrow['lv014'];
			$vArrHopDong[$vrow['lv001']]['lv015'] = $vrow['lv015'];
			$vArrHopDong[$vrow['lv001']]['lv009'] = $vrow['lv009'];
			$vArrHopDong[$vrow['lv001']]['lv013'] = $vrow['lv013'];
			$vArrHopDong[$vrow['lv001']]['lv102'] = $vrow['lv102'];
			$vArrHopDong[$vrow['lv001']]['lv017'] = $vrow['lv017'];
			$vArrHopDong[$vrow['lv001']]['lv109'] = $vrow['lv109'];
			$vArrHopDong[$vrow['lv001']]['lv110'] = $vrow['lv110'];
			$vArrHopDong[$vrow['lv001']]['lv112'] = $vrow['lv112'];
			$vArrHopDong[$vrow['lv001']]['lv016'] = $vrow['lv016'];
			$vArrHopDong[$vrow['lv001']]['lv020'] = $vrow['lv020'];
			$vArrHopDong[$vrow['lv001']]['lv018'] = $vrow['lv018'];
			$vArrHopDong[$vrow['lv001']]['lv023'] = $vrow['lv023'];
			$vArrHopDong[$vrow['lv001']]['lv105'] = $vrow['lv105'];
			if ($vListStaff == '')
				$vListStaff = "'" . $vrow['lv010'] . "'";
			else
				$vListStaff = $vListStaff . ",'" . $vrow['lv010'] . "'";
			if ($vListCus == '')
				$vListCus = "'" . $vrow['lv002'] . "'";
			else
				$vListCus = $vListCus . ",'" . $vrow['lv002'] . "'";
		}
		$vsqlstaff = "select lv001,lv002,lv010,lv039 from hr_lv0020 where lv001 in ($vListStaff)";
		$vResult1 = db_query($vsqlstaff);
		while ($vrow1 = db_fetch_array($vResult1)) {
			$this->ArrStaff[$vrow1['lv001']][2] = $vrow1['lv002'];
			$this->ArrStaff[$vrow1['lv001']][10] = $vrow1['lv010'];
			$this->ArrStaff[$vrow1['lv001']][39] = $vrow1['lv039'];
		}
		$vsqlcustomer = "select lv001,lv002,lv011 from sl_lv0001 where lv001 in ($vListCus)";
		$vResult2 = db_query($vsqlcustomer);
		while ($vrow2 = db_fetch_array($vResult2)) {
			$this->ArrCus[$vrow2['lv001']][2] = $vrow2['lv002'];
			$this->ArrCus[$vrow2['lv001']][11] = $vrow2['lv011'];
		}
		return $vArrHopDong;
	}
	function LV_CreateDetailUpdate($vContractID)
	{
		$vTable = '<table class="lvtable" width="100%">
		<tr class="lvlinehtable0">
			<td align="center"><strong>STT</strong></td>
			<td align="center"><strong>Sản phẩm</strong></td>
			<td align="center"><strong>Chính/Phụ</strong></td>
			<td align="center"><strong>FYP</strong></td>
			<td align="center"><strong>Công ty</strong></td>
		</tr>
		
		@#01
	</table>';
		$vTr = '
			<tr class="lvlinehtable@#01">
				<td align="left">@01</td>
				<td align="left">@02</td>
				<td align="left">@03</td>
				<td align="right">@04</td>
				<td align="center">@08</td>
			</tr>';

		$sqlS = "SELECT A.*,B.lv013 CongTy FROM sl_lv0014 A inner join sl_lv0007 B on A.lv003=B.lv001 WHERE A.lv002='" . $vContractID . "' $vDieuKien";
		$bResult = db_query($sqlS);
		while ($vrow = db_fetch_array($bResult)) {
			$vLineOne = $vTr;
			$vOrder++;
			$vPhiBH = round($vrow['lv006'], 0);
			$sPhiBH = $sPhiBH + $vPhiBH;
			$vLineOne = str_replace("@#01", (($vOrder + 1) % 2), $vLineOne);
			$vLineOne = str_replace("@01", $vOrder, $vLineOne);
			$vLineOne = str_replace("@02", $this->getvaluelink('lv902', $vrow['lv003']) . $vrow['lv003'], $vLineOne);
			$vLineOne = str_replace("@03", $this->getvaluelink('lv016', $vrow['lv009']), $vLineOne);
			$vLineOne = str_replace("@04", $this->FormatView($vPhiBH, 10), $vLineOne);
			$vLineOne = str_replace("@05", $vrow['lv010'], $vLineOne);
			$vLineOne = str_replace("@06", $this->FormatView($vrow['lv011'], 20), $vLineOne);
			$vPFYP = round($vrow['lv012'], 0);
			$sPFYP = $sPFYP + $vPFYP;
			$vLineOne = str_replace("@07", $this->FormatView($vPFYP, 20), $vLineOne);
			$vLineOne = str_replace("@08", $vrow['CongTy'], $vLineOne);
			$vLineAll = $vLineAll . $vLineOne;
			if ($vrow['lv009'] == 'ANCHI') $this->anchi = $vrow['lv009'];
		}
		$vSumTr = '
			<tr class="lvlinehtable0">
				<td align="left" colspan="3"><strong>Tổng FYP</strong></td>
				<td align="right"><strong><input type="hidden" id="tongphibaohiem" value="' . $sPhiBH . '"/>' . $this->FormatView($sPhiBH, 10) . '</strong></td>
				<td align="left"><strong></strong></td>
				<td align="left"><strong></strong></td>
			</tr>';
		$vTable = str_replace("@#01", $vLineAll . $vSumTr, $vTable);
		return $vTable;
		$vStrTr = '
	';
	}
	//SP Phu	
	function LV_GetProSPPhu($vContract)
	{
		$sqlC = "SELECT sum(B.lv012) PFYP FROM sl_lv0013 A inner join sl_lv0014 B on A.lv001=B.lv002 WHERE A.lv001='$vContract' and B.lv009<>'CHINH'";
		$bResultC = db_query($sqlC);
		while ($arrRowC = db_fetch_array($bResultC)) {
			return round($arrRowC['PFYP'], 0);
		}
		return 0;
	}
	////
	function LV_AutoChangeKy1To2()
	{
		$vYear = getyear($this->DateCurrent);
		$vMonth = getmonth($this->DateCurrent);
		$vKy1 = "CK" . $vYear . $vMonth . "01";
		$vKy2 = "CK" . $vYear . $vMonth . "02";
		$vsql = "update sl_lv0013 set lv017='$vKy2' where lv017='$vKy1' and lv103 in ('VBI','BHV','Pacificros');";
		$bResultC = db_query($vsql);
	}
	function LV_LoadAmountDetail($vSaleID)
	{
		$sqlC = "SELECT sum(A.lv054) TongChiTiet FROM cr_lv0276 A WHERE A.lv002='$vSaleID' ";
		$bResultC = db_query($sqlC);
		while ($arrRowC = db_fetch_array($bResultC)) {
			return round($arrRowC['TongChiTiet'], 0);
		}
		return 0;
	}
	function LV_GetTapTin($vID)
	{
		$sqlC = "SELECT lv001,lv004  FROM sl_lv0322 WHERE lv002='$vID' and lv003='HĐKT' ";
		$bResultC = db_query($sqlC);
		$lvImg = '';
		while ($arrRowC = db_fetch_array($bResultC)) {
			$lvImg = "<a target='_blank' href='" . $this->Dir . "sl_lv0322/readfile.php?FileID=" . $arrRowC['lv001'] . "&type=8&size=0'><div style=\"border-radius:5px;background:#aaa;padding:4px;color:#000;\">" . (($arrRowC['lv004']) == '' ? 'Tải tập tin' : $arrRowC['lv004']) . "</div></a>";
		}
		return $lvImg;
	}
	//////////////////////Buil list////////////////////
	//////////////////////Buil list////////////////////
	function LV_BuilList($lvList, $lvFrom, $lvChkAll, $lvChk, $curRow, $maxRows, $paging, $lvOrderList, $lvSortNum)
	{
		$this->isBuilList = 1;
		if ($lvList == "") $lvList = $this->DefaultFieldList;
		if ($this->isView == 0) return false;
		if ($curRow < 0) $curRow = 0;
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
	<div id=\"func_id\" style='position:relative;background:#f2f2f2'><div style=\"float:left\">" . $this->TabFunction($lvFrom, $lvList, $maxRows) . "</div><div style=\"float:right\">" . $this->ListFieldSave($lvFrom, $lvList, $maxRows, $lvOrderList, $lvSortNum) . "</div><div style='float:right'>&nbsp;&nbsp;&nbsp;</div><div style='float:right'>" . $this->ListFieldExport($lvFrom, $lvList, $maxRows) . "</div></div><div style='height:35px'></div>
	<table  align=\"center\" class=\"lvtable\"><!--<tr ><td colspan=\"" . (2 + count($lstArr)) . "\" class=\"lvTTable\">" . $this->ArrPush[0] . "</td></tr>-->
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
		$lvTrSum = "<tr class=\"lvlinehtable@01\">
		<td colspan=\"2\"><strong>" . ($this->ArrPush[99]) . "</strong></td>
		@#01
	</tr>
	";
		$lvHref = "@02";
		$lvTdH = "<td width=\"@01\" class=\"lvhtable\">@02</td>";
		$lvTd = "<td  class=\"@#04\" align=\"@#05\">@02</td>";
		$lvTd1 = "<td align='@#05' nowrap><div class=\"@#44\">@02</div></td>";
		$lvSum = "<td  class=\"@#04\" align=\"@#05\"><strong>@02</strong></td>";
		$lvTdF = "<td align=\"right\"><strong>@01</strong></td>";
		$strF = "<tr><td colspan=\"2\">&nbsp;</td>";
		$sqlS = "SELECT  * FROM sl_lv0013 WHERE 1=1  " . $this->GetCondition() . " $strSort LIMIT $curRow, $maxRows";
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
		$sumHD = 0;
		$sumTT = 0;
		$sumCL = 0;
		while ($vrow = db_fetch_array($bResult)) {
			$strL = "";
			$vorder++;
			$strSum = '';
			$slv109 = $slv109 + $vrow['lv109'];
			$slv110 = $slv110 + $vrow['lv110'];

			$vlv900 = $this->LV_LoadAmountDetail($vrow['lv001']);
			$vDiscount = $vrow['lv108'];
			$vTienDisCount = $vlv900 * $vDiscount / 100;
			$vCostShip = (float)$vrow['lv109'];
			$vVAT = (float)$vrow['lv110'];
			$vTienVAT = ($vlv900 - $vTienDisCount) * $vVAT / 100; //$vTienVAT=($vlv900-$vTienDisCount+$vCostShip)*$vVAT/100;
			$vlv900 = $vlv900 + $vTienVAT + $vCostShip - $vTienDisCount;
			$slv900 = $slv900 + $vlv900;
			//$this->LV_GetQRcodeLink($vrow['lv115'],'SL_SOF_'.$vrow['lv001']);
			for ($i = 0; $i < count($lstArr); $i++) {
				switch ($lstArr[$i]) {
					case 'lv200':
						$lvImg = $this->LV_GetTapTin($vrow['lv001']);
						$vTemp = str_replace("@02", str_replace("@02", $lvImg, str_replace("@01", $vrow['lv001'], $lvHref)), $this->Align($lvTd, (int)$this->ArrView[$lstArr[$i]]));
						break;
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

						/*if($this->GetApr()==1 )
					{
					
						$vChucNang=$vChucNang.'<td><img Title="'.(($this->GetApr()==1)?'Đề xuất duyệt':'Duyệt tham khảo').'" style="cursor:pointer;width:25px;padding:5px;" onclick="'.(($this->GetApr()==1)?'AprOk':'AprNoOk').'(\''.($vrow['lv001']).'\')" alt="NoImg" src="../images/icon/'.(($this->GetApr()==1)?'Apr.png':'Add.png').'" align="middle" border="0" name="new" class="lviconimg"></td>';
					}*/

						$vChucNang = $vChucNang . '<td><img style="cursor:pointer;height:25px;padding:5px;" onclick="Report(\'' . $vrow['lv001'] . '\')" alt="NoImg" src="../images/icon/Rpt.png" align="middle" border="0" name="new" class="lviconimg"></td>';
						/*
					<span onclick="ProcessTextHiden(this)"><a href="javascript:FunctRunning1(\''.$vrow['lv001'].'\')"><img style="cursor:pointer;width:25px;;padding:5px;"  alt="NoImg" src="../images/icon/work_experience.png" align="middle" border="0" name="new" class="lviconimg"></a></span>
					';*/
						$vStr = '	<td>
					<div style="cursor:pointer;color:blue;" onclick="showDetailBBG(\'chitietbbgid_' . $vrow['lv001'] . '\',\'' . $vrow['lv001'] . '\')">' . '<img style="cursor:pointer;width:25px;;padding:5px;"  alt="NoImg" src="../images/icon/job.png" title="Xem chi tiết BBG"/>' . '</div>
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
					</div>	
				</td>
					';
						$vStr1 = '<td>
									<div style="cursor:pointer;color:blue;" onclick="showDetailHistory(\'chitietid_' . $vrow['lv001'] . '\',\'' . $vrow['lv001'] . '\')">' . '<img style="cursor:pointer;width:25px;;padding:5px;"  alt="NoImg" src="../images/icon/license.png" title="Xem lịch sử duyệt"/>' . '</div>
									<div style="display:none;position:absolute;z-index:999999999999;background:#efefef;" id="chitietid_' . $vrow['lv001'] . '" class="noidung_member">					
										<div class="hd_cafe" style="width:100%">
											<ul class="qlycafe" style="width:100%">
												<li style="padding:10px;"><img onclick="document.getElementById(\'chitietid_' . $vrow['lv001'] . '\').style.display=\'none\';" width="20" src="images/icon/close.png"/></li>
												<li style="padding:10px;"><div style="width:100%;padding-top:2px;">
												<strong>LỊCH SỬ DUYỆT PHIẾU BÁN HÀNG:' . $vrow['lv115'] . '</strong></div>
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
						$vChucNang = $vChucNang . $vStr1 . $vStr;
						if ($this->GetApr() == 1 && $vrow['lv011'] == 0) {
							$vChucNang = $vChucNang . '<td><input type="button" value="Đề xuất duyệt" style="padding:3px;border-radius:3px;font-weight:bold;cursor:pointer;" onclick="Approvals(\'' . $vrow['lv001'] . '@\')"/></td>';
						}
						$vChucNang = $vChucNang . "</tr></table>";
						$vTemp = str_replace("@02", $vChucNang, $this->Align($lvTd, (int)$this->ArrView[$lstArr[$i]]));
						break;
					case 'lv115':
					case 'lv014':
					case 'lv224':
					case 'lv112':
					case 'lv212':
						if ($this->GetApr() == 1 && $this->GetUnApr() == 1 && 1 == 0) {
							$vposition = (int)str_replace('lv', '', $lstArr[$i]);
							$lvTdTextBox = "<input class='txtenterquick' type=\"textbox\" value=\"" . $vrow[$lstArr[$i]] . "\" @03 onfocus=\"if(this.value=='') this.value='" . $vrow[$lstArr[$i]] . "'\" onblur=\"UpdateText(this,'" . $vrow['lv001'] . "',$vposition)\" style=\"min-width:120px;width:100%;text-align:center\" tabindex=\"2\"  maxlength=\"100\" />";
							$vTemp = str_replace("@02", str_replace("@02", $lvTdTextBox, str_replace("@01", $vrow['lv001'], $lvHref)), $this->Align($lvTd, (int)$this->ArrView[$lstArr[$i]]));
						} else {
							$vTemp = str_replace("@02", str_replace("@02", $this->getvaluelink($lstArr[$i], $this->FormatView($vrow[$lstArr[$i]], (int)$this->ArrView[$lstArr[$i]])), str_replace("@01", $vrow['lv001'], $lvHref)), $this->Align($lvTd, (int)$this->ArrView[$lstArr[$i]]));
						}
						break;
					case 'lv350':
						if ($vrow['lv027'] < 1   && $this->GetEdit() > 0) {
							$vField = $lstArr[$i];
							$vSTTCot = (int)substr($lstArr[$i], 2, 3);
							$vID = $vrow['lv001'];
							$vStringNumber = ' onblur="UpdateTextCheck(this,\'' . $vID . '\',' . $vSTTCot . ')" ';
							$vstr = "<input " . $vStringNumber . " autocomplete=\"off\"   type=\"checkbox\" value=\"1\" " . (($vrow[$vField] == 1) ? 'checked="true"' : '') . " @03  style=\"text-align:center;\" tabindex=\"2\" maxlength=\"32\"   onKeyPress=\"return CheckKey(event,7)\"/>";
							$vTemp = str_replace("@02", $vstr, $this->Align($lvTd, (int)$this->ArrView[$lstArr[$i]]));
						} else {
							$vField = $lstArr[$i];
							$vSTTCot = (int)substr($lstArr[$i], 2, 3);
							$vID = $vrow['lv001'];
							$vStringNumber = '';
							$vstr = "<input  disabled=\"disabled\" " . $vStringNumber . " autocomplete=\"off\"   type=\"checkbox\" value=\"1\" " . (($vrow[$vField] == 1) ? 'checked="true"' : '') . " @03  style=\"text-align:center;\" tabindex=\"2\" maxlength=\"32\"   onKeyPress=\"return CheckKey(event,7)\"/>";
							$vTemp = str_replace("@02", $vstr, $this->Align($lvTd, (int)$this->ArrView[$lstArr[$i]]));
						}
						break;
					case 'lv069':
						if ($vrow['lv027'] < 1   && $this->GetEdit() > 0) {
							$vposition = (int)str_replace('lv', '', $lstArr[$i]);
							$lvTdTextBox = "<input class='txtenterquick' type=\"textbox\" value=\"" . $vrow[$lstArr[$i]] . "\" @03 onfocus=\"if(this.value=='') this.value='" . $vrow[$lstArr[$i]] . "'\" onblur=\"UpdateText(this,'" . $vrow['lv001'] . "',$vposition)\" style=\"min-width:150px;width:100%;text-align:center\" tabindex=\"2\"  maxlength=\"100\" />";
							$vTemp = str_replace("@02", str_replace("@02", $lvTdTextBox, str_replace("@01", $vrow['lv001'], $lvHref)), $this->Align($lvTd, (int)$this->ArrView[$lstArr[$i]]));
						} else {
							$vTemp = str_replace("@02", str_replace("@02", $this->getvaluelink($lstArr[$i], $this->FormatView($vrow[$lstArr[$i]], (int)$this->ArrView[$lstArr[$i]])), str_replace("@01", $vrow['lv001'], $lvHref)), $this->Align($lvTd, (int)$this->ArrView[$lstArr[$i]]));
						}
						break;
					case 'lv900':
						$vTemp = str_replace("@02", str_replace("@02", $this->getvaluelink($lstArr[$i], $this->FormatView($vlv900, (int)$this->ArrView[$lstArr[$i]])), str_replace("@01", $vrow['lv001'], $lvHref)), $this->Align($lvTd, (int)$this->ArrView[$lstArr[$i]]));
						break;
					case 'lv027':
						if ($vrow[$lstArr[$i]] == 1 || $vrow[$lstArr[$i]] == 3) {
							$vrow[$lstArr[$i]] = $vrow[$lstArr[$i]] . $vrow['lv034'];
						}
						$vTemp = str_replace("@02", str_replace("@02", $this->getvaluelink($lstArr[$i], $this->FormatView($vrow[$lstArr[$i]], (int)$this->ArrView[$lstArr[$i]])), str_replace("@01", $vrow['lv001'], $lvHref)), $this->Align($lvTd, (int)$this->ArrView[$lstArr[$i]]));
						break;
					default:
						$vTemp = str_replace("@02", str_replace("@02", $this->getvaluelink($lstArr[$i], $this->FormatView($vrow[$lstArr[$i]], (int)$this->ArrView[$lstArr[$i]])), str_replace("@01", $vrow['lv001'], $lvHref)), $this->Align($lvTd, (int)$this->ArrView[$lstArr[$i]]));
						break;
				}
				$strL = $strL . $vTemp;
				$strSum = $strSum . $vTempsum;
			}
			if ($vrow['lv011'] == 4)		$strL = str_replace("@#44", "trangthaimau_green", $strL);
			elseif ($vrow['lv011'] == 3)		$strL = str_replace("@#44", "trangthaimau_orange", $strL);
			else if ($vrow['lv011'] == 2)		$strL = str_replace("@#44", "trangthaimau_blue", $strL);
			else if ($vrow['lv011'] == 1)		$strL = str_replace("@#44", "trangthaimau_xanhduong", $strL);
			else if ($vrow['lv011'] == 0)		$strL = str_replace("@#44", "trangthaimau_xanhtuoi", $strL);
			else if ($vrow['lv011'] == -1)		$strL = str_replace("@#44", "trangthaimau_purple", $strL);
			else if ($vrow['lv011'] == -2)		$strL = str_replace("@#44", "trangthaimau_red", $strL);
			else	$strL = str_replace("@#44", "", $strL);
			$strL = str_replace("@#04", "lvlineapproval_black", $strL);
			$strTr = $strTr . str_replace("@#01", $strL, str_replace("@02", $vrow['lv001'], str_replace("@03", $vorder, str_replace("@01", $vorder % 2, $lvTr))));
		}
		$strF = $strF . "</tr>";
		$strF = str_replace("<!--lv022-->", $this->FormatView($slv022, 10), $strF);
		$strF = str_replace("<!--lv108-->", $this->FormatView($slv108, 10), $strF);
		$strF = str_replace("<!--lv109-->", $this->FormatView($slv109, 10), $strF);
		$strF = str_replace("<!--lv110-->", $this->FormatView($slv110, 10), $strF);
		$strF = str_replace("<!--lv114-->", $this->FormatView($slv114, 10), $strF);
		$strF = str_replace("<!--lv900-->", $this->FormatView($slv900, (int)$this->ArrView['lv900']), $strF);
		$strF = str_replace("<!--lv901-->", $this->FormatView($slv901, (int)$this->ArrView['lv900']), $strF);
		$lvTable = str_replace("@#02", $strF, $lvTable);
		//$strTr=$strTr.str_replace("@#01",$strSum,str_replace("@02",$vrow['lv001'],str_replace("@03",$vorder,str_replace("@01",$vorder%2,$lvTrSum))));
		$strTrH = str_replace("@#01", $strH, $lvTrH);
		//$lvTotalNumTd=str_replace("@#01",$this->FormatView($vtotalnum,10),$lvTotalNumTd);
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
			ExportMore(value);
			//window.open('" . $this->Dir . "sl_lv0013/?lang=" . $this->lang . "&childfunc='+value+'&ID=" . base64_encode($this->lv002) . "','','width=800,height=600,left=200,top=100,screenX=0,screenY=100,resizable=yes,status=no,scrollbars=yes,menubar=yes');
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
	function LV_BuilListReportMini($lvList, $lvFrom, $lvChkAll, $lvChk, $curRow, $maxRows, $paging, $lvOrderList, $lvDateSort)
	{
		$this->isBuilList = 1;
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
		$lvTd = "<td  class=\"#04\" align=\"@#05\">@02</td>";
		$sqlS = "SELECT * FROM sl_lv0013 WHERE 1=1 and lv004 like '$lvDateSort%'  $strSort LIMIT $curRow, $maxRows";
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
			$slv109 = $slv109 + $vrow['lv109'];
			$slv110 = $slv110 + $vrow['lv110'];
			for ($i = 0; $i < count($lstArr); $i++) {
				if ($lstArr[$i] == "lv023") {
					$vTemp = str_replace("@02", $this->getvaluelink($lstArr[$i], $this->FormatView($this->LV_GetContractMoney($vrow['lv001'], $vrow['lv006']), (int)$this->ArrView[$lstArr[$i]])), $this->Align($lvTd, (int)$this->ArrView[$lstArr[$i]]));
				} elseif ($lstArr[$i] == "lv024") {
					$vTemp = str_replace("@02", $this->getvaluelink($lstArr[$i], $this->FormatView($this->LV_GetContractMoneyProduct($vrow['lv001'], $vrow['lv006']), (int)$this->ArrView[$lstArr[$i]])), $this->Align($lvTd, (int)$this->ArrView[$lstArr[$i]]));
				} elseif ($lstArr[$i] == "lv026") {
					$vTemp = str_replace("@02", $this->getvaluelink($lstArr[$i], $this->FormatView($this->LV_GetPTMoney($vrow['lv001']) - $this->LV_GetPCMoney($vrow['lv001']), (int)$this->ArrView[$lstArr[$i]])), $this->Align($lvTd, (int)$this->ArrView[$lstArr[$i]]));
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
	function LV_GetBLMoney($vContractID)
	{
		$lvsql = "select sum(PM.lv003) money,sum(PM.lv004) convertmoney,sum(PM.lv005) discount from ((select sum(A.lv004*A.lv006) lv003,sum(A.lv004*A.lv006*A.lv008/100) lv004,sum(A.lv004*A.lv006*A.lv011/100) lv005 from sl_lv0014 A inner join sl_lv0013 B on A.lv002=B.lv001  where 1=1 and B.lv001='$vContractID' )) PM ";
		$vresult = db_query($lvsql);
		$vrow = db_fetch_array($vresult);
		if ($vrow['convertmoney'] == 0) {
			if ($vrow['money'] == 0) return "0";
		}
		return $vrow['convertmoney'] + $vrow['money'] - $vrow['discount'];
	}
	//////////////////////Buil list////////////////////
	function LV_BuilPopup($lvList, $vArrCot, $bResult, $objid, $objvalue)
	{
		$lstArr = explode(",", $lvList);
		if ($this->lang == 'EN')
			$lvTrH = "<tr class=\"lvhtable\"><td width=1% class=\"lvhtable\">No</td>@#01</tr>";
		else

			$lvTrH = "<tr class=\"lvhtable\"><td width=1% class=\"lvhtable\">STT</td>@#01</tr>";
		$lvTr = "<tr class=\"lvlinehtable@01\"><td>@03</td>@#01</tr>";
		$lvTdH = "<td width=\"@01\" class=\"lvhtable\">@02</td>";
		$lvTd = "<td align=left><a href=\"javascript:PopupSelect('@01','$objid')\">@02</a></td>";
		$lvTdNo = "<td align=left nowrap><a href=\"javascript:PopupSelect('@01','$objid')\">@02</a></td>";
		for ($i = 0; $i < count($lstArr); $i++) {
			$vTemp = str_replace("@01", "", $lvTdH);
			$vTemp = str_replace("@02", $this->ArrPush[(int)$this->ArrGet[$lstArr[$i]]], $vTemp);
			$strH = $strH . $vTemp;
		}
		$strTrH = str_replace("@#01", $strH, $lvTrH);
		while ($vrow = db_fetch_array($bResult)) {
			$strL = "";
			$vorder++;
			for ($i = 0; $i < count($lstArr); $i++) {
				$vrow[$vArrCot[$lstArr[$i]]] = str_replace($objvalue, "<font color=\"#FF0000\">" . $objvalue . "</font>", $this->getvaluelink($lstArr[$i], $this->FormatView($vrow[$vArrCot[$lstArr[$i]]], (int)$this->ArrView[$lstArr[$i]])));
				switch ($lstArr[$i]) {
					default:
						$vTemp = str_replace("@02", $vrow[$vArrCot[$lstArr[$i]]], $this->Align(str_replace("@01", $vrow['lv001'], $lvTd), (int)$this->ArrView[$lstArr[$i]]));
						break;
				}

				$strL = $strL . $vTemp;
			}
			$strTr = $strTr . str_replace("@#01", $strL, str_replace("@02", $vrow['lv001'], str_replace("@03", $vorder, str_replace("@01", $vorder % 2, $lvTr))));
		}

		return $strTrH . $strTr;
	}
	function LV_BuilListReport($lvList, $lvFrom, $lvChkAll, $lvChk, $curRow, $maxRows, $paging, $lvOrderList, $lvSortNum)
	{
		$this->isBuilList = 1;
		if ($lvList == "") $lvList = $this->DefaultFieldList;
		$lvList = str_replace('lv199,', '', $lvList);
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
		$lvTrSum = "<tr class=\"lvlinehtable@01\">
			<td ><strong>" . ($this->ArrPush[99]) . "</strong></td>
			@#01
		</tr>
		";
		$lvTdH = "<td width=\"@01\" class=\"lvhtable\">@02</td>";
		$lvTd = "<td  class=\"#04\" align=\"@#05\">@02</td>";
		$lvTdF = "<td align=\"right\"><strong>@01</strong></td>";
		$strF = "<tr><td >&nbsp;</td>";
		$sqlS = "SELECT * FROM sl_lv0013 WHERE 1=1  " . $this->RptCondition . " $strSort ";
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
			$strSum = '';
			$slv109 = $slv109 + $vrow['lv109'];
			$slv110 = $slv110 + $vrow['lv110'];
			$vlv900 = $this->LV_LoadAmountDetail($vrow['lv001']);
			$vDiscount = $vrow['lv108'];
			$vTienDisCount = $vlv900 * $vDiscount / 100;
			$vCostShip = (float)$vrow['lv109'];
			$vVAT = (float)$vrow['lv110'];
			$vTienVAT = ($vlv900 - $vTienDisCount) * $vVAT / 100; //$vTienVAT=($vlv900-$vTienDisCount+$vCostShip)*$vVAT/100;
			$vlv900 = $vlv900 + $vTienVAT + $vCostShip - $vTienDisCount;
			$slv900 = $slv900 + $vlv900;
			for ($i = 0; $i < count($lstArr); $i++) {
				switch ($lstArr[$i]) {
					default:
						$vTemp = str_replace("@02", $this->getvaluelink($lstArr[$i], $this->FormatView($vrow[$lstArr[$i]], (int)$this->ArrView[$lstArr[$i]])), $this->Align($lvTd, (int)$this->ArrView[$lstArr[$i]]));
						break;
				}
				$vTempsum = str_replace("@02", '', $this->Align($lvSum, (int)$this->ArrView[$lstArr[$i]]));

				$strL = $strL . $vTemp;
				$strSum = $strSum . $vTempsum;
			}

			$strTr = $strTr . str_replace("@#01", $strL, str_replace("@02", $vrow['lv001'], str_replace("@03", $vorder, str_replace("@01", $vorder % 2, $lvTr))));
			if ($vrow['lv011'] == 1)		$strTr = str_replace("@#04", "lvlineapproval", $strTr);
			else $strTr = str_replace("@#04", "", $strTr);
		}
		$strF = $strF . "</tr>";
		$strF = str_replace("<!--lv022-->", $this->FormatView($slv022, 10), $strF);
		$strF = str_replace("<!--lv108-->", $this->FormatView($slv108, 10), $strF);
		$strF = str_replace("<!--lv109-->", $this->FormatView($slv109, 10), $strF);
		$strF = str_replace("<!--lv110-->", $this->FormatView($slv110, 10), $strF);
		$strF = str_replace("<!--lv114-->", $this->FormatView($slv114, 10), $strF);
		$strF = str_replace("<!--lv900-->", $this->FormatView($slv900, (int)$this->ArrView['lv900']), $strF);
		$strF = str_replace("<!--lv901-->", $this->FormatView($slv901, (int)$this->ArrView['lv900']), $strF);
		$lvTable = str_replace("@#02", $strF, $lvTable);
		$strTrH = str_replace("@#01", $strH, $lvTrH);
		return str_replace("@#01", $strTrH . ($strTr ?? ""), $lvTable);
	}
	//////////////////////Buil list////////////////////
	function LV_BuilListReportOther($lvList, $lvFrom, $lvChkAll, $lvChk, $curRow, $maxRows, $paging, $lvOrderList, $lvSortNum)
	{
		$this->isBuilList = 1;
		if ($lvList == "") $lvList = $this->DefaultFieldList;
		$lvList = str_replace('lv199,', '', $lvList);
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
		$lvTrSum = "<tr class=\"lvlinehtable@01\">
			<td ><strong>" . ($this->ArrPush[99]) . "</strong></td>
			@#01
		</tr>
		";
		$lvTdH = "<td width=\"@01\" class=\"lvhtable\">@02</td>";
		$lvTd = "<td  class=\"#04\" align=\"@#05\">@02</td>";
		$lvTdF = "<td align=\"right\"><strong>@01</strong></td>";
		$strF = "<tr><td >&nbsp;</td>";
		$vCondition = "";
		if ($this->lv010 != "") $vCondition = " and lv010='" . $this->lv010 . "'";
		$sqlS = "SELECT * FROM sl_lv0013 WHERE 1=1  " . $this->GetConditionView() . " $strSort";
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
			$strSum = '';
			$slv109 = $slv109 + $vrow['lv109'];
			$slv110 = $slv110 + $vrow['lv110'];
			$vlv900 = $this->LV_LoadAmountDetail($vrow['lv001']);
			$vDiscount = $vrow['lv108'];
			$vTienDisCount = $vlv900 * $vDiscount / 100;
			$vCostShip = (float)$vrow['lv109'];
			$vVAT = (float)$vrow['lv110'];
			$vTienVAT = ($vlv900 - $vTienDisCount) * $vVAT / 100; //$vTienVAT=($vlv900-$vTienDisCount+$vCostShip)*$vVAT/100;
			$vlv900 = $vlv900 + $vTienVAT + $vCostShip - $vTienDisCount;
			$slv900 = $slv900 + $vlv900;
			for ($i = 0; $i < count($lstArr); $i++) {
				switch ($lstArr[$i]) {
					case 'lv900':
						$vTemp = str_replace("@02", $this->getvaluelink($lstArr[$i], $this->FormatView($vlv900, (int)$this->ArrView[$lstArr[$i]])), $this->Align($lvTd, (int)$this->ArrView[$lstArr[$i]]));
						break;
					default:
						$vTemp = str_replace("@02", $this->getvaluelink($lstArr[$i], $this->FormatView($vrow[$lstArr[$i]], (int)$this->ArrView[$lstArr[$i]])), $this->Align($lvTd, (int)$this->ArrView[$lstArr[$i]]));
						break;
				}
				$strL = $strL . $vTemp;
			}
			$strTr = $strTr . str_replace("@#01", $strL, str_replace("@02", $vrow['lv001'], str_replace("@03", $vorder, str_replace("@01", $vorder % 2, $lvTr))));
			if ($vrow['lv011'] == 1)		$strTr = str_replace("@#04", "lvlineapproval", $strTr);
			else $strTr = str_replace("@#04", "", $strTr);
		}
		$strF = $strF . "</tr>";
		$strF = str_replace("<!--lv022-->", $this->FormatView($slv022, 10), $strF);
		$strF = str_replace("<!--lv108-->", $this->FormatView($slv108, 10), $strF);
		$strF = str_replace("<!--lv109-->", $this->FormatView($slv109, 10), $strF);
		$strF = str_replace("<!--lv110-->", $this->FormatView($slv110, 10), $strF);
		$strF = str_replace("<!--lv114-->", $this->FormatView($slv114, 10), $strF);
		$strF = str_replace("<!--lv900-->", $this->FormatView($slv900, (int)$this->ArrView['lv900']), $strF);
		$strF = str_replace("<!--lv901-->", $this->FormatView($slv901, (int)$this->ArrView['lv900']), $strF);
		$lvTable = str_replace("@#02", $strF, $lvTable);
		$strTrH = str_replace("@#01", $strH, $lvTrH);
		return str_replace("@#01", $strTrH . ($strTr ?? ""), $lvTable);
	}
	public function LV_LinkField($vFile, $vSelectID)
	{
		switch ($vFile) {
			case 'lv008':
			case 'lv002':
			case 'lv016':
				return ($this->CreateSelect($this->sqlcondition($vFile, $vSelectID), 0));
				break;
			default:
				return ($this->CreateSelect($this->sqlcondition($vFile, $vSelectID), 0));
				break;
		}
	}
	private function sqlcondition($vFile, $vSelectID)
	{
		$vsql = "";
		switch ($vFile) {
			case 'lv095':
				$vsql = "select lv001,lv002,IF(lv001='$vSelectID',1,0) lv003 from  jo_lv0015";
				break;
			case 'lv094':
				$vsql = "select lv001,lv002,IF(lv001='$vSelectID',1,0) lv003 from tc_lv0004";
				break;
			case 'lv000':
				$vsql = "select lv001,lv002,IF(concat(lv001,'')='$vSelectID',1,0) lv003 from  cr_lv0039";
				break;
			case 'lv905':
				$vsql = "select lv001,lv002,IF(lv001='$vSelectID',1,0) lv003 from  sl_lv0005";
				break;
			case 'lv907':
				$vsql = "select lv001,lv002,IF(lv001='$vSelectID',1,0) lv003 from  hr_lv0018 order by lv004";
				break;
			case 'lv002':
				$vsql = "select lv001,lv002,IF(lv001='$vSelectID',1,0) lv003 from  sl_lv0001";
				//$vsql="select lv001,lv002,IF(lv001='$vSelectID',1,0) lv003 from  sl_lv0001 where lv025='$this->LV_UserID'";
				break;

			case 'lv007':
				$vsql = "select lv001,lv002,IF(lv001='$vSelectID',1,0) lv003 from  cr_lv0260";
				break;
			case 'lv008':
				$vsql = "select lv001,lv002,IF(lv001='$vSelectID',1,0) lv003 from  cr_lv0007";
				break;
			case 'lv027':
				$vsql = "select lv001,lv002,IF(concat('',lv001)='$vSelectID',1,0) lv003 from  sl_lv0054";
				break;
			case 'lv012':
				if ($this->lv002 != "")
					$vsql = "select lv001,lv003 lv002,IF(lv001='$vSelectID',1,0) lv003 from  sl_lv0013 where lv002='$this->lv002'";
				else
					$vsql = "select lv001,lv003 lv002,IF(lv001='$vSelectID',1,0) lv003 from  sl_lv0013 ";
				break;
			case 'lv013':
				$vCondition = '';
				$vsql = "select lv001,lv002 lv002,IF(lv001='$vSelectID',1,0) lv003 from  hr_lv0020 where  1=1 $vCondition";
				break;
			case 'lv015':
				$vsql = "select lv001,concat(lv001,' ',lv002) lv002,IF(lv001='$vSelectID',1,0) lv003 from  cr_lv0023";
				break;
			case 'lv016':
				$vsql = "select lv001,lv002 lv002,IF(lv001='$vSelectID',1,0) lv003 from  hr_lv0020 ";
				break;
			case 'lv018':
				$vsql = "select lv001,lv002 lv002,IF(lv001='$vSelectID',1,0) lv003 from  hr_lv0020 ";
				break;
			case 'lv020':
				if ($this->GroupID != "")
					$vsql = "select lv001,lv002 lv002,IF(lv001='$vSelectID',1,0) lv003 from  hr_lv0020";
				else
					$vsql = "select lv001,lv002 lv002,IF(lv001='$vSelectID',1,0) lv003 from  hr_lv0020";
				break;
			case 'lv017':
				$vsql = "select lv001,lv001 lv002,IF(lv001='$vSelectID',1,0) lv003 from  sl_lv0034 order by lv003";
				break;
			case 'lv021':
				$vsql = "select lv001,lv001 lv002,IF(lv001='$vSelectID',1,0) lv003 from  sl_lv0008";
				break;
			case 'lv024':
				$vsql = "select lv001,lv002 lv002,IF(lv001='$vSelectID',1,0) lv003 from  hr_lv0241";
				break;
			case 'lv026':
				if ($this->GroupID != '')
					$vsql = "select lv001,lv012 lv002,IF(lv001='$vSelectID',1,0) lv003 from  sl_lv0013 where lv010='$this->GroupID' order by lv012";
				else
					$vsql = "select lv001,lv012 lv002,IF(lv001='$vSelectID',1,0) lv003 from  sl_lv0013  order by lv012";
				break;
			case 'lv030':
				$vsql = "select lv003 lv001,lv003 lv002,IF(lv003='$vSelectID',1,0) lv003 from  sl_lv0299 where lv002='$this->ContractorID'  order by lv009 desc";
				break;
			case 'lv104':
				$vCondition = '';
				$vsql = "select lv001,lv002 lv002,IF(lv001='$vSelectID',1,0) lv003 from  hr_lv0020 where  1=1 $vCondition";
				break;
			case 'lv103':
				$vsql = "select lv001,lv002 lv002,IF(lv001='$vSelectID',1,0) lv003 from  wh_lv0003 where lv018=3 and lv100=0";
				break;
			case 'lv102':
				$vsql = "select lv001,lv002 lv002,IF(lv001='$vSelectID',1,0) lv003 from  sl_lv0302";
				break;
			case 'lv101':
				$vsql = "select lv001,lv001 lv002,IF(lv001='$vSelectID',1,0) lv003 from  cr_lv0008";
				break;
			case 'lv107':
				$vsql = "select lv001,lv001 lv002,IF(lv001='$vSelectID',1,0) lv003 from  sl_lv0009";
				break;
			case 'lv888':
				$vsql = "select lv001,lv002 lv002,IF(lv001='$vSelectID',1,0) lv003 from  sl_lv0016 order by lv002";
				break;
			case 'lv113':
				$vsql = "select lv001,lv002 lv002,IF(concat(lv001,'')='$vSelectID',1,0) lv003 from  cr_lv0044 order by lv005";
				break;
			case 'lv114':
				$vsql = "select lv001,concat(lv004,' ',DATE_FORMAT(lv005,'%d/%m/%Y %H:%i')) lv002,IF(lv001='$vSelectID',1,0) lv003 from  cr_lv0005 where (lv006='$this->LV_UserID' or lv007='$this->LV_UserID')  and lv011='1' and ( lv003='PO' or lv003='HĐKT' or lv003='PLHĐ' or lv003='DEMO' or lv003='L' or lv003='M'  or lv003='S') and lv011='1'";
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
			case 'lv000':
				$vsql = "select lv001,lv002,IF(lv001='$vSelectID',1,0) lv003 from  cr_lv0039 where lv001='$vSelectID'";
				$lvopt = 0;
				break;
			case 'lv002':
				$vsql = "select lv001,lv002,IF(lv001='$vSelectID',1,0) lv003 from  sl_lv0001 where lv001='$vSelectID'";
				$lvopt = 0;
				break;
			case 'lv007':
				$lvopt = 0;
				$vsql = "select lv001,lv002,IF(lv001='$vSelectID',1,0) lv003 from  sl_lv0009 where lv001='$vSelectID'";
				break;
			case 'lv077':
				$lvopt = 0;
				$vsql = "select lv001,lv002,IF(lv001='$vSelectID',1,0) lv003 from  sl_lv0009 where lv001='$vSelectID'";
				break;
			case 'lv008':
				$lvopt = 0;
				$vsql = "select lv001,lv002,IF(lv001='$vSelectID',1,0) lv003 from  cr_lv0007 where lv001='$vSelectID'";
				break;
			case 'lv010':
				$lvopt = 2;
				$vsql = "select lv001,lv002,IF(lv001='$vSelectID',1,0) lv003 from  hr_lv0020 where lv001='$vSelectID'";
				break;
			case 'lv011':
				$vsql = "select lv001,lv002,IF(lv001='$vSelectID',1,0) lv003 from  sl_lv0054 where lv001='$vSelectID'";
				break;
			case 'lv012':
				$vsql = "select lv001,lv003 lv002,IF(lv001='$vSelectID',1,0) lv003 from  sl_lv0013 where lv002='$this->lv002'";
				break;
			case 'lv013':
				$lvopt = 0;
				$vsql = "select lv001,lv002,IF(lv001='$vSelectID',1,0) lv003 from  hr_lv0020 where lv001='$vSelectID'";
				break;
			case 'lv016':
				$lvopt = 2;
				$vsql = "select lv001,lv002,IF(lv001='$vSelectID',1,0) lv003 from  hr_lv0020 where lv001='$vSelectID'";
				break;
			case 'lv018':
				$lvopt = 2;
				$vsql = "select lv001,lv002,IF(lv001='$vSelectID',1,0) lv003 from  hr_lv0020 where lv001='$vSelectID'";
				break;
			case 'lv020':
				$lvopt = 2;
				$vsql = "select lv001,lv002,IF(lv001='$vSelectID',1,0) lv003 from  hr_lv0020 where lv001='$vSelectID'";
				break;
			case 'lv024':
				$lvopt = 2;
				$vsql = "select lv001,lv002,IF(lv001='$vSelectID',1,0) lv003 from  hr_lv0241 where lv001='$vSelectID'";
				break;
			case 'lv026':
				$lvopt = 0;
				$vsql = "select lv001,lv014 lv002,IF(lv001='$vSelectID',1,0) lv003 from  sl_lv0010 where lv001='$vSelectID'";
				break;
			case 'lv027':
				$vsql = "select lv001,lv002,IF(concat('',lv001)='$vSelectID',1,0) lv003 from  sl_lv0054 where lv001='$vSelectID'";
				break;
			case 'lv028':
				$vsql = "select A.lv001,(select concat(B.lv002,'(',B.lv001,')') from hr_lv0020 B where B.lv001=A.lv011)  lv002,IF(lv001='$vSelectID',1,0) lv003 from  wh_lv0010 A  where (A.lv005='CONTRACT' or A.lv005='RECONTRACT') and A.lv006='$vSelectID'";
				break;
			case 'lv029':
				$vsql = "select A.lv001,concat(lv009,',',lv007) lv002,IF(lv001='$vSelectID',1,0) lv003 from  sl_lv0001 A  where A.lv001='$vSelectID'";
				break;
			/*case 'lv017':
				$vsql="select lv001,lv002 lv002,IF(lv001='$vSelectID',1,0) lv003 from  sl_lv0034 where lv001='$vSelectID'";
				break;
			case 'lv101':
				$lvopt=0;
				$vsql="select lv001,lv002 lv002,IF(lv001='$vSelectID',1,0) lv003 from  hr_lv0002 where lv001='$vSelectID'";
				break;*/
			case 'lv102':
				$lvopt = 0;
				$vsql = "select lv001,lv002 lv002,IF(lv001='$vSelectID',1,0) lv003 from  sl_lv0302 where lv001='$vSelectID'";
			case 'lv103':
				$lvopt = 0;
				$vsql = "select lv001,lv002 lv002,IF(lv001='$vSelectID',1,0) lv003 from  wh_lv0003 where lv001='$vSelectID'";
				break;
			case 'lv104':
				$vsql = "select lv001,lv002 lv002,IF(lv001='$vSelectID',1,0) lv003 from  sl_lv0006 where lv001='$vSelectID'";
				break;
			case 'lv107':
				$vsql = "select lv001,lv002 lv002,IF(lv001='$vSelectID',1,0) lv003 from  sl_lv0009 where lv001='$vSelectID'";
				break;
			case 'lv021':
				$vsql = "select lv001,lv002 lv002,IF(lv001='$vSelectID',1,0) lv003 from  sl_lv0008 where lv001='$vSelectID'";
				break;
			case 'lv903':
				$vsql = "select lv001,lv002 lv002,IF(lv001='$vSelectID',1,0) lv003 from  sl_lv0007 where lv001='$vSelectID'";
				break;
			case 'lv113':
				$vsql = "select lv001,lv002 lv002,IF(lv001='$vSelectID',1,0) lv003 from  cr_lv0044 where lv001='$vSelectID'";
				break;
			case 'lv890':
				$vsql = "select lv001,lv007 lv002,IF(lv001='$vSelectID',1,0) lv003 from  cr_lv0044 where lv001='$vSelectID'";
				break;
			/*case 'lv114':
				$vsql="select A.lv001,B.lv002 lv002,'' lv003 from  cr_lv0005 A inner join cr_lv0004 B on A.lv002=B.lv001 where  A.lv001='$vSelectID'";
				break;*/
			case 'lv497':
				$vsql = "select lv001,lv005 lv002,IF(lv001='$vSelectID',1,0) lv003 from  sl_lv0016 where lv001='$vSelectID'";
				break;
			case 'lv094':
				$vsql = "select lv001,lv002,IF(lv001='$vSelectID',1,0) lv003 from  tc_lv0004 where lv001='$vSelectID'";
				break;
			case 'lv095':
				$vsql = "select lv001,lv002,IF(lv001='$vSelectID',1,0) lv003 from  jo_lv0015 where lv001='$vSelectID'";
				break;
			default:
				$vsql = "";
				break;
		}
		$lvopt = 0;
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
