<?php
require_once __DIR__ . "/../vendor/autoload.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once 'BaseModel.php';
require_once __DIR__ . '/../app/DB.php';

class OrderModel extends BaseModel {
    // Tên bảng chính
    protected $table = 'orders';

    /**
     * Lấy chi tiết đơn hàng theo order_id
     * @param int $orderId
     * @return array
     */
    public function getOrderDetailsByOrderId($orderId) {
        $sql = "SELECT * FROM order_details WHERE order_id = ?";
        return $this->select($sql, [$orderId]);
    }

    /**
     * Lấy danh sách đơn của 1 user (theo user_id)
     * @param int $userId
     * @return array
     */
    public function getOrdersByUser($userId) {
        $sql = "SELECT * FROM {$this->table} WHERE user_id = ? ORDER BY created_at DESC";
        return $this->select($sql, [$userId]);
    }

    /**
     * Lấy danh sách đơn theo email
     * @param string $email
     * @return array
     */
    public function getOrdersByEmail($email) {
        $sql = "SELECT * FROM {$this->table} WHERE user_email = ? ORDER BY created_at DESC";
        return $this->select($sql, [$email]);
    }

    /**
     * Lấy thông tin 1 đơn hàng theo id
     * @param int $id
     * @return array|null
     */
    public function getOrderById($id) {
        $sql = "SELECT * FROM {$this->table} WHERE id = ?";
        $result = $this->select($sql, [$id]);
        return !empty($result) ? $result[0] : null;
    }

    /**
     * Lấy thông tin sản phẩm kèm thông tin khuyến mãi (nếu có) — dùng trong add-to-cart khi cần
     * @param string $masp
     * @return array
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
        ";

        return $this->select($sql, [$masp]);
    }

    /**
     * Lấy đơn hàng theo mã đơn (order_code)
     * @param string $orderCode
     * @return array|null
     */
    public function getOrderByCode($orderCode) {
        $sql = "SELECT * FROM {$this->table} WHERE order_code = ?";
        $result = $this->select($sql, [$orderCode]);
        return !empty($result) ? $result[0] : null;
    }

    /**
     * Cập nhật trạng thái đơn hàng
     * @param int $orderId
     * @param string $status
     * @return void
     */
    public function updateStatus($orderId, $status) {
        $sql = "UPDATE {$this->table} SET status = :status WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':status' => $status, ':id' => $orderId]);
    }

