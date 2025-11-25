<?php
require_once '../../config/cors.php';
require_once '../../core/dp.php';
require_once '../../core/session.php';

require_role('benhnhan');

try {
    // Lấy mã bệnh nhân từ session
    $userId = $_SESSION['id'];
    
    $stmt = $conn->prepare("
        SELECT maBenhNhan 
        FROM benhnhan 
        WHERE nguoiDungId = ?
    ");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $patient = $result->fetch_assoc();
    
    if (!$patient) {
        echo json_encode(['success' => false, 'message' => 'Không tìm thấy thông tin bệnh nhân']);
        exit;
    }
    
    $maBenhNhan = $patient['maBenhNhan'];
    
    // Lấy danh sách thông báo (mới nhất trước)
    $sql = "SELECT 
            maThongBao,
            loai,
            tieuDe,
            noiDung,
            thoiGian,
            daXem
        FROM thongbaobenhnhan
        WHERE maBenhNhan = ?
        ORDER BY thoiGian DESC
        LIMIT 50";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $maBenhNhan);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $notifications = [];
    while ($row = $result->fetch_assoc()) {
        $notifications[] = [
            'maThongBao' => $row['maThongBao'],
            'loai' => $row['loai'],
            'tieuDe' => $row['tieuDe'],
            'noiDung' => $row['noiDung'],
            'thoiGian' => $row['thoiGian'],
            'daXem' => (bool)$row['daXem']
        ];
    }
    
    echo json_encode([
        'success' => true,
        'data' => $notifications
    ]);
    
    $stmt->close();
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi: ' . $e->getMessage()
    ]);
}

$conn->close();
?>

<?php