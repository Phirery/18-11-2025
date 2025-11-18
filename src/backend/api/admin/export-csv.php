<?php
require_once '../../config/cors.php';
require_once '../../core/dp.php';
require_once '../../core/session.php';

require_role('quantri');

// Lấy tham số
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'month';
$dateFrom = isset($_GET['dateFrom']) ? $_GET['dateFrom'] : null;
$dateTo = isset($_GET['dateTo']) ? $_GET['dateTo'] : null;

// Xác định khoảng thời gian
$dateCondition = getDateCondition($filter, $dateFrom, $dateTo);

// Lấy dữ liệu
$sql = "SELECT 
            lk.maLichKham,
            lk.ngayKham,
            lk.trangThai,
            bn.tenBenhNhan,
            bn.soDienThoai as sdtBenhNhan,
            bs.tenBacSi,
            ck.tenChuyenKhoa,
            k.tenKhoa
        FROM lichkham lk
        LEFT JOIN benhnhan bn ON lk.maBenhNhan = bn.maBenhNhan
        LEFT JOIN bacsi bs ON lk.maBacSi = bs.maBacSi
        LEFT JOIN chuyenkhoa ck ON bs.maChuyenKhoa = ck.maChuyenKhoa
        LEFT JOIN khoa k ON ck.maKhoa = k.maKhoa
        WHERE $dateCondition
        ORDER BY lk.ngayKham DESC";

$result = $conn->query($sql);

// Set headers cho CSV
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="bao-cao-thong-ke-' . date('Y-m-d') . '.csv"');
header('Pragma: no-cache');
header('Expires: 0');

// Xuất BOM cho UTF-8
echo "\xEF\xBB\xBF";

// Mở output stream
$output = fopen('php://output', 'w');

// Tiêu đề
fputcsv($output, ['BÁO CÁO THỐNG KÊ LỊCH KHÁM']);
fputcsv($output, ['Ngày xuất: ' . date('d/m/Y H:i:s')]);
fputcsv($output, ['Khoảng thời gian: ' . getFilterText($filter, $dateFrom, $dateTo)]);
fputcsv($output, []); // Dòng trống

// Header của bảng
fputcsv($output, [
    'STT',
    'Mã lịch khám',
    'Ngày khám',
    'Bệnh nhân',
    'Số điện thoại',
    'Bác sĩ',
    'Chuyên khoa',
    'Khoa',
    'Trạng thái'
]);

// Dữ liệu
$stt = 1;
while($row = $result->fetch_assoc()) {
    $trangThai = [
        'confirmed' => 'Đã xác nhận',
        'pending' => 'Chờ xác nhận',
        'cancelled' => 'Đã hủy'
    ][$row['trangThai']] ?? $row['trangThai'];
    
    fputcsv($output, [
        $stt++,
        $row['maLichKham'],
        date('d/m/Y', strtotime($row['ngayKham'])),
        $row['tenBenhNhan'],
        $row['sdtBenhNhan'],
        $row['tenBacSi'],
        $row['tenChuyenKhoa'],
        $row['tenKhoa'],
        $trangThai
    ]);
}

fclose($output);
$conn->close();

// Helper functions
function getDateCondition($filter, $dateFrom, $dateTo) {
    switch($filter) {
        case 'week':
            $startOfWeek = date('Y-m-d', strtotime('monday this week'));
            return "lk.ngayKham >= '$startOfWeek'";
            
        case 'month':
            $startOfMonth = date('Y-m-01');
            return "lk.ngayKham >= '$startOfMonth'";
            
        case 'year':
            $startOfYear = date('Y-01-01');
            return "lk.ngayKham >= '$startOfYear'";
            
        case 'custom':
            if ($dateFrom && $dateTo) {
                return "lk.ngayKham BETWEEN '$dateFrom' AND '$dateTo'";
            }
            return "1=1";
            
        case 'all':
        default:
            return "1=1";
    }
}

function getFilterText($filter, $dateFrom, $dateTo) {
    switch($filter) {
        case 'week': return 'Tuần này';
        case 'month': return 'Tháng này';
        case 'year': return 'Năm nay';
        case 'custom': return $dateFrom . ' đến ' . $dateTo;
        case 'all': return 'Toàn bộ';
        default: return '';
    }
}
?>