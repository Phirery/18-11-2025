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

    $sql = "SELECT t.maThongBao, t.loai, t.tieuDe, t.noiDung, t.thoiGian, t.daXem,
            bn.tenBenhNhan, l.ngayKham, c.tenCa
            FROM thongbaolichkham t
            LEFT JOIN lichkham l ON t.maLichKham = l.maLichKham
            LEFT JOIN benhnhan bn ON l.maBenhNhan = bn.maBenhNhan
            LEFT JOIN calamviec c ON l.maCa = c.maCa
            WHERE t.maBacSi = ?
            ORDER BY t.thoiGian DESC";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $maBacSi);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $notifications = [];
    while ($row = $result->fetch_assoc()) {
        $row['daXem'] = (bool)$row['daXem'];
        $notifications[] = $row;
    }
    $stmt->close();

    echo json_encode(['success' => true, 'data' => $notifications]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
}

$conn->close();
?>