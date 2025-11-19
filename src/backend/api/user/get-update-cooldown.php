<?php
require_once '../../config/cors.php';
require_once '../../core/dp.php';
require_once '../../core/session.php';

$nguoiDungId = $_SESSION['id'];

try {
    $stmt = $conn->prepare("SELECT ngayCapNhatTaiKhoan FROM nguoidung WHERE id = ?");
    $stmt->bind_param("i", $nguoiDungId);
    $stmt->execute();
    $lastUpdate = $stmt->get_result()->fetch_assoc()['ngayCapNhatTaiKhoan'];
    $stmt->close();
    
    if (!$lastUpdate) {
        echo json_encode(['success' => true, 'canUpdate' => true]);
        exit;
    }
    
    $hoursSinceUpdate = (time() - strtotime($lastUpdate)) / 3600;
    $canUpdate = $hoursSinceUpdate >= 24;
    
    $remainingHours = max(0, 24 - $hoursSinceUpdate);
    $remainingTime = sprintf('%d giờ %d phút', floor($remainingHours), ($remainingHours - floor($remainingHours)) * 60);
    
    echo json_encode([
        'success' => true,
        'canUpdate' => $canUpdate,
        'remainingTime' => $remainingTime
    ]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
$conn->close();
?>