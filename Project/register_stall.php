<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    die("Error: User is not logged in.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stall_name = trim($_POST['stall_name']);
    $description = trim($_POST['stall_description']);
    $address = trim($_POST['address']);
    $review_rating = 0;
    $price_range = trim($_POST['price_range']);
    $permit_number = trim($_POST['permit_number']);
    $operating_hours = trim($_POST['operating_hours']);
    $contact_email = trim($_POST['contact_email']);
    $contact_phone = trim($_POST['contact_phone']);
    $is_approved = 0;
    $user_id = $_SESSION['user_id'];

    $check_query = "SELECT * FROM stalls WHERE user_id = ?";
    $stmt = $conn->prepare($check_query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $error_message = "You already own a stall. You cannot register another one.";
        $stmt->close();
    } else {
        $upload_dir = 'uploads/';
        $allowed_image_types = ['jpg', 'jpeg', 'png'];
        $allowed_document_types = ['pdf', 'jpg', 'jpeg', 'png'];

        $image_result = handleFileUpload($_FILES['image_url'], $allowed_image_types, $upload_dir);
        if (isset($image_result["error"])) {
            $error_message = $image_result["error"];
        } else {
            $image_url = $image_result["success"];

            $permit_result = handleFileUpload($_FILES['permit_document'], $allowed_document_types, $upload_dir);
            if (isset($permit_result["error"])) {
                $error_message = $permit_result["error"];
            } else {
                $permit_document = $permit_result["success"];

                $additional_result = handleFileUpload($_FILES['additional_document'], $allowed_document_types, $upload_dir);
                $additional_document = isset($additional_result["success"]) ? $additional_result["success"] : null;

                $query = "INSERT INTO stalls (name, description, image_url, address, review_rating, price_range, permit_number, permit_document, additional_document, operating_hours, contact_email, contact_phone, is_approved, user_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($query);
                $stmt->bind_param('ssssdsssssssii', $stall_name, $description, $image_url, $address, $review_rating, $price_range, $permit_number, $permit_document, $additional_document, $operating_hours, $contact_email, $contact_phone, $is_approved, $user_id);

                if ($stmt->execute()) {
                    $success_message = "Stall successfully registered!";
                    $stmt->close();
                    $conn->close();
                    header("location:stalls.php");
                    exit();
                } else {
                    $error_message = "Error registering stall.";
                    $stmt->close();
                }
            }
        }
    }
}

function handleFileUpload($file, $allowed_types, $upload_dir)
{
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    if (isset($file) && $file['size'] > 0) {
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if (!in_array($extension, $allowed_types)) {
            return ["error" => "Invalid file format. Allowed formats: " . implode(", ", $allowed_types)];
        }

        if ($file['size'] > 5000000) {
            return ["error" => "File is too large (max 5MB)."];
        }

        $new_name = uniqid('file_') .
            "_" . basename($file['name']);
        $destination = $upload_dir . $new_name;

        if (move_uploaded_file($file['tmp_name'], $destination)) {
            return ["success" => $destination];
        }
        return ["error" => "File upload failed."];
    }
    return ["success" => null];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Stall</title>
    <link rel="stylesheet" href="css/style_register_stall.css">
</head>

<body>
    <div class="form-container">
        <h2>Register a Stall</h2>

        <?php if (isset($error_message)) : ?>
            <div class="notification show">
                <div class="notification-inner">
                    <p><?php echo $error_message; ?></p>
                    <a href="stalls.php">Go back to your stalls</a>
                </div>
            </div>
        <?php endif; ?>

        <?php if (isset($success_message)) : ?>
            <div class="notification show">
                <div class="notification-inner">
                    <p><?php echo $success_message; ?></p>
                    <a href="stalls.php">Go back to your stalls</a>
                </div>
            </div>
        <?php endif; ?>

        <form action="register_stall.php" method="POST" enctype="multipart/form-data">
            <!-- Stall Name -->
            <label for="stall_name">Stall Name</label>
            <input type="text" id="stall_name" name="stall_name" required placeholder="Enter your stall name">

            <!-- Stall Description -->
            <label for="stall_description">Stall Description</label>
            <textarea id="stall_description" name="stall_description" rows="4" required placeholder="Describe your stall and what you sell"></textarea>

            <!-- Stall Address -->
            <label for="address">Address</label>
            <input type="text" id="address" name="address" required placeholder="Enter your stall location">

            <!-- Stall Image -->
            <label for="image_url">Upload Stall Image</label>
            <input type="file" id="image_url" name="image_url" accept="uploads/*" required>

            <!-- Price Range -->
            <label for="price_range">Price Range</label>
            <input type="text" id="price_range" name="price_range" required placeholder="Enter price range of the stall">

            <!-- Permit Number -->
            <label for="permit_number">Permit or License Number</label>
            <input type="text" id="permit_number" name="permit_number" required placeholder="Enter your permit/license number">

            <!-- Permit Document -->
            <label for="permit_document">Upload Permit Document</label>
            <input type="file" id="permit_document" name="permit_document" accept="application/pdf, image/*" required>

            <!-- Additional Documents -->
            <label for="additional_document">Upload Additional Documents</label>
            <input type="file" id="additional_documents" name="additional_document" accept="application/pdf, image/*">

            <!-- Operating Hours -->
            <label for="operating_hours">Operating Hours</label>
            <input type="text" id="operating_hours" name="operating_hours" placeholder="e.g., 9:00 AM - 6:00 PM" required>

            <!-- Contact Email -->
            <label for="contact_email">Contact Email</label>
            <input type="email" id="contact_email" name="contact_email" required placeholder="Enter your contact email">

            <!-- Contact Phone -->
            <label for="contact_phone">Contact Phone</label>
            <input type="tel" id="contact_phone" name="contact_phone" required placeholder="Enter your contact phone number">

            <!-- Terms and Conditions -->
            <label>
                <input type="checkbox" name="terms" required>
                I agree to the <a href="terms.html" target="_blank">terms and conditions</a> for registering a stall.
            </label>

            <!-- Submit Button -->
            <button type="submit">Submit Registration</button>
        </form>
    </div>
</body>

</html>