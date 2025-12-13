<?php
// layout/adminLayout.php  (ví dụ tên file)
// Layout admin chung — include các view con bằng biến $page trong $data
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Hiển thị lỗi ngắn nếu có
if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger">
        <?= htmlspecialchars($_SESSION['error']); ?>
    </div>
    <?php unset($_SESSION['error']);
endif;

// Chuẩn bị biến app base url
$app = defined('APP_URL') ? rtrim(APP_URL, '/') : '';

// Chuẩn hóa $data và extract an toàn
$data = $data ?? [];
if (is_array($data) && !empty($data)) {
    // EXTR_SKIP để không ghi đè các biến đã tồn tại
    extract($data, EXTR_SKIP);
}

// đảm bảo $page tồn tại
$page = $page ?? '';
?>
<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title><?= isset($title) ? htmlspecialchars($title) : 'Trang Quản trị' ?></title>

    <!-- Bootstrap CSS -->
    <link href="<?= htmlspecialchars($app) ?>/public/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous" />
</head>
<body>
<header>
    <nav class="navbar navbar-expand-sm navbar-dark bg-primary">
        <div class="container">
           <a class="navbar-brand" href="<?= htmlspecialchars($app) ?>/Admin/showDashboard">Quản trị</a>

            <button
                class="navbar-toggler d-lg-none"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#collapsibleNavId"
                aria-controls="collapsibleNavId"
                aria-expanded="false"
                aria-label="Toggle navigation"
            >
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="collapsibleNavId">
                <ul class="navbar-nav me-auto mt-2 mt-lg-0">
                    <li class="nav-item dropdown">
                        <a
                            class="nav-link dropdown-toggle"
                            href="#"
                            id="dropdownId"
                            data-bs-toggle="dropdown"
                            aria-haspopup="true"
                            aria-expanded="false"
                        >Quản lý</a>
                        <div class="dropdown-menu bg-primary" aria-labelledby="dropdownId" style="border: none; box-shadow: none;">
<?php if ($_SESSION['user']['role'] == 'admin'): ?>
    <a class="dropdown-item" href="<?= htmlspecialchars($app) ?>/TaiKhoan/show">Quản lý tài khoản</a>
    <a class="dropdown-item" href="<?= htmlspecialchars($app) ?>/Report/">Báo cáo doanh thu</a>
    <a class="dropdown-item" href="<?= htmlspecialchars($app) ?>/Promo/">Quản lý khuyến mãi</a>
    <a class="dropdown-item" href="<?= htmlspecialchars($app) ?>/Banner/">Quản lý banner</a>
    <a class="dropdown-item" href="<?= htmlspecialchars($app) ?>/Page/">Quản lý nội dung tĩnh</a>
    <a class="dropdown-item" href="<?= htmlspecialchars($app) ?>/News/">Quản lý tin tức</a>
<?php endif; ?>

<?php if (in_array($_SESSION['user']['role'], ['admin', 'staff'])): ?>
    <a class="dropdown-item" href="<?= htmlspecialchars($app) ?>/ProductType/">Quản lý loại sản phẩm</a>
    <a class="dropdown-item" href="<?= htmlspecialchars($app) ?>/Product/">Quản lý sản phẩm</a>
    <a class="dropdown-item" href="<?= htmlspecialchars($app) ?>/Inventory/">Quản lý tồn kho</a>
    <a class="dropdown-item" href="<?= htmlspecialchars($app) ?>/Order/">Quản lý đơn hàng</a>
    <a class="dropdown-item" href="<?= htmlspecialchars($app) ?>/Review/">Quản lý đánh giá</a>
    <a class="dropdown-item" href="<?= htmlspecialchars($app) ?>/NhaCC/">Quản lý nhà cung cấp</a>
    <a class="dropdown-item" href="<?= htmlspecialchars($app) ?>/Chat/">Quản lý chatbox</a>
<?php endif; ?>

                    </li>
                </ul>

                <ul class="navbar-nav ms-auto">
    <?php if (isset($_SESSION['user'])): ?>
    <li class="nav-item dropdown user-dropdown">

<?php
// Nếu chưa login → dùng default
if (!isset($_SESSION['user'])) {
    $avatarSrc = "/MVC3/public/images/avatars/default.png";
    $fullname = "Người dùng";
} else {
    // Lấy user từ database
    $userModel = $this->model("AdminModel");
    $user = $userModel->getById($_SESSION['user']['user_id']);

    // Avatar file trong DB
    $avatarFile = $user['avatar'] ?? 'default.png';

    // Đường dẫn ảnh cho trình duyệt
    $avatarRelPath = "/MVC3/public/images/avatars/";

    // Đường dẫn file thật để kiểm tra tồn tại
    $avatarAbsPath = $_SERVER['DOCUMENT_ROOT'] . $avatarRelPath . $avatarFile;

    // Nếu avatar có tồn tại → dùng
    if ($avatarFile !== '' && file_exists($avatarAbsPath)) {
        $avatarSrc = $avatarRelPath . rawurlencode($avatarFile);
    } else {
        $avatarSrc = $avatarRelPath . "default.png";
    }

    $fullname = htmlspecialchars($user['fullname']);
}
?>



