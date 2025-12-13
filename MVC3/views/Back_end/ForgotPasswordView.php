<div class="container mt-5">
    <h2>Quên mật khẩu</h2>
    <form action="<?php echo APP_URL; ?>/AuthController2/resetPassword" method="POST">
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <button type="submit" class="btn btn-primary">Gửi lại mật khẩu</button>
    </form>
    <div class="mt-3">
        <a href="<?php echo APP_URL; ?>/AuthController2/ShowLogin" class="btn btn-link">Đăng nhập</a>
    </div>
</div>
