<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Success</title>
    <link rel="stylesheet" href="css/style_checkout_success.css">

</head>

<body>
    <header>
        <div class="header-container">
            <h1>Order Successfully Placed!</h1>
        </div>
    </header>

    <section id="checkout-success">
        <h3>Thank you for your purchase!</h3>
        <p>Your order has been successfully processed. You can now choose your next step.</p>

        <div class="action-buttons">
            <a href="stalls.php" class="btn">Go To Stalls</a>
            <a href="my_orders.php" class="btn">To My Orders</a>
        </div>
    </section>

    <footer>
        <p>&copy; 2025 QuickGrill. All rights reserved.</p>
    </footer>
</body>

</html>