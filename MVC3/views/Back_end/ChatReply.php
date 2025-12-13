<div class="container py-4">
    <h4 class="text-primary mb-3">✉️ Phản hồi đến: <?= htmlspecialchars($data['email']) ?></h4>

    <form method="POST" action="<?= APP_URL ?>/Chat/doReply">
        <input type="hidden" name="email" value="<?= htmlspecialchars($data['email']) ?>">

        <div class="mb-3">
            <label class="form-label">Nội dung phản hồi</label>
            <textarea name="message" class="form-control" rows="4" required></textarea>
        </div>

        <button class="btn btn-success">Gửi phản hồi</button>

        <a href="<?= APP_URL ?>/Chat/viewUserMessages/<?= urlencode($data['email']) ?>"
           class="btn btn-secondary">Quay lại</a>
    </form>
</div>
