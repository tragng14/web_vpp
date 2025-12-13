<?php
require_once 'BaseModel.php';
require_once __DIR__ . '/../models/PromoModel.php';

class ReportModel extends BaseModel {

    /**
     * Trả về array: [ 'clause' => '...', 'params' => [...]]
     * Clause sẽ dùng placeholder :date_start và :date_end (hoặc :ym, :y nếu cần)
     */
    private function buildDateConditionParams($date, $type, $field = 'o.created_at') {
        $params = [];
        if ($type === 'year') {
            // date expected 'YYYY' or 'YYYY-MM-DD' -> lấy year phần đầu
            $year = substr($date, 0, 4);
            $clause = "$field >= :y_start AND $field < :y_next_start";
            $params[':y_start'] = "$year-01-01 00:00:00";
            $params[':y_next_start'] = ($year + 1) . "-01-01 00:00:00";
        } elseif ($type === 'month') {
            // date expected 'YYYY-MM' or 'YYYY-MM-DD'
            $ym = substr($date, 0, 7); // YYYY-MM
            // start: YYYY-MM-01 00:00:00, next start by adding 1 month
            $clause = "$field >= :m_start AND $field < :m_next_start";
            $params[':m_start'] = $ym . "-01 00:00:00";
            // compute next month via PHP DateTime to avoid DB functions
            $dt = DateTime::createFromFormat('Y-m', $ym);
            if (!$dt) $dt = new DateTime(); // fallback
            $dt->modify('+1 month');
            $params[':m_next_start'] = $dt->format('Y-m-01 00:00:00');
        } else {
            // day
            $d = substr($date, 0, 10); // YYYY-MM-DD
            $clause = "$field >= :d_start AND $field <= :d_end";
            $params[':d_start'] = $d . " 00:00:00";
            $params[':d_end'] = $d . " 23:59:59";
        }
        return ['clause' => $clause, 'params' => $params];
    }

    // ================================
    // 1) Lấy chi tiết đơn + thông tin khuyến mãi
    // ================================
    public function getReportData($date, $type) {
        $dc = $this->buildDateConditionParams($date, $type, "o.created_at");
        $condition = $dc['clause'];
        $params = $dc['params'];

        $sql = "
           SELECT 
    o.id AS order_id,
    o.order_code, 
    o.created_at,
    o.discount_code,
    o.shipping_method,
    o.shipping_fee,

    km.code AS promo_name,
    km.type AS promo_type,
    km.value AS promo_value,
    km.min_total AS promo_min_total,
    km.status AS promo_status,

    od.product_id,
    od.product_name,
    od.product_type,
    od.quantity, 
    od.sale_price, 
    od.total, 

    /* ⭐ TÍNH TỔNG ĐƠN GỐC (tổng tất cả total cùng order_code) */
    SUM(od.total) OVER (PARTITION BY o.id) AS original_total_order,

    /* ⭐ TÍNH GIẢM GIÁ (theo phần trăm hoặc cố định) */
    CASE 
        WHEN km.type = 'percent' THEN 
            (SUM(od.total) OVER (PARTITION BY o.id) * km.value / 100)
        WHEN km.type = 'fixed' THEN 
            km.value
        ELSE 
            0
    END AS discount_amount,

    /* ⭐ TÍNH THÀNH TIỀN CUỐI CÙNG KHÔNG BAO GỒM PHÍ SHIP */
    (
        SUM(od.total) OVER (PARTITION BY o.id)
        -
        CASE 
            WHEN km.type = 'percent' THEN 
                (SUM(od.total) OVER (PARTITION BY o.id) * km.value / 100)
            WHEN km.type = 'fixed' THEN 
                km.value
            ELSE 
                0
        END
    ) AS final_total_order,

    sp.hinhanh

FROM orders o
JOIN order_details od ON o.id = od.order_id
LEFT JOIN tblsanpham sp ON sp.masp = od.product_id
LEFT JOIN promo_codes km ON km.code = o.discount_code

WHERE o.transaction_info = 'dathanhtoan'
  AND o.cancelled_by IS NULL
  AND {$condition}

ORDER BY o.created_at DESC;

        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $this->appendPromotionTotals($rows);
    }

