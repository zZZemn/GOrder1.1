<?php 
// $dbservername = "localhost";
// $dbusername = "u711816221_goldengate";
// $dbpassword = "Pabiligamot10";
// $dbname = "u711816221_gorder";

$dbservername = "localhost";
$dbusername = "root";
$dbpassword = "";
$dbname = "gorder";

// Create connection
$conn = new mysqli($dbservername, $dbusername, $dbpassword, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>