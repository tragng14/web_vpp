<div class="container py-4">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="text-primary mb-0">Quản lý đánh giá sản phẩm</h3>
    </div>

    <!-- Bộ lọc -->
    <form method="GET" action="<?= APP_URL ?>/Review/show" class="d-flex gap-2 mb-3">
        <input type="text" name="keyword" class="form-control" placeholder="Nhập mã sản phẩm..." 
               value="<?= isset($_GET['keyword']) ? htmlspecialchars($_GET['keyword']) : '' ?>">

        <select name="stars" class="form-select">
            <option value="">-- Tất cả số sao --</option>
            <?php for ($i = 1; $i <= 5; $i++): ?>
                <option value="<?= $i ?>" <?= (isset($_GET['stars']) && $_GET['stars'] == $i) ? 'selected' : '' ?>>
                    <?= $i ?> ⭐
                </option>
            <?php endfor; ?>
        </select>

        <select name="status" class="form-select">
            <option value="">-- Trạng thái --</option>
            <option value="0" <?= (isset($_GET['status']) && $_GET['status'] == '0') ? 'selected' : '' ?>>Chờ duyệt</option>
            <option value="1" <?= (isset($_GET['status']) && $_GET['status'] == '1') ? 'selected' : '' ?>>Hiển thị</option>
            <option value="2" <?= (isset($_GET['status']) && $_GET['status'] == '2') ? 'selected' : '' ?>>Ẩn</option>
        </select>

        <button type="submit" class="btn btn-primary">🔍 Lọc</button>
    </form>

    <!-- Danh sách đánh giá -->
    <div class="card shadow-sm">
        <div class="card-header bg-dark text-white">
            <strong>Danh sách đánh giá</strong>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0 align-middle text-center">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Mã SP</th>
                            <th>Tên người dùng</th>
                            <th>Email</th>
                            <th>Nội dung</th>
                            <th>Số sao</th>
                            <th>Trạng thái</th>
                            <th>Ngày đăng</th>
                            <th>Phản hồi Admin</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if (!empty($data['reviewList'])) { 
                            $i = 1;
                            foreach ($data['reviewList'] as $r): 
                        ?>
                        <tr>
                            <td><?= $i++ ?></td>
                            <td><?= htmlspecialchars($r['masp']) ?></td>
                            <td><?= htmlspecialchars($r['tenNguoiDung']) ?></td>
                            <td><?= htmlspecialchars($r['email']) ?></td>
                            <td class="text-start" style="max-width: 250px;">
                                <?= nl2br(htmlspecialchars($r['noidung'])) ?>
                            </td>
                            <td>
                                <?php for ($j = 1; $j <= 5; $j++): ?>
                                    <?= $j <= $r['sao'] ? '⭐' : '☆' ?>
                                <?php endfor; ?>
                            </td>

                            <!-- Trạng thái -->
                            <td>
                                <?php 
                                    switch ($r['trangthai']) {
                                        case 1: echo '<span class="badge bg-success">Hiển thị</span>'; break;
                                        case 2: echo '<span class="badge bg-secondary">Ẩn</span>'; break;
                                        default: echo '<span class="badge bg-warning text-dark">Chờ duyệt</span>'; break;
                                    }
                                ?>
                            </td>

                            <td><?= htmlspecialchars($r['ngayDang']) ?></td>

                            <!-- Hiển thị phản hồi (nếu có) -->
                            <td class="text-start">
                                <?php if (!empty($r['traloi'])): ?>
                                    <div class="p-2 bg-light rounded">
                                        <strong>Admin:</strong> <?= nl2br(htmlspecialchars($r['traloi'])) ?>
                                    </div>
                                <?php else: ?>
                                    <em class="text-muted">Chưa có phản hồi</em>
                                <?php endif; ?>
                            </td>

                            <!-- Thao tác -->
                            <td>
                                <a href="<?= APP_URL ?>/Review/replyForm/<?= $r['id'] ?>" 
                                   class="btn btn-sm btn-info text-white mb-1">💬 Phản hồi</a>

                                <?php if ($r['trangthai'] == 0): ?>
                                    <a href="<?= APP_URL ?>/Review/approve/<?= $r['id'] ?>" 
                                       class="btn btn-sm btn-success mb-1">✅ Duyệt</a>
                                <?php elseif ($r['trangthai'] == 1): ?>
                                    <a href="<?= APP_URL ?>/Review/hide/<?= $r['id'] ?>" 
                                       class="btn btn-sm btn-warning mb-1">🚫 Ẩn</a>
                                <?php else: ?>
                                    <a href="<?= APP_URL ?>/Review/approve/<?= $r['id'] ?>" 
                                       class="btn btn-sm btn-success mb-1">👁️ Hiển thị lại</a>
                                <?php endif; ?>

                                <a href="<?= APP_URL ?>/Review/delete/<?= $r['id'] ?>" 
                                   class="btn btn-sm btn-danger" 
                                   onclick="return confirm('Bạn có chắc muốn xoá đánh giá này?');">🗑️ Xóa</a>
                            </td>
                        </tr>
                        <?php 
                            endforeach; 
                        } else { 
                        ?>
                        <tr>
                            <td colspan="10" class="text-center text-muted py-4">Không có đánh giá nào.</td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