    // ================================
    // 2) Doanh thu theo loại sản phẩm (phân bổ khuyến mãi)
    // ================================
    public function getRevenueByCategory($date, $type) {
        $dc = $this->buildDateConditionParams($date, $type, "o.created_at");
        $condition = $dc['clause'];
        $params = $dc['params'];

        $sql = "
            SELECT 
                o.id AS order_id,
                o.discount_code,
                od.product_id,
                od.quantity,
                od.total AS product_total,
                l.tenLoaiSP AS category_name
            FROM order_details od
            JOIN orders o ON o.id = od.order_id
            JOIN tblsanpham sp ON sp.masp = od.product_id
            LEFT JOIN tblloaisp l ON l.maLoaiSP = sp.maLoaiSP
            WHERE o.transaction_info = 'dathanhtoan'
              AND o.cancelled_by IS NULL
              AND {$condition}
            ORDER BY o.id
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($rows)) return [];

        $promoModel = new PromoModel();

        // Gom theo order để phân bổ
        $orders = [];
        foreach ($rows as $r) {
            $orders[$r['order_id']][] = $r;
        }

        $categoryRevenue = [];

        foreach ($orders as $orderId => $items) {
            $orderTotal = 0;
            foreach ($items as $it) {
                $orderTotal += (float)$it['product_total'];
            }

            // Tính giảm giá toàn đơn
            $discount = 0;
            $discountCode = $items[0]['discount_code'] ?? null;

            if (!empty($discountCode)) {
                $promo = $promoModel->getPromoByCode($discountCode);
                if ($promo && ($promo['status'] ?? '') === 'active') {
                    if ($orderTotal >= (float)($promo['min_total'] ?? 0)) {
                        if (($promo['type'] ?? '') === 'percent') {
                            $discount = $orderTotal * ((float)$promo['value'] / 100.0);
                        } else {
                            $discount = (float)($promo['value'] ?? 0);
                        }
                    }
                }
            }

            if ($discount > $orderTotal) $discount = $orderTotal;
            if ($orderTotal <= 0) $orderTotal = 1e-9; // tránh chia cho 0

            // Phân bổ theo tỷ lệ
            foreach ($items as $it) {
                $ratio = ((float)$it['product_total']) / $orderTotal;
                $allocatedDiscount = $discount * $ratio;
                $realRevenue = (float)$it['product_total'] - $allocatedDiscount;
                $cat = $it['category_name'] ?? 'Chưa phân loại';

                if (!isset($categoryRevenue[$cat])) {
                    $categoryRevenue[$cat] = [
                        'category_name' => $cat,
                        'total_quantity' => 0,
                        'total_revenue' => 0.0
                    ];
                }

                $categoryRevenue[$cat]['total_quantity'] += (int)$it['quantity'];
                $categoryRevenue[$cat]['total_revenue'] += $realRevenue;
            }
        }

        // Chuyển sang mảng và sắp xếp
        $out = array_values($categoryRevenue);
        usort($out, function ($a, $b) {
            return $b['total_revenue'] <=> $a['total_revenue'];
        });

        return $out;
    }

    // ================================
    // 3) Tổng hợp doanh thu
    // ================================
    public function getSummary($date, $type) {
        $dc = $this->buildDateConditionParams($date, $type, "o.created_at");
        $condition = $dc['clause'];
        $params = $dc['params'];

        $sql = "
            SELECT 
                o.id AS order_id,
                o.discount_code,
                od.total AS product_total,
                od.quantity,
                o.created_at
            FROM orders o
            JOIN order_details od ON o.id = od.order_id
            WHERE o.transaction_info = 'dathanhtoan'
              AND o.cancelled_by IS NULL
              AND {$condition}
            ORDER BY o.id
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($rows)) {
            return [
                'total_orders'   => 0,
                'total_products' => 0,
                'total_revenue'  => 0.0,
                'used_promos'    => null
            ];
        }

        $promoModel = new PromoModel();

        // Gom theo từng đơn
        $orders = [];
        foreach ($rows as $r) {
            $orders[$r['order_id']][] = $r;
        }

        $totalRevenue = 0.0;
        $totalProducts = 0;
        $usedPromos = [];

        foreach ($orders as $orderId => $items) {
            $orderTotal = 0.0;
            foreach ($items as $it) {
                $orderTotal += (float)$it['product_total'];
                $totalProducts += (int)$it['quantity'];
            }

            $discount = 0.0;
            $code = $items[0]['discount_code'] ?? null;

            if (!empty($code)) {
                $promo = $promoModel->getPromoByCode($code);
                if ($promo) $usedPromos[] = $code;
                if ($promo && ($promo['status'] ?? '') === 'active') {
                    if ($orderTotal >= (float)($promo['min_total'] ?? 0)) {
                        if (($promo['type'] ?? '') === 'percent') {
                            $discount = $orderTotal * ((float)$promo['value'] / 100.0);
                        } else {
                            $discount = (float)($promo['value'] ?? 0);
                        }
                    }
                }
            }

            if ($discount > $orderTotal) $discount = $orderTotal;
            $realRevenue = $orderTotal - $discount;
            $totalRevenue += $realRevenue;
        }

        return [
            'total_orders'   => count($orders),
            'total_products' => $totalProducts,
            'total_revenue'  => $totalRevenue,
            'used_promos'    => implode(',', array_unique($usedPromos))
        ];
    }

    // ================================
    // Xử lý tính khuyến mãi (append totals)
    // ================================
