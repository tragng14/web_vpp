<div class="container py-4">

    <!-- Tabs -->
    <ul class="nav nav-tabs mb-3" id="accountTabs">
        <li class="nav-item">
            <a class="nav-link active" data-bs-toggle="tab" href="#tab-accounts">
                👤 Quản lý tài khoản
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#tab-users">
                🧑‍🤝‍🧑 Quản lý người dùng
            </a>
        </li>
    </ul>

    <div class="tab-content">

        <!-- ========================= -->
        <!-- TAB 1: QUẢN LÝ TÀI KHOẢN -->
        <!-- ========================= -->
        <div class="tab-pane fade show active" id="tab-accounts">

            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="text-primary mb-0">👤 Quản lý tài khoản </h3>
                <a href="<?= APP_URL ?>/TaiKhoan/create" class="btn btn-success">
                    <i class="bi bi-plus-circle"></i> Thêm tài khoản
                </a>
            </div>

            <!-- Thông báo -->
            <?php if(isset($_SESSION['success'])): ?>
                <div class="alert alert-success"><?=$_SESSION['success']?></div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>

            <?php if(isset($_SESSION['error'])): ?>
                <div class="alert alert-danger"><?=$_SESSION['error']?></div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <form method="get" action="">
                <div style="display:flex; gap:10px; margin-bottom:15px;">

                    <!-- Tìm kiếm -->
                    <input type="text"
                        name="keyword"
                        placeholder="Tìm theo tên, email, username..."
                        value="<?= isset($keyword) ? htmlspecialchars($keyword) : '' ?>"
                        class="form-control me-2">

                    <!-- Lọc quyền -->
                    <select name="role" class="form-select">
                        <option value="">Tất cả</option>
                        <option value="admin" <?= ($role=="admin" ? "selected" : "") ?>>Admin</option>
                        <option value="user"  <?= ($role=="user" ? "selected" : "") ?>>Khách hàng</option>
                         <option value="staff"  <?= ($role=="staff" ? "selected" : "") ?>>Nhân viên</option>
                    </select>

                    <button class="btn btn-primary">Lọc</button>
                </div>
            </form>

            <!-- Danh sách tài khoản -->
            <div class="card shadow-sm">
                <div class="card-header bg-dark text-white">
                    <strong>Danh sách tài khoản</strong>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">

                        <table class="table table-striped table-hover mb-0 align-middle text-center">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Ảnh</th>
                                    <th>Họ tên</th>
                                    <th>Email</th>
                                    <th>Số điện thoại</th>
                                    <th>Địa chỉ</th>
                                    <th>Quyền</th>
                                    <th>Trạng thái</th>
                                    <th>Ngày tạo</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php
                                if (!empty($data['users'])) {
                                    $i = 1;
                                    foreach ($data['users'] as $u) {
                                ?>
                                    <tr>
                                        <td><?= $i++ ?></td>

                                        <td>
                                            <?php if (!empty($u['avatar'])): ?>
                                                <img src="<?= APP_URL ?>/public/images/avatars/<?= htmlspecialchars($u['avatar']) ?>"
                                                     width="60" height="60"
                                                     style="border-radius:50%; object-fit:cover;"
                                                     onerror="this.src='<?= APP_URL ?>/public/images/user-default.png'">
                                            <?php else: ?>
                                                <img src="<?= APP_URL ?>/public/images/user-default.png"
                                                     width="45" height="45"
                                                     style="object-fit:cover; border-radius:50%;">
                                            <?php endif; ?>
                                        </td>

                                        <td><?= htmlspecialchars($u["fullname"]) ?></td>
                                        <td><?= htmlspecialchars($u["email"]) ?></td>
                                            <td><?= htmlspecialchars($u["phone"]) ?></td>
                                            <td><?= htmlspecialchars($u["address"]) ?></td>
                                        <td>
                                          <?php 
                                            if ($u["role"] == "admin") { 
                                            ?>
                                                <span class="badge bg-danger">Admin</span>

                                            <?php 
                                            } elseif ($u["role"] == "staff") { 
                                            ?>
                                                <span class="badge bg-warning text-dark">Nhân viên</span>

                                            <?php 
                                            } else { 
                                            ?>
                                                <span class="badge bg-success">Khách hàng</span>
                                            <?php 
                                            } 
                                            ?>

                                        </td>

                                        <td>
                                            <?php
                                            if ($u["is_deleted"] == 1) {
                                                echo '<span class="badge bg-secondary">Đã xóa</span>';
                                            } elseif ($u["status"] == "Tạm ngưng") {
                                                echo '<span class="badge bg-warning text-dark">Tạm ngưng</span>';
                                            } else {
                                                echo '<span class="badge bg-primary">Hoạt động</span>';
                                            }
                                            ?>
                                        </td>

                                        <td><?= date("d/m/Y H:i", strtotime($u["created_at"])) ?></td>

                                        <td class="d-flex flex-wrap justify-content-center gap-1">

    <?php if ($u['is_deleted'] == 0): ?>

        <!-- ====== QUYỀN ADMIN ====== -->
<?php if ($_SESSION['user']['role'] == 'admin'): ?>

    <?php if (in_array($u['role'], ['admin', 'staff'])): ?>

        <?php if ($u["role"] == "admin"): ?>

            <?php if ($_SESSION['user']['user_id'] != $u['user_id']): ?>
                <a href="<?= APP_URL ?>/TaiKhoan/revokeRole/<?= $u["user_id"] ?>" 
                   class="btn btn-secondary btn-sm">🔽 Hạ quyền</a>
            <?php else: ?>
                <span class="text-muted">⛔ Không thể tự hạ quyền</span>
            <?php endif; ?>

        <?php else: ?>
            <a href="<?= APP_URL ?>/TaiKhoan/editRole/<?= $u["user_id"] ?>" 
               class="btn btn-warning btn-sm">🛠️ Cấp quyền</a>
        <?php endif; ?>

    <?php endif; ?>

<?php endif; ?>



        <!-- ====== QUYỀN STAFF ====== -->
        <?php if ($_SESSION['user']['role'] == "staff"): ?>

            <!-- Staff KHÔNG được chỉnh quyền admin -->
            <?php if ($u["role"] != "admin"): ?>
                <span class="text-muted">⛔ Không có quyền chỉnh sửa quyền</span>
            <?php endif; ?>

        <?php endif; ?>


        <!-- ====== HÀNH ĐỘNG CHUNG (admin + staff) ====== -->
        <a href="<?= APP_URL ?>/TaiKhoan/edit/<?= $u["user_id"] ?>" 
           class="btn btn-info btn-sm text-white">✏️ Sửa</a>

        <a href="<?= APP_URL ?>/TaiKhoan/resetPassword/<?= $u["user_id"] ?>" 
           class="btn btn-sm btn-outline-danger">🔑 Reset</a>

        <?php if ($_SESSION['user']['user_id'] != $u['user_id']): ?>
            <a href="<?= APP_URL ?>/TaiKhoan/delete/<?= $u['user_id'] ?>" 
               class="btn btn-danger btn-sm"
               onclick="return confirm('Bạn có chắc muốn xoá tài khoản này?');">
               🗑️ Xoá
            </a>
        <?php else: ?>
            <span class="text-muted">⛔ Không thể XÓA</span>
        <?php endif; ?>

    <?php else: ?>

        <!-- KHÔI PHỤC TÀI KHOẢN -->
        <a href="<?= APP_URL ?>/TaiKhoan/restoreUser/<?= $u['user_id'] ?>" 
           class="btn btn-success btn-sm"
           onclick="return confirm('Khôi phục tài khoản này?');">
           🔄 Khôi phục
        </a>

    <?php endif; ?>

</td>

                                    </tr>
                                <?php
                                    }
                                } else {
                                ?>
                                    <tr>
                                        <td colspan="9" class="text-center text-muted py-4">
                                            Chưa có tài khoản nào.
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>

                        </table>
                    </div>
                </div>
            </div>

        </div> <!-- END TAB 1 -->


<!-- ========================= -->
<!-- TAB 2: QUẢN LÝ NGƯỜI DÙNG -->
<!-- ========================= -->
<div class="tab-pane fade" id="tab-users">

    <h3 class="text-primary mb-3">🧑‍🤝‍🧑 Quản lý khách hàng</h3>
                <div class="card mt-4" style="max-width: 600px; margin: auto;">
    <div class="card-header bg-primary text-white">
        Top 5 khách hàng chi tiêu nhiều nhất
    </div>
    <div class="card-body">
        <canvas id="chartTopCustomers" style="height:140px;"></canvas>
    </div>
</div>


    <!-- Form tìm kiếm -->
    <form method="get" action="">
        <div class="d-flex gap-2 mb-3">
            <input type="hidden" name="tab" value="users">

            <input type="text" name="keyword"
                   class="form-control"
                   placeholder="Tìm theo tên hoặc email..."
                   value="<?= isset($_GET['keyword']) ? htmlspecialchars($_GET['keyword']) : '' ?>">
            
            <button class="btn btn-primary">Tìm</button>
        </div>
    </form>

    <div class="card shadow-sm">
        <div class="card-header bg-dark text-white">
            <strong>Danh sách khách hàng</strong>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0 align-middle text-center">
                    <thead class="table-light">
    <tr>
        <th>#</th>
        <th>Ảnh</th>
        <th>Họ tên</th>
        <th>Email</th>
        <th>Lượt mua</th>
        <th>Sản phẩm đã mua</th>
        <th>AOV</th>
        <th>Tổng chi tiêu</th>
        <th>Lần mua gần nhất</th>
        <th>Hạng</th>
        <th>Ngày tạo</th>
    </tr>
</thead>


                   <tbody>
<?php 
if (!empty($data['customers'])) {
    $i = 1;
    foreach ($data['customers'] as $c) {
?>
<tr>
    <td><?= $i++ ?></td>

    <td>
        <img src="<?= APP_URL ?>/public/images/avatars/<?= $c['avatar'] ?: 'user-default.png' ?>"
            width="55" height="55"
            style="border-radius:50%; object-fit:cover;">
    </td>

    <td><?= htmlspecialchars($c['fullname']) ?></td>
    <td><?= htmlspecialchars($c['email']) ?></td>

    <td><span class="badge bg-info text-dark"><?= $c['total_orders'] ?></span></td>

    <td><?= $c['total_products'] ?></td>

    <td><?= number_format($c['aov']) ?>₫</td>

    <td><strong class="text-danger"><?= number_format($c['total_spent']) ?>₫</strong></td>

    <td>
        <?= $c['last_order_date'] 
                ? date("d/m/Y H:i", strtotime($c['last_order_date'])) 
                : "<span class='text-muted'>Chưa mua</span>" ?>
    </td>

    <td>
        <?php if ($c['rank'] == "VIP") { ?>
            <span class="badge bg-warning text-dark">VIP</span>
        <?php } elseif ($c['rank'] == "Thân thiết") { ?>
            <span class="badge bg-primary">Thân thiết</span>
        <?php } else { ?>
            <span class="badge bg-secondary">Mới</span>
        <?php } ?>
    </td>

    <td><?= date("d/m/Y H:i", strtotime($c['created_at'])) ?></td>
</tr>

<?php 
    }
} else { 
?>
<tr>
    <td colspan="11" class="text-muted py-3">
        Không tìm thấy khách hàng nào.
    </td>
</tr>
<?php } ?>
</tbody>

                </table>

            </div>
        </div>
    </div>

</div>

</div>
 <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
var customers = <?= json_encode($data['customers']) ?>;

// Lấy top 5
customers = customers.slice(0, 5);

var labels = customers.map(c => c.fullname);
var values = customers.map(c => c.total_spent);

new Chart(document.getElementById('chartTopCustomers'), {
    type: 'bar',
    data: {
        labels: labels,
        datasets: [{
            label: "Tổng chi tiêu",
            data: values,
        }]
    },
    options: {
        plugins: {
            legend: { display: false }
        },
        scales: {
            y: { beginAtZero: true }
        }
    }
});
</script>
