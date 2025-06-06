<?php
// เริ่มต้น session
session_start();

// เชื่อมต่อฐานข้อมูล
$conn = mysqli_connect('localhost', 'root', '', 'user');
$conn1 = mysqli_connect('localhost', 'root', '', 'db');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['user_id'])) {
    echo "คุณยังไม่ได้เข้าสู่ระบบ กรุณาล็อกอินก่อน!";
    header("Location: login.php"); // เปลี่ยนเส้นทางไปยังหน้า login หากยังไม่ได้ล็อกอิน
    exit();
}

$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT username, email, fname, lname, address, tel FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($username, $email, $fname, $lname, $address, $tel);
$stmt->fetch();
$stmt->close();

if (isset($_POST['save_information'])) {
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $address = $_POST['address'];
    $tel = $_POST['tel'];

    $update_query = $conn->prepare("UPDATE users SET fname = ?, lname = ?, address = ?, tel = ? WHERE id = ?");
    $update_query->bind_param("ssssi", $fname, $lname, $address, $tel, $user_id);
    
    if ($update_query->execute()) {
        $display_message = "ข้อมูลถูกอัปเดตเรียบร้อยแล้ว";
        $fname = $fname;
        $lname = $lname;
        $address = $address;
        $tel = $tel;
    } else {
        $display_message = "เกิดข้อผิดพลาดในการอัปเดตข้อมูล";
    }
}

$order_query = $conn1->prepare("SELECT id, payment_method, total_amount, created_at FROM orders WHERE user_id = ?");
$order_query->bind_param("i", $user_id);
$order_query->execute();
$order_result = $order_query->get_result();

$conn->close();
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>User Information</title>
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
    background-color: #393e46;
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
    background-color: #222831;
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
    font-family: 'Franklin Gothic Medium', 'Arial Narrow', Arial, sans-serif;
}
.btn {
    background-color: #222831; /* ปุ่มออกจากระบบ */
    color: white;
    padding: 10px 15px;
    border-radius: 5px;
}

.bg_color {
    background-color: white;
}

h2, h1{
    font-family: 'Franklin Gothic Medium', 'Arial Narrow', Arial, sans-serif;
    color: white
}
    </style>
</head>
<body class="bg_color">
<div class="container"> <!-- Use a consistent container for layout -->
    <a href="shop_products.php" class="btn">Back</a>
    <div class="profile-content">
        <h1 class="center">Profile</h1>
        <h2><strong>Username:</strong> <?php echo htmlspecialchars($username); ?></h2>
        <h2><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></h2>
        <h2><strong>Name:</strong> <?php echo htmlspecialchars($fname . ' ' . $lname); ?></h2>
        <h2><strong>Address:</strong> <?php echo htmlspecialchars($address ?? 'ไม่ระบุ'); ?></h2>
        <h2><strong>TEL:</strong> <?php echo htmlspecialchars($tel ?? 'ไม่ระบุ'); ?></h2>

        <!-- ฟอร์มบันทึกข้อมูล -->
        <form method="POST">
            <input type="text" name="fname" placeholder="ชื่อจริง" class="input_fields" value="<?php echo isset($fname) ? htmlspecialchars($fname) : ''; ?>" required>
            <input type="text" name="lname" placeholder="นามสกุล" class="input_fields" value="<?php echo isset($lname) ? htmlspecialchars($lname) : ''; ?>" required>
            <input type="text" name="address" placeholder="ใส่ที่อยู่" class="input_fields" value="<?php echo isset($address) ? htmlspecialchars($address) : ''; ?>" required>
            <input type="tel" name="tel" placeholder="ใส่เบอร์โทรศัพท์" class="input_fields" 
       value="<?php echo isset($tel) ? htmlspecialchars($tel) : ''; ?>" 
       pattern="[0-9]{10}" 
       title="กรุณากรอกเบอร์โทรศัพท์ 10 หลัก" 
       required>

            <input type="submit" name="save_information" class="submit_btn" value="Save information">
            <center><br><br><br>
            <a href="order_history.php" class="btn">Order History</a>
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
