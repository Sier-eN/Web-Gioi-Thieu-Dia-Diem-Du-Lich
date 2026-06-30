<?php
// Khởi động phiên làm việc nếu file này được gọi ngầm bằng AJAX từ admin_script.js
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Kết nối database để đếm dữ liệu theo thời gian thực
$host = 'localhost';
$dbname = 'DuLichDB';
$username = 'root';
$password = '';

// Khởi tạo biến mặc định để tránh vỡ layout nếu database gặp sự cố kết nối ngầm
$tongDiaDiem = 0;
$tongLuotXem = 0;
$saoTrungBinh = 0;

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 1. Lấy tổng số địa điểm
    $stmtDiaDiem = $conn->query("SELECT COUNT(*) AS tong_dia_diem FROM DiaDiem");
    $rowDiaDiem = $stmtDiaDiem->fetch(PDO::FETCH_ASSOC);
    $tongDiaDiem = $rowDiaDiem['tong_dia_diem'] ?? 0;

    // 2. Lấy tổng số lượt xem
    $stmtLuotXem = $conn->query("SELECT SUM(LuotXem) AS tong_luot_xem FROM DiaDiem");
    $rowLuotXem = $stmtLuotXem->fetch(PDO::FETCH_ASSOC);
    $tongLuotXem = $rowLuotXem['tong_luot_xem'] ?? 0;

    // 3. Lấy số sao đánh giá trung bình
    $stmtDanhGia = $conn->query("SELECT AVG(SoSao) AS sao_trung_binh FROM DanhGia");
    $rowDanhGia = $stmtDanhGia->fetch(PDO::FETCH_ASSOC);
    $saoTrungBinh = $rowDanhGia['sao_trung_binh'] ? round($rowDanhGia['sao_trung_binh'], 1) : 0;

} catch (PDOException $e) {
    // Không dùng die() để tránh làm sập trắng trang admin, chỉ ghi nhận lỗi vào log của hệ thống
    error_log("Lỗi thống kê tổng quan: " . $e->getMessage());
}
?>

<div class="stats-grid">
    <div class="stat-card card-blue">
        <div class="stat-info">
            <h3><?php echo number_format($tongDiaDiem); ?></h3>
            <p>Địa điểm du lịch</p>
        </div>
        <div class="stat-icon"><i class="fa-solid fa-hotel"></i></div>
    </div>

    <div class="stat-card card-green">
        <div class="stat-info">
            <h3><?php echo number_format($tongLuotXem); ?></h3>
            <p>Tổng lượt xem địa điểm</p>
        </div>
        <div class="stat-icon"><i class="fa-solid fa-plane-departure"></i></div>
    </div>

    <div class="stat-card card-orange">
        <div class="stat-info">
            <h3><?php echo $saoTrungBinh; ?> ★</h3>
            <p>Đánh giá trung bình</p>
        </div>
        <div class="stat-icon"><i class="fa-solid fa-heart"></i></div>
    </div>
</div>