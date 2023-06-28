<?php
session_start();
if (isset($_SESSION['id'])) {
    include('../database/db.php');
    include('../time-date.php');

    $sql = "SELECT * FROM employee WHERE EMP_ID = {$_SESSION['id']}";
    $result  = $conn->query($sql);
    $emp = $result->fetch_assoc();

    if (isset($emp) && ($emp["EMP_TYPE"] == "Admin" || $emp['EMP_TYPE'] == "PA" || $emp['EMP_TYPE'] == "Pharmacists") && $emp['EMP_STATUS'] == "active") {
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

            $sales_check_sql = "SELECT CUST_TYPE FROM sales WHERE TRANSACTION_ID = '$transaction_id'";
            $sales_check_result = $conn->query($sales_check_sql);
            if ($sales_check_result->num_rows > 0) {
                $sales = $sales_check_result->fetch_assoc();
                $cust_type = $sales['CUST_TYPE'];

                $discount_sql = "SELECT DISCOUNT_PERCENTAGE FROM discount WHERE DISCOUNT_NAME = '$cust_type'";
                $discount_result = $conn->query($discount_sql);
                if ($discount_result->num_rows > 0) {
                    $discount = $discount_result->fetch_assoc();
                    $discount_percentage = $discount['DISCOUNT_PERCENTAGE'];
                } 
                // else {
                //     $discount_percentage = 0.00;
                // }
            } 
            // else {
            //     $discount_percentage = 0.00;
            // }

            $tax_sql = "SELECT TAX_PERCENTAGE FROM tax WHERE TAX_ID = '1'";
            $tax_result = $conn->query($tax_sql);
            $tax = $tax_result->fetch_assoc();
            $tax_percentage = $tax['TAX_PERCENTAGE'];

            $totalAmount = 0;
            $discountable_total = 0;
            $vatable_total = 0;
            foreach ($items as $item) {
                $qty = $item['qty'];
                $inv_id = $item['id'];
                $inv_sql = "SELECT PRODUCT_ID FROM inventory WHERE INV_ID = '$inv_id'";
                $inv_result = $conn->query($inv_sql);
                if($inv_result->num_rows > 0){
                    $inv = $inv_result->fetch_assoc();
                    $product_id = $inv['PRODUCT_ID'];
                }

                $products_sql = "SELECT VATABLE, DISCOUNTABLE, SELLING_PRICE FROM products WHERE PRODUCT_ID = '$product_id'";
                $products_result = $conn->query($products_sql);
                if ($products_result->num_rows > 0) {
                    $product = $products_result->fetch_assoc();
                    $isDiscountable = $product['DISCOUNTABLE'];
                    $isVatable = $product['VATABLE'];
                } else {
                    $isDiscountable = false;
                    $isVatable = false;
                }

                $amount = $item['amount'];


                if ($isDiscountable) {
                    $discountable_total += $amount;
                }

                if ($isVatable) {
                    $vatable_total += $amount;
                }

                $totalAmount += $amount;
            }

            $vat = $vatable_total * $tax_percentage;
            $discount = $discountable_total * $discount_percentage;

            $final_total = ($totalAmount + $vat) - $discount;

            $insert_return = "INSERT INTO `return`(`RETURN_ID`, `TRANSACTION_ID`, `RETURN_DATE`, `RETURN_AMOUNT`) 
                                            VALUES ('$return_id','$transaction_id','$currentDate','$final_total')";

            if ($conn->query($insert_return) === TRUE) {
                $sales_update = "UPDATE `sales` SET `UPDATED_TOTAL`= `TOTAL` - $final_total WHERE TRANSACTION_ID = '$transaction_id'";
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
                        $response = array(
                            'message' => 'Success: All items inserted',
                            'vat' => $vat,
                            'discount' =>$discount,
                            'cust' => $cust_type
                        );
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
