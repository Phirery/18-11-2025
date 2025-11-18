<?php
require_once '../../config/cors.php';
require_once '../../core/dp.php';
require_once '../../core/session.php';

require_role('quantri');

try {
    $stats = [
        'appointments' => 0,
        'patients' => 0,
        'doctors' => 0,
        'departments' => 0
    ];

    // Đếm lịch khám
    $result = $conn->query("SELECT COUNT(*) as count FROM lichkham");
    if ($result) {
        $stats['appointments'] = $result->fetch_assoc()['count'];
    }

    // Đếm bệnh nhân
    $result = $conn->query("SELECT COUNT(*) as count FROM benhnhan");
    if ($result) {
        $stats['patients'] = $result->fetch_assoc()['count'];
    }

    // Đếm bác sĩ
    $result = $conn->query("SELECT COUNT(*) as count FROM bacsi");
    if ($result) {
        $stats['doctors'] = $result->fetch_assoc()['count'];
    }

    // Đếm khoa
    $result = $conn->query("SELECT COUNT(*) as count FROM khoa");
    if ($result) {
        $stats['departments'] = $result->fetch_assoc()['count'];
    }

    echo json_encode([
        "success" => true,
        "data" => $stats
    ]);
} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "message" => "Lỗi: " . $e->getMessage()
    ]);
}

$conn->close();
?>