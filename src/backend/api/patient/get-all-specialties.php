<?php
require_once '../../config/cors.php';
require_once '../../core/dp.php';

// Get filter parameters
$maKhoa = $_GET['maKhoa'] ?? '';
$search = $_GET['search'] ?? '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;

try {
    // Build query with filters
    $sql = "
        SELECT 
            ck.maChuyenKhoa,
            ck.tenChuyenKhoa,
            ck.maKhoa,
            ck.moTa,
            k.tenKhoa,
            COUNT(DISTINCT bs.maBacSi) as soBacSi
        FROM chuyenkhoa ck
        LEFT JOIN khoa k ON ck.maKhoa = k.maKhoa
        LEFT JOIN bacsi bs ON ck.maChuyenKhoa = bs.maChuyenKhoa
        WHERE 1=1
    ";
    
    $countSql = "
        SELECT COUNT(DISTINCT ck.maChuyenKhoa) as total
        FROM chuyenkhoa ck
        LEFT JOIN khoa k ON ck.maKhoa = k.maKhoa
        WHERE 1=1
    ";
    
    $params = [];
    $types = '';
    
    // Apply filters
    if (!empty($maKhoa)) {
        $sql .= " AND ck.maKhoa = ?";
        $countSql .= " AND ck.maKhoa = ?";
        $params[] = $maKhoa;
        $types .= 's';
    }
    
    if (!empty($search)) {
        $sql .= " AND (ck.tenChuyenKhoa LIKE ? OR k.tenKhoa LIKE ?)";
        $countSql .= " AND (ck.tenChuyenKhoa LIKE ? OR k.tenKhoa LIKE ?)";
        $searchParam = '%' . $search . '%';
        $params[] = $searchParam;
        $params[] = $searchParam;
        $types .= 'ss';
    }
    
    // Get total count
    $countStmt = $conn->prepare($countSql);
    if (!empty($params)) {
        $countStmt->bind_param($types, ...$params);
    }
    $countStmt->execute();
    $totalResult = $countStmt->get_result();
    $totalRow = $totalResult->fetch_assoc();
    $total = (int)$totalRow['total'];
    $countStmt->close();
    
    // Add grouping, ordering and pagination
    $sql .= " GROUP BY ck.maChuyenKhoa, ck.tenChuyenKhoa, ck.maKhoa, ck.moTa, k.tenKhoa";
    $sql .= " ORDER BY k.tenKhoa ASC, ck.tenChuyenKhoa ASC";
    
    $offset = ($page - 1) * $limit;
    $sql .= " LIMIT ? OFFSET ?";
    
    // Prepare and execute main query
    $stmt = $conn->prepare($sql);
    
    $allParams = $params;
    $allParams[] = $limit;
    $allParams[] = $offset;
    $allTypes = $types . 'ii';
    
    if (!empty($allParams)) {
        $stmt->bind_param($allTypes, ...$allParams);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    $specialties = [];
    while ($row = $result->fetch_assoc()) {
        $specialties[] = [
            'maChuyenKhoa' => $row['maChuyenKhoa'],
            'tenChuyenKhoa' => $row['tenChuyenKhoa'],
            'maKhoa' => $row['maKhoa'],
            'tenKhoa' => $row['tenKhoa'],
            'moTa' => $row['moTa'],
            'soBacSi' => (int)$row['soBacSi']
        ];
    }
    
    // Calculate pagination info
    $totalPages = ceil($total / $limit);
    
    echo json_encode([
        'success' => true,
        'data' => $specialties,
        'pagination' => [
            'total' => $total,
            'page' => $page,
            'limit' => $limit,
            'totalPages' => $totalPages
        ]
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