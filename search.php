<?php
session_start();
$conn = new mysqli("localhost", "root", "", "ebook");
$conn->set_charset("utf8mb4");

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$search_query = "";
$result = null;

// รับค่าคำค้นหาจากช่อง Search
if (isset($_GET['q']) && trim($_GET['q']) != '') {
    $search_query = trim($_GET['q']);
    // ใช้ % ครอบหน้าหลัง เพื่อให้ค้นเจอคำที่แทรกอยู่ตรงไหนของชื่อหนังสือก็ได้
    $sql = "SELECT * FROM books WHERE bname LIKE ?";
    $stmt = $conn->prepare($sql);
    $search_term = "%" . $search_query . "%";
    $stmt->bind_param("s", $search_term);
    $stmt->execute();
    $result = $stmt->get_result();
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>Search Results - Onelight Ebook</title>
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
    </div>
        
        <div class="content">
            <div class="section-header">
                Search Results for: "<?php echo htmlspecialchars($search_query); ?>"
            </div>
            
            <div class="book-grid">
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <!-- แสดงการ์ดหนังสือและลิงก์ไปหน้า Details.php -->
                         <a href="Details.php?id=<?php echo $row['bid']; ?>" class="book-card" style="text-decoration: none; color: inherit; display: block;">
                            <div class="book-cover" style="background-image: url('admin/uploads/<?php echo $row['bcover']; ?>'); background-size: cover; background-position: center; height: 320px; border-radius: 10px;"></div>
                            <div class="book-title" style="margin-top: 10px; font-weight: bold;"><?php echo htmlspecialchars($row['bname']); ?></div>
                            <div class="book-price" style="color: #8b1c85; font-size: 14px; margin-top: 5px;"><?php echo number_format($row['price'], 2); ?>B</div>
                        </a>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div style="width: 100%; padding: 50px; text-align: center; color: #666;">
                        <h3>No books found matching "<?php echo htmlspecialchars($search_query); ?>"</h3>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- นำ Script จัดการปุ่ม Dropdown มาวางตรงนี้ -->
</body>
</html>
<?php
if (isset($stmt)) {
    $stmt->close();
}
$conn->close();
?>