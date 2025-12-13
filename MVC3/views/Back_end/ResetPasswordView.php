<div class="container mt-4">
    <div class="card shadow">
        <div class="card-header bg-danger text-white">
            <h5 class="mb-0">Đặt lại mật khẩu cho người dùng</h5>
        </div>

        <div class="card-body">
            <p><strong>Email:</strong> <?= htmlspecialchars($data['user']['email']) ?></p>
            <p><strong>Họ tên:</strong> <?= htmlspecialchars($data['user']['fullname']) ?></p>

            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Mật khẩu mới</label>
                    <input type="password" name="new_password" class="form-control" placeholder="Nhập mật khẩu mới">
                </div>

                <button type="submit" class="btn btn-danger">Xác nhận đặt lại</button>
                <a href="<?= APP_URL ?>/Admin/show" class="btn btn-secondary">Hủy</a>
            </form>
        </div>
    </div>
</div>
