<?php
require_once '../../config/cors.php';
require_once '../../core/dp.php';
require_once '../../core/session.php';

if (!isset($_SESSION['id']) || !isset($_SESSION['vaiTro'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Chưa đăng nhập'
    ]);
    exit;
}

if ($_SESSION['vaiTro'] !== 'benhnhan') {
    echo json_encode([
        'success' => false,
        'message' => 'Không có quyền truy cập'
    ]);
    exit;
}

try {
    $stmt = $conn->prepare("
        SELECT bn.maBenhNhan, bn.tenBenhNhan, nd.tenDangNhap
        FROM benhnhan bn
        JOIN nguoidung nd ON bn.nguoiDungId = nd.id
        WHERE bn.nguoiDungId = ?
    ");
    $stmt->bind_param("i", $_SESSION['id']);
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
        echo json_encode([
            'success' => false,
            'message' => 'Không tìm thấy thông tin bệnh nhân'
        ]);
    }
    
    $stmt->close();
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi: ' . $e->getMessage()
    ]);
}

$conn->close();
?>