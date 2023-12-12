<?php session_start(); ?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="style.css">
    <title>Home Page</title>
  </head>
  <body>
    <header>
      <nav class="navbar navbar-expand-sm navbar-light bg-light">
        <ul class="navbar-nav">
          <li class="nav-item active"><a class="nav-link" href="index.php">Home</a></li>
          <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true): ?>
            <!-- Show Logout and Profile links for logged-in users -->
            <li class="nav-item active"><a class="nav-link" href="logout.php">Logout</a></li>
            <li class="nav-item active"><a class="nav-link" href="profile.php">Profile</a></li>
            <li class="nav-item active"><a class="nav-link" href="enrollment.php">Enroll to class</a></li>
          <?php else: ?>
            <li class="nav-item active"><a class="nav-link" href="login.php">Login</a></li>
            <li class="nav-item active"><a class="nav-link" href="registration.php">Registration</a></li>
          <?php endif; ?>
        </ul>
      </nav>
    </header>
    <main class="container">
      <h1 class="text-center">Course Registration Portal</h1>
    </main>
  </body>
</html>