<?php
// get-admin-notifications.php
require_once '../../config/cors.php';
require_once '../../core/dp.php';
require_once '../../core/session.php';

require_role('quantri');

try {
    // Query mới: Lấy thông tin người gửi từ bảng nguoidung và các bảng liên quan
    $sql = "SELECT 
            t.maThongBao, 
            t.maNghi, 
            t.maYeuCau, -- Đã đổi tên từ requestId
            t.loai, 
            t.tieuDe, 
            t.noiDung, 
            t.thoiGian, 
            t.daXem,
            t.soDienThoai,
            t.trangThai as trangThaiThongBao,
            t.thoiGianXuLy,
            t.nguoiDungId,
            
            -- Lấy tên hiển thị (Ưu tiên: Bác sĩ > Bệnh nhân > Tên đăng nhập)
            COALESCE(bs.tenBacSi, bn.tenBenhNhan, nd.tenDangNhap) as tenNguoiGui,
            nd.vaiTro as vaiTroNguoiGui,
            
            -- Thông tin nghỉ phép
            n.ngayNghi, 
            c.tenCa,
            
            -- Thông tin yêu cầu mật khẩu
            d.trangThai as trangThaiYeuCau,
            d.thoiGianYeuCau,
            d.thoiGianXuLy as requestThoiGianXuLy,
            d.nguoiDungId as resetUserId
            
            FROM thongbaoadmin t
            JOIN nguoidung nd ON t.nguoiDungId = nd.id
            LEFT JOIN bacsi bs ON t.nguoiDungId = bs.nguoiDungId
            LEFT JOIN benhnhan bn ON t.nguoiDungId = bn.nguoiDungId
            
            LEFT JOIN ngaynghi n ON t.maNghi = n.maNghi
            LEFT JOIN calamviec c ON n.maCa = c.maCa
            LEFT JOIN doimatkhau d ON t.maYeuCau = d.id -- Đã đổi tên
            
            ORDER BY t.thoiGian DESC";
    
    $result = $conn->query($sql);
    
    $notifications = [];
    while ($row = $result->fetch_assoc()) {
        $notification = [
            'maThongBao' => $row['maThongBao'],
            'maNghi' => $row['maNghi'],
            'loai' => $row['loai'],
            'tieuDe' => $row['tieuDe'],
            'noiDung' => $row['noiDung'],
            'thoiGian' => $row['thoiGian'],
            'daXem' => (bool)$row['daXem'],
            'tenNguoiGui' => $row['tenNguoiGui'], // Tên hiển thị chung
            'vaiTro' => $row['vaiTroNguoiGui'],
            'ngayNghi' => $row['ngayNghi'] ? date('d/m/Y', strtotime($row['ngayNghi'])) : null,
            'tenCa' => $row['tenCa']
        ];
        
        if ($row['loai'] === 'Cấp lại mật khẩu' && $row['maYeuCau']) {
            $notification['maYeuCau'] = $row['maYeuCau'];
            $notification['soDienThoai'] = $row['soDienThoai'];
            $notification['trangThai'] = $row['trangThaiThongBao'] ?? $row['trangThaiYeuCau'];
            $notification['thoiGianXuLy'] = $row['thoiGianXuLy'] ?? $row['requestThoiGianXuLy'];
            $notification['resetUserId'] = $row['resetUserId'];
        }
        
        $notifications[] = $notification;
    }

    echo json_encode(['success' => true, 'data' => $notifications]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
}

$conn->close();
?>