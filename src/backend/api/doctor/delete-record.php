<?php
require_once '../../config/cors.php';
require_once '../../core/dp.php';
require_once '../../core/session.php';

require_role('bacsi');

$input = json_decode(file_get_contents('php://input'), true);
$maHoSo = $input['maHoSo'] ?? '';

if (!$maHoSo) {
    echo json_encode(['success' => false, 'message' => 'Thiếu mã hồ sơ']);
    exit;
}

try {
    $stmt = $conn->prepare("SELECT maBacSi FROM bacsi WHERE nguoiDungId = ?");
    $stmt->bind_param("i", $_SESSION['id']);
    $stmt->execute();
    $maBacSi = $stmt->get_result()->fetch_assoc()['maBacSi'] ?? null;
    $stmt->close();

    if (!$maBacSi) {
        echo json_encode(['success' => false, 'message' => 'Không tìm thấy bác sĩ']);
        exit;
    }

    // Verify ownership before deleting
    $stmt = $conn->prepare("DELETE FROM hosobenhan WHERE maHoSo = ? AND maBacSi = ?");
    $stmt->bind_param("ss", $maHoSo, $maBacSi);
    
    if ($stmt->execute() && $stmt->affected_rows > 0) {
        echo json_encode(['success' => true, 'message' => 'Xóa thành công']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Xóa thất bại hoặc không có quyền']);
    }
    $stmt->close();
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
}

$conn->close();
?>