<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

requireLogin();

if (!isset($_GET['id'])) {
    exit('Transaction ID not provided');
}

$transaction_id = (int)$_GET['id'];

// Get transaction items
$query = "SELECT ti.*, p.name as product_name, p.code as product_code 
          FROM transaction_items ti 
          LEFT JOIN products p ON ti.product_id = p.id 
          WHERE ti.transaction_id = $transaction_id";
$items = mysqli_query($conn, $query);

if (!$items) {
    exit('Error fetching transaction details');
}
?>

<div class="table-container">
    <table class="table">
        <thead>
            <tr>
                <th>Product Code</th>
                <th>Product Name</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($item = mysqli_fetch_assoc($items)): ?>
            <tr>
                <td><?php echo htmlspecialchars($item['product_code']); ?></td>
                <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                <td><?php echo $item['quantity']; ?></td>
                <td><?php echo formatCurrency($item['price']); ?></td>
                <td><?php echo formatCurrency($item['subtotal']); ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
