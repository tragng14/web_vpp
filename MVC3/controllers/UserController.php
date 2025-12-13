<?php
// controllers/UserController.php
// Phiên bản chỉnh sửa nhẹ — chỉ sửa những gì cần thiết và giữ lại ghi chú.
// Ghi chú (tiếng Việt):
// - Bảo đảm session được start trong __construct.
// - Đường dẫn upload avatar dùng __DIR__ để phù hợp cấu trúc controllers/.
// - Có fallback nếu random_bytes() không có (hosting rất cũ).
// - Thông báo lỗi/redirect bằng tiếng Việt.
// - Tạo thumbnail nếu extension GD có sẵn.
// - Kiểm tra MIME với fileinfo.

// Lưu ý: UserModel phải cung cấp:
// - getUserById($id)
// - updateProfile($user_id, $updateData)
// - updatePasswordById($user_id, $hashedPassword)
// - deleteUser($user_id)

class UserController extends Controller {

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Hiển thị trang hồ sơ người dùng
     */
    // Thay thế method show() trong controllers/UserController.php bằng đoạn này:

public function show() {
    if (!isset($_SESSION['user'])) {
        header("Location: " . APP_URL . "/AuthController/login");
        exit;
    }

    $userModel = $this->model("UserModel");
    $user_id = $_SESSION['user']['user_id'] ?? null;

    if (!$user_id) {
        echo "<script>alert('Không tìm thấy tài khoản trong phiên làm việc. Vui lòng đăng nhập lại!');
              window.location='" . APP_URL . "/AuthController/login';</script>";
        exit;
    }

    $user = $userModel->getUserById($user_id);
    if (!$user) {
        echo "<script>alert('Không tìm thấy thông tin người dùng.'); window.location='" . APP_URL . "/Home';</script>";
        exit;
    }

    // --- BẮT ĐẦU: Nạp dữ liệu dùng chung cho layout (categories, pagesList, contactPage, NewsList, banners)
    $common = [
        'categories'  => [],
        'pagesList'   => [],
        'contactPage' => null,
        'NewsList'    => [],
        'banners'     => []
    ];

    // pagesList / contactPage
    try {
        $pagesModel = $this->model("PageModel");
        if ($pagesModel && method_exists($pagesModel, 'getAllActive')) {
            $common['pagesList'] = $pagesModel->getAllActive() ?: [];
        } elseif ($pagesModel && method_exists($pagesModel, 'all')) {
            $common['pagesList'] = $pagesModel->all() ?: [];
        }

        if ($pagesModel && method_exists($pagesModel, 'getById')) {
            $common['contactPage'] = $pagesModel->getById(5) ?: null;
        }
    } catch (Throwable $e) {
        // ignore lỗi, giữ mặc định rỗng
    }

    // categories
    try {
        $typeModel = $this->model("AdProductTypeModel");
        if ($typeModel && method_exists($typeModel, 'all')) {
            $cats = $typeModel->all("tblloaisp");
            $common['categories'] = is_array($cats) ? $cats : [];
        }
    } catch (Throwable $e) {
        // ignore
    }

    // news
    try {
        $newsModel = $this->model("News");
        if ($newsModel && method_exists($newsModel, 'all')) {
            $newsList = $newsModel->all("news") ?: [];
            // nếu cần chỉ lấy news hiển thị
            $visible = array_filter($newsList, function($it){
                return isset($it['status']) && ($it['status'] == 1 || mb_strtolower(trim((string)$it['status'])) === 'hiển thị');
            });
            $common['NewsList'] = array_values($visible);
        }
    } catch (Throwable $e) {
        // ignore
    }

    // banners
    try {
        $bannerModel = $this->model("BannerModel");
        if ($bannerModel && method_exists($bannerModel, 'getActiveBanners')) {
            $common['banners'] = $bannerModel->getActiveBanners() ?: [];
        }
    } catch (Throwable $e) {
        // ignore
    }
    // --- KẾT THÚC: Nạp dữ liệu dùng chung

    // Merge dữ liệu chung và dữ liệu của trang profile rồi render
    $viewData = array_merge($common, [
        "page" => "UserProfileView",
        "user" => $user
    ]);

    $this->view("homePage", $viewData);
}


