<div class="container py-4">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="text-primary mb-0">Quản lý Banner</h3>
        <a href="<?= APP_URL ?>/Banner/create" class="btn btn-success">
            <i class="bi bi-plus-circle"></i> Thêm Banner mới
        </a>
    </div>

    <!-- (KHÔNG CẦN TÌM KIẾM) -->
    <!-- Nếu sau muốn tìm theo tiêu đề, tôi viết thêm -->

    <div class="card shadow-sm">
        <div class="card-header bg-dark text-white">
            <strong>Danh sách Banner</strong>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">

                <table class="table table-striped table-hover mb-0 align-middle text-center">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Tên Banner</th>
                            <th>Mô tả</th>
                            <th>Trạng thái</th>
                            <th>Ngày tạo</th>
                            <th>Hình ảnh</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (!empty($data['banners'])): 
                            $i = 1;
                            foreach ($data['banners'] as $b): ?>
                            
                            <tr>
                                <td><?= $i++ ?></td>

                                <td><?= htmlspecialchars($b["title"]) ?></td>

                                <td style="text-align: left;">
                                    <?= nl2br(htmlspecialchars(substr($b["description"], 0, 120))) ?>
                                    <?= strlen($b["description"]) > 120 ? "..." : "" ?>
                                </td>

                                <td>
                                    <?php if ($b["status"] === "active"): ?>
                                        <span class="badge bg-success">Đang hiển thị</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Ẩn</span>
                                    <?php endif; ?>
                                </td>

                                <td><?= htmlspecialchars($b["created_at"]) ?></td>

                                <td>
                                    <a href="<?= APP_URL ?>/Banner/edit/<?= $b["banner_id"] ?>"
                                       class="btn btn-info btn-sm">
                                        📷 Xem ảnh
                                    </a>
                                </td>

                                <td>
                                    <a href="<?= APP_URL ?>/Banner/edit/<?= $b["banner_id"] ?>" 
                                       class="btn btn-warning btn-sm">
                                        ✏️ Sửa
                                    </a>

                                    <a href="<?= APP_URL ?>/Banner/delete/<?= $b["banner_id"] ?>" 
                                       class="btn btn-danger btn-sm"
                                       onclick="return confirm('Bạn có chắc muốn xoá banner này? Tất cả ảnh bên trong cũng sẽ bị xóa!');">
                                        🗑️ Xoá
                                    </a>
                                </td>
                            </tr>

                        <?php endforeach; else: ?>

                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                Chưa có banner nào.
                            </td>
                        </tr>

                        <?php endif; ?>
                    </tbody>
                </table>

            </div>
        </div>

    </div>
</div>
