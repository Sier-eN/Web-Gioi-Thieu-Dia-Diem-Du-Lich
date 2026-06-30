<?php
$host = 'localhost'; $dbname = 'DuLichDB'; $username = 'root'; $password = '';
try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Kiểm tra xem người dùng có đang lọc theo Danh mục nào không
    $maDanhMuc = $_GET['madanhmuc'] ?? '';

    if (!empty($maDanhMuc)) {
        // Truy vấn lấy địa điểm theo danh mục được chọn
        $stmt = $conn->prepare("SELECT dd.*, dm.TenDanhMuc FROM DiaDiem dd 
                                INNER JOIN DanhMuc dm ON dd.MaDanhMuc = dm.MaDanhMuc 
                                WHERE dd.MaDanhMuc = ? ORDER BY dd.NgayDang DESC");
        $stmt->execute([$maDanhMuc]);
        
        // Lấy tên danh mục để làm tiêu đề bài viết
        $stmtTitle = $conn->prepare("SELECT TenDanhMuc FROM DanhMuc WHERE MaDanhMuc = ?");
        $stmtTitle->execute([$maDanhMuc]);
        $tenDanhMucHienTai = $stmtTitle->fetchColumn();
        $tieuDe = "Địa điểm thuộc danh mục: " . htmlspecialchars($tenDanhMucHienTai);
    } else {
        // Nếu không lọc, lấy toàn bộ địa điểm
        $stmt = $conn->query("SELECT dd.*, dm.TenDanhMuc FROM DiaDiem dd 
                              INNER JOIN DanhMuc dm ON dd.MaDanhMuc = dm.MaDanhMuc 
                              ORDER BY dd.NgayDang DESC");
        $tieuDe = "Tất cả địa điểm du lịch";
    }
    
    $danhSach = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo 'Lỗi kết nối dữ liệu.';
    exit;
}
?>

<h2 class="section-title"><?php echo $tieuDe; ?></h2>

<?php if (count($danhSach) > 0): ?>
    <div class="places-grid">
        <?php foreach ($danhSach as $row): ?>
            <?php 
                $imgUrl = !empty($row['HinhAnhChinh']) ? '../images/'.$row['HinhAnhChinh'] : '../images/default.jpg';
            ?>
            <div class="place-card">
                <img src="<?php echo $imgUrl; ?>" class="place-img" alt="Ảnh">
                <div class="place-info">
                    <h3><?php echo htmlspecialchars($row['TenDiaDiem']); ?></h3>
                    <span style="font-size:12px; padding:3px 8px; background:#e3f2fd; color:#0d47a1; border-radius:12px; font-weight:600;">
                        <?php echo htmlspecialchars($row['TenDanhMuc']); ?>
                    </span>
                    <p style="font-size:14px; color:#64748b; margin-top:8px; line-height:1.4;">
                        <?php echo htmlspecialchars($row['MoTaNgan'] ?? ''); ?>
                    </p>
                    <div class="place-meta" style="margin-bottom: 12px;">
                        <span><i class="fa-solid fa-location-dot" style="color:#ff4d4d;"></i> <?php echo htmlspecialchars($row['VungMien']); ?></span>
                        <span><i class="fa-regular fa-eye"></i> <?php echo number_format($row['LuotXem']); ?></span>
                    </div>

                    <div style="border-top: 1px solid #f1f5f9; padding-top: 10px; text-align: right;">
                        <a href="javascript:void(0)" onclick="xemChiTietDiaDiem(<?php echo $row['MaDiaDiem']; ?>)" style="color: #17b978; font-weight: 600; font-size: 14px; text-decoration: none; display: inline-block;">
                            Xem chi tiết <i class="fa-solid fa-arrow-right" style="font-size: 12px; margin-left: 4px;"></i>
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <p style="text-align:center; color:#777; padding:40px;">Chưa có địa điểm nào thuộc danh mục này.</p>
<?php endif; ?>