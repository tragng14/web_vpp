<div class="container py-2">
<ul class="nav nav-tabs mb-2" id="reportTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="overview-tab" data-bs-toggle="tab"
            data-bs-target="#overview" type="button">Tổng quan</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="slow-tab" data-bs-toggle="tab"
            data-bs-target="#slow" type="button">Sản phẩm bán chậm</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="unsold-tab" data-bs-toggle="tab"
            data-bs-target="#unsold" type="button">Sản phẩm chưa bán</button>
    </li>
</ul>

</div>

<div class="tab-content">

<!-- TAB 1: TỔNG QUAN -->
<div class="tab-pane fade show active" id="overview">
<div class="container py-4">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="text-primary mb-0">Báo cáo doanh thu</h3>

        <form class="d-flex" method="get" action="<?= APP_URL ?>/Report/show">
            <input type="date" name="date" value="<?= htmlspecialchars($data['date']) ?>" class="form-control me-2" />
            <select name="type" class="form-select me-2">
                <option value="day" <?= $data['filterType'] == 'day' ? 'selected' : '' ?>>Theo ngày</option>
                <option value="month" <?= $data['filterType'] == 'month' ? 'selected' : '' ?>>Theo tháng</option>
                <option value="year" <?= $data['filterType'] == 'year' ? 'selected' : '' ?>>Theo năm</option>
            </select>
            <button class="btn btn-primary" type="submit"><i class="bi bi-funnel"></i> Lọc</button>
        </form>
    </div>

    <!-- Tổng quan -->
    <div class="alert alert-info shadow-sm">
        <strong><?= htmlspecialchars($data['title']) ?></strong><br>
        <span class="me-3">Tổng doanh thu: <b><?= number_format($data['summary']['total_revenue'] ?? 0, 0, ',', '.') ?> ₫</b></span>
        <span class="me-3">Tổng đơn hàng: <b><?= $data['summary']['total_orders'] ?? 0 ?></b></span>
        <span>Tổng sản phẩm bán ra: <b><?= $data['summary']['total_products'] ?? 0 ?></b></span>
       

    </div>

    <!-- Bảng doanh thu theo loại sản phẩm -->
    <?php if (!empty($data['byCategory'])): ?>
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-dark text-white">
            <strong>Doanh thu theo loại sản phẩm</strong>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0 align-middle text-center">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Loại sản phẩm</th>
                            <th>Số lượng bán</th>
                            <th>Doanh thu (₫)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i=1; foreach ($data['byCategory'] as $row): ?>
                        <tr>
                            <td><?= $i++ ?></td>
                            <td><?= htmlspecialchars($row['category_name']) ?></td>
                            <td><?= number_format($row['total_quantity']) ?></td>
                            <td><?= number_format($row['total_revenue'], 0, ',', '.') ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <?php if (!empty($data['byCategory'])): ?>


<!-- BIỂU ĐỒ -->
<!-- BIỂU ĐỒ -->
<div class="row g-4 mb-4">

    <!-- Doanh thu theo loại -->
    <div class="col-md-6">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-primary text-white">
                <strong>Biểu đồ doanh thu theo loại sản phẩm</strong>
            </div>
          <div class="card-body chart-box">
    <div class="chart-wrapper">
        <canvas id="revenueChart"></canvas>
    </div>
</div>

        </div>
    </div>

    <!-- Số lượng bán theo loại -->
    <div class="col-md-6">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-success text-white">
                <strong>Biểu đồ số lượng bán theo loại sản phẩm</strong>
            </div>
            <div class="card-body">
                <canvas id="quantityChart" height="200"></canvas>
            </div>
        </div>
    </div>

</div>


<div class="row g-4 mb-4">

    <!-- Doanh thu theo thời gian -->
    <div class="col-md-6">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-warning text-dark">
                <strong>Biểu đồ doanh thu theo thời gian</strong>
            </div>
            <div class="card-body">
                <canvas id="timeChart" height="200"></canvas>
            </div>
        </div>
    </div>

    <!-- Top sản phẩm bán chạy -->
    <div class="col-md-6">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-danger text-white">
                <strong>Top sản phẩm bán chạy</strong>
            </div>
            <div class="card-body">
                <canvas id="topProductChart" height="200"></canvas>
            </div>
        </div>
    </div>

