<?php
require_once '../../config/cors.php';
require_once '../../core/dp.php';

session_start();

// Get JSON input
$input = json_decode(file_get_contents("php://input"), true);

if (!isset($input['fullname'], $input['phone'], $input['gender'], $input['birthdate'], $input['username'], $input['password'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Thiếu thông tin đăng ký'
    ]);
    exit;
}

$fullname = trim($input['fullname']);
$phone = trim($input['phone']);
$gender = $input['gender'];
$birthdate = $input['birthdate'];
$username = trim($input['username']);
$password = $input['password'];

// Validate
if (strlen($username) < 4) {
    echo json_encode([
        'success' => false,
        'message' => 'Tên đăng nhập phải có ít nhất 4 ký tự'
    ]);
    exit;
}

if (strlen($password) < 6) {
    echo json_encode([
        'success' => false,
        'message' => 'Mật khẩu phải có ít nhất 6 ký tự'
    ]);
    exit;
}

if (!preg_match('/^[0-9]{10,11}$/', $phone)) {
    echo json_encode([
        'success' => false,
        'message' => 'Số điện thoại không hợp lệ'
    ]);
    exit;
}

try {
    // Kiểm tra tên đăng nhập đã tồn tại
    $stmt = $conn->prepare("SELECT id FROM nguoidung WHERE tenDangNhap = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Tên đăng nhập đã tồn tại'
        ]);
        $stmt->close();
        $conn->close();
        exit;
    }
    $stmt->close();
    
    // Kiểm tra số điện thoại đã tồn tại
    $stmt = $conn->prepare("SELECT id FROM nguoidung WHERE soDienThoai = ?");
    $stmt->bind_param("s", $phone);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Số điện thoại đã được đăng ký'
        ]);
        $stmt->close();
        $conn->close();
        exit;
    }
    $stmt->close();
    
    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    // Bắt đầu transaction
    $conn->begin_transaction();
    
    try {
        // Thêm vào bảng nguoidung
        $stmt = $conn->prepare("
            INSERT INTO nguoidung (tenDangNhap, matKhau, soDienThoai, vaiTro, trangThai) 
            VALUES (?, ?, ?, 'benhnhan', 'Hoạt Động')
        ");
        $stmt->bind_param("sss", $username, $hashedPassword, $phone);
        $stmt->execute();
        $nguoiDungId = $conn->insert_id;
        $stmt->close();
        
        // Tạo mã bệnh nhân
        $maBenhNhan = 'BN' . date('YmdHis') . substr($nguoiDungId, -3);
        
        // Thêm vào bảng benhnhan
        $stmt = $conn->prepare("
            INSERT INTO benhnhan (nguoiDungId, maBenhNhan, tenBenhNhan, ngaySinh, gioiTinh) 
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("issss", $nguoiDungId, $maBenhNhan, $fullname, $birthdate, $gender);
        $stmt->execute();
        $stmt->close();
        
        // Commit transaction
        $conn->commit();
        
        echo json_encode([
            'success' => true,
            'message' => 'Đăng ký thành công'
        ]);
        
    } catch (Exception $e) {
        $conn->rollback();
        throw $e;
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi hệ thống: ' . $e->getMessage()
    ]);
}

$conn->close();
?>