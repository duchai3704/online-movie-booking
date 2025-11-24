<?php
session_start();
include_once 'Database.php';
$id = $_GET['pass'];
$result = mysqli_query($conn,"SELECT * FROM add_movie WHERE id = '".$id."'");
$row = mysqli_fetch_array($result);
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="description" content="Trang chi tiết phim trực tuyến">
<meta name="keywords" content="phim, chi tiết phim, đặt vé, rạp chiếu phim">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="ie=edge">
<title><?php echo $row['movie_name'];?> - Chi tiết phim</title>

<link rel="stylesheet" href="css/bootstrap.min.css" type="text/css">
<link rel="stylesheet" href="css/font-awesome.min.css" type="text/css">
<link rel="stylesheet" href="css/elegant-icons.css" type="text/css">
<link rel="stylesheet" href="css/magnific-popup.css" type="text/css">
<link rel="stylesheet" href="css/nice-select.css" type="text/css">
<link rel="stylesheet" href="css/owl.carousel.min.css" type="text/css">
<link rel="stylesheet" href="css/slicknav.min.css" type="text/css">
<link rel="stylesheet" href="css/fonts-googleapis.css" type="text/css">
<link rel="stylesheet" href="css/style.css" type="text/css">
</head>
<body>
<?php include("header.php"); ?>

<section id="aboutUs">
  <div class="container">
<?php
$result = mysqli_query($conn,"SELECT * FROM add_movie WHERE id = '".$id."'");
if (mysqli_num_rows($result) > 0) {
  while($row = mysqli_fetch_array($result)) {
?>
    <div class="row feature design">
        <div class="col-lg-5">
            <img src="admin/image/<?php echo $row['image']; ?>" class="resize-detail" alt="<?php echo $row['movie_name'];?>" width="100%">
        </div>
        <div class="col-lg-7">
            <table class="table table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th colspan="2">Chi tiết phim</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td>Tên phim</td><td><?php echo $row['movie_name'];?></td></tr>
                    <tr><td>Ngày ra mắt</td><td><?php echo $row['release_date'];?></td></tr>
                    <tr><td>Đạo diễn</td><td><?php echo $row['directer'];?></td></tr>
                    <tr><td>Thể loại</td><td><?php echo $row['categroy'];?></td></tr>
                    <tr><td>Ngôn ngữ</td><td><?php echo $row['language'];?></td></tr>
                    <tr>
                        <td>Trailer</td>
                        <td>
                            <a class="btn btn-primary btn-sm" data-toggle="modal" data-target="#trailer_modal<?php echo $row['id'];?>">Xem Trailer</a>
                            <div class="modal fade" id="trailer_modal<?php echo $row['id'];?>" tabindex="-1" role="dialog" aria-hidden="true">
                                <div class="modal-dialog modal-lg" role="document">
                                    <div class="modal-content">
                                        <embed style="width:100%; height:500px;" src="<?php echo $row['you_tube_link'];?>"></embed>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>

            <?php if($row['action']== "running"){ ?>
            <div class="tiem-link mt-3">
                <h4>Đặt vé xem phim:</h4><br>
                <?php 
                $time = $row['show'];
                $movie = $row['movie_name'];
                $set_time = explode(",", $time);
                $res = mysqli_query($conn,"SELECT * FROM theater_show");
                if (mysqli_num_rows($res) > 0) {
                    while($show = mysqli_fetch_array($res)) {
                        if(in_array($show['show'],$set_time)){
                            echo '<a href="seatbooking.php?movie='.$movie.'&time='.$show['show'].'" class="btn btn-outline-primary me-2 mb-2">'.$show['show'].'</a>';
                        }
                    }
                }
                ?>
            </div>
            <?php } ?>
        </div>
    </div>

    <div class="description mt-4">
        <h4>Mô tả phim</h4>
        <p>
            <?php echo $row['decription'] ? $row['decription'] : 'Chưa có mô tả cho phim này.'; ?>
        </p>
    </div>
<?php
    }
}
?>
  </div>
</section>

<?php include("footer.php"); ?>

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
