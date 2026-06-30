<?php
$host = 'localhost'; $dbname = 'DuLichDB'; $username = 'root'; $password = '';
try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Tính toán số lượng địa điểm thực tế của từng danh mục
    $sql = "SELECT dm.MaDanhMuc, dm.TenDanhMuc, dm.MoTa, COUNT(dd.MaDiaDiem) AS SoLuong
            FROM DanhMuc dm
            LEFT JOIN DiaDiem dd ON dm.MaDanhMuc = dd.MaDanhMuc
            GROUP BY dm.MaDanhMuc, dm.TenDanhMuc, dm.MoTa
            ORDER BY TenDanhMuc ASC";
    $stmt = $conn->query($sql);
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo 'Lỗi kết nối.';
    exit;
}
?>

<h2 class="section-title">Danh mục du lịch tâm điểm</h2>

<div class="places-grid">
    <?php foreach ($categories as $cat): ?>
        <div class="place-card" style="cursor: pointer; border: 1px solid #e2e8f0;" onclick="xemDiaDiemTheoDanhMuc(<?php echo $cat['MaDanhMuc']; ?>)">
            <div style="background: #1e3d59; height: 100px; display:flex; align-items:center; justify-content:center; color:white; font-size:32px;">
                <i class="fa-solid fa-folder-open"></i>
            </div>
            <div class="place-info">
                <h3 style="color: #17b978;"><?php echo htmlspecialchars($cat['TenDanhMuc']); ?></h3>
                <p style="font-size: 13px; color: #64748b; margin-top: 5px; min-height: 40px;">
                    <?php echo htmlspecialchars($cat['MoTa'] ?? 'Khám phá ngay các điểm đến hấp dẫn...'); ?>
                </p>
                <div style="margin-top: 15px; display: flex; justify-content: space-between; align-items: center;">
                    <span style="font-size:13px; font-weight:bold; color:#1e3d59;">
                        <i class="fa-solid fa-map-pin"></i> <?php echo $cat['SoLuong']; ?> địa điểm
                    </span>
                    <span style="font-size:12px; color:#17b978; font-weight:600;">Xem tất cả <i class="fa-solid fa-arrow-right"></i></span>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>