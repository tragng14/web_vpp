<?php
// views/Font_end/UserProfileView.php
// Phiên bản sửa: - KHÔNG chứa navbar (layout chính homePage.php đã có).
//                  - Tránh redeclare h() bằng guard if (!function_exists('h')).
//                  - Modal update profile, preview client-side, validation phone.
// Yêu cầu: file này được include bởi layout (homePage.php) - APP_URL định nghĩa ở config.

if (session_status() === PHP_SESSION_NONE) {
    @session_start();
}

$data = $data ?? [];
$user = $data['user'] ?? null;

if (!function_exists('h')) {
    function h($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
}

$app = defined('APP_URL') ? rtrim(APP_URL, '/') : '';

/* Chuẩn hóa avatar URL */
$avatarFile = trim((string)($user['avatar'] ?? ''));
if ($avatarFile === '') {
    $avatarUrl = $app . '/public/images/user-default.png';
} elseif (preg_match('/^https?:\/\//i', $avatarFile)) {
    $avatarUrl = $avatarFile;
} else {
    $avatarUrl = $app . '/public/images/avatars/' . rawurlencode($avatarFile);
}

/* Tên hiển thị */
$displayName = trim((string)($user['fullname'] ?? ''));
if ($displayName === '') {
    $emailTmp = $user['email'] ?? '';
    if ($emailTmp !== '') $displayName = explode('@', $emailTmp)[0];
    else $displayName = $user['username'] ?? 'Người dùng';
}
?>
<!-- BEGIN: Profile content (no header/nav) -->
<div class="container mt-4">
    <h2 class="mb-4">Thông tin tài khoản</h2>

    <?php if (!$user): ?>
        <div class="alert alert-warning">Không tìm thấy thông tin người dùng.</div>
    <?php else: ?>

    <div class="row g-4">
        <!-- LEFT: Avatar + quick actions -->
        <div class="col-md-4">
            <div class="card p-4 shadow-sm text-center">
                <img id="currentAvatar"
                     src="<?= h($avatarUrl) ?>"
                     class="img-fluid rounded mb-3"
                     style="width:160px;height:160px;object-fit:cover;border-radius:8px;border:1px solid #e6e6e6;cursor:pointer;"
                     data-bs-toggle="modal" data-bs-target="#avatarModal"
                     onerror="this.onerror=null;this.src='<?= h($app) ?>/public/images/user-default.png'">

                <div class="d-grid gap-2 mb-2">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#avatarModal">Sửa thông tin</button>
                    <a href="<?= h($app) ?>/Home/orderHistory" class="btn btn-outline-secondary">Lịch sử đơn hàng</a>
                    <a href="<?= h($app) ?>/ProductFront/?page=1&favorites=1" class="btn btn-outline-secondary">Sản phẩm yêu thích</a>
                </div>

                <p class="mt-3 mb-0"><strong><?= h($displayName) ?></strong></p>
                <p class="text-muted mb-0"><?= h($user['email'] ?? '') ?></p>
            </div>
        </div>

        <!-- RIGHT: Details -->
        <div class="col-md-8">
            <div class="card p-4 shadow-sm mb-4">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Họ tên:</strong> <?= h($displayName) ?></p>
                        <p><strong>Email:</strong> <?= h($user['email'] ?? '') ?></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Số điện thoại:</strong> <?= h($user['phone'] ?? 'Chưa cập nhật') ?></p>
                        <p><strong>Địa chỉ:</strong> <?= h($user['address'] ?? 'Chưa cập nhật') ?></p>
                    </div>
                </div>

            </div>

            <!-- Change password -->
            <div class="card p-4 shadow-sm mb-4">
    <h5 class="mb-3">Đổi mật khẩu</h5>

    <form action="<?= h($app) ?>/User/updatePassword" method="POST" class="needs-validation" novalidate>
        <input type="hidden" name="user_id" value="<?= h($user['user_id'] ?? '') ?>">

        <!-- Mật khẩu cũ -->
        <div class="mb-3">
            <label class="form-label">Mật khẩu cũ</label>
            <div class="input-group">
                <input type="password" class="form-control" id="old_password" name="old_password" required>
                <button type="button" class="btn btn-outline-secondary" onclick="togglePW('old_password')">👁</button>
            </div>
            <div class="invalid-feedback">Vui lòng nhập mật khẩu cũ.</div>
        </div>

        <!-- Mật khẩu mới -->
        <div class="mb-3">
            <label class="form-label">Mật khẩu mới</label>
            <div class="input-group">
                <input type="password" class="form-control" id="new_password" name="new_password" minlength="6" required>
                <button type="button" class="btn btn-outline-secondary" onclick="togglePW('new_password')">👁</button>
            </div>
            <p id="password-error" style="color:red; font-size:14px; margin-top:4px; display:none;">
    Mật khẩu mới tối thiểu 6 ký tự.
</p>

        </div>

        <!-- Nhập lại mật khẩu mới -->
        <div class="mb-3">
            <label class="form-label">Nhập lại mật khẩu mới</label>
            <div class="input-group">
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" minlength="6" required>
                <button type="button" class="btn btn-outline-secondary" onclick="togglePW('confirm_password')">👁</button>
            </div>
            <div class="invalid-feedback">Vui lòng nhập lại mật khẩu trùng khớp.</div>
        </div>

        <button class="btn btn-primary">Cập nhật mật khẩu</button>
    </form>
</div>


            <!-- Delete account -->
            <div class="card p-4 shadow-sm">
                <h5 class="mb-3">Xóa tài khoản</h5>
                <form action="<?= h($app) ?>/User/deleteAccount" method="POST" onsubmit="return confirm('Bạn có chắc muốn xóa tài khoản không?')">
                    <input type="hidden" name="user_id" value="<?= h($user['user_id'] ?? '') ?>">
                    <button class="btn btn-danger">Xóa tài khoản</button>
                </form>
            </div>
        </div>
    </div>

    <?php endif; ?>
</div>

<!-- Modal update profile -->
<div class="modal fade" id="avatarModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="avatarForm" action="<?= h($app) ?>/User/updateProfile" method="POST" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title">Cập nhật thông tin</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="user_id" value="<?= h($user['user_id'] ?? '') ?>">

                    <div class="text-center mb-3">
                        <img id="modalCurrent" src="<?= h($avatarUrl) ?>" class="rounded mb-2" style="width:140px;height:140px;object-fit:cover;border:1px solid #e6e6e6;">
                        <div class="form-text">Click ảnh để chọn file (tối đa 2MB). Nếu không chọn ảnh, avatar sẽ giữ nguyên.</div>
                    </div>

                    <div class="mb-3">
                        <input type="file" name="avatar" id="modalAvatarInput" class="form-control" accept="image/*">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Họ và tên</label>
                        <input type="text" name="fullname" class="form-control" value="<?= h($user['fullname'] ?? '') ?>" placeholder="Họ và tên">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Số điện thoại</label>
                        <input type="text" name="phone" id="phoneInput" class="form-control"
                               pattern="0[0-9]{9}"
                               title="Bắt đầu bằng 0 và gồm đúng 10 chữ số (ví dụ: 0912345678)"
                               value="<?= h($user['phone'] ?? '') ?>" placeholder="0912345678">
                        <div class="form-text">Bắt đầu bằng 0 và gồm 10 chữ số.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Địa chỉ</label>
                        <input type="text" name="address" class="form-control" value="<?= h($user['address'] ?? '') ?>" placeholder="Địa chỉ">
                    </div>

                    <div id="previewWrapper" style="display:none;text-align:center;">
                        <div style="display:inline-block;border:1px solid #e6e6e6;border-radius:6px;overflow:hidden;width:140px;height:140px;">
                            <img id="modalPreview" alt="Preview" style="width:100%;height:100%;object-fit:cover;">
                        </div>
                        <div class="form-text mt-2">(Kích thước tối đa 2MB)</div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Client JS (không include bootstrap.js ở đây) -->
<script>
(function(){
    // Modal preview logic
    var modalCurrent = document.getElementById('modalCurrent');
    var avatarInput = document.getElementById('modalAvatarInput');
    var previewWrapper = document.getElementById('previewWrapper');
    var modalPreview = document.getElementById('modalPreview');

    if (modalCurrent) {
        modalCurrent.addEventListener('click', function(){ if (avatarInput) avatarInput.click(); });
    }

    if (avatarInput) {
        avatarInput.addEventListener('change', function(e){
            var file = e.target.files[0];
            if (!file) { previewWrapper.style.display='none'; modalPreview.src=''; return; }
            var maxBytes = 2 * 1024 * 1024;
            if (file.size > maxBytes) {
                alert('Kích thước ảnh quá lớn (tối đa 2MB).');
                e.target.value = '';
                previewWrapper.style.display='none';
                modalPreview.src='';
                return;
            }
            var allowed = ['image/jpeg','image/png','image/webp'];
            if (allowed.indexOf(file.type) === -1) {
                alert('Chỉ chấp nhận JPG / PNG / WEBP.');
                e.target.value = '';
                previewWrapper.style.display='none';
                modalPreview.src='';
                return;
            }
            var reader = new FileReader();
            reader.onload = function(ev){ modalPreview.src = ev.target.result; previewWrapper.style.display='block'; };
            reader.readAsDataURL(file);
        });
    }

    // Form validation (bootstrap-style)
    (function () {
        var forms = document.getElementsByClassName('needs-validation');
        Array.prototype.forEach.call(forms, function(form) {
            form.addEventListener('submit', function(event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    })();

    // Validate avatar form before submit: check phone pattern again
    var avatarForm = document.getElementById('avatarForm');
    if (avatarForm) {
        avatarForm.addEventListener('submit', function(e){
            var phoneEl = document.querySelector('input[name="phone"]');
            if (phoneEl) {
                var val = phoneEl.value.trim();
                if (val !== '') {
                    var re = /^0\d{9}$/;
                    if (!re.test(val)) {
                        alert('Số điện thoại không hợp lệ. Phải bắt đầu bằng 0 và gồm đúng 10 chữ số.');
                        e.preventDefault();
                        return false;
                    }
                }
            }
            // file size/type already checked on change; double-check if user bypassed change
            var input = document.getElementById('modalAvatarInput');
            if (input && input.files && input.files.length > 0) {
                var f = input.files[0];
                var maxBytes = 2 * 1024 * 1024;
                if (f.size > maxBytes) { alert('Kích thước ảnh quá lớn (tối đa 2MB).'); e.preventDefault(); return false; }
            }
        });
    }
})();

</script>
<script>
function togglePW(id) {
    const el = document.getElementById(id);
    el.type = el.type === "password" ? "text" : "password";
}
</script>
<script>
document.getElementById("new_password").addEventListener("input", function () {
    let err = document.getElementById("password-error");

    if (this.value.length > 0 && this.value.length < 6) {
        err.style.display = "block";
    } else {
        err.style.display = "none";
    }
});
</script>

<!-- END: Profile content -->
