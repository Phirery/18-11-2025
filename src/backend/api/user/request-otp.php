<?php
require_once '../../config/cors.php';
require_once '../../core/dp.php';
require_once '../../core/session.php';

$nguoiDungId = $_SESSION['id'];

try {
    // Generate 6-digit OTP
    $otp = sprintf('%06d', mt_rand(0, 999999));
    
    // Store OTP in session with expiry (5 minutes)
    $_SESSION['otp'] = $otp;
    $_SESSION['otp_expiry'] = time() + 300;
    
    echo json_encode(['success' => true, 'otp' => $otp]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>