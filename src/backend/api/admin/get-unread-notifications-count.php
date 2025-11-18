<?php
require_once '../../config/cors.php';
require_once '../../core/dp.php';
require_once '../../core/session.php';

require_role('quantri');

try {
    // Đếm số thông báo chưa đọc
    $stmt = $conn->prepare("
        SELECT COUNT(*) as count 
        FROM thongbaoadmin 
        WHERE daXem = 0
    ");
    
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    echo json_encode([
        "success" => true,
        "count" => (int)$row['count']
    ]);
    
    $stmt->close();
} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "message" => "Lỗi: " . $e->getMessage(),
        "count" => 0
    ]);
}

$conn->close();
?>