</div>
<?php endif; ?>

    <!-- Bảng chi tiết đơn hàng -->
<div class="card shadow-sm">
    <div class="card-header bg-secondary text-white">
        <strong>Chi tiết giao dịch</strong>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">

            <table class="table table-striped table-hover mb-0 align-middle text-center">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Mã đơn</th>
                        <th>Ngày đặt</th>
                        <th>Mã KM</th>
                        <th>Giảm</th>
                        <th>Phí ship</th>
                        <th>Tên SP</th>
                    
                        <th>Ảnh</th>
                        <th>SL</th>
                        <th>Giá bán</th>
                        <th>Thành tiền sản phẩm</th>
                    </tr>
                </thead>

                <tbody>
                <?php
                if (!empty($data['reportData'])):
                    $i = 1;
                    $prevOrder = null;
                    foreach ($data['reportData'] as $row):

                        $isSameOrder = ($row['order_code'] === $prevOrder);

                        // Khi qua đơn mới → in tổng + KM cho đơn cũ
                        if (!$isSameOrder && $prevOrder !== null):
    echo "
        <tr class='table-secondary fw-bold'>
            <td colspan='11' class='text-start p-3'>
                <div><b>Mã khuyến mãi:</b> " . ($lastRow['discount_code'] ?: "Không áp dụng") . "</div>
                <div><b>Tên KM:</b> " . ($lastRow['promo_name'] ?: "-") . "</div>
                <div><b>Tổng đơn gốc:</b> " . number_format($lastRow['original_total_order'], 0, ',', '.') . " ₫</div>
                <div><b>Giảm giá:</b> -" . number_format($lastRow['discount_amount'], 0, ',', '.') . " ₫</div>
                <div><b>Phí ship:</b> +" . number_format($lastRow['shipping_fee'], 0, ',', '.') . " ₫</div>
                <div><b>Thanh toán:</b> " . number_format($lastRow['original_total_order'] - $lastRow['discount_amount'] + $lastRow['shipping_fee'], 0, ',', '.') . " ₫</div>
            </td>
        </tr>
    ";
endif;

                ?>

                <tr>
                    <!-- Cột thông tin đơn -->
                    <?php if (!$isSameOrder): ?>
                        <td><?= $i++ ?></td>
                        <td><?= htmlspecialchars($row['order_code']) ?></td>
                        <td><?= htmlspecialchars($row['created_at']) ?></td>

                        <!-- Mã giảm -->
                        <td><?= $row['discount_code'] ?: "-" ?></td>

                        <!-- Hiển thị mức giảm -->
                        <td>
                            <?php
                                if (!empty($row['discount_code'])) {
                                    if ($row['promo_type'] == 'percent') {
                                        echo $row['promo_value'] . "%";
                                    } else {
                                        echo number_format($row['promo_value'], 0, ',', '.') . " ₫";
                                    }
                                } else echo "-";
                            ?>
                        </td>

                    <?php else: ?>
                        <td></td><td></td><td></td><td></td><td></td>
                    <?php endif; ?>
                        <!-- Hiển thị phí ship -->
<td>
    <?php if (!$isSameOrder): ?>
        <?= number_format($row['shipping_fee'], 0, ',', '.') ?> ₫
    <?php else: ?>
        -
    <?php endif; ?>
