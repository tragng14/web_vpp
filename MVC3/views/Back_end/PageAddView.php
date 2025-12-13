<form 
    action="<?= isset($data['pageData']) 
        ? APP_URL . '/Page/update/' . $data['pageData']['page_id'] 
        : APP_URL . '/Page/store' ?>"
    method="post"
    class="container mt-4"
>


    <div class="card shadow">

        <!-- HEADER -->
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">
                <?= isset($data['pageData']) ? 'Chỉnh sửa trang nội dung' : 'Thêm trang nội dung mới'; ?>
            </h5>
        </div>

        <!-- BODY -->
        <div class="card-body row g-3">

            <!-- CỘT TRÁI -->
            <div class="col-md-6">

                <!-- Title -->
                <label class="form-label">Tiêu đề</label>
                <input type="text" name="title" class="form-control"
                    value="<?= isset($data['pageData']) ? htmlspecialchars($data['pageData']['title']) : '' ?>" required>

                <br>

                <!-- Slug -->
                <label class="form-label">Slug (đường dẫn URL)</label>
                <input type="text" name="slug" class="form-control"
                    value="<?= isset($data['pageData']) ? htmlspecialchars($data['pageData']['slug']) : '' ?>" 
                    placeholder="vd: gioi-thieu" required>

                <br>

                <!-- Status -->
                <label class="form-label">Trạng thái</label>
                <select name="status" class="form-select">
                    <option value="active" <?= (isset($data['pageData']) && $data['pageData']['status'] == 'active') ? 'selected' : '' ?>>
                        Hiển thị
                    </option>
                    <option value="inactive" <?= (isset($data['pageData']) && $data['pageData']['status'] == 'inactive') ? 'selected' : '' ?>>
                        Ẩn
                    </option>
                </select>

            </div>

            <!-- CỘT PHẢI -->
            <div class="col-md-6">

                <label class="form-label">Nội dung HTML</label>
                <textarea name="content" rows="14" class="form-control" required><?= isset($data['pageData']) 
                    ? htmlspecialchars($data['pageData']['content']) 
                    : '' ?></textarea>

            </div>

        </div>

        <!-- FOOTER -->
        <div class="card-footer text-end">
            <button type="submit" class="btn btn-<?= isset($data['pageData']) ? 'warning' : 'success'; ?>">
                <?= isset($data['pageData']) ? 'Cập nhật trang' : 'Lưu trang'; ?>
            </button>
        </div>

    </div>
</form>
