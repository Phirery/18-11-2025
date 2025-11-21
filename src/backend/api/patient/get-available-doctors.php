<?php
// src/backend/api/patient/get-available-doctors.php
require_once '../../config/cors.php';
require_once '../../core/dp.php';

$maChuyenKhoa = $_GET['maChuyenKhoa'] ?? '';
$ngayKham = $_GET['ngayKham'] ?? '';

if (empty($maChuyenKhoa) || empty($ngayKham)) {
    echo json_encode([
        'success' => false,
        'message' => 'Thiếu thông tin'
    ]);
    exit;
}

try {
    // Lấy TẤT CẢ bác sĩ thuộc chuyên khoa, kèm trạng thái khả dụng
    $stmt = $conn->prepare("
        SELECT 
            bs.maBacSi, 
            bs.tenBacSi, 
            ck.tenChuyenKhoa,
            CASE 
                WHEN EXISTS (
                    -- Nghỉ cả ngày (maCa = NULL)
                    SELECT 1 FROM ngaynghi n1
                    WHERE n1.maBacSi = bs.maBacSi
                    AND n1.ngayNghi = ?
                    AND n1.maCa IS NULL
                ) THEN 0
                WHEN (
                    -- Nghỉ cả 2 ca
                    SELECT COUNT(DISTINCT maCa) 
                    FROM ngaynghi n2
                    WHERE n2.maBacSi = bs.maBacSi
                    AND n2.ngayNghi = ?
                    AND n2.maCa IS NOT NULL
                ) >= 2 THEN 0
                ELSE 1
            END as available
        FROM bacsi bs
        JOIN chuyenkhoa ck ON bs.maChuyenKhoa = ck.maChuyenKhoa
        WHERE bs.maChuyenKhoa = ?
        ORDER BY bs.tenBacSi
    ");
    $stmt->bind_param("sss", $ngayKham, $ngayKham, $maChuyenKhoa);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $doctors = [];
    while ($row = $result->fetch_assoc()) {
        $doctors[] = [
            'maBacSi' => $row['maBacSi'],
            'tenBacSi' => $row['tenBacSi'],
            'tenChuyenKhoa' => $row['tenChuyenKhoa'],
            'available' => (bool)$row['available']
        ];
    }
    
    echo json_encode([
        'success' => true,
        'data' => $doctors
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