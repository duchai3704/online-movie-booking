<?php
session_start();
if (!isset($_SESSION['uname'])) {
    header("location:login_form.php");
    exit();
}

include "Database.php";

// Lấy ID vé từ URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<script>alert('Không tìm thấy vé!'); window.location='user.php';</script>";
    exit();
}

$ticket_id = intval($_GET['id']);
$username = $_SESSION['uname'];

// Lấy thông tin vé và kiểm tra quyền truy cập
$result = mysqli_query($conn, "
    SELECT c.id, c.custemer_id, c.movie, c.booking_date, c.show_time, c.seat, c.totalseat, c.price, c.payment_date, c.combo,
           u.username, u.email, u.mobile, u.city, t.theater
    FROM customers c
    INNER JOIN user u ON c.uid = u.id
    INNER JOIN theater_show t ON c.show_time = t.show
    WHERE c.id = $ticket_id AND u.username = '$username'
    LIMIT 1
");

// Kiểm tra vé có tồn tại và thuộc về user này không
if(mysqli_num_rows($result) == 0) {
    echo "<script>alert('Không tìm thấy vé hoặc bạn không có quyền truy cập!'); window.location='user.php';</script>";
    exit();
}

$row = mysqli_fetch_assoc($result);
$booking_id = $row['id'];

// Chuyển đổi định dạng ngày Việt Nam
$booking_date_vn = date("d-m-Y H:i", strtotime($row['booking_date']));
$payment_date_vn = date("d-m-Y H:i", strtotime($row['payment_date']));

// Lấy chi tiết combos từ bảng booking_combos
$combo_details = [];
$combo_total = 0;
$seat_price = $row['price'];

if (!empty($row['combo'])) {
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
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 40px 20px;
        }
        
        .ticket-wrapper {
            max-width: 900px;
            margin: 0 auto;
            perspective: 1000px;
        }
        
        .ticket {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
            position: relative;
            animation: fadeIn 0.6s ease-out;
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .ticket-header {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            color: white;
            padding: 40px 50px;
            position: relative;
            overflow: hidden;
        }
        
        .ticket-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            animation: shimmer 3s infinite;
        }
        
        @keyframes shimmer {
            0%, 100% { transform: translate(-50%, -50%) scale(1); }
            50% { transform: translate(-30%, -30%) scale(1.1); }
        }
        
        .movie-title {
            font-size: 36px;
            font-weight: 800;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 2px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
        
        .ticket-id {
            font-size: 14px;
            opacity: 0.9;
            font-weight: 300;
        }
        
        .ticket-body {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            padding: 40px 50px;
        }
        
        .ticket-section {
            position: relative;
        }
        
        .section-title {
            font-size: 14px;
            font-weight: 700;
            text-transform: uppercase;
            color: #667eea;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 3px solid #667eea;
            letter-spacing: 1px;
        }
        
        .info-item {
            margin-bottom: 18px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .info-label {
            font-size: 13px;
            color: #666;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .info-value {
            font-size: 16px;
            color: #222;
            font-weight: 600;
        }
        
        .seat-display {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 12px;
            text-align: center;
            margin: 20px 0;
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
        }
        
        .seat-numbers {
            font-size: 28px;
            font-weight: 700;
            letter-spacing: 2px;
        }
        
        .combo-section {
            grid-column: 1 / -1;
            background: linear-gradient(135deg, #fff8e1 0%, #fff3cd 100%);
            padding: 25px;
            border-radius: 12px;
            border: 2px solid #ffc107;
            margin-top: 10px;
        }
        
        .combo-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px dashed #ffb300;
        }
        
        .combo-item:last-child {
            border-bottom: none;
        }
        
        .combo-name {
            font-weight: 600;
            color: #f57c00;
            font-size: 15px;
        }
        
        .combo-price {
            color: #e65100;
            font-weight: 700;
            font-size: 16px;
        }
        
        .price-summary {
            grid-column: 1 / -1;
            background: linear-gradient(135deg, #f5f5f5 0%, #e8e8e8 100%);
            padding: 25px;
            border-radius: 12px;
            margin-top: 10px;
        }
        
        .price-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            font-size: 16px;
        }
        
        .price-row.total {
            border-top: 3px solid #667eea;
            padding-top: 15px;
            margin-top: 15px;
            font-size: 20px;
            font-weight: 700;
            color: #1e3c72;
        }
        
        .total-amount {
            color: #e91e63;
            font-size: 28px;
        }
        
        .ticket-footer {
            background: #f8f9fa;
            padding: 30px;
            text-align: center;
            border-top: 2px dashed #ddd;
        }
        
        .qr-placeholder {
            width: 120px;
            height: 120px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            margin: 0 auto 15px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 14px;
            font-weight: 600;
        }
        
        .action-buttons {
            margin-top: 20px;
        }
        
        .btn-custom {
            padding: 12px 35px;
            border-radius: 25px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 0 10px;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-print {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
        }
        
        .btn-print:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
            color: white;
        }
        
        .btn-back {
            background: white;
            color: #667eea;
            border: 2px solid #667eea;
        }
        
        .btn-back:hover {
            background: #667eea;
            color: white;
        }
        
        .perforated-line {
            position: relative;
            height: 2px;
            background: linear-gradient(to right, #ddd 50%, transparent 50%);
            background-size: 20px 2px;
            margin: 30px 0;
        }
        
        @media print {
            body {
                background: white;
                padding: 0;
            }
            .action-buttons, .btn-custom {
                display: none !important;
            }
            .ticket {
                box-shadow: none;
            }
        }
        
        @media (max-width: 768px) {
            .ticket-body {
                grid-template-columns: 1fr;
                gap: 20px;
                padding: 30px 25px;
            }
            .ticket-header {
                padding: 30px 25px;
            }
            .movie-title {
                font-size: 24px;
            }
            .combo-section, .price-summary {
                grid-column: 1;
            }
            .btn-custom {
                padding: 10px 20px;
                font-size: 14px;
                margin: 5px;
            }
        }
    </style>
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
                        <span class="info-value" style="font-size: 13px;"><?php echo $payment_date_vn;?></span>
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
                    <a href="user.php" class="btn-custom btn-back">
                        ⬅️ Quay Lại
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>