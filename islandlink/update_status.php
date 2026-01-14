<?php
include 'db.php';

if ($_SESSION['role'] != 'rdc') {
    die("Access Denied");
}

$id = $_GET['id'];
$status = $_GET['status'];

$conn->query("
    UPDATE orders
    SET status = '$status'
    WHERE id = $id
");

header("Location: rdc_orders.php");
