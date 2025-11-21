<?php
require_once '../../config/cors.php';
require_once '../../core/dp.php';
require_once '../../core/session.php';

require_role('quantri');

$data = json_decode(file_get_contents("php://input"), true);
if (
    !empty($data['vaiTro']) &&
    !empty($data['tenDangNhap']) &&
    !empty($data['matKhau']) &&
    !empty($data['soDienThoai']) &&
    !empty($data['hoTen'])
) {
    $conn->begin_transaction();

    try {
        $hashedPassword = password_hash($data['matKhau'], PASSWORD_DEFAULT);

        $stmt = $conn->prepare("
            INSERT INTO nguoidung (tenDangNhap, matKhau, soDienThoai, vaiTro)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->bind_param("ssss", $data['tenDangNhap'], $hashedPassword, $data['soDienThoai'], $data['vaiTro']);
        $stmt->execute();

        $nguoiDungId = $conn->insert_id;

        if ($data['vaiTro'] === 'benhnhan') {
            $result = $conn->query("SELECT COUNT(*) as total FROM benhnhan");
            $count = $result->fetch_assoc()['total'];
            $maBenhNhan = 'BN' . date('YmdHi') . sprintf('%03d', rand(0, 999));

            $stmt = $conn->prepare("
                INSERT INTO benhnhan (nguoiDungId, maBenhNhan, tenBenhNhan, ngaySinh, gioiTinh, soTheBHYT)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmt->bind_param(
                "isssss",
                $nguoiDungId,
                $maBenhNhan,
                $data['hoTen'],
                $data['ngaySinh'],
                $data['gioiTinh'],
                $data['soTheBHYT']
            );
            $stmt->execute();

        } elseif ($data['vaiTro'] === 'bacsi') {
            $result = $conn->query("SELECT COUNT(*) as total FROM bacsi");
            $count = $result->fetch_assoc()['total'];
            $maBacSi = 'BS' . date('YmdHi') . sprintf('%03d', rand(0, 999));

            // Lấy giới tính và năm làm việc từ request
            $gioiTinh = isset($data['gioiTinh']) ? $data['gioiTinh'] : null;
            $namLamViec = isset($data['namLamViec']) ? intval($data['namLamViec']) : null;
            $moTa = isset($data['moTa']) ? $data['moTa'] : null;

            $stmt = $conn->prepare("
                INSERT INTO bacsi (nguoiDungId, maBacSi, tenBacSi, maChuyenKhoa, gioiTinh, namLamViec, moTa)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->bind_param(
                "issssss",
                $nguoiDungId,
                $maBacSi,
                $data['hoTen'],
                $data['maChuyenKhoa'],
                $gioiTinh,
                $namLamViec,
                $moTa
            );
            $stmt->execute();

        } elseif ($data['vaiTro'] === 'quantri') {
            $result = $conn->query("SELECT COUNT(*) as total FROM quantrivien");
            $count = $result->fetch_assoc()['total'];
            $maQuanTriVien = 'ADMIN' . date('YmdHi') . sprintf('%03d', rand(0, 999));

            $stmt = $conn->prepare("
                INSERT INTO quantrivien (nguoiDungId, maQuanTriVien)
                VALUES (?, ?)
            ");
            $stmt->bind_param("is", $nguoiDungId, $maQuanTriVien);
            $stmt->execute();
        }

        $conn->commit();

        echo json_encode([
            'success' => true,
            'message' => 'Tạo tài khoản thành công!'
        ], JSON_UNESCAPED_UNICODE);

    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode([
            'success' => false,
            'message' => 'Lỗi: ' . $e->getMessage()
        ], JSON_UNESCAPED_UNICODE);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Dữ liệu không đầy đủ'
    ], JSON_UNESCAPED_UNICODE);
}
$conn->close();
?>