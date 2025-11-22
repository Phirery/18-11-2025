<?php
require_once '../../config/cors.php';
require_once '../../core/dp.php';

// Lấy dữ liệu từ request
$data = json_decode(file_get_contents('php://input'), true);

$hoTen = trim($data['hoTen'] ?? '');
$email = trim($data['email'] ?? '');
$soDienThoai = trim($data['soDienThoai'] ?? '');
$chuDe = trim($data['chuDe'] ?? '');
$noiDung = trim($data['noiDung'] ?? '');

// Validation
if (empty($hoTen) || empty($email) || empty($soDienThoai) || empty($chuDe) || empty($noiDung)) {
    echo json_encode([
        'success' => false,
        'message' => 'Vui lòng điền đầy đủ thông tin!'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// Validate email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode([
        'success' => false,
        'message' => 'Email không hợp lệ!'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// Validate phone number (10-11 digits)
if (!preg_match('/^[0-9]{10,11}$/', $soDienThoai)) {
    echo json_encode([
        'success' => false,
        'message' => 'Số điện thoại không hợp lệ! Vui lòng nhập 10-11 chữ số.'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    // Thêm liên hệ vào database
    $stmt = $conn->prepare("
        INSERT INTO lienhe (hoTen, email, soDienThoai, chuDe, noiDung, thoiGianGui) 
        VALUES (?, ?, ?, ?, ?, NOW())
    ");
    
    $stmt->bind_param("sssss", $hoTen, $email, $soDienThoai, $chuDe, $noiDung);
    
    if ($stmt->execute()) {
        $maLienHe = $conn->insert_id;
        
        echo json_encode([
            'success' => true,
            'message' => 'Gửi liên hệ thành công! Chúng tôi sẽ phản hồi sớm nhất có thể.',
            'maLienHe' => $maLienHe
        ], JSON_UNESCAPED_UNICODE);
    } else {
        throw new Exception('Không thể gửi liên hệ: ' . $stmt->error);
    }
    
    $stmt->close();
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}

$conn->close();
?>