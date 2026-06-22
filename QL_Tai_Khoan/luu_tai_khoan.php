<?php
$host = 'localhost';
$dbname = 'DuLichDB';
$username = 'root';
$password = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $id = $_POST['id'] ?? '';
    $hoten = $_POST['hoten'] ?? '';
    $tendangnhap = $_POST['tendangnhap'] ?? '';
    $email = $_POST['email'] ?? '';
    $vaitro = $_POST['vaitro'] ?? 'User';

    // --- KHOÁ 1: KIỂM TRA EMAIL Ở SERVER ---
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Email không đúng định dạng hợp lệ!";
        exit;
    }

    if (empty($id)) {
        // --- HÀNH ĐỘNG: THÊM MỚI ---
        $matkhau = $_POST['matkhau'] ?? '';
        
        // --- KHÓA 2: KIỂM TRA ĐỘ DÀI MẬT KHẨU Ở SERVER ---
        $length = mb_strlen($matkhau);
        if ($length < 8 || $length > 22) {
            echo "Mật khẩu bắt buộc phải từ 8 đến 22 ký tự!";
            exit;
        }
        
        // Kiểm tra trùng tên đăng nhập
        $check = $conn->prepare("SELECT COUNT(*) FROM TaiKhoan WHERE TenDangNhap = ?");
        $check->execute([$tendangnhap]);
        if ($check->fetchColumn() > 0) {
            echo "Tên đăng nhập đã tồn tại trên hệ thống!";
            exit;
        }

        // Chèn vào database
        $sql = "INSERT INTO TaiKhoan (TenDangNhap, MatKhau, HoTen, Email, VaiTro, TrangThai) VALUES (?, ?, ?, ?, ?, 'Hoạt động')";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$tendangnhap, $matkhau, $hoten, $email, $vaitro]);
        echo "SUCCESS";
        
    } else {
        // --- HÀNH ĐỘNG: CẬP NHẬT (SỬA) ---
        $sql = "UPDATE TaiKhoan SET HoTen = ?, Email = ?, VaiTro = ? WHERE MaTaiKhoan = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$hoten, $email, $vaitro, $id]);
        echo "SUCCESS";
    }

} catch (PDOException $e) {
    echo "Lỗi hệ thống database: " . $e->getMessage();
}
?>