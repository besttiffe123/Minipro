<?php
$conn = new mysqli("localhost", "root", "", "ebook");
$conn->set_charset("utf8mb4");

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $bname = $_POST['bname'];
    $author = $_POST['author'];
    $type = $_POST['type'];
    $pages = $_POST['pages'];
    $price = $_POST['price'];
    $about = $_POST['about'];
    
    // รับค่า Array ของหมวดหมู่
    $genres = isset($_POST['genres']) ? $_POST['genres'] : [];

    // --- จัดการอัปโหลดไฟล์รูปปก (bcover) ---
    $bcover = "";
    if (isset($_FILES['bcover']) && $_FILES['bcover']['error'] == 0) {
        $cover_name = time() . "_" . basename($_FILES["bcover"]["name"]);
        $cover_path = "uploads/" . $cover_name;
        if (move_uploaded_file($_FILES["bcover"]["tmp_name"], $cover_path)) {
            $bcover = $cover_name;
        }
    }

    // --- จัดการอัปโหลดไฟล์หนังสือ (bfile) ---
    $bfile = "";
    if (isset($_FILES['bfile']) && $_FILES['bfile']['error'] == 0) {
        $file_name = time() . "_" . basename($_FILES["bfile"]["name"]);
        $file_path = "uploads/" . $file_name;
        if (move_uploaded_file($_FILES["bfile"]["tmp_name"], $file_path)) {
            $bfile = $file_name;
        }
    }

    // 1. เพิ่มข้อมูลลงตาราง books
    $stmt = $conn->prepare("INSERT INTO books (bname, author, type, pages, price, about, bcover, bfile) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssidsss", $bname, $author, $type, $pages, $price, $about, $bcover, $bfile);
    
    if ($stmt->execute()) {
        // ดึงไอดีหนังสือที่เพิ่งถูกเพิ่ม (bid) เพื่อนำไปเชื่อมกับตารางหมวดหมู่
        $new_bid = $conn->insert_id;

        // 2. เพิ่มข้อมูลลงตาราง book_genres 
        if (!empty($genres)) {
            $stmt_genre = $conn->prepare("INSERT INTO book_genres (bid, gid) VALUES (?, ?)");
            foreach ($genres as $gid) {
                $stmt_genre->bind_param("ii", $new_bid, $gid);
                $stmt_genre->execute();
            }
            $stmt_genre->close();
        }

        echo "<script>
                alert('New book added successfully.');
                window.location.href = 'foradmin.php'; 
              </script>";
    } else {
        echo "An error occurred: " . $stmt->error;
    }

    $stmt->close();
}
$conn->close();
?>