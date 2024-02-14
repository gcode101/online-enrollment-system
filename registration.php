<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <title>Registration Page</title>
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
  
    <main class="main-container">
      <div class="form-container">
        <div class="form-inputs">
          <form action="<?PHP htmlspecialchars($_SERVER["PHP_SELF"]) ?>" method="post">
            <h2 class="mb-4">Sign Up</h2>
            <div class="mb-3">
              <label for="email" class="form-label">Email address</label>
              <input type="email" class="input-box form-control" name="email" aria-describedby="emailHelp">
            </div>
            <div class="mb-3">
              <label for="password" class="form-label">Password</label>
              <input type="password" class="input-box form-control" name="password">
            </div>
            <div class="mb-3">
              <label for="firstName" class="form-label">First Name</label>
              <input type="text" class="input-box form-control" name="firstName">
            </div>
            <div class="mb-3">
              <label for="lastName" class="form-label">Last Name</label>
              <input type="text" class="input-box form-control" name="lastName">
            </div>
            <div class="mb-3">
              <label for="address" class="form-label">Address</label>
              <input type="text" class="input-box form-control" name="address">
            </div>
            <div class="mb-3">
              <label for="phone" class="form-label">Phone</label>
              <input type="tel" class="input-box form-control" name="phone">
            </div>
            <div class="submit-button">
              <button type="submit" name="submit" class="btn btn-primary">Submit</button>
            </div>
          </form>
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

<?PHP

  include("db_handler.php");
  require_once('config.php');

  $database = new DatabaseHandler(DBHOST, DBUSER, DBPASS, DBNAME);

  $con = $database->connect();

  $errors = array();
  

  if($_SERVER["REQUEST_METHOD"] == "POST"){
    $email = filter_input(INPUT_POST, "email");
    $password = filter_input(INPUT_POST, "password");
    $firstName = filter_input(INPUT_POST, "firstName");
    $lastName = filter_input(INPUT_POST, "lastName");
    $address = filter_input(INPUT_POST, "address");
    $phone = filter_input(INPUT_POST, "phone");


    if(empty($email)){
      $errors[] = "Email is required";
    } elseif (empty($password)) {
      $errors[] = "Password is required";
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO tblUser (email, password, firstName, lastName, address, phone)
              VALUES ('$email', '$hash', '$firstName', '$lastName', '$address', '$phone')";

        $resultInsert = $database->executeQuery($con, $sql);
        
        if ($resultInsert){
          echo '<script>
                  alert("Registration successful. Please login."); 
                  window.location.href = "login.php";
                </script>';
        } else {
          $errors[] = "Error inserting user.";
        }
        
    }
  }
  $database->close($con);

?>