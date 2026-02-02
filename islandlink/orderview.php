<?php
include 'db.php';

// Access Control: Only Customers
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'customer') {
    die("Access Denied");
}

$user_id = $_SESSION['user_id'];

// Fetch all orders for this customer
$sql = "SELECT * FROM orders WHERE user_id = $user_id ORDER BY id DESC";
$result = $conn->query($sql);

// Define Status Steps (Must match the manager's tracker)
$status_steps = ['Confirmed', 'Processing', 'Shipped', 'Out for Delivery', 'Delivered'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Orders</title>
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
    background: linear-gradient(135deg, #eef2ff, #f8fafc);
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
    margin-bottom: 30px;
}

h3 {
    font-size: 20px;
    margin-bottom: 5px;
}

/* Order Card */
.order-box {
    background: var(--card);
    padding: 25px;
    border-radius: 18px;
    box-shadow: 0 12px 35px rgba(0,0,0,0.08);
    margin-bottom: 35px;
    border: 1px solid #eef2ff;
    animation: fadeIn 0.35s ease;
}

/* Header Row */
.order-box > div:first-child {
    margin-bottom: 8px;
}

.order-box span {
    font-size: 14px;
    color: var(--muted);
}

/* Status Badge */
.order-box p {
    margin-top: 8px;
}

.order-box p b {
    color: var(--primary);
    background: #eef2ff;
    padding: 4px 10px;
    border-radius: 999px;
    font-size: 13px;
}

/* Progress Bar */
.progress-track {
    display: flex;
    justify-content: space-between;
    margin: 40px 0 30px;
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
    width: 36px;
    height: 36px;
    background: #c7d2fe;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 700;
    font-size: 14px;
    transition: all 0.3s ease;
}

.step.completed .circle,
.step.active .circle {
    background: linear-gradient(135deg, var(--success), #16a34a);
    box-shadow: 0 0 0 6px rgba(34,197,94,0.15);
}

.step p {
    margin-top: 10px;
    font-size: 13px;
    color: var(--muted);
}

.step.active p {
    color: var(--text);
    font-weight: 600;
}

/* Cancelled Order */
.order-box > div[style*="Cancelled"] {
    background: #fee2e2 !important;
    color: var(--danger) !important;
    border-radius: 14px;
    font-weight: 600;
}

/* Items List */
.items-list {
    margin-top: 20px;
    padding-top: 20px;
    border-top: 1px dashed #d1d5db;
    font-size: 14px;
    color: #374151;
    line-height: 1.7;
}

.items-list strong {
    display: block;
    margin-bottom: 6px;
    color: var(--primary-dark);
}

/* Empty State */
.container > p {
    background: #eef2ff;
    padding: 18px;
    border-radius: 14px;
    font-weight: 500;
}

/* Links */
a {
    color: var(--primary);
    text-decoration: none;
    font-weight: 600;
}

a:hover {
    text-decoration: underline;
}

/* Animation */
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

/* Responsive */
@media (max-width: 600px) {
    .progress-track {
        font-size: 12px;
    }
    .step p {
        font-size: 11px;
    }
}
</style>

</head>
<body>

<div class="container">
    <h2>My Order History</h2>

    <?php if ($result->num_rows > 0): ?>
        <?php while($order = $result->fetch_assoc()): ?>
            <?php 
                $current_status_index = array_search($order['status'], $status_steps);
                if ($current_status_index === false) $current_status_index = -1;
            ?>
            
            <div class="order-box">
                <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap;">
                    <h3>Order #<?= $order['id'] ?></h3>
                    <span style="font-size: 14px; color: #555;">
                        Placed: <b><?= $order['order_date'] ?></b> | 
                        Expected: <b><?= $order['delivery_date'] ?></b>
                    </span>
                </div>
                
                <p>Status: <b style="color:#007bff;"><?= $order['status'] ?></b></p>

                <!-- Progress Bar -->
                <?php if ($order['status'] != 'Cancelled'): ?>
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
                <?php else: ?>
                    <div style="background:#ffecec; color:#d9534f; padding:15px; border-radius:5px; text-align:center; margin: 15px 0;">
                        <b>This order has been Cancelled.</b>
                    </div>
                <?php endif; ?>

                <!-- Order Items Summary -->
                <div class="items-list">
                    <strong>Items in this order:</strong><br>
                    <?php
                    $oid = $order['id'];
                    $items_query = $conn->query("
                        SELECT p.name, oi.quantity 
                        FROM order_items oi 
                        JOIN products p ON oi.product_id = p.id 
                        WHERE oi.order_id = $oid
                    ");
                    
                    $item_list = [];
                    while($item = $items_query->fetch_assoc()) {
                        $item_list[] = "{$item['quantity']} x {$item['name']}";
                    }
                    echo implode(" | ", $item_list);
                    ?>
                </div>

            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>You haven't placed any orders yet. <a href="products.php">Go Shop!</a></p>
    <?php endif; ?>

    <br>
    <a href="dashboard.php">Back to Dashboard</a>
</div>

</body>
</html>