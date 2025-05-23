<?php
include 'connect.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    echo "กรุณาล็อกอินเพื่อทำการชำระเงิน";
    exit();
}

$user_id = $_SESSION['user_id'];
$payment_method = $_POST['payment_method'];
$total_amount = $_POST['total_amount'];

$order_number = '';
for ($i = 0; $i < 16; $i++) {
    $order_number .= mt_rand(0, 9);  
}

$query = "INSERT INTO payments (user_id, payment_method, total_amount, order_number) VALUES ('$user_id', '$payment_method', '$total_amount', '$order_number')";
$result = mysqli_query($conn, $query);

if ($result) {
    $cart_query = mysqli_query($conn, "SELECT * FROM `cart` WHERE user_id='$user_id'");
    
    while ($cart_item = mysqli_fetch_assoc($cart_query)) {
        $product_name = $cart_item['name'];
        $product_price = $cart_item['price'];
        $product_quantity_in_cart = $cart_item['quantity'];

        $product_query = mysqli_query($conn, "SELECT * FROM `products` WHERE name='$product_name'");
        if ($product = mysqli_fetch_assoc($product_query)) {
            $current_quantity = $product['quantity'];
            $new_quantity = $current_quantity - $product_quantity_in_cart;

            if ($new_quantity < 0) {
                $new_quantity = 0;
            }
            mysqli_query($conn, "UPDATE `products` SET quantity='$new_quantity' WHERE name='$product_name'");
        }
        $insert_record = mysqli_query($conn, "INSERT INTO `record` (user_id, product_name, product_price, product_quantity, order_number) 
                                             VALUES ('$user_id', '$product_name', '$product_price', '$product_quantity_in_cart', '$order_number')");
    }
    mysqli_query($conn, "DELETE FROM `cart` WHERE user_id='$user_id'");

    echo "<script>
            alert('ชำระเงินเสร็จสิ้น เลขคำสั่งซื้อของคุณคือ: $order_number');
            window.location.href = 'shop_products.php';
          </script>";
} else {
    echo "เกิดข้อผิดพลาดในการชำระเงิน";
}

?>
