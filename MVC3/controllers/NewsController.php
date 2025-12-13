    <?php
    class NewsController extends Controller {

            public function __construct() {
           $this->requireRole(['admin']);
        }

        // Hiển thị danh sách bài viết
        public function show() {
        
            $obj = $this->model("News");

              if (isset($_POST["btn_search"])) {
            $keyword = trim($_POST["keyword"]);
            $data = $obj->search($keyword);
        } else {
           
            $data = $obj->all("news");
        }

        
            $this->view("adminPage", ["page" => "NewsListView", "NewsList" => $data]);
        }

        // Xóa bài viết theo id
        public function delete($id) {
            $obj = $this->model("News");
            $obj->delete("news", $id);
            header("Location: " . APP_URL . "/News/");
            exit();
        }

        // Tạo mới bài viết
        public function create() {
            $obj = $this->model("News");

            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $title = $_POST["title"];
                $content = $_POST["content"];
                $status = ($_POST["status"] == "hiển thị") ? 1 : 0;
                $created_at = date("Y-m-d H:i:s");

                $image = "";
                if (!empty($_FILES["image"]["name"])) {
                    $image = $_FILES["image"]["name"];
                    $file_tmp = $_FILES["image"]["tmp_name"];
                    move_uploaded_file($file_tmp, "./public/images/" . $image);
                }

                $obj->insert($title, $content, $image, $status, $created_at);
                header("Location: " . APP_URL . "/News/");
                exit();
            }

            $this->view("adminPage", ["page" => "NewsAddView"]);
        }

        // Chỉnh sửa bài viết
        public function edit($id) {
            $obj = $this->model("News");
            $news = $obj->find("news", $id);

            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $title = $_POST["title"];
                $content = $_POST["content"];
                $status = ($_POST["status"] == "hiển thị") ? 1 : 0;
                $created_at = $news["created_at"]; // giữ nguyên ngày tạo
                $image = $news["image"];

                if (!empty($_FILES["image"]["name"])) {
                    $image = $_FILES["image"]["name"];
                    $file_tmp = $_FILES["image"]["tmp_name"];
                    move_uploaded_file($file_tmp, "./public/images/" . $image);
                }

                $obj->update($id, $title, $content, $image, $status, $created_at);
                header("Location: " . APP_URL . "/News/");
                exit();
            }

            $this->view("adminPage", [
                "page" => "NewsAddView",
                "editItem" => $news
            ]);
        }
    }
    ?>