<?php
header('Content-Type: application/json'); // trả về dạng JSON
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}
// Check if custom storage path is provided from frontend
$customPath = isset($_POST['storage_path']) ? $_POST['storage_path'] : null;

if ($customPath && !empty($customPath)) {
    // Use custom path from frontend settings
    $rootDir = rtrim($customPath, '/\\') . '/';
} else {
    // Default fallback path - ĐỒNG BỘ với get_image.php
    $rootDir = "C:/HinhAnh_ChamCongNhanSu/";
}

// Create date-based subdirectories for better organization
$year = date("Y");
$month = date("m");
$day = date("d");

$subDir = "Nam_$year/Thang_$month/Ngay_$day/";
$targetDir = $rootDir . $subDir;

if (!is_dir($targetDir)) {
    if (!mkdir($targetDir, 0755, true)) {
        http_response_code(500);
        echo json_encode([
            "success" => false,
            "filePath" => "",
            "message" => "Không thể tạo thư mục: $targetDir"
        ]);
        exit;
    }
}

if (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN') {
    chmod($targetDir, 0777);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_FILES['image'])) {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "filePath" => "",
            "message" => "Không có file ảnh được gửi lên."
        ]);
        exit;
    }

    $file = $_FILES['image'];

    // Use the filename from frontend if provided, otherwise use original filename
    if (isset($_POST['filename']) && !empty($_POST['filename'])) {
        $filename = basename($_POST['filename']); // Use frontend filename
    } else {
        $filename = basename($file["name"]); // Fallback to original
    }

    $targetFile = $targetDir . $filename;

    if (move_uploaded_file($file["tmp_name"], $targetFile)) {
        echo json_encode([
            "success" => true,
            "filePath" => $filename, // Return just filename for database storage
            "fullPath" => $targetFile, // Full path for debugging
            "message" => "Upload thành công"
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            "success" => false,
            "filePath" => "",
            "message" => "Upload thất bại."
        ]);
    }
} else {
    http_response_code(405);
    echo json_encode([
        "success" => false,
        "filePath" => "",
        "message" => "Chỉ hỗ trợ phương thức POST."
    ]);
}
