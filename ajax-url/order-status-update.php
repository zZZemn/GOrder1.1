<?php
include("../database/db.php");

session_start();

if (isset($_SESSION['id'])) {
    include('../database/db.php');

    $sql = "SELECT * FROM employee WHERE EMP_ID = {$_SESSION['id']}";
    $result  = $conn->query($sql);
    $emp = $result->fetch_assoc();

    if (isset($emp) && $emp["EMP_TYPE"] == "Admin" || $emp['EMP_TYPE'] == "PA" && $emp['EMP_STATUS'] == "active") {
        if (isset($_POST['new_status']) && isset($_POST['transaction_id'])) {
            $new_status = $_POST['new_status'];
            $transaction_id = $_POST['transaction_id'];

            $updateOrderStat_sql = "UPDATE `order` SET `STATUS`='$new_status' WHERE TRANSACTION_ID = '$transaction_id'";
            if ($conn->query($updateOrderStat_sql) === TRUE) {
                echo $new_status;
            } else {
                echo 'not_ok';
            }
        } elseif(isset($_POST['rider']) && isset($_POST['transaction_id'])){
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
