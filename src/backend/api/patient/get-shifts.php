<?php
// src/backend/api/patient/get-available-shifts.php
require_once '../../config/cors.php';
require_once '../../core/dp.php';

$maBacSi = $_GET['maBacSi'] ?? '';
$ngayKham = $_GET['ngayKham'] ?? '';

if (empty($maBacSi) || empty($ngayKham)) {
    echo json_encode([
        'success' => false,
        'message' => 'Thiếu thông tin'
    ]);
    exit;
}

try {
    // Lấy tất cả ca làm việc và đánh dấu ca nào bác sĩ nghỉ
    $stmt = $conn->prepare("
        SELECT 
            c.maCa, 
            c.tenCa, 
            c.gioBatDau, 
            c.gioKetThuc,
            CASE 
                WHEN EXISTS (
                    SELECT 1 FROM ngaynghi n
                    WHERE n.maBacSi = ?
                    AND n.ngayNghi = ?
                    AND (n.maCa = c.maCa OR n.maCa IS NULL)
                ) THEN 0
                ELSE 1
            END as available
        FROM calamviec c
        ORDER BY c.gioBatDau
    ");
    $stmt->bind_param("ss", $maBacSi, $ngayKham);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $shifts = [];
    while ($row = $result->fetch_assoc()) {
        $shifts[] = [
            'maCa' => $row['maCa'],
            'tenCa' => $row['tenCa'],
            'gioBatDau' => $row['gioBatDau'],
            'gioKetThuc' => $row['gioKetThuc'],
            'available' => (bool)$row['available']
        ];
    }
    
    echo json_encode([
        'success' => true,
        'data' => $shifts
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