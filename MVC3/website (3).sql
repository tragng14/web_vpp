-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th10 15, 2025 lúc 08:35 PM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.2.12

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
-- Cấu trúc bảng cho bảng `news`
--

CREATE TABLE `news` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `status` enum('hiển thị','ẩn') DEFAULT 'hiển thị',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `news`
--

INSERT INTO `news` (`id`, `title`, `content`, `image`, `status`, `created_at`) VALUES
(5, '123', 'vfdghvbjcbvcnbmnb', 'iphone14.png', 'hiển thị', '2025-10-15 19:24:44'),
(6, 'vfdbcb', 'dfvfdcbvcxbcvbn', 'iphone16promax.png', 'hiển thị', '2025-10-15 14:27:53'),
(8, '5', 'VFDBCVBCVB', 'samsung_galaxy.png', '', '2025-10-15 14:34:25'),
(11, 'Giảm giá sốc 80%', 'MUA IP 2 TẶNG 1', 'iphone15.png', '', '2025-10-15 15:37:45'),
(12, 'SIÊU GIẢM GIÁ ', 'GIẢM GIÁ IP CÒN 100000', 'iphone17.png', '', '2025-10-15 15:47:26');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `order_code` varchar(50) NOT NULL,
  `total_amount` decimal(15,2) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `user_email` varchar(100) DEFAULT NULL,
  `receiver` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `transaction_info` text DEFAULT NULL,
  `status` enum('pending','approved','shipping','completed','cancelled') DEFAULT 'pending',
  `cancelled_by` varchar(20) DEFAULT NULL,
  `discount_code` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `order_code`, `total_amount`, `created_at`, `user_email`, `receiver`, `phone`, `address`, `transaction_info`, `status`, `cancelled_by`, `discount_code`) VALUES
