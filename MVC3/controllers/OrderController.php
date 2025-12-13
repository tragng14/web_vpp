<?php
require_once 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * OrderController
 * - Quản lý danh sách đơn hàng (Admin)
 * - Chi tiết, cập nhật trạng thái, xác nhận thanh toán, huỷ đơn, in hoá đơn
 *
 * Lưu ý: file giả định Controller base cung cấp:
 *   - $this->model($name) để lấy model
 *   - $this->view($layout, $data) để render view
 *   - $this->requireRole($roles) để kiểm tra quyền truy cập
 *
 * Mình chỉ sửa/ thêm những phần cần thiết để an toàn và trực quan hơn.
 */
class OrderController extends Controller {

    public function __construct() {
        // Nếu framework của bạn cần start session ở đây, giữ nguyên
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    // -------------------------
    // HIỂN THỊ DANH SÁCH ĐƠN HÀNG (ADMIN)
    // -------------------------
    public function show() {
        // Yêu cầu quyền admin/staff
        $this->requireRole(['admin', 'staff']);

        // Lấy param từ query (sanitize cơ bản)
        $keyword = isset($_GET['keyword']) ? trim((string)$_GET['keyword']) : '';
        $status  = isset($_GET['status']) ? trim((string)$_GET['status']) : '';
        $date    = isset($_GET['date']) ? trim((string)$_GET['date']) : '';

        $orderModel = $this->model("OrderModel");

        try {
            $orders = $orderModel->filterOrders($keyword, $status, $date);
        } catch (Throwable $e) {
            // Log lỗi, tránh hiển thị ra user
            error_log("OrderController::show error: " . $e->getMessage());
            $orders = [];
        }

        // Render trang quản trị danh sách đơn
        $this->view("adminPage", [
            "page" => "OrderListView",
            "orders" => $orders,
            "keyword" => $keyword,
            "status" => $status,
            "date" => $date
        ]);
    }

    /**
     * Trả về chuỗi hiển thị trạng thái (dùng trong view)
     * @param string $status enum trạng thái
     * @param string|null $cancelled_by 'admin' | 'user' | null
     * @return string
     */
    public function showStatus($status, $cancelled_by = null) {
        switch ($status) {
            case 'pending':   return "Chờ xử lý";
            case 'approved':  return "Đã duyệt";
            case 'shipping':  return "Đang giao";
            case 'completed': return "Hoàn thành";
            case 'cancelled':
                if ($cancelled_by === 'admin') return "Admin đã hủy đơn";
                if ($cancelled_by === 'user')  return "User đã tự hủy đơn";
                return "Đã hủy";
            default:
                return ucfirst($status);
        }
    }

    // -------------------------
    // CHI TIẾT ĐƠN HÀNG (ADMIN)
    // -------------------------
    public function detail($id) {
        $this->requireRole(['admin', 'staff']);

        $orderModel = $this->model("OrderModel");

        // Bảo vệ tham số id
        $id = is_numeric($id) ? (int)$id : null;
        if ($id === null) {
            die("ID đơn hàng không hợp lệ.");
        }

        try {
            $order = $orderModel->getOrderWithFinalTotal($id);
            $details = $orderModel->getOrderDetailsByOrderId($id);
        } catch (Throwable $e) {
            error_log("OrderController::detail error: " . $e->getMessage());
            $order = null;
            $details = [];
        }

        $this->view("adminPage", [
            "page" => "OrderDetailAdd",
            "order" => $order,
            "details" => $details
        ]);
    }

    // -------------------------
    // ADMIN: CẬP NHẬT TRẠNG THÁI ĐƠN HÀNG
    // (pending / approved / shipping / completed / cancelled)
    // -------------------------
    public function updateStatus($id) {
        $this->requireRole(['admin', 'staff']);

        // Kiểm tra tham số status trong GET/POST
        $status = $_REQUEST['status'] ?? null;
        if ($status === null) {
            // không có status -> redirect về list
            header("Location: " . APP_URL . "/Order");
            exit;
        }

        // sanitize
        $status = trim((string)$status);
        $valid = ['pending','approved','shipping','completed','cancelled'];
        if (!in_array($status, $valid, true)) {
            $_SESSION['error'] = "Trạng thái không hợp lệ.";
            header("Location: " . APP_URL . "/Order");
            exit;
        }

        $orderModel = $this->model("OrderModel");

        try {
            if ($status === 'cancelled') {
                // Hoàn kho trước khi đánh dấu hủy
                $orderModel->restoreStockAfterCancel($id);
                $orderModel->cancelOrder($id, 'admin');
            } else {
                $orderModel->updateStatus($id, $status);
            }

            // Gửi email thông báo trạng thái (nếu có hàm)
            if (method_exists($this, 'sendStatusEmail')) {
                try { $this->sendStatusEmail($id, $status); } catch (Throwable $e) { error_log("sendStatusEmail error: ".$e->getMessage()); }
            }

            $_SESSION['success'] = "Cập nhật trạng thái thành công.";
        } catch (Throwable $e) {
            error_log("OrderController::updateStatus error: " . $e->getMessage());
            $_SESSION['error'] = "Cập nhật trạng thái thất bại.";
        }

        header("Location: " . APP_URL . "/Order");
        exit;
    }

    // -------------------------
    // ADMIN: XÁC NHẬN THANH TOÁN (mark as paid)
    // -------------------------
    public function confirmPayment($orderId) {
        $this->requireRole(['admin', 'staff']);
        $orderModel = $this->model("OrderModel");

        try {
            // Đánh dấu đã thanh toán (model sẽ xử lý trừ kho nếu cần)
            $orderModel->markAsPaid($orderId);

            // Gửi email thông báo
            if (method_exists($this, 'sendStatusEmail')) {
                try { $this->sendStatusEmail($orderId, 'approved'); } catch (Throwable $e) { error_log("sendStatusEmail error: ".$e->getMessage()); }
            }

            $_SESSION['success'] = "Xác nhận thanh toán thành công.";
        } catch (Throwable $e) {
            error_log("OrderController::confirmPayment error: " . $e->getMessage());
            $_SESSION['error'] = "Xác nhận thanh toán thất bại.";
        }

        header("Location: " . APP_URL . "/Order");
        exit;
    }

    // -------------------------
    // USER: XÁC NHẬN ĐÃ NHẬN HÀNG (completed)
    // -------------------------
    public function confirmReceived($orderId) {
        $orderModel = $this->model("OrderModel");

        try {
            $orderModel->updateStatus($orderId, 'completed');
            if (method_exists($this, 'sendStatusEmail')) {
                try { $this->sendStatusEmail($orderId, 'completed'); } catch (Throwable $e) { error_log("sendStatusEmail error: ".$e->getMessage()); }
            }
            $_SESSION['success'] = "Cảm ơn — đơn hàng đã được đánh dấu là hoàn thành.";
        } catch (Throwable $e) {
            error_log("OrderController::confirmReceived error: " . $e->getMessage());
            $_SESSION['error'] = "Không thể xác nhận nhận hàng.";
        }

        header("Location: " . APP_URL . "/Home/orderHistory");
        exit;
    }

    // -------------------------
    // HỦY ĐƠN (ADMIN hoặc USER)
    // - Admin: hủy từ trang admin -> hoàn kho + cập nhật cancelled_by = 'admin'
    // - User: tự hủy -> cancelled_by = 'user'
    // -------------------------
    public function cancelOrder($orderId) {
        // Xác định role requester
        $role = 'user';
        if (!empty($_SESSION['user']['role']) && strtolower($_SESSION['user']['role']) === 'admin') {
            $role = 'admin';
        }

        $orderModel = $this->model("OrderModel");

        try {
            // Hoàn kho
            $orderModel->restoreStockAfterCancel($orderId);

            // Cập nhật trạng thái hủy
            $orderModel->cancelOrder($orderId, $role);

            // Gửi email thông báo
            if (method_exists($this, 'sendStatusEmail')) {
                try { $this->sendStatusEmail($orderId, 'cancelled'); } catch (Throwable $e) { error_log("sendStatusEmail error: ".$e->getMessage()); }
            }

            $_SESSION['success'] = "Đã hủy đơn.";
        } catch (Throwable $e) {
            error_log("OrderController::cancelOrder error: " . $e->getMessage());
            $_SESSION['error'] = "Hủy đơn thất bại.";
        }

        // Redirect về trang phù hợp
        if ($role === 'admin') {
            header("Location: " . APP_URL . "/Order");
        } else {
            header("Location: " . APP_URL . "/Home/orderHistory");
        }
        exit;
    }

    // -------------------------
    // IN HOÁ ĐƠN (ADMIN)
    // -------------------------
    public function printInvoice($id) {
        $this->requireRole(['admin', 'staff']);
        $orderModel = $this->model("OrderModel");

        try {
            $order = $orderModel->getOrderWithFinalTotal($id);
            $items = $orderModel->getOrderDetailsByOrderId($id);
        } catch (Throwable $e) {
            error_log("OrderController::printInvoice error: " . $e->getMessage());
            die("Đơn hàng không tồn tại hoặc có lỗi.");
        }

        if (!$order) {
            die("Đơn hàng không tồn tại!");
        }

        // Render view in hóa đơn (adminPage -> printInvoice)
        $this->view("adminPage", [
            "page" => "printInvoice",
            "order" => $order,
            "items" => $items
        ]);
    }

    // -------------------------
    // HÀM PHỤ: gửi email khi thay đổi trạng thái
    // - Nếu bạn không muốn gửi email, có thể comment hoặc override
    // -------------------------
    protected function sendStatusEmail($orderId, $newStatus) {
        // Lấy model order để lấy thông tin email
        try {
            $orderModel = $this->model("OrderModel");
            if (!$orderModel) return false;

            $order = $orderModel->getOrderWithFinalTotal($orderId);
            if (empty($order) || empty($order['user_email'])) return false;

            $to = $order['user_email'];
            $customer = $order['receiver'] ?? ($order['customer_name'] ?? 'Khách hàng');

            $subject = "Cập nhật đơn hàng #{$order['order_code']}";
            $statusText = $this->showStatus($newStatus, $order['cancelled_by'] ?? null);
            $body = "<p>Xin chào <strong>" . htmlspecialchars($customer) . "</strong>,</p>";
            $body .= "<p>Đơn hàng <strong>#{$order['order_code']}</strong> đã được cập nhật trạng thái: <strong>{$statusText}</strong>.</p>";
            $body .= "<p>Chi tiết đơn và liên hệ hỗ trợ vui lòng truy cập tài khoản hoặc trả lời email này.</p>";
            $body .= "<p>Trân trọng,<br/>Shop của bạn</p>";

            // PHPMailer cấu hình cơ bản (bạn cần sửa theo môi trường)
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = getenv('SMTP_HOST') ?: 'smtp.example.com';
            $mail->SMTPAuth = true;
            $mail->Username = getenv('SMTP_USER') ?: 'user@example.com';
            $mail->Password = getenv('SMTP_PASS') ?: 'password';
            $mail->SMTPSecure = getenv('SMTP_SECURE') ?: PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = getenv('SMTP_PORT') ? intval(getenv('SMTP_PORT')) : 587;

            $fromAddr = getenv('MAIL_FROM') ?: 'no-reply@example.com';
            $fromName = getenv('MAIL_FROM_NAME') ?: 'Shop';

            $mail->setFrom($fromAddr, $fromName);
            $mail->addAddress($to, $customer);
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $body;

            $mail->send();
            return true;
        } catch (Throwable $e) {
            error_log("OrderController::sendStatusEmail error: " . $e->getMessage());
            return false;
        }
    }
}
