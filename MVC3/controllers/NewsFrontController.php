<?php
class NewsFrontController extends Controller {
    public function show() {

    $obj = $this->model("News");
    $newsList = $obj->all("news");

    // Lọc chỉ lấy bài hiển thị
    $visibleNews = array_filter($newsList, function ($item) {
        return isset($item['status']) && 
               ($item['status'] == 1 || strtolower(trim($item['status'])) == 'hiển thị');
    });
     $pagesModel = $this->model("PageModel");
    $pagesList = $pagesModel->getAllActive();
$contactPage = $pagesModel->getById(5);
    $this->view("homePage", [
        "page" => "NewsListView",
        "NewsList" => $visibleNews,
         "contactPage" => $contactPage,
        "pagesList" => $pagesList
    ]);
}
    // Trang xem chi tiết tin tức
    public function Detail($id = 0) {
        if ($id == 0) {
            echo "<div class='container mt-5'><div class='alert alert-danger'>Không tìm thấy bài viết!</div></div>";
            return;
        }

        $newsModel = $this->model("News");
        $news = $newsModel->find("news", $id);

       $news = $newsModel->findVisible("news", $id);
            if ($news) {
                $this->view("homePage", [
                    "page" => "NewsDetailView",
                    "news" => $news
                ]);
            } else {
                echo "<div class='container mt-5'><div class='alert alert-danger'>Bài viết không tồn tại hoặc đang bị ẩn!</div></div>";
            }
    }
}
?>