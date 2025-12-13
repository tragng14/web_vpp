<?php
require_once "BaseModel.php";

class PromoModel extends BaseModel {
    private $table = "promo_codes";

    /**
     * =========================================
     * 1. Thêm mới mã khuyến mãi
     * =========================================
     */
    public function insert($code, $type, $value, $min_total, $usage_limit, $used_count, $start_date, $end_date, $status, $created_at) {
        // Kiểm tra trùng mã
        $checkSql = "SELECT COUNT(*) FROM {$this->table} WHERE code = :code";
        $checkStmt = $this->db->prepare($checkSql);
        $checkStmt->bindParam(':code', $code);
        $checkStmt->execute();
        if ($checkStmt->fetchColumn() > 0) {
            echo "❌ Mã khuyến mãi đã tồn tại, vui lòng chọn mã khác.";
            return false;
        }

        // Chuẩn hóa ngày
        $start_date = trim($start_date);
        $end_date   = trim($end_date);
        $created_at = trim($created_at);

        // Câu lệnh thêm
        $sql = "INSERT INTO {$this->table} 
                (code, type, value, min_total, usage_limit, used_count, start_date, end_date, status, created_at)
                VALUES 
                (:code, :type, :value, :min_total, :usage_limit, :used_count, :start_date, :end_date, :status, :created_at)";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':code', $code);
            $stmt->bindParam(':type', $type);
            $stmt->bindParam(':value', $value);
            $stmt->bindParam(':min_total', $min_total);
            $stmt->bindParam(':usage_limit', $usage_limit);
            $stmt->bindParam(':used_count', $used_count);
            $stmt->bindParam(':start_date', $start_date);
            $stmt->bindParam(':end_date', $end_date);
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':created_at', $created_at);
            $stmt->execute();

            return true;
        } catch (PDOException $e) {
            echo "❌ Lỗi khi thêm mã khuyến mãi: " . $e->getMessage();
            return false;
        }
    }

    /**
     * =========================================
     * 2. Cập nhật mã khuyến mãi
     * =========================================
     */
    public function update($code, $type, $value, $min_total, $usage_limit, $used_count, $start_date, $end_date, $status, $created_at) {
        $sql = "UPDATE {$this->table} SET 
                    type = :type,
                    value = :value,
                    min_total = :min_total,
                    usage_limit = :usage_limit,
                    used_count = :used_count,
                    start_date = :start_date,
                    end_date = :end_date,
                    status = :status,
                    created_at = :created_at
                WHERE code = :code";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':code', $code);
            $stmt->bindParam(':type', $type);
            $stmt->bindParam(':value', $value);
            $stmt->bindParam(':min_total', $min_total);
            $stmt->bindParam(':usage_limit', $usage_limit);
            $stmt->bindParam(':used_count', $used_count);
            $stmt->bindParam(':start_date', $start_date);
            $stmt->bindParam(':end_date', $end_date);
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':created_at', $created_at);
            $stmt->execute();

            return true;
        } catch (PDOException $e) {
            echo "❌ Cập nhật không thành công: " . $e->getMessage();
            return false;
        }
    }

    /**
     * =========================================
     * 3. Lấy toàn bộ danh sách mã khuyến mãi
     * =========================================
     */
    public function getAll($tableName = null) {
        $sql = "SELECT * FROM {$this->table} ORDER BY created_at DESC";
        try {
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "❌ Lỗi lấy danh sách: " . $e->getMessage();
            return [];
        }
    }

    /**
     * =========================================
     * 4. Tìm mã khuyến mãi theo code
     * =========================================
     */
    public function find($tableName, $code) {
        $sql = "SELECT * FROM {$this->table} WHERE code = :code LIMIT 1";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':code', $code);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "❌ Lỗi tìm mã khuyến mãi: " . $e->getMessage();
            return null;
        }
    }

    /**
     * =========================================
     * 5. Xóa mềm (soft delete)
     * =========================================
     */
    public function deletecode($tableName, $code) {
        $sql = "UPDATE {$this->table} SET status = 'deleted' WHERE code = :code";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':code', $code);
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            echo "❌ Xóa thất bại: " . $e->getMessage();
            return false;
        }
    }

    /**
     * =========================================
     * 6. Kiểm tra mã khuyến mãi hợp lệ
     * =========================================
     */
    public function checkValidPromo($code, $totalAmount) {
        $sql = "SELECT * FROM {$this->table} 
                WHERE code = :code 
                  AND status = 'active'
                  AND (usage_limit IS NULL OR used_count < usage_limit)
                  AND start_date <= CURDATE()
                  AND end_date >= CURDATE()
                  AND min_total <= :totalAmount
                LIMIT 1";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':code', $code);
            $stmt->bindParam(':totalAmount', $totalAmount);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "❌ Lỗi kiểm tra mã: " . $e->getMessage();
            return false;
        }
    }

   /**
 * =========================================
 * 7. Tăng số lần sử dụng mã khuyến mãi
 * =========================================
 */
