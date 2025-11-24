<?php 
session_start();
include_once 'Database.php';

$time = $_GET['time'];
$movie = $_GET['movie'];
$date = date("Y-m-d");

// Lấy danh sách ghế đã được đặt
$result = mysqli_query($conn,"SELECT * FROM customers WHERE show_time = '".$time."' AND movie = '".$movie."' AND payment_date = '".$date."'");

$seats = "";
while($row = mysqli_fetch_array($result)) {
    $seats .= $row['seat'] . ",";
}
$seats1 = explode(",", rtrim($seats, ","));

// Lấy combo active từ DB
$combo_query = mysqli_query($conn, "SELECT * FROM combos WHERE status = 1");
$combos = [];
while($c = mysqli_fetch_assoc($combo_query)){
    $combos[] = $c;
}
?>


<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="description" content="Movie Booking Template">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo "Movie - ".$movie.", Time - ".$time;?></title>

    <!-- Google Font & CSS -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@300;400;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/bootstrap.min.css" type="text/css">
    <link rel="stylesheet" href="css/font-awesome.min.css" type="text/css">
    <link rel="stylesheet" href="css/elegant-icons.css" type="text/css">
    <link rel="stylesheet" href="css/magnific-popup.css" type="text/css">
    <link rel="stylesheet" href="css/nice-select.css" type="text/css">
    <link rel="stylesheet" href="css/owl.carousel.min.css" type="text/css">
    <link rel="stylesheet" href="css/slicknav.min.css" type="text/css">
    <link rel="stylesheet" href="css/style.css" type="text/css">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <link rel="stylesheet" href="css/seatbooking.css" type="text/css">
    <link rel="stylesheet" href="css/combos.css" type="text/css">
    
    <script type="text/javascript">
    $(document).ready(function(){
        // Xử lý chọn ghế
        $('.larger').click(function(){
            var text = "";
            $('.larger:checked').each(function(){
                text += $(this).val() + ',';
            });
            text = text.substring(0, text.length-1);
            $('#selectedtext').val(text);

            var count = $("[type='checkbox']:checked").length;
            $('#count').val(count);

            if(count > 8){
                document.getElementById('notvalid').innerHTML = "⚠️ Tối đa chỉ được chọn 8 ghế!";
                $(this).prop('checked', false);
                return false;
            } else if(count > 0) {
                document.getElementById('notvalid').innerHTML = "";
            }
            
            // Tính tổng tiền
            calculateTotal();
        });
        
        // Xử lý tăng/giảm số lượng combo
        $(document).on('click', '.btn-plus', function(){
            var input = $(this).siblings('.quantity-input');
            var currentVal = parseInt(input.val());
            if(currentVal < 10) {
                input.val(currentVal + 1);
                calculateTotal();
            }
        });
        
        $(document).on('click', '.btn-minus', function(){
            var input = $(this).siblings('.quantity-input');
            var currentVal = parseInt(input.val());
            if(currentVal > 0) {
                input.val(currentVal - 1);
                calculateTotal();
            }
        });
        
        // Hàm tính tổng tiền (ghế + combo)
        function calculateTotal() {
            var seatTotal = 0;
            var comboTotal = 0;
            
            // Tính tiền ghế
            $('.larger:checked').each(function(){
                var seatValue = $(this).val();
                var seatRow = seatValue.charAt(0);
                
                // Silver: I, H, G
                if(['I','H','G'].includes(seatRow)) {
                    seatTotal += 100000;
                }
                // Gold: F, E, D, C, B
                else if(['F','E','D','C','B'].includes(seatRow)) {
                    seatTotal += 150000;
                }
                // Platinum: A
                else if(seatRow === 'A') {
                    seatTotal += 300000;
                }
            });
            
            // Tính tiền combo
            $('.combo-item').each(function(){
                var quantity = parseInt($(this).find('.quantity-input').val());
                var price = parseInt($(this).data('price'));
                comboTotal += (quantity * price);
            });
            
            // Hiển thị
            $('#seatPrice').text(seatTotal.toLocaleString('vi-VN') + ' VNĐ');
            $('#comboPrice').text(comboTotal.toLocaleString('vi-VN') + ' VNĐ');
            $('#totalPrice').text((seatTotal + comboTotal).toLocaleString('vi-VN') + ' VNĐ');
        }
        
        // Validate trước khi submit
        $('form').submit(function(e){
            var selectedSeats = $('#selectedtext').val();
            if(!selectedSeats || selectedSeats === '') {
                e.preventDefault();
                $('#notvalid').html('⚠️ Vui lòng chọn ít nhất 1 ghế!');
                $('html, body').animate({
                    scrollTop: $('#notvalid').offset().top - 100
                }, 500);
                return false;
            }
        });
    });
    </script>
