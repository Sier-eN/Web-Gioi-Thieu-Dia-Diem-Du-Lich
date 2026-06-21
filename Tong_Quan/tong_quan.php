<?php
// Kết nối database để đếm dữ liệu theo thời gian thực
$host = 'localhost';
$dbname = 'DuLichDB';
$username = 'root';
$password = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmtDiaDiem = $conn->query("SELECT COUNT(*) AS tong_dia_diem FROM DiaDiem");
    $rowDiaDiem = $stmtDiaDiem->fetch(PDO::FETCH_ASSOC);
    $tongDiaDiem = $rowDiaDiem['tong_dia_diem'];

    $stmtLuotXem = $conn->query("SELECT SUM(LuotXem) AS tong_luot_xem FROM DiaDiem");
    $rowLuotXem = $stmtLuotXem->fetch(PDO::FETCH_ASSOC);
    $tongLuotXem = $rowLuotXem['tong_luot_xem'] ?? 0;

    $stmtDanhGia = $conn->query("SELECT AVG(SoSao) AS sao_trung_binh FROM DanhGia");
    $rowDanhGia = $stmtDanhGia->fetch(PDO::FETCH_ASSOC);
    $saoTrungBinh = $rowDanhGia['sao_trung_binh'] ? round($rowDanhGia['sao_trung_binh'], 1) : 0;
} catch (PDOException $e) {
    die("<p style='color:red;'>Lỗi kết nối: " . $e->getMessage() . "</p>");
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