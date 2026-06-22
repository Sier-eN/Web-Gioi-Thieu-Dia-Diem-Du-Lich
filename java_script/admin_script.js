document.addEventListener("DOMContentLoaded", function () {
  // ==========================================
  // 1. XỬ LÝ CHUYỂN ĐỔI PHÂN HỆ QUA AJAX
  // ==========================================
  const menuLinks = document.querySelectorAll(".sidebar-menu ul li a");
  const contentBody = document.getElementById("main-content-body");

  menuLinks.forEach((link) => {
    link.addEventListener("click", function (e) {
      e.preventDefault();

      const url = this.getAttribute("data-url");
      if (!url) return;

      document.querySelectorAll(".sidebar-menu ul li").forEach((li) => {
        li.classList.remove("active");
      });
      this.parentElement.classList.add("active");

      contentBody.innerHTML =
        '<div style="padding: 20px; font-weight: bold; color: #1e3d59;"><i class="fa-solid fa-spinner fa-spin"></i> Đang tải dữ liệu...</div>';

      fetch(url)
        .then((response) => {
          if (!response.ok) throw new Error("Không thể tải được trang này.");
          return response.text();
        })
        .then((htmlNoidung) => {
          contentBody.innerHTML = htmlNoidung;
        })
        .catch((error) => {
          contentBody.innerHTML =
            '<div style="padding: 20px; color: red; font-weight: bold;"><i class="fa-solid fa-triangle-exclamation"></i> Lỗi: ' +
            error.message +
            "</div>";
        });
    });
  });
});

// ==========================================
// 2. CHỨC NĂNG QUẢN LÝ TÀI KHOẢN (MODAL & AJAX)
// ==========================================
var currentUrl = "../QL_Tai_Khoan/quan_ly_tai_khoan.php";

function openAddModal() {
  document.getElementById("modalTitle").innerHTML =
    '<i class="fa-solid fa-user-plus"></i> Thêm Tài Khoản Mới';
  document.getElementById("formTaiKhoan").reset();
  document.getElementById("modal_id").value = "";
  document.getElementById("modal_tendangnhap").disabled = false;
  document.getElementById("password-group").style.display = "block";
  document.getElementById("modal_matkhau").required = true;
  document.getElementById("taiKhoanModal").style.display = "block";
}

function openEditModal(id, hoten, tendangnhap, email, vaitro) {
  document.getElementById("modalTitle").innerHTML =
    '<i class="fa-solid fa-user-pen"></i> Cập Nhật Tài Khoản';
  document.getElementById("modal_id").value = id;
  document.getElementById("modal_hoten").value = hoten;
  document.getElementById("modal_tendangnhap").value = tendangnhap;
  document.getElementById("modal_tendangnhap").disabled = true; // Khóa trường đăng nhập
  document.getElementById("modal_email").value = email;
  document.getElementById("modal_vaitro").value = vaitro;
  document.getElementById("password-group").style.display = "none"; // Ẩn mật khẩu khi sửa
  document.getElementById("modal_matkhau").required = false;
  document.getElementById("taiKhoanModal").style.display = "block";
}

function closeModal() {
  document.getElementById("taiKhoanModal").style.display = "none";
}

