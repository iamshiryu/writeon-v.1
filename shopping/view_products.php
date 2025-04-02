<?php include 'connect.php' ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Products</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>
    <?php include 'header2.php'; ?>

    <div class="container">
        <section class="display_product">
            <?php
            // ลบสินค้าที่มี quantity เท่ากับ 0
            mysqli_query($conn, "DELETE FROM `products` WHERE quantity = 0");

            // ดึงข้อมูลสินค้าทั้งหมด
            $display_product = mysqli_query($conn, "SELECT * FROM `products`");
            $num = 1;
            if(mysqli_num_rows($display_product) > 0){
                echo "<table>
                        <thead>
                            <th>Sl No</th>
                            <th>Product Image</th>
                            <th>Product Name</th>
                            <th>Product Price</th>
                            <th>Product Quantity</th>
                            <th>Action</th>
                        </thead>
                    <tbody>";
                while($row = mysqli_fetch_assoc($display_product)){
            ?>
            <tr>
                <td><?php echo $num ?></td>
                <td><img src="images/<?php echo $row['image'] ?>" alt="<?php echo $row['name'] ?>" width="100"></td>
                <td><?php echo $row['name'] ?></td>
                <td><?php echo $row['price'] ?> BAHT</td>
                <td><?php echo $row['quantity'] ?> ea</td>
                <td>
                    <a href="delete.php?delete=<?php echo $row['id'] ?>" class="delete_product_btn" onclick="return confirm('Are you sure?');"><i class="fas fa-trash"></i></a>
                    <a href="update.php?edit=<?php echo $row['id'] ?>" class="update_product_btn"><i class="fas fa-edit"></i></a>
                </td>
            </tr>
            <?php
                $num++;
                }
            } else {
                echo "<div class='empty_text'>No Product Available</div>";
            }
            ?>
            </tbody>
        </table>
        </section>
    </div>
</body>
</html>
