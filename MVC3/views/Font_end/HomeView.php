  
<?php
$products = $data["productList"];
?>
<<div class="container py-4">
    <h2 class="mb-4">Danh sách sản phẩm</h2>

    <!-- HÀNG CUỘN NGANG -->
    <div class="scroll-x">
 <?php foreach ($products as $p): ?>
            <?php
                // Giá gốc
                $price = floatval($p['giaXuat'] ?? 0);
                $p['price'] = $price;

                // Loại khuyến mãi
                $type  = strtolower($p['promo_type'] ?? '');
$value = floatval($p['promo_value'] ?? 0);


                // Giá cuối
                $final = $price;

                if ($type === 'percent' && $value > 0) {
                    $final = $price - ($price * ($value / 100));
                }
                elseif ($type === 'amount' && $value > 0) {
                    $final = $price - $value;
                }
                elseif ($type === 'fixed' && $value > 0) {
                    $final = $value; // giá cố định
                }

                if ($final < 0) $final = 0;

                $p['final'] = $final;
            ?>  
        <div class="product-item">
            <div class="card h-100 border rounded shadow-sm product-card">

                <a href="<?php echo APP_URL; ?>/Home/detail/<?= $p['masp'] ?>">
                    <img src="<?php echo APP_URL; ?>/public/images/<?= htmlspecialchars($p['hinhanh']) ?>"
                         class="card-img-top"
                         style="width: 100%; height: 9rem; object-fit: contain;">
                </a>

                <div class="card-body text-center">
                    <h5 class="card-title"><?= htmlspecialchars($p['tensp']) ?></h5>

                    <!-- Giá -->
                    <?php if ($final < $price): ?>
                        <p class="mb-0">
                            <span class="price-final"><?= number_format($final) ?> ₫</span>
                            <?php if ($type === 'percent'): ?>
                                <span class="discount-badge">-<?= intval($value) ?>%</span>
                            <?php endif; ?>
                        </p>
                        <p class="price-old"><?= number_format($price) ?> ₫</p>
                    <?php else: ?>
                        <p class="price-final"><?= number_format($price) ?> ₫</p>
                    <?php endif; ?>

                    <a href="<?php echo APP_URL; ?>/Home/addtocard/<?= $p['masp'] ?>" 
                       class="btn-add-cart w-100">
                       <i class="bi bi-cart-plus"></i> Mua ngay
                    </a>
                    
                </div>

            </div>
        </div>

        <?php endforeach; ?>


    </div>
    <div class="text-center mt-4">
    <a href="<?= APP_URL ?>/ProductFront" class="btn btn-primary px-4 py-2">
        Xem thêm sản phẩm
    </a>
</div>
</div>
<?php if (!empty($data["NewsList"]) && ($data["page"] ?? '') === "HomeView"): ?>

<div class="news-container container py-5">
    <h2 class="news-title mb-5 text-center">Tin tức mới nhất</h2>

    <div class="row">
        <?php foreach ($data["NewsList"] as $news): ?>
            <div class="col-md-12 mb-4">
                <div class="news-item d-flex flex-md-row flex-column align-items-start shadow-sm p-3 rounded bg-white">

                    <!-- Ảnh -->
                    <div class="news-thumb me-md-3 mb-3 mb-md-0">
                        <img src="<?= h($appUrl) ?>/public/images/<?= h($news['image']) ?>"
                             alt="<?= h($news['title']) ?>">
                    </div>

                    <!-- Nội dung -->
                    <div class="news-content flex-fill">
                        <h5 class="news-title-item"><?= h($news['title']); ?></h5>

                        <p class="news-desc text-muted">
                            <?= mb_substr(strip_tags($news['content']), 0, 150) . '...'; ?>
                        </p>

                        <a href="<?= h($appUrl) ?>/NewsFrontController/Detail/<?= h($news['id']) ?>"
                           class="btn btn-primary btn-sm rounded-pill px-4">
                           Đọc tiếp
                        </a>
                    </div>

                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php endif; ?>

<style>

