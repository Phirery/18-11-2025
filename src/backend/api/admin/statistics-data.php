<?php
require_once '../../config/cors.php';
require_once '../../core/dp.php';
require_once '../../core/session.php';

require_role('quantri');

$filter = $_GET['filter'] ?? 'month';
$dateFrom = $_GET['dateFrom'] ?? null;
$dateTo = $_GET['dateTo'] ?? null;

$dateCondition = getDateCondition($filter, $dateFrom, $dateTo);
$previousDateCondition = getPreviousDateCondition($filter, $dateFrom, $dateTo);

$summary = getSummaryData($conn, $dateCondition, $previousDateCondition);
$appointmentsTrend = getAppointmentsTrend($conn, $filter, $dateCondition);
$patientsTrend = getPatientsTrend($conn, $filter, $dateCondition);
$departmentsData = getDepartmentsData($conn, $dateCondition);
$statusData = getStatusData($conn, $dateCondition);
$revenueTrend = getRevenueTrend($conn, $filter, $dateCondition);
$revenueTrendActual = getRevenueTrendActual($conn, $filter, $dateCondition);
$topDoctors = getTopDoctors($conn, $dateCondition);

echo json_encode([
    'success' => true,
    'data' => [
        'summary' => $summary,
        'appointmentsTrend' => $appointmentsTrend,
        'patientsTrend' => $patientsTrend,
        'departmentsData' => $departmentsData,
        'statusData' => $statusData,
        'revenueTrend' => $revenueTrend,
        'revenueTrendActual' => $revenueTrendActual,
        'topDoctors' => $topDoctors
    ]
], JSON_UNESCAPED_UNICODE);

$conn->close();

function getDateCondition($filter, $dateFrom, $dateTo) {
    switch($filter) {
        case 'week':
            return "ngayKham >= '" . date('Y-m-d', strtotime('monday this week')) . "'";
        case 'month':
            return "ngayKham >= '" . date('Y-m-01') . "'";
        case 'year':
            return "ngayKham >= '" . date('Y-01-01') . "'";
        case 'custom':
            return $dateFrom && $dateTo ? "ngayKham BETWEEN '$dateFrom' AND '$dateTo'" : "1=1";
        default:
            return "1=1";
    }
}

function getPreviousDateCondition($filter, $dateFrom, $dateTo) {
    switch($filter) {
        case 'week':
            $start = date('Y-m-d', strtotime('monday last week'));
            $end = date('Y-m-d', strtotime('sunday last week'));
            return "ngayKham BETWEEN '$start' AND '$end'";
        case 'month':
            $start = date('Y-m-01', strtotime('first day of last month'));
            $end = date('Y-m-t', strtotime('last day of last month'));
            return "ngayKham BETWEEN '$start' AND '$end'";
        case 'year':
            return "YEAR(ngayKham) = " . (date('Y') - 1);
        default:
            return "1=1";
    }
}

function getSummaryData($conn, $dateCondition, $previousDateCondition) {
    $result = $conn->query("SELECT COUNT(*) as count FROM lichkham WHERE $dateCondition");
    $currentAppointments = $result->fetch_assoc()['count'];
    
    $result = $conn->query("SELECT COUNT(*) as count FROM lichkham WHERE $previousDateCondition");
    $previousAppointments = $result->fetch_assoc()['count'];
    
    $appointmentChange = $previousAppointments > 0 
        ? (($currentAppointments - $previousAppointments) / $previousAppointments) * 100 : 0;
    
    $result = $conn->query("SELECT COUNT(*) as count FROM benhnhan");
    $totalPatients = $result->fetch_assoc()['count'];
    
    $result = $conn->query("SELECT COUNT(*) as count FROM bacsi");
    $totalDoctors = $result->fetch_assoc()['count'];
    
    $sql = "SELECT COALESCE(SUM(gk.gia), 0) as total 
            FROM lichkham lk 
            LEFT JOIN goikham gk ON lk.maGoi = gk.maGoi 
            WHERE lk.trangThai = 'Hoàn thành' AND $dateCondition";
    $result = $conn->query($sql);
    $revenueActual = (int)$result->fetch_assoc()['total'];
    
    $sql = "SELECT COALESCE(SUM(gk.gia), 0) as total 
            FROM lichkham lk 
            LEFT JOIN goikham gk ON lk.maGoi = gk.maGoi 
            WHERE lk.trangThai != 'Hủy' AND $dateCondition";
    $result = $conn->query($sql);
    $revenueEstimated = (int)$result->fetch_assoc()['total'];
    
    $sql = "SELECT COALESCE(SUM(gk.gia), 0) as total 
            FROM lichkham lk 
            LEFT JOIN goikham gk ON lk.maGoi = gk.maGoi 
            WHERE lk.trangThai != 'Hủy' AND $previousDateCondition";
    $result = $conn->query($sql);
    $previousRevenue = $result->fetch_assoc()['total'];
    
    $revenueChange = $previousRevenue > 0 
        ? (($revenueEstimated - $previousRevenue) / $previousRevenue) * 100 : 0;
    
    return [
        'appointments' => $currentAppointments,
        'patients' => $totalPatients,
        'doctors' => $totalDoctors,
        'revenueActual' => $revenueActual,
        'revenueEstimated' => $revenueEstimated,
        'appointmentChange' => round($appointmentChange, 1),
        'patientChange' => 0,
        'revenueChange' => round($revenueChange, 1)
    ];
}

