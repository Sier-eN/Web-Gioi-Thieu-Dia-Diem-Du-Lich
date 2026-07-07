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
            <li class="nav-item active">
                <a href="javascript:void(0)" onclick="chuyenTabUser(this, 'home')">Trang chủ</a>
            </li>
            <li class="nav-item">
                <a href="javascript:void(0)" onclick="chuyenTabUser(this, 'places')">Địa điểm</a>
            </li>
            <li class="nav-item">
                <a href="javascript:void(0)" onclick="chuyenTabUser(this, 'categories')">Danh mục</a>
            </li>
            
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
                
                $stmt = $conn->query("SELECT dd.*, dm.TenDanhMuc FROM DiaDiem dd INNER JOIN DanhMuc dm ON dd.MaDanhMuc = dm.MaDanhMuc LIMIT 6");
                
                while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $tenFileAnh = trim($row['HinhAnhChinh'] ?? '');
                    $duongDanAnhThat = '../images/' . $tenFileAnh;

                    if (!empty($tenFileAnh) && file_exists($duongDanAnhThat) && is_file($duongDanAnhThat)) {
                        $imgUrl = $duongDanAnhThat;
                    } else {
                        $imgUrl = '../images/default.jpg';
                    }

                    echo '<div class="place-card">
                            <img src="'.$imgUrl.'" class="place-img" alt="'.htmlspecialchars($row['TenDiaDiem']).'">
                            <div class="place-info">
                                <h3>'.htmlspecialchars($row['TenDiaDiem']).'</h3>
                                <span style="font-size:12px; padding:3px 8px; background:#e3f2fd; color:#0d47a1; border-radius:12px; font-weight:600;">'.htmlspecialchars($row['TenDanhMuc']).'</span>
                                <div class="place-meta" style="margin-bottom: 12px;">
                                    <span><i class="fa-solid fa-location-dot" style="color:#ff4d4d;"></i> '.htmlspecialchars($row['VungMien']).'</span>
                                    <span><i class="fa-regular fa-eye"></i> '.number_format($row['LuotXem']).'</span>
                                </div>
                                
                                <div style="border-top: 1px solid #f1f5f9; padding-top: 10px; text-align: right;">
                                    <a href="javascript:void(0)" onclick="xemChiTietDiaDiem('.$row['MaDiaDiem'].')" style="color: #17b978; font-weight: 600; font-size: 14px; text-decoration: none; display: inline-block;">
                                        Xem chi tiết <i class="fa-solid fa-arrow-right" style="font-size: 12px; margin-left: 4px;"></i>
                                    </a>
                                </div>
                            </div>
                          </div>';
                }
            } catch (PDOException $e) { 
                echo '<div style="grid-column: 1/-1; text-align:center; padding:20px; color:#666;">Chưa có dữ liệu kết nối hoặc database trống.</div>'; 
            }
            ?>
        </div>

    </main>

    <script src="../java_script/user_script.js"></script>
    
    <script>
    function loadAuthPage(type) {
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

    // ĐÃ THAY THẾ: Hàm tường minh bọc stopPropagation để ngăn file js ngoài tự bắt hành vi click này
    function chuyenTabUser(element, page) {
        if (window.event) {
            window.event.stopPropagation();
            window.event.preventDefault();
        }

        // Đổi màu trạng thái Active cho thanh menu
        document.querySelectorAll('.nav-links li').forEach(li => li.classList.remove('active'));
        element.parentElement.classList.add('active');
        
        var contentZone = document.getElementById('user-content-body');
        var heroBanner = document.getElementById('hero-banner');

        if(page === 'home') {
            location.reload();
        } 
        else if(page === 'places') {
            heroBanner.style.display = 'flex';
            contentZone.innerHTML = '<div style="padding:40px; text-align:center; color:#777;"><i class="fa-solid fa-spinner fa-spin"></i> Đang tải danh sách địa điểm...</div>';
            
            // Ép buộc tải bản mới nhất bằng cách đính kèm nhãn thời gian thực Date.now()
            fetch('user_places.php?v=' + Date.now())
                .then(res => res.text())
                .then(html => { contentZone.innerHTML = html; })
                .catch(err => { contentZone.innerHTML = '<p style="color:red; text-align:center;">Lỗi: ' + err.message + '</p>'; });
        } 
        else if(page === 'categories') {
            heroBanner.style.display = 'flex';
            contentZone.innerHTML = '<div style="padding:40px; text-align:center; color:#777;"><i class="fa-solid fa-spinner fa-spin"></i> Đang tải danh mục...</div>';
            
            fetch('user_categories.php?v=' + Date.now())
                .then(res => res.text())
                .then(html => { contentZone.innerHTML = html; })
                .catch(err => { contentZone.innerHTML = '<p style="color:red; text-align:center;">Lỗi: ' + err.message + '</p>'; });
        }
    }

    // Hàm xử lý gọi lọc động toàn bộ địa điểm khi click vào Card danh mục
    function xemDiaDiemTheoDanhMuc(maDanhMuc) {
        var contentZone = document.getElementById('user-content-body');
        contentZone.innerHTML = '<div style="padding:40px; text-align:center; color:#777;"><i class="fa-solid fa-spinner fa-spin"></i> Đang lọc địa điểm thuộc danh mục...</div>';
        
        fetch('user_places.php?madanhmuc=' + maDanhMuc + '&v=' + Date.now())
            .then(res => res.text())
            .then(html => {
                contentZone.innerHTML = html;
                window.scrollTo({ top: contentZone.offsetTop - 100, behavior: 'smooth' });
            })
            .catch(err => {
                contentZone.innerHTML = '<p style="color:red; text-align:center;">Lỗi kết nối: ' + err.message + '</p>';
            });
    }

    // Hàm nạp trang xem chi tiết địa điểm bằng AJAX
    function xemChiTietDiaDiem(id) {
        var contentZone = document.getElementById('user-content-body');
        contentZone.innerHTML = '<div style="padding:40px; text-align:center; color:#777;"><i class="fa-solid fa-spinner fa-spin"></i> Đang tải nội dung chi tiết...</div>';
        
        fetch('user_place_detail.php?id=' + id + '&v=' + Date.now())
            .then(res => res.text())
            .then(html => {
                contentZone.innerHTML = html;
                window.scrollTo({ top: contentZone.offsetTop - 100, behavior: 'smooth' });
            })
            .catch(err => {
                contentZone.innerHTML = '<p style="color:red; text-align:center;">Lỗi: ' + err.message + '</p>';
            });
    }

    // Hàm để bấm nút Quay lại từ trang chi tiết về danh sách địa điểm ban đầu
    function quayLaiDanhSachDiaDiem() {
        var contentZone = document.getElementById('user-content-body');
        contentZone.innerHTML = '<div style="padding:40px; text-align:center; color:#777;"><i class="fa-solid fa-spinner fa-spin"></i> Đang tải lại danh sách...</div>';
        
        fetch('user_places.php?v=' + Date.now())
            .then(res => res.text())
            .then(html => {
                contentZone.innerHTML = html;
            });
    }

    // Hàm AJAX gửi đánh giá và sao trực tiếp
    function guiDanhGiaMoi(event, maDiaDiem) {
        event.preventDefault();
        
        var soSao = document.getElementById('dg_sao').value;
        var noiDung = document.getElementById('dg_content').value;
        var msgZone = document.getElementById('review_msg');
        
        msgZone.style.color = '#777';
        msgZone.innerHTML = 'Hệ thống đang lưu đánh giá...';

        var formData = new FormData();
        formData.append('maDiaDiem', maDiaDiem);
        formData.append('soSao', soSao);
        formData.append('noiDung', noiDung);

        fetch('xu_ly_danh_gia.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.text())
        .then(data => {
            if(data.trim() === 'SUCCESS') {
                msgZone.style.color = '#17b978';
                msgZone.innerHTML = '✓ Đăng đánh giá chấm điểm thành công!';
                document.getElementById('dg_content').value = '';
                
                // Tải lại phân vùng đánh giá thời gian thực
                fetch('user_place_detail.php?id=' + maDiaDiem + '&v=' + Date.now())
                    .then(r => r.text())
                    .then(html => {
                        var parser = new DOMParser();
                        var doc = parser.parseFromString(html, 'text/html');
                        document.getElementById('reviews_list_zone').innerHTML = doc.getElementById('reviews_list_zone').innerHTML;
                    });
            } else {
                msgZone.style.color = 'red';
                msgZone.innerHTML = 'Lỗi: ' + data;
            }
        })
        .catch(err => {
            msgZone.style.color = 'red';
            msgZone.innerHTML = 'Lỗi kết nối: ' + err.message;
        });
    }
    </script>
</body>
</html>