<?php 
session_start();
if (!isset($_SESSION['uname'])) {
    header("location:index.php");
    exit();
}
include_once 'Database.php';
?>

<!doctype html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="description" content="Payment Page">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Thanh Toán Đặt Vé</title>

    <link href='https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css' rel='stylesheet'>
    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@300;400;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/payment.css"> 
    
    <style>
        .combo-list {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #ffc107;
            margin-top: 10px;
        }
        .combo-item-detail {
            padding: 8px 0;
            border-bottom: 1px dashed #dee2e6;
        }
        .combo-item-detail:last-child {
            border-bottom: none;
        }
    </style>
</head>

<body>
<div class="container py-5">
    <div class="row mb-4">
        <div class="col-lg-12 text-center">
            <h1 class="display-6">XÁC NHẬN VÀ THANH TOÁN ĐẶT VÉ</h1>
        </div>
    </div> 
    
    <div class="row">
        <div class="col-lg-6">
            <div class="card summary-card">
                <div class="card-header">
                    Chi Tiết Đặt Chỗ
                </div>
                <div class="card-body">
                    <div class="tab-content">
                        <div class="row">
                            <?php
                            $username = $_SESSION['uname'];
                            $price = 0;
                            $combo_total = 0;

                            if(isset($_POST['submit'])){
                                $show = $_POST['show'];
                                $seats1 = implode(",", $_POST["seat"]);
                                $seats = explode(",", $seats1);

                                // Tính tiền theo hàng ghế
                                for($i=1;$i<=12;$i++){
                                    $I = "I".$i; $H = "H".$i; $G = "G".$i; 
                                    $F = "F".$i; $E = "E".$i; $D = "D".$i;
                                    $C = "C".$i; $B = "B".$i; $A = "A".$i;

                                    if(in_array($I,$seats) || in_array($H,$seats) || in_array($G,$seats)){ $price += 100000; } 
                                    if(in_array($F,$seats) || in_array($E,$seats) || in_array($D,$seats) || in_array($C,$seats) || in_array($B,$seats)){ $price += 150000; } 
                                    if(in_array($A,$seats)){ $price += 300000; } 
                                }

                                // Lấy thông tin combo nếu có
                                $selected_combos = isset($_POST['combo']) ? $_POST['combo'] : [];
                                $combo_details = [];
                                $combo_ids = [];
                                
                                if(count($selected_combos) > 0){
                                    foreach($selected_combos as $combo_id => $quantity){
                                        $quantity = intval($quantity);
                                        if($quantity > 0){
                                            $combo_id = intval($combo_id);
                                            $combo_query = mysqli_query($conn, "SELECT combo_name, price FROM combos WHERE id = $combo_id");
                                            if($combo_data = mysqli_fetch_assoc($combo_query)){
                                                $subtotal = $combo_data['price'] * $quantity;
                                                $combo_total += $subtotal;
                                                $combo_details[] = [
                                                    'id' => $combo_id,
                                                    'name' => $combo_data['combo_name'],
                                                    'price' => $combo_data['price'],
                                                    'quantity' => $quantity,
                                                    'subtotal' => $subtotal
                                                ];
                                                $combo_ids[] = $combo_id . ':' . $quantity;
                                            }
                                        }
                                    }
                                }

                                $total_price = $price + $combo_total;
                                $formatted_price = number_format($total_price, 0, ',', '.');
                                $formatted_combo_total = number_format($combo_total,0,',','.');
                                $combo_string = implode('|', $combo_ids); // Format: id1:qty1|id2:qty2
                            ?>
                                <div class="col-lg-6">
                                    <p><strong>👤 Tên tài khoản:</strong><br><?= $username; ?></p>
                                    <p><strong>🎬 Phim:</strong><br><?= $_POST['movie']; ?></p>
                                    <p><strong>🕐 Suất chiếu:</strong><br><?= $_POST['show']; ?></p>
                                    <p><strong>💺 Ghế đã chọn:</strong><br><?= implode(", ", $_POST["seat"]); ?></p>
                                    <p><strong>📅 Ngày thanh toán:</strong><br><?= date("d-m-Y"); ?></p>
                                </div>
                                <div class="col-lg-6">
                                    <p><strong>🔢 Tổng ghế:</strong> <?= $_POST['totalseat']; ?></p>
                                    <p><strong>💵 Tiền ghế:</strong> <?= number_format($price,0,',','.'); ?> VNĐ</p>
                                    
                                    <?php if(count($combo_details) > 0): ?>
                                        <div class="combo-list">
                                            <strong>🍿 Combo đã chọn:</strong>
                                            <?php foreach($combo_details as $combo): ?>
                                                <div class="combo-item-detail">
                                                    <small>
                                                        <?= $combo['name']; ?> 
                                                        <br>
                                                        <span style="color: #666;">
                                                            <?= number_format($combo['price'],0,',','.'); ?> VNĐ × <?= $combo['quantity']; ?> = 
                                                            <strong style="color: #f5576c;"><?= number_format($combo['subtotal'],0,',','.'); ?> VNĐ</strong>
                                                        </span>
                                                    </small>
                                                </div>
                                            <?php endforeach; ?>
                                            <div style="margin-top: 10px; padding-top: 10px; border-top: 2px solid #ffc107;">
                                                <strong>Tổng combo: <?= $formatted_combo_total; ?> VNĐ</strong>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <p style="margin-top: 15px; font-size: 18px;">
                                        <strong>💰 TỔNG THANH TOÁN:</strong> 
                                        <span style="color: #f5576c; font-size: 22px;"><?= $formatted_price; ?> VNĐ</span>
                                    </p>
                                </div>
                            <?php } ?>
                        </div>
                    </div> 
                </div>
            </div>
        </div>
        
        <div class="col-lg-6">
            <div class="card payment-card">
                <div class="card-header bg-success text-white">
                    Thanh Toán Bằng Số Dư Tài Khoản
                </div>
                <div class="card-body">
                    <?php
                    $user_query = mysqli_query($conn, "SELECT balance FROM user WHERE username = '$username'");
                    $user_data = mysqli_fetch_assoc($user_query);
                    $balance = $user_data['balance'];
                    $formatted_balance = number_format($balance,0,',','.');
                    ?>
                    <div class="form-group">
                        <label><h6>Số dư hiện tại:</h6></label>
                        <div class="alert alert-info"><b><?= $formatted_balance; ?> VNĐ</b></div>
                    </div>

                    <div class="form-group">
                        <label><h6>Số tiền cần thanh toán:</h6></label>
                        <div class="alert alert-warning"><b><?= $formatted_price; ?> VNĐ</b></div>
                    </div>

                    <div id="msg"></div>

                    <div class="card-footer bg-light">
                        <input type="hidden" id="movie" value="<?= $_POST['movie']; ?>">
                        <input type="hidden" id="time" value="<?= $_POST['show']; ?>">
                        <input type="hidden" id="seat" value="<?= implode(",", $_POST["seat"]); ?>">
                        <input type="hidden" id="totalseat" value="<?= $_POST['totalseat']; ?>">
                        <input type="hidden" id="price" value="<?= $total_price; ?>">
                        <input type="hidden" id="balance" value="<?= $balance; ?>">
                        <input type="hidden" id="combos" value="<?= $combo_string; ?>">

                        <button type="submit" id="payment" class="btn btn-success btn-block shadow-sm">XÁC NHẬN THANH TOÁN</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JS -->
<script src="js/jquery-3.3.1.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script type="text/javascript">
$(document).ready(function(){
    $("#payment").click(function(){
        var movie = $("#movie").val().trim();
        var time = $("#time").val().trim();
        var seat = $("#seat").val().trim();
        var totalseat = $("#totalseat").val().trim();
        var price = parseInt($("#price").val().trim());
        var balance = parseInt($("#balance").val().trim());
        var combos = $("#combos").val().trim();

        if (balance < price) {
            $("#msg").html(`
                <div class="alert alert-danger">
                    ❌ Số dư tài khoản không đủ để thanh toán!
                    <br>
                    <a href="user.php" class="btn btn-primary btn-sm mt-2">➕ Nạp thêm tiền</a>
                </div>
            `);
            return false;
        }

        $.ajax({
            url: 'payment_balance.php',
            type: 'post',
            data: { 
                movie: movie, 
                time: time, 
                seat: seat, 
                totalseat: totalseat, 
                price: price,
                combos: combos
            },
            success: function(response){
                if(response == 1){
                    window.location = "tickes.php";
                } else {
                    $("#msg").html("<font color='red'>❌ Lỗi thanh toán. Vui lòng thử lại.</font>");
                }
            }
        });
    });
});
</script>
</body>
</html>