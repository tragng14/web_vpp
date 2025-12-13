<?php

// Dữ liệu từ controller
$product = $data['product'] ?? null;
$userWishlist = isset($data['wishlist']) && is_array($data['wishlist']) ? $data['wishlist'] : [];
$reviewImages = isset($data['reviewImages']) && is_array($data['reviewImages']) ? $data['reviewImages'] : []; // mapping danhgia_id => [rows...]
$avgRating = $data['avgRating'] ?? null;
$reviews = $data['reviews'] ?? [];
$csrfToken = $_SESSION['csrf_token'] ?? '';

// Nếu không có product -> hiển thị thông báo ngắn
if (empty($product) || !is_array($product)) {
    echo '<div class="container text-center my-5">'
       . '<h4 style="color:#e63946;">Sản phẩm không tồn tại.</h4>'
       . '<a href="' . (defined('APP_URL') ? APP_URL : '') . '/Home/productList" class="btn btn-outline-primary mt-3">Quay lại cửa hàng</a>'
       . '</div>';
    return;
}

// Hàm escape
if (!function_exists('h')) {
    function h($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
}

// Cấu hình cơ bản
$baseApp = defined('APP_URL') ? rtrim(APP_URL, '/') : '';
$soluong = isset($product['soluong']) ? (int)$product['soluong'] : 0;
$masp = (string)($product['masp'] ?? $product['id'] ?? '');
$isFavServer = in_array($masp, $userWishlist, true);

// Xác định public root vật lý (dựa trên cấu trúc project)
$publicRoot = realpath(__DIR__ . '/../../public');
if ($publicRoot === false) $publicRoot = __DIR__ . '/../../public';

/**
 * buildReviewImageUrl
 * Trả về URL công khai cho ảnh review dựa trên metadata $imgRecord (filepath/filename).
 * Logic:
 *  - Nếu metadata chứa path (có dấu '/'), kiểm tra file vật lý publicRoot/<path>.
 *      + Nếu tồn tại, trả APP_URL/<path-encoded>
 *      + Ngược lại fallback APP_URL/public/<path-encoded>
 *  - Nếu metadata chỉ là filename, kiểm tra publicRoot/images/reviews/<filename>.
 *      + Nếu tồn tại, trả APP_URL/images/reviews/<filename-encoded>
 *      + Ngược lại fallback APP_URL/public/images/reviews/<filename-encoded>
 * Trả null nếu không thể tạo URL.
 */
function buildReviewImageUrl(array $imgRecord, string $baseApp, string $publicRoot): ?string {
    $fp = '';
    if (!empty($imgRecord['filepath'])) $fp = trim((string)$imgRecord['filepath']);
    elseif (!empty($imgRecord['filename'])) $fp = trim((string)$imgRecord['filename']);

    if ($fp === '') return null;

    // Nếu fp có segment (vd 'images/reviews/xxx.jpg' hoặc 'uploads/reviews/xxx.jpg')
    if (strpos($fp, '/') !== false) {
        $candidatePhysical = rtrim($publicRoot, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, ltrim($fp, '/'));
        // nếu file tồn tại ở publicRoot/<fp>
        if (file_exists($candidatePhysical) && is_file($candidatePhysical)) {
            $segments = explode('/', ltrim($fp, '/'));
            $encoded = implode('/', array_map('rawurlencode', $segments));
            return rtrim($baseApp, '/') . '/' . $encoded;
        }
        // fallback: thử `public/` trước đường dẫn (dev config)
        $segments = explode('/', ltrim($fp, '/'));
        $encoded = implode('/', array_map('rawurlencode', $segments));
        return rtrim($baseApp, '/') . '/public/' . $encoded;
    }

    // Nếu chỉ là filename -> kiểm tra public/images/reviews/<filename>
    $filename = basename($fp);
    $physical = rtrim($publicRoot, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'reviews' . DIRECTORY_SEPARATOR . $filename;
    if (file_exists($physical) && is_file($physical)) {
        return rtrim($baseApp, '/') . '/images/reviews/' . rawurlencode($filename);
    }

    // fallback general
    return rtrim($baseApp, '/') . '/public/images/reviews/' . rawurlencode($filename);
}

// chuẩn hoá avg
$avgVal = 0.0; $avgCount = 0;
if (is_array($avgRating)) {
    $avgVal = isset($avgRating['avg']) ? floatval($avgRating['avg']) : 0.0;
    $avgCount = isset($avgRating['count']) ? intval($avgRating['count']) : 0;
}
$rounded = (int) round($avgVal);
?>
<div class="container my-5 product-detail">
    <div class="row">
        <div class="col-md-6">
            <div class="image-box">
                <?php
                // Ảnh sản phẩm chính (không phải ảnh đánh giá)
                $imgRaw = $product['hinhanh'] ?? '';
                $imgSafe = trim((string)$imgRaw);
                $imgSafe = $imgSafe !== '' ? basename($imgSafe) : '';
                // Đây giữ nguyên cách bạn phục vụ ảnh sản phẩm; nếu public là document root, đổi theo APP_URL/images/...
                $imgPath = $imgSafe !== '' ? $baseApp . '/public/images/' . rawurlencode($imgSafe) : $baseApp . '/public/images/defaut.png';
                ?>
                <img src="<?= h($imgPath) ?>" class="img-fluid" alt="<?= h($product['tensp'] ?? 'Sản phẩm') ?>">
            </div>
        </div>

        <div class="col-md-6 product-info">
            <h2 class="product-name"><?= h($product['tensp'] ?? '') ?></h2>
            <p class="product-code">Mã sản phẩm: <strong><?= h($masp) ?></strong></p>

            <?php
            $giaGoc = isset($product['giaXuat']) ? floatval($product['giaXuat']) : 0.0;
            $giaSauKM = $giaGoc;
            $promo_type = $product['promo_type'] ?? null;
            $promo_value = isset($product['promo_value']) ? floatval($product['promo_value']) : null;
            if (!empty($promo_type) && $promo_value !== null) {
                if (strtolower($promo_type) === 'percent') {
                    $giaSauKM = $giaGoc * (1 - $promo_value / 100);
                } elseif (strtolower($promo_type) === 'amount' || strtolower($promo_type) === 'fixed') {
                    $giaSauKM = max(0, $giaGoc - $promo_value);
                }
            }
            ?>
            <div class="price-box">
                <p>
                    <span class="gia-sau"><?= number_format($giaSauKM, 0, ',', '.') ?> ₫</span>
                    <?php if ($giaGoc > $giaSauKM): ?>
                        <span class="gia-goc"><?= number_format($giaGoc, 0, ',', '.') ?> ₫</span>
                    <?php endif; ?>
                </p>
            </div>

            <p class="product-desc"><?= nl2br(h($product['mota'] ?? '')) ?></p>

            <p class="product-stock">
                Số lượng :
                <span class="<?= $soluong > 0 ? 'stock-available' : 'stock-out' ?>">
                    <?= h((string)$soluong) ?>
                </span>
            </p>

            <div class="d-flex align-items-center" style="gap:12px;">
                <?php if ($soluong > 0): ?>
                    <a href="<?= h($baseApp) ?>/Home/addtocard/<?= urlencode($masp) ?>" class="btn btn-primary">Thêm vào giỏ</a>
                <?php else: ?>
                    <button class="btn btn-disabled" disabled>Sản phẩm đã hết hàng</button>
                <?php endif; ?>

                <button
                    id="wishlistBtn"
                    class="btn-wishlist <?= $isFavServer ? 'fav-active' : '' ?>"
                    data-product="<?= h($masp) ?>"
                    aria-label="Thêm vào yêu thích"
                    type="button"
                >
                    <?= $isFavServer ? '❤' : '♡' ?>
                </button>
            </div>
        </div>
    </div>

    <hr>

    <h4>Đánh giá sản phẩm</h4>

    <div class="mb-3">
        <strong>Điểm trung bình:</strong>
        <span class="ms-2">
            <?= str_repeat('★', max(0, min(5, $rounded))) . str_repeat('☆', 5 - max(0, min(5, $rounded))) ?>
        </span>
        <small class="text-muted">
            (<?= number_format($avgVal, 2) ?> trên 5 — <?= intval($avgCount) ?> đánh giá)
        </small>
    </div>

    <?php if (!empty($reviews) && is_array($reviews)): ?>
        <?php foreach ($reviews as $r): ?>
            <div class="card mb-3 border-0 shadow-sm">
                <div class="card-body">
                    <?php
                        $displayName = $r['tenNguoiDung'] ?? 'Khách';
                        $displayEmail = $r['email'] ?? '';
                        $sao = isset($r['sao']) ? intval($r['sao']) : 0;
                        $sao = max(0, min(5, $sao));
                    ?>
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <strong><?= h($displayName) ?></strong>
                            <?php if (!empty($displayEmail)): ?>
                                <div><small class="text-muted"><?= h($displayEmail) ?></small></div>
                            <?php endif; ?>
                        </div>
                        <div>
                            <span class="text-warning">
                                <?= str_repeat('★', $sao) . str_repeat('☆', 5 - $sao) ?>
                            </span>
                        </div>
                    </div>

                    <p class="mt-2 mb-1"><?= nl2br(h($r['noidung'] ?? '')) ?></p>
                    <small class="text-muted"><?= h($r['ngayDang'] ?? '') ?></small>

                    <?php
                    // Hiển thị ảnh đánh giá (nếu có) — sử dụng buildReviewImageUrl()
                    $thisRevImages = [];
                    if (!empty($reviewImages) && isset($reviewImages[$r['id']])) {
                        $thisRevImages = $reviewImages[$r['id']];
                    }

                    if (!empty($thisRevImages)) {
                        echo '<div class="review-images mt-2 d-flex" style="gap:8px;flex-wrap:wrap;">';
                        foreach ($thisRevImages as $img) {
                            $url = buildReviewImageUrl($img, $baseApp, $publicRoot);
                            if (!$url) continue;
                            // Kiểm tra: tránh hiển thị broken image nếu file thực tế không tồn tại (đã checked trong hàm)
                            echo '<a href="'. h($url) .'" target="_blank" rel="noopener noreferrer" style="display:inline-block;">';
                            echo '<img src="'. h($url) .'" alt="ảnh đánh giá" loading="lazy" style="max-width:120px; max-height:120px; object-fit:cover; border-radius:8px; border:1px solid #eee;">';
                            echo '</a>';
                        }
                        echo '</div>';
                    }
                    ?>

                    <?php if (!empty($r['traloi'])): ?>
                        <div class="mt-3 ms-3 p-3 border-start border-3 border-primary bg-light rounded">
                            <strong class="text-primary">Phản hồi từ cửa hàng:</strong>
                            <p class="mb-1"><?= nl2br(h($r['traloi'])) ?></p>
                            <small class="text-muted fst-italic">Cảm ơn bạn đã đánh giá sản phẩm của chúng tôi 💬</small>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>Chưa có đánh giá nào cho sản phẩm này.</p>
    <?php endif; ?>

    <?php if (!empty($data['user']) && !empty($data['canReview'])): ?>
        <hr>
        <h5>Gửi đánh giá của bạn</h5>
        <form action="<?= h($baseApp) ?>/ReviewController/submit" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="masp" value="<?= h($product['masp'] ?? '') ?>">
            <input type="hidden" name="_csrf" value="<?= h($csrfToken) ?>">

            <div class="mb-3">
                <label class="form-label">Xếp hạng</label>
                <select name="sao" class="form-select" required>
                    <option value="5">5 - Xuất sắc</option>
                    <option value="4">4 - Tốt</option>
                    <option value="3">3 - Trung bình</option>
                    <option value="2">2 - Kém</option>
                    <option value="1">1 - Rất kém</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Bình luận</label>
                <textarea name="noidung" class="form-control" rows="4" required></textarea>
            </div>

            

        <?php $user = $_SESSION['user'] ?? null; ?>

        <div class="mb-3">
            <label class="form-label">Tên của bạn</label>
            <input type="text" name="tenNguoiDung" class="form-control" value="<?= h($user['fullname'] ?? '') ?>" readonly>
        </div>

        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" value="<?= h($user['email'] ?? '') ?>" readonly>
        </div>

            <button class="btn btn-success" type="submit">Gửi đánh giá</button>
        </form>

    <?php elseif (!empty($data['user']) && empty($data['canReview'])): ?>
        <p class="mt-3 text-danger">
            Bạn chỉ có thể đánh giá khi đã mua sản phẩm này và đơn hàng đã hoàn tất.
        </p>

    <?php else: ?>
        <p class="mt-3">
            Bạn cần đăng nhập để đánh giá.
            <a href="<?= h($baseApp) ?>/AuthController/ShowLogin?next=detail&product=<?= urlencode($product['masp'] ?? '') ?>">
                Đăng nhập
            </a> hoặc mua hàng để đánh giá.
        </p>
    <?php endif; ?>
</div>

<!-- styles + wishlist script (giữ nguyên như trước) -->
<style>
/* wishlist button base */
.btn-wishlist {
  display:inline-flex;
  align-items:center;
  justify-content:center;
  width:40px;
  height:40px;
  border-radius:8px;
  border:1px solid #cbd3d9;
  background:#f3f5f7;
  color:#6b6f73;
  font-size:18px;
  transition: all .18s ease;
  cursor:pointer;
}
.btn-wishlist:hover { transform: translateY(-2px); }

.btn-wishlist.fav-active {
  background: #ffe9ea;
  border-color: #f1a1a6;
  color: #d62828;
  box-shadow: 0 6px 18px rgba(214,40,40,0.12);
}

.container { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color:#333; }
.col-md-6 img { width:100%; height:450px; object-fit:contain; background:#f8f9fa; border-radius:10px; padding:10px; box-shadow:0 0 10px rgba(0,0,0,0.05); }
.price-box { background:#fff5f5; border-radius:8px; padding:10px 15px; margin:15px 0; box-shadow:0 0 6px rgba(255,0,0,0.06); }
.price-box .gia-sau { color:#e60000; font-weight:700; font-size:1.4em; margin-left:5px; }
.price-box .gia-goc { color:#777; text-decoration:line-through; margin-left:10px; font-size:0.95em; }
.btn-primary { background:#007bff; border:none; border-radius:8px; padding:10px 20px; }
.btn-primary:hover { background:#0056b3; }
.card { border-radius:10px; border:1px solid #e0e0e0; transition:0.2s; }
.card:hover { box-shadow:0 0 10px rgba(0,0,0,0.1); }

@media (max-width: 767px) {
  .col-md-6 img { height: 300px; }
  .review-images img { width: 80px; height: 80px; }
}
</style>


<script>
(function(){
    var wishlistBtn = document.getElementById('wishlistBtn');
    if (!wishlistBtn) return;
    if (!wishlistBtn.classList.contains('btn-wishlist')) wishlistBtn.classList.add('btn-wishlist');
    var pid = wishlistBtn.dataset.product;
    if (!pid) return;

    var listEndpoint = '<?= h($baseApp) ?>/wishlist/list';
    var toggleEndpoint = '<?= h($baseApp) ?>/wishlist/toggle';
    var guestKey = 'guest_wishlist';

    function setState(added) {
        wishlistBtn.textContent = added ? '❤' : '♡';
        if (added) wishlistBtn.classList.add('fav-active'); else wishlistBtn.classList.remove('fav-active');
    }

    function loadState() {
        fetch(listEndpoint, { method: 'GET', headers: {'X-Requested-With':'XMLHttpRequest'} })
            .then(function(r){ return r.json(); })
            .then(function(json){
                if (json && json.success && Array.isArray(json.data) && json.data.length) {
                    setState(json.data.indexOf(String(pid)) !== -1);
                    localStorage.setItem(guestKey, JSON.stringify(json.data));
                } else {
                    var guest = JSON.parse(localStorage.getItem(guestKey) || '[]');
                    setState(Array.isArray(guest) && guest.indexOf(String(pid)) !== -1);
                }
            }).catch(function(){
                var guest = JSON.parse(localStorage.getItem(guestKey) || '[]');
                setState(Array.isArray(guest) && guest.indexOf(String(pid)) !== -1);
            });
    }

    function toggle() {
        var currentlyAdded = wishlistBtn.classList.contains('fav-active');
        setState(!currentlyAdded);
        fetch(toggleEndpoint, {
            method: 'POST',
            headers: {'Content-Type':'application/json', 'X-Requested-With':'XMLHttpRequest'},
            body: JSON.stringify({ product_id: pid })
        }).then(function(r){ return r.json(); })
          .then(function(json){
            if (json && json.success) {
                var added = (json.action === 'added');
                setState(added); syncLocalWithServer(pid, added);
            } else { guestToggle(); }
          }).catch(function(){ guestToggle(); });
    }

    function guestToggle() {
        var arr = JSON.parse(localStorage.getItem(guestKey) || '[]'); var idx = arr.indexOf(String(pid));
        if (idx === -1) { arr.push(String(pid)); setState(true); } else { arr.splice(idx,1); setState(false); }
        localStorage.setItem(guestKey, JSON.stringify(arr));
    }

    function syncLocalWithServer(pid, added) {
        var arr = JSON.parse(localStorage.getItem(guestKey) || '[]'); var idx = arr.indexOf(String(pid));
        if (added && idx === -1) arr.push(String(pid)); else if (!added && idx !== -1) arr.splice(idx,1);
        localStorage.setItem(guestKey, JSON.stringify(arr));
    }

    wishlistBtn.addEventListener('click', function(e){ e.preventDefault(); toggle(); });
    document.addEventListener('DOMContentLoaded', loadState);
})();
</script>
