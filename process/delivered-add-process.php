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


        $qty = $del_qty;

        if (is_numeric($product_id)) {
            $check_pro_price = "SELECT * FROM products WHERE PRODUCT_ID = $product_id";
            $check_pro_price_result = $conn->query($check_pro_price);
            if ($check_pro_price_result !== false) {
                if ($check_pro_price_result->num_rows > 0) {
                    $product = $check_pro_price_result->fetch_assoc();
                    $selling_price = $product['SELLING_PRICE'];

                    $mark_up = $selling_price - $supp_price;
                } else {
                    header("Location: ../admin/delivered-products.php?del_id=$del_id&status=failed&message=product_not_found");
                    exit;
                }

                $insert_new_delivered = "INSERT INTO `inventory`(`INV_ID`, `DELIVERY_ID`, `PRODUCT_ID`, `SUPPLIER_PRICE`, `QUANTITY`, `EXP_DATE`, `DEL_QUANTITY`, `MARK_UP`) 
                                    VALUES ('$inv_id','$del_id','$product_id','$supp_price','$qty','$expiration_date','$del_qty','$mark_up')";

                $del_price = $supp_price * $del_qty;

                $update_del_price = "UPDATE `delivery` SET `DELIVERY_PRICE`= DELIVERY_PRICE + $del_price WHERE DELIVERY_ID = $del_id";

                if ($conn->query($insert_new_delivered) === TRUE && $conn->query($update_del_price) === TRUE) {
                    header("Location: ../admin/delivered-products.php?del_id=$del_id&status=success");
                    exit;
                } else {
                    header("Location: ../admin/delivered-products.php?del_id=$del_id&status=failed");
                    exit;
                }
            } else {
                header("Location: ../admin/delivered-products.php?del_id=$del_id&status=product_id_not_exist");
                exit;
            }
        } else {
            header("Location: ../admin/delivered-products.php?del_id=$del_id&status=product_id_not_exist");
            exit;
        }
    } else {
        header("Location: ../admin/delivered-products.php?status=failed");
        exit;
    }
} else {
    header("Location: ../index.php");
    exit;
}
