<?php
/////////////coding ac_lv0034///////////////
class   cr_lv0285 extends lv_controler
{
	public $lv001 = null;
	public $lv002 = null;
	public $lv003 = null;
	public $lv004 = null;
	public $lv005 = null;
	public $lv006 = null;
	public $lv007 = null;
	public $lv008 = null;


	///////////
	public $DefaultFieldList = "lv005,lv003,lv007";
	////////////////////GetDate
	public $DateDefault = "1900-01-01";
	public $DateCurrent = "1900-01-01";
	public $Count = null;
	public $paging = null;
	public $lang = null;
	protected $objhelp = 'cr_lv0285';
	////////////
	var $ArrOther = array();
	var $ArrPush = array();
	var $ArrFunc = array();
	var $ArrGet = array("lv001" => "2", "lv002" => "3", "lv003" => "4", "lv004" => "5", "lv005" => "6", "lv006" => "7", "lv007" => "8");
	var $ArrView = array("lv001" => "0", "lv002" => "0", "lv003" => "10", "lv004" => "10", "lv005" => "0", "lv006" => "0", "lv007" => "0");
	var $ArrViewEnter = array("lv001" => "-1", "lv005" => "789", "lv006" => "99");
	var $Tables = array('lv005' => 'ac_lv0002', 'lv006' => 'ac_lv0002');
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
	protected function LV_CheckLock()
	{
		$lvsql = "select lv007 from cr_lv0285 B where  B.lv001='$this->lv002'";
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
		$vsql = "select * from  cr_lv0285";
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
		}
	}
	function LV_LoadID($vlv001)
	{
		$lvsql = "select * from  cr_lv0285 Where lv001='$vlv001'";
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
		}
	}
	function LV_Insert()
	{
		echo $lvsql = "insert into cr_lv0285 (lv002,lv003,lv004,lv005,lv006,lv007) values('$this->lv002','$this->lv003','$this->lv004','$this->lv005','$this->lv006','$this->lv007')";
		$vReturn = db_query($lvsql);
		if ($vReturn) {
			$this->InsertLogOperation($this->DateCurrent, 'cr_lv0285.insert', sof_escape_string($lvsql));
		}
		return $vReturn;
	}
	function LV_InsertTemp($vTemID, $vlv002, $vlv008, $vTKCo)
	{
		$lvsql = "insert into cr_lv0203(lv002,lv003,lv004,lv005,lv006,lv007) select '$vTemID',lv003,ROUND(lv003,0) lv004,lv005,'$vTKCo' lv006,lv007 from cr_lv0285 where lv002='$vlv002' and lv003>0";
		$vReturn = db_query($lvsql);
		if ($vReturn) {
			$this->InsertLogOperation($this->DateCurrent, 'cr_lv0203.insert', sof_escape_string($lvsql));
			$this->LV_DeleteTemp($vlv002);
		}
		return $vReturn;
	}
	function LV_InsertCAL($vCalID, $vUserID, $vlv005, $vlv006, $vlv011, $vlv012)
	{
		if ($vlv010 == '') $vlv010 = '1111';
		//$this->LV_DeleteTemp($vUserID);
		$lvsql = "insert into cr_lv0285(lv002,lv003,lv004,lv005,lv006,lv007,lv008) select '$vUserID' ,sum(A.lv004*A.lv006+A.lv016),sum(A.lv009+A.lv017),'$vlv005','$vlv006','SO(Bán hàng)','$vCalID' from ac_lv0006 A inner join ac_lv0004 B on A.lv002=B.lv001  where 1=1 and A.lv002='$vCalID'";
		$vresult = db_query($lvsql);
		return $vresult;
	}
	function LV_DeleteTemp($vlv002)
	{
		$lvsql = "DELETE FROM cr_lv0285  WHERE cr_lv0285.lv002='$vlv002'";
		$vReturn = db_query($lvsql);
		return $vReturn;
	}
	function LV_Update()
	{
		if ($this->isEdit == 0) return false;
		$lvsql = "Update cr_lv0285 set lv002='$this->lv002',lv003='$this->lv003',lv004='$this->lv004',lv005='$this->lv005',lv006='$this->lv006',lv007='$this->lv007' where  lv001='$this->lv001';";
		$vReturn = db_query($lvsql);
		if ($vReturn) $this->InsertLogOperation($this->DateCurrent, 'cr_lv0285.update', sof_escape_string($lvsql));
		return $vReturn;
	}
	function LV_UpdateEdit($vOldlv004, $vOldlv006)
	{
		if ($this->isEdit == 0) return false;
		$lvsql = "Update cr_lv0285 set lv002='$this->lv002',lv003='$this->lv003',lv004='$this->lv004',lv005='$this->lv005',lv006='$this->lv006',lv007='$this->lv007' where  lv001='$this->lv001';";
		$vReturn = db_query($lvsql);
		if ($vReturn) {
			$this->InsertLogOperation($this->DateCurrent, 'cr_lv0285.update', sof_escape_string($lvsql));
		}
		return $vReturn;
	}

	function LV_UpdateEditLoaiUng()
	{
		if ($this->isEdit == 0) return false;
		if ($this->LoaiTamUng == 3) {
			$lvsql = "DELETE FROM cr_lv0285  WHERE lv002='$this->LV_UserID' and lv003<>'1412'";
			$vReturn = db_query($lvsql);
			$lvsql = "select count(*) num from  cr_lv0285 Where lv002='$this->LV_UserID'";
			$vresult = db_query($lvsql);
			$vrow = db_fetch_array($vresult);
			if ($vrow['num'] == 0) {
				$vsql = "insert into cr_lv0285(lv002,lv005,lv003,lv007) values('$this->LV_UserID','1412','0','Tạm ứng lương')";
				$vReturn = db_query($vsql);
				if ($vReturn) {
					$this->InsertLogOperation($this->DateCurrent, 'cr_lv0285.insert', sof_escape_string($lvsql));
				}
			}
		} else {
			if ($this->LoaiTamUng == 2) {
				$lvsql = "Update cr_lv0285 set lv005=REPLACE(lv005,'1412','641'),lv007=REPLACE(lv007,'Tạm ứng lương','Thanh toán') where lv002='$this->LV_UserID';";
				db_query($lvsql);
				if ($vReturn) {
					$this->InsertLogOperation($this->DateCurrent, 'cr_lv0285.udpate', sof_escape_string($lvsql));
				}
				$lvsql = "Update cr_lv0285 set lv005=REPLACE(lv005,'141','641') where lv002='$this->LV_UserID';";
			} else {
				$lvsql = "Update cr_lv0285 set lv005=REPLACE(lv005,'1412','141'),lv007=REPLACE(lv007,'Tạm ứng lương','Tiền ứng') where lv002='$this->LV_UserID';";
				db_query($lvsql);
				if ($vReturn) {
					$this->InsertLogOperation($this->DateCurrent, 'cr_lv0285.udpate', sof_escape_string($lvsql));
				}
				$lvsql = "Update cr_lv0285 set lv005=REPLACE(lv005,'641','141') where lv002='$this->LV_UserID';";
			}
			$vReturn = db_query($lvsql);
			if ($vReturn) {
				$this->InsertLogOperation($this->DateCurrent, 'cr_lv0285.update', sof_escape_string($lvsql));
				if ($this->LoaiTamUng == 2)
					$lvsql = "Update cr_lv0285 set lv007=REPLACE(lv007,'Tiền ứng','Thanh toán') where lv007='Tiền ứng';";
				else
					$lvsql = "Update cr_lv0285 set lv007=REPLACE(lv007,'Thanh toán','Tiền ứng') where lv007='Thanh toán';";
				$vReturn = db_query($lvsql);
				if ($vReturn) {
					$this->InsertLogOperation($this->DateCurrent, 'cr_lv0285.update', sof_escape_string($lvsql));
				}
			}
		}
		return $vReturn;
	}

	function LV_CheckLocked($vlv002)
	{
		$lvsql = "select lv016 from  ac_lv0004 Where lv001='$vlv002'";
		$vresult = db_query($lvsql);
		if ($vresult) {
			$vrow = db_fetch_array($vresult);
			if ($vrow) {
				if ($vrow['lv016'] <= 0) return true;
				else
					return false;
			} else
				return false;
		} else
			return false;
	}
	function LV_Delete($lvarr)
	{
		if ($this->isDel == 0) return false;
		$lvsql = "DELETE FROM cr_lv0285  WHERE cr_lv0285.lv001 IN ($lvarr)";
		$vReturn = db_query($lvsql);
		if ($vReturn) {
			$this->InsertLogOperation($this->DateCurrent, 'cr_lv0285.delete', sof_escape_string($lvsql));
		}
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
		if ($this->lv002 != "") $strCondi = $strCondi . " and lv002  = '$this->lv002'";
		if ($this->lv003 != "") $strCondi = $strCondi . " and lv003  like '%$this->lv003%'";
		if ($this->lv004 != "") $strCondi = $strCondi . " and lv004  like '%$this->lv004%'";
		if ($this->lv005 != "") $strCondi = $strCondi . " and lv005  like '%$this->lv005%'";
		if ($this->lv006 != "") $strCondi = $strCondi . " and lv006  like '%$this->lv006%'";
		if ($this->lv007 != "")  $strCondi = $strCondi . " and lv007 like '%$this->lv007%'";
		return $strCondi;
	}
	////////////////Count///////////////////////////
	function GetCount()
	{
		$sqlC = "SELECT COUNT(*) AS nums FROM cr_lv0285 WHERE 1=1 " . $this->GetCondition();
		$bResultC = db_query($sqlC);
		$arrRowC = db_fetch_array($bResultC);
		return $arrRowC['nums'];
	}
	function GetCountUser($vlv002, $vlv008)
	{
		$sqlC = "SELECT COUNT(*) AS nums FROM cr_lv0285 WHERE lv002='$vlv002'";
		//$sqlC = "SELECT COUNT(*) AS nums FROM cr_lv0285 WHERE lv002='$vlv002' and lv008='$vlv008'";
		$bResultC = db_query($sqlC);
		$arrRowC = db_fetch_array($bResultC);
		return $arrRowC['nums'];
	}
	function GetSumUser($vlv002)
	{
		echo $sqlC = "SELECT sum(lv003) AS nums FROM cr_lv0285 WHERE lv002='$vlv002'";
		//$sqlC = "SELECT COUNT(*) AS nums FROM cr_lv0285 WHERE lv002='$vlv002' and lv008='$vlv008'";
		$bResultC = db_query($sqlC);
		$arrRowC = db_fetch_array($bResultC);
		return $arrRowC['nums'];
	}
	//////////////////////Buil list////////////////////
	//////////////////////Buil list////////////////////
	function LV_BuilList($lvList, $lvFrom, $lvChkAll, $lvChk, $curRow, $maxRows, $paging, $lvOrderList, $lvSortNum)
	{
		if ($curRow < 0) $curRow = 0;
		$this->LV_CheckLock();
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
		$lvTdF = "<td align=\"right\"><strong>@01</strong></td>";
		$strF = "<tr><td colspan=\"2\">&nbsp;</td>";
		$lvTd = "<td align=@#05>@02</td>";
		$sqlS = "SELECT * FROM cr_lv0285 WHERE 1=1  " . $this->GetCondition() . " $strSort LIMIT $curRow, $maxRows";
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
			//if($this->ArrViewEnter[$vField] = $this->ArrViewEnter[$vField] ?? 0) $this->ArrViewEnter[$vField]=0;
			$vStringNumber = "";
			$vWidth = ";width:100%;";
			switch ($vField) {
				case 'lv003':
					$vStringNumber = ' onkeyup="CalculateM()" onchange="CalculateM();" ';
					$vWidth = ";width:160px;";
					break;
				case 'lv005':
					$vStringNumber = ' onblur="LoadAccName(this);" ';
					break;
				case 'lv007':
					$vWidth = ";width:300px;";
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
								<input type="text" autocomplete="off" class="search_img_btn" name="qxtlv014_search" id="qxtlv014_search" style="width:100%;height:22px;min-width:60px;" onKeyUp="LoadPopupParent(this,\'qxt' . $vField . '\',\'' . $this->Tables[$vField] . '\',\'concat(lv002,@! @!,lv001)\',2)" onFocus="LoadPopupParent(this,\'qxt' . $vField . '\',\'' . $this->Tables[$vField] . '\',\'concat(lv002,@! @!,lv001)\',2)" tabindex="2" >
								<div id="lv_popup' . (($this->isPopupPlus == 1) ? '' : $this->isPopupPlus) . '" lang="lv_popup' . (($this->isPopupPlus == 1) ? '' : $this->isPopupPlus) . '"> </div>	
							</td>
							<td>
								<select ' . $vStringNumber . ' class="selenterquick" name="qxt' . $vField . '" id="qxt' . $vField . '" tabindex="2" style="' . $vWidth . ';min-width:100px" onKeyPress="return CheckKey(event,7)">
									<option value="">...</option>
									' . $this->LV_LinkField($vField, $this->Values[$vField]) . '
								</select>
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
								<input ' . $vStringNumber . ' autocomplete="off" class="txtenterquick" type="text" autocomplete="off" style="' . $vWidth . ';min-width:30px" name="qxt' . $vField . '" id="qxt' . $vField . '" onKeyUp="LoadPopupParentTabIndex(event,this,\'qxt' . $vField . '\',\'' . $this->Tables[$vField] . '\',\'concat(lv002,@! @!,lv001)\')"  onKeyPress="return CheckKey(event,7)" tabindex="2"  value="' . $this->Values[$vField] . '">
								<div id="lv_popup' . (($this->isPopupPlus == 1) ? '' : $this->isPopupPlus) . '" lang="lv_popup' . $this->isPopupPlus . '"> </div>						  
								</li>
							</ul>';
					$this->isPopupPlus++;
					$vTempEnter = str_replace("@02", $vstr, $this->Align($lvTd, (int)($this->ArrView[$lstArr[$i]] ?? 0)));
					break;
				case 999:
					if ($this->isPopupPlus == 0) $this->isPopupPlus = 1;
					$vstr = '<ul style="width:100%" id="pop-nav' . (($this->isPopupPlus == 1) ? '' : $this->isPopupPlus) . '" lang="pop-nav' . $this->isPopupPlus . '" onMouseOver="ChangeName(this,' . $this->isPopupPlus . ')" onKeyUp="ChangeName(this,' . $this->isPopupPlus . ')"> <li class="menupopT">
								<input ' . $vStringNumber . ' autocomplete="off" class="txtenterquick" type="text" autocomplete="off" style="' . $vWidth . ';min-width:30px" name="qxt' . $vField . '" id="qxt' . $vField . '" onKeyUp="LoadSelfNextParent(this,\'qxt' . $vField . '\',\'' . $this->Tables[$vField] . '\',\'' . $this->TableLinkReturn[$vField] . '\',\'' . $this->TableLink[$vField] . '\')"  onKeyPress="return CheckKey(event,7)" tabindex="2" value="' . $this->Values[$vField] . '" onblur="if(this.value.substr(this.value.length-1,this.value.length)==\',\') {this.value=this.value.substr(0,this.value.length-1);};">
								<div id="lv_popup' . (($this->isPopupPlus == 1) ? '' : $this->isPopupPlus) . '" lang="lv_popup' . $this->isPopupPlus . '"> </div>						  
								</li>
							</ul>';
					$this->isPopupPlus++;
					$vTempEnter = str_replace("@02", $vstr, $this->Align($lvTd, (int)($this->ArrView[$lstArr[$i]] ?? 0)));
					break;
				case 88:
					$vstr = '<select ' . $vStringNumber . ' class="selenterquick" name="qxt' . $vField . '" id="qxt' . $vField . '" tabindex="2" style="' . $vWidth . ';min-width:30px" onKeyPress="return CheckKey(event,7)">' . $this->LV_LinkField($vField, $this->Values[$vField]) . '</select>';
					$vTempEnter = str_replace("@02", $vstr, $this->Align($lvTd, (int)($this->ArrView[$lstArr[$i]] ?? 0)));
					break;
				case 89:
					$vstr = '<select ' . $vStringNumber . ' class="selenterquick" name="qxt' . $vField . '" id="qxt' . $vField . '" tabindex="2" style="' . $vWidth . ';min-width:30px" onKeyPress="return CheckKey(event,7)">
							<option value="">...</option>
						' . $this->LV_LinkField($vField, $this->Values[$vField]) . '</select>';
					$vTempEnter = str_replace("@02", $vstr, $this->Align($lvTd, (int)($this->ArrView[$lstArr[$i]] ?? 0)));
					break;
				case 4:
					$vstr = '<table><tr><td><input autocomplete="off" class="txtenterquick"  autocomplete="off" name="qxt' . $vField . '_1" type="text" id="qxt' . $vField . '_1" value="' . $this->Values[$vField] . '" tabindex="2" maxlength="32" style="' . $vWidth . ';min-width:80px" onKeyPress="return CheckKey(event,7)" ondblclick="if(self.gfPop)gfPop.fPopCalendar(this);return false;"></td><td><input class="txtenterquick"  autocomplete="off" name="qxt' . $vField . '_2" type="text" id="qxt' . $vField . '_2" value="' . $this->Values[$vField] . '" tabindex="2" maxlength="32" style="width:50%;min-width:60px" onKeyPress="return CheckKey(event,7)" ></td></tr></table>';
					$vTempEnter = str_replace("@02", $vstr, $this->Align($lvTd, (int)($this->ArrView[$lstArr[$i]] ?? 0)));
					break;
				case 22:
				case 2:
					$vstr = '<input autocomplete="off" class="txtenterquick"  autocomplete="off" name="qxt' . $vField . '" type="text" id="qxt' . $vField . '" value="' . $this->Values[$vField] . '" tabindex="2" maxlength="32" style="' . $vWidth . ';min-width:60px" onKeyPress="return CheckKey(event,7)" ondblclick="if(self.gfPop)gfPop.fPopCalendar(this);return false;">';
					$vTempEnter = str_replace("@02", $vstr, $this->Align($lvTd, (int)($this->ArrView[$lstArr[$i]] ?? 0)));
					break;
				case 33:
					$vstr = '<input autocomplete="off" class="txtenterquick" name="qxt' . $vField . '" type="checkbox" id="qxt' . $vField . '" value="1" ' . (($this->Values[$vField] == 1) ? 'checked="true"' : '') . ' tabindex="2" style="' . $vWidth . ';min-width:30px;text-align:center;" onKeyPress="return CheckKey(event,7)">';
					$vTempEnter = str_replace("@02", $vstr, $this->Align($lvTd, (int)($this->ArrView[$lstArr[$i]] ?? 0)));
					break;
				case 0:
					$vstr = '<input ' . $vStringNumber . ' autocomplete="off" class="txtenterquick" name="qxt' . $vField . '" type="text" id="qxt' . $vField . '" value="' . $this->Values[$vField] . '" tabindex="2" style="' . $vWidth . ';min-width:30px;text-align:center;" onKeyPress="return CheckKey(event,7)">';
					$vTempEnter = str_replace("@02", $vstr, $this->Align($lvTd, (int)($this->ArrView[$lstArr[$i]] ?? 0)));
					break;
				default:
					$vTempEnter = "<td>&nbsp;</td>";
					break;
			}
			$strTrEnter = $strTrEnter . $vTempEnter;
			$strTrEnterEmpty = $strTrEnterEmpty . "<td>&nbsp;</td>";
		}
		if ($this->isAdd == 1)
			$strTrEnter = "<tr class='entermobil'><td colspan='2'>" . '<img tabindex="2" border="0" title="Add" class="imgButton" onclick="Save()" onmouseout="this.src=\'' . $this->Dir . '../images/iconcontrol/btn_add.jpg\';" onmouseover="this.src=\'' . $this->Dir . '../images/iconcontrol/btn_add_02.jpg\';" src="' . $this->Dir . '../images/iconcontrol/btn_add.jpg" onkeypress="return CheckKey(event,11)">' . "</td>" . $strTrEnter . "</tr>";
		else
			$strTrEnter = "";

		while ($vrow = db_fetch_array($bResult)) {
			$strL = "";
			$vorder++;
			$vSumLv003 = $vSumLv003 + $vrow['lv003'];
			$vSumLv004 = $vSumLv004 + $vrow['lv004'];
			for ($i = 0; $i < count($lstArr); $i++) {
				switch ($lstArr[$i]) {
					case 'lv003':
						if ($this->GetEdit() == 1) {
							$lvTdTextBox = "<td align=center><input class='txtenterquick' type=\"textbox\" value=\"" . $this->FormatView($vrow['lv003'], 10) . "\" title=\"" . $vrow['lv003'] . "\" @03 onblur=\"UpdateText(this,'" . $vrow['lv001'] . "',3);SetGiaTri(this);\" onfocus=\"LayLaiGiaTri(this)\"  style=\"min-width:85px;width:100%;text-align:center;\" tabindex=\"2\" maxlength=\"32\"   onKeyPress=\"return CheckKey(event,7)\"/></td>";
							$vTemp = str_replace("@02", $this->FormatView($vrow[$lstArr[$i]], 0), $this->Align(str_replace("@01", $vrow['lv001'], $lvTdTextBox), (int)$this->ArrView[$lstArr[$i]]));
						} else
							$vTemp = str_replace("@02", $this->getvaluelink($lstArr[$i], $this->FormatView($vrow[$lstArr[$i]], (int)$this->ArrView[$lstArr[$i]])), $this->Align($lvTd, (int)$this->ArrView[$lstArr[$i]]));
						break;
					case 'lv007':
						if ($this->GetEdit() == 1) {
							$lvTdTextBox = "<td align=center><input class='txtenterquick' type=\"textbox\" value=\"" . $vrow['lv007'] . "\" @03 onblur=\"UpdateText(this,'" . $vrow['lv001'] . "',7);\" style=\"min-width:130px;width:100%;text-align:center;\" tabindex=\"2\"   onKeyPress=\"return CheckKey(event,7)\"/></td>";
							$vTemp = str_replace("@02", $this->FormatView($vrow[$lstArr[$i]], 0), $this->Align(str_replace("@01", $vrow['lv001'], $lvTdTextBox), (int)$this->ArrView[$lstArr[$i]]));
						} else
							$vTemp = str_replace("@02", $this->getvaluelink($lstArr[$i], $this->FormatView($vrow[$lstArr[$i]], (int)$this->ArrView[$lstArr[$i]])), $this->Align($lvTd, (int)$this->ArrView[$lstArr[$i]]));
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
		$strF = str_replace("<!--lv003-->", $this->FormatView($vSumLv003, 10), $strF);
		$strF = str_replace("<!--lv004-->", $this->FormatView($vSumLv004, 10), $strF);
		$lvTable = str_replace("@#02", $strF, $lvTable);
		$strTrH = str_replace("@#01", $strH, $lvTrH);
		return str_replace("@#01", $strTrH . $strTrEnter . $strTr, $lvTable);
	}
	function LV_BuilList1111($lvList, $lvFrom, $lvChkAll, $lvChk, $curRow, $maxRows, $paging, $lvOrderList, $lvSortNum)
	{
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
	<div id=\"func_id\" style='position:relative;background:#f2f2f2'><div style=\"float:left\">" . $this->TabFunction($lvFrom, $lvList, $maxRows) . "</div><div style=\"float:right\">" . $this->ListFieldSave($lvFrom, $lvList, $maxRows, $lvOrderList, $lvSortNum) . "</div><div style='float:right'>&nbsp;&nbsp;&nbsp;</div><div style='float:right'>" . $this->ListFieldExport($lvFrom, $lvList, $maxRows) . "</div></div><div style='height:35px'></div><table  align=\"center\" class=\"lvtable\"><!--<tr ><td colspan=\"" . (2 + count($lstArr)) . "\" class=\"lvTTable\">" . $this->ArrPush[0] . "</td></tr>-->
	@#01
	<tr ><td colspan=\"" . (count($lstArr) + 2) . "\">$paging</td></tr>
	</table>
	";
		$lvTrH = "<tr class=\"lvhtable\">
		<td width=1% class=\"lvhtable\">" . $this->ArrPush[1] . "</td>
		<td width=1%><input name=\"$lvChkAll\" type=\"checkbox\" id=\"$lvChkAll\" onclick=\"DoChkAll($lvFrom, '$lvChk', this)\" value=\"$curRow\" tabindex=\"2\"/></td>
		@#01
	</tr>
	";
		$lvTr = "<tr class=\"lvlinehtable@01\"><td width=1% onclick=\"Select_Check('$lvChk@03',$lvFrom, '$lvChk', '$lvChkAll')\">@03</td>	<td width=1%><input name=\"$lvChk\" type=\"checkbox\" id=\"$lvChk@03\" onclick=\"CheckOne($lvFrom, '$lvChk', '$lvChkAll', this)\" value=\"@02\" tabindex=\"2\"  onKeyUp=\"return CheckKeyCheck(event,2,'$lvChk',$lvFrom, '$lvChk', '$lvChkAll',@03)\"/></td>@#01</tr>";
		$lvHref = "<a href=\"javascript:FunctRunning1('@01')\" class=@#04 style=\"text-decoration:none\">@02</a>";
		$lvTdH = "<td width=\"@01\" class=\"lvhtable\">@02</td>";
		$lvTd = "<td align=@#05>@02</td>";
		$sqlS = "SELECT * FROM cr_lv0285 WHERE 1=1  " . $this->GetCondition() . " $strSort LIMIT $curRow, $maxRows";
		$vorder = $curRow;
		$bResult = db_query($sqlS);
		$this->Count = db_num_rows($bResult);
		$strTrH = "";
		$strH = "";
		for ($i = 0; $i < count($lstArr); $i++) {
			$vTemp = str_replace("@01", "", $lvTdH);
			$vTemp = str_replace("@02", $this->ArrPush[(int)$this->ArrGet[$lstArr[$i]]], $vTemp);
			$strH = $strH . $vTemp;
			$vField = $lstArr[$i];
			$vStringNumber = "";
			//if($this->ArrViewEnter[$vField] = $this->ArrViewEnter[$vField] ?? 0) $this->ArrViewEnter[$vField]=0;
			$vStringNumber = "";
			switch ($vField) {
				case 'lv003':
					$vStringNumber = ' onkeyup="CalculateM()" onchange="CalculateM();" ';
					break;
			}
			$vStringName = '';
			if ($vField == 'lv005') $vStringName = ' onblur="LoadAccName(this);" ';
			switch ($this->ArrViewEnter[$vField]) {
				case 99:
					if ($this->isPopupPlus == 0) $this->isPopupPlus = 1;
					$vstr = '<ul style="width:100%" id="pop-nav' . (($this->isPopupPlus == 1) ? '' : $this->isPopupPlus) . '" lang="pop-nav' . $this->isPopupPlus . '" onMouseOver="ChangeName(this,' . $this->isPopupPlus . ')" onKeyUp="ChangeName(this,' . $this->isPopupPlus . ')"> <li class="menupopT">
								<input autocomplete="off" class="txtenterquick" type="text" autocomplete="off" style="width:100%;min-width:30px" name="qxt' . $vField . '" id="qxt' . $vField . '" onKeyUp="LoadPopupParentTabIndex(event,this,\'qxt' . $vField . '\',\'' . $this->Tables[$vField] . '\',\'concat(lv002,@! @!,lv001)\')"  onKeyPress="return CheckKey(event,7)" tabindex="2" onblur="LoadSource(this.value)" value="' . $this->Values[$vField] . '">
								<div id="lv_popup' . (($this->isPopupPlus == 1) ? '' : $this->isPopupPlus) . '" lang="lv_popup' . $this->isPopupPlus . '"> </div>						  
								</li>
							</ul>';
					$this->isPopupPlus++;
					$vTempEnter = str_replace("@02", $vstr, $this->Align($lvTd, (int)($this->ArrView[$lstArr[$i]] ?? 0)));
					break;
				case 999:
					if ($this->isPopupPlus == 0) $this->isPopupPlus = 1;
					$vstr = '<ul style="width:100%" id="pop-nav' . (($this->isPopupPlus == 1) ? '' : $this->isPopupPlus) . '" lang="pop-nav' . $this->isPopupPlus . '" onMouseOver="ChangeName(this,' . $this->isPopupPlus . ')" onKeyUp="ChangeName(this,' . $this->isPopupPlus . ')"> <li class="menupopT">
								<input autocomplete="off" class="txtenterquick" type="text" autocomplete="off" style="width:100%;min-width:30px" name="qxt' . $vField . '" id="qxt' . $vField . '" onKeyUp="LoadSelfNextParent(this,\'qxt' . $vField . '\',\'' . $this->Tables[$vField] . '\',\'' . $this->TableLinkReturn[$vField] . '\',\'' . $this->TableLink[$vField] . '\')"  onKeyPress="return CheckKey(event,7)" tabindex="2" value="' . $this->Values[$vField] . '" onblur="if(this.value.substr(this.value.length-1,this.value.length)==\',\') {this.value=this.value.substr(0,this.value.length-1);};">
								<div id="lv_popup' . (($this->isPopupPlus == 1) ? '' : $this->isPopupPlus) . '" lang="lv_popup' . $this->isPopupPlus . '"> </div>						  
								</li>
							</ul>';
					$this->isPopupPlus++;
					$vTempEnter = str_replace("@02", $vstr, $this->Align($lvTd, (int)($this->ArrView[$lstArr[$i]] ?? 0)));
					break;
				case 88:
					$vstr = '<select ' . $vStringName . ' class="selenterquick" name="qxt' . $vField . '" id="qxt' . $vField . '" tabindex="2" style="width:100%;min-width:30px" onKeyPress="return CheckKey(event,7)">' . $this->LV_LinkField($vField, $this->Values[$vField]) . '</select>';
					$vTempEnter = str_replace("@02", $vstr, $this->Align($lvTd, (int)($this->ArrView[$lstArr[$i]] ?? 0)));
					break;
				case 89:
					$vstr = '<select ' . $vStringName . ' class="selenterquick" name="qxt' . $vField . '" id="qxt' . $vField . '" tabindex="2" style="width:100%;min-width:30px" onKeyPress="return CheckKey(event,7)">
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
					$vTempEnter = "<td>&nbsp;</td>";
					break;
			}
			$strTrEnter = $strTrEnter . $vTempEnter;
			$strTrEnterEmpty = $strTrEnterEmpty . "<td>&nbsp;</td>";
		}
		if ($this->isAdd == 1)
			$strTrEnter = "<tr class='entermobil'><td colspan='2'>" . '<img tabindex="2" border="0" title="Add" class="imgButton" onclick="Save()" onmouseout="this.src=\'' . $this->Dir . '../images/iconcontrol/btn_add.jpg\';" onmouseover="this.src=\'' . $this->Dir . '../images/iconcontrol/btn_add_02.jpg\';" src="' . $this->Dir . '../images/iconcontrol/btn_add.jpg" onkeypress="return CheckKey(event,11)">' . "</td>" . $strTrEnter . "</tr>";
		else
			$strTrEnter = ""; //"<tr class='entermobil'><td colspan='2'>".'&nbsp;'."</td>".$strTrEnterEmpty."</tr>";
		while ($vrow = db_fetch_array($bResult)) {
			$strL = "";
			$vorder++;
			for ($i = 0; $i < count($lstArr); $i++) {
				switch ($lstArr[$i]) {
					case 'lv003':
						if ($this->GetEdit() == 1) {
							$lvTdTextBox = "<td align=center><input class='txtenterquick' type=\"textbox\" value=\"" . $this->FormatView($vrow['lv003'], 10) . "\" title=\"" . $vrow['lv003'] . "\" @03 onblur=\"UpdateText(this,'" . $vrow['lv001'] . "',3);SetGiaTri(this);\" onfocus=\"LayLaiGiaTri(this)\"  style=\"min-width:85px;width:100%;text-align:center;\" tabindex=\"2\" maxlength=\"32\"   onKeyPress=\"return CheckKey(event,7)\"/></td>";
							$vTemp = str_replace("@02", $this->FormatView($vrow[$lstArr[$i]], 0), $this->Align(str_replace("@01", $vrow['lv001'], $lvTdTextBox), (int)$this->ArrView[$lstArr[$i]]));
						} else
							$vTemp = str_replace("@02", $this->getvaluelink($lstArr[$i], $this->FormatView($vrow[$lstArr[$i]], (int)$this->ArrView[$lstArr[$i]])), $this->Align($lvTd, (int)$this->ArrView[$lstArr[$i]]));
						break;
					case 'lv007':
						if ($this->GetEdit() == 1) {
							$lvTdTextBox = "<td align=center><input class='txtenterquick' type=\"textbox\" value=\"" . $vrow['lv007'] . "\" @03 onblur=\"UpdateText(this,'" . $vrow['lv001'] . "',7);\" style=\"min-width:130px;width:100%;text-align:center;\" tabindex=\"2\" maxlength=\"32\"   onKeyPress=\"return CheckKey(event,7)\"/></td>";
							$vTemp = str_replace("@02", $this->FormatView($vrow[$lstArr[$i]], 0), $this->Align(str_replace("@01", $vrow['lv001'], $lvTdTextBox), (int)$this->ArrView[$lstArr[$i]]));
						} else
							$vTemp = str_replace("@02", $this->getvaluelink($lstArr[$i], $this->FormatView($vrow[$lstArr[$i]], (int)$this->ArrView[$lstArr[$i]])), $this->Align($lvTd, (int)$this->ArrView[$lstArr[$i]]));
						break;


					default:
						$vTemp = str_replace("@02", $this->getvaluelink($lstArr[$i], $this->FormatView($vrow[$lstArr[$i]], (int)$this->ArrView[$lstArr[$i]])), $this->Align($lvTd, (int)$this->ArrView[$lstArr[$i]]));
						break;
				}
				$strL = $strL . $vTemp;
			}
			$strTr = $strTr . str_replace("@#01", $strL, str_replace("@02", $vrow['lv001'], str_replace("@03", $vorder, str_replace("@01", $vorder % 2, $lvTr))));
		}
		$strTrH = str_replace("@#01", $strH, $lvTrH);
		return str_replace("@#01", $strTrH . $strTrEnter . $strTr, $lvTable);
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
			window.open('" . $this->Dir . "cr_lv0285/?lang=" . $this->lang . "&childdetailfunc='+value+'&ID=" . base64_encode($this->lv002) . "','','width=800,height=600,left=200,top=100,screenX=0,screenY=100,resizable=yes,status=no,scrollbars=yes,menubar=yes');
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
		$sqlS = "SELECT * FROM cr_lv0285 WHERE 1=1  " . $this->RptCondition . " $strSort LIMIT $curRow, $maxRows";
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
		$sqlS = "SELECT * FROM cr_lv0285 WHERE 1=1  " . $this->GetCondition() . " $strSort LIMIT $curRow, $maxRows";
		$vorder = $curRow;
		$bResult = db_query($sqlS);
		$this->Count = db_num_rows($bResult);
		$strTrH = "";
		$strH = "";
		$strSubTotal = 0;
		$strSubTax = 0;
		$strTotalAmount = 0;
		$vUnitPrice = "VNÐ";
		for ($i = 0; $i < count($lstArr); $i++) {
			$vTemp = str_replace("@01", "", $lvTdH);
			$vTemp = str_replace("@02", $this->ArrPush[(int)$this->ArrGet[$lstArr[$i]]], $vTemp);
			$strH = $strH . $vTemp;
		}

		while ($vrow = db_fetch_array($bResult)) {
			$strL = "";
			$vorder++;
			for ($i = 0; $i < count($lstArr); $i++) {
				if ($lstArr[$i] == "lv013") {
					if ($vTax > 0 || $vTax == -1) {
						$vTemp = str_replace("@02", $this->getvaluelink($lstArr[$i], $this->FormatView($vrow['lv004'] * $vrow['lv006'], (int)$this->ArrView[$lstArr[$i]])), $this->Align($lvTd, (int)$this->ArrView[$lstArr[$i]]));
					} else {
						$vTemp = str_replace("@02", $this->getvaluelink($lstArr[$i], $this->FormatView($vrow['lv004'] * $vrow['lv006'] + $vrow['lv004'] * $vrow['lv006'] * $vrow['lv008'] / 100, (int)$this->ArrView[$lstArr[$i]])), $this->Align($lvTd, (int)$this->ArrView[$lstArr[$i]]));
					}
				} else
					$vTemp = str_replace("@02", $this->getvaluelink($lstArr[$i], $this->FormatView($vrow[$lstArr[$i]], (int)$this->ArrView[$lstArr[$i]])) . (($lstArr[$i] == 'lv008') ? '%' : ''), $lvTd);
				$strL = $strL . $vTemp;
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
		$strTrH = str_replace("@#01", str_replace("@01", "(" . $vUnitPrice . ")", $strH), $lvTrH);
		return str_replace("@#01", $strTrH . ($strTr ?? ""), $lvTable);
	}
	function LV_GetDepOne()
	{
		$lvsql = "select lv029 from  hr_lv0020 Where lv001='$this->LV_UserID'";
		$vresult = db_query($lvsql);
		$vrow = db_fetch_array($vresult);
		if ($vrow) {
			return $vrow['lv029'];
		}
		return '';
	}
	function LV_ShowPermission()
	{
		//////
		$vStrReturn = '';
		if ($this->LV_UserID == 'MP001')
			$vDepID = '';
		else
			$vDepID = $this->LV_GetDepOne();
		if ($this->LoaiTamUng == 3)
			$vDieuKien = " and lv006 in ('2','')";
		else if ($this->LoaiTamUng == 2)
			$vDieuKien = " and lv006 in ('1','')";
		else
			$vDieuKien = " and lv006 in ('0','')";
		if ($vDepID != '') {
			$vsql = "select lv018 from  cr_lv0309  where (trim(lv012)='' or (concat(',',lv012,',') like '%,$vDepID,%'))  $vDieuKien";
		} else
			$vsql = "select lv018 from  cr_lv0309  where 1=1 $vDieuKien";
		$vresult = db_query($vsql);
		while ($vrow = db_fetch_array($vresult)) {
			if ($vStrReturn == '')
				$vStrReturn = "'" . $vrow['lv018'] . "'";
			else
				$vStrReturn = $vStrReturn . ",'" . $vrow['lv018'] . "'";
		}
		return $vStrReturn;
	}
	public function LV_LinkField($vFile, $vSelectID)
	{
		return ($this->CreateSelect($this->sqlcondition($vFile, $vSelectID), 0));
	}
	private function sqlcondition($vFile, $vSelectID)
	{
		$vsql = "";
		switch ($vFile) {

			case 'lv005':
				/*if($this->LoaiTamUng==1)
					$vDieuKienNguoc=" and lv001 not like '641%'";
				else
					$vDieuKienNguoc=" and lv001 not like '141%'";
				$vsql="select lv001,concat(lv001,' ',lv002) lv002,IF(lv001='$vSelectID',1,0) lv003 from  ac_lv0002  where (lv001 like '141%' or lv001='641' or lv001='1331') $vDieuKienNguoc" ;
				*/
				$vListTK = $this->LV_ShowPermission();
				$vsql = "select lv001,concat(lv001,' ',lv002) lv002,IF(lv001='$vSelectID',1,0) lv003 from  ac_lv0002  where (lv001 in ($vListTK))";
				break;
			case 'lv006':
				$vsql = "select lv001,concat(lv001,' ',lv002) lv002,IF(lv001='$vSelectID',1,0) lv003 from  ac_lv0002";
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
				$vsql = "select lv001,concat(lv001,' ',lv002) lv002,IF(lv001='$vSelectID',1,0) lv003 from  ac_lv0002 where lv001='$vSelectID' ";
				break;
			case 'lv006':
				$vsql = "select lv001,concat(lv001,' ',lv002) lv002,IF(lv001='$vSelectID',1,0) lv003 from  ac_lv0002 where lv001='$vSelectID'";
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
	function Mb_LoadId($maNhanVien)
	{
		$sql = "select * from cr_lv0285 where lv002 = '" . $maNhanVien . "'";
		return db_query($sql);
	}
}
