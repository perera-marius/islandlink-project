<?php
include 'db.php';
if ($_SESSION['role'] != 'customer') die("Access Denied");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Select Products</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --bg: #f8fafc;
            --card-bg: #ffffff;
            --text-dark: #1e293b;
            --text-light: #64748b;
            --primary: #2563eb;
            --primary-gradient: linear-gradient(135deg, #3b82f6, #2563eb);
            --accent: #eff6ff;
            --border: #e2e8f0;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            color: var(--text-dark);
            margin: 0;
            padding: 40px 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        /* Header */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
        }

        h1 { font-size: 2rem; font-weight: 800; letter-spacing: -1px; }

        .back-link {
            text-decoration: none;
            color: var(--text-light);
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: 0.3s;
        }
        .back-link:hover { color: var(--primary); transform: translateX(-5px); }

        /* Form Styling */
        .phone-group {
            background: var(--card-bg);
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            margin-bottom: 40px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            border: 1px solid var(--border);
        }

        .phone-group label {
            font-size: 0.9rem;
            font-weight: 700;
            text-transform: uppercase;
            color: var(--text-light);
            letter-spacing: 0.5px;
        }

        .phone-group input {
            width: 100%;
            padding: 15px;
            border: 2px solid var(--border);
            border-radius: 12px;
            font-size: 1.1rem;
            transition: 0.3s;
            outline: none;
        }

        .phone-group input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
        }

        /* Grid */
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
            gap: 30px;
            margin-bottom: 100px; /* Space for fixed button */
        }

        /* Product Card */
        .product-card {
            background: var(--card-bg);
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            border: 1px solid var(--border);
            position: relative;
        }

        .product-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
            border-color: var(--primary);
        }

        .img-wrapper {
            width: 100%;
            height: 200px;
            overflow: hidden;
            background: #f1f5f9;
        }

        .product-card img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s;
        }

        .product-card:hover img { transform: scale(1.1); }

        .card-details { padding: 20px; }
        
        .card-details h4 {
            font-size: 1.1rem;
            margin-bottom: 5px;
            font-weight: 700;
        }

        .price {
            color: var(--primary);
            font-weight: 600;
            font-size: 1.2rem;
            margin-bottom: 15px;
            display: block;
        }

        /* Controls */
        .controls {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: var(--accent);
            padding: 10px;
            border-radius: 12px;
        }

        .checkbox-wrapper {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        input[type="checkbox"] {
            width: 20px;
            height: 20px;
            accent-color: var(--primary);
            cursor: pointer;
        }

        .qty-wrapper {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 0.9rem;
            font-weight: 600;
        }

        input[type="number"] {
            width: 50px;
            padding: 5px;
            border: 1px solid var(--border);
            border-radius: 6px;
            text-align: center;
            font-weight: bold;
        }

        /* Floating Submit Button */
        .submit-bar {
            position: fixed;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%);
            width: 90%;
            max-width: 600px;
            z-index: 100;
        }

        .submit-btn {
            width: 100%;
            background: var(--primary-gradient);
            color: white;
            border: none;
            padding: 18px;
            border-radius: 100px;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 20px 25px -5px rgba(37, 99, 235, 0.4);
            transition: 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .submit-btn:hover {
            transform: scale(1.02);
            box-shadow: 0 25px 30px -5px rgba(37, 99, 235, 0.5);
        }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h1>Store Inventory</h1>
        <a href="dashboard.php" class="back-link"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
    </div>

    <form method="post" action="order.php">
        
        <div class="phone-group">
            <label><i class="fas fa-mobile-alt"></i> Contact Phone Number</label>
            <input type="text" name="phone" placeholder="e.g. 07XXXXXXXX" required>
        </div>

        <div class="product-grid">
            <?php
            $result = $conn->query("SELECT * FROM products");
            while ($row = $result->fetch_assoc()) {
                echo "
                <div class='product-card'>
                    <div class='img-wrapper'>
                        <img src='uploads/{$row['image']}' alt='{$row['name']}'>
                    </div>
                    <div class='card-details'>
                        <h4>{$row['name']}</h4>
                        <span class='price'>Rs. {$row['price']}</span>
                        
                        <div class='controls'>
                            <div class='checkbox-wrapper'>
                                <input type='checkbox' name='products[{$row['id']}]' value='1' id='chk_{$row['id']}'>
                                <label for='chk_{$row['id']}' style='font-size:0.9rem; cursor:pointer; font-weight:600;'>Select</label>
                            </div>
                            <div class='qty-wrapper'>
                                <span>Qty:</span>
                                <input type='number' name='qty[{$row['id']}]' value='1' min='1'>
                            </div>
                        </div>
                    </div>
                </div>
                ";
            }
            ?>
        </div>

        <div class="submit-bar">
            <button type="submit" class="submit-btn">
                <i class="fas fa-check-circle"></i> Confirm Order
            </button>
        </div>

    </form>
</div>

</body>
</html>