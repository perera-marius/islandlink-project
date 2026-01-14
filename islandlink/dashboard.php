<?php
include 'db.php';
if (!isset($_SESSION['role'])) header("Location: login.php");
?>
<link rel="stylesheet" href="all.css">
<div class="container">
<h2>Dashboard</h2>
<p>Role: <b><?= $_SESSION['role'] ?></b></p>


<?php if ($_SESSION['role'] == 'customer'): ?>
    <p>WELCOME TO OUR Customer Dashboard</p>
    <a href="products.php">Place Order</a>
    
<?php endif; ?>

<?php if ($_SESSION['role'] == 'rdc'): ?>
    <p>WELCOME TO OUR Regional Distribution Centre Staff Dashboard</p>
    <a href="rdc_orders.php">View Orders</a>
    
<?php endif; ?>

<?php if ($_SESSION['role'] == 'manager'): ?>
    <p>WELCOME TO OUR Manager Dashboard</p>
    <a href="manager_dashboard.php">View Reports</a>
    <a href="manager_orders.php">View Customer Orders</a>
    <a href="add_product.php">Add Products</a>
    <a href="manage_products.php">Manage Products</a>
<?php endif; ?>

<br><br>
<a href="logout.php">Logout</a>
