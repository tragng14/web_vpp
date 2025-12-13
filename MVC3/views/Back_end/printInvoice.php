<?php
$order = $order ?? $data['order'];
$items = $items ?? $data['items'];

// --- Tính toán lại giá ---
$originalSubtotal = 0;
$productDiscountedSubtotal = 0;
$productDiscountTotal = 0;

foreach ($items as $item) {
    $originalSubtotal += $item['price'] * $item['quantity'];
    $productDiscountedSubtotal += $item['sale_price'] * $item['quantity'];
    $productDiscountTotal += ($item['price'] - $item['sale_price']) * $item['quantity'];
}

$finalTotal = floatval($order['total_amount']);
$discountCodeAmount = $productDiscountedSubtotal - $finalTotal;
if ($discountCodeAmount < 0) $discountCodeAmount = 0;

$promoCode = !empty($order['discount_code']) ? htmlspecialchars($order['discount_code']) : "Không sử dụng";


?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>In hóa đơn</title>

<style>
    body {
        font-family: Arial, sans-serif;
        padding: 20px;
        background: #fff;
    }
    .invoice-box {
        width: 800px;
        margin: auto;
        padding: 25px;
        border: 2px solid #000;
    }

    h2 {
        text-align: center;
        font-size: 24px;
        margin-bottom: 5px;
    }

    .shop-info {
        text-align: center;
        font-size: 14px;
        margin-bottom: 20px;
    }

    .section-title {
        font-weight: bold;
        margin-top: 20px;
        margin-bottom: 8px;
        border-left: 4px solid #000;
        padding-left: 8px;
        font-size: 18px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 10px;
        font-size: 14px;
    }

    table th, table td {
        border: 1px solid #000;
        padding: 6px;
    }

    .text-right { text-align: right; }
    .text-center { text-align: center; }

    .product-img {
        width: 45px;
        height: 45px;
        object-fit: contain;
    }

    @media print {
        .no-print { display: none; }
    }
</style>
</head>

<body onload="window.print()">

<div class="invoice-box">

    <h2>HÓA ĐƠN BÁN HÀNG</h2>
    <div class="shop-info">
        Văn phòng phẩm LT – Hotline: 0879999999<br>

    </div>

    <!-- THÔNG TIN ĐƠN -->
    <div class="section-title">Thông tin đơn hàng</div>
    <table>
        <tr><td><b>Mã đơn hàng</b></td><td>#<?= htmlspecialchars($order['order_code']) ?></td></tr>
        <tr><td><b>Ngày đặt</b></td><td><?= htmlspecialchars($order['created_at']) ?></td></tr>
        <tr><td><b>Người nhận</b></td><td><?= htmlspecialchars($order['receiver']) ?></td></tr>
        <tr><td><b>Email</b></td><td><?= htmlspecialchars($order['user_email']) ?></td></tr>
        <tr><td><b>SĐT</b></td><td><?= htmlspecialchars($order['phone']) ?></td></tr>
        <tr><td><b>Địa chỉ</b></td><td><?= htmlspecialchars($order['address']) ?></td></tr>
        <tr>
            <td><b>Trạng thái</b></td>
            <td>
                <?php
                $statusNames = [
                    'pending' => 'Chờ xử lý',
                    'approved' => 'Đã duyệt',
                    'shipping' => 'Đang giao',
                    'completed' => 'Hoàn thành',
                    'cancelled' => 'Đã hủy'
                ];
                echo $statusNames[$order['status']];
                ?>
            </td>
        </tr>
    </table>

    <!-- THANH TOÁN -->
    <div class="section-title">Thanh toán & Giao hàng</div>
    <table>
        <tr><td><b>Thanh toán</b></td><td><?= $order['transaction_info'] === 'dathanhtoan' ? 'Đã thanh toán' : 'Chưa thanh toán' ?></td></tr>
        <tr><td><b>Hình thức giao</b></td><td><?= $order['shipping_method'] === 'giao_hang' ? 'Giao hàng tận nơi' : 'Nhận tại cửa hàng' ?></td></tr>
        <tr><td><b>Phí ship</b></td><td><?= number_format($order['shipping_fee']) ?> ₫</td></tr>
    </table>

    <!-- GIẢM GIÁ -->
    <div class="section-title">Tổng tiền & Giảm giá</div>
    <table>
        <tr><td><b>Tổng giá gốc</b></td><td class="text-right"><?= number_format($originalSubtotal) ?> ₫</td></tr>
        <tr><td><b>Giảm trên sản phẩm</b></td><td class="text-right">- <?= number_format($productDiscountTotal) ?> ₫</td></tr>
        <tr><td><b>Mã giảm giá</b></td><td><?= $promoCode ?></td></tr>
        <tr><td><b>Giảm từ mã</b></td><td class="text-right">- <?= number_format($discountCodeAmount) ?> ₫</td></tr>
        <tr>
            <td><b>Tổng thanh toán</b></td>
            <td class="text-right" style="font-size:18px; font-weight:bold; color:red;">
                <?= number_format($finalTotal) ?> ₫
            </td>
        </tr>
    </table>

    <!-- DANH SÁCH SẢN PHẨM -->
    <div class="section-title">Danh sách sản phẩm</div>
    <table>
        <thead>
            <tr>
                <th width="80">Mã SP</th>
                <th>Tên sản phẩm</th>
                <th width="55">Hình</th>
                <th width="50">SL</th>
                <th width="100">Giá</th>
                <th width="100">Giá KM</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $item): ?>
            <tr>
                <td class="text-center"><?= $item['product_id'] ?></td>
                <td><?= htmlspecialchars($item['product_name']) ?></td>
                <td class="text-center">
                    <img src="<?= APP_URL ?>/public/images/<?= $item['image'] ?>" class="product-img">
                </td>
                <td class="text-center"><?= $item['quantity'] ?></td>
                <td class="text-right"><?= number_format($item['price']) ?> ₫</td>
                <td class="text-right"><?= number_format($item['sale_price']) ?> ₫</td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

</div>

<div class="no-print" style="text-align:center; margin-top:20px;">
    <button onclick="window.print()">In hóa đơn</button>
    <button onclick="window.close()">Đóng</button>
</div>

</body>
</html>
