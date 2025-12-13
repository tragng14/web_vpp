<?php
// models/WishlistModel.php

class WishlistModel {
    protected ?PDO $pdo;
    protected string $table = 'wishlist';

    public function __construct(?PDO $pdo = null) {
        $this->pdo = null;
        if ($pdo instanceof PDO) { $this->pdo = $pdo; return; }
        if (isset($GLOBALS['pdo']) && $GLOBALS['pdo'] instanceof PDO) { $this->pdo = $GLOBALS['pdo']; return; }
        if (class_exists('DB')) {
            try {
                $dbObj = new DB();
                $this->pdo = $dbObj->getConnection();
                return;
            } catch (Exception $e) {
                error_log("WishlistModel::__construct DB error: " . $e->getMessage());
            }
        }
    }

    protected function getPdo(): ?PDO { return $this->pdo; }

    public function getByUserId($userId): array {
        $pdo = $this->getPdo();
        if (!$pdo) return [];
        try {
            $sql = "SELECT * FROM `{$this->table}` WHERE user_id = :uid ORDER BY created_at DESC";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':uid' => $userId]);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return is_array($rows) ? $rows : [];
        } catch (Exception $e) {
            error_log("WishlistModel::getByUserId error: " . $e->getMessage());
            return [];
        }
    }

    public function getProductIdsByUserId($userId): array {
        $rows = $this->getByUserId($userId);
        $ids = [];
        foreach ($rows as $r) {
            if (isset($r['product_id']) && $r['product_id'] !== '') $ids[] = (string)$r['product_id'];
            elseif (isset($r['masp']) && $r['masp'] !== '') $ids[] = (string)$r['masp'];
            elseif (isset($r['ma_sp']) && $r['ma_sp'] !== '') $ids[] = (string)$r['ma_sp'];
        }
        return array_values(array_unique($ids));
    }

    public function exists($userId, $productId): bool {
        $pdo = $this->getPdo();
        if (!$pdo) return false;
        try {
            $sql = "SELECT COUNT(1) AS cnt FROM `{$this->table}` WHERE user_id = :uid AND product_id = :pid";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':uid' => $userId, ':pid' => (string)$productId]);
            $r = $stmt->fetch(PDO::FETCH_ASSOC);
            return intval($r['cnt'] ?? 0) > 0;
        } catch (Exception $e) {
            error_log("WishlistModel::exists error: " . $e->getMessage());
            return false;
        }
    }

    public function add($userId, $productId): bool {
        $pdo = $this->getPdo();
        if (!$pdo) return false;
        try {
            if ($this->exists($userId, $productId)) return true;
            $sql = "INSERT INTO `{$this->table}` (user_id, product_id, created_at) VALUES (:uid, :pid, :now)";
            $stmt = $pdo->prepare($sql);
            $ok = $stmt->execute([
                ':uid' => $userId,
                ':pid' => (string)$productId,
                ':now' => date('Y-m-d H:i:s')
            ]);
            return (bool)$ok;
        } catch (Exception $e) {
            error_log("WishlistModel::add error: " . $e->getMessage());
            return false;
        }
    }

    public function remove($userId, $productId): bool {
        $pdo = $this->getPdo();
        if (!$pdo) return false;
        try {
            $sql = "DELETE FROM `{$this->table}` WHERE user_id = :uid AND product_id = :pid";
            $stmt = $pdo->prepare($sql);
            $ok = $stmt->execute([':uid' => $userId, ':pid' => (string)$productId]);
            return (bool)$ok;
        } catch (Exception $e) {
            error_log("WishlistModel::remove error: " . $e->getMessage());
            return false;
        }
    }

    public function toggle($userId, $productId): array {
        if (empty($userId)) return ['success' => false, 'action' => 'error', 'message' => 'Chưa đăng nhập'];
        if ($productId === '' || $productId === null) return ['success' => false, 'action' => 'error', 'message' => 'productId trống'];

        $pdo = $this->getPdo();
        if (!$pdo) return ['success' => false, 'action' => 'error', 'message' => 'Database not available'];

        try {
            if ($pdo->inTransaction() === false) $pdo->beginTransaction();
            if ($this->exists($userId, $productId)) {
                $ok = $this->remove($userId, $productId);
                if ($pdo->inTransaction()) $pdo->commit();
                return $ok ? ['success' => true, 'action' => 'removed'] : ['success' => false, 'action' => 'error', 'message' => 'Không xóa được'];
            } else {
                $ok = $this->add($userId, $productId);
                if ($pdo->inTransaction()) $pdo->commit();
                return $ok ? ['success' => true, 'action' => 'added'] : ['success' => false, 'action' => 'error', 'message' => 'Không thêm được'];
            }
        } catch (Exception $e) {
            try { if ($pdo->inTransaction()) $pdo->rollBack(); } catch (Exception $_) {}
            error_log("WishlistModel::toggle error: " . $e->getMessage());
            return ['success' => false, 'action' => 'error', 'message' => 'Lỗi server'];
        }
    }

    // optional helpers
    public function countByUser($userId): int {
        $pdo = $this->getPdo();
        if (!$pdo) return 0;
        try {
            $sql = "SELECT COUNT(1) AS cnt FROM `{$this->table}` WHERE user_id = :uid";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':uid' => $userId]);
            $r = $stmt->fetch(PDO::FETCH_ASSOC);
            return intval($r['cnt'] ?? 0);
        } catch (Exception $e) {
            error_log("WishlistModel::countByUser error: " . $e->getMessage());
            return 0;
        }
    }

    public function clearByUser($userId): bool {
        $pdo = $this->getPdo();
        if (!$pdo) return false;
        try {
            $sql = "DELETE FROM `{$this->table}` WHERE user_id = :uid";
            $stmt = $pdo->prepare($sql);
            return (bool)$stmt->execute([':uid' => $userId]);
        } catch (Exception $e) {
            error_log("WishlistModel::clearByUser error: " . $e->getMessage());
            return false;
        }
    }
}
