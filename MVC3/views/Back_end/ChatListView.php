<?php 
$users = isset($data['users']) ? $data['users'] : []; 
?>
<div class="container py-4">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="text-primary mb-0">📨 Quản lý Chatbox</h3>
    </div>

    <div class="card shadow-sm">

        <?php if (empty($users)): ?>
            <div class="card-body">
                <p class="text-muted mb-0">❌ Chưa có người dùng nào gửi tin nhắn.</p>
            </div>

        <?php else: ?>

            <div class="card-header bg-dark text-white">
                <strong>Danh sách người dùng đã gửi tin nhắn</strong>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0 align-middle text-center">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 60px;">#</th>
                            <th>Email người dùng</th>
                            <th>Tin nhắn gần nhất</th>
                            <th>Gửi bởi</th>
                            <th>Phản hồi mới nhất của admin</th>
                            <th>Ngày gửi</th>
                            <th style="width: 200px;" class="text-end">Thao tác</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php $i = 1; ?>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?= $i++; ?></td>

                                <td><?= htmlspecialchars($user['username']); ?></td>

                                <td class="text-start" style="max-width: 280px;">
                                    <?= htmlspecialchars($user['message']); ?>
                                </td>

                                <td>
                                    <?php 
                                        if ($user['sent_by'] === "admin") echo "👨‍💼 Admin";
                                        elseif ($user['sent_by'] === "staff") echo "👩‍💼 Nhân viên";
                                        else echo "👤 Người dùng";
                                    ?>
                                </td>

                                <td class="text-start" style="max-width: 280px;">
                                    <?php if (!empty($user['last_admin_reply'])): ?>
                                        📨 <?= htmlspecialchars($user['last_admin_reply']); ?>
                                    <?php else: ?>
                                        <span class="text-muted">Chưa phản hồi</span>
                                    <?php endif; ?>
                                </td>

                                <td><?= htmlspecialchars($user['created_at']); ?></td>

                                <td class="text-end">

                                    <!-- Nút xem hội thoại -->
                                    <a href="<?= APP_URL ?>/Chat/viewUserMessages/<?= urlencode($user['username']) ?>"
                                       class="btn btn-primary btn-sm">
                                        💬 Phản hồi
                                    </a>

                                 

                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="card-footer text-end text-muted">
                Tổng số: <b><?= count($users) ?></b> người dùng
            </div>

        <?php endif; ?>

    </div>

</div>
