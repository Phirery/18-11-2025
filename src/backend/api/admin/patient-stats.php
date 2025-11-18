<?php
require_once '../../config/cors.php';
require_once '../../core/dp.php';
require_once '../../core/session.php';

require_role('quantri');

try {
    // Tổng bệnh nhân
    $totalSql = "SELECT COUNT(*) as total FROM benhnhan";
    $total = $conn->query($totalSql)->fetch_assoc()['total'];

    // Nam
    $maleSql = "SELECT COUNT(*) as male FROM benhnhan WHERE gioiTinh = 'nam'";
    $male = $conn->query($maleSql)->fetch_assoc()['male'];

    // Nữ
    $femaleSql = "SELECT COUNT(*) as female FROM benhnhan WHERE gioiTinh = 'nu'";
    $female = $conn->query($femaleSql)->fetch_assoc()['female'];

    // Có BHYT
    $bhytSql = "SELECT COUNT(*) as bhyt FROM benhnhan WHERE soTheBHYT IS NOT NULL AND soTheBHYT != ''";
    $bhyt = $conn->query($bhytSql)->fetch_assoc()['bhyt'];

    echo json_encode([
        'success' => true,
        'total' => (int)$total,
        'male' => (int)$male,
        'female' => (int)$female,
        'bhyt' => (int)$bhyt
    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}

$conn->close();
?>