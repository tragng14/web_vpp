<div class="container mt-5">
    <h2>Quên mật khẩu</h2>

    <form action="<?php echo APP_URL; ?>/AuthController/resetPassword" method="POST">
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>

        <!-- Hai nút nằm cạnh nhau -->
        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">
                Gửi lại mật khẩu
            </button>

            <a href="<?php echo APP_URL; ?>/AuthController/ShowLogin" class="btn btn-secondary">
                Đăng nhập
            </a>
        </div>
    </form>
</div>
