<?php
// Include the database connection file
include('../database/db.php');
include('../time-date.php');

$date = $currentDate;
$time = $currentTime;

// Get the JSON data sent from the client
$data = json_decode(file_get_contents('php://input'), true);

$transaction_id = mt_rand(10000000, 99999999);
$check_transactionID = "SELECT * FROM sales WHERE TRANSACTION_ID = '$transaction_id'";
$check_transactionID_result = $conn->query($check_transactionID);
while($check_transactionID_result->num_rows > 0) {
    $transaction_id = mt_rand(10000000, 99999999);
    $check_transactionID = "SELECT * FROM sales WHERE TRANSACTION_ID = '$transaction_id'";
    $check_transactionID_result = $conn->query($check_transactionID);
}

$cust_id = !empty($data['sales']['cust_id']) ? $data['sales']['cust_id'] : null;

// Prepare the SQL statement for inserting into the 'sales' table
$sales_sql = "INSERT INTO `sales`(`TRANSACTION_ID`, `TRANSACTION_TYPE`, `PAYMENT_TYPE`, `CUST_TYPE`, `CUST_ID`, `TIME`, `DATE`, `EMP_ID`, `SUBTOTAL`, `VAT`, `DISCOUNT`, `TOTAL`, `PAYMENT`, `CHANGE`) 
              VALUES ('".$transaction_id."','".$data['sales']['transaction_type']."','','".$data['sales']['cust_type']."','".$cust_id."','".$time."','".$date."','".$data['sales']['emp_id']."','".$data['sales']['subtotal']."','".$data['sales']['vat']."','".$data['sales']['discount']."','".$data['sales']['total']."','".$data['sales']['payment']."','".$data['sales']['change']."')";

// Insert the sales data into the 'sales' table
if (mysqli_query($conn, $sales_sql)) {
    // Loop through each sales detail and insert it into the database
    foreach ($data['salesDetails'] as $detail) {
        $sales_details_sql = "INSERT INTO `sales_details`(`TRANSACTION_ID`, `PRODUCT_ID`, `QUANTITY`, `AMOUNT`) 
                              VALUES ('".$transaction_id."','".$detail['product_id']."','".$detail['quantity']."','".$detail['amount']."')";
        mysqli_query($conn, $sales_details_sql);
    }
    // Return a success response
    $response = array('success' => true);
    echo json_encode($response);
} else {
    // Return an error response
    $response = array('success' => false, 'error' => mysqli_error($conn));
    echo json_encode($response);
}
?>
