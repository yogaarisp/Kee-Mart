<?php
require_once 'database.php';

// Sample Categories
$categories = [
    "Beverages",
    "Snacks",
    "Instant Food",
    "Personal Care",
    "Household",
    "Stationery"
];

foreach ($categories as $category) {
    mysqli_query($conn, "INSERT INTO categories (name) VALUES ('$category')");
}

// Get category IDs
$category_ids = [];
$result = mysqli_query($conn, "SELECT id, name FROM categories");
while ($row = mysqli_fetch_assoc($result)) {
    $category_ids[$row['name']] = $row['id'];
}

// Create sample product images
$sample_images = [
    'assets/images/products/beverage.jpg',
    'assets/images/products/snack.jpg',
    'assets/images/products/instant-food.jpg',
    'assets/images/products/personal-care.jpg',
    'assets/images/products/household.jpg',
    'assets/images/products/stationery.jpg'
];

// Create placeholder images
foreach ($sample_images as $image) {
    $dir = dirname($image);
    if (!file_exists($dir)) {
        mkdir($dir, 0777, true);
    }
    
    // Create a simple colored rectangle as placeholder
    $im = imagecreatetruecolor(400, 400);
    $bg_color = imagecolorallocate($im, rand(100, 255), rand(100, 255), rand(100, 255));
    imagefill($im, 0, 0, $bg_color);
    
    // Add some text
    $text_color = imagecolorallocate($im, 0, 0, 0);
    $text = basename($image, '.jpg');
    imagestring($im, 5, 150, 190, $text, $text_color);
    
    imagejpeg($im, $image);
    imagedestroy($im);
}

// Sample Products
$products = [
    // Beverages
    ['BEV001', 'Coca Cola 1.5L', $category_ids['Beverages'], 12000, 50, 'assets/images/products/beverage.jpg'],
    ['BEV002', 'Mineral Water 600ml', $category_ids['Beverages'], 4000, 100, 'assets/images/products/beverage.jpg'],
    ['BEV003', 'Orange Juice 500ml', $category_ids['Beverages'], 8000, 30, 'assets/images/products/beverage.jpg'],
    
    // Snacks
    ['SNK001', 'Potato Chips Original', $category_ids['Snacks'], 9500, 40, 'assets/images/products/snack.jpg'],
    ['SNK002', 'Chocolate Bar', $category_ids['Snacks'], 12500, 45, 'assets/images/products/snack.jpg'],
    ['SNK003', 'Mixed Nuts 100g', $category_ids['Snacks'], 15000, 25, 'assets/images/products/snack.jpg'],
    
    // Instant Food
    ['FOOD001', 'Instant Noodles Chicken', $category_ids['Instant Food'], 3500, 200, 'assets/images/products/instant-food.jpg'],
    ['FOOD002', 'Instant Soup Pack', $category_ids['Instant Food'], 4500, 50, 'assets/images/products/instant-food.jpg'],
    ['FOOD003', 'Ready to Eat Rice', $category_ids['Instant Food'], 12000, 30, 'assets/images/products/instant-food.jpg'],
    
    // Personal Care
    ['PC001', 'Toothpaste 120g', $category_ids['Personal Care'], 12000, 40, 'assets/images/products/personal-care.jpg'],
    ['PC002', 'Shampoo 200ml', $category_ids['Personal Care'], 18000, 35, 'assets/images/products/personal-care.jpg'],
    ['PC003', 'Body Soap 85g', $category_ids['Personal Care'], 4500, 60, 'assets/images/products/personal-care.jpg'],
    
    // Household
    ['HH001', 'Dish Soap 500ml', $category_ids['Household'], 15000, 40, 'assets/images/products/household.jpg'],
    ['HH002', 'Tissue Paper', $category_ids['Household'], 8500, 100, 'assets/images/products/household.jpg'],
    ['HH003', 'Air Freshener', $category_ids['Household'], 22000, 25, 'assets/images/products/household.jpg'],
    
    // Stationery
    ['ST001', 'Ballpoint Pen', $category_ids['Stationery'], 3500, 100, 'assets/images/products/stationery.jpg'],
    ['ST002', 'Notebook A5', $category_ids['Stationery'], 12000, 50, 'assets/images/products/stationery.jpg'],
    ['ST003', 'Pencil 2B', $category_ids['Stationery'], 2500, 150, 'assets/images/products/stationery.jpg']
];

