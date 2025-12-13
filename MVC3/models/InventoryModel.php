<?php
require_once "BaseModel.php";

class InventoryModel extends BaseModel
{
    private $tableProduct = "tblsanpham";

   public function getAllProducts($keyword = '', $category = '')
{
    $sql = "SELECT * FROM {$this->tableProduct} WHERE 1=1";

    if (!empty($keyword)) {
        $sql .= " AND (LOWER(masp) LIKE :kw1 OR LOWER(tensp) LIKE :kw2)";
    }

    if ($category !== null && trim($category) !== '') {
        $sql .= " AND maLoaiSP = :cat";
    }

    $stmt = $this->db->prepare($sql);

    if (!empty($keyword)) {
        $kw = '%' . strtolower($keyword) . '%';
        $stmt->bindValue(':kw1', $kw, PDO::PARAM_STR);
        $stmt->bindValue(':kw2', $kw, PDO::PARAM_STR);
    }

    if ($category !== null && trim($category) !== '') {
        $stmt->bindValue(':cat', $category, PDO::PARAM_STR);
    }

    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

}
?>
