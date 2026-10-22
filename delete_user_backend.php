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

    // เตรียมคำสั่ง SQL (ใช้ ? เพื่อป้องกัน SQL Injection)
    $stmt = $conn->prepare("DELETE FROM euser WHERE uid = ?");
    // "i" หมายถึงตัวแปรประเภท Integer (ตัวเลข)
    $stmt->bind_param("i", $uid);

    // ทำงานคำสั่ง SQL
    if ($stmt->execute()) {
        // ลบสำเร็จ ให้เด้งกลับไปหน้าตารางผู้ใช้
        echo "<script>
                alert('User data has been deleted');
                window.location.href = 'foradmin.php'; 
              </script>";
    } else {
        echo "Error occurred while deleting data: " . $stmt->error;
    }

    $stmt->close();
} else {
    // กรณีไม่มีการส่งค่า id มา ให้เด้งกลับไปหน้าแอดมินเลย
    header("Location: Foradmin.php");
    exit();
}

$conn->close();
?>