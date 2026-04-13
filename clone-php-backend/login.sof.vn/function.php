<?php
function GetServerDate()
{
$tsql="select CURRENT_DATE() as GDate";
$tresult=db_query($tsql);
if($tresult)
	{
		$trow=db_fetch_array($tresult);
		return  $trow['GDate'];
	}
	return "Error connect";
}
function GetServerTime()
{
$tsql="select CURRENT_TIME() as GDate";
$tresult=db_query($tsql);
if($tresult)
	{
		$trow=db_fetch_array($tresult);
		return  $trow['GDate'];
	}
	return "Error connect";

}
function GetWeek($tNow)
{
$tsql="select WEEK('$tNow') as GDate";
$tresult=db_query($tsql);
if($tresult)
	{
		$trow=db_fetch_array($tresult);
		return  $trow['GDate'];
	}
	return "Error connect";
}
function GetDayOfWeek($tNow)
{
$tsql="select DAYOFWEEK('$tNow') as GDate";
$tresult=db_query($tsql);
if($tresult)
	{
		$trow=db_fetch_array($tresult);
		return  $trow['GDate'];
	}
	return "Error connect";
}
function MakeDate($tYear,$tNumDay)
{
$tsql="select MAKEDATE(".$tYear.",".$tNumDay.") as GDate";
$tresult=db_query($tsql);
if($tresult)
	{
		$trow=db_fetch_array($tresult);
		return  $trow['GDate'];
	}
	return "Error.";
}

function TIMEDIFF($vTimeEnd,$vTimeStart)
{
	$tsql="select TIMEDIFF('$vTimeEnd','$vTimeStart') as GDate";
	$tresult=db_query($tsql);
	$trow=db_fetch_array($tresult);
	return $trow['GDate'];
}
function ADDDATE($vDate,$vnum)
{
	$tsql="select ADDDATE('$vDate',$vnum) as GDate";
	$tresult=db_query($tsql);
	$trow=db_fetch_array($tresult);
	return $trow['GDate'];
}
function DATEDIFF($vDateEnd,$vDateStart)
{
	$tsql="select DATEDIFF('$vDateEnd','$vDateStart') as NDate";
	$tresult=db_query($tsql);
	$trow=db_fetch_array($tresult);
	return $trow['NDate'];
}
function TIMEADD($vTimeEnd,$vTimeStart)
{
$vSStart=(int)substr($vTimeStart,strlen($vTimeStart)-6+3,2);
$vMStart=(int)substr($vTimeStart,strlen($vTimeStart)-6+1,2);
$vHStart=(int)substr($vTimeStart,0,strlen($vTimeStart)-6);
$vSEnd=(int)substr($vTimeEnd,strlen($vTimeEnd)-6+3,2);
$vMEnd=(int)substr($vTimeEnd,strlen($vTimeEnd)-6+1,2);
$vHEnd=(int)substr($vTimeEnd,0,strlen($vTimeEnd)-6);
if(($vSStart+$vSEnd)>=60)
{
	$sreturn=Fillnum($vSStart+$vSEnd-60,2);
	$vMStart=$vMStart+1;
}
else
	$sreturn=Fillnum($vSStart+$vSEnd,2);

if(($vMStart+$vMEnd)>=60)
{
	$sreturn=Fillnum($vMStart+$vMEnd-60,2).":".$sreturn;
	$vHStart=$vHStart+1;
}
else
	$sreturn=Fillnum($vMStart+$vMEnd,2).":".$sreturn;

	$sreturn=Fillnum($vHStart+$vHEnd,2).":".$sreturn;
return $sreturn;
}
function TIMEMULTI($vTime,$vNum)
{
$vSStart=(int)substr($vTime,strlen($vTime)-6+3,2);
$vMStart=(int)substr($vTime,strlen($vTime)-6+1,2);
$vHStart=(int)substr($vTime,0,strlen($vTime)-6);
$vSStart=$vSStart*$vNum;
$vMStart=$vMStart*$vNum;
$vHStart=$vHStart*$vNum;

	$sreturn=Fillnum(($vSStart%60),2);
	$vMStart=$vMStart+(int)($vSStart/60);
	$sreturn=Fillnum($vMStart%60,2).":".$sreturn;
	$vHStart=$vHStart+(int)($vMStart/60);	
	$sreturn=Fillnum($vHStart,2).":".$sreturn;	
return $sreturn;
}
function CompareTime($vGreateT,$vLessT)
{
	$vReturn=true;
	$vSStart=(int)substr($vGreateT,strlen($vGreateT)-6+3,2);
	$vMStart=(int)substr($vGreateT,strlen($vGreateT)-6+1,2);
	$vHStart=(int)substr($vGreateT,0,strlen($vGreateT)-6);
	$vSEnd=(int)substr($vLessT,strlen($vLessT)-6+3,2);
	$vMEnd=(int)substr($vLessT,strlen($vLessT)-6+1,2);
	$vHEnd=(int)substr($vLessT,0,strlen($vLessT)-6);
	if($vHStart==$vHEnd)
		{
			if($vMStart==$vMEnd)
			{
				$vReturn=($vSStart<$vSEnd)?false:true;	
			}
			else
			{
			$vReturn=($vMStart<$vMEnd)?false:true;
			}
		}
	else
		{
			$vReturn=($vHStart<$vHEnd)?false:true;
		}
	return $vReturn;
}
function Fillnum($vNum,$vLength)
{
	if(strlen($vNum)<$vLength)
	{
	$vLength=$vLength-strlen($vNum);
		for($vi=1;$vi<=($vLength);$vi++)
		{
			$vNum="0".$vNum;
		}
	}
	return $vNum;
}
function Fillnumeric($vNum,$vLength)
{
	if(strlen($vNum)<$vLength)
	{
	$vLength=$vLength-strlen($vNum);
		for($vi=1;$vi<=($vLength);$vi++)
		{
			$vNum=$vNum."0";
		}
	}
	return $vNum;
}
function FillSpace($vNum,$vLength)
{
	if(strlen($vNum)<$vLength)
	{
	$vLength=$vLength-strlen($vNum);
		for($vi=1;$vi<=($vLength);$vi++)
		{
			$vNum=" ".$vNum;
		}
	}
	return $vNum;
}

