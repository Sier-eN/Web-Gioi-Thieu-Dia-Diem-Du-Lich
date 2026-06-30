<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "Yêu cầu không hợp lệ.";
    exit;
}

// Kiểm tra bảo mật chặt chẽ xem có đúng là đã đăng nhập chưa
if (!isset($_SESSION['id']) && !isset($_SESSION['mataikhoan'])) {
    echo "Hết phiên làm việc, vui lòng đăng nhập lại.";
    exit;
}

// Tùy thuộc vào việc khi đăng nhập Nghĩa lưu khóa chính tài khoản vào $_SESSION['id'] hay $_SESSION['mataikhoan']
$maTaiKhoan = $_SESSION['id'] ?? $_SESSION['mataikhoan'] ?? '';

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

    // Chèn dữ liệu chính xác vào bảng DanhGia của Nghĩa
    $stmt = $conn->prepare("INSERT INTO DanhGia (MaDiaDiem, MaTaiKhoan, SoSao, NoiDung) VALUES (?, ?, ?, ?)");
    $stmt->execute([$maDiaDiem, $maTaiKhoan, $soSao, $noiDung]);

    echo "SUCCESS";
} catch (PDOException $e) {
    echo "Lỗi hệ thống database: " . $e->getMessage();
}