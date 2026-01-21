<?php
session_start();
include 'db.php';

// කළමනාකරුට පමණක් අවසර දීම
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'manager') {
    die("Access Denied");
}

// 1. Dashboard දත්ත ලබා ගැනීම (Totals)
$total_orders = $conn->query("SELECT COUNT(*) AS c FROM orders")->fetch_assoc()['c'];
$total_revenue = $conn->query("SELECT SUM(amount) AS r FROM orders WHERE status='paid'")->fetch_assoc()['r'];

// 2. Reporting කොටස: අන්තිම ඇණවුම් 5 ලබා ගැනීම
$recent_orders = $conn->query("SELECT * FROM orders ORDER BY created_at DESC LIMIT 5");
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="all.css">
    <title>Manager Dashboard</title>
</head>
<body>

<div class="container">
    <h2>Manager Dashboard</h2>
    
    <div class="stats-panel">
        <div class="card">
            <h3>Total Orders</h3>
            <p><?= $total_orders ?></p>
        </div>
        <div class="card">
            <h3>Total Revenue</h3>
            <p>Rs. <?= number_format($total_revenue, 2) ?></p>
        </div>
        <div class="card">
            <h3>System Status</h3>
            <p style="color: green;">Operational</p>
        </div>
    </div>

    <hr>

    <h3>Recent Orders Report</h3>
    <table border="1" cellpadding="10">
        <tr>
            <th>Order ID</th>
            <th>Customer</th>
            <th>Amount</th>
            <th>Status</th>
            <th>Date</th>
        </tr>
        <?php while($row = $recent_orders->fetch_assoc()): ?>
        <tr>
            <td>#<?= $row['id'] ?></td>
            <td><?= htmlspecialchars($row['customer_name']) ?></td>
            <td>Rs. <?= number_format($row['amount'], 2) ?></td>
            <td><?= $row['status'] ?></td>
            <td><?= $row['created_at'] ?></td>
        </tr>
        <?php endwhile; ?>
    </table>
    
    <br>
    <button onclick="window.print()">Print Report</button>
</div>

</body>
</html>