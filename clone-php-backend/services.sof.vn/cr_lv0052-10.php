<?php
/////////////coding cr_lv0052///////////////
class   cr_lv0052 extends lv_controler
{
	public $lv001=null;
	public $lv002=null;
	public $lv003=null;
	public $lv004=null;
///////////
	//public $DefaultFieldList="lv001,lv115,lv608,lv612,lv609,lv610,lv649,lv005,lv015,lv016,lv017,lv018,lv019,lv020,lv021,lv022,lv023,lv024,lv025,lv026,lv027,lv028,lv029,lv030,lv031,lv006,lv007,lv008,lv009,lv010,lv011,lv012,lv013,lv014,lv003,lv004";	
	public $DefaultFieldList="lv199,lv001,lv115,lv009,lv608,lv612,lv609,lv610,lv649,lv008,lv652,lv005,lv025,lv026,lv028,lv029,lv030,lv031,lv018,lv020,lv021,lv006,lv007,lv027,lv015,lv016,lv017,lv022,lv023,lv024,lv019,lv010,lv011,lv012,lv013,lv014,lv003,lv004";	
////////////////////GetDate
	public $DateCurrent="1900-01-01";
	public $Count=null;
	public $paging=null;
	public $lang=null;
	protected $objhelp='cr_lv0052';
	public $Dir="";
////////////
	var $ArrOther=array();
	var $ArrPush=array();
	var $ArrFunc=array();
	var $ArrGet=array("lv001"=>"2","lv002"=>"3","lv003"=>"4","lv004"=>"5","lv005"=>"6","lv006"=>"7","lv007"=>"8","lv008"=>"9","lv009"=>"10","lv010"=>"11","lv011"=>"12","lv012"=>"13","lv013"=>"14","lv014"=>"15","lv015"=>"16","lv016"=>"17","lv017"=>"18","lv018"=>"19","lv019"=>"20","lv020"=>"21","lv021"=>"22","lv022"=>"23","lv023"=>"24","lv024"=>"25","lv025"=>"26","lv026"=>"27","lv027"=>"28","lv028"=>"29","lv029"=>"30","lv030"=>"31","lv031"=>"32","lv115"=>"116","lv608"=>"609","lv609"=>"610","lv610"=>"611","lv612"=>"613","lv649"=>"650","lv652"=>"653","lv199"=>"200");
	var $ArrView=array("lv001"=>"0","lv002"=>"0","lv003"=>"0","lv004"=>"0","lv005"=>"0","lv006"=>"2","lv007"=>"2","lv008"=>"0","lv009"=>"0","lv010"=>"0","lv011"=>"0","lv012"=>"0","lv013"=>"0","lv014"=>"0","lv015"=>"0","lv016"=>"0","lv017"=>"0","lv018"=>"0","lv019"=>"0","lv020"=>"0","lv021"=>"0","lv022"=>"0","lv023"=>"0","lv024"=>"0","lv025"=>"0","lv026"=>"0","lv027"=>"2","lv028"=>"0","lv029"=>"0","lv030"=>"0","lv031"=>"0");
	var $ArrViewEnter=array("lv199"=>"-1","lv612"=>"-1","lv609"=>"-1","lv610"=>"-1","lv649"=>"-1","lv001"=>"-1","lv002"=>"99","lv003"=>"-1","lv004"=>"-1","lv005"=>"5","lv006"=>"2","lv007"=>"2","lv009"=>"33","lv010"=>"-1","lv011"=>"-1","lv012"=>"-1","lv013"=>"-1","lv014"=>"-1",'lv018'=>"999","lv115"=>"-1","lv026"=>"33","lv027"=>"2","lv028"=>"33","lv029"=>"33","lv608"=>"-1","lv609"=>"-1","lv610"=>"-1","lv649"=>"-1","lv612"=>"-1","lv025"=>"999");
	var $Tables=array('lv002'=>'cr_lv0004','lv003'=>'cr_lv0005','lv004'=>'cr_lv0031',"lv018"=>"cr_lv0332","lv025"=>"cr_lv0332");
	var $TableLink=array("lv002"=>"concat(lv001,@! @!,lv002)","lv004"=>"concat(lv001,@! @!,lv002)","lv018"=>"lv002","lv025"=>"lv002");
	var $TableLinkReturn=array("lv002"=>"lv001","lv004"=>"lv001","lv018"=>"lv002","lv025"=>"lv002");
	public $LE_CODE="NjlIUS02VFdULTZIS1QtNlFIQQ==";
	function __construct($vCheckAdmin,$vUserID,$vright)
	{
		$this->DateCurrent=GetServerDate()." ".GetServerTime();
		$this->Set_User($vCheckAdmin,$vUserID,$vright);
		$this->isRel=1;		
	 	$this->isHelp=1;	
		$this->isConfig=0;
		$this->isRpt=0;		
	 	$this->isFil=1;	
	
		$this->lang=$_GET['lang'];
		
	}
	function LV_SetTypeBH()
	{
		if($this->isTypeBH==1)
		{
			switch($this->TypeBH)
			{
				case 2:
					$this->DefaultFieldList="lv199,lv015,lv016,lv017,lv612,lv609,lv610,lv649,lv008,lv652,lv028,lv029,lv030,lv031";	
					break;
				case 1:
					$this->DefaultFieldList="lv199,lv015,lv016,lv017,lv006,lv018,lv019,lv008,lv025,lv026,lv027,lv020,lv021,lv022,lv023,lv007,lv024";	
					break;
				default:
					$this->DefaultFieldList="lv199,lv015,lv016,lv017,lv612,lv609,lv610,lv649,lv008,lv652,lv025,lv026,lv027,lv031";	
					break;
			}
		}
		else
		{
			
		}
	}
	function LV_CheckCate($vCateID)
	{
		$vsql="select * from  cr_lv0052 where lv001='$vCateID' and (lv003='' or lv001=lv001)";
		$vresult=db_query($vsql);
		while($vrow=db_fetch_array($vresult))
		{
			if($str_return=="")
				$str_return="'".$vrow['lv001']."'".$this->LV_CheckChildCate($vrow['lv001']);
			else 
				$str_return=$str_return.",'".$vrow['lv001']."'".$this->LV_CheckChildCate($vrow['lv001']);
		}
		return $str_return;
	}
	function LV_CheckChildCate($vCateID)
	{
		$vsql="select * from  cr_lv0052 where lv003='$vCateID'";
		$vresult=db_query($vsql);
		while($vrow=db_fetch_array($vresult))
		{
				$str_return=$str_return.",'".$vrow['lv001']."'".$this->LV_CheckChildCate($vrow['lv001']);
		}
		return $str_return;
	}
	function LV_Load()
	{
		$vsql="select * from  cr_lv0052";
		$vresult=db_query($vsql);
		$vrow=db_fetch_array($vresult);
		if($vrow)
		{
			$this->lv001=$vrow['lv001'];
			$this->lv002=$vrow['lv002'];
			$this->lv003=$vrow['lv003'];
			$this->lv004=$vrow['lv004'];
			$this->lv005=$vrow['lv005'];
			$this->lv006=$vrow['lv006'];
			$this->lv007=$vrow['lv007'];
			$this->lv008=$vrow['lv008'];
			$this->lv009=$vrow['lv009'];
			$this->lv010=$vrow['lv010'];
			$this->lv011=$vrow['lv011'];
			$this->lv012=$vrow['lv012'];
			$this->lv013=$vrow['lv013'];
			$this->lv014=$vrow['lv014'];
			$this->lv015=$vrow['lv015'];
			$this->lv016=$vrow['lv016'];
			$this->lv017=$vrow['lv017'];
			$this->lv018=$vrow['lv018'];
			$this->lv019=$vrow['lv019'];
			$this->lv020=$vrow['lv020'];
			$this->lv021=$vrow['lv021'];
			$this->lv022=$vrow['lv022'];
			$this->lv023=$vrow['lv023'];
			$this->lv024=$vrow['lv024'];
			$this->lv025=$vrow['lv025'];
			$this->lv026=$vrow['lv026'];
			$this->lv027=$vrow['lv027'];
			$this->lv028=$vrow['lv028'];
			$this->lv029=$vrow['lv029'];
			$this->lv030=$vrow['lv030'];
			$this->lv031=$vrow['lv031'];
		}
	}
	function LV_LoadID($vlv001)
	{
		$lvsql="select * from  cr_lv0052 Where lv001='$vlv001'";
		$vresult=db_query($lvsql);
		$vrow=db_fetch_array($vresult);
		if($vrow)
		{
			$this->lv001=$vrow['lv001'];
			$this->lv002=$vrow['lv002'];
			$this->lv003=$vrow['lv003'];
			$this->lv004=$vrow['lv004'];
			$this->lv005=$vrow['lv005'];
			$this->lv006=$vrow['lv006'];
			$this->lv007=$vrow['lv007'];
			$this->lv008=$vrow['lv008'];
			$this->lv009=$vrow['lv009'];
			$this->lv010=$vrow['lv010'];
			$this->lv011=$vrow['lv011'];
			$this->lv012=$vrow['lv012'];
			$this->lv013=$vrow['lv013'];
			$this->lv014=$vrow['lv014'];
			$this->lv015=$vrow['lv015'];
			$this->lv016=$vrow['lv016'];
			$this->lv017=$vrow['lv017'];
			$this->lv018=$vrow['lv018'];
			$this->lv019=$vrow['lv019'];
			$this->lv020=$vrow['lv020'];
			$this->lv021=$vrow['lv021'];
			$this->lv022=$vrow['lv022'];
			$this->lv023=$vrow['lv023'];
			$this->lv024=$vrow['lv024'];
			$this->lv025=$vrow['lv025'];
			$this->lv026=$vrow['lv026'];
			$this->lv027=$vrow['lv027'];
			$this->lv028=$vrow['lv028'];
			$this->lv029=$vrow['lv029'];
			$this->lv030=$vrow['lv030'];
			$this->lv031=$vrow['lv031'];
		}
	}
	function LV_LoadArr($vType=0)
	{
		$lvsql="select * from  cr_lv0052 Where lv010='$vType'";
		$vresult=db_query($lvsql);
		while($vrow=db_fetch_array($vresult))
		{
			$this->ArrListCot[$vrow['lv001']][0]=$vrow['lv007'];
			$this->ArrListCot[$vrow['lv001']][1]=$vrow['lv001'];
		}
	}
	function LV_Insert()
	{
		if($this->isAdd==0) return false;
		$this->lv006 = ($this->lv006!="")?recoverdate(($this->lv006), $this->lang):$this->DateDefault;
		$this->lv007 = ($this->lv007!="")?recoverdate(($this->lv007), $this->lang):$this->DateDefault;
		//////////////PBH
		$this->lv115 = $this->LV_AutoInsertUpdateGroup($this->PBHID,$this->CUSID,$this->lv006);
		$lvsql="insert into cr_lv0052 (lv001,lv002,lv003,lv004,lv005,lv006,lv007,lv008,lv009,lv013,lv014,lv015,lv016,lv017,lv018,lv019,lv020,lv021,lv022,lv023,lv024,lv025,lv026,lv027,lv028,lv029,lv030,lv031,lv115) values('".sof_escape_string($this->lv001)."','".sof_escape_string($this->lv002)."','".sof_escape_string($this->lv003)."','".sof_escape_string($this->lv004)."','".sof_escape_string($this->lv005)."','".sof_escape_string($this->lv006)."','".sof_escape_string($this->lv007)."','".sof_escape_string($this->lv008)."','".sof_escape_string($this->lv009)."','$this->LV_UserID',now(),'".sof_escape_string($this->lv015)."','".sof_escape_string($this->lv016)."','".sof_escape_string($this->lv017)."','".sof_escape_string($this->lv018)."','".sof_escape_string($this->lv019)."','".sof_escape_string($this->lv020)."','".sof_escape_string($this->lv021)."','".sof_escape_string($this->lv022)."','".sof_escape_string($this->lv023)."','".sof_escape_string($this->lv024)."','".sof_escape_string($this->lv025)."','".sof_escape_string($this->lv026)."','".sof_escape_string($this->lv027)."','".sof_escape_string($this->lv028)."','".sof_escape_string($this->lv029)."','".sof_escape_string($this->lv030)."','".sof_escape_string($this->lv031)."','$this->lv115')";
		$vReturn= db_query($lvsql);
		if($vReturn) $this->InsertLogOperation($this->DateCurrent,'cr_lv0052.insert',sof_escape_string($lvsql));
		return $vReturn;
	}	
	function LV_InsertAuto()
	{		
		if($this->isAdd==0) return false;
		$this->lv006 = ($this->lv006!="")?recoverdate(($this->lv006), $this->lang):$this->DateDefault;
		$this->lv007 = ($this->lv007!="")?recoverdate(($this->lv007), $this->lang):$this->DateDefault;
		//////////////PBH
		$this->lv115 = $this->LV_AutoInsertUpdateGroup($this->PBHID,$this->CUSID,$this->lv004);
		$lvsql="insert into cr_lv0052 (lv001,lv002,lv003,lv004,lv005,lv006,lv007,lv008,lv009,lv013,lv014,lv015,lv016,lv017,lv018,lv019,lv020,lv021,lv022,lv023,lv024,lv025,lv026,lv027,lv028,lv029,lv030,lv031,lv115) values('".sof_escape_string($this->lv001)."','".sof_escape_string($this->lv002)."','".sof_escape_string($this->lv003)."','".sof_escape_string($this->lv004)."','".sof_escape_string($this->lv005)."','".sof_escape_string($this->lv006)."','".sof_escape_string($this->lv007)."','".sof_escape_string($this->lv008)."','".sof_escape_string($this->lv009)."','$this->LV_UserID',now(),'".sof_escape_string($this->lv015)."','".sof_escape_string($this->lv016)."','".sof_escape_string($this->lv017)."','".sof_escape_string($this->lv018)."','".sof_escape_string($this->lv019)."','".sof_escape_string($this->lv020)."','".sof_escape_string($this->lv021)."','".sof_escape_string($this->lv022)."','".sof_escape_string($this->lv023)."','".sof_escape_string($this->lv024)."','".sof_escape_string($this->lv025)."','".sof_escape_string($this->lv026)."','".sof_escape_string($this->lv027)."','".sof_escape_string($this->lv028)."','".sof_escape_string($this->lv029)."','".sof_escape_string($this->lv030)."','".sof_escape_string($this->lv031)."','$this->lv115')";
		$vReturn= db_query($lvsql);
		if($vReturn) $this->InsertLogOperation($this->DateCurrent,'sl_lv0009.insert',sof_escape_string($lvsql));
		return $vReturn;
	}	
	function LV_LoadMaOld($lv099)
	{
		$lvsql="select lv001 from  cr_lv0052 Where lv099='$lv099'";
		$vresult=db_query($lvsql);
		$vrow=db_fetch_array($vresult);
		if($vrow)
		{
			$this->lv001=$vrow['lv001'];
		}
		else
		{
			$this->lv001=null;
		}
		return $this->lv001;
	}
	//Lấy data
	function LV_GetDataAuto()
	{
		//Tạo lệnh sản xuất từ SAP.
		//Điều kiện sản xuất
		//Biểu diễn lệnh sản xuất, Mã Lệnh, Mã Lệnh SAP, 		
		$link = sqlsrv_connect($this->Server, $this->connectionOptions);
		if(!$link)
		{
			print_r(sqlsrv_errors());
			return;
		}
		//SELECT [id],[order_id],[reservation],[material],[plant],[sloc],[redate],[quant],[unit],[wqty],[wvalue],[cur],[une],[uentry],[pegre] FROM [erp_stk].[dbo].[sap_bom]	
		//$bResult = db_query($sqlS);
		$vCondition="";
		$lvsql = "SELECT [WELL_ID] lv099
		,[WELL_CODE] lv001
		,[WELL_NAME] lv002
		,[WELL_ACTIVE] lv003
		,[WELL_REMARK] lv004
		FROM [ContractManagement].[dbo].[WELL]
  		where 1=1 $vCondition";
		$vresult=sqlsrv_query($link,$lvsql);
		$i=0;
		while($vrow=sqlsrv_fetch_array($vresult))
		{
			$vOldID=$this->LV_LoadMaOld($vrow['lv099']);
			if($vOldID==null)
			{
				$this->lv001=$vrow['lv001'];
				$this->lv002=$vrow['lv002'];
				$this->lv003=$vrow['lv003'];
				$this->lv004=$vrow['lv004'];
				$this->lv099=$vrow['lv099'];
				$this->LV_InsertAuto();
			}
		}
		//Biễu diễn chi tiết sản xuất. 
	}
	function LV_AutoInsertUpdateGroup($vPBHID,$vMaKHID,$vDateFrom)
	{
		if(trim($vMaKHID)=='') return '';
		////////
		///////Kiểm tr
		$vVRun=true;
		$lvsql="select A.lv001 from cr_lv0330 A where A.lv115='$vPBHID' and A.lv002='$vMaKHID' and A.lv004<='$vDateFrom' and A.lv005>='$vDateFrom' and A.lv006=0";
		$vresult=db_query($lvsql);
		while($vrow=db_fetch_array($vresult))
		{
			return $vrow['lv001'];
		}	
		if($vVRun)
		{
			///////Tạo ra nhóm công việc mới cho khoản trong tháng đó
			$vJobID=$this->JobID;
			$vNguoiGV=$this->LV_UserID;
			$vNguoiDuyet='';
			//
			$vJobIDRun=$this->mocr_lv0092->LV_CheckCreateJob($vPBHID,$vJobID,'BHPBH',$vNguoiGV,$vNguoiDuyet);
			$vMonth=getmonth($vDateFrom);
			$vYear=getyear($vDateFrom);
			$vDateFrom=$vYear.'-'.$vMonth.'-01';
			$vDateTo=$vYear.'-'.$vMonth.'-'.GetDayInMonth($vYear,$vMonth);
			$vMaGPHM=InsertWithCheckFist('cr_lv0330', 'lv001', '/BBNBH/MP'.substr(getyear($this->DateCurrent),-2,2),4);
			$vMaCV='';
			$lvsql="insert into cr_lv0330 (lv001,lv002,lv003,lv004,lv005,lv006,lv007,lv008,lv009,lv010,lv011,lv114,lv115) values('$vMaGPHM','$vMaKHID','','$vDateFrom','$vDateTo','0','$this->lv007','$this->lv008','$this->lv009','$this->LV_UserID',now(),'$vJobIDRun','$vPBHID')";
			$vReturn= db_query($lvsql);
			if($vReturn)
			{
				return $vMaGPHM;
			}
			else
			{
				$lvsql="insert into cr_lv0330 (lv001,lv002,lv003,lv004,lv005,lv006,lv007,lv008,lv009,lv010,lv011,lv114,lv115) values('$vMaGPHM','$vMaKHID','','$vDateFrom','$vDateTo','0','$this->lv007','$this->lv008','$this->lv009','$this->LV_UserID',now(),'$vJobIDRun','$vPBHID')";
				$vReturn= db_query($lvsql);
				if($vReturn)
				{
					return $vMaGPHM;
				}
				
			}
		}
		echo 'Liên hệ administrator để kiểm tra!';
		return '';
	}
	function LV_UpdateSQL($lvsql)
	{
		if($this->isEdit==0) return false;
		$vReturn= db_query($lvsql);
		if($vReturn) $this->InsertLogOperation($this->DateCurrent,'cr_lv0052.update',sof_escape_string($lvsql));
		return $vReturn;
	}
	function LV_Update()
	{
		if($this->isEdit==0) return false;
		$this->lv006 = ($this->lv006!="")?recoverdate(($this->lv006), $this->lang):$this->DateDefault;
		$this->lv007 = ($this->lv007!="")?recoverdate(($this->lv007), $this->lang):$this->DateDefault;
		$lvsql="Update cr_lv0052 set lv002='$this->lv002',lv003='$this->lv003',lv004='$this->lv004',lv005='$this->lv005',lv006='$this->lv006',lv007='$this->lv007',lv008='$this->lv008',lv009='$this->lv009',lv015='".sof_escape_string($this->lv015)."',lv016='".sof_escape_string($this->lv016)."',lv017='".sof_escape_string($this->lv017)."',lv018='".sof_escape_string($this->lv018)."',lv019='".sof_escape_string($this->lv019)."',lv020='".sof_escape_string($this->lv020)."',lv021='".sof_escape_string($this->lv021)."',lv022='".sof_escape_string($this->lv022)."',lv023='".sof_escape_string($this->lv023)."',lv024='".sof_escape_string($this->lv024)."',lv025='".sof_escape_string($this->lv025)."',lv026='".sof_escape_string($this->lv026)."',lv027='".sof_escape_string($this->lv027)."',lv028='".sof_escape_string($this->lv028)."',lv029='".sof_escape_string($this->lv029)."',lv030='".sof_escape_string($this->lv030)."',lv031='".sof_escape_string($this->lv031)."' where  lv001='$this->lv001' and lv010=0;";
		$vReturn= db_query($lvsql);
		if($vReturn) $this->InsertLogOperation($this->DateCurrent,'cr_lv0052.update',sof_escape_string($lvsql));
		return $vReturn;
	}
	function LV_Delete($lvarr)
	{
		if($this->isDel==0) return false;
		$lvsql = "DELETE FROM cr_lv0052  WHERE cr_lv0052.lv001 IN ($lvarr)";// and (select count(*) from cr_lv0052 B where  B.lv002= cr_lv0052.lv001)<=0  ";
		$vReturn= db_query($lvsql);
		if($vReturn) $this->InsertLogOperation($this->DateCurrent,'cr_lv0052.delete',sof_escape_string($lvsql));
		return $vReturn;
	}	
	function LV_Aproval($lvarr)
	{
		if($this->isApr==0) return false;
		$vSTT=0;
		$lvsql = "select lv001 from cr_lv0052  WHERE cr_lv0052.lv001 IN ($lvarr)  and lv010=0 ";
		$vresult=db_query($lvsql);
		while($vrow=db_fetch_array($vresult))
		{
			$vSTT++;
			$lvsql = "Update cr_lv0052 set lv010=1,lv011='$this->LV_UserID',lv012=now() WHERE cr_lv0052.lv001='".$vrow['lv001']."'  and lv010=0 ";
			$vReturn= db_query($lvsql);
			$this->InsertLogOperation($this->DateCurrent,'sl_lv0013.approval',sof_escape_string($lvsql));
			
		}
		
		return $vReturn;
	}	
	
