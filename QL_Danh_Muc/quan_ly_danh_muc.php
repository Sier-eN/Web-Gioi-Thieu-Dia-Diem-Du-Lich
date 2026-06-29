<?php
$host = 'localhost';
$dbname = 'DuLichDB';
$username = 'root';
$password = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // --- XỬ LÝ LỆNH XÓA DANH MỤC ---
    if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
        $id = $_GET['id'];
        $stmtDel = $conn->prepare("DELETE FROM DanhMuc WHERE MaDanhMuc = ?");
        $stmtDel->execute([$id]);
        echo "SUCCESS";
        exit;
    }

    // --- TRUY VẤN LẤY DANH SÁCH & TỰ ĐỘNG ĐẾM SỐ LƯỢNG ĐỊA ĐIỂM ---
    $sql = "SELECT dm.MaDanhMuc, dm.TenDanhMuc, dm.MoTa, COUNT(dd.MaDiaDiem) AS SoLuongDiaDiem
            FROM DanhMuc dm
            LEFT JOIN DiaDiem dd ON dm.MaDanhMuc = dd.MaDanhMuc
            GROUP BY dm.MaDanhMuc, dm.TenDanhMuc, dm.MoTa
            ORDER BY dm.MaDanhMuc DESC";
            
    $stmt = $conn->query($sql);
    $danhSachDanhMuc = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo 'Lỗi: ' . $e->getMessage();
    exit;
}
?>

<div class="data-section">
    <div class="section-header">
        <h2><i class="fa-solid fa-tags"></i> Hệ thống Quản lý Danh mục</h2>
        <button class="btn-add" onclick="openDanhMucModal()"><i class="fa-solid fa-plus"></i> Thêm danh mục mới</button>
    </div>
    
    <table class="custom-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Tên Danh Mục</th>
                <th>Mô Tả Ý Nghĩa</th>
                <th>Số Lượng Địa Điểm</th>
                <th>Hành Động</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($danhSachDanhMuc) > 0): ?>
                <?php foreach ($danhSachDanhMuc as $dm): ?>
                    <tr>
                        <td>#<?php echo $dm['MaDanhMuc']; ?></td>
                        <td><strong><?php echo htmlspecialchars($dm['TenDanhMuc']); ?></strong></td>
                        <td><?php echo htmlspecialchars($dm['MoTa'] ?? 'Chưa có mô tả'); ?></td>
                        <td>
                            <span class="badge" style="background-color: #e8f5e9; color: #2e7d32; font-weight: bold;">
                                <i class="fa-solid fa-map-pin"></i> <?php echo $dm['SoLuongDiaDiem']; ?> địa điểm
                            </span>
                        </td>
                        <td>
                            <button class="btn-action btn-edit" onclick="openEditDanhMucModal(<?php echo $dm['MaDanhMuc']; ?>, '<?php echo addslashes($dm['TenDanhMuc']); ?>', '<?php echo addslashes($dm['MoTa']); ?>')" title="Sửa danh mục">
                                <i class="fa-solid fa-pen"></i>
                            </button>
                            
                            <button class="btn-action btn-delete" onclick="xoaDanhMuc(<?php echo $dm['MaDanhMuc']; ?>)" title="Xóa danh mục">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" style="text-align: center; color: #777; padding: 20px;">Chưa có danh mục nào được tạo.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div id="danhMucModal" class="custom-modal" style="display:none;">
    <div class="modal-content">
        <h3 id="dmModalTitle"></h3>
        
        <form id="formDanhMuc" onsubmit="saveDanhMuc(event)">
            <input type="hidden" id="dm_id" name="id">
            
            <div class="form-group">
                <label>Tên Danh Mục</label>
                <input type="text" id="dm_ten" name="tendanhmuc" required class="form-control" placeholder="Ví dụ: Du lịch Biển, Ẩm thực Hà Nội...">
            </div>
            
            <div class="form-group">
                <label>Mô Tả Chi Tiết</label>
                <textarea id="dm_mota" name="mota" class="form-control" rows="4" placeholder="Nhập mô tả ngắn gọn về danh mục này..." style="resize: none; font-family: inherit;"></textarea>
            </div>

            <div class="modal-actions">
                <button type="button" onclick="closeDanhMucModal()" class="btn-cancel">Hủy</button>
                <button type="submit" class="btn-save">Lưu lại</button>
            </div>
        </form>
    </div>
</div>