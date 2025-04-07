<?php
session_start();
include 'db.php';

$stall_name = null;

if (isset($_SESSION['user_id'])) {
  $user_id = $_SESSION['user_id'];

  // Query to find the stall associated with this user
  $query = "SELECT name FROM stalls WHERE user_id = ? AND is_approved = 1 LIMIT 1";
  $stmt = $conn->prepare($query);
  $stmt->bind_param("i", $user_id);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $stall_name = $row['name'];
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>About | QuickGrill</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="css/style_about.css" />
</head>

<body>
  <div class="menu">
    <nav>
      <input type="checkbox" id="check" />
      <label for="check" class="checkbtn">
        <ion-icon name="grid-outline"></ion-icon>
      </label>

      <label class="logo">QuickGrill</label>

      <ul>
        <li><a href="home.php">Home</a></li>
        <li><a href="stalls.php">Stalls</a></li>
        <li><a href="about.php" class="active">About</a></li>
        <?php if (isset($_SESSION['user_name'])): ?>
          <?php
          $first_name = explode(" ", $_SESSION['user_name'])[0];
          $user_id = $_SESSION['user_id'];

          $query = "SELECT stall_id, name FROM stalls WHERE user_id = ?";
          $stmt = $conn->prepare($query);
          $stmt->bind_param("i", $user_id);
          $stmt->execute();
          $stmt->store_result();
          $stmt->bind_result($stall_id, $stall_name);
          $stmt->fetch();
          $stmt->close();
          ?>
          <li class="dropdown">
            <a href="#" class="dropdown-toggle"><?php echo htmlspecialchars($first_name); ?></a>
            <ul class="dropdown-menu">
              <li><a href="profile.php">Profile</a></li>
              <li><a href="cart.php">Cart</a></li>
              <li><a href="my_orders.php">My Orders</a></li>

              <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                <li><a href="stall_requests.php">Stall Requests</a></li>
                <li><a href="user_management.php">User Management</a></li>
              <?php else: ?>
                <?php if ($stall_name): ?>
                  <li><a href="stall_profile.php?stall_id=<?php echo $stall_id; ?>" class="view-btn">Manage Stall: <?php echo htmlspecialchars($stall_name); ?></a></li>
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

  <!--About Section-->
  <section id="about-section">
    <!-- about left  -->
    <div class="about-left">
      <img src="assets/QuickGrill_App.jpg" alt="About Img" />
    </div>

    <!-- about right  -->
    <div class="about-right">
      <h1>About Us</h1>
      <p>Welcome to QuickGrill – a local burger stall application built to connect burger lovers with the best street-side stalls through seamless online ordering.
        We aim to bring your favorite burger stalls to your fingertips, making it easier than ever to enjoy freshly made burgers with just a few clicks.
        Our platform empowers local burger vendors to serve their customers with convenience, while providing users with quick and easy access to delicious, high-quality burgers from the streets.
      </p>
      <br>
      <p>
        Join us today and experience how QuickGrill is transforming the way we enjoy burgers, one click at a time.
      </p>
      <br><br>
      <div class="expertise">
        <h3>Social Medias</h3>
        <ul>
          <li>
            <span class="expertise-logo">
              <i class="bi bi-facebook"></i>

            </span>
            <p>Facebook</p>
          </li>
          <li>
            <span class="expertise-logo">
              <i class="bi bi-instagram"></i>
            </span>
            <p>Instagram</p>
          </li>
          <li>
            <span class="expertise-logo">
              <i class="bi bi-twitter-x"></i>
            </span>
            <p>X</p>
          </li>
          <li>
            <span class="expertise-logo">
              <i class="bi bi-tiktok"></i>
            </span>
            <p>TikTok</p>
          </li>
        </ul>
      </div>
    </div>
  </section>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const dropdownToggle = document.querySelector('.dropdown-toggle');
      const dropdownMenu = document.querySelector('.dropdown-menu');

      dropdownToggle.addEventListener('click', function(event) {
        event.preventDefault(); // Prevent the default anchor behavior
        dropdownMenu.style.display = dropdownMenu.style.display === 'block' ? 'none' : 'block';
      });

      // Close the dropdown if clicked outside
      window.addEventListener('click', function(event) {
        if (!dropdownToggle.contains(event.target) && !dropdownMenu.contains(event.target)) {
          dropdownMenu.style.display = 'none';
        }
      });
    });
  </script>
</body>

</html>