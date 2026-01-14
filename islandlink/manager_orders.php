<?php
include 'db.php';

if ($_SESSION['role'] != 'manager') {
    die("Access Denied");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>All Customer Orders</title>
    <link rel="stylesheet" href="all.css">
</head>
<body>

<div class="container">
    <h2>Customer Orders</h2>

<?php
$orders = $conn->query("
    SELECT o.id, o.order_date, o.delivery_date, o.status,  o.phone, u.username
    FROM orders o
    JOIN users u ON o.user_id = u.id
    ORDER BY o.id DESC
");

while ($order = $orders->fetch_assoc()) {

    echo "<div style='border:1px solid #ccc; padding:15px; margin-bottom:20px;'>";
   
    echo "<h4>Order ID: {$order['id']}</h4>";
    echo "<p><b>Customer:</b> {$order['username']}</p>";
    echo "<p><b>Order Date:</b> {$order['order_date']}</p>";
    echo "<p><b>Delivery Date:</b> {$order['delivery_date']}</p>";
    echo "<p><b>Phone:</b> {$order['phone']}</p>";
    echo "<p><b>Status:</b> {$order['status']}</p>";

    // ✅ DELETE OPTION ADDED HERE
    echo "
    <p>
        <a href='delete_order.php?id={$order['id']}'
           onclick=\"return confirm('Are you sure you want to permanently delete this order?');\"
           style='color:red; font-weight:bold;'>
           Delete Order
        </a>
    </p>
    ";

    echo "<table border='1' width='100%' style='margin-top:10px;'>";
    echo "<tr>
            <th>Product</th>
            <th>Price</th>
            <th>Quantity</th>
          </tr>";

    $items = $conn->query("
        SELECT p.name, p.price, oi.quantity
        FROM order_items oi
        JOIN products p ON oi.product_id = p.id
        WHERE oi.order_id = {$order['id']}
    ");

    while ($item = $items->fetch_assoc()) {
        echo "<tr>
                <td>{$item['name']}</td>
                <td>Rs. {$item['price']}</td>
                <td>{$item['quantity']}</td>
              </tr>";
    }

    echo "</table>";
    echo "</div>";
}
?>

    <a href="dashboard.php">Back to Dashboard</a>
</div>

</body>
</html>

