<?php
$host = 'localhost';
$dbname = 'DuLichDB';
$username = 'root';
$password = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $id = $_POST['id'] ?? '';
    $tendanhmuc = $_POST['tendanhmuc'] ?? '';
    $mota = $_POST['mota'] ?? '';

    if (empty($tendanhmuc)) {
        echo "Tên danh mục không được để trống!";
        exit;
    }

    if (empty($id)) {
        // --- THÊM DANH MỤC MỚI ---
        $sql = "INSERT INTO DanhMuc (TenDanhMuc, MoTa) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$tendanhmuc, $mota]);
        echo "SUCCESS";
    } else {
        // --- CẬP NHẬT DANH MỤC ---
        $sql = "UPDATE DanhMuc SET TenDanhMuc = ?, MoTa = ? WHERE MaDanhMuc = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$tendanhmuc, $mota, $id]);
        echo "SUCCESS";
    }

} catch (PDOException $e) {
    echo "Lỗi hệ thống database: " . $e->getMessage();
}
?>