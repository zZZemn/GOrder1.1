<?php
session_start();

if (isset($_SESSION['id'])) {
    include('../database/db.php');
    include('../time-date.php');

    $sql = "SELECT * FROM employee WHERE EMP_ID = {$_SESSION['id']}";
    $result  = $conn->query($sql);
    $emp = $result->fetch_assoc();

    if (isset($_POST['branch']) && isset($_POST['date']) && isset($_POST['add'])) {
        $branch = $_POST['branch'];
        $date = $_POST['date'];
        $emp_id = $emp['EMP_ID'];

        $stock_out_id = 'STK' . rand(00000, 99999);
        $check_stock_out = $conn->query("SELECT * FROM `stock_out` WHERE `STOCK_OUT_ID` = '$stock_out_id'");
        while ($check_stock_out->num_rows > 0) {
            $check_stock_out = $conn->query("SELECT * FROM `stock_out` WHERE `STOCK_OUT_ID` = '$stock_out_id'");
            $stock_out_id = 'STK' . rand(00000, 99999);
        }

        $insert = "INSERT INTO `stock_out`(`STOCK_OUT_ID`, `BRANCH_ID`, `EMP_ID`, `DATE`, `TOTAL`, `STATUS`) 
                                    VALUES ('$stock_out_id','$branch','$emp_id','$date','0','Active')";
        if ($conn->query($insert)) {
            echo 'Stock out added';
        } else {
            echo 'Stock out not added';
        }
    } elseif (isset($_POST['branch']) && isset($_POST['date']) && isset($_POST['id']) && isset($_POST['edit'])) {
        $id = $_POST['id'];
        $branch = $_POST['branch'];
        $date = $_POST['date'];

        $update_sql = "UPDATE `stock_out` SET `BRANCH_ID`='$branch', `DATE`='$date' WHERE `STOCK_OUT_ID` = '$id'";

        if ($conn->query($update_sql)) {
            echo 'Editing Success';
        } else {
            echo 'Editing Failed';
        }
    } elseif (isset($_POST['id']) && isset($_POST['delete'])) {
        $id = $_POST['id'];
        $update_sql = "UPDATE `stock_out` SET `STATUS` = 'Deactivated' WHERE `STOCK_OUT_ID` = '$id'";

        if ($conn->query($update_sql)) {
            echo 'Stock Out Report Deleted';
        } else {
            echo 'Deletion Unsuccessful';
        }
    }
} else {
    header("Location: ../index.php");
    exit;
}
