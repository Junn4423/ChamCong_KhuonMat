<?php
// ini_set('display_errors', 1); 
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

switch ($vtable) {

    // case "cr_lv0150":
    //     include_once("./class/cr_lv0150.php");
    //     $cr_lv0150 = new cr_lv0150($_SESSION['ERPSOFV2RRight'], $_SESSION['ERPSOFV2RUserID'], 'Cr0150');
    //     switch ($vfun) {
    //         case "data":
    //             $cr_lv0150->lv001 = $input['maCv'] ?? $_POST['maCv'] ?? null;
    //             $cr_lv0150->lang = "VN";
    //             $objEmp = $cr_lv0150->Mb_LayThongTinCV($cr_lv0150->lv001);
    //             var_dump($objEmp);
    //             $vOutput = [];
    //             while ($vrow = db_fetch_array($objEmp)) {
    //                 $vOutput[] = [
    //                     "maChiTietTam" => $vrow['lv001'],
    //                     "maNhanVien" => $vrow['lv002'],
    //                     "tien" => $vrow['lv003'],
    //                     "tiGiaQuyDoi" => $vrow['lv004'],
    //                     "maTaiKhoanNo" => $vrow['lv005'],
    //                     "moTa" => $vrow['lv007']
    //                 ];
    //             }
    //             break;
    //     }
    //     break;
    case "ac_lv0002":
        include("./class/ac_lv0002.php");
        $ac_lv0002 = new ac_lv0002($_SESSION['ERPSOFV2RRight'], $_SESSION['ERPSOFV2RUserID'], 'Ac0002');
        switch ($vfun) {
            case "data":
                $objEmp = $ac_lv0002->Mb_LoadAll();
                $vOutput = [];
                while ($vrow = db_fetch_array($objEmp)) {
                    $vOutput[] = [
                        "maTaiKhoanQuy" => $vrow['lv001'],
                        "tenTaiKhoanQuy" => $vrow['lv002'],
                    ];
                }
                break;
        }
        break;
    case "hr_lv0020":
        include_once("./class/hr_lv0020");
        $hr_lv0020 = new hr_lv0020($_SESSION['ERPSOFV2RRight'], $_SESSION['ERPSOFV2RUserID'], 'Hr0020');
        switch ($vfun) {
            case "layNhanVienTheoMa":
                $hr_lv0020->lv001 = $input['maNhanVien'] ?? $_POST['maNhanVien'] ?? null;
                $objEmp = $hr_lv0020->layNhanVienTheoMa($hr_lv0020->lv001);
                $vOutput = [];
                while ($vrow = db_fetch_array($objEmp)) {
                    $vOutput[] = [
                        "maNhanVien" => $vrow['lv001'],
                        "tenNhanVien" => $vrow['lv002'],
                        "soDienThoai" => $vrow['lv039'],
                        "email" => $vrow['lv041'],
                        "maPhongBan" => $vrow['lv029'],
                    ];
                }
                break;
        }
        break;

    case "cr_lv0285":
        include_once("./class/cr_lv0285.php");
        $cr_lv0285 = new cr_lv0285($_SESSION['ERPSOFV2RRight'], $_SESSION['ERPSOFV2RUserID'], 'Cr0285');
        switch ($vfun) {
            case "add":
                $cr_lv0285->lv002 = $input['maNhanVien'] ?? $_POST['maNhanVien'] ?? null;
                $cr_lv0285->lv003 = $input['tien'] ?? $_POST['tien'] ?? null;
                $cr_lv0285->lv004 = $input['tiGiaQuyDoi'] ?? $_POST['tiGiaQuyDoi'] ?? null;
                $cr_lv0285->lv005 = $input['maTaiKhoanNo'] ?? $_POST['maTaiKhoanNo'] ?? null;
                $cr_lv0285->lv006 = "1111";
                $cr_lv0285->lv007 = $input['moTa'] ?? $_POST['moTa'] ?? null;
                $objEmp = $cr_lv0285->LV_Insert();

                break;
            case "data":
                $cr_lv0285->lv002 = $input['maNhanVien'] ?? $_POST['maNhanVien'] ?? null;
                $objEmp = $cr_lv0285->Mb_LoadId($cr_lv0285->lv002);
                $vOutput = [];
                while ($vrow = db_fetch_array($objEmp)) {
                    $vOutput[] = [
                        "maChiTietTam" => $vrow['lv001'],
                        "maNhanVien" => $vrow['lv002'],
                        "tien" => $vrow['lv003'],
                        "tiGiaQuyDoi" => $vrow['lv004'],
                        "maTaiKhoanNo" => $vrow['lv005'],
                        "moTa" => $vrow['lv007']
                    ];
                }
                break;
        }
    case "hr_kb0002":
        include_once("./class/hr_kb0002 copy.php");
        include_once("./class/hr_kb0006.php");
        include_once("./class/hr_kb0009.php");
        include_once("./class/hr_kb0012.php");

        include_once("./class/hr_kb0007.php");
        include_once("./class/hr_kb0010.php");
        include_once("./class/hr_kb0013.php");

        $hr_kb0006 = new hr_kb0006($_SESSION['ERPSOFV2RRight'], $_SESSION['ERPSOFV2RUserID'], 'Kb0006');
        $hr_kb0009 = new hr_kb0009($_SESSION['ERPSOFV2RRight'], $_SESSION['ERPSOFV2RUserID'], 'Kb0009');
        $hr_kb0012 = new hr_kb0012($_SESSION['ERPSOFV2RRight'], $_SESSION['ERPSOFV2RUserID'], 'Kb0012');

        $hr_kb0007 = new hr_kb0007($_SESSION['ERPSOFV2RRight'], $_SESSION['ERPSOFV2RUserID'], 'Kb0007');
        $hr_kb0010 = new hr_kb0010($_SESSION['ERPSOFV2RRight'], $_SESSION['ERPSOFV2RUserID'], 'Kb0010');
        $hr_kb0013 = new hr_kb0013($_SESSION['ERPSOFV2RRight'], $_SESSION['ERPSOFV2RUserID'], 'Kb0013');

        $hr_kb0002 = new hr_kb0002($_SESSION['ERPSOFV2RRight'], $_SESSION['ERPSOFV2RUserID'], 'Kb0002');
        switch ($vfun) {
            case 'data':
                $objEmp = $hr_kb0002->KB_Load();
                $vOutput = [];
                while ($vrow = db_fetch_array($objEmp)) {
                    // Lấy danh sách file từ bảng documents
                    $files = $hr_kb0002->Mb_GetFileContent($vrow['lv001']);
                    $fileNames = [];
                    if ($files) {
                        foreach ($files as $f) {
                            $fileNames[] = $f['fileName'];
                        }
                    }
                    
                    $vOutput[] = [
                        'maPhieu' => $vrow['lv001'],

                        'maNhanVien' => $vrow['lv002'],
                        'ngayDeXuat' => $vrow['lv003'],
                        'loaiChamCong' => $vrow['lv004'],
                        'viTri' => $vrow['lv005'],

                        'trangThaiCap1' => $vrow['lv006'],
                        'trangThaiCap2' => $vrow['lv046'],
                        'trangThaiCap3' => $vrow['lv021'],
                        'hinhAnh' => $vrow['lv007'], // Tên file trong bảng chính
                        'files' => $fileNames, // Danh sách tên file từ bảng documents

                        'ngayDuyetCap1' => $vrow['lv047'],
                        'ngayDuyetCap2' => $vrow['lv018'],
                        'ngayDuyetCap3' => $vrow['lv019'],

                        'nguoiDuyetCap1' => $vrow['lv013'],
                        'nguoiDuyetCap2' => $vrow['lv014'],
                        'nguoiDuyetCap3' => $vrow['lv020'],

                        'ngayKhongDuyet1' => $vrow['lv045'],


                    ];
                }

                header('Content-Type: application/json');
                echo json_encode($vOutput);
                exit;

            case "getChamCongHistory":
                $maNhanVien = $input['maNhanVien'] ?? $_POST['lv002'] ?? null;
                $objEmp = $hr_kb0002->KB_LoadID($maNhanVien);
                $vOutput = [];
                while ($vrow = db_fetch_array($objEmp)) {
                    $vOutput[] = [
                        'maNhanVien' => $vrow['lv002'],
                        'ngayDeXuat' => $vrow['lv003'],
                        'loaiChamCong' => $vrow['lv004'],
                        'viTri' => $vrow['lv005'],
                        'trangThai' => $vrow['lv006'],
                        'hinhAnh' => $vrow['lv007'],
                    ];
                }
                break;
            case "add":

                $maNhanVien = $input['maNhanVien'] ?? $_POST['lv002'] ?? null;
                $viTri = $input['viTri'] ?? $_POST['lv005'] ?? null;
                $trangThai = $input['trangThai'] ?? $_POST['lv006'] ?? 0;
                $hinhAnh = $input['hinhAnh'] ?? $_POST['lv007'] ?? '';  // Fix: empty string instead of 0
                $loaiChamCong = $input['loaiChamCong'] ?? $_POST['lv004'] ?? null;  // Fix: null for auto-detect
              

                $result = $hr_kb0002->KB_Insert($maNhanVien, $viTri, $trangThai, $hinhAnh, $loaiChamCong);
                

                $vOutput = $result['success']
                    ? [
                        'success'      => true,
                        'message'      => 'Thêm mới thành công',
                        'loaiChamCong' => $result['loaiChamCong'],
                        'maPhieu'      => $result['lv001']
                    ]
                    : [
                        'success' => false,
                        'message' => $result['message'] ?? 'Lỗi khi thêm mới'
                    ];
                
                break;

            case "delete":
                $delArr = $input['maPhieu'] ?? $_POST['lv002'] ?? null;
                if ($delArr) {
                    $arr = is_array($delArr) ? array_map(function ($item) {
                        return "'" . addslashes($item) . "'";
                    }, $delArr) : ["'" . addslashes($delArr) . "'"];
                    $lvarr = implode(',', $arr);
                    $result = $hr_kb0002->KB_Delete($lvarr);
                    $vOutput = $result ? ['success' => true, 'message' => 'Xóa thành công'] : ['success' => false, 'message' => 'Lỗi khi xóa'];
                } else {
                    $vOutput = ['success' => false, 'message' => 'Không có mã để xóa'];
                }
                break;
            case "edit":
                $hr_kb0002->lv002 = $input['maNhanVien'] ?? $_POST['lv002'] ?? null;
                $hr_kb0002->lv003 = $input['ngayDeXuat'] ?? $_POST['lv003'] ?? null;
                $hr_kb0002->lv004 = $input['loaiChamCong'] ?? $_POST['lv004'] ?? null;
                $hr_kb0002->lv005 = $input['viTri'] ?? $_POST['lv005'] ?? null;
                $hr_kb0002->lv006 = $input['trangThai'] ?? $_POST['lv006'] ?? null;
                $hr_kb0002->lv007 = $input['hinhAnh'] ?? $_POST['lv007'] ?? null;
                $result = $hr_kb0002->KB_Update();
                $vOutput = $result ? ['success' => true, 'message' => 'Cập nhật thành công'] : ['success' => false, 'message' => 'Lỗi khi cập nhật'];
                break;
            case "upload":
                if (isset($_FILES['file'])) {
                    $vFileID = $input['maPhieu'] ?? $_POST['maPhieu'] ?? '';
                    if (empty($vFileID)) {
                        $vOutput = ["success" => false, "message" => "Thiếu mã tài liệu"];
                        break;
                    }

                    $vHinhUpdate = file_get_contents($_FILES['file']['tmp_name']);
                    $hr_kb0002->LV_LoadID($vFileID);
                    $vKetQua = $hr_kb0002->LV_LoadStepCheck($vFileID);

                    $hr_kb0002->lv002 = $vFileID;
                    $hr_kb0002->lv006 = $_FILES['file']['name'];
                    $hr_kb0002->lv003 = 'tailieu';
                    $hr_kb0002->lv007 = $hr_kb0002->LV_UserID ?? $input['maNhanVien'] ?? $_POST['maNhanVien'] ?? '';

                    if ($vKetQua == null) {
                        $vresult = $hr_kb0002->LV_InsertAuto('', $vHinhUpdate);
                    } else {
                        $hr_kb0002->lv001 = $vKetQua;
                        $vresult = $hr_kb0002->LV_UpdateAuto($vKetQua, '', $vHinhUpdate);
                    }

                    if ($vresult) {
                        $vOutput = ["success" => true, "message" => "Upload file thành công"];
                    } else {
                        $vOutput = ["success" => false, "message" => "Lỗi khi upload file", "error" => sof_error()];
                    }
                } else {
                    $vOutput = ["success" => false, "message" => "Không có file để upload"];
                }
                break;
            case "viewfile":
                $maPhieu = $input['maPhieu'] ?? $_POST['maPhieu'] ?? null;

                if ($maPhieu) {
                    $files = $hr_kb0002->Mb_GetFileContent($maPhieu);
                    if ($files) {
                        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
                        $host     = $_SERVER['HTTP_HOST'];
                        $script   = dirname($_SERVER['SCRIPT_NAME']) . "/get_file_copy.php";

                        $list = [];
                        foreach ($files as $f) {
                            $list[] = [
                                "id"         => $f['id'],
                                "tenTaiLieu" => $f['tenTaiLieu'],
                                "fileName"   => $f['fileName'],
                                "fileType"   => $f['fileType'],
                                "downloadUrl" => $protocol . $host . $script
                                    . "?maPhieu=" . urlencode($maPhieu)
                                    . "&id=" . urlencode($f['id'])
                            ];
                        }

                        $vOutput = ["success" => true, "files" => $list];
                    } else {
                        $vOutput = ["success" => false, "message" => "Không tìm thấy file cho mã $maTaiLieu"];
                    }
                } else {
                    $vOutput = ["success" => false, "message" => "Thiếu mã tài liệu"];
                }
                break;
            case "DuyetCap1":
                $maPhieu = $input['maPhieu'] ?? $_POST['maPhieu'] ?? null;
                $result = $hr_kb0006->LV_Aproval($maPhieu);
                $vOutput = $result ? ['success' => true, 'message' => 'Cập nhật thành công'] : ['success' => false, 'message' => 'Lỗi khi cập nhật'];
                break;
            case "DuyetCap2":
                $maPhieu = $input['maPhieu'] ?? $_POST['maPhieu'] ?? null;
                $result = $hr_kb0009->LV_Aproval($maPhieu);
                $vOutput = $result ? ['success' => true, 'message' => 'Cập nhật thành công'] : ['success' => false, 'message' => 'Lỗi khi cập nhật'];
                break;
            case "DuyetCap3":
                $maPhieu = $input['maPhieu'] ?? $_POST['maPhieu'] ?? null;
                $result = $hr_kb0012->LV_Aproval($maPhieu);
                $vOutput = $result ? ['success' => true, 'message' => 'Cập nhật thành công'] : ['success' => false, 'message' => 'Lỗi khi cập nhật'];
                break;
            case "KhongDuyetCap1":
                $maPhieu = $input['maPhieu'] ?? $_POST['maPhieu'] ?? null;
                $result = $hr_kb0006->LV_UnAproval($maPhieu);
                $vOutput = $result ? ['success' => true, 'message' => 'Cập nhật thành công'] : ['success' => false, 'message' => 'Lỗi khi cập nhật'];
                break;
            case "KhongDuyetCap2":
                $maPhieu = $input['maPhieu'] ?? $_POST['maPhieu'] ?? null;
                $result = $hr_kb0009->LV_UnAproval($maPhieu);
                $vOutput = $result ? ['success' => true, 'message' => 'Cập nhật thành công'] : ['success' => false, 'message' => 'Lỗi khi cập nhật'];
                break;
            case "KhongDuyetCap3":
                $maPhieu = $input['maPhieu'] ?? $_POST['maPhieu'] ?? null;
                $result = $hr_kb0012->LV_UnAproval($maPhieu);
                $vOutput = $result ? ['success' => true, 'message' => 'Cập nhật thành công'] : ['success' => false, 'message' => 'Lỗi khi cập nhật'];
                break;
            case "TraLaiCap1":
                $maPhieu = $input['maPhieu'] ?? $_POST['maPhieu'] ?? null;
                $result = $hr_kb0007->LV_UnAproval($maPhieu);
                $vOutput = $result ? ['success' => true, 'message' => 'Cập nhật thành công'] : ['success' => false, 'message' => 'Lỗi khi cập nhật'];
                break;
            case "TraLaiCap2":
                $maPhieu = $input['maPhieu'] ?? $_POST['maPhieu'] ?? null;
                $result = $hr_kb0010->LV_UnAproval($maPhieu);
                $vOutput = $result ? ['success' => true, 'message' => 'Cập nhật thành công'] : ['success' => false, 'message' => 'Lỗi khi cập nhật'];
                break;
            case "TraLaiCap3":
                $maPhieu = $input['maPhieu'] ?? $_POST['maPhieu'] ?? null;
                $result = $hr_kb0013->LV_UnAproval($maPhieu);
                $vOutput = $result ? ['success' => true, 'message' => 'Cập nhật thành công'] : ['success' => false, 'message' => 'Lỗi khi cập nhật'];
                break;
        }
        break;
    case "cr_lv0284":
        include_once("./class/cr_lv0284.php");
        include_once("./class/cr_lv0285.php");

        $cr_lv0284 = new cr_lv0284($_SESSION['ERPSOFV2RRight'], $_SESSION['ERPSOFV2RUserID'], 'Cr0284');
        $cr_lv0285 = new cr_lv0285($_SESSION['ERPSOFV2RRight'], $_SESSION['ERPSOFV2RUserID'], 'Cr0285');

        switch ($vfun) {
            case "add":
                $vNow = date('Y-m-d');

                // Sinh mã phiếu tự động
                $cr_lv0284->lv001 = InsertWithCheckFist('cr_lv0202', 'lv001', '/ĐNPC/MP' . substr(getyear($vNow), -2, 2), 4);

                $cr_lv0284->lv002 = 1; // Phiếu chi
                $cr_lv0284->lv003 = (!empty($input['maKeHoach']) ? 'PLAN' : 'MANUAL');
                $cr_lv0284->lv004 = $input['maKeHoach'] ?? null;
                $cr_lv0284->lv005 = $input['tenNhanVien'] ?? null;
                $cr_lv0284->lv006 = $input['phongBan'] ?? null;
                $cr_lv0284->lv007 = $input['noiDung'] ?? null;
                $cr_lv0284->lv008 = $input['maNhanVien'] ?? $_POST['maNhanVien'] ?? null;
                $cr_lv0284->lv009 = $input['ngayDeNghi'] ?? $vNow;
                $cr_lv0284->lv010 = ($input['loaiChi'] == 'CK') ? '1121' : '1111';
                $cr_lv0284->lv011 = 'VND';
                $cr_lv0284->lv012 = 1;
                $cr_lv0284->lv013 = $input['maPBH'] ?? '';

                // Hạn thanh toán = ngày đề nghị + số ngày hoàn ứng
                $cr_lv0284->lv093 = min((int)($input['soNgayHoanUng'] ?? 0), 30);
                $cr_lv0284->lv014 = date('Y-m-d', strtotime($cr_lv0284->lv009 . ' + ' . $cr_lv0284->lv093 . ' days'));
                $cr_lv0284->lv019 = $cr_lv0284->lv014;

                $cr_lv0284->lv015 = $input['chungTuGoc'] ?? '';
                $cr_lv0284->lv016 = 0;
                $cr_lv0284->lv017 = 109; // Mã tác vụ Thu/Chi NetViet
                $cr_lv0284->lv018 = ($input['loaiChi'] == 'CK') ? 1 : 0;

                $cr_lv0284->lv020 = ($cr_lv0284->lv018 == 1) ? ($input['soTaiKhoan'] ?? '') : '';
                $cr_lv0284->lv021 = ($cr_lv0284->lv018 == 1) ? ($input['chuTaiKhoan'] ?? '') : '';
                $cr_lv0284->lv022 = $input['maChiNhanh'] ?? '';

                $cr_lv0284->lv027 = 0;
                $cr_lv0284->lv028 = 0;
                $cr_lv0284->lv029 = 0;
                $cr_lv0284->lv030 = $input['coHoaDon'] ?? 0;

                $cr_lv0284->lv092 = $cr_lv0284->lv009; // Ngày đề xuất hoàn ứng
                $cr_lv0284->lv094 = date('Y-m-d H:i:s'); // Ngày giờ tạo
                $cr_lv0284->lv095 = $input['loaiChi'] ?? 'TM'; // TM hoặc CK
                $cr_lv0284->lv096 = $_SESSION['ERPSOFV2RUserID'];
                $cr_lv0284->lv098 = $input['maKH'] ?? '';
                $cr_lv0284->lv099 = ''; // Quyển số, nếu có
                $cr_lv0284->lv114 = $input['maCongViec'] ?? '';
                $cr_lv0284->lv119 = 'ĐNPC';
                $cr_lv0284->lv120 = $input['phanLoaiChi'] ?? 'PC'; // UNC/PC/PT
                $cr_lv0284->lv121 = $input['maTinhLuong'] ?? '';
                $cr_lv0284->lv122 = date('Y-m-d', strtotime($cr_lv0284->lv014)); // Đến ngày ứng lương
                $cr_lv0284->lv123 = $input['maNhomMuaHang'] ?? '';

                // Kiểm tra chi tiết trước khi insert
                if ($cr_lv0285->GetSumUser($cr_lv0284->lv008) > 0) {
                    echo 'ododododd';
                    $bResultI = $cr_lv0284->LV_Insert();
                    if ($bResultI === true) {
                        $cr_lv0285->LV_InsertTemp($cr_lv0284->lv001, $cr_lv0284->lv008, $cr_lv0284->lv013, $cr_lv0284->lv010);
                        $vStrMessage = "Tạo phiếu ĐNPC thành công";
                        $flagCtrl = 1;
                    } else {
                        $vStrMessage = "Lỗi tạo phiếu: " . sof_error();
                        $flagCtrl = 0;
                    }
                } else {
                    echo 'hehehehe';
                    $vStrMessage = "Bạn phải thêm dòng chi tiết bằng nút Thêm!";
                    $flagCtrl = 0;
                }
                break;
        }

        break;

        //     case "add":
        //         $pm_nc0005->lv001 = $input['maChoDo'] ?? $_POST['lv001'] ?? null;
        //         $pm_nc0005->lv002 = $input['maKhuVuc'] ?? $_POST['lv002'] ?? null;
        //         $pm_nc0005->lv003 = $input['trangThai'] ?? $_POST['lv003'] ?? 'TRONG';
        //         $result = $pm_nc0005->KB_Insert();
        //         $vOutput = $result ? ['success'=>true,'message'=>'Thêm mới thành công'] : ['success'=>false,'message'=>'Lỗi khi thêm mới'];
        //         break;
        //     case "delete":
        //         $delArr = $input['maChoDo'] ?? $_POST['lv001'] ?? null;
        //         if ($delArr) {
        //             $arr = is_array($delArr) ? array_map(function($item){return "'".addslashes($item)."'";}, $delArr) : ["'".addslashes($delArr)."'"];
        //             $success = true;
        //             foreach ($arr as $spotId) {
        //                 $spotId = trim($spotId, "'");
        //                 $result = $pm_nc0005->KB_Delete($spotId);
        //                 if (!$result) $success = false;
        //             }
        //             $vOutput = $success ? ['success'=>true,'message'=>'Xóa thành công'] : ['success'=>false,'message'=>'Lỗi khi xóa (có thể đang có xe gửi tại chỗ đỗ này)'];
        //         } else {
        //             $vOutput = ['success'=>false,'message'=>'Không có mã lv001 để xóa'];
        //         }
        //         break;
        //     case "edit":
        //         $pm_nc0005->lv001 = $input['maChoDo'] ?? $_POST['lv001'] ?? null;
        //         $pm_nc0005->lv002 = $input['maKhuVuc'] ?? $_POST['lv002'] ?? null;
        //         $pm_nc0005->lv003 = $input['trangThai'] ?? $_POST['lv003'] ?? null;
        //         $result = $pm_nc0005->KB_Update();
        //         $vOutput = $result ? ['success'=>true,'message'=>'Cập nhật thành công'] : ['success'=>false,'message'=>'Lỗi khi cập nhật'];
        //         break;

        // 	case "chinhSuaTrangThai":
        // 		$pm_nc0005->lv001 = $input['maChoDo'] ?? $_POST['lv001'] ?? null;
        // 		$pm_nc0005->lv003 = $input['trangThai'] ?? $_POST['lv003'] ?? null;
        // 		$result = $pm_nc0005->KB_ChinhSuaTrangThai($pm_nc0005->lv001,$pm_nc0005->lv003);
        // 		$vOutput = $result ? ['success'=>true,'message'=>'Cập nhật thành công'] : ['success'=>false,'message'=>'Lỗi khi cập nhật'];
        // 		break;
        // 		//chung thêm sync_data
        // 	case "sync_data":
        // 		$result = $pm_nc0005->sync_data();
        // 		$vOutput = $result;
        // 		break;
        // }
        // break;    
}
