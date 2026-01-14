<?php
include 'db.php';
if ($_SESSION['role'] != 'rdc') die("Access Denied");
?>

<link rel="stylesheet" href="all.css">

<div class="container">
<h2>RDC Orders</h2>

<table border="1">
<tr>
    <th>ID</th>
    <th>Order Date</th>
    <th>Delivery</th>
    <th>Phone</th>
    <th>Status</th>
    <th>Action</th>
</tr>

<?php
$result = $conn->query("SELECT * FROM orders");
while ($row = $result->fetch_assoc()) {

    echo "<tr>
        <td>{$row['id']}</td>
        <td>{$row['order_date']}</td>
        <td>{$row['delivery_date']}</td>
        <td>{$row['phone']}</td>
        <td>{$row['status']}</td>
        <td>
            <a href='update_status.php?id={$row['id']}&status=Dispatched'>Dispatch</a>
            |
            <a href='update_status.php?id={$row['id']}&status=Cancelled'
               onclick=\"return confirm('Are you sure you want to cancel this order?');\">
               Cancel
            </a>
            |
            <a href='delete_order.php?id={$row['id']}'
               onclick=\"return confirm('Are you sure you want to permanently delete this order?');\">
               Delete
            </a>
        </td>
    </tr>";
}
?>
</table>

<br>
<a href="dashboard.php">Back to Dashboard</a>
</div>

