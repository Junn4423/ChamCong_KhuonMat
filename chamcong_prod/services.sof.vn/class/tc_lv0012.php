<?php
/////////////coding tc_lv0012///////////////
class   tc_lv0012 extends lv_controler
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
	public $lvNVID = null;
	public $ArrGioVaoRa = null;
	public $ArrNextDay = null;

	///////////
	public $DefaultFieldList = "lv001,lv002,lv003,lv004,lv005,lv006,lv007";
	////////////////////GetDate
	public $DateDefault = "1900-01-01";
	public $DateCurrent = "1900-01-01";
	public $Count = null;
	public $paging = null;
	public $lang = null;
	protected $objhelp = 'tc_lv0012';
	////////////
	var $ArrOther = array();
	var $ArrPush = array();
	var $ArrFunc = array();
	var $ArrGet = array("lv001" => "2", "lv002" => "3", "lv003" => "4", "lv004" => "5", "lv005" => "6", "lv006" => "7", "lv007" => "8");
	var $ArrView = array("lv001" => "0", "lv002" => "0", "lv003" => "0", "lv004" => "2", "lv005" => "0", "lv006" => "0", "lv007" => "0");

	public $LE_CODE = "NjlIUS02VFdULTZIS1QtNlFIQQ==";
	function __construct($vCheckAdmin, $vUserID, $vright)
	{
		$this->DateCurrent = GetServerDate() . " " . GetServerTime();
		$this->Set_User($vCheckAdmin, $vUserID, $vright);
		$this->isRel = 0;
		$this->isRpt = 0;
		$this->isAdd = 0;
		$this->isEdit = 0;
		$this->isHelp = 1;
		$this->isConfig = 0;
		$this->isFil = 0;
		$this->isDel = 0;
		$this->lang = $_GET['lang'];
	}
	protected function LV_CheckLock()
	{
		$lvsql = "select lv005 from  tc_lv0009 Where lv003=month('" . $this->lv004 . "-00') and lv004=year('" . $this->lv004 . "-00') and lv002='$this->lvNVID'";
		$vReturn = db_query($lvsql);
		if ($vReturn) {
			$vrow = db_fetch_array($vReturn);
			if ($vrow['lv005'] >= 1) {
				$this->isAdd = 0;
				$this->isEdit = 0;
				$this->isDel = 0;
			}
		}
	}
	function LV_Load()
	{
		$vsql = "select * from  tc_lv0011";
		$vresult = db_query($vsql);
		$vrow = db_fetch_array($vresult);
		if ($vrow) {
			$this->lv001 = $vrow['lv001'];
			$this->lv002 = $vrow['lv002'];
			$this->lv003 = $vrow['lv003'];
			$this->lv004 = $vrow['lv004'];
			$this->lv005 = $vrow['lv005'];
			$this->lv005 = $vrow['lv006'];
		}
	}
	function LV_LoadID($vlv001)
	{
		$lvsql = "select * from  tc_lv0011 Where lv001='$vlv001'";
		$vresult = db_query($lvsql);
		$vrow = db_fetch_array($vresult);
		if ($vrow) {
			$this->lv001 = $vrow['lv001'];
			$this->lv002 = $vrow['lv002'];
			$this->lv003 = $vrow['lv003'];
			$this->lv004 = $vrow['lv004'];
			$this->lv005 = $vrow['lv005'];
			$this->lv005 = $vrow['lv006'];
		}
	}
	function LV_Update()
	{
		if ($this->isEdit == 0) return false;
		if (!$this->LV_CheckLocked($this->lv004)) return false;
		$lvsql = "Update tc_lv0011 set lv005='$this->lv005',lv006='$this->lv006',lv007='$this->lv007',lv008='$this->lv008',lv010='$this->lv010' where  lv001='$this->lv001';";
		$vReturn = db_query($lvsql);
		if ($vReturn) {
			$this->InsertLogOperation($this->DateCurrent, 'tc_lv0012.update', sof_escape_string($lvsql));
		}
		return $vReturn;
	}
	function LV_UpdateState($vEmployeeID, $vDate, $vTime, $vState, $vUserID = '')
	{
		if ($this->LV_CheckLocked($vDate) == FALSE) return false;
		$lvsql = "Update tc_lv0012 set lv006='$vState',lv007='$vUserID' where  lv001='$vEmployeeID' and lv002='$vDate' and lv003='$vTime';";
		$vReturn = db_query($lvsql);
		if ($vReturn) {
			$this->InsertLogOperation($this->DateCurrent, 'tc_lv0012.update', sof_escape_string($lvsql));
		}
		return $vReturn;
	}
	function LV_UpdateState_1($vEmployeeID, $vDate, $vTime, $vState, $vUserID = '')
	{
		//if($this->LV_CheckLocked($vDate)==FALSE) return false;
		echo $lvsql = "Update tc_lv0012 set lv006='$vState',lv007='$vUserID' where  lv001='$vEmployeeID' and lv002='$vDate' and lv003='$vTime';";
		$vReturn = db_query($lvsql);
		if ($vReturn) {
			$this->InsertLogOperation($this->DateCurrent, 'tc_lv0012.update', sof_escape_string($lvsql));
		}
		return $vReturn;
	}
	function LV_UpdateStateOverTime($vEmployeeID, $vDate, $vTime, $vState, $vUserID = '')
	{
		if ($this->LV_CheckLocked($vDate) == FALSE) return false;
		$lvsql = "Update tc_lv0012 set lv006='1',lv007='$vUserID' where  lv001='$vEmployeeID' and lv002='$vDate' and lv003='$vTime';";
		$vReturn = db_query($lvsql);
		if ($vReturn) {
			$this->InsertLogOperation($this->DateCurrent, 'tc_lv0012.update', sof_escape_string($lvsql));
		}
		return $vReturn;
	}
	function LV_InsertAuto($vEmployeeID, $vDate, $vTime, $vState, $vUserID = '')
	{
		if ($this->LV_CheckLocked($vDate) == FALSE) return false;
		$lvsql = "insert into tc_lv0012 (lv001,lv002,lv003,lv004,lv006,lv005,lv098) values('$vEmployeeID','$vDate','$vTime','I','$vState','$vUserID',now())";
		$vReturn = db_query($lvsql);
		if ($vReturn) {
			$this->InsertLogOperation($this->DateCurrent, 'tc_lv0012.insert', sof_escape_string($lvsql));
		}
		return $vReturn;
	}
	function LV_InsertAuto_1($vEmployeeID, $vDate, $vTime, $loaiChamCong, $vUserID = '')
	{
		if ($this->LV_CheckLocked($vDate) == FALSE) return false;
		$lvsql = "insert into tc_lv0012 (lv001,lv002,lv003,lv004,lv006,lv005,lv098,lv007) values('$vEmployeeID','$vDate','$vTime','$loaiChamCong','0','MAP',now(),'$vUserID')";
		$vReturn = db_query($lvsql);
		if ($vReturn) {
			$this->InsertLogOperation($this->DateCurrent, 'tc_lv0012.insert', sof_escape_string($lvsql));
		}
		return $vReturn;
	}
	function LV_InsertXML($vEmployeeID, $vDate, $vTime, $vState, $vUserID = '', $vMoreAdv = '0')
	{
		if ($this->LV_CheckLocked($vDate) == FALSE) return false;
		$vTime = str_replace(";", ",", $vTime);
		$vArrTime = explode(",", $vTime);
		foreach ($vArrTime as $Time) {
			if ($vMoreAdv == 9) {
				$lvsql = "delete from tc_lv0012 where lv001='$vEmployeeID' and lv002='$vDate' and lv003='$Time'";
				$vReturn = db_query($lvsql);
				$lvsqllog = $lvsqllog . $lvsql . ";";
			} else {
				if (trim($Time) != "" && $Time != NULL && $Time != "00:00:00" && $Time != "00:00" && $Time != "00") {
					$lvsql = "insert into tc_lv0012 (lv001,lv002,lv003,lv004,lv006,lv005) values('$vEmployeeID','$vDate','$Time','$vMoreAdv','$vState','$vUserID')";
					$vReturn = db_query($lvsql);
					$lvsqllog = $lvsqllog . $lvsql . ";";
				}
			}
		}
		if ($vReturn) {
			$this->InsertLogOperation($this->DateCurrent, 'tc_lv0012.insert', sof_escape_string($lvsqllog));
		}
		return $vReturn;
	}
	function LV_Insert()
	{
		$lvsql = "insert into tc_lv0012 (lv001,lv002,lv003,lv004,lv005) values('$this->lv001','$this->lv002','$this->lv003','$this->lv004','$this->lv005')";
		$vReturn = db_query($lvsql);
		if ($vReturn) {
			$this->InsertLogOperation($this->DateCurrent, 'tc_lv0009.insert', sof_escape_string($lvsql));
		}
		return $vReturn;
	}
	//Hàm kiểm tra xem tất cả giờ vào ra điều 
	function  CheckOutShiftFirst($vArrTime, $vArrShift, $vListShiftReceive)
	{
		//Xác định giờ đầu, giờ kết thúc.
		$vCurArrTime = array();
		$vCount = count($vArrTime[0]);
		$vArrReturn = array();
		if ($vCount > 0) {
			$vShift = array();
			$vStartTime = $vArrTime[0][$vCount - 1][0];
			$vEndTime = $vArrTime[1][0][0];
			$vShift = $this->ConfirmShiftOut($vArrShift, $vStartTime, $vEndTime, $vListShiftReceive);
			if ($vShift['In'] != $vArrShift[0][0][0]) {
				$vArrReturn['CaIn'] = $vShift['In'];
				$vArrReturn['CaOut'] = $vArrShift[0][0][0];
			} elseif ($vShift['Out'] != $vArrShift[0][0][0]) {
				$vArrReturn['CaIn'] = $vArrShift[0][0][0];
				$vArrReturn['CaOut'] = $vShift['Out'];
			} else {
				$vArrReturn['CaIn'] = $vArrShift[0][0][0];
				$vArrReturn['CaOut'] = $vArrShift[0][0][0];
			}
			$vArrReturn['GioVao'] = $vStartTime;
			$vArrReturn['GioRa'] = $vEndTime;
			//$vArrReturn['CaIn']=$vShift['In'];
			//$vArrReturn['CaOut']=$vShift['Out'];
		} else {
			$vArrReturn['CaIn'] = $vArrShift[0][0][0];
			$vArrReturn['CaOut'] = $vArrShift[0][0][0];
		}

		return $vArrReturn;
	}
	function ConfirmShiftOut($vArrShift, $vStartTime, $vEndTime, $vListShiftReceive)
	{
		//trả về 2 trạng thái
		//Ca làm việc
		//Từ giờ starttime xác định vị trí ca.
		$vShift = array();
		$vStartInt = (int)str_replace(":", "", $vStartTime);
		$vEndInt = (int)str_replace(":", "", $vEndTime);
		$vShiftIn = '';
		$vShiftOut = '';
		$vDistantInSizeStart = 0;
		$vDistantInSizeStartEnd = 0;
		$vStartTrue = false;
		$vEndTrue = false;
		foreach ($vArrShift as $vShift) {
			if ($vListShiftReceive == "" || (strpos(',,' . $vListShiftReceive . ',', ',' . $vShift[1][0] . ',') > 0)) {
				//Ca đầu xác định ca làm việc
				$vShiftLast = $vShift[1][0];
				if ($vStartTrue == false) {
					if ($vStartInt <= $vShift[3][1]) {
						$vStartTrue = true;
						if ($vDistantInSizeStart == 0) {
							$vShiftIn = $vShift[1][0];
						} else {
							if ($vStartInt >= $vShift[3][1])
								$vDistantInSizeStartNew = $vStartInt - $vShift[3][1];
							else
								$vDistantInSizeStartNew = $vShift[3][1] - $vStartInt;
							if ($vDistantInSizeStartNew < $vDistantInSizeStart) $vShiftIn = $vShift[1][0];
						}
						if ($vStartInt >= $vShift[3][1])
							$vDistantInSizeStart = $vStartInt - $vShift[3][1];
						else
							$vDistantInSizeStart = $vShift[3][1] - $vStartInt;
					} else {
						$vStartTrue = false;
						$vShiftIn = $vShift[1][0];
						if ($vStartInt > $vShift[3][1])
							$vDistantInSizeStart = $vStartInt - $vShift[3][1];
						else
							$vDistantInSizeStart = $vShift[3][1] - $vStartInt;
					}
				}
				if ($vEndTrue == false) {
					if ($vShift[3][1] <= $vShift[4][1]) {
						if ($vEndInt >= $vShift[4][1]) {
							$vEndTrue = true;
							if ($vDistantInSizeEnd == 0) {
								$vShiftOut = $vShift[1][0];
							} else {
								if ($vEndInt >= $vShift[4][1])
									$vDistantInSizeEndNew = $vEndInt - $vShift[4][1];
								else
									$vDistantInSizeEndNew = $vShift[4][1] - $vEndInt;
								if ($vDistantInSizeEndNew <= $vDistantInSizeEnd) $vShiftOut = $vShift[1][0];
							}
							if ($vEndInt >= $vShift[4][1])
								$vDistantInSizeEnd = $vEndInt - $vShift[4][1];
							else
								$vDistantInSizeEnd = $vShift[4][1] - $vEndInt;
						} else {
							if ($vDistantInSizeEnd == 0) {
								$vShiftOut = $vShift[1][0];
								if ($vEndInt >= $vShift[4][1])
									$vDistantInSizeEnd = $vEndInt - $vShift[4][1];
								else
									$vDistantInSizeEnd = $vShift[4][1] - $vEndInt;
							} else {
								if ($vEndInt > $vShift[4][1])
									$vDistantInSizeEndNew = $vEndInt - $vShift[4][1];
								else
									$vDistantInSizeEndNew = $vShift[4][1] - $vEndInt;
								if ($vDistantInSizeEndNew <= $vDistantInSizeEnd) $vShiftOut = $vShift[1][0];
							}
						}
					} else {

						if ($vDistantInSizeEnd == 0) {
							$vShiftOut = $vShift[1][0];
						} else {
							if ($vEndInt >= $vShift[4][1])
								$vDistantInSizeEndNew = $vEndInt - $vShift[4][1];
							else
								$vDistantInSizeEndNew = $vShift[4][1] - $vEndInt;
							//echo "if($vDistantInSizeEndNew<$vDistantInSizeEnd) $vShiftOut=".$vShift[1][0].";";
							if ($vDistantInSizeEndNew <= $vDistantInSizeEnd) $vShiftOut = $vShift[1][0];
						}
						if ($vEndInt > $vShift[4][1])
							$vDistantInSizeEnd = $vEndInt - $vShift[4][1];
						else
							$vDistantInSizeEnd = $vShift[4][1] - $vEndInt;
					}
					//echo "HA:$vEndInt<".$vShift[4][1];
					//echo ": $vShiftOut<br>";
				}
			}
		}
		if ($vShiftIn == '') $vShiftIn = $vShiftLast;
		if ($vShiftOut == '') {
			$vShiftOut = $vShiftLast;
		}
		///Ca cuối xác định công và có tăng ca không					
		$vShift['In'] = $vShiftIn;
		$vShift['Out'] = $vShiftOut;
		return $vShift;
		//Công của công việc 0 không có tăng ca, 1 có tăng ca.
	}
	//Hàm kiểm tra xem tất cả giờ vào ra điều 
	function  CheckInShiftFirst($vArrTime, $vArrShift, $vListShiftReceive)
	{
		//Xác định giờ đầu, giờ kết thúc.
		$vCurArrTime = array();
		$vCount = count($vArrTime[0]);
		$vArrReturn = array();
		if ($vCount == 2) {
			$vShift = array();
			$vStartTime = $vArrTime[0][0][0];
			$vEndTime = $vArrTime[0][$vCount - 1][0];
			$vShift = $this->ConfirmShift($vArrShift, $vStartTime, $vEndTime, $vListShiftReceive);
			$vArrReturn['CaIn'] = $vShift['In'];
			$vArrReturn['CaOut'] = $vShift['Out'];
			if ($vArrReturn['CaIn'] != $vArrReturn['CaOut'] && (strpos(' ' . $vListShiftReceive, 'CaC') > 0 || strpos(' ' . $vListShiftReceive, 'CaD') > 0)) {
				if ((substr($vArrReturn['CaIn'], 0, 4) == 'CaHC' && substr($vArrReturn['CaOut'], 0, 3) == 'CaA') || (substr($vArrReturn['CaOut'], 0, 4) == 'CaHC' && substr($vArrReturn['CaIn'], 0, 3) == 'CaA')) {
					return 0;
				} else
					return 1;
			}
		} elseif ($vCount == 1) {
			$vShift = array();
			$vStartTime = $vArrTime[0][0][0];
			$vEndTime = $vArrTime[0][$vCount - 1][0];
			$vShift = $this->ConfirmShift($vArrShift, $vStartTime, $vEndTime, $vListShiftReceive);
			$vArrReturn['CaIn'] = $vShift['In'];
			if (substr($vArrReturn['CaIn'], 0, 3) == 'CaC' || substr($vArrReturn['CaIn'], 0, 3) == 'CaD') return 1;
		}
		return 0;
	}
	function ConfirmShift($vArrShift, $vStartTime, $vEndTime, $vListShiftReceive)
	{
		//trả về 2 trạng thái
		//Ca làm việc
		//Từ giờ starttime xác định vị trí ca.
		$vShift = array();
		$vStartInt = (int)str_replace(":", "", $vStartTime);
		$vEndInt = (int)str_replace(":", "", $vEndTime);
		$vShiftIn = '';
		$vShiftOut = '';
		$vDistantInSizeStart = 0;
		$vDistantInSizeStartEnd = 0;
		$vStartTrue = false;
		$vEndTrue = false;
		foreach ($vArrShift as $vShift) {
			//echo ',,'.$vListShiftReceive.',',',vinh'.$vShift[1][0].'vinh,='.strpos(',,'.$vListShiftReceive.',',','.$vShift[1][0].',').'<br/>';
			if ($vListShiftReceive == "" || (strpos(',,' . $vListShiftReceive . ',', ',' . $vShift[1][0] . ',') > 0)) {
				//Ca đầu xác định ca làm việc
				$vShiftLast = $vShift[1][0];
				if ($vStartTrue == false) {
					if ($vStartInt <= $vShift[3][1]) {
						$vStartTrue = true;
						if ($vDistantInSizeStart == 0) {
							$vShiftIn = $vShift[1][0];
						} else {
							if ($vStartInt >= $vShift[3][1])
								$vDistantInSizeStartNew = strtotime($vStartTime) - strtotime($vShift[3][0]);
							else
								$vDistantInSizeStartNew = strtotime($vShift[3][0]) - strtotime($vStartTime);
							if ($vDistantInSizeStartNew < $vDistantInSizeStart) $vShiftIn = $vShift[1][0];
						}
						if ($vStartInt >= $vShift[3][1])
							$vDistantInSizeStart = strtotime($vStartTime) - strtotime($vShift[3][0]);
						else
							$vDistantInSizeStart = strtotime($vShift[3][0]) - strtotime($vStartTime);
					} else {
						$vStartTrue = false;
						$vShiftIn = $vShift[1][0];
						if ($vStartInt >= $vShift[3][1])
							$vDistantInSizeStart = strtotime($vStartTime) - strtotime($vShift[3][0]);
						else
							$vDistantInSizeStart = strtotime($vShift[3][0]) - strtotime($vStartTime);
					}
				}
				if ($vEndTrue == false) {
					if ($vShift[3][1] <= $vShift[4][1]) {
						if ($vEndInt <= $vShift[4][1]) {
							$vEndTrue = true;
							if ($vDistantInSizeEnd == 0) {
								$vShiftOut = $vShift[1][0];
							} else {
								if ($vEndInt >= $vShift[4][1])
									$vDistantInSizeEndNew = strtotime($vEndTime) - strtotime($vShift[4][1]);
								else
									$vDistantInSizeEndNew = strtotime($vShift[4][0]) - strtotime($vEndTime);
								if ($vDistantInSizeEndNew < $vDistantInSizeEnd) $vShiftOut = $vShift[1][0];
							}
							if ($vEndInt >= $vShift[4][1])
								$vDistantInSizeEnd = strtotime($vEndTime) - strtotime($vShift[4][1]);
							else
								$vDistantInSizeEnd = strtotime($vShift[4][0]) - strtotime($vEndTime);
						} else {
							$vEndTrue = false;
							$vShiftOut = $vShift[1][0];
							if ($vEndInt >= $vShift[4][1])
								$vDistantInSizeEnd = strtotime($vEndTime) - strtotime($vShift[4][1]);
							else
								$vDistantInSizeEnd = strtotime($vShift[4][0]) - strtotime($vEndTime);
						}
					}
					//echo "HA:$vEndInt<".$vShift[4][1];
					//echo ": $vShiftOut<br>";
				}
			}
		}
		if ($vShiftIn == '') $vShiftIn = $vShiftLast;
		if ($vShiftOut == '') {
			$vShiftOut = $vShiftLast;
		}
		///Ca cuối xác định công và có tăng ca không					
		$vShift['In'] = $vShiftIn;
		$vShift['Out'] = $vShiftOut;
		return $vShift;
		//Công của công việc 0 không có tăng ca, 1 có tăng ca.
	}
	function LV_GetListTime_Arr($vDateWork, $vArrShift, $vListShiftReceive, $lineprocess = '', $vCheckNT)
	{
		$vNumDayInMonth = GetDayInMonth((int)getyear($vDateWork), (int)getmonth($vDateWork));
		$vDateNext = getyear($vDateWork) . "-" . getmonth($vDateWork) . "-" . Fillnum($vNumDayInMonth, 2);
		$vDatePre = getyear($vDateWork) . "-" . getmonth($vDateWork) . "-01";
		if ($vCheckNT == 1)
			$vsql = "select A.lv001,A.lv002,A.lv003,A.lv004 from tc_lv0012 A where ((month(A.lv002)=month('$vDateWork') and year(A.lv002)=year('$vDateWork')) or A.lv002=DATE_ADD('$vDateNext',INTERVAL 1 DAY) or A.lv002=DATE_ADD('$vDatePre',INTERVAL -1 DAY) ) and A.lv006<>1 and A.lv001 in (" . $lineprocess . ") ORDER BY lv003 ASC";
		else
			$vsql = "select A.lv001,A.lv002,A.lv003,A.lv004 from tc_lv0012 A where (A.lv002='$vDateWork' or A.lv002=DATE_ADD('$vDateWork',INTERVAL 1 DAY) or A.lv002=DATE_ADD('$vDateWork',INTERVAL -1 DAY)) and A.lv006<>1 and A.lv001 in (" . $lineprocess . ") ORDER BY lv003 ASC";
		$vResult = db_query($vsql);
		$i = 1;
		$i1 = 1;
		while ($vrow = db_fetch_array($vResult)) {
			if ((int)$vrow['lv004'] == 0) {
				$this->ArrGioVaoRa[$vrow['lv001']][$vrow['lv002']][$i]['lv003'] = $vrow['lv003'];
				$this->ArrGioVaoRa[$vrow['lv001']][$vrow['lv002']][$i]['lv004'] = $vrow['lv004'];
				$i = $i + 1;
			} else {
				$this->ArrGioVaoRaTC[$vrow['lv001']][$vrow['lv002']][$vrow['lv004']][$i1]['lv003'] = $vrow['lv003'];
				$this->ArrGioVaoRaTC[$vrow['lv001']][$vrow['lv002']][$vrow['lv004']][$i1]['lv004'] = $vrow['lv004'];
				$i1 = $i1 + 1;
			}
		}
	}
	function LV_GetDatePre($vDateWork)
	{
		$vday = getday($vDateWork);
		$vmonth = getmonth($vDateWork);
		$vyear = getyear($vDateWork);
		if ($vday == '01') {
			if ($vmonth == '01')
				return $vPreDay = (getyear($vDateWork) - 1) . "-12-31";
			else
				return $vPreDay = getyear($vDateWork) . "-" . Fillnum($vmonth - 1, 2) . "-" . GetDayInMonth(getyear($vDateWork), $vmonth - 1);
		}
		$vPreDay = getyear($vDateWork) . "-" . $vmonth . "-" . (Fillnum((int)getday($vDateWork) - 1, 2));
		return $vPreDay;
	}
	function LV_GetDateNext($vDateWork)
	{
		if ($this->ArrNextDay[$vDateWork][0] != true) {
			$this->ArrNextDay[$vDateWork][0] = true;
			$this->ArrNextDay[$vDateWork][1] = ADDDATE($vDateWork, 1);
		}
		return $this->ArrNextDay[$vDateWork][1];
	}
	function ConfirmShiftAuto($vArrShift, $vEndTime, $vListShiftReceive)
	{
		//trả về 2 trạng thái
		//Ca làm việc
		//Từ giờ starttime xác định vị trí ca.
		$vShift = array();
		$vStartInt = (int)str_replace(":", "", $vStartTime);
		$vEndInt = (int)str_replace(":", "", $vEndTime);
		$vShiftIn = '';
		$vShiftOut = '';
		$vDistantInSizeStart = 0;
		$vDistantInSizeStartEnd = 0;
		$vStartTrue = false;
		$vEndTrue = false;
		foreach ($vArrShift as $vShift) {
			//echo ',,'.$vListShiftReceive.',',',vinh'.$vShift[1][0].'vinh,='.strpos(',,'.$vListShiftReceive.',',','.$vShift[1][0].',').'<br/>';
			if ($vListShiftReceive == "" || (strpos(',,' . $vListShiftReceive . ',', ',' . $vShift[1][0] . ',') > 0)) {
				if ($vShift[3][1] <= $vShift[4][1]) {
					if ($vEndInt >= $vShift[4][1]) {
						$vEndTrue = true;
						if ($vDistantInSizeEnd == 0) {
							$vShiftOut = $vShift[1][0];
						} else {
							if ($vEndInt >= $vShift[4][1])
								$vDistantInSizeEndNew = $vEndInt - $vShift[4][1];
							else
								$vDistantInSizeEndNew = $vShift[4][1] - $vEndInt;
							//	echo "if(".$vDistantInSizeEndNew."<=".$vDistantInSizeEnd.") ".$vShiftOut."=".$vShift[1][0];
							if ($vDistantInSizeEndNew <= $vDistantInSizeEnd) $vShiftOut = $vShift[1][0];
						}
						if ($vEndInt >= $vShift[4][1])
							$vDistantInSizeEnd = $vEndInt - $vShift[4][1];
						else
							$vDistantInSizeEnd = $vShift[4][1] - $vEndInt;

						if ($vDistantInSizeEnd == 0) return $vShiftOut;
					} else {
						if ($vDistantInSizeEnd == 0) {
							$vShiftOut = $vShift[1][0];
							if ($vEndInt >= $vShift[4][1])
								$vDistantInSizeEnd = $vEndInt - $vShift[4][1];
							else
								$vDistantInSizeEnd = $vShift[4][1] - $vEndInt;
							//return $vShiftOut;
						} else {
							if ($vEndInt > $vShift[4][1])
								$vDistantInSizeEndNew = $vEndInt - $vShift[4][1];
							else
								$vDistantInSizeEndNew = $vShift[4][1] - $vEndInt;
							if ($vDistantInSizeEndNew <= $vDistantInSizeEnd) $vShiftOut = $vShift[1][0];
						}
					}
				} else {

					if ($vDistantInSizeEnd == 0) {
						$vShiftOut = $vShift[1][0];
						//return $vShiftOut;
					} else {
						if ($vEndInt >= $vShift[4][1])
							$vDistantInSizeEndNew = $vEndInt - $vShift[4][1];
						else
							$vDistantInSizeEndNew = $vShift[4][1] - $vEndInt;
						//echo "if($vDistantInSizeEndNew<$vDistantInSizeEnd) $vShiftOut=".$vShift[1][0].";";
						if ($vDistantInSizeEndNew <= $vDistantInSizeEnd) $vShiftOut = $vShift[1][0];
					}
					if ($vEndInt > $vShift[4][1])
						$vDistantInSizeEnd = $vEndInt - $vShift[4][1];
					else
						$vDistantInSizeEnd = $vShift[4][1] - $vEndInt;
					if ($vDistantInSizeEnd == 0) return $vShiftOut;
				}
			}
		}
		///Ca cuối xác định công và có tăng ca không					
		return $vShiftOut;
		//Công của công việc 0 không có tăng ca, 1 có tăng ca.
	}
	function GetTimeListArrAuto($vArrTimeA, $vEmployeeID, $vDateWork, $shiftDay, $shiftYear, &$passday, $vListShiftReceive, $vPreShift, &$arrTime, &$CalShiftAgain = 0)
	{
		$CalShiftAgain = 0;
		$vPass = (int)$vPreShift[1];
		if ($shiftDay != "" && $shiftDay != NULL) {
			$shift_start = (int)str_replace(":", "", $this->ArrShift[0]['IN-' . $shiftDay][3]);
			$shift_end = (int)str_replace(":", "", $this->ArrShift[0]['OUT-' . $shiftDay][4]);
			if ($passday != 2 && $passday != -1) $passday = ($shift_start > $shift_end) ? 1 : 0;
		} else if ($shiftYear != "" && $shiftYear != NULL) {
			$shift_start = (int)str_replace(":", "", $this->ArrShift[0]['IN-' . $shiftYear][3]);
			$shift_end = (int)str_replace(":", "", $this->ArrShift[0]['OUT-' . $shiftYear][4]);
			if ($passday != 2 && $passday != -1) $passday = ($shift_start > $shift_end) ? 1 : 0;
		}
		$strReturn = array();
		if ($passday != 1) {
			if ($passday == 2) {
				$count = $vArrTimeA[0][0];
				if ($count > 2) $count = 2;
				$i = 1;
				$j = 1;
				foreach ($vArrTimeA[0] as $vrow) {
					if ($i == 1) {
						if (($i == 1)) {
							$arrTime[0] = $vrow[0];
							$strReturn[0] = (int)str_replace(":", "", $vrow[0]);
						} else {
							$arrTime[1] = $vrow[0];
							$strReturn[1] = (int)str_replace(":", "", $vrow[0]);
						}
					} else {
						if (($j == ($count))) {
							$arrTime[1] = $vrow[0];
							$strReturn[1] = (int)str_replace(":", "", $vrow[0]);
						}
					}
					$j++;
					$i++;
				}
			} elseif ($passday == -1) {
				$count = count($vArrTimeA[0]);
				$i = 1;
				if ($count > 0) {

					foreach ($vArrTimeA[0] as $vrow) {
						if (($i == 1)) {
							$arrTime[0] = $vrow[0];
							$strReturn[0] = (int)str_replace(":", "", $vrow[0]);
						} else {
							$arrTime[1] = $vrow[0];
							$strReturn[1] = (int)str_replace(":", "", $vrow[0]);
						}

						$i++;
					}
				}
			} else {
				$count = count($this->ArrGioVaoRa[$vEmployeeID][$vDateWork]);
				if ($this->ArrGioVaoRa[$vEmployeeID][$vDateWork] != NULL) {
					$i = 1;
					$j = 1;
					$vbo = 0;
					foreach ($this->ArrGioVaoRa[$vEmployeeID][$vDateWork] as $vrow) {
						if ($vPass == 1) {
							$vShiftOut = $this->ConfirmShiftAuto($this->ArrShift, $vrow['lv003'], $vListShiftReceive);
							//echo $vrow['lv003']."->". $vPreShift[0]."!=".$vShiftOut."<br/>";
							if ($vPreShift[0] != $vShiftOut) {
								if (($i == 1)) {
									$arrTime[0] = $vrow['lv003'];
									$strReturn[0] = (int)str_replace(":", "", $vrow['lv003']);
								} else if ($j == $count) {
									$strReturn[1] = (int)str_replace(":", "", $vrow['lv003']);
									$arrTime[1] = $vrow['lv003'];
								}
								$i++;
							}
							$CalShiftAgain = 1;
							//Nhận lại ca
						} else {
							if (($i == 1)) {
								$arrTime[0] = $vrow['lv003'];
								$strReturn[0] = (int)str_replace(":", "", $vrow['lv003']);
							} else if ($j == $count) {
								$strReturn[1] = (int)str_replace(":", "", $vrow['lv003']);
								$arrTime[1] = $vrow['lv003'];
							}
							$i++;
							////Tạm tắt/////
							/*
							if($this->LV_GetSecond($arrTime[$i-2+$vbo],$vrow['lv003'])>900 || $i==1)
								{
									if(($i==1))
									{
										$arrTime[0]=$vrow['lv003'];
										$strReturn[0]=(int)str_replace(":","",$vrow['lv003']);
									}
									else
									{
										$arrTime[1]=$vrow['lv003'];
										$strReturn[1]=(int)str_replace(":","",$vrow['lv003']);
									}
									$i++;
								}
								elseif($this->LV_GetSecond($arrTime[$i-2+$vbo],$vrow['lv003'])>900 )
								{
									if(($j==$count))
									{
										$arrTime[1]=$vrow['lv003'];
										$strReturn[1]=(int)str_replace(":","",$vrow['lv003']);
									}
									$vbo++;
								}
									
							*/
						}
						$j++;
					}
				}
			}
		} else {
			$count = count($this->ArrGioVaoRa[$vEmployeeID][$vDateWork]);
			if ($this->ArrGioVaoRa[$vEmployeeID][$vDateWork] != NULL) {
				$i = 1;
				$vCount = 0;
				foreach ($this->ArrGioVaoRa[$vEmployeeID][$vDateWork] as $vrow) {
					$arrTime[0] = $vrow['lv003'];
					$strReturn[0] = (int)str_replace(":", "", $vrow['lv003']);
				}
			}
			$vDateWorkNext = $this->LV_GetDateNext($vDateWork);
			$count = count($this->ArrGioVaoRa[$vEmployeeID][$vDateWorkNext]);
			if ($this->ArrGioVaoRa[$vEmployeeID][$vDateWorkNext] != NULL) {
				$i = 1;
				$vCount = 0;
				foreach ($this->ArrGioVaoRa[$vEmployeeID][$vDateWorkNext] as $vrow) {
					//Xác định giờ thuộc ca C không;
					$vShiftOut = $this->ConfirmShiftAuto($this->ArrShift, $vrow['lv003'], $vListShiftReceive);
					//echo $vrow['lv003']."->"."$vShiftOut==$shiftDay";
					if ($vShiftOut == $shiftDay || (substr($shiftDay, 0, 3) == 'CaC' && substr($vShiftOut, 0, 3) == 'CaD') || (substr($shiftDay, 0, 3) == 'CaD' && substr($vShiftOut, 0, 3) == 'CaC')) {
						if ($strReturn[1] != NULL && $strReturn[1] != '') {
							if (($this->LV_GetSecond($strReturn[1], $vrow['lv003']) > 900)) {
								$strReturn[1] = (int)str_replace(":", "", $vrow['lv003']);
								$arrTime[1] = $vrow['lv003'];
							}
						} else {
							$strReturn[1] = (int)str_replace(":", "", $vrow['lv003']);
							$arrTime[1] = $vrow['lv003'];
						}
					}
				}
			}
		}

		return $strReturn;
	}
	function ConfirmShiftOutDay($vArrShift, $vEndTime, $vListShiftReceive)
	{
		//trả về 2 trạng thái
		//Ca làm việc
		//Từ giờ starttime xác định vị trí ca.
		$vShift = array();
		$vStartInt = (int)str_replace(":", "", $vStartTime);
		$vEndInt = (int)str_replace(":", "", $vEndTime);
		$vShiftIn = '';
		$vShiftOut = '';
		$vDistantInSizeStart = 0;
		$vDistantInSizeStartEnd = 0;
		$vStartTrue = false;
		$vEndTrue = false;
		foreach ($vArrShift as $vShift) {

			if ($vListShiftReceive == "" || (strpos(',,' . $vListShiftReceive . ',', ',' . $vShift[1][0] . ',') > 0)) {
				//echo ',,'.$vListShiftReceive.',',',vinh'.$vShift[1][0].'vinh,='.strpos(',,'.$vListShiftReceive.',',','.$vShift[1][0].',').'<br/>';
				//echo "vinhne:$vDistantInSizeEnd<br/>";
				if ($vShift[3][1] <= $vShift[4][1]) {

					if ($vEndInt >= $vShift[4][1]) {
						$vEndTrue = true;
						/*if($vDistantInSizeEnd==0)
							{
								$vShiftOut=$vShift[1][0];
							}
							else*/ {
							if ($vEndInt >= $vShift[4][1])
								$vDistantInSizeEndNew = $vEndInt - $vShift[4][1];
							else
								$vDistantInSizeEndNew = $vShift[4][1] - $vEndInt;
							if ($vDistantInSizeEndNew <= $vDistantInSizeEnd) $vShiftOut = $vShift[1][0];
						}
						if ($vEndInt >= $vShift[4][1])
							$vDistantInSizeEnd = $vEndInt - $vShift[4][1];
						else
							$vDistantInSizeEnd = $vShift[4][1] - $vEndInt;
					} else {
						if ($vDistantInSizeEnd == 0) {
							$vShiftOut = $vShift[1][0];
							if ($vEndInt >= $vShift[4][1])
								$vDistantInSizeEnd = $vEndInt - $vShift[4][1];
							else
								$vDistantInSizeEnd = $vShift[4][1] - $vEndInt;
						} else {
							if ($vEndInt > $vShift[4][1])
								$vDistantInSizeEndNew = $vEndInt - $vShift[4][1];
							else
								$vDistantInSizeEndNew = $vShift[4][1] - $vEndInt;
							//echo "if($vDistantInSizeEndNew<$vDistantInSizeEnd) $vShiftOut=".$vShift[1][0].";<br/>";
							if ($vDistantInSizeEndNew <= $vDistantInSizeEnd) $vShiftOut = $vShift[1][0];
						}
					}
				} else {

					/*if($vDistantInSizeEnd==0)
							{
								$vShiftOut=$vShift[1][0];
							}
							else*/ {
						if ($vEndInt >= $vShift[4][1])
							$vDistantInSizeEndNew = $vEndInt - $vShift[4][1];
						else
							$vDistantInSizeEndNew = $vShift[4][1] - $vEndInt;
						//echo "if($vDistantInSizeEndNew<$vDistantInSizeEnd) $vShiftOut=".$vShift[1][0].";<br/>";
						if ($vDistantInSizeEndNew <= $vDistantInSizeEnd) $vShiftOut = $vShift[1][0];
					}
					if ($vEndInt > $vShift[4][1])
						$vDistantInSizeEnd = $vEndInt - $vShift[4][1];
					else
						$vDistantInSizeEnd = $vShift[4][1] - $vEndInt;
				}
			}
		}
		///Ca cuối xác định công và có tăng ca không					
		return $vShiftOut;
		//Công của công việc 0 không có tăng ca, 1 có tăng ca.
	}
	function ConfirmShiftOutDay11($vArrShift, $vEndTime, $vListShiftReceive)
	{
		//trả về 2 trạng thái
		//Ca làm việc
		//Từ giờ starttime xác định vị trí ca.
		$vShift = array();
		$vStartInt = (int)str_replace(":", "", $vStartTime);
		$vEndInt = (int)str_replace(":", "", $vEndTime);
		$vShiftIn = '';
		$vShiftOut = '';
		$vDistantInSizeStart = 0;
		$vDistantInSizeStartEnd = 0;
		$vStartTrue = false;
		$vEndTrue = false;
		foreach ($vArrShift as $vShift) {
			//echo ',,'.$vListShiftReceive.',',',vinh'.$vShift[1][0].'vinh,='.strpos(',,'.$vListShiftReceive.',',','.$vShift[1][0].',').'<br/>';
			if ($vListShiftReceive == "" || (strpos(',,' . $vListShiftReceive . ',', ',' . $vShift[1][0] . ',') > 0)) {
				if ($vShift[3][1] <= $vShift[4][1]) {
					if ($vEndInt >= $vShift[4][1]) {
						$vEndTrue = true;
						if ($vDistantInSizeEnd == 0) {
							$vShiftOut = $vShift[1][0];
						} else {
							if ($vEndInt >= $vShift[4][1])
								$vDistantInSizeEndNew = $vEndInt - $vShift[4][1];
							else
								$vDistantInSizeEndNew = $vShift[4][1] - $vEndInt;
							if ($vDistantInSizeEndNew <= $vDistantInSizeEnd) $vShiftOut = $vShift[1][0];
						}
						if ($vEndInt >= $vShift[4][1])
							$vDistantInSizeEnd = $vEndInt - $vShift[4][1];
						else
							$vDistantInSizeEnd = $vShift[4][1] - $vEndInt;
					} else {
						if ($vDistantInSizeEnd == 0) {
							$vShiftOut = $vShift[1][0];
							if ($vEndInt >= $vShift[4][1])
								$vDistantInSizeEnd = $vEndInt - $vShift[4][1];
							else
								$vDistantInSizeEnd = $vShift[4][1] - $vEndInt;
						} else {
							if ($vEndInt > $vShift[4][1])
								$vDistantInSizeEndNew = $vEndInt - $vShift[4][1];
							else
								$vDistantInSizeEndNew = $vShift[4][1] - $vEndInt;
							if ($vDistantInSizeEndNew <= $vDistantInSizeEnd) $vShiftOut = $vShift[1][0];
						}
					}
				} else {
					if ($vDistantInSizeEnd == 0) {
						$vShiftOut = $vShift[1][0];
					} else {
						if ($vEndInt >= $vShift[4][1])
							$vDistantInSizeEndNew = $vEndInt - $vShift[4][1];
						else
							$vDistantInSizeEndNew = $vShift[4][1] - $vEndInt;
						//echo "if($vDistantInSizeEndNew<$vDistantInSizeEnd) $vShiftOut=".$vShift[1][0].";";
						if ($vDistantInSizeEndNew <= $vDistantInSizeEnd) $vShiftOut = $vShift[1][0];
					}
					if ($vEndInt > $vShift[4][1])
						$vDistantInSizeEnd = $vEndInt - $vShift[4][1];
					else
						$vDistantInSizeEnd = $vShift[4][1] - $vEndInt;
				}
			}
		}
		///Ca cuối xác định công và có tăng ca không					
		return $vShiftOut;
		//Công của công việc 0 không có tăng ca, 1 có tăng ca.
	}
	function LV_GetListTimeATC($vEmployeeID, $vDateWork)
	{
		$vReturnWorkTC = array();
		$vDateWorkNext = $this->LV_GetDateNext($vDateWork);
		$j = 0;
		$i = 0;
		for ($zs = 1; $zs <= 4; $zs++) {
			if ($this->ArrGioVaoRaTC[$vEmployeeID][$vDateWork][$zs] != NULL) {
				if ($vReturnWorkTC[$zs] == NULL) $vReturnWorkTC[$zs] = '00:00:00';
				if ($zs == 3) {
					$i = 0;
					if (count($this->ArrGioVaoRaTC[$vEmployeeID][$vDateWork][$zs]) == 2) {
						foreach ($this->ArrGioVaoRaTC[$vEmployeeID][$vDateWork][$zs] as $vrow) {
							if ($i == 0) {
								$vGioDau = $vrow['lv003'];
							} else {
								$vGioCuoi = $vrow['lv003'];
							}
							$i++;
							if ($i == 2) {
								if ($vGioDau >= '22:00:00' && $vGioCuoi >= '22:00:00') {
									$vReturnWorkTC[$zs] = TIMEADD($vReturnWorkTC[$zs], TIMEDIFF($vGioCuoi, $vGioDau));
								} else {
									if ($vGioCuoi >= '22:00:00') {
										$vReturnWorkTC[$zs] = TIMEADD($vReturnWorkTC[$zs], TIMEDIFF('24:00:00', $vGioCuoi));
										$vReturnWorkTC[$zs] = TIMEADD($vReturnWorkTC[$zs], $vGioDau);
									} else {
										$vReturnWorkTC[$zs] = TIMEADD($vReturnWorkTC[$zs], TIMEDIFF('24:00:00', $vGioDau));
										$vReturnWorkTC[$zs] = TIMEADD($vReturnWorkTC[$zs], $vGioCuoi);
									}
								}
								$i = 0;
							}
						}
					}
				} else {
					$i = 0;
					if ($this->ArrGioVaoRaTC[$vEmployeeID][$vDateWork][$zs] > 1) {

						foreach ($this->ArrGioVaoRaTC[$vEmployeeID][$vDateWork][$zs] as $vrow) {
							if ($i == 0) {
								$vGioDau = $vrow['lv003'];
							} else {
								$vGioCuoi = $vrow['lv003'];
							}
							$i++;
							if ($i == 2) {
								$vReturnWorkTC[$zs] = TIMEADD($vReturnWorkTC[$zs], TIMEDIFF($vGioCuoi, $vGioDau));
								$i = 0;
							}
						}
					}
				}
			}
		}
		return $vReturnWorkTC;
	}
	function LV_GetListTimeA_Kho($vEmployeeID, $vDateWork, $vArrShift, $vListShiftReceive, $vShiftID, $vopt = 0)
	{
		$vArrReturn = array();
		$vArrReturn[99][0][0] = 0;
		$shift_start = (int)str_replace(":", "", $vArrShift[0]['IN-' . $vShiftID][3]);
		$shift_end = (int)str_replace(":", "", $vArrShift[0]['OUT-' . $vShiftID][4]);
		if ($vopt == 1) {
			$vArrReturn[99][0][0] = ($shift_start > $shift_end) ? 1 : 0;
		}
		$i = 0;
		$vTruoc = false;
		$vLan = 0;
		$vDateWorkPre = $this->LV_GetDatePre($vDateWork);
		if ($this->ArrGioVaoRa[$vEmployeeID][$vDateWorkPre] != NULL) {
			foreach ($this->ArrGioVaoRa[$vEmployeeID][$vDateWorkPre] as $vrow) {

				$vTruoc = true;

				if ($this->LV_GetSecond($vArrReturn[2][$i - 1][0], $vrow['lv003']) > 600 || $i == 0) {
					$vArrReturn[2][$i][0] = $vrow['lv003'];
					$vArrReturn[2][$i][1] = $vrow['lv004'];
					$i++;
				}
			}
		}
		if ($i == 1) $vLan++;

		//Lay gio ca ngoai
		$j = 0;
		$i = 0;
		$vDateWorkNext = $this->LV_GetDateNext($vDateWork);
		if ($this->ArrGioVaoRa[$vEmployeeID][$vDateWorkNext] != NULL) {
			foreach ($this->ArrGioVaoRa[$vEmployeeID][$vDateWorkNext] as $vrow) {
				if ($this->LV_GetSecond($vArrReturn[1][$i - 1][0], $vrow['lv003']) > 600 || $i == 0) {
					$vArrReturn[1][$i][0] = $vrow['lv003'];
					$vArrReturn[1][$i][1] = $vrow['lv004'];
					$i++;
				}
			}
		}
		$j = 0;
		$i = 0;
		$solan = 0;
		if ($this->ArrGioVaoRa[$vEmployeeID][$vDateWork] != NULL) {
			foreach ($this->ArrGioVaoRa[$vEmployeeID][$vDateWork] as $vrow) {
				if ($this->LV_GetSecond($vArrReturn[0][$i - 1][0], $vrow['lv003']) > 600 || $i == 0) {
					$vArrReturn[0][$i][0] = $vrow['lv003'];
					$vArrReturn[0][$i][1] = $vrow['lv004'];
					//if($i==0) $i=1;
					$i++;
					$solan++;
				}
				/*
			if($this->LV_GetSecond($vArrReturn[0][$j-1][0],$vrow['lv003'])>600 || $i==0)
			{
					if($vopt!=1)
					{
						$vArrReturn[0][$j][0]=$vrow['lv003'];
						$vArrReturn[0][$j][1]=$vrow['lv004'];
						$j++;
						
					}
					else
					{
						if($shift_start>(str_replace(":","",$vrow['lv003'])+40000))
						{
							$i--;
						}
						else
						{
							$vArrReturn[0][$j][0]=$vrow['lv003'];
							$vArrReturn[0][$j][1]=$vrow['lv004'];
							$j++;
						}
					}

				$i++;
			}*/
			}
		}
		if ($vArrReturn[99][0][0] != 1) {
			if ($solan == 1) {
				//if($shift_end>=63000)
				{
					if ($this->ArrGioVaoRa[$vEmployeeID][$vDateWorkNext] != NULL) {
						foreach ($this->ArrGioVaoRa[$vEmployeeID][$vDateWorkNext] as $vrow) {
							if (str_replace(":", "", $vrow['lv003']) < 30000) {
								$vArrReturn[0][1][0] = $vrow['lv003'];
								$vArrReturn[0][1][1] = $vrow['lv004'];
								$vArrReturn[99][0][0] = 1;
								break;
							}
						}
					}
				}
			} elseif ($i == 2) {
				if ($shift_end >= 63000) {
					if (str_replace(":", "", $vArrReturn[0][1][0]) < 100000) {
						if ($this->ArrGioVaoRa[$vEmployeeID][$vDateWorkNext] != NULL) {
							foreach ($this->ArrGioVaoRa[$vEmployeeID][$vDateWorkNext] as $vrow) {
								if (str_replace(":", "", $vrow['lv003']) < 30000) {
									$vArrReturn[0][1][0] = $vrow['lv003'];
									$vArrReturn[0][1][1] = $vrow['lv004'];
									$vArrReturn[99][0][0] = 1;
									break;
								}
							}
						}
					}
				}
			}
		}
		return $vArrReturn;
	}
	function LV_GetListTimeA_Kho_1($vEmployeeID, $vDateWork, $vArrShift, $vListShiftReceive, $vPreShift, $vShiftID, $vopt = 0, $DonXinPhep)
	{
		$vArrReturn = array();
		$vArrReturn[99][0][0] = 0;
		$shift_start = (int)str_replace(":", "", $vArrShift[0]['IN-' . $vShiftID][3]);
		$shift_end = (int)str_replace(":", "", $vArrShift[0]['OUT-' . $vShiftID][4]);
		if ($vopt == 1) {
			$vArrReturn[99][0][0] = ($shift_start > $shift_end) ? 1 : 0;
		}
		$i = 0;
		$vTruoc = false;
		$vLan = 0;
		$vPass = $vPreShift[1];
		$vDateWorkPre = $this->LV_GetDatePre($vDateWork);
		if ($vPass != 3) {
			//if(!isset($this->ArrGioVaoRa[$vEmployeeID][$vDateWorkPre])) $this->ArrGioVaoRa[$vEmployeeID][$vDateWorkPre]=null;
			if ($this->ArrGioVaoRa[$vEmployeeID][$vDateWorkPre] != NULL) {
				foreach ($this->ArrGioVaoRa[$vEmployeeID][$vDateWorkPre] as $vrow) {
					$vTruoc = true;
					if ($this->LV_GetSecond($vArrReturn[2][$i - 1][0], $vrow['lv003']) > 900 || $i == 0) {
						$vArrReturn[2][$i][0] = $vrow['lv003'];
						$vArrReturn[2][$i][1] = $vrow['lv004'];
						$i++;
					}
				}
			} else {
				$vPhepBu = $DonXinPhep->ArrTimeAddInOut[$vEmployeeID][$vDateWorkPre];
				if ($vPhepBu != NULL) {
					foreach ($vPhepBu as $vPhep) {
						$vArrReturn[2][$i][0] = $vPhep;
						$vArrReturn[2][$i][1] = 0;
						$i++;
					}
				}
			}
		}
		if ($vPass == -1) {
			if ($i == 2) {
				$vShift = $this->ConfirmShift($vArrShift, $vArrReturn[2][1][0], '', $vListShiftReceive);
				if (substr($vShift['In'], 0, 3) == 'CaC' || substr($vShift['In'], 0, 4) == 'CaD')
					$vPass = 2;
				else
					$vPass = 0;
			} elseif ($i > 2) {
				$vPass = 0;
			} else
				$vPass = 0;
		}
		if ($vPass == 2) {
			$vDem2 = $i - 1;
		}
		if ($i == 1) $vLan++;

		//Lay gio ca ngoai
		$j = 0;
		$i = 0;
		$vDateWorkNext = $this->LV_GetDateNext($vDateWork);
		//if(!isset($this->ArrGioVaoRa[$vEmployeeID][$vDateWorkNext])) $this->ArrGioVaoRa[$vEmployeeID][$vDateWorkNext]=NULL;
		if ($this->ArrGioVaoRa[$vEmployeeID][$vDateWorkNext] != NULL) {
			foreach ($this->ArrGioVaoRa[$vEmployeeID][$vDateWorkNext] as $vrow) {
				if ($this->LV_GetSecond($vArrReturn[1][$i - 1][0], $vrow['lv003']) > 900 || $i == 0) {
					$vArrReturn[1][$i][0] = $vrow['lv003'];
					$vArrReturn[1][$i][1] = $vrow['lv004'];
					$i++;
				}
			}
		} else {
			$vPhepBu = $DonXinPhep->ArrTimeAddInOut[$vEmployeeID][$vDateWorkNext];
			if ($vPhepBu != NULL) {
				foreach ($vPhepBu as $vPhep) {
					$vArrReturn[1][$i][0] = $vPhep;
					$vArrReturn[1][$i][1] = 0;
					$i++;
				}
			}
		}
		$j = 0;
		$i = 0;
		$vf = 0;
		$vt = 0;
		//if(!isset($this->ArrGioVaoRa[$vEmployeeID][$vDateWork])) $this->ArrGioVaoRa[$vEmployeeID][$vDateWork]=NULL;
		if ($this->ArrGioVaoRa[$vEmployeeID][$vDateWork] != NULL) {
			if ($vPass == 2) {
				$vArrReturn[0][$vt][0] = $vArrReturn[2][$vDem2][0];
				$vArrReturn[0][$vt][1] = $vArrReturn[2][$vDem2][1];
				$vt++;
			}
			foreach ($this->ArrGioVaoRa[$vEmployeeID][$vDateWork] as $vrow) {
				if ($this->LV_GetSecond($vArrReturn[0][$j - 1][0], $vrow['lv003']) > 900 || $i == 0) {
					$vArrReturn[0][$j][0] = $vrow['lv003'];
					$vArrReturn[0][$j][1] = $vrow['lv004'];
					$j++;
					$i++;
					if ($vPass == 2) {
						$vArrReturn[0][$vt][0] = $vrow['lv003'];
						$vArrReturn[0][$vt][1] = $vrow['lv004'];
						$vt++;
						break;
					} else {
						if ($vPass == 1 && $vf == 0) {
							//echo $vDateWork;
							$vShiftOut = $this->ConfirmShiftOutDay($vArrShift, $vrow['lv003'], $vListShiftReceive);
							//echo $vShiftOut."==".$vPreShift[0];
							//echo "<br/>";
							if ($vShiftOut != $vPreShift[0]) $vf = 1;
						}
						if (($vPass == 1 && $vf > 0) || ($vPass == 0)) {
							$vArrReturn[0][$vt][0] = $vrow['lv003'];
							$vArrReturn[0][$vt][1] = $vrow['lv004'];
							$vt++;
						}
						$vf++;
					}
				}
			}
		} else {
			//Cần xem lại xử lý
			$vPhepBu = $DonXinPhep->ArrTimeAddInOut[$vEmployeeID][$vDateWork];
			if ($vPhepBu != NULL) {
				foreach ($vPhepBu as $vPhep) {
					$vArrReturn[0][$i][0] = $vPhep;
					$vArrReturn[0][$i][1] = 0;
					$i++;
				}
			}
		}
		if ($vPass == 2) {
			$vArrReturn[99][0][0] = -1;
		} else {
			if ($i == 1) {
				$vCount2 = count($vArrReturn[2]);
				if ($vCount2 < 3) {
					// Có 3 trường hơp
					//vao Ca  dem
					$vvalue = $this->CheckInShiftFirst($vArrReturn, $vArrShift, $vListShiftReceive);
					if ($vvalue == 1) {
						if ($vCount2 == 0) {
							$vArrReturn[99][0][0] = 1;
							//la gio ra chieu
							$vStartTime = $vArrReturn[1][0][0];
							$vEndTime = $vArrReturn[1][$vCount2 - 1][0];
							$vShift = $this->ConfirmShift($vArrShift, $vStartTime, $vEndTime, $vListShiftReceive);
							if (substr($vShift['In'], 0, 3) != 'CaA' && substr($vShift['In'], 0, 4) != 'CaHC') $vArrReturn[99][0][0] = 0;
						} else {
							$vStartTime = $vArrReturn[2][0][0];
							$vEndTime = $vArrReturn[2][$vCount2 - 1][0];
							$vShift = $this->ConfirmShift($vArrShift, $vStartTime, $vEndTime, $vListShiftReceive);
							if (substr($vShift['In'], 0, 3) == 'CaA' || substr($vShift['In'], 0, 4) == 'CaHC') $vArrReturn[99][0][0] = 1;
						}
					} else {
						if ($shift_end >= 140000) {
							//if(!isset($this->ArrGioVaoRa[$vEmployeeID][$vDateWorkNext])) $this->ArrGioVaoRa[$vEmployeeID][$vDateWorkNext]=NULL;
							if ($this->ArrGioVaoRa[$vEmployeeID][$vDateWorkNext] != NULL) {
								foreach ($this->ArrGioVaoRa[$vEmployeeID][$vDateWorkNext] as $vrow) {
									if (str_replace(":", "", $vrow['lv003']) < 20000) {
										$vArrReturn[0][1][0] = $vrow['lv003'];
										$vArrReturn[0][1][1] = $vrow['lv004'];
										$vArrReturn[99][0][0] = 1;
										break;
									}
								}
							}
						}
						//Ra ca dem
						//thieu gio vao ra	
						$vArrReturn[99][0][0] = 0;
					}
				} else {

					if ($shift_end >= 140000) {
						//if(!isset($this->ArrGioVaoRa[$vEmployeeID][$vDateWorkNext])) $this->ArrGioVaoRa[$vEmployeeID][$vDateWorkNext]=NULL;
						if ($this->ArrGioVaoRa[$vEmployeeID][$vDateWorkNext] != NULL) {
							foreach ($this->ArrGioVaoRa[$vEmployeeID][$vDateWorkNext] as $vrow) {
								if (str_replace(":", "", $vrow['lv003']) < 20000) {
									$vArrReturn[0][1][0] = $vrow['lv003'];
									$vArrReturn[0][1][1] = $vrow['lv004'];
									$vArrReturn[99][0][0] = 1;
									break;
								}
							}
						}
					}
				}
			} else if ($i == 2) {
				$vvalue = $this->CheckInShiftFirst($vArrReturn, $vArrShift, $vListShiftReceive);
				if ($vvalue == 1) {
					$vArrReturn[99][0][0] = 1;
				}
			} else if ($i == 3) {
				if (strpos(',,' . $vListShiftReceive . ',', ',CaD,') > 0) {
					$vCount = count($vArrReturn[0]);
					$vArrReturn[99][0][0] = 0;
					$vStartTime = $vArrReturn[0][0][0];
					$vEndTime = $vArrReturn[0][$vCount - 1][0];
					$vShift = $this->ConfirmShift($vArrShift, $vStartTime, $vEndTime, $vListShiftReceive);
					if (substr($vShift['In'], 0, 3) == 'CaA' || substr($vShift['In'], 0, 4) == 'CaHC') {
						$vShift = $this->ConfirmShift($vArrShift, $vEndTime, $vEndTime, $vListShiftReceive);
						if (substr($vShift['In'], 0, 3) == 'CaC' || substr($vShift['In'], 0, 4) == 'CaD') $vArrReturn[99][0][0] = 2;
					}
				}
			}
		}
		return $vArrReturn;
	}
	function LV_GetListTimeA($vEmployeeID, $vDateWork, $vArrShift, $vListShiftReceive, $vShiftID, $vopt = 0)
	{
		$vArrReturn = array();
		$vArrReturn[99][0][0] = 0;
		$shift_start = (int)str_replace(":", "", $vArrShift[0]['IN-' . $vShiftID][3]);
		$shift_end = (int)str_replace(":", "", $vArrShift[0]['OUT-' . $vShiftID][4]);
		if ($vopt == 1) {
			$vArrReturn[99][0][0] = ($shift_start > $shift_end) ? 1 : 0;
		}
		$i = 0;
		$vTruoc = false;
		$vLan = 0;
		$vDateWorkPre = $this->LV_GetDatePre($vDateWork);
		if ($this->ArrGioVaoRa[$vEmployeeID][$vDateWorkPre] != NULL) {
			foreach ($this->ArrGioVaoRa[$vEmployeeID][$vDateWorkPre] as $vrow) {

				$vTruoc = true;

				if ($this->LV_GetSecond($vArrReturn[2][$i - 1][0], $vrow['lv003']) > 600 || $i == 0) {
					$vArrReturn[2][$i][0] = $vrow['lv003'];
					$vArrReturn[2][$i][1] = $vrow['lv004'];
					$i++;
				}
			}
		}
		if ($i == 1) $vLan++;

		//Lay gio ca ngoai
		$j = 0;
		$i = 0;
		$vDateWorkNext = $this->LV_GetDateNext($vDateWork);
		if ($this->ArrGioVaoRa[$vEmployeeID][$vDateWorkNext] != NULL) {
			foreach ($this->ArrGioVaoRa[$vEmployeeID][$vDateWorkNext] as $vrow) {
				if ($this->LV_GetSecond($vArrReturn[1][$i - 1][0], $vrow['lv003']) > 600 || $i == 0) {
					$vArrReturn[1][$i][0] = $vrow['lv003'];
					$vArrReturn[1][$i][1] = $vrow['lv004'];
					$i++;
				}
			}
		}
		$j = 0;
		$i = 0;
		$solan = 0;
		if ($this->ArrGioVaoRa[$vEmployeeID][$vDateWork] != NULL) {
			foreach ($this->ArrGioVaoRa[$vEmployeeID][$vDateWork] as $vrow) {
				if ($this->LV_GetSecond($vArrReturn[0][$i - 1][0], $vrow['lv003']) > 600 || $i == 0) {
					$vArrReturn[0][$i][0] = $vrow['lv003'];
					$vArrReturn[0][$i][1] = $vrow['lv004'];
					if ($i == 0) $i = 1;
					$solan++;
				}
				/*
			if($this->LV_GetSecond($vArrReturn[0][$j-1][0],$vrow['lv003'])>600 || $i==0)
			{
					if($vopt!=1)
					{
						$vArrReturn[0][$j][0]=$vrow['lv003'];
						$vArrReturn[0][$j][1]=$vrow['lv004'];
						$j++;
						
					}
					else
					{
						if($shift_start>(str_replace(":","",$vrow['lv003'])+40000))
						{
							$i--;
						}
						else
						{
							$vArrReturn[0][$j][0]=$vrow['lv003'];
							$vArrReturn[0][$j][1]=$vrow['lv004'];
							$j++;
						}
					}

				$i++;
			}*/
			}
		}
		if ($vArrReturn[99][0][0] != 1) {
			if ($solan == 1) {
				//if($shift_end>=63000)
				{
					if ($this->ArrGioVaoRa[$vEmployeeID][$vDateWorkNext] != NULL) {
						foreach ($this->ArrGioVaoRa[$vEmployeeID][$vDateWorkNext] as $vrow) {
							if (str_replace(":", "", $vrow['lv003']) < 30000) {
								$vArrReturn[0][1][0] = $vrow['lv003'];
								$vArrReturn[0][1][1] = $vrow['lv004'];
								$vArrReturn[99][0][0] = 1;
								break;
							}
						}
					}
				}
			} elseif ($i == 2) {
				if ($shift_end >= 63000) {
					if (str_replace(":", "", $vArrReturn[0][1][0]) < 100000) {
						if ($this->ArrGioVaoRa[$vEmployeeID][$vDateWorkNext] != NULL) {
							foreach ($this->ArrGioVaoRa[$vEmployeeID][$vDateWorkNext] as $vrow) {
								if (str_replace(":", "", $vrow['lv003']) < 30000) {
									$vArrReturn[0][1][0] = $vrow['lv003'];
									$vArrReturn[0][1][1] = $vrow['lv004'];
									$vArrReturn[99][0][0] = 1;
									break;
								}
							}
						}
					}
				}
			}
		}


		return $vArrReturn;
	}
	function LV_GetListTime($vEmployeeID, $vDateWork)
	{
		$vArrReturn = array();
		$i = 0;
		$vsql = "select lv003,lv004 from tc_lv0012 where lv002='$vDateWork' and lv001='$vEmployeeID' and lv006<>1  ORDER BY lv003 ASC";
		$vArrReturn[99][0][0] = 0;
		$vResult = db_query($vsql);
		while ($vrow = db_fetch_array($vResult)) {
			if ($this->LV_GetSecond($vArrReturn[0][$i - 1][0], $vrow['lv003']) > 90 || $i == 0) {
				$vArrReturn[0][$i][0] = $vrow['lv003'];
				$vArrReturn[0][$i][1] = $vrow['lv004'];
				$i++;
			}
		}
		$vsql = "select lv003,lv004 from tc_lv0012 where lv002=DATE_ADD('$vDateWork',INTERVAL 1 DAY) and lv001='$vEmployeeID' and lv006<>1   ORDER BY lv003 ASC";
		$vResult = db_query($vsql);
		$i = 0;
		while ($vrow = db_fetch_array($vResult)) {
			if ($this->LV_GetSecond($vArrReturn[1][$i - 1][0], $vrow['lv003']) > 90 || $i == 0) {
				//$vArrReturn[99][0][0]=1;
				$vArrReturn[1][$i][0] = $vrow['lv003'];
				$vArrReturn[1][$i][1] = $vrow['lv004'];
				$i++;
			}
		}

		$i = 0;
		$vsql = "select lv003,lv004 from tc_lv0012 where lv002=DATE_ADD('$vDateWork',INTERVAL -1 DAY) and lv001='$vEmployeeID' and lv006<>1 ORDER BY lv003 ASC";
		$vResult = db_query($vsql);
		while ($vrow = db_fetch_array($vResult)) {
			if ($this->LV_GetSecond($vArrReturn[2][$i - 1][0], $vrow['lv003']) > 90 || $i == 0) {
				$vArrReturn[2][$i][0] = $vrow['lv003'];
				$vArrReturn[2][$i][1] = $vrow['lv004'];
				$i++;
			}
		}
		return $vArrReturn;
	}
	function LV_GetSecond($lvStartTime, $lvEndTime)
	{
		return $this->TIMES_TO_SEC(TIMEDIFF($lvEndTime, $lvStartTime));
	}
	function TIMES_TO_SEC($vTimeStart)
	{
		$vSStart = (int)substr($vTimeStart, strlen($vTimeStart) - 6 + 4, 2);
		$vMStart = (int)substr($vTimeStart, strlen($vTimeStart) - 6 + 1, 2);
		$vHStart = (int)substr($vTimeStart, 0, strlen($vTimeStart) - 6);
		return (float)$vSStart + (float)$vMStart * 60 + (float)$vHStart * 3600;
	}
	/*function LV_GetSecond($lvStartTime,$lvEndTime)
	{
		$lvsql="select TIME_TO_SEC(TIMEDIFF('$lvEndTime','$lvStartTime')) as lv_time";
		$vresult=db_query($lvsql);
		$vrow=db_fetch_array($vresult);
		return $vrow['lv_time'];
	}*/
	function LV_Aproval($vlv004)
	{
		if ($this->isApr == 0) return false;
		$lvsql = "Update tc_lv0009 set lv005=1  WHERE lv003=month('$vlv004') and lv004=year('$vlv004') ";
		$vReturn = db_query($lvsql);
		if ($vReturn) $this->InsertLogOperation($this->DateCurrent, 'tc_lv0012.approval', sof_escape_string($lvsql));
		return $vReturn;
	}
	function LV_UnAproval($vlv004)
	{
		if ($this->isApr == 0) return false;
		$lvsql = "Update tc_lv0009 set lv005=0  WHERE lv003=month('$vlv004') and lv004=year('$vlv004')  ";
		$vReturn = db_query($lvsql);
		if ($vReturn) $this->InsertLogOperation($this->DateCurrent, 'tc_lv0012.unapproval', sof_escape_string($lvsql));
		return $vReturn;
	}
	function LV_CheckLocked($vlv004)
	{
		$lvsql = "select lv005 from  tc_lv0009 Where lv003=month('" . $vlv004 . "-00') and lv004=year('" . $vlv004 . "-00') and lv002='$this->lvNVID'";
		$vresult = db_query($lvsql);
		if ($vresult) {
			$vrow = db_fetch_array($vresult);
			if ($vrow) {
				if ($vrow['lv005'] <= 0)
					return true;
				else
					return false;
			} else {
				$lvsql = "insert into tc_lv0009 (lv002,lv003,lv004,lv005,lv006) values('$this->lvNVID',month('$vlv004'),year('$vlv004'),0,'" . getInfor($this->UserID, 2) . "')";
				$vReturn = db_query($lvsql);
				if ($vReturn) {
					$this->InsertLogOperation($this->DateCurrent, 'tc_lv0009.insert', sof_escape_string($lvsql));
				}
				return true;
			}
		} else {
			$lvsql = "insert into tc_lv0009 (lv002,lv003,lv004,lv005,lv006) values('$this->lvNVID',month('$vlv004'),year('$vlv004'),0,'" . getInfor($this->UserID, 2) . "')";
			$vReturn = db_query($lvsql);
			if ($vReturn) {
				$this->InsertLogOperation($this->DateCurrent, 'tc_lv0009.insert', sof_escape_string($lvsql));
			}
			return true;
		}
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
		if ($this->lv010 != "")  $strCondi = $strCondi . " and lv010 like '%$this->lv010%'";
		if ($this->lvNVID != "") $strCondi = $strCondi . " and lv002 IN (select B.lv001 from tc_lv0010 B where B.lv002='$this->lvNVID')";
		return $strCondi;
	}
	////////////////Count///////////////////////////
	function GetCount()
	{
		$sqlC = "SELECT COUNT(*) AS nums FROM tc_lv0011 WHERE 1=1 " . $this->GetCondition();
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
		<table  align=\"center\" class=\"lvtable\">
		<!--<tr ><td colspan=\"" . (2 + count($lstArr)) . "\" class=\"lvTTable\">" . $this->ArrPush[0] . "</td></tr>-->
		<tr class=\"cssbold_tab\"><td colspan=\"" . (count($lstArr)) . "\">" . $this->TabFunction($lvFrom, $lvList, $maxRows) . "</td><td colspan=\"2\" align=right>" . $this->ListFieldSave($lvFrom, $lvList, $maxRows, $lvOrderList, $lvSortNum) . "</td></tr>
		@#01
		<tr ><td colspan=\"" . (count($lstArr) + 2) . "\">$paging</td></tr>
		<tr class=\"cssbold_tab\"><td colspan=\"" . (count($lstArr)) . "\">" . $this->TabFunction($lvFrom, $lvList, $maxRows) . "</td><td colspan=\"2\" align=right>" . $this->ListFieldExport($lvFrom, $lvList, $maxRows) . "</td></tr>
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
		$lvTd = "<td  class=@#04>@02</td>";
		$sqlS = "SELECT * FROM tc_lv0011 WHERE 1=1  " . $this->GetCondition() . " $strSort LIMIT $curRow, $maxRows";
		$vorder = $curRow;
		$bResult = db_query($sqlS);
		$this->Count = db_num_rows($bResult);
		$strTrH = "";
		$strTr = "";
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
			$strTr = $strTr . str_replace("@#01", $strL, str_replace("@02", $vrow['lv001'], str_replace("@03", $vorder, str_replace("@01", $vorder % 2, $lvTr))));
			if ($vrow['lv011'] == 1)		$strTr = str_replace("@#04", "lvlineapproval", $strTr);
			else $strTr = str_replace("@#04", "", $strTr);
		}
		$strTrH = str_replace("@#01", $strH, $lvTrH);
		return str_replace("@#01", $strTrH . $strTr, $lvTable);
	}
	function LV_BuilListInput($lvList, $lvFrom, $lvChkAll, $lvChk, $curRow, $maxRows, $paging, $lvOrderList)
	{
		$this->LV_CheckLock();
		if ($lvList == "") $lvList = $this->DefaultFieldList;
		if ($this->isView == 0) return false;
		$lstArr = explode(",", $lvList);
		$lstOrdArr = explode(",", $lvOrderList);
		$lstArr = $this->getsort($lstArr, $lstOrdArr);
		if ($this->LV_CheckLocked($this->lv004 . "-01")) {
			$vSave = "<a class=\"lvtoolbar\" href=\"javascript:Save();\" tabindex=\"47\"><img src=\"../images/controlright/save_f2.png\"  alt=\"Save\" title=\"<?php echo $vLangArr[1];?>\"
name=\"save\" border=\"0\" align=\"middle\" id=\"save\" /> <?php echo $vLangArr[2];?></a>";
$this->isUnApr = 0;
} else {
$this->isApr = 0;
}
$lvTable = "
<table align=\"center\" class=\"lvtable\">
    <!--<tr ><td colspan=\"" . (2 + count($lstArr)) . "\" class=\"lvTTable\">" . $this->ArrPush[0] . "</td></tr>-->
    <tr>
        <td colspan=\"" . (count($lstArr) - 2) . "\" align=right>" . $vSave . "</td>
        <td colspan=\"2\">" . $this->TabFunction($lvFrom, $lvList, $maxRows) . "</td>
        <td colspan=\"2\" align=right>" . $this->ListFieldSave($lvFrom, $lvList, $maxRows, $lvOrderList, $lvSortNum) . "
        </td>
    </tr>
    @#01
    <tr>
        <td colspan=\"" . (count($lstArr) + 2) . "\">$paging</td>
    </tr>
    <tr>
        <td colspan=\"" . (count($lstArr) - 2) . "\" align=right>" . $vSave . "</td>
        <td colspan=\"2\">" . $this->TabFunction($lvFrom, $lvList, $maxRows) . "</td>
        <td colspan=\"2\" align=right>" . $this->ListFieldExport($lvFrom, $lvList, $maxRows) . "</td>
    </tr>
</table>
";
$lvTrH = "<tr class=\"lvhtable\">
    <td width=1% class=\"lvhtable\">" . $this->ArrPush[1] . "</td>
    <td width=1%><input name=\"$lvChkAll\" type=\"checkbox\" id=\"$lvChkAll\" onclick=\"DoChkAll($lvFrom, '$lvChk' ,
            this)\" value=\"$curRow\" tabindex=\"2\" /></td>
    @#01
