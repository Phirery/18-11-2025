<?php
require_once '../../config/cors.php';
require_once '../../core/dp.php';
require_once '../../core/session.php';

require_role('quantri');

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $tenKhoa = trim($conn->real_escape_string($data['tenKhoa'] ?? ''));
    $moTa = $conn->real_escape_string($data['moTa'] ?? '');
    
    if (empty($tenKhoa)) {
        throw new Exception('Tên khoa là bắt buộc!');
    }
    
    // Tạo mã khoa tự động (3 chữ cái đầu viết hoa + 4 số cuối của timestamp)
    $prefix = strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $tenKhoa), 0, 3));
    if (strlen($prefix) < 3) {
        $prefix = str_pad($prefix, 3, 'X');
    }
    $maKhoa = $prefix . date('Hi');
    
    // Kiểm tra mã khoa đã tồn tại chưa
    $checkSql = "SELECT COUNT(*) as count FROM khoa WHERE maKhoa = '$maKhoa'";
    $checkResult = $conn->query($checkSql);
    $count = $checkResult->fetch_assoc()['count'];
    
    // Nếu trùng, thêm số random
    if ($count > 0) {
        $maKhoa = $prefix . rand(100, 999);
    }
    
    // Thêm khoa mới
    $sql = "INSERT INTO khoa (maKhoa, tenKhoa, moTa) 
            VALUES ('$maKhoa', '$tenKhoa', '$moTa')";
    
    if ($conn->query($sql) === TRUE) {
        echo json_encode([
            'success' => true,
            'message' => 'Thêm khoa thành công!',
            'maKhoa' => $maKhoa
        ], JSON_UNESCAPED_UNICODE);
    } else {
        throw new Exception('Lỗi: ' . $conn->error);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}

$conn->close();
?>