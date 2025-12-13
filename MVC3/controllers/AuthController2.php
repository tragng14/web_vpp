    
<?php
require_once 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;

//session_start();

class AuthController2 extends Controller {
    // Hiển thị form đăng ký
    //http://localhost/MVC3/AuthController/Show
    public function Show() {
        $this->view("adminPage",["page"=>"RegisterView"]);
    }

    // Xử lý đăng ký, gửi OTP
    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $fullname = isset($_POST['fullname']) ? trim($_POST['fullname']) : '';
            $email = isset($_POST['email']) ? trim($_POST['email']) : '';
            $password = isset($_POST['password']) ? $_POST['password'] : '';

            if ($fullname === '' || $email === '' || $password === '') {
                echo '<div class="container mt-5"><div class="alert alert-danger">Vui lòng nhập đầy đủ thông tin!</div></div>';
                $this->view("adminPage",["page"=>"RegisterView"]);
                return;
            }

            // Tạo mã OTP
            $otp = rand(100000, 999999);
            $_SESSION['register'] = [
                'fullname' => $fullname,
                'email' => $email,
                'password' => password_hash($password, PASSWORD_DEFAULT),
                'otp' => $otp
            ];
            // Gửi OTP qua email
            $this->sendOtpEmail($email, $otp);

            // Hiển thị form nhập OTP
            $this->view("adminPage",["page"=>"OtpView"]);
        }
    }

    // Gửi OTP qua Gmail
    private function sendOtpEmail($email, $otp) {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'baochanbon@gmail.com'; // Thay bằng Gmail của bạn
            $mail->Password = 'sgjj pztp cdte iimu'; // Thay bằng App Password
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom('baochanbon@gmail.com', 'Your App');
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = "Mã OTP xác thực đăng ký";
            $mail->Body = "Mã OTP của bạn là: <b>$otp</b>";

            $mail->send();
        } catch (Exception $e) {
            echo "Gửi email thất bại: {$mail->ErrorInfo}";
        }
    }

    // Xác thực OTP
    public function verifyOtp() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $inputOtp = $_POST['otp'];
            if (isset($_SESSION['register']) && $_SESSION['register']['otp'] == $inputOtp) {
                // Lưu user vào DB
                $user = $this->model('UserModel');
                $email = $_SESSION['register']['email'];
                if ($user->emailExists($email)) {
                    echo '<div class="container mt-5"><div class="alert alert-danger">Email đã được đăng ký. Vui lòng sử dụng email khác!</div></div>';
                    unset($_SESSION['register']);
                    $this->view("homePage",["page"=>"RegisterView"]);
                    return;
                }
                $user->email = $email;
                $user->password = $_SESSION['register']['password'];
                $user->fullname = $_SESSION['register']['fullname'];
                $user->token = bin2hex(random_bytes(16));
                $user->create();
                unset($_SESSION['register']);
                echo '<div class="container mt-5"><div class="alert alert-success">Đăng ký thành viên thành công! Bạn có thể <a href="' . APP_URL . '/AuthController/ShowLogin" class="btn btn-success ms-2">Đăng nhập để đặt hàng</a></div></div>';
                // Hoặc tự động đăng nhập và chuyển sang trang đặt hàng nếu muốn
                // Tự động đăng nhập
                $_SESSION['user'] = [
                    'email' => $user->email,
                    'fullname' => $user->fullname
                ];

                // Điều hướng sau khi đăng ký thành công
                if (isset($_SESSION['redirect_after_login'])) {
                    $redirect = $_SESSION['redirect_after_login'];
                    unset($_SESSION['redirect_after_login']);
                    header("Location: $redirect");
                } else {
                    header('Location: ' . APP_URL . '/Admin');
                }
                exit();

            } else {
                echo '<div class="container mt-5"><div class="alert alert-danger">Mã OTP không đúng!</div></div>';
                $this->view("adminPage",["page"=>"OtpView"]);
            }
        }
    }


    // Hiển thị form đăng nhập
    public function ShowLogin() {
        // Nếu user đã đăng nhập rồi
        if (isset($_SESSION['user'])) {
            // Nếu có "redirect_after_login" thì quay lại đúng trang đó
            if (isset($_SESSION['redirect_after_login'])) {
                $redirect = $_SESSION['redirect_after_login'];
                unset($_SESSION['redirect_after_login']);
                header("Location: $redirect");
            } else {
                // Nếu không có redirect thì về trang chủ
                header('Location: ' . APP_URL . '/Admin');
            }
            exit();
        }

        // Nếu chưa đăng nhập thì hiển thị form login
        $this->view("adminPage", ["page" => "LoginView"]);
    }



    // Xử lý đăng nhập
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            $email = trim($_POST['email']);
            $password = $_POST['password'];
    
            $userModel = $this->model('AdminModel');
            $user = $userModel->getByEmail($email);
    
            if (!$user) {
    $_SESSION['error'] = "Email hoặc mật khẩu không đúng.";
    header("Location: " . APP_URL . "/AuthController2/ShowLogin");
    exit;
}

