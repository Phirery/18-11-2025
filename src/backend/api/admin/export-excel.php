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

// Set headers cho Excel
header('Content-Type: application/vnd.ms-excel; charset=utf-8');
header('Content-Disposition: attachment; filename="bao-cao-thong-ke-' . date('Y-m-d') . '.xls"');
header('Pragma: no-cache');
header('Expires: 0');

// Xuất BOM cho UTF-8
echo "\xEF\xBB\xBF";

// Tạo nội dung Excel (HTML table)
echo '<html xmlns:x="urn:schemas-microsoft-com:office:excel">';
echo '<head>';
echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">';
echo '<style>';
echo 'table { border-collapse: collapse; width: 100%; }';
echo 'th, td { border: 1px solid black; padding: 8px; text-align: left; }';
echo 'th { background-color: #4CAF50; color: white; font-weight: bold; }';
echo '</style>';
echo '</head>';
echo '<body>';

echo '<h2>BÁO CÁO THỐNG KÊ LỊCH KHÁM</h2>';
echo '<p>Ngày xuất: ' . date('d/m/Y H:i:s') . '</p>';
echo '<p>Khoảng thời gian: ' . getFilterText($filter, $dateFrom, $dateTo) . '</p>';
echo '<br>';

echo '<table>';
echo '<thead>';
echo '<tr>';
echo '<th>STT</th>';
echo '<th>Mã lịch khám</th>';
echo '<th>Ngày khám</th>';
echo '<th>Bệnh nhân</th>';
echo '<th>Số điện thoại</th>';
echo '<th>Bác sĩ</th>';
echo '<th>Chuyên khoa</th>';
echo '<th>Khoa</th>';
echo '<th>Trạng thái</th>';
echo '</tr>';
echo '</thead>';
echo '<tbody>';

$stt = 1;
while($row = $result->fetch_assoc()) {
    $trangThai = [
        'confirmed' => 'Đã xác nhận',
        'pending' => 'Chờ xác nhận',
        'cancelled' => 'Đã hủy'
    ][$row['trangThai']] ?? $row['trangThai'];
    
    echo '<tr>';
    echo '<td>' . $stt++ . '</td>';
    echo '<td>' . $row['maLichKham'] . '</td>';
    echo '<td>' . date('d/m/Y', strtotime($row['ngayKham'])) . '</td>';
    echo '<td>' . $row['tenBenhNhan'] . '</td>';
    echo '<td>' . $row['sdtBenhNhan'] . '</td>';
    echo '<td>' . $row['tenBacSi'] . '</td>';
    echo '<td>' . $row['tenChuyenKhoa'] . '</td>';
    echo '<td>' . $row['tenKhoa'] . '</td>';
    echo '<td>' . $trangThai . '</td>';
    echo '</tr>';
}

echo '</tbody>';
echo '</table>';

echo '</body>';
echo '</html>';

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