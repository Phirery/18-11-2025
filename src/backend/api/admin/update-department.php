<?php
require_once '../../config/cors.php';
require_once '../../core/dp.php';
require_once '../../core/session.php';

require_role('quantri');

try {
    // Lấy dữ liệu POST
    $data = json_decode(file_get_contents('php://input'), true);
    
    $maKhoa = $conn->real_escape_string($data['maKhoa']);
    $tenKhoa = $conn->real_escape_string($data['tenKhoa']);
    $moTa = $conn->real_escape_string($data['moTa']);
    
    // Kiểm tra khoa có tồn tại không
    $checkSql = "SELECT COUNT(*) as count FROM khoa WHERE maKhoa = '$maKhoa'";
    $checkResult = $conn->query($checkSql);
    $count = $checkResult->fetch_assoc()['count'];
    
    if ($count === 0) {
        throw new Exception('Không tìm thấy khoa!');
    }
    
    // Cập nhật khoa
    $sql = "UPDATE khoa 
            SET tenKhoa = '$tenKhoa',
                moTa = '$moTa'
            WHERE maKhoa = '$maKhoa'";
    
    if ($conn->query($sql) === TRUE) {
        if ($conn->affected_rows > 0) {
            echo json_encode([
                'success' => true,
                'message' => 'Cập nhật khoa thành công!'
            ], JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Không có thay đổi nào được thực hiện'
            ], JSON_UNESCAPED_UNICODE);
        }
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