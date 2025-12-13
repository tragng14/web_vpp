<?php

$products = isset($data['products']) && is_array($data['products']) ? $data['products'] : [];
$categories = isset($data['categories']) && is_array($data['categories']) ? $data['categories'] : [];
$filterCategory = isset($data['filterCategory']) ? (string)$data['filterCategory'] : '';
$searchQuery = isset($data['searchQuery']) ? (string)$data['searchQuery'] : '';
$currentSort = isset($data['sort']) ? (string)$data['sort'] : '';
$currentPage = isset($data['currentPage']) ? (int)$data['currentPage'] : 1;
$perPage = isset($data['per_page']) ? (int)$data['per_page'] : 12;
$total_pages = isset($data['total_pages']) ? (int)$data['total_pages'] : 1;
$total = isset($data['total']) ? (int)$data['total'] : 0;

$min_price = isset($data['min_price']) ? $data['min_price'] : '';
$max_price = isset($data['max_price']) ? $data['max_price'] : '';
$price_range = isset($data['price_range']) ? $data['price_range'] : '';

// NEW: lọc theo đánh giá (min_rating: 1..5 hoặc '' để không lọc)
$min_rating = isset($data['min_rating']) ? $data['min_rating'] : '';

// Danh sách wishlist từ server (mảng các mã sản phẩm) — dùng để render trạng thái ban đầu
$userWishlist = isset($data['wishlist']) && is_array($data['wishlist']) ? $data['wishlist'] : [];

// BASE APP URL (đảm bảo chỉ định nghĩa một lần)
if (!defined('BASE_APP_URL')) {
    define('BASE_APP_URL', defined('APP_URL') ? rtrim(APP_URL, '/') : '');
}

// escape HTML (chỉ định nghĩa nếu chưa có)
if (!function_exists('e')) {
    function e($str) { return htmlspecialchars((string)$str, ENT_QUOTES, 'UTF-8'); }
}

// Lấy URL ảnh (hỗ trợ chuỗi, JSON array, mảng)
if (!function_exists('get_image_url')) {
    function get_image_url($img) {
        if (!$img) return '';
        if (is_string($img)) {
            $trim = trim($img);
            if ($trim === '' || strtolower($trim) === 'array') return '';
            // Nếu là JSON (mảng) -> decode -> lấy phần tử đầu
            if ($trim !== '' && ($trim[0] === '[' || $trim[0] === '{')) {
                $decoded = json_decode($trim, true);
                if (is_array($decoded) && !empty($decoded)) {
                    $first = reset($decoded);
                    $fname = (string)$first;
                } else {
                    return '';
                }
            } else {
                $fname = $trim;
            }

            // Nếu đã chứa đường dẫn public/images thì giữ nguyên (tránh double prefix)
            if (preg_match('#public/images#', $fname)) {
                // chuẩn hoá: nếu là URL đầy đủ thì trả trực tiếp
                if (preg_match('#^https?://#i', $fname)) return $fname;
                // else chuyển thành relative URL
                return rtrim(BASE_APP_URL, '/') . '/' . ltrim($fname, '/');
            }

            // Nếu filename chứa folder 'danhgia' hoặc 'avatars' thì gom vào đúng folder
            if (stripos($fname, 'danhgia') !== false) {
                return BASE_APP_URL . '/public/images/danhgia/' . rawurlencode(basename($fname));
            }
            if (stripos($fname, 'avatars') !== false) {
                return BASE_APP_URL . '/public/images/avatars/' . rawurlencode(basename($fname));
            }

            // Mặc định: public/images/<file>
            return BASE_APP_URL . '/public/images/' . rawurlencode($fname);
        } elseif (is_array($img) && !empty($img)) {
            $first = reset($img);
            return BASE_APP_URL . '/public/images/' . rawurlencode($first);
        }
        return '';
    }
}

