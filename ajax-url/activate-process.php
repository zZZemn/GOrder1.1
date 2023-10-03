<?php
session_start();

if (isset($_SESSION['id'])) {
    include('../database/db.php');
    include('../time-date.php');

    $sql = "SELECT * FROM employee WHERE EMP_ID = {$_SESSION['id']}";
    $result  = $conn->query($sql);
    $emp = $result->fetch_assoc();

    $emp_type = $emp['EMP_TYPE'];
    $emp_status = $emp['EMP_STATUS'];

    if ($emp_type === 'Admin' && $emp_status === 'active') {
        if (isset($_POST['table'], $_POST['id'])) {
            $table = $_POST['table'];
            $tableId = $_POST['id'];
            if ($table == 'delivery') {
                $update_sql = "UPDATE `delivery` SET `DELIVERY_STATUS` = 'active' WHERE `DELIVERY_ID` = '$tableId'";
            } elseif ($table == 'products') {
                $update_sql = "UPDATE `products` SET `PRODUCT_STATUS` = 'active' WHERE `PRODUCT_ID` = '$tableId'";
            } elseif ($table == 'supplier') {
                $update_sql = "UPDATE `supplier` SET `SUPPLIER_STATUS` = 'active' WHERE `SUPPLIER_ID` = '$tableId'";
            } else {
            }
            $log = "INSERT INTO `emp_log`(`EMP_ID`, `LOG_TYPE`, `LOG_DATE`, `LOG_TIME`) VALUES 
                                         ('{$emp['EMP_ID']}','Restore $table ID($tableId)','$currentDate','$currentTime')";

            if ($conn->query($update_sql) && $conn->query($log)) {
                echo '200';
            } else {
                echo '400';
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
