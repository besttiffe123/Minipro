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

// ดึงข้อมูลหนังสือที่ผู้ใช้เคยซื้อและชำระเงินเรียบร้อยแล้ว (ใช้ DISTINCT เผื่อกันการซ้ำซ้อน)
$sql = "SELECT DISTINCT b.bid, b.bname, b.bcover, b.bfile 
        FROM books b
        JOIN order_details od ON b.bid = od.bid
        JOIN eorder o ON od.oid = o.oid
        WHERE o.uid = ? AND o.pstatus IN ('paid', 'succeed')
        ORDER BY o.oid DESC";

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
    <title>Your Library - Onelight Ebook</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
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
        
        <div class="search-container">
    <div class="search-bar">
        <!-- เพิ่ม form ครอบไว้ และชี้ไปที่ search.php -->
        <form action="search.php" method="GET" style="display: flex; align-items: center; width: 100%; margin: 0; padding: 0;">
            <button type="submit" style="background: none; border: none; cursor: pointer; padding: 0;">
                <i class="fa-solid fa-magnifying-glass" style="color: #8b1c85;"></i>
            </button>
            <!-- เพิ่ม name="q" เข้าไปเพื่อให้ PHP รู้จักคำที่ค้นหา -->
            <input type="text" name="q" placeholder="Search" required style="border: none; outline: none; background: transparent; width: 100%; margin-left: 10px;">
        </form>
        <i class="fa-solid fa-circle-chevron-down" id="dropdownIcon" style="color: #8b1c85; cursor: pointer; margin-left: 10px;"></i>
    </div>
            <!-- เมนู Dropdown ที่ซ่อนอยู่ -->
            <div class="search-dropdown" id="searchDropdown">
                <div class="dropdown-section">
                    <div class="dropdown-header">Type</div>
                    <ul class="dropdown-list">
                        <li><a href="Novel.html">Novel</a></li>
                        <li><a href="Comic.html">Comic</a></li>
                        <li><a href="Manga.html">Manga</a></li>
                        <li><a href="Artbook.html">Artbook</a></li>
                    </ul>
                </div>

                <hr class="dropdown-divider">

                <div class="dropdown-section">
                    <div class="dropdown-header">Genres</div>
                    <ul class="dropdown-list">
                        <li><a href="Action.html">Action</a></li>
                        <li><a href="Sci-fi.html">Sci-fi</a></li>
                        <li><a href="Fantasy.html">Fantasy</a></li>
                        <li><a href="Romance.html">Romance</a></li>
                        <li><a href="Thriller.html">Thriller</a></li>
                        <li><a href="Horror.html">Horror</a></li>
                    </ul>
                </div>
            </div> 
        </div>
    </div>

    <div class="main-container">
        <!-- เมนูด้านซ้าย -->
        <div class="sidebar">
            <a href="index.php">
                <i class="fa-solid fa-house"></i>
            </a>
            <a href="library.php">
                <i class="fa-solid fa-bookmark active"></i>
            </a>
            <a href="profile.php">
                <i class="fa-solid fa-user"></i>
            </a>
        </div>

        <!-- เนื้อหาหลัก -->
        <div class="content">
            <div class="section-header">
                Your library
            </div>
            
            <div class="book-grid">
                <?php if ($result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <div class="book-card">
                            <!-- ดึงรูปปกมาแสดง -->
                            <div class="book-cover" style="background-image: url('admin/uploads/<?php echo $row['bcover']; ?>'); background-size: cover; background-position: center;"></div>
                            
                            <!-- ดึงชื่อหนังสือ -->
                            <div class="book-title"><?php echo htmlspecialchars($row['bname']); ?></div>
                            
                            <!-- ส่วนปุ่มกด 2 ปุ่ม -->
                        <div class="card-actions" style="display: flex; gap: 8px; justify-content: space-between; margin-top: 10px;">
                            <!-- ปุ่ม Download -->
                            <a href="admin/uploads/<?php echo $row['bfile']; ?>" download="<?php echo htmlspecialchars($row['bname']); ?>" style="text-decoration: none; flex: 1;">
                                <button class="btn-action" style="width: 100%; padding: 10px 0; font-size: 14px; background-color: #8b1c85; color: white; border: none; border-radius: 20px; cursor: pointer;">
                                Download
                                </button>
                            </a>
    
                            <!-- ปุ่ม Read now -->
                            <a href="admin/uploads/<?php echo $row['bfile']; ?>" target="_blank" style="text-decoration: none; flex: 1;">
                                <button class="btn-action" style="width: 100%; padding: 10px 0; font-size: 14px; background-color: #8b1c85; color: white; border: none; border-radius: 20px; cursor: pointer;">
                                Read now
                                </button>
                            </a>
                        </div>
                    </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div style="width: 100%; padding: 50px; text-align: center; color: #666;">
                        <h3>คลังหนังสือของคุณยังว่างเปล่า</h3>
                        <p>ไปเลือกซื้อหนังสือเล่มโปรดกันเถอะ!</p>
                        <a href="index.php" style="color: #8b1c85; text-decoration: underline;">ไปที่หน้าแรก</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
    const dropdownIcon = document.getElementById('dropdownIcon');
    const searchDropdown = document.getElementById('searchDropdown');

    // เมื่อคลิกที่ไอคอนลูกศร
    dropdownIcon.addEventListener('click', function(event) {
        event.stopPropagation();
        searchDropdown.classList.toggle('show');
        
        if (searchDropdown.classList.contains('show')) { 
            dropdownIcon.classList.remove('fa-circle-chevron-down');
            dropdownIcon.classList.add('fa-circle-chevron-up');
        } else {
            dropdownIcon.classList.remove('fa-circle-chevron-up');
            dropdownIcon.classList.add('fa-circle-chevron-down');
        }
    });

    window.addEventListener('click', function(event) {
        const searchContainer = document.querySelector('.search-container');
        if (searchContainer && !searchContainer.contains(event.target)) {
            searchDropdown.classList.remove('show');
            dropdownIcon.classList.remove('fa-circle-chevron-up');
            dropdownIcon.classList.add('fa-circle-chevron-down');
        }
    });
    </script>
</body>
</html>
<?php
$stmt->close();
$conn->close();
?>