<?php
require_once '../../config/cors.php';
require_once '../../core/dp.php';

try {
    $sql = "SELECT maGoi, tenGoi, moTa, gia FROM goikham ORDER BY gia";
    $result = $conn->query($sql);
    
    $packages = [];
    while ($row = $result->fetch_assoc()) {
        $packages[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'data' => $packages
    ], JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}

$conn->close();
?>