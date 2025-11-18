<?php
require_once '../../config/cors.php';
require_once '../../core/dp.php';
require_once '../../core/session.php';

require_role('quantri');

// Lấy danh sách bác sĩ với thông tin chuyên khoa
$sql = "SELECT 
            bs.maBacSi,
            bs.tenBacSi,
            bs.maChuyenKhoa,
            ck.tenChuyenKhoa,
            k.tenKhoa,
            nd.soDienThoai
        FROM bacsi bs
        LEFT JOIN chuyenkhoa ck ON bs.maChuyenKhoa = ck.maChuyenKhoa
        LEFT JOIN khoa k ON ck.maKhoa = k.maKhoa
        LEFT JOIN nguoidung nd ON bs.nguoiDungId = nd.id
        ORDER BY bs.tenBacSi ASC";

$result = $conn->query($sql);

if ($result === false) {
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi truy vấn: ' . $conn->error
    ]);
    $conn->close();
    exit;
}

$doctors = [];

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $doctors[] = [
            'maBacSi' => $row['maBacSi'],
            'tenBacSi' => $row['tenBacSi'],
            'maChuyenKhoa' => $row['maChuyenKhoa'],
            'tenChuyenKhoa' => $row['tenChuyenKhoa'] ?? 'N/A',
            'tenKhoa' => $row['tenKhoa'] ?? 'N/A',
            'soDienThoai' => $row['soDienThoai']
        ];
    }
}

echo json_encode([
    'success' => true,
    'data' => $doctors
], JSON_UNESCAPED_UNICODE);

$conn->close();
?>