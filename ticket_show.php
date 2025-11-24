<?php
session_start();
if (!isset($_SESSION['uname']) || !isset($_SESSION['custemer_id'])) {
    header("location:index.php");
    exit();
}

include "Database.php";

$custemer_id = $_SESSION['custemer_id'];
$result = mysqli_query($conn, "
    SELECT c.id, c.custemer_id, c.movie, c.booking_date, c.show_time, c.seat, c.totalseat, c.price, c.payment_date, c.combo,
           u.username, u.email, u.mobile, u.city, t.theater
    FROM customers c
    INNER JOIN user u ON c.uid = u.id
    INNER JOIN theater_show t ON c.show_time = t.show
    WHERE c.custemer_id = '$custemer_id'
    ORDER BY c.payment_date DESC
    LIMIT 1
");

$row = mysqli_fetch_assoc($result);
$booking_id = $row['id'];

// Chuyển đổi định dạng ngày Việt Nam
$booking_date_vn = date("d-m-Y H:i", strtotime($row['booking_date']));
$payment_date_vn = date("d-m-Y H:i", strtotime($row['payment_date']));

// Lấy chi tiết combos từ bảng booking_combos
$combo_details = [];
$combo_total = 0;
$seat_price = $row['price']; // Giá gốc bao gồm cả combo

if (!empty($row['combo'])) {
    // Lấy thông tin chi tiết combo với số lượng
    $combo_query = mysqli_query($conn, "
        SELECT bc.quantity, c.combo_name, c.price
        FROM booking_combos bc
        INNER JOIN combos c ON bc.combo_id = c.id
        WHERE bc.booking_id = $booking_id
    ");
    
    while ($combo = mysqli_fetch_assoc($combo_query)) {
        $subtotal = $combo['price'] * $combo['quantity'];
        $combo_total += $subtotal;
        $combo_details[] = [
            'name' => $combo['combo_name'],
            'price' => $combo['price'],
            'quantity' => $combo['quantity'],
            'subtotal' => $subtotal
        ];
    }
    
    // Tính tiền ghế = tổng tiền - tiền combo
    $seat_price = $row['price'] - $combo_total;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <title>Vé Xem Phim - <?php echo $row['movie'];?></title>
    <link href='https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css' rel='stylesheet'>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="./css/styles_ticket.css">
</head>
<body>
    <div class="ticket-wrapper">
        <div class="ticket">
            <!-- Header -->
            <div class="ticket-header">
                <div class="movie-title"><?php echo strtoupper($row['movie']);?></div>
                <div class="ticket-id">🎫 Mã vé: #<?php echo str_pad($row['custemer_id'], 8, '0', STR_PAD_LEFT); ?></div>
            </div>
            
            <!-- Body -->
            <div class="ticket-body">
                <!-- Left Column -->
                <div class="ticket-section">
                    <div class="section-title">📅 Thông Tin Suất Chiếu</div>
                    <div class="info-item">
                        <span class="info-label">Rạp</span>
                        <span class="info-value">Số <?php echo $row['theater'];?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Ngày</span>
                        <span class="info-value"><?php echo date("d/m/Y", strtotime($row['booking_date']));?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Giờ chiếu</span>
                        <span class="info-value"><?php echo $row['show_time'];?></span>
                    </div>
                    
                    <div class="seat-display">
                        <div style="font-size: 14px; margin-bottom: 8px; opacity: 0.9;">Ghế của bạn</div>
                        <div class="seat-numbers"><?php echo str_replace(',', ' • ', $row['seat']);?></div>
                        <div style="font-size: 13px; margin-top: 8px; opacity: 0.8;">Tổng: <?php echo $row['totalseat'];?> ghế</div>
                    </div>
                </div>
                
                <!-- Right Column -->
                <div class="ticket-section">
                    <div class="section-title">👤 Thông Tin Khách Hàng</div>
                    <div class="info-item">
                        <span class="info-label">Họ tên</span>
                        <span class="info-value"><?php echo $row['username'];?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Email</span>
                        <span class="info-value" style="font-size: 13px;"><?php echo $row['email'];?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Điện thoại</span>
                        <span class="info-value"><?php echo $row['mobile'];?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Thành phố</span>
                        <span class="info-value"><?php echo $row['city'];?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Thanh toán</span>
                        <span class="info-value"><?php echo $payment_date_vn;?></span>
                    </div>
                </div>
                
                <!-- Combo Section -->
                <?php if(count($combo_details) > 0): ?>
                <div class="combo-section">
                    <div class="section-title" style="color: #f57c00; border-color: #ff9800;">🍿 Combo Đồ Ăn & Nước Uống</div>
                    <?php foreach($combo_details as $combo): ?>
                    <div class="combo-item">
                        <div>
                            <div class="combo-name"><?php echo $combo['name']; ?></div>
                            <div style="font-size: 13px; color: #666; margin-top: 4px;">
                                <?php echo number_format($combo['price'],0,',','.'); ?> VNĐ × <?php echo $combo['quantity']; ?>
                            </div>
                        </div>
                        <div class="combo-price"><?php echo number_format($combo['subtotal'],0,',','.'); ?> VNĐ</div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
                
                <!-- Price Summary -->
                <div class="price-summary">
                    <div class="section-title" style="border-color: #1e3c72;">💰 Chi Tiết Thanh Toán</div>
                    <div class="price-row">
                        <span>Tiền vé (<?php echo $row['totalseat'];?> ghế)</span>
                        <span style="font-weight: 600;"><?php echo number_format($seat_price,0,',','.'); ?> VNĐ</span>
                    </div>
                    <?php if($combo_total > 0): ?>
                    <div class="price-row">
                        <span>Tiền combo</span>
                        <span style="font-weight: 600;"><?php echo number_format($combo_total,0,',','.'); ?> VNĐ</span>
                    </div>
                    <?php endif; ?>
                    <div class="price-row total">
                        <span>TỔNG CỘNG</span>
                        <span class="total-amount"><?php echo number_format($row['price'],0,',','.');?> VNĐ</span>
                    </div>
                </div>
            </div>
            
            <div class="perforated-line"></div>
            
            <!-- Footer -->
            <div class="ticket-footer">
                <div class="qr-placeholder">
                    QR CODE<br>CHECK-IN
                </div>
                <div style="font-size: 13px; color: #666; margin-bottom: 20px;">
                    Vui lòng xuất trình vé này khi vào rạp
                </div>
                <div class="action-buttons">
                    <button class="btn-custom btn-print" onclick="window.print()">
                        🖨️ In Vé
                    </button>
                    <a href="index.php" class="btn-custom btn-home">
                        🏠 Trang Chủ
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>