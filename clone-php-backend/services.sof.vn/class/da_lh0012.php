<?php
/////////////coding da_lh0012///////////////
class da_lh0012 extends lv_controler
{
	public $lv001 = null;
	public $lv002 = null;
	public $lv003 = null;
	public $lv004 = null;
	public $lv005 = null;
	///////////
	public $DefaultFieldList = "lv199,lv001,lv002,lv003,lv004";
	////////////////////GetDate
	public $DateCurrent = "1900-01-01";
	public $Count = null;
	public $paging = null;
	public $lang = null;
	protected $objhelp = 'da_lh0012';
	public $isChinhNhanh = false;

	////////////
	var $ArrOther = array();
	var $ArrPush = array();
	var $ArrFunc = array();
	var $ArrGet = array("lv001" => "2", "lv002" => "3", "lv003" => "4", "lv004" => "5", "lv005" => "6", "lv199" => "200");
	var $ArrView = array("lv001" => "0", "lv002" => "0", "lv003" => "0", "lv004" => "2", "lv005" => "0", "lv199" => "0");
	var $ArrViewEnter = array("lv003" => "89", "lv199" => "-1", "lv001" => "-1", "lv004" => "2");
	var $Tables = array('lv0031' => 'ki_lv0002');
	var $TableLink = array("lv0031" => "concat(lv001,@! @!,lv002)");
	var $TableLinkReturn = array("lv0031" => "lv001");
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

