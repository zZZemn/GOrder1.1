<?php 
$dbservername = "mysql.hostinger.com";
$dbusername = "goldengate";
$dbpassword = "Pabiligamot10";
$dbname = "gorder";

// Create connection
$conn = new mysqli($dbservername, $dbusername, $dbpassword, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>