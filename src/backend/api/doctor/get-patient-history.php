<?php
require_once '../../config/cors.php';
require_once '../../core/dp.php';
require_once '../../core/session.php';

require_role('bacsi');

if (!isset($_GET['maBenhNhan'])) {
    echo json_encode(['success' => false, 'message' => 'Thiếu thông tin bệnh nhân']);
    exit;
}

$maBenhNhan = $_GET['maBenhNhan'];

try {
    $stmt = $conn->prepare("SELECT maBacSi FROM bacsi WHERE nguoiDungId = ?");
    $stmt->bind_param("i", $_SESSION['id']);
    $stmt->execute();
    $maBacSi = $stmt->get_result()->fetch_assoc()['maBacSi'] ?? null;
    $stmt->close();

    if (!$maBacSi) {
        echo json_encode(['success' => false, 'message' => 'Không tìm thấy bác sĩ']);
        exit;
    }

    $stmt = $conn->prepare("
        SELECT 
            lk.ngayKham,
            lk.trangThai,
            ca.tenCa,
            gk.tenGoi,
            lk.ghiChu
        FROM lichkham lk
        JOIN calamviec ca ON lk.maCa = ca.maCa
        JOIN goikham gk ON lk.maGoi = gk.maGoi
        WHERE lk.maBacSi = ? AND lk.maBenhNhan = ?
        ORDER BY lk.ngayKham DESC
    ");
    $stmt->bind_param("ss", $maBacSi, $maBenhNhan);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $history = [];
    while ($row = $result->fetch_assoc()) {
        $history[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'data' => $history
    ]);
    
    $stmt->close();
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
}

$conn->close();
?>