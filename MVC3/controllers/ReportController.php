<?php
class ReportController extends Controller {
    public function __construct() {
      $this->requireRole(['admin']);
    }
    /**
     * ✅ Hiển thị báo cáo doanh thu
     */
   public function show() {
        $obj = $this->model("ReportModel");

        $date = $_GET['date'] ?? date('Y-m-d');
        $filterType = $_GET['type'] ?? 'day';

        // ======================== 
        // Dữ liệu báo cáo chính
        // ========================
        $reportData     = $obj->getReportData($date, $filterType);
        $byCategory     = $obj->getRevenueByCategory($date, $filterType);
        $summary        = $obj->getSummary($date, $filterType);
        $revenueTime    = $obj->getRevenueByTime($date, $filterType);
        $topProducts    = $obj->getTopProducts($date, $filterType);

        // ======================== 
        // Bổ sung: bán chậm & chưa bán
        // ========================
        $slowProducts      = $obj->getSlowProducts($date, $filterType);   // Bán ít nhưng > 0
        $neverSoldProducts = $obj->getNeverSoldProducts();                // Chưa bán lần nào

        // ======================== 
        // Tạo tiêu đề
        // ========================
        switch ($filterType) {
            case 'month': 
                $title = "Báo cáo doanh thu tháng " . date('m/Y', strtotime($date)); 
                break;
            case 'year':  
                $title = "Báo cáo doanh thu năm " . date('Y', strtotime($date)); 
                break;
            default:      
                $title = "Báo cáo doanh thu ngày " . date('d/m/Y', strtotime($date)); 
                break;
        }

        // ======================== 
        // Gửi dữ liệu sang View
        // ========================
        $this->view("adminPage", [
            "page"              => "ReportView",

            // Dữ liệu mặc định
            "reportData"        => $reportData,
            "byCategory"        => $byCategory,
            "summary"           => $summary,
            "revenueTime"       => $revenueTime,
            "topProducts"       => $topProducts,

            // Dữ liệu mới thêm
            "slowProducts"      => $slowProducts,       // sản phẩm bán chậm (đã bán > 0)
            "neverSoldProducts" => $neverSoldProducts,  // sản phẩm chưa bán (bằng 0)

            "filterType"        => $filterType,
            "date"              => $date,
            "title"             => $title
        ]);
    }




    /**
     * ✅ Lọc nhanh theo ngày / tháng / năm (nếu cần)
     */
    public function filter() {
        // Gọi lại hàm show() để hiển thị kết quả lọc
        $this->show();
    }


}
?>
