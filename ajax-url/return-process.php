<?php
session_start();
if (isset($_SESSION['id'])) {
    include('../database/db.php');
    include('../time-date.php');

    $sql = "SELECT * FROM employee WHERE EMP_ID = {$_SESSION['id']}";
    $result  = $conn->query($sql);
    $emp = $result->fetch_assoc();

    if (isset($emp) && ($emp["EMP_TYPE"] == "Admin" || $emp['EMP_TYPE'] == "PA") && $emp['EMP_STATUS'] == "active") {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            $return_data = json_decode(file_get_contents('php://input'), true);

            $transaction_id = $return_data[0]['transaction_id'];
            $items = $return_data[1];

            $return_id = 'RTN' . sprintf('%06d', rand(100000, 999999));
            $return_id_sql = "SELECT * FROM `return` WHERE RETURN_ID = '$return_id'";
            $return_result = $conn->query($return_id_sql);
            while ($return_result->num_rows > 0) {
                $return_id = 'RTN' . sprintf('%06d', rand(100000, 999999));
                $return_id_sql = "SELECT * FROM `return` WHERE RETURN_ID = '$return_id'";
                $return_result = $conn->query($return_id_sql);
            }

            $totalAmount = 0;
            foreach ($items as $item) {
                $amount = $item['amount'];
                $totalAmount += $amount;
            }

            $insert_return = "INSERT INTO `return`(`RETURN_ID`, `TRANSACTION_ID`, `RETURN_DATE`, `RETURN_AMOUNT`) 
                                            VALUES ('$return_id','$transaction_id','$currentDate','$totalAmount')";

            if ($conn->query($insert_return) === TRUE) {
                $sales_update = "UPDATE `sales` SET `UPDATED_TOTAL`= `TOTAL` - $totalAmount WHERE TRANSACTION_ID = '$transaction_id'";
                if ($conn->query($sales_update) === TRUE) {
                    $successCount = 0;
                    $errors = 0;

                    foreach ($items as $item) {
                        $inv_id = $item['id'];
                        $qty = $item['qty'];
                        $amount = $item['amount'];
                        $insert_return_items = "INSERT INTO `return_items`(`RETURN_ID`, `INV_ID`, `QTY`) 
                                                            VALUES ('$return_id','$inv_id','$qty')";

                        if ($conn->query($insert_return_items) === TRUE) {
                            $update_inventory = "UPDATE `inventory` SET `QUANTITY`= `QUANTITY` + '$qty' WHERE INV_ID = '$inv_id'";
                            if ($conn->query($update_inventory) === TRUE) {
                                $successCount++;
                            }
                        } else {
                            $errors++;
                        }
                    }

                    if ($successCount === count($items)) {
                        $response = array('message' => 'Success: All items inserted');
                    } else {
                        $response = array('message' => 'Partial Success: Some items failed to insert (' . $errors . ' items)');
                    }
                    echo json_encode($response);
                }
            } else {
                $response = array('message' => 'Not Success: Failed to insert return record');
                echo json_encode($response);
            }
        }
    }
}
