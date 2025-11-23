<?php
require_once '../../config/cors.php';
require_once '../../core/dp.php';

$maBacSi = $_GET['maBacSi'] ?? '';

if (empty($maBacSi)) {
    echo json_encode([
        'success' => false,
        'message' => 'Thiếu mã bác sĩ'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    // Lấy thông tin chi tiết bác sĩ
    $stmt = $conn->prepare("
        SELECT 
            bs.maBacSi,
            bs.tenBacSi,
            bs.gioiTinh,
            bs.namLamViec,
            bs.moTa,
            ck.maChuyenKhoa,
            ck.tenChuyenKhoa,
            ck.moTa as moTaChuyenKhoa,
            k.maKhoa,
            k.tenKhoa,
            nd.soDienThoai
        FROM bacsi bs
        LEFT JOIN chuyenkhoa ck ON bs.maChuyenKhoa = ck.maChuyenKhoa
        LEFT JOIN khoa k ON ck.maKhoa = k.maKhoa
        LEFT JOIN nguoidung nd ON bs.nguoiDungId = nd.id
        WHERE bs.maBacSi = ?
    ");
    $stmt->bind_param("s", $maBacSi);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Không tìm thấy bác sĩ'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    $doctor = $result->fetch_assoc();
    $stmt->close();
    
    // Lấy thống kê lịch khám
    $statsStmt = $conn->prepare("
        SELECT 
            COUNT(*) as totalAppointments,
            COUNT(DISTINCT maBenhNhan) as totalPatients,
            SUM(CASE WHEN trangThai = 'Hoàn thành' THEN 1 ELSE 0 END) as completedAppointments
        FROM lichkham 
        WHERE maBacSi = ?
    ");
    $statsStmt->bind_param("s", $maBacSi);
    $statsStmt->execute();
    $statsResult = $statsStmt->get_result();
    $stats = $statsResult->fetch_assoc();
    $statsStmt->close();
    
    // Tính số năm kinh nghiệm
    $currentYear = date('Y');
    $experience = 0;
    if ($doctor['namLamViec']) {
        $experience = $currentYear - (int)$doctor['namLamViec'];
    }
    
    // Xác định ảnh đại diện theo giới tính
    $imageName = ($doctor['gioiTinh'] === 'nu') ? 'doctor_female.png' : 'doctor_male.png';
    $imageUrl = "http://localhost/DO_AN_1/code_doan1/src/frontend/assets/images/{$imageName}";
    
    $responseData = [
        'maBacSi' => $doctor['maBacSi'],
        'tenBacSi' => $doctor['tenBacSi'],
        'gioiTinh' => $doctor['gioiTinh'],
        'namLamViec' => $doctor['namLamViec'],
        'namKinhNghiem' => $experience,
        'moTa' => $doctor['moTa'],
        'soDienThoai' => $doctor['soDienThoai'],
        'maChuyenKhoa' => $doctor['maChuyenKhoa'],
        'tenChuyenKhoa' => $doctor['tenChuyenKhoa'],
        'moTaChuyenKhoa' => $doctor['moTaChuyenKhoa'],
        'maKhoa' => $doctor['maKhoa'],
        'tenKhoa' => $doctor['tenKhoa'],
        'anhDaiDien' => $imageUrl,
        'totalAppointments' => (int)$stats['totalAppointments'],
        'totalPatients' => (int)$stats['totalPatients'],
        'completedAppointments' => (int)$stats['completedAppointments']
    ];
    
    echo json_encode([
        'success' => true,
        'data' => $responseData
    ], JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}

$conn->close();
?>