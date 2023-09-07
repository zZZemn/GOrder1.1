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
        if (isset($_POST['inv_id'], $_POST['qty'], $_POST['soid'], $_POST['sodid'], $_POST['selling_price'])) {
            $inv_id = $_POST['inv_id'];
            $qty = $_POST['qty'];
            $selling_price = $_POST['selling_price'];
            $soid = $_POST['soid'];
            $sodid = $_POST['sodid'];

            $totMinus = $selling_price * $qty;

            $delete_sql = "DELETE FROM `stock_out_details` WHERE `ID` = '$sodid'";
            $updateINV_sql = "UPDATE `inventory` SET `QUANTITY`= `QUANTITY` + '$qty' WHERE `INV_ID` = '$inv_id'";
            $updateSO_sql = "UPDATE `stock_out` SET `TOTAL` = `TOTAL` - '$totMinus' WHERE `STOCK_OUT_ID` = '$soid'";

            if ($conn->query($delete_sql) == TRUE && $conn->query($updateINV_sql) == TRUE && $conn->query($updateSO_sql) == TRUE) {
                echo 'Deletion Success!';
            } else {
                echo 'Something Went Wrong :<';
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
