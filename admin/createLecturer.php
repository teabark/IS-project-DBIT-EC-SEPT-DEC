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

$courseLevelData = array(
    array('id' => "diploma", 'courseLevelName' => 'Diploma'),
    array('id' => "1st year", 'courseLevelName' => 'Degree year 1'),
    array('id' => "2nd year", 'courseLevelName' => 'Degree year 2'),
    array('id' => "3rd year", 'courseLevelName' => 'Degree year 3'),
    array('id' => "4th year", 'courseLevelName' => 'Degree year 4'),
    // Add more classes as needed
);

if (isset($_POST['save'])) {
    //something was posted
    $password = $_POST['password'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $middle_name = $_POST['middle_name'];
    $email = $_POST['email'];
    $staff_no = $_POST['staff_no'];
    $phone_number = $_POST['phone_number'];
    $user_name = $_POST['user_name'];
    $course_name = $_POST['course_name'];
    $course_level = $_POST['course_level'];

    if (!empty($staff_no) && !empty($password) && !empty($first_name) && !empty($last_name) && !empty($course_name) && !empty($course_level) && !is_numeric($staff_no)) {

        $table =  "tbllecturer";

        //save to database
        $user_type = "lecturer";
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $query = "INSERT INTO $table (user_type, password, first_name, last_name, middle_name, email, staff_no, phone_number, user_name, course_name, course_level) VALUES ('$user_type', '$hashed_password', '$first_name', '$last_name', '$middle_name', '$email', '$staff_no', '$phone_number', '$user_name', '$course_name', '$course_level')";



        mysqli_query($con, $query);

        header("Location: createLecturer.php");
        die;
    } else {
        echo "Please enter some valid information!";
    }
}

// delete
if (isset($_GET['id']) && isset($_GET['action']) && $_GET['action'] == "delete") {
    $id = $_GET['id'];

    $query = mysqli_query($con, "DELETE FROM tbllecturer WHERE id='$id'");

    if ($query == TRUE) {

        echo "<script type = \"text/javascript\">
              window.location = (\"createLecturer.php\")
              </script>";
    } else {

        $statusMsg = "<div class='alert alert-danger' style='margin-right:700px;'>An error Occurred!</div>";
    }
}

// edit

if (isset($_GET['id']) && isset($_GET['action']) && $_GET['action'] == "edit") {
    $id = $_GET['id'];

    $query = mysqli_query($con, "select * from tbllecturer where id ='$id'");
    $row = mysqli_fetch_array($query);

    // Check if the form is submitted for an update
    if (isset($_POST['update'])) {

        // Check if $row is set before using it
        if (isset($row)) {

            // Collect updated data
            $firstName = isset($_POST['first_name']) ? $_POST['first_name'] : $row['first_name'];
            $lastName = isset($_POST['last_name']) ? $_POST['last_name'] : $row['last_name'];
            $middleName = isset($_POST['middle_name']) ? $_POST['middle_name'] : $row['middle_name'];
            $email = isset($_POST['email']) ? $_POST['email'] : $row['email'];
            $staffNumber = isset($_POST['staff_no']) ? $_POST['staff_no'] : $row['staff_no'];
            $phoneNumber = isset($_POST['phone_number']) ? $_POST['phone_number'] : $row['phone_number'];
            $userName = isset($_POST['user_name']) ? $_POST['user_name'] : $row['user_name'];
            $courseName = isset($_POST['course_name']) ? $_POST['course_name'] : $row['course_name'];
            $courseLevel = isset($_POST['course_level']) ? $_POST['course_level'] : $row['course_level'];

            $hashedPassword = isset($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : $row['password'];

            // Use prepared statements to prevent SQL injection
            $stmt = $con->prepare("UPDATE tbllecturer SET first_name=?, last_name=?, middle_name=?, email=?, staff_no=?, phone_number=?, user_name=?, password=?, course_name=?, course_level=? WHERE id=?");

            // Bind parameters
            $stmt->bind_param("ssssssssssi", $firstName, $lastName, $middleName, $email, $staffNumber, $phoneNumber, $userName, $hashedPassword, $courseName, $courseLevel, $id);



            // Execute the statement
            if ($stmt->execute()) {
                // Redirect after successful update
                header("Location: createLecturer.php");
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
        <h1 style="color:blue">Lecturers</h1>
        <div id="box">

            <form method="post">
                <div style="font-size: 20px; color:gray; padding: 15px;">Create Lecturer</div>
                <div class="form-group">
                    <div style="display: flex; gap: 20px;">
                        <div style="flex: 1;">
                            <label for="firstName">Firstname<span class="text-danger ml-2">*</span></label>
                            <input type="text" class="form-control" name="first_name" value="<?php echo isset($row['first_name']) ? $row['first_name'] : ''; ?>" id="firstName">
                        </div>
                        <div style="flex: 1;">
                            <label for="lastName">Lastname<span class="text-danger ml-2">*</span></label>
                            <input type="text" class="form-control" name="last_name" value="<?php echo isset($row['last_name']) ? $row['last_name'] : ''; ?>" id="lastName">
                        </div>
                        <div style="flex: 1;">
                            <label for="middleName">Other Name<span class="text-danger ml-2">*</span></label>
                            <input type="text" class="form-control" name="middle_name" value="<?php echo isset($row['middle_name']) ? $row['middle_name'] : ''; ?>" id="middleName">
                        </div>
                    </div>
                </div>

                <!-- new... -->
                <div class="form-group">
                    <div style="display: flex; gap: 20px;">
                        <div style="flex: 1;">
                            <label for="email">Email<span class="text-danger ml-2">*</span></label>
                            <input type="text" class="form-control" name="email" value="<?php echo isset($row['email']) ? $row['email'] : ''; ?>" id="email">
                        </div>
                        <div style="flex: 1;">
                            <label for="phoneNumber">Mobile<span class="text-danger ml-2">*</span></label>
                            <input type="text" class="form-control" name="phone_number" value="<?php echo isset($row['phone_number']) ? $row['phone_number'] : ''; ?>" id="phoneNumber">
                        </div>
                        <div style="flex: 1;">
                            <label for="userName">Username<span class="text-danger ml-2">*</span></label>
                            <input type="text" class="form-control" name="user_name" value="<?php echo isset($row['user_name']) ? $row['user_name'] : ''; ?>" id="userName">
                        </div>
                    </div>
                </div>

                <!-- new... -->

                <div class="form-group">
                    <div style="display: flex; gap: 20px;">
                        <div style="flex: 1;">
                            <label for="staffNumber">Staff Number<span class="text-danger ml-2">*</span></label>
                            <input type="text" class="form-control" required name="staff_no" value="<?php echo isset($row['staff_no']) ? $row['staff_no'] : ''; ?>" id="staffNumber">
                        </div>
                        <div style="flex: 1;">
                            <label for="password">Password<span class="text-danger ml-2">*</span></label>
                            <input type="text" class="form-control" required name="password" value="<?php echo isset($row['password']) ? $row['password'] : ''; ?>" id="password">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div style="display: flex; gap: 20px;">
                        <div style="flex: 1;">
                            <label for="selectClass">Select Course<span class="text-danger ml-2">*</span></label>
                            <select name="course_name" class="form-control" style="height: 40px">
                                <option value="" selected disabled>Select a Course</option>
                                <?php
                                $class_query = "SELECT * FROM tbllecture ORDER BY course_name ASC";
                                $result = $con->query($class_query);

                                if ($result && $result->num_rows > 0) {
                                    while ($course = $result->fetch_assoc()) {
                                        $courseName = $course['course_name'];
                                        $selected = ($courseId == $row['course_name']) ? 'selected' : '';
                                        echo '<option value="' . $courseName . '" ' . $selected . '>' . $courseName . '</option>';
                                    }
                                } else {
                                    // Handle the case where no courses are found in the database
                                    echo '<option value="" disabled>No courses found</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div style="flex: 1;">
                            <label for="selectClassArm">Course Level<span class="text-danger ml-2">*</span></label>
                            <select name="course_level" class="form-control" style="height: 40px">
                                <?php
                                // Assuming $courseLevelData is an array with your course level data
                                foreach ($courseLevelData as $level) {
                                    $levelId = isset($level['id']) ? $level['id'] : '';
                                    $levelName = isset($level['courseLevelName']) ? $level['courseLevelName'] : '';
                                    $selected = ($levelId == $row['course_level']) ? 'selected' : '';
                                    echo '<option value="' . $levelId . '" ' . $selected . '>' . $levelName . '</option>';
                                }
                                ?>
                            </select>
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


        <!-- Lecturer list -->
        <div class="row">
            <div class="">
                <h3 style="color:blue">All Lecturers</h3>
            </div>
            <div class="table-responsive">
                <table class="table align-items-center table-flush table-hover" id="dataTableHover">
                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Middle Name</th>
                            <th>Staff No</th>
                            <th>Course Name</th>
                            <th>Course Type</th>
                            <th>Date Created</th>
                            <th>Edit</th>
                            <th>Delete</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php
                        $query = "SELECT id, first_name, last_name, middle_name, user_type, course_name, course_level, staff_no, password, date FROM tbllecturer";

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
                                <td>" . $rows['staff_no'] . "</td>
                                <td>" . $rows['course_name'] . "</td>
                                <td>" . $rows['course_level'] . "</td>
                                <td>" . $rows['date'] . "</td>
                                <td><a href='?action=edit&id=" . $rows['id'] . "'><i class='fas fa-fw fa-edit'></i></a></td>
                                <td><a href='?action=delete&id=" . $rows['id'] . "'><i class='fas fa-fw fa-trash trash-icon'></i></a></td>
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