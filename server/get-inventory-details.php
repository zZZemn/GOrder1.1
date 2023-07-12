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
        if (isset($_POST['inv_id'])) {
            $inv_id = filter_input(INPUT_POST, 'inv_id', FILTER_SANITIZE_NUMBER_INT);

            $inv_sql = "SELECT * FROM inventory WHERE INV_ID = '$inv_id'";
            if ($inv_result = $conn->query($inv_sql)) {
                if ($inv_result->num_rows > 0) {
                    $inv = $inv_result->fetch_assoc();
                    $expiration_date = $inv['EXP_DATE'];
                    $price = $inv['SUPPLIER_PRICE'];
                    $quantity = $inv['DEL_QUANTITY'];
                    $qty_left = $inv['QUANTITY'];

                    if ($quantity === $qty_left) {
                        $product_id = $inv['PRODUCT_ID'];
                        $product_sql = "SELECT PRODUCT_NAME FROM products WHERE PRODUCT_ID = '$product_id'";
                        if ($products_result = $conn->query($product_sql)) {
                            if ($products_result->num_rows > 0) {
                                $product = $products_result->fetch_assoc();
                                $product_name = $product['PRODUCT_NAME'];

                                echo json_encode([$inv_id, $expiration_date, $price, $quantity, $product_name]);
                            } else {
                                echo 'error';
                            }
                        } else {
                            echo 'error';
                        }
                    } else {
                        echo 'no_edit';
                    }
                } else {
                    echo 'not_exist';
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
