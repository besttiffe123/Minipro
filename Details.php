<?php
session_start();
// ตรวจสอบว่าล็อกอินหรือยัง
if (!isset($_SESSION['uid'])) {
    header("Location: login.php");
    exit(); 
}

// เชื่อมต่อฐานข้อมูล
$conn = new mysqli("localhost", "root", "", "ebook");
$conn->set_charset("utf8mb4");
if ($conn->connect_error) {
    die("เชื่อมต่อฐานข้อมูลล้มเหลว: " . $conn->connect_error);
}

// รับค่า id หนังสือจาก URL
if (isset($_GET['id'])) {
    $bid = intval($_GET['id']);
    
    // 1. โค้ดเดิม: ดึงข้อมูลหนังสือเล่มนั้น
    $sql = "SELECT b.*, GROUP_CONCAT(g.gname SEPARATOR ', ') AS genres 
            FROM books b
            LEFT JOIN book_genres bg ON b.bid = bg.bid
            LEFT JOIN genres g ON bg.gid = g.gid
            WHERE b.bid = ?
            GROUP BY b.bid";
            
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $bid);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $book = $result->fetch_assoc(); // เก็บข้อมูลหนังสือไว้ในตัวแปร $book
    } else {
        echo "<script>alert('ไม่พบข้อมูลหนังสือ'); window.location.href='index.php';</script>";
        exit();
    }
    $stmt->close(); // ปิดการทำงานของคำสั่งดึงข้อมูลหนังสือ

    // 2. โค้ดใหม่ที่เพิ่มเข้ามา: เช็คว่าเคยซื้อเล่มนี้ไปแล้วหรือยัง
    $is_owned = false;
    $uid = $_SESSION['uid'];
    $sql_check_own = "SELECT od.bid FROM eorder o 
                      JOIN order_details od ON o.oid = od.oid 
                      WHERE o.uid = ? AND od.bid = ? AND o.pstatus IN ('paid', 'succeed')";
    $stmt_own = $conn->prepare($sql_check_own);
    $stmt_own->bind_param("ii", $uid, $bid);
    $stmt_own->execute();
    if ($stmt_own->get_result()->num_rows > 0) {
        $is_owned = true; // พบประวัติว่าจ่ายเงินแล้ว
    }
    $stmt_own->close(); // ปิดการทำงานของคำสั่งเช็คประวัติ

} else {
    // ถ้าไม่มี id ส่งมา ให้เด้งกลับหน้าแรก
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($book['bname']); ?> - Onelight Ebook</title>
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
                <i class="fa-solid fa-house active"></i>
            </a>
            <a href="library.php">
                <i class="fa-solid fa-bookmark"></i>
            </a>
            <a href="profile.php">
                <i class="fa-solid fa-user"></i>
            </a>
        </div>

        <!-- เนื้อหาหลัก -->
         <div class="content">
            
            <!-- ปุ่มย้อนกลับไปหน้าแรก (แก้เป็น index.php) -->
            <a href="index.php" class="back-btn">
                <i class="fa-solid fa-arrow-left"></i>
            </a>

            <!-- ส่วนข้อมูลหนังสือ (แบ่ง 2 คอลัมน์) -->
            <div class="book-detail-top">
                <!-- คอลัมน์ซ้าย: รูปปก (ดึงจากโฟลเดอร์ uploads) -->
                <div class="book-cover-large" style="background-image: url('admin/uploads/<?php echo $book['bcover']; ?>'); background-size: cover; background-position: center;"></div>
                
                <!-- คอลัมน์ขวา: รายละเอียด -->
                <div class="book-info-container">
                    <!-- ดึงชื่อหนังสือ -->
                    <div class="book-title-large"><?php echo htmlspecialchars($book['bname']); ?></div>
                    <div class="book-meta">
                        <!-- ดึงชื่อผู้แต่ง ประเภท หมวดหมู่ และจำนวนหน้า -->
                        <div>by <?php echo htmlspecialchars($book['author']); ?></div>
                        <div>Type: <?php echo ucfirst(htmlspecialchars($book['type'])); ?></div>
                        <div>Genres: <?php echo htmlspecialchars($book['genres'] ?? '-'); ?></div>
                        <div>Pages: <?php echo htmlspecialchars($book['pages']); ?></div>
                    </div>
                    
                    <!-- ดึงราคา -->
                    <div class="book-price-large"><?php echo number_format($book['price'], 2); ?>B</div>
                    
                    <!-- กลุ่มปุ่มกด -->
<div class="book-actions" style="display: flex; gap: 10px; align-items: center;">
    <?php if ($is_owned): ?>
        <!-- ถ้าเคยซื้อแล้ว ให้แสดงปุ่ม Already Owned -->
        <button class="btn-buy" style="background-color: #cccccc; cursor: not-allowed; border: none;" disabled>Already Owned</button>
        
        <!-- ปุ่ม Read เปลี่ยนมาใช้คลาส btn-buy เพื่อให้ขนาดเท่ากัน -->
        <a href="library.php" style="text-decoration: none;">
            <button class="btn-buy" style="background-color: #8b1c85; color: white; cursor: pointer; border: none;">
                Read
            </button>
        </a>
    <?php else: ?>
        <!-- ถ้ายังไม่เคยซื้อ ให้แสดงปุ่มปกติ -->
        <a href="add_to_cart.php?bid=<?php echo $book['bid']; ?>&action=buy_now" style="text-decoration: none;">
            <button class="btn-buy">Buy Now</button>
        </a>
        <a href="add_to_cart.php?bid=<?php echo $book['bid']; ?>&action=add_only" style="text-decoration: none;">
            <button class="btn-add-cart">
                <i class="fa-solid fa-cart-shopping"></i>
            </button>
        </a>
    <?php endif; ?>
</div>
                </div>
            </div>
            <!-- ส่วนเรื่องย่อ (ด้านล่าง) -->
            <div class="about-section">
                <div class="about-title">About the Book</div>
                <div class="about-desc">
                    <!-- ดึงเรื่องย่อ (ใช้ nl2br เพื่อให้ขึ้นบรรทัดใหม่ตามที่แอดมินพิมพ์มา) -->
                    <?php echo nl2br(htmlspecialchars($book['about'])); ?>
                </div>
            </div>

        </div>
    </div>

    <!-- ปุ่มตะกร้า -->
    <a href="Basket.php">
        <div class="floating-cart">
            <i class="fa-solid fa-cart-shopping"></i>
        </div>
    </a>

    <script>
    const dropdownIcon = document.getElementById('dropdownIcon');
    const searchDropdown = document.getElementById('searchDropdown');

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
        if (!document.querySelector('.search-container').contains(event.target)) {
            searchDropdown.classList.remove('show');
            dropdownIcon.classList.remove('fa-circle-chevron-up');
            dropdownIcon.classList.add('fa-circle-chevron-down');
        }
    });
</script>
</body>
</html>