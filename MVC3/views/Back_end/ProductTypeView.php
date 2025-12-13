<?php if (!empty($_SESSION['delete_message'])): ?>
    <div class="alert alert-info">
        <?= $_SESSION['delete_message']; ?>
    </div>
    <?php unset($_SESSION['delete_message']); ?>
<?php endif; ?>


<div class="container mt-5">
    <h2 class="mb-4">📦 Quản lý danh mục loại sản phẩm</h2>

    <!-- 🔍 Form tìm kiếm -->
    <form action="<?= APP_URL ?>/ProductType/search" method="get" class="mb-4">
        <div class="row g-2 align-items-end">
            <div class="col-md-4">
                <label for="keyword" class="form-label">Mã loại SP/ Tên loại SP</label>
                <input type="text" name="keyword" id="keyword" 
       class="form-control"
       placeholder="Nhập mã hoặc tên loại sản phẩm..."
       value="<?= isset($data['keyword']) ? htmlspecialchars($data['keyword']) : '' ?>">
</div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-success">🔍 Tìm kiếm</button>
              
            </div>
        </div>
    </form>

    <!-- Nút Thêm sản phẩm -->
    <?php if (!empty($data["productList"])): ?>
        <table class="table table-bordered table-hover">
            <tr>
                <td colspan="7">
                    <?php
                        $isEdit = isset($data["editItem"]);
                        $edit = $isEdit ? $data["editItem"] : null;
                    ?>
                    <form 
                        action="<?= $isEdit ? APP_URL . "/ProductType/update/" . $edit["maLoaiSP"] 
                                           : APP_URL . "/ProductType/create" ?>" 
                        method="post" 
                        class="bg-light p-3 rounded shadow-sm"
                    >
                    <div class="row align-items-end gx-3 gy-2">
                        <div class="col-md-3">
                            <label for="txt_maloaisp" class="form-label">Mã loại SP</label>
                            <input type="text" name="txt_maloaisp" id="txt_maloaisp" class="form-control" 
                                required value="<?= $isEdit ? htmlspecialchars($edit["maLoaiSP"]) : '' ?>" 
                                <?= $isEdit ? 'readonly' : '' ?> />
                        </div>

                        <div class="col-md-3">
                            <label for="txt_tenloaisp" class="form-label">Tên loại SP</label>
                            <input type="text" name="txt_tenloaisp" id="txt_tenloaisp" class="form-control"
                                value="<?= $isEdit ? htmlspecialchars($edit["tenLoaiSP"]) : '' ?>" />
                        </div>

                        <div class="col-md-3">
                            <label for="txt_motaloaisp" class="form-label">Mô tả</label>
                            <input type="text" name="txt_motaloaisp" id="txt_motaloaisp" class="form-control"
                                value="<?= $isEdit ? htmlspecialchars($edit["moTaLoaiSP"]) : '' ?>" />
                        </div>
                       
                        <div class="col-md-3">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-<?= $isEdit ? 'warning' : 'primary' ?>">
                                    💾 <?= $isEdit ? "Cập nhật" : "Thêm mới" ?>
                                </button>
                                <?php if ($isEdit): ?>
                                    <a href="<?= APP_URL ?>/ProductType" class="btn btn-secondary">
                                        🔁 Huỷ 
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>  
                    </form>              
                </td>
            </tr>

            <!-- Danh sách -->
            <tr>
                <th>STT</th>
                <th>Mã loại SP</th>
                <th>Tên loại SP</th>
                <th>Mô tả</th>
                <th>Ngày tạo</th>
                <th>Ngày sửa</th>
                <th>Hành động</th>
            </tr>
            <?php 
            $i = 0;
            foreach ($data["productList"] as $v): 
                $i++;
            ?>
            <tr>
                <td><?= $i ?></td>
                <td><?= htmlspecialchars($v["maLoaiSP"]) ?></td>
                <td><?= htmlspecialchars($v["tenLoaiSP"]) ?></td>
                <td><?= htmlspecialchars($v["moTaLoaiSP"]) ?></td>
                <td><?= date('d/m/Y H:i', strtotime($v["ngayTao"])) ?></td>
<td><?= date('d/m/Y H:i', strtotime($v["ngaySua"])) ?></td>

                <td>
                    <a href="<?= APP_URL ?>/ProductType/edit/<?= $v["maLoaiSP"] ?>" class="btn btn-warning btn-sm">✏️ Sửa</a>
                    <a href="<?= APP_URL ?>/ProductType/delete/<?= $v["maLoaiSP"] ?>" class="btn btn-danger btn-sm"
                        onclick="return confirm('Bạn có chắc muốn xoá sản phẩm này?');">🗑️ Xoá</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <div class="alert alert-info">Không tìm thấy loại sản phẩm nào.</div>
    <?php endif; ?>
</div>
