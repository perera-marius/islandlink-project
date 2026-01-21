<?php
include 'db.php';

$cart_items = [
    ['item' => 'Laptop Stand', 'price' => 2500, 'qty' => 2],
    ['item' => 'USB Cable', 'price' => 500, 'qty' => 3]
];

// (Automated Calculation)
$grand_total = 0;
foreach ($cart_items as $item) {
    $grand_total += ($item['price'] * $item['qty']);
}

// (Payment Processing)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['pay_now'])) {
    
    $customer_id = 1; // Logged user ID
    $payment_status = "paid"; 

    $stmt = $conn->prepare("INSERT INTO orders (customer_id, amount, status, created_at) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("ids", $customer_id, $grand_total, $payment_status);
    
    if ($stmt->execute()) {
        echo "<script>alert('Payment Successful! Bill Generated.'); window.location.href='dashboard.php';</script>";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<div class="container">
    <h2>Automated Billing System</h2>
    
    <table border="1" cellpadding="10">
        <tr>
            <th>Item</th>
            <th>Price (Rs.)</th>
            <th>Qty</th>
            <th>Total (Rs.)</th>
        </tr>
        <?php foreach ($cart_items as $item): ?>
        <tr>
            <td><?= $item['item'] ?></td>
            <td><?= number_format($item['price'], 2) ?></td>
            <td><?= $item['qty'] ?></td>
            <td><?= number_format($item['price'] * $item['qty'], 2) ?></td>
        </tr>
        <?php endforeach; ?>
        <tr>
            <td colspan="3" align="right"><b>Grand Total</b></td>
            <td><b>Rs. <?= number_format($grand_total, 2) ?></b></td>
        </tr>
    </table>

    <form method="POST">
        <button type="submit" name="pay_now" class="btn-pay">Pay Now & Generate Bill</button>
    </form>
</div>