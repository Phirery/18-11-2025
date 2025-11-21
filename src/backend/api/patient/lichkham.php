<?php
session_start();

error_reporting(0);
ini_set('display_errors', 0);

// CORS headers
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: http://localhost');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

try {
    // Kiểm tra đăng nhập
    if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
        echo json_encode([
            'success' => false,
            'message' => 'Vui lòng đăng nhập để xem lịch khám!',
            'requireLogin' => true
        ], JSON_UNESCAPED_UNICODE);
        exit();
    }
    
    // Lấy mã bệnh nhân từ session
    $maBenhNhan = $_SESSION['maBenhNhan'] ?? null;
    
    if (empty($maBenhNhan)) {
        echo json_encode([
            'success' => false,
            'message' => 'Không tìm thấy thông tin bệnh nhân. Vui lòng đăng nhập lại!',
            'requireLogin' => true
        ], JSON_UNESCAPED_UNICODE);
        exit();
    }
    
    // Kết nối database
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "datlichkham";

    $conn = new mysqli($servername, $username, $password, $dbname);
    
    if ($conn->connect_error) {
        throw new Exception('Kết nối database thất bại');
    }
    
    $conn->set_charset("utf8mb4");
    
    // Query lấy lịch khám sắp tới
    $sql = "SELECT 
        lk.maLichKham,
        lk.ngayKham,
        lk.maCa,
        lk.maSuat,
        lk.maGoi,
        lk.trangThai,
        lk.ghiChu,
        bs.tenBacSi,
        bs.chuyenKhoa,
        goi.tenGoi
    FROM lichkham lk
    LEFT JOIN bacsi bs ON lk.maBacSi = bs.maBacSi
    LEFT JOIN goikham goi ON lk.maGoi = goi.maGoi
    WHERE lk.maBenhNhan = ? 
    AND lk.ngayKham >= CURDATE()
    AND lk.trangThai NOT IN ('Hoàn thành', 'Đã hủy')
    ORDER BY lk.ngayKham ASC, lk.maCa ASC";

    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        throw new Exception('Lỗi prepare statement: ' . $conn->error);
    }
    
    $stmt->bind_param("s", $maBenhNhan);
    $stmt->execute();
    $result = $stmt->get_result();

    $lichKham = array();

    while($row = $result->fetch_assoc()) {
        $ngayKham = 'N/A';
        if (!empty($row['ngayKham'])) {
            $date = new DateTime($row['ngayKham']);
            $ngayKham = $date->format('d/m/Y');
        }
        
        $gioKham = 'Ca ' . ($row['maCa'] ?: '?') . ' - Suất ' . ($row['maSuat'] ?: '?');
        
        $lichKham[] = array(
            'maLichKham' => $row['maLichKham'] ?: 'N/A',
            'ngayKham' => $ngayKham,
            'gioKham' => $gioKham,
            'bacSi' => !empty($row['tenBacSi']) ? 'BS. ' . $row['tenBacSi'] : 'Chưa có thông tin',
            'chuyenKhoa' => $row['chuyenKhoa'] ?: 'Chưa xác định',
            'goiKham' => $row['tenGoi'] ?: 'Thường',
            'trangThai' => $row['trangThai'] ?: 'Chờ xác nhận',
            'ghiChu' => $row['ghiChu'] ?: ''
        );
    }

    echo json_encode([
        'success' => true,
        'data' => $lichKham,
        'total' => count($lichKham),
        'message' => count($lichKham) > 0 ? 'Lấy dữ liệu thành công' : 'Không có lịch khám sắp tới',
        'user' => [
            'maBenhNhan' => $maBenhNhan,
            'username' => $_SESSION['username'] ?? '',
            'hoTen' => $_SESSION['hoTen'] ?? null
        ]
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

    $stmt->close();
    $conn->close();
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi: ' . $e->getMessage(),
        'data' => []
    ], JSON_UNESCAPED_UNICODE);
}
?>