    /**
     * Xử lý cập nhật profile, upload avatar, fullname, phone, address
     * - POST only
     */
    public function updateProfile() {

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: " . APP_URL . "/User/show");
            exit;
        }

        if (!isset($_SESSION['user'])) {
            header("Location: " . APP_URL . "/AuthController/login");
            exit;
        }

        $user_id = (int)($_SESSION['user']['user_id']);

        // Lấy dữ liệu từ form (có thể rỗng)
        $fullname = array_key_exists('fullname', $_POST) ? trim($_POST['fullname']) : null;
        $phone = array_key_exists('phone', $_POST) ? trim($_POST['phone']) : null;
        $address = array_key_exists('address', $_POST) ? trim($_POST['address']) : null;

        $userModel = $this->model("UserModel");

        // Xử lý upload avatar (nếu có)
        $avatarField = $_FILES['avatar'] ?? null;
        $newAvatarFileName = null;

        // Thư mục upload: đường dẫn chuẩn dựa trên file controllers
        $uploadDir = __DIR__ . '/../public/images/avatars/';

        if (!is_dir($uploadDir)) {
            @mkdir($uploadDir, 0755, true);
        }

        if ($avatarField && $avatarField['error'] !== UPLOAD_ERR_NO_FILE) {
            $maxFileSize = 2 * 1024 * 1024; // 2MB
            $allowedMime = ['image/jpeg','image/png','image/webp'];

            if ($avatarField['error'] !== UPLOAD_ERR_OK) {
                echo "<script>alert('Lỗi khi tải ảnh lên (mã lỗi: {$avatarField['error']}).'); window.location='" . APP_URL . "/User/show';</script>";
                exit;
            }

            if ($avatarField['size'] > $maxFileSize) {
                echo "<script>alert('Kích thước ảnh quá lớn (tối đa 2MB).'); window.location='" . APP_URL . "/User/show';</script>";
                exit;
            }

            // kiểm tra mime bằng fileinfo nếu có
            if (function_exists('finfo_open')) {
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mime = finfo_file($finfo, $avatarField['tmp_name']);
                finfo_close($finfo);
            } else {
                // fallback: cố gắng lấy từ getimagesize
                $info = @getimagesize($avatarField['tmp_name']);
                $mime = $info['mime'] ?? '';
            }

            if (!in_array($mime, $allowedMime, true)) {
                echo "<script>alert('Chỉ chấp nhận định dạng JPG / PNG / WEBP.'); window.location='" . APP_URL . "/User/show';</script>";
                exit;
            }

            if (getimagesize($avatarField['tmp_name']) === false) {
                echo "<script>alert('Tệp tải lên không phải ảnh hợp lệ.'); window.location='" . APP_URL . "/User/show';</script>";
                exit;
            }

            // Tạo tên file an toàn
            $originalName = basename($avatarField['name']);
            $pathInfo = pathinfo($originalName);
            $ext = isset($pathInfo['extension']) ? strtolower($pathInfo['extension']) : 'jpg';
            $base = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $pathInfo['filename']);

            // fallback random nếu random_bytes không có
            try {
                $rand = bin2hex(random_bytes(4));
            } catch (Throwable $e) {
                $rand = dechex(mt_rand(0, 0xfffffff));
            } catch (Exception $e) {
                $rand = dechex(mt_rand(0, 0xfffffff));
            }

            $newAvatarFileName = $base . '_' . time() . '_' . $rand . '.' . $ext;
            $destination = $uploadDir . $newAvatarFileName;

            if (!move_uploaded_file($avatarField['tmp_name'], $destination)) {
                echo "<script>alert('Không thể lưu file ảnh trên server.'); window.location='" . APP_URL . "/User/show';</script>";
                exit;
            }

