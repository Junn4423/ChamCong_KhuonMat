<?php
// get_file.php
session_start();

// Lấy tham số từ URL
$maTaiLieu = $_GET['maPhieu'] ?? null;
$id        = $_GET['id'] ?? null;

if (!$maTaiLieu || !$id) {
    http_response_code(400);
    echo "Thiếu maTaiLieu hoặc id";
    exit;
}

//  Kết nối DB trực tiếp
$dbhost = "localhost";
$dbuser = "root"; // sửa nếu user DB khác
$dbpass = "";     // sửa nếu DB có mật khẩu
$dbname = "hao_erp_sof_documents_v5_0"; // tên database của bạn

$mysql = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
if (!$mysql) {
    die("Kết nối DB thất bại: " . mysqli_connect_error());
}

// Escape dữ liệu để chống SQL Injection
$maTaiLieu = mysqli_real_escape_string($mysql, $maTaiLieu);
$id        = mysqli_real_escape_string($mysql, $id);

// Truy vấn lấy file từ bảng documents
$sql = "
    SELECT lv003 AS fileName, lv008 AS fileContent
    FROM hr_kb0002
    WHERE lv002 = '$maTaiLieu'
      AND lv001 = '$id'
    LIMIT 1
";
$rs  = mysqli_query($mysql, $sql);
$row = mysqli_fetch_assoc($rs);

if (!$row || empty($row['fileContent'])) {
    http_response_code(404);
    echo "Không tìm thấy file";
    exit;
}

$fileName = $row['fileName'];
$ext      = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
$data     = $row['fileContent'];

// Kiểm tra xem có phải base64 hay binary data
if (!empty($data)) {
    if (base64_decode($data, true) !== false && base64_encode(base64_decode($data, true)) === $data) {
        // Là base64, decode nó
        $data = base64_decode($data);
    }
}

// Map MIME
$mimeMap = [
    'jpg' => 'image/jpeg',
    'jpeg' => 'image/jpeg',
    'png' => 'image/png',
    'pdf' => 'application/pdf',
    'doc' => 'application/msword',
    'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    'xls' => 'application/vnd.ms-excel',
    'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    'mp4' => 'video/mp4'
];
$mime = $mimeMap[$ext] ?? 'application/octet-stream';

// Xóa buffer trước khi output
if (function_exists('ob_get_level')) {
    while (ob_get_level() > 0) {
        ob_end_clean();
    }
}

// Xuất file trực tiếp
header("Content-Type: $mime");
header("Content-Disposition: inline; filename=\"" . basename($fileName) . "\"");
header("Content-Length: " . strlen($data));

echo $data;
exit;