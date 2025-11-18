<?php
require_once '../../config/cors.php';
require_once '../../core/dp.php';
require_once '../../core/session.php';

require_role('quantri');

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $maBenhNhan = $conn->real_escape_string($data['maBenhNhan'] ?? '');

    if (empty($maBenhNhan)) {
        throw new Exception('Mã bệnh nhân là bắt buộc!');
    }

    // Lấy nguoiDungId
    $getIdSql = "SELECT nguoiDungId FROM benhnhan WHERE maBenhNhan = '$maBenhNhan'";
    $result = $conn->query($getIdSql);
    if ($result->num_rows === 0) {
        throw new Exception('Không tìm thấy bệnh nhân!');
    }
    $nguoiDungId = $result->fetch_assoc()['nguoiDungId'];

    // Xóa benhnhan (FK cascade sẽ xóa hosobenhan, lichkham nếu có)
    $deletePatientSql = "DELETE FROM benhnhan WHERE maBenhNhan = '$maBenhNhan'";
    if (!$conn->query($deletePatientSql)) {
        throw new Exception('Lỗi xóa bệnh nhân: ' . $conn->error);
    }

    // Xóa nguoidung
    $deleteUserSql = "DELETE FROM nguoidung WHERE id = $nguoiDungId";
    if (!$conn->query($deleteUserSql)) {
        throw new Exception('Lỗi xóa tài khoản: ' . $conn->error);
    }

    echo json_encode([
        'success' => true,
        'message' => 'Xóa bệnh nhân thành công!'
    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}

$conn->close();
?>