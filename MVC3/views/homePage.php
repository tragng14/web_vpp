
<?php
// views/homePage.php
// Layout chính (HomePage) - đã được sanitize & chỉnh sửa nhẹ
// Yêu cầu: APP_URL được định nghĩa trong config; $data là mảng truyền từ controller.

// --- BẮT ĐẦU SESSION AN TOÀN ---
// Nếu session chưa start thì start ngay, tránh mọi warning khi truy xuất $_SESSION
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Debug log an toàn: chỉ log giá trị nếu có, tránh truy xuất trực tiếp $_SESSION['user'] khi chưa tồn tại
$sessionUserForLog = $_SESSION['user'] ?? null;
error_log("SESSION USER: " . print_r($sessionUserForLog, true));

// -------------------------------

if (!isset($data)) $data = [];

// Hàm escape HTML an toàn
if (!function_exists('h')) {
    function h($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
}

$appUrl = defined('APP_URL') ? rtrim(APP_URL, '/') : '';
?>
<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title><?= isset($data['title']) ? h($data['title']) : 'Website' ?></title>

    <link href="<?= h($appUrl) ?>/public/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous" />
    <script defer src="<?= h($appUrl) ?>/public/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>

</head>
<style>
        .navbar-brand { font-weight:600; }

        /* Avatar trong navbar */
        .nav-avatar {
            width:36px;
            height:36px;
            border-radius:50%;
            object-fit:cover;
            border:1px solid rgba(0,0,0,0.12);
        }

        .nav-username {
            max-width:120px;
            overflow:hidden;
            text-overflow:ellipsis;
            white-space:nowrap;
        }

        /* Footer nhỏ gọn */
        footer h6 { letter-spacing:0.5px; }
        footer .small a { color: inherit; }

        /* Dropdown danh mục sản phẩm: cố định kích thước, có scroll */
        .product-dropdown-menu {
            min-width: 220px;
            max-height: 320px; /* khi nhiều mục sẽ có scroll */
            overflow-y: auto;
            padding-right: 6px; /* tránh bị che scrollbar */
        }

        /* Làm đẹp item */
        .product-dropdown-menu .dropdown-item {
            white-space: nowrap;
        }
    </style>
<body>
<header>
    <nav class="navbar navbar-expand-sm navbar-light bg-light">
        <div class="container">

            <a class="navbar-brand" href="<?= h($appUrl) ?>/Home/">LT</a>

            <button class="navbar-toggler d-lg-none" type="button" data-bs-toggle="collapse"
                    data-bs-target="#collapsibleNavId" aria-controls="collapsibleNavId" aria-expanded="false"
                    aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="collapsibleNavId">
                <ul class="navbar-nav me-auto mt-2 mt-lg-0">
                    <li class="nav-item">
                        <a class="nav-link <?= (isset($data['page']) && $data['page']=='home') ? 'active' : ''; ?>"
                           href="<?= h($appUrl) ?>/Home/">Trang chủ</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="<?= h($appUrl) ?>/NewsFront/">Tin tức</a>
                    </li>

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="infoDropdown" data-bs-toggle="dropdown">Thông tin</a>

                        <ul class="dropdown-menu" aria-labelledby="infoDropdown">
                            <?php if (!empty($data["pagesList"])): ?>
                                <?php foreach ($data["pagesList"] as $p): ?>
                                    <li>
                                        <a class="dropdown-item" href="<?= h($appUrl) ?>/Page/PageDetail/<?= h($p["slug"]) ?>">
                                            <?= h($p["title"]) ?>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <li><span class="dropdown-item text-muted">Không có trang</span></li>
                            <?php endif; ?>
                        </ul>
                    </li>

                    <!-- SẢN PHẨM: luôn hiển thị dropdown danh mục (không phụ thuộc trang) -->
<li class="nav-item dropdown d-flex align-items-center">

    <!-- LINK CHUYỂN TRANG -->
    <a class="nav-link pe-1"
       href="<?= h($appUrl) ?>/ProductFront/">
        Sản phẩm
    </a>

    <!-- NÚT MỞ DROPDOWN -->
    <a class="nav-link dropdown-toggle ps-0"
       href="#"
       id="navbarProductDropdown"
       role="button"
       data-bs-toggle="dropdown"
       aria-expanded="false">
    </a>

    <!-- DROPDOWN MENU -->
    <div class="dropdown-menu product-dropdown-menu"
         aria-labelledby="navbarProductDropdown">

        <a class="dropdown-item" href="<?= h($appUrl) ?>/ProductFront/">
            Sản phẩm bán chạy
        </a>

        <div class="dropdown-divider"></div>

        <?php
        $rawCats = $data['categories'] ?? [];
        if (empty($rawCats)):
        ?>
            <a class="dropdown-item text-muted"
               href="<?= h($appUrl) ?>/ProductFront/">
                Chưa có danh mục
            </a>
        <?php
        else:
            $cats = [];
            foreach ($rawCats as $c) {
                $id = $c['maLoaiSP'] ?? $c['maLoai'] ?? $c['maloai'] ?? $c['id'] ?? '';
                $name = $c['tenLoaiSP'] ?? $c['tenloai'] ?? $c['ten'] ?? $c['name'] ?? $id;
                if ($id === '') continue;
                $cats[] = ['id' => (string)$id, 'name' => (string)$name];
            }
            usort($cats, function($a,$b){
                return strcmp(
                    mb_strtolower($a['name'],'UTF-8'),
                    mb_strtolower($b['name'],'UTF-8')
                );
            });

            foreach ($cats as $cat):
        ?>
            <a class="dropdown-item"
               href="<?= h($appUrl) ?>/ProductFront/?category=<?= urlencode($cat['id']) ?>">
                <?= h($cat['name']) ?>
            </a>
        <?php endforeach; endif; ?>

    </div>
</li>


                    <li class="nav-item">
                        <a class="nav-link" href="<?= h($appUrl) ?>/Home/order">Giỏ hàng</a>
                    </li>
                </ul>

                <!-- Search -->
                <form class="d-flex my-2 my-lg-0" action="<?= h($appUrl) ?>/ProductFront/" method="get" role="search">
                    <input name="q" class="form-control me-sm-2" type="search" placeholder="Tìm sản phẩm"
                           value="<?= h($data['searchQuery'] ?? ''); ?>" />
                    <?php if (!empty($data['filterCategory'])): 
                        // hidden input: escape with h()
                        if (!function_exists('fmt')) {
    function fmt($n) {
        return number_format(floatval($n), 0, ',', '.');
    }
}

if (!function_exists('clean_price')) {
    function clean_price($raw) {
        $str = (string)$raw;
        $str = str_replace(',', '.', $str);
        $s = preg_replace('/[^0-9\.\-]/', '', $str);
        return $s === '' ? 0.0 : floatval($s);
    }
}
                    ?>
                        <input type="hidden" name="category" value="<?= h($data['filterCategory']); ?>">
                    <?php endif; ?>
                </form>

                <!-- Avatar + Dropdown -->
                <?php
                // An toàn: chỉ truy xuất $_SESSION['user'] khi đã tồn tại và là mảng
                if (isset($_SESSION['user']) && is_array($_SESSION['user'])):
                   $user = $_SESSION['user'];

                   // Avatar default
                   $defaultAvatar = $appUrl . "/public/images/user-default.png";
                   $avatar = trim($user['avatar'] ?? '');

                   if ($avatar !== '') {
                       // Nếu avatar là URL đầy đủ
                       if (preg_match('/^https?:\/\//i', $avatar)) {
                           $avatarUrl = $avatar;
                       // Nếu avatar chỉ là tên file
                       } elseif (strpos($avatar, '/') === false) {
                           $avatarUrl = $appUrl . "/public/images/avatars/" . rawurlencode($avatar);
                       } else {
                           // Chuẩn hoá lại: chỉ lấy tên file
                           $avatarFile = basename($avatar);
                           $avatarUrl = $appUrl . "/public/images/avatars/" . rawurlencode($avatarFile);
                       }
                   } else {
                       $avatarUrl = $defaultAvatar;
                   }

                   $displayName = trim($user['fullname'] ?? '');
                   $usernameSafe = $displayName !== '' ? $displayName : 'Người dùng';
                   ?>

                    <div class="ms-3 dropdown">
                        <a class="d-flex align-items-center text-decoration-none dropdown-toggle" href="#"
                           id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                          <img src="<?= h($avatarUrl) ?>"
     class="nav-avatar me-2"
     alt="Avatar"
     onerror="this.src='<?= h($defaultAvatar) ?>'">

                            <span class="nav-username d-none d-sm-inline">
                                <?= h($usernameSafe); ?>
                            </span>
                        </a>

                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <li class="px-3 py-2">
                                <div class="d-flex align-items-center">
                                  <img src="<?= h($avatarUrl) ?>"
     class="nav-avatar me-2"
     style="width:48px;height:48px;"
     alt="Avatar"
     onerror="this.src='<?= h($defaultAvatar) ?>'">
                                <div>
                                    <div class="fw-semibold"><?= h($usernameSafe) ?></div>
                                </div>
                                </div>
                            </li>

                            <li><hr class="dropdown-divider"></li>

                            <li><a class="dropdown-item" href="<?= h($appUrl) ?>/User/profile">Thông tin tài khoản</a></li>
                            <li><a class="dropdown-item" href="<?= h($appUrl) ?>/Home/orderHistory">Lịch sử đơn hàng</a></li>

                            <!-- Sản phẩm yêu thích vào dropdown user -->
                            <li><a class="dropdown-item" href="<?= h($appUrl) ?>/ProductFront/?page=1&favorites=1">Sản phẩm yêu thích</a></li>
                            <li><a class="dropdown-item" href="<?= h($appUrl) ?>/ChatUser/" id="openChatbox">💬 Hỗ trợ (Chatbox)</a></li>

                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="<?= h($appUrl) ?>/AuthController/logout">Đăng xuất</a></li>
                        </ul>
                    </div>

                <?php else: 
                    // Nếu chưa login: hiển thị nút Đăng nhập, giữ lại param redirect an toàn
                    $currentRequest = $_SERVER['REQUEST_URI'] ?? '/';
                    $catchUrl = h($appUrl) . '/AuthController/catchRedirect?to=' . urlencode($currentRequest);
                    ?>
                  <a href="<?= $catchUrl ?>"
   class="btn btn-outline-success ms-3">
    Đăng nhập
</a>

                <?php endif; ?>

            </div>
        </div>
    </nav>
</header>

<main class="py-3">
    <div class="container">

    <?php if (!empty($data["banners"]) && ($data["page"] ?? '') === "HomeView"): ?>

<div id="homeBannerSlide" class="carousel slide mb-4" data-bs-ride="carousel">

    <div class="carousel-indicators">
        <?php foreach ($data["banners"] as $index => $b): ?>
            <button type="button"
                    data-bs-target="#homeBannerSlide"
                    data-bs-slide-to="<?= $index ?>"
                    class="<?= $index == 0 ? 'active' : '' ?>">
            </button>
        <?php endforeach; ?>
    </div>

    <div class="carousel-inner">
        <?php foreach ($data["banners"] as $index => $b): ?>
            <div class="carousel-item <?= $index == 0 ? 'active' : '' ?>">
                <?php if (!empty($b["link"])): ?>
                    <a href="<?= h($b["link"]) ?>">
                        <img src="<?= h($appUrl) ?>/public/images/banners/<?= h($b["image_path"]) ?>"
                             class="d-block w-100"
                             style="height: 360px; object-fit: cover;">
                    </a>
                <?php else: ?>
                    <img src="<?= h($appUrl) ?>/public/images/banners/<?= h($b["image_path"]) ?>"
                         class="d-block w-100"
                         style="height: 360px; object-fit: cover;">
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>

    <button class="carousel-control-prev" type="button" data-bs-slide="prev" data-bs-target="#homeBannerSlide">
        <span class="carousel-control-prev-icon"></span>
    </button>

    <button class="carousel-control-next" type="button" data-bs-slide="next" data-bs-target="#homeBannerSlide">
        <span class="carousel-control-next-icon"></span>
    </button>
</div>
<?php endif; ?>
<?php if (!empty($data["NewsList"]) && ($data["page"] ?? '') === "HomeView"): ?>

    </div>
</div>

<?php endif; ?>

        <?php
        $page = $data["page"] ?? 'home';
        // sanitize page name: chỉ cho phép chữ, số, gạch dưới và dấu gạch ngang
        $pageSafe = preg_match('/^[a-zA-Z0-9_\-]+$/', $page) ? $page : 'home';
        $child = __DIR__ . "/Font_end/" . $pageSafe . ".php";
        if (file_exists($child)) {
            require_once $child;
        } else {
            echo "<div class='alert alert-warning'>View not found: " . h($pageSafe) . "</div>";
        }
        ?>
    </div>
</main>

<footer class="bg-light pt-5 pb-4 mt-5 border-top">
    <div class="container">
        <div class="row g-4">

            <!-- CỘT 1: NỘI DUNG TĨNH -->
            <div class="col-12 col-md-4">
                <h6 class="fw-bold mb-3">VỀ CHÚNG TÔI</h6>
                <ul class="list-unstyled small text-muted">
                    <?php if (!empty($data["pagesList"])): ?>
                        <?php foreach ($data["pagesList"] as $p): ?>
                            <li>
                                <a class="dropdown-item" href="<?= h($appUrl) ?>/Page/PageDetail/<?= h($p["slug"]) ?>">
                                    <?= h($p["title"]) ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li><span class="dropdown-item text-muted">Không có trang</span></li>
                    <?php endif; ?>
                </ul>
            </div>

            <!-- CỘT 2: TIN TỨC -->
            <div class="col-12 col-md-4">
                <h6 class="fw-bold mb-3">TIN TỨC</h6>
                <ul class="list-unstyled small text-muted">
                    <?php foreach ($data["NewsList"] ?? [] as $n): ?>
                        <li>
                            <a href="<?= h($appUrl) ?>/NewsFrontController/Detail/<?= h($n["id"]) ?>"
                               class="text-decoration-none text-muted">
                                <?= h($n["title"]) ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <!-- CỘT 3: THÔNG TIN CỬA HÀNG -->
            <div class="col-12 col-md-4">
                <h6 class="fw-bold mb-3">LIÊN HỆ</h6>
               <?php if (!empty($data["contactPage"])): ?>
                    <div class="small text-muted">
                        <?= nl2br(h($data["contactPage"]["content"])) ?>
                    </div>
                <?php else: ?>
                    <p class="small text-muted">Chưa có nội dung liên hệ.</p>
                <?php endif; ?>
            </div>

        </div>
    </div>
</footer>

<footer class="mt-4">
<div class="container text-center">
    <hr>
    <p class="text-muted small mt-2">© <?= date('Y'); ?> - Website</p>
</div>
</footer>
<!-- CHATBOX FLOATING -->
<div id="chatbox-frame"
     style="
        position: fixed;
        bottom: 20px;
        right: 20px;
        width: 360px;
        height: 520px;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.2);
        overflow: hidden;
        z-index: 9999;
        display: none;
     ">

    <div style="
        background: #007bff;
        color: #fff;
        padding: 10px 12px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-weight: 600;
    ">
        <span>💬 Hỗ trợ trực tuyến</span>
        <button id="closeChatbox" style="
            border: none;
            background: transparent;
            color: #fff;
            font-size: 20px;
            line-height: 1;
            cursor: pointer;
        ">&times;

    </button>
    </div>

    <iframe src="<?= h($appUrl) ?>/ChatUser/iframe"
            style="width:100%; height:100%; border:none;">
    </iframe>
