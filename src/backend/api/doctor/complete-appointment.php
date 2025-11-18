<?php
require_once '../../config/cors.php';
require_once '../../core/dp.php';
require_once '../../core/session.php';

require_role('bacsi');

$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['maLichKham'])) {
    echo json_encode(['success' => false, 'message' => 'Thiếu mã lịch khám']);
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

    $stmt = $conn->prepare("
        UPDATE lichkham 
        SET trangThai = 'Hoàn thành'
        WHERE maLichKham = ? AND maBacSi = ? AND trangThai = 'Đã đặt'
    ");
    $stmt->bind_param("is", $input['maLichKham'], $maBacSi);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo json_encode(['success' => true, 'message' => 'Cập nhật thành công']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Không thể cập nhật hoặc lịch đã hoàn thành']);
    }
    $stmt->close();
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
}
$conn->close();
?>
