	<?php
	ob_start(); // Turn on output buffering
	session_start(); 
	header("Content-Type: application/json; charset=UTF-8");
	include("../soft/config.php");
	include("../soft/function.php");
	include("lv_controler.php");
	$_SESSION['SERVICESDEM']=$_SESSION['SERVICESDEM']+1;
	if($_SESSION['SERVICESDEM']>100)
	{
		$vArrJon['code']=107;
		$vArrJon['message']='Không thành công';
		$vArrJon['notes']='Bạn đã truy cập không thành công quá nhiều lần!';
		echo json_encode($vArrJon);
		exit();
	}
	
$data = json_decode(file_get_contents('php://input'), true);
if($data!=null)
{
	//foreach($data as $vArrValues)
	{
	//print_r($data);
	//$vArrValues= json_decode($vrow['reportData']);
	$vLineHead=true;
	$memberid='';
	foreach($data  as $key => $vCode)
	{
		switch($key)
		{
			case 'token':
				$token=$vCode;
				break;
			case 'dtackey':
				$dtackey=$vCode;
				break;
			case 'privatekey':
				$privatekey=$vCode;
				break;
			case 'memberid':
				$memberid=$vCode;
				break;
			case 'hdbh':
				$hdbh=$vCode;
				break;
			case 'hsycbh':
				$hsycbh=$vCode;
				break;
			case 'phispchinh':
				$phispchinh=$vCode;
				break;
			case 'tongphi':
				$tongphi=$vCode;
				break;
			case 'sothang':
				$sothang=$vCode;
				break;
			case 'ngayd':
				$ngayd=$vCode;
				break;
			case 'ngayh':
				$ngayh=$vCode;
				break;
			case 'ngaycapdon':
				$ngaycapdon=$vCode;
				break;
			case 'dailybh':
				$dailybh=$vCode;
				break;
			case 'tvvdoclap':
				$tvvdoclap=$vCode;
				break;
			case 'trangthai':
				$trangthai=$vCode;
				break;
			case 'kymoi':
				$kymoi=$vCode;
				break;
			case 'hotenkh':
				$hotenkh=$vCode;
				break;
			case 'cmnd':
				$cmnd=$vCode;			
				if(trim($cmnd)=='' || $cmnd==null)
				{
					$cmnd='KHONGCMND';
				}
				break;
			case 'ngaysinh':
				$ngaysinh=$vCode;
				break;
			case 'dienthoai':
				$dienthoai=$vCode;
				break;
			case 'diachi':
				$diachi=$vCode;
				break;
			case 'email':
				$email=$vCode;
				break;
			case 'diachi':
				$diachi=$vCode;
				break;
			case 'chitiethd':
				$chitiethd=$vCode;
				break;
		}
	}
	if(($dtackey=='' || $dtackey==NULL) && ($privatekey=='' || $privatekey==NULL))
	{
		$vArrJon['code']=103;
		$vArrJon['message']='Không thành công';
		$vArrJon['notes']='Bạn không có key!';
		echo json_encode($vArrJon);
		exit();
	}
	if($dtackey=='' || $dtackey==NULL)
	{
		$vArrJon['code']=104;
		$vArrJon['message']='Không thành công';
		$vArrJon['notes']='Bạn không có key!';
		echo json_encode($vArrJon);
		exit();
	}
	if($privatekey=='' || $privatekey==NULL)
	{
		$vArrJon['code']=105;
		$vArrJon['message']='Không thành công';
		$vArrJon['notes']='Bạn không có key!';
		echo json_encode($vArrJon);
		exit();
	}
	if($token=='' || $token==NULL)
	{
		$vArrJon['code']=116;
		$vArrJon['message']='Token không đúng';
		$vArrJon['notes']=$vArrKQ[1];
		exit();
	}
	else
	{
		$vsql="select lv001,lv103 congtybaohiem,lv104 DataSave from lv_lv0007 where lv101='$dtackey' and lv102='$privatekey' and (lv197='$token' and ((TIME_TO_SEC(now())-TIME_TO_SEC(lv198))<3600 and (TIME_TO_SEC(now())-TIME_TO_SEC(lv198))/60>=0) ) and lv096=0";
		$vresult=db_query($vsql);
		if($vresult)
		{
			$vnum=db_num_rows($vresult);
		}
		if($vnum>0)
		{
			$vrow=db_fetch_array($vresult);
			$congtybaohiem=$vrow['congtybaohiem'];
			$vDataSave=$vrow['DataSave'].".";
			$vIsRun=true;
			$user=$vrow['lv001'];
			$_SESSION['SERVICESDEM']=0;
			$vArrHopDong=Array();
			/*$vArrHopDong[1]['sanpham']='phinhantho';
			$vArrHopDong[1]['phibh']=10000000;
			$vArrHopDong[1]['thue']=10;
			$vArrHopDong[1]['spchinh']=1;
			$vArrHopDong[2]['sanpham']='phinhanthobosung1';
			$vArrHopDong[2]['phibh']=1000000;
			$vArrHopDong[2]['thue']=10;
			$vArrHopDong[2]['spchinh']=0;
			$vArrHopDong[3]['sanpham']='phinhanthobosung2';
			$vArrHopDong[3]['phibh']=1000000;
			$vArrHopDong[3]['thue']=10;
			$vArrHopDong[3]['spchinh']=0;
			$vArrHopDong[4]['sanpham']='phinhanthobosung3';
			$vArrHopDong[4]['phibh']=1000000;
			$vArrHopDong[4]['thue']=10;
			$vArrHopDong[4]['spchinh']=0;
			$token_key=$_GET['token_key'];//token_key
			
			if($token_key=='' || $token_key==NULL) $token_key=$_POST['token_key'];
			$vIsRun=false;
			$congtybaohiem='';
			$vDataSave='';
			switch($token_key)
			{
				case 'BIZNET03KC05VKBUNFS934AMN29F':
					$congtybaohiem='mic';
					$vDataSave='biznet_mic.';
					$vIsRun=true;
					$user='mic.admin';
					break;
			}
			*/
			if($vIsRun && $congtybaohiem!='')
			{
				//Json theo cấu trúc ví dụ dưói		
				$vOutput=Array();
				require_once("../clsall/sl_lv0217.php");
				$lvsl_lv0217=new sl_lv0217('admin','admin','Sl0217');
				
				$vArrKQ=$lvsl_lv0217->LV_CheckServerice($memberid,$hdbh,$hsycbh,$phispchinh,$tongphi,$sothang,$ngayd,$ngayh,$dailybh,$tvvdoclap,$trangthai,$kymoi,$chitiethd1,$hotenkh,$cmnd,$ngaysinh,$dienthoai,$diachi,$email);
				if($vArrKQ[0]==1)
				{
						$vIDBaoHiem=$lvsl_lv0217->LV_CheckExist($vDataSave,$hdbh,$congtybaohiem);
						if($vIDBaoHiem==null)
						{
							$lvsl_lv0217->lv103=$congtybaohiem;
							require_once("../clsall/sl_lv0001.php");
							require_once("../clsall/sl_lv0007.php");
							$lvsl_lv0001=new sl_lv0001('admin','admin','Sl0217');
							$lvsl_lv0007=new sl_lv0007('admin','admin','Sl0217');
							$lvsl_lv0217->LV_UserID=$user;
							$lvsl_lv0001->lv002=$hotenkh;
							$lvsl_lv0001->lv013=$cmnd;
							$lvsl_lv0001->lv017=recoverdate($ngaysinh,'VN');//substr($ngaysinh,0,4)."/".substr($ngaysinh,4,2)."/".substr($ngaysinh,7,2);
							$lvsl_lv0001->lv011=$dienthoai;
							$lvsl_lv0001->lv006=$diachi;
							$lvsl_lv0001->lv015=$email;
							$lvsl_lv0001->lv025=$memberid;
							$vCusID='';
							if(trim($lvsl_lv0001->lv013)!='' && trim($lvsl_lv0001->lv013)!='KHONGCMND')
							{
								$vCusID=$lvsl_lv0001->LV_CheckCMND_Service($vDataSave,$memberid,$lvsl_lv0001->lv013);
							}
							else
							{
								if(trim($lvsl_lv0001->lv011)!='')
								{
									$vCusID=$lvsl_lv0001->LV_CheckSDT_Service($vDataSave,$memberid,$lvsl_lv0001->lv011);
								}
								else
								{
									$vCusID=$lvsl_lv0001->LV_CheckEmail_Service($vDataSave,$memberid,$lvsl_lv0001->lv015);
								}
							}
							if($vCusID=='')
							{
								$lvsl_lv0001->lv001=$memberid.str_replace(" ","",str_replace("-","",str_replace(":","",$lvsl_lv0217->DateCurrent))).rand(1,9).rand(0,9);
								$lvsl_lv0001->LV_InsertMember_Service($vDataSave);
							}
							else
							{
								$lvsl_lv0001->lv001=$vCusID;
								$lvsl_lv0001->LV_UpdateMember_Service($vDataSave);
							}
							$lvsl_lv0217->lv112=$hdbh;
							$lvsl_lv0217->lv014=$hsycbh;
							$lvsl_lv0217->lv022=$phispchinh;
							$lvsl_lv0217->lv010=$memberid;
							$lvsl_lv0217->lv108=$tongphi;
							$lvsl_lv0217->lv027=$sothang;
							if(trim($ngaycapdon)!='' && $ngaycapdon!=null)
								$lvsl_lv0217->lv105=recoverdate($ngaycapdon,'VN');
							else
								$lvsl_lv0217->lv105=recoverdate($ngayd,'VN');
							//$lvsl_lv0217->lv105=recoverdate($ngayd,'VN');//substr($ngayd,0,4)."/".substr($ngayd,4,2)."/".substr($ngayd,6,2);
							//$lvsl_lv0217->lv107=substr($ngayd,0,4)."/".substr($ngayd,4,2)."/".substr($ngayd,6,2);
							$lvsl_lv0217->lv004=recoverdate($ngayd,'VN');//substr($ngayd,0,4)."/".substr($ngayd,4,2)."/".substr($ngayd,6,2);
							$lvsl_lv0217->lv005=recoverdate($ngayh,'VN');//substr($ngayh,0,4)."/".substr($ngayh,4,2)."/".substr($ngayh,6,2);
							$lvsl_lv0217->lv008='SAIGON';
							$lvsl_lv0217->lv101=$dailybh;
							$lvsl_lv0217->lv102=$tvvdoclap;
							$lvsl_lv0217->lv011=$trangthai;
							if($kymoi=='taituc')
								$lvsl_lv0217->lv015=1;
							else
								$lvsl_lv0217->lv015=0;
							$lvsl_lv0217->datachitiet=$chitiethd;
							//Khách hàng sau khi tạo xong xẻ được đẩy vào đây.			
							$lvsl_lv0217->lv002=$lvsl_lv0001->lv001;
							if($lvsl_lv0217->lv108>=0)
							{
								$vresult=$lvsl_lv0217->LV_InsertXML_ServiceBHV($vDataSave);
								$vArrJon['code']=100;
								$vArrJon['message']='Thành công';
								$vArrJon['notes']=$vArrKQ[1];
							}
							else
							{
								
								$vArrJon['code']=102;
								$vArrJon['message']='Không thành công đẩy dữ liệu vào hệ thống BIZNET. XIn vui lòng liên hệ BIZNET để được hỗ trợ!';
								$vArrJon['notes']=$vArrKQ[1];
							}
					}
					else
					{
						$lvsl_lv0217->lv107=substr($ngayh,0,4)."/".substr($ngayh,4,2)."/".substr($ngayh,7,2);
						$lvsl_lv0217->lv011=$trangthai;
						$lvsl_lv0217->lv001=$vIDBaoHiem;
						$vresult=$lvsl_lv0217->LV_UpdateXML_Service($vDataSave);
						$vArrJon['code']=100;
						$vArrJon['message']='Cập nhật đơn hàng '.$lvsl_lv0217->lv001.' về ngày hoàn thành '.$lvsl_lv0217->lv107.' và trạng thái :'.$lvsl_lv0217->lv011;
						$vArrJon['notes']=$vArrKQ[1];
					}
					$lvsl_lv0217->LV_UpdateXML_Service_Logs($vDataSave,$vArrJon['notes']);
				}
				else
				{
					$vArrJon['code']=101;
					$vArrJon['message']='Không thành công';
					$vArrJon['notes']=$vArrKQ[1];
					$lvsl_lv0217->LV_UpdateXML_Service_Logs($vDataSave,$vArrJon['notes']);
				}
			}
			else
			{
				$vArrJon['code']=120;
				$vArrJon['message']='Tài khoản này không phù có liên kết Công Ty Bảo Hiểm';
				$vArrJon['notes']=$vArrKQ[1];
			}
		
			
			/*print_r($vArrJSON);
			if($vLineHead)
			{
				$vArrTitle=$vArrJSON;
				foreach($vArrTitle as $key => $vArrHead)
				{
					echo $key.'=>';
					foreach($vArrHead as $vCode)
					{
						$vArrHeader[$key]=$vCode;
						echo $vCode.'<br/>';
						break;
					}
					//echo $key."<br/>ABCDEF</br>";
				}
				print_r($vArrHeader);
				$vLineHead=false;
			}
			else
			{
				foreach($data as $key => $vArrHead)
				{
					foreach($vArrHead as $cot => $vCode)
					{
						$vField=$vArrCot[$vArrHeader[$cot]];
						if($vField!='')
						{
							//echo  $vArrHeader[$key].'|'.$key.'=>'.$vField	;
							//echo '<br/>';
							
							if($this->Rowss[$vField]>0 && $vArrCotCong[$vField]==1)
							{
								$this->Rowss[$vField]=$this->Rowss[$vField]+$vCode;
							}
							else
								$this->Rowss[$vField]=$vCode;
								
						}
						//echo $key.'.'..$cot.'==>'.$vCode.'<br/>';
						//break;
					}

					//$this->LV_InsertAutoSAP();
				}
				
			}
			*/
		
			
		}
		else
		{
			$vArrJon['code']=106;
			$vArrJon['message']='Key không đúng';
			$vArrJon['notes']=$vArrKQ[1];
		}
	}
}
}
else
		{
			$token=$_GET['token'];//token
			if($token=='' || $token==NULL) $token=$_POST['token'];
			$dtackey=$_GET['dtackey'];//dtackey
			if($dtackey=='' || $dtackey==NULL) $dtackey=$_POST['dtackey'];
			$privatekey=$_GET['privatekey'];//privatekey
			if($privatekey=='' || $privatekey==NULL) $privatekey=$_POST['privatekey'];
			if(($dtackey=='' || $dtackey==NULL) && ($privatekey=='' || $privatekey==NULL))
			{
				$vArrJon['code']=103;
				$vArrJon['message']='Không thành công';
				$vArrJon['notes']='Bạn không có key!';
				echo json_encode($vArrJon);
				exit();
			}
			if($dtackey=='' || $dtackey==NULL)
			{
				$vArrJon['code']=104;
				$vArrJon['message']='Không thành công';
				$vArrJon['notes']='Bạn không có key!';
				echo json_encode($vArrJon);
				exit();
			}
			if($privatekey=='' || $privatekey==NULL)
			{
				$vArrJon['code']=105;
				$vArrJon['message']='Không thành công';
				$vArrJon['notes']='Bạn không có key!';
				echo json_encode($vArrJon);
				exit();
			}
			if($token=='' || $token==NULL)
			{
				$vArrJon['code']=116;
				$vArrJon['message']='Token không đúng';
				$vArrJon['notes']=$vArrKQ[1];
				exit();
			}
			else
			{
			$vsql="select lv001,lv103 congtybaohiem,lv104 DataSave from lv_lv0007 where lv101='$dtackey' and lv102='$privatekey' and (lv197='$token' and ((TIME_TO_SEC(now())-TIME_TO_SEC(lv198))<3600 and (TIME_TO_SEC(now())-TIME_TO_SEC(lv198))/60>=0) ) and lv096=0";
			$vresult=db_query($vsql);
			if($vresult)
			{
				$vnum=db_num_rows($vresult);
			}
			if($vnum>0)
			{
				$vrow=db_fetch_array($vresult);
				$congtybaohiem=$vrow['congtybaohiem'];
				$vDataSave=$vrow['DataSave'].".";
				$vIsRun=true;
				$user=$vrow['lv001'];
				$_SESSION['SERVICESDEM']=0;
				$vArrHopDong=Array();
				/*$vArrHopDong[1]['sanpham']='phinhantho';
				$vArrHopDong[1]['phibh']=10000000;
				$vArrHopDong[1]['thue']=10;
				$vArrHopDong[1]['spchinh']=1;
				$vArrHopDong[2]['sanpham']='phinhanthobosung1';
				$vArrHopDong[2]['phibh']=1000000;
				$vArrHopDong[2]['thue']=10;
				$vArrHopDong[2]['spchinh']=0;
				$vArrHopDong[3]['sanpham']='phinhanthobosung2';
				$vArrHopDong[3]['phibh']=1000000;
				$vArrHopDong[3]['thue']=10;
				$vArrHopDong[3]['spchinh']=0;
				$vArrHopDong[4]['sanpham']='phinhanthobosung3';
				$vArrHopDong[4]['phibh']=1000000;
				$vArrHopDong[4]['thue']=10;
				$vArrHopDong[4]['spchinh']=0;
				$token_key=$_GET['token_key'];//token_key
				
				if($token_key=='' || $token_key==NULL) $token_key=$_POST['token_key'];
				$vIsRun=false;
				$congtybaohiem='';
				$vDataSave='';
				switch($token_key)
				{
					case 'BIZNET03KC05VKBUNFS934AMN29F':
						$congtybaohiem='mic';
						$vDataSave='biznet_mic.';
						$vIsRun=true;
						$user='mic.admin';
						break;
				}
				*/
				if($vIsRun && $congtybaohiem!='')
				{
				//echo $data["operacion"];
				//Mã biznet của thành viên
				$memberid=$_GET['memberid'];
				if($memberid=='' || $memberid==NULL) $memberid=$_POST['memberid'];
				//HĐBH
				$hdbh=$_GET['hdbh'];
				if($hdbh=='' || $hdbh==NULL) $hdbh=$_POST['hdbh'];
				//Số Hồ Sơ
				$hsycbh=$_GET['hsycbh'];
				if($hsycbh=='' || $hsycbh==NULL) $hsycbh=$_POST['hsycbh'];
				//Phí bảo hiểm sản phẩm chính
				$phispchinh=$_GET['phispchinh'];
				if($phispchinh=='' || $phispchinh==NULL) $phispchinh=$_POST['phispchinh'];
				//Tổng phí bảo hiểm ( Gồm sản phẩm chính + sản phẩm bổ sung)
				$tongphi=$_GET['tongphi'];
				if($tongphi=='' || $tongphi==NULL) $tongphi=$_POST['tongphi'];
				//Số tháng đóng (1,3,6,12)
				$sothang=$_GET['sothang'];
				if($sothang=='' || $sothang==NULL) $sothang=$_POST['sothang'];
				//Ngày bắt đầu họp đồng ( định dạng YYYYMMdd)
				$ngayd=$_GET['ngayd'];
				if($ngayd=='' || $ngayd==NULL) $ngayd=$_POST['ngayd'];
				//Hoàn thành hợp đồng ( định dạng YYYYMMdd, nếu chưa có bỏ trống)
				$ngayh=$_GET['ngayh'];
				if($ngayh=='' || $ngayh==NULL) $ngayh=$_POST['ngayh'];
				//ngaycapdon
				$ngaycapdon=$_GET['ngaycapdon'];
				if($ngaycapdon=='' || $ngaycapdon==NULL) $ngaycapdon=$_POST['ngaycapdon'];
				//Mã đại lý bảo hiểm
				$dailybh=$_GET['dailybh'];
				if($dailybh=='' || $dailybh==NULL) $dailybh=$_POST['dailybh'];
				//Mã tư vấn viên độc lập
				$tvvdoclap=$_GET['tvvdoclap'];
				if($tvvdoclap=='' || $tvvdoclap==NULL) $tvvdoclap=$_POST['tvvdoclap'];
				//Trạng thái đơn (-1:Huỷ,0:Đang xử lý,1:Đã phát hành,2:Đã bàn giao hợp đồng,3:Hoàn thành )
				$trangthai=$_GET['trangthai'];
				if($trangthai=='' || $trangthai==NULL) $trangthai=$_POST['trangthai'];
				//Ký mới/Tái tục ( kymoi, taituc)
				$kymoi=$_GET['kymoi'];
				if($kymoi=='' || $kymoi==NULL) $kymoi=$_POST['kymoi'];
				//Json theo cấu trúc ví dụ dưói
				$chitiethd=$_GET['chitiethd'];
				if($chitiethd=='' || $chitiethd==NULL) $chitiethd=$_POST['chitiethd'];
				$vOutput=Array();
				//Khách hàng
				//Họ tên khách hàng
				$hotenkh=$_GET['hotenkh'];
				if($hotenkh=='' || $hotenkh==NULL) $hotenkh=$_POST['hotenkh'];
				//Số CMND khách  hàng
				$cmnd=$_GET['cmnd'];
				if($cmnd=='' || $cmnd==NULL) $cmnd=$_POST['cmnd'];
				//Ngày sinh khách hàng
				$ngaysinh=$_GET['ngaysinh'];
				if($ngaysinh=='' || $ngaysinh==NULL) $ngaysinh=$_POST['ngaysinh'];
				//Điện thoại khách hàng
				$dienthoai=$_GET['dienthoai'];
				if($dienthoai=='' || $dienthoai==NULL) $dienthoai=$_POST['dienthoai'];
				//Địa chỉ khách hàng
				$diachi=$_GET['diachi'];
				if($diachi=='' || $diachi==NULL) $diachi=$_POST['diachi'];
				//Email khách hàng
				$email=$_GET['email'];
				if($email=='' || $email==NULL) $email=$_POST['email'];
				require_once("../clsall/sl_lv0217.php");
				$lvsl_lv0217=new sl_lv0217('admin','admin','Sl0217');			
				if(trim($cmnd)=='' || $cmnd==null)
				{
					$cmnd='KHONGCMND';
				}
				
				$vArrKQ=$lvsl_lv0217->LV_CheckServerice($memberid,$hdbh,$hsycbh,$phispchinh,$tongphi,$sothang,$ngayd,$ngayh,$dailybh,$tvvdoclap,$trangthai,$kymoi,$chitiethd,$hotenkh,$cmnd,$ngaysinh,$dienthoai,$diachi,$email);
				if($vArrKQ[0]==1)
				{
						$vIDBaoHiem=$lvsl_lv0217->LV_CheckExist($vDataSave,$hdbh,$congtybaohiem);
						if($vIDBaoHiem==null)
						{
							$lvsl_lv0217->lv103=$congtybaohiem;
							require_once("../clsall/sl_lv0001.php");
							require_once("../clsall/sl_lv0007.php");
							$lvsl_lv0001=new sl_lv0001('admin','admin','Sl0217');
							$lvsl_lv0007=new sl_lv0007('admin','admin','Sl0217');
							$lvsl_lv0217->LV_UserID=$user;
							$lvsl_lv0001->lv002=$hotenkh;
							$lvsl_lv0001->lv013=$cmnd;
							$lvsl_lv0001->lv017=recoverdate($ngaysinh,'VN');//substr($ngaysinh,0,4)."/".substr($ngaysinh,4,2)."/".substr($ngaysinh,7,2);
							$lvsl_lv0001->lv011=$dienthoai;
							$lvsl_lv0001->lv006=$diachi;
							$lvsl_lv0001->lv015=$email;
							$lvsl_lv0001->lv025=$memberid;
							$vCusID='';
							if(trim($lvsl_lv0001->lv013)!='' && trim($lvsl_lv0001->lv013)!='KHONGCMND')
							{
								$vCusID=$lvsl_lv0001->LV_CheckCMND_Service($vDataSave,$memberid,$lvsl_lv0001->lv013);
							}
							else
							{
								if(trim($lvsl_lv0001->lv011)!='')
								{
									$vCusID=$lvsl_lv0001->LV_CheckSDT_Service($vDataSave,$memberid,$lvsl_lv0001->lv011);
								}
								else
								{
									$vCusID=$lvsl_lv0001->LV_CheckEmail_Service($vDataSave,$memberid,$lvsl_lv0001->lv015);
								}
							}
							if($vCusID=='')
							{
								$lvsl_lv0001->lv001=$memberid.str_replace(" ","",str_replace("-","",str_replace(":","",$lvsl_lv0217->DateCurrent))).rand(1,9).rand(0,9);
								$lvsl_lv0001->LV_InsertMember_Service($vDataSave);
							}
							else
							{
								$lvsl_lv0001->lv001=$vCusID;
								$lvsl_lv0001->LV_UpdateMember_Service($vDataSave);
							}
							$lvsl_lv0217->lv112=$hdbh;
							$lvsl_lv0217->lv014=$hsycbh;
							$lvsl_lv0217->lv022=$phispchinh;
							$lvsl_lv0217->lv010=$memberid;
							$lvsl_lv0217->lv108=$tongphi;
							$lvsl_lv0217->lv027=$sothang;
							if(trim($ngaycapdon)!='' && $ngaycapdon!=null)
								$lvsl_lv0217->lv105=recoverdate($ngaycapdon,'VN');
							else
								$lvsl_lv0217->lv105=recoverdate($ngayd,'VN');
							//$lvsl_lv0217->lv105=recoverdate($ngayd,'VN');//substr($ngayd,0,4)."/".substr($ngayd,4,2)."/".substr($ngayd,6,2);
							//$lvsl_lv0217->lv107=substr($ngayd,0,4)."/".substr($ngayd,4,2)."/".substr($ngayd,6,2);
							$lvsl_lv0217->lv004=recoverdate($ngayd,'VN');//substr($ngayd,0,4)."/".substr($ngayd,4,2)."/".substr($ngayd,6,2);
							$lvsl_lv0217->lv005=recoverdate($ngayh,'VN');//substr($ngayh,0,4)."/".substr($ngayh,4,2)."/".substr($ngayh,6,2);
							$lvsl_lv0217->lv008='SAIGON';
							$lvsl_lv0217->lv101=$dailybh;
							$lvsl_lv0217->lv102=$tvvdoclap;
							$lvsl_lv0217->lv011=$trangthai;
							if($kymoi=='taituc')
								$lvsl_lv0217->lv015=1;
							else
								$lvsl_lv0217->lv015=0;
							$lvsl_lv0217->datachitiet=$chitiethd;
							//Khách hàng sau khi tạo xong xẻ được đẩy vào đây.			
							$lvsl_lv0217->lv002=$lvsl_lv0001->lv001;
							if($lvsl_lv0217->lv108>=0)
							{
								$vresult=$lvsl_lv0217->LV_InsertXML_Service($vDataSave);
								$vArrJon['code']=100;
								$vArrJon['message']='Thành công';
								$vArrJon['notes']=$vArrKQ[1];
							}
							else
							{
								
								$vArrJon['code']=102;
								$vArrJon['message']='Không thành công đẩy dữ liệu vào hệ thống BIZNET. XIn vui lòng liên hệ BIZNET để được hỗ trợ!';
								$vArrJon['notes']=$vArrKQ[1];
							}
					}
					else
					{
						$lvsl_lv0217->lv107=substr($ngayh,0,4)."/".substr($ngayh,4,2)."/".substr($ngayh,7,2);
						$lvsl_lv0217->lv011=$trangthai;
						$lvsl_lv0217->lv001=$vIDBaoHiem;
						$vresult=$lvsl_lv0217->LV_UpdateXML_Service($vDataSave);
						$vArrJon['code']=100;
						$vArrJon['message']='Cập nhật đơn hàng '.$lvsl_lv0217->lv001.' về ngày hoàn thành '.$lvsl_lv0217->lv107.' và trạng thái :'.$lvsl_lv0217->lv011;
						$vArrJon['notes']=$vArrKQ[1];
					}
					$lvsl_lv0217->LV_UpdateXML_Service_Logs($vDataSave,$vArrJon['notes']);
				}
				else
				{
					$vArrJon['code']=101;
					$vArrJon['message']='Không thành công';
					$vArrJon['notes']=$vArrKQ[1];
					$lvsl_lv0217->LV_UpdateXML_Service_Logs($vDataSave,$vArrJon['notes']);
				}
			}
			else
			{
				$vArrJon['code']=106;
				$vArrJon['message']='Key không đúng';
				$vArrJon['notes']=$vArrKQ[1];
			}
		}
	}
}
echo json_encode($vArrJon);
?>
