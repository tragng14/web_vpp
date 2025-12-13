<div class="news-container container py-5">
    <h2 class="news-title mb-5 text-center">Tin tức mới nhất</h2>
    <div class="row">
        <?php foreach ($data["NewsList"] as $news): ?>
            <div class="col-md-12 mb-4">
    <div class="news-item d-flex flex-md-row flex-column align-items-start shadow-sm p-3 rounded bg-white">
        
        <!-- Ảnh -->
        <div class="news-thumb me-md-3 mb-3 mb-md-0">
            <img src="<?php echo APP_URL . '/public/images/' . htmlspecialchars($news['image']); ?>" 
                alt="<?php echo htmlspecialchars($news['title']); ?>">
        </div>

        <!-- Nội dung -->
        <div class="news-content flex-fill">
            <h5 class="news-title-item"><?php echo htmlspecialchars($news['title']); ?></h5>

            <p class="news-desc text-muted">
                <?php echo mb_substr(strip_tags($news['content']), 0, 150) . '...'; ?>
            </p>

            <a href="<?php echo APP_URL; ?>/NewsFrontController/Detail/<?php echo $news['id']; ?>" 
               class="btn btn-primary btn-sm rounded-pill px-4">
               Đọc tiếp
            </a>
        </div>
    </div>
</div>

        <?php endforeach; ?>
    </div>
</div>

<!-- CSS -->
<style>
/* ===== BỐ CỤC CHUNG ===== */
.news-item {
    transition: all 0.3s ease;
    border: 1px solid #eee;
}

.news-item:hover {
    box-shadow: 0 6px 18px rgba(0,0,0,0.08);
    transform: translateY(-3px);
}

/* ===== ẢNH ===== */
.news-thumb {
    width: 260px;
    height: 170px;
    flex-shrink: 0;
    overflow: hidden;
    border-radius: 8px;
}

.news-thumb img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform .35s ease;
}

.news-item:hover img {
    transform: scale(1.05);
}

/* ===== NỘI DUNG ===== */
/* ===== NÚT ĐỌC TIẾP ===== */
.news-content a {
    background-color: #bcd9ff;        /* xanh nhạt */
    border: 1px solid #a7c8f5;        /* viền nhẹ */
    color: #0b3d91;                   /* chữ xanh đậm */
    font-size: 0.8rem;                /* thu nhỏ chữ */
    padding: 6px 14px;                /* giảm kích thước nút */
    font-weight: 500;
    transition: all 0.25s ease;
}

.news-content a:hover {
    background-color: #94c0ff;        /* đậm nhẹ khi hover */
    border-color: #7baef0;
    color: #fff;
    transform: translateY(-1px);
}

.news-title-item {
    font-size: 1.25rem;
    font-weight: 600;
    color: #212529;
    line-height: 1.4;
    margin-bottom: 8px;
    transition: color .3s;
}

.news-item:hover .news-title-item {
    color: #0f1b2cff;
}

.news-desc {
    font-size: 0.95rem;
    line-height: 1.5;
    margin-bottom: 15px;
}

/* ===== MOBILE ===== */
@media (max-width: 768px) {
    .news-thumb {
        width: 100%;
        height: 220px;
        margin-bottom: 10px;
    }
}

</style>