<a class="nav-link dropdown-toggle d-flex align-items-center text-white" href="#" 
   id="userMenu" role="button" data-bs-toggle="dropdown" aria-expanded="false">

    <img src="<?= $avatarSrc ?>"
         class="rounded-circle me-2"
         style="width:32px; height:32px; object-fit:cover; border:2px solid #fff;"
         alt="Avatar">

    <?= $fullname ?>
</a>



        <ul class="dropdown-menu dropdown-menu-end shadow">
            <li>
                <a class="dropdown-item" href="<?= htmlspecialchars($app) ?>/Admin/profile">
                    Thông tin cá nhân
                </a>
            </li>
            <li><hr class="dropdown-divider"></li>
            <li>
                <a class="dropdown-item text-danger" href="<?= htmlspecialchars($app) ?>/AuthController2/logout">
                    Đăng xuất
                </a>
            </li>
        </ul>
    </li>
    <?php else: ?>
    <a href="<?= htmlspecialchars($app) ?>/AuthController2/ShowLogin" class="btn btn-outline-light ms-2">Đăng nhập</a>
    <?php endif; ?>
</ul>

            </div>
        </div>
    </nav>
</header>

<main class="py-4">
    <div class="container">
        <?php
        // Nếu $page rỗng hoặc file không tồn tại thì hiển thị lỗi thân thiện
        if ($page !== "") {
            $viewPath = __DIR__ . "/Back_end/" . $page . ".php";
            if (file_exists($viewPath)) {
                require $viewPath;
            } else {
                echo "<div class='alert alert-warning'>❌ Không tìm thấy view cần load: <strong>" . htmlspecialchars($page) . "</strong></div>";
            }
        } else {
            echo "<div class='alert alert-danger'>❌ Không tìm thấy view cần load – biến <code>page</code> rỗng!</div>";
        }
        ?>
    </div>
</main>

<footer class="admin-footer mt-4 py-3">
    <div class="container text-center">
        <hr class="opacity-25">
        <p class="text-muted small mb-0">
            © <?= date('Y') ?> - Trang Quản trị Website Văn phòng phẩm LT
        </p>
    </div>
</footer>

<style>
.admin-footer {
    background: #f8f9fa;
    border-top: 1px solid #e1e1e1;
}
.admin-footer p {
    font-size: 13px;
    color: #6c757d;
}
</style>

<!-- Bootstrap Bundle JS (có Popper) -->
<script src="<?= htmlspecialchars($app) ?>/public/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</body>
</html>
<style>
/* ===== NAVBAR ADMIN ===== */
.navbar {
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

.navbar-nav .nav-link {
    font-weight: 500;
    padding: 10px 18px;
}

.navbar-nav .dropdown-menu {
    border-radius: 8px !important;
    padding: 0;
    overflow: hidden;
    min-width: 260px;
}

/* Nền dropdown */
.navbar-nav .dropdown-menu.bg-primary {
    background: #718db7ff !important;
}

/* Item trong dropdown */
.dropdown-item {
    color: #fff !important;
    padding: 12px 18px;
    font-size: 15px;
    border-bottom: 1px solid rgba(255,255,255,0.1);
}

.dropdown-item:last-child {
    border-bottom: none;
}

/* Hover mượt */
.dropdown-item:hover {
    background: rgba(255,255,255,0.15) !important;
    padding-left: 22px;
    transition: 0.25s;
}

/* Icon + chữ người dùng */
.navbar-text {
    font-weight: 500;
}

/* ===== MAIN CONTAINER ===== */
main .container {
    background: #fff;
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 2px 7px rgba(0,0,0,0.08);
}

/* ===== FOOTER ===== */
.admin-footer {
    background: #f5f6f7 !important;
    padding: 20px;
}

.admin-footer p {
    font-size: 13px;
    color: #777;
}

/* ===== TĂNG HIỆU ỨNG CHUNG ===== */
.dropdown-toggle::after {
    margin-left: 6px;
}
/* Dropdown tài khoản */
/* ===== DROPDOWN USER — ƯU TIÊN CAO NHẤT ===== */
.navbar .user-dropdown .dropdown-item {
    color: #333 !important;
    padding: 10px 20px;
}

.navbar .user-dropdown .dropdown-item:hover {
    background: rgba(0,0,0,0.07) !important;
    color: #0d6efd !important;
}

/* Nút đăng xuất */
.navbar .user-dropdown .dropdown-item.text-danger {
    color: #dc3545 !important;
}

.navbar .user-dropdown .dropdown-item.text-danger:hover {
    background: #dc3545 !important;
    color: #fff !important;
}

.navbar .user-dropdown img {
    width: 32px;
    height: 32px;
    object-fit: cover;
    border-radius: 50%;
    border: 2px solid rgba(255,255,255,0.8);
}

</style>
