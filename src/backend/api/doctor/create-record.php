<?php
require_once '../../config/cors.php';
require_once '../../core/dp.php';
require_once '../../core/session.php';

require_role('bacsi');

$input = json_decode(file_get_contents('php://input'), true);
$maLichKham = $input['maLichKham'] ?? '';
$chanDoan = $input['chanDoan'] ?? '';
$dieuTri = $input['dieuTri'] ?? '';
$ghiChu = $input['ghiChu'] ?? '';

if (!$maLichKham || !$chanDoan || !$dieuTri) {
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

    // Verify the appointment belongs to this doctor and get patient + date info
    $stmt = $conn->prepare("SELECT maBenhNhan, ngayKham FROM lichkham WHERE maLichKham = ? AND maBacSi = ?");
    $stmt->bind_param("is", $maLichKham, $maBacSi);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$result) {
        echo json_encode(['success' => false, 'message' => 'Không tìm thấy lịch khám hoặc không có quyền']);
        exit;
    }

    $maBenhNhan = $result['maBenhNhan'];
    $ngayKham = $result['ngayKham'];

    // Check if record already exists for this appointment
    $stmt = $conn->prepare("SELECT maHoSo FROM hosobenhan WHERE maLichKham = ?");
    $stmt->bind_param("i", $maLichKham);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        $stmt->close();
        echo json_encode(['success' => false, 'message' => 'Lịch khám này đã có hồ sơ bệnh án']);
        exit;
    }
    $stmt->close();

    $maHoSo = 'HS' . date('YmdHis') . rand(100, 999);
    
    // Insert with ngayKham and ghiChu
    $stmt = $conn->prepare("INSERT INTO hosobenhan (maHoSo, maBenhNhan, maBacSi, maLichKham, chanDoan, dieuTri, ghiChu, trangThai, ngayTao, ngayKham) VALUES (?, ?, ?, ?, ?, ?, ?, 'Chưa hoàn thành', NOW(), ?)");
    $stmt->bind_param("sssissss", $maHoSo, $maBenhNhan, $maBacSi, $maLichKham, $chanDoan, $dieuTri, $ghiChu, $ngayKham);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Tạo hồ sơ thành công', 'maHoSo' => $maHoSo]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Tạo hồ sơ thất bại: ' . $stmt->error]);
    }
    $stmt->close();
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
}

$conn->close();
?>