<?php
require_once '../../config/cors.php';
require_once '../../core/dp.php';

$maKhoa = $_GET['maKhoa'] ?? '';

if (empty($maKhoa)) {
    echo json_encode([
        'success' => false,
        'message' => 'Thiếu mã khoa'
    ]);
    exit;
}

try {
    $stmt = $conn->prepare("
        SELECT maChuyenKhoa, tenChuyenKhoa, moTa 
        FROM chuyenkhoa 
        WHERE maKhoa = ?
        ORDER BY tenChuyenKhoa
    ");
    $stmt->bind_param("s", $maKhoa);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $specialties = [];
    while ($row = $result->fetch_assoc()) {
        $specialties[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'data' => $specialties
    ], JSON_UNESCAPED_UNICODE);
    
    $stmt->close();
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}

$conn->close();
?>