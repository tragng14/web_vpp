<?php
// models/OrderDetailModel.php
// Phiên bản cải tiến: an toàn hơn, có thêm các hàm tiện ích (getWithProduct, updateQty, deleteByOrderId, sumTotal)

require_once 'BaseModel.php';

class OrderDetailModel extends BaseModel {
    protected $table = 'order_details';

    /**
     * Thêm một dòng chi tiết đơn hàng
     * @param int $orderId
     * @param string|int $productId
     * @param int $quantity
     * @param float $price        Giá gốc trên đơn vị
     * @param float|null $salePrice Giá sau KM trên đơn vị (null nếu không có)
     * @param float $total        Tổng tiền cho dòng = đơn giá (sau KM nếu có) * qty
     * @param string|null $image  Tên file ảnh
     * @param string|null $productName
     * @return bool|PDOStatement   PDOStatement khi thành công, false khi thất bại
     * @throws Exception nếu $orderId null
     */
    public function addOrderDetail($orderId, $productId, $quantity, $price, $salePrice, $total, $image = null, $productName = null) {
        if (is_null($orderId)) {
            throw new Exception("orderId không được để trống khi thêm chi tiết đơn hàng.");
        }

        $sql = "INSERT INTO {$this->table}
                (order_id, product_id, quantity, price, sale_price, total, image, product_name)
                VALUES (:order_id, :product_id, :quantity, :price, :sale_price, :total, :image, :product_name)";

        $params = [
            ':order_id'   => $orderId,
            ':product_id' => $productId,
            ':quantity'   => (int)$quantity,
            ':price'      => (float)$price,
            ':sale_price' => $salePrice !== null ? (float)$salePrice : null,
            ':total'      => (float)$total,
            ':image'      => $image,
            ':product_name' => $productName
        ];

        return $this->query($sql, $params);
    }

    /**
     * Lấy các chi tiết theo order_id (mảng)
     * @param int $orderId
     * @return array
     */
    public function getOrderDetails($orderId) {
        $sql = "SELECT * FROM {$this->table} WHERE order_id = :order_id ORDER BY id ASC";
        return $this->select($sql, [':order_id' => $orderId]);
    }

    /**
     * Lấy chi tiết kèm thông tin sản phẩm (nếu có bảng products tblsanpham)
     * Trả về mảng các row, với alias: product_name (từ product table nếu có), product_image ...
     * Nếu hệ thống bạn đặt tên bảng khác, sửa JOIN tương ứng.
     *
     * @param int $orderId
     * @return array
     */
    public function getOrderDetailsWithProduct($orderId) {
        // Giữ an toàn: nếu không có bảng sản phẩm, query vẫn chạy (LEFT JOIN)
        $sql = "SELECT od.*, p.tensp AS product_name_from_products, p.hinhanh AS product_image
                FROM {$this->table} od
                LEFT JOIN tblsanpham p ON p.masp = od.product_id
                WHERE od.order_id = :order_id
                ORDER BY od.id ASC";
        return $this->select($sql, [':order_id' => $orderId]);
    }

    /**
     * Cập nhật số lượng + total cho một dòng (theo id của order_details)
     * @param int $id
     * @param int $newQty
     * @param float|null $unitPrice (nếu null thì giữ price hiện tại)
     * @return bool|PDOStatement
     */
    public function updateQuantity($id, $newQty, $unitPrice = null) {
        // Lấy row hiện tại
        $row = $this->select("SELECT * FROM {$this->table} WHERE id = :id LIMIT 1", [':id' => $id]);
        if (empty($row)) return false;
        $row = $row[0];

        $price = ($unitPrice === null) ? (float)$row['price'] : (float)$unitPrice;
        // ưu tiên sale_price nếu tồn tại
        $unit = (isset($row['sale_price']) && $row['sale_price'] !== null && $row['sale_price'] !== '') ? (float)$row['sale_price'] : $price;
        $newTotal = $unit * (int)$newQty;

        $sql = "UPDATE {$this->table}
                SET quantity = :qty, price = :price, total = :total
                WHERE id = :id";
        return $this->query($sql, [
            ':qty' => (int)$newQty,
            ':price' => $price,
            ':total' => $newTotal,
            ':id' => $id
        ]);
    }

    /**
     * Xoá tất cả chi tiết theo order_id (thường dùng khi huỷ đơn)
     * @param int $orderId
     * @return bool|PDOStatement
     */
    public function deleteByOrderId($orderId) {
        $sql = "DELETE FROM {$this->table} WHERE order_id = :order_id";
        return $this->query($sql, [':order_id' => $orderId]);
    }

    /**
     * Lấy tổng tiền (SUM total) cho một order
     * @param int $orderId
     * @return float
     */
    public function sumTotalByOrderId($orderId) {
        $sql = "SELECT COALESCE(SUM(total), 0) AS sum_total FROM {$this->table} WHERE order_id = :order_id";
        $res = $this->select($sql, [':order_id' => $orderId]);
        if (empty($res)) return 0.0;
        return (float)$res[0]['sum_total'];
    }

    /**
     * Lấy 1 dòng theo id
     * @param int $id
     * @return array|null
     */
    public function getById($id) {
        $res = $this->select("SELECT * FROM {$this->table} WHERE id = :id LIMIT 1", [':id' => $id]);
        return !empty($res) ? $res[0] : null;
    }

    /**
     * Xoá 1 dòng chi tiết theo id
     * @param int $id
     * @return bool|PDOStatement
     */
    public function deleteById($id) {
        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        return $this->query($sql, [':id' => $id]);
    }
}
