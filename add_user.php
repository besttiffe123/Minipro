<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New User - Admin Panel</title>
    <link rel="stylesheet" href="Foradminstyle.css">
</head>
<body>
    <div class="form-container">
        <div class="form-title">Add New User</div>

        <form action="insert_user_backend.php" method="POST">
            <div class="form-grid">
                <!-- Username (uname) -->
                <div class="form-group full-width">
                    <label class="form-label">Username</label>
                    <input type="text" name="uname" class="form-input" required>
                </div>

                <!-- Password (upass) -->
                <div class="form-group full-width">
                    <label class="form-label">Password</label>
                    <input type="password" name="upass" class="form-input" required>
                </div>

                <!-- Date of Birth (dob) -->
                <div class="form-group full-width">
                    <label class="form-label">Date of Birth</label>
                    <!-- ใช้ type="date" สำหรับคอลัมน์ dob -->
                    <input type="date" name="dob" class="form-input" required>
                </div>
            </div>

            <div class="form-actions">
                <a href="admin_users.php" class="btn-cancel">Cancel</a>
                <button type="submit" class="btn-save">Save User</button>
            </div>
        </form>
    </div>
</body>
</html>