	function LV_UnAproval($lvarr)
	{
		if($this->isUnApr==0) return false;
		$lvsql = "Update cr_lv0052 set lv010=0,lv011='$this->LV_UserID',lv012=now() WHERE cr_lv0052.lv001 IN ($lvarr)";
		$vReturn= db_query($lvsql);
		if($vReturn) 
		{
			$this->InsertLogOperation($this->DateCurrent,'sl_lv0013.unapproval',sof_escape_string($lvsql));
			//$this->LV_SetHistoryArr('UnApr',$lvarr);
		}
		return $vReturn;
	}
	/////lv admin deletet
	
	//////////get view///////////////
	function GetView()
	{
		return $this->isView;
	}//////////get view///////////////
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
		$strCondi="";
		switch($this->datetype)
		{
			case 0:
				if($this->datefrom!='')
				{
					$strCondi=$strCondi." and A.lv004 >= '".recoverdate($this->datefrom,$this->lang)."'";
				}
				if($this->dateto!='')
				{
					$strCondi=$strCondi." and A.lv004 <= '".recoverdate($this->dateto,$this->lang)."'";
				}
				break;
			case 1:
				if($this->datefrom!='')
				{
					$strCondi=$strCondi." and A.lv005 >= '".recoverdate($this->datefrom,$this->lang)."'";
				}
				if($this->dateto!='')
				{
					$strCondi=$strCondi." and A.lv005 <= '".recoverdate($this->dateto,$this->lang)."'";
				}
				break;
		}
		if($this->lv001!="") 
		{
			if(!strpos($this->lv001,',')===false)
			{	
				$strCondi1='';
				$vArrNameCus=explode(",",$this->lv001);
				foreach($vArrNameCus as $vNameCus)
				{
					if($vNameCus!="")
					{
					if($strCondi1=="")	
						$strCondi1= " AND ( A.lv001 = '$vNameCus'";
					else
						$strCondi1=$strCondi1." OR A.lv001 = '$vNameCus'";		
					}
				}
				if($strCondi1!='') $strCondi=$strCondi1.")";
				
			}
			else
			{
				$strCondi=$strCondi." and A.lv001  = '$this->lv001'";
			}
			
		}
		if($this->lv002!="") 
		{
			if(!strpos($this->lv002,',')===false)
			{	
				$strCondi1='';
				$vArrNameCus=explode(",",$this->lv002);
				foreach($vArrNameCus as $vNameCus)
				{
					if($vNameCus!="")
					{
					if($strCondi1=="")	
						$strCondi1= " AND ( A.lv002 = '$vNameCus'";
					else
						$strCondi1=$strCondi1." OR A.lv002 = '$vNameCus'";		
					}
				}
				if($strCondi1!='') $strCondi=$strCondi1.")";
				
			}
			else
			{
				$strCondi=$strCondi." and A.lv002  = '$this->lv002'";
			}
			
		}
		if($this->lv003!="") $strCondi=$strCondi." and A.lv003 like '%$this->lv003%'";
		if($this->lv004!="") $strCondi=$strCondi." and A.lv004 like '%$this->lv004%'";
		if($this->lv005!="") $strCondi=$strCondi." and A.lv005 like '%$this->lv005%'";
		if($this->lv006!="") $strCondi=$strCondi." and A.lv006 like '%$this->lv006%'";
		if($this->lv007!="") $strCondi=$strCondi." and A.lv007 like '%$this->lv007%'";
		if($this->lv008!="") $strCondi=$strCondi." and A.lv008 like '%$this->lv008%'";
		if($this->lv009!="") $strCondi=$strCondi." and A.lv009 like '%$this->lv009%'";
		if($this->lv010!="") $strCondi=$strCondi." and A.lv010 like '%$this->lv010%'";
		if($this->lv115!="") $strCondi=$strCondi." and A.lv115 = '$this->lv115'";
		
