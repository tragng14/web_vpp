<?php

require_once "BaseModel.php";
require_once "PromoModel.php"; // Dùng để gọi checkValidPromo() và incrementUsage()

class AdProducModel extends BaseModel {
    // Tên bảng chính
    private $table = "tblsanpham";

    // -------------------- CÁC HÀM SẢN PHẨM CƠ BẢN --------------------

    /**
     * Thêm sản phẩm.
     * Trả về mảng ['success'=>bool, 'message'=>string, 'insert_id'=>mixed]
     */
    public function insert($maLoaiSP, $masp, $tensp, $hinhanh, $soluong, $giaNhap, $giaXuat, $mota, $createDate, $promoCode = null) {
        if (!array_key_exists($this->table, $this->primaryKeys)) {
            return ['success' => false, 'message' => "Bảng không hợp lệ hoặc chưa được định nghĩa."];
        }

        $column = $this->primaryKeys[$this->table];

        // Kiểm tra mã sản phẩm trùng
        if ($this->check($this->table, $column, $masp) > 0) {
            return ['success' => false, 'message' => "Mã sản phẩm đã tồn tại. Vui lòng chọn mã khác."];
        }

        $sql = "INSERT INTO tblsanpham 
                (maLoaiSP, masp, tensp, hinhanh, soluong, soluongnhap, giaNhap, giaXuat, mota, createDate)
                VALUES 
                (:maLoaiSP, :masp, :tensp, :hinhanh, :soluong, :soluongnhap, :giaNhap, :giaXuat, :mota, :createDate)";

        try {
            $this->db->beginTransaction();

            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':maLoaiSP' => $maLoaiSP,
                ':masp' => $masp,
                ':tensp' => $tensp,
                ':hinhanh' => $hinhanh,
                ':soluong' => $soluong,
                ':soluongnhap' => $soluong,
                ':giaNhap' => $giaNhap,
                ':giaXuat' => $giaXuat,
                ':mota' => $mota,
                ':createDate' => $createDate
            ]);

            // Nếu có mã khuyến mãi -> lưu vào bảng promo_product (và tăng usage trong PromoModel)
            if (!empty($promoCode)) {
                $sqlPromo = "INSERT INTO promo_product (masp, promo_code, created_at)
                             VALUES (:masp, :promo_code, NOW())";
                $stmtPromo = $this->db->prepare($sqlPromo);
                $stmtPromo->execute([':masp' => $masp, ':promo_code' => $promoCode]);

                // Nếu PromoModel tồn tại, tăng lượt dùng (nếu cần)
                try {
                    $promoModel = new PromoModel();
                    if (method_exists($promoModel, 'incrementUsage')) {
                        $promoModel->incrementUsage($promoCode);
                    }
                } catch (Throwable $e) {
                    // Nếu tăng usage lỗi — rollback để tránh dữ liệu không nhất quán
                    if ($this->db->inTransaction()) $this->db->rollBack();
                    return ['success' => false, 'message' => "Lỗi khi áp khuyến mãi: " . $e->getMessage()];
                }
            }

            $this->db->commit();

