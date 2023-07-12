<?php
session_start();

if (isset($_SESSION['id'])) {
    include('../database/db.php');

    $sql = "SELECT * FROM employee WHERE EMP_ID = {$_SESSION['id']}";
    $result  = $conn->query($sql);
    $emp = $result->fetch_assoc();

    $emp_type = $emp['EMP_TYPE'];
    $emp_status = $emp['EMP_STATUS'];

    if ($emp_type === 'Admin' && $emp_status === 'active') {
        if (isset($_POST['inv_id']) && isset($_POST['expiration_date']) && isset($_POST['supplier_price']) && isset($_POST['del_qty'])) {
            $inv_id = filter_input(INPUT_POST, 'inv_id', FILTER_SANITIZE_NUMBER_INT);
            $expiration_date = filter_input(INPUT_POST, 'expiration_date', FILTER_SANITIZE_NUMBER_INT);
            $supplier_price = filter_input(INPUT_POST, 'supplier_price', FILTER_SANITIZE_NUMBER_INT);
            $del_qty = filter_input(INPUT_POST, 'del_qty', FILTER_SANITIZE_NUMBER_INT);

            $inv_sql = "SELECT PRODUCT_ID FROM inventory WHERE INV_ID = '$inv_id'";
            if ($inv_result = $conn->query($inv_sql)) {
                if ($inv_result->num_rows > 0) {
                    $inv = $inv_result->fetch_assoc();
                    $product_id = $inv['PRODUCT_ID'];
                    $product_sql = "SELECT SELLING_PRICE FROM products WHERE PRODUCT_ID = '$product_id'";
                    if ($product_result = $conn->query($product_sql)) {
                        if ($product_result->num_rows > 0) {
                            $pro = $product_result->fetch_assoc();
                            $selling_price = $pro['SELLING_PRICE'];
                            $mark_up = $selling_price - $supplier_price;
                            $update_inv_sql = "UPDATE `inventory` SET `SUPPLIER_PRICE`='$supplier_price',`QUANTITY`='$del_qty',`EXP_DATE`='$expiration_date',`DEL_QUANTITY`='$del_qty',`MARK_UP`='$mark_up' WHERE INV_ID = '$inv_id'";
                            if ($conn->query($update_inv_sql)) {
                                echo 'updated';
                            } else {
                                echo 'not_updated';
                            }
                        } else {
                            echo 'error';
                        }
                    } else {
                        echo 'error';
                    }
                } else {
                    echo 'error';
                }
            } else {
                echo 'error';
            }
        } else {
            echo <<<HTML
        <head>
            <link rel='stylesheet' href='../css/access-denied.css'>
        </head>
        <div class='access-denied'>
            <h1>Access Denied</h1>
            <h5>Sorry, you are not authorized to access this page.</h5>
        </div>
HTML;
        }
    } else {
        echo <<<HTML
        <head>
            <link rel='stylesheet' href='../css/access-denied.css'>
        </head>
        <div class='access-denied'>
            <h1>Access Denied</h1>
            <h5>Sorry, you are not authorized to access this page.</h5>
        </div>
HTML;
    }
} else {
    header("Location: ../index.php");
    exit();
}
