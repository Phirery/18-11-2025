<?php
// NOTE: Để sử dụng file này, bạn cần cài đặt thư viện TCPDF hoặc FPDF
// Cách cài đặt: composer require tecnickcom/tcpdf
// Hoặc tải TCPDF từ: https://tcpdf.org/

// Tạm thời xuất dạng HTML để xem trước (giống Excel)
// Để xuất PDF thực sự, uncomment code bên dưới

// Kết nối database
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

// Tạo HTML content
$html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; }
        h2 { color: #333; text-align: center; }
        .info { text-align: center; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; font-size: 12px; }
        th { background-color: #4CAF50; color: white; font-weight: bold; }
        tr:nth-child(even) { background-color: #f2f2f2; }
    </style>
</head>
<body>';

$html .= '<h2>BÁO CÁO THỐNG KÊ LỊCH KHÁM</h2>';
$html .= '<div class="info">';
$html .= '<p><strong>Ngày xuất:</strong> ' . date('d/m/Y H:i:s') . '</p>';
$html .= '<p><strong>Khoảng thời gian:</strong> ' . getFilterText($filter, $dateFrom, $dateTo) . '</p>';
$html .= '</div>';

$html .= '<table>';
$html .= '<thead><tr>';
$html .= '<th>STT</th>';
$html .= '<th>Mã lịch khám</th>';
$html .= '<th>Ngày khám</th>';
$html .= '<th>Bệnh nhân</th>';
$html .= '<th>Bác sĩ</th>';
$html .= '<th>Chuyên khoa</th>';
$html .= '<th>Trạng thái</th>';
$html .= '</tr></thead>';
$html .= '<tbody>';

$stt = 1;
while($row = $result->fetch_assoc()) {
    $trangThai = [
        'confirmed' => 'Đã xác nhận',
        'pending' => 'Chờ xác nhận',
        'cancelled' => 'Đã hủy'
    ][$row['trangThai']] ?? $row['trangThai'];
    
    $html .= '<tr>';
    $html .= '<td>' . $stt++ . '</td>';
    $html .= '<td>' . $row['maLichKham'] . '</td>';
    $html .= '<td>' . date('d/m/Y', strtotime($row['ngayKham'])) . '</td>';
    $html .= '<td>' . $row['tenBenhNhan'] . '</td>';
    $html .= '<td>' . $row['tenBacSi'] . '</td>';
    $html .= '<td>' . $row['tenChuyenKhoa'] . '</td>';
    $html .= '<td>' . $trangThai . '</td>';
    $html .= '</tr>';
}

$html .= '</tbody></table>';
$html .= '</body></html>';

// OPTION 1: Xuất dạng HTML (tạm thời)
header('Content-Type: text/html; charset=utf-8');
echo $html;

/* OPTION 2: Xuất PDF thực sự (cần cài đặt TCPDF)
require_once('tcpdf/tcpdf.php');

$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

$pdf->SetCreator('VietLife Admin');
$pdf->SetAuthor('VietLife Hospital');
$pdf->SetTitle('Báo cáo thống kê');

$pdf->AddPage();
$pdf->writeHTML($html, true, false, true, false, '');

$pdf->Output('bao-cao-thong-ke-' . date('Y-m-d') . '.pdf', 'D');
*/

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