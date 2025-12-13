<?php
require_once "BaseModel.php";

class NhaCCModel extends BaseModel
{
    /* =====================================================
       1) BẢNG tblnhacungcap – THÔNG TIN NHÀ CUNG CẤP
    ===================================================== */

    public function getAll()
    {
        $sql = "SELECT * FROM tblnhacungcap ORDER BY createDate DESC";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findbyID($maNCC)
    {
        $sql = "SELECT * FROM tblnhacungcap WHERE maNCC = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$maNCC]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function insert($data)
    {
        $sql = "INSERT INTO tblnhacungcap (maNCC, tenNCC, diaChi, sdt, email, nguoiLH, ghiChu)
                VALUES (?,?,?,?,?,?,?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['maNCC'], $data['tenNCC'], $data['diaChi'],
            $data['sdt'], $data['email'], $data['nguoiLH'], $data['ghiChu']
        ]);
    }

    public function update($data)
    {
        $sql = "UPDATE tblnhacungcap 
                SET tenNCC=?, diaChi=?, sdt=?, email=?, nguoiLH=?, ghiChu=?, updatedAt=NOW()
                WHERE maNCC=?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['tenNCC'], $data['diaChi'], $data['sdt'],
            $data['email'], $data['nguoiLH'], $data['ghiChu'], $data['maNCC']
        ]);
    }

public function deleteNCC($maNCC)
{
    // kiểm tra hợp đồng còn tồn tại?
    $checkHD = $this->db->prepare("SELECT COUNT(*) FROM tblhopdong WHERE maNCC=?");
    $checkHD->execute([$maNCC]);
    $countHD = $checkHD->fetchColumn();

    if ($countHD > 0) {
        return [
            "success" => false,
            "message" => "Không thể xóa vì nhà cung cấp còn hợp đồng!"
        ];
    }

    // kiểm tra sản phẩm NCC cung cấp?
    $checkSP = $this->db->prepare("SELECT COUNT(*) FROM tblct_ncc_sanpham WHERE maNCC=?");
    $checkSP->execute([$maNCC]);
    $countSP = $checkSP->fetchColumn();

    if ($countSP > 0) {
        return [
            "success" => false,
            "message" => "Không thể xóa vì nhà cung cấp còn cung cấp sản phẩm!"
        ];
    }

    // Nếu không còn gì liên quan thì xóa
    $stmt = $this->db->prepare("DELETE FROM tblnhacungcap WHERE maNCC=?");
    $stmt->execute([$maNCC]);

    return ["success" => true];
}





    /* =====================================================
       2) BẢNG tblhopdong – HỢP ĐỒNG 1 : N VỚI NCC
    ===================================================== */

    public function getContracts($maNCC)
    {
        $sql = "SELECT * FROM tblhopdong WHERE maNCC=? ORDER BY ngayKy DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$maNCC]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getContract($maHD)
    {
        $sql = "SELECT * FROM tblhopdong WHERE maHD=?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$maHD]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function insertContract($data)
    {
        $sql = "INSERT INTO tblhopdong
                (maHD, maNCC, tenHD, ngayKy, ngayHetHan, giaTri, trangThai, noiDung)
                VALUES (?,?,?,?,?,?,?,?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['maHD'], $data['maNCC'], $data['tenHD'], $data['ngayKy'],
            $data['ngayHetHan'], $data['giaTri'], $data['trangThai'], $data['noiDung']
        ]);
    }

    public function updateContract($data)
    {
        $sql = "UPDATE tblhopdong 
                SET tenHD=?, ngayKy=?, ngayHetHan=?, giaTri=?, trangThai=?, noiDung=?, updatedAt=NOW()
                WHERE maHD=?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['tenHD'], $data['ngayKy'], $data['ngayHetHan'], 
            $data['giaTri'], $data['trangThai'], $data['noiDung'], $data['maHD']
        ]);
    }

    public function deleteContract($maHD)
    {
        $sql = "DELETE FROM tblhopdong WHERE maHD=?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$maHD]);
    }




    /* =====================================================
       3) BẢNG tblct_ncc_sanpham – NCC CUNG CẤP SẢN PHẨM
    ===================================================== */

    public function getProductsByNCC($maNCC)
    {
        $sql = "SELECT c.*, s.tensp, s.hinhanh, s.giaNhap, s.giaXuat
                FROM tblct_ncc_sanpham c
                JOIN tblsanpham s ON c.masp = s.masp
                WHERE c.maNCC=?
                ORDER BY s.tensp ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$maNCC]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function insertNCC_Product($data)
    {
        $sql = "INSERT INTO tblct_ncc_sanpham (maNCC, masp, giaNhapNCC, thongTinThem)
                VALUES (?,?,?,?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['maNCC'], $data['masp'], $data['giaNhapNCC'], $data['thongTinThem']
        ]);
    }

    public function updateNCC_Product($data)
    {
        $sql = "UPDATE tblct_ncc_sanpham 
                SET giaNhapNCC=?, thongTinThem=?, ngayCapNhat=NOW()
                WHERE maNCC=? AND masp=?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['giaNhapNCC'], $data['thongTinThem'], $data['maNCC'], $data['masp']
        ]);
    }

    public function deleteNCC_Product($maNCC, $masp)
    {
        $sql = "DELETE FROM tblct_ncc_sanpham WHERE maNCC=? AND masp=?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$maNCC, $masp]);
    }

    public function getAllContracts() {
    $sql = "SELECT * FROM tblhopdong ORDER BY ngayKy DESC";
    $stmt = $this->db->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

public function getAllNCCProducts() {
    $sql = "SELECT * FROM tblct_ncc_sanpham ORDER BY id DESC";
    $stmt = $this->db->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

}
