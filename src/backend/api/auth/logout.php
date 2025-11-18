<?php
require_once '../../config/cors.php';
require_once '../../core/session.php';

// Xóa toàn bộ session
$_SESSION = array();

// Xóa session cookie
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time()-3600, '/');
}

// Hủy session
session_destroy();

echo json_encode([
    'success' => true,
    'message' => 'Đăng xuất thành công'
]);
?>