document.addEventListener("DOMContentLoaded", function () {
    // Lấy tất cả các mục menu có chứa thuộc tính data-url
    const menuLinks = document.querySelectorAll(".sidebar-menu ul li a");
    const contentBody = document.getElementById("main-content-body");

    menuLinks.forEach((link) => {
        link.addEventListener("click", function (e) {
            e.preventDefault();

            // 1. Lấy đường dẫn file cần tải dữ liệu
            const url = this.getAttribute("data-url");
            if (!url) return;

            // 2. Đổi hiệu ứng "active" cho thanh menu
            document.querySelectorAll(".sidebar-menu ul li").forEach((li) => {
                li.classList.remove("active");
            });
            this.parentElement.classList.add("active");

            // Hiển thị hiệu ứng chờ load (Loading...) trong lúc tải trang
            contentBody.innerHTML = '<div style="padding: 20px; font-weight: bold; color: #1e3d59;"><i class="fa-solid fa-spinner fa-spin"></i> Đang tải dữ liệu...</div>';

            // 3. Dùng Fetch API (AJAX nâng cao) để gọi file PHP ngầm
            fetch(url)
                .then((response) => {
                    if (!response.ok) {
                        throw new Error("Không thể tải được trang này.");
                    }
                    return response.text(); // Chuyển kết quả nhận được thành văn bản HTML
                })
                .then((htmlNoidung) => {
                    // Thay thế toàn bộ nội dung bên phải bằng HTML vừa tải về
                    contentBody.innerHTML = htmlNoidung;
                })
                .catch((error) => {
                    contentBody.innerHTML = '<div style="padding: 20px; color: red; font-weight: bold;"><i class="fa-solid fa-triangle-exclamation"></i> Lỗi: ' + error.message + '</div>';
                });
        });
    });
});