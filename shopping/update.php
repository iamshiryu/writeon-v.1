<?php
include 'connect.php';
if(isset($_POST['update_product'])){
    $update_product_id = $_POST['update_product_id'];
    $update_product_name = $_POST['update_product_name'];
    $update_product_price = $_POST['update_product_price'];
    $update_product_quantity = $_POST['update_product_quantity'];

    // ตรวจสอบว่าผู้ใช้ได้อัปโหลดไฟล์รูปใหม่หรือไม่
    $update_product_image = $_FILES['update_product_image']['name'];
    $update_product_image_tmp_name = $_FILES['update_product_image']['tmp_name'];
    $update_product_image_folder = 'images/'.$update_product_image;

    if(!empty($update_product_image)){
        // ถ้ามีการอัปโหลดไฟล์รูปใหม่
        $update_products = mysqli_query($conn, "UPDATE `products` SET name='$update_product_name', price='$update_product_price', quantity='$update_product_quantity', image='$update_product_image' WHERE id=$update_product_id");
        if($update_products){
            move_uploaded_file($update_product_image_tmp_name, $update_product_image_folder);
            header('location:view_products.php');
        } else {
            $display_message = "There is some error in updating the product";
        }
    } else {
        // ถ้าไม่มีการอัปโหลดไฟล์รูปใหม่
        $update_products = mysqli_query($conn, "UPDATE `products` SET name='$update_product_name', price='$update_product_price', quantity='$update_product_quantity' WHERE id=$update_product_id");
        if($update_products){
            header('location:view_products.php');
        } else {
            $display_message = "There is some error in updating the product";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title>Update Products</title>
</head>
<body>
    <?php include 'header.php' ?>
    <?php
        if(isset($display_message)){
            echo "<div class='display_message'>
            <span>'.$display_message.'</span>
            <i class='fas fa-times' onclick='this.parentElement.style.display=`none`';></i>
        </div>";
        }
    ?>
    <section class="edit_container">
        <?php
        if(isset($_GET['edit'])){
            $edit_id = $_GET['edit'];
            $edit_query = mysqli_query($conn, "SELECT * FROM `products` WHERE id=$edit_id");
            if(mysqli_num_rows($edit_query) > 0){
                $fetch_data = mysqli_fetch_assoc($edit_query);
                ?>
            <form action="" method="post" enctype="multipart/form-data" class="update_product product_container_box">
                <img src="images/<?php echo $fetch_data['image']?>" alt="">
                <input type="hidden" value="<?php echo $fetch_data['id']?>" name="update_product_id">
                <input type="text" class="input_fields fields" required value="<?php echo $fetch_data['name']?>" name="update_product_name">         
                <input type="number" class="input_fields fields" required value="<?php echo $fetch_data['price']?>" name="update_product_price">            
                <input type="number" class="input_fields fields" required value="<?php echo $fetch_data['quantity']?>" name="update_product_quantity">            
                <input type="file" class="input_fields fields" accept="image/png, image/jpg, image/jpeg" name="update_product_image">
                <div class="btns">
                    <input type="submit" class="edit_btn" value="Update Product" name="update_product">
                    <button type="button" id="close-edit" class="cancel_btn" onclick="window.location.href='view_products.php'">Cancel</button>
                </div>
            </form>
            <?php
            }
        }
        ?>
    </section>
</body>
</html>
