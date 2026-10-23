<?php
session_start();
if (!isset($_SESSION['uid'])) {
    header("Location: login.php");
    exit();
}

// ถ้าไม่มีการส่งข้อมูลหนังสือมาจากตะกร้า ให้เด้งกลับไปหน้า Basket
if ($_SERVER["REQUEST_METHOD"] != "POST" || empty($_POST['selected_books'])) {
    header("Location: Basket.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "ebook");
$conn->set_charset("utf8mb4");

$selected_books = $_POST['selected_books'];
$total_price = 0;
$book_summary_html = "";

// ดึงข้อมูลหนังสือที่เลือกมาคำนวณราคา
foreach ($selected_books as $bid) {
    $stmt = $conn->prepare("SELECT bname, price FROM books WHERE bid = ?");
    $stmt->bind_param("i", $bid);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($row = $res->fetch_assoc()) {
        $total_price += $row['price'];
        $book_summary_html .= "<div class='summary-row'>
                                <span>" . htmlspecialchars($row['bname']) . "</span>
                                <span>" . number_format($row['price'], 2) . "B</span>
                               </div>";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirm Payment - Onelight Ebook</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

    <!-- แถบด้านบน -->
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
        <!-- เมนูด้านซ้าย -->
        <div class="sidebar">
            <a href="index.php"><i class="fa-solid fa-house active"></i></a>
            <a href="library.php"><i class="fa-solid fa-bookmark"></i></a>
            <a href="profile.php"><i class="fa-solid fa-user"></i></a>
        </div>

        <div class="content">
            <div class="basket-title-area">
                <a href="Basket.php" class="back-arrow"><i class="fa-solid fa-arrow-left"></i></a>
                <h2>Your Basket</h2>
            </div>

            <!-- Stepper -->
            <div class="stepper-container">
                <div class="step">
                    <div class="step-circle">1</div>
                    <div class="step-label">Basket</div>
                </div>
                <i class="fa-solid fa-arrow-right step-arrow"></i>
                <div class="step active">
                    <div class="step-circle">2</div>
                    <div class="step-label">Confirm Payment</div>
                </div>
                <i class="fa-solid fa-arrow-right step-arrow"></i>
                <div class="step">
                    <div class="step-circle">3</div>
                    <div class="step-label">Completed</div>
                </div>
            </div>

            <div class="payment-card">
                <a href="Basket.php" class="payment-back-btn"><i class="fa-solid fa-arrow-left"></i></a>

                <!-- สรุปราคาที่ดึงจากฐานข้อมูล -->
                <div class="payment-summary">
                    <?php echo $book_summary_html; ?>
                    <div class="summary-total" style="margin-top: 15px; border-top: 1px solid #ddd; padding-top: 10px; font-weight: bold;">
                        <span>Total price:</span>
                        <span><?php echo number_format($total_price, 2); ?>B</span>
                    </div>
                </div>

                <!-- ฟอร์มชำระเงิน (ส่งไปที่ completed.php) -->
                <form action="completed.php" method="POST" id="paymentForm" class="payment-form">
                    
                    <!-- ซ่อนรหัสหนังสือที่เลือกไว้ เพื่อส่งต่อให้หน้า completed จัดการลงฐานข้อมูล -->
                    <?php foreach ($selected_books as $bid): ?>
                        <input type="hidden" name="selected_books[]" value="<?php echo $bid; ?>">
                    <?php endforeach; ?>

                    <div class="form-group">
                        <label>Payment Methods</label>
                        <select id="cardType" class="payment-select" required>
                            <option value="Galaxy Card">Galaxy Card</option>
                            <option value="Cosmos Card">Cosmos Card</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Card Number (15 digits)</label>
                        <!-- บังคับเลข 15 หลัก -->
                        <input type="text" class="payment-input" pattern="\d{15}" maxlength="15" title="Please enter a 15-digit card number" required>
                    </div>

                    <div class="form-group">
                        <label>cvv code (3 digits)</label>
                        <!-- บังคับเลข 3 หลัก -->
                        <input type="password" class="payment-input" pattern="\d{3}" maxlength="3" title="Please enter the 3-digit CVV" required>
                    </div>

                    <div class="form-group">
                        <label>Expiration date (MM/YY)</label>
                        <!-- เพิ่ม id="expDate" และ maxlength="5" เข้าไป -->
                        <input type="text" id="expDate" class="payment-input" placeholder="MM/YY" pattern="(0[1-9]|1[0-2])\/\d{2}" maxlength="5" title="example: 12/25" required>
                    </div>

                    <div class="payment-btn-wrapper">
                        <button type="submit" class="btn-confirm-payment">Confirm Payment</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // ตรวจสอบชนิดบัตรก่อน Submit ฟอร์ม
        document.getElementById('paymentForm').addEventListener('submit', function(e) {
            const cardType = document.getElementById('cardType').value;
            if (cardType === 'Galaxy Card') {
                e.preventDefault(); // ยกเลิกการส่งข้อมูล
                alert('Payment rejected: Galaxy Card cannot be used. Please switch to Cosmos Card');
            }
        });

        // ระบบเติมเครื่องหมาย / อัตโนมัติในช่อง Expiration date
        const expDateInput = document.getElementById('expDate');
        expDateInput.addEventListener('input', function(e) {
            // ถ้าผู้ใช้กดปุ่มลบ (Backspace) ให้ปล่อยผ่านไป ไม่ต้องทำงาน
            if (e.inputType === 'deleteContentBackward') return;

            // ลบทุกอย่างที่ไม่ใช่ตัวเลขออกไปก่อน
            let v = e.target.value.replace(/\D/g, ''); 
    
            // ถ้าตัวเลขมีมากกว่า 2 ตัว ให้แทรก '/' ตรงกลาง
            if (v.length > 2) {
                v = v.substring(0, 2) + '/' + v.substring(2, 4);
            }
    
            // ส่งค่าที่จัดรูปแบบแล้วกลับไปที่ช่องกรอก
            e.target.value = v;
        });
    </script>
</body>
</html>