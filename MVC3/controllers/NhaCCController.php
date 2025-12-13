<?php 
class NhaCCController extends Controller {

    // ============================================================
    // 1) NHÀ CUNG CẤP (CRUD)
    // ============================================================
    public function __construct() {
        $this->requireRole(['admin', 'staff']);
    }
    public function show()
    {
        
        $model = $this->model("NhaCCModel");

        // Lấy danh sách NCC
        $nccList = $model->getAll();
        // Lấy toàn bộ hợp đồng
$contracts = $model->getAllContracts();

// Lấy toàn bộ danh sách sản phẩm NCC cung cấp
$products = $model->getAllNCCProducts();

        $this->view("adminPage", [
            "page"  => "NhaCCListView",
            "data"  => [
                "ncc"       => $nccList,
                "hopdong"   => $contracts,
                "ct_nccsp"  => $products
            ]
        ]);
    }



    // Form Thêm
    public function create()
    {
        $this->view("adminPage", [
            "page" => "NhaCCAddView",
            "edit" => false,
            "row" => null
        ]);
    }

    // Xử lý thêm
    public function store()
    {
        if ($_SERVER["REQUEST_METHOD"] !== "POST") die("Invalid Request!");

        $data = [
            "maNCC"   => $_POST["maNCC"],
            "tenNCC"  => $_POST["tenNCC"],
            "diaChi"  => $_POST["diaChi"],
            "sdt"     => $_POST["sdt"],
            "email"   => $_POST["email"],
            "nguoiLH" => $_POST["nguoiLH"],
            "ghiChu"  => $_POST["ghiChu"]
        ];

        $model = $this->model("NhaCCModel");
        $model->insert($data);

        header("Location: " . APP_URL . "/NhaCC/show");
          
        exit;
    }

    // Form sửa
    public function edit($maNCC)
    {
        $model = $this->model("NhaCCModel");
        $row = $model->findbyID($maNCC);

        if (!$row) die("❌ Không tìm thấy NCC!");

        $this->view("adminPage", [
            "page" => "NhaCCAddView",
            "edit" => true,
            "row"  => $row
        ]);
    }

    // Xử lý cập nhật
    public function update($maNCC)
    {
        $model = $this->model("NhaCCModel");
          $row = $model->findbyID($maNCC);
          if (!$row) die("❌ Không tìm thấy NCC!");
        $data = [
            "maNCC"   => $maNCC,
            "tenNCC"  => $_POST["tenNCC"],
            "diaChi"  => $_POST["diaChi"],
            "sdt"     => $_POST["sdt"],
            "email"   => $_POST["email"],
            "nguoiLH" => $_POST["nguoiLH"],
            "ghiChu"  => $_POST["ghiChu"]
        ];

        $model = $this->model("NhaCCModel");
        $model->update($data);

        header("Location: " . APP_URL . "/NhaCC/show");
        exit;
    }

    // Xóa NCC
    public function delete($maNCC)
    {
        $model = $this->model("NhaCCModel");
        $result = $model->deleteNCC($maNCC);
if (!$result["success"]) {
    $_SESSION["error"] = $result["message"];
    header("Location: " . APP_URL . "/NhaCC");
    exit;
}

        header("Location: " . APP_URL . "/NhaCC/show");
        exit;
    }

    // ============================================================
    // 2) HỢP ĐỒNG
    // ============================================================

    public function hopdong($maNCC)
    {
        $model = $this->model("NhaCCModel");
        $ncc = $model->getNCC($maNCC);
        $hopdong = $model->getContract_ByNCC($maNCC);

        $this->view("adminPage", [
            "page" => "Nhacc/show",
            "ncc" => $ncc,
            "hopdong" => $hopdong
        ]);
    }

public function hd_create()
{
    $model = $this->model("NhaCCModel");
    $nccList = $model->getAll();

    $this->view("adminPage", [
        "page" => "HopDongAddView",
        "edit" => false,
        "nccList" => $nccList
    ]);
}


    public function hd_store()
    {
        $model = $this->model("NhaCCModel");

        $data = [
            "maHD"      => $_POST["maHD"],
            "maNCC"     => $_POST["maNCC"],
            "tenHD"     => $_POST["tenHD"],
            "ngayKy"    => $_POST["ngayKy"],
            "ngayHetHan"=> $_POST["ngayHetHan"],
            "giaTri"    => $_POST["giaTri"],
            "trangThai" => $_POST["trangThai"],
            "noiDung"   => $_POST["noiDung"]
        ];

        $model->insertContract($data);

      header("Location: " . APP_URL . "/NhaCC/show?tab=hd");
exit;

    }

    public function hd_edit($maHD)
    {
        $model = $this->model("NhaCCModel");
        $row   = $model->getContract($maHD);

        $this->view("adminPage", [
            "page" => "HopDongAddView",
            "edit" => true,
            "row"  => $row
        ]);
    }

    public function hd_update($maHD)
    {
        $model = $this->model("NhaCCModel");

        $data = [
            "maHD"      => $maHD,
            "tenHD"     => $_POST["tenHD"],
            "ngayKy"    => $_POST["ngayKy"],
            "ngayHetHan"=> $_POST["ngayHetHan"],
            "giaTri"    => $_POST["giaTri"],
            "trangThai" => $_POST["trangThai"],
            "noiDung"   => $_POST["noiDung"]
        ];

        $model->updateContract($data);

        $hd = $model->getContract($maHD);

        header("Location: /NhaCC/hopdong/" . $hd["maNCC"]);
        exit;
    }

    public function hd_delete($maHD)
    {
        $model = $this->model("NhaCCModel");
        $hd = $model->getContract($maHD);

        $model->deleteContract($maHD);

        header("Location: /NhaCC/hopdong/" . $hd["maNCC"]);
        exit;
    }

    // ============================================================
    // 3) SẢN PHẨM DO NCC CUNG CẤP
    // ============================================================

    public function sanpham($maNCC)
    {
        $model = $this->model("NhaCCModel");
        $ncc = $model->getNCC($maNCC);
        $sanpham = $model->getSP_ByNCC($maNCC);

        $modelSP = $this->model("AdProducModel");
        $listSP = $modelSP->getAll();

        $this->view("adminPage", [
            "page" => "Nhacc/sanpham",
            "ncc" => $ncc,
            "sanpham" => $sanpham,
            "listSP" => $listSP
        ]);
    }

    public function sp_store()
    {
        $model = $this->model("NhaCCModel");

        $data = [
            "maNCC"       => $_POST["maNCC"],
            "masp"        => $_POST["masp"],
            "giaNhapNCC"  => $_POST["giaNhapNCC"],
            "thongTinThem"=> $_POST["thongTinThem"]
        ];

        $model->addNCC_Product($data);

        header("Location: /NhaCC/sanpham/" . $data["maNCC"]);
        exit;
    }

    public function sp_update()
    {
        $model = $this->model("NhaCCModel");

        $data = [
            "maNCC"       => $_POST["maNCC"],
            "masp"        => $_POST["masp"],
            "giaNhapNCC"  => $_POST["giaNhapNCC"],
            "thongTinThem"=> $_POST["thongTinThem"]
        ];

        $model->updateNCC_Product($data);

        header("Location: /NhaCC/sanpham/" . $data["maNCC"]);
        exit;
    }

    public function sp_delete($maNCC, $masp)
    {
        $model = $this->model("NhaCCModel");

        $model->deleteNCC_Product($maNCC, $masp);

        header("Location: /NhaCC/sanpham/" . $maNCC);
        exit;
    }
}
