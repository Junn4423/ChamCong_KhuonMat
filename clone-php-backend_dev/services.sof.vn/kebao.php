<?php


if (!defined('DB_SERVER_SECOND')) {
    define("DB_SERVER_SECOND", "192.168.1.19");
    define("DB_USER_SECOND", "tienerp");
    define("DB_PWD_SECOND", "tien.erp");
    define("DB_DATABASE_SECOND", "web_sof.com.vn");
}

if (!function_exists('db_connect_second')) {
    function db_connect_second() {
        global $db_link_second;
        if ($db_link_second) return $db_link_second;
        $db_link_second = mysqli_connect(DB_SERVER_SECOND, DB_USER_SECOND, DB_PWD_SECOND);
        if ($db_link_second) mysqli_select_db($db_link_second, DB_DATABASE_SECOND);
        mysqli_query($db_link_second, 'SET NAMES utf8');
        return $db_link_second;
    }
}

if (!function_exists('db_query_second')) {
    function db_query_second($db_query) {
        return mysqli_query(db_connect_second(), $db_query);
    }
}

if (!function_exists('db_fetch_array_second')) {
    function db_fetch_array_second($db_query) {
        if ($db_query == NULL) return NULL;
        return mysqli_fetch_array($db_query);
    }
}

switch ($vtable) {
    case "ping":
        $vOutput = ["status" => "ok", "message" => "pong"];
        break;
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
    case "wb_lv0004":
        include_once("./class/wb_lv0004.php");
        $wb_lv0004 = new wb_lv0004($role, $userId, 'Wb0004');
        switch ($vfun) {
            case "data":
                $objEmp = $wb_lv0004->Mb_LoadAll();
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
        break;
    case 'wb_lv0005':
        switch ($vfun) {
            case "getTopCategories":
                $sql = "SELECT lv001, lv002, lv003, lv004 FROM wb_lv0005 WHERE IFNULL(lv003, '') = ''";
                $result = db_query($sql);
                
                $vOutput = [];
                while ($vrow = db_fetch_array($result)) {
                    $vOutput[] = [
                        "maLoai" => $vrow['lv001'],
                        "tenLoai" => $vrow['lv002'],
                        "maCha" => $vrow['lv003'],
                        "hinhAnh" => $vrow['lv004']
                    ];
                }
                break;
            case "getSubCategories":
                $maCha = $input['maCha'] ?? $_POST['maCha'] ?? '';
                $sql = "SELECT lv001, lv002, lv003, lv004 FROM wb_lv0005 WHERE lv003 = '$maCha'";
                $result = db_query_second($sql);
                $vOutput = [];
                while ($vrow = db_fetch_array_second($result)) {
                    $vOutput[] = [
                        "maLoai" => $vrow['lv001'],
                        "tenLoai" => $vrow['lv002'],
                        "maCha" => $vrow['lv003'],
                        "hinhAnh" => $vrow['lv004']
                    ];
                }
                break;
            case "data":
                $sql = "SELECT lv001 as maLoai, lv002 as tenLoai, lv003 as maCha, lv004 as hinhAnh, lv005, lv006 FROM wb_lv0005";
                $result = db_query_second($sql);
                $vOutput = [];
                while ($vrow = db_fetch_array_second($result)) {
                    $vOutput[] = [
                        "maLoai" => trim($vrow['maLoai']),
                        "tenLoai" => $vrow['tenLoai'],
                        "maCha" => trim($vrow['maCha']),
                        "hinhAnh" => $vrow['hinhAnh'],
                        "lv005" => $vrow['lv005'],
                        "lv006" => $vrow['lv006']
                    ];
                }
                break;
        }
        break;

    case "wb_lv0006":
        switch ($vfun) {
            case "getProducts":
                $maLoai = $input['maLoai'] ?? $_POST['maLoai'] ?? '';
                $sql = "SELECT * FROM wb_lv0006 WHERE lv004 = '$maLoai'";
                $result = db_query_second($sql);
                $vOutput = [];
                while ($vrow = db_fetch_array_second($result)) {
                    $vOutput[] = [
                        "maSanPham" => trim($vrow['lv001']),
                        "maHang" => trim($vrow['lv002']),
                        "loaiSanPham" => trim($vrow['lv003']), 
                        "maLoai" => trim($vrow['lv004']),
                        "tenSanPham" => $vrow['lv005'],
                        "donViTinhChinh" => $vrow['lv006'],
                        "giaBan" => $vrow['lv007'],
                        "tonKho" => $vrow['lv008'],
                        "moTaNgan" => $vrow['lv018'],
                        "nhaCungCap" => $vrow['lv017'],
                        "hinhAnh" => $vrow['lv010'],
                        "hinhAnhLon" => $vrow['lv011']
                    ];
                }
                break;
            case "data":
                $sql = "SELECT * FROM wb_lv0006";
                $result = db_query_second($sql);
                $vOutput = [];
                while ($vrow = db_fetch_array_second($result)) {
                    $vOutput[] = [
                        "maSanPham" => trim($vrow['lv001']),
                        "maHang" => trim($vrow['lv002']),
                        "loaiSanPham" => trim($vrow['lv003']),
                        "maLoai" => trim($vrow['lv004']),
                        "tenSanPham" => $vrow['lv005'],
                        "donViTinhChinh" => $vrow['lv006'],
                        "giaBan" => $vrow['lv007'],
                        "tonKho" => $vrow['lv008'],
                        "moTaNgan" => $vrow['lv018'],
                        "nhaCungCap" => $vrow['lv017'],
                        "hinhAnh" => $vrow['lv010'],
                        "hinhAnhLon" => $vrow['lv011']
                    ];
                }
                break;
        }
        break;

    case "wb_lv0007":
        switch ($vfun) {
            case "getDetails":
                $maSanPham = $input['maSanPham'] ?? $_POST['maSanPham'] ?? '';
                $sql = "SELECT * FROM wb_lv0007 WHERE lv002 = '$maSanPham'";
                $result = db_query_second($sql);
                $vOutput = [];
                while ($vrow = db_fetch_array_second($result)) {
                    $vOutput[] = [
                        "id" => $vrow['lv001'],
                        "maSanPham" => $vrow['lv002'],
                        "tieuDe" => $vrow['lv003'],
                        "noiDungShort" => $vrow['lv004'],
                        "noiDungLong" => $vrow['lv005'],
                        "ngayTao" => $vrow['lv006'],
                        "ngonNgu" => $vrow['lv007'],
                        "lv008" => $vrow['lv008'],
                        "lv009" => $vrow['lv009'],
                        "lv010" => $vrow['lv010'],
                        "lv011" => $vrow['lv011'],
                        "lv012" => $vrow['lv012']
                    ];
                }
                break;
        }
        break;
}

