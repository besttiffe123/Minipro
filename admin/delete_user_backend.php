<?php
// เชื่อมต่อฐานข้อมูล
$conn = new mysqli("localhost", "root", "", "ebook");
$conn->set_charset("utf8mb4");

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// ตรวจสอบว่ามีการส่งค่า id มาทาง URL หรือไม่
if (isset($_GET['id'])) {
    $uid = $_GET['id'];

    // 1. ลบข้อมูลใน order_details ที่ผูกกับบิลออเดอร์ของ User คนนี้ก่อน
    $sql1 = "DELETE FROM order_details WHERE oid IN (SELECT oid FROM eorder WHERE uid = ?)";
    $stmt1 = $conn->prepare($sql1);
    $stmt1->bind_param("i", $uid);
    $stmt1->execute();
    $stmt1->close();

    // 2. ลบข้อมูลบิลในตาราง eorder ของ User คนนี้
    $sql2 = "DELETE FROM eorder WHERE uid = ?";
    $stmt2 = $conn->prepare($sql2);
    $stmt2->bind_param("i", $uid);
    $stmt2->execute();
    $stmt2->close();

    // 3. เตรียมคำสั่ง SQL (ใช้ ? เพื่อป้องกัน SQL Injection) เพื่อลบข้อมูลผู้ใช้
    $stmt3 = $conn->prepare("DELETE FROM euser WHERE uid = ?");
    // "i" หมายถึงตัวแปรประเภท Integer (ตัวเลข)
    $stmt3->bind_param("i", $uid);

    // ทำงานคำสั่ง SQL
    if ($stmt3->execute()) {
        // ลบสำเร็จ ให้เด้งกลับไปหน้าตารางผู้ใช้
        echo "<script>
                alert('User data and related orders have been deleted');
                window.location.href = 'Foradmin.php'; 
              </script>";
    } else {
        echo "Error occurred while deleting data: " . $stmt3->error;
    }

    $stmt3->close();
} else {
    // กรณีไม่มีการส่งค่า id มา ให้เด้งกลับไปหน้าแอดมินเลย
    header("Location: Foradmin.php");
    exit();
}

$conn->close();
?>