<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Kết nối database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "datlichkham";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(['error' => 'Connection failed']));
}

// Lấy thống kê
$stats = [
    'appointments' => 0,
    'patients' => 0,
    'doctors' => 0,
    'departments' => 0
];

// Đếm lịch khám
$result = $conn->query("SELECT COUNT(*) as count FROM lichkham");
$stats['appointments'] = $result->fetch_assoc()['count'];

// Đếm bệnh nhân
$result = $conn->query("SELECT COUNT(*) as count FROM benhnhan");
$stats['patients'] = $result->fetch_assoc()['count'];

// Đếm bác sĩ
$result = $conn->query("SELECT COUNT(*) as count FROM bacsi");
$stats['doctors'] = $result->fetch_assoc()['count'];

// Đếm khoa
$result = $conn->query("SELECT COUNT(*) as count FROM khoa");
$stats['departments'] = $result->fetch_assoc()['count'];

echo json_encode($stats);
$conn->close();
?>