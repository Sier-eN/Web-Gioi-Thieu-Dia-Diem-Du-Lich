<?php
// 1. Kết nối cơ sở dữ liệu MySQL (XAMPP)
$host = 'localhost';
$dbname = 'DuLichDB';
$username = 'root';
$password = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 2. Truy vấn lấy toàn bộ danh sách tài khoản, sắp xếp theo ngày tạo mới nhất
    $stmt = $conn->query("SELECT MaTaiKhoan, TenDangNhap, HoTen, Email, SoDienThoai, VaiTro, TrangThai FROM TaiKhoan ORDER BY NgayTao DESC");
    $danhSachTaiKhoan = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo '<div style="padding: 20px; color: red; font-weight: bold;"><i class="fa-solid fa-triangle-exclamation"></i> Lỗi kết nối dữ liệu tài khoản: ' . $e->getMessage() . '</div>';
    exit;
}
?>

<div class="data-section">
    <div class="section-header">
        <h2><i class="fa-solid fa-user-gear"></i> Hệ thống Quản lý Tài khoản</h2>
        <button class="btn-add"><i class="fa-solid fa-user-plus"></i> Thêm tài khoản mới</button>
    </div>
    
    <table class="custom-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Họ và Tên</th>
                <th>Tên Đăng Nhập</th>
                <th>Email</th>
                <th>Số Điện Thoại</th>
                <th>Vai Trò</th>
                <th>Trạng Thái</th>
                <th>Hành Động</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($danhSachTaiKhoan) > 0): ?>
                <?php foreach ($danhSachTaiKhoan as $tk): ?>
                    <tr>
                        <td>#<?php echo $tk['MaTaiKhoan']; ?></td>
                        <td><strong><?php echo htmlspecialchars($tk['HoTen']); ?></strong></td>
                        <td><span style="color: #1e3d59; font-weight: 600;"><?php echo htmlspecialchars($tk['TenDangNhap']); ?></span></td>
                        <td><?php echo htmlspecialchars($tk['Email'] ?? 'Chưa cập nhật'); ?></td>
                        <td><?php echo htmlspecialchars($tk['SoDienThoai'] ?? 'Chưa cập nhật'); ?></td>
                        <td>
                            <?php if (strcasecmp($tk['VaiTro'], 'Admin') == 0): ?>
                                <span class="badge" style="background-color: #e3f2fd; color: #0d47a1;">Admin</span>
                            <?php else: ?>
                                <span class="badge" style="background-color: #f5f5f5; color: #616161;">User</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($tk['TrangThai'] == 'Hoạt động'): ?>
                                <span class="badge badge-active">Hoạt động</span>
                            <?php else: ?>
                                <span class="badge" style="background-color: #ffebee; color: #c62828;">Bị khóa</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <button class="btn-action btn-edit" title="Sửa tài khoản">
                                <i class="fa-solid fa-pen"></i>
                            </button>
                            
                            <?php if (strcasecmp($tk['VaiTro'], 'Admin') == 0): ?>
                                <button class="btn-action" style="background-color: #cfd8dc; color: #37474f; cursor: not-allowed;" title="Không thể khóa tài khoản Admin" disabled>
                                    <i class="fa-solid fa-user-lock"></i>
                                </button>
                            <?php else: ?>
                                <?php if ($tk['TrangThai'] == 'Hoạt động'): ?>
                                    <button class="btn-action" style="background-color: #ff9800; color: white;" title="Khóa tài khoản">
                                        <i class="fa-solid fa-user-lock"></i>
                                    </button>
                                <?php else: ?>
                                    <button class="btn-action" style="background-color: #4caf50; color: white;" title="Mở khóa tài khoản">
                                        <i class="fa-solid fa-user-check"></i>
                                    </button>
                                <?php endif; ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8" style="text-align: center; color: #777; padding: 20px;">Không có tài khoản nào trong hệ thống.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>