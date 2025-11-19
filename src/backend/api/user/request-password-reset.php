<?php
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
    // Check if there's already a pending request
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
    
    // Insert new request
    $stmt = $conn->prepare("
        INSERT INTO doimatkhau (nguoiDungId) VALUES (?)
    ");
    $stmt->bind_param("i", $nguoiDungId);
    $stmt->execute();
    $stmt->close();
    
    // Create notification for admin
    $stmt = $conn->prepare("
        INSERT INTO thongbaoadmin (maBacSi, loai, tieuDe, noiDung, thoiGian)
        SELECT 
            CASE 
                WHEN ? = 'bacsi' THEN (SELECT maBacSi FROM bacsi WHERE nguoiDungId = ?)
                ELSE 'SYSTEM'
            END,
            'Nghỉ phép',
            'Yêu cầu cấp lại mật khẩu',
            CONCAT('Người dùng ', nd.tenDangNhap, ' yêu cầu cấp lại mật khẩu'),
            NOW()
        FROM nguoidung nd
        WHERE nd.id = ?
    ");
    $stmt->bind_param("sii", $vaiTro, $nguoiDungId, $nguoiDungId);
    $stmt->execute();
    $stmt->close();
    
    echo json_encode(['success' => true, 'message' => 'Yêu cầu đã được gửi!']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
$conn->close();
?>