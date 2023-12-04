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
        <h1 style="color:blue">View Lecture Attendance</h1>
        <div id="box">

            <form method="post">
                <div class="form-group">
                    <div style="display: flex; gap: 20px;">
                        <div style="flex: 1;">
                            <label for="exampleInputFirstName">Select Date<span class="text-danger ml-2">*</span></label>
                            <input type="date" class="form-control" name="dateTaken" id="exampleInputFirstName" required>
                        </div>
                    </div>
                </div>
                <input id="button" type="submit" name="view" value="View Attendance"><br><br>
            </form>
        </div>





        <!-- students list -->
        <div class="row">
            <div class="">
                <h3 style="color:blue">Lecture attendance</h3>
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
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php
                        // Check if dateTaken is set
                        if (isset($_POST['dateTaken'])) {
                            $dateTaken = $_POST['dateTaken'];


                            $query = "SELECT * FROM tblattendance
                        INNER JOIN tblstudent ON tblstudent.registration_no = tblattendance.registration_no
                        WHERE tblattendance.dateTaken = '$dateTaken' AND tblattendance.course_name = '$select_row_result[course_name]' AND tblattendance.course_level = '$select_row_result[course_level]'";

                            $results = $con->query($query);

                            $num = $results->num_rows;
                            $index = 0;
                            $status = "";
                            if ($num > 0) {
                                while ($rows = $results->fetch_assoc()) {
                                    $status = ($rows['status'] == '1') ? "Present" : "Absent";
                                    $colour = ($rows['status'] == '1') ? "#00FF00" : "#FF0000";
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
                            <td style='background-color:" . $colour . "'>" . $status . "</td>
                            <td>" . $rows['dateTaken'] . "</td>
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
                        } else {
                            echo "Please select a date.";
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