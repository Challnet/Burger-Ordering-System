<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

// Fetch all users from the database
$query = "
    SELECT u.user_id, u.name, u.email, u.phone, u.role, u.created_at, s.name AS stall_name 
    FROM users u
    LEFT JOIN stalls s ON u.user_id = s.user_id
";
$result = $conn->query($query);

// Add a new user
if (isset($_POST['add_user'])) {
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $phone = htmlspecialchars($_POST['phone']);
    $role = htmlspecialchars($_POST['role']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    if ($role !== 'customer' && $role !== 'admin') {
        echo "<script>alert('Invalid role.'); window.location.href='user_management.php';</script>";
        exit;
    }

    // Insert the new user into the database
    $insert_query = "INSERT INTO users (name, email, phone, role, password, created_at) VALUES (?, ?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($insert_query);
    $stmt->bind_param("sssss", $name, $email, $phone, $role, $password);

    if ($stmt->execute()) {
        echo "<script>alert('User added successfully.'); window.location.href='user_management.php';</script>";
    } else {
        echo "<script>alert('Failed to add user. Please try again.'); window.location.href='user_management.php';</script>";
    }

    $stmt->close();
    exit();
}

// Update user data
if (isset($_POST['update_user'])) {
    $user_id = intval($_POST['user_id']);

    // Initialize variables for the fields
    $name = isset($_POST['name']) ? htmlspecialchars($_POST['name']) : null;
    $email = isset($_POST['email']) ? htmlspecialchars($_POST['email']) : null;
    $phone = isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : null;
    $role = isset($_POST['role']) ? htmlspecialchars($_POST['role']) : null;

    if ($role === 'vendor') {
        echo "<script>alert('Role vendor cannot be assigned.'); window.location.href='user_management.php';</script>";
        exit;
    }

    // Prevent updating the role of a user with the 'vendor' role
    $check_user_query = "SELECT role FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($check_user_query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($current_role);
    $stmt->fetch();
    $stmt->close();

    if ($current_role === 'vendor' && $role !== 'vendor') {
        echo "<script>alert('You cannot change the role of an vendor.'); window.location.href='user_management.php';</script>";
        exit;
    }

    // Allow changing role only between 'customer' and 'admin'
    if ($role !== 'customer' && $role !== 'admin' && $role !== null) {
        echo "<script>alert('Role can only be changed between customer and admin.'); window.location.href='user_management.php';</script>";
        exit;
    }

    // Build the update query dynamically based on which fields are not null
    $update_query = "UPDATE users SET ";
    $params = [];
    $types = "";

    if ($name) {
        $update_query .= "name = ?, ";
        $params[] = $name;
        $types .= "s";
    }

    if ($email) {
        $update_query .= "email = ?, ";
        $params[] = $email;
        $types .= "s";
    }

    if ($phone) {
        $update_query .= "phone = ?, ";
        $params[] = $phone;
        $types .= "s";
    }

    if ($role !== null) {
        $update_query .= "role = ?, ";
        $params[] = $role;
        $types .= "s";
    }

    // Remove the last comma
    $update_query = rtrim($update_query, ", ");

    $update_query .= " WHERE user_id = ?";
    $params[] = $user_id;
    $types .= "i";

    // Prepare and execute the update query
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $stmt->close();

    header('Location: user_management.php');
    exit();
}

// Delete user
if (isset($_GET['delete_user_id'])) {
    $delete_user_id = intval($_GET['delete_user_id']);

    // Check if the user is an vendor
    $check_owner_query = "SELECT role FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($check_owner_query);
    $stmt->bind_param("i", $delete_user_id);
    $stmt->execute();
    $stmt->bind_result($role);
    $stmt->fetch();
    $stmt->close();

    // If the user is an vendor, delete their stall
    if ($role === 'vendor') {
        $delete_stall_query = "DELETE FROM stalls WHERE user_id = ?";
        $stmt = $conn->prepare($delete_stall_query);
        $stmt->bind_param("i", $delete_user_id);
        $stmt->execute();
        $stmt->close();
    }

    // Delete the user
    $delete_user_query = "DELETE FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($delete_user_query);
    $stmt->bind_param("i", $delete_user_id);
    $stmt->execute();
    $stmt->close();

    header('Location: user_management.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
    <link rel="stylesheet" href="css/style_user_management.css">
</head>

<body>

    <header>
        <div class="header-container">
            <h1>User Management</h1>
        </div>
    </header>

    <a href="stalls.php" class="btn back-to-dashboard">Back to Stalls</a>

    <!-- Add User Form -->
    <section id="add-user">
        <h2>Add New User</h2>
        <form method="POST" action="user_management.php" class="add-user-form">
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" name="name" id="name" required>
            </div>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" name="email" id="email" required>
            </div>

            <div class="form-group">
                <label for="phone">Phone:</label>
                <input type="text" name="phone" id="phone" required>
            </div>

            <div class="form-group">
                <label for="role">Role:</label>
                <select name="role" id="role" required>
                    <option value="customer">Customer</option>
                    <option value="admin">Admin</option>
                </select>
            </div>

            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" name="password" id="password" required>
            </div>

            <button type="submit" name="add_user" class="btn add-btn">Add User</button>
        </form>
    </section>

    <!-- User Management Table -->
    <section id="user-management">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Role</th>
                    <th>Created At</th>
                    <th>Stall</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['user_id']; ?></td>
                            <td><?php echo htmlspecialchars($row['name']); ?></td>
                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                            <td><?php echo htmlspecialchars($row['phone']); ?></td>
                            <td><?php echo htmlspecialchars($row['role']); ?></td>
                            <td><?php echo $row['created_at']; ?></td>
                            <td>
                                <?php
                                echo $row['stall_name']
                                    ? htmlspecialchars($row['stall_name'])
                                    : 'No Stall';
                                ?>
                            </td>
                            <td>
                                <!-- Edit User -->
                                <button class="btn edit-btn" onclick="editUser(<?php echo $row['user_id']; ?>, '<?php echo $row['role']; ?>')">Edit</button>

                                <!-- Delete User -->
                                <?php if ($row['user_id'] != $_SESSION['user_id']): ?>
                                    <a href="user_management.php?delete_user_id=<?php echo $row['user_id']; ?>" class="btn delete-btn" onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8">No users found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>

        </table>
    </section>

    <!-- Edit User Modal -->
    <div id="editUserModal" class="modal">
        <div class="modal-content">
            <h2>Edit User</h2>
            <form method="POST" action="user_management.php">
                <input type="hidden" name="user_id" id="user_id">

                <!-- Only show fields that need to be updated -->
                <label for="name">Name:</label>
                <input type="text" name="name" id="name" placeholder="New name">

                <label for="email">Email:</label>
                <input type="email" name="email" id="email" placeholder="New email">

                <label for="phone">Phone:</label>
                <input type="text" name="phone" id="phone" placeholder="New phone">

                <label for="role">Role:</label>
                <select name="role" id="role">
                    <option value="customer">Customer</option>
                    <option value="admin">Admin</option>
                </select>

                <button type="submit" name="update_user" class="btn update-btn">Update</button>
                <button type="button" class="btn cancel-btn" onclick="closeModal()">Cancel</button>
            </form>
        </div>
    </div>

    <footer>
        <p>&copy; 2025 QuickGrill. All rights reserved.</p>
    </footer>

    <script>
        function editUser(userId, currentRole) {
            const modal = document.getElementById('editUserModal');
            const userName = document.getElementById('name');
            const userEmail = document.getElementById('email');
            const userPhone = document.getElementById('phone');
            const userRole = document.getElementById('role');
            const userIdField = document.getElementById('user_id');

            userIdField.value = userId;
            userRole.value = currentRole;

            if (currentRole === 'vendor') {
                userRole.disabled = true;
            } else {
                userRole.disabled = false;
            }

            modal.style.display = "block";
        }

        function closeModal() {
            document.getElementById('editUserModal').style.display = "none";
        }
    </script>

</body>

</html>

<?php
$conn->close();
?>