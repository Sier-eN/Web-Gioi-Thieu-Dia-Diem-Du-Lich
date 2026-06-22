<?php
$host = 'localhost';
$dbname = 'DuLichDB';
$username = 'root';
$password = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // --- XỬ LÝ LỆNH KHÓA TÀI KHOẢN ---
    if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
        $id = $_GET['id'];
        $stmtDel = $conn->prepare("UPDATE TaiKhoan SET TrangThai = 'Bị khóa' WHERE MaTaiKhoan = ? AND STRCASECMP(VaiTro, 'Admin') != 0");
        $stmtDel->execute([$id]);
        echo "SUCCESS";
        exit;
    }

    // --- XỬ LÝ LỆNH MỞ KHÓA TÀI KHOẢN ---
    if (isset($_GET['action']) && $_GET['action'] == 'activate' && isset($_GET['id'])) {
        $id = $_GET['id'];
        $stmtAct = $conn->prepare("UPDATE TaiKhoan SET TrangThai = 'Hoạt động' WHERE MaTaiKhoan = ?");
        $stmtAct->execute([$id]);
        echo "SUCCESS";
        exit;
    }

    // --- XỬ LÝ LỆNH XÓA VĨNH VIỄN (MỚI THÊM) ---
    if (isset($_GET['action']) && $_GET['action'] == 'destroy' && isset($_GET['id'])) {
        $id = $_GET['id'];
        
        $stCheck = $conn->prepare("SELECT VaiTro FROM TaiKhoan WHERE MaTaiKhoan = ?");
        $stCheck->execute([$id]);
        $role = $stCheck->fetchColumn();

        if (strcasecmp($role, 'Admin') == 0) {
            echo "Không được phép xóa tài khoản Administrator.";
            exit;
        }

        try {
            $stmtDestroy = $conn->prepare("DELETE FROM TaiKhoan WHERE MaTaiKhoan = ?");
            $stmtDestroy->execute([$id]);
            echo "SUCCESS";
        } catch (PDOException $ex) {
            echo "Tài khoản này đang chứa dữ liệu liên quan (như bài viết, đánh giá...), không thể xóa cứng! Hãy dùng chức năng Khóa.";
        }
        exit;
    }

    $stmt = $conn->query("SELECT MaTaiKhoan, TenDangNhap, HoTen, Email, SoDienThoai, VaiTro, TrangThai FROM TaiKhoan ORDER BY NgayTao DESC");
    $danhSachTaiKhoan = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo 'Lỗi: ' . $e->getMessage();
    exit;
}
?>

<div class="data-section">
    <div class="section-header">
        <h2><i class="fa-solid fa-user-gear"></i> Hệ thống Quản lý Tài khoản</h2>
        <button class="btn-add" onclick="openAddModal()"><i class="fa-solid fa-user-plus"></i> Thêm tài khoản mới</button>
    </div>
    
    <table class="custom-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Họ và Tên</th>
                <th>Tên Đăng Nhập</th>
                <th>Email</th>
                <th>Vai Trò</th>
                <th>Trạng Thái</th>
                <th>Hành Động</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($danhSachTaiKhoan as $tk): ?>
                <tr id="row-<?php echo $tk['MaTaiKhoan']; ?>">
                    <td>#<?php echo $tk['MaTaiKhoan']; ?></td>
                    <td><strong><?php echo htmlspecialchars($tk['HoTen']); ?></strong></td>
                    <td><span style="color: #1e3d59; font-weight: 600;"><?php echo htmlspecialchars($tk['TenDangNhap']); ?></span></td>
                    <td><?php echo htmlspecialchars($tk['Email']); ?></td>
                    <td><?php echo htmlspecialchars($tk['VaiTro']); ?></td>
                    <td>
                        <?php if ($tk['TrangThai'] == 'Hoạt động'): ?>
                            <span class="badge badge-active">Hoạt động</span>
                        <?php else: ?>
                            <span class="badge" style="background-color: #ffebee; color: #c62828;">Bị khóa</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <button class="btn-action btn-edit" onclick="openEditModal(<?php echo $tk['MaTaiKhoan']; ?>, '<?php echo addslashes($tk['HoTen']); ?>', '<?php echo addslashes($tk['TenDangNhap']); ?>', '<?php echo addslashes($tk['Email']); ?>', '<?php echo $tk['VaiTro']; ?>')">
                            <i class="fa-solid fa-pen"></i>
                        </button>
                        
                        <?php if (strcasecmp($tk['VaiTro'], 'Admin') == 0): ?>
                            <button class="btn-action" style="background-color: #cfd8dc; color: #37474f; cursor: not-allowed;" disabled><i class="fa-solid fa-user-lock"></i></button>
                            <button class="btn-action" style="background-color: #cfd8dc; color: #37474f; cursor: not-allowed;" disabled><i class="fa-solid fa-trash"></i></button>
                        <?php else: ?>
                            <?php if ($tk['TrangThai'] == 'Hoạt động'): ?>
                                <button class="btn-action" style="background-color: #ff9800; color: white;" onclick="khoaTaiKhoan(<?php echo $tk['MaTaiKhoan']; ?>)" title="Khóa tài khoản">
                                    <i class="fa-solid fa-user-lock"></i>
                                </button>
                            <?php else: ?>
                                <button class="btn-action" style="background-color: #4caf50; color: white;" onclick="moKhoaTaiKhoan(<?php echo $tk['MaTaiKhoan']; ?>)" title="Mở khóa tài khoản">
                                    <i class="fa-solid fa-user-check"></i>
                                </button>
                            <?php endif; ?>

                            <button class="btn-action btn-delete" onclick="xoaHanTaiKhoan(<?php echo $tk['MaTaiKhoan']; ?>)" title="Xóa vĩnh viễn">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div id="taiKhoanModal" class="custom-modal" style="display:none;">
    <div class="modal-content">
        <h3 id="modalTitle"></h3>
        
        <form id="formTaiKhoan" onsubmit="saveTaiKhoan(event)">
            <input type="hidden" id="modal_id" name="id">
            
            <div class="form-group">
                <label>Họ và Tên</label>
                <input type="text" id="modal_hoten" name="hoten" required class="form-control">
            </div>
            
            <div class="form-group">
                <label>Tên Đăng Nhập</label>
                <input type="text" id="modal_tendangnhap" name="tendangnhap" required class="form-control">
            </div>

            <div class="form-group" id="password-group">
                <label>Mật Khẩu</label>
                <input type="password" id="modal_matkhau" name="matkhau" class="form-control">
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email" id="modal_email" name="email" required class="form-control">
            </div>

            <div class="form-group">
                <label>Vai Trò</label>
                <select id="modal_vaitro" name="vaitro" class="form-control">
                    <option value="User">User</option>
                    <option value="Admin">Admin</option>
                </select>
            </div>

            <div class="modal-actions">
                <button type="button" onclick="closeModal()" class="btn-cancel">Hủy</button>
                <button type="submit" class="btn-save">Lưu lại</button>
            </div>
        </form>
    </div>
</div>