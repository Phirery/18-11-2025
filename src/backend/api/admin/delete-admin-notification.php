<?php
require_once '../../config/cors.php';
require_once '../../core/dp.php';
require_once '../../core/session.php';

require_role('quantri');

$input = json_decode(file_get_contents('php://input'), true);
$maThongBao = $input['maThongBao'] ?? '';

if (!$maThongBao) {
    echo json_encode(['success' => false, 'message' => 'Thiếu mã thông báo']);
    exit;
}

try {
    $stmt = $conn->prepare("DELETE FROM thongbaoadmin WHERE maThongBao = ?");
    $stmt->bind_param("i", $maThongBao);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Xóa thất bại']);
    }
    $stmt->close();
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
}

$conn->close();
?>