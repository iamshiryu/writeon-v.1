<?php
// เริ่มต้น session
session_start();

// เชื่อมต่อฐานข้อมูล
$conn = new mysqli('localhost', 'root', '', 'user');

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ตรวจสอบเมื่อมีการ submit ฟอร์ม
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // กำหนด role เป็น 'buyer' โดยอัตโนมัติ
    $role = 'user';

    // เข้ารหัสรหัสผ่านก่อนเก็บลงฐานข้อมูล
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // ตรวจสอบว่าผู้ใช้งานมี username หรือ email ซ้ำกันอยู่หรือไม่
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo "<script>alert('ชื่อผู้ใช้หรืออีเมลนี้ถูกใช้งานแล้ว');</script>";
    } else {
        // บันทึกข้อมูลผู้ใช้ใหม่
        $stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $username, $email, $hashed_password, $role);

        if ($stmt->execute()) {
            echo "<script>alert('สมัครสมาชิกสำเร็จ!'); window.location.href='index.html';</script>";
            exit; // ใช้ exit เพื่อหยุดการทำงานของสคริปต์
        } else {
            echo "<script>alert('เกิดข้อผิดพลาด: " . $conn->error . "');</script>";
        }
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สมัครสมาชิก</title>
</head>
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

        .margin-bottom {
            margin-bottom: -5px;
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
<body class="bg_img">
<section class="container">
<div class="display_product">
    <div class="edit_form">
    <h1>Register</h1>
    <form action="register.php" method="POST">
        <label for="email" >Email:</label>
        <input type="email"  class="margin-bottom" name="email" required><br>

        <label for="username">Username:</label>
        <input type="text" class="margin-bottom" name="username" required><br>

        <label for="password">Password:</label>
        <input type="password" class="margin-bottom" name="password" required><br>

        <button type="submit" class="margin-top login-btn">Register</button>
        <button type="button" class="login-btn margin-top" onclick="window.history.back();" class="margin-top">
        <i class="fas fa-arrow-left"></i> Back
        </button>
    </form>
    </div>
    </div>
    </section>
</body>
</html>
