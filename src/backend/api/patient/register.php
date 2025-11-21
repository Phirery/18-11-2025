<?php //Register
// Cấu hình database
$host = 'localhost';
$dbname = 'datlichkham';
$db_username = 'root';
$db_password = '';

header('Content-Type: application/json; charset=utf-8');

// Kết nối database
try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $db_username, $db_password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Lỗi kết nối: ' . $e->getMessage()]);
    exit();
}

// Lấy dữ liệu từ form
$tenDangNhap = trim($_POST['username'] ?? '');
$matKhau = $_POST['password'] ?? '';
$soDienThoai = trim($_POST['phone'] ?? '');

// Validate đơn giản
if (empty($tenDangNhap) || empty($matKhau) || empty($soDienThoai)) {
    echo json_encode(['success' => false, 'message' => 'Vui lòng điền đầy đủ thông tin!']);
    exit();
}

if (strlen($tenDangNhap) < 4) {
    echo json_encode(['success' => false, 'message' => 'Tên đăng nhập phải có ít nhất 4 ký tự!']);
    exit();
}

if (strlen($matKhau) < 6) {
    echo json_encode(['success' => false, 'message' => 'Mật khẩu phải có ít nhất 6 ký tự!']);
    exit();
}

try {
    // Bắt đầu transaction để đảm bảo cả 2 bảng đều được tạo
    $conn->beginTransaction();

    // Kiểm tra tên đăng nhập đã tồn tại chưa
    $stmt = $conn->prepare("SELECT id FROM nguoidung WHERE tenDangNhap = :tenDangNhap");
    $stmt->execute(['tenDangNhap' => $tenDangNhap]);
    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => false, 'message' => 'Tên đăng nhập đã tồn tại!']);
        exit();
    }

    // Kiểm tra số điện thoại đã tồn tại chưa
    $stmt = $conn->prepare("SELECT id FROM nguoidung WHERE soDienThoai = :soDienThoai");
    $stmt->execute(['soDienThoai' => $soDienThoai]);
    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => false, 'message' => 'Số điện thoại đã được đăng ký!']);
        exit();
    }

    // Mã hóa mật khẩu
    $matKhauHash = password_hash($matKhau, PASSWORD_DEFAULT);

    // BƯỚC 1: Thêm người dùng mới vào bảng nguoidung
    $sql = "INSERT INTO nguoidung (tenDangNhap, matKhau, soDienThoai, vaiTro, trangThai) 
            VALUES (:tenDangNhap, :matKhau, :soDienThoai, 'benhnhan', 'Hoạt Động')";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        'tenDangNhap' => $tenDangNhap,
        'matKhau' => $matKhauHash,
        'soDienThoai' => $soDienThoai
    ]);

    // Lấy ID của user vừa tạo
    $userId = $conn->lastInsertId();

    // BƯỚC 2: Tự động tạo mã bệnh nhân
    // Format: BN-YYYY-XXXX (BN-2024-0001, BN-2024-0002, ...)
    $year = date('Y');
    
    // Lấy số thứ tự bệnh nhân cuối cùng trong năm
    $stmt = $conn->prepare("SELECT maBenhNhan FROM benhnhan WHERE maBenhNhan LIKE :pattern ORDER BY maBenhNhan DESC LIMIT 1");
    $stmt->execute(['pattern' => "BN-{$year}-%"]);
    $lastBenhNhan = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($lastBenhNhan) {
        // Tách số thứ tự từ mã cuối cùng (VD: BN-2024-0005 → 5)
        $lastNumber = intval(substr($lastBenhNhan['maBenhNhan'], -4));
        $newNumber = $lastNumber + 1;
    } else {
        // Nếu chưa có bệnh nhân nào trong năm, bắt đầu từ 1
        $newNumber = 1;
    }
    
    // Tạo mã bệnh nhân mới (format: 4 chữ số, có leading zero)
    $maBenhNhan = sprintf("BN-%s-%04d", $year, $newNumber);

    // Lấy dữ liệu từ form ở đầu file (sau phần validate)
$tenBenhNhan = trim($_POST['fullname'] ?? $tenDangNhap);
$ngaySinh = $_POST['birthdate'] ?? null;
$gioiTinh = $_POST['gender'] ?? null;
$soTheBHYT = trim($_POST['bhyt'] ?? '');

// ... code ở giữa ...

// BƯỚC 3: Thêm bệnh nhân vào bảng benhnhan với dữ liệu từ form
$sql2 = "INSERT INTO benhnhan (nguoiDungId, maBenhNhan, tenBenhNhan, ngaySinh, gioiTinh, soTheBHYT) 
         VALUES (:nguoiDungId, :maBenhNhan, :tenBenhNhan, :ngaySinh, :gioiTinh, :soTheBHYT)";

$stmt2 = $conn->prepare($sql2);
$stmt2->execute([
    'nguoiDungId' => $userId,
    'maBenhNhan' => $maBenhNhan,
    'tenBenhNhan' => $tenBenhNhan,           // Từ form: fullname
    'ngaySinh' => $ngaySinh,                 // Từ form: birthdate
    'gioiTinh' => $gioiTinh,                 // Từ form: gender
    'soTheBHYT' => !empty($soTheBHYT) ? $soTheBHYT : null  // Từ form: bhyt
]);

    // Commit transaction - Cả 2 thao tác đều thành công
    $conn->commit();

    echo json_encode([
        'success' => true, 
        'message' => 'Đăng ký thành công!',
        'user_id' => $userId,
        'ma_benh_nhan' => $maBenhNhan
    ]);

} catch(PDOException $e) {
    // Rollback nếu có lỗi
    if ($conn->inTransaction()) {
        $conn->rollBack();
    }
    echo json_encode(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
}

$conn = null;
?>