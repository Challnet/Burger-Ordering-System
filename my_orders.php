<?php
include 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch orders for the logged-in user, along with stall name and order_type
$sql = "SELECT o.id AS order_id, o.orderTime, o.status, o.order_type, u.name AS buyer_name, m.name AS item_name, od.quantity, od.total_amount, s.name AS stall_name
        FROM orders o
        JOIN order_details od ON o.id = od.order_id
        JOIN stall_menus m ON od.stall_menu_id = m.id
        JOIN users u ON o.user_id = u.user_id
        JOIN stalls s ON m.stall_id = s.stall_id
        WHERE o.user_id = ? ORDER BY o.orderTime DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$orders = [];
while ($row = $result->fetch_assoc()) {
    $order_id = $row['order_id'];
    if (!isset($orders[$order_id])) {
        $orders[$order_id] = [
            'order_id' => $order_id,
            'orderTime' => $row['orderTime'],
            'status' => strtolower($row['status']),
            'buyer_name' => $row['buyer_name'],
            'stall_name' => $row['stall_name'],
            'order_type' => $row['order_type'],
            'items' => []
        ];
    }
    $orders[$order_id]['items'][] = [
        'name' => $row['item_name'],
        'quantity' => $row['quantity'],
        'total_amount' => $row['total_amount']
    ];
}
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Orders</title>
    <link rel="stylesheet" href="css/style_my_orders.css">
</head>

<body>
    <header>
        <h1>Your Orders</h1>
    </header>

    <div class="go-to-stalls">
        <a href="stalls.php" class="btn-go-to-stalls">Go to Stalls</a>
    </div>

    <div class="orders-container">
        <?php if (empty($orders)): ?>
            <p>You have no orders yet.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Stall</th>
                        <th>Items</th>
                        <th>Order Time</th>
                        <th>Status</th>
                        <th>Order Type</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr class="status-<?php echo htmlspecialchars($order['status']); ?>">
                            <td><?php echo $order['order_id']; ?></td>
                            <td class="stall-name"><?php echo htmlspecialchars($order['stall_name']); ?></td>
                            <td>
                                <ul>
                                    <?php foreach ($order['items'] as $item): ?>
                                        <li><?php echo htmlspecialchars($item['name']) . " - " . $item['quantity'] . " pcs (RM" . number_format($item['total_amount'], 2) . ")"; ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </td>
                            <td><?php echo $order['orderTime']; ?></td>
                            <td class="status"><?php echo ucfirst($order['status']); ?></td>
                            <td><?php echo htmlspecialchars($order['order_type']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>

</html>