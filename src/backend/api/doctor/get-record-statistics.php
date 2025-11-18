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

    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM hosobenhan WHERE maBacSi = ?");
    $stmt->bind_param("s", $maBacSi);
    $stmt->execute();
    $totalRecords = $stmt->get_result()->fetch_assoc()['total'] ?? 0;
    $stmt->close();

    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM hosobenhan WHERE maBacSi = ? AND trangThai = 'Chưa hoàn thành'");
    $stmt->bind_param("s", $maBacSi);
    $stmt->execute();
    $pendingRecords = $stmt->get_result()->fetch_assoc()['total'] ?? 0;
    $stmt->close();

    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM hosobenhan WHERE maBacSi = ? AND trangThai = 'Đã hoàn thành'");
    $stmt->bind_param("s", $maBacSi);
    $stmt->execute();
    $completedRecords = $stmt->get_result()->fetch_assoc()['total'] ?? 0;
    $stmt->close();

    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM hosobenhan WHERE maBacSi = ? AND DATE(ngayTao) = CURDATE()");
    $stmt->bind_param("s", $maBacSi);
    $stmt->execute();
    $todayRecords = $stmt->get_result()->fetch_assoc()['total'] ?? 0;
    $stmt->close();

    echo json_encode([
        'success' => true,
        'data' => [
            'totalRecords' => (int)$totalRecords,
            'pendingRecords' => (int)$pendingRecords,
            'completedRecords' => (int)$completedRecords,
            'todayRecords' => (int)$todayRecords
        ]
    ]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
}

$conn->close();
?>