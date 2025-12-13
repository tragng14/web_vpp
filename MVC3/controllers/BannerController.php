<?php
class BannerController extends Controller
{

            public function __construct() {
           $this->requireRole(['admin']);
        }

    /* ============================================================
        🟦 HIỂN THỊ DANH SÁCH TẤT CẢ BANNER SET
    ============================================================ */
    public function show()
    {
          $this->requireRole(['admin', 'staff']);
        $model = $this->model("BannerModel");
        $banners = $model->allSets();

        $this->view("adminPage", [
            "page" => "BannerListView",
            "banners" => $banners
        ]);
    }
public function store()
{
    $title = $_POST['title'];
    $desc = $_POST['description'];
    $status = $_POST['status'];
    $created_at = date("Y-m-d H:i:s");

    $model = $this->model("BannerModel");

    // 1) Tạo banner set
    $banner_id = $model->insertSet($title, $desc, $status, $created_at);

    if (!$banner_id) {
        die("Lỗi không tạo được banner!");
    }

    // 2) Upload nhiều ảnh (nếu có)
    if (!empty($_FILES['images']['name'][0])) {

        $uploadDir = "./public/images/banners/";

        foreach ($_FILES['images']['name'] as $i => $name) {

    if (!$name) continue; // chỉ xử lý nếu có file

    $tmp = $_FILES['images']['tmp_name'][$i];
    $newName = time() . "_" . rand(1000,9999) . "_" . $name;

    move_uploaded_file($tmp, $uploadDir . $newName);

    // LẤY ĐÚNG SORT THEO ẢNH CÓ FILE
    $link = $_POST['link'][$i] ?? "";
    $sort = $_POST['sort'][$i] ?? 0;

    $model->insertImage($banner_id, $newName, $link, $sort);
}

    }

    header("Location: " . APP_URL . "/Banner/show");
    exit;
}

    /* ============================================================
        🟦 FORM XEM / SỬA 1 BANNER SET
    ============================================================ */
    public function edit($id)
    {
        $model = $this->model("BannerModel");

        $banner = $model->findSet($id);
        $images = $model->getImages($id);

        if (!$banner) {
            die("❌ Banner không tồn tại.");
        }

        $this->view("adminPage", [
            "page"   => "BannerAddView",   // View bạn sẽ dùng để thêm / sửa
            "banner" => $banner,
            "images" => $images
        ]);
    }

    /* ============================================================
        🟢 LƯU BANNER SET MỚI
    ============================================================ */
  public function create()
{
    $this->view("adminPage", [
        "page" => "BannerAddView"  // dùng lại form edit
    ]);
}


    /* ============================================================
        🟢 UPLOAD NHIỀU ẢNH CHO 1 BANNER SET
    ============================================================ */
    public function uploadImages($banner_id)
    {
        $model = $this->model("BannerModel");
        $uploadDir = "./public/images/banners/";

        if (!empty($_FILES['images']['name'])) {

            foreach ($_FILES['images']['name'] as $i => $name) {

                if (!$name) continue;

                $tmp_path  = $_FILES['images']['tmp_name'][$i];
                $new_name  = time() . "_" . rand(1000, 9999) . "_" . $name;

                move_uploaded_file($tmp_path, $uploadDir . $new_name);

                // Các thông tin thêm (link, sort order)
                $link = $_POST['link'][$i] ?? "";
                $sort = $_POST['sort'][$i] ?? 0;

                // Ghi database
                $model->insertImage($banner_id, $new_name, $link, $sort);
            }
        }

        header("Location: " . APP_URL . "/Banner/edit/$banner_id");
        exit;
    }

    /* ============================================================
        🔴 XOÁ 1 ẢNH TRONG BANNER
    ============================================================ */
    public function deleteImage($banner_id, $img_id)
    {
        $model = $this->model("BannerModel");
        $model->deleteImage($img_id);

        header("Location: " . APP_URL . "/Banner/edit/$banner_id");
        exit;
    }

    /* ============================================================
        🔴 XOÁ TOÀN BỘ 1 BANNER SET (CASCADE)
        → Xoá luôn toàn bộ hình ảnh
    ============================================================ */
    public function delete2($banner_id)
    {
        $model = $this->model("BannerModel");
        $model->deleteSet($banner_id);

        header("Location: " . APP_URL . "/Banner/show");
        exit;
    }

public function update($id)
{
    $model = $this->model("BannerModel");

    $title   = $_POST['title'];
    $desc    = $_POST['description'];
    $status  = $_POST['status'];
    $created = date("Y-m-d H:i:s");

    // ===== 1. CẬP NHẬT THÔNG TIN BANNER =====
    $model->updateSet($id, $title, $desc, $status, $created);

    // ===== 2. CẬP NHẬT ẢNH CŨ =====
    if (!empty($_POST['old_img_id'])) {
        foreach ($_POST['old_img_id'] as $k => $img_id) {

            $old_link = $_POST['old_link'][$k] ?? "";
            $old_sort = $_POST['old_sort'][$k] ?? 0;

            // Gọi hàm update ảnh cũ
            $model->updateImage($img_id, $old_link, $old_sort);
        }
    }

    // ===== 3. UPLOAD ẢNH MỚI =====
    if (!empty($_FILES['images']['name'][0])) {

        $uploadDir = "./public/images/banners/";

        foreach ($_FILES['images']['name'] as $i => $name) {

            if (!$name) continue;

            $tmp = $_FILES['images']['tmp_name'][$i];

            $newName = time() . "_" . rand(1000, 9999) . "_" . $name;

            move_uploaded_file($tmp, $uploadDir . $newName);

            $link = $_POST['link'][$i] ?? "";
            $sort = $_POST['sort'][$i] ?? 0;

            // Thêm ảnh mới
            $model->insertImage($id, $newName, $link, $sort);
        }
    }

    // ===== 4. CHUYỂN HƯỚNG =====
    header("Location: " . APP_URL . "/Banner/show");
    exit;
}

public function delete($id)
{
    $model = $this->model("BannerModel");

    // Xóa file ảnh vật lý
    $images = $model->getImages($id);
    foreach ($images as $img) {
        $path = "./public/images/banners/" . $img['image_path'];
        if (file_exists($path)) unlink($path);
    }

    // Xóa DB
    $model->deleteSet($id);

    header("Location: " . APP_URL . "/Banner/show");
    exit;
}

 
}
?>
