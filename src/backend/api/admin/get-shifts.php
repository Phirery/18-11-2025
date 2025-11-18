<?php
require_once '../../config/cors.php';
require_once '../../core/dp.php';
require_once '../../core/session.php';

require_role('quantri');

$sql = "SELECT maCa, tenCa, gioBatDau, gioKetThuc FROM calamviec ORDER BY gioBatDau";
$result = $conn->query($sql);
$shifts = [];

if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $shifts[] = $row;
    }
}

echo json_encode(['success' => true, 'data' => $shifts], JSON_UNESCAPED_UNICODE);
$conn->close();
?>