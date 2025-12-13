<?php
// controllers/WishlistController.php

class WishlistController extends Controller {
    public function __construct(){
        if (session_status() === PHP_SESSION_NONE) session_start();
        $this->requireRole(['admin', 'staff']);
    }

    /**
     * POST /wishlist/toggle
     * body: product_id (form post) OR { "product_id": "..." } (json)
     * Response JSON: { success: bool, action: 'added'|'removed'|'error', message?: string }
     */
    public function toggle() {
        header('Content-Type: application/json; charset=utf-8');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid method']);
            return;
        }

        // If not logged in, return error (frontend will fallback to localStorage)
        if (!isset($_SESSION['user']) || empty($_SESSION['user']['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Bạn cần đăng nhập để thực hiện hành động này.']);
            return;
        }

        $userId = (int) $_SESSION['user']['user_id'];

        // Get product id from form or JSON
        $productId = '';
        if (!empty($_POST['product_id'])) {
            $productId = trim((string)$_POST['product_id']);
        } else {
            $raw = file_get_contents('php://input');
            if ($raw) {
                $data = json_decode($raw, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    if (isset($data['product_id'])) $productId = trim((string)$data['product_id']);
                    elseif (isset($data['masp'])) $productId = trim((string)$data['masp']);
                    elseif (isset($data['id'])) $productId = trim((string)$data['id']);
                }
            }
        }

        if ($productId === '') {
            echo json_encode(['success' => false, 'message' => 'product_id required']);
            return;
        }

        // Load model (framework helper or fallback)
        $wishlistModel = null;
        try {
            if (method_exists($this, 'model')) $wishlistModel = $this->model('WishlistModel');
        } catch (Throwable $e) {}

        if (!$wishlistModel) {
            $modelPath = __DIR__ . '/../models/WishlistModel.php';
            if (file_exists($modelPath)) {
                require_once $modelPath;
                $pdo = $this->db ?? (isset($GLOBALS['pdo']) ? $GLOBALS['pdo'] : null);
                try { $wishlistModel = new WishlistModel($pdo); } catch (Throwable $e) { $wishlistModel = null; }
            }
        }

        if (!$wishlistModel) {
            echo json_encode(['success' => false, 'message' => 'Wishlist model not available']);
            return;
        }

        try {
            $res = $wishlistModel->toggle($userId, $productId);

            if (is_array($res)) {
                $out = [
                    'success' => isset($res['success']) ? (bool)$res['success'] : true,
                    'action'  => isset($res['action']) ? (string)$res['action'] : (isset($res['result']) ? (string)$res['result'] : 'unknown'),
                ];
                if (isset($res['message'])) $out['message'] = (string)$res['message'];
                echo json_encode($out);
                return;
            }

            if (is_string($res)) {
                echo json_encode(['success' => true, 'action' => $res]);
                return;
            }

            if (is_bool($res)) {
                echo json_encode(['success' => $res, 'action' => $res ? 'added' : 'removed']);
                return;
            }

            echo json_encode(['success' => false, 'message' => 'Unexpected model response']);
            return;

        } catch (Throwable $e) {
            // error_log($e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Lỗi server khi cập nhật wishlist']);
            return;
        }
    }

    /**
     * GET /wishlist/list
     * Response JSON: { success: true, data: [product_id,...] }
     * For guest (not logged in) returns success:true data:[] (frontend may read localStorage)
     */
    public function list() {
        header('Content-Type: application/json; charset=utf-8');
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            echo json_encode(['success' => false, 'message' => 'Invalid method']);
            return;
        }

        if (!isset($_SESSION['user']) || empty($_SESSION['user']['user_id'])) {
            echo json_encode(['success' => true, 'data' => []]); // guest: empty
            return;
        }

        $userId = (int) $_SESSION['user']['user_id'];

        // Load model
        $wishlistModel = null;
        try { if (method_exists($this, 'model')) $wishlistModel = $this->model('WishlistModel'); } catch (Throwable $e) {}

        if (!$wishlistModel) {
            $modelPath = __DIR__ . '/../models/WishlistModel.php';
            if (file_exists($modelPath)) {
                require_once $modelPath;
                $pdo = $this->db ?? (isset($GLOBALS['pdo']) ? $GLOBALS['pdo'] : null);
                try { $wishlistModel = new WishlistModel($pdo); } catch (Throwable $e) { $wishlistModel = null; }
            }
        }

        if (!$wishlistModel) {
            echo json_encode(['success' => false, 'message' => 'Wishlist model not available']);
            return;
        }

        try {
            $ids = $wishlistModel->getProductIdsByUserId($userId);
            echo json_encode(['success' => true, 'data' => $ids]);
            return;
        } catch (Throwable $e) {
            // error_log($e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Lỗi server khi lấy wishlist']);
            return;
        }
    }
}
