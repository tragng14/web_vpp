<?php
// views/Font_end/OrderView.php
// Phiên bản: chỉ sửa những gì cần thiết để hoạt động ổn định
// - Sửa việc load all products (tránh gọi phương thức không tồn tại)
// - Chuẩn hoá lấy giá (hỗ trợ nhiều key và chuỗi có kí tự)
// - Giới hạn gợi ý tối đa 4 sản phẩm
// - Giữ nguyên mọi chức năng (hiển thị giỏ, cập nhật, xóa, checkout, gợi ý)
// - Tất cả chú thích bằng tiếng Việt

if (!empty($data['success'])): ?>
    <div class="alert alert-success text-center mt-3">
        <?= htmlspecialchars($data['success']) ?>
    </div>
<?php endif; ?>

<?php if (!empty($data['error'])): ?>
    <div class="alert alert-danger text-center mt-3">
        <?= $data['error'] ?>
    </div>
<?php endif; ?>

<?php
// ------------------------
// Helpers an toàn
// ------------------------
if (!function_exists('h')) {
    function h($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
}
if (!function_exists('fmt')) {
    // Format số thành VNĐ (1.000.000)
    function fmt($n) { return number_format(floatval($n), 0, ',', '.'); }
}
if (!function_exists('clean_price')) {
    // Loại bỏ ký tự không phải số/dấu chấm/dấu trừ rồi trả về float
    function clean_price($raw) {
        $str = (string)$raw;
        // Thay dấu phẩy thành dấu chấm nếu cần
        $str = str_replace(',', '.', $str);
        // Giữ lại chỉ chữ số, dấu chấm và dấu trừ
        $s = preg_replace('/[^0-9\.\-]/', '', $str);
        if ($s === '') return 0.0;
        return floatval($s);
    }
}

// ------------------------
// Chuẩn bị dữ liệu
// ------------------------
$cartItems = !empty($data["listProductOrder"]) && is_array($data["listProductOrder"]) ? $data["listProductOrder"] : [];
$allProducts = !empty($data['allProducts']) && is_array($data['allProducts']) ? $data['allProducts'] : [];

// Nếu controller không truyền allProducts, cố gắng fallback load model (nhẹ, an toàn)
$productModel = null;
if (empty($allProducts)) {
    // Nhiều vị trí có thể chứa model -> thử từng đường dẫn
    $possible = [
        __DIR__ . '/../models/AdProducModel.php',
        __DIR__ . '/../../models/AdProducModel.php',
        __DIR__ . '/../../../models/AdProducModel.php',
        __DIR__ . '/models/AdProducModel.php'
    ];
    foreach ($possible as $p) {
        if (file_exists($p)) {
            try {
                require_once $p;
                if (class_exists('AdProducModel')) {
                    // Thử khởi tạo (không ném lỗi ra ngoài)
                    try { $productModel = new AdProducModel(); } catch (Throwable $e) { $productModel = null; }
                }
            } catch (Throwable $e) {
                $productModel = null;
            }
            break;
        }
    }

    // Nếu có model, cố lấy danh sách sản phẩm nhưng chỉ khi model có method phù hợp
    if ($productModel) {
        try {
            $tmp = null;
            // ưu tiên phương thức all($table) nếu tồn tại
            if (is_callable([$productModel, 'all'])) {
                // gọi an toàn trong try/catch vì một số model có signature khác
                try { $tmp = $productModel->all("tblsanpham"); } catch (Throwable $e) { $tmp = null; }
            }
            // fallback getAll()
            if (empty($tmp) && is_callable([$productModel, 'getAll'])) {
                try { $tmp = $productModel->getAll(); } catch (Throwable $e) { $tmp = null; }
            }
            // fallback getProductsWithCategory()
            if (empty($tmp) && is_callable([$productModel, 'getProductsWithCategory'])) {
                try { $tmp = $productModel->getProductsWithCategory(); } catch (Throwable $e) { $tmp = null; }
            }
            if (is_array($tmp)) $allProducts = $tmp;
        } catch (Throwable $e) {
            // ignore lỗi, để $allProducts rỗng
        }
    }
}

// ------------------------
// Thu thập mã loại (category codes) từ giỏ hàng
// ------------------------
$inCartIds = [];
$cartTypes = [];
$productCacheById = [];

foreach ($cartItems as $it) {
    $pid = (string)($it['masp'] ?? $it['id'] ?? '');
    if ($pid !== '') $inCartIds[] = $pid;

    // nhiều tên trường có thể chứa mã loại
    $ptype = $it['maLoaiSP'] ?? $it['maLoai'] ?? $it['maloai'] ?? '';
    if ($ptype !== '') {
        $cartTypes[] = (string)$ptype;
        $productCacheById[$pid] = $it;
    } else {
        // nếu không có mã loại trong item, thử lấy từ model khi có
        if ($productModel && $pid !== '' && !isset($productCacheById[$pid])) {
            try {
                $prod = null;
                if (is_callable([$productModel, 'getProductById'])) $prod = $productModel->getProductById($pid);
                elseif (is_callable([$productModel, 'find'])) $prod = $productModel->find("tblsanpham", $pid);
                elseif (is_callable([$productModel, 'get'])) $prod = $productModel->get($pid);
                if (!empty($prod) && is_array($prod)) {
                    $productCacheById[$pid] = $prod;
                    $ptype2 = $prod['maLoaiSP'] ?? $prod['maLoai'] ?? $prod['maloai'] ?? '';
                    if ($ptype2 !== '') $cartTypes[] = (string)$ptype2;
                }
            } catch (Throwable $e) {
                // ignore
            }
        }
    }
}
$cartTypes = array_values(array_unique(array_filter($cartTypes, function($v){ return (string)$v !== ''; })));

// ------------------------
// Chọn sản phẩm gợi ý — cùng mã loại, loại trừ sp trong giỏ, giới hạn 4
// ------------------------
$recs = [];
if (!empty($cartTypes) && !empty($allProducts) && is_array($allProducts)) {
    foreach ($allProducts as $prod) {
        // nhiều tên trường chứa mã loại
        $ptype = $prod['maLoaiSP'] ?? $prod['maLoai'] ?? $prod['maloai'] ?? '';
        if ($ptype === '' && $productModel) {
            // thử load đầy đủ 1 item nếu model hỗ trợ
            $pid_try = (string)($prod['masp'] ?? $prod['id'] ?? '');
            if ($pid_try !== '' && !isset($productCacheById[$pid_try])) {
                try {
                    $full = null;
                    if (is_callable([$productModel, 'getProductById'])) $full = $productModel->getProductById($pid_try);
                    elseif (is_callable([$productModel, 'find'])) $full = $productModel->find("tblsanpham", $pid_try);
                    if (!empty($full) && is_array($full)) {
                        $ptype = $full['maLoaiSP'] ?? $full['maLoai'] ?? $full['maloai'] ?? '';
                        $productCacheById[$pid_try] = $full;
                    }
                } catch (Throwable $e) {
                    // ignore
                }
            }
        }
        if ($ptype === '') continue;
        if (!in_array((string)$ptype, $cartTypes, true)) continue;

        $pid = (string)($prod['masp'] ?? $prod['id'] ?? '');
        if ($pid === '') continue;
        if (in_array($pid, $inCartIds, true)) continue; // loại trừ sản phẩm đã có trong giỏ

        // thêm vào gợi ý
        $recs[] = $prod;
        if (count($recs) >= 4) break; // GIỚI HẠN 4 SẢN PHẨM
    }
}

// ------------------------
// Bắt đầu render HTML
// ------------------------
?>

<form action="<?= APP_URL ?>/Home/update" method="post">
<div class="container my-5">
    <h2 class="mb-4">🛒 Giỏ Hàng Của Bạn</h2>

    <table class="table table-bordered table-hover align-middle">
        <thead class="table-dark">
            <tr>
                <th>STT</th>
                <th>Sản phẩm</th>
                <th>Giá bán</th>
                <th>Khuyến Mãi</th>
                <th>Số lượng</th>
                <th>Thành Tiền</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
        <?php 
            if (!empty($cartItems)): 
                $i=0;
                foreach ($cartItems as $k => $v): 
                    $i++;

                    // Lấy qty an toàn
                    $qty = max(1, intval($v['qty'] ?? 1));

                    // Lấy giá thô, hỗ trợ nhiều key và chuỗi có kí tự
                    $price_raw = $v['giaxuat'] ?? $v['giaXuat'] ?? $v['price'] ?? 0;
                    $giaxuat = clean_price($price_raw);

                    // Tính thành tiền (áp khuyến mãi nếu có)
                    $thanhtien = $giaxuat * $qty;
                    if (!empty($v['promo_code'])) {
                        $ptype = strtolower($v['promo_type'] ?? '');
                        $pval = isset($v['promo_value']) ? clean_price($v['promo_value']) : (isset($v['promo']) ? clean_price($v['promo']) : 0);
                        if ($ptype === 'percent' && $pval > 0) {
                            $discount = $giaxuat * ($pval / 100.0);
                            $thanhtien = max(($giaxuat - $discount) * $qty, 0);
                        } elseif ($ptype === 'amount' && $pval > 0) {
                            $thanhtien = max(($giaxuat - $pval) * $qty, 0);
                        }
                    }
        ?>
            <tr>
                <td><?= $i ?></td>
                <td>
                    <img src="<?= h(APP_URL . '/public/images/' . ($v['hinhanh'] ?? '')) ?>" 
                         class="card-img-top" style="width: 100%; height: 9rem; object-fit: contain;" alt="<?= h($v['tensp'] ?? '') ?>">
                    <br>
                    <?= h($v["masp"] ?? '') ?><br>
                    <?= h($v["tensp"] ?? '') ?>
                 </td>  
                <td><?= fmt($giaxuat) ?> ₫</td>
                <td>
    <?php 
        if (!empty($v['promo_code'])) {
            if (strtolower($v['promo_type'] ?? '') === 'percent') {
                echo h($v['promo_value']) . '% (' . h($v['promo_code']) . ')';
            } elseif (strtolower($v['promo_type'] ?? '') === 'amount') {
                echo fmt($v['promo_value']) . ' ₫ (' . h($v['promo_code']) . ')';
            } else {
                echo h($v['promo_code']);
            }
        } else {
            echo '—';
        }
    ?>
</td>

                <td>
                  <input type="number" name="qty[<?= h($k) ?>]" value="<?= h($qty) ?>" min="1"
                         class="form-control form-control-sm" style="width: 80px;">
                </td>
                <td>
                    <?= fmt($thanhtien) ?> ₫
                </td>
                <td>
                    <a href="<?= APP_URL ?>/Home/delete/<?= h($v['masp'] ?? '') ?>" 
                       class="btn btn-danger btn-sm" 
                       onclick="return confirm('Bạn có chắc muốn xoá sản phẩm này?');">
                        🗑️ Xoá
                    </a>
                </td>
            </tr>
        <?php 
                endforeach; 
            else: 
        ?>
            <tr>
                <td colspan="7" class="text-center">🛍️ Giỏ hàng của bạn đang trống.</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>

    <?php if (!empty($cartItems)): ?>
    <div class="text-end mb-4">
        <button type="submit" class="btn btn-primary">🔄 Cập nhật giỏ hàng</button>
        <a href="<?php echo APP_URL . '/Home/checkStockBeforeCheckout'; ?>" class="btn btn-success ms-2">🛒 Đặt hàng</a>
    </div>
    <?php endif; ?>
<?php if (!empty($cartItems)): ?>
<!-- ====================
     GỢI Ý SẢN PHẨM (CÙNG LOẠI) - GIỚI HẠN 4
     ==================== -->
<div class="recommendations mt-5">
    <h4>✨ Bạn có thể thích (cùng loại với sản phẩm trong giỏ)</h4>

        <?php if (!empty($recs)): ?>
            <div class="row">
                <?php foreach ($recs as $r):
                    $r_id = (string)($r['masp'] ?? $r['id'] ?? '');
                    $r_name = $r['tensp'] ?? $r['ten'] ?? 'Sản phẩm';
                    $r_img = $r['hinhanh'] ?? ($r['images'] ?? '');
                    if (!empty($r_img)) {
                        $r_img = is_array($r_img) ? reset($r_img) : $r_img;
                        $r_img_url = APP_URL . '/public/images/' . rawurlencode($r_img);
                    } else {
                        $r_img_url = '';
                    }

                    // Lấy giá sạch
                    $r_price_raw = $r['giaxuat'] ?? $r['giaXuat'] ?? $r['price'] ?? 0;
                    $r_price = clean_price($r_price_raw);

                    // Tính giá cuối ưu tiên 'final' nếu controller đã cung cấp
                    $r_final = (isset($r['final']) && is_numeric($r['final'])) ? floatval($r['final']) : $r_price;

                    // Nếu chưa có final, áp promo nếu có
                    if ((!isset($r['final']) || $r_final === $r_price) && !empty($r['promo_type'])) {
                        $promoType = strtolower($r['promo_type'] ?? $r['type'] ?? '');
                        $promoValue = isset($r['promo_value']) ? clean_price($r['promo_value']) : (isset($r['value']) ? clean_price($r['value']) : 0);
                        if ($promoType === 'percent' && $promoValue > 0) {
                            $r_final = max($r_price - ($r_price * ($promoValue / 100.0)), 0);
                        } elseif ($promoType === 'amount' && $promoValue > 0) {
                            $r_final = max($r_price - $promoValue, 0);
                        }
                    }

                    // Nhãn khuyến mãi
                    $r_promo_label = '';
                    $promoType2 = strtolower($r['promo_type'] ?? $r['type'] ?? '');
                    $promoVal2 = isset($r['promo_value']) ? clean_price($r['promo_value']) : (isset($r['value']) ? clean_price($r['value']) : 0);
                    if ($promoType2 === 'percent' && $promoVal2 > 0) $r_promo_label = 'Giảm ' . rtrim(rtrim(number_format($promoVal2, 2, ',', '.'), '0'), ',') . '%';
                    elseif ($promoType2 === 'amount' && $promoVal2 > 0) $r_promo_label = '- ' . number_format($promoVal2, 0, ',', '.') . ' ₫';
                ?>
                    <div class="col-6 col-md-3 mb-3">
                        <div class="card h-100">
                            <?php if ($r_img_url): ?>
                                <a href="<?= h(APP_URL . '/Home/detail/' . urlencode($r_id)) ?>">
                                    <img src="<?= h($r_img_url) ?>" class="card-img-top" style="height:140px;object-fit:cover;" alt="<?= h($r_name) ?>">
                                </a>
                            <?php else: ?>
                                <a href="<?= h(APP_URL . '/Home/detail/' . urlencode($r_id)) ?>">
                                    <div style="height:140px;display:flex;align-items:center;justify-content:center;background:#f8f8f8;">
                                        Ảnh tạm
                                    </div>
                                </a>
                            <?php endif; ?>

                            <div class="card-body d-flex flex-column">
                                <h6 class="card-title" style="min-height:2.4em;overflow:hidden;font-size:0.95rem;"><?= h($r_name) ?></h6>

                                <div class="mt-auto">
                                    <?php if ($r_final < $r_price): ?>
                                        <div class="fw-bold text-danger"><?= fmt($r_final) ?> ₫</div>
                                        <div class="small text-muted"><del><?= fmt($r_price) ?> ₫</del></div>
                                        <?php if ($r_promo_label): ?><div class="small text-danger"><?= h($r_promo_label) ?></div><?php endif; ?>
                                    <?php else: ?>
                                        <div class="fw-bold text-danger"><?= fmt($r_price) ?> ₫</div>
                                    <?php endif; ?>
                                    <a href="<?= h(APP_URL . '/Home/addtocard/' . urlencode($r_id)) ?>" class="btn btn-sm btn-outline-primary mt-2 w-100">Thêm vào giỏ</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="text-muted">Chưa có gợi ý — thêm sản phẩm vào giỏ để nhận đề xuất cùng loại.</p>
        <?php endif; ?>
    </div>

</div>
<?php endif; ?>
</form>

<style>
/* CSS nhỏ cho phần gợi ý */
.recommendations .card { border-radius:8px; overflow:hidden; }
.recommendations .card .card-body { padding:10px; }
.recommendations .card-title { margin-bottom:6px; font-size:0.95rem; }
</style>
