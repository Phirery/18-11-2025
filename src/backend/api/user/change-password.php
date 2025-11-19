<?php
require_once '../../config/cors.php';
require_once '../../core/dp.php';
require_once '../../core/session.php';

$nguoiDungId = $_SESSION['id'];
$input = json_decode(file_get_contents('php://input'), true);

try {
    // Check 24h cooldown
    $stmt = $conn->prepare("SELECT matKhau, ngayCapNhatMatKhau FROM nguoidung WHERE id = ?");
    $stmt->bind_param("i", $nguoiDungId);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    
    if ($result['ngayCapNhatMatKhau']) {
        $hoursSinceChange = (time() - strtotime($result['ngayCapNhatMatKhau'])) / 3600;
        if ($hoursSinceChange < 24) {
            throw new Exception('Bạn chỉ có thể đổi mật khẩu sau 24 giờ!');
        }
    }
    
    // Verify current password
    if (!password_verify($input['currentPassword'], $result['matKhau'])) {
        throw new Exception('Mật khẩu hiện tại không đúng!');
    }
    
    // Update password
    $newPasswordHash = password_hash($input['newPassword'], PASSWORD_DEFAULT);
    $stmt = $conn->prepare("UPDATE nguoidung SET matKhau = ?, ngayCapNhatMatKhau = NOW() WHERE id = ?");
    $stmt->bind_param("si", $newPasswordHash, $nguoiDungId);
    $stmt->execute();
    $stmt->close();
    
    echo json_encode(['success' => true, 'message' => 'Đổi mật khẩu thành công!']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
$conn->close();
?>