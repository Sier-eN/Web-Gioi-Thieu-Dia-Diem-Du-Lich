<div class="data-section">
    <div class="section-header">
        <h2><i class="fa-solid fa-map-location-dot"></i> Hệ thống Quản lý Địa điểm</h2>
        <button class="btn-add" onclick="openDiaDiemModal()"><i class="fa-solid fa-plus"></i> Thêm địa điểm mới</button>
    </div>
    
    <table class="custom-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Hình ảnh</th>
                <th>Tên Địa Điểm</th>
                <th>Danh Mục</th>
                <th>Vùng Miền</th>
                <th>Lượt Xem</th>
                <th>Hành Động</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($danhSachDiaDiem) > 0): ?>
                <?php foreach ($danhSachDiaDiem as $dd): ?>
                    <tr>
                        <td>#<?php echo $dd['MaDiaDiem']; ?></td>
                        <td>
                            <img src="<?php echo !empty($dd['HinhAnhChinh']) ? '../images/'.$dd['HinhAnhChinh'] : 'https://picsum.photos/60/40'; ?>" alt="Ảnh" class="table-img" style="width:60px; height:40px; object-fit:cover; border-radius:4px;">
                        </td>
                        <td><strong><?php echo htmlspecialchars($dd['TenDiaDiem']); ?></strong></td>
                        <td><span class="badge" style="background-color: #e3f2fd; color: #0d47a1;"><?php echo htmlspecialchars($dd['TenDanhMuc']); ?></span></td>
                        <td><?php echo htmlspecialchars($dd['VungMien']); ?></td>
                        <td><i class="fa-regular fa-eye"></i> <?php echo number_format($dd['LuotXem']); ?></td>
                        <td>
                            <button class="btn-action btn-edit" onclick="openEditDiaDiemModal(<?php echo $dd['MaDiaDiem']; ?>, '<?php echo addslashes($dd['TenDiaDiem']); ?>', <?php echo $dd['MaDanhMuc']; ?>, '<?php echo $dd['VungMien']; ?>', '<?php echo addslashes($dd['DiaChi']); ?>', '<?php echo addslashes($dd['MoTaNgan']); ?>', '<?php echo addslashes($dd['ChiTiet']); ?>', '<?php echo $dd['HinhAnhChinh']; ?>')" title="Sửa địa điểm">
                                <i class="fa-solid fa-pen"></i>
                            </button>
                            
                            <button class="btn-action btn-delete" onclick="xoaDiaDiem(<?php echo $dd['MaDiaDiem']; ?>)" title="Xóa địa điểm">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" style="text-align: center; color: #777; padding: 20px;">Chưa có địa điểm du lịch nào được tạo.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div id="diaDiemModal" class="custom-modal" style="display:none;">
    <div class="modal-content" style="width: 550px; margin: 4% auto;"> 
        <h3 id="ddModalTitle"></h3>
        
        <form id="formDiaDiem" onsubmit="saveDiaDiem(event)">
            <input type="hidden" id="dd_id" name="id">
            
            <div class="form-group">
                <label>Tên Địa Điểm Du Lịch</label>
                <input type="text" id="dd_ten" name="tendiadiem" required class="form-control">
            </div>
            
            <div style="display: flex; gap: 15px;">
                <div class="form-group" style="flex: 1;">
                    <label>Danh Mục Phân Loại</label>
                    <select id="dd_danhmuc" name="madanhmuc" required class="form-control">
                        <option value="">-- Chọn danh mục --</option>
                        <?php foreach ($danhSachDanhMuc as $dm): ?>
                            <option value="<?php echo $dm['MaDanhMuc']; ?>"><?php echo htmlspecialchars($dm['TenDanhMuc']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group" style="flex: 1;">
                    <label>Vùng Miền / Khu Vực</label>
                    <select id="dd_vungmien" name="vungmien" required class="form-control">
                        <option value="Miền Bắc">Miền Bắc</option>
                        <option value="Miền Trung">Miền Trung</option>
                        <option value="Miền Nam">Miền Nam</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label>Địa Chỉ Chi Tiết</label>
                <input type="text" id="dd_diachi" name="diachi" required class="form-control" placeholder="Ví dụ: Hạ Long, Quảng Ninh...">
            </div>

            <div class="form-group">
                <label>Tên File Hình Ảnh (Đại diện)</label>
                <input type="text" id="dd_hinhanh" name="hinhanhchinh" class="form-control" placeholder="Ví dụ: halong.jpg (Lưu trong thư mục images)">
            </div>

            <div class="form-group">
                <label>Mô Tả Ngắn Gọn</label>
                <input type="text" id="dd_motangan" name="motangan" class="form-control">
            </div>

            <div class="form-group">
                <label>Bài Viết Giới Thiệu Chi Tiết</label>
                <textarea id="dd_chitiet" name="chitiet" class="form-control" rows="5" style="resize: none; font-family: inherit;"></textarea>
            </div>

            <div class="modal-actions">
                <button type="button" onclick="closeDiaDiemModal()" class="btn-cancel">Hủy</button>
                <button type="submit" class="btn-save">Lưu dữ liệu</button>
            </div>
        </form>
    </div>
</div>