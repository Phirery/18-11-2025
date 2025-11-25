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
    
    // Lấy thông tin yêu cầu
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
        // Tạo mật khẩu mặc định (Eden + ngày hiện tại)
        $defaultPassword = 'Eden' . date('dmY');
        $hashedPassword = password_hash($defaultPassword, PASSWORD_DEFAULT);
        
        // Cập nhật mật khẩu
        $stmt = $conn->prepare("
            UPDATE nguoidung 
            SET matKhau = ?, ngayCapNhatMatKhau = NULL 
            WHERE id = ?
        ");
        $stmt->bind_param("si", $hashedPassword, $request['nguoiDungId']);
        $stmt->execute();
        $stmt->close();
        
        // Gửi thông báo cho người dùng
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
        $message = 'Đã cấp lại mật khẩu thành công! Mật khẩu mới: ' . $defaultPassword;
    } else {
        $newStatus = 'Từ chối';
        $message = 'Đã từ chối yêu cầu!';
        
        // Gửi thông báo từ chối cho người dùng
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
    
    // Cập nhật trạng thái yêu cầu
    $stmt = $conn->prepare("
        UPDATE doimatkhau 
        SET trangThai = ?, thoiGianXuLy = NOW(), nguoiXuLy = ?
        WHERE id = ?
    ");
    $stmt->bind_param("sii", $newStatus, $_SESSION['id'], $requestId);
    $stmt->execute();
    $stmt->close();
    
    // Cập nhật thông báo admin (trigger sẽ tự động làm điều này, nhưng để chắc chắn)
    $stmt = $conn->prepare("
        UPDATE thongbaoadmin
        SET trangThai = ?, thoiGianXuLy = NOW()
        WHERE maYeuCau = ?
    ");
    $stmt->bind_param("si", $newStatus, $requestId);
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