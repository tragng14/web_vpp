<form 
    action="<?= isset($data['banner']) 
        ? APP_URL . '/Banner/update/' . $data['banner']['banner_id'] 
        : APP_URL . '/Banner/store' ?>"
    method="post"
    enctype="multipart/form-data"
    class="container mt-4"
>
    <div class="card shadow">

        <!-- HEADER -->
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">
                <?= isset($data['banner']) ? 'Chỉnh sửa Banner' : 'Thêm Banner mới'; ?>
            </h5>
        </div>

        <!-- BODY -->
        <div class="card-body row g-3">

            <!-- CỘT TRÁI: HÌNH ẢNH -->
            <div class="col-md-6">
<?php if (isset($data['banner']) && !empty($data['images'])): ?>
<h6 class="mt-4 mb-2">Ảnh hiện tại</h6>

<div class="row">
<?php foreach ($data['images'] as $img): ?>
    <div class="col-md-4">

        <div class="card shadow-sm mb-3">
            <div class="card-body text-center">

                <!-- Ảnh -->
                <img src="<?= APP_URL ?>/public/images/banners/<?= htmlspecialchars($img['image_path']) ?>" 
                     class="img-fluid rounded mb-2" 
                     style="height: 100px; object-fit: cover;">

                <input type="hidden" name="old_img_id[]" value="<?= $img['img_id'] ?>">

                <!-- Link -->
                <label class="form-label">Link ảnh</label>
                <input type="text" name="old_link[]" class="form-control form-control-sm mb-2"
                       value="<?= htmlspecialchars($img['link']) ?>">

                <!-- Sort -->
                <label class="form-label">Thứ tự</label>
                <input type="number" name="old_sort[]" class="form-control form-control-sm mb-2"
                       value="<?= $img['sort_order'] ?>">

                <!-- Nút xóa -->
                <a href="<?= APP_URL ?>/Banner/deleteImage/<?= $data['banner']['banner_id'] ?>/<?= $img['img_id'] ?>"
                   onclick="return confirm('Xóa ảnh này?');"
                   class="btn btn-danger btn-sm w-100">
                    Xoá ảnh
                </a>

            </div>
        </div>

    </div>
<?php endforeach; ?>
</div>

<?php endif; ?>


            </div>

            <!-- CỘT PHẢI: THÔNG TIN -->
            <div class="col-md-6">

                <!-- Tên -->
                <label class="form-label">Tên Banner</label>
                <input type="text" name="title" class="form-control"
                    value="<?= isset($data['banner']) ? htmlspecialchars($data['banner']['title']) : '' ?>" required>

                <br>

                <!-- Mô tả -->
                <label class="form-label">Mô tả</label>
                <textarea name="description" rows="4" class="form-control"><?= isset($data['banner']) ? htmlspecialchars($data['banner']['description']) : '' ?></textarea>

                <br>

                <!-- Trạng thái -->
                <label class="form-label">Trạng thái</label>
                <select name="status" class="form-select">
                    <option value="active" <?= (isset($data['banner']) && $data['banner']['status'] == 'active') ? 'selected' : '' ?>>Hiển thị</option>
                    <option value="inactive" <?= (isset($data['banner']) && $data['banner']['status'] == 'inactive') ? 'selected' : '' ?>>Ẩn</option>
                </select>

            </div>


            <!-- UPLOAD ẢNH MỚI -->
<div class="mt-3">
    <h6>Thêm ảnh mới</h6>

    <div id="img-input-list">

        <div class="row mb-2">
            <div class="col-md-4">
                <input type="file" name="images[]" class="form-control">
            </div>

            <div class="col-md-4">
                <input type="text" name="link[]" class="form-control" placeholder="Link ảnh">
            </div>

            <div class="col-md-4">
                <input type="number" name="sort[]" class="form-control" placeholder="Thứ tự">
            </div>
        </div>

    </div>

    <button type="button" class="btn btn-secondary btn-sm" onclick="addMoreImage()">+ Thêm ảnh</button>
</div>


<script>
function addMoreImage() {
    const row = `
    <div class="row mb-2">
        <div class="col-md-4">
            <input type="file" name="images[]" class="form-control">
        </div>
        <div class="col-md-4">
            <input type="text" name="link[]" class="form-control" placeholder="Link ảnh">
        </div>
        <div class="col-md-4">
            <input type="number" name="sort[]" class="form-control" placeholder="Thứ tự">
        </div>
    </div>`;
    document.getElementById("img-input-list").insertAdjacentHTML("beforeend", row);
}

</script>


        <!-- FOOTER -->
        <div class="card-footer text-end">
            <button type="submit" class="btn btn-<?= isset($data['banner']) ? 'warning' : 'success'; ?>">
                <?= isset($data['banner']) ? 'Cập nhật Banner' : 'Lưu Banner'; ?>
            </button>
        </div>

    </div>
</form>
