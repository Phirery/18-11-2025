<?php
require_once '../../config/cors.php';
require_once '../../core/dp.php';

try {
    $sql = "SELECT maKhoa, tenKhoa, moTa FROM khoa ORDER BY tenKhoa";
    $result = $conn->query($sql);
    
    $departments = [];
    while ($row = $result->fetch_assoc()) {
        $departments[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'data' => $departments
    ], JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}

$conn->close();
?>