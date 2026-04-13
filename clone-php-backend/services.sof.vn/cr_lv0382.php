<?php
/////////////coding cr_lv0382///////////////
class cr_lv0382 extends lv_controler
{
	public $lv001 = null;
	public $lv002 = null;
	public $lv003 = null;
	public $lv004 = null;
	public $lv005 = null;
	public $lv006 = null;
	///////////
	public $DefaultFieldList = "lv199,lv008,lv003,lv005,lv011,lv012,lv013,lv014,lv015,lv016,lv017,lv018,lv009,lv010";
	////////////////////GetDate
	public $DateCurrent = "1900-01-01";
	public $Count = null;
	public $paging = null;
	public $lang = null;
	protected $objhelp = 'cr_lv0382';
	public $Dir = "";
	////////////
	var $ArrOther = array();
	var $ArrPush = array();
	var $ArrFunc = array();
	var $ArrGet = array("lv001" => "2", "lv002" => "3", "lv003" => "4", "lv004" => "5", "lv005" => "6", "lv006" => "7", "lv007" => "8", "lv008" => "9", "lv009" => "10", "lv010" => "11", "lv011" => "12", "lv012" => "13", "lv013" => "14", "lv014" => "15", "lv015" => "16", "lv016" => "17", "lv017" => "18", "lv018" => "19", "lv199" => "200");
	var $ArrView = array("lv001" => "0", "lv002" => "0", "lv003" => "0", "lv004" => "0", "lv005" => "0", "lv006" => "0", "lv007" => "0", "lv008" => "0", "lv009" => "0", "lv010" => "22");
	var $ArrViewEnter = array("lv199" => "-1", "lv009" => "-1", "lv010" => "-1", "lv007" => "-1", "lv004" => "-1", "lv006" => "-1", "lv005" => "-1", "lv011" => "-1", "lv012" => "-1", "lv013" => "-1", "lv014" => "-1", "lv015" => "-1", "lv016" => "-1", "lv017" => "-1", "lv018" => "-1", "lv008" => "99", "lv009" => "-1", "lv010" => "-1");
	var $Tables = array();
	public $LE_CODE = "NjlIUS02VFdULTZIS1QtNlFIQQ==";
	function __construct($vCheckAdmin, $vUserID, $vright)
	{
		$this->DateCurrent = GetServerDate() . " " . GetServerTime();
		$this->Set_User($vCheckAdmin, $vUserID, $vright);
		$this->isRel = 1;
		$this->isHelp = 1;
		$this->isConfig = 0;
		$this->isFil = 1;
		$this->isApr = 0;
		$this->isUnApr = 0;
		$this->lang = $_GET['lang'];
	}
	function LV_Load()
	{
		$vsql = "select * from  cr_lv0382";
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
		}
	}
	function LV_LoadID($vlv001)
	{
		$lvsql = "select * from  cr_lv0382 Where lv001='$vlv001'";
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
		}
	}
	function GetBuilCheckShift($vListID, $vID, $vTabIndex, $vTbl, $vFieldView = 'lv002')
	{
		$vListID = "," . $vListID . ",";
		$strTbl = "<table  align=\"center\" class=\"lvtable\">
		<input type=\"hidden\" id=$vID name=$vID value=\"@#02\">
		@#01
		</table>
		";
		$lvChk = "<input type=\"checkbox\" id=\"$vID@01\" value=\"@02\" @03 title=\"@04\" tabindex=\"$vTabIndex\">";
		$lvTrH = "<tr class=\"lvlinehtable1\">
			<td width=1%>@#01</td><td>@#02</td>
			
		</tr>
		";
		$vsql = "select * from  " . $vTbl . " where lv001!='' ";
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
			$strTemp = str_replace("@#02", $vrow[$vFieldView] . "(" . $vrow['lv001'] . ")", $strTemp);
			$strGetScript = $strGetScript . $strTemp;
			$i++;

		}
		$strReturn = str_replace("@#01", $strGetScript, str_replace("@#02", $numrows, $strTbl));
		return $strReturn;
	}
	function LV_CheckSelft($vUserID, $vFileID)
	{
		$lvsql = "select count(*) num from  sl_lv0013 Where lv001='$vFileID' and lv010='$vUserID'";
		$vresult = db_query($lvsql);
		$vrow = db_fetch_array($vresult);
		if ($vrow) {
			if ($vrow['num'] > 0)
				return true;
		}
		return false;
	}
	function LV_LoadUserID($vlv002)
	{
		$strReturn = "";
		$strTotal = 0;
		$strTable = "<table border=1 width=80% cellpadding=0 cellspacing=0 style=\"border-color:#CCCCCC\">@#01</table>";
		$lvsql = "select lv001,lv005,lv003 from  cr_lv0382 Where lv002='$vlv002'";
		$vresult = db_query($lvsql);
		if ($this->lang == 'VN')
			$btxoa = "Xóa";
		else
			$btxoa = "Delete";
		while ($vrow = db_fetch_array($vresult)) {
			$this->lv001 = $vrow['lv001'];
			$this->lv002 = $vrow['lv002'];
			$this->lv003 = $vrow['lv003'];
			$this->lv004 = $vrow['lv004'];
			$this->lv005 = $vrow['lv005'];
			$this->lv006 = $vrow['lv006'];
			$strTotal = $strTotal + $vrow['lv003'];
			if ($strReturn == "")
				$strReturn = "<tr bordercolor=#999999><td><a href=\"javascript:delattach('" . $this->lv001 . "')\">" . $btxoa . "</a></td><td align=left>" . $this->lv005 . "</td><td style=\"text-align:right!important\">" . $this->LV_GetByte($this->lv003) . "</td></tr>";
			else
				$strReturn = $strReturn . "<tr bordercolor=#999999><td><a href=\"javascript:delattach('" . $this->lv001 . "')\">" . $btxoa . "</a></td><td align=left>" . $this->lv005 . "</td><td style=\"text-align:right!important\">" . $this->LV_GetByte($this->lv003) . "</td></tr>";
		}
		if ($strReturn != "")
			$strReturn = $strReturn . "<tr bordercolor=#999999><td align=left colspan=2>&nbsp;" . "</td><td style=\"text-align:right!important\">" . $this->LV_GetByte($strTotal) . "</td></tr>";
		return str_replace("@#01", $strReturn, $strTable);
	}
	function LV_LoadUserArray($vlv002)
	{
		$strReturn = "";
		$strTotal = 0;
		$lvsql = "select * from  cr_lv0382 Where lv002='$vlv002'";
		$vresult = db_query($lvsql);
		return $vresult;
	}
	function LV_GetByte($vByte)
	{
		if ($vByte >= 1048576)
			return round($vByte / 1048576, 3) . "MB";
		elseif ($vByte >= 1024)
			return round($vByte / 1024, 2) . "KB";
		else
			return ($vByte) . "Byte";
	}
	function LV_LoadStep($vlv007 = 'sovanban', $vlv008 = 'tokhai', $vlv009 = 'bbbg', $vCodeID)
	{
		$strReturn = "";
		$strTotal = 0;
		$strTable = "<table border=1 cellpadding=0 cellspacing=0 style=\"border-color:#CCCCCC\">@#01</table>";
		$lvsql = "select * from  cr_lv0382 Where lv007='$vlv007' and lv008='$vlv008' and lv009='$vlv009'";
		$vresult = db_query($lvsql);
		if ($this->lang == 'VN')
			$btxoa = "Xóa";
		else
			$btxoa = "Delete";
		while ($vrow = db_fetch_array($vresult)) {
			$this->lv001 = $vrow['lv001'];
			$this->lv002 = $vrow['lv002'];
			$this->lv003 = $vrow['lv003'];
			$this->lv004 = $vrow['lv004'];
			$this->lv005 = $vrow['lv005'];
			$this->lv006 = $vrow['lv006'];
			$this->lv007 = $vrow['lv007'];
			$this->lv008 = $vrow['lv008'];
			$this->lv009 = $vrow['lv009'];
			$strTotal = $strTotal + $vrow['lv003'];
			if ($strReturn == "")
				$strReturn = "<tr bordercolor=#999999><td><a href=\"javascript:delattach('" . $this->lv001 . "','" . $vlv008 . "','" . $vCodeID . "','" . $vlv009 . "')\">" . $btxoa . "</a></td><td align=left><a target='_blank' href='../" . ($this->lv004 . '/' . $this->lv005) . "'>" . $this->lv005 . "</a></td></tr>";
			else
				$strReturn = $strReturn . "<tr bordercolor=#999999><td><a href=\"javascript:delattach('" . $this->lv001 . "','" . $vlv008 . "','" . $vCodeID . "','" . $vlv009 . "')\">" . $btxoa . "</a></td><td align=left>" . $this->lv005 . "</td></tr>";
		}
		return str_replace("@#01", $strReturn, $strTable);
	}
	function LV_LoadStepCheckTemp($vlv007)
	{
		$strReturn = "";
		$strTotal = 0;
		$lvsql = "select lv001 from  erp_minhphuong_documents_v3_0.cr_lv0382_temp Where lv002='$vlv007' ";
		$vresult = db_query($lvsql);
		$vrow = db_fetch_array($vresult);
		if ($vrow) {
			return $vrow['lv001'];
		}
		return null;
	}
	function LV_LoadStepCheck($vlv002)
	{
		$strReturn = "";
		$strTotal = 0;
		$lvsql = "select lv001 from  erp_minhphuong_documents_v3_0.cr_lv0382 Where lv002='$vlv002' ";
		$vresult = db_query($lvsql);
		$vrow = db_fetch_array($vresult);
		if ($vrow) {
			return $vrow['lv001'];
		}
		return null;
	}
	function scaleImageFileToBlob($file, $max_width, $max_height)
	{

		$source_pic = $file;
		if ($max_width == 0)
			$max_width = 661;
		if ($max_height == 0)
			$max_height = 935;
		list($width, $height, $image_type) = getimagesize($file);

		switch ($image_type) {
			case 1:
				$src = imagecreatefromgif($file);
				break;
			case 2:
				$src = imagecreatefromjpeg($file);
				break;
			case 3:
				$src = imagecreatefrompng($file);
				break;
			default:
				return '';
				break;
		}

		$x_ratio = $max_width / $width;
		$y_ratio = $max_height / $height;

		if (($width <= $max_width) && ($height <= $max_height)) {
			$tn_width = $width;
			$tn_height = $height;
		} elseif (($x_ratio * $height) < $max_height) {
			$tn_height = ceil($x_ratio * $height);
			$tn_width = $max_width;
		} else {
			$tn_width = ceil($y_ratio * $width);
			$tn_height = $max_height;
		}

		$tmp = imagecreatetruecolor($tn_width, $tn_height);

		/* Check if this image is PNG or GIF, then set if Transparent*/
		if (($image_type == 1) or ($image_type == 3)) {
			imagealphablending($tmp, false);
			imagesavealpha($tmp, true);
			$transparent = imagecolorallocatealpha($tmp, 255, 255, 255, 127);
			imagefilledrectangle($tmp, 0, 0, $tn_width, $tn_height, $transparent);
		}
		imagecopyresampled($tmp, $src, 0, 0, 0, 0, $tn_width, $tn_height, $width, $height);

		/*
		 * imageXXX() only has two options, save as a file, or send to the browser.
		 * It does not provide you the oppurtunity to manipulate the final GIF/JPG/PNG file stream
		 * So I start the output buffering, use imageXXX() to output the data stream to the browser, 
		 * get the contents of the stream, and use clean to silently discard the buffered contents.
		 */
		ob_start();

		switch ($image_type) {
			case 1:
				imagegif($tmp);
				break;
			case 2:
				imagejpeg($tmp, NULL, 100);
				break; // best quality
			case 3:
				imagepng($tmp, NULL, 0);
				break; // no compression
			default:
				echo '';
				break;
		}

		$final_image = ob_get_contents();

		ob_end_clean();

		return $final_image;
	}
	function LV_InsertAutoTemp($vViTri, $vHinh)
	{
		if ($this->isAdd == 0)
			return false;
		$vField = 'lv' . Fillnum($vViTri, 3);
		$lvsql = "insert into erp_minhphuong_documents_v3_0.cr_lv0382_temp (lv002,lv003,lv004,lv005,lv006,lv007,$vField) values('" . sof_escape_string($this->lv002) . "','" . sof_escape_string($this->lv003) . "','" . sof_escape_string($this->lv004) . "','" . sof_escape_string($this->lv005) . "','" . sof_escape_string($this->lv006) . "','" . sof_escape_string($this->lv007) . "','" . sof_escape_string($vHinh) . "')";
		$vReturn = db_query($lvsql);
		if ($vReturn) {
			$this->lv001 = sof_insert_id();
			$this->InsertLogOperation($this->DateCurrent, 'cr_lv0382_temp.insert', sof_escape_string($lvsql));
		}
		return $vReturn;
	}
	function LV_UpdateAutoTemp($vKetQua, $vViTri, $vHinh)
	{
		if ($this->isEdit == 0)
			return false;
		$vField = 'lv' . Fillnum($vViTri, 3);
		$lvsql = "Update erp_minhphuong_documents_v3_0.cr_lv0382_temp set $vField='" . sof_escape_string($vHinh) . "' where lv001='$vKetQua'";
		$vReturn = db_query($lvsql);
		if ($vReturn)
			$this->InsertLogOperation($this->DateCurrent, 'cr_lv0382_temp.update', sof_escape_string($lvsql));
		return $vReturn;
	}
	function LV_InsertAuto($vViTri, $vHinh)
	{
		if ($this->isAdd == 0)
			return false;
		$vField = 'lv' . Fillnum($vViTri, 3);
		$lvsql = "insert into erp_minhphuong_documents_v3_0.cr_lv0382 (lv002,lv003,lv004,lv005,lv006,lv007,$vField) values('" . sof_escape_string($this->lv002) . "','" . sof_escape_string($this->lv003) . "','" . sof_escape_string($this->lv004) . "','" . sof_escape_string($this->lv005) . "','" . sof_escape_string($this->lv006) . "','" . sof_escape_string($this->lv007) . "','" . sof_escape_string($vHinh) . "')";
		$vReturn = db_query($lvsql);
		if ($vReturn) {
			$this->lv001 = sof_insert_id();
			$this->InsertLogOperation($this->DateCurrent, 'cr_lv0382.insert', sof_escape_string($lvsql));
		}
		return $vReturn;
	}
	function LV_UpdateAuto($vKetQua, $vViTri, $vHinh)
	{
		if ($this->isEdit == 0)
			return false;
		$vField = 'lv' . Fillnum($vViTri, 3);
		$lvsql = "Update erp_minhphuong_documents_v3_0.cr_lv0382 set $vField='" . sof_escape_string($vHinh) . "' where lv001='$vKetQua'";
		$vReturn = db_query($lvsql);
		if ($vReturn)
			$this->InsertLogOperation($this->DateCurrent, 'cr_lv0382.update', sof_escape_string($lvsql));
		return $vReturn;
	}
	function LV_Insert()
	{
		if ($this->isAdd == 0)
			return false;
		$lvsql = "insert into cr_lv0382 (lv001,lv002,lv003,lv004,lv005,lv006,lv007,lv008,lv009,lv010) values('$this->lv001','$this->lv002','$this->lv003','$this->lv004','$this->lv005','$this->lv006','$this->lv007','$this->lv008','$this->LV_UserID',now())";
		$vReturn = db_query($lvsql);
		if ($vReturn) {
			$vInsertID = sof_insert_id();
			$this->InsertLogOperation($this->DateCurrent, 'cr_lv0382.insert', sof_escape_string($lvsql));
			//Attached files
			$lvsql = "insert into erp_minhphuong_documents_v3_0.cr_lv0382(lv002,lv003,lv004,lv005,lv006,lv007,lv008) select '$vInsertID',lv003,lv004,lv005,lv006,lv007,lv008 from erp_minhphuong_documents_v3_0.cr_lv0382_temp where lv007='" . $this->LV_UserID . "'";
			$vReturn = db_query($lvsql);
			if ($vReturn) {
				$this->InsertLogOperation($this->DateCurrent, 'cr_lv0382.insert', sof_escape_string($lvsql));
				$lvsql1 = "delete from erp_minhphuong_documents_v3_0.cr_lv0382_temp where lv007='" . $this->LV_UserID . "'";
				$vReturn = db_query($lvsql1);
			}
		}
		return $vReturn;
	}

	function LV_Update()
	{
		if ($this->isEdit == 0)
			return false;
		$lvsql = "Update cr_lv0382 set lv002='$this->lv002',lv003='$this->lv003',lv004='$this->lv004',lv005='$this->lv005',lv007='$this->lv007',lv008='$this->lv008',lv009='$this->LV_UserID',lv010=now() where  lv001='$this->lv001';";
		$vReturn = db_query($lvsql);
		if ($vReturn)
			$this->InsertLogOperation($this->DateCurrent, 'cr_lv0382.update', sof_escape_string($lvsql));
		return $vReturn;
	}
	function LV_Delete($lvarr)
	{
		if ($this->isDel == 0)
			return false;
		$lvsql = "DELETE FROM cr_lv0382  WHERE lv001 IN ($lvarr)";// and (select count(*) from cr_lv0382 B where  B.lv002= cr_lv0382.lv001)<=0  ";
		$vReturn = db_query($lvsql);
		if ($vReturn) {
			$this->InsertLogOperation($this->DateCurrent, 'cr_lv0382.delete', sof_escape_string($lvsql));
			$lvsql = "DELETE FROM erp_minhphuong_documents_v3_0.cr_lv0382  WHERE lv007 IN ($lvarr)";// and (select count(*) from hr_lv0221 B where  B.lv002= hr_lv0221.lv001)<=0  ";
			$vReturn = db_query($lvsql);
			$this->InsertLogOperation($this->DateCurrent, 'erp_minhphuong_documents_v3_0.cr_lv0382.delete', sof_escape_string($lvsql));
		}
		return $vReturn;
	}
	function LV_Aproval($lvarr)
	{
		if ($this->isApr == 0)
			return false;
		$lvsql = "Update cr_lv0382 set lv006=1  WHERE cr_lv0382.lv001 IN ($lvarr)  ";
		$vReturn = db_query($lvsql);
		if ($vReturn)
			$this->InsertLogOperation($this->DateCurrent, 'cr_lv0382.approval', sof_escape_string($lvsql));
		return $vReturn;
	}
	function LV_UnAproval($lvarr)
	{
		if ($this->isUnApr == 0)
			return false;
		$lvsql = "Update cr_lv0382 set lv006=0  WHERE cr_lv0382.lv001 IN ($lvarr)  ";
		$vReturn = db_query($lvsql);
		if ($vReturn)
			$this->InsertLogOperation($this->DateCurrent, 'cr_lv0382.unapproval', sof_escape_string($lvsql));
		return $vReturn;
	}
	function LV_DeleteUser($vUserID)
	{
		if ($this->isDel == 0)
			return false;
		$lvsql = "DELETE FROM erp_minhphuong_documents_v3_0.cr_lv0382  WHERE cr_lv0382.lv002='$vUserID'";// and (select count(*) from cr_lv0382 B where  B.lv002= cr_lv0382.lv001)<=0  ";
		$vReturn = db_query($lvsql);
		if ($vReturn)
			$this->InsertLogOperation($this->DateCurrent, 'cr_lv0382.delete', sof_escape_string($lvsql));
		return $vReturn;
	}
	////////GetFile Attachment/////////
	function LV_GetForward($AttachFile, $AttachFileHidden)
	{
		$arrFile = explode("|", $AttachFile);
		$arrFileHidden = explode("<@>", $AttachFileHidden);
		$strTrAll = "";
		for ($i = 0; $i < count($arrFile); $i++) {
			$strTemp = $strTr;
			$this->lv001 = InsertWithCheckExt('cr_lv0382', 'lv001', '', 0);
			$this->lv002 = $_SESSION['ERPSOFV2RUserID'];
			$arrTemp1 = explode(">", $arrFile[$i], 2);
			$arrTemp2 = explode("<", $arrTemp1[1], 2);
			$path = $this->Dir . "../images/human/File/MailTemp/";



			$this->lv004 = "bbbg/";
			$this->lv006 = GetServerDate() . " " . GetServerTime();
			$this->lv005 = $arrTemp2[0];
			if ($this->SaveAndGetFile($this->Dir . $arrFileHidden[$i], $path, $this->lv002 . "_" . $this->lv001, $arrTemp2[0])) {
				$this->LV_Insert();
				/*$fp = fopen($this->Dir.$arrFileHidden[$i], "r" );
															 fread( $fp,$strSave );
															 fclose( $fp );			*/
				$strTrAll = $strTrAll . $strTemp;
			}
		}
		return str_replace("@01", $strTrAll, $strTable);

	}

	/////lv SaveAndGetFile
	function SaveAndGetFile($vFileRead, $vFilePath, $folder_name, $vFileName)
	{
		if (create_folder($vFilePath, $folder_name) == true || is_dir($vFilePath . $folder_name)) {
			$strPath = $vFilePath . $folder_name . "/" . $vFileName;
			$handle = fopen($vFileRead, "r");
			$this->lv003 = filesize($vFileRead);

			if ($this->lv003 > 0) {
				$contents = fread($handle, $this->lv003);
				fclose($handle);
				$fp = fopen($strPath, "w");
				fwrite($fp, $contents);
				fclose($fp);
				return true;
			} else
				return false;
		}
	}
	//////////get view///////////////
	function GetView()
	{
		return $this->isView;
	}
	//////////get view///////////////
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
			$strCondi = $strCondi . " and lv001 like '%$this->lv001%'";
		if ($this->lv002 != "")
			$strCondi = $strCondi . " and lv002 = '$this->lv002'";
		if ($this->lv003 != "")
			$strCondi = $strCondi . " and lv003 like '%$this->lv003%'";
		if ($this->lv004 != "")
			$strCondi = $strCondi . " and lv004 like '%$this->lv004%'";
		if ($this->lv005 != "")
			$strCondi = $strCondi . " and lv005 like '%$this->lv005%'";
		if ($this->lv006 != "")
			$strCondi = $strCondi . " and lv006 like '%$this->lv006%'";
		return $strCondi;
	}
	////////////////Count///////////////////////////
	function GetCount()
	{
		$sqlC = "SELECT COUNT(*) AS nums FROM cr_lv0382 WHERE 1=1 " . $this->GetCondition();
		$bResultC = db_query($sqlC);
		$arrRowC = db_fetch_array($bResultC);
		return $arrRowC['nums'];
	}
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
		$lvTr = "<tr class=\"lvlinehtable@01\">
			<td width=1% onclick=\"Select_Check('$lvChk@03',$lvFrom, '$lvChk', '$lvChkAll')\">@03</td>
			<td width=1%><input name=\"$lvChk\" type=\"checkbox\" id=\"$lvChk@03\" onclick=\"CheckOne($lvFrom, '$lvChk', '$lvChkAll', this)\" value=\"@02\" tabindex=\"2\"  onKeyUp=\"return CheckKeyCheck(event,2,'$lvChk',$lvFrom, '$lvChk', '$lvChkAll',@03)\"/></td>
			@#01
		</tr>
		";
		$lvHref = "<a href=\"javascript:FunctRunning1('@01')\" style=\"text-decoration:none\">@02</a>";
		$lvTdH = "<td width=\"@01\" class=\"lvhtable\">@02</td>";
		$lvTd = "<td align=@#05>@02</td>";
		$sqlS = "SELECT * FROM cr_lv0382 WHERE 1=1  " . $this->GetCondition() . " $strSort LIMIT $curRow, $maxRows";
		$vorder = $curRow;
		$bResult = db_query($sqlS);
		$this->Count = db_num_rows($bResult);
		$strTrH = "";
		$strTr = "";
		for ($i = 0; $i < count($lstArr); $i++) {
			$vTemp = str_replace("@01", "", $lvTdH);
			$vTemp = str_replace("@02", $this->ArrPush[(int) $this->ArrGet[$lstArr[$i]]], $vTemp);
			$strH = $strH . $vTemp;
			$vField = $lstArr[$i];
			$vStringNumber = "";
			if ($this->ArrViewEnter[$vField] == null)
				$this->ArrViewEnter[$vField] = 0;
			$vStringNumber = "";
			switch ($this->ArrView[$vField]) {
				case '10':
				case '20':
				case '1':
					$vStringNumber = ' onfocus="LayLaiGiaTri(this)" onblur="SetGiaTri(this);" ';
					break;
			}
			switch ($this->ArrViewEnter[$vField]) {
				//browfile
				case 11:
					$lvImg = "<a target='_blank' href='../cr_lv0382/readfiletemp.php?FileID=" . $vrow['lv001'] . "&type=8&size=0'><div style='border-radius:5px;background:#aaa;padding:4px;color:#000;width:40px;text-align:center;'>View</div></a>";
					$vstr = '
						<table valign=\"top\" style=\"width:100%\">
							<tr>
							
							
								<td title=\"Download file pdf\">
								<div id="framuploadtemp" style="width:116px">
									<iframe  height=24 width="100%" marginheight=0 marginwidth=0 frameborder=0 src="../cr_lv0382/?childfunc=uploadtemp&ViTriUp=8&FileID=' . $vrow['lv001'] . '&lang=' . $this->lang . '"></iframe>
								</td>
								<td align=\"center\">
								<div style=\"text-align:center\" id="attachfiletemp">' . $lvImg . '</div>
							</td>
							</tr>
						</table>	
								';
					$vTempEnter = str_replace("@02", $vstr, $this->Align($lvTd, (int) $this->ArrView[$lstArr[$i]]));
					break;
				case 99:
					if ($this->isPopupPlus == 0)
						$this->isPopupPlus = 1;
					$vstr = '<ul style="width:100%" id="pop-nav" lang="pop-nav1" onMouseOver="ChangeName(this,1)" onKeyUp="ChangeName(this,1)"> <li class="menupopT">
									<input autocomplete="off" class="txtenterquick" type="text" autocomplete="off" style="width:100%;min-width:30px" name="qxt' . $vField . '" id="qxt' . $vField . '" onKeyUp="LoadSelfNext(this,\'qxt' . $vField . '\',\'' . $this->Tables[$vField] . '\',\'lv001\',\'concat(lv002,@! @!,lv001)\')"  onKeyPress="return CheckKey(event,7)" tabindex="2" value="' . $this->Values[$vField] . '">
									<div id="lv_popup' . (($this->isPopupPlus == 1) ? '' : $this->isPopupPlus) . '" lang="lv_popup' . $this->isPopupPlus . '"> </div>						  
									</li>
								</ul>';
					$this->isPopupPlus++;
					$vTempEnter = str_replace("@02", $vstr, $this->Align($lvTd, (int) $this->ArrView[$lstArr[$i]]));
					break;
				case 88:
					$vstr = '<select class="selenterquick" name="qxt' . $vField . '" id="qxt' . $vField . '" tabindex="2" style="width:100%;min-width:30px" onKeyPress="return CheckKey(event,7)">' . $this->LV_LinkField($vField, $this->Values[$vField]) . '</select>';
					$vTempEnter = str_replace("@02", $vstr, $this->Align($lvTd, (int) $this->ArrView[$lstArr[$i]]));
					break;
				case 89:
					$vstr = '<select class="selenterquick" name="qxt' . $vField . '" id="qxt' . $vField . '" tabindex="2" style="width:100%;min-width:30px" onKeyPress="return CheckKey(event,7)">
								<option value="">...</option>
							' . $this->LV_LinkField($vField, $this->Values[$vField]) . '</select>';
					$vTempEnter = str_replace("@02", $vstr, $this->Align($lvTd, (int) $this->ArrView[$lstArr[$i]]));
					break;
				case 4:
					$vstr = '<table><tr><td><input autocomplete="off" class="txtenterquick"  autocomplete="off" name="qxt' . $vField . '_1" type="text" id="qxt' . $vField . '_1" value="' . $this->Values[$vField] . '" tabindex="2" maxlength="32" style="width:100%;min-width:80px" onKeyPress="return CheckKey(event,7)" ondblclick="if(self.gfPop)gfPop.fPopCalendar(this);return false;"></td><td><input class="txtenterquick"  autocomplete="off" name="qxt' . $vField . '_2" type="text" id="qxt' . $vField . '_2" value="' . $this->Values[$vField] . '" tabindex="2" maxlength="32" style="width:50%;min-width:60px" onKeyPress="return CheckKey(event,7)" ></td></tr></table>';
					$vTempEnter = str_replace("@02", $vstr, $this->Align($lvTd, (int) $this->ArrView[$lstArr[$i]]));
					break;
				case 22:
				case 2:
					$vstr = '<input autocomplete="off" class="txtenterquick"  autocomplete="off" name="qxt' . $vField . '" type="text" id="qxt' . $vField . '" value="' . $this->Values[$vField] . '" tabindex="2" maxlength="32" style="width:100%;min-width:60px" onKeyPress="return CheckKey(event,7)" ondblclick="if(self.gfPop)gfPop.fPopCalendar(this);return false;">';
					$vTempEnter = str_replace("@02", $vstr, $this->Align($lvTd, (int) $this->ArrView[$lstArr[$i]]));
					break;
				case 33:
					$vstr = '<input autocomplete="off" class="txtenterquick" name="qxt' . $vField . '" type="checkbox" id="qxt' . $vField . '" value="1" ' . (($this->Values[$vField] == 1) ? 'checked="true"' : '') . ' tabindex="2" style="width:100%;min-width:30px;text-align:center;" onKeyPress="return CheckKey(event,7)">';
					$vTempEnter = str_replace("@02", $vstr, $this->Align($lvTd, (int) $this->ArrView[$lstArr[$i]]));
					break;
				case 0:
					$vstr = '<input ' . $vStringNumber . ' autocomplete="off" class="txtenterquick" name="qxt' . $vField . '" type="text" id="qxt' . $vField . '" value="' . $this->Values[$vField] . '" tabindex="2" style="width:100%;min-width:30px;text-align:center;" onKeyPress="return CheckKey(event,7)">';
					$vTempEnter = str_replace("@02", $vstr, $this->Align($lvTd, (int) $this->ArrView[$lstArr[$i]]));
					break;
				default:
					$vTempEnter = "<td>&nbsp;</td>";
					break;
			}
			$strTrEnter = $strTrEnter . $vTempEnter;
			$strTrEnterEmpty = $strTrEnterEmpty . "<td>&nbsp;</td>";
		}
		if ($this->isAdd == 1)
			$strTrEnter = "<tr class='entermobil'><td colspan='2'>" . '<img tabindex="2" border="0" title="Add" class="imgButton" onclick="Save()" onmouseout="this.src=\'../../images/iconcontrol/btn_add.jpg\';" onmouseover="this.src=\'../../images/iconcontrol/btn_add_02.jpg\';" src="../../images/iconcontrol/btn_add.jpg" onkeypress="return CheckKey(event,11)">' . "</td>" . $strTrEnter . "</tr>";
		else
			$strTrEnter = "";//"<tr class='entermobil'><td colspan='2'>".'&nbsp;'."</td>".$strTrEnterEmpty."</tr>";
		while ($vrow = db_fetch_array($bResult)) {
			$strL = "";
			$vorder++;
			for ($i = 0; $i < count($lstArr); $i++) {
				switch ($lstArr[$i]) {
					case 'lv199':
						$vChucNang = "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\">
					<tr>
					";


						$vChucNang = $vChucNang . '<td><img style="cursor:pointer;height:25px;padding:5px;" onclick="Report(\'' . $vrow['lv001'] . '\')" alt="NoImg" src="../images/icon/Rpt.png" align="middle" border="0" name="new" class="lviconimg"></td>';

						$vChucNang = $vChucNang . "</tr></table>";
						$vTemp = str_replace("@02", $vChucNang, $this->Align($lvTd, (int) $this->ArrView[$lstArr[$i]]));
						break;
					case 'lv001':
					case 'lv002':
					case 'lv009':
					case 'lv010':
						$vTemp = str_replace("@02", $this->getvaluelink($lstArr[$i], $this->FormatView($vrow[$lstArr[$i]], (int) $this->ArrView[$lstArr[$i]])), $this->Align($lvTd, (int) $this->ArrView[$lstArr[$i]]));
						break;
					case 'lv007';
						if ($this->GetEdit() == 1) {
							$lvTdTextBox = "<td align=center><input class='txtenterquick' type=\"checkbox\" value=\"1\" " . (($vrow['lv007'] == 1) ? 'checked="true"' : '') . " @03 onclick=\"UpdateTextCheck(this,'" . $vrow['lv001'] . "',7)\" style=\"width:35px;text-align:center;\" tabindex=\"2\" maxlength=\"32\"   onKeyPress=\"return CheckKey(event,7)\"/></td>";
							$vTemp = str_replace("@02", $this->FormatView($vrow[$lstArr[$i]], 0), $this->Align(str_replace("@01", $vrow['lv001'], $lvTdTextBox), (int) $this->ArrView[$lstArr[$i]]));
						} else
							$vTemp = str_replace("@02", $this->getvaluelink($lstArr[$i], $this->FormatView($vrow[$lstArr[$i]], (int) $this->ArrView[$lstArr[$i]])), $this->Align($lvTd, (int) $this->ArrView[$lstArr[$i]]));
						break;
					case 'lv005':
						$lvImg = "<center><a target='_blank' href='" . $this->Dir . "cr_lv0382/readfile.php?UserID=" . $vrow['lv001'] . "&type=8&size=0'><img name=\"imgView\" border=\"0\" style=\"border-color:#CCCCCC\" title=\"\" alt=\"Image\" width1=\"96px\" height=\"48px\" src=\"" . $this->Dir . "cr_lv0382/readfile.php?UserID=" . $vrow['lv001'] . "&type=8&size=1\" /></a></center>";
						$vTempEnter = '
							<table valign=\"top\" style=\"width:100%\">
							
								<tr>
								<td align=\"center\">
									<div style=\"text-align:center\" id="attachfile_8_' . $vrow['lv001'] . '">' . $lvImg . '</div>
								</td>
								</tr>
								<tr>
								' . '
								<td title=\"Tải tập tin pdf\">
								<div id="framupload_8_' . $vrow['lv001'] . '" style="width:106px">
								<iframe  height=24 width="100%" marginheight=0 marginwidth=0 frameborder=0 src="' . $this->Dir . 'cr_lv0382/?childfunc=upload&ViTriUp=8&FileID=' . $vrow['lv001'] . '&lang=' . $this->lang . '"></iframe>
								</div>
								</td>' . '
								</tr>	
							</table>	
								';
						$vTemp = str_replace("@02", str_replace("@02", $vTempEnter, str_replace("@01", $vrow['lv001'], $lvHref)), $this->Align($lvTd, (int) $this->ArrView[$lstArr[$i]]));

						break;
					case 'lv011':
						$lvImg = "<center><a target='_blank' href='" . $this->Dir . "cr_lv0382/readfile.php?UserID=" . $vrow['lv001'] . "&type=9&size=0'><img name=\"imgView\" border=\"0\" style=\"border-color:#CCCCCC\" title=\"\" alt=\"Image\" width1=\"96px\" height=\"48px\" src=\"" . $this->Dir . "cr_lv0382/readfile.php?UserID=" . $vrow['lv001'] . "&type=9&size=1\" /></a></center>";
						$vTempEnter = '
							<table valign=\"top\" style=\"width:100%\">
							
								<tr>
								<td align=\"center\">
									<div style=\"text-align:center\" id="attachfile_9_' . $vrow['lv001'] . '">' . $lvImg . '</div>
								</td>
								</tr>
								<tr>
								' . '
								<td title=\"Tải tập tin pdf\">
								<div id="framupload_9_' . $vrow['lv001'] . '" style="width:106px">
								<iframe  height=24 width="100%" marginheight=0 marginwidth=0 frameborder=0 src="' . $this->Dir . 'cr_lv0382/?childfunc=upload&ViTriUp=9&FileID=' . $vrow['lv001'] . '&lang=' . $this->lang . '"></iframe>
								</div>
								</td>' . '
								</tr>	
							</table>	
								';
						$vTemp = str_replace("@02", str_replace("@02", $vTempEnter, str_replace("@01", $vrow['lv001'], $lvHref)), $this->Align($lvTd, (int) $this->ArrView[$lstArr[$i]]));

						break;
					case 'lv012':
						$lvImg = "<center><a target='_blank' href='" . $this->Dir . "cr_lv0382/readfile.php?UserID=" . $vrow['lv001'] . "&type=10&size=0'><img name=\"imgView\" border=\"0\" style=\"border-color:#CCCCCC\" title=\"\" alt=\"Image\" width1=\"96px\" height=\"48px\" src=\"" . $this->Dir . "cr_lv0382/readfile.php?UserID=" . $vrow['lv001'] . "&type=10&size=1\" /></a></center>";
						$vTempEnter = '
							<table valign=\"top\" style=\"width:100%\">
							
								<tr>
								<td align=\"center\">
									<div style=\"text-align:center\" id="attachfile_10_' . $vrow['lv001'] . '">' . $lvImg . '</div>
								</td>
								</tr>
								<tr>
								' . '
								<td title=\"Tải tập tin pdf\">
								<div id="framupload_10_' . $vrow['lv001'] . '" style="width:106px">
								<iframe  height=24 width="100%" marginheight=0 marginwidth=0 frameborder=0 src="' . $this->Dir . 'cr_lv0382/?childfunc=upload&ViTriUp=10&FileID=' . $vrow['lv001'] . '&lang=' . $this->lang . '"></iframe>
								</div>
								</td>' . '
								</tr>	
							</table>	
								';
						$vTemp = str_replace("@02", str_replace("@02", $vTempEnter, str_replace("@01", $vrow['lv001'], $lvHref)), $this->Align($lvTd, (int) $this->ArrView[$lstArr[$i]]));

						break;
					case 'lv013':
						$lvImg = "<center><a target='_blank' href='" . $this->Dir . "cr_lv0382/readfile.php?UserID=" . $vrow['lv001'] . "&type=11&size=0'><img name=\"imgView\" border=\"0\" style=\"border-color:#CCCCCC\" title=\"\" alt=\"Image\" width1=\"96px\" height=\"48px\" src=\"" . $this->Dir . "cr_lv0382/readfile.php?UserID=" . $vrow['lv001'] . "&type=11&size=1\" /></a></center>";
						$vTempEnter = '
							<table valign=\"top\" style=\"width:100%\">
							
								<tr>
								<td align=\"center\">
									<div style=\"text-align:center\" id="attachfile_11_' . $vrow['lv001'] . '">' . $lvImg . '</div>
								</td>
								</tr>
								<tr>
								' . '
								<td title=\"Tải tập tin pdf\">
								<div id="framupload_11_' . $vrow['lv001'] . '" style="width:106px">
								<iframe  height=24 width="100%" marginheight=0 marginwidth=0 frameborder=0 src="' . $this->Dir . 'cr_lv0382/?childfunc=upload&ViTriUp=11&FileID=' . $vrow['lv001'] . '&lang=' . $this->lang . '"></iframe>
								</div>
								</td>' . '
								</tr>	
							</table>	
								';
						$vTemp = str_replace("@02", str_replace("@02", $vTempEnter, str_replace("@01", $vrow['lv001'], $lvHref)), $this->Align($lvTd, (int) $this->ArrView[$lstArr[$i]]));

						break;
					case 'lv014':
						$lvImg = "<center><a target='_blank' href='" . $this->Dir . "cr_lv0382/readfile.php?UserID=" . $vrow['lv001'] . "&type=12&size=0'><img name=\"imgView\" border=\"0\" style=\"border-color:#CCCCCC\" title=\"\" alt=\"Image\" width1=\"96px\" height=\"48px\" src=\"" . $this->Dir . "cr_lv0382/readfile.php?UserID=" . $vrow['lv001'] . "&type=12&size=1\" /></a></center>";
						$vTempEnter = '
							<table valign=\"top\" style=\"width:100%\">
							
								<tr>
								<td align=\"center\">
									<div style=\"text-align:center\" id="attachfile_12_' . $vrow['lv001'] . '">' . $lvImg . '</div>
								</td>
								</tr>
								<tr>
								' . '
								<td title=\"Tải tập tin pdf\">
								<div id="framupload_12_' . $vrow['lv001'] . '" style="width:106px">
								<iframe  height=24 width="100%" marginheight=0 marginwidth=0 frameborder=0 src="' . $this->Dir . 'cr_lv0382/?childfunc=upload&ViTriUp=12&FileID=' . $vrow['lv001'] . '&lang=' . $this->lang . '"></iframe>
								</div>
								</td>' . '
								</tr>	
							</table>	
								';
						$vTemp = str_replace("@02", str_replace("@02", $vTempEnter, str_replace("@01", $vrow['lv001'], $lvHref)), $this->Align($lvTd, (int) $this->ArrView[$lstArr[$i]]));

						break;
					case 'lv015':
						$lvImg = "<center><a target='_blank' href='" . $this->Dir . "cr_lv0382/readfile.php?UserID=" . $vrow['lv001'] . "&type=13&size=0'><img name=\"imgView\" border=\"0\" style=\"border-color:#CCCCCC\" title=\"\" alt=\"Image\" width1=\"96px\" height=\"48px\" src=\"" . $this->Dir . "cr_lv0382/readfile.php?UserID=" . $vrow['lv001'] . "&type=13&size=1\" /></a></center>";
						$vTempEnter = '
							<table valign=\"top\" style=\"width:100%\">
							
								<tr>
								<td align=\"center\">
									<div style=\"text-align:center\" id="attachfile_13_' . $vrow['lv001'] . '">' . $lvImg . '</div>
								</td>
								</tr>
								<tr>
								' . '
								<td title=\"Tải tập tin pdf\">
								<div id="framupload_13_' . $vrow['lv001'] . '" style="width:106px">
								<iframe  height=24 width="100%" marginheight=0 marginwidth=0 frameborder=0 src="' . $this->Dir . 'cr_lv0382/?childfunc=upload&ViTriUp=13&FileID=' . $vrow['lv001'] . '&lang=' . $this->lang . '"></iframe>
								</div>
								</td>' . '
								</tr>	
							</table>	
								';
						$vTemp = str_replace("@02", str_replace("@02", $vTempEnter, str_replace("@01", $vrow['lv001'], $lvHref)), $this->Align($lvTd, (int) $this->ArrView[$lstArr[$i]]));
						break;
					case 'lv016':
						$lvImg = "<center><a target='_blank' href='" . $this->Dir . "cr_lv0382/readfile.php?UserID=" . $vrow['lv001'] . "&type=14&size=0'><img name=\"imgView\" border=\"0\" style=\"border-color:#CCCCCC\" title=\"\" alt=\"Image\" width1=\"96px\" height=\"48px\" src=\"" . $this->Dir . "cr_lv0382/readfile.php?UserID=" . $vrow['lv001'] . "&type=14&size=1\" /></a></center>";
						$vTempEnter = '
							<table valign=\"top\" style=\"width:100%\">
							
								<tr>
								<td align=\"center\">
									<div style=\"text-align:center\" id="attachfile_14_' . $vrow['lv001'] . '">' . $lvImg . '</div>
								</td>
								</tr>
								<tr>
								' . '
								<td title=\"Tải tập tin pdf\">
								<div id="framupload_14_' . $vrow['lv001'] . '" style="width:106px">
								<iframe  height=24 width="100%" marginheight=0 marginwidth=0 frameborder=0 src="' . $this->Dir . 'cr_lv0382/?childfunc=upload&ViTriUp=14&FileID=' . $vrow['lv001'] . '&lang=' . $this->lang . '"></iframe>
								</div>
								</td>' . '
								</tr>	
							</table>	
								';
						$vTemp = str_replace("@02", str_replace("@02", $vTempEnter, str_replace("@01", $vrow['lv001'], $lvHref)), $this->Align($lvTd, (int) $this->ArrView[$lstArr[$i]]));

						break;
					case 'lv017':
						$lvImg = "<center><a target='_blank' href='" . $this->Dir . "cr_lv0382/readfile.php?UserID=" . $vrow['lv001'] . "&type=15&size=0'><img name=\"imgView\" border=\"0\" style=\"border-color:#CCCCCC\" title=\"\" alt=\"Image\" width1=\"96px\" height=\"48px\" src=\"" . $this->Dir . "cr_lv0382/readfile.php?UserID=" . $vrow['lv001'] . "&type=15&size=1\" /></a></center>";
						$vTempEnter = '
							<table valign=\"top\" style=\"width:100%\">
							
								<tr>
								<td align=\"center\">
									<div style=\"text-align:center\" id="attachfile_15_' . $vrow['lv001'] . '">' . $lvImg . '</div>
								</td>
								</tr>
								<tr>
								' . '
								<td title=\"Tải tập tin pdf\">
								<div id="framupload_15_' . $vrow['lv001'] . '" style="width:106px">
								<iframe  height=24 width="100%" marginheight=0 marginwidth=0 frameborder=0 src="' . $this->Dir . 'cr_lv0382/?childfunc=upload&ViTriUp=15&FileID=' . $vrow['lv001'] . '&lang=' . $this->lang . '"></iframe>
								</div>
								</td>' . '
								</tr>	
							</table>	
								';
						$vTemp = str_replace("@02", str_replace("@02", $vTempEnter, str_replace("@01", $vrow['lv001'], $lvHref)), $this->Align($lvTd, (int) $this->ArrView[$lstArr[$i]]));

						break;
					case 'lv018':
						$lvImg = "<center><a target='_blank' href='" . $this->Dir . "cr_lv0382/readfile.php?UserID=" . $vrow['lv001'] . "&type=16&size=0'><img name=\"imgView\" border=\"0\" style=\"border-color:#CCCCCC\" title=\"\" alt=\"Image\" width1=\"96px\" height=\"48px\" src=\"" . $this->Dir . "cr_lv0382/readfile.php?UserID=" . $vrow['lv001'] . "&type=16&size=1\" /></a></center>";
						$vTempEnter = '
							<table valign=\"top\" style=\"width:100%\">
							
								<tr>
								<td align=\"center\">
									<div style=\"text-align:center\" id="attachfile_16_' . $vrow['lv001'] . '">' . $lvImg . '</div>
								</td>
								</tr>
								<tr>
								' . '
								<td title=\"Tải tập tin pdf\">
								<div id="framupload_16_' . $vrow['lv001'] . '" style="width:106px">
								<iframe  height=24 width="100%" marginheight=0 marginwidth=0 frameborder=0 src="' . $this->Dir . 'cr_lv0382/?childfunc=upload&ViTriUp=16&FileID=' . $vrow['lv001'] . '&lang=' . $this->lang . '"></iframe>
								</div>
								</td>' . '
								</tr>	
							</table>	
								';
						$vTemp = str_replace("@02", str_replace("@02", $vTempEnter, str_replace("@01", $vrow['lv001'], $lvHref)), $this->Align($lvTd, (int) $this->ArrView[$lstArr[$i]]));

						break;
					default:
						if (($this->isEdit == 1)) {
							$vGiaTri = $vrow[$lstArr[$i]];
							$vSoTT = (int) str_replace('lv', '', $lstArr[$i]);
							$lvTdTextBox = "<input class='txtenterquick' type=\"textbox\" value=\"" . $vGiaTri . "\" @03 onfocus=\"if(this.value=='') this.value='" . $vGiaTri . "'\" onblur=\"UpdateText(this,'" . $vrow['lv001'] . "',$vSoTT)\" style=\"min-width:120px;width:100%;text-align:center\" tabindex=\"2\"  maxlength=\"255\" onKeyPress=\"return CheckKey(event,7)\"/>";
							$vTemp = str_replace("@02", $lvTdTextBox, $this->Align($lvTd, (int) $this->ArrView[$lstArr[$i]]));
						} else {
							$vTemp = str_replace("@02", $this->getvaluelink($lstArr[$i], $this->FormatView($vrow[$lstArr[$i]], (int) $this->ArrView[$lstArr[$i]])), $this->Align($lvTd, (int) $this->ArrView[$lstArr[$i]]));
						}
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
			window.open('$this->Dir'+'cr_lv0382/?lang=" . $this->lang . "&childdetailfunc='+value+'&ID=" . base64_encode($this->lv002) . "','','width=800,height=600,left=200,top=100,screenX=0,screenY=100,resizable=yes,status=no,scrollbars=yes,menubar=yes');
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
	function LV_BuilListReport($lvList, $lvFrom, $lvChkAll, $lvChk, $curRow, $maxRows, $paging, $lvOrderList, $lvSortNum)
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
		$sqlS = "SELECT * FROM cr_lv0382 WHERE 1=1  " . $this->RptCondition . " $strSort LIMIT $curRow, $maxRows";
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
			case 'lv003':
				$vsql = "select lv001, lv002,IF(lv001='$vSelectID',1,0) lv003 from  hr_lv0040";
				break;
			case 'lv006':
				$vsql = "select lv001,lv002,IF(lv001='$vSelectID',1,0) lv003 from  hr_lv0240";
				break;
		}
		return $vsql;
	}
	private function getvaluelink($vFile, $vSelectID)
	{
		if ($this->ArrGetValueLink[$vFile][$vSelectID][0])
			return $this->ArrGetValueLink[$vFile][$vSelectID][1];
		if ($vSelectID == "") {
			return $vSelectID;
		}
		switch ($vFile) {

			case 'lv006':
				$vsql = "select lv001,lv002,IF(lv001='$vSelectID',1,0) lv003 from  hr_lv0240 where lv001='$vSelectID'";
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
	function getChiTietPhieu($lv001)
	{
		$vArrRe = [];
		$vsql = "SELECT * FROM `cr_lv0382` WHERE lv002 = '$lv001'";
		$vresult = db_query($vsql);
		while ($vrow = mysqli_fetch_assoc($vresult)) {
			$vArrRe[] = $vrow;
		}
		return $vArrRe;
	}


	function themMoi($lv001, $lv003, $lv008)
	{

		$success = true;
		$errorMessage = '';
		$vsql = "INSERT INTO cr_lv0382 (lv002, lv003, lv008) VALUES ('$lv001', '$lv003', $lv008)";
		$vReturn = db_query($vsql);
		if (!$vReturn) {
			$success = false;
			$errorMessage .= "Lỗi thêm dữ liệu";
		}
		return [
			'success' => $success,
			'message' => "thêm thành công"
		];
	}



	function xoa($lv001)
	{
		$success = true;
		$errorMessage = '';
		$vsql = "DELETE FROM `cr_lv0382` WHERE lv001 = '$lv001'";
		$vReturn = db_query($vsql);
		if (!$vReturn) {
			$success = false;
			$errorMessage .= "Lỗi xoá dữ liệu";
		}
		return [
			'success' => $success,
			'message' => "Xoá thành công"
		];
	}


	function sua($lv001, $lv003, $lv008)
	{
		$success = true;
		$errorMessage = '';
		$vsql = "UPDATE `cr_lv0382` SET 
		lv003 = '" . sof_escape_string($lv003) . "',   
		lv008 = '" . sof_escape_string($lv008) . "'
		WHERE lv001 = '$lv001'";
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



}
?>