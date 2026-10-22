<?php
$conn = new mysqli("localhost", "root", "", "ebook");
$conn->set_charset("utf8mb4");
if ($conn->connect_error) {
    die("เชื่อมต่อฐานข้อมูลล้มเหลว: " . $conn->connect_error);
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel user - Onelight Ebook</title>
    <link rel="stylesheet" href="Foradminstyle.css">
</head>
<body>

    <!-- เมนู Tabs ด้านบน -->
    <div class="admin-tabs">
        <a href="Foradmin.php" class="admin-tab">Book data</a>
        <a href="admin_users.php" class="admin-tab active">User data</a>
        <a href="admin_order.php" class="admin-tab">Order data</a>
    </div>
    <div class="tab-divider"></div>

<div class="data-box">
<table class="admin-table">
    <thead>
        <tr>
            <th>UID</th>
            <th>Username</th>
            <th>Password (Hashed)</th>
            <th>Date of Birth</th>
        </tr>
    </thead>
    <tbody>
        <?php
        // ดึงข้อมูลผู้ใช้ทั้งหมดจากตาราง euser
        $sql = "SELECT * FROM euser ORDER BY uid DESC";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
        ?>
            <tr>
                <td><?php echo htmlspecialchars($row['uid'] ?? '-'); ?></td>
                <td><?php echo htmlspecialchars($row['uname'] ?? '-'); ?></td>
                <td><?php echo htmlspecialchars($row['upass'] ?? '-'); ?></td>
                <td><?php echo htmlspecialchars($row['dob'] ?? '-'); ?></td>
                
                <td>
                    <div class="action-buttons">
                        <!-- แนบรหัสผู้ใช้ (uid) ไปกับปุ่ม modify และ delete -->
                        <a href="modify_user.php?id=<?php echo $row['uid']; ?>" class="btn-modify" style="text-decoration:none;">modify</a>
                        <a href="delete_user_backend.php?id=<?php echo $row['uid']; ?>" class="btn-delete" style="text-decoration:none;" onclick="return confirm('Are you sure you want to delete this user?');">delete</a>
                    </div>
                </td>
            </tr>
        <?php
            }
        } else {
            echo "<tr><td colspan='5' style='text-align:center;'>No user yet</td></tr>";
        }
        ?>
    </tbody>
</table>
</div>

<!-- ปุ่ม Add new data สำหรับหน้า User -->
<div class="add-data-container">
    <a href="add_user.php">
        <button class="btn-add-data">Add new user</button>
    </a>
</div>