<?php
// เชื่อมต่อฐานข้อมูล
$conn = new mysqli("localhost", "root", "", "ebook");
$conn->set_charset("utf8mb4");

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// ตรวจสอบว่ามีการส่งข้อมูลแบบ POST มาหรือไม่
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // รับค่าจากฟอร์ม
    $uname = trim($_POST['uname']);
    $upass = $_POST['upass'];
    $dob = $_POST['dob'];

    // เข้ารหัสผ่าน (Hashing) เพื่อความปลอดภัย ห้ามเก็บรหัสผ่านเป็นข้อความธรรมดา
    $hashed_password = password_hash($upass, PASSWORD_DEFAULT);

    // เตรียมคำสั่ง SQL (ใช้ ? เพื่อป้องกัน SQL Injection)
    $stmt = $conn->prepare("INSERT INTO euser (uname, upass, dob) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $uname, $hashed_password, $dob);

    // ทำงานคำสั่ง SQL
    if ($stmt->execute()) {
        // บันทึกสำเร็จ ให้เด้งกลับไปหน้าตารางผู้ใช้ (ปรับชื่อไฟล์ปลายทางตามที่คุณตั้งไว้)
        echo "<script>
                alert('New user added successfully');
                window.location.href = 'foradmin.php'; 
              </script>";
    } else {
        echo "Error occurred: " . $stmt->error;
    }

    $stmt->close();
}
$conn->close();
?>