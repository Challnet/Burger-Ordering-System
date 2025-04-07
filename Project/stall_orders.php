<?php
include 'db.php';

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

$is_admin = ($_SESSION['role'] === 'admin');

if (!$is_admin) {
    $sql = "SELECT stall_id FROM stalls WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($stall_id);
    $stmt->fetch();
    $stmt->close();
} else {
    if (isset($_GET['stall_id'])) {
        $stall_id = $_GET['stall_id'];
    } else {
        echo "Error: Stall ID is required for admins.";
        exit;
    }
}

// Fetch orders for the given stall_id
$sql = "SELECT o.id AS order_id, o.orderTime, o.status, o.order_type, u.name AS buyer_name, m.name AS item_name, od.quantity, od.total_amount 
        FROM orders o
        JOIN order_details od ON o.id = od.order_id
        JOIN stall_menus m ON od.stall_menu_id = m.id
        JOIN stalls s ON m.stall_id = s.stall_id
        JOIN users u ON o.user_id = u.user_id 
        WHERE s.stall_id = ?
        ORDER BY o.id";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $stall_id);
$stmt->execute();
$result = $stmt->get_result();

$orders = [];
while ($row = $result->fetch_assoc()) {
    $order_id = $row['order_id'];
    if (!isset($orders[$order_id])) {
        $orders[$order_id] = [
            'order_id' => $order_id,
            'orderTime' => $row['orderTime'],
            'status' => $row['status'],
            'order_type' => $row['order_type'],
            'buyer_name' => $row['buyer_name'],
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
    <title>Your Stall Orders</title>
    <link rel="stylesheet" href="css/style_stall_orders.css">
</head>

<body>
    <div class="orders-container">
        <?php if (empty($orders)): ?>
            <p>No orders yet.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Items</th>
                        <th>Order Time</th>
                        <th>Order Type</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <?php
                        $statusClass = strtolower($order['status']);
                        ?>
                        <tr class="<?php echo $statusClass; ?>">
                            <td><?php echo $order['order_id']; ?></td>
                            <td><?php echo $order['buyer_name']; ?></td>
                            <td>
                                <ul>
                                    <?php foreach ($order['items'] as $item): ?>
                                        <li><?php echo $item['name'] . " - " . $item['quantity'] . " pcs (RM" . number_format($item['total_amount'], 2) . ")"; ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </td>
                            <td><?php echo $order['orderTime']; ?></td>
                            <td><?php echo htmlspecialchars($order['order_type']); ?></td>
                            <td>
                                <select class="status-select" data-order-id="<?php echo $order['order_id']; ?>">
                                    <option value="Pending" <?php echo ($order['status'] == 'Pending') ? 'selected' : ''; ?>>Pending</option>
                                    <option value="Processing" <?php echo ($order['status'] == 'Processing') ? 'selected' : ''; ?>>Processing</option>
                                    <option value="Completed" <?php echo ($order['status'] == 'Completed') ? 'selected' : ''; ?>>Completed</option>
                                    <option value="Cancelled" <?php echo ($order['status'] == 'Cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                                </select>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <script>
        document.querySelectorAll(".status-select").forEach(select => {
            select.addEventListener("change", function() {
                let orderId = this.getAttribute("data-order-id");
                let newStatus = this.value;
                let row = this.closest("tr");

                row.classList.remove('pending', 'processing', 'completed', 'cancelled');

                switch (newStatus) {
                    case 'Pending':
                        row.classList.add('pending');
                        break;
                    case 'Processing':
                        row.classList.add('processing');
                        break;
                    case 'Completed':
                        row.classList.add('completed');
                        break;
                    case 'Cancelled':
                        row.classList.add('cancelled');
                        break;
                }

                fetch("update_order_status.php", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/x-www-form-urlencoded"
                        },
                        body: `order_id=${orderId}&status=${newStatus}`
                    })
                    .then(response => response.text())
                    .then(data => {
                        if (data === "Success") {
                            alert("Status updated successfully!");
                        } else {
                            alert("Error updating status.");
                        }
                    })
                    .catch(error => {
                        console.error("Error:", error);
                        alert("An error occurred while updating the status.");
                    });
            });
        });
    </script>

</body>

</html>