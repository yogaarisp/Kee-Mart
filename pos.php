<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

requireLogin();

// Handle AJAX requests for product info
if (isset($_GET['get_product'])) {
    $code = sanitize($_GET['get_product']);
    $query = "SELECT * FROM products WHERE code = '$code'";
    $result = mysqli_query($conn, $query);
    
    if ($product = mysqli_fetch_assoc($result)) {
        echo json_encode([
            'success' => true,
            'product' => $product
        ]);
    } else {
        echo json_encode(['success' => false]);
    }
    exit;
}

// Handle transaction submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $items = json_decode($_POST['items'], true);
    $total_amount = $_POST['total_amount'];
    $payment_amount = $_POST['payment_amount'];
    $change_amount = $payment_amount - $total_amount;
    
    if ($change_amount < 0) {
        flashMessage('Insufficient payment amount', 'danger');
        redirect('pos.php');
    }
    
    mysqli_begin_transaction($conn);
    try {
        // Create transaction record
        $invoice_number = generateInvoiceNumber();
        $cashier_id = $_SESSION['user_id'];
        
        $query = "INSERT INTO transactions (invoice_number, cashier_id, total_amount, payment_amount, change_amount) 
                  VALUES ('$invoice_number', $cashier_id, $total_amount, $payment_amount, $change_amount)";
        mysqli_query($conn, $query);
        $transaction_id = mysqli_insert_id($conn);
        
        // Insert transaction items and update stock
        foreach ($items as $item) {
            $product_id = $item['id'];
            $quantity = $item['quantity'];
            $price = $item['price'];
            $subtotal = $quantity * $price;
            
            $query = "INSERT INTO transaction_items (transaction_id, product_id, quantity, price, subtotal) 
                      VALUES ($transaction_id, $product_id, $quantity, $price, $subtotal)";
            mysqli_query($conn, $query);
            
            // Update stock
            $query = "UPDATE products SET stock = stock - $quantity WHERE id = $product_id";
            mysqli_query($conn, $query);
        }
        
        mysqli_commit($conn);
        
        // Redirect to print receipt
        header("Location: print_receipt.php?id=$transaction_id");
        exit;
        
    } catch (Exception $e) {
        mysqli_rollback($conn);
        flashMessage('Error processing transaction: ' . $e->getMessage(), 'danger');
        redirect('pos.php');
    }
}
?>

<?php include 'views/common/header.php'; ?>

<?php
// Pagination
$items_per_page = 12;
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($current_page - 1) * $items_per_page;

// Get total products
$total_query = "SELECT COUNT(*) as total FROM products";
$total_result = mysqli_query($conn, $total_query);
$total_products = mysqli_fetch_assoc($total_result)['total'];
$total_pages = ceil($total_products / $items_per_page);

// Get products for current page
$query = "SELECT p.*, c.name as category_name 
         FROM products p 
         LEFT JOIN categories c ON p.category_id = c.id
         ORDER BY p.name
         LIMIT $items_per_page OFFSET $offset";
$result = mysqli_query($conn, $query);
?>

