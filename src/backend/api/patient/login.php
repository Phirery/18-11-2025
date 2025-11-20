<?php
session_start();

// BẬT hiển thị lỗi để debug
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

$debugInfo = [];

try {
    // 1. Kiểm tra phương thức request
    $debugInfo['request_method'] = $_SERVER['REQUEST_METHOD'];
    
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode([
            'success' => false,
            'message' => 'Chỉ chấp nhận POST request',
            'debug' => $debugInfo
        ], JSON_UNESCAPED_UNICODE);
        exit();
    }
    
    // 2. Kiểm tra dữ liệu POST
    $debugInfo['post_data'] = $_POST;
    $debugInfo['raw_input'] = file_get_contents('php://input');
    
    $tenDangNhap = trim($_POST['username'] ?? '');
    $matKhau = $_POST['password'] ?? '';
    
    $debugInfo['username_received'] = $tenDangNhap;
    $debugInfo['password_received'] = !empty($matKhau) ? '***có***' : '***trống***';
    
    if (empty($tenDangNhap) || empty($matKhau)) {
        echo json_encode([
            'success' => false,
            'message' => 'Vui lòng nhập đầy đủ thông tin!',
            'debug' => $debugInfo
        ], JSON_UNESCAPED_UNICODE);
        exit();
    }
    
    // 3. Kết nối database
    $host = 'localhost';
    $dbname = 'datlichkham';
    $db_username = 'root';
    $db_password = '';
    
    $debugInfo['db_config'] = [
        'host' => $host,
        'dbname' => $dbname,
        'username' => $db_username
    ];
    
    try {
        $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $db_username, $db_password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $debugInfo['db_connection'] = 'Thành công';
    } catch(PDOException $e) {
        $debugInfo['db_connection'] = 'Thất bại: ' . $e->getMessage();
        throw $e;
    }
    
    // 4. Kiểm tra bảng nguoidung có tồn tại không
    try {
        $checkTable = $conn->query("SHOW TABLES LIKE 'nguoidung'");
        $debugInfo['table_nguoidung_exists'] = $checkTable->rowCount() > 0 ? 'Có' : 'Không';
        
        $checkTable2 = $conn->query("SHOW TABLES LIKE 'benhnhan'");
        $debugInfo['table_benhnhan_exists'] = $checkTable2->rowCount() > 0 ? 'Có' : 'Không';
    } catch(Exception $e) {
        $debugInfo['check_tables_error'] = $e->getMessage();
    }
    
    // 5. Query tìm user (không JOIN trước để test)
    $sql = "SELECT id, tenDangNhap, matKhau, soDienThoai, vaiTro, trangThai 
            FROM nguoidung 
            WHERE tenDangNhap = :tenDangNhap
            LIMIT 1";
    
    $debugInfo['sql_query'] = $sql;
    
    $stmt = $conn->prepare($sql);
    $stmt->execute(['tenDangNhap' => $tenDangNhap]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $debugInfo['user_found'] = $user ? 'Có' : 'Không';
    
    if ($user) {
        $debugInfo['user_data'] = [
            'id' => $user['id'],
            'tenDangNhap' => $user['tenDangNhap'],
            'soDienThoai' => $user['soDienThoai'],
            'vaiTro' => $user['vaiTro'],
            'trangThai' => $user['trangThai'],
            'matKhau_format' => substr($user['matKhau'], 0, 10) . '...' // Chỉ hiển thị 10 ký tự đầu
        ];
    }
    
    if (!$user) {
        // Kiểm tra xem có user nào trong DB không
        $countUsers = $conn->query("SELECT COUNT(*) as total FROM nguoidung")->fetch();
        $debugInfo['total_users_in_db'] = $countUsers['total'];
        
        // Lấy danh sách username (5 user đầu)
        $allUsers = $conn->query("SELECT tenDangNhap FROM nguoidung LIMIT 5")->fetchAll(PDO::FETCH_COLUMN);
        $debugInfo['sample_usernames'] = $allUsers;
        
        echo json_encode([
            'success' => false,
            'message' => 'Tên đăng nhập không tồn tại!',
            'debug' => $debugInfo
        ], JSON_UNESCAPED_UNICODE);
        exit();
    }
    
    // 6. Kiểm tra trạng thái
    if ($user['trangThai'] !== 'Hoạt Động') {
        echo json_encode([
            'success' => false,
            'message' => 'Tài khoản đã bị khóa! Trạng thái: ' . $user['trangThai'],
            'debug' => $debugInfo
        ], JSON_UNESCAPED_UNICODE);
        exit();
    }
    
    // 7. Kiểm tra mật khẩu
    $passwordVerified = password_verify($matKhau, $user['matKhau']);
    $passwordPlainMatch = ($matKhau === $user['matKhau']);
    
    $debugInfo['password_check'] = [
        'password_verify' => $passwordVerified ? 'Khớp' : 'Không khớp',
        'plain_match' => $passwordPlainMatch ? 'Khớp' : 'Không khớp',
        'password_hash_format' => (strpos($user['matKhau'], '$2y$') === 0) ? 'Đã hash (bcrypt)' : 'Plain text hoặc format khác'
    ];
    
    if (!$passwordVerified && !$passwordPlainMatch) {
        echo json_encode([
            'success' => false,
            'message' => 'Mật khẩu không đúng!',
            'debug' => $debugInfo
        ], JSON_UNESCAPED_UNICODE);
        exit();
    }
    
    // 8. Thử JOIN với bảng benhnhan
    try {
        $sql2 = "SELECT bn.maBenhNhan, bn.hoTen 
                FROM benhnhan bn 
                WHERE bn.soDienThoai = :soDienThoai
                LIMIT 1";
        
        $stmt2 = $conn->prepare($sql2);
        $stmt2->execute(['soDienThoai' => $user['soDienThoai']]);
        $benhnhan = $stmt2->fetch(PDO::FETCH_ASSOC);
        
        $debugInfo['benhnhan_found'] = $benhnhan ? 'Có' : 'Không';
        
        if ($benhnhan) {
            $debugInfo['benhnhan_data'] = $benhnhan;
        } else {
            // Kiểm tra có bệnh nhân nào không
            $countBN = $conn->query("SELECT COUNT(*) as total FROM benhnhan")->fetch();
            $debugInfo['total_benhnhan_in_db'] = $countBN['total'];
        }
        
    } catch(Exception $e) {
        $debugInfo['benhnhan_query_error'] = $e->getMessage();
    }
    
    // 9. Lưu session
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['tenDangNhap'];
    $_SESSION['vai_tro'] = $user['vaiTro'];
    $_SESSION['so_dien_thoai'] = $user['soDienThoai'];
    $_SESSION['maBenhNhan'] = $benhnhan['maBenhNhan'] ?? null;
    $_SESSION['hoTen'] = $benhnhan['hoTen'] ?? null;
    $_SESSION['logged_in'] = true;
    
    $debugInfo['session_saved'] = [
        'user_id' => $_SESSION['user_id'],
        'username' => $_SESSION['username'],
        'maBenhNhan' => $_SESSION['maBenhNhan']
    ];
    
    // 10. Thành công
    echo json_encode([
        'success' => true,
        'message' => 'Đăng nhập thành công!',
        'user' => [
            'id' => $user['id'],
            'username' => $user['tenDangNhap'],
            'vai_tro' => $user['vaiTro'],
            'so_dien_thoai' => $user['soDienThoai'],
            'maBenhNhan' => $benhnhan['maBenhNhan'] ?? null,
            'hoTen' => $benhnhan['hoTen'] ?? null
        ],
        'redirect' => 'index.html',
        'debug' => $debugInfo
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    
} catch(Exception $e) {
    $debugInfo['exception'] = [
        'message' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'trace' => $e->getTraceAsString()
    ];
    
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi hệ thống: ' . $e->getMessage(),
        'debug' => $debugInfo
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
}

$conn = null;
?>