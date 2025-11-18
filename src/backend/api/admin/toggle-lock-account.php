<?php
require_once '../../config/cors.php';
require_once '../../core/dp.php';
require_once '../../core/session.php';

require_role('quantri');

$data = json_decode(file_get_contents("php://input"), true);

if (!empty($data['id'])) {
    $id = intval($data['id']);
    $trangThai = !empty($data['lock']) ? 'Khóa' : 'Hoạt Động';

    // Lấy thông tin tài khoản
    $checkQuery = "SELECT id, vaiTro FROM nguoidung WHERE id = ?";
    $stmt = $conn->prepare($checkQuery);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Tài khoản không tồn tại']);
        exit;
    }

    $user = $result->fetch_assoc();

    // Chặn khóa tài khoản quản trị
    if (strtolower($user['vaiTro']) === 'quantri' && $trangThai === 'Khóa') {
        echo json_encode(['success' => false, 'message' => 'Không thể khóa tài khoản quản trị viên']);
        exit;
    }

    // Bắt đầu transaction để an toàn
    $conn->begin_transaction();

    try {
        $updateQuery = "UPDATE nguoidung SET trangThai = ? WHERE id = ?";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param("si", $trangThai, $id);

        if ($stmt->execute()) {
            $conn->commit();
            echo json_encode([
                'success' => true,
                'message' => ($trangThai === 'Khóa' ? 'Khóa' : 'Mở khóa') . ' tài khoản thành công'
            ]);
        } else {
            $conn->rollback();
            echo json_encode(['success' => false, 'message' => 'Không thể cập nhật trạng thái tài khoản']);
        }
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Thiếu ID tài khoản'
    ]);
}
?>
