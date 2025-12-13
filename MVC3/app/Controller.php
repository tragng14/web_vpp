<?php
class Controller {

    public function __construct() {
        // Nếu đã đăng nhập → kiểm tra liên tục trạng thái
        if (isset($_SESSION['user'])) {

            $userModel = $this->model("UserModel");
            $stmt = $userModel->findByEmail($_SESSION['user']['email']);
            $user = $stmt ? $stmt->fetch(PDO::FETCH_ASSOC) : null;

            // Không tìm thấy user (đã xóa DB)
            if (!$user || $user['is_deleted'] == 1) {
                session_destroy();
                header("Location: " . APP_URL . "/Home/");
                exit();
            }

            // User bị tạm ngưng
            if ($user['status'] === 'Tạm ngưng') {
                session_destroy();
                header("Location: " . APP_URL . "/Home/");
                exit();
            }
        }
    }

    public function model($model){
        require_once "./models/".$model.".php";
        return new $model;
    }

    public function view($view,$data=array()){
        require_once "./views/".$view.".php";
    }

    
    protected function requireAdmin() {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            $_SESSION['error'] = "Vui lòng đăng nhập trước khi truy cập trang quản trị.";
            header("Location: " . APP_URL . "/AuthController2/ShowLogin");
            exit();
        }
    }
    
    protected function requireUser() {
        if (!isset($_SESSION['user'])) {
            header("Location: " . APP_URL . "/Home/");
            exit();
        }
    }


protected function requireRole(array $roles = []) {
    if (!isset($_SESSION['user'])) {
        $_SESSION['error'] = "Vui lòng đăng nhập.";
        header("Location: " . APP_URL . "/AuthController2/ShowLogin");
        exit();
    }

    if (!empty($roles) && !in_array($_SESSION['user']['role'], $roles)) {
        $_SESSION['error'] = "Bạn không có quyền truy cập.";
        header("Location: " . APP_URL . "/Home/");
        exit();
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

}
