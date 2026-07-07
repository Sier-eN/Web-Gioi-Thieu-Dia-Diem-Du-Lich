<?php
$host = 'localhost'; $dbname = 'DuLichDB'; $username = 'root'; $password = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // --- XỬ LÝ LỆNH XÓA ĐÁNH GIÁ ---
    if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
        $id = intval($_GET['id']);
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

<link rel="stylesheet" href="/GTDDDL/css/admin_style.css?v=<?php echo time(); ?>">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<div class="data-section" style="padding: 20px; background: #fff; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">
    <div class="section-header" style="margin-bottom: 20px; border-bottom: 2px solid #f1f5f9; padding-bottom: 10px;">
        <h2 style="color: #1e3d59; font-size: 22px; margin: 0;"><i class="fa-solid fa-star" style="color: #ff9800;"></i> Hệ thống Quản lý Đánh giá / Bình luận</h2>
    </div>
    
    <table class="custom-table" style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="background: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                <th style="padding: 12px; text-align: left;">ID</th>
                <th style="padding: 12px; text-align: left;">Người Đánh Giá</th>
                <th style="padding: 12px; text-align: left;">Địa Điểm</th>
                <th style="padding: 12px; text-align: left;">Số Sao</th>
                <th style="padding: 12px; text-align: left;">Nội Dung Bình Luận</th>
                <th style="padding: 12px; text-align: left;">Ngày Đăng</th>
                <th style="padding: 12px; text-align: center;">Hành Động</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($danhSachDanhGia) > 0): ?>
                <?php foreach ($danhSachDanhGia as $dg): ?>
                    <tr style="border-bottom: 1px solid #f1f5f9;">
                        <td style="padding: 12px;">#<?php echo $dg['MaDanhGia']; ?></td>
                        <td style="padding: 12px;">
                            <strong style="color: #334155;"><?php echo htmlspecialchars($dg['HoTen']); ?></strong>
                            <br><small style="color: #64748b;">@<?php echo htmlspecialchars($dg['TenDangNhap']); ?></small>
                        </td>
                        <td style="padding: 12px;"><span class="badge" style="background-color: #e3f2fd; color: #0d47a1; padding: 4px 8px; border-radius: 4px; font-size: 13px; font-weight: 500;"><?php echo htmlspecialchars($dg['TenDiaDiem']); ?></span></td>
                        <td style="padding: 12px;">
                            <span style="color: #ff9800; font-weight: bold; letter-spacing: 2px;">
                                <?php 
                                    for ($i = 1; $i <= 5; $i++) {
                                        echo ($i <= $dg['SoSao']) ? '★' : '☆';
                                    }
                                ?>
                            </span>
                        </td>
                        <td style="padding: 12px;">
                            <div style="max-width: 300px; word-wrap: break-word; color: #475569; font-size: 14px; line-height: 1.5;">
                                <?php echo htmlspecialchars($dg['NoiDung']); ?>
                            </div>
                        </td>
                        <td style="padding: 12px;"><small style="color: #64748b;"><?php echo date('d/m/Y H:i', strtotime($dg['NgayDanhGia'])); ?></small></td>
                        <td style="padding: 12px; text-align: center;">
                            <button class="btn-action btn-delete" onclick="xoaDanhGia(<?php echo $dg['MaDanhGia']; ?>)" title="Xóa bình luận này"
                                    style="background: #ef4444; color: white; border: none; padding: 6px 10px; border-radius: 4px; cursor: pointer; transition: 0.2s;">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" style="text-align: center; color: #64748b; padding: 30px; font-style: italic;">Chưa có lượt đánh giá hay bình luận nào từ người dùng.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    <script>
function xoaDanhGia(id) {
    if (confirm("Bạn có chắc chắn muốn xóa đánh giá/bình luận này không?")) {
        // Sử dụng đường dẫn tương đối trực tiếp vì script này chạy ngay tại thư mục QL_Danh_Gia
        var urlXuly = "quan_ly_danh_gia.php?action=delete&id=" + id + "&v=" + Date.now();
        
        fetch(urlXuly)
            .then((res) => res.text())
            .then((data) => {
                if (data.trim() === "SUCCESS") {
                    // Tải lại phân vùng bảng quản lý đánh giá
                    fetch("quan_ly_danh_gia.php?v=" + Date.now())
                        .then((r) => r.text())
                        .then((html) => {
                            var contentZone = document.getElementById("main-content-body") || document.getElementById("user-content-body");
                            if (contentZone) {
                                contentZone.innerHTML = html;
                            } else {
                                location.reload();
                            }
                        });
                } else {
                    alert("Không thể xóa: " + data);
                }
            })
            .catch((error) => {
                alert("Lỗi kết nối hệ thống: " + error.message);
            });
    }
}
</script>
</div>