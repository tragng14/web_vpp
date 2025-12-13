<?php
require_once "BaseModel.php";

class AdProductTypeModel extends BaseModel {
    private $table = "tblloaisp";

    // -------------------- THÊM LOẠI SẢN PHẨM --------------------
    public function insert($maLoaiSP, $tenLoaiSP, $moTaLoaiSP) {
        if (!array_key_exists($this->table, $this->primaryKeys)) {
            throw new Exception("Bảng không hợp lệ hoặc chưa được định nghĩa.");
        }

        $column = $this->primaryKeys[$this->table];
        if ($this->check($this->table, $column, $maLoaiSP) > 0) {
            echo "Mã loại sản phẩm đã tồn tại. Vui lòng chọn mã khác.";
            return;
        }

        $sql = "INSERT INTO tblloaisp 
                    (maLoaiSP, tenLoaiSP, moTaLoaiSP, ngayTao, ngaySua)
                VALUES 
                    (:maLoaiSP, :tenLoaiSP, :moTaLoaiSP, NOW(), NOW())";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':maLoaiSP', $maLoaiSP);
            $stmt->bindParam(':tenLoaiSP', $tenLoaiSP);
            $stmt->bindParam(':moTaLoaiSP', $moTaLoaiSP);
            $stmt->execute();
            echo "Thêm loại sản phẩm thành công.";
        } catch (PDOException $e) {
            echo "Thất bại: " . $e->getMessage();
        }
    }

    // -------------------- CẬP NHẬT LOẠI --------------------
    public function update($maLoaiSP, $tenLoaiSP, $moTaLoaiSP) {
        $sql = "UPDATE tblloaisp SET 
                    tenLoaiSP = :tenLoaiSP, 
                    moTaLoaiSP = :moTaLoaiSP,
                    ngaySua = NOW()
                WHERE maLoaiSP = :maLoaiSP";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':maLoaiSP', $maLoaiSP);
            $stmt->bindParam(':tenLoaiSP', $tenLoaiSP);
            $stmt->bindParam(':moTaLoaiSP', $moTaLoaiSP);
            $stmt->execute();
            echo "Cập nhật loại sản phẩm thành công.";
        } catch (PDOException $e) {
            echo "Cập nhật không thành công: " . $e->getMessage();
        }
    }

    // -------------------- LẤY DANH SÁCH --------------------
    public function getAll() {
        try {
            $sql = "SELECT maLoaiSP, tenLoaiSP, moTaLoaiSP, ngayTao, ngaySua
                    FROM $this->table
                    ORDER BY ngayTao DESC";

            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            echo "Lỗi khi lấy danh sách loại sản phẩm: " . $e->getMessage();
            return [];
        }
    }

public function searchByKeyword($keyword) {
    $sql = "SELECT * FROM tblloaisp 
            WHERE maLoaiSP LIKE :kw1
               OR tenLoaiSP LIKE :kw2";

    $stmt = $this->db->prepare($sql);
    $stmt->execute([
        ":kw1" => "%$keyword%",
        ":kw2" => "%$keyword%"
    ]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


    // -------------------- KIỂM TRA LOẠI CÓ SẢN PHẨM KHÔNG --------------------
    public function isCategoryInUse($maLoaiSP) {
        $sql = "SELECT COUNT(*) FROM tblsanpham WHERE maLoaiSP = :maLoaiSP";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(":maLoaiSP", $maLoaiSP);
        $stmt->execute();

        return $stmt->fetchColumn() > 0;
    }

    // -------------------- XÓA LOẠI --------------------
    public function delete($table, $id)
    {
        if ($table !== $this->table) {
            $_SESSION['delete_message'] = "Bảng không hợp lệ.";
            return false;
        }

        if ($this->isCategoryInUse($id)) {
            $_SESSION['delete_message'] = "❌ Không thể xóa! Loại sản phẩm này đang được sử dụng.";
            return false;
        }

        $sql = "DELETE FROM tblloaisp WHERE maLoaiSP = :id";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":id", $id);
            $stmt->execute();

            $_SESSION['delete_message'] = "🗑️ Xóa loại sản phẩm thành công.";
            return true;

        } catch (PDOException $e) {
            $_SESSION['delete_message'] = "❌ Xóa thất bại: " . $e->getMessage();
            return false;
        }
    }

}
