<?php
error_reporting(0);
if (!empty($_FILES['pof']['size'])) {
    $file_name = $prescription['name'];
    $file_tmp = $prescription['tmp_name'];
    $extension = pathinfo($file_name, PATHINFO_EXTENSION);

    if ($extension === 'jpg' || $extension === 'jpeg' || $extension === 'png') {

        $new_file_name = substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 13) . '.' . $extension;
        $check_file_name = "SELECT PRESCRIPTION FROM `order` WHERE PROOF_OF_PAYMENT = '$new_file_name'";
        $check_file_result = $conn->query($check_file_name);
        while ($check_file_result->num_rows > 0) {
            $new_file_name = substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 13) . '.' . $extension;
            $check_file_name = "SELECT PRESCRIPTION FROM `order` WHERE PROOF_OF_PAYMENT = '$new_file_name'";
            $check_file_result = $conn->query($check_file_name);
        }

        $destination = "../img/prescription/" . $new_file_name;
        if (move_uploaded_file($file_tmp, $destination)) {
            $transaction_id = randomTransaction_id();

            $insert_order_sql = "INSERT INTO `order`(`TRANSACTION_ID`, `CUST_ID`, `PAYMENT_TYPE`, `DELIVERY_TYPE`, `UNIT_STREET`, `BARANGAY_ID`, `TIME`, `DATE`, `SUBTOTAL`, `VAT`, `DISCOUNT`, `TOTAL`, `STATUS`, `PROOF_OF_PAYMENT`) 
                                        VALUES ('$transaction_id','$cust_id','$payment_type','$delivery_type','$unit_st','$bgy_id','$currentTime','$currentDate','$subtotal','$vat','$discount','$total','Waiting', '$new_file_name')";

            if ($conn->query($insert_order_sql) === TRUE) {
                foreach ($order_items_array as $order_item) {
                    $product_id = $order_item['PRODUCT_ID'];
                    $qty = $order_item['QTY'];
                    $amount = $order_item['AMOUNT'];

                    $insert_order_details_sql = "INSERT INTO `order_details`(`TRANSACTION_ID`, `PRODUCT_ID`, `QTY`, `AMOUNT`) 
                                            VALUES ('$transaction_id', '$product_id', '$qty', '$amount')";
                    if ($conn->query($insert_order_details_sql) !== TRUE) {
                        $message = 'Inserting Error';
                        return error422($message);
                    }
                }

                $delete_cartItems_sql = "DELETE FROM `cart_items` WHERE CART_ID = '$cart_id'";
                if ($conn->query($delete_cartItems_sql) !== TRUE) {
                }

                if ($delivery_type === 'Deliver') {
                    $data = [
                        'status' => 200,
                        'message' => 'Order Success',
                        'order_items' => $order_items_array,
                        'transaction_id' => $transaction_id,
                        'cust_id' => $cust_id,
                        'payment_type' => $payment_type,
                        'delivery_type' => $delivery_type,
                        'unit_st' => $unit_st,
                        'bgy_id' => $bgy_id,
                        'time' => $currentTime,
                        'date' => $currentDate,
                        'subtotal' => $subtotal,
                        'VAT' => $vat,
                        'discount' => $discount,
                        'total' => $total,
                        'del_status' => 'Waiting',
                        'df' => $df
                    ];
                    header("HTTP/1.0 405 OK");
                    return json_encode($data);
                } elseif ($delivery_type === 'Pick Up') {
                    $data = [
                        'status' => 200,
                        'message' => 'Order Success',
                        'order_items' => $order_items_array,
                        'transaction_id' => $transaction_id,
                        'cust_id' => $cust_id,
                        'payment_type' => $payment_type,
                        'delivery_type' => $delivery_type,
                        'unit_st' => $unit_st,
                        'bgy_id' => $bgy_id,
                        'time' => $currentTime,
                        'date' => $currentDate,
                        'subtotal' => $subtotal,
                        'VAT' => $vat,
                        'discount' => $discount,
                        'total' => $total,
                        'del_status' => 'Waiting'
                    ];
                    header("HTTP/1.0 405 OK");
                    return json_encode($data);
                } else {
                    $message = 'Invalid Delivery Type';
                    return error422($message);
                }
            } else {
                $message = 'Inserting Error';
                return error422($message);
            }
        } else {
            $message = 'Upload Unsuccessfull';
            return error422($message);
        }
    } else {
        $message = 'File Extension Not Accepted';
        return error422($message);
    }
} else {
    $message = 'Please Upload Proof Of Payment';
    return error422($message);
}
