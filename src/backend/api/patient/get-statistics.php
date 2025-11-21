<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "datlichkham";

try {
    $conn = new mysqli($servername, $username, $password, $dbname);
    $conn->set_charset("utf8mb4");

    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    $stats = [
        'patients' => 0,
        'doctors' => 0,
        'specialties' => 0,
        'appointments' => 0
    ];

    // Count patients
    $result = $conn->query("SELECT COUNT(*) as count FROM benhnhan");
    if ($result) {
        $stats['patients'] = (int)$result->fetch_assoc()['count'];
    }

    // Count doctors
    $result = $conn->query("SELECT COUNT(*) as count FROM bacsi");
    if ($result) {
        $stats['doctors'] = (int)$result->fetch_assoc()['count'];
    }

    // Count specialties
    $result = $conn->query("SELECT COUNT(*) as count FROM chuyenkhoa");
    if ($result) {
        $stats['specialties'] = (int)$result->fetch_assoc()['count'];
    }

    // Count appointments
    $result = $conn->query("SELECT COUNT(*) as count FROM lichkham");
    if ($result) {
        $stats['appointments'] = (int)$result->fetch_assoc()['count'];
    }

    echo json_encode([
        'success' => true,
        'data' => $stats
    ], JSON_UNESCAPED_UNICODE);

    $conn->close();

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
?>