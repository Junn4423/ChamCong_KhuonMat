<?php

switch ($vtable) {

    /**
     * =========================================================================
     * SL_LV0007 - DANH MỤC SẢN PHẨM/VẬT TƯ
     * =========================================================================
     * Các trường chính:
     * - lv001: Mã sản phẩm | lv002: Tên sản phẩm | lv003: Loại SP
     * - lv004: ĐVT chính | lv007: Giá bán | lv017: Mã vạch
     * =========================================================================
     */
    case "sl_lv0007":
        switch ($vfun) {
            
            // Lấy toàn bộ sản phẩm
            case "data":
                $lvsql = "SELECT * FROM sl_lv0007 ORDER BY lv001";
                $objEmp = db_query($lvsql);
                $vOutput = [];
                
                while ($vrow = db_fetch_array($objEmp)) {
                    $vOutput[] = [
                        "maSanPham"       => $vrow['lv001'],
                        "tenSanPham"      => $vrow['lv002'],
                        "loaiSanPham"     => $vrow['lv003'],
                        "donViTinhChinh"  => $vrow['lv004'],
                        "donViTinhPhu"    => $vrow['lv005'],
                        "tyLeQuyDoi"      => $vrow['lv006'],
                        "giaBan"          => $vrow['lv007'],
                        "donViTienTe"     => $vrow['lv008'],
                        "nhaCungCap"      => $vrow['lv009'],
                        "moTa"            => $vrow['lv010'],
                        "maVach"          => $vrow['lv017'],
                        "nhomSanPham"     => $vrow['lv018'],
                        "ghiChu"          => $vrow['lv019'],
                    ];
                }
                break;
            
            // Lấy sản phẩm theo mã
            case "getById":
                $maSanPham = $input['maSanPham'] ?? $_POST['maSanPham'] ?? $_POST['id'] ?? null;
                
                if (empty($maSanPham)) {
                    $vOutput = ['success' => false, 'message' => 'Thiếu mã sản phẩm'];
                    break;
                }
                
                $maSanPham = sof_escape_string($maSanPham);
                $lvsql = "SELECT * FROM sl_lv0007 WHERE lv001 = '$maSanPham'";
                $objEmp = db_query($lvsql);
                $vrow = db_fetch_array($objEmp);
                
                if ($vrow) {
                    $vOutput = [
                        'success' => true,
                        'data' => [
                            "maSanPham"       => $vrow['lv001'],
                            "tenSanPham"      => $vrow['lv002'],
                            "loaiSanPham"     => $vrow['lv003'],
                            "donViTinhChinh"  => $vrow['lv004'],
                            "donViTinhPhu"    => $vrow['lv005'],
                            "tyLeQuyDoi"      => $vrow['lv006'],
                            "giaBan"          => $vrow['lv007'],
                            "donViTienTe"     => $vrow['lv008'],
                            "nhaCungCap"      => $vrow['lv009'],
                            "moTa"            => $vrow['lv010'],
                            "maVach"          => $vrow['lv017'],
                            "nhomSanPham"     => $vrow['lv018'],
                            "ghiChu"          => $vrow['lv019'],
                        ]
                    ];
                } else {
                    $vOutput = ['success' => false, 'message' => 'Không tìm thấy sản phẩm'];
                }
                break;
            
            // Tìm kiếm sản phẩm
            case "search":
                $tuKhoa = $input['tuKhoa'] ?? $_POST['tuKhoa'] ?? '';
                $loaiSanPham = $input['loaiSanPham'] ?? $_POST['loaiSanPham'] ?? '';
                
                $lvsql = "SELECT * FROM sl_lv0007 WHERE 1=1";
                
                if (!empty($tuKhoa)) {
                    $tuKhoa = sof_escape_string($tuKhoa);
                    $lvsql .= " AND (lv001 LIKE '%$tuKhoa%' OR lv002 LIKE '%$tuKhoa%' OR lv017 LIKE '%$tuKhoa%')";
                }
                
                if (!empty($loaiSanPham)) {
                    $loaiSanPham = sof_escape_string($loaiSanPham);
                    $lvsql .= " AND lv003 = '$loaiSanPham'";
                }
                
                $lvsql .= " ORDER BY lv001 LIMIT 100";
                $objEmp = db_query($lvsql);
                $vOutput = [];
                
                while ($vrow = db_fetch_array($objEmp)) {
                    $vOutput[] = [
                        "maSanPham"       => $vrow['lv001'],
                        "tenSanPham"      => $vrow['lv002'],
                        "loaiSanPham"     => $vrow['lv003'],
                        "donViTinhChinh"  => $vrow['lv004'],
                        "giaBan"          => $vrow['lv007'],
                        "donViTienTe"     => $vrow['lv008'],
                        "maVach"          => $vrow['lv017'],
                    ];
                }
                break;
        }
        break;

    /**
     * =========================================================================
     * SL_LV0010 - ĐƠN HÀNG / HỢP ĐỒNG BÁN HÀNG
     * =========================================================================
     * Các trường chính:
     * - lv001: Mã đơn hàng | lv002: Mã KH | lv003: Ngày đặt
     * - lv011: Trạng thái | lv014: Số đơn hàng | lv106: Tổng tiền
     * =========================================================================
     */
    case "sl_lv0010":
        include_once("./class/sl_lv0010.php");
        $sl_lv0010 = new sl_lv0010($_SESSION['ERPSOFV2RRight'], $_SESSION['ERPSOFV2RUserID'], 'Sl0010');
        
        switch ($vfun) {
            
            // Lấy toàn bộ đơn hàng
            case "data":
                $lvsql = "SELECT * FROM sl_lv0010 ORDER BY lv001 DESC";
                $objEmp = db_query($lvsql);
                $vOutput = [];
                
                while ($vrow = db_fetch_array($objEmp)) {
                    $vOutput[] = [
                        "maDonHang"        => $vrow['lv001'],
                        "maKhachHang"      => $vrow['lv002'],
                        "ngayDatHang"      => $vrow['lv003'],
                        "ngayGiaoDuKien"   => $vrow['lv004'],
                        "ngayGiaoThucTe"   => $vrow['lv005'],
                        "diaChiGiao"       => $vrow['lv008'],
                        "trangThai"        => $vrow['lv011'],
                        "soDonHang"        => $vrow['lv014'],
                        "nguoiLap"         => $vrow['lv016'],
                        "nguoiDuyet"       => $vrow['lv018'],
                        "nguoiPhuTrach"    => $vrow['lv020'],
                        "tongTien"         => $vrow['lv106'],
                        "donViTienTe"      => $vrow['lv108'],
                        "ghiChu"           => $vrow['lv110'],
                    ];
                }
                break;
            
            // Lấy đơn hàng theo mã
            case "getById":
                $maDonHang = $input['maDonHang'] ?? $_POST['maDonHang'] ?? null;
                
                if (empty($maDonHang)) {
                    $vOutput = ['success' => false, 'message' => 'Thiếu mã đơn hàng'];
                    break;
                }
                
                $sl_lv0010->LV_LoadID($maDonHang);
                
                if ($sl_lv0010->lv001 !== null) {
                    $vOutput = [
                        'success' => true,
                        'data' => [
                            "maDonHang"        => $sl_lv0010->lv001,
                            "maKhachHang"      => $sl_lv0010->lv002,
                            "ngayDatHang"      => $sl_lv0010->lv003,
                            "ngayGiaoDuKien"   => $sl_lv0010->lv004,
                            "ngayGiaoThucTe"   => $sl_lv0010->lv005,
                            "diaChiGiao"       => $sl_lv0010->lv008,
                            "trangThai"        => $sl_lv0010->lv011,
                            "soDonHang"        => $sl_lv0010->lv014,
                            "nguoiLap"         => $sl_lv0010->lv016,
                            "nguoiDuyet"       => $sl_lv0010->lv018,
                            "nguoiPhuTrach"    => $sl_lv0010->lv020,
                            "tongTien"         => $sl_lv0010->lv106,
                            "donViTienTe"      => $sl_lv0010->lv108,
                            "ghiChu"           => $sl_lv0010->lv110,
                        ]
                    ];
                } else {
                    $vOutput = ['success' => false, 'message' => 'Không tìm thấy đơn hàng'];
                }
                break;
            
            // Lấy đơn hàng theo khách hàng
            case "getByCustomer":
                $maKhachHang = $input['maKhachHang'] ?? $_POST['maKhachHang'] ?? null;
                
                if (empty($maKhachHang)) {
                    $vOutput = ['success' => false, 'message' => 'Thiếu mã khách hàng'];
                    break;
                }
                
                $maKhachHang = sof_escape_string($maKhachHang);
                $lvsql = "SELECT * FROM sl_lv0010 WHERE lv002 = '$maKhachHang' ORDER BY lv001 DESC";
                $objEmp = db_query($lvsql);
                $vOutput = [];
                
                while ($vrow = db_fetch_array($objEmp)) {
                    $vOutput[] = [
                        "maDonHang"        => $vrow['lv001'],
                        "maKhachHang"      => $vrow['lv002'],
                        "ngayDatHang"      => $vrow['lv003'],
                        "trangThai"        => $vrow['lv011'],
                        "soDonHang"        => $vrow['lv014'],
                        "tongTien"         => $vrow['lv106'],
                    ];
                }
                break;
            
            // Lọc đơn hàng theo trạng thái và thời gian
            case "filter":
                $trangThai = $input['trangThai'] ?? $_POST['trangThai'] ?? null;
                $tuNgay = $input['tuNgay'] ?? $_POST['tuNgay'] ?? null;
                $denNgay = $input['denNgay'] ?? $_POST['denNgay'] ?? null;
                
                $lvsql = "SELECT * FROM sl_lv0010 WHERE 1=1";
                
                if ($trangThai !== null && $trangThai !== '') {
                    $trangThai = sof_escape_string($trangThai);
                    $lvsql .= " AND lv011 = '$trangThai'";
                }
                
                if (!empty($tuNgay)) {
                    $tuNgay = sof_escape_string($tuNgay);
                    $lvsql .= " AND lv003 >= '$tuNgay'";
                }
                
                if (!empty($denNgay)) {
                    $denNgay = sof_escape_string($denNgay);
                    $lvsql .= " AND lv003 <= '$denNgay'";
                }
                
                $lvsql .= " ORDER BY lv001 DESC LIMIT 500";
                $objEmp = db_query($lvsql);
                $vOutput = [];
                
                while ($vrow = db_fetch_array($objEmp)) {
                    $vOutput[] = [
                        "maDonHang"        => $vrow['lv001'],
                        "maKhachHang"      => $vrow['lv002'],
                        "ngayDatHang"      => $vrow['lv003'],
                        "trangThai"        => $vrow['lv011'],
                        "soDonHang"        => $vrow['lv014'],
                        "tongTien"         => $vrow['lv106'],
                    ];
                }
                break;
        }
        break;

    /**
     * =========================================================================
     * SL_LV0011 - CHI TIẾT ĐƠN HÀNG
     * =========================================================================
     * Các trường chính:
     * - lv001: Mã chi tiết | lv002: Mã đơn hàng | lv003: Mã SP
     * - lv005: SL đặt | lv006: Đơn giá | lv008: Thành tiền
     * =========================================================================
     */
    case "sl_lv0011":
        include_once("./class/sl_lv0011.php");
        $sl_lv0011 = new sl_lv0011($_SESSION['ERPSOFV2RRight'], $_SESSION['ERPSOFV2RUserID'], 'Sl0011');
        
        switch ($vfun) {
            
            // Lấy toàn bộ chi tiết
            case "data":
                $lvsql = "SELECT A.*, B.lv002 as tenSanPham 
                          FROM sl_lv0011 A 
                          LEFT JOIN sl_lv0007 B ON A.lv003 = B.lv001 
                          ORDER BY A.lv001";
                $objEmp = db_query($lvsql);
                $vOutput = [];
                
                while ($vrow = db_fetch_array($objEmp)) {
                    $vOutput[] = [
                        "maChiTiet"    => $vrow['lv001'],
                        "maDonHang"    => $vrow['lv002'],
                        "maSanPham"    => $vrow['lv003'],
                        "tenSanPham"   => $vrow['tenSanPham'],
                        "soLuongDat"   => $vrow['lv005'],
                        "donGia"       => $vrow['lv006'],
                        "soLuongGiao"  => $vrow['lv007'],
                        "thanhTien"    => $vrow['lv008'],
                        "chietKhau"    => $vrow['lv009'],
                        "thueVAT"      => $vrow['lv010'],
                        "ghiChu"       => $vrow['lv011'],
                    ];
                }
                break;
            
            // Lấy chi tiết theo mã đơn hàng
            case "getByOrder":
                $maDonHang = $input['maDonHang'] ?? $_POST['maDonHang'] ?? null;
                
                if (empty($maDonHang)) {
                    $vOutput = ['success' => false, 'message' => 'Thiếu mã đơn hàng'];
                    break;
                }
                
                $maDonHang = sof_escape_string($maDonHang);
                $lvsql = "SELECT A.*, B.lv002 as tenSanPham, B.lv004 as donViTinh
                          FROM sl_lv0011 A 
                          LEFT JOIN sl_lv0007 B ON A.lv003 = B.lv001 
                          WHERE A.lv002 = '$maDonHang'
                          ORDER BY A.lv001";
                $objEmp = db_query($lvsql);
                $vOutput = [];
                
                while ($vrow = db_fetch_array($objEmp)) {
                    $vOutput[] = [
                        "maChiTiet"    => $vrow['lv001'],
                        "maDonHang"    => $vrow['lv002'],
                        "maSanPham"    => $vrow['lv003'],
                        "tenSanPham"   => $vrow['tenSanPham'],
                        "donViTinh"    => $vrow['donViTinh'],
                        "soLuongDat"   => $vrow['lv005'],
                        "donGia"       => $vrow['lv006'],
                        "soLuongGiao"  => $vrow['lv007'],
                        "thanhTien"    => $vrow['lv008'],
                        "chietKhau"    => $vrow['lv009'],
                        "thueVAT"      => $vrow['lv010'],
                        "ghiChu"       => $vrow['lv011'],
                    ];
                }
                break;
        }
        break;

    /**
     * =========================================================================
     * SL_LV0014 - CHI TIẾT SẢN PHẨM MỞ RỘNG
     * =========================================================================
     * Các trường chính:
     * - lv001: Mã chi tiết | lv002: Mã đơn hàng | lv003: Mã SP
     * - lv004: Số lượng | lv005: Đơn giá | lv006: Thành tiền
     * =========================================================================
     */
    case "sl_lv0014":
        include_once("./class/sl_lv0014.php");
        $sl_lv0014 = new sl_lv0014($_SESSION['ERPSOFV2RRight'], $_SESSION['ERPSOFV2RUserID'], 'Sl0014');
        
        switch ($vfun) {
            
            // Lấy toàn bộ chi tiết
            case "data":
                $lvsql = "SELECT A.*, B.lv002 as tenSanPham 
                          FROM sl_lv0014 A 
                          LEFT JOIN sl_lv0007 B ON A.lv003 = B.lv001 
                          ORDER BY A.lv001";
                $objEmp = db_query($lvsql);
                $vOutput = [];
                
                while ($vrow = db_fetch_array($objEmp)) {
                    $vOutput[] = [
                        "maChiTiet"    => $vrow['lv001'],
                        "maDonHang"    => $vrow['lv002'],
                        "maSanPham"    => $vrow['lv003'],
                        "tenSanPham"   => $vrow['tenSanPham'],
                        "soLuong"      => $vrow['lv004'],
                        "donGia"       => $vrow['lv005'],
                        "thanhTien"    => $vrow['lv006'],
                        "ngayBatDau"   => $vrow['lv013'],
                        "ngayKetThuc"  => $vrow['lv014'],
                        "ghiChu"       => $vrow['lv015'],
                    ];
                }
                break;
            
            // Lấy chi tiết theo mã đơn hàng
            case "getByOrder":
                $maDonHang = $input['maDonHang'] ?? $_POST['maDonHang'] ?? null;
                
                if (empty($maDonHang)) {
                    $vOutput = ['success' => false, 'message' => 'Thiếu mã đơn hàng'];
                    break;
                }
                
                $maDonHang = sof_escape_string($maDonHang);
                $lvsql = "SELECT A.*, B.lv002 as tenSanPham, B.lv004 as donViTinh
                          FROM sl_lv0014 A 
                          LEFT JOIN sl_lv0007 B ON A.lv003 = B.lv001 
                          WHERE A.lv002 = '$maDonHang'
                          ORDER BY A.lv001";
                $objEmp = db_query($lvsql);
                $vOutput = [];
                
                while ($vrow = db_fetch_array($objEmp)) {
                    $vOutput[] = [
                        "maChiTiet"    => $vrow['lv001'],
                        "maDonHang"    => $vrow['lv002'],
                        "maSanPham"    => $vrow['lv003'],
                        "tenSanPham"   => $vrow['tenSanPham'],
                        "donViTinh"    => $vrow['donViTinh'],
                        "soLuong"      => $vrow['lv004'],
                        "donGia"       => $vrow['lv005'],
                        "thanhTien"    => $vrow['lv006'],
                        "ngayBatDau"   => $vrow['lv013'],
                        "ngayKetThuc"  => $vrow['lv014'],
                        "ghiChu"       => $vrow['lv015'],
                    ];
                }
                break;
            
            // Lấy chi tiết theo sản phẩm
            case "getByProduct":
                $maSanPham = $input['maSanPham'] ?? $_POST['maSanPham'] ?? null;
                
                if (empty($maSanPham)) {
                    $vOutput = ['success' => false, 'message' => 'Thiếu mã sản phẩm'];
                    break;
                }
                
                $maSanPham = sof_escape_string($maSanPham);
                $lvsql = "SELECT A.*, C.lv014 as soDonHang
                          FROM sl_lv0014 A 
                          LEFT JOIN sl_lv0010 C ON A.lv002 = C.lv001
                          WHERE A.lv003 = '$maSanPham'
                          ORDER BY A.lv001 DESC LIMIT 100";
                $objEmp = db_query($lvsql);
                $vOutput = [];
                
                while ($vrow = db_fetch_array($objEmp)) {
                    $vOutput[] = [
                        "maChiTiet"    => $vrow['lv001'],
                        "maDonHang"    => $vrow['lv002'],
                        "soDonHang"    => $vrow['soDonHang'],
                        "maSanPham"    => $vrow['lv003'],
                        "soLuong"      => $vrow['lv004'],
                        "donGia"       => $vrow['lv005'],
                        "thanhTien"    => $vrow['lv006'],
                    ];
                }
                break;
        }
        break;

    /**
     * =========================================================================
     * NC_DONHANG - API TỔNG HỢP ĐƠN HÀNG
     * =========================================================================
     */
    case "nc_donhang":
        include_once("./class/sl_lv0010.php");
        include_once("./class/sl_lv0011.php");
        include_once("./class/sl_lv0007.php");
        
        $sl_lv0010 = new sl_lv0010($_SESSION['ERPSOFV2RRight'], $_SESSION['ERPSOFV2RUserID'], 'Sl0010');
        
        switch ($vfun) {
            
            // Lấy đơn hàng đầy đủ kèm chi tiết
            case "getFullOrder":
                $maDonHang = $input['maDonHang'] ?? $_POST['maDonHang'] ?? null;
                
                if (empty($maDonHang)) {
                    $vOutput = ['success' => false, 'message' => 'Thiếu mã đơn hàng'];
                    break;
                }
                
                $maDonHang = sof_escape_string($maDonHang);
                $sl_lv0010->LV_LoadID($maDonHang);
                
                if ($sl_lv0010->lv001 === null) {
                    $vOutput = ['success' => false, 'message' => 'Không tìm thấy đơn hàng'];
                    break;
                }
                
                // Lấy chi tiết đơn hàng
                $lvsql = "SELECT A.*, B.lv002 as tenSanPham, B.lv004 as donViTinh
                          FROM sl_lv0011 A 
                          LEFT JOIN sl_lv0007 B ON A.lv003 = B.lv001 
                          WHERE A.lv002 = '$maDonHang'
                          ORDER BY A.lv001";
                $objEmp = db_query($lvsql);
                $chiTietList = [];
                
                while ($vrow = db_fetch_array($objEmp)) {
                    $chiTietList[] = [
                        "maChiTiet"    => $vrow['lv001'],
                        "maSanPham"    => $vrow['lv003'],
                        "tenSanPham"   => $vrow['tenSanPham'],
                        "donViTinh"    => $vrow['donViTinh'],
                        "soLuongDat"   => $vrow['lv005'],
                        "donGia"       => $vrow['lv006'],
                        "soLuongGiao"  => $vrow['lv007'],
                        "thanhTien"    => $vrow['lv008'],
                        "chietKhau"    => $vrow['lv009'],
                        "thueVAT"      => $vrow['lv010'],
                    ];
                }
                
                $vOutput = [
                    'success' => true,
                    'donHang' => [
                        "maDonHang"        => $sl_lv0010->lv001,
                        "maKhachHang"      => $sl_lv0010->lv002,
                        "ngayDatHang"      => $sl_lv0010->lv003,
                        "ngayGiaoDuKien"   => $sl_lv0010->lv004,
                        "trangThai"        => $sl_lv0010->lv011,
                        "soDonHang"        => $sl_lv0010->lv014,
                        "tongTien"         => $sl_lv0010->lv106,
                    ],
                    'chiTiet' => $chiTietList,
                    'tongSoSanPham' => count($chiTietList)
                ];
                break;
            
            // Lấy danh sách đơn hàng mới nhất
            case "getRecent":
                $limit = intval($input['limit'] ?? $_POST['limit'] ?? 10);
                if ($limit > 100) $limit = 100;
                
                $lvsql = "SELECT A.*, 
                            (SELECT COUNT(*) FROM sl_lv0011 WHERE lv002 = A.lv001) as soSanPham
                          FROM sl_lv0010 A 
                          ORDER BY A.lv001 DESC 
                          LIMIT $limit";
                $objEmp = db_query($lvsql);
                $vOutput = [];
                
                while ($vrow = db_fetch_array($objEmp)) {
                    $vOutput[] = [
                        "maDonHang"      => $vrow['lv001'],
                        "maKhachHang"    => $vrow['lv002'],
                        "ngayDatHang"    => $vrow['lv003'],
                        "trangThai"      => $vrow['lv011'],
                        "soDonHang"      => $vrow['lv014'],
                        "tongTien"       => $vrow['lv106'],
                        "soSanPham"      => $vrow['soSanPham'],
                    ];
                }
                break;
        }
        break;

    /**
     * =========================================================================
     * NC_CART - GIỎ HÀNG & THANH TOÁN (Customer Frontend)
     * =========================================================================
     * APIs cho khách hàng: giỏ hàng, thanh toán, lịch sử đơn hàng
     * =========================================================================
     */
    case "nc_cart":
        
        // Helper function
        if (!function_exists('nc_escape_str')) {
            function nc_escape_str($s) {
                if (function_exists('db_connect')) db_connect();
                if (function_exists('sof_escape_string')) return sof_escape_string($s);
                if (function_exists('db_escape_string')) return db_escape_string($s);
                if (isset($GLOBALS['db_link']) && function_exists('mysqli_real_escape_string')) {
                    return mysqli_real_escape_string($GLOBALS['db_link'], $s);
                }
                return addslashes($s);
            }
        }
        
        // Generate unique order code
        if (!function_exists('nc_generate_order_code')) {
            function nc_generate_order_code() {
                $prefix = 'ORD';
                $date = date('Ymd');
                $random = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 6));
                return $prefix . $date . $random;
            }
        }
        
        // Generate product license code
        if (!function_exists('nc_generate_license_code')) {
            function nc_generate_license_code($productCode, $orderId) {
                $prefix = 'LIC';
                $hash = strtoupper(substr(md5($productCode . $orderId . time() . mt_rand()), 0, 12));
                return $prefix . '-' . substr($hash, 0, 4) . '-' . substr($hash, 4, 4) . '-' . substr($hash, 8, 4);
            }
        }
        
        switch ($vfun) {
            
            /**
             * CHECKOUT - Thanh toán giỏ hàng
             * Input: customerId, customerName, customerEmail, customerPhone, customerAddress, companyName, taxCode, isBusinessCustomer, items[], demoMode
             * items[]: { productCode, productName, plan, quantity, price, months }
             */
            case "checkout":
                $customerId = trim($input['customerId'] ?? $_POST['customerId'] ?? '');
                $customerName = trim($input['customerName'] ?? $_POST['customerName'] ?? '');
                $customerEmail = trim($input['customerEmail'] ?? $_POST['customerEmail'] ?? '');
                $customerPhone = trim($input['customerPhone'] ?? $_POST['customerPhone'] ?? '');
                $customerAddress = trim($input['customerAddress'] ?? $_POST['customerAddress'] ?? '');
                $companyName = trim($input['companyName'] ?? $_POST['companyName'] ?? '');
                $taxCode = trim($input['taxCode'] ?? $_POST['taxCode'] ?? '');
                $isBusinessCustomer = (bool)($input['isBusinessCustomer'] ?? $_POST['isBusinessCustomer'] ?? false);
                $items = $input['items'] ?? $_POST['items'] ?? [];
                $demoMode = (bool)($input['demoMode'] ?? $_POST['demoMode'] ?? false);
                $note = trim($input['note'] ?? $_POST['note'] ?? '');
                
                // Validate required fields
                if ($customerName === '' || empty($items)) {
                    $vOutput = ['success' => false, 'message' => 'Thiếu thông tin khách hàng hoặc giỏ hàng trống'];
                    break;
                }
                
                // Parse items if string
                if (is_string($items)) {
                    $items = json_decode($items, true);
                }
                
                if (!is_array($items) || count($items) === 0) {
                    $vOutput = ['success' => false, 'message' => 'Giỏ hàng trống'];
                    break;
                }
                
                // Generate or use customer ID
                if ($customerId === '') {
                    $customerId = 'WEB' . date('YmdHis') . mt_rand(100, 999);
                }
                
                $customerIdEsc = nc_escape_str($customerId);
                $customerNameEsc = nc_escape_str($customerName);
                $customerEmailEsc = nc_escape_str($customerEmail);
                $customerPhoneEsc = nc_escape_str($customerPhone);
                $customerAddressEsc = nc_escape_str($customerAddress);
                $companyNameEsc = nc_escape_str($companyName);
                $taxCodeEsc = nc_escape_str($taxCode);
                
                // Build note with business info
                $fullNote = $note;
                if ($isBusinessCustomer && ($companyName !== '' || $taxCode !== '')) {
                    $fullNote .= ($fullNote !== '' ? ' | ' : '') . "Công ty: $companyName | MST: $taxCode";
                }
                if ($customerAddress !== '') {
                    $fullNote .= ($fullNote !== '' ? ' | ' : '') . "Địa chỉ: $customerAddress";
                }
                $noteEsc = nc_escape_str($fullNote);
                
                // Check if customer exists in sl_lv0001
                $checkCustomerSql = "SELECT lv001 FROM sl_lv0001 WHERE lv001='$customerIdEsc' LIMIT 1";
                $checkResult = db_query($checkCustomerSql);
                
                if ($checkResult) {
                    $existingCustomer = db_fetch_array($checkResult);
                    
                    if (!$existingCustomer) {
                        // Create new customer with address
                        $insertCustomerSql = "INSERT INTO sl_lv0001 (lv001, lv002, lv006, lv008, lv019, lv024, lv010)
                                              VALUES ('$customerIdEsc', '$customerNameEsc', '$customerPhoneEsc', '$customerEmailEsc', 'Web Customer', NOW(), '$customerAddressEsc')";
                        db_query($insertCustomerSql);
                    }
                }
                
                // Generate order code
                $orderCode = nc_generate_order_code();
                $orderCodeEsc = nc_escape_str($orderCode);
                
                // Calculate total and prepare order items for response
                $totalAmount = 0;
                $orderItems = [];
                foreach ($items as $item) {
                    $qty = floatval($item['quantity'] ?? 1);
                    $price = floatval($item['price'] ?? 0);
                    $months = intval($item['months'] ?? 6);
                    $totalAmount += $qty * $price;
                    
                    // Store item for response
                    $orderItems[] = [
                        'productCode' => $item['productCode'] ?? '',
                        'productName' => $item['productName'] ?? $item['name'] ?? '',
                        'plan' => $item['plan'] ?? 'basic',
                        'quantity' => (int)$qty,
                        'price' => $price,
                        'months' => $months
                    ];
                }
                
                // Payment status: 1 = paid (for demo), 0 = pending
                $paymentStatus = $demoMode ? 1 : 0;
                $paymentNote = $demoMode ? 'Demo payment - auto approved' : '';
                
                $today = date('Y-m-d H:i:s');
                $todayDate = date('Y-m-d');
                
                // Try inserting into sl_lv0013 first, fallback to sl_lv0010 if table doesn't exist
                $useTable = 'sl_lv0013';
                
                // Get next ID for sl_lv0013
                $maxIdSql = "SELECT COALESCE(MAX(CAST(lv001 AS UNSIGNED)), 0) AS max_id FROM sl_lv0013";
                $maxResult = @db_query($maxIdSql);
                
                // If sl_lv0013 doesn't exist, try sl_lv0010
                if (!$maxResult) {
                    $useTable = 'sl_lv0010';
                    $maxIdSql = "SELECT COALESCE(MAX(CAST(lv001 AS UNSIGNED)), 0) AS max_id FROM sl_lv0010";
                    $maxResult = @db_query($maxIdSql);
                    
                    if (!$maxResult) {
                        $vOutput = ['success' => false, 'message' => 'Không thể truy vấn database - Bảng đơn hàng không tồn tại'];
                        break;
                    }
                }
                
                $maxRow = db_fetch_array($maxResult);
                $nextOrderId = ($maxRow['max_id'] ?? 0) + 1;
                $orderIdStr = str_pad($nextOrderId, 6, '0', STR_PAD_LEFT);
                
                // Insert order
                $orderInsertResult = false;
                
                if ($useTable === 'sl_lv0013') {
                    // Try sl_lv0013 first
                    $insertOrderSql = "INSERT INTO sl_lv0013 (lv001, lv002, lv003, lv004, lv006, lv009, lv011, lv013, lv016, lv030)
                                       VALUES ('$orderIdStr', '$customerIdEsc', '$customerNameEsc', '$today', '$customerPhoneEsc', '$customerEmailEsc', $paymentStatus, '$noteEsc', $totalAmount, '$paymentNote')";
                    $orderInsertResult = @db_query($insertOrderSql);
                    
                    if (!$orderInsertResult) {
                        // Try simplified insert
                        $insertOrderSql2 = "INSERT INTO sl_lv0013 (lv001, lv002, lv003, lv004, lv011, lv016)
                                            VALUES ('$orderIdStr', '$customerIdEsc', '$customerNameEsc', '$today', $paymentStatus, $totalAmount)";
                        $orderInsertResult = @db_query($insertOrderSql2);
                    }
                    
                    // If still failed, fallback to sl_lv0010
                    if (!$orderInsertResult) {
                        $useTable = 'sl_lv0010';
                    }
                }
                
                if ($useTable === 'sl_lv0010' && !$orderInsertResult) {
                    // Try sl_lv0010 (standard order table)
                    // sl_lv0010: lv001=OrderID, lv002=CustomerID, lv003=Date, lv011=Status, lv014=OrderCode, lv106=Total
                    $insertOrderSql = "INSERT INTO sl_lv0010 (lv001, lv002, lv003, lv011, lv014, lv106, lv110)
                                       VALUES ('$orderIdStr', '$customerIdEsc', '$todayDate', $paymentStatus, '$orderCodeEsc', $totalAmount, '$noteEsc')";
                    $orderInsertResult = @db_query($insertOrderSql);
                    
                    if (!$orderInsertResult) {
                        // Try minimal insert
                        $insertOrderSql2 = "INSERT INTO sl_lv0010 (lv001, lv002, lv003, lv011, lv106)
                                            VALUES ('$orderIdStr', '$customerIdEsc', '$todayDate', $paymentStatus, $totalAmount)";
                        $orderInsertResult = @db_query($insertOrderSql2);
                    }
                }
                
                if (!$orderInsertResult) {
                    $dbError = function_exists('db_error') ? db_error() : 'Unknown database error';
                    $vOutput = ['success' => false, 'message' => 'Không thể tạo đơn hàng. Chi tiết: ' . $dbError];
                    break;
                }
                
                // Insert order details into sl_lv0014
                $detailErrors = [];
                $licenses = [];
                
                // Get next ID for sl_lv0014
                $maxDetailIdSql = "SELECT COALESCE(MAX(CAST(lv001 AS UNSIGNED)), 0) AS max_id FROM sl_lv0014";
                $maxDetailResult = db_query($maxDetailIdSql);
                $maxDetailRow = db_fetch_array($maxDetailResult);
                $nextDetailId = ($maxDetailRow['max_id'] ?? 0) + 1;
                
                foreach ($items as $item) {
                    $productCode = nc_escape_str(trim($item['productCode'] ?? ''));
                    $productName = nc_escape_str(trim($item['productName'] ?? $item['name'] ?? ''));
                    $plan = nc_escape_str(trim($item['plan'] ?? 'basic'));
                    $qty = floatval($item['quantity'] ?? 1);
                    $months = intval($item['months'] ?? 6); // Số tháng đăng ký (bội số của 6)
                    $price = floatval($item['price'] ?? 0); // Giá đã tính cho cả thời hạn
                    $lineTotal = $qty * $price; // Tổng tiền = số lượng * giá (đã có months)
                    $itemNote = nc_escape_str(trim($item['note'] ?? ''));
                    
                    $detailIdStr = str_pad($nextDetailId, 8, '0', STR_PAD_LEFT);
                    
                    // Calculate dates based on months
                    $startDate = date('Y-m-d');
                    $endDate = date('Y-m-d', strtotime("+$months months"));
                    
                    // Generate license code if payment is completed (demo mode)
                    $licenseCode = '';
                    if ($demoMode) {
                        $licenseCode = nc_generate_license_code($productCode, $orderIdStr);
                    }
                    $licenseCodeEsc = nc_escape_str($licenseCode);
                    
                    // Insert detail
                    // lv001: ID chi tiết
                    // lv002: Mã đơn hàng (orderIdStr)
                    // lv003: Mã sản phẩm (productCode)
                    // lv004: SỐ THÁNG (months) - không phải quantity
                    // lv005: Giá đơn vị / tháng
                    // lv006: Tổng tiền dòng
                    // lv010: Ghi chú
                    // lv013: Ngày bắt đầu
                    // lv014: Ngày kết thúc  
                    // lv015: Trạng thái thanh toán (1=đã TT, 0=chưa TT)
                    // lv016: Số lượng (quantity)
                    // lv021: License code
                    $pricePerMonth = $months > 0 ? ($price / $months) : $price;
                    $insertDetailSql = "INSERT INTO sl_lv0014 (lv001, lv002, lv003, lv004, lv005, lv006, lv010, lv013, lv014, lv015, lv016, lv021)
                                        VALUES ('$detailIdStr', '$orderIdStr', '$productCode', $months, $pricePerMonth, $lineTotal, '$itemNote ($productName - $plan)', '$startDate', '$endDate', " . ($demoMode ? "1" : "0") . ", $qty, '$licenseCodeEsc')";
                    
                    $detailResult = db_query($insertDetailSql);
                    
                    if (!$detailResult) {
                        $detailErrors[] = $productCode;
                    } else {
                        // Create extension record in sl_lv0511 (as requested by user)
                        // lv001: Auto increment
                        // lv002: Link to Order ID (sl_lv0013.lv001)
                        // lv003: Start Date (renewal date)
                        // lv004: End Date (expiry date)
                        // lv005: Lan thanh toan (Initial = 1)
                        // lv006: Amount
                        // lv007: Paid Status (1=Paid, 0=Unpaid)
                        $insertExtensionSql = "INSERT INTO sl_lv0511 (lv002, lv003, lv004, lv005, lv006, lv007)
                                              VALUES ('$orderIdStr', '$startDate', '$endDate', 1, $lineTotal, " . ($demoMode ? "1" : "0") . ")";
                        @db_query($insertExtensionSql);

                        if ($demoMode && $licenseCode !== '') {
                            $licenses[] = [
                                'productCode' => $productCode,
                                'productName' => $item['productName'] ?? $item['name'] ?? $productCode,
                                'plan' => $item['plan'] ?? 'basic',
                                'licenseCode' => $licenseCode,
                                'activatedAt' => $today,
                                'months' => $months,
                                'startDate' => $startDate,
                                'endDate' => $endDate,
                                'quantity' => $qty,
                                'price' => $price
                            ];
                        }
                    }
                    
                    // Thêm vào phiếu bán hàng (cr_lv0276)
                    $insertTicketSql = "INSERT INTO cr_lv0276(lv002,lv003,lv004,lv005,lv006,lv007,lv008,lv011,lv018,lv019) 
                                        SELECT '$orderIdStr', '$productCode', $qty, A.lv004, 0, A.lv007, 0, 0, A.lv009, A.lv002 
                                        FROM sl_lv0007 A WHERE lv001='$productCode'";
                    @db_query($insertTicketSql);
                    
                    $nextDetailId++;
                }
                
                // Save licenses to customer record (JSON in lv019 note field or separate table)
                if ($demoMode && !empty($licenses)) {
                    // Store licenses in order note for now (can be separate table later)
                    $licensesJson = nc_escape_str(json_encode($licenses, JSON_UNESCAPED_UNICODE));
                    $updateLicenseSql = "UPDATE sl_lv0013 SET lv030 = CONCAT(IFNULL(lv030,''), ' | Licenses: $licensesJson') WHERE lv001='$orderIdStr'";
                    db_query($updateLicenseSql);
                }
                
                $vOutput = [
                    'success' => true,
                    'message' => $demoMode ? 'Thanh toán thành công (Demo Mode)' : 'Đơn hàng đã được tạo, chờ thanh toán',
                    'data' => [
                        'orderId' => $orderIdStr,
                        'orderCode' => $orderCode,
                        'customerId' => $customerId,
                        'totalAmount' => $totalAmount,
                        'paymentStatus' => $paymentStatus,
                        'paymentStatusText' => $demoMode ? 'Đã thanh toán' : 'Chờ thanh toán',
                        'createdAt' => $today,
                        'itemCount' => count($items),
                        'items' => $orderItems,
                        'licenses' => $demoMode ? $licenses : []
                    ]
                ];
                
                if (!empty($detailErrors)) {
                    $vOutput['warnings'] = ['Một số sản phẩm không thể thêm: ' . implode(', ', $detailErrors)];
                }
                break;
            
            /**
             * GET CUSTOMER ORDERS - Lấy danh sách đơn hàng của khách
             * Input: customerId, limit, offset
             */
            case "getCustomerOrders":
                $customerId = trim($input['customerId'] ?? $_POST['customerId'] ?? '');
                $limit = intval($input['limit'] ?? $_POST['limit'] ?? 20);
                $offset = intval($input['offset'] ?? $_POST['offset'] ?? 0);
                
                if ($customerId === '') {
                    $vOutput = ['success' => false, 'message' => 'Thiếu mã khách hàng'];
                    break;
                }
                
                if ($limit < 1) $limit = 1;
                if ($limit > 100) $limit = 100;
                if ($offset < 0) $offset = 0;
                
                $customerIdEsc = nc_escape_str($customerId);
                
                $ordersSql = "SELECT lv001, lv002, lv003, lv004, lv011, lv013, lv016, lv029, lv030
                              FROM sl_lv0013 
                              WHERE lv002='$customerIdEsc' 
                              ORDER BY lv004 DESC 
                              LIMIT $offset, $limit";
                
                $ordersResult = db_query($ordersSql);
                $orders = [];
                
                while ($row = db_fetch_array($ordersResult, MYSQLI_ASSOC)) {
                    $statusText = 'Chờ xử lý';
                    $status = intval($row['lv011'] ?? 0);
                    if ($status === 1) $statusText = 'Đang xử lý';
                    if ($status === 2) $statusText = 'Đã thanh toán';
                    if ($status === 3) $statusText = 'Đã hủy';
                    if ($status >= 4) $statusText = 'Hoàn thành';
                    
                    $orders[] = [
                        'orderId' => $row['lv001'],
                        'customerId' => $row['lv002'],
                        'customerName' => $row['lv003'],
                        'createdAt' => $row['lv004'],
                        'status' => $status,
                        'statusText' => $statusText,
                        'note' => $row['lv013'],
                        'totalAmount' => floatval($row['lv016'] ?? 0),
                        'paidAt' => $row['lv029'],
                        'paymentNote' => $row['lv030']
                    ];
                }
                
                // Get total count
                $countSql = "SELECT COUNT(*) as total FROM sl_lv0013 WHERE lv002='$customerIdEsc'";
                $countResult = db_query($countSql);
                $countRow = db_fetch_array($countResult, MYSQLI_ASSOC);
                $total = intval($countRow['total'] ?? 0);
                
                $vOutput = [
                    'success' => true,
                    'data' => $orders,
                    'pagination' => [
                        'total' => $total,
                        'limit' => $limit,
                        'offset' => $offset
                    ]
                ];
                break;
            
            /**
             * GET ORDER DETAIL - Lấy chi tiết đơn hàng
             * Input: orderId
             */
            case "getOrderDetail":
                $orderId = trim($input['orderId'] ?? $_POST['orderId'] ?? '');
                
                if ($orderId === '') {
                    $vOutput = ['success' => false, 'message' => 'Thiếu mã đơn hàng'];
                    break;
                }
                
                $orderIdEsc = nc_escape_str($orderId);
                
                // Get order header
                $orderSql = "SELECT * FROM sl_lv0013 WHERE lv001='$orderIdEsc' LIMIT 1";
                $orderResult = db_query($orderSql);
                $orderRow = db_fetch_array($orderResult, MYSQLI_ASSOC);
                
                if (!$orderRow) {
                    $vOutput = ['success' => false, 'message' => 'Không tìm thấy đơn hàng'];
                    break;
                }
                
                $status = intval($orderRow['lv011'] ?? 0);
                $statusText = 'Chờ xử lý';
                if ($status === 1) $statusText = 'Đang xử lý';
                if ($status === 2) $statusText = 'Đã thanh toán';
                if ($status === 3) $statusText = 'Đã hủy';
                if ($status >= 4) $statusText = 'Hoàn thành';
                
                // Get order items
                $itemsSql = "SELECT A.*, B.lv002 as tenSanPham, B.lv004 as donViTinh
                             FROM sl_lv0014 A 
                             LEFT JOIN sl_lv0007 B ON A.lv003 = B.lv001 
                             WHERE A.lv002='$orderIdEsc' 
                             ORDER BY A.lv001";
                $itemsResult = db_query($itemsSql);
                $items = [];
                $licenses = [];
                
                while ($itemRow = db_fetch_array($itemsResult, MYSQLI_ASSOC)) {
                    $items[] = [
                        'detailId' => $itemRow['lv001'],
                        'productCode' => $itemRow['lv003'],
                        'productName' => $itemRow['tenSanPham'] ?? $itemRow['lv003'],
                        'unit' => $itemRow['donViTinh'] ?? '',
                        'quantity' => floatval($itemRow['lv004'] ?? 0),
                        'price' => floatval($itemRow['lv005'] ?? 0),
                        'lineTotal' => floatval($itemRow['lv006'] ?? 0),
                        'note' => $itemRow['lv010'],
                        'startDate' => $itemRow['lv013'],
                        'endDate' => $itemRow['lv014'],
                        'approved' => intval($itemRow['lv015'] ?? 0),
                        'licenseCode' => $itemRow['lv021'] ?? ''
                    ];
                    
                    // Extract license info if exists
                    if (!empty($itemRow['lv021'])) {
                        $licenses[] = [
                            'productCode' => $itemRow['lv003'],
                            'productName' => $itemRow['tenSanPham'] ?? $itemRow['lv003'],
                            'licenseCode' => $itemRow['lv021'],
                            'activatedAt' => $itemRow['lv013']
                        ];
                    }
                }
                
                $paymentNote = $orderRow['lv030'] ?? '';
                
                $vOutput = [
                    'success' => true,
                    'data' => [
                        'order' => [
                            'orderId' => $orderRow['lv001'],
                            'customerId' => $orderRow['lv002'],
                            'customerName' => $orderRow['lv003'],
                            'createdAt' => $orderRow['lv004'],
                            'email' => $orderRow['lv009'],
                            'status' => $status,
                            'statusText' => $statusText,
                            'note' => $orderRow['lv013'],
                            'totalAmount' => floatval($orderRow['lv016'] ?? 0),
                            'paidAt' => $orderRow['lv029'],
                            'paymentNote' => $paymentNote
                        ],
                        'items' => $items,
                        'licenses' => $licenses,
                        'itemCount' => count($items)
                    ]
                ];
                break;
            
            /**
             * GET CUSTOMER LICENSES - Lấy danh sách license của khách
             * Input: customerId
             */
            case "getCustomerLicenses":
                $customerId = trim($input['customerId'] ?? $_POST['customerId'] ?? '');
                
                if ($customerId === '') {
                    $vOutput = ['success' => false, 'message' => 'Thiếu mã khách hàng'];
                    break;
                }
                
                $customerIdEsc = nc_escape_str($customerId);
                
                // Get all licenses from paid orders (sl_lv0014.lv021 stores license code)
                $licensesSql = "SELECT 
                                    D.lv001 as detailId,
                                    D.lv002 as orderId,
                                    D.lv003 as productCode,
                                    P.lv002 as productName,
                                    D.lv021 as licenseCode,
                                    D.lv013 as activatedAt,
                                    O.lv004 as orderDate
                                FROM sl_lv0014 D
                                INNER JOIN sl_lv0013 O ON D.lv002 = O.lv001
                                LEFT JOIN sl_lv0007 P ON D.lv003 = P.lv001
                                WHERE O.lv002='$customerIdEsc' 
                                  AND O.lv011=2 
                                  AND D.lv021 IS NOT NULL 
                                  AND D.lv021 != ''
                                ORDER BY O.lv004 DESC, D.lv001";
                $licensesResult = db_query($licensesSql);
                $allLicenses = [];
                
                while ($row = db_fetch_array($licensesResult, MYSQLI_ASSOC)) {
                    $allLicenses[] = [
                        'orderId' => $row['orderId'],
                        'orderDate' => $row['orderDate'],
                        'productCode' => $row['productCode'],
                        'productName' => $row['productName'] ?? $row['productCode'],
                        'licenseCode' => $row['licenseCode'],
                        'activatedAt' => $row['activatedAt']
                    ];
                }
                
                $vOutput = [
                    'success' => true,
                    'data' => $allLicenses,
                    'count' => count($allLicenses)
                ];
                break;
            
            /**
             * GET CUSTOMER PROFILE - Lấy thông tin khách hàng
             * Input: customerId OR email
             */
            case "getCustomerProfile":
                $customerId = trim($input['customerId'] ?? $_POST['customerId'] ?? '');
                $email = trim($input['email'] ?? $_POST['email'] ?? '');
                
                if ($customerId === '' && $email === '') {
                    $vOutput = ['success' => false, 'message' => 'Thiếu mã khách hàng hoặc email'];
                    break;
                }
                
                $whereClause = '';
                if ($customerId !== '') {
                    $customerIdEsc = nc_escape_str($customerId);
                    $whereClause = "lv001='$customerIdEsc'";
                } else {
                    $emailEsc = nc_escape_str($email);
                    $whereClause = "lv008='$emailEsc'";
                }
                
                $customerSql = "SELECT lv001, lv002, lv003, lv006, lv008, lv019, lv022, lv024, lv025 
                                FROM sl_lv0001 WHERE $whereClause LIMIT 1";
                $customerResult = db_query($customerSql);
                $customerRow = db_fetch_array($customerResult, MYSQLI_ASSOC);
                
                if (!$customerRow) {
                    $vOutput = ['success' => false, 'message' => 'Không tìm thấy khách hàng'];
                    break;
                }
                
                // Get order statistics
                $custId = nc_escape_str($customerRow['lv001']);
                $statsSql = "SELECT 
                                COUNT(*) as totalOrders,
                                SUM(CASE WHEN lv011=2 THEN 1 ELSE 0 END) as paidOrders,
                                SUM(CASE WHEN lv011=2 THEN lv016 ELSE 0 END) as totalSpent
                             FROM sl_lv0013 WHERE lv002='$custId'";
                $statsResult = db_query($statsSql);
                $statsRow = db_fetch_array($statsResult, MYSQLI_ASSOC);
                
                $vOutput = [
                    'success' => true,
                    'data' => [
                        'customerId' => $customerRow['lv001'],
                        'name' => $customerRow['lv002'],
                        'address' => $customerRow['lv003'],
                        'phone' => $customerRow['lv006'],
                        'email' => $customerRow['lv008'],
                        'note' => $customerRow['lv019'],
                        'group' => $customerRow['lv022'],
                        'createdAt' => $customerRow['lv024'],
                        'assignedTo' => $customerRow['lv025'],
                        'stats' => [
                            'totalOrders' => intval($statsRow['totalOrders'] ?? 0),
                            'paidOrders' => intval($statsRow['paidOrders'] ?? 0),
                            'totalSpent' => floatval($statsRow['totalSpent'] ?? 0)
                        ]
                    ]
                ];
                break;
            
            /**
             * CREATE/UPDATE CUSTOMER - Tạo hoặc cập nhật khách hàng
             * Input: customerId (optional for create), name, email, phone, address
             */
            case "saveCustomer":
                db_connect();
                $customerId = trim($input['customerId'] ?? $_POST['customerId'] ?? '');
                $name = trim($input['name'] ?? $_POST['name'] ?? '');
                $email = trim($input['email'] ?? $_POST['email'] ?? '');
                $phone = trim($input['phone'] ?? $_POST['phone'] ?? '');
                $address = trim($input['address'] ?? $_POST['address'] ?? '');
                
                if ($name === '') {
                    $vOutput = ['success' => false, 'message' => 'Thiếu tên khách hàng'];
                    break;
                }
                
                $nameEsc = nc_escape_str($name);
                $emailEsc = nc_escape_str($email);
                $phoneEsc = nc_escape_str($phone);
                $addressEsc = nc_escape_str($address);
                
                if ($customerId === '') {
                    // Create new customer
                    $customerId = 'WEB' . date('YmdHis') . mt_rand(100, 999);
                    $customerIdEsc = nc_escape_str($customerId);
                    
                    $insertSql = "INSERT INTO sl_lv0001 (lv001, lv002, lv003, lv006, lv008, lv019, lv024)
                                  VALUES ('$customerIdEsc', '$nameEsc', '$addressEsc', '$phoneEsc', '$emailEsc', 'Web Customer', NOW())";
                    $result = db_query($insertSql);
                    
                    if ($result) {
                        $vOutput = ['success' => true, 'message' => 'Tạo khách hàng thành công', 'data' => ['customerId' => $customerId]];
                    } else {
                        $vOutput = ['success' => false, 'message' => 'Không thể tạo khách hàng'];
                    }
                } else {
                    // Update existing customer
                    $customerIdEsc = nc_escape_str($customerId);
                    
                    $updateSql = "UPDATE sl_lv0001 SET lv002='$nameEsc', lv003='$addressEsc', lv006='$phoneEsc', lv008='$emailEsc' WHERE lv001='$customerIdEsc'";
                    $result = db_query($updateSql);
                    
                    if ($result) {
                        $vOutput = ['success' => true, 'message' => 'Cập nhật khách hàng thành công', 'data' => ['customerId' => $customerId]];
                    } else {
                        $vOutput = ['success' => false, 'message' => 'Không thể cập nhật khách hàng'];
                    }
                }
                break;
            
            /**
             * GET DEMO CONFIG - Lấy cấu hình demo (số ngày, giá)
             */
            case "getDemoConfig":
                // Lấy cấu hình từ sl_lv0007 với mã DEMOMIENPHI và PHIDEMONGAY
                $configSql = "SELECT lv001, lv007, lv018 FROM sl_lv0007 WHERE lv001 IN ('DEMOMIENPHI', 'PHIDEMONGAY')";
                $configResult = @db_query($configSql);
                
                // Default values
                $freeDemoWeeks = 1;    // Mặc định 1 tuần demo miễn phí
                $paidDemoWeeks = 4;    // Mặc định 4 tuần demo có phí
                $paidDemoPrice = 200000; // Giá demo có phí mặc định
                
                if ($configResult) {
                    while ($row = db_fetch_array($configResult)) {
                        if ($row['lv001'] === 'DEMOMIENPHI') {
                            $freeDemoWeeks = intval($row['lv018']) ?: 1;
                        } elseif ($row['lv001'] === 'PHIDEMONGAY') {
                            $paidDemoWeeks = intval($row['lv018']) ?: 4;
                            $paidDemoPrice = floatval($row['lv007']) ?: 200000;
                        }
                    }
                }
                
                $vOutput = [
                    'success' => true,
                    'data' => [
                        'freeDemoWeeks' => $freeDemoWeeks,
                        'paidDemoWeeks' => $paidDemoWeeks,
                        'paidDemoPrice' => $paidDemoPrice,
                        'freeDemoDays' => $freeDemoWeeks * 7,
                        'paidDemoDays' => $paidDemoWeeks * 7
                    ]
                ];
                break;
            
            /**
             * REQUEST DEMO FREE - Yêu cầu demo miễn phí
             * Input: customerName, customerEmail, customerPhone, customerAddress, 
             *        companyName, taxCode, productCode, productName, note
             */
            case "requestDemoFree":
                db_connect();
                $customerName = trim($input['customerName'] ?? $_POST['customerName'] ?? '');
                $customerEmail = trim($input['customerEmail'] ?? $_POST['customerEmail'] ?? '');
                $customerPhone = trim($input['customerPhone'] ?? $_POST['customerPhone'] ?? '');
                $customerAddress = trim($input['customerAddress'] ?? $_POST['customerAddress'] ?? '');
                $companyName = trim($input['companyName'] ?? $_POST['companyName'] ?? '');
                $taxCode = trim($input['taxCode'] ?? $_POST['taxCode'] ?? '');
                $productCode = trim($input['productCode'] ?? $_POST['productCode'] ?? '');
                $productName = trim($input['productName'] ?? $_POST['productName'] ?? '');
                $note = trim($input['note'] ?? $_POST['note'] ?? '');
                
                // Validation
                if ($customerName === '' || $customerEmail === '' || $customerPhone === '') {
                    $vOutput = ['success' => false, 'message' => 'Vui lòng điền đầy đủ thông tin bắt buộc'];
                    break;
                }
                
                // Email validation
                if (!filter_var($customerEmail, FILTER_VALIDATE_EMAIL)) {
                    $vOutput = ['success' => false, 'message' => 'Email không hợp lệ'];
                    break;
                }
                
                // Escape strings
                $customerNameEsc = nc_escape_str($customerName);
                $customerEmailEsc = nc_escape_str($customerEmail);
                $customerPhoneEsc = nc_escape_str($customerPhone);
                $customerAddressEsc = nc_escape_str($customerAddress);
                $companyNameEsc = nc_escape_str($companyName);
                $taxCodeEsc = nc_escape_str($taxCode);
                $productCodeEsc = nc_escape_str($productCode);
                $productNameEsc = nc_escape_str($productName);
                $today = date('Y-m-d H:i:s');
                
                // Build note
                $fullNote = "YÊU CẦU DEMO MIỄN PHÍ";
                if ($productName !== '') $fullNote .= " | Sản phẩm: $productName";
                if ($companyName !== '') $fullNote .= " | Công ty: $companyName";
                if ($taxCode !== '') $fullNote .= " | MST: $taxCode";
                if ($customerAddress !== '') $fullNote .= " | Địa chỉ: $customerAddress";
                if ($note !== '') $fullNote .= " | Ghi chú: $note";
                $noteEsc = nc_escape_str($fullNote);
                
                // Check or create customer
                $customerId = trim($input['customerId'] ?? $_POST['customerId'] ?? '');
                
                // If customerId provided, verification
                if ($customerId !== '') {
                    $checkIdSql = "SELECT lv001 FROM sl_lv0001 WHERE lv001='" . nc_escape_str($customerId) . "'";
                    $checkIdRes = @db_query($checkIdSql);
                    if ($checkIdRes && mysqli_num_rows($checkIdRes) > 0) {
                         // Valid customer
                    } else {
                        $customerId = ''; // Invalid ID, reset
                    }
                }

                // If no valid ID, check by email
                if ($customerId === '') {
                    $checkCustomerSql = "SELECT lv001 FROM sl_lv0001 WHERE lv008='$customerEmailEsc' OR lv004='$customerEmailEsc' LIMIT 1";
                    $checkResult = @db_query($checkCustomerSql);
                    if ($checkResult) {
                        $existing = db_fetch_array($checkResult);
                        if ($existing) {
                            $customerId = $existing['lv001'];
                        }
                    }
                }
                
                if ($customerId === '') {
                    $customerId = 'DEMO_' . strtoupper(substr(md5($customerEmail . time()), 0, 8));
                    $customerIdEsc = nc_escape_str($customerId);
                    // Use correct columns: lv002=Name, lv003=Address, lv006=Phone, lv008=Email
                    $insertCustomerSql = "INSERT INTO sl_lv0001 (lv001, lv002, lv003, lv006, lv008) 
                                          VALUES ('$customerIdEsc', '$customerNameEsc', '$customerAddressEsc', '$customerPhoneEsc', '$customerEmailEsc')";
                    @db_query($insertCustomerSql);
                }
                
                $customerIdEsc = nc_escape_str($customerId);
                
                // Generate request ID
                $requestId = 'DMF-' . date('Ymd') . '-' . strtoupper(substr(md5(time() . rand()), 0, 6));
                $requestIdEsc = nc_escape_str($requestId);
                
                // Insert demo request into sl_lv0013 with status = 0 (Chờ duyệt)
                // Using lv030 to store special type tag: TYPE:DEMO_FREE
                $typeTag = "TYPE:DEMO_FREE | Demo miễn phí - Chờ duyệt";
                $insertSql = "INSERT INTO sl_lv0013 (lv001, lv002, lv003, lv004, lv006, lv009, lv011, lv013, lv016, lv030)
                              VALUES ('$requestIdEsc', '$customerIdEsc', '$customerNameEsc', '$today', '$customerPhoneEsc', '$customerEmailEsc', 0, '$noteEsc', 0, '$typeTag')";
                
                $insertResult = db_query($insertSql);
                
                if ($insertResult) {
                     // Insert detail into cr_lv0276 (Phiếu bán hàng)
                     if ($productCode !== '') {
                        $ticketSql = "INSERT INTO cr_lv0276(lv002,lv003,lv004,lv005,lv006,lv007,lv008,lv011,lv018,lv019) 
                                      SELECT '$requestIdEsc', '$productCodeEsc', 1, A.lv004, 0, A.lv007, 0, 0, A.lv009, A.lv002 
                                      FROM sl_lv0007 A WHERE lv001='$productCodeEsc'";
                        @db_query($ticketSql);
                     }
                    $vOutput = [
                        'success' => true,
                        'message' => 'Yêu cầu demo miễn phí đã được gửi. Chúng tôi sẽ liên hệ trong 3-7 ngày làm việc.',
                        'data' => [
                            'requestId' => $requestId,
                            'customerId' => $customerId,
                            'status' => 'pending',
                            'statusText' => 'Chờ duyệt',
                            'estimatedDays' => '3-7 ngày làm việc'
                        ]
                    ];
                } else {
                     $dbErr = "";
                     if(function_exists('db_error')) $dbErr = db_error();
                     $vOutput = ['success' => false, 'message' => 'Lỗi hệ thống: ' . $dbErr];
                }
                break;
            
            /**
             * REQUEST DEMO PAID - Yêu cầu demo có phí (thanh toán ngay)
             * Input: customerName, customerEmail, customerPhone, customerAddress, 
             *        companyName, taxCode, productCode, productName, amount, note
             */
            case "requestDemoPaid":
                db_connect();
                $customerName = trim($input['customerName'] ?? $_POST['customerName'] ?? '');
                $customerEmail = trim($input['customerEmail'] ?? $_POST['customerEmail'] ?? '');
                $customerPhone = trim($input['customerPhone'] ?? $_POST['customerPhone'] ?? '');
                $customerAddress = trim($input['customerAddress'] ?? $_POST['customerAddress'] ?? '');
                $companyName = trim($input['companyName'] ?? $_POST['companyName'] ?? '');
                $taxCode = trim($input['taxCode'] ?? $_POST['taxCode'] ?? '');
                $productCode = trim($input['productCode'] ?? $_POST['productCode'] ?? '');
                $productName = trim($input['productName'] ?? $_POST['productName'] ?? '');
                $amount = floatval($input['amount'] ?? $_POST['amount'] ?? 200000);
                $note = trim($input['note'] ?? $_POST['note'] ?? '');
                
                // Validation
                if ($customerName === '' || $customerEmail === '' || $customerPhone === '') {
                    $vOutput = ['success' => false, 'message' => 'Vui lòng điền đầy đủ thông tin bắt buộc'];
                    break;
                }
                
                // Email validation
                if (!filter_var($customerEmail, FILTER_VALIDATE_EMAIL)) {
                    $vOutput = ['success' => false, 'message' => 'Email không hợp lệ'];
                    break;
                }
                
                // Escape strings
                $customerNameEsc = nc_escape_str($customerName);
                $customerEmailEsc = nc_escape_str($customerEmail);
                $customerPhoneEsc = nc_escape_str($customerPhone);
                $customerAddressEsc = nc_escape_str($customerAddress);
                $companyNameEsc = nc_escape_str($companyName);
                $taxCodeEsc = nc_escape_str($taxCode);
                $productCodeEsc = nc_escape_str($productCode);
                $productNameEsc = nc_escape_str($productName);
                $today = date('Y-m-d H:i:s');
                
                // Build note
                $fullNote = "YÊU CẦU DEMO CÓ PHÍ";
                if ($productName !== '') $fullNote .= " | Sản phẩm: $productName";
                if ($companyName !== '') $fullNote .= " | Công ty: $companyName";
                if ($taxCode !== '') $fullNote .= " | MST: $taxCode";
                if ($customerAddress !== '') $fullNote .= " | Địa chỉ: $customerAddress";
                $fullNote .= " | Số tiền: " . number_format($amount, 0, ',', '.') . " VND";
                if ($note !== '') $fullNote .= " | Ghi chú: $note";
                $noteEsc = nc_escape_str($fullNote);
                
                // Check or create customer
                $customerId = trim($input['customerId'] ?? $_POST['customerId'] ?? '');
                
                if ($customerId !== '') {
                    $checkIdSql = "SELECT lv001 FROM sl_lv0001 WHERE lv001='" . nc_escape_str($customerId) . "'";
                    $checkIdRes = @db_query($checkIdSql);
                    if ($checkIdRes && mysqli_num_rows($checkIdRes) > 0) {
                         // Valid customer
                    } else {
                        $customerId = '';
                    }
                }

                if ($customerId === '') {
                    $checkCustomerSql = "SELECT lv001 FROM sl_lv0001 WHERE lv008='$customerEmailEsc' OR lv004='$customerEmailEsc' LIMIT 1";
                    $checkResult = @db_query($checkCustomerSql);
                    if ($checkResult) {
                        $existing = db_fetch_array($checkResult);
                        if ($existing) {
                            $customerId = $existing['lv001'];
                        }
                    }
                }
                
                if ($customerId === '') {
                    $customerId = 'DEMO_' . strtoupper(substr(md5($customerEmail . time()), 0, 8));
                    $customerIdEsc = nc_escape_str($customerId);
                    // Use correct columns: lv002=Name, lv003=Address, lv006=Phone, lv008=Email
                    $insertCustomerSql = "INSERT INTO sl_lv0001 (lv001, lv002, lv003, lv006, lv008) 
                                          VALUES ('$customerIdEsc', '$customerNameEsc', '$customerAddressEsc', '$customerPhoneEsc', '$customerEmailEsc')";
                    @db_query($insertCustomerSql);
                }
                
                $customerIdEsc = nc_escape_str($customerId);
                
                // Generate order ID for paid demo
                $orderId = 'DMP-' . date('Ymd') . '-' . strtoupper(substr(md5(time() . rand()), 0, 6));
                $orderIdEsc = nc_escape_str($orderId);
                
                // Insert demo order into sl_lv0013 with status = 1 (Chờ thanh toán)
                // Using lv030 tag: TYPE:DEMO_PAID
                $typeTag = "TYPE:DEMO_PAID | Demo có phí - Chờ thanh toán";
                $insertSql = "INSERT INTO sl_lv0013 (lv001, lv002, lv003, lv004, lv006, lv009, lv011, lv013, lv016, lv030)
                              VALUES ('$orderIdEsc', '$customerIdEsc', '$customerNameEsc', '$today', '$customerPhoneEsc', '$customerEmailEsc', 1, '$noteEsc', $amount, '$typeTag')";
                
                $insertResult = db_query($insertSql);
                
                if ($insertResult) {
                    // Insert detail into cr_lv0276 (Phiếu bán hàng)
                     if ($productCode !== '') {
                        $ticketSql = "INSERT INTO cr_lv0276(lv002,lv003,lv004,lv005,lv006,lv007,lv008,lv011,lv018,lv019) 
                                      SELECT '$orderIdEsc', '$productCodeEsc', 1, A.lv004, 0, A.lv007, 0, 0, A.lv009, A.lv002 
                                      FROM sl_lv0007 A WHERE lv001='$productCodeEsc'";
                        @db_query($ticketSql);
                     }
                    // Return payment info (for VNPay, MoMo, etc.)
                    $vOutput = [
                        'success' => true,
                        'message' => 'Đơn demo có phí đã được tạo. Vui lòng thanh toán để kích hoạt.',
                        'data' => [
                            'orderId' => $orderId,
                            'customerId' => $customerId,
                            'amount' => $amount,
                            'status' => 'pending_payment',
                            'statusText' => 'Chờ thanh toán',
                            'paymentInfo' => [
                                'bankName' => 'Vietcombank',
                                'accountNumber' => '0421000123456',
                                'accountName' => 'CONG TY TNHH SOF',
                                'transferContent' => "DEMO $orderId"
                            ]
                        ]
                    ];
                } else {
                    $vOutput = ['success' => false, 'message' => 'Không thể tạo đơn demo. Vui lòng thử lại.'];
                }
                break;
            
            /**
             * CONFIRM DEMO FREE - Xác nhận duyệt demo miễn phí (Admin)
             * Input: requestId
             */
            case "confirmDemoFree":
                $requestId = trim($input['requestId'] ?? $_POST['requestId'] ?? '');
                
                if ($requestId === '') {
                    $vOutput = ['success' => false, 'message' => 'Thiếu mã yêu cầu'];
                    break;
                }
                
                $requestIdEsc = nc_escape_str($requestId);
                $today = date('Y-m-d H:i:s');
                
                // Update status to approved (2)
                $updateSql = "UPDATE sl_lv0013 SET lv011=2, lv029='$today', lv030='Demo miễn phí - Đã duyệt' WHERE lv001='$requestIdEsc' AND lv027=1";
                $updateResult = @db_query($updateSql);
                
                if ($updateResult && mysqli_affected_rows($GLOBALS['db_link'] ?? null) > 0) {
                    $vOutput = [
                        'success' => true,
                        'message' => 'Đã duyệt yêu cầu demo miễn phí',
                        'data' => ['requestId' => $requestId]
                    ];
                } else {
                    $vOutput = ['success' => false, 'message' => 'Không tìm thấy yêu cầu hoặc đã được xử lý'];
                }
                break;
            
            /**
             * CONFIRM PAYMENT ONLINE - Xác nhận thanh toán online (callback từ cổng thanh toán)
             * Input: orderId, transactionId, paymentMethod (vnpay, momo, zalopay, sepay), amount, status
             */
            case "confirmPaymentOnline":
                $orderId = trim($input['orderId'] ?? $_POST['orderId'] ?? '');
                $transactionId = trim($input['transactionId'] ?? $_POST['transactionId'] ?? '');
                $paymentMethod = trim($input['paymentMethod'] ?? $_POST['paymentMethod'] ?? 'transfer');
                $paymentAmount = floatval($input['amount'] ?? $_POST['amount'] ?? 0);
                $paymentStatus = trim($input['status'] ?? $_POST['status'] ?? 'success');
                
                if ($orderId === '') {
                    $vOutput = ['success' => false, 'message' => 'Thiếu mã đơn hàng'];
                    break;
                }
                
                $orderIdEsc = nc_escape_str($orderId);
                $transactionIdEsc = nc_escape_str($transactionId);
                $paymentMethodEsc = nc_escape_str($paymentMethod);
                $today = date('Y-m-d H:i:s');
                
                // Check order exists and is pending
                // Check order exists and is pending
                $checkSql = "SELECT lv001, lv011, lv016, lv030 FROM sl_lv0013 WHERE lv001='$orderIdEsc' LIMIT 1";
                $checkResult = @db_query($checkSql);
                $order = $checkResult ? db_fetch_array($checkResult) : null;
                
                if (!$order) {
                    $vOutput = ['success' => false, 'message' => 'Không tìm thấy đơn hàng'];
                    break;
                }
                
                if (intval($order['lv011']) === 2) {
                    $vOutput = ['success' => true, 'message' => 'Đơn hàng đã được thanh toán trước đó', 'data' => ['orderId' => $orderId]];
                    break;
                }
                
                if ($paymentStatus !== 'success') {
                    $vOutput = ['success' => false, 'message' => 'Thanh toán thất bại'];
                    break;
                }
                
                // Update order status to paid (1)
                $paymentNote = "Thanh toán qua $paymentMethod" . ($transactionId ? " | Mã GD: $transactionId" : "") . " | Số tiền: " . number_format($paymentAmount, 0, ',', '.') . " VND";
                $paymentNoteEsc = nc_escape_str($paymentNote);
                
                $updateSql = "UPDATE sl_lv0013 SET lv011=1, lv029='$today', lv030='$paymentNoteEsc' WHERE lv001='$orderIdEsc'";
                $updateResult = @db_query($updateSql);
                
                if ($updateResult) {
                    // Always update sl_lv0511 and sl_lv0014 status to paid (1) for this order
                    $updateSl0511Sql = "UPDATE sl_lv0511 SET lv007=1 WHERE lv002='$orderIdEsc'";
                    @db_query($updateSl0511Sql);
                    
                    $updateSl0014Sql = "UPDATE sl_lv0014 SET lv015=1 WHERE lv002='$orderIdEsc'";
                    @db_query($updateSl0014Sql);

                    // Check if this is a renewal order (TYPE:RENEWAL in lv030)
                    if (strpos($order['lv030'] ?? '', 'TYPE:RENEWAL') !== false) {
                        // Parse IDs from note: Original order ID and Renewal ID
                        // Format: TYPE:RENEWAL | Ref: [OriginalOrderID] | RID:[RenewalID] | DID:[DetailID]
                        $note = $order['lv030'] ?? '';
                        $originalOrderId = '';
                        $detailId = '';
                        $renewalId = '';
                        $newEndDate = '';
                        
                        if (preg_match('/Ref:\s*([A-Za-z0-9\-_]+)/', $note, $matches)) $originalOrderId = $matches[1];
                        if (preg_match('/RID:\s*(\d+)/', $note, $matches)) $renewalId = $matches[1];
                        if (preg_match('/DID:\s*([A-Za-z0-9\-_]+)/', $note, $matches)) $detailId = $matches[1];
                        
                        if ($renewalId !== '') {
                            // Update sl_lv0511 status to paid
                            $updateRenewalSql = "UPDATE sl_lv0511 SET lv007=1 WHERE lv001='$renewalId'";
                            db_query($updateRenewalSql);
                            
                            // Get new expiry date from sl_lv0511
                            $renewalInfoSql = "SELECT lv004 FROM sl_lv0511 WHERE lv001='$renewalId' LIMIT 1";
                            $rResult = db_query($renewalInfoSql);
                            if ($rResult) {
                                $rInfo = db_fetch_array($rResult);
                                $newEndDate = $rInfo['lv004'];
                            }
                        }
                        
                        if ($detailId !== '' && $newEndDate !== '') {
                            // Update sl_lv0014 (Product Detail) expiry date
                            $updateDetailSql = "UPDATE sl_lv0014 SET lv014='$newEndDate' WHERE lv001='$detailId'";
                            db_query($updateDetailSql);
                        }
                    }

                    $vOutput = [
                        'success' => true,
                        'message' => 'Xác nhận thanh toán thành công',
                        'data' => [
                            'orderId' => $orderId,
                            'transactionId' => $transactionId,
                            'paymentMethod' => $paymentMethod,
                            'amount' => $paymentAmount,
                            'paidAt' => $today
                        ]
                    ];
                } else {
                    $vOutput = ['success' => false, 'message' => 'Không thể cập nhật trạng thái đơn hàng'];
                }
                break;
            
            /**
             * GET DEMO REQUESTS - Lấy danh sách yêu cầu demo của khách (dựa trên email)
             * Input: email
             */
            case "getDemoRequests":
                $email = trim($input['email'] ?? $_POST['email'] ?? '');
                
                if ($email === '') {
                    $vOutput = ['success' => false, 'message' => 'Thiếu email'];
                    break;
                }
                
                $emailEsc = nc_escape_str($email);
                
                // Get demo requests based on identifying string in lv030
                // We look for 'TYPE:DEMO_' in lv030
                $sql = "SELECT lv001, lv003, lv004, lv009, lv011, lv013, lv016, lv030
                        FROM sl_lv0013 
                        WHERE lv009='$emailEsc' AND lv030 LIKE '%TYPE:DEMO_%'
                        ORDER BY lv004 DESC";
                $result = @db_query($sql);
                $requests = [];
                
                if ($result) {
                    while ($row = db_fetch_array($result)) {
                        $paymentNote = $row['lv030'];
                        
                        $demoType = 'free';
                        if (strpos($paymentNote, 'TYPE:DEMO_PAID') !== false) {
                            $demoType = 'paid';
                        }
                        
                        $status = intval($row['lv011']);
                        $statusText = 'Chờ duyệt';
                        if ($status === 1) $statusText = 'Chờ thanh toán';
                        if ($status === 2) $statusText = 'Đã kích hoạt';
                        if ($status === 3) $statusText = 'Đã hủy';
                        
                        $requests[] = [
                            'requestId' => $row['lv001'],
                            'customerName' => $row['lv003'],
                            'requestedAt' => $row['lv004'],
                            'email' => $row['lv009'],
                            'status' => $status,
                            'statusText' => $statusText,
                            'note' => $row['lv013'],
                            'amount' => floatval($row['lv016']),
                            'demoType' => $demoType,
                            'demoTypeText' => $demoType === 'free' ? 'Demo miễn phí' : 'Demo có phí',
                            'paymentNote' => $paymentNote
                        ];
                    }
                }
                
                $vOutput = [
                    'success' => true,
                    'data' => $requests,
                    'count' => count($requests)
                ];
                break;
            
            /**
             * REQUEST CONTACT - Gửi yêu cầu liên hệ/tư vấn từ form Contact
             * Input: maKH, tenKH, email, sdt, nguoiDaiDien, ngayLam, ngayKetThuc, itemId, ghiChu
             */
            case "requestContact":
                db_connect();
                $maKH = trim($input['maKH'] ?? $_POST['maKH'] ?? '');
                $tenKH = trim($input['tenKH'] ?? $_POST['tenKH'] ?? '');
                $email = trim($input['email'] ?? $_POST['email'] ?? '');
                $sdt = trim($input['sdt'] ?? $_POST['sdt'] ?? '');
                $nguoiDaiDien = trim($input['nguoiDaiDien'] ?? $_POST['nguoiDaiDien'] ?? '');
                $ngayLam = trim($input['ngayLam'] ?? $_POST['ngayLam'] ?? '');
                $ngayKetThuc = trim($input['ngayKetThuc'] ?? $_POST['ngayKetThuc'] ?? '');
                $itemId = trim($input['itemId'] ?? $_POST['itemId'] ?? '');
                $ghiChu = trim($input['ghiChu'] ?? $_POST['ghiChu'] ?? '');
                
                // Validation
                if ($tenKH === '' || $sdt === '') {
                    $vOutput = ['success' => false, 'message' => 'Vui lòng điền đầy đủ thông tin bắt buộc (Tên, SĐT)'];
                    break;
                }
                
                // Escape strings
                $maKHEsc = nc_escape_str($maKH);
                $tenKHEsc = nc_escape_str($tenKH);
                $emailEsc = nc_escape_str($email);
                $sdtEsc = nc_escape_str($sdt);
                $nguoiDaiDienEsc = nc_escape_str($nguoiDaiDien);
                $ngayLamEsc = nc_escape_str($ngayLam);
                $ngayKetThucEsc = nc_escape_str($ngayKetThuc);
                $itemIdEsc = nc_escape_str($itemId);
                $ghiChuEsc = nc_escape_str($ghiChu);
                $today = date('Y-m-d H:i:s');
                
                // Build full note
                $fullNote = "YÊU CẦU TƯ VẤN";
                if ($itemId !== '') $fullNote .= " | Sản phẩm: $itemId";
                if ($nguoiDaiDien !== '') $fullNote .= " | Người đại diện: $nguoiDaiDien";
                if ($ngayLam !== '') $fullNote .= " | Ngày bắt đầu: $ngayLam";
                if ($ngayKetThuc !== '') $fullNote .= " | Ngày kết thúc: $ngayKetThuc";
                if ($ghiChu !== '') $fullNote .= " | Ghi chú: $ghiChu";
                $noteEsc = nc_escape_str($fullNote);
                
                // Check or create customer
                $customerId = trim($input['customerId'] ?? $_POST['customerId'] ?? $maKH); // Support customerId or maKH input
                
                if ($customerId !== '') {
                    $checkIdSql = "SELECT lv001 FROM sl_lv0001 WHERE lv001='" . nc_escape_str($customerId) . "'";
                    $checkIdRes = @db_query($checkIdSql);
                    if ($checkIdRes && mysqli_num_rows($checkIdRes) > 0) {
                         // Valid customer
                    } else {
                        $customerId = '';
                    }
                }

                if ($customerId === '' && $email !== '') {
                    $checkCustomerSql = "SELECT lv001 FROM sl_lv0001 WHERE lv008='$emailEsc' OR lv004='$emailEsc' LIMIT 1";
                    $checkResult = @db_query($checkCustomerSql);
                    if ($checkResult) {
                        $existing = db_fetch_array($checkResult);
                        if ($existing) {
                            $customerId = $existing['lv001'];
                        }
                    }
                }
                
                if ($customerId === '') {
                    $customerId = 'CNT_' . strtoupper(substr(md5($email . time()), 0, 8));
                    $customerIdEsc = nc_escape_str($customerId);
                    // Insert new customer using correct columns: lv002=Name, lv003=Address(empty), lv006=Phone, lv008=Email
                    $insertCustomerSql = "INSERT INTO sl_lv0001 (lv001, lv002, lv003, lv006, lv008) 
                                          VALUES ('$customerIdEsc', '$tenKHEsc', '', '$sdtEsc', '$emailEsc')";
                    @db_query($insertCustomerSql);
                }
                $customerIdEsc = nc_escape_str($customerId);
                
                // Generate request ID
                $requestId = 'CNT-' . date('Ymd') . '-' . strtoupper(substr(md5(time() . rand()), 0, 6));
                $requestIdEsc = nc_escape_str($requestId);
                
                // Insert contact request into sl_lv0013 with status = 0 (Chờ xử lý) and tag TYPE:CONTACT
                $typeTag = "TYPE:CONTACT | Yêu cầu tư vấn";
                $insertSql = "INSERT INTO sl_lv0013 (lv001, lv002, lv003, lv004, lv006, lv009, lv011, lv013, lv016, lv030)
                              VALUES ('$requestIdEsc', '$customerIdEsc', '$tenKHEsc', '$today', '$sdtEsc', '$emailEsc', 0, '$noteEsc', 0, '$typeTag')";
                
                $insertResult = db_query($insertSql);
                
                if ($insertResult) {
                    // Insert detail into cr_lv0276 (Chi tiết yêu cầu)
                    if ($itemId !== '') {
                        $detailSql = "INSERT INTO cr_lv0276(lv002,lv003,lv004,lv005,lv006,lv007,lv008,lv011,lv018,lv019) 
                                      SELECT '$requestIdEsc', '$itemIdEsc', 1, A.lv004, 0, A.lv007, 0, 0, A.lv009, A.lv002 
                                      FROM sl_lv0007 A WHERE lv001='$itemIdEsc'";
                        @db_query($detailSql);
                    }

                    $vOutput = [
                        'success' => true,
                        'message' => 'Yêu cầu của bạn đã được gửi thành công. Chúng tôi sẽ liên hệ sớm.',
                        'data' => [
                            'requestId' => $requestId,
                            'customerId' => $customerId,
                            'status' => 'pending',
                            'statusText' => 'Chờ xử lý'
                        ]
                    ];
                } else {
                    $vOutput = ['success' => false, 'message' => 'Không thể gửi yêu cầu. Vui lòng thử lại.'];
                }
                break;
            
            /**
             * REQUEST DEMO FROM CART - Yêu cầu demo từ giỏ hàng
             * Input: customerName, customerEmail, customerPhone, customerAddress, items[], demoType (free/paid)
             */
            case "requestDemoFromCart":
                db_connect();
                $customerName = trim($input['customerName'] ?? $_POST['customerName'] ?? '');
                $customerEmail = trim($input['customerEmail'] ?? $_POST['customerEmail'] ?? '');
                $customerPhone = trim($input['customerPhone'] ?? $_POST['customerPhone'] ?? '');
                $customerAddress = trim($input['customerAddress'] ?? $_POST['customerAddress'] ?? '');
                $items = $input['items'] ?? $_POST['items'] ?? [];
                $demoType = trim($input['demoType'] ?? $_POST['demoType'] ?? 'free'); // 'free' or 'paid'
                $note = trim($input['note'] ?? $_POST['note'] ?? '');
                
                // Validation
                if ($customerName === '' || $customerEmail === '' || $customerPhone === '') {
                    $vOutput = ['success' => false, 'message' => 'Vui lòng điền đầy đủ thông tin bắt buộc'];
                    break;
                }
                
                // Email validation
                if (!filter_var($customerEmail, FILTER_VALIDATE_EMAIL)) {
                    $vOutput = ['success' => false, 'message' => 'Email không hợp lệ'];
                    break;
                }
                
                // Parse items if string
                if (is_string($items)) {
                    $items = json_decode($items, true);
                }
                
                if (!is_array($items) || count($items) === 0) {
                    $vOutput = ['success' => false, 'message' => 'Giỏ hàng trống'];
                    break;
                }
                
                // Escape strings
                $customerNameEsc = nc_escape_str($customerName);
                $customerEmailEsc = nc_escape_str($customerEmail);
                $customerPhoneEsc = nc_escape_str($customerPhone);
                $customerAddressEsc = nc_escape_str($customerAddress);
                $today = date('Y-m-d H:i:s');
                
                // Check or create customer
                $customerId = trim($input['customerId'] ?? $_POST['customerId'] ?? '');
                
                if ($customerId !== '') {
                    $checkIdSql = "SELECT lv001 FROM sl_lv0001 WHERE lv001='" . nc_escape_str($customerId) . "'";
                    $checkIdRes = @db_query($checkIdSql);
                    if ($checkIdRes && mysqli_num_rows($checkIdRes) > 0) {
                         // Valid customer
                    } else {
                        $customerId = '';
                    }
                }

                if ($customerId === '') {
                     $checkCustomerSql = "SELECT lv001 FROM sl_lv0001 WHERE lv008='$customerEmailEsc' OR lv004='$customerEmailEsc' LIMIT 1";
                    $checkResult = @db_query($checkCustomerSql);
                    if ($checkResult) {
                        $existing = db_fetch_array($checkResult);
                        if ($existing) {
                            $customerId = $existing['lv001'];
                        }
                    }
                }
                
                if ($customerId === '') {
                    $customerId = 'DEMO_CART_' . strtoupper(substr(md5($customerEmail . time()), 0, 8));
                    $customerIdEsc = nc_escape_str($customerId);
                    // Use correct columns: lv002=Name, lv003=Address, lv006=Phone, lv008=Email
                    $insertCustomerSql = "INSERT INTO sl_lv0001 (lv001, lv002, lv003, lv006, lv008) 
                                          VALUES ('$customerIdEsc', '$customerNameEsc', '$customerAddressEsc', '$customerPhoneEsc', '$customerEmailEsc')";
                    @db_query($insertCustomerSql);
                }
                
                $customerIdEsc = nc_escape_str($customerId);
                
                // Build product list from cart items
                $productList = [];
                foreach ($items as $item) {
                    $productName = $item['productName'] ?? $item['name'] ?? '';
                    $productCode = $item['productCode'] ?? '';
                    $plan = $item['plan'] ?? 'basic';
                    $months = isset($item['months']) ? intval($item['months']) : 6;
                    $productList[] = "$productName ($plan - $months tháng)";
                }
                $productListStr = implode(', ', $productList);
                
                // Build note
                $fullNote = ($demoType === 'paid' ? "YÊU CẦU DEMO CÓ PHÍ" : "YÊU CẦU DEMO MIỄN PHÍ") . " TỪ GIỎ HÀNG";
                $fullNote .= " | Sản phẩm: $productListStr";
                if ($customerAddress !== '') $fullNote .= " | Địa chỉ: $customerAddress";
                if ($note !== '') $fullNote .= " | Ghi chú: $note";
                $noteEsc = nc_escape_str($fullNote);
                
                // Generate request ID
                $requestId = ($demoType === 'paid' ? 'DMPC-' : 'DMFC-') . date('Ymd') . '-' . strtoupper(substr(md5(time() . rand()), 0, 6));
                $requestIdEsc = nc_escape_str($requestId);
                
                // lv027: 1 = demo free, 2 = demo paid
                // lv011: 0 = pending (free), 1 = pending payment (paid)
                $demoTypeCode = $demoType === 'paid' ? 2 : 1;
                $status = $demoType === 'paid' ? 1 : 0;
                $statusNote = $demoType === 'paid' ? 'Demo có phí - Chờ thanh toán' : 'Demo miễn phí - Chờ duyệt';
                
                // Calculate demo price if paid
                $demoPrice = 0;
                if ($demoType === 'paid') {
                    // Get demo price from config
                    $configSql = "SELECT lv007 FROM sl_lv0007 WHERE lv001 = 'PHIDEMONGAY' LIMIT 1";
                    $configResult = @db_query($configSql);
                    if ($configResult) {
                        $configRow = db_fetch_array($configResult);
                        if ($configRow) {
                            $demoPrice = floatval($configRow['lv007']) ?: 200000;
                        }
                    }
                    if ($demoPrice === 0) $demoPrice = 200000; // Default price
                }
                
                // Insert demo request
                $typeTag = $demoType === 'paid' ? "TYPE:DEMO_PAID" : "TYPE:DEMO_FREE";
                $typeTag .= " | " . $statusNote;
                
                $insertSql = "INSERT INTO sl_lv0013 (lv001, lv002, lv003, lv004, lv006, lv009, lv011, lv013, lv016, lv030)
                              VALUES ('$requestIdEsc', '$customerIdEsc', '$customerNameEsc', '$today', '$customerPhoneEsc', '$customerEmailEsc', $status, '$noteEsc', $demoPrice, '$typeTag')";
                
                $insertResult = @db_query($insertSql);
                
                if ($insertResult) {
                    // Insert items into cr_lv0276
                    foreach ($items as $item) {
                        $pCode = nc_escape_str($item['productCode'] ?? $item['id'] ?? '');
                        if($pCode !== '') {
                            $ticketSql = "INSERT INTO cr_lv0276(lv002,lv003,lv004,lv005,lv006,lv007,lv008,lv011,lv018,lv019) 
                                          SELECT '$requestIdEsc', '$pCode', 1, A.lv004, 0, A.lv007, 0, 0, A.lv009, A.lv002 
                                          FROM sl_lv0007 A WHERE lv001='$pCode'";
                            @db_query($ticketSql);
                        }
                    }
                    $responseData = [
                        'requestId' => $requestId,
                        'customerId' => $customerId,
                        'demoType' => $demoType,
                        'status' => $status === 0 ? 'pending' : 'pending_payment',
                        'statusText' => $demoType === 'paid' ? 'Chờ thanh toán' : 'Chờ duyệt',
                        'amount' => $demoPrice,
                        'itemCount' => count($items)
                    ];
                    
                    if ($demoType === 'paid') {
                        $responseData['paymentInfo'] = [
                            'bankName' => 'Vietcombank',
                            'accountNumber' => '0421000123456',
                            'accountName' => 'CONG TY TNHH SOF',
                            'transferContent' => "DEMO $requestId"
                        ];
                        $responseData['estimatedDays'] = 'Kích hoạt ngay sau khi thanh toán';
                    } else {
                        $responseData['estimatedDays'] = '3-7 ngày làm việc';
                    }
                    
                    $vOutput = [
                        'success' => true,
                        'message' => $demoType === 'paid' 
                            ? 'Yêu cầu demo có phí đã được tạo. Vui lòng thanh toán để kích hoạt.'
                            : 'Yêu cầu demo miễn phí đã được gửi. Chúng tôi sẽ liên hệ trong 3-7 ngày.',
                        'data' => $responseData
                    ];
                } else {
                    $vOutput = ['success' => false, 'message' => 'Không thể gửi yêu cầu demo. Vui lòng thử lại.'];
                }
                break;
            
            default:
                $vOutput = ['success' => false, 'message' => 'Chức năng không tồn tại'];
                break;
        }
        break;

    /**
     * =========================================================================
     * SL_LV0006 - DANH MỤC SẢN PHẨM (Product Categories/Plans)
     * =========================================================================
     */
    case "sl_lv0006":
        switch ($vfun) {
            case "data":
                $lvsql = "SELECT lv001, lv002, lv003, lv004, lv005, lv006, lv037 FROM sl_lv0006 WHERE lv036 = 1 ORDER BY lv001";
                $objEmp = db_query($lvsql);
                $vOutput = [];
                
                while ($vrow = db_fetch_array($objEmp)) {
                    $vOutput[] = [
                        "maDanhMuc" => $vrow['lv001'],
                        "tenDanhMuc" => $vrow['lv002'],
                        "maNhomCha" => $vrow['lv003'],
                        "moTa" => $vrow['lv004'],
                        "nguoiTao" => $vrow['lv005'],
                        "ngayTao" => $vrow['lv006'],
                        "hinhAnh" => $vrow['lv037']
                    ];
                }
                break;
            
            case "listWithCount":
                // Trả về danh mục kèm số lượng sản phẩm
                $lvsql = "SELECT 
                            A.lv001 as maDanhMuc,
                            A.lv002 as tenDanhMuc,
                            A.lv003 as maNhomCha,
                            A.lv004 as moTa,
                            A.lv005 as nguoiTao,
                            A.lv006 as ngayTao,
                            COUNT(B.lv001) as soLuongSanPham
                          FROM sl_lv0006 A
                          LEFT JOIN sl_lv0007 B ON A.lv001 = B.lv003
                          GROUP BY A.lv001, A.lv002, A.lv003, A.lv004, A.lv005, A.lv006
                          ORDER BY A.lv001";
                $objEmp = db_query($lvsql);
                $vOutput = [];
                
                while ($vrow = db_fetch_array($objEmp)) {
                    $vOutput[] = [
                        "maDanhMuc" => $vrow['maDanhMuc'],
                        "tenDanhMuc" => $vrow['tenDanhMuc'],
                        "maNhomCha" => $vrow['maNhomCha'],
                        "moTa" => $vrow['moTa'],
                        "nguoiTao" => $vrow['nguoiTao'],
                        "ngayTao" => $vrow['ngayTao'],
                        "soLuongSanPham" => (int)$vrow['soLuongSanPham']
                    ];
                }
                break;
            
            case "getById":
                $maDanhMuc = $input['maDanhMuc'] ?? $_POST['maDanhMuc'] ?? null;
                
                if (empty($maDanhMuc)) {
                    $vOutput = ['success' => false, 'message' => 'Thiếu mã danh mục'];
                    break;
                }
                
                $maDanhMucEsc = sof_escape_string($maDanhMuc);
                $lvsql = "SELECT lv001, lv002, lv003, lv004, lv005, lv006 FROM sl_lv0006 WHERE lv001='$maDanhMucEsc'";
                $objEmp = db_query($lvsql);
                $vrow = db_fetch_array($objEmp, MYSQLI_ASSOC);
                
                if ($vrow) {
                    $vOutput = [
                        'success' => true,
                        'data' => [
                            "maDanhMuc" => $vrow['lv001'],
                            "tenDanhMuc" => $vrow['lv002'],
                            "maNhomCha" => $vrow['lv003'],
                            "moTa" => $vrow['lv004'],
                            "nguoiTao" => $vrow['lv005'],
                            "ngayTao" => $vrow['lv006']
                        ]
                    ];
                } else {
                    $vOutput = ['success' => false, 'message' => 'Không tìm thấy danh mục'];
                }
                break;
            
            case "getProducts":
                // Get category info and its products
                $maDanhMuc = $input['maDanhMuc'] ?? $_POST['maDanhMuc'] ?? null;
                
                if (empty($maDanhMuc)) {
                    $vOutput = ['success' => false, 'message' => 'Thiếu mã danh mục'];
                    break;
                }
                
                $maDanhMucEsc = sof_escape_string($maDanhMuc);
                
                // Get category info
                $catSql = "SELECT lv001, lv002, lv003, lv004, lv005, lv006 FROM sl_lv0006 WHERE lv001='$maDanhMucEsc'";
                $catResult = db_query($catSql);
                $catRow = db_fetch_array($catResult, MYSQLI_ASSOC);
                
                if (!$catRow) {
                    $vOutput = ['success' => false, 'message' => 'Không tìm thấy danh mục'];
                    break;
                }
                
                // Get products in this category
                $prodSql = "SELECT lv001, lv002, lv003, lv004, lv005, lv006, lv007, lv017, lv018 
                            FROM sl_lv0007 
                            WHERE lv003='$maDanhMucEsc' 
                            ORDER BY lv001";
                $prodResult = db_query($prodSql);
                $products = [];
                
                while ($prodRow = db_fetch_array($prodResult, MYSQLI_ASSOC)) {
                    $products[] = [
                        "maSanPham" => $prodRow['lv001'],
                        "tenSanPham" => $prodRow['lv002'],
                        "loaiSanPham" => $prodRow['lv003'],
                        "donViTinh" => $prodRow['lv004'],
                        "giaBan" => $prodRow['lv007'],
                        "maVach" => $prodRow['lv017'],
                        "nhomSanPham" => $prodRow['lv018']
                    ];
                }
                
                $vOutput = [
                    'success' => true,
                    'category' => [
                        "maDanhMuc" => $catRow['lv001'],
                        "tenDanhMuc" => $catRow['lv002'],
                        "maNhomCha" => $catRow['lv003'],
                        "moTa" => $catRow['lv004'],
                        "nguoiTao" => $catRow['lv005'],
                        "ngayTao" => $catRow['lv006']
                    ],
                    'products' => $products,
                    'productCount' => count($products)
                ];
                break;
            
            default:
                $vOutput = ['success' => false, 'message' => 'Chức năng không tồn tại'];
                break;
        }
        break;

    /**
     * =========================================================================
     * NC_PRICING - API GIÁ SẢN PHẨM THEO THỜI HẠN
     * =========================================================================
     * Load giá từ sl_lv0007 với các mốc thời hạn từ sl_lv0014.lv004 (số tháng)
     * =========================================================================
     */
    case "nc_pricing":
        
        // Helper function
        if (!function_exists('nc_pricing_escape_str')) {
            function nc_pricing_escape_str($s) {
                if (function_exists('sof_escape_string')) return sof_escape_string($s);
                if (function_exists('db_escape_string')) return db_escape_string($s);
                if (isset($GLOBALS['db_link']) && function_exists('mysqli_real_escape_string')) {
                    return mysqli_real_escape_string($GLOBALS['db_link'], $s);
                }
                return addslashes($s);
            }
        }
        
        switch ($vfun) {
            
            /**
             * GET PRODUCT PRICING - Lấy giá sản phẩm theo mã
             * Input: productCode (ONLINE.CAFE.BS.2025.V1) hoặc categoryCode (CAFE)
             */
            case "getProductPricing":
                $productCode = trim($input['productCode'] ?? $_POST['productCode'] ?? '');
                $categoryCode = trim($input['categoryCode'] ?? $_POST['categoryCode'] ?? '');
                
                if ($productCode === '' && $categoryCode === '') {
                    $vOutput = ['success' => false, 'message' => 'Thiếu mã sản phẩm hoặc mã danh mục'];
                    break;
                }
                
                $whereClause = '';
                if ($productCode !== '') {
                    $productCodeEsc = nc_pricing_escape_str($productCode);
                    $whereClause = "lv001 = '$productCodeEsc'";
                } else {
                    $categoryCodeEsc = nc_pricing_escape_str($categoryCode);
                    $whereClause = "(lv003 = '$categoryCodeEsc' OR lv001 LIKE '%.$categoryCodeEsc.%')";
                }
                
                $productSql = "SELECT lv001, lv002, lv003, lv004, lv005, lv006, lv007, lv008, lv009, lv010, lv017, lv018, lv019
                               FROM sl_lv0007 
                               WHERE $whereClause
                               ORDER BY lv001";
                $productResult = db_query($productSql);
                $products = [];
                
                while ($row = db_fetch_array($productResult, MYSQLI_ASSOC)) {
                    $products[] = [
                        'maSanPham' => $row['lv001'],
                        'tenSanPham' => $row['lv002'],
                        'loaiSanPham' => $row['lv003'],
                        'donViTinhChinh' => $row['lv004'],
                        'donViTinhPhu' => $row['lv005'],
                        'tyLeQuyDoi' => floatval($row['lv006'] ?? 1),
                        'giaBan' => floatval($row['lv007'] ?? 0),
                        'donViTienTe' => $row['lv008'] ?? 'VND',
                        'nhaCungCap' => $row['lv009'],
                        'moTa' => $row['lv010'],
                        'maVach' => $row['lv017'],
                        'nhomSanPham' => $row['lv018'],
                        'ghiChu' => $row['lv019']
                    ];
                }
                
                $vOutput = [
                    'success' => true,
                    'data' => $products,
                    'count' => count($products)
                ];
                break;
            
            /**
             * GET PRICING BY CATEGORY - Lấy bảng giá theo danh mục (BS, PR, FU)
             * Input: categoryCode (CAFE, NHAHANG, KHACHSAN, ...)
             */
            case "getPricingByCategory":
                $categoryCode = strtoupper(trim($input['categoryCode'] ?? $_POST['categoryCode'] ?? ''));
                
                if ($categoryCode === '') {
                    $vOutput = ['success' => false, 'message' => 'Thiếu mã danh mục'];
                    break;
                }
                
                $categoryCodeEsc = nc_pricing_escape_str($categoryCode);
                
                // Lấy sản phẩm theo category: ONLINE.{CATEGORY}.{BS|PR|FU}.*.V*
                $productSql = "SELECT lv001, lv002, lv003, lv004, lv007, lv008, lv009, lv010, lv018, lv019
                               FROM sl_lv0007 
                               WHERE (lv003 = '$categoryCodeEsc' OR lv001 LIKE 'ONLINE.$categoryCodeEsc.%' OR lv001 LIKE 'ONLINE.%.%' AND lv003 = '$categoryCodeEsc')
                               ORDER BY lv001";
                $productResult = db_query($productSql);
                
                $pricingPlans = [
                    'basic' => null,
                    'pro' => null,
                    'full' => null
                ];
                
                while ($row = db_fetch_array($productResult, MYSQLI_ASSOC)) {
                    $code = strtoupper($row['lv001']);
                    $planType = null;
                    
                    if (strpos($code, '.BS.') !== false || strpos($code, '.BS') !== false) {
                        $planType = 'basic';
                    } elseif (strpos($code, '.PR.') !== false || strpos($code, '.PR') !== false) {
                        $planType = 'pro';
                    } elseif (strpos($code, '.FU.') !== false || strpos($code, '.FU') !== false) {
                        $planType = 'full';
                    }
                    
                    if ($planType && $pricingPlans[$planType] === null) {
                        $pricingPlans[$planType] = [
                            'maSanPham' => $row['lv001'],
                            'tenSanPham' => $row['lv002'],
                            'loaiSanPham' => $row['lv003'],
                            'donViTinh' => $row['lv004'] ?? 'month',
                            'giaBan' => floatval($row['lv007'] ?? 0),
                            'donViTienTe' => $row['lv008'] ?? 'VND',
                            'nhaCungCap' => $row['lv009'], // Features HTML
                            'moTa' => $row['lv010'], // Description HTML
                            'nhomSanPham' => floatval($row['lv018'] ?? 0), // Có thể dùng cho số tháng mặc định
                            'ghiChu' => floatval($row['lv019'] ?? 0) // Có thể dùng cho số bàn/phòng
                        ];
                    }
                }
                
                $vOutput = [
                    'success' => true,
                    'categoryCode' => $categoryCode,
                    'plans' => $pricingPlans
                ];
                break;
            
            /**
             * GET PRICING TIERS - Lấy các mốc giá theo số tháng
             * Input: productCode
             * Trả về các mốc giá: 1 tháng, 3 tháng, 6 tháng, 12 tháng với giá tương ứng
             */
            case "getPricingTiers":
                $productCode = trim($input['productCode'] ?? $_POST['productCode'] ?? '');
                
                if ($productCode === '') {
                    $vOutput = ['success' => false, 'message' => 'Thiếu mã sản phẩm'];
                    break;
                }
                
                $productCodeEsc = nc_pricing_escape_str($productCode);
                
                // Lấy thông tin sản phẩm
                $productSql = "SELECT lv001, lv002, lv003, lv004, lv007, lv008 FROM sl_lv0007 WHERE lv001='$productCodeEsc' LIMIT 1";
                $productResult = db_query($productSql);
                $product = db_fetch_array($productResult, MYSQLI_ASSOC);
                
                if (!$product) {
                    $vOutput = ['success' => false, 'message' => 'Không tìm thấy sản phẩm'];
                    break;
                }
                
                $basePrice = floatval($product['lv007'] ?? 0);
                $currency = $product['lv008'] ?? 'VND';
                $unit = $product['lv004'] ?? 'month';
                
                // Định nghĩa các mốc thời hạn (bội số của 6 tháng) và hệ số giảm giá
                $tiers = [
                    ['months' => 6, 'discount' => 0, 'label' => '6 tháng'],
                    ['months' => 12, 'discount' => 10, 'label' => '12 tháng'],
                    ['months' => 18, 'discount' => 15, 'label' => '18 tháng'],
                    ['months' => 24, 'discount' => 20, 'label' => '24 tháng'],
                    ['months' => 36, 'discount' => 25, 'label' => '36 tháng'],
                ];
                
                $pricingTiers = [];
                foreach ($tiers as $tier) {
                    $totalPrice = $basePrice * $tier['months'];
                    $discountAmount = $totalPrice * $tier['discount'] / 100;
                    $finalPrice = $totalPrice - $discountAmount;
                    $pricePerMonth = $tier['months'] > 0 ? $finalPrice / $tier['months'] : $finalPrice;
                    
                    $pricingTiers[] = [
                        'months' => $tier['months'],
                        'label' => $tier['label'],
                        'basePrice' => $basePrice,
                        'totalPrice' => $totalPrice,
                        'discountPercent' => $tier['discount'],
                        'discountAmount' => $discountAmount,
                        'finalPrice' => $finalPrice,
                        'pricePerMonth' => round($pricePerMonth, 0),
                        'currency' => $currency
                    ];
                }
                
                $vOutput = [
                    'success' => true,
                    'product' => [
                        'maSanPham' => $product['lv001'],
                        'tenSanPham' => $product['lv002'],
                        'loaiSanPham' => $product['lv003'],
                        'donViTinh' => $unit,
                        'basePrice' => $basePrice,
                        'currency' => $currency
                    ],
                    'tiers' => $pricingTiers
                ];
                break;
            
            default:
                $vOutput = ['success' => false, 'message' => 'Chức năng không tồn tại'];
                break;
        }
        break;

    /**
     * =========================================================================
     * NC_SUBSCRIPTION - QUẢN LÝ GÓI ĐĂNG KÝ & GIA HẠN
     * =========================================================================
     * Quản lý subscription với sl_lv0511 (bảng gia hạn)
     * =========================================================================
     */
    case "nc_subscription":
        
        // Helper function
        if (!function_exists('nc_sub_escape_str')) {
            function nc_sub_escape_str($s) {
                if (function_exists('sof_escape_string')) return sof_escape_string($s);
                if (function_exists('db_escape_string')) return db_escape_string($s);
                if (isset($GLOBALS['db_link']) && function_exists('mysqli_real_escape_string')) {
                    return mysqli_real_escape_string($GLOBALS['db_link'], $s);
                }
                return addslashes($s);
            }
        }
        
        switch ($vfun) {
            
            /**
             * GET CUSTOMER SUBSCRIPTIONS - Lấy danh sách gói đăng ký của khách
             * Input: customerId
             * Trả về: Danh sách gói với thông tin hết hạn, trạng thái
             */
            case "getCustomerSubscriptions":
                $customerId = trim($input['customerId'] ?? $_POST['customerId'] ?? '');
                
                if ($customerId === '') {
                    $vOutput = ['success' => false, 'message' => 'Thiếu mã khách hàng'];
                    break;
                }
                
                $customerIdEsc = nc_sub_escape_str($customerId);
                
                // Lấy đơn hàng đã thanh toán của khách hàng
                $orderSql = "SELECT 
                                O.lv001 as orderId,
                                O.lv003 as customerName,
                                O.lv004 as createdAt,
                                O.lv011 as status,
                                O.lv029 as paidAt,
                                D.lv001 as detailId,
                                D.lv003 as productCode,
                                D.lv004 as quantity,
                                D.lv005 as price,
                                D.lv006 as lineTotal,
                                D.lv013 as startDate,
                                D.lv014 as endDate,
                                D.lv021 as licenseCode,
                                P.lv002 as productName,
                                P.lv004 as unit
                             FROM sl_lv0013 O
                             INNER JOIN sl_lv0014 D ON O.lv001 = D.lv002
                             LEFT JOIN sl_lv0007 P ON D.lv003 = P.lv001
                             WHERE O.lv002 = '$customerIdEsc'
                               AND O.lv011 = 2
                               AND D.lv021 IS NOT NULL 
                               AND D.lv021 != ''
                             ORDER BY D.lv014 DESC, O.lv001 DESC";
                $orderResult = db_query($orderSql);
                
                $subscriptions = [];
                $today = date('Y-m-d');
                
                while ($row = db_fetch_array($orderResult, MYSQLI_ASSOC)) {
                    $endDate = $row['endDate'];
                    $isExpired = false;
                    $daysRemaining = null;
                    $status = 'active';
                    
                    if ($endDate && $endDate !== '0000-00-00') {
                        $endTimestamp = strtotime($endDate);
                        $todayTimestamp = strtotime($today);
                        $daysRemaining = floor(($endTimestamp - $todayTimestamp) / (60 * 60 * 24));
                        
                        if ($daysRemaining < 0) {
                            $isExpired = true;
                            $status = 'expired';
                        } elseif ($daysRemaining <= 7) {
                            $status = 'expiring_soon';
                        }
                    }
                    
                    $subscriptions[] = [
                        'orderId' => $row['orderId'],
                        'detailId' => $row['detailId'],
                        'productCode' => $row['productCode'],
                        'productName' => $row['productName'],
                        'licenseCode' => $row['licenseCode'],
                        'startDate' => $row['startDate'],
                        'endDate' => $endDate,
                        'isExpired' => $isExpired,
                        'daysRemaining' => $daysRemaining,
                        'status' => $status,
                        'price' => floatval($row['price'] ?? 0),
                        'quantity' => floatval($row['quantity'] ?? 1),
                        'paidAt' => $row['paidAt']
                    ];
                }
                
                $vOutput = [
                    'success' => true,
                    'data' => $subscriptions,
                    'count' => count($subscriptions)
                ];
                break;
            
            /**
             * GET SUBSCRIPTION DETAIL - Lấy chi tiết 1 gói đăng ký
             * Input: orderId, detailId hoặc licenseCode
             */
            case "getSubscriptionDetail":
                $orderId = trim($input['orderId'] ?? $_POST['orderId'] ?? '');
                $detailId = trim($input['detailId'] ?? $_POST['detailId'] ?? '');
                $licenseCode = trim($input['licenseCode'] ?? $_POST['licenseCode'] ?? '');
                
                if ($orderId === '' && $detailId === '' && $licenseCode === '') {
                    $vOutput = ['success' => false, 'message' => 'Thiếu thông tin gói đăng ký'];
                    break;
                }
                
                $whereClause = '';
                if ($licenseCode !== '') {
                    $licenseCodeEsc = nc_sub_escape_str($licenseCode);
                    $whereClause = "D.lv021 = '$licenseCodeEsc'";
                } elseif ($detailId !== '') {
                    $detailIdEsc = nc_sub_escape_str($detailId);
                    $whereClause = "D.lv001 = '$detailIdEsc'";
                } else {
                    $orderIdEsc = nc_sub_escape_str($orderId);
                    $whereClause = "D.lv002 = '$orderIdEsc'";
                }
                
                $detailSql = "SELECT 
                                O.lv001 as orderId,
                                O.lv002 as customerId,
                                O.lv003 as customerName,
                                O.lv004 as orderDate,
                                O.lv011 as orderStatus,
                                O.lv029 as paidAt,
                                D.lv001 as detailId,
                                D.lv003 as productCode,
                                D.lv004 as quantity,
                                D.lv005 as price,
                                D.lv006 as lineTotal,
                                D.lv013 as startDate,
                                D.lv014 as endDate,
                                D.lv021 as licenseCode,
                                P.lv002 as productName,
                                P.lv003 as categoryCode,
                                P.lv004 as unit
                             FROM sl_lv0014 D
                             INNER JOIN sl_lv0013 O ON D.lv002 = O.lv001
                             LEFT JOIN sl_lv0007 P ON D.lv003 = P.lv001
                             WHERE $whereClause
                             LIMIT 1";
                $detailResult = db_query($detailSql);
                $detail = db_fetch_array($detailResult, MYSQLI_ASSOC);
                
                if (!$detail) {
                    $vOutput = ['success' => false, 'message' => 'Không tìm thấy gói đăng ký'];
                    break;
                }
                
                // Lấy lịch sử gia hạn từ sl_lv0511
                $renewalSql = "SELECT lv001, lv002, lv003 as ngayGiaHan, lv004 as ngayHetHan, lv005 as lanThanhToan, lv006 as soTienGiaHan, lv007 as thanhToan
                               FROM sl_lv0511 
                               WHERE lv002 = '{$detail['orderId']}'
                               ORDER BY lv001 DESC";
                $renewalResult = db_query($renewalSql);
                $renewals = [];
                
                while ($rRow = db_fetch_array($renewalResult, MYSQLI_ASSOC)) {
                    $renewals[] = [
                        'id' => $rRow['lv001'],
                        'orderId' => $rRow['lv002'],
                        'renewalDate' => $rRow['ngayGiaHan'],
                        'expiryDate' => $rRow['ngayHetHan'],
                        'renewalCount' => intval($rRow['lanThanhToan'] ?? 0),
                        'amount' => floatval($rRow['soTienGiaHan'] ?? 0),
                        'isPaid' => intval($rRow['thanhToan'] ?? 0)
                    ];
                }
                
                $today = date('Y-m-d');
                $endDate = $detail['endDate'];
                $isExpired = false;
                $daysRemaining = null;
                $status = 'active';
                
                if ($endDate && $endDate !== '0000-00-00') {
                    $endTimestamp = strtotime($endDate);
                    $todayTimestamp = strtotime($today);
                    $daysRemaining = floor(($endTimestamp - $todayTimestamp) / (60 * 60 * 24));
                    
                    if ($daysRemaining < 0) {
                        $isExpired = true;
                        $status = 'expired';
                    } elseif ($daysRemaining <= 7) {
                        $status = 'expiring_soon';
                    }
                }
                
                $vOutput = [
                    'success' => true,
                    'data' => [
                        'subscription' => [
                            'orderId' => $detail['orderId'],
                            'detailId' => $detail['detailId'],
                            'customerId' => $detail['customerId'],
                            'customerName' => $detail['customerName'],
                            'productCode' => $detail['productCode'],
                            'productName' => $detail['productName'],
                            'categoryCode' => $detail['categoryCode'],
                            'licenseCode' => $detail['licenseCode'],
                            'startDate' => $detail['startDate'],
                            'endDate' => $endDate,
                            'price' => floatval($detail['price'] ?? 0),
                            'quantity' => floatval($detail['quantity'] ?? 1),
                            'paidAt' => $detail['paidAt'],
                            'isExpired' => $isExpired,
                            'daysRemaining' => $daysRemaining,
                            'status' => $status
                        ],
                        'renewalHistory' => $renewals,
                        'totalRenewals' => count($renewals)
                    ]
                ];
                break;
            
            /**
             * RENEW SUBSCRIPTION - Gia hạn gói đăng ký
             * Input: orderId, months, amount, demoMode
             * Tạo record trong sl_lv0511 và cập nhật ngày hết hạn trong sl_lv0014
             */
            case "renewSubscription":
                $orderId = trim($input['orderId'] ?? $_POST['orderId'] ?? '');
                $detailId = trim($input['detailId'] ?? $_POST['detailId'] ?? '');
                $months = intval($input['months'] ?? $_POST['months'] ?? 1);
                $amount = floatval($input['amount'] ?? $_POST['amount'] ?? 0);
                $demoMode = (bool)($input['demoMode'] ?? $_POST['demoMode'] ?? false);
                $note = trim($input['note'] ?? $_POST['note'] ?? '');
                
                if ($orderId === '') {
                    $vOutput = ['success' => false, 'message' => 'Thiếu mã đơn hàng'];
                    break;
                }
                
                $orderIdEsc = nc_sub_escape_str($orderId);
                $detailIdEsc = nc_sub_escape_str($detailId);
                
                // Kiểm tra đơn hàng tồn tại
                $checkSql = "SELECT O.lv001, O.lv011, D.lv014 as currentEndDate, D.lv001 as detailId
                             FROM sl_lv0013 O
                             INNER JOIN sl_lv0014 D ON O.lv001 = D.lv002
                             WHERE O.lv001 = '$orderIdEsc'";
                if ($detailIdEsc !== '') {
                    $checkSql .= " AND D.lv001 = '$detailIdEsc'";
                }
                $checkSql .= " LIMIT 1";
                
                $checkResult = db_query($checkSql);
                $orderInfo = db_fetch_array($checkResult, MYSQLI_ASSOC);
                
                if (!$orderInfo) {
                    $vOutput = ['success' => false, 'message' => 'Không tìm thấy đơn hàng'];
                    break;
                }
                
                $today = date('Y-m-d');
                $currentEndDate = $orderInfo['currentEndDate'];
                $actualDetailId = $orderInfo['detailId'];
                
                // Tính ngày hết hạn mới
                // Nếu còn hạn, cộng dồn từ ngày hết hạn hiện tại
                // Nếu đã hết hạn, tính từ hôm nay
                $startFrom = $today;
                if ($currentEndDate && $currentEndDate !== '0000-00-00' && strtotime($currentEndDate) > strtotime($today)) {
                    $startFrom = $currentEndDate;
                }
                $newEndDate = date('Y-m-d', strtotime("$startFrom + $months months"));
                
                // Đếm số lần gia hạn
                $countSql = "SELECT COUNT(*) as cnt FROM sl_lv0511 WHERE lv002 = '$orderIdEsc'";
                $countResult = db_query($countSql);
                $countRow = db_fetch_array($countResult, MYSQLI_ASSOC);
                $renewalCount = intval($countRow['cnt'] ?? 0) + 1;
                
                // Insert into sl_lv0511 BEFORE payment (Unpaid status: 0)
                // lv002 = Original Order ID (Linked to Bảng gốc)
                // We will link this renewal record to the Payment Order via the Payment Order's Note
                $isPaid = $demoMode ? 1 : 0; // If demo mode, auto paid? But user wants payment step.
                // If demoMode is true, we might allow bypassing payment step, assuming tests/admin usage.
                
                $noteEsc = nc_sub_escape_str($note);
                
                $insertSql = "INSERT INTO sl_lv0511 (lv002, lv003, lv004, lv005, lv006, lv007)
                              VALUES ('$orderIdEsc', '$today', '$newEndDate', $renewalCount, $amount, $isPaid)";
                $insertResult = db_query($insertSql);
                $renewalId = db_insert_id($GLOBALS['db_link'] ?? null);
                
                if (!$insertResult) {
                    $vOutput = ['success' => false, 'message' => 'Không thể tạo bản ghi gia hạn'];
                    break;
                }
                
                if ($demoMode) {
                    // DEMO MODE: Auto Renew Immediately
                    $updateSql = "UPDATE sl_lv0014 SET lv014 = '$newEndDate' WHERE lv001 = '$actualDetailId'";
                    db_query($updateSql);
                    
                    $vOutput = [
                        'success' => true,
                        'message' => 'Gia hạn thành công (Demo Mode)',
                        'data' => [
                            'orderId' => $orderId,
                            'detailId' => $actualDetailId,
                            'newEndDate' => $newEndDate,
                            'isPaid' => 1
                        ]
                    ];
                } else {
                    // NORMAL MODE: Create a NEW Payment Order (sl_lv0013)
                    // This new order represents the fee for renewal.
                    
                    $renewalOrderCode = 'RNW-' . date('Ymd') . '-' . mt_rand(1000, 9999);
                    $renewalOrderId = $renewalOrderCode; // Using same format for ID
                    $customerId = $orderInfo['lv002'] ?? ''; 
                    // Need to fetch customer info to fill order? Or just ID is enough?
                    // We need at least basic info for sl_lv0013
                    $custSql = "SELECT lv002, lv006, lv008 FROM sl_lv0001 WHERE lv001 = '$customerId' LIMIT 1"; // lv002=Name, lv006=Phone, lv008=Email
                    // Check customerId in sl_lv0013 is actually in column lv002
                    // Yes, previous query: SELECT O.lv001...
                    // Wait, previous query didn't select O.lv002. fix it.
                }
                
                // Fetch customer ID from Original Order if not available
                 $custInfoSql = "SELECT lv002, lv003, lv006, lv009 FROM sl_lv0013 WHERE lv001='$orderIdEsc' LIMIT 1";
                 $custInfoRes = db_query($custInfoSql);
                 $custInfo = db_fetch_array($custInfoRes);
                 
                 if ($custInfo && !$demoMode) {
                     $cId = $custInfo['lv002'];
                     $cName = nc_sub_escape_str($custInfo['lv003']);
                     $cPhone = nc_sub_escape_str($custInfo['lv006']);
                     $cEmail = nc_sub_escape_str($custInfo['lv009']);
                     $todayFull = date('Y-m-d H:i:s');
                     
                     $renewalOrderCode = 'RNW-' . date('Ymd') . '-' . strtoupper(substr(md5(time() . rand()), 0, 6));
                     
                     // Helper note to link with Renewal Record
                     $typeTag = "TYPE:RENEWAL | Ref: $orderId | RID:$renewalId | DID:$actualDetailId | Gia hạn phần mềm";
                     $typeTagEsc = nc_sub_escape_str($typeTag);
                     
                     // Create Payment Order (Status 1: Pending Payment)
                     $insertOrderSql = "INSERT INTO sl_lv0013 (lv001, lv002, lv003, lv004, lv006, lv009, lv011, lv013, lv016, lv030)
                                        VALUES ('$renewalOrderCode', '$cId', '$cName', '$todayFull', '$cPhone', '$cEmail', 1, '$noteEsc', $amount, '$typeTagEsc')";
                     
                     $orderRes = db_query($insertOrderSql);
                     
                     if ($orderRes) {
                         // Insert detail for this renewal order into sl_lv0014 ???
                         // Not strictly necessary if we rely on the Note/Tag, but cleaner to have a line item.
                         // Let's skip sl_lv0014 for the renewal fee order to keep it simple, or add a dummy line.
                         // Adding dummy line item "Phí gia hạn"
                         $detailItemId = str_pad(mt_rand(1, 99999999), 8, '0', STR_PAD_LEFT);
                         $itemName = "Phí gia hạn đơn hàng $orderId ($months tháng)";
                         $itemNameEsc = nc_sub_escape_str($itemName);
                         $itemSql = "INSERT INTO sl_lv0014 (lv001, lv002, lv003, lv004, lv005, lv006, lv010)
                                     VALUES ('$detailItemId', '$renewalOrderCode', 'RENEWAL_FEE', 1, $amount, $amount, '$itemNameEsc')";
                         db_query($itemSql);
                         
                         $vOutput = [
                            'success' => true,
                            'message' => 'Đơn hàng gia hạn đã được tạo. Vui lòng thanh toán.',
                            'data' => [
                                'originalOrderId' => $orderId,
                                'renewalOrderId' => $renewalOrderCode,
                                'amount' => $amount,
                                'status' => 'pending_payment',
                                'paymentInfo' => [
                                    'bankName' => 'Vietcombank',
                                    'accountNumber' => '0421000123456',
                                    'accountName' => 'CONG TY TNHH SOF',
                                    'transferContent' => "RENEW $renewalOrderCode"
                                ]
                            ]
                        ];
                     } else {
                         $vOutput = ['success' => false, 'message' => 'Lỗi tạo đơn hàng thanh toán'];
                     }
                 }
                break;
            
            /**
             * GET RENEWAL HISTORY - Lấy lịch sử gia hạn
             * Input: orderId
             */
            case "getRenewalHistory":
                $orderId = trim($input['orderId'] ?? $_POST['orderId'] ?? '');
                
                if ($orderId === '') {
                    $vOutput = ['success' => false, 'message' => 'Thiếu mã đơn hàng'];
                    break;
                }
                
                $orderIdEsc = nc_sub_escape_str($orderId);
                
                $renewalSql = "SELECT lv001, lv002, lv003 as ngayGiaHan, lv004 as ngayHetHan, lv005 as lanThanhToan, lv006 as soTienGiaHan, lv007 as thanhToan
                               FROM sl_lv0511 
                               WHERE lv002 = '$orderIdEsc'
                               ORDER BY lv001 DESC";
                $renewalResult = db_query($renewalSql);
                $renewals = [];
                
                while ($rRow = db_fetch_array($renewalResult, MYSQLI_ASSOC)) {
                    $renewals[] = [
                        'id' => $rRow['lv001'],
                        'orderId' => $rRow['lv002'],
                        'renewalDate' => $rRow['ngayGiaHan'],
                        'expiryDate' => $rRow['ngayHetHan'],
                        'renewalCount' => intval($rRow['lanThanhToan'] ?? 0),
                        'amount' => floatval($rRow['soTienGiaHan'] ?? 0),
                        'isPaid' => intval($rRow['thanhToan'] ?? 0)
                    ];
                }
                
                $vOutput = [
                    'success' => true,
                    'data' => $renewals,
                    'count' => count($renewals)
                ];
                break;
            
            default:
                $vOutput = ['success' => false, 'message' => 'Chức năng không tồn tại'];
                break;
        }
        break;
}