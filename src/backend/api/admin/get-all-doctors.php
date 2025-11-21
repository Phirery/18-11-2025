<?php
require_once '../../config/cors.php';
require_once '../../core/dp.php';
require_once '../../core/session.php';

require_role('quantri');

$sql = "SELECT 
            bs.maBacSi, bs.tenBacSi, bs.nguoiDungId, bs.maChuyenKhoa, bs.moTa,
            bs.gioiTinh, bs.namLamViec,
            ck.tenChuyenKhoa, ck.maKhoa, k.tenKhoa,
            nd.soDienThoai, nd.tenDangNhap
        FROM bacsi bs
        LEFT JOIN chuyenkhoa ck ON bs.maChuyenKhoa = ck.maChuyenKhoa
        LEFT JOIN khoa k ON ck.maKhoa = k.maKhoa
        LEFT JOIN nguoidung nd ON bs.nguoiDungId = nd.id
        ORDER BY bs.tenBacSi ASC";

$result = $conn->query($sql);

if ($result === false) {
    echo json_encode(['success' => false, 'message' => 'Lỗi truy vấn: ' . $conn->error]);
    $conn->close();
    exit;
}

$doctors = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $doctors[] = [
            'maBacSi' => $row['maBacSi'],
            'tenBacSi' => $row['tenBacSi'],
            'nguoiDungId' => $row['nguoiDungId'],
            'maChuyenKhoa' => $row['maChuyenKhoa'],
            'tenChuyenKhoa' => $row['tenChuyenKhoa'],
            'maKhoa' => $row['maKhoa'],
            'tenKhoa' => $row['tenKhoa'],
            'soDienThoai' => $row['soDienThoai'],
            'tenDangNhap' => $row['tenDangNhap'],
            'moTa' => $row['moTa'],
            'gioiTinh' => $row['gioiTinh'],
            'namLamViec' => $row['namLamViec']
        ];
    }
}

echo json_encode(['success' => true, 'data' => $doctors, 'total' => count($doctors)], JSON_UNESCAPED_UNICODE);
$conn->close();
?>