<?php
require_once '../../config/cors.php';
require_once '../../core/dp.php';
require_once '../../core/session.php';

require_role('bacsi');

$input = json_decode(file_get_contents('php://input'), true);
$maHoSo = $input['maHoSo'] ?? '';
$chanDoan = $input['chanDoan'] ?? '';
$dieuTri = $input['dieuTri'] ?? '';
$ghiChu = $input['ghiChu'] ?? '';

if (!$maHoSo || !$chanDoan || !$dieuTri) {
    echo json_encode(['success' => false, 'message' => 'Thiếu thông tin bắt buộc']);
    exit;
}

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

    // Verify ownership before updating - update ghiChu as well
    $stmt = $conn->prepare("UPDATE hosobenhan SET chanDoan = ?, dieuTri = ?, ghiChu = ?, trangThai = 'Đã hoàn thành', ngayHoanThanh = NOW() WHERE maHoSo = ? AND maBacSi = ?");
    $stmt->bind_param("sssss", $chanDoan, $dieuTri, $ghiChu, $maHoSo, $maBacSi);
    
    if ($stmt->execute() && $stmt->affected_rows > 0) {
        echo json_encode(['success' => true, 'message' => 'Cập nhật thành công']);
    } else {
        if ($stmt->affected_rows === 0) {
            echo json_encode(['success' => false, 'message' => 'Không có thay đổi hoặc không có quyền']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Cập nhật thất bại: ' . $stmt->error]);
        }
    }
    $stmt->close();
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
}

$conn->close();
?>