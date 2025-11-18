<?php
require_once '../../config/cors.php';
require_once '../../core/dp.php';
require_once '../../core/session.php';

require_role('bacsi');

$input = json_decode(file_get_contents('php://input'), true);
$maThongBao = $input['maThongBao'] ?? '';

if (!$maThongBao) {
    echo json_encode(['success' => false, 'message' => 'Thiếu mã thông báo']);
    exit;
}

try {
    // Verify notification belongs to this doctor
    $stmt = $conn->prepare("SELECT maBacSi FROM bacsi WHERE nguoiDungId = ?");
    $stmt->bind_param("i", $_SESSION['id']);
    $stmt->execute();
    $maBacSi = $stmt->get_result()->fetch_assoc()['maBacSi'] ?? null;
    $stmt->close();

    if (!$maBacSi) {
        echo json_encode(['success' => false, 'message' => 'Không tìm thấy bác sĩ']);
        exit;
    }

    $stmt = $conn->prepare("DELETE FROM thongbaolichkham WHERE maThongBao = ? AND maBacSi = ?");
    $stmt->bind_param("is", $maThongBao, $maBacSi);
    
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