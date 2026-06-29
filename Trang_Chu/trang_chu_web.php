<?php
// Khởi động phiên làm việc để kiểm tra trạng thái đăng nhập người dùng
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Khám Phá Việt Nam - Cẩm Nang Du Lịch Trực Tuyến</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/user_style.css">
</head>
<body>

    <nav class="navbar">
        <div class="logo">
            <i class="fa-solid fa-earth-asia"></i>
            <span>TravelViệt</span>
        </div>
        
        <ul class="nav-links">
            <li class="nav-item active"><a href="javascript:void(0)" data-page="home">Trang chủ</a></li>
            <li class="nav-item"><a href="javascript:void(0)" data-page="places">Địa điểm</a></li>
            <li class="nav-item"><a href="javascript:void(0)" data-page="categories">Danh mục</a></li>
            
            <?php if (isset($_SESSION['role']) && strcasecmp($_SESSION['role'], 'Admin') == 0): ?>
                <li class="nav-item">
                    <a href="trang_chu_quan_ly.php" style="color: #17b978; font-weight: bold;">
                        <i class="fa-solid fa-user-shield"></i> Quản lý
                    </a>
                </li>
            <?php endif; ?>
        </ul>

        <div class="nav-auth" id="nav-auth-zone">
            <?php if (isset($_SESSION['username'])): ?>
                <span style="margin-right: 15px; font-weight: 600; color: #1e3d59;">
                    <i class="fa-regular fa-user"></i> Xin chào, <?php echo htmlspecialchars($_SESSION['fullname']); ?>
                </span>
                <a href="dang_xuat.php" class="btn-auth btn-register" style="background: #ff4d4d;">Đăng xuất</a>
            <?php else: ?>
                <button class="btn-auth btn-login" onclick="loadAuthPage('login')">Đăng nhập</button>
                <button class="btn-auth btn-register" onclick="loadAuthPage('register')">Đăng ký</button>
            <?php endif; ?>
        </div>
    </nav>

    <div class="hero" id="hero-banner">
        <h1>Khám Phá Những Vùng Đất Mới</h1>
        <p>Lên kế hoạch và tìm kiếm những địa điểm dừng chân tuyệt vời nhất dành cho bạn.</p>
    </div>

    <main class="container" id="user-content-body">
        
        <h2 class="section-title">Điểm đến nổi bật dành cho bạn</h2>
        <div class="places-grid">
            <?php
            $host = 'localhost'; $dbname = 'DuLichDB'; $username = 'root'; $password = '';
            try {
                $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                
                // Truy vấn lấy dữ liệu địa điểm hiển thị trực quan kèm ảnh thật từ picsum để test giao diện du lịch
                $stmt = $conn->query("SELECT dd.*, dm.TenDanhMuc FROM DiaDiem dd INNER JOIN DanhMuc dm ON dd.MaDanhMuc = dm.MaDanhMuc LIMIT 6");
                $index = 1011; // ID bắt đầu để lấy ảnh ngẫu nhiên từ picsum
                
                while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $imgUrl = !empty($row['HinhAnhChinh']) ? '../images/'.$row['HinhAnhChinh'] : 'https://picsum.photos/id/'.$index.'/300/180';
                    echo '<div class="place-card">
                            <img src="'.$imgUrl.'" class="place-img" alt="'.htmlspecialchars($row['TenDiaDiem']).'">
                            <div class="place-info">
                                <h3>'.htmlspecialchars($row['TenDiaDiem']).'</h3>
                                <span style="font-size:12px; padding:3px 8px; background:#e3f2fd; color:#0d47a1; border-radius:12px;">'.htmlspecialchars($row['TenDanhMuc']).'</span>
                                <div class="place-meta">
                                    <span><i class="fa-solid fa-location-dot" style="color:#ff4d4d;"></i> '.htmlspecialchars($row['VungMien']).'</span>
                                    <span><i class="fa-regular fa-eye"></i> '.number_format($row['LuotXem']).'</span>
                                </div>
                            </div>
                          </div>';
                    $index++;
                }
            } catch (PDOException $e) { 
                echo '<div style="grid-column: 1/-1; text-align:center; padding:20px; color:#666;">Chưa có dữ liệu kết nối hoặc database trống.</div>'; 
            }
            ?>
        </div>

    </main>

    <script src="../java_script/user_script.js"></script>
    
    <script>
    // Hàm xử lý gọi Form Đăng nhập / Đăng ký từ file ngoài qua AJAX
    function loadAuthPage(type) {
        // Khóa ẩn Banner Hero ngay lập tức để tránh form bị đẩy vỡ layout xuống dưới
        document.getElementById('hero-banner').style.display = 'none';
        
        let url = type === 'login' ? 'login.php' : 'register.php';
        
        fetch(url)
            .then(res => {
                if(!res.ok) throw new Error("Không thể nạp được biểu mẫu.");
                return res.text();
            })
            .then(html => {
                document.getElementById('user-content-body').innerHTML = html;
            })
            .catch(err => {
                document.getElementById('user-content-body').innerHTML = '<p style="color:red; text-align:center;">Lỗi: ' + err.message + '</p>';
            });
    }

    // Xử lý hiệu ứng active và tải phân hệ điều hướng menu chính
    document.querySelectorAll('.nav-links a').forEach(link => {
        link.addEventListener('click', function() {
            document.querySelectorAll('.nav-links li').forEach(li => li.classList.remove('active'));
            this.parentElement.classList.add('active');
            
            let page = this.getAttribute('data-page');
            if(page === 'home') {
                // Trở về trang chủ nguyên bản bằng cách làm mới
                location.reload();
            } else {
                // Hiển thị lại Banner Hero khi rời khỏi biểu mẫu Auth
                document.getElementById('hero-banner').style.display = 'flex';
                document.getElementById('user-content-body').innerHTML = '<div style="padding:40px; text-align:center; color:#777;"><i class="fa-solid fa-spinner fa-spin"></i> Đang tải phân hệ dữ liệu...</div>';
            }
        });
    });
    </script>
</body>
</html>