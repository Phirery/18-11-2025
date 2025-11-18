<?php
require_once '../../config/cors.php';
require_once '../../core/dp.php';
require_once '../../core/session.php';

require_role('quantri');

$stats = [
    'total' => 0,
    'confirmed' => 0,
    'completed' => 0,
    'cancelled' => 0
];

$result = $conn->query("SELECT COUNT(*) as count FROM lichkham");
$stats['total'] = $result->fetch_assoc()['count'];

$result = $conn->query("SELECT COUNT(*) as count FROM lichkham WHERE trangThai = 'Đã đặt'");
$stats['confirmed'] = $result->fetch_assoc()['count'];

$result = $conn->query("SELECT COUNT(*) as count FROM lichkham WHERE trangThai = 'Hoàn thành'");
$stats['completed'] = $result->fetch_assoc()['count'];

$result = $conn->query("SELECT COUNT(*) as count FROM lichkham WHERE trangThai = 'Hủy'");
$stats['cancelled'] = $result->fetch_assoc()['count'];

echo json_encode($stats);
$conn->close();
?>