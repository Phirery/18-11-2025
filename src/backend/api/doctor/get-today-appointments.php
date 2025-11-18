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
            lk.maLichKham, lk.trangThai,
            bn.tenBenhNhan, bn.ngaySinh, bn.gioiTinh,
            ca.tenCa, ca.maCa,
            sk.gioBatDau, sk.gioKetThuc,
            gk.tenGoi
        FROM lichkham lk
        JOIN benhnhan bn ON lk.maBenhNhan = bn.maBenhNhan
        JOIN calamviec ca ON lk.maCa = ca.maCa
        JOIN suatkham sk ON lk.maSuat = sk.maSuat
        JOIN goikham gk ON lk.maGoi = gk.maGoi
        WHERE lk.maBacSi = ? AND lk.ngayKham = CURDATE()
        ORDER BY ca.maCa, sk.gioBatDau
    ");
    $stmt->bind_param("s", $maBacSi);
    $stmt->execute();
    $result = $stmt->get_result();

    $appointments = [];
    while ($row = $result->fetch_assoc()) {
        $appointments[] = $row;
    }

    echo json_encode(['success' => true, 'data' => $appointments]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
}
$conn->close();
?>