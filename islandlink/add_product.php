<?php
include 'db.php';

/* Only manager can add products */
if ($_SESSION['role'] != 'manager') {
    die("Access Denied");
}

$message = "";

if (isset($_POST['add'])) {
    $name = $_POST['name'];
    $price = $_POST['price'];

    // Image upload
    $imageName = $_FILES['image']['name'];
    $imageTmp = $_FILES['image']['tmp_name'];

    move_uploaded_file($imageTmp, "uploads/" . $imageName);

    $conn->query("
        INSERT INTO products (name, price, image)
        VALUES ('$name', $price, '$imageName')
    ");

    $message = "Product added successfully";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Product</title>
    <link rel="stylesheet" href="all.css">
</head>
<body>

<div class="container">
    <h2>Add New Product</h2>

    <?php if ($message): ?>
        <p class="success"><?= $message ?></p>
    <?php endif; ?>

    <!-- IMPORTANT: enctype added -->
    <form method="post" enctype="multipart/form-data">
        <input type="text" name="name" placeholder="Product Name" required>
        <input type="number" name="price" placeholder="Price" required>
        <input type="file" name="image" required>
        <button name="add">Add Product</button>
    </form>

    <a href="dashboard.php">Back</a>
</div>

</body>
</html>

