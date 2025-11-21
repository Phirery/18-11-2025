<?php
require_once '../../config/cors.php';
require_once '../../core/dp.php';
require_once '../../core/session.php';

require_role('quantri');

// Bắt đầu transaction
$conn->begin_transaction();

try {
    // Lấy dữ liệu POST
    $data = json_decode(file_get_contents('php://input'), true);
    
    $maBacSi = $conn->real_escape_string($data['maBacSi']);
    $tenBacSi = $conn->real_escape_string($data['tenBacSi']);
    $soDienThoai = $conn->real_escape_string($data['soDienThoai']);
    $maChuyenKhoa = $conn->real_escape_string($data['maChuyenKhoa']);
    $tenDangNhap = $conn->real_escape_string($data['tenDangNhap']);
    $matKhau = isset($data['matKhau']) ? $conn->real_escape_string($data['matKhau']) : '';
    $moTa = isset($data['moTa']) ? $conn->real_escape_string($data['moTa']) : '';
    $gioiTinh = isset($data['gioiTinh']) ? $conn->real_escape_string($data['gioiTinh']) : null;
    $namLamViec = isset($data['namLamViec']) ? intval($data['namLamViec']) : null;
    
    // Lấy nguoiDungId từ maBacSi
    $getUserSql = "SELECT nguoiDungId FROM bacsi WHERE maBacSi = '$maBacSi'";
    $userResult = $conn->query($getUserSql);
    
    if ($userResult->num_rows === 0) {
        throw new Exception('Không tìm thấy bác sĩ!');
    }
    
    $nguoiDungId = $userResult->fetch_assoc()['nguoiDungId'];
    
    // Kiểm tra tên đăng nhập (nếu thay đổi)
    $checkSql = "SELECT COUNT(*) as count FROM nguoidung 
                 WHERE tenDangNhap = '$tenDangNhap' AND id != $nguoiDungId";
    $checkResult = $conn->query($checkSql);
    $count = $checkResult->fetch_assoc()['count'];
    
    if ($count > 0) {
        throw new Exception('Tên đăng nhập đã tồn tại!');
    }
    
    // 1. Cập nhật bảng nguoidung
    if (!empty($matKhau)) {
        // Nếu có mật khẩu mới
        $hashedPassword = password_hash($matKhau, PASSWORD_DEFAULT);
        $sql1 = "UPDATE nguoidung 
                 SET tenDangNhap = '$tenDangNhap',
                     matKhau = '$hashedPassword',
                     soDienThoai = '$soDienThoai'
                 WHERE id = $nguoiDungId";
    } else {
        // Không đổi mật khẩu
        $sql1 = "UPDATE nguoidung 
                 SET tenDangNhap = '$tenDangNhap',
                     soDienThoai = '$soDienThoai'
                 WHERE id = $nguoiDungId";
    }
    
    if (!$conn->query($sql1)) {
        throw new Exception('Lỗi cập nhật tài khoản: ' . $conn->error);
    }
    
    // 2. Cập nhật bảng bacsi với giới tính và năm làm việc
    $sql2 = "UPDATE bacsi 
             SET tenBacSi = '$tenBacSi',
                 maChuyenKhoa = '$maChuyenKhoa',
                 moTa = '$moTa',
                 gioiTinh = " . ($gioiTinh ? "'$gioiTinh'" : "NULL") . ",
                 namLamViec = " . ($namLamViec ? $namLamViec : "NULL") . "
             WHERE maBacSi = '$maBacSi'";
    
    if (!$conn->query($sql2)) {
        throw new Exception('Lỗi cập nhật hồ sơ bác sĩ: ' . $conn->error);
    }
    
    // Commit transaction
    $conn->commit();
    
    echo json_encode([
        'success' => true,
        'message' => 'Cập nhật thông tin bác sĩ thành công!'
    ], JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    // Rollback nếu có lỗi
    $conn->rollback();
    
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}

$conn->close();
?>