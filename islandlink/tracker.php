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
    $_GET['search'] = $order_id;
}

// Handle Search
if (isset($_GET['search'])) {
    $search = $conn->real_escape_string($_GET['search']);
    $sql = "SELECT o.*, u.username FROM orders o JOIN users u ON o.user_id = u.id WHERE o.id = '$search' OR o.phone = '$search'";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        $order = $result->fetch_assoc();
        $oid = $order['id'];
        $items_result = $conn->query("SELECT p.name, p.price, p.image, oi.quantity FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = $oid");
        while($row = $items_result->fetch_assoc()) { $items[] = $row; }
    } else {
        $msg = "No order found with ID or Phone: $search";
    }
}

$status_steps = ['Confirmed', 'Processing', 'Shipped', 'Out for Delivery', 'Delivered'];
$current_status_index = -1;
if ($order) {
    $current_status_index = array_search($order['status'], $status_steps);
    if ($current_status_index === false) $current_status_index = -1;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Order Tracker</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
<style>
    :root {
        --bg-dark: #0f172a;
        --card-bg: #1e293b;
        --text-main: #f8fafc;
        --text-sub: #94a3b8;
        --primary: #6366f1;
        --primary-glow: rgba(99, 102, 241, 0.4);
        --success: #10b981;
        --danger: #ef4444;
    }

    body {
        font-family: 'Inter', sans-serif;
        background: var(--bg-dark);
        color: var(--text-main);
        padding: 40px 20px;
    }

    .container {
        max-width: 1000px;
        margin: 0 auto;
    }

    /* Header & Back Link */
    .top-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 40px;
    }
    
    .back-btn {
        color: var(--text-sub);
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: 0.3s;
    }
    .back-btn:hover { color: var(--primary); }

    /* Search Box */
    .search-wrapper {
        background: var(--card-bg);
        padding: 20px;
        border-radius: 16px;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
        display: flex;
        gap: 10px;
        border: 1px solid rgba(255,255,255,0.05);
        margin-bottom: 20px;
    }

    .search-wrapper input {
        flex: 1;
        background: transparent;
        border: none;
        color: white;
        font-size: 1.1rem;
        outline: none;
    }

    .search-wrapper button {
        background: var(--primary);
        color: white;
        border: none;
        padding: 12px 24px;
        border-radius: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: 0.3s;
    }
    .search-wrapper button:hover { background: #4f46e5; box-shadow: 0 0 15px var(--primary-glow); }

    /* Result Card */
    .result-card {
        background: var(--card-bg);
        border-radius: 20px;
        padding: 40px;
        border: 1px solid rgba(255,255,255,0.05);
        animation: fadeIn 0.5s ease;
    }

    /* Progress Steps */
    .progress-track {
        display: flex;
        justify-content: space-between;
        margin: 50px 0;
        position: relative;
    }
    .progress-track::before {
        content: '';
        position: absolute;
        top: 20px;
        left: 0;
        width: 100%;
        height: 4px;
        background: #334155;
        border-radius: 10px;
        z-index: 0;
    }
    
    .step {
        z-index: 1;
        text-align: center;
        position: relative;
        flex: 1;
    }
    
    .circle {
        width: 44px;
        height: 44px;
        background: #334155;
        border-radius: 50%;
        margin: 0 auto;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        border: 4px solid var(--card-bg);
        transition: 0.3s;
    }
    
    .step.completed .circle, .step.active .circle {
        background: var(--success);
        box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.2);
    }
    
    .step p { margin-top: 15px; font-size: 0.85rem; color: var(--text-sub); }
    .step.active p { color: white; font-weight: 700; }

    /* Info Grid */
    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        background: rgba(255,255,255,0.03);
        padding: 25px;
        border-radius: 16px;
        margin-bottom: 30px;
    }
    
    .info-item h4 { color: var(--text-sub); font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px; }
    .info-item p { font-size: 1.1rem; font-weight: 600; }

    /* Modern Table */
    .table-container { overflow-x: auto; }
    table { width: 100%; border-collapse: separate; border-spacing: 0; }
    th { text-align: left; color: var(--text-sub); padding: 15px; border-bottom: 1px solid #334155; }
    td { padding: 15px; border-bottom: 1px solid rgba(255,255,255,0.05); vertical-align: middle; }
    
    td img {
        width: 50px;
        height: 50px;
        object-fit: cover;
        border-radius: 8px;
    }

    /* Update Section */
    .update-zone {
        margin-top: 40px;
        padding-top: 30px;
        border-top: 1px dashed #334155;
    }
    
    .update-form { display: flex; gap: 15px; flex-wrap: wrap; }
    
    select {
        background: #0f172a;
        color: white;
        border: 1px solid #334155;
        padding: 12px 20px;
        border-radius: 10px;
        flex: 1;
        font-size: 1rem;
        cursor: pointer;
    }
    
    .btn-update {
        background: var(--primary);
        color: white;
        border: none;
        padding: 12px 30px;
        border-radius: 10px;
        font-weight: 600;
        cursor: pointer;
        transition: 0.3s;
    }
    .btn-update:hover { background: #4f46e5; }

    .alert { padding: 15px; background: rgba(56, 189, 248, 0.1); border-radius: 10px; color: #38bdf8; margin-bottom: 20px; }

    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
</style>
</head>
<body>

<div class="container">
    <div class="top-bar">
        <h2><i class="fas fa-radar"></i> Order Surveillance</h2>
        <a href="dashboard.php" class="back-btn"><i class="fas fa-chevron-left"></i> Dashboard</a>
    </div>

    <form method="get" action="tracker.php" class="search-wrapper">
        <input type="text" name="search" placeholder="Enter Order ID or Phone Number..." required value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
        <button type="submit"><i class="fas fa-search"></i> Track</button>
    </form>

    <?php if ($msg): ?>
        <div class="alert"><i class="fas fa-info-circle"></i> <?= $msg ?></div>
    <?php endif; ?>

    <?php if ($order): ?>
    <div class="result-card">
        
        <div style="display:flex; justify-content:space-between; align-items:center;">
            <h3><span style="color:var(--text-sub)">Order</span> #<?= $order['id'] ?></h3>
            <span style="background:var(--primary); padding:6px 14px; border-radius:20px; font-size:0.85rem; font-weight:600;">
                <?= $order['status'] ?>
            </span>
        </div>

        <div class="progress-track">
            <?php foreach ($status_steps as $index => $step): ?>
                <?php 
                    $class = '';
                    if ($index < $current_status_index) $class = 'completed';
                    if ($index == $current_status_index) $class = 'active';
                ?>
                <div class="step <?= $class ?>">
                    <div class="circle">
                        <?php if ($index < $current_status_index): ?>
                            <i class="fas fa-check"></i>
                        <?php else: ?>
                            <?= $index + 1 ?>
                        <?php endif; ?>
                    </div>
                    <p><?= $step ?></p>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="info-grid">
            <div class="info-item">
                <h4>Customer</h4>
                <p><?= $order['username'] ?></p>
            </div>
            <div class="info-item">
                <h4>Phone</h4>
                <p><?= $order['phone'] ?></p>
            </div>
            <div class="info-item">
                <h4>Placed On</h4>
                <p><?= date('M d, Y', strtotime($order['order_date'])) ?></p>
            </div>
            <div class="info-item">
                <h4>Expected</h4>
                <p><?= date('M d, Y', strtotime($order['delivery_date'])) ?></p>
            </div>
        </div>

        <h4 style="margin-bottom:20px; color:var(--text-sub);">Package Contents</h4>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Name</th>
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
                        <td><img src="uploads/<?= $item['image'] ?>" alt="Product"></td>
                        <td><?= $item['name'] ?></td>
                        <td>Rs. <?= $item['price'] ?></td>
                        <td>x <?= $item['quantity'] ?></td>
                        <td style="color:var(--success); font-weight:600;">Rs. <?= $total ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div style="text-align:right; margin-top:20px; font-size:1.2rem;">
            Grand Total: <b style="color:var(--success);">Rs. <?= $grand_total ?></b>
        </div>

        <div class="update-zone">
            <h4 style="margin-bottom:15px; color:var(--text-sub);">Manage Status</h4>
            <form method="post" class="update-form">
                <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                <select name="new_status">
                    <?php foreach ($status_steps as $step): ?>
                        <option value="<?= $step ?>" <?= ($order['status'] == $step) ? 'selected' : '' ?>><?= $step ?></option>
                    <?php endforeach; ?>
                    <option value="Cancelled">Cancelled</option>
                </select>
                <button type="submit" name="update_status" class="btn-update">Update Status</button>
            </form>
        </div>

    </div>
    <?php endif; ?>

</div>

</body>
</html>