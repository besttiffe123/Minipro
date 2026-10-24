<?php
session_start();
// ตรวจสอบว่าล็อกอินหรือยัง
if (!isset($_SESSION['uid'])) {
    header("Location: login.php");
    exit(); 
}

// เมื่อผู้ใช้กดปุ่ม Confirm (ส่งฟอร์มแบบ POST)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = new mysqli("localhost", "root", "", "ebook");
    $conn->set_charset("utf8mb4");

    if ($conn->connect_error) {
        die("Database connection failed: " . $conn->connect_error);
    }

    $new_uname = trim($_POST['new_uname']);
    $uid = $_SESSION['uid'];

    // ตรวจสอบว่าไม่ได้กรอกค่าว่างมา
    if (!empty($new_uname)) {
        // อัปเดตชื่อผู้ใช้ในฐานข้อมูล
        $stmt = $conn->prepare("UPDATE euser SET uname = ? WHERE uid = ?");
        $stmt->bind_param("si", $new_uname, $uid);
        
        if ($stmt->execute()) {
            // อัปเดตตัวแปร Session ให้เป็นชื่อใหม่ด้วย
            $_SESSION['uname'] = $new_uname;
            
            echo "<script>
                    alert('Username changed successfully!');
                    window.location.href = 'profile.php';
                  </script>";
            exit();
        } else {
            echo "<script>alert('An error occurred while changing the username.');</script>";
        }
        $stmt->close();
    }
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Username - Onelight Ebook</title>
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
                <i class="fa-solid fa-magnifying-glass" style="color: #8b1c85;"></i>
                <input type="text" placeholder="Search">
                <i class="fa-solid fa-circle-chevron-down" id="dropdownIcon" style="color: #8b1c85; cursor: pointer;"></i>
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
            
            <!-- เปลี่ยนคลาสกรอบเป็น form เพื่อให้ส่งข้อมูลกลับไปหา PHP ด้านบนได้ -->
            <form action="" method="POST" class="rename-section">
                
                <div class="rename-label">Enter your new username</div>
                
                <!-- ใส่ name="new_uname" เพื่อรับค่า และ required บังคับให้กรอกก่อนกด -->
                <input type="text" name="new_uname" class="rename-input" required>
                
                <div class="rename-actions">
                    <a href="profile.php" class="btn-cancel">Cancel</a>
                    <button type="submit" class="btn-confirm">Confirm</button>
                </div>
                
            </form>
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