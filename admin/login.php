<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Login</title>
  <link href="css/bootstrap.min.css" rel="stylesheet">
  <script src="js/jquery-3.5.1.min.js"></script>
  <script src="js/ajaxValidation.js"></script>
  <style>
    body {
      background: linear-gradient(to right, #4e54c8, #8f94fb);
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .login-card {
      background: #fff;
      padding: 30px 40px;
      border-radius: 15px;
      box-shadow: 0 8px 20px rgba(0,0,0,0.2);
      width: 100%;
      max-width: 400px;
    }

    .login-card h2 {
      text-align: center;
      margin-bottom: 25px;
      color: #4e54c8;
    }

    .btn-login {
      background-color: #4e54c8;
      color: #fff;
      font-weight: bold;
      transition: 0.3s;
    }

    .btn-login:hover {
      background-color: #3b3fc1;
      color: #fff;
    }

    #message {
      color: red;
      text-align: center;
      margin-top: 10px;
      font-weight: 500;
    }
  </style>
</head>
<body>

  <div class="login-card">
    <h2>Đăng nhập Admin</h2>
    <div class="mb-3">
      <label class="form-label">Tài khoản</label>
      <input type="email" class="form-control" id="userEmail" placeholder="Nhập tài khoản của bạn">
    </div>
    <div class="mb-3">
      <label class="form-label">Password</label>
      <input type="password" class="form-control" id="userPassword" placeholder="Nhập mật khẩu của bạn">
    </div>
    <div class="d-grid">
      <button class="btn btn-login" id="checkValidation">Đăng nhập</button>
    </div>
    <p id="message"></p>
  </div>

 
</body>
</html>
