-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th12 07, 2025 lúc 03:37 PM
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
-- Cấu trúc bảng cho bảng `banner_images`
--

CREATE TABLE `banner_images` (
  `img_id` int(11) NOT NULL,
  `banner_id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `link` varchar(255) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `banner_images`
--

INSERT INTO `banner_images` (`img_id`, `banner_id`, `image_path`, `link`, `sort_order`, `created_at`) VALUES
(7, 3, '1764779785_9482_hinh-anh-gau-truc-lon-ngo-nghinh_121845367_1.jpg', 'https://maple-indiscrete-subglobularly.ngrok-free.dev/MVC3//ProductFront/', 1, '2025-12-03 23:36:25'),
(8, 3, '1764779785_8673_istockphoto-467584493-612x612.jpg', 'https://maple-indiscrete-subglobularly.ngrok-free.dev/MVC3//Home/detail/BC01', 2, '2025-12-03 23:36:25'),
(9, 4, '1764780701_5619_slider_3.webp', 'https://maple-indiscrete-subglobularly.ngrok-free.dev/MVC3//ProductFront/', 1, '2025-12-03 23:51:41'),
(10, 4, '1764780701_8870_slider_5.webp', 'https://maple-indiscrete-subglobularly.ngrok-free.dev/MVC3//Home/detail/BC01', 2, '2025-12-03 23:51:41'),
(11, 4, '1764780701_3714_slider_7.webp', 'https://maple-indiscrete-subglobularly.ngrok-free.dev/MVC3//ProductFront/', 3, '2025-12-03 23:51:41');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `banner_sets`
--

CREATE TABLE `banner_sets` (
  `banner_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `banner_sets`
--

INSERT INTO `banner_sets` (`banner_id`, `title`, `description`, `status`, `created_at`) VALUES
(3, 'mua 1 tặng 1', 'Trang thu', 'inactive', '2025-12-03 17:38:25'),
(4, 'Banner trang chủ', '', 'active', '2025-12-04 15:44:12');

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
(19, 'Thư chúc Tết và thông báo lịch nghỉ Tết Ất Tỵ 2025', 'Thân gửi Quý khách hàng,\r\n\r\nMột mùa xuân mới lại về, khắp mọi miền đất nước đang rộn ràng chào đón Xuân Ất Tỵ 2025. Nhân dịp này, Công ty cổ phần Văn phòng phẩm Hồng Hà xin trân trọng gửi đến Quý khách hàng lời chúc: MẠNH KHOẺ - BÌNH AN - AN KHANG - HẠNH PHÚC!\r\n\r\n \r\n\r\nNhìn lại năm 2024, dù đối mặt với nhiều thách thức trong bối cảnh kinh tế, Hồng Hà vẫn không ngừng nỗ lực và tự hào đạt được những thành tựu nổi bật, tiếp tục khẳng định vị thế là thương hiệu văn phòng phẩm được sử dụng nhiều nhất tại Việt Nam.\r\n\r\n \r\n\r\nHướng đến năm 2025, toàn thể công ty đã sẵn sàng cho những kế hoạch đột phá và mục tiêu lớn. Chúng tôi cam kết tiếp tục đổi mới, đem đến cho Quý khách những sản phẩm chất lượng cao, cùng dịch vụ chuyên nghiệp, đáp ứng trọn vẹn niềm tin yêu của Quý khách.\r\n\r\n \r\n\r\nNhân dịp xuân mới, chúng tôi xin gửi lời tri ân chân thành tới Quý khách hàng vì sự đồng hành và ủng hộ trong suốt thời gian qua. Với sự tận tâm và tinh thần lắng nghe, Văn phòng phẩm Hồng Hà sẽ không ngừng hoàn thiện để mang đến những giải pháp tối ưu và trải nghiệm tốt nhất.', 'screenshot_1764832111.png', 'hiển thị', '2025-12-04 14:17:44'),
(20, 'Tổ chức chuỗi sự kiện \"Đọc sách Ehon và Mogu\"', 'Ehon là bộ truyện được minh họa bằng tranh ảnh với những nội dung hoặc đề tài hết sức gần gũi. Truyện Ehon được đọc từ khi các bé được 1-2 tuổi, thậm chí nhỏ hơn, trở thành một thứ dinh dưỡng hàng ngày của tâm hồn và lớn lên trở thành những người ham đọc sách. \r\nChuỗi sự kiện \"Đọc sách Ehon và Mogu\" được tổ chức thường kì vào mỗi Chủ nhật cuối cùng của tháng, tại cửa hàng Văn phòng phẩm - Tầng 1 TTTM Vincom center Long Biên và số 25 Lý Thường Kiệt, Hoàn Kiếm. Tại đây các bé được tham gia vào các hoạt động vô cùng lý thú như Kamishibai, làm đồ thủ công, nghe đọc truyện Ehon. Cuối mỗi sự kiện, các bé còn nhận được các phần quà ý nghĩa tới từ Mogu và Hồng Hà.', 'screenshot_1764832447.png', 'hiển thị', '2025-12-04 14:17:18'),
(21, 'PHÁT ĐỘNG CUỘC THI VẼ TRANH ', 'Cuộc thi Vẽ tranh hè là sân chơi sáng tạo nằm trong chuỗi chương trình hàng năm của Công ty Văn phòng phẩm Với tiêu chí tạo ra sân chơi bổ ích vào mỗi kỳ nghỉ hè giúp các bạn học sinh thỏa sức vui chơi sáng tạo.\r\n\r\nTop 150 cuộc thi năm nay trở lại đặc biệt hơn với chủ đề \"TƯƠNG LAI TRONG MẮT EM\" sẽ được tổ chức tại Showroom Hồng Hà, 25 Lý Thường Kiệt, Quận Hoàn Kiếm, Thành phố Hà Nội. Các thí sinh tham dự cuộc thi sẽ được chia thành 3 nhóm tuổi, quý phụ huynh quét mã QR để tải mẫu đăng ký.\r\n\r\nNhóm 1: 6 - 8 tuổi\r\nNhóm 2: 9 - 11 tuổi\r\nNhóm 3: 12 - 15 tuổi\r\n\r\nTHỂ LỆ CUỘC THI:\r\nĐối tượng tham gia: Là học sinh trên địa bàn Hà Nội (từ 6 – 15 tuổi).\r\nChủ đề cuộc thi: TƯƠNG LAI TRONG MẮT EM.\r\nBài dự thi được vẽ trên khổ giấy A3, khuyến khích sử dụng giấy A3 Hồng Hà để bài thi đạt chất lượng tốt nhất (297cm x 420cm).\r\nThí sinh có thể gửi nhiều tác phẩm.\r\nKhông ghi thông tin lên mặt vẽ của bức tranh.\r\nĐiền đầy đủ thông tin và đính kèm bản đăng ký của BTC vào mặt sau bức tranh.\r\nBản đăng ký có thể được viết tay, đánh máy, in và điền tay.\r\nBài dự thi không có bản đăng ký được coi là không hợp lệ.\r\n\r\nCƠ CẤU GIẢI THƯỞNG:\r\n03 Giải Nhất\r\n06 Giải Nhì\r\n09 Giải Ba\r\n12 Giải Khuyến Khích\r\n01 Giải Tác phẩm được khán giả yêu thích nhất (Bình chọn online trên Fanpage)\r\n\r\n\r\nLỜI KHUYÊN CỦA BTC ĐỂ CÓ MỘT BÀI DỰ THI TỐT \r\nHãy cố gắng tô màu kín cả bức tranh, không nên để thừa lại các mảng giấy trắng trơn\r\nNên chọn màu vẽ đậm, rực rỡ, sắc nét\r\nViết thông điệp hay như một bài văn, không giới hạn số từ, hãy mô tả thật chi tiết phát minh của em', 'screenshot_1764832770.png', 'hiển thị', '2025-12-04 14:21:43');

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
  `note` text DEFAULT NULL,
  `transaction_info` text DEFAULT NULL,
  `shipping_method` enum('giao_hang','nhan_tai_cua_hang') NOT NULL DEFAULT 'giao_hang',
  `shipping_fee` int(11) NOT NULL DEFAULT 0,
  `status` enum('pending','approved','shipping','completed','cancelled') DEFAULT 'pending',
  `cancelled_by` varchar(20) DEFAULT NULL,
  `discount_code` varchar(50) DEFAULT NULL,
  `stock_reduced` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `order_code`, `total_amount`, `created_at`, `user_email`, `receiver`, `phone`, `address`, `note`, `transaction_info`, `shipping_method`, `shipping_fee`, `status`, `cancelled_by`, `discount_code`, `stock_reduced`) VALUES