// Lấy path gốc (không bao gồm query) để sinh link phân trang
$baseUrl = isset($baseUrl) && $baseUrl ? $baseUrl : (strtok($_SERVER["REQUEST_URI"], '?') ?: '/');
// Sao chép query hiện tại để giữ param khi phân trang / lọc
$currentQuery = isset($_GET) && is_array($_GET) ? $_GET : [];

// Helper format tiền (VNĐ)
$fmtPrice = function($v) {
    return number_format(floatval($v), 0, ',', '.');
};

// Helper: hiển thị sao (tròn đến .5) — trả về HTML (an toàn: small output)
function render_stars($avg, $max = 5) {
    $avg = floatval($avg);
    if ($avg <= 0) return '';
    $full = floor($avg);
    $half = ($avg - $full) >= 0.5 ? 1 : 0;
    $empty = $max - $full - $half;
    $out = '';
    for ($i=0;$i<$full;$i++) $out .= '★';
    if ($half) $out .= '☆'; // giữ ký tự biểu diễn "nửa" (đơn giản)
    for ($i=0;$i<$empty;$i++) $out .= '✩';
    return $out ? '<span class="star-inline" aria-hidden="true">' . e($out) . '</span>' : '';
}
?>
<div class="container my-4">
  <div class="row">
    <!-- Sidebar: danh mục -->
    <aside class="col-md-3 mb-3">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Danh mục</h5>
          <div class="list-group">
            <?php $allActive = ($filterCategory === '') ? 'active' : ''; ?>
            <a href="<?php echo e(BASE_APP_URL . '/ProductFront/'); ?>" class="list-group-item list-group-item-action <?php echo $allActive; ?>">Tất cả</a>

            <?php if (!empty($categories)): ?>
              <?php foreach ($categories as $cat):
                $cid = $cat['maLoaiSP'] ?? $cat['maLoai'] ?? $cat['maloai'] ?? $cat['id'] ?? '';
                $cname = $cat['tenLoaiSP'] ?? $cat['tenloai'] ?? $cat['ten'] ?? $cat['name'] ?? '';
                if ($cname === '' && $cid !== '') $cname = $cid;
                $isActive = ($filterCategory == $cid) ? 'active' : '';
              ?>
                <a href="<?php echo e(BASE_APP_URL . '/ProductFront/?category=' . urlencode($cid)); ?>" class="list-group-item list-group-item-action <?php echo $isActive; ?>">
                  <?php echo e($cname); ?>
                </a>
              <?php endforeach; ?>
            <?php else: ?>
              <div class="list-group-item">Chưa có danh mục.</div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </aside>

    <!-- Danh sách sản phẩm -->
    <section class="col-md-9">
      <div class="products-filter-bar">       
         <h3 class="mb-0">Sản phẩm</h3>

        <!-- Form lọc & sắp xếp: khi submit sẽ giữ các param category / q ... bằng input hidden -->
        <form id="filterForm" method="GET" class="d-flex align-items-center" style="gap:12px;flex-wrap:wrap;">
            <?php if ($filterCategory !== ''): ?><input type="hidden" name="category" value="<?php echo e($filterCategory); ?>"><?php endif; ?>
            <?php if ($searchQuery !== ''): ?><input type="hidden" name="q" value="<?php echo e($searchQuery); ?>"><?php endif; ?>
            <input type="hidden" name="page" value="1">

            <!-- Preset price -->
            <div>
              <select name="price_range" id="price_range" class="form-select form-select-sm">
                <option value="">Mọi mức giá</option>
                <option value="0-100000" <?php echo ($price_range === '0-100000' || ($min_price==0 && $max_price==100000)) ? 'selected' : ''; ?>>0 - 100k</option>
                <option value="100000-300000" <?php echo ($price_range === '100000-300000' || ($min_price==100000 && $max_price==300000)) ? 'selected' : ''; ?>>100k - 300k</option>
                <option value="300000-700000" <?php echo ($price_range === '300000-700000' || ($min_price==300000 && $max_price==700000)) ? 'selected' : ''; ?>>300k - 700k</option>
                <option value="700000-" <?php echo ($price_range === '700000-' || ($min_price==700000 && ($max_price=='' || $max_price===null))) ? 'selected' : ''; ?>>700k+</option>
              </select>
            </div>

            <!-- Min/Max -->
            <div class="d-flex align-items-center" style="gap:6px;">
                <label class="small mb-0">Từ</label>
                <input type="number" class="form-control form-control-sm" name="min_price" id="min_price" min="0" step="1000" placeholder="0" value="<?php echo e($min_price); ?>">
                <label class="small mb-0">đến</label>
                <input type="number" class="form-control form-control-sm" name="max_price" id="max_price" min="0" step="1000" placeholder="Không giới hạn" value="<?php echo e($max_price); ?>">
                <button type="submit" class="btn btn-primary btn-sm">Áp dụng</button>
            </div>

            <!-- NEW: Lọc theo đánh giá -->
            <div>
              <select name="min_rating" class="form-select form-select-sm w-auto" onchange="this.form.submit()">
                <option value="" <?php echo ($min_rating === '') ? 'selected' : ''; ?>>Tất cả đánh giá</option>
                <option value="5" <?php echo ($min_rating === '5' || $min_rating === 5) ? 'selected' : ''; ?>>5 sao</option>
                <option value="4" <?php echo ($min_rating === '4' || $min_rating === 4) ? 'selected' : ''; ?>>4 sao trở lên</option>
                <option value="3" <?php echo ($min_rating === '3' || $min_rating === 3) ? 'selected' : ''; ?>>3 sao trở lên</option>
                <option value="3" <?php echo ($min_rating === '2' || $min_rating === 2) ? 'selected' : ''; ?>>2 sao trở lên</option>
                <option value="1" <?php echo ($min_rating === '1' || $min_rating === 1) ? 'selected' : ''; ?>>Có đánh giá</option>
              </select>
            </div>

            <!-- Sort -->
            <div>
              <select name="sort" class="form-select form-select-sm w-auto" onchange="this.form.submit()">
                  <option value="" <?php echo ($currentSort === '') ? 'selected' : ''; ?>>Mặc định</option>
                  <option value="asc" <?php echo ($currentSort === 'asc') ? 'selected' : ''; ?>>Giá thấp → cao</option>
                  <option value="desc" <?php echo ($currentSort === 'desc') ? 'selected' : ''; ?>>Giá cao → thấp</option>
              </select>
            </div>

            <!-- Pill favorites -->
            <?php $favoritesChecked = (isset($_GET['favorites']) && ($_GET['favorites'] == '1' || $_GET['favorites'] === 1)); ?>
            <div>
              <button id="favoritesToggle" type="button" class="favorites-pill <?php echo $favoritesChecked ? 'active' : ''; ?>" aria-pressed="<?php echo $favoritesChecked ? 'true' : 'false'; ?>">
                <span class="pill-icon">❤</span>
                <span class="pill-text">
                  <span class="pill-line pill-line-1">Sản phẩm</span>
                  <span class="pill-line pill-line-2">
                    <span class="word-left">yêu</span>
                    <span class="word-right">thích</span>
                  </span>
                </span>
              </button>
            </div>
        </form>
      </div>

      <?php if ($searchQuery !== ''): ?>
        <div class="text-muted mb-2">Kết quả tìm: <?php echo e($searchQuery); ?></div>
      <?php endif; ?>

      <!-- Lưới sản phẩm -->
      <div class="row" id="productGrid">
        <?php if (empty($products)): ?>
          <div class="col-12">
            <div class="alert alert-secondary">Không có sản phẩm.</div>
          </div>

        <?php else: foreach ($products as $p):
            // Lấy các trường phổ biến của sản phẩm (linh hoạt với nhiều cấu trúc khác nhau)
            $masp  = $p['masp'] ?? $p['maSP'] ?? $p['id'] ?? '';
            $name  = $p['tensp'] ?? $p['ten'] ?? 'Sản phẩm';
            $img   = $p['hinhanh'] ?? ($p['images'] ?? '');
            $imgUrl = get_image_url($img);

            // Giá đã chuẩn hoá bởi controller: 'price','final','discount_percent','saving','promo_label'
            $price = isset($p['price']) ? floatval($p['price']) : 0.0;
            $final = isset($p['final']) ? floatval($p['final']) : $price;
            $discount_percent = isset($p['discount_percent']) ? (int)$p['discount_percent'] : 0;
            $saving = isset($p['saving']) ? (int)$p['saving'] : (($price > $final) ? round($price - $final) : 0);
            $promoLabel = trim((string)($p['promo_label'] ?? ($discount_percent > 0 ? '-' . $discount_percent . '%' : '')));

            // RATING: hỗ trợ nhiều key khác nhau (controller nên cung cấp avg_rating, rating, rating_count)
            $avgRating = null;
            if (isset($p['avg_rating'])) $avgRating = floatval($p['avg_rating']);
            elseif (isset($p['rating'])) $avgRating = floatval($p['rating']);
            elseif (isset($p['rating_avg'])) $avgRating = floatval($p['rating_avg']);
            $ratingCount = $p['rating_count'] ?? $p['reviews_count'] ?? $p['count_reviews'] ?? 0;

            $detailUrl = BASE_APP_URL . '/Home/detail/' . urlencode((string)$masp);

            $isFav = in_array((string)$masp, $userWishlist, true);

            // Nếu server-side có min_rating và sản phẩm không đủ (bỏ hiển thị) -> (Option: controller nên đã lọc; nhưng để an toàn, client-side cũng lọc nếu min_rating đã set)
            if ($min_rating !== '' && $min_rating !== null) {
                $mr = intval($min_rating);
                // min_rating = 1 means "có đánh giá" -> chỉ cần có ratingCount>0
                if ($mr === 1) {
                    if (intval($ratingCount) <= 0) continue;
                } else {
                    if ($avgRating === null || floatval($avgRating) < $mr) continue;
                }
            }
        ?>

        <div class="col-sm-6 col-md-4 mb-4 product-card" data-product="<?php echo e($masp); ?>">
          <div class="card h-100 position-relative" style="padding-bottom:28px; overflow:visible;">
            <!-- Nút wishlist nằm bên trong card, góc dưới phải -->
           
            <?php if ($discount_percent > 0): ?>
              <span class="badge bg-danger position-absolute" style="right:8px;top:8px; z-index:4;">-<?php echo e($discount_percent); ?>%</span>
            <?php endif; ?>

            <?php if ($imgUrl): ?>
              <a href="<?php echo e($detailUrl); ?>" title="<?php echo e($name); ?>">
                <img src="<?php echo e($imgUrl); ?>" class="card-img-top" style="height:180px;object-fit:cover;" alt="<?php echo e($name); ?>">
              </a>
            <?php else: ?>
              <a href="<?php echo e($detailUrl); ?>" title="<?php echo e($name); ?>">
                <div style="height:180px;display:flex;align-items:center;justify-content:center;background:#f8f8f8;">
                  Ảnh tạm
                </div>
              </a>
            <?php endif; ?>

            <div class="card-body d-flex flex-column">
              <h5 class="card-title" style="min-height:2.6em;overflow:hidden;"><?php echo e($name); ?></h5>

              <!-- Rating: luôn hiển thị, dù không có đánh giá -->
