<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

include("../connection.php");
include("../functions.php");

$admin_data = check_login($con, "admin");

// Check if the user is an admin; if not, redirect to the login page
if (empty($admin_data)) {
    header("Location: login.php");
    exit();
}

// Sample array with class data
$courseData = array(
    array('id' => "dbit001", 'courseName' => 'DBIT'),
    array('id' => "llb401", 'courseName' => 'LLB'),
    array('id' => "bcom001", 'courseName' => 'BComm'),
    // Add more classes as needed
);

$courseLevelData = array(
    array('id' => "diploma", 'courseLevelName' => 'Diploma'),
    array('id' => "1st year", 'courseLevelName' => 'Degree year 1'),
    array('id' => "2nd year", 'courseLevelName' => 'Degree year 2'),
    array('id' => "3rd year", 'courseLevelName' => 'Degree year 3'),
    array('id' => "4th year", 'courseLevelName' => 'Degree year 4'),
    // Add more classes as needed
);

// if ($_SERVER['REQUEST_METHOD'] == "POST") {
if (isset($_POST['save'])) {
    //something was posted
    $course_name = $_POST['course_name'];
    $course_code = $_POST['course_code'];

    if (!empty($course_name) && !empty($course_code)) {

        //save to database

        $query = "INSERT INTO tbllecture (course_name, course_code) VALUES ('$course_name', '$course_code')";


        mysqli_query($con, $query);

        header("Location: lecture.php");
        die;
    } else {
        echo "Please enter some valid information!";
    }
}

// delete
if (isset($_GET['id']) && isset($_GET['action']) && $_GET['action'] == "delete") {
    $id = $_GET['id'];

    $query = mysqli_query($con, "DELETE FROM tbllecture WHERE id='$id'");

    if ($query == TRUE) {

        echo "<script type = \"text/javascript\">
              window.location = (\"lecture.php\")
              </script>";
    } else {

        $statusMsg = "<div class='alert alert-danger' style='margin-right:700px;'>An error Occurred!</div>";
    }
}

// edit

if (isset($_GET['id']) && isset($_GET['action']) && $_GET['action'] == "edit") {
    $id = $_GET['id'];

    $query = mysqli_query($con, "select * from tbllecture where id ='$id'");
    $row = mysqli_fetch_array($query);

    // Check if the form is submitted for an update
    if (isset($_POST['update'])) {

        // Check if $row is set before using it
        if (isset($row)) {

            // Collect updated data
            $courseName = isset($_POST['course_name']) ? $_POST['course_name'] : $row['course_name'];
            $courseCode = isset($_POST['course_code']) ? $_POST['course_code'] : $row['course_code'];

            // Use prepared statements to prevent SQL injection
            $stmt = $con->prepare("UPDATE tbllecture SET course_name=?, course_code=? WHERE id=?");

            // Bind parameters
            $stmt->bind_param("ssi", $courseName, $courseCode, $id);

            // Execute the statement
            if ($stmt->execute()) {
                // Redirect after successful update
                header("Location: lecture.php");
                exit();
            } else {
                $statusMsg = "<div class='alert alert-danger' style='margin-right:700px;'>An error occurred during the update!</div>";
            }

            // Close the statement
            $stmt->close();
        } else {
            $statusMsg = "<div class='alert alert-danger' style='margin-right:700px;'>Record not found!</div>";
        }
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
    <?php include "admin_nav.php"; ?>
    <!-- Sidebar -->
    <?php include "admin_sidebar.php"; ?>
    <div id="content">
        <h1 style="color:blue">Lecture</h1>
        <div id="box">

            <form method="post">
                <div style="font-size: 20px; color:gray; padding: 15px;">Create Lecture</div>
                <div class="form-group">
                    <div style="display: flex; gap: 20px;">
                        <div style="flex: 1;">
                            <label for="firstName">Course Name<span class="text-danger ml-2">*</span></label>
                            <input type="text" class="form-control" name="course_name" value="<?php echo isset($row['course_name']) ? $row['course_name'] : ''; ?>" id="courseName">
                        </div>
                        <div style="flex: 1;">
                            <label for="courseCode">Course Code<span class="text-danger ml-2">*</span></label>
                            <input type="text" class="form-control" name="course_code" value="<?php echo isset($row['course_code']) ? $row['course_code'] : ''; ?>" id="courseCode">
                        </div>
                    </div>
                </div>

        </div>

        <?php
        if (isset($id)) {
        ?>
            <input id="button" class="update-button" type="submit" name="update" value="Update"><br><br>
        <?php
        } else {
        ?>
            <input id="button" type="submit" name="save" value="Save"><br><br>
        <?php
        }
        ?>
        </form>


        <!-- students list -->
        <div class="row">
            <div class="">
                <h3 style="color:blue">All Courses</h3>
            </div>
            <div class="table-responsive">
                <table class="table align-items-center table-flush table-hover" id="dataTableHover">
                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>Course Name</th>
                            <th>Course Code</th>
                            <th>Date Created</th>
                            <th>Edit</th>
                            <th>Delete</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php
                        $query = "SELECT id, course_name, course_code, date FROM tbllecture";

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
                                <td>" . $rows['course_name'] . "</td>
                                <td>" . $rows['course_code'] . "</td>
                                <td>" . $rows['date'] . "</td>
                                <td><a href='?action=edit&id=" . $rows['id'] . "'><i class='fas fa-fw fa-edit'></i></a></td>
                                <td><a href='?action=delete&id=" . $rows['id'] . "'><i class='fas fa-fw fa-trash trash-icon'></i></a></td>
                                </tr>";
                            }} else {
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