<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$host = 'localhost'; $dbname = 'DuLichDB'; $username = 'root'; $password = '';
try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $id = $_GET['id'] ?? '';
    if (empty($id)) {
        echo '<p style="text-align:center; padding:20px;">Không tìm thấy địa điểm.</p>';
        exit;
    }

    // 1. TĂNG LƯỢT XEM: Thực hiện cộng 1 lượt xem ngay khi nạp trang chi tiết
    $stmtUpdate = $conn->prepare("UPDATE DiaDiem SET LuotXem = LuotXem + 1 WHERE MaDiaDiem = ?");
    $stmtUpdate->execute([$id]);

    // 2. LẤY THÔNG TIN ĐỊA ĐIỂM
    $stmt = $conn->prepare("SELECT dd.*, dm.TenDanhMuc FROM DiaDiem dd 
                            INNER JOIN DanhMuc dm ON dd.MaDanhMuc = dm.MaDanhMuc 
                            WHERE dd.MaDiaDiem = ?");
    $stmt->execute([$id]);
    $place = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$place) {
        echo '<p style="text-align:center; padding:20px;">Địa điểm không tồn tại.</p>';
        exit;
    }

    // 3. LẤY DANH SÁCH ĐÁNH GIÁ (INNER JOIN với TaiKhoan để lấy HoTen người đánh giá)
    $stmtDG = $conn->prepare("SELECT dg.*, tk.HoTen FROM DanhGia dg 
                              INNER JOIN TaiKhoan tk ON dg.MaTaiKhoan = tk.MaTaiKhoan 
                              WHERE dg.MaDiaDiem = ? ORDER BY dg.NgayDanhGia DESC");
    $stmtDG->execute([$id]);
    $reviews = $stmtDG->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo '<p style="text-align:center; color:red; padding:20px;">Lỗi kết nối dữ liệu.</p>';
    exit;
}

$imgUrl = !empty($place['HinhAnhChinh']) ? '../images/'.$place['HinhAnhChinh'] : '../images/default.jpg';
?>

