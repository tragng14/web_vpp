<?php
require_once "BaseModel.php";

class ChatModel extends BaseModel {
    private $table = "tblchat";

    // -------------------- GỬI TIN NHẮN --------------------


    // -------------------- LẤY TOÀN BỘ TIN NHẮN --------------------
public function getMessages($username) {
    $sql = "SELECT * FROM tblchat WHERE username = :username ORDER BY created_at ASC";
    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

public function sendMessage($username, $message, $sent_by) {
    $sql = "INSERT INTO tblchat (username, message, sent_by) 
            VALUES (:username, :message, :sent_by)";

    $stmt = $this->db->prepare($sql);
    $stmt->execute([
        ':username' => $username,
        ':message'  => $message,
        ':sent_by'  => $sent_by
    ]);
}
// Lấy danh sách user đã chat
public function getAllUsers() {
    $sql = "SELECT DISTINCT * FROM tblchat ORDER BY username ASC";
    $stmt = $this->db ->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

 // Lấy danh sách người dùng + tin nhắn gần nhất + phản hồi admin
   public function getAllLatestUserMessages() {
    $sql = "
        SELECT c.*
        FROM tblchat c
        INNER JOIN (
            SELECT username, MAX(id) AS last_id
            FROM tblchat
            GROUP BY username
        ) x ON c.id = x.last_id
        ORDER BY c.id DESC
    ";

    $stmt = $this->db->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}



    // Lưu tin nhắn phản hồi của admin
    public function replyMessage($username, $message) {
        // Thêm bản ghi mới vào tblchat
        $sql = "INSERT INTO tblchat (username, message, sent_by, created_at, last_admin_reply)
                VALUES (?, ?, 'admin', NOW(), ?)";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$username, $message, $message]);

        // Đồng thời cập nhật last_admin_reply cho tất cả bản ghi của user (để danh sách hiển thị đúng)
        $sql2 = "UPDATE tblchat SET last_admin_reply = ? WHERE username = ?";
        $stmt2 = $this->db->prepare($sql2);
        $stmt2->execute([$message, $username]);
    }
}
?>