		return $strCondi;
	}
		////////////////Count///////////////////////////
	function GetCount()
	{
		$sqlC = "SELECT COUNT(*) AS nums FROM cr_lv0052 A WHERE 1=1 ".$this->GetCondition();
		$bResultC = db_query($sqlC);
		$arrRowC = db_fetch_array($bResultC);
		return $arrRowC['nums'];
	}
	
	function LV_ReadXuLyAuto($lstArr,$vrow)
	{

		if($vrow['lv612']=='' || $vrow['lv612']==null)
		{	
			if(trim($vrow['lv612'])=='' || $vrow['lv612']==null)
				{
					$vrow['lv612']=$vrow['lv512'];
					$vrow['lv609']=$vrow['lv509'];
					$vrow['lv610']=$vrow['lv510'];
					$vrow['lv649']=$vrow['lv549'];
					$vrow['lv652']=$vrow['lv552'];
				}
			$this->ArrViewEnter=array("lv199"=>"-1","lv001"=>"-1","lv002"=>"99","lv003"=>"-1","lv004"=>"-1","lv005"=>"5","lv006"=>"2","lv007"=>"2","lv009"=>"33","lv010"=>"-1","lv011"=>"-1","lv012"=>"-1","lv013"=>"-1","lv014"=>"-1",'lv018'=>"999","lv115"=>"-1","lv026"=>"33","lv027"=>"2","lv028"=>"33","lv029"=>"33","lv025"=>"999");
		}
		else
		{
			$this->ArrViewEnter=array("lv199"=>"-1","lv612"=>"-1","lv609"=>"-1","lv610"=>"-1","lv649"=>"-1","lv001"=>"-1","lv002"=>"99","lv003"=>"-1","lv004"=>"-1","lv005"=>"5","lv006"=>"2","lv007"=>"2","lv009"=>"33","lv010"=>"-1","lv011"=>"-1","lv012"=>"-1","lv013"=>"-1","lv014"=>"-1",'lv018'=>"999","lv115"=>"-1","lv026"=>"33","lv027"=>"2","lv028"=>"33","lv029"=>"33","lv608"=>"-1","lv609"=>"-1","lv610"=>"-1","lv649"=>"-1","lv612"=>"-1","lv025"=>"999");
		}
		$vID=$vrow['lv001'];
		$lvTr="<tr class=\"lvlinehtable@01\"><td width=1% onclick=\"Select_Check('$lvChk@03',$lvFrom, '$lvChk', '$lvChkAll')\">@03</td>	<td width=1%><input name=\"$lvChk\" type=\"checkbox\" id=\"$lvChk@03\" onclick=\"CheckOne($lvFrom, '$lvChk', '$lvChkAll', this)\" value=\"@02\" tabindex=\"2\"  onKeyUp=\"return CheckKeyCheck(event,2,'$lvChk',$lvFrom, '$lvChk', '$lvChkAll',@03)\"/></td>@#01</tr>";
		$lvHref="<a href=\"javascript:FunctRunning1('@01')\" class=@#04 style=\"text-decoration:none\">@02</a>";
		$lvTdH="<td width=\"@01\" class=\"lvhtable\"><div id=\"div_@03_$vID\">@02</div></td>";
		$lvTd1="<td align=@#05><div id=\"div_@03_$vID\">@02</div></td>";
		$lvTd="<td align=@#05>@02</td>";
		
		$lvTdF="<td align=\"right\"><strong>@01</strong></td>";
		$strF="<tr><td colspan=\"2\">&nbsp;</td>";
		for($i=0;$i<count($lstArr);$i++)
		{
			switch($lstArr[$i])
			{
				case 'lv199':
					//<img style="height:30px" src="../clsall/barcode/barcode.php?barnumber='.$vrow['lv001'].'"/>
					$vChucNang='';
					if(($vrow['lv008']<1)) 
					{
						if($this->isApr==1) $vChucNang=$vChucNang.'
						<span><a href="javascript:AprUng(\''.$vrow['lv001'].'\')"><img title="Ứng đợi quản lý duyệt" style="cursor:pointer;width:25px;;padding:5px;"  alt="NoImg" src="../images/icon/Apr.png" align="middle" border="0" name="new" class="lviconimg"></a></span>
						';
						/*if($this->isUnApr==1) $vChucNang=$vChucNang.'
						<span><a href="javascript:UnAprUng(\''.$vrow['lv001'].'\')"><img title="Ứng đợi quyết toán" style="cursor:pointer;width:25px;;padding:5px;"  alt="NoImg" src="../images/icon/UnApr.png" align="middle" border="0" name="new" class="lviconimg"></a></span>
						';*/
					}
						
					$vStr1='
							<div style="cursor:pointer;color:blue;" onclick="showDetailHD(\'chitietid_'.$vrow['lv001'].'\',\''.$vrow['lv001'].'\')">'.'<img style="cursor:pointer;width:25px;;padding:5px;"  alt="NoImg" src="../images/icon/job.png" title="Xem Chi tiết báo giá"/>'.'</div>
							<div style="display:none;position:absolute;z-index:999999999999;background:#efefef;width:800px;" id="chitietid_'.$vrow['lv001'].'" class="noidung_member">					
								<div class="hd_cafe" style="width:100%">
									<ul class="qlycafe" style="width:100%">
										<li style="padding:10px;"><img onclick="document.getElementById(\'chitietid_'.$vrow['lv001'].'\').style.display=\'none\';" width="20" src="../images/icon/close.png"/></li>
										<li style="padding:10px;"><div style="width:100%;padding-top:2px;">
										<div style="float:left;"><strong>'.$vrow['lv008'].' (Hệ số giá:'.$vrow['lv062'].' x Giá chuẩn:'.$this->FormatView($vrow['lv063'],20).'đ = '.$this->FormatView($vrow['lv053'],20).' đ)</strong></div>'.'<div style="float:right;padding-left:50px;">'.$vChucNang.'</div>'.'
										</div>
										</li>
									</ul>
								</div>
								<div id="chitietnoidung_'.$vrow['lv001'].'" style="min-width:360px;overflow:hidden;overflow: scroll;"></div>
								<div width="100%;height:40px;">
									<center>
										<div style="width:160px;border-radius:5px;cursor:pointer;height:30px;padding-top:10px;" onclick="document.getElementById(\'chitietid_'.$vrow['lv001'].'\').style.display=\'none\';">ĐÓNG LẠI</div>
									</center>
								</div>
							</div>	
							';
							$vChucNang=$vStr1.$vChucNang;
					$vTemp=str_replace("@02",$vChucNang,$this->Align($lvTd,(int)$this->ArrView[$lstArr[$i]]));	
					$vTempEnter=$vTemp;					
					break;
				/*case 'lv061':
					
					if($this->isEdit>0 || $this->isAdd>0)
					{
						$lvImg="<center><a target='_blank' href='".$this->Dir."cr_lv0176/readfile.php?UserID=".$vrow['lv001']."&type=8&size=0'><img name=\"imgView\" border=\"0\" style=\"border-color:#CCCCCC\" title=\"\" alt=\"Image\" width1=\"96px\" height=\"48px\" src=\"".$this->Dir."cr_lv0176/readfile.php?UserID=".$vrow['lv001']."&type=8&size=1\" /></a></center>";
						$vTempEnter='<td>
						<table valign="top" style="width:100%;border: 1px solid #89B4D6">
							<tr>
							<td align="center">
								<div style="text-align:center" id="attachfile_8_'.$vrow['lv001'].'">'.$lvImg.'</div>
							</td>
							</tr>
							<tr>
							<td align=" title="Phiếu khai thác thông tin khách hàng">
							<div id="framupload_8_'.$vrow['lv001'].'" style="width:116px">
							<iframe  height=28 width="100%" marginheight=0 marginwidth=0 frameborder=0 src="'.$this->Dir.'cr_lv0176?childdetailfunc=upload&ViTriUp=8&UserID='.$vrow['lv001'].'&lang='.$plang.'&Dir=../"></iframe>
							</div>
							</td>
							
						</tr>
						</table>	</td>
								';
							$vTemp=str_replace("@02",$vrow[$lstArr[$i]],str_replace("@01",$vrow['lv001'],$vTempEnter));
					}
					else
					{
						$lvImg="<td  ><a target='_blank' href='cr_lv0176/readfile.php?UserID=".$vrow['lv001']."&type=9&size=0'><img name=\"imgView\" border=\"0\" style=\"border-color:#CCCCCC\" title=\"\" alt=\"Image\" width1=\"96px\" height=\"48px\" src=\"cr_lv0176/readfile.php?UserID=".$vrow['lv001']."&type=8&size=1\" /></a></td>";
						$vTemp=str_replace("@02",$vrow[$lstArr[$i]],str_replace("@01",$vrow['lv001'],$lvImg));
					}
					$vTempEnter=$vTemp;
				break;*/
				/*case 'lv007':
					
					if($this->isEdit>0 || $this->isAdd>0)
					{						
						$lvImg="<center><a target='_blank' href='cr_lv0176/readfile.php?UserID=".$vrow['lv001']."&type=9&size=0'><img name=\"imgView\" border=\"0\" style=\"border-color:#CCCCCC\" title=\"\" alt=\"Image\" width1=\"96px\" height=\"48px\" src=\"cr_lv0176/readfile.php?UserID=".$vrow['lv001']."&type=9&size=1\" /></a></center>";
						$vTempEnter='<td>
						<table valign="top" style="width:100%;border: 1px solid #89B4D6">
							<tr>
							<td align="center">
								<div style="text-align:center" id="attachfile_9_'.$vrow['lv001'].'">'.$lvImg.'</div>
							</td>
							</tr>
							<tr>
							<td align=" title="Phiếu khai thác thông tin khách hàng">
							<div id="framupload_9_'.$vrow['lv001'].'" style="width:116px">
							<iframe  height=28 width="100%" marginheight=0 marginwidth=0 frameborder=0 src="wh_lv0022?childdetailfunc=upload&ViTriUp=9&UserID='.$vrow['lv001'].'&lang='.$plang.'"></iframe>
							</div>
							</td>
							
						</tr>
						</table>	</td>
								';
							$vTemp=str_replace("@02",$vrow[$lstArr[$i]],str_replace("@01",$vrow['lv001'],$vTempEnter));
					}
					else
					{
						$lvImg="<td  ><a target='_blank' href='cr_lv0176/readfile.php?UserID=".$vrow['lv001']."&type=9&size=0'><img name=\"imgView\" border=\"0\" style=\"border-color:#CCCCCC\" title=\"\" alt=\"Image\" width1=\"96px\" height=\"48px\" src=\"cr_lv0176/readfile.php?UserID=".$vrow['lv001']."&type=9&size=1\" /></a></td>";
						$vTemp=str_replace("@02",$vrow[$lstArr[$i]],str_replace("@01",$vrow['lv001'],$lvImg));
					}
					$vTempEnter=$vTemp;
				break;*/
				default:
					$vTemp=str_replace("@01","",$lvTdH);
					
					$vTemp=str_replace("@02",$this->ArrPush[(int)$this->ArrGet[$lstArr[$i]]],$vTemp);
					$strH=$strH.$vTemp;
					$vTempF=str_replace("@01","<!--".$lstArr[$i]."-->",$lvTdF);
					$strF=$strF.$vTempF;
					$vField=$lstArr[$i];
					$lvTd=str_replace("@03",$vField,$lvTd1);
					$vStringNumber="";
					if($this->ArrViewEnter[$vField]==null) $this->ArrViewEnter[$vField]=0;
					$vSTTCot=(int)substr($vField,2,3);
					$vStringBlur='';//' onblur="if(this.value.substr(this.value.length-1,this.value.length)==\',\') {this.value=this.value.substr(0,this.value.length-1);};" ';
					switch($this->ArrView[$vField])
					{
						case '10':
						case '20':
						case '1':
							$vStringNumber=' onfocus="LayLaiGiaTri(this)" onblur="LV_SetGiaTriTien(\''.$vID.'\');SetGiaTri(this);UpdateText(this,\''.$vID.'\','.$vSTTCot.');if(this.value.substr(this.value.length-1,this.value.length)==\',\') {this.value=this.value.substr(0,this.value.length-1);};" ';
							break;
						default:
							if($this->ArrViewEnter[$vField]==5)
								$vStringNumber=' onblur="if(this.value.substr(this.value.length-1,this.value.length)==\',\') {this.value=this.value.substr(0,this.value.length-1);};UpdateTextArea(this,\''.$vID.'\','.$vSTTCot.');" ';
							else
								$vStringNumber=' onblur="if(this.value.substr(this.value.length-1,this.value.length)==\',\') {this.value=this.value.substr(0,this.value.length-1);};UpdateText(this,\''.$vID.'\','.$vSTTCot.');" ';
							break;
					}
					switch($this->ArrViewEnter[$vField])
					{			
						case 99:
							if($this->isPopupPlus==0) $this->isPopupPlus=1;
							$vstr='<ul style="width:100%" id="pop-nav'.(($this->isPopupPlus==1)?'':$this->isPopupPlus).'" lang="pop-nav'.$this->isPopupPlus.'" onMouseOver="ChangeName(this,'.$this->isPopupPlus.')" onKeyUp="ChangeName(this,'.$this->isPopupPlus.')"> <li class="menupopT">
										<input '.$vStringNumber.' autocomplete="off" class="txtenterquick" type="text" autocomplete="off" style="width:100%;min-width:80px" name="qxt'.$vField.'_'.$vID.'" id="qxt'.$vField.'_'.$vID.'" onKeyUp="LoadPopupParentTabIndex(event,this,\'qxt'.$vField.'_'.$vID.'\',\''.$this->Tables[$vField].'\',\'concat(lv002,@! @!,lv001)\')"   tabindex="2" value="'.$vrow[$vField].'" '.$vStringBlur.'>
										<div id="lv_popup'.(($this->isPopupPlus==1)?'':$this->isPopupPlus).'" lang="lv_popup'.$this->isPopupPlus.'"> </div>						  
										</li>
									</ul>';
							$this->isPopupPlus++;
							$vTempEnter=str_replace("@02",$vstr,$this->Align($lvTd,(int)$this->ArrView[$lstArr[$i]]));
							break;
						case 999:
							if($this->isPopupPlus==0) $this->isPopupPlus=1;
							$vstr='<ul style="width:100%" id="pop-nav'.(($this->isPopupPlus==1)?'':$this->isPopupPlus).'" lang="pop-nav'.$this->isPopupPlus.'" onMouseOver="ChangeName(this,'.$this->isPopupPlus.')" onKeyUp="ChangeName(this,'.$this->isPopupPlus.')"> <li class="menupopT">
										<input '.$vStringNumber.' autocomplete="off" class="txtenterquick" type="text" autocomplete="off" style="width:100%;min-width:80px" name="qxt'.$vField.'_'.$vID.'" id="qxt'.$vField.'_'.$vID.'" onKeyUp="LoadSelfNext(this,\'qxt'.$vField.'_'.$vID.'\',\''.$this->Tables[$vField].'\',\''.$this->TableLinkReturn[$vField].'\',\''.$this->TableLink[$vField].'\')"   tabindex="2" value="'.$vrow[$vField].'"  '.$vStringBlur.' >
										<div id="lv_popup'.(($this->isPopupPlus==1)?'':$this->isPopupPlus).'" lang="lv_popup'.$this->isPopupPlus.'"> </div>						  
										</li>
									</ul>';
							$this->isPopupPlus++;
							$vTempEnter=str_replace("@02",$vstr,$this->Align($lvTd,(int)$this->ArrView[$lstArr[$i]]));
							break;
						case 88:
							$vstr='<select '.$vStringNumber.' class="selenterquick" name="qxt'.$vField.'_'.$vID.'" id="qxt'.$vField.'_'.$vID.'" tabindex="2" style="width:100%;min-width:80px;"  '.$vStringBlur.'>'.$this->LV_LinkField($vField,$vrow[$vField]).'</select>';
							$vTempEnter=str_replace("@02",$vstr,$this->Align($lvTd,(int)$this->ArrView[$lstArr[$i]]));
							break;
						case 89:
								$vstr='<select '.$vStringNumber.' class="selenterquick" name="qxt'.$vField.'_'.$vID.'" id="qxt'.$vField.'_'.$vID.'" tabindex="2" style="width:100%;min-width:80px;"  '.$vStringBlur.'>
									<option value="">...</option>
								'.$this->LV_LinkField($vField,$vrow[$vField]).'</select>';
								$vTempEnter=str_replace("@02",$vstr,$this->Align($lvTd,(int)$this->ArrView[$lstArr[$i]]));
								break;
						case 4:
							$vstr='<table><tr><td><input '.$vStringNumber.' autocomplete="off" class="txtenterquick"  autocomplete="off" name="qxt'.$vField.'_'.$vID.'_1" type="text" id="qxt'.$vField.'_'.$vID.'_1" value="'.substr($vrow[$vField],0,10).'" tabindex="2" maxlength="32" style="width:50%;min-width:80px;text-align:center;"  ondblclick="if(self.gfPop)gfPop.fPopCalendar(this);return false;"></td><td><input class="txtenterquick"  autocomplete="off" name="qxt'.$vField.'_'.$vID.'_2" type="text" id="qxt'.$vField.'_'.$vID.'_2" value="'.substr($vrow[$vField],11,8).'" tabindex="2" maxlength="32" style="width:50%;min-width:60px;text-align:center;"  ></td></tr></table>';
							$vTempEnter=str_replace("@02",$vstr,$this->Align($lvTd,(int)$this->ArrView[$lstArr[$i]]));
							break;
						case 22:
							$vValues=$this->FormatView($vrow[$vField],2).' '.substr($vrow[$vField],11,8);
							$vstr='<input '.$vStringNumber.' autocomplete="off" class="txtenterquick"  autocomplete="off" name="qxt'.$vField.'_'.$vID.'" type="text" id="qxt'.$vField.'_'.$vID.'" value="'.$vValues.'" tabindex="2" maxlength="32" style="width:100%;min-width:120px"  ondblclick="if(self.gfPop)gfPop.fPopCalendar(this);return false;">';
							$vTempEnter=str_replace("@02",$vstr,$this->Align($lvTd,(int)$this->ArrView[$lstArr[$i]]));
							break;
						case 2:
							$vValues=$this->FormatView($vrow[$vField],2);
							$vstr='<input '.$vStringNumber.' autocomplete="off" class="txtenterquick"  autocomplete="off" name="qxt'.$vField.'_'.$vID.'" type="text" id="qxt'.$vField.'_'.$vID.'" value="'.$vValues.'" tabindex="2" maxlength="32" style="width:100%;min-width:80px"  ondblclick="if(self.gfPop)gfPop.fPopCalendar(this);return false;">';
							$vTempEnter=str_replace("@02",$vstr,$this->Align($lvTd,(int)$this->ArrView[$lstArr[$i]]));
							break;
						case 33:
							$vstr='<input '.$vStringNumber.' autocomplete="off" class="txtenterquick" name="qxt'.$vField.'_'.$vID.'" type="checkbox" id="qxt'.$vField.'_'.$vID.'" value="1" '.(($vrow[$vField]==1)?'checked="true"':'').' tabindex="2" style="width:100%;min-width:80px;text-align:center;"   '.$vStringBlur.'>';
							$vTempEnter=str_replace("@02",$vstr,$this->Align($lvTd,(int)$this->ArrView[$lstArr[$i]]));
							break;
						case 10:
							$vstr='<input '.$vStringNumber.' onblur="SetGiaTri(this);" onfocus="LayLaiGiaTri(this);" autocomplete="off" class="txtenterquick" name="qxt'.$vField.'_'.$vID.'" type="text" id="qxt'.$vField.'_'.$vID.'"  title="'.$vrow[$vField].'" value="'.$vrow[$vField].'" tabindex="2" style="width:100%;min-width:80px;text-align:center;" >';
							$vTempEnter=str_replace("@02",$vstr,$this->Align($lvTd,(int)$this->ArrView[$lstArr[$i]]));
							break;
						case 0:
							$vstr='<input '.$vStringNumber.' autocomplete="off" class="txtenterquick" name="qxt'.$vField.'_'.$vID.'" type="text" id="qxt'.$vField.'_'.$vID.'" value="'.$vrow[$vField].'" tabindex="2" style="width:100%;min-width:80px;text-align:center;" >';
							$vTempEnter=str_replace("@02",$vstr,$this->Align($lvTd,(int)$this->ArrView[$lstArr[$i]]));
							break;	
						case 5:
							$vstr='<textarea '.$vStringNumber.' autocomplete="off" class="txtenterquick" name="qxt'.$vField.'_'.$vID.'" type="text" id="qxt'.$vField.'_'.$vID.'" tabindex="2" style="width:99%;min-width:180px;text-align:left;height:80px;" >'.$vrow[$vField].'</textarea>';
							$vTempEnter=str_replace("@02",$vstr,$this->Align($lvTd,(int)$this->ArrView[$lstArr[$i]]));
							break;
						default:
							$vTempEnter=str_replace("@02",$this->getvaluelink($lstArr[$i],$this->FormatView($vrow[$lstArr[$i]],(int)$this->ArrView[$lstArr[$i]])),$this->Align($lvTd,(int)$this->ArrView[$lstArr[$i]]));
							//$vTempEnter="<td>&nbsp;</td>";
							break;
					}
					break;
				}
			$strTrEnter=$strTrEnter.$vTempEnter;
			$strTrEnterEmpty=$strTrEnterEmpty."<td>&nbsp;</td>";
		}
		return $strTrEnter;
		//$strTrEnter="<tr class='entermobil'>".$strTrEnter."</tr>";
	}
	//////////////////////Buil list////////////////////
	function LV_BuilList($lvList,$lvFrom,$lvChkAll,$lvChk,$curRow, $maxRows,$paging,$lvOrderList,$lvSortNum)
	{
		if($curRow<0) $curRow=0;	if($lvList=="") $lvList=$this->DefaultFieldList;
		if($this->isView==0) return false;
		$lstArr=explode(",",$lvList);
		$lstOrdArr=explode(",",$lvOrderList);
		$lstArr=$this->getsort($lstArr,$lstOrdArr);
		$strSort="";
		switch($lvSortNum)
		{
			case 0:
				break;
			case 1:
				$strSort=" order by ".$this->LV_SortBuild($this->GB_Sort,"asc");
				break;
			case 2:
				$strSort=" order by ".$this->LV_SortBuild($this->GB_Sort,"desc");
				break;
		}
		$lvTable="
		<div id=\"func_id\" style='position:relative;background:#f2f2f2'><div style=\"float:left\">".$this->TabFunction($lvFrom,$lvList,$maxRows)."</div><div style=\"float:right\">".$this->ListFieldSave($lvFrom,$lvList,$maxRows,$lvOrderList,$lvSortNum)."</div><div style='float:right'>&nbsp;&nbsp;&nbsp;</div><div style='float:right'>".$this->ListFieldExport($lvFrom,$lvList,$maxRows)."</div></div><div style='height:35px'></div><table  align=\"center\" class=\"lvtable\"><!--<tr ><td colspan=\"".(2+count($lstArr))."\" class=\"lvTTable\">".$this->ArrPush[0]."</td></tr>-->
		@#01
		<tr ><td colspan=\"".(count($lstArr)+2)."\">$paging</td></tr>
		<tr class=\"cssbold_tab\"><td colspan=\"".(count($lstArr)+2)."\">".$this->TabFunction($lvFrom,$lvList,$maxRows)."</td></tr>
		</table>
		";
		$lvTrH="<tr class=\"lvhtable\">
			<td width=1% class=\"lvhtable\">".$this->ArrPush[1]."</td>
			<td width=1%><input name=\"$lvChkAll\" type=\"checkbox\" id=\"$lvChkAll\" onclick=\"DoChkAll($lvFrom, '$lvChk', this)\" value=\"$curRow\" tabindex=\"2\"/></td>
			@#01
		</tr>
		";
		$lvTr="<tr class=\"lvlinehtable@01\"><td width=1% onclick=\"Select_Check('$lvChk@03',$lvFrom, '$lvChk', '$lvChkAll')\">@03</td>	<td width=1%><input name=\"$lvChk\" type=\"checkbox\" id=\"$lvChk@03\" onclick=\"CheckOne($lvFrom, '$lvChk', '$lvChkAll', this)\" value=\"@02\" tabindex=\"2\"  onKeyUp=\"return CheckKeyCheck(event,2,'$lvChk',$lvFrom, '$lvChk', '$lvChkAll',@03)\"/></td>@#01</tr>";
		$lvTdH="<td width=\"@01\" class=\"lvhtable\">@02</td>";
		$lvTd="<td align=@#05>@02</td>";
		$sqlS = "SELECT A.*,AA.lv008 lv608,AA.lv012 lv612,AA.lv009 lv609,AA.lv010 lv610,AA.lv049 lv649 FROM cr_lv0052 A inner join cr_lv0276 AA on AA.lv001=A.lv002 WHERE 1=1  ".$this->GetCondition()." $strSort LIMIT $curRow, $maxRows";
		$vorder=$curRow;
		$bResult = db_query($sqlS);
		$this->Count=db_num_rows($bResult);
		$strTrH="";
		$strTr="";
		for($i=0;$i<count($lstArr);$i++)
			{
				$vTemp=str_replace("@01","",$lvTdH);
				$vTemp=str_replace("@02",$this->ArrPush[(int)$this->ArrGet[$lstArr[$i]]],$vTemp);
				$strH=$strH.$vTemp;
				$vField=$lstArr[$i];
				$vStringNumber="";
				if($this->ArrViewEnter[$vField]==null) $this->ArrViewEnter[$vField]=0;
				$vStringNumber="";
				switch($this->ArrView[$vField])
				{
					case '10':
					case '20':
					case '1':
						$vStringNumber=' onfocus="LayLaiGiaTri(this)" onblur="SetGiaTri(this);" ';
						break;
				}
				if($vField=='lv005')
				{
					$vStringNumber=' onKeyUp="TypeSource(this)" onblur="LoadSource(this);" ';
				}
				if($this->Dir=='')
				{
					switch($this->ArrViewEnter[$vField])
					{		
						case 49:
							if($this->isPopupPlus==0) $this->isPopupPlus=1;
							$vstr='<ul style="width:100%" id="pop-nav'.(($this->isPopupPlus==1)?'':$this->isPopupPlus).'" lang="pop-nav'.$this->isPopupPlus.'" onMouseOver="ChangeName(this,'.$this->isPopupPlus.')" onKeyUp="ChangeName(this,'.$this->isPopupPlus.')"> <li class="menupopT">
										<input '.$vStringNumber.' autocomplete="off" class="txtenterquick" type="text" autocomplete="off" style="width:100%;min-width:30px" name="qxt'.$vField.'" id="qxt'.$vField.'"  tabindex="2" value="'.$this->Values[$vField].'">
										<div id="lv_popup'.(($this->isPopupPlus==1)?'':$this->isPopupPlus).'" lang="lv_popup'.$this->isPopupPlus.'"> </div>						  
										</li>
									</ul>';
							$this->isPopupPlus++;
							$vTempEnter=str_replace("@02",$vstr,$this->Align($lvTd,(int)$this->ArrView[$lstArr[$i]]));
							break;	
						case 99:
							if($this->isPopupPlus==0) $this->isPopupPlus=1;
							$vstr='<ul style="width:100%" id="pop-nav'.(($this->isPopupPlus==1)?'':$this->isPopupPlus).'" lang="pop-nav'.$this->isPopupPlus.'" onMouseOver="ChangeName(this,'.$this->isPopupPlus.')" onKeyUp="ChangeName(this,'.$this->isPopupPlus.')"> <li class="menupopT">
										<input autocomplete="off" class="txtenterquick" type="text" autocomplete="off" style="width:100%;min-width:30px" name="qxt'.$vField.'" id="qxt'.$vField.'" onKeyUp="LoadPopupTabIndex(event,this,\'qxt'.$vField.'\',\''.$this->Tables[$vField].'\',\'concat(lv002,@! @!,lv001)\')"  onKeyPress="return CheckKey(event,7)" tabindex="2"  value="'.$this->Values[$vField].'">
										<div id="lv_popup'.(($this->isPopupPlus==1)?'':$this->isPopupPlus).'" lang="lv_popup'.$this->isPopupPlus.'"> </div>						  
										</li>
									</ul>';
							$this->isPopupPlus++;
							$vTempEnter=str_replace("@02",$vstr,$this->Align($lvTd,(int)$this->ArrView[$lstArr[$i]]));
							break;
						case 999:
							if($this->isPopupPlus==0) $this->isPopupPlus=1;
							$vstr='<ul style="width:100%" id="pop-nav'.(($this->isPopupPlus==1)?'':$this->isPopupPlus).'" lang="pop-nav'.$this->isPopupPlus.'" onMouseOver="ChangeName(this,'.$this->isPopupPlus.')" onKeyUp="ChangeName(this,'.$this->isPopupPlus.')"> <li class="menupopT">
										<input autocomplete="off" class="txtenterquick" type="text" autocomplete="off" style="width:100%;min-width:30px" name="qxt'.$vField.'" id="qxt'.$vField.'" onKeyUp="LoadSelfNextParent(this,\'qxt'.$vField.'\',\''.$this->Tables[$vField].'\',\''.$this->TableLinkReturn[$vField].'\',\''.$this->TableLink[$vField].'\')"  onKeyPress="return CheckKey(event,7)" tabindex="2" value="'.$this->Values[$vField].'" onblur="if(this.value.substr(this.value.length-1,this.value.length)==\',\') {this.value=this.value.substr(0,this.value.length-1);};">
										<div id="lv_popup'.(($this->isPopupPlus==1)?'':$this->isPopupPlus).'" lang="lv_popup'.$this->isPopupPlus.'"> </div>						  
										</li>
									</ul>';
							$this->isPopupPlus++;
							$vTempEnter=str_replace("@02",$vstr,$this->Align($lvTd,(int)$this->ArrView[$lstArr[$i]]));
							break;
						case 88:
							$vstr='<select class="selenterquick" name="qxt'.$vField.'" id="qxt'.$vField.'" tabindex="2" style="width:100%;min-width:30px" onKeyPress="return CheckKey(event,7)">'.$this->LV_LinkField($vField,$this->Values[$vField]).'</select>';
							$vTempEnter=str_replace("@02",$vstr,$this->Align($lvTd,(int)$this->ArrView[$lstArr[$i]]));
							break;
						case 89:
								$vstr='<select class="selenterquick" name="qxt'.$vField.'" id="qxt'.$vField.'" tabindex="2" style="width:100%;min-width:30px" onKeyPress="return CheckKey(event,7)">
									<option value="">...</option>
								'.$this->LV_LinkField($vField,$this->Values[$vField]).'</select>';
								$vTempEnter=str_replace("@02",$vstr,$this->Align($lvTd,(int)$this->ArrView[$lstArr[$i]]));
								break;
						case 4:
							$vstr='<table><tr><td><input autocomplete="off" class="txtenterquick"  autocomplete="off" name="qxt'.$vField.'_1" type="text" id="qxt'.$vField.'_1" value="'.$this->Values[$vField].'" tabindex="2" maxlength="32" style="width:100%;min-width:80px" onKeyPress="return CheckKey(event,7)" ondblclick="if(self.gfPop)gfPop.fPopCalendar(this);return false;"></td><td><input class="txtenterquick"  autocomplete="off" name="qxt'.$vField.'_2" type="text" id="qxt'.$vField.'_2" value="'.$this->Values[$vField].'" tabindex="2" maxlength="32" style="width:50%;min-width:60px" onKeyPress="return CheckKey(event,7)" ></td></tr></table>';
							$vTempEnter=str_replace("@02",$vstr,$this->Align($lvTd,(int)$this->ArrView[$lstArr[$i]]));
							break;
						case 22:
						case 2:
							$vstr='<input autocomplete="off" class="txtenterquick"  autocomplete="off" name="qxt'.$vField.'" type="text" id="qxt'.$vField.'" value="'.$this->Values[$vField].'" tabindex="2" maxlength="32" style="width:100%;min-width:60px" onKeyPress="return CheckKey(event,7)" ondblclick="if(self.gfPop)gfPop.fPopCalendar(this);return false;">';
							$vTempEnter=str_replace("@02",$vstr,$this->Align($lvTd,(int)$this->ArrView[$lstArr[$i]]));
							break;
						case 33:
							$vstr='<input autocomplete="off" class="txtenterquick" name="qxt'.$vField.'" type="checkbox" id="qxt'.$vField.'" value="1" '.(($this->Values[$vField]==1)?'checked="true"':'').' tabindex="2" style="width:100%;min-width:30px;text-align:center;" onKeyPress="return CheckKey(event,7)">';
							$vTempEnter=str_replace("@02",$vstr,$this->Align($lvTd,(int)$this->ArrView[$lstArr[$i]]));
							break;
						case 5:
							$vstr='<textarea '.$vStringNumber.' autocomplete="off" class="txtenterquick" name="qxt'.$vField.'" type="text" id="qxt'.$vField.'" tabindex="2" style="width:100%;min-width:130px;text-align:center;" rows="1" onKeyPress="return CheckKey(event,7)">'.$this->Values[$vField].'</textarea>';
							$vTempEnter=str_replace("@02",$vstr,$this->Align($lvTd,(int)$this->ArrView[$lstArr[$i]]));
							break;
						case 0:
							$vstr='<input '.$vStringNumber.' autocomplete="off" class="txtenterquick" name="qxt'.$vField.'" type="text" id="qxt'.$vField.'" value="'.$this->Values[$vField].'" tabindex="2" style="width:100%;min-width:30px;text-align:center;" onKeyPress="return CheckKey(event,7)">';
							$vTempEnter=str_replace("@02",$vstr,$this->Align($lvTd,(int)$this->ArrView[$lstArr[$i]]));
							break;
						default:
							$vTempEnter="<td>&nbsp;</td>";
							break;
					}
				}
				else
				{
					switch($this->ArrViewEnter[$vField])
					{		
						case 49:
							if($this->isPopupPlus==0) $this->isPopupPlus=1;
							$vstr='<ul style="width:100%" id="pop-nav'.(($this->isPopupPlus==1)?'':$this->isPopupPlus).'" lang="pop-nav'.$this->isPopupPlus.'" onMouseOver="ChangeName(this,'.$this->isPopupPlus.')" onKeyUp="ChangeName(this,'.$this->isPopupPlus.')"> <li class="menupopT">
										<input '.$vStringNumber.' autocomplete="off" class="txtenterquick" type="text" autocomplete="off" style="width:100%;min-width:30px" name="qxt'.$vField.'" id="qxt'.$vField.'"  tabindex="2" value="'.$this->Values[$vField].'">
										<div id="lv_popup'.(($this->isPopupPlus==1)?'':$this->isPopupPlus).'" lang="lv_popup'.$this->isPopupPlus.'"> </div>						  
										</li>
									</ul>';
							$this->isPopupPlus++;
							$vTempEnter=str_replace("@02",$vstr,$this->Align($lvTd,(int)$this->ArrView[$lstArr[$i]]));
							break;	
						case 99:
							if($this->isPopupPlus==0) $this->isPopupPlus=1;
							$vstr='<ul style="width:100%" id="pop-nav'.(($this->isPopupPlus==1)?'':$this->isPopupPlus).'" lang="pop-nav'.$this->isPopupPlus.'" onMouseOver="ChangeName(this,'.$this->isPopupPlus.')" onKeyUp="ChangeName(this,'.$this->isPopupPlus.')"> <li class="menupopT">
										<input autocomplete="off" class="txtenterquick" type="text" autocomplete="off" style="width:100%;min-width:30px" name="qxt'.$vField.'" id="qxt'.$vField.'" onKeyUp="LoadPopupTabIndex(event,this,\'qxt'.$vField.'\',\''.$this->Tables[$vField].'\',\'concat(lv002,@! @!,lv001)\')"  onKeyPress="return CheckKey(event,7)" tabindex="2"  value="'.$this->Values[$vField].'">
										<div id="lv_popup'.(($this->isPopupPlus==1)?'':$this->isPopupPlus).'" lang="lv_popup'.$this->isPopupPlus.'"> </div>						  
										</li>
									</ul>';
							$this->isPopupPlus++;
							$vTempEnter=str_replace("@02",$vstr,$this->Align($lvTd,(int)$this->ArrView[$lstArr[$i]]));
							break;
						case 999:
							if($this->isPopupPlus==0) $this->isPopupPlus=1;
							$vstr='<ul style="width:100%" id="pop-nav'.(($this->isPopupPlus==1)?'':$this->isPopupPlus).'" lang="pop-nav'.$this->isPopupPlus.'" onMouseOver="ChangeName(this,'.$this->isPopupPlus.')" onKeyUp="ChangeName(this,'.$this->isPopupPlus.')"> <li class="menupopT">
										<input autocomplete="off" class="txtenterquick" type="text" autocomplete="off" style="width:100%;min-width:30px" name="qxt'.$vField.'" id="qxt'.$vField.'" onKeyUp="LoadSelfNext(this,\'qxt'.$vField.'\',\''.$this->Tables[$vField].'\',\''.$this->TableLinkReturn[$vField].'\',\''.$this->TableLink[$vField].'\')"  onKeyPress="return CheckKey(event,7)" tabindex="2" value="'.$this->Values[$vField].'" onblur="if(this.value.substr(this.value.length-1,this.value.length)==\',\') {this.value=this.value.substr(0,this.value.length-1);};">
										<div id="lv_popup'.(($this->isPopupPlus==1)?'':$this->isPopupPlus).'" lang="lv_popup'.$this->isPopupPlus.'"> </div>						  
										</li>
									</ul>';
							$this->isPopupPlus++;
							$vTempEnter=str_replace("@02",$vstr,$this->Align($lvTd,(int)$this->ArrView[$lstArr[$i]]));
							break;
						case 88:
							$vstr='<select class="selenterquick" name="qxt'.$vField.'" id="qxt'.$vField.'" tabindex="2" style="width:100%;min-width:30px" onKeyPress="return CheckKey(event,7)">'.$this->LV_LinkField($vField,$this->Values[$vField]).'</select>';
							$vTempEnter=str_replace("@02",$vstr,$this->Align($lvTd,(int)$this->ArrView[$lstArr[$i]]));
							break;
						case 89:
								$vstr='<select class="selenterquick" name="qxt'.$vField.'" id="qxt'.$vField.'" tabindex="2" style="width:100%;min-width:30px" onKeyPress="return CheckKey(event,7)">
									<option value="">...</option>
								'.$this->LV_LinkField($vField,$this->Values[$vField]).'</select>';
								$vTempEnter=str_replace("@02",$vstr,$this->Align($lvTd,(int)$this->ArrView[$lstArr[$i]]));
								break;
						case 4:
							$vstr='<table><tr><td><input autocomplete="off" class="txtenterquick"  autocomplete="off" name="qxt'.$vField.'_1" type="text" id="qxt'.$vField.'_1" value="'.$this->Values[$vField].'" tabindex="2" maxlength="32" style="width:100%;min-width:80px" onKeyPress="return CheckKey(event,7)" ondblclick="if(self.gfPop)gfPop.fPopCalendar(this);return false;"></td><td><input class="txtenterquick"  autocomplete="off" name="qxt'.$vField.'_2" type="text" id="qxt'.$vField.'_2" value="'.$this->Values[$vField].'" tabindex="2" maxlength="32" style="width:50%;min-width:60px" onKeyPress="return CheckKey(event,7)" ></td></tr></table>';
							$vTempEnter=str_replace("@02",$vstr,$this->Align($lvTd,(int)$this->ArrView[$lstArr[$i]]));
							break;
						case 22:
						case 2:
							$vstr='<input autocomplete="off" class="txtenterquick"  autocomplete="off" name="qxt'.$vField.'" type="text" id="qxt'.$vField.'" value="'.$this->Values[$vField].'" tabindex="2" maxlength="32" style="width:100%;min-width:60px" onKeyPress="return CheckKey(event,7)" ondblclick="if(self.gfPop)gfPop.fPopCalendar(this);return false;">';
							$vTempEnter=str_replace("@02",$vstr,$this->Align($lvTd,(int)$this->ArrView[$lstArr[$i]]));
							break;
						case 33:
							$vstr='<input autocomplete="off" class="txtenterquick" name="qxt'.$vField.'" type="checkbox" id="qxt'.$vField.'" value="1" '.(($this->Values[$vField]==1)?'checked="true"':'').' tabindex="2" style="width:100%;min-width:30px;text-align:center;" onKeyPress="return CheckKey(event,7)">';
							$vTempEnter=str_replace("@02",$vstr,$this->Align($lvTd,(int)$this->ArrView[$lstArr[$i]]));
							break;
						case 5:
							$vstr='<textarea '.$vStringNumber.' autocomplete="off" class="txtenterquick" name="qxt'.$vField.'" type="text" id="qxt'.$vField.'" tabindex="2" style="width:100%;min-width:130px;text-align:center;" rows="1" onKeyPress="return CheckKey(event,7)">'.$this->Values[$vField].'</textarea>';
							$vTempEnter=str_replace("@02",$vstr,$this->Align($lvTd,(int)$this->ArrView[$lstArr[$i]]));
							break;
						case 0:
							$vstr='<input '.$vStringNumber.' autocomplete="off" class="txtenterquick" name="qxt'.$vField.'" type="text" id="qxt'.$vField.'" value="'.$this->Values[$vField].'" tabindex="2" style="width:100%;min-width:30px;text-align:center;" onKeyPress="return CheckKey(event,7)">';
							$vTempEnter=str_replace("@02",$vstr,$this->Align($lvTd,(int)$this->ArrView[$lstArr[$i]]));
							break;
						default:
							$vTempEnter="<td>&nbsp;</td>";
							break;
					}
				}
				$strTrEnter=$strTrEnter.$vTempEnter;
				$strTrEnterEmpty=$strTrEnterEmpty."<td>&nbsp;</td>";
			}
		if($this->isAdd==1) 
			$strTrEnter="<tr class='entermobil'><td colspan='2'>".'<img tabindex="2" border="0" title="Add" class="imgButton" onclick="Save()" onmouseout="this.src=\'../images/iconcontrol/btn_add.jpg\';" onmouseover="this.src=\'../images/iconcontrol/btn_add_02.jpg\';" src="../images/iconcontrol/btn_add.jpg" onkeypress="return CheckKey(event,11)">'."</td>".$strTrEnter."</tr>";
		else
			$strTrEnter="";//"<tr class='entermobil'><td colspan='2'>".'&nbsp;'."</td>".$strTrEnterEmpty."</tr>";
			
		while ($vrow = db_fetch_array ($bResult)){
			$strL="";
			$vorder++;
			if($this->isFullEdit==1)
			{
				$strL=$this->LV_ReadXuLyAuto($lstArr,$vrow);
			}
			else
			{
				if(trim($vrow['lv612'])=='' || $vrow['lv612']==null)
				{
					$vrow['lv612']=$vrow['lv512'];
					$vrow['lv609']=$vrow['lv509'];
					$vrow['lv610']=$vrow['lv510'];
					$vrow['lv649']=$vrow['lv549'];
					$vrow['lv652']=$vrow['lv552'];
				}
				for($i=0;$i<count($lstArr);$i++)
				{
					switch($lstArr[$i])
					{	
						case 'lv199':
							$vChucNang="<span onclick=\"ProcessTextHiden(this)\"><table border=\"0\" cellpadding=\"0\" cellspacing=\"0\">
							<tr>
							";
							$vChucNang=$vChucNang.'<td><a href="javascript:FunctRunning1(\''.$vrow['lv001'].'\')"><img style="cursor:pointer;width:25px;;padding:5px;"  alt="NoImg" src="../images/icon/work_experience.png" align="middle" border="0" name="new" class="lviconimg"></a></td>';
							
							$vChucNang=$vChucNang."</tr></table></span>";
							$vStr='	
							';
							$vChucNang=$vStr.$vChucNang;
							$vTemp=str_replace("@02",$vChucNang,$this->Align($lvTd,(int)$this->ArrView[$lstArr[$i]]));						
							break;	
						case 'lv026':
						case 'lv028':
						case 'lv029':
						case 'lv009':
							$vPosition=(int)str_replace("lv","",$lstArr[$i]);
							if($this->GetEdit()==1)
							{													
								$lvTdTextBox="<td align=center><input class='txtenterquick' type=\"checkbox\" value=\"1\" ".(($vrow[$lstArr[$i]]==1)?'checked="true"':'')." @03 onclick=\"UpdateTextCheck(this,'".$vrow['lv001']."',".$vPosition.")\" style=\"width:35px;text-align:center;\" tabindex=\"2\" maxlength=\"32\"   onKeyPress=\"return CheckKey(event,7)\"/></td>";
								$vTemp=str_replace("@02",$this->FormatView($vrow[$lstArr[$i]],0),$this->Align(str_replace("@01",$vrow['lv001'],$lvTdTextBox),(int)$this->ArrView[$lstArr[$i]]));	
							}
							else
								$vTemp=str_replace("@02",$this->getvaluelink($lstArr[$i],$this->FormatView($vrow[$lstArr[$i]],(int)$this->ArrView[$lstArr[$i]])),$this->Align($lvTd,(int)$this->ArrView[$lstArr[$i]]));
							break;
						/*case 'lv010':
							if($this->GetEdit()==1)
							{													
								$lvTdTextBox="<td align=center><input class='txtenterquick' type=\"checkbox\" value=\"1\" ".(($vrow['lv010']==1)?'checked="true"':'')." @03 onclick=\"UpdateTextCheck(this,'".$vrow['lv001']."',10)\" style=\"width:35px;text-align:center;\" tabindex=\"2\" maxlength=\"32\"   onKeyPress=\"return CheckKey(event,7)\"/></td>";
								$vTemp=str_replace("@02",$this->FormatView($vrow[$lstArr[$i]],0),$this->Align(str_replace("@01",$vrow['lv001'],$lvTdTextBox),(int)$this->ArrView[$lstArr[$i]]));	
							}
							else
								$vTemp=str_replace("@02",$this->getvaluelink($lstArr[$i],$this->FormatView($vrow[$lstArr[$i]],(int)$this->ArrView[$lstArr[$i]])),$this->Align($lvTd,(int)$this->ArrView[$lstArr[$i]]));
							break;*/
						default:
							$vTemp=str_replace("@02",$this->getvaluelink($lstArr[$i],$this->FormatView($vrow[$lstArr[$i]],(int)$this->ArrView[$lstArr[$i]])),$this->Align($lvTd,(int)$this->ArrView[$lstArr[$i]]));
							break;
					}
					$strL=$strL.$vTemp;
				}

			}
			$strTr=$strTr.str_replace("@#01",$strL,str_replace("@02",$vrow['lv001'],str_replace("@03",$vorder,str_replace("@01",$vorder%2,$lvTr))));
			
		
		}
		$strTrH=str_replace("@#01",$strH,$lvTrH);
		return str_replace("@#01",$strTrH.$strTrEnter.$strTr,$lvTable);
	}
	//////////////////////Buil list////////////////////
	function LV_BuilListReportOtherMP($lvList,$lvFrom,$lvChkAll,$lvChk,$curRow, $maxRows,$paging,$lvOrderList,$lvSortNum)
	{
		if($lvList=="") $lvList=$this->DefaultFieldList;
		if($this->isView==0) return false;
		$lstArr=explode(",",$lvList);
		$lstOrdArr=explode(",",$lvOrderList);
		$lstArr=$this->getsort($lstArr,$lstOrdArr);
		$strSort="";
		switch($lvSortNum)
		{
			case 0:
				break;
			case 1:
				$strSort=" order by ".$this->LV_SortBuild($this->GB_Sort,"asc");
				break;
			case 2:
				$strSort=" order by ".$this->LV_SortBuild($this->GB_Sort,"desc");
				break;
		}
		$lvTable="
		<table width=\"100%\"  align=\"center\" border=\"0\"  cellspacing=\"0\"  cellspadding=\"0\">
		@#01
		@#02
		</table>
		";
		$lvTrH="<tr>
			<td  bgcolor=\"ffc000\" style=\"border-top: 1px solid #000000;border-left: 1px solid #000000;font-size16: 16px; font-family:arial, times new roman;\" width=1% valign=\"center\"><strong>".$this->ArrPush[1]."</strong></td>	
			@#01
		</tr>
		";
		$lvTr="<tr >
			<td  style=\"border-top: 1px solid #000000;border-left: 1px solid #000000;font-size16: 16px; font-family:arial, times new roman;\" width=1% align=\"center\">@03</td>
			@#01
		</tr>
		";
		$lvTrF="<tr >
			<td  style=\"border-bottom: 1px solid #000000;border-top: 1px solid #000000;border-left: 1px solid #000000;font-size16: 16px; font-family:arial, times new roman;\" width=1% align=\"center\">@03</td>
			@#01
		</tr>
		";
		$lvTdH="<td bgcolor=\"ffc000\"  style=\"border-top: 1px solid #000000;border-left: 1px solid #000000;font-size16: 16px; font-family:arial, times new roman;\" width=\"@01\" align=\"center\" valign=\"center\"><strong>@02</trong></td>";
		$lvTdHIn="<td bgcolor=\"bfbfbf\"  style=\"border-top: 1px solid #000000;border-left: 1px solid #000000;font-size16: 16px; font-family:arial, times new roman;\" width=\"@01\" align=\"center\" valign=\"center\"><strong>@02</trong></td>";
		$lvTdHE="<td bgcolor=\"ffc000\"  style=\"border-top: 1px solid #000000;border-left: 1px solid #000000;border-right: 1px solid #000000;font-size16: 16px; font-family:arial, times new roman;\" width=\"@01\" align=\"center\" valign=\"center\"><strong>@02</trong></td>";

		$BlTdH="<td  style=\"background:#d0cece;border-top: 1px solid #000000;border-left: 1px solid #000000;font-size16: 16px; font-family:arial, times new roman;\" width=\"@01\" align=\"center\" valign=\"center\"><strong>@02</trong></td>";
		$BlTdHE="<td  style=\"background:#d0cece;border-top: 1px solid #000000;border-left: 1px solid #000000;border-right: 1px solid #000000;font-size16: 16px; font-family:arial, times new roman;\" width=\"@01\" align=\"center\" valign=\"center\"><strong>@02</trong></td>";
		
		$lvTdF="<td style=\"border:0px;font-size16: 16px; font-family:arial, times new roman;\" align=\"right\"><strong>@01</strong></td>";
		$lvTdF3="<td   style=\"border:0px;white-space: nowrap;font-size14: 14px; font-family:arial, times new roman;\" align=\"left\" colspan=\"3\"><strong>@01</strong></td>";
		$BlTdH98="<td  style=\"background:#fff;border-top: 1px solid #000000;border-left: 1px solid #000000;border-right: 1px solid #000000;font-size16: 16px; font-family:arial, times new roman;\" width=\"98\" align=\"center\" valign=\"center\"><strong>@02</trong></td>";
		$strF="<tr style=\"background:#fff;\"><td >&nbsp;</td>";
		if($this->GroupProduct==1)
		{
			$sqlS = "SELECT A.*,AA.lv008 lv608,AA.lv012 lv612,AA.lv009 lv609,AA.lv010 lv610,AA.lv049 lv649,AA.lv052 lv652 FROM cr_lv0052 A inner join cr_lv0276 AA on AA.lv001=A.lv002 WHERE A.lv115='$this->lv115'" ;
			
		}
		else
		{
			$sqlS = "SELECT A.*,AA.lv008 lv608,AA.lv012 lv612,AA.lv009 lv609,AA.lv010 lv610,AA.lv049 lv649,AA.lv052 lv652 FROM cr_lv0052 A inner join cr_lv0276 AA on AA.lv001=A.lv002 WHERE A.lv115='$this->lv115'" ;
		}
		$vorder=$curRow;
		$bResult = db_query($sqlS);
		$this->Count=db_num_rows($bResult);
		$strTrH="";
		$strTr="";
		$vSoCot=count($lstArr);
		for($i=0;$i<count($lstArr);$i++)
			{

				switch($lstArr[$i])
				{
					/*case 'lv075':
						$vTemp=str_replace("@01","",$BlTdH98);
						break;
					case 'lv005':
					case 'lv006':
						if((count($lstArr)-1)==($i))
							$vTemp=str_replace("@01","",$BlTdHE);
						else
							$vTemp=str_replace("@01","",$BlTdH);
						break;
						break;	*/
					case 'lv025':
					case 'lv026':
					case 'lv028':
					case 'lv029':
					case 'lv030':
						$vTemp=str_replace("@01","",$lvTdHIn);
						break;
					default:
					if((count($lstArr)-1)==($i))
						$vTemp=str_replace("@01","",$lvTdHE);
					else
						$vTemp=str_replace("@01","",$lvTdH);
					break;
				}
			
				
				$vTemp=str_replace("@02",$this->ArrPush[(int)$this->ArrGet[$lstArr[$i]]],$vTemp);
				$strH=$strH.$vTemp;
				switch($lstArr[$i])
				{
					/*case 'lv051':
						$vTempF=str_replace("@01","<!--".$lstArr[$i]."-->",$lvTdF3);
						$strF=$strF.$vTempF;
						break;
						break;
					case 'lv052':
						break;
					case 'lv053':
						break;	*/
					default:
						$vTempF=str_replace("@01","<!--".$lstArr[$i]."-->",$lvTdF);
						$strF=$strF.$vTempF;
						break;
				}
				
			}
			$strF1111="<tr style=\"background:#fff;\"><td colspan=\"".count($lstArr)."\">@01</td></tr>";
		$strF=$strF."</tr>";	
		$vCodeCheck='11111111111111111111';
		while ($vrow = db_fetch_array ($bResult)){
			/*
			if($this->SoTang>1)
			{
				if($vCodeCheck!=$vrow['lv064'].'@'.$vrow['lv065'])
				{
					if(trim($vrow['lv065'])!='')
					{
					$strFFOK="<tr style=\"background:#ffffff;\"><td style=\"border-top: 1px solid #000000;border-left: 1px solid #000000;\"></td><td style=\"border-top: 1px solid #000000;border-left: 1px solid #000000;border-right: 1px solid #000000;font-size16: 16px; font-family:arial, times new roman;\" colspan=\"".$vSoCot."\">".$this->ArrKhuVuc[$vrow['lv065']]."</td></tr>";
					$strTr=$strTr.$strFFOK;
					}
					if(trim($vrow['lv064'])!='')
					{
					$strFFOK="<tr style=\"background:#d0cece;\"><td style=\"border-top: 1px solid #000000;border-left: 1px solid #000000;\"></td><td style=\"border-top: 1px solid #000000;border-left: 1px solid #000000;border-right: 1px solid #000000;font-size16: 16px; font-family:arial, times new roman;\" colspan=\"".$vSoCot."\">".$this->ArrTang[$vrow['lv064']]."</td></tr>";
					$strTr=$strTr.$strFFOK;
					}
					$vCodeCheck=$vrow['lv064'].'@'.$vrow['lv065'];
				}	
			}	*/	
			$strL="";
			$vorder++;
			if($this->Count==$vorder)
				$lvTd="<td style=\"border-bottom: 1px solid #000000;border-top: 1px solid #000000;border-left: 1px solid #000000;font-size16: 16px; font-family:arial, times new roman;\" align=@#05 valign=\"center\">@02</td>";
			else
				$lvTd="<td style=\"border-top: 1px solid #000000;border-left: 1px solid #000000;font-size16: 16px; font-family:arial, times new roman;\" align=@#05 valign=\"center\">@02</td>";
			if(trim($vrow['lv612'])=='' || $vrow['lv612']==null)
			{
				$vrow['lv612']=$vrow['lv512'];
				$vrow['lv609']=$vrow['lv509'];
				$vrow['lv610']=$vrow['lv510'];
				$vrow['lv649']=$vrow['lv549'];
				$vrow['lv652']=$vrow['lv552'];
			}
			for($i=0;$i<count($lstArr);$i++)
			{
				switch($lstArr[$i])
				{
					case 'lv011':
					case 'lv005':
						$lvTdLeft="left";
						break;
					case 'lv054':
						$lvTdLeft="right";
						break;
					default:
						$lvTdLeft="center";
						break;
				}
				if($this->Count==$vorder)
				{
					if($i==(count($lstArr)-1))
						$lvTd="<td style=\"border-bottom: 1px solid #000000;border-top: 1px solid #000000;border-left: 1px solid #000000;border-right: 1px solid #000000;font-size16: 16px; font-family:arial, times new roman;\"  align=\"".$lvTdLeft."\" valign=\"center\">@02</td>";
					else
						$lvTd="<td style=\"border-bottom: 1px solid #000000;border-top: 1px solid #000000;border-left: 1px solid #000000;font-size16: 16px; font-family:arial, times new roman;\"  align=\"".$lvTdLeft."\" valign=\"center\">@02</td>";
				}
				else
				{
					if($i==(count($lstArr)-1))
						$lvTd="<td style=\"border-top: 1px solid #000000;border-left: 1px solid #000000;border-right: 1px solid #000000;font-size16: 16px; font-family:arial, times new roman;\"  align=\"".$lvTdLeft."\" valign=\"center\">@02</td>";
					else
						$lvTd="<td style=\"border-top: 1px solid #000000;border-left: 1px solid #000000;font-size16: 16px; font-family:arial, times new roman;\"  align=\"".$lvTdLeft."\" valign=\"center\">@02</td>";
				}
				switch($lstArr[$i])
				{
					case 'lv026':
					case 'lv028':
					case 'lv029':
					case 'lv009':													
						$lvTdTextBox="<input class='txtenterquick' type=\"checkbox\" value=\"1\" ".(($vrow[$lstArr[$i]]==1)?'checked="true"':'')." @03 onclick=\"return false;\" style=\"width:35px;text-align:center;\" tabindex=\"2\" maxlength=\"32\"   onKeyPress=\"return CheckKey(event,7)\"/>";
						$vTemp=str_replace("@02",$lvTdTextBox,$this->Align($lvTd,(int)$this->ArrView[$lstArr[$i]]));
						//$vTemp=str_replace("@02",$this->FormatView($vrow[$lstArr[$i]],0),$this->Align(str_replace("@01",$vrow['lv001'],$lvTdTextBox),(int)$this->ArrView[$lstArr[$i]]));	
						break;
					/*case 'lv011':
						$vrow[$lstArr[$i]]=str_replace("\n\r","<br/>",$vrow[$lstArr[$i]]);
						$vrow[$lstArr[$i]]=str_replace("\r\n","<br/>",$vrow[$lstArr[$i]]);
						$vrow[$lstArr[$i]]=str_replace("\n","<br/>",$vrow[$lstArr[$i]]);
						$vrow[$lstArr[$i]]=str_replace("\r","<br/>",$vrow[$lstArr[$i]]);
						$vrow[$lstArr[$i]]=str_replace("<br/> <br/>","<br/>",$vrow[$lstArr[$i]]);
						$vrow[$lstArr[$i]]=str_replace("<br/><br/><br/><br/>","<br/>",$vrow[$lstArr[$i]]);
						$vrow[$lstArr[$i]]=str_replace("<br/><br/><br/>","<br/>",$vrow[$lstArr[$i]]);
						$vrow[$lstArr[$i]]=str_replace("<br/> <br/>","<br/>",$vrow[$lstArr[$i]]);
						$vrow[$lstArr[$i]]=str_replace("<br/><br/>","<br/>",$vrow[$lstArr[$i]]);
						$vrow[$lstArr[$i]]=str_replace("<br/> <br/>","<br/>",$vrow[$lstArr[$i]]);
						$vrow[$lstArr[$i]]=str_replace("<br/><br/>","<br/>",$vrow[$lstArr[$i]]);
						$vrow[$lstArr[$i]]=str_replace("<br/> <br/>","<br/>",$vrow[$lstArr[$i]]);
						$vTemp=str_replace("@02",$this->getvaluelink($lstArr[$i],$this->FormatView($vrow[$lstArr[$i]],(int)$this->ArrView[$lstArr[$i]])),$this->Align($lvTd,(int)$this->ArrView[$lstArr[$i]]));
						break;*/
					default:
						$vTemp=str_replace("@02",$this->getvaluelink($lstArr[$i],$this->FormatView($vrow[$lstArr[$i]],(int)$this->ArrView[$lstArr[$i]])),$this->Align($lvTd,(int)$this->ArrView[$lstArr[$i]]));
						break;
				}
				$strL=$strL.$vTemp;
			}
			if($this->Count==$vorder)
				$strTr=$strTr.str_replace("@#01",$strL,str_replace("@02",$vrow['lv001'],str_replace("@03",$vorder,str_replace("@01",$vorder%2,$lvTrF))));
			else
				$strTr=$strTr.str_replace("@#01",$strL,str_replace("@02",$vrow['lv001'],str_replace("@03",$vorder,str_replace("@01",$vorder%2,$lvTr))));
		}
		
		$strF=str_replace("<!--lv051-->",GetLangExcept('NetAmount',$this->lang).': ',$strF);
		$strF=str_replace("<!--lv053-->",'',$strF);
		$strF=str_replace("<!--lv054-->",$this->FormatView($slv054,10),$strF);
		$strTrH=str_replace("@#01",$strH,$lvTrH);
		$this->TextKhongSum=str_replace("@#01",$strTrH.$strTr,str_replace("@#02","",$lvTable));
		$lvTable=str_replace("@#02",$strF,$lvTable);
		return str_replace("@#01",$strTrH.$strTr,$lvTable);
	}
	/////////////////////ListFieldExport//////////////////////////
	function ListFieldExport($lvFrom,$lvList,$maxRows)
	{
		if($lvList=="") $lvList=$this->DefaultFieldList;
		$lvList=",".$lvList.",";
		$lstArr=explode(",",$this->DefaultFieldList);
		$lvSelect="<ul id=\"menu1-nav\" onkeyup=\"return CheckKeyCheckTabExp(event)\"> 
						<li class=\"menusubT1\"><img src=\"$this->Dir../images/lvicon/config.png\" border=\"0\" />".$this->ArrFunc[12]."
							<ul id=\"submenu1-nav\">
							@#01
							</ul>
						</li>
					</ul>";
		$strScript="		
		<script language=\"javascript\">
		function Export(vFrom,value)
		{
			window.open('".$this->Dir."cr_lv0052/?lang=".$this->lang."&func='+value+'&ID=".base64_encode($this->lv002)."','','width=800,height=600,left=200,top=100,screenX=0,screenY=100,resizable=yes,status=no,scrollbars=yes,menubar=yes');
		}
	
		
		</script>
";
		$lvScript="<li class=\"menuT\"> @01 </li>";
		$lvexcel="<input class=lvbtdisplay type=\"button\" id=\"lvbuttonexcel\" value=\"".$this->ArrFunc[13]."\" onclick=\"Export($lvFrom,'excel')\">";
		$lvpdf="<input class=lvbtdisplay type=\"button\" id=\"lvbutton\" value=\"".$this->ArrFunc[15]."\" onclick=\"Export($lvFrom,'pdf')\">";
		$lvword="<input class=lvbtdisplay type=\"button\" id=\"lvbutton\" value=\"".$this->ArrFunc[14]."\" onclick=\"Export($lvFrom,'word')\">";
		$strGetList="";
		$strGetScript="";
		
		$strTemp=str_replace("@01",$lvexcel,$lvScript);
		$strGetScript=$strGetScript.$strTemp;
		$strTemp=str_replace("@01",$lvword,$lvScript);
		$strGetScript=$strGetScript.$strTemp;
		$strTemp=str_replace("@01",$lvpdf,$lvScript);
		$strGetScript=$strGetScript.$strTemp;
		$strReturn=str_replace("@#01",$strGetScript,$lvSelect).$strScript;
		return $strReturn;
		
	}
	/////////////////////ListFieldSave//////////////////////////
	function ListFieldSave($lvFrom,$lvList,$maxRows,$lvOrder,$lvSortNum)
	{
		if($lvList=="") $lvList=$this->DefaultFieldList;
		$lvList=",".$lvList.",";
		$lstArr=explode(",",$this->DefaultFieldList);
		$lvArrOrder=explode(",",$lvOrder);
		$lvSelect="<ul id=\"menu-nav\" onkeyup=\"return CheckKeyCheckTab(event,$lvFrom,".count($lstArr).")\">
						<li class=\"menusubT\"><img src=\"$this->Dir../images/lvicon/config.png\" border=\"0\" />".$this->ArrFunc[11]."
							<ul id=\"submenu-nav\">
							@#01
							</ul>
						</li>
					</ul>";
		$strScript="		
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
		$lvScript="<li class=\"menuT\"> @01 </li>";
		$lvNumPage="".$this->ArrOther[2]."<input type=\"text\" class=\"lvmaxrow\" name=lvmaxrow id=lvmaxrow value=\"$maxRows\">";
		$lvSortPage="".GetLangSort(0,$this->lang)."<select class=\"lvsortrow\" name=lvsort id=lvsort >
				<option value=0 ".(($lvSortNum==0)?'selected':'').">".GetLangSort(1,$this->lang)."</option>
				<option value=1 ".(($lvSortNum==1)?'selected':'').">".GetLangSort(2,$this->lang)."</option>
				<option value=2 ".(($lvSortNum==2)?'selected':'').">".GetLangSort(3,$this->lang)."</option>
		</select>";
		$lvChk="<input type=\"checkbox\" id=\"lvdisplaychk@01\" name=\"lvdisplaychk@01\" value=\"@02\" @03><input id=\"lvorder@01\" name=\"lvorder@01\"  type=\"text\" value=\"@06\"\ style=\"width:20px\" >";
		$lvButton="<input class=lvbtdisplay type=\"button\" id=\"lvbutton\" value=\"".$this->ArrOther[1]."\" onclick=\"SelectChk($lvFrom,".count($lstArr).")\">";
		$strGetList="";
		$strGetScript="";
		$strTemp=str_replace("@01",$lvButton,$lvScript);
		$strGetScript=$strGetScript.$strTemp;
		$strTemp=str_replace("@01",$lvNumPage,$lvScript);
		$strGetScript=$strGetScript.$strTemp;
				$strTemp=str_replace("@01",$lvSortPage,$lvScript);
		$strGetScript=$strGetScript.$strTemp;
		
		for ($i=0;$i<count($lstArr);$i++)
		{
			
			$strTempChk=str_replace("@01",$i,$lvChk.$this->ArrPush[(int)$this->ArrGet[$lstArr[$i]]]);
			$strTempChk=str_replace("@02",$lstArr[$i],$strTempChk);
			
			$strTempChk=str_replace("@07",100+$i,$strTempChk);
			if(strpos($lvList,",".$lstArr[$i].",") === FALSE)
			{
				$strTempChk=str_replace("@03","",$strTempChk);
				
			}
			else
			{
				$strTempChk=str_replace("@03","checked=checked",$strTempChk);
			}
			if($lvArrOrder[$i]==NULL || $lvArrOrder[$i]=="")
				{
				$strTempChk=str_replace("@06",$i,$strTempChk);
				}
			else
				$strTempChk=str_replace("@06",$lvArrOrder[$i],$strTempChk);
			
			
			$strTemp=str_replace("@01",$strTempChk,$lvScript);
			$strGetScript=$strGetScript.$strTemp;
		}
		$strReturn=str_replace("@#01",$strGetScript,$lvSelect).$strScript;
		return $strReturn;
		
	}
	public function GetBuilCheckList($vListID,$vID,$vTabIndex)
	{
		$vListID=",".$vListID.",";
		$strTbl="<table  align=\"center\" class=\"lvtable\">
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
		$lvChk="<input type=\"checkbox\" id=\"$vID@01\" value=\"@02\" @03 title=\"@04\" tabindex=\"$vTabIndex\">";
		$lvTrH="<tr class=\"lvlinehtable1\">
			<td width=1%>@#01</td><td>@#02</td>
			
		</tr>
		";
		$vsql="select * from  hr_lv0004";
		$strGetList="";
		$strGetScript="";
		$i=0;
		$vresult=db_query($vsql);
		$numrows=db_num_rows($vresult);
		while($vrow=db_fetch_array($vresult))		
		{

			$strTempChk=str_replace("@01",$i,$lvChk);
			$strTempChk=str_replace("@02",$vrow['lv001'],$strTempChk);
			if(strpos($vListID,",".$vrow['lv001'].",") === FALSE)
				$strTempChk=str_replace("@03","",$strTempChk);
			else
				$strTempChk=str_replace("@03","checked=checked",$strTempChk);
			
			$strTempChk=str_replace("@04",$vrow['lv003'],$strTempChk);
			
			$strTemp=str_replace("@#01",$strTempChk,$lvTrH);
			$strTemp=str_replace("@#02",$vrow['lv002']."(".$vrow['lv001'].")",$strTemp);
			$strGetScript=$strGetScript.$strTemp;
						$i++;
			
		}
	 $strReturn=str_replace("@#01",$strGetScript,str_replace("@#02",$numrows,$strTbl));
	 return $strReturn;
	}
