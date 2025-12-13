<?php
// controllers/AuthController.php

require_once 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


if (!defined('MAIL_APP_PASSWORD')) {
    // Thay chuỗi này bằng App Password thật (KHÔNG có dấu cách).
    define('MAIL_APP_PASSWORD', 'sgjjpztpcdteiimu');
}

class AuthController extends Controller {

        public function __construct() {
        if (isset($_SESSION['user'])) {
    
            $userId = $_SESSION['user']['user_id'];
            $userModel = $this->model("UserModel");
    
            // Lấy dữ liệu user (hàm này trả về ARRAY chứ không phải PDOStatement)
            $data = $userModel->getUserById($userId);
    
            // Nếu không tìm thấy user hoặc user bị khóa/xóa
            if (!$data || $data['status'] === 'Tạm ngưng' || $data['is_deleted'] == 1) {
                session_destroy();
                header("Location: " . APP_URL . "/Home/");
                exit();
            }
        }
    }

    // Hiển thị form đăng ký
public function catchRedirect() {
    if (isset($_GET['to'])) {
        $_SESSION['redirect_after_login'] = $_GET['to'];
    }
    header("Location: " . APP_URL . "/AuthController/ShowLogin");
    exit();
}

    public function Show() {

      $pagesModel = $this->model("PageModel");
    $pagesList = $pagesModel->getAllActive();
$contactPage = $pagesModel->getById(5);
$obj = $this->model("News"); 
    $newsList = $obj->all("news"); 
    $visibleNews = array_filter($newsList, function ($item) {
        return isset($item['status']) && 
               ($item['status'] == 1 || strtolower(trim($item['status'])) == 'hiển thị');
    });

        $this->view("homePage", 
        ["page" => "RegisterView",
        "NewsList" => $visibleNews,
         "contactPage" => $contactPage,
        "pagesList" => $pagesList
    
    ]);
    }


    // Xử lý đăng ký, gửi OTP
    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $fullname = isset($_POST['fullname']) ? trim($_POST['fullname']) : '';
            $email = isset($_POST['email']) ? trim($_POST['email']) : '';
            $password = isset($_POST['password']) ? $_POST['password'] : '';

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

            if ($fullname === '' || $email === '' || $password === '') {
                echo '<div class="container mt-5"><div class="alert alert-danger">Vui lòng nhập đầy đủ thông tin!</div></div>';
                $this->view("homePage", [
                    "NewsList" => $visibleNews,
         "contactPage" => $contactPage,
        "pagesList" => $pagesList,
                    "page" => "RegisterView"]);
                return;
            }
 // ================================
        // 🔥 KIỂM TRA EMAIL ĐÃ TỒN TẠI CHƯA
        // ================================
        $userModel = $this->model("UserModel"); // hoặc UserModel tùy bạn dùng gì

        if ($userModel->emailExists($email)) {
            echo '<div class="container mt-5"><div class="alert alert-danger">
                    Email này đã tồn tại trong hệ thống! Vui lòng sử dụng email khác.
                  </div></div>';
            $this->view("homePage", [
                 "NewsList" => $visibleNews,
         "contactPage" => $contactPage,
        "pagesList" => $pagesList,
        "page" => "RegisterView"]);
            return;
        }
            // Tạo mã OTP
            $otp = rand(100000, 999999);
            $_SESSION['register'] = [
                'fullname' => $fullname,
                'email' => $email,
                'password' => password_hash($password, PASSWORD_DEFAULT),
                'otp' => $otp,
                'otp_generated_at' => time()
            ];

            // Gửi OTP qua email
            $this->sendOtpEmail($email, $otp);

