<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

include("../connection.php");
include("../functions.php");
require 'C:\xampp1\htdocs\automated_attendance_system\vendor\autoload.php';

$lecturer_data = check_login($con, "lecturer");

// Check if the user is a lecturer; if not, redirect to the login page
if (empty($lecturer_data)) {
    error_log('Before redirection logic');
    header("Location: login.php");
    error_log('After redirection logic');
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
    <?php include('../phpqrcode/qrlib.php'); ?>
    <div id="content">

        <h1>QR Generator</h1>
        <?php
        // how to save PNG codes to server

        $tempDir = __DIR__ . "/qrcodes/";

        $scan_data_placeholder = 'IS project 2023';
        $encoded_data = urlencode($scan_data_placeholder);

        $codeContents = 'http://10.55.51.27/automated_attendance_system/lecturer/qr_generator.php?scan=1&scan_data=' . $encoded_data;



        $fileName = '005_file_' . md5($codeContents) . '.png';

        $pngAbsoluteFilePath = $tempDir . $fileName;
        $urlRelativeFilePath = $tempDir . $fileName;

        echo "url Relative Path " . $pngAbsoluteFilePath;

        // generating
        if (!file_exists($pngAbsoluteFilePath)) {
            QRcode::png($codeContents, $pngAbsoluteFilePath);
            echo 'File generated!';
            echo '<hr />';
        } else {
            echo 'File already generated! We can use this cached file to speed up site on common codes!';
            echo '<hr />';
        }

        echo 'Server PNG File: ' . $pngAbsoluteFilePath;
        echo '<hr />';

        // displaying
        $relativeImagePath = str_replace('C:\xampp1\htdocs', '', $urlRelativeFilePath);
        echo '<img src="' . $relativeImagePath . '" />';

        // QR code reading logic
        if (isset($_GET['scan'])) {
            $scanData = $_GET['scan_data'] ?? '';

            echo 'scanData!!!<br>' . $scanData;
            echo 'imagePath: gggghhee434367 ' . $pngAbsoluteFilePath . '<br>';

            // Create an instance of the QRDecoder
            $qrDecoder = new Zxing\QrReader($pngAbsoluteFilePath);

            try {
                echo 'Debug Point 2<br>';

                // Decode the QR code
                $qrCodeText = $qrDecoder->text();

                $placeholder = 'http://10.55.51.27/automated_attendance_system/lecturer/qr_generator.php?scan=1&scan_data=';

                $trimmedData = extractTrimmedData($qrCodeText, $placeholder);

                // Display the trimmed data directly on the page
                echo '<p>Trimmed QR Code Data: ' . $trimmedData . '</p>';

                // Save the trimmed data to the database
                saveScannedDataToDatabase($lecturer_data['staff_no'], $trimmedData);

                echo 'Debug Point 3<br>';
            } catch (\Exception $e) {
                echo 'Debug Point 4<br>';
                echo '<p>Error reading QR code: ' . $e->getMessage() . '</p>';
            }
        }
        ?>

    </div>

    <br>

</body>

</html>