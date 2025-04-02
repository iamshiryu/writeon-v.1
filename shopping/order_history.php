<?php
// เริ่มต้น session
session_start();

// เชื่อมต่อฐานข้อมูล
$conn = mysqli_connect('localhost', 'root', '', 'user');
$conn1 = mysqli_connect('localhost', 'root', '', 'db'); // ใช้ shopping_cart สำหรับข้อมูลการสั่งซื้อและการชำระเงิน
$conn2 = mysqli_connect('localhost', 'root', '', 'db'); // แก้ไขให้เชื่อมต่อกับ shopping_cart แทน payments

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error || $conn2->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ตรวจสอบว่าได้ทำการล็อกอินหรือไม่
if (!isset($_SESSION['user_id'])) {
    echo "คุณยังไม่ได้เข้าสู่ระบบ กรุณาล็อกอินก่อน!";
    header("Location: login.php");
    exit();
}

// ดึงข้อมูลผู้ใช้จากฐานข้อมูล
$user_id = $_SESSION['user_id'];

// ดึงข้อมูลประวัติการสั่งซื้อจากฐานข้อมูล
$order_query = $conn1->prepare("SELECT id, payment_method, total_amount, created_at FROM orders WHERE user_id = ?");
$order_query->bind_param("i", $user_id);
$order_query->execute();
$order_result = $order_query->get_result();

// ดึงข้อมูลการชำระเงินจากตาราง payments ในฐานข้อมูล shopping_cart
$payment_query = $conn2->prepare("SELECT id, payment_method, total_amount, order_number, created_at FROM payments WHERE user_id = ?");
$payment_query->bind_param("i", $user_id);
$payment_query->execute();
$payment_result = $payment_query->get_result();
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order History</title>
    <link rel="stylesheet" href="css/styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 1000px;
            margin: 50px auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 12px;
            text-align: center;
        }

        th {
            background-color: #f8f8f8;
            color: #333;
        }

        .no-records {
            text-align: center;
            color: #666;
            font-size: 18px;
            padding: 20px;
        }

        .back-button {
            display: inline-block;
            padding: 10px 20px;
            font-size: 16px;
            color: #fff;
            background-color: #007BFF;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            transition: background-color 0.3s;
        }

        .back-button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

    <div class="container">
        <h1>Order History</h1>

        <h2>Payment History:</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Order ID</th>
                    <th>Payment method</th>
                    <th>Total</th>
                    <th>Date created</th>
                </tr>
            </thead>
            <tbody>
            <?php if ($payment_result->num_rows > 0): ?>
                <?php while ($payment = $payment_result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($payment['id']); ?></td>
                        <td><?php echo htmlspecialchars($payment['order_number']); ?></td>
                        <td><?php echo htmlspecialchars($payment['payment_method']); ?></td>
                        <td><?php echo isset($payment['total_amount']) ? htmlspecialchars(number_format($payment['total_amount'], 2)) : '0.00'; ?> BAHT</td>
                        <td><?php echo htmlspecialchars(date('Y-m-d H:i:s', strtotime($payment['created_at']))); ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="5" class="no-records">No payment History</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
        
        <div style="text-align: center; margin-top: 20px;">
            <a href="profile.php" class="back-button">back to profile</a>
        </div>
    </div>

</body>
</html>

<?php
// ปิดการเชื่อมต่อฐานข้อมูล
$conn->close();
$conn1->close();
$conn2->close();
?>
