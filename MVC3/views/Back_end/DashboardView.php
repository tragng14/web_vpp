<div class="container py-4">

    <h2 class="mb-4">📊 Trang quản trị - Tổng quan</h2>

    <!-- BỘ LỌC -->
    <form method="get" class="d-flex gap-3 mb-4">
        <input type="hidden" name="controller" value="AdminDashboard">
        <input type="hidden" name="action" value="index">

        <select name="type" class="form-select w-auto">
            <option value="day"   <?= $data["filterType"] == "day" ? "selected" : "" ?>>Ngày</option>
            <option value="month" <?= $data["filterType"] == "month" ? "selected" : "" ?>>Tháng</option>
            <option value="year"  <?= $data["filterType"] == "year" ? "selected" : "" ?>>Năm</option>
        </select>

        <input type="date" name="date" value="<?= $data['date'] ?>" class="form-control w-auto">
        <button class="btn btn-primary">Lọc</button>
    </form>

    <!-- SUMMARY -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card shadow p-3 text-center chart-summary">
                <h6>Đơn hàng</h6>
                <h3><?= $data["summary"]["total_orders"] ?? 0 ?></h3>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow p-3 text-center chart-summary">
                <h6>Sản phẩm bán</h6>
                <h3><?= $data["summary"]["total_products"] ?? 0 ?></h3>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow p-3 text-center chart-summary">
                <h6>Doanh thu</h6>
                <h3><?= number_format($data["summary"]["total_revenue"] ?? 0, 0, ',', '.') ?> ₫</h3>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow p-3 text-center chart-summary">
                <h6>Mã giảm đã dùng</h6>
                <h5><?= $data["summary"]["used_promos"] ?: "Không có" ?></h5>
            </div>
        </div>
    </div>

    <hr>

    <!-- BIỂU ĐỒ -->
    <div class="row">

        <!-- Chart 1 -->
        <div class="col-md-6 mb-4">
            <div class="card shadow chart-card">
                <h5 class="text-center mt-2">Doanh thu theo thời gian</h5>
                <div class="chart-wrapper">
                    <canvas id="chartTime"></canvas>
                </div>
            </div>
        </div>

        <!-- Chart 2 -->
        <div class="col-md-6 mb-4">
            <div class="card shadow chart-card">
                <h5 class="text-center mt-2">Doanh thu theo loại</h5>
                <div class="chart-wrapper">
                    <canvas id="chartCategory"></canvas>
                </div>
            </div>
        </div>

        <!-- Chart 3 -->
        <div class="col-md-6 mb-4">
            <div class="card shadow chart-card">
                <h5 class="text-center mt-2">Top sản phẩm bán chạy</h5>
                <div class="chart-wrapper">
                    <canvas id="chartTop"></canvas>
                </div>
            </div>
        </div>

        <!-- Chart 4 -->
        <div class="col-md-6 mb-4">
            <div class="card shadow chart-card">
                <h5 class="text-center mt-2">Số lượng bán theo loại</h5>
                <div class="chart-wrapper">
                    <canvas id="chartQty"></canvas>
                </div>
            </div>
        </div>

    </div>

</div>


<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    const chartTime = <?= json_encode($data["chartTime"]) ?>;
    const categoryRevenue = <?= json_encode($data["categoryRevenue"]) ?>;
    const topProducts = <?= json_encode($data["topProducts"]) ?>;

    // =====================
    // 1) LINE – Doanh thu theo thời gian
    // =====================
    new Chart(document.getElementById("chartTime"), {
        type: "line",
        data: {
            labels: chartTime.map(e => e.label),
            datasets: [{
                label: "Doanh thu",
                data: chartTime.map(e => e.total),
                borderWidth: 2,
                borderColor: "blue"
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });

    // =====================
    // 2) PIE – Doanh thu theo loại
    // =====================
    new Chart(document.getElementById("chartCategory"), {
        type: "pie",
        data: {
            labels: categoryRevenue.map(e => e.category_name),
            datasets: [{
                data: categoryRevenue.map(e => e.total_revenue),
                backgroundColor: ["#FF6384","#36A2EB","#FFCE56","#4BC0C0","#9966FF"]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });

    // =====================
    // 3) BAR – Top sản phẩm bán chạy
    // =====================
    new Chart(document.getElementById("chartTop"), {
        type: "bar",
        data: {
            labels: topProducts.map(e => e.product_name),
            datasets: [{
                label: "SL bán",
                data: topProducts.map(e => e.total_quantity),
                backgroundColor: "orange"
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });

    // =====================
    // 4) BAR – Số lượng bán theo loại
    // =====================
    new Chart(document.getElementById("chartQty"), {
        type: "bar",
        data: {
            labels: categoryRevenue.map(e => e.category_name),
            datasets: [{
                label: "Số lượng",
                data: categoryRevenue.map(e => e.total_quantity),
                backgroundColor: "green"
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });
</script>


<!-- CSS FIX SIZE BIỂU ĐỒ -->
<style>
    .chart-card {
        height: 350px;
        padding: 15px;
        display: flex;
        flex-direction: column;
    }

    .chart-wrapper {
        flex: 1;
        position: relative;
    }

    .chart-wrapper canvas {
        width: 100% !important;
        height: 100% !important;
    }
</style>
