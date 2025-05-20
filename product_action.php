<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

requireAdmin();

function handleImageUpload($file) {
    $target_dir = "assets/images/products/";
    $file_extension = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
    $new_filename = uniqid() . '.' . $file_extension;
    $target_file = $target_dir . $new_filename;
    
    // Check if image file is actual image or fake image
    $check = getimagesize($file["tmp_name"]);
    if ($check === false) {
        throw new Exception("File is not an image.");
    }
    
    // Check file size (max 5MB)
    if ($file["size"] > 5000000) {
        throw new Exception("File is too large. Maximum size is 5MB.");
    }
    
    // Allow certain file formats
    if (!in_array($file_extension, ["jpg", "jpeg", "png", "gif"])) {
        throw new Exception("Only JPG, JPEG, PNG & GIF files are allowed.");
    }
    
    // Upload file
    if (!move_uploaded_file($file["tmp_name"], $target_file)) {
        throw new Exception("Error uploading file.");
    }
    
    return $target_file;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $image_path = null;
    
    // Handle image upload if file is selected
    if (!empty($_FILES["image"]["name"])) {
        try {
            $image_path = handleImageUpload($_FILES["image"]);
        } catch (Exception $e) {
            flashMessage($e->getMessage(), 'danger');
            redirect('products.php');
        }
    }
    $id = isset($_POST['id']) ? (int)$_POST['id'] : null;
    $code = sanitize($_POST['code']);
    $name = sanitize($_POST['name']);
    $category_id = (int)$_POST['category_id'];
    $price = (float)$_POST['price'];
    $stock = (int)$_POST['stock'];
    
    // Check if code exists (for new products)
    if (!$id) {
        $check = mysqli_query($conn, "SELECT id FROM products WHERE code = '$code'");
        if (mysqli_num_rows($check) > 0) {
            flashMessage('Product code already exists', 'danger');
            redirect('products.php');
        }
    }
    
    if ($id) {
        // Update
        $query = "UPDATE products SET 
                  code = '$code',
                  name = '$name',
                  category_id = $category_id,
                  price = $price,
                  stock = $stock";
        
        // Add image to update if new image was uploaded
        if ($image_path) {
            // Delete old image if exists
            $old_image_query = "SELECT image FROM products WHERE id = $id";
            $old_image_result = mysqli_query($conn, $old_image_query);
            $old_image = mysqli_fetch_assoc($old_image_result)['image'];
            if ($old_image && file_exists($old_image)) {
                unlink($old_image);
            }
            
            $query .= ", image = '$image_path'";
        }
        
        $query .= " WHERE id = $id";
        
        if (mysqli_query($conn, $query)) {
            flashMessage('Product updated successfully');
        } else {
            flashMessage('Error updating product', 'danger');
        }
    } else {
        // Insert
        $query = "INSERT INTO products (code, name, category_id, price, stock, image) 
                  VALUES ('$code', '$name', $category_id, $price, $stock, " . 
                  ($image_path ? "'$image_path'" : "NULL") . ")";
        
        if (mysqli_query($conn, $query)) {
            flashMessage('Product added successfully');
        } else {
            flashMessage('Error adding product', 'danger');
        }
    }
}

redirect('products.php');
