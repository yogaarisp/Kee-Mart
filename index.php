<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

requireLogin();

// Get today's total sales
$today = date('Y-m-d');
$query = "SELECT COUNT(*) as total_transactions, COALESCE(SUM(total_amount), 0) as total_sales 
          FROM transactions 
          WHERE DATE(created_at) = '$today'";
$result = mysqli_query($conn, $query);
$today_stats = mysqli_fetch_assoc($result);

// Get total products
$query = "SELECT COUNT(*) as total_products FROM products";
$result = mysqli_query($conn, $query);
$product_stats = mysqli_fetch_assoc($result);

// Get low stock products (less than 10 items)
$query = "SELECT * FROM products WHERE stock < 10 ORDER BY stock ASC LIMIT 5";
$low_stock = mysqli_query($conn, $query);

// Get recent transactions
$query = "SELECT t.*, u.username as cashier_name 
          FROM transactions t 
          LEFT JOIN users u ON t.cashier_id = u.id 
          ORDER BY t.created_at DESC LIMIT 5";
$recent_transactions = mysqli_query($conn, $query);
?>

<?php include 'views/common/header.php'; ?>

<h1>Dashboard</h1>

<div class="dashboard-stats">
    <div class="stat-card">
        <h3>Today's Sales</h3>
        <p class="stat-value"><?php echo formatCurrency($today_stats['total_sales']); ?></p>
        <p class="stat-label"><?php echo $today_stats['total_transactions']; ?> transactions</p>
    </div>
    
    <div class="stat-card">
        <h3>Total Products</h3>
        <p class="stat-value"><?php echo $product_stats['total_products']; ?></p>
        <p class="stat-label">items in inventory</p>
    </div>
</div>

<div class="row">
    <div class="card" style="width: 48%; float: left; margin-right: 2%;">
        <h3>Low Stock Alert</h3>
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Stock</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($product = mysqli_fetch_assoc($low_stock)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($product['name']); ?></td>
                        <td><?php echo $product['stock']; ?></td>
                        <td>
                            <a href="edit_product.php?id=<?php echo $product['id']; ?>" class="btn btn-primary">Update Stock</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="card" style="width: 48%; float: left;">
        <h3>Recent Transactions</h3>
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Invoice</th>
                        <th>Amount</th>
                        <th>Cashier</th>
                        <th>Time</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($transaction = mysqli_fetch_assoc($recent_transactions)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($transaction['invoice_number']); ?></td>
                        <td><?php echo formatCurrency($transaction['total_amount']); ?></td>
                        <td><?php echo htmlspecialchars($transaction['cashier_name']); ?></td>
                        <td><?php echo date('H:i', strtotime($transaction['created_at'])); ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div style="clear: both;"></div>

<?php include 'views/common/footer.php'; ?>
