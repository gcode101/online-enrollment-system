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


  if (isset($_POST['unenroll']) && !empty($enrolledCourses)) {  
    $courseIDToDelete = $_POST['course'];

    $deleteEnrollmentSql = "DELETE FROM tblEnrollment WHERE userID = $studentID AND courseID = $courseIDToDelete";


    $unenrollResult = $database->executeQuery($con, $deleteEnrollmentSql);

    if ($unenrollResult) {
        echo '<div class="alert alert-success" role="alert">Class was successfully unenrolled!</div>';
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
    <title>Welcome Page</title>
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
    <main>
        <div class="profile">
          <h2>Student Profile</h2>
          <div class="profile-info">
            <label>Email:</label>
            <span><?php echo $email; ?></span>
          </div>
          <div class="profile-info">
            <label>First Name:</label>
            <span><?php echo $firstName; ?></span>
          </div>
          <div class="profile-info">
            <label>Last Name:</label>
            <span><?php echo $lastName; ?></span>
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
    </main>
  </body>
</html>