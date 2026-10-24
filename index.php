<?php
session_start(); // 1. เปิดระบบตรวจสอบบัตรผ่าน (Session)

// 2. เช็คว่า "ไม่มี" ข้อมูลล็อกอินอยู่ใช่ไหม? (!isset แปลว่า ไม่มีค่า)
if (!isset($_SESSION['uid'])) {
    // 3. ถ้าไม่มี ให้เด้งกลับไปหน้า login.php ทันที
    header("Location: login.php");
    exit(); // สั่งให้หยุดอ่านโค้ดบรรทัดต่อไปทันที เพื่อความปลอดภัย
}
?>

<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['uid'])) {
    header("Location: login.php");
    exit(); 
}

// เพิ่มการเชื่อมต่อฐานข้อมูลตรงนี้
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
    <title>Onelight Ebook online shop</title>
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
            <div 
                class="section-header"> Newly releases <a href="Newreleases.php"><i class="fa-solid fa-caret-down"></i></a>
            </div>
            <div class="book-grid">
            <?php
                // ดึงหนังสือ 4 เล่มล่าสุด
                $sql_new = "SELECT bid, bname, price, bcover FROM books ORDER BY bid DESC LIMIT 4";
                $result_new = $conn->query($sql_new);

                if ($result_new->num_rows > 0) {
                while($row = $result_new->fetch_assoc()) {
            ?>
            <!-- เปลี่ยน href ให้แนบ ?id= ตามด้วยรหัสหนังสือ -->
            <a href="Details.php?id=<?php echo $row['bid']; ?>" style="text-decoration: none; color: inherit;">
                <div class="book-card">
                    <!-- แสดงรูปปกจากโฟลเดอร์ uploads -->
                    <div class="book-cover" style="background-image: url('admin/uploads/<?php echo $row['bcover']; ?>'); background-size: cover; background-position: center;"></div>
                    <div class="book-title"><?php echo htmlspecialchars($row['bname']); ?></div>
                    <div class="book-price"><?php echo number_format($row['price'], 2); ?>B</div>
                </div>
            </a>
            <?php
                }
            } else {
                echo "<p>There are no books yet.</p>";
            }
            ?>
        </div>

            <!-- หมวดหมู่ Novel -->
            <div class="section-header" style="margin-top: 20px;">
                Novel <a href="Novel.php"><i class="fa-solid fa-caret-down"></i></a>
            </div>
        <div class="book-grid">
            <?php
            // ดึงเฉพาะหนังสือที่ type = 'novel'
            $sql_novel = "SELECT bid, bname, price, bcover FROM books WHERE type = 'novel' ORDER BY bid DESC LIMIT 4";
            $result_novel = $conn->query($sql_novel);

            if ($result_novel->num_rows > 0) {
                while($row = $result_novel->fetch_assoc()) {
            ?>
            <a href="Details.php?id=<?php echo $row['bid']; ?>" style="text-decoration: none; color: inherit;">
                <div class="book-card">
                    <div class="book-cover" style="background-image: url('admin/uploads/<?php echo $row['bcover']; ?>'); background-size: cover; background-position: center;"></div>
                    <div class="book-title"><?php echo htmlspecialchars($row['bname']); ?></div>
                    <div class="book-price"><?php echo number_format($row['price'], 2); ?>B</div>
                </div>
            </a>
            <?php
            }
            } else {
                echo "<p>There are no books yet.</p>";
            }
            ?>
        </div>

        <!-- หมวดหมู่ comic -->
            <div class="section-header" style="margin-top: 20px;">
                Comic <a href="Comic.php"><i class="fa-solid fa-caret-down"></i></a>
            </div>
        <div class="book-grid">
            <?php
            // ดึงเฉพาะหนังสือที่ type = 'comic'
            $sql_comic = "SELECT bid, bname, price, bcover FROM books WHERE type = 'comic' ORDER BY bid DESC LIMIT 4";
            $result_comic = $conn->query($sql_comic);

            if ($result_comic->num_rows > 0) {
                while($row = $result_comic->fetch_assoc()) {
            ?>
            <a href="Details.php?id=<?php echo $row['bid']; ?>" style="text-decoration: none; color: inherit;">
                <div class="book-card">
                    <div class="book-cover" style="background-image: url('admin/uploads/<?php echo $row['bcover']; ?>'); background-size: cover; background-position: center;"></div>
                    <div class="book-title"><?php echo htmlspecialchars($row['bname']); ?></div>
                    <div class="book-price"><?php echo number_format($row['price'], 2); ?>B</div>
                </div>
            </a>
            <?php
            }
            } else {
                echo "<p>There are no books yet.</p>";
            }
            ?>
        </div>

        <!-- หมวดหมู่ manga -->
            <div class="section-header" style="margin-top: 20px;">
                Manga <a href="Manga.php"><i class="fa-solid fa-caret-down"></i></a>
            </div>
        <div class="book-grid">
            <?php
            // ดึงเฉพาะหนังสือที่ type = 'manga'
            $sql_manga = "SELECT bid, bname, price, bcover FROM books WHERE type = 'manga' ORDER BY bid DESC LIMIT 4";
            $result_manga = $conn->query($sql_manga);

            if ($result_manga->num_rows > 0) {
                while($row = $result_manga->fetch_assoc()) {
            ?>
            <a href="Details.php?id=<?php echo $row['bid']; ?>" style="text-decoration: none; color: inherit;">
                <div class="book-card">
                    <div class="book-cover" style="background-image: url('admin/uploads/<?php echo $row['bcover']; ?>'); background-size: cover; background-position: center;"></div>
                    <div class="book-title"><?php echo htmlspecialchars($row['bname']); ?></div>
                    <div class="book-price"><?php echo number_format($row['price'], 2); ?>B</div>
                </div>
            </a>
            <?php
            }
            } else {
                echo "<p>There are no books yet.</p>";
            }
            ?>
        </div>

        <!-- หมวดหมู่ artbook -->
            <div class="section-header" style="margin-top: 20px;">
                Artbook <a href="Artbook.php"><i class="fa-solid fa-caret-down"></i></a>
            </div>
        <div class="book-grid">
            <?php
            // ดึงเฉพาะหนังสือที่ type = 'artbook'
            $sql_artbook = "SELECT bid, bname, price, bcover FROM books WHERE type = 'artbook' ORDER BY bid DESC LIMIT 4";
            $result_artbook = $conn->query($sql_artbook);

            if ($result_artbook->num_rows > 0) {
                while($row = $result_artbook->fetch_assoc()) {
            ?>
            <a href="Details.php?id=<?php echo $row['bid']; ?>" style="text-decoration: none; color: inherit;">
                <div class="book-card">
                    <div class="book-cover" style="background-image: url('admin/uploads/<?php echo $row['bcover']; ?>'); background-size: cover; background-position: center;"></div>
                    <div class="book-title"><?php echo htmlspecialchars($row['bname']); ?></div>
                    <div class="book-price"><?php echo number_format($row['price'], 2); ?>B</div>
                </div>
            </a>
            <?php
            }
            } else {
                echo "<p>There are no books yet.</p>";
            }
            ?>
        </div>

        <!-- หมวดหมู่ Action -->
    <div class="section-header" style="margin-top: 20px;">
        Action <a href="Action.php"><i class="fa-solid fa-caret-down"></i></a>
    </div>
    <div class="book-grid">
        <?php
        // ดึงเฉพาะหนังสือที่มีหมวดหมู่ (Genre) เป็น 'Action' โดยดึงมาแค่ 4 เล่มล่าสุด
        $sql_action = "SELECT b.bid, b.bname, b.price, b.bcover 
                       FROM books b
                       JOIN book_genres bg ON b.bid = bg.bid
                       JOIN genres g ON bg.gid = g.gid
                       WHERE g.gname = 'Action' 
                       ORDER BY b.bid DESC LIMIT 4";
        $result_action = $conn->query($sql_action);

        if ($result_action->num_rows > 0) {
            while($row = $result_action->fetch_assoc()) {
        ?>
        <a href="Details.php?id=<?php echo $row['bid']; ?>" style="text-decoration: none; color: inherit;">
            <div class="book-card">
                <div class="book-cover" style="background-image: url('admin/uploads/<?php echo $row['bcover']; ?>'); background-size: cover; background-position: center;"></div>
                <div class="book-title"><?php echo htmlspecialchars($row['bname']); ?></div>
                <div class="book-price"><?php echo number_format($row['price'], 2); ?>B</div>
            </div>
        </a>
        <?php
            }
        } else {
            echo "<p>There are no books yet.</p>";
        }
        ?>
    </div>

        <!-- หมวดหมู่ Sci-fi -->
    <div class="section-header" style="margin-top: 20px;">
        Sci-fi <a href="Sci-fi.php"><i class="fa-solid fa-caret-down"></i></a>
    </div>
    <div class="book-grid">
        <?php
        // ดึงเฉพาะหนังสือที่มีหมวดหมู่ (Genre) เป็น 'Sci-fi' โดยดึงมาแค่ 4 เล่มล่าสุด
        $sql_sci = "SELECT b.bid, b.bname, b.price, b.bcover 
                       FROM books b
                       JOIN book_genres bg ON b.bid = bg.bid
                       JOIN genres g ON bg.gid = g.gid
                       WHERE g.gname = 'Sci-fi' 
                       ORDER BY b.bid DESC LIMIT 4";
        $result_sci = $conn->query($sql_sci);

        if ($result_sci->num_rows > 0) {
            while($row = $result_sci->fetch_assoc()) {
        ?>
        <a href="Details.php?id=<?php echo $row['bid']; ?>" style="text-decoration: none; color: inherit;">
            <div class="book-card">
                <div class="book-cover" style="background-image: url('admin/uploads/<?php echo $row['bcover']; ?>'); background-size: cover; background-position: center;"></div>
                <div class="book-title"><?php echo htmlspecialchars($row['bname']); ?></div>
                <div class="book-price"><?php echo number_format($row['price'], 2); ?>B</div>
            </div>
        </a> 
        <?php
            }
        } else {
            echo "<p>There are no books yet.</p>";
        }
        ?>
    </div>

    <!-- หมวดหมู่ Fantasy -->
    <div class="section-header" style="margin-top: 20px;">
        Fantasy <a href="Fantasy.php"><i class="fa-solid fa-caret-down"></i></a>
    </div>
    <div class="book-grid">
        <?php
        // ดึงเฉพาะหนังสือที่มีหมวดหมู่ (Genre) เป็น 'Fantasy' โดยดึงมาแค่ 4 เล่มล่าสุด
        $sql_fantasy = "SELECT b.bid, b.bname, b.price, b.bcover 
                          FROM books b
                          JOIN book_genres bg ON b.bid = bg.bid
                          JOIN genres g ON bg.gid = g.gid
                          WHERE g.gname = 'Fantasy'
                          ORDER BY b.bid DESC LIMIT 4";
        $result_fantasy = $conn->query($sql_fantasy);

        if ($result_fantasy->num_rows > 0) {
            while($row = $result_fantasy->fetch_assoc()) {
        ?>
        <a href="Details.php?id=<?php echo $row['bid']; ?>" style="text-decoration: none; color: inherit;">
            <div class="book-card">
                <div class="book-cover" style="background-image: url('admin/uploads/<?php echo $row['bcover']; ?>'); background-size: cover; background-position: center;"></div>
                <div class="book-title"><?php echo htmlspecialchars($row['bname']); ?></div>
                <div class="book-price"><?php echo number_format($row['price'], 2); ?>B</div>
            </div>
        </a> 
        <?php
            }
        } else {
            echo "<p>There are no books yet.</p>";
        }
        ?>
    </div>

    <!-- หมวดหมู่ Romance -->
    <div class="section-header" style="margin-top: 20px;">
        Romance <a href="Romance.php"><i class="fa-solid fa-caret-down"></i></a>
    </div>
    <div class="book-grid">
        <?php
        // ดึงเฉพาะหนังสือที่มีหมวดหมู่ (Genre) เป็น 'Romance' โดยดึงมาแค่ 4 เล่มล่าสุด
        $sql_romance = "SELECT b.bid, b.bname, b.price, b.bcover 
                          FROM books b
                          JOIN book_genres bg ON b.bid = bg.bid
                          JOIN genres g ON bg.gid = g.gid
                          WHERE g.gname = 'Romance'
                          ORDER BY b.bid DESC LIMIT 4";
        $result_romance = $conn->query($sql_romance);

        if ($result_romance->num_rows > 0) {
            while($row = $result_romance->fetch_assoc()) {
        ?>
        <a href="Details.php?id=<?php echo $row['bid']; ?>" style="text-decoration: none; color: inherit;">
            <div class="book-card">
                <div class="book-cover" style="background-image: url('admin/uploads/<?php echo $row['bcover']; ?>'); background-size: cover; background-position: center;"></div>
                <div class="book-title"><?php echo htmlspecialchars($row['bname']); ?></div>
                <div class="book-price"><?php echo number_format($row['price'], 2); ?>B</div>
            </div>
        </a> 
        <?php
            }
        } else {
            echo "<p>There are no books yet.</p>";
        }
        ?>
    </div>

    <!-- หมวดหมู่ Thriller -->
    <div class="section-header" style="margin-top: 20px;">
        Thriller <a href="Thriller.php"><i class="fa-solid fa-caret-down"></i></a>
    </div>
    <div class="book-grid">
        <?php
        // ดึงเฉพาะหนังสือที่มีหมวดหมู่ (Genre) เป็น 'Thriller' โดยดึงมาแค่ 4 เล่มล่าสุด
        $sql_thriller = "SELECT b.bid, b.bname, b.price, b.bcover 
                          FROM books b
                          JOIN book_genres bg ON b.bid = bg.bid
                          JOIN genres g ON bg.gid = g.gid
                          WHERE g.gname = 'Thriller'
                          ORDER BY b.bid DESC LIMIT 4";
        $result_thriller = $conn->query($sql_thriller);

        if ($result_thriller->num_rows > 0) {
            while($row = $result_thriller->fetch_assoc()) {
        ?>
        <a href="Details.php?id=<?php echo $row['bid']; ?>" style="text-decoration: none; color: inherit;">
            <div class="book-card">
                <div class="book-cover" style="background-image: url('admin/uploads/<?php echo $row['bcover']; ?>'); background-size: cover; background-position: center;"></div>
                <div class="book-title"><?php echo htmlspecialchars($row['bname']); ?></div>
                <div class="book-price"><?php echo number_format($row['price'], 2); ?>B</div>
            </div>
        </a> 
        <?php
            }
        } else {
            echo "<p>There are no books yet.</p>";
        }
        ?>
    </div>

    <!-- หมวดหมู่ Horror -->
    <div class="section-header" style="margin-top: 20px;">
        Horror <a href="Horror.php"><i class="fa-solid fa-caret-down"></i></a>
    </div>
    <div class="book-grid">
        <?php
        // ดึงเฉพาะหนังสือที่มีหมวดหมู่ (Genre) เป็น 'Horror' โดยดึงมาแค่ 4 เล่มล่าสุด
        $sql_horror = "SELECT b.bid, b.bname, b.price, b.bcover 
                         FROM books b
                         JOIN book_genres bg ON b.bid = bg.bid
                         JOIN genres g ON bg.gid = g.gid
                         WHERE g.gname = 'Horror'
                         ORDER BY b.bid DESC LIMIT 4";
        $result_horror = $conn->query($sql_horror);

        if ($result_horror->num_rows > 0) {
            while($row = $result_horror->fetch_assoc()) {
        ?>
        <a href="Details.php?id=<?php echo $row['bid']; ?>" style="text-decoration: none; color: inherit;">
            <div class="book-card">
                <div class="book-cover" style="background-image: url('admin/uploads/<?php echo $row['bcover']; ?>'); background-size: cover; background-position: center;"></div>
                <div class="book-title"><?php echo htmlspecialchars($row['bname']); ?></div>
                <div class="book-price"><?php echo number_format($row['price'], 2); ?>B</div>
            </div>
        </a> 
        <?php
            }
        } else {
            echo "<p>There are no books yet.</p>";
        }
        ?>
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

    // เมื่อคลิกที่ไอคอนลูกศร
    dropdownIcon.addEventListener('click', function(event) {
        event.stopPropagation(); // ป้องกันไม่ให้คลิกทะลุไปโดนส่วนอื่น
        searchDropdown.classList.toggle('show');
        
        // สลับไอคอนชี้ลง/ชี้ขึ้น
        if (searchDropdown.classList.contains('show')) { //  เช็กดูว่า ตอนนี้ตัวเมนูค้นหา (searchDropdown) มีคำสั่ง (Class) ที่ชื่อว่า 'show' แปะอยู่ไหม?
            dropdownIcon.classList.remove('fa-circle-chevron-down');
            dropdownIcon.classList.add('fa-circle-chevron-up');
        } else {
            dropdownIcon.classList.remove('fa-circle-chevron-up');
            dropdownIcon.classList.add('fa-circle-chevron-down');
        }
    });

    // ปิดเมนูอัตโนมัติหากผู้ใช้คลิกพื้นที่อื่นบนหน้าเว็บ
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