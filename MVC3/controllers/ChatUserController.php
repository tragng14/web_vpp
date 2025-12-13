<?php

class ChatUserController extends Controller {

    // ----------- MẶC ĐỊNH: /Chat → show() ----------------
    public function index() {
        $chatModel = $this->model("ChatModel");

    if (!isset($_SESSION["user"])) {
        header('Location: ' . APP_URL . '/AuthController/ShowLogin');
        exit();
    }
        // Lấy email làm username
        $username = $_SESSION["user"]["email"];
      $messages = $chatModel->getMessages($username);
        $this->view("chatPage", [
    "messages" => $messages
]);

    }

    // ----------- HIỂN THỊ CHATBOX ------------------------
public function show() {
    $chatModel = $this->model("ChatModel");

    if (!isset($_SESSION["user"])) {
        header('Location: ' . APP_URL . '/AuthController/ShowLogin');
        exit();
    }

    // Lấy email làm username
    $username = $_SESSION["user"]["email"];

    $messages = $chatModel->getMessages($username);

    $this->view("homePage", [
        "page"      => "chatbox",
        "messages"  => $messages
    ]);
}



public function iframe() {
    $chatModel = $this->model("ChatModel");

    if (!isset($_SESSION["user"])) {
        echo "<p>Vui lòng đăng nhập để sử dụng chatbox</p>";
        return;
    }

    $username = $_SESSION["user"]["email"];
    $messages = $chatModel->getMessages($username);

    // Tải đúng file chatbox nhỏ dành riêng cho iframe
    $this->view("Font_end/chatbox", [
        "messages" => $messages
    ]);
}




    // ---------------- GỬI TIN NHẮN -------------------
  public function send() {
    if (!isset($_SESSION["user"])) {
        echo json_encode(["status" => "not_logged_in"]);
        return;
    }

    if (!isset($_POST["message"]) || trim($_POST["message"]) == "") {
        echo json_encode(["status" => "empty"]);
        return;
    }

    $username = $_SESSION["user"]["email"];
    $message = trim($_POST["message"]);

    $chatModel = $this->model("ChatModel");
    $chatModel->sendMessage($username, $message, "user");

    // Trả về phản hồi JSON để AJAX xử lý
    echo json_encode([
        "status" => "success",
        "message" => $message
    ]);
}

public function getMessages() {
    $chatModel = $this->model("ChatModel");
    $messages = $chatModel->getMessages($_SESSION["user"]["email"]);

    foreach ($messages as $msg) {
        $class = $msg["sent_by"] . "-msg";
        $name  = ($msg["sent_by"] == "user") ? "Bạn" : ucfirst($msg["sent_by"]);

        echo '<div class="message '.$class.'">
                <b>'.$name.':</b><br>'.$msg["message"].'<br>
                <small>'.$msg["created_at"].'</small>
              </div>';
    }
}


}


