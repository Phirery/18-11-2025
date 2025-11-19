<?php
require_once '../../config/cors.php';
require_once '../../core/dp.php';
require_once '../../core/session.php';

$nguoiDungId = $_SESSION['id'];

try {
    $stmt = $conn->prepare("SELECT ngayCapNhatMatKhau FROM nguoidung WHERE id = ?");
    $stmt->bind_param("i", $nguoiDungId);
    $stmt->execute();
    $lastChange = $stmt->get_result()->fetch_assoc()['ngayCapNhatMatKhau'];
    $stmt->close();
    
    if (!$lastChange) {
        echo json_encode(['success' => true, 'canChange' => true]);
        exit;
    }
    
    $hoursSinceChange = (time() - strtotime($lastChange)) / 3600;
    $canChange = $hoursSinceChange >= 24;
    
    $remainingHours = max(0, 24 - $hoursSinceChange);
    $remainingTime = sprintf('%d giờ %d phút', floor($remainingHours), ($remainingHours - floor($remainingHours)) * 60);
    
    echo json_encode([
        'success' => true,
        'canChange' => $canChange,
        'remainingTime' => $remainingTime
    ]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
$conn->close();
?>