function getAppointmentsTrend($conn, $filter, $dateCondition) {
    $labels = [];
    $values = [];
    
    if ($filter == 'week') {
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $dayNames = ['Monday'=>'T2','Tuesday'=>'T3','Wednesday'=>'T4','Thursday'=>'T5','Friday'=>'T6','Saturday'=>'T7','Sunday'=>'CN'];
            $labels[] = $dayNames[date('l', strtotime($date))];
            $result = $conn->query("SELECT COUNT(*) as count FROM lichkham WHERE ngayKham = '$date'");
            $values[] = (int)$result->fetch_assoc()['count'];
        }
    } elseif ($filter == 'month') {
        for ($i = 3; $i >= 0; $i--) {
            $labels[] = "Tuần " . (4 - $i);
            $startDate = date('Y-m-d', strtotime("-" . ($i * 7) . " days"));
            $endDate = date('Y-m-d', strtotime("-" . ($i * 7 - 6) . " days"));
            $result = $conn->query("SELECT COUNT(*) as count FROM lichkham WHERE ngayKham BETWEEN '$endDate' AND '$startDate'");
            $values[] = (int)$result->fetch_assoc()['count'];
        }
    } elseif ($filter == 'year') {
        for ($i = 1; $i <= 12; $i++) {
            $labels[] = "T" . $i;
            $result = $conn->query("SELECT COUNT(*) as count FROM lichkham WHERE YEAR(ngayKham) = ".date('Y')." AND MONTH(ngayKham) = $i");
            $values[] = (int)$result->fetch_assoc()['count'];
        }
    } else {
        $result = $conn->query("SELECT YEAR(ngayKham) as year, COUNT(*) as count FROM lichkham GROUP BY YEAR(ngayKham) ORDER BY year");
        while($row = $result->fetch_assoc()) {
            $labels[] = "Năm " . $row['year'];
            $values[] = (int)$row['count'];
        }
    }
    
    return ['labels' => $labels, 'values' => $values];
}

function getPatientsTrend($conn, $filter, $dateCondition) {
    $labels = [];
    $values = [];
    
    if ($filter == 'week') {
        $sql = "SELECT DATE(ngayKham) as date, COUNT(DISTINCT maBenhNhan) as count 
                FROM lichkham 
                WHERE ngayKham >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
                GROUP BY DATE(ngayKham)
                ORDER BY date";
        $result = $conn->query($sql);
        $data = [];
        while($row = $result->fetch_assoc()) {
            $data[$row['date']] = $row['count'];
        }
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $dayNames = ['Monday'=>'T2','Tuesday'=>'T3','Wednesday'=>'T4','Thursday'=>'T5','Friday'=>'T6','Saturday'=>'T7','Sunday'=>'CN'];
            $labels[] = $dayNames[date('l', strtotime($date))];
            $values[] = isset($data[$date]) ? (int)$data[$date] : 0;
        }
    } elseif ($filter == 'month') {
        for ($i = 3; $i >= 0; $i--) {
            $labels[] = "Tuần " . (4 - $i);
            $endDate = date('Y-m-d', strtotime("-" . ($i * 7 - 6) . " days"));
            $startDate = date('Y-m-d', strtotime("-" . ($i * 7) . " days"));
            $result = $conn->query("SELECT COUNT(DISTINCT maBenhNhan) as count FROM lichkham WHERE ngayKham BETWEEN '$endDate' AND '$startDate'");
            $values[] = (int)$result->fetch_assoc()['count'];
        }
    } else {
        for ($i = 1; $i <= 12; $i++) {
            $labels[] = "T" . $i;
            $result = $conn->query("SELECT COUNT(DISTINCT maBenhNhan) as count FROM lichkham WHERE YEAR(ngayKham) = ".date('Y')." AND MONTH(ngayKham) = $i");
            $values[] = (int)$result->fetch_assoc()['count'];
        }
    }
    
    return ['labels' => $labels, 'values' => $values];
}

function getDepartmentsData($conn, $dateCondition) {
    $sql = "SELECT k.tenKhoa, COUNT(lk.maLichKham) as count
            FROM khoa k
            LEFT JOIN chuyenkhoa ck ON k.maKhoa = ck.maKhoa
            LEFT JOIN bacsi bs ON ck.maChuyenKhoa = bs.maChuyenKhoa
            LEFT JOIN lichkham lk ON bs.maBacSi = lk.maBacSi AND $dateCondition
            GROUP BY k.maKhoa, k.tenKhoa
            HAVING count > 0
            ORDER BY count DESC
            LIMIT 7";
    
    $result = $conn->query($sql);
    $labels = [];
    $values = [];
    
    while($row = $result->fetch_assoc()) {
        $labels[] = $row['tenKhoa'];
        $values[] = (int)$row['count'];
    }
    
    return ['labels' => $labels, 'values' => $values];
}

