<form 
    action="<?= isset($data['editUser']) 
        ? APP_URL . '/TaiKhoan/edit/' . $data['editUser']['user_id'] 
        : APP_URL . '/TaiKhoan/create'; ?>" 
    method="post" 
    enctype="multipart/form-data"
    class="container mt-4">

    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">
                <?= isset($data['editUser']) ? '✏️ Chỉnh sửa thông tin tài khoản' : '➕ Thêm tài khoản mới'; ?>
            </h5>
        </div>

        <div class="card-body row g-3">

            <!-- Họ tên -->
            <div class="col-md-6">
                <label class="form-label">Họ tên</label>
                <input type="text" name="fullname" class="form-control" required
                    value="<?= isset($data['editUser']) ? htmlspecialchars($data['editUser']['fullname']) : ''; ?>">
            </div>

            <!-- Email -->
            <div class="col-md-6">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" required
                    value="<?= isset($data['editUser']) ? htmlspecialchars($data['editUser']['email']) : ''; ?>"
                    <?= isset($data['editUser']) ? 'readonly' : ''; ?>>
            </div>
              <!-- SDT -->
            <div class="col-md-6">
                <label class="form-label">Số điẹn thoại</label>
                <input type="phone" name="phone" class="form-control" required
                    value="<?= isset($data['editUser']) ? htmlspecialchars($data['editUser']['phone']) : ''; ?>"
                    <?= isset($data['editUser']) ?  : ''; ?>>
            </div>
              <!-- Địa chỉ -->
            <div class="col-md-6">
                <label class="form-label">Địa chỉ</label>
                <input type="address" name="address" class="form-control" required
                    value="<?= isset($data['editUser']) ? htmlspecialchars($data['editUser']['address']) : ''; ?>"
                    <?= isset($data['editUser']) ?  : ''; ?>>
            </div>

            <!-- Avatar -->
<!-- Avatar -->
<div class="col-md-6">
    <label class="form-label">Ảnh đại diện (Avatar)</label>
    <input type="file" name="avatar" class="form-control" accept="image/*" 
           onchange="previewAvatar(event)">
    
    <?php 
        $avatar = isset($data['editUser']['avatar']) && $data['editUser']['avatar'] != ''
            ? APP_URL . '/public/images/avatars/' . $data['editUser']['avatar']
            : APP_URL . '/public/images/avatars/default.png';
    ?>

    <img id="avatarPreview" 
         src="<?= $avatar ?>" 
         style="width:100px;height:100px;object-fit:cover;margin-top:10px;border-radius:8px;">
</div>


            <script>
                function previewAvatar(event) {
                    const img = document.getElementById('avatarPreview');
                    img.src = URL.createObjectURL(event.target.files[0]);
                }
            </script>

            <!-- Mật khẩu -->
            <div class="col-md-6">
                <label class="form-label">
                    <?= isset($data['editUser']) ? 'Mật khẩu mới' : 'Mật khẩu'; ?>
                </label>
                <input type="password" name="password" class="form-control"
                    placeholder="<?= isset($data['editUser']) ? 'Để trống nếu không đổi' : 'Nhập mật khẩu'; ?>">
            </div>

            <!-- Quyền -->
            <div class="col-md-6">
                <label class="form-label">Quyền người dùng</label>
                <select name="role" class="form-select">
                    <?php
                    $roles = [
                        'user' => 'Khách hàng',
                        'admin' => 'Quản trị viên',
                        'staff' => 'Nhân viên'
                    ];
                    $currentRole = isset($data['editUser']) ? $data['editUser']['role'] : 'user';
                    foreach ($roles as $key => $label) {
                        $selected = ($currentRole == $key) ? 'selected' : '';
                        echo "<option value='{$key}' {$selected}>{$label}</option>";
                    }
                    ?>
                </select>
            </div>

            <!-- Trạng thái -->
            <div class="col-md-6">
                <label class="form-label">Trạng thái tài khoản</label>
                <select name="status" class="form-select">
                    <?php
                    $statuses = [
                        'Hoạt động' => 'Hoạt động',
                        'Tạm ngưng' => 'Tạm ngưng',
                        'Đã xóa'   => 'Đã xóa'
                    ];
                    $currentStatus = isset($data['editUser']) ? $data['editUser']['status'] : 'Hoạt động';
                    foreach ($statuses as $value => $label) {
                        $selected = ($currentStatus == $value) ? 'selected' : '';
                        echo "<option value='" . htmlspecialchars($value, ENT_QUOTES) . "' {$selected}>{$label}</option>";
                    }
                    ?>
                </select>
            </div>

            <!-- Ngày tạo -->
            <div class="col-md-6">
                <label class="form-label">Ngày tạo</label>
                <input type="date" name="created_at" class="form-control"
                    value="<?= isset($data['editUser']) 
                        ? date('Y-m-d', strtotime($data['editUser']['created_at'])) 
                        : date('Y-m-d'); ?>"
                    readonly>
            </div>

        </div>

        <div class="card-footer text-end">
            <button type="submit" name="btn_submit"
                class="btn btn-<?= isset($data['editUser']) ? 'warning' : 'success'; ?>">
                <?= isset($data['editUser']) ? 'Cập nhật' : 'Lưu'; ?>
            </button>
        </div>
    </div>
</form>
