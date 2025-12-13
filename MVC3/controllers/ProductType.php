<?php
class ProductType extends Controller {
    public function __construct() {
        $this->requireRole(['admin', 'staff']);
    }
    // ---------------- HIỂN THỊ DANH SÁCH ----------------
    public function show() {
        $obj = $this->model("AdProductTypeModel");
        $data = $obj->getAll(); // Đúng với model đã sửa
        
        $this->view("adminPage", [
            "page" => "ProductTypeView",
            "productList" => $data
        ]);
    }

    // ---------------- XOÁ ----------------
    public function delete($id) {
        $obj = $this->model("AdProductTypeModel");
        $obj->delete("tblloaisp", $id);

        header("Location: " . APP_URL . "/ProductType/");
        exit();
    }

    // ---------------- THÊM ----------------
    public function create() {
        $txt_maloaisp   = $_POST["txt_maloaisp"] ?? "";
        $txt_tenloaisp  = $_POST["txt_tenloaisp"] ?? "";
        $txt_motaloaisp = $_POST["txt_motaloaisp"] ?? "";

        $obj = $this->model("AdProductTypeModel");
        $obj->insert($txt_maloaisp, $txt_tenloaisp, $txt_motaloaisp);

        header("Location: " . APP_URL . "/ProductType/");
        exit();
    }

    // ---------------- NÚT SỬA (HIỂN THỊ LÊN FORM) ----------------
    public function edit($maLoaiSP) {
        $obj = $this->model("AdProductTypeModel");

        $product = $obj->find("tblloaisp", $maLoaiSP);
        $productList = $obj->getAll(); // Đúng với model

        $this->view("adminPage", [
            "page" => "ProductTypeView",
            "productList" => $productList,
            "editItem" => $product
        ]);
    }

    // ---------------- CẬP NHẬT ----------------
    public function update($maLoaiSP) {
        $tenLoaiSP = $_POST['txt_tenloaisp'];
        $moTaLoaiSP = $_POST['txt_motaloaisp'];

        $obj = $this->model("AdProductTypeModel");
        $obj->update($maLoaiSP, $tenLoaiSP, $moTaLoaiSP);

        header("Location: " . APP_URL . "/ProductType/");
        exit();
    }

    // ---------------- TÌM KIẾM ----------------
    public function search() {
        $keyword = trim($_GET['keyword'] ?? "");

        $productTypeModel = $this->model("AdProductTypeModel");

        $result = $keyword !== "" 
            ? $productTypeModel->searchByKeyword($keyword)
            : $productTypeModel->getAll();

        $this->view("adminPage", [
            "page" => "ProductTypeView",
            "productList" => $result,
            "keyword" => $keyword
        ]);
    }

}