foreach ($products as $product) {
    $query = "INSERT INTO products (code, name, category_id, price, stock, image) 
              VALUES ('$product[0]', '$product[1]', $product[2], $product[3], $product[4], '$product[5]')";
    mysqli_query($conn, $query);
}

// Sample Users
$users = [
    ['cashier1', 'cashier123', 'cashier'],
    ['cashier2', 'cashier123', 'cashier'],
    ['manager', 'manager123', 'admin']
];

foreach ($users as $user) {
    $username = $user[0];
    $password = password_hash($user[1], PASSWORD_DEFAULT);
    $role = $user[2];
    
    $query = "INSERT INTO users (username, password, role) 
              VALUES ('$username', '$password', '$role')";
    mysqli_query($conn, $query);
}

// Sample Transactions
$cashiers = [];
$result = mysqli_query($conn, "SELECT id FROM users WHERE role = 'cashier'");
while ($row = mysqli_fetch_assoc($result)) {
    $cashiers[] = $row['id'];
}

$products_data = [];
$result = mysqli_query($conn, "SELECT id, price FROM products");
while ($row = mysqli_fetch_assoc($result)) {
    $products_data[$row['id']] = $row['price'];
}

// Create 10 sample transactions
for ($i = 0; $i < 10; $i++) {
    $cashier_id = $cashiers[array_rand($cashiers)];
    $total_amount = 0;
    
    // Start transaction
    mysqli_begin_transaction($conn);
    
    try {
        // Create transaction
        $invoice_number = 'INV-' . date('Ymd') . '-' . sprintf('%04d', $i + 1);
        
        // Random items (1-5 items per transaction)
        $items = [];
        $num_items = rand(1, 5);
        $product_ids = array_rand($products_data, $num_items);
        if (!is_array($product_ids)) $product_ids = [$product_ids];
        
        foreach ($product_ids as $product_id) {
            $quantity = rand(1, 3);
            $price = $products_data[$product_id];
            $subtotal = $price * $quantity;
            $total_amount += $subtotal;
            
            $items[] = [
                'product_id' => $product_id,
                'quantity' => $quantity,
                'price' => $price,
                'subtotal' => $subtotal
            ];
        }
        
        $payment_amount = ceil($total_amount / 1000) * 1000; // Round up to nearest thousand
        $change_amount = $payment_amount - $total_amount;
        
        // Insert transaction
        $query = "INSERT INTO transactions (invoice_number, cashier_id, total_amount, payment_amount, change_amount) 
                  VALUES ('$invoice_number', $cashier_id, $total_amount, $payment_amount, $change_amount)";
        mysqli_query($conn, $query);
        $transaction_id = mysqli_insert_id($conn);
        
        // Insert items
        foreach ($items as $item) {
            $query = "INSERT INTO transaction_items (transaction_id, product_id, quantity, price, subtotal) 
                      VALUES ($transaction_id, {$item['product_id']}, {$item['quantity']}, {$item['price']}, {$item['subtotal']})";
            mysqli_query($conn, $query);
            
            // Update stock
            $query = "UPDATE products 
                      SET stock = stock - {$item['quantity']} 
                      WHERE id = {$item['product_id']}";
            mysqli_query($conn, $query);
        }
        
        mysqli_commit($conn);
    } catch (Exception $e) {
        mysqli_rollback($conn);
        echo "Error creating sample transaction: " . $e->getMessage();
    }
}

echo "Sample data has been successfully added to the database.";