(73, 0, 'HD1761998754', 320000.00, '2025-11-01 13:05:54', 'baochanbon@gmail.com', 'Trang admin', '0879119493', 'HN', NULL, 'dathanhtoan', 'giao_hang', 20000, 'cancelled', NULL, NULL, 0),
(74, 0, 'HD1761999943', 600000.00, '2025-11-01 13:25:43', 'baochanbon@gmail.com', 'Trang admin', '0879119493', 'HN1', NULL, 'chothanhtoan', 'giao_hang', 20000, 'pending', NULL, NULL, 0),
(75, 0, 'HD1761999961', 600000.00, '2025-11-01 13:26:01', 'baochanbon@gmail.com', 'Trang admin', 'yty', 'HN', NULL, 'chothanhtoan', 'giao_hang', 20000, 'pending', NULL, NULL, 0),
(76, 0, 'HD1761999995', 550000.00, '2025-11-01 13:26:35', 'baochanbon@gmail.com', 'Trang admin', '0879119493', 'HN', NULL, 'chothanhtoan', 'giao_hang', 20000, 'pending', NULL, NULL, 0),
(77, 0, 'HD1762000133', 550000.00, '2025-11-01 13:28:53', 'baochanbon@gmail.com', 'Trang admin', '0879119493', 'HN', NULL, 'chothanhtoan', 'giao_hang', 20000, 'pending', NULL, NULL, 0),
(78, 0, 'HD1762000164', 320000.00, '2025-11-01 13:29:24', 'baochanbon@gmail.com', 'Trang admin', '0879119493', 'HN', NULL, 'chothanhtoan', 'giao_hang', 20000, 'pending', NULL, NULL, 0),
(79, 0, 'DH1762001243452', 0.00, '2025-11-01 13:47:23', 'Array', 'Trang admin', '0879119493', 'HN', NULL, 'pending', 'giao_hang', 20000, 'pending', NULL, NULL, 0),
(80, 0, 'HD1762001458', 550000.00, '2025-11-01 13:50:58', 'baochanbon@gmail.com', 'Trang admin', '0879119493', 'HN', NULL, 'chothanhtoan', 'giao_hang', 20000, 'pending', NULL, NULL, 0),
(81, 0, 'HD1762002454', 550000.00, '2025-11-01 14:07:34', 'baochanbon@gmail.com', 'Trang admin', '0879119493', 'HN', NULL, 'dathanhtoan', 'giao_hang', 20000, 'pending', NULL, NULL, 0),
(82, 0, 'HD1762002487', 320000.00, '2025-11-01 14:08:07', 'baochanbon@gmail.com', 'Trang admin', '0879119493', 'HN', NULL, 'dathanhtoan', 'giao_hang', 20000, 'pending', NULL, NULL, 0),
(83, 0, 'HD1762002531', 320000.00, '2025-11-01 14:08:51', 'baochanbon@gmail.com', 'Trang admin', '0879119493', 'HN', NULL, 'dathanhtoan', 'giao_hang', 20000, 'pending', NULL, NULL, 0),
(84, 0, 'HD1762002538', 320000.00, '2025-11-01 14:08:58', 'baochanbon@gmail.com', 'Trang admin', '0879119493', 'HN', NULL, 'chothanhtoan', 'giao_hang', 20000, 'pending', NULL, NULL, 0),
(85, 0, 'HD1762003660', 126000.00, '2025-11-01 14:27:40', 'baochanbon@gmail.com', 'Trang admin', '0879119493', 'HN', NULL, 'chothanhtoan', 'giao_hang', 20000, 'pending', NULL, NULL, 0),
(86, 0, 'HD1762003792', 126000.00, '2025-11-01 14:29:52', 'baochanbon@gmail.com', 'Trang admin', '0879119493', 'HN', NULL, 'chothanhtoan', 'giao_hang', 20000, 'pending', NULL, NULL, 0),
(87, 0, 'HD1762003829', 500000.00, '2025-11-01 14:30:29', 'baochanbon@gmail.com', 'Trang admin', '0879119493', 'HN', NULL, 'chothanhtoan', 'giao_hang', 20000, 'pending', NULL, NULL, 0),
(88, 0, 'HD1762003858', 440000.00, '2025-11-01 14:30:58', 'baochanbon@gmail.com', 'Trang admin', '0879119493', 'HN', NULL, 'chothanhtoan', 'giao_hang', 20000, 'pending', NULL, NULL, 0),
(89, 0, 'HD1762003914', 140800.00, '2025-11-01 14:31:54', 'baochanbon@gmail.com', 'Trang admin', '0879119493', 'HN', NULL, 'chothanhtoan', 'giao_hang', 20000, 'pending', NULL, NULL, 0),
(90, 0, 'HD1762003941', 76000.00, '2025-11-01 14:32:21', 'baochanbon@gmail.com', 'Trang admin', '0879119493', 'HN', NULL, 'chothanhtoan', 'giao_hang', 20000, 'pending', NULL, NULL, 0),
(91, 0, 'HD1762003970', 256000.00, '2025-11-01 14:32:50', 'baochanbon@gmail.com', 'Trang admin', '0879119493', 'HN', NULL, 'chothanhtoan', 'giao_hang', 20000, 'pending', NULL, NULL, 0),
(92, 0, 'HD1762004447', 450000.00, '2025-11-01 14:40:47', 'baochanbon@gmail.com', 'Trang admin', '0879119493', 'HN', NULL, 'dathanhtoan', 'giao_hang', 20000, 'pending', NULL, NULL, 0),
(93, 0, 'HD1762004619', 176000.00, '2025-11-01 14:43:39', 'baochanbon@gmail.com', 'Trang admin', '0879119493', 'HN', NULL, 'chothanhtoan', 'giao_hang', 20000, 'pending', NULL, NULL, 0),
(94, 0, 'HD1762004887', 176000.00, '2025-11-01 14:48:07', 'baochanbon@gmail.com', 'Trang admin', '0879119493', 'HN', NULL, 'chothanhtoan', 'giao_hang', 20000, 'pending', NULL, NULL, 0),
(95, 0, 'HD1762004943', 126000.00, '2025-11-01 14:49:03', 'baochanbon@gmail.com', 'Trang admin', '0879119493', 'HN', NULL, 'dathanhtoan', 'giao_hang', 20000, 'pending', NULL, NULL, 0),
(96, 0, 'HD1762005247', 176000.00, '2025-11-01 14:54:07', 'baochanbon@gmail.com', 'Trang admin', '0879119493', 'HN', NULL, 'chothanhtoan', 'giao_hang', 20000, 'pending', NULL, NULL, 0),
(97, 0, 'HD1762005423', 126000.00, '2025-11-01 14:57:03', 'baochanbon@gmail.com', 'Trang admin', '0879119493', 'HN', NULL, 'dathanhtoan', 'giao_hang', 20000, 'pending', NULL, NULL, 0),
(98, 0, 'HD1762006112', 450000.00, '2025-11-01 15:08:32', 'baochanbon@gmail.com', 'Trang admin', '0879119493', 'HN', NULL, 'dathanhtoan', 'giao_hang', 20000, 'pending', NULL, NULL, 0),
(99, 0, 'HD1762006312', 171000.00, '2025-11-01 15:11:52', 'baochanbon@gmail.com', 'Trang admin', '0879119493', 'HN', NULL, 'dathanhtoan', 'giao_hang', 20000, 'pending', NULL, NULL, 0),
(100, 0, 'HD1762007029', 450000.00, '2025-11-01 15:23:49', 'baochanbon@gmail.com', 'Trang admin', '0879119493', 'HN', NULL, 'dathanhtoan', 'giao_hang', 20000, 'pending', NULL, NULL, 0),
(101, 0, 'HD1762007483', 450000.00, '2025-11-01 15:31:23', 'baochanbon@gmail.com', 'Trang admin', '0879119493', 'HN', NULL, 'chothanhtoan', 'giao_hang', 20000, 'pending', NULL, NULL, 0),
(102, 0, 'HD1762341256', 925000.00, '2025-11-05 12:14:16', 'baochanbon@gmail.com', 'Trang admin', '0879119493', 'HN', NULL, 'dathanhtoan', 'giao_hang', 20000, 'pending', NULL, NULL, 0),
(103, 0, 'HD1762341274', 540000.00, '2025-11-05 12:14:34', 'baochanbon@gmail.com', 'Trang admin', '0879119493', 'HN', NULL, 'chothanhtoan', 'giao_hang', 20000, 'pending', NULL, NULL, 0),
(104, 0, 'HD1762341299', 540000.00, '2025-11-05 12:14:59', 'baochanbon@gmail.com', 'Trang admin', '0879119493', 'HN', NULL, 'chothanhtoan', 'giao_hang', 20000, 'pending', NULL, NULL, 0),
(105, 0, 'HD1762341899', 380000.00, '2025-11-05 12:24:59', 'baochanbon@gmail.com', 'Trang admin', '0879119493', 'HN', NULL, ' Đã thanh toán', 'giao_hang', 20000, 'pending', NULL, NULL, 0),
(106, 0, 'HD1762342354', 175000.00, '2025-11-05 12:32:34', 'baochanbon@gmail.com', 'Trang admin', '0879119493', 'HN', NULL, ' Đã thanh toán', 'giao_hang', 20000, 'pending', NULL, NULL, 0),
(107, 0, 'HD1762342486', 205000.00, '2025-11-05 12:34:46', 'baochanbon@gmail.com', 'Trang admin', '0879119493', 'HN', NULL, 'dathanhtoan', 'giao_hang', 20000, 'pending', NULL, NULL, 0),
(108, 0, 'HD1762522534', 600000.00, '2025-11-07 14:35:34', 'baochanbon@gmail.com', 'Trang admin', '0879119493', 'HN', NULL, 'dathanhtoan', 'giao_hang', 20000, 'pending', NULL, NULL, 0),
(109, 0, 'HD1762523680', 600000.00, '2025-11-07 14:54:40', 'baochanbon@gmail.com', 'Trang admin', '0879119493', 'HN', NULL, 'dathanhtoan', 'giao_hang', 20000, 'pending', NULL, NULL, 0),
(110, 0, 'HD1762524012', 500000.00, '2025-11-07 15:00:12', 'baochanbon@gmail.com', 'Trang admin', '0879119493', 'HN', NULL, 'dathanhtoan', 'giao_hang', 20000, 'pending', NULL, NULL, 0),
(111, 0, 'HD1762524262', 500000.00, '2025-11-07 15:04:22', 'baochanbon@gmail.com', 'Trang admin', '0879119493', 'HN', NULL, 'dathanhtoan', 'giao_hang', 20000, 'pending', NULL, NULL, 0),
(112, 0, 'HD1762525152', 600000.00, '2025-11-07 15:19:12', 'baochanbon@gmail.com', 'Trang admin', '0879119493', 'HN', NULL, 'chothanhtoan', 'giao_hang', 20000, 'pending', NULL, NULL, 0),
(113, 0, 'HD1762525298', 200000.00, '2025-11-07 15:21:38', 'baochanbon@gmail.com', 'Trang admin', '0879119493', 'HN', NULL, 'chothanhtoan', 'giao_hang', 20000, 'pending', NULL, NULL, 0),
(114, 0, 'HD1762525614', 500000.00, '2025-11-07 15:26:54', 'baochanbon@gmail.com', 'Trang admin', '0879119493', 'HN', NULL, 'dathanhtoan', 'giao_hang', 20000, 'pending', NULL, NULL, 0),
(115, 0, 'HD1762526035', 5000000.00, '2025-11-07 15:33:55', 'baochanbon@gmail.com', 'Trang admin', '0879119493', 'HN', NULL, 'dathanhtoan', 'giao_hang', 20000, 'pending', NULL, NULL, 0),
(116, 0, 'HD1762526528', 1500000.00, '2025-11-07 15:42:08', 'baochanbon@gmail.com', 'Trang admin', '0879119493', 'HN', NULL, 'chothanhtoan', 'giao_hang', 20000, 'approved', NULL, NULL, 0),
(117, 0, 'HD1762527350', 1000000.00, '2025-11-07 15:55:50', 'baochanbon@gmail.com', 'Trang admin', '0879119493', 'HN', NULL, 'dathanhtoan', 'giao_hang', 20000, 'pending', NULL, NULL, 0),
(118, 0, 'HD1762527457', 1000000.00, '2025-11-07 15:57:37', 'baochanbon@gmail.com', 'Trang admin', '0879119493', 'HN', NULL, 'dathanhtoan', 'giao_hang', 20000, 'pending', NULL, NULL, 0),
(119, 0, 'HD1762530248', 1500000.00, '2025-11-07 16:44:08', 'baochanbon@gmail.com', 'Trang admin', '0879119493', 'HN', NULL, 'dathanhtoan', 'giao_hang', 20000, 'pending', NULL, NULL, 0),
(120, 0, 'HD1762531749', 720000.00, '2025-11-07 17:09:09', 'baochanbon@gmail.com', 'Trang admin', '0879119493', 'HN', NULL, 'dathanhtoan', 'giao_hang', 20000, 'pending', NULL, NULL, 0),
(121, 0, 'HD1762603115', 1340000.00, '2025-11-08 12:58:35', 'baochanbon@gmail.com', 'Trang admin', '0879119493', 'HN', NULL, 'dathanhtoan', 'giao_hang', 20000, 'cancelled', 'admin', NULL, 0),
(122, 0, 'HD1762603347', 940000.00, '2025-11-08 13:02:27', 'baochanbon@gmail.com', 'Trang admin', '0879119493', 'HN', NULL, 'dathanhtoan', 'giao_hang', 20000, 'completed', NULL, NULL, 0),
(123, 0, 'HD1762608919', 770000.00, '2025-11-08 14:35:19', 'baochanbon@gmail.com', 'Trang admin', '0879119493', 'HN', NULL, 'dathanhtoan', 'giao_hang', 20000, 'cancelled', NULL, NULL, 0),
(124, 0, 'HD1762609179', 270000.00, '2025-11-08 14:39:39', 'baochanbon@gmail.com', 'Trang admin', '0879119493', 'HN', NULL, 'dathanhtoan', 'giao_hang', 20000, 'cancelled', NULL, NULL, 0),
(125, 0, 'HD1762609362', 270000.00, '2025-11-08 14:42:42', 'baochanbon@gmail.com', 'Trang admin', '0879119493', 'HN', NULL, 'dathanhtoan', 'giao_hang', 20000, 'cancelled', NULL, NULL, 0),
(126, 0, 'HD1762609605', 270000.00, '2025-11-08 14:46:45', 'baochanbon@gmail.com', 'Trang admin', '0879119493', 'HN', NULL, 'dathanhtoan', 'giao_hang', 20000, 'cancelled', NULL, NULL, 0),
(127, 0, 'HD1762609836', 270000.00, '2025-11-08 14:50:36', 'baochanbon@gmail.com', 'Trang admin', '0879119493', 'HN', NULL, 'dathanhtoan', 'giao_hang', 20000, 'completed', NULL, NULL, 0),
(128, 0, 'HD1762610882', 2430000.00, '2025-11-08 15:08:02', 'baochanbon@gmail.com', 'Trang admin', '0879119493', 'HN', NULL, 'dathanhtoan', 'giao_hang', 20000, 'cancelled', NULL, NULL, 0),
(129, 0, 'HD1762610908', 2430000.00, '2025-11-08 15:08:28', 'baochanbon@gmail.com', 'Trang admin', '0879119493', 'HN', NULL, 'dathanhtoan', 'giao_hang', 20000, 'cancelled', NULL, NULL, 0),
(130, 0, 'HD1762611028', 270000.00, '2025-11-08 15:10:28', 'baochanbon@gmail.com', 'Trang admin', '0879119493', 'HN', NULL, 'dathanhtoan', 'giao_hang', 20000, 'cancelled', NULL, NULL, 0),
(131, 0, 'HD1762611555', 270000.00, '2025-11-08 15:19:15', 'baochanbon@gmail.com', 'Trang admin', '0879119493', 'HN', NULL, 'dathanhtoan', 'giao_hang', 20000, 'completed', NULL, NULL, 0),
(132, 0, 'HD1763129781', 650000.00, '2025-11-14 15:16:21', 'baochanbon@gmail.com', 'Trang user', '0879119493', 'Nam Tu Liem, Ha Noi', NULL, 'chothanhtoan', 'giao_hang', 20000, 'cancelled', 'admin', NULL, 0),
(133, 0, 'HD1763130904', 650000.00, '2025-11-14 15:35:04', 'baochanbon@gmail.com', 'Trang user', '0879119493', 'Nam Tu Liem, Ha Noi', NULL, 'dathanhtoan', 'giao_hang', 20000, 'approved', NULL, NULL, 0),
(134, 0, 'HD1763131286', 650000.00, '2025-11-14 15:41:26', 'baochanbon@gmail.com', 'Trang user', '0879119493', 'Nam Tu Liem, Ha Noi', NULL, 'dathanhtoan', 'giao_hang', 20000, 'approved', NULL, NULL, 0),
(135, 0, 'HD1763131724', 650000.00, '2025-11-14 15:48:44', 'baochanbon@gmail.com', 'Trang user', '0879119493', 'Nam Tu Liem, Ha Noi', NULL, 'dathanhtoan', 'giao_hang', 20000, 'approved', NULL, NULL, 0),
(136, 0, 'HD1763133036', 920000.00, '2025-11-14 16:10:36', 'baochanbon@gmail.com', 'Trang user', '0879119493', 'Nam Tu Liem, Ha Noi', NULL, 'dathanhtoan', 'giao_hang', 20000, 'approved', NULL, NULL, 0),
(137, 0, 'HD1763134409', 920000.00, '2025-11-14 16:33:29', 'baochanbon@gmail.com', 'Trang user', '0879119493', 'Nam Tu Liem, Ha Noi', NULL, 'chothanhtoan', 'giao_hang', 20000, 'cancelled', NULL, NULL, 0),
(138, 0, 'HD1763135111', 650000.00, '2025-11-14 16:45:11', 'baochanbon@gmail.com', 'Trang user', '0879119493', 'HEM', NULL, 'dathanhtoan', 'giao_hang', 20000, 'approved', NULL, NULL, 0),
(139, 0, 'HD1763135431', 650000.00, '2025-11-14 16:50:31', 'baochanbon@gmail.com', 'Trang user', '0879119493', 'HEM', NULL, 'dathanhtoan', 'giao_hang', 20000, 'approved', NULL, 'GG100K', 0),
(140, 0, 'HD1763136061', 670000.00, '2025-11-14 17:01:01', 'baochanbon@gmail.com', 'Trang user', '0879119493', 'Nam Tu Liem, Ha Noi', NULL, 'dathanhtoan', 'giao_hang', 20000, 'cancelled', NULL, 'GG100K', 0),
(141, 0, 'HD1763136248', 270000.00, '2025-11-14 17:04:08', 'baochanbon@gmail.com', 'Trang user', '0879119493', 'Nam Tu Liem, Ha Noi', NULL, 'dathanhtoan', 'giao_hang', 20000, 'shipping', NULL, NULL, 0),
(142, 0, 'HD1763136276', 270000.00, '2025-11-14 17:04:36', 'baochanbon@gmail.com', 'Trang user', '0879119493', 'HEM', NULL, 'dathanhtoan', 'giao_hang', 20000, 'cancelled', NULL, NULL, 0),
(143, 0, 'HD1763136378', 270000.00, '2025-11-14 17:06:18', 'baochanbon@gmail.com', 'Trang user', '0879119493', 'Nam Tu Liem, Ha Noi', NULL, 'dathanhtoan', 'giao_hang', 20000, 'completed', NULL, NULL, 0),
(144, 0, 'HD1763136724', 270000.00, '2025-11-14 17:12:04', 'baochanbon@gmail.com', 'Trang user', '0879119493', 'KHONG', NULL, 'chothanhtoan', 'giao_hang', 20000, 'cancelled', NULL, NULL, 0),
(145, 0, 'HD1763136850', 270000.00, '2025-11-14 17:14:10', 'baochanbon@gmail.com', 'Trang user', '0879119493', 'HEM', NULL, 'chothanhtoan', 'giao_hang', 20000, 'cancelled', 'admin', NULL, 0),
(146, 0, 'HD1763140826', 650000.00, '2025-11-14 18:20:26', 'baochanbon@gmail.com', 'Trang user', '0879119493', 'HEM', NULL, 'chothanhtoan', 'giao_hang', 20000, 'cancelled', 'user', 'GG100K', 0),
(147, 0, 'HD1763140871', 750000.00, '2025-11-14 18:21:11', 'baochanbon@gmail.com', 'Trang user', '0879119493', 'Nam Tu Liem, Ha Noi', NULL, 'chothanhtoan', 'giao_hang', 20000, 'cancelled', NULL, NULL, 0),
(148, 0, 'HD1763141994', 1500000.00, '2025-11-14 18:39:54', 'baochanbon@gmail.com', 'Trang user', '0879119493', 'KHONG', NULL, 'chothanhtoan', 'giao_hang', 20000, 'cancelled', 'admin', NULL, 0),
(149, 0, 'HD1763142678', 650000.00, '2025-11-14 18:51:18', 'baochanbon@gmail.com', 'Trang user', '0879119493', 'Nam Tu Liem, Ha Noi', NULL, 'chothanhtoan', 'giao_hang', 20000, 'pending', NULL, 'gg100k', 0),
(150, 0, 'HD1763142697', 650000.00, '2025-11-14 18:51:37', 'baochanbon@gmail.com', 'Trang user', '0879119493', 'Nam Tu Liem, Ha Noi', NULL, 'dathanhtoan', 'giao_hang', 20000, 'pending', NULL, 'gg100k', 0),
(151, 0, 'HD1763227687', 920000.00, '2025-11-16 00:28:07', 'bon@gmail.com', 'Bon user', '0879119493', 'Nam Tu Liem, Ha Noi', NULL, '2025-11-15 18:28:07', 'giao_hang', 20000, 'cancelled', 'user', 'gg100k', 0),
(152, 0, 'HD1763227735', 650000.00, '2025-11-16 00:28:55', 'bon@gmail.com', 'Bon user', '0879119493', 'Nam Tu Liem, Ha Noi', NULL, '2025-11-15 18:28:55', 'giao_hang', 20000, 'pending', NULL, 'GG100K', 0),
(153, 0, 'HD1763227901', 920000.00, '2025-11-16 00:31:41', 'bon@gmail.com', 'Bon user', '0879119493', 'KHONG', NULL, 'dathanhtoan', 'giao_hang', 20000, 'cancelled', 'admin', 'GG100K', 0),
(154, 0, 'HD1763229133', 2130000.00, '2025-11-16 00:52:13', 'bon@gmail.com', 'Bon user', '0879119493', 'KHONG', NULL, 'dathanhtoan', 'giao_hang', 20000, 'cancelled', 'user', 'GG100K', 0),
(155, 0, 'HD1763229995', 300000.00, '2025-11-16 01:06:35', 'bon@gmail.com', 'Bon user', '0879119493', 'Nam Tu Liem, Ha Noi', NULL, '2025-11-15 19:06:35', 'giao_hang', 20000, 'cancelled', 'user', NULL, 0),
(156, 0, 'HD1763230573', 750000.00, '2025-11-16 01:16:13', 'bon@gmail.com', 'Bon user', '0879119493', 'Nam Tu Liem, Ha Noi', NULL, '2025-11-15 19:16:13', 'giao_hang', 20000, 'cancelled', 'user', NULL, 0),
(157, 0, 'HD1763230603', 750000.00, '2025-11-16 01:16:43', 'bon@gmail.com', 'Bon user', '0879119493', 'Nam Tu Liem, Ha Noi', NULL, '2025-11-15 19:16:43', 'giao_hang', 20000, 'cancelled', 'admin', NULL, 0),
(158, 0, 'HD1763230806', 950000.00, '2025-11-16 01:20:06', 'bon@gmail.com', 'Bon user', '0879119493', 'Nam Tu Liem, Ha Noi', NULL, 'dathanhtoan', 'giao_hang', 20000, 'cancelled', 'user', 'GG100K', 0),
(159, 0, 'HD1763231328', 650000.00, '2025-11-16 01:28:48', 'bon@gmail.com', 'Bon user', '0879119493', 'KHONG', NULL, 'dathanhtoan', 'giao_hang', 20000, 'cancelled', 'admin', 'GG100K', 0),
(160, 0, 'HD1763264550', 300000.00, '2025-11-16 10:42:30', 'builong111104@gmail.com', 'long', '0834615369', 'fdfsfsf', NULL, '2025-11-16 04:42:30', 'giao_hang', 20000, 'cancelled', 'user', NULL, 0),
(161, 0, 'HD1763271683', 4000.00, '2025-11-16 12:41:23', 'bao@gmail.com', 'BAO admin', '0834615369', 'fdfsfsf', NULL, '2025-11-16 06:41:23', 'giao_hang', 20000, 'pending', NULL, NULL, 0),
(162, 0, 'HD1763273341', 4000.00, '2025-11-16 13:09:01', 'builong111104@gmail.com', 'long', '08342381744', 'qưe', NULL, '2025-11-16 07:09:01', 'giao_hang', 20000, 'cancelled', 'user', NULL, 0),
(163, 0, 'HD1763273563', 750000.00, '2025-11-16 13:12:43', 'builong111104@gmail.com', 'long', '08342381744', 'qưe', NULL, 'dathanhtoan', 'giao_hang', 20000, 'completed', NULL, NULL, 0),
(164, 0, 'HD1763276788', 4000.00, '2025-11-16 14:06:28', 'builong111104@gmail.com', 'long', '08342381744', 'qưe', NULL, '2025-11-16 08:06:28', 'giao_hang', 20000, 'pending', NULL, NULL, 0),
(165, 0, 'HD1763277393', 4000.00, '2025-11-16 14:16:33', 'builong111104@gmail.com', 'long', '08342381744', 'qưe', NULL, '2025-11-16 08:16:33', 'giao_hang', 20000, 'pending', NULL, NULL, 0),
(166, 0, 'HD1763277579', 4000.00, '2025-11-16 14:19:39', 'builong111104@gmail.com', 'long', '08342381744', 'qưe', NULL, '2025-11-16 08:19:39', 'giao_hang', 20000, 'cancelled', 'user', NULL, 0),
(167, 0, 'HD1763278982', 4000.00, '2025-11-16 14:43:02', 'builong111104@gmail.com', 'long', '08342381744', 'qưe', NULL, '2025-11-16 08:43:02', 'giao_hang', 20000, 'pending', NULL, NULL, 0),
(168, 0, 'HD1763279193', 4000.00, '2025-11-16 14:46:33', 'builong111104@gmail.com', 'long', '08342381744', 'qưe', NULL, '2025-11-16 08:46:33', 'giao_hang', 20000, 'pending', NULL, NULL, 0),
(169, 0, 'HD1763279294', 4000.00, '2025-11-16 14:48:14', 'builong111104@gmail.com', 'long', '08342381744', 'qưe', NULL, '2025-11-16 08:48:14', 'giao_hang', 20000, 'pending', NULL, NULL, 0),
(170, 0, 'HD1763279861', 8000.00, '2025-11-16 14:57:41', 'builong111104@gmail.com', 'long', '08342381744', 'qưe', NULL, '2025-11-16 08:57:41', 'giao_hang', 20000, 'pending', NULL, NULL, 0),
(171, 0, 'HD1763280178', 270000.00, '2025-11-16 15:02:58', 'builong111104@gmail.com', 'long', '08342381744', 'qưe', NULL, 'dathanhtoan', 'giao_hang', 20000, 'pending', NULL, NULL, 0),
(172, 0, 'HD1763280787', 274000.00, '2025-11-16 15:13:07', 'builong111104@gmail.com', 'long', '08342381744', 'qưe', NULL, '2025-11-16 09:13:07', 'giao_hang', 20000, 'pending', NULL, NULL, 0),
(173, 0, 'HD1763280955', 270000.00, '2025-11-16 15:15:55', 'builong111104@gmail.com', 'long', '08342381744', 'qưe', NULL, '2025-11-16 09:15:55', 'giao_hang', 20000, 'pending', NULL, NULL, 0),
(174, 0, 'HD1763286068', 770000.00, '2025-11-16 16:41:08', 'builong111104@gmail.com', 'long', '0879789673', 'qưe', NULL, 'dathanhtoan', 'giao_hang', 20000, 'pending', NULL, NULL, 0),
(175, 0, 'HD1763286470', 300000.00, '2025-11-16 16:47:50', 'builong111104@gmail.com', 'long', '0834615369', 'qưe', NULL, 'dathanhtoan', 'giao_hang', 20000, 'pending', NULL, NULL, 0),
(176, 0, 'HD1763286593', 200000.00, '2025-11-16 16:49:53', 'builong111104@gmail.com', 'long', '0834615369', 'qưe', NULL, 'dathanhtoan', 'giao_hang', 20000, 'pending', NULL, 'GG100K', 0),
(177, 0, 'HD1763286731', 4000.00, '2025-11-16 16:52:11', 'builong111104@gmail.com', 'long', '0834615369', 'qưe', NULL, '2025-11-16 10:52:11', 'giao_hang', 20000, 'pending', NULL, NULL, 0),
(178, 0, 'HD1763287214', 270000.00, '2025-11-16 17:00:14', 'baochanbon@gmail.com', 'Trang user', '0879119493', 'HEM', NULL, '2025-11-16 11:00:14', 'giao_hang', 20000, 'pending', NULL, NULL, 0),
(179, 0, 'HD1763287666', 270000.00, '2025-11-16 17:07:46', 'builong111104@gmail.com', 'long', '0834615369', 'qưeqwe', NULL, 'dathanhtoan', 'giao_hang', 20000, 'pending', NULL, NULL, 0),
(180, 0, 'HD1763355801', 270000.00, '2025-11-17 12:03:21', 'builong111104@gmail.com', 'long', '0834615369', 'fdfsfsf', NULL, '2025-11-17 06:03:21', 'giao_hang', 20000, 'pending', NULL, NULL, 0),
(181, 0, 'HD1763355980', 540000.00, '2025-11-17 12:06:20', 'builong111104@gmail.com', 'long', '0834615369', 'fdfsfsf', NULL, '2025-11-17 06:06:20', 'giao_hang', 20000, 'pending', NULL, NULL, 0),
(182, 0, 'HD1763356482', 270000.00, '2025-11-17 12:14:42', 'builong111104@gmail.com', 'long', '0834615369', 'fdfsfsf', NULL, '2025-11-17 06:14:42', 'giao_hang', 20000, 'pending', NULL, NULL, 0),
(183, 0, 'HD1763356617', 270000.00, '2025-11-17 12:16:57', 'builong111104@gmail.com', 'long', '0834615369', 'fdfsfsf', NULL, 'dathanhtoan', 'giao_hang', 20000, 'pending', NULL, NULL, 0),
(184, 0, 'HD1763430556', 270000.00, '2025-11-18 08:49:16', 'builong111104@gmail.com', 'long', '0834615369', 'fdfsfsf', NULL, '2025-11-18 02:49:16', 'giao_hang', 20000, 'pending', NULL, NULL, 0),
(185, 0, 'HD1763430704', 540000.00, '2025-11-18 08:51:44', 'builong111104@gmail.com', 'long', '0834615369', 'fdfsfsf', NULL, 'dathanhtoan', 'giao_hang', 20000, 'pending', NULL, NULL, 0),
(186, 0, 'HD1763430889', 270000.00, '2025-11-18 08:54:49', 'builong111104@gmail.com', 'long', '0834615369', 'fdfsfsf', NULL, 'dathanhtoan', 'giao_hang', 20000, 'pending', NULL, NULL, 0),
(187, 0, 'HD1763430984', 270000.00, '2025-11-18 08:56:24', 'builong111104@gmail.com', 'long', '0834615369', 'fdfsfsf', NULL, 'dathanhtoan', 'giao_hang', 20000, 'pending', NULL, NULL, 0),
(188, 0, 'HD1763431619', 270000.00, '2025-11-18 09:06:59', 'builong111104@gmail.com', 'long', '0834615369', 'fdfsfsf', NULL, 'dathanhtoan', 'giao_hang', 20000, 'pending', NULL, NULL, 0),
(189, 0, 'HD1763431871', 270000.00, '2025-11-18 09:11:11', 'builong111104@gmail.com', 'long', '0834615369', 'fdfsfsf', NULL, 'dathanhtoan', 'giao_hang', 20000, 'pending', NULL, NULL, 0),
(190, 0, 'HD1763431959', 270000.00, '2025-11-18 09:12:39', 'builong111104@gmail.com', 'long', '0834615369', 'fdfsfsf', NULL, 'dathanhtoan', 'giao_hang', 20000, 'pending', NULL, NULL, 0),
(191, 0, 'HD1763432227', 270000.00, '2025-11-18 09:17:07', 'builong111104@gmail.com', 'long', '0834615369', 'fdfsfsf', NULL, 'dathanhtoan', 'giao_hang', 20000, 'pending', NULL, NULL, 0),
(192, 0, 'HD1763432558', 270000.00, '2025-11-18 09:22:38', 'builong111104@gmail.com', 'long', '0834615369', 'fdfsfsf', NULL, 'dathanhtoan', 'giao_hang', 20000, 'pending', NULL, NULL, 0),
(193, 0, 'HD1763432819', 270000.00, '2025-11-18 09:26:59', 'builong111104@gmail.com', 'long', '0834615369', 'eqwq', NULL, 'dathanhtoan', 'giao_hang', 20000, 'pending', NULL, NULL, 0),
(194, 0, 'HD1763433043', 540000.00, '2025-11-18 09:30:43', 'builong111104@gmail.com', 'long', '0834615369', 'fdfsfsf', NULL, '2025-11-18 03:30:43', 'giao_hang', 20000, 'pending', NULL, NULL, 0),
(195, 0, 'HD1763433067', 270000.00, '2025-11-18 09:31:07', 'builong111104@gmail.com', 'long', '0834615369', 'fdfsfsf', NULL, 'dathanhtoan', 'giao_hang', 20000, 'pending', NULL, NULL, 0),
(196, 0, 'HD1763433321', 270000.00, '2025-11-18 09:35:21', 'builong111104@gmail.com', 'long', '0834615369', 'fdfsfsf', NULL, 'dathanhtoan', 'giao_hang', 20000, 'pending', NULL, NULL, 1),
(197, 0, 'HD1763433545', 270000.00, '2025-11-18 09:39:05', 'builong111104@gmail.com', 'long', '0834615369', 'fdfsfsf', NULL, 'dathanhtoan', 'giao_hang', 20000, 'pending', NULL, NULL, 1),
(198, 0, 'HD1763611713', 220000.00, '2025-11-20 11:08:33', 'builong111104@gmail.com', 'long', '0834615369', 'fdfsfsf', NULL, 'dathanhtoan', 'giao_hang', 20000, 'pending', NULL, '123', 1),
(199, 0, 'HD1763611858', 220000.00, '2025-11-20 11:10:58', 'builong111104@gmail.com', 'long', '0834615369', 'fdfsfsf', NULL, 'dathanhtoan', 'giao_hang', 20000, 'pending', NULL, '123', 1),
(200, 0, 'HD1763612497', 220000.00, '2025-11-20 11:21:37', 'builong111104@gmail.com', 'long', '0834615369', 'fdfsfsf', NULL, '2025-11-20 05:21:37', 'giao_hang', 20000, 'pending', NULL, '123', 0),
(201, 0, 'HD1763613130', 220000.00, '2025-11-20 11:32:10', 'builong111104@gmail.com', 'long', '0834615369', 'fdfsfsf', NULL, 'dathanhtoan', 'giao_hang', 20000, 'pending', NULL, '123', 1),
(202, 0, 'HD1763614458', 220000.00, '2025-11-20 11:54:18', 'builong111104@gmail.com', 'long', '0834615369', 'fdfsfsf', NULL, '2025-11-20 05:54:18', 'giao_hang', 20000, 'pending', NULL, '123', 0),
(203, 0, 'HD1763614582', 220000.00, '2025-11-20 11:56:22', 'builong111104@gmail.com', 'long', '0834615369', 'fdfsfsf', NULL, '2025-11-20 05:56:22', 'giao_hang', 20000, 'pending', NULL, '123', 0),
(204, 0, 'HD1763615020', 220000.00, '2025-11-20 12:03:40', 'builong111104@gmail.com', 'long', '0834615369', 'fdfsfsf', NULL, '2025-11-20 06:03:40', 'giao_hang', 20000, 'pending', NULL, '123', 0),
(205, 0, 'HD1763615280', 540000.00, '2025-11-20 12:08:00', 'builong111104@gmail.com', 'long', '0834615369', 'fdfsfsf', NULL, 'dathanhtoan', 'giao_hang', 20000, 'pending', NULL, '123', 0),
(206, 0, 'HD1763796633', 220000.00, '2025-11-22 14:30:33', 'builong111104@gmail.com', 'long', '0834615369', 'fdfsfsf', NULL, 'dathanhtoan', 'giao_hang', 20000, 'pending', NULL, '123', 1),
(207, 0, 'HD1763797816', 220000.00, '2025-11-22 14:50:16', 'builong111104@gmail.com', 'long', '0834615369', 'fdfsfsf', NULL, 'dathanhtoan', 'giao_hang', 20000, 'pending', NULL, '123', 1),
(208, 0, 'HD1763812571', 220000.00, '2025-11-22 18:56:11', 'builong111104@gmail.com', 'long', '0834615369', 'fdfsfsf', NULL, 'dathanhtoan', 'giao_hang', 20000, 'pending', NULL, '123', 1),
(209, 0, 'HD1763812697', 540000.00, '2025-11-22 18:58:17', 'builong111104@gmail.com', 'long', '0834615369', 'fdfsfsf', NULL, 'dathanhtoan', 'giao_hang', 20000, 'pending', NULL, '123', 1),
(210, 0, 'HD1763812969', 540000.00, '2025-11-22 19:02:49', 'builong111104@gmail.com', 'long', '0834615369', 'fdfsfsf', NULL, 'dathanhtoan', 'giao_hang', 20000, 'pending', NULL, '123', 1),
(211, 0, 'HD1763884173', 540000.00, '2025-11-23 14:49:33', 'builong111104@gmail.com', 'long', '0834615369', 'fdfsfsf', NULL, 'dathanhtoan', 'giao_hang', 20000, 'pending', NULL, '123', 1),
(212, 0, 'HD1763884626', 670000.00, '2025-11-23 14:57:06', 'builong111104@gmail.com', 'long', '0834615369', 'fdfsfsf', NULL, 'dathanhtoan', 'giao_hang', 20000, 'completed', NULL, '123', 1),
(213, 0, 'HD1763891949', 170000.00, '2025-11-23 16:59:09', 'builong111104@gmail.com', 'long', '0834615369', 'fdfsfsf', NULL, '2025-11-23 10:59:09', 'giao_hang', 20000, 'pending', NULL, '123', 0),
(214, 0, 'HD1763909250', 710000.00, '2025-11-23 21:47:30', 'builong111104@gmail.com', 'long', '0834615369', 'fdfsfsf', NULL, '2025-11-23 15:47:30', 'giao_hang', 20000, 'pending', NULL, '123', 0),
(215, 0, 'HD1763913398', 270000.00, '2025-11-23 22:56:38', 'builong111104@gmail.com', 'long', '0834615369', 'fdfsfsf', NULL, '2025-11-23 16:56:38', 'giao_hang', 20000, 'pending', NULL, NULL, 0),
(216, 0, 'HD1763914794', 170000.00, '2025-11-23 23:19:54', 'builong111104@gmail.com', 'long', '0834615369', 'fdfsfsf', NULL, '2025-11-23 17:19:54', 'giao_hang', 20000, 'pending', NULL, '123', 0),
(217, 30, 'HD1763959536', 270000.00, '2025-11-24 11:45:36', 'builong111104@gmail.com', 'long', '0834615369', 'fdfsfsf', NULL, 'chothanhtoan', 'giao_hang', 20000, 'pending', NULL, '', 0),
(218, 30, 'HD1763959945', 270000.00, '2025-11-24 11:52:25', 'builong111104@gmail.com', 'long', '0834615369', 'fdfsfsf', NULL, 'chothanhtoan', 'giao_hang', 20000, 'pending', NULL, '', 0),
(219, 30, 'HD1763961473', 270000.00, '2025-11-24 12:17:53', 'builong111104@gmail.com', 'long', '0834615369', 'fdfsfsf', NULL, 'chothanhtoan', 'giao_hang', 20000, 'pending', NULL, '', 0),
(220, 30, 'HD1763961580', 770000.00, '2025-11-24 12:19:40', 'builong111104@gmail.com', 'long', '0834615369', 'fdfsfsf', NULL, 'dathanhtoan', 'giao_hang', 20000, 'approved', NULL, '', 1),
(221, 30, 'HD1763961726', 670000.00, '2025-11-24 12:22:06', 'builong111104@gmail.com', 'long', '0834615369', 'fdfsfsf', NULL, 'dathanhtoan', 'giao_hang', 20000, 'approved', NULL, '123', 1),
(222, 27, 'HD1764086051', 770000.00, '2025-11-25 22:54:11', 'baochanbon@gmail.com', 'Trang user', '0879119493', 'HN', NULL, 'dathanhtoan', 'giao_hang', 20000, 'cancelled', 'user', '', 1),
(223, 27, 'HD1764093022', 770000.00, '2025-11-26 00:50:22', 'baochanbon@gmail.com', 'Trang user', '0879119493', 'HN', NULL, 'dathanhtoan', 'giao_hang', 20000, 'completed', NULL, '', 1),
(224, 27, 'HD1764664779', 298000.00, '2025-12-02 15:39:39', 'baochanbon@gmail.com', 'Trang user', '0879119493', 'hem', NULL, 'dathanhtoan', 'giao_hang', 20000, 'shipping', NULL, 'GG30', 1),
(225, 27, 'HD1764673395', 10500.00, '2025-12-02 18:03:15', 'baochanbon@gmail.com', 'Trang user', '0879119493', 'HN', NULL, 'chothanhtoan', 'giao_hang', 20000, 'pending', NULL, '', 0),
(226, 27, 'HD1764674062', 23500.00, '2025-12-02 18:14:22', 'baochanbon@gmail.com', 'Trang user', '0879119493', 'HN', NULL, 'giao_hang', '', 20000, 'pending', NULL, '', 0),
(227, 27, 'HD1764674090', 3500.00, '2025-12-02 18:14:50', 'baochanbon@gmail.com', 'Trang user', '0879119493', 'HN', NULL, 'nhan_tai_cua_hang', '', 20000, 'pending', NULL, '', 0),
(228, 27, 'HD1764674180', 27000.00, '2025-12-02 18:16:20', 'baochanbon@gmail.com', 'Trang user', '0879119493', 'HN', NULL, 'dathanhtoan', 'giao_hang', 20000, 'approved', NULL, '', 1),
(229, 27, 'HD1764675200', 70000.00, '2025-12-02 18:33:20', 'baochanbon@gmail.com', 'Trang user', '0879119493', 'HN', NULL, 'chothanhtoan', 'nhan_tai_cua_hang', 0, 'cancelled', 'user', '', 1),
(230, 27, 'HD1764675955', 130000.00, '2025-12-02 18:45:55', 'baochanbon@gmail.com', 'Trang user', '0879119493', 'HN', NULL, 'dathanhtoan', 'giao_hang', 20000, 'cancelled', 'user', 'GG30', 1),
(231, 27, 'HD1764677863', 130000.00, '2025-12-02 19:17:43', 'baochanbon@gmail.com', 'Trang user', '0879119493', 'HN', NULL, 'dathanhtoan', 'giao_hang', 20000, 'approved', NULL, 'GG30', 1),
(232, 27, 'HD1764679367', 177000.00, '2025-12-02 19:42:47', 'baochanbon@gmail.com', 'Trang user', '0879119493', 'HN', NULL, 'chothanhtoan', 'giao_hang', 20000, 'approved', NULL, 'GG50K', 0),
(233, 27, 'HD1764679397', 177000.00, '2025-12-02 19:43:17', 'baochanbon@gmail.com', 'Trang user', '0879119493', 'HN', NULL, 'chothanhtoan', 'giao_hang', 20000, 'pending', NULL, 'GG50K', 0),
(234, 27, 'HD1764679721', 177000.00, '2025-12-02 19:48:41', 'baochanbon@gmail.com', 'Trang user', '0879119493', 'HN', NULL, 'chothanhtoan', 'giao_hang', 20000, 'pending', NULL, 'GG50K', 0),
(235, 27, 'HD1764680032', 330000.00, '2025-12-02 19:53:52', 'baochanbon@gmail.com', 'Trang user', '0879119493', 'HN', NULL, 'chothanhtoan', 'giao_hang', 20000, 'pending', NULL, 'GG50K', 0),
(236, 27, 'HD1764680188', 330000.00, '2025-12-02 19:56:28', 'baochanbon@gmail.com', 'Trang user', '0879119493', 'HN', NULL, 'chothanhtoan', 'giao_hang', 20000, 'pending', NULL, 'GG50K', 0),
(237, 27, 'HD1764680236', 330000.00, '2025-12-02 19:57:16', 'baochanbon@gmail.com', 'Trang user', '0879119493', 'HN', NULL, 'dathanhtoan', 'giao_hang', 20000, 'approved', NULL, 'GG50K', 1),
(238, 27, 'HD1764680379', 740000.00, '2025-12-02 19:59:39', 'baochanbon@gmail.com', 'Trang user', '0879119493', 'HN', NULL, 'dathanhtoan', 'giao_hang', 20000, 'approved', NULL, '', 1),
(239, 27, 'HD1764680643', 23000.00, '2025-12-02 20:04:03', 'baochanbon@gmail.com', 'Trang user', '0879119493', 'HN', NULL, 'chothanhtoan', 'giao_hang', 20000, 'pending', NULL, '', 1),
(240, 27, 'HD1764680696', 3500.00, '2025-12-02 20:04:56', 'baochanbon@gmail.com', 'Trang user', '0879119493', 'HN', NULL, 'chothanhtoan', 'nhan_tai_cua_hang', 0, 'pending', NULL, '', 1),
(241, 27, 'HD1764680981', 30000.00, '2025-12-02 20:09:41', 'baochanbon@gmail.com', 'Trang user', '0879119493', 'HN', NULL, 'dathanhtoan', 'giao_hang', 20000, 'approved', NULL, 'GG50K', 1),
(242, 33, 'HD1764775023', 50000.00, '2025-12-03 22:17:03', 'baobao@gmail.com', 'Baobaobao', '0879119493', 'Nam Tu Liem, Ha Noi', NULL, 'dathanhtoan', 'giao_hang', 20000, 'cancelled', 'user', 'GG15K', 1),
(243, 33, 'HD1764789658', 429000.00, '2025-12-04 02:20:58', 'baobao@gmail.com', 'Baobaobao', '0879119493', 'KHONG', NULL, 'dathanhtoan', 'giao_hang', 20000, 'approved', NULL, 'GG15K', 1),
(244, 27, 'HD1764859673', 510000.00, '2025-12-04 21:47:53', 'baochanbon@gmail.com', 'Trang user', '0879119493', 'Nam Tu Liem, Ha Noi', NULL, 'dathanhtoan', 'giao_hang', 20000, 'shipping', NULL, 'GG30', 1),
(245, 30, 'HD1764864241', 68000.00, '2025-12-04 23:04:01', 'builong111104@gmail.com', 'long', '0834615369', 'fdfsfsf', NULL, 'dathanhtoan', 'giao_hang', 20000, 'completed', NULL, '', 1),
(246, 30, 'HD1764917261', 52000.00, '2025-12-05 13:47:41', 'builong111104@gmail.com', 'long', '0676576575', '1233444', NULL, 'dathanhtoan', 'giao_hang', 20000, 'approved', NULL, '', 1),
(247, 30, 'HD1764924311', 57600.00, '2025-12-05 15:45:11', 'builong111104@gmail.com', 'long', '0999999996', 'ssđr', NULL, 'chothanhtoan', 'giao_hang', 20000, 'pending', NULL, '', 0),
(248, 30, 'HD1764924671', 175000.00, '2025-12-05 15:51:11', 'builong111104@gmail.com', 'long', '0999999991', 'ssđt', NULL, 'dathanhtoan', 'nhan_tai_cua_hang', 0, 'approved', NULL, '', 1),
(249, 27, 'HD1764930439', 22000.00, '2025-12-05 17:27:19', 'baochanbon@gmail.com', 'Trang user', '0879119493', 'Nam Tu Liem, Ha Noi', NULL, 'dathanhtoan', 'giao_hang', 20000, 'completed', NULL, 'GG50K', 1),
(250, 30, 'HD1765113000', 195000.00, '2025-12-07 20:10:00', 'builong111104@gmail.com', 'long', '0834615369', 'qưqw', NULL, 'chothanhtoan', 'giao_hang', 20000, 'pending', NULL, '', 0);

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
(175, '159', 'SS1116', 1, 800000.00, 750000.00, 750000.00, 'iphone16.png', NULL, 'IP16'),
(176, '160', 'NK22', 1, 300000.00, 300000.00, 300000.00, 'defaut.png', NULL, 'NKasmvfd'),
(177, '161', 'BUT01_TL', 1, 4000.00, 4000.00, 4000.00, 'bút bi TL.jpg', NULL, 'Bút Bi Thiên Long'),
(178, '162', 'BUT01_TL', 1, 4000.00, 4000.00, 4000.00, 'bút bi TL.jpg', NULL, 'Bút Bi Thiên Long'),
(179, '163', 'SS1116', 1, 800000.00, 750000.00, 750000.00, 'iphone16.png', NULL, 'IP16'),
(180, '164', 'BUT01_TL', 1, 4000.00, 4000.00, 4000.00, 'iphone16promax.png', NULL, 'Bút Bi Thiên Long'),
(181, '165', 'BUT01_TL', 1, 4000.00, 4000.00, 4000.00, 'iphone16promax.png', NULL, 'Bút Bi Thiên Long'),
(182, '166', 'BUT01_TL', 1, 4000.00, 4000.00, 4000.00, 'iphone16promax.png', NULL, 'Bút Bi Thiên Long'),
(183, '167', 'BUT01_TL', 1, 4000.00, 4000.00, 4000.00, 'iphone16promax.png', NULL, 'Bút Bi Thiên Long'),
(184, '168', 'BUT01_TL', 1, 4000.00, 4000.00, 4000.00, 'iphone16promax.png', NULL, 'Bút Bi Thiên Long'),
(185, '169', 'BUT01_TL', 1, 4000.00, 4000.00, 4000.00, 'iphone16promax.png', NULL, 'Bút Bi Thiên Long'),
(186, '170', 'BUT01_TL', 2, 4000.00, 4000.00, 8000.00, 'iphone16promax.png', NULL, 'Bút Bi Thiên Long'),
(187, '171', 'IP14', 1, 320000.00, 270000.00, 270000.00, 'iphone14.png', NULL, 'ip14pro'),
(188, '172', 'BUT01_TL', 1, 4000.00, 4000.00, 4000.00, 'iphone16promax.png', NULL, 'Bút Bi Thiên Long'),
(189, '172', 'IP14', 1, 320000.00, 270000.00, 270000.00, 'iphone14.png', NULL, 'ip14pro'),
(190, '173', 'IP14', 1, 320000.00, 270000.00, 270000.00, 'iphone14.png', NULL, 'ip14pro'),
(191, '174', 'ip17', 1, 820000.00, 770000.00, 770000.00, 'iphone17_1.png', NULL, 'ip17MAX'),
(192, '175', 'NK22', 1, 300000.00, 300000.00, 300000.00, 'defaut.png', NULL, 'NKasmvfd'),
(193, '176', 'NK22', 1, 300000.00, 300000.00, 300000.00, 'defaut.png', NULL, 'NKasmvfd'),
(194, '177', 'BUT01_TL', 1, 4000.00, 4000.00, 4000.00, 'iphone16promax.png', NULL, 'Bút Bi Thiên Long'),
(195, '178', 'IP14', 1, 320000.00, 270000.00, 270000.00, 'iphone14.png', NULL, 'ip14pro'),
(196, '179', 'IP14', 1, 320000.00, 270000.00, 270000.00, 'iphone14.png', NULL, 'ip14pro'),
(197, '180', 'IP14', 1, 320000.00, 270000.00, 270000.00, 'iphone14.png', NULL, 'ip14pro'),
(198, '181', 'IP14', 2, 320000.00, 270000.00, 540000.00, 'iphone14.png', NULL, 'ip14pro'),
(199, '182', 'IP14', 1, 320000.00, 270000.00, 270000.00, 'iphone14.png', NULL, 'ip14pro'),
(200, '183', 'IP14', 1, 320000.00, 270000.00, 270000.00, 'iphone14.png', NULL, 'ip14pro'),
(201, '184', 'IP14', 1, 320000.00, 270000.00, 270000.00, 'iphone14.png', NULL, 'ip14pro'),
(202, '185', 'IP14', 2, 320000.00, 270000.00, 540000.00, 'iphone14.png', NULL, 'ip14pro'),
(203, '186', 'IP14', 1, 320000.00, 270000.00, 270000.00, 'iphone14.png', NULL, 'ip14pro'),
(204, '187', 'IP14', 1, 320000.00, 270000.00, 270000.00, 'iphone14.png', NULL, 'ip14pro'),
(205, '188', 'IP14', 1, 320000.00, 270000.00, 270000.00, 'iphone14.png', NULL, 'ip14pro'),
(206, '189', 'IP14', 1, 320000.00, 270000.00, 270000.00, 'iphone14.png', NULL, 'ip14pro'),
(207, '190', 'IP14', 1, 320000.00, 270000.00, 270000.00, 'iphone14.png', NULL, 'ip14pro'),
(208, '191', 'IP14', 1, 320000.00, 270000.00, 270000.00, 'iphone14.png', NULL, 'ip14pro'),
(209, '192', 'IP14', 1, 320000.00, 270000.00, 270000.00, 'iphone14.png', NULL, 'ip14pro'),
(210, '193', 'IP14', 1, 320000.00, 270000.00, 270000.00, 'iphone14.png', NULL, 'ip14pro'),
(211, '194', 'IP14', 2, 320000.00, 270000.00, 540000.00, 'iphone14.png', NULL, 'ip14pro'),
(212, '195', 'IP14', 1, 320000.00, 270000.00, 270000.00, 'iphone14.png', NULL, 'ip14pro'),
(213, '196', 'IP14', 1, 320000.00, 270000.00, 270000.00, 'iphone14.png', NULL, 'ip14pro'),
(214, '197', 'IP14', 1, 320000.00, 270000.00, 270000.00, 'iphone14.png', NULL, 'ip14pro'),
(215, '198', 'IP14', 1, 320000.00, 320000.00, 320000.00, 'iphone14.png', NULL, 'ip14pro'),
(216, '199', 'IP14', 1, 320000.00, 320000.00, 320000.00, 'iphone14.png', NULL, 'ip14pro'),
(217, '200', 'IP14', 1, 320000.00, 320000.00, 320000.00, 'iphone14.png', NULL, 'ip14pro'),
(218, '201', 'IP14', 1, 320000.00, 320000.00, 320000.00, 'iphone14.png', NULL, ''),
(219, '202', 'IP14', 1, 320000.00, 320000.00, 320000.00, 'iphone14.png', NULL, ''),
(220, '203', 'IP14', 1, 320000.00, 320000.00, 320000.00, 'iphone14.png', NULL, ''),
(221, '204', 'IP14', 1, 320000.00, 320000.00, 320000.00, 'iphone14.png', NULL, ''),
(222, '205', 'IP14', 2, 320000.00, 320000.00, 640000.00, 'iphone14.png', NULL, 'ip14pro'),
(223, '206', 'IP14', 1, 320000.00, 320000.00, 320000.00, 'iphone14.png', NULL, 'ip14pro'),
(224, '207', 'IP14', 1, 320000.00, 320000.00, 320000.00, 'iphone14.png', NULL, 'ip14pro'),
(225, '208', 'IP14', 1, 320000.00, 320000.00, 320000.00, 'iphone14.png', NULL, 'ip14pro'),
(226, '209', 'IP14', 2, 320000.00, 320000.00, 640000.00, 'iphone14.png', NULL, 'ip14pro'),
(227, '210', 'IP14', 2, 320000.00, 320000.00, 640000.00, 'iphone14.png', NULL, 'ip14pro'),
(228, '211', 'IP14', 2, 320000.00, 320000.00, 640000.00, 'iphone14.png', NULL, 'ip14pro'),
(229, '212', 'ip17', 1, 820000.00, 770000.00, 770000.00, 'iphone17_1.png', NULL, 'ip17MAX'),
(230, '213', 'IP14', 1, 320000.00, 270000.00, 270000.00, 'iphone14.png', NULL, 'ip14pro'),
(231, '214', 'IP14', 3, 320000.00, 270000.00, 810000.00, 'iphone14.png', NULL, 'ip14pro'),
(232, '215', 'IP14', 1, 320000.00, 270000.00, 270000.00, 'iphone14.png', NULL, 'ip14pro'),
(233, '216', 'IP14', 1, 320000.00, 270000.00, 270000.00, 'iphone14.png', NULL, 'ip14pro'),
(234, '217', 'IP14', 1, 320000.00, 270000.00, 270000.00, 'iphone14.png', NULL, 'ip14pro'),
(235, '218', 'IP14', 1, 320000.00, 270000.00, 270000.00, 'iphone14.png', NULL, 'ip14pro'),
(236, '219', 'IP14', 1, 320000.00, 270000.00, 270000.00, 'iphone14.png', NULL, 'ip14pro'),
(237, '220', 'ip17', 1, 820000.00, 770000.00, 770000.00, 'iphone17_1.png', NULL, 'ip17MAX'),
(238, '221', 'ip17', 1, 820000.00, 770000.00, 770000.00, 'iphone17_1.png', NULL, 'ip17MAX'),
(239, '222', 'ip17', 1, 820000.00, 770000.00, 770000.00, 'iphone17_1.png', NULL, 'ip17MAX'),
(240, '223', 'ip17', 1, 820000.00, 770000.00, 770000.00, 'iphone17_1.png', NULL, 'ip17MAX'),
(241, '224', 'BC01', 1, 40000.00, 32000.00, 32000.00, 'but chi go 2b.png', NULL, 'Bút chì gỗ 2B ( Hộp 10 chiếc )'),
(242, '224', 'BC02', 1, 20000.00, 16000.00, 16000.00, 'but chi kim.png', NULL, 'Bút chì Kim Sliver'),
(243, '224', 'BCC', 1, 10000.00, 10000.00, 10000.00, 'bang.png', NULL, 'Bảng chữ cái và số tiếng Anh Hồng Hà '),
(244, '224', 'BL001', 1, 190000.00, 175000.00, 175000.00, 'balo.png', NULL, 'Ba lô chống gù Siêu nhân'),
(245, '224', 'SBD01', 1, 95000.00, 95000.00, 95000.00, 'so 1.png', NULL, 'Sổ tổng hợp 600 trang B5 Hồng Hà'),
(246, '225', 'BC04', 1, 3500.00, 3500.00, 3500.00, 'but chi go co tay.png', NULL, 'Bút chì gỗ có tẩy 2B'),
(247, '225', 'BDB02', 1, 7000.00, 7000.00, 7000.00, 'but da 2.png', NULL, 'Bút dạ bảng 2 đầu'),
(248, '226', 'BHL01', 1, 3500.00, 3500.00, 3500.00, 'but hl vang.png', NULL, 'Bút dạ quang '),
(249, '227', 'BHL01', 1, 3500.00, 3500.00, 3500.00, 'but hl vang.png', NULL, 'Bút dạ quang '),
(250, '228', 'BDB02', 1, 7000.00, 7000.00, 7000.00, 'but da 2.png', NULL, 'Bút dạ bảng 2 đầu'),
(251, '229', 'BDB02', 10, 7000.00, 7000.00, 70000.00, 'but da 2.png', NULL, 'Bút dạ bảng 2 đầu'),
(252, '230', 'BDB02', 20, 7000.00, 7000.00, 140000.00, 'but da 2.png', NULL, 'Bút dạ bảng 2 đầu'),
(253, '231', 'BDB02', 20, 7000.00, 7000.00, 140000.00, 'but da 2.png', NULL, 'Bút dạ bảng 2 đầu'),
(254, '234', 'BC01', 1, 40000.00, 32000.00, 32000.00, 'but chi go 2b.png', NULL, 'Bút chì gỗ 2B ( Hộp 10 chiếc )'),
(255, '234', 'BL001', 1, 190000.00, 175000.00, 175000.00, 'balo.png', NULL, 'Ba lô chống gù Siêu nhân'),
(256, '235', 'BL002', 1, 360000.00, 360000.00, 360000.00, 'balo2.png', NULL, 'Ba lô mầm non'),
(257, '236', 'BL002', 1, 360000.00, 360000.00, 360000.00, 'balo2.png', NULL, 'Ba lô mầm non'),
(258, '237', 'BL002', 1, 360000.00, 360000.00, 360000.00, 'balo2.png', NULL, 'Ba lô mầm non'),
(259, '238', 'BL002', 2, 360000.00, 360000.00, 720000.00, 'balo2.png', NULL, 'Ba lô mầm non'),
(260, '239', 'BC03', 1, 3000.00, 3000.00, 3000.00, 'but chi vang.png', NULL, 'Bút chì gỗ ABC - 2B '),
(261, '240', 'BC04', 1, 3500.00, 3500.00, 3500.00, 'but chi go co tay.png', NULL, 'Bút chì gỗ có tẩy 2B'),
(262, '241', 'BC05', 10, 8000.00, 6000.00, 60000.00, 'but chi kim bam.png', NULL, 'Bút chì kim bấm ngòi 0.5mm'),
(263, '242', 'BC01', 1, 40000.00, 32000.00, 32000.00, 'but chi go 2b.png', NULL, 'Bút chì gỗ 2B ( Hộp 10 chiếc )'),
(264, '242', 'HB1', 1, 10000.00, 10000.00, 10000.00, 'hop but.png', NULL, 'Hộp bút Kin Kin'),
(265, '242', 'TK2', 1, 3000.00, 3000.00, 3000.00, 'thuoc ke 2.png', NULL, 'Thước kẻ 16cm'),
(266, '243', 'BC01', 10, 40000.00, 32000.00, 320000.00, 'but chi go 2b.png', NULL, 'Bút chì gỗ 2B ( Hộp 10 chiếc )'),
(267, '243', 'BX02', 10, 13000.00, 10400.00, 104000.00, 'but xoa 7ml.png', NULL, 'Bút xóa 7ml'),
(268, '244', 'BK01', 1, 550000.00, 520000.00, 520000.00, 'but ky 1.png', NULL, 'Bút máy văn phòng cao cấp Trường Sơn'),
(269, '245', 'BC02', 1, 20000.00, 16000.00, 16000.00, 'but chi kim.png', NULL, 'Bút chì Kim Sliver'),
(270, '245', 'BC01', 1, 40000.00, 32000.00, 32000.00, 'but chi go 2b.png', NULL, 'Bút chì gỗ 2B ( Hộp 10 chiếc )'),
(271, '246', 'BC01', 1, 40000.00, 32000.00, 32000.00, 'but chi go 2b.png', NULL, 'Bút chì gỗ 2B ( Hộp 10 chiếc )'),
(272, '247', 'BC01', 1, 40000.00, 32000.00, 32000.00, 'but chi go 2b.png', NULL, 'Bút chì gỗ 2B ( Hộp 10 chiếc )'),
(273, '247', 'BG01', 1, 7000.00, 5600.00, 5600.00, 'but gel.png', NULL, 'Bút gel BG01'),
(274, '248', 'BL001', 1, 190000.00, 175000.00, 175000.00, 'balo.png', NULL, 'Ba lô chống gù Siêu nhân'),
(275, '249', 'BX02', 5, 13000.00, 10400.00, 52000.00, 'but xoa 7ml.png', NULL, 'Bút xóa 7ml'),
(276, '250', 'BL001', 1, 190000.00, 175000.00, 175000.00, 'balo.png', NULL, 'Ba lô chống gù Siêu nhân');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `pages`
--

