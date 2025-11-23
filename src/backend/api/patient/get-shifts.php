<?php
require_once '../../config/cors.php';
require_once '../../core/dp.php';

try {
    $sql = "SELECT maCa, tenCa, gioBatDau, gioKetThuc FROM calamviec ORDER BY gioBatDau";
    $result = $conn->query($sql);
    
    $shifts = [];
    while ($row = $result->fetch_assoc()) {
        $shifts[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'data' => $shifts
    ], JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}

$conn->close();
?>