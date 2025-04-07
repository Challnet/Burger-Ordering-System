<?php
session_start();
include 'db.php';

if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Database connection failed: ' . $conn->connect_error]));
}

if (!isset($_SESSION['user_id'])) {
    die(json_encode(['success' => false, 'message' => 'User is not logged in. Please log in.']));
}

$user_id = $_SESSION['user_id'];

if (!isset($_SESSION['stall_id'])) {
    if ($_SESSION['role'] == 'admin') {
        $stmt = $conn->prepare("SELECT stall_id FROM stalls LIMIT 1");
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $stall_id = $result->fetch_assoc()['stall_id'];
            $_SESSION['stall_id'] = $stall_id;
        } else {
            die(json_encode(['success' => false, 'message' => 'No stalls found in the system.']));
        }
    } else {
        $stmt = $conn->prepare("SELECT stall_id FROM stalls WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $stall_id = $result->fetch_assoc()['stall_id'];
            $_SESSION['stall_id'] = $stall_id;
        } else {
            die(json_encode(['success' => false, 'message' => 'No stall associated with the logged-in user.']));
        }
    }
} else {
    $stall_id = $_SESSION['stall_id'];
}


$input = json_decode(file_get_contents("php://input"), true);
$action = $input['action'] ?? $_GET['action'] ?? $_POST['action'] ?? null;

// Handle actions
switch ($action) {
    case "fetch":
        $stmt = $conn->prepare("SELECT * FROM stall_menus WHERE stall_id = ?");
        $stmt->bind_param("i", $stall_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $menuItems = $result->fetch_all(MYSQLI_ASSOC);
        echo json_encode(['success' => true, 'data' => $menuItems]);
        break;

        // Add a new menu item (Action Case)
    case "add":
        $name = $_POST['menu-name'] ?? '';
        $description = $_POST['menu-description'] ?? '';
        $price = $_POST['menu-price'] ?? 0;

        if (isset($_FILES['menu-image']) && $_FILES['menu-image']['error'] === UPLOAD_ERR_OK) {
            $image = $_FILES['menu-image'];
            $image_path = 'uploads/' . uniqid() . '_' . basename($image['name']);

            if (!move_uploaded_file($image['tmp_name'], $image_path)) {
                die(json_encode(['success' => false, 'message' => 'Failed to upload image. Check folder permissions and path.']));
            }

            $stmt = $conn->prepare("INSERT INTO stall_menus (stall_id, name, description, price, image_url) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("issds", $stall_id, $name, $description, $price, $image_path);

            if ($stmt->execute()) {
                $stmt = $conn->prepare("SELECT * FROM stall_menus WHERE stall_id = ?");
                $stmt->bind_param("i", $stall_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $menuItems = $result->fetch_all(MYSQLI_ASSOC);
                echo json_encode(['success' => true, 'message' => 'Menu item added successfully!', 'data' => $menuItems]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error: ' . $stmt->error]);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Image upload failed. File not received.']);
        }
        break;


    case "remove":
        // Remove selected menu items
        $ids = $input['ids'] ?? [];
        if (!empty($ids)) {
            $ids_placeholder = implode(',', array_fill(0, count($ids), '?'));
            $stmt = $conn->prepare("DELETE FROM stall_menus WHERE id IN ($ids_placeholder) AND stall_id = ?");
            $types = str_repeat('i', count($ids)) . "i";
            $params = array_merge($ids, [$stall_id]);
            $stmt->bind_param($types, ...$params);

            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Selected menu items removed successfully!']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error: ' . $stmt->error]);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'No items selected to remove.']);
        }
        break;

    case "update":
        $id = $_POST['id'] ?? null;
        $name = $_POST['menu-name'] ?? null;
        $description = $_POST['menu-description'] ?? null;
        $price = $_POST['menu-price'] ?? null;
        $image_url = $_FILES['menu-image'] ?? null;

        if ($id) {
            $stmt = $conn->prepare("SELECT image_url FROM stall_menus WHERE id = ? AND stall_id = ?");
            $stmt->bind_param("ii", $id, $stall_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $currentImage = $result->fetch_assoc()['image_url'] ?? null;

            $query = "UPDATE stall_menus SET ";
            $params = [];
            $types = "";

            if ($name) {
                $query .= "name = ?, ";
                $params[] = $name;
                $types .= "s";
            }
            if ($description) {
                $query .= "description = ?, ";
                $params[] = $description;
                $types .= "s";
            }
            if ($price) {
                $query .= "price = ?, ";
                $params[] = $price;
                $types .= "d";
            }

            if ($image_url) {
                $image_path = 'uploads/' . uniqid() . '_' . basename($image_url['name']);
                move_uploaded_file($image_url['tmp_name'], $image_path);
                $query .= "image_url = ?, ";
                $params[] = $image_path;
                $types .= "s";
            } else {
                $query .= "image_url = ?, ";
                $params[] = $currentImage;
                $types .= "s";
            }

            $query = rtrim($query, ", ") . " WHERE id = ? AND stall_id = ?";
            $params[] = $id;
            $params[] = $stall_id;
            $types .= "ii";

            $stmt = $conn->prepare($query);
            $stmt->bind_param($types, ...$params);

            if ($stmt->execute()) {
                // Return the updated menu items
                $stmt = $conn->prepare("SELECT * FROM stall_menus WHERE stall_id = ?");
                $stmt->bind_param("i", $stall_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $menuItems = $result->fetch_all(MYSQLI_ASSOC);
                echo json_encode(['success' => true, 'message' => 'Menu item updated successfully!', 'data' => $menuItems]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error: ' . $stmt->error]);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Missing required fields.']);
        }
        break;


    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action.']);
        break;
}

$conn->close();
