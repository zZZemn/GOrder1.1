<?php
include("../database/db.php");

session_start();

if (isset($_SESSION['id'])) {
    include('../database/db.php');
    include('../time-date.php');
    $sql = "SELECT * FROM employee WHERE EMP_ID = {$_SESSION['id']}";
    $result  = $conn->query($sql);
    $emp = $result->fetch_assoc();

    if (isset($emp) && $emp["EMP_TYPE"] == "Admin" || $emp['EMP_TYPE'] == "PA" && $emp['EMP_STATUS'] == "active") {
        if (isset($_POST['new_status']) && isset($_POST['transaction_id']) && isset($_POST['action'])) {
            $new_status = $_POST['new_status'];
            $transaction_id = $_POST['transaction_id'];
            $action = $_POST['action'];
            if ($new_status === 'For-Delivery' || $new_status === 'Ready To Pick Up') {
                $order_sql = "SELECT * FROM `order` WHERE TRANSACTION_ID = '$transaction_id'";
                $order_result = $conn->query($order_sql);
                if ($order_result->num_rows > 0) {
                    $order = $order_result->fetch_assoc();
                    $updateOrderStat_sql = "UPDATE `order` SET `STATUS`='$new_status' WHERE TRANSACTION_ID = '$transaction_id'";
                    if ($conn->query($updateOrderStat_sql) === TRUE) {
                        $order_id = $order['TRANSACTION_ID'];
                        $cust_id = $order['CUST_ID'];
                        $payment_type = $order['PAYMENT_TYPE'];
                        $subtotal = $order['SUBTOTAL'];
                        $vat = $order['VAT'];
                        $discount = $order['DISCOUNT'];
                        $total = $order['TOTAL'];
                        $payment = $order['PAYMENT'];
                        $change = $order['CHANGE'];
                        $emp_id = $emp['EMP_ID'];

                        $cust_sql = "SELECT DISCOUNT_TYPE FROM customer_user WHERE CUST_ID = $cust_id";
                        $cust_result = $conn->query($cust_sql);
                        if ($cust_result->num_rows > 0) {
                            $cust = $cust_result->fetch_assoc();
                            $discount_type = $cust['DISCOUNT_TYPE'];

                            $discount_sql = "SELECT DISCOUNT_NAME FROM discount WHERE DISCOUNT_ID = '$discount_type'";
                            $discount_result = $conn->query($discount_sql);
                            if ($discount_result->num_rows > 0) {
                                $discount = $discount_result->fetch_assoc();
                                $discount_name = $discount['DISCOUNT_NAME'];
                            }
                        } else {
                            $cust_type = '';
                        }

                        $new_transaction_id = mt_rand(10000000, 99999999);
                        $check_transactionID = "SELECT * FROM sales WHERE TRANSACTION_ID = '$new_transaction_id'";
                        $check_transactionID_result = $conn->query($check_transactionID);
                        while ($check_transactionID_result->num_rows > 0) {
                            $new_transaction_id = mt_rand(10000000, 99999999);
                            $check_transactionID = "SELECT * FROM sales WHERE TRANSACTION_ID = '$new_transaction_id'";
                            $check_transactionID_result = $conn->query($check_transactionID);
                        }

                        $insert_sales_sql = "INSERT INTO `sales`(`TRANSACTION_ID`, `TRANSACTION_TYPE`, `ORDER_ID`, `PAYMENT_TYPE`, `CUST_TYPE`, `CUST_ID`, `TIME`, `DATE`, `EMP_ID`, `SUBTOTAL`, `VAT`, `DISCOUNT`, `TOTAL`, `PAYMENT`, `CHANGE`, `UPDATED_TOTAL`) 
                                                        VALUES ('$new_transaction_id','GOrder','$order_id','$payment_type','$discount_name','$cust_id','$currentTime','$currentDate','$emp_id','$subtotal','$vat','$discount','$total','$payment','$change','$total')";

                        if ($conn->query($insert_sales_sql)) {
                            $products_order_sql = "SELECT * FROM order_details WHERE TRANSACTION_ID = '$order_id'";
                            $products_order_result = $conn->query($products_order_sql);
                            if ($products_order_result->num_rows > 0) {
                                while ($products_order = $products_order_result->fetch_assoc()) {
                                    $product_id = $products_order['PRODUCT_ID'];
                                    $quantity = $products_order['QTY'];
                                    $amount = $products_order['AMOUNT'];

                                    $inventory_sql = "SELECT * FROM `inventory` 
                                    WHERE `PRODUCT_ID` = '$product_id' AND `QUANTITY` > 0
                                    ORDER BY `EXP_DATE` ASC, `PRODUCT_ID` ASC, `QUANTITY` DESC, `INV_ID` ASC";
                                    $inventory_result = $conn->query($inventory_sql);

                                    $detail_quantity = $quantity;

                                    while ($inventory_row = $inventory_result->fetch_assoc()) {
                                        $available_quantity = $inventory_row['QUANTITY'];
                                        $subtracted_quantity = min($detail_quantity, $available_quantity);

                                        $inventory_id = $inventory_row['INV_ID'];
                                        $update_inventory_sql = "UPDATE `inventory` 
                                         SET `QUANTITY` = `QUANTITY` - $subtracted_quantity
                                         WHERE `INV_ID` = $inventory_id";
                                        $conn->query($update_inventory_sql);

                                        if ($subtracted_quantity > 0) {
                                            $amount = $products_order['AMOUNT'];
                                            $sales_details_sql = "INSERT INTO `sales_details`(`TRANSACTION_ID`, `PRODUCT_ID`, `QUANTITY`, `AMOUNT`, `INV_ID`) 
                                                                  VALUES ('$new_transaction_id', '$product_id', $subtracted_quantity, $amount, $inventory_id)";
                                            $conn->query($sales_details_sql);

                                            $detail_quantity -= $subtracted_quantity;
                                        }

                                        if ($detail_quantity <= 0) {
                                            break;
                                        }
                                    }
                                }
                            } else {
                                echo 'not_ok';
                            }
                        } else {
                            echo 'not_ok';
                        }
                    } else {
                        echo 'not_ok';
                    }
                }
            } else {
                if ($action === 'accept-prescription') {
                    $updateOrderStat_sql = "UPDATE `order` SET `PRES_REJECT_REASON`='confirmed' WHERE TRANSACTION_ID = '$transaction_id'";
                } elseif ($action === 'accept-payment') {
                    $updateOrderStat_sql = "UPDATE `order` SET `POF_REJECT_REASON`='confirmed', `STATUS`='Accepted' WHERE TRANSACTION_ID = '$transaction_id'";
                } elseif ($action === 'accept-order') {
                    $updateOrderStat_sql = "UPDATE `order` SET `STATUS`='Accepted' WHERE TRANSACTION_ID = '$transaction_id'";
                } elseif ($action === 'decline-prescription') {
                    $updateOrderStat_sql = "UPDATE `order` SET `PRES_REJECT_REASON`='decline' WHERE TRANSACTION_ID = '$transaction_id'";
                } elseif ($action === 'decline-payment') {
                    $updateOrderStat_sql = "UPDATE `order` SET `POF_REJECT_REASON`='decline' WHERE TRANSACTION_ID = '$transaction_id'";
                } elseif ($action === 'decline-order') {
                    $updateOrderStat_sql = "UPDATE `order` SET `STATUS`='decline' WHERE TRANSACTION_ID = '$transaction_id'";
                } else {
                    $updateOrderStat_sql = "UPDATE `order` SET `STATUS`='$new_status' WHERE TRANSACTION_ID = '$transaction_id'";
                }
                if ($conn->query($updateOrderStat_sql) === TRUE) {
                    echo 'ok';
                } else {
                    echo 'not_ok';
                }
            }
        } elseif (isset($_POST['rider']) && isset($_POST['transaction_id'])) {
            $rider = $_POST['rider'];
            $transaction_id = $_POST['transaction_id'];

            $updateRider_sql = "UPDATE `order` SET `RIDER_ID`='$rider' WHERE TRANSACTION_ID = '$transaction_id'";
            if ($conn->query($updateRider_sql) === TRUE) {
                echo $rider;
            } else {
                echo 'not_ok';
            }
        } else {
            echo "
            <head>
                <link rel='stylesheet' href='../css/access-denied.css'>
            </head>
            <div class='access-denied'>
                <h1>Access Denied</h1>
                <h5>Sorry, you are not authorized to access this page.</h5>
            </div>";
        }
    } else {
        echo "
        <head>
            <link rel='stylesheet' href='../css/access-denied.css'>
        </head>
        <div class='access-denied'>
            <h1>Access Denied</h1>
            <h5>Sorry, you are not authorized to access this page.</h5>
        </div>";
    }
} else {
    header("Location: ../index.php");
    exit();
}
