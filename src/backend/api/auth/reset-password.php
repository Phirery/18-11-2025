<?php
require_once '../../config/cors.php';
require_once '../../core/dp.php';

// Start session để kiểm tra OTP
session_start();

// Get JSON input
$input = json_decode(file_get_contents("php://input"), true);

if (!isset($input['username'], $input['otp'], $input['newPassword'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Thiếu thông tin cần thiết'
    ]);
    exit;
}

$username = trim($input['username']);
$otp = trim($input['otp']);
$newPassword = $input['newPassword'];

try {
    // Validate OTP from session
    if (!isset($_SESSION['forgot_otp']) || !isset($_SESSION['forgot_otp_user']) || !isset($_SESSION['forgot_otp_expiry'])) {
        echo json_encode([
            'success' => false,
            'message' => 'Không có mã OTP nào được tạo. Vui lòng yêu cầu mã mới.'
        ]);
        exit;
    }
    
    // Check OTP expiry
    if (time() > $_SESSION['forgot_otp_expiry']) {
        unset($_SESSION['forgot_otp'], $_SESSION['forgot_otp_user'], $_SESSION['forgot_otp_expiry']);
        echo json_encode([
            'success' => false,
            'message' => 'Mã OTP đã hết hạn. Vui lòng yêu cầu mã mới.'
        ]);
        exit;
    }
    
    // Verify OTP
    if ($otp !== $_SESSION['forgot_otp']) {
        echo json_encode([
            'success' => false,
            'message' => 'Mã OTP không chính xác!'
        ]);
        exit;
    }
    
    // Validate password length
    if (strlen($newPassword) < 6) {
        echo json_encode([
            'success' => false,
            'message' => 'Mật khẩu phải có ít nhất 6 ký tự!'
        ]);
        exit;
    }
    
    $userId = $_SESSION['forgot_otp_user'];
    
    // Verify username matches the OTP session
    $stmt = $conn->prepare("
        SELECT id FROM nguoidung 
        WHERE id = ? AND (tenDangNhap = ? OR soDienThoai = ?)
    ");
    $stmt->bind_param("iss", $userId, $username, $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Thông tin không khớp!'
        ]);
        $stmt->close();
        exit;
    }
    $stmt->close();
    
    // Hash new password
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    
    // Update password and reset ngayCapNhatMatKhau to allow immediate login
    $stmt = $conn->prepare("
        UPDATE nguoidung 
        SET matKhau = ?, ngayCapNhatMatKhau = NULL 
        WHERE id = ?
    ");
    $stmt->bind_param("si", $hashedPassword, $userId);
    $stmt->execute();
    $stmt->close();
    
    // Clear OTP from session
    unset($_SESSION['forgot_otp'], $_SESSION['forgot_otp_user'], $_SESSION['forgot_otp_expiry']);
    
    echo json_encode([
        'success' => true,
        'message' => 'Đổi mật khẩu thành công!'
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi hệ thống: ' . $e->getMessage()
    ]);
}

$conn->close();
?>