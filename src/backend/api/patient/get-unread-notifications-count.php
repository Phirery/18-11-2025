<?php
require_once '../../config/cors.php';
require_once '../../core/dp.php';
require_once '../../core/session.php';

require_role('benhnhan');

try {
    $userId = $_SESSION['id'];
    
    $stmt = $conn->prepare("SELECT maBenhNhan FROM benhnhan WHERE nguoiDungId = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $patient = $result->fetch_assoc();
    
    if (!$patient) {
        echo json_encode(['success' => false, 'message' => 'Không tìm thấy thông tin bệnh nhân']);
        exit;
    }
    
    // Đếm số thông báo chưa đọc
    $stmt = $conn->prepare("
        SELECT COUNT(*) as count 
        FROM thongbaobenhnhan 
        WHERE maBenhNhan = ? AND daXem = 0
    ");
    $stmt->bind_param("s", $patient['maBenhNhan']);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    echo json_encode([
        'success' => true,
        'count' => (int)$row['count']
    ]);
    
    $stmt->close();
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi: ' . $e->getMessage(),
        'count' => 0
    ]);
}

$conn->close();
?>

<?php