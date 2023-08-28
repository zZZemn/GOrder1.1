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
        if (isset($_POST['action']) && isset($_POST['id'])) {
            $action = $_POST['action'];
            $id = $_POST['id'];

            if ($action === 'deactivate') {
                $emp_sql = "UPDATE `employee` SET `EMP_STATUS` = 'deactivated' WHERE `EMP_ID` = '$id'";
            } else {
                $emp_sql = "UPDATE `employee` SET `EMP_STATUS` = 'active' WHERE `EMP_ID` = '$id'";
            }
            
            if ($conn->query($emp_sql)) {
                echo ($action === 'deactivate') ? 'Account Deactivated' : 'Account Activated';
            } else {
                echo '405';
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
