<?php
$activeTab = isset($_GET['tab']) ? $_GET['tab'] : 'ncc';
?>

<div class="container py-4">

    <!-- NAV TABS -->
    <ul class="nav nav-tabs mb-3" id="nccTabs">
        <li class="nav-item">
            <a class="nav-link <?= $activeTab == 'ncc' ? 'active' : '' ?>" 
   data-bs-toggle="tab" href="#tab-ncc">🏢 Nhà cung cấp</a>
        </li>

        <li class="nav-item">
            <a class="nav-link <?= $activeTab == 'hd' ? 'active' : '' ?>" 
   data-bs-toggle="tab" href="#tab-hd">📑 Hợp đồng</a>
        </li>

    </ul>

    <div class="tab-content">

        <!-- =================================================== -->
        <!-- TAB 1 - QUẢN LÝ NHÀ CUNG CẤP -->
        <!-- =================================================== -->
       <div class="tab-pane fade <?= $activeTab == 'ncc' ? 'show active' : '' ?>" id="tab-ncc">


            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="text-primary mb-0">🏢 Quản lý nhà cung cấp</h3>
                <a href="<?= APP_URL ?>/NhaCC/create" class="btn btn-success">
                    <i class="bi bi-plus-circle"></i> Thêm nhà cung cấp
                </a>
            </div>

            <!-- Thông báo -->
            <?php if(isset($_SESSION['success'])): ?>
                <div class="alert alert-success"><?= $_SESSION['success']; ?></div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>

            <?php if(isset($_SESSION['error'])): ?>
                <div class="alert alert-danger"><?= $_SESSION['error']; ?></div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <!-- Bảng Nhà cung cấp -->
            <div class="card shadow-sm">
                <div class="card-header bg-dark text-white">
                    <strong>Danh sách nhà cung cấp</strong>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">

                        <table class="table table-striped table-hover mb-0 align-middle text-center">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Mã NCC</th>
                                    <th>Tên NCC</th>
                                    <th>Điện thoại</th>
                                    <th>Email</th>
                                    <th>Địa chỉ</th>
                                    <th>Ngày tạo</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php 
                                if (!empty($data['data']['ncc'])) {
                                    $i = 1;
                                    foreach ($data['data']['ncc'] as $row) {
                                ?>
                                <tr>
                                    <td><?= $i++ ?></td>
                                    <td><?= $row['maNCC'] ?></td>
                                    <td><?= htmlspecialchars($row['tenNCC']) ?></td>
                                    <td><?= htmlspecialchars($row['sdt']) ?></td>
                                    <td><?= htmlspecialchars($row['email']) ?></td>
                                    <td><?= htmlspecialchars($row['diaChi']) ?></td>
                                    <td><?= date("d/m/Y", strtotime($row['createDate'])) ?></td>

                                    <td class="d-flex justify-content-center gap-1">
                                        <a href="<?= APP_URL ?>/NhaCC/edit/<?= $row['maNCC'] ?>" class="btn btn-info btn-sm text-white">✏️ Sửa</a>

                                        <a href="<?= APP_URL ?>/NhaCC/delete/<?= $row['maNCC'] ?>"
                                           class="btn btn-danger btn-sm"
                                           onclick="return confirm('Xóa nhà cung cấp này?');">
                                           🗑️ Xóa
                                        </a>
                                    </td>
                                </tr>
                                <?php 
                                    } 
                                } else { 
                                ?>
                                <tr>
                                    <td colspan="8" class="text-muted py-3">Chưa có nhà cung cấp nào.</td>
                                </tr>
                                <?php } ?>
                            </tbody>

                        </table>

                    </div>
                </div>
            </div>
        </div>

        <!-- =================================================== -->
        <!-- TAB 2 - QUẢN LÝ HỢP ĐỒNG NCC -->
        <!-- =================================================== -->
       <div class="tab-pane fade <?= $activeTab == 'hd' ? 'show active' : '' ?>" id="tab-hd">


            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="text-primary mb-0">📑 Quản lý hợp đồng NCC</h3>
                <a href="<?= APP_URL ?>/NhaCC/hd_create" class="btn btn-success">
                    <i class="bi bi-plus-circle"></i> Thêm hợp đồng
                </a>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-dark text-white">
                    <strong>Danh sách hợp đồng</strong>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">

                        <table class="table table-striped table-hover mb-0 text-center align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Mã HĐ</th>
                                    <th>Mã NCC</th>
                                    <th>Ngày bắt đầu</th>
                                    <th>Ngày kết thúc</th>
                                    <th>Giá trị</th>
                                    <th>Nội dung hợp đồng</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php 
                                if (!empty($data['data']['hopdong'])) {
                                    $i = 1;
                                    foreach ($data['data']['hopdong'] as $hd) {
                                ?>
                                <tr>
                                    <td><?= $i++ ?></td>
                                    <td><?= $hd['maHD'] ?></td>
                                    <td><?= $hd['maNCC'] ?></td>
                                    <td><?= date("d/m/Y", strtotime($hd['ngayKy'])) ?></td>
                                    <td><?= date("d/m/Y", strtotime($hd['ngayHetHan'])) ?></td>
                                    <td><?= number_format($hd['giaTri']) ?>₫</td>
                                    <td><?= htmlspecialchars($hd['noiDung']) ?></td>

                                    <td class="d-flex justify-content-center gap-1">
                                        <a href="<?= APP_URL ?>/NhaCC/hd_edit/<?= $hd['maHD'] ?>" 

                                           class="btn btn-info btn-sm text-white">✏️ Sửa</a>

                                        <a href="<?= APP_URL ?>/NhaCC/deleteContract/<?= $hd['maHD'] ?>" 
                                           onclick="return confirm('Có chắc xóa hợp đồng này?');"
                                           class="btn btn-danger btn-sm">🗑️ Xóa</a>
                                    </td>
                                </tr>
                                <?php 
                                    }
                                } else { 
                                ?>
                                <tr>
                                    <td colspan="8" class="text-muted py-3">Chưa có hợp đồng nào.</td>
                                </tr>
                                <?php } ?>
                            </tbody>

                        </table>

                    </div>
                </div>
            </div>

        </div>

        <!-- =================================================== -->
        <!-- TAB 3 - SẢN PHẨM NCC CUNG CẤP -->
        <!-- =================================================== -->
        <div class="tab-pane fade <?= $activeTab == 'ctsp' ? 'show active' : '' ?>" id="tab-ctsp">


            <h3 class="text-primary mb-3">📦 Danh mục hàng hóa NCC cung cấp</h3>

            <a href="<?= APP_URL ?>/NhaCC/addSupply" class="btn btn-success mb-3">
                <i class="bi bi-plus-circle"></i> Thêm sản phẩm NCC cung cấp
            </a>

            <div class="card shadow-sm">
                <div class="card-header bg-dark text-white">
                    <strong>Danh sách hàng NCC cung cấp</strong>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">

                        <table class="table table-striped table-hover mb-0 text-center align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Mã NCC</th>
                                    <th>Mã SP</th>
                                    <th>Giá nhập</th>
                                    <th>Ghi chú</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php 
                                if (!empty($data['data']['ct_nccsp'])) {
                                    $i = 1;
                                    foreach ($data['data']['ct_nccsp'] as $ct) {
                                ?>
                                <tr>
                                    <td><?= $i++ ?></td>
                                    <td><?= $ct['maNCC'] ?></td>
                                    <td><?= $ct['masp'] ?></td>
                                    <td><?= number_format($ct['gianhap']) ?>₫</td>
                                    <td><?= htmlspecialchars($ct['ghichu']) ?></td>

                                    <td class="d-flex justify-content-center gap-1">
                                        <a href="<?= APP_URL ?>/NhaCC/editSupply/<?= $ct['id'] ?>" 
                                           class="btn btn-info btn-sm text-white">✏️ Sửa</a>

                                        <a href="<?= APP_URL ?>/NhaCC/deleteSupply/<?= $ct['id'] ?>" 
                                           onclick="return confirm('Xóa dòng sản phẩm này?');"
                                           class="btn btn-danger btn-sm">🗑️ Xóa</a>
                                    </td>
                                </tr>

                                <?php 
                                    }
                                } else { 
                                ?>
                                <tr>
                                    <td colspan="6" class="text-muted py-3">Chưa có sản phẩm nào.</td>
                                </tr>
                                <?php } ?>
                            </tbody>

                        </table>

                    </div>
                </div>
            </div>

        </div>

    </div>
</div>
