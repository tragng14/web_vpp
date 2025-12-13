<?php
class Product extends Controller {

    public function __construct() {
        $this->requireRole(['admin', 'staff']);
    }
    // Hiển thị danh sách sản phẩm
    public function show() {
    $obj = $this->model("AdProducModel");
    $promoModel = $this->model("PromoModel");

    // ============================
    // 🔍 XỬ LÝ TÌM KIẾM SẢN PHẨM
    // ============================
    if (isset($_POST["btn_search"])) {
        $keyword = trim($_POST["keyword"]);
        $data = $obj->search($keyword); 
    } else {
        $data = $obj->all("tblsanpham");
    }

    // Cập nhật mã khuyến mãi hết hạn
    $promoModel->autoUpdateExpiredPromos();

    // Gắn mã khuyến mãi vào mỗi sản phẩm
    foreach ($data as &$sp) {
        $promo = $obj->getProductPromo($sp["masp"]);
        $sp["promo_info"] = $promo ? $promo["code"] : "Không có KM";
    }

    $this->view("adminPage", [
        "page" => "ProductListView",
        "productList" => $data,
        "productModel" => $obj
    ]);
}


    // Xóa sản phẩm (và mã khuyến mãi liên quan)
    public function delete($id) { 
        $productModel = $this->model("AdProducModel");
        $promoModel = $this->model("PromoModel");
    
        // 1️⃣ Lấy mã khuyến mãi trước khi xóa
        $promo = $productModel->getProductPromo($id);
        $promoCode = $promo['code'] ?? null;
    
        // 2️⃣ Xóa liên kết sản phẩm - khuyến mãi
        $productModel->deletePromo($id);
    
        // 3️⃣ Giảm số lần dùng nếu có mã
        if (!empty($promoCode)) {
            $promoModel->decrementUsage($promoCode);
        }
    
        // 4️⃣ Xóa sản phẩm
        $productModel->delete("tblsanpham", $id);
    
        header("Location:" . APP_URL . "/Product/");
        exit();
    }
    
    

    // Thêm sản phẩm mới
    public function create() {
        $obj = $this->model("AdProducModel");
        $obj2 = $this->model("AdProductTypeModel");
        $objPromo = $this->model("PromoModel");

        $producttype = $obj2->all("tblloaisp");
        $promoList = $objPromo->getAll();

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $masp_goc = $_POST["txt_masp"];
            $masp = preg_replace('/\s+/', '', $masp_goc);
            $tensp = $_POST["txt_tensp"];
            $maloaisp = $_POST["txt_maloaisp"];
            $soluong = $_POST["txt_soluong"];
            $gianhap = $_POST["txt_gianhap"];
            $giaxuat = $_POST["txt_giaxuat"];
            $khuyenmai = $_POST["txt_khuyenmai"]; // promo code
            $mota = $_POST["txt_mota"];
            $createDate = $_POST["create_date"];
            $hinhanh = $_FILES['uploadfile'];
            // Xử lý ảnh
            if (!empty($_FILES["uploadfile"]["name"])) {
                $hinhanh = $_FILES["uploadfile"]["name"];
                $file_tmp = $_FILES["uploadfile"]["tmp_name"];
                move_uploaded_file($file_tmp, "./public/images/" . $hinhanh);
            }
 

            // Thêm sản phẩm
          // Thêm sản phẩm
$obj->insert($maloaisp, $masp, $tensp, $hinhanh, $soluong, $gianhap, $giaxuat, $mota, $createDate);

// Nếu có mã khuyến mãi
if (!empty($khuyenmai)) {

    // Lấy thông tin mã KM
    $promoInfo = $objPromo->getByCode($khuyenmai);

    if (!$promoInfo) {
        echo "<script>alert('❌ Mã khuyến mãi không tồn tại!'); history.back();</script>";
        exit();
    }

    // Kiểm tra trạng thái
    if ($promoInfo['status'] === 'inactive' || $promoInfo['status'] === 'deleted') {
        echo "<script>alert('⚠️ Mã khuyến mãi không khả dụng!'); history.back();</script>";
        exit();
    }

    // Kiểm tra điều kiện giá tối thiểu
    if (floatval($giaxuat) < floatval($promoInfo['min_total'])) {
        echo "<script>alert('⚠️ Giá sản phẩm chưa đạt mức tối thiểu để áp dụng mã KM!'); history.back();</script>";
        exit();
    }

    // Giới hạn sử dụng
    if (!$objPromo->incrementUsage($khuyenmai)) {
        echo "<script>alert('⚠️ Mã khuyến mãi đã đạt giới hạn sử dụng!'); history.back();</script>";
        exit();
    }

    // 🔥 QUAN TRỌNG: LƯU vào bảng promo_product
    $objPromo->saveProductPromo($masp, $khuyenmai);
}

header('Location: ' . APP_URL . '/Product/');
exit();
        }

