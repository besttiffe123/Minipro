<?php
// 1. เชื่อมต่อฐานข้อมูล
$conn = new mysqli("localhost", "root", "", "ebook");
$conn->set_charset("utf8mb4");

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// 2. รับค่า ID หนังสือจาก URL (ลิงก์ที่กดมาจากหน้า admin)
if (isset($_GET['id'])) {
    $bid = intval($_GET['id']);
    
    // 3. ดึงข้อมูลหนังสือจากตาราง books
    $sql = "SELECT * FROM books WHERE bid = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $bid);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $book = $result->fetch_assoc(); // เก็บข้อมูลหนังสือไว้ในตัวแปร $book
    } else {
        echo "<script>alert('Book not found'); window.location.href='foradmin.php';</script>";
        exit();
    }
    $stmt->close();

    // 4. ดึงข้อมูลหมวดหมู่ (Genres) ที่หนังสือเล่มนี้เคยเลือกไว้
    $sql_genres = "SELECT gid FROM book_genres WHERE bid = ?";
    $stmt_g = $conn->prepare($sql_genres);
    $stmt_g->bind_param("i", $bid);
    $stmt_g->execute();
    $result_g = $stmt_g->get_result();
    
    $current_genres = []; // สร้าง Array เก็บ ID หมวดหมู่ปัจจุบัน
    while ($row = $result_g->fetch_assoc()) {
        $current_genres[] = $row['gid'];
    }
    $stmt_g->close();

} else {
    // ถ้าไม่มี id แนบมา ให้เด้งกลับหน้าแอดมิน
    header("Location: foradmin.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modify Book - Admin Panel</title>
    <link rel="stylesheet" href="Foradminstyle.css">
</head>
<body>

    <div class="form-container">
        <div class="form-title">Modify Book Data</div>

        <form action="update_book_backend.php" method="POST" enctype="multipart/form-data">
            
            <!-- จุดสำคัญ 1: ส่งรหัสหนังสือของจริงไปให้ Backend -->
            <input type="hidden" name="bid" value="<?php echo $book['bid']; ?>"> 
            
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">Book Name</label>
                    <!-- นำข้อมูลจากตัวแปร $book มาใส่ใน value แทนข้อความจำลอง -->
                    <input type="text" name="bname" class="form-input" value="<?php echo htmlspecialchars($book['bname']); ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Author</label>
                    <input type="text" name="author" class="form-input" value="<?php echo htmlspecialchars($book['author']); ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Type</label>
                    <select name="type" class="form-select" required>
                        <!-- ใช้ if เช็คว่า type ใน DB ตรงกับข้อไหน ให้เติมคำว่า selected -->
                        <option value="novel" <?php if($book['type'] == 'novel') echo 'selected'; ?>>Novel</option>
                        <option value="comic" <?php if($book['type'] == 'comic') echo 'selected'; ?>>Comic</option>
                        <option value="manga" <?php if($book['type'] == 'manga') echo 'selected'; ?>>Manga</option>
                        <option value="artbook" <?php if($book['type'] == 'artbook') echo 'selected'; ?>>Artbook</option>
                    </select>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Pages</label>
                        <input type="number" name="pages" class="form-input" value="<?php echo $book['pages']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Price (B)</label>
                        <input type="number" step="0.01" name="price" class="form-input" value="<?php echo $book['price']; ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Cover Image (.jpg, .png)</label>
                    <input type="file" name="bcover" class="form-input" accept="image/*">
                    <!-- แสดงชื่อไฟล์รูปปัจจุบันที่ดึงมาจากฐานข้อมูล -->
                    <div class="current-file">Current file: <?php echo htmlspecialchars($book['bcover']); ?> (Leave blank to keep current)</div>
                </div>

                <div class="form-group">
                    <label class="form-label">Book File (.pdf, .epub)</label>
                    <input type="file" name="bfile" class="form-input" accept=".pdf,.epub">
                    <!-- แสดงชื่อไฟล์หนังสือปัจจุบันที่ดึงมาจากฐานข้อมูล -->
                    <div class="current-file">Current file: <?php echo htmlspecialchars($book['bfile']); ?> (Leave blank to keep current)</div>
                </div>

                <div class="form-group full-width">
                    <label class="form-label">Genres (Select multiple)</label>
                    <div class="checkbox-group">
                        <!-- ใช้ in_array() ตรวจสอบว่า ID หมวดหมู่นี้อยู่ใน $current_genres หรือไม่ ถ้าอยู่ให้ติ๊กถูก (checked) -->
                        <label class="checkbox-item"><input type="checkbox" name="genres[]" value="1" <?php if(in_array(1, $current_genres)) echo 'checked'; ?>> Action</label>
                        <label class="checkbox-item"><input type="checkbox" name="genres[]" value="2" <?php if(in_array(2, $current_genres)) echo 'checked'; ?>> Sci-fi</label>
                        <label class="checkbox-item"><input type="checkbox" name="genres[]" value="3" <?php if(in_array(3, $current_genres)) echo 'checked'; ?>> Fantasy</label>
                        <label class="checkbox-item"><input type="checkbox" name="genres[]" value="4" <?php if(in_array(4, $current_genres)) echo 'checked'; ?>> Romance</label>
                        <label class="checkbox-item"><input type="checkbox" name="genres[]" value="5" <?php if(in_array(5, $current_genres)) echo 'checked'; ?>> Thriller</label>
                        <label class="checkbox-item"><input type="checkbox" name="genres[]" value="6" <?php if(in_array(6, $current_genres)) echo 'checked'; ?>> Horror</label>
                    </div>
                </div>

                <div class="form-group full-width">
                    <label class="form-label">About</label>
                    <!-- นำข้อมูลมายัดตรงกลางระหว่างแท็ก <textarea> -->
                    <textarea name="about" class="form-textarea" rows="5" required><?php echo htmlspecialchars($book['about']); ?></textarea>
                </div>
            </div>

            <div class="form-actions">
                <a href="foradmin.php" class="btn-cancel">Cancel</a>
                <button type="submit" class="btn-save">Update Book</button>
            </div>

        </form>
    </div>

</body>
</html>