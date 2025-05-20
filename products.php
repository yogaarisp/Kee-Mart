<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

requireAdmin();

// Handle Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    mysqli_query($conn, "DELETE FROM products WHERE id = $id");
    flashMessage('Product deleted successfully');
    redirect('products.php');
}

// Get categories for form
$categories_query = "SELECT * FROM categories ORDER BY name";
$categories = mysqli_query($conn, $categories_query);

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 10;
$offset = ($page - 1) * $per_page;

// Search condition
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$where = '';
if ($search) {
    $where = " WHERE p.name LIKE '%$search%' OR p.code LIKE '%$search%' OR c.name LIKE '%$search%'";
}

// Get total products count with search condition
$count_query = "SELECT COUNT(*) as count 
               FROM products p
               LEFT JOIN categories c ON p.category_id = c.id
               $where";
$total_result = mysqli_query($conn, $count_query);
$total_products = mysqli_fetch_assoc($total_result)['count'];
$total_pages = ceil($total_products / $per_page);

// Get products for current page
$query = "SELECT p.*, c.name as category_name 
          FROM products p 
          LEFT JOIN categories c ON p.category_id = c.id
          $where
          ORDER BY p.name
          LIMIT $per_page OFFSET $offset";
$products = mysqli_query($conn, $query);

?>

<?php include 'views/common/header.php'; ?>

<div class="card mb-4">
    <div class="content-header">
        <div class="d-flex justify-content-between align-items-center">
            <h1>Products Management</h1>
            <div class="d-flex gap-3 align-items-center">
                <div class="search-box">
                    <input type="text" id="searchInput" class="form-control" placeholder="Search products...">
                    <i class="bi bi-search"></i>
                </div>
                <button class="btn btn-primary" onclick="showAddForm()">
                    <i class="bi bi-plus-lg"></i> Add Product
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let searchTimeout;

document.getElementById('searchInput').addEventListener('input', function(e) {
    clearTimeout(searchTimeout);
    const searchTerm = e.target.value;
    
    // Wait for user to stop typing
    searchTimeout = setTimeout(() => {
        // Update URL with search parameter
        const url = new URL(window.location);
        if (searchTerm) {
            url.searchParams.set('search', searchTerm);
        } else {
            url.searchParams.delete('search');
        }
        url.searchParams.set('page', '1'); // Reset to first page on search
        
        // Load search results
        window.location = url;
    }, 500); // Wait 500ms after last keystroke
});

// Set initial search value from URL
const urlParams = new URLSearchParams(window.location.search);
if (urlParams.has('search')) {
    document.getElementById('searchInput').value = urlParams.get('search');
}
</script>

