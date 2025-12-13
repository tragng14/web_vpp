<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng nhập</title>
    <link rel="stylesheet" href="/public/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2>Đăng nhập tài khoản</h2>
    <form action="<?php echo APP_URL; ?>/AuthController2/login" method="POST">
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Mật khẩu</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <button type="submit" class="btn btn-success">Đăng nhập</button>
    <a href="<?php echo APP_URL; ?>/AuthController2/Show" class="btn btn-primary ms-2">Đăng ký thành viên</a>
    <a href="<?php echo APP_URL; ?>/AuthController2/forgotPassword" class="btn btn-link ms-2">Quên mật khẩu?</a>
    </form>
</div>

</body>
</html>