private function appendPromotionTotals($rows) {
    if (empty($rows)) return [];

    $promoModel = new PromoModel();

    // Gom đơn
    $grouped = [];
    foreach ($rows as $r) {
        $grouped[$r['order_id']][] = $r;
    }

    $result = [];

    foreach ($grouped as $orderId => $items) {

        // Lấy phí ship theo từng đơn
        $shippingFee = 0;
        if (($items[0]['shipping_method'] ?? '') === 'giao_hang') {
            $shippingFee = (int)($items[0]['shipping_fee'] ?? 20000);
        }

        // Tổng gốc SP
        $originalTotal = 0.0;
        foreach ($items as $it) {
            $originalTotal += (float)$it['total'];
        }

        // Tính giảm giá
        $discountAmount = 0.0;
        $discountCode = $items[0]['discount_code'] ?? null;

        if (!empty($discountCode)) {
            $promo = $promoModel->getPromoByCode($discountCode);
            if ($promo && ($promo['status'] ?? '') === 'active') {
                if ($originalTotal >= (float)($promo['min_total'] ?? 0)) {
                    if ($promo['type'] === 'percent') {
                        $discountAmount = $originalTotal * ((float)$promo['value'] / 100.0);
                    } else {
                        $discountAmount = (float)($promo['value'] ?? 0);
                    }
                }
            }
        }

        if ($discountAmount > $originalTotal) $discountAmount = $originalTotal;

        // ⭐ Tổng tiền cuối cùng = SP - KM + phí ship
        $finalTotal = $originalTotal - $discountAmount + $shippingFee;

        // Gán lại cho từng dòng sản phẩm
        foreach ($items as $it) {
            $it['original_total_order'] = $originalTotal;
            $it['discount_amount'] = $discountAmount;
            $it['shipping_fee'] = $shippingFee;           // ⭐ THÊM
            $it['final_total_order'] = $finalTotal;
            $result[] = $it;
        }
    }

    return $result;
}

    // ================================
    // Doanh thu theo thời gian (label + total)
    // ================================
    public function getRevenueByTime($date, $type) {
        $dc = $this->buildDateConditionParams($date, $type, "o.created_at");
        $condition = $dc['clause'];
        $params = $dc['params'];

        $sql = "
            SELECT 
                o.id AS order_id,
                o.discount_code,
                o.created_at,
                od.total AS product_total
            FROM orders o
            JOIN order_details od ON o.id = od.order_id
            WHERE o.transaction_info = 'dathanhtoan'
              AND o.cancelled_by IS NULL
              AND {$condition}
            ORDER BY o.id
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($rows)) return [];

        $orders = [];
        foreach ($rows as $r) {
            $orders[$r['order_id']][] = $r;
        }

        $promoModel = new PromoModel();
        $result = [];

        foreach ($orders as $orderId => $items) {
            $orderTotal = 0.0;
            foreach ($items as $it) {
                $orderTotal += (float)$it['product_total'];
            }

            $discount = 0.0;
            $code = $items[0]['discount_code'] ?? null;

            if (!empty($code)) {
                $promo = $promoModel->getPromoByCode($code);
                if ($promo && ($promo['status'] ?? '') === 'active') {
                    if ($orderTotal >= (float)($promo['min_total'] ?? 0)) {
                        if (($promo['type'] ?? '') === 'percent') {
                            $discount = $orderTotal * ((float)$promo['value'] / 100.0);
                        } else {
                            $discount = (float)($promo['value'] ?? 0);
                        }
                    }
                }
            }

            if ($discount > $orderTotal) $discount = $orderTotal;

            // label
            $label = date("d/m", strtotime($items[0]['created_at']));
            if ($type === 'month') $label = date("d", strtotime($items[0]['created_at']));
            if ($type === 'year') $label = date("m", strtotime($items[0]['created_at']));

            if (!isset($result[$label])) $result[$label] = 0.0;
            $result[$label] += ($orderTotal - $discount);
        }

        $final = [];
        foreach ($result as $label => $total) {
            $final[] = ['label' => $label, 'total' => $total];
        }

        return $final;
    }

    // ================================
    // Top products
    // ================================
    public function getTopProducts($date, $type, $limit = 10) {
        $dc = $this->buildDateConditionParams($date, $type, "o.created_at");
        $condition = $dc['clause'];
        $params = $dc['params'];

        $sql = "
            SELECT 
                od.product_name,
                SUM(od.quantity) AS total_quantity
            FROM order_details od
            JOIN orders o ON o.id = od.order_id
            WHERE o.transaction_info = 'dathanhtoan'
              AND o.cancelled_by IS NULL
              AND {$condition}
            GROUP BY od.product_id, od.product_name
            ORDER BY total_quantity DESC
            LIMIT :limit
        ";

        $stmt = $this->db->prepare($sql);
        // bind dynamic params
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ================================
    // Doanh thu tổng theo ngày/month/year
    // ================================
    public function getRevenueByDate($date, $type) {
        $dc = $this->buildDateConditionParams($date, $type, "o.created_at");
        $condition = $dc['clause'];
        $params = $dc['params'];

        // Dùng tổng đơn giảm (nếu orders có cột discount_amount bạn có thể dùng trực tiếp)
        // Ở đây ta tính bằng cách lấy sum(orderTotal) - sum(discount) bằng cùng logic phân bổ
        // Simpler: nếu bảng orders có total_amount và có discount_amount => dùng luôn:
        $sql = "
            SELECT SUM(o.total_amount - IFNULL(o.discount_amount, 0)) AS revenue
            FROM orders o
            WHERE o.transaction_info = 'dathanhtoan'
              AND o.cancelled_by IS NULL
              AND {$condition}
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $val = $stmt->fetchColumn();
        return $val ? (float)$val : 0.0;
    }

    // ================================
// Top sản phẩm bán chậm
// ================================
public function getSlowProducts($date, $type, $limit = 10) {
    $dc = $this->buildDateConditionParams($date, $type, "o.created_at");
    $condition = $dc['clause'];
    $params = $dc['params'];

    $sql = "
        SELECT 
            od.product_name,
            SUM(od.quantity) AS total_quantity
        FROM order_details od
        JOIN orders o ON o.id = od.order_id
        WHERE o.transaction_info = 'dathanhtoan'
          AND o.cancelled_by IS NULL
          AND {$condition}
        GROUP BY od.product_id, od.product_name
        ORDER BY total_quantity ASC     -- 🔹 Bán chậm: số lượng nhỏ nhất
        LIMIT :limit
    ";

    $stmt = $this->db->prepare($sql);

    foreach ($params as $k => $v) {
        $stmt->bindValue($k, $v);
    }

    $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

public function getNeverSoldProducts() {
   $sql = "
        SELECT p.masp, p.tensp
        FROM tblsanpham p
        LEFT JOIN order_details od ON od.product_id = p.masp
        LEFT JOIN orders o ON o.id = od.order_id AND o.transaction_info = 'dathanhtoan' AND o.cancelled_by IS NULL
        GROUP BY p.masp, p.tensp
        HAVING COUNT(o.id) = 0
        ORDER BY p.tensp ASC
        LIMIT 500
    ";

    $stmt = $this->db->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}




}
?>
