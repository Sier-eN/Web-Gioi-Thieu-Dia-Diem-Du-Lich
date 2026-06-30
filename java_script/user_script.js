// Hàm xử lý Đăng nhập phía Client
function handleUserLogin(e) {
  e.preventDefault();

  var formData = new FormData(document.getElementById("formUserLogin"));
  var data = new URLSearchParams(formData);
  var errorDiv = document.getElementById("login-error");

  // SỬA TẠI ĐÂY: Dùng đường dẫn tuyệt đối tính từ htdocs để tránh lỗi 404
  fetch("/GTDDDL-clone/Trang_Chu/xu_ly_auth.php?action=login", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: data,
  })
    .then((res) => res.text())
    .then((response) => {
      var res = response.trim();
      if (res === "ADMIN") {
        alert(
          "Đăng nhập quyền Admin thành công! Chuyển hướng sang trang quản trị.",
        );
        window.location.href = "trang_chu_quan_ly.php";
      } else if (res === "USER") {
        alert("Đăng nhập thành công! Chúc bạn có chuyến khám phá thú vị.");
        window.location.reload();
      } else {
        errorDiv.style.display = "block";
        errorDiv.innerText = res;
      }
    })
    .catch((err) => console.error("Lỗi hệ thống:", err));
}

// Hàm xử lý Đăng ký phía Client
function handleUserRegister(e) {
  e.preventDefault();

  var email = document.getElementById("reg_email").value;
  var matkhau = document.getElementById("reg_pass").value;
  var errorDiv = document.getElementById("register-error");

  // Kiểm tra Email chuẩn quốc tế
  var emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
  if (!emailRegex.test(email)) {
    alert("Email không đúng định dạng chuẩn quốc tế!");
    document.getElementById("reg_email").focus();
    return;
  }

  // Kiểm tra độ dài mật khẩu (8-22 ký tự)
  if (matkhau.length < 8 || matkhau.length > 22) {
    alert("Mật khẩu bắt buộc phải từ 8 đến 22 ký tự!");
    document.getElementById("reg_pass").focus();
    return;
  }

  var formData = new FormData(document.getElementById("formUserRegister"));
  var data = new URLSearchParams(formData);

  // SỬA TẠI ĐÂY: Dùng đường dẫn tuyệt đối tính từ htdocs để tránh lỗi 404
  fetch("/GTDDDL-clone/Trang_Chu/xu_ly_auth.php?action=register", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: data,
  })
    .then((res) => res.text())
    .then((response) => {
      if (response.trim() === "SUCCESS") {
        alert("Đăng ký tài khoản thành công! Hãy đăng nhập để tiếp tục.");
        loadAuthPage("login"); // Hàm loadAuthPage nằm ở trang_chu_web.php
      } else {
        errorDiv.style.display = "block";
        errorDiv.innerText = response;
      }
    })
    .catch((err) => console.error("Lỗi hệ thống:", err));
}

// Xử lý hiệu ứng active và tải phân hệ điều hướng menu chính (ĐÃ SỬA)
document.querySelectorAll('.nav-links a').forEach(link => {
    link.addEventListener('click', function() {
        let page = this.getAttribute('data-page');
        
        // Nếu click vào nút "Quản lý" (đi sang trang khác) thì không chặn mặc định
        if (!page) return; 

        document.querySelectorAll('.nav-links li').forEach(li => li.classList.remove('active'));
        this.parentElement.classList.add('active');
        
        var contentZone = document.getElementById('user-content-body');
        var heroBanner = document.getElementById('hero-banner');

        if(page === 'home') {
            // Trở về trang chủ nguyên bản bằng cách reload
            location.reload();
        } else if(page === 'places') {
            // HIỂN THỊ LẠI HERO BANNER NẾU MUỐN
            heroBanner.style.display = 'flex';
            contentZone.innerHTML = '<div style="padding:40px; text-align:center; color:#777;"><i class="fa-solid fa-spinner fa-spin"></i> Đang tải danh sách địa điểm...</div>';
            
            // Gọi AJAX lấy trực tiếp file quản lý địa điểm sang (hoặc file riêng của user nếu có)
            fetch('/GTDDDL-clone/QL_Dia_Diem/quan_ly_dia_diem.php')
                .then(res => res.text())
                .then(html => {
                    contentZone.innerHTML = html;
                })
                .catch(err => {
                    contentZone.innerHTML = '<p style="color:red; text-align:center; padding:20px;">Lỗi: ' + err.message + '</p>';
                });
        } else if(page === 'categories') {
            heroBanner.style.display = 'flex';
            contentZone.innerHTML = '<div style="padding:40px; text-align:center; color:#777;"><i class="fa-solid fa-spinner fa-spin"></i> Đang tải danh mục du lịch...</div>';
            
            // Gọi AJAX lấy file quản lý danh mục sang
            fetch('/GTDDDL-clone/QL_Danh_Muc/quan_ly_danh_muc.php')
                .then(res => res.text())
                .then(html => {
                    contentZone.innerHTML = html;
                })
                .catch(err => {
                    contentZone.innerHTML = '<p style="color:red; text-align:center; padding:20px;">Lỗi: ' + err.message + '</p>';
                });
        }
    });
});