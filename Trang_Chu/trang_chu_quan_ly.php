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
                    <li class="active">
                        <a href="#" data-target="tong-quan">
                            <i class="fa-solid fa-chart-pie"></i> Tổng quan
                        </a>
                    </li>
                    <li>
                        <a href="../QL_Dia_Diem/index.php" data-target="ql-dia-diem">
                            <i class="fa-solid fa-map-location-dot"></i> Quản lý Địa điểm
                        </a>
                    </li>
                    <li>
                        <a href="../QL_Danh_Muc/index.php" data-target="ql-danh-muc">
                            <i class="fa-solid fa-tags"></i> Quản lý Danh mục
                        </a>
                    </li>
                    <li>
                        <a href="../QL_Danh_Gia/index.php" data-target="ql-danh-gia">
                            <i class="fa-solid fa-star"></i> Quản lý Đánh giá
                        </a>
                    </li>
                    <li>
                        <a href="../QL_Tai_Khoan/index.php" data-target="ql-tai-khoan">
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
                    <img src="https://picsum.photos/40" alt="Avatar" class="avatar">
                    <span>Nghĩa Admin</span>
                </div>
            </header>

            <div class="content-body">
                
                <div class="stats-grid">
                    <div class="stat-card card-blue">
                        <div class="stat-info">
                            <h3>142</h3>
                            <p>Địa điểm du lịch</p>
                        </div>
                        <div class="stat-icon">
                            <i class="fa-solid fa-hotel"></i>
                        </div>
                    </div>

                    <div class="stat-card card-green">
                        <div class="stat-info">
                            <h3>12,450</h3>
                            <p>Lượt check-in / Tìm kiếm</p>
                        </div>
                        <div class="stat-icon">
                            <i class="fa-solid fa-plane-departure"></i>
                        </div>
                    </div>

                    <div class="stat-card card-orange">
                        <div class="stat-info">
                            <h3>4.8 ★</h3>
                            <p>Đánh giá trung bình</p>
                        </div>
                        <div class="stat-icon">
                            <i class="fa-solid fa-heart"></i>
                        </div>
                    </div>
                </div>

                <div class="data-section">
                    <div class="section-header">
                        <h2><i class="fa-solid fa-umbrella-beach"></i> Địa điểm nổi bật vừa cập nhật</h2>
                        <button class="btn-add"><i class="fa-solid fa-plus"></i> Thêm địa điểm</button>
                    </div>
                    
                    <table class="custom-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Hình ảnh</th>
                                <th>Tên địa điểm</th>
                                <th>Vùng miền</th>
                                <th>Trạng thái</th>
                                <th>Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>#01</td>
                                <td><img src="https://picsum.photos/id/1015/60/40" alt="Vịnh Hạ Long" class="table-img"></td>
                                <td><strong>Vịnh Hạ Long</strong></td>
                                <td>Quảng Ninh</td>
                                <td><span class="badge badge-active">Đang hiển thị</span></td>
                                <td>
                                    <button class="btn-action btn-edit"><i class="fa-solid fa-pen"></i></button>
                                    <button class="btn-action btn-delete"><i class="fa-solid fa-trash"></i></button>
                                </td>
                            </tr>
                            <tr>
                                <td>#02</td>
                                <td><img src="https://picsum.photos/id/1016/60/40" alt="Phú Quốc" class="table-img"></td>
                                <td><strong>Đảo Ngọc Phú Quốc</strong></td>
                                <td>Kiên Giang</td>
                                <td><span class="badge badge-active">Đang hiển thị</span></td>
                                <td>
                                    <button class="btn-action btn-edit"><i class="fa-solid fa-pen"></i></button>
                                    <button class="btn-action btn-delete"><i class="fa-solid fa-trash"></i></button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

            </div>
        </main>

    </div>

    <script src="../java_script/admin_script.js"></script>
</body>
</html>