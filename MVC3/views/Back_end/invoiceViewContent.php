<?php
$order = $order ?? $data['order'];
$items = $items ?? $data['items'];

// 1. Tổng giá gốc (chưa giảm)
$originalSubtotal = 0;

// 2. Tổng giá sau giảm (sale_price)
$productDiscountedSubtotal = 0;

// 3. Tổng giảm trên từng sản phẩm
$productDiscountTotal = 0;

foreach ($items as $item) {

    // Tổng ban đầu
    $originalSubtotal += $item['price'] * $item['quantity'];

    // Tổng sau giảm
    $productDiscountedSubtotal += $item['sale_price'] * $item['quantity'];

    // Tổng giảm trên từng SP
    $productDiscountTotal += ($item['price'] - $item['sale_price']) * $item['quantity'];
}

// 4. Tổng cuối cùng đã lưu DB
$finalTotal = floatval($order['total_amount']);

// 5. Giảm từ mã giảm giá
$discountCodeAmount = $productDiscountedSubtotal - $finalTotal;
if ($discountCodeAmount < 0) $discountCodeAmount = 0;

// Tên mã giảm giá
$promoCode = !empty($order['discount_code'])
    ? htmlspecialchars($order['discount_code'])
    : "Không sử dụng";
?>

<style>
.order-card-title {
    font-weight: 600;
    font-size: 18px;
    border-left: 4px solid #0d6efd;
    padding-left: 10px;
    margin-bottom: 15px;
}
.order-info-row {
    margin-bottom: 8px;
}
.order-info-row b {
    width: 180px;
    display: inline-block;
}
.product-img {
    width: 60px;
    height: 60px;
    object-fit: contain;
    border: 1px solid #ddd;
    border-radius: 6px;
    padding: 3px;
    background: #fff;
}
</style>

