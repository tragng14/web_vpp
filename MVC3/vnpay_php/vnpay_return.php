<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once("./config.php");
require_once(__DIR__ . '/../models/OrderModel.php');
require_once(__DIR__ . '/../models/AdProducModel.php');
require_once __DIR__ . '/../app/DB.php';
require_once __DIR__ . '/../models/BaseModel.php';

$orderModel = new OrderModel();

// =====================
// LẤY DỮ LIỆU TỪ VNPAY
// =====================
$orderId = isset($_GET['vnp_TxnRef']) ? trim($_GET['vnp_TxnRef']) : '';
$vnp_ResponseCode = $_GET['vnp_ResponseCode'] ?? '';
$vnp_SecureHash = $_GET['vnp_SecureHash'] ?? '';

$logFile = __DIR__ . '/vnpay_return.log';
file_put_contents($logFile, "===== CALLBACK " . date('Y-m-d H:i:s') . " =====\n" . print_r($_GET, true) . "\n", FILE_APPEND);

// =====================
// KIỂM TRA CHỮ KÝ (HASH)
// =====================
$inputData = [];
foreach ($_GET as $key => $value) {
    if (strpos($key, 'vnp_') === 0) {
        $inputData[$key] = $value;
    }
}
unset($inputData['vnp_SecureHash']);
ksort($inputData);

$hashData = '';
$i = 0;
foreach ($inputData as $key => $value) {
    if ($i == 1) {
        $hashData .= '&' . urlencode($key) . '=' . urlencode($value);
    } else {
        $hashData .= urlencode($key) . '=' . urlencode($value);
        $i = 1;
    }
}

$secureHash = '';
if (!empty($vnp_HashSecret)) {
    $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);
} else {
    file_put_contents($logFile, "[".date('Y-m-d H:i:s')."] WARNING: vnp_HashSecret empty or not defined in config.\n", FILE_APPEND);
}

// =====================
// XỬ LÝ ORDER TRONG HỆ THỐNG
// =====================
$order = null;
if ($orderId !== '') {
    // getOrderByCode() nhận vnp_TxnRef (mã đơn) và trả về row order (có id)
    $order = $orderModel->getOrderByCode($orderId);
}

if ($secureHash !== '' && hash_equals($secureHash, $vnp_SecureHash) && $vnp_ResponseCode === "00") {

    file_put_contents($logFile, "[".date('Y-m-d H:i:s')."] Signature OK | Payment SUCCESS\n", FILE_APPEND);

    if ($order) {

        // ============================
        // 1) GỌI markAsPaid()
        // ============================
        try {
            $update = $orderModel->markAsPaid($order['id']);
        } catch (Throwable $e) {
            $update = false;
            file_put_contents($logFile, "[".date('Y-m-d H:i:s')."] Exception markAsPaid: " . $e->getMessage() . "\n", FILE_APPEND);
        }

        if ($update) {

            file_put_contents($logFile, "[".date('Y-m-d H:i:s')."] markAsPaid OK (OrderID {$order['id']})\n", FILE_APPEND);

            // reload order từ DB để lấy thông tin mới
            $freshOrder = $orderModel->getOrderByCode($orderId);

            // -------------------------
            // 2) CẬP NHẬT PROMO (nếu có)
            // -------------------------
            $discountCode = trim($freshOrder['discount_code'] ?? '');
            if ($discountCode !== '') {
                $codeToUse = $discountCode;

                try {
                    $dbObj = new DB();
                    $pdo = $dbObj->getPdo();

                    $sql = "
                        UPDATE promo_codes
                        SET used_count = used_count + 1
                        WHERE code = :code
                          AND status = 'active'
                          AND NOW() BETWEEN start_date AND end_date
                          AND (usage_limit IS NULL OR used_count < usage_limit)
                    ";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([':code' => $codeToUse]);
                    $affected = $stmt->rowCount();

                    if ($affected > 0) {
                        file_put_contents($logFile, "[".date('Y-m-d H:i:s')."] Promo code updated (code={$codeToUse}) -> affected={$affected}\n", FILE_APPEND);
                    } else {
                        file_put_contents($logFile, "[".date('Y-m-d H:i:s')."] Promo update skipped/failed (code={$codeToUse})\n", FILE_APPEND);
                    }

                } catch (Throwable $e) {
                    file_put_contents($logFile, "[".date('Y-m-d H:i:s')."] Promo update error: " . $e->getMessage() . "\n", FILE_APPEND);
                }
            }

            // -------------------------
            // 3) GIẢM TỒN (reduce stock)
            // -------------------------
            $numericOrderId = $freshOrder['id'] ?? $order['id'] ?? null;
            $stockReducedFlag = intval($freshOrder['stock_reduced'] ?? 0);

            if ($numericOrderId !== null) {

                if ($stockReducedFlag === 0) {
                    $productModel = new AdProducModel();
                    try {
                        $resReduce = $productModel->reduceStockAfterPayment($numericOrderId);
                        file_put_contents($logFile, "[".date('Y-m-d H:i:s')."] reduceStockAfterPayment -> " . ($resReduce ? "OK" : "FAILED") . "\n", FILE_APPEND);
                    } catch (Throwable $e) {
                        file_put_contents($logFile, "[".date('Y-m-d H:i:s')."] reduceStock exception: " . $e->getMessage() . "\n", FILE_APPEND);
                    }
                } else {
                    file_put_contents($logFile, "[".date('Y-m-d H:i:s')."] Skip reduceStock: already reduced before\n", FILE_APPEND);
                }

            }

        } else {
            file_put_contents($logFile, "[".date('Y-m-d H:i:s')."] markAsPaid FAILED (OrderCode {$orderId})\n", FILE_APPEND);
        }

    } else {
        file_put_contents($logFile, "[".date('Y-m-d H:i:s')."] Order not found: $orderId\n", FILE_APPEND);
    }
} else {
    file_put_contents($logFile, "[".date('Y-m-d H:i:s')."] Signature FAIL or Payment FAIL | computed={$secureHash} | received={$vnp_SecureHash} | resp={$vnp_ResponseCode}\n", FILE_APPEND);
}

