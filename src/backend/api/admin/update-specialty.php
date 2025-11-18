<?php
require_once '../../config/cors.php';
require_once '../../core/dp.php';
require_once '../../core/session.php';

require_role('quantri');

$conn->begin_transaction();

try {
    $data = json_decode(file_get_contents('php://input'), true);

    if (empty($data['maChuyenKhoa']) || empty($data['tenChuyenKhoa'])) {
        throw new Exception('Vui lòng cung cấp đủ Mã chuyên khoa và Tên chuyên khoa.');
    }
    
    $maChuyenKhoa = $conn->real_escape_string($data['maChuyenKhoa']);
    $tenChuyenKhoa = $conn->real_escape_string($data['tenChuyenKhoa']);
    $maKhoa = $conn->real_escape_string($data['maKhoa']);
    $oldMaKhoa = isset($data['oldMaKhoa']) ? $conn->real_escape_string($data['oldMaKhoa']) : '';
    $moTa = isset($data['moTa']) ? $conn->real_escape_string($data['moTa']) : '';
    
    $checkSql = "SELECT COUNT(*) as count FROM chuyenkhoa WHERE maChuyenKhoa = ?";
    $stmtCheck = $conn->prepare($checkSql);
    $stmtCheck->bind_param("s", $maChuyenKhoa);
    $stmtCheck->execute();
    $checkResult = $stmtCheck->get_result();
    $count = $checkResult->fetch_assoc()['count'];
    $stmtCheck->close();
    
    if ($count == 0) {
        throw new Exception('Không tìm thấy chuyên khoa để cập nhật!');
    }
    
    if ($oldMaKhoa && $oldMaKhoa !== $maKhoa) {
        $oldPrefix = substr($maChuyenKhoa, 0, strlen($oldMaKhoa));
        
        if ($oldPrefix === $oldMaKhoa) {
            $countSql = "SELECT COUNT(*) as count FROM chuyenkhoa WHERE maKhoa = ? FOR UPDATE";
            $stmtCount = $conn->prepare($countSql);
            $stmtCount->bind_param("s", $maKhoa);
            $stmtCount->execute();
            $result = $stmtCount->get_result();
            $count = $result->fetch_assoc()['count'];
            $stmtCount->close();
            
            $nextSequentialNumber = $count + 1;
            $suffix = str_pad($nextSequentialNumber, 2, '0', STR_PAD_LEFT);
            $newMaChuyenKhoa = $maKhoa . $suffix;
            
            $updateCodeSql = "UPDATE chuyenkhoa 
                             SET maChuyenKhoa = ?,
                                 tenChuyenKhoa = ?,
                                 maKhoa = ?,
                                 moTa = ?
                             WHERE maChuyenKhoa = ?";
            $stmtUpdateCode = $conn->prepare($updateCodeSql);
            $stmtUpdateCode->bind_param("sssss", $newMaChuyenKhoa, $tenChuyenKhoa, $maKhoa, $moTa, $maChuyenKhoa);
            
            if (!$stmtUpdateCode->execute()) {
                throw new Exception('Lỗi khi cập nhật chuyên khoa: ' . $stmtUpdateCode->error);
            }
            $stmtUpdateCode->close();
            
            $updateDoctorsSql = "UPDATE bacsi SET maChuyenKhoa = ? WHERE maChuyenKhoa = ?";
            $stmtUpdateDoctors = $conn->prepare($updateDoctorsSql);
            $stmtUpdateDoctors->bind_param("ss", $newMaChuyenKhoa, $maChuyenKhoa);
            $stmtUpdateDoctors->execute();
            $stmtUpdateDoctors->close();
            
            $maChuyenKhoa = $newMaChuyenKhoa;
        } else {
            $sql = "UPDATE chuyenkhoa 
                   SET tenChuyenKhoa = ?,
                       maKhoa = ?,
                       moTa = ?
                   WHERE maChuyenKhoa = ?";
            
            $stmtUpdate = $conn->prepare($sql);
            $stmtUpdate->bind_param("ssss", $tenChuyenKhoa, $maKhoa, $moTa, $maChuyenKhoa);
            
            if (!$stmtUpdate->execute()) {
                throw new Exception('Lỗi khi cập nhật chuyên khoa: ' . $stmtUpdate->error);
            }
            $stmtUpdate->close();
        }
    } else {
        $sql = "UPDATE chuyenkhoa 
               SET tenChuyenKhoa = ?,
                   moTa = ?
               WHERE maChuyenKhoa = ?";
        
        $stmtUpdate = $conn->prepare($sql);
        $stmtUpdate->bind_param("sss", $tenChuyenKhoa, $moTa, $maChuyenKhoa);
        
        if ($stmtUpdate->execute() === TRUE) {
            if ($conn->affected_rows > 0) {
                $conn->commit();
                echo json_encode([
                    'success' => true,
                    'message' => 'Cập nhật chuyên khoa thành công!'
                ], JSON_UNESCAPED_UNICODE);
            } else {
                $conn->commit();
                echo json_encode([
                    'success' => true, 
                    'message' => 'Không có thay đổi nào được ghi nhận.'
                ], JSON_UNESCAPED_UNICODE);
            }
        } else {
            throw new Exception('Lỗi khi cập nhật chuyên khoa: ' . $stmtUpdate->error);
        }
        $stmtUpdate->close();
        $conn->close();
        exit;
    }
    
    $conn->commit();
    
    echo json_encode([
        'success' => true,
        'message' => 'Cập nhật chuyên khoa thành công!',
        'newMaChuyenKhoa' => $maChuyenKhoa
    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}

$conn->close();
?>