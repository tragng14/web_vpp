<?php
declare(strict_types=1);

class UserModel extends DB {
    private string $table = "tbluser";

    // Properties (có thể gán trước khi gọi create)
    public $email;
    public $password;
    public $fullname;
    public $token;
    public $phone;    // mới
    public $address;  // mới

    /**
     * Tạo user mới (sử dụng properties)
     * @return bool
     */
 public function create($fullname, $email, $password, $role = "user"): bool {
    $query = "INSERT INTO {$this->table}
        (fullname, email, password, role, status, is_deleted, created_at)
        VALUES (:fullname, :email, :password, :role, 'Hoạt động', 0, :created_at)";

    $stmt = $this->db->prepare($query);
    $created_at = date('Y-m-d H:i:s');

    $stmt->bindParam(":fullname", $fullname);
    $stmt->bindParam(":email", $email);
    $stmt->bindParam(":password", $password);
    $stmt->bindParam(":role", $role);
    $stmt->bindParam(":created_at", $created_at);

    return $stmt->execute();
}


    /**
     * Lấy user theo email
     * @return array|false
     */
    public function getByEmail(string $email) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE email = :email LIMIT 1");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        return $res ?: false;
    }

    public function getUserByEmail(string $email) {
        return $this->getByEmail($email);
    }

    /**
     * Trả về PDOStatement để caller tuỳ fetch
     */
    public function findByEmail(string $email) {
        $query = "SELECT * FROM {$this->table} WHERE email = :email LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":email", $email);
        $stmt->execute();
        return $stmt;
    }

    /**
     * Dùng cho verify email (trả PDOStatement)
     */
    public function verify(string $token) {
        $query = "SELECT * FROM {$this->table} WHERE verification_token = :token AND is_verified = 0 LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":token", $token);
        $stmt->execute();
        return $stmt;
    }

    public function setVerified(string $token): bool {
        $query = "UPDATE {$this->table} SET is_verified = 1, verification_token = NULL WHERE verification_token = :token";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":token", $token);
        return $stmt->execute();
    }

    public function checkEmailExists(string $email): bool {
        $sql = "SELECT 1 FROM {$this->table} WHERE email = :email LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return (bool) $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function emailExists(string $email): bool {
        return $this->checkEmailExists($email);
    }

    public function updatePassword(string  $email, string $newPasswordHash): bool {
        $query = "UPDATE {$this->table} SET password = :password WHERE email = :email";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":password", $newPasswordHash);
        $stmt->bindParam(":email", $email);
        return $stmt->execute();
    }

    public function updatePasswordById(int $user_id, string $newPasswordHash): bool {
        $query = "UPDATE {$this->table} SET password = :password WHERE user_id = :uid";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([':password' => $newPasswordHash, ':uid' => $user_id]);
    }

    /**
     * Lấy tất cả user (có hỗ trợ keyword tìm kiếm)
     * @return array
     */
    public function getAll(string $keyword = ''): array {
        if ($keyword !== '') {
            $stmt = $this->db->prepare(
                "SELECT * FROM {$this->table}
                 WHERE fullname LIKE :kw OR email LIKE :kw OR phone LIKE :kw OR address LIKE :kw
                 ORDER BY created_at DESC"
            );
            $stmt->execute(['kw' => "%{$keyword}%"]);
        } else {
            $stmt = $this->db->query("SELECT * FROM {$this->table} ORDER BY created_at DESC");
        }
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $rows ?: [];
    }

    public function updateRole(int $id, string $role): bool {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET role = :role WHERE user_id = :id");
        return $stmt->execute(['role' => $role, 'id' => $id]);
    }

public function deleteUser($user_id) {
    $sql = "UPDATE tbluser SET is_deleted = 1 WHERE user_id = :id";
    $stmt = $this->db->prepare($sql);
    return $stmt->execute(['id' => $user_id]);
}


    public function getLastInsertedId() {
        return $this->db->lastInsertId();
    }

    /**
     * Lấy user theo user_id
     * @return array|false
     */
    public function getUserById($id) {
        $sql = "SELECT * FROM {$this->table} WHERE user_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: false;
    }

    public function updateUserWithoutPassword(string $email, string $fullname, string $role, string $status): bool {
        $sql = "UPDATE {$this->table} SET fullname = ?, role = ?, status = ? WHERE email = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$fullname, $role, $status, $email]);
    }

    public function insertUser(string $fullname, string $email, string $password, string $role = 'user', string $status = 'Hoạt động', $created_at = null, $phone = null, $address = null): bool {
        if ($created_at === null) {
            $created_at = date('Y-m-d H:i:s');
        }
        $sql = "INSERT INTO {$this->table}(fullname, email, password, role, status, created_at, phone, address, is_deleted)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, 0)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$fullname, $email, $password, $role, $status, $created_at, $phone, $address]);
    }

    public function findById($id) {
        return $this->getUserById($id);
    }

    /**
     * Cập nhật profile: chỉ cập nhật những field có trong $data
     * $data keys: fullname, avatar, phone, address
     */
    public function updateProfile(int $user_id, array $data): bool {
        $fields = [];
        $params = [':uid' => $user_id];

        if (array_key_exists('fullname', $data)) {
            if ($data['fullname'] !== null && $data['fullname'] !== '') {
                $fields[] = "fullname = :fullname";
                $params[':fullname'] = $data['fullname'];
            }
        }

        if (array_key_exists('avatar', $data) && $data['avatar'] !== null) {
            $fields[] = "avatar = :avatar";
            $params[':avatar'] = $data['avatar'];
        }

        if (array_key_exists('phone', $data) && $data['phone'] !== null) {
            $fields[] = "phone = :phone";
            $params[':phone'] = $data['phone'];
        }

        if (array_key_exists('address', $data) && $data['address'] !== null) {
            $fields[] = "address = :address";
            $params[':address'] = $data['address'];
        }

        if (empty($fields)) return false;

        $sql = "UPDATE {$this->table} SET " . implode(", ", $fields) . " WHERE user_id = :uid";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function updateAvatar(int $user_id, string $avatarFilename): bool {
        $sql = "UPDATE {$this->table} SET avatar = :avatar WHERE user_id = :uid";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':avatar' => $avatarFilename, ':uid' => $user_id]);
    }

    public function updateContact(int $user_id, $phone = null, $address = null): bool {
        $fields = [];
        $params = [':uid' => $user_id];
        if ($phone !== null) {
            $fields[] = "phone = :phone";
            $params[':phone'] = $phone;
        }
        if ($address !== null) {
            $fields[] = "address = :address";
            $params[':address'] = $address;
        }
        if (empty($fields)) return false;
        $sql = "UPDATE {$this->table} SET " . implode(', ', $fields) . " WHERE user_id = :uid";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }
}
