<?php
$host = 'localhost';
$dbname = 'DuLichDB';
$username = 'root';
$password = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $id = $_POST['id'] ?? '';
    $tendiadiem = $_POST['tendiadiem'] ?? '';
    $madanhmuc = $_POST['madanhmuc'] ?? '';
    $vungmien = $_POST['vungmien'] ?? '';
    $diachi = $_POST['diachi'] ?? '';
    $motangan = $_POST['motangan'] ?? '';
    $chitiet = $_POST['chitiet'] ?? '';
    
    // Mặc định gán bằng tên ảnh cũ được gửi từ input hidden lên
    $tenFileAnh = $_POST['hinhanhchinh_cu'] ?? '';

    if (empty($tendiadiem) || empty($madanhmuc)) {
        echo "Tên địa điểm và danh mục không được để trống!";
        exit;
    }

    // --- LOGIC XỬ LÝ UPLOAD FILE ẢNH QUA MẢNG $_FILES ---
    if (isset($_FILES['hinhanh_file']) && $_FILES['hinhanh_file']['error'] == 0) {
        $allowedExts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $originName = $_FILES['hinhanh_file']['name'];
        $ext = strtolower(pathinfo($originName, PATHINFO_EXTENSION));

        if (in_array($ext, $allowedExts)) {
            // Đổi tên ảnh ngẫu nhiên theo mốc thời gian time() chống trùng file trên server
            $tenFileAnh = 'img_' . time() . '.' . $ext;
            $targetDir = "../images/";
            
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0777, true);
            }

            // Di chuyển file từ vùng nhớ tạm của XAMPP vào thư mục images
            move_uploaded_file($_FILES['hinhanh_file']['tmp_name'], $targetDir . $tenFileAnh);
        }
    }

    // --- ĐIỀU PHỐI HÀNH ĐỘNG SQL THÊM / SỬA ---
    if (empty($id)) {
        // --- HÀNH ĐỘNG: THÊM ĐỊA ĐIỂM MỚI ---
        
        // Nếu người dùng không upload file ảnh nào, tự động chọn ảnh mặc định
        if (empty($tenFileAnh)) {
            $tenFileAnh = 'default.jpg';
        }
        
        $sql = "INSERT INTO DiaDiem (TenDiaDiem, MaDanhMuc, VungMien, DiaChi, MoTaNgan, ChiTiet, HinhAnhChinh, LuotXem) 
                VALUES (?, ?, ?, ?, ?, ?, ?, 0)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$tendiadiem, $madanhmuc, $vungmien, $diachi, $motangan, $chitiet, $tenFileAnh]);
        echo "SUCCESS";
    } else {
        // --- HÀNH ĐỘNG: CẬP NHẬT ĐỊA ĐIỂM (SỬA) ---
        // Nếu không chọn ảnh mới, hệ thống tự dùng lại giá trị $tenFileAnh (ảnh cũ)
        $sql = "UPDATE DiaDiem SET TenDiaDiem = ?, MaDanhMuc = ?, VungMien = ?, DiaChi = ?, MoTaNgan = ?, ChiTiet = ?, HinhAnhChinh = ? 
                WHERE MaDiaDiem = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$tendiadiem, $madanhmuc, $vungmien, $diachi, $motangan, $chitiet, $tenFileAnh, $id]);
        echo "SUCCESS";
    }

} catch (PDOException $e) {
    echo "Lỗi hệ thống database: " . $e->getMessage();
}
?>