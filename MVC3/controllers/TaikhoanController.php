<?php
class TaikhoanController extends Controller{
    public function __construct() {
       $this->requireRole(['admin']);
    }
public function show() {

    
    $model = $this->model('AdminModel');

    // Lấy role
    $role = isset($_GET['role']) ? trim($_GET['role']) : "";

    // Lấy keyword
    $keyword = isset($_GET['keyword']) ? $_GET['keyword'] : "";

    // Nếu keyword là array → reset về chuỗi
    if (is_array($keyword)) $keyword = "";

    $users = $model->getAll2($keyword, $role);

    // TAB khách hàng
    $kw = isset($_GET['keyword']) ? $_GET['keyword'] : "";

    // Nếu keyword là array → reset về chuỗi
    if (is_array($kw)) $kw = "";

    $customers = $model->getCustomers($kw);

    $this->view("adminPage", [
        "page"  => "UserListView",
        "users" => $users,
        "keyword" => $keyword,
        "role" => $role,
        "customers" => $customers
    ]);
}



    // ✅ Hiển thị form thêm mới & xử lý thêm tài khoản
public function create() {
    $adminModel = $this->model("AdminModel");

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $fullname = trim($_POST['fullname']);
        $email = trim($_POST['email']);
         $phone = trim($_POST['phone']);
          $address = trim($_POST['address']);
        $password = trim($_POST['password']);
        $role = $_POST['role'];
        $status = $_POST['status'];

        // Kiểm tra dữ liệu bắt buộc
        if (empty($fullname) || empty($email) || empty($password)) {
            $_SESSION['error'] = "Vui lòng nhập đầy đủ họ tên, email và mật khẩu!";
            $this->view("adminPage", ["page" => "EditUser"]);
            return;
        }

        // Kiểm tra email đã tồn tại chưa
        if ($adminModel->getByEmail($email)) {
            $_SESSION['error'] = "Email này đã được sử dụng!";
            $this->view("adminPage", ["page" => "EditUser"]);
            return;
        }

        // Mã hóa mật khẩu
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Xử lý avatar upload
// Xử lý avatar upload
$avatar = null;

if (!empty($_FILES['avatar']['name'])) {

    $uploadDir = "public/images/avatars/";

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // giữ nguyên tên gốc
    $originalName = basename($_FILES['avatar']['name']);
    
    // chống trùng tên
    $targetPath = $uploadDir . $originalName;
    $pathInfo = pathinfo($originalName);
    $count = 1;

    while (file_exists($targetPath)) {
        $targetPath = $uploadDir . $pathInfo['filename'] . "_" . $count . "." . $pathInfo['extension'];
        $count++;
    }

    if (move_uploaded_file($_FILES['avatar']['tmp_name'], $targetPath)) {
        $avatar = basename($targetPath); 
    }
}


        // Gọi model thêm mới
       if ($adminModel->create($fullname, $email, $hashedPassword, $role, $status, $avatar,$phone,$address)) {

            $_SESSION['success'] = "Thêm tài khoản mới thành công!";
            header("Location: " . APP_URL . "/TaiKhoan/show");
            exit;
        } else {
            $_SESSION['error'] = "Thêm tài khoản thất bại!";
        }
    }

    // Hiển thị form thêm mới (dùng lại view form chung)
    $this->view("adminPage", ["page" => "EditUser"]);
}
    public function editRole($id) {
        $model = $this->model('AdminModel');
        $model->updateRole($id, 'admin');
        $_SESSION['success'] = "Cấp quyền admin thành công!";
        header("Location: " . APP_URL . "/TaiKhoan/show");
    }

    public function revokeRole($id) {
        $model = $this->model('AdminModel');
        $model->updateRole($id, 'staff');
        $_SESSION['success'] = "Hạ quyền thành staff thành công!";
        header("Location: " . APP_URL . "/TaiKhoan/show");
    }

    public function delete($id) {
        $model = $this->model('AdminModel');
    
        // 🔒 Không cho phép xóa chính tài khoản đang đăng nhập
        if (isset($_SESSION['user']) && $_SESSION['user']['user_id'] == $id) {
            $_SESSION['error'] = "Bạn không thể xóa tài khoản đang đăng nhập!";
            header("Location: " . APP_URL . "/TaiKhoan/show");
            exit;
        }
    
        // ✅ Cho phép xóa tài khoản admin khác (không phải mình)
        $targetUser = $model->getUserById($id);
        if (!$targetUser) {
            $_SESSION['error'] = "Tài khoản không tồn tại!";
            header("Location: " . APP_URL . "/TaiKhoan/show");
            exit;
        }
    
        // ⚙️ Thực hiện xóa (đánh dấu is_deleted = 1)
        if ($model->deleteUser($id)) {
            $_SESSION['success'] = "Đã xóa tài khoản: " . htmlspecialchars($targetUser['fullname']);
        } else {
            $_SESSION['error'] = "Xóa tài khoản thất bại!";
        }
    
        header("Location: " . APP_URL . "/TaiKhoan/show");
    }
    

    
    // Trong class Admin
