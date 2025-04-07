<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

// Update stall approval status
if (isset($_POST['action'], $_POST['stall_id'])) {
    $stall_id = intval($_POST['stall_id']);
    $action = $_POST['action'];

    if ($action === 'approve') {
        $is_approved = 1;
        $query_update = "UPDATE stalls SET is_approved = ? WHERE stall_id = ?";
        $stmt = $conn->prepare($query_update);
        $stmt->bind_param("ii", $is_approved, $stall_id);

        if ($stmt->execute()) {
            $message = "Stall has been approved.";
            $message_type = "success";

            $query_owner = "SELECT user_id FROM stalls WHERE stall_id = ?";
            $stmt_owner = $conn->prepare($query_owner);
            $stmt_owner->bind_param("i", $stall_id);
            $stmt_owner->execute();
            $stmt_owner->bind_result($owner_id);
            $stmt_owner->fetch();
            $stmt_owner->close();

            // Update the user's role to 'vendor'
            if ($owner_id) {
                $query_role_update = "UPDATE users SET role = 'vendor' WHERE user_id = ?";
                $stmt_role_update = $conn->prepare($query_role_update);
                $stmt_role_update->bind_param("i", $owner_id);
                $stmt_role_update->execute();
                $stmt_role_update->close();
            }
        } else {
            $message = "Error: " . $stmt->error;
            $message_type = "error";
        }

        $stmt->close();
    } elseif ($action === 'reject') {
        // Reject the stall by deleting it from the database
        $query_delete = "DELETE FROM stalls WHERE stall_id = ?";
        $stmt = $conn->prepare($query_delete);
        $stmt->bind_param("i", $stall_id);

        if ($stmt->execute()) {
            $message = "Stall has been rejected and removed.";
            $message_type = "error";
        } else {
            $message = "Error: " . $stmt->error;
            $message_type = "error";
        }

        $stmt->close();
    }
}

// Fetch all stall requests
$query = "
    SELECT stall_id, name, description, image_url, permit_number, permit_document, additional_document, contact_phone, contact_email, is_approved
    FROM stalls
    ORDER BY is_approved, stall_id DESC
";
$result = $conn->query($query);

if ($result === false) {
    die("SQL Error: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stall Requests | QuickGrill</title>
    <link rel="stylesheet" href="css/style_stall_requests.css">
</head>

<body>

    <!-- Notification for success/failure -->
    <?php if (isset($message)): ?>
        <div class="notification <?= $message_type ?>" id="notification"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <header>
        <div class="header-container">
            <h1>Stall Requests Management</h1>
        </div>
    </header>
    <a href="stalls.php" class="btn back-to-home">Back to Stalls</a>

    <section class="stall-requests">
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Image</th>
                    <th>Permit Number</th>
                    <th>Permit Document</th>
                    <th>Additional Document</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Request</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['name']) ?></td>
                            <td><?= htmlspecialchars($row['description']) ?></td>
                            <td>
                                <img src="<?= htmlspecialchars($row['image_url']) ?>" alt="<?= htmlspecialchars($row['name']) ?>" class="stall-image">
                            </td>
                            <td><?= htmlspecialchars($row['permit_number']) ?></td>
                            <td>
                                <?php if ($row['permit_document']): ?>
                                    <a href="<?= htmlspecialchars($row['permit_document']) ?>" target="_blank">View Document</a>
                                <?php else: ?>
                                    No document
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($row['additional_document']): ?>
                                    <a href="<?= htmlspecialchars($row['additional_document']) ?>" target="_blank">View Document</a>
                                <?php else: ?>
                                    No document
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($row['contact_phone']) ?></td>
                            <td><?= htmlspecialchars($row['contact_email']) ?></td>
                            <td>
                                <form action="stall_requests.php" method="POST">
                                    <input type="hidden" name="stall_id" value="<?= $row['stall_id'] ?>">
                                    <?php if ($row['is_approved'] == 0): ?>
                                        <button type="submit" name="action" value="approve" class="btn approve-btn">Approve</button>
                                        <button type="submit" name="action" value="reject" class="btn reject-btn">Reject</button>
                                    <?php else: ?>
                                        <span class="status <?= $row['is_approved'] ? 'approved' : 'rejected' ?>">
                                            <?= $row['is_approved'] ? 'Approved' : 'Rejected' ?>
                                        </span>
                                    <?php endif; ?>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9">No stall requests available.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </section>

    <footer>
        <p>&copy; 2025 QuickGrill. All rights reserved.</p>
    </footer>

    <script>
        window.onload = function() {
            var notification = document.getElementById('notification');
            if (notification) {
                notification.style.display = 'block';
                setTimeout(function() {
                    notification.style.opacity = '0';
                    setTimeout(function() {
                        notification.style.display = 'none';
                    }, 1000);
                }, 3000);
            }
        };
    </script>
</body>

</html>

<?php
$conn->close();
?>