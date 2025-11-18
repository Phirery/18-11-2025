<?php
require_once '../../config/cors.php';
require_once '../../core/dp.php';
require_once '../../core/session.php';

require_role('quantri');

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Kết nối thất bại']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$maThongBao = $input['maThongBao'] ?? '';

if (!$maThongBao) {
    echo json_encode(['success' => false, 'message' => 'Thiếu mã thông báo']);
    exit;
}

try {
    $stmt = $conn->prepare("UPDATE thongbaoadmin SET daXem = 1 WHERE maThongBao = ?");
    $stmt->bind_param("i", $maThongBao);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Cập nhật thất bại']);
    }
    $stmt->close();
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
}

$conn->close();
?>