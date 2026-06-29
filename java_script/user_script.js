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
