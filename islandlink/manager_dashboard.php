<?php
include 'db.php';
if ($_SESSION['role'] != 'manager') die("Access Denied");

$total = $conn->query("SELECT COUNT(*) AS c FROM orders")->fetch_assoc()['c'];
?>
<link rel="stylesheet" href="all.css">

<div class="container">

<h2>Manager Dashboard</h2>
<p>Total Orders: <b><?= $total ?></b></p>
<p>System Status: Operational</p>

<button onclick="window.location.href='dashboard.php'">back to the dashboard</button>

</div>
