<?php
// เริ่มต้น session ก่อนใช้งาน $_SESSION
session_start();

// เริ่มต้นการเชื่อมต่อฐานข้อมูล
$conn = mysqli_connect('localhost', 'root', '', 'user'); // สำหรับตาราง users
$conn2 = mysqli_connect('localhost', 'root', '', 'db');  // สำหรับตาราง payments

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ฟังก์ชั่นในการลบข้อมูลผู้ใช้
if (isset($_POST['delete_user'])) {
    $delete_user_id = $_POST['delete_user_id'];
    
    // ตรวจสอบว่าผู้ใช้ไม่สามารถลบตัวเองได้
    if ($delete_user_id == $_SESSION['user_id']) {
        $display_message = "คุณไม่สามารถลบข้อมูลของตัวเองได้";
        echo "<script>alert('$display_message');</script>";
    } else {
        // ลบข้อมูลจากตาราง payments ที่เกี่ยวข้องกับ user_id (ใช้การเชื่อมต่อฐานข้อมูล db)
        $delete_payment_query = "DELETE FROM payments WHERE user_id = ?";
        $stmt = $conn2->prepare($delete_payment_query);  // ใช้การเชื่อมต่อฐานข้อมูล db
        $stmt->bind_param("i", $delete_user_id);
        $stmt->execute();
        
        // ลบผู้ใช้จากตาราง users
        $delete_query = "DELETE FROM users WHERE id = ?";
        $stmt = $conn->prepare($delete_query);  // ใช้การเชื่อมต่อฐานข้อมูล user
        $stmt->bind_param("i", $delete_user_id);

        if ($stmt->execute()) {
            $display_message = "ผู้ใช้ถูกลบเรียบร้อยแล้ว";
            echo "<script>alert('$display_message');</script>";
            // Redirect to prevent resubmission on page refresh
            header("Location: " . $_SERVER['PHP_SELF']); // Refresh the page to stop resubmission
            exit();
        } else {
            $display_message = "เกิดข้อผิดพลาดในการลบผู้ใช้";
            echo "<script>alert('$display_message');</script>";
        }

        $stmt->close();
    }
}

// ดึงข้อมูลจากตาราง users
$query = "SELECT id, username, email, role, fname, lname, address, tel FROM users";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}

?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/styles.css">
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .delete-btn {
            background-color: #dc3545;
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            text-decoration: none;
        }
        .delete-btn:hover {
            background-color: #c82333;
        }
        .logout-btn {
    display: inline-block;        /* ให้เป็นปุ่มแบบอินไลน์ */
    background-color: #007BFF;    /* สีพื้นหลัง */
    color: white;                 /* สีข้อความ */
    padding: 10px 20px;           /* การจัดขนาดปุ่ม */
    border-radius: 5px;           /* ทำให้มุมปุ่มมน */
    text-decoration: none;        /* ลบขีดเส้นใต้จากลิงก์ */
    font-size: 16px;              /* ขนาดตัวอักษร */
    text-align: center;           /* จัดข้อความตรงกลาง */
    cursor: pointer;             /* เปลี่ยนเคอร์เซอร์เมื่อวางบนปุ่ม */
    transition: background-color 0.3s ease;  /* เพิ่มเอฟเฟ็กต์เมื่อมีการ hover */
}

.logout-btn:hover {
    background-color: #0056b3;    /* เปลี่ยนสีพื้นหลังเมื่อ hover */
}

.button-container {
    text-align: center;   /* จัดตำแหน่งปุ่มให้ตรงกลาง */
    margin-top: 20px;      /* เพิ่มระยะห่างด้านบน */
}

    </style>
</head>
<body>
    <div class="container">
        <center><br><br>
        <h1>ข้อมูลผู้ใช้</h1>
        </center><br><br>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>ชื่อ</th>
                    <th>นามสกุล</th>
                    <th>ที่อยู่</th>
                    <th>Role</th>
                    <th>เบอร์โทร</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // แสดงข้อมูลผู้ใช้ที่ดึงจากฐานข้อมูล
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    echo "<td>" . $row['id'] . "</td>";
                    echo "<td>" . $row['username'] . "</td>";
                    echo "<td>" . $row['email'] . "</td>";
                    echo "<td>" . $row['fname'] . "</td>";
                    echo "<td>" . $row['lname'] . "</td>";
                    echo "<td>" . $row['address'] . "</td>";
                    echo "<td>" . $row['role'] . "</td>";
                    echo "<td>" . $row['tel'] . "</td>";
                    // ตรวจสอบว่าไม่ใช่ผู้ใช้ที่กำลังใช้งานอยู่ (ไม่ให้ลบตัวเอง)
                    if ($_SESSION['user_id'] != $row['id']) {
                        echo "<td><form method='POST' action='' onsubmit='return confirm(\"คุณต้องการลบผู้ใช้คนนี้?\")'>
                                <input type='hidden' name='delete_user_id' value='" . $row['id'] . "'>
                                <button type='submit' name='delete_user' class='delete-btn'>Delete</button>
                              </form></td>";
                    } else {
                        echo "<td>ไม่สามารถลบตัวเองได้</td>";
                    }
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
        <center>
        <div class="button-container">
            <a href="/shopping/profile_admin.php" class="logout-btn">Back</a>
        </div>
        </center>

    <?php
    // ปิดการเชื่อมต่อฐานข้อมูล
    mysqli_close($conn);
    mysqli_close($conn2); // ปิดการเชื่อมต่อฐานข้อมูล db
    ?>
</body>
</html>
