<?php session_start(); ?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <title>Home Page</title>
  </head>
  <body>
  <header>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
      <div class="container-fluid">
        <a class="navbar-brand mx-4" href="index.php">
          <img src="images/nav-logo.png" width="30" height="30" class="d-inline-block align-top" alt="Logo">
          Online Portal
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div id="navbarCollapse" class="collapse navbar-collapse">
          <ul class="navbar-nav">
            <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
            <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true): ?>
              <!-- Show Logout and Profile links for logged-in users -->
              <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
              <li class="nav-item"><a class="nav-link" href="profile.php">Profile</a></li>
              <li class="nav-item"><a class="nav-link" href="enrollment.php">Enroll to class</a></li>
            <?php else: ?>
              <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
              <li class="nav-item"><a class="nav-link" href="registration.php">Signup</a></li>
            <?php endif; ?>
          </ul>
        </div>
      </div>
    </nav>
  </header>
    <main class="home-section">
      <div class="home-title">
        <h1 class="text-center">Course Registration Portal</h1>
      </div>
      <div class="container d-flex justify-content-center align-items-center">
        <div class="buttons">
          <a href="login.php" id="login-button" class="btn btn-primary">Log In</a>
          <a href="registration.php" class="btn btn-outline-secondary">Sign Up</a>
        </div>
      </div>
      <div class="bottom-img">
      </div>
    </main>
    <footer>
        <div class="footer-container">
            <p class="rights-reserved">© 2024 Guelmis Cortina. All Rights Reserved.</p>
        </div>
    </footer>
  </body>
</html>