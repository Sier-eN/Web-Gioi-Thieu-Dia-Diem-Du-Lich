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

      // Thêm ?v= chống cache khi chuyển phân hệ quản trị
      fetch(url + (url.includes("?") ? "&" : "?") + "v=" + Date.now())
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
// ĐỒNG BỘ ĐƯỜNG DẪN TUYỆT ĐỐI CHO PHÂN HỆ TÀI KHOẢN
var currentUrl = "/GTDDDL/QL_Tai_Khoan/quan_ly_tai_khoan.php";

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

  fetch("/GTDDDL/QL_Tai_Khoan/luu_tai_khoan.php", {
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
        fetch(currentUrl + "?v=" + Date.now())
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
    fetch(currentUrl + "?action=delete&id=" + id + "&v=" + Date.now())
      .then((res) => res.text())
      .then((data) => {
        if (data.trim() === "SUCCESS") {
          fetch(currentUrl + "?v=" + Date.now())
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
  fetch(currentUrl + "?action=activate&id=" + id + "&v=" + Date.now())
    .then((res) => res.text())
    .then((data) => {
      if (data.trim() === "SUCCESS") {
        fetch(currentUrl + "?v=" + Date.now())
          .then((r) => r.text())
          .then(
            (html) =>
              (document.getElementById("main-content-body").innerHTML = html),
          );
      }
    });
}

function xoaHanTaiKhoan(id) {
  if (
    confirm(
      "CẢNH BÁO: Bạn có chắc chắn muốn XÓA VĨNH VIỄN tài khoản này không? Hành động này không thể hoàn tác!",
    )
  ) {
    fetch(currentUrl + "?action=destroy&id=" + id + "&v=" + Date.now())
      .then((res) => res.text())
      .then((data) => {
        if (data.trim() === "SUCCESS") {
          fetch(currentUrl + "?v=" + Date.now())
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
var dmUrl = "/GTDDDL/QL_Danh_Muc/quan_ly_danh_muc.php";

function openDanhMucModal() {
  document.getElementById("dmModalTitle").innerHTML =
    '<i class="fa-solid fa-folder-plus"></i> Thêm Danh Mục Mới';
  document.getElementById("formDanhMuc").reset();
  document.getElementById("dm_id").value = "";
  document.getElementById("danhMucModal").style.display = "block";
}

function openEditDanhMucModal(id, ten, mota) {
  document.getElementById("dmModalTitle").innerHTML =
    '<i class="fa-solid fa-folder-open"></i> Cập Nhật Danh Mục';
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

  fetch("/GTDDDL/QL_Danh_Muc/luu_danh_muc.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: data,
  })
    .then((res) => res.text())
    .then((response) => {
      if (response.trim() === "SUCCESS") {
        closeDanhMucModal();
        document.getElementById("main-content-body").innerHTML =
          '<div style="padding: 20px;"><i class="fa-solid fa-spinner fa-spin"></i> Đang tải lại danh mục...</div>';
        fetch(dmUrl + "?v=" + Date.now())
          .then((r) => r.text())
          .then((html) => {
            document.getElementById("main-content-body").innerHTML = html;
          });
      } else {
        alert("Có lỗi xảy ra: " + response);
      }
    });
}

function xoaDanhMuc(id) {
  if (
    confirm(
      "LƯU Ý: Xóa danh mục này sẽ đồng thời xóa TẤT CẢ các địa điểm du lịch thuộc danh mục này! Bạn có chắc chắn muốn xóa?",
    )
  ) {
    fetch(dmUrl + "?action=delete&id=" + id + "&v=" + Date.now())
      .then((res) => res.text())
      .then((data) => {
        if (data.trim() === "SUCCESS") {
          fetch(dmUrl + "?v=" + Date.now())
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
// 4. CHỨC NĂNG QUẢN LÝ ĐỊA ĐIỂM (MODAL & AJAX UPLOAD FILE)
// ==========================================
var ddUrl = "/GTDDDL/QL_Dia_Diem/quan_ly_dia_diem.php";

function previewImage(input) {
  var preview = document.getElementById("img-preview");
  if (input.files && input.files[0]) {
    var reader = new FileReader();
    reader.onload = function (e) {
      preview.src = e.target.result;
      preview.style.display = "block";
    };
    reader.readAsDataURL(input.files[0]);
  }
}

function openDiaDiemModal() {
  document.getElementById("ddModalTitle").innerHTML =
    '<i class="fa-solid fa-map-location-dot"></i> Thêm Địa Điểm Du Lịch Mới';
  document.getElementById("formDiaDiem").reset();
  document.getElementById("dd_id").value = "";
  document.getElementById("dd_hinhanh_cu").value = "";
  document.getElementById("img-preview").style.display = "none";
  document.getElementById("diaDiemModal").style.display = "block";
}

function openEditDiaDiemModal(jsonDataRaw) {
  var dd = JSON.parse(jsonDataRaw);

  document.getElementById("ddModalTitle").innerHTML =
    '<i class="fa-solid fa-route"></i> Cập Nhật Địa Điểm Du Lịch';

  document.getElementById("dd_id").value = dd.MaDiaDiem;
  document.getElementById("dd_ten").value = dd.TenDiaDiem;
  document.getElementById("dd_danhmuc").value = dd.MaDanhMuc;
  document.getElementById("dd_vungmien").value = dd.VungMien;
  document.getElementById("dd_diachi").value = dd.DiaChi;
  document.getElementById("dd_motangan").value = dd.MoTaNgan;
  document.getElementById("dd_chitiet").value = dd.ChiTiet;
  document.getElementById("dd_hinhanh_cu").value = dd.HinhAnhChinh;

  var preview = document.getElementById("img-preview");
  if (dd.HinhAnhChinh) {
    preview.src = "../images/" + dd.HinhAnhChinh;
  } else {
    preview.src = "../images/du_lich.png";
  }
  preview.style.display = "block";

  document.getElementById("diaDiemModal").style.display = "block";
}

function closeDiaDiemModal() {
  document.getElementById("diaDiemModal").style.display = "none";
}

function saveDiaDiem(e) {
  e.preventDefault();
  var formData = new FormData(document.getElementById("formDiaDiem"));

  fetch("/GTDDDL/QL_Dia_Diem/luu_dia_diem.php", {
    method: "POST",
    body: formData,
  })
    .then((res) => res.text())
    .then((response) => {
      if (response.trim() === "SUCCESS") {
        closeDiaDiemModal();
        document.getElementById("main-content-body").innerHTML =
          '<div style="padding: 20px;"><i class="fa-solid fa-spinner fa-spin"></i> Đang tải lại danh sách địa điểm...</div>';
        fetch(ddUrl + "?v=" + Date.now())
          .then((r) => r.text())
          .then((html) => {
            document.getElementById("main-content-body").innerHTML = html;
          });
      } else {
        alert("Có lỗi xảy ra: " + response);
      }
    })
    .catch((err) => console.error("Lỗi gửi dữ liệu địa điểm:", err));
}

function xoaDiaDiem(id) {
  if (
    confirm("Bạn có chắc chắn muốn xóa địa điểm du lịch này vĩnh viễn không?")
  ) {
    fetch(ddUrl + "?action=delete&id=" + id + "&v=" + Date.now())
      .then((res) => res.text())
      .then((data) => {
        if (data.trim() === "SUCCESS") {
          fetch(ddUrl + "?v=" + Date.now())
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
// 5. CHỨC NĂNG QUẢN LÝ ĐÁNH GIÁ (AJAX) - ĐÃ FIX DẤU CÁCH VÀ ĐƯỜNG DẪN ROOT
// ==========================================
var dgUrl = "/GTDDDL/QL_Danh_Gia/quan_ly_danh_gia.php";

function xoaDanhGia(id) {
  if (
    confirm(
      "Bạn có chắc chắn muốn xóa đánh giá/bình luận này không? Hành động này sẽ gỡ bỏ hoàn toàn phản hồi của user khỏi địa điểm du lịch.",
    )
  ) {
    fetch(dgUrl + "?action=delete&id=" + id + "&v=" + Date.now())
      .then((res) => res.text())
      .then((data) => {
        if (data.trim() === "SUCCESS") {
          fetch(dgUrl + "?v=" + Date.now())
            .then((r) => r.text())
            .then((html) => {
              document.getElementById("main-content-body").innerHTML = html;
            });
        } else {
          if (data.includes("<!DOCTYPE") || data.includes("<html")) {
            alert(
              "Lỗi hệ thống: Đường dẫn không chính xác hoặc dính lỗi server (404).",
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
