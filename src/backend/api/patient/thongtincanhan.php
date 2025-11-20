<?php
session_start();

// BẬT hiển thị lỗi để debug
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Credentials: true');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

try {
    // Kiểm tra đăng nhập
    if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
        echo json_encode([
            'success' => false,
            'requireLogin' => true,
            'message' => 'Vui lòng đăng nhập!'
        ], JSON_UNESCAPED_UNICODE);
        exit();
    }

    // Kết nối database
    $host = 'localhost';
    $dbname = 'datlichkham';
    $db_username = 'root';
    $db_password = '';
    
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $db_username, $db_password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Lấy user_id từ session
    $userId = $_SESSION['user_id'] ?? null;

    if (!$userId) {
        echo json_encode([
            'success' => false,
            'message' => 'Không tìm thấy thông tin người dùng trong session!',
            'debug' => [
                'session_data' => $_SESSION
            ]
        ], JSON_UNESCAPED_UNICODE);
        exit();
    }

    // Query lấy thông tin bệnh nhân theo tên cột mới
    $sql = "SELECT 
                bn.nguoiDungId,
                bn.maBenhNhan,
                bn.tenBenhNhan,
                bn.ngaySinh,
                bn.gioiTinh,
                bn.soTheBHYT,
                nd.soDienThoai
            FROM benhnhan bn
            INNER JOIN nguoidung nd ON bn.nguoiDungId = nd.id
            WHERE bn.nguoiDungId = :userId
            LIMIT 1";

    $stmt = $conn->prepare($sql);
    $stmt->execute(['userId' => $userId]);
    $benhnhan = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$benhnhan) {
        echo json_encode([
            'success' => false,
            'message' => 'Không tìm thấy thông tin bệnh nhân!',
            'debug' => [
                'userId' => $userId,
                'sql' => $sql
            ]
        ], JSON_UNESCAPED_UNICODE);
        exit();
    }

    // Format ngày sinh
    if ($benhnhan['ngaySinh']) {
        $benhnhan['ngaySinh'] = date('d/m/Y', strtotime($benhnhan['ngaySinh']));
    }

    // Trả về dữ liệu
    echo json_encode([
        'success' => true,
        'data' => $benhnhan,
        'message' => 'Lấy thông tin thành công!'
    ], JSON_UNESCAPED_UNICODE);

} catch(PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi database: ' . $e->getMessage(),
        'debug' => [
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ]
    ], JSON_UNESCAPED_UNICODE);
} catch(Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi hệ thống: ' . $e->getMessage(),
        'debug' => [
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ]
    ], JSON_UNESCAPED_UNICODE);
}

$conn = null;
?>