<?php
require_once '../../config/cors.php';
require_once '../../core/dp.php';
require_once '../../core/session.php';

require_role('quantri');

$input = json_decode(file_get_contents('php://input'), true);
$requestId = $input['requestId'];
$action = $input['action']; // 'approve' or 'reject'

try {
    $conn->begin_transaction();
    
    // Get request info
    $stmt = $conn->prepare("
        SELECT r.nguoiDungId, nd.tenDangNhap, nd.vaiTro
        FROM doimatkhau r
        JOIN nguoidung nd ON r.nguoiDungId = nd.id
        WHERE r.id = ? AND r.trangThai = 'Chờ'
    ");
    $stmt->bind_param("i", $requestId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception('Không tìm thấy yêu cầu hoặc yêu cầu đã được xử lý!');
    }
    
    $request = $result->fetch_assoc();
    $stmt->close();
    
    if ($action === 'approve') {
        // Generate default password (Eden + current date)
        $defaultPassword = 'Eden' . date('dmY');
        $hashedPassword = password_hash($defaultPassword, PASSWORD_DEFAULT);
        
        // Update password
        $stmt = $conn->prepare("
            UPDATE nguoidung 
            SET matKhau = ?, ngayCapNhatMatKhau = NULL 
            WHERE id = ?
        ");
        $stmt->bind_param("si", $hashedPassword, $request['nguoiDungId']);
        $stmt->execute();
        $stmt->close();
        
        // Send notification to user
        if ($request['vaiTro'] === 'benhnhan') {
            $stmt = $conn->prepare("
                INSERT INTO thongbaobenhnhan (maBenhNhan, loai, tieuDe, noiDung)
                SELECT maBenhNhan, 'Mật khẩu', 'Cấp lại mật khẩu', 
                       CONCAT('Mật khẩu mới của bạn là: ', ?, '. Vui lòng đổi mật khẩu sau khi đăng nhập.')
                FROM benhnhan WHERE nguoiDungId = ?
            ");
            $stmt->bind_param("si", $defaultPassword, $request['nguoiDungId']);
            $stmt->execute();
            $stmt->close();
        } elseif ($request['vaiTro'] === 'bacsi') {
            $stmt = $conn->prepare("
                INSERT INTO thongbaolichkham (maBacSi, loai, tieuDe, noiDung)
                SELECT maBacSi, 'Đặt lịch', 'Cấp lại mật khẩu',
                       CONCAT('Mật khẩu mới của bạn là: ', ?, '. Vui lòng đổi mật khẩu sau khi đăng nhập.')
                FROM bacsi WHERE nguoiDungId = ?
            ");
            $stmt->bind_param("si", $defaultPassword, $request['nguoiDungId']);
            $stmt->execute();
            $stmt->close();
        }
        
        $newStatus = 'Đã xử lý';
        $message = 'Đã cấp lại mật khẩu thành công!';
    } else {
        $newStatus = 'Từ chối';
        $message = 'Đã từ chối yêu cầu!';
        
        // Notify user about rejection
        if ($request['vaiTro'] === 'benhnhan') {
            $stmt = $conn->prepare("
                INSERT INTO thongbaobenhnhan (maBenhNhan, loai, tieuDe, noiDung)
                SELECT maBenhNhan, 'Hệ thống', 'Yêu cầu bị từ chối', 
                       'Yêu cầu cấp lại mật khẩu của bạn đã bị từ chối. Vui lòng liên hệ quản trị viên để biết thêm chi tiết.'
                FROM benhnhan WHERE nguoiDungId = ?
            ");
            $stmt->bind_param("i", $request['nguoiDungId']);
            $stmt->execute();
            $stmt->close();
        } elseif ($request['vaiTro'] === 'bacsi') {
            $stmt = $conn->prepare("
                INSERT INTO thongbaolichkham (maBacSi, loai, tieuDe, noiDung)
                SELECT maBacSi, 'Hủy lịch', 'Yêu cầu bị từ chối',
                       'Yêu cầu cấp lại mật khẩu của bạn đã bị từ chối. Vui lòng liên hệ quản trị viên để biết thêm chi tiết.'
                FROM bacsi WHERE nguoiDungId = ?
            ");
            $stmt->bind_param("i", $request['nguoiDungId']);
            $stmt->execute();
            $stmt->close();
        }
    }
    
    // Update request status
    $stmt = $conn->prepare("
        UPDATE doimatkhau 
        SET trangThai = ?, thoiGianXuLy = NOW(), nguoiXuLy = ?
        WHERE id = ?
    ");
    $stmt->bind_param("sii", $newStatus, $_SESSION['id'], $requestId);
    $stmt->execute();
    $stmt->close();
    
    $conn->commit();
    echo json_encode(['success' => true, 'message' => $message]);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
$conn->close();
?>