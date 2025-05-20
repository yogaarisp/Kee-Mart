<?php
require_once 'config/database.php';

// Delete existing data
mysqli_query($conn, "DELETE FROM transaction_items");
mysqli_query($conn, "DELETE FROM transactions");
mysqli_query($conn, "DELETE FROM products");
mysqli_query($conn, "DELETE FROM categories");
mysqli_query($conn, "DELETE FROM users WHERE username != 'admin'");

// Insert Categories
$categories = [
    'Minuman',
    'Makanan Ringan',
    'Sembako',
    'Perlengkapan Mandi',
    'Perlengkapan Rumah',
    'Obat-obatan',
    'Rokok',
    'Es Krim',
    'Susu & Dairy',
    'Makanan Instan'
];

$category_ids = [];
foreach ($categories as $category) {
    mysqli_query($conn, "INSERT INTO categories (name) VALUES ('$category')");
    $category_ids[$category] = mysqli_insert_id($conn);
}

// Sample products data
$products = [
    // Minuman
    ['DRK001', 'Coca Cola 1.5L', 'Minuman', 12000, 50],
    ['DRK002', 'Sprite 1.5L', 'Minuman', 12000, 45],
    ['DRK003', 'Fanta 1.5L', 'Minuman', 12000, 40],
    ['DRK004', 'Teh Pucuk 350ml', 'Minuman', 4000, 100],
    
    // Makanan Ringan
    ['SNK001', 'Chitato Original 75g', 'Makanan Ringan', 9500, 60],
    ['SNK002', 'Lays Rumput Laut 75g', 'Makanan Ringan', 9500, 55],
    ['SNK003', 'Oreo Original 137g', 'Makanan Ringan', 10000, 80],
    
    // Sembako
    ['GRC001', 'Beras Pandan Wangi 5kg', 'Sembako', 68000, 30],
    ['GRC002', 'Minyak Goreng 1L', 'Sembako', 23000, 50],
    ['GRC003', 'Gula Pasir 1kg', 'Sembako', 15000, 100],
    
    // Perlengkapan Mandi
    ['BTH001', 'Shampoo Clear 170ml', 'Perlengkapan Mandi', 23500, 40],
    ['BTH002', 'Sabun Lifebuoy 75g', 'Perlengkapan Mandi', 4500, 100],
    ['BTH003', 'Pasta Gigi Pepsodent 225g', 'Perlengkapan Mandi', 15500, 60],
    
    // Perlengkapan Rumah
    ['HOM001', 'Rinso Cair 800ml', 'Perlengkapan Rumah', 25000, 30],
    ['HOM002', 'Baygon 600ml', 'Perlengkapan Rumah', 38000, 25],
    ['HOM003', 'Tissue Nice 250s', 'Perlengkapan Rumah', 18000, 45],
    
    // Obat-obatan
    ['MED001', 'Paracetamol Strip', 'Obat-obatan', 12000, 100],
    ['MED002', 'Antangin Sachet', 'Obat-obatan', 3500, 150],
    
    // Rokok
    ['CIG001', 'Sampoerna Mild 16', 'Rokok', 29000, 50],
    ['CIG002', 'Gudang Garam Filter 12', 'Rokok', 19500, 60],
    
    // Es Krim
    ['ICE001', 'Magnum Classic', 'Es Krim', 15000, 30],
    ['ICE002', 'Cornetto Oreo', 'Es Krim', 10000, 35],
    
    // Susu & Dairy
    ['MLK001', 'Ultra Milk 1L', 'Susu & Dairy', 18000, 40],
    ['MLK002', 'Yakult 5pcs', 'Susu & Dairy', 12000, 50],
    
    // Makanan Instan
    ['INS001', 'Indomie Goreng', 'Makanan Instan', 3500, 200],
    ['INS002', 'Pop Mie Ayam', 'Makanan Instan', 5500, 150]
];

// Create placeholder images for categories
$category_images = [
    'Minuman' => 'beverage.jpg',
    'Makanan Ringan' => 'snack.jpg',
    'Sembako' => 'sembako.jpg',
    'Perlengkapan Mandi' => 'personal-care.jpg',
    'Perlengkapan Rumah' => 'household.jpg',
    'Obat-obatan' => 'medicine.jpg',
    'Rokok' => 'cigarette.jpg',
    'Es Krim' => 'ice-cream.jpg',
    'Susu & Dairy' => 'dairy.jpg',
    'Makanan Instan' => 'instant-food.jpg'
];

// Create image directory if not exists
if (!file_exists('assets/images/products')) {
    mkdir('assets/images/products', 0777, true);
}

// Create placeholder images
foreach ($category_images as $category => $image) {
    $image_path = 'assets/images/products/' . $image;
    if (!file_exists($image_path)) {
        $im = imagecreatetruecolor(400, 400);
        $bg_color = imagecolorallocate($im, rand(100, 255), rand(100, 255), rand(100, 255));
        imagefill($im, 0, 0, $bg_color);
        $text_color = imagecolorallocate($im, 0, 0, 0);
        imagestring($im, 5, 150, 190, $category, $text_color);
        imagejpeg($im, $image_path);
        imagedestroy($im);
    }
}

// Insert products
foreach ($products as $product) {
    $code = $product[0];
    $name = $product[1];
    $category = $product[2];
    $price = $product[3];
    $stock = $product[4];
    $image = 'assets/images/products/' . $category_images[$category];
    
    $category_id = $category_ids[$category];
    
    $query = "INSERT INTO products (code, name, category_id, price, stock, image) 
              VALUES ('$code', '$name', $category_id, $price, $stock, '$image')";
    mysqli_query($conn, $query);
}

echo "Sample data has been imported successfully!";
