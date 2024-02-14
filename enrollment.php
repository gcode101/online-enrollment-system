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
$studentID = $_SESSION['userID'];

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

function addToWaitlist($studentID, $selectedCourseID, $selectedSemester, $database, $con){
  $waitlistSql = "INSERT INTO tblWaitlist (userID, courseID, semester) VALUES ($studentID, $selectedCourseID, '$selectedSemester')";
  $waitlistResult = $database->executeQuery($con, $waitlistSql);
  if ($waitlistResult) {
      echo "You have been added. We will notify you once a spot becomes available.";
  } else {
      echo '<div class="alert alert-danger" role="alert">Error adding to waitlist.</div>';
  }
  exit;
}

//Check if student requested to be added to waitlist. 
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["action"])) {
    if ($_POST["action"] == "add_to_waitlist") {
        addToWaitlist($studentID, $_POST['course'], $_POST['semester'], $database, $con);
    }
}

// Check if a semester and course are selected for enrollment
if (isset($_POST['enroll'])) {
    if (isset($_POST['semester']) && isset($_POST['course'])) {
        $selectedSemester = $_POST['semester'];
        $selectedCourseID = $_POST['course'];

        $selectSql = "SELECT capacity, enrolled FROM tblCourse WHERE courseID = ?";
        $capEnroll = $database->executeSelectQuery($con, $selectSql, array("i", $selectedCourseID));

        $capacity = $capEnroll[0]['capacity'];
        $enrolled = $capEnroll[0]['enrolled'];
        $enrollResult = null;
        $studEnrolled = false;

        // Check if the course's capacity is not full
        if ($enrolled + 1 <= $capacity) {

            //Fetch enrollemnt list from the database based on the selected semester
            $sql = "SELECT * FROM tblEnrollment WHERE semester = ?";
            $enrollList = $database->executeSelectQuery($con, $sql, array("s", $selectedSemester));
            foreach ($enrollList as $row) {
              if ($row['userID'] == $studentID && $row['courseID'] == $selectedCourseID){
                $studEnrolled = true;
                break;
              }  
            }
            //Check if the student is not enrolled in the selected course
            if (!$studEnrolled){
              // Enroll the student in the selected course
              $enrollSql = "INSERT INTO tblEnrollment (userID, courseID, semester) VALUES ($studentID, $selectedCourseID,'$selectedSemester')";
              $enrollResult = $database->executeQuery($con, $enrollSql);

              // Add one more student to the enrolled column in tblCourse
              $enrolled += 1;
              $updateSql = "UPDATE tblCourse SET enrolled = $enrolled WHERE courseID = $selectedCourseID ";
              $totalResult = $database->executeQuery($con, $updateSql);
              if ($enrollResult) {
                  echo '<div class="alert alert-success" role="alert">Enrollment successful!</div>';
              } else {
                  echo '<div class="alert alert-danger" role="alert">Error enrolling in the course.</div>';
              }
            }else {
              echo '<div class="alert alert-danger" role="alert">Looks like you are already enrolled in this course for this semester.</div>';
            }
        } else {//Course capcity is full.

            //Fetch enrollemnt list from the database based on the selected semester
            $sql = "SELECT * FROM tblEnrollment WHERE semester = ?";
            $enrollList = $database->executeSelectQuery($con, $sql, array("s", $selectedSemester));
            foreach ($enrollList as $row) {
              if ($row['userID'] == $studentID && $row['courseID'] == $selectedCourseID){
                $studEnrolled = true;
                break;
              }  
            }

            //Check if the student is not enrolled in the selected course
            if (!$studEnrolled){

              $stuInWaitlist = false;
               //Fetch waitlist from the database based on the selected semester
              $sql = "SELECT * FROM tblWaitlist WHERE semester = ?";
              $waitlistSpots = $database->executeSelectQuery($con, $sql, array("s", $selectedSemester));

              foreach ($waitlistSpots as $row) {
                if ($row['userID'] == $studentID && $row['courseID'] == $selectedCourseID){
                $stuInWaitlist = true;
                break;
                } 
              }

              //Check if the student is already in the waitlist
              if ($stuInWaitlist){
                  echo '<div class="alert alert-danger" role="alert">You are already in the waitlist for this course.</div>';
              }else{

              ?>
                <script>
                    var choice = confirm("Looks like this course capacity is full. Would you like to be added to a waiting list?");
                    if (choice) {
                        var xmlhttp = new XMLHttpRequest();
                        xmlhttp.onreadystatechange = function () {
                            if (this.readyState == 4 && this.status == 200) {
                                alert(this.responseText);
                            }
                        };
                        var courseID = <?php echo isset($selectedCourseID) ? json_encode($selectedCourseID) : 'null'; ?>;
                        var semester = <?php echo isset($selectedSemester) ? json_encode($selectedSemester) : 'null'; ?>;

                        var data = "action=add_to_waitlist&course=" + encodeURIComponent(courseID) + "&semester=" + encodeURIComponent(semester);
                        xmlhttp.open("POST", "<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>", true);
                        xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                        xmlhttp.send(data);
                    }
                </script>
                <?php
              }
            }else {
              echo '<div class="alert alert-danger" role="alert">Looks like you are already enrolled in this course for this semester.</div>';
            }
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <title>Enrollment Page</title>
  </head>
  <body>
    <header>
      <nav class="navbar navbar-expand-sm navbar-light bg-light">
        <a class="navbar-brand mx-4" href="profile.php">
          <img src="images/nav-logo.png" width="30" height="30" class="d-inline-block align-top">
          Online Portal
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div id="navbarCollapse" class="collapse navbar-collapse">
          <ul class="navbar-nav">
            <li class="nav-item active"><a class="nav-link" href="profile.php">Home</a></li>
            <li class="nav-item active"><a class="nav-link" href="logout.php">Logout</a></li>
            <li class="nav-item active"><a class="nav-link" href="profile.php">Profile</a></li>
            <li class="nav-item active"><a class="nav-link" href="enrollment.php">Enroll to class</a></li>
          </ul>
        </div>    
      </nav>
    </header>
      <main>
        <div class="enrollment-title">
          <h1 class="text-center">Course Enrollment</h1>  
        </div>
<div class="container enrollment-form">
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <div data-toggle="buttons">
            <label class="btn btn-secondary <?php echo isset($_POST['semester']) && $_POST['semester'] === 'spring' ? 'active' : ''; ?>">
                <input type="radio" name="semester" value="spring" onchange="this.form.submit()" <?php echo isset($_POST['semester']) && $_POST['semester'] === 'spring' ? 'checked' : ''; ?>> Spring
            </label>
            <label class="btn btn-secondary <?php echo isset($_POST['semester']) && $_POST['semester'] === 'summer' ? 'active' : ''; ?>">
                <input type="radio" name="semester" value="summer" onchange="this.form.submit()" <?php echo isset($_POST['semester']) && $_POST['semester'] === 'summer' ? 'checked' : ''; ?>> Summer
            </label>
            <label class="btn btn-secondary <?php echo isset($_POST['semester']) && $_POST['semester'] === 'fall' ? 'active' : ''; ?>">
                <input type="radio" name="semester" value="fall" onchange="this.form.submit()" <?php echo isset($_POST['semester']) && $_POST['semester'] === 'fall' ? 'checked' : ''; ?>> Fall
            </label>
        </div>
        <br>
        <div class="form-group">
            <label for="course">Select Course:</label>
            <select name="course" id="course">
                <?php
                // Display courses for the selected semester
                foreach ($result as $row) {
                    echo "<option value=\"{$row['courseID']}\">{$row['title']}</option>";
                }
                ?>
            </select>
        </div>
        <button type="submit" name="enroll" class="btn btn-primary">Enroll</button>
    </form>
</div>
      	
      </main>
      <footer>
          <div class="footer-container">
              <p class="rights-reserved">© 2024 Guelmis Cortina. All Rights Reserved.</p>
          </div>
      </footer>
  </body>
</html>


	