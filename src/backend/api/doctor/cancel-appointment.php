<?php
// 1. Tải các file cấu hình và bảo vệ
require_once '../../config/cors.php';
require_once '../../core/dp.php';
require_once '../../core/session.php';

// 2. Yêu cầu vai trò: Chỉ 'bacsi' mới được truy cập
require_role('bacsi');

// Lấy ID người dùng (bác sĩ) từ session
$nguoiDungId = $_SESSION['id'];

// Lấy dữ liệu JSON từ body request
$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['maLichKham'])) {
    echo json_encode(['success' => false, 'message' => 'Thiếu thông tin mã lịch khám']);
    exit;
}
$maLichKham = $input['maLichKham'];

try {
    // 3. Lấy maBacSi của bác sĩ đang đăng nhập (để bảo mật)
    $stmt_bs = $conn->prepare("SELECT maBacSi FROM bacsi WHERE nguoiDungId = ?");
    $stmt_bs->bind_param("i", $nguoiDungId);
    $stmt_bs->execute();
    $result_bs = $stmt_bs->get_result();
    
    if ($result_bs->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Không tìm thấy thông tin bác sĩ liên kết.']);
        exit;
    }
    
    $bacsi = $result_bs->fetch_assoc();
    $maBacSi = $bacsi['maBacSi']; // Đây là mã bác sĩ đã được xác thực
    $stmt_bs->close();

    // 4. Cập nhật lịch khám, chỉ cập nhật nếu maLichKham VÀ maBacSi đều khớp
    //    và trạng thái đang là 'Đã đặt'
    $stmt = $conn->prepare("
        UPDATE lichkham 
        SET trangThai = 'Hủy'
        WHERE maLichKham = ? AND maBacSi = ? AND trangThai = 'Đã đặt'
    ");
    
    // Giả sử maLichKham là kiểu chuỗi (string), nếu là số thì dùng "is"
    $stmt->bind_param("ss", $maLichKham, $maBacSi); 
    
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(['success' => true, 'message' => 'Đã hủy lịch khám thành công.']);
        } else {
            // Không có dòng nào bị ảnh hưởng
            echo json_encode([
                'success' => false, 
                'message' => 'Không thể hủy. Lịch không tồn tại, không phải của bạn, hoặc đã ở trạng thái không thể hủy.'
            ]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Lỗi khi thực thi lệnh hủy.']);
    }
    $stmt->close();

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Lỗi máy chủ: ' . $e->getMessage()]);
}

$conn->close();
?>