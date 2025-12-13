<form action="" method="post" enctype="multipart/form-data" class="container mt-4">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">
                <?php echo isset($data['editItem']) ? 'Chỉnh sửa bài viết' : 'Thêm bài viết mới'; ?>
            </h5>
        </div>
        </div>
        <div class="card-body row g-3">
            <div class="col-md-6">
                <?php 
                if (isset($data['editItem']) && $data['editItem']['image']) {
                    echo "<img src='" . APP_URL . "/public/images/" . $data['editItem']['image'] . "' 
                          class='img-thumbnail mb-2' style='height: 10rem; width: auto;'>";
                }
                else {?>
                    <img src="<?php echo APP_URL?>/public/images/defaut.png" >
              <?php  }
                ?>
            </div>


            <!-- Cột nhập dữ liệu -->
            <div class="col-md-6">
                <label class="form-label">Tiêu đề bài viết</label>
                <input type="text" name="title" class="form-control" 
                       value="<?php echo isset($data['editItem']) ? htmlspecialchars($data['editItem']['title']) : ''; ?>" 
                       required>

                <br>
                <label class="form-label">Hình ảnh</label>
                <input type="file" name="image" class="form-control">
            </div>

            <!-- Nội dung -->
            <div class="col-md-12">
                <label class="form-label">Nội dung</label>
                <textarea name="content" rows="6" class="form-control" 
                          placeholder="Nhập nội dung bài viết..."><?php 
                          echo isset($data['editItem']) ? htmlspecialchars($data['editItem']['content']) : ''; ?></textarea>
            </div>

            <!-- Trạng thái -->
            <div class="col-md-6">
                <label class="form-label">Trạng thái</label>
                    <select name="status" class="form-select">
                        <option value="hiển thị">Hiển thị</option>
                        <option value="ẩn">Ẩn</option>
                    </select>
            </div>

            <!-- Ngày tạo -->
            <div class="col-md-6">
                <label class="form-label">Ngày tạo</label>
                <input type="datetime-local" name="created_at" class="form-control"
              <?php date_default_timezone_set('Asia/Ho_Chi_Minh'); ?>

value="<?php 
echo isset($data['editItem']) 
    ? date('Y-m-d\TH:i', strtotime($data['editItem']['created_at'])) 
    : date('Y-m-d\TH:i');
?>">

            </div>

        </div>

        <!-- Nút lưu -->
        <div class="card-footer text-end">
            <input type="submit" name="btn_submit" 
                   class="btn btn-<?php echo isset($data['editItem']) ? 'warning' : 'success'; ?>" 
                   value="<?php echo isset($data['editItem']) ? 'Cập nhật bài viết' : 'Lưu bài viết'; ?>">
        </div>
    </div>
</form>