<?php
require_once '../../config/cors.php';
require_once '../../core/dp.php';
require_once '../../core/session.php';

$nguoiDungId = $_SESSION['id'];
$vaiTro = $_SESSION['vaiTro'];

$input = json_decode(file_get_contents('php://input'), true);

try {
    $conn->begin_transaction();
    
    // Check 24h cooldown
    $checkStmt = $conn->prepare("SELECT ngayCapNhatTaiKhoan FROM nguoidung WHERE id = ?");
    $checkStmt->bind_param("i", $nguoiDungId);
    $checkStmt->execute();
    $lastUpdate = $checkStmt->get_result()->fetch_assoc()['ngayCapNhatTaiKhoan'];
    $checkStmt->close();
    
    if ($lastUpdate) {
        $hoursSinceUpdate = (time() - strtotime($lastUpdate)) / 3600;
        if ($hoursSinceUpdate < 24) {
            throw new Exception('Bạn chỉ có thể cập nhật thông tin sau 24 giờ!');
        }
    }
    
    // Update nguoidung
    if (isset($input['soDienThoai'])) {
        $stmt = $conn->prepare("UPDATE nguoidung SET soDienThoai = ?, ngayCapNhatTaiKhoan = NOW() WHERE id = ?");
        $stmt->bind_param("si", $input['soDienThoai'], $nguoiDungId);
        $stmt->execute();
        $stmt->close();
    }
    
    // Update role-specific tables
    if ($vaiTro === 'benhnhan') {
        $stmt = $conn->prepare("
            UPDATE benhnhan 
            SET tenBenhNhan = ?, ngaySinh = ?, gioiTinh = ?, soTheBHYT = ?
            WHERE nguoiDungId = ?
        ");
        $stmt->bind_param("ssssi", 
            $input['hoTen'], 
            $input['ngaySinh'], 
            $input['gioiTinh'], 
            $input['soTheBHYT'], 
            $nguoiDungId
        );
        $stmt->execute();
        $stmt->close();
    } elseif ($vaiTro === 'bacsi') {
        $stmt = $conn->prepare("
            UPDATE bacsi 
            SET tenBacSi = ?, moTa = ?
            WHERE nguoiDungId = ?
        ");
        $stmt->bind_param("ssi", $input['hoTen'], $input['moTa'], $nguoiDungId);
        $stmt->execute();
        $stmt->close();
    }
    
    $conn->commit();
    echo json_encode(['success' => true, 'message' => 'Cập nhật thành công!']);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
$conn->close();
?>