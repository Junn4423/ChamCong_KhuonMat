<?php
define("DB_SERVER", "127.0.0.1");
define("DB_USER", "root");
define("DB_PWD", "SofSql@2025.");
define("DB_DATABASE", "erp_sofv4_0");
define("No_Date_Default", "1900-01-01");
global $pListFolder;
date_default_timezone_set('Asia/Ho_Chi_Minh');
function db_connect()
{
	global $db_link;
	if ($db_link) return $db_link;
	$db_link = mysqli_connect(DB_SERVER, DB_USER, DB_PWD);
	if ($db_link) mysqli_select_db($db_link, DB_DATABASE);
	mysqli_query($db_link, 'SET NAMES utf8');
	return $db_link;
}
function sof_escape_string($str)
{
	//return $str;
	global $db_link;
	return mysqli_real_escape_string($db_link, $str);
}
function sof_insert_id()
{
	global $db_link;
	return mysqli_insert_id($db_link);
}
function sof_error()
{
	global $db_link;
	return mysqli_error($db_link);
}
function db_close()
{
	global $db_link;
	$result = mysqli_close($db_link);
	return $db_link;
}
function db_query($db_query)
{
	global $db_link;
	$result = mysqli_query(db_connect(), $db_query);
	return $result;
}
function db_fetch_array($db_query)
{
	if ($db_query == NULL) return NULL;
	$result = mysqli_fetch_array($db_query);
	return $result;
}
function db_num_rows($db_query)
{
	if ($db_query == NULL) return -1;
	$result = mysqli_num_rows($db_query);
	return $result;
}
//general function
function redirect($page)
{
	//echo"<meta http-equiv='refresh' content='0;url=".$page."'>\\n";	
	echo "<meta http-equiv='refresh' content='0;url=" . $page . "'>\n";
}
function redirect2($page, $vSecond)
{
	//echo"<meta http-equiv='refresh' content='0;url=".$page."'>\\n";	
	echo "<meta http-equiv='refresh' content='" . $vSecond . ";url=" . $page . "'>\n";
}
function show_error($err)
{
	$strerr = "";
	switch ($err) {
		case 1:
			$strerr = "<font class=std ><p align=center>Invalid Password or Username</font>";
			break;
		case 2:
			$strerr = "<font class=std ><p align=center> Chua DAng Ky session</font>";
			break;
		case 3:
			$strerr = "";
			break;
	}
	return $strerr;
}
//get viet nam date 
function m_Getdate()
{
	$date = date('d') . "/" . date('m') . "/" . date('Y');
	return ($date);
}
//convert mysql date to vn date
function vn_date($g_date)
{
	if ($g_date != "" || $g_date != NULL) {
		$y = substr($g_date, 0, 4);
		$m = substr($g_date, 5, 2);
		$d = substr($g_date, 8, 2);
		return ($d . "/" . $m . "/" . $y);
	} else {
		return ("&nbsp;"); //NULL
	}
}
function cen_date($g_date)
{ //input: mm/dd/yyyy
	$g_date = check_date($g_date);
	$m = substr($g_date, 0, 2);
	$d = substr($g_date, 3, 2);
	$y = substr($g_date, 6, 4);
	return ($y . "/" . $m . "/" . $d);
	//output: yyyy/mm/dd
}
function cen_date_vn($g_date)
{ //input: dd-mm-yyyy
	$d = substr($g_date, 0, 2);
	$m = substr($g_date, 3, 2);
	$y = substr($g_date, 6, 4);
	return ($y . "-" . $m . "-" . $d);
	//output: yyyy-mm-dd
}
function check_date($g_date)
{
	$g_date = str_replace("-", "/", $g_date);
	$vArr = explode("/", $g_date);
	return Fillnum($vArr[0], 2) . "/" . Fillnum($vArr[1], 2) . "/" . Fillnum($vArr[2], 4);
}
function cvn_date($g_date)
{
	$g_date = check_date($g_date);
	$d = substr($g_date, 0, 2);
	$m = substr($g_date, 3, 2);
	$y = substr($g_date, 6, 4);
	return ($y . "/" . $m . "/" . $d);
}
function formatdate($v_date, $vLang)
{
	if (trim($v_date) == "" || $v_date == NULL) return "";
	if ($vLang == "VN" || $vLang == "vn") {
		return	vn_date($v_date);
	} else {
		return	en_date($v_date);
	}
}
function recoverdate($v_date, $vLang)
{
	$v_date = trim($v_date);
	if (strlen($v_date) == 4) $v_date = "01/01/" . $v_date;
	if ($vLang == "VN" || $vLang == "vn") {
		return	cvn_date($v_date);
	} else {
		return	cen_date($v_date);
	}
}
//Get Time 
function gettime($g_date)
{
	return substr($g_date, 11, 10);
}
//Get Date belong to English
function en_date($g_date)
{ //input: yyyy/mm/dd
	if ($g_date != "" || $g_date != NULL) {
		$y = substr($g_date, 0, 4);
		$m = substr($g_date, 5, 2);
		$d = substr($g_date, 8, 2);
		return ($m . "/" . $d . "/" . $y);
	} else {
		return ("&nbsp;"); //NULL
	}
	//output: mm/dd/yyyy
}
function en_date_vn($g_date)
{ //input: yyyy/mm/dd
	$y = substr($g_date, 0, 4);
	$m = substr($g_date, 5, 2);
	$d = substr($g_date, 8, 2);
	return ($d . "-" . $m . "-" . $y);
	//output: dd-mm-yyyy
}
function getyear($g_date)
{
	return (substr($g_date, 0, 4));
}
function getmonth($g_date)
{
	return (substr($g_date, 5, 2));
}
function getday($g_date)
{
	return (substr($g_date, 8, 2));
}
/////convert vn date to mysql date
function mysql_date($g_date)
{
	$d = substr($g_date, 0, 2);
	$m = substr($g_date, 3, 2);
	$y = substr($g_date, 6, 4);
	return ($y . "-" . $m . "-" . $d);
}
/////////////////////////////////////connect to database//db_connect() or die("can not connect to  database");
//ma hoa password
function crypt_pwd($plain_pass)
{
	/* create a semi random salt */
	mt_srand((float) microtime() * 1000000);
	for ($i = 0; $i < 10; $i++) {
		$tstring	.= mt_rand();
	}
	$salt = substr(md5($tstring), 0, 2);
	$passtring = $salt . $plain_pass;
	$encrypted = md5($passtring);
	return ($encrypted . ":" . $salt);
	//return(md5($plain_pass));
} // function crypt_password($plain_pass)
//function get new id
function newID($tablename, $column)
{
	$id = db_fetch_array(db_query("select max(" . $column . ") as id from $tablename"));
	$id = $id["id"] + 1;
	return ($id);
}
//function check exist value in table
function checkexist($tablename, $column, $value)
{
	$result = db_num_rows(db_query("select * from " . $tablename . " where " . $column . " like '" . $value . "%'"));
	if ($result != 0) return true;
	return false;
}
/*thuc hien chuc nang upload file len server
cac bien :$chua_file=ten doi tuong chua file tren form, $folder=ten folder de chua file upload len server.
tra ve cac gia tri tuong ung voi $error cua no.*/
function do_upload($chua_file, $ten_moi, $folder)
{
	$error = 0;
	$file_type = array(".jpg", ".gif", ".bmp", ".jpeg", ".JPG", ".GIF", ".BMP", ".JPEG");
	$file_news_img = $_FILES[$chua_file]['name'];
	if (trim($_FILES[$chua_file]['name']) != "") {
		$ext = strrchr($_FILES[$chua_file]['name'], ".");
		if (!in_array($ext, $file_type)) {
			$error = 1; //sai phan mo rong cua file.
		}
		if ($_FILES[$chua_file]['size'] <= 0 || $_FILES[$chua_file]['size'] >= 4000000) {
			$error = 2; //Kich thuoc vuot qua gioi han cho phep
		}
	}
	if ($error == 0) {
		$extension = substr($_FILES[$chua_file]['name'], -4);
		$new_name = $ten_moi . $extension;
		move_uploaded_file($_FILES[$chua_file]['tmp_name'], $folder . $new_name);
	}
	return $error;
}
//-----------------------------------------------------
//upload file to server
function upload_file($userfile, $new_name, $path, $maxsize)
{ ///* Ham nay da duoc sua mot so cau truc if ... else o phan duoi khi su dung cho file post resume
	//echo "file:".$userfile." name:".$userfile_name." size:".$userfile_size." type:".$userfile_type;
	//Chu y: 
	//1).'userfile' la ten cua textbox type=file
	//2).form dung pthuc POST va phai co enctype="multipart/form-data"
	$flag = 1;
	$userfile = $_FILES['userfile']['tmp_name'];
	$userfile_name = $_FILES['userfile']['name'];
	$userfile_size = $_FILES['userfile']['size'];
	$userfile_type = $_FILES['userfile']['type'];
	if ($_FILES['userfile'] != "") {
		$file_type = array(".docx", ".DOCX", ".doc", ".pdf", ".xls", ".xlsx", ".rtf", ".txt", ".zip", ".rar", ".tar", ".gz", ".jpg", ".gif", ".bmp", ".jpeg", ".DOC", ".PDF", ".XLS", ".RTF", ".TXT", ".ZIP", ".RAR", ".TAR", ".GZ", ".JPG", ".GIF", ".BMP", ".JPEG", ".mdb", ".ods", ".sql", '.png', '.PNG');
		global $extension;
		$extension = exten($userfile_name);
		$new_name = $new_name . $extension;
		$upfile = $path . $new_name;
		if (!in_array($extension, $file_type)) {
			$flag = 2;
			//return;
		} else if ($userfile_size <= 0) {
			$flag = 3;
			//return;
		} else if ($userfile_size > $maxsize) {
			$flag = 4; //1000000
			//return;
		} else if (!is_uploaded_file($userfile)) {
			$flag = 5;
			//return;
		} else if (!move_uploaded_file($userfile, $upfile)) {
			$flag = 6;
			//return;
		}
	}
	return $flag;
}
function upload_filemail($userfile, $new_name, $path, $maxsize)
{ ///* Ham nay da duoc sua mot so cau truc if ... else o phan duoi khi su dung cho file post resume
	//echo "file:".$userfile." name:".$userfile_name." size:".$userfile_size." type:".$userfile_type;
	//Chu y: 
	//1).'userfile' la ten cua textbox type=file
	//2).form dung pthuc POST va phai co enctype="multipart/form-data"
	$flag = 1;
	$userfile = $_FILES['userfile']['tmp_name'];
	$userfile_name = $_FILES['userfile']['name'];
	$userfile_size = $_FILES['userfile']['size'];
	$userfile_type = $_FILES['userfile']['type'];
	if ($_FILES['userfile'] != "") {
		$file_type = array(".php", ".js");
		global $extension;
		$extension = exten($userfile_name);
		$new_name = $new_name . $extension;
		$upfile = $path . $new_name;
		if (in_array($extension, $file_type)) {
			$flag = 2;
			//return;
		} else if ($userfile_size <= 0) {
			$flag = 3;
			//return;
		} else if ($userfile_size > $maxsize) {
			$flag = 4; //1000000
			//return;
		} else if (!is_uploaded_file($userfile)) {
			$flag = 5;
			//return;
		} else if (!move_uploaded_file($userfile, $upfile)) {
			$flag = 6;
			//return;
		}
	}
	return $flag;
}
////////////////////////
function upload_images($filehinh, $path, $ma)
{
	$file_type = array(".GIF", ".JPG", ".PNG", ".BMP", ".PSD", ".gif", ".jpg", ".png", ".bmp", ".psd");
	$error = 0;
	if (trim($filehinh['name']) != "") {
		if (!in_array($ext, $file_type)) {
			$error = 1;
		}
		if ($filehinh['size'] <= 0 or $filehinh['size'] >= 102400) {
			$error = 2;
		}
	} else $error = 3;
	if ($error == 0) {
		$file_img = $ma;
		move_uploaded_file($filehinh['tmp_name'], $path . $file_img);
	}
	return $error;
}
///////////////////////////////////////////////////////////////////////////////////////////////
function exten($name)
{
	$ext = strrchr($name, ".");
	return $ext;
}

