<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

include("../connection.php");
include("../functions.php");

$lecturer_data = check_login($con, "lecturer");

// Check if the user is an admin; if not, redirect to the login page
if (empty($lecturer_data)) {
    header("Location: login.php");
    exit();
}

$id = $_SESSION['user_name'];

$query = "SELECT * FROM tbllecturer WHERE user_name = '$id' LIMIT 1";
$result = $con->query($query);
$num = $result->num_rows;
$select_row_result = $result->fetch_assoc();


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" integrity="sha512-xr6zWA5w4y7MN5QG2tGhOPQBGNEF/ytUuj9Gp4c5lR7TI7z5QVb3F5WOtMzFOjjjol5+8lJbGX7/MK0pb9I6DQ==" crossorigin="anonymous" />
    <link rel="stylesheet" type="text/css" href="styles.css">
    <title>Admin Dashboard</title>
</head>

<body>
    <!-- TopBar -->
    <?php include "lecturer_nav.php"; ?>
    <!-- Sidebar -->
    <?php include "lecturer_sidebar.php"; ?>
    <div id="content">
        <h1 style="color:blue">All Students</h1>
        <div id="box">

            <!-- students list -->
            <div class="row">
                <div class="">
                    <h3 style="color:blue">All Students in (<?php echo $select_row_result['course_name'] . ' - ' . $select_row_result['course_level']; ?>) course</h3>
                    <h6 class="m-0 font-weight-bold text-danger"></h6>
                </div>
                <div class="">
                </div>
                <div class="table-responsive">
                    <table class="table align-items-center table-flush table-hover" id="dataTableHover">
                        <thead class="thead-light">
                            <tr>
                                <th>#</th>
                                <th>First Name</th>
                                <th>Last Name</th>
                                <th>Middle Name</th>
                                <th>Registration No</th>
                                <th>Course Name</th>
                                <th>Course Type</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php
                            // $registration_no =  $_POST['registration_no'];

                            $query = "SELECT * FROM tblstudent
                            where tblstudent.course_name = '$select_row_result[course_name]' and tblstudent.course_level = '$select_row_result[course_level]'";

                            // $query = "SELECT * FROM tblstudent
                            //     WHERE tblstudent.registration_no = tblattendance.registration_no
                            //     WHERE tblstudent.course_name = '$select_row_result[course_name]' AND tblstudent.course_level = '$select_row_result[course_level]'";

                            $results = $con->query($query);
                            $num = $results->num_rows;
                            $index = 0;
                            $status = "";
                            if ($num > 0) {
                                while ($rows = $results->fetch_assoc()) {
                                    $index = $index + 1;
                                    echo "
                                    <tr>
                                    <td>" . $index . "</td>
                                    <td>" . $rows['first_name'] . "</td>
                                    <td>" . $rows['last_name'] . "</td>
                                    <td>" . $rows['middle_name'] . "</td>
                                    <td>" . $rows['registration_no'] . "</td>
                                    <td>" . $rows['course_name'] . "</td>
                                    <td>" . $rows['course_level'] . "</td>
                                    
                                    </tr>";
                                }
                            } else {
                                echo "
                                <tr>
                                <td colspan='10'>
                                <div class='alert alert-danger' role='alert'>
                                No Record Found!
                                </div>
                                </td>
                                </tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
        <script src="https://kit.fontawesome.com/4755bfc200.js" crossorigin="anonymous"></script>
</body>

</html>