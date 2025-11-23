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
    
    // Query lấy tất cả hồ sơ bệnh án (chỉ lấy đã hoàn thành)
    $recordsStmt = $conn->prepare("
        SELECT 
            hs.maHoSo,
            hs.chanDoan,
            hs.dieuTri,
            hs.ghiChu,
            hs.ngayHoanThanh,
            lk.ngayKham,
            lk.maCa,
            lk.maSuat,
            bs.tenBacSi,
            ck.tenChuyenKhoa,
            ca.tenCa,
            sk.gioBatDau,
            sk.gioKetThuc
        FROM hosobenhan hs
        JOIN lichkham lk ON hs.maLichKham = lk.maLichKham
        JOIN bacsi bs ON hs.maBacSi = bs.maBacSi
        LEFT JOIN chuyenkhoa ck ON bs.maChuyenKhoa = ck.maChuyenKhoa
        JOIN calamviec ca ON lk.maCa = ca.maCa
        JOIN suatkham sk ON lk.maSuat = sk.maSuat
        WHERE hs.maBenhNhan = ?
        AND hs.trangThai = 'Đã hoàn thành'
        ORDER BY lk.ngayKham DESC, sk.gioBatDau DESC
    ");
    $recordsStmt->bind_param("s", $maBenhNhan);
    $recordsStmt->execute();
    $recordsResult = $recordsStmt->get_result();
    
    $records = [];
    $latest = null;
    
    while ($row = $recordsResult->fetch_assoc()) {
        $ngayKhamFormatted = date('d/m/Y', strtotime($row['ngayKham']));
        $gioKham = substr($row['gioBatDau'], 0, 5) . ' - ' . substr($row['gioKetThuc'], 0, 5);
        
        $record = [
            'maHoSo' => $row['maHoSo'],
            'ngayKham' => $ngayKhamFormatted,
            'ngayKhamRaw' => $row['ngayKham'], // For filtering
            'gioKham' => $gioKham,
            'tenCa' => $row['tenCa'],
            'tenBacSi' => $row['tenBacSi'],
            'tenChuyenKhoa' => $row['tenChuyenKhoa'] ?: 'Đa khoa',
            'chanDoan' => $row['chanDoan'],
            'dieuTri' => $row['dieuTri'],
            'ghiChu' => $row['ghiChu'],
            'ngayHoanThanh' => $row['ngayHoanThanh'] ? date('d/m/Y H:i', strtotime($row['ngayHoanThanh'])) : null
        ];
        
        $records[] = $record;
        
        // First record is the latest
        if ($latest === null) {
            $latest = $record;
        }
    }
    $recordsStmt->close();
    
    echo json_encode([
        'success' => true,
        'latest' => $latest,
        'records' => $records,
        'total' => count($records),
        'message' => 'Lấy dữ liệu thành công'
    ], JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'latest' => null,
        'records' => []
    ], JSON_UNESCAPED_UNICODE);
}

$conn->close();
?>