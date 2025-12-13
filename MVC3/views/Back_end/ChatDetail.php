<div class="container py-4">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="text-primary mb-0">
            💬 Hội thoại với: <?= htmlspecialchars($email) ?>
        </h3>

     
    </div>


    <!-- CARD CHAT -->
    <div class="card shadow-sm">

        <div class="card-header bg-primary text-white fw-bold">
            💬 Lịch sử tin nhắn
        </div>

        <!-- KHUNG CHAT -->
        <div class="card-body chat-box" id="chatBox">

            <?php if (empty($messages)) : ?>
                <p class="text-center text-muted">Không có tin nhắn nào.</p>
            <?php else: ?>
                <?php foreach ($messages as $msg): ?>

                    <?php 
                        // Kiểu người gửi
                        if ($msg["sent_by"] === "admin") {
                            $align = "text-end";
                            $bg = "admin-msg";
                            $title = "👨‍💼 Admin";
                        } elseif ($msg["sent_by"] === "staff") {
                            $align = "text-end";
                            $bg = "staff-msg";
                            $title = "👩‍💼 Nhân viên";
                        } else {
                            $align = "text-start";
                            $bg = "user-msg";
                            $title = "👤 Người dùng";
                        }
                    ?>

                    <div class="mb-3 <?= $align ?>">
                        <div class="msg-item <?= $bg ?>">
                            <div class="msg-title fw-bold"><?= $title ?></div>
                            <div class="msg-content"><?= nl2br(htmlspecialchars($msg["message"])) ?></div>
                            <div class="msg-time text-muted small">
                                <?= date("d/m/Y H:i", strtotime($msg["created_at"])) ?>
                            </div>
                        </div>
                    </div>

                <?php endforeach; ?>
            <?php endif; ?>

        </div>


        <!-- FORM PHẢN HỒI -->
        <div class="card-footer bg-light">

            <form method="POST" action="<?= APP_URL ?>/Chat/doReply?reply=1">

                <input type="hidden" name="email" value="<?= htmlspecialchars($email) ?>">

                <label class="form-label fw-bold">✉️ Trả lời:</label>

                <textarea name="message" class="form-control mb-3" rows="3" required 
                    placeholder="Nhập nội dung phản hồi..."></textarea>

                <button class="btn btn-success w-100">
                    Gửi phản hồi
                </button>

            </form>

        </div>

    </div>

</div>

   <a href="<?= APP_URL ?>/Chat" class="btn btn-secondary">
            ⬅ Quay lại danh sách
        </a>
<!-- ===================== CSS ===================== -->
<style>
    .chat-box {
        height: 600px;
        overflow-y: auto;
        padding: 15px;
        scroll-behavior: smooth;
        background: #f4f7ff;
    }

    .msg-item {
        display: inline-block;
        max-width: 75%;
        padding: 12px 16px;
        border-radius: 14px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        animation: fadeIn 0.2s ease-in-out;
    }

    .user-msg {
        background: #fff7d1;
        border: 1px solid #f2d57c;
    }

    .admin-msg {
        background: #d9ecff;
        border: 1px solid #9cc5ff;
    }

    .staff-msg {
        background: #d7f8df;
        border: 1px solid #9de3a2;
    }

    .msg-title {
        font-size: 14px;
        margin-bottom: 4px;
    }

    .msg-content {
        font-size: 15px;
        margin-bottom: 6px;
        white-space: pre-wrap;
        word-break: break-word;
    }

    @keyframes fadeIn {
        from {opacity: 0; transform: translateY(5px);}
        to {opacity: 1; transform: translateY(0);}
    }
</style>


<!-- ===================== AUTO SCROLL ===================== -->
<script>
    const chatBox = document.getElementById("chatBox");
    const url = new URL(window.location.href);

    if (url.searchParams.get("reply") === "1") {
        chatBox.scrollTop = chatBox.scrollHeight; // sau khi gửi thì cuộn xuống
    }
</script>
