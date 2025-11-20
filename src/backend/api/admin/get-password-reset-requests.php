<?php
require_once '../../config/cors.php';
require_once '../../core/dp.php';
require_once '../../core/session.php';

require_role('quantri');

try {
    $sql = "SELECT 
                r.id, r.nguoiDungId, r.trangThai, r.thoiGianYeuCau, r.thoiGianXuLy,
                nd.tenDangNhap, nd.vaiTro, nd.soDienThoai,
                CASE 
                    WHEN nd.vaiTro = 'benhnhan' THEN bn.tenBenhNhan
                    WHEN nd.vaiTro = 'bacsi' THEN bs.tenBacSi
                    ELSE nd.tenDangNhap
                END as hoTen,
                ad.tenDangNhap as nguoiXuLyName
            FROM doimatkhau r
            JOIN nguoidung nd ON r.nguoiDungId = nd.id
            LEFT JOIN benhnhan bn ON nd.id = bn.nguoiDungId
            LEFT JOIN bacsi bs ON nd.id = bs.nguoiDungId
            LEFT JOIN nguoidung ad ON r.nguoiXuLy = ad.id
            ORDER BY 
                CASE r.trangThai 
                    WHEN 'Chờ' THEN 1 
                    WHEN 'Đã xử lý' THEN 2 
                    ELSE 3 
                END,
                r.thoiGianYeuCau DESC";
    
    $result = $conn->query($sql);
    $requests = [];
    
    while ($row = $result->fetch_assoc()) {
        $requests[] = $row;
    }
    
    echo json_encode(['success' => true, 'data' => $requests]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
}
$conn->close();
?>