document.addEventListener("DOMContentLoaded", function () {
  // Lấy tất cả các thẻ liên kết trong Menu Sidebar
  const menuItems = document.querySelectorAll(".sidebar-menu ul li a");

  menuItems.forEach((item) => {
    item.addEventListener("click", function (e) {
      // Nếu link là chuyển hướng trang thật (ví dụ: sang file index.php của thư mục khác)
      // thì để trình duyệt tự chuyển hướng. Ở đây ta thêm logic đổi class active để test trực quan:

      // Xóa class 'active' khỏi tất cả các hàng <li> trước đó
      document.querySelectorAll(".sidebar-menu ul li").forEach((li) => {
        li.classList.remove("active");
      });

      // Thêm class 'active' vào thẻ <li> cha của thẻ <a> vừa click
      this.parentElement.classList.add("active");

      // Log kiểm tra mục tiêu click
      const targetSection = this.getAttribute("data-target");
      console.log("Đang chuyển hướng hoặc tải phân hệ: " + targetSection);
    });
  });
});
