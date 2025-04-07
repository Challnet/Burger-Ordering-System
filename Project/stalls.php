  <?php
  session_start();
  include 'db.php';

  if (!isset($_SESSION["user_id"])) {
    header("location: login.php");
    exit();
  }

  // Fetch user's stall details
  $stall_name = null;
  $stall_id = null;
  $is_approved = null;

  if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $query_stall = "SELECT stall_id, name, is_approved FROM stalls WHERE user_id = ?";
    $stmt = $conn->prepare($query_stall);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($stall_id, $stall_name, $is_approved);
    $stmt->fetch();
    $stmt->close();
  }

  $query = "
      SELECT s.stall_id, s.name AS stall_name, s.description, s.image_url, s.user_id, u.name AS owner_name, s.price_range, s.address, s.operating_hours, s.is_approved
      FROM stalls s
      JOIN users u ON s.user_id = u.user_id
      WHERE s.is_approved = 1
      ORDER BY s.stall_id DESC
  ";
  $stmt = $conn->prepare($query);
  $stmt->execute();
  $result = $stmt->get_result();
  ?>

  <!DOCTYPE html>
  <html lang="en">

  <head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stalls | QuickGrill</title>
    <link rel="stylesheet" href="css/style_stalls.css">
    <link href="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.min.css" rel="stylesheet">
  </head>

  <body>

    <!-- Menu -->
    <div class="menu">
      <nav>
        <input type="checkbox" id="check">
        <label for="check" class="checkbtn">
          <ion-icon name="grid-outline"></ion-icon>
        </label>
        <label class="logo">QuickGrill</label>
        <ul>
          <li><a href="home.php">Home</a></li>
          <li><a href="stalls.php" class="active">Stalls</a></li>
          <li><a href="about.php">About</a></li>
          <?php if (isset($_SESSION['user_name'])): ?>
            <?php
            $first_name = explode(" ", $_SESSION['user_name'])[0];
            ?>
            <li class="dropdown">
              <a href="#" class="dropdown-toggle"><?= htmlspecialchars($first_name) ?></a>
              <ul class="dropdown-menu">
                <li><a href="profile.php">Profile</a></li>
                <li><a href="cart.php">Cart</a></li>
                <li><a href="my_orders.php">My Orders</a></li>

                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                  <li><a href="stall_requests.php">Stall Requests</a></li>
                  <li><a href="user_management.php">User Management</a></li>
                <?php else: ?>
                  <?php if ($stall_name && $is_approved == 1): ?>
                    <li><a href="stall_profile.php?stall_id=<?php echo $stall_id; ?>">Manage Stall: <?php echo htmlspecialchars($stall_name); ?></a></li>
                  <?php elseif ($stall_name && $is_approved == 0): ?>
                  <?php else: ?>
                    <li><a href="register_stall.php">Register Stall</a></li>
                  <?php endif; ?>
                <?php endif; ?>

                <li><a href="logout.php">Sign Out</a></li>
              </ul>
            </li>
          <?php else: ?>
            <li><a href="login.php">Sign In</a></li>
          <?php endif; ?>
        </ul>
      </nav>
    </div>
    <!-- End Menu -->

    <!-- Stalls Section -->
    <section class="stalls-container">
      <h2 class="section-title">Discover Our Stalls</h2>
      <div class="stalls-grid">
        <?php if ($result->num_rows > 0): ?>
          <?php while ($row = $result->fetch_assoc()): ?>
            <div class="stall-card">
              <div class="stall-image-wrapper">
                <img src="<?= htmlspecialchars($row['image_url']) ?>" alt="<?= htmlspecialchars($row['stall_name']) ?>" class="stall-image">
              </div>
              <div class="stall-info">
                <h3><?= htmlspecialchars($row['stall_name']) ?></h3>
                <p class="description"><?= htmlspecialchars($row['description']) ?></p>
                <p class="price-range"><strong>Price Range:</strong> <?= htmlspecialchars($row['price_range']) ?></p>
                <p class="address"><strong>Address:</strong> <?= htmlspecialchars($row['address']) ?></p>
                <p class="operating-hours"><strong>Operating Hours:</strong> <?= htmlspecialchars($row['operating_hours']) ?></p>
                <p class="owner">Vendor: <?= htmlspecialchars($row['owner_name']) ?></p>
                <a href="menu.php?stall_id=<?= $row['stall_id'] ?>" class="view-btn">View Stall</a>

                <!-- Only the Owner of the Stall or Admin can see this link-->
                <?php if (isset($_SESSION['user_id']) && ($_SESSION['user_id'] == $row['user_id'] || $_SESSION['role'] == 'admin')): ?>
                  <a href="stall_profile.php?stall_id=<?= $row['stall_id'] ?>" class="view-btn">Manage Stall</a>
                <?php endif; ?>

              </div>
            </div>
          <?php endwhile; ?>
        <?php else: ?>
          <p class="no-stalls">No stalls available at the moment.</p>
        <?php endif; ?>
      </div>
    </section>
    <!-- End Stalls Section -->

  </body>

  </html>