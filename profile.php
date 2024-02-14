<?php
include("db_handler.php");
require_once('config.php');
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

  $database = new DatabaseHandler(DBHOST, DBUSER, DBPASS, DBNAME);
  $con = $database->connect();

  if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
      header("Location: login.php");
      exit;
  }

  $email = $_SESSION['email']; 
  $firstName = $_SESSION['firstName'];
  $lastName = $_SESSION['lastName']; 
  $address = $_SESSION['address']; 
  $phone = $_SESSION['phone'];
  $studentID = $_SESSION['userID'];

  // Fetch enrolled courses for the student
  $enrolledCoursesSql = "SELECT c.courseID, c.title, c.semester FROM tblCourse c
                        JOIN tblEnrollment e ON c.courseID = e.courseID
                        WHERE e.userID = ?";
  $enrolledCourses = $database->executeSelectQuery($con, $enrolledCoursesSql, array("i", $studentID));

  //Check if unenroll is clicked and the student is enrolled in some courses
  if (isset($_POST['unenroll']) && !empty($enrolledCourses)) {  
    $courseIDToDelete = $_POST['course'];

    $deleteEnrollmentSql = "DELETE FROM tblEnrollment WHERE userID = $studentID AND courseID = $courseIDToDelete";
    $unenrollResult = $database->executeQuery($con, $deleteEnrollmentSql);

    $updateCapacitySql = "UPDATE tblCourse SET enrolled = enrolled - 1 WHERE courseID = $courseIDToDelete";
    $database->executeQuery($con, $updateCapacitySql);

    //Check if the course was successfully unenrolled
    if ($unenrollResult) {
        echo '<div class="alert alert-success" role="alert">Class was successfully unenrolled!</div>';

        //Fetch waitlist from the database
        $waitlistSql = "SELECT * FROM tblWaitlist WHERE courseID = ?";
        $waitlist = $database->executeSelectQuery($con, $waitlistSql, array("s", $courseIDToDelete));

        //Check if there are students in the waitlist for the selected course
        if(!empty($waitlist)){
          $studInListID = $waitlist[0]['userID'];
          $courseInListID = $waitlist[0]['courseID'];
          $semesterInList = $waitlist[0]['semester'];

          // Enroll the student in the selected course
          $enrollSql = "INSERT INTO tblEnrollment (userID, courseID, semester) VALUES ($studInListID, $courseInListID,'$semesterInList')";
          $enrollResult = $database->executeQuery($con, $enrollSql);

          //Check if enrollment was successfull. Then delete student from waitlist
          if ($enrollResult){
            $deleteEnrollmentSql = "DELETE FROM tblWaitlist WHERE userID = $studInListID";
            $deleteResult = $database->executeQuery($con, $deleteEnrollmentSql);

            $updateCapacitySql = "UPDATE tblCourse SET enrolled = enrolled + 1 WHERE courseID = $courseInListID";
            $database->executeQuery($con, $updateCapacitySql);
          }
        }
    } else {
        echo '<div class="alert alert-danger" role="alert">Error unenrolling in the course.</div>';
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
    <title>Welcome Page</title>
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
        <div class="profile-title">
          <h2>Student Profile</h2>
        </div>
        <div class="container-fluid profile-container">
          <div class="row row-container">
            <div class="profile col-6-lg col-md">
              <div class="profile-info">
                <label>First Name:</label>
                <span><?php echo $firstName; ?></span>
              </div>
              <div class="profile-info">
                <label>Last Name:</label>
                <span><?php echo $lastName; ?></span>
              </div>
              <div class="profile-info">
                <label>Email:</label>
                <span><?php echo $email; ?></span>
              </div>
              <div class="profile-info">
                <label>Address:</label>
                <span><?php echo $address; ?></span>
              </div>
              <div class="profile-info">
                <label>Phone:</label>
                <span><?php echo $phone; ?></span>
              </div>
            </div>
            <div class="courses col-6-lg col-md">
              <div class="enrolled-courses">
                  <h2>Enrolled Courses</h2>

                  <?php
                    if (!empty($enrolledCourses)) {
                      echo "<ul>";
                      foreach ($enrolledCourses as $course) {
                        echo "<li>{$course['title']} - Semester: {$course['semester']}</li>";
                      }
                      echo "</ul>";
                    } else {
                      echo "<p>No enrolled courses found.</p>";
                    }
                  ?>
              </div>
              <div class="course-unenrollment">
                  <h2>Course Unenrollment</h2>
                  <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                      <label for="course">Select Course:</label>
                      <select name="course" id="course">
                          <?php
                          // Display courses for the selected semester
                          foreach ($enrolledCourses as $row) {
                              echo "<option value=\"{$row['courseID']}\">{$row['title']} - Semester: {$row['semester']}</option>";
                          }
                          ?>
                      </select>
                      <button type="submit" name="unenroll" class="btn btn-primary">Unenroll</button>
                  </form>
              </div>  
            </div>
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