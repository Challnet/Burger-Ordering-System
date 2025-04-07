<?php
session_start();
include 'db.php';

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Handle order type selection
if (isset($_POST['order_type'])) {
    $_SESSION['order_type'] = htmlspecialchars($_POST['order_type']);
}

// Remove item from cart
if (isset($_GET['remove_item_id'])) {
    $remove_item_id = intval($_GET['remove_item_id']);
    if (isset($_SESSION['cart'][$remove_item_id])) {
        unset($_SESSION['cart'][$remove_item_id]);
    }
}

// Clear cart
if (isset($_GET['clear_cart'])) {
    unset($_SESSION['cart']);
}

// Update item quantity
if (isset($_POST['update_quantity'])) {
    $menu_item_id = intval($_POST['menu_item_id']);
    $quantity = intval($_POST['quantity']);

    // Ensure quantity is valid
    if ($quantity > 0) {
        $_SESSION['cart'][$menu_item_id]['quantity'] = $quantity;
    } else {
        unset($_SESSION['cart'][$menu_item_id]);
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart</title>
    <link rel="stylesheet" href="css/style_cart.css">
    <script>
        // Automatically submit the form when the order type is changed
        function updateOrderType() {
            document.getElementById('order_type_form').submit();
        }
    </script>
</head>

<body>

    <header>
        <div class="header-container">
            <h1>Your Cart</h1>
        </div>
    </header>

    <!-- Back to Stalls Button -->
    <a href="stalls.php" class="btn back-to-stalls">Back to Stalls</a>

    <section id="cart-items">
        <?php if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])): ?>
            <table>
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Total</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $total = 0;
                    foreach ($_SESSION['cart'] as $item_id => $item) {
                        $item_total = $item['price'] * $item['quantity'];
                        $total += $item_total;
                    ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['name']); ?></td>
                            <td>RM<?php echo number_format($item['price'], 2); ?></td>
                            <td>
                                <form action="cart.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="menu_item_id" value="<?php echo $item_id; ?>">
                                    <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" min="1">
                                    <button type="submit" name="update_quantity" class="btn update-btn">Update</button>
                                </form>
                            </td>
                            <td>RM<?php echo number_format($item_total, 2); ?></td>
                            <td>
                                <a href="cart.php?remove_item_id=<?php echo $item_id; ?>" class="btn remove-btn">Remove</a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>

            <!-- Order type radio button group below the table -->
            <form id="order_type_form" action="cart.php" method="POST" class="order-type-form">
                <div class="order-type-display">
                    <h3>Select Order Type:</h3>
                    <label>
                        <input type="radio" name="order_type" value="Dine In"
                            <?php echo (isset($_SESSION['order_type']) && $_SESSION['order_type'] == 'Dine In') ? 'checked' : ''; ?>
                            onchange="updateOrderType()">
                        Dine In
                    </label>
                    <label>
                        <input type="radio" name="order_type" value="Take Away"
                            <?php echo (isset($_SESSION['order_type']) && $_SESSION['order_type'] == 'Take Away') ? 'checked' : ''; ?>
                            onchange="updateOrderType()">
                        Take Away
                    </label>
                </div>
            </form>

            <div class="cart-total">
                <h3>Total: RM<?php echo number_format($total, 2); ?></h3>
            </div>

            <div class="cart-actions">
                <a href="cart.php?clear_cart=true" class="btn">Clear Cart</a>
                <a href="checkout.php" class="btn">Proceed to Checkout</a>
            </div>

        <?php else: ?>
            <p>Your cart is empty.</p>
        <?php endif; ?>
    </section>

    <footer>
        <p>&copy; 2025 QuickGrill. All rights reserved.</p>
    </footer>
</body>

</html>

<?php
$conn->close();
?>