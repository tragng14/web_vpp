<?php
class InventoryController extends Controller {

    public function __construct() {
        $this->requireRole(['admin', 'staff']);
    }
    public function show() {
       
        $keyword = isset($_GET['keyword']) ? $_GET['keyword'] : '';
        $category = isset($_GET['category']) ? $_GET['category'] : '';

        #echo "DEBUG keyword=$keyword | category=$category";
            $productModel = $this->model("InventoryModel");
            $typeModel = $this->model("AdProductTypeModel");
    
          
            
    
          #  error_log("DEBUG keyword=$keyword | category=$category");
    
            $dataProducts = $productModel->getAllProducts($keyword, $category);
            $categories = $typeModel->getAll();
    
            $this->view("adminPage", [
                "page" => "InventoryView",
                "productList" => $dataProducts,
                "categories" => $categories,
                "keyword" => $keyword,
                "selectedCategory" => $category
            ]);
        }
    }
    

?>
