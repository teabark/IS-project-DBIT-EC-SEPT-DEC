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


$dateTaken = date("Y-m-d");

$qurty = mysqli_query($con, "SELECT * FROM tblattendance WHERE course_name = '$select_row_result[course_name]' AND course_level = '$select_row_result[course_level]' AND dateTaken = '$dateTaken'");
$count = mysqli_num_rows($qurty);

if ($count == 0) { 
    //if Record does not exist, insert the new record
    //insert the students' records into the attendance table on page load
    $qus = mysqli_query($con, "SELECT * FROM tblstudent WHERE course_name = '$select_row_result[course_name]' AND course_level = '$select_row_result[course_level]'");

    while ($ros = $qus->fetch_assoc()) {
        $qquery = mysqli_query($con, "INSERT INTO tblattendance (registration_no, course_name, course_level, status, dateTaken) 
        VALUES ('$ros[registration_no]', '$select_row_result[course_name]', '$select_row_result[course_level]', '0', '$dateTaken')");
    }

    if (!$qquery) {
        // Display an error message and the MySQL error, if any
        echo "Error: " . mysqli_error($con);
    }
}


if (isset($_POST['save'])) {

    $registration_no = $_POST['registration_no'];

    // Check if 'check' key exists in $_POST
    $check = isset($_POST['check']) ? $_POST['check'] : [];

    $N = count($registration_no);
    $status = "";

    //check if the attendance has not been taken i.e if no record has a status of 1

    $qurty = mysqli_query($con, "SELECT * FROM tblattendance WHERE course_name = '$select_row_result[course_name]' AND course_level = '$select_row_result[course_level]' AND date='$dateTaken' AND status = '1'");

    $count = mysqli_num_rows($qurty);

    if ($count > 0) {

        $statusMsg = "<div class='alert alert-danger' style='margin-right:700px;'>Attendance has been taken for today!</div>";
    } else //update the status to 1 for the checkboxes checked
    {

        mysqli_begin_transaction($con);
        for ($i = 0; $i < $N; $i++) {
            $registration_no[$i]; //admission Number

            if (isset($check[$i])) //the checked checkboxes
            {

                $qquery = mysqli_query($con, "UPDATE tblattendance SET status='1' WHERE registration_no = '$check[$i]'");

                // Debugging statement
                echo "SQL Query: UPDATE tblattendance SET status='1' WHERE registration_no = '$check[$i]'";

                if ($qquery) {
                    $statusMsg = "<div class='alert alert-success'  style='margin-right:700px;'>Attendance Taken Successfully!</div>";
                } else {
                    $statusMsg = "<div class='alert alert-danger' style='margin-right:700px;'>An error Occurred! " . mysqli_error($con) . "</div>";
                }
            }
        }

        mysqli_commit($con);
    }
}
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
        <h1 style="color:blue">Record Attendance</h1>
        <div id="box">

            <form method="post">
                <div class="row">
                    <div class="">
                        <h3 style="color:blue">All Students in (<?php echo $select_row_result['course_name'] . ' - ' . $select_row_result['course_level']; ?>) course</h3>
                        <h6 class="m-0 font-weight-bold text-danger"></h6>
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
                                    <th>Check Attendance</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php
                                $query = "SELECT id, first_name, last_name, middle_name, user_type, course_name, course_level, registration_no, password, date FROM tblstudent WHERE tblstudent.course_name = ? AND tblstudent.course_level = ?";

                                $stmt = $con->prepare($query);

                                // Check if the preparation succeeded
                                $index = 0;
                                if ($stmt) {
                                    // Bind parameters
                                    $stmt->bind_param("ss", $select_row_result['course_name'], $select_row_result['course_level']);

                                    // Execute the statement
                                    $stmt->execute();

                                    // Get the result set
                                    $results = $stmt->get_result();

                                    // Fetch data as needed
                                    while ($rows = $results->fetch_assoc()) {
                                        // Your code here
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
                                <td><input name='check[]' type='checkbox' value=" . $rows['registration_no'] . " class='form-control'></td>
                                </tr>";
                                        echo "<input name='registration_no[]' value=" . $rows['registration_no'] . " type='hidden' class='form-control'>";
                                    }

                                    // Close the statement
                                    $stmt->close();
                                } else {
                                    // Handle the case where the preparation failed
                                    echo "
                                    <tr>
                                    <td colspan='10'>
                                    <div class='alert alert-danger' role='alert'>
                                    No Record Found!
                                    </div>
                                    </td>
                                    </tr>";
                                    die('Error in preparing SQL statement: ' . $con->error);
                                }
                                ?>
                            </tbody>
                        </table>
                        <br>
                        <input id="button" type="submit" name="save" value="Submit Attendance"><br><br>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <script src="https://kit.fontawesome.com/4755bfc200.js" crossorigin="anonymous"></script>
</body>

</html>