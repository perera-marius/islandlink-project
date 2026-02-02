<?php
include 'db.php';

// Access Control
if ($_SESSION['role'] != 'manager') {
    die("Access Denied");
}

$order = null;
$items = [];
$msg = "";

// Handle Status Update
if (isset($_POST['update_status'])) {
    $order_id = $_POST['order_id'];
    $new_status = $_POST['new_status'];
    
    $conn->query("UPDATE orders SET status = '$new_status' WHERE id = $order_id");
    $msg = "Order #$order_id status updated to $new_status.";
    
    // Refresh the search so we see the new status immediately
    $_GET['search'] = $order_id;
}

// Handle Search
if (isset($_GET['search'])) {
    $search = $conn->real_escape_string($_GET['search']);
    
    // Fetch Order & Customer Details
    $sql = "SELECT o.*, u.username 
            FROM orders o 
            JOIN users u ON o.user_id = u.id 
            WHERE o.id = '$search' OR o.phone = '$search'";
            
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        $order = $result->fetch_assoc();
        
        // Fetch Order Items
        $oid = $order['id'];
        $items_result = $conn->query("
            SELECT p.name, p.price, p.image, oi.quantity 
            FROM order_items oi
            JOIN products p ON oi.product_id = p.id
            WHERE oi.order_id = $oid
        ");
        
        while($row = $items_result->fetch_assoc()) {
            $items[] = $row;
        }
    } else {
        $msg = "No order found with ID or Phone: $search";
    }
}

// Define Status Steps for the Progress Bar
$status_steps = ['Confirmed', 'Processing', 'Shipped', 'Out for Delivery', 'Delivered'];
$current_status_index = -1;

if ($order) {
    $current_status_index = array_search($order['status'], $status_steps);
    // If status isn't in the standard list (e.g., "Cancelled"), handle gracefully
    if ($current_status_index === false) $current_status_index = -1;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Order Tracker</title>
    <link rel="stylesheet" href="all.css">
<style>
:root {
    --primary: #4f46e5;
    --primary-dark: #3730a3;
    --success: #22c55e;
    --danger: #ef4444;
    --bg: #f4f6fb;
    --card: #ffffff;
    --text: #1f2937;
    --muted: #6b7280;
}

* {
    box-sizing: border-box;
    font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, sans-serif;
}

body {
    background: linear-gradient(135deg, #071238, #032d57);
    color: var(--text);
}

.container {
    max-width: 1100px;
    margin: auto;
    padding: 30px;
}

/* Headings */
h2 {
    font-size: 28px;
    margin-bottom: 20px;
}

h3 {
    font-size: 22px;
}

h4 {
    margin-bottom: 10px;
    color: var(--primary-dark);
}

/* Search Bar */
form[action="tracker.php"] {
    display: flex;
    gap: 10px;
}

form[action="tracker.php"] input {
    flex: 1;
    border-radius: 8px;
    border: 1px solid #d1d5db;
    padding: 12px 14px;
    font-size: 15px;
}

form[action="tracker.php"] button {
    background: linear-gradient(135deg, var(--primary), var(--primary-dark));
    color: white;
    border: none;
    padding: 12px 22px;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 600;
}

form[action="tracker.php"] button:hover {
    opacity: 0.9;
}

/* Message */
.container > p {
    background: #e0e7ff;
    padding: 12px 15px;
    border-radius: 8px;
    margin-top: 15px;
}

/* Tracker Card */
.tracker-box {
    background: var(--card);
    border-radius: 16px;
    padding: 30px;
    margin-top: 25px;
    box-shadow: 0 15px 40px rgba(0,0,0,0.08);
    animation: fadeIn 0.4s ease;
}

/* Header Row */
.tracker-box > div:first-child {
    margin-bottom: 15px;
}

.tracker-box span {
    background: #eef2ff;
    color: var(--primary-dark);
    font-weight: 600;
}

/* Progress Bar */
.progress-track {
    display: flex;
    justify-content: space-between;
    margin: 50px 0;
    position: relative;
}

.progress-track::before {
    content: '';
    position: absolute;
    top: 18px;
    left: 0;
    width: 100%;
    height: 5px;
    background: #e5e7eb;
    border-radius: 5px;
}

.step {
    position: relative;
    z-index: 1;
    text-align: center;
    flex: 1;
}

.step .circle {
    width: 40px;
    height: 40px;
    background: #c7d2fe;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 700;
    transition: 0.3s;
}

.step.completed .circle,
.step.active .circle {
    background: linear-gradient(135deg, var(--success), #16a34a);
    box-shadow: 0 0 0 6px rgba(34,197,94,0.15);
}

.step p {
    margin-top: 12px;
    font-size: 14px;
    color: var(--muted);
}

.step.active p {
    color: var(--text);
    font-weight: 600;
}

/* Order Info */
.order-info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px,1fr));
    gap: 25px;
    margin: 30px 0;
    background: #f9fafb;
    padding: 25px;
    border-radius: 14px;
}

