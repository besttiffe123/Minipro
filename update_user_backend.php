<?php
// เชื่อมต่อฐานข้อมูล
$conn = new mysqli("localhost", "root", "", "ebook");
$conn->set_charset("utf8mb4");

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // รับค่าจากฟอร์ม
    $uid = $_POST['uid'];
    $uname = trim($_POST['uname']);
    $upass = $_POST['upass']; // อาจจะว่างหรือไม่ว่าง
    $dob = $_POST['dob'];

    // เช็คว่าผู้ใช้กรอกรหัสผ่านช่อง New Password มาหรือไม่
    if (!empty($upass)) {
        // กรณีมีการเปลี่ยนรหัสผ่านใหม่
        $hashed_password = password_hash($upass, PASSWORD_DEFAULT);
        
        $stmt = $conn->prepare("UPDATE euser SET uname = ?, upass = ?, dob = ? WHERE uid = ?");
        $stmt->bind_param("sssi", $uname, $hashed_password, $dob, $uid);
    } else {
        // กรณีไม่เปลี่ยนรหัสผ่าน (อัปเดตแค่ชื่อและวันเกิด)
        $stmt = $conn->prepare("UPDATE euser SET uname = ?, dob = ? WHERE uid = ?");
        $stmt->bind_param("ssi", $uname, $dob, $uid);
    }

    // ทำงานคำสั่ง SQL
    if ($stmt->execute()) {
        echo "<script>
                alert('User information has been updated');
                window.location.href = 'foradmin.php';
              </script>";
    } else {
        echo "Error occurred: " . $stmt->error;
    }

    $stmt->close();
}
$conn->close();
?>