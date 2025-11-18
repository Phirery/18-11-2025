<?php
require_once '../../config/cors.php';
require_once '../../core/dp.php';
require_once '../../core/session.php';

require_role('bacsi');

try {
    $stmt = $conn->prepare("SELECT maBacSi FROM bacsi WHERE nguoiDungId = ?");
    $stmt->bind_param("i", $_SESSION['id']);
    $stmt->execute();
    $maBacSi = $stmt->get_result()->fetch_assoc()['maBacSi'] ?? null;
    $stmt->close();

    if (!$maBacSi) {
        echo json_encode(['success' => false, 'message' => 'Không tìm thấy bác sĩ']);
        exit;
    }

    $stmt = $conn->prepare("
        SELECT 
            bn.maBenhNhan,
            bn.tenBenhNhan,
            bn.ngaySinh,
            bn.gioiTinh,
            bn.soTheBHYT,
            nd.soDienThoai,
            COUNT(lk.maLichKham) as soLanKham,
            MAX(lk.ngayKham) as lanKhamGanNhat
        FROM benhnhan bn
        JOIN nguoidung nd ON bn.nguoiDungId = nd.id
        JOIN lichkham lk ON bn.maBenhNhan = lk.maBenhNhan
        WHERE lk.maBacSi = ?
        GROUP BY bn.maBenhNhan, bn.tenBenhNhan, bn.ngaySinh, bn.gioiTinh, bn.soTheBHYT, nd.soDienThoai
        ORDER BY lanKhamGanNhat DESC
    ");
    $stmt->bind_param("s", $maBacSi);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $patients = [];
    while ($row = $result->fetch_assoc()) {
        $patients[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'data' => $patients
    ]);
    
    $stmt->close();
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
}

$conn->close();
?>