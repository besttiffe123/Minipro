<?php
$conn = new mysqli("localhost", "root", "", "ebook");
$conn->set_charset("utf8mb4");
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Onelight Ebook</title>
    <link rel="stylesheet" href="Foradminstyle.css">
</head>
<body>

    <!-- เมนู Tabs ด้านบน -->
    <div class="admin-tabs">
        <a href="Foradmin.php" class="admin-tab active">Book data</a>
        <a href="admin_users.php" class="admin-tab">User data</a>
        <a href="admin_order.php" class="admin-tab">Order data</a>
    </div>
    <div class="tab-divider"></div>

    <!-- กล่องข้อมูล -->
    <div class="data-box">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Book name</th>
                    <th>Author</th>
                    <th>type</th>
                    <th>pages</th>
                    <th>price</th>
                    <th>genres</th>
                    <th>About</th>
                    <th>cover</th>
                    <th>file</th>
                </tr>
            </thead>
<tbody>
    <?php
    // ดึงข้อมูลหนังสือทั้งหมด เรียงจากเล่มล่าสุดลงไป
    $sql = "SELECT b.*, GROUP_CONCAT(g.gname SEPARATOR ', ') AS genre_names 
            FROM books b
            LEFT JOIN book_genres bg ON b.bid = bg.bid
            LEFT JOIN genres g ON bg.gid = g.gid
            GROUP BY b.bid 
            ORDER BY b.bid DESC";

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
    ?>
        <tr>
            <td><?php echo htmlspecialchars($row['bname'] ?? '-'); ?></td>
            <td><?php echo htmlspecialchars($row['author'] ?? '-'); ?></td>
            <td><?php echo htmlspecialchars($row['type'] ?? '-'); ?></td>
            <td><?php echo htmlspecialchars($row['pages'] ?? '-'); ?></td>
            <td><?php echo number_format($row['price'] ?? 0, 2); ?></td>
            <td><?php echo htmlspecialchars($row['genres'] ?? '-'); ?></td>
            <td><?php echo htmlspecialchars(mb_substr($row['about'] ?? '', 0, 30)) . '...'; ?></td> <!-- ตัดข้อความ About ให้แสดงแค่ 30 ตัวอักษร ตารางจะได้ไม่ยืด -->
            <td><?php echo htmlspecialchars($row['bcover'] ?? '-'); ?></td>
            <td><?php echo htmlspecialchars($row['file'] ?? '-'); ?></td>
            
            <td>
                <div class="action-buttons">
                    <!-- แนบรหัสหนังสือ (bid) ไปกับปุ่ม เพื่อให้รู้ว่ากดแก้ไข/ลบเล่มไหน -->
                    <a href="modify_book.php?id=<?php echo $row['bid']; ?>" class="btn-modify" style="text-decoration:none;">modify</a>
                    
                    <!-- ปุ่มลบ พร้อมเด้งกล่องยืนยันป้องกันการกดพลาด -->
                    <a href="delete_book.php?id=<?php echo $row['bid']; ?>" class="btn-delete" style="text-decoration:none;" onclick="return confirm('Are you sure you want to delete this book?');">delete</a>
                </div>
            </td>
        </tr>
    <?php
        }
    } else {
        echo "<tr><td colspan='10' style='text-align:center;'>No book available yet</td></tr>";
    }
    ?>
</tbody>
        </table>
    </div>

    <!-- ปุ่มเพิ่มข้อมูลใหม่ -->
    <a href="add_book.php">
    <div class="add-data-container">
        <button class="btn-add-data">Add new data</button>
    </div>
    </a>

    <!-- ปุ่มกลับหน้าแรก -->
    <a href="../index.php" class="btn-back-home">back to home</a>

</body>
</html>