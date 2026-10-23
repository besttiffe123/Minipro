<?php
// 1. เชื่อมต่อฐานข้อมูล
$conn = new mysqli("localhost", "root", "", "ebook");
$conn->set_charset("utf8mb4");

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// 2. รับค่า ID ผู้ใช้จาก URL
if (isset($_GET['id'])) {
    $uid = intval($_GET['id']);
    
    // 3. ดึงข้อมูลผู้ใช้จากตาราง euser
    $sql = "SELECT * FROM euser WHERE uid = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $uid);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc(); // เก็บข้อมูลผู้ใช้ไว้ในตัวแปร $user
    } else {
        echo "<script>alert('User not found'); window.location.href='foradmin.php';</script>";
        exit();
    }
    $stmt->close();
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
    <title>Modify User - Admin Panel</title>
    <link rel="stylesheet" href="Foradminstyle.css">
</head>
<body>
    <div class="form-container">
        <div class="form-title">Modify User Data</div>

        <form action="update_user_backend.php" method="POST">
            
            <!-- ดึงรหัส uid จากฐานข้อมูลมาใส่ในช่องซ่อน -->
            <input type="hidden" name="uid" value="<?php echo $user['uid']; ?>"> 
            
            <div class="form-grid">
                <!-- Username (uname) -->
                <div class="form-group full-width">
                    <label class="form-label">Username</label>
                    <!-- ดึงชื่อผู้ใช้เดิมมาแสดง -->
                    <input type="text" name="uname" class="form-input" value="<?php echo htmlspecialchars($user['uname']); ?>" required>
                </div>

                <!-- Password (upass) -->
                <div class="form-group full-width">
                    <label class="form-label">New Password</label>
                    <!-- ช่องรหัสผ่านปล่อยว่างไว้เหมือนเดิม ถ้าไม่พิมพ์อะไร ระบบจะไม่เปลี่ยนรหัสให้ -->
                    <input type="password" name="upass" class="form-input">
                    <div class="current-file">Leave blank to keep current password</div>
                </div>

                <!-- Date of Birth (dob) -->
                <div class="form-group full-width">
                    <label class="form-label">Date of Birth</label>
                    <!-- ดึงวันเกิด (YYYY-MM-DD) จากฐานข้อมูลมาใส่ ปฏิทินจะแสดงวันที่ของคนนี้อัตโนมัติ -->
                    <input type="date" name="dob" class="form-input" value="<?php echo $user['dob']; ?>" required>
                </div>
            </div>

            <div class="form-actions">
                <a href="foradmin.php" class="btn-cancel">Cancel</a>
                <button type="submit" class="btn-save">Update User</button>
            </div>
        </form>
    </div>
</body>
</html>