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

    // JOIN with suatkham to get time details
    $sql = "SELECT h.maHoSo, h.ngayTao, h.chanDoan, h.dieuTri, h.ghiChu, h.ngayKham,
            bn.tenBenhNhan, bn.ngaySinh, bn.gioiTinh,
            l.ngayKham, c.tenCa,
            s.gioBatDau, s.gioKetThuc
            FROM hosobenhan h
            JOIN benhnhan bn ON h.maBenhNhan = bn.maBenhNhan
            LEFT JOIN lichkham l ON h.maLichKham = l.maLichKham
            LEFT JOIN calamviec c ON l.maCa = c.maCa
            LEFT JOIN suatkham s ON l.maSuat = s.maSuat
            WHERE h.maBacSi = ? AND h.trangThai = 'Chưa hoàn thành'
            ORDER BY h.ngayKham DESC, s.gioBatDau DESC";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $maBacSi);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $records = [];
    while ($row = $result->fetch_assoc()) {
        $records[] = $row;
    }
    $stmt->close();

    echo json_encode(['success' => true, 'data' => $records]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
}

$conn->close();
?>