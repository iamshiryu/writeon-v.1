<?php
include 'connect.php';

if (isset($_POST['add_product'])) {
    $product_name = $_POST['product_name'];
    $product_price = $_POST['product_price'];
    $product_quantity = $_POST['product_quantity'];
    $product_image = $_FILES['product_image']['name'];
    $product_image_temp_name = $_FILES['product_image']['tmp_name'];
    $product_image_folder = 'images/' . $product_image;

    // ตรวจสอบว่า price และ quantity ไม่ติดลบ
    if ($product_price < 0) {
        $display_message = "ราคาสินค้าไม่สามารถเป็นค่าติดลบได้!";
    } elseif ($product_quantity < 0) {
        $display_message = "ปริมาณสินค้าต้องเป็นจำนวนบวก!";
    } else {
        // ตรวจสอบชื่อสินค้าซ้ำในฐานข้อมูล
        $check_product_query = mysqli_query($conn, "SELECT * FROM `products` WHERE name = '$product_name'");
        if (mysqli_num_rows($check_product_query) > 0) {
            // หากพบชื่อสินค้าซ้ำ
            $display_message = "ชื่อสินค้าซ้ำในฐานข้อมูล, กรุณาเลือกชื่อสินค้าที่ยังไม่เคยมี";
        } else {
            // หากชื่อสินค้าถูกต้อง ไม่มีซ้ำ
            $insert_query = mysqli_query($conn, "INSERT INTO `products` (name, price, quantity, image) 
            VALUES ('$product_name', '$product_price', '$product_quantity', '$product_image')") or die("insert query failed");

            if ($insert_query) {
                move_uploaded_file($product_image_temp_name, $product_image_folder);
                $display_message = "Product inserted successfully";
            } else {
                $display_message = "There is some error inserting the product";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="css/styles.css"></link>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop</title>
</head>
<body>
    <?php include('header2.php') ?>

    <div class="container">
        <?php
        if (isset($display_message)) {
            echo "<div class='display_message'>
                    <span>" . $display_message . "</span>
                    <i class='fas fa-times' onclick='this.parentElement.style.display=`none`';></i>
                  </div>";
        }
        ?>
        <section>
            <h3 class="heading">Add Products</h3>
            <form action="" class="add_product" method="post" enctype="multipart/form-data">
                <input type="text" name="product_name" placeholder="Enter product Name of product" class="input_fields" required>
                <input type="number" name="product_price" placeholder="Enter product Price" class="input_fields" required>
                <input type="number" name="product_quantity" placeholder="Enter product Quantity" class="input_fields" required>
                <input type="file" name="product_image" class="input_fields" required accept="image/png, image/jpg, image/jpeg">
                <input type="submit" name="add_product" class="submit_btn" value="Add Product">
            </form>
        </section>
    </div>

<script src="js/script.js"></script>
</body>
</html>
