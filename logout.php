<?php
session_start();
$_SESSION = array(); // เคลียร์ข้อมูลที่จำไว้ทั้งหมด
session_destroy(); // ทำลายระบบบัตรผ่าน (Session) ทิ้ง
header("Location: login.php"); // เตะกลับไปหน้าล็อกอิน
exit();
?>  