</td>

                    <!-- Thông tin sản phẩm -->
                    <td><?= htmlspecialchars($row['product_name']) ?></td>
                   

                    <td>
                        <img src="<?= APP_URL ?>/public/images/<?= htmlspecialchars($row['hinhanh'] ?? '') ?>"
                             style="width:60px;height:60px;object-fit:contain;">
                    </td>

                    <td><?= (int)$row['quantity'] ?></td>
                    <td><?= number_format($row['sale_price'], 0, ',', '.') ?> ₫</td>
                    <td><?= number_format($row['total'], 0, ',', '.') ?> ₫</td>
                </tr>

                <?php
                    // Lưu đơn hiện tại để in tổng khi đổi đơn
                    $lastRow = $row;
                    $prevOrder = $row['order_code'];

                    endforeach;

                if ($prevOrder !== null):
    echo "
    <tr class='table-secondary fw-bold'>
        <td colspan='11' class='text-start p-3'>
            <div><b>Mã khuyến mãi:</b> " . ($lastRow['discount_code'] ?: "Không áp dụng") . "</div>
            <div><b>Tên KM:</b> " . ($lastRow['promo_name'] ?: "-") . "</div>
            <div><b>Tổng đơn gốc:</b> " . number_format($lastRow['original_total_order'], 0, ',', '.') . " ₫</div>
            <div><b>Giảm giá:</b> -" . number_format($lastRow['discount_amount'], 0, ',', '.') . " ₫</div>
            <div><b>Phí ship:</b> +" . number_format($lastRow['shipping_fee'], 0, ',', '.') . " ₫</div>
            <div><b>Thanh toán:</b> " . number_format($lastRow['original_total_order'] - $lastRow['discount_amount'] + $lastRow['shipping_fee'], 0, ',', '.') . " ₫</div>
        </td>
    </tr>
    ";
endif;


                else:
                    echo '<tr><td colspan="11" class="text-center text-muted py-4">Không có dữ liệu.</td></tr>';
                endif;
                ?>
                </tbody>
            </table>

        </div>
    </div>
</div>

</div>


</div>
<div class="tab-pane fade" id="slow">
    <div class="container">

        <div class="row g-3">

            <!-- Cột trái: BIỂU ĐỒ -->
            <div class="col-12 col-lg-6">
                <div class="card shadow-sm rounded-3">
                    <div class="card-header bg-warning text-dark py-2">
                        <strong>Biểu đồ sản phẩm bán chậm</strong>
                    </div>
                    <div class="card-body py-2">
                        <canvas id="slowProductChart" height="250"></canvas>
                    </div>
                </div>
            </div>

            <!-- Cột phải: DANH SÁCH -->
            <div class="col-12 col-lg-6">
                <div class="card shadow-sm rounded-3 h-100">
                    <div class="card-header bg-secondary text-white py-2">
                        <strong>Danh sách sản phẩm bán chậm</strong>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-sm table-striped table-hover text-center mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th width="50">#</th>
                                    <th>Tên sản phẩm</th>
                                    <th width="120">Số lượng bán</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($data['slowProducts'])): 
                                    $i = 1;
                                    foreach ($data['slowProducts'] as $row): ?>
                                        <tr>
                                            <td><?= $i++ ?></td>
                                            <td><?= htmlspecialchars($row['product_name']) ?></td>
                                            <td><?= (int)$row['total_quantity'] ?></td>
                                        </tr>
                                    <?php endforeach;
                                else: ?>
                                    <tr>
                                        <td colspan="3" class="text-muted py-3">Không có sản phẩm bán chậm.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>

    </div>
</div>


<!-- TAB 3: SẢN PHẨM CHƯA BÁN -->
<div class="tab-pane fade" id="unsold">
    <div class="container" style="max-width:1200px;">

        <div class="card shadow-sm rounded-3">
            <div class="card-header bg-danger text-white py-2">
                <strong>Danh sách sản phẩm chưa bán</strong>
            </div>
            <div class="card-body p-0">
                <table class="table table-sm table-striped table-hover text-center mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="50">#</th>
                            <th>Tên sản phẩm</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($data['neverSoldProducts'])): 
                            $i=1;
                            foreach ($data['neverSoldProducts'] as $row): ?>
                                <tr>
                                    <td><?= $i++ ?></td>
                                    <td><?= htmlspecialchars($row['tensp']) ?></td> 

                                    
                                </tr>
                            <?php endforeach;
                        else: ?>
                            <tr>
                                <td colspan="2" class="text-muted py-3">Không có sản phẩm chưa bán.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>




