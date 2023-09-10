<?php 
$dbservername = "localhost";
$dbusername = "u711816221_gorder12312312";
$dbpassword = "PotanginaAngHirap!2";
$dbname = "u711816221_gorderLatest12";

// $dbservername = "localhost";
// $dbusername = "root";
// $dbpassword = "";
// $dbname = "GOrder";

// Create connection
$conn = new mysqli($dbservername, $dbusername, $dbpassword, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>