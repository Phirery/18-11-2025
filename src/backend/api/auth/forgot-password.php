<?php
require_once '../../config/cors.php';
require_once '../../core/dp.php';

// Start session để lưu OTP
session_start();

// Get JSON input
$input = json_decode(file_get_contents("php://input"), true);

if (!isset($input['username'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Thiếu thông tin tên đăng nhập'
    ]);
    exit;
}

$username = trim($input['username']);

try {
    // Check if user exists
    $stmt = $conn->prepare("
        SELECT id, tenDangNhap, vaiTro 
        FROM nguoidung 
        WHERE (tenDangNhap = ? OR soDienThoai = ?) AND trangThai = 'Hoạt Động'
    ");
    $stmt->bind_param("ss", $username, $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Tài khoản không tồn tại hoặc đã bị khóa'
        ]);
        $stmt->close();
        $conn->close();
        exit;
    }
    
    $user = $result->fetch_assoc();
    $stmt->close();
    
    // Generate 6-digit OTP
    $otp = sprintf('%06d', mt_rand(0, 999999));
    
    // Store OTP in session with expiry (5 minutes)
    $_SESSION['forgot_otp'] = $otp;
    $_SESSION['forgot_otp_user'] = $user['id'];
    $_SESSION['forgot_otp_expiry'] = time() + 300; // 5 minutes
    
    echo json_encode([
        'success' => true,
        'message' => 'Mã OTP đã được tạo',
        'otp' => $otp
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi hệ thống: ' . $e->getMessage()
    ]);
}

$conn->close();
?>