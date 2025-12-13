<?php
require_once "BaseModel.php";

class News extends BaseModel {
    private $table = "news";

    // 🟢 Thêm bài viết mới
   public function insert($title, $content, $image, $status) {

    date_default_timezone_set('Asia/Ho_Chi_Minh');
    $created_at = date("Y-m-d H:i:s");

    $sql = "INSERT INTO news (title, content, image, status, created_at)
            VALUES (:title, :content, :image, :status, :created_at)";
    
    try {
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':content', $content);
        $stmt->bindParam(':image', $image);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':created_at', $created_at);
        $stmt->execute();

        echo "✅ Thêm bài viết thành công.";
    } catch (PDOException $e) {
        echo "❌ Thất bại: " . $e->getMessage();
    }
}


    // 🟡 Cập nhật bài viết
  public function update($id, $title, $content, $image, $status) {

    date_default_timezone_set('Asia/Ho_Chi_Minh');
    $created_at = date("Y-m-d H:i:s");

    $sql = "UPDATE news SET 
                title = :title,
                content = :content,
                image = :image,
                status = :status,
                created_at = :created_at
            WHERE id = :id";
    
    try {
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':content', $content);
        $stmt->bindParam(':image', $image);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':created_at', $created_at);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        echo "✅ Cập nhật bài viết thành công.";
    } catch (PDOException $e) {
        echo "❌ Cập nhật không thành công: " . $e->getMessage();
    }
}


    // 🔵 Lấy toàn bộ bài viết
    public function all($tableName = null) {
        $table = $tableName ?? $this->table;
        try {
            $stmt = $this->db->query("SELECT * FROM $table ORDER BY id DESC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Lỗi khi lấy dữ liệu: " . $e->getMessage();
            return [];
        }
    }

    // 🔍 Tìm 1 bài viết theo ID
public function findVisible($table, $id) {
    $sql = "SELECT * FROM $table WHERE id = ? AND status = ?";
    $stmt = $this->db->prepare($sql);
    $stmt->execute([$id, 'hiển thị']); // hoặc [ $id, 1 ] nếu bạn dùng số
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

    // 🗑️ Xóa bài viết
    public function delete($tableName, $id) {
        $sql = "DELETE FROM $tableName WHERE id = :id";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            echo "✅ Xóa bài viết thành công.";
        } catch (PDOException $e) {
            echo "❌ Xóa không thành công: " . $e->getMessage();
        }
    }

    # 🔎 Tìm kiếm bài viết theo từ khóa
    public function search($keyword) {
    $sql = "SELECT * FROM news 
            WHERE title LIKE ? 
               OR content LIKE ? 
               OR status LIKE ? 
            ORDER BY created_at DESC";

    $stmt = $this->db->prepare($sql);
    $key = "%$keyword%";

    $stmt->execute([$key, $key, $key]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

}
?>