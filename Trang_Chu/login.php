<div class="auth-box">
    <h2>Đăng Nhập Khám Phá</h2>
    <div id="login-error" class="auth-error"></div>
    
    <form id="formUserLogin" onsubmit="handleUserLogin(event)">
        <div class="form-group-user">
            <label>Tên đăng nhập</label>
            <input type="text" name="username" id="login_user" required class="form-control" style="width:100%; padding:10px; border:1px solid #cbd5e1; border-radius:6px; outline: none;">
        </div>
        
        <div class="form-group-user">
            <label>Mật khẩu</label>
            <input type="password" name="password" id="login_pass" required class="form-control" style="width:100%; padding:10px; border:1px solid #cbd5e1; border-radius:6px; outline: none;">
        </div>

        <button type="submit" class="btn-auth-submit btn-bg-green">
            Bắt đầu hành trình
        </button>
    </form>
    <p style="font-size: 14px; color: #64748b; text-align: center; margin-top: 15px;">Chưa có tài khoản? <a href="javascript:void(0)" onclick="loadAuthPage('register')">Đăng ký ngay</a></p>
</div>