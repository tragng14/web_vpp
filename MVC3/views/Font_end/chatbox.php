<?php 
$messages = isset($data["messages"]) ? $data["messages"] : [];
?>

<style>
.chatbox-container {
    width: 100%;
    height: 450px;
    background: #fff;
    border-radius: 12px;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    font-family: Arial, sans-serif;
}

/* Khung tin nhắn */
.chatbox-messages {
    flex: 1;
    padding: 10px;
    overflow-y: auto;
    background: #f7f9fc;
}

/* Bong bóng */
.message {
    padding: 8px 10px;
    margin-bottom: 10px;
    border-radius: 8px;
    width: 85%;
    font-size: 14px;
}

/* USER — bên phải */
.user-msg {
    background: #e7f3ff;
    margin-left: auto;
    text-align: right;
    border-right: 3px solid #007bff;
}

/* ADMIN — bên trái */
.admin-msg {
    background: #ffe9e9;
    margin-right: auto;
    border-left: 3px solid #ff4d4d;
}

/* STAFF — bên trái */
.staff-msg {
    background: #fff4ce;
    margin-right: auto;
    border-left: 3px solid #ffc107;
}

/* Input */
.chatbox-input {
    padding: 10px;
    border-top: 1px solid #ddd;
    background: #fff;
    display: flex;
    gap: 10px;
}

.chatbox-input input {
    flex: 1;
    padding: 10px 12px;
    border: 1px solid #ccc;
    border-radius: 20px;
    outline: none;
    font-size: 14px;
}

.chatbox-input button {
    background: #007bff;
    border: none;
    color: #fff;
    padding: 10px 16px;
    border-radius: 50%;
    font-size: 16px;
    cursor: pointer;
}
.chatbox-input button:hover {
    background: #0056d2;
}
</style>

<div class="chatbox-container">

    <div class="chatbox-messages" id="chatMessages">
        <?php if (!empty($messages)): ?>
            <?php foreach ($messages as $msg): ?>
                <?php 
                    $class = $msg["sent_by"] . "-msg";
                    $name  = ($msg["sent_by"] == "user") ? "Bạn" : ucfirst($msg["sent_by"]);
                ?>
                <div class="message <?= $class ?>">
                    <b><?= $name ?>:</b><br>
                    <?= htmlspecialchars($msg["message"]) ?><br>
                    <small style="color:#666;"><?= $msg["created_at"] ?></small>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p style="color:#777;">💬 Chưa có tin nhắn nào.</p>
        <?php endif; ?>
    </div>

    <!-- FORM GỬI TIN -->
    <form id="chatForm" class="chatbox-input">
        <input type="text" id="messageInput" name="message" placeholder="Nhập tin nhắn..." required>
        <button type="submit">➤</button>
    </form>

</div>

<script>
// AUTO SCROLL DOWN
function scrollToBottom() {
    let box = document.getElementById("chatMessages");
    box.scrollTop = box.scrollHeight;
}

// Gửi tin nhắn — KHÔNG RELOAD TRANG
document.getElementById("chatForm").addEventListener("submit", function(e) {
    e.preventDefault();

    let msg = document.getElementById("messageInput").value.trim();
    if (msg === "") return;

    let formData = new FormData();
    formData.append("message", msg);

    fetch("<?= APP_URL ?>/ChatUser/send", {
        method: "POST",
        body: formData
    })
    .then(res => res.text())
    .then(() => {
        document.getElementById("messageInput").value = "";
        loadMessages(); // load lại tin nhắn mới
    });
});

// Hàm lấy tin nhắn tự động
function loadMessages() {
    fetch("<?= APP_URL ?>/ChatUser/getMessages")
        .then(res => res.text())
        .then(html => {
            document.getElementById("chatMessages").innerHTML = html;
            scrollToBottom();
        });
}

// Auto refresh mỗi 2 giây
setInterval(loadMessages, 2000);

// Scroll xuống khi load trang
scrollToBottom();
</script>