<div class="container mt-4 mb-5">

    <h2 class="mb-4">
        🧾 Chi tiết đơn hàng
        <span class="text-primary fw-bold">#<?= htmlspecialchars($order['order_code']) ?></span>
    </h2>

    <div class="row g-4">

        <!-- THÔNG TIN ĐƠN HÀNG -->
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="order-card-title">Thông tin đơn hàng</div>

                    <div class="order-info-row"><b>Ngày đặt:</b> <?= htmlspecialchars($order['created_at']) ?></div>
                    <div class="order-info-row"><b>Người nhận:</b> <?= htmlspecialchars($order['receiver']) ?></div>
                    <div class="order-info-row"><b>Email:</b> <?= htmlspecialchars($order['user_email']) ?></div>
                    <div class="order-info-row"><b>Số điện thoại:</b> <?= htmlspecialchars($order['phone']) ?></div>
                    <div class="order-info-row"><b>Địa chỉ:</b> <?= htmlspecialchars($order['address']) ?></div>

                    <div class="order-info-row mt-2"><b>Trạng thái đơn:</b>
                        <?php
                        switch ($order['status']) {
                            case 'pending': echo '<span class="badge bg-secondary">Chờ xử lý</span>'; break;
                            case 'approved': echo '<span class="badge bg-info text-dark">Đã duyệt</span>'; break;
                            case 'shipping': echo '<span class="badge bg-primary">Đang giao</span>'; break;
                            case 'completed': echo '<span class="badge bg-success">Hoàn thành</span>'; break;
                            case 'cancelled': echo '<span class="badge bg-danger">Đã hủy</span>'; break;
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- THANH TOÁN & GIAO HÀNG -->
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="order-card-title">Thanh toán & giao hàng</div>

                    <div class="order-info-row"><b>Thanh toán:</b>
                        <?php if ($order['transaction_info'] === 'dathanhtoan'): ?>
                            <span class="badge bg-success">Đã thanh toán</span>
                        <?php else: ?>
                            <span class="badge bg-warning text-dark">Chờ thanh toán</span>
                        <?php endif; ?>
                    </div>

                    <div class="order-info-row"><b>Hình thức giao:</b>
                        <?php if ($order['shipping_method'] === 'giao_hang'): ?>
                            <span class="badge bg-primary">Giao hàng tận nơi</span>
                        <?php else: ?>
                            <span class="badge bg-info text-dark">Nhận tại cửa hàng</span>
                        <?php endif; ?>
                    </div>

                    <div class="order-info-row"><b>Phí ship:</b>
                        <?= number_format($order['shipping_fee'], 0, ',', '.') ?> ₫
                    </div>
                </div>
            </div>
        </div>

        <!-- TỔNG TIỀN -->
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="order-card-title">Tổng tiền & giảm giá</div>

                    <div class="row">
                        <div class="col-md-6 order-info-row"><b>Tổng giá gốc:</b>
                            <?= number_format($originalSubtotal, 0, ',', '.') ?> ₫
                        </div>
                        <div class="col-md-6 order-info-row"><b>Giảm trên sản phẩm:</b>
                            <?= number_format($productDiscountTotal, 0, ',', '.') ?> ₫
                        </div>

                        <div class="col-md-6 order-info-row"><b>Mã giảm giá:</b> <?= $promoCode ?></div>
                        <div class="col-md-6 order-info-row"><b>Giảm từ mã:</b>
                            <?= number_format($discountCodeAmount, 0, ',', '.') ?> ₫
                        </div>

                        <div class="col-md-6 order-info-row"><b>Tổng sau giảm SP:</b>
                            <?= number_format($productDiscountedSubtotal, 0, ',', '.') ?> ₫
                        </div>
                        <div class="col-md-6 order-info-row"><b>Tổng thanh toán:</b>
                            <span class="fw-bold text-danger">
                                <?= number_format($finalTotal, 0, ',', '.') ?> ₫
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- DANH SÁCH SẢN PHẨM -->
    <h4 class="mt-5 mb-3 fw-bold">📦 Sản phẩm trong đơn</h4>

    <table class="table table-bordered table-striped align-middle shadow-sm">
        <thead class="table-dark">
        <tr>
            <th width="80">Mã SP</th>
            <th>Tên sản phẩm</th>
            <th width="80">Hình</th>
            <th width="80">SL</th>
            <th>Giá</th>
            <th>Giá KM</th>
        </tr>
        </thead>
        <tbody>

        <?php if (!empty($items)): foreach ($items as $item): ?>
            <tr>
                <td><?= htmlspecialchars($item['product_id']) ?></td>
                <td><?= htmlspecialchars($item['product_name']) ?></td>
                <td><img src="<?= APP_URL ?>/public/images/<?= htmlspecialchars($item['image']) ?>" class="product-img"></td>
                <td><?= htmlspecialchars($item['quantity']) ?></td>
                <td><?= number_format($item['price'], 0, ',', '.') ?> ₫</td>
                <td><?= number_format($item['sale_price'], 0, ',', '.') ?> ₫</td>
            </tr>
        <?php endforeach; else: ?>

            <tr>
                <td colspan="6" class="text-center text-muted py-3">
                    Không có sản phẩm trong đơn hàng này.
                </td>
            </tr>

        <?php endif; ?>
        </tbody>
    </table>

    <!-- NÚT -->
    <div class="mt-4">
        <a href="<?= APP_URL ?>/Order" class="btn btn-secondary me-2">
            ⬅ Quay lại danh sách
        </a>

        <?php if ($order['status'] === 'pending'): ?>
            <a href="<?= APP_URL ?>/Order/updateStatus/<?= $order['id'] ?>?status=approved"
               class="btn btn-success me-2">✔ Duyệt đơn</a>

            <a href="<?= APP_URL ?>/Order/updateStatus/<?= $order['id'] ?>?status=cancelled"
               class="btn btn-danger">✖ Hủy đơn</a>

        <?php elseif ($order['status'] === 'approved'): ?>
            <a href="<?= APP_URL ?>/Order/updateStatus/<?= $order['id'] ?>?status=shipping"
               class="btn btn-primary">🚚 Giao hàng</a>
        <?php endif; ?>
    </div>

</div>
