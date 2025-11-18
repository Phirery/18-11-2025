<?php
require_once '../../config/cors.php';
require_once '../../core/dp.php';
require_once '../../core/session.php';

require_role('quantri');

try {
    $result = $conn->query("DELETE FROM thongbaoadmin WHERE daXem = 1");
    
    if ($result) {
        $deleted = $conn->affected_rows;
        echo json_encode(['success' => true, 'deleted' => $deleted, 'message' => "Đã xóa $deleted thông báo."]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Xóa thất bại: ' . $conn->error]);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
}

$conn->close();
?>