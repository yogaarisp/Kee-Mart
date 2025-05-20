<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

requireLogin();

// Get date range filter
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-d');
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');

// Get transactions
$query = "SELECT t.*, u.username as cashier_name 
          FROM transactions t 
          LEFT JOIN users u ON t.cashier_id = u.id 
          WHERE DATE(t.created_at) BETWEEN '$start_date' AND '$end_date' 
          ORDER BY t.created_at DESC";
$transactions = mysqli_query($conn, $query);

// Calculate summary
$summary_query = "SELECT 
                    COUNT(*) as total_transactions,
                    SUM(total_amount) as total_sales,
                    AVG(total_amount) as average_sale
                 FROM transactions 
                 WHERE DATE(created_at) BETWEEN '$start_date' AND '$end_date'";
$summary = mysqli_fetch_assoc(mysqli_query($conn, $summary_query));
?>

<?php include 'views/common/header.php'; ?>

<div class="content-header">
    <h1>Transaction History</h1>
</div>

<!-- Date Filter -->
<div class="card" style="margin-bottom: 20px;">
    <form method="GET" class="form-inline">
        <div class="form-group" style="margin-right: 10px;">
            <label style="margin-right: 5px;">Start Date</label>
            <input type="date" name="start_date" value="<?php echo $start_date; ?>" class="form-control">
        </div>
        <div class="form-group" style="margin-right: 10px;">
            <label style="margin-right: 5px;">End Date</label>
            <input type="date" name="end_date" value="<?php echo $end_date; ?>" class="form-control">
        </div>
        <button type="submit" class="btn btn-primary">Filter</button>
    </form>
</div>

<!-- Summary Cards -->
<div class="dashboard-stats">
    <div class="stat-card">
        <h3>Total Transactions</h3>
        <p class="stat-value"><?php echo $summary['total_transactions']; ?></p>
    </div>
    <div class="stat-card">
        <h3>Total Sales</h3>
        <p class="stat-value"><?php echo formatCurrency($summary['total_sales']); ?></p>
    </div>
    <div class="stat-card">
        <h3>Average Sale</h3>
        <p class="stat-value"><?php echo formatCurrency($summary['average_sale']); ?></p>
    </div>
</div>

<!-- Transactions Table -->
<div class="card">
    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>Invoice</th>
                    <th>Date/Time</th>
                    <th>Cashier</th>
                    <th>Total Amount</th>
                    <th>Payment</th>
                    <th>Change</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($transaction = mysqli_fetch_assoc($transactions)): ?>
                <tr>
                    <td><?php echo htmlspecialchars($transaction['invoice_number']); ?></td>
                    <td><?php echo date('d/m/Y H:i', strtotime($transaction['created_at'])); ?></td>
                    <td><?php echo htmlspecialchars($transaction['cashier_name']); ?></td>
                    <td><?php echo formatCurrency($transaction['total_amount']); ?></td>
                    <td><?php echo formatCurrency($transaction['payment_amount']); ?></td>
                    <td><?php echo formatCurrency($transaction['change_amount']); ?></td>
                    <td>
                        <button class="btn btn-primary" onclick="viewDetails(<?php echo $transaction['id']; ?>)">Details</button>
                        <a href="print_receipt.php?id=<?php echo $transaction['id']; ?>" class="btn btn-secondary" target="_blank">Print</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Transaction Details Modal -->
<div id="detailsModal" class="modal" style="display: none;">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h3>Transaction Details</h3>
        <div id="modalContent"></div>
    </div>
</div>

<style>
.modal {
    display: none;
    position: fixed;
    z-index: 1;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.4);
}

.modal-content {
    background-color: #fefefe;
    margin: 15% auto;
    padding: 20px;
    border: 1px solid #888;
    width: 80%;
    max-width: 600px;
    border-radius: var(--border-radius);
}

.close {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
}

.close:hover {
    color: black;
}
</style>

<script>
function viewDetails(transactionId) {
    const modal = document.getElementById('detailsModal');
    const modalContent = document.getElementById('modalContent');
    
    // Fetch transaction details
    fetch(`transaction_details.php?id=${transactionId}`)
        .then(response => response.text())
        .then(html => {
            modalContent.innerHTML = html;
            modal.style.display = 'block';
        });
}

// Close modal
document.querySelector('.close').onclick = function() {
    document.getElementById('detailsModal').style.display = 'none';
}

window.onclick = function(event) {
    const modal = document.getElementById('detailsModal');
    if (event.target == modal) {
        modal.style.display = 'none';
    }
}
</script>

<?php include 'views/common/footer.php'; ?>