function GetNoDelete($vStr,$vTable)
{
	$vrStr="";
	$tsql="select A.ID from $vTable A where A.lv001 in ($vStr)";
	$tresult=db_query($tsql);
	if($tresult)
	{
	while($trow=db_fetch_array($tresult)){
		if($vrStr==""){
			$vrStr=$trow['lv001'];
		} else
			$vrStr=$vrStr."||".$trow['lv001'];
	}
	}
	if($vrStr=="")
		return "<font color='#FF0066' face='Verdana, Arial, Helvetica, sans-serif'>"."Delete all successfull!"."</font>";
	else
		return "<font color='#FF0066' face='Verdana, Arial, Helvetica, sans-serif'>".$vrStr." : is used! No delete!"."</font";
}

function GetDayInMonth($vYear,$vMonth)
{
	$vMonth=(int)$vMonth;
	$vt2=($vYear%4==0)?"29":"28";
	$vArr=array (1=>"31",2=>$vt2,3=>"31",4=>"30",5=>"31",6=>"30",7=>"31",8=>"31",9=>"30",10=>"31",11=>"30",12=>"31");
	return $vArr[$vMonth];
}
function GetMonthNumBer($vMonth)
{
	$vArr=array ("Jan"=>"01","Feb"=>"02","Mar"=>"03","Apr"=>"04","May"=>"05","Jun"=>"06","Jul"=>"07","Aug"=>"08","Sep"=>"09","Oct"=>"10","Nov"=>"11","Dec"=>"12");
	return $vArr[$vMonth];
}
function GetMonthName($vMonth)
{
	$vArr=array ("01"=>"January","02"=>"February","03"=>"March","04"=>"April","05"=>"May","06"=>"June","07"=>"July","08"=>"August","09"=>"September","10"=>"October","11"=>"November","12"=>"December");
	return $vArr[$vMonth];
}
function GetDayWorkInMonth($vYear,$vMonth,$vopt=0)
{
	$vReturn=4;
	$vStartDate=$vYear."-".Fillnum($vMonth,2)."-01";
	$vFDayofMonth=GetDayOfWeek($vStartDate);
	$vNumMonth=	GetDayInMonth($vYear,$vMonth);
	$vNumMonthA=$vNumMonth-21;
	if($vFDayofMonth==1)
	{
		if($vMonth==2)
			$vReturn=4;
		else
			$vReturn=5;
	}
	else
	{
		$vt=$vNumMonthA+($vFDayofMonth-8);
		if($vt>7) $vReturn=5;
	}
	return ($vNumMonth-$vReturn);
}
function GetDayWorkInMonths($vYear,$vMonth,$vopt=0)
{
	$vReturn=0;
	$vStartDate=$vYear."-".Fillnum($vMonth,2)."-01";
	$vFDayofMonth=GetDayOfWeek($vStartDate);
	$vNumMonth=	GetDayInMonth($vYear,$vMonth);
	for($i=1;$i<=$vNumMonth;$i++)
	{
		$vFDayofMonth=GetDayOfWeek($vYear."-".Fillnum($vMonth,2)."-".Fillnum($i,2));
		if($vopt==0)
		{
			if($vFDayofMonth==1) $vReturn++;
		}
		elseif($vopt==1)
		{
			if($vFDayofMonth==1 || $vFDayofMonth==7) $vReturn++;
		}
		else
		{
			return $vNumMonth;
		}
	}
	return ($vNumMonth-$vReturn);
}
////////////////////////////////////////////Format Currency and Reject number on last////////////////////////////////////
function LCurrency($strValue,$vLang)
{
	$strReturn = "";
	$arrNum = explode(".", $strValue);//Tach thanh 2 phan 2 ben dau cham dua vao mang
	$decimal=strlen($arrNum[1]);
		if($strValue==NULL || trim($strValue)=='')  $strValue=0;
			if($vLang=="VN" || $vLang=="vn")						
				$strReturn=number_format($strValue,$decimal,",",'.');
			else
				$strReturn=number_format($strValue,$decimal,".",',');			

	return  $strReturn;
}
function LCurrencys($strValue,$vLang)
{
	if($strValue==0 || $strValue==NULL) $strValue=0;
	$strReturn = "";
	$arrNum = explode(".", $strValue);//Tach thanh 2 phan 2 ben dau cham dua vao mang
	if(((int)$arrNum[1])!=0) $decimal=2;
	else $decimal=0;

		if($strValue==NULL || trim($strValue)=='')  $strValue=0;
			if($vLang=="VN" || $vLang=="vn")						
				$strReturn=number_format($strValue,$decimal,",",'.');
			else
				$strReturn=number_format($strValue,$decimal,".",',');			

	return  $strReturn;
}
////////////////////////////////////////////Format Currency and Reject number on last////////////////////////////////////