function get_exten()
{
	global $extension;
	return $extension;
}
/////////////////////////////////////////
//Ham phan trang 
//$curPg:Trang hien hanh;
//$totalRows:Tong so dong
//;$maxRows:So dong lon nhat;$maxPages:So trang lon nhat;$curRow:Dong hien tai
/////////////////////////////////////////
function phantrang($vlang, $curPg, $totalRows, $maxRows, $maxPages, $curRow)
{
	$paging = "";

	////////////////////FOR QUERY DATABASE//////////////////////

	if ($totalRows % $maxRows == 0)

		$totalPages = (int)($totalRows / $maxRows);

	else

		$totalPages = (int)($totalRows / $maxRows + 1);

	$curPage = 1;

	if ($curPg == "")

		$curPage = 1;

	else

		$curPage = $curPg;

	$paging = GetLangPublic($vlang, 1) . " <font color=red>" . $curPage . "</font>" . " / " . GetLangPublic($vlang, 2) . " <FONT color=red>"  . $totalPages . "</FONT><br>";

	if ($totalRows >= $maxRows) {

		$start = 1;

		$end = 1;

		$paging1 = "";

		for ($i = 1; $i <= $totalPages; $i++) {
			if (($i > ((int)(($curPage - 1) / $maxPages)) * $maxPages) && ($i <= ((int)(($curPage - 1) / $maxPages + 1)) * $maxPages)) {

				if ($start == 1) $start = $i;

				if ($i == $curPage)

					$paging1 .=  $i . "  ";

				else
					$paging1 .= "<a href='javascript:GotoPage(" . $i . ")' style='text-decoration:none;'>" . "&nbsp;" . $i . "&nbsp;" . "</a>";
				$end = $i;
			}
		}

		$paging .= GetLangPublic($vlang, 5);

		if ($curPage > $maxPages)

			$paging .= "<a href='javascript:GotoPage(" . ($start - 1) . ")' style='text-decoration:none;'>" . GetLangPublic($vlang, 3) . "</a>";

		$paging .= $paging1;

		if (((int)(($curPage - 1) / $maxPages + 1) * $maxPages) < $totalPages)

			$paging .= "<a href='javascript:GotoPage(" . ($end + 1) . ")' style='text-decoration:none;'>" . GetLangPublic($vlang, 4) . "</a>";
	}

	return $paging;
}
function divepage($vlang, $curPg, $totalRows, $maxRows, $maxPages, $curRow, $frmName, $vobj, $vTabIndex)
{
	$paging = "";

	////////////////////FOR QUERY DATABASE//////////////////////

	if ($totalRows % $maxRows == 0)

		$totalPages = (int)($totalRows / $maxRows);

	else

		$totalPages = (int)($totalRows / $maxRows + 1);

	$curPage = 1;

	if ($curPg == "")

		$curPage = 1;

	else

		$curPage = $curPg;

	$paging = GetLangPublic($vlang, 1) . " <font color=red>" . $curPage . "</font>" . " / " . GetLangPublic($vlang, 2) . " <FONT color=red>"  . $totalPages . "</FONT><br>";

	if ($totalRows >= $maxRows) {

		$start = 1;

		$end = 1;

		$paging1 = "";

		for ($i = 1; $i <= $totalPages; $i++) {
			if (($i > ((int)(($curPage - 1) / $maxPages)) * $maxPages) && ($i <= ((int)(($curPage - 1) / $maxPages + 1)) * $maxPages)) {

				if ($start == 1) $start = $i;

				if ($i == $curPage)

					$paging1 .= "<font class='lvnumpageselect'> " . $i . " </font> &nbsp;";

				else
					$paging1 .= "<a tabindex=$vTabIndex href='javascript:GotoPageMulti($frmName,$vobj," . $i . ")' >" . "<font class='lvnumpagenone'> " . "&nbsp;" . $i	. "&nbsp;" . "</font>" . "</a>&nbsp;";
				$end = $i;
			}
		}

		$paging .= GetLangPublic($vlang, 5);

		if ($curPage > $maxPages)

			$paging .= "<a href='javascript:GotoPageMulti($frmName,$vobj," . ($start - 1) . ")'  tabindex=$vTabIndex>" . "<font class='lvnumpageprev'> " . GetLangPublic($vlang, 3) . "</font></a>&nbsp;";

		$paging .= $paging1;

		if (((int)(($curPage - 1) / $maxPages + 1) * $maxPages) < $totalPages)

			$paging .= "<a href='javascript:GotoPageMulti($frmName,$vobj," . ($end + 1) . ")'  tabindex=$vTabIndex>" . "<font class='lvnumpagenext'> " . GetLangPublic($vlang, 4) . "</font></a>&nbsp;";
	} else {
		$paging .= GetLangPublic($vlang, 5);
		$paging .= "<a tabindex=$vTabIndex href='javascript:GotoPageMulti($frmName,$vobj,1)' >" . "<font class='lvnumpagenone'> " . "&nbsp;1&nbsp;" . "</font>" . "</a>&nbsp;";
	}

	return $paging;
}
function getsaveget($vlang, $vopt, $vitem, $vlink, $vgroup, $vitemlst, $vchildlst, $vlevel3lst, $vchild3lst)
{
	$vreturn = "&lang=" . $vlang . "&opt=" . $vopt . "&item=" . $vitem . "&link=" . $vlink . "&group=" . $vgroup . "&itemlst=" . $vitemlst . "&childlst=" . $vchildlst . "&level3lst=" . $vlevel3lst . "&child3lst=" . $vchild3lst;
	return $vreturn;
}
//// componnent,user,right,rightcontrol////
function checkright($vComponent, $vlv002, $vright, $vrightcontrol)
{
	if ('admin' == $_SESSION['ERPSOFV2RRight']) {
		return 1;
	} else {
		switch ($vComponent) {
			case "HR":
				$vsql =	"select count(*) as count from lv_lv0008 B where B.lv002='$vlv002' and B.lv003='Ad0018' and B.lv004=1";
				$tresult = db_query($vsql);
				$trow = db_fetch_array($tresult);
				if ($trow['count'] > 0) return 1;
				break;
			case "WB":
				$vsql =	"select count(*) as count from lv_lv0008 B where B.lv002='$vlv002' and B.lv003='Ad0034' and B.lv004=1";
				$tresult = db_query($vsql);
				$trow = db_fetch_array($tresult);
				if ($trow['count'] > 0) return 1;
				break;
			case "LB":
				$vsql =	"select count(*) as count from lv_lv0008 B where B.lv002='$vlv002' and B.lv003='Ad0035' and B.lv004=1";
				$tresult = db_query($vsql);
				$trow = db_fetch_array($tresult);
				if ($trow['count'] > 0) return 1;
				break;

			default:
				break;
		}
		if ($vrightcontrol == "") {
			$vsql =	"select count(*) as count from lv_lv0008 B where B.lv002='$vlv002' and B.lv003='$vright' and B.lv004=1";
		} else {
			$vsql = "select count(*) as count from lv_lv0009 A Where A.lv002='$vrightcontrol' and A.lv004=1 and A.lv003 in (select B.lv001 from lv_lv0008 B where B.lv002='$vlv002' and B.lv003='$vright' and B.lv004=1) ";
		}
		$tresult = db_query($vsql);
		$trow = db_fetch_array($tresult);
		return (int)$trow['count'];
	}
}
function checkright_private($vComponent, $vlv002, $vright, $vrightcontrol)
{
	switch ($vComponent) {
		case "HR":
			$vsql =	"select count(*) as count from lv_lv0008 B where B.lv002='$vlv002' and B.lv003='Ad0018' and B.lv004=1";
			$tresult = db_query($vsql);
			$trow = db_fetch_array($tresult);
			if ($trow['count'] > 0) return 1;
			break;
		case "WB":
			$vsql =	"select count(*) as count from lv_lv0008 B where B.lv002='$vlv002' and B.lv003='Ad0034' and B.lv004=1";
			$tresult = db_query($vsql);
			$trow = db_fetch_array($tresult);
			if ($trow['count'] > 0) return 1;
			break;
		case "LB":
			$vsql =	"select count(*) as count from lv_lv0008 B where B.lv002='$vlv002' and B.lv003='Ad0035' and B.lv004=1";
			$tresult = db_query($vsql);
			$trow = db_fetch_array($tresult);
			if ($trow['count'] > 0) return 1;
			break;

		default:
			break;
	}
	if ($vrightcontrol == "") {
		$vsql =	"select count(*) as count from lv_lv0008 B where B.lv002='$vlv002' and B.lv003='$vright' and B.lv004=1";
	} else {
		$vsql = "select count(*) as count from lv_lv0009 A Where A.lv002='$vrightcontrol' and A.lv004=1 and A.lv003 in (select B.lv001 from lv_lv0008 B where B.lv002='$vlv002' and B.lv003='$vright' and B.lv004=1) ";
	}
	$tresult = db_query($vsql);
	$trow = db_fetch_array($tresult);
	return (int)$trow['count'];
}
function checkApproval($vEmployeeID, $vWeekwork, $vYear)
{
	$vsql = "select Approval  from tc_weekwork A Where A.WeekID=$vWeekwork and A.Year=$vYear and A.EmployeeID='$vEmployeeID' ";
	$tresult = db_query($vsql);
	$tnumrow = db_num_rows($tresult);
	if ($tnumrow > 0) {
		$trow = db_fetch_array($tresult);
		return (int)$trow['Approval'];
	} else
		return -1;
}
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function LDelete($strsql, $confirm)
{
	if ($confirm == true) {
		$result = mysql_query($strsql);
		if (!$result)
			return false;
		return true;
	} else
		exit;
}
function LCast2Show($strTime) //Input: "- 07:00:00 - 17:00:00 -"   Output "07:00 - 17:00"
{
	$TimeIN = substr($strTime, 3, 5);
	$TimeOUT = substr($strTime, 14, 5);
	echo (" " . $TimeIN . " - " . $TimeOUT . " ");
}
function LCastTime($strTime) //Input: "HH:MM:SS"   Output "HH:MM"
{
	echo (" " . substr($strTime, 0, 5) . " ");
}
///////////////////////////////////////////////////////////////////////////////////////
function LShowClock() //Hien thi dong ho tren trang
{
	echo ("<div class='relatedLinkss' style='height:8px' id='txt'><script language='javascript'>startTime();</script></div>");
}
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function Logtime($vlv002, $vLoginDate, $vLoginTime, $vState, $vIp, $vMac)
{
	$vsql = "insert into log (UserID,LoginDate,LoginTime,State,Ip,Mac) values('$vlv002','$vLoginDate','$vLoginTime',$vState,'$vIp','$vMac')";
	db_query($vsql);
}
function EmpLogtime($vlv002, $vLoginDate, $vLoginTime, $vState)
{
	$vsql = "	INSERT INTO logemp (EmployeeID, LoginDate, LoginTime, State) 
			VALUES('$vlv002', '$vLoginDate', '$vLoginTime', '$vState')";
	db_query($vsql);
}
/////////////////////////////////////////
//ham dieu khien cho trang thao tac
/////////////////////////////////////////
function empctrl($vopt, $vitemlst)
{
	$titemlst = (int)$vitemlst;
}
function empgetsaveget($vlang, $vopt, $vitemlst)
{
	$vreturn = "lang=" . $vlang . "&opt=" . $vopt . "&itemlst=" . $vitemlst;
	return $vreturn;
}
//Hm permission
function CheckPermission($vlv002, $vright)
{
	if ('admin' == $_SESSION['ERPSOFV2RRight']) {
		return 1;
	} else {
		$vsql = "select count(*) as count from lv_lv0008 A where A.lv002='$vlv002' and A.lv003='$vright' and A.lv004=1) ";
		$tresult = db_query($vsql);
		$trow = db_fetch_array($tresult);
		return (int)$trow['count'];
	}
}
function GetLangFile($vDir, $vStrFile, $vStrLanguage)
{
	$vArr = null;
	$vArr = array();
	$filename = $vDir . "languages/" . $vStrLanguage . "/" . $vStrFile;
	$handle = fopen($filename, "r");
	$i = 0;
	while ($vLine = stream_get_line($handle, 0, "\n")) {
		$vArr[$i] = substr($vLine, 0, strlen($vLine) - 1);
		$i++;
	}
	fclose($handle);
	return $vArr;
}
function GetLangPublic($vLangID, $vOrder)
{
	if (strtoupper($vLangID) == "VN") {
		$vArr = array(1 => "Trang hiện tại :", 2 => "Tổng trang :", 3 => "Trước", 4 => "Tiếp", 5 => "Trang :");
	} else
		$vArr = array(1 => "Current page :", 2 => "Total pages :", 3 => "Privous", 4 => "Next", 5 => "Pages :");
	return $vArr[$vOrder];
}
function GetLineFile($vDir, $vStrFile, $vStrPath)
{
	$vArr = null;
	$vArr = array();
	$filename = $vDir . $vStrPath . $vStrFile;
	$handle = fopen($filename, "r");
	$i = 0;
	while ($vLine = stream_get_line($handle, 0, "\n")) {
		$vArr[$i] = substr($vLine, 0, strlen($vLine) - 1);
		$i++;
	}
	fclose($handle);
	return $vArr;
}