            // Tạo thumbnail nếu GD khả dụng
            $thumbPath = $uploadDir . 'thumb_' . $newAvatarFileName;
            $this->createThumbnail($destination, $thumbPath, 200, 200);
        }

        // Chuẩn bị dữ liệu để cập nhật (chỉ include các field thay đổi)
        $updateData = [];

        if ($fullname !== null && $fullname !== '') {
            $updateData['fullname'] = $fullname;
        }

        // Xử lý phone: cho phép xóa (gửi chuỗi rỗng) hoặc validate
        if ($phone !== null) {
            $phoneTrim = trim($phone);
            if ($phoneTrim === '') {
                $updateData['phone'] = '';
            } else {
                if (!preg_match('/^0[0-9]{9}$/', $phoneTrim)) {
                    $msg = "Số điện thoại không hợp lệ. Phải bắt đầu bằng '0' và gồm đúng 10 chữ số (ví dụ: 0912345678).";
                    echo "<script>alert('". addslashes($msg) ."'); window.location='" . APP_URL . "/User/show';</script>";
                    exit;
                }
                $normalized = preg_replace('/[^0-9]/', '', $phoneTrim);
                $updateData['phone'] = $normalized;
            }
        }

        if ($address !== null) {
            $updateData['address'] = $address;
        }

        if ($newAvatarFileName !== null) {
            $updateData['avatar'] = $newAvatarFileName;
        }

        if (empty($updateData)) {
            echo "<script>alert('Không có thay đổi nào để lưu.'); window.location='" . APP_URL . "/User/show';</script>";
            exit;
        }

        try {
            // Lấy avatar cũ để xóa nếu cập nhật thành công
            $old = $userModel->getUserById($user_id);
            $oldAvatar = $old['avatar'] ?? null;

            $updated = $userModel->updateProfile($user_id, $updateData);

            if ($updated) {
                // xóa file cũ (nếu có và có avatar mới)
                if (isset($updateData['avatar']) && !empty($oldAvatar)) {
                    $oldPath = $uploadDir . basename($oldAvatar);
                    $oldThumb = $uploadDir . 'thumb_' . basename($oldAvatar);
                    if (is_file($oldPath)) @unlink($oldPath);
                    if (is_file($oldThumb)) @unlink($oldThumb);
                }

                // Lấy lại thông tin user từ DB để cập nhật session => tránh mismatch
                $freshUser = $userModel->getUserById($user_id);
                if ($freshUser) {
                    $_SESSION['user'] = [
                        'user_id'     => $freshUser['user_id'],
                        'fullname'    => $freshUser['fullname'],
                        'email'       => $freshUser['email'],
                        'avatar'      => $freshUser['avatar'],
                        'role'        => $freshUser['role'],
                        'status'      => $freshUser['status'] ?? null,
                        'created_at'  => $freshUser['created_at'] ?? null,
                        'is_verified' => isset($freshUser['is_verified']) ? (int)$freshUser['is_verified'] : 0,
                        'phone'       => $freshUser['phone'] ?? null,
                        'address'     => $freshUser['address'] ?? null
                    ];
                } else {
                    // fallback cập nhật session từ $updateData
                    if (isset($updateData['fullname'])) $_SESSION['user']['fullname'] = $updateData['fullname'];
                    if (isset($updateData['avatar'])) $_SESSION['user']['avatar'] = $updateData['avatar'];
                    if (isset($updateData['phone'])) $_SESSION['user']['phone'] = $updateData['phone'];
                    if (isset($updateData['address'])) $_SESSION['user']['address'] = $updateData['address'];
                }

                echo "<script>alert('Cập nhật thông tin thành công.'); window.location='" . APP_URL . "/User/show';</script>";
                exit;
            } else {
                // Nếu fail, xóa file mới vừa upload để tránh rác
                if ($newAvatarFileName !== null) {
                    @unlink($uploadDir . $newAvatarFileName);
                    @unlink($uploadDir . 'thumb_' . $newAvatarFileName);
                }
                echo "<script>alert('Cập nhật thất bại.'); window.location='" . APP_URL . "/User/show';</script>";
                exit;
            }
        } catch (Exception $e) {
            if ($newAvatarFileName !== null) {
                @unlink($uploadDir . $newAvatarFileName);
                @unlink($uploadDir . 'thumb_' . $newAvatarFileName);
            }
            echo "<script>alert('Lỗi hệ thống: " . addslashes($e->getMessage()) . "'); window.location='" . APP_URL . "/User/show';</script>";
            exit;
        }
    }

    /**
     * Đổi mật khẩu
     */
    public function updatePassword() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_SESSION['user'])) {
                echo "<script>alert('Vui lòng đăng nhập trước.'); window.location='" . APP_URL . "/AuthController/login';</script>";
                exit;
            }

            $user_id = (int)($_POST['user_id'] ?? 0);
            $old = $_POST['old_password'] ?? '';
            $new = $_POST['new_password'] ?? '';

            if ($user_id <= 0 || $old === '' || $new === '') {
                echo "<script>alert('Dữ liệu không hợp lệ.'); window.location='" . APP_URL . "/User/show';</script>";
                exit;
            }

            $userModel = $this->model("UserModel");
            $user = $userModel->getUserById($user_id);

            if ($user && password_verify($old, $user['password'])) {
                $hashed = password_hash($new, PASSWORD_BCRYPT);
                $userModel->updatePasswordById($user_id, $hashed);
                echo "<script>alert('Đổi mật khẩu thành công!'); window.location='" . APP_URL . "/User/show';</script>";
            } else {
                echo "<script>alert('Mật khẩu cũ không đúng!'); window.location='" . APP_URL . "/User/show';</script>";
            }
        } else {
            header("Location: " . APP_URL . "/User/show");
            exit;
        }
    }

    /**
     * Xóa tài khoản
     */
    public function deleteAccount() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user_id = (int)($_POST['user_id'] ?? 0);
            if ($user_id <= 0) {
                echo "<script>alert('ID không hợp lệ.'); window.location='" . APP_URL . "/User/show';</script>";
                exit;
            }

            $userModel = $this->model("UserModel");

            if ($userModel->deleteUser($user_id)) {
                // xóa session và redirect về Home
                session_destroy();
                echo "<script>alert('Tài khoản đã bị xóa.'); window.location='" . APP_URL . "/Home';</script>";
                exit;
            } else {
                echo "<script>alert('Xóa tài khoản thất bại!'); window.location='" . APP_URL . "/User/show';</script>";
                exit;
            }
        } else {
            header("Location: " . APP_URL . "/User/show");
            exit;
        }
    }

    /**
     * Tạo thumbnail nếu GD extension có sẵn.
     * - Không ném lỗi nếu GD không có (chỉ skip).
     */
    private function createThumbnail(string $srcPath, string $destPath, int $maxW, int $maxH): void {
        if (!extension_loaded('gd')) return;
        $info = @getimagesize($srcPath);
        if ($info === false) return;
        [$w, $h] = $info;
        $mime = $info['mime'] ?? '';

        $ratio = min($maxW / $w, $maxH / $h, 1);
        $nw = (int)($w * $ratio);
        $nh = (int)($h * $ratio);

        $dst = imagecreatetruecolor($nw, $nh);
        if (!$dst) return;

        switch ($mime) {
            case 'image/jpeg':
                $src = @imagecreatefromjpeg($srcPath);
                break;
            case 'image/png':
                $src = @imagecreatefrompng($srcPath);
                imagealphablending($dst, false);
                imagesavealpha($dst, true);
                break;
            case 'image/webp':
                $src = @imagecreatefromwebp($srcPath);
                break;
            default:
                return;
        }

        if (!$src) { imagedestroy($dst); return; }

        imagecopyresampled($dst, $src, 0,0,0,0, $nw, $nh, $w, $h);

        switch ($mime) {
            case 'image/jpeg': imagejpeg($dst, $destPath, 85); break;
            case 'image/png': imagepng($dst, $destPath); break;
            case 'image/webp': imagewebp($dst, $destPath, 85); break;
        }

        imagedestroy($dst);
        imagedestroy($src);
    }
}
