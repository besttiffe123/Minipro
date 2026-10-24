<?php
// เชื่อมต่อฐานข้อมูล
$conn = new mysqli("localhost", "root", "", "ebook");
$conn->set_charset("utf8mb4");

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

if (isset($_GET['id'])) {
    $bid = $_GET['id'];

    // 1. ดึงชื่อไฟล์รูปและไฟล์หนังสือเพื่อนำไปลบออกจากโฟลเดอร์ uploads
    $sql_file = "SELECT bcover, bfile FROM books WHERE bid = ?";
    $stmt_file = $conn->prepare($sql_file);
    $stmt_file->bind_param("i", $bid);
    $stmt_file->execute();
    $result_file = $stmt_file->get_result();
    
    if ($row = $result_file->fetch_assoc()) {
        if (!empty($row['bcover']) && file_exists("uploads/" . $row['bcover'])) {
            unlink("uploads/" . $row['bcover']);
        }
        if (!empty($row['bfile']) && file_exists("uploads/" . $row['bfile'])) {
            unlink("uploads/" . $row['bfile']);
        }
    }
    $stmt_file->close();

    // 2. ลบข้อมูลการเชื่อมโยงหมวดหมู่ในตาราง book_genres
    $stmt_genres = $conn->prepare("DELETE FROM book_genres WHERE bid = ?");
    $stmt_genres->bind_param("i", $bid);
    $stmt_genres->execute();
    $stmt_genres->close();

    // 3. ลบข้อมูลหนังสือเล่มนี้ออกจากประวัติการสั่งซื้อ (order_details) เพื่อแก้ปัญหา Foreign Key
    $stmt_orders = $conn->prepare("DELETE FROM order_details WHERE bid = ?");
    $stmt_orders->bind_param("i", $bid);
    $stmt_orders->execute();
    $stmt_orders->close();

    // 4. ลบข้อมูลหนังสือจากตาราง books
    $stmt = $conn->prepare("DELETE FROM books WHERE bid = ?");
    $stmt->bind_param("i", $bid);

    if ($stmt->execute()) {
        echo "<script>
                alert('The book has been deleted');
                window.location.href = 'Foradmin.php'; 
              </script>";
    } else {
        echo "An error occurred during deletion: " . $stmt->error;
    }
    
    $stmt->close();
} else {
    header("Location: Foradmin.php");
    exit();
}

$conn->close();
?>