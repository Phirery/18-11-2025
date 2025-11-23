<?php
require_once '../../config/cors.php';
require_once '../../core/dp.php';
require_once '../../core/session.php';

// --- SỬA ĐỔI QUAN TRỌNG ---
// Thay vì tự viết if check session, hãy dùng hàm chuẩn bạn đã viết
// Hàm này sẽ tự động chặn nếu chưa đăng nhập hoặc sai quyền, và trả về JSON lỗi luôn.
require_role('benhnhan'); 

try {
    // Lúc này chắc chắn user đã đăng nhập và là 'benhnhan' rồi
    $userId = $_SESSION['id'];

    $stmt = $conn->prepare("
        SELECT bn.maBenhNhan, bn.tenBenhNhan, nd.tenDangNhap
        FROM benhnhan bn
        JOIN nguoidung nd ON bn.nguoiDungId = nd.id
        WHERE bn.nguoiDungId = ?
    ");
    
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();
        echo json_encode([
            'success' => true,
            'role' => 'benhnhan',
            'patientId' => $data['maBenhNhan'],
            'fullName' => $data['tenBenhNhan'],
            'username' => $data['tenDangNhap']
        ]);
    } else {
        // Trường hợp hy hữu: User có tài khoản 'benhnhan' trong bảng nguoidung
        // nhưng chưa có dữ liệu trong bảng benhnhan
        echo json_encode([
            'success' => false,
            'message' => 'Tài khoản chưa được liên kết hồ sơ bệnh nhân'
        ]);
    }
    
    $stmt->close();
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi hệ thống: ' . $e->getMessage()
    ]);
}

$conn->close();