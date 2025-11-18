<?php
require_once '../../config/cors.php';
require_once '../../core/dp.php';
require_once '../../core/session.php';

require_role('quantri');

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $tenBenhNhan = trim($conn->real_escape_string($data['tenBenhNhan'] ?? ''));
    $soDienThoai = trim($conn->real_escape_string($data['soDienThoai'] ?? ''));
    $ngaySinh = $conn->real_escape_string($data['ngaySinh'] ?? '');
    $gioiTinh = $conn->real_escape_string($data['gioiTinh'] ?? '');
    $soTheBHYT = $conn->real_escape_string($data['soTheBHYT'] ?? null);
    $tenDangNhap = trim($conn->real_escape_string($data['tenDangNhap'] ?? ''));
    $matKhau = $data['matKhau'] ?? '';

    if (empty($tenBenhNhan) || empty($tenDangNhap) || empty($matKhau) || empty($ngaySinh) || empty($gioiTinh)) {
        throw new Exception('Vui lòng điền đầy đủ thông tin bắt buộc!');
    }

    // Kiểm tra username tồn tại
    $checkUserSql = "SELECT COUNT(*) as count FROM nguoidung WHERE tenDangNhap = '$tenDangNhap'";
    $checkUser = $conn->query($checkUserSql)->fetch_assoc()['count'];
    if ($checkUser > 0) {
        throw new Exception('Tên đăng nhập đã tồn tại!');
    }

    // Hash password
    $hashedPassword = password_hash($matKhau, PASSWORD_DEFAULT);

    // Tạo nguoidung
    $insertUserSql = "INSERT INTO nguoidung (tenDangNhap, matKhau, soDienThoai, vaiTro, trangThai) 
                      VALUES ('$tenDangNhap', '$hashedPassword', '$soDienThoai', 'benhnhan', 'Hoạt Động')";
    if (!$conn->query($insertUserSql)) {
        throw new Exception('Lỗi tạo tài khoản: ' . $conn->error);
    }
    $nguoiDungId = $conn->insert_id;

    // Tạo maBenhNhan auto: BN + YYYYMMDDHHMMSS + random 3 số
    $prefix = 'BN';
    $timestamp = date('YmdHi');
    $random = str_pad(rand(0, 999), 3, '0', STR_PAD_LEFT);
    $maBenhNhan = $prefix . $timestamp . $random;

    if (strtotime($ngaySinh) > time()) {
        throw new Exception('Ngày sinh không được vượt quá ngày hiện tại!');
    }

    // Insert benhnhan
    $insertPatientSql = "INSERT INTO benhnhan (nguoiDungId, maBenhNhan, tenBenhNhan, ngaySinh, gioiTinh, soTheBHYT) 
                         VALUES ($nguoiDungId, '$maBenhNhan', '$tenBenhNhan', '$ngaySinh', '$gioiTinh', " . ($soTheBHYT ? "'$soTheBHYT'" : "NULL") . ")";
    if (!$conn->query($insertPatientSql)) {
        // Rollback: Xóa nguoidung nếu insert benhnhan fail
        $conn->query("DELETE FROM nguoidung WHERE id = $nguoiDungId");
        throw new Exception('Lỗi tạo bệnh nhân: ' . $conn->error);
    }

    echo json_encode([
        'success' => true,
        'message' => 'Thêm bệnh nhân thành công!',
        'maBenhNhan' => $maBenhNhan
    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}

$conn->close();
?>