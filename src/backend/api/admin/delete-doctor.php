<?php
require_once '../../config/cors.php';
require_once '../../core/dp.php';
require_once '../../core/session.php';

require_role('quantri');

// Bắt đầu transaction
$conn->begin_transaction();

try {
    // Lấy dữ liệu POST
    $data = json_decode(file_get_contents('php://input'), true);
    $maBacSi = $conn->real_escape_string($data['maBacSi']);
    
    // Kiểm tra xem bác sĩ có lịch khám nào không
    $checkSql = "SELECT COUNT(*) as count FROM lichkham WHERE maBacSi = '$maBacSi'";
    $checkResult = $conn->query($checkSql);
    $count = $checkResult->fetch_assoc()['count'];
    
    if ($count > 0) {
        throw new Exception('Không thể xóa bác sĩ này vì đã có lịch khám!');
    }
    
    // Lấy nguoiDungId
    $getUserSql = "SELECT nguoiDungId FROM bacsi WHERE maBacSi = '$maBacSi'";
    $userResult = $conn->query($getUserSql);
    
    if ($userResult->num_rows === 0) {
        throw new Exception('Không tìm thấy bác sĩ!');
    }
    
    $nguoiDungId = $userResult->fetch_assoc()['nguoiDungId'];
    
    // 1. Xóa từ bảng bacsi (sẽ tự động xóa nguoidung do ON DELETE CASCADE)
    $sql = "DELETE FROM bacsi WHERE maBacSi = '$maBacSi'";
    
    if (!$conn->query($sql)) {
        throw new Exception('Lỗi xóa bác sĩ: ' . $conn->error);
    }
    
    if ($conn->affected_rows === 0) {
        throw new Exception('Không tìm thấy bác sĩ để xóa!');
    }
    
    // Commit transaction
    $conn->commit();
    
    echo json_encode([
        'success' => true,
        'message' => 'Xóa bác sĩ thành công!'
    ], JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    // Rollback nếu có lỗi
    $conn->rollback();
    
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}

$conn->close();
?>