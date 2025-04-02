<?php
include 'connect.php';
$conn1 = mysqli_connect('localhost', 'root', '', 'user');
$conn = mysqli_connect('localhost', 'root', '', 'db');

session_start();

if (!isset($_SESSION['user_id'])) {
    echo "กรุณาล็อกอินเพื่อทำการชำระเงิน";
    exit();
}

// ดึงข้อมูลผู้ใช้
$user_id = $_SESSION['user_id'];
$user_query = mysqli_query($conn1, "SELECT * FROM `users` WHERE id='$user_id'");
$user_data = mysqli_fetch_assoc($user_query);

// ดึงข้อมูลสินค้าจากตะกร้า
$cart_query = mysqli_query($conn, "SELECT * FROM `cart` WHERE user_id='$user_id'");
$total_amount = 0;

if (mysqli_num_rows($cart_query) > 0) {
    while ($item = mysqli_fetch_assoc($cart_query)) {
        $total_amount += $item['price'] * $item['quantity'];
    }
} else {
    echo "<div class='empty_text'>ตะกร้าสินค้าเปล่า</div>";
    exit();
}

if (isset($_POST['payment_method'])) {
    $payment_method = $_POST['payment_method'];

    // บันทึกข้อมูลการสั่งซื้อ
    $order_query = $conn->prepare("INSERT INTO orders (user_id, payment_method, total_amount) VALUES (?, ?, ?)");
    $order_query->bind_param("isd", $user_id, $payment_method, $total_amount);
    
    if ($order_query->execute()) {
        echo "<script>alert('Payment Succesfull');</script>";
    } else {
        echo "<script>alert('เกิดข้อผิดพลาดในการบันทึกข้อมูลการสั่งซื้อ');</script>";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    
    <style>
        /* styles.css */

        body {
            font-family: Arial, sans-serif;
            background-color: #f8f8f8;
            margin: 0;
            padding: 20px;
        }

        .heading {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }

        .center-text {
            background-color: #fff; /* สีพื้นหลังขาว */
            border-radius: 8px; /* มุมโค้ง */
            padding: 20px; /* ระยะห่างภายใน */
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); /* เงา */
            max-width: 600px; /* กำหนดความกว้างสูงสุด */
            margin: 0 auto; /* จัดกลาง */
        }

        .center-text h2 {
            color: #444; /* สีข้อความ */
            margin: 10px 0; /* ระยะห่างบนล่าง */
        }

        label {
            display: block; /* จัดให้เป็นบล็อก */
            margin-top: 20px; /* ระยะห่างด้านบน */
            font-weight: bold; /* ตัวหนา */
        }

        select {
            width: 50%; /* กำหนดความกว้างเต็ม */
            padding: 10px; /* ระยะห่างภายใน */
            border: 1px solid #ccc; /* ขอบสีเทา */
            border-radius: 5px; /* มุมโค้ง */
            margin-top: 5px; /* ระยะห่างด้านบน */
        }

        .btn {
            display: inline-block; /* แสดงเป็นบล็อก */
            margin-top: 20px; /* ระยะห่างด้านบน */
            padding: 10px 20px; /* ระยะห่างภายใน */
            background-color: #072227; /* สีพื้นหลัง */
            color: white; /* สีข้อความ */
            border: none; /* ไม่มีขอบ */
            border-radius: 5px; /* มุมโค้ง */
            cursor: pointer; /* เปลี่ยน cursor เมื่อ hover */
            text-align: center; /* จัดกลาง */
            transition: background-color 0.3s; /* เปลี่ยนสีเมื่อ hover */
        }

        .btn:hover {
            background-color: #35858b; /* สีพื้นหลังเมื่อ hover */
        }

        .margin-top {
            margin-top: 20px;
        }

        .margin-left {
            margin-left: 10px;
        }

        .container {
            background-color: var(--lightgray);
        }

        .heading {
            text-align: center;
            font-size: 3rem;
            text-transform: uppercase;
            padding: 1.5rem 0 2rem;
            color: var(--color1);
        }

        .bottom_btn {
            color: var(--black);
            font-size: 1.3rem;
            background-color: var(--color4);
            padding: 1rem;
            margin: 2rem 0;
        }

        .btns {
    display: flex;
    justify-content: center;
    align-items: center;
    width: 100%;
  }

    </style>
</head>
<body>
    <div>
        <h1 class="heading">Checkout</h1>
        <section>
            <center>
                <div class="center-text">  
                    <h2>User Information</h2>
                    <h2>Name: <?php echo $user_data['fname'] . ' ' . $user_data['lname']; ?></h2>
                    <h2>Tel: <?php echo $user_data['tel']; ?></h2>
                    <h2>Address: <?php echo $user_data['address'];?></h2>
                    <h2>Total: <span><?php echo number_format($total_amount); ?> BAHT</span></h2>
                </div>
                <form action="process_payment.php" method="POST">
                    <label for="payment_method" class="center-text margin-top">Choose payment method: <select id="payment_method" name="payment_method" class="margin-left" required>
                        <option value="cash_on_delivery">Cash on delivery <input type="submit" value="Pay" class="btn margin-left"></option>
                    </select></label><br>
                    <a href="cart.php" class="btn"><i class="fas fa-arrow-left"></i> Back </a>
                    <input type="hidden" name="total_amount" value="<?php echo $total_amount; ?>">
                </form>
            </center>
        </section>
    </div>
</body>
</html>
