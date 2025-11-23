<?php
require_once '../../config/cors.php';
require_once '../../core/dp.php';

$maChuyenKhoa = $_GET['maChuyenKhoa'] ?? '';

if (empty($maChuyenKhoa)) {
    echo json_encode([
        'success' => false,
        'message' => 'Thiếu mã chuyên khoa'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    // Lấy thông tin chi tiết chuyên khoa
    $stmt = $conn->prepare("
        SELECT 
            ck.maChuyenKhoa,
            ck.tenChuyenKhoa,
            ck.moTa as moTaChuyenKhoa,
            ck.maKhoa,
            k.tenKhoa,
            k.moTa as moTaKhoa,
            COUNT(DISTINCT bs.maBacSi) as soBacSi
        FROM chuyenkhoa ck
        LEFT JOIN khoa k ON ck.maKhoa = k.maKhoa
        LEFT JOIN bacsi bs ON ck.maChuyenKhoa = bs.maChuyenKhoa
        WHERE ck.maChuyenKhoa = ?
        GROUP BY ck.maChuyenKhoa, ck.tenChuyenKhoa, ck.moTa, ck.maKhoa, k.tenKhoa, k.moTa
    ");
    
    $stmt->bind_param("s", $maChuyenKhoa);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Không tìm thấy chuyên khoa'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    $specialty = $result->fetch_assoc();
    $stmt->close();
    
    $responseData = [
        'maChuyenKhoa' => $specialty['maChuyenKhoa'],
        'tenChuyenKhoa' => $specialty['tenChuyenKhoa'],
        'moTaChuyenKhoa' => $specialty['moTaChuyenKhoa'],
        'maKhoa' => $specialty['maKhoa'],
        'tenKhoa' => $specialty['tenKhoa'],
        'moTaKhoa' => $specialty['moTaKhoa'],
        'soBacSi' => (int)$specialty['soBacSi']
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