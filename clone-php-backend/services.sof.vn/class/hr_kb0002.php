<?php
/////////////coding hr_kb0002///////////////
class   hr_kb0002 extends lv_controler
{
	public $lv001 = null;
	public $lv002 = null; // Mã nhân viên
	public $lv003 = null; // Ngày đề xuất
	public $lv004 = null; // Chấm công vào hay ra (1: vào, 2: ra)
	public $lv005 = null; // Vị trí chấm công (Tọa độ)jo
	public $lv006 = null; // Trạng thái duyệt (0: chưa duyệt, 1: đã duyệt)
	public $lv007 = null; // Hinh anh minh chung
	public $lv008 = null; // Mô tả đơn xin
	public $lv009 = null; // Phản hồi cấp 1
	public $lv010 = null; // Mã phòng ban
	public $lv011 = null; // Người tạo
	public $lv012 = null; // Người duyệt cấp 1
	public $lv013 = null; // Người duyệt cấp 2
	public $lv014 = null; // Người duyệt cấp 2
	public $lv015 = null; // Người xin phép
	public $lv016 = null; // Ngày bắt đầu nghỉ
	public $lv017 = null; // Đến ngày
	public $lv018 = null; // Ngày duyệt cấp 2
	public $lv019 = null; // Ngày chấp nhận cấp 3
	public $lv020 = null; // Người duyệt cấp 3
	public $lv021 = null; // Trạng thái level 3
	public $lv022 = null; // Loại đơn
	public $lv023 = null; // Ngày tạo
	public $lv024 = null; // Token 1
	public $lv025 = null; // Ngày tạo token 1
	public $lv026 = null; // Token 2
	public $lv027 = null; // Ngày tạo token 2
	public $lv028 = null; // Token 3
	public $lv029 = null; // Ngày tạo token 3
	public $lv030 = null; // Phản hồi cấp 3
	public $lv031 = null; // Email cấp 1
	public $lv032 = null; // Email cấp 2
	public $lv033 = null; // Email cấp 3
	public $lv034 = null; // Nội dung duyệt c1
	public $lv035 = null; // Nội dung duyệt c2
	public $lv036 = null; // Nội dung duyệt c3
	public $lv037 = null; // Nội dung đầu email
	public $lv038 = null; // Tieeuu đề Email
	public $lv039 = null; // Bất khả kháng
	public $lv040 = null; // Cả ngày
	public $lv041 = null; // Ngày hiển thị phép
	public $lv042 = null; // Đến ngày
	public $lv043 = null; // Trạng thái
	public $lv044 = null; // Tại thời điểm
	public $lv089 = null;
	public $lv099 = null;
	public $lv100 = null;
	public $lv045 = null; // Ngày ko duyệt c1
	public $lv046 = null; // Trạng thái duyệt c2
	public $lv047 = null; // Ngày duyệt c1
	public $current_row_lv003 = null;
	public $ArrJobs = null;
	///////////
	public $DefaultFieldList = "lv001,lv002,lv010,lv003,lv004,lv005,lv007,lv047,lv013,lv006,lv045,lv018,lv014,lv046,lv019,lv020,lv021";
	////////////////////GetDate
	public $DateDefault = "1900-01-01";
	public $DateCurrent = "1900-01-01";
	public $Count = null;
	public $paging = null;
	public $lang = null;
	protected $objhelp = 'hr_kb0002';
	////////////
	var $ArrOther = array();
	var $ArrPush = array();
	var $ArrFunc = array();
	var $ArrGet = array(
		"lv001" => "1",  // Mã đề xuất
		"lv002" => "2",  // Mã nhân viên (Người đề xuất)
		"lv010" => "3",  // Mã phòng ban
		"lv003" => "4",  // Ngày đề xuất
		"lv004" => "5",  // Loại đề xuất (công vào công ra)
		"lv005" => "6",  // Tọa độ
		"lv007" => "7",  // Hình ảnh minh chứng
		"lv047" => "8",  // Ngày duyệt cấp 1
		"lv013" => "9",  // Người duyệt cấp 1
		"lv006" => "10", // Trạng thái duyệt cấp 1
		"lv045" => "11", // Ngày không duyệt cấp 1
		"lv018" => "12", // Ngày duyệt cấp 2
		"lv014" => "13", // Người duyệt cấp 2
		"lv046" => "14", // Trạng thái duyệt cấp 2
		"lv019" => "15", // Ngày duyệt cấp 3
		"lv020" => "16", // Người duyệt cấp 3 
		"lv021" => "17"  // Trạng thái duyệt cấp 3 
	);

