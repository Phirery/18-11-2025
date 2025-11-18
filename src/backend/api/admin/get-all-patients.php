<?php
require_once '../../config/cors.php';
require_once '../../core/dp.php';
require_once '../../core/session.php';

require_role('quantri');

try {
    $sql = "SELECT 
                b.maBenhNhan, b.tenBenhNhan, b.ngaySinh, b.gioiTinh, b.soTheBHYT,
                n.tenDangNhap, n.soDienThoai
            FROM benhnhan b
            JOIN nguoidung n ON b.nguoiDungId = n.id
            WHERE n.vaiTro = 'benhnhan'
            ORDER BY b.maBenhNhan DESC";

    $result = $conn->query($sql);
    $patients = [];

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $patients[] = $row;
        }
    }

    echo json_encode([
        'success' => true,
        'data' => $patients
    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}

$conn->close();
?>