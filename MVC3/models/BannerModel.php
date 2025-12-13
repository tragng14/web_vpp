<?php
require_once "BaseModel.php";

class BannerModel extends BaseModel
{
    private $tableSet = "banner_sets";     // bảng nhóm banner
    private $tableImg = "banner_images";   // bảng ảnh trong nhóm banner

    /* ============================================================
        🔵 LẤY TOÀN BỘ BANNER SET
    ============================================================ */
    public function allSets() {
        try {
            $stmt = $this->db->query("SELECT * FROM {$this->tableSet} ORDER BY banner_id DESC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    /* ============================================================
        🔍 LẤY 1 BANNER SET THEO ID
    ============================================================ */
    public function findSet($id) {
        $sql = "SELECT * FROM {$this->tableSet} WHERE banner_id = :id";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":id", $id);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return null;
        }
    }

    /* ============================================================
        🔵 LẤY DANH SÁCH ẢNH CỦA 1 BANNER SET
    ============================================================ */
    public function getImages($banner_id) {
        $sql = "SELECT * FROM {$this->tableImg} 
                WHERE banner_id = :id
                ORDER BY sort_order ASC";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":id", $banner_id);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    /* ============================================================
        🟢 THÊM BANNER SET
    ============================================================ */
    public function insertSet($title, $description, $status, $created_at) 
    {
        $sql = "INSERT INTO {$this->tableSet} 
                (title, description, status, created_at)
                VALUES (:title, :description, :status, :created_at)";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":title", $title);
            $stmt->bindParam(":description", $description);
            $stmt->bindParam(":status", $status);
            $stmt->bindParam(":created_at", $created_at);
            $stmt->execute();

            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            return false;
        }
    }

    /* ============================================================
        🟡 CẬP NHẬT BANNER SET
    ============================================================ */
    public function updateSet($id, $title, $description, $status, $created_at)
    {
        $sql = "UPDATE {$this->tableSet}
                SET title = :title,
                    description = :description,
                    status = :status,
                    created_at = :created_at
                WHERE banner_id = :id";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":title", $title);
            $stmt->bindParam(":description", $description);
            $stmt->bindParam(":status", $status);
            $stmt->bindParam(":created_at", $created_at);
            $stmt->bindParam(":id", $id);

            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

    /* ============================================================
        🟢 THÊM ẢNH CHO BANNER
    ============================================================ */
    public function insertImage($banner_id, $image_path, $link, $sort_order)
    {
        $sql = "INSERT INTO {$this->tableImg}
                (banner_id, image_path, link, sort_order, created_at)
                VALUES (:banner_id, :image_path, :link, :sort_order, NOW())";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":banner_id", $banner_id);
            $stmt->bindParam(":image_path", $image_path);
            $stmt->bindParam(":link", $link);
            $stmt->bindParam(":sort_order", $sort_order);

            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

    /* ============================================================
        🔴 XOÁ ẢNH
    ============================================================ */
    public function deleteImage($img_id)
    {
        $sql = "DELETE FROM {$this->tableImg} WHERE img_id = :id";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":id", $img_id);
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

    /* ============================================================
        🔴 XOÁ BANNER SET (CASCADE XÓA ẢNH)
    ============================================================ */
    public function deleteSet($banner_id)
    {
        $sql = "DELETE FROM {$this->tableSet} WHERE banner_id = :id";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":id", $banner_id);
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

    /* ============================================================
        🔎 TÌM KIẾM BANNER SET
    ============================================================ */
    public function search($keyword)
    {
        $sql = "SELECT * FROM {$this->tableSet}
                WHERE title LIKE ? OR description LIKE ? OR status LIKE ?
                ORDER BY created_at DESC";

        try {
            $stmt = $this->db->prepare($sql);
            $key = "%$keyword%";
            $stmt->execute([$key, $key, $key]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            return [];
        }
    }

public function updateImage($img_id, $link, $sort)
{
    $sql = "UPDATE banner_images SET link=?, sort_order=? WHERE img_id=?";
    $stm = $this->db->prepare($sql);
    return $stm->execute([$link, $sort, $img_id]);
}

## LẤY TOÀN BỘ BANNER ĐANG KÍCH HOẠT

public function getActiveBanners() {
    $sql = "SELECT b.banner_id, b.title, bi.image_path, bi.link, bi.sort_order
            FROM banner_sets b
            JOIN banner_images bi ON b.banner_id = bi.banner_id
            WHERE b.status = 'active'
            ORDER BY bi.sort_order ASC";

    return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
}


}
?>
