<?php
include 'db.php';

if ($_SESSION['role'] != 'manager') {
    die("Access Denied");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Products</title>
    <link rel="stylesheet" href="all.css">
</head>
<body>

<div class="container">
    <h2>Manage Products</h2>

    <table>
        <tr>
            <th>Image</th>
            <th>Name</th>
            <th>Price</th>
            <th>Action</th>
        </tr>

<?php
$result = $conn->query("SELECT * FROM products");

while ($row = $result->fetch_assoc()) {

    echo "<tr>
            <td>
                <img src='uploads/{$row['image']}' width='80'>
            </td>
            <td>{$row['name']}</td>
            <td>Rs. {$row['price']}</td>
            <td>
                <a href='delete_product.php?id={$row['id']}' 
                   onclick=\"return confirm('Are you sure you want to delete this product?');\">
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

</body>
</html>
