<?php
$host = 'localhost';
$dbname = 'DuLichDB';
$username = 'root';
$password = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // --- XỬ LÝ LỆNH XÓA ĐÁNH GIÁ ---
    if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
        $id = $_GET['id'];
        $stmtDel = $conn->prepare("DELETE FROM DanhGia WHERE MaDanhGia = ?");
        $stmtDel->execute([$id]);
        echo "SUCCESS";
        exit;
    }

    // --- TRUY VẤN LẤY DANH SÁCH ĐÁNH GIÁ (JOIN với DiaDiem và TaiKhoan) ---
    $sql = "SELECT dg.*, dd.TenDiaDiem, tk.HoTen, tk.TenDangNhap 
            FROM DanhGia dg
            INNER JOIN DiaDiem dd ON dg.MaDiaDiem = dd.MaDiaDiem
            INNER JOIN TaiKhoan tk ON dg.MaTaiKhoan = tk.MaTaiKhoan
            ORDER BY dg.NgayDanhGia DESC";
            
    $stmt = $conn->query($sql);
    $danhSachDanhGia = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo 'Lỗi: ' . $e->getMessage();
    exit;
}
?>

<div class="data-section">
    <div class="section-header">
        <h2><i class="fa-solid fa-star"></i> Hệ thống Quản lý Đánh giá / Bình luận</h2>
    </div>
    
    <table class="custom-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Người Đánh Giá</th>
                <th>Địa Điểm</th>
                <th>Số Sao</th>
                <th>Nội Dung Bình Luận</th>
                <th>Ngày Đăng</th>
                <th>Hành Động</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($danhSachDanhGia) > 0): ?>
                <?php foreach ($danhSachDanhGia as $dg): ?>
                    <tr>
                        <td>#<?php echo $dg['MaDanhGia']; ?></td>
                        <td>
                            <strong><?php echo htmlspecialchars($dg['HoTen']); ?></strong>
                            <br><small style="color: #777;">@<?php echo htmlspecialchars($dg['TenDangNhap']); ?></small>
                        </td>
                        <td><span class="badge" style="background-color: #e3f2fd; color: #0d47a1;"><?php echo htmlspecialchars($dg['TenDiaDiem']); ?></span></td>
                        <td>
                            <span style="color: #ff9800; font-weight: bold;">
                                <?php 
                                    for ($i = 1; $i <= 5; $i++) {
                                        if ($i <= $dg['SoSao']) {
                                            echo '★';
                                        } else {
                                            echo '☆';
                                        }
                                    }
                                ?>
                            </span>
                        </td>
                        <td>
                            <div style="max-width: 300px; word-wrap: break-word;">
                                <?php echo htmlspecialchars($dg['NoiDung']); ?>
                            </div>
                        </td>
                        <td><small style="color: #666;"><?php echo date('d/m/Y H:i', strtotime($dg['NgayDanhGia'])); ?></small></td>
                        <td>
                            <button class="btn-action btn-delete" onclick="xoaDanhGia(<?php echo $dg['MaDanhGia']; ?>)" title="Xóa bình luận này">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" style="text-align: center; color: #777; padding: 20px;">Chưa có lượt đánh giá hay bình luận nào từ người dùng.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>