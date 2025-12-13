<?php
class Admin extends Controller {

    public function __construct() {
       $this->requireRole(['admin', 'staff']);

    }

    // TRANG DASHBOARD CHÍNH
    public function show() {

    $report = $this->model("ReportModel");

    $date = $_GET['date'] ?? date("Y-m-d");
    $filterType = $_GET['type'] ?? "day";

    // Lấy dữ liệu biểu đồ
    $summary = $report->getSummary($date, $filterType);
    $revenueByCategory = $report->getRevenueByCategory($date, $filterType);
    $revenueByTime = $report->getRevenueByTime($date, $filterType);
    $topProducts = $report->getTopProducts($date, $filterType);

    $this->view("adminPage", [
        "page" => "DashboardView",
        "summary" => $summary,
        "categoryRevenue" => $revenueByCategory,
        "chartTime" => $revenueByTime,
        "topProducts" => $topProducts,
        "filterType" => $filterType,
        "date" => $date
    ]);
}

public function profile() {
    $this->view("adminPage", [
        "page" => "admin_profile",
        "title" => "Thông tin cá nhân"
    ]);
}

public function updateProfile() {
    if (!isset($_SESSION['user'])) {
        header("Location: " . APP_URL . "/AuthController2/ShowLogin");
        exit;
    }

    $fullname = $_POST['fullname'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $address = $_POST['address'] ?? '';

    $userId = $_SESSION['user']['user_id'];
    $userModel = $this->model("AdminModel");

    $avatarName = $_SESSION['user']['avatar'] ?? 'default.png';

    /* =====================================================
        XỬ LÝ UPLOAD AVATAR
    ===================================================== */

    if (!empty($_FILES['avatar']['name'])) {

        $file = $_FILES['avatar'];
        $newName = time() . "_" . basename($file['name']);

        // ĐƯỜNG DẪN TUYỆT ĐỐI CHÍNH XÁC
        $avatarDir = $_SERVER['DOCUMENT_ROOT'] . "/MVC3/public/images/avatars/";

        // Tạo thư mục nếu chưa có
        if (!file_exists($avatarDir)) {
            mkdir($avatarDir, 0777, true);
        }

        // Kiểm tra quyền ghi
        if (!is_writable($avatarDir)) {
            echo "<script>alert('Thư mục avatar KHÔNG có quyền ghi!'); history.back();</script>";
            exit;
        }

        $uploadPath = $avatarDir . $newName;

        if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
            $avatarName = $newName;
        } else {
            echo "<script>alert('Upload ảnh thất bại!'); history.back();</script>";
            exit;
        }
    }

    $_SESSION['user']['avatar'] = $avatarName;

    /* =====================================================
        UPDATE DATABASE
    ===================================================== */

    if ($userModel->updateProfile($userId, $fullname, $phone, $address, $avatarName)) {

        $_SESSION['user']['fullname'] = $fullname;
        $_SESSION['user']['phone']    = $phone;
        $_SESSION['user']['address']  = $address;

        echo "<script>alert('Cập nhật thành công'); window.location='" . APP_URL . "/Admin/profile';</script>";
    } else {
        echo "<script>alert('Cập nhật thất bại'); history.back();</script>";
    }
}

public function changePassword() {
    if (!isset($_SESSION['user'])) {
        header("Location: " . APP_URL . "/AuthController2/ShowLogin");
        exit;
    }

    $old = $_POST['old_password'] ?? '';
    $new = $_POST['new_password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    $userId = $_SESSION['user']['user_id'];
    $userModel = $this->model("AdminModel");

    // Lấy user
    $user = $userModel->getUserById($userId);

    // Nếu mật khẩu mới không khớp
    if ($new !== $confirm) {
        $this->view("adminPage", [
            "page" => "admin_profile",
            "title" => "Thông tin cá nhân",
            "error" => "Mật khẩu mới không khớp!"
        ]);
        return;
    }

    // Check mật khẩu cũ
    if (!$user || !password_verify($old, $user['password'])) {
        $this->view("adminPage", [
            "page" => "admin_profile",
            "title" => "Thông tin cá nhân",
            "error" => "Mật khẩu hiện tại không chính xác!"
        ]);
        return;
    }

    // Hash mật khẩu mới
    $hashed = password_hash($new, PASSWORD_BCRYPT);

    // Cập nhật DB
    if ($userModel->updatePassword($userId, $hashed)) {
        $this->view("adminPage", [
            "page" => "admin_profile",
            "title" => "Thông tin cá nhân",
            "success" => "Đổi mật khẩu thành công!"
        ]);
    } else {
        $this->view("adminPage", [
            "page" => "admin_profile",
            "title" => "Thông tin cá nhân",
            "error" => "Đổi mật khẩu thất bại!"
        ]);
    }
}



    
}
