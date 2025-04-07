<?php
session_start();
include 'db.php';

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

if (!isset($_GET['stall_id']) || !isset($_SESSION['user_id'])) {
  die("Stall ID or User is missing.");
}

$stall_id = intval($_GET['stall_id']);

// Fetch the stall data from the database
$checkStallQuery = "SELECT user_id, name, description, address, image_url FROM stalls WHERE stall_id = ?";
$stmt = $conn->prepare($checkStallQuery);
$stmt->bind_param('i', $stall_id);
$stmt->execute();
$result = $stmt->get_result();
$stall = $result->fetch_assoc();

if (!$stall) {
  die("Stall not found.");
}

// Check if the user is authorized to manage this stall
if ($stall['user_id'] != $_SESSION['user_id'] && $_SESSION['role'] != 'admin') {
  die("You are not authorized to manage this stall.");
}

$_SESSION['stall_id'] = $stall_id;
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Your Stall | QuickGrill</title>
  <link rel="stylesheet" href="css/style_stall_profile.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
  <div class="sidebar">
    <div class="sidebar-header">
      <div class="icon">
        <ion-icon name="storefront-outline"></ion-icon>
      </div>
      <span>Stall Manager</span>
      <ion-icon name="chevron-down-outline" class="dropdown-icon" onclick="toggleDropdown()"></ion-icon>
      <div class="dropdown-menu" id="dropdownMenu">
        <a href="stalls.php">Burger Marketplace</a>
      </div>
    </div>
    <ul class="sidebar-menu">
      <li id="profileLink"><ion-icon name="person-outline"></ion-icon> Profile</li>
      <li id="menusLink"><ion-icon name="list-outline"></ion-icon> Menus</li>
      <li id="ordersLink"><ion-icon name="clipboard-outline"></ion-icon> Orders</li>
    </ul>
    <div class="sidebar-footer">
      <div class="user-profile">
        <span><?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
      </div>
    </div>
  </div>

  <div id="profile" class="content-section">
    <?php include('stall_profile_pro.php'); ?>
  </div>
  <div id="menus" class="content-section" style="display:none;">
    <?php include('stall_menus.php'); ?>
  </div>
  <div id="orders" class="content-section" style="display:none;">
    <h1>Orders</h1>
    <?php include('stall_orders.php'); ?>
  </div>

  <script>
    const profileLink = document.getElementById("profileLink");
    const menusLink = document.getElementById("menusLink");
    const ordersLink = document.getElementById("ordersLink");

    const profileSection = document.getElementById("profile");
    const menusSection = document.getElementById("menus");
    const ordersSection = document.getElementById("orders");

    profileLink.addEventListener("click", () => {
      hideAllSections();
      profileSection.style.display = "block";
    });

    menusLink.addEventListener("click", () => {
      hideAllSections();
      menusSection.style.display = "block";
    });

    ordersLink.addEventListener("click", () => {
      hideAllSections();
      ordersSection.style.display = "block";
    });

    function hideAllSections() {
      profileSection.style.display = "none";
      menusSection.style.display = "none";
      ordersSection.style.display = "none";
    }

    profileSection.style.display = "block";
  </script>
</body>

</html>