function saveTaiKhoan(e) {
  e.preventDefault();

  var id = document.getElementById("modal_id").value;
  var matkhau = document.getElementById("modal_matkhau").value;
  var email = document.getElementById("modal_email").value;

  // --- KIỂM TRA EMAIL CHUẨN QUỐC TẾ ---
  var emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
  if (!emailRegex.test(email)) {
    alert("Email không đúng định dạng chuẩn quốc tế! (Ví dụ: abc@domain.com)");
    document.getElementById("modal_email").focus();
    return;
  }

  // --- KIỂM TRA ĐỘ DÀI MẬT KHẨU (Chỉ khi thêm mới) ---
  if (id === "") {
    if (matkhau.length < 8 || matkhau.length > 22) {
      alert("Mật khẩu phải có độ dài từ 8 đến 22 ký tự!");
      document.getElementById("modal_matkhau").focus();
      return;
    }
  }

  var formData = new FormData(document.getElementById("formTaiKhoan"));
  document.getElementById("modal_tendangnhap").disabled = false; // Mở tạm thời để gửi data
  var data = new URLSearchParams(formData);

  fetch("../QL_Tai_Khoan/luu_tai_khoan.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: data,
  })
    .then((res) => res.text())
    .then((response) => {
      if (response.trim() === "SUCCESS") {
        closeModal();
        document.getElementById("main-content-body").innerHTML =
          '<div style="padding: 20px;"><i class="fa-solid fa-spinner fa-spin"></i> Đang cập nhật dữ liệu...</div>';
        fetch(currentUrl)
          .then((r) => r.text())
          .then((html) => {
            document.getElementById("main-content-body").innerHTML = html;
          });
      } else {
        alert("Có lỗi xảy ra: " + response);
        if (id !== "") {
          document.getElementById("modal_tendangnhap").disabled = true;
        }
      }
    });
}

function khoaTaiKhoan(id) {
  if (confirm("Bạn có chắc chắn muốn khóa tài khoản này không?")) {
    fetch(currentUrl + "?action=delete&id=" + id)
      .then((res) => res.text())
      .then((data) => {
        if (data.trim() === "SUCCESS") {
          fetch(currentUrl)
            .then((r) => r.text())
            .then(
              (html) =>
                (document.getElementById("main-content-body").innerHTML = html),
            );
        }
      });
  }
}

function moKhoaTaiKhoan(id) {
  fetch(currentUrl + "?action=activate&id=" + id)
    .then((res) => res.text())
    .then((data) => {
      if (data.trim() === "SUCCESS") {
        fetch(currentUrl)
          .then((r) => r.text())
          .then(
            (html) =>
              (document.getElementById("main-content-body").innerHTML = html),
          );
      }
    });
}

// CHỨC NĂNG XÓA VĨNH VIỄN (MỚI THÊM)
function xoaHanTaiKhoan(id) {
  if (
    confirm(
      "CẢNH BÁO: Bạn có chắc chắn muốn XÓA VĨNH VIỄN tài khoản này không? Hành động này không thể hoàn tác!",
    )
  ) {
    fetch(currentUrl + "?action=destroy&id=" + id)
      .then((res) => res.text())
      .then((data) => {
        if (data.trim() === "SUCCESS") {
          fetch(currentUrl)
            .then((r) => r.text())
            .then(
              (html) =>
                (document.getElementById("main-content-body").innerHTML = html),
            );
        } else {
          alert("Không thể xóa: " + data);
        }
      });
  }
}

// ==========================================
// 3. CHỨC NĂNG QUẢN LÝ DANH MỤC (MODAL & AJAX)
// ==========================================
var dmUrl = "../QL_Danh_Muc/quan_ly_danh_muc.php";

function openDanhMucModal() {
    document.getElementById("dmModalTitle").innerHTML = '<i class="fa-solid fa-folder-plus"></i> Thêm Danh Mục Mới';
    document.getElementById("formDanhMuc").reset();
    document.getElementById("dm_id").value = "";
    document.getElementById("danhMucModal").style.display = "block";
}

function openEditDanhMucModal(id, ten, mota) {
    document.getElementById("dmModalTitle").innerHTML = '<i class="fa-solid fa-folder-open"></i> Cập Nhật Danh Mục';
    document.getElementById("dm_id").value = id;
    document.getElementById("dm_ten").value = ten;
    document.getElementById("dm_mota").value = mota;
    document.getElementById("danhMucModal").style.display = "block";
}

function closeDanhMucModal() {
    document.getElementById("danhMucModal").style.display = "none";
}

