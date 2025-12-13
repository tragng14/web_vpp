<?php
class PageController extends Controller
{

    // ==============================
    //  DANH SÁCH TRANG
    // ==============================
    public function show()
    {
          $this->requireRole(['admin', 'staff']);
        $model = $this->model("PageModel");
        $pages = $model->allPages();

        $this->view("adminPage", [
            "page"  => "PageListView",
            "pages" => $pages
        ]);
    }

    // ==============================
    //  FORM THÊM TRANG
    // ==============================
    public function create()
    {
        $this->view("adminPage", [
            "page" => "PageAddView",
            "pageData" => null   // QUAN TRỌNG: để form nhận đúng biến
        ]);
    }

    // ==============================
    //  LƯU TRANG MỚI
    // ==============================
    public function store()
    {
        if ($_SERVER["REQUEST_METHOD"] != "POST") {
            die("Method not allowed");
        }

        $title   = trim($_POST['title'] ?? '');
        $slug    = trim($_POST['slug'] ?? '');
        $content = $_POST['content'] ?? '';
        $status  = $_POST['status'] ?? 'active';

        if ($title == '' || $slug == '') {
            die("Vui lòng nhập đầy đủ tiêu đề và slug");
        }

        $model = $this->model("PageModel");
        $model->insertPage($title, $slug, $content, $status);

        header("Location: " . APP_URL . "/Page/show");
        exit;
    }

    // ==============================
    //  FORM SỬA TRANG
    // ==============================
    public function edit($id)
    {
        $model = $this->model("PageModel");
        $page = $model->getById($id);

        if (!$page) {
            die("Trang không tồn tại");
        }

$this->view("adminPage", [
    "page" => "PageAddView",
    "pageData" => $page   // CHUẨN
]);

    }

    // ==============================
    //  CẬP NHẬT TRANG
    // ==============================
    public function update($id)
    {
        if ($_SERVER["REQUEST_METHOD"] != "POST") {
            die("Method not allowed");
        }

        $title   = trim($_POST['title']);
        $slug    = trim($_POST['slug']);
        $content = $_POST['content'];
        $status  = $_POST['status'];

        $model = $this->model("PageModel");
        $model->updatePage($id, $title, $slug, $content, $status);

        header("Location: " . APP_URL . "/Page/show");
        exit;
    }

    // ==============================
    //  XÓA TRANG
    // ==============================
    public function delete($id)
    {
        $model = $this->model("PageModel");
        $model->deletePage($id);

        header("Location: " . APP_URL . "/Page/show");
        exit;
    }

     // Hiển thị chi tiết 1 trang
    public function PageDetail($slug = "")
    {
        $model = $this->model("PageModel");
        if (empty($slug)) {
            die("Slug không hợp lệ");
        }
        $page = $model->getBySlug($slug);
        if (!$page) {
            die("Không tìm thấy trang");
        }
            
    $pagesList = $model->getAllActive();
    $contactPage = $model->getById(5);
    $obj = $this->model("News"); 
    $newsList = $obj->all("news"); 
    $visibleNews = array_filter($newsList, function ($item) {
        return isset($item['status']) && 
               ($item['status'] == 1 || strtolower(trim($item['status'])) == 'hiển thị');
    });

        $this->view("homePage", [
            "page" => "PageDetail",
            "pageData" => $page,
            "contactPage" => $contactPage,
            "NewsList" => $visibleNews,
               "pagesList" => $pagesList

        ]);
    }
}