</div>

<script>
document.getElementById("openChatbox")?.addEventListener("click", function (e) {
    e.preventDefault();
    document.getElementById("chatbox-frame").style.display = "block";
});

// An toàn: kiểm tra tồn tại nút close trước khi gán event
var closeBtn = document.getElementById("closeChatbox");
if (closeBtn) {
    closeBtn.addEventListener("click", function () {
        var f = document.getElementById("chatbox-frame");
        if (f) f.style.display = "none";
    });
}
</script>

</body>
</html>
<style>
    /* ===== BỐ CỤC CHUNG ===== */
.news-item {
    transition: all 0.3s ease;
    border: 1px solid #eee;
}

.news-item:hover {
    box-shadow: 0 6px 18px rgba(0,0,0,0.08);
    transform: translateY(-3px);
}

/* ===== ẢNH ===== */
.news-thumb {
    width: 260px;
    height: 170px;
    flex-shrink: 0;
    overflow: hidden;
    border-radius: 8px;
}

.news-thumb img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform .35s ease;
}

.news-item:hover img {
    transform: scale(1.05);
}

/* ===== TIÊU ĐỀ ===== */
.news-title-item {
    font-size: 1.25rem;
    font-weight: 600;
    color: #212529;
    line-height: 1.4;
    margin-bottom: 8px;
    transition: color .3s;
}

.news-item:hover .news-title-item {
    color: #0f1b2cff;
}

/* ===== MÔ TẢ ===== */
.news-desc {
    font-size: 0.95rem;
    line-height: 1.5;
    margin-bottom: 15px;
}

/* ===== NÚT ĐỌC TIẾP ===== */
.news-content a {
    background-color: #bcd9ff;
    border: 1px solid #a7c8f5;
    color: #0b3d91;
    font-size: 0.8rem;
    padding: 6px 14px;
    font-weight: 500;
    transition: all 0.25s ease;
}

.news-content a:hover {
    background-color: #94c0ff;
    border-color: #7baef0;
    color: #fff;
    transform: translateY(-1px);
}

/* ===== RESPONSIVE ===== */
@media (max-width: 768px) {
    .news-thumb {
        width: 100%;
        height: 220px;
        margin-bottom: 10px;
    }
}

</style>