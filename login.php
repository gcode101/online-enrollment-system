<?php
  include("db_handler.php");
  require_once('config.php');

  session_start();

  $database = new DatabaseHandler(DBHOST, DBUSER, DBPASS, DBNAME);
  $con = $database->connect();
  $errors = array();

  if ($_SERVER["REQUEST_METHOD"] == "POST") {
      $email = filter_input(INPUT_POST, "email");
      $password = filter_input(INPUT_POST, "password");

      if (empty($email) || empty($password)) {
          $errors[] = "Invalid email or password.";
      } else {
          $sql = "SELECT * FROM tblUser WHERE email = '$email'";
          $result = $database->executeSelectQuery($con, $sql);

          if ($result && count($result) > 0) {
              $student = $result[0];
              $storedPassword = $student['password'];

              if (password_verify($password, $storedPassword)) {
                  $_SESSION['loggedin'] = true;
                  $_SESSION['userID'] = $student['userID'];
                  $_SESSION['email'] = $student['email'];
                  $_SESSION['firstName'] = $student['firstName'];
                  $_SESSION['lastName'] = $student['lastName'];
                  $_SESSION['address'] = $student['address'];
                  $_SESSION['phone'] = $student['phone'];
                  header("Location: profile.php");
                  exit();
              } else {
                  $errors[] = "Invalid password.";
              }
          } else {
              $errors[] = "Student not found.";
          }
      }
  }
  $database->close($con);

?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <title>Login Page</title>
  </head>
  <body>
    <header>
      <nav class="navbar navbar-expand-sm navbar-light bg-light">
        <a class="navbar-brand mx-4" href="index.php">
          <img src="images/nav-logo.png" width="30" height="30" class="d-inline-block align-top">
          Online Portal
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div id="navbarCollapse" class="collapse navbar-collapse">
          <ul class="navbar-nav">
            <li class="nav-item active"><a class="nav-link" href="index.php">Home</a></li>
            <li class="nav-item active"><a class="nav-link" href="login.php">Login</a></li>
            <li class="nav-item active"><a class="nav-link" href="registration.php">Signup</a></li>
          </ul>
        </div>
      </nav>
    </header>
    
    <main class="main-login">
      <div class="container d-flex justify-content-center">
        <div class="login-container">
          <form action="<?PHP htmlspecialchars($_SERVER["PHP_SELF"]) ?>" method="post">
            
            <h2 class="mb-4 text-light">Log In</h2>
              <div class="mb-3" class="login-form">
                <label for="email" class="form-label text-light">Email address</label>
                <input type="email" class="form-control input-box" name="email">
              </div>
              <div class="mb-3">
                <label for="password" class="form-label text-light">Password</label>
                <input type="password" class="form-control input-box" name="password">
              </div>
            <button type="submit" name="submit" class="btn btn-primary">Login</button>
          </form>
          <div id="error-container"></div>
          <script>
            <?php if (!empty($errors)): ?>
              var errorContainer = document.getElementById('error-container');
              errorContainer.innerHTML = '<ul>';
              <?php foreach ($errors as $error): ?>
                errorContainer.innerHTML += '<li class="error-msg"><?php echo $error; ?></li>';
              <?php endforeach; ?>
              errorContainer.innerHTML += '</ul>';
            <?php endif; ?>
          </script>
        </div>        
      </div>
    </main>
    <footer>
        <div class="footer-container">
            <p class="rights-reserved">© 2024 Guelmis Cortina. All Rights Reserved.</p>
        </div>
    </footer>
  </body>
</html>