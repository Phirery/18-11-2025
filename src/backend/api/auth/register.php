<?php
require_once '../../config/cors.php';
require_once '../../core/dp.php';

session_start();

// Get JSON input
$input = json_decode(file_get_contents("php://input"), true);

// Validate required fields
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
$bhyt = isset($input['bhyt']) && !empty($input['bhyt']) ? trim($input['bhyt']) : null;
$username = trim($input['username']);
$password = $input['password'];

// Validate username
if (strlen($username) < 4) {
    echo json_encode([
        'success' => false,
        'message' => 'Tên đăng nhập phải có ít nhất 4 ký tự'
    ]);
    exit;
}

// Validate password
if (strlen($password) < 6) {
    echo json_encode([
        'success' => false,
        'message' => 'Mật khẩu phải có ít nhất 6 ký tự'
    ]);
    exit;
}

// Validate phone
if (!preg_match('/^[0-9]{10,11}$/', $phone)) {
    echo json_encode([
        'success' => false,
        'message' => 'Số điện thoại không hợp lệ'
    ]);
    exit;
}

// Validate BHYT if provided
if ($bhyt !== null && !preg_match('/^[A-Z0-9]{15}$/', $bhyt)) {
    echo json_encode([
        'success' => false,
        'message' => 'Số thẻ BHYT không hợp lệ. Phải gồm 15 ký tự chữ và số'
    ]);
    exit;
}

// Validate gender
if (!in_array($gender, ['nam', 'nu', 'khac'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Giới tính không hợp lệ'
    ]);
    exit;
}

// Validate birthdate
$today = new DateTime();
$birth = new DateTime($birthdate);
if ($birth > $today) {
    echo json_encode([
        'success' => false,
        'message' => 'Ngày sinh không hợp lệ'
    ]);
    exit;
}

try {
    // Check if username already exists
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
    
    // Check if phone already exists
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
    
    // Check if BHYT already exists (if provided)
    if ($bhyt !== null) {
        $stmt = $conn->prepare("SELECT maBenhNhan FROM benhnhan WHERE soTheBHYT = ?");
        $stmt->bind_param("s", $bhyt);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            echo json_encode([
                'success' => false,
                'message' => 'Số thẻ BHYT đã được đăng ký'
            ]);
            $stmt->close();
            $conn->close();
            exit;
        }
        $stmt->close();
    }
    
    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    // Begin transaction
    $conn->begin_transaction();
    
    try {
        // Insert into nguoidung table
        $stmt = $conn->prepare("
            INSERT INTO nguoidung (tenDangNhap, matKhau, soDienThoai, vaiTro, trangThai, ngayCapNhatTaiKhoan) 
            VALUES (?, ?, ?, 'benhnhan', 'Hoạt Động', NOW())
        ");
        $stmt->bind_param("sss", $username, $hashedPassword, $phone);
        $stmt->execute();
        $nguoiDungId = $conn->insert_id;
        $stmt->close();
        
        // Generate patient ID
        $maBenhNhan = 'BN' . date('Ymd') . str_pad($nguoiDungId, 8, '0', STR_PAD_LEFT);
        
        // Insert into benhnhan table
        if ($bhyt !== null) {
            $stmt = $conn->prepare("
                INSERT INTO benhnhan (nguoiDungId, maBenhNhan, tenBenhNhan, ngaySinh, gioiTinh, soTheBHYT) 
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmt->bind_param("isssss", $nguoiDungId, $maBenhNhan, $fullname, $birthdate, $gender, $bhyt);
        } else {
            $stmt = $conn->prepare("
                INSERT INTO benhnhan (nguoiDungId, maBenhNhan, tenBenhNhan, ngaySinh, gioiTinh) 
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->bind_param("issss", $nguoiDungId, $maBenhNhan, $fullname, $birthdate, $gender);
        }
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