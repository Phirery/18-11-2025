<?php
require_once '../../config/cors.php';
require_once '../../core/dp.php';
require_once '../../core/session.php';

require_role('quantri');

try {
    $sql = "SELECT t.maThongBao, t.maNghi, t.loai, t.tieuDe, t.noiDung, t.thoiGian, t.daXem,
            bs.tenBacSi, n.ngayNghi, c.tenCa
            FROM thongbaoadmin t
            JOIN bacsi bs ON t.maBacSi = bs.maBacSi
            LEFT JOIN ngaynghi n ON t.maNghi = n.maNghi
            LEFT JOIN calamviec c ON n.maCa = c.maCa
            ORDER BY t.thoiGian DESC";
    
    $result = $conn->query($sql);
    
    $notifications = [];
    while ($row = $result->fetch_assoc()) {
        $row['daXem'] = (bool)$row['daXem'];
        $row['ngayNghi'] = $row['ngayNghi'] ? date('d/m/Y', strtotime($row['ngayNghi'])) : null;
        $notifications[] = $row;
    }

    echo json_encode(['success' => true, 'data' => $notifications]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
}

$conn->close();
?>