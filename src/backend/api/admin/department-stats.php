<?php
require_once '../../config/cors.php';
require_once '../../core/dp.php';
require_once '../../core/session.php';

require_role('quantri');

$stats = [
    'departments' => 0,
    'specialties' => 0,
    'doctors' => 0
];

// Tổng số khoa
$result = $conn->query("SELECT COUNT(*) as count FROM khoa");
if ($result) {
    $stats['departments'] = $result->fetch_assoc()['count'];
}

// Tổng số chuyên khoa
$result = $conn->query("SELECT COUNT(*) as count FROM chuyenkhoa");
if ($result) {
    $stats['specialties'] = $result->fetch_assoc()['count'];
}

// Tổng số bác sĩ
$result = $conn->query("SELECT COUNT(*) as count FROM bacsi");
if ($result) {
    $stats['doctors'] = $result->fetch_assoc()['count'];
}

echo json_encode($stats);
$conn->close();
?>