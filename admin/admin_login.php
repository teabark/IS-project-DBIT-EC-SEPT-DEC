<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

include("../connection.php");
include("../functions.php");

// $user_data = check_login($con);
$admin_data = check_login($con, "admin");

// Check if the user is an admin; if not, redirect to the login page
if (empty($admin_data)) {
    header("Location: login.php");
    exit();
}

$id = $_SESSION['user_id'];

$query = "SELECT * FROM tbladmin WHERE id = '$id' LIMIT 1";
$result = $con->query($query);
$num = $result->num_rows;
$select_row_result = $result->fetch_assoc();

$students_query = mysqli_query($con, "SELECT * from tblstudent");
$students = mysqli_num_rows($students_query);


$class_query = mysqli_query($con, "SELECT * from tbllecture");
$class = mysqli_num_rows($class_query);

$course_query = mysqli_query($con, "SELECT * from tbllecture");
$courseTypes = mysqli_num_rows($course_query);

$student_attendance = mysqli_query($con, "SELECT * from tblattendance where status = '1'");
$presentAttendance = mysqli_num_rows($student_attendance);

$student_absent_attendance = mysqli_query($con, "SELECT * from tblattendance where status = '0'");
$absentAttendance = mysqli_num_rows($student_absent_attendance);

$total_attendance = mysqli_query($con, "SELECT * from tblattendance where status = '0' or status = '1'");
$totalAttendance = mysqli_num_rows($total_attendance);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="styles.css">
    <title>Admin Dashboard</title>
</head>

<body>
    <!-- TopBar -->
    <?php include "admin_nav.php"; ?>
    <!-- Sidebar -->
    <?php include "admin_sidebar.php"; ?>
    <div id="content">
        <h1>Welcome to the Admin Dashboard</h1>

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
            <?php echo ", percentage: " . $presentAttendance*100/ $totalAttendance . "%"; ?>
        </div>

        <div class="col mr-2">
            <?php echo "Absent Attendance: " . $absentAttendance; ?>
            <?php echo ", percentage: " . $absentAttendance*100/ $totalAttendance . "%"; ?>
        </div>
    </div>

    <br>
</body>

</html>
