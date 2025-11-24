<?php
session_start();
include_once 'Database.php';

if (!isset($_SESSION['uname'])) {
    echo 0;
    exit();
}

$username = mysqli_real_escape_string($conn, $_SESSION['uname']);
$movie = mysqli_real_escape_string($conn, $_POST['movie']);
$time = mysqli_real_escape_string($conn, $_POST['time']);
$seat = mysqli_real_escape_string($conn, $_POST['seat']);
$totalseat = intval($_POST['totalseat']);
$price = floatval($_POST['price']);
$combos = isset($_POST['combos']) ? mysqli_real_escape_string($conn, $_POST['combos']) : '';

// Lấy thông tin user
$user_query = mysqli_query($conn, "SELECT id, balance FROM user WHERE username = '$username'");
$user_data = mysqli_fetch_assoc($user_query);

if (!$user_data) {
    echo 0;
    exit();
}

$current_balance = $user_data['balance'];
$user_id = $user_data['id'];

if ($current_balance >= $price) {
    // Trừ tiền
    $new_balance = $current_balance - $price;
    mysqli_query($conn, "UPDATE user SET balance = $new_balance WHERE id = $user_id");

    // Lưu booking vào bảng 'customers' - Chú ý: cột là 'combo' không phải 'combos'
    $insert_booking = mysqli_query($conn, "INSERT INTO customers 
        (uid, movie, show_time, seat, totalseat, price, combo, payment_date, booking_date, custemer_id) 
        VALUES 
        ($user_id, '$movie', '$time', '$seat', $totalseat, $price, '$combos', NOW(), NOW(), $user_id)");
    
    if(!$insert_booking) {
        echo "Error: " . mysqli_error($conn);
        exit();
    }
    
    $booking_id = mysqli_insert_id($conn);

    // Lưu chi tiết combo vào bảng booking_combos
    if(!empty($combos)) {
        $combo_items = explode('|', $combos);
        
        foreach($combo_items as $item) {
            if(!empty($item)) {
                $parts = explode(':', $item);
                if(count($parts) == 2) {
                    $combo_id = intval($parts[0]);
                    $quantity = intval($parts[1]);
                    
                    if($combo_id > 0 && $quantity > 0) {
                        mysqli_query($conn, "INSERT INTO booking_combos 
                            (booking_id, combo_id, quantity, created_at) 
                            VALUES 
                            ($booking_id, $combo_id, $quantity, NOW())");
                    }
                }
            }
        }
    }

    // Lưu customer_id vào session (không phải custemer_id)
    $_SESSION['customer_id'] = $user_id;

    echo 1;
} else {
    echo 0;
}
?>