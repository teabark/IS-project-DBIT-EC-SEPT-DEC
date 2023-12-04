<?php

function check_admin_login($con)
{
    if (isset($_SESSION['user_id'])) {

        $id = $_SESSION['user_id'];
        $query = "SELECT * FROM tbladmin WHERE user_id = '$id' LIMIT 1";

        $result = mysqli_query($con, $query);
        
        if ($result) {
            if (mysqli_num_rows($result) > 0) {
                $admin_data = mysqli_fetch_assoc($result);
                return $admin_data;
            } else {
                echo "User is not an admin"; 
            }
        } else {
            // Query execution error
            echo "Query execution error: " . mysqli_error($con);
        }
    }
}

// function check_student_login($con)
// {
//     if (isset($_SESSION['user_id'])) {

//         $id = $_SESSION['user_id'];
//         $query = "SELECT * FROM tblstudent WHERE user_id = '$id' LIMIT 1";

//         $result = mysqli_query($con, $query);
        
//         if ($result) {
//             if (mysqli_num_rows($result) > 0) {
//                 $student_data = mysqli_fetch_assoc($result);
//                 return $student_data;
//             } else {
//                 // User is not an admin
//                 echo "User is not an student"; 
//             }
//         } else {
//             // Query execution error
//             echo "Query execution error: " . mysqli_error($con);
//         }
//     }
// }

function check_student_login($con)
{
    if (isset($_SESSION['registration_no'])) {

        $id = $_SESSION['registration_no'];
        $query = "SELECT * FROM tblstudent WHERE registration_no = '$id' LIMIT 1";

        $result = mysqli_query($con, $query);
        
        if ($result) {
            if (mysqli_num_rows($result) > 0) {
                $student_data = mysqli_fetch_assoc($result);
                return $student_data;
            } else {
                // User is not an student
                echo "User is not an student"; 
            }
        } else {
            // Query execution error
            echo "Query execution error: " . mysqli_error($con);
        }
    }
}

function check_lecturer_login($con)
{
    if (isset($_SESSION['user_name'])) {

        $id = $_SESSION['user_name'];
        $query = "SELECT * FROM tbllecturer WHERE user_name = '$id' LIMIT 1";

        $result = mysqli_query($con, $query);
        
        if ($result) {
            if (mysqli_num_rows($result) > 0) {
                $lecturer_data = mysqli_fetch_assoc($result);
                return $lecturer_data;
            } else {
                // User is not an admin
                echo "User is not an lecturer";
            }
        } else {
            echo "Query execution error: " . mysqli_error($con);
        }
    }
}

function check_login($con, $user_type)
{
	if ($user_type === "admin") {
        return check_admin_login($con);
    } 
	elseif ($user_type === "student") {
        return check_student_login($con);
    } 
	elseif ($user_type === "lecturer") {
        return check_lecturer_login($con);
    } 
	else {
        // Invalid user type
		//redirect to login
        header("Location: login.php");
        die;
    }
}

function random_num($length)
{

	$text = "";
	if($length < 5)
	{
		$length = 5;
	}

	$len = rand(4,$length);

	for ($i=0; $i < $len; $i++) { 
		# code...

		$text .= rand(0,9);
	}

	return $text;
}
function saveScannedDataToDatabase($lecturerId, $scannedData)
{
    global $con; // Assuming $con is your database connection

    // Sanitize input data before inserting into the database
    $lecturerId = mysqli_real_escape_string($con, $lecturerId);
    $scannedData = mysqli_real_escape_string($con, $scannedData);

    // Insert data into the attendance_records table
    $query = "INSERT INTO attendance_records (lecturer_id, scanned_data) VALUES ('$lecturerId', '$scannedData')";
    $result = $con->query($query);

    if ($result) {
        echo '<p>Scanned data successfully saved to the database.</p>';
    } else {
        echo '<p>Error saving scanned data to the database: ' . $con->error . '</p>';
    }
}

// Function to extract trimmed data based on the placeholder
function extractTrimmedData($qrCodeText, $placeholder)
{
    $startPosition = strpos($qrCodeText, $placeholder);

    if ($startPosition !== false) {
        $trimmedData = substr($qrCodeText, $startPosition + strlen($placeholder));
        return $trimmedData;
    }

    return $qrCodeText; // Return the original data if the placeholder is not found
}