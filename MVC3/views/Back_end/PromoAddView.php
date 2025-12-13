<form action="" method="post" class="container mt-4">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><?php echo isset($data['editItem']) ? 'Chỉnh sửa mã khuyến mãi' : 'Thêm mã khuyến mãi mới'; ?></h5>
        </div>
        <div class="card-body row g-3">

            <div class="col-md-6">
                <label class="form-label">Mã khuyến mãi</label>
                <input type="text" name="txt_code" class="form-control"
                       value="<?php echo isset($data['editItem']) ? $data['editItem']['code'] : ''; ?>"
                       <?php echo isset($data['editItem']) ? 'readonly' : ''; ?>>
            </div>

            <div class="col-md-6">
    <label class="form-label">Loại giảm giá</label>
    <select name="txt_type" class="form-select">
        <?php
        $types = [
            'percent' => 'Giảm theo phần trăm (%)',
            'amount' => 'Giảm theo số tiền (VNĐ)' // <-- gõ mới tay dòng này
        ];
        
        foreach ($types as $key => $label) {
            $selected = (isset($data['editItem']) && $data['editItem']['type'] == $key) ? "selected" : "";
            echo "<option value='{$key}' $selected>{$label}</option>";
        }
        ?>
    </select>
</div>

            <div class="col-md-6">
                <label class="form-label">Giá trị</label>
                <input type="number" step="0.01" name="txt_value" class="form-control"
                       value="<?php echo isset($data['editItem']) ? $data['editItem']['value'] : '0.00'; ?>">
            </div>

            <div class="col-md-6">
    <label class="form-label">Tổng giá trị đơn hàng tối thiểu để áp dụng</label>
    <input type="number" step="0.01" name="txt_min_total" class="form-control"
           value="<?php echo isset($data['editItem']) ? $data['editItem']['min_total'] : '0.00'; ?>">
</div>

            <div class="col-md-6">
                <label class="form-label">Giới hạn sử dụng</label>
                <input type="number" name="txt_usage_limit" class="form-control"
                       value="<?php echo isset($data['editItem']) ? $data['editItem']['usage_limit'] : ''; ?>">
            </div>

            <div class="col-md-6">
    <label class="form-label">Ngày bắt đầu</label>
    <input type="date" name="txt_start_date" class="form-control"
           value="<?php echo isset($data['editItem']) ? date('Y-m-d', strtotime($data['editItem']['start_date'])) : ''; ?>">
</div>

<div class="col-md-6">
    <label class="form-label">Ngày kết thúc</label>
    <input type="date" name="txt_end_date" class="form-control"
           value="<?php echo isset($data['editItem']) ? date('Y-m-d', strtotime($data['editItem']['end_date'])) : ''; ?>">
</div>



<div class="col-md-6">
    <label class="form-label">Trạng thái</label>
    <select name="txt_status" class="form-select">
        <?php
        $statuses = [
            'active' => 'Đang hoạt động',
            'inactive' => 'Tạm ngưng',
            'deleted' => 'Hết hạn'
        ];
        
        foreach ($statuses as $key => $label) {
            $selected = (isset($data['editItem']) && $data['editItem']['status'] == $key) ? "selected" : "";
            echo "<option value='{$key}' $selected>{$label}</option>";
        }
        ?>
    </select>
</div>

            <div class="col-md-6">
    <label class="form-label">Ngày tạo</label>
    <input type="date" name="txt_created_at" class="form-control"
           value="<?php echo isset($data['editItem']) ? date('Y-m-d', strtotime($data['editItem']['created_at'])) : date('Y-m-d'); ?>" readonly>
</div>


        </div>

        <div class="card-footer text-end">
            <input type="submit" name="btn_submit"
                   class="btn btn-<?php echo isset($data['editItem']) ? 'warning' : 'success'; ?>"
                   value="<?php echo isset($data['editItem']) ? 'Cập nhật' : 'Lưu'; ?>">
        </div>
    </div>
</form>
