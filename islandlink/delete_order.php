<?php
include 'db.php';

/* Allow ONLY RDC or Manager */
if ($_SESSION['role'] != 'rdc' && $_SESSION['role'] != 'manager') {
    die("Access Denied");
}

$order_id = $_GET['id'];

/* 1️⃣ Delete order items first */
$conn->query("
    DELETE FROM order_items 
    WHERE order_id = $order_id
");

/* 2️⃣ Delete the order */
$conn->query("
    DELETE FROM orders 
    WHERE id = $order_id
");

/* Redirect based on role */
if ($_SESSION['role'] == 'rdc') {
    header("Location: rdc_orders.php");
} else {
    header("Location: manager_orders.php");
}
