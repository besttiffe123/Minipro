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
    die("Database connection failed: " . $conn->connect_error);
}

$uid = $_SESSION['uid'];

// ดึงข้อมูลชื่อผู้ใช้และวันเกิด
$stmt = $conn->prepare("SELECT uname, dob FROM euser WHERE uid = ?");
$stmt->bind_param("i", $uid);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $uname = htmlspecialchars($row['uname']);
    // แปลงรูปแบบวันที่จาก YYYY-MM-DD เป็น DD/MM/YYYY เพื่อความสวยงาม
    $dob = date("d/m/Y", strtotime($row['dob']));
} else {
    $uname = "ไม่พบข้อมูล";
    $dob = "-";
}
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Account - Onelight Ebook</title>
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
        
        <!-- search -->
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
        <!-- เมนูด้านซ้าย -->
        <div class="sidebar">
            <a href="index.php">
                <i class="fa-solid fa-house"></i>
            </a>
            <a href="library.php">
                <i class="fa-solid fa-bookmark"></i>
            </a>
            <a href="profile.php">
                <i class="fa-solid fa-user active"></i> 
            </a> 
        </div>

        <!-- เนื้อหาหลัก -->
        <div class="content">
            <div class="section-header">
                Your Account
            </div>
            
            <div class="profile-section">
                <!-- ข้อมูลชื่อผู้ใช้ -->
                <div class="info-group">
                    <div class="info-label">Your UserName</div>
                    <div class="info-value"><?php echo $uname; ?></div>
                    <a href="Rename.php" class="link-change">change username</a>
                </div>

                <!-- ข้อมูลวันเกิด -->
                <div class="info-group">
                    <div class="info-label">Date of Birth</div>
                    <div class="info-value"><?php echo $dob; ?></div>
                </div>

                <!-- ปุ่ม Logout เปลี่ยนไปเรียกไฟล์ logout.php เพื่อล้าง Session -->
                 <a href="logout.php">
                    <button class="btn-logout">Logout</button>
                </a>
            </div>
        </div>
    </div>

    <!-- ปุ่มสำหรับ Admin เท่านั้น -->
     <a href="admin/foradmin.php">
        <button class="btn-admin">For admin only</button>
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