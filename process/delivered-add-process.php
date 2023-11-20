<?php

session_start();

if (isset($_SESSION['id'])) {
    include('../database/db.php');

    $emp_id = intval($_SESSION['id']);
    $sql = "SELECT * FROM employee WHERE EMP_ID = $emp_id";
    $result = $conn->query($sql);
    $emp = $result->fetch_assoc();

    if (isset($_POST['add_delivered'])) {

        $inv_id = mt_rand(100000, 999999);
        $check_inv_id = "SELECT * FROM inventory WHERE INV_ID = $inv_id";
        $check_inv_id_result = $conn->query($check_inv_id);
        while ($check_inv_id_result->num_rows > 0) {
            $inv_id = mt_rand(100000, 999999);
            $check_inv_id = "SELECT * FROM inventory WHERE INV_ID = $inv_id";
            $check_inv_id_result = $conn->query($check_inv_id);
        }

        $del_id = filter_var($_POST['del_id'], FILTER_SANITIZE_NUMBER_INT);
        $product_id = filter_var($_POST['product_id'], FILTER_SANITIZE_NUMBER_INT);
        $expiration_date = filter_var($_POST['expiration_date'], FILTER_SANITIZE_STRING);
        $supp_price = filter_var($_POST['supp_price'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $del_qty = filter_var($_POST['del_qty'], FILTER_SANITIZE_NUMBER_INT);
        $batchNumber = filter_var($_POST['batchNumber'], FILTER_SANITIZE_NUMBER_INT);

        ($expiration_date === "") ? $expiration_date = null : $expiration_date = $expiration_date;

        $qty = $del_qty;

        if (is_numeric($product_id)) {
            $check_pro_price = "SELECT * FROM products WHERE PRODUCT_ID = $product_id";
            $check_pro_price_result = $conn->query($check_pro_price);
            if ($check_pro_price_result !== false) {
                if ($check_pro_price_result->num_rows > 0) {
                    $product = $check_pro_price_result->fetch_assoc();
                    $selling_price = $product['SELLING_PRICE'];

                    $mark_up = $selling_price - $supp_price;

                    $insert_new_delivered = "INSERT INTO `inventory`(`INV_ID`, `DELIVERY_ID`, `PRODUCT_ID`, `SUPPLIER_PRICE`, `QUANTITY`, `EXP_DATE`, `BATCH_NO`,`DEL_QUANTITY`, `MARK_UP`) 
                                    VALUES ('$inv_id','$del_id','$product_id','$supp_price','$qty','$expiration_date','$batchNumber','$del_qty','$mark_up')";

                    $del_price = $supp_price * $del_qty;

                    $update_del_price = "UPDATE `delivery` SET `DELIVERY_PRICE`= DELIVERY_PRICE + $del_price WHERE DELIVERY_ID = $del_id";

                    if ($conn->query($insert_new_delivered) === TRUE && $conn->query($update_del_price) === TRUE) {
                        echo 'ok';
                    } else {
                        echo 'adding_failed';
                    }
                } else {
                    echo 'not_exist';
                }
            } else {
                echo 'adding_failed';
            }
        } else {
            echo 'not_exist';
        }
    } else {
        echo 'adding_failed';
    }
} else {
    header("Location: ../index.php");
    exit;
}
