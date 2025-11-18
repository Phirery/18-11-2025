<?php
require_once '../../config/cors.php';
require_once '../../core/dp.php';
require_once '../../core/session.php';

require_role('quantri');

// Lấy dữ liệu POST
$data = json_decode(file_get_contents('php://input'), true);
$maLichKham = $conn->real_escape_string($data['maLichKham']);

// Xóa lịch khám
$sql = "DELETE FROM lichkham WHERE maLichKham = '$maLichKham'";

if ($conn->query($sql) === TRUE) {
    if ($conn->affected_rows > 0) {
        echo json_encode([
            'success' => true,
            'message' => 'Xóa lịch khám thành công!'
        ], JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Không tìm thấy lịch khám để xóa'
        ], JSON_UNESCAPED_UNICODE);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi: ' . $conn->error
    ], JSON_UNESCAPED_UNICODE);
}

$conn->close();
?>