CREATE TABLE `pages` (
  `page_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `content` longtext DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `pages`
--

INSERT INTO `pages` (`page_id`, `title`, `slug`, `content`, `status`, `created_at`) VALUES
(1, 'Chính sách đổi trả', 'chinh-sach-doi-tra', 'Quy định đổi hàng  \r\n1. Các trường hợp áp dụng đổi hàng . Áp dụng đổi 01 lần / 01 hoá đơn\r\nSản phẩm đổi nguyên giá có giá trị bằng hoặc lớn hơn sản phẩm được nguyên giá. Khách hàng vui lòng thanh toán phần chênh lệch thêm giữa sản phẩm đổi và được đổi.\r\nSản phẩm bị lỗi trong quá trình vận chuyển như biến dạng, nứt vỡ, trầy xước, ...\r\nGiao sai số lượng, mẫu mã so với đơn hàng.\r\nSản phẩm hết hạn sử dụng.\r\nSản phẩm khuyến mại, giảm giá không được hỗ trợ đổi trả.\r\n2. Điều kiện đổi hàng hoá \r\nSản phẩm còn nguyên vẹn, đầy đủ tem mác theo quy cách ban đầu (trừ trường hợp sản phẩm bị lỗi hoặc bị hư hại trong quá trình vận chuyển). Sản phẩm còn đầy đủ phụ kiện và tặng phẩm đi kèm (nếu có).\r\nSản phẩm chưa qua sử dụng (trừ trường hợp đổi do lỗi kỹ thuật).\r\nĐầy đủ hoá đơn tem trên sản phẩm, thiếu bảo hành (nếu có).\r\nHồng Hà có quyền từ chối việc đổi trả trong trường hợp phát hiện sản phẩm đã bị sử dụng hoặc hư hỏng, bể vỡ do Quý Khách Hàng. \r\nĐối với sản phẩm thiết bị điện tử, tin học áp dụng theo chính sách đổi trả của nhà sản xuất.\r\n3. Thực hiện trả hàng \r\nQuý Khách tự vận chuyển hàng hoá kèm thèo hoá đơn / chứng từ đến cửa hàng gần nhất của Hồng Hà. \r\nTrong trường hợp Quý khách không thể tự vận chuyển hàng đổi đến địa chỉ tiếp nhận như trên, vui lòng liên hệ vợi chúng tôi qua số điện thoại 024 2246 2003 để được hỗ trợ nhận hàng đổi trả tại địa chỉ của Quý khách (Quý khách chịu chi phí vận chuyển). \r\nThời gian đổi hàng bằng với  \"Thời gian giao hàng\".\r\n Quy định trả hàng \r\n1. Chính sách trả hàng\r\nÁp dụng đối với sản phẩm, hàng hoá lỗi kĩ thuật do Nhà sản xuất.\r\nĐối với các lỗi kĩ thuật khó hoặc không xác định được bằng vác phương pháp thông thường, cửa hàng lập phiếu tiếp nhận hàng hoá và yêu cầu của khách hàng, gửi các đơn vị liên quan kiểm tra và trả lời khách hàng trong vòng 7 ngày, kể từ ngày nhận.\r\n2. Điều kiện trả hàng \r\nCòn hoá đơn mua hàng, tem bảo hành, hướng dẫn sử dụng.\r\nHồng Hà nhận trả hàng trong vòng 3 ngày, kể từ ngày khách hàng nhận được hàng (Căn cứ vào thời gian trên phiếu giao hàng).\r\n3. Thực hiện trả hàng \r\nBước 1 : Gửi thông tin mã hàng , số hoá đơn qua email : dvkh@vpphongha.com.vn  hoặc liên hệ qua số Hotline: 024 2246 2003\r\n\r\nBước 2 : Gửi sản phẩm lỗi đến một trong các Cửa hàng của Hồng Hà gần nhất.\r\n\r\nNgay sau khi Hồng Hà nhận được sản phẩm lỗi sẽ tiến hành kiểm tra nguyên nhân và thực hiện việc đổi sản phẩm mới , đạt chất lượng cho khách hàng hoặc hoàn tiền.\r\nThời gian trả hàng bằng \"Thời gian giao hàng\" (Không bao gồm thời gian xác định nguyên nhân lỗi của sản phẩm).', 'active', '2025-12-04 00:35:54'),
(2, 'Cam Kết Chính Hãng', 'cam-ket-chinh-hang', 'Cam Kết Chính Hãng 100%\r\nTất cả các mặt hàng bán tại Văn Phòng Phẩm Hồng Hà đều đi kèm cam kết bán hàng chính hãng hoàn tiền 100% nếu phát hiện hàng giả, hàng nhái.\r\n\r\nXuất xứ hàng hóa rõ ràng;\r\nCam kết sản phẩm được kiểm soát theo đúng công bố chất lượng của nhà sản xuất;\r\nĐổi trả 100% sản phẩm lỗi do nhà sản xuất.', 'active', '2025-12-04 00:42:40'),
(3, 'Giới thiệu', 'gioi-thieu', 'Văn phòng phẩm LT – 60 NĂM XÂY DỰNG VÀ PHÁT TRIỂN\r\n\r\nToạ lạc tại một vị trí tuyệt đẹp giữa trung tâm Thủ đô, số 25 phố Lý Thường Kiệt, quận Hoàn Kiếm, Hà Nội. Nơi trước kia là một xưởng sửa chữa xe hơi của Pháp mang tên Stai. Với sự giúp đỡ của Trung Quốc, Nhà máy Văn phòng phẩm LT, nhà máy sản xuất đồ dùng văn phòng đầu tiên của nước Việt Nam Dân Chủ Cộng Hoà, đã chính thức cắt băng khánh thành vào ngày 1/10/1959.\r\n\r\nTrải qua gần 60 năm trưởng thành và phát triển, với bản lĩnh mạnh dạn đổi mới cơ chế, đón đầu áp dụng công nghệ mới; bản lĩnh đột phá, phát huy tính sáng tạo và năng động của tập thể CBCNV, Công ty CP Văn phòng phẩm LT đã trở thành một trong những doanh nghiệp hàng đầu trong lĩnh vực văn phòng phẩm tại Việt Nam. Văn phòng phẩm LT nêu cao chiến lược phát triển:\r\n\r\nTrở thành Công ty hàng đầu của Việt Nam trong lĩnh vực sản xuất, kinh doanh văn phòng phẩm và đồ dùng học tập;\r\n\r\nCung cấp cho khách hàng những sản phẩm, dịch vụ thỏa mãn nhu cầu và hàm chứa yếu tố trách nhiệm xã hội, thân thiện với môi trường;\r\n\r\nKhông ngừng đầu tư phát triển thương hiệu Văn phòng phẩm LT, gắn liền với việc học tập và vì sức khỏe học đường. Không ngừng đầu tư nghiên cứu thị trường văn phòng phẩm, phát triển các sản phẩm văn phòng phẩm phục vụ khối tổ chức và doanh nghiệp;\r\n\r\nĐa dạng hóa sản phẩm theo từng phân khúc thị trường nhằm phục vụ nhu cầu tốt nhất cho học sinh, sinh viên và giới văn phòng với phương châm:\r\n“Văn phòng phẩm LT luôn đồng hành trên mỗi chặng đường phát triển của người Việt”.', 'active', '2025-12-04 01:30:05'),
(4, 'Hướng dẫn đặt hàng', 'huong-dan-dat-hang', 'Để mang đến trải nghiệm mua sắm nhanh chóng và thuận tiện, Văn phòng phẩm LT cung cấp nhiều cách thức đặt hàng khác nhau. Quý khách có thể lựa chọn phương thức phù hợp nhất với nhu cầu của mình theo hướng dẫn dưới đây.\r\n\r\n1. Đặt hàng trực tiếp trên website\r\n\r\nQuý khách thực hiện theo các bước:\r\n\r\nBước 1: Truy cập website và tìm kiếm sản phẩm muốn mua bằng thanh tìm kiếm hoặc chọn theo danh mục.\r\nBước 2: Nhấn vào sản phẩm để xem chi tiết hình ảnh, mô tả, giá và các tùy chọn khác.\r\nBước 3: Chọn số lượng và nhấn nút “Thêm vào giỏ hàng”.\r\nBước 4: Vào Giỏ hàng để kiểm tra lại các sản phẩm đã thêm.\r\nBước 5: Nhấn “Thanh toán” để điền thông tin giao hàng gồm họ tên, số điện thoại, địa chỉ nhận hàng.\r\nBước 6: Chọn phương thức thanh toán phù hợp (COD hoặc chuyển khoản).\r\nBước 7: Xác nhận đơn hàng. Hệ thống sẽ gửi thông báo xác nhận đơn hàng thành công.\r\n\r\n2. Đặt hàng qua Facebook / Zalo\r\n\r\nQuý khách có thể nhắn tin trực tiếp qua:\r\n\r\nFacebook: Văn phòng phẩm LT\r\n\r\nZalo: 0879 999 999\r\n\r\nSau khi nhận được thông tin sản phẩm, số lượng và địa chỉ giao hàng, nhân viên sẽ kiểm tra và xác nhận đơn trong thời gian sớm nhất.\r\n\r\n3. Đặt hàng qua Hotline\r\n\r\nNếu cần tư vấn nhanh hoặc đặt hàng trực tiếp, vui lòng liên hệ:\r\n\r\n📞 Hotline: 0879 999 999\r\n\r\nNhân viên sẽ hỗ trợ kiểm tra tồn kho, báo giá và chốt đơn ngay lập tức.\r\n\r\n4. Phí vận chuyển và thời gian giao hàng\r\n\r\nMiễn phí vận chuyển cho đơn hàng có giá trị từ 300.000đ trở lên trong nội thành.\r\n\r\nCác khu vực còn lại sẽ tính phí theo đơn vị vận chuyển.\r\n\r\nThời gian giao hàng dự kiến từ 1 – 3 ngày tùy khu vực.\r\n\r\n5. Kiểm tra hàng khi nhận\r\n\r\nKhi nhận hàng, Quý khách vui lòng:\r\n\r\nKiểm tra tình trạng bao bì, số lượng và sản phẩm.\r\n\r\nNếu có lỗi do nhà sản xuất hoặc vận chuyển, vui lòng liên hệ ngay để được hỗ trợ đổi trả theo chính sách.\r\n\r\n6. Hỗ trợ khách hàng\r\n\r\nNếu cần tư vấn thêm về sản phẩm hoặc gặp khó khăn khi đặt hàng, Quý khách có thể liên hệ qua:\r\n\r\n📞 Hotline: 0879 999 999\r\n📧 Email: support@vpplt.vn\r\n\r\nVăn phòng phẩm LT luôn sẵn sàng hỗ trợ Quý khách 24/7!', 'active', '2025-12-04 01:44:12'),
(5, 'ABC', 'lien-he', '📍 Địa chỉ: TDH – Hà Nội\r\n📞 Hotline: 0879 999 999\r\n📧 Email: support@vpplt.vn\r\n\r\n🕒 Hỗ trợ 24/7 – Tư vấn nhanh chóng', 'active', '2025-12-04 01:52:43');

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
(9, 'GG20', 'percent', 20.00, 5000.00, 10, 10, '2025-11-04 00:00:00', '2025-12-13 00:00:00', 'active', '2025-11-01 00:00:00'),
(10, 'GG50K', 'amount', 50000.00, 50000.00, 16, 9, '2025-11-01 00:00:00', '2025-12-21 00:00:00', 'active', '2025-11-01 00:00:00'),
(11, 'GG100K', 'amount', 100000.00, 200000.00, 15, 11, '2025-11-01 00:00:00', '2025-11-18 00:00:00', 'deleted', '2025-11-01 00:00:00'),
(12, 'GG5K', 'amount', 2000.00, 1000.00, 16, 9, '2025-11-01 00:00:00', '2025-12-13 00:00:00', 'active', '2025-11-01 00:00:00'),
(13, 'GG30', 'amount', 30000.00, 100000.00, 12, 8, '2025-11-01 00:00:00', '2025-12-21 00:00:00', 'active', '2025-11-01 00:00:00'),
(14, 'GG15K', 'amount', 15000.00, 20000.00, 20, 5, '2025-11-05 00:00:00', '2025-12-21 00:00:00', 'active', '2025-11-05 00:00:00'),
(16, 'GGSS', 'percent', 100.00, 500000.00, 2, 1, '2025-11-05 00:00:00', '2025-11-06 00:00:00', 'deleted', '2025-11-05 00:00:00'),
(17, '123', 'amount', 100000.00, 10000.00, 10, 7, '2025-11-20 00:00:00', '2025-11-30 00:00:00', 'deleted', '2025-11-20 00:00:00');

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
(52, 'BG01', 'GG20', '2025-12-02 13:36:48'),
(53, 'BG02', 'GG20', '2025-12-02 13:49:22'),
(54, 'BG03', 'GG5K', '2025-12-02 13:49:30'),
(55, 'BG04', 'GG20', '2025-12-02 13:53:17'),
(56, 'BG06', 'GG5K', '2025-12-02 14:05:24'),
(57, 'BC01', 'GG20', '2025-12-02 14:07:27'),
(58, 'BC02', 'GG20', '2025-12-02 14:09:01'),
(59, 'BC05', 'GG5K', '2025-12-02 14:13:53'),
(60, 'BX02', 'GG20', '2025-12-02 14:17:07'),
(61, 'BHL02', 'GG5K', '2025-12-02 14:21:27'),
(62, 'BK01', 'GG30', '2025-12-02 14:27:26'),
(63, 'BK02', 'GG30', '2025-12-02 14:28:59'),
(64, 'SBD02', 'GG5K', '2025-12-02 14:48:11'),
(65, 'BL001', 'GG15K', '2025-12-02 14:55:37'),
(66, 'GKT01', 'GG5K', '2025-12-02 15:01:35'),
(67, 'SoGA', 'GG15K', '2025-12-02 15:24:11');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `tblchat`
--

CREATE TABLE `tblchat` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `message` text NOT NULL,
  `last_admin_reply` text DEFAULT NULL,
  `sent_by` enum('user','admin','staff') DEFAULT 'user',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `tblchat`
--

INSERT INTO `tblchat` (`id`, `username`, `message`, `last_admin_reply`, `sent_by`, `created_at`) VALUES
(1, 'baobao@gmail.com', 'tư vấn sản phẩm', 'ok bạn ', 'user', '2025-12-07 18:45:24'),
(2, 'baobao@gmail.com', 'tư vấn sản phẩm', 'ok bạn ', 'user', '2025-12-07 18:45:47'),
(3, 'baobao@gmail.com', 'chào', 'ok bạn ', 'user', '2025-12-07 18:45:55'),
(4, 'baobao@gmail.com', 'chào', 'ok bạn ', 'user', '2025-12-07 19:13:47'),
(5, 'baobao@gmail.com', 'hi', 'ok bạn ', 'user', '2025-12-07 19:13:55'),
(6, 'baobao@gmail.com', '1', 'ok bạn ', 'user', '2025-12-07 19:15:37'),
(7, 'baobao@gmail.com', 'chào', 'ok bạn ', 'user', '2025-12-07 19:28:57'),
(8, 'baobao@gmail.com', 'chào', 'ok bạn ', 'user', '2025-12-07 19:32:27'),
(9, 'baobao@gmail.com', 'chào', 'ok bạn ', 'user', '2025-12-07 19:32:29'),
(10, 'baobao@gmail.com', 'trang', 'ok bạn ', 'user', '2025-12-07 19:43:23'),
(11, 'baobao@gmail.com', 'cảm ơn', 'ok bạn ', 'admin', '2025-12-07 20:12:36'),
(12, 'baobao@gmail.com', 'vâng', 'ok bạn ', 'admin', '2025-12-07 20:22:45'),
(13, 'baobao@gmail.com', 'hỗ trợ nhiệt tình', 'ok bạn ', 'admin', '2025-12-07 20:26:18'),
(14, 'baobao@gmail.com', 'dạ', 'ok bạn ', 'admin', '2025-12-07 20:26:41'),
(15, 'baobao@gmail.com', 'vâng ạ', 'ok bạn ', 'admin', '2025-12-07 20:26:56'),
(16, 'baobao@gmail.com', 'vâbf', 'ok bạn ', 'admin', '2025-12-07 20:27:56'),
(17, 'baobao@gmail.com', 'vâbf', 'ok bạn ', 'admin', '2025-12-07 20:30:13'),
(18, 'baobao@gmail.com', 'vâbf', 'ok bạn ', 'admin', '2025-12-07 20:30:21'),
(19, 'baobao@gmail.com', 'vâbf', 'ok bạn ', 'admin', '2025-12-07 20:31:31'),
(20, 'baobao@gmail.com', 'hai', 'ok bạn ', 'admin', '2025-12-07 20:31:39'),
(21, 'baobao@gmail.com', 'hai', 'ok bạn ', 'admin', '2025-12-07 20:32:09'),
(22, 'baobao@gmail.com', 'hai', 'ok bạn ', 'admin', '2025-12-07 20:32:25'),
(23, 'baobao@gmail.com', 'vâng', 'ok bạn ', 'admin', '2025-12-07 20:32:30'),
(24, 'baobao@gmail.com', 'cảm ơn ạ', 'ok bạn ', 'admin', '2025-12-07 20:34:13'),
(25, 'baobao@gmail.com', 'oki', 'ok bạn ', 'user', '2025-12-07 20:46:28'),
(26, 'baobao@gmail.com', 'không sao ạ', 'ok bạn ', 'admin', '2025-12-07 20:47:41'),
(27, 'baobao@gmail.com', 'ok', 'ok bạn ', 'user', '2025-12-07 20:48:42'),
(28, 'baobao@gmail.com', 'gửi sớm cho mình', 'ok bạn ', 'user', '2025-12-07 20:49:08'),
(29, 'baobao@gmail.com', 'vâng ạ\r\n', 'ok bạn ', 'admin', '2025-12-07 20:49:25'),
(30, 'baobao@gmail.com', 'hì', 'ok bạn ', 'admin', '2025-12-07 20:57:15'),
(31, 'baobao@gmail.com', 'cảm ơn', 'ok bạn ', 'admin', '2025-12-07 20:58:58'),
(32, 'baobao@gmail.com', 'cảm ơn', 'ok bạn ', 'admin', '2025-12-07 20:59:33'),
(33, 'baobao@gmail.com', 'cảm ơn', 'ok bạn ', 'admin', '2025-12-07 20:59:42'),
(34, 'baobao@gmail.com', 'cảm ơn', 'ok bạn ', 'admin', '2025-12-07 20:59:54'),
(35, 'baobao@gmail.com', 'cảm ơn', 'ok bạn ', 'admin', '2025-12-07 21:00:55'),
(36, 'baobao@gmail.com', 'ok bạn ', 'ok bạn ', 'admin', '2025-12-07 21:01:29'),
(37, 'baobao@gmail.com', 'vậy ạ', NULL, 'user', '2025-12-07 21:11:59'),
(38, 'baobao@gmail.com', 'em không biwe', NULL, 'user', '2025-12-07 21:12:07'),
(39, 'baochanbon@gmail.com', 'hi', 'khong', 'user', '2025-12-07 21:25:43'),
(40, 'baochanbon@gmail.com', 'mình đây', 'khong', 'admin', '2025-12-07 21:25:53'),
(41, 'baochanbon@gmail.com', 'mình xin sdt', 'khong', 'user', '2025-12-07 21:28:54'),
(42, 'baochanbon@gmail.com', 'dạ', 'khong', 'user', '2025-12-07 21:33:46'),
(43, 'baochanbon@gmail.com', 'vâng? ', 'khong', 'admin', '2025-12-07 21:34:24'),
(44, 'baochanbon@gmail.com', 'vâng? ', 'khong', 'admin', '2025-12-07 21:34:57'),
(45, 'baochanbon@gmail.com', 'vâng? ', 'khong', 'admin', '2025-12-07 21:35:12'),
(46, 'baochanbon@gmail.com', 'sao', 'khong', 'user', '2025-12-07 21:35:40'),
(47, 'baochanbon@gmail.com', 'khong', 'khong', 'admin', '2025-12-07 21:35:49');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `tblct_ncc_sanpham`
--

CREATE TABLE `tblct_ncc_sanpham` (
  `id` int(11) NOT NULL,
  `maNCC` varchar(20) NOT NULL,
  `masp` varchar(50) NOT NULL,
  `giaNhapNCC` bigint(20) DEFAULT NULL,
  `thongTinThem` varchar(255) DEFAULT NULL,
  `ngayCapNhat` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `tblhopdong`
--

CREATE TABLE `tblhopdong` (
  `maHD` varchar(30) NOT NULL,
  `maNCC` varchar(20) NOT NULL,
  `tenHD` varchar(200) DEFAULT NULL,
  `ngayKy` date DEFAULT NULL,
  `ngayHetHan` date DEFAULT NULL,
  `giaTri` bigint(20) DEFAULT 0,
  `trangThai` enum('dang_hieu_luc','het_hieu_luc','khong_hieu_luc') DEFAULT 'dang_hieu_luc',
  `noiDung` text DEFAULT NULL,
  `createDate` datetime DEFAULT current_timestamp(),
  `updatedAt` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `tblhopdong`
--

INSERT INTO `tblhopdong` (`maHD`, `maNCC`, `tenHD`, `ngayKy`, `ngayHetHan`, `giaTri`, `trangThai`, `noiDung`, `createDate`, `updatedAt`) VALUES
('HD1', 'NCC1', 'HỢP ĐỒNG MUA BÁN BÚT CHÌ', '2025-12-04', '2025-12-26', 5000000, 'dang_hieu_luc', 'MUA BÁN 500 BÚT CHÌ ', '2025-12-04 21:12:50', NULL),
('HD2', 'NCC1', 'HỢP ĐỒNG MUA BÁN BA LÔ', '2025-12-04', '2025-12-27', 30000000, 'dang_hieu_luc', 'OK', '2025-12-04 21:21:26', NULL);

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
('BB01', 'Bút bi - Bút gel', 'Nhóm bút bi và bút gel chất lượng cao, viết êm, mực ra đều, phù hợp cho học sinh, sinh viên và nhân viên văn phòng. Nhiều màu sắc và kiểu dáng để lựa chọn.', '2025-11-28 21:02:03', '2025-11-28 21:02:38'),
('BB02', 'Bút chì', 'Các loại bút chì viết và vẽ chất lượng cao, nét rõ, êm và dễ kiểm soát. Phù hợp cho học sinh, sinh viên, nhân viên văn phòng và người dùng sáng tạo.', '2025-11-28 21:03:25', '2025-11-28 21:03:25'),
('BB03', 'Bút xóa', 'Bút xóa mực nhanh khô, che phủ tốt, giúp sửa lỗi viết dễ dàng và sạch sẽ. Phù hợp cho học sinh, sinh viên và nhân viên văn phòng sử dụng hằng ngày', '2025-11-28 21:07:55', '2025-11-28 21:08:02'),
('BDB', 'Bút dạ bảng/ Bút lông dầu', '', '2025-12-02 14:22:06', '2025-12-02 14:22:06'),
('BHL', 'Bút Highlight', '', '2025-12-02 14:17:56', '2025-12-02 14:17:56'),
('BK', 'Bút ký cao cấp', '', '2025-12-02 14:25:37', '2025-12-02 14:25:37'),
('BLTUI', 'Ba lô/ Túi/ Cặp', '', '2025-12-02 14:52:16', '2025-12-02 14:52:16'),
('BM22', 'Bút máy', '', '2025-12-02 14:51:16', '2025-12-02 14:51:16'),
('DCHT', 'Dụng cụ học tập ', '', '2025-12-02 14:51:28', '2025-12-02 14:51:28'),
('HP00', 'Họa phẩm', '', '2025-12-02 14:51:47', '2025-12-02 14:51:47'),
('MT_00', 'Máy tính', '', '2025-12-02 14:52:31', '2025-12-02 14:52:31'),
('SBC', 'Sổ Bìa Còng', '', '2025-11-28 21:38:54', '2025-12-02 14:30:08'),
('SBD', 'Sổ Bìa Da', '', '2025-11-28 21:38:25', '2025-12-02 14:30:00'),
('SDA00', 'Sổ giáo án', '', '2025-12-02 14:49:01', '2025-12-02 14:49:01'),
('SSTT ', 'Sách / Truyện', '', '2025-12-02 14:52:52', '2025-12-02 14:52:52'),
('TK11', 'Trình ký', '', '2025-12-02 14:49:43', '2025-12-02 14:49:43'),
('VV0123', 'Vở viết', '', '2025-12-02 14:51:07', '2025-12-02 14:51:07');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `tblnhacungcap`
--

CREATE TABLE `tblnhacungcap` (
  `maNCC` varchar(20) NOT NULL,
  `tenNCC` varchar(150) NOT NULL,
  `diaChi` varchar(255) DEFAULT NULL,
  `sdt` varchar(30) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `nguoiLH` varchar(100) DEFAULT NULL,
  `ghiChu` text DEFAULT NULL,
  `createDate` datetime DEFAULT current_timestamp(),
  `updatedAt` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `tblnhacungcap`
--

INSERT INTO `tblnhacungcap` (`maNCC`, `tenNCC`, `diaChi`, `sdt`, `email`, `nguoiLH`, `ghiChu`, `createDate`, `updatedAt`) VALUES
('NCC1', 'Hồng Hà', 'hai bà trưng, Hà Nội', '0987654321', 'hongha@gmail.com', '', 'Nhập hàng thứ 2', '2025-12-04 18:15:44', NULL),
('NCC2', 'Hữu Lan', 'hải phòng', '0879119493', '', '', '', '2025-12-05 20:09:31', NULL);

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
('BB02', 'BC01', 'Bút chì gỗ 2B ( Hộp 10 chiếc )', 'but chi go 2b.png', 17, 30, 20000, 40000, 0, 'Thân gỗ lục giác\r\nKích thước: Φ7,6 x 178mm (đường kính x chiều dài bút)\r\nMàu sắc: Bạc\r\nQuy cách: 12 chiếc/ hộp', '2025-12-02'),
('BB02', 'BC02', 'Bút chì Kim Sliver', 'but chi kim.png', 33, 35, 8000, 20000, 0, 'Nhãn hiệu: Hồng Hà\r\nNgòi chì: 0.7 mm\r\nChất liệu: Kim loại ( nhôm, thép) , nhựa & Than chì\r\nKích thước: Φ 8 x 140mm ( đường kính x chiều dài bút)\r\nMàu sắc: Ánh bạc', '2025-12-02'),
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
('BLTUI', 'BL001', 'Ba lô chống gù Siêu nhân', 'balo.png', 18, 20, 90000, 190000, 0, 'Thương hiệu: Hồng Hà\r\nChất liệu: vải polyester 600PU\r\nKích thước: 35x31x19 cm\r\nKết cấu: 01 ngăn chính, 01 ngăn phụ + khóa kéo\r\nMàu sắc: Xanh dương\r\nTrọng lượng: 0.65 kg', '2025-12-02'),
('BLTUI', 'BL002', 'Ba lô mầm non', 'balo2.png', 47, 50, 150000, 360000, 0, 'Chất liệu: vải polyester 900PU\r\nKích thước: 30x25x10 cm\r\nKết cấu: 01 ngăn chính, 01 ngăn phụ + khóa kéo\r\nTrọng lượng: 0.26 kg', '2025-12-02'),
('BM22', 'BM01', 'Bút máy Hồng Hà', 'but may.png', 30, 30, 20000, 40000, 0, 'Chất liệu: Kim loại\r\nNgòi bút: Ngòi mài chuyên dụng\r\nChu vi piston: Φ3.4mm\r\nĐóng gói: 01 chiếc/hộp nhỏ, 12 chiếc/hộp trưng bày, 480 chiếc/thùng', '2025-12-02'),
('BM22', 'BM02', 'Ngòi bút máy nét trơn', 'ngoi but.png', 20, 20, 1000, 3000, 0, 'Ngòi nét trơn nét 0.38mm dùng để viết  chữ, phù hợp cho giáo viên tiểu học, các bạn học sinh cấp I, mẫu giáo lớn.\r\nChất liệu thép không gỉ.\r\nĐầu ngòi đính hạt Iridium', '2025-12-02'),
('BB01', 'BS2', 'Bút sáp vặn Minions 12 màu', 'Array', 10, 10, 32000, 65000, 0, 'Kích thước bút: Ø9,8x157,7 (±2mm)\r\nKích thước sáp màu: Ø5,6x131 (±2mm) \r\nKhối lượng: 9,3g\r\nThương hiệu: Văn phòng phẩm Hồng Hà', '2025-12-02'),
('DCHT', 'BV1', 'Bọc sách, vở nylon Hồng Hà dạng cuộn 170x240mm', 'bocvo.png', 15, 15, 6000, 15000, 0, 'Chất liệu: Nhựa CPP \r\nKích thước bọc: 170 x 240 mm\r\nMàu sắc: Trong suốt\r\nThương hiệu: Hồng Hà\r\nSố lượng 10 tờ bọc/ cuộn', '2025-12-02'),
('BB03', 'BX01', 'Bút Xóa Hồng hà', 'but xoa.png', 20, 20, 5000, 12000, 0, 'Kích thước thân bút: 25 x19 x 103 mm (D x R x C)\r\nMàu sắc thân bút: Xanh\r\nDung tích: 12 ml', '2025-12-02'),
('BB03', 'BX02', 'Bút xóa 7ml', 'but xoa 7ml.png', 0, 15, 4000, 13000, 0, 'Kích thước thân bút: 21 x17 x 128 mm (D x R x C)\r\nMàu sắc thân bút: Xanh, Hồng Vàng\r\nDung tích: 7 ml', '2025-12-02'),
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
  `role` enum('admin','staff','user') NOT NULL DEFAULT 'user',
  `status` enum('Hoạt động','Tạm ngưng') DEFAULT 'Hoạt động',
  `is_deleted` tinyint(1) DEFAULT 0,
  `avatar` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `tbluser`
--

INSERT INTO `tbluser` (`user_id`, `fullname`, `email`, `password`, `is_verified`, `verification_token`, `created_at`, `role`, `status`, `is_deleted`, `avatar`, `phone`, `address`) VALUES
(24, 'Trang admin', 'trag@gmail.com', '$2y$10$EJFuPx/HA5o5QvtbubzKfufbnp9laK.8km10o7d3ZLhUoEt/Q1Obq', 0, 2, '2025-10-15 20:19:19', 'admin', '', 1, NULL, NULL, NULL),
(27, 'Trang user', 'baochanbon@gmail.com', '$2y$10$uxKGhHlR0CicdJ4hkvM21.nH7KHcAkQlNmv3mzfz7Cvv0E23x2iQu', 0, 0, '2025-11-13 16:44:13', 'user', 'Hoạt động', 0, 'hinh-anh-gau-truc-lon-ngo-nghinh_121845367_1.jpg', '08789119493', 'Nam Tu Liem, Ha Noi'),
(28, 'BAO admin', 'bao@gmail.com', '$2y$10$o4Drz.uyE8aeBM3fFiW3nOWn.bjVywX9AlpC.rfpZoaX/eHjhIYVK', 0, 0, '2025-11-13 17:09:39', 'admin', 'Hoạt động', 0, NULL, NULL, NULL),
(29, 'Bon user', 'bon@gmail.com', '$2y$10$mhKszyStFskEauSwCe.R9u4OPP0OZiqcvOE8XNi7T2QCvNvz1Uhz6', 0, 0, '2025-11-16 00:22:46', 'user', 'Hoạt động', 1, NULL, NULL, NULL),
(30, 'long', 'builong111104@gmail.com', '$2y$10$Jsf8ybPLB3t4t/cF1jsd9OLjCT9PWAMYpqlFjyVIMeNVpM79dpFJa', 0, 0, '2025-11-16 10:39:39', 'user', 'Hoạt động', 0, 'istockphoto-467584493-612x612_1.jpg', '0999999997', 'ssđs'),
(31, 'longAmin', 'builong11112004@gmail.com', '$2y$10$qBauHKK2fm/HImIwlqzYqOui6GHk0z.Hn/h3FAPEDY0pP9ckcnUaa', 0, 0, '2025-11-16 11:59:57', 'admin', 'Hoạt động', 0, NULL, NULL, NULL),
(33, 'Baobaobao', 'baobao@gmail.com', '$2y$10$QD0r49PLqKtX29AV7gg0o.vT18ZnZOcSW94NvD5ieUsWRP0rsh2h6', 0, 0, '2025-11-25 23:56:06', 'user', 'Hoạt động', 0, 'vit-meme-yodyvn31_1.jpg', NULL, NULL),
(34, 'tret', 'b@gmail.com', '', 0, 0, '2025-12-05 21:41:54', 'admin', 'Hoạt động', 1, NULL, 'êtr', 'tẻt'),
(35, 'Mèo', 'meo@gmail.com', '$2y$10$1l9.6WORB.IlXsPpHLptfOxJJcCc4NmdbK3g5kQn5GNKo0TV55Foi', 0, 0, '2025-12-05 21:57:36', 'staff', 'Hoạt động', 0, 'hinh-anh-meo-khoc-thet_1.jpeg', '0999999999', 'Hai ba Trung');

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
  `traloi` text DEFAULT NULL,
  `images` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `tbl_danhgia`
--

INSERT INTO `tbl_danhgia` (`id`, `masp`, `tenNguoiDung`, `email`, `noidung`, `sao`, `trangthai`, `ngayDang`, `traloi`, `images`) VALUES
(2, 'ip17', 'BAO3', 'baochanbon@gmail.com', '565', 1, 1, '2025-11-08', 'ok', NULL),
(3, 'ip17', 'BAO2', 'baochanbon@gmail.com', '45', 4, 1, '2025-11-08', NULL, NULL),
(4, 'BUT01_TL', 'huy88gh', 'builong111104@gmail.com', 'ygh8h', 5, 1, '2025-11-16', NULL, NULL),
(5, 'BUT01_TL', 'ưeqwe', 'builong111104@gmail.com', 'qưeqwe', 5, 1, '2025-11-16', NULL, NULL),
(6, 'BC01', 'long\r\n', 'builong111104@gmail.com', 'ádasdas', 5, 1, '2025-12-04', NULL, '[\"6c39152b79092742_1764864556.png\"]'),
(7, 'BC01', 'long\r\n', 'builong111104@gmail.com', 'qưqewqewqw', 5, 0, '2025-12-04', NULL, NULL),
(8, 'BC01', 'long\r\n', 'builong111104@gmail.com', '1234567890', 5, 0, '2025-12-04', NULL, '[\"6b5099c926a1b031_1764867149.png\"]'),
(9, 'BC01', 'long\r\n', 'builong111104@gmail.com', 'love', 4, 1, '2025-12-04', NULL, '[\"97fb98683b75a0ae_1764867169.png\"]'),
(10, 'BX02', 'Trang user', 'baochanbon@gmail.com', 'ok z', 5, 1, '2025-12-05', NULL, NULL),
(11, 'BC01', 'long', 'builong111104@gmail.com', 'ádasdasdafvdfafadf', 1, 1, '2025-12-07', NULL, '[\"15d1ef620e61b638_1765113865.png\"]');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `wishlist`
--

CREATE TABLE `wishlist` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` varchar(191) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `wishlist`
--

INSERT INTO `wishlist` (`id`, `user_id`, `product_id`, `created_at`) VALUES
(22, 27, 'BCC', '2025-12-05 11:33:14'),
(23, 27, 'BDB01', '2025-12-05 11:33:15');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `banner_images`
--
ALTER TABLE `banner_images`
  ADD PRIMARY KEY (`img_id`),
  ADD KEY `banner_id` (`banner_id`);

--
-- Chỉ mục cho bảng `banner_sets`
--
ALTER TABLE `banner_sets`
  ADD PRIMARY KEY (`banner_id`);

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
-- Chỉ mục cho bảng `pages`
--
ALTER TABLE `pages`
  ADD PRIMARY KEY (`page_id`),
  ADD UNIQUE KEY `slug` (`slug`);

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
-- Chỉ mục cho bảng `tblchat`
--
ALTER TABLE `tblchat`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `tblct_ncc_sanpham`
--
ALTER TABLE `tblct_ncc_sanpham`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ux_ncc_sp` (`maNCC`,`masp`),
  ADD KEY `fk_ctncc_sp` (`masp`);

--
-- Chỉ mục cho bảng `tblhopdong`
--
ALTER TABLE `tblhopdong`
  ADD PRIMARY KEY (`maHD`),
  ADD KEY `fk_hopdong_ncc` (`maNCC`);

--
-- Chỉ mục cho bảng `tblloaisp`
--
ALTER TABLE `tblloaisp`
  ADD PRIMARY KEY (`maLoaiSP`);

--
-- Chỉ mục cho bảng `tblnhacungcap`
--
ALTER TABLE `tblnhacungcap`
  ADD PRIMARY KEY (`maNCC`),
  ADD UNIQUE KEY `uk_ncc_email` (`email`);

--
-- Chỉ mục cho bảng `tblsanpham`
--
ALTER TABLE `tblsanpham`
  ADD PRIMARY KEY (`masp`),
  ADD KEY `idx_masp` (`masp`);

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
-- Chỉ mục cho bảng `wishlist`
--
ALTER TABLE `wishlist`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_product_unique` (`user_id`,`product_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `banner_images`
--
ALTER TABLE `banner_images`
  MODIFY `img_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT cho bảng `banner_sets`
--
ALTER TABLE `banner_sets`
  MODIFY `banner_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `news`
--
ALTER TABLE `news`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT cho bảng `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=251;

--
-- AUTO_INCREMENT cho bảng `order_details`
--
ALTER TABLE `order_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=277;

--
-- AUTO_INCREMENT cho bảng `pages`
--
ALTER TABLE `pages`
  MODIFY `page_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `promo_codes`
--
ALTER TABLE `promo_codes`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT cho bảng `promo_product`
--
ALTER TABLE `promo_product`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=68;

--
-- AUTO_INCREMENT cho bảng `tblchat`
--
ALTER TABLE `tblchat`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT cho bảng `tblct_ncc_sanpham`
--
ALTER TABLE `tblct_ncc_sanpham`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `tbluser`
--
ALTER TABLE `tbluser`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT cho bảng `tbl_danhgia`
--
ALTER TABLE `tbl_danhgia`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT cho bảng `wishlist`
--
ALTER TABLE `wishlist`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `banner_images`
--
ALTER TABLE `banner_images`
  ADD CONSTRAINT `banner_images_ibfk_1` FOREIGN KEY (`banner_id`) REFERENCES `banner_sets` (`banner_id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `promo_product`
--
ALTER TABLE `promo_product`
  ADD CONSTRAINT `fk_product` FOREIGN KEY (`masp`) REFERENCES `tblsanpham` (`masp`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_promo` FOREIGN KEY (`promo_code`) REFERENCES `promo_codes` (`code`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `tblct_ncc_sanpham`
--
ALTER TABLE `tblct_ncc_sanpham`
  ADD CONSTRAINT `fk_ctncc_ncc` FOREIGN KEY (`maNCC`) REFERENCES `tblnhacungcap` (`maNCC`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_ctncc_sp` FOREIGN KEY (`masp`) REFERENCES `tblsanpham` (`masp`) ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `tblhopdong`
--
ALTER TABLE `tblhopdong`
  ADD CONSTRAINT `fk_hopdong_ncc` FOREIGN KEY (`maNCC`) REFERENCES `tblnhacungcap` (`maNCC`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
