<?php
// Số lượng sản phẩm mỗi trang
$itemsPerPage = 10;

// Tổng sản phẩm
$totalItems = count($data['productList']);

// Tổng số trang
$totalPages = ceil($totalItems / $itemsPerPage);

// Lấy trang hiện tại (mặc định = 1)
$currentPage = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;

// Chỉ số bắt đầu
$start = ($currentPage - 1) * $itemsPerPage;

// Lấy danh sách sản phẩm theo trang
$currentItems = array_slice($data['productList'], $start, $itemsPerPage);

$i = $start + 1; // Đánh số thứ tự
?>

 <div class="container py-4">


    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="text-primary mb-0">Quản lý sản phẩm</h3>
        <a href="<?= APP_URL ?>/Product/create" class="btn btn-success">
            <i class="bi bi-plus-circle"></i> Thêm sản phẩm
        </a>
    </div>
<form method="post" class="d-flex mb-3 gap-2">
    <input type="text" name="keyword" class="form-control" placeholder="Tìm mã, tên, loại, mô tả...">
    <button type="submit" name="btn_search" class="btn btn-primary">Tìm</button>
</form>
    <!-- Danh sách sản phẩm -->
    <div class="card shadow-sm">
        <div class="card-header bg-dark text-white">
            <strong>Danh sách sản phẩm</strong>
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
                            <th>Số lượng</th>
                            <th>Giá nhập</th>
                            <th>Giá xuất</th>
                            <th>KM</th>
                            <th>Mô tả</th>
                            <th>Ngày tạo</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
<?php
if (!empty($currentItems)) {
    foreach ($currentItems as $k => $v) {
?>
<tr>
    <td><?= $i++ ?></td>
    <td>
        <img src="<?php echo APP_URL;?>/public/images/<?= htmlspecialchars($v['hinhanh']) ?>" 
        style="height: 10rem;"/>
    </td>
    <td><?= htmlspecialchars($v["masp"]) ?></td>
    <td><?= htmlspecialchars($v["tensp"]) ?></td>
    <td><?= htmlspecialchars($v["maLoaiSP"]) ?></td>
    <td><?= htmlspecialchars($v["soluong"]) ?></td>
    <td><?= htmlspecialchars($v["giaNhap"]) ?></td>
    <td><?= htmlspecialchars($v["giaXuat"]) ?></td>

    <td>
        <?php 
            $promo = $data['productModel']->getProductPromo($v['masp']); 
            echo $promo 
                ? htmlspecialchars($promo['code'] . ' - ' . ($promo['type']=='percent' ? $promo['value'].'%' : number_format($promo['value'],0,',','.').'đ')) 
                : "Không có KM";
        ?>
    </td>

    <td><?= htmlspecialchars($v["mota"]) ?></td>
    <td><?= htmlspecialchars($v["createDate"]) ?></td>

    <td>
        <a href="<?= APP_URL ?>/Product/edit/<?= $v["masp"] ?>" class="btn btn-warning btn-sm">✏️ Sửa</a>
        <a href="<?= APP_URL ?>/Product/delete/<?= $v["masp"] ?>" class="btn btn-danger btn-sm"
           onclick="return confirm('Bạn có chắc muốn xoá sản phẩm này?');">🗑️ Xoá</a>
    </td>
</tr>
<?php 
    }
} else {
?>
<tr>
    <td colspan="12" class="text-center text-muted py-4">Không có sản phẩm nào.</td>
</tr>
<?php } ?>

                </table>
<div class="p-3">
    <nav>
        <ul class="pagination justify-content-center">

            <!-- Trang trước -->
            <li class="page-item <?= ($currentPage <= 1) ? 'disabled' : '' ?>">
                <a class="page-link" href="?page=<?= $currentPage - 1 ?>">«</a>
            </li>

            <!-- Các số trang -->
            <?php for ($page = 1; $page <= $totalPages; $page++): ?>
                <li class="page-item <?= ($currentPage == $page) ? 'active' : '' ?>">
                    <a class="page-link" href="?page=<?= $page ?>"><?= $page ?></a>
                </li>
            <?php endfor; ?>

            <!-- Trang tiếp -->
            <li class="page-item <?= ($currentPage >= $totalPages) ? 'disabled' : '' ?>">
                <a class="page-link" href="?page=<?= $currentPage + 1 ?>">»</a>
            </li>

        </ul>
    </nav>
</div>

            </div>
        </div>
    </div>
</div>