/* Vùng giá sản phẩm */
.price-final {
    font-size: 1.2rem;
    font-weight: 700;
    color: #e60000; /* đỏ nổi bật */
    margin-bottom: 0;
}

.price-old {
    font-size: 0.9rem;
    color: #888;
    text-decoration: line-through;
    margin-top: -2px;
}

/* Badge giảm giá */
.discount-badge {
    background: #ff3b30;
    color: white;
    padding: 3px 8px;
    font-size: 0.75rem;
    border-radius: 6px;
    font-weight: bold;
    margin-left: 6px;
}

/* Card sản phẩm */
.product-card {
    transition: 0.25s ease;
}
.product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 14px rgba(0,0,0,0.15);
}

/* Tên sản phẩm giới hạn 2 dòng */
.card-title {
    font-size: 1rem;
    height: 46px;
    overflow: hidden;
}
/* ===========================
   CARD SẢN PHẨM HIỆN ĐẠI
   ===========================*/
.product-card {
    border: none !important;
    border-radius: 14px;
    overflow: hidden;
    background: #ffffff;
    transition: all 0.3s ease;
}

.product-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.12);
}

/* ẢNH SẢN PHẨM */
.product-card img {
    padding: 10px;
    background: #f8f9fa;
    border-bottom: 1px solid #eee;
}

/* Tên sản phẩm */
.card-title {
    font-size: 1rem;
    font-weight: 600;
    height: 44px;
    overflow: hidden;
    color: #333;
}

/* ===========================
       GIÁ – GIẢM GIÁ
   ===========================*/
.price-final {
    font-size: 1.25rem;
    font-weight: 700;
    color: #e60000;
}

.price-old {
    font-size: 0.9rem;
    color: #888;
    text-decoration: line-through;
    margin-top: -4px;
}

/* Badge giảm giá */
.discount-badge {
    background: #ff3b30;
    color: #fff;
    padding: 3px 8px;
    font-size: 0.75rem;
    border-radius: 6px;
    margin-left: 6px;
    font-weight: bold;
}

/* ===========================
   NÚT THÊM GIỎ HÀNG ĐẸP
   ===========================*/
.btn-add-cart {
    display: inline-block;
    padding: 10px;
    margin-top: 8px;
    background: linear-gradient(135deg, #fafafbff, #0056d2);
    color: #fff !important;
    font-weight: 600;
    border-radius: 10px;
    transition: 0.3s ease;
    border: none;
}

.btn-add-cart i {
    margin-right: 6px;
    font-size: 1.1rem;
}

.btn-add-cart:hover {
    background: linear-gradient(135deg, #d5e5faff, #003f9e);
    transform: translateY(-2px);
    box-shadow: 0 4px 14px rgba(0, 91, 187, 0.35);
}

/* ===========================
     BỐ CỤC HÀNG SẢN PHẨM
   ===========================*/
.row {
    row-gap: 20px;
}
/* ===========================
   CUỘN NGANG 1 HÀNG SẢN PHẨM
   ===========================*/
.scroll-x {
    display: flex;
    overflow-x: auto;
    gap: 20px;
    padding-bottom: 10px;
    scroll-behavior: smooth;
    white-space: nowrap;
}

/* Thanh cuộn đẹp */
.scroll-x::-webkit-scrollbar {
    height: 8px;
}

.scroll-x::-webkit-scrollbar-track {
    background: #eee;
    border-radius: 10px;
}

.scroll-x::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 10px;
}

.scroll-x::-webkit-scrollbar-thumb:hover {
    background: #999;
}

/* Mỗi sản phẩm chiếm 25% chiều rộng → 4 sản phẩm / màn hình */
.product-item {
    flex: 0 0 25%;
    max-width: 25%;
    display: inline-block;
}

/* Màn hình vừa */
@media (max-width: 768px) {
    .product-item {
        flex: 0 0 50%;
        max-width: 50%;
    }
}

/* Màn hình nhỏ */
@media (max-width: 500px) {
    .product-item {
        flex: 0 0 80%;
        max-width: 80%;
    }
}

</style>
