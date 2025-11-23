<?php
require_once '../../config/cors.php';
require_once '../../core/dp.php';
require_once '../../core/session.php';

require_role('benhnhan');

$data = json_decode(file_get_contents('php://input'), true);

$maBenhNhan = $data['maBenhNhan'] ?? '';
$maBacSi = $data['maBacSi'] ?? '';
$ngayKham = $data['ngayKham'] ?? '';
$maCa = $data['maCa'] ?? '';
$maSuat = $data['maSuat'] ?? '';
$maGoi = $data['maGoi'] ?? '';
$ghiChu = $data['ghiChu'] ?? '';

// Validation
if (empty($maBenhNhan) || empty($maBacSi) || empty($ngayKham) || empty($maCa) || empty($maSuat)) {
    echo json_encode([
        'success' => false,
        'message' => 'Thiếu thông tin bắt buộc'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// Validate ngày khám phải là ngày hợp lệ
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $ngayKham)) {
    echo json_encode([
        'success' => false,
        'message' => 'Định dạng ngày không hợp lệ'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// Validate ngày khám không được là ngày quá khứ
$today = date('Y-m-d');
if ($ngayKham < $today) {
    echo json_encode([
        'success' => false,
        'message' => 'Không thể đặt lịch cho ngày trong quá khứ'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $conn->begin_transaction();
    
    // 1. Kiểm tra bác sĩ có nghỉ ca này không
    $checkOffStmt = $conn->prepare("
        SELECT COUNT(*) as count 
        FROM ngaynghi 
        WHERE maBacSi = ? AND ngayNghi = ? AND (maCa = ? OR maCa IS NULL)
    ");
    $checkOffStmt->bind_param("ssi", $maBacSi, $ngayKham, $maCa);
    $checkOffStmt->execute();
    $checkOffResult = $checkOffStmt->get_result();
    $isDoctorOff = $checkOffResult->fetch_assoc()['count'] > 0;
    $checkOffStmt->close();
    
    if ($isDoctorOff) {
        throw new Exception('Bác sĩ nghỉ trong ca này. Vui lòng chọn bác sĩ hoặc ca khác!');
    }
    
    // 2. Kiểm tra suất khám có bị trùng KHÔNG (bất kể gói khám)
    // Quan trọng: Chỉ cần maBacSi + ngayKham + maSuat trùng là không cho đặt
    $checkStmt = $conn->prepare("
        SELECT maLichKham, maGoi, trangThai
        FROM lichkham 
        WHERE maBacSi = ? 
        AND ngayKham = ? 
        AND maSuat = ? 
        AND trangThai != 'Hủy'
    ");
    $checkStmt->bind_param("ssi", $maBacSi, $ngayKham, $maSuat);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    
    if ($checkResult->num_rows > 0) {
        $existingBooking = $checkResult->fetch_assoc();
        $checkStmt->close();
        throw new Exception('Suất khám này đã được đặt (Mã lịch: ' . $existingBooking['maLichKham'] . '). Vui lòng chọn suất khác!');
    }
    $checkStmt->close();
    
    // 3. Kiểm tra bệnh nhân đã đặt lịch trùng thời gian chưa
    // (Một bệnh nhân không thể đặt 2 lịch cùng lúc)
    $checkPatientStmt = $conn->prepare("
        SELECT lk.maLichKham
        FROM lichkham lk
        JOIN suatkham sk1 ON lk.maSuat = sk1.maSuat
        JOIN suatkham sk2 ON sk2.maSuat = ?
        WHERE lk.maBenhNhan = ?
        AND lk.ngayKham = ?
        AND lk.trangThai != 'Hủy'
        AND (
            (sk1.gioBatDau >= sk2.gioBatDau AND sk1.gioBatDau < sk2.gioKetThuc)
            OR (sk1.gioKetThuc > sk2.gioBatDau AND sk1.gioKetThuc <= sk2.gioKetThuc)
            OR (sk1.gioBatDau <= sk2.gioBatDau AND sk1.gioKetThuc >= sk2.gioKetThuc)
        )
    ");
    $checkPatientStmt->bind_param("iss", $maSuat, $maBenhNhan, $ngayKham);
    $checkPatientStmt->execute();
    $checkPatientResult = $checkPatientStmt->get_result();
    
    if ($checkPatientResult->num_rows > 0) {
        $conflictBooking = $checkPatientResult->fetch_assoc();
        $checkPatientStmt->close();
        throw new Exception('Bạn đã có lịch khám trùng giờ (Mã lịch: ' . $conflictBooking['maLichKham'] . '). Vui lòng chọn giờ khác!');
    }
    $checkPatientStmt->close();
    
    // 4. Thêm lịch khám
    $stmt = $conn->prepare("
        INSERT INTO lichkham (maBenhNhan, maBacSi, ngayKham, maCa, maSuat, maGoi, trangThai, ghiChu)
        VALUES (?, ?, ?, ?, ?, ?, 'Đã đặt', ?)
    ");
    $stmt->bind_param("sssiiss", $maBenhNhan, $maBacSi, $ngayKham, $maCa, $maSuat, $maGoi, $ghiChu);
    
    if (!$stmt->execute()) {
        throw new Exception('Không thể tạo lịch khám: ' . $stmt->error);
    }
    
    $maLichKham = $conn->insert_id;
    $stmt->close();
    
    $conn->commit();
    
    echo json_encode([
        'success' => true,
        'message' => 'Đặt lịch thành công!',
        'maLichKham' => $maLichKham
    ], JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}

$conn->close();
?>