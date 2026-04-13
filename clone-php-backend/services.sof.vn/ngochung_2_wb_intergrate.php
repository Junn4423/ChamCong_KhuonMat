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

    case "test_db":
        $conn = db_connect_second();
        if (!$conn) {
            $vOutput = ["success" => false, "message" => "Không thể kết nối DB Second", "error" => mysqli_connect_error()];
        } else {
            $result = mysqli_query($conn, "SHOW TABLES");
            $tables = [];
            if ($result) {
                while ($row = mysqli_fetch_array($result)) {
                    $tables[] = $row[0];
                }
            }
            $vOutput = ["success" => true, "database" => DB_DATABASE_SECOND, "tables" => $tables];
        }
        break;

    case "wb_lv0012":
        switch ($vfun) {
            case "data":
                // Mặc định lấy các tin không bị khóa (lv008 = 0)
                // Tuy nhiên nếu bạn muốn lấy hết thì bỏ WHERE
                $sql = "SELECT lv001 as maTinTuc, lv002 as maLoai, lv003 as chuDe, lv004 as hinhAnh, lv005 as ngayTao, lv006 as thuTu, lv007 as tinMoi, lv008 as khoa
                        FROM wb_lv0012 
                        WHERE lv008 = 0
                        ORDER BY lv006 ASC, lv005 DESC";
                
                $result = db_query_second($sql);
                $vOutput = [];
                
                // Nếu không có tin nào chưa khóa, thử lấy tất cả (để tránh trường hợp db toàn tin đang khóa)
                if (!$result || mysqli_num_rows($result) == 0) {
                     $sql = "SELECT lv001 as maTinTuc, lv002 as maLoai, lv003 as chuDe, lv004 as hinhAnh, lv005 as ngayTao, lv006 as thuTu, lv007 as tinMoi, lv008 as khoa
                             FROM wb_lv0012 
                             ORDER BY lv006 ASC, lv005 DESC LIMIT 20";
                     $result = db_query_second($sql);
                }

                if ($result) {
                    while ($vrow = mysqli_fetch_assoc($result)) {
                        $newsId = $vrow['maTinTuc'];
                        // Lấy mô tả ngắn từ bảng lv0013
                        $sql_desc = "SELECT lv004 as moTaNgan FROM wb_lv0013 WHERE lv002 = '$newsId' LIMIT 1";
                        $res_desc = db_query_second($sql_desc);
                        $vrow['moTaNgan'] = '';
                        if ($res_desc) {
                            $row_desc = mysqli_fetch_assoc($res_desc);
                            if ($row_desc) {
                                $vrow['moTaNgan'] = $row_desc['moTaNgan'];
                            }
                        }
                        $vOutput[] = $vrow;
                    }
                }
                break;
        }
        break;

    case "wb_lv0013":
        switch ($vfun) {
            case "getDetail":
                $maTinTuc = $input['maTinTuc'] ?? $_POST['maTinTuc'] ?? '';
                if (empty($maTinTuc)) {
                    $vOutput = ["success" => false, "message" => "Thiếu mã tin tức"];
                    break;
                }
                
                $conn = db_connect_second();
                $maTinTucEsc = mysqli_real_escape_string($conn, $maTinTuc);
                
                $sql = "SELECT lv001 as id, lv002 as maTinTuc, lv003 as chuDe, lv004 as noiDungNgan, lv005 as noiDung, lv006 as ngonNgu 
                        FROM wb_lv0013 
                        WHERE lv002 = '$maTinTucEsc' LIMIT 1";
                
                $result = db_query_second($sql);
                if ($result) {
                    $data = mysqli_fetch_assoc($result);
                    if ($data) {
                        $vOutput = ["success" => true, "data" => $data];
                    } else {
                        $vOutput = ["success" => false, "message" => "Không tìm thấy chi tiết tin tức cho mã: $maTinTuc"];
                    }
                } else {
                    $vOutput = ["success" => false, "message" => "Lỗi truy vấn database", "error" => mysqli_error($conn)];
                }
                break;
        }
        break;

    case "wb_lv0016":
        $conn = db_connect_second();
        switch ($vfun) {
            case "createContactOrder":
                $tenKH = $input['tenKH'] ?? $_POST['tenKH'] ?? '';
                $nguoiDaiDien = $input['nguoiDaiDien'] ?? $_POST['nguoiDaiDien'] ?? '';
                $email = $input['email'] ?? $_POST['email'] ?? '';
                $sdt = $input['sdt'] ?? $_POST['sdt'] ?? '';
                $services = $input['service'] ?? []; // This is an array of maSanPham
                $message = $input['message'] ?? $_POST['message'] ?? '';
                $ngayLam = $input['ngayLam'] ?? $_POST['ngayLam'] ?? '';
                $ngayKetThuc = $input['ngayKetThuc'] ?? $_POST['ngayKetThuc'] ?? '';

                if (empty($tenKH) || empty($sdt) || empty($services)) {
                    $vOutput = ["success" => false, "message" => "Thiếu thông tin bắt buộc (Tên, SĐT hoặc Dịch vụ)"];
                    break;
                }

                $tenKHEsc = mysqli_real_escape_string($conn, $tenKH);
                $nguoiDaiDienEsc = mysqli_real_escape_string($conn, $nguoiDaiDien);
                $emailEsc = mysqli_real_escape_string($conn, $email);
                $sdtEsc = mysqli_real_escape_string($conn, $sdt);
                
                $fullMessage = $message;
                if (!empty($ngayLam)) $fullMessage .= "\nNgày dự kiến: $ngayLam";
                if (!empty($ngayKetThuc)) $fullMessage .= " đến $ngayKetThuc";
                $messageEsc = mysqli_real_escape_string($conn, $fullMessage);
                
                $successCount = 0;
                $orderIds = [];
                $userCode = "CONTACT_" . date("Ymd_His");

                // 1. Ghi vào wb_lv0016 (Thông tin đơn hàng tổng) - GHI 1 LẦN DUY NHẤT
                // lv018: Trạng thái (đặt mặc định = 4 theo yêu cầu)  - 4 là trạng thái gửi tư vấn
                $sql1 = "INSERT INTO wb_lv0016 (lv002, lv003, lv005, lv006, lv009, lv015, lv017, lv018, lv033) 
                         VALUES (NOW(), '$userCode', '$tenKHEsc', '$nguoiDaiDienEsc', '$sdtEsc', '$emailEsc', '$messageEsc', 4, NOW())";
                
                if (mysqli_query($conn, $sql1)) {
                    $newOrderId = mysqli_insert_id($conn);
                    $orderIds[] = $newOrderId;

                    foreach ($services as $serviceId) {
                        $serviceIdEsc = mysqli_real_escape_string($conn, $serviceId);
                        // Lấy Mã sản phẩm (lv002) từ wb_lv0006 để ghi vào chi tiết đơn hàng
                        $pkgCode = $serviceId; 
                        $sql_get_code = "SELECT lv002 FROM wb_lv0006 WHERE lv001 = '$serviceIdEsc' LIMIT 1";
                        $res_code = mysqli_query($conn, $sql_get_code);
                        if ($res_code && mysqli_num_rows($res_code) > 0) {
                            $row_code = mysqli_fetch_assoc($res_code);
                            if (!empty($row_code['lv002'])) {
                                $pkgCode = trim($row_code['lv002']);
                            }
                        }
                        $pkgCodeEsc = mysqli_real_escape_string($conn, $pkgCode);
                        
                        // 2. Ghi vào wb_lv0017 (Chi tiết mã sản phẩm khách chọn)
                        // All services point to the same $newOrderId
                        $sql2 = "INSERT INTO wb_lv0017 (lv002, lv003, lv004, lv007, lv008, lv009) 
                                 VALUES ('$pkgCodeEsc', 0, 1, NOW(), NULL, '$newOrderId')";
                        
                        if (mysqli_query($conn, $sql2)) {
                            $successCount++;
                        }
                    }
                }

                if ($successCount > 0) {
                    $vOutput = [
                        "success" => true, 
                        "message" => "Đã tạo thành công $successCount phiếu yêu cầu tư vấn.",
                        "orderIds" => $orderIds
                    ];
                } else {
                    $vOutput = ["success" => false, "message" => "Lỗi khi lưu dữ liệu vào database", "error" => mysqli_error($conn)];
                }
                break;

            case "getCart":
                $maKH = $input['maKH'] ?? $_POST['maKH'] ?? '';
                if (empty($maKH)) {
                    $vOutput = ["success" => false, "message" => "Thiếu mã khách hàng"];
                    break;
                }

                $maKHEsc = mysqli_real_escape_string($conn, $maKH);
                // Giỏ hàng có lv018 = 2
                $sql_cart = "SELECT lv001, lv003 as orderCode FROM wb_lv0016 WHERE lv011 = '$maKHEsc' AND lv018 = 2 LIMIT 1";
                $res_cart = mysqli_query($conn, $sql_cart);
                
                if ($res_cart && mysqli_num_rows($res_cart) > 0) {
                    $cartHeader = mysqli_fetch_assoc($res_cart);
                    $cartId = $cartHeader['lv001'];
                    
                    // Lấy chi tiết các item trong giỏ
                    // Join với wb_lv0006 để lấy tên và slug
                    $sql_items = "SELECT t1.lv002 as pkgCode, t1.lv003 as price, t1.lv004 as months, t2.lv001 as productId, t2.lv003 as productName, t2.lv017 as productSlug
                                  FROM wb_lv0017 t1
                                  LEFT JOIN wb_lv0006 t2 ON t1.lv002 = t2.lv002
                                  WHERE t1.lv009 = '$cartId'";
                    $res_items = mysqli_query($conn, $sql_items);
                    $items = [];
                    while ($iRow = mysqli_fetch_assoc($res_items)) {
                        $items[] = [
                            "productCode" => $iRow['productId'], 
                            "pkgCode" => $iRow['pkgCode'],
                            "name" => $iRow['productName'],
                            "slug" => $iRow['productSlug'],
                            "price" => floatval($iRow['price']),
                            "months" => intval($iRow['months']),
                            "quantity" => 1
                        ];
                    }
                    $vOutput = ["success" => true, "data" => $items, "orderCode" => $cartHeader['orderCode']];
                } else {
                    $vOutput = ["success" => true, "data" => [], "message" => "Chưa có giỏ hàng"];
                }
                break;

            case "createCartOrder":
                $maKH = $input['maKH'] ?? $_POST['maKH'] ?? '';
                $tenKH = $input['tenKH'] ?? $_POST['tenKH'] ?? '';
                $email = $input['email'] ?? $_POST['email'] ?? '';
                $services = $input['services'] ?? []; // Array of {productCode, price, quantity, months}

                if (empty($maKH)) {
                  $vOutput = ["success" => false, "message" => "Thiếu mã khách hàng"];
                  break;
                }

                $maKHEsc = mysqli_real_escape_string($conn, $maKH);
                $tenKHEsc = mysqli_real_escape_string($conn, $tenKH);
                $emailEsc = mysqli_real_escape_string($conn, $email);

                // Kiểm tra xem khách hàng đã có giỏ hàng (lv018 = 2) chưa
                $newOrderId = null;
                $orderCode = "";
                $sql_check = "SELECT lv001, lv003 FROM wb_lv0016 WHERE lv011 = '$maKHEsc' AND lv018 = 2 ORDER BY lv001 DESC LIMIT 1";
                $res_check = mysqli_query($conn, $sql_check);
                
                if ($res_check && mysqli_num_rows($res_check) > 0) {
                    $row_check = mysqli_fetch_assoc($res_check);
                    $newOrderId = $row_check['lv001'];
                    $orderCode = $row_check['lv003'];
                    // Cập nhật lại thông tin khách hàng và thời gian cập nhật giỏ hàng
                    mysqli_query($conn, "UPDATE wb_lv0016 SET lv005 = '$tenKHEsc', lv015 = '$emailEsc', lv033 = NOW() WHERE lv001 = '$newOrderId'");
                } else {
                    $orderCode = "CART_" . date("Ymd_His");
                    // lv018 = 2 for Cart/Draft orders theo yêu cầu - trạng thái giỏ hàng đang chờ thanh toán
                    // lv011 lưu mã khách hàng sl_lv0001
                    $sql1 = "INSERT INTO wb_lv0016 (lv002, lv003, lv005, lv011, lv015, lv018, lv033) 
                             VALUES (NOW(), '$orderCode', '$tenKHEsc', '$maKHEsc', '$emailEsc', 2, NOW())";
                    
                    if (mysqli_query($conn, $sql1)) {
                        $newOrderId = mysqli_insert_id($conn);
                    } else {
                        $vOutput = ["success" => false, "message" => "Lỗi tạo giỏ hàng: " . mysqli_error($conn)];
                        break;
                    }
                }

                if ($newOrderId) {
                    // Xóa các item cũ trong giỏ hàng này trước khi chèn mới để tránh trùng lặp
                    mysqli_query($conn, "DELETE FROM wb_lv0017 WHERE lv009 = '$newOrderId'");

                    // Lấy mã ID nội bộ (lv001) từ sl_lv0001 của khách hàng
                    $customerInternalId = 0;
                    $sql_cust = "SELECT lv001 FROM sl_lv0001 WHERE lv002 = '$maKHEsc' LIMIT 1";
                    $res_cust = mysqli_query($conn, $sql_cust);
                    if ($res_cust && mysqli_num_rows($res_cust) > 0) {
                        $row_cust = mysqli_fetch_assoc($res_cust);
                        $customerInternalId = intval($row_cust['lv001']);
                    }

                    $successCount = 0;
                    foreach ($services as $item) {
                        $pIdEsc = mysqli_real_escape_string($conn, $item['productCode'] ?? '');
                        $price = floatval($item['price'] ?? 0);
                        $qty = intval($item['quantity'] ?? 1);
                        $months = intval($item['months'] ?? 1);
                        
                        // Lấy Mã sản phẩm (lv002) từ wb_lv0006
                        $pkgCode = $item['productCode'] ?? ''; 
                        $sql_get_code = "SELECT lv002 FROM wb_lv0006 WHERE lv001 = '$pIdEsc' LIMIT 1";
                        $res_code = mysqli_query($conn, $sql_get_code);
                        if ($res_code && mysqli_num_rows($res_code) > 0) {
                            $row_code = mysqli_fetch_assoc($res_code);
                            if (!empty($row_code['lv002'])) {
                                $pkgCode = trim($row_code['lv002']);
                            }
                        }
                        $pkgCodeEsc = mysqli_real_escape_string($conn, $pkgCode);

                        if ($pkgCodeEsc) {
                            // lv003: đơn giá, lv004: SL (số tháng), lv005: mã người dùng (ID khách hàng từ sl_lv0001), lv009: ID của wb_lv0016
                            $sql2 = "INSERT INTO wb_lv0017 (lv002, lv003, lv004, lv005, lv007, lv008, lv009) 
                                     VALUES ('$pkgCodeEsc', $price, $months, $customerInternalId, NOW(), NULL, '$newOrderId')";
                            if (mysqli_query($conn, $sql2)) {
                                $successCount++;
                            }
                        }
                    }
                    $vOutput = [
                        "success" => true, 
                        "message" => "Giỏ hàng đã được lưu thành công!", 
                        "orderId" => $newOrderId, 
                        "orderCode" => $orderCode,
                        "itemCount" => $successCount
                    ];
                } else {
                    $vOutput = ["success" => false, "message" => "Lỗi: " . mysqli_error($conn)];
                }
                break;

            case "clearCart":
                $maKH = $input['maKH'] ?? $_POST['maKH'] ?? '';
                if (empty($maKH)) {
                    $vOutput = ["success" => false, "message" => "Thiếu mã khách hàng"];
                    break;
                }
                $maKHEsc = mysqli_real_escape_string($conn, $maKH);
                // 1. Tìm ID giỏ hàng hiện tại (lv018 = 2)
                $sql_cart = "SELECT lv001 FROM wb_lv0016 WHERE lv011 = '$maKHEsc' AND lv018 = 2 LIMIT 1";
                $res_cart = mysqli_query($conn, $sql_cart);
                if ($res_cart && mysqli_num_rows($res_cart) > 0) {
                    $cart = mysqli_fetch_assoc($res_cart);
                    $cartId = $cart['lv001'];
                    // 2. Xóa tất cả các item trong wb_lv0017 mapping với cartId
                    mysqli_query($conn, "DELETE FROM wb_lv0017 WHERE lv009 = '$cartId'");
                    // 3. Xóa luôn phiếu giỏ hàng hoặc đổi trạng thái? Thường là xóa hoặc giữ phiếu trống.
                    // Ở đây xóa luôn phiếu giỏ hàng cho sạch.
                    mysqli_query($conn, "DELETE FROM wb_lv0016 WHERE lv001 = '$cartId'");
                    $vOutput = ["success" => true, "message" => "Đã xóa toàn bộ giỏ hàng database"];
                } else {
                    $vOutput = ["success" => true, "message" => "Không tìm thấy giỏ hàng trong database để xóa"];
                }
                break;

            case "removeCartItem":
                $maKH = $input['maKH'] ?? $_POST['maKH'] ?? '';
                $productCode = $input['productCode'] ?? $_POST['productCode'] ?? '';

                if (empty($maKH) || empty($productCode)) {
                    $vOutput = ["success" => false, "message" => "Thiếu mã khách hàng hoặc mã sản phẩm"];
                    break;
                }

                $maKHEsc = mysqli_real_escape_string($conn, $maKH);
                $pIdEsc = mysqli_real_escape_string($conn, $productCode);

                // 1. Tìm ID giỏ hàng hiện tại (lv018 = 2)
                $sql_cart = "SELECT lv001 FROM wb_lv0016 WHERE lv011 = '$maKHEsc' AND lv018 = 2 LIMIT 1";
                $res_cart = mysqli_query($conn, $sql_cart);
                
                if ($res_cart && mysqli_num_rows($res_cart) > 0) {
                    $cart = mysqli_fetch_assoc($res_cart);
                    $cartId = $cart['lv001'];

                    // 2. Xóa item trong wb_lv0017 mapping với cartId và productCode
                    // Chấp nhận xóa theo lv002 (mã sản phẩm thực tế) hoặc mã ID (lv001) trong wb_lv0006
                    $sql_del = "DELETE FROM wb_lv0017 WHERE lv009 = '$cartId' AND (lv002 = '$pIdEsc' OR lv002 IN (SELECT lv002 FROM wb_lv0006 WHERE lv001 = '$pIdEsc'))";
                    if (mysqli_query($conn, $sql_del)) {
                        $vOutput = ["success" => true, "message" => "Đã xóa sản phẩm khỏi giỏ hàng database"];
                    } else {
                        $vOutput = ["success" => false, "message" => "Lỗi xóa item: " . mysqli_error($conn)];
                    }
                } else {
                    $vOutput = ["success" => true, "message" => "Không tìm thấy giỏ hàng trong database để xóa"];
                }
                break;

            case "createDemoOrder":
                // Demo API handles: DEMOFREE and DEMOPAY
                $type = $input['type'] ?? 'FREE'; // FREE or PAY
                $maKH = $input['maKH'] ?? $_POST['maKH'] ?? ''; 
                $tenKH = $input['tenKH'] ?? $_POST['tenKH'] ?? '';
                $email = $input['email'] ?? $_POST['email'] ?? '';
                $sdt = $input['sdt'] ?? $_POST['sdt'] ?? '';
                $diaChi = $input['diaChi'] ?? $_POST['diaChi'] ?? '';
                $ghiChu = $input['note'] ?? $_POST['note'] ?? '';
                $services = $input['services'] ?? [];

                $maKHEsc = mysqli_real_escape_string($conn, $maKH);
                $tenKHEsc = mysqli_real_escape_string($conn, $tenKH);
                $emailEsc = mysqli_real_escape_string($conn, $email);
                $sdtEsc = mysqli_real_escape_string($conn, $sdt);
                $diaChiEsc = mysqli_real_escape_string($conn, $diaChi);
                $ghiChuEsc = mysqli_real_escape_string($conn, $ghiChu);

                $prefix = ($type == 'PAY') ? "DEMOPAY_" : "DEMOFREE_";
                $orderCode = $prefix . date("Ymd_His");
                
                // Trạng thái (lv018):
                // 5: Demo Miễn Phí (DEMOFREE)
                // 7: Chờ thanh toán (DEMOPAY)
                $status = ($type == 'PAY') ? 7 : 5;
                
                $sql1 = "INSERT INTO wb_lv0016 (lv002, lv003, lv005, lv009, lv011, lv015, lv017, lv018, lv033, lv006) 
                         VALUES (NOW(), '$orderCode', '$tenKHEsc', '$sdtEsc', '$maKHEsc', '$emailEsc', '$ghiChuEsc', $status, NOW(), '$diaChiEsc')";
                
                if (mysqli_query($conn, $sql1)) {
                    $newOrderId = mysqli_insert_id($conn);
                    
                    // Lấy ID khách hàng nếu có
                    $customerInternalId = 0;
                    if (!empty($maKH)) {
                        $sql_cust = "SELECT lv001 FROM sl_lv0001 WHERE lv002 = '$maKHEsc' LIMIT 1";
                        $res_cust = mysqli_query($conn, $sql_cust);
                        if ($res_cust && mysqli_num_rows($res_cust) > 0) {
                            $row_cust = mysqli_fetch_assoc($res_cust);
                            $customerInternalId = intval($row_cust['lv001']);
                        }
                    }

                    foreach ($services as $item) {
                        $pIdEsc = mysqli_real_escape_string($conn, $item['productCode'] ?? '');
                        $price = floatval($item['price'] ?? 0);
                        $months = intval($item['months'] ?? 1);
                        
                        $pkgCode = $item['productCode'] ?? '';
                        $sql_get_code = "SELECT lv002 FROM wb_lv0006 WHERE lv001 = '$pIdEsc' LIMIT 1";
                        $res_code = mysqli_query($conn, $sql_get_code);
                        if ($res_code && mysqli_num_rows($res_code) > 0) {
                            $row_code = mysqli_fetch_assoc($res_code);
                            $pkgCode = trim($row_code['lv002'] ?? $pkgCode);
                        }
                        $pkgCodeEsc = mysqli_real_escape_string($conn, $pkgCode);

                        $sql2 = "INSERT INTO wb_lv0017 (lv002, lv003, lv004, lv005, lv007, lv009) 
                                 VALUES ('$pkgCodeEsc', $price, $months, $customerInternalId, NOW(), '$newOrderId')";
                        mysqli_query($conn, $sql2);
                    }

                    $vOutput = ["success" => true, "message" => "Đã tạo yêu cầu demo thành công", "orderCode" => $orderCode, "orderId" => $newOrderId];

                    // Sau khi tạo demo thành công, nếu là khách hàng đăng nhập thì xóa giỏ hàng hiện tại (lv018 = 2)
                    if (!empty($maKH)) {
                        $sql_get_cart = "SELECT lv001 FROM wb_lv0016 WHERE lv011 = '$maKHEsc' AND lv018 = 2 LIMIT 1";
                        $res_cart = mysqli_query($conn, $sql_get_cart);
                        if ($res_cart && mysqli_num_rows($res_cart) > 0) {
                            $cartRow = mysqli_fetch_assoc($res_cart);
                            $cartId = $cartRow['lv001'];
                            mysqli_query($conn, "DELETE FROM wb_lv0017 WHERE lv009 = '$cartId'");
                            mysqli_query($conn, "DELETE FROM wb_lv0016 WHERE lv001 = '$cartId'");
                        }
                    }
                } else {
                    $vOutput = ["success" => false, "message" => "Lỗi tạo demo: " . mysqli_error($conn)];
                }
                break;

            case "updateOrderStatus":
                $orderCode = $input['orderCode'] ?? '';
                $status = $input['status'] ?? ''; // 'SUCCESS' or 'CANCEL'

                if (empty($orderCode)) {
                    $vOutput = ["success" => false, "message" => "Thiếu mã đơn hàng"];
                    break;
                }

                $codeEsc = mysqli_real_escape_string($conn, $orderCode);
                
                // Check if it's an ERP order (starts with ORD)
                if (strpos($orderCode, 'ORD') === 0) {
                    // This is an ERP order created from nc_cart checkout
                    // We need to call the confirmPaymentOnline logic
                    // For simplicity, we can just do the SQL here since we have the connection
                    // But we should use the same table structure
                    
                    if ($status == 'SUCCESS') {
                        $today = date('Y-m-d H:i:s');
                        $note = "Thanh toán thành công từ Website";
                        
                        // Update sl_lv0013
                        $updateSql = "UPDATE sl_lv0013 SET lv011=1, lv029='$today', lv030=CONCAT(IFNULL(lv030,''), ' | $note') WHERE lv001 = '$codeEsc' OR lv001 = (SELECT lv001 FROM sl_lv0013 WHERE lv014 = '$codeEsc' LIMIT 1)";
                        // Note: lv014 might be where the ORD... code is stored if it's sl_lv0010, but in sl_lv0013 we used lv001
                        
                        if (mysqli_query($conn, $updateSql)) {
                            // Also update sl_lv0511 and sl_lv0014
                            mysqli_query($conn, "UPDATE sl_lv0511 SET lv007=1 WHERE lv002 IN ('$codeEsc', (SELECT lv001 FROM sl_lv0013 WHERE lv014 = '$codeEsc' LIMIT 1))");
                            mysqli_query($conn, "UPDATE sl_lv0014 SET lv015=1 WHERE lv002 IN ('$codeEsc', (SELECT lv001 FROM sl_lv0013 WHERE lv014 = '$codeEsc' LIMIT 1))");
                            
                            $vOutput = ["success" => true, "message" => "Cập nhật trạng thái đơn hàng ERP thành công"];
                        } else {
                            $vOutput = ["success" => false, "message" => "Lỗi cập nhật ERP: " . mysqli_error($conn)];
                        }
                    } else {
                        $vOutput = ["success" => true, "message" => "ERP order remains pending"];
                    }
                    break;
                }

                // mapping logic for WB tables:
                $targetStatus = 7;
                if ($status == 'SUCCESS') {
                    $targetStatus = 1;
                } else if ($status == 'CANCEL') {
                    $targetStatus = 7;
                } else {
                    $targetStatus = intval($status); // fallback
                }

                $sql = "UPDATE wb_lv0016 SET lv018 = $targetStatus, lv033 = NOW() WHERE lv003 = '$codeEsc'";
                if (mysqli_query($conn, $sql)) {
                    $vOutput = ["success" => true, "message" => "Cập nhật trạng thái đơn hàng thành công", "newStatus" => $targetStatus];
                } else {
                    $vOutput = ["success" => false, "message" => "Lỗi cập nhật: " . mysqli_error($conn)];
                }
                break;

            case "getUserOrders":
                $maKH = $input['maKH'] ?? $_POST['maKH'] ?? '';
                if (empty($maKH)) {
                    $vOutput = ["success" => false, "message" => "Thiếu mã khách hàng"];
                    break;
                }

                $maKHEsc = mysqli_real_escape_string($conn, $maKH);
                $orders = [];

                // 1. Load website orders (wb_lv0016) - Status: 0=Hủy, 5=Demo Free, 1=Success, 7=Pending
                $sql_wb = "SELECT lv001, lv003 as orderCode, lv002 as orderDate, lv018 as status, lv005 as customerName 
                           FROM wb_lv0016 
                           WHERE lv011 = '$maKHEsc' AND lv018 IN (0, 1, 5, 7) 
                           ORDER BY lv002 DESC";
                $res_wb = mysqli_query($conn, $sql_wb);
                while ($row = mysqli_fetch_assoc($res_wb)) {
                    $orderId = $row['lv001'];
                    $sql_items = "SELECT lv002 as productCode, lv003 as price, lv004 as months FROM wb_lv0017 WHERE lv009 = '$orderId'";
                    $res_items = mysqli_query($conn, $sql_items);
                    $items = [];
                    while ($iRow = mysqli_fetch_assoc($res_items)) {
                        $items[] = $iRow;
                    }
                    $row['items'] = $items;
                    $row['source'] = 'WEB';
                    $orders[] = $row;
                }

                // 2. Load ERP orders (sl_lv0013) - Status: 1=Pending, 2=Paid, 0=Cancelled (standard ERP mapping)
                $sql_erp = "SELECT lv001 as orderId, lv001 as orderCode, lv004 as orderDate, lv011 as status, lv003 as customerName, lv016 as totalAmount
                            FROM sl_lv0013 
                            WHERE lv002 = '$maKHEsc' OR lv002 = 'WEB_$maKHEsc'
                            ORDER BY lv004 DESC";
                $res_erp = mysqli_query($conn, $sql_erp);
                while ($row = mysqli_fetch_assoc($res_erp)) {
                    $erpOrderId = $row['orderId'];
                    // Map ERP status (1) to WB status (1)
                    $erpStatus = intval($row['status']);
                    $mappedStatus = 7; // Default Pending
                    if ($erpStatus === 1) $mappedStatus = 1; // Paid
                    if ($erpStatus === 0) $mappedStatus = 0; // Cancelled
                    
                    $sql_items = "SELECT lv003 as productCode, lv005 as price, lv004 as months FROM sl_lv0014 WHERE lv002 = '$erpOrderId'";
                    $res_items = mysqli_query($conn, $sql_items);
                    $items = [];
                    while ($iRow = mysqli_fetch_assoc($res_items)) {
                        $items[] = $iRow;
                    }
                    
                    $orders[] = [
                        'lv001' => $erpOrderId,
                        'orderCode' => $row['orderCode'],
                        'orderDate' => $row['orderDate'],
                        'status' => (string)$mappedStatus,
                        'customerName' => $row['customerName'],
                        'totalAmount' => $row['totalAmount'],
                        'items' => $items,
                        'source' => 'ERP'
                    ];
                }

                // Sort unified list by date
                usort($orders, function($a, $b) {
                    return strtotime($b['orderDate']) - strtotime($a['orderDate']);
                });

                $vOutput = ["success" => true, "data" => $orders];
                break;
        }
        break;
}
