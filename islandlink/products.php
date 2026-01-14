<?php
include 'db.php';
if ($_SESSION['role'] != 'customer') die("Access Denied");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Products</title>
    <link rel="stylesheet" href="all.css">
</head>
<body>

<div class="container">
    <h1>Products</h1>

    <form method="post" action="order.php">
     <label><b>Contact Phone Number</b></label>
     <br>
     <br><br>
     <input type="text" name="phone" placeholder="07XXXXXXXX" required>
     <br><br>

    <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(200px,1fr)); gap:20px;">

    <?php
    $result = $conn->query("SELECT * FROM products");
    while ($row = $result->fetch_assoc()) {
        echo "
        <div style='border:1px solid #ccc; padding:15px; border-radius:10px; text-align:center;'>
            <img src='uploads/{$row['image']}' 
                 style='width:100%; height:150px; object-fit:cover; border-radius:8px;'>
            <h4>{$row['name']}</h4>
            <p>Rs. {$row['price']}</p>

            <input type='checkbox' name='products[{$row['id']}]' value='1'>
            Qty:
            <input type='number' name='qty[{$row['id']}]' value='1' min='1' style='width:60px'>
        </div>
        ";
    }
    ?>

    </div>

    <br>
    <button type="submit">Confirm Order</button>
    <br><br>
    <a href="dashboard.php">Back to Dashboard</a>

    </form>
</div>

</body>
</html>


