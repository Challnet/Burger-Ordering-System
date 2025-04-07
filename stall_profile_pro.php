<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include 'db.php';

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_GET['stall_id'])) {
    die("Stall ID is missing.");
}

$stall_id = intval($_GET['stall_id']);

// Check if the user owns the stall
$checkStallQuery = "SELECT user_id, name, description, address, image_url FROM stalls WHERE stall_id = ?";
$stmt = $conn->prepare($checkStallQuery);
$stmt->bind_param('i', $stall_id);
$stmt->execute();
$result = $stmt->get_result();
$stall = $result->fetch_assoc();

if (!$stall) {
    die("Stall not found.");
}

if ($stall['user_id'] != $_SESSION['user_id'] && $_SESSION['role'] != 'admin') {
    die("You are not authorized to manage this stall.");
}

// Handle form submission to update stall details
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_stall'])) {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $address = $_POST['address'];
    $image_url = $stall['image_url'];

    // Directory for uploading images
    $uploadDir = 'uploads/';
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    if (isset($_FILES['image_url']) && $_FILES['image_url']['error'] == 0) {
        $imageTmpName = $_FILES['image_url']['tmp_name'];
        $imageName = $_FILES['image_url']['name'];
        $imageExt = strtolower(pathinfo($imageName, PATHINFO_EXTENSION));

        $allowedExts = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($imageExt, $allowedExts)) {
            $_SESSION['error_message'] = "Invalid file type. Only images are allowed.";
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        }

        $imageNewName = uniqid('img_') . '.' . $imageExt;
        $imagePath = $uploadDir . $imageNewName;

        // Delete old image if exists
        if (!empty($image_url) && file_exists($image_url)) {
            unlink($image_url);
        }

        // Move the uploaded file
        if (move_uploaded_file($imageTmpName, $imagePath)) {
            $image_url = $imagePath;
        } else {
            $_SESSION['error_message'] = "Error uploading image.";
        }
    }

    // Update stall details in the database
    $query = "UPDATE stalls SET name = ?, description = ?, address = ?, image_url = ? WHERE stall_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssssi", $name, $description, $address, $image_url, $stall_id);

    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Stall details updated successfully.";
        $stall['name'] = $name;
        $stall['description'] = $description;
        $stall['address'] = $address;
        $stall['image_url'] = $image_url;
    } else {
        $_SESSION['error_message'] = "Failed to update stall details. Please try again.";
    }

    $stmt->close();
}

// Handle stall deletion
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_stall'])) {
    $vendor_id = $stall['user_id'];

    // Delete stall from database
    $deleteQuery = "DELETE FROM stalls WHERE stall_id = ?";
    $stmt = $conn->prepare($deleteQuery);
    $stmt->bind_param('i', $stall_id);

    if ($stmt->execute()) {
        // Update vendor's role to 'customer'
        $updateUserRoleQuery = "UPDATE users SET role = 'customer' WHERE user_id = ?";
        $stmt_update = $conn->prepare($updateUserRoleQuery);
        $stmt_update->bind_param('i', $vendor_id);

        if ($stmt_update->execute()) {
            $_SESSION['success_message'] = "Stall deleted successfully, and vendor role updated to 'customer'.";
        } else {
            $_SESSION['error_message'] = "Failed to update the user's role. Please try again.";
        }

        $stmt_update->close();

        header("Location: home.php");
        exit;
    } else {
        $_SESSION['error_message'] = "Failed to delete stall. Please try again.";
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Stall</title>
    <link rel="stylesheet" href="css/style_stall_profile_pro.css">
    <script>
        setTimeout(() => {
            const messages = document.querySelectorAll('.message');
            messages.forEach(message => message.style.display = 'none');
        }, 5000);
    </script>
</head>

<body>
    <?php if (isset($_SESSION['success_message'])): ?>
        <p class="message success-message"><?php echo $_SESSION['success_message'];
                                            unset($_SESSION['success_message']); ?></p>
    <?php elseif (isset($_SESSION['error_message'])): ?>
        <p class="message error-message"><?php echo $_SESSION['error_message'];
                                            unset($_SESSION['error_message']); ?></p>
    <?php endif; ?>

    <form action="" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="stall_id" value="<?php echo $stall_id; ?>">

        <label for="name">Stall Name:</label>
        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($stall['name']); ?>" required>

        <label for="description">Description:</label>
        <textarea id="description" name="description" required><?php echo htmlspecialchars($stall['description']); ?></textarea>

        <label for="address">Address:</label>
        <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($stall['address']); ?>" required>

        <label for="image_url">Current Image:</label>
        <img src="<?php echo $stall['image_url'] ?: 'default-image.jpg'; ?>" alt="Stall Image" class="stall-image">

        <label for="image_url">Upload New Image:</label>
        <input type="file" id="image_url" name="image_url" accept="image/*">

        <button type="submit" name="update_stall">Save Changes</button>
    </form>

    <form action="" method="POST" onsubmit="return confirm('Are you sure you want to delete this stall?');">
        <button type="submit" name="delete_stall" class="delete-button">Delete Stall</button>
    </form>
</body>

</html>