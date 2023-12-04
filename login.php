<?php

session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

include("connection.php");
include("functions.php");


if ($_SERVER['REQUEST_METHOD'] == "POST") {
	//something was posted
	$user_type = $_POST['user_type'];
	$user_identifier = ($user_type === "student") ? "registration_no" : "user_name";
	$user_value = $_POST[$user_identifier];
	$password = $_POST['password'];

	echo "User Type: $user_type<br>";
    echo "User Identifier: $user_identifier<br>";
    echo "User Value: $user_value<br>";
    echo "Password: $password<br>";

	var_dump($user_type, $user_identifier, $user_value, $password);


	if (!empty($user_value) && !empty($password) && !empty($user_type) && !is_numeric($user_value)) {

		// Determine the appropriate table based on user_type
		$table = ($user_type === "admin") ? "tbladmin" : (($user_type === "student") ? "tblstudent" : "tbllecturer");
		var_dump($table);

		//read from database
		$query = "SELECT * FROM $table WHERE $user_identifier = '$user_value' LIMIT 1";
		var_dump($query);

		$result = mysqli_query($con, $query);
		var_dump($result);

		if ($result) {
			if ($result && mysqli_num_rows($result) > 0) {

				$user_data = mysqli_fetch_assoc($result);
				var_dump($user_data);

					if (password_verify($password, $user_data['password'])) {

					// Set session variable based on user type
					if ($user_type === "admin") {
						$_SESSION['user_id'] = $user_data['user_id'];
					} elseif ($user_type === "student") {
						$_SESSION['registration_no'] = $user_data['registration_no'];
					} elseif ($user_type === "lecturer") {
						$_SESSION['user_name'] = $user_data['user_name'];
					}

					// Redirect based on user type
					if ($user_type === "admin") {
						// header("Location: admin_login.php");
						header("Location: admin/admin_login.php");

						die;
					} elseif ($user_type === "student") {
						header("Location: student_login.php");
						die;
					} elseif ($user_type === "lecturer") {
						header("Location: lecturer/lecturer_login.php");
						die;
					} else {
						header("Location: login.php");
						die;
					}
				}
			}
		}

		echo "wrong username or password!";
	} else {
		echo "wrong username or password!";
	}
}

?>


<!doctype html>
<html>

<head>
	<title>Login</title>
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

				<div style="font-size: 20px;margin: 10px;color: white;">Login</div>
				<select required name="user_type" class="form-control mb-3" id="userTypeSelect">
					<option value="">--Select User Type--</option>
					<option value="admin">Admin</option>
					<option value="student">Student</option>
					<option value="lecturer">Lecturer</option>
				</select><br><br>

				<div id="dynamicInput" class="input-field">
					<!-- Initial input field -->
					<input id="text" type="text" name="user_name" placeholder="Username" class="input-field"><br><br>
				</div>

				<input id="text" type="password" placeholder="Enter password" name="password"><br><br>

				<input id="button" type="submit" value="Login"><br><br>

				<!-- <a href="register.php" style="color: white">Click to Signup</a><br><br> -->
			</form>
		</div>
	</div>
	<script>
		// Get the select element
		var userTypeSelect = document.getElementById('userTypeSelect');

		// Get the input element
		var dynamicInput = document.getElementById('dynamicInput');

		// Add an event listener to the select element
		userTypeSelect.addEventListener('change', function() {
			// Update the input field based on the selected user type
			dynamicInput.innerHTML = (this.value === 'student') ?
				'<input id="text" type="text" placeholder="Registration" name="registration_no" class="input-field"><br><br>' :
				'<input id="text" type="text" placeholder="Username" name="user_name" class="input-field"><br><br>';
		});
	</script>
</body>

</html>