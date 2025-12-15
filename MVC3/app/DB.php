<?php
class DB {
    private $host   = 'sql310.infinityfree.com';
    private $user   = 'if0_40674409';
    private $pass   = 'YOUR_VPANEL_PASSWORD'; // <-- thay bằng mật khẩu vPanel
    private $dbname = 'if0_40674409_web_vpp';

    protected $db;

    public function __construct() {
        $this->db = $this->connect();
        $this->db->exec("SET NAMES utf8mb4");
    }

    private function connect() {
        $dsn = "mysql:host={$this->host};dbname={$this->dbname};charset=utf8mb4";

        try {
            return new PDO($dsn, $this->user, $this->pass, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false
            ]);
        } catch (PDOException $e) {
            die("Lỗi kết nối database: " . $e->getMessage());
        }
    }

    // Trả về PDO — dùng để truy vấn
    public function getConnection() {
        return $this->db;
    }

    // Alias để tương thích code cũ
    public function getPdo() {
        return $this->db;
    }
}