function getStatusData($conn, $dateCondition) {
    $sql = "SELECT 
                SUM(CASE WHEN trangThai = 'Đã đặt' THEN 1 ELSE 0 END) as confirmed,
                SUM(CASE WHEN trangThai = 'Chờ' THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN trangThai = 'Hoàn thành' THEN 1 ELSE 0 END) as completed,
                SUM(CASE WHEN trangThai = 'Hủy' THEN 1 ELSE 0 END) as cancelled
            FROM lichkham WHERE $dateCondition";
    
    $result = $conn->query($sql);
    $data = $result->fetch_assoc();
    
    return [
        'confirmed' => (int)$data['confirmed'],
        'pending' => (int)$data['pending'],
        'completed' => (int)$data['completed'],
        'cancelled' => (int)$data['cancelled']
    ];
}

function getRevenueTrend($conn, $filter, $dateCondition) {
    $appointmentsTrend = getAppointmentsTrend($conn, $filter, $dateCondition);
    $values = array_map(function($count) { return $count * 150000; }, $appointmentsTrend['values']);
    return ['labels' => $appointmentsTrend['labels'], 'values' => $values];
}

function getRevenueTrendActual($conn, $filter, $dateCondition) {
    $labels = [];
    $values = [];
    
    if ($filter == 'week') {
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $dayNames = ['Monday'=>'T2','Tuesday'=>'T3','Wednesday'=>'T4','Thursday'=>'T5','Friday'=>'T6','Saturday'=>'T7','Sunday'=>'CN'];
            $labels[] = $dayNames[date('l', strtotime($date))];
            $sql = "SELECT COALESCE(SUM(gk.gia), 0) as total 
                    FROM lichkham lk 
                    LEFT JOIN goikham gk ON lk.maGoi = gk.maGoi 
                    WHERE lk.trangThai = 'Hoàn thành' AND lk.ngayKham = '$date'";
            $result = $conn->query($sql);
            $values[] = (int)$result->fetch_assoc()['total'];
        }
    } elseif ($filter == 'month') {
        for ($i = 3; $i >= 0; $i--) {
            $labels[] = "Tuần " . (4 - $i);
            $startDate = date('Y-m-d', strtotime("-" . ($i * 7) . " days"));
            $endDate = date('Y-m-d', strtotime("-" . ($i * 7 - 6) . " days"));
            $sql = "SELECT COALESCE(SUM(gk.gia), 0) as total 
                    FROM lichkham lk 
                    LEFT JOIN goikham gk ON lk.maGoi = gk.maGoi 
                    WHERE lk.trangThai = 'Hoàn thành' AND lk.ngayKham BETWEEN '$endDate' AND '$startDate'";
            $result = $conn->query($sql);
            $values[] = (int)$result->fetch_assoc()['total'];
        }
    } else {
        for ($i = 1; $i <= 12; $i++) {
            $labels[] = "T" . $i;
            $sql = "SELECT COALESCE(SUM(gk.gia), 0) as total 
                    FROM lichkham lk 
                    LEFT JOIN goikham gk ON lk.maGoi = gk.maGoi 
                    WHERE lk.trangThai = 'Hoàn thành' AND YEAR(lk.ngayKham) = ".date('Y')." AND MONTH(lk.ngayKham) = $i";
            $result = $conn->query($sql);
            $values[] = (int)$result->fetch_assoc()['total'];
        }
    }
    
    return ['labels' => $labels, 'values' => $values];
}

function getTopDoctors($conn, $dateCondition) {
    $sql = "SELECT 
                bs.maBacSi, bs.tenBacSi, ck.tenChuyenKhoa,
                COUNT(lk.maLichKham) as total,
                SUM(CASE WHEN lk.trangThai = 'Hoàn thành' THEN 1 ELSE 0 END) as completed
            FROM bacsi bs
            LEFT JOIN chuyenkhoa ck ON bs.maChuyenKhoa = ck.maChuyenKhoa
            LEFT JOIN lichkham lk ON bs.maBacSi = lk.maBacSi AND $dateCondition
            GROUP BY bs.maBacSi, bs.tenBacSi, ck.tenChuyenKhoa
            HAVING total > 0
            ORDER BY total DESC
            LIMIT 10";
    
    $result = $conn->query($sql);
    $doctors = [];
    while($row = $result->fetch_assoc()) {
        $doctors[] = [
            'maBacSi' => $row['maBacSi'],
            'tenBacSi' => $row['tenBacSi'],
            'tenChuyenKhoa' => $row['tenChuyenKhoa'],
            'total' => (int)$row['total'],
            'completed' => (int)$row['completed']
        ];
    }
    return $doctors;
}
?>