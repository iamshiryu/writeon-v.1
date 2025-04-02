<?php
include 'connect.php';
session_start(); // ตรวจสอบให้แน่ใจว่าได้เริ่ม session

if (isset($_POST['add_to_cart'])) {
    $products_name = $_POST['product_name'];
    $products_price = $_POST['product_price'];
    $products_image = $_POST['product_image'];
    $products_quantity = $_POST['product_quantity'];
    $product_quantity = 1;

    // ตรวจสอบว่าผู้ใช้ล็อกอินอยู่หรือไม่
    if (!isset($_SESSION['user_id'])) {
        $display_message[] = "กรุณาเข้าสู่ระบบเพื่อเพิ่มสินค้าลงในตะกร้า";
    } else {
        $user_id = $_SESSION['user_id']; // ดึง user_id จาก session

        $select_cart = mysqli_query($conn, "SELECT * FROM `cart` WHERE name='$products_name' AND user_id='$user_id'");
        if (mysqli_num_rows($select_cart) > 0) {
            $display_message[] = "Product already added to cart"; 
        } else {
            // ใช้ prepared statements
            $insert_query = $conn->prepare("INSERT INTO `cart` (name, price, image, quantity, user_id) VALUES (?, ?, ?, ?, ?)");
            $insert_query->bind_param("ssssi", $products_name, $products_price, $products_image, $product_quantity, $user_id);
            
            if ($insert_query->execute()) {
                $display_message[] = "Product added to cart"; // ข้อความยืนยันการเพิ่มสินค้า
            } else {
                $display_message[] = "Failed to add product to cart"; // ข้อความแจ้งข้อผิดพลาด
            }
            
            $insert_query->close(); // ปิด prepared statement
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop Product</title>
    <link rel="stylesheet" href="css/styles.css"></link>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
        .img_sell {
            width: 150px;
            height: 150px;
            margin: 0 auto;
        }
        .font-header {
            font-family: 'Franklin Gothic Medium', 'Arial Narrow', Arial, sans-serif;
        }
    </style>
</head>
<body>
    
    <?php include 'header.php'; ?>
    <?php
    if (isset($display_message)) {
        foreach ($display_message as $msg) {
            echo "<div class='display_message'>
                    <span>$msg</span>
                    <i class='fas fa-times' onClick='this.parentElement.style.display=`none`';></i>
                  </div>";
        }
    }
    ?>

    <div>
        <section class="products">
            <h1 class="heading font-header">Write On</h1>
            <div class="product_container">
                <?php
                // ดึงข้อมูลสินค้าที่มีปริมาณมากกว่า 0 เท่านั้น
                $select_products = mysqli_query($conn, "SELECT * FROM `products` WHERE quantity > 0");
                if (mysqli_num_rows($select_products) > 0) {
                    while ($fetch_product = mysqli_fetch_assoc($select_products)) {
                        ?>
                        <form method="post" class="font-header" action="">
                            <div class="edit_form">
                                <img src="images/<?php echo $fetch_product['image'] ?>" class="img_sell" alt="">
                                <h3><?php echo $fetch_product['name'] ?></h3>
                                <div class="price">Price: <?php echo $fetch_product['price'] ?> Baht</div>
                                <div class="price">Quantity: <?php echo $fetch_product['quantity'] ?></div><br>
                                <input type="hidden" name="product_name" value="<?php echo $fetch_product['name'] ?>">
                                <input type="hidden" name="product_price" value="<?php echo $fetch_product['price'] ?>">
                                <input type="hidden" name="product_quantity" value="<?php echo $fetch_product['quantity'] ?>">
                                <input type="hidden" name="product_image" value="<?php echo $fetch_product['image'] ?>">
                                <input type="submit" class="submit_btn cart_btn" value="Add to cart" name="add_to_cart">
                            </div>
                        </form>
                        <?php
                    }
                } else {
                    echo "<div class='empty_text'>No Product Available</div>";
                }
                ?>
            </div>
        </section>
    </div>
</body>
</html>
