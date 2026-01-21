<?php
include 'db.php';
if (!isset($_SESSION['role'])) header("Location: login.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <!-- Use Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Keep original CSS reference just in case -->
    <link rel="stylesheet" href="all.css">
    
    <style>
        :root {
            /* Modern Palette */
            --bg-gradient: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            --glass-bg: rgba(255, 255, 255, 0.05);
            --glass-border: rgba(255, 255, 255, 0.1);
            --primary-accent: #38bdf8; /* Sky blue */
            --secondary-accent: #818cf8; /* Indigo */
            --text-main: #f8fafc;
            --text-secondary: #94a3b8;
            --danger-color: #ef4444;
            --card-hover: rgba(56, 189, 248, 0.15);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', 'Segoe UI', sans-serif;
            background: var(--bg-gradient);
            color: var(--text-main);
            height: 100vh;
            width: 100vw;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden; /* Prevent body scroll */
        }

        /* Main Floating Layout */
        .dashboard-container {
            width: 90vw;
            height: 85vh;
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 30px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            display: flex;
            overflow: hidden;
        }

        /* --- Left Sidebar (Info & Profile) --- */
        .sidebar {
            width: 300px;
            background: rgba(0, 0, 0, 0.2);
            border-right: 1px solid var(--glass-border);
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            flex-shrink: 0;
        }

        .brand-section h2 {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--text-main);
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 10px;
        }

        .brand-section h2 i {
            color: var(--primary-accent);
        }

        .user-info {
            margin-top: 20px;
        }

        .role-badge {
            display: inline-block;
            background: linear-gradient(90deg, var(--primary-accent), var(--secondary-accent));
            padding: 8px 16px;
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 10px;
            box-shadow: 0 4px 15px rgba(56, 189, 248, 0.3);
        }

        .welcome-text {
            color: var(--text-secondary);
            font-size: 1rem;
            line-height: 1.6;
            margin-top: 30px;
            border-left: 3px solid var(--secondary-accent);
            padding-left: 15px;
        }

        /* --- Right Content (Action Grid) --- */
        .main-content {
            flex: 1;
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            position: relative;
            overflow-y: auto; /* Allow scroll only inside if needed on tiny screens */
        }

        .grid-menu {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 30px;
            width: 100%;
            max-width: 900px;
        }

        .menu-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            border-radius: 24px;
            padding: 30px;
            aspect-ratio: 1.4/1; /* Rectangular landscape cards */
            text-decoration: none;
            color: var(--text-main);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            position: relative;
            overflow: hidden;
        }

        /* New Modifier Class for Small/Thin buttons */
        .menu-item.small-thin {
            padding: 10px 25px;
            aspect-ratio: auto;
            width: auto;
            height: auto;
            flex-direction: row;
            gap: 12px;
            border-radius: 50px; /* Pill shape */
            min-height: 40px;
        }

        .menu-item.small-thin i {
            font-size: 1.1rem;
            margin-bottom: 0; /* Remove vertical spacing */
        }

        .menu-item.small-thin span {
            font-size: 0.85rem;
            font-weight: 500;
        }

        .menu-item::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle at center, var(--card-hover), transparent 70%);
            opacity: 0;
            transition: opacity 0.4s;
        }

        .menu-item:hover {
            transform: translateY(-5px) scale(1.02);
            border-color: var(--primary-accent);
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
        }

        .menu-item:hover::after {
            opacity: 1;
        }

        .menu-item i {
            font-size: 2.5rem;
            margin-bottom: 15px;
            background: -webkit-linear-gradient(45deg, var(--primary-accent), var(--secondary-accent));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            transition: transform 0.3s;
        }

        .menu-item:hover i {
            transform: scale(1.1) rotate(-5deg);
        }

        .menu-item span {
            font-size: 1.1rem;
            font-weight: 600;
            z-index: 1;
        }

        /* --- Logout Button (Sidebar Bottom) --- */
        .logout-container {
            margin-top: auto;
        }

        .logout-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: var(--danger-color);
            padding: 12px;
            border-radius: 15px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
            width: 100%;
        }

        .logout-btn:hover {
            background: var(--danger-color);
            color: white;
            box-shadow: 0 5px 15px rgba(239, 68, 68, 0.3);
        }

        /* --- Responsive Adjustments --- */
        @media (max-width: 900px) {
            .dashboard-container {
                flex-direction: column;
                height: 95vh;
                width: 95vw;
                overflow-y: auto;
            }

            .sidebar {
                width: 100%;
                height: auto;
                padding: 20px;
                flex-direction: row;
                align-items: center;
                border-right: none;
                border-bottom: 1px solid var(--glass-border);
            }

            .welcome-text {
                display: none; /* Hide welcome text on mobile to save space */
            }

            .logout-container {
                margin: 0;
            }

            .main-content {
                padding: 20px;
                display: block; /* Allow natural flow */
            }

            .grid-menu {
                grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
            }

            .menu-item {
                padding: 20px;
                aspect-ratio: 1/1;
            }
            
            .menu-item.small-thin {
                width: 100%; /* Full width on mobile for easier tap */
            }
        }
    </style>
