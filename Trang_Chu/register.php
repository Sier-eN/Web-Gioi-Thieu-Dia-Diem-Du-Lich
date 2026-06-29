<div class="auth-box" style="max-width: 450px;">
    <h2>Tạo Tài Khoản Mới</h2>
    <div id="register-error" class="auth-error"></div>

    <form id="formUserRegister" onsubmit="handleUserRegister(event)">
        <div class="form-group-user">
            <label>Họ và tên</label>
            <input type="text" name="hoten" required class="form-control" style="width:100%; padding:10px; border:1px solid #cbd5e1; border-radius:6px; outline: none;">
        </div>

        <div class="form-group-user">
            <label>Tên đăng nhập</label>
            <input type="text" name="username" required class="form-control" style="width:100%; padding:10px; border:1px solid #cbd5e1; border-radius:6px; outline: none;">
        </div>

        <div class="form-group-user">
            <label>Email liên hệ</label>
            <input type="email" name="email" id="reg_email" required class="form-control" style="width:100%; padding:10px; border:1px solid #cbd5e1; border-radius:6px; outline: none;">
        </div>

        <div class="form-group-user">
            <label>Mật khẩu (Từ 8 đến 22 ký tự)</label>
            <input type="password" name="password" id="reg_pass" required class="form-control" style="width:100%; padding:10px; border:1px solid #cbd5e1; border-radius:6px; outline: none;">
        </div>

        <button type="submit" class="btn-auth-submit btn-bg-blue">
            Đăng ký thành viên
        </button>
    </form>
    <p style="font-size: 14px; color: #64748b; text-align: center; margin-top: 15px;">Đã là thành viên? <a href="javascript:void(0)" onclick="loadAuthPage('login')">Đăng nhập</a></p>
</div>