            return ['success' => true, 'message' => "Thêm sản phẩm + khuyến mãi thành công.", 'insert_id' => $masp];

        } catch (PDOException $e) {
            if ($this->db->inTransaction()) $this->db->rollBack();
            return ['success' => false, 'message' => "Thêm thất bại: " . $e->getMessage()];
        } catch (Exception $e) {
            if ($this->db->inTransaction()) $this->db->rollBack();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Cập nhật sản phẩm (số lượng, thông tin...)
     * Trả về array ['success'=>bool,'message'=>string,'delta'=>int]
     */
    public function update($maLoaiSP, $masp, $tensp, $hinhanh, $soluongMoi, $giaNhap, $giaXuat, $mota, $createDate) {
        try {
            // Lấy dữ liệu cũ
            $checkSql = "SELECT soluong, soluongnhap FROM tblsanpham WHERE masp = :masp";
            $checkStmt = $this->db->prepare($checkSql);
            $checkStmt->execute([':masp' => $masp]);
            $oldData = $checkStmt->fetch(PDO::FETCH_ASSOC);

            if (!$oldData) {
                return ['success' => false, 'message' => "Không tìm thấy sản phẩm có mã '$masp'."];
            }

            $soluongCu = intval($oldData['soluong'] ?? 0);
            $soluongNhapCu = intval($oldData['soluongnhap'] ?? 0);

            // Tính phần chênh lệch
            $chenhLech = intval($soluongMoi) - $soluongCu;

            // Nếu chênh lệch dương => nhập thêm hàng; chênh lệch âm => giảm kho
            $newSLNhap = $soluongNhapCu + $chenhLech;
            if ($newSLNhap < 0) $newSLNhap = 0;

            // Tồn kho = số mới (theo tham số)
            $newSLTon = max(0, intval($soluongMoi));

            // Cập nhật DB
            $sql = "UPDATE tblsanpham SET 
                        maLoaiSP = :maLoaiSP,
                        tensp = :tensp,
                        hinhanh = :hinhanh,
                        soluongnhap = :soluongnhap,
                        soluong = :soluong,
                        giaNhap = :giaNhap,
                        giaXuat = :giaXuat,
                        mota = :mota,
                        createDate = :createDate
                    WHERE masp = :masp";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':maLoaiSP' => $maLoaiSP,
                ':tensp' => $tensp,
                ':hinhanh' => $hinhanh,
                ':soluongnhap' => $newSLNhap,
                ':soluong' => $newSLTon,
                ':giaNhap' => $giaNhap,
                ':giaXuat' => $giaXuat,
                ':mota' => $mota,
                ':createDate' => $createDate,
                ':masp' => $masp
            ]);

            return ['success' => true, 'message' => "Sửa số lượng thành công.", 'delta' => $chenhLech];

        } catch (PDOException $e) {
            return ['success' => false, 'message' => "Cập nhật không thành công: " . $e->getMessage()];
        }
    }

    // -------------------- PROMO FUNCTIONS --------------------

    /**
     * Áp dụng mã khuyến mãi cho sản phẩm (và tự động tăng số lượt dùng)
     * Trả về ['success'=>bool,'message'=>string]
     */
    public function addPromo($masp, $promo_code) {
        $promoModel = new PromoModel();

        try {
            // Lấy sản phẩm
            $stmt = $this->db->prepare("SELECT giaXuat FROM tblsanpham WHERE masp = :masp LIMIT 1");
            $stmt->execute([':masp' => $masp]);
            $product = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$product) {
                return ['success' => false, 'message' => "Không tìm thấy sản phẩm để áp mã."];
            }

            $price = floatval($product['giaXuat']);

            // Kiểm tra mã khuyến mãi hợp lệ
            if (!method_exists($promoModel, 'checkValidPromo') || !$promoModel->checkValidPromo($promo_code, $price)) {
                return ['success' => false, 'message' => "Mã khuyến mãi không hợp lệ hoặc đã hết hạn."];
            }

            // Kiểm tra xem sản phẩm đã có mã này chưa
            $check = $this->db->prepare("SELECT COUNT(*) FROM promo_product WHERE masp = :masp AND promo_code = :promo");
            $check->execute([':masp' => $masp, ':promo' => $promo_code]);
            if ($check->fetchColumn() > 0) {
                return ['success' => false, 'message' => "Mã này đã được áp dụng cho sản phẩm rồi."];
            }

            // Thực hiện trong transaction: insert + incrementUsage
            $this->db->beginTransaction();

            $sql = "INSERT INTO promo_product (masp, promo_code, created_at) VALUES (:masp, :promo, NOW())";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':masp' => $masp, ':promo' => $promo_code]);

            if (method_exists($promoModel, 'incrementUsage')) {
                $promoModel->incrementUsage($promo_code);
            }

            $this->db->commit();

            return ['success' => true, 'message' => "Áp dụng mã khuyến mãi cho sản phẩm thành công."];
        } catch (Exception $e) {
            if ($this->db->inTransaction()) $this->db->rollBack();
            return ['success' => false, 'message' => "Lỗi khi áp mã: " . $e->getMessage()];
        }
    }

    /**
     * Xóa tất cả mã khuyến mãi của sản phẩm
     */
    public function deletePromo($masp) {
        try {
            $sql = "DELETE FROM promo_product WHERE masp = :masp";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':masp' => $masp]);
            return ['success' => true, 'message' => "Đã xóa tất cả mã khuyến mãi khỏi sản phẩm."];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Lấy mã khuyến mãi còn hiệu lực của sản phẩm (hoặc null)
     * Trả về mảng promo hoặc null
     */
    public function getProductPromo($masp) {
        try {
            // 1) Tự động cập nhật các khuyến mãi đã hết hạn
            $update = "UPDATE promo_codes 
                       SET status = 'deleted'
                       WHERE end_date < NOW()
                         AND status = 'active'";
            $this->db->exec($update);
        
            // 2) Lấy thông tin khuyến mãi còn hiệu lực (nếu có) - lấy 1 khuyến mãi gần nhất
            $sql = "SELECT p2.code, p2.type, p2.value, p2.start_date, p2.end_date
                    FROM promo_product p1
                    JOIN promo_codes p2 ON p1.promo_code = p2.code
                    WHERE p1.masp = :masp
                      AND p2.status = 'active'
                      AND NOW() BETWEEN p2.start_date AND p2.end_date
                    ORDER BY p1.created_at DESC
                    LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':masp' => $masp]);
            $promo = $stmt->fetch(PDO::FETCH_ASSOC);
        
            if (!$promo) return null;
            return $promo;
        } catch (PDOException $e) {
            // Trong lỗi, trả về null — caller nên log nếu cần
            return null;
        }
    }

    /**
     * Cập nhật (thay thế) khuyến mãi cho sản phẩm
     */
    public function updateProductPromo($masp, $promo_code) {
        try {
            $this->db->beginTransaction();

            $del = $this->db->prepare("DELETE FROM promo_product WHERE masp = :masp");
            $del->execute([':masp' => $masp]);

            $sql = "INSERT INTO promo_product (masp, promo_code, created_at) VALUES (:masp, :promo_code, NOW())";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':masp' => $masp, ':promo_code' => $promo_code]);

            $this->db->commit();
            return ['success' => true];
        } catch (Exception $e) {
            if ($this->db->inTransaction()) $this->db->rollBack();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Xóa khuyến mãi khỏi sản phẩm
     */
    public function deleteProductPromo($masp) {
        return $this->deletePromo($masp);
    }

    // -------------------- GIẢM SỐ LƯỢNG TỒN KHO --------------------

    /**
     * Trừ tồn kho khi thanh toán (an toàn, dùng transaction và FOR UPDATE)
     * Trả về ['success'=>bool, 'message'=>string]
     */
    public function reduceStockAfterPayment($orderId) {
        $logPath = __DIR__ . '/../logs';
        if (!is_dir($logPath)) {
            @mkdir($logPath, 0777, true);
        }
        $logFile = $logPath . '/reduce_stock.log';
        file_put_contents($logFile, "[".date('Y-m-d H:i:s')."] reduceStock called for orderId: {$orderId}\n", FILE_APPEND);

        if (empty($orderId)) {
            file_put_contents($logFile, "[".date('Y-m-d H:i:s')."] Invalid orderId\n", FILE_APPEND);
            return ['success' => false, 'message' => 'Invalid orderId'];
        }

        try {
            $this->db->beginTransaction();

            // 1) Kiểm tra cờ đã trừ chưa
            $stmtCheckOrder = $this->db->prepare("SELECT id, stock_reduced FROM orders WHERE id = :oid LIMIT 1");
            $stmtCheckOrder->execute([':oid' => $orderId]);
            $orderRow = $stmtCheckOrder->fetch(PDO::FETCH_ASSOC);

            if (!$orderRow) {
                file_put_contents($logFile, "[".date('Y-m-d H:i:s')."] Order not found for id {$orderId}\n", FILE_APPEND);
                $this->db->rollBack();
                return ['success' => false, 'message' => 'Order not found'];
            }

            if (intval($orderRow['stock_reduced']) === 1) {
                file_put_contents($logFile, "[".date('Y-m-d H:i:s')."] Stock already reduced for order {$orderId}, skip.\n", FILE_APPEND);
                $this->db->rollBack();
                return ['success' => true, 'message' => 'Already reduced'];
            }

            // 2) Lấy danh sách sản phẩm trong order_details
            $stmtItems = $this->db->prepare("SELECT od.product_id AS masp, od.quantity 
                                             FROM order_details od
                                             WHERE od.order_id = :order_id");
            $stmtItems->execute([':order_id' => $orderId]);
            $items = $stmtItems->fetchAll(PDO::FETCH_ASSOC);

            if (empty($items)) {
                file_put_contents($logFile, "[".date('Y-m-d H:i:s')."] No items found for order {$orderId}\n", FILE_APPEND);
                $this->db->rollBack();
                return ['success' => false, 'message' => 'No items in order'];
            }

            foreach ($items as $it) {
                $masp = $it['masp'];
                $qty = intval($it['quantity']);
                if ($qty <= 0) continue;

                $stmtStock = $this->db->prepare("SELECT soluong FROM tblsanpham WHERE masp = :masp FOR UPDATE");
                $stmtStock->execute([':masp' => $masp]);
                $row = $stmtStock->fetch(PDO::FETCH_ASSOC);

                if (!$row) {
                    file_put_contents($logFile, "[".date('Y-m-d H:i:s')."] Product not found: {$masp}\n", FILE_APPEND);
                    $this->db->rollBack();
                    return ['success' => false, 'message' => "Product not found: $masp"];
                }

                $currentStock = intval($row['soluong']);
                $newStock = $currentStock - $qty;

                if ($newStock < 0) {
                    file_put_contents($logFile, "[".date('Y-m-d H:i:s')."] Insufficient stock for product {$masp}: current={$currentStock}, reduce={$qty}\n", FILE_APPEND);
                    $this->db->rollBack();
                    return ['success' => false, 'message' => "Insufficient stock for $masp"];
                }

                $stmtUpdate = $this->db->prepare("UPDATE tblsanpham SET soluong = :newstock WHERE masp = :masp");
                $stmtUpdate->execute([':newstock' => $newStock, ':masp' => $masp]);
                file_put_contents($logFile, "[".date('Y-m-d H:i:s')."] Product {$masp} stock updated: {$currentStock} -> {$newStock}\n", FILE_APPEND);
            }

            // 4) Đánh dấu order đã trừ stock
            $stmtFlag = $this->db->prepare("UPDATE orders SET stock_reduced = 1 WHERE id = :oid");
            $stmtFlag->execute([':oid' => $orderId]);

            $this->db->commit();
            file_put_contents($logFile, "[".date('Y-m-d H:i:s')."] reduceStock completed for order {$orderId}\n", FILE_APPEND);
            return ['success' => true];
        } catch (Exception $e) {
            if ($this->db->inTransaction()) $this->db->rollBack();
            file_put_contents($logFile, "[".date('Y-m-d H:i:s')."] Exception: " . $e->getMessage() . "\n", FILE_APPEND);
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    // -------------------- TĂNG SỐ LƯỢNG TỒN KHO KHI HỦY ĐƠN --------------------
    public function restoreStockAfterCancel($orderId) {
        $logPath = __DIR__ . '/../logs';
        if (!is_dir($logPath)) @mkdir($logPath, 0777, true);
        $logFile = $logPath . '/reduce_stock.log';
        file_put_contents($logFile, "[".date('Y-m-d H:i:s')."] restoreStock called for orderId: {$orderId}\n", FILE_APPEND);

        if (empty($orderId)) {
            file_put_contents($logFile, "[".date('Y-m-d H:i:s')."] Invalid orderId for restore\n", FILE_APPEND);
            return ['success' => false, 'message' => 'Invalid orderId'];
        }

        try {
            $this->db->beginTransaction();

            $stmt = $this->db->prepare("SELECT od.product_id AS masp, od.quantity 
                                        FROM order_details od
                                        WHERE od.order_id = :order_id");
            $stmt->execute([':order_id' => $orderId]);
            $orderItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (empty($orderItems)) {
                file_put_contents($logFile, "[".date('Y-m-d H:i:s')."] No items to restore for order {$orderId}\n", FILE_APPEND);
                $this->db->rollBack();
                return ['success' => false, 'message' => 'No items to restore'];
            }

            foreach ($orderItems as $it) {
                $masp = $it['masp'];
                $qty = intval($it['quantity']);
                if ($qty <= 0) continue;

                $stmtStock = $this->db->prepare("SELECT soluong FROM tblsanpham WHERE masp = :masp FOR UPDATE");
                $stmtStock->execute([':masp' => $masp]);
                $row = $stmtStock->fetch(PDO::FETCH_ASSOC);
                if (!$row) {
                    file_put_contents($logFile, "[".date('Y-m-d H:i:s')."] Product not found when restore: {$masp}\n", FILE_APPEND);
                    $this->db->rollBack();
                    return ['success' => false, 'message' => "Product not found: $masp"];
                }

                $current = intval($row['soluong']);
                $new = $current + $qty;

                $stmtUpdate = $this->db->prepare("UPDATE tblsanpham SET soluong = :newstock WHERE masp = :masp");
                $stmtUpdate->execute([':newstock' => $new, ':masp' => $masp]);
                file_put_contents($logFile, "[".date('Y-m-d H:i:s')."] Restored product {$masp}: {$current} -> {$new}\n", FILE_APPEND);
            }

            // Reset cờ để có thể xử lý lại nếu cần
            $stmtFlag = $this->db->prepare("UPDATE orders SET stock_reduced = 0 WHERE id = :oid");
            $stmtFlag->execute([':oid' => $orderId]);

            $this->db->commit();
            file_put_contents($logFile, "[".date('Y-m-d H:i:s')."] restoreStock completed for order {$orderId}\n", FILE_APPEND);
            return ['success' => true];
        } catch (Exception $e) {
            if ($this->db->inTransaction()) $this->db->rollBack();
            file_put_contents($logFile, "[".date('Y-m-d H:i:s')."] Exception restore: " . $e->getMessage() . "\n", FILE_APPEND);
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    // -------------------- LẤY SẢN PHẨM --------------------

    /**
     * Lấy 1 sản phẩm theo mã (toàn bộ cột)
     */
    public function getProductById($masp) {
        $sql = "SELECT * FROM tblsanpham WHERE masp = :masp";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':masp' => $masp]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy danh sách sản phẩm còn hàng (số lượng > 0)
     */
    public function getAvailableProducts($table = "tblsanpham") {
        $sql = "SELECT * FROM $table WHERE soluong > 0";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
/**
 * Lấy tất cả sản phẩm kèm tồn kho + khuyến mãi (nếu có)
 */
public function getAllProductsWithStock($table = "tblsanpham") {
    $sql = "
        SELECT 
            sp.masp,
            sp.tensp,
            sp.maLoaiSP,
            sp.soluongnhap,
            sp.soluong,
            sp.giaNhap,
            sp.giaXuat,
            sp.mota,
            sp.hinhanh,
            sp.createDate,
            pc.code AS promo_code,
            pc.type AS promo_type,
            pc.value AS promo_value
        FROM tblsanpham sp
        LEFT JOIN promo_product pp ON sp.masp = pp.masp
        LEFT JOIN promo_codes pc ON pp.promo_code = pc.code
        ORDER BY sp.createDate DESC
    ";

    $stmt = $this->db->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

    /**
     * Tìm kiếm sản phẩm theo từ khóa
     */
    public function search($keyword) {
        $sql = "SELECT * 
                FROM tblsanpham 
                WHERE masp LIKE ? 
                   OR tensp LIKE ? 
                   OR maLoaiSP LIKE ?
                   OR mota LIKE ?";

        $stmt = $this->db->prepare($sql);
        $key = "%$keyword%";
        $stmt->execute([$key, $key, $key, $key]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy sản phẩm kèm tên loại (dùng cho trang danh sách)
     */
    public function getProductsWithCategory() {
        $sql = "SELECT s.*, l.tenLoaiSP, l.moTaLoaiSP
                FROM tblsanpham s
                JOIN tblloaisp l ON s.maLoaiSP = l.maLoaiSP";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy thông tin sản phẩm kèm mã khuyến mãi (Nếu có) — TRẢ 1 ROW (assoc) hoặc NULL
     */
    public function getProductWithPromo($masp)
    {
        $sql = "
            SELECT 
                sp.masp,
                sp.tensp,
                sp.hinhanh,
                sp.giaXuat,
                pc.code AS promo_code,
                pc.type AS promo_type,
                pc.value AS promo_value
            FROM tblsanpham sp
            LEFT JOIN promo_product pp ON sp.masp = pp.masp
            LEFT JOIN promo_codes pc ON pp.promo_code = pc.code
            WHERE sp.masp = ?
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$masp]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    // --- THÊM HAI HÀM LỌC THEO SỐ SAO (GIỮ NGUYÊN CHỨC NĂNG KHÁC) ---

    /**
     * Lấy danh sách sản phẩm theo số sao trung bình tối thiểu
     * Trả về mảng các sản phẩm kèm avg_rating và count_rating
     *
     * @param int $minStars Số sao tối thiểu (0 = không lọc)
     * @param int $limit Số dòng trả về
     * @param int $offset Offset cho phân trang
     * @param array $filters Mảng lọc dạng ['maLoaiSP' => '...'] (chỉ chấp nhận whitelist)
     * @return array
     */
    public function getProductsByMinStars(int $minStars = 0, int $limit = 20, int $offset = 0, array $filters = []) {
        $allowedFilters = ['maLoaiSP', 'masp', 'tensp'];

        $extraWhere = '';
        $params = [];

        foreach ($filters as $col => $val) {
            if (in_array($col, $allowedFilters, true)) {
                $ph = ":f_" . $col;
                $extraWhere .= " AND s.`$col` = $ph";
                $params[$ph] = $val;
            }
        }

        // Ép kiểu an toàn cho limit/offset
        $limit = max(1, (int)$limit);
        $offset = max(0, (int)$offset);
        $minStars = max(0, min(5, (int)$minStars));

        $sql = "
        SELECT s.*,
               COALESCE(AVG(d.sao), 0) AS avg_rating,
               COUNT(d.id) AS count_rating
        FROM tblsanpham s
        LEFT JOIN tbl_danhgia d
          ON s.masp COLLATE utf8mb4_unicode_ci = d.masp COLLATE utf8mb4_unicode_ci AND d.trangthai = 1
        WHERE 1=1
          {$extraWhere}
        GROUP BY s.masp
        HAVING COALESCE(AVG(d.sao),0) >= :minStars
        ORDER BY avg_rating DESC, s.createDate DESC
        LIMIT {$limit} OFFSET {$offset}
        ";

        $stmt = $this->db->prepare($sql);

        // bind các tham số filter
        foreach ($params as $ph => $val) {
            $stmt->bindValue($ph, $val);
        }

        // bind minStars
        $stmt->bindValue(':minStars', $minStars, PDO::PARAM_INT);

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Đếm tổng số sản phẩm thỏa điều kiện số sao (dùng cho phân trang)
     *
     * @param int $minStars
     * @param array $filters
     * @return int
     */
    public function countProductsByMinStars(int $minStars = 0, array $filters = []) {
        $allowedFilters = ['maLoaiSP', 'masp', 'tensp'];

        $extraWhere = '';
        $params = [];

        foreach ($filters as $col => $val) {
            if (in_array($col, $allowedFilters, true)) {
                $ph = ":f_" . $col;
                $extraWhere .= " AND s.`$col` = $ph";
                $params[$ph] = $val;
            }
        }

        $minStars = max(0, min(5, (int)$minStars));

        // Đếm trên subquery để tính HAVING
        $sql = "
        SELECT COUNT(*) AS total FROM (
            SELECT s.masp, COALESCE(AVG(d.sao),0) AS avg_rating
            FROM tblsanpham s
            LEFT JOIN tbl_danhgia d
              ON s.masp COLLATE utf8mb4_unicode_ci = d.masp COLLATE utf8mb4_unicode_ci AND d.trangthai = 1
            WHERE 1=1
              {$extraWhere}
            GROUP BY s.masp
            HAVING COALESCE(AVG(d.sao),0) >= :minStars
        ) t
        ";

        $stmt = $this->db->prepare($sql);

        foreach ($params as $ph => $val) {
            $stmt->bindValue($ph, $val);
        }
        $stmt->bindValue(':minStars', $minStars, PDO::PARAM_INT);

        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return isset($row['total']) ? (int)$row['total'] : 0;
    }

    // -------------------- CÁC HÀM KHÁC --------------------

    /**
     * Lấy 1 sản phẩm theo mã (alias)
     */
    public function get($masp) {
        return $this->getProductById($masp);
    }

    public function getAll() {
        return $this->getAllProductsWithStock();
    }

    // Một số code cũ gọi all($table)
    public function all($table = null) {
        return $this->getAllProductsWithStock($table ?? $this->table);
    }

    // Hỗ trợ find("tblsanpham", $id) hoặc find($id)
    public function find($tableOrId, $id = null) {
        if ($id === null) $id = $tableOrId;
        return $this->getProductById($id);
    }






}
