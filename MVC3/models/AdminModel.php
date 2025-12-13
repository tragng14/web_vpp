<?php
class AdminModel extends DB {
    private $table = "tbluser";

    // ✅ Tạo tài khoản mới (mặc định trạng thái "Hoạt động")
public function create($fullname, $email, $password, $role = "user", $status = "Hoạt động", $avatar = null, $phone, $address) {
    $query = "INSERT INTO {$this->table} 
              (fullname, email, password, role, avatar, status, is_deleted, created_at, phone, address) 
              VALUES (:fullname, :email, :password, :role, :avatar, :status, 0, NOW(), :phone, :address)";

    $stmt = $this->db->prepare($query);

    $stmt->bindParam(":fullname", $fullname);
    $stmt->bindParam(":email", $email);
    $stmt->bindParam(":password", $password);
    $stmt->bindParam(":role", $role);
    $stmt->bindParam(":avatar", $avatar);
    $stmt->bindParam(":status", $status);
    $stmt->bindParam(":phone", $phone);
    $stmt->bindParam(":address", $address);

    return $stmt->execute();
}



    // ✅ Lấy thông tin user theo email
    public function getByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE email = :email AND is_deleted = 0 LIMIT 1");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // ✅ Lấy toàn bộ tài khoản 
    public function getAll() {
        $sql = "SELECT * FROM {$this->table} WHERE 1 ORDER BY created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ✅ Lấy tài khoản theo ID
    public function getUserById($user_id) {
        $sql = "SELECT * FROM {$this->table} WHERE user_id = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $user_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // ✅ Cập nhật thông tin user (admin có thể đổi trạng thái)
public function updateUser($user_id, $fullname, $email, $role, $status, $avatar, $password = null, $phone, $address) {
    if ($password !== null) {
        $query = "UPDATE {$this->table} 
                  SET fullname = :fullname,
                      email = :email,
                      role = :role,
                      status = :status,
                      avatar = :avatar,
                      password = :password,
                      phone = :phone,
                      address = :address
                  WHERE user_id = :id";
    } else {
        $query = "UPDATE {$this->table} 
                  SET fullname = :fullname,
                      email = :email,
                      role = :role,
                      status = :status,
                      avatar = :avatar,
                      phone = :phone,
                      address = :address
                  WHERE user_id = :id";
    }

    $stmt = $this->db->prepare($query);

    $stmt->bindParam(":fullname", $fullname);
    $stmt->bindParam(":email", $email);
    $stmt->bindParam(":role", $role);
    $stmt->bindParam(":status", $status);
    $stmt->bindParam(":avatar", $avatar);
    $stmt->bindParam(":phone", $phone);
    $stmt->bindParam(":address", $address);

    if ($password !== null) {
        $stmt->bindParam(":password", $password);
    }

    $stmt->bindParam(":id", $user_id);

    return $stmt->execute();
}

    

    // ✅ Tạm ngưng tài khoản
    public function suspendUser($user_id) {
        $sql = "UPDATE {$this->table} SET status = 'Tạm ngưng' WHERE user_id = :id AND is_deleted = 0";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $user_id);
        return $stmt->execute();
    }

    // ✅ Mở khóa lại tài khoản
    public function activateUser($user_id) {
        $sql = "UPDATE {$this->table} SET status = 'Hoạt động' WHERE user_id = :id AND is_deleted = 0";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $user_id);
        return $stmt->execute();
    }

    // ✅ Xóa mềm tài khoản (người dùng hoặc admin)
    public function deleteUser($user_id) {
        $sql = "UPDATE {$this->table} SET is_deleted = 1 WHERE user_id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $user_id);
        return $stmt->execute();
    }