            // Hiển thị form nhập OTP
              $this->view("homePage", [
                 "NewsList" => $visibleNews,
         "contactPage" => $contactPage,
        "pagesList" => $pagesList,
        "page" => "OtpView"]);
        }
    }

    // Gửi OTP qua Gmail
    private function sendOtpEmail($email, $otp) {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'baochanbon@gmail.com';
            // LẤY PASSWORD: ưu tiên hằng định nghĩa, nếu null thì getenv
            $pw = MAIL_APP_PASSWORD;
            if (!$pw) {
                $pw = getenv('MAIL_APP_PASSWORD') ?: '';
            }
            $mail->Password = $pw;
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            // Một vài option an toàn (tránh lỗi TLS trên môi trường dev)
            $mail->SMTPOptions = [
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                ]
            ];

            $mail->setFrom('baochanbon@gmail.com', 'Your App');
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = "Mã OTP xác thực đăng ký";
            $mail->Body = "Mã OTP của bạn là: <b>$otp</b><br><small>Không chia sẻ mã này với người khác.</small>";

            $mail->send();
            // không in ra khi thành công
        } catch (Exception $e) {
            error_log("PHPMailer OTP send error: " . $e->getMessage());
            // Hiển thị cảnh báo nhẹ cho user
            echo '<div class="container mt-3"><div class="alert alert-warning">Không gửi được email xác thực. Vui lòng thử lại hoặc kiểm tra cấu hình email.</div></div>';
        }
    }

    // Xác thực OTP
    public function verifyOtp() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $inputOtp = isset($_POST['otp']) ? trim($_POST['otp']) : '';

            if (!isset($_SESSION['register'])) {
                echo '<div class="container mt-5"><div class="alert alert-danger">Phiên đăng ký đã hết hạn. Vui lòng đăng ký lại.</div></div>';
                $this->view("homePage", ["page" => "RegisterView"]);
                return;
            }

            // Kiểm tra thời gian OTP (ví dụ 10 phút)
            if (isset($_SESSION['register']['otp_generated_at']) && (time() - $_SESSION['register']['otp_generated_at']) > 600) {
                unset($_SESSION['register']);
                echo '<div class="container mt-5"><div class="alert alert-danger">Mã OTP đã hết hạn. Vui lòng đăng ký lại.</div></div>';
                $this->view("homePage", ["page" => "RegisterView"]);
                return;
            }

            if ($_SESSION['register']['otp'] == $inputOtp) {
                // Lưu user vào DB
                $userModel = $this->model('UserModel'); // dùng UserModel để nhất quán với login
                $email = $_SESSION['register']['email'];

                // Kiểm tra email tồn tại bằng getByEmail (tương thích với UserModel)
                $existing = $userModel->getByEmail($email);
                if ($existing) {
                    echo '<div class="container mt-5"><div class="alert alert-danger">Email đã được đăng ký. Vui lòng sử dụng email khác!</div></div>';
                    unset($_SESSION['register']);
                    $this->view("homePage", ["page" => "RegisterView"]);
                    return;
                }

                // Gọi create theo signature: create($fullname, $email, $password, $role = "user")
                $created = $userModel->create(
                    $_SESSION['register']['fullname'],
                    $_SESSION['register']['email'],
                    $_SESSION['register']['password'],
                    'user' // hoặc 'admin' nếu bạn muốn tạo admin qua form này
                );

                unset($_SESSION['register']);

                if ($created) {
                    // Tự động đăng nhập
                    $user = $userModel->getByEmail($email);
                    if ($user) {
                        $_SESSION['user'] = [
                            'user_id' => $user['user_id'],
                            'email' => $user['email'],
                            'fullname' => $user['fullname'],
                            'role' => $user['role'] ?? 'user',
                          
    'phone' => $user['phone'] ?? '',
    'address' => $user['address'] ?? '',
    'avatar' => $user['avatar'] ?? 'default.png'
                        ];
                        // --- THÊM DÒNG NÀY ĐỂ TƯƠNG THÍCH VỚI CÁC VIEW/CONTROLLER DÙNG $_SESSION['user_id']
                        $_SESSION['user_id'] = $user['user_id'];
                        // --- KẾT THÚC THÊM DÒNG
                    }

                    // Điều hướng sau khi đăng ký thành công
                    if (isset($_SESSION['redirect_after_login'])) {
                        $redirect = $_SESSION['redirect_after_login'];
                        unset($_SESSION['redirect_after_login']);
                        header("Location: $redirect");
                    } else {
                        header('Location: ' . APP_URL . '/Home');
                    }
                    exit();
                } else {
                    echo '<div class="container mt-5"><div class="alert alert-danger">Không thể tạo tài khoản. Vui lòng thử lại.</div></div>';
                    $this->view("homePage", ["page" => "RegisterView"]);
                    return;
                }

            } else {
                echo '<div class="container mt-5"><div class="alert alert-danger">Mã OTP không đúng!</div></div>';
                $this->view("homePage", ["page" => "OtpView"]);
            }
        }
    }

    // Hiển thị form đăng nhập
    public function ShowLogin() {
        // Nếu user đã đăng nhập rồi
        if (isset($_SESSION['user'])) {
            if (isset($_SESSION['redirect_after_login'])) {
                $redirect = $_SESSION['redirect_after_login'];
                unset($_SESSION['redirect_after_login']);
                header("Location: $redirect");
            } else {
                header('Location: ' . APP_URL . '/Home');
            }
            exit();
        }
          $pagesModel = $this->model("PageModel");
    $pagesList = $pagesModel->getAllActive();
$contactPage = $pagesModel->getById(5);
$obj = $this->model("News"); 
    $newsList = $obj->all("news"); 
    $visibleNews = array_filter($newsList, function ($item) {
        return isset($item['status']) && 
               ($item['status'] == 1 || strtolower(trim($item['status'])) == 'hiển thị');
    });

        $this->view("homePage", 
        ["page" => "LoginView",
        "NewsList" => $visibleNews,
         "contactPage" => $contactPage,
        "pagesList" => $pagesList
    
    ]);
    }

    // Xử lý đăng nhập
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $email = trim($_POST['email']);
            $password = $_POST['password'];

            $userModel = $this->model('UserModel');
            $user = $userModel->getByEmail($email);

              $pagesModel = $this->model("PageModel");
    $pagesList = $pagesModel->getAllActive();
