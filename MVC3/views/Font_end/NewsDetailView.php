<div class="container mt-4">
    <?php if (isset($data["news"])): ?>
        <div class="news-detail">
            <h2 class="mb-3 text-primary"><?php echo htmlspecialchars($data["news"]["title"]); ?></h2>
            <p class="text-muted mb-3">
                Ngày đăng: <?php echo $data["news"]["created_at"]; ?>
            </p>
            <img src="<?php echo APP_URL . '/public/images/' . $data["news"]["image"]; ?>" 
                 class="img-fluid rounded mb-4" 
                 alt="<?php echo htmlspecialchars($data["news"]["title"]); ?>">
            <div class="news-content">
                <?php echo nl2br($data["news"]["content"]); ?>
            </div>
            <a href="<?php echo APP_URL; ?>/NewsFrontController/Index" 
               class="btn btn-outline-secondary mt-4 rounded-pill">
                ← Quay lại danh sách tin tức
            </a>
        </div>
    <?php else: ?>
        <div class="alert alert-danger">Không tìm thấy nội dung bài viết.</div>
    <?php endif; ?>
</div>

<style>
.news-detail {
    background: #fff;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}
.news-detail img {
    max-height: 400px;
    object-fit: cover;
    display: block;
    margin: 0 auto;
}
.news-content {
    font-size: 1.05rem;
    line-height: 1.7;
    color: #333;
    text-align: justify;
}
</style>    