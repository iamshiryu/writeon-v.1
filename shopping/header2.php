<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Write On</title>
    
<style>
    /* CSS สำหรับจัดรูปให้ชิดซ้ายและเมนูให้ตรงกลาง */
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
    width: 90px; /* ขนาดของโลโก้ */
    height: auto; /* ปรับความสูงตามสัดส่วน */
    margin-bottom: 0px;
}

.navbar {
        display: flex;
        justify-content: flex-end; /* ทำให้เมนูอยู่ทางขวาสุด */
        flex: 1; /* ทำให้ navbar ขยายเต็มที่ */
    }

.navbar-start {
        display: flex;
        justify-content: flex-start; 
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
        
        <!-- เมนูจะอยู่ตรงกลาง -->
        <nav class="navbar">
            <a href="index.php" class="navbar-start">Admin Dashboard</a>
            <a href="index.php">Add Products</a>
            <a href="view_products.php">View Products</a>
            <!-- <a href="checkrecord.php">Check Record</a> -->
            <a href="profile_admin.php">Profile</a>
        </nav>

        <?php 
            $select_product=mysqli_query($conn,"Select * from `cart`") or die("query failed");
            $row_count=mysqli_num_rows($select_product);
        ?>
    </div>
</header>

</body>
</html>
