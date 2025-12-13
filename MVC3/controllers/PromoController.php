<?php 
class PromoController extends Controller {
    public function __construct() {
        $this->requireRole(['admin']);
    }
    // Hiển thị danh sách mã khuyến mãi
// Hiển thị danh sách mã khuyến mãi
public function show() 
{
    $promoModel = $this->model("PromoModel");

    // Tự động cập nhật trạng thái hết hạn
    $promoModel->autoUpdateExpiredPromos();

    // Lấy keyword tìm kiếm nếu có
    $keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : "";

    // Nếu có từ khóa → tìm kiếm
    if ($keyword !== "") {
        $promoList = $promoModel->search($keyword);
    } 
    else {
        // Không có từ khóa → lấy toàn bộ
        $promoList = $promoModel->getAll("promo_codes");
    }

    // Render giao diện
    $this->view("adminPage", [
        "page"      => "PromoListView",
        "promoList" => $promoList,
        "keyword"   => $keyword
    ]);
}


    // Xóa mã khuyến mãi theo id
    public function delete($id) {
        $obj = $this->model("PromoModel");
        $obj->deletecode("promo_codes", $id);
        header("Location:" . APP_URL . "/Promo/");
        exit();
    }

    // =======================
    // Thêm mã khuyến mãi mới
    // =======================
    public function create() {
        $obj = $this->model("PromoModel");

        if ($_SERVER["REQUEST_METHOD"] === "POST") {

            // Lấy dữ liệu từ form và làm sạch
            $code = strtoupper(trim(preg_replace('/\s+/', '', $_POST["txt_code"] ?? '')));
            $type = isset($_POST["txt_type"]) ? trim($_POST["txt_type"]) : 'amount';


            $value = floatval($_POST["txt_value"] ?? 0);
            $min_total = floatval($_POST["txt_min_total"] ?? 0);
            $usage_limit = !empty($_POST["txt_usage_limit"]) ? intval($_POST["txt_usage_limit"]) : NULL;
            $start_date = $_POST["txt_start_date"] ?? null;
            $end_date = $_POST["txt_end_date"] ?? null;
            $status = $_POST["txt_status"] ?? 'inactive';
            $created_at = date("Y-m-d"); // chỉ lưu ngày, bỏ giờ

            // Gọi hàm insert trong PromoModel
            $result=$obj->insert(
                $code,
                $type,
                $value,
                $min_total,
                $usage_limit,
                0, // used_count mặc định = 0
                $start_date,
                $end_date,
                $status,
                $created_at
            );
          

            header("Location: " . APP_URL . "/Promo/");
            exit();
        }

        // Nếu không phải POST => hiển thị form thêm
        $this->view("adminPage", ["page" => "PromoAddView"]);
    }

    // =======================
    // Chỉnh sửa mã khuyến mãi
    // =======================
    public function edit($id) {
        $obj = $this->model("PromoModel");
        $promo = $obj->find("promo_codes", $id);

        if (!$promo) {
            die("Không tìm thấy mã khuyến mãi cần sửa.");
        }

        if ($_SERVER["REQUEST_METHOD"] === "POST") {

            // Giữ nguyên mã gốc
            $code = $promo["code"];

            // Cập nhật các trường khác
            $type = $_POST["txt_type"] ?? $promo["type"];
            $value = floatval($_POST["txt_value"] ?? $promo["value"]);
            $min_total = floatval($_POST["txt_min_total"] ?? $promo["min_total"]);
            $usage_limit = !empty($_POST["txt_usage_limit"]) ? intval($_POST["txt_usage_limit"]) : NULL;
            $used_count = intval($promo["used_count"]);
            $start_date = $_POST["txt_start_date"] ?? $promo["start_date"];
            $end_date = $_POST["txt_end_date"] ?? $promo["end_date"];
            $status = $_POST["txt_status"] ?? $promo["status"];
            $created_at = $promo["created_at"]; // giữ nguyên ngày tạo

            // Gọi hàm update trong PromoModel
            $obj->update(
                $code,
                $type,
                $value,
                $min_total,
                $usage_limit,
                $used_count,
                $start_date,
                $end_date,
                $status,
                $created_at
            );

            header("Location: " . APP_URL . "/Promo/");
            exit();
        }

        // Nếu chưa submit thì hiển thị form edit
        $this->view("adminPage", [
            "page" => "PromoAddView",
            "editItem" => $promo
        ]);
    }

public function filter()
{
    $type   = $_GET['type']   ?? "";
    $status = $_GET['status'] ?? "";
    $date   = $_GET['date']   ?? "";

    $obj = $this->model("PromoModel");

    // Tự động cập nhật hết hạn
    $obj->autoUpdateExpiredPromos();

    // Gọi bộ lọc nâng cao
    $data = $obj->filterAdvanced($type, $status, $date);

    $this->view("adminPage", [
        "page"      => "PromoListView",
        "promoList" => $data,
        "filter"    => [
            "type" => $type,
            "status" => $status,
            "date" => $date
        ]
    ]);
}

}
?>
