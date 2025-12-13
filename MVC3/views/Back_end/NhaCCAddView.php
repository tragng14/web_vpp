
<div class="container py-4">

    <?php 
        $isEdit = isset($edit) && $edit === true;
$row = $row ?? [];

        $title  = $isEdit ? "✏️ Cập nhật nhà cung cấp" : "➕ Thêm nhà cung cấp";
        $action = $isEdit 
                    ? APP_URL . "/NhaCC/update/" . $row['maNCC'] 
                    : APP_URL . "/NhaCC/store";
    ?>

    <h3 class="text-primary mb-3"><?= $title ?></h3>

    <div class="card shadow-sm">
        <div class="card-header bg-dark text-white">
            <strong><?= $isEdit ? "Chỉnh sửa thông tin" : "Nhập nhà cung cấp mới" ?></strong>
        </div>

        <div class="card-body">

            <form action="<?= $action ?>" method="POST">

                <!-- Mã nhà cung cấp -->
                <div class="mb-3">
                    <label class="form-label">Mã nhà cung cấp</label>
                    <input 
                        type="text" 
                        name="maNCC" 
                        class="form-control"
                        value="<?= $isEdit ? $row['maNCC'] : '' ?>"
                        <?= $isEdit ? 'readonly' : '' ?>
                        required>
                </div>

                <!-- Tên NCC -->
                <div class="mb-3">
                    <label class="form-label">Tên nhà cung cấp</label>
                    <input 
                        type="text" 
                        name="tenNCC" 
                        class="form-control"
                        value="<?= $isEdit ? htmlspecialchars($row['tenNCC']) : '' ?>"
                        required>
                </div>

                <!-- Địa chỉ -->
                <div class="mb-3">
                    <label class="form-label">Địa chỉ</label>
                    <textarea 
                        name="diaChi" 
                        rows="2" 
                        class="form-control"
                    ><?= $isEdit ? htmlspecialchars($row['diaChi']) : '' ?></textarea>
                </div>

                <!-- SĐT + Email -->
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Số điện thoại</label>
                        <input 
                            type="text" 
                            name="sdt" 
                            class="form-control"
                            value="<?= $isEdit ? htmlspecialchars($row['sdt']) : '' ?>"
                            required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Email</label>
                        <input 
                            type="email" 
                            name="email" 
                            class="form-control"
                            value="<?= $isEdit ? htmlspecialchars($row['email']) : '' ?>">
                    </div>
                </div>

                <!-- Người liên hệ -->
                <div class="mb-3">
                    <label class="form-label">Người liên hệ</label>
                    <input 
                        type="text" 
                        name="nguoiLH" 
                        class="form-control"
                        value="<?= $isEdit ? htmlspecialchars($row['nguoiLH']) : '' ?>">
                </div>

                <!-- Ghi chú -->
                <div class="mb-3">
                    <label class="form-label">Ghi chú</label>
                    <textarea 
                        name="ghiChu" 
                        rows="2" 
                        class="form-control"
                    ><?= $isEdit ? htmlspecialchars($row['ghiChu']) : '' ?></textarea>
                </div>

                <button type="submit" class="btn btn-primary">
                    <?= $isEdit ? "💾 Cập nhật" : "➕ Thêm mới" ?>
                </button>

                <a href="<?= APP_URL ?>/NhaCC/show" class="btn btn-secondary">
                    ⬅️ Quay lại
                </a>

            </form>

        </div>
    </div>

</div>
