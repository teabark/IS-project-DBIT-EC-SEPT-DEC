<?php
session_start();

include("connection.php");
include("functions.php");


if ($_SERVER['REQUEST_METHOD'] == "POST") {
    //something was posted
    $user_type = $_POST['user_type'];
    $user_name = $_POST['user_name'];
    $password = $_POST['password'];

    if (!empty($user_name) && !empty($password) && !empty($user_type) && !is_numeric($user_name)) {

        // Determine the appropriate table based on user_type
		$table = ($user_type === "admin") ? "tbladmin" : (($user_type === "student") ? "tblstudent" : "tbllecturer");
		var_dump($table);

        //save to database
        $user_id = random_num(20);
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $query = "INSERT INTO $table (user_id, user_type, user_name, password) VALUES ('$user_id', '$user_type', '$user_name', '$hashed_password')";

        mysqli_query($con, $query);

        header("Location: login.php");
        die;
    } else {
        echo "Please enter some valid information!";
    }
}
?>


<!DOCTYPE html>
<html>

<head>
    <title>Signup</title>
</head>

<body>

    <style type="text/css">
        #container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }


        #text {

            height: 25px;
            border-radius: 5px;
            padding: 4px;
            border: solid thin #aaa;
            width: 100%;
        }

        #button {
            padding: 10px;
            width: 100px;
            color: white;
            background-color: #b28842;
            border: none;
        }

        #box {

            background-color: #2e00c3;
            margin: auto;
            width: 300px;
            padding: 20px;
        }
    </style>
    <div id="container">
        <div style="font-size: 20px;margin: 10px;color: #2e00c3;">
            <h2>Attendance Management System</h2>
        </div>
        <div id="box">

            <form method="post">
                <div style="font-size: 20px;margin: 10px;color: white;">Register</div>
                <select required name="user_type" class="form-control mb-3">
                    <option value="">--Select User Type--</option>
                    <option value="admin">Admin</option>
                    <option value="student">Student</option>
                    <option value="lecturer">Lecturer</option>
                </select><br><br>

                <input id="text" type="text" name="user_name"><br><br>
                <input id="text" type="password" name="password"><br><br>

                <input id="button" type="submit" value="Signup"><br><br>

                <a href="login.php" style="color: white">Click to Login</a><br><br>
            </form>
        </div>
    </div>
</body>

</html>