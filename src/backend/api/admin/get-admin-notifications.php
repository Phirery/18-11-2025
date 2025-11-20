<?php
require_once '../../config/cors.php';
require_once '../../core/dp.php';
require_once '../../core/session.php';

require_role('quantri');

try {
    $sql = "SELECT 
            t.maThongBao, 
            t.maNghi, 
            t.requestId,
            t.loai, 
            t.tieuDe, 
            t.noiDung, 
            t.thoiGian, 
            t.daXem,
            t.soDienThoai,
            t.trangThai as trangThaiThongBao,
            t.thoiGianXuLy,
            bs.tenBacSi, 
            n.ngayNghi, 
            c.tenCa,
            d.trangThai,
            d.thoiGianYeuCau,
            d.thoiGianXuLy as requestThoiGianXuLy,
            d.nguoiDungId
            FROM thongbaoadmin t
            LEFT JOIN bacsi bs ON t.maBacSi = bs.maBacSi
            LEFT JOIN ngaynghi n ON t.maNghi = n.maNghi
            LEFT JOIN calamviec c ON n.maCa = c.maCa
            LEFT JOIN doimatkhau d ON t.requestId = d.id
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
            'tenBacSi' => $row['tenBacSi'],
            'ngayNghi' => $row['ngayNghi'] ? date('d/m/Y', strtotime($row['ngayNghi'])) : null,
            'tenCa' => $row['tenCa']
        ];
        
        // Thêm thông tin cho yêu cầu cấp lại mật khẩu
        if ($row['loai'] === 'Cấp lại mật khẩu' && $row['requestId']) {
            $notification['requestId'] = $row['requestId'];
            $notification['soDienThoai'] = $row['soDienThoai'];
            $notification['trangThai'] = $row['trangThaiThongBao'] ?? $row['trangThai'];
            $notification['thoiGianXuLy'] = $row['thoiGianXuLy'] ?? $row['requestThoiGianXuLy'];
            $notification['nguoiDungId'] = $row['nguoiDungId'];
        }
        
        $notifications[] = $notification;
    }

    echo json_encode(['success' => true, 'data' => $notifications]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
}

$conn->close();
?>