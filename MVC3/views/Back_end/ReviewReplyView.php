<div class="container py-4">
    <h3 class="text-primary mb-4">Phản hồi đánh giá sản phẩm</h3>

    <div class="card shadow-sm">
        <div class="card-body">
            <h5><strong>Mã sản phẩm:</strong> <?= htmlspecialchars($data['review']['masp']) ?></h5>
            <p><strong>Người dùng:</strong> <?= htmlspecialchars($data['review']['tenNguoiDung']) ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($data['review']['email']) ?></p>
            <p><strong>Nội dung:</strong><br><?= nl2br(htmlspecialchars($data['review']['noidung'])) ?></p>
            <hr>
            <form method="POST" action="<?= APP_URL ?>/Review/saveReply">
                <input type="hidden" name="id" value="<?= $data['review']['id'] ?>">
                <div class="mb-3">
                    <label class="form-label">Phản hồi của Admin</label>
                    <textarea name="reply" class="form-control" rows="4" required><?= htmlspecialchars($data['review']['traloi'] ?? '') ?></textarea>
                </div>
                <button type="submit" class="btn btn-success">💬 Lưu phản hồi</button>
                <a href="<?= APP_URL ?>/Review/show" class="btn btn-secondary">⬅ Quay lại</a>
            </form>
        </div>
    </div>
</div>
