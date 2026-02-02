<?php
include 'db.php';

// Access Control
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'customer') {
    die("Access Denied");
}

$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM orders WHERE user_id = $user_id ORDER BY id DESC";
$result = $conn->query($sql);

$status_steps = ['Confirmed', 'Processing', 'Shipped', 'Out for Delivery', 'Delivered'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>My Orders</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
<style>
    :root {
        --bg: #f3f4f6;
        --card-bg: #ffffff;
        --primary: #3b82f6;
        --success: #10b981;
        --danger: #ef4444;
        --text-dark: #1f2937;
        --text-light: #6b7280;
    }

    body {
        font-family: 'Inter', sans-serif;
        background: var(--bg);
        color: var(--text-dark);
        padding: 40px 20px;
    }

    .container {
        max-width: 900px;
        margin: 0 auto;
    }

    /* Header */
    .header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 40px;
    }
    
    .header h2 { font-size: 2rem; font-weight: 800; color: #111; }
    
    .back-btn {
        background: white;
        padding: 10px 20px;
        border-radius: 30px;
        text-decoration: none;
        color: var(--text-dark);
        font-weight: 600;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
        transition: 0.3s;
    }
    .back-btn:hover { transform: translateY(-2px); box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); }

    /* Order Card */
    .order-card {
        background: var(--card-bg);
        border-radius: 20px;
        padding: 30px;
        margin-bottom: 30px;
        box-shadow: 0 10px 25px -5px rgba(0,0,0,0.05);
        transition: 0.3s ease;
        border: 1px solid rgba(0,0,0,0.04);
        position: relative;
        overflow: hidden;
    }

    .order-card:hover { transform: translateY(-5px); box-shadow: 0 20px 30px -10px rgba(0,0,0,0.1); }

    .card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        flex-wrap: wrap;
        gap: 15px;
    }

    .order-id { font-size: 1.4rem; font-weight: 700; }
    
    .meta-data {
        font-size: 0.9rem;
        color: var(--text-light);
        background: #f9fafb;
        padding: 8px 15px;
        border-radius: 8px;
    }

    /* Status Badge */
    .status-badge {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        background: #e0f2fe;
        color: var(--primary);
    }
    
    .status-badge.Cancelled { background: #fee2e2; color: var(--danger); }
    .status-badge.Delivered { background: #dcfce7; color: var(--success); }

    /* Mini Progress Bar */
    .mini-progress {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin: 25px 0;
        position: relative;
    }
    
    .mini-progress::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 0;
        width: 100%;
        height: 3px;
        background: #e5e7eb;
        z-index: 0;
        transform: translateY(-50%);
    }

    .dot {
        width: 12px;
        height: 12px;
        background: #e5e7eb;
        border-radius: 50%;
        z-index: 1;
        position: relative;
    }
    
    .dot.active {
        background: var(--success);
        box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.2);
        transform: scale(1.2);
    }
    
    /* Cancelled State Override */
    .cancelled-msg {
        background: #fef2f2;
        color: var(--danger);
        padding: 15px;
        border-radius: 10px;
        text-align: center;
        font-weight: 600;
        margin: 20px 0;
    }

    /* Items List */
    .items-row {
        margin-top: 20px;
        padding-top: 20px;
        border-top: 1px dashed #e5e7eb;
        color: var(--text-dark);
        font-size: 0.95rem;
        line-height: 1.6;
    }

    .empty-state {
        text-align: center;
        padding: 60px;
        color: var(--text-light);
    }
    .empty-state i { font-size: 3rem; margin-bottom: 20px; color: #d1d5db; }
</style>
</head>
<body>

<div class="container">
    <div class="header">
        <h2>Order History</h2>
        <a href="dashboard.php" class="back-btn"><i class="fas fa-home"></i> Dashboard</a>
    </div>

    <?php if ($result->num_rows > 0): ?>
        <?php while($order = $result->fetch_assoc()): ?>
            <?php 
                $current_status_index = array_search($order['status'], $status_steps);
                if ($current_status_index === false) $current_status_index = -1;
            ?>
            
            <div class="order-card">
                <div class="card-header">
                    <div>
                        <div class="order-id">#<?= $order['id'] ?></div>
                        <div class="status-badge <?= $order['status'] ?>"><?= $order['status'] ?></div>
                    </div>
                    <div class="meta-data">
                        <i class="far fa-calendar-alt"></i> <?= date('M d, Y', strtotime($order['order_date'])) ?>
                    </div>
                </div>

                <?php if ($order['status'] != 'Cancelled'): ?>
                <!-- Visual Steps -->
                <div class="mini-progress">
                    <?php foreach ($status_steps as $index => $step): ?>
                        <div class="dot <?= ($index <= $current_status_index) ? 'active' : '' ?>" title="<?= $step ?>"></div>
                    <?php endforeach; ?>
                </div>
                <div style="text-align:right; font-size:0.8rem; color:var(--text-light); margin-top:-15px;">
                    Progress: <?= $order['status'] ?>
                </div>
                <?php else: ?>
                    <div class="cancelled-msg">
                        <i class="fas fa-ban"></i> This order has been cancelled.
                    </div>
                <?php endif; ?>

                <div class="items-row">
                    <strong style="color:var(--primary); display:block; margin-bottom:5px;">Package Contains:</strong>
                    <?php
                    $oid = $order['id'];
                    $items_query = $conn->query("SELECT p.name, oi.quantity FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = $oid");
                    $item_list = [];
                    while($item = $items_query->fetch_assoc()) {
                        $item_list[] = "<i class='fas fa-box-open' style='color:#ccc; margin-right:5px;'></i> {$item['quantity']} &times; {$item['name']}";
                    }
                    echo implode(" &nbsp;&nbsp;|&nbsp;&nbsp; ", $item_list);
                    ?>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-shopping-basket"></i>
            <h3>No orders yet</h3>
            <p>Once you make a purchase, it will appear here.</p>
            <br>
            <a href="products.php" class="back-btn" style="background:var(--primary); color:white;">Start Shopping</a>
        </div>
    <?php endif; ?>
</div>

</body>
</html>