CREATE DATABASE IF NOT EXISTS `datlichkham` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `datlichkham`;

CREATE TABLE `bacsi` (
  `nguoiDungId` int(11) NOT NULL,
  `maBacSi` varchar(20) NOT NULL,
  `tenBacSi` varchar(100) DEFAULT NULL,
  `maChuyenKhoa` varchar(10) DEFAULT NULL,
  `moTa` text DEFAULT NULL,
  `chuyenGia` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `bacsi` (`nguoiDungId`, `maBacSi`, `tenBacSi`, `maChuyenKhoa`, `moTa`, `chuyenGia`) VALUES
(2, 'bs1', 'Trần Văn BBD', 'EYE102501', '', 0),
(5, 'BS202511090112882', 'Nguyễn Thành C', 'SUR102501', NULL, 0),
(7, 'BS202511090152610', 'Lê Văn D', 'INT102503', '1111111111111', 0),
(13, 'BS202511102320635', 'Nguyễn Z', 'DER102502', NULL, 0);

CREATE TABLE `benhnhan` (
  `nguoiDungId` int(11) NOT NULL,
  `maBenhNhan` varchar(20) NOT NULL,
  `tenBenhNhan` varchar(100) DEFAULT NULL,
  `ngaySinh` date DEFAULT NULL,
  `gioiTinh` enum('nam','nu','khac') DEFAULT NULL,
  `soTheBHYT` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `benhnhan` (`nguoiDungId`, `maBenhNhan`, `tenBenhNhan`, `ngaySinh`, `gioiTinh`, `soTheBHYT`) VALUES
(1, 'bn1', 'Nguyễn Văn A', '2000-01-01', 'nam', NULL),
(8, 'BN202511082304701', 'ABCs', '2005-10-09', 'nam', ''),
(11, 'BN202511101515250', 'AAAAAAAA', '2025-11-09', 'khac', 'BH189318214111'),
(15, 'BN2025111712142915', 'Trần Văn H', '2000-12-31', 'nam', NULL);
DELIMITER $$
CREATE TRIGGER `validate_birthdate_before_insert` BEFORE INSERT ON `benhnhan` FOR EACH ROW BEGIN
    IF NEW.ngaySinh > CURDATE() THEN
        SET NEW.ngaySinh = CURDATE();
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `validate_birthdate_before_update` BEFORE UPDATE ON `benhnhan` FOR EACH ROW BEGIN
    IF NEW.ngaySinh > CURDATE() THEN
        SET NEW.ngaySinh = CURDATE();
    END IF;
END
$$
DELIMITER ;

CREATE TABLE `calamviec` (
  `maCa` int(11) NOT NULL,
  `tenCa` varchar(30) NOT NULL,
  `gioBatDau` time NOT NULL,
  `gioKetThuc` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `calamviec` (`maCa`, `tenCa`, `gioBatDau`, `gioKetThuc`) VALUES
(1, 'Ca sáng', '07:00:00', '11:00:00'),
(2, 'Ca chiều', '13:00:00', '17:00:00');

CREATE TABLE `chuyenkhoa` (
  `maChuyenKhoa` varchar(10) NOT NULL,
  `tenChuyenKhoa` varchar(100) NOT NULL,
  `maKhoa` varchar(10) DEFAULT NULL,
  `moTa` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `chuyenkhoa` (`maChuyenKhoa`, `tenChuyenKhoa`, `maKhoa`, `moTa`) VALUES
('CAR102501', 'Tim Mạch Nội Khoa', 'CAR1025', 'Chẩn đoán và điều trị các bệnh tim bằng thuốc, như tăng huyết áp, rối loạn nhịp tim, suy tim. Khoa chú trọng điều trị lâu dài và phòng ngừa tái phát.'),
('CAR102502', 'Tim Mạch Can Thiệp', 'CAR1025', 'Thực hiện các thủ thuật như nong mạch, đặt stent, chụp mạch vành. Khoa phối hợp chặt chẽ với nội tim mạch để quản lý bệnh nhân sau can thiệp.'),
('DEN102501', 'Nha Khoa Tổng Quát', 'DEN1025', 'Khám, trám, nhổ răng và điều trị sâu răng. Khoa tiếp nhận hầu hết các ca bệnh răng miệng thông thường.'),
('DEN102502', 'Chỉnh Nha', 'DEN1025', 'Điều chỉnh vị trí răng, khớp cắn bằng khí cụ hoặc niềng răng. Khoa giúp cải thiện cả chức năng nhai và thẩm mỹ.'),
('DEN102503', 'Phẫu Thuật Hàm Mặt', 'DEN1025', 'Thực hiện các ca phẫu thuật chấn thương, dị tật và thẩm mỹ vùng mặt. Khoa kết hợp với chuyên khoa tai – mũi – họng khi cần thiết.'),
('DER102501', 'Da Liễu Tổng Quát', 'DER1025', 'Khám và điều trị các bệnh ngoài da như viêm da, mụn, nấm và dị ứng. Khoa áp dụng phác đồ hiện đại để cải thiện tình trạng da.'),
('DER102502', 'Da Liễu Thẩm Mỹ', 'DER1025', 'Cung cấp các dịch vụ chăm sóc và điều trị thẩm mỹ da như laser, trị sẹo, giảm nám. Khoa đảm bảo an toàn và hiệu quả cho từng loại da.'),
('ENT102501', 'Tai Học', 'ENT1025', 'Chẩn đoán và điều trị các bệnh về tai như viêm tai, thủng màng nhĩ, giảm thính lực. Khoa cung cấp dịch vụ đo thính lực và phục hồi thính giác.'),
('ENT102502', 'Mũi Xoang', 'ENT1025', 'Điều trị viêm mũi dị ứng, viêm xoang và polyp mũi. Khoa sử dụng nội soi để chẩn đoán và phẫu thuật chính xác.'),
('ENT102503', 'Họng – Thanh Quản', 'ENT1025', 'Khám và điều trị các bệnh về họng, thanh quản như khàn tiếng, viêm amidan. Khoa cũng thực hiện các phẫu thuật nhỏ như cắt amidan hoặc polyp dây thanh.'),
('EYE102501', 'Khúc Xạ', 'EYE1025', 'Chẩn đoán và điều trị các tật khúc xạ như cận, viễn, loạn thị. Khoa cũng tư vấn sử dụng kính và phẫu thuật laser điều chỉnh thị lực.'),
('EYE102502', 'Mắt Nội Khoa', 'EYE1025', 'Điều trị các bệnh lý như viêm kết mạc, tăng nhãn áp, thoái hóa điểm vàng. Khoa chú trọng phát hiện sớm để ngăn ngừa mất thị lực.'),
('EYE102503', 'Mắt Phẫu Thuật', 'EYE1025', 'Thực hiện các ca phẫu thuật mắt như đục thủy tinh thể, mổ lé và ghép giác mạc. Các bác sĩ được đào tạo chuyên sâu về vi phẫu.'),
('INT102501', 'Nội Hô Hấp', 'INT1025', 'Chuyên khoa Nội hô hấp điều trị các bệnh về đường hô hấp như viêm phổi, hen suyễn, COPD. Khoa cung cấp các dịch vụ chẩn đoán chuyên sâu như đo chức năng hô hấp và nội soi phế quản.'),
('INT102502', 'Nội Tiêu Hóa', 'INT1025', 'Chuyên khoa Nội tiêu hóa tập trung chẩn đoán và điều trị bệnh dạ dày, gan, mật, ruột. Các kỹ thuật nội soi và xét nghiệm sinh hóa thường được sử dụng.'),
('INT102503', 'Nội Thần Kinh', 'INT1025', 'Chuyên khoa Nội thần kinh điều trị các bệnh về não và hệ thần kinh như đột quỵ, động kinh, Parkinson. Khoa phối hợp với chẩn đoán hình ảnh để theo dõi tiến triển bệnh.'),
('INT102504', 'Nội Tiết', 'INT1025', 'Chuyên khoa Nội tiết chuyên điều trị các rối loạn hormone như tiểu đường, cường giáp, béo phì. Khoa thường theo dõi bệnh nhân lâu dài để kiểm soát bệnh mạn tính.'),
('INT102505', 'Nội Thận – Tiết Niệu', 'INT1025', 'Chuyên khoa này điều trị các bệnh lý về thận và đường tiết niệu không cần phẫu thuật. Bao gồm viêm cầu thận, suy thận, sỏi thận giai đoạn nhẹ.'),
('INT102506', 'Nội Tổng Hợp', 'INT1025', 'Chuyên khoa Nội tổng hợp tiếp nhận và điều trị các bệnh thông thường và chưa xác định rõ chuyên khoa. Đây là nơi bệnh nhân được thăm khám tổng quát và phân tuyến điều trị.'),
('OBG102501', 'Sản Khoa', 'OBG1025', 'Theo dõi thai kỳ, đỡ sinh, mổ lấy thai và chăm sóc hậu sản. Khoa đảm bảo an toàn cho cả mẹ và bé trong suốt quá trình mang thai và sinh nở.'),
('OBG102502', 'Phụ Khoa', 'OBG1025', 'Điều trị các bệnh lý phụ khoa như viêm nhiễm, u xơ, rối loạn kinh nguyệt. Ngoài ra còn tư vấn sức khỏe sinh sản cho phụ nữ.'),
('PED102501', 'Nhi Tổng Quát', 'PED1025', 'Khám và điều trị các bệnh thường gặp ở trẻ em như sốt, ho, tiêu chảy, viêm phổi. Khoa đảm bảo theo dõi và tư vấn dinh dưỡng cho trẻ.'),
('PED102502', 'Nhi Sơ Sinh', 'PED1025', 'Chăm sóc và điều trị các bệnh lý ở trẻ sơ sinh, đặc biệt là trẻ sinh non. Khoa có trang thiết bị hỗ trợ hô hấp và nuôi dưỡng đặc biệt.'),
('PED102503', 'Nhi Tim Mạch', 'PED1025', 'Chẩn đoán và điều trị bệnh tim bẩm sinh ở trẻ em. Khoa phối hợp với khoa tim mạch để theo dõi và can thiệp sớm.'),
('SUR102501', 'Ngoại Tổng Hợp', 'SUR1025', 'Chuyên khoa Ngoại tổng hợp thực hiện các phẫu thuật phổ biến như ruột thừa, thoát vị, sỏi mật. Đây là chuyên khoa đa dạng, tiếp nhận nhiều loại bệnh lý khác nhau.'),
('SUR102502', 'Ngoại Chấn Thương Chỉnh Hình', 'SUR1025', 'Điều trị gãy xương, trật khớp và các chấn thương cơ xương khớp. Khoa thường phối hợp với vật lý trị liệu để phục hồi vận động.'),
('SUR102503', 'Ngoại Thần Kinh', 'SUR1025', 'Thực hiện phẫu thuật liên quan đến não, cột sống và tủy sống. Khoa xử lý các ca chấn thương sọ não, u não và thoát vị đĩa đệm.'),
('SUR102504', 'Ngoại Tiết Niệu', 'SUR1025', 'Điều trị bằng phẫu thuật cho các bệnh lý thận, bàng quang và tuyến tiền liệt. Khoa ứng dụng công nghệ nội soi và laser trong điều trị.'),
('SUR102505', 'Ngoại Lồng Ngực – Tim Mạch', 'SUR1025', 'Chuyên về phẫu thuật tim, phổi và mạch máu lớn. Các ca mổ đòi hỏi kỹ thuật cao được thực hiện tại đây.');

CREATE TABLE `doimatkhau` (
  `id` int(11) NOT NULL,
  `nguoiDungId` int(11) NOT NULL,
  `trangThai` enum('Chờ','Đã xử lý','Từ chối') DEFAULT 'Chờ',
  `thoiGianYeuCau` datetime DEFAULT current_timestamp(),
  `thoiGianXuLy` datetime DEFAULT NULL,
  `nguoiXuLy` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `doimatkhau` (`id`, `nguoiDungId`, `trangThai`, `thoiGianYeuCau`, `thoiGianXuLy`, `nguoiXuLy`) VALUES
(1, 2, 'Chờ', '2025-11-19 10:30:56', NULL, NULL);

CREATE TABLE `goikham` (
  `maGoi` int(11) NOT NULL,
  `tenGoi` varchar(100) NOT NULL,
  `moTa` text DEFAULT NULL,
  `thoiLuong` int(11) DEFAULT 40,
  `gia` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `goikham` (`maGoi`, `tenGoi`, `moTa`, `thoiLuong`, `gia`) VALUES
(1, 'Gói khám thường', 'Khám với bác sĩ tổng quát', 40, 150000.00),
(2, 'Gói khám cao cấp', 'Khám với bác sĩ chuyên gia', 40, 250000.00);

CREATE TABLE `hosobenhan` (
  `maHoSo` varchar(20) NOT NULL,
  `maBenhNhan` varchar(20) DEFAULT NULL,
  `maBacSi` varchar(20) DEFAULT NULL,
  `maLichKham` int(11) DEFAULT NULL,
  `chanDoan` text DEFAULT NULL,
  `dieuTri` text DEFAULT NULL,
  `trangThai` enum('Chưa hoàn thành','Đã hoàn thành') DEFAULT 'Chưa hoàn thành',
  `ngayTao` datetime DEFAULT current_timestamp(),
  `ngayHoanThanh` datetime DEFAULT NULL,
  `ghiChu` text DEFAULT NULL,
  `ngayKham` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `hosobenhan` (`maHoSo`, `maBenhNhan`, `maBacSi`, `maLichKham`, `chanDoan`, `dieuTri`, `trangThai`, `ngayTao`, `ngayHoanThanh`, `ghiChu`, `ngayKham`) VALUES
('HS20251118184754834', 'BN2025111712142915', 'bs1', 26, 'Xong', 'Xong', 'Đã hoàn thành', '2025-11-18 18:47:54', '2025-11-18 19:33:24', '', NULL),
('HS20251119083129501', 'BN2025111712142915', 'bs1', 30, '1', '1', 'Đã hoàn thành', '2025-11-19 08:31:29', '2025-11-19 08:31:52', '1', '2025-11-19 00:00:00'),
('HS20251119083135625', 'bn1', 'bs1', 29, '2', '2', 'Đã hoàn thành', '2025-11-19 08:31:35', '2025-11-19 08:31:55', '2', '2025-11-19 00:00:00'),
('HS20251119083141153', 'BN202511082304701', 'bs1', 28, '3', '3', 'Đã hoàn thành', '2025-11-19 08:31:41', '2025-11-19 08:31:57', '3', '2025-11-19 00:00:00'),
('HS20251119083148530', 'BN202511101515250', 'bs1', 27, '4', '4', 'Đã hoàn thành', '2025-11-19 08:31:48', '2025-11-19 08:31:59', '4', '2025-11-19 00:00:00');

CREATE TABLE `khoa` (
  `maKhoa` varchar(10) NOT NULL,
  `tenKhoa` varchar(100) NOT NULL,
  `moTa` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `khoa` (`maKhoa`, `tenKhoa`, `moTa`) VALUES
('CAR1025', 'Khoa Tim mạch', 'Khoa Tim mạch tập trung khám và điều trị các bệnh về tim và mạch máu. Đây là khoa chuyên sâu trong chẩn đoán, theo dõi và phục hồi chức năng tim mạch.'),
('DEN1025', 'Khoa Răng – Hàm – Mặt', 'Khoa này chuyên điều trị các bệnh lý răng miệng, hàm và vùng mặt. Ngoài điều trị, khoa còn thực hiện phẫu thuật thẩm mỹ và chỉnh hình hàm mặt.'),
('DER1025', 'Khoa Da liễu', 'Khoa Da liễu chuyên khám và điều trị các bệnh ngoài da, tóc, móng. Ngoài điều trị bệnh, khoa còn cung cấp dịch vụ thẩm mỹ da như laser, trị sẹo và nám.'),
('ENT1025', 'Khoa Tai – Mũi – Họng', 'Khoa Tai - Mũi - Họng chẩn đoán và điều trị các bệnh lý về tai, mũi, họng và thanh quản. Khoa cũng đảm nhận các ca phẫu thuật nhỏ trong khu vực đầu – cổ.'),
('EYE1025', 'Khoa Mắt', 'Khoa Mắt chịu trách nhiệm khám và điều trị các bệnh về mắt và thị lực. Ngoài ra còn thực hiện các phẫu thuật khúc xạ, đục thủy tinh thể và các tật bẩm sinh.'),
('INT1025', 'Khoa Nội', 'Khoa Nội chuyên khám và điều trị các bệnh lý bên trong cơ thể mà không cần phẫu thuật. Đây là nơi tập trung nhiều chuyên khoa như hô hấp, tiêu hóa, thần kinh và nội tổng hợp.'),
('OBG1025', 'Khoa Sản', 'Khoa Sản theo dõi, chăm sóc và điều trị cho phụ nữ trong thời kỳ mang thai và sau sinh. Ngoài ra, khoa còn hỗ trợ điều trị các bệnh lý phụ khoa thường gặp.'),
('PED1025', 'Khoa Nhi', 'Khoa Nhi chịu trách nhiệm chăm sóc sức khỏe cho trẻ sơ sinh và trẻ em. Khoa chuyên điều trị các bệnh thường gặp ở trẻ nhỏ, từ hô hấp đến tim mạch bẩm sinh.'),
('SUR1025', 'Khoa Ngoại', 'Khoa Ngoại đảm nhiệm các ca phẫu thuật và điều trị bằng can thiệp ngoại khoa. Tại đây tiếp nhận các trường hợp cần mổ từ đơn giản đến phức tạp.');

CREATE TABLE `lichkham` (
  `maLichKham` int(11) NOT NULL,
  `maBacSi` varchar(20) NOT NULL,
  `maBenhNhan` varchar(20) NOT NULL,
  `ngayKham` date NOT NULL,
  `maCa` int(11) NOT NULL,
  `maSuat` int(11) NOT NULL,
  `maGoi` int(11) DEFAULT NULL,
  `trangThai` enum('Chờ','Đã đặt','Hoàn thành','Hủy') DEFAULT 'Đã đặt',
  `ghiChu` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `lichkham` (`maLichKham`, `maBacSi`, `maBenhNhan`, `ngayKham`, `maCa`, `maSuat`, `maGoi`, `trangThai`, `ghiChu`) VALUES
(1, 'BS202511090112882', 'BN202511082304701', '2025-11-09', 1, 1, 1, 'Hoàn thành', 'Sức khỏe bình thường'),
(3, 'BS202511090112882', 'BN202511082304701', '2025-11-10', 1, 1, 2, 'Hoàn thành', NULL),
(8, 'bs1', 'bn1', '2025-11-12', 1, 1, 1, 'Hủy', NULL),
(9, 'BS202511090112882', 'BN202511082304701', '2025-11-12', 2, 8, 1, 'Đã đặt', NULL),
(10, 'BS202511090112882', 'BN202511101515250', '2025-11-13', 1, 1, 2, 'Hoàn thành', NULL),
(11, 'BS202511090112882', 'BN202511082304701', '2025-11-15', 1, 2, 1, 'Hoàn thành', NULL),
(26, 'bs1', 'BN2025111712142915', '2025-11-18', 1, 2, 2, 'Hoàn thành', NULL),
(27, 'bs1', 'BN202511101515250', '2025-11-19', 1, 1, 1, 'Hoàn thành', NULL),
(28, 'bs1', 'BN202511082304701', '2025-11-19', 1, 6, 1, 'Hoàn thành', NULL),
(29, 'bs1', 'bn1', '2025-11-19', 2, 7, 2, 'Hoàn thành', NULL),
(30, 'bs1', 'BN2025111712142915', '2025-11-19', 2, 12, 2, 'Hoàn thành', NULL);
DELIMITER $$
CREATE TRIGGER `after_lichkham_insert` AFTER INSERT ON `lichkham` FOR EACH ROW BEGIN
    DECLARE patientName VARCHAR(100);
    DECLARE appointmentDate VARCHAR(20);
    DECLARE shiftName VARCHAR(50);
    
    SELECT tenBenhNhan INTO patientName 
    FROM benhnhan 
    WHERE maBenhNhan = NEW.maBenhNhan;
    
    SELECT tenCa INTO shiftName 
    FROM calamviec 
    WHERE maCa = NEW.maCa;
    
    SET appointmentDate = DATE_FORMAT(NEW.ngayKham, '%d/%m/%Y');
    
    INSERT INTO thongbaolichkham (maBacSi, maLichKham, loai, tieuDe, noiDung, thoiGian, daXem)
    VALUES (
        NEW.maBacSi,
        NEW.maLichKham,
        'Đặt lịch',
        'Lịch khám mới',
        CONCAT('Bệnh nhân ', patientName, ' đã đặt lịch khám vào ngày ', appointmentDate, ' - ', shiftName),
        NOW(),
        0
    );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_lichkham_update` AFTER UPDATE ON `lichkham` FOR EACH ROW BEGIN
    DECLARE patientName VARCHAR(100);
    DECLARE appointmentDate VARCHAR(20);
    DECLARE shiftName VARCHAR(50);
    
    IF NEW.trangThai = 'Hủy' AND OLD.trangThai != 'Hủy' THEN
        SELECT tenBenhNhan INTO patientName 
        FROM benhnhan 
        WHERE maBenhNhan = NEW.maBenhNhan;
        
        SELECT tenCa INTO shiftName 
        FROM calamviec 
        WHERE maCa = NEW.maCa;
        
        SET appointmentDate = DATE_FORMAT(NEW.ngayKham, '%d/%m/%Y');
        
        INSERT INTO thongbaolichkham (maBacSi, maLichKham, loai, tieuDe, noiDung, thoiGian, daXem)
        VALUES (
            NEW.maBacSi,
            NEW.maLichKham,
            'Hủy lịch',
            'Lịch khám đã hủy',
            CONCAT('Bệnh nhân ', patientName, ' đã hủy lịch khám ngày ', appointmentDate, ' - ', shiftName),
            NOW(),
            0
        );
    END IF;
END
$$
DELIMITER ;

CREATE TABLE `ngaynghi` (
  `maNghi` int(11) NOT NULL,
  `maBacSi` varchar(20) NOT NULL,
  `ngayNghi` date NOT NULL,
  `maCa` int(11) DEFAULT NULL,
  `lyDo` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `ngaynghi` (`maNghi`, `maBacSi`, `ngayNghi`, `maCa`, `lyDo`) VALUES
(1, 'BS202511090112882', '2025-11-16', 1, '0'),
(2, 'BS202511090112882', '2025-11-16', 2, 'thử'),
(3, 'BS202511090112882', '2025-11-17', 1, '0'),
(4, 'BS202511090112882', '2025-11-17', 2, '???'),
(5, 'bs1', '2025-11-19', 1, '0'),
(6, 'bs1', '2025-11-19', 2, '........');
DELIMITER $$
CREATE TRIGGER `after_ngaynghi_delete` AFTER DELETE ON `ngaynghi` FOR EACH ROW BEGIN
    DECLARE doctorName VARCHAR(100);
    DECLARE leaveDate VARCHAR(20);
    DECLARE shiftName VARCHAR(50);
    DECLARE caInfo VARCHAR(100);
    DECLARE otherShiftExists INT DEFAULT 0;
    
    SELECT tenBacSi INTO doctorName 
    FROM bacsi 
    WHERE maBacSi = OLD.maBacSi;
    
    SET leaveDate = DATE_FORMAT(OLD.ngayNghi, '%d/%m/%Y');
    
    SELECT COUNT(*) INTO otherShiftExists
    FROM ngaynghi
    WHERE maBacSi = OLD.maBacSi 
    AND ngayNghi = OLD.ngayNghi
    AND maCa != OLD.maCa;
    
    IF otherShiftExists > 0 THEN
        SET caInfo = ' Một ca';
    ELSE
        IF OLD.maCa IS NOT NULL THEN
            SELECT tenCa INTO shiftName 
            FROM calamviec 
            WHERE maCa = OLD.maCa;
            SET caInfo = CONCAT(' - ', shiftName);
        ELSE
            SET caInfo = ' Cả ngày';
        END IF;
    END IF;
    
    INSERT INTO thongbaoadmin (maNghi, maBacSi, loai, tieuDe, noiDung, thoiGian, daXem)
    VALUES (
        NULL,
        OLD.maBacSi,
        'Hủy nghỉ',
        'Hủy đơn nghỉ phép',
        CONCAT('Bác sĩ ', doctorName, ' đã hủy đơn nghỉ phép ngày ', leaveDate, caInfo),
        NOW(),
        0
    );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_ngaynghi_insert` AFTER INSERT ON `ngaynghi` FOR EACH ROW trigger_body: BEGIN
    DECLARE doctorName VARCHAR(100);
    DECLARE leaveDate VARCHAR(20);
    DECLARE shiftName VARCHAR(50);
    DECLARE caInfo VARCHAR(100);
    DECLARE isFullDay BOOLEAN DEFAULT FALSE;
    DECLARE otherShiftExists INT DEFAULT 0;
    
    SELECT tenBacSi INTO doctorName 
    FROM bacsi 
    WHERE maBacSi = NEW.maBacSi;
    
    SET leaveDate = DATE_FORMAT(NEW.ngayNghi, '%d/%m/%Y');
    
    SELECT COUNT(*) INTO otherShiftExists
    FROM ngaynghi
    WHERE maBacSi = NEW.maBacSi 
    AND ngayNghi = NEW.ngayNghi
    AND maCa != NEW.maCa
    AND lyDo = NEW.lyDo;
    
    IF otherShiftExists > 0 AND NEW.maCa = 2 THEN
        SET isFullDay = TRUE;
        SET caInfo = ' Cả ngày';
    ELSEIF otherShiftExists > 0 AND NEW.maCa = 1 THEN
        LEAVE trigger_body;
    ELSE
        SELECT tenCa INTO shiftName 
        FROM calamviec 
        WHERE maCa = NEW.maCa;
        SET caInfo = CONCAT(' - ', shiftName);
    END IF;
    INSERT INTO thongbaoadmin (maNghi, maBacSi, loai, tieuDe, noiDung, thoiGian, daXem)
    VALUES (
        NEW.maNghi,
        NEW.maBacSi,
        'Nghỉ phép',
        'Đơn xin nghỉ phép',
        CONCAT('Bác sĩ ', doctorName, ' xin nghỉ phép vào ngày ', leaveDate, caInfo, 
               IF(NEW.lyDo IS NOT NULL AND NEW.lyDo != '', CONCAT('. Lý do: ', NEW.lyDo), '')),
        NOW(),
        0
    );
END
$$
DELIMITER ;

CREATE TABLE `nguoidung` (
  `id` int(11) NOT NULL,
  `tenDangNhap` varchar(50) NOT NULL,
  `matKhau` varchar(255) NOT NULL,
  `soDienThoai` varchar(16) DEFAULT NULL,
  `vaiTro` enum('benhnhan','bacsi','quantri') NOT NULL,
  `trangThai` enum('Hoạt Động','Khóa') NOT NULL DEFAULT 'Hoạt Động',
  `ngayCapNhatTaiKhoan` datetime DEFAULT NULL,
  `ngayCapNhatMatKhau` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `nguoidung` (`id`, `tenDangNhap`, `matKhau`, `soDienThoai`, `vaiTro`, `trangThai`, `ngayCapNhatTaiKhoan`, `ngayCapNhatMatKhau`) VALUES
(1, 'nguoidung1', 'passwork', '0987654322', 'benhnhan', 'Hoạt Động', NULL, NULL),
(2, 'nguoidung2', '$2y$10$XRsZeXYxxddlREUPuzZAD.vhWQKjQJGpJaNSmjyA1I.I99dJs2h1y', '0987654323', 'bacsi', 'Hoạt Động', '2025-11-19 10:30:38', '2025-11-19 10:29:16'),
(3, 'nguoidung3', '$2y$10$55XC.J1xbMe0tIWNJb9UneupzHgh1x1fioWqKiF/y8eAbcGpERYHC', '0987654321', 'quantri', 'Hoạt Động', NULL, NULL),
(5, 'nguyenthanhccur1025', '$2y$10$j/IqnU9fT0QPeHyZNU1uuum/5IktMdkELYMVs.Uvu9KOgu1PzjXoq', '0917382642', 'bacsi', 'Hoạt Động', NULL, NULL),
(7, 'levand', '$2y$10$0w2wLh5q8dn.05WVbEYjc.Epw.C4BLmppiM5Hwj4QO7fbSDvqfOkK', '0361846731', 'bacsi', 'Hoạt Động', NULL, NULL),
(8, 'ABCD', '$2y$10$Gucpt7iX418XWZkSgIf8EeSwEj3qDkaepUfrFLc6hiDm.CbmFDqsS', '0936846244', 'benhnhan', 'Hoạt Động', NULL, NULL),
(11, '0000000000', '$2y$10$dPxrzHJVA454.TgEO/rLXeta9uL32XKD3jHgx4x6F7VWRX0MSnCW2', '0000000000', 'benhnhan', 'Hoạt Động', NULL, NULL),
(13, 'ndagidyawbda', '$2y$10$CyYX/o4kjkEJwQY7d2EOqOvlDdwBPAhKKZWnILRQJj3oq3wTKYXKq', '0388888888', 'bacsi', 'Hoạt Động', NULL, NULL),
(14, '1234', '$2y$10$9fgoU9a7DmIzfT9lBeEIeOyck4vHWoGcUB4jWO3zG7iADAbjX1YBS', '0123456789', 'quantri', 'Hoạt Động', NULL, NULL),
(15, 'tranvanh2000', '$2y$10$b6vPWLbfLDSGTIy1FLdeau.aEyVNEA47aYMKQ7VttOy8umBOyQKBi', '0345678921', 'benhnhan', 'Hoạt Động', NULL, NULL);

CREATE TABLE `quantrivien` (
  `nguoiDungId` int(11) NOT NULL,
  `maQuanTriVien` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `quantrivien` (`nguoiDungId`, `maQuanTriVien`) VALUES
(3, 'admin1');

CREATE TABLE `suatkham` (
  `maSuat` int(11) NOT NULL,
  `maCa` int(11) NOT NULL,
  `gioBatDau` time NOT NULL,
  `gioKetThuc` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `suatkham` (`maSuat`, `maCa`, `gioBatDau`, `gioKetThuc`) VALUES
(1, 1, '07:00:00', '07:40:00'),
(2, 1, '07:40:00', '08:20:00'),
(3, 1, '08:20:00', '09:00:00'),
(4, 1, '09:00:00', '09:40:00'),
(5, 1, '09:40:00', '10:20:00'),
(6, 1, '10:20:00', '11:00:00'),
(7, 2, '13:00:00', '13:40:00'),
(8, 2, '13:40:00', '14:20:00'),
(9, 2, '14:20:00', '15:00:00'),
(10, 2, '15:00:00', '15:40:00'),
(11, 2, '15:40:00', '16:20:00'),
(12, 2, '16:20:00', '17:00:00');

CREATE TABLE `thongbaoadmin` (
  `maThongBao` int(11) NOT NULL,
  `maNghi` int(11) DEFAULT NULL,
  `maBacSi` varchar(20) NOT NULL,
  `loai` enum('Nghỉ phép','Hủy nghỉ') NOT NULL DEFAULT 'Nghỉ phép',
  `tieuDe` varchar(255) NOT NULL,
  `noiDung` text NOT NULL,
  `thoiGian` datetime DEFAULT current_timestamp(),
  `daXem` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `thongbaoadmin` (`maThongBao`, `maNghi`, `maBacSi`, `loai`, `tieuDe`, `noiDung`, `thoiGian`, `daXem`) VALUES
(1, 3, 'BS202511090112882', 'Nghỉ phép', 'Đơn xin nghỉ phép', 'Bác sĩ Nguyễn Thành C xin nghỉ phép vào ngày 17/11/2025 - Ca sáng. Lý do: 0', '2025-11-15 20:22:57', 1),
(2, 4, 'BS202511090112882', 'Nghỉ phép', 'Đơn xin nghỉ phép', 'Bác sĩ Nguyễn Thành C xin nghỉ phép vào ngày 17/11/2025 - Ca chiều. Lý do: ???', '2025-11-15 20:22:57', 1),
(3, 5, 'bs1', 'Nghỉ phép', 'Đơn xin nghỉ phép', 'Bác sĩ Trần Văn B xin nghỉ phép vào ngày 19/11/2025 - Ca sáng. Lý do: 0', '2025-11-18 19:52:10', 1),
(4, 6, 'bs1', 'Nghỉ phép', 'Đơn xin nghỉ phép', 'Bác sĩ Trần Văn B xin nghỉ phép vào ngày 19/11/2025 - Ca chiều. Lý do: ........', '2025-11-18 19:52:10', 1),
(5, NULL, 'bs1', 'Nghỉ phép', 'Yêu cầu cấp lại mật khẩu', 'Người dùng nguoidung2 yêu cầu cấp lại mật khẩu', '2025-11-19 10:30:56', 1);

CREATE TABLE `thongbaobenhnhan` (
  `maThongBao` int(11) NOT NULL,
  `maBenhNhan` varchar(20) NOT NULL,
  `loai` enum('Hệ thống','Lịch khám','Mật khẩu','Khác') NOT NULL DEFAULT 'Hệ thống',
  `tieuDe` varchar(255) NOT NULL,
  `noiDung` text NOT NULL,
  `thoiGian` datetime DEFAULT current_timestamp(),
  `daXem` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `thongbaolichkham` (
  `maThongBao` int(11) NOT NULL,
  `maBacSi` varchar(20) NOT NULL,
  `maLichKham` int(11) DEFAULT NULL,
  `loai` enum('Đặt lịch','Hủy lịch') NOT NULL,
  `tieuDe` varchar(255) NOT NULL,
  `noiDung` text NOT NULL,
  `thoiGian` datetime DEFAULT current_timestamp(),
  `daXem` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `thongbaolichkham` (`maThongBao`, `maBacSi`, `maLichKham`, `loai`, `tieuDe`, `noiDung`, `thoiGian`, `daXem`) VALUES
(1, 'bs1', 26, 'Đặt lịch', 'Lịch khám mới', 'Bệnh nhân Trần Văn H đã đặt lịch khám vào ngày 18/11/2025 - Ca sáng', '2025-11-18 03:44:15', 1),
(2, 'bs1', 27, 'Đặt lịch', 'Lịch khám mới', 'Bệnh nhân AAAAAAAA đã đặt lịch khám vào ngày 19/11/2025 - Ca sáng', '2025-11-18 18:48:42', 1),
(3, 'bs1', 28, 'Đặt lịch', 'Lịch khám mới', 'Bệnh nhân ABCs đã đặt lịch khám vào ngày 19/11/2025 - Ca sáng', '2025-11-18 18:48:59', 1),
(4, 'bs1', 29, 'Đặt lịch', 'Lịch khám mới', 'Bệnh nhân Nguyễn Văn A đã đặt lịch khám vào ngày 19/11/2025 - Ca chiều', '2025-11-18 18:49:11', 1),
(5, 'bs1', 30, 'Đặt lịch', 'Lịch khám mới', 'Bệnh nhân Trần Văn H đã đặt lịch khám vào ngày 19/11/2025 - Ca chiều', '2025-11-18 18:49:24', 1);


ALTER TABLE `bacsi`
  ADD PRIMARY KEY (`maBacSi`),
  ADD UNIQUE KEY `nguoiDungId` (`nguoiDungId`),
  ADD KEY `maChuyenKhoa` (`maChuyenKhoa`);

ALTER TABLE `benhnhan`
  ADD PRIMARY KEY (`maBenhNhan`),
  ADD UNIQUE KEY `nguoiDungId` (`nguoiDungId`);

ALTER TABLE `calamviec`
  ADD PRIMARY KEY (`maCa`);

ALTER TABLE `chuyenkhoa`
  ADD PRIMARY KEY (`maChuyenKhoa`),
  ADD KEY `maKhoa` (`maKhoa`);

ALTER TABLE `doimatkhau`
  ADD PRIMARY KEY (`id`),
  ADD KEY `nguoiDungId` (`nguoiDungId`),
  ADD KEY `nguoiXuLy` (`nguoiXuLy`);

ALTER TABLE `goikham`
  ADD PRIMARY KEY (`maGoi`);

ALTER TABLE `hosobenhan`
  ADD PRIMARY KEY (`maHoSo`),
  ADD KEY `maBenhNhan` (`maBenhNhan`),
  ADD KEY `maBacSi` (`maBacSi`),
  ADD KEY `maLichKham` (`maLichKham`);

ALTER TABLE `khoa`
  ADD PRIMARY KEY (`maKhoa`);

ALTER TABLE `lichkham`
  ADD PRIMARY KEY (`maLichKham`),
  ADD KEY `maBacSi` (`maBacSi`),
  ADD KEY `maBenhNhan` (`maBenhNhan`),
  ADD KEY `maCa` (`maCa`),
  ADD KEY `maSuat` (`maSuat`),
  ADD KEY `maGoi` (`maGoi`);

ALTER TABLE `ngaynghi`
  ADD PRIMARY KEY (`maNghi`),
  ADD KEY `maBacSi` (`maBacSi`),
  ADD KEY `maCa` (`maCa`);

ALTER TABLE `nguoidung`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tenDangNhap` (`tenDangNhap`),
  ADD UNIQUE KEY `soDienThoai` (`soDienThoai`);

ALTER TABLE `quantrivien`
  ADD PRIMARY KEY (`maQuanTriVien`),
  ADD UNIQUE KEY `nguoiDungId` (`nguoiDungId`);

ALTER TABLE `suatkham`
  ADD PRIMARY KEY (`maSuat`),
  ADD KEY `maCa` (`maCa`);

ALTER TABLE `thongbaoadmin`
  ADD PRIMARY KEY (`maThongBao`),
  ADD KEY `maBacSi` (`maBacSi`),
  ADD KEY `maNghi` (`maNghi`),
  ADD KEY `idx_daxem` (`daXem`),
  ADD KEY `idx_thoigian` (`thoiGian`);

ALTER TABLE `thongbaobenhnhan`
  ADD PRIMARY KEY (`maThongBao`),
  ADD KEY `maBenhNhan` (`maBenhNhan`),
  ADD KEY `idx_daxem` (`daXem`),
  ADD KEY `idx_thoigian` (`thoiGian`);

ALTER TABLE `thongbaolichkham`
  ADD PRIMARY KEY (`maThongBao`),
  ADD KEY `maBacSi` (`maBacSi`),
  ADD KEY `maLichKham` (`maLichKham`),
  ADD KEY `idx_daxem` (`daXem`),
  ADD KEY `idx_thoigian` (`thoiGian`);


ALTER TABLE `calamviec`
  MODIFY `maCa` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

ALTER TABLE `doimatkhau`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

ALTER TABLE `goikham`
  MODIFY `maGoi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

ALTER TABLE `lichkham`
  MODIFY `maLichKham` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

ALTER TABLE `ngaynghi`
  MODIFY `maNghi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

ALTER TABLE `nguoidung`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

ALTER TABLE `suatkham`
  MODIFY `maSuat` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

ALTER TABLE `thongbaoadmin`
  MODIFY `maThongBao` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

ALTER TABLE `thongbaobenhnhan`
  MODIFY `maThongBao` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `thongbaolichkham`
  MODIFY `maThongBao` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;


ALTER TABLE `bacsi`
  ADD CONSTRAINT `bacsi_ibfk_1` FOREIGN KEY (`nguoiDungId`) REFERENCES `nguoidung` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bacsi_ibfk_2` FOREIGN KEY (`maChuyenKhoa`) REFERENCES `chuyenkhoa` (`maChuyenKhoa`) ON DELETE SET NULL;

ALTER TABLE `benhnhan`
  ADD CONSTRAINT `benhnhan_ibfk_1` FOREIGN KEY (`nguoiDungId`) REFERENCES `nguoidung` (`id`) ON DELETE CASCADE;

ALTER TABLE `chuyenkhoa`
  ADD CONSTRAINT `chuyenkhoa_ibfk_1` FOREIGN KEY (`maKhoa`) REFERENCES `khoa` (`maKhoa`) ON DELETE CASCADE;

ALTER TABLE `doimatkhau`
  ADD CONSTRAINT `doimatkhau_ibfk_1` FOREIGN KEY (`nguoiDungId`) REFERENCES `nguoidung` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `doimatkhau_ibfk_2` FOREIGN KEY (`nguoiXuLy`) REFERENCES `nguoidung` (`id`) ON DELETE SET NULL;

ALTER TABLE `hosobenhan`
  ADD CONSTRAINT `hosobenhan_ibfk_1` FOREIGN KEY (`maBenhNhan`) REFERENCES `benhnhan` (`maBenhNhan`) ON DELETE CASCADE,
  ADD CONSTRAINT `hosobenhan_ibfk_2` FOREIGN KEY (`maBacSi`) REFERENCES `bacsi` (`maBacSi`) ON DELETE SET NULL,
  ADD CONSTRAINT `hosobenhan_ibfk_3` FOREIGN KEY (`maLichKham`) REFERENCES `lichkham` (`maLichKham`) ON DELETE SET NULL;

ALTER TABLE `lichkham`
  ADD CONSTRAINT `lichkham_ibfk_1` FOREIGN KEY (`maBacSi`) REFERENCES `bacsi` (`maBacSi`),
  ADD CONSTRAINT `lichkham_ibfk_2` FOREIGN KEY (`maBenhNhan`) REFERENCES `benhnhan` (`maBenhNhan`),
  ADD CONSTRAINT `lichkham_ibfk_3` FOREIGN KEY (`maCa`) REFERENCES `calamviec` (`maCa`),
  ADD CONSTRAINT `lichkham_ibfk_4` FOREIGN KEY (`maSuat`) REFERENCES `suatkham` (`maSuat`),
  ADD CONSTRAINT `lichkham_ibfk_5` FOREIGN KEY (`maGoi`) REFERENCES `goikham` (`maGoi`);

ALTER TABLE `ngaynghi`
  ADD CONSTRAINT `ngaynghi_ibfk_1` FOREIGN KEY (`maBacSi`) REFERENCES `bacsi` (`maBacSi`),
  ADD CONSTRAINT `ngaynghi_ibfk_2` FOREIGN KEY (`maCa`) REFERENCES `calamviec` (`maCa`);

ALTER TABLE `quantrivien`
  ADD CONSTRAINT `quantrivien_ibfk_1` FOREIGN KEY (`nguoiDungId`) REFERENCES `nguoidung` (`id`) ON DELETE CASCADE;

ALTER TABLE `suatkham`
  ADD CONSTRAINT `suatkham_ibfk_1` FOREIGN KEY (`maCa`) REFERENCES `calamviec` (`maCa`);

ALTER TABLE `thongbaoadmin`
  ADD CONSTRAINT `thongbaoadmin_ibfk_1` FOREIGN KEY (`maBacSi`) REFERENCES `bacsi` (`maBacSi`) ON DELETE CASCADE,
  ADD CONSTRAINT `thongbaoadmin_ibfk_2` FOREIGN KEY (`maNghi`) REFERENCES `ngaynghi` (`maNghi`) ON DELETE SET NULL;

ALTER TABLE `thongbaobenhnhan`
  ADD CONSTRAINT `thongbaobenhnhan_ibfk_1` FOREIGN KEY (`maBenhNhan`) REFERENCES `benhnhan` (`maBenhNhan`) ON DELETE CASCADE;

ALTER TABLE `thongbaolichkham`
  ADD CONSTRAINT `thongbao_ibfk_1` FOREIGN KEY (`maBacSi`) REFERENCES `bacsi` (`maBacSi`) ON DELETE CASCADE,
  ADD CONSTRAINT `thongbao_ibfk_2` FOREIGN KEY (`maLichKham`) REFERENCES `lichkham` (`maLichKham`) ON DELETE SET NULL;