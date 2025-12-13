-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th12 11, 2025 lúc 12:27 PM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `website`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `tblsanpham`
--

CREATE TABLE `tblsanpham` (
  `maLoaiSP` varchar(20) NOT NULL,
  `masp` varchar(50) NOT NULL,
  `tensp` varchar(50) NOT NULL,
  `hinhanh` varchar(50) NOT NULL,
  `soluong` int(11) NOT NULL,
  `soluongnhap` int(11) DEFAULT 0,
  `giaNhap` int(11) NOT NULL,
  `giaXuat` int(11) NOT NULL,
  `khuyenmai` int(11) NOT NULL,
  `mota` varchar(200) NOT NULL,
  `createDate` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `tblsanpham`
--

INSERT INTO `tblsanpham` (`maLoaiSP`, `masp`, `tensp`, `hinhanh`, `soluong`, `soluongnhap`, `giaNhap`, `giaXuat`, `khuyenmai`, `mota`, `createDate`) VALUES
('BB02', 'BC01', 'Bút chì gỗ 2B ( Hộp 10 chiếc )', 'but chi go 2b.png', 19, 30, 20000, 40000, 0, 'Thân gỗ lục giác\r\nKích thước: Φ7,6 x 178mm (đường kính x chiều dài bút)\r\nMàu sắc: Bạc\r\nQuy cách: 12 chiếc/ hộp', '2025-12-02'),
('BB02', 'BC02', 'Bút chì Kim Sliver', 'but chi kim.png', 34, 35, 8000, 20000, 0, 'Nhãn hiệu: Hồng Hà\r\nNgòi chì: 0.7 mm\r\nChất liệu: Kim loại ( nhôm, thép) , nhựa & Than chì\r\nKích thước: Φ 8 x 140mm ( đường kính x chiều dài bút)\r\nMàu sắc: Ánh bạc', '2025-12-02'),
('BB02', 'BC03', 'Bút chì gỗ ABC - 2B ', 'but chi vang.png', 59, 60, 900, 3000, 0, 'Nhãn hiệu: Hồng Hà\r\nThân gỗ lục giác\r\nKích thước: Φ7,6 x 178mm ( đường kính x chiều dài bút)\r\nMàu sắc: Vàng', '2025-12-02'),
('BB02', 'BC04', 'Bút chì gỗ có tẩy 2B', 'but chi go co tay.png', 19, 20, 1000, 3500, 0, 'Kích thước (D):190 (mm)\r\nRuột chì : 2B\r\nĐường kính chỉ: Ø 2,4 mm\r\nChất liệu: Gỗ Bạch Dương mềm\r\nThân bút 6 cạnh\r\n', '2025-12-02'),
('BB02', 'BC05', 'Bút chì kim bấm ngòi 0.5mm', 'but chi kim bam.png', 0, 10, 2000, 8000, 0, 'Nhãn hiệu: Hồng Hà\r\nNgòi chì HB: 0.5 mm\r\nChất liệu: nhựa ABS, PP & Than chì\r\nKích thước: Φ 11 x 150mm ( đường kính x chiều dài bút)', '2025-12-02'),
('DCHT', 'BCC', 'Bảng chữ cái và số tiếng Anh Hồng Hà ', 'bang.png', 29, 30, 4000, 10000, 0, 'Định lượng: 250g/m2\r\nKích thước: 380 * 520 (mm)\r\nĐóng gói: 01 sản phẩm / Túi nilong\r\nXuất xứ: Việt Nam\r\nSản xuất: Hồng Hà ', '2025-12-02'),
('BDB', 'BDB01', 'Bút dạ bảng Hồng Hà', 'but da 1.png', 16, 16, 2000, 6500, 0, 'Kích thước: 147 * Ø17mm', '2025-12-02'),
('BDB', 'BDB02', 'Bút dạ bảng 2 đầu', 'but da 2.png', 29, 50, 3000, 7000, 0, 'Kích thước: 141 x Ø15.5mm', '2025-12-02'),
('BDB', 'BDB03', 'Bút dạ bảng 2 đầu to', 'but da 3.png', 20, 20, 3500, 9000, 0, 'Kích thước: 138 x Ø 20.7 mm', '2025-12-02'),
('BB01', 'BG01', 'Bút gel BG01', 'but gel.png', 27, 90, 3000, 7000, 0, 'Kích thước: 151,4x8,8 (mm)\r\nĐầu ngòi: 0,5mm\r\nMàu mực: Xanh, Đen, Đỏ, Tím\r\nThương hiệu: Văn phòng phẩm Hồng Hà', '2025-11-16'),
('BB01', 'BG02', 'Bút gel xóa được', 'but gel xoa duoc.png', 15, 15, 6000, 15000, 0, 'Kích thước: 155,5x8,8 (mm)\r\nĐầu ngòi: 0.5mm\r\nMàu mực: Xanh, Tím, Đen\r\nMực gel có thẻ xóa khi ma sát tạo nhiệt độ 65°C\r\nTẩy chuyên dụng được trang bị ở đuôi bút', '2025-12-02'),
('BB01', 'BG03', 'Bút bi Hồng Hà', 'bút bi hh.png', 100, 100, 1500, 11000, 0, 'Vật liệu: Nhựa ABS\r\nKích thước: 144x9,5mm\r\nNgòi: 0,5mm\r\nMực: Xanh, đen, đỏ\r\nThương hiệu: Văn phòng phẩm Hồng Hà', '2025-12-02'),
('BB01', 'BG04', 'Ruột bút Gel ngòi 0.5mm ( Hộp 10 chiếc ) ', 'ruot but 10c.png', 30, 30, 15000, 50000, 0, 'Ngòi: 0.5mm\r\nMàu mực: Xanh, Đen,Tím\r\nĐóng gói: 10 chiếc/hộp\r\nThương hiệu: Văn phòng phẩm Hồng Hà\r\nXuất xứ: Việt Nam', '2025-12-02'),
('BB01', 'BG05', 'Ruột bút gel xóa được ngòi 0.5mm', 'ruot but gel xoa duoc.png', 40, 40, 6000, 12000, 0, 'Ngòi: 0.5mm\r\nMàu mực: Xanh/Tím\r\nĐóng gói: 2 chiếc/túi \r\nThương hiệu: Văn phòng phẩm Hồng Hà\r\nXuất xứ: Việt Nam', '2025-12-02'),
('BB01', 'BG06', 'Bút gel hoạt hình', 'but gel 2.png', 15, 15, 3500, 6500, 0, 'Kích thước: 153,6x8,8 (mm)\r\nĐầu ngòi: 0,5mm\r\nMàu mực: Xanh, Đen, Đỏ, Tím\r\nThương hiệu: Văn phòng phẩm Hồng Hà', '2025-12-02'),
('BHL', 'BHL01', 'Bút dạ quang ', 'but hl vang.png', 30, 30, 1000, 3500, 0, 'Chất liệu: nhựa ABS\r\nKích thước: 100x8mm\r\nHình dạng ngòi: vát xéo\r\nBề rộng nét viết: Đầu to 4mm\r\nThương hiệu: Văn phòng phẩm Hồng Hà\r\nXuất xứ: Việt Nam', '2025-12-02'),
('BHL', 'BHL02', 'Bút dạ quang đầu nhỏ', 'but hl xanh.png', 10, 10, 3000, 9000, 0, 'Chiều dài viết tối đa:  300 m/ 2 đầu\r\nChất liệu nhựa ABS\r\nĐầu bút và ruột bút bằng Polyester, dạng vát xéo và đầu đạn', '2025-12-02'),
('BK', 'BK01', 'Bút máy văn phòng cao cấp Trường Sơn', 'but ky 1.png', 14, 15, 150000, 550000, 0, 'Ngòi bút 0.5 mm cao cấp \r\nChất liệu: Hợp kim sang trọng\r\nMàu sắc: Đen\r\nQuy cách đóng gói: 1 cây/hộp \r\nXuất xứ: Việt Nam.', '2025-12-02'),
('BK', 'BK02', 'Bút dạ bi cao cấp Hồng Hà', 'but ky 2.png', 13, 13, 100000, 360000, 0, 'Phù hợp việc ghi chép và ký những văn bản quan trọng.\r\nThương hiệu: Văn phòng phẩm Hồng Hà\r\nXuất xứ: Việt Nam', '2025-12-02'),
('BLTUI', 'BL001', 'Ba lô chống gù Siêu nhân', 'balo.png', 19, 20, 90000, 190000, 0, 'Thương hiệu: Hồng Hà\r\nChất liệu: vải polyester 600PU\r\nKích thước: 35x31x19 cm\r\nKết cấu: 01 ngăn chính, 01 ngăn phụ + khóa kéo\r\nMàu sắc: Xanh dương\r\nTrọng lượng: 0.65 kg', '2025-12-02'),
('BLTUI', 'BL002', 'Ba lô mầm non', 'balo2.png', 47, 50, 150000, 360000, 0, 'Chất liệu: vải polyester 900PU\r\nKích thước: 30x25x10 cm\r\nKết cấu: 01 ngăn chính, 01 ngăn phụ + khóa kéo\r\nTrọng lượng: 0.26 kg', '2025-12-02'),
('BM22', 'BM01', 'Bút máy Hồng Hà', 'but may.png', 30, 30, 20000, 40000, 0, 'Chất liệu: Kim loại\r\nNgòi bút: Ngòi mài chuyên dụng\r\nChu vi piston: Φ3.4mm\r\nĐóng gói: 01 chiếc/hộp nhỏ, 12 chiếc/hộp trưng bày, 480 chiếc/thùng', '2025-12-02'),
('BM22', 'BM02', 'Ngòi bút máy nét trơn', 'ngoi but.png', 20, 20, 1000, 3000, 0, 'Ngòi nét trơn nét 0.38mm dùng để viết  chữ, phù hợp cho giáo viên tiểu học, các bạn học sinh cấp I, mẫu giáo lớn.\r\nChất liệu thép không gỉ.\r\nĐầu ngòi đính hạt Iridium', '2025-12-02'),
('BB01', 'BS2', 'Bút sáp vặn Minions 12 màu', 'Array', 10, 10, 32000, 65000, 0, 'Kích thước bút: Ø9,8x157,7 (±2mm)\r\nKích thước sáp màu: Ø5,6x131 (±2mm) \r\nKhối lượng: 9,3g\r\nThương hiệu: Văn phòng phẩm Hồng Hà', '2025-12-02'),
('DCHT', 'BV1', 'Bọc sách, vở nylon Hồng Hà dạng cuộn 170x240mm', 'bocvo.png', 15, 15, 6000, 15000, 0, 'Chất liệu: Nhựa CPP \r\nKích thước bọc: 170 x 240 mm\r\nMàu sắc: Trong suốt\r\nThương hiệu: Hồng Hà\r\nSố lượng 10 tờ bọc/ cuộn', '2025-12-02'),
('BB03', 'BX01', 'Bút Xóa Hồng hà', 'but xoa.png', 20, 20, 5000, 12000, 0, 'Kích thước thân bút: 25 x19 x 103 mm (D x R x C)\r\nMàu sắc thân bút: Xanh\r\nDung tích: 12 ml', '2025-12-02'),
('BB03', 'BX02', 'Bút xóa 7ml', 'but xoa 7ml.png', 5, 15, 4000, 13000, 0, 'Kích thước thân bút: 21 x17 x 128 mm (D x R x C)\r\nMàu sắc thân bút: Xanh, Hồng Vàng\r\nDung tích: 7 ml', '2025-12-02'),
('DCHT', 'CP1', 'Compa Hồng Hà ', 'compa.png', 30, 30, 5000, 10000, 0, 'Chất liệu: Kim Loại\r\nĐóng gói: 1 chiếc/ túi OPP\r\nSản xuất tại: Công ty Cổ phần Văn phòng phẩm Hồng Hà\r\nXuất xứ: Việt Nam', '2025-12-02'),
('DCHT', 'EKE1', 'Bộ Eke 4 sản phẩm', 'eke.png', 10, 10, 6000, 13000, 0, 'Bộ sản phẩm gồm: thước thẳng 15cm - eke 45 độ - eke 60 độ và thước đo góc 180 độ\r\nVạch thước in rõ ràng, chính xác.\r\nMàu sắc: Trong suốt\r\nSản xuất: Hồng Hà\r\nXuất xứ: Việt Nam\r\nQuy cách: 1 bộ/ túi', '2025-12-02'),
('DCHT', 'G24', 'Gọt bút chì phi thuyền', 'goi.png', 60, 60, 1200, 3500, 0, 'Nhãn hiệu: Hồng Hà\r\nChất liệu: Nhựa PS và thép không gỉ.', '2025-12-02'),
('DCHT', 'GKT01', 'Giấy kiểm tra kẻ ngang', 'giayktra.png', 50, 50, 7000, 12000, 0, 'Dòng kẻ:        Kẻ ngang (7mm)\r\nKích thước:    175 x 250 (±2mm)\r\nĐịnh lượng:    70gsm\r\nĐộ trắng:        90 - 92% ISO\r\nPhân loại:       20 tờ đôi + 10 tờ đơn\r\nĐóng gói:        100 túi/thùng\r\nXuất xứ vở', '2025-12-02'),
('DCHT', 'GKT02', 'Giấy kiểm tra 4 ô ly dành cho học sinh cấp I ', 'giayktra2.png', 30, 30, 6000, 10000, 0, 'Loại dòng kẻ: 4 ô ly vuông (2,5x2,5mm)\r\nKích thước: 170x240 (±2mm)\r\nSố tờ: 15 tờ đôi\r\nĐịnh lượng: 100gsm\r\nĐộ trắng: 90 - 92% ISO\r\nThương hiệu: Văn phòng phẩm Hồng Hà\r\nXuất xứ: Việt Nam', '2025-12-02'),
('DCHT', 'HB1', 'Hộp bút Kin Kin', 'hop but.png', 13, 13, 6000, 10000, 0, '', '2025-12-02'),
('DCHT', 'M11', 'Mực lọ Hồng Hà 60 ml', 'muc.png', 10, 10, 3000, 7000, 0, 'Thương hiệu: Hồng Hà\r\nMàu mực: Xanh Đen \r\nDung tích: 60 ± 5 ml\r\nKhối lượng: 70 ± 2 g', '2025-12-02'),
('SSTT ', 'Sach1', 'Truyện thiếu nhi Tiếng Việt ', 'sach.png', 5, 5, 30000, 90000, 0, 'Kích thước:  19.5 x 24 cm\r\nSố trang: 44 trang/ quyển\r\nNgôn ngữ: Tiếng Việt\r\nNhà xuất bản: Thanh Niên', '2025-12-02'),
('SBC', 'SBC01', 'Sổ bìa còng Hồng Hà A5 Business 200 trang', 'so 2.png', 23, 23, 25000, 100000, 0, 'Kích thước: A5 148*210 (±2mm) có khuy cài\r\nSố trang: 200 trang\r\nĐịnh lượng: 80 g/m²\r\nĐộ trắng: 76 - 78%ISO', '2025-12-02'),
('SBD', 'SBD01', 'Sổ tổng hợp 600 trang B5 Hồng Hà', 'so 1.png', 19, 20, 35000, 95000, 0, 'Kẻ ngang\r\nKích thước: B5\r\nSố trang: 600 trang\r\nĐịnh lượng: 70gsm\r\nĐộ trắng: 95%ISO\r\nĐóng gói: 1 quyển/ túi OPP, 16 quyển/thùng', '2025-12-02'),
('SBD', 'SBD02', 'Sổ da Hồng hà 200 trang', 'so 3.png', 15, 15, 19000, 50000, 0, 'Kích thước:  A5 148*210 (±2mm) có khuy cài\r\nSố trang:       200 trang\r\nĐịnh lượng:  80 g/m²\r\nĐộ trắng:      76 - 78%ISO', '2025-12-02'),
('SDA00', 'SoGA', 'Sổ Giáo án A4 Hồng Hà 120 trang', 'so.png', 100, 100, 10000, 35000, 0, 'Kích thước:  210 * 297 (±2mm)\r\nSố trang:       120  trang\r\nĐịnh lượng:  58 g/m²\r\nĐộ trắng:      95% ISO', '2025-12-02'),
('DCHT', 'T111', 'Tẩy trắng', 'tay.png', 100, 100, 300, 3000, 0, 'Chất liệu: Cao su\r\nKích thước: 43 x 16 x 11 mm (DxRxC)\r\nMàu sắc: Trắng\r\nĐối tượng sử dụng: Nhiều đối tượng', '2025-12-02'),
('DCHT', 'TK1', 'Thước kẻ 30cm', 'thuocke.png', 30, 30, 1000, 4000, 0, 'Kích thước:  300 mm (±2mm) \r\nMàu sắc:      Trong suốt\r\nĐóng gói:     1 chiếc/ túi \r\nSản xuất tại: Công ty Cổ phần Văn phòng phẩm Hồng Hà\r\nXuất xứ:       Việt Nam', '2025-12-02'),
('TK11', 'TK123', 'Trình ký Plastic Hồng Hà A4 S7', 'trinh ky.png', 30, 30, 12000, 30000, 0, 'Trình ký được tích hợp thêm tính năng thước kẻ, thuận tiện cho kẻ vẽ, đo đạc\r\n\r\nLưu trữ khoảng 100 tờ A4\r\n\r\nKích thước: 317x228 mm', '2025-12-02'),
('DCHT', 'TK2', 'Thước kẻ 16cm', 'thuoc ke 2.png', 30, 30, 500, 3000, 0, 'Thương hiệu: Hồng Hà\r\nKích thước: 165 x 20 x 1.7 mm (±2mm)\r\nMàu sắc: Trong suốt\r\nĐóng gói: 1 chiếc/ túi ', '2025-12-02'),
('VV0123', 'Vo1', 'Vở kẻ ngang 72 trang Study Basic Hồng Hà', 'vo1.png', 15, 15, 4000, 1000, 0, 'Loại dòng kẻ: Kẻ ngang\r\nKích thước: 180x252 (±2mm)\r\nSố trang: 72 trang cả bìa\r\nĐịnh lượng: 70 gsm\r\nĐộ trắng: 76 - 78% ISO\r\nĐóng gói: 5 quyển/lốc, 120 quyển/thùng\r\nSản xuất tại: Công ty Cổ phần Văn phò', '2025-12-02'),
('VV0123', 'Vo2', 'Vở 5 ô ly 96 trang Hồng Hà Class Yummy', 'vo2.png', 60, 60, 3000, 6000, 0, 'Số trang: 96 trang cả bìa và tờ lót\r\nRuột: 5 ô ly vuông\r\nKích thước: 156x205 (±2mm)\r\nĐịnh lượng: 70gsm\r\nĐộ trắng: 90 - 92% ISO\r\nThương hiệu: Văn phòng phẩm Hồng Hà\r\nĐóng gói: 10 quyển/lốc, 200 quyển/t', '2025-12-02'),
('VV0123', 'Vo3', 'Vở 4 ô ly 96 trang Hồng Hà Class ArkDuck', 'vo3.png', 60, 60, 5000, 10000, 0, 'Dòng Kẻ: 4 ô ly vuông (2 x 2)mm \r\nKích Thước: 156 x 205 (±2mm)\r\nSố Trang: 96 trang cả bìa và tờ lót\r\nĐịnh Lượng: 70g/m2\r\nĐộ Trắng: 90 - 92%ISO\r\nĐóng Gói: 10 quyển/lốc; 200 quyển/thùng', '2025-12-02'),
('HP00', 'VV01', 'Vở vẽ A4 20 tờ ', 'vo ve.png', 30, 30, 9000, 17000, 0, 'Kích thước:  290 * 210 (±2mm)\r\nSố tờ:           20 tờ\r\nĐịnh lượng:  100 g/m²\r\nĐộ trắng:      78% ISO', '2025-12-02'),
('HP00', 'VV02', 'Tập tô màu Hồng Hà Oringa cho bé', 'to mau.png', 20, 20, 6000, 15000, 0, 'Số trang: 24 trang\r\nKích thước: 20 x 28 cm\r\nBìa: Giấy Couche, định lượng: 230g/m², in 4 màu, cán bóng.\r\nRuột: Định lượng: 80g/m² , độ trắng 90 - 92% ISO', '2025-12-02');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `tblsanpham`
--
ALTER TABLE `tblsanpham`
  ADD PRIMARY KEY (`masp`),
  ADD KEY `idx_masp` (`masp`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
