<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Ticket</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(to right, #74ebd5, #ACB6E5);
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            text-align: center;
        }

        h1 {
            color: #ff4b5c;
            text-shadow: 2px 2px 5px rgba(0,0,0,0.3);
            font-size: 2.5em;
            margin-bottom: 40px;
        }

        a.button {
            display: inline-block;
            margin: 15px;
            padding: 15px 30px;
            background: #3498db;
            color: #fff;
            text-decoration: none;
            font-size: 1.2em;
            border-radius: 10px;
            transition: 0.3s;
            box-shadow: 2px 5px 15px rgba(0,0,0,0.2);
        }

        a.button:hover {
            background: #2980b9;
            transform: translateY(-3px);
            box-shadow: 2px 8px 20px rgba(0,0,0,0.3);
        }

        .links {
            margin-top: 30px;
        }

        .links a {
            display: block;
            margin: 10px;
            color: #3498db;
            text-decoration: none;
            font-weight: bold;
            transition: 0.3s;
        }

        .links a:hover {
            color: #1d6fa5;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <h1>🎉 Bạn đã thanh toán thành công. Chúc bạn xem phim vui vẻ!!! 🎉</h1>

    <a href="ticket_show.php" target="_blank" class="button">Quay về trang chủ</a>

    <div class="links">
        <a href="logout.php">Đăng xuất</a>
    
    </div>
</body>
</html>
