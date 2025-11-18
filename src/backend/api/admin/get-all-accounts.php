<?php
require_once '../../config/cors.php';
require_once '../../core/dp.php';
require_once '../../core/session.php';

require_role('quantri');

$sql = "
SELECT 
    nd.id, 
    nd.tenDangNhap, 
    nd.soDienThoai, 
    nd.vaiTro, 
    nd.trangThai,
    CASE 
        WHEN nd.vaiTro = 'benhnhan' THEN bn.tenBenhNhan
        WHEN nd.vaiTro = 'bacsi' THEN bs.tenBacSi
        ELSE 'Admin'
    END AS hoTen, 
    CASE 
        WHEN nd.vaiTro = 'benhnhan' THEN bn.ngaySinh
        ELSE NULL
    END AS ngaySinh, 
    CASE 
        WHEN nd.vaiTro = 'benhnhan' THEN bn.gioiTinh
        ELSE NULL
    END AS gioiTinh, 
    CASE 
        WHEN nd.vaiTro = 'benhnhan' THEN bn.soTheBHYT
        ELSE NULL
    END AS soTheBHYT, 
    CASE 
        WHEN nd.vaiTro = 'bacsi' THEN bs.maBacSi
        ELSE NULL
    END AS maBacSi, 
    CASE 
        WHEN nd.vaiTro = 'bacsi' THEN ck.tenChuyenKhoa
        ELSE NULL
    END AS tenChuyenKhoa
FROM nguoidung nd
LEFT JOIN benhnhan bn ON nd.id = bn.nguoiDungId
LEFT JOIN bacsi bs ON nd.id = bs.nguoiDungId
LEFT JOIN chuyenkhoa ck ON bs.maChuyenKhoa = ck.maChuyenKhoa
ORDER BY nd.id DESC
";

// Thực thi truy vấn
$result = $conn->query($sql);

if ($result) {
    $accounts = [];
    while ($row = $result->fetch_assoc()) {
        $accounts[] = $row;
    }

    echo json_encode([
        'success' => true,
        'data' => $accounts
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => $conn->error
    ]);
}

$conn->close();
?>