// Kiểm tra mật khẩu mã hóa trước
$hashedMatch = password_verify($password, $user['password']);

// Nếu không khớp, kiểm tra dạng text thường
$plainMatch = ($password === $user['password']);

if (!$hashedMatch && !$plainMatch) {
    $_SESSION['error'] = "Email hoặc mật khẩu không đúng.";
    header("Location: " . APP_URL . "/AuthController2/ShowLogin");
    exit;
}

if (!in_array($user['role'], ['admin', 'staff'])) {
    $_SESSION['error'] = "Tài khoản không có quyền truy cập.";
    header("Location: " . APP_URL . "/AuthController2/ShowLogin");
    exit;
}



            if ($user['status'] != 'Hoạt động') {
                $_SESSION['error'] = "Tài khoản đang bị khóa, không thể đăng nhập.";
                header("Location: " . APP_URL . "/AuthController2/ShowLogin");
                exit;
            }
    
            // Ghi session đúng định dạng
            $_SESSION['user'] = [
                'user_id' => $user['user_id'],
                'fullname' => $user['fullname'],
                'email' => $user['email'],
                'role' => $user['role'],
                   'avatar'    => $user['avatar'],   // <-- thêm
    'phone'     => $user['phone'],    // <-- thêm
    'address'   => $user['address'] 
            ];
  

            header("Location: " . APP_URL . "/Admin/show");
            exit;
        }
    }
    
    
    public function checkBeforeCheckout() {
    // Nếu chưa đăng nhập
    if (!isset($_SESSION['user'])) {
        // Nếu giỏ hàng rỗng thì cho về Home luôn, không cần login
        if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
            header('Location: ' . APP_URL . '/Home');
            exit();
        }
        // Lưu lại để login xong quay về giỏ hàng
        $_SESSION['redirect_after_login'] = APP_URL . '/Home/order';
        header('Location: ' . APP_URL . '/AuthController/ShowLogin');
        exit();
    } else {
        // Nếu đã đăng nhập thì đi checkout luôn
        header('Location: ' . APP_URL . '/Admin/checkoutInfo');
        exit();
    }
}


    // Đăng xuất
    public function logout() {
        unset($_SESSION['user']);

        header('Location: ' . APP_URL . '/Admin');
        exit();
    }

    // Hiển thị form quên mật khẩu
    public function forgotPassword() {
        //$this->view("Font_end/ForgotPasswordView");
        $this->view("adminPage",["page"=>"ForgotPasswordView"]);
    }

    // Xử lý gửi lại mật khẩu mới qua email
    public function resetPassword() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = isset($_POST['email']) ? trim($_POST['email']) : '';
            $userModel = $this->model('AdminModel');
            $stmt = $userModel->findByEmail($email);
            $user = $stmt ? $stmt->fetch(PDO::FETCH_ASSOC) : null;
            if ($user) {
                $newPass = substr(bin2hex(random_bytes(4)), 0, 8);
                // Lưu trực tiếp mật khẩu thường vào DB (không hash)
                $userModel->updatePassword($email, $newPass);

                $this->sendNewPasswordEmail($email, $newPass);
                echo '<div class="container mt-5"><div class="alert alert-success">Mật khẩu mới đã được gửi về email của bạn!</div></div>';
            } else {
                echo '<div class="container mt-5"><div class="alert alert-danger">Email không tồn tại!</div></div>';
            }
            //$this->view("Font_end/ForgotPasswordView");
             $this->view("adminPage",["page"=>"ForgotPasswordView"]);
            
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
            $mail->Password = 'sgjj pztp cdte iimu';
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;
            $mail->setFrom('baochanbon@gmail.com', 'Your App');
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = "Mật khẩu mới cho tài khoản của bạn";
            $mail->Body = "Mật khẩu mới của bạn là: <b>$newPass</b>";
            $mail->send();
        } catch (Exception $e) {
            // Không echo lỗi ra ngoài
        }
    }

}
