<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

requireLogin();

if (!isset($_GET['id'])) {
    redirect('pos.php');
}

$transaction_id = (int)$_GET['id'];

// Get transaction details
$query = "SELECT t.*, u.username as cashier_name 
          FROM transactions t 
          LEFT JOIN users u ON t.cashier_id = u.id 
          WHERE t.id = $transaction_id";
$result = mysqli_query($conn, $query);
$transaction = mysqli_fetch_assoc($result);

if (!$transaction) {
    redirect('pos.php');
}

// Get transaction items
$query = "SELECT ti.*, p.name as product_name 
          FROM transaction_items ti 
          LEFT JOIN products p ON ti.product_id = p.id 
          WHERE ti.transaction_id = $transaction_id";
$items = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Receipt #<?php echo $transaction['invoice_number']; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        /* Styles for both screen and print */
        body {
            font-family: Arial, sans-serif;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }

        .receipt-container {
            width: 80mm;
            margin: 20px auto;
            padding: 10px;
            background: white;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        /* Print specific styles */
        @media print {
            @page {
                size: 80mm auto;
                margin: 0;
            }

            html, body {
                width: 80mm;
                height: auto;
                margin: 0 auto !important;
                padding: 0 !important;
            }

            .receipt-container {
                width: 100%;
                margin: 0;
                padding: 5mm;
                box-shadow: none;
            }

            .no-print {
                display: none;
            }
            }
        }
        .receipt {
            font-family: monospace;
            font-size: 12px;
            line-height: 1.4;
        }
        .receipt-header {
            text-align: center;
            margin-bottom: 20px;
        }
        .receipt-footer {
            text-align: center;
            margin-top: 20px;
            font-size: 10px;
        }
        .receipt table {
            width: 100%;
            margin: 10px 0;
        }
        .receipt th, .receipt td {
            text-align: left;
            padding: 3px 0;
        }
        .receipt .amount {
            text-align: right;
        }
        .receipt .total {
            border-top: 1px dashed #000;
            font-weight: bold;
        }
    </style>
</head>
<body>
<div class="receipt-container">
    <div class="receipt">
        <div class="receipt-header">
            <h2>Kee Mart</h2>
            <p>Point of Sale System</p>
            <p><?php echo date('d/m/Y H:i', strtotime($transaction['created_at'])); ?></p>
            <p>Invoice: <?php echo $transaction['invoice_number']; ?></p>
            <p>Cashier: <?php echo $transaction['cashier_name']; ?></p>
        </div>
        
        <table>
            <tr>
                <th>Item</th>
                <th>Qty</th>
                <th class="amount">Price</th>
                <th class="amount">Total</th>
            </tr>
            <?php while ($item = mysqli_fetch_assoc($items)): ?>
            <tr>
                <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                <td><?php echo $item['quantity']; ?></td>
                <td class="amount"><?php echo formatCurrency($item['price']); ?></td>
                <td class="amount"><?php echo formatCurrency($item['subtotal']); ?></td>
            </tr>
            <?php endwhile; ?>
            
            <tr class="total">
                <td colspan="3">Total</td>
                <td class="amount"><?php echo formatCurrency($transaction['total_amount']); ?></td>
            </tr>
            <tr>
                <td colspan="3">Payment</td>
                <td class="amount"><?php echo formatCurrency($transaction['payment_amount']); ?></td>
            </tr>
            <tr>
                <td colspan="3">Change</td>
                <td class="amount"><?php echo formatCurrency($transaction['change_amount']); ?></td>
            </tr>
        </table>
        
        <div class="receipt-footer">
            <p>Thank you for shopping at Kee Mart!</p>
            <p>Please come again</p>
        </div>
    </div>
    
    <div class="no-print" style="text-align: center; margin-top: 20px;">
        <button onclick="window.print()" class="btn btn-primary">Print Receipt</button>
        <a href="pos.php" class="btn btn-secondary">Back to POS</a>
    </div>
</div>
</body>
</html>
