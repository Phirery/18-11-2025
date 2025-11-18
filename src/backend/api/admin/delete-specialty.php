<?php
require_once '../../config/cors.php';
require_once '../../core/dp.php';
require_once '../../core/session.php';

require_role('quantri');

$conn->begin_transaction();

try {
    // Lấy dữ liệu POST
    $data = json_decode(file_get_contents('php://input'), true);

    if (empty($data['maChuyenKhoa'])) {
        throw new Exception('Vui lòng cung cấp Mã chuyên khoa.');
    }
    
    $maChuyenKhoa = $conn->real_escape_string($data['maChuyenKhoa']);
    
    // 1. Kiểm tra xem chuyên khoa có tồn tại không
    $checkExistSql = "SELECT COUNT(*) as count FROM chuyenkhoa WHERE maChuyenKhoa = ?";
    $stmtExist = $conn->prepare($checkExistSql);
    $stmtExist->bind_param("s", $maChuyenKhoa);
    $stmtExist->execute();
    $existResult = $stmtExist->get_result();
    
    if ($existResult->fetch_assoc()['count'] == 0) {
        throw new Exception('Không tìm thấy chuyên khoa để xóa!');
    }
    $stmtExist->close();
    
    // 2. Đếm số bác sĩ thuộc chuyên khoa này
    $checkDoctorSql = "SELECT COUNT(*) as count FROM bacsi WHERE maChuyenKhoa = ?";
    $stmtDoctor = $conn->prepare($checkDoctorSql);
    $stmtDoctor->bind_param("s", $maChuyenKhoa);
    $stmtDoctor->execute();
    $doctorResult = $stmtDoctor->get_result();
    $doctorCount = $doctorResult->fetch_assoc()['count'];
    $stmtDoctor->close();
    
    // 3. CẬP NHẬT: Set NULL cho các bác sĩ thuộc chuyên khoa này
    // (Theo ràng buộc ON DELETE SET NULL trong db.sql, điều này sẽ tự động xảy ra,
    //  nhưng chúng ta làm thủ công để đảm bảo và ghi log)
    if ($doctorCount > 0) {
        $updateDoctorSql = "UPDATE bacsi SET maChuyenKhoa = NULL WHERE maChuyenKhoa = ?";
        $stmtUpdate = $conn->prepare($updateDoctorSql);
        $stmtUpdate->bind_param("s", $maChuyenKhoa);
        
        if (!$stmtUpdate->execute()) {
            throw new Exception('Lỗi khi cập nhật bác sĩ: ' . $stmtUpdate->error);
        }
        $stmtUpdate->close();
    }
    
    // 4. Xóa chuyên khoa
    $deleteSql = "DELETE FROM chuyenkhoa WHERE maChuyenKhoa = ?";
    $stmtDelete = $conn->prepare($deleteSql);
    $stmtDelete->bind_param("s", $maChuyenKhoa);
    
    if (!$stmtDelete->execute()) {
        throw new Exception('Lỗi xóa chuyên khoa: ' . $stmtDelete->error);
    }
    
    if ($conn->affected_rows === 0) {
        throw new Exception('Không tìm thấy chuyên khoa để xóa!');
    }
    $stmtDelete->close();
    
    // Commit transaction
    $conn->commit();
    
    // Thông báo thành công với chi tiết
    $message = 'Xóa chuyên khoa thành công!';
    if ($doctorCount > 0) {
        $message .= " Đã cập nhật $doctorCount bác sĩ về trạng thái 'Chưa có chuyên khoa'.";
    }
    
    echo json_encode([
        'success' => true,
        'message' => $message,
        'affectedDoctors' => $doctorCount
    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}

$conn->close();
?>