<?php
/////////////coding hr_lv0020///////////////
class   hr_lv0020 extends lv_controler
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
	public $lv026 = null;
	public $lv027 = null;
	public $lv028 = null;
	public $lv029 = null;
	public $lv030 = null;
	public $lv031 = null;
	public $lv032 = null;
	public $lv033 = null;
	public $lv034 = null;
	public $lv035 = null;
	public $lv036 = null;
	public $lv037 = null;
	public $lv038 = null;
	public $lv039 = null;
	public $lv040 = null;
	public $lv041 = null;
	public $lv042 = null;
	public $lv043 = null;
	public $lv044 = null;
	public $lv045 = null;
	public $lv046 = null;
	public $lv047 = null;

	///////////
	//public $DefaultFieldList="lv001,lv002,lv006,lv019,lv025,lv030,lv009,lv010,lv011,lv015,lv012,lv014,lv106,lv126,lv013,lv039,lv038,lv041,lv040,lv034,lv035,lv036,lv029,lv042,lv045,lv020,lv021,lv201,lv202,lv301,lv302,lv303,lv304,lv305,lv018,lv024,lv022,lv032,lv113,lv114,lv115,lv116";	
	public $DefaultFieldList = "lv001,lv002,lv006,lv266,lv019,lv025,lv030,lv009,lv010,lv011,lv015,lv012,lv014,lv106,lv126,lv013,lv039,lv038,lv041,lv040,lv034,lv035,lv036,lv029,lv042,lv045,lv020,lv021,lv201,lv202,lv208,lv209,lv210,lv211,lv212,lv301,lv302,lv303,lv304,lv305,lv018,lv024,lv022,lv032,lv113,lv114,lv115,lv116";
	////////////////////GetDate
	public $DateDefault = "1900-01-01";
	public $DateCurrent = "1900-01-01";
	public $Count = null;
	public $paging = null;
	public $lang = null;
	protected $objhelp = 'hr_lv0020';
	////////////
	var $ArrOther = array();
	var $ArrPush = array();
	var $ArrFunc = array();
	var $ArrGet = array("lv001" => "2", "lv002" => "3", "lv003" => "4", "lv004" => "5", "lv005" => "6", "lv006" => "7", "lv007" => "8", "lv008" => "9", "lv009" => "10", "lv010" => "11", "lv011" => "12", "lv012" => "13", "lv013" => "14", "lv014" => "15", "lv015" => "16", "lv016" => "17", "lv017" => "18", "lv018" => "19", "lv019" => "20", "lv020" => "21", "lv021" => "22", "lv022" => "23", "lv023" => "24", "lv024" => "25", "lv025" => "26", "lv026" => "27", "lv027" => "28", "lv028" => "29", "lv029" => "30", "lv030" => "31", "lv031" => "32", "lv032" => "33", "lv033" => "34", "lv034" => "35", "lv035" => "36", "lv036" => "37", "lv037" => "38", "lv038" => "39", "lv039" => "40", "lv040" => "41", "lv041" => "42", "lv042" => "43", "lv043" => "44", "lv044" => "45", "lv045" => "46", "lv046" => "47", "lv047" => "48", "lv048" => "49", "lv049" => "50", "lv050" => "51", "lv106" => "107", "lv116" => "117", "lv126" => "127", "lv201" => "202", "lv202" => "203", "lv208" => "209", "lv209" => "210", "lv210" => "211", "lv211" => "212", "lv212" => "213", "lv301" => "302", "lv302" => "303", "lv303" => "304", "lv304" => "305", "lv305" => "306", "lv113" => "114", "lv114" => "115", "lv115" => "116", "lv116" => "117", "lv266" => "267");
	var $ArrView = array("lv001" => "0", "lv002" => "0", "lv003" => "0", "lv004" => "0", "lv005" => "0", "lv006" => "0", "lv007" => "0", "lv008" => "0", "lv009" => "0", "lv010" => "0", "lv011" => "2", "lv012" => "0", "lv013" => "0", "lv014" => "0", "lv015" => "2", "lv016" => "0", "lv017" => "0", "lv018" => "0", "lv019" => "0", "lv020" => "0", "lv021" => "4", "lv022" => "0", "lv023" => "0", "lv024" => "0", "lv025" => "0", "lv026" => "0", "lv027" => "0", "lv028" => "0", "lv029" => "0", "lv030" => "22", "lv031" => "0", "lv032" => "0", "lv033" => "0", "lv034" => "0", "lv035" => "0", "lv036" => "0", "lv037" => "0", "lv038" => "0", "lv039" => "0", "lv040" => "0", "lv041" => "0", "lv042" => "0", "lv043" => "0", "lv044" => "22", "lv045" => "10", "lv046" => "10", "lv047" => "0", "lv048" => "10", "lv049" => "10", "lv050" => "10", "lv106" => "0", "lv116" => "0", "lv126" => "0", "lv201" => "0", "lv202" => "4", "lv208" => "0", "lv209" => "0", "lv210" => "0", "lv211" => "0", "lv212" => "0", "lv301" => "0", "lv302" => "0", "lv303" => "0", "lv304" => "0", "lv305" => "0", "lv113" => "0", "lv114" => "0", "lv115" => "0", "lv116" => "0", "lv266" => "0");

	public $LE_CODE = "NjlIUS02VFdULTZIS1QtNlFIQQ==";
	function __construct($vCheckAdmin, $vUserID, $vright)
	{
		$this->DateCurrent = GetServerDate() . " " . GetServerTime();
		$this->Set_User($vCheckAdmin, $vUserID, $vright);
		$this->isRel = 1;
		$this->isHelp = 1;
		$this->isConfig = 0;
		$this->isFil = 0;
		//$this->isDel=0;

		$this->lang = $_GET['lang'];
	}
	function LV_AutoDroppii()
	{
		$vNVList = "'20023799'";
		$lvsql1 = "select lv001 from droppii  ";
		$vReturn1 = db_query($lvsql1);
		while ($vrow1 = db_fetch_array($vReturn1)) {
			$this->ArrDroppii[$vrow1['lv001']] = true;
			$vNVList = $vNVList . ",'" . $vrow1['lv001'] . "'";
		}
		$vsql = "select lv001 from  hr_lv0020 where lv042 in ($vNVList)";
		$vresult = db_query($vsql);
		while ($vrow = db_fetch_array($vresult)) {
			if ($this->ArrDroppii[$vrow['lv001']] != true) {
				$vsql2 = "insert into droppii(lv001) values('" . $vrow['lv001'] . "')";
				$vresult2 = db_query($vsql2);
				if ($vresult2) $this->InsertLogOperation($this->DateCurrent, 'droppii.insert', sof_escape_string($vsql2));
			}
		}
	}
	function LV_Load()
	{
		$vsql = "select * from  hr_lv0020";
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
			$this->lv026 = $vrow['lv026'];
			$this->lv027 = $vrow['lv027'];
			$this->lv028 = $vrow['lv028'];
			$this->lv029 = $vrow['lv029'];
			$this->lv030 = $vrow['lv030'];
			$this->lv031 = $vrow['lv031'];
			$this->lv032 = $vrow['lv032'];
			$this->lv033 = $vrow['lv033'];
			$this->lv034 = $vrow['lv034'];
			$this->lv035 = $vrow['lv035'];
			$this->lv036 = $vrow['lv036'];
			$this->lv037 = $vrow['lv037'];
			$this->lv038 = $vrow['lv038'];
			$this->lv039 = $vrow['lv039'];
			$this->lv040 = $vrow['lv040'];
			$this->lv041 = $vrow['lv041'];
			$this->lv042 = $vrow['lv042'];
			$this->lv043 = $vrow['lv043'];
			$this->lv044 = $vrow['lv044'];
			$this->lv045 = $vrow['lv045'];
			$this->lv046 = $vrow['lv046'];
			$this->lv047 = $vrow['lv047'];
			$this->lv106 = $vrow['lv106'];

			$this->lv113 = $vrow['lv113'];
			$this->lv114 = $vrow['lv114'];
			$this->lv115 = $vrow['lv115'];
			$this->lv116 = $vrow['lv116'];
			$this->lv266 = $vrow['lv266'];

			$this->lv126 = $vrow['lv126'];
			$this->lv200 = $vrow['lv200'];
			$this->lv201 = $vrow['lv201'];
			$this->lv202 = $vrow['lv202'];

			$this->lv301 = $vrow['lv301'];
			$this->lv302 = $vrow['lv302'];
			$this->lv303 = $vrow['lv303'];
			$this->lv304 = $vrow['lv304'];
			$this->lv305 = $vrow['lv305'];
			$this->lv309 = $vrow['lv309'];
		}
	}
	function LV_SendMailAll($lvarr)
	{

		$sql = "select A.*,md5('$str') passcode,B.lv040 Email1,B.lv041 Email2,B.lv002 TenTV,B.lv001 MaTV from lv_lv0007 A left join hr_lv0020 B on A.lv006=B.lv001 where B.lv001 in ($lvarr)";
		$vReturn = db_query($sql);
		while ($vrow = db_fetch_array($vReturn)) {
			$str = "";
			$lvcontent = $this->moml_lv0013->lv003;
			$lvtitle = unicode_to_case($this->moml_lv0013->lv002);
			$lvemail = "no-reply@pw.biznet.com.vn";
			$vTo = '';
			if ($vTo == '') {
				$vTo = trim($vrow['Email1']);
			}
			if ($vTo == '') {
				$vTo = trim($vrow['Email2']);
			}
			if ($vTo == '') {
				$vTo = trim($vrow['lv009']);
			}
			$lvuser = $_SESSION['ERPSOFV2RUserID'];
			//echo "$lvcontent,$lvtitle,$lvuser,$lvemail,$vTo<br/>";
			$lvcontent = str_replace("{1}", $vrow['MaTV'], $lvcontent);
			$lvcontent = str_replace("{2}", $vrow['TenTV'], $lvcontent);
			$this->LV_SendMail($lvcontent, $lvtitle, $lvuser, $lvemail, $vTo);
		}
		return $vReturn;
	}
	function LV_SendMail($lvcontent, $lvtitle, $lvuser, $lvemail, $vTo)
	{
		$lvListId_del = "";
		$lvml_lv0008 = new ml_lv0008('admin', 'admin', 'Ml0008');
		$lvml_lv0100 = new ml_lv0100('admin', 'admin', 'Ml0100');
		$lvml_lv0009 = new ml_lv0009('admin', 'admin', 'Ml0009');
		$lvml_lv0009->LV_LoadSMTP();
		$lvuser = 'admin';
		$lvml_lv0008->LV_LoadUser($lvuser, $lvemail);
		$this->Domain = $lvml_lv0009->lv010;
		$vstrTo = SplitTo(str_replace(";", ",", str_replace(" ", "", $vTo)), "<", ">", ",");
		$vstrToSend = $this->explodeToEsc($vstrTo, ",", 0);
		$lvml_lv0100 = new ml_lv0100('admin', 'admin', 'Ml0100');
		$lvml_lv0100->To(explode(",", $vstrToSend));
		if ($lvml_lv0008->lv005 == 1) {
			$lvml_lv0100->lvml_lv0009 = $lvml_lv0009;
			$lvml_lv0100->lvml_lv0008 = $lvml_lv0008;
			$lvml_lv0100->To(explode(",", $vstrToSend));
			$lvml_lv0100->From($lvemail);
			$lvml_lv0100->Subject($lvtitle);
			$lvml_lv0100->Priority(3);
			$lvml_lv0100->Content_type("multipart/related");
			$lvml_lv0100->charset = "utf-8";
			$lvml_lv0100->ctencoding = "quoted-printable";
			$lvml_lv0100->Cc(explode(",", $vstrCCSend));
			$lvml_lv0100->Bcc(explode(",", $vstrBCCSend));
			$lvml_lv0100->Body($lvcontent, '');
			$lvml_lv0100->Content_type('text/html');
			if ($lvml_lv0100->Send()) {
				echo 'Thành công gửi! Email:' . $vTo . "<br/>";
			}
			//else	
			//	echo 'Không thành công gửi! Email:'.$vTo."<br/>";

		}
		//else	
		//echo 'Không thành công gửi! Email:'.$vTo."<br/>";

		return $vReturn;
	}
	public function LV_LinkField($vFile, $vSelectID)
	{
		switch ($vFile) {
			case 'lv009':
				return ($this->CreateSelect($this->sqlcondition($vFile, $vSelectID), 0));
				break;
			default:
				return ($this->CreateSelect($this->sqlcondition($vFile, $vSelectID), 2));
				break;
		}
	}
	private function sqlcondition($vFile, $vSelectID)
	{
		$vsql = "";
		switch ($vFile) {
			case 'lv008':
				$vsql = "select lv001,lv002,IF(lv001='$vSelectID',1,0) lv003 from  lv_lv0004";
				break;
			case 'lv009':
				$vsql = "select lv001,lv002,IF(lv001='$vSelectID',1,0) lv003 from  hr_lv0022";
				break;
			case 'lv017':
				$vsql = "select lv001,lv002,IF(lv001='$vSelectID',1,0) lv003 from  hr_lv0021";
				break;
			case 'lv019':
				$vsql = "select lv001,lv002,IF(lv001='$vSelectID',1,0) lv003 from  hr_lv0151 order by lv017 asc";
				break;
			case 'lv022':
				$vsql = "select lv001,lv002,IF(lv001='$vSelectID',1,0) lv003 from  hr_lv0014";
				break;
			case 'lv023':
				$vsql = "select lv001,lv002,IF(lv001='$vSelectID',1,0) lv003 from  hr_lv0016";
				break;
			case 'lv024':
				$vsql = "select lv001,lv002,IF(lv001='$vSelectID',1,0) lv003 from  hr_lv0017";
				break;
			case 'lv025':
				$vsql = "select lv001,lv002,IF(lv001='$vSelectID',1,0) lv003 from  hr_lv0151 order by lv017 asc";
				break;
			case 'lv026':
				$vsql = "select lv001,lv002,IF(lv001='$vSelectID',1,0) lv003 from  hr_lv0007";
				break;
			case 'lv027':
				$vsql = "select lv001,lv002,IF(lv001='$vSelectID',1,0) lv003 from  hr_lv0004";
				break;
			case 'lv028':
				$vsql = "select lv001,lv002,IF(lv001='$vSelectID',1,0) lv003 from  hr_lv0005";
				break;
			case 'lv029':
				$vsql = "select lv001,CONCAT(lv003,'[',lv002,']') lv002,IF(lv001='$vSelectID',1,0) lv003 from  hr_lv0002";
				break;
			case 'lv031':
				$vsql = "select lv001,lv002,IF(lv001='$vSelectID',1,0) lv003 from  hr_lv0014";
				break;
			case 'lv032':
				$vsql = "select lv001,CONCAT(lv002,'[',lv003,']') lv002,IF(lv001='$vSelectID',1,0) lv003 from  hr_lv0023	";

				break;
		}
		return $vsql;
	}
	public  function getvaluelink($vFile, $vSelectID)
	{
		switch ($vFile) {
			case 'lv008':
				$vsql = "select lv001,lv002,IF(lv001='$vSelectID',1,0) lv003 from  lv_lv0004 where lv001='$vSelectID'";
				break;
			case 'lv009':
				$vsql = "select lv001,lv002,IF(lv001='$vSelectID',1,0) lv003 from  hr_lv0022 where lv001='$vSelectID'";
				break;
			case 'lv017':
				$vsql = "select lv001,lv002,IF(lv001='$vSelectID',1,0) lv003 from  hr_lv0021 where lv001='$vSelectID'";
				break;
			case 'lv018':
				$vsql = "
				select * from (
				select '0' lv001,'Nữ' lv002,IF('0'='$vSelectID',1,0) lv003
				union 
				select '1' lv001,'Nam' lv002,IF('1'='$vSelectID',1,0) lv003 
				) MP
				where lv001='$vSelectID'";
				break;
			/*case 'lv019':
				$vsql="select lv001,lv002,IF(lv001='$vSelectID',1,0) lv003 from  hr_lv0151 where lv001='$vSelectID'";
				break;*/
			case 'lv022':
				$vsql = "select lv001,lv002,IF(lv001='$vSelectID',1,0) lv003 from  hr_lv0014 where lv001='$vSelectID'";
				break;
			case 'lv023':
				$vsql = "select lv001,lv002,IF(lv001='$vSelectID',1,0) lv003 from  hr_lv0016 where lv001='$vSelectID'";
				break;
			case 'lv024':
				$vsql = "select lv001,lv002,IF(lv001='$vSelectID',1,0) lv003 from  hr_lv0017 where lv001='$vSelectID'";
				break;
			/*case 'lv025':
				$vsql="select lv001,lv002,IF(lv001='$vSelectID',1,0) lv003 from  hr_lv0151 where lv001='$vSelectID'";
				break;*/
			case 'lv026':
				$vsql = "select lv001,lv002,IF(lv001='$vSelectID',1,0) lv003 from  hr_lv0007 where lv001='$vSelectID'";
				break;
			case 'lv027':
				$vsql = "select lv001,lv002,IF(lv001='$vSelectID',1,0) lv003 from  hr_lv0004 where lv001='$vSelectID'";
				break;
			case 'lv028':
				$vsql = "select lv001,lv002,IF(lv001='$vSelectID',1,0) lv003 from  hr_lv0005 where lv001='$vSelectID'";
				break;
			case 'lv029':
				$vsql = "select lv001,lv003 lv002,IF(lv001='$vSelectID',1,0) lv003 from  hr_lv0002 where lv001='$vSelectID'";
				break;
			case 'lv031':
				$vsql = "select lv001,lv002,IF(lv001='$vSelectID',1,0) lv003 from  hr_lv0014  where lv001='$vSelectID'";
				break;
			case 'lv032':
				$vsql = "select lv001,lv002,IF(lv001='$vSelectID',1,0) lv003 from hr_lv0023 	 where lv001='$vSelectID'";
				break;
			case 'lv042':
				$lvopt = 2;
				$vsql = "select lv001,lv002,IF(lv001='$vSelectID',1,0) lv003 from  hr_lv0020 where lv001='$vSelectID'";
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

	function getNhanVien()
	{
		$vArrRe = [];
		$vsql = "SELECT * from  hr_lv0020";
		$vresult = db_query($vsql);
		while ($vrow = mysqli_fetch_assoc($vresult)) {
			$vArrRe[] = $vrow;
		}
		return $vArrRe;
	}

	function layNhanVienTheoMa($maNhanVien)
	{
		$vsql = "SELECT * FROM hr_lv0020 WHERE lv001 = '" . $maNhanVien . "'";
		return db_query($vsql);
	}
	function MB_LayNhanVien()
	{
		$vsql = "select * from hr_lv0020";
		return db_query($vsql);
	}
}