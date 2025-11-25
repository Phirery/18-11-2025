<?php
require_once '../../config/cors.php';
require_once '../../core/dp.php';
require_once '../../core/session.php';

require_role('benhnhan');

$input = json_decode(file_get_contents('php://input'), true);
$maThongBao = $input['maThongBao'] ?? null;

if (!$maThongBao) {
    echo json_encode(['success' => false, 'message' => 'Thiếu mã thông báo']);
    exit;
}

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
    
    // Cập nhật trạng thái đã đọc
    $stmt = $conn->prepare("
        UPDATE thongbaobenhnhan 
        SET daXem = 1 
        WHERE maThongBao = ? AND maBenhNhan = ?
    ");
    $stmt->bind_param("is", $maThongBao, $patient['maBenhNhan']);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Cập nhật thất bại']);
    }
    
    $stmt->close();
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
}

$conn->close();
?>

<?php