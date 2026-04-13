<?php
/////////////coding cr_lv0313///////////////
class   cr_lv0313 extends lv_controler
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
	public $trangthai = null;
	///////////
	public $DefaultFieldList = "lv002,lv808,lv807,lv817,lv003,lv006,lv004,lv005,lv009";
	////////////////////GetDate
	public $DateCurrent = "1900-01-01";
	public $Count = null;
	public $paging = null;
	public $lang = null;
	protected $objhelp = 'cr_lv0313';
	public $Dir = "";
	////////////
	var $ArrOther = array();
	var $ArrPush = array();
	var $ArrFunc = array();
	var $ArrGet = array("lv001" => "2", "lv002" => "3", "lv003" => "4", "lv004" => "5", "lv005" => "6", "lv006" => "7", "lv009" => "10", "lv808" => "809", "lv807" => "808", "lv907" => "908", "lv817" => "818", "lv898" => "899", "lv905" => '906');
	var $ArrView = array("lv001" => "0", "lv002" => "0", "lv003" => "0", "lv004" => "0", "lv005" => "22", "lv006" => "0", "lv009" => "0", "lv808" => "0", "lv807" => "0", "lv907" => "0", "lv817" => "10");
	var $ArrViewEnter = array("lv003" => 99, "lv004" => "-1", "lv005" => "-1", "lv006" => "-1");
	var $Tables = array("lv003" => "hr_lv0002");
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
		$this->isAdd = 0;
		$this->isEdit = 0;
		$this->isDel = 0;
		$this->lang = $_GET['lang'];
	}
	protected function LV_CheckLock()
	{
		$lvsql = "select lv027 from sl_lv0010  where  lv001='$this->lv002'";
		$vReturn = db_query($lvsql);
		if ($vReturn) {
			$vrow = db_fetch_array($vReturn);
			if ($vrow['lv027'] >= 1) {
				$this->isAdd = 0;
				$this->isEdit = 0;
				$this->isDel = 0;
			}
		}
	}
	function LV_Load()
	{
		$vsql = "select * from  cr_lv0313";
		$vresult = db_query($vsql);
		$vrow = db_fetch_array($vresult);
		if ($vrow) {
			$this->lv001 = $vrow['lv001'];
			$this->lv002 = $vrow['lv002'];
			$this->lv003 = $vrow['lv003'];
			$this->lv004 = $vrow['lv004'];
			$this->lv005 = $vrow['lv005'];
			$this->lv009 = $vrow['lv009'];
		}
	}
	function LV_LoadID($vlv001)
	{
		$lvsql = "select * from  cr_lv0313 Where lv001='$vlv001'";
		$vresult = db_query($lvsql);
		$vrow = db_fetch_array($vresult);
		if ($vrow) {
			$this->lv001 = $vrow['lv001'];
			$this->lv002 = $vrow['lv002'];
			$this->lv003 = $vrow['lv003'];
			$this->lv004 = $vrow['lv004'];
			$this->lv005 = $vrow['lv005'];
			$this->lv009 = $vrow['lv009'];
		}
	}
	function LV_Insert()
	{
		if ($this->isAdd == 0) return false;
		$lvsql = "insert into cr_lv0313 (lv002,lv003,lv004,lv005) values('" . sof_escape_string($this->lv002) . "','" . sof_escape_string($this->lv003) . "','$this->LV_UserID',now())";
		$vReturn = db_query($lvsql);
		if ($vReturn) $this->InsertLogOperation($this->DateCurrent, 'cr_lv0313.insert', sof_escape_string($lvsql));
		return $vReturn;
	}
	function LV_InsertAuto()
	{
		if ($this->isAdd == 0) return false;
		$lvsql = "insert into cr_lv0313 (lv002,lv003,lv004,lv005) values('" . sof_escape_string($this->lv002) . "','" . sof_escape_string($this->lv003) . "','$this->LV_UserID',now())";
		$vReturn = db_query($lvsql);
		if ($vReturn) $this->InsertLogOperation($this->DateCurrent, 'cr_lv0313.insert', sof_escape_string($lvsql));
		return $vReturn;
	}

	function LV_LoadGroup($lv099)
	{
		$lvsql = "select lv001 from  hr_lv0002 Where lv099='$lv099'";
		$vresult = db_query($lvsql);
		$vrow = db_fetch_array($vresult);
		if ($vrow) {
			$this->lv001 = $vrow['lv001'];
		} else {
			$this->lv001 = null;
		}
		return $this->lv001;
	}
	function LV_InsertAutoImp()
	{
		if ($this->isAdd == 0) return false;
		$lvsql = "insert into cr_lv0313 (lv002,lv003,lv004,lv005) values('" . sof_escape_string($this->lv002) . "','" . sof_escape_string($this->lv003) . "','" . sof_escape_string($this->lv004) . "','" . sof_escape_string($this->lv005) . "')";
		$vReturn = db_query($lvsql);
		if ($vReturn) $this->InsertLogOperation($this->DateCurrent, 'cr_lv0313.insert', sof_escape_string($lvsql));
		return $vReturn;
	}
	function LV_GetDefaultList()
	{
		if ($this->isLoadOK) return;
		$link = sqlsrv_connect($this->Server, $this->connectionOptions);
		if (!$link) {
			print_r(sqlsrv_errors());
			return;
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
		$this->isLoadOK = true;
	}
	//Lấy data
	function LV_GetDataAuto($vContractID, $vNewID)
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
		//SELECT [id],[order_id],[reservation],[material],[plant],[sloc],[redate],[quant],[unit],[wqty],[wvalue],[cur],[une],[uentry],[pegre] FROM [erp_stk].[dbo].[sap_bom]	
		//$bResult = db_query($sqlS);
		$vCondition = "";
		$lvsql = " SELECT  
			[PR_ID] lv002
		,[USERS_ID] lv004
		,CONVERT(VARCHAR(20),[LOCK_DATE],120) lv005
		,[DESCRIPTION] lv003
	FROM [ContractManagement].[dbo].[PR_History]

		where  [PR_ID]='$vContractID'";
		$vresult = sqlsrv_query($link, $lvsql);
		$i = 0;
		while ($vrow = sqlsrv_fetch_array($vresult)) {
			//$vOldID=$this->LV_LoadMaOld($vrow['lv099']);
			//if($vOldID==null)
			{
				$this->lv002 = $vNewID;
				$this->lv003 = $vrow['lv003'];
				$this->lv004 = $this->LV_UserList[$vrow['lv004']];
				$this->lv005 = $vrow['lv005'];
				$this->LV_InsertAutoImp();
			}
		}
		//Biễu diễn chi tiết sản xuất. 
	}
	function LV_Update()
	{
		if ($this->isEdit == 0) return false;
		$lvsql = "Update cr_lv0313 set lv002='$this->lv002',lv003='$this->lv003',lv004='$this->LV_UserID',lv005=now() where  lv001='$this->lv001'";
		$vReturn = db_query($lvsql);
		if ($vReturn) $this->InsertLogOperation($this->DateCurrent, 'cr_lv0313.update', sof_escape_string($lvsql));
		return $vReturn;
	}
	function LV_Delete($lvarr)
	{
		if ($this->isDel == 0) return false;
		$lvsql = "DELETE FROM cr_lv0313  WHERE cr_lv0313.lv001 IN ($lvarr) and (select B.lv027 from sl_lv0010 B where  cr_lv0313.lv002= B.lv001)<=0 ";
		$vReturn = db_query($lvsql);
		if ($vReturn) $this->InsertLogOperation($this->DateCurrent, 'cr_lv0313.delete', sof_escape_string($lvsql));
		return $vReturn;
	}
	/////lv admin deletet
	function LV_Aproval($lvarr)
	{
		if ($this->isApr == 0) return false;
		$vUserID = getInfor($_SESSION['ERPSOFV2RUserID'], 2);
		$lvsql = "Update cr_lv0313 set lv015=1  WHERE cr_lv0313.lv001 IN ($lvarr)  ";
		$vReturn = db_query($lvsql);
		if ($vReturn) $this->InsertLogOperation($this->DateCurrent, 'cr_lv0313.approval', sof_escape_string($lvsql));
		return $vReturn;
	}
	function LV_UnAproval($lvarr)
	{
		if ($this->isUnApr == 0) return false;
		$vUserID = getInfor($_SESSION['ERPSOFV2RUserID'], 2);
		$lvsql = "Update cr_lv0313 set lv015=0 WHERE cr_lv0313.lv001 IN ($lvarr)  ";
		$vReturn = db_query($lvsql);
		if ($vReturn) $this->InsertLogOperation($this->DateCurrent, 'cr_lv0313.unapproval', sof_escape_string($lvsql));
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
		if ($this->lv001 != "") $strCondi = $strCondi . " and A.lv001 like '%$this->lv001%'";
		if ($this->lv002 != "") $strCondi = $strCondi . " and A.lv002 = '$this->lv002'";
		if ($this->lv003 != "") $strCondi = $strCondi . " and A.lv003 like '%$this->lv003%'";
		if ($this->lv004 != "") $strCondi = $strCondi . " and A.lv004 like '%$this->lv004%'";
		if ($this->lv005 != "") $strCondi = $strCondi . " and A.lv005 like '%$this->lv005%'";
		if ($this->lv006 != "") $strCondi = $strCondi . " and A.lv006 like '%$this->lv006%'";
		if ($this->lv007 != "") $strCondi = $strCondi . " and A.lv007 like '%$this->lv007%'";
		if ($this->lv008 != "") $strCondi = $strCondi . " and A.lv008 like '%$this->lv008%'";
		if ($this->lv009 != "") $strCondi = $strCondi . " and A.lv009 like '%$this->lv009%'";
		if ($this->lv010 != "") $strCondi = $strCondi . " and A.lv010 like '%$this->lv010%'";
		if ($this->lv011 != "") $strCondi = $strCondi . " and A.lv011 like '%$this->lv011%'";
		if ($this->lv012 != "") $strCondi = $strCondi . " and A.lv012 like '%$this->lv012%'";
		if ($this->lv807 != "") $strCondi = $strCondi . " and B.lv007 like '%$this->lv807%'";
		$strCondi1 = '';
		if ($this->lv105 != "") {
			if (!strpos($this->lv105, ',') === false) {
				$vArrName = explode(",", $this->lv105);
				foreach ($vArrName as $vName) {
					if ($vName != "") {
						if ($strCondi1 == "")
							$strCondi1 = " AND (  B.lv105 = '$vName'";
						else
							$strCondi1 = $strCondi1 . " OR  B.lv105 = '$vName'";
					}
				}
				$strCondi1 = $strCondi1 . ")";
			} else {
				$strCondi1 = $strCondi1 . " and  B.lv105  like '%$this->lv105%'";
			}
			$strCondi = $strCondi . $strCondi1;
		}
		$strCondi1 = '';
		if ($this->lv098 != "") {
			if (!strpos($this->lv098, ',') === false) {
				$vArrName = explode(",", $this->lv098);
				foreach ($vArrName as $vName) {
					if ($vName != "") {
						if ($strCondi1 == "")
							$strCondi1 = " AND (  B.lv098 = '$vName'";
						else
							$strCondi1 = $strCondi1 . " OR  B.lv098 = '$vName'";
					}
				}
				$strCondi1 = $strCondi1 . ")";
			} else {
				$strCondi1 = $strCondi1 . " and  B.lv098  like '%$this->lv098%'";
			}
			$strCondi = $strCondi . $strCondi1;
		}
		if ($this->lv808 != "") {
			if (!strpos($this->lv808, ',') === false) {
				$vArrNameCus = explode(",", $this->lv808);
				foreach ($vArrNameCus as $vNameCus) {
					if ($vNameCus != "") {
						if ($strCondi == "")
							$strCondi = " AND ( B.lv117 = '$vNameCus'";
						else
							$strCondi = $strCondi . " OR B.lv117 = '$vNameCus'";
					}
				}
				$strCondi = $strCondi . ")";
			} else {
				$strCondi = $strCondi . " and B.lv117  = '$this->lv808'";
			}
		}
		switch ($this->trangthai) {
			case '1':
				if ($this->LV_UserID != 'MP001')
					$strCondi = $strCondi . " and A.lv004='$this->LV_UserID' and ((A.lv007=1 and A.lv006 in('Apr','UnApr')))";
				else
					$strCondi = $strCondi . " and ((A.lv007=1 and A.lv006 in('Apr','UnApr')))";
				break;
			case '2':
				$strCondi = $strCondi . " and ((A.lv007=2 and  A.lv006 in('Apr','UnApr','AprHU','UnAprHU')) )";
				break;
			case '3':
				$strCondi = $strCondi . " and ((A.lv007=3 and A.lv006 in('Apr','UnApr')))";
				break;
			case '4':
				$strCondi = $strCondi . " and ((A.lv007=4 and and A.lv006 in('Apr','UnApr')) )";
				break;
			case '5':
				$strCondi = $strCondi . " and ((A.lv007=5 and A.lv006 in('Apr','UnApr')) )";
				break;
		}
		//if($this->trangthai.''!="") $strCondi=$strCondi." and A.lv007 = '$this->trangthai'";
		return $strCondi;
	}
	////////////////Count///////////////////////////
	function GetCount()
	{
		$sqlC = "SELECT COUNT(*) AS nums FROM cr_lv0313 A inner join cr_lv0150 B on A.lv002=B.lv001 WHERE 1=1 " . $this->GetCondition();
		$bResultC = db_query($sqlC);
		$arrRowC = db_fetch_array($bResultC);
		return $arrRowC['nums'];
	}
	function LV_GetAmountFullTK($vVoiceID)
	{
		$lvsql = "select lv003,lv004,lv005,lv006 from cr_lv0203 A  WHERE A.lv002 ='$vVoiceID'";
		$vReturnArr = array();
		$lvResult = db_query($lvsql);
		$vDauMaPre = '';
		$vArr[0] = true;
		while ($row = db_fetch_array($lvResult)) {
			$vDauMa = substr($row['lv005'], 0, 3);
			$vArr[3] = $vArr[3] + $row['lv003'];
			$vArr[4] = $vArr[4] + $row['lv004'];
			if ($vDauMaPre != '' && $vDauMa != '' && $vDauMaPre != $vDauMa) {
				if (substr($vDauMa, 0, 2) != '33' && $vDauMa != '133') {
					$vArr[0] = false;
				}
			}
			if (strpos($vArr[1], $vDauMa) === false) {
				if ($vArr[1] == '') {
					$vArr[1] = $vDauMa . '***';
				} else {
					$vArr[1] = $vArr[1] . ',' . $vDauMa . '***';
				}
			}
			$vDauMaPre = $vDauMa;
		}
		return $vArr;
	}
	function LV_GetNoteHU($vPNDID)
	{
		$vStrReturn = '';
		$lvsql = "select lv107  from cr_lv0203 where lv002='$vPNDID'";
		$vresult = db_query($lvsql);
		while ($vrow = db_fetch_array($vresult)) {
			if (trim($vrow['lv107']) != '') {
				if ($vStrReturn == '')
					$vStrReturn = $vrow['lv107'];
				else
					$vStrReturn = $vStrReturn . ',' . $vrow['lv107'];
			}
		}
		return $vStrReturn;
	}
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
			<div id=\"func_id\" style='position:relative;background:#f2f2f2'>
			<div style=\"float:left\">" . $this->TabFunction($lvFrom, $lvList, $maxRows) . "</div>
			<div style=\"float:right\">" . $this->ListFieldSave($lvFrom, $lvList, $maxRows, $lvOrderList, $lvSortNum) . "</div>
			<div style='float:right'>&nbsp;&nbsp;&nbsp;</div><div style='float:right'>" . $this->ListFieldExport($lvFrom, $lvList, $maxRows) . "</div></div><div style='height:35px'></div>
			<table  align=\"center\" class=\"lvtable\">
			@#01
			@#02
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
		$lvTdF = "<td align=\"right\"><strong>@01</strong></td>";
		$strF = "<tr><td colspan=\"2\">&nbsp;</td>";
		$sqlS = "SELECT A.*,B.lv117 lv808,B.lv008 lv807 FROM cr_lv0313 A inner join cr_lv0150 B on A.lv002=B.lv001 WHERE 1=1  " . $this->GetCondition() . " $strSort LIMIT $curRow, $maxRows";
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
					$vstr = '<ul style="width:100%" id="pop-nav" lang="pop-nav1" onMouseOver="ChangeName(this,1)" onKeyUp="ChangeName(this,1)"> <li class="menupopT">
										<input autocomplete="off" class="txtenterquick" type="text" autocomplete="off" style="width:100%;min-width:30px" name="qxt' . $vField . '" id="qxt' . $vField . '" onKeyUp="LoadPopupTabIndex(event,this,\'qxt' . $vField . '\',\'' . $this->Tables[$vField] . '\',\'concat(lv002,@! @!,lv001)\')"  onKeyPress="return CheckKey(event,7)" tabindex="2" onblur="changecustomer_change(this.value)" value="' . $this->Values[$vField] . '">
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
			$vArrTongTien = $this->LV_GetAmountFullTK($vrow['lv002']);
			$vrow['lv907'] = $this->LV_GetNoteHU($vrow['lv002']);
			$vTongTien = (float)$vArrTongTien[3];
			$vSumTongTien = $vSumTongTien + $vTongTien;
			for ($i = 0; $i < count($lstArr); $i++) {
				switch ($lstArr[$i]) {
					case 'lv817':
						//$vTemp=str_replace("@02",str_replace("@02",$this->getvaluelink($lstArr[$i],$this->FormatView($vTongTien,(int)$this->ArrView[$lstArr[$i]])),str_replace("@01",$vrow['lv001'] ,$lvHref)),$this->Align($lvTd,(int)$this->ArrView[$lstArr[$i]]));	
						$vTemp = str_replace("@02", $this->getvaluelink($lstArr[$i], $this->FormatView($vTongTien, (int)$this->ArrView[$lstArr[$i]])), $this->Align($lvTd, (int)$this->ArrView[$lstArr[$i]]));
						break;
					case 'lv002':
						$vLinkWeb = '<a href="javascript:open_phieu_full(\'' . $vrow['lv002'] . '\');">' . $vrow['lv002'] . '</a>';
						$vTemp = str_replace("@02", $vLinkWeb, $this->Align($lvTd, (int)$this->ArrView[$lstArr[$i]]));
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
		$strF = str_replace("<!--lv817-->", $this->FormatView($vSumTongTien, 10), $strF);
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
				window.open('$this->Dir'+'cr_lv0313/?lang=" . $this->lang . "&childdetailfunc='+value+'&ID=" . base64_encode($this->lv002) . "','','width=800,height=600,left=200,top=100,screenX=0,screenY=100,resizable=yes,status=no,scrollbars=yes,menubar=yes');
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
	function LV_BuilListReport($lvList, $lvFrom, $lvChkAll, $lvChk, $curRow, $maxRows, $paging, $lvOrderList, $lvSortNum)
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
				<td width=1% align=\"center\">@03</td>
				@#01
			</tr>
			";
		$lvTdH = "<td width=\"@01\" class=\"lvhtable\">@02</td>";
		$lvTd = "<td align=@#05>@02</td>";
		$sqlS = "SELECT A.*,B.lv117 lv808,B.lv008 lv807 FROM cr_lv0313 A inner join cr_lv0150 B on A.lv002=B.lv001 WHERE A.lv002='$this->lv002' $strSort LIMIT $curRow, $maxRows";
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
	//Gửi kết quả
	function LV_SendResult($vResult)
	{
		$this->NumMax = 0;
		$vListStaff = '';
		$vListCus = '';
		while ($vrow = db_fetch_array($vResult)) {
			$vArrHopDong[$vrow['lv001']]['lv001'] = $vrow['lv001'];
			$vArrHopDong[$vrow['lv001']]['lv002'] = $vrow['lv002'];
			$vArrHopDong[$vrow['lv001']]['lv003'] = $vrow['lv003'];
			$vArrHopDong[$vrow['lv001']]['lv004'] = $vrow['lv004'];
			$vArrHopDong[$vrow['lv001']]['lv005'] = $vrow['lv005'];
			$vArrHopDong[$vrow['lv001']]['lv006'] = $vrow['lv006'];
			$vArrHopDong[$vrow['lv001']]['lv007'] = $vrow['lv007'];
			$vArrHopDong[$vrow['lv001']]['lv008'] = $vrow['lv008'];
			$vArrHopDong[$vrow['lv001']]['lv009'] = $vrow['lv009'];
			if ($this->NumMax < $vrow['lv008']) $this->NumMax = $vrow['lv008'];
		}

		return $vArrHopDong;
	}
	//////////////////////Buil list////////////////////
	function LV_BuilListReport_Short($lvList, $lvFrom, $lvChkAll, $lvChk, $curRow, $maxRows, $paging, $lvOrderList)
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
				<td width=1% align=\"center\">@03</td>
				@#01
			</tr>
			";
		$lvTdH = "<td width=\"@01\" class=\"lvhtable\">@02</td>";
		$lvTd = "<td align=@#05>@02</td>";
		if (trim($this->lv007) != '') $vCondition = " and  A.lv007='$this->lv007' ";
		$sqlS = "SELECT A.lv001,A.lv002,A.lv003,concat(B.lv002,' - ',B.lv005) lv004,A.lv005,A.lv006,A.lv007,A.lv008,A.lv009 FROM cr_lv0313 A inner join hr_lv0020 B on A.lv004=B.lv001  WHERE A.lv002='$this->lv002' $vCondition order by A.lv008 ASC,A.lv005 desc";
		$vorder = $curRow;
		$bResult = db_query($sqlS);
		$ArrResult = $this->LV_SendResult($bResult);
		//$this->Count=db_num_rows($bResult);
		$strTrH = "";
		$strH = "";
		for ($i = 0; $i < count($lstArr); $i++) {
			$vTemp = str_replace("@01", "", $lvTdH);
			$vTemp = str_replace("@02", $this->ArrPush[(int)$this->ArrGet[$lstArr[$i]]], $vTemp);
			$strH = $strH . $vTemp;
		}
		$vGroup = '1111111111';
		foreach ($ArrResult as $vrow) {
			$strL = "";
			$vorder++;
			if ($vrow['lv008'] != $vGroup) {
				$vGroup = $vrow['lv008'];
				$strTr = $strTr . '<tr><td colspan="5"><strong>Duyệt lần ' . ($this->NumMax - $vrow['lv008'] + 1) . '</strong></td></tr>';
			}
			for ($i = 0; $i < count($lstArr); $i++) {
				if ('lv004' == $lstArr[$i])
					$vTemp = str_replace("@02", $this->FormatView($vrow[$lstArr[$i]], (int)$this->ArrView[$lstArr[$i]]), $this->Align($lvTd, (int)$this->ArrView[$lstArr[$i]]));
				else
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
			case 'lv006':
				$vsql = "select lv001,lv002,IF(lv001='$vSelectID',1,0) lv003 from  cr_lv0206";
				break;
			case 'lv003':
				$vsql = "select lv001,lv002,IF(lv001='$vSelectID',1,0) lv003 from hr_lv0002";
				break;
		}
		return $vsql;
	}
	private function getvaluelink($vFile, $vSelectID)
	{
		if (!empty($this->ArrGetValueLink[$vFile][$vSelectID][0] ?? null)) {
			return $this->ArrGetValueLink[$vFile][$vSelectID][1] ?? null;
		}
		switch ($vFile) {
			case 'lv004':
				$vsql = "select lv001,concat(lv002,' - ',lv005) lv002,IF(lv001='$vSelectID',1,0) lv003 from  hr_lv0020 where lv001='$vSelectID'";
				break;
			case 'lv006':
				$vsql = "select lv001,lv002,IF(lv001='$vSelectID',1,0) lv003 from  cr_lv0206 where lv001='$vSelectID'";
				break;
			case 'lv808':
				$vsql = "select lv001,lv002,IF(lv001='$vSelectID',1,0) lv003 from  hr_lv0020 where lv001='$vSelectID'";
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

	function MB_LichSuDuyet()
	{
		$sqlS = "SELECT A.*,B.lv117 lv808,B.lv008 lv807 FROM cr_lv0313 A inner join cr_lv0150 B on A.lv002=B.lv001 WHERE 1=1  " . $this->GetCondition() . "";
		return db_query($sqlS);
	}
}
