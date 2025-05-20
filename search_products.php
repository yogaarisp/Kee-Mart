<?php
require_once 'config/database.php';

$search = isset($_GET['search']) ? $_GET['search'] : '';
$query = "SELECT p.*, c.name as category_name 
         FROM products p 
         LEFT JOIN categories c ON p.category_id = c.id
         WHERE p.name LIKE ? OR p.code LIKE ?
         ORDER BY p.name";

$stmt = mysqli_prepare($conn, $query);
$search_param = "%$search%";
mysqli_stmt_bind_param($stmt, "ss", $search_param, $search_param);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$products = [];
while ($product = mysqli_fetch_assoc($result)) {
    $products[] = [
        'id' => $product['id'],
        'code' => $product['code'],
        'name' => $product['name'],
        'price' => $product['price'],
        'stock' => $product['stock'],
        'image' => $product['image'],
        'category_name' => $product['category_name']
    ];
}

header('Content-Type: application/json');
echo json_encode($products);
?>
