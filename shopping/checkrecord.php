<?php
include 'connect.php';
session_start();

// ตรวจสอบว่า user ได้ล็อกอินหรือไม่
if (!isset($_SESSION['user_id'])) {
    echo "กรุณาล็อกอินเพื่อดูข้อมูลการสั่งซื้อ";
    exit();
}

$user_id = $_SESSION['user_id'];

// ดึงข้อมูลสรุปตามสินค้าแต่ละชนิด
$summary_query = "SELECT product_name, SUM(product_quantity) as total_quantity, SUM(product_quantity * product_price) as total_price 
                  FROM `record` 
                  WHERE user_id='$user_id' 
                  GROUP BY product_name";
$summary_result = mysqli_query($conn, $summary_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Summary</title>
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

        .summary-container {
            margin-bottom: 30px;
        }

        .summary-container h2 {
            text-align: center;
            margin-bottom: 10px;
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
        background-color: #007BFF; /* เปลี่ยนสีตามที่ต้องการ */
        border: none;
        border-radius: 5px;
        text-decoration: none;
        transition: background-color 0.3s;
    }

    .back-button:hover {
        background-color: #0056b3; /* เปลี่ยนสีเมื่อ hover */
    }
    </style>
</head>
<body>

    <div class="container">
        <h1>Order Summary</h1>

        <!-- ตารางสรุปการซื้อสินค้าแต่ละชนิด -->
        <div class="summary-container">
            <h2>Product Summary</h2>
            <table>
                <thead>
                    <tr>
                        <th>Product Name</th>
                        <th>Total Quantity Purchased</th>
                        <th>Total Price</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (mysqli_num_rows($summary_result) > 0) {
                        while ($row = mysqli_fetch_assoc($summary_result)) {
                            echo "<tr>
                                    <td>{$row['product_name']}</td>
                                    <td>{$row['total_quantity']}</td>
                                    <td>" . number_format($row['total_price'], 2) . " BAHT</td>
                                  </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='3' class='no-records'>No order history</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
            <div style="text-align: center; margin-top: 20px;">
                     <a href="index.php" class="back-button">Back to home</a>
                    </div>
        </div>
    </div>

</body>
</html>
