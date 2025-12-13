
<?php
$keyword = $keyword ?? '';
$status  = $status ?? '';
$date    = $date ?? '';
?>

<div class="container mt-5">
    <h2>Quản lý đơn hàng</h2>
   
<form method="get" action="<?= APP_URL ?>/Order/show" class="d-flex gap-2 mb-3">

    <input type="text" name="keyword"  placeholder="Tìm theo mã đơn, tên, sđt..."
        value="<?= $keyword ?>" class="form-control" style="width: 500px">

    <select name="status" class="form-select" style="width: 180px">
        <option value="">-- Trạng thái --</option>
        <option value="pending"   <?= $status == 'pending' ? 'selected' : '' ?>>Đang chờ</option>
        <option value="approved"  <?= $status == 'approved' ? 'selected' : '' ?>>Đã duyệt</option>
        <option value="shipping"  <?= $status == 'shipping' ? 'selected' : '' ?>>Đang giao</option>
        <option value="completed" <?= $status == 'completed' ? 'selected' : '' ?>>Hoàn thành</option>
        <option value="cancelled" <?= $status == 'cancelled' ? 'selected' : '' ?>>Đã hủy</option>
    </select>

    <input type="date" name="date" value="<?= $date ?>" class="form-control" style="width:150px">

    <button type="submit" class="btn btn-primary">Lọc</button>
</form>



    <table class="table table-bordered table-hover align-middle">
        <thead class="table-dark">
            <tr>
                <th>Mã hóa đơn</th>
                <th>Ngày đặt</th>
                <th>Tổng tiền</th>
                <th>Người nhận</th>
                <th>Địa chỉ</th>
                <th>SĐT</th>
                <th>Thanh toán</th>
                <th>Giao hàng</th>
                <th>Trạng thái</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($data['orders'])): foreach ($data['orders'] as $order): ?>
                <tr>
                    <td><?= htmlspecialchars($order['order_code']) ?></td>
                    <td><?= htmlspecialchars($order['created_at']) ?></td>
                    <td><?= number_format($order['total_amount'], 0, ',', '.') ?> ₫</td>
                    <td><?= htmlspecialchars($order['receiver']) ?></td>
                    <td><?= htmlspecialchars($order['address']) ?></td>
                    <td><?= htmlspecialchars($order['phone']) ?></td>
                    
                    <td>
    <?php
        // Hiển thị trạng thái thanh toán
        echo ($order['transaction_info'] == 'dathanhtoan') 
            ? '<span class="badge bg-success">Đã thanh toán</span>'
            : '<span class="badge bg-warning text-dark">Chưa thanh toán</span>';
    ?>
</td>
<td>
    <?php
    echo ($order['shipping_method'] == 'giao_hang') 
    ? '<span class="badge bg-success">Giao hàng tận nơi</span>'
    : '<span class="badge bg-warning text-dark">Nhận tại cửa hàng   </span>';
    ?>
</td>
<td>
    <?php
    if (isset($order['status']) && $order['status'] === 'cancelled') {

        $cb = isset($order['cancelled_by']) ? trim((string)$order['cancelled_by']) : '';

        // chuẩn hóa xuống lowercase để so sánh
        $cbLower = mb_strtolower($cb);

        if ($cbLower === 'admin') {
            echo '<span class="badge bg-danger">Admin đã hủy đơn</span>';
        } elseif ($cbLower === 'user') {
            echo '<span class="badge bg-warning text-dark">User đã tự hủy đơn</span>';
        } elseif ($cb === '') {
            // không biết ai hủy
            echo '<span class="badge bg-secondary">Đã hủy</span>';
        } else {
            // nếu cancelled_by có số (user id) hoặc tên - coi là user đã hủy
            if (ctype_digit($cb)) {
                echo '<span class="badge bg-warning text-dark">User đã tự hủy đơn</span>';
            } else {
                // nếu là tên (vd: 'Trang user') hiển thị tên người hủy
                // tránh XSS
                echo '<span class="badge bg-warning text-dark">Đã hủy bởi: ' . htmlspecialchars($cb) . '</span>';
            }
        }

    } else {

        switch ($order['status'] ?? '') {
            case 'pending': echo '<span class="badge bg-secondary">Chờ xử lý</span>'; break;
            case 'approved': echo '<span class="badge bg-info text-dark">Đã duyệt</span>'; break;
            case 'shipping': echo '<span class="badge bg-primary">Đang giao</span>'; break;
            case 'completed': echo '<span class="badge bg-success">Hoàn thành</span>'; break;
            default: echo '<span class="badge bg-light text-dark">Không rõ</span>';
        }

    }
    ?>
</td>



                    <!-- Hành động -->
                    <td>
                        <a href="<?= APP_URL ?>/Order/detail/<?= $order['id'] ?>" 
                           class="btn btn-info btn-sm mb-1">
                           <i class="bi bi-eye"></i> Xem
                        </a>

                        <?php if ($order['status'] === 'pending'): ?>
                            <a href="<?= APP_URL ?>/Order/updateStatus/<?= $order['id'] ?>?status=approved" 
                               class="btn btn-success btn-sm mb-1">
                               <i class="bi bi-check2-circle"></i> Duyệt
                            </a>
                            <a href="<?= APP_URL ?>/Order/updateStatus/<?= $order['id'] ?>?status=cancelled" 
                               class="btn btn-danger btn-sm mb-1">
                               <i class="bi bi-x-circle"></i> Hủy
                            </a>
                        <?php elseif ($order['status'] === 'approved'): ?>
                            <a href="<?= APP_URL ?>/Order/updateStatus/<?= $order['id'] ?>?status=shipping" 
                               class="btn btn-primary btn-sm mb-1">
                               <i class="bi bi-truck"></i> Giao hàng
                            </a>
                        <a href="<?= APP_URL ?>/Order/printInvoice/<?= $order['id'] ?>" 
   class="btn btn-secondary btn-sm mb-1" target="_blank">
   <i class="bi bi-printer"></i> In hóa đơn
</a>

                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; else: ?>
                <tr><td colspan="9" class="text-center">Chưa có đơn hàng nào.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
