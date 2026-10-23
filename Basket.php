<?php
session_start();
// ตรวจสอบว่าล็อกอินหรือยัง
if (!isset($_SESSION['uid'])) {
    header("Location: login.php");
    exit(); 
}

$conn = new mysqli("localhost", "root", "", "ebook");
$conn->set_charset("utf8mb4");

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$uid = $_SESSION['uid'];

// ดึงข้อมูลหนังสือที่อยู่ในตะกร้าของผู้ใช้คนนี้ โดยเชื่อมตาราง cart กับตาราง books
$sql = "SELECT c.cid, b.bid, b.bname, b.price, b.bcover 
        FROM cart c 
        JOIN books b ON c.bid = b.bid 
        WHERE c.uid = ? 
        ORDER BY c.cid DESC";
        
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $uid);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Basket - Onelight Ebook</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

    <!-- แถบด้านบน (ละไว้ให้เหมือนเดิม เพื่อความกระชับ) -->
    <div class="topbar">
        <!-- ... โค้ด topbar ของคุณเหมือนเดิม ... -->
        <div class="logo-area">
            <img src="pic/logo.png" alt="Logo" style="width: 50px; height: 50px; margin-right: 10px;">
            <div>
                <h1>Onelight</h1>
                <p>Ebook online shop</p>
            </div>
        </div>
        <div class="search-container">
            <div class="search-bar">
                <i class="fa-solid fa-magnifying-glass" style="color: #8b1c85;"></i>
                <input type="text" placeholder="Search">
                <i class="fa-solid fa-circle-chevron-down" id="dropdownIcon" style="color: #8b1c85; cursor: pointer;"></i>
            </div>
            <div class="search-dropdown" id="searchDropdown">
                <!-- ... โค้ดเมนู dropdown เหมือนเดิม ... -->
            </div>    
        </div>
    </div>

    <!-- เนื้อหาหลัก -->
    <div class="main-container">
        <!-- เมนูด้านซ้าย -->
        <div class="sidebar">
            <a href="index.php">
                <i class="fa-solid fa-house active"></i>
            </a>
            <a href="library.php">
                <i class="fa-solid fa-bookmark"></i>
            </a>
            <a href="profile.php">
                <i class="fa-solid fa-user"></i>
            </a>
        </div>

        <div class="content">
            <!-- หัวข้อหน้าตะกร้า -->
            <div class="basket-title-area">
                <a href="index.php" class="back-arrow"><i class="fa-solid fa-arrow-left"></i></a>
                <h2>Your Basket</h2>
            </div>

            <!-- แถบแสดงขั้นตอน (Stepper) -->
            <div class="stepper-container">
                <!-- ... โค้ด stepper เหมือนเดิม ... -->
            </div>

            <!-- เริ่มฟอร์มเพื่อส่งรายการที่เลือกไปหน้าชำระเงิน -->
            <form action="payment.php" method="POST" id="cartForm">
                
                <div class="cart-items-wrapper">
                    <?php if ($result->num_rows > 0): ?>
                        
                        <div class="select-all-box">
                            <input type="checkbox" id="selectAll">
                            <label for="selectAll">Select all</label>
                        </div>

                        <?php 
                        $total_price_initial = 0; // ตัวแปรรวมราคาเริ่มต้น
                        while($row = $result->fetch_assoc()): 
                        ?>
                            <!-- การ์ดรายการหนังสือในตะกร้าแต่ละเล่ม -->
                            <div class="cart-item">
                                <!-- Checkbox สำหรับส่งรหัสหนังสือ (bid) ที่เลือกไปหน้าถัดไป -->
                                <input type="checkbox" class="item-checkbox" name="selected_books[]" value="<?php echo $row['bid']; ?>" data-price="<?php echo $row['price']; ?>">
                                
                                <!-- รูปปกหนังสือ -->
                                <div class="cart-cover" style="background-image: url('admin/uploads/<?php echo $row['bcover']; ?>'); background-size: cover; background-position: center;"></div>
                                
                                <div class="cart-item-info">
                                    <div class="cart-item-title"><?php echo htmlspecialchars($row['bname']); ?></div>
                                    <div class="cart-item-price"><?php echo number_format($row['price'], 2); ?>B</div>
                                </div>
                                
                                <!-- ลิงก์ไปไฟล์ลบสินค้าออกจากตะกร้า -->
                                <a href="remove_from_cart.php?cid=<?php echo $row['cid']; ?>" onclick="return confirm('ต้องการลบหนังสือเล่มนี้ออกจากตะกร้าใช่หรือไม่?');" style="color: inherit;">
                                    <button type="button" class="btn-delete-item">
                                        <i class="fa-regular fa-trash-can"></i>
                                    </button>
                                </a>
                            </div>
                        <?php endwhile; ?>
                    
                    <?php else: ?>
                        <div style="text-align: center; padding: 50px;">
                            <h3 style="color: #666;">No items in the cart</h3>
                            <a href="index.php" style="color: #8b1c85; text-decoration: underline;">กลับไปเลือกซื้อหนังสือ</a>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- ส่วนสรุปราคารวมและปุ่ม Pay Now -->
                <?php if ($result->num_rows > 0): ?>
                <div class="cart-summary-bar">
                    <div class="total-price-text">
                        Total price: <span id="totalPriceDisplay">0.00B</span>
                    </div>
                    <!-- ปุ่มนี้จะทำงาน Submit ฟอร์ม ไปที่ payment.php -->
                    <button type="submit" class="btn-pay-now" id="btnPayNow" disabled style="opacity: 0.5;">Pay Now</button>
                </div>
                <?php endif; ?>
                
            </form>
        </div>
    </div>

    <script>
    // ส่วน Dropdown (เหมือนเดิม)
    // ...

    // 1. ระบบคำนวณราคาแบบ Real-time ตาม Checkbox ที่เลือก
    const selectAll = document.getElementById('selectAll');
    const itemCheckboxes = document.querySelectorAll('.item-checkbox');
    const totalPriceDisplay = document.getElementById('totalPriceDisplay');
    const btnPayNow = document.getElementById('btnPayNow');

    function calculateTotal() {
        let total = 0;
        let checkedCount = 0;
        
        itemCheckboxes.forEach(checkbox => {
            if (checkbox.checked) {
                // ดึงค่าราคาที่แอบซ่อนไว้ใน data-price ของ HTML
                total += parseFloat(checkbox.getAttribute('data-price'));
                checkedCount++;
            }
        });

        // แสดงราคารวม
        totalPriceDisplay.textContent = total.toFixed(2) + 'B';
        
        // ถ้าไม่ได้ติ๊กเลือกสินค้าเลย ให้ปุ่ม Pay Now กดไม่ได้ (disabled)
        if (checkedCount > 0) {
            btnPayNow.disabled = false;
            btnPayNow.style.opacity = '1';
        } else {
            btnPayNow.disabled = true;
            btnPayNow.style.opacity = '0.5';
        }
    }

    if (selectAll) {
        selectAll.addEventListener('change', function () {
            itemCheckboxes.forEach(checkbox => {
                checkbox.checked = selectAll.checked;
            });
            calculateTotal();
        });

        itemCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function () {
                const allChecked = Array.from(itemCheckboxes).every(cb => cb.checked);
                selectAll.checked = allChecked;
                calculateTotal();
            });
        });
    }
    </script>
</body>
</html>
<?php
$stmt->close();
$conn->close();
?>