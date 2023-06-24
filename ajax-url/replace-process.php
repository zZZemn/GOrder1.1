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
while ($check_transactionID_result->num_rows > 0) {
    $transaction_id = mt_rand(10000000, 99999999);
    $check_transactionID = "SELECT * FROM sales WHERE TRANSACTION_ID = '$transaction_id'";
    $check_transactionID_result = $conn->query($check_transactionID);
}

$cust_id = !empty($data['sales']['cust_id']) ? $data['sales']['cust_id'] : null;

// Prepare the SQL statement for inserting into the 'sales' table
$sales_sql = "INSERT INTO `sales`(`TRANSACTION_ID`, `TRANSACTION_TYPE`, `PAYMENT_TYPE`, `CUST_TYPE`, `CUST_ID`, `TIME`, `DATE`, `EMP_ID`, `SUBTOTAL`, `VAT`, `DISCOUNT`, `TOTAL`, `PAYMENT`, `CHANGE`, `UPDATED_TOTAL`) 
              VALUES ('" . $transaction_id . "','" . $data['sales']['transaction_type'] . "','','" . $data['sales']['cust_type'] . "','" . $cust_id . "','" . $time . "','" . $date . "','" . $data['sales']['emp_id'] . "','" . $data['sales']['subtotal'] . "','" . $data['sales']['vat'] . "','" . $data['sales']['discount'] . "','" . $data['sales']['total'] . "','" . $data['sales']['payment'] . "','" . $data['sales']['change'] . "','" . $data['sales']['total'] . "')";

$update_return_Sql = "UPDATE `return` SET `REPLACE_ID`='$transaction_id' WHERE RETURN_ID = '". $data['sales']['return_id'] ."'";
// Insert the sales data into the 'sales' table
if (mysqli_query($conn, $sales_sql) && mysqli_query($conn, $update_return_Sql)) {
    // Loop through each sales detail and insert it into the database
    foreach ($data['salesDetails'] as $detail) {
        // Get the available inventory with the product
        $inventory_sql = "SELECT * FROM `inventory` 
                          WHERE `PRODUCT_ID` = '" . $detail['product_id'] . "' AND `QUANTITY` > 0
                          ORDER BY `EXP_DATE` ASC, `PRODUCT_ID` ASC, `QUANTITY` DESC, `INV_ID` ASC";
        $result = mysqli_query($conn, $inventory_sql);

        $detail_quantity = $detail['quantity'];

        // Allocate quantity from available inventory
        while ($product = mysqli_fetch_assoc($result)) {
            $available_quantity = $product['QUANTITY'];
            $subtracted_quantity = min($detail_quantity, $available_quantity);

            // Subtract quantity from this product
            $inventory_sql = "UPDATE `inventory` 
                              SET `QUANTITY` = `QUANTITY` - $subtracted_quantity
                              WHERE `PRODUCT_ID` = '" . $detail['product_id'] . "' AND `EXP_DATE` = '" . $product['EXP_DATE'] . "' AND `INV_ID` = " . $product['INV_ID'];
            mysqli_query($conn, $inventory_sql);

            if ($subtracted_quantity > 0) {
                $product_id = $detail['product_id'];
                $product_sql = "SELECT * FROM products WHERE PRODUCT_ID = '$product_id'";
                $product_result = $conn->query($product_sql);
                $product_amount_insert = $product_result->fetch_assoc();
                $selling_price = $product_amount_insert['SELLING_PRICE'];
                $amount = $selling_price * $subtracted_quantity;

                // Insert sales detail into the database
                $sales_details_sql = "INSERT INTO `sales_details`(`TRANSACTION_ID`, `PRODUCT_ID`, `QUANTITY`, `AMOUNT`, `INV_ID`) 
                VALUES ('" . $transaction_id . "','" . $detail['product_id'] . "','" . $subtracted_quantity . "','" . $amount . "','" . $product['INV_ID'] . "')";

                mysqli_query($conn, $sales_details_sql);

                $detail_quantity -= $subtracted_quantity;
            }

            if ($detail_quantity <= 0) {
                break;
            }
        }
    }

    // Return a success response
    $response = array(
        'success' => true,
        'transaction_id' => $transaction_id,
        'time' => $time,
        'date' => $date,
    );

    echo json_encode($response);
}
 else {
    // Return an error response
    $response = array('success' => false, 'error' => mysqli_error($conn));
    echo json_encode($response);
}
