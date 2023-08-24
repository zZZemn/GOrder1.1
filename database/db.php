<?php 
$dbservername = "localhost";
$dbusername = "u711816221_gorder1";
$dbpassword = "Gorder1.1passwordhardpassword";
$dbname = "u711816221_Gorder1";

// $dbservername = "localhost";
// $dbusername = "root";
// $dbpassword = "";
// $dbname = "gorder";

// Create connection
$conn = new mysqli($dbservername, $dbusername, $dbpassword, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>