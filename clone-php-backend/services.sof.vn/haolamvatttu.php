<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

switch ($vtable) {
    case "cr_lv0150":
        include_once("./class/cr_lv0150.php");
        include_once("./class/cr_lv0151.php");
        include_once("./class/cr_lv0153.php");
        include_once("./class/cr_lv0154.php");
        include_once("./class/cr_lv0383.php");


        $cr_lv0150 = new cr_lv0150($_SESSION['ERPSOFV2RRight'], $_SESSION['ERPSOFV2RUserID'], 'Cr0150');
        $cr_lv0151 = new cr_lv0151($_SESSION['ERPSOFV2RRight'], $_SESSION['ERPSOFV2RUserID'], 'Cr0151');
        $cr_lv0153 = new cr_lv0153($_SESSION['ERPSOFV2RRight'], $_SESSION['ERPSOFV2RUserID'], 'Cr0153');
        $cr_lv0154 = new cr_lv0154($_SESSION['ERPSOFV2RRight'], $_SESSION['ERPSOFV2RUserID'], 'Cr0154');
        $cr_lv0383 = new cr_lv0383($_SESSION['ERPSOFV2RRight'], $_SESSION['ERPSOFV2RUserID'], 'Cr0383');


        $cr_lv0150->lang = 'VN';
        switch ($vfun) {
            case "layCongviec":
                $objEmp = $cr_lv0150->MB_LayMACV();
                $vOutput = [];
                while ($vrow = db_fetch_array($objEmp)) {
                    $vOutput[] = [
                        "maCongviec" => $vrow['lv001'],
                        "tenCongviec" => $vrow['lv002'],
                    ];
                }
                break;
            case "layMakehoach":
                $vNow = date('Y-m-d');
                $cr_lv0150->lv001 = InsertWithCheckFist('cr_lv0150', 'lv001', '/ĐNVT/MP' . substr(getyear($vNow), -2, 2), 4);
                echo json_encode([
                    'success' => true,
                    'lv001' => $cr_lv0150->lv001
                ]);
                break;
            case "layDanhsachkho":
                $objEmp = $cr_lv0150->MB_LayDSKho();
                $vOutput = [];
                while ($vrow = db_fetch_array($objEmp)) {
                    $vOutput[] = [
                        "maKho" => $vrow['lv001'],
                        "tenKho" => $vrow['lv003'],
                    ];
                }
                break;
            case "layDVT":
                $objEmp = $cr_lv0150->MB_LayDVT();
                $vOutput = [];
                while ($vrow = db_fetch_array($objEmp)) {
                    $vOutput[] = [
                        "maDVT" => $vrow['lv001'],
                        "tenDVT" => $vrow['lv002'],
                    ];
                }
                break;
            case "insertPhieu":
                $cr_lv0150->lv001 = $input['maPhieu'] ?? $_POST['maPhieu'] ?? null;
                $cr_lv0150->lv002 = $input['maKho'] ?? $_POST['maKho'] ?? null;
                $cr_lv0150->lv003 = $input['maNhanVien'] ?? $_POST['maNhanVien'] ?? null;
                $cr_lv0150->lv004 = $input['ghiChu'] ?? $_POST['ghiChu'] ?? null;
                $cr_lv0150->lv009 = $input['ngayDeNghi'] ?? $_POST['ngayDeNghi'] ?? null;
                $cr_lv0150->lv029 = $input['lv029'] ?? $_POST['lv029'] ?? null;
                $cr_lv0150->lv114 = $input['maCongViec'] ?? $_POST['maCongViec'] ?? null;
                $cr_lv0150->lv117 = $input['maNhanVien'] ?? $_POST['maNhanVien'] ?? null;
                $cr_lv0150->lv118 = $input['ngayTao'] ?? $_POST['ngayTao'] ?? null;
                $bResultI = $cr_lv0150->LV_InsertTemp();
                if ($bResultI == true) {
                    $cr_lv0151->LV_InsertTemp($cr_lv0150->lv001,  $cr_lv0150->lv003, $cr_lv0150->lv099);
                    $vStrMessage = 'TẠO ĐỀ NGHỊ CẤP VẬT TƯ!';
                    $cr_lv0150->lv001 = "";
                } else {
                    $vStrMessage = sof_error();
                }
                break;
            case "updatePhieu":
                $cr_lv0150->lv001 = $input['maPhieu'] ?? $_POST['maPhieu'] ?? null;
                $cr_lv0150->lv002 = $input['maKho'] ?? $_POST['maKho'] ?? null;
                $cr_lv0150->lv004 = $input['ghiChu'] ?? $input['mucDich'] ?? $_POST['ghiChu'] ?? $_POST['mucDich'] ?? null; // Nhận cả ghiChu và mucDich
                $cr_lv0150->lv009 = $input['ngayDeNghi'] ?? $_POST['ngayDeNghi'] ?? null;
                $cr_lv0150->lv114 = $input['maCongViec'] ?? $_POST['maCongViec'] ?? null;
                $objEmp = $cr_lv0150->LV_Update();
                if ($objEmp) {
                    echo json_encode([
                        'success' => true,
                    ]);
                }
                break;
            case "layDanhSachPhieu":
                $objEmp = $cr_lv0150->MB_LayDSPhieu();
                $vOutput = [];
                while ($vrow = db_fetch_array($objEmp)) {
                    $tongTien = $cr_lv0150->LV_GetBLMoney($vrow['lv001']);
                    $vOutput[] = [
                        "maPhieu" => $vrow['lv001'],
                        "maKho" => $vrow['lv002'],
                        "maNhanVien" => $vrow['lv003'],
                        "mucDich" => $vrow['lv004'],
                        "lv005" => $vrow['lv005'],
                        "lv006" => $vrow['lv006'],
                        "qlyduyet" => $vrow['lv007'],
                        "lv008" => $vrow['lv008'],
                        "ngayDeNghi" => $vrow['lv009'],
                        "lv010" => $vrow['lv010'],
                        "lv027" => $vrow['lv027'],
                        "lv028" => $vrow['lv028'],
                        "lv029" => $vrow['lv029'],
                        "lv030" => $vrow['lv030'],
                        "maCongViec" => $vrow['lv114'],
                        "maNhanVien" => $vrow['lv117'],
                        "lv829" => $vrow['lv829'],
                        "nlv862" => $vrow['lv862'],
                        "ngayTao" => $vrow['lv118'],
                        "tongTien" => $tongTien
                    ];
                }
                break;
            case "deXuatDuyet":
                $cr_lv0150->LV_UserID = $input['maNhanVien'] ?? $_POST['maNhanVien'] ?? null;
                $cr_lv0150->lv001 = $input['maPhieu'] ?? $_POST['maPhieu'] ?? null;
                $objEmp = $cr_lv0150->LV_Aproval($cr_lv0150->lv001);
                if ($objEmp) {
                    echo json_encode([
                        'success' => true
                    ]);
                }
                break;
            case "quanLyDuyet":
                $cr_lv0150->LV_UserID = $input['maNhanVien'] ?? $_POST['maNhanVien'] ?? null;
                $cr_lv0150->lv001 = $input['maPhieu'] ?? $_POST['maPhieu'] ?? null;

                $cr_lv0153->LV_UserID = $cr_lv0150->LV_UserID;

                $objEmp = $cr_lv0153->LV_Aproval($cr_lv0150->lv001);
                if ($objEmp) {
                    echo json_encode([
                        'success' => true
                    ]);
                }
                break;
            case "teamLeaderDuyet":
                $cr_lv0150->LV_UserID = $input['maNhanVien'] ?? $_POST['maNhanVien'] ?? null;
                $cr_lv0150->lv001 = $input['maPhieu'] ?? $_POST['maPhieu'] ?? null;
                $cr_lv0383->LV_UserID = $cr_lv0150->LV_UserID;

                $objEmp = $cr_lv0383->LV_Aproval($cr_lv0150->lv001);
                if ($objEmp) {
                    echo json_encode([
                        'success' => true
                    ]);
                }
                break;
            case "bgdDuyet":
                $cr_lv0150->LV_UserID = $input['maNhanVien'] ?? $_POST['maNhanVien'] ?? null;
                $cr_lv0150->lv001 = $input['maPhieu'] ?? $_POST['maPhieu'] ?? null;
                $cr_lv0154->LV_UserID = $cr_lv0150->LV_UserID;
                $objEmp = $cr_lv0154->LV_Aproval($cr_lv0150->lv001);
                if ($objEmp) {
                    echo json_encode([
                        'success' => true
                    ]);
                }
                break;
            case "traLai":
                $cr_lv0150->lv001 = $input['maPhieu'] ?? $_POST['maPhieu'] ?? null;
                $objEmp = $cr_lv0153->MB_LV_UnAproval($cr_lv0150->lv001);
                if ($objEmp) {
                    echo json_encode([
                        'success' => true
                    ]);
                }
                break;
            case "layChiTietPhieu":
                $cr_lv0150->lv001 = $input['maPhieu'] ?? $_POST['maPhieu'] ?? null;
                $objEmp = $cr_lv0151->MB_ChiTietPhieu($cr_lv0150->lv001);
                while ($vrow = db_fetch_array($objEmp)) {
                    $vOutput[] = [
                        "maTuDong" => $vrow['lv001'],
                        "maBom" => $vrow['lv003'],
                        "soLuongDuyet" => $vrow['lv004'],
                        "soLuongDeXuat" => $vrow['lv006'],
                        "dvt" => $vrow['lv005'],
                        "donGia" => $vrow['lv008'],
                        "thue" => $vrow['lv010'],
                        "ghiChu" => $vrow['lv015'],
                    ];
                }
                break;
            case "suaVattu":
                $cr_lv0151->lv001 = $input['maTuDong'] ?? $_POST['maTuDong'] ?? null;
                $cr_lv0151->lv002 = $input['maPhieu'] ?? $_POST['maPhieu'] ?? null;
                $cr_lv0151->lv003 = $input['maBom'] ?? $_POST['maBom'] ?? null;
                $cr_lv0151->lv004 = $input['soLuongDuyet'] ?? $_POST['soLuongDuyet'] ?? null;
                $cr_lv0151->lv006 = $input['soLuongDeXuat'] ?? $_POST['soLuongDeXuat'] ?? null;
                $cr_lv0151->lv005 = $input['dvt'] ?? $_POST['dvt'] ?? null;
                $cr_lv0151->lv008 = $input['donGia'] ?? $_POST['donGia'] ?? null;
                $cr_lv0151->lv010 = $input['thue'] ?? $_POST['thue'] ?? null;
                $cr_lv0151->lv015 = $input['ghiChu'] ?? $_POST['ghiChu'] ?? null;
                $objEmp = $cr_lv0151->LV_Update();
                if ($objEmp) {
                    echo json_encode([
                        'success' => true
                    ]);
                }
                break;
        }
        break;
    case "cr_lv0149":
        include_once("./class/cr_lv0149.php");
        $cr_lv0149 = new cr_lv0149($_SESSION['ERPSOFV2RRight'], $_SESSION['ERPSOFV2RUserID'], 'Cr0149');
        switch ($vfun) {
            case "layMaBOM":
                $objEmp = $cr_lv0149->MB_LayMaBOM();
                $vOutput = [];
                while ($vrow = db_fetch_array($objEmp)) {
                    $vOutput[] = [
                        "maBom" => $vrow['lv001'],
                        "ten" => $vrow['lv002'],
                    ];
                }
                break;
            case "insert":
                $cr_lv0149->lv002 = $input['maNhanVien'] ?? $_POST['maNhanVien'] ?? null;
                $cr_lv0149->lv003 = $input['maBom'] ?? $_POST['maBom'] ?? null;
                $cr_lv0149->lv039 = $input['orderNo'] ?? $_POST['orderNo'] ?? null;
                $cr_lv0149->lv004 = $input['soLuong'] ?? $_POST['soLuong'] ?? null;
                $cr_lv0149->lv006 = $input['soLuong'] ?? $_POST['soLuong'] ?? null;
                $cr_lv0149->lv007 = $input['maNhanVien'] ?? $_POST['maNhanVien'] ?? null;
                $cr_lv0149->lv008 = $input['donGia'] ?? $_POST['donGia'] ?? null;
                $cr_lv0149->lv010 = $input['thue'] ?? $_POST['thue'] ?? null;
                $cr_lv0149->lv015 = $input['ghiChu'] ?? $_POST['ghiChu'] ?? null;

                $objEmp = $cr_lv0149->LV_Insert();

                if ($objEmp) {
                    $response = [
                        'success' => true,
                        'message' => 'Thêm vật tư thành công',
                        'id' => $cr_lv0149->lastId,
                        'maTuDong' =>  $cr_lv0149->lastId,
                    ];
                    echo json_encode($response);
                } else {
                    $response = [
                        'success' => false,
                        'message' => 'Không thể thêm vật tư',
                        'error' => 'Insert operation failed'
                    ];
                    echo json_encode($response);
                }
                break;
            case "delete":
                $cr_lv0149->lv001 = $input['maVatTu'] ?? $_POST['maVatTu'] ?? null;
                $objEmp = $cr_lv0149->LV_Delete($cr_lv0149->lv001);
                break;
            case "load":
                $objEmp = $cr_lv0149->MB_LoadVatTu();
                while ($vrow = db_fetch_array($objEmp)) {
                    $vOutput[] = [
                        "maTuDong" => $vrow['lv001'],
                        "maBom" => $vrow['lv003'],
                        "soLuongDuyet" => $vrow['lv004'],
                        "soLuongDeXuat" => $vrow['lv006'],
                        "dvt" => $vrow['lv005'],
                        "donGia" => $vrow['lv008'],
                        "thue" => $vrow['lv010'],
                        "ghiChu" => $vrow['lv015'],
                    ];
                }
                break;
        }
        break;
    case  "cr_lv0313":
        include_once("./class/cr_lv0313.php");
        $cr_lv0313 = new cr_lv0313($_SESSION['ERPSOFV2RRight'], $_SESSION['ERPSOFV2RUserID'], 'Cr0313');
        switch ($vfun) {
            case "layLichSuDuyet":
                $cr_lv0313->LV_UserID = $input['maNhanVien'] ?? $_POST['maNhanVien'] ?? null;

                $cr_lv0313->trangthai = $input['trangthai'] ?? $_POST['trangthai'] ?? null;

                $objEmp = $cr_lv0313->MB_LichSuDuyet();

                $vOutput = [];
                while ($vrow = db_fetch_array($objEmp)) {
                    $vOutput[] = [
                        "ma" => $vrow['lv001'],
                        "maPhieu" => $vrow['lv002'],
                        "trangThai" => $vrow['lv003'],
                        "nguoiDuyet" => $vrow['lv004'],
                        "ngayDuyet" => $vrow['lv005'],
                        "loaiDuyet" => $vrow['lv006']
                    ];
                }
                break;
        }
        break;
    case "cr_lv0384":
        include_once("./class/cr_lv0384.php");
        $cr_lv0384 = new cr_lv0384($_SESSION['ERPSOFV2RRight'], $_SESSION['ERPSOFV2RUserID'], 'Cr0384');
        switch ($vfun) {
            case "layLoaiTaiLieu":
                $objEmp = $cr_lv0384->MB_LoaiTaiLieu();
                $vOutput = [];
                while ($vrow = db_fetch_array($objEmp)) {
                    $vOutput[] = [
                        "maTaiLieu" => $vrow['lv001'],
                        "tenTaiLieu" => $vrow['lv002'],
                    ];
                }
                break;
            case "layLoaiTapTin":
                $objEmp = $cr_lv0384->MB_LoaiTapTin();
                $vOutput = [];
                while ($vrow = db_fetch_array($objEmp)) {
                    $vOutput[] = [
                        "maTapTin" => $vrow['lv001'],
                        "tenTapTin" => $vrow['lv002'],
                    ];
                }
                break;
            case "layDanhSachTaiLieu":
                $cr_lv0384->lv002 = $input['maPhieu'] ?? $_POST['maPhieu'] ?? null;
                $objDocs = $cr_lv0384->MB_LoadTaiLieu($cr_lv0384->lv002);
                $vOutput = [];
                while ($vrow = db_fetch_array($objDocs)) {
                    $vOutput[] = [
                        "maTaiLieu" => $vrow['lv001'],
                        "maPhieu" => $vrow['lv002'],
                        "loaiTaiLieu" => $vrow['lv003'],
                        "tenTaiLieu" => $vrow['lv004'],
                        "tapTin" => $vrow['lv005'],
                        "maTapTin" => $vrow['lv006'],
                        "phanLoai" => $vrow['lv007'],
                        "thamChieu" => $vrow['lv008'],
                        "nguoiTao" => $vrow['lv009'],
                        "ngayGioTao" => $vrow['lv010'],
                    ];
                }
                break;
                break;
            case "insertTaiLieu":
                $cr_lv0384->lv002 = $input['maPhieu'] ?? $_POST['maPhieu'] ?? null;
                $cr_lv0384->lv003 = $input['maTaiLieu'] ?? $_POST['maTaiLieu'] ?? null;
                $cr_lv0384->lv004 = $input['tentaiLieu'] ?? $_POST['tentaiLieu'] ?? null;
                $cr_lv0384->lv006 = $input['maTapTin'] ?? $_POST['maTapTin'] ?? null;
                $cr_lv0384->lv008 = $input['danhsachNhanVien'] ?? $_POST['danhsachNhanVien'] ?? null;
                $cr_lv0384->lv009 = $input['maNhanVien'] ?? $_POST['maNhanVien'] ?? null;
                $cr_lv0384->lv010 = $input['ngayTao'] ?? $_POST['ngayTao'] ?? null;

                $objEmp = $cr_lv0384->LV_Insert();
                if ($objEmp) {
                    echo json_encode([
                        'success' => true,
                        'id' => $cr_lv0384->lastId
                    ]);
                } else {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Insert thất bại'
                    ]);
                }
                break;

            case "upload":
                if (isset($_FILES['file'])) {
                    $vFileID = $input['maTaiLieu'] ?? $_POST['maTaiLieu'] ?? '';
                    if (empty($vFileID)) {
                        $vOutput = ["success" => false, "message" => "Thiếu mã tài liệu"];
                        break;
                    }

                    $vHinhUpdate = file_get_contents($_FILES['file']['tmp_name']);
                    $cr_lv0384->LV_LoadID($vFileID);
                    $vKetQua = $cr_lv0384->LV_LoadStepCheck($vFileID);

                    $cr_lv0384->lv002 = $vFileID;
                    $cr_lv0384->lv006 = $_FILES['file']['name'];
                    $cr_lv0384->lv003 = 'tailieu';
                    $cr_lv0384->lv007 = $cr_lv0384->LV_UserID ?? $input['maNhanVien'] ?? $_POST['maNhanVien'] ?? '';

                    if ($vKetQua == null) {
                        $vresult = $cr_lv0384->LV_InsertAuto('', $vHinhUpdate);
                    } else {
                        $cr_lv0384->lv001 = $vKetQua;
                        $vresult = $cr_lv0384->LV_UpdateAuto($vKetQua, '', $vHinhUpdate);
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
                $maTaiLieu = $input['maTaiLieu'] ?? $_POST['maTaiLieu'] ?? null;

                if ($maTaiLieu) {
                    $files = $cr_lv0384->Mb_GetFileContent($maTaiLieu);
                    if ($files) {
                        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
                        $host     = $_SERVER['HTTP_HOST'];
                        // ⚡ Trỏ sang get_file.php thay vì index.php
                        $script   = dirname($_SERVER['SCRIPT_NAME']) . "/get_file.php";

                        $list = [];
                        foreach ($files as $f) {
                            $list[] = [
                                "id"         => $f['id'],
                                "tenTaiLieu" => $f['tenTaiLieu'],
                                "fileName"   => $f['fileName'],
                                "fileType"   => $f['fileType'],
                                "downloadUrl" => $protocol . $host . $script
                                    . "?maTaiLieu=" . urlencode($maTaiLieu)
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
        }
    case "hr_lv0020":
        include_once("./class/hr_lv0020.php");
        $hr_lv0020 = new hr_lv0020($_SESSION['ERPSOFV2RRight'], $_SESSION['ERPSOFV2RUserID'], 'Hr0020');
        switch ($vfun) {
            case "LayNhanVien":
                $objEmp = $hr_lv0020->MB_LayNhanVien();
                $vOutput = [];
                while ($vrow = db_fetch_array($objEmp)) {
                    $vOutput[] = [
                        "maNhanVien" => $vrow['lv001'],
                        "tenNhanVien" => $vrow['lv002'],
                        "phongBan" => $vrow['lv029']
                    ];
                }
                break;
        }
        break;
    case "da_lh0012":
        include_once("./class/da_lh0012.php");
        $da_lh0012 = new da_lh0012($_SESSION['ERPSOFV2RRight'], $_SESSION['ERPSOFV2RUserID'], 'Da0012');
        switch ($vfun) {
            case "data":
                $objEmp = $da_lh0012->loadcauhoi_traloi();
                $vOutput = [];
                while ($vrow = db_fetch_array($objEmp)) {
                    $vOutput[] = [
                        "id" => $vrow['lv001'],
                        "question" => $vrow['lv002'],
                        "tags" => $vrow['lv003'],
                        "created_at" => $vrow['lv004'],
                        "answer" => $vrow['cautraloi'],
                    ];
                }
                break;
        }
        break;
}
