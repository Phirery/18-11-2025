CREATE DATABASE IF NOT EXISTS `datlichkham` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `datlichkham`;

CREATE TABLE `bacsi` (
  `nguoiDungId` int(11) NOT NULL,
  `maBacSi` varchar(20) NOT NULL,
  `tenBacSi` varchar(100) DEFAULT NULL,
  `maChuyenKhoa` varchar(10) DEFAULT NULL,
  `moTa` text DEFAULT NULL,
  `chuyenGia` tinyint(1) NOT NULL DEFAULT 0,
  `gioiTinh` enum('nam','nu') DEFAULT NULL,
  `namLamViec` smallint(5) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `bacsi` (`nguoiDungId`, `maBacSi`, `tenBacSi`, `maChuyenKhoa`, `moTa`, `chuyenGia`, `gioiTinh`, `namLamViec`) VALUES
(2, 'bs1', 'Trần Văn BBD', 'EYE102501', '', 0, 'nam', 2021),
(5, 'BS202511090112882', 'Nguyễn Thành C', 'SUR102501', '', 0, 'nam', 2018),
(13, 'BS202511102320635', 'Nguyễn Z', 'DER102502', '', 0, 'nu', 2022),
(16, 'BS20251121009', 'Lê Thanh Bình', 'PED102501', 'Bác sĩ chuyên khoa II Nhi khoa. 10 năm công tác tại Bệnh viện Nhi Đồng 1. Rất mát tay và yêu trẻ, chuyên điều trị các bệnh hô hấp ở trẻ nhỏ.', 0, 'nam', 2015),
(17, 'BS20251121010', 'Nguyễn Thị Kim Anh', 'PED102502', 'Tốt nghiệp loại Giỏi Đại học Y Dược. Chuyên gia về chăm sóc và nuôi dưỡng trẻ sơ sinh, đặc biệt là trẻ sinh non nhẹ cân.', 0, 'nu', 2013),
(18, 'BS20251121011', 'Trần Quốc Đạt', 'OBG102501', 'Trưởng khoa Sản bệnh viện Đa khoa Khu vực. Hơn 15 năm kinh nghiệm đỡ sinh và phẫu thuật sản khoa (mổ lấy thai) phức tạp.', 0, 'nam', 2018),
(19, 'BS20251121012', 'Phạm Thị Thanh Thủy', 'OBG102502', 'Chuyên gia tư vấn sức khỏe sinh sản và điều trị các bệnh lý phụ khoa. Tốt nghiệp Thạc sĩ y học tại Pháp. Nhẹ nhàng, tâm lý.', 0, 'nu', 2011),
(20, 'BS20251121013', 'Hoàng Văn Sơn', 'DER102501', 'Chuyên điều trị các bệnh lý về da mãn tính: Vảy nến, Viêm da cơ địa, Nấm da. 7 năm kinh nghiệm tại Bệnh viện Da Liễu.', 0, 'nam', 2018),
(21, 'BS20251121014', 'Vương Thị Mai', 'DER102502', 'Bác sĩ trẻ tài năng, chuyên sâu về Laser thẩm mỹ, trị sẹo rỗ và trẻ hóa da. Chứng chỉ hành nghề thẩm mỹ quốc tế.', 0, 'nu', 2005),
(22, 'BS20251121015', 'Đặng Minh Hiếu', 'ENT102502', 'Kinh nghiệm dày dặn trong phẫu thuật nội soi mũi xoang và điều trị viêm xoang mãn tính.', 0, 'nam', 2008),
(23, 'BS20251121016', 'Bùi Thị Lan', 'ENT102501', 'Chuyên khám và đo thính lực, điều trị viêm tai giữa ở trẻ em và người lớn. Nhẹ nhàng, cẩn thận.', 0, 'nu', 2011),
(24, 'BS20251121017', 'Phan Thành Long', 'DEN102503', 'Chuyên gia cấy ghép Implant và phẫu thuật chấn thương hàm mặt. Tu nghiệp 2 năm tại Hàn Quốc.', 0, 'nam', 2011),
(25, 'BS20251121018', 'Lý Thị Bích Ngọc', 'DEN102502', 'Chuyên sâu về chỉnh nha (niềng răng) thẩm mỹ invisalign. Đã thực hiện thành công hơn 500 ca chỉnh nha phức tạp.', 0, 'nu', 2010),
(26, 'BS20251121019', 'Nguyễn Quang Dũng', 'CAR102502', 'Phó giáo sư, Tiến sĩ y học. Chuyên gia hàng đầu về đặt stent và chụp mạch vành. 25 năm kinh nghiệm.', 0, 'nam', 2019),
(27, 'BS20251121020', 'Trần Thị Hương Giang', 'CAR102501', 'Chuyên điều trị tăng huyết áp, suy tim và rối loạn nhịp tim bằng thuốc. Theo dõi sát sao sức khỏe bệnh nhân cao tuổi.', 0, 'nu', 2019),
(28, 'BS20251121021', 'Vũ Minh Đức', 'INT102503', 'Chuyên điều trị đau đầu, mất ngủ, rối loạn tiền đình và phục hồi sau đột quỵ.', 0, 'nam', 2011),
(29, 'BS20251121022', 'Nguyễn Hoàng Anh', 'INT102503', 'Bác sĩ trẻ, cập nhật các phác đồ điều trị mới về bệnh Parkinson và Alzheimer.', 0, 'nam', 2015),
(30, 'BS20251121023', 'Lê Trung Kiên', 'INT102502', 'Chuyên gia nội soi tiêu hóa (dạ dày, đại tràng) không đau. Tầm soát ung thư đường tiêu hóa sớm.', 0, 'nam', 2013),
(31, 'BS20251121024', 'Trần Thị Kim Huệ', 'INT102502', 'Điều trị viêm loét dạ dày do vi khuẩn HP, bệnh lý gan mật và tư vấn dinh dưỡng tiêu hóa.', 0, 'nu', 2020),
(32, 'BS20251121025', 'Phạm Thanh Hải', 'SUR102502', 'Chuyên gia phẫu thuật thay khớp gối, khớp háng và nội soi khớp vai. Đã thực hiện hơn 1000 ca mổ thành công.', 0, 'nam', 2020),
(33, 'BS20251121026', 'Nguyễn Thị Thu Phương', 'SUR102502', 'Chuyên điều trị bảo tồn (không phẫu thuật) các chấn thương thể thao, thoái hóa khớp và loãng xương.', 0, 'nu', 2019),
(34, 'BS20251121027', 'Trần Trung Nghĩa', 'EYE102502', 'Chuyên điều trị các bệnh lý đáy mắt, võng mạc tiểu đường và thoái hóa điểm vàng. 8 năm kinh nghiệm tại Viện Mắt.', 0, 'nam', 2016),
(35, 'BS20251121028', 'Đỗ Thị Bích', 'EYE102502', 'Điều trị viêm kết mạc, khô mắt và các bệnh lý bề mặt nhãn cầu. Tận tâm, nhẹ nhàng với người cao tuổi.', 0, 'nu', 2020),
(36, 'BS20251121029', 'Nguyễn Hoàng Sơn', 'EYE102503', 'Bàn tay vàng trong phẫu thuật Phaco (đục thủy tinh thể). Đã thực hiện thành công hơn 5000 ca mổ mắt.', 0, 'nam', 2000),
(37, 'BS20251121030', 'Lê Thị Mai Hương', 'EYE102503', 'Chuyên phẫu thuật mộng thịt, quặm mi và thẩm mỹ mắt. Được đào tạo chuyên sâu về tạo hình nhãn khoa.', 0, 'nu', 2014),
(38, 'BS20251121031', 'Phạm Văn Quang', 'INT102501', 'Chuyên gia điều trị Hen suyễn và Phổi tắc nghẽn mạn tính (COPD). Có nhiều công trình nghiên cứu về chức năng hô hấp.', 0, 'nam', 2014),
(39, 'BS20251121032', 'Nguyễn Thị Lan Phương', 'INT102501', 'Chuyên khám và điều trị viêm phổi, viêm phế quản. Tư vấn cai thuốc lá và phục hồi chức năng hô hấp hậu Covid.', 0, 'nu', 2014),
(40, 'BS20251121033', 'Hoàng Văn Khải', 'INT102504', 'Chuyên gia điều trị Đái tháo đường và các bệnh lý Tuyến giáp. Giúp bệnh nhân kiểm soát đường huyết ổn định lâu dài.', 0, 'nam', 2016),
(41, 'BS20251121034', 'Vũ Thị Tú Oanh', 'INT102504', 'Tư vấn dinh dưỡng và điều trị rối loạn chuyển hóa, béo phì, mỡ máu cao. Tốt nghiệp Đại học Y Hà Nội.', 0, 'nu', 2010),
(42, 'BS20251121035', 'Đặng Văn Hùng', 'INT102505', 'Chuyên điều trị hội chứng thận hư, suy thận mạn. Tư vấn các phương pháp lọc máu và bảo tồn chức năng thận.', 0, 'nam', 2017),
(43, 'BS20251121036', 'Lý Thị Minh', 'INT102505', 'Điều trị viêm đường tiết niệu, sỏi thận nhỏ bằng phương pháp nội khoa. Nhẹ nhàng, chu đáo.', 0, 'nu', 2009),
(44, 'BS20251121037', 'Nguyễn Văn Phúc', 'SUR102503', 'Phẫu thuật viên thần kinh sọ não hàng đầu. Chuyên mổ u não, chấn thương sọ não và phẫu thuật cột sống ít xâm lấn.', 0, 'nam', 2019),
(45, 'BS20251121038', 'Trần Thị Yến', 'SUR102503', 'Chuyên gia về phẫu thuật điều trị thoát vị đĩa đệm và đau thần kinh tọa. Áp dụng công nghệ vi phẫu hiện đại.', 0, 'nu', 2009),
(46, 'BS20251121039', 'Bùi Văn Toàn', 'SUR102504', 'Chuyên tán sỏi thận qua da và nội soi ngược dòng. Điều trị phì đại tuyến tiền liệt bằng Laser.', 0, 'nam', 2007),
(47, 'BS20251121040', 'Phạm Thị Hương', 'SUR102504', 'Bác sĩ nữ hiếm hoi trong ngành ngoại tiết niệu. Chuyên điều trị són tiểu, bàng quang tăng hoạt ở phụ nữ.', 0, 'nu', 2014),
(48, 'BS20251121041', 'Lê Minh Vương', 'SUR102505', 'Bàn tay vàng phẫu thuật tim hở và bắc cầu động mạch vành. 15 năm tu nghiệp tại Đức và Pháp.', 0, 'nam', 2013),
(49, 'BS20251121042', 'Nguyễn Thị Kiều Trinh', 'SUR102505', 'Chuyên phẫu thuật nội soi lồng ngực điều trị tăng tiết mồ hôi tay và các bệnh lý phổi, màng phổi.', 0, 'nu', 2016),
(50, 'BS20251121043', 'Đỗ Vũ Hoàng', 'PED102503', 'Chuyên gia tầm soát bệnh tim bẩm sinh ở trẻ em. Từng tu nghiệp 3 năm tại Singapore về can thiệp tim mạch nhi.', 0, 'nam', 2022),
(51, 'BS20251121044', 'Lưu Thị Minh', 'PED102503', 'Nguyên trưởng khoa Nhi Bệnh viện Tim. Hơn 30 năm kinh nghiệm theo dõi và điều trị nội khoa tim mạch cho trẻ sinh non.', 0, 'nu', 2012),
(52, 'BS20251121045', 'Mạc Văn Khoa', 'INT102506', 'Bác sĩ nội trú tốt nghiệp loại xuất sắc. Năng động, cập nhật nhanh các phác đồ điều trị mới về bệnh lý nhiễm trùng và miễn dịch.', 0, 'nam', 2022),
(53, 'BS20251121046', 'Tống Thị Kim', 'INT102506', 'Chuyên khám sức khỏe tổng quát và tư vấn tầm soát ung thư sớm. Có kinh nghiệm dày dặn trong điều trị các bệnh lão khoa.', 0, 'nu', 2008),
(54, 'BS20251121047', 'Nguyễn Bá Duy', 'ENT102503', 'Chuyên phẫu thuật cắt Amidan bằng công nghệ Plasma. Điều trị hiệu quả các bệnh lý khàn tiếng, hạt xơ dây thanh.', 0, 'nam', 2019),
(55, 'BS20251121048', 'Hồ Tiên', 'ENT102503', 'Chuyên gia đầu ngành về bệnh lý ung thư vòm họng và thanh quản. Giảng viên kiêm nhiệm Đại học Y Dược.', 0, 'nu', 2019),
(56, 'BS20251121049', 'Trịnh Quốc Thái', 'DEN102501', 'Bác sĩ trẻ nhiệt huyết, tốt nghiệp Thủ khoa Răng Hàm Mặt. Ứng dụng công nghệ kỹ thuật số trong hàn răng và điều trị tủy.', 0, 'nam', 2024),
(57, 'BS20251121050', 'Bùi Thị Xuân', 'DEN102501', 'Hơn 20 năm kinh nghiệm trong nha khoa gia đình. Nhẹ nhàng, tâm lý, chuyên điều trị sâu răng cho trẻ em không gây đau.', 0, 'nu', 2010);

CREATE TABLE `benhnhan` (
  `nguoiDungId` int(11) NOT NULL,
  `maBenhNhan` varchar(20) NOT NULL,
  `tenBenhNhan` varchar(100) DEFAULT NULL,
  `ngaySinh` date DEFAULT NULL,
  `gioiTinh` enum('nam','nu','khac') DEFAULT NULL,
  `soTheBHYT` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `benhnhan` (`nguoiDungId`, `maBenhNhan`, `tenBenhNhan`, `ngaySinh`, `gioiTinh`, `soTheBHYT`) VALUES
(1, 'bn1', 'Nguyễn Văn Anh', '2000-01-01', 'nam', ''),
(8, 'BN202511082304701', 'ABCs', '2005-10-09', 'nam', ''),
(11, 'BN202511101515250', 'AAAAAAAA', '2025-11-09', 'khac', 'BH189318214111'),
(15, 'BN2025111712142915', 'Trần Văn H', '2000-12-31', 'nam', NULL),
(58, 'BN2025112200000058', 'Test', '2025-11-21', 'nam', NULL),
(59, 'BN2025112200000059', 'Test', '2025-11-21', 'nam', NULL);
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
(1, 2, 'Đã xử lý', '2025-11-19 10:30:56', '2025-11-19 11:49:57', 3),
(2, 2, 'Đã xử lý', '2025-11-21 00:26:03', '2025-11-21 00:26:19', 3);
DELIMITER $$
CREATE TRIGGER `after_doimatkhau_insert` AFTER INSERT ON `doimatkhau` FOR EACH ROW BEGIN
    DECLARE userName VARCHAR(100);
    DECLARE userPhone VARCHAR(16);
    DECLARE userRole VARCHAR(20);
    DECLARE bacSiId VARCHAR(20);
    
    SELECT nd.tenDangNhap, nd.soDienThoai, nd.vaiTro
    INTO userName, userPhone, userRole
    FROM nguoidung nd
    WHERE nd.id = NEW.nguoiDungId;
    
    IF userRole = 'bacsi' THEN
        SELECT maBacSi INTO bacSiId
        FROM bacsi
        WHERE nguoiDungId = NEW.nguoiDungId;
    ELSE
        SET bacSiId = 'SYSTEM';
    END IF;
    
    INSERT INTO thongbaoadmin (
        maNghi, 
        requestId,
        maBacSi, 
        soDienThoai,
        loai, 
        tieuDe, 
        noiDung, 
        thoiGian, 
        daXem,
        trangThai
    )
    VALUES (
        NULL,
        NEW.id,
        bacSiId,
        userPhone,
        'Cấp lại mật khẩu',
        'Yêu cầu cấp lại mật khẩu',
        CONCAT('Người dùng ', userName, ' (', userRole, ') yêu cầu cấp lại mật khẩu'),
        NOW(),
        0,
        'Chờ'
    );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_doimatkhau_update` AFTER UPDATE ON `doimatkhau` FOR EACH ROW BEGIN
    IF NEW.trangThai != OLD.trangThai THEN
        UPDATE thongbaoadmin
        SET trangThai = NEW.trangThai,
            thoiGianXuLy = NEW.thoiGianXuLy
        WHERE requestId = NEW.id;
    END IF;
END
$$
DELIMITER ;

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
('HS20251119083148530', 'BN202511101515250', 'bs1', 27, '4', '4', 'Đã hoàn thành', '2025-11-19 08:31:48', '2025-11-19 08:31:59', '4', '2025-11-19 00:00:00'),
('HS20251123220207601', 'BN2025112200000058', 'bs1', 46, '1', '1', 'Đã hoàn thành', '2025-11-23 22:02:07', '2025-11-23 22:02:18', '1', '2025-11-23 00:00:00'),
('HS20251123220214894', 'BN2025112200000058', 'bs1', 45, '2', '2', 'Đã hoàn thành', '2025-11-23 22:02:14', '2025-11-23 22:02:21', '2', '2025-11-23 00:00:00');

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
(30, 'bs1', 'BN2025111712142915', '2025-11-19', 2, 12, 2, 'Hoàn thành', NULL),
(31, 'BS20251121031', 'bn1', '0000-00-00', 1, 4, 2, 'Đã đặt', 'test 1'),
(32, 'BS20251121022', 'bn1', '0000-00-00', 1, 1, 1, 'Đã đặt', ''),
(33, 'BS20251121022', 'bn1', '0000-00-00', 1, 1, 2, 'Đã đặt', ''),
(34, 'BS20251121022', 'bn1', '0000-00-00', 1, 1, 1, 'Đã đặt', '1'),
(35, 'BS20251121022', 'bn1', '2025-11-22', 1, 1, 1, 'Đã đặt', '1'),
(36, 'BS20251121022', 'bn1', '2025-11-22', 1, 2, 1, 'Đã đặt', '1'),
(37, 'BS202511102320635', 'bn1', '2025-11-21', 2, 8, 2, 'Đã đặt', ''),
(38, 'BS20251121027', 'bn1', '2025-11-24', 1, 1, 1, 'Đã đặt', 'Không'),
(39, 'BS20251121027', 'BN2025112200000058', '2025-11-24', 1, 2, 2, 'Hủy', '1\n[Lý do hủy]: Thử'),
(40, 'BS20251121028', 'BN2025112200000058', '2025-11-29', 1, 1, 2, 'Hủy', '\n[Lý do hủy]: Hủy'),
(41, 'BS20251121027', 'BN2025112200000058', '2025-11-24', 1, 2, 2, 'Hủy', '1\n[Lý do hủy]: 1'),
(42, 'BS20251121027', 'BN2025112200000058', '2025-11-24', 1, 2, 2, 'Hủy', '2\n[Lý do hủy]: 2'),
(43, 'BS20251121027', 'BN2025112200000058', '2025-11-23', 1, 4, 2, 'Hủy', '\n[Lý do hủy]: a'),
(44, 'BS20251121028', 'BN2025112200000058', '2025-11-23', 2, 12, 2, 'Hủy', '\n[Lý do hủy]: a'),
(45, 'bs1', 'BN2025112200000058', '2025-11-23', 1, 6, 2, 'Hoàn thành', ''),
(46, 'bs1', 'BN2025112200000058', '2025-11-23', 2, 12, 2, 'Hoàn thành', '');
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
    DECLARE slotTime VARCHAR(50);
    IF NEW.trangThai = 'Hủy' AND OLD.trangThai != 'Hủy' THEN
        SELECT tenBenhNhan INTO patientName 
        FROM benhnhan 
        WHERE maBenhNhan = NEW.maBenhNhan;
        SELECT tenCa INTO shiftName 
        FROM calamviec 
        WHERE maCa = NEW.maCa;
        SELECT CONCAT(
            SUBSTRING(gioBatDau, 1, 5), 
            ' - ', 
            SUBSTRING(gioKetThuc, 1, 5)
        ) INTO slotTime
        FROM suatkham
        WHERE maSuat = NEW.maSuat;
        SET appointmentDate = DATE_FORMAT(NEW.ngayKham, '%d/%m/%Y');
        IF NOT EXISTS (
            SELECT 1 FROM thongbaolichkham 
            WHERE maLichKham = NEW.maLichKham 
            AND loai = 'Hủy lịch'
            AND thoiGian >= DATE_SUB(NOW(), INTERVAL 5 SECOND)
        ) THEN
            INSERT INTO thongbaolichkham (
                maBacSi, 
                maLichKham, 
                loai, 
                tieuDe, 
                noiDung, 
                thoiGian, 
                daXem
            )
            VALUES (
                NEW.maBacSi,
                NEW.maLichKham,
                'Hủy lịch',
                'Lịch khám đã hủy',
                CONCAT(
                    'Bệnh nhân ', 
                    patientName, 
                    ' đã hủy lịch khám vào ngày ', 
                    appointmentDate, 
                    ' - ', 
                    shiftName,
                    ' - ',
                    slotTime
                ),
                NOW(),
                0
            );
        END IF;
        
    END IF;
END
$$
DELIMITER ;

CREATE TABLE `lienhe` (
  `maLienHe` int(11) NOT NULL,
  `hoTen` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `soDienThoai` varchar(15) NOT NULL,
  `chuDe` varchar(100) NOT NULL,
  `noiDung` text NOT NULL,
  `trangThai` enum('Chưa xử lý','Đang xử lý','Đã xử lý') NOT NULL DEFAULT 'Chưa xử lý',
  `thoiGianGui` datetime NOT NULL DEFAULT current_timestamp(),
  `nguoiXuLy` int(11) DEFAULT NULL,
  `thoiGianXuLy` datetime DEFAULT NULL,
  `ghiChu` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `lienhe` (`maLienHe`, `hoTen`, `email`, `soDienThoai`, `chuDe`, `noiDung`, `trangThai`, `thoiGianGui`, `nguoiXuLy`, `thoiGianXuLy`, `ghiChu`) VALUES
(1, 'Test', 'test@gmail.com', '0123456789', 'Khác', 'test', 'Chưa xử lý', '2025-11-23 19:21:51', NULL, NULL, NULL),
(2, 'testtwo', 'two@gmail.vn', '0987654321', 'Khác', 'a', 'Chưa xử lý', '2025-11-23 19:42:29', NULL, NULL, NULL);

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
(1, 'nguoidung1', '$2y$10$WOub5LzDY5orp0Kf3Yuphe3MBLcafNC5VIA6C0zvXHaZ.uUT1AILW', '0987654322', 'benhnhan', 'Hoạt Động', '2025-11-21 15:25:43', NULL),
(2, 'nguoidung2', '$2y$10$J8OsJlHOsaa07Kvz4AmiRehJ0csjW4ef16ogQ.tYyKPbYTts5G4n2', '0987654323', 'bacsi', 'Hoạt Động', '2025-11-19 10:30:38', NULL),
(3, 'nguoidung3', '$2y$10$55XC.J1xbMe0tIWNJb9UneupzHgh1x1fioWqKiF/y8eAbcGpERYHC', '0987654321', 'quantri', 'Hoạt Động', NULL, NULL),
(5, 'nguyenthanhccur1025', '$2y$10$j/IqnU9fT0QPeHyZNU1uuum/5IktMdkELYMVs.Uvu9KOgu1PzjXoq', '0917382642', 'bacsi', 'Hoạt Động', NULL, NULL),
(7, 'levand', '$2y$10$0w2wLh5q8dn.05WVbEYjc.Epw.C4BLmppiM5Hwj4QO7fbSDvqfOkK', '0361846731', 'bacsi', 'Hoạt Động', NULL, NULL),
(8, 'ABCD', '$2y$10$Gucpt7iX418XWZkSgIf8EeSwEj3qDkaepUfrFLc6hiDm.CbmFDqsS', '0936846244', 'benhnhan', 'Hoạt Động', NULL, NULL),
(11, '0000000000', '$2y$10$dPxrzHJVA454.TgEO/rLXeta9uL32XKD3jHgx4x6F7VWRX0MSnCW2', '0000000000', 'benhnhan', 'Hoạt Động', NULL, NULL),
(13, 'ndagidyawbda', '$2y$10$CyYX/o4kjkEJwQY7d2EOqOvlDdwBPAhKKZWnILRQJj3oq3wTKYXKq', '0388888888', 'bacsi', 'Hoạt Động', NULL, NULL),
(14, '1234', '$2y$10$9fgoU9a7DmIzfT9lBeEIeOyck4vHWoGcUB4jWO3zG7iADAbjX1YBS', '0123456789', 'quantri', 'Hoạt Động', NULL, NULL),
(15, 'tranvanh2000', '$2y$10$b6vPWLbfLDSGTIy1FLdeau.aEyVNEA47aYMKQ7VttOy8umBOyQKBi', '0345678921', 'benhnhan', 'Hoạt Động', NULL, NULL),
(16, 'lethanhbinh', 'lethanhbinh1993', '0912001001', 'bacsi', 'Hoạt Động', NULL, NULL),
(17, 'nguyenthikimanh', 'nguyenthikimanh1999', '0912001002', 'bacsi', 'Hoạt Động', NULL, NULL),
(18, 'tranquocdat', 'tranquocdat1990', '0912002001', 'bacsi', 'Hoạt Động', NULL, NULL),
(19, 'phamthithanhthuy', 'phamthithanhthuy2001', '0912002002', 'bacsi', 'Hoạt Động', NULL, NULL),
(20, 'hoangvanson', 'hoangvanson1996', '0912003001', 'bacsi', 'Hoạt Động', NULL, NULL),
(21, 'vuongthimai', 'vuongthimai2002', '0912003002', 'bacsi', 'Hoạt Động', NULL, NULL),
(22, 'dangminhhieu', 'dangminhhieu1994', '0912004001', 'bacsi', 'Hoạt Động', NULL, NULL),
(23, 'buithilan', 'buithilan2000', '0912004002', 'bacsi', 'Hoạt Động', NULL, NULL),
(24, 'phanthanhlong', 'phanthanhlong1991', '0912005001', 'bacsi', 'Hoạt Động', NULL, NULL),
(25, 'lythibichngoc', 'lythibichngoc2003', '0912005002', 'bacsi', 'Hoạt Động', NULL, NULL),
(26, 'nguyenquangdung', 'nguyenquangdung1988', '0912006001', 'bacsi', 'Hoạt Động', NULL, NULL),
(27, 'tranthihuonggiang', 'tranthihuonggiang1998', '0912006002', 'bacsi', 'Hoạt Động', NULL, NULL),
(28, 'vuminhduc', 'vuminhduc1992', '0912007001', 'bacsi', 'Hoạt Động', NULL, NULL),
(29, 'nguyenhoanganh', 'nguyenhoanganh2004', '0912007002', 'bacsi', 'Hoạt Động', NULL, NULL),
(30, 'letrungkien', 'letrungkien1995', '0912008001', 'bacsi', 'Hoạt Động', NULL, NULL),
(31, 'tranthikimhue', 'tranthikimhue1997', '0912008002', 'bacsi', 'Hoạt Động', NULL, NULL),
(32, 'phamthanhhai', 'phamthanhhai1990', '0912009001', 'bacsi', 'Hoạt Động', NULL, NULL),
(33, 'nguyenthithuphuong', 'nguyenthithuphuong2000', '0912009002', 'bacsi', 'Hoạt Động', NULL, NULL),
(34, 'trantrungnghia', 'trantrungnghia1994', '0922001001', 'bacsi', 'Hoạt Động', NULL, NULL),
(35, 'dothibich', 'dothibich2001', '0922001002', 'bacsi', 'Hoạt Động', NULL, NULL),
(36, 'nguyenhoangson', 'nguyenhoangson1990', '0922002001', 'bacsi', 'Hoạt Động', NULL, NULL),
(37, 'lethimaihuong', 'lethimaihuong1999', '0922002002', 'bacsi', 'Hoạt Động', NULL, NULL),
(38, 'phamvanquang', 'phamvanquang1992', '0922003001', 'bacsi', 'Hoạt Động', NULL, NULL),
(39, 'nguyenthilanphuong', 'nguyenthilanphuong2000', '0922003002', 'bacsi', 'Hoạt Động', NULL, NULL),
(40, 'hoangvankhai', 'hoangvankhai1995', '0922004001', 'bacsi', 'Hoạt Động', NULL, NULL),
(41, 'vuthituoanh', 'vuthituoanh1998', '0922004002', 'bacsi', 'Hoạt Động', NULL, NULL),
(42, 'dangvanhung', 'dangvanhung1991', '0922005001', 'bacsi', 'Hoạt Động', NULL, NULL),
(43, 'lythiminh', 'lythiminh2003', '0922005002', 'bacsi', 'Hoạt Động', NULL, NULL),
(44, 'nguyenvanphuc', 'nguyenvanphuc1989', '0922006001', 'bacsi', 'Hoạt Động', NULL, NULL),
(45, 'tranthiyen', 'tranthiyen2002', '0922006002', 'bacsi', 'Hoạt Động', NULL, NULL),
(46, 'buivantoan', 'buivantoan1996', '0922007001', 'bacsi', 'Hoạt Động', NULL, NULL),
(47, 'phamthihuong', 'phamthihuong2000', '0922007002', 'bacsi', 'Hoạt Động', NULL, NULL),
(48, 'leminhvuong', 'leminhvuong1987', '0922008001', 'bacsi', 'Hoạt Động', NULL, NULL),
(49, 'nguyenthikieutrinh', 'nguyenthikieutrinh1999', '0922008002', 'bacsi', 'Hoạt Động', NULL, NULL),
(50, 'dovuhoang', 'dovuhoang2011', '0933001001', 'bacsi', 'Hoạt Động', NULL, NULL),
(51, 'luuthiminh', 'luuthiminh1993', '0933001002', 'bacsi', 'Hoạt Động', NULL, NULL),
(52, 'macvankhoa', 'macvankhoa2022', '0933002001', 'bacsi', 'Hoạt Động', NULL, NULL),
(53, 'tongthikim', 'tongthikim2008', '0933002002', 'bacsi', 'Hoạt Động', NULL, NULL),
(54, 'nguyenbaduy', 'nguyenbaduy2019', '0933003001', 'bacsi', 'Hoạt Động', NULL, NULL),
(55, 'hotien', 'hotien1991', '0933003002', 'bacsi', 'Hoạt Động', NULL, NULL),
(56, 'trinhquocthai', 'trinhquocthai2024', '0933004001', 'bacsi', 'Hoạt Động', NULL, NULL),
(57, 'buithixuan', 'buithixuan2004', '0933004002', 'bacsi', 'Hoạt Động', NULL, NULL),
(58, 'test1', '$2y$10$BggHGVbOVxyYXW0ewXl6NeWGS0a6wlywSfBVltAl.87OmOGKAI3A.', '0111111111', 'benhnhan', 'Hoạt Động', '2025-11-22 23:31:28', '2025-11-23 16:28:43'),
(59, 'test2', '$2y$10$6Cn2VIZkjaF7kfvcZk.6POVas2oojtrKoVXkUQHH3H3/0psYmJL/q', '0222222222', 'benhnhan', 'Hoạt Động', '2025-11-22 23:33:27', NULL);

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
  `requestId` int(11) DEFAULT NULL,
  `soDienThoai` varchar(16) DEFAULT NULL,
  `maBacSi` varchar(20) NOT NULL,
  `loai` enum('Nghỉ phép','Hủy nghỉ','Cấp lại mật khẩu') NOT NULL DEFAULT 'Nghỉ phép',
  `tieuDe` varchar(255) NOT NULL,
  `noiDung` text NOT NULL,
  `thoiGian` datetime DEFAULT current_timestamp(),
  `daXem` tinyint(1) DEFAULT 0,
  `trangThai` enum('Chờ','Đã xử lý','Từ chối') DEFAULT NULL,
  `thoiGianXuLy` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `thongbaoadmin` (`maThongBao`, `maNghi`, `requestId`, `soDienThoai`, `maBacSi`, `loai`, `tieuDe`, `noiDung`, `thoiGian`, `daXem`, `trangThai`, `thoiGianXuLy`) VALUES
(1, 3, NULL, NULL, 'BS202511090112882', 'Nghỉ phép', 'Đơn xin nghỉ phép', 'Bác sĩ Nguyễn Thành C xin nghỉ phép vào ngày 17/11/2025 - Ca sáng. Lý do: 0', '2025-11-15 20:22:57', 1, NULL, NULL),
(2, 4, NULL, NULL, 'BS202511090112882', 'Nghỉ phép', 'Đơn xin nghỉ phép', 'Bác sĩ Nguyễn Thành C xin nghỉ phép vào ngày 17/11/2025 - Ca chiều. Lý do: ???', '2025-11-15 20:22:57', 1, NULL, NULL),
(3, 5, NULL, NULL, 'bs1', 'Nghỉ phép', 'Đơn xin nghỉ phép', 'Bác sĩ Trần Văn B xin nghỉ phép vào ngày 19/11/2025 - Ca sáng. Lý do: 0', '2025-11-18 19:52:10', 1, NULL, NULL),
(4, 6, NULL, NULL, 'bs1', 'Nghỉ phép', 'Đơn xin nghỉ phép', 'Bác sĩ Trần Văn B xin nghỉ phép vào ngày 19/11/2025 - Ca chiều. Lý do: ........', '2025-11-18 19:52:10', 1, NULL, NULL),
(5, NULL, 1, NULL, 'bs1', 'Cấp lại mật khẩu', 'Yêu cầu cấp lại mật khẩu', 'Người dùng nguoidung2 yêu cầu cấp lại mật khẩu', '2025-11-19 10:30:56', 1, 'Đã xử lý', '2025-11-19 11:49:57'),
(6, NULL, 2, '0987654323', 'bs1', 'Cấp lại mật khẩu', 'Yêu cầu cấp lại mật khẩu', 'Người dùng nguoidung2 (bacsi) yêu cầu cấp lại mật khẩu', '2025-11-21 00:26:03', 1, 'Đã xử lý', '2025-11-21 00:26:19'),
(7, NULL, NULL, NULL, 'bs1', 'Nghỉ phép', 'Yêu cầu cấp lại mật khẩu', 'Người dùng nguoidung2 yêu cầu cấp lại mật khẩu', '2025-11-21 00:26:03', 1, NULL, NULL);

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
(5, 'bs1', 30, 'Đặt lịch', 'Lịch khám mới', 'Bệnh nhân Trần Văn H đã đặt lịch khám vào ngày 19/11/2025 - Ca chiều', '2025-11-18 18:49:24', 1),
(6, 'bs1', NULL, 'Đặt lịch', 'Cấp lại mật khẩu', 'Mật khẩu mới của bạn là: Eden19112025. Vui lòng đổi mật khẩu sau khi đăng nhập.', '2025-11-19 11:49:57', 1),
(7, 'bs1', NULL, 'Đặt lịch', 'Cấp lại mật khẩu', 'Mật khẩu mới của bạn là: Eden21112025. Vui lòng đổi mật khẩu sau khi đăng nhập.', '2025-11-21 00:26:19', 1),
(8, 'BS20251121031', 31, 'Đặt lịch', 'Lịch khám mới', 'Bệnh nhân Nguyễn Văn A đã đặt lịch khám vào ngày 00/00/0000 - Ca sáng', '2025-11-21 13:12:04', 0),
(9, 'BS20251121022', 32, 'Đặt lịch', 'Lịch khám mới', 'Bệnh nhân Nguyễn Văn A đã đặt lịch khám vào ngày 00/00/0000 - Ca sáng', '2025-11-21 13:48:36', 0),
(10, 'BS20251121022', 33, 'Đặt lịch', 'Lịch khám mới', 'Bệnh nhân Nguyễn Văn A đã đặt lịch khám vào ngày 00/00/0000 - Ca sáng', '2025-11-21 13:49:07', 0),
(11, 'BS20251121022', 34, 'Đặt lịch', 'Lịch khám mới', 'Bệnh nhân Nguyễn Văn A đã đặt lịch khám vào ngày 00/00/0000 - Ca sáng', '2025-11-21 13:56:45', 0),
(12, 'BS20251121022', 35, 'Đặt lịch', 'Lịch khám mới', 'Bệnh nhân Nguyễn Văn A đã đặt lịch khám vào ngày 22/11/2025 - Ca sáng', '2025-11-21 14:12:55', 0),
(13, 'BS20251121022', 36, 'Đặt lịch', 'Lịch khám mới', 'Bệnh nhân Nguyễn Văn A đã đặt lịch khám vào ngày 22/11/2025 - Ca sáng', '2025-11-21 14:13:35', 0),
(14, 'BS202511102320635', 37, 'Đặt lịch', 'Lịch khám mới', 'Bệnh nhân Nguyễn Văn A đã đặt lịch khám vào ngày 21/11/2025 - Ca chiều', '2025-11-21 14:30:28', 0),
(15, 'BS20251121027', 38, 'Đặt lịch', 'Lịch khám mới', 'Bệnh nhân Nguyễn Văn Anh đã đặt lịch khám vào ngày 24/11/2025 - Ca sáng', '2025-11-23 15:07:43', 0),
(16, 'BS20251121027', 39, 'Đặt lịch', 'Lịch khám mới', 'Bệnh nhân Test đã đặt lịch khám vào ngày 24/11/2025 - Ca sáng', '2025-11-23 15:14:29', 0),
(17, 'BS20251121028', 40, 'Đặt lịch', 'Lịch khám mới', 'Bệnh nhân Test đã đặt lịch khám vào ngày 29/11/2025 - Ca sáng', '2025-11-23 15:32:44', 0),
(18, 'BS20251121027', 39, 'Hủy lịch', 'Lịch khám đã hủy', 'Bệnh nhân Test đã hủy lịch khám vào ngày 24/11/2025 - Ca sáng - 07:40 - 08:20', '2025-11-23 16:09:29', 0),
(19, 'BS20251121027', 39, 'Hủy lịch', 'Lịch khám đã hủy', 'Bệnh nhân Test đã hủy lịch khám vào ngày 24/11/2025 - Ca sáng - 07:40 - 08:20. Lý do: Thử', '2025-11-23 16:09:29', 0),
(20, 'BS20251121028', 40, 'Hủy lịch', 'Lịch khám đã hủy', 'Bệnh nhân Test đã hủy lịch khám vào ngày 29/11/2025 - Ca sáng - 07:00 - 07:40', '2025-11-23 16:09:38', 0),
(21, 'BS20251121028', 40, 'Hủy lịch', 'Lịch khám đã hủy', 'Bệnh nhân Test đã hủy lịch khám vào ngày 29/11/2025 - Ca sáng - 07:00 - 07:40. Lý do: Hủy', '2025-11-23 16:09:38', 0),
(22, 'BS20251121027', 41, 'Đặt lịch', 'Lịch khám mới', 'Bệnh nhân Test đã đặt lịch khám vào ngày 24/11/2025 - Ca sáng', '2025-11-23 16:11:19', 0),
(23, 'BS20251121027', 41, 'Hủy lịch', 'Lịch khám đã hủy', 'Bệnh nhân Test đã hủy lịch khám vào ngày 24/11/2025 - Ca sáng - 07:40 - 08:20', '2025-11-23 16:11:38', 0),
(24, 'BS20251121027', 41, 'Hủy lịch', 'Lịch khám đã hủy', 'Bệnh nhân Test đã hủy lịch khám vào ngày 24/11/2025 - Ca sáng - 07:40 - 08:20. Lý do: 1', '2025-11-23 16:11:38', 0),
(25, 'BS20251121027', 42, 'Đặt lịch', 'Lịch khám mới', 'Bệnh nhân Test đã đặt lịch khám vào ngày 24/11/2025 - Ca sáng', '2025-11-23 16:11:59', 0),
(26, 'BS20251121027', 42, 'Hủy lịch', 'Lịch khám đã hủy', 'Bệnh nhân Test đã hủy lịch khám vào ngày 24/11/2025 - Ca sáng - 07:40 - 08:20', '2025-11-23 16:26:45', 0),
(27, 'BS20251121027', 42, 'Hủy lịch', 'Lịch khám đã hủy', 'Bệnh nhân Test đã hủy lịch khám vào ngày 24/11/2025 - Ca sáng - 07:40 - 08:20. Lý do: 2', '2025-11-23 16:26:45', 0),
(28, 'BS20251121027', 43, 'Đặt lịch', 'Lịch khám mới', 'Bệnh nhân Test đã đặt lịch khám vào ngày 23/11/2025 - Ca sáng', '2025-11-23 21:54:48', 0),
(29, 'BS20251121028', 44, 'Đặt lịch', 'Lịch khám mới', 'Bệnh nhân Test đã đặt lịch khám vào ngày 23/11/2025 - Ca chiều', '2025-11-23 21:55:14', 0),
(30, 'bs1', 45, 'Đặt lịch', 'Lịch khám mới', 'Bệnh nhân Test đã đặt lịch khám vào ngày 23/11/2025 - Ca sáng', '2025-11-23 22:00:28', 1),
(31, 'BS20251121027', 43, 'Hủy lịch', 'Lịch khám đã hủy', 'Bệnh nhân Test đã hủy lịch khám vào ngày 23/11/2025 - Ca sáng - 09:00 - 09:40', '2025-11-23 22:00:53', 0),
(32, 'BS20251121027', 43, 'Hủy lịch', 'Lịch khám đã hủy', 'Bệnh nhân Test đã hủy lịch khám vào ngày 23/11/2025 - Ca sáng - 09:00 - 09:40. Lý do: a', '2025-11-23 22:00:53', 0),
(33, 'BS20251121028', 44, 'Hủy lịch', 'Lịch khám đã hủy', 'Bệnh nhân Test đã hủy lịch khám vào ngày 23/11/2025 - Ca chiều - 16:20 - 17:00', '2025-11-23 22:00:59', 0),
(34, 'BS20251121028', 44, 'Hủy lịch', 'Lịch khám đã hủy', 'Bệnh nhân Test đã hủy lịch khám vào ngày 23/11/2025 - Ca chiều - 16:20 - 17:00. Lý do: a', '2025-11-23 22:00:59', 0),
(35, 'bs1', 46, 'Đặt lịch', 'Lịch khám mới', 'Bệnh nhân Test đã đặt lịch khám vào ngày 23/11/2025 - Ca chiều', '2025-11-23 22:01:29', 1);


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

ALTER TABLE `lienhe`
  ADD PRIMARY KEY (`maLienHe`),
  ADD KEY `fk_lienhe_nguoixuly` (`nguoiXuLy`),
  ADD KEY `idx_trangThai` (`trangThai`),
  ADD KEY `idx_thoiGianGui` (`thoiGianGui`);

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
  ADD KEY `idx_thoigian` (`thoiGian`),
  ADD KEY `fk_thongbaoadmin_doimatkhau` (`requestId`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

ALTER TABLE `goikham`
  MODIFY `maGoi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

ALTER TABLE `lichkham`
  MODIFY `maLichKham` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

ALTER TABLE `lienhe`
  MODIFY `maLienHe` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

ALTER TABLE `ngaynghi`
  MODIFY `maNghi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

ALTER TABLE `nguoidung`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

ALTER TABLE `suatkham`
  MODIFY `maSuat` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

ALTER TABLE `thongbaoadmin`
  MODIFY `maThongBao` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

ALTER TABLE `thongbaobenhnhan`
  MODIFY `maThongBao` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `thongbaolichkham`
  MODIFY `maThongBao` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;


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

ALTER TABLE `lienhe`
  ADD CONSTRAINT `fk_lienhe_nguoixuly` FOREIGN KEY (`nguoiXuLy`) REFERENCES `nguoidung` (`id`) ON DELETE SET NULL;

ALTER TABLE `ngaynghi`
  ADD CONSTRAINT `ngaynghi_ibfk_1` FOREIGN KEY (`maBacSi`) REFERENCES `bacsi` (`maBacSi`),
  ADD CONSTRAINT `ngaynghi_ibfk_2` FOREIGN KEY (`maCa`) REFERENCES `calamviec` (`maCa`);

ALTER TABLE `quantrivien`
  ADD CONSTRAINT `quantrivien_ibfk_1` FOREIGN KEY (`nguoiDungId`) REFERENCES `nguoidung` (`id`) ON DELETE CASCADE;

ALTER TABLE `suatkham`
  ADD CONSTRAINT `suatkham_ibfk_1` FOREIGN KEY (`maCa`) REFERENCES `calamviec` (`maCa`);

ALTER TABLE `thongbaoadmin`
  ADD CONSTRAINT `fk_thongbaoadmin_doimatkhau` FOREIGN KEY (`requestId`) REFERENCES `doimatkhau` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `thongbaoadmin_ibfk_1` FOREIGN KEY (`maBacSi`) REFERENCES `bacsi` (`maBacSi`) ON DELETE CASCADE,
  ADD CONSTRAINT `thongbaoadmin_ibfk_2` FOREIGN KEY (`maNghi`) REFERENCES `ngaynghi` (`maNghi`) ON DELETE SET NULL;

ALTER TABLE `thongbaobenhnhan`
  ADD CONSTRAINT `thongbaobenhnhan_ibfk_1` FOREIGN KEY (`maBenhNhan`) REFERENCES `benhnhan` (`maBenhNhan`) ON DELETE CASCADE;

ALTER TABLE `thongbaolichkham`
  ADD CONSTRAINT `thongbao_ibfk_1` FOREIGN KEY (`maBacSi`) REFERENCES `bacsi` (`maBacSi`) ON DELETE CASCADE,
  ADD CONSTRAINT `thongbao_ibfk_2` FOREIGN KEY (`maLichKham`) REFERENCES `lichkham` (`maLichKham`) ON DELETE SET NULL;