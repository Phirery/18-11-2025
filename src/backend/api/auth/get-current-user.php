<?php
require_once '../../config/cors.php';
require_once '../../core/dp.php';
require_once '../../core/session.php';

global $conn; // Lấy biến kết nối DB từ dp.php (Đảm bảo dp.php đã được require_once)

if (isset($_SESSION['id']) && isset($_SESSION['vaiTro'])) {
    $userId = $_SESSION['id'];
    $userRole = $_SESSION['vaiTro'];
    $tenDangNhap = $_SESSION['tenDangNhap'];
    
    // Mặc định tên hiển thị ban đầu là tên đăng nhập
    $hoTenHienThi = $tenDangNhap; 

    // Logic ĐẶC BIỆT để lấy Tên đầy đủ (hoTen) cho Bệnh nhân
    if ($userRole === 'benhnhan') {
        try {
            $stmt = $conn->prepare("SELECT tenBenhNhan FROM benhnhan WHERE nguoiDungId = ?");
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $patient = $result->fetch_assoc();
                $hoTenHienThi = $patient['tenBenhNhan'];
            }
            $stmt->close();
        } catch (Exception $e) {
            // Log lỗi nếu cần, nhưng vẫn trả về tên đăng nhập nếu truy vấn lỗi
            // error_log("Lỗi truy vấn tên bệnh nhân: " . $e->getMessage()); 
        }
    } 
    // Bạn có thể thêm logic tương tự cho vai trò 'bacsi' và 'quantri' nếu muốn hiển thị tên thật của họ

    echo json_encode([
        'success' => true,
        'data' => [
            'id' => $userId,
            'username' => $tenDangNhap, 
            'role' => $userRole,
            'fullName' => $hoTenHienThi
        ]
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Chưa đăng nhập'
    ]);
}
?>