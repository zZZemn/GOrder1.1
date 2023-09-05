<?php
session_start();

if (isset($_SESSION['id'])) {
    include('../database/db.php');
    include('../time-date.php');

    $sql = "SELECT * FROM employee WHERE EMP_ID = {$_SESSION['id']}";
    $result  = $conn->query($sql);
    $emp = $result->fetch_assoc();

    if (isset($_POST['branch']) && isset($_POST['date'])) {
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
    }
} else {
    header("Location: ../index.php");
    exit;
}
