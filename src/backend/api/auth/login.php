<?php
/*
   SỬA FILE: src/backend/api/auth/login.php
   Thay thế toàn bộ nội dung
*/

require_once '../../config/cors.php';
require_once '../../core/dp.php';

session_start();

// Get JSON input
$input = json_decode(file_get_contents("php://input"), true);

if (!isset($input['username'], $input['password'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Thiếu thông tin đăng nhập'
    ]);
    exit;
}

$username = trim($input['username']);
$password = $input['password'];

try {
    $stmt = $conn->prepare("
        SELECT id, tenDangNhap, matKhau, vaiTro, trangThai 
        FROM nguoidung 
        WHERE (tenDangNhap = ? OR soDienThoai = ?)
    ");
    
    $stmt->bind_param("ss", $username, $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Tài khoản không tồn tại'
        ]);
        $stmt->close();
        $conn->close();
        exit;
    }
    
    $user = $result->fetch_assoc();
    $stmt->close();

    if ($user['trangThai'] === 'Khóa') {
        echo json_encode([
            'success' => false,
            'message' => 'Tài khoản đã bị khóa. Không thể đăng nhập'
        ]);
        $conn->close();
        exit;
    }

    // Verify password
    $dbPassword = $user['matKhau'];
    $isHashVerified = password_verify($password, $dbPassword);

    if (!$isHashVerified) {
        // Check plaintext (for legacy passwords)
        $isPlaintextVerified = hash_equals($dbPassword, $password);

        if ($isPlaintextVerified) {
            // Update to hashed password
            $newHash = password_hash($password, PASSWORD_DEFAULT);
            $update = $conn->prepare("UPDATE nguoidung SET matKhau = ? WHERE id = ?");
            $update->bind_param("si", $newHash, $user['id']);
            $update->execute();
            $update->close();
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Tên đăng nhập hoặc mật khẩu không đúng'
            ]);
            $conn->close();
            exit;
        }
    }

    // Set session
    $_SESSION['id'] = $user['id'];
    $_SESSION['vaiTro'] = $user['vaiTro'];
    $_SESSION['tenDangNhap'] = $user['tenDangNhap'];

    // ===== THÊM: Lấy thông tin bổ sung dựa theo vai trò =====
    $extraData = [];
    
    if ($user['vaiTro'] === 'benhnhan') {
        $stmt = $conn->prepare("
            SELECT maBenhNhan, tenBenhNhan 
            FROM benhnhan 
            WHERE nguoiDungId = ?
        ");
        $stmt->bind_param("i", $user['id']);
        $stmt->execute();
        $patientResult = $stmt->get_result();
        
        if ($patientResult->num_rows > 0) {
            $patient = $patientResult->fetch_assoc();
            $extraData['maBenhNhan'] = $patient['maBenhNhan'];
            $extraData['hoTen'] = $patient['tenBenhNhan'];
            
            // Lưu vào session
            $_SESSION['maBenhNhan'] = $patient['maBenhNhan'];
            $_SESSION['hoTen'] = $patient['tenBenhNhan'];
        }
        $stmt->close();
        
    } elseif ($user['vaiTro'] === 'bacsi') {
        $stmt = $conn->prepare("
            SELECT maBacSi, tenBacSi 
            FROM bacsi 
            WHERE nguoiDungId = ?
        ");
        $stmt->bind_param("i", $user['id']);
        $stmt->execute();
        $doctorResult = $stmt->get_result();
        
        if ($doctorResult->num_rows > 0) {
            $doctor = $doctorResult->fetch_assoc();
            $extraData['maBacSi'] = $doctor['maBacSi'];
            $extraData['hoTen'] = $doctor['tenBacSi'];
            
            // Lưu vào session
            $_SESSION['maBacSi'] = $doctor['maBacSi'];
            $_SESSION['hoTen'] = $doctor['tenBacSi'];
        }
        $stmt->close();
        
    } elseif ($user['vaiTro'] === 'quantri') {
        $stmt = $conn->prepare("
            SELECT maQuanTriVien 
            FROM quantrivien 
            WHERE nguoiDungId = ?
        ");
        $stmt->bind_param("i", $user['id']);
        $stmt->execute();
        $adminResult = $stmt->get_result();
        
        if ($adminResult->num_rows > 0) {
            $admin = $adminResult->fetch_assoc();
            $extraData['maQuanTriVien'] = $admin['maQuanTriVien'];
            
            // Lưu vào session
            $_SESSION['maQuanTriVien'] = $admin['maQuanTriVien'];
        }
        $stmt->close();
    }
    // ===== KẾT THÚC PHẦN THÊM =====

    echo json_encode(array_merge([
        'success' => true,
        'message' => 'Đăng nhập thành công',
        'role' => $user['vaiTro'],
        'username' => $user['tenDangNhap']
    ], $extraData));

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi hệ thống: ' . $e->getMessage()
    ]);
}

$conn->close();
?>