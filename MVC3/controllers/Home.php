<?php
// controllers/Home.php
// Phiên bản: chỉnh sửa tối thiểu để chạy ổn định
// - Chú thích và ghi nhớ bằng tiếng Việt
// - Giữ nguyên toàn bộ chức năng cũ, chỉ sửa chỗ gây lỗi (addtocard, checkout quick order, 1 số guard)

class Home extends Controller {

    public function __construct() {
        // đảm bảo session đã start
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Nạp các dữ liệu dùng chung cho layout (categories/pages/news/banners)
     * Trả về mảng associative có các key trên (luôn tồn tại, có thể rỗng).
     */
    private function loadCommonData(): array {
        $dataCommon = [
            'categories'  => [],
            'pagesList'   => [],
            'contactPage' => null,
            'NewsList'    => [],
            'banners'     => []
        ];

        // Categories
        try {
            $typeModel = $this->model("AdProductTypeModel");
            if ($typeModel && method_exists($typeModel, 'all')) {
                $cats = $typeModel->all("tblloaisp");
                $dataCommon['categories'] = is_array($cats) ? $cats : [];
            }
        } catch (Throwable $e) {
            // fallback: require file nếu cần
            $modelPath = __DIR__ . '/../models/AdProductTypeModel.php';
            if (file_exists($modelPath)) {
                try {
                    require_once $modelPath;
                    if (class_exists('AdProductTypeModel')) {
                        $tmp = new AdProductTypeModel();
                        if (method_exists($tmp, 'all')) {
                            $cats = $tmp->all("tblloaisp");
                            $dataCommon['categories'] = is_array($cats) ? $cats : [];
                        }
                    }
                } catch (Throwable $e2) {
                    // ignore
                }
            }
        }

        // Pages + contact
        try {
            $pagesModel = $this->model("PageModel");
            if ($pagesModel && method_exists($pagesModel, 'getAllActive')) {
                $dataCommon['pagesList'] = $pagesModel->getAllActive();
            }
            if ($pagesModel && method_exists($pagesModel, 'getById')) {
                $dataCommon['contactPage'] = $pagesModel->getById(5);
            }
        } catch (Throwable $e) {
            // ignore
        }

        // News
        try {
            $newsModel = $this->model("News");
            if ($newsModel && method_exists($newsModel, 'all')) {
                $newsList = $newsModel->all("news");
                $visibleNews = array_filter($newsList ?? [], function ($item) {
                    return isset($item['status']) && ($item['status'] == 1 || strtolower(trim($item['status'])) == 'hiển thị');
                });
                $dataCommon['NewsList'] = $visibleNews;
            }
        } catch (Throwable $e) {
            // ignore
        }

        // Banners
        try {
            $bannerModel = $this->model("BannerModel");
            if ($bannerModel && method_exists($bannerModel, 'getActiveBanners')) {
                $dataCommon['banners'] = $bannerModel->getActiveBanners();
            }
        } catch (Throwable $e) {
            // ignore
        }

        return $dataCommon;
    }

    // ----------------------------
    // Các action (giữ logic ban đầu)
    // ----------------------------

    // Hiển thị lịch sử đơn hàng cho người dùng đã đăng nhập
    public function orderHistory() {
        if (!isset($_SESSION['user'])) {
            header('Location: ' . APP_URL . '/AuthController/ShowLogin');
            exit();
        }

        // Phân trang
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 6;
        $offset = ($page - 1) * $limit;

        // CHỈ LẤY user_id
        $user_id = $_SESSION['user']['user_id'];

        // Lọc ngày
        $from = $_GET['from'] ?? null;
        $to = $_GET['to'] ?? null;

        $orderModel = $this->model('OrderModel');

        // Lấy đơn theo user + lọc ngày + phân trang
        $orders = $orderModel->getOrdersByUserFiltered($user_id, $from, $to, $limit, $offset);

        // Đếm tổng đơn theo filter
        $totalOrders = $orderModel->countOrdersByUserFiltered($user_id, $from, $to);
        $totalPages = $totalOrders > 0 ? ceil($totalOrders / $limit) : 1;

        $common = $this->loadCommonData();

        $this->view('homePage', array_merge($common, [
            'page' => 'OrderHistoryView',
            'orders' => $orders,
            'totalPages' => $totalPages,
            'pageCurrent' => $page
        ]));
    }

    // Trang chủ
    public function show() {
        // Load common data
        $common = $this->loadCommonData();

        // Model sản phẩm
        $productModel = $this->model("AdProducModel");

        $limit = 12;
        $productList = [];

        // 1) Nếu model có method getTopSold / getBestSellers -> gọi trực tiếp
        if ($productModel) {
            if (method_exists($productModel, 'getTopSold')) {
                try {
                    $productList = $productModel->getTopSold($limit);
                } catch (Throwable $e) {
                    $productList = [];
                }
            } elseif (method_exists($productModel, 'getBestSellers')) {
                try {
                    $productList = $productModel->getBestSellers($limit);
                } catch (Throwable $e) {
                    $productList = [];
                }
            }
        }

        // 2) fallback: lấy all và sort theo trường lượt bán (nếu có)
        if (empty($productList)) {
            $all = [];
            try {
                if ($productModel && method_exists($productModel, 'all')) {
                    $all = $productModel->all("tblsanpham");
                } else {
                    $mp = __DIR__ . "/../models/AdProducModel.php";
                    if (file_exists($mp)) {
                        require_once $mp;
                        if (class_exists('AdProducModel')) {
                            $tmp = new AdProducModel();
                            if (method_exists($tmp, 'all')) {
                                $all = $tmp->all("tblsanpham");
                            }
                        }
                    }
                }
            } catch (Throwable $e) {
                $all = [];
            }

            if (!empty($all) && is_array($all)) {
                $scoreKeys = ['sold','luotban','soluongban','bought','sales_count','sold_count'];
                usort($all, function($a, $b) use ($scoreKeys) {
                    $getScore = function($item) use ($scoreKeys) {
                        foreach ($scoreKeys as $k) {
                            if (isset($item[$k]) && is_numeric($item[$k])) {
                                return (int)$item[$k];
                            }
                        }
                        return 0;
                    };
                    $sa = $getScore($a);
                    $sb = $getScore($b);
                    if ($sa == $sb) return 0;
                    return ($sa > $sb) ? -1 : 1;
                });
                $productList = array_slice($all, 0, $limit);
            } else {
                $productList = [];
            }
        }

        // Merge dữ liệu chung và render view homePage
        $viewData = array_merge($common, [
            "page" => "HomeView",
            "productList" => $productList,
            "banners" => $common['banners'] ?? []
        ]);

        $this->view("homePage", $viewData);
    }

    // Chi tiết sản phẩm
    public function detail($masp) {
        $obj = $this->model("AdProducModel");
        $data = null;
        if ($obj && method_exists($obj, 'find')) {
            $data = $obj->find("tblsanpham", $masp);
        } elseif ($obj && method_exists($obj, 'getProductById')) {
            $data = $obj->getProductById($masp);
        }

        // Lấy thông tin khuyến mãi nếu model hỗ trợ
        if ($obj && method_exists($obj, 'getProductPromo')) {
            $promo = $obj->getProductPromo($masp);
            if (is_array($promo)) {
                $data['promo_type']  = $promo['type']  ?? null;
                $data['promo_value'] = $promo['value'] ?? null;
                $data['promo_code']  = $promo['code']  ?? null;
            }
        }

        // Lấy user và review
        $user = $_SESSION['user'] ?? null;
        $reviewModel = $this->model("ReviewModel");
        $reviews = [];
        $avgRating = 0;
        if ($reviewModel) {
            if (method_exists($reviewModel, 'getByProduct')) $reviews = $reviewModel->getByProduct($masp);
            if (method_exists($reviewModel, 'getAvgRating')) $avgRating = $reviewModel->getAvgRating($masp);
        }

        // Kiểm tra quyền đánh giá (user đã mua và completed & đã thanh toán)
        $canReview = false;
        if ($user && isset($user['user_id']) && $reviewModel && method_exists($reviewModel, 'getDB')) {
            $db = $reviewModel->getDB();
            if ($db) {
                $sql = "
                    SELECT od.id
                    FROM orders od
                    JOIN order_details dt ON od.id = dt.order_id
                    WHERE od.user_id = ?
                      AND dt.product_id = ?
                      AND od.status = 'completed'
                      AND LOWER(od.transaction_info) = 'dathanhtoan'
                    LIMIT 1
                ";
                try {
                    $stm = $db->prepare($sql);
                    $stm->execute([$user['user_id'], $masp]);
                    $result = $stm->fetch(PDO::FETCH_ASSOC);
                    if ($result) $canReview = true;
                } catch (Throwable $e) {
                    // ignore
                }
            }
        }

        $common = $this->loadCommonData();

        $this->view("homePage", array_merge($common, [
            "page" => "DetailView",
            "product" => $data,
            "reviews" => $reviews,
            "avgRating" => $avgRating,
            "user" => $user,
            "canReview" => $canReview
        ]));
    }

    // Thêm vào giỏ (addtocard)
    // Sửa: lấy sản phẩm bằng AdProducModel::getProductById (ổn định hơn),
    //      và lấy promo bằng PromoModel nếu có -> tránh phụ thuộc vào cấu trúc trả về khác nhau
    public function addtocard($masp) {
        require_once(__DIR__ . '/../models/AdProducModel.php');
        $productModel = new AdProducModel();
        $product = $productModel->getProductById($masp);

        if (!$product) {
            $common = $this->loadCommonData();
            $this->view("homePage", array_merge($common, [
                "page" => "OrderView",
                "listProductOrder" => isset($_SESSION['cart']) ? $_SESSION['cart'] : [],
                "error" => "❌ Sản phẩm không tồn tại!"
            ]));
            return;
        }

        if (intval($product['soluong'] ?? 0) <= 0) {
            $common = $this->loadCommonData();
            $this->view("homePage", array_merge($common, [
                "page" => "OrderView",
                "listProductOrder" => isset($_SESSION['cart']) ? $_SESSION['cart'] : [],
                "error" => "❌ Sản phẩm <b>".htmlspecialchars($product['tensp'])."</b> đã hết hàng!"
            ]));
            return;
        }

        if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

        // nếu đã có trong cart -> tăng qty (kiểm tra tồn)
        if (isset($_SESSION['cart'][$masp])) {
            $currentQty = intval($_SESSION['cart'][$masp]['qty']);
            $newQty = $currentQty + 1;
            if ($newQty > intval($product['soluong'])) {
                $common = $this->loadCommonData();
                $this->view("homePage", array_merge($common, [
                    "page" => "OrderView",
                    "listProductOrder" => $_SESSION['cart'],
                    "error" => "⚠️ Sản phẩm <b>".htmlspecialchars($product['tensp'])."</b> chỉ còn <b>".$product['soluong']."</b> cái trong kho!"
                ]));
                return;
            }
            $_SESSION['cart'][$masp]['qty'] = $newQty;
        } else {
            // build item với thông tin cơ bản + kiểm tra promo
            // promo lấy từ PromoModel (nếu có phương thức)
            $promoCode = null; $promoType = null; $promoValue = null;
            $promoModel = $this->model("PromoModel");
            if ($promoModel && method_exists($promoModel, 'getPromoByProduct')) {
                // nếu model hỗ trợ getPromoByProduct
                $p = $promoModel->getPromoByProduct($masp);
                if (is_array($p)) {
                    $promoCode = $p['code'] ?? null;
                    $promoType = isset($p['type']) ? strtolower($p['type']) : null;
                    $promoValue = $p['value'] ?? null;
                    if ($promoType === 'fixed') $promoType = 'amount';
                }
            } else {
                // nếu không có getPromoByProduct trên PromoModel, thử AdProducModel::getProductPromo
                if (method_exists($productModel, 'getProductPromo')) {
                    $p = $productModel->getProductPromo($masp);
                    if (is_array($p)) {
                        $promoCode = $p['code'] ?? null;
                        $promoType = isset($p['type']) ? strtolower($p['type']) : null;
                        $promoValue = $p['value'] ?? null;
                        if ($promoType === 'fixed') $promoType = 'amount';
                    }
                }
            }

            // lưu vào session cart (cấu trúc chung dễ dùng)
            $_SESSION['cart'][$masp] = [
                'qty'         => 1,
                'masp'        => $product['masp'] ?? $masp,
                'tensp'       => $product['tensp'] ?? '',
                'hinhanh'     => $product['hinhanh'] ?? '',
                'giaxuat'     => floatval($product['giaXuat'] ?? $product['giaxuat'] ?? 0),
                'promo_code'  => $promoCode,
                'promo_type'  => $promoType,
                'promo_value' => $promoValue
            ];
        }

        header('Location: ' . APP_URL . '/Home/order');
        exit();
    }

    // Xoá sản phẩm khỏi giỏ
    public function delete($masp){
        if (isset($_SESSION['cart'][$masp])) {
            unset($_SESSION['cart'][$masp]);
        }
        $common = $this->loadCommonData();
        $this->view("homePage", array_merge($common, [
            "page" => "OrderView",
            "listProductOrder" => isset($_SESSION['cart']) ? $_SESSION['cart'] : []
        ]));
    }

    // Hiển thị giỏ hàng
    public function order() {
        $cart = isset($_SESSION['cart']) && is_array($_SESSION['cart']) ? $_SESSION['cart'] : [];

        if (!empty($cart)) {
            $promoModel = $this->model('PromoModel');
            $productModel = $this->model('AdProducModel');

            foreach ($cart as &$item) {
                // Nếu item có promo_code -> kiểm tra lại tính hợp lệ của mã
                if (!empty($item['promo_code']) && $promoModel && method_exists($promoModel, 'getValidPromoByCode')) {
                    $promo = $promoModel->getValidPromoByCode($item['promo_code']);
                    if ($promo) {
                        $item['promo_code']  = $promo['code'];
                        $type = strtolower($promo['type']);
                        if ($type === 'fixed') $type = 'amount';
                        $item['promo_type']  = $type;
                        $item['promo_value'] = $promo['value'];
                    } else {
                        // nếu code không còn hợp lệ -> bỏ
                        $item['promo_code']  = null;
                        $item['promo_type']  = null;
                        $item['promo_value'] = null;
                    }
                }

                // Nếu chưa có promo -> thử lấy promo theo product (model hỗ trợ)
                if (empty($item['promo_code']) && $productModel && method_exists($productModel, 'getProductPromo')) {
                    $promo = $productModel->getProductPromo($item['masp']);
                    if ($promo) {
                        $item['promo_code']  = $promo['code'] ?? null;
                        $type = isset($promo['type']) ? strtolower($promo['type']) : null;
                        if ($type === 'fixed') $type = 'amount';
                        $item['promo_type']  = $type;
                        $item['promo_value'] = $promo['value'] ?? null;
                    }
                }
            }
            unset($item);
            $_SESSION['cart'] = $cart;
        }

        $common = $this->loadCommonData();
        $this->view("homePage", array_merge($common, [
            "page" => "OrderView",
            "listProductOrder" => $cart
        ]));
    }

    // Cập nhật số lượng
    public function update() {
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['qty'])) {
            foreach ($_POST['qty'] as $k => $v) {
                if (isset($_SESSION['cart'][$k])) {
                    $_SESSION['cart'][$k]['qty'] = max(1, (int)$v);
                }
            }
        }

