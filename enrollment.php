<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


  include("db_handler.php");
  require_once('config.php');
  session_start();

  if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
      header("Location: login.php");
      exit;
  }

  // Create a connection
  $database = new DatabaseHandler(DBHOST, DBUSER, DBPASS, DBNAME);
  $con = $database->connect();


  // Check if a semester is selected
  if (isset($_POST['semester'])) {
      $selectedSemester = $_POST['semester'];

      // Fetch courses from the database based on the selected semester
      $sql = "SELECT * FROM tblCourse WHERE semester = ?";
      $result = $database->executeSelectQuery($con, $sql, array("s", $selectedSemester));
    }

  // Check if a semester and course are selected for enrollment
  if (isset($_POST['enroll'])) {  
    if (isset($_POST['semester']) && isset($_POST['course'])) {
        $selectedSemester = $_POST['semester'];
        $selectedCourseID= $_POST['course'];

        $studentID = $_SESSION['userID'];

        //Enroll the student in the selected course
        $enrollSql = "INSERT INTO tblEnrollment (userID, courseID, semester) VALUES ($studentID, $selectedCourseID,'$selectedSemester')";
        
        $enrollResult = $database->executeQuery($con, $enrollSql);

        if ($enrollResult) {
            echo '<div class="alert alert-success" role="alert">Enrollment successful!</div>';
        } else {
            echo '<div class="alert alert-danger" role="alert">Error enrolling in the course.</div>';
        }
    }
  }

  // Close the database connection
  $database->close($con);
 ?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="style.css">
    <title>Enrollment Page</title>
  </head>
  <body>
    <header>
      <nav class="navbar navbar-expand-sm navbar-light bg-light">
        <ul class="navbar-nav">
          <li class="nav-item active"><a class="nav-link" href="index.php">Home</a></li>
          <li class="nav-item active"><a class="nav-link" href="logout.php">Logout</a></li>
            <li class="nav-item active"><a class="nav-link" href="profile.php">Profile</a></li>
            <li class="nav-item active"><a class="nav-link" href="enrollment.php">Enroll to class</a></li>
        </ul>
      </nav>
    </header>
      <main class="container">
      	<h1 class="text-center">Course Enrollment</h1>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
          <label for="semester">Select Semester:</label>
          <select name="semester" id="semester" onchange="this.form.submit()">
              <option value="" <?php echo !isset($_POST['semester']) ? 'selected' : ''; ?>>Select</option>
              <option value="spring" <?php echo isset($_POST['semester']) && $_POST['semester'] === 'spring' ? 'selected' : ''; ?>>Spring</option>
              <option value="summer" <?php echo isset($_POST['semester']) && $_POST['semester'] === 'summer' ? 'selected' : ''; ?>>Summer</option>
              <option value="fall" <?php echo isset($_POST['semester']) && $_POST['semester'] === 'fall' ? 'selected' : ''; ?>>Fall</option>
          </select>
          <br>
            <label for="course">Select Course:</label>
            <select name="course" id="course">
                <?php
                // Display courses for the selected semester
                foreach ($result as $row) {
                    echo "<option value=\"{$row['courseID']}\">{$row['title']}</option>";
                }
                ?>
            </select>
            <button type="submit" name="enroll" class="btn btn-primary">Enroll</button>
        </form>
      </main>
  </body>
</html>


	