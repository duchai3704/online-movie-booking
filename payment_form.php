<?php
session_start();
include "Database.php";

if ($_POST['card_name'] != '' && $_POST['card_number'] != '' && $_POST['ex_date'] != '' && $_POST['cvv'] != '') {

    $movie = mysqli_real_escape_string($conn, $_POST['movie']);
    $time = mysqli_real_escape_string($conn, $_POST['time']);
    $seat = mysqli_real_escape_string($conn, $_POST['seat']);
    $totalseat = mysqli_real_escape_string($conn, $_POST['totalseat']);
    $price = floatval($_POST['price']);
    $card_name = mysqli_real_escape_string($conn, $_POST['card_name']);
    $card_number = mysqli_real_escape_string($conn, $_POST['card_number']);
    $ex_date = mysqli_real_escape_string($conn, $_POST['ex_date']);
    $cvv = mysqli_real_escape_string($conn, $_POST['cvv']);

    // Lấy thông tin user hiện tại
    $result = mysqli_query($conn, "SELECT * FROM user WHERE username = '" . $_SESSION['uname'] . "'");
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $uid = $row['id'];
        $currentBalance = isset($row['balance']) ? floatval($row['balance']) : 0;
    } else {
        echo 2; // Không tìm thấy user
        exit;
    }

    // Kiểm tra số dư
    if ($currentBalance < $price) {
        echo "not_enough_money";
        exit;
    }

    // Trừ tiền trong tài khoản
    $newBalance = $currentBalance - $price;
    mysqli_query($conn, "UPDATE user SET balance = '$newBalance' WHERE id = '$uid'");

    // Tạo thông tin vé
    $custemer_id = mt_rand();
    $payment = date("Y-m-d", strtotime('today'));
    $booking = date("Y-m-d", strtotime('tomorrow'));

    $_SESSION['custemer_id'] = $custemer_id;

    // Ghi vé vào bảng customers
    $insert_record = mysqli_query($conn, "
        INSERT INTO customers 
        (`uid`,`movie`,`show_time`,`seat`,`totalseat`,`price`,`payment_date`,`booking_date`,
        `card_name`,`card_number`,`ex_date`,`cvv`,`custemer_id`) 
        VALUES (
            '$uid','$movie','$time','$seat','$totalseat','$price','$payment','$booking',
            '$card_name','$card_number','$ex_date','$cvv','$custemer_id'
        )
    ");

    // Lưu giao dịch (nếu có bảng transactions)
    mysqli_query($conn, "
        INSERT INTO transactions (username, amount, type, created_at)
        VALUES ('" . $_SESSION['uname'] . "', '-$price', 'purchase', NOW())
    ");

    if (!$insert_record) {
        echo 2; // Lỗi ghi CSDL
    } else {
        echo 1; // Thành công
    }
}
?>