<div class="place-detail-wrapper" style="background: #ffffff; padding: 30px; border-radius: 12px; box-shadow: 0 4px 25px rgba(0,0,0,0.06); max-width: 1000px; margin: 0 auto;">
    
    <button onclick="quayLaiDanhSachDiaDiem()" style="background: #1e3d59; color: white; border: none; padding: 10px 20px; border-radius: 6px; cursor: pointer; margin-bottom: 25px; font-weight: 600; font-size: 14px;">
        <i class="fa-solid fa-arrow-left"></i> Quay lại danh sách
    </button>

    <h1 style="color: #1e3d59; font-size: 32px; font-weight: 700; margin-bottom: 15px;"><?php echo htmlspecialchars($place['TenDiaDiem']); ?></h1>
    
    <div style="display: flex; gap: 20px; margin-bottom: 25px; font-size: 14px; color: #64748b; border-bottom: 1px solid #f1f5f9; padding-bottom: 15px; flex-wrap: wrap;">
        <span><i class="fa-solid fa-layer-group" style="color: #17b978;"></i> Danh mục: <b><?php echo htmlspecialchars($place['TenDanhMuc']); ?></b></span>
        <span><i class="fa-solid fa-map-location-dot" style="color: #ff4d4d;"></i> Vùng miền: <b><?php echo htmlspecialchars($place['VungMien']); ?></b></span>
        <span><i class="fa-solid fa-location-dot" style="color: #4facfe;"></i> Địa chỉ: <b><?php echo htmlspecialchars($place['DiaChi']); ?></b></span>
        <span><i class="fa-regular fa-eye"></i> Lượt xem: <b><?php echo number_format($place['LuotXem']); ?></b></span>
    </div>

    <img src="<?php echo $imgUrl; ?>" alt="Ảnh" style="width: 100%; max-height: 500px; object-fit: cover; border-radius: 10px; margin-bottom: 30px;">

    <div style="margin-bottom: 40px;">
        <h3 style="color: #1e3d59; font-size: 20px; border-left: 4px solid #17b978; padding-left: 12px; margin-bottom: 15px; font-weight: 700;">Thông tin giới thiệu chi tiết</h3>
        <div style="line-height: 1.9; color: #334155; font-size: 16px; text-align: justify; white-space: pre-line;">
            <?php echo htmlspecialchars($place['ChiTiet'] ?? $place['MoTaNgan'] ?? 'Hiện chưa có nội dung bài viết chi tiết.'); ?>
        </div>
    </div>

    <div style="border-top: 2px solid #f1f5f9; padding-top: 35px; margin-top: 40px;">
        <h3 style="color: #1e3d59; font-size: 22px; margin-bottom: 25px; font-weight: 700;">
            <i class="fa-regular fa-star" style="color: #ffb703;"></i> Đánh giá từ cộng đồng (<?php echo count($reviews); ?>)
        </h3>

        <?php if (isset($_SESSION['username'])): ?>
            <div style="background: #f8fafc; padding: 20px; border-radius: 8px; border: 1px solid #e2e8f0; margin-bottom: 30px;">
                <h4 style="margin-top:0; color:#334155; margin-bottom: 12px;">Để lại đánh giá của bạn (Tài khoản: <b><?php echo htmlspecialchars($_SESSION['fullname']); ?></b>):</h4>
                <form id="reviewForm" onsubmit="guiDanhGiaMoi(event, <?php echo $place['MaDiaDiem']; ?>)">
                    
                    <div style="margin-bottom: 12px;">
                        <label for="dg_sao" style="font-size: 14px; font-weight: 600; color: #475569;">Chọn mức độ hài lòng: </label>
                        <select id="dg_sao" style="padding: 8px; border-radius: 6px; border: 1px solid #cbd5e1; outline: none; font-weight: 600; color: #ffb703;">
                            <option value="5">⭐⭐⭐⭐⭐ 5 Sao (Tuyệt vời)</option>
                            <option value="4">⭐⭐⭐⭐ 4 Sao (Tốt)</option>
                            <option value="3">⭐⭐⭐ 3 Sao (Bình thường)</option>
                            <option value="2">⭐⭐ 2 Sao (Tạm được)</option>
                            <option value="1">⭐ 1 Sao (Kém)</option>
                        </select>
                    </div>

                    <div style="margin-bottom: 12px;">
                        <textarea id="dg_content" rows="4" placeholder="Chia sẻ trải nghiệm thực tế của bạn về địa điểm này..." required
                                  style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 14px; font-family: inherit; resize: vertical; outline: none;"></textarea>
                    </div>
                    <button type="submit" style="background: #17b978; color: white; border: none; padding: 10px 22px; font-weight: 600; border-radius: 6px; cursor: pointer; font-size: 14px;">
                        Gửi đánh giá <i class="fa-regular fa-paper-plane" style="margin-left: 5px;"></i>
                    </button>
                </form>
                <div id="review_msg" style="margin-top: 10px; font-size: 14px; font-weight: 500;"></div>
            </div>
        <?php else: ?>
            <div style="background: #fff3cd; color: #856404; padding: 15px 20px; border-radius: 8px; border: 1px solid #ffeeba; margin-bottom: 30px; font-weight: 500;">
                <i class="fa-solid fa-circle-info"></i> Bạn cần <a href="javascript:void(0)" onclick="loadAuthPage('login')" style="color: #0056b3; font-weight: 700; text-decoration: underline;">Đăng nhập</a> tài khoản để gửi đánh giá và chấm điểm cho địa điểm này.
            </div>
        <?php endif; ?>

        <div id="reviews_list_zone">
            <?php if (count($reviews) > 0): ?>
                <?php foreach ($reviews as $dg): ?>
                    <div style="border-bottom: 1px solid #f1f5f9; padding: 15px 5px; margin-bottom: 10px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                            <div>
                                <strong style="color: #1e3d59; font-size: 15px;"><i class="fa-regular fa-user-circle"></i> <?php echo htmlspecialchars($dg['HoTen']); ?></strong>
                                <span style="margin-left: 10px; color: #ffb703; font-weight: bold;">
                                    <?php echo str_repeat('⭐', $dg['SoSao']); ?>
                                </span>
                            </div>
                            <small style="color: #94a3b8; font-size: 12px;"><?php echo date('d/m/Y H:i', strtotime($dg['NgayDanhGia'])); ?></small>
                        </div>
                        <p style="margin: 0; color: #475569; font-size: 14.5px; line-height: 1.5; padding-left: 20px;">
                            <?php echo nl2br(htmlspecialchars($dg['NoiDung'])); ?>
                        </p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="text-align: center; color: #94a3b8; padding: 20px; font-style: italic;">Chưa có lượt chấm điểm đánh giá nào. Hãy đăng nhập và trở thành người đầu tiên!</p>
            <?php endif; ?>
        </div>
    </div>
</div>