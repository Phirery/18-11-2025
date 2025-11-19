<?php
require_once '../../config/cors.php';
require_once '../../core/dp.php';
require_once '../../core/session.php';

$nguoiDungId = $_SESSION['id'];
$vaiTro = $_SESSION['vaiTro'];

try {
    if ($vaiTro === 'benhnhan') {
        $stmt = $conn->prepare("
            SELECT nd.tenDangNhap, nd.soDienThoai, nd.vaiTro,
                   bn.tenBenhNhan as hoTen, bn.ngaySinh, bn.gioiTinh, bn.soTheBHYT,
                   nd.ngayCapNhatTaiKhoan
            FROM nguoidung nd
            JOIN benhnhan bn ON nd.id = bn.nguoiDungId
            WHERE nd.id = ?
        ");
    } elseif ($vaiTro === 'bacsi') {
        $stmt = $conn->prepare("
            SELECT nd.tenDangNhap, nd.soDienThoai, nd.vaiTro,
                   bs.tenBacSi as hoTen, bs.moTa,
                   ck.tenChuyenKhoa, k.tenKhoa,
                   nd.ngayCapNhatTaiKhoan
            FROM nguoidung nd
            JOIN bacsi bs ON nd.id = bs.nguoiDungId
            LEFT JOIN chuyenkhoa ck ON bs.maChuyenKhoa = ck.maChuyenKhoa
            LEFT JOIN khoa k ON ck.maKhoa = k.maKhoa
            WHERE nd.id = ?
        ");
    } else {
        $stmt = $conn->prepare("
            SELECT nd.tenDangNhap, nd.soDienThoai, nd.vaiTro,
                   nd.tenDangNhap as hoTen
            FROM nguoidung nd
            WHERE nd.id = ?
        ");
    }
    
    $stmt->bind_param("i", $nguoiDungId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        echo json_encode(['success' => true, 'data' => $row]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Không tìm thấy thông tin']);
    }
    $stmt->close();
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
}
$conn->close();
?>