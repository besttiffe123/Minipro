<?php
session_start();
if (!isset($_SESSION['uid'])) {
    header("Location: login.php");
    exit();
}

// ป้องกันคนเข้าหน้านี้ตรงๆ โดยไม่ได้กดจ่ายเงิน
if ($_SERVER["REQUEST_METHOD"] != "POST" || empty($_POST['selected_books'])) {
    header("Location: index.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "ebook");
$conn->set_charset("utf8mb4");

$uid = $_SESSION['uid'];
$selected_books = $_POST['selected_books'];
$total_price = 0;
$items = [];

// 1. คำนวณราคารวมอีกครั้งเพื่อความปลอดภัย
foreach ($selected_books as $bid) {
    $stmt = $conn->prepare("SELECT price FROM books WHERE bid = ?");
    $stmt->bind_param("i", $bid);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($row = $res->fetch_assoc()) {
        $total_price += $row['price'];
        $items[] = ['bid' => $bid, 'price' => $row['price']];
    }
    $stmt->close();
}

// 2. สร้าง Order ใหม่ในตาราง eorder (สถานะ paid)
$pstatus = 'paid';
$stmt_order = $conn->prepare("INSERT INTO eorder (uid, tprice, pstatus) VALUES (?, ?, ?)");
$stmt_order->bind_param("ids", $uid, $total_price, $pstatus);
$stmt_order->execute();
$new_oid = $conn->insert_id; // ได้รับรหัสออเดอร์ใหม่
$stmt_order->close();

// 3. บันทึกรายละเอียดลง order_details และลบหนังสือออกจากตะกร้า
$stmt_detail = $conn->prepare("INSERT INTO order_details (oid, bid, price) VALUES (?, ?, ?)");
$stmt_cart_del = $conn->prepare("DELETE FROM cart WHERE uid = ? AND bid = ?");

foreach ($items as $item) {
    // ลง order_details
    $stmt_detail->bind_param("iid", $new_oid, $item['bid'], $item['price']);
    $stmt_detail->execute();
    
    // ลบออกจากตะกร้า
    $stmt_cart_del->bind_param("ii", $uid, $item['bid']);
    $stmt_cart_del->execute();
}

$stmt_detail->close();
$stmt_cart_del->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirm - Onelight Ebook</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="topbar">
        <div class="logo-area">
            <img src="pic/logo.png" alt="Logo" style="width: 50px; height: 50px; margin-right: 10px;">
            <div>
                <h1>Onelight</h1>
                <p>Ebook online shop</p>
            </div>
        </div>
    </div>

    <div class="main-container">
        <div class="sidebar">
            <a href="index.php"><i class="fa-solid fa-house active"></i></a>
            <a href="library.php"><i class="fa-solid fa-bookmark"></i></a>
            <a href="profile.php"><i class="fa-solid fa-user"></i></a>
        </div>

        <div class="content">
            <div class="basket-title-area">
                <a href="index.php" class="back-arrow"><i class="fa-solid fa-arrow-left"></i></a>
                <h2>Your Basket</h2>
            </div>

            <div class="stepper-container">
                <div class="step">
                    <div class="step-circle">1</div>
                    <div class="step-label">Basket</div>
                </div>
                <i class="fa-solid fa-arrow-right step-arrow"></i>
                <div class="step">
                    <div class="step-circle">2</div>
                    <div class="step-label">Confirm Payment</div>
                </div>
                <i class="fa-solid fa-arrow-right step-arrow"></i>
                <div class="step active">
                    <div class="step-circle">3</div>
                    <div class="step-label">Completed</div>
                </div>
            </div>

            <div class="completed-card">
                <a href="index.php" class="payment-back-btn"><i class="fa-solid fa-arrow-left"></i></a>

                <div class="success-circle">
                    <i class="fa-solid fa-check"></i>
                </div>

                <div class="completed-title">Payment successful</div>
                <div class="completed-sub">Your payment has been successfully done</div>
                <div class="completed-sub">Order ID: #<?php echo $new_oid; ?></div>
                <div class="completed-sub">Thank you for using our service.</div>

                <div class="completed-actions" style="margin-top: 20px;">
                    <a href="index.php" class="btn-completed-outline">Continue to Home</a>
                    <a href="library.php" class="btn-completed-outline" style="background-color: #8b1c85; color: white;">Go to Library</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>