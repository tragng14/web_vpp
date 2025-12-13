<?php
// controllers/ProductFront.php
// FRONT-END controller cho trang danh sách + chi tiết sản phẩm
// Phiên bản: chuẩn hoá giá (price/final/discount_percent/saving/promo_label)
// Chỉ sửa tối thiểu, giữ nguyên chức năng và chú thích bằng TIẾNG VIỆT.

class ProductFront extends Controller {

    public function __construct() {
        // FRONT-END controller: KHÔNG requireAdmin
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Đảm bảo hằng APP_URL hoặc BASE_APP_URL tồn tại để sử dụng trong view khi cần
        if (!defined('BASE_APP_URL') && defined('APP_URL')) {
            define('BASE_APP_URL', rtrim(APP_URL, '/'));
        } elseif (!defined('BASE_APP_URL')) {
            define('BASE_APP_URL', '');
        }
    }

    // Một số router gọi Show() (chữ hoa) => chuyển tiếp
    public function Show() {
        return $this->index();
    }

    /**
     * Hàm trợ giúp: chuẩn hoá giá từ nhiều định dạng input khác nhau
     * Trả về mảng:
     * [
     *   'price' => (float) giá gốc,
     *   'final' => (float) giá sau khuyến mãi,
     *   'discount_percent' => (int) phần trăm giảm (ví dụ 20),
     *   'saving' => (int) số tiền tiết kiệm (làm tròn),
     *   'promo_label' => (string) nhãn ngắn để hiển thị (vd "Giảm 10%" hoặc "- 50.000 ₫")
     * ]
     *
     * Hàm này xử lý:
     *  - các key legacy (giaXuat, price, ...)
     *  - sale_price / gia_km
     *  - discount % legacy
     *  - promo_type / promo_value mới (percent, amount/fixed)
     */
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
            "page" => "ProductsView",
            "categories" => $categories,
            "products" => $pagedProducts,
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

    // Trang chi tiết sản phẩm
    public function detail($masp = null) {
        $productModel = $this->model("AdProducModel");
        if (!$masp && isset($_GET['id'])) $masp = $_GET['id'];
        $sp = null;
        if ($masp && method_exists($productModel, 'find')) {
            $sp = $productModel->find("tblsanpham", $masp);
        }

        // Nếu tìm được sản phẩm thì chuẩn hoá giá + lấy promo nếu có
        if ($sp && is_array($sp)) {
            if (method_exists($productModel, 'getProductPromo')) {
                try {
                    $promo = $productModel->getProductPromo($masp);
                    if (is_array($promo) && !empty($promo)) {
                        $sp['promo_type'] = $promo['type'] ?? ($sp['promo_type'] ?? '');
                        $sp['promo_value'] = $promo['value'] ?? ($sp['promo_value'] ?? 0);
                        $sp['promo_code'] = $promo['code'] ?? ($sp['promo_code'] ?? null);
                        if (isset($promo['start_date'])) $sp['promo_start'] = $promo['start_date'];
                        if (isset($promo['end_date'])) $sp['promo_end'] = $promo['end_date'];
                    } else {
                        $sp['promo_type'] = $sp['promo_type'] ?? '';
                        $sp['promo_value'] = $sp['promo_value'] ?? 0;
                    }
                } catch (\Throwable $e) {
                    $sp['promo_type'] = $sp['promo_type'] ?? '';
                    $sp['promo_value'] = $sp['promo_value'] ?? 0;
                }
            }

            $info = $this->computePriceInfo($sp);
            $sp['price'] = $info['price'];
            $sp['final'] = $info['final'];
            $sp['discount_percent'] = $info['discount_percent'];
            $sp['saving'] = $info['saving'];
            $sp['promo_label'] = $info['promo_label'];
        }

        $this->view("homePage", [
            "page" => "DetailView",
            "product" => $sp
        ]);
    }
}
