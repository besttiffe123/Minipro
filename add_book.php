<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Book - Admin Panel</title>
    <link rel="stylesheet" href="Foradminstyle.css">
</head>
<body>

    <div class="form-container">
        <div class="form-title">Add New Book</div>

        <!-- 
          จุดสำคัญ: ต้องมี enctype="multipart/form-data" 
          ไม่อย่างนั้นจะอัปโหลดไฟล์รูปภาพหรือไฟล์ PDF ไม่ได้ 
        -->
          
        <form action="insert_book_backend.php" method="POST" enctype="multipart/form-data">
            
            <div class="form-grid">
                <!-- Book Name -->
                <div class="form-group">
                    <label class="form-label">Book Name</label>
                    <input type="text" name="bname" class="form-input" required>
                </div>

                <!-- Author -->
                <div class="form-group">
                    <label class="form-label">Author</label>
                    <input type="text" name="author" class="form-input" required>
                </div>

                <!-- Type -->
                <div class="form-group">
                    <label class="form-label">Type</label>
                    <select name="type" class="form-select" required>
                        <option value="" disabled selected>Select type...</option>
                        <option value="novel">Novel</option>
                        <option value="comic">Comic</option>
                        <option value="manga">Manga</option>
                        <option value="artbook">Artbook</option>
                    </select>
                </div>

                <!-- Pages & Price -->
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Pages</label>
                        <input type="number" name="pages" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Price (B)</label>
                        <input type="number" step="0.01" name="price" class="form-input" required>
                    </div>
                </div>

                <!-- Cover Image Upload -->
                <div class="form-group">
                    <label class="form-label">Cover Image (.jpg, .png)</label>
                    <input type="file" name="bcover" class="form-input" accept="image/*" required>
                </div>

                <!-- Book File Upload -->
                <div class="form-group">
                    <label class="form-label">Book File (.pdf, .epub)</label>
                    <input type="file" name="bfile" class="form-input" accept=".pdf,.epub" required>
                </div>

                <!-- Genres (Checkbox หลายตัวเลือก) -->
                <div class="form-group full-width">
                    <label class="form-label">Genres (Select multiple)</label>
                    <div class="checkbox-group">
                        <!-- เราใช้ name="genres[]" (มีก้ามปู) เพื่อส่งค่าไปหลังบ้านเป็นแบบ Array (เลือกได้หลายอัน) -->
                        <label class="checkbox-item"><input type="checkbox" name="genres[]" value="1"> Action</label>
                        <label class="checkbox-item"><input type="checkbox" name="genres[]" value="2"> Sci-fi</label>
                        <label class="checkbox-item"><input type="checkbox" name="genres[]" value="3"> Fantasy</label>
                        <label class="checkbox-item"><input type="checkbox" name="genres[]" value="4"> Romance</label>
                        <label class="checkbox-item"><input type="checkbox" name="genres[]" value="5"> Thriller</label>
                        <label class="checkbox-item"><input type="checkbox" name="genres[]" value="6"> Horror</label>
                    </div>
                </div>

                <!-- About / Description -->
                <div class="form-group full-width">
                    <label class="form-label">About</label>
                    <textarea name="about" class="form-textarea" rows="5" required></textarea>
                </div>
            </div>

            <!-- ปุ่มกดยืนยันและยกเลิก -->
            <div class="form-actions">
                <a href="foradmin.php" class="btn-cancel">Cancel</a>
                <button type="submit" class="btn-save">Save Book</button>
            </div>

        </form>
    </div>

</body>
</html>