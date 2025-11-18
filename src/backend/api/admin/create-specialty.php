<?php
require_once '../../config/cors.php';
require_once '../../core/dp.php';
require_once '../../core/session.php';

require_role('quantri');

// Bắt đầu một transaction để đảm bảo an toàn dữ liệu khi tạo mã
$conn->begin_transaction();

try {
    // Lấy dữ liệu POST
    $data = json_decode(file_get_contents('php://input'), true);

    if (empty($data['tenChuyenKhoa']) || empty($data['maKhoa'])) {
        throw new Exception('Vui lòng cung cấp đủ Tên chuyên khoa và Mã khoa.');
    }
    
    $tenChuyenKhoa = $conn->real_escape_string($data['tenChuyenKhoa']);
    $maKhoa = $conn->real_escape_string($data['maKhoa']);
    $moTa = isset($data['moTa']) ? $conn->real_escape_string($data['moTa']) : '';
    
    // 1. Kiểm tra xem mã khoa có tồn tại không
    $checkKhoaSql = "SELECT COUNT(*) as count FROM khoa WHERE maKhoa = ?";
    $stmtKhoa = $conn->prepare($checkKhoaSql);
    $stmtKhoa->bind_param("s", $maKhoa);
    $stmtKhoa->execute();
    $checkKhoaResult = $stmtKhoa->get_result();
    if ($checkKhoaResult->fetch_assoc()['count'] == 0) {
        throw new Exception('Mã khoa không tồn tại. Không thể thêm chuyên khoa.');
    }
    $stmtKhoa->close();

    // 2. Đếm số lượng chuyên khoa HIỆN CÓ của khoa này
    // Dùng FOR UPDATE để khóa dòng/bảng, tránh 2 người cùng thêm 1 lúc
    $countSql = "SELECT COUNT(*) as count FROM chuyenkhoa WHERE maKhoa = ? FOR UPDATE";
    $stmtCount = $conn->prepare($countSql);
    $stmtCount->bind_param("s", $maKhoa);
    $stmtCount->execute();
    $result = $stmtCount->get_result();
    $count = $result->fetch_assoc()['count'];
    $stmtCount->close();
    
    // 3. Tính số thứ tự tiếp theo
    // Nếu có 0 chuyên khoa -> $count = 0 -> số tiếp theo là 1
    // Nếu có 2 chuyên khoa -> $count = 2 -> số tiếp theo là 3
    $nextSequentialNumber = $count + 1;
    
    // 4. Định dạng số thứ tự thành 2 chữ số (VD: 1 -> "01", 3 -> "03", 10 -> "10")
    $suffix = str_pad($nextSequentialNumber, 2, '0', STR_PAD_LEFT);
    
    // 5. Tạo mã chuyên khoa mới
    // Ví dụ: maKhoa = "EYE1025", suffix = "03" -> maChuyenKhoa = "EYE102503"
    $maChuyenKhoa = $maKhoa . $suffix;
    
    // 6. Thêm chuyên khoa mới vào database (dùng prepared statement để an toàn)
    $sql = "INSERT INTO chuyenkhoa (maChuyenKhoa, tenChuyenKhoa, maKhoa, moTa) 
            VALUES (?, ?, ?, ?)";
    
    $stmtInsert = $conn->prepare($sql);
    $stmtInsert->bind_param("ssss", $maChuyenKhoa, $tenChuyenKhoa, $maKhoa, $moTa);
    
    if ($stmtInsert->execute() === TRUE) {
        // Nếu thành công, commit transaction
        $conn->commit();
        echo json_encode([
            'success' => true,
            'message' => 'Thêm chuyên khoa thành công!',
            'maChuyenKhoa' => $maChuyenKhoa
        ], JSON_UNESCAPED_UNICODE);
    } else {
        throw new Exception('Lỗi khi thực thi câu lệnh INSERT: ' . $stmtInsert->error);
    }
    $stmtInsert->close();

} catch (Exception $e) {
    // Nếu có bất kỳ lỗi nào, rollback transaction
    $conn->rollback();
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}

$conn->close();
?>