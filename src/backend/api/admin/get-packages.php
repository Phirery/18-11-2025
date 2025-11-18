<?php
require_once '../../config/cors.php';
require_once '../../core/dp.php';
require_once '../../core/session.php';

require_role('quantri');

$sql = "SELECT maGoi, tenGoi, moTa, thoiLuong, gia FROM goikham ORDER BY gia";
$result = $conn->query($sql);
$packages = [];

if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $packages[] = $row;
    }
}

echo json_encode(['success' => true, 'data' => $packages], JSON_UNESCAPED_UNICODE);
$conn->close();
?>