$contactPage = $pagesModel->getById(5);
$obj = $this->model("News"); 
    $newsList = $obj->all("news"); 
    $visibleNews = array_filter($newsList, function ($item) {
        return isset($item['status']) && 
               ($item['status'] == 1 || strtolower(trim($item['status'])) == 'hiển thị');
    });
 if (!$user || !password_verify($password, $user['password'])) {
                echo '<div class="container mt-5"><div class="alert alert-danger">Email hoặc mật khẩu không đúng!</div></div>';
                $this->view("homePage", 
                ["page" => "LoginView",
                   "NewsList" => $visibleNews,
         "contactPage" => $contactPage,
        "pagesList" => $pagesList
            ]);
                return;
            }

            // Đã xóa
            if (!empty($user['is_deleted']) && $user['is_deleted'] == 1) {
                echo '<div class="container mt-5"><div class="alert alert-danger">Tài khoản này đã bị xóa.</div></div>';
                $this->view("homePage",   ["page" => "LoginView",
                   "NewsList" => $visibleNews,
         "contactPage" => $contactPage,
        "pagesList" => $pagesList
    ]);
                return;
            }

            // Tạm ngưng
            if ($user['status'] === 'Tạm ngưng') {
                echo '<div class="container mt-5"><div class="alert alert-warning">Tài khoản của bạn đang bị tạm ngưng!</div></div>';
                $this->view("homePage", ["page" => "LoginView",
                   "NewsList" => $visibleNews,
         "contactPage" => $contactPage,
        "pagesList" => $pagesList
            ]);
                return;
            }

            // SESSION LOGIN
$_SESSION['user'] = [
    'user_id' => $user['user_id'],
    'fullname' => $user['fullname'],
    'email' => $user['email'],
    'phone' => $user['phone'] ?? '',
    'address' => $user['address'] ?? '',
    'avatar' => $user['avatar'] ?? 'default.png'
];


            // Chặn admin
            if ($user['role'] !== 'user') {
                echo '<div class="container mt-5"><div class="alert alert-danger">Không được truy cập khu vực khách hàng!</div></div>';
                unset($_SESSION['user']);
                $this->view("homePage", ["page" => "LoginView",
            "NewsList" => $visibleNews,
         "contactPage" => $contactPage,
        "pagesList" => $pagesList]);
                return;
            }


            // Ghi session đúng định dạng
            $_SESSION['user'] = [
                'user_id' => $user['user_id'],
                'fullname' => $user['fullname'],
                'email' => $user['email'],
                'role' => $user['role'],
               
    'phone' => $user['phone'] ?? '',
    'address' => $user['address'] ?? '',
    'avatar' => $user['avatar'] ?? 'default.png'
            ];

            // --- THÊM DÒNG NÀY ĐỂ ĐẢM BẢO CONTROLLER PROFILE LẤY ĐƯỢC user_id ---
            $_SESSION['user_id'] = $user['user_id'];
            // --- KẾT THÚC THÊM DÒNG ---

            // Nếu có trang cần quay lại
