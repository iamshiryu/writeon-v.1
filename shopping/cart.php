<?php 
include 'connect.php'; 
session_start();

// ตรวจสอบว่าผู้ใช้ล็อกอินอยู่หรือไม่
if (!isset($_SESSION['user_id'])) {
    echo "กรุณาล็อกอินเพื่อดูตะกร้าสินค้า";
    exit();
}

// ดึง user_id จาก session
$user_id = $_SESSION['user_id'];

// เช็คปริมาณสินค้าว่ามีเพียงพอหรือไม่ก่อนที่จะไปยัง checkout
$is_quantity_available = true; // สถานะเริ่มต้นที่ให้ปริมาณเพียงพอ
$error_message = ''; // ตัวแปรสำหรับเก็บข้อความแจ้งเตือน

// ดึงสินค้าจากตะกร้าสำหรับ user_id ที่ตรงกัน
$select_cart_products = mysqli_query($conn, "SELECT * FROM `cart` WHERE user_id = '$user_id'");
$grand_total = 0;

// อัปเดต quantity ถ้ามีการส่งข้อมูล
if (isset($_POST['update_quantity_id']) && isset($_POST['update_quantity'])) {
    $update_quantity_id = $_POST['update_quantity_id'];
    $update_quantity = $_POST['update_quantity'];
    
    // ตรวจสอบว่าปริมาณใหม่ถูกต้องหรือไม่
    if ($update_quantity > 0) {
        // ดึงชื่อสินค้าจากตาราง cart
        $cart_query = mysqli_query($conn, "SELECT name FROM `cart` WHERE id = '$update_quantity_id'");
        $cart_data = mysqli_fetch_assoc($cart_query);
        $product_name = $cart_data['name'];

        // ดึงข้อมูลปริมาณสินค้าจากตาราง products
        $product_query = mysqli_query($conn, "SELECT quantity FROM `products` WHERE name = '$product_name'");
        $product_data = mysqli_fetch_assoc($product_query);
        $available_quantity = $product_data['quantity'];

        // เช็คว่าปริมาณในตะกร้ามากกว่าปริมาณที่มีในคลังหรือไม่
        if ($update_quantity > $available_quantity) {
            $is_quantity_available = false;
            $error_message = "ปริมาณของสินค้าที่คุณต้องการมากกว่าที่มีในคลัง (คงเหลือ: $available_quantity)";
        } else {
            // อัปเดตปริมาณในฐานข้อมูล
            mysqli_query($conn, "UPDATE `cart` SET quantity = '$update_quantity' WHERE id = '$update_quantity_id'");
        }
    }
}
// เช็คการลบสินค้า
if (isset($_GET['remove'])) {
    $remove_id = $_GET['remove'];
    mysqli_query($conn, "DELETE FROM `cart` WHERE id = '$remove_id' AND user_id = '$user_id'");
    header('location: cart.php'); // เปลี่ยนเส้นทางกลับไปที่หน้า cart
    exit();
}


// เช็คสินค้าในตะกร้าว่ายังมีอยู่ในฐานข้อมูลหรือไม่
while ($fetch_cart_products = mysqli_fetch_assoc($select_cart_products)) {
    $product_name = $fetch_cart_products['name'];
    $product_query = mysqli_query($conn, "SELECT * FROM `products` WHERE name = '$product_name'");
    
    if (!mysqli_fetch_assoc($product_query)) {
        // ถ้าสินค้าไม่มีในฐานข้อมูล ให้ลบออกจากตะกร้า
        mysqli_query($conn, "DELETE FROM `cart` WHERE id = '" . $fetch_cart_products['id'] . "'");
        // แสดงข้อความเตือนผู้ใช้
        $error_message = "มีสินค้าในตะกร้าที่ถูกลบออกจากฐานข้อมูล";
    }
}

// ดึงสินค้าจากตะกร้าสำหรับ user_id ที่ตรงกันอีกครั้งเพื่อคำนวณ grand_total ใหม่
$select_cart_products = mysqli_query($conn, "SELECT * FROM `cart` WHERE user_id = '$user_id'");

while ($fetch_cart_products = mysqli_fetch_assoc($select_cart_products)) {
    $grand_total += ($fetch_cart_products['price'] * $fetch_cart_products['quantity']);
}

