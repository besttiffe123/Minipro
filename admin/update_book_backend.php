<?php
$conn = new mysqli("localhost", "root", "", "ebook");
$conn->set_charset("utf8mb4");

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $bid = $_POST['bid'];
    $bname = $_POST['bname'];
    $author = $_POST['author'];
    $type = $_POST['type'];
    $pages = $_POST['pages'];
    $price = $_POST['price'];
    $about = $_POST['about'];
    $genres = isset($_POST['genres']) ? $_POST['genres'] : [];

    // ดึงชื่อไฟล์เดิมจากฐานข้อมูลมาก่อน
    $sql_old = "SELECT bcover, bfile FROM books WHERE bid = ?";
    $stmt_old = $conn->prepare($sql_old);
    $stmt_old->bind_param("i", $bid);
    $stmt_old->execute();
    $result_old = $stmt_old->get_result();
    $old_data = $result_old->fetch_assoc();
    
    $bcover = $old_data['bcover'];
    $bfile = $old_data['bfile'];
    $stmt_old->close();

    // หากมีการอัปโหลดรูปปกใหม่ ให้บันทึกทับค่าเดิม
    if (isset($_FILES['bcover']) && $_FILES['bcover']['error'] == 0) {
        $cover_name = time() . "_" . basename($_FILES["bcover"]["name"]);
        if (move_uploaded_file($_FILES["bcover"]["tmp_name"], "uploads/" . $cover_name)) {
            // (ทางเลือก) สามารถเขียนโค้ดลบไฟล์รูปเก่าทิ้งได้ที่นี่
            $bcover = $cover_name; 
        }
    }

    // หากมีการอัปโหลดไฟล์หนังสือใหม่ ให้บันทึกทับค่าเดิม
    if (isset($_FILES['bfile']) && $_FILES['bfile']['error'] == 0) {
        $file_name = time() . "_" . basename($_FILES["bfile"]["name"]);
        if (move_uploaded_file($_FILES["bfile"]["tmp_name"], "uploads/" . $file_name)) {
            $bfile = $file_name;
        }
    }

    // 1. อัปเดตข้อมูลตาราง books
    $stmt = $conn->prepare("UPDATE books SET bname=?, author=?, type=?, pages=?, price=?, about=?, bcover=?, bfile=? WHERE bid=?");
    $stmt->bind_param("sssidsssi", $bname, $author, $type, $pages, $price, $about, $bcover, $bfile, $bid);
    
    if ($stmt->execute()) {
        // 2. จัดการตาราง book_genres: ลบข้อมูลหมวดหมู่เดิมของหนังสือเล่มนี้ทิ้งทั้งหมดก่อน
        $stmt_del_genres = $conn->prepare("DELETE FROM book_genres WHERE bid = ?");
        $stmt_del_genres->bind_param("i", $bid);
        $stmt_del_genres->execute();
        $stmt_del_genres->close();

        // 3. เพิ่มหมวดหมู่ใหม่ที่ผู้ใช้เลือกเข้าไป
        if (!empty($genres)) {
            $stmt_add_genre = $conn->prepare("INSERT INTO book_genres (bid, gid) VALUES (?, ?)");
            foreach ($genres as $gid) {
                $stmt_add_genre->bind_param("ii", $bid, $gid);
                $stmt_add_genre->execute();
            }
            $stmt_add_genre->close();
        }

        echo "<script>
                alert('Book information has been updated.');
                window.location.href = 'foradmin.php'; 
              </script>";
    } else {
        echo "An error occurred: " . $stmt->error;
    }
    
    $stmt->close();
}
$conn->close();
?>