<?php
// get_image.php

// Thư mục gốc ảnh
$rootDir = "C:/HinhAnh_ChamCongNhanSu/";

// Lấy tên file và đường dẫn con từ POST hoặc GET
$filename = $_POST['filename'] ?? $_GET['filename'] ?? null;
$subDir   = $_POST['subdir'] ?? $_GET['subdir'] ?? '';
$subDir   = $subDir ? rtrim($subDir, '/\\') . '/' : '';

if (!$filename) {
    http_response_code(400);
    echo "Thiếu tham số filename";
    exit;
}

$filePath = $rootDir . $subDir . $filename;

if (!file_exists($filePath)) {
    http_response_code(404);
    echo "Không tìm thấy ảnh";
    exit;
}

// Trả về file ảnh
$mime = mime_content_type($filePath);
header("Content-Type: $mime");
header("Content-Length: " . filesize($filePath));

readfile($filePath);
exit;
