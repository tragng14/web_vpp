<div class="container mt-4">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="text-primary mb-0">Quản lý Nội dung tĩnh</h3>
        <a href="<?= APP_URL ?>/Page/create" class="btn btn-success">+ Thêm trang mới</a>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-dark text-white">
            <strong>Danh sách Nội dung</strong>
        </div>

        <table class="table table-striped table-hover mb-0 align-middle text-center">
            <thead class="table-light">
                <tr>
                    <th width="50">ID</th>
                    <th>Tiêu đề</th>
                    <th>Slug</th>
                    <th>Nội dung</th>
                    <th width="120">Trạng thái</th>
                    <th width="160">Hành động</th>
                </tr>
            </thead>

            <tbody>
            <?php if (!empty($data['pages'])): ?>
                <?php foreach ($data['pages'] as $p): ?>

                    <?php 
                        $page_id = isset($p['page_id']) ? $p['page_id'] : 0;   // <-- ĐÃ SỬA
                        $title   = $p['title']   ?? '(Không có tiêu đề)';
                        $slug    = $p['slug']    ?? '';
                        $content = $p['content'] ?? '';
                        $status  = $p['status']  ?? 'inactive';
                    ?>

                    <tr>
                        <td><?= $page_id ?></td>
                        <td><?= htmlspecialchars($title) ?></td>
                        <td><?= htmlspecialchars($slug) ?></td>
                        <td class="text-start">
    <?php 
        $short = mb_substr(strip_tags($content), 0, 120, 'UTF-8');
        echo htmlspecialchars($short . (strlen($content) > 120 ? '...' : ''));
    ?>
</td>

                        <td>
                            <?= ($status === 'active')
                                ? '<span class="badge bg-success">Hiển thị</span>'
                                : '<span class="badge bg-secondary">Ẩn</span>' ?>
                        </td>
                        <td>
                            <a href="<?= APP_URL ?>/Page/edit/<?= $page_id ?>" 
                                class="btn btn-warning btn-sm">Sửa</a>

                            <a href="<?= APP_URL ?>/Page/delete/<?= $page_id ?>"
                                onclick="return confirm('Xóa trang này?')"
                                class="btn btn-danger btn-sm">
                                Xoá
                            </a>
                        </td>
                    </tr>

                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" class="text-center text-muted">Chưa có trang nội dung nào.</td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>