	var $ArrView = array(
		"lv001" => "0",  // Mã đề xuất
		"lv002" => "0",  // Mã nhân viên (Người đề xuất)
		"lv010" => "0",  // Mã phòng ban
		"lv003" => "4",  // Ngày đề xuất
		"lv004" => "0",  // Loại đề xuất (công vào công 
		"lv005" => "0",  // Tọa độ
		"lv007" => "0",  // Hình ảnh minh chứng
		"lv047" => "4",  // Ngày duyệt cấp 1
		"lv013" => "0",  // Người duyệt cấp 1
		"lv006" => "0",  // Trạng thái duyệt cấp 1
		"lv045" => "4",  // Ngày không duyệt cấp 1
		"lv018" => "4",  // Ngày duyệt cấp 2
		"lv014" => "0",  // Người duyệt cấp 2
		"lv046" => "0",  // Trạng thái duyệt cấp 2
		"lv019" => "4",  // Ngày duyệt cấp 3
		"lv020" => "0",  // Người duyệt cấp 3 
		"lv021" => "0"   // Trạng thái duyệt cấp 3 
	);
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
		$this->ArrJobs = array();
	}
	function KB_Load()
	{
		$vsql = "SELECT * FROM hr_kb0002";
		$vresult = db_query($vsql);
		return $vresult;
	}

	// Load dữ liệu theo mã nhân viên
	function KB_LoadID($vlv002)
	{
		$vsql = "SELECT * FROM hr_kb0002 WHERE lv002 = '$vlv002'";
		$vresult = db_query($vsql);
		return $vresult;
	}
	function KB_LoadID_1($vlv001)
	{
		echo $vsql = "SELECT * FROM hr_kb0002 WHERE lv001 = '$vlv001'";
		$vresult = db_query($vsql);
		$row = mysqli_fetch_assoc($vresult);

		if ($row) {
			foreach ($row as $key => $value) {
				$this->$key = $value; // Gán tất cả trường của dòng vào thuộc tính objec
			}

			$this->ArrGet = $row;
		}
		return $row;
	}

	// Thêm bản ghi mới
	function KB_Insert($maNhanVien, $viTri, $trangThai, $hinhAnh)
	{
		// Lấy loại chấm công của bản ghi cuối cùng
		$lastRecordSQL = "SELECT lv004 FROM hr_kb0002 
                        WHERE lv002 = '" . addslashes($maNhanVien) . "' 
                        ORDER BY lv003 DESC LIMIT 1";
		$result = db_query($lastRecordSQL);

		// Mặc định là loại 1 nếu không có bản ghi nào
		$loaiChamCong = 1;

		if (db_num_rows($result) > 0) {
			$row = db_fetch_array($result);
			$lastType = intval($row['lv004']);
			$loaiChamCong = ($lastType == 1) ? 2 : 1;
		}

		// Thực hiện insert
		echo $insertSQL = "INSERT INTO hr_kb0002 (lv002, lv003, lv004, lv005, lv006, lv007,lv021,lv046) 
                    VALUES ('" . addslashes($maNhanVien) . "', 
                            NOW(), 
                            " . $loaiChamCong . ", 
                            '" . addslashes($viTri) . "', 
                            " . $trangThai . ", 
                            '" . addslashes($hinhAnh) . "')";
		$resultInsert = db_query($insertSQL);

		return [
			'success' => $resultInsert,
			'loaiChamCong' => $loaiChamCong
		];
	}

	// Cập nhật dữ liệu
	function KB_Update()
	{
		// Sử dụng $this->lv003 nếu có, ngược lại sử dụng NOW()
		$date = !empty($this->lv003) ? "'$this->lv003'" : "NOW()";

		$vsql = "UPDATE hr_kb0002 SET 
                    lv003 = $date, 
                    lv004 = '$this->lv004', 
                    lv005 = '$this->lv005', 
                    lv006 = '$this->lv006',
                    lv006 = '$this->lv007',
                WHERE lv002 = '$this->lv002'";
		$result = db_query($vsql);
		return $result;
	}

	function KB_Delete($lvarr)
	{
		//if($this->isDel==0) return false;
		$lvsql = "DELETE FROM hr_kb0002  WHERE hr_kb0002.lv001 IN ($lvarr) and lv021=0 and lv006=0";
		$vReturn = db_query($lvsql);
		if ($vReturn) $this->InsertLogOperation($this->DateCurrent, 'hr_kb0002.delete', sof_escape_string($lvsql));
		return $vReturn;
	}
	function LV_InsertAuto($vViTri, $vHinh)
	{
		// if ($this->isAdd == 0) return false;
		$vField = 'lv008';
		$lvsql = "insert into erp_sof_documents_v4_0.hr_kb0002 (lv002,lv003,lv004,lv005,lv006,lv007,$vField) values('" . sof_escape_string($this->lv002) . "','" . sof_escape_string($this->lv003) . "','" . sof_escape_string($this->lv004) . "','" . sof_escape_string($this->lv005) . "','" . sof_escape_string($this->lv006) . "','" . sof_escape_string($this->lv007) . "','" . sof_escape_string($vHinh) . "')";
		$vReturn = db_query($lvsql);
		if ($vReturn) {
			$this->lv001 = sof_insert_id();
			$this->InsertLogOperation($this->DateCurrent, 'hr_kb0002.insert', sof_escape_string($lvsql));
		}
		return $vReturn;
	}
	function LV_UpdateAuto($vKetQua, $vViTri, $vHinh)
	{
		// if ($this->isEdit == 0) return false;
		$vField = 'lv008';
		$lvsql = "Update erp_sof_documents_v4_0.hr_kb0002 set $vField='" . sof_escape_string($vHinh) . "' where lv001='$vKetQua'";
		$vReturn = db_query($lvsql);
		if ($vReturn) $this->InsertLogOperation($this->DateCurrent, 'hr_kb0002.update', sof_escape_string($lvsql));
		return $vReturn;
	}
	function LV_Load()
	{
		$vsql = "select * from  hr_kb0002";
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
		}
	}
	function LV_LoadID($vlv001)
	{
		$vsql = "select * from  hr_kb0002 = '$vlv001'";
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
		}
	}
	function LV_LoadStepCheck($vlv007)
	{
		$strReturn = "";
		$strTotal = 0;
		$lvsql = "select lv001 from  erp_sof_documents_v4_0.hr_kb0002 Where lv002='$vlv007' ";
		$vresult = db_query($lvsql);
		$vrow = db_fetch_array($vresult);
		if ($vrow) {
			return $vrow['lv001'];
		}
		return null;
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

		if ($this->ListEmp != '') $strCondi .= " and lv001 in ($this->ListEmp)";
		if ($this->lv001 != "") $strCondi .= " and lv001 like '%$this->lv001%'";
		if ($this->lv002 != "") $strCondi .= " and lv002 like '%$this->lv002%'";
		if ($this->lv003 != "") $strCondi .= " and lv003 like '%$this->lv003%'";
		if ($this->lv004 != "") $strCondi .= " and lv004 like '%$this->lv004%'";
		if ($this->lv005 != "") $strCondi .= " and lv005 like '%$this->lv005%'";
		if ($this->lv006 != "") $strCondi .= " and lv006 like '%$this->lv006%'";
		if ($this->lv007 != "") $strCondi .= " and lv007 like '%$this->lv007%'";
		if ($this->lv008 != "") $strCondi .= " and lv008 like '%$this->lv008%'";
		if ($this->lv009 != "") $strCondi .= " and lv009 like '%$this->lv009%'";
		if ($this->lv010 != "") $strCondi .= " and lv010 like '%$this->lv010%'";
		if ($this->lv011 != "") $strCondi .= " and lv011 like '%$this->lv011%'";

		if ($this->lv012 != "") {
			if ($this->lv015 != "")
				$strCondi .= " and (lv012='$this->lv012' or lv015 like '%$this->lv015%')";
			else
				$strCondi .= " and lv012='$this->lv012'";
		}

		if ($this->lv013 != "") $strCondi .= " and lv013 like '%$this->lv013%'";
		if ($this->lv014 != "") $strCondi .= " and lv014 like '%$this->lv014%'";
		if ($this->lv015 != "") $strCondi .= " and lv015 = '$this->lv015'";
		if ($this->lv016 != "") $strCondi .= " and lv016 like '%$this->lv016%'";
		if ($this->lv017 != "") $strCondi .= " and lv017 like '%$this->lv017%'";
		if ($this->lv018 != "") $strCondi .= " and lv018 like '%$this->lv018%'";
		if ($this->lv019 != "") $strCondi .= " and lv019 like '%$this->lv019%'";
		if ($this->lv020 != "") $strCondi .= " and lv020 like '%$this->lv020%'";
		if ($this->lv021 != "") $strCondi .= " and lv021 like '%$this->lv021%'";
		if ($this->lv022 != "") $strCondi .= " and lv022 like '%$this->lv022%'";
		if ($this->lv023 != "") $strCondi .= " and lv023 like '%$this->lv023%'";

		//them
		if ($this->lv045 != "") $strCondi .= " and lv045 like '%$this->lv045%'";
		if ($this->lv046 != "") $strCondi .= " and lv046 like '%$this->lv046%'";
		if ($this->lv047 != "") $strCondi .= " and lv047 like '%$this->lv047%'";

		return $strCondi;
	}

	////////////////Count///////////////////////////
	function GetCount()
	{
		$sqlC = "SELECT COUNT(*) AS nums FROM hr_kb0002 WHERE 1=1 " . $this->GetCondition();
		$bResultC = db_query($sqlC);
		$arrRowC = db_fetch_array($bResultC);
		return $arrRowC['nums'];
	}
	function LV_ReportPhep()
	{
		$vTable = '
		<table cellpadding="10" cellspacing="0" style="page-break-before: always" width="977">
			<colgroup>
				<col width="27" />
				<col width="232" />
				<col width="28" />
				<col width="33" />
				<col width="84" />
				<col width="141" />
				<col width="71" />
				<col width="198" /></colgroup>
			<tbody>
				<tr>
					<td colspan="3" height="5" style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: none; padding-top: 0in; padding-bottom: 0in; padding-left: 0.08in; padding-right: 0in" width="327">
						<p align="center" class="western">&nbsp;</p>
					</td>
					<td colspan="3" style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: none; padding-top: 0in; padding-bottom: 0in; padding-left: 0.08in; padding-right: 0in" width="298">
						<p align="center" class="western" style="margin-bottom: 0in"><font face="Arial, sans-serif"><font style="font-size: 15pt">PHIẾU B&Aacute;O TĂNG CA</font></font></p>
						<p align="center" class="western"><font face="Arial, sans-serif">Ng&agrave;y: &hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;</font></p>
					</td>
					<td colspan="2" style="border: 1px solid #000000; padding: 0in 0.08in" width="290">
						<p class="western" style="margin-bottom: 0in"><font face="Arial, sans-serif"><font style="font-size: 11pt">M&atilde;: BM-HCNS-15</font></font></p>
						<p class="western" style="margin-bottom: 0in"><font face="Arial, sans-serif"><font style="font-size: 11pt">Lần sửa đổi: 01</font></font></p>
						<p class="western"><font face="Arial, sans-serif"><font style="font-size: 11pt">Ng&agrave;y ban h&agrave;nh: 15/05/2012</font></font></p>
					</td>
				</tr>
				<tr>
					<td height="18" style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: none; padding-top: 0in; padding-bottom: 0in; padding-left: 0.08in; padding-right: 0in" width="27">
						<p align="center" class="western"><font face="Arial, sans-serif"><b>Stt</b></font></p>
					</td>
					<td style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: none; padding-top: 0in; padding-bottom: 0in; padding-left: 0.08in; padding-right: 0in" width="232">
						<p align="center" class="western"><font face="Arial, sans-serif"><b>Họ t&ecirc;n</b></font></p>
					</td>
					<td colspan="2" style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: none; padding-top: 0in; padding-bottom: 0in; padding-left: 0.08in; padding-right: 0in" width="81">
						<p align="center" class="western"><font face="Arial, sans-serif"><b>Từ giờ</b></font></p>
					</td>
					<td style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: none; padding-top: 0in; padding-bottom: 0in; padding-left: 0.08in; padding-right: 0in" width="84">
						<p align="center" class="western"><font face="Arial, sans-serif"><b>Đến giờ</b></font></p>
					</td>
					<td colspan="2" style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: none; padding-top: 0in; padding-bottom: 0in; padding-left: 0.08in; padding-right: 0in" width="233">
						<p align="center" class="western"><font face="Arial, sans-serif"><b>L&yacute; do tăng ca</b></font></p>
					</td>
					<td style="border: 1px solid #000000; padding: 0in 0.08in" width="198">
						<p align="center" class="western"><font face="Arial, sans-serif"><b>Kết quả c&ocirc;ng việc</b></font></p>
					</td>
				</tr>
				
				<tr>
					<td colspan="8" height="72" style="border: 1px solid #000000; padding: 0in 0.08in" valign="top" width="955">
						<p class="western" style="margin-top: 0.08in; margin-bottom: 0in"><font face="Arial, sans-serif"><span lang="en-US">P.NCNS Trưởng Bộ phận Trưởng đơn vị</span></font></p>
						<p align="center" class="western" lang="en-US" style="margin-top: 0.08in; margin-bottom: 0in">&nbsp;</p>
						<p align="center" class="western" lang="en-US" style="margin-top: 0.08in">&nbsp;</p>
					</td>
				</tr>
			</tbody>
		</table>
';
		$vTR = '<tr height="17">
			<td height="17" >' . (($sExport == "excel") ? '<Data ss:Type="String">="@#01"' : '@#01') . '</td>
			<td style="white-space:nowrap">@#02</td>
			<td style="white-space:nowrap">@#03</td>
			<td align=center>@#04</td>
			<td align=center>@#05</td>
			<td align=center>@#06</td>
		</tr>
			
		';
		$sqlS = "SELECT *,lv015 lv829,lv015 lv099,IF(SUBSTR(lv003,1,3)='1/2',DATEDIFF(lv017,lv016)+0.5,IF(TIMEDIFF(lv017,lv016)<'24:00:00' AND DATEDIFF(lv017,lv016)<1,1,DATEDIFF(lv017,lv016)+1)) lv098 FROM hr_kb0002 WHERE 1=1  " . $this->GetCondition() . "";
		$vorder = $curRow;
		$bResult = db_query($sqlS);
		$strDepart = '';

		$vlv079_0 = 0;
		$vlv024_0 = 0;
		$vlv019_0 = 0;
		$vlv028_0 = 0;
		$vlv015_0 = 0;
		$vlv020_0 = 0;
		$vlv050_0 = 0;
		$vlv025_0 = 0;
		$vlv085_0 = 0;
		$vlv043_0 = 0;
		$vlv039_0 = 0;
		$vlv045_0 = 0;
		$vlv035_0 = 0;
		$vlv048_0 = 0;
		$vlv084_0 = 0;
		$vlv080_0 = 0;
		$strTrH = '';
		$vOrder = 0;
		while ($vrow = db_fetch_array($bResult)) {

			$vLineOne = $vTR;
			$vLineOne = str_replace("@#01", $vOrder, $vLineOne);
			$vLineOne = str_replace("@#02", $this->getvaluelink('lv007', $this->FormatView($vrow['lv002'], (int)$this->ArrView['lv002'])), $vLineOne);
			$vLineOne = str_replace("@#03", $this->FormatView($vrow['lv025'], 20), $vLineOne);
			$vLineOne = str_replace("@#04", $this->FormatView($vrow['lv079'], 20), $vLineOne);
			$vLineOne = str_replace("@#05", $this->FormatView($vrow['lv024'], 20), $vLineOne);
			$vLineOne = str_replace("@#06", $this->FormatView($vrow['lv019'], 20), $vLineOne);
			$vLineOne = str_replace("@#07", $this->FormatView($vrow['lv028'], 20), $vLineOne);
			$vLineOne = str_replace("@#08", $this->FormatView($vrow['lv015'], 20), $vLineOne);
			$vLineOne = str_replace("@#09", $this->FormatView($vrow['lv020'], 20), $vLineOne);
			$vLineOne = str_replace("@#10", $this->FormatView($vrow['lv050'], 20), $vLineOne);
			$vLineOne = str_replace("@#15", $this->FormatView($vrow['lv051'], 20), $vLineOne);
			$vLineOne = str_replace("@!02", $this->getvaluelink('lv058', $strDepart), $vLineOne);
			$strTrH = $strTrH . $vLineOne;
		}
		$strTrH = $strTrH . $vLineOne;
		$strTable = str_replace("@#02", '', $lvTable);
		$strTable = str_replace("@#03", $this->getvaluelink('lv058', $strDepart), $strTable);
		$strFullTbl = $strFullTbl . str_replace("@#01", $strTrH, $strTable);
		return $strFullTbl;
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
		$lvHref = "<span onclick=\"ProcessTextHiden(this)\"><a href=\"javascript:FunctRunning1('@01')\" style=\"text-decoration:none\" class=@#04>@02</a></span>";
		$lvTdH = "<td width=\"@01\" class=\"lvhtable\">@02</td>";

		$lvTd = "<td  class=\"@#04\" align=\"@#05\">@02</td>";
		echo $sqlS = "select kb.*,
         DATEDIFF(kb.lv017,kb.lv016) SoNgayXinPhep,
         kb.lv015 lv829,
         kb.lv015 lv099,
         nv.lv029 as ma_phong_ban,
         pb.lv003 as ten_phong_ban,
         IF(SUBSTR(kb.lv003,1,3)='1/2',DATEDIFF(kb.lv017,kb.lv016)+0.5,IF(TIMEDIFF(kb.lv017,kb.lv016)<'24:00:00' AND DATEDIFF(kb.lv017,kb.lv016)<1,1,DATEDIFF(kb.lv017,kb.lv016)+1)) lv098,
         ADDTIME(kb.lv025,'00:05:00') CheckNgay 
         FROM hr_kb0002 kb 
         LEFT JOIN hr_lv0020 nv ON kb.lv002 = nv.lv001
         LEFT JOIN hr_lv0002 pb ON nv.lv029 = pb.lv001
         WHERE 1=1 " . $this->GetCondition() . " $strSort LIMIT $curRow, $maxRows";
		$vorder = $curRow;
		$bResult = db_query($sqlS);
		$this->Count = db_num_rows($bResult);
		$strTrH = "";
		$strH = "";
		$strTrEnter = "<td class=\"@#04\"><input title=\"Hiển thị tất cả phép\" type=\"checkbox\" name=\"txtcheckmonth\" value=\"1\" onclick=\"document.frmchoose.submit();\" " . (($this->checkmonth == 1) ? 'checked="true"' : "") . "/></td><td class=\"@#04\"><input tabindex=2 type=\"checkbox\" name=\"qxtlvkeep\" value=1 " . (($this->lv001 == '1') ? 'checked="true"' : '') . "/></td>"; //<input type='hidden' name='qxtlv001' id='qxtlv001' value=''/><input onclick='Save()' tabindex='3' type='button' value='Thêm' style='width:80%'/></td>";
		for ($i = 0; $i < count($lstArr); $i++) {
			$vTemp = str_replace("@01", "", $lvTdH);
			$vTemp = str_replace("@02", $this->ArrPush[(int)$this->ArrGet[$lstArr[$i]]], $vTemp);
			$strH = $strH . $vTemp;
			switch ($lstArr[$i]) {
				case 'lv099':
					$vTempEnter = '<td><ul style="width:100%" id="pop-nav" lang="pop-nav1" onMouseOver="ChangeName(this,1)" onKeyUp="ChangeName(this,1)"> <li class="menupopT">
                                        <input name="qxtlv001" class="txtenterquick" type="text" autocomplete="off" style="width:100%;min-width:80px" onKeyUp="LoadPopupParentTabIndex(event,this,\'qxtlv015\',\'hr_lv0020\',\'concat(lv002,@! - @!,lv001)\')" tabindex="2" value="">
                                        <div id="lv_popup" lang="lv_popup1"> </div>                          
                                        </li>
                                    </ul></td>';
					break;
				case 'lv002':
					$vstr = '<select class="selenterquick" name="qxtlv002" id="qxtlv002" tabindex="2" style="width:100%;min-width:50px" onKeyPress="return CheckKey(event,7)" onchange="getPhongBan(this.value)">' . $this->LV_LinkFieldExt('lv002', $this->lv002) . '</select>';
					$vTempEnter = str_replace("@02", $vstr, $this->Align($lvTd, (int)($this->ArrView[$lstArr[$i]] ?? 0)));
					break;
				case 'lv010':
					$vstr = '<select class="selenterquick" name="qxtlv010_display" id="qxtlv010" tabindex="2" style="width:100%;min-width:50px" disabled="disabled">' . $this->LV_LinkFieldExt('lv010', $this->lv010) . '</select>';
					$vstr .= '<input type="hidden" name="qxtlv010" id="qxtlv010_hidden" value="' . $this->lv010 . '">';
					$vTempEnter = str_replace("@02", $vstr, $this->Align($lvTd, (int)($this->ArrView[$lstArr[$i]] ?? 0)));
					break;
				case 'lv004':
					$vstr = '<select name="qxtlv004" id="qxtlv004" tabindex="2" style="width:100%;min-width:50px" onKeyPress="return CheckKey(event,7)">' . $this->LV_LinkFieldExt('lv004', $this->lv004) . '</select>';
					$vTempEnter = str_replace("@02", $vstr, $this->Align($lvTd, (int)($this->ArrView[$lstArr[$i]] ?? 0)));
					break;
				case 'lv005':
					$vstr  = '<div style="display:flex; align-items:center; gap:10px; min-width:300px;">';
					$vstr .= '<input type="text" name="lv005" id="lv005" value="'
						. htmlspecialchars($_GET['latlng'] ?? ($this->ArrView[$lstArr[$i]] ?? ''))
						. '" readonly style="flex:1; min-width:120px;">';
					$vstr .= '<button type="button" onclick="window.location.href=\'/nhansu/soft/hr_kb0002/select_location.php?back='
						. rawurlencode($_SERVER['REQUEST_URI']) . '\'">Chọn tọa độ</button>';
					$vstr .= '</div>';
					$vTempEnter = str_replace("@02", $vstr, $this->Align($lvTd, (int)($this->ArrView[$lstArr[$i]] ?? 0)));
					break;

				case 'lv007':
					$vTempEnter = '
						<td>
							<div style="display:flex; align-items:center; gap:10px; min-width:300px;">
								<input type="file" name="qxtlv007" id="qxtlv007" accept="image/*"
									style="flex:1; min-width:120px;"
									onchange="previewImage_lv007(this)">
								<img tabindex="2" border="0" title="Add" class="imgButton"
									onclick="handleAddChamCong()"
									onmouseout="this.src=\'../images/iconcontrol/btn_add.jpg\';"
									onmouseover="this.src=\'../images/iconcontrol/btn_add_02.jpg\';"
									src="../images/iconcontrol/btn_add.jpg"
									onkeypress="return CheckKey(event,11)">
							</div>
							<div id="preview_lv007" style="margin-top:5px"></div>
						</td>
					';
					break;
				default:
					$vTempEnter = "<td>&nbsp;</td>";
					break;
			}
			$strTrEnter = $strTrEnter . $vTempEnter;
		}

		while ($vrow = db_fetch_array($bResult)) {
			$this->current_row_lv003 = $vrow['lv003'];
			error_log("Current row lv003: " . $this->current_row_lv003);
			$strL = "";
			$vorder++;
			if ($vrow['SoNgayXinPhep'] < 2) {
				if ($vrow['lv008'] != 'CT') {
					//$vrow['lv008']=$vrow['lv008'].'('.'Xin phép không đúng trước 2 ngày'.')';
				}
			}
			for ($i = 0; $i < count($lstArr); $i++) {
				if ($lstArr[$i] == 'lv008')
					$vTemp = str_replace("@02", str_replace("@02", $this->getvaluelink($lstArr[$i], $this->FormatView(str_replace(" ", "&nbsp;", str_replace("\n", "<br/>", $vrow[$lstArr[$i]])), (int)$this->ArrView[$lstArr[$i]])), str_replace("@01", $vrow['lv001'], $lvHref)), $this->Align($lvTd, (int)$this->ArrView[$lstArr[$i]]));
				else if ($lstArr[$i] == 'lv010') {
					// Hiển thị tên phòng ban trực tiếp từ JOIN
					$tenPhongBan = !empty($vrow['ten_phong_ban']) ? $vrow['ten_phong_ban'] : '';
					$vTemp = str_replace("@02", $this->FormatView($tenPhongBan, (int)$this->ArrView[$lstArr[$i]]), $this->Align($lvTd, (int)$this->ArrView[$lstArr[$i]]));
				} else
					$vTemp = str_replace("@02", str_replace("@02", $this->getvaluelink($lstArr[$i], $this->FormatView($vrow[$lstArr[$i]], (int)$this->ArrView[$lstArr[$i]])), str_replace("@01", $vrow['lv001'], $lvHref)), $this->Align($lvTd, (int)$this->ArrView[$lstArr[$i]]));
				$strL = $strL . $vTemp;
			}
			/*if($vrow['CheckNgay']>=$this->DateCurrent && $vrow['lv004']==0 && $vrow['lv021']==0)
            $lvTr=$lvTrTemp."<td width=1%><input style=\"padding:2px;\" name=\"$lvChk\" type=\"button\" id=\"$lvChk@03\" onclick=\"Delete('@02')\" value=\"Xoá phép\" tabindex=\"2\"  onKeyUp=\"return CheckKeyCheck(event,2,'$lvChk',$lvFrom, '$lvChk', '$lvChkAll',@03)\"/></td>@#01</tr>";
        else
            $lvTr=$lvTrTemp."<td></td>@#01</tr>";*/
			$strTr = $strTr . str_replace("@#01", $strL, str_replace("@02", $vrow['lv001'], str_replace("@03", $vorder, str_replace("@01", $vorder % 2, $lvTr))));
			//if($vrow['lv021']==1)
			{
				switch ($vrow['lv021']) {
					case -1:
						$strTr = str_replace("@#04", "lvlineapproval_purple", $strTr);
						break;
					case 0:
						$strTr = str_replace("@#04", "lvlineapproval_level2", $strTr);
						break;
					case 1:
						$strTr = str_replace("@#04", "lvlineapproval_black", $strTr);
						break;
					case 2:
						$strTr = str_replace("@#04", "lvlineapproval_level3", $strTr);
						break;
				}
			}
			//else    $strTr=str_replace("@#04","",$strTr);

		}
		$strTrH = str_replace("@#01", $strH, $lvTrH);

		// Thêm JavaScript mapping từ mã nhân viên đến mã phòng ban
		$jsMapping = "<script>
        // Mapping từ mã nhân viên đến mã phòng ban
        var nhanVienToPhongBan = {";

		// Lấy dữ liệu ánh xạ từ database
		$sql = "SELECT lv001, lv029 FROM hr_lv0020 WHERE lv029 != ''";
		$result = db_query($sql);
		$mappings = array();

		while ($row = db_fetch_array($result)) {
			if (!empty($row['lv029'])) {
				$mappings[] = "'" . addslashes($row['lv001']) . "': '" . addslashes($row['lv029']) . "'";
			}
		}

		$jsMapping .= implode(',', $mappings);
		$jsMapping .= "};
        
        function getPhongBan(maNhanVien) {
            if (maNhanVien && nhanVienToPhongBan[maNhanVien]) {
                var maPhongBan = nhanVienToPhongBan[maNhanVien];
                var selectElement = document.getElementById('qxtlv010');
                var hiddenInput = document.getElementById('qxtlv010_hidden');
                
                // Cập nhật giá trị hidden để submit
                hiddenInput.value = maPhongBan;
                
                // Hiển thị giá trị trên combobox (cho người dùng thấy)
                for (var i = 0; i < selectElement.options.length; i++) {
                    if (selectElement.options[i].value == maPhongBan) {
                        selectElement.selectedIndex = i;
                        break;
                    }
                }
            }
        }
    </script>";

		return str_replace("@#01", $strTrH . "<tr class='lvlinehtable0'>" . $strTrEnter . "</tr>" . $strTr, $lvTable) . $jsMapping;
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
			window.open('hr_kb0002/?lang=" . $this->lang . "&func='+value,'','width=800,height=600,left=200,top=100,screenX=0,screenY=100,resizable=yes,status=no,scrollbars=yes,menubar=yes');
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
		$sqlS = "SELECT *,lv015 lv829,lv015 lv099,IF(SUBSTR(lv003,1,3)='1/2',DATEDIFF(lv017,lv016)+0.5,IF(TIMEDIFF(lv017,lv016)<'24:00:00' AND DATEDIFF(lv017,lv016)<1,1,DATEDIFF(lv017,lv016)+1)) lv098 FROM hr_kb0002 WHERE 1=1  " . $this->RptCondition . " $strSort LIMIT $curRow, $maxRows";
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
				if ($lstArr[$i] == 'lv099')
					$vTemp = str_replace("@02", $this->getvaluelink($lstArr[$i], $this->FormatView($vrow[$lstArr[$i]], (int)$this->ArrView[$lstArr[$i]])), $this->Align($lvTd, (int)$this->ArrView[$lstArr[$i]]));
				else
					$vTemp = str_replace("@02", $this->FormatView($vrow[$lstArr[$i]], (int)$this->ArrView[$lstArr[$i]]), $this->Align($lvTd, (int)$this->ArrView[$lstArr[$i]]));
				$strL = $strL . $vTemp;
			}


			$strTr = $strTr . str_replace("@#01", $strL, str_replace("@02", $vrow['lv001'], str_replace("@03", $vorder, str_replace("@01", $vorder % 2, $lvTr))));
		}
		$strTrH = str_replace("@#01", $strH, $lvTrH);
		return str_replace("@#01", $strTrH . ($strTr ?? ""), $lvTable);
	}
	protected function GetCondition7Day()
	{
		$strCondi = "";
		if ($this->NgayXinPhep != '') {
			$strCondi = $strCondi . " and 
			(
				(A.lv016 >='$this->NgayXinPhep 00:00:00' and A.lv017 <='$this->NgayXinPhep 23:59:59')
				OR
				( '$this->NgayXinPhep 23:59:59'>=A.lv016 and '$this->NgayXinPhep 00:00:00'<=A.lv017)
			)
			";
		}
		if ($this->lv016_ != '' && $this->lv017_ != '') {
			$strCondi = $strCondi . " and 
			(
				(A.lv016 >='$this->lv016_ 00:00:00' and A.lv017 <='$this->lv017_ 23:59:59')
				OR
				( '$this->lv016_ 23:59:59'>=A.lv016 and '$this->lv016_ 00:00:00'<=A.lv017)
				OR
				( '$this->lv017_ 23:59:59'>=A.lv016 and '$this->lv017_ 00:00:00'<=A.lv017)
			)
			";
		} elseif ($this->lv016_ != '') {
			$strCondi = $strCondi . " and ('$this->lv016_ 23:59:59'>=A.lv016 and '$this->lv016_ 00:00:00'<=A.lv017)";
		} elseif ($this->lv017_ != '') {
			$strCondi = $strCondi . " and ('$this->lv017_ 23:59:59'>=A.lv016 and '$this->lv017_ 00:00:00'<=A.lv017)
			";
		}
		if ($this->ListEmp != '') $strCondi = $strCondi . " and A.lv001 in ($this->ListEmp)";
		if ($this->lv001 != "") $strCondi = $strCondi . " and A.lv001  like '%$this->lv001%'";
		if ($this->lv002 != "") $strCondi = $strCondi . " and A.lv002  like '%$this->lv002%'";
		if ($this->lv003 != "") $strCondi = $strCondi . " and A.lv003  like '%$this->lv003%'";
		if ($this->lv004 != "") $strCondi = $strCondi . " and A.lv004  like '%$this->lv004%'";
		if ($this->lv005 != "") $strCondi = $strCondi . " and A.lv005  like '%$this->lv005%'";
		if ($this->lv006 != "") $strCondi = $strCondi . " and A.lv006  like '%$this->lv006%'";
		if ($this->lv007 != "")  $strCondi = $strCondi . " and A.lv007 like '%$this->lv007%'";
		if ($this->lv008 != "")  $strCondi = $strCondi . " and A.lv008 like '%$this->lv008%'";
		if ($this->lv009 != "")  $strCondi = $strCondi . " and A.lv009 like '%$this->lv009%'";
		if ($this->lv010 != "")  $strCondi = $strCondi . " and A.lv010 like '%$this->lv010%'";
		if ($this->lv011 != "")  $strCondi = $strCondi . " and A.lv011 like '%$this->lv011%'";
		if ($this->lv012 != "") {
			if ($this->lv015 != "")  $strCondi = $strCondi . " and (A.lv012='$this->lv012' or A.lv015 like '%$this->lv015%')";
			else
				$strCondi = $strCondi . " and A.lv012='$this->lv012'";
		}
		if ($this->lv013 != "")  $strCondi = $strCondi . " and A.lv013 like '%$this->lv013%'";
		if ($this->lv014 != "")  $strCondi = $strCondi . " and A.lv014 like '%$this->lv014%'";
		if ($this->lv015 != "")  $strCondi = $strCondi . " and A.lv015 = '$this->lv015'";
		if ($this->lv016 != "")  $strCondi = $strCondi . " and A.lv016 like '%$this->lv016%'";
		if ($this->lv017 != "")  $strCondi = $strCondi . " and A.lv017 like '%$this->lv017%'";
		if ($this->lv018 != "")  $strCondi = $strCondi . " and A.lv018 like '%$this->lv018%'";
		if ($this->lv019 != "")  $strCondi = $strCondi . " and A.lv019 like '%$this->lv019%'";
		if ($this->lv020 != "")  $strCondi = $strCondi . " and A.lv020 like '%$this->lv020%'";
		if ($this->lv021 != "")  $strCondi = $strCondi . " and A.lv021 like '%$this->lv021%'";
		if ($this->lv022 != "")  $strCondi = $strCondi . " and A.lv022 like '%$this->lv022%'";
		if ($this->lv023 != "")  $strCondi = $strCondi . " and A.lv023 like '%$this->lv023%'";
		return $strCondi;
	}

	function LV_BuilList7Day($lvList, $lvFrom, $lvChkAll, $lvChk, $curRow, $maxRows, $paging, $lvOrderList)
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
			<td width=1% onclick=\"Select_Check('$lvChk@03',$lvFrom, '$lvChk', '$lvChkAll')\">@03</td>
			@#01
		</tr>
		";
		$lvTdH = "<td width=\"@01\" class=\"lvhtable\">@02</td>";
		$lvTd = "<td class=@#04 align=@#05 nowrap>@02</td>";
		$sqlS = "SELECT A.*,DATEDIFF(A.lv017,A.lv016) SoNgayXinPhep,B.lv029 lv829,A.lv015 lv099,IF(SUBSTR(A.lv003,1,3)='1/2',DATEDIFF(A.lv017,A.lv016)+0.5,IF(TIME_TO_SEC(TIMEDIFF(A.lv017,A.lv016))<TIME_TO_SEC('24:00:00'),1,DATEDIFF(A.lv017,A.lv016)+1)) lv098,TIMEDIFF(A.lv017,A.lv016) lv098_1 FROM hr_kb0002 A inner join hr_lv0020 B on A.lv015=B.lv001 WHERE 1=1  " . $this->GetCondition7Day() . "  order by A.lv016 asc";
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
			if ($vrow['lv003'] == 'CT' && $this->LV_UserID != 'MP001') {
				$vrow['lv008'] = '***';
			}
			if ($vrow['SoNgayXinPhep'] < 2) {
				if ($vrow['lv003'] != 'CT') {
					//$vrow['lv008']=$vrow['lv008'].'('.'Xin phép không đúng trước 2 ngày'.')';
				}
			}
			for ($i = 0; $i < count($lstArr); $i++) {
				if ($lstArr[$i] == 'lv003') {
					if ($vrow['lv003'] == '')
						$vTemp = str_replace("@02", $this->getvaluelink('lv022', $this->FormatView($vrow['lv022'], (int)$this->ArrView[$lstArr[$i]])), $this->Align($lvTd, (int)$this->ArrView[$lstArr[$i]]));
					else
						$vTemp = str_replace("@02", $this->getvaluelink($lstArr[$i], $this->FormatView($vrow[$lstArr[$i]], (int)$this->ArrView[$lstArr[$i]])), $this->Align($lvTd, (int)$this->ArrView[$lstArr[$i]]));
				} elseif ($lstArr[$i] == 'lv829') {
					$vTemp = str_replace("@02", $this->LV_GetNameVietTat($vrow[$lstArr[$i]]), $this->Align($lvTd, (int)$this->ArrView[$lstArr[$i]]));
				} elseif ($lstArr[$i] == 'lv098') {
					switch ($vrow['lv022']) {
						case 13:
							$vTemp = str_replace("@02", $vrow['lv098_1'], $this->Align($lvTd, (int)$this->ArrView[$lstArr[$i]]));
							break;
						case 4:
						case 5:
						case 6:
						case 7:
						case 8:
						case 9:
							$vTimeStart = substr($vrow['lv016'], 11, 8);
							$vTimeEnd = substr($vrow['lv017'], 11, 8);
							if ($vTimeStart > $vTimeEnd) {
								$vSoGio = TIMEADD(TIMEDIFF('24:00:00', $vTimeStart), $vTimeEnd);
							} else
								$vSoGio = TIMEDIFF($vTimeEnd, $vTimeStart);
							$vTemp = str_replace("@02", $vSoGio, $this->Align($lvTd, (int)$this->ArrView[$lstArr[$i]]));
							break;
						default:
							$vTemp = str_replace("@02", $this->getvaluelink($lstArr[$i], $this->FormatView($vrow[$lstArr[$i]], (int)$this->ArrView[$lstArr[$i]])), $this->Align($lvTd, (int)$this->ArrView[$lstArr[$i]]));
							break;
					}
				} else
					$vTemp = str_replace("@02", $this->getvaluelink($lstArr[$i], $this->FormatView($vrow[$lstArr[$i]], (int)$this->ArrView[$lstArr[$i]])), $this->Align($lvTd, (int)$this->ArrView[$lstArr[$i]]));
				//else
				//$vTemp=str_replace("@02",$this->FormatView($vrow[$lstArr[$i]],(int)$this->ArrView[$lstArr[$i]]),$this->Align($lvTd,(int)$this->ArrView[$lstArr[$i]]));
				$strL = $strL . $vTemp;
			}


			$strTr = $strTr . str_replace("@#01", $strL, str_replace("@02", $vrow['lv001'], str_replace("@03", $vorder, str_replace("@01", $vorder % 2, $lvTr))));
			//if($vrow['lv021']==1)
			{
				switch ($vrow['lv021']) {
					case 0:
						$strTr = str_replace("@#04", "lvlineapproval_level2", $strTr);
						break;
					case 1:
						$strTr = str_replace("@#04", "lvlineapproval_black", $strTr);
						break;
					case 2:
						$strTr = str_replace("@#04", "lvlineapproval_level3", $strTr);
						break;
				}
			}
			//else	$strTr=str_replace("@#04","",$strTr);

		}
		$strTrH = str_replace("@#01", $strH, $lvTrH);
		return str_replace("@#01", $strTrH . ($strTr ?? ""), $lvTable);
	}
	public function LV_LinkFieldExt($vFile, $vSelectID)
	{
		if ($vFile == 'lv002') {
			// Lấy dữ liệu nhân viên kèm theo phòng ban
			$sql = "SELECT a.lv001, a.lv002, a.lv029, IF(a.lv001='$vSelectID',1,0) lv003 
                    FROM hr_lv0020 a";
			$result = db_query($sql);

			$options = "<option value=''>--Chọn nhân viên--</option>";
			while ($row = db_fetch_array($result)) {
				$selected = ($row['lv003'] == 1) ? "selected" : "";
				$options .= "<option value='{$row['lv001']}' data-phongban='{$row['lv029']}' $selected>{$row['lv002']}</option>";
			}
			return $options;
		} else {
			return $this->CreateSelect($this->sqlcondition($vFile, $vSelectID), 0);
		}
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
				$vsql = "select lv001,lv002,IF(lv001='$vSelectID',1,0) lv003 from  hr_lv0020";
				break;
			case 'lv010':
				$vsql = "select lv001,lv003 as lv002, IF(lv001='$vSelectID',1,0) lv003 from  hr_lv0002 where lv002 = 'SOF'";
				break;
			case 'lv004':
				$vsql = "select lv001,lv002, IF(lv001='$vSelectID',1,0) lv003 from  hr_kb0001 ";
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
				$lvopt = 0;
				$vsql = "select lv001,lv002,IF(lv001='$vSelectID',1,0) lv003 from  hr_lv0020 where lv001='$vSelectID'";
				break;
			case 'lv010':
				$lvopt = 0;
				$vsql = "select pb.lv001, pb.lv003 as lv002, 1 as lv003
					FROM hr_lv0020 nv 
					JOIN hr_lv0002 pb ON nv.lv029 = pb.lv001 
					WHERE nv.lv001='$vSelectID'";
				break;
			case 'lv004':
				$vsql = "select lv001,lv002,IF(lv001='$vSelectID',1,0) lv003 from  hr_kb0001 where lv001='$vSelectID'";
				break;
			case 'lv007':
				if ($vSelectID != "") {
					// Debug: Kiểm tra dữ liệu
					error_log("LV007 Debug - vSelectID: " . $vSelectID);
					error_log("LV007 Debug - current_row_lv003: " . $this->current_row_lv003);

					$date = new DateTime($this->current_row_lv003);
					$subdir = "Nam_" . $date->format('Y') . "/Thang_" . $date->format('m') . "/Ngay_" . $date->format('d') . "/";
					$imageUrl = "http://192.168.1.90/services/hr/services.sof.vn/loadAnh.php?filename=" . urlencode($vSelectID) . "&subdir=" . urlencode($subdir);

					error_log("LV007 Debug - imageUrl: " . $imageUrl);

					return '<img src="' . $imageUrl . '" style="max-width:100px;max-height:100px;border:1px solid red;" title="' . $imageUrl . '" onerror="console.log(\'Image error: ' . $imageUrl . '\')">';
				}
				return "No image";
				break;
			case 'lv006':
				$vsql = "select lv001,lv002,IF(lv001='$vSelectID',1,0) lv003 from  jo_lv0003 where lv001='$vSelectID'";
				break;
			case 'lv021':
				$vsql = "select lv001,lv002,IF(lv001='$vSelectID',1,0) lv003 from  jo_lv0003 where lv001='$vSelectID'";
				break;
			case 'lv046':
				$vsql = "select lv001,lv002,IF(lv001='$vSelectID',1,0) lv003 from  jo_lv0003 where lv001='$vSelectID'";
				break;
			case 'lv013':
				$lvopt = 0;
				$vsql = "select lv001,lv002,IF(lv001='$vSelectID',1,0) lv003 from  hr_lv0020 where lv001='$vSelectID'";
				break;
			case 'lv014':
				$lvopt = 0;
				$vsql = "select lv001,lv002,IF(lv001='$vSelectID',1,0) lv003 from  hr_lv0020 where lv001='$vSelectID'";
				break;
			case 'lv020':
				$lvopt = 0;
				$vsql = "select lv001,lv002,IF(lv001='$vSelectID',1,0) lv003 from  hr_lv0020 where lv001='$vSelectID'";
				break;
			case 'lv829':
				$lvopt = 0;
				$vsql = "select lv001,lv003 lv002,IF(lv001='$vSelectID',1,0) lv003 from  hr_lv0002 where lv001='$vSelectID'";
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
?>

<script>
	function previewImage_lv007(input) {
		const file = input.files[0];
		if (!file) return;
		const reader = new FileReader();
		reader.onload = function(evt) {
			document.getElementById("preview_lv007").innerHTML =
				`<img src="${evt.target.result}" 
                style="max-width:80px;max-height:80px;
                       border:1.5px solid #d32f2f;
                       border-radius:8px;object-fit:cover;">`;
		};
		reader.readAsDataURL(file);
	}

	async function uploadImageChamCong(file, filename = "") {
		const formData = new FormData();
		formData.append("image", file);
		if (filename) formData.append("filename", filename);

		const response = await fetch("http://192.168.1.90/services/hr/services.sof.vn/upload.php", {
			method: "POST",
			body: formData
		});
		return await response.json();
	}

	async function handleAddChamCong() {
		const input = document.getElementById("qxtlv007");
		const file = input.files[0];
		if (!file) {
			alert("Vui lòng chọn ảnh minh chứng!");
			return;
		}

		const maNhanVien = document.getElementById("qxtlv002")?.value || "null";
		const viTri = document.getElementById("lv005")?.value || "";
		const loaiChamCong = document.getElementById("qxtlv004").value || "";
		let filename = `${maNhanVien}_${Date.now()}_${file.name}`;
		const uploadResult = await uploadImageChamCong(file, filename);

		if (!uploadResult.success) {
			alert("Upload ảnh thất bại: " + uploadResult.message);
			return;
		}

		const requestData = {
			table: "hr_kb0002",
			func: "add",
			maNhanVien: maNhanVien,
			trangThai: "0",
			viTri: viTri,
			hinhAnh: uploadResult.filePath,
			hinh_anh: uploadResult.filePath,
			image: uploadResult.filePath,
			loaiChamCong: loaiChamCong,
		};

		try {
			const response = await fetch("http://192.168.1.90/services/hr/services.sof.vn/index.php", {
				method: "POST",
				headers: {
					"Content-Type": "application/json"
				},
				body: JSON.stringify(requestData)
			});
			const data = await response.json();
			if (data.success) {
				alert("Đề xuất chấm công thành công!");

				// Reset các giá trị
				input.value = "";
				document.getElementById("lv005").value = ""
				document.getElementById("preview_lv007").innerHTML = "";
				// document.getElementById("qxtlv002").value = ""; // nếu muốn reset mã nhân viên
				window.location.reload();

			} else {
				alert("Có lỗi: " + data.message);
			}
		} catch (error) {
			alert("Lỗi gọi API chấm công: " + error);
		}
	}
</script>