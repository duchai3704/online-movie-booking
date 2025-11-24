<?php
session_start();
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="description" content="Mẫu đặt vé phim trực tuyến">
    <meta name="keywords" content="đặt vé, phim, liên hệ, rạp chiếu phim">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Trang Liên Hệ</title>

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@300;400;600;700;800;900&display=swap"
        rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@5.15.3/css/fontawesome.min.css">

    <!-- Css Styles -->
    <link rel="stylesheet" href="css/bootstrap.min.css" type="text/css">
    <link rel="stylesheet" href="css/font-awesome.min.css" type="text/css">
    <link rel="stylesheet" href="css/elegant-icons.css" type="text/css">
    <link rel="stylesheet" href="css/magnific-popup.css" type="text/css">
    <link rel="stylesheet" href="css/nice-select.css" type="text/css">
    <link rel="stylesheet" href="css/owl.carousel.min.css" type="text/css">
    <link rel="stylesheet" href="css/slicknav.min.css" type="text/css">
    <link rel="stylesheet" href="css/style.css" type="text/css">
</head>

<body>
    <?php include("header.php"); ?>

    <!-- Contact Section Begin -->
    <section class="contact spad">
        <div class="container">
            <div class="row">
                <!-- Thông tin liên hệ -->
                <div class="col-lg-6 col-md-6">
                    <div class="contact__text">
                        <div class="section-title">
                            <span>Thông tin liên hệ</span>
                            <h2>Liên hệ với chúng tôi</h2>
                            <p>Chúng tôi luôn lắng nghe và sẵn sàng hỗ trợ bạn về mọi thắc mắc và góp ý.</p>
                        </div>
                        <ul>
                            <li>
                                <h4>Nhóm 4 64CNTT-3</h4>
                                <p>Rạp INOX, Toà nhà 2, Tầng 4 Lotte Mart Nha Trang <br />
                                Điện thoại: 0123456789<br />
                                Fax: +79 123 456 789</p>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Bản đồ -->
                <div class="col-lg-6 col-md-6">
                  <div style="margin-top:40px;"><h3>Bản đồ Trường Đại học Nha Trang</h3></div>  
                    <div class="mapouter" style="width:90%;height:90%;margin-top:20px;">
                        <iframe 
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3918.4252694940267!2d109.20393527469778!3d12.238417591496562!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31706679cba2153d%3A0x57ebd80d81a7e49e!2sTr%C6%B0%E1%BB%9Dng%20%C4%90%E1%BA%A1i%20H%E1%BB%8Dc%20Nha%20Trang!5e0!3m2!1svi!2s!4v1700000000000!5m2!1svi!2s" 
                            width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy">
                        </iframe>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Contact Section End -->

    <!-- Footer Section Begin -->
    <?php include("footer.php"); ?>
    <!-- Footer Section End -->

    <!-- Js Plugins -->
    <script src="js/jquery-3.3.1.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/jquery.nice-select.min.js"></script>
    <script src="js/jquery.nicescroll.min.js"></script>
    <script src="js/jquery.magnific-popup.min.js"></script>
    <script src="js/jquery.countdown.min.js"></script>
    <script src="js/jquery.slicknav.js"></script>
    <script src="js/mixitup.min.js"></script>
    <script src="js/owl.carousel.min.js"></script>
    <script src="js/main.js"></script>
</body>

</html>
