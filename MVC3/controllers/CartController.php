<?php
require_once 'models/OrderModel.php';
require_once 'models/OrderDetailModel.php';
require_once 'models/UserModel.php';

class CartController extends Controller {
    public function __construct() {
        $this->requireUser();
    }
    public function index() {
        
        // Hiển thị trang giỏ hàng
        $cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
      //  $this->render('Font_end/OrderView.php', ['cart' => $cart]);
       $this->view("homePage",["page"=>"OrderView",'cart' =>$cart]);
    }

    public function checkout() {
        if (!isset($_SESSION['user'])) {
            header('Location: /AuthController/login');
            exit();
        }
    
        $cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
        if (empty($cart)) {
            header('Location: /CartController/index');
            exit();
        }
    
        $user = $_SESSION['user'];
        $orderModel = $this->model("OrderModel");
        $orderDetailModel = $this->model("OrderDetailModel");
        $promoModel = $this->model("PromoModel"); // ✅ thêm dòng này
    
        $orderCode = 'HD' . time();
        $totalAmount = 0;
    
        // Tính tổng tiền đơn hàng
        foreach ($cart as $item) {
            $totalAmount += ($item['sale_price'] ?? $item['price']) * $item['quantity'];
        }
    
        // Tạo đơn hàng
        $orderId = $orderModel->createOrder($user['id'], $orderCode, $totalAmount);
    
        // Lưu chi tiết đơn hàng
        foreach ($cart as $item) {
            $orderDetailModel->addOrderDetail(
                $orderId,
                $item['id'],
                $item['quantity'],
                $item['price'],
                $item['sale_price'] ?? 0,
                ($item['sale_price'] ?? $item['price']) * $item['quantity'],
                $item['image'],
                $item['type'],
                $item['name']
            );
    
            // ✅ Nếu sản phẩm có áp mã giảm giá, cập nhật lượt dùng
            if (!empty($item['promo_code'])) {
                $promoModel->incrementUsage($item['promo_code']);
            }
        }
    
        // ✅ Nếu mã áp toàn giỏ (ví dụ lưu trong session), cũng cập nhật
        if (!empty($_SESSION['promo_code'])) {
            $promoModel->incrementUsage($_SESSION['promo_code']);
        }
    
        unset($_SESSION['cart']);
        unset($_SESSION['promo_code']); // ✅ xóa sau khi dùng
    
        $this->view("homePage", [
            "page" => "OrderView",
            'success' => true,
            'order_code' => $orderCode
        ]);
    }
    
}
