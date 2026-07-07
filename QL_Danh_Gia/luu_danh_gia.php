// ==========================================
// 5. CHỨC NĂNG QUẢN LÝ ĐÁNH GIÁ (AJAX)
// ==========================================
var dgUrl = "../QL_Danh_Gia/index.php";

function xoaDanhGia(id) {
    if (confirm("Bạn có chắc chắn muốn xóa đánh giá/bình luận này không? Hành động này sẽ gỡ bỏ hoàn toàn phản hồi của user khỏi địa điểm du lịch.")) {
        fetch(dgUrl + "?action=delete&id=" + id)
            .then((res) => res.text())
            .then((data) => {
                if (data.trim() === "SUCCESS") {
                    // Tải lại ruột bảng đánh giá bằng AJAX
                    fetch(dgUrl)
                        .then((r) => r.text())
                        .then((html) => {
                            document.getElementById("main-content-body").innerHTML = html;
                        });
                } else {
                    alert("Không thể xóa: " + data);
                }
            })
            .catch((error) => {
                alert("Lỗi kết nối hệ thống: " + error.message);
            });
    }
}