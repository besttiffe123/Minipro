<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modify Book - Admin Panel</title>
    <link rel="stylesheet" href="Foradminstyle.css">
</head>
<body>

    <!-- 
      (ในอนาคตตรงนี้จะต้องมี PHP ดึงข้อมูล SELECT * FROM books WHERE bid = $_GET['id'] 
      เพื่อเอาค่ามาใส่ในตัวแปรต่างๆ ก่อนนำไปแสดงในฟอร์มด้านล่าง) 
    -->

    <div class="form-container">
        <div class="form-title">Modify Book Data</div>

        <form action="update_book_backend.php" method="POST" enctype="multipart/form-data">
            
            <!-- จุดสำคัญ 1: Hidden Input ซ่อนรหัสหนังสือไว้ส่งไปตอนอัปเดต -->
            <input type="hidden" name="bid" value="1"> 
            
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">Book Name</label>
                    <!-- จุดสำคัญ 2: ใส่ value="" เพื่อแสดงค่าเดิม -->
                    <input type="text" name="bname" class="form-input" value="Book1" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Author</label>
                    <input type="text" name="author" class="form-input" value="Me" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Type</label>
                    <select name="type" class="form-select" required>
                        <!-- เวลาทำ PHP จริง จะต้องใช้คำสั่ง if เช็คว่าค่าตรงกับอันไหนเพื่อใส่คำว่า selected -->
                        <option value="novel" selected>Novel</option>
                        <option value="comic">Comic</option>
                        <option value="manga">Manga</option>
                        <option value="artbook">Artbook</option>
                    </select>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Pages</label>
                        <input type="number" name="pages" class="form-input" value="200" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Price (B)</label>
                        <input type="number" step="0.01" name="price" class="form-input" value="0.00" required>
                    </div>
                </div>

                <!-- จุดสำคัญ 3: เอา required ออก และแสดงไฟล์เก่า -->
                <div class="form-group">
                    <label class="form-label">Cover Image (.jpg, .png)</label>
                    <input type="file" name="bcover" class="form-input" accept="image/*">
                    <div class="current-file">Current file: cover1.jpg (Leave blank to keep current)</div>
                </div>

                <div class="form-group">
                    <label class="form-label">Book File (.pdf, .epub)</label>
                    <input type="file" name="bfile" class="form-input" accept=".pdf,.epub">
                    <div class="current-file">Current file: book1.pdf (Leave blank to keep current)</div>
                </div>

                <div class="form-group full-width">
                    <label class="form-label">Genres (Select multiple)</label>
                    <div class="checkbox-group">
                        <!-- เวลาทำ PHP จริง จะเช็คว่ามีใน book_genres ไหม ถ้ามีให้เติมคำว่า checked -->
                        <label class="checkbox-item"><input type="checkbox" name="genres[]" value="1"> Action</label>
                        <label class="checkbox-item"><input type="checkbox" name="genres[]" value="2"> Sci-fi</label>
                        <label class="checkbox-item"><input type="checkbox" name="genres[]" value="3" checked> Fantasy</label>
                        <label class="checkbox-item"><input type="checkbox" name="genres[]" value="4"> Romance</label>
                        <label class="checkbox-item"><input type="checkbox" name="genres[]" value="5"> Thriller</label>
                        <label class="checkbox-item"><input type="checkbox" name="genres[]" value="6"> Horror</label>
                    </div>
                </div>

                <div class="form-group full-width">
                    <label class="form-label">About</label>
                    <!-- Textarea จะต่างจากเพื่อนตรงที่ไม่มี attribute value ให้เอาข้อความมาใส่ตรงกลางระหว่างแท็กแทน -->
                    <textarea name="about" class="form-textarea" rows="5" required>Description Description Description...</textarea>
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