</head>
<body>
<div class="container mt-5">
    <div class="seat_heading">
        <h3><center>🎬 ĐẶT GHẾ XEM PHIM 🎬</center></h3>
    </div>

<form action="payment.php" method="post">
    <div class="row">
        <div class="col-lg-7">
            <div class="seatCharts-container">
                <div class="front">🎥 MÀN HÌNH 🎥</div>
                
                <!-- Legend -->
                <div class="legend">
                    <div class="legend-item">
                        <div class="legend-box legend-available"></div>
                        <span>Còn trống</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-box legend-selected"></div>
                        <span>Đang chọn</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-box legend-booked"></div>
                        <span>Đã đặt</span>
                    </div>
                </div>
                
                <center><p id="notvalid"></p></center>

                <!-- Silver Section -->
                <div class="seat_type">💺 Ghế Bạc - 100,000 VNĐ</div>
                <div class="row">
                    <div class="col-md-6">
                        <table class="w-100">
                            <?php 
                            $silver_rows = ['I','H','G'];
                            $silver_cols = [1,2,3,4,5,6];
                            foreach($silver_rows as $row):
                            ?>
                            <tr>
                                <td class="line" style="width:15%;"><?php echo $row;?></td>
                                <?php foreach($silver_cols as $col): 
                                    $seat_val = $row.$col;
                                ?>
                                <td><input type="checkbox" class="larger" name="seat[]" value="<?php echo $seat_val;?>" 
                                    <?php if(in_array($seat_val, $seats1)) echo "disabled";?>></td>
                                <?php endforeach; ?>
                            </tr>
                            <?php endforeach; ?>
                        </table>
                    </div>

                    <div class="col-md-6">
                        <table class="w-100">
                            <?php
                            $silver_right_cols = [7,8,9,10,11,12];
                            foreach($silver_rows as $row):
                            ?>
                            <tr>
                                <?php foreach($silver_right_cols as $col):
                                    $seat_val = $row.$col;
                                ?>
                                <td><input type="checkbox" class="larger" name="seat[]" value="<?php echo $seat_val;?>" 
                                    <?php if(in_array($seat_val,$seats1)) echo "disabled";?>></td>
                                <?php endforeach; ?>
                                <td class="line" style="width:15%;"><?php echo $row;?></td>
                            </tr>
                            <?php endforeach; ?>
                        </table>
                    </div>
                </div>

                <!-- Gold Section -->
                <div class="seat_type">🌟 Ghế Vàng - 150,000 VNĐ</div>
                <?php
                $gold_rows = ['F','E','D','C','B'];
                $gold_left_cols = [1,2,3,4,5,6];
                $gold_right_cols = [7,8,9,10,11,12];
                ?>
                <div class="row">
                    <div class="col-md-6">
                        <table class="w-100">
                            <?php foreach($gold_rows as $row): ?>
                            <tr>
                                <td class="line" style="width:15%"><?php echo $row;?></td>
                                <?php foreach($gold_left_cols as $col): 
                                    $seat_val = $row.$col;
                                ?>
                                <td><input type="checkbox" class="larger" name="seat[]" value="<?php echo $seat_val;?>" 
                                    <?php if(in_array($seat_val,$seats1)) echo "disabled";?>></td>
                                <?php endforeach; ?>
                            </tr>
                            <?php endforeach; ?>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="w-100">
                            <?php foreach($gold_rows as $row): ?>
                            <tr>
                                <?php foreach($gold_right_cols as $col):
                                    $seat_val = $row.$col;
                                ?>
                                <td><input type="checkbox" class="larger" name="seat[]" value="<?php echo $seat_val;?>" 
                                    <?php if(in_array($seat_val,$seats1)) echo "disabled";?>></td>
                                <?php endforeach; ?>
                                <td class="line" style="width:15%;"><?php echo $row;?></td>
                            </tr>
                            <?php endforeach; ?>
                        </table>
                    </div>
                </div>

                <!-- Platinum Section -->
                <div class="seat_type">👑 Ghế Platinum - 300,000 VNĐ</div>
                <div class="row">
                    <div class="col-md-6">
                        <table class="w-100">
                            <tr>
                                <td class="line" style="width:15%">A</td>
                                <?php for($i=1;$i<=6;$i++):
                                    $seat_val = "A".$i;
                                ?>
                                <td><input type="checkbox" class="larger" name="seat[]" value="<?php echo $seat_val;?>" 
                                    <?php if(in_array($seat_val,$seats1)) echo "disabled";?>></td>
                                <?php endfor; ?>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="w-100">
                            <tr>
                                <?php for($i=7;$i<=12;$i++):
                                    $seat_val = "A".$i;
                                ?>
                                <td><input type="checkbox" class="larger" name="seat[]" value="<?php echo $seat_val;?>" 
                                    <?php if(in_array($seat_val,$seats1)) echo "disabled";?>></td>
                                <?php endfor; ?>
                                <td class="line" style="width:15%">A</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar info -->
        <div class="col-lg-5">
            <div class="info-card">
                <h4 style="color: #667eea; font-weight: 700; margin-bottom: 25px;">📋 THÔNG TIN ĐẶT VÉ</h4>
                <table class="info-table">
                    <tr>
                        <td class="info-label">🎬 Phim:</td>
                        <td class="info-value"><?php echo $movie;?></td>
                    </tr>
                    <tr>
                        <td class="info-label">🕐 Suất chiếu:</td>
                        <td class="info-value"><?php echo $time;?></td>
                    </tr>
                    <tr>
                        <td class="info-label">💺 Ghế đã chọn:</td>
                        <td class="info-value">
                            <input type="text" id="selectedtext" name="seats" placeholder="Chưa chọn ghế" readonly>
                        </td>
                    </tr>
                    <tr>
                        <td class="info-label">🔢 Số lượng:</td>
                        <td class="info-value">
                            <input type="text" id="count" name="totalseat" placeholder="0" readonly>
                        </td>
                    </tr>
                    <tr>
                        <td class="info-label">💵 Tiền ghế:</td>
                        <td class="info-value" style="color: #667eea; font-size: 16px; font-weight: 600;">
                            <span id="seatPrice">0 VNĐ</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="info-label">🍿 Tiền combo:</td>
                        <td class="info-value" style="color: #ffc107; font-size: 16px; font-weight: 600;">
                            <span id="comboPrice">0 VNĐ</span>
                        </td>
                    </tr>
                    <tr style="border-top: 2px solid #667eea;">
                        <td class="info-label" style="font-size: 18px;">💰 TỔNG TIỀN:</td>
                        <td class="info-value" style="color: #f5576c; font-size: 22px; font-weight: 700;">
                            <span id="totalPrice">0 VNĐ</span>
                        </td>
                    </tr>
                </table>
                
                <input type="hidden" name="movie" value="<?php echo $movie;?>">
                <input type="hidden" name="show" value="<?php echo $time;?>">

         
              <!-- Combo Section -->
        <?php if(count($combos) > 0): ?>
            <div class="combo-section">
                <h5>🍿 CHỌN COMBO ĐỒ ĂN & NƯỚC UỐNG</h5>
                <?php foreach($combos as $combo): ?>
                    <div class="combo-item" data-combo-id="<?= $combo['id']; ?>" data-price="<?= $combo['price']; ?>">
                    <img src="image/combos/combo<?= $combo['id']; ?>.jpg" 
                        alt="<?= htmlspecialchars($combo['combo_name']); ?>">

                    <div class="combo-item-content">
                        <div>
                            <strong><?= htmlspecialchars($combo['combo_name']); ?></strong>
                            - <span style="color:#f5576c;"><?= number_format($combo['price'],0,',','.'); ?> VNĐ</span>
                            <?php if(!empty($combo['description'])): ?>
                                <br><small><?= htmlspecialchars($combo['description']); ?></small>
                            <?php endif; ?>
                        </div>

                        <div class="combo-quantity">
                            <button type="button" class="btn-minus">-</button>
                            <input type="number" name="combo[<?= $combo['id']; ?>]" value="0" min="0" max="10" readonly class="quantity-input">
                            <button type="button" class="btn-plus">+</button>
                        </div>
                    </div>
                </div>

                <?php endforeach; ?>
            </div>
        <?php endif; ?>


                <?php if (!isset($_SESSION['uname'])): ?>
                    <button type="button" class="btn btn-payment w-100" data-toggle="modal" data-target="#login_modal">
                        🔐 ĐĂNG NHẬP ĐỂ THANH TOÁN
                    </button>
                    
                    <!-- Login Modal -->
                    <div class="modal fade" id="login_modal" tabindex="-1" role="dialog">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-header border-0">
                                    <h4 class="modal-title w-100 text-center" style="color: #667eea; font-weight: 700;">
                                        🔐 Đăng nhập để tiếp tục
                                    </h4>
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                </div>
                                <div class="modal-body">
                                    <p style="color: #666; margin-bottom: 25px;">Bạn cần đăng nhập để thực hiện thanh toán</p>
                                    <a class="btn btn-payment w-100" href="login_form.php?redirect=<?php echo urlencode('payment.php?movie='.$movie.'&time='.$time);?>">
                                        Đăng nhập ngay
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <button type="submit" name="submit" class="btn btn-payment w-100" style="margin-top: 20px;">
                        💳 THANH TOÁN NGAY
                    </button>
                <?php endif; ?>

            </div>
        </div>
    </div>
</form>
</div>

<!-- JS Plugins -->
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