<!-- Product Form Modal -->
<div class="modal" id="productModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="formTitle">Add New Product</h5>
                <button type="button" class="btn-close" onclick="hideForm()"></button>
            </div>
            <div class="modal-body">
                <form action="product_action.php" method="post" enctype="multipart/form-data" id="productForm">
                    <input type="hidden" name="id" id="productId">
                    <div class="form-group">
                        <label>Product Code</label>
                        <input type="text" name="code" id="code" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Product Name</label>
                        <input type="text" name="name" id="name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Category</label>
                        <select name="category_id" id="category_id" class="form-control" required>
                            <option value="">Select Category</option>
                            <?php while ($category = mysqli_fetch_assoc($categories)): ?>
                                <option value="<?php echo $category['id']; ?>">
                                    <?php echo htmlspecialchars($category['name']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Price</label>
                        <input type="number" name="price" id="price" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Stock</label>
                        <input type="number" name="stock" id="stock" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Product Image</label>
                        <div class="image-input-container">
                            <div class="image-input-buttons">
                                <button type="button" class="btn btn-outline" onclick="document.getElementById('image').click()">
                                    <i class="bi bi-image"></i> Choose File
                                </button>
                                <button type="button" class="btn btn-outline" onclick="startCamera()">
                                    <i class="bi bi-camera"></i> Take Photo
                                </button>
                            </div>
                            <input type="file" name="image" id="image" accept="image/*" style="display: none" capture="environment">
                            <small class="form-text text-muted">Leave empty to keep current image when editing</small>
                        </div>
                        
                        <!-- Camera Preview -->
                        <div id="cameraContainer" style="display: none;" class="camera-container">
                            <video id="cameraPreview" autoplay playsinline></video>
                            <div class="camera-controls">
                                <button type="button" class="btn btn-danger" onclick="stopCamera()">
                                    <i class="bi bi-x-lg"></i> Cancel
                                </button>
                                <button type="button" class="btn btn-primary" onclick="capturePhoto()">
                                    <i class="bi bi-camera"></i> Capture
                                </button>
                            </div>
                        </div>

                        <!-- Image Preview -->
                        <div id="imagePreview" class="image-preview"></div>
                        <div id="currentImage" style="margin-top: 10px;"></div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="hideForm()">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="document.getElementById('productForm').submit()">Save Product</button>
            </div>
        </div>
    </div>
</div>

<!-- Products Grid/Table -->
<div class="products-wrapper">
    <!-- Grid View (Mobile) -->
    <div class="products-grid">
        <?php while ($product = mysqli_fetch_assoc($products)): ?>
            <div class="product-card">
                <div class="product-image">
                    <?php if ($product['image']): ?>
                        <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                    <?php else: ?>
                        <img src="assets/images/products/default.jpg" alt="Default Image">
                    <?php endif; ?>
                </div>
                <div class="product-info">
                    <div class="product-name"><?php echo htmlspecialchars($product['name']); ?></div>
                    <div class="product-category"><?php echo htmlspecialchars($product['category_name']); ?></div>
                    <div class="product-details">
                        <span class="price"><?php echo formatCurrency($product['price']); ?></span>
                        <span class="stock">Stock: <?php echo $product['stock']; ?></span>
                    </div>
                </div>
                <div class="product-actions">
                    <button class="btn btn-sm btn-primary" onclick='editProduct(<?php echo json_encode($product); ?>)'>
                        <i class="bi bi-pencil"></i>
                    </button>
                    <a href="products.php?delete=<?php echo $product['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">
                        <i class="bi bi-trash"></i>
                    </a>
                </div>
            </div>
        <?php endwhile; ?>
        <?php mysqli_data_seek($products, 0); ?>
    </div>

    <!-- Table View (Desktop) -->
    <div class="card">
        <div class="table-container">
            <table class="table products-table">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($product = mysqli_fetch_assoc($products)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($product['code']); ?></td>
                        <td>
                            <?php if ($product['image']): ?>
                                <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="Product Image" style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px; margin-right: 10px;">
                            <?php endif; ?>
                            <?php echo htmlspecialchars($product['name']); ?>
                        </td>
                        <td><?php echo htmlspecialchars($product['category_name']); ?></td>
                        <td><?php echo formatCurrency($product['price']); ?></td>
                        <td><?php echo $product['stock']; ?></td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary" onclick="editProduct(<?php echo htmlspecialchars(json_encode($product)); ?>)" title="Edit Product">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <a href="products.php?delete=<?php echo $product['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure?')" title="Delete Product">
                                <i class="bi bi-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Pagination -->
<?php if ($total_pages > 1): ?>
<div class="pagination-container">
    <div class="pagination">
        <?php if ($page > 1): ?>
            <a href="?page=<?php echo ($page - 1); ?>" class="btn btn-outline">
                <i class="bi bi-chevron-left"></i> Previous
            </a>
        <?php endif; ?>

        <span class="page-info">Page <?php echo $page; ?> of <?php echo $total_pages; ?></span>

        <?php if ($page < $total_pages): ?>
            <a href="?page=<?php echo ($page + 1); ?>" class="btn btn-outline">
                Next <i class="bi bi-chevron-right"></i>
            </a>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>

<script>
function showAddForm() {
    document.getElementById('formTitle').textContent = 'Add New Product';
    document.getElementById('productModal').classList.add('show');
    document.getElementById('productId').value = '';
    document.getElementById('code').value = '';
    document.getElementById('name').value = '';
    document.getElementById('category_id').value = '';
    document.getElementById('price').value = '';
    document.getElementById('stock').value = '';
    document.getElementById('currentImage').innerHTML = '';
    document.getElementById('imagePreview').innerHTML = '';
}

function hideForm() {
    document.getElementById('productModal').classList.remove('show');
    stopCamera();
}

function editProduct(product) {
    document.getElementById('formTitle').textContent = 'Edit Product';
    document.getElementById('productModal').classList.add('show');
    document.getElementById('productId').value = product.id;
    document.getElementById('code').value = product.code;
    document.getElementById('name').value = product.name;
    document.getElementById('category_id').value = product.category_id;
    document.getElementById('price').value = product.price;
    document.getElementById('stock').value = product.stock;

    const currentImageDiv = document.getElementById('currentImage');
    if (product.image) {
        currentImageDiv.innerHTML = `<img src="${product.image}" alt="Current Image" style="max-width: 100px;">`;
    } else {
        currentImageDiv.innerHTML = '';
    }
    document.getElementById('imagePreview').innerHTML = '';
}

// Camera handling
let stream = null;
const cameraPreview = document.getElementById('cameraPreview');
const cameraContainer = document.getElementById('cameraContainer');
const imagePreview = document.getElementById('imagePreview');

async function startCamera() {
    try {
        stream = await navigator.mediaDevices.getUserMedia({ 
            video: { facingMode: 'environment' } 
        });
        cameraPreview.srcObject = stream;
        cameraContainer.style.display = 'block';
    } catch (err) {
        console.error('Error accessing camera:', err);
        alert('Could not access camera. Please ensure you have given permission.');
    }
}

function stopCamera() {
    if (stream) {
        stream.getTracks().forEach(track => track.stop());
        stream = null;
    }
    cameraContainer.style.display = 'none';
}

function capturePhoto() {
    const canvas = document.createElement('canvas');
    canvas.width = cameraPreview.videoWidth;
    canvas.height = cameraPreview.videoHeight;
    canvas.getContext('2d').drawImage(cameraPreview, 0, 0);
    
    // Convert to file
    canvas.toBlob(blob => {
        const file = new File([blob], 'camera_photo.jpg', { type: 'image/jpeg' });
        const dt = new DataTransfer();
        dt.items.add(file);
        document.getElementById('image').files = dt.files;
        
        // Show preview
        imagePreview.innerHTML = `<img src="${URL.createObjectURL(blob)}" alt="Preview">`;
        
        // Stop camera
        stopCamera();
    }, 'image/jpeg');
}

// Handle file input change
document.getElementById('image').addEventListener('change', function(e) {
    if (this.files && this.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            imagePreview.innerHTML = `<img src="${e.target.result}" alt="Preview">`;
        }
        reader.readAsDataURL(this.files[0]);
    }
});
</script>

<?php include 'views/common/footer.php'; ?>
