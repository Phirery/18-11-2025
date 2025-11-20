<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// Thông tin kết nối database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "datlichkham"; // đổi nếu database khác

// Kết nối
$conn = new mysqli($servername, $username, $password, $dbname);
$conn->set_charset("utf8mb4");

// Kiểm tra kết nối
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => $conn->connect_error]);
    exit;
}

// SQL lấy dữ liệu bác sĩ
$sql = "SELECT maBacSi, tenBacSi, chuyenGia, maChuyenKhoa, moTa FROM bacsi";
$result = $conn->query($sql);

$doctors = [];
$defaultImg = 'http://localhost/DO_AN_1/code_doan1/src/frontend/assets/images/doctor1.jpg'; // ảnh mặc định
$defaultProfile = '#'; // link mặc định

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $doctors[] = [
            'name' => $row['tenBacSi'],
            'title' => $row['chuyenGia'],
            'specialty' => $row['maChuyenKhoa'],
            'interests' => $row['moTa'],
            'img' => $defaultImg,
            'profile' => $defaultProfile
        ];
    }
    echo json_encode(['success' => true, 'data' => $doctors], JSON_UNESCAPED_UNICODE);
} else {
    echo json_encode(['success' => false, 'message' => $conn->error]);
}

$conn->close();
?>
