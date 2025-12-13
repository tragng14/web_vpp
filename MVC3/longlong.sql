-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th12 11, 2025 lúc 10:25 PM
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
-- Cấu trúc bảng cho bảng `tbl_danhgia`
--

CREATE TABLE `tbl_danhgia` (
  `id` int(11) NOT NULL,
  `masp` varchar(50) NOT NULL,
  `tenNguoiDung` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `noidung` text NOT NULL,
  `sao` int(11) DEFAULT NULL CHECK (`sao` between 1 and 5),
  `trangthai` tinyint(1) DEFAULT 0,
  `ngayDang` date DEFAULT curdate(),
  `traloi` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `tbl_danhgia`
--

INSERT INTO `tbl_danhgia` (`id`, `masp`, `tenNguoiDung`, `email`, `noidung`, `sao`, `trangthai`, `ngayDang`, `traloi`) VALUES
(2, 'ip17', 'BAO3', 'baochanbon@gmail.com', '565', 1, 1, '2025-11-08', 'ok'),
(3, 'ip17', 'BAO2', 'baochanbon@gmail.com', '45', 4, 1, '2025-11-08', NULL),
(4, 'BUT01_TL', 'huy88gh', 'builong111104@gmail.com', 'ygh8h', 5, 1, '2025-11-16', NULL),
(5, 'BUT01_TL', 'ưeqwe', 'builong111104@gmail.com', 'qưeqwe', 5, 1, '2025-11-16', NULL),
(6, 'BC01', 'long\r\n', 'builong111104@gmail.com', 'ádasdas', 5, 1, '2025-12-04', NULL),
(7, 'BC01', 'long\r\n', 'builong111104@gmail.com', 'qưqewqewqw', 5, 0, '2025-12-04', NULL),
(8, 'BC01', 'long\r\n', 'builong111104@gmail.com', '1234567890', 5, 0, '2025-12-04', NULL),
(9, 'BC01', 'long\r\n', 'builong111104@gmail.com', 'love', 4, 1, '2025-12-04', NULL),
(10, 'BX02', 'Trang user', 'baochanbon@gmail.com', 'ok z', 5, 1, '2025-12-05', NULL),
(11, 'BC01', 'long', 'builong111104@gmail.com', 'ádasdasdafvdfafadf', 1, 1, '2025-12-07', NULL),
(12, 'BC01', 'long', 'builong111104@gmail.com', '121314', 5, 1, '2025-12-12', NULL),
(13, 'BC01', 'long', 'builong111104@gmail.com', 'haha', 5, 1, '2025-12-12', NULL);

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `tbl_danhgia`
--
ALTER TABLE `tbl_danhgia`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `tbl_danhgia`
--
ALTER TABLE `tbl_danhgia`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
