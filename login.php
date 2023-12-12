<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="style.css">
    <title>Login Page</title>
  </head>
  <body>
    <header>
      <nav class="navbar navbar-expand-sm navbar-light bg-light">
        <ul class="navbar-nav">
          <li class="nav-item active"><a class="nav-link" href="index.php">Home</a></li>
          <li class="nav-item active"><a class="nav-link" href="login.php">Login</a></li>
          <li class="nav-item active"><a class="nav-link" href="registration.php">Registration</a></li>
        </ul>
      </nav>
    </header>
  
    <main class="container">
      <form action="<?PHP htmlspecialchars($_SERVER["PHP_SELF"]) ?>" method="post">
        <h2 class="mb-4">Log In</h2>
          <div class="mb-3" class="login-form">
            <label for="email" class="form-label">Email address</label>
            <input type="email" class="form-control" name="email">
          </div>
          <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" name="password">
          </div>
        <button type="submit" name="submit" class="btn btn-primary">Login</button>
      </form>
    </main>
  </body>
</html>

<?php
  include("db_handler.php");
  require_once('config.php');

  session_start();

  $database = new DatabaseHandler(DBHOST, DBUSER, DBPASS, DBNAME);
  $con = $database->connect();

  if ($_SERVER["REQUEST_METHOD"] == "POST") {
      $email = filter_input(INPUT_POST, "email");
      $password = filter_input(INPUT_POST, "password");

      if (empty($email) || empty($password)) {
          echo "Invalid email or password.";
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
                  echo "Invalid password.";
              }
          } else {
              echo "Student not found.";
          }
      }
  }
  $database->close($con);

?>