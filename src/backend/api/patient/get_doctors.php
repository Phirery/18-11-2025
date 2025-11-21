<?php
require_once '../../config/cors.php';
require_once '../../core/dp.php';

// Get filter parameters
$maKhoa = $_GET['maKhoa'] ?? '';
$maChuyenKhoa = $_GET['maChuyenKhoa'] ?? '';
$search = $_GET['search'] ?? '';

try {
    // Build query with filters
    $sql = "
        SELECT 
            bs.maBacSi,
            bs.tenBacSi,
            bs.gioiTinh,
            bs.namLamViec,
            bs.moTa,
            ck.maChuyenKhoa,
            ck.tenChuyenKhoa,
            ck.maKhoa,
            k.tenKhoa
        FROM bacsi bs
        LEFT JOIN chuyenkhoa ck ON bs.maChuyenKhoa = ck.maChuyenKhoa
        LEFT JOIN khoa k ON ck.maKhoa = k.maKhoa
        WHERE 1=1
    ";
    
    $params = [];
    $types = '';
    
    // Apply filters
    if (!empty($maKhoa)) {
        $sql .= " AND k.maKhoa = ?";
        $params[] = $maKhoa;
        $types .= 's';
    }
    
    if (!empty($maChuyenKhoa)) {
        $sql .= " AND ck.maChuyenKhoa = ?";
        $params[] = $maChuyenKhoa;
        $types .= 's';
    }
    
    if (!empty($search)) {
        $sql .= " AND (bs.tenBacSi LIKE ? OR ck.tenChuyenKhoa LIKE ?)";
        $searchParam = '%' . $search . '%';
        $params[] = $searchParam;
        $params[] = $searchParam;
        $types .= 'ss';
    }
    
    $sql .= " ORDER BY bs.tenBacSi ASC";
    
    // Prepare and execute
    $stmt = $conn->prepare($sql);
    
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    $doctors = [];
    $currentYear = date('Y');
    
    while ($row = $result->fetch_assoc()) {
        // Calculate experience
        $experience = 0;
        if ($row['namLamViec']) {
            $experience = $currentYear - (int)$row['namLamViec'];
        }
        
        // Determine image based on gender
        $imageName = ($row['gioiTinh'] === 'nu') ? 'doctor_female.png' : 'doctor_male.png';
        $imageUrl = "http://localhost/DO_AN_1/code_doan1/src/frontend/assets/images/{$imageName}";
        
        $doctors[] = [
            'maBacSi' => $row['maBacSi'],
            'tenBacSi' => $row['tenBacSi'],
            'gioiTinh' => $row['gioiTinh'],
            'namLamViec' => $row['namLamViec'],
            'namKinhNghiem' => $experience,
            'moTa' => $row['moTa'],
            'maChuyenKhoa' => $row['maChuyenKhoa'],
            'tenChuyenKhoa' => $row['tenChuyenKhoa'],
            'maKhoa' => $row['maKhoa'],
            'tenKhoa' => $row['tenKhoa'],
            'anhDaiDien' => $imageUrl
        ];
    }
    
    echo json_encode([
        'success' => true,
        'data' => $doctors,
        'total' => count($doctors)
    ], JSON_UNESCAPED_UNICODE);
    
    $stmt->close();
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi: ' . $e->getMessage(),
        'data' => []
    ], JSON_UNESCAPED_UNICODE);
}

$conn->close();
?>