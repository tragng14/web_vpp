<?php
require_once "BaseModel.php";
class PageModel extends BaseModel
{
    protected $table = "pages";

    /* Lấy tất cả trang */
    public function allPages()
    {
        $sql = "SELECT * FROM pages ORDER BY created_at DESC";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    /* Lấy 1 trang theo ID */
    public function getById($id)
    {
        $sql = "SELECT * FROM pages WHERE page_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }



    /* Thêm trang */
    public function insertPage($title, $slug, $content, $status)
    {
        $sql = "INSERT INTO pages (title, slug, content, status) VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$title, $slug, $content, $status]);
    }

    /* Cập nhật trang */
    public function updatePage($id, $title, $slug, $content, $status)
    {
        $sql = "UPDATE pages SET 
                    title = ?, 
                    slug = ?, 
                    content = ?, 
                    status = ?
                WHERE page_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$title, $slug, $content, $status, $id]);
    }

    /* Xóa trang */
    public function deletePage($id)
    {
        $sql = "DELETE FROM pages WHERE page_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }

    /* Lấy trang theo slug (dùng bên ngoài website) */
public function getBySlug($slug) {
    $sql = "SELECT * FROM pages WHERE slug = :slug LIMIT 1";
    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(":slug", $slug);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}


#* Lấy tất cả trang đang hoạt động (dùng bên ngoài website) */
public function getAllActive()
{
    $sql = "SELECT * FROM pages WHERE status = 'active' ORDER BY title ASC";
    $stmt = $this->db->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

}