if (isset($_SESSION['redirect_after_login'])) {
    $redirect = $_SESSION['redirect_after_login'];
    unset($_SESSION['redirect_after_login']);
    header("Location: $redirect");
    exit();
}

// Nếu không có thì về trang chủ
header("Location: " . APP_URL . "/Home");
exit();

        }
    }

    // Kiểm tra trước khi checkout (giữ nguyên logic của bạn)
    public function checkBeforeCheckout() {
        if (!isset($_SESSION['user'])) {
            if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
                header('Location: ' . APP_URL . '/Home');
                exit();
            }
            $_SESSION['redirect_after_login'] = APP_URL . '/Home/order';
            header('Location: ' . APP_URL . '/AuthController/ShowLogin');
            exit();
        } else {
            header('Location: ' . APP_URL . '/Home/checkoutInfo');
            exit();
        }
    }

    // Đăng xuất
    public function logout() {
        if (session_status() === PHP_SESSION_ACTIVE) {
        unset($_SESSION['user']);

        }
        header('Location: ' . APP_URL . '/Home');
        exit;
    }

    // Hiển thị form quên mật khẩu
    public function forgotPassword() {
  $pagesModel = $this->model("PageModel");
    $pagesList = $pagesModel->getAllActive();
$contactPage = $pagesModel->getById(5);
$obj = $this->model("News"); 
    $newsList = $obj->all("news"); 
    $visibleNews = array_filter($newsList, function ($item) {
        return isset($item['status']) && 
               ($item['status'] == 1 || strtolower(trim($item['status'])) == 'hiển thị');
    });

        $this->view("homePage", 
        ["page" => "ForgotPasswordView",
        "NewsList" => $visibleNews,
         "contactPage" => $contactPage,
        "pagesList" => $pagesList
    
    ]);
    }

    // Xử lý gửi lại mật khẩu mới qua email
    public function resetPassword() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = isset($_POST['email']) ? trim($_POST['email']) : '';
            $userModel = $this->model('UserModel');

            $user = $userModel->getByEmail($email);

            if ($user) {
                $newPass = substr(bin2hex(random_bytes(4)), 0, 8);

// HASH mật khẩu trước khi lưu
$hashed = password_hash($newPass, PASSWORD_DEFAULT);

if (method_exists($userModel, 'updatePassword')) {
    $userModel->updatePassword($email, $hashed);
}


                $this->sendNewPasswordEmail($email, $newPass);
                echo '<div class="container mt-5"><div class="alert alert-success">Mật khẩu mới đã được gửi về email của bạn!</div></div>';
            } else {
                echo '<div class="container mt-5"><div class="alert alert-danger">Email không tồn tại!</div></div>';
            }

            $this->view("homePage", ["page" => "ForgotPasswordView"]);
        }
    }

    // Gửi mật khẩu mới qua email
    private function sendNewPasswordEmail($email, $newPass) {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'baochanbon@gmail.com';

            $pw = MAIL_APP_PASSWORD;
            if (!$pw) {
                $pw = getenv('MAIL_APP_PASSWORD') ?: '';
            }
            $mail->Password = $pw;

            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->SMTPOptions = [
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                ]
            ];

            $mail->setFrom('baochanbon@gmail.com', 'Your App');
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = "Mật khẩu mới cho tài khoản của bạn";
            $mail->Body = "Mật khẩu mới của bạn là: <b>$newPass</b>";
            $mail->send();
        } catch (Exception $e) {
            error_log("PHPMailer reset password error: " . $e->getMessage());
            // Không hiện lỗi chi tiết cho user
        }
    }

}
