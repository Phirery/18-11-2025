<?php
require_once '../../config/cors.php';
require_once '../../core/dp.php';
require_once '../../core/session.php';

require_role('quantri');

$stats = [
    'total' => 0,
    'patients' => 0,
    'doctors' => 0,
    'admins' => 0,
];

// Lấy tổng số tài khoản người dùng
$result = $conn->query("SELECT COUNT(*) as total FROM nguoidung");
if ($result) {
    $stats['total'] = $result->fetch_assoc()['total'];}

// Lấy tổng số tài khoản bệnh nhân
$result = $conn->query("SELECT COUNT(*) as total FROM nguoidung WHERE vaiTro = 'benhnhan'");
if ($result) {
    $stats['patients'] = $result->fetch_assoc()['total'];}

// Lấy tổng số tài khoản bác sĩ
$result = $conn->query("SELECT COUNT(*) as total FROM nguoidung WHERE vaiTro = 'bacsi'");
if ($result) {
    $stats['doctors'] = $result->fetch_assoc()['total'];}

// Lấy tổng số tài khoản quản trị viên
$result = $conn->query("SELECT COUNT(*) as total FROM nguoidung WHERE vaiTro = 'quantri'");
if ($result) {
    $stats['admins'] = $result->fetch_assoc()['total'];}

echo json_encode(array_merge(['success' => true], $stats));

$conn->close();
?>
