<div class="container py-4">

    <?php
        // Xác định chế độ thêm hay sửa
       $isEdit = isset($edit) && $edit === true;

    $title  = $isEdit ? "✏️ Cập nhật hợp đồng" : "➕ Thêm hợp đồng";
  $action = $isEdit 
    ? APP_URL . "/NhaCC/hd_update/" . $row['maHD'] 
    : APP_URL . "/NhaCC/hd_store";
?>



    <h3 class="text-primary mb-3"><?= $title ?></h3>

    <div class="card shadow-sm">

        <div class="card-header bg-dark text-white">
            <strong><?= $isEdit ? "Chỉnh sửa thông tin hợp đồng" : "Nhập hợp đồng mới" ?></strong>
        </div>

        <div class="card-body">

            <form action="<?= $action ?>" method="POST">

                <!-- Mã hợp đồng -->
                <div class="mb-3">
                    <label class="form-label">Mã hợp đồng</label>
                    <input
                        type="text"
                        name="maHD"
                        class="form-control"
                        value="<?= $isEdit ? $row['maHD'] : '' ?>"
                        <?= $isEdit ? 'readonly' : '' ?>
                        required>
                </div>

                <!-- Nhà cung cấp -->
                <div class="mb-3">
                    <label class="form-label">Nhà cung cấp</label>

                    <?php if ($isEdit): ?>
                        <!-- Nếu sửa, không cho chọn NCC -->
                        <input type="text" class="form-control"
                               value="<?= $row['maNCC'] ?>" readonly>
                        <input type="hidden" name="maNCC" value="<?= $row['maNCC'] ?>">

                    <?php else: ?>
                        <!-- Nếu thêm mới, cho chọn NCC -->
                        <select name="maNCC" class="form-control" required>
                            <option value="">-- Chọn nhà cung cấp --</option>

                            <?php foreach ($nccList as $n): ?>
                                <option value="<?= $n['maNCC'] ?>">
                                    <?= $n['maNCC'] ?> - <?= htmlspecialchars($n['tenNCC']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    <?php endif; ?>
                </div>

                <!-- Tên hợp đồng -->
                <div class="mb-3">
                    <label class="form-label">Tên hợp đồng</label>
                    <input
                        type="text"
                        name="tenHD"
                        class="form-control"
                        value="<?= $isEdit ? htmlspecialchars($row['tenHD']) : '' ?>"

                        required>
                </div>

                <!-- Ngày ký & Ngày hết hạn -->
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Ngày ký</label>
                        <input type="date" name="ngayKy" class="form-control"
                              value="<?= $isEdit ? $row['ngayKy'] : '' ?>" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Ngày hết hạn</label>
                        <input type="date" name="ngayHetHan" class="form-control"
                              value="<?= $isEdit ? $row['ngayHetHan'] : '' ?>"
>
                    </div>
                </div>

                <!-- Giá trị hợp đồng -->
                <div class="mb-3">
                    <label class="form-label">Giá trị (VNĐ)</label>
                    <input type="number" name="giaTri" class="form-control"
                           value="<?= $isEdit ? $row['giaTri'] : 0 ?>">
                </div>

                <!-- Trạng thái -->
                <div class="mb-3">
                    <label class="form-label">Trạng thái</label>
                    <select name="trangThai" class="form-control">
                        <?php
                            $states = [
                                "dang_hieu_luc" => "Đang hiệu lực",
                                "het_hieu_luc"  => "Hết hiệu lực",
                                "khong_hieu_luc" => "Không hiệu lực"
                            ];
                        ?>

                        <?php foreach ($states as $key => $text): ?>
                            <option value="<?= $key ?>"
                                <?= $isEdit && $row['trangThai'] == $key ? 'selected' : '' ?>>
                                <?= $text ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Nội dung -->
                <div class="mb-3">
                    <label class="form-label">Nội dung hợp đồng</label>
                    <textarea name="noiDung" rows="5" class="form-control"><?= 
                        $isEdit ? htmlspecialchars($row['noiDung']) : '' 
                    ?></textarea>
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
