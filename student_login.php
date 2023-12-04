<?php
session_start();

include("connection.php");
include("functions.php");

// $user_data = check_login($con);
$student_data = check_login($con, "student");

// Check if the user is an admin; if not, redirect to the login page
if (empty($student_data)) {
    header("Location: login.php");
    exit();
}

?>

<!DOCTYPE html>
<html>

<head>
    <title>Student Dashboard</title>
</head>

<body>

    <a href="logout.php">Logout</a>
    <h1>Welcome to the Student Dashboard</h1>

    <br>
    Hello, <?php echo $student_data['first_name']; ?>
</body>

</html>