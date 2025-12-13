<div class="container py-4">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="text-primary mb-0">Quản lý tin tức</h3>
        <a href="<?= APP_URL ?>/News/create" class="btn btn-success">
            <i class="bi bi-plus-circle"></i> Thêm bài viết
        </a>
    </div>
    <form method="post" class="d-flex mb-3 gap-2">
    <input type="text" name="keyword" 
           class="form-control" 
           placeholder="Nhập tiêu đề, nội dung, trạng thái...">
    <button type="submit" name="btn_search" class="btn btn-primary">
         Tìm 
    </button>
</form>

    <!-- Danh sách tin tức -->
    <div class="card shadow-sm">
        <div class="card-header bg-dark text-white">
            <strong>Danh sách bài viết</strong>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0 align-middle text-center">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Ảnh</th>
                            <th>Tiêu đề</th>
                            <th>Nội dung</th>
                            <th>Trạng thái</th>
                            <th>Ngày tạo</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (!empty($data['NewsList'])) {
                            $i = 1;
                            foreach ($data['NewsList'] as $v) {
                        ?>
                                <tr>
                                    <td><?= $i++ ?></td>
                                    <td>
                                <img src="<?php echo APP_URL;?>/public/images/<?= htmlspecialchars($v['image']) ?>" 
                                style="height: 10rem;"/>
                            </td>
                                    <td><?= htmlspecialchars($v["title"]) ?></td>
                                    <td style="text-align: left;">
                                        <?= nl2br(htmlspecialchars(substr($v["content"], 0, 150))) ?><?= strlen($v["content"]) > 150 ? "..." : "" ?>
                                    </td>
                                  
                                    <td>
                                        <?php if (trim($v["status"]) == "hiển thị") { ?>
                                            <span class="badge bg-success">Hiển thị</span>
                                        <?php } else { ?>
                                            <span class="badge bg-secondary">Ẩn</span>
                                        <?php } ?>
                                    </td>
                                    <td><?= htmlspecialchars($v["created_at"]) ?></td>
                                    <td>
                                        <a href="<?= APP_URL ?>/News/edit/<?= $v["id"] ?>" class="btn btn-warning btn-sm">
                                            ✏️ Sửa
                                        </a>
                                        <a href="<?= APP_URL ?>/News/delete/<?= $v["id"] ?>" 
                                           class="btn btn-danger btn-sm"
                                           onclick="return confirm('Bạn có chắc muốn xoá bài viết này?');">
                                           🗑️ Xoá
                                        </a>
                                    </td>
                                </tr>
                        <?php
                            }
                        } else {
                        ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    Chưa có bài viết nào.
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>