<?php
// 1. KHỞI ĐỘNG SESSION ĐỂ KIỂM TRA TRẠNG THÁI ĐĂNG NHẬP
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// BẢO MẬT: Nếu chưa đăng nhập hoặc vai trò không phải Admin thì chặn lại, đuổi thẳng về trang chủ
if (!isset($_SESSION['role']) || strcasecmp($_SESSION['role'], 'Admin') !== 0) {
    header("Location: trang_chu_web.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hệ thống Quản lý Địa điểm Du lịch</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/admin_style.css">
</head>
<body>

    <div class="dashboard-container">
        
        <aside class="sidebar">
            <div class="sidebar-logo">
                <i class="fa-solid fa-compass compass-icon"></i>
                <h2>TravelAdmin</h2>
            </div>
            
            <nav class="sidebar-menu">
                <ul>
                    <li class="menu-item active">
                        <a href="javascript:void(0)" data-url="../Tong_Quan/tong_quan.php">
                            <i class="fa-solid fa-chart-pie"></i> Tổng quan
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="javascript:void(0)" data-url="../QL_Dia_Diem/quan_ly_dia_diem.php">
                            <i class="fa-solid fa-map-location-dot"></i> Quản lý Địa điểm
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="javascript:void(0)" data-url="../QL_Danh_Muc/quan_ly_danh_muc.php">
                            <i class="fa-solid fa-tags"></i> Quản lý Danh mục
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="javascript:void(0)" data-url="../QL_Danh_Gia/quan_ly_danh_gia.php">
                            <i class="fa-solid fa-star"></i> Quản lý Đánh giá
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="javascript:void(0)" data-url="../QL_Tai_Khoan/quan_ly_tai_khoan.php">
                            <i class="fa-solid fa-users"></i> Quản lý Tài khoản
                        </a>
                    </li>
                </ul>
            </nav>

            <div class="sidebar-footer">
                <a href="trang_chu_web.php"><i class="fa-solid fa-earth-asia"></i> Xem Website</a>
            </div>
        </aside>

        <main class="main-content">
            <header class="main-header">
                <div class="greeting">
                    <h1>Xin chào, Quản trị viên!</h1>
                    <p>Hôm nay là một ngày tuyệt vời để khám phá những điểm đến mới.</p>
                </div>
                <div class="user-profile">
                    <img src="https://picsum.photos/id/1025/40/40" alt="Avatar" class="avatar">
                    <span><?php echo htmlspecialchars($_SESSION['fullname'] ?? 'Admin'); ?></span>
                </div>
            </header>

            <div class="content-body" id="main-content-body">
                <?php 
                    // Mặc định load nội dung tổng quan lên khi mới vào trang
                    include '../Tong_Quan/tong_quan.php'; 
                ?>
            </div>
        </main>

    </div>

    <script src="../java_script/admin_script.js?v=<?php echo time(); ?>"></script>
</body>
</html>