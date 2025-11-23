<?php
require_once '../../config/cors.php';
require_once '../../core/dp.php';

$maBacSi = $_GET['maBacSi'] ?? '';
$ngayKham = $_GET['ngayKham'] ?? '';
$maCa = $_GET['maCa'] ?? '';

if (empty($maBacSi) || empty($ngayKham) || empty($maCa)) {
    echo json_encode([
        'success' => false,
        'message' => 'Thiếu thông tin'
    ]);
    exit;
}

try {
    // Kiểm tra xem bác sĩ có nghỉ ca này không
    $checkStmt = $conn->prepare("
        SELECT COUNT(*) as count 
        FROM ngaynghi 
        WHERE maBacSi = ? AND ngayNghi = ? AND (maCa = ? OR maCa IS NULL)
    ");
    $checkStmt->bind_param("ssi", $maBacSi, $ngayKham, $maCa);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    $isOff = $checkResult->fetch_assoc()['count'] > 0;
    $checkStmt->close();
    
    // Lấy TẤT CẢ các suất khám của ca, kèm trạng thái
    $stmt = $conn->prepare("
        SELECT 
            sk.maSuat, 
            sk.gioBatDau, 
            sk.gioKetThuc,
            CASE 
                -- Nếu bác sĩ nghỉ ca này -> tất cả suất đều unavailable
                WHEN ? = 1 THEN 0
                -- Kiểm tra suất đã được đặt chưa
                WHEN EXISTS (
                    SELECT 1 FROM lichkham lk 
                    WHERE lk.maBacSi = ? 
                    AND lk.ngayKham = ? 
                    AND lk.maSuat = sk.maSuat
                    AND lk.trangThai != 'Hủy'
                ) THEN 0
                ELSE 1
            END as available
        FROM suatkham sk
        WHERE sk.maCa = ?
        ORDER BY sk.gioBatDau
    ");
    $stmt->bind_param("issi", $isOff, $maBacSi, $ngayKham, $maCa);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $slots = [];
    while ($row = $result->fetch_assoc()) {
        $slots[] = [
            'maSuat' => $row['maSuat'],
            'gioBatDau' => $row['gioBatDau'],
            'gioKetThuc' => $row['gioKetThuc'],
            'available' => (bool)$row['available']
        ];
    }
    
    echo json_encode([
        'success' => true,
        'data' => $slots,
        'doctorOff' => $isOff
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