public function incrementUsage($code) {
    try {
        $sqlCheck = "SELECT used_count, usage_limit FROM {$this->table} WHERE code = :code";
        $stmt = $this->db->prepare($sqlCheck);
        $stmt->bindParam(':code', $code);
        $stmt->execute();
        $promo = $stmt->fetch(PDO::FETCH_ASSOC);

        // ❌ Không tồn tại
        if (!$promo) return false;

        // ⚠️ Nếu có giới hạn (usage_limit > 0)
        if ($promo['usage_limit'] > 0 && $promo['used_count'] >= $promo['usage_limit']) {
            return false; // đạt giới hạn
        }

        // ✅ Tăng lượt dùng
        $sql = "UPDATE {$this->table}
                SET used_count = used_count + 1
                WHERE code = :code";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':code', $code);
        $stmt->execute();

        return true;
    } catch (PDOException $e) {
        echo "❌ Lỗi tăng lượt dùng mã KM: " . $e->getMessage();
        return false;
    }
}


/**
 * =========================================
 * 8. Giảm số lần sử dụng mã khuyến mãi
 * =========================================
 */
public function decrementUsage($code) {
    try {
        // ✅ Chỉ giảm nếu used_count > 0
        $sql = "UPDATE {$this->table}
                SET used_count = CASE 
                    WHEN used_count > 0 THEN used_count - 1 
                    ELSE 0 
                END
                WHERE code = :code";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':code', $code);
        $stmt->execute();
        return true;
    } catch (PDOException $e) {
        echo "❌ Giảm số lần dùng thất bại: " . $e->getMessage();
        return false;
    }
}

    /**
     * =========================================
     * 9. Tự động cập nhật trạng thái mã hết hạn / kích hoạt lại
     * =========================================
     */

     public function autoUpdateExpiredPromos() {
        try {
            // 1️⃣ Tự động chuyển "active" sang "deleted" nếu đã hết hạn
            $sqlExpire = "UPDATE {$this->table}
                          SET status = 'deleted'
                          WHERE end_date < NOW()
                            AND status = 'active'";
            $this->db->exec($sqlExpire);
    
            // 2️⃣ Chỉ tự động kích hoạt lại những mã bị 'deleted' do hết hạn,
            // ⚠️ KHÔNG tự bật lại các mã admin đã tạm ngưng (inactive)
            $sqlReactivate = "UPDATE {$this->table}
                              SET status = 'active'
                              WHERE start_date <= NOW()
                                AND end_date >= NOW()
                                AND status = 'deleted'";
            $this->db->exec($sqlReactivate);
    
        } catch (PDOException $e) {
            echo "❌ Lỗi tự động cập nhật trạng thái khuyến mãi: " . $e->getMessage();
        }
    }
    
    
    

    /**
     * 10. Lấy mã khuyến mãi còn hiệu lực theo code
     * Trả về mảng promo nếu còn active và trong khoảng thời gian, ngược lại trả về false
     */
    public function getValidPromoByCode($code) {
        $sql = "SELECT code, type, value, start_date, end_date, status
                FROM {$this->table}
                WHERE code = :code
                AND status = 'active'
                AND NOW() BETWEEN start_date AND end_date
                LIMIT 1";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':code', $code);
            $stmt->execute();
            $promo = $stmt->fetch(PDO::FETCH_ASSOC);
            return $promo ? $promo : false;
        } catch (PDOException $e) {
            // Nếu cần debug tạm thời có thể echo, nhưng production nên log
            // echo "Lỗi getValidPromoByCode: " . $e->getMessage();
            return false;
        }
    }

    public function getPromoByCode($code) {
        $query = "SELECT * FROM promo_codes WHERE code = :code AND NOW() BETWEEN start_date AND end_date";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':code' => $code]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function getByCode($code) {
    $sql = "SELECT * FROM {$this->table} WHERE code = :code LIMIT 1";
    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(':code', $code);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

public function getPromoByProduct($code)
{
    $sql = "
        SELECT 
            id,
            code,
            type,
            value,
            min_total,
            usage_limit,
            used_count,
            start_date,
            end_date,
            status
        FROM promo_codes
        WHERE code = ?
        AND status = 'active'
        LIMIT 1
    ";

    $result = $this->select($sql, [$code]);
    return !empty($result) ? $result[0] : null;
}
# tìm kiếm mã khuyến mãi theo từ khóa
// Hàm bỏ dấu tiếng Việt
private function vn_no_accent($str) {
    $str = mb_strtolower($str, 'UTF-8');
    $accents = [
        'a'=>'áàảãạâấầẩẫậăắằẳẵặ',
        'e'=>'éèẻẽẹêếềểễệ',
        'i'=>'íìỉĩị',
        'o'=>'óòỏõọôốồổỗộơớờởỡợ',
        'u'=>'úùủũụưứừửữự',
        'y'=>'ýỳỷỹỵ',
        'd'=>'đ'
    ];
    foreach ($accents as $nonAccent => $accentGroup) {
        $str = str_replace(mb_str_split($accentGroup), $nonAccent, $str);
    }
    return $str;
}

public function search($keyword)
{
    $keywordRaw = trim(mb_strtolower($keyword));
    $keywordND  = $this->vn_no_accent($keywordRaw);

    // Map không dấu
    $statusMapND = [
        "dang" => "active",
        "hoat" => "active",
        "active" => "active",

        "tam" => "inactive",
        "ngung" => "inactive",
        "inactive" => "inactive",

        "het" => "deleted",
        "han" => "deleted",
        "hethang" => "deleted",
        "deleted" => "deleted",
    ];

    $typeMapND = [
        "phan" => "percent",
        "tram" => "percent",
        "percent" => "percent",

        "tien" => "amount",
        "vnd" => "amount",
        "amount" => "amount",

        "dinh" => "fixed",
        "codinh" => "fixed",
        "fixed" => "fixed",
    ];

    $searchStatus = $statusMapND[$keywordND] ?? null;
    $searchType   = $typeMapND[$keywordND] ?? null;

    $sql = "SELECT *
            FROM promo_codes
            WHERE code LIKE ?
            OR type LIKE ?
            OR status LIKE ?
            OR (type = ?)
            OR (status = ?)
            ORDER BY created_at DESC";

    return $this->query(
        $sql,
        [
            "%$keywordRaw%",
            "%$keywordRaw%",
            "%$keywordRaw%",
            $searchType,
            $searchStatus
        ]
    )->fetchAll();
}

public function filterAdvanced($type, $status, $date)
{
    $sql = "SELECT * FROM promo_codes WHERE 1"; 
    $params = [];

    // Lọc theo loại khuyến mãi
    if (!empty($type)) {
        $sql .= " AND type = ?";
        $params[] = $type;
    }

    // Lọc theo trạng thái
    if (!empty($status)) {
        $sql .= " AND status = ?";
        $params[] = $status;
    }

    // Lọc theo ngày (ngày nằm giữa start_date và end_date)
    if (!empty($date)) {
        $sql .= " AND start_date <= ? AND end_date >= ?";
        $params[] = $date;
        $params[] = $date;
    }

    $sql .= " ORDER BY created_at DESC";

    return $this->query($sql, $params)->fetchAll();
}

public function saveProductPromo($masp, $promoCode) {
    $sql = "INSERT INTO promo_product (masp, promo_code, created_at) 
            VALUES (:masp, :promo_code, NOW())";

    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(':masp', $masp);
    $stmt->bindParam(':promo_code', $promoCode);
    return $stmt->execute();
}

}

?>
