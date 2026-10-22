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
    <title>Admin Panel order - Onelight Ebook</title>
    <link rel="stylesheet" href="Foradminstyle.css">
</head>
<body>
    <!-- เมนู Tabs ด้านบน -->
    <div class="admin-tabs">
        <a href="Foradmin.php" class="admin-tab">Book data</a>
        <a href="admin_users.php" class="admin-tab">User data</a>
        <a href="admin_order.php" class="admin-tab active">Order data</a>
    </div>
    <div class="tab-divider"></div>

    <div class="data-box">
<table class="admin-table">
    <thead>
        <tr>
            <th>Order ID</th>
            <th>Customer</th>
            <th>Books in Order</th>
            <th>Total Price</th>
            <th>Status</th>
            <th>Date / Time</th>
        </tr>
    </thead>
    <tbody>
        <?php
        // ดึงข้อมูลจาก eorder เชื่อมกับ euser และดึงรายชื่อหนังสือจาก order_details เชื่อม books
        $sql = "SELECT 
                    o.oid, 
                    o.uid,
                    u.uname, 
                    o.tprice, 
                    o.pstatus, 
                    o.ptime,
                    GROUP_CONCAT(b.bname SEPARATOR '<br>') AS book_list
                FROM eorder o
                LEFT JOIN euser u ON o.uid = u.uid
                LEFT JOIN order_details od ON o.oid = od.oid
                LEFT JOIN books b ON od.bid = b.bid
                GROUP BY o.oid 
                ORDER BY o.oid DESC";
                
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                
                // กำหนดสีให้สถานะการสั่งซื้อ (pstatus) เพื่อให้แอดมินดูง่ายขึ้น
                $status_bg = "#eeeeee"; // ค่าเริ่มต้นสีเทา
                $status = strtolower($row['pstatus']);
                if ($status == 'succeed' || $status == 'paid') {
                    $status_bg = "#d4edda"; // สีเขียว
                } elseif ($status == 'failed') {
                    $status_bg = "#f8d7da"; // สีแดง
                } elseif ($status == 'pending') {
                    $status_bg = "#fff3cd"; // สีเหลือง
                }
        ?>
            <tr>
                <!-- แสดงรหัส Order -->
                <td>#<?php echo htmlspecialchars($row['oid']); ?></td>
                
                <!-- แสดงชื่อลูกค้า (ถ้าไม่มีชื่อให้แสดง UID แทน) -->
                <td><?php echo htmlspecialchars($row['uname'] ?? 'UID: ' . $row['uid']); ?></td>
                
                <!-- แสดงรายชื่อหนังสือ (ใช้ <br> ขั้นบรรทัดที่ได้จาก GROUP_CONCAT) -->
                <td><?php echo $row['book_list'] ?? '-'; ?></td>
                
                <!-- แสดงราคารวม -->
                <td style="font-weight: bold;"><?php echo number_format($row['tprice'] ?? 0, 2); ?> B</td>
                
                <!-- แสดงสถานะพร้อมแถบสี -->
                <td>
                    <span style="padding: 5px 12px; border-radius: 15px; font-size: 14px; background-color: <?php echo $status_bg; ?>;">
                        <?php echo htmlspecialchars($row['pstatus'] ?? '-'); ?>
                    </span>
                </td>
                
                <!-- แสดงเวลา -->
                <td><?php echo htmlspecialchars($row['ptime']); ?></td>
            </tr>
        <?php
            }
        } else {
            // กรณีไม่มีรายการสั่งซื้อ
            echo "<tr><td colspan='6' style='text-align:center;'>No order information available yet</td></tr>";
        }
        ?>
    </tbody>
</table>
</div>