    /**
     * Hủy đơn và gán cancelled_by
     * @param int $orderId
     * @param string $cancelledBy ('admin'|'user')
     * @return void
     */
    public function cancelOrder($orderId, $cancelledBy) {
        $sql = "UPDATE {$this->table} SET status = 'cancelled', cancelled_by = :cb WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':cb' => $cancelledBy,
            ':id' => $orderId
        ]);
    }

    /**
     * Tạo order kèm thông tin giao hàng
     * @return int last insert id
     */
    public function createOrderWithShipping(
        $userId,
        $userEmail,
        $orderCode,
        $totalAmount,
        $discountCode,
        $receiver,
        $phone,
        $address,
        $transaction_info = 'chothanhtoan',
        $shipping_method = 'giao_hang',
        $shipping_fee = 0
    ) {
        // Trạng thái tự động theo transaction_info
        $status = ($transaction_info === 'dathanhtoan') ? 'approved' : 'pending';

        $sql = "INSERT INTO {$this->table} (
            user_id,
            user_email,
            order_code,
            total_amount,
            discount_code,
            receiver,
            phone,
            address,
            transaction_info,
            shipping_method,
            shipping_fee,
            status,
            created_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";

        $this->query($sql, [
            $userId,
            $userEmail,
            $orderCode,
            $totalAmount,
            $discountCode,
            $receiver,
            $phone,
            $address,
            $transaction_info,
            $shipping_method,
            $shipping_fee,
            $status
        ]);

        return $this->getLastInsertId();
    }

    /**
     * Lấy tất cả đơn (có thể dùng filter keyword)
     * @param string $keyword
     * @return array
     */
    public function getAllOrders($keyword = '') {
        $sql = "SELECT * FROM {$this->table}";
        if (!empty($keyword)) {
            $sql .= " WHERE order_code LIKE :kw OR receiver LIKE :kw";
        }
        $sql .= " ORDER BY created_at DESC";
        $stmt = $this->db->prepare($sql);
        if (!empty($keyword)) {
            $stmt->bindValue(':kw', "%$keyword%");
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy đơn hàng kèm tổng cuối (tính promo toàn đơn + promo theo sản phẩm nếu có)
     * Trả về mảng order có thêm keys: original_total, discount_amount, final_total, details
     * @param int $orderId
     * @return array|null
     */
    public function getOrderWithFinalTotal($orderId) {
        // 1. Lấy order
        $order = $this->getOrderById($orderId);
        if (!$order) return null;

        // 2. Lấy chi tiết
        $details = $this->getOrderDetailsByOrderId($orderId);

        // 3. Tính tổng gốc
        $originalTotal = 0;
        foreach ($details as $item) {
            $lineTotal = isset($item['total']) ? (float)$item['total'] : ((float)$item['price'] * (int)$item['quantity']);
            $originalTotal += $lineTotal;
        }

        // 4. Tính giảm giá
        require_once __DIR__ . '/../models/PromoModel.php';
        $promoModel = new PromoModel();

        $discountAmount = 0.0;

        // 5A. Giảm theo mã toàn đơn (discount_code trong orders)
        if (!empty($order['discount_code'])) {
            $promo = $promoModel->getPromoByCode($order['discount_code']);
            if ($promo && ($promo['status'] === 'active' || $promo['status'] === 1)) {
                // Kiểm tra min_total nếu có
                if (empty($promo['min_total']) || $originalTotal >= (float)$promo['min_total']) {
                    if ($promo['type'] === 'percent') {
                        $discountAmount = $originalTotal * ((float)$promo['value'] / 100.0);
                    } else { // amount | fixed
                        $discountAmount = (float)$promo['value'];
                    }
                }
            }
        }

        // 5B. Giảm theo từng sản phẩm (nếu có cấu hình promo_product)
        foreach ($details as $item) {
            if (!empty($item['masp'])) {
                $promoForProduct = $promoModel->getPromoByProduct($item['masp']);
                if ($promoForProduct && ($promoForProduct['status'] === 'active' || $promoForProduct['status'] === 1)) {
                    $lineTotal = isset($item['total']) ? (float)$item['total'] : ((float)$item['price'] * (int)$item['quantity']);
                    if ($promoForProduct['type'] === 'percent') {
                        $discountAmount += $lineTotal * ((float)$promoForProduct['value'] / 100.0);
                    } else { // amount/fixed: áp dụng cho mỗi item theo quantity
                        $discountAmount += (float)$promoForProduct['value'] * ((int)$item['quantity']);
                    }
                }
            }
        }

        // 6. Không cho discount lớn hơn tổng
        if ($discountAmount > $originalTotal) $discountAmount = $originalTotal;

        // 7. Kết quả
        $finalTotal = $originalTotal - $discountAmount;
        if ($finalTotal < 0) $finalTotal = 0;

        $order['original_total'] = $originalTotal;
        $order['discount_amount'] = $discountAmount;
        $order['final_total'] = $finalTotal;
        $order['details'] = $details;

        return $order;
    }

    /**
     * Hoàn trả tồn kho khi huỷ đơn
     * Tăng soluong trong tblsanpham theo quantity đã mua
     *
     * @param int $orderId
     * @return bool
     */
    public function restoreStockAfterCancel($orderId) {
        try {
            $sql = "SELECT product_id AS masp, quantity 
                    FROM order_details
                    WHERE order_id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':id' => $orderId]);
            $orderItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (empty($orderItems)) {
                return false;
            }

            foreach ($orderItems as $item) {
                $update = "UPDATE tblsanpham
                           SET soluong = soluong + :qty
                           WHERE masp = :masp";
                $stmt2 = $this->db->prepare($update);
                $stmt2->execute([
                    ':qty' => $item['quantity'],
                    ':masp' => $item['masp']
                ]);
            }

            return true;
        } catch (PDOException $e) {
            // KHÔNG echo lỗi ở model — log để debug
            error_log("OrderModel::restoreStockAfterCancel error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Đánh dấu đã thanh toán (mark as paid) và trừ kho nếu chưa trừ
     * @param int $orderId
     * @return bool
     */
    public function markAsPaid($orderId) {
        try {
            $sql = "SELECT transaction_info, stock_reduced FROM {$this->table} WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':id' => $orderId]);
            $order = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$order) {
                return false;
            }

            // Nếu đã ghi nhận thanh toán trước đó, trả về true
            if (isset($order['transaction_info']) && $order['transaction_info'] === 'dathanhtoan') {
                return true;
            }

            // Cập nhật transaction_info + status
            $updateSql = "UPDATE {$this->table} 
                          SET transaction_info = 'dathanhtoan',
                              status = 'approved'
                          WHERE id = :id";
            $stmt2 = $this->db->prepare($updateSql);
            $stmt2->execute([':id' => $orderId]);

            // Nếu chưa trừ kho, gọi model sản phẩm để trừ
            if (empty($order['stock_reduced']) || $order['stock_reduced'] == 0) {
                require_once __DIR__ . '/../models/AdProducModel.php';
                $productModel = new AdProducModel();
                // Giả định AdProducModel::reduceStockAfterPayment($orderId) tồn tại
                if (method_exists($productModel, 'reduceStockAfterPayment')) {
                    $productModel->reduceStockAfterPayment($orderId);
                }

                // Ghi lại flag đã trừ kho
                $stmt3 = $this->db->prepare("UPDATE {$this->table} SET stock_reduced = 1 WHERE id = :id");
                $stmt3->execute([':id' => $orderId]);
            }

            return true;
        } catch (Exception $e) {
            error_log("OrderModel::markAsPaid error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Hàm trợ giúp: chuyển chuỗi tiếng Việt có dấu -> không dấu (dùng cho tìm kiếm)
     * @param string $str
     * @return string
     */
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

    /**
     * Tìm đơn hàng theo từ khoá (hỗ trợ không dấu)
     * @param string $keyword
     * @return array
     */
    public function searchOrders($keyword)
    {
        $keywordRaw = trim(mb_strtolower($keyword));
        if ($keywordRaw == "") {
            return $this->query("SELECT * FROM {$this->table} ORDER BY created_at DESC")->fetchAll();
        }

        $keywordND = $this->vn_no_accent($keywordRaw);

        $statusMap = [
            "cho" => "pending",
            "choduyet" => "pending",
            "dangcho" => "pending",
            "duyet" => "approved",
            "daduyet" => "approved",
            "giao" => "shipping",
            "dangiao" => "shipping",
            "hoan" => "completed",
            "hoanthanh" => "completed",
            "huy" => "cancelled",
            "dahuy" => "cancelled"
        ];

        $searchStatus = $statusMap[$keywordND] ?? null;

        $orders = $this->query("SELECT * FROM {$this->table} ORDER BY created_at DESC")->fetchAll();

        $result = [];
        foreach ($orders as $row) {
            $addrRaw = mb_strtolower($row['address'] ?? '');
            $addrND  = $this->vn_no_accent($addrRaw);

            $transRaw = mb_strtolower($row['transaction_info'] ?? '');
            $transND  = $this->vn_no_accent($transRaw);

            if (
                (isset($row['order_code']) && strpos($row['order_code'], $keywordRaw) !== false) ||
                (isset($row['user_email']) && strpos(mb_strtolower($row['user_email']), $keywordRaw) !== false) ||
                (isset($row['receiver']) && strpos(mb_strtolower($row['receiver']), $keywordRaw) !== false) ||
                (isset($row['phone']) && strpos(mb_strtolower($row['phone']), $keywordRaw) !== false) ||
                strpos($addrND, $keywordND) !== false ||
                strpos($transND, $keywordND) !== false ||
                ($searchStatus !== null && isset($row['status']) && $row['status'] == $searchStatus)
            ) {
                $result[] = $row;
            }
        }

        return $result;
    }

    /**
     * Lọc đơn order list (dùng trong admin)
     * @param string $keyword
     * @param string $status
     * @param string $date (YYYY-MM-DD)
     * @return array
     */
    public function filterOrders($keyword = '', $status = '', $date = '') {
        $sql = "SELECT * FROM {$this->table} WHERE 1";
        $params = [];

        if ($keyword !== '') {
            $sql .= " AND (order_code LIKE ? OR user_email LIKE ? OR phone LIKE ? OR receiver LIKE ?)";
            $params[] = "%$keyword%";
            $params[] = "%$keyword%";
            $params[] = "%$keyword%";
            $params[] = "%$keyword%";
        }

        if ($status !== '') {
            $sql .= " AND status = ?";
            $params[] = $status;
        }

        if ($date !== '') {
            $sql .= " AND DATE(created_at) = ?";
            $params[] = $date;
        }

        $sql .= " ORDER BY created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Gửi email xác nhận đơn hàng đang chờ xử lý (pending)
     * Sử dụng PHPMailer; cấu hình mật khẩu từ hằng hoặc env
     */
    public function sendOrderPendingEmail($email, $orderCode, $orderDate, $totalPrice, $shippingFee)
    {
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'baochanbon@gmail.com';

            $pw = defined('MAIL_APP_PASSWORD') ? MAIL_APP_PASSWORD : (getenv('MAIL_APP_PASSWORD') ?: '');
            $mail->Password = $pw;

            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            // Fix font tiếng Việt
            $mail->CharSet = 'UTF-8';
            $mail->Encoding = 'base64';

            $mail->SMTPOptions = [
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                ]
            ];

            $mail->setFrom('baochanbon@gmail.com', 'Văn phòng phẩm LT');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = "Xác nhận đơn hàng #$orderCode đang chờ xử lý";

            $formatTotal = number_format($totalPrice, 0, ',', '.') . " ₫";
            $formatShip = number_format($shippingFee, 0, ',', '.') . " ₫";

            $mail->Body = "
                <h3>Cảm ơn bạn đã đặt hàng!</h3>
                <p>Đơn hàng của bạn đang được tiếp nhận và xử lý.</p>

                <p><b>Mã đơn hàng:</b> $orderCode</p>
                <p><b>Ngày đặt:</b> $orderDate</p>
               
                <p><b>Phí vận chuyển:</b> $formatShip</p>
                <p><b>Tổng thanh toán:</b> $formatTotal</p>

                <p>Chúng tôi sẽ chuẩn bị giao cho đơn vị vận chuyển.</p>

                <br>
                <p>Trân trọng,<br>Văn phòng phẩm LT</p>
            ";

            $mail->send();
            return true;

        } catch (Exception $e) {
            error_log("OrderModel::sendOrderPendingEmail error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Lấy đơn theo user kèm lọc thời gian + phân trang (dùng trên trang lịch sử đơn của user)
     * @param int $user
     * @param string|null $from YYYY-MM-DD
     * @param string|null $to YYYY-MM-DD
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getOrdersByUserFiltered($user, $from = null, $to = null, $limit = 6, $offset = 0) {
        $sql = "SELECT * FROM {$this->table} WHERE user_id = :uid";
        $params = [':uid' => $user];

        if ($from) {
            $sql .= " AND DATE(created_at) >= :from";
            $params[':from'] = $from;
        }
        if ($to) {
            $sql .= " AND DATE(created_at) <= :to";
            $params[':to'] = $to;
        }

        $sql .= " ORDER BY created_at DESC LIMIT :offset, :limit";

        $stmt = $this->db->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->bindValue(":offset", (int)$offset, PDO::PARAM_INT);
        $stmt->bindValue(":limit", (int)$limit, PDO::PARAM_INT);

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Đếm số lượng đơn của user (dùng khi phân trang lịch sử đơn)
     */
    public function countOrdersByUserFiltered($user_id, $from = null, $to = null) {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE user_id = :uid";
        $params = [':uid' => $user_id];

        if ($from) {
            $sql .= " AND DATE(created_at) >= :from";
            $params[':from'] = $from;
        }
        if ($to) {
            $sql .= " AND DATE(created_at) <= :to";
            $params[':to'] = $to;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchColumn();
    }
}
