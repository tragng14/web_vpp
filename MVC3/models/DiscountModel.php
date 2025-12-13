<?php
require_once "BaseModel.php";

class DiscountModel extends BaseModel {
    public function getDiscountByCode($code) {
        $stmt = $this->db->prepare("SELECT * FROM discount_codes WHERE code = ? AND status = 'active' AND expiry_date >= CURDATE()");
        $stmt->execute([$code]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
