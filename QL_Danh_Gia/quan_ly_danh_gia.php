<div class="data-section">
    <div class="section-header">
        <h2><i class="fa-solid fa-star"></i> Hệ thống Quản lý Đánh giá / Bình luận</h2>
    </div>
    
    <table class="custom-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Người Đánh Giá</th>
                <th>Địa Điểm</th>
                <th>Số Sao</th>
                <th>Nội Dung Bình Luận</th>
                <th>Ngày Đăng</th>
                <th>Hành Động</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($danhSachDanhGia) > 0): ?>
                <?php foreach ($danhSachDanhGia as $dg): ?>
                    <tr>
                        <td>#<?= $dg['MaDanhGia']; ?></td>
                        <td>
                            <strong><?= htmlspecialchars($dg['HoTen']); ?></strong><br>
                            <small>@<?= htmlspecialchars($dg['TenDangNhap']); ?></small>
                        </td>
                        <td><?= htmlspecialchars($dg['TenDiaDiem']); ?></td>
                        <td>
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <?= $i <= $dg['SoSao'] ? '★' : '☆' ?>
                            <?php endfor; ?>
                        </td>
                        <td><?= htmlspecialchars($dg['NoiDung']); ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($dg['NgayDanhGia'])); ?></td>
                        <td>
                            <button onclick="xoaDanhGia(<?= $dg['MaDanhGia']; ?>)">Xóa</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7">Chưa có dữ liệu</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>