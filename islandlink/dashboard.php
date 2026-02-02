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
    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --bg-dark: #0f172a;
            --bg-gradient: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            --glass-bg: rgba(255, 255, 255, 0.03);
            --glass-border: rgba(255, 255, 255, 0.08);
            --primary: #38bdf8;
            --primary-glow: rgba(56, 189, 248, 0.3);
            --secondary: #818cf8;
            --text-main: #f1f5f9;
            --text-muted: #94a3b8;
            --danger: #ef4444;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg-gradient);
            color: var(--text-main);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
        }

        /* Floating Glass Container */
        .dashboard-container {
            width: 90vw;
            height: 85vh;
            background: rgba(15, 23, 42, 0.7);
            backdrop-filter: blur(25px);
            -webkit-backdrop-filter: blur(25px);
            border: 1px solid var(--glass-border);
            border-radius: 32px;
            box-shadow: 0 50px 100px -20px rgba(0, 0, 0, 0.5);
            display: flex;
            overflow: hidden;
            animation: slideUp 0.8s cubic-bezier(0.2, 0.8, 0.2, 1);
        }

        /* Sidebar */
        .sidebar {
            width: 320px;
            background: rgba(0, 0, 0, 0.2);
            border-right: 1px solid var(--glass-border);
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
        }

        .brand-section h2 {
            font-size: 2rem;
            font-weight: 800;
            background: linear-gradient(to right, #fff, #94a3b8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .brand-section i {
            color: var(--primary);
            filter: drop-shadow(0 0 10px var(--primary-glow));
        }

        .role-badge {
            margin-top: 25px;
            display: inline-block;
            background: rgba(56, 189, 248, 0.1);
            border: 1px solid rgba(56, 189, 248, 0.2);
            color: var(--primary);
            padding: 8px 16px;
            border-radius: 100px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.5px;
        }

        .welcome-text {
            color: var(--text-muted);
            margin-top: 30px;
            line-height: 1.7;
            font-size: 0.95rem;
            border-left: 2px solid var(--secondary);
            padding-left: 20px;
        }

        /* Logout Button */
        .logout-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            background: rgba(239, 68, 68, 0.1);
            color: var(--danger);
            padding: 14px;
            border-radius: 16px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
            border: 1px solid transparent;
        }

        .logout-btn:hover {
            background: var(--danger);
            color: white;
            box-shadow: 0 10px 20px rgba(239, 68, 68, 0.2);
        }

        /* Main Content */
        .main-content {
            flex: 1;
            padding: 50px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .grid-menu {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 25px;
            width: 100%;
            max-width: 1000px;
            margin: 0 auto;
        }

        /* Cards */
        .menu-item {
            position: relative;
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            border-radius: 24px;
            padding: 40px;
            text-decoration: none;
            color: white;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 20px;
            transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            overflow: hidden;
            aspect-ratio: 1.4/1;
        }

        .menu-item::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(255,255,255,0.1), transparent);
            opacity: 0;
            transition: 0.4s;
        }

        .menu-item i {
            font-size: 3rem;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            transition: 0.4s;
            filter: drop-shadow(0 10px 20px rgba(0,0,0,0.2));
        }

        .menu-item span {
            font-size: 1.2rem;
            font-weight: 500;
            z-index: 2;
        }

        .menu-item:hover {
            transform: translateY(-10px);
            border-color: rgba(255,255,255,0.2);
            box-shadow: 0 20px 40px rgba(0,0,0,0.4);
        }

        .menu-item:hover::before { opacity: 1; }
        .menu-item:hover i { transform: scale(1.1) rotate(-5deg); }

        /* Small Thin Card Variant */
        .menu-item.small-thin {
            aspect-ratio: auto;
            flex-direction: row;
            padding: 20px 40px;
            border-radius: 100px;
            width: auto;
            background: linear-gradient(90deg, rgba(56, 189, 248, 0.1), rgba(129, 140, 248, 0.1));
            border: 1px solid rgba(56, 189, 248, 0.3);
        }
        
        .menu-item.small-thin i { font-size: 1.5rem; margin: 0; }
        .menu-item.small-thin span { font-size: 1rem; }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(50px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Mobile */
        @media (max-width: 900px) {
            .dashboard-container { flex-direction: column; width: 100%; height: 100%; border-radius: 0; }
            .sidebar { width: 100%; padding: 20px; flex-direction: row; align-items: center; border-right: none; border-bottom: 1px solid var(--glass-border); height: auto; }
            .welcome-text, .logout-span { display: none; }
            .logout-btn span { display: none; } 
            .logout-btn { padding: 10px; border-radius: 50%; width: 40px; height: 40px; }
            .main-content { padding: 20px; display: block; }
            .menu-item { padding: 20px; aspect-ratio: 16/9; }
        }
    </style>
</head>
<body>

<div class="dashboard-container">
    <!-- Sidebar -->
    <aside class="sidebar">
        <div>
            <div class="brand-section">
                <h2><i class="fas fa-cube"></i> Nexus</h2>
            </div>
            
            <div class="user-info">
                <span class="role-badge"><?= $_SESSION['role'] ?></span>
            </div>

            <?php if ($_SESSION['role'] == 'customer'): ?>
                <p class="welcome-text">Your portal for ordering and tracking shipments in real-time.</p>
            <?php elseif ($_SESSION['role'] == 'rdc'): ?>
                <p class="welcome-text">Distribution Hub. Manage logistics and active dispatch queues.</p>
            <?php elseif ($_SESSION['role'] == 'manager'): ?>
                <p class="welcome-text">Executive Control. Inventory analytics and order oversight.</p>
            <?php endif; ?>
        </div>

        <div class="logout-container">
            <a href="logout.php" class="logout-btn">
                <i class="fas fa-sign-out-alt"></i> <span>Disconnect</span>
            </a>
        </div>
    </aside>

    <!-- Content -->
    <main class="main-content">
        
        <!-- CUSTOMER -->
        <?php if ($_SESSION['role'] == 'customer'): ?>
            <div class="grid-menu">
                <a href="products.php" class="menu-item">
                    <i class="fas fa-shopping-bag"></i>
                    <span>New Order</span>
                </a>
                <a href="orderview.php" class="menu-item">
                    <i class="fas fa-satellite-dish"></i>
                    <span>Track History</span>
                </a>
            </div>
        <?php endif; ?>

        <!-- RDC -->
        <?php if ($_SESSION['role'] == 'rdc'): ?>
            <div style="display: flex; justify-content: center; width: 100%;">
                <a href="rdc_orders.php" class="menu-item small-thin">
                    <i class="fas fa-conveyor-belt"></i>
                    <span>Process Queue</span>
                </a>
            </div>
        <?php endif; ?>

        <!-- MANAGER -->
        <?php if ($_SESSION['role'] == 'manager'): ?>
            <div class="grid-menu">
                <a href="manager_dashboard.php" class="menu-item">
                    <i class="fas fa-chart-line"></i>
                    <span>Analytics</span>
                </a>
                <a href="manager_orders.php" class="menu-item">
                    <i class="fas fa-network-wired"></i>
                    <span>Global Orders</span>
                </a>
                <a href="add_product.php" class="menu-item">
                    <i class="fas fa-plus-hexagon"></i>
                    <span>Add Item</span>
                </a>
                <a href="manage_products.php" class="menu-item">
                    <i class="fas fa-boxes"></i>
                    <span>Inventory</span>
                </a>
            </div>
        <?php endif; ?>

    </main>
</div>

</body>
</html>