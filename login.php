<?php
session_start(); // เริ่มต้น session เพื่อจดจำการล็อกอินของผู้ใช้

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = new mysqli("localhost", "root", "", "ebook");
    $conn->set_charset("utf8mb4");

    if ($conn->connect_error) {
        die("Database connection failed: " . $conn->connect_error);
    }

    // รับค่าจากฟอร์ม
    $uname = trim($_POST['uname']);
    $upass = $_POST['upass'];

    // ค้นหาชื่อผู้ใช้ในฐานข้อมูล
    $stmt = $conn->prepare("SELECT uid, upass FROM euser WHERE uname = ?");
    $stmt->bind_param("s", $uname);
    $stmt->execute();
    $result = $stmt->get_result();

    // เช็คว่ามีชื่อผู้ใช้นี้หรือไม่
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        
        // ตรวจสอบรหัสผ่านว่าตรงกับที่เข้ารหัสไว้ในฐานข้อมูลหรือไม่
        if (password_verify($upass, $row['upass'])) {
            // ถ้ารหัสผ่านถูกต้อง ให้เก็บข้อมูลลง Session
            $_SESSION['uid'] = $row['uid'];
            $_SESSION['uname'] = $uname;

            echo "<script>
                    alert('Login successful!');
                    window.location.href = 'index.php';
                  </script>";
        } else {
            // ถ้ารหัสผ่านผิด
            echo "<script>alert('This password is incorrect!');</script>";
        }
    } else {
        // ถ้าไม่พบชื่อผู้ใช้
        echo "<script>alert('This username was not found in the system.!');</script>";
    }

    $stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Onelight Ebook</title>
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
                <div class="login-title">Login</div>
                
                <!-- ฟอร์มกรอกข้อมูล (เปลี่ยนจากแท็กเปล่าๆ เป็น <form>) -->
                <form action="" method="POST">
                    <div class="input-group">
                        <input type="text" name="uname" class="input-field" placeholder="Username" required>
                    </div>
                    <div class="input-group">
                        <input type="password" name="upass" class="input-field" placeholder="Password" required>
                    </div>
                    
                    <!-- ปุ่มกดเข้าสู่ระบบ เปลี่ยนเป็น submit -->
                    <button type="submit" class="btn-login">Login</button>
                </form>
                
                <div class="signup-text">
                    Don't have account yet? <a href="Signin.php" class="signup-link">Sign up</a>
                </div>
            </div>
        </div>

    </div>

</body>
</html>