function saveDanhMuc(e) {
    e.preventDefault();
    var formData = new FormData(document.getElementById("formDanhMuc"));
    var data = new URLSearchParams(formData);

    fetch('../QL_Danh_Muc/luu_danh_muc.php', {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: data,
    })
    .then((res) => res.text())
    .then((response) => {
        if (response.trim() === "SUCCESS") {
            closeDanhMucModal();
            // Làm mới vùng content-body sang danh mục mới cập nhật
            document.getElementById("main-content-body").innerHTML = '<div style="padding: 20px;"><i class="fa-solid fa-spinner fa-spin"></i> Đang tải lại danh mục...</div>';
            fetch(dmUrl).then((r) => r.text()).then((html) => {
                document.getElementById("main-content-body").innerHTML = html;
            });
        } else {
            alert("Có lỗi xảy ra: " + response);
        }
    });
}

function xoaDanhMuc(id) {
    if (confirm("LƯU Ý: Xóa danh mục này sẽ đồng thời xóa TẤT CẢ các địa điểm du lịch thuộc danh mục này! Bạn có chắc chắn muốn xóa?")) {
        fetch(dmUrl + "?action=delete&id=" + id)
            .then((res) => res.text())
            .then((data) => {
                if (data.trim() === "SUCCESS") {
                    fetch(dmUrl).then((r) => r.text()).then((html) => (document.getElementById("main-content-body").innerHTML = html));
                } else {
                    alert("Không thể xóa: " + data);
                }
            });
    }
}


// ==========================================
// 4. CHỨC NĂNG QUẢN LÝ ĐỊA ĐIỂM (MODAL & AJAX)
// ==========================================
var ddUrl = "../QL_Dia_Diem/quan_ly_dia_diem.php";

function openDiaDiemModal() {
    document.getElementById("ddModalTitle").innerHTML = '<i class="fa-solid fa-map-location-dot"></i> Thêm Địa Điểm Du Lịch Mới';
    document.getElementById("formDiaDiem").reset();
    document.getElementById("dd_id").value = "";
    document.getElementById("diaDiemModal").style.display = "block";
}

function openEditDiaDiemModal(id, ten, madanhmuc, vungmien, diachi, motangan, chitiet, hinhanh) {
    document.getElementById("ddModalTitle").innerHTML = '<i class="fa-solid fa-route"></i> Cập Nhật Địa Điểm Du Lịch';
    document.getElementById("dd_id").value = id;
    document.getElementById("dd_ten").value = ten;
    document.getElementById("dd_danhmuc").value = madanhmuc;
    document.getElementById("dd_vungmien").value = vungmien;
    document.getElementById("dd_diachi").value = diachi;
    document.getElementById("dd_hinhanh").value = hinhanh;
    document.getElementById("dd_motangan").value = motangan;
    document.getElementById("dd_chitiet").value = chitiet;
    document.getElementById("diaDiemModal").style.display = "block";
}

function closeDiaDiemModal() {
    document.getElementById("diaDiemModal").style.display = "none";
}

function saveDiaDiem(e) {
    e.preventDefault();
    var formData = new FormData(document.getElementById("formDiaDiem"));
    var data = new URLSearchParams(formData);

    fetch('../QL_Dia_Diem/luu_dia_diem.php', {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: data,
    })
    .then((res) => res.text())
    .then((response) => {
        if (response.trim() === "SUCCESS") {
            closeDiaDiemModal();
            // Làm mới vùng content-body để nạp bảng địa điểm vừa cập nhật
            document.getElementById("main-content-body").innerHTML = '<div style="padding: 20px;"><i class="fa-solid fa-spinner fa-spin"></i> Đang tải lại danh sách địa điểm...</div>';
            fetch(ddUrl).then((r) => r.text()).then((html) => {
                document.getElementById("main-content-body").innerHTML = html;
            });
        } else {
            alert("Có lỗi xảy ra: " + response);
        }
    });
}

function xoaDiaDiem(id) {
    if (confirm("Bạn có chắc chắn muốn xóa địa điểm du lịch này vĩnh viễn không?")) {
        fetch(ddUrl + "?action=delete&id=" + id)
            .then((res) => res.text())
            .then((data) => {
                if (data.trim() === "SUCCESS") {
                    fetch(ddUrl).then((r) => r.text()).then((html) => (document.getElementById("main-content-body").innerHTML = html));
                } else {
                    alert("Không thể xóa: " + data);
                }
            });
    }
}

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