<?php
include("../database/db.php");

session_start();

if (isset($_SESSION['id'])) {
    include('../database/db.php');

    $sql = "SELECT * FROM employee WHERE EMP_ID = {$_SESSION['id']}";
    $result  = $conn->query($sql);
    $emp = $result->fetch_assoc();


    $emp_type = $emp['EMP_TYPE'];
    $emp_status = $emp['EMP_STATUS'];

    if(isset($emp) && $emp["EMP_TYPE"] == "Admin" || $emp['EMP_TYPE'] == "PA" && $emp['EMP_STATUS'] == "active") {
        if (isset($_GET['transaction_id'])) {

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
