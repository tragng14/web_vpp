

<?php

class ChatController extends Controller {

    // Trang tổng hợp danh sách người dùng đã nhắn tin
    public function show() {
        $chatModel = $this->model("ChatModel");

        // Lấy danh sách user đã gửi tin nhắn
       $users = $chatModel->getAllLatestUserMessages();


        $this->view("adminPage", [
            "page"  => "ChatListView",
            "users" => $users
        ]);
    }

    // Xem toàn bộ tin nhắn của 1 user
    public function viewUserMessages($email) {
        $chatModel = $this->model("ChatModel");

        $messages = $chatModel->getMessages($email);

        $this->view("adminPage", [
            "page"     => "ChatDetail",
            "messages" => $messages,
            "email"    => $email
        ]);
    }

    // Admin gửi tin nhắn trả lời
    public function sendReply() {
        $chatModel = $this->model("ChatModel");

        $email   = $_POST['email'];      // User mà admin trả lời
        $message = $_POST['message'];    // Nội dung admin trả lời

        $chatModel->sendMessage($email, $message, "admin");

        // reload về trang chi tiết chat
        header("Location: " . APP_URL . "/ChatAdminController/viewUserMessages/" . $email);
        exit();
    }

    // Load form phản hồi
    public function reply($email) {
        $this->view("adminPage", [
            "page"  => "ChatReply",
            "email" => $email
        ]);
    }

    // Xử lý gửi phản hồi
    public function doReply() {
        if (!isset($_POST['email']) || !isset($_POST['message'])) {
            die("Dữ liệu không hợp lệ!");
        }

        $email = $_POST['email'];
        $message = $_POST['message'];

        $chatModel = $this->model("ChatModel");
        $chatModel->replyMessage($email, $message);

        $messages = $chatModel->getMessages($email);

        $this->view("adminPage", [
            "page"     => "ChatDetail",
            "messages" => $messages,
            "email"    => $email
        ]);

        
    }
}
