<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Write On</title>

    <style>
    /* CSS สำหรับจัดรูปให้ชิดซ้ายและเมนูให้ตรงขวา */
    .header {
        display: flex;
        justify-content: space-between; /* รูปอยู่ซ้ายสุด */
        align-items: center; /* จัดตำแหน่งให้ทุกอย่างอยู่ตรงกลางแนวตั้ง */
    }

    .header_body {
        width: 100%;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .logo {
        width: 450px; /* ขนาดของโลโก้ */
        height: auto; /* ปรับความสูงตามสัดส่วน */
        margin-bottom: 0px;
    }

    .navbar {
        display: flex;
        justify-content: flex-end; /* ทำให้เมนูอยู่ทางขวาสุด */
        flex: 1; /* ทำให้ navbar ขยายเต็มที่ */
    }

    .navbar a {
        margin: 0 15px;
        text-decoration: none;
        color: #333;
        font-size: 16px;
    }

    .navbar a:hover {
        color: #072227;
    }
    </style>

</head>
<body>

<header class="header">
    <div class="header_body">
        <!-- ใช้รูปโลโก้แทนข้อความ -->
        <a href="shop_products.php" class="logo">
            <img src="/logo.png" alt="Write On Logo" />
        </a>
        <nav class="navbar">
            <!-- <a href="index.php">Add Products</a> -->
            <a href="profile.php">Profile</a>
            <a href="shop_products.php"></a>
        </nav>

        <?php 
        include 'connect.php'; // รวมไฟล์การเชื่อมต่อฐานข้อมูล
        
        // ตรวจสอบว่ามีการเริ่มเซสชันหรือไม่
        if (session_status() == PHP_SESSION_NONE) {
            session_start(); // เริ่ม session ถ้ายังไม่มีการเริ่ม
        }
        
        // ตรวจสอบว่าผู้ใช้ล็อกอินอยู่หรือไม่
        if (isset($_SESSION['user_id'])) {
            $user_id = $_SESSION['user_id'];
            // ค้นหาสินค้าจากตะกร้าสำหรับ user_id ที่ตรงกัน
            $select_product = mysqli_query($conn, "SELECT * FROM `cart` WHERE user_id = '$user_id'") or die("query failed");
            $row_count = mysqli_num_rows($select_product);
        } else {
            $row_count = 0; // ถ้าผู้ใช้ยังไม่ล็อกอิน ให้จำนวนสินค้าเป็น 0
        }
        ?>

        <a href="cart.php" class="cart"><i class="fa-solid fa-cart-shopping"></i><span><sup><?php echo $row_count ?></sup></span></a>
        <div id="menu-btn" class="fas fa-bars"></div>
    </div>
</header>

</body>
</html>