<div style="display: flex; gap: 20px;">
    <!-- Product Scanner/Search -->
    <div class="card" style="flex: 2;">
        <h2>Point of Sale</h2>
        <div class="search-container">
            <div class="form-group">
                <i class="bi bi-search search-icon"></i>
                <input type="text" id="product-search" class="form-control" placeholder="Search by name or code..." autofocus>
                <div class="spinner-border search-spinner" role="status" style="display: none;">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        </div>
        
        <div class="pos-container">
            <div class="pos-grid">
                <?php while ($product = mysqli_fetch_assoc($result)): ?>
                    <div class="product-card" onclick="addToCart(<?php echo htmlspecialchars(json_encode($product)); ?>)">
                        <div class="product-image">
                            <img src="<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>">
                        </div>
                        <div class="product-info">
                            <h3><?php echo $product['name']; ?></h3>
                            <div class="product-details">
                                <span class="price">Rp <?php echo number_format($product['price'], 0, ',', '.'); ?></span>
                                <span class="stock">Stock: <?php echo $product['stock']; ?></span>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>

            <div class="pagination">
                <?php if ($total_pages > 1): ?>
                    <?php if ($current_page > 1): ?>
                        <a href="?page=<?php echo $current_page - 1; ?>" class="btn">
                            <i class="bi bi-chevron-left"></i> Previous
                        </a>
                    <?php endif; ?>

                    <span class="page-info">Page <?php echo $current_page; ?> of <?php echo $total_pages; ?></span>

                    <?php if ($current_page < $total_pages): ?>
                        <a href="?page=<?php echo $current_page + 1; ?>" class="btn">
                            Next <i class="bi bi-chevron-right"></i>
                        </a>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
        
    </div>
    
    <!-- Mobile Cart Button -->
    <div class="mobile-cart-button" id="mobile-cart-btn">
        <i class="bi bi-cart3"></i>
        <span class="cart-count">0</span>
    </div>

    <!-- Payment Section -->
    <div class="payment-section" id="payment-section">
        <div class="payment-header">
            <h3><i class="bi bi-cart3"></i> Shopping Cart</h3>
            <button type="button" class="btn-close" onclick="togglePaymentSection()"><i class="bi bi-x-lg"></i></button>
        </div>

        <div class="cart-items" id="cart-items">
            <div class="cart-list" id="cart-table">
            </div>
        </div>

        <div class="payment-summary">
            <div class="summary-item">
                <span>Subtotal</span>
                <strong id="total-amount">Rp 0</strong>
            </div>
          
            <div class="summary-item total">
                <span>Total</span>
                <strong id="final-amount">Rp 0</strong>
            </div>
        </div>

        <form id="payment-form" method="POST">
            <input type="hidden" name="items" id="items-json">
            <input type="hidden" name="total_amount" id="total-amount-input">
            
            <div class="payment-input">
                <div class="form-group">
                    <label><i class="bi bi-wallet2"></i> Payment Amount</label>
                    <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="number" name="payment_amount" id="payment-amount" class="form-control" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label><i class="bi bi-cash-coin"></i> Change</label>
                    <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="text" id="change-amount" class="form-control" readonly>
                    </div>
                </div>
            </div>

            <div class="payment-actions">
                <button type="button" class="btn btn-outline" onclick="togglePaymentSection()">Continue Shopping</button>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check2-circle"></i> Process Payment
                </button>
            </div>
        </form>
    </div>
</div>

<script>
let cart = [];
let total = 0;

