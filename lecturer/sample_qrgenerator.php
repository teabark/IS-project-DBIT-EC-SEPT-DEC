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



?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="styles.css">
    <title>Lecturer Dashboard</title>
</head>

<body>
    <!-- TopBar -->
    <?php include "lecturer_nav.php"; ?>
    <!-- Sidebar -->
    <?php include "lecturer_sidebar.php"; ?>
    <div id="content">

        <h1>QR Generator</h1>
<?php

echo "Generating QR code";
include('../phpqrcode/qrlib.php');

// how to save PNG codes to server

$tempDir = __DIR__ . "/qrcodes/";

$codeContents = '27/11/2023-LAW/DIPLOMA';

// we need to generate filename somehow, 
// with md5 or with database ID used to obtains $codeContents...
$fileName = '005_file_'.md5($codeContents).'.png';

$pngAbsoluteFilePath = $tempDir.$fileName;
$urlRelativeFilePath = $tempDir.$fileName;

// generating
if (!file_exists($pngAbsoluteFilePath)) {
    QRcode::png($codeContents, $pngAbsoluteFilePath);
    echo 'File generated!';
    echo '<hr />';
} else {
    echo 'File already generated! We can use this cached file to speed up site on common codes!';
    echo '<hr />';
}

echo 'Server PNG File: '.$pngAbsoluteFilePath;
echo '<hr />';

// displaying
// echo '<img src="'.$urlRelativeFilePath.'" />';
echo '<img src="/automated_attendance_system/lecturer/qrcodes/005_file_eefa1c6082073bed8dc8fa6d52c01a7c.png" />';
?>

    </div>

    <br>



</body>

</html>
