<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Trang Đăng Nhập</title>
<link href="https://fonts.googleapis.com/css?family=Montserrat" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<style>
body {
    font-family: 'Montserrat', sans-serif;
    background: #f0f2f5;
}

.login-container {
    max-width: 400px;
    margin: 80px auto;
    background: #fff;
    padding: 40px;
    border-radius: 15px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
}

.login-container h2 {
    text-align: center;
    margin-bottom: 30px;
    font-weight: 600;
}

.input-error {
    color: #d9534f;
    font-size: 0.875rem;
    margin: 3px 0 10px;
}

.btn-login {
    width: 100%;
    padding: 10px;
    font-weight: 600;
    border-radius: 8px;
}

.login-footer {
    text-align: center;
    margin-top: 15px;
}

.login-footer a {
    text-decoration: none;
    color: #007bff;
}

.login-footer a:hover {
    text-decoration: underline;
}
</style>
</head>
<body>

<div class="login-container">
    <div class="text-center mb-4">
        <a href="index.html"><img src="img/logo.png" alt="Logo" width="180"></a>
    </div>

    <h2>Đăng Nhập</h2>

    <div class="mb-3">
        <label class="form-label">Tên đăng nhập</label>
        <input type="text" class="form-control" id="username" placeholder="Nhập tên đăng nhập">
        <p id="nameerror" class="input-error"></p>
    </div>

    <div class="mb-3">
        <label class="form-label">Mật khẩu</label>
        <input type="password" class="form-control" id="password" placeholder="Nhập mật khẩu">
        <p id="passerror" class="input-error"></p>
    </div>

    <button class="btn btn-primary btn-login" id="login">Đăng Nhập</button>
    <p id="msg" class="input-error text-center mt-2"></p>

    <div class="login-footer mt-3">
        <a href="forget_password.php">Quên mật khẩu?</a> | 
        <a href="register_form.php">Đăng ký ngay</a>
    </div>
</div>

<script>
$(document).ready(function(){
    $("#login").click(function(e){
        e.preventDefault();
        $(".input-error").text(''); // Xóa lỗi cũ
        var username = $("#username").val().trim();
        var password = $("#password").val().trim();
        var hasError = false;

        if(username == ''){
            $("#nameerror").text("Vui lòng nhập tên đăng nhập.");
            hasError = true;
        }
        if(password == ''){
            $("#passerror").text("Vui lòng nhập mật khẩu.");
            hasError = true;
        }
        if(hasError) return false;

        $.ajax({
            url: 'login.php',
            type: 'POST',
            data: {username: username, password: password},
            success: function(response){
                response = response.trim();
                if(response == '1'){
                    window.location.href = "index.php";
                } else {
                    $("#msg").text("Tên đăng nhập hoặc mật khẩu không hợp lệ.");
                }
            },
            error: function(){
                $("#msg").text("Lỗi máy chủ. Vui lòng thử lại.");
            }
        });
    });
});
</script>

</body>
</html>