if ($is_quantity_available) {
    // แสดงผลตะกร้าสินค้า
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Cart Page</title>
        <link rel="stylesheet" href="css/styles.css"></link>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
        <script>
            function handleQuantityChange(input) {
                const currentQuantity = input.dataset.current; // เก็บค่าปัจจุบัน
                
                // ตรวจสอบว่ากด Enter
                input.onkeydown = function(event) {
                    if (event.key === "Enter") {
                        input.form.submit(); // ส่งฟอร์ม
                    }
                };

                // หากไม่กด Enter ให้กลับไปใช้ค่าปัจจุบัน
                input.onblur = function() {
                    input.value = currentQuantity; // กลับไปใช้ค่าปัจจุบัน
                };
            }

            // แสดงข้อความแจ้งเตือนถ้ามีข้อผิดพลาด
            window.onload = function() {
                <?php if ($error_message): ?>
                    alert("<?php echo $error_message; ?>");
                <?php endif; ?>
            };
        </script>
    </head>
    <body>
        <?php include 'header.php'; ?>
        <div class="container">
            <section class="shopping_cart">
                <h1 class="heading">My Cart</h1>
                <table>
                    <?php
                    // ดึงสินค้าจากตะกร้าสำหรับ user_id ที่ตรงกัน
                    $select_cart_products = mysqli_query($conn, "SELECT * FROM `cart` WHERE user_id = '$user_id'");
                    $num = 1;
                    if(mysqli_num_rows($select_cart_products) > 0){
                        echo "<thead>
                            <th>Sl No</th>
                            <th>Product Name</th>
                            <th>Product Image</th>
                            <th>Product Price</th>
                            <th>Product Quantity</th>
                            <th>Total Price</th>
                            <th>Action</th>
                        </thead>
                        <tbody>";
                        while($fetch_cart_products = mysqli_fetch_assoc($select_cart_products)){
                        ?>
                        <tr>
                            <td><?php echo $num?></td>
                            <td><?php echo $fetch_cart_products['name']?></td>
                            <td>
                                <img src="images/<?php echo $fetch_cart_products['image']?>" alt="">
                            </td>
                            <td><?php echo $fetch_cart_products['price']?> Baht</td>
                            <td>
                                <form action="" method="post">
                                    <input type="hidden" value="<?php echo $fetch_cart_products['id']?>" name="update_quantity_id">
                                    <div class="quantity_box">
                                        <input type="number" min="1" value="<?php echo $fetch_cart_products['quantity']?>" name="update_quantity" 
                                        onfocus="handleQuantityChange(this);" data-current="<?php echo $fetch_cart_products['quantity']; ?>" /> <!-- ใช้ฟังก์ชัน handleQuantityChange -->
                                    </div>
                                </form>
                            </td>
                            <td><?php echo number_format($fetch_cart_products['price'] * $fetch_cart_products['quantity'])?> Baht</td>
                            <td>
                                <a href="cart.php?remove=<?php echo $fetch_cart_products['id']?>" onclick="return confirm('Are you sure?')">
                                    <i class="fas fa-trash"><br><br>Remove</i>
                                </a>
                            </td>
                        </tr>
                        <?php
                        $num++;
                        }
                    } else {
                        echo "<div class='empty_text'>Cart is empty</div>";
                    }
                    ?>
                    </tbody>
                </table>

                <?php
                // แสดง Grand Total
                if($grand_total > 0){
                    echo "<div class='table_bottom'>
                        <a href='shop_products.php' class='bottom_btn'>Continue Shopping</a>
                        <h3 class='bottom_btn'>Grand total: <span>" . number_format($grand_total) . " BAHT</span></h3>
                        <a href='checkout.php' class='bottom_btn'>Proceed to checkout</a>
                    </div>";
                    ?>
                    
                    <?php
                }
                ?>
            </section>
        </div>
    </body>
    </html>
    <?php
} else {
    // ถ้าปริมาณไม่เพียงพอ แสดงปุ่มให้กลับไปช้อปปิ้ง
    echo "<script>
            alert('สินค้าไม่พอ');
            window.location.href = 'cart.php'; // เปลี่ยนเส้นทางไปยังหน้าตะกร้า
          </script>";
}
?>
