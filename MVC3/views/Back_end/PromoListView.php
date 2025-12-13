<div class="container py-4">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="text-primary mb-0"><i class="bi bi-ticket-detailed"></i> Quản lý mã khuyến mãi</h3>
        <a href="<?= APP_URL ?>/Promo/create" class="btn btn-success">
            <i class="bi bi-plus-circle"></i> Thêm mã khuyến mãi
        </a>
    </div>

    <!-- Search Box -->
<form method="get" action="<?= APP_URL ?>/Promo/show" 
      class="mb-3 d-flex" style="max-width: 350px;">

    <input type="text"
           name="keyword"
           placeholder="Tìm theo mã / loại / trạng thái..."
           value="<?= isset($data['keyword']) ? htmlspecialchars($data['keyword']) : '' ?>"
           class="form-control me-2">

    <button class="btn btn-primary">
        <i class="bi bi-search"></i> Tìm
    </button>
</form>

<form method="get" action="<?= APP_URL ?>/Promo/filter" class="row g-3 mb-3">

    <div class="col-md-3">
        <label class="form-label fw-bold">Loại khuyến mãi</label>
        <select name="type" class="form-select">
            <option value="">-- Tất cả --</option>
            <option value="percent"  <?= ($data['filter']['type'] ?? '') == 'percent' ? 'selected' : '' ?>>Giảm %</option>
            <option value="amount"   <?= ($data['filter']['type'] ?? '') == 'amount' ? 'selected' : '' ?>>Giảm tiền</option>
          
        </select>
    </div>

    <div class="col-md-3">
        <label class="form-label fw-bold">Trạng thái</label>
        <select name="status" class="form-select">
            <option value="">-- Tất cả --</option>
            <option value="active"   <?= ($data['filter']['status'] ?? '') == 'active' ? 'selected' : '' ?>>Đang hoạt động</option>
            <option value="inactive" <?= ($data['filter']['status'] ?? '') == 'inactive' ? 'selected' : '' ?>>Tạm ngưng</option>
            <option value="deleted"  <?= ($data['filter']['status'] ?? '') == 'deleted' ? 'selected' : '' ?>>Hết hạn</option>
        </select>
    </div>

    <div class="col-md-3">
        <label class="form-label fw-bold">Ngày kết thúc</label>
        <input type="date"
               name="date"
               value="<?= $data['filter']['date'] ?? '' ?>"
               class="form-control">
    </div>

    <div class="col-md-3 d-flex align-items-end">
        <button class="btn btn-primary w-100">
            <i class="bi bi-funnel"></i> Lọc
        </button>
    </div>

</form>

    <!-- Danh sách mã khuyến mãi -->
    <div class="card shadow-sm">
        <div class="card-header bg-dark text-white">
            <strong><i class="bi bi-list-ul"></i> Danh sách mã khuyến mãi</strong>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0 align-middle text-center">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Mã khuyến mãi</th>
                            <th>Loại giảm giá</th>
                            <th>Giá trị</th>
                            <th>Tổng tối thiểu</th>
                            <th>Giới hạn sử dụng</th>
                            <th>Đã dùng</th>
                            <th>Ngày bắt đầu</th>
                            <th>Ngày kết thúc</th>
                            <th>Trạng thái</th>
                            <th>Ngày tạo</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (!empty($data['promoList'])) {
                            $i = 1;
                            foreach ($data['promoList'] as $promo) {

                                // Định dạng ngày
                                $startDate = date('d/m/Y', strtotime($promo['start_date']));
                                $endDate   = date('d/m/Y', strtotime($promo['end_date']));
                                $createdAt = date('d/m/Y', strtotime($promo['created_at']));

                                // Hiển thị loại giảm giá
                                $typeLabel = ($promo['type'] == 'percent') ? 'Giảm theo phần trăm (%)' : 'Giảm theo số tiền (VNĐ)';

                                // Hiển thị giá trị
                                $valueDisplay = ($promo['type'] == 'percent')
                                    ? htmlspecialchars($promo['value']) . '%'
                                    : number_format($promo['value'], 0, ',', '.') . ' ₫';

                                // Hiển thị trạng thái
                                $statusLabels = [
                                    'active'   => '<span class="badge bg-success">Đang hoạt động</span>',
                                    'inactive' => '<span class="badge bg-warning text-dark">Tạm ngưng</span>',
                                    'deleted'  => '<span class="badge bg-secondary">Hết hạn</span>',
                                ];
                                
                                
                                $statusDisplay = $statusLabels[$promo['status']] ?? htmlspecialchars($promo['status']);
                        ?>
                            <tr>
                                <td><?= $i++ ?></td>
                                <td><strong><?= htmlspecialchars($promo['code']) ?></strong></td>
                                <td><?= $typeLabel ?></td>
                                <td><?= $valueDisplay ?></td>
                                <td><?= number_format($promo['min_total'], 0, ',', '.') ?> ₫</td>
                                <td><?= $promo['usage_limit'] ? htmlspecialchars($promo['usage_limit']) : '-' ?></td>
                                <td><?= htmlspecialchars($promo['used_count']) ?></td>
                                <td><?= $startDate ?></td>
                                <td><?= $endDate ?></td>
                                <td><?= $statusDisplay ?></td>
                                <td><?= $createdAt ?></td>
                                <td>
                                    <a href="<?= APP_URL ?>/Promo/edit/<?= $promo['code'] ?>" class="btn btn-warning btn-sm">
                                        ✏️ Sửa
                                    </a>
                                    
                                    <a href="<?= APP_URL ?>/Promo/delete/<?= $promo['code'] ?>" 
                                       class="btn btn-danger btn-sm"
                                       onclick="return confirm('Bạn có chắc muốn xoá mã khuyến mãi này?');">
                                        🗑️ Xoá
                                    </a>
                                </td>
                            </tr>
                        <?php
                            }
                        } else {
                        ?>
                            <tr>
                                <td colspan="12" class="text-center text-muted py-4">
                                    <i class="bi bi-exclamation-circle"></i> Không có mã khuyến mãi nào.
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