        $this->view("adminPage", [
            "page" => "ProductView",
            "producttype" => $producttype,
            "promoList" => $promoList
        ]);
    }

    // Sửa sản phẩm
    public function edit($masp) {
        $productModel = $this->model("AdProducModel");
        $obj2 = $this->model("AdProductTypeModel");
        $producttype = $obj2->all("tblloaisp");

        $promoModel = $this->model("PromoModel");
    
        $product = $productModel->find("tblsanpham", $masp);


        $promoList = $promoModel->all("promo_codes");
        $currentPromo = $productModel->getProductPromo($masp)['code'] ?? null;

    
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $maloaisp = $_POST["txt_maloaisp"];
            $masp = $_POST["txt_masp"];
            $tensp = $_POST["txt_tensp"];
            $soluong = $_POST["txt_soluong"];
            $gianhap = $_POST["txt_gianhap"];
            $giaxuat = $_POST["txt_giaxuat"];
            $mota = $_POST["txt_mota"];
            $createDate = $_POST["create_date"];
            $khuyenmai = $_POST["txt_khuyenmai"] ?? '';
            $hinhanh = $product['hinhanh'];


    
            // Upload hình ảnh nếu có
            if (!empty($_FILES["uploadfile"]["name"])) {
                $hinhanh = $_FILES["uploadfile"]["name"];
                $file_tmp = $_FILES["uploadfile"]["tmp_name"];
                move_uploaded_file($file_tmp, "./public/images/" . $hinhanh);
            }
 
    
            // Cập nhật bảng sản phẩm
            $productModel->update($maloaisp, $masp, $tensp, $hinhanh, $soluong, $gianhap, $giaxuat, $mota, $createDate);

    
           // --- Xử lý cập nhật bảng promo_product và used_count ---
           if ($currentPromo !== $khuyenmai) {
            // giảm mã cũ
            if (!empty($currentPromo)) {
                $promoModel->decrementUsage($currentPromo);
            }
        
            // tăng mã mới
            // === TRONG HÀM edit() ===
if (!empty($khuyenmai)) {
    // 🔹 Lấy thông tin mã khuyến mãi
    $promoInfo = $promoModel->getByCode($khuyenmai);

    if (!$promoInfo) {
        echo "<script>alert('❌ Mã khuyến mãi không tồn tại!'); history.back();</script>";
        exit();
    }

    // 🔹 Kiểm tra trạng thái
    if ($promoInfo['status'] === 'inactive') {
        echo "<script>alert('⚠️ Mã khuyến mãi đang tạm ngưng, không thể áp dụng!'); history.back();</script>";
        exit();
    } elseif ($promoInfo['status'] === 'deleted') {
        echo "<script>alert('⚠️ Mã khuyến mãi đã hết hạn hoặc bị xóa, không thể áp dụng!'); history.back();</script>";
        exit();
    }

    // 🔹 Kiểm tra điều kiện min_total
    if (floatval($giaxuat) < floatval($promoInfo['min_total'])) {
        echo "<script>
            alert('⚠️ Giá sản phẩm chưa đạt mức tối thiểu (" . number_format($promoInfo['min_total'], 0, ',', '.') . "₫) để áp dụng mã khuyến mãi!');
            history.back();
        </script>";
        exit();
    }

    // 🔹 Kiểm tra giới hạn sử dụng
    $ok = $promoModel->incrementUsage($khuyenmai);
    if ($ok) {
        $productModel->updateProductPromo($masp, $khuyenmai);
    } else {
        echo "<script>
            alert('⚠️ Mã khuyến mãi đã đạt giới hạn sử dụng, không thể áp dụng!');
            history.back();
        </script>";
        exit();
    }
}

            
        }
        
        // ✅ Chỉ redirect khi mọi thứ hợp lệ
        header('Location: ' . APP_URL . '/Product/');
        exit();
        }
    
        $this->view("adminPage", [
            "page" => "ProductView",
            "editItem" => $product,
            "promoList" => $promoList,
            "currentPromo" => $currentPromo,
            "producttype" => $producttype
            
        ]);
    }
    
}