document.getElementById('product-code').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        const code = this.value;
        fetch(`pos.php?get_product=${code}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    addToCart(data.product);
                    this.value = '';
                } else {
                    alert('Product not found!');
                }
            });
    }
});

function addToCart(product) {
    const existingItem = cart.find(item => item.id === product.id);
    
    if (existingItem) {
        existingItem.quantity += 1;
    } else {
        cart.push({...product, quantity: 1});
    }
    
    updateCart();
}

function removeFromCart(index) {
    cart.splice(index, 1);
    updateCart();
}

function updateQuantity(index, delta) {
    cart[index].quantity = Math.max(1, cart[index].quantity + delta);
    updateCart();
}

function updateCart() {
    const cartList = document.getElementById('cart-table');
    cartList.innerHTML = '';
    
    total = 0;
    
    if (cart.length === 0) {
        cartList.innerHTML = `
            <div class="empty-cart">
                <i class="bi bi-cart-x"></i>
                <p>Your cart is empty</p>
                <small>Add some items to your cart</small>
            </div>
        `;
        updateTotal();
        return;
    }

    cart.forEach((item, index) => {
        const subtotal = item.price * item.quantity;
        total += subtotal;
        
        document.getElementById('cart-table').innerHTML += `
            <div class="cart-item">
                <div class="item-image">
                    <img src="${item.image || 'assets/images/products/default.jpg'}" alt="${item.name}">
                </div>
                <div class="item-details">
                    <div class="item-name">${item.name}</div>
                    <div class="item-price">Rp ${numberFormat(item.price)}</div>
                </div>
                <div class="item-controls">
                    <div class="quantity-control">
                        <button onclick="updateQuantity(${index}, -1)" class="btn-qty"><i class="bi bi-dash"></i></button>
                        <span>${item.quantity}</span>
                        <button onclick="updateQuantity(${index}, 1)" class="btn-qty"><i class="bi bi-plus"></i></button>
                    </div>
                    <button onclick="removeFromCart(${index})" class="btn-remove"><i class="bi bi-trash"></i></button>
                </div>
            </div>
        `;
    });
    
    updateTotal();
    document.getElementById('items-json').value = JSON.stringify(cart);
    
    // Update change amount if payment amount is set
    calculateChange();
}

function updateTotal() {
    const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    total = subtotal; // Update global total

    document.getElementById('total-amount').textContent = `Rp ${numberFormat(subtotal)}`;
    document.getElementById('final-amount').textContent = `Rp ${numberFormat(subtotal)}`;
    document.getElementById('total-amount-input').value = subtotal;

    // Update cart count
    updateCartCount();
}

function calculateChange() {
    const paymentAmount = parseFloat(document.getElementById('payment-amount').value) || 0;
    const finalTotal = parseFloat(document.getElementById('total-amount-input').value) || 0;
    const change = paymentAmount - finalTotal;
    document.getElementById('change-amount').value = `Rp ${numberFormat(Math.max(0, change))}`;
}

function formatCurrency(amount) {
    return 'Rp ' + amount.toFixed(0).replace(/\d(?=(\d{3})+$)/g, '$&,');
}

function numberFormat(number) {
    return new Intl.NumberFormat('id-ID').format(number);
}

document.getElementById('payment-amount').addEventListener('input', calculateChange);
document.getElementById('payment-amount').addEventListener('input', updateChange);

document.getElementById('payment-form').addEventListener('submit', function(e) {
    if (cart.length === 0) {
        e.preventDefault();
        alert('Cart is empty!');
        return;
    }
    
    const paymentAmount = parseFloat(document.getElementById('payment-amount').value);
    if (paymentAmount < total) {
        e.preventDefault();
        alert('Insufficient payment amount!');
        return;
    }
});
</script>

<script>
let searchTimeout;
const searchSpinner = document.querySelector('.search-spinner');
const productGrid = document.querySelector('.pos-grid');

document.getElementById('product-search').addEventListener('input', function(e) {
    const searchQuery = e.target.value.trim();
    
    // Clear previous timeout
    if (searchTimeout) {
        clearTimeout(searchTimeout);
    }
    
    // Show spinner
    searchSpinner.style.display = 'inline-block';
    
    // Set new timeout
    searchTimeout = setTimeout(() => {
        fetch(`search_products.php?search=${encodeURIComponent(searchQuery)}`)
            .then(response => response.json())
            .then(products => {
                productGrid.innerHTML = products.map(product => `
                    <div class="product-card" onclick='addToCart(${JSON.stringify(product)})'>
                        <div class="product-image">
                            <img src="${product.image}" alt="${product.name}">
                        </div>
                        <div class="product-info">
                            <h3>${product.name}</h3>
                            <div class="product-details">
                                <span class="price">Rp ${numberFormat(product.price)}</span>
                                <span class="stock">Stock: ${product.stock}</span>
                            </div>
                        </div>
                    </div>
                `).join('');
                
                // Hide spinner
                searchSpinner.style.display = 'none';
            })
            .catch(error => {
                console.error('Error:', error);
                searchSpinner.style.display = 'none';
            });
    }, 300); // Delay for 300ms
});

function numberFormat(number) {
    return new Intl.NumberFormat('id-ID').format(number);
}
</script>

<div class="overlay" id="overlay"></div>

<script>
const mobileCartBtn = document.getElementById('mobile-cart-btn');
const paymentSection = document.getElementById('payment-section');
const overlay = document.getElementById('overlay');
let cartItemCount = 0;

// Update cart count
function updateCartCount() {
    const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
    document.querySelector('.cart-count').textContent = totalItems;
}

// Toggle payment section
function togglePaymentSection() {
    paymentSection.classList.toggle('show');
    overlay.classList.toggle('show');
    document.body.classList.toggle('no-scroll');
}

// Event listeners
mobileCartBtn.addEventListener('click', togglePaymentSection);

overlay.addEventListener('click', togglePaymentSection);

// Update cart count when items change
const cartObserver = new MutationObserver(updateCartCount);
const cartTableBody = document.querySelector('#cart-table tbody');
cartObserver.observe(cartTableBody, { childList: true });

// Handle escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && paymentSection.classList.contains('show')) {
        togglePaymentSection();
    }
});

// Update original addToCart function
const originalAddToCart = window.addToCart;
window.addToCart = function(product) {
    originalAddToCart(product);
    updateCartCount();
};
</script>

<?php include 'views/common/footer.php'; ?>

