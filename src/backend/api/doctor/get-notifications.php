<?php
require_once '../../config/cors.php';
require_once '../../core/dp.php';
require_once '../../core/session.php';

require_role('bacsi');

try {
    $stmt = $conn->prepare("SELECT maBacSi FROM bacsi WHERE nguoiDungId = ?");
    $stmt->bind_param("i", $_SESSION['id']);
    $stmt->execute();
    $maBacSi = $stmt->get_result()->fetch_assoc()['maBacSi'] ?? null;
    $stmt->close();

    if (!$maBacSi) {
        echo json_encode(['success' => false, 'message' => 'Không tìm thấy bác sĩ']);
        exit;
    }

    // Query để lấy tất cả thông báo bao gồm cả thông báo cấp lại mật khẩu
    $sql = "SELECT 
            t.maThongBao, 
            t.loai, 
            t.tieuDe, 
            t.noiDung, 
            t.thoiGian, 
            t.daXem,
            t.maLichKham,
            bn.tenBenhNhan, 
            l.ngayKham, 
            c.tenCa
            FROM thongbaolichkham t
            LEFT JOIN lichkham l ON t.maLichKham = l.maLichKham
            LEFT JOIN benhnhan bn ON l.maBenhNhan = bn.maBenhNhan
            LEFT JOIN calamviec c ON l.maCa = c.maCa
            WHERE t.maBacSi = ?
            ORDER BY t.thoiGian DESC";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $maBacSi);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $notifications = [];
    while ($row = $result->fetch_assoc()) {
        // Convert daXem to boolean
        $row['daXem'] = (bool)$row['daXem'];
        
        // Đảm bảo loại thông báo được set đúng
        // Nếu không có maLichKham và tiêu đề chứa "mật khẩu", đây là thông báo cấp lại mật khẩu
        if (!$row['maLichKham'] && 
            (stripos($row['tieuDe'], 'mật khẩu') !== false || 
             stripos($row['noiDung'], 'mật khẩu') !== false)) {
            $row['loai'] = 'Cấp lại mật khẩu';
        }
        
        $notifications[] = $row;
    }
    $stmt->close();

    echo json_encode(['success' => true, 'data' => $notifications]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
}

$conn->close();
?>