<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng ký thành viên</title>
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/public/css/bootstrap.min.css">
    <style>
        body {
            background: #f8f9fa;
        }
        .register-container {
            max-width: 450px;
            margin: 60px auto;
            padding: 30px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            margin-bottom: 25px;
            color: #333;
        }
        .btn-primary {
            width: 100%;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="register-container">
        <h2>Đăng ký thành viên</h2>

        <form action="<?php echo APP_URL; ?>/AuthController/register" method="POST">
            <div class="mb-3">
                <label for="fullname" class="form-label">Họ và tên</label>
                <input type="text" class="form-control" id="fullname" name="fullname" required>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Địa chỉ Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Mật khẩu</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>

            <!-- Ẩn role, mặc định là user -->
            <input type="hidden" name="role" value="user">

            <button type="submit" class="btn btn-primary">Đăng ký</button>

            <div class="text-center mt-3">
                <small>Đã có tài khoản? <a href="<?php echo APP_URL; ?>/AuthController/showLogin">Đăng nhập ngay</a></small>
            </div>
        </form>
    </div>
</div>
</body>
</html>