//////////////////////Buil list////////////////////
	function LV_BuilListReport($lvList,$lvFrom,$lvChkAll,$lvChk,$curRow, $maxRows,$paging,$lvOrderList,$lvSortNum)
	{
			
		if($lvList=="") $lvList=$this->DefaultFieldList;
		if($this->isView==0) return false;
		$lstArr=explode(",",$lvList);
		$lstOrdArr=explode(",",$lvOrderList);
		$lstArr=$this->getsort($lstArr,$lstOrdArr);
		$strSort="";
		switch($lvSortNum)
		{
			case 0:
				break;
			case 1:
				$strSort=" order by ".$this->LV_SortBuild($this->GB_Sort,"asc");
				break;
			case 2:
				$strSort=" order by ".$this->LV_SortBuild($this->GB_Sort,"desc");
				break;
		}
		$lvTable="
		<table  align=\"center\" class=\"lvtable\" border=1>
		@#01
		</table>
		";
		$lvTrH="<tr class=\"lvhtable\">
			<td width=1% class=\"lvhtable\">".$this->ArrPush[1]."</td>
			
			@#01
		</tr>
		";
		$lvTr="<tr class=\"lvlinehtable@01\">
			<td width=1% align=\"center\">@03</td>
			@#01
		</tr>
		";
		$lvTdH="<td width=\"@01\" class=\"lvhtable\">@02</td>";
		$lvTd="<td align=@#05>@02</td>";
		$sqlS = "SELECT * FROM cr_lv0052 WHERE 1=1  ".$this->RptCondition." $strSort LIMIT $curRow, $maxRows";
		$vorder=$curRow;
		$bResult = db_query($sqlS);
		$this->Count=db_num_rows($bResult);
		$strTrH="";
		$strTr="";
		for($i=0;$i<count($lstArr);$i++)
			{
				$vTemp=str_replace("@01","",$lvTdH);
				$vTemp=str_replace("@02",$this->ArrPush[(int)$this->ArrGet[$lstArr[$i]]],$vTemp);
				$strH=$strH.$vTemp;
				
			}
			
		while ($vrow = db_fetch_array ($bResult)){
			$strL="";
			$vorder++;
			for($i=0;$i<count($lstArr);$i++)
			{
				$vTemp=str_replace("@02",$this->getvaluelink($lstArr[$i],$this->FormatView($vrow[$lstArr[$i]],(int)$this->ArrView[$lstArr[$i]])),$this->Align($lvTd,(int)$this->ArrView[$lstArr[$i]]));
				$strL=$strL.$vTemp;
			}
			$strTr=$strTr.str_replace("@#01",$strL,str_replace("@02",$vrow['lv001'],str_replace("@03",$vorder,str_replace("@01",$vorder%2,$lvTr))));
			
		}
		$strTrH=str_replace("@#01",$strH,$lvTrH);
		return str_replace("@#01",$strTrH.$strTr,$lvTable);
	}
	
	public function LV_LinkField($vFile,$vSelectID)
	{
		return($this->CreateSelect($this->sqlcondition($vFile,$vSelectID),0));
	}
	private function sqlcondition($vFile,$vSelectID)
	{
		$vsql="";
		switch($vFile)
		{
			case 'lv1104':
				$vsql="select lv001,lv002,IF(lv001='$vSelectID',1,0) lv003 from  cr_lv0031";
				break;
		}
		return $vsql;
	}
	private function getvaluelink($vFile,$vSelectID)
	{
		if($this->ArrGetValueLink[$vFile][$vSelectID][0]) return $this->ArrGetValueLink[$vFile][$vSelectID][1];
		if($vSelectID=="")
		{
			return $vSelectID;
		}
		switch($vFile)
		{
			case 'lv1104':
				$vsql="select lv001,lv002,IF(lv001='$vSelectID',1,0) lv003 from  cr_lv0031 where lv001='$vSelectID'";
				break;
			default:
				$vsql ="";
				break;
		}
		if($vsql=="")
		{
			return $vSelectID;
		}
		else
		{
			$lvResult = db_query($vsql);
			$this->ArrGetValueLink[$vFile][$vSelectID][0]=true;
		}
		while($row= db_fetch_array($lvResult)){
			$this->ArrGetValueLink[$vFile][$vSelectID][1]=($lvopt==0)?$row['lv002']:(($lvopt==1)?$row['lv001']."(".$row['lv002'].")":(($lvopt==2)?$row['lv002']."(".$row['lv001'].")":$row['lv001']));
			return $this->ArrGetValueLink[$vFile][$vSelectID][1];
		}
		
	}
}
?>