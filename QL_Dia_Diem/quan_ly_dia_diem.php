<?php
$host = 'localhost';
$dbname = 'DuLichDB';
$username = 'root';
$password = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // --- 1. XỬ LÝ LỆNH XÓA ĐỊA ĐIỂM ---
    if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
        $id = $_GET['id'];
        $stmtDel = $conn->prepare("DELETE FROM DiaDiem WHERE MaDiaDiem = ?");
        $stmtDel->execute([$id]);
        echo "SUCCESS";
        exit;
    }

    // --- 2. TRUY VẤN LẤY DANH SÁCH ĐỊA ĐIỂM (JOIN để lấy tên danh mục) ---
    $sqlDiaDiem = "SELECT dd.*, dm.TenDanhMuc 
                   FROM DiaDiem dd 
                   INNER JOIN DanhMuc dm ON dd.MaDanhMuc = dm.MaDanhMuc 
                   ORDER BY dd.NgayDang DESC";
    $stmtDD = $conn->query($sqlDiaDiem);
    $danhSachDiaDiem = $stmtDD->fetchAll(PDO::FETCH_ASSOC);

    // --- 3. TRUY VẤN LẤY DANH SÁCH DANH MỤC CHO THẺ SELECT ---
    $stmtDM = $conn->query("SELECT MaDanhMuc, TenDanhMuc FROM DanhMuc ORDER BY TenDanhMuc ASC");
    $danhSachDanhMuc = $stmtDM->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo 'Lỗi hệ thống: ' . $e->getMessage();
    exit;
}
?>

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
                            <img src="<?php echo !empty($dd['HinhAnhChinh']) ? '../images/'.$dd['HinhAnhChinh'] : '../images/default.jpg'; ?>" alt="Ảnh địa điểm" class="table-img" style="width:60px; height:40px; object-fit:cover; border-radius:4px;">
                        </td>
                        <td><strong><?php echo htmlspecialchars($dd['TenDiaDiem']); ?></strong></td>
                        <td><span class="badge" style="background-color: #e3f2fd; color: #0d47a1;"><?php echo htmlspecialchars($dd['TenDanhMuc']); ?></span></td>
                        <td><?php echo htmlspecialchars($dd['VungMien']); ?></td>
                        <td><i class="fa-regular fa-eye"></i> <?php echo number_format($dd['LuotXem']); ?></td>
                        <td>
                            <?php 
                            // Mã hóa toàn bộ object địa điểm sang chuỗi JSON an toàn, chống vỡ dòng
                            $json_data = htmlspecialchars(json_encode($dd, JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8');
                            ?>
                            <button class="btn-action btn-edit" onclick="openEditDiaDiemModal('<?php echo $json_data; ?>')" title="Sửa địa điểm">
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
                <label>Hình Ảnh Minh Họa</label>
                <input type="file" id="dd_file_anh" name="hinhanh_file" accept="image/*" class="form-control" onchange="previewImage(this)">
                
                <input type="hidden" id="dd_hinhanh_cu" name="hinhanhchinh_cu">
                
                <div style="margin-top: 10px;">
                    <img id="img-preview" src="" style="max-width: 120px; max-height: 80px; object-fit: cover; border-radius: 4px; display: none; border: 1px dashed #cbd5e1;">
                </div>
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