</head>
<body>

<div class="dashboard-container">
    <!-- Left Sidebar: Identity & Control -->
    <aside class="sidebar">
        <div>
            <div class="brand-section">
                <h2><i class="fas fa-layer-group"></i> Portal</h2>
            </div>
            
            <div class="user-info">
                <span class="role-badge"><?= $_SESSION['role'] ?></span>
            </div>

            <!-- Dynamic Welcome Text based on Role -->
            <?php if ($_SESSION['role'] == 'customer'): ?>
                <p class="welcome-text">Welcome back. Ready to place a new order or track your shipment?</p>
            <?php elseif ($_SESSION['role'] == 'rdc'): ?>
                <p class="welcome-text">Distribution Centre Access. Manage incoming orders efficiently.</p>
            <?php elseif ($_SESSION['role'] == 'manager'): ?>
                <p class="welcome-text">Manager Control Panel. Overview reports and product inventory.</p>
            <?php endif; ?>
        </div>

        <div class="logout-container">
            <a href="logout.php" class="logout-btn">
                <i class="fas fa-power-off"></i> <span>Sign Out</span>
            </a>
        </div>
    </aside>

    <!-- Right Content: Actions -->
    <main class="main-content">
        
        <!-- CUSTOMER MENU -->
        <?php if ($_SESSION['role'] == 'customer'): ?>
            <div class="grid-menu">





                <a href="products.php" class="menu-item">
                    <i class="fas fa-cart-plus"></i>
                    <span>Start Order</span>
                </a>





                <a href="track.html" class="menu-item">
                    <i class="fas fa-map-location-dot"></i>
                    <span>Track Order</span>
                </a>





            </div>
        <?php endif; ?>

        <!-- RDC MENU -->
        <?php if ($_SESSION['role'] == 'rdc'): ?>
            <!-- Changed wrapper to simple flex center, removed grid-menu class for this specific item -->
            <div style="display: flex; justify-content: center; width: 100%;">



                <a href="rdc_orders.php" class="menu-item small-thin">
                    <i class="fas fa-clipboard-check"></i>
                    <span>Active Orders</span>
                </a>




            </div>
        <?php endif; ?>

        <!-- MANAGER MENU -->
        <?php if ($_SESSION['role'] == 'manager'): ?>
            <div class="grid-menu" style="max-width: 1000px;"> <!-- Wider for manager -->


                <a href="manager_dashboard.php" class="menu-item">
                    <i class="fas fa-chart-pie"></i>
                    <span>Analytics</span>
                </a>





                <a href="manager_orders.php" class="menu-item">
                    <i class="fas fa-users-viewfinder"></i>
                    <span>Orders</span>
                </a>

  


                <a href="add_product.php" class="menu-item">
                    <i class="fas fa-circle-plus"></i>
                    <span>Add Item</span>
                </a>



                <a href="manage_products.php" class="menu-item">
                    <i class="fas fa-boxes-stacked"></i>
                    <span>Inventory</span>
                </a>



            </div>
        <?php endif; ?>

    </main>
</div>

</body>
</html>
