// ĐỒNG BỘ: Chạy chính xác theo thư mục gốc gốc GTDDDL và tệp tin quan_ly_danh_gia.php
var dgUrl = "/GTDDDL/QL_Danh_Gia/quan_ly_danh_gia.php";

function xoaDanhGia(id) {
  if (
    confirm(
      "Bạn có chắc chắn muốn xóa đánh giá/bình luận này không? Hành động này sẽ gỡ bỏ hoàn toàn phản hồi của user khỏi địa điểm du lịch.",
    )
  ) {
    // 1. Gửi lệnh xóa kèm nhãn v= để ép trình duyệt xóa bỏ cache
    fetch(dgUrl + "?action=delete&id=" + id + "&v=" + Date.now())
      .then((res) => res.text())
      .then((data) => {
        if (data.trim() === "SUCCESS") {
          // 2. Tải lại bảng quản trị đánh giá, loại bỏ cache triệt để
          fetch(dgUrl + "?v=" + Date.now())
            .then((r) => r.text())
            .then((html) => {
              // Đắp dữ liệu mới vào đúng vùng chứa của bảng quản trị Admin chính
              var contentZone =
                document.getElementById("main-content-body") ||
                document.getElementById("user-content-body");
              if (contentZone) {
                contentZone.innerHTML = html;
              } else {
                location.reload(); // Phương án dự phòng nếu không tìm thấy ID phân vùng
              }
            });
        } else {
          // Lọc bỏ hiển thị bách khoa toàn thư mã HTML lỗi 404/500 nếu dính cấu hình sai
          if (data.includes("<!DOCTYPE") || data.includes("<html")) {
            alert(
              "Hệ thống phản hồi sai định dạng (Lỗi đường dẫn hoặc Database). Vui lòng kiểm tra lại!",
            );
          } else {
            alert("Không thể xóa: " + data);
          }
        }
      })
      .catch((error) => {
        alert("Lỗi kết nối hệ thống: " + error.message);
      });
  }
}
