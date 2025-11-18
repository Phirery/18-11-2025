<?php
require_once '../../config/cors.php';
require_once '../../core/dp.php';
require_once '../../core/session.php';

require_role('bacsi');

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

    $stmt = $conn->prepare("SELECT COUNT(DISTINCT maBenhNhan) AS total FROM lichkham WHERE maBacSi = ?");
    $stmt->bind_param("s", $maBacSi);
    $stmt->execute();
    $totalPatients = $stmt->get_result()->fetch_assoc()['total'] ?? 0;
    $stmt->close();

    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM lichkham WHERE maBacSi = ? AND ngayKham = CURDATE()");
    $stmt->bind_param("s", $maBacSi);
    $stmt->execute();
    $todayCount = $stmt->get_result()->fetch_assoc()['total'] ?? 0;
    $stmt->close();

    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM lichkham WHERE maBacSi = ? AND trangThai = 'Hoàn thành'");
    $stmt->bind_param("s", $maBacSi);
    $stmt->execute();
    $completedCount = $stmt->get_result()->fetch_assoc()['total'] ?? 0;
    $stmt->close();

    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM lichkham WHERE maBacSi = ? AND trangThai = 'Đã đặt'");
    $stmt->bind_param("s", $maBacSi);
    $stmt->execute();
    $pendingCount = $stmt->get_result()->fetch_assoc()['total'] ?? 0;
    $stmt->close();

    echo json_encode([
        'success' => true,
        'data' => [
            'totalPatients' => (int)$totalPatients,
            'todayCount' => (int)$todayCount,
            'completedCount' => (int)$completedCount,
            'pendingCount' => (int)$pendingCount
        ]
    ]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
}
$conn->close();
?>