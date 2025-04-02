<?php
// เริ่มต้น session
session_start();

// เชื่อมต่อฐานข้อมูล
$conn = mysqli_connect('localhost', 'root', '', 'user');
$conn1 = mysqli_connect('localhost', 'root', '', 'db');

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ตรวจสอบว่าได้ทำการล็อกอินหรือไม่
if (!isset($_SESSION['user_id'])) {
    echo "คุณยังไม่ได้เข้าสู่ระบบ กรุณาล็อกอินก่อน!";
    header("Location: login.php"); // เปลี่ยนเส้นทางไปยังหน้า login หากยังไม่ได้ล็อกอิน
    exit();
}

// ดึงข้อมูลผู้ใช้จากฐานข้อมูล
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT username, email, fname, lname, address, tel FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($username, $email, $fname, $lname, $address, $tel);
$stmt->fetch();
$stmt->close();

// ตรวจสอบเมื่อมีการ submit ฟอร์ม
if (isset($_POST['save_information'])) {
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $address = $_POST['address'];
    $tel = $_POST['tel'];

    // อัปเดตข้อมูลในฐานข้อมูล
    $update_query = $conn->prepare("UPDATE users SET fname = ?, lname = ?, address = ?, tel = ? WHERE id = ?");
    $update_query->bind_param("ssssi", $fname, $lname, $address, $tel, $user_id);
    
    if ($update_query->execute()) {
        $display_message = "ข้อมูลถูกอัปเดตเรียบร้อยแล้ว";
        // อัปเดตค่าที่ถูกส่งกลับในฟอร์ม
        $fname = $fname;
        $lname = $lname;
        $address = $address;
        $tel = $tel;
    } else {
        $display_message = "เกิดข้อผิดพลาดในการอัปเดตข้อมูล";
    }
}

// ดึงข้อมูลประวัติการสั่งซื้อจากฐานข้อมูล
$order_query = $conn1->prepare("SELECT id, payment_method, total_amount, created_at FROM orders WHERE user_id = ?");
$order_query->bind_param("i", $user_id);
$order_query->execute();
$order_result = $order_query->get_result();

// ปิดการเชื่อมต่อฐานข้อมูลหลังจากบันทึกข้อมูลแล้ว
$conn->close();
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>Customer Information</title>
    <link rel="stylesheet" href="css/styles.css">
    <style>
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: #f4f4f4;
    margin: 0;
    padding: 0;
}

h1, h2 {
    color: #333;
    text-align: center;
}

.container {
    max-width: 800px;
    margin: 50px auto;
    padding: 20px;
    background-color: #fff;
    box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.1);
    border-radius: 8px;
}

h2 {
    margin: 15px 0;
}

.input_fields {
    width: calc(100% - 22px);
    padding: 10px;
    margin: 10px 0;
    border: 1px solid #ddd;
    border-radius: 4px;
    box-sizing: border-box;
}

.submit_btn {
    background-color: #007BFF;
    color: white;
    border: none;
    padding: 10px 15px;
    border-radius: 5px;
    cursor: pointer;
    font-size: 16px;
}

.submit_btn:hover {
    background-color: #0056b3;
}

.display_message {
    background-color: #e7f3fe;
    color: #31708f;
    padding: 10px;
    border: 1px solid #bce8f1;
    border-radius: 4px;
    margin-bottom: 15px;
}

.header {
    display: flex;
    align-items: center; /* จัดให้ปุ่มและหัวข้ออยู่ตรงกลางในแนวตั้ง */
    margin-bottom: 20px; /* เว้นระยะด้านล่าง */
}

.back-btn {
    background-color: #007BFF; /* สีของปุ่มกลับ */
    color: white;
    padding: 10px 15px;
    border-radius: 5px;
    text-decoration: none;
    margin-right: 20px; /* เว้นระยะระหว่างปุ่มและหัวข้อ */
}

.button-container {
    display: flex;
    justify-content: flex-end; /* ปุ่มอื่น ๆ ให้อยู่ด้านขวา */
    margin-top: 20px; /* เว้นระยะด้านบน */
}

.btn {
    background-color: #28a745; /* สีของปุ่ม */
    color: white;
    padding: 15px 30px; /* ปรับขนาดปุ่มให้ใหญ่ขึ้น */
    border-radius: 5px;
    text-decoration: none;
    font-size: 16px; /* ขนาดฟอนต์ */
    transition: background-color 0.3s;
}

.btn:hover {
    background-color: #218838; /* เปลี่ยนสีเมื่อ hover */
}


.logout-btn {
    background-color: #dc3545; /* ปุ่มออกจากระบบ */
    color: white;
    padding: 10px 15px;
    border-radius: 5px;
}


    </style>
</head>
<body>
<div class="container"> <!-- Use a consistent container for layout -->
    <a href="index.php" class="back-btn">back</a>
    <div class="profile-content">
        <h1 class="center">Profile</h1>
        <h2><strong>Username:</strong> <?php echo htmlspecialchars($username); ?></h2>

            <center><br><br><br>
            <a href="checkrecord.php" class="btn">check record</a>
            </center>
        </form>

        <div class="button-container">
            <a href="/index.html" class="logout-btn">Log out</a>
        </div>

        <p><?php echo isset($display_message) ? $display_message : ''; ?></p>
    </div>
</div>



</body>
</html>