// =====================
// URL TRANG CHỦ
// =====================
$homeHref = "https://maple-indiscrete-subglobularly.ngrok-free.dev/MVC3/index.php";
file_put_contents($logFile, "[".date('Y-m-d H:i:s')."] [HOME] $homeHref\n", FILE_APPEND);

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <title>VNPAY - Kết quả thanh toán</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

<style>
    body { background:#f5f7fb; font-family: 'Segoe UI', Roboto, Arial, sans-serif; }
    .wrapper { max-width:920px; margin:30px auto; }
    .brand { display:flex; gap:12px; align-items:center; }
    .brand img{ height:48px; }
    .card { border-radius:12px; box-shadow:0 6px 20px rgba(24,39,75,0.08); }
    .kv { color:#6b7280; font-size:0.95rem; }
    .value { font-weight:600; font-size:1rem; }
    .status-success{ color:#0b6e3a; background:#e6ffef; padding:8px 12px; border-radius:8px; }
    .status-failed{ color:#b02a37; background:#fff0f2; padding:8px 12px; border-radius:8px; }
    .status-invalid{ color:#7a5af8; background:#f3f0ff; padding:8px 12px; border-radius:8px; }

    .btn-home-prominent {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      background: linear-gradient(90deg, #0d6efd, #0b5ed7);
      color: #fff;
      padding: 10px 16px;
      border-radius: 10px;
      text-decoration: none;
      font-weight: 700;
      box-shadow: 0 8px 20px rgba(13,110,253,0.18);
      border: none;
      transition: transform 150ms ease, box-shadow 150ms ease;
    }
    .btn-home-prominent:hover {
      transform: translateY(-3px);
      box-shadow: 0 14px 30px rgba(13,110,253,0.22);
      color: #fff;
      text-decoration:none;
    }

    .footer { text-align:center; color:#9aa0a6; padding:18px 0; }
</style>
</head>

<body>
<div class="wrapper">

    <div class="brand mb-3">
        <img src="/vnpay_php/assets/logo.png" alt="Logo" onerror="this.style.display='none'">
        <div>
            <h4 style="margin:0">VNPAY - Kết quả thanh toán</h4>
            <div style="color:#777">Mã đơn: <strong><?= htmlspecialchars($orderId) ?: '-' ?></strong></div>
        </div>
    </div>

    <div class="card p-4 bg-white">
        <div class="d-flex justify-content-between mb-3">
            <h5>Chi tiết giao dịch</h5>

            <div>
                <?php if ($secureHash !== $vnp_SecureHash): ?>
                    <div class="status-invalid"><i class="fa fa-exclamation-circle"></i> Chữ ký không hợp lệ</div>
                <?php elseif ($vnp_ResponseCode == "00"): ?>
                    <div class="status-success"><i class="fa fa-check-circle"></i> Giao dịch thành công</div>
                <?php else: ?>
                    <div class="status-failed"><i class="fa fa-times-circle"></i> Giao dịch thất bại</div>
                <?php endif; ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-2">
                <div class="kv">Mã giao dịch VNPAY</div>
                <div class="value"><?= htmlspecialchars($_GET['vnp_TransactionNo'] ?? '-') ?></div>
            </div>

            <div class="col-md-6 mb-2">
                <div class="kv">Ngân hàng</div>
                <div class="value"><?= htmlspecialchars($_GET['vnp_BankCode'] ?? '-') ?></div>
            </div>

            <div class="col-md-6 mb-2">
                <div class="kv">Thời gian thanh toán</div>
                <div class="value"><?= htmlspecialchars($_GET['vnp_PayDate'] ?? '-') ?></div>
            </div>

            <div class="col-md-6 mb-2">
                <div class="kv">Mã phản hồi</div>
                <div class="value"><?= htmlspecialchars($vnp_ResponseCode ?: '-') ?></div>
            </div>

            <div class="col-md-6 mb-2">
                <div class="kv">Số tiền</div>
                <div class="value">
                    <?php
                    if (isset($_GET['vnp_Amount'])) {
                        echo number_format($_GET['vnp_Amount']/100,0,',','.') . ' VND';
                    } else echo "-";
                    ?>
                </div>
            </div>

            <div class="col-md-6 mb-2">
                <div class="kv">Nội dung</div>
                <div class="value"><?= htmlspecialchars($_GET['vnp_OrderInfo'] ?? '-') ?></div>
            </div>
        </div>

        <div class="mt-4">
            <a href="<?= htmlspecialchars($homeHref) ?>" class="btn-home-prominent">
                <i class="fa fa-home"></i> Về trang chủ
            </a>
        </div>

    </div>

    <div class="footer mt-3">
        © VNPAY <?= date('Y') ?> — Trang xác nhận giao dịch
    </div>

</div>
</body>
</html>
