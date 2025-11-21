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
    
    $tenBacSi = $conn->real_escape_string($data['tenBacSi']);
    $soDienThoai = $conn->real_escape_string($data['soDienThoai']);
    $maChuyenKhoa = $conn->real_escape_string($data['maChuyenKhoa']);
    $tenDangNhap = $conn->real_escape_string($data['tenDangNhap']);
    $matKhau = $conn->real_escape_string($data['matKhau']);
    $gioiTinh = isset($data['gioiTinh']) ? $conn->real_escape_string($data['gioiTinh']) : null;
    $namLamViec = isset($data['namLamViec']) ? intval($data['namLamViec']) : null;
    
    // Kiểm tra tên đăng nhập đã tồn tại chưa
    $checkSql = "SELECT COUNT(*) as count FROM nguoidung WHERE tenDangNhap = '$tenDangNhap'";
    $checkResult = $conn->query($checkSql);
    $count = $checkResult->fetch_assoc()['count'];
    
    if ($count > 0) {
        throw new Exception('Tên đăng nhập đã tồn tại!');
    }
    
    // Mã hóa mật khẩu
    $hashedPassword = password_hash($matKhau, PASSWORD_DEFAULT);
    
    // 1. Thêm vào bảng nguoidung
    $sql1 = "INSERT INTO nguoidung (tenDangNhap, matKhau, soDienThoai, vaiTro) 
             VALUES ('$tenDangNhap', '$hashedPassword', '$soDienThoai', 'bacsi')";
    
    if (!$conn->query($sql1)) {
        throw new Exception('Lỗi tạo tài khoản: ' . $conn->error);
    }
    
    // Lấy ID vừa tạo
    $nguoiDungId = $conn->insert_id;
    
    // 2. Tạo mã bác sĩ tự động (format: BS + timestamp)
    $maBacSi = 'BS' . date('YmdHi') . sprintf('%03d', rand(0, 999));
    
    // 3. Thêm vào bảng bacsi với giới tính và năm làm việc
    $sql2 = "INSERT INTO bacsi (nguoiDungId, maBacSi, tenBacSi, maChuyenKhoa, gioiTinh, namLamViec) 
             VALUES ($nguoiDungId, '$maBacSi', '$tenBacSi', '$maChuyenKhoa', " . 
             ($gioiTinh ? "'$gioiTinh'" : "NULL") . ", " . 
             ($namLamViec ? $namLamViec : "NULL") . ")";
    
    if (!$conn->query($sql2)) {
        throw new Exception('Lỗi tạo hồ sơ bác sĩ: ' . $conn->error);
    }
    
    // Commit transaction
    $conn->commit();
    
    echo json_encode([
        'success' => true,
        'message' => 'Thêm bác sĩ thành công!',
        'maBacSi' => $maBacSi
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