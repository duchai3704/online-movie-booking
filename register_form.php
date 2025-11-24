<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Đăng Ký Tài Khoản</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://fonts.googleapis.com/css?family=Montserrat" rel="stylesheet">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<style>
body {
    font-family: 'Montserrat', sans-serif;
    background: #f0f2f5;
    font-size: 18px; /* font mặc định lớn hơn */
}

.container {
    max-width: 500px;
    margin: 50px auto;
    background: #fff;
    padding: 40px 50px; /* padding to hơn */
    border-radius: 12px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
}

.title {
    text-align: center;
    font-size: 2.8rem; /* tăng font size */
    font-weight: 700;
    margin-bottom: 30px;
}

.input-box {
    margin-bottom: 20px;
}

.input-box span.details {
    font-weight: 600;
    font-size: 1.2rem; /* label to hơn */
}

.input-box input {
    width: 100%;
    padding: 14px; /* input cao hơn */
    border-radius: 6px;
    border: 1px solid #ccc;
    font-size: 1.1rem; /* font input to hơn */
}

.input-error {
    color: #d9534f;
    font-size: 1rem; /* lỗi to hơn */
    margin-top: 5px;
}

.button input {
    width: 100%;
    padding: 16px; /* nút cao hơn */
    background: #007bff;
    color: #fff;
    font-weight: 700;
    font-size: 1.2rem; /* font nút to hơn */
    border: none;
    border-radius: 8px;
    cursor: pointer;
    transition: background 0.3s ease;
}

.button input:hover {
    background: #0056b3;
}

img {
    max-width: 50%;
    margin-bottom: 20px;
}
</style>

</head>
<body>

<div class="container">
    <center><a href="./index.html"><img src="img/logo.png" alt="Logo"></a></center>
    <div class="title">Đăng Ký Tài Khoản</div>
    <form id="form" action="register.php" method="post" enctype="multipart/form-data" onsubmit="return validate();">
        <div class="user-details">

            <div class="input-box">
                <span class="details">Tên đăng nhập</span>
                <input type="text" id="username" name="username" placeholder="Nhập tên đăng nhập">
                <p id="nameerror" class="input-error"></p>
            </div>

            <div class="input-box">
                <span class="details">Email</span>
                <input type="text" id="email" name="email" placeholder="Nhập email của bạn">
                <p id="emailerror" class="input-error"></p>
            </div>

            <div class="input-box">
                <span class="details">Số điện thoại</span>
                <input type="text" id="number" name="number" placeholder="Nhập số điện thoại">
                <p id="numbererror" class="input-error"></p>
            </div>

            <div class="input-box">
                <span class="details">Thành phố</span>
                <input type="text" id="city" name="city" placeholder="Nhập thành phố">
                <p id="cityerror" class="input-error"></p>
            </div>

            <div class="input-box">
                <span class="details">Mật khẩu</span>
                <input type="password" id="password" name="password" placeholder="Nhập mật khẩu">
                <p id="passworderror" class="input-error"></p>
            </div>

            <div class="input-box">
                <span class="details">Xác nhận mật khẩu</span>
                <input type="password" id="cpassword" name="cpassword" placeholder="Nhập lại mật khẩu">
                <p id="cpassworderror" class="input-error"></p>
            </div>

            <div class="input-box">
                <span class="details">Tải ảnh (Tuỳ chọn)</span>
                <input type="file" id="image" name="image">
            </div>

        </div>

        <div class="button">
            <input type="submit" value="Đăng Ký" id="submit" name="submit">
        </div>
    </form>
</div>

<script>
function validate() {
    $(".input-error").text(""); // reset lỗi
    let name = $("#username").val().trim();
    let email = $("#email").val().trim();
    let number = $("#number").val().trim();
    let city = $("#city").val().trim();
    let password = $("#password").val().trim();
    let cpassword = $("#cpassword").val().trim();
    let valid = true;

    if(name === "") {
        $("#nameerror").text("Vui lòng nhập tên đăng nhập.");
        valid = false;
    } else if(name.length < 3 || name.length > 20) {
        $("#nameerror").text("Tên đăng nhập phải từ 3 đến 20 ký tự.");
        valid = false;
    } else if(!isNaN(name)) {
        $("#nameerror").text("Tên đăng nhập không được là số.");
        valid = false;
    }

    if(email === "") {
        $("#emailerror").text("Vui lòng nhập email.");
        valid = false;
    } else if(!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
        $("#emailerror").text("Email không hợp lệ.");
        valid = false;
    }

    if(number === "") {
        $("#numbererror").text("Vui lòng nhập số điện thoại.");
        valid = false;
    } else if(number.length !== 10 || isNaN(number)) {
        $("#numbererror").text("Số điện thoại phải 10 chữ số và là số.");
        valid = false;
    }

    if(city === "") {
        $("#cityerror").text("Vui lòng nhập thành phố.");
        valid = false;
    }

    if(password === "") {
        $("#passworderror").text("Vui lòng nhập mật khẩu.");
        valid = false;
    } else if(password.length < 3 || password.length > 10) {
        $("#passworderror").text("Mật khẩu phải từ 3 đến 10 ký tự.");
        valid = false;
    }

    if(cpassword === "") {
        $("#cpassworderror").text("Vui lòng xác nhận mật khẩu.");
        valid = false;
    } else if(cpassword !== password) {
        $("#cpassworderror").text("Mật khẩu không khớp.");
        valid = false;
    }

    return valid;
}
</script>

</body>
</html>