</tr>
";
$lvTr = "<tr class=\"lvlinehtable@01\">
    <td width=1% onclick=\"Select_Check('$lvChk@03',$lvFrom, '$lvChk' , '$lvChkAll' )\">@03</td>
    <td width=1%><input name=\"$lvChk\" type=\"checkbox\" id=\"$lvChk@03\" onclick=\"CheckOne($lvFrom, '$lvChk'
            , '$lvChkAll' , this)\" value=\"@02\" tabindex=\"2\" onKeyUp=\"return
            CheckKeyCheck(event,2,'$lvChk',$lvFrom, '$lvChk' , '$lvChkAll' ,@03)\" /></td>
    @#01
</tr>
";
$lvHref = "@02";
$lvTdH = "<td width=\"@01\" class=\"lvhtable\">@02</td>";
$lvTd = "<td>@02";
    $lvTdInput = "
<td><input id=\"@03\" name=\"@03\" value=\"@02\" tabindex=2 class=\"lvsizeinput\"></td>";
$lvTdInput2 = "<td><input id=\"@03\" name=\"@03\" value=\"@02\" tabindex=2 class=\"lvsizeinput2\"></td>";
$lvTdSelect = "<td><select id=\"@03\" name=\"@03\" tabindex=2 class=\"lvsizeselect\">@02</select>";
    $lvTdSelect2 = "
