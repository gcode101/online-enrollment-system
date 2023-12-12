<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="style.css">
    <title>Registration Page</title>
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
        <h2 class="mb-4">Registration Form</h2>
        <div class="mb-3">
          <label for="email" class="form-label">Email address</label>
          <input type="email" placeholder="email" class="form-control" name="email" aria-describedby="emailHelp">
        </div>
        <div class="mb-3">
          <label for="password" class="form-label">Password</label>
          <input type="password" class="form-control" name="password">
        </div>
        <div class="mb-3">
          <label for="firstName" class="form-label">First Name</label>
          <input type="text" class="form-control" name="firstName">
        </div>
        <div class="mb-3">
          <label for="lastName" class="form-label">Last Name</label>
          <input type="text" class="form-control" name="lastName">
        </div>
        <div class="mb-3">
          <label for="address" class="form-label">Address</label>
          <input type="text" class="form-control" name="address">
        </div>
        <div class="mb-3">
          <label for="phone" class="form-label">Phone</label>
          <input type="tel" class="form-control" name="phone">
        </div>
        <button type="submit" name="submit" class="btn btn-primary">Submit</button>
      </form>
    </main>
  </body>
</html>

<?PHP

  include("db_handler.php");
  require_once('config.php');

  $database = new DatabaseHandler(DBHOST, DBUSER, DBPASS, DBNAME);

  $con = $database->connect();
  

  if($_SERVER["REQUEST_METHOD"] == "POST"){
    $email = filter_input(INPUT_POST, "email");
    $password = filter_input(INPUT_POST, "password");
    $firstName = filter_input(INPUT_POST, "firstName");
    $lastName = filter_input(INPUT_POST, "lastName");
    $address = filter_input(INPUT_POST, "address");
    $phone = filter_input(INPUT_POST, "phone");


    if(empty($email)){
      echo "Email is required";
    } elseif (empty($password)) {
      echo "Password is required";
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
          echo "Error inserting user.";
        }
        
    }
  }
  $database->close($con);

?>