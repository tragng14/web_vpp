<?php
// views/Front_end/orderHistory.php
// View: Lịch sử đơn hàng (full file)
// Yêu cầu: biến $data (mảng) được truyền từ controller:
//   - $data['orders'] => array of orders (each: id, order_code, status, created_at, total_amount)
//   - $data['pageCurrent'] => current page (int)
//   - $data['totalPages'] => total pages (int)
// NOTE: APP_URL phải được định nghĩa trong config.

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$app = defined('APP_URL') ? rtrim(APP_URL, '/') : '';
$data = $data ?? [];
$orders = $data['orders'] ?? [];
$current = isset($data['pageCurrent']) ? (int)$data['pageCurrent'] : 1;
$totalPages = isset($data['totalPages']) ? (int)$data['totalPages'] : 0;

// Safe GET values for preserving filters in links
$from_get = isset($_GET['from']) ? $_GET['from'] : '';
$to_get   = isset($_GET['to']) ? $_GET['to'] : '';
$from_q = $from_get !== '' ? urlencode($from_get) : '';
$to_q   = $to_get !== '' ? urlencode($to_get) : '';
?>
<div class="container mt-5 mb-5">
    <h2 class="mb-4">📜 Lịch sử đơn hàng</h2>

    <!-- Bộ lọc theo ngày -->
    <form method="GET" class="row g-3 mb-4" novalidate>
        <div class="col-md-4">
            <label class="form-label">Từ ngày</label>
            <input type="date" name="from" class="form-control"
                   value="<?= htmlspecialchars($from_get) ?>">
        </div>

        <div class="col-md-4">
            <label class="form-label">Đến ngày</label>
            <input type="date" name="to" class="form-control"
                   value="<?= htmlspecialchars($to_get) ?>">
        </div>

        <div class="col-md-4 d-flex align-items-end">
            <button class="btn btn-primary me-2" type="submit">Lọc</button>

            <a href="<?= htmlspecialchars($app . '/Home/orderHistory') ?>" class="btn btn-secondary">
                Reset
            </a>
        </div>
    </form>

    <div class="order-list">
        <?php if (!empty($orders)): foreach ($orders as $order): 
            // Normalize fields
            $id = isset($order['id']) ? (int)$order['id'] : 0;
            $code = htmlspecialchars($order['order_code'] ?? '---');
            $status = $order['status'] ?? '';
            $created = htmlspecialchars($order['created_at'] ?? '');
            $total = isset($order['total_amount']) ? number_format((float)$order['total_amount'], 0, ',', '.') : '0';
        ?>
            <div class="order-card">
                <div class="order-header">
                    <span class="order-code">#<?= $code ?></span>
                    <span class="order-status">
                        <?php
                            switch ($status) {
                                case 'pending': echo '<span class="badge bg-secondary">Chờ xử lý</span>'; break;
                                case 'approved': echo '<span class="badge bg-info text-dark">Đã duyệt</span>'; break;
                                case 'shipping': echo '<span class="badge bg-primary">Đang giao</span>'; break;
                                case 'completed': echo '<span class="badge bg-success">Hoàn thành</span>'; break;
                                case 'cancelled': echo '<span class="badge bg-danger">Đã hủy</span>'; break;
                                default: echo '<span class="badge bg-light text-dark">Không rõ</span>';
                            }
                        ?>
                    </span>
                </div>

                <div class="order-body">
                    <p class="mb-1"><strong>Ngày đặt:</strong> <?= $created ?></p>
                    <p class="mb-0"><strong>Tổng tiền:</strong> <?= $total ?> ₫</p>
                </div>

                <div class="order-footer">
                    <a href="<?= htmlspecialchars($app . '/Home/orderDetail/' . $id) ?>" class="btn btn-detail">
                        Xem chi tiết
                    </a>
                </div>
            </div>
        <?php endforeach; else: ?>
            <!-- Khi không có đơn hàng: hiển thị card đẹp -->
            <div class="no-orders" style="width:100%;display:flex;justify-content:center;">
                <div class="order-card" style="max-width:640px;text-align:center;">
                    <div style="font-size:48px;margin-bottom:12px;color:#adb5bd;">🧾</div>
                    <p class="text-muted mb-0" style="font-size:16px;">
                        Bạn chưa có đơn hàng nào.
                    </p>
                    <div style="margin-top:14px;">
                        <a href="<?= htmlspecialchars($app . '/') ?>" class="btn btn-outline-primary">Tiếp tục mua sắm</a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
    <nav class="pagination-wrapper mt-4" aria-label="Trang">
        <ul class="pagination justify-content-center">

            <?php if ($current > 1): 
                $hrefPrev = '?page=' . ($current - 1) . '&from=' . $from_q . '&to=' . $to_q;
            ?>
                <li class="page-item">
                    <a class="page-link" href="<?= htmlspecialchars($hrefPrev) ?>" aria-label="Trang trước">«</a>
                </li>
            <?php endif; ?>

            <?php
            // Show limited page range for usability
            $start = max(1, $current - 3);
            $end = min($totalPages, $current + 3);
            if ($start > 1) {
                $href1 = '?page=1&from=' . $from_q . '&to=' . $to_q;
                echo '<li class="page-item"><a class="page-link" href="' . htmlspecialchars($href1) . '">1</a></li>';
                if ($start > 2) {
                    echo '<li class="page-item disabled"><span class="page-link">…</span></li>';
                }
            }
            for ($i = $start; $i <= $end; $i++):
                $href = '?page=' . $i . '&from=' . $from_q . '&to=' . $to_q;
            ?>
                <li class="page-item <?= ($i === $current) ? 'active' : '' ?>">
                    <a class="page-link" href="<?= htmlspecialchars($href) ?>"><?= $i ?></a>
                </li>
            <?php endfor;
            if ($end < $totalPages) {
                if ($end < $totalPages - 1) {
                    echo '<li class="page-item disabled"><span class="page-link">…</span></li>';
                }
                $hrefLast = '?page=' . $totalPages . '&from=' . $from_q . '&to=' . $to_q;
                echo '<li class="page-item"><a class="page-link" href="' . htmlspecialchars($hrefLast) . '">' . $totalPages . '</a></li>';
            }
            ?>

            <?php if ($current < $totalPages): 
                $hrefNext = '?page=' . ($current + 1) . '&from=' . $from_q . '&to=' . $to_q;
            ?>
                <li class="page-item">
                    <a class="page-link" href="<?= htmlspecialchars($hrefNext) ?>" aria-label="Trang sau">»</a>
                </li>
            <?php endif; ?>

        </ul>
    </nav>
    <?php endif; ?>

    <style>
    /* ---------- Form ---------- */
    form .form-control:focus {
        border-color: #3498db;
        box-shadow: 0 0 5px rgba(52,152,219,0.18);
    }
    form button.btn-primary {
        background-color: #3498db;
        border: none;
        padding: 10px 20px;
        border-radius: 10px;
        font-weight: 500;
    }
    form button.btn-primary:hover { background-color: #2980b9; }
    form a.btn-secondary { padding: 10px 12px; border-radius: 10px; }

    /* ---------- Order list & cards ---------- */
    .order-list { display:flex; flex-direction:column; gap:20px; }

    .order-card {
        background: #fff;
        border-radius: 15px;
        padding: 18px;
        box-shadow: 0 3px 12px rgba(0,0,0,0.06);
        transition: transform .18s ease, box-shadow .18s ease;
        border: 1px solid #ececec;
    }
    .order-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 20px rgba(0,0,0,0.10);
    }
    .order-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:12px; }
    .order-code { font-size:1.05rem; font-weight:700; color:#2c3e50; }

    .order-body p { margin-bottom:6px; color:#333; }
    .order-footer { margin-top:12px; }

    .btn-detail {
        background-color: #00a8ff;
        color: #fff !important;
        padding: 8px 14px;
        border-radius: 10px;
        text-decoration: none;
        font-weight: 600;
        display: inline-block;
    }
    .btn-detail:hover { background-color: #0097e6; }

    /* badge small tweak */
    .badge { padding: 6px 10px; border-radius: 8px; font-weight:600; }

    /* No orders style */
    .no-orders { display:flex; justify-content:center; align-items:center; padding: 18px 0; }
    .no-orders .order-card { max-width:640px; text-align:center; }

    /* Pagination */
    .pagination { gap:6px; }
    .pagination .page-item .page-link { border-radius:8px; padding:6px 10px; }
    .pagination .page-item.active .page-link { background:#3498db; border-color:#3498db; color:#fff; }
    </style>
</div>
