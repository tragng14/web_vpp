<?php
// views/Font_end/checkoutView.php (hoặc file tương ứng)
// Lưu ý: session phải được start ở controller hoặc view này
if (session_status() === PHP_SESSION_NONE) {
    @session_start();
}

// ưu tiên lấy từ $data['user'] nếu controller truyền, nếu không thì từ session
$user = $data['user'] ?? ($_SESSION['user'] ?? null);

$receiverVal = '';
$emailVal = '';
$phoneVal = '';
$addressVal = '';

if ($user) {
    $receiverVal = htmlspecialchars($user['fullname'] ?? '', ENT_QUOTES, 'UTF-8');
    $emailVal    = htmlspecialchars($user['email']    ?? '', ENT_QUOTES, 'UTF-8');
    // phone/address có thể chưa có -> mặc định rỗng
    $phoneVal    = htmlspecialchars($user['phone']    ?? '', ENT_QUOTES, 'UTF-8');
    $addressVal  = htmlspecialchars($user['address']  ?? '', ENT_QUOTES, 'UTF-8');
}
?>

<div class="container mt-5">
    <h2>Thông tin giao hàng</h2>

    <form id="checkoutForm" action="<?php echo APP_URL; ?>/Home/checkoutSave" method="POST" novalidate>
        <div class="mb-3">
            <label for="receiver" class="form-label">Tên người nhận</label>
            <input type="text" class="form-control" id="receiver" name="receiver"
                   value="<?php echo $receiverVal; ?>" required>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email"
                   value="<?php echo $emailVal; ?>" readonly>
        </div>

        <div class="mb-3">
            <label for="phone" class="form-label">Số điện thoại</label>
            <!-- pattern + title cho client-side; server-side vẫn cần kiểm tra -->
            <input type="text" class="form-control" id="phone" name="phone"
                   value="<?php echo $phoneVal; ?>"
                   pattern="0[0-9]{9}"
                   title="Bắt đầu bằng 0 và gồm đúng 10 chữ số (ví dụ: 0912345678)"
                   required>
            <div class="form-text">Bắt đầu bằng 0 và gồm 10 chữ số.</div>
        </div>

        <div class="mb-3">
            <label for="address" class="form-label">Địa chỉ giao hàng</label>
            <input type="text" class="form-control" id="address" name="address"
                   value="<?php echo $addressVal; ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Hình thức giao hàng</label>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="shipping_method" value="giao_hang" checked>
                <label class="form-check-label">Giao hàng tận nơi (+20.000₫)</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="shipping_method" value="nhan_tai_cua_hang">
                <label class="form-check-label">Nhận tại cửa hàng (0₫)</label>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Phương thức thanh toán</label>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="payment_method" id="pay_cod" value="cod" checked>
                <label class="form-check-label" for="pay_cod">Thanh toán khi nhận hàng (COD)</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="payment_method" id="pay_vnpay" value="vnpay">
                <label class="form-check-label" for="pay_vnpay">VNPAY</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="payment_method" id="pay_store" value="store">
                <label class="form-check-label" for="pay_store">Thanh toán tại cửa hàng</label>
            </div>
        </div>

        <div class="mb-3">
            <label for="discount_code" class="form-label">Mã giảm giá (nếu có)</label>
            <input type="text" class="form-control" id="discount_code" name="discount_code" placeholder="Nhập mã giảm giá của bạn...">
        </div>

        <button type="submit" class="btn btn-success">Xác nhận đặt hàng</button>
    </form>
</div>

<script>
document.getElementById('checkoutForm')?.addEventListener('submit', function(e) {
    const phone = document.getElementById('phone').value.trim();
    const phoneRe = /^0[0-9]{9}$/;

    if (!phoneRe.test(phone)) {
        alert("Số điện thoại không hợp lệ. Phải bắt đầu bằng 0 và gồm đúng 10 chữ số (ví dụ: 0912345678).");
        e.preventDefault();
        return false;
    }

    // address required check (HTML required already handles it, nhưng để an toàn)
    const address = document.getElementById('address').value.trim();
    if (address === '') {
        alert("Vui lòng nhập địa chỉ giao hàng.");
        e.preventDefault();
        return false;
    }

    // submit normally - lưu ý: view này KHÔNG thay đổi tbluser
});
</script>
