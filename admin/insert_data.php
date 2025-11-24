<?php
include_once("Database.php");

// Thêm Admin
if(isset($_POST['name']) && !isset($_POST['edit_name'])) {
    $name = mysqli_real_escape_string($conn,$_POST['name']);
    $email = mysqli_real_escape_string($conn,$_POST['email']);
    $password = mysqli_real_escape_string($conn,$_POST['password']);
    $is_active = intval($_POST['is_active']);
    $sql = mysqli_query($conn,"INSERT INTO admin(name,email,password,is_active) VALUES('$name','$email','$password','$is_active')");
    echo $sql ? "success" : "unsuccessful";
    exit;
}

// Sửa Admin
if(isset($_POST['edit_name'])) {
    $id = intval($_POST['id']);
    $name = mysqli_real_escape_string($conn,$_POST['edit_name']);
    $email = mysqli_real_escape_string($conn,$_POST['edit_email']);
    $password = mysqli_real_escape_string($conn,$_POST['edit_password']);
    $is_active = intval($_POST['edit_is_active']);
    $sql = mysqli_query($conn,"UPDATE admin SET name='$name', email='$email', password='$password', is_active='$is_active' WHERE id='$id'");
    echo $sql ? "updated" : "unsuccessful";
    exit;
}

// Xóa Admin
if(isset($_POST['delete_admin'])) {
    $id = intval($_POST['id']);
    $sql = mysqli_query($conn,"DELETE FROM admin WHERE id='$id'");
    echo $sql ? "deleted" : "unsuccessful";
    exit;
}
// ============================
// THÊM PHIM
// ============================
if (isset($_POST['submit'])) {
    $movie_name = mysqli_real_escape_string($conn, $_POST['movie_name']);
    $directer_name = mysqli_real_escape_string($conn, $_POST['directer_name']);
    $release_date = mysqli_real_escape_string($conn, $_POST['release_date']);
    $categroy = mysqli_real_escape_string($conn, $_POST['category']);
    $language = mysqli_real_escape_string($conn, $_POST['language']);
    $tailer = mysqli_real_escape_string($conn, $_POST['tailer']);
    $action = mysqli_real_escape_string($conn, $_POST['action']);
    $decription = mysqli_real_escape_string($conn, $_POST['decription']);
    $show = isset($_POST['show']) ? mysqli_real_escape_string($conn, implode(',', $_POST['show'])) : '';

    $filename = $_FILES['img']['name'];
    if($filename == '') { echo "Bạn chưa chọn hình"; exit; }
    $location = 'image/' . $filename;
    $file_extension = strtolower(pathinfo($location, PATHINFO_EXTENSION));
    $image_ext = array('jpg', 'png', 'jpeg', 'gif');

    if (in_array($file_extension, $image_ext)) {
        if (!move_uploaded_file($_FILES['img']['tmp_name'], $location)) {
            echo "Lỗi upload hình"; exit;
        }
    } else {
        echo "Định dạng hình không hợp lệ"; exit;
    }

    $status = 1;
    $insert_record = mysqli_query($conn, "INSERT INTO add_movie 
        (`movie_name`,`directer`,`release_date`,`categroy`,`language`,`you_tube_link`,`action`,`decription`,`show`,`image`,`status`) 
        VALUES 
        ('$movie_name','$directer_name','$release_date','$categroy','$language','$tailer','$action','$decription','$show','$filename','$status')");

    if($insert_record){
        header("Location: Add-movie.php"); // trở về trang quản lý phim
        exit;
    } else {
        echo "Thêm phim thất bại: ".mysqli_error($conn);
        exit;
    }
}

// ============================
// CẬP NHẬT PHIM
// ============================
if (isset($_POST['updatemovie'])) {
    $e_id = mysqli_real_escape_string($conn, $_POST['e_id']);
    $edit_movie_name = mysqli_real_escape_string($conn, $_POST['edit_movie_name']);
    $edit_directer_name = mysqli_real_escape_string($conn, $_POST['edit_directer_name']);    
    $edit_categroy = mysqli_real_escape_string($conn, $_POST['edit_category']);
    $edit_language = mysqli_real_escape_string($conn, $_POST['edit_language']);
    $tailer = mysqli_real_escape_string($conn, $_POST['edit_tailer']);
    $action = mysqli_real_escape_string($conn, $_POST['edit_action']);
    $decription = mysqli_real_escape_string($conn, $_POST['decription']);
    $edit_show = isset($_POST['show']) ? mysqli_real_escape_string($conn, implode(',', $_POST['show'])) : '';
    $edit_old_image = mysqli_real_escape_string($conn, $_POST['old_image']);
    $edit_filename = $_FILES['edit_img']['name'];

    if ($edit_filename != '') {
        $image = $edit_filename;
        $location = 'image/' . $image;
        $file_extension = strtolower(pathinfo($location, PATHINFO_EXTENSION));
        $image_ext = array('jpg', 'png', 'jpeg', 'gif');

        if (!in_array($file_extension, $image_ext)) { echo "Định dạng hình không hợp lệ"; exit; }
        if (!move_uploaded_file($_FILES['edit_img']['tmp_name'], $location)) { echo "Lỗi upload hình"; exit; }
    } else {
        $image = $edit_old_image;
    }

    $update_record = mysqli_query($conn, "
    UPDATE `add_movie` 
    SET 
        `movie_name` = '$edit_movie_name', 
        `directer` = '$edit_directer_name', 
        `categroy` = '$edit_categroy', 
        `language` = '$edit_language',
        `you_tube_link` = '$tailer',
        `action` = '$action',
        `decription` = '$decription', 
        `show` = '$edit_show', 
        `image` = '$image' 
    WHERE `id` = '$e_id'
    ");

    if($update_record){
        header("Location: Add-movie.php"); // trở về trang quản lý phim
        exit;
    } else {
        echo "Cập nhật thất bại: ".mysqli_error($conn);
        exit;
    }
}

// ============================
// XÓA PHIM
// ============================
if (isset($_POST['deletemovie'])) {
    $id = mysqli_real_escape_string($conn, $_POST['id']);
    $sql = mysqli_query($conn, "DELETE FROM add_movie WHERE id='$id'");
    if($sql){
        header("Location: Add-movie.php");
        exit;
    } else {
        echo "Xóa thất bại: ".mysqli_error($conn);
        exit;
    }
}

// ============================
// THÊM LỊCH CHIẾU / THEATER SHOW
// ============================
if (isset($_POST['addshow'])) {
    $theater = mysqli_real_escape_string($conn, $_POST['theater_name'] ?? '');
    $show = mysqli_real_escape_string($conn, $_POST['show'] ?? '');

    if ($theater != '' && $show != '') {
        mysqli_query($conn, "INSERT INTO theater_show (`show`, `theater`) VALUES ('$show', '$theater')");
    }
    header("Location: Theater_and_show.php");
    exit;
}

// ============================
// CẬP NHẬT LỊCH CHIẾU
// ============================
if (isset($_POST['updatetime'])) {
    $e_id = mysqli_real_escape_string($conn, $_POST['e_id'] ?? '');
    $edit_screen = mysqli_real_escape_string($conn, $_POST['edit_screen'] ?? '');
    $edit_time = mysqli_real_escape_string($conn, $_POST['edit_time'] ?? '');

    if ($e_id != '' && $edit_screen != '' && $edit_time != '') {
        mysqli_query($conn, "UPDATE theater_show SET theater='$edit_screen', `show`='$edit_time' WHERE id='$e_id'");
    }
    header("Location: Theater_and_show.php");
    exit;
}

// ============================
// XÓA LỊCH CHIẾU
// ============================
if (isset($_POST['deletetime'])) {
    $id = mysqli_real_escape_string($conn, $_POST['id'] ?? '');
    if ($id != '') {
        mysqli_query($conn, "DELETE FROM theater_show WHERE id='$id'");
    }
    header("Location: Theater_and_show.php");
    exit;
}
 
// ============================
 
// ============================
// Cập nhật feedback
// ============================
if (isset($_POST['updatefeedback'])) {
    $e_id = mysqli_real_escape_string($conn, $_POST['e_id']);
    $edit_feedback_name = mysqli_real_escape_string($conn, $_POST['edit_feedback_name']);
    $edit_feedback_email = mysqli_real_escape_string($conn, $_POST['edit_feedback_email']);
    $edit_feedback_massage = mysqli_real_escape_string($conn, $_POST['edit_feedback_massage']);

    mysqli_query($conn, "UPDATE feedback SET name='$edit_feedback_name', email='$edit_feedback_email', massage='$edit_feedback_massage' WHERE id='$e_id'");
    header("Location: feedback.php"); // quay lại trang feedback
    exit;
}

// ============================
// Xóa feedback
// ============================
if (isset($_POST['deletefeedback'])) {
    $id = mysqli_real_escape_string($conn, $_POST['id']);
    mysqli_query($conn, "DELETE FROM feedback WHERE id='$id'");
    header("Location: feedback.php"); // quay lại trang feedback
    exit;
}


// Thêm user
// ============================
if (isset($_POST['add_user'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $mobile = mysqli_real_escape_string($conn, $_POST['mobile']);
    $city = mysqli_real_escape_string($conn, $_POST['city']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    $filename = $_FILES['img']['name'];
    $location = 'image/' . $filename;
    $file_extension = strtolower(pathinfo($location, PATHINFO_EXTENSION));
    $image_ext = ['jpg','png','jpeg','gif'];

    if($filename != '' && in_array($file_extension, $image_ext)){
        if(!move_uploaded_file($_FILES['img']['tmp_name'], $location)){
            echo "error_upload"; exit;
        }
    } else {
        $filename = ''; // nếu không upload ảnh
    }

    mysqli_query($conn,"INSERT INTO user (`username`,`email`,`mobile`,`city`,`password`,`image`) VALUES ('$username','$email','$mobile','$city','$password','$filename')");
    header("Location: users.php");
    exit;
}

// ============================
// Cập nhật user
// ============================
if (isset($_POST['updateusers'])) {
    $e_id = mysqli_real_escape_string($conn,$_POST['e_id']);
    $edit_Username = mysqli_real_escape_string($conn,$_POST['edit_username']);
    $edit_email = mysqli_real_escape_string($conn,$_POST['edit_email']);    
    $edit_mobile = mysqli_real_escape_string($conn,$_POST['edit_mobile']);
    $edit_city = mysqli_real_escape_string($conn,$_POST['edit_city']);
    $edit_password = mysqli_real_escape_string($conn,$_POST['edit_password']);
    $edit_old_image = mysqli_real_escape_string($conn,$_POST['old_image']);
    $edit_filename = $_FILES['edit_img']['name'];

    if($edit_filename != ''){
        $image = $edit_filename;
        $location='image/'.$image;
        $file_extension=strtolower(pathinfo($location, PATHINFO_EXTENSION));
        $image_ext = ['jpg','png','jpeg','gif'];

        if(!in_array($file_extension,$image_ext)){ echo "invalid_file"; exit; }
        if(!move_uploaded_file($_FILES['edit_img']['tmp_name'],$location)){ echo "error_upload"; exit; }

        // Xóa ảnh cũ nếu có
        if($edit_old_image != '' && file_exists('image/'.$edit_old_image)){
            unlink('image/'.$edit_old_image);
        }
    } else {
        $image = $edit_old_image;
    }

    mysqli_query($conn,"UPDATE user SET username='$edit_Username', email='$edit_email', mobile='$edit_mobile', city='$edit_city', password='$edit_password', image='$image' WHERE id='$e_id'");
    header("Location: users.php");
    exit;
}

// ============================
// Xóa user
// ============================
if (isset($_POST['deleteuser'])) {
    $id = mysqli_real_escape_string($conn,$_POST['id']);

    // Xóa ảnh trước khi delete
    $res = mysqli_query($conn,"SELECT image FROM user WHERE id='$id'");
    if($row = mysqli_fetch_assoc($res)){
        $img = $row['image'];
        if($img != '' && file_exists('image/'.$img)){
            unlink('image/'.$img);
        }
    }

    mysqli_query($conn,"DELETE FROM user WHERE id='$id'");
    header("Location: users.php");
    exit;
}

// ============================
// CUSTOMERS
// ============================
if (isset($_POST['customers'])) {
    $username_id = mysqli_real_escape_string($conn,$_POST['username_id']);
    $movie = mysqli_real_escape_string($conn,$_POST['movie']);
    $show_time = mysqli_real_escape_string($conn,$_POST['show_time']);
    $seat = mysqli_real_escape_string($conn,$_POST['seat']);
    $totalseat = mysqli_real_escape_string($conn,$_POST['totalseat']);
    $price = mysqli_real_escape_string($conn,$_POST['price']);
    $custemer_id = mt_rand();
    $payment = date("D-m-y");
    $booking = date("D-m-y", strtotime("+1 day"));
    $insert_record = mysqli_query($conn,"INSERT INTO customers (`uid`,`movie`,`show_time`,`seat`,`totalseat`,`price`,`payment_date`,`booking_date`,`custemer_id`) VALUES ('$username_id','$movie','$show_time','$seat','$totalseat','$price','$payment','$booking','$custemer_id')");
    echo $insert_record ? "success" : "unsuccessful";
    exit;
}
// ============================
// XỬ LÝ HOÀN TIỀN TỪ FEEDBACK (THEO ID KHÁCH HÀNG)
// ============================
if (isset($_POST['approve_refund'])) {
    include_once "Database.php";

    // Lấy dữ liệu từ form
    $fid        = intval($_POST['refund_feedback_id']);
    $user_id    = intval($_POST['refund_user_id']);
    $amount     = floatval($_POST['refund_amount']);
    $reason     = mysqli_real_escape_string($conn, $_POST['refund_reason']);

    // Validate dữ liệu
    if ($user_id <= 0 || $amount <= 0) {
        header("Location: feedback.php?error=invalid_data");
        exit;
    }

    // Kiểm tra user theo ID - DÙNG QUERY THƯỜNG TRƯỚC
    $result = mysqli_query($conn, "SELECT id, username, balance FROM user WHERE id = $user_id");
    
    if (!$result || mysqli_num_rows($result) == 0) {
        header("Location: feedback.php?error=user_not_found&id=$user_id");
        exit;
    }

    $user = mysqli_fetch_assoc($result);
    $username = $user['username'];
    $current_balance = floatval($user['balance']);

    // Tính số dư mới
    $new_balance = $current_balance + $amount;

    // 1. Cập nhật số dư khách hàng
    $update_balance = mysqli_query($conn, "UPDATE user SET balance = $new_balance WHERE id = $user_id");
    
    if (!$update_balance) {
        header("Location: feedback.php?error=update_balance_failed");
        exit;
    }

    // 2. Lưu log hoàn tiền (kiểm tra xem bảng refunds có cột gì)
    $insert_refund = mysqli_query($conn, "INSERT INTO refunds(username, amount, reason) 
                                    VALUES('$username', $amount, '$reason')");
    
    if (!$insert_refund) {
        // Nếu lỗi, rollback số dư
        mysqli_query($conn, "UPDATE user SET balance = $current_balance WHERE id = $user_id");
        header("Location: feedback.php?error=insert_refund_failed");
        exit;
    }

    // 3. Đánh dấu feedback đã hoàn
    $update_feedback = mysqli_query($conn, "UPDATE feedback SET refund_status = 1 WHERE id = $fid");
    
    if (!$update_feedback) {
        header("Location: feedback.php?error=update_feedback_failed");
        exit;
    }

    // Thành công - Redirect về feedback.php
    header("Location: feedback.php?success=refund_completed&user=$username&amount=$amount");
    exit;
}

// ============================
// THÊM COMBO
// ============================
if (isset($_POST['add_combo'])) {

    $combo_name = mysqli_real_escape_string($conn, $_POST['combo_name']);
    $combo_price = intval($_POST['combo_price']);
    $description = mysqli_real_escape_string($conn, $_POST['combo_description']);
    $is_active = intval($_POST['combo_is_active']);

    // Upload ảnh
    $filename = $_FILES['combo_img']['name'];
    $location = 'image/' . $filename;
    $file_extension = strtolower(pathinfo($location, PATHINFO_EXTENSION));
    $image_ext = ['jpg','png','jpeg','gif'];

    if($filename != '' && in_array($file_extension, $image_ext)){
        if(!move_uploaded_file($_FILES['combo_img']['tmp_name'], $location)){
            echo "upload_error"; exit;
        }
    } else {
        $filename = ''; // không upload ảnh
    }

    $insert = mysqli_query($conn, "INSERT INTO combos (combo_name, price, description, image, status)
                                   VALUES ('$combo_name', '$combo_price', '$description', '$filename', '$is_active')");

    if($insert){
        header("Location: food_orders.php");  
        exit;
    } else {
        echo "Thêm combos thất bại: ".mysqli_error($conn);
        exit;
    }
}
// ============================
// CẬP NHẬT COMBO
// ============================
if (isset($_POST['update_combo'])) {

    $id = intval($_POST['combo_id']);
    $name = mysqli_real_escape_string($conn, $_POST['edit_combo_name']);
    $price = intval($_POST['edit_combo_price']);
    $description = mysqli_real_escape_string($conn, $_POST['edit_combo_description']);
    $is_active = intval($_POST['edit_combo_is_active']);

    $old_image = mysqli_real_escape_string($conn, $_POST['old_combo_image']);
    $new_image = $_FILES['edit_combo_img']['name'];

    if ($new_image != '') {
        $image = $new_image;
        $location = 'image/' . $image;
        $file_extension = strtolower(pathinfo($location, PATHINFO_EXTENSION));
        $image_ext = ['jpg','png','jpeg','gif'];

        if (!in_array($file_extension, $image_ext)) { echo "invalid_image"; exit; }
        if (!move_uploaded_file($_FILES['edit_combo_img']['tmp_name'], $location)) { echo "upload_error"; exit; }

        // xóa ảnh cũ
        if ($old_image != '' && file_exists('image/'.$old_image)) {
            unlink('image/'.$old_image);
        }
    } else {
        $image = $old_image;
    }

    $update = mysqli_query($conn,
        "UPDATE combos SET 
            combo_name='$name',
            price='$price',
            description='$description',
            image='$image',
            status='$is_active'
        WHERE id='$id'"
    );

   
    if($update){
        header("Location: food_orders.php");  
        exit;
    } else {
        echo "Cập nhật combos thất bại: ".mysqli_error($conn);
        exit;
    }
}
// ============================
// XÓA COMBO
// ============================
if (isset($_POST['delete_combo'])) {

    $id = intval($_POST['id']);

    // xóa ảnh
    $q = mysqli_query($conn, "SELECT image FROM combos WHERE id='$id'");
    if ($r = mysqli_fetch_assoc($q)) {
        $img = $r['image'];
        if ($img != '' && file_exists('image/'.$img)) {
            unlink('image/'.$img);
        }
    }

    $delete = mysqli_query($conn, "DELETE FROM combos WHERE id='$id'");
   
    if($delete){
        header("Location: food_orders.php");  
        exit;
    } else {
        echo "Xoá combos thất bại: ".mysqli_error($conn);
        exit;
    }
}

?>
