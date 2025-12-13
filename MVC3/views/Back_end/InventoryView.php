<div class="container py-4">
<?php
$categories = $data['categories'];
$keyword = isset($data['keyword']) ? htmlspecialchars($data['keyword']) : '';
$selectedCategory = isset($data['selectedCategory']) ? $data['selectedCategory'] : '';
$productList = isset($data['productList']) ? $data['productList'] : [];
?>



<div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="text-primary mb-0">📦 Quản lý tồn kho</h3>
</div>
<!-- Form tìm kiếm -->
<form method="GET" action="<?= APP_URL ?>Inventory/show" class="d-flex align-items-center mb-3">

    <input type="text" name="keyword" 
           placeholder="Nhập tên hoặc mã sản phẩm..." 
           class="form-control me-2"
           style="max-width: 250px;"
           value="<?= $keyword ?>">

    <select name="category" class="form-select me-2" style="max-width: 200px;">
        <option value="">-- Tất cả loại --</option>
        <?php foreach ($categories as $cat): ?>
            <option value="<?= $cat['maLoaiSP'] ?>" 
                <?= ($selectedCategory == $cat['maLoaiSP']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($cat['tenLoaiSP']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <button type="submit" class="btn btn-primary">Tìm kiếm</button>
</form>
<?php
$tongSP = count($productList);
$tongSLNhap = 0;
$tongSLTon = 0;
$tongSLBan = 0;
$tongGiaTriTon = 0;

foreach ($productList as $v) {
    $tongSLNhap += $v['soluongnhap'];
    $tongSLTon += $v['soluong'];
    $tongSLBan += ($v['soluongnhap'] - $v['soluong']);
    $tongGiaTriTon += $v['soluong'] * $v['giaNhap'];
}
?>

<!-- Báo cáo tổng hợp -->
<div class="card mb-4 shadow-sm">
    <div class="card-body bg-light">
        <div class="row text-center">
            <div class="col-md-3 border-end">
                <h6 class="text-muted mb-1">Tổng sản phẩm</h6>
                <h4 class="text-primary fw-bold"><?= $tongSP ?></h4>
            </div>
            <div class="col-md-3 border-end">
                <h6 class="text-muted mb-1">Tổng đã bán</h6>
                <h4 class="text-danger fw-bold"><?= $tongSLBan ?></h4>
            </div>
            <div class="col-md-3 border-end">
                <h6 class="text-muted mb-1">Tổng tồn kho</h6>
                <h4 class="text-success fw-bold"><?= $tongSLTon ?></h4>
            </div>
            <div class="col-md-3">
                <h6 class="text-muted mb-1">Giá trị hàng tồn</h6>
                <h4 class="text-info fw-bold"><?= number_format($tongGiaTriTon, 0, ',', '.') ?> ₫</h4>
            </div>
        </div>
    </div>
</div>

<!-- Danh sách tồn kho -->
<div class="card shadow-sm">
    <div class="card-header bg-dark text-white">
        <strong>Danh sách tồn kho sản phẩm</strong>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped table-hover mb-0 align-middle text-center">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Ảnh</th>
                        <th>Mã SP</th>
                        <th>Tên SP</th>
                        <th>Loại</th>
                        <th>SL nhập</th>
                        <th>SL hiện tại</th>
                        <th>Đã bán</th>
                        <th>Giá trị tồn (₫)</th>
                        <th>Trạng thái</th>
                        <th>Giá nhập</th>
                        <th>Giá xuất</th>
                        <th>Ngày tạo</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($productList)): 
                        $i = 1;
                        foreach ($productList as $v): 
                            $sold = $v['soluongnhap'] - $v['soluong'];
                    ?>
                        <tr>
                            <td><?= $i++ ?></td>
                            <td><img src="<?= APP_URL ?>/public/images/<?= htmlspecialchars($v['hinhanh']) ?>" style="height: 6rem;"></td>
                            <td><?= htmlspecialchars($v['masp']) ?></td>
                            <td><?= htmlspecialchars($v['tensp']) ?></td>
                            <td><?= htmlspecialchars($v['maLoaiSP']) ?></td>
                            <td class="text-info fw-bold"><?= $v['soluongnhap'] ?></td>
                            <td class="text-success fw-bold"><?= $v['soluong'] ?></td>
                            <td class="text-danger fw-bold"><?= $sold ?></td>
                            <td><?= number_format($v['soluong'] * $v['giaNhap'], 0, ',', '.') ?> ₫</td>
                            <td>
                                <?php 
                                if ($v['soluong'] == 0) echo '<span class="text-danger fw-bold">Hết hàng</span>';
                                else if ($v['soluong'] < 5) echo '<span class="text-warning fw-bold">Sắp hết</span>';
                                else echo '<span class="text-success fw-bold">Còn hàng</span>';
                                ?>
                            </td>
                            <td><?= number_format($v['giaNhap'], 0, ',', '.') ?> ₫</td>
                            <td><?= number_format($v['giaXuat'], 0, ',', '.') ?> ₫</td>
                            <td><?= htmlspecialchars($v['createDate']) ?></td>
                        </tr>
                    <?php endforeach; else: ?>
                        <tr>
                            <td colspan="13" class="text-center text-muted py-4">Không có dữ liệu tồn kho.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</div>
