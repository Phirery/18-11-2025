<?php
require_once '../../config/cors.php';
require_once '../../core/dp.php';
require_once '../../core/session.php';

require_role('benhnhan');

try {
    // Lấy mã bệnh nhân từ session
    $stmt = $conn->prepare("
        SELECT maBenhNhan 
        FROM benhnhan 
        WHERE nguoiDungId = ?
    ");
    $stmt->bind_param("i", $_SESSION['id']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception('Không tìm thấy thông tin bệnh nhân');
    }
    
    $patient = $result->fetch_assoc();
    $maBenhNhan = $patient['maBenhNhan'];
    $stmt->close();
    
    // Query lấy lịch khám sắp tới (trạng thái = 'Đã đặt')
    $upcomingStmt = $conn->prepare("
        SELECT 
            lk.maLichKham,
            lk.ngayKham,
            lk.trangThai,
            lk.ghiChu,
            bs.tenBacSi,
            ck.tenChuyenKhoa,
            gk.tenGoi,
            ca.tenCa,
            sk.gioBatDau,
            sk.gioKetThuc
        FROM lichkham lk
        JOIN bacsi bs ON lk.maBacSi = bs.maBacSi
        LEFT JOIN chuyenkhoa ck ON bs.maChuyenKhoa = ck.maChuyenKhoa
        JOIN goikham gk ON lk.maGoi = gk.maGoi
        JOIN calamviec ca ON lk.maCa = ca.maCa
        JOIN suatkham sk ON lk.maSuat = sk.maSuat
        WHERE lk.maBenhNhan = ?
        AND lk.trangThai = 'Đã đặt'
        ORDER BY lk.ngayKham ASC, sk.gioBatDau ASC
    ");
    $upcomingStmt->bind_param("s", $maBenhNhan);
    $upcomingStmt->execute();
    $upcomingResult = $upcomingStmt->get_result();
    
    $upcoming = [];
    while ($row = $upcomingResult->fetch_assoc()) {
        $upcoming[] = [
            'maLichKham' => $row['maLichKham'],
            'ngayKham' => date('d/m/Y', strtotime($row['ngayKham'])),
            'gioKham' => substr($row['gioBatDau'], 0, 5) . ' - ' . substr($row['gioKetThuc'], 0, 5),
            'bacSi' => 'BS. ' . $row['tenBacSi'],
            'chuyenKhoa' => $row['tenChuyenKhoa'] ?: 'Đa khoa',
            'goiKham' => $row['tenGoi'],
            'trangThai' => $row['trangThai'],
            'ghiChu' => $row['ghiChu']
        ];
    }
    $upcomingStmt->close();
    
    // Query lấy lịch sử khám (trạng thái = 'Hoàn thành' hoặc 'Hủy')
    $historyStmt = $conn->prepare("
        SELECT 
            lk.maLichKham,
            lk.ngayKham,
            lk.trangThai,
            lk.ghiChu,
            bs.tenBacSi,
            ck.tenChuyenKhoa,
            gk.tenGoi,
            ca.tenCa,
            sk.gioBatDau,
            sk.gioKetThuc
        FROM lichkham lk
        JOIN bacsi bs ON lk.maBacSi = bs.maBacSi
        LEFT JOIN chuyenkhoa ck ON bs.maChuyenKhoa = ck.maChuyenKhoa
        JOIN goikham gk ON lk.maGoi = gk.maGoi
        JOIN calamviec ca ON lk.maCa = ca.maCa
        JOIN suatkham sk ON lk.maSuat = sk.maSuat
        WHERE lk.maBenhNhan = ?
        AND lk.trangThai IN ('Hoàn thành', 'Hủy')
        ORDER BY lk.ngayKham DESC, sk.gioBatDau DESC
    ");
    $historyStmt->bind_param("s", $maBenhNhan);
    $historyStmt->execute();
    $historyResult = $historyStmt->get_result();
    
    $history = [];
    while ($row = $historyResult->fetch_assoc()) {
        $history[] = [
            'maLichKham' => $row['maLichKham'],
            'ngayKham' => date('d/m/Y', strtotime($row['ngayKham'])),
            'gioKham' => substr($row['gioBatDau'], 0, 5) . ' - ' . substr($row['gioKetThuc'], 0, 5),
            'bacSi' => 'BS. ' . $row['tenBacSi'],
            'chuyenKhoa' => $row['tenChuyenKhoa'] ?: 'Đa khoa',
            'goiKham' => $row['tenGoi'],
            'trangThai' => $row['trangThai'],
            'ghiChu' => $row['ghiChu']
        ];
    }
    $historyStmt->close();
    
    echo json_encode([
        'success' => true,
        'upcoming' => $upcoming,
        'history' => $history,
        'message' => 'Lấy dữ liệu thành công'
    ], JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'upcoming' => [],
        'history' => []
    ], JSON_UNESCAPED_UNICODE);
}

$conn->close();