public function edit($userId) {
    $adminModel = $this->model("AdminModel");

    // Lấy thông tin user theo id (dùng để hiển thị form khi GET)
    $user = $adminModel->getUserById($userId);

    // Nếu không tìm thấy user -> chuyển về danh sách
    if (!$user) {
        $_SESSION['error'] = "Không tìm thấy tài khoản.";
        header("Location: " . APP_URL . "/TaiKhoan/show");
        exit;
    }

    // Nếu POST -> xử lý cập nhật
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $fullname = isset($_POST['fullname']) ? trim($_POST['fullname']) : '';
        $password = isset($_POST['password']) ? trim($_POST['password']) : '';
        $email = isset($_POST['email']) ? trim($_POST['email']) : $user['email'];
        $role = isset($_POST['role']) ? $_POST['role'] : $user['role'];
        $status = isset($_POST['status']) ? $_POST['status'] : $user['status'];
        $phone = isset($_POST['phone']) ? trim($_POST['phone']) : $user['phone'];
        $address = isset($_POST['address']) ? trim($_POST['address']) : $user['address'];

        // Nếu admin nhập mật khẩu mới -> mã hóa, ngược lại giữ mật khẩu cũ
        if (!empty($password)) {
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        } else {
            $passwordHash = $user['password'];
        }

        // Gọi model để cập nhật (hàm updateUser trong AdminModel cần có tham số phù hợp)
    $avatar = $user['avatar']; // giữ avatar cũ

if (!empty($_FILES['avatar']['name'])) {

    $uploadDir = "public/images/avatars/";

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $originalName = basename($_FILES['avatar']['name']);
    $targetPath = $uploadDir . $originalName;

    $pathInfo = pathinfo($originalName);
    $count = 1;

    while (file_exists($targetPath)) {
        $targetPath = $uploadDir . $pathInfo['filename'] . "_" . $count . "." . $pathInfo['extension'];
        $count++;
    }

    if (move_uploaded_file($_FILES['avatar']['tmp_name'], $targetPath)) {
        $avatar = basename($targetPath);
    }
}


       $ok = $adminModel->updateUser($userId, $fullname, $email, $role, $status, $avatar,$password,$phone,$address);

        if ($ok) {
            $_SESSION['success'] = "Cập nhật tài khoản thành công!";
            header("Location: " . APP_URL . "/TaiKhoan/show");
            exit;
        } else {
            $_SESSION['error'] = "Cập nhật không thành công, hãy thử lại.";
            // tải lại view với dữ liệu cũ (để giữ giá trị đã nhập)
            $user = $adminModel->getUserById($userId);
            $this->view("adminPage", ["page" => "EditUser", "editUser" => $user]);
            return;
        }
    }

    // Nếu GET -> hiển thị form, truyền key 'editUser' để form biết là sửa
    $this->view("adminPage", ["page" => "EditUser", "editUser" => $user]);
}

    public function resetPassword($userId) {
        $adminModel = $this->model("AdminModel");
    
        // Lấy thông tin người dùng để hiển thị form
        $user = $adminModel->getUserById($userId);
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $newPassword = trim($_POST['new_password']);
    
            if (empty($newPassword)) {
                $_SESSION['error'] = "Vui lòng nhập mật khẩu mới!";
            } else {
                if ($adminModel->resetPasswordByAdmin($userId, $newPassword)) {
                    $_SESSION['success'] = "Đặt lại mật khẩu thành công!";
                    header("Location: " . APP_URL . "/TaiKhoan/show");
                    exit;
                } else {
                    $_SESSION['error'] = "Có lỗi xảy ra khi đặt lại mật khẩu!";
                }
            }
        }
    
        // Hiển thị form reset mật khẩu riêng
        $this->view("adminPage", ["page" => "ResetPasswordView", "user" => $user]);
    }
    public function restoreUser($id) {
        $model = $this->model('AdminModel');
    
        // Nếu tài khoản chưa bị xóa thì không khôi phục
        if (!$model->isUserDeleted($id)) {
            $_SESSION['error'] = "Tài khoản này chưa bị xóa, không thể khôi phục!";
            header("Location: " . APP_URL . "/TaiKhoan/show");
            exit;
        }
    
        if ($model->restoreUser($id)) {
            $_SESSION['success'] = "Khôi phục tài khoản thành công!";
        } else {
            $_SESSION['error'] = "Không thể khôi phục tài khoản!";
        }
    
        header("Location: " . APP_URL . "/TaiKhoan/show");
    }
    
}