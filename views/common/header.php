<?php 
require_once 'config/database.php';
require_once 'includes/functions.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kee Mart - Point of Sale System</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php if (isLoggedIn()): ?>
    <nav class="navbar">
        <div class="container">
          
            <div class="nav-links"> 
                 <a href="index.php" class="navbar-brand">
                <i class="bi bi-shop"></i>
                <span>Kee Mart POS</span>
            </a>
                <?php if (isAdmin()): ?>
                    <a href="products.php" class="btn">
                        <i class="bi bi-box-seam"></i>
                        <span>Products</span>
                    </a>
                    <a href="categories.php" class="btn">
                        <i class="bi bi-tags"></i>
                        <span>Categories</span>
                    </a>
                    <a href="users.php" class="btn">
                        <i class="bi bi-people"></i>
                        <span>Users</span>
                    </a>
                <?php endif; ?>
                <a href="transactions.php" class="btn">
                    <i class="bi bi-receipt"></i>
                    <span>Transactions</span>
                </a>
                <a href="pos.php" class="btn btn-primary">
                    <i class="bi bi-cart3"></i>
                    <span>POS</span>
                </a>
                <a href="logout.php" class="btn btn-danger">
                    <i class="bi bi-box-arrow-right"></i>
                    <span>Logout</span>
                </a>
            </div>
        </div>
    </nav>
    <?php endif; ?>
    <div class="container">
        <?php echo displayFlashMessage(); ?>
