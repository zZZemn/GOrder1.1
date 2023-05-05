<?php
include ('../database/db.php');
// Get the customer ID from the AJAX request
$custId = $_POST['cust_id'];

$cust_id_sql = "SELECT * FROM customer_user WHERE CUST_ID = '$custId'";
$cust_id_result = $conn->query($cust_id_sql);

if ($cust_id_result->num_rows > 0) {
    // If the customer ID exists, send the response "exists" to the AJAX request
    echo "exists";
} else {
    // If the customer ID does not exist, send the response "does_not_exist" to the AJAX request
    echo "does_not_exist";
}

// Close the database connection
$conn->close();
?>
