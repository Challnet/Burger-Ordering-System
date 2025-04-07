<?php
session_start();
include 'db.php';

$stall_name = null;
$stall_id = null;

if (isset($_SESSION['user_id'])) {
  $user_id = $_SESSION['user_id'];

  // Query to find the stall associated with this user
  $query = "SELECT stall_id, name, is_approved FROM stalls WHERE user_id = ? LIMIT 1";
  $stmt = $conn->prepare($query);
  $stmt->bind_param("i", $user_id);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $stall_name = $row['name'];
    $stall_id = $row['stall_id'];
    $is_approved = $row['is_approved'];
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Home | QuickGrill</title>
  <link rel="stylesheet" href="css/style_home.css" />
</head>

<body>

  <!-- Menu -->
  <div class="menu">
    <nav>
      <input type="checkbox" id="check" />
      <label for="check" class="checkbtn">
        <ion-icon name="grid-outline"></ion-icon>
      </label>

      <label class="logo">QuickGrill</label>

      <ul>
        <li><a href="home.php" class="active">Home</a></li>
        <li><a href="stalls.php">Stalls</a></li>
        <li><a href="about.php">About</a></li>
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
                <?php if ($stall_name && $is_approved == 1): ?>
                  <li><a href="stall_profile.php?stall_id=<?php echo $stall_id; ?>" class="view-btn">Manage Stall: <?php echo htmlspecialchars($stall_name); ?></a></li>
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

  <!-- Hero Section -->
  <div class="section flex" id="hero-section">
    <div class="text">
      <h1 class="primary">
        It's Not Just a Burger, <br />
        It's a <span>Flavor Adventure</span>
      </h1>

      <p class="tertiary">
        Savor the juiciest, most mouthwatering burgers crafted with passion. From fresh ingredients to bold flavors, every bite is an experience worth craving. Check out the stalls!
      </p>


      <a href="<?php echo isset($_SESSION['user_name']) ? 'stalls.php' : 'login.php'; ?>" class="btn">Explore Stalls</a>
    </div>
    <div class="visual">
      <img src="assets/home-burger.png" alt="home__burger" />
      <img src="assets/home-dish.png" alt="home__dish" />
    </div>
  </div>
  <!-- End Hero Section -->

  <!-- How It Works -->
  <div class="section" id="how-it-works">
    <h2 class="secondary">How It Works</h2>

    <div class="container flex">
      <div class="box">
        <h3>Easy Order</h3>
        <ion-icon name="timer-outline"></ion-icon>
        <p>
          Lorem, ipsum dolor sit amet consectetur adipisicing elit. Aperiam,
          non?
        </p>
      </div>

      <div class="box active">
        <h3>Best Quality</h3>
        <ion-icon name="trophy-outline"></ion-icon>
        <p>
          Lorem, ipsum dolor sit amet consectetur adipisicing elit. Aperiam,
          non?
        </p>
      </div>

      <div class="box">
        <h3>Fast Delivery</h3>
        <ion-icon name="checkmark-done-circle-outline"></ion-icon>
        <p>
          Lorem, ipsum dolor sit amet consectetur adipisicing elit. Aperiam,
          non?
        </p>
      </div>
    </div>
  </div>
  <!-- End How It Works -->

  <!-- About -->
  <div class="section" id="about">
    <div class="container flex">
      <div class="visual">
        <img src="https://raw.githubusercontent.com/programmercloud/foodlover/main/img/about.png" alt="" />
      </div>
      <div class="text">
        <div class="secondary">About Our Application</div>
        <h2 class="primary">50+</h2>

        <h3 class="tertiary">Burger Stalls Signed Up</h3>

        <p>
          Join a growing community of burger stalls that have embraced our platform. From managing orders to connecting with hungry customers, our system helps streamline operations and boost visibility. Be part of the flavor revolution today!
        </p>

        <a href="<?php echo isset($_SESSION['user_name']) ? 'register_stall.php' : 'login.php'; ?>" class="btn">Register Stall</a>
      </div>
    </div>
  </div>
  <!-- End About -->

  <!-- Testimonial -->
  <div class="section" id="testimonial">
    <div class="container flex">
      <div class="text">
        <h2 class="secondary">What people say about FoodLover</h2>
        <p>
          Lorem ipsum dolor sit amet, consectetur adipisicing elit. Tempore,
          eos voluptatem odio, molestias ullam error dolor rem laboriosam illo
          quae odit aliquam sint a amet, autem natus! Praesentium, ipsam
          mollitia?
        </p>

        <div class="user flex">
          <img src="https://raw.githubusercontent.com/programmercloud/foodlover/main/img/client.jpg" alt="" />

          <div class="name">
            <div class="title">John Smith</div>
            <div class="location">Lahore, Pakistan</div>
          </div>
        </div>
      </div>
      <div class="visual">
        <img src="https://raw.githubusercontent.com/programmercloud/foodlover/main/img/testimonial.png" alt="" />
      </div>
    </div>
  </div>
  <!-- End Testimonial -->

  <!-- FAQ -->
  <div class="section" id="faq">
    <h2 class="secondary">Frequently Asked Questions</h2>

    <div class="faq">
      <details>
        <summary>I got wrong food what shoud I do?</summary>
        <div class="content">
          <p>
            Lorem ipsum dolor sit amet consectetur adipisicing elit.
            Asperiores amet sunt at?
          </p>
        </div>
      </details>

      <details>
        <summary>I got wrong food what shoud I do?</summary>
        <div class="content">
          <p>
            Lorem ipsum dolor sit amet consectetur adipisicing elit.
            Asperiores amet sunt at?
          </p>
        </div>
      </details>

      <details>
        <summary>I got wrong food what shoud I do?</summary>
        <div class="content">
          <p>
            Lorem ipsum dolor sit amet consectetur adipisicing elit.
            Asperiores amet sunt at?
          </p>
        </div>
      </details>

      <details>
        <summary>I got wrong food what shoud I do?</summary>
        <div class="content">
          <p>
            Lorem ipsum dolor sit amet consectetur adipisicing elit.
            Asperiores amet sunt at?
          </p>
        </div>
      </details>
    </div>
  </div>
  <!-- End FAQ -->

  <!-- App -->
  <div class="section" id="app">
    <div class="container flex">
      <div class="visual">
        <img src="https://raw.githubusercontent.com/programmercloud/foodlover/main/img/app.png" alt="" />
      </div>

      <div class="text">
        <h2 class="secondary">Download The FoodLover App</h2>
        <p>
          Lorem ipsum dolor sit amet consectetur adipisicing elit. Repudiandae
          laudantium velit iure illo facilis at delectus sint, doloribus
          magnam officiis rerum nobis, perspiciatis maxime repellat qui
          consequuntur? Aspernatur, architecto voluptatum!
        </p>

        <div class="download flex">
          <div class="app-store">
            <ion-icon name="logo-google-playstore"></ion-icon>
            <p>
              GET IT ON <br />
              <span>Google Play</span>
            </p>
          </div>

          <div class="app-store">
            <ion-icon name="logo-apple"></ion-icon>
            <p>
              Donload on the <br />
              <span>App Store</span>
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- End App -->

  <!-- Footer -->
  <div class="footer">
    <div class="container flex">
      <div class="footer-about">
        <h2>About</h2>
        <p>
          Lorem ipsum dolor sit amet consectetur adipisicing elit. Maxime
          aspernatur sit deleniti enim voluptas voluptatum incidunt rerum,
          exercitationem voluptate nemo quo impedit ad perspiciatis tempore
          nulla dolore fugit, fuga eos.
        </p>
      </div>

      <div class="footer-category">
        <h2>Our Menu</h2>

        <ul>
          <li>Biryani</li>
          <li>Chicken</li>
          <li>Pizza</li>
          <li>Burger</li>
          <li>Pasta</li>
        </ul>
      </div>

      <div class="quick-links">
        <h2>Quick Links</h2>

        <ul>
          <li>About Us</li>
          <li>Contact Us</li>
          <li>Menu</li>
          <li>Order</li>
          <li>Services</li>
        </ul>
      </div>

      <div class="get-in-touch">
        <h2>Get in touch</h2>
        <ul>
          <li>Account</li>
          <li>Support Center</li>
          <li>Feedback</li>
          <li>Suggession</li>
        </ul>
      </div>
    </div>

    <div class="copyright">
      <p>Copyright &copy; 2022. All Rights Reserved.</p>
    </div>
  </div>


  <!-- End Footer -->

  <!-- Ion Icons Js -->
  <script
    type="module"
    src="https://cdn.jsdelivr.net/npm/@ionic/core/dist/ionic/ionic.esm.js"></script>
  <script
    nomodule
    src="https://cdn.jsdelivr.net/npm/@ionic/core/dist/ionic/ionic.js"></script>
  <script>
    document.querySelectorAll('.dropdown-toggle').forEach(item => {
      item.addEventListener('click', event => {
        const dropdownMenu = item.nextElementSibling;
        dropdownMenu.style.display = dropdownMenu.style.display === 'block' ? 'none' : 'block';
      });
    });
  </script>
</body>

</html>