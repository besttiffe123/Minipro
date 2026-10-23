<?php
// โค้ด PHP สำหรับรับข้อมูลและบันทึกลงฐานข้อมูล
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = new mysqli("localhost", "root", "", "ebook");
    $conn->set_charset("utf8mb4");

    if ($conn->connect_error) {
        die("Database connection failed: " . $conn->connect_error);
    }

    // รับค่าจากฟอร์ม
    $uname = trim($_POST['uname']);
    $upass = $_POST['upass'];
    $dob = $_POST['dob'];

    // ตรวจสอบว่ารหัสผ่านมีอย่างน้อย 6 ตัวอักษร
    if (strlen($upass) < 6) {
        echo "<script>
                alert('Password must be at least 6 characters long.');
                history.back();
            </script>";
    exit;
}

    // เช็คว่ามีชื่อผู้ใช้นี้ในระบบแล้วหรือยัง
    $check_stmt = $conn->prepare("SELECT uid FROM euser WHERE uname = ?");
    $check_stmt->bind_param("s", $uname);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
        // ถ้ามีชื่อซ้ำ แจ้งเตือน
        echo "<script>alert('Username already exists. Please choose another username.');</script>";
    } else {
        // ถ้าไม่ซ้ำ ให้เข้ารหัสผ่านและบันทึกลงฐานข้อมูล
        $hashed_password = password_hash($upass, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO euser (uname, upass, dob) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $uname, $hashed_password, $dob);
        
        if ($stmt->execute()) {
            echo "<script>
                    alert('Account created successfully! Please login.');
                    window.location.href = 'login.php';
                  </script>";
        } else {
            echo "<script>alert('Error occurred: " . $stmt->error . "');</script>";
        }
        $stmt->close();
    }
    $check_stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account - Onelight Ebook</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="stylelogin.css">
</head>
<body>

    <div class="login-container">
        
        <!-- ฝั่งซ้าย -->
        <div class="left-side">
            <div class="logo-area">
                <img src="pic/logo.png" alt="Logo" style="width: 50px; height: 50px; margin-right: 10px;">
                <div>
                    <h1>Onelight</h1>
                    <p>Ebook online shop</p>
                </div>
            </div>
            <div class="quote">
                "Ignite your imagination,<br>
                one book at a time."
            </div>
        </div>

        <!-- ฝั่งขวา -->
        <div class="right-side">
            <div class="login-box">
                <div class="login-title">Create account</div>
                
                <!-- เริ่มต้นฟอร์ม (action ปล่อยว่างเพื่อให้ส่งข้อมูลหาตัวเอง) -->
                <form action="" method="POST">
                    
                    <div class="input-group">
                        <input type="text" name="uname" class="input-field" placeholder="Username" required>
                    </div>
                    <div class="input-group">
                        <input type="password" name="upass" class="input-field" placeholder="Password" minlength="6" required>
                    </div>
                    
                    <!-- ช่องกรอกวันเกิดแบบ Dropdown เลือกค่า -->
                    <div class="input-group">
                        <input type="date" name="dob" class="input-field" required>
                    </div>

                    <!-- ปุ่ม Submit เพื่อส่งข้อมูล -->
                    <button type="submit" class="btn-login">Sign up</button>
                    
                </form>
                
                <!-- ลิงก์กลับไปหน้า Login -->
                <div class="signup-text">
                    Already have an account? <a href="login.php" class="signup-link">Login</a>
                </div>
            </div>
        </div>

    </div>

</body>
</html>