/* Items Table */
.items-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

.items-table th {
    text-align: left;
    color: var(--muted);
    font-weight: 600;
    border-bottom: 2px solid #e5e7eb;
    padding: 12px;
}

.items-table td {
    padding: 14px 12px;
    border-bottom: 1px solid #f1f5f9;
}

.items-table img {
    border-radius: 8px;
}

/* Grand Total */
.items-table tr:last-child td {
    background: #f1f5f9;
}

/* Manager Action */
.tracker-box form[method="post"] {
    display: flex;
    gap: 12px;
    align-items: center;
    margin-top: 10px;
}

.tracker-box select {
    padding: 12px;
    border-radius: 8px;
    border: 1px solid #d1d5db;
}

.tracker-box button[name="update_status"] {
    background: linear-gradient(135deg, var(--primary), var(--primary-dark));
    color: white;
    padding: 12px 20px;
    border-radius: 8px;
    border: none;
    font-weight: 600;
    cursor: pointer;
}

/* Back Link */
a {
    color: var(--primary);
    text-decoration: none;
    font-weight: 600;
}

a:hover {
    text-decoration: underline;
}

/* Animations */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>

</head>
<body>

<div class="container">
    <h2>Track Customer Order</h2>
    
    <!-- Search Form -->
    <form method="get" action="tracker.php" style="margin-bottom: 20px;">
        <input type="text" name="search" placeholder="Enter Order ID or Phone Number" required value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>" style="padding: 10px; width: 300px;">
        <button type="submit" style="padding: 10px;">Track Order</button>
    </form>

    <?php if ($msg): ?>
        <p style="color: blue; font-weight: bold;"><?= $msg ?></p>
    <?php endif; ?>

    <?php if ($order): ?>
    <div class="tracker-box">
        
        <div style="display:flex; justify-content:space-between; align-items:center;">
            <h3>Order #<?= $order['id'] ?></h3>
            <span style="background: #eee; padding: 5px 10px; border-radius: 4px;">
                Current Status: <b><?= $order['status'] ?></b>
            </span>
        </div>

        <!-- Progress Bar -->
        <div class="progress-track">
            <?php foreach ($status_steps as $index => $step): ?>
                <?php 
                    $class = '';
                    if ($index < $current_status_index) $class = 'completed';
                    if ($index == $current_status_index) $class = 'active';
                ?>
                <div class="step <?= $class ?>">
                    <div class="circle"><?= $index + 1 ?></div>
                    <p><?= $step ?></p>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Order Details Grid -->
        <div class="order-info-grid">
            <div>
                <h4>Customer Details</h4>
                <p><b>Name:</b> <?= $order['username'] ?></p>
                <p><b>Phone:</b> <?= $order['phone'] ?></p>
            </div>
            <div>
                <h4>Order Dates</h4>
                <p><b>Placed:</b> <?= $order['order_date'] ?></p>
                <p><b>Expected Delivery:</b> <?= $order['delivery_date'] ?></p>
            </div>
        </div>

        <!-- Order Items -->
        <h4>Items in Package</h4>
        <table class="items-table">
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Qty</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $grand_total = 0;
                foreach ($items as $item): 
                    $total = $item['price'] * $item['quantity'];
                    $grand_total += $total;
                ?>
                <tr>
                    <td><img src="uploads/<?= $item['image'] ?>" width="50" style="border-radius:4px;"></td>
                    <td><?= $item['name'] ?></td>
                    <td>Rs. <?= $item['price'] ?></td>
                    <td><?= $item['quantity'] ?></td>
                    <td>Rs. <?= $total ?></td>
                </tr>
                <?php endforeach; ?>
                <tr>
                    <td colspan="4" align="right"><b>Grand Total:</b></td>
                    <td><b>Rs. <?= $grand_total ?></b></td>
                </tr>
            </tbody>
        </table>

        <!-- Manager Action: Update Status -->
        <div style="margin-top: 30px; border-top: 2px dashed #ccc; padding-top: 20px;">
            <h4>Update Order Status</h4>
            <form method="post">
                <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                <select name="new_status" style="padding: 10px;">
                    <?php foreach ($status_steps as $step): ?>
                        <option value="<?= $step ?>" <?= ($order['status'] == $step) ? 'selected' : '' ?>><?= $step ?></option>
                    <?php endforeach; ?>
                    <option value="Cancelled">Cancelled</option>
                </select>
                <button type="submit" name="update_status" style="padding: 10px; background-color: #007bff; color: white; border: none; cursor: pointer;">Update Status</button>
            </form>
        </div>

    </div>
    <?php endif; ?>

    <br><br>
    <a href="dashboard.php">Back to Dashboard</a>
</div>

</body>
</html>