(73, 0, 'HD1761998754', 320000.00, '2025-11-01 13:05:54', 'baochanbon@gmail.com', 'Trang admin', '0879119493', 'HN', 'dathanhtoan', 'cancelled', NULL, NULL),
(74, 0, 'HD1761999943', 600000.00, '2025-11-01 13:25:43', 'baochanbon@gmail.com', 'Trang admin', '0879119493', 'HN1', 'chothanhtoan', 'pending', NULL, NULL),
(75, 0, 'HD1761999961', 600000.00, '2025-11-01 13:26:01', 'baochanbon@gmail.com', 'Trang admin', 'yty', 'HN', 'chothanhtoan', 'pending', NULL, NULL),
(76, 0, 'HD1761999995', 550000.00, '2025-11-01 13:26:35', 'baochanbon@gmail.com', 'Trang admin', '0879119493', 'HN', 'chothanhtoan', 'pending', NULL, NULL),
(77, 0, 'HD1762000133', 550000.00, '2025-11-01 13:28:53', 'baochanbon@gmail.com', 'Trang admin', '0879119493', 'HN', 'chothanhtoan', 'pending', NULL, NULL),
(78, 0, 'HD1762000164', 320000.00, '2025-11-01 13:29:24', 'baochanbon@gmail.com', 'Trang admin', '0879119493', 'HN', 'chothanhtoan', 'pending', NULL, NULL),
(79, 0, 'DH1762001243452', 0.00, '2025-11-01 13:47:23', 'Array', 'Trang admin', '0879119493', 'HN', 'pending', 'pending', NULL, NULL),
(80, 0, 'HD1762001458', 550000.00, '2025-11-01 13:50:58', 'baochanbon@gmail.com', 'Trang admin', '0879119493', 'HN', 'chothanhtoan', 'pending', NULL, NULL),
(81, 0, 'HD1762002454', 550000.00, '2025-11-01 14:07:34', 'baochanbon@gmail.com', 'Trang admin', '0879119493', 'HN', 'dathanhtoan', 'pending', NULL, NULL),
(82, 0, 'HD1762002487', 320000.00, '2025-11-01 14:08:07', 'baochanbon@gmail.com', 'Trang admin', '0879119493', 'HN', 'dathanhtoan', 'pending', NULL, NULL),
(83, 0, 'HD1762002531', 320000.00, '2025-11-01 14:08:51', 'baochanbon@gmail.com', 'Trang admin', '0879119493', 'HN', 'dathanhtoan', 'pending', NULL, NULL),
(84, 0, 'HD1762002538', 320000.00, '2025-11-01 14:08:58', 'baochanbon@gmail.com', 'Trang admin', '0879119493', 'HN', 'chothanhtoan', 'pending', NULL, NULL),
(85, 0, 'HD1762003660', 126000.00, '2025-11-01 14:27:40', 'baochanbon@gmail.com', 'Trang admin', '0879119493', 'HN', 'chothanhtoan', 'pending', NULL, NULL),
(86, 0, 'HD1762003792', 126000.00, '2025-11-01 14:29:52', 'baochanbon@gmail.com', 'Trang admin', '0879119493', 'HN', 'chothanhtoan', 'pending', NULL, NULL),
(87, 0, 'HD1762003829', 500000.00, '2025-11-01 14:30:29', 'baochanbon@gmail.com', 'Trang admin', '0879119493', 'HN', 'chothanhtoan', 'pending', NULL, NULL),
(88, 0, 'HD1762003858', 440000.00, '2025-11-01 14:30:58', 'baochanbon@gmail.com', 'Trang admin', '0879119493', 'HN', 'chothanhtoan', 'pending', NULL, NULL),
(89, 0, 'HD1762003914', 140800.00, '2025-11-01 14:31:54', 'baochanbon@gmail.com', 'Trang admin', '0879119493', 'HN', 'chothanhtoan', 'pending', NULL, NULL),
(90, 0, 'HD1762003941', 76000.00, '2025-11-01 14:32:21', 'baochanbon@gmail.com', 'Trang admin', '0879119493', 'HN', 'chothanhtoan', 'pending', NULL, NULL),
(91, 0, 'HD1762003970', 256000.00, '2025-11-01 14:32:50', 'baochanbon@gmail.com', 'Trang admin', '0879119493', 'HN', 'chothanhtoan', 'pending', NULL, NULL),
(92, 0, 'HD1762004447', 450000.00, '2025-11-01 14:40:47', 'baochanbon@gmail.com', 'Trang admin', '0879119493', 'HN', 'dathanhtoan', 'pending', NULL, NULL),
(93, 0, 'HD1762004619', 176000.00, '2025-11-01 14:43:39', 'baochanbon@gmail.com', 'Trang admin', '0879119493', 'HN', 'chothanhtoan', 'pending', NULL, NULL),
(94, 0, 'HD1762004887', 176000.00, '2025-11-01 14:48:07', 'baochanbon@gmail.com', 'Trang admin', '0879119493', 'HN', 'chothanhtoan', 'pending', NULL, NULL),
(95, 0, 'HD1762004943', 126000.00, '2025-11-01 14:49:03', 'baochanbon@gmail.com', 'Trang admin', '0879119493', 'HN', 'dathanhtoan', 'pending', NULL, NULL),
(96, 0, 'HD1762005247', 176000.00, '2025-11-01 14:54:07', 'baochanbon@gmail.com', 'Trang admin', '0879119493', 'HN', 'chothanhtoan', 'pending', NULL, NULL),
(97, 0, 'HD1762005423', 126000.00, '2025-11-01 14:57:03', 'baochanbon@gmail.com', 'Trang admin', '0879119493', 'HN', 'dathanhtoan', 'pending', NULL, NULL),
(98, 0, 'HD1762006112', 450000.00, '2025-11-01 15:08:32', 'baochanbon@gmail.com', 'Trang admin', '0879119493', 'HN', 'dathanhtoan', 'pending', NULL, NULL),
(99, 0, 'HD1762006312', 171000.00, '2025-11-01 15:11:52', 'baochanbon@gmail.com', 'Trang admin', '0879119493', 'HN', 'dathanhtoan', 'pending', NULL, NULL),
(100, 0, 'HD1762007029', 450000.00, '2025-11-01 15:23:49', 'baochanbon@gmail.com', 'Trang admin', '0879119493', 'HN', 'dathanhtoan', 'pending', NULL, NULL),
(101, 0, 'HD1762007483', 450000.00, '2025-11-01 15:31:23', 'baochanbon@gmail.com', 'Trang admin', '0879119493', 'HN', 'chothanhtoan', 'pending', NULL, NULL),
(102, 0, 'HD1762341256', 925000.00, '2025-11-05 12:14:16', 'baochanbon@gmail.com', 'Trang admin', '0879119493', 'HN', 'dathanhtoan', 'pending', NULL, NULL),
(103, 0, 'HD1762341274', 540000.00, '2025-11-05 12:14:34', 'baochanbon@gmail.com', 'Trang admin', '0879119493', 'HN', 'chothanhtoan', 'pending', NULL, NULL),
(104, 0, 'HD1762341299', 540000.00, '2025-11-05 12:14:59', 'baochanbon@gmail.com', 'Trang admin', '0879119493', 'HN', 'chothanhtoan', 'pending', NULL, NULL),
(105, 0, 'HD1762341899', 380000.00, '2025-11-05 12:24:59', 'baochanbon@gmail.com', 'Trang admin', '0879119493', 'HN', ' Đã thanh toán', 'pending', NULL, NULL),
(106, 0, 'HD1762342354', 175000.00, '2025-11-05 12:32:34', 'baochanbon@gmail.com', 'Trang admin', '0879119493', 'HN', ' Đã thanh toán', 'pending', NULL, NULL),
(107, 0, 'HD1762342486', 205000.00, '2025-11-05 12:34:46', 'baochanbon@gmail.com', 'Trang admin', '0879119493', 'HN', 'dathanhtoan', 'pending', NULL, NULL),
(108, 0, 'HD1762522534', 600000.00, '2025-11-07 14:35:34', 'baochanbon@gmail.com', 'Trang admin', '0879119493', 'HN', 'dathanhtoan', 'pending', NULL, NULL),
(109, 0, 'HD1762523680', 600000.00, '2025-11-07 14:54:40', 'baochanbon@gmail.com', 'Trang admin', '0879119493', 'HN', 'dathanhtoan', 'pending', NULL, NULL),
(110, 0, 'HD1762524012', 500000.00, '2025-11-07 15:00:12', 'baochanbon@gmail.com', 'Trang admin', '0879119493', 'HN', 'dathanhtoan', 'pending', NULL, NULL),
(111, 0, 'HD1762524262', 500000.00, '2025-11-07 15:04:22', 'baochanbon@gmail.com', 'Trang admin', '0879119493', 'HN', 'dathanhtoan', 'pending', NULL, NULL),
(112, 0, 'HD1762525152', 600000.00, '2025-11-07 15:19:12', 'baochanbon@gmail.com', 'Trang admin', '0879119493', 'HN', 'chothanhtoan', 'pending', NULL, NULL),
(113, 0, 'HD1762525298', 200000.00, '2025-11-07 15:21:38', 'baochanbon@gmail.com', 'Trang admin', '0879119493', 'HN', 'chothanhtoan', 'pending', NULL, NULL),
(114, 0, 'HD1762525614', 500000.00, '2025-11-07 15:26:54', 'baochanbon@gmail.com', 'Trang admin', '0879119493', 'HN', 'dathanhtoan', 'pending', NULL, NULL),
(115, 0, 'HD1762526035', 5000000.00, '2025-11-07 15:33:55', 'baochanbon@gmail.com', 'Trang admin', '0879119493', 'HN', 'dathanhtoan', 'pending', NULL, NULL),
(116, 0, 'HD1762526528', 1500000.00, '2025-11-07 15:42:08', 'baochanbon@gmail.com', 'Trang admin', '0879119493', 'HN', 'chothanhtoan', 'approved', NULL, NULL),
(117, 0, 'HD1762527350', 1000000.00, '2025-11-07 15:55:50', 'baochanbon@gmail.com', 'Trang admin', '0879119493', 'HN', 'dathanhtoan', 'pending', NULL, NULL),
(118, 0, 'HD1762527457', 1000000.00, '2025-11-07 15:57:37', 'baochanbon@gmail.com', 'Trang admin', '0879119493', 'HN', 'dathanhtoan', 'pending', NULL, NULL),
(119, 0, 'HD1762530248', 1500000.00, '2025-11-07 16:44:08', 'baochanbon@gmail.com', 'Trang admin', '0879119493', 'HN', 'dathanhtoan', 'pending', NULL, NULL),
(120, 0, 'HD1762531749', 720000.00, '2025-11-07 17:09:09', 'baochanbon@gmail.com', 'Trang admin', '0879119493', 'HN', 'dathanhtoan', 'pending', NULL, NULL),
(121, 0, 'HD1762603115', 1340000.00, '2025-11-08 12:58:35', 'baochanbon@gmail.com', 'Trang admin', '0879119493', 'HN', 'dathanhtoan', 'cancelled', 'admin', NULL),
(122, 0, 'HD1762603347', 940000.00, '2025-11-08 13:02:27', 'baochanbon@gmail.com', 'Trang admin', '0879119493', 'HN', 'dathanhtoan', 'completed', NULL, NULL),
(123, 0, 'HD1762608919', 770000.00, '2025-11-08 14:35:19', 'baochanbon@gmail.com', 'Trang admin', '0879119493', 'HN', 'dathanhtoan', 'cancelled', NULL, NULL),
(124, 0, 'HD1762609179', 270000.00, '2025-11-08 14:39:39', 'baochanbon@gmail.com', 'Trang admin', '0879119493', 'HN', 'dathanhtoan', 'cancelled', NULL, NULL),
(125, 0, 'HD1762609362', 270000.00, '2025-11-08 14:42:42', 'baochanbon@gmail.com', 'Trang admin', '0879119493', 'HN', 'dathanhtoan', 'cancelled', NULL, NULL),
(126, 0, 'HD1762609605', 270000.00, '2025-11-08 14:46:45', 'baochanbon@gmail.com', 'Trang admin', '0879119493', 'HN', 'dathanhtoan', 'cancelled', NULL, NULL),
(127, 0, 'HD1762609836', 270000.00, '2025-11-08 14:50:36', 'baochanbon@gmail.com', 'Trang admin', '0879119493', 'HN', 'dathanhtoan', 'completed', NULL, NULL),
(128, 0, 'HD1762610882', 2430000.00, '2025-11-08 15:08:02', 'baochanbon@gmail.com', 'Trang admin', '0879119493', 'HN', 'dathanhtoan', 'cancelled', NULL, NULL),
(129, 0, 'HD1762610908', 2430000.00, '2025-11-08 15:08:28', 'baochanbon@gmail.com', 'Trang admin', '0879119493', 'HN', 'dathanhtoan', 'cancelled', NULL, NULL),
(130, 0, 'HD1762611028', 270000.00, '2025-11-08 15:10:28', 'baochanbon@gmail.com', 'Trang admin', '0879119493', 'HN', 'dathanhtoan', 'cancelled', NULL, NULL),
(131, 0, 'HD1762611555', 270000.00, '2025-11-08 15:19:15', 'baochanbon@gmail.com', 'Trang admin', '0879119493', 'HN', 'dathanhtoan', 'completed', NULL, NULL),
(132, 0, 'HD1763129781', 650000.00, '2025-11-14 15:16:21', 'baochanbon@gmail.com', 'Trang user', '0879119493', 'Nam Tu Liem, Ha Noi', 'chothanhtoan', 'cancelled', 'admin', NULL),
(133, 0, 'HD1763130904', 650000.00, '2025-11-14 15:35:04', 'baochanbon@gmail.com', 'Trang user', '0879119493', 'Nam Tu Liem, Ha Noi', 'dathanhtoan', 'approved', NULL, NULL),
(134, 0, 'HD1763131286', 650000.00, '2025-11-14 15:41:26', 'baochanbon@gmail.com', 'Trang user', '0879119493', 'Nam Tu Liem, Ha Noi', 'dathanhtoan', 'approved', NULL, NULL),
(135, 0, 'HD1763131724', 650000.00, '2025-11-14 15:48:44', 'baochanbon@gmail.com', 'Trang user', '0879119493', 'Nam Tu Liem, Ha Noi', 'dathanhtoan', 'approved', NULL, NULL),
(136, 0, 'HD1763133036', 920000.00, '2025-11-14 16:10:36', 'baochanbon@gmail.com', 'Trang user', '0879119493', 'Nam Tu Liem, Ha Noi', 'dathanhtoan', 'approved', NULL, NULL),
(137, 0, 'HD1763134409', 920000.00, '2025-11-14 16:33:29', 'baochanbon@gmail.com', 'Trang user', '0879119493', 'Nam Tu Liem, Ha Noi', 'chothanhtoan', 'cancelled', NULL, NULL),
(138, 0, 'HD1763135111', 650000.00, '2025-11-14 16:45:11', 'baochanbon@gmail.com', 'Trang user', '0879119493', 'HEM', 'dathanhtoan', 'approved', NULL, NULL),
(139, 0, 'HD1763135431', 650000.00, '2025-11-14 16:50:31', 'baochanbon@gmail.com', 'Trang user', '0879119493', 'HEM', 'dathanhtoan', 'approved', NULL, 'GG100K'),
(140, 0, 'HD1763136061', 670000.00, '2025-11-14 17:01:01', 'baochanbon@gmail.com', 'Trang user', '0879119493', 'Nam Tu Liem, Ha Noi', 'dathanhtoan', 'cancelled', NULL, 'GG100K'),
(141, 0, 'HD1763136248', 270000.00, '2025-11-14 17:04:08', 'baochanbon@gmail.com', 'Trang user', '0879119493', 'Nam Tu Liem, Ha Noi', 'dathanhtoan', 'shipping', NULL, NULL),
(142, 0, 'HD1763136276', 270000.00, '2025-11-14 17:04:36', 'baochanbon@gmail.com', 'Trang user', '0879119493', 'HEM', 'dathanhtoan', 'cancelled', NULL, NULL),
(143, 0, 'HD1763136378', 270000.00, '2025-11-14 17:06:18', 'baochanbon@gmail.com', 'Trang user', '0879119493', 'Nam Tu Liem, Ha Noi', 'dathanhtoan', 'completed', NULL, NULL),
(144, 0, 'HD1763136724', 270000.00, '2025-11-14 17:12:04', 'baochanbon@gmail.com', 'Trang user', '0879119493', 'KHONG', 'chothanhtoan', 'cancelled', NULL, NULL),
(145, 0, 'HD1763136850', 270000.00, '2025-11-14 17:14:10', 'baochanbon@gmail.com', 'Trang user', '0879119493', 'HEM', 'chothanhtoan', 'cancelled', 'admin', NULL),
(146, 0, 'HD1763140826', 650000.00, '2025-11-14 18:20:26', 'baochanbon@gmail.com', 'Trang user', '0879119493', 'HEM', 'chothanhtoan', 'cancelled', 'user', 'GG100K'),
(147, 0, 'HD1763140871', 750000.00, '2025-11-14 18:21:11', 'baochanbon@gmail.com', 'Trang user', '0879119493', 'Nam Tu Liem, Ha Noi', 'chothanhtoan', 'cancelled', NULL, NULL),
(148, 0, 'HD1763141994', 1500000.00, '2025-11-14 18:39:54', 'baochanbon@gmail.com', 'Trang user', '0879119493', 'KHONG', 'chothanhtoan', 'cancelled', 'admin', NULL),
(149, 0, 'HD1763142678', 650000.00, '2025-11-14 18:51:18', 'baochanbon@gmail.com', 'Trang user', '0879119493', 'Nam Tu Liem, Ha Noi', 'chothanhtoan', 'pending', NULL, 'gg100k'),
(150, 0, 'HD1763142697', 650000.00, '2025-11-14 18:51:37', 'baochanbon@gmail.com', 'Trang user', '0879119493', 'Nam Tu Liem, Ha Noi', 'dathanhtoan', 'pending', NULL, 'gg100k'),
(151, 0, 'HD1763227687', 920000.00, '2025-11-16 00:28:07', 'bon@gmail.com', 'Bon user', '0879119493', 'Nam Tu Liem, Ha Noi', '2025-11-15 18:28:07', 'cancelled', 'user', 'gg100k'),
(152, 0, 'HD1763227735', 650000.00, '2025-11-16 00:28:55', 'bon@gmail.com', 'Bon user', '0879119493', 'Nam Tu Liem, Ha Noi', '2025-11-15 18:28:55', 'pending', NULL, 'GG100K'),
(153, 0, 'HD1763227901', 920000.00, '2025-11-16 00:31:41', 'bon@gmail.com', 'Bon user', '0879119493', 'KHONG', 'dathanhtoan', 'cancelled', 'admin', 'GG100K'),
(154, 0, 'HD1763229133', 2130000.00, '2025-11-16 00:52:13', 'bon@gmail.com', 'Bon user', '0879119493', 'KHONG', 'dathanhtoan', 'cancelled', 'user', 'GG100K'),
(155, 0, 'HD1763229995', 300000.00, '2025-11-16 01:06:35', 'bon@gmail.com', 'Bon user', '0879119493', 'Nam Tu Liem, Ha Noi', '2025-11-15 19:06:35', 'cancelled', 'user', NULL),
(156, 0, 'HD1763230573', 750000.00, '2025-11-16 01:16:13', 'bon@gmail.com', 'Bon user', '0879119493', 'Nam Tu Liem, Ha Noi', '2025-11-15 19:16:13', 'cancelled', 'user', NULL),
(157, 0, 'HD1763230603', 750000.00, '2025-11-16 01:16:43', 'bon@gmail.com', 'Bon user', '0879119493', 'Nam Tu Liem, Ha Noi', '2025-11-15 19:16:43', 'cancelled', 'admin', NULL),
(158, 0, 'HD1763230806', 950000.00, '2025-11-16 01:20:06', 'bon@gmail.com', 'Bon user', '0879119493', 'Nam Tu Liem, Ha Noi', 'dathanhtoan', 'cancelled', 'user', 'GG100K'),
(159, 0, 'HD1763231328', 650000.00, '2025-11-16 01:28:48', 'bon@gmail.com', 'Bon user', '0879119493', 'KHONG', 'dathanhtoan', 'cancelled', 'admin', 'GG100K');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `order_details`
--

