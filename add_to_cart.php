<?php
session_start();

// 1. ตรวจสอบว่าผู้ใช้ล็อกอินหรือยัง (ถ้ายังให้เด้งไปหน้า login)
if (!isset($_SESSION['uid'])) {
    echo "<script>
            alert('Please log in before placing an order.');
            window.location.href = 'login.php';
          </script>";
    exit();
}

$conn = new mysqli("localhost", "root", "", "ebook");
$conn->set_charset("utf8mb4");

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// 2. รับค่าที่ส่งมาจากปุ่ม (bid = รหัสหนังสือ, action = คำสั่งปุ่ม)
if (isset($_GET['bid']) && isset($_GET['action'])) {
    $uid = $_SESSION['uid'];
    $bid = intval($_GET['bid']);
    $action = $_GET['action'];

    // 3. เช็คว่ามีหนังสือเล่มนี้ในตะกร้าของตัวเองแล้วหรือยัง (Ebook ปกติซื้อซ้ำไม่ได้)
    $check_sql = "SELECT * FROM cart WHERE uid = ? AND bid = ?";
    $stmt_check = $conn->prepare($check_sql);
    $stmt_check->bind_param("ii", $uid, $bid);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows == 0) {
        // ถ้ายังไม่มี ให้บันทึกลงตาราง cart
        $insert_sql = "INSERT INTO cart (uid, bid) VALUES (?, ?)";
        $stmt_insert = $conn->prepare($insert_sql);
        $stmt_insert->bind_param("ii", $uid, $bid);
        $stmt_insert->execute();
        $stmt_insert->close();
    }
    $stmt_check->close();

    // 4. แยกการทำงานตามปุ่มที่ผู้ใช้กด
    if ($action == 'buy_now') {
        // ถ้ากดปุ่ม Buy Now ให้พาไปหน้าตะกร้าสินค้าทันที 
        header("Location: Basket.php"); 
        exit();
    } else {
        // ถ้ากดปุ่มเพิ่มลงตะกร้า (add_only) ให้เด้งเตือนว่าเพิ่มแล้ว และกลับไปหน้า Details เดิม
        echo "<script>
                alert('The book has been added to your cart.!');
                window.location.href = 'Details.php?id=" . $bid . "';
              </script>";
        exit();
    }

} else {
    // ถ้ามีการเข้าไฟล์นี้ตรงๆ โดยไม่ได้กดปุ่ม ให้เด้งกลับหน้าแรก
    header("Location: index.php");
    exit();
}

$conn->close();
?>