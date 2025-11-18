<?php
require_once '../../config/cors.php';
require_once '../../core/dp.php';
require_once '../../core/session.php';

require_role('quantri');

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $maKhoa = $conn->real_escape_string($data['maKhoa'] ?? '');
    
    if (empty($maKhoa)) {
        throw new Exception('Mã khoa là bắt buộc!');
    }
    
    // Kiểm tra tồn tại
    $checkSql = "SELECT COUNT(*) as count FROM khoa WHERE maKhoa = '$maKhoa'";
    $checkResult = $conn->query($checkSql);
    $count = $checkResult->fetch_assoc()['count'];
    
    if ($count === 0) {
        throw new Exception('Không tìm thấy khoa!');
    }
    
    // Xóa khoa (FK sẽ cascade/set null tự động)
    $sql = "DELETE FROM khoa WHERE maKhoa = '$maKhoa'";
    
    if ($conn->query($sql) === TRUE) {
        if ($conn->affected_rows > 0) {
            echo json_encode([
                'success' => true,
                'message' => 'Xóa khoa thành công!'
            ], JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Không có thay đổi nào'
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