CREATE TABLE `order_details` (
  `id` int(11) NOT NULL,
  `order_id` varchar(20) NOT NULL,
  `product_id` varchar(50) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(15,2) NOT NULL,
  `sale_price` decimal(15,2) NOT NULL,
  `total` decimal(15,2) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `product_type` varchar(100) DEFAULT NULL,
  `product_name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `order_details`
--

INSERT INTO `order_details` (`id`, `order_id`, `product_id`, `quantity`, `price`, `sale_price`, `total`, `image`, `product_type`, `product_name`) VALUES
(35, '32', 'Iphone16', 1, 30000000.00, 27000000.00, 27000000.00, 'iphone17_1.png', '', 'Iphone 16 '),
(36, '33', 'Iphone15', 2, 25000000.00, 23750000.00, 47500000.00, 'iphone14.png', '', 'Iphone 15 Promax'),
(37, '34', 'Iphone15', 3, 25000000.00, 23750000.00, 71250000.00, 'iphone14.png', '', 'Iphone 15 Promax'),
(38, '35', 'Iphone15', 1, 25000000.00, 23750000.00, 23750000.00, 'iphone14.png', '', 'Iphone 15 Promax'),
(39, '36', 'Iphone15', 1, 25000000.00, 23750000.00, 23750000.00, 'iphone14.png', '', 'Iphone 15 Promax'),
(40, '36', 'Iphone17', 1, 30000000.00, 27000000.00, 27000000.00, 'onway.png', '', 'Iphone 17'),
(41, '37', 'Iphone16', 1, 30000000.00, 27000000.00, 27000000.00, 'iphone17_1.png', '', 'Iphone 16 '),
(42, '38', 'Iphone15', 1, 25000000.00, 23750000.00, 23750000.00, 'iphone14.png', '', 'Iphone 15 Promax'),
(43, '39', 'Iphone15', 1, 25000000.00, 23750000.00, 23750000.00, 'iphone14.png', '', 'Iphone 15 Promax'),
(44, '40', 'Iphone15', 1, 25000000.00, 23750000.00, 23750000.00, 'iphone14.png', '', 'Iphone 15 Promax'),
(45, '41', 'Iphone15', 1, 25000000.00, 23750000.00, 23750000.00, 'iphone14.png', '', 'Iphone 15 Promax'),
(46, '43', 'Iphone15', 1, 25000000.00, 23750000.00, 23750000.00, 'iphone14.png', NULL, 'Iphone 15 Promax'),
(47, '44', 'Iphone15', 1, 25000000.00, 23750000.00, 23750000.00, 'iphone14.png', NULL, 'Iphone 15 Promax'),
(48, '45', 'Iphone15', 1, 25000000.00, 23750000.00, 23750000.00, 'iphone14.png', NULL, 'Iphone 15 Promax'),
(49, '46', 'Iphone16', 1, 30000000.00, 27000000.00, 27000000.00, 'iphone17_1.png', NULL, 'Iphone 16 '),
(50, '47', 'Iphone16', 1, 30000000.00, 27000000.00, 27000000.00, 'iphone17_1.png', NULL, 'Iphone 16 '),
(51, '48', 'Iphone15', 1, 25000000.00, 23750000.00, 23750000.00, 'iphone14.png', NULL, 'Iphone 15 Promax'),
(52, '49', 'Iphone15', 1, 25000000.00, 23750000.00, 23750000.00, 'iphone14.png', NULL, 'Iphone 15 Promax'),
(53, '50', 'Iphone16', 2, 30000000.00, 27000000.00, 54000000.00, 'iphone17_1.png', NULL, 'Iphone 16 '),
(54, '50', 'Iphone15', 1, 25000000.00, 23750000.00, 23750000.00, 'iphone14.png', NULL, 'Iphone 15 Promax'),
(55, '51', 'Iphone17', 1, 30000000.00, 27000000.00, 27000000.00, 'onway.png', NULL, 'Iphone 17'),
(56, '51', 'Iphone16', 1, 30000000.00, 27000000.00, 27000000.00, 'iphone17_1.png', NULL, 'Iphone 16 '),
(57, '52', 'Iphone16', 1, 30000000.00, 27000000.00, 27000000.00, 'iphone17_1.png', NULL, 'Iphone 16 '),
(58, '53', 'Iphone16', 2, 30000000.00, 27000000.00, 54000000.00, 'iphone17_1.png', NULL, 'Iphone 16 '),
(59, '53', 'Iphone17', 1, 30000000.00, 27000000.00, 27000000.00, 'onway.png', NULL, 'Iphone 17'),
(60, '54', 'Iphone15', 10, 25000000.00, 23750000.00, 237500000.00, 'iphone14.png', NULL, 'Iphone 15 Promax'),
(61, '55', 'Iphone15', 2, 25000000.00, 23750000.00, 47500000.00, 'iphone14.png', NULL, 'Iphone 15 Promax'),
(62, '56', 'Iphone16', 1, 30000000.00, 27000000.00, 27000000.00, 'iphone17_1.png', NULL, 'Iphone 16 '),
(63, '57', 'Iphone15', 1, 25000000.00, 23750000.00, 23750000.00, 'iphone14.png', NULL, 'Iphone 15 Promax'),
(64, '58', 'Iphone15', 1, 25000000.00, 23750000.00, 23750000.00, 'iphone14.png', NULL, 'Iphone 15 Promax'),
(65, '59', 'Iphone17', 1, 30000000.00, 27000000.00, 27000000.00, 'onway.png', NULL, 'Iphone 17'),
(66, '60', 'Iphone15', 1, 25000000.00, 23750000.00, 23750000.00, 'iphone14.png', NULL, 'Iphone 15 Promax'),
(67, '61', 'Iphone15', 1, 25000000.00, 23750000.00, 23750000.00, 'iphone14.png', NULL, 'Iphone 15 Promax'),
(68, '62', 'IPR', 1, 4000.00, 4000.00, 4000.00, '', NULL, 'IPRONG'),
(69, '63', 'IP14', 1, 7000.00, 7000.00, 7000.00, 'iphone14.png', NULL, 'Iphone 14'),
(70, '63', 'IPR', 1, 4000.00, 4000.00, 4000.00, '', NULL, 'IPRONG'),
(71, '64', 'IP14', 1, 7000.00, 7000.00, 7000.00, 'iphone14.png', NULL, 'Iphone 14'),
(72, '64', 'IPR', 2, 4000.00, 4000.00, 8000.00, '', NULL, 'IPRONG'),
(73, '65', 'IPR', 1, 4000.00, 4000.00, 4000.00, '', NULL, 'IPRONG'),
(74, '66', 'IP14', 1, 220000.00, 220000.00, 220000.00, '', NULL, 'ip14pro'),
(75, '67', 'ip17', 2, 600000.00, 600000.00, 1200000.00, 'iphone17_1.png', NULL, 'ip17prp'),
(76, '67', 'IP14', 1, 220000.00, 220000.00, 220000.00, 'iphone14.png', NULL, 'ip14pro'),
(77, '68', 'ip17', 1, 600000.00, 600000.00, 600000.00, 'iphone17_1.png', NULL, 'ip17prp'),
(78, '69', 'ip17', 1, 600000.00, 600000.00, 600000.00, 'iphone17_1.png', NULL, 'ip17prp'),
(79, '70', 'SSgalaxy', 1, 400000.00, 400000.00, 400000.00, 'samsung_galaxy.png', NULL, 'samsung'),
(80, '71', 'SSgalaxy', 1, 400000.00, 400000.00, 400000.00, 'samsung_galaxy.png', NULL, 'samsung'),
(81, '72', 'ip17', 1, 600000.00, 550000.00, 550000.00, 'iphone17_1.png', NULL, 'ip17prp'),
(82, '73', 'SSgalaxy', 1, 400000.00, 320000.00, 320000.00, 'samsung_galaxy.png', NULL, 'samsung'),
(83, '74', 'ip17', 1, 600000.00, 600000.00, 600000.00, 'iphone17_1.png', NULL, 'ip17prp'),
(84, '75', 'ip17', 1, 600000.00, 600000.00, 600000.00, 'iphone17_1.png', NULL, 'ip17prp'),
(85, '76', 'ip17', 1, 600000.00, 550000.00, 550000.00, 'iphone17_1.png', NULL, 'ip17prp'),
(86, '77', 'ip17', 1, 600000.00, 550000.00, 550000.00, 'iphone17_1.png', NULL, 'ip17prp'),
(87, '78', 'SSgalaxy', 1, 400000.00, 320000.00, 320000.00, 'samsung_galaxy.png', NULL, 'samsung'),
(88, '80', 'ip17', 1, 600000.00, 550000.00, 550000.00, 'iphone17_1.png', NULL, 'ip17prp'),
(89, '81', 'ip17', 1, 600000.00, 550000.00, 550000.00, 'iphone17_1.png', NULL, 'ip17prp'),
(90, '82', 'SSgalaxy', 1, 400000.00, 320000.00, 320000.00, 'samsung_galaxy.png', NULL, 'samsung'),
(91, '83', 'SSgalaxy', 1, 400000.00, 320000.00, 320000.00, 'samsung_galaxy.png', NULL, 'samsung'),
(92, '84', 'SSgalaxy', 1, 400000.00, 320000.00, 320000.00, 'samsung_galaxy.png', NULL, 'samsung'),
(93, '85', 'IP14', 1, 220000.00, 176000.00, 176000.00, 'iphone14.png', NULL, 'ip14pro'),
(94, '86', 'IP14', 1, 220000.00, 176000.00, 176000.00, 'iphone14.png', NULL, 'ip14pro'),
(95, '87', 'ip17', 1, 600000.00, 550000.00, 550000.00, 'iphone17_1.png', NULL, 'ip17prp'),
(96, '88', 'ip17', 1, 600000.00, 550000.00, 550000.00, 'iphone17_1.png', NULL, 'ip17prp'),
(97, '89', 'IP14', 1, 220000.00, 176000.00, 176000.00, 'iphone14.png', NULL, 'ip14pro'),
(98, '90', 'IP14', 1, 220000.00, 176000.00, 176000.00, 'iphone14.png', NULL, 'ip14pro'),
(99, '91', 'SSgalaxy', 1, 400000.00, 320000.00, 320000.00, 'samsung_galaxy.png', NULL, 'samsung'),
(100, '92', 'ip17', 1, 600000.00, 550000.00, 550000.00, 'iphone17_1.png', NULL, 'ip17prp'),
(101, '93', 'IP14', 1, 220000.00, 176000.00, 176000.00, 'iphone14.png', NULL, 'ip14pro'),
(102, '94', 'IP14', 1, 220000.00, 176000.00, 176000.00, 'iphone14.png', NULL, 'ip14pro'),
(103, '95', 'IP14', 1, 220000.00, 176000.00, 176000.00, 'iphone14.png', NULL, 'ip14pro'),
(104, '96', 'IP14', 1, 220000.00, 176000.00, 176000.00, 'iphone14.png', NULL, 'ip14pro'),
(105, '97', 'IP14', 1, 220000.00, 176000.00, 176000.00, 'iphone14.png', NULL, 'ip14pro'),
(106, '98', 'ip17', 1, 600000.00, 550000.00, 550000.00, 'iphone17_1.png', NULL, 'ip17prp'),
(107, '99', 'IP14', 1, 220000.00, 176000.00, 176000.00, 'iphone14.png', NULL, 'ip14pro'),
(108, '100', 'ip17', 1, 600000.00, 550000.00, 550000.00, 'iphone17_1.png', NULL, 'ip17prp'),
(109, '101', 'ip17', 1, 600000.00, 550000.00, 550000.00, 'iphone17_1.png', NULL, 'ip17prp'),
(110, '102', 'ip17', 1, 600000.00, 570000.00, 570000.00, 'iphone17_1.png', NULL, 'ip17prp'),
(111, '102', 'SSgalaxy', 1, 400000.00, 385000.00, 385000.00, 'samsung_galaxy.png', NULL, 'samsung'),
(112, '103', 'ip17', 1, 600000.00, 570000.00, 570000.00, 'iphone17_1.png', NULL, 'ip17prp'),
(113, '104', 'ip17', 1, 600000.00, 570000.00, 570000.00, 'iphone17_1.png', NULL, 'ip17prp'),
(114, '105', 'IP14', 2, 220000.00, 205000.00, 410000.00, 'iphone14.png', NULL, 'ip14pro'),
(115, '106', 'IP14', 1, 220000.00, 205000.00, 205000.00, 'iphone14.png', NULL, 'ip14pro'),
(116, '107', 'IP14', 1, 220000.00, 205000.00, 205000.00, 'iphone14.png', NULL, 'ip14pro'),
(117, '108', 'ip17MAX', 3, 250000.00, 200000.00, 600000.00, 'iphone17.png', NULL, 'ip17MAX'),
(118, '109', 'SSgalaxy', 2, 400000.00, 300000.00, 600000.00, 'samsung_galaxy.png', NULL, 'samsung'),
(119, '110', 'IP14', 1, 500000.00, 500000.00, 500000.00, 'iphone14.png', NULL, 'ip14pro'),
(120, '111', 'IP14', 1, 500000.00, 500000.00, 500000.00, 'iphone14.png', NULL, 'ip14pro'),
(121, '112', 'ip17', 1, 600000.00, 600000.00, 600000.00, 'iphone17_1.png', NULL, 'ip17prp'),
(122, '113', 'ip17MAX', 1, 250000.00, 200000.00, 200000.00, 'iphone17.png', NULL, 'ip17MAX'),
(123, '114', 'IP14', 1, 500000.00, 500000.00, 500000.00, 'iphone14.png', NULL, 'ip14pro'),
(124, '115', 'IP14', 10, 500000.00, 500000.00, 5000000.00, 'iphone14.png', NULL, 'ip14pro'),
(125, '117', 'IP14', 2, 500000.00, 500000.00, 1000000.00, 'iphone14.png', NULL, 'ip14pro'),
(126, '118', 'IP14', 2, 500000.00, 500000.00, 1000000.00, 'iphone14.png', NULL, 'ip14pro'),
(127, '119', 'IP14', 5, 320000.00, 320000.00, 1600000.00, 'iphone14.png', NULL, 'ip14pro'),
(128, '120', 'ip17', 1, 820000.00, 720000.00, 720000.00, 'iphone17_1.png', NULL, 'ip17MAX'),
(129, '121', 'ip17', 2, 820000.00, 720000.00, 1440000.00, 'iphone17_1.png', NULL, 'ip17MAX'),
(130, '122', 'ip17', 1, 820000.00, 720000.00, 720000.00, 'iphone17_1.png', NULL, 'ip17MAX'),
(131, '122', 'IP14', 1, 320000.00, 270000.00, 270000.00, 'iphone14.png', NULL, 'ip14pro'),
(132, '123', 'ip17', 1, 820000.00, 770000.00, 770000.00, 'iphone17_1.png', NULL, 'ip17MAX'),
(133, '124', 'IP14', 1, 320000.00, 270000.00, 270000.00, 'iphone14.png', NULL, 'ip14pro'),
(134, '125', 'IP14', 1, 320000.00, 270000.00, 270000.00, 'iphone14.png', NULL, 'ip14pro'),
(135, '126', 'IP14', 1, 320000.00, 270000.00, 270000.00, 'iphone14.png', NULL, 'ip14pro'),
(136, '127', 'IP14', 1, 320000.00, 270000.00, 270000.00, 'iphone14.png', NULL, 'ip14pro'),
(137, '128', 'IP14', 9, 320000.00, 270000.00, 2430000.00, 'iphone14.png', NULL, 'ip14pro'),
(138, '129', 'IP14', 9, 320000.00, 270000.00, 2430000.00, 'iphone14.png', NULL, 'ip14pro'),
(139, '130', 'IP14', 1, 320000.00, 270000.00, 270000.00, 'iphone14.png', NULL, 'ip14pro'),
(140, '131', 'IP14', 1, 320000.00, 270000.00, 270000.00, 'iphone14.png', NULL, 'ip14pro'),
(141, '132', 'SS1116', 1, 800000.00, 750000.00, 750000.00, 'iphone16.png', NULL, 'IP16'),
(142, '133', 'SS1116', 1, 800000.00, 750000.00, 750000.00, 'iphone16.png', NULL, 'IP16'),
(143, '134', 'SS1116', 1, 800000.00, 750000.00, 750000.00, 'iphone16.png', NULL, 'IP16'),
(144, '135', 'SS1116', 1, 800000.00, 750000.00, 750000.00, 'iphone16.png', NULL, 'IP16'),
(145, '136', 'IP14', 1, 320000.00, 270000.00, 270000.00, 'iphone14.png', NULL, 'ip14pro'),
(146, '136', 'SS1116', 1, 800000.00, 750000.00, 750000.00, 'iphone16.png', NULL, 'IP16'),
(147, '137', 'IP14', 1, 320000.00, 270000.00, 270000.00, 'iphone14.png', NULL, 'ip14pro'),
(148, '137', 'SS1116', 1, 800000.00, 750000.00, 750000.00, 'iphone16.png', NULL, 'IP16'),
(149, '138', 'SS1116', 1, 800000.00, 750000.00, 750000.00, 'iphone16.png', NULL, 'IP16'),
(150, '139', 'SS1116', 1, 800000.00, 750000.00, 750000.00, 'iphone16.png', NULL, 'IP16'),
(151, '140', 'ip17', 1, 820000.00, 770000.00, 770000.00, 'iphone17_1.png', NULL, 'ip17MAX'),
(152, '141', 'IP14', 1, 320000.00, 270000.00, 270000.00, 'iphone14.png', NULL, 'ip14pro'),
(153, '142', 'IP14', 1, 320000.00, 270000.00, 270000.00, 'iphone14.png', NULL, 'ip14pro'),
(154, '143', 'IP14', 1, 320000.00, 270000.00, 270000.00, 'iphone14.png', NULL, 'ip14pro'),
(155, '144', 'IP14', 1, 320000.00, 270000.00, 270000.00, 'iphone14.png', NULL, 'ip14pro'),
(156, '145', 'IP14', 1, 320000.00, 270000.00, 270000.00, 'iphone14.png', NULL, 'ip14pro'),
(157, '146', 'SS1116', 1, 800000.00, 750000.00, 750000.00, 'iphone16.png', NULL, 'IP16'),
(158, '147', 'SS1116', 1, 800000.00, 750000.00, 750000.00, 'iphone16.png', NULL, 'IP16'),
(159, '148', 'SS1116', 2, 800000.00, 750000.00, 1500000.00, 'iphone16.png', NULL, 'IP16'),
(160, '149', 'SS1116', 1, 800000.00, 750000.00, 750000.00, 'iphone16.png', NULL, 'IP16'),
(161, '150', 'SS1116', 1, 800000.00, 750000.00, 750000.00, 'iphone16.png', NULL, 'IP16'),
(162, '151', 'IP14', 1, 320000.00, 270000.00, 270000.00, 'iphone14.png', NULL, 'ip14pro'),
(163, '151', 'SS1116', 1, 800000.00, 750000.00, 750000.00, 'iphone16.png', NULL, 'IP16'),
(164, '152', 'SS1116', 1, 800000.00, 750000.00, 750000.00, 'iphone16.png', NULL, 'IP16'),
(165, '153', 'IP14', 1, 320000.00, 270000.00, 270000.00, 'iphone14.png', NULL, 'ip14pro'),
(166, '153', 'SS1116', 1, 800000.00, 750000.00, 750000.00, 'iphone16.png', NULL, 'IP16'),
(167, '154', 'NK22', 2, 300000.00, 300000.00, 600000.00, 'defaut.png', NULL, 'NKasmvfd'),
(168, '154', 'SS1116', 2, 800000.00, 750000.00, 1500000.00, 'iphone16.png', NULL, 'IP16'),
(169, '154', 'TOP', 1, 230000.00, 130000.00, 130000.00, 'Array', NULL, 'Táo chính xanh'),
(170, '155', 'NK22', 1, 300000.00, 300000.00, 300000.00, 'defaut.png', NULL, 'NKasmvfd'),
(171, '156', 'SS1116', 1, 800000.00, 750000.00, 750000.00, 'iphone16.png', NULL, 'IP16'),
(172, '157', 'SS1116', 1, 800000.00, 750000.00, 750000.00, 'iphone16.png', NULL, 'IP16'),
(173, '158', 'SS1116', 1, 800000.00, 750000.00, 750000.00, 'iphone16.png', NULL, 'IP16'),
(174, '158', 'NK22', 1, 300000.00, 300000.00, 300000.00, 'defaut.png', NULL, 'NKasmvfd'),
(175, '159', 'SS1116', 1, 800000.00, 750000.00, 750000.00, 'iphone16.png', NULL, 'IP16');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `promo_codes`
--

CREATE TABLE `promo_codes` (
  `id` int(10) UNSIGNED NOT NULL,
  `code` varchar(50) NOT NULL,
  `type` enum('percent','amount','fixed') NOT NULL DEFAULT 'percent',
  `value` decimal(10,2) NOT NULL DEFAULT 0.00,
  `min_total` decimal(10,2) NOT NULL DEFAULT 0.00,
  `usage_limit` int(10) UNSIGNED DEFAULT NULL,
  `used_count` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `status` enum('active','inactive','deleted') NOT NULL DEFAULT 'active',
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `promo_codes`
--

INSERT INTO `promo_codes` (`id`, `code`, `type`, `value`, `min_total`, `usage_limit`, `used_count`, `start_date`, `end_date`, `status`, `created_at`) VALUES
(9, 'GG20', 'percent', 20.00, 100000.00, 2, 2, '2025-11-04 00:00:00', '2025-11-06 00:00:00', 'deleted', '2025-11-01 00:00:00'),
(10, 'GG50K', 'amount', 50000.00, 50000.00, 6, 6, '2025-11-01 00:00:00', '2025-11-19 00:00:00', 'active', '2025-11-01 00:00:00'),
(11, 'GG100K', 'amount', 100000.00, 200000.00, 15, 11, '2025-11-01 00:00:00', '2025-11-18 00:00:00', 'active', '2025-11-01 00:00:00'),
(12, 'GG5K', 'amount', 5000.00, 100000.00, 5, 1, '2025-11-01 00:00:00', '2025-11-04 00:00:00', 'deleted', '2025-11-01 00:00:00'),
(13, 'GG30', 'amount', 30000.00, 100000.00, 2, 2, '2025-11-01 00:00:00', '2025-11-05 00:00:00', 'deleted', '2025-11-01 00:00:00'),
(14, 'GG15K', 'amount', 15000.00, 100000.00, 2, 1, '2025-11-05 00:00:00', '2025-11-06 00:00:00', 'deleted', '2025-11-05 00:00:00'),
(16, 'GGSS', 'percent', 100.00, 500000.00, 2, 1, '2025-11-05 00:00:00', '2025-11-06 00:00:00', 'deleted', '2025-11-05 00:00:00');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `promo_product`
--

CREATE TABLE `promo_product` (
  `id` int(10) UNSIGNED NOT NULL,
  `masp` varchar(50) NOT NULL,
  `promo_code` varchar(50) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `promo_product`
--

INSERT INTO `promo_product` (`id`, `masp`, `promo_code`, `created_at`) VALUES
(48, 'IP14', 'GG50K', '2025-11-08 19:01:34'),
(49, 'ip17', 'GG50K', '2025-11-08 19:01:44'),
(50, 'SS1116', 'GG50K', '2025-11-14 21:10:51'),
(51, 'TOP', 'GG100K', '2025-11-16 00:50:27');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `tblloaisp`
--

CREATE TABLE `tblloaisp` (
  `maLoaiSP` varchar(20) NOT NULL,
  `tenLoaiSP` varchar(50) NOT NULL,
  `moTaLoaiSP` varchar(200) NOT NULL,
  `ngayTao` datetime DEFAULT current_timestamp(),
  `ngaySua` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `tblloaisp`
--

INSERT INTO `tblloaisp` (`maLoaiSP`, `tenLoaiSP`, `moTaLoaiSP`, `ngayTao`, `ngaySua`) VALUES
('Iphone', 'IPHONE', 'Màn hình màu hồng, vàng, xanh', '2025-11-14 21:00:57', '2025-11-14 21:06:13'),
('NK', 'NOkia', 'khong', '2025-11-16 00:48:53', '2025-11-16 00:48:53'),
('SS', 'Samsung', 'khong', '2025-11-14 21:00:57', '2025-11-14 21:00:57'),
('táo', 'Táo xanh', 'dep', '2025-11-16 00:49:02', '2025-11-16 00:49:02');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `tblsanpham`
--

CREATE TABLE `tblsanpham` (
  `maLoaiSP` varchar(20) NOT NULL,
  `masp` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `tensp` varchar(20) NOT NULL,
  `hinhanh` varchar(50) NOT NULL,
  `soluong` int(11) NOT NULL,
  `soluongnhap` int(11) DEFAULT 0,
  `giaNhap` int(11) NOT NULL,
  `giaXuat` int(11) NOT NULL,
  `khuyenmai` int(11) NOT NULL,
  `mota` varchar(200) NOT NULL,
  `createDate` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `tblsanpham`
--

INSERT INTO `tblsanpham` (`maLoaiSP`, `masp`, `tensp`, `hinhanh`, `soluong`, `soluongnhap`, `giaNhap`, `giaXuat`, `khuyenmai`, `mota`, `createDate`) VALUES
('Iphone', 'IP14', 'ip14pro', 'iphone14.png', 0, 18, 120000, 320000, 0, '', '2025-11-07'),
('Iphone', 'ip17', 'ip17MAX', 'iphone17_1.png', 0, 5, 520000, 820000, 0, '', '2025-11-07'),
('NK', 'NK22', 'NKasmvfd', 'defaut.png', 1, 6, 200000, 300000, 0, '', '2025-11-16'),
('SS', 'SS1116', 'IP16', 'iphone16.png', 1, 11, 400000, 800000, 0, '', '2025-11-14'),
('táo', 'TOP', 'Táo chính xanh', 'Array', 26, 28, 100000, 230000, 0, '', '2025-11-16');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `tbluser`
--

CREATE TABLE `tbluser` (
  `user_id` int(11) NOT NULL,
  `fullname` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `is_verified` tinyint(1) DEFAULT 0,
  `verification_token` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `role` enum('admin','user') NOT NULL DEFAULT 'user',
  `status` enum('Hoạt động','Tạm ngưng') DEFAULT 'Hoạt động',
  `is_deleted` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `tbluser`
--

INSERT INTO `tbluser` (`user_id`, `fullname`, `email`, `password`, `is_verified`, `verification_token`, `created_at`, `role`, `status`, `is_deleted`) VALUES
(24, 'Trang admin', 'trag@gmail.com', '$2y$10$EJFuPx/HA5o5QvtbubzKfufbnp9laK.8km10o7d3ZLhUoEt/Q1Obq', 0, 2, '2025-10-15 20:19:19', 'admin', '', 1),
(27, 'Trang user', 'baochanbon@gmail.com', '$2y$10$4kVvqnZZ5UAWHxEFgfGfxea7LYWgKYg/q3dp5MPT.0uHUIH1kPCzu', 0, 0, '2025-11-13 16:44:13', 'user', 'Hoạt động', 0),
(28, 'BAO admin', 'bao@gmail.com', '$2y$10$o4Drz.uyE8aeBM3fFiW3nOWn.bjVywX9AlpC.rfpZoaX/eHjhIYVK', 0, 0, '2025-11-13 17:09:39', 'admin', 'Hoạt động', 0),
(29, 'Bon user', 'bon@gmail.com', '$2y$10$mhKszyStFskEauSwCe.R9u4OPP0OZiqcvOE8XNi7T2QCvNvz1Uhz6', 0, 0, '2025-11-16 00:22:46', 'user', 'Hoạt động', 1);

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
(3, 'ip17', 'BAO2', 'baochanbon@gmail.com', '45', 4, 1, '2025-11-08', NULL);

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `news`
--
ALTER TABLE `news`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_code` (`order_code`);

--
-- Chỉ mục cho bảng `order_details`
--
ALTER TABLE `order_details`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `promo_codes`
--
ALTER TABLE `promo_codes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Chỉ mục cho bảng `promo_product`
--
ALTER TABLE `promo_product`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `masp_promo_unique` (`masp`,`promo_code`),
  ADD KEY `fk_promo` (`promo_code`);

--
-- Chỉ mục cho bảng `tblloaisp`
--
ALTER TABLE `tblloaisp`
  ADD PRIMARY KEY (`maLoaiSP`);

--
-- Chỉ mục cho bảng `tblsanpham`
--
ALTER TABLE `tblsanpham`
  ADD PRIMARY KEY (`masp`);

--
-- Chỉ mục cho bảng `tbluser`
--
ALTER TABLE `tbluser`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Chỉ mục cho bảng `tbl_danhgia`
--
ALTER TABLE `tbl_danhgia`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `news`
--
ALTER TABLE `news`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT cho bảng `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=160;

--
-- AUTO_INCREMENT cho bảng `order_details`
--
ALTER TABLE `order_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=176;

--
-- AUTO_INCREMENT cho bảng `promo_codes`
--
ALTER TABLE `promo_codes`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT cho bảng `promo_product`
--
ALTER TABLE `promo_product`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT cho bảng `tbluser`
--
ALTER TABLE `tbluser`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT cho bảng `tbl_danhgia`
--
ALTER TABLE `tbl_danhgia`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `promo_product`
--
ALTER TABLE `promo_product`
  ADD CONSTRAINT `fk_product` FOREIGN KEY (`masp`) REFERENCES `tblsanpham` (`masp`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_promo` FOREIGN KEY (`promo_code`) REFERENCES `promo_codes` (`code`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
