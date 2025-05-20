<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

requireAdmin();

// Handle Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    
    // Check if category has products
    $check = mysqli_query($conn, "SELECT id FROM products WHERE category_id = $id LIMIT 1");
    if (mysqli_num_rows($check) > 0) {
        flashMessage('Cannot delete category that has products', 'danger');
    } else {
        mysqli_query($conn, "DELETE FROM categories WHERE id = $id");
        flashMessage('Category deleted successfully');
    }
    redirect('categories.php');
}

// Handle Add/Edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : null;
    $name = sanitize($_POST['name']);
    
    if ($id) {
        // Update
        $query = "UPDATE categories SET name = '$name' WHERE id = $id";
        if (mysqli_query($conn, $query)) {
            flashMessage('Category updated successfully');
        } else {
            flashMessage('Error updating category', 'danger');
        }
    } else {
        // Insert
        $query = "INSERT INTO categories (name) VALUES ('$name')";
        if (mysqli_query($conn, $query)) {
            flashMessage('Category added successfully');
        } else {
            flashMessage('Error adding category', 'danger');
        }
    }
    redirect('categories.php');
}

// Get all categories with product count
$query = "SELECT c.*, COUNT(p.id) as product_count 
          FROM categories c 
          LEFT JOIN products p ON c.id = p.category_id 
          GROUP BY c.id 
          ORDER BY c.name";
$categories = mysqli_query($conn, $query);
?>

<?php include 'views/common/header.php'; ?>

<div class="content-header">
    <h1>Categories Management</h1>
    <button class="btn btn-primary" onclick="showAddForm()">
        <i class="bi bi-plus-lg"></i> Add New Category
    </button>
</div>

<!-- Category Form Modal -->
<div class="modal" id="categoryModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="formTitle">Add New Category</h5>
                <button type="button" class="btn-close" onclick="hideForm()"></button>
            </div>
            <div class="modal-body">
                <form action="categories.php" method="post" id="categoryForm">
                    <input type="hidden" name="id" id="categoryId">
                    <div class="form-group">
                        <label>Category Name</label>
                        <input type="text" name="name" id="categoryName" class="form-control" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="hideForm()">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="document.getElementById('categoryForm').submit()">Save Category</button>
            </div>
        </div>
    </div>
</div>

<!-- Categories Table -->
<div class="card">
    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Products</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($category = mysqli_fetch_assoc($categories)): ?>
                <tr>
                    <td><?php echo htmlspecialchars($category['name']); ?></td>
                    <td><?php echo $category['product_count']; ?></td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary" onclick='editCategory(<?php echo json_encode($category); ?>)' title="Edit Category">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <?php if ($category['product_count'] == 0): ?>
                        <a href="categories.php?delete=<?php echo $category['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure?')" title="Delete Category">
                            <i class="bi bi-trash"></i>
                        </a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function showAddForm() {
    document.getElementById('formTitle').textContent = 'Add New Category';
    document.getElementById('categoryModal').classList.add('show');
    document.getElementById('categoryId').value = '';
    document.getElementById('categoryName').value = '';
}

function hideForm() {
    document.getElementById('categoryModal').classList.remove('show');
}

function editCategory(category) {
    document.getElementById('formTitle').textContent = 'Edit Category';
    document.getElementById('categoryModal').classList.add('show');
    document.getElementById('categoryId').value = category.id;
    document.getElementById('categoryName').value = category.name;
}
</script>

<?php include 'views/common/footer.php'; ?>
