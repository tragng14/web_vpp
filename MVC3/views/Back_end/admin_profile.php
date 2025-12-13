<?php if (isset($data["error"])): ?> 
    <div class="alert alert-danger"><?= $data["error"] ?></div>
<?php endif; ?>

<?php if (isset($data["success"])): ?>
    <div class="alert alert-success"><?= $data["success"] ?></div>
<?php endif; ?>

<div class="card shadow-sm p-4 mb-4">
    <h4 class="mb-3 text-primary">Thông tin cá nhân</h4>

    <form action="<?= htmlspecialchars($app) ?>/Admin/updateProfile"
          method="POST"
          enctype="multipart/form-data"
          autocomplete="off">

        <!-- Avatar -->
        <div class="mb-3 text-center">
            <img src="<?= $app ?>/public/images/avatars/<?= htmlspecialchars($_SESSION['user']['avatar'] ?? 'default.png') ?>"
                 id="avatarPreview"
                 style="width: 120px; height: 120px; object-fit: cover; border-radius: 50%; border: 2px solid #ddd;">
        </div>

        <div class="mb-3">
            <label class="form-label">Chọn ảnh đại diện mới</label>
            <input type="file"
                   name="avatar"
                   accept="image/*"
                   class="form-control"
                   onchange="previewAvatar(event)">
        </div>

        <hr>

        <div class="mb-3">
            <label class="form-label">Họ và tên</label>
            <input type="text" name="fullname"
                   value="<?= htmlspecialchars($_SESSION['user']['fullname']) ?>"
                   class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email"
                   value="<?= htmlspecialchars($_SESSION['user']['email']) ?>"
                   class="form-control" disabled>
        </div>
<div class="mb-3">
    <label class="form-label">Số điện thoại</label>
    <input type="text" name="phone"
           value="<?= htmlspecialchars($_POST['phone'] ?? $_SESSION['user']['phone'] ?? '') ?>"
           class="form-control">
</div>

<div class="mb-3">
    <label class="form-label">Địa chỉ</label>
    <input type="text" name="address"
           value="<?= htmlspecialchars($_POST['address'] ?? $_SESSION['user']['address'] ?? '') ?>"
           class="form-control">
</div>


        <button type="submit" class="btn btn-primary">Cập nhật</button>
    </form>
</div>

<!-- FORM ĐỔI MẬT KHẨU (độc lập hoàn toàn) -->
<div class="card shadow-sm p-4">
    <h4 class="mb-3 text-warning">Đổi mật khẩu</h4>

    <form action="<?= htmlspecialchars($app) ?>/Admin/changePassword" method="POST" autocomplete="off">

        <!-- Mật khẩu hiện tại -->
        <div class="mb-3">
            <label>Mật khẩu hiện tại</label>
            <div class="input-group">
                <input type="password" name="old_password" id="old_password" class="form-control" required>
                <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('old_password')">
                    👁
                </button>
            </div>
        </div>

        <!-- Mật khẩu mới -->
        <div class="mb-3">
            <label>Mật khẩu mới</label>
            <div class="input-group">
                <input type="password" name="new_password" id="new_password" class="form-control" required>
                <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('new_password')">
                    👁
                </button>
            </div>
        </div>

        <!-- Nhập lại mật khẩu mới -->
        <div class="mb-3">
            <label>Nhập lại mật khẩu mới</label>
            <div class="input-group">
                <input type="password" name="confirm_password" id="confirm_password" class="form-control" required>
                <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('confirm_password')">
                    👁
                </button>
            </div>
        </div>

        <button type="submit" class="btn btn-warning">Đổi mật khẩu</button>
    </form>
</div>

<script>
function togglePassword(id) {
    const input = document.getElementById(id);
    input.type = input.type === "password" ? "text" : "password";
}
</script>
<script>
function previewAvatar(event) {
    const reader = new FileReader();
    reader.onload = function() {
        document.getElementById("avatarPreview").src = reader.result;
    };
    reader.readAsDataURL(event.target.files[0]);
}
</script>
