<?php
// Khởi động session để lưu lại trạng thái đăng nhập của người dùng toàn hệ thống
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$host = 'localhost'; 
$dbname = 'DuLichDB'; 
$username = 'root'; 
$password = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $action = $_GET['action'] ?? '';

    // ==========================================
    // 1. XỬ LÝ CHỨC NĂNG ĐĂNG NHẬP (LOGIN)
    // ==========================================
    if ($action == 'login') {
        $user = trim($_POST['username'] ?? '');
        $pass = trim($_POST['password'] ?? '');

        if (empty($user) || empty($pass)) {
            echo "Vui lòng nhập đầy đủ tên đăng nhập và mật khẩu!";
            exit;
        }

        // Truy vấn tìm tài khoản theo tên đăng nhập
        $stmt = $conn->prepare("SELECT * FROM TaiKhoan WHERE TenDangNhap = ?");
        $stmt->execute([$user]);
        $account = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$account) {
            echo "Tài khoản không tồn tại trên hệ thống!";
            exit;
        }

        // Kiểm tra xem tài khoản có đang bị Admin khóa hay không
        if ($account['TrangThai'] == 'Bị khóa') {
            echo "Tài khoản của bạn đã bị khóa! Vui lòng liên hệ Admin để được hỗ trợ.";
            exit;
        }

        // Kiểm tra mật khẩu (Do đồ án lưu mật khẩu thuần nên so sánh trực tiếp chuỗi)
        if ($pass === $account['MatKhau']) {
            // Đăng nhập thành công -> Lưu dữ liệu vào biến toàn cục Session
            $_SESSION['user_id'] = $account['MaTaiKhoan'];
            $_SESSION['username'] = $account['TenDangNhap'];
            $_SESSION['fullname'] = $account['HoTen'];
            $_SESSION['role'] = $account['VaiTro'];

            // Trả về tín hiệu phân quyền để JavaScript (user_script.js) điều hướng trang
            if (strcasecmp($account['VaiTro'], 'Admin') == 0) {
                echo "ADMIN";
            } else {
                echo "USER";
            }
        } else {
            echo "Mật khẩu nhập vào không chính xác!";
        }
        exit;
    }

    // ==========================================
    // 2. XỬ LÝ CHỨC NĂNG ĐĂNG KÝ (REGISTER)
    // ==========================================
    if ($action == 'register') {
        $hoten = trim($_POST['hoten'] ?? '');
        $user = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $pass = trim($_POST['password'] ?? '');

        // Kiểm tra dữ liệu rỗng đầu vào
        if (empty($hoten) || empty($user) || empty($email) || empty($pass)) {
            echo "Vui lòng điền đầy đủ tất cả thông tin đăng ký!";
            exit;
        }

        // Xác thực định dạng email chuẩn quốc tế ở lớp Server
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo "Email không đúng định dạng chuẩn quốc tế!";
            exit;
        }

        // Xác thực độ dài mật khẩu ở lớp Server
        $length = mb_strlen($pass);
        if ($length < 8 || $length > 22) {
            echo "Mật khẩu bắt buộc phải từ 8 đến 22 ký tự!";
            exit;
        }

        // Kiểm tra xem tên đăng nhập này đã có người khác đăng ký chưa
        $checkUser = $conn->prepare("SELECT COUNT(*) FROM TaiKhoan WHERE TenDangNhap = ?");
        $checkUser->execute([$user]);
        if ($checkUser->fetchColumn() > 0) {
            echo "Tên đăng nhập này đã tồn tại, vui lòng chọn tên khác!";
            exit;
        }

        // Tiến hành thêm tài khoản du khách mới vào cơ sở dữ liệu (mặc định vai trò là User)
        $sql = "INSERT INTO TaiKhoan (TenDangNhap, MatKhau, HoTen, Email, VaiTro, TrangThai) VALUES (?, ?, ?, ?, 'User', 'Hoạt động')";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$user, $pass, $hoten, $email]);
        
        echo "SUCCESS";
        exit;
    }

} catch (PDOException $e) {
    echo "Lỗi kết nối hệ thống cơ sở dữ liệu: " . $e->getMessage();
}
?>