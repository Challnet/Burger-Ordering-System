<?php
session_start();

include 'db.php';

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$stall_id = isset($_GET['stall_id']) ? $_GET['stall_id'] : 0;

$stall_sql = "SELECT * FROM stalls WHERE stall_id = " . $conn->real_escape_string($stall_id);
$stall_result = $conn->query($stall_sql);
$stall = $stall_result->fetch_assoc();

// Query to get the menu items for this stall
$menu_sql = "SELECT * FROM stall_menus WHERE stall_id = " . $conn->real_escape_string($stall_id);
$menu_result = $conn->query($menu_sql);

// Handle adding items to the cart
if (isset($_POST['add_to_cart'])) {
    $menu_item_id = $_POST['menu_item_id'];
    $quantity = isset($_POST['quantity']) ? $_POST['quantity'] : 1;

    // Add the item to the cart
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = array();
    }

    // If the item is already in the cart, increase the quantity
    if (isset($_SESSION['cart'][$menu_item_id])) {
        $_SESSION['cart'][$menu_item_id]['quantity'] += $quantity;
    } else {
        // Get the item details from the database
        $menu_item_sql = "SELECT * FROM stall_menus WHERE id = " . $conn->real_escape_string($menu_item_id);
        $menu_item_result = $conn->query($menu_item_sql);
        $menu_item = $menu_item_result->fetch_assoc();

        $_SESSION['cart'][$menu_item_id] = array(
            'name' => $menu_item['name'],
            'price' => $menu_item['price'],
            'quantity' => $quantity
        );
    }

    // Set the notification after every add to cart action
    if (isset($menu_item)) {
        $_SESSION['notification'] = "Added {$quantity} x {$menu_item['name']} to your cart.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu of <?php echo $stall['name']; ?></title>
    <link rel="stylesheet" href="css/style_menu.css">
    <style>
        /* Notification styles */
        .notification {
            position: fixed;
            top: 20px;
            left: 20px;
            /* Updated position */
            background-color: #28a745;
            color: white;
            padding: 15px;
            border-radius: 5px;
            font-size: 1.2em;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            opacity: 0;
            transition: opacity 0.5s ease;
        }

        .notification.show {
            opacity: 1;
        }
    </style>
</head>

<body>

    <header>
        <div class="header-container">
            <h1><?php echo $stall['name']; ?> - Menu</h1>
            <p><?php echo $stall['description']; ?></p>
        </div>
    </header>

    <!-- Fixed navigation buttons in the top right corner -->
    <div class="top-buttons">
        <a href="cart.php" class="btn go-to-cart-btn">Go to Cart</a>
    </div>

    <div class="back-button-container">
        <a href="stalls.php" class="back-button">&lt; Back to Stalls</a>
    </div>

    <section id="menu-items">
        <?php while ($menu_item = $menu_result->fetch_assoc()) { ?>
            <div class="menu-item">
                <img src="<?php echo $menu_item['image_url']; ?>" alt="Menu Item" class="menu-image">
                <h3 class="menu-item-name"><?php echo $menu_item['name']; ?></h3>
                <p class="menu-item-description"><?php echo $menu_item['description']; ?></p>
                <p class="menu-item-price">RM<?php echo number_format($menu_item['price'], 2); ?></p>

                <!-- Add to cart form -->
                <form action="" method="POST">
                    <input type="hidden" name="menu_item_id" value="<?php echo $menu_item['id']; ?>">
                    <div class="quantity-container">
                        <button type="button" class="quantity-btn" id="decrease-<?php echo $menu_item['id']; ?>">-</button>
                        <input type="number" name="quantity" value="1" min="1" max="10" id="quantity-<?php echo $menu_item['id']; ?>">
                        <button type="button" class="quantity-btn" id="increase-<?php echo $menu_item['id']; ?>">+</button>
                    </div>
                    <button type="submit" name="add_to_cart" class="btn">Add to Cart</button>
                </form>
            </div>

            <script>
                // Increase quantity
                document.getElementById('increase-<?php echo $menu_item['id']; ?>').addEventListener('click', function() {
                    var quantityInput = document.getElementById('quantity-<?php echo $menu_item['id']; ?>');
                    var currentValue = parseInt(quantityInput.value);
                    if (currentValue < 10) {
                        quantityInput.value = currentValue + 1;
                    }
                });

                // Decrease quantity
                document.getElementById('decrease-<?php echo $menu_item['id']; ?>').addEventListener('click', function() {
                    var quantityInput = document.getElementById('quantity-<?php echo $menu_item['id']; ?>');
                    var currentValue = parseInt(quantityInput.value);
                    if (currentValue > 1) {
                        quantityInput.value = currentValue - 1;
                    }
                });
            </script>
        <?php } ?>
    </section>

    <!-- Notification -->
    <?php if (isset($_SESSION['notification'])) { ?>
        <div class="notification show" id="notification">
            <?php echo $_SESSION['notification']; ?>
        </div>
        <?php unset($_SESSION['notification']); ?>
    <?php } ?>

    <footer>
        <p>&copy; 2025 QuickGrill. All rights reserved.</p>
    </footer>

    <script>
        // Hide the notification after 3 seconds
        setTimeout(function() {
            document.getElementById('notification').classList.remove('show');
        }, 3000);
    </script>

</body>

</html>

<?php
$conn->close();
?>