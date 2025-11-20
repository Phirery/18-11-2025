<?php
require_once '../../config/cors.php';
require_once '../../core/dp.php';
require_once '../../core/session.php';

require_role('quantri');

$input = json_decode(file_get_contents('php://input'), true);
$requestId = $input['requestId'];

try {
    $stmt = $conn->prepare("DELETE FROM doimatkhau WHERE id = ?");
    $stmt->bind_param("i", $requestId);
    
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(['success' => true, 'message' => 'Xóa yêu cầu thành công!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Không tìm thấy yêu cầu!']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Xóa thất bại!']);
    }
    $stmt->close();
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
}
$conn->close();
?>