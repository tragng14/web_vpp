<?php
session_start();

define('HOME_URL', 'https://tragng14-webvpp.infinityfreeapp.com/index.php');

define('DEFAULT_AMOUNT', 270000);

$orderCode = $_SESSION['orderCode'] ?? ('HD' . time());

$amountRaw = $_SESSION['checkout']['amount'] ?? $_SESSION['totalAmount'] ??
             $_GET['amount'] ?? $_POST['amount'] ?? DEFAULT_AMOUNT;
$amount = floatval($amountRaw);
$amountFormatted = number_format($amount, 0, ',', '.');

$checkout = $_SESSION['checkout'] ?? [];
$user = $_SESSION['user'] ?? [];

$prefName  = $checkout['receiver'] ?? $user['fullname'] ?? ($_POST['receiver'] ?? '');
$prefPhone = $checkout['phone'] ?? $user['phone'] ?? ($_POST['phone'] ?? '');
$prefEmail = $checkout['email'] ?? $user['email'] ?? ($_POST['email'] ?? '');
$prefAddr  = $checkout['address'] ?? ($_POST['address'] ?? '');

// Escape helper
$esc = fn($v) => htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');

header("Cache-Control: no-cache, must-revalidate");
?>
<!doctype html>
<html lang="vi">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Thanh toán VNPAY</title>

<link href="/vnpay_php/assets/bootstrap.min.css" rel="stylesheet"/>
<script src="/vnpay_php/assets/jquery-1.11.3.min.js"></script>

<style>
    body{background:#f4f7fb;font-family:Inter,Roboto,Arial;color:#111;margin:20px;}
    .container{max-width:900px;margin:0 auto;}
    .card{background:#fff;padding:20px;border-radius:12px;border:1px solid #eef2f7;box-shadow:0 8px 24px rgba(0,0,0,0.05);margin-bottom:20px;}

    .summary-title{font-weight:700;margin-bottom:12px;font-size:18px;}
    .summary-row{display:flex;gap:10px;margin-bottom:6px;}
    .summary-label{width:140px;font-weight:700;color:#111827;}
    .summary-value{color:#111;}
    .summary-total{font-size:28px;font-weight:800;color:#d63384;margin-top:12px;}

    .method-block{
        background:#fbfdff;
        padding:12px;
        border-radius:8px;
        border:1px solid #eef4fb;
        margin-top:16px;
    }

    .btn-primary-custom{background:#0d6efd;color:#fff;border:none;padding:10px 18px;border-radius:8px;font-weight:700;}
    .btn-ghost{padding:10px 18px;border-radius:8px;border:1px solid #dfe6ef;color:#6b7280;}

    .hidden-form{display:none;}
</style>
</head>
<body>

<div class="container">

    <h3 class="mb-1">Tạo đơn và Thanh toán</h3>
    

    <!-- FORM ẨN (giữ chức năng submit sang VNPAY) -->
    <form id="frmCreateOrder" class="hidden-form" action="vnpay_create_payment.php" method="post">
        <input type="hidden" name="orderCode" value="<?php echo $esc($orderCode); ?>">
        <input type="hidden" name="amount" value="<?php echo $esc($amount); ?>">

        <input type="hidden" name="receiver" value="<?php echo $esc($prefName); ?>">
        <input type="hidden" name="phone" value="<?php echo $esc($prefPhone); ?>">
        <input type="hidden" name="email" value="<?php echo $esc($prefEmail); ?>">
        <input type="hidden" name="address" value="<?php echo $esc($prefAddr); ?>">

        <input type="hidden" name="bankCode" id="bankCode_hidden" value="">
        <input type="hidden" name="language" id="language_hidden" value="vn">
    </form>

    <!-- TÓM TẮT ĐƠN HÀNG -->
    <div class="card">
        <div class="summary-title">Tóm tắt đơn hàng</div>

        <div class="summary-row"><div class="summary-label">Mã đơn:</div><div class="summary-value"><?php echo $esc($orderCode); ?></div></div>
        <div class="summary-row"><div class="summary-label">Họ và tên:</div><div class="summary-value"><?php echo $esc($prefName ?: 'Khách hàng'); ?></div></div>
        <div class="summary-row"><div class="summary-label">Số điện thoại:</div><div class="summary-value"><?php echo $esc($prefPhone ?: '-'); ?></div></div>
        <div class="summary-row"><div class="summary-label">Email:</div><div class="summary-value"><?php echo $esc($prefEmail ?: '-'); ?></div></div>
        <div class="summary-row"><div class="summary-label">Địa chỉ:</div><div class="summary-value"><?php echo $esc($prefAddr ?: '-'); ?></div></div>

        <div class="summary-total"><?php echo $esc($amountFormatted); ?> ₫</div>


        <!-- KHUNG CHỌN PHƯƠNG THỨC THANH TOÁN (GIỐNG CŨ) -->
        <div class="method-block">
            <h5 style="margin:0 0 8px 0;font-weight:700;">Phương thức thanh toán</h5>

            <div class="form-check">
                <input class="form-check-input" type="radio" name="pm_bank" value="" checked>
                <label class="form-check-label">Chuyển hướng sang cổng VNPAY</label>
            </div>

            <div class="form-check mt-2">
                <input class="form-check-input" type="radio" name="pm_bank" value="VNPAYQR">
                <label class="form-check-label">VNPAYQR</label>
            </div>

            <div class="form-check mt-2">
                <input class="form-check-input" type="radio" name="pm_bank" value="VNBANK">
                <label class="form-check-label">Thẻ ATM / Ngân hàng nội địa</label>
            </div>

            <div class="form-check mt-2">
                <input class="form-check-input" type="radio" name="pm_bank" value="INTCARD">
                <label class="form-check-label">Thẻ quốc tế</label>
            </div>

            <hr style="margin:12px 0;">

            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="pm_lang" value="vn" checked>
                <label class="form-check-label">Tiếng Việt</label>
            </div>

            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="pm_lang" value="en">
                <label class="form-check-label">Tiếng Anh</label>
            </div>
        </div>

        <hr>

        <p style="font-weight:700;color:#6b7280">Ghi chú</p>
        <ul style="color:#444">
            <li>Thanh toán an toàn qua VNPAY.</li>
            <li>Hỗ trợ thẻ nội địa, quốc tế & Internet Banking.</li>
        </ul>

        

        <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:20px;">
            <a class="btn-ghost" href="<?php echo HOME_URL; ?>">Quay lại cửa hàng</a>
            <button id="btnPayVisible" class="btn-primary-custom">Thanh toán</button>
        </div>
    </div>
</div>

<script>
// Copy lựa chọn xuống form ẩn rồi submit
(function(){
    const btn = document.getElementById('btnPayVisible');
    const form = document.getElementById('frmCreateOrder');

    btn.addEventListener('click', function(e){
        e.preventDefault();

        // Lấy bankCode
        const bank = document.querySelector('input[name="pm_bank"]:checked');
        document.getElementById('bankCode_hidden').value = bank ? bank.value : '';

        // Lấy language
        const lang = document.querySelector('input[name="pm_lang"]:checked');
        document.getElementById('language_hidden').value = lang ? lang.value : 'vn';

        // Submit form ẩn để gửi lên vnpay_create_payment.php
        form.submit();
    });
})();
</script>

</body>
</html>