<td><select id=\"@03\" name=\"@03\" tabindex=2 class=\"lvsizeselect2\">@02</select>";
    $lvTdInputHidden = "
<td>@02<input type=hidden id=\"@03\" name=\"@03\" value=\"@02\"></td>";
$sqlS = "SELECT lv001,lv002,lv003,lv004,DAYOFWEEK(lv004) DOW FROM tc_lv0011 WHERE 1=1 " . $this->GetCondition() . "
$strSort LIMIT $curRow, $maxRows";
$vorder = $curRow;
$bResult = db_query($sqlS);
$this->Count = db_num_rows($bResult);
$strTrH = "";
$strTr = "";
for ($i = 0; $i < count($lstArr); $i++) { $vTemp=str_replace("@01", "" , $lvTdH); $vTemp=str_replace("@02", $this->
    ArrPush[(int)$this->ArrGet[$lstArr[$i]]], $vTemp);
    $strH = $strH . $vTemp;
    }

    while ($vrow = db_fetch_array($bResult)) {
    $strL = "";
    $vorder++;
    for ($i = 0; $i < count($lstArr); $i++) { switch ($lstArr[$i]) { case 'lv007' : case 'lv006' :
        $vTemp=str_replace("@02", str_replace("@02", $this->getvaluelink($lstArr[$i], $vrow[$lstArr[$i]]),
        str_replace("@01", $vrow['lv001'], $lvHref)), str_replace("@03", "txt" . $lstArr[$i] . $vorder, $lvTdInput));
        break;
        case 'lv005':
        $vTemp = str_replace("@02", str_replace("@02", $this->getvaluelink($lstArr[$i],
        $this->GetTimeListOutOff($this->lvNVID, $vrow['lv004'], $vrow['lv001'])), str_replace("@01", $vrow['lv001'],
        $lvHref)), $this->Align($lvTd, (int)$this->ArrView[$lstArr[$i]]));

        break;
        case 'lv001':
        case 'lv002':
        $vTemp = str_replace("@02", str_replace("@02", $this->getvaluelink($lstArr[$i],
        $this->FormatView($vrow[$lstArr[$i]], (int)$this->ArrView[$lstArr[$i]])), str_replace("@01", $vrow['lv001'],
        $lvHref)), str_replace("@03", "txt" . $lstArr[$i] . $vorder, $lvTdInputHidden));
        break;
        case 'lv004':
        $vTemp = str_replace("@02", str_replace("@02", $this->getvaluelink($lstArr[$i],
        $this->FormatView($vrow[$lstArr[$i]], (int)$this->ArrView[$lstArr[$i]])), str_replace("@01", $vrow['lv001'],
        $lvHref)), str_replace("@03", "txt" . $lstArr[$i] . $vorder, str_replace("@02", $vrow[$lstArr[$i]],
        $lvTdInputHidden)));
        break;
        default:
        $vTemp = str_replace("@02", str_replace("@02", $this->getvaluelink($lstArr[$i],
        $this->FormatView($vrow[$lstArr[$i]], (int)$this->ArrView[$lstArr[$i]])), str_replace("@01", $vrow['lv001'],
        $lvHref)), $this->Align($lvTd, (int)$this->ArrView[$lstArr[$i]]));
        break;
        }
        $strL = $strL . $vTemp;
        }
        if ((int)$vrow['DOW'] == 1) {
        $strTr = $strTr . str_replace("@#01", $strL, str_replace("@02", $vrow['lv001'], str_replace("@03", $vorder,
        str_replace("@01", '3', $lvTr))));
        } else
        $strTr = $strTr . str_replace("@#01", $strL, str_replace("@02", $vrow['lv001'], str_replace("@03", $vorder,
        str_replace("@01", $vorder % 2, $lvTr))));
        }
        $strTrH = str_replace("@#01", $strH, $lvTrH);
        return str_replace("@#01", $strTrH . $strTr, $lvTable);
        }
        ////GetTime list
        function GetTimeList($vlv001, $vlv002)
        {
        $strReturn = "";
        $lvsql = "select lv003,lv005 from tc_lv0012 Where lv001='$vlv001' and lv002='$vlv002' order by lv003 ASC";
        $vresult = db_query($lvsql);
        if ($vresult) {

        while ($vrow = db_fetch_array($vresult)) {
        if ($strReturn == "") {
        if (trim($vrow['lv005']) == '' || $vrow['lv005'] == NULL)
        $strReturn = $strReturn . $vrow['lv003'];
        else {
        $strReturn = $strReturn . '<font style="color:red;text-decoration:underline"
            title="' . GetUserName($vrow['lv005'], $this->lang) . '(' . $vrow['lv005'] . ')' . '">' . $vrow['lv003'] . "
        </font>";
        }
        } else {
        if (trim($vrow['lv005']) == '' || $vrow['lv005'] == NULL)
        $strReturn = $strReturn . "->" . $vrow['lv003'];
        else
        $strReturn = $strReturn . "->" . '<font style="color:red;text-decoration:underline"
            title="' . GetUserName($vrow['lv005'], $this->lang) . '(' . $vrow['lv005'] . ')' . '">' . $vrow['lv003'] . "
        </font>";
        }
        }
        }
        return $strReturn;
        }
        ////GetTime list
        function GetTimeListOutOff($vlv001, $vlv002, $vid, $vopt = 0)
        {
        $strReturn = "";
        $lvsql = "select lv001,lv002,lv003,lv004,lv005,lv006 from tc_lv0012 Where lv001='$vlv001' and lv002='$vlv002'
        order by lv003 ASC";
        $vresult = db_query($lvsql);
        if ($vresult) {

        while ($vrow = db_fetch_array($vresult)) {
        if ($strReturn == "") {
        if (trim($vrow['lv005']) == '' || $vrow['lv005'] == NULL)
        $strReturn = $strReturn . '<a
            href="javascript:' . (($vrow['lv006'] == '1') ? 'undeltime' : (($vrow['lv006'] == '0' && $vrow['lv004'] == 'OTO') ? 'deltime' : 'overtime')) . '(\'' . $vrow['lv001'] . '\',\'' . $vrow['lv002'] . '\',\'' . $vrow['lv003'] . '\',\'' . $vid . '\')">
            <font
                style="' . (($vrow['lv006'] == '1') ? 'text-decoration:line-through;' : (($vrow['lv006'] == '0' && $vrow['lv004'] == 'OTO') ? 'color:red' : 'color:blue')) . '">
                ' . $vrow['lv003'] . "</font>
        </a>";
        else {
        $strReturn = $strReturn . '<a
            href="javascript:' . (($vrow['lv006'] == '1') ? 'undeltime' : (($vrow['lv006'] == '0' && $vrow['lv004'] == 'OTO') ? 'deltime' : 'overtime')) . '(\'' . $vrow['lv001'] . '\',\'' . $vrow['lv002'] . '\',\'' . $vrow['lv003'] . '\',\'' . $vid . '\')">'
            . '<font style="color:red;text-decoration:underline"
                title="' . GetUserName($vrow['lv005'], $this->lang) . '(' . $vrow['lv005'] . ')' . '">
                <font
                    style="' . (($vrow['lv006'] == '1') ? 'text-decoration:line-through;' : (($vrow['lv006'] == '0' && $vrow['lv004'] == 'OTO') ? 'color:red' : 'color:blue')) . '">
                    ' . $vrow['lv003'] . "</font>
            </font></a>";
        }
        } else {
        if (trim($vrow['lv005']) == '' || $vrow['lv005'] == NULL)
        $strReturn = $strReturn . "->" . '<a
            href="javascript:' . (($vrow['lv006'] == '1') ? 'undeltime' : (($vrow['lv006'] == '0' && $vrow['lv004'] == 'OTO') ? 'deltime' : 'overtime')) . '(\'' . $vrow['lv001'] . '\',\'' . $vrow['lv002'] . '\',\'' . $vrow['lv003'] . '\',\'' . $vid . '\')">
            <font
                style="' . (($vrow['lv006'] == '1') ? 'text-decoration:line-through;' : (($vrow['lv006'] == '0' && $vrow['lv004'] == 'OTO') ? 'color:red' : 'color:blue')) . '">
                ' . $vrow['lv003'] . "</font>
        </a>";
        else
        $strReturn = $strReturn . "->" . '<a
            href="javascript:' . (($vrow['lv006'] == '1') ? 'undeltime' : (($vrow['lv006'] == '0' && $vrow['lv004'] == 'OTO') ? 'deltime' : 'overtime')) . '(\'' . $vrow['lv001'] . '\',\'' . $vrow['lv002'] . '\',\'' . $vrow['lv003'] . '\',\'' . $vid . '\')">'
            . '<font style="color:red;text-decoration:underline"
                title="' . GetUserName($vrow['lv005'], $this->lang) . '(' . $vrow['lv005'] . ')' . '">
                <font
                    style="' . (($vrow['lv006'] == '1') ? 'text-decoration:line-through;' : (($vrow['lv006'] == '0' && $vrow['lv004'] == 'OTO') ? 'color:red' : 'color:blue')) . '">
                    ' . $vrow['lv003'] . "</font>
            </font></a>";
        }
        }
        }
        if ($vopt == 1)
        return $strReturn;
        else
        return '<div id="' . $vid . '">' . $strReturn . '</div>';
        }
        /////////////////////ListFieldExport//////////////////////////
        function ListFieldExport($lvFrom, $lvList, $maxRows)
        {
        if ($lvList == "") $lvList = $this->DefaultFieldList;
        $lvList = "," . $lvList . ",";
        $lstArr = explode(",", $this->DefaultFieldList);
        $lvSelect = "<ul id=\"menu1-nav\" onkeyup=\"return CheckKeyCheckTabExp(event)\">
            <li class=\"menusubT1\"><img src=\"$this->Dir../images/lvicon/config.png\" border=\"0\" />" .
                $this->ArrFunc[12] . "
                <ul id=\"submenu1-nav\">
                    @#01
                </ul>
            </li>
        </ul>";
        $strScript = "
        <script language=\"javascript\">
        function Export(vFrom, value) {
            window.open('" . $this->Dir . "tc_lv0012/?lang=" . $this->lang . "&childdetailfunc=' + value +
                '&ID=" . base64_encode($this->lv002) . "&NVID=" . $this->lvNVID . "&YearMonth=" . $this->lv004 . "',
                '',
                'width=800,height=600,left=200,top=100,screenX=0,screenY=100,resizable=yes,status=no,scrollbars=yes,menubar=yes'
                );
        }
        </script>
        ";
        $lvScript = "<li class=\"menuT\"> @01 </li>";
        $lvexcel = "<input class=lvbtdisplay type=\"button\" id=\"lvbuttonexcel\" value=\"" . $this->ArrFunc[13] . "\"
        onclick=\"Export($lvFrom,'excel')\">";
        $lvpdf = "<input class=lvbtdisplay type=\"button\" id=\"lvbutton\" value=\"" . $this->ArrFunc[15] . "\"
        onclick=\"Export($lvFrom,'pdf')\">";
        $lvword = "<input class=lvbtdisplay type=\"button\" id=\"lvbutton\" value=\"" . $this->ArrFunc[14] . "\"
        onclick=\"Export($lvFrom,'word')\">";
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
            <li class=\"menusubT\"><img src=\"$this->Dir../images/lvicon/config.png\" border=\"0\" />" .
                $this->ArrFunc[11] . "
                <ul id=\"submenu-nav\">
                    @#01
                </ul>
            </li>
        </ul>";
        $strScript = "
        <script language=\"javascript\">
        function SelectChk(vFrom, len) {
            vFrom.txtFieldList.value = getChecked(len, 'lvdisplaychk');
            vFrom.txtOrderList.value = getAlllen(len, 'lvorder');
            vFrom.txtFlag.value = 2;
            vFrom.submit();
        }

        function lv_on_open(opt) {
            div = document.getElementById('lvsllist');
            if (opt == 0) {
                div.size = 1;
            } else
                div.size = div.length;

        }

        function getChecked(len, nameobj) {
            var str = '';
            for (i = 0; i < len; i++) {
                div = document.getElementById(nameobj + i);
                if (div.checked) {

                    if (str == '')
                        str = '' + div.value;
                    else
                        str = str + ',' + div.value;

                }
            }
            return str;
        }

        function getAlllen(len, nameobj) {
            var str = '';
            for (i = 0; i < len; i++) {
                div = document.getElementById(nameobj + i);
                if (str == '')
                    str = '' + div.value;
                else
                    str = str + ',' + div.value;
            }
            return str;
        }
        </script>
        ";
        $lvScript = "<li class=\"menuT\"> @01 </li>";
        $lvNumPage = "" . $this->ArrOther[2] . "<input type=\"text\" class=\"lvmaxrow\" name=lvmaxrow id=lvmaxrow
            value=\"$maxRows\">";
        $lvSortPage = "" . GetLangSort(0, $this->lang) . "<select class=\"lvsortrow\" name=lvsort id=lvsort>
            <option value=0 " . (($lvSortNum == 0) ? 'selected' : '') . ">" . GetLangSort(1, $this->lang) . "</option>
            <option value=1 " . (($lvSortNum == 1) ? 'selected' : '') . ">" . GetLangSort(2, $this->lang) . "</option>
            <option value=2 " . (($lvSortNum == 2) ? 'selected' : '') . ">" . GetLangSort(3, $this->lang) . "</option>
        </select>";
        $lvChk = "<input type=\"checkbox\" id=\"lvdisplaychk@01\" name=\"lvdisplaychk@01\" value=\"@02\" @03><input
            id=\"lvorder@01\" name=\"lvorder@01\" type=\"text\" value=\"@06\"\ style=\"width:20px\">";
        $lvButton = "<input class=lvbtdisplay type=\"button\" id=\"lvbutton\" value=\"" . $this->ArrOther[1] . "\"
        onclick=\"SelectChk($lvFrom," . count($lstArr) . ")\">";
        $strGetList = "";
        $strGetScript = "";
        $strTemp = str_replace("@01", $lvButton, $lvScript);
        $strGetScript = $strGetScript . $strTemp;
        $strTemp = str_replace("@01", $lvNumPage, $lvScript);
        $strGetScript = $strGetScript . $strTemp;
        $strTemp = str_replace("@01", $lvSortPage, $lvScript);
        $strGetScript = $strGetScript . $strTemp;

        for ($i = 0; $i < count($lstArr); $i++) { $strTempChk=str_replace("@01", $i, $lvChk . $this->
            ArrPush[(int)$this->ArrGet[$lstArr[$i]]]);
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
            $strTbl = "<table align=\"center\" class=\"lvtable\">
                <input type=\"hidden\" id=$vID name=$vID value=\"@#02\">
                @#01
            </table>
            <script language=\"javascript\">
            function getChecked(len, nameobj, namevalue) {
                var str = '';
                for (i = 0; i < len; i++) {
                    div = document.getElementById(nameobj + i);
                    if (div.checked) {
                        div1 = document.getElementById(namevalue + i);
                        if (str == '')
                            str = (namevalue == '') ? div.value : div1.value;
                        else
                            str = str + ',' + (namevalue == '') ? div.value : div1.value;
                    }

                }
                return str;
            }
            </script>
            ";
            $lvChk = "<input type=\"checkbox\" id=\"$vID@01\" value=\"@02\" @03 title=\"@04\" tabindex=\"$vTabIndex\">";
            $lvTrH = "<tr class=\"lvlinehtable1\">
                <td width=1%>@#01</td>
                <td>@#02</td>

            </tr>
            ";
            $vsql = "select * from hr_lv0004";
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
            $lvTable = "
            <!--<div align=\"center\"><img  src=\"" . $this->GetLogo() . "\" style=\"max-width:1024px\" /></div>-->
            <div align=\"center\">
                <h1>" . ($this->ArrPush[0]) . "</h2>
            </div>
            <table align=\"center\" class=\"lvtable\" border=1>
                @#01
            </table>
            ";
            $lvTrH = "<tr class=\"lvhtable\">
                <td width=1% class=\"lvhtable\">" . $this->ArrPush[1] . "</td>

                @#01
            </tr>
            ";
            $lvTr = "<tr class=\"lvlinehtable@01\">
                <td width=1% class=@#04>@03</td>
                @#01
            </tr>
            ";
            $lvTdH = "<td width=\"@01\" class=\"lvhtable\">@02</td>";
            $lvTd = "<td class=@#04>@02</td>";
            $sqlS = "SELECT * FROM tc_lv0011 WHERE 1=1 " . $this->RptCondition . " $strSort LIMIT $curRow, $maxRows";
            $vorder = $curRow;
            $bResult = db_query($sqlS);
            $this->Count = db_num_rows($bResult);
            $strTrH = "";
            $strTr = "";
            for ($i = 0; $i < count($lstArr); $i++) { $vTemp=str_replace("@01", "" , $lvTdH); $vTemp=str_replace("@02",
                $this->ArrPush[(int)$this->ArrGet[$lstArr[$i]]], $vTemp);
                $strH = $strH . $vTemp;
                }

                while ($vrow = db_fetch_array($bResult)) {
                $strL = "";
                $vorder++;
                for ($i = 0; $i < count($lstArr); $i++) { $vTemp=str_replace("@02", $this->getvaluelink($lstArr[$i],
                    $this->FormatView($vrow[$lstArr[$i]], (int)$this->ArrView[$lstArr[$i]])), $this->Align($lvTd,
                    (int)$this->ArrView[$lstArr[$i]]));
                    $strL = $strL . $vTemp;
                    }


                    $strTr = $strTr . str_replace("@#01", $strL, str_replace("@02", $vrow['lv001'], str_replace("@03",
                    $vorder, str_replace("@01", $vorder % 2, $lvTr))));
                    if ($vrow['lv011'] == 1) $strTr = str_replace("@#04", "lvlineapproval", $strTr);
                    else $strTr = str_replace("@#04", "", $strTr);
                    }
                    $strTrH = str_replace("@#01", $strH, $lvTrH);
                    return str_replace("@#01", $strTrH . $strTr, $lvTable);
                    }
                    //////////////////////Buil list////////////////////
                    function LV_BuilListReportOther($lvList, $lvFrom, $lvChkAll, $lvChk, $curRow, $maxRows, $paging,
                    $lvOrderList)
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
                    <table align=\"center\" class=\"lvtable\" border=1 cellspacing=\"0\" cellpadding=\"0\">
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
                    $lvTd = "<td class=@#04>@02</td>";
                    $sqlS = "SELECT * FROM tc_lv0011 WHERE 1=1 " . $this->GetCondition() . " $strSort LIMIT $curRow,
                    $maxRows";
                    $vorder = $curRow;
                    $bResult = db_query($sqlS);
                    $this->Count = db_num_rows($bResult);
                    $strTrH = "";
                    $strTr = "";
                    for ($i = 0; $i < count($lstArr); $i++) { $vTemp=str_replace("@01", "" , $lvTdH);
                        $vTemp=str_replace("@02", $this->ArrPush[(int)$this->ArrGet[$lstArr[$i]]], $vTemp);
                        $strH = $strH . $vTemp;
                        }

                        while ($vrow = db_fetch_array($bResult)) {
                        $strL = "";
                        $vorder++;
                        for ($i = 0; $i < count($lstArr); $i++) { $vTemp=str_replace("@02", $this->
                            getvaluelink($lstArr[$i], $this->FormatView($vrow[$lstArr[$i]],
                            (int)$this->ArrView[$lstArr[$i]])), $this->Align($lvTd, (int)$this->ArrView[$lstArr[$i]]));
                            $strL = $strL . $vTemp;
                            }


                            $strTr = $strTr . str_replace("@#01", $strL, str_replace("@02", $vrow['lv001'],
                            str_replace("@03", $vorder, str_replace("@01", $vorder % 2, $lvTr))));
                            if ($vrow['lv011'] == 1) $strTr = str_replace("@#04", "", $strTr);
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
                            case 'lv007':
                            $vsql = "select lv001,lv002,IF(lv001='$vSelectID',1,0) lv003 from tc_lv0002";
                            break;
                            case 'lv009':
                            $vsql = "select lv001,lv002,IF(lv001='$vSelectID',1,0) lv003 from hr_lv0020";
                            case 'lv010':
                            $vsql = "select lv001,lv002,IF(lv001='$vSelectID',1,0) lv003 from tc_lv0004";
                            break;

                            break;
                            }
                            return $vsql;
                            }
                            public function getvaluelink($vFile, $vSelectID)
                            {
                            if ($this->ArrGetValueLink[$vFile][$vSelectID][0]) return
                            $this->ArrGetValueLink[$vFile][$vSelectID][1];
                            if ($vSelectID == "") {
                            return $vSelectID;
                            }
                            switch ($vFile) {
                            case 'lv007':
                            $vsql = "select lv001,lv002,IF(lv001='$vSelectID',1,0) lv003 from tc_lv0002 where
                            lv001='$vSelectID'";
                            break;
                            case 'lv009':
                            $vsql = "select lv001,lv002,IF(lv001='$vSelectID',1,0) lv003 from hr_lv0020 where
                            lv001='$vSelectID'";
                            break;
                            case 'lv010':
                            $vsql = "select lv001,lv002,IF(lv001='$vSelectID',1,0) lv003 from tc_lv0004 where
                            lv001='$vSelectID'";
                            break;
                            }
                            if ($vsql == "") {
                            return $vSelectID;
                            } else {
                            $lvResult = db_query($vsql);
                            $this->ArrGetValueLink[$vFile][$vSelectID][0] = true;
                            }
                            while ($row = db_fetch_array($lvResult)) {
                            $this->ArrGetValueLink[$vFile][$vSelectID][1] = ($lvopt == 0) ? $row['lv002'] : (($lvopt ==
                            1) ? $row['lv001'] . "(" . $row['lv002'] . ")" : (($lvopt == 2) ? $row['lv002'] . "(" .
                            $row['lv001'] . ")" : $row['lv001']));
                            return $this->ArrGetValueLink[$vFile][$vSelectID][1];
                            }
                            }
                            }