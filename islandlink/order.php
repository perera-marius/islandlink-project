<?php
include 'db.php';

if ($_SESSION['role'] != 'customer') {
    die("Access Denied");
}

if (!isset($_POST['products'])) {
    die("No products selected");
}

$user_id = $_SESSION['user_id'];
$phone = $_POST['phone'];
$order_date = date('Y-m-d');
$delivery_date = date('Y-m-d', strtotime('+2 days'));

/* 1️⃣ Create the order */
$conn->query("
INSERT INTO orders (user_id, order_date, delivery_date, status, phone)
VALUES ($user_id, '$order_date', '$delivery_date', 'Confirmed', '$phone')
");


$order_id = $conn->insert_id;

/* 2️⃣ Save selected products */
foreach ($_POST['products'] as $product_id => $val) {
    $quantity = $_POST['qty'][$product_id];

    $conn->query("
    INSERT INTO order_items (order_id, product_id, quantity)
    VALUES ($order_id, $product_id, $quantity)
    ");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Order Confirmed</title>
    <link rel="stylesheet" href="orders.css">
</head>
<body>

<div class="order-container">
    <h2>Order Successfully Placed</h2>

    <p>Your order has been confirmed.</p>

    <p><b>Order ID:</b> <?= $order_id ?></p>

    <div class="delivery-date">
        Estimated Delivery Date: <b><?= $delivery_date ?></b>
    </div>

    <a href="dashboard.php">Back to Dashboard</a>
</div>

</body>
</html>
