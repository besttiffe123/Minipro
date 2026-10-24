<?php
session_start();
$conn = new mysqli("localhost", "root", "", "ebook");
$conn->set_charset("utf8mb4");

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// ดึงข้อมูลหนังสือทั้งหมด โดยเรียงจากเล่มที่เพิ่มล่าสุด (bid มากไปน้อย) 
// ถ้าต้องการจำกัดจำนวนเล่มที่แสดงให้เติม LIMIT ต่อท้าย เช่น ORDER BY bid DESC LIMIT 20
$sql = "SELECT * FROM books ORDER BY bid DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Newly releases - Onelight Ebook</title>
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
                <!-- อัปเดตช่องค้นหาให้ใช้งานได้จริง -->
                <form action="search.php" method="GET" style="display: flex; align-items: center; width: 100%; margin: 0; padding: 0;">
                    <button type="submit" style="background: none; border: none; cursor: pointer; padding: 0;">
                        <i class="fa-solid fa-magnifying-glass" style="color: #8b1c85;"></i>
                    </button>
                    <input type="text" name="q" placeholder="Search" required style="border: none; outline: none; background: transparent; width: 100%; margin-left: 10px;">
                </form>
                <i class="fa-solid fa-circle-chevron-down" id="dropdownIcon" style="color: #8b1c85; cursor: pointer; margin-left: 10px;"></i>
            </div>

            <!-- เมนู Dropdown ที่ซ่อนอยู่ (เปลี่ยนนามสกุลลิงก์เป็น .php) -->
            <div class="search-dropdown" id="searchDropdown">
                <div class="dropdown-section">
                    <div class="dropdown-header">Type</div>
                    <ul class="dropdown-list">
                        <li><a href="Novel.php">Novel</a></li>
                        <li><a href="Comic.php">Comic</a></li>
                        <li><a href="Manga.php">Manga</a></li>
                        <li><a href="Artbook.php">Artbook</a></li>
                    </ul>
                </div>

                <hr class="dropdown-divider">

                <div class="dropdown-section">
                    <div class="dropdown-header">Genres</div>
                    <ul class="dropdown-list">
                        <li><a href="Action.php">Action</a></li>
                        <li><a href="Sci-fi.php">Sci-fi</a></li>
                        <li><a href="Fantasy.php">Fantasy</a></li>
                        <li><a href="Romance.php">Romance</a></li>
                        <li><a href="Thriller.php">Thriller</a></li>
                        <li><a href="Horror.php">Horror</a></li>
                    </ul>
                </div>
            </div>    
        </div>
    </div>

    <div class="main-container">
        <!-- เมนูด้านซ้าย (เปลี่ยนนามสกุลลิงก์เป็น .php) -->
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
            <!-- หมวดหมู่ Newly releases -->
            <div class="section-header">
                Newly releases
            </div>
            
            <div class="book-grid">
                <div class="book-grid <?php echo ($result && $result->num_rows == 0) ? 'empty-grid' : ''; ?>">
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <!-- เปลี่ยนการ์ดหนังสือให้ดึงข้อมูลจริงจากฐานข้อมูล -->
                        <a href="Details.php?id=<?php echo $row['bid']; ?>" class="book-card" style="text-decoration: none; color: inherit;">
                            <div class="book-cover" style="background-image: url('admin/uploads/<?php echo $row['bcover']; ?>'); background-size: cover; background-position: center;"></div>
                            <div class="book-title"><?php echo htmlspecialchars($row['bname']); ?></div>
                            <div class="book-price"><?php echo number_format($row['price'], 2); ?>B</div>
                        </a>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="empty-message">
                        <h3>There are no books yet.</h3>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

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
<?php
$conn->close();
?>