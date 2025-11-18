<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

$conn = new mysqli("localhost", "root", "", "datlichkham");
$conn->set_charset("utf8mb4");

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Kết nối thất bại']);
    exit;
}

$sql = "SELECT
            lk.maLichKham,
            lk.maBenhNhan,
            lk.maBacSi,
            lk.ngayKham,
            lk.maCa,
            lk.maSuat,
            lk.maGoi,
            lk.trangThai,
            lk.ghiChu,
            bn.tenBenhNhan,
            bs.tenBacSi,
            ck.tenChuyenKhoa,
            k.tenKhoa,
            ca.tenCa,
            CONCAT(sk.gioBatDau, ' - ', sk.gioKetThuc) as gioSuat,
            gk.tenGoi
        FROM lichkham lk
        LEFT JOIN benhnhan bn ON lk.maBenhNhan = bn.maBenhNhan
        LEFT JOIN bacsi bs ON lk.maBacSi = bs.maBacSi
        LEFT JOIN chuyenkhoa ck ON bs.maChuyenKhoa = ck.maChuyenKhoa
        LEFT JOIN khoa k ON ck.maKhoa = k.maKhoa
        LEFT JOIN calamviec ca ON lk.maCa = ca.maCa
        LEFT JOIN suatkham sk ON lk.maSuat = sk.maSuat
        LEFT JOIN goikham gk ON lk.maGoi = gk.maGoi
        ORDER BY lk.ngayKham DESC, sk.gioBatDau DESC";

$result = $conn->query($sql);
$appointments = [];

if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $appointments[] = $row;
    }
}

echo json_encode([
    'success' => true,
    'data' => $appointments,
    'total' => count($appointments)
], JSON_UNESCAPED_UNICODE);

$conn->close();
?>