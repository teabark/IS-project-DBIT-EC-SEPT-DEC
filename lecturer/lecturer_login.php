<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

include("../connection.php");
include("../functions.php");

$lecturer_data = check_login($con, "lecturer");

// Check if the user is a lecturer; if not, redirect to the login page
if (empty($lecturer_data)) {
    header("Location: login.php");
    exit();
}

$id = $_SESSION['user_name'];

$query = "SELECT * FROM tbllecturer WHERE user_name = '$id' LIMIT 1";
$result = $con->query($query);
$num = $result->num_rows;
$select_row_result = $result->fetch_assoc();

$students_query = mysqli_query($con, "SELECT * from tblstudent where course_name = '$select_row_result[course_name]' and course_level = '$select_row_result[course_level]'");
$students = mysqli_num_rows($students_query);


$class_query = mysqli_query($con, "SELECT * from tbllecture");
$class = mysqli_num_rows($class_query);

$course_query = mysqli_query($con, "SELECT * from tbllecture");
$courseTypes = mysqli_num_rows($course_query);

$student_attendance = mysqli_query($con, "SELECT * from tblattendance where course_name = '$select_row_result[course_name]' and course_level = '$select_row_result[course_level]' and status = '1'");
$presentAttendance = mysqli_num_rows($student_attendance);

$student_absent_attendance = mysqli_query($con, "SELECT * from tblattendance where course_name = '$select_row_result[course_name]' and course_level = '$select_row_result[course_level]' and status = '0'");
$absentAttendance = mysqli_num_rows($student_absent_attendance);

?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="styles.css">
    <title>Lecturer Dashboard</title>
</head>

<body>
    <!-- TopBar -->
    <?php include "lecturer_nav.php"; ?>
    <!-- Sidebar -->
    <?php include "lecturer_sidebar.php"; ?>
    <div id="content">

        <h1>Welcome to the Lecturer Dashboard</h1>

        <div class="col mr-2">
            <?php echo "Students: " . $students; ?>
        </div>
        <div class="col mr-2">
            <?php echo "Classes: " . $class; ?>
        </div>

        <div class="col mr-2">
            <?php echo "Course Types: " . $courseTypes; ?>
        </div>

        <div class="col mr-2">
            <?php echo "Present Attendance: " . $presentAttendance; ?>
        </div>

        <div class="col mr-2">
            <?php echo "Absent Attendance: " . $absentAttendance; ?>
        </div>

    </div>
    </div>

    <br>



</body>

</html>
