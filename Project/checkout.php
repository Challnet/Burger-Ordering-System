<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$cart = $_SESSION['cart'] ?? [];

if (empty($cart)) {
    echo "Your cart is empty.";
    exit();
}

// Retrieve the order type from the session
$order_type = $_SESSION['order_type'] ?? 'Dine In';

// Calculate the total amount for the order
$total_amount = 0;
foreach ($cart as $item_id => $item) {
    $total_amount += $item['price'] * $item['quantity'];
}

// Handle the payment if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['payment_method'])) {
    $payment_method = $_POST['payment_method'];

    // Start a database transaction
    $conn->begin_transaction();

    try {
        // Add the order to the 'orders' table including the order_type
        $stmt = $conn->prepare("INSERT INTO orders (user_id, orderTime, total_price, status, order_type) VALUES (?, NOW(), ?, 'pending', ?)");
        $stmt->bind_param('ids', $user_id, $total_amount, $order_type);
        $stmt->execute();
        $order_id = $stmt->insert_id;

        // Add order details to the 'order_details' table for each item in the cart
        foreach ($cart as $item_id => $item) {
            $total_item_amount = $item['price'] * $item['quantity'];

            $stmt = $conn->prepare("INSERT INTO order_details (order_id, stall_menu_id, quantity, total_amount) VALUES (?, ?, ?, ?)");
            $stmt->bind_param('iiid', $order_id, $item_id, $item['quantity'], $total_item_amount);
            $stmt->execute();
        }

        // Add payment record to the 'payments' table
        $stmt = $conn->prepare("INSERT INTO payments (order_id, paymentMethod, paymentStatus, amountPaid) VALUES (?, ?, 'completed', ?)");
        $stmt->bind_param('isd', $order_id, $payment_method, $total_amount);
        $stmt->execute();

        // Commit the transaction
        $conn->commit();

        // Clear the cart after successful payment
        unset($_SESSION['cart']);

        // Redirect to checkout_success.php with the order_id
        header("Location: checkout_success.php?order_id=" . $order_id);
        exit(); // Make sure the script stops here after redirection
    } catch (Exception $e) {
        // If an error occurs, roll back the transaction
        $conn->rollback();
        echo "Error: " . $e->getMessage();
    }
} else {
    // Display the checkout page with the payment method selection
?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Checkout</title>
        <link rel="stylesheet" href="css/style_checkout.css">
    </head>

    <body>
        <header>
            <div class="header-container">
                <h1>Checkout</h1>
            </div>
        </header>

        <section id="checkout">
            <h3>Order Summary</h3>
            <table>
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cart as $item_id => $item): ?>
                        <tr>
                            <td><?php echo $item['name']; ?></td>
                            <td>RM<?php echo number_format($item['price'], 2); ?></td>
                            <td><?php echo $item['quantity']; ?></td>
                            <td>RM<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="cart-total">
                <h3>Total: RM<?php echo number_format($total_amount, 2); ?></h3>
            </div>

            <form action="checkout.php" method="POST">
                <label for="payment_method">Select Payment Method:</label>
                <select name="payment_method" id="payment_method" required>
                    <option value="credit_card">Credit Card</option>
                    <option value="paypal">PayPal</option>
                    <option value="bank_transfer">Bank Transfer</option>
                </select>
                <button type="submit" class="btn">Complete Payment</button>
            </form>
        </section>

        <footer>
            <p>&copy; 2025 QuickGrill. All rights reserved.</p>
        </footer>
    </body>

    </html>
<?php
}
?>