	function LV_Load()
	{
		$vsql = "select * from  da_lh0012";
		$vresult = db_query($vsql);
		$vrow = db_fetch_array($vresult);
		if ($vrow) {
			$this->lv001 = $vrow['lv001'];
			$this->lv002 = $vrow['lv002'];
			$this->lv003 = $vrow['lv003'];
			$this->lv004 = $vrow['lv004'];
			$this->lv005 = $vrow['lv005'];
		}
	}
	function LV_LoadID($vlv001)
	{
		$lvsql = "select * from  da_lh0012 Where lv001='$vlv001'";
		$vresult = db_query($lvsql);
		$vrow = db_fetch_array($vresult);
		if ($vrow) {
			$this->lv001 = $vrow['lv001'];
			$this->lv002 = $vrow['lv002'];
			$this->lv003 = $vrow['lv003'];
			$this->lv004 = $vrow['lv004'];
			$this->lv005 = $vrow['lv005'];
		} else {
			$this->lv001 = null;
		}
	}
	function LV_Insert()
	{

		if ($this->isAdd == 0) return false;
		$this->lv004 = ($this->lv004 != "")
			? recoverdate($this->lv004, $this->lang) . " " . gettime($this->lv004)
			: $this->DateDefault;
		$lvsql = "insert into da_lh0012 (lv001,lv002,lv003,lv004) values('$this->lv001','$this->lv002','$this->lv003','" . sof_escape_string($this->lv004) . "')";
		$vReturn = db_query($lvsql);
		if ($vReturn) $this->InsertLogOperation($this->DateCurrent, 'da_lh0012.insert', sof_escape_string($lvsql));
		return $vReturn;
	}
	function LV_Update()
	{
		if ($this->isEdit == 0) return false;
		$lvsql = "Update da_lh0012 set lv002='$this->lv002',lv003='$this->lv003',lv004='$this->lv004',lv005='$this->lv005' where  lv001='$this->lv001';";
		$vReturn = db_query($lvsql);
		if ($vReturn) $this->InsertLogOperation($this->DateCurrent, 'da_lh0012.update', sof_escape_string($lvsql));
		return $vReturn;
	}
	function LV_Delete($lvarr)
	{
		if ($this->isDel == 0) return false;
		$lvsql = "DELETE FROM da_lh0012  WHERE da_lh0012.lv001 IN ($lvarr) "; //and (select count(*) from da_lh0012 B where  B.lv002= da_lh0012.lv001)<=0  ";
		$vReturn = db_query($lvsql);
		if ($vReturn) $this->InsertLogOperation($this->DateCurrent, 'da_lh0012.delete', sof_escape_string($lvsql));
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
		if ($this->lv001 != "") {
			if (!strpos($this->lv001, ',') === false) {
				$strCondi1 = '';
				$vArrNameCus = explode(",", $this->lv001);
				foreach ($vArrNameCus as $vNameCus) {
					if ($vNameCus != "") {
						if ($strCondi1 == "")
							$strCondi1 = " AND ( lv001 = '$vNameCus'";
						else
							$strCondi1 = $strCondi1 . " OR lv001 = '$vNameCus'";
					}
				}
				if ($strCondi1 != '') $strCondi = $strCondi1 . ")";
			} else {
				$strCondi = $strCondi . " and lv001  like '%$this->lv001%'";
			}
		}
		if ($this->lv002 != "") {
			$strCondi = $strCondi . " and lv002  like '%$this->lv002%'";
			/*
			if(!strpos($this->lv002,',')===false)
			{	
				$strCondi1='';
				$vArrNameCus=explode(",",$this->lv002);
				foreach($vArrNameCus as $vNameCus)
				{
					if($vNameCus!="")
					{
					if($strCondi1=="")	
						$strCondi1= " AND ( lv002 = '$vNameCus'";
					else
						$strCondi1=$strCondi1." OR lv002 = '$vNameCus'";		
					}
				}
				if($strCondi1!='') $strCondi=$strCondi1.")";
				
			}
			else
			{
				$strCondi=$strCondi." and lv002  like '%$this->lv002%'";
			}
			*/
		}
		if ($this->lv003 != "") $strCondi = $strCondi . " and lv003 like '%$this->lv003%'";
		if ($this->lv004 != "") $strCondi = $strCondi . " and lv004 like '%$this->lv004%'";
		if ($this->lv005 != "") $strCondi = $strCondi . " and lv005 like '%$this->lv005%'";
		return $strCondi;
	}
	////////////////Count///////////////////////////
	function GetCount()
	{
		$sqlC = "SELECT COUNT(*) AS nums FROM da_lh0012 WHERE 1=1 " . $this->GetCondition();
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
		<div id=\"func_id\" style='position:relative;background:#f2f2f2'><div style=\"float:left\">" . $this->TabFunction($lvFrom, $lvList, $maxRows) . "</div><div style=\"float:right\">" . $this->ListFieldSave($lvFrom, $lvList, $maxRows, $lvOrderList, $lvSortNum) . "</div><div style='float:right'>&nbsp;&nbsp;&nbsp;</div><div style='float:right'>" . $this->ListFieldExport($lvFrom, $lvList, $maxRows) . "</div></div><div style='height:35px'></div><table  align=\"center\" class=\"lvtable\"><!--<tr ><td colspan=\"" . (2 + count($lstArr)) . "\" class=\"lvTTable\">" . $this->ArrPush[0] . "</td></tr>-->
		@#01
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
		$lvTr = "<tr class=\"lvlinehtable@01\"><td width=1% onclick=\"Select_Check('$lvChk@03',$lvFrom, '$lvChk', '$lvChkAll')\">@03</td>	<td width=1%><input name=\"$lvChk\" type=\"checkbox\" id=\"$lvChk@03\" onclick=\"CheckOne($lvFrom, '$lvChk', '$lvChkAll', this)\" value=\"@02\" tabindex=\"2\"  onKeyUp=\"return CheckKeyCheck(event,2,'$lvChk',$lvFrom, '$lvChk', '$lvChkAll',@03)\"/></td>@#01</tr>";
		$lvTdH = "<td width=\"@01\" class=\"lvhtable\">@02</td>";
		$lvTd = "<td align=@#05>@02</td>";
		$sqlS = "SELECT * FROM da_lh0012 WHERE 1=1  " . $this->GetCondition() . " $strSort LIMIT $curRow, $maxRows";
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
			if ($this->ArrViewEnter[$vField] == null) $this->ArrViewEnter[$vField] = 0;
			$vStringNumber = "";
			switch ($this->ArrView[$vField] ?? 0) {
				case '10':
				case '20':
				case '1':
					$vStringNumber = ' onfocus="LayLaiGiaTri(this)" onblur="SetGiaTri(this);" ';
					break;
			}
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
					$vstr = '<select class="selenterquick" name="qxt' . $vField . '" id="qxt' . $vField . '" tabindex="2" style="width:100%;min-width:30px" onKeyPress="return CheckKey(event,7)">' . $this->LV_LinkField($vField, $this->Values[$vField]) . '</select>';
					$vTempEnter = str_replace("@02", $vstr, $this->Align($lvTd, (int)($this->ArrView[$lstArr[$i]] ?? 0)));
					break;
				case 89:
					$vstr = '<select class="selenterquick" name="qxt' . $vField . '" id="qxt' . $vField . '" tabindex="2" style="width:100%;min-width:30px" onKeyPress="return CheckKey(event,7)">
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
					$vstr = '<input autocomplete="off" class="txtenterquick" name="qxt' . $vField . '" type="text" id="qxt' . $vField . '" value="' . $this->Values[$vField] . '" tabindex="2" style="width:100%;min-width:30px;text-align:center;" onKeyPress="return CheckKey(event,7)">';
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
			$strTrEnter = "<tr class='entermobil'><td colspan='2'>" . '<img tabindex="2" border="0" title="Add" class="imgButton" onclick="Save()" onmouseout="this.src=\'../images/iconcontrol/btn_add.jpg\';" onmouseover="this.src=\'../images/iconcontrol/btn_add_02.jpg\';" src="../images/iconcontrol/btn_add.jpg" onkeypress="return CheckKey(event,11)">' . "</td>" . $strTrEnter . "</tr>";
		else
			$strTrEnter = ""; //"<tr class='entermobil'><td colspan='2'>".'&nbsp;'."</td>".$strTrEnterEmpty."</tr>";

		$strTr = "";
		while ($vrow = db_fetch_array($bResult)) {
			$strL = "";
			$vorder++;
			for ($i = 0; $i < count($lstArr); $i++) {
				switch ($lstArr[$i]) {
					case 'lv199':
						$vStr1 = '';
						$vChucNang = "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\">
						<tr>
						";
						//$vChucNang=$vChucNang.'<td><a href="'.$this->LinkQR.'" target="_blank">QR</a><td>';
						$vChucNang = $vChucNang . '<td><span onclick="ProcessTextHidenMore(this)"><a href="javascript:FunctRunning1(\'' . $vrow['lv001'] . '\')"><img style="cursor:pointer;width:25px;;padding:5px;"  alt="NoImg" src="../images/icon/work_experience.png" align="middle" border="0" name="new" class="lviconimg"></a></span></td>';
						if ($this->GetEdit() == 1) {
							$vChucNang = $vChucNang . '
							<td><img Title="' . (($vrow['lv016'] <= 0) ? 'Edit' : 'View') . '" style="cursor:pointer;width:25px;padding:5px;" onclick="Edit(\'' . ($vrow['lv001']) . '\')" alt="NoImg" src="../images/icon/' . (($vrow['lv016'] <= 0) ? 'Edt.png' : 'detail.png') . '" align="middle" border="0" name="new" class="lviconimg"></td>
							';
						}
						$vChucNang = $vChucNang . "</tr></table>";
						$vTemp = str_replace("@02", $vChucNang, $this->Align($lvTd, (int)$this->ArrView[$lstArr[$i]]));
						break;
					case 'lv004':
						if ($this->isChinhNhanh) {
							$vField = $lstArr[$i];
							$vSTTCot = (int)substr($vField, 2, 3);
							$vID = $vrow['lv001'];
							$vStringNumber = ' onblur="UpdateText(this,\'' . $vID . '\',' . $vSTTCot . ')" ';
							$vstr = '<input ' . $vStringNumber . ' autocomplete="off" class="txtenterquick" name="qxt' . $vField . '_' . $vID . '" type="text" id="qxt' . $vField . '_' . $vID . '"  title="' . $vrow[$vField] . '" value="' . $vrow[$vField] . '" tabindex="2" style="width:100%;min-width:80px;text-align:center;" >';
							$vTemp = str_replace("@02", $vstr, $this->Align($lvTd, (int)$this->ArrView[$lstArr[$i]]));
						} else {
							// Nếu không, chỉ hiển thị giá trị (không cho sửa)
							$displayValue = $vrow[$lstArr[$i]];
							$vTemp = str_replace("@02", $displayValue, $this->Align($lvTd, (int)$this->ArrView[$lstArr[$i]]));
						}
						break;
					case 'lv004':
						$vField = $lstArr[$i];
						$vSTTCot = (int)substr($lstArr[$i], 2, 3);
						$vID = $vrow['lv001'];
						$vStringNumber = ' onblur="UpdateText(this,\'' . $vID . '\',' . $vSTTCot . ')" ';
						$vstr = '<input ' . $vStringNumber . ' readonly="readonly" autocomplete="off" class="txtenterquick" name="qxt' . $vField . '_' . $vID . '" type="text" id="qxt' . $vField . '_' . $vID . '"  title="' . $vrow[$vField] . '" value="' . $this->FormatView($vrow[$lstArr[$i]], (int)$this->ArrView[$lstArr[$i]]) . '" tabindex="2" style="width:100%;min-width:80px;text-align:center;"  ondblclick="if(self.gfPop)gfPop.fPopCalendar(this);return false;">';
						$vTemp = str_replace("@02", $vstr, $this->Align($lvTd, (int)$this->ArrView[$lstArr[$i]]));
						break;
					default:
						$vTemp = str_replace("@02", $this->getvaluelink($lstArr[$i], $this->FormatView($vrow[$lstArr[$i]], (int)$this->ArrView[$lstArr[$i]])), $this->Align($lvTd, (int)$this->ArrView[$lstArr[$i]]));
						break;
				}
				$strL = $strL . $vTemp;
			}


			$currentRow = str_replace(
				"@#01",
				$strL,
				str_replace(
					"@02",
					$vrow['lv001'],
					str_replace(
						"@03",
						$vorder,
						str_replace("@01", $vorder % 2, $lvTr)
					)
				)
			);

			$strTr .= $currentRow;
		}
		$strTrH = str_replace("@#01", $strH, $lvTrH);
		return str_replace("@#01", $strTrH . $strTrEnter . $strTr, $lvTable);
	}
	function LV_BuilList1($lvList, $lvFrom, $lvChkAll, $lvChk, $curRow, $maxRows, $paging, $lvOrderList, $lvSortNum)
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
		$lvTdH = "<td width=\"@01\" class=\"lvhtable\">@02</td>";
		$lvTd = "<td align=@#05>@02</td>";
		$sqlS = "SELECT * FROM da_lh0012 WHERE 1=1  " . $this->GetCondition() . " $strSort LIMIT $curRow, $maxRows";
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
			window.open('" . $this->Dir . "da_lh0012/?lang=" . $this->lang . "&func='+value+'&ID=" . base64_encode($this->lv002) . "','','width=800,height=600,left=200,top=100,screenX=0,screenY=100,resizable=yes,status=no,scrollbars=yes,menubar=yes');
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
		$lvChk = "<input type=\"checkbox\" id=\"$vID@01\" value=\"@02\" @03 title=\"@04\" tabindex=\"$vTabIndex\">";
		$lvTrH = "<tr class=\"lvlinehtable1\">
			<td width=1%>@#01</td><td>@#02</td>
			
		</tr>
		";
		$vsql = "select * from  da_lh0012";
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
		$sqlS = "SELECT * FROM da_lh0012 WHERE 1=1  " . $this->RptCondition . " $strSort LIMIT $curRow, $maxRows";
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
				$vsql = "select lv001, lv002,IF(lv001='$vSelectID',1,0) lv003 from  ac_lv0001";
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
			case 'lv003':
				$vsql = "select lv001,lv002,IF(lv001='$vSelectID',1,0) lv003 from  ac_lv0001 where lv001='$vSelectID'";
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
	//KEBAO
	function Mb_LoadAll()
	{
		$vsql = "select * from  da_lh0012";
		$vresult = db_query($vsql);
		return $vresult;
	}
	function loadcauhoi_traloi()
	{
		$vsql = "select ch.*,ctl.lv003 as cautraloi
					from da_lh0012 as ch
					inner join da_lh0013 as ctl on ch.lv001=ctl.lv002;";
		$vresult = db_query($vsql);
		return $vresult;
	}
}
