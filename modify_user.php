<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modify User - Admin Panel</title>
    <link rel="stylesheet" href="Foradminstyle.css">
</head>
<body>
    <div class="form-container">
        <div class="form-title">Modify User Data</div>

        <form action="update_user_backend.php" method="POST">
            
            <!-- ซ่อน uid ไว้สำหรับอ้างอิงตอนส่งคำสั่ง UPDATE ไปยังฐานข้อมูล -->
            <input type="hidden" name="uid" value="1"> 
            
            <div class="form-grid">
                <!-- Username (uname) -->
                <div class="form-group full-width">
                    <label class="form-label">Username</label>
                    <input type="text" name="uname" class="form-input" value="User1" required>
                </div>

                <!-- Password (upass) -->
                <div class="form-group full-width">
                    <label class="form-label">New Password</label>
                    <!-- เอา required ออก เพื่อให้ผู้ใช้เลือกได้ว่าจะเปลี่ยนรหัสหรือไม่ -->
                    <input type="password" name="upass" class="form-input">
                    <div class="current-file">Leave blank to keep current password</div>
                </div>

                <!-- Date of Birth (dob) -->
                <div class="form-group full-width">
                    <label class="form-label">Date of Birth</label>
                    <!-- ใส่ value เป็นรูปแบบ YYYY-MM-DD เพื่อให้ปฏิทินแสดงค่าเดิม -->
                    <input type="date" name="dob" class="form-input" value="2000-01-01" required>
                </div>
            </div>

            <div class="form-actions">
                <a href="admin_users.php" class="btn-cancel">Cancel</a>
                <button type="submit" class="btn-save">Update User</button>
            </div>
        </form>
    </div>
</body>
</html>