<div class="mb-2 small text-muted text-center">

    <?php
      // Nếu có rating thật
        if ($avgRating !== null && $avgRating > 0) {

    $roundedRating = ceil($avgRating); // 🔥 làm tròn LÊN

    echo '<span class="me-1" title="Đánh giá trung bình: ' . e(number_format($avgRating,1)) . '">';
    echo render_stars($roundedRating);
    echo '</span>';
    echo '<span>(' . intval($ratingCount) . ')</span>';
}else {
          
            echo '<span class="me-1" title="Chưa có đánh giá">';
            echo '✩✩✩✩✩'; // 5 sao rỗng
            echo '</span>';
            echo '<span>(0)</span>';
        }
    ?>
</div>

              

              <div class="price-block">
    <div class="price-inline-inner">
        <span class="final-price"><?php echo $fmtPrice($final); ?> ₫</span>
        <?php if ($final < $price): ?>
            <span class="original-price"><del><?php echo $fmtPrice($price); ?> ₫</del></span>
        <?php endif; ?>
    </div>

    <?php if ($saving > 0): ?>
        <div class="saving-line">Tiết kiệm <?php echo $fmtPrice($saving); ?> ₫</div>
    <?php endif; ?>
</div>

               
              </div>

<div class="mt-auto w-100 d-flex justify-content-between align-items-center gap-2">

    <!-- Nút thêm vào giỏ -->
    <a href="<?= BASE_APP_URL . '/Home/addtocard/' . urlencode($masp) ?>" 
       class="btn btn-primary flex-grow-1 d-flex justify-content-center align-items-center gap-2 px-3"
       style="border-radius: 8px;">
        <i class="bi bi-cart-plus"></i>
        Thêm vào giỏ
    </a>

    <!-- Nút wishlist -->
    <button
        class="wishlist-btn <?php echo $isFav ? 'fav-active' : ''; ?>"
        data-product="<?php echo e($masp); ?>"
        aria-label="<?php echo $isFav ? 'Bỏ yêu thích' : 'Thêm vào yêu thích'; ?>"
        aria-pressed="<?php echo $isFav ? 'true' : 'false'; ?>"
        title="<?php echo $isFav ? 'Đã yêu thích — bấm để bỏ' : 'Thêm vào yêu thích'; ?>"
        type="button"
        style="font-size: 24px; background:none; border:none;">
        <?php echo $isFav ? '❤' : '♡'; ?>
    </button>

