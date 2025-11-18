<?php
require_once '../../config/cors.php';
require_once '../../core/dp.php';
require_once '../../core/session.php';

require_role('quantri');

// Lấy danh sách khoa
$sql = "SELECT 
            maKhoa,
            tenKhoa,
            moTa
        FROM khoa
        ORDER BY tenKhoa ASC";

$result = $conn->query($sql);

if ($result === false) {
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi truy vấn: ' . $conn->error
    ]);
    $conn->close();
    exit;
}

$departments = [];

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $departments[] = [
            'maKhoa' => $row['maKhoa'],
            'tenKhoa' => $row['tenKhoa'],
            'moTa' => $row['moTa']
        ];
    }
}

echo json_encode([
    'success' => true,
    'data' => $departments
], JSON_UNESCAPED_UNICODE);

$conn->close();
?>