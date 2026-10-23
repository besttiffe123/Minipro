<?php
session_start();

if (!isset($_SESSION['uid'])) {
    header("Location: login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "ebook");
$conn->set_charset("utf8mb4");

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// เปลี่ยนมารับค่า cid แทน
if (isset($_GET['cid'])) {
    $cid = intval($_GET['cid']);
    $uid = $_SESSION['uid']; 

    // เปลี่ยนคำสั่งลบให้ใช้คอลัมน์ cid
    $sql = "DELETE FROM cart WHERE cid = ? AND uid = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $cid, $uid);

    if ($stmt->execute()) {
        header("Location: Basket.php");
        exit();
    } else {
        echo "<script>
                alert('An error occurred while deleting the product.');
                window.location.href = 'Basket.php';
              </script>";
    }

    $stmt->close();
} else {
    header("Location: Basket.php");
    exit();
}

$conn->close();
?>