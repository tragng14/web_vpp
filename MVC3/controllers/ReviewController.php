<?php

class ReviewController extends Controller
{
    // Thư mục relative so với /public nơi lưu ảnh đánh giá (kết thúc bằng '/')
    private string $uploadDirPublic = 'images/reviews/';

    // Các mime hợp lệ và extension tương ứng
    private array $allowedMimes = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
    ];

    private int $maxFiles = 5;
    private int $maxSize = 2097152; // 2MB

    public function __construct()
    {
        parent::__construct();
    }

    public function show() {
         $this->requireRole(['admin', 'staff']);
    
        $reviewModel = $this->model("ReviewModel");
    
        // Lấy giá trị lọc từ GET
        $keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
        $stars = isset($_GET['stars']) ? trim($_GET['stars']) : '';
        $status = isset($_GET['status']) ? trim($_GET['status']) : '';
    
        // Gọi model lấy danh sách theo điều kiện lọc
        $reviewList = $reviewModel->getFilteredReviews($keyword, $stars, $status);
    
        // Trả về view
        $this->view("adminPage", [
            "page" => "ReviewListView",
            "reviewList" => $reviewList
        ]);
    }
    

    // ✅ Người dùng gửi đánh giá sản phẩm
    public function submit() {
        if (empty($_SESSION['user'])) {
            header("Location: " . APP_URL . "/AuthController/ShowLogin");
            exit;
        }



        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
        $user = $_SESSION['user'] ?? null;
            $masp = $_POST['masp'] ?? '';
           
$tenNguoiDung = $user['fullname'];
$email = $user['email'];
            $noidung = trim($_POST['noidung'] ?? '');
            $sao = intval($_POST['sao'] ?? 5);

            if ($masp && $tenNguoiDung && $email && $noidung) {
                $obj = $this->model("ReviewModel");
                // ✅ khi thêm mới thì trạng thái mặc định nên là 0 (Chờ duyệt)
                $obj->insert($masp, $tenNguoiDung, $email, $noidung, $sao, 0);

                header("Location: " . APP_URL . "/Home/detail/" . $masp);
                exit;
            } else {
                echo "<script>alert('Vui lòng nhập đầy đủ thông tin!');history.back();</script>";
            }
        }
    }

    // ✅ Xóa đánh giá
    public function delete($id) {
        $obj = $this->model("ReviewModel");
        $obj->delete('tbl_danhgia', $id);
        header("Location: " . APP_URL . "/Review/show");
        exit;
    }

    // ✅ Duyệt đánh giá (chuyển trạng thái từ 0 → 1)
    public function approve($id) {
        $obj = $this->model("ReviewModel");
        $review = $obj->find('tbl_danhgia', $id); // lấy dữ liệu đầy đủ
        if ($review) {
            // Giữ nguyên phản hồi admin
            $obj->update($id, $review['noidung'], $review['sao'], 1, $review['traloi']);
        }
        header("Location: " . APP_URL . "/Review/show");
        exit;
    }
    

    // ✅ Ẩn đánh giá (chuyển trạng thái từ 1 → 2)
    public function hide($id) {
        $obj = $this->model("ReviewModel");
        $review = $obj->find('tbl_danhgia', $id);
        if ($review) {
            // Giữ lại phản hồi admin
            $obj->update($id, $review['noidung'], $review['sao'], 2, $review['traloi']);
        }
        header("Location: " . APP_URL . "/Review/show");
        exit;
    }
    

    // ✅ Trang hiển thị form phản hồi chi tiết (nếu bạn cần tách riêng)
    public function replyForm($id) {
        $model = $this->model("ReviewModel");
        $review = $model->find('tbl_danhgia', $id);
        $this->view("adminPage", [
            "page" => "ReviewReplyView",
            "review" => $review
        ]);
    }

    // ✅ Lưu phản hồi
    public function saveReply() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $reply = trim($_POST['reply']);
            $model = $this->model("ReviewModel");
            $model->saveReply($id, $reply);
            header("Location: " . APP_URL . "/Review/show");
            exit;
        }
    }

    // ✅ Cập nhật phản hồi (dùng cho form trong danh sách)
    public function reply() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $traloi = trim($_POST['reply']);
            $obj = $this->model("ReviewModel");
            $obj->reply($id, $traloi);
            header("Location: " . APP_URL . "/Review/show");
            exit;
        }
    }
}