<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    /* ================================
       BIỂU ĐỒ DOANH THU THEO LOẠI
       ================================ */
    const categoryLabels = <?= json_encode($data['byCategory'] ? array_column($data['byCategory'], 'category_name') : []) ?>;
    const revenueData    = <?= json_encode($data['byCategory'] ? array_column($data['byCategory'], 'total_revenue') : []) ?>;
    const quantityData   = <?= json_encode($data['byCategory'] ? array_column($data['byCategory'], 'total_quantity') : []) ?>;

    // Biểu đồ Doanh thu theo loại
    if (categoryLabels.length > 0) {
        new Chart(document.getElementById('revenueChart'), {
            type: 'pie',
            data: {
                labels: categoryLabels,
                datasets: [{
                    data: revenueData,
                    backgroundColor: ['#4e73df','#1cc88a','#36b9cc','#f6c23e','#e74a3b','#858796']
                }]
            }
        });

        // Biểu đồ Số lượng bán theo loại
        new Chart(document.getElementById('quantityChart'), {
            type: 'bar',
            data: {
                labels: categoryLabels,
                datasets: [{
                    label: 'Số lượng bán',
                    data: quantityData,
                    backgroundColor: '#1cc88a'
                }]
            },
            options: {
                responsive: true,
                scales: { y: { beginAtZero: true } }
            }
        });
    }

    /* ================================
       BIỂU ĐỒ DOANH THU THEO THỜI GIAN
       ================================ */
    <?php if (!empty($data['revenueTime'])): ?>

    const timeLabels = <?= json_encode(array_column($data['revenueTime'], 'label')) ?>;
    const timeRevenue = <?= json_encode(array_column($data['revenueTime'], 'total')) ?>;

    new Chart(document.getElementById('timeChart'), {
        type: 'line',
        data: {
            labels: timeLabels,
            datasets: [{
                label: 'Doanh thu',
                data: timeRevenue,
                fill: false,
                borderColor: '#4e73df',
                tension: 0.2,
                borderWidth: 3,
                pointRadius: 4
            }]
        },
        options: {
            responsive: true,
            scales: { y: { beginAtZero: true } }
        }
    });

    <?php endif; ?>

    /* ================================
       BIỂU ĐỒ TOP SẢN PHẨM BÁN CHẠY
       ================================ */
    <?php if (!empty($data['topProducts'])): ?>

    const topLabels = <?= json_encode(array_column($data['topProducts'], 'product_name')) ?>;
    const topQty = <?= json_encode(array_column($data['topProducts'], 'total_quantity')) ?>;

    new Chart(document.getElementById('topProductChart'), {
        type: 'bar',
        data: {
            labels: topLabels,
            datasets: [{
                label: 'Số lượng bán',
                data: topQty,
                backgroundColor: '#e74a3b'
            }]
        },
        options: {
            responsive: true,
            indexAxis: 'y',
            scales: { x: { beginAtZero: true } }
        }
    });

    <?php endif; ?>

/* ================================
   BIỂU ĐỒ SẢN PHẨM BÁN CHẬM
   ================================ */
<?php if (!empty($data['slowProducts'])): ?>

const slowLabels = <?= json_encode(array_column($data['slowProducts'], 'product_name')) ?>;
const slowQty = <?= json_encode(array_column($data['slowProducts'], 'total_quantity')) ?>;

new Chart(document.getElementById('slowProductChart'), {
    type: 'bar',
    data: {
        labels: slowLabels,
        datasets: [{
            label: "Số lượng bán",
            data: slowQty,
            backgroundColor: "#f6c23e"
        }]
    },
    options: {
        indexAxis: 'y', // Horizontal bar
        responsive: true,
        scales: { x: { beginAtZero: true } }
    }
});

<?php endif; ?>

</script>

<style>
    .card {
    border-radius: 12px;
}

.card-header {
    font-size: 15px;
    font-weight: 600;
}

canvas {
    max-width: 100%;
}

.table img {
    border-radius: 6px;
    border: 1px solid #ddd;
}

/* === KHUNG BIỂU ĐỒ CHUẨN === */
.chart-box {
    height: 350px;        /* Đồng bộ chiều cao */
    padding: 10px;
}

.chart-wrapper {
    position: relative;
    width: 100%;
    height: 100%;
}

/* Canvas auto fit không tràn */
.chart-wrapper canvas {
    width: 100% !important;
    height: 100% !important;
    max-height: 100%;
    max-width: 100%;
}

/* Fix PIE CHART bị méo hoặc bị tràn */
.chart-box .chart-wrapper canvas {
    object-fit: contain;     /* Bắt Chart.js fit gọn */
}

</style>