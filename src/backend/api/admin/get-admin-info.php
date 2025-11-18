<?php
require_once '../../config/cors.php';
require_once '../../core/dp.php';
require_once '../../core/session.php';

require_role('quantri');

try {
    // Lấy thông tin admin
    $stmt = $conn->prepare("
        SELECT qv.maQuanTriVien, nd.tenDangNhap, nd.soDienThoai
        FROM quantrivien qv
        JOIN nguoidung nd ON qv.nguoiDungId = nd.id
        WHERE qv.nguoiDungId = ?
    ");
    
    $stmt->bind_param("i", $_SESSION['id']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $admin = $result->fetch_assoc();
        echo json_encode([
            "success" => true,
            "data" => [
                "maQuanTriVien" => $admin['maQuanTriVien'],
                "tenDangNhap" => $admin['tenDangNhap'],
                "soDienThoai" => $admin['soDienThoai']
            ]
        ]);
    } else {
        echo json_encode([
            "success" => false,
            "message" => "Không tìm thấy thông tin quản trị viên"
        ]);
    }
    
    $stmt->close();
} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "message" => "Lỗi: " . $e->getMessage()
    ]);
}

$conn->close();
?>