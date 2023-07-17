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
        if (isset($_POST['cust_id']) && isset($_POST['new_stats'])) {
            $cust_id = $_POST['cust_id'];
            $new_stats = $_POST['new_stats'];
            $cust_update_sql = "UPDATE `customer_user` SET `STATUS`='$new_stats' WHERE CUST_ID = '$cust_id'";
            $status_response = '';
            ($new_stats === 'active') ? $status_response = 'alert-act' : $status_response = 'alert-deact';
            if ($conn->query($cust_update_sql)) {
                echo $status_response;
            } else {
                echo 'not';
            }
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
