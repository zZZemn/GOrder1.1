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

    if ($emp_type === 'Admin' && $emp_status === 'active') {
        if (isset($_POST['type'])) {
            $type = $_POST['type'];
            if ($type == 'deact') {
                if (isset($_POST['action'], $_POST['id'])) {
                    $id = $_POST['id'];
                    $action = $_POST['action'];

                    $deact = "UPDATE `branch` SET `STATUS`='$action' WHERE `ID` = '$id'";
                    if ($conn->query($deact)) {
                        echo '200';
                    } else {
                        echo '404';
                    }
                } else {
                    echo '404';
                }
            } elseif ($type == 'addDiscount') {
                if (isset($_POST['name'])) {
                    $branch = $_POST['name'];
                    $randID = 'BCH_' . str_pad(mt_rand(0, 999), 3, '0', STR_PAD_LEFT);
                    $check_id = $conn->query("SELECT * FROM `branch` WHERE `ID` = '$randID'");
                    while ($check_id->num_rows > 0) {
                        $randID = 'BCH_' . str_pad(mt_rand(0, 999), 3, '0', STR_PAD_LEFT);
                        $check_id = $conn->query("SELECT * FROM `branch` WHERE `ID` = '$randID'");
                    }
                    $insert_sql = "INSERT INTO `branch`(`ID`, `BRANCH`, `STATUS`) 
                                                VALUES ('$randID','$branch','Active')";
                    if ($conn->query($insert_sql)) {
                        echo '200';
                    } else {
                        echo '400';
                    }
                } else {
                    echo '404';
                }
            }
        } else {
            echo '404';
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
