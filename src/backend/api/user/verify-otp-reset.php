<?php
require_once '../../config/cors.php';
require_once '../../core/dp.php';
require_once '../../core/session.php';

$nguoiDungId = $_SESSION['id'];
$input = json_decode(file_get_contents('php://input'), true);

try {
    // Check OTP validity
    if (!isset($_SESSION['otp']) || !isset($_SESSION['otp_expiry'])) {
        throw new Exception('Không có mã OTP nào được tạo!');
    }
    
    if (time() > $_SESSION['otp_expiry']) {
        unset($_SESSION['otp'], $_SESSION['otp_expiry']);
        throw new Exception('Mã OTP đã hết hạn!');
    }
    
    if ($input['otp'] !== $_SESSION['otp']) {
        throw new Exception('Mã OTP không chính xác!');
    }
    
    // Clear OTP
    unset($_SESSION['otp'], $_SESSION['otp_expiry']);
    
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