        // Re-apply promos (giữ nguyên logic đã có)
        $cart = isset($_SESSION['cart']) && is_array($_SESSION['cart']) ? $_SESSION['cart'] : [];
        if (!empty($cart)) {
            $promoModel = $this->model('PromoModel');
            foreach ($cart as &$item) {
                if (!empty($item['promo_code']) && $promoModel && method_exists($promoModel, 'getValidPromoByCode')) {
                    $promo = $promoModel->getValidPromoByCode($item['promo_code']);
                    if ($promo) {
                        $item['promo_code']  = $promo['code'];
                        $type = strtolower($promo['type']);
                        if ($type === 'fixed') $type = 'amount';
                        $item['promo_type']  = $type;
                        $item['promo_value'] = $promo['value'];
                    } else {
                        $item['promo_code']  = null;
                        $item['promo_type']  = null;
                        $item['promo_value'] = null;
                    }
                }
            }
            unset($item);
            $_SESSION['cart'] = $cart;
        }

        $common = $this->loadCommonData();
        $this->view("homePage", array_merge($common, [
            "page" => "OrderView",
            "listProductOrder" => $_SESSION['cart']
        ]));
    }

    // Đặt hàng nhanh (checkout)
    // Sửa: gọi createOrderWithShipping với tham số đúng (userId, userEmail, ...)
    public function checkout() {
        if (!isset($_SESSION['user'])) {
            header('Location: ' . APP_URL . '/AuthController/ShowLogin');
            exit();
        }

        $cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
        if (empty($cart)) {
            $common = $this->loadCommonData();
            $this->view("homePage", array_merge($common, [
                "page" => "OrderView",
                "listProductOrder" => [],
                "success" => "Giỏ hàng trống!"
            ]));
            return;
        }

        $orderModel = $this->model("OrderModel");
        $orderDetailModel = $this->model("OrderDetailModel");
        $user = $_SESSION['user'];
        $orderCode = 'HD' . time();
        $totalAmount = 0.0;

        // Tính tổng (áp dụng promo trong item nếu có)
        foreach ($cart as $item) {
            $qty = intval($item['qty'] ?? 0);
            $price = floatval($item['giaxuat'] ?? $item['giaXuat'] ?? 0);
            $discount = 0.0;
            if (!empty($item['promo_type']) && isset($item['promo_value'])) {
                $ptype = strtolower($item['promo_type']);
                $pval = floatval($item['promo_value']);
                if ($ptype === 'percent') {
                    $discount = $price * ($pval / 100.0);
                } elseif ($ptype === 'amount') {
                    $discount = $pval;
                }
            }
            $priceAfter = max($price - $discount, 0.0);
            $thanhtien = $priceAfter * $qty;
            $totalAmount += $thanhtien;
        }

        // Đặt hàng nhanh: không có thông tin giao (để trống)
        $userId = $user['user_id'] ?? null;
        $userEmail = $user['email'] ?? ($user['user_email'] ?? '');
        if ($userId === null) {
            $common = $this->loadCommonData();
            $this->view("homePage", array_merge($common, [
                "page" => "OrderView",
                "listProductOrder" => $cart,
                "error" => "❌ Lỗi: không tìm thấy user_id trong session."
            ]));
            return;
        }

        // Gọi đúng thứ tự: userId, userEmail, orderCode, totalAmount, discountCode, receiver, phone, address, transaction_info, shipping_method, shipping_fee
        $orderId = $orderModel->createOrderWithShipping(
            $userId,
            $userEmail,
            $orderCode,
            $totalAmount,
            '', // discountCode
            '', // receiver
            '', // phone
            '', // address
            'chothanhtoan',
            'giao_hang',
            0
        );

        if ($orderId) {
            // Lưu chi tiết (kiểm tra signature addOrderDetail trong OrderDetailModel: 8 tham số)
            foreach ($cart as $item) {
                $qty = intval($item['qty'] ?? 0);
                $price = floatval($item['giaxuat'] ?? $item['giaXuat'] ?? 0);
                $discount = 0.0;
                if (!empty($item['promo_type']) && isset($item['promo_value'])) {
                    $ptype = strtolower($item['promo_type']);
                    $pval = floatval($item['promo_value']);
                    if ($ptype === 'percent') $discount = $price * ($pval / 100.0);
                    elseif ($ptype === 'amount') $discount = $pval;
                }
                $priceAfter = max($price - $discount, 0.0);
                $thanhtien = $priceAfter * $qty;

                // OrderDetailModel::addOrderDetail($orderId, $productId, $quantity, $price, $salePrice, $total, $image, $productName)
                $orderDetailModel->addOrderDetail(
                    $orderId,
                    $item['masp'],
                    $qty,
                    $price,
                    $priceAfter,
                    $thanhtien,
                    $item['hinhanh'] ?? '',
                    $item['tensp'] ?? ''
                );
            }
        }

        // Reset giỏ
        $_SESSION['cart'] = [];
        $common = $this->loadCommonData();
        $this->view("homePage", array_merge($common, [
            "page" => "OrderView",
            "listProductOrder" => [],
            "success" => "Đặt hàng thành công! Mã hóa đơn: $orderCode"
        ]));
    }

    // Kiểm tra tồn kho trước khi checkout
    public function checkStockBeforeCheckout() {
        $cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];

        if (empty($cart)) {
            $common = $this->loadCommonData();
            $this->view("homePage", array_merge($common, [
                "page" => "OrderView",
                "listProductOrder" => [],
                "error" => "🛒 Giỏ hàng của bạn đang trống!"
            ]));
            return;
        }

        require_once(__DIR__ . '/../models/AdProducModel.php');
        $productModel = new AdProducModel();

        $errors = [];
        foreach ($cart as $item) {
            $masp = $item['masp'];
            $qty = intval($item['qty']);
            $product = $productModel->getProductById($masp);
            if (!$product) {
                $errors[] = "❌ Sản phẩm có mã <b>$masp</b> không tồn tại!";
                continue;
            }
            if (intval($product['soluong']) <= 0) {
                $errors[] = "❌ Sản phẩm <b>" . htmlspecialchars($product['tensp']) . "</b> đã hết hàng!";
                continue;
            }
            if ($qty > intval($product['soluong'])) {
                $errors[] = "⚠️ Sản phẩm <b>" . htmlspecialchars($product['tensp']) . "</b> chỉ còn <b>" . $product['soluong'] . "</b> sản phẩm trong kho!";
                continue;
            }
        }

        if (!empty($errors)) {
            $common = $this->loadCommonData();
            $this->view("homePage", array_merge($common, [
                "page" => "OrderView",
                "listProductOrder" => $cart,
                "error" => implode("<br>", $errors)
            ]));
            return;
        }

        if (!isset($_SESSION['user'])) {
            $_SESSION['redirect_after_login'] = APP_URL . '/Home/checkoutInfo';
            $_SESSION['error'] = "Vui lòng đăng nhập trước khi đặt hàng!";
            header('Location: ' . APP_URL . '/AuthController/ShowLogin');
            exit;
        }

        header('Location: ' . APP_URL . '/Home/checkoutInfo');
        exit;
    }

    // Hủy đơn (admin/user)
    public function cancelOrder($orderId) {
        $role = 'user';
        if (isset($_SESSION['user']) && !empty($_SESSION['user']['role'])) {
            $sessRole = strtolower($_SESSION['user']['role']);
            if ($sessRole === 'admin' || $sessRole === 'administrator') $role = 'admin';
        }

        $orderModel = $this->model("OrderModel");
        $orderModel->cancelOrder($orderId, $role);

        if ($role === 'admin') {
            header("Location: " . APP_URL . "/Order");
        } else {
            header("Location: " . APP_URL . "/Home/orderHistory");
        }
        exit;
    }

    public function userCancelOrder($orderId) {
        $orderModel = $this->model("OrderModel");
        $orderModel->restoreStockAfterCancel($orderId);
        $orderModel->cancelOrder($orderId, 'user');
        header("Location: " . APP_URL . "/Home/orderHistory");
        exit;
    }

    // Lưu thông tin checkout (checkoutSave) — giữ logic, thêm validation sdt 0 + 10 chữ số
    public function checkoutSave() {
        if (!isset($_SESSION['user'])) {
            header('Location: ' . APP_URL . '/AuthController/Show');
            exit();
        }

        $cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
        if (empty($cart)) {
            $common = $this->loadCommonData();
            $this->view("homePage", array_merge($common, [
                "page" => "OrderView",
                "listProductOrder" => [],
                "success" => "Giỏ hàng trống!"
            ]));
            return;
        }

        $receiver = trim($_POST['receiver'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $discountCode = trim($_POST['discount_code'] ?? '');

        // Validate số điện thoại: bắt đầu bằng 0 và đúng 10 chữ số
        if (!preg_match('/^0\d{9}$/', $phone)) {
            echo '<div class="alert alert-danger">Số điện thoại không hợp lệ! (Bắt đầu bằng 0 và đúng 10 chữ số)</div>';
            return;
        }

        if ($receiver === '' || $phone === '' || $address === '') {
            echo '<div class="alert alert-danger">Vui lòng nhập đầy đủ thông tin giao hàng!</div>';
            $common = $this->loadCommonData();
            $this->view("homePage", array_merge($common, ["page" => "CheckoutInfoView"]));
            return;
        }

        $shipping_method = $_POST['shipping_method'] ?? 'giao_hang';
        $shipping_fee = ($shipping_method === 'giao_hang') ? 20000 : 0;

        $orderModel = $this->model("OrderModel");
        $orderDetailModel = $this->model("OrderDetailModel");
        $promoModel = $this->model("PromoModel");

        $user = $_SESSION['user'];
        $orderCode = 'HD' . time();

        // Tính total
        $totalAmount = 0;
        foreach ($cart as $item) {
            $gia = floatval($item['giaxuat'] ?? $item['giaXuat'] ?? 0);
            $qty = intval($item['qty'] ?? 0);
            if ($qty <= 0) continue;
            $discount = 0;
            if (isset($item['promo_type']) && isset($item['promo_value'])) {
                if ($item['promo_type'] == 'percent') $discount = $gia * ($item['promo_value'] / 100);
                elseif ($item['promo_type'] == 'amount') $discount = $item['promo_value'];
            }
            $priceAfterDiscount = max($gia - $discount, 0);
            $thanhtien = $priceAfterDiscount * $qty;
            $totalAmount += $thanhtien;
        }

        // Áp mã giảm giá toàn đơn nếu có
        $validDiscountCode = null;
        if ($discountCode !== '') {
            $discountInfo = $promoModel->getPromoByCode($discountCode);
            if (!$discountInfo || !is_array($discountInfo)) {
                $common = $this->loadCommonData();
                echo '<div class="alert alert-danger text-center">❌ Mã giảm giá không tồn tại hoặc đã hết hạn!</div>';
                $this->view("homePage", array_merge($common, ["page" => "CheckoutInfoView", "error" => "Mã giảm giá không tồn tại hoặc đã hết hạn!"]));
                return;
            }
            if (!empty($discountInfo['min_total']) && $totalAmount < $discountInfo['min_total']) {
                $common = $this->loadCommonData();
                echo '<div class="alert alert-warning text-center">⚠️ Đơn hàng chưa đạt giá trị tối thiểu để áp dụng mã giảm giá!</div>';
                $this->view("homePage", array_merge($common, ["page" => "CheckoutInfoView", "error" => "Đơn hàng chưa đạt giá trị tối thiểu để áp dụng mã giảm giá!"]));
                return;
            }
            if (isset($discountInfo['usage_limit']) && isset($discountInfo['used_count']) && $discountInfo['used_count'] >= $discountInfo['usage_limit']) {
                $common = $this->loadCommonData();
                echo '<div class="alert alert-warning text-center">⚠️ Mã giảm giá này đã đạt giới hạn sử dụng!</div>';
                $this->view("homePage", array_merge($common, ["page" => "CheckoutInfoView", "error" => "Mã giảm giá này đã đạt giới hạn sử dụng!"]));
                return;
            }

            if ($discountInfo['type'] === 'percent') {
                $discountValue = $totalAmount * ($discountInfo['value'] / 100);
            } else {
                $discountValue = $discountInfo['value'];
            }
            $totalAmount = max($totalAmount - $discountValue, 0);
            $validDiscountCode = $discountCode;
        }

        if ($shipping_method == 'giao_hang') $totalAmount += 20000;

        $userId = $user['user_id'] ?? null;
        if ($userId === null) {
            die("❌ Lỗi: Không tìm thấy user_id trong session!");
        }
        $userEmail = $user['email'] ?? '';

        $orderId = $orderModel->createOrderWithShipping(
            $userId,
            $userEmail,
            $orderCode,
            $totalAmount,
            $discountCode,
            $receiver,
            $phone,
            $address,
            'chothanhtoan',
            $shipping_method,
            $shipping_fee
        );

        if (!$orderId) {
            echo '<div class="alert alert-danger text-center">❌ Lỗi khi lưu đơn hàng. Vui lòng thử lại!</div>';
            return;
        }

        foreach ($cart as $item) {
            $gia = floatval($item['giaxuat'] ?? $item['giaXuat'] ?? 0);
            $qty = intval($item['qty'] ?? 0);
            $discount = 0;
            if (isset($item['promo_type']) && isset($item['promo_value'])) {
                if ($item['promo_type'] == 'percent') $discount = $gia * ($item['promo_value'] / 100);
                elseif ($item['promo_type'] == 'amount') $discount = $item['promo_value'];
            }
            $priceAfterDiscount = max($gia - $discount, 0);
            $thanhtien = $priceAfterDiscount * $qty;

            $orderDetailModel->addOrderDetail(
                $orderId,
                $item['masp'],
                $qty,
                $gia,
                $priceAfterDiscount,
                $thanhtien,
                $item['hinhanh'] ?? '',
                $item['tensp'] ?? ''
            );
        }

        // Lưu session cho checkout/vnpay
        $_SESSION['orderCode'] = $orderCode;
        $_SESSION['totalAmount'] = $totalAmount;
        $_SESSION['cart'] = [];
        $_SESSION['checkout'] = [
            'receiver' => $receiver,
            'phone'    => $phone,
            'email'    => $userEmail,
            'address'  => $address,
            'amount'   => $totalAmount,
            'bankCode' => $_POST['bankCode'] ?? $_POST['payment_method'] ?? ''
        ];

        if (!empty($validDiscountCode)) $_SESSION['validDiscountCode'] = $validDiscountCode;

        // Gửi email xác nhận tạm thời
        $orderModel->sendOrderPendingEmail($userEmail, $orderCode, date('Y-m-d H:i:s'), $totalAmount, $shipping_fee);

        $payment_method = $_POST['payment_method'] ?? ($_POST['bankCode'] ?? '');

        if ($payment_method == 'vnpay' || (isset($_POST['bankCode']) && $_POST['bankCode'] !== '')) {
            header('Location: ' . APP_URL . '/vnpay_php/vnpay_pay.php');
            exit();
        } else {
            // COD: trừ tồn kho và tăng lượt sử dụng mã nếu có
            require_once(__DIR__ . '/../models/AdProducModel.php');
            $productModel = new AdProducModel();
            $productModel->reduceStockAfterPayment($orderId);
            if (!empty($discountCode) && $promoModel && method_exists($promoModel, 'incrementUsage')) $promoModel->incrementUsage($discountCode);

            $msg = "Đặt hàng thành công! Mã hóa đơn: $orderCode (Thanh toán COD)";
            if ($discountCode) $msg .= " - Đã áp dụng mã: " . htmlspecialchars($discountCode);

            $common = $this->loadCommonData();
            $this->view("homePage", array_merge($common, [
                "page" => "OrderView",
                "listProductOrder" => [],
                "success" => $msg
            ]));
        }
    }

    // VNPAY return (giữ nguyên)
    public function vnpayReturn() {
        $data = $_GET;
        $vnp_HashSecret = "BIEQ0QKGVSML4W5GY46GQXFCT9YUQ1WU";
        $message = '';

        if (isset($data['vnp_SecureHash'])) {
            $secureHash = $data['vnp_SecureHash'];
            unset($data['vnp_SecureHash'], $data['vnp_SecureHashType']);
            ksort($data);

            $hashData = '';
            foreach ($data as $key => $value) $hashData .= $key . '=' . $value . '&';
            $hashData = rtrim($hashData, '&');
            $calculatedHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);

            if ($calculatedHash === $secureHash) {
                $vnp_ResponseCode = $_GET['vnp_ResponseCode'] ?? '';
                $vnp_TxnRef = $_GET['vnp_TxnRef'] ?? '';
                $orderModel = $this->model("OrderModel");

                if ($vnp_ResponseCode === '00') {
                    $order = $orderModel->getOrderByCode($vnp_TxnRef);
                    if ($order) {
                        $orderModel->markAsPaid($order['id']);
                        require_once(__DIR__ . '/../models/AdProducModel.php');
                        $productModel = new AdProducModel();
                        $productModel->reduceStockAfterPayment($order['id']);
                    }
                    if (isset($_SESSION['validDiscountCode']) && $_SESSION['validDiscountCode'] != '') {
                        $promoModel = $this->model("PromoModel");
                        $promoModel->incrementUsage($_SESSION['validDiscountCode']);
                        unset($_SESSION['validDiscountCode']);
                    }
                    $message = "Thanh toán VNPAY thành công! Mã đơn hàng: $vnp_TxnRef";
                } else {
                    $message = "Thanh toán VNPAY không thành công. Mã trả về: " . htmlspecialchars($vnp_ResponseCode);
                }
            } else {
                $message = 'Chữ ký không hợp lệ.';
            }
        } else {
            $message = 'Thiếu tham số trả về từ VNPAY.';
        }

        $common = $this->loadCommonData();
        $this->view('homePage', array_merge($common, [
            'page' => 'OrderView',
            'listProductOrder' => [],
            'success' => $message
        ]));
    }

    // Form nhập thông tin giao hàng (checkoutInfo)
    public function checkoutInfo() {
        if (!isset($_SESSION['user'])) {
            header('location: ' . APP_URL . '/AuthController/Showlogin');
            exit();
        }
        $common = $this->loadCommonData();
        $this->view("homePage", array_merge($common, ["page" => "CheckoutInfoView"]));
    }

    // Hiển thị chi tiết đơn hàng
    public function orderDetail($id) {
        if (!isset($_SESSION['user'])) {
            header('Location: ' . APP_URL . '/AuthController/ShowLogin');
            exit();
        }

        $orderModel = $this->model("OrderModel");
        $order = $orderModel->getOrderById($id);
        $details = $orderModel->getOrderDetailsByOrderId($id);

        if (!$order || $order['user_email'] !== ($_SESSION['user']['email'] ?? '')) {
            echo "<div class='container mt-5'><div class='alert alert-danger'>Bạn không có quyền xem đơn hàng này!</div></div>";
            return;
        }

        $common = $this->loadCommonData();
        $this->view("homePage", array_merge($common, [
            "page" => "OrderDetailView",
            "order" => $order,
            "details" => $details
        ]));
    }

   protected function computePriceInfo(array $item): array {
        $priceKeys = ['giaXuat','giaxuat','price','gia','gia_ban'];
        $saleKeys  = ['sale_price','gia_km','giaGiam','price_sale','giaKM'];
        $discountKeys = ['discount','khuyen_mai','percent_off'];

        $toFloat = function($v) {
            if ($v === null || $v === '') return 0.0;
            if (is_array($v)) { $v = reset($v); }
            if (is_string($v)) {
                $s = trim($v);
                // Nếu có cả '.' và ',' xử lý ngăn nghìn/decimal
                if (strpos($s, '.') !== false && strpos($s, ',') !== false) {
                    $s = str_replace('.', '', $s); // 1.234.567 -> 1234567
                    $s = str_replace(',', '.', $s); // 1.234,56 -> 1234.56
                } else {
                    // loại bỏ kí tự tiền tệ, khoảng trắng, dấu phẩy ngăn nghìn
                    $s = str_replace(['₫','đ',' '], ['', '', ''], $s);
                    $s = str_replace(',', '', $s);
                }
                $s = preg_replace('/[^0-9\\.\\-]/', '', $s);
                return (float)$s;
            }
            return (float)$v;
        };

        // Lấy giá gốc
        $price = 0.0;
        foreach ($priceKeys as $k) {
            if (isset($item[$k]) && $item[$k] !== '') { $price = $toFloat($item[$k]); break; }
        }

        // Sale trực tiếp
        $salePrice = null;
        foreach ($saleKeys as $k) {
            if (isset($item[$k]) && $item[$k] !== '') { $salePrice = $toFloat($item[$k]); break; }
        }

        // Legacy discount %
        $discount = null;
        foreach ($discountKeys as $k) {
            if (isset($item[$k]) && $item[$k] !== '') { $discount = $toFloat($item[$k]); break; }
        }

        // Promo mới
        $promo_type = strtolower(trim((string)($item['promo_type'] ?? '')));
        $promo_value = isset($item['promo_value']) ? $toFloat($item['promo_value']) : 0.0;

        // Nếu legacy discount %
        if ($discount !== null && $price > 0 && $discount > 0) {
            $calc = $price * (100 - $discount) / 100;
            if ($salePrice === null || $salePrice > $calc) $salePrice = $calc;
        }

        // Áp promo mới nếu có
        $promoApplied = false;
        $promoLabel = '';
        if ($promo_type && $promo_value > 0 && $price > 0) {
            $promoFinal = $price;
            if (strpos($promo_type, 'percent') !== false || $promo_type === 'percent' || $promo_type === 'phantram') {
                $promoFinal = $price * (1 - $promo_value / 100);
                $promoLabel = 'Giảm ' . rtrim(rtrim(number_format($promo_value, 2, ',', '.'), '0'), ',') . '%';
            } else {
                $promoFinal = max(0, $price - $promo_value);
                $promoLabel = '- ' . number_format($promo_value, 0, ',', '.') . ' ₫';
            }
            if ($salePrice === null || $salePrice > $promoFinal) {
                $salePrice = $promoFinal;
                $promoApplied = true;
            }
        }

        // Chọn final price
        $final = ($salePrice !== null && $salePrice > 0 && $salePrice < $price) ? $salePrice : $price;
        $discount_percent = ($price > 0 && $final < $price) ? (int) round(100 * ($price - $final) / $price) : 0;
        $saving = ($price > $final) ? round($price - $final) : 0;
        if ($promoLabel === '' && $discount_percent > 0) {
            $promoLabel = '-' . $discount_percent . '%';
        }

        return [
            'price' => $price,
            'final' => $final,
            'discount_percent' => $discount_percent,
            'saving' => $saving,
            'promo_label' => $promoLabel
        ];
    }

     // Trang danh sách sản phẩm
    public function index() {
        // Load model loại & sản phẩm
        $typeModel = $this->model("AdProductTypeModel");
        $productModel = $this->model("AdProducModel");
        $reviewModel = null;
        try {
            $reviewModel = $this->model("ReviewModel");
        } catch (Throwable $t) {
            // nếu không có model Review, filter theo rating sẽ không hoạt động (fallback)
            $reviewModel = null;
        }

        $categories = [];
        $products = [];

        if (method_exists($typeModel, 'all')) {
            $categories = $typeModel->all("tblloaisp");
        }

        if (method_exists($productModel, 'getProductsWithCategory')) {
            $products = $productModel->getProductsWithCategory();
        } elseif (method_exists($productModel, 'all')) {
            $products = $productModel->all("tblsanpham");
        }

        // -----------------------
        // NHẬN PARAMS TỪ QUERY (sanitize input)
        // -----------------------
        $filterCat = isset($_GET['category']) ? trim($_GET['category']) : null;
        $q = isset($_GET['q']) ? trim($_GET['q']) : '';
        $sort = isset($_GET['sort']) ? trim($_GET['sort']) : ''; // asc | desc
        $page = max(1, filter_var($_GET['page'] ?? 1, FILTER_VALIDATE_INT) ?: 1);
        $perPage = max(1, filter_var($_GET['per_page'] ?? 12, FILTER_VALIDATE_INT) ?: 12);

        $min_price_raw = isset($_GET['min_price']) ? trim($_GET['min_price']) : '';
        $max_price_raw = isset($_GET['max_price']) ? trim($_GET['max_price']) : '';
        $price_range = isset($_GET['price_range']) ? trim($_GET['price_range']) : '';

        // MỚI: lọc theo đánh giá (nhận raw để truyền xuống view)
        $min_rating_raw = isset($_GET['min_rating']) ? trim($_GET['min_rating']) : '';
        // chuẩn hoá thành số hoặc 'has' (1 = có đánh giá)
        $min_rating_num = null;
        if ($min_rating_raw !== '') {
            if (strtolower($min_rating_raw) === 'has') {
                $min_rating_num = 1; // interpret as "có đánh giá"
            } elseif (is_numeric($min_rating_raw)) {
                $min_rating_num = intval($min_rating_raw);
                if ($min_rating_num < 1) $min_rating_num = 1;
                if ($min_rating_num > 5) $min_rating_num = 5;
            }
        }

        // Nếu user chọn preset (price_range) và không nhập tay, override raw
        if ($price_range !== '' && $min_price_raw === '' && $max_price_raw === '') {
            $parts = explode('-', $price_range);
            $min_price_raw = $parts[0] ?? '';
            $max_price_raw = $parts[1] ?? '';
        }

        $min_price = ($min_price_raw !== '' && is_numeric($min_price_raw)) ? (int)$min_price_raw : null;
        $max_price = ($max_price_raw !== '' && is_numeric($max_price_raw)) ? (int)$max_price_raw : null;

        if ($min_price !== null && $max_price !== null && $min_price > $max_price) {
            $tmp = $min_price; $min_price = $max_price; $max_price = $tmp;
        }

        // -----------------------
        // LẤY PROMO VÀ CHUẨN HOÁ GIÁ CHO TẤT CẢ PRODUCTS
        // Đồng thời lấy avg rating & rating_count nếu có ReviewModel
        // -----------------------
        foreach ($products as $k => $p) {
            $masp = $p['masp'] ?? $p['maSP'] ?? $p['id'] ?? null;

            $products[$k]['promo_type'] = $products[$k]['promo_type'] ?? '';
            $products[$k]['promo_value'] = $products[$k]['promo_value'] ?? 0;
            $products[$k]['promo_code'] = $products[$k]['promo_code'] ?? null;

            if ($masp && method_exists($productModel, 'getProductPromo')) {
                try {
                    $promo = $productModel->getProductPromo($masp);
                    if (is_array($promo) && !empty($promo)) {
                        $products[$k]['promo_type'] = $promo['type'] ?? $products[$k]['promo_type'];
                        $products[$k]['promo_value'] = $promo['value'] ?? $products[$k]['promo_value'];
                        $products[$k]['promo_code'] = $promo['code'] ?? $products[$k]['promo_code'];
                        if (isset($promo['start_date'])) $products[$k]['promo_start'] = $promo['start_date'];
                        if (isset($promo['end_date'])) $products[$k]['promo_end'] = $promo['end_date'];
                    }
                } catch (\Throwable $e) {
                    // bỏ qua lỗi lấy promo
                }
            }

            $info = $this->computePriceInfo((array)$products[$k]);
            $products[$k]['price'] = $info['price'];
            $products[$k]['final'] = $info['final'];
            $products[$k]['discount_percent'] = $info['discount_percent'];
            $products[$k]['saving'] = $info['saving'];
            $products[$k]['promo_label'] = $info['promo_label'];

            // Lấy điểm trung bình + số lượng đánh giá (nếu ReviewModel tồn tại)
            if ($masp && $reviewModel && method_exists($reviewModel, 'getAvgRating')) {
                try {
                    $avg = $reviewModel->getAvgRating($masp);
                    // getAvgRating trả ['avg'=>..., 'count'=>...]
                    $products[$k]['avg_rating'] = isset($avg['avg']) ? floatval($avg['avg']) : 0.0;
                    $products[$k]['rating_count'] = isset($avg['count']) ? intval($avg['count']) : 0;
                } catch (\Throwable $e) {
                    $products[$k]['avg_rating'] = 0.0;
                    $products[$k]['rating_count'] = 0;
                }
            } else {
                // fallback nếu không có model: cố gắng đọc các key sẵn có
                $products[$k]['avg_rating'] = isset($products[$k]['avg_rating']) ? floatval($products[$k]['avg_rating']) : (isset($products[$k]['rating']) ? floatval($products[$k]['rating']) : null);
                $products[$k]['rating_count'] = isset($products[$k]['rating_count']) ? intval($products[$k]['rating_count']) : (isset($products[$k]['reviews_count']) ? intval($products[$k]['reviews_count']) : 0);
            }
        }

        // -----------------------
        // LỌC PRODUCTS THEO CATEGORY / SEARCH / PRICE (dùng price SAU KM khi filter)
        // -----------------------
        if ($filterCat) {
            $products = array_values(array_filter($products, function($p) use ($filterCat) {
                return (isset($p['maLoaiSP']) && (string)$p['maLoaiSP'] === (string)$filterCat)
                    || (isset($p['maLoai']) && (string)$p['maLoai'] === (string)$filterCat)
                    || (isset($p['maloai']) && (string)$p['maloai'] === (string)$filterCat);
            }));
        }

        if ($q !== '') {
            $qLower = mb_strtolower($q, 'UTF-8');
            $products = array_values(array_filter($products, function($p) use ($qLower) {
                $name = $p['tensp'] ?? $p['ten'] ?? $p['name'] ?? '';
                return mb_stripos(mb_strtolower((string)$name, 'UTF-8'), $qLower, 0, 'UTF-8') !== false;
            }));
        }

        if ($min_price !== null || $max_price !== null) {
            $products = array_values(array_filter($products, function($p) use ($min_price, $max_price) {
                $priceToCheck = isset($p['final']) ? (int)round($p['final']) : (isset($p['price']) ? (int)round($p['price']) : 0);
                if ($min_price !== null && $priceToCheck < $min_price) return false;
                if ($max_price !== null && $max_price !== '' && $priceToCheck > $max_price) return false;
                return true;
            }));
        }

        // -----------------------
        // MỚI: LỌC THEO ĐÁNH GIÁ (min_rating)
        // - min_rating = 1 => chỉ "có đánh giá" (rating_count > 0)
        // - min_rating = 2..5 => avg_rating >= min_rating
        // Nếu không có ReviewModel hoặc dữ liệu rating, sẽ dùng fallback có/không có rating_count
        // -----------------------
        if ($min_rating_num !== null) {
            $products = array_values(array_filter($products, function($p) use ($min_rating_num) {
                $cnt = isset($p['rating_count']) ? intval($p['rating_count']) : 0;
                $avg = isset($p['avg_rating']) && $p['avg_rating'] !== null ? floatval($p['avg_rating']) : null;

                if ($min_rating_num === 1) {
                    // "có đánh giá"
                    return $cnt > 0;
                } else {
                    // cần avg >= min_rating_num
                    if ($avg === null) {
                        // nếu không có avg nhưng có cnt>0, coi như không đủ (an toàn)
                        return false;
                    }
                    return $avg >= $min_rating_num;
                }
            }));
        }

        // -----------------------
        // SẮP XẾP THEO GIÁ (nếu user request)
        // -----------------------
        if ($sort === 'asc' || $sort === 'desc') {
            usort($products, function($a, $b) use ($sort) {
                $priceA = floatval($a['final'] ?? $a['price'] ?? 0);
                $priceB = floatval($b['final'] ?? $b['price'] ?? 0);
                if ($priceA == $priceB) return 0;
                return ($sort === 'asc') ? ($priceA <=> $priceB) : ($priceB <=> $priceA);
            });
        }

        // -----------------------
        // PAGINATION (slice array)
        // -----------------------
        $total = count($products);
        $total_pages = $perPage > 0 ? (int)ceil($total / $perPage) : 1;
        $offset = ($page - 1) * $perPage;
        $pagedProducts = array_slice($products, $offset, $perPage);

        // -----------------------
        // WISHLIST: LẤY DANH SÁCH PRODUCT_ID ĐÃ THÍCH (CHO USER ĐANG ĐĂNG NHẬP)
        // -----------------------
        $wishlistIds = [];
        if (isset($_SESSION['user']) && !empty($_SESSION['user']['user_id'])) {
            $userId = (int)$_SESSION['user']['user_id'];
            try {
                $wishlistModel = $this->model("WishlistModel");
            } catch (Throwable $e) {
                $wishlistModel = null;
            }

            if ($wishlistModel && method_exists($wishlistModel, 'getProductIdsByUserId')) {
                try {
                    $wishlistIds = $wishlistModel->getProductIdsByUserId($userId);
                } catch (Throwable $e) {
                    $wishlistIds = [];
                }
            }
        }

        // -----------------------
        // CÁC DỮ LIỆU KHÁC (TRANG, TIN TỨC)
        // -----------------------
        $pagesModel = $this->model("PageModel");
        $pagesList = method_exists($pagesModel, 'getAllActive') ? $pagesModel->getAllActive() : [];
        $contactPage = method_exists($pagesModel, 'getById') ? $pagesModel->getById(5) : null;

        $newsModel = $this->model("News");
        $newsList = method_exists($newsModel, 'all') ? $newsModel->all("news") : [];
        $visibleNews = array_filter($newsList, function ($item) {
            return isset($item['status']) &&
                   ($item['status'] == 1 || strtolower(trim($item['status'])) == 'hiển thị');
        });

        // -----------------------
        // TRUYỀN DỮ LIỆU XUỐNG VIEW
        // -----------------------
        $this->view("homePage", [
            "page" => "HomeView",
            "categories" => $categories,
            "products" => $products, // toàn bộ sản phẩm chưa phân trang
"pagedProducts" => $pagedProducts, // sản phẩm theo trang
            "filterCategory" => $filterCat,
            "searchQuery" => $q,
            "sort" => $sort,
            "min_price" => $min_price_raw !== '' ? $min_price_raw : '',
            "max_price" => $max_price_raw !== '' ? $max_price_raw : '',
            "price_range" => $price_range,
            // truyền min_rating về view để select hiển thị trạng thái
            "min_rating" => $min_rating_raw !== '' ? $min_rating_raw : '',
            "currentPage" => $page,
            "per_page" => $perPage,
            "total" => $total,
            "total_pages" => $total_pages,
            "contactPage" => $contactPage,
            "NewsList" => $visibleNews,
            "pagesList" => $pagesList,
            "wishlist" => $wishlistIds,
        ]);
    }
}