    // ✅ Reset mật khẩu cho user (admin sử dụng)
    public function resetPasswordByAdmin($user_id, $newPassword) {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $sql = "UPDATE {$this->table} SET password = :password WHERE user_id = :id AND is_deleted = 0";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':id', $user_id);
        return $stmt->execute();
    }

    // ✅ Cấp quyền admin
    public function grantAdminRole($user_id) {
        $sql = "UPDATE {$this->table} SET role = 'admin' WHERE user_id = :id AND is_deleted = 0";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $user_id);
        return $stmt->execute();
    }

    // ✅ Hạ quyền về user
    public function revokeAdminRole($user_id) {
        $sql = "UPDATE {$this->table} SET role = 'staff' WHERE user_id = :id AND is_deleted = 0";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $user_id);
        return $stmt->execute();
    }

    // ✅ Tìm kiếm người dùng (bỏ qua tài khoản đã xóa)
    public function searchUsers($keyword) {
        $sql = "SELECT * FROM {$this->table} 
                WHERE (fullname LIKE :kw OR email LIKE :kw) AND is_deleted = 0
                ORDER BY created_at DESC";
        $stmt = $this->db->prepare($sql);
        $like = "%".$keyword."%";
        $stmt->bindParam(':kw', $like);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
// ✅ Kiểm tra tài khoản có bị xóa không
public function isUserDeleted($user_id) {
    $sql = "SELECT is_deleted FROM {$this->table} WHERE user_id = :id LIMIT 1";
    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(':id', $user_id);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ? (int)$row['is_deleted'] === 1 : false;
}

// ✅ Khôi phục tài khoản chỉ khi đã bị xóa
public function restoreUser($user_id) {
    if (!$this->isUserDeleted($user_id)) {
        return false; // không thực hiện khôi phục nếu chưa bị xóa
    }
    $sql = "UPDATE {$this->table} 
            SET is_deleted = 0, status = 'Hoạt động' 
            WHERE user_id = :id";
    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(':id', $user_id);
    return $stmt->execute();
}
    // ✅ Cập nhật quyền người dùng (nếu cần)
    public function updateRole($id, $role) {
        $sql = "UPDATE $this->table SET role = ? WHERE user_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$role, $id]);
    }
    
public function getAll2($keyword = "", $role = "")
{
    // Chống lỗi do keyword là mảng
    if (is_array($keyword)) $keyword = "";

    $keyword = trim(mb_strtolower($keyword));
    $where = [];
    $params = [];

    // Tìm kiếm fullname, email
    if ($keyword !== "") {
        $where[] = "(LOWER(fullname) LIKE ? OR LOWER(email) LIKE ?)";
        $params[] = "%$keyword%";
        $params[] = "%$keyword%";
    }

    // Lọc quyền
    if (in_array($role, ["admin", "user", "staff"])) {
        $where[] = "role = ?";
        $params[] = $role;
    }

    // ❗ KHÔNG LỌC is_deleted → để hiện cả 0 và 1

    $sql = "SELECT * FROM tbluser";

    if (!empty($where)) {
        $sql .= " WHERE " . implode(" AND ", $where);
    }

    $sql .= " ORDER BY created_at DESC";

    $stmt = $this->db->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


#lấy danh sách khách hàng + lượt mua + tổng chi tiêu
public function getCustomers($keyword = "")
{
    $sql = "
    SELECT 
        u.user_id,
        u.fullname,
        u.email,
        u.avatar,
        u.created_at,

        COALESCE(o.total_orders, 0) AS total_orders,
        COALESCE(o.total_spent, 0) AS total_spent,
        COALESCE(p.total_products, 0) AS total_products,
        o.last_order_date

    FROM tbluser u

    /* Bảng tính tổng đơn và tổng chi tiêu */
    LEFT JOIN (
        SELECT 
            user_id,
            COUNT(id) AS total_orders,
            SUM(total_amount) AS total_spent,
            MAX(created_at) AS last_order_date
        FROM orders
        WHERE transaction_info = 'dathanhtoan'
          AND cancelled_by IS NULL
        GROUP BY user_id
    ) o ON o.user_id = u.user_id

    /* Bảng tính tổng sản phẩm đã mua */
    LEFT JOIN (
        SELECT 
            o.user_id,
            SUM(od.quantity) AS total_products
        FROM orders o
        JOIN order_details od ON od.order_id = o.id
        WHERE o.transaction_info = 'dathanhtoan'
          AND o.cancelled_by IS NULL
        GROUP BY o.user_id
    ) p ON p.user_id = u.user_id

    WHERE u.role = 'user'
      AND u.is_deleted = 0
";


    $params = [];
if (!empty($keyword)) {
    $sql .= " AND (u.fullname LIKE :kw1 OR u.email LIKE :kw2) ";
    $params[':kw1'] = "%".$keyword."%";
    $params[':kw2'] = "%".$keyword."%";
}


    $sql .= "
        GROUP BY u.user_id
        ORDER BY total_spent DESC
        LIMIT 500
    ";

    $stmt = $this->db->prepare($sql);
    $stmt->execute($params);
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    /* =====================================
       TÍNH HẠNG KHÁCH HÀNG + AOV
    ====================================== */
    foreach ($data as &$c) {
        $orders = (int)$c['total_orders'];
        $spent  = (int)$c['total_spent'];

        // Chi tiêu trung bình mỗi đơn (AOV)
        $c['aov'] = $orders > 0 ? round($spent / $orders) : 0;

        // Xếp hạng khách hàng
        if ($spent >= 5000000) {
            $c['rank'] = "VIP";
        } elseif ($spent >= 1000000) {
            $c['rank'] = "Thân thiết";
        } else {
            $c['rank'] = "Mới";
        }
    }

    return $data;
}

public function updateProfile($id, $fullname, $phone, $address, $avatar)
{
    $sql = "UPDATE tbluser 
            SET fullname = ?, phone = ?, address = ?, avatar = ?
            WHERE user_id = ?";

    $stmt = $this->db->prepare($sql);
    return $stmt->execute([$fullname, $phone, $address, $avatar, $id]);
}


public function updatePassword($userId, $hashedPassword)
{
    $sql = "UPDATE tbluser SET password = ? WHERE user_id = ?";
    $stm = $this->db->prepare($sql);
    return $stm->execute([$hashedPassword, $userId]);
}
public function getById($id)
{
    try {
        $sql = "SELECT * FROM tbluser WHERE user_id = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        error_log("getById error: " . $e->getMessage());
        return null;
    }
}


}
?>
 