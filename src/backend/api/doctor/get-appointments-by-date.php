<?php
require_once '../../config/cors.php';
require_once '../../core/dp.php';
require_once '../../core/session.php';

require_role('bacsi');

$nguoiDungId = $_SESSION['id'];

$date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');

try {
    $stmt_bs = $conn->prepare("SELECT maBacSi FROM bacsi WHERE nguoiDungId = ?");
    $stmt_bs->bind_param("i", $nguoiDungId);
    $stmt_bs->execute();
    $result_bs = $stmt_bs->get_result();
    
    if ($result_bs->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Không tìm thấy thông tin bác sĩ liên kết.']);
        exit;
    }
    
    $bacsi = $result_bs->fetch_assoc();
    $maBacSi = $bacsi['maBacSi'];
    $stmt_bs->close();

    $stmt = $conn->prepare("
        SELECT 
            lk.maLichKham, lk.ngayKham, lk.trangThai,
            bn.tenBenhNhan, bn.ngaySinh, bn.gioiTinh,
            ca.tenCa, ca.maCa,
            sk.gioBatDau, sk.gioKetThuc,
            gk.tenGoi
        FROM lichkham lk
        LEFT JOIN benhnhan bn ON lk.maBenhNhan = bn.maBenhNhan
        LEFT JOIN calamviec ca ON lk.maCa = ca.maCa
        LEFT JOIN suatkham sk ON lk.maSuat = sk.maSuat
        LEFT JOIN goikham gk ON lk.maGoi = gk.maGoi
        WHERE lk.maBacSi = ? AND lk.ngayKham = ? AND lk.trangThai IN ('Chờ', 'Đã đặt')
        ORDER BY ca.maCa, sk.gioBatDau
    ");
    
    $stmt->bind_param("ss", $maBacSi, $date);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $appointments = [];
    while($row = $result->fetch_assoc()) {
        $appointments[] = $row;
    }
    
    echo json_encode(['success' => true, 'data' => $appointments]);
    $stmt->close();

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Lỗi máy chủ: ' . $e->getMessage()]);
}

$conn->close();
?>