<?php
// request-leave.php
require_once '../../config/cors.php';
require_once '../../core/dp.php';
require_once '../../core/session.php';

require_role('bacsi');

$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['ngayNghi']) || !isset($input['lyDo'])) {
    echo json_encode(['success' => false, 'message' => 'Thiếu thông tin']);
    exit;
}

// Lấy mã bác sĩ từ session ID
$stmt = $conn->prepare("SELECT maBacSi FROM bacsi WHERE nguoiDungId = ?");
$stmt->bind_param("i", $_SESSION['id']);
$stmt->execute();
$maBacSi = $stmt->get_result()->fetch_assoc()['maBacSi'] ?? null;
$stmt->close();

if (!$maBacSi) {
    echo json_encode(['success' => false, 'message' => 'Không tìm thấy bác sĩ']);
    exit;
}

$ngayNghi = $input['ngayNghi'];
$maCa = $input['maCa']; // null nếu nghỉ cả ngày
$lyDo = trim($input['lyDo']);

$tomorrow = date('Y-m-d', strtotime('+1 day'));
if ($ngayNghi < $tomorrow) {
    echo json_encode(['success' => false, 'message' => 'Chỉ được xin nghỉ từ ngày mai trở đi']);
    exit;
}

try {
    $conn->begin_transaction();
    
    // Insert vào bảng ngaynghi
    // Trigger 'after_ngaynghi_insert' sẽ tự động tạo thông báo Admin
    if ($maCa === null) {
        // Nghỉ cả ngày (Ca 1 và Ca 2)
        $stmt = $conn->prepare("
            INSERT INTO ngaynghi (maBacSi, ngayNghi, maCa, lyDo) 
            VALUES (?, ?, 1, ?), (?, ?, 2, ?)
        ");
        $stmt->bind_param("ssisss", $maBacSi, $ngayNghi, $lyDo, $maBacSi, $ngayNghi, $lyDo);
    } else {
        // Nghỉ 1 ca
        $stmt = $conn->prepare("
            INSERT INTO ngaynghi (maBacSi, ngayNghi, maCa, lyDo) 
            VALUES (?, ?, ?, ?)
        ");
        $stmt->bind_param("ssis", $maBacSi, $ngayNghi, $maCa, $lyDo);
    }
    
    if (!$stmt->execute()) {
        throw new Exception('Không thể tạo đơn nghỉ phép: ' . $stmt->error);
    }
    $stmt->close();
    
    // Đếm số lịch khám bị ảnh hưởng để cảnh báo
    $checkStmt = $conn->prepare("
        SELECT COUNT(*) as count FROM lichkham 
        WHERE maBacSi = ? AND ngayKham = ? AND trangThai = 'Đã đặt'
        " . ($maCa ? " AND maCa = ?" : "")
    );
    
    if ($maCa) {
        $checkStmt->bind_param("ssi", $maBacSi, $ngayNghi, $maCa);
    } else {
        $checkStmt->bind_param("ss", $maBacSi, $ngayNghi);
    }
    
    $checkStmt->execute();
    $count = $checkStmt->get_result()->fetch_assoc()['count'];
    $checkStmt->close();
    
    $conn->commit();
    
    $message = 'Gửi yêu cầu nghỉ phép thành công!';
    if ($count > 0) {
        $message .= " Lưu ý: Có $count lịch khám cần sắp xếp lại.";
    }
    
    echo json_encode(['success' => true, 'message' => $message, 'affectedAppointments' => $count]);
    
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
}

$conn->close();
?>