</div>

            </div>

       
        </div>

        <?php endforeach; endif; ?>
      </div>

      <!-- Phân trang (GIỮ NGUYÊN CÁC PARAM hiện tại khi sinh link) -->
      <?php if ($total_pages > 1): ?>
        <nav aria-label="Page navigation" class="mt-3">
          <ul class="pagination">
            <?php
              // $currentQuery (lấy từ $_GET) được dùng để giữ các param khác; chỉ thay page
              $preserve = is_array($currentQuery) ? $currentQuery : [];

              for ($i = 1; $i <= $total_pages; $i++):
                $preserve['page'] = $i;
                // Loại bỏ null hoặc chuỗi rỗng để URL gọn hơn
                $query = array_filter($preserve, function($v){ return $v !== null && $v !== ''; });
                $link = $baseUrl . (count($query) ? '?' . http_build_query($query) : '');
            ?>
              <li class="page-item <?php echo ($i == $currentPage) ? 'active' : ''; ?>">
                <a class="page-link" href="<?php echo e($link); ?>"><?php echo $i; ?></a>
              </li>
            <?php endfor; ?>
          </ul>
        </nav>
      <?php endif; ?>

    </section>
  </div>
</div>

<!-- CSS: giữ nguyên style, thêm chú thích tiếng Việt -->
<style>
/* Pill "Sản phẩm yêu thích" - 2 dòng */
.favorites-pill {
  display: inline-flex;
  align-items: center;
  gap: 10px;
  padding: 8px 14px;
  border-radius: 999px;
  background: #fff;
  border: 1px solid #e6e9ee;
  color: #333;
  font-size: 14px;
  line-height: 1;
  cursor: pointer;
  box-shadow: 0 2px 8px rgba(12,12,13,0.04);
  transition: all .14s ease;
  white-space: nowrap;
}
.favorites-pill .pill-icon { font-size: 16px; color: #d33; display:flex; align-items:center; }
.favorites-pill .pill-text { display: flex; flex-direction: column; align-items: flex-start; gap: 0; line-height:1; padding-top:2px; }
.favorites-pill .pill-line-1 { font-size: 12px; color: #666; margin-bottom:2px; display:block; }
.favorites-pill .pill-line-2 { display:flex; align-items:center; gap:8px; font-weight:600; color:#b22222; font-size:14px; }
.favorites-pill .word-left, .favorites-pill .word-right { display:inline-block; }
.favorites-pill.active { background: linear-gradient(180deg,#fff6f6,#fff0f0); border-color: rgba(210,50,50,0.12); color:#b22222; box-shadow:0 8px 20px rgba(210,50,50,0.06); }
.favorites-pill.active .pill-icon { color:#b22222; }

/* Card và nút wishlist đặt **trong** card (không tràn) */
.product-card { position: relative; }
.card { overflow: visible; }
/* Nhãn promo nhỏ (dưới giá) */
.promo-label {
  display: inline-block;
  background: rgba(255,230,230,0.9);
  color: #c92b2b;
  padding: 4px 8px;
  border-radius: 12px;
  font-weight:600;
  font-size: 12px;
  margin-top: 4px;
  border: 1px solid rgba(200,40,40,0.08);
}

/* Dòng tiết kiệm */
.saving-line { color: #0b7a3f; font-weight:600; }

.wishlist-btn {
    position: static !important;   /* rất quan trọng */
    background: white;
    border: 1px solid #ddd;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    cursor: pointer;
}
.wishlist-btn.fav-active {
    color: red;
}


/* trạng thái active (đỏ, nền hồng nhạt) */
.wishlist-btn.fav-active {
  background: linear-gradient(180deg, #fff0f0, #ffecec);
  color: #d8272a;
  border-color: rgba(216,39,41,0.12);
  box-shadow: 0 8px 22px rgba(216,40,40,0.06);
}

/* badge discount z-index thấp hơn nút */
.badge.position-absolute { z-index: 5; }

/* Responsive */
@media (max-width:576px) {
  .wishlist-btn { bottom: 10px; right: 10px; width:40px; height:40px; font-size:16px; }
  .favorites-pill { padding:6px 10px; gap:8px; }
  .favorites-pill .pill-line-1 { font-size:11px; }
  .favorites-pill .pill-line-2 { font-size:13px; gap:6px; }
  .favorites-pill .pill-icon { font-size:14px; }
  .promo-label { padding: 3px 6px; font-size:11px; }
  .saving-line { font-size:12px; }
}

/* PRICE UI */
.price-block { margin-bottom: 8px; }
.price-block .final-price { font-size: 1.15rem; font-weight: 700; color: #e60023; letter-spacing: 0.3px; }
.price-block .original-price { font-size: 0.9rem; color: #888; opacity: 0.9; margin-left: 2px; }
.product-card:hover .price-block .final-price { color: #c2001d; transition: 0.2s; }
.card .badge.bg-danger { background: #ff3b30 !important; padding: 6px 10px; font-size: 13px; font-weight: 600; border-radius: 8px; }
.saving-line { color: #0a8a43; font-size: 13px; font-weight: 600; margin-top: 3px; }

/* STARS */
.star-inline { font-size: 14px; margin-right:6px; }
</style>

<!-- JS: xử lý wishlist + pill favorites (đã chú thích bằng tiếng Việt) -->
<script>
(function(){
  // Endpoint lấy danh sách wishlist và toggle — giữ nguyên đường dẫn server (controller xử lý)
  var listEndpoint = '<?php echo BASE_APP_URL; ?>/wishlist/list';
  var toggleEndpoint = '<?php echo BASE_APP_URL; ?>/wishlist/toggle';
  var guestKey = 'guest_wishlist';

  // Thiết lập trạng thái hiển thị cho 1 nút
  function setBtnState(btn, added) {
    if (!btn) return;
    btn.textContent = added ? '❤' : '♡';
    if (added) btn.classList.add('fav-active'); else btn.classList.remove('fav-active');
    btn.setAttribute('aria-pressed', added ? 'true' : 'false');
    btn.setAttribute('title', added ? 'Đã yêu thích — bấm để bỏ' : 'Thêm vào yêu thích');
  }

  // Đánh dấu tất cả các nút dựa trên mảng ids
  function markButtons(ids) {
    if (!Array.isArray(ids)) return;
    document.querySelectorAll('.wishlist-btn').forEach(function(btn){
      var pid = btn.dataset.product;
      setBtnState(btn, ids.indexOf(String(pid)) !== -1);
    });
  }

  // Tải wishlist từ server, nếu lỗi fallback sang localStorage
  function loadWishlistThenMark(callback) {
    fetch(listEndpoint, { method: 'GET', headers: {'X-Requested-With':'XMLHttpRequest'} })
      .then(function(resp){ return resp.json(); })
      .then(function(json){
        if (json && json.success && Array.isArray(json.data)) {
          markButtons(json.data);
          try { localStorage.setItem(guestKey, JSON.stringify(json.data)); } catch(e){}
          if (typeof callback === 'function') callback(json.data);
          return;
        }
        var guest = JSON.parse(localStorage.getItem(guestKey) || '[]');
        markButtons(guest);
        if (typeof callback === 'function') callback(guest);
      }).catch(function(){
        var guest = JSON.parse(localStorage.getItem(guestKey) || '[]');
        markButtons(guest);
        if (typeof callback === 'function') callback(guest);
      });
  }

  // Đồng bộ localStorage với server sau khi thay đổi
  function syncLocalWithServer(pid, added) {
    var arr = JSON.parse(localStorage.getItem(guestKey) || '[]');
    var idx = arr.indexOf(String(pid));
    if (added && idx === -1) arr.push(String(pid));
    else if (!added && idx !== -1) arr.splice(idx,1);
    try { localStorage.setItem(guestKey, JSON.stringify(arr)); } catch(e){}
  }

  // Xử lý click nút wishlist (bên trong card)
  document.addEventListener('click', function(e){
    var btn = e.target.closest('.wishlist-btn');
    if (!btn) return;
    e.preventDefault();
    var pid = btn.dataset.product;
    if (!pid) return;
    // optimistic UI: đổi trước, gọi server sau
    var currently = btn.classList.contains('fav-active');
    setBtnState(btn, !currently);

    fetch(toggleEndpoint, {
      method: 'POST',
      headers: {'Content-Type':'application/json','X-Requested-With':'XMLHttpRequest'},
      body: JSON.stringify({ product_id: pid })
    }).then(function(resp){ return resp.json(); })
      .then(function(json){
        if (json && json.success) {
          var added = (json.action === 'added');
          setBtnState(btn, added);
          syncLocalWithServer(pid, added);
        } else {
          // Nếu server trả lỗi -> fallback lưu local
          guestToggle(pid, btn);
        }
      }).catch(function(){
        // Mạng lỗi -> lưu local
        guestToggle(pid, btn);
      });
  });

  // Fallback lưu/vô hiệu wishlist trên localStorage khi server không khả dụng
  function guestToggle(pid, btn) {
    var arr = JSON.parse(localStorage.getItem(guestKey) || '[]');
    var idx = arr.indexOf(String(pid));
    if (idx === -1) { arr.push(String(pid)); setBtnState(btn, true); }
    else { arr.splice(idx,1); setBtnState(btn, false); }
    try { localStorage.setItem(guestKey, JSON.stringify(arr)); } catch(e){}
  }

  // Lọc chỉ hiển thị sản phẩm yêu thích (client-side)
  function applyFavoritesFilter(favIds) {
    if (!Array.isArray(favIds) || favIds.length === 0) {
      document.querySelectorAll('.product-card').forEach(function(card){ card.style.display = ''; });
      return;
    }
    document.querySelectorAll('.product-card').forEach(function(card){
      var pid = card.dataset.product;
      if (favIds.indexOf(String(pid)) !== -1) card.style.display = '';
      else card.style.display = 'none';
    });
  }

  var favPill = document.getElementById('favoritesToggle');
  function setPillActive(on) {
    if (!favPill) return;
    if (on) { favPill.classList.add('active'); favPill.setAttribute('aria-pressed','true'); }
    else { favPill.classList.remove('active'); favPill.setAttribute('aria-pressed','false'); }
  }

  if (favPill) {
    favPill.addEventListener('click', function(){
      var isActive = favPill.classList.contains('active');
      // Thay đổi trạng thái ngay UI
      setPillActive(!isActive);

      // Tạo query mới: giữ các param hiện tại, chỉ set favorites và page=1
      var params = new URLSearchParams(window.location.search);
      if (!isActive) params.set('favorites','1'); else params.delete('favorites');
      params.set('page','1');

      // CHUYỂN TRANG để server-side nhận param (đảm bảo phân trang giữ bộ lọc)
      window.location.href = window.location.pathname + (params.toString() ? '?' + params.toString() : '');
    });
  }

  // Khi load trang, đánh dấu các nút wishlist theo server/local; nếu URL có favorites=1, apply filter trên client
  document.addEventListener('DOMContentLoaded', function(){
    loadWishlistThenMark(function(ids){
      var params = new URLSearchParams(window.location.search);
      var wantsFav = params.get('favorites') === '1';
      setPillActive(wantsFav);
      if (wantsFav) applyFavoritesFilter(ids);
      else applyFavoritesFilter(null);
    });
  });

})();
</script>

<style>

  /* Hàng filter gọn, đẹp */
.products-filter-bar {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    align-items: center;
    background: #fff;
    padding: 12px 15px;
    border-radius: 10px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.06);
    margin-bottom: 18px;
}

.products-filter-bar select,
.products-filter-bar input,
.products-filter-bar button {
    height: 33px;
}

/* Nút yêu thích dạng pill */
.favorites-pill {
    border: 1px solid #ff4d4d;
    color: #ff4d4d;
    background: #fff;
    border-radius: 20px;
    padding: 4px 14px;
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 14px;
    cursor: pointer;
    transition: 0.25s;
}

.favorites-pill.active {
    background: #ff4d4d;
    color: #fff;
}

.favorites-pill .pill-icon {
    font-size: 15px;
}

/* Nhỏ gọn chữ */
.pill-line-1 {
    font-size: 11px;
    line-height: 10px;
}
.pill-line-2 {
    font-size: 12px;
    font-weight: 600;
    margin-top: -3px;
}

.product-card .card {
    padding-bottom: 50px !important; /* tạo đủ khoảng để tim không đè nút giỏ hàng */
    overflow: visible;
}

/* Nút tim cố định ở góc phải phía trên ảnh */
.wishlist-btn {
    position: absolute;
    bottom: 10px;
    right: 10px;
    z-index: 10;
    background: white;
    border: 1px solid #ddd;
    padding: 4px 8px;
    border-radius: 50%;
    font-size: 18px;
    cursor: pointer;
}

/* Khi yêu thích */
.wishlist-btn.fav-active {
    color: red;
}

/* Căn giá tiền */
.price-block {
    display: flex;
    flex-direction: column;
    align-items: center; /* căn giữa giá + tiết kiệm */
    text-align: center;
}

.price-inline-inner {
    display: flex;
    flex-direction: row;
    align-items: center;
    gap: 6px;
}

/* Giá chính */
.final-price {
    color: #d0021b;
    font-size: 18px;
    font-weight: bold;
}

/* Giá gạch */
.original-price del {
    color: #777;
    font-size: 14px;
}

/* Dòng tiết kiệm */
.saving-line {
    color: green;
    font-size: 14px;
    margin-top: 2px;
}

</style>