<?php
include("../database/db.php");

session_start();

if (isset($_SESSION['id'])) {
    include('../database/db.php');

    $sql = "SELECT * FROM employee WHERE EMP_ID = {$_SESSION['id']}";
    $result  = $conn->query($sql);
    $emp = $result->fetch_assoc();

    if (isset($emp) && $emp["EMP_TYPE"] == "Admin" || $emp['EMP_TYPE'] == "PA" && $emp['EMP_STATUS'] == "active") {
        if (isset($_POST['transaction_id']) && isset($_POST['total']) && isset($_POST['payment'])) {
            $payment = $_POST['payment'];
            $total = $_POST['total'];
            $transaction_id = $_POST['transaction_id'];

            if ($payment >= $total) {
                $change =  $payment - $total;

                $order_sql = "UPDATE `order` SET `PAYMENT`='$payment',`CHANGE`='$change', `STATUS`='Picked Up' WHERE TRANSACTION_ID = '$transaction_id'";
                $sales_update_sql = "UPDATE `sales` SET `TOTAL`='$total',`PAYMENT`='$payment',`CHANGE`='$change', `UPDATED_TOTAL`='$total' WHERE ORDER_ID = '$transaction_id'";
                if ($conn->query($order_sql) === TRUE && $conn->query($sales_update_sql) === TRUE) {
                    echo 'OK';
                } else {
                    echo 'Not ok';
                }
            } else {
                echo 'Invalid Payment';
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
