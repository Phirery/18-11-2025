<?php
// request-password-reset.php
require_once '../../config/cors.php';
require_once '../../core/dp.php';
require_once '../../core/session.php';

$nguoiDungId = $_SESSION['id'];
$vaiTro = $_SESSION['vaiTro'];

if ($vaiTro === 'quantri') {
    echo json_encode(['success' => false, 'message' => 'Quản trị viên không cần yêu cầu reset mật khẩu']);
    exit;
}

try {
    // Kiểm tra xem có yêu cầu đang chờ không
    $checkStmt = $conn->prepare("
        SELECT id FROM doimatkhau 
        WHERE nguoiDungId = ? AND trangThai = 'Chờ'
    ");
    $checkStmt->bind_param("i", $nguoiDungId);
    $checkStmt->execute();
    if ($checkStmt->get_result()->num_rows > 0) {
        throw new Exception('Bạn đã có yêu cầu đang chờ xử lý!');
    }
    $checkStmt->close();
    
    // Tạo yêu cầu mới
    // Trigger 'after_doimatkhau_insert' sẽ tự động tạo thông báo cho Admin
    $stmt = $conn->prepare("
        INSERT INTO doimatkhau (nguoiDungId) VALUES (?)
    ");
    $stmt->bind_param("i", $nguoiDungId);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Yêu cầu đã được gửi!']);
    } else {
        throw new Exception('Lỗi khi gửi yêu cầu');
    }
    $stmt->close();
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
$conn->close();
?>