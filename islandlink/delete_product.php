<?php
include 'db.php';

if ($_SESSION['role'] != 'manager') {
    die("Access Denied");
}

$product_id = $_GET['id'];

/* Check if product is used in any order */
$check = $conn->query("
    SELECT COUNT(*) AS count 
    FROM order_items 
    WHERE product_id = $product_id
");

$data = $check->fetch_assoc();

if ($data['count'] > 0) {
    die("Cannot delete product. It has already been ordered.");
}

/* Get image name to delete file */
$imageResult = $conn->query("SELECT image FROM products WHERE id = $product_id");
$imageRow = $imageResult->fetch_assoc();
$imagePath = "uploads/" . $imageRow['image'];

/* Delete product */
$conn->query("DELETE FROM products WHERE id = $product_id");

/* Delete image file */
if (file_exists($imagePath)) {
    unlink($imagePath);
}

header("Location: manage_products.php");
