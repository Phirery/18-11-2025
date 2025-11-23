<?php
require_once '../../config/cors.php';
require_once '../../core/dp.php';
require_once '../../core/session.php';

require_role('benhnhan');

$data = json_decode(file_get_contents('php://input'), true);

$maLichKham = $data['maLichKham'] ?? '';
$lyDo = trim($data['lyDo'] ?? '');

// Validation
if (empty($maLichKham) || empty($lyDo)) {
    echo json_encode([
        'success' => false,
        'message' => 'Thiếu thông tin hoặc lý do hủy lịch'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $conn->begin_transaction();
    
    // 1. Lấy thông tin lịch khám
    $stmt = $conn->prepare("
        SELECT 
            lk.maBenhNhan,
            lk.maBacSi,
            lk.ngayKham,
            lk.maCa,
            lk.maSuat,
            lk.trangThai,
            bn.tenBenhNhan,
            ca.tenCa,
            sk.gioBatDau,
            sk.gioKetThuc
        FROM lichkham lk
        JOIN benhnhan bn ON lk.maBenhNhan = bn.maBenhNhan
        JOIN calamviec ca ON lk.maCa = ca.maCa
        JOIN suatkham sk ON lk.maSuat = sk.maSuat
        WHERE lk.maLichKham = ?
    ");
    $stmt->bind_param("i", $maLichKham);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception('Không tìm thấy lịch khám');
    }
    
    $appointment = $result->fetch_assoc();
    $stmt->close();
    
    // Kiểm tra quyền sở hữu lịch khám
    $checkOwnerStmt = $conn->prepare("
        SELECT maBenhNhan 
        FROM benhnhan 
        WHERE nguoiDungId = ?
    ");
    $checkOwnerStmt->bind_param("i", $_SESSION['id']);
    $checkOwnerStmt->execute();
    $ownerResult = $checkOwnerStmt->get_result();
    $owner = $ownerResult->fetch_assoc();
    $checkOwnerStmt->close();
    
    if ($owner['maBenhNhan'] !== $appointment['maBenhNhan']) {
        throw new Exception('Bạn không có quyền hủy lịch khám này');
    }
    
    // Kiểm tra trạng thái
    if ($appointment['trangThai'] === 'Hủy') {
        throw new Exception('Lịch khám này đã được hủy trước đó');
    }
    
    if ($appointment['trangThai'] === 'Hoàn thành') {
        throw new Exception('Không thể hủy lịch khám đã hoàn thành');
    }
    
    // 2. Tính thời gian còn lại đến lịch khám
    $appointmentDateTime = new DateTime($appointment['ngayKham'] . ' ' . $appointment['gioBatDau']);
    $currentDateTime = new DateTime();
    $hoursDiff = ($appointmentDateTime->getTimestamp() - $currentDateTime->getTimestamp()) / 3600;
    
    // 3. Cập nhật trạng thái lịch khám thành 'Hủy'
    $updateStmt = $conn->prepare("
        UPDATE lichkham 
        SET trangThai = 'Hủy',
            ghiChu = CONCAT(COALESCE(ghiChu, ''), '\n[Lý do hủy]: ', ?)
        WHERE maLichKham = ?
    ");
    $updateStmt->bind_param("si", $lyDo, $maLichKham);
    
    if (!$updateStmt->execute()) {
        throw new Exception('Không thể cập nhật trạng thái lịch khám');
    }
    $updateStmt->close();
    
    // 4. Gửi thông báo đến bác sĩ (trigger sẽ tự động gửi)
    // Format: "Bệnh nhân [Tên] đã hủy lịch khám vào ngày [DD/MM/YYYY] - [Ca] - [Giờ]. Lý do: [Lý do]"
    $ngayKhamFormatted = date('d/m/Y', strtotime($appointment['ngayKham']));
    $gioKham = substr($appointment['gioBatDau'], 0, 5) . ' - ' . substr($appointment['gioKetThuc'], 0, 5);
    
    $noiDung = "Bệnh nhân {$appointment['tenBenhNhan']} đã hủy lịch khám vào ngày {$ngayKhamFormatted} - {$appointment['tenCa']} - {$gioKham}. Lý do: {$lyDo}";
    
    $notifStmt = $conn->prepare("
        INSERT INTO thongbaolichkham (maBacSi, maLichKham, loai, tieuDe, noiDung, thoiGian, daXem)
        VALUES (?, ?, 'Hủy lịch', 'Lịch khám đã hủy', ?, NOW(), 0)
    ");
    $notifStmt->bind_param("sis", $appointment['maBacSi'], $maLichKham, $noiDung);
    $notifStmt->execute();
    $notifStmt->close();
    
    // 5. Kiểm tra xem có cần khôi phục suất khám không (hủy trước 12h)
    $shouldRestore = $hoursDiff >= 12;
    $restoreMessage = '';
    
    if ($shouldRestore) {
        // Suất khám được khôi phục tự động vì trigger không xóa suất
        // Logic: Khi kiểm tra suất available, chỉ cần WHERE trangThai != 'Hủy'
        $restoreMessage = ' Suất khám đã được khôi phục để người khác có thể đặt.';
    } else {
        $restoreMessage = ' Suất khám không được khôi phục do hủy trong vòng 12 giờ.';
    }
    
    $conn->commit();
    
    echo json_encode([
        'success' => true,
        'message' => 'Hủy lịch khám thành công!' . $restoreMessage,
        'restored' => $shouldRestore,
        'hoursDiff' => round($hoursDiff, 1)
    ], JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}

$conn->close();