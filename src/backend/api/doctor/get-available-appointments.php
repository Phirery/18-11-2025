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

    // Join with suatkham to get time details
    $sql = "SELECT l.maLichKham, l.ngayKham, 
            bn.tenBenhNhan, bn.ngaySinh, bn.gioiTinh,
            c.tenCa,
            s.gioBatDau, s.gioKetThuc
            FROM lichkham l
            JOIN benhnhan bn ON l.maBenhNhan = bn.maBenhNhan
            JOIN calamviec c ON l.maCa = c.maCa
            LEFT JOIN suatkham s ON l.maSuat = s.maSuat
            WHERE l.maBacSi = ? 
            AND l.trangThai IN ('Đã đặt', 'Hoàn thành')
            AND l.maLichKham NOT IN (SELECT maLichKham FROM hosobenhan WHERE maLichKham IS NOT NULL)
            ORDER BY l.ngayKham DESC, s.gioBatDau DESC";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $maBacSi);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $appointments = [];
    while ($row = $result->fetch_assoc()) {
        $appointments[] = $row;
    }
    $stmt->close();

    echo json_encode(['success' => true, 'data' => $appointments]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
}

$conn->close();
?>