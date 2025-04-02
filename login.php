<?php
// เริ่มต้น session
session_start();

// เปิดการรายงานข้อผิดพลาด
error_reporting(E_ALL);
ini_set('display_errors', 1);

// เชื่อมต่อฐานข้อมูล
$conn = new mysqli('localhost', 'root', '', 'user');

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ตรวจสอบเมื่อมีการ submit ฟอร์ม
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // ตรวจสอบว่าผู้ใช้งานมีอยู่ในระบบหรือไม่
    $stmt = $conn->prepare("SELECT id, password, role FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // ผู้ใช้งานมีอยู่ในระบบ ดึงข้อมูลรหัสผ่านและ role เพื่อตรวจสอบ
        $stmt->bind_result($id, $hashed_password, $role);
        $stmt->fetch();

        // ตรวจสอบรหัสผ่าน
        if (password_verify($password, $hashed_password)) {
            // เข้าสู่ระบบสำเร็จ
            $_SESSION['user_id'] = $id;

            // ตรวจสอบ role ว่าเป็น admin หรือไม่
            if ($role === 'admin') {
                echo "<script>
                        alert('คุณเป็นแอดมิน กรุณาเข้าสู่ระบบแบบ admin');
                        window.location.href = 'index.html';
                      </script>";
                exit;
            } else {
                header("Location: shopping/shop_products.php");
                exit;
            }
        } else {
            echo "<script>alert('รหัสผ่านไม่ถูกต้อง');</script>";
        }
    } else {
        echo "<script>alert('ไม่พบผู้ใช้นี้ในระบบ');</script>";
    }

    $stmt->close();
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="th">
<head>
    <link rel="stylesheet" href="css/styles.css">
    <meta charset="UTF-8">
    <title>Log in</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
       
       .margin-top{
            margin-top:10px;
        }
        /* สไตล์สำหรับปุ่ม */
        .login-btn {
            padding: 10px 20px;
            background-color: #222831;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 18px;
            font-family: 'Franklin Gothic Medium', 'Arial Narrow', Arial, sans-serif;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .login-btn:hover {
            background-color: #393e46;
        }

        .bg_img {
            background-image: url(asset/bg.jpg);
            background-size: cover;
        }

        h1 {
            color: white;
            text-shadow: black 0.1em 0.1em 0.2em
            
        }

        .container_index {
            background-color: black;
        }
        
        label {
            font-family: 'Franklin Gothic Medium', 'Arial Narrow', Arial, sans-serif;
            color: white;
            text-shadow: black 0.1em 0.1em 0.2em
        }
        .edit_form {
            text-align: center;
    padding: 2rem;
    box-shadow: var(--black);
    border: var(--border);
    box-sizing: 10px;
    border-style: outset;
    border-radius: 0.5rem;
    background-color: var(--black);
    border-color: gray; 
    box-shadow: 2px 5px 14px #888888;
    backdrop-filter: blur(5px); /* ปรับค่าตามที่ต้องการ */
        }
    </style>
</head>
<body class="bg_img">
    <section class="container">
        <div class="display_product">
            <div class="edit_form">
    <h1>Log In</h1>
    <form action="login.php" method="POST">
        <label for="username" class="margin-top">Username:</label>
        <input type="text" name="username" required><br>

        <label for="password">Password:</label>
        <input type="password" name="password" required><br>

        <button type="submit" class="login-btn">Login</button>

        <button type="button" class="login-btn margin-top" onclick="window.history.back();" class="margin-top">
        <i class="fas fa-arrow-left"></i> Back
        </button>
        </div>
        </div>
        </section>
    </form>
</body>
</html>
