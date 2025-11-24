<?php
session_start();
include_once 'Database.php';

// Kiểm tra login
if (!isset($_SESSION['uname'])) {
    header("Location: login_form.php");
    exit;
}

// Lấy thông tin user
$uname = $_SESSION['uname'];
$userResult = mysqli_query($conn, "SELECT * FROM user WHERE username='$uname'");
if (mysqli_num_rows($userResult) == 0) {
    echo "User not found!";
    exit;
}
$user = mysqli_fetch_assoc($userResult);
$user_id = $user['id'];

// ---- XỬ LÝ NẠP TIỀN TRƯỚC HTML ----
if (isset($_POST['recharge'])) {
    $bank_name = $_POST['bank_name'] ?? '';
    $account_number = trim($_POST['account_number'] ?? '');
    $amount = floatval($_POST['amount'] ?? 0);

    if ($bank_name == '') {
        $_SESSION['recharge_msg'] = ['type' => 'danger', 'text' => '❌ Vui lòng chọn ngân hàng!'];
    } elseif (!preg_match('/^[0-9]{8,20}$/', $account_number)) {
        $_SESSION['recharge_msg'] = ['type' => 'danger', 'text' => '❌ Số tài khoản không hợp lệ!'];
    } elseif ($amount <= 0) {
        $_SESSION['recharge_msg'] = ['type' => 'danger', 'text' => '❌ Số tiền phải lớn hơn 0!'];
    } else {
        // Lưu giao dịch
        mysqli_query($conn, "INSERT INTO transactions (user_id, bank_name, account_number, amount) 
                             VALUES ('$user_id', '$bank_name', '$account_number', '$amount')");
        
        // Cập nhật số dư
        $newBalance = $user['balance'] + $amount;
        mysqli_query($conn, "UPDATE user SET balance='$newBalance' WHERE id='$user_id'");
        $_SESSION['recharge_msg'] = ['type' => 'success', 'text' => "✅ Nạp thành công ".number_format($amount,0,',','.')." VNĐ từ ngân hàng $bank_name!"];
    }

    // Redirect sang GET để tránh reload POST
    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}

// ---- Lấy vé đã đặt ----
$ticketsResult = mysqli_query($conn, "
    SELECT c.*, m.movie_name 
    FROM customers c 
    LEFT JOIN add_movie m ON c.movie = m.movie_name 
    WHERE c.uid='$user_id'
    ORDER BY c.payment_date DESC
");

// ---- Lấy lịch sử nạp tiền ----
$limit = 5; 
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

$totalResult = mysqli_query($conn, "SELECT COUNT(*) as total FROM transactions WHERE user_id='$user_id'");
$totalRow = mysqli_fetch_assoc($totalResult);
$totalRecords = $totalRow['total'];
$totalPages = ceil($totalRecords / $limit);

$trans = mysqli_query($conn, "SELECT * FROM transactions WHERE user_id='$user_id' ORDER BY created_at DESC LIMIT $offset, $limit");
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>User Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .avatar { width: 120px; height: 120px; border-radius: 50%; object-fit: cover; border: 4px solid #667eea; }
        .ticket-table th, .ticket-table td { vertical-align: middle; }
        .btn-home { display: inline-block; padding: 10px 20px; background-color: #007bff; color: white; font-weight: bold; border-radius: 8px; text-decoration: none; transition: background-color 0.3s, transform 0.2s; }
        .btn-home:hover { color: white; background-color: #0056b3; transform: scale(1.05); }
        .btn-view-ticket { padding: 5px 15px; font-size: 14px; border-radius: 20px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; transition: all 0.3s ease; }
        .btn-view-ticket:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4); }
        .combo-badge { background: linear-gradient(135deg, #ffd89b 0%, #ff9a56 100%); color: white; padding: 3px 10px; border-radius: 12px; font-size: 12px; font-weight: 600; margin-right: 5px; display: inline-block; margin-bottom: 3px; }
        .profile-header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; border-radius: 15px; margin-bottom: 30px; box-shadow: 0 10px 30px rgba(0,0,0,0.2); }
        .balance-card { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); color: white; padding: 20px; border-radius: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        .stat-card { background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); text-align: center; margin-bottom: 20px; }
        .stat-number { font-size: 32px; font-weight: 700; color: #667eea; }
        .stat-label { color: #666; font-size: 14px; margin-top: 5px; }
    </style>
</head>
<body style="background: #f5f7fa;">
<div class="container mt-5">

    <!-- Profile Header -->
    <div class="profile-header">
        <div class="row align-items-center">
            <div class="col-md-2 text-center">
                <img src="<?php echo ($user['image'] == '') ? 'image/img_avatar.png' : 'admin/image/' . $user['image']; ?>" class="avatar" alt="Avatar">
            </div>
            <div class="col-md-6">
                <h2 style="margin-bottom: 10px;">👋 Xin chào, <?php echo $user['username']; ?>!</h2>
                <p><strong>📧 Email:</strong> <?php echo $user['email']; ?></p>
                <p><strong>📱 Mobile:</strong> <?php echo $user['mobile']; ?></p>
                <p><strong>🏙️ City:</strong> <?php echo $user['city']; ?></p>
            </div>
            <div class="col-md-4">
                <div class="balance-card">
                    <div style="font-size: 14px; opacity: 0.9;">Số dư tài khoản</div>
                    <div style="font-size: 36px; font-weight: 700; margin: 10px 0;">
                        <?php echo number_format($user['balance'], 0, ',', '.'); ?> VNĐ
                    </div>
                    <div style="font-size: 12px; opacity: 0.8;">💳 Có thể sử dụng</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-number"><?php echo mysqli_num_rows($ticketsResult); ?></div>
                <div class="stat-label">🎫 Tổng vé đã đặt</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <?php
                $total_spent_result = mysqli_query($conn, "SELECT SUM(price) as total FROM customers WHERE uid='$user_id'");
                $total_spent = mysqli_fetch_assoc($total_spent_result)['total'] ?? 0;
                ?>
                <div class="stat-number"><?php echo number_format($total_spent, 0, ',', '.'); ?></div>
                <div class="stat-label">💰 Tổng chi tiêu (VNĐ)</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <?php
                $trans_count = mysqli_query($conn, "SELECT COUNT(*) as total FROM transactions WHERE user_id='$user_id'");
                $trans_total = mysqli_fetch_assoc($trans_count)['total'];
                ?>
                <div class="stat-number"><?php echo $trans_total; ?></div>
                <div class="stat-label">📊 Giao dịch nạp tiền</div>
            </div>
        </div>
    </div>

    <!-- Vé đã đặt -->
    <div class="card shadow-sm mb-4">
        <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
            <h4 style="margin: 0;">🎬 Lịch Sử Đặt Vé</h4>
        </div>
        <div class="card-body">
            <table class="table table-hover ticket-table">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Phim</th>
                        <th>Suất chiếu</th>
                        <th>Ghế</th>
                        <th>Combo</th>
                        <th>Tổng tiền</th>
                        <th>Ngày đặt</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    mysqli_data_seek($ticketsResult, 0); 
                    if (mysqli_num_rows($ticketsResult) > 0) {
                        $i = 1;
                        while ($ticket = mysqli_fetch_assoc($ticketsResult)) {
                            $combo_display = '';
                            if(!empty($ticket['combo'])) {
                                $combo_query = mysqli_query($conn, "
                                    SELECT bc.quantity, c.combo_name 
                                    FROM booking_combos bc
                                    INNER JOIN combos c ON bc.combo_id = c.id
                                    WHERE bc.booking_id = {$ticket['id']}
                                ");
                                $combo_items = [];
                                while($combo = mysqli_fetch_assoc($combo_query)) {
                                    $combo_items[] = $combo['combo_name'] . ' x' . $combo['quantity'];
                                }
                                if(count($combo_items) > 0) {
                                    foreach($combo_items as $item) {
                                        $combo_display .= '<span class="combo-badge">🍿 ' . $item . '</span>';
                                    }
                                } else {
                                    $combo_display = '<small class="text-muted">Không có</small>';
                                }
                            } else {
                                $combo_display = '<small class="text-muted">Không có</small>';
                            }

                            echo "<tr>";
                            echo "<td><strong>{$i}</strong></td>";
                            echo "<td><strong>{$ticket['movie']}</strong></td>";
                            echo "<td><span class='badge bg-primary'>{$ticket['show_time']}</span></td>";
                            echo "<td><span class='badge bg-success'>{$ticket['seat']}</span> <small>({$ticket['totalseat']} ghế)</small></td>";
                            echo "<td>{$combo_display}</td>";
                            echo "<td><strong style='color: #e91e63;'>" . number_format($ticket['price'], 0, ',', '.') . " VNĐ</strong></td>";
                            echo "<td><small>" . date("d/m/Y H:i", strtotime($ticket['booking_date'])) . "</small></td>";
                            echo "<td><a href='view_ticket.php?id={$ticket['id']}' class='btn-view-ticket'>👁️ Xem vé</a></td>";
                            echo "</tr>";
                            $i++;
                        }
                    } else {
                        echo "<tr><td colspan='8' class='text-center py-4'>
                                <div style='color: #999;'><i style='font-size: 48px;'>🎫</i><p>Chưa có vé nào được đặt.</p></div>
                              </td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Form nạp tiền -->
    <div class="card mt-4 p-3 shadow-sm">
        <h5 class="mb-3">💳 Nạp tiền vào tài khoản</h5>
        <?php
        if (isset($_SESSION['recharge_msg'])) {
            $msg = $_SESSION['recharge_msg'];
            echo "<div class='alert alert-{$msg['type']}'>".$msg['text']."</div>";
            unset($_SESSION['recharge_msg']);
        }
        ?>
        <form method="post">
            <div class="row g-3">
                <div class="col-md-3">
                    <select name="bank_name" class="form-select" required>
                        <option value="">-- Chọn ngân hàng --</option>
                        <option value="Vietcombank">Vietcombank</option>
                        <option value="MB Bank">MB Bank</option>
                        <option value="Sacombank">Sacombank</option>
                        <option value="VietinBank">VietinBank</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <input type="text" name="account_number" class="form-control" placeholder="Nhập số tài khoản" required>
                </div>
                <div class="col-md-3">
                    <input type="number" name="amount" class="form-control" placeholder="Nhập số tiền cần nạp" required>
                </div>
                <div class="col-md-2">
                    <button type="submit" name="recharge" class="btn btn-success w-100">Nộp tiền</button>
                </div>
            </div>
        </form>
    </div>

    <!-- Lịch sử nạp tiền -->
    <div class="card shadow-sm mb-4 mt-3">
        <div class="card-header" style="background: #f8f9fa;">
            <h5 style="margin: 0; color: #333;">📄 Lịch Sử Nạp Tiền</h5>
        </div>
        <div class="card-body">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Ngân hàng</th>
                        <th>Số tài khoản</th>
                        <th>Số tiền (VNĐ)</th>
                        <th>Ngày nạp</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (mysqli_num_rows($trans) > 0) {
                        $i = $offset + 1;
                        while ($row = mysqli_fetch_assoc($trans)) {
                            $masked_account = substr($row['account_number'], 0, 4) . '****' . substr($row['account_number'], -4);
                            echo "<tr>
                                    <td><strong>{$i}</strong></td>
                                    <td><span class='badge bg-info'>{$row['bank_name']}</span></td>
                                    <td>{$masked_account}</td>
                                    <td><strong style='color: #11998e;'>+" . number_format($row['amount'], 0, ',', '.') . "</strong></td>
                                    <td><small>" . date("d/m/Y H:i", strtotime($row['created_at'])) . "</small></td>
                                  </tr>";
                            $i++;
                        }
                    } else {
                        echo "<tr><td colspan='5' class='text-center py-4'>
                                <div style='color: #999;'><i style='font-size: 48px;'>💳</i><p>Chưa có giao dịch nạp tiền nào</p></div>
                              </td></tr>";
                    }
                    ?>
                </tbody>
            </table>

            <?php if($totalPages > 1): ?>
            <nav>
                <ul class="pagination justify-content-center">
                    <?php
                    for ($p = 1; $p <= $totalPages; $p++) {
                        $active = ($p == $page) ? "active" : "";
                        echo "<li class='page-item $active'><a class='page-link' href='?page=$p'>$p</a></li>";
                    }
                    ?>
                </ul>
            </nav>
            <?php endif; ?>
        </div>
    </div>

    <div class="my-4 text-center">
        <a href="index.php" class="btn-home">🏠 Về Trang Chủ</a>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
