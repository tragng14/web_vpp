<?php
session_start(); // ⚠️ Bắt buộc có để dùng được $_SESSION

error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
date_default_timezone_set('Asia/Ho_Chi_Minh');

/**
 * Tích hợp thanh toán VNPAY
 * Tác giả gốc: CTT VNPAY
 * Đã chỉnh sửa bởi ChatGPT để hỗ trợ truyền mã giảm giá
 */

require_once("./config.php");

// ========================
// 1️⃣ Lấy thông tin đơn hàng
// ========================
$vnp_TxnRef = $_SESSION['orderCode'];  // Mã giao dịch tham chiếu
$vnp_Amount = $_POST['amount'];        // Số tiền thanh toán (đơn vị: VNĐ)
$vnp_Locale = $_POST['language'];      // Ngôn ngữ hiển thị
$vnp_BankCode = $_POST['bankCode'];    // Mã ngân hàng (nếu có)
$vnp_IpAddr = $_SERVER['REMOTE_ADDR']; // IP của khách hàng

// ========================
// 2️⃣ Xử lý mã giảm giá (nếu có)
// ========================
$orderInfo = "Thanh toan GD:" . $vnp_TxnRef;

// Nếu có mã giảm giá đang lưu trong session (toàn đơn hàng)
if (!empty($_SESSION['validDiscountCode'])) {
    $orderInfo .= "|MAKM=" . $_SESSION['validDiscountCode']; // nối thêm vào nội dung gửi VNPAY
}

// ========================
// 3️⃣ Tạo dữ liệu gửi sang VNPAY
// ========================
$inputData = array(
    "vnp_Version"    => "2.1.0",
    "vnp_TmnCode"    => $vnp_TmnCode,
    "vnp_Amount"     => $vnp_Amount * 100, // nhân 100 theo quy định của VNPAY
    "vnp_Command"    => "pay",
    "vnp_CreateDate" => date('YmdHis'),
    "vnp_CurrCode"   => "VND",
    "vnp_IpAddr"     => $vnp_IpAddr,
    "vnp_Locale"     => $vnp_Locale,
    "vnp_OrderInfo"  => $orderInfo, // ✅ có mã giảm giá kèm theo
    "vnp_OrderType"  => "other",
    "vnp_ReturnUrl"  => $vnp_Returnurl,
    "vnp_TxnRef"     => $vnp_TxnRef,
    "vnp_ExpireDate" => $expire
);

if (!empty($vnp_BankCode)) {
    $inputData['vnp_BankCode'] = $vnp_BankCode;
}

// ========================
// 4️⃣ Tạo URL và mã bảo mật
// ========================
ksort($inputData);
$query = "";
$hashdata = "";
$i = 0;
foreach ($inputData as $key => $value) {
    if ($i == 1) {
        $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
    } else {
        $hashdata .= urlencode($key) . "=" . urlencode($value);
        $i = 1;
    }
    $query .= urlencode($key) . "=" . urlencode($value) . '&';
}

$vnp_Url = $vnp_Url . "?" . $query;

if (!empty($vnp_HashSecret)) {
    $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
    $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
}

// ========================
// 5️⃣ Ghi log (tùy chọn - kiểm tra)
// ========================
// file_put_contents('vnpay_debug.log', date('Y-m-d H:i:s') . " => URL: $vnp_Url\nOrderInfo: $orderInfo\n", FILE_APPEND);

// ========================
// 6️⃣ Chuyển hướng sang cổng thanh toán
// ========================
header('Location: ' . $vnp_Url);
exit;
