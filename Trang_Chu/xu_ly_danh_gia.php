<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "Yêu cầu không hợp lệ.";
    exit;
}

// ĐỒNG BỘ CHÍNH XÁC: Lấy đúng biến 'user_id' mà file xu_ly_auth.php vừa tạo ra khi đăng nhập thành công
$maTaiKhoan = $_SESSION['user_id'] ?? '';

// Kiểm tra bảo mật lớp Server
if (empty($maTaiKhoan)) {
    echo "Hết phiên làm việc, vui lòng đăng nhập lại.";
    exit;
}

$host = 'localhost'; $dbname = 'DuLichDB'; $username = 'root'; $password = '';
try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $maDiaDiem = $_POST['maDiaDiem'] ?? '';
    $soSao = intval($_POST['soSao'] ?? 5);
    $noiDung = trim($_POST['noiDung'] ?? '');

    if (empty($maDiaDiem) || empty($noiDung) || $soSao < 1 || $soSao > 5) {
        echo "Vui lòng nhập đầy đủ nội dung cảm nhận.";
        exit;
    }

    // Chèn dữ liệu chính xác vào bảng DanhGia với MaTaiKhoan hợp lệ
    $stmt = $conn->prepare("INSERT INTO DanhGia (MaDiaDiem, MaTaiKhoan, SoSao, NoiDung) VALUES (?, ?, ?, ?)");
    $stmt->execute([$maDiaDiem, $maTaiKhoan, $soSao, $noiDung]);

    echo "SUCCESS";
} catch (PDOException $e) {
    echo "Lỗi hệ thống database: " . $e->getMessage();
}
?>