/////////////////////////////////////////////////////////////////Read VND////////////////////////////////////////////////
function LNum2Text($NumCurrency, $plang)
{
	$vreturn="";
	if($plang == "VN")//Ca tieng Viet va tieng Anh deu su dung ngon ngu tieng Viet
	{
		if($NumCurrency<0)
		{
			$NumCurrency=$NumCurrency-2*$NumCurrency;
			$vreturn="Âm ";
		}
		if($NumCurrency == 0)
			return "Không đồng";

		if($NumCurrency > 922337203685477)# Then ' Số lớn nhất của loại CURRENCY
			$vreturn = "Không đổi được số lớn hơn 922,337,203,685,477";

		$DonViTien = "đồng";
		//$DonViLe = "xu";
		$DonViLe = "";//xu
		
		$CharVND[1] = "một";
		$CharVND[2] = "hai";
		$CharVND[3] = "ba";
		$CharVND[4] = "bốn";
		$CharVND[5] = "năm";
		$CharVND[6] = "sáu";
		$CharVND[7] = "bảy";
		$CharVND[8] = "tám";
		$CharVND[9] = "chín";
		$arrNum = explode(".", $NumCurrency);//Tach thanh 2 phan 2 ben dau cham dua vao mang
		$SoLe = $arrNum[1];//co 2 so le phia sau
		$PhanChan = $arrNum[0];
		$PhanChan = FillSpace($PhanChan, 15);//Chen du 15 ky tu - bao gom ca khoang trang

		$NganTy = intval(substr($PhanChan, 0, 3));
		$Ty = intval(substr($PhanChan, 3, 3));
		$Trieu = intval(substr($PhanChan, 6, 3));
		$Ngan = intval(substr($PhanChan, 9, 3));
		$Dong = intval(substr($PhanChan, 12, 3));
		if($NganTy == 0 && $Ty == 0 && $Trieu == 0 && $Ngan == 0 && $Dong == 0)
		{
			$BangChu = "không ".$DonViTien." ";
			$I = 5;
		}
		else
		{
			$BangChu = "";
			$I = 0;
		}
		while( $I <= 5)	
		{
			switch($I)
			{
				case 0: 
				{
					$SoDoi = $NganTy;
					$Ten = "ngàn tỷ";
					break;
				}
				case 1:
				{
					$SoDoi = $Ty;
					$Ten = "tỷ";
					break;
				}
				case 2:
				{
					$SoDoi = $Trieu;
					$Ten = "triệu";
					break;
				}
				case 3:
				{
					$SoDoi = $Ngan;
					$Ten = "ngàn";
					break;
				}
				case 4:
				{
					$SoDoi = $Dong;
					$Ten = $DonViTien;
					break;
				}
				case 5:
				{
					$SoDoi = $SoLe;
					$Ten = $DonViLe;
					break;
				}
			}
			
			if($SoDoi != 0)
			{
				$Tram = intval($SoDoi / 100);
				$Muoi = intval(($SoDoi - $Tram * 100) / 10);
				$DonVi = ($SoDoi - $Tram * 100) - $Muoi * 10;
				$BangChu = trim($BangChu).((strlen($BangChu) == 0) ? "" : " ").(($Tram != 0) ? trim($CharVND[$Tram])." trăm " : "");
				if($Muoi == 0 && $Tram != 0 && $DonVi != 0)
					$BangChu = $BangChu."lẻ ";
				else
					if($Muoi != 0)
						$BangChu = $BangChu.(($Muoi != 0 && $Muoi != 1) ? trim($CharVND[$Muoi])." mươi " : "mười ");
				if($Muoi != 0 && $DonVi == 5)
					$BangChu = $BangChu."lăm ".$Ten." ";
				else
				{
					if($Muoi > 1 && $DonVi == 1)
						$BangChu = $BangChu."mốt ".$Ten." ";
					else
						$BangChu = $BangChu.(($DonVi != 0) ? trim($CharVND[$DonVi])." ".$Ten." " : $Ten." ");
				}
			}
			else
				$BangChu = $BangChu.(($I == 4)? $DonViTien." " : "");
			$I++;
		}

		if($SoLe == 0)
			$BangChu = $BangChu."";//chẵn
		if($vreturn=="")
		{
			$strTemp = $BangChu[0];
			$strTemp = strtoupper($strTemp);
			$BangChu = $strTemp.substr($BangChu, 1);
			$vreturn = $BangChu;
		}
		else	
			$vreturn=$vreturn.$BangChu ;			
		return $vreturn;
	}

	if($plang == "CN")//Ca tieng Viet va tieng Anh deu su dung ngon ngu tieng Viet
	{
		if($NumCurrency<0)
		{
			$NumCurrency=$NumCurrency-2*$NumCurrency;
			$vreturn="Âm ";
		}
		if($NumCurrency == 0)
			$vreturn = "Zero dong";

		if($NumCurrency > 922337203685477)# Then ' Số lớn nhất của loại CURRENCY
			$vreturn = "Could not transfer with 922,337,203,685,477";

		$DonViTien = "dong";
		//$DonViLe = "xu";
		$DonViLe = "";//xu
		
		$CharVND[1] = "one";
		$CharVND[2] = "two";
		$CharVND[3] = "three";
		$CharVND[4] = "four";
		$CharVND[5] = "five";
		$CharVND[6] = "six";
		$CharVND[7] = "seven";
		$CharVND[8] = "eight";
		$CharVND[9] = "nine";

		$SoLe = intval(round(($NumCurrency - intval($NumCurrency)), 2) * 100);//co 2 so le phia sau
		$PhanChan = (intval($NumCurrency));
		$PhanChan = FillSpace($PhanChan, 15);//Chen du 15 ky tu - bao gom ca khoang trang

		$NganTy = intval(substr($PhanChan, 0, 3));
		$Ty = intval(substr($PhanChan, 3, 3));
		$Trieu = intval(substr($PhanChan, 6, 3));
		$Ngan = intval(substr($PhanChan, 9, 3));
		$Dong = intval(substr($PhanChan, 12, 3));
		if($NganTy == 0 && $Ty == 0 && $Trieu == 0 && $Ngan == 0 && $Dong == 0)
		{
			$BangChu = "and ".$DonViTien." ";
			$I = 5;
		}
		else
		{
			$BangChu = "";
			$I = 0;
		}
		while( $I <= 5)	
		{
			switch($I)
			{
				case 0: 
				{
					$SoDoi = $NganTy;
					$Ten = "thousand billion";
					break;
				}
				case 1:
				{
					$SoDoi = $Ty;
					$Ten = "billion";
					break;
				}
				case 2:
				{
					$SoDoi = $Trieu;
					$Ten = "million";
					break;
				}
				case 3:
				{
					$SoDoi = $Ngan;
					$Ten = "thousand";
					break;
				}
				case 4:
				{
					$SoDoi = $Dong;
					$Ten = $DonViTien;
					break;
				}
				case 5:
				{
					$SoDoi = $SoLe;
					$Ten = $DonViLe;
					break;
				}
			}
			
			if($SoDoi != 0)
			{
				$Tram = intval($SoDoi / 100);
				$Muoi = intval(($SoDoi - $Tram * 100) / 10);
				$DonVi = ($SoDoi - $Tram * 100) - $Muoi * 10;
				$BangChu = trim($BangChu).((strlen($BangChu) == 0) ? "" : " ").(($Tram != 0) ? trim($CharVND[$Tram])." handred " : "");
				if($Muoi == 0 && $Tram != 0 && $DonVi != 0)
					$BangChu = $BangChu."and ";
				else
					if($Muoi != 0)
						$BangChu = $BangChu.(($Muoi != 0 && $Muoi != 1) ? trim($CharVND[$Muoi])." ten " : "ten ");
				if($Muoi != 0 && $DonVi == 5)
					$BangChu = $BangChu."five ".$Ten." ";
				else
				{
					if($Muoi > 1 && $DonVi == 1)
						$BangChu = $BangChu."one ".$Ten." ";
					else
						$BangChu = $BangChu.(($DonVi != 0) ? trim($CharVND[$DonVi])." ".$Ten." " : $Ten." ");
				}
			}
			else
				$BangChu = $BangChu.(($I == 4)? $DonViTien." " : "");
			$I++;
		}

		if($SoLe == 0)
			$BangChu = $BangChu."";//chẵn
		if($vreturn=="")
		{
			$strTemp = $BangChu[0];
			$strTemp = strtoupper($strTemp);
			$BangChu = $strTemp.substr($BangChu, 1);
			$vreturn = $BangChu;
		}
		else	
			$vreturn=$vreturn.$BangChu ;			
		return $vreturn;
	}
}
/////////////////////////////////////////////////////////////////Read VND////////////////////////////////////////////////

///////////////////////////////////////////////////////////Get file in folder////////////////////////////////////////////
function GetImsInDir($dir)
{
	//$dir = ".";//thu muc hien tai
	if(is_dir($dir))
	{
		$dh  = opendir($dir);
		while (false !== ($filename = readdir($dh)))
		{
			//if(is_file($filename))
			//{
				$file_type = array (".jpg",".gif",".bmp",".jpeg",".png",".JPG",".GIF",".BMP",".JPEG",".PNG");
				global $extension;
				$extension = strrchr($filename,".");
				if(in_array($extension,$file_type))
				{
					$files[] = $filename;
				}
			//}
		}
		sort($files);
		//print_r($files);
		//$num=count($files);
		return $files;//array
	}
}
///////////////////////////////////////////////////////////Get file in folder////////////////////////////////////////////

///////////////////////////////////////////////////////////////Short Text////////////////////////////////////////////////
function ShortenText($strStr, $intNunText)//return a part of string (first part)
{
	if(strlen($strStr)>$intNunText)
	{
		$strStrNew = substr($strStr, 0, $intNunText);
		$strStrNew = $strStrNew." ...";
	}
	else
		$strStrNew = $strStr;
	return $strStrNew;
}
///////////////////////////////////////////////////Function Get User////////////////////////////////////////////
function getInfor($vUserID,$State)
{
	$vReturn="";
	switch($State)
	{
		case 1:
			$vsql="	SELECT lv002 UserGroupID 			FROM lv_lv0007  
					WHERE lv001='$vUserID';";
			$vresult=db_query($vsql);
			$vrow=db_fetch_array($vresult);
			if($vrow)
			{
				$vReturn=$vrow['UserGroupID'];
			}
			break;
		case 2:
			$vsql="	SELECT lv006 EmployeeID 			FROM lv_lv0007  
					WHERE lv001='$vUserID';";
			$vresult=db_query($vsql);
			$vrow=db_fetch_array($vresult);
			if($vrow)
			{
				$vReturn=$vrow['EmployeeID'];
			}
			break;
		case 99:
			$vsql="	SELECT lv099 Themes 			FROM lv_lv0007  
					WHERE lv001='$vUserID';";
			$vresult=db_query($vsql);
			$vrow=db_fetch_array($vresult);
			if($vrow)
			{
				$vReturn=$vrow['Themes'];
			}
			break;
	}
 return $vReturn;
}
function GetUserThemeUpdate($vUserID,$themes)
{
	$vsql="	update lv_lv0007 set lv099='$themes' WHERE lv001='$vUserID';";
	$vresult=db_query($vsql);
}
function GetUserName($strUser, $plang,&$vImg=''){
	//if(is_numeric(substr($strUser,0,1))){
		$strSQL = "SELECT lv002 FirstName, lv003 MiddleName,lv004 LastName,lv007 Img FROM hr_lv0020 WHERE lv001='$strUser'; ";
		$arrResult = db_query($strSQL);
		$intRows = db_fetch_array($arrResult);
		
		
		if($plang=="VN"){
			$strFullName=$intRows['LastName']." ".$intRows['MiddleName']." ".$intRows['FirstName'];
		} else {
			$strFullName=$intRows['MiddleName']." ".$intRows['FirstName']." ".$intRows['LastName'];
		}
		$vImg=$intRows['Img'];
		$vArrName= explode(" ",trim($strFullName));
		return $vArrName[count($vArrName)-1];
		//return $strFullName;
	/*} else {
		$strSQL = "SELECT lv004 FROM lv_lv0007 WHERE lv001='$strUser'; ";
		$arrResult = db_query($strSQL);
		$intRows = db_fetch_array($arrResult);
		$strFullName = $intRows['UserName'];
		return $strFullName;
	}*/
}
////////////////////////////////////////////////getStartDate and getEndDate////////////////////////////////////////
function getSEDate($vValue){
	$vDate = NULL;
	$strNow = GetServerDate();
	$vYear = getyear($strNow);
	$vMonth = getmonth($strNow);
	$vDay = getday($strNow);
	$vNumDayInMonth = GetDayInMonth((int)$vYear,(int)$vMonth);
	if((int)$vValue==0){///Start date
		//$vStartDate = $vYear."-".$vMonth."-01";
		$vDate = $vYear."-".$vMonth."-01";///Start month
	} else if((int)$vValue==1){///End date
		//$vEndDate = $vYear."-".$vMonth."-".$vNumDayInMonth;
		//$vDate = $vYear."-".$vMonth."-".$vNumDayInMonth;///End month
		$vDate = $strNow;
	} else {
		return NULL;
	}
	return $vDate;
}
///////////////////////////////////2007-03-06 14:59/////////////////////////////////////////////////
/* Ham nay co tac dung tra ve 1 gia tri dung de nhap vao ID field trong bang, tuong tu auto number */
function InsertWithCheck($vTableName, $vField, $vSYMBOL,$vLengthCast){
	$vReturn = "";
	$sqlS = "	SELECT 
					(SELECT MAX(CAST(SUBSTRING($vField, ".(strlen($vSYMBOL)+1).") AS SIGNED)) FROM $vTableName where ($vField LIKE '$vSYMBOL%') and ASCII(SUBSTRING($vField,".(strlen($vSYMBOL)+1).",1))<58) ROWMAX, 
					(SELECT COUNT(*) FROM $vTableName where ($vField LIKE '$vSYMBOL%')and ASCII(SUBSTRING($vField,".(strlen($vSYMBOL)+1).",1))<58) AS NUMROW;";
	$bResultS = db_query($sqlS);
	$totalRows = db_num_rows($bResultS);
	if ($totalRows>0) {
		$arrS = db_fetch_array($bResultS);
		$vRowMax =(int) $arrS['ROWMAX'];
		$vNumRow = $arrS['NUMROW'];
		if ((int)$vNumRow == (int)$vRowMax) {
			$vRowMax = (int)$vRowMax + 1;
			$vRowMax = Fillnum($vRowMax,$vLengthCast);
			$vReturn = $vReturn.$vSYMBOL.$vRowMax;
		} else if((int)$vNumRow < (int)$vRowMax) {
			$vNumReturn = 0;
			$arrTemp = "@".getArrValue($vTableName, $vField)."@";
			for ($i=1; $i<(int)$vRowMax; $i++) {
				$vNumReturn=$vSYMBOL.Fillnum($i,$vLengthCast);
				if(strpos($arrTemp,"@".$vNumReturn."@")===false) {
					return $vNumReturn;
				}
			}
		} else { // co van de trong viec luu du lieu, vi du luu khong dung format theo quy dinh
			return $vSYMBOL.Fillnum("1",$vLengthCast);
		}
	}
	return $vReturn;
}
function getArrValue($vTableName, $vFiled) {
	$vNum = 0;
	$vArrReturn = "";
	$sqlS = "SELECT $vFiled FROM $vTableName WHERE 1=1 ORDER BY $vFiled;";
	$bResultS = db_query($sqlS);
	$totalRows = db_num_rows($bResultS);
	if ($totalRows > 0) {
		while ($arrS = db_fetch_array($bResultS)) {
			$arrTemp = $arrS[0].'@';
			$vArrReturn = $vArrReturn.$arrTemp;
			$vNum++;
		}
		if ((int)$vNum == 1) {///truong hop co 1 record
			$bResultS1 = db_query($sqlS);
			$arrS1 = db_fetch_array($bResultS1);
			$vArrReturn = $arrS1[0];
		}
	} else {
		return NULL;
	}
	$vArrReturn = substr($vArrReturn, 0, strlen($vArrReturn)-1);
	return $vArrReturn; /// Day la dang chuoi, vd: EST001@EST002@EST004@EST006@EST007
}
	/* Co the xu ly nhu sau de co duoc mang chua cac phan tu, vd: $arrTemp[0] = 001 */
	/*
			$arrTemp = substr($arrTemp, 0, strlen($arrTemp)-1);// Bo di ky tu cuoi chuoi.
			$arrTemp = str_replace("EST", "", $arrTemp); // Xoa di cac ky tu dac biet.
			$arrTemp = explode("@", $arrTemp); /// Tach tung phan tu vao mang theo ky tu dac biet.
	*/
function InsertWithCheckExtCONVERT($vTableName, $vField, $vSYMBOL,$vLengthCast){	
$vReturn = "";
	$sqlS = "	SELECT 
					(SELECT MAX(CAST(SUBSTRING($vField, ".(strlen(unicode_to_none($vSYMBOL))+1).") AS SIGNED)) FROM $vTableName) ROWMAX, 
					(SELECT COUNT(*) FROM $vTableName) AS NUMROW;";
	$bResultS = db_query($sqlS);
	$totalRows = db_num_rows($bResultS);
	if ($totalRows>0) {
		$arrS = db_fetch_array($bResultS);
		$vRowMax = $arrS['ROWMAX'];
		$vNumRow = $arrS['NUMROW'];
		if ((int)$vNumRow == (int)$vRowMax) {
			$vRowMax = (int)$vRowMax + 1;
			$vRowMax = Fillnum($vRowMax,$vLengthCast);
			$vReturn = $vReturn.$vSYMBOL.$vRowMax;
		} else if((int)$vNumRow < (int)$vRowMax) {
			$vNumReturn = 0;
			$arrTemp = "@".getArrValue($vTableName, $vField)."@";
			for ($i=1; $i<(int)$vRowMax; $i++) {
				$vNumReturn=$vSYMBOL.Fillnum($i,$vLengthCast);
				if(strpos($arrTemp,"@".$vNumReturn."@")===false) {
					return $vNumReturn;
				}
			}
		} else { // co van de trong viec luu du lieu, vi du luu khong dung format theo quy dinh
			return $vSYMBOL.Fillnum("1",$vLengthCast);
		}
	}
	return $vReturn;
}
function InsertWithCheckExt($vTableName, $vField, $vSYMBOL,$vLengthCast){
	$vReturn = "";
	$sqlS = "	SELECT 
					(SELECT MAX(CAST(SUBSTRING($vField, ".(strlen($vSYMBOL)+1).") AS SIGNED)) FROM $vTableName) ROWMAX, 
					(SELECT COUNT(*) FROM $vTableName) AS NUMROW;";
	$bResultS = db_query($sqlS);
	$totalRows = db_num_rows($bResultS);
	if ($totalRows>0) {
		$arrS = db_fetch_array($bResultS);
		$vRowMax = $arrS['ROWMAX'];
		$vNumRow = $arrS['NUMROW'];
		if ((int)$vNumRow == (int)$vRowMax) {
			$vRowMax = (int)$vRowMax + 1;
			$vRowMax = Fillnum($vRowMax,$vLengthCast);
			$vReturn = $vReturn.$vSYMBOL.$vRowMax;
		} else if((int)$vNumRow < (int)$vRowMax) {
			$vNumReturn = 0;
			$arrTemp = "@".getArrValue($vTableName, $vField)."@";
			for ($i=1; $i<(int)$vRowMax; $i++) {
				$vNumReturn=$vSYMBOL.Fillnum($i,$vLengthCast);
				if(strpos($arrTemp,"@".$vNumReturn."@")===false) {
					return $vNumReturn;
				}
			}
		} else { // co van de trong viec luu du lieu, vi du luu khong dung format theo quy dinh
			return $vSYMBOL.Fillnum("1",$vLengthCast);
		}
	}
	return $vReturn;
}
function InsertWithCheckExt2($vTableName, $vField, $vSYMBOL,$vLengthCast,$vField2,$vValue2){
	$vReturn = "";
	$sqlS = "	SELECT 
					(SELECT MAX(CAST(SUBSTRING($vField, ".(strlen($vSYMBOL)+1).") AS SIGNED)) FROM $vTableName where $vField2='$vValue2') ROWMAX, 
					(SELECT COUNT(*) FROM $vTableName where $vField2='$vValue2' ) AS NUMROW;";
	$bResultS = db_query($sqlS);
	$totalRows = db_num_rows($bResultS);
	if ($totalRows>0) {
		$arrS = db_fetch_array($bResultS);
		$vRowMax = $arrS['ROWMAX'];
		$vNumRow = $arrS['NUMROW'];
		if ((int)$vNumRow >= (int)$vRowMax) {
			$vRowMax = (int)$vRowMax + 1;
			$vRowMax = Fillnum($vRowMax,$vLengthCast);
			$vReturn = $vReturn.$vSYMBOL.$vRowMax;
		} else if((int)$vNumRow < (int)$vRowMax) {
			$vNumReturn = 0;
			$arrTemp = "@".getArrValue3($vTableName, $vField,$vSYMBOL,$vLengthCast,$vField2,$vValue2)."@";			
			for ($i=1; $i<(int)$vRowMax; $i++) {
				$vNumReturn=$vSYMBOL.Fillnum($i,$vLengthCast);
				if(strpos($arrTemp,"@".$vNumReturn."@")===false) {
					return $vNumReturn;
				}
			}
		} else { // co van de trong viec luu du lieu, vi du luu khong dung format theo quy dinh
			return $vSYMBOL.Fillnum("1",$vLengthCast);
		}
	}
	return $vReturn;
}
function InsertWithCheckExt3($vTableName, $vField, $vSYMBOL,$vLengthCast,$vField2,$vValue2,$vValue3){
	$vReturn = "";
	$sqlS = "	SELECT (SELECT MAX(CAST(SUBSTRING($vField, ".(strlen($vSYMBOL)+1).") AS SIGNED)) FROM $vTableName where ($vField LIKE '$vSYMBOL%$vValue3') AND $vField2='$vValue2') ROWMAX, (SELECT COUNT(*) FROM $vTableName where ($vField LIKE '$vSYMBOL%$vValue3') AND $vField2='$vValue2' ) AS NUMROW;";
	$bResultS = db_query($sqlS);
	$totalRows = db_num_rows($bResultS);
	if ($totalRows>0) {
		$arrS = db_fetch_array($bResultS);
		$vRowMax = $arrS['ROWMAX'];
		$vNumRow = $arrS['NUMROW'];
		if ((int)$vNumRow >= (int)$vRowMax) {
			$vRowMax = (int)$vRowMax + 1;
			$vRowMax = Fillnum($vRowMax,$vLengthCast);
			$vReturn = $vReturn.$vSYMBOL.$vRowMax;
		} else if((int)$vNumRow < (int)$vRowMax) {
			$vNumReturn = 0;
			$arrTemp = getArrValue3($vTableName, $vField,$vSYMBOL,$vLengthCast,$vField2,$vValue2,$vValue3)."@";
			for ($i=1; $i<(int)$vRowMax; $i++) {
				$vNumReturn=$vSYMBOL.Fillnum($i,$vLengthCast).$vValue3;
				if(strpos($arrTemp,$vNumReturn."@")===false) {
					return $vNumReturn;
				}
			}
		} else { // co van de trong viec luu du lieu, vi du luu khong dung format theo quy dinh
			return $vSYMBOL.Fillnum("1",$vLengthCast);
		}
	}
	return $vReturn;
}
function getArrValue3($vTableName, $vFiled, $vSYMBOL,$vLengthCast,$vField2,$vValue2,$vValue3) {
	$vNum = 0;
	$vArrReturn = "";
	$sqlS = "SELECT $vFiled FROM $vTableName WHERE 1=1 AND ($vFiled LIKE '$vSYMBOL%$vValue3') AND $vField2='$vValue2' ORDER BY $vFiled;";
	$bResultS = db_query($sqlS);
	$totalRows = db_num_rows($bResultS);
	if ($totalRows > 0) {
		while ($arrS = db_fetch_array($bResultS)) {
			$arrTemp = $arrS[0].'@';
			$vArrReturn = $vArrReturn.$arrTemp;
			$vNum++;
		}
		if ((int)$vNum == 1) {///truong hop co 1 record
			$bResultS1 = db_query($sqlS);
			$arrS1 = db_fetch_array($bResultS1);
			$vArrReturn = $arrS1[0];
		}
	} else {
		return NULL;
	}
	$vArrReturn = substr($vArrReturn, 0, strlen($vArrReturn)-1);
	return $vArrReturn; /// Day la dang chuoi, vd: EST001@EST002@EST004@EST006@EST007
}
//Hàm công/trừ thêm giá trị ngày
	function LV_DATE_ADD($vDate,$vValue)
	{
		$vsql="select DATE_ADD('$vDate', INTERVAL $vValue DAY) as GDate";
		$tresult=db_query($vsql);
		$trow=db_fetch_array($tresult);
		return  $trow['GDate'];
	}
	///Hàm  phân tích body hiển thị lên link nối kết website
	function AnalysisLink($vBody,$pPara1,$pPara2,$pLink)
	{
		$strTemp=str_replace("<br />"," ",$vBody);
		$strTemp=str_replace("<br/>"," ",$strTemp);		
		$strTemp=" ".str_replace("<br>"," ",$strTemp)." ";		
		$vArrTemp=explode($pPara1,$strTemp);
		$strFinish="";
		for($i=1;$i<count($vArrTemp);$i++)
		{
			$vArrChild=explode($pPara2,$vArrTemp[$i]);
			if(strpos($strFinish,trim($pPara1.$vArrChild[0]))===false) 
			{
				$strFinish=$strFinish."@".trim($pPara1.$vArrChild[0]);
				$vBody=str_replace(trim($pPara1.$vArrChild[0]),str_replace("@01",trim($pPara1.$vArrChild[0]),$pLink),$vBody);
			}
		}
		return $vBody;
	}
	function explodeTo($vBody,$pPara1,$pPara2,$vPara3)
	{
		$strTemp=$vBody;
		$vArrTemp=explode($pPara1,$strTemp);
		$strReturn="";
		if(count($vArrTemp)==1) return $vBody;
		for($i=1;$i<count($vArrTemp);$i++)
		{
			$vArrChild=explode($pPara2,$vArrTemp[$i]);
			if($strReturn!="")
				$strReturn=$strReturn.$vPara3.trim($vArrChild[0]);
			else
				$strReturn=$strReturn.trim($vArrChild[0]);			
		}
		return $strReturn;
	}
	function ProcessEmail($vBody,$pPara1,$pPara2,$vPara3)
	{
		$strTemp=$vBody;
		$vArrTemp=explode($pPara1,$strTemp."",2);
		$strReturn="";
		if(count($vArrTemp)==1) 
		{
			$vArrTemp=explode($pPara2,$strTemp."",2);
			if(count($vArrTemp)==1) 
			{
				$vArrTemp=explode($vPara3,$strTemp."",2);
				if(count($vArrTemp)==1)		return str_replace("\n","<br>",$vBody);
				else
				$vArrTemp[1]=$vPara3.$vArrTemp[1];
			}
			else
			$vArrTemp[1]=$pPara2.$vArrTemp[1];
		}
		else
		{
		$vArrTemp[1]=$pPara1.$vArrTemp[1];
		}
		$strReturn=str_replace("\n","<br>",$vArrTemp[0]).$vArrTemp[1];	
		return $strReturn;
	}	
function GetLangSort($vControl,$vlang)
{
	 if(strtolower($vlang)=="en")
		{
			$arr = array('0'=>"Sort","1"=>"None",2=>"Increment",'3'=>"Desc");		
		}
	else
		{
			$arr = array('0'=>"Sắp xếp","1"=>"Bình thường",2=>"Tăng dần",'3'=>"Giảm dần");
		}
		return $arr[$vControl];

}	
function unicode_to_case($str,$vop=0)
{
	$str = preg_replace("/(ầ|ặ|à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ|ằ)/", 'a', $str);
$str = preg_replace("/(ệ|ế|ễ|è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ|ệ)/", 'e', $str);
$str = preg_replace("/(ì|í|ị|ỉ|ĩ|ị)/", 'i', $str);
$str = preg_replace("/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/", 'o', $str);
$str = preg_replace("/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ|ũ)/", 'u', $str);
$str = preg_replace("/(ỳ|ý|ỵ|ỷ|ỹ|ỳ)/", 'y', $str);
$str = preg_replace("/(đ)/", 'd', $str);
$str = preg_replace("/(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ)/", 'A', $str);
$str = preg_replace("/(Ẽ|È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)/", 'E', $str);
$str = preg_replace("/(Ì|Í|Ị|Ỉ|Ĩ)/", 'I', $str);
$str = preg_replace("/(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ)/", 'O', $str);
$str = preg_replace("/(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)/", 'U', $str);
$str = preg_replace("/(Ỳ|Ý|Ỵ|Ỷ|Ỹ)/", 'Y', $str);
$str = preg_replace("/(Đ)/", 'D', $str);
//$str = str_replace(" ", " ", str_replace("&*#39;","",$str));
if($vop==1)
	$str=strtolower($str);
else
	$str=strtoupper($str);
return $str;
}
function unicode_to_none($fac_contetn)
{
	$arr_uni=array('À','Ô','Ă','Ó','Ú','É','Õ','Ê','Ũ','Ẹ','Ù' ,'È','Ì','Í','Ò','Đ','Ũ','Ý','Ố','Â','Ĩ','Ị','Á','Ú','Ạ','Ộ','Ể','Ụ','Ủ','Ệ','Ã','Ò','Ậ','Ề','Ã','Ế','Ả','Ụ','Ọ','Ễ','Ó','Ỏ','Ầ','Ẫ','Ằ','Ẩ','Ọ','Ẳ','Ẽ','Ỏ','Õ','Ấ','Ũ','Ồ','Ỗ','Ỳ','Ơ','Ặ','Ư','Ờ','Ỹ','Ắ','Ở','Ứ','Ő');
	  $UNICODE_UPPER_ACCENTS = array('À'=>'A' ,'Ô'=>'O',
  'Ă'=>'A','Ó'=>'O','Ú'=>'U','É'=>'E','Õ'=>'O','Ê'=>'E','Ű'=>'U','Ẹ'=>'E','Ù'=>'U' ,'È'=>'E','Ì'=>'I','Í'=>'I' ,'Ò'=>'O','Đ'=>'D' ,'Ũ'=>'U','Ý'=>'Y','Ő'=>'O','Â'=>'A','Ĩ'=>'I','Ị'=>'I' ,'Á'=>'A','Ứ'=>'U','Ạ'=>'A','Ộ'=>'O','Ể'=>'E','Ụ'=>'U','Ủ'=>'U','Ệ'=>'E','Ã'=>'A','Ở'=>'O','Ậ'=>'A','Ề'=>'E','Ã'=>'A','Ế'=>'E','Ả'=>'A','Ự'=>'U','Ợ'=>'O','Ễ'=>'E','Ớ'=>'O','Ở'=>'O','Ầ'=>'A','Ẫ'=>'A','Ằ'=>'A','Ổ'=>'O','Ọ'=>'O','Ẳ'=>'A','Ẽ'=>'E','Ỏ'=>'O','Ỡ'=>'O','Ấ'=>'A','Ữ'=>'U','Ồ'=>'O','Ỗ'=>'O','Ỳ'=>'Y','Ơ'=>'O','Ặ'=>'A','Ư'=>'U','Ờ'=>'O','Ỹ'=>'Y','Ắ'=>'A','Ở'=>'O','Ứ'=>'U','Ő'=>'O');
	  foreach ($arr_uni as $uni)
	  {
		$fac_contetn=str_replace($uni,$UNICODE_UPPER_ACCENTS[$uni],$fac_contetn);
	  }
	return $fac_contetn;
}
function unicode_to_upper($fac_contetn)
{
	$arr_uni=array('ẩ','à','ô','ơ','ă','ó','ú','é','õ','ê','ũ','ẹ','ù' ,'è','ì','í','ò','đ','ũ','ư','ý','ố','â','ĩ','ị','á','ứ','ạ','ộ','ể','ụ','ủ','ệ','ã','ờ','ậ','ề','ã','ế','ả','ự','ợ','ễ','ớ','ở','ầ','ẫ','ằ','ổ','ọ','ẳ','ẽ','ỏ','ỡ','ấ','ữ','ồ','ỗ','a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','w','v','u','x','y','ặ','ắ','ỹ','ỳ');
	  $UNICODE_UPPER_ACCENTS = array('ẩ'=>'Ẩ','à'=>'À' ,'ô'=>'Ô','ơ'=>'Ơ',
  'ă'=>'Ă','ó'=>'Ó','ú'=>'Ú','é'=>'É','õ'=>'Õ','ê'=>'Ê','ũ'=>'Ű','ẹ'=>'Ẹ','ù'=>'Ù' ,'è'=>'È','ì'=>'Ì','í'=>'Í' ,'ò'=>'Ò','đ'=>'Đ' ,'ũ'=>'Ũ','ư'=>'Ư','ý'=>'Ý','ố'=>'Ő','â'=>'Â','ĩ'=>'Ĩ','ị'=>'Ị' ,'á'=>'Á','ứ'=>'Ứ','ạ'=>'Ạ','ộ'=>'Ộ','ể'=>'Ể','ụ'=>'Ụ','ủ'=>'Ủ','ệ'=>'Ệ','ã'=>'Ã','ờ'=>'Ở','ậ'=>'Ậ','ề'=>'Ề','ã'=>'Ã','ế'=>'Ế','ả'=>'Ả','ự'=>'Ự','ợ'=>'Ợ','ễ'=>'Ễ','ớ'=>'Ớ','ở'=>'Ở','ầ'=>'Ầ','ẫ'=>'Ẫ','ằ'=>'Ằ','ổ'=>'Ổ','ọ'=>'Ọ','ẳ'=>'Ẳ','ẽ'=>'Ẽ','ỏ'=>'Ỏ','ỡ'=>'Ỡ','ấ'=>'Ấ','ữ'=>'Ữ','ồ'=>'Ồ','ỗ'=>'Ỗ','a'=>'A','b'=>'B','c'=>'C','d'=>'D','e'=>'E','f'=>'F','g'=>'G','h'=>'H','i'=>'I','j'=>'J','k'=>'K','l'=>'L','m'=>'M','n'=>'N','o'=>'O','p'=>'P','q'=>'Q','r'=>'R','s'=>'S','t'=>'T','w'=>'W','v'=>'V','u'=>'U','x'=>'X','y'=>'Y','ặ'=>'Ặ','ắ'=>'Ắ','ỹ'=>'Ỹ','ỳ'=>'Ỳ');
	  foreach ($arr_uni as $uni)
	  {
		$fac_contetn=str_replace($uni,$UNICODE_UPPER_ACCENTS[$uni],$fac_contetn);
	  }
	return $fac_contetn;
}   
 function is_valid_email($email)
    {
     if(preg_match('/[^\x80-\xF7 a-z0-9@_.\'-]/i', $email) > 0)
        {
          return true;
        }
          else
            {
          return false;
        }
    }
?>