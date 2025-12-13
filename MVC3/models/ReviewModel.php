<?php
require_once "BaseModel.php";

class ReviewModel extends BaseModel {
    private $table = "tbl_danhgia";

    // ✅ Thêm đánh giá mới// ✅ Thêm đánh giá mới (mặc định chờ duyệt)
public function insert($masp, $tenNguoiDung, $email, $noidung, $sao, $trangthai = 0) {
    $sql = "INSERT INTO $this->table (masp, tenNguoiDung, email, noidung, sao, trangthai, ngayDang)
            VALUES (:masp, :tenNguoiDung, :email, :noidung, :sao, :trangthai, NOW())";
    try {
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':masp', $masp);
        $stmt->bindParam(':tenNguoiDung', $tenNguoiDung);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':noidung', $noidung);
        $stmt->bindParam(':sao', $sao);
        $stmt->bindParam(':trangthai', $trangthai);
        return $stmt->execute();
    } catch (PDOException $e) {
        return false;
    }
}


    // ✅ Cập nhật đánh giá
   // ✅ Cập nhật đánh giá (bao gồm phản hồi admin)
public function update($id, $noidung, $sao, $trangthai, $traloi = null) {
    $sql = "UPDATE $this->table 
            SET noidung = :noidung, sao = :sao, trangthai = :trangthai, traloi = :traloi
            WHERE id = :id";
    try {
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':noidung', $noidung);
        $stmt->bindParam(':sao', $sao);
        $stmt->bindParam(':trangthai', $trangthai);
        $stmt->bindParam(':traloi', $traloi);
        return $stmt->execute();
    } catch (PDOException $e) {
        return false;
    }
}
// ✅ Cập nhật phản hồi của admin
public function reply($id, $traloi) {
    $sql = "UPDATE $this->table SET traloi = :traloi WHERE id = :id";
    try {
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':traloi', $traloi);
        return $stmt->execute();
    } catch (PDOException $e) {
        return false;
    }
}




    // ✅ Lấy tất cả đánh giá (VD: hiển thị trong admin)
    public function getAll() {
        $sql = "SELECT id, masp, tenNguoiDung, email, noidung, sao, trangthai, ngayDang, traloi 
                FROM danhgia 
                ORDER BY ngayDang DESC";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
    

    // ✅ Lấy đánh giá theo mã sản phẩm
    public function getByProduct($masp, $onlyActive = true) {
        try {
            $sql = "SELECT * FROM $this->table WHERE masp = :masp";
            if ($onlyActive) $sql .= " AND trangthai = 1";
            $sql .= " ORDER BY ngayDang DESC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':masp', $masp);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

   

    // ✅ Tính điểm trung bình và số lượng đánh giá của 1 sản phẩm
    public function getAvgRating($masp) {
        try {
            $sql = "SELECT AVG(sao) AS avg, COUNT(*) AS count 
                    FROM $this->table 
                    WHERE masp = :masp AND trangthai = 1";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':masp', $masp);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ?: ['avg' => 0, 'count' => 0];
        } catch (PDOException $e) {
            return ['avg' => 0, 'count' => 0];
        }
    }
    public function findById($id) {
        $sql = "SELECT * FROM tbl_danhgia WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function saveReply($id, $reply) {
        $sql = "UPDATE tbl_danhgia SET traloi = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$reply, $id]);
    }
    #   ✅ Lấy danh sách đánh giá với bộ lọc
    public function getFilteredReviews($keyword = '', $stars = '', $status = '') {
        $sql = "SELECT * FROM $this->table WHERE 1=1";
        $params = [];
    
        // Lọc theo mã sản phẩm
        if (!empty($keyword)) {
            $sql .= " AND masp LIKE :keyword";
            $params[':keyword'] = "%$keyword%";
        }
    
        // Lọc theo số sao
        if (!empty($stars)) {
            $sql .= " AND sao = :sao";
            $params[':sao'] = $stars;
        }
    
        // Lọc theo trạng thái
        if ($status !== '' && $status !== null) {
            $sql .= " AND trangthai = :trangthai";
            $params[':trangthai'] = $status;
        }
    
        $sql .= " ORDER BY ngayDang DESC";
    
        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
    
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getRatingSummaryByProduct($masp)
{
    $sql = "
        SELECT 
            COUNT(*) AS ratingCount,
            +
            
            ROUND(AVG(sao), 1) AS avgRating
        FROM reviews
        WHERE masp = :masp
    ";

    $stmt = $this->db->prepare($